<?php

header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";

$Sql = "
	select 
			A.StartDateTimeStamp
		from Classes A 
		where A.ClassID=:ClassID 
";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$StartDateTimeStamp = $Row["StartDateTimeStamp"];



//  시간 비교용
$CurrentTime = date("Y-m-d H:i:s");//현재시간


//  클래스 생성 일자와 현재 시간 비교
$ClassTimeLimit =  (strtotime($CurrentTime) - $StartDateTimeStamp);
$ClassTimeLimit = ceil($ClassTimeLimit / (60)) ;

//if($ClassTimeLimit >= 10 || $ClassTimeLimit <= -10) {//수업전후 10분 이내가 아닐경우(즉, 입장불가) - 시작타임 전 10분, 시작타임 후 10분 이내일때만 1이 됨.
if($ClassTimeLimit < -10) {//수업시작시간보다 10분이상 이전일 경우
	$EnableClassTime = 0;
}else{
	$EnableClassTime = 1;
}
$ArrValue["EnableClassTime"] = $EnableClassTime;


$EnableClassTime = 1;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult;


function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');

?>