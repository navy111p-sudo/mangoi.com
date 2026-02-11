<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$EventID = isset($_REQUEST["EventID"]) ? $_REQUEST["EventID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";



//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from Events A where A.EventID=:EventID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EventID', $EventID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$EventState = $Row["EventState"];
$EventID_1 = $Row["EventID"];
$EventOrder_1 = $Row["EventOrder"];


//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
if ($OrderType=="1"){//위로	
	$Sql = "select A.* from Events A where A.EventState=:EventState and A.EventOrder < :EventOrder_1 order by A.EventOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from Events A where A.EventState=:EventState and A.EventOrder > :EventOrder_1 order by A.EventOrder asc limit 0, 1 ";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EventState', $EventState);
$Stmt->bindParam(':EventOrder_1', $EventOrder_1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$EventID_2 = $Row["EventID"];
$EventOrder_2 = $Row["EventOrder"];

if($EventID_2) {
	//  오더 순서 변경하기
	$Sql = "update Events set EventOrder=:EventOrder_2 where EventID=:EventID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EventID_1', $EventID_1);
	$Stmt->bindParam(':EventOrder_2', $EventOrder_2);
	$Stmt->execute();

	$Sql = "update Events set EventOrder=:EventOrder_1 where EventID=:EventID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EventOrder_1', $EventOrder_1);
	$Stmt->bindParam(':EventID_2', $EventID_2);
	$Stmt->execute();
}

$ArrValue["Result"] = 1;
$QueryResult = my_json_encode($ArrValue);
echo $QueryResult;



function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');

?>