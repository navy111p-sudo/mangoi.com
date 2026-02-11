<?php
/*
강의 시간을 체크해서 10분 후 강의가 있을 경우
강사에게 팝업을 띄워 강의가 있음을 알린다.
*/

// 2024-04-08 데이터크레딧 수정 : 초기화되지 않는 변수로 인한 PHP Warning 수정
// 기본값으로 초기화
$EnableClassTime = 0;


if ($_COOKIE["Class10MinuteBefore"]!=1) { // 이미 팝업을 띄웠으면 수업이 끝날때까지 다시 띄우지 않는다.
	header('Content-Type: application/json; charset=UTF-8');
	include_once('../includes/dbopen.php');
	include_once('../includes/common.php');

	$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";

	$Sql = "SELECT  
				A.StartDateTimeStamp,
				A.EndDateTimeStamp 
			from Classes A 
			where A.StartDateTime >= Now() AND A.StartDateTime <= DATE_ADD(NOW(), INTERVAL 1 HOUR) 
				AND A.TeacherID = (SELECT TeacherID FROM Members WHERE MemberLoginID = '".$_COOKIE["LoginMemberID"]."') 
				AND A.TeacherInDateTime IS NULL 
				AND A.ClassAttendState = 0 
	";
	//echo $Sql;
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassID', $ClassID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$StartDateTimeStamp = $Row["StartDateTimeStamp"];
	$EndDateTimeStamp = $Row["EndDateTimeStamp"];



	//  시간 비교용
	$CurrentTime = date("Y-m-d H:i:s");//현재시간
	//echo 'haha'.strtotime($CurrentTime)."//".$StartDateTimeStamp;


	//  클래스 생성 일자와 현재 시간 비교
	$ClassTimeLimit =  (strtotime($CurrentTime) - $StartDateTimeStamp);
	$ClassTimeLimit = ceil($ClassTimeLimit / (60)) ;

	//if($ClassTimeLimit >= 10 || $ClassTimeLimit <= -10) {//수업전후 10분 이내가 아닐경우(즉, 입장불가) - 시작타임 전 10분, 시작타임 후 10분 이내일때만 1이 됨.
	if($ClassTimeLimit < -10) {//수업시작시간보다 10분이상 이전일 경우
		$EnableClassTime = 0;
	} else if($ClassTimeLimit < 10) { // 수업 시작시간이 10분 이하로 남았거나 수업 시간이 지났을 경우
		$EnableClassTime = 1;
		setcookie("Class10MinuteBefore", 1, 0, false, NULL); // 쿠키값으로 수업 시간 10분전 팝업 작동했음을 저장.
		setcookie("EndDateTimeStamp", $EndDateTimeStamp, 0,false, NULL); // 쿠키값으로 수업 끝나는 시간 저장.
	}
	
	
} else {
	$EnableClassTime = 0;

	//  시간 비교용
	$CurrentTime = date("Y-m-d H:i:s");//현재시간

	//  클래스 생성 일자와 현재 시간 비교
	$ClassTimeDiff =  ($_COOKIE["EndDateTimeStamp"] - strtotime($CurrentTime));
	echo $ClassTimeDiff."haha";

	// 현재 시간과 수업 끝나는 시간을 비교해서 수업이 끝났으면 팝업창을 다시 띄울 수 있게 쿠키값 초기화
	if ($ClassTimeDiff < 0) {
		setcookie("Class10MinuteBefore", 0, 0, false, NULL);
		setcookie("EndDateTimeStamp", 0, 0,false, NULL);
	}	
}

$ArrValue["EnableClassTime"] = $EnableClassTime;
$QueryResult = my_json_encode($ArrValue);
echo $QueryResult;



function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');

?>