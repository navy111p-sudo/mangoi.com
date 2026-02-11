<?php

header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$MemberType = isset($_REQUEST["MemberType"]) ? $_REQUEST["MemberType"] : "";
$CommonCiTelephone = isset($_REQUEST["CommonCiTelephone"]) ? $_REQUEST["CommonCiTelephone"] : "";
$BeginTime = isset($_REQUEST["BeginTime"]) ? $_REQUEST["BeginTime"] : "";
$EndTime = isset($_REQUEST["EndTime"]) ? $_REQUEST["EndTime"] : "";

$Sid = 2351620;
$Secret = "dNG0uoa4";
$TimeStamp = DateToTimestamp(date("Y-m-d H:i:s"), "Asia/Seoul");
$Res = $Secret . $TimeStamp;
$SafeKey = md5($Res);


$Sql = "
	select 
			A.ClassOrderID,
			A.StartDateTimeStamp,
			A.EndDateTimeStamp,
			A.CommonCiCourseID,
			A.CommonCiClassID,
			A.CommonCiTelephoneTeacher,
			A.CommonCiTelephoneStudent,
			B.TeacherName,
			C.MemberName
		from Classes A 
			inner join Teachers B on A.TeacherID=B.TeacherID 
			inner join Members C on A.MemberID=C.MemberID 
		where A.ClassID=:ClassID 
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ClassOrderID = $Row["ClassOrderID"];
$StartDateTimeStamp = $Row["StartDateTimeStamp"];
$EndDateTimeStamp = $Row["EndDateTimeStamp"];
$CommonCiCourseID = $Row["CommonCiCourseID"];
$CommonCiClassID = $Row["CommonCiClassID"];
$CommonCiTelephoneTeacher = $Row["CommonCiTelephoneTeacher"];
$CommonCiTelephoneStudent = $Row["CommonCiTelephoneStudent"];
$TeacherName = $Row["TeacherName"];
$MemberName = $Row["MemberName"];
//$BeginTime = $StartDateTimeStamp;
//$EndTime = $EndDateTimeStamp;
//$BeginTime = DateToTimestamp(date("Y-m-d H:i:s", strtotime("+1 minutes")), "Asia/Seoul");
//$EndTime = DateToTimestamp(date("Y-m-d H:i:s", strtotime("+60 minutes")), "Asia/Seoul");





$ExpiryTime = $TimeStamp + 31535990;//코스만들때 쓰임.






if($CommonCiCourseID=="" && $MemberType=="2") {

	$Sql = "
		select 
				ifnull(A.ClassInCourseID, '') as ClassInCourseID
			from ClassOrderClassInCourses A 
			where 
				A.ClassOrderID=:ClassOrderID 
				and TIMESTAMPDIFF(day, now(), ClassInCourseEndDate) >=0 
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$ClassInCourseID = $Row["ClassInCourseID"];	

	
	if ($ClassInCourseID==""){

		$Url = "https://www.eeo.cn/partner/api/course.api.php?action=addCourse";

		$Params = array(
			'SID' => $Sid,
			'safeKey' => $SafeKey,
			'timeStamp' => $TimeStamp,
			'courseName' => "MangoiCourse_".$ClassOrderID,
			'expiryTime' => $ExpiryTime
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $Url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $Params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$result = json_decode(curl_exec($ch), true);
		curl_close($ch);

		//  리턴값이 정상이면 인설트 실행
		if($result['data']!='') {
			$ClassInCourseID = $result['data'];

			$Sql = "insert into ClassOrderClassInCourses (
							ClassOrderID, 
							ClassInCourseID, 
							ClassInCourseStartDate, 
							ClassInCourseEndDate, 
							ClassInCourseRegDateTime, 
							ClassInCourseModiDateTime, 
							ClassInCourseState
						) values(
							:ClassOrderID, 
							:ClassInCourseID, 
							now(), 
							date_add(now(), interval 364 day), 
							now(), 
							now(), 
							1
				)";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ClassOrderID', $ClassOrderID, PDO::PARAM_INT);
			$Stmt->bindParam(':ClassInCourseID', $ClassInCourseID, PDO::PARAM_INT);
			$Stmt->execute();

			$CommonCiCourseID = $ClassInCourseID;
			$CommonCiClassID = "";


			$Sql = "UPDATE Classes SET 
						CommonCiCourseID = :CommonCiCourseID
					WHERE ClassID=:ClassID;";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':CommonCiCourseID', $CommonCiCourseID);
			$Stmt->bindParam(':ClassID', $ClassID, PDO::PARAM_INT);
			$Stmt->execute();		

			$Url = "https://www.eeo.cn/partner/api/course.api.php?action=addCourseStudent";

			$Params = array(
				'SID' => $Sid,
				'safeKey' => $SafeKey,
				'timeStamp' => $TimeStamp,
				'courseId' => $CommonCiCourseID,
				'identity' => 1,
				'studentAccount' => $CommonCiTelephoneStudent,
				'studentName' => $MemberName
			);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $Url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $Params);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$result = json_decode(curl_exec($ch), true);
			curl_close($ch);

			$Url = "https://www.eeo.cn/partner/api/course.api.php?action=addTeacher";

			$Params = array(
				'SID' => $Sid,
				'safeKey' => $SafeKey,
				'timeStamp' => $TimeStamp,
				'teacherAccount' => $CommonCiTelephoneTeacher,
				'teacherName' => $TeacherName
			);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $Url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $Params);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$result = json_decode(curl_exec($ch), true);
			curl_close($ch);
		}

	}
}


if ($CommonCiClassID=="" && $MemberType=="2"){

	//  클래스 생성
	$Url = "https://www.eeo.cn/partner/api/course.api.php?action=addCourseClass";

	/*
	echo $Sid."\n";
	echo $SafeKey."\n";
	echo $TimeStamp."\n";
	echo $CommonCiCourseID."\n";
	echo "MangoiClass_".$ClassID."\n";
	echo $BeginTime."\n";
	echo $BeginTime2."\n";
	echo $EndTime."\n";
	echo $EndTime2."\n";
	echo $CommonCiTelephone."\n";
	echo $TeacherName."\n";
	*/

	$Params = array(
		'SID'=> $Sid,
		'safeKey'=> $SafeKey,
		'timeStamp'=> $TimeStamp,
		'courseId'=> $CommonCiCourseID,
		'className'=> "MangoiClass_".$ClassID,
		'beginTime'=> $BeginTime,
		'endTime'=> $EndTime,
		'teacherAccount'=> $CommonCiTelephone,
		'teacherName'=> $TeacherName
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $Url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $Params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


	$result = json_decode(curl_exec($ch), true);
	curl_close($ch);
	//echo "***".$result['error_info']['errno'];
	//  클래스 생성 API 가 정상작동 되었다면
	if($result['data']!='') {
		$CommonCiClassID = $result['data'];


		$Sql = "UPDATE Classes SET 
					CommonCiCourseID = :CommonCiCourseID, 
					CommonCiClassID = :CommonCiClassID 
				WHERE ClassID=:ClassID;";


		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':CommonCiClassID', $CommonCiClassID);
		$Stmt->bindParam(':CommonCiCourseID', $CommonCiCourseID);
		$Stmt->bindParam(':ClassID', $ClassID, PDO::PARAM_INT);

		$Stmt->execute();


	} else {
		//에러
	}

} else {
	//에러
}




//==== 최종 주소 만들기 ===============
if ($CommonCiCourseID !="" && $CommonCiClassID!=""){


	$Url = "https://www.eeo.cn/partner/api/course.api.php?action=getLoginLinked";
	$Params = array(
		'SID'=> $Sid,
		'safeKey'=> $SafeKey,
		'timeStamp'=> $TimeStamp,
		'telephone'=> $CommonCiTelephone,
		'courseId'=> $CommonCiCourseID,
		'classId'=> $CommonCiClassID
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $Url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $Params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$result = json_decode(curl_exec($ch), true);
	$ClassRoomUrl = $result['data'];

	if($ClassRoomUrl=="") {
		$ClassRoomUrl = $result['error_info']['errno'];
	}
	curl_close($ch);

}else{
	$ClassRoomUrl = "";
}


$ArrValue["ClassRoomUrl"] = $ClassRoomUrl;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult;
//==== 최종 주소 만들기 ===============

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');

?>