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


$EnableClassTimeReset = 1;//1: 가능 2:시간문제 3:회수문제


//=========================== 시간 체크 ==============================
//  시간 비교용
$CurrentTime = date("Y-m-d H:i:s");//현재시간

//  클래스 생성 일자와 현재 시간 비교
$ClassTimeLimit =  (strtotime($CurrentTime) - $StartDateTimeStamp);
$ClassTimeLimit = ceil($ClassTimeLimit / (60)) ;

if($ClassTimeLimit > -30) {//수업시작시간보다 2시간 이내이면 비허용
	$EnableClassTimeReset = 2;
}
//=========================== 시간 체크 ==============================


//=========================== 회수 체크 ==============================
if ($EnableClassTimeReset == 1){


}
//=========================== 회수 체크 ==============================

$ArrValue["ClassTimeLimit"] = $ClassTimeLimit;
$ArrValue["EnableClassTimeReset"] = $EnableClassTimeReset;


$QueryResult = my_json_encode($ArrValue);
echo $QueryResult;


function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');

?>