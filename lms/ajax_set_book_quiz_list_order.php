<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$BookQuizID = isset($_REQUEST["BookQuizID"]) ? $_REQUEST["BookQuizID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";
$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";



//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from BookQuizs A where A.BookQuizID=:BookQuizID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookQuizID', $BookQuizID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookQuizID_1 = $Row["BookQuizID"];
$BookQuizOrder_1 = $Row["BookQuizOrder"];


//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
if ($OrderType=="1"){//위로	
	$Sql = "select A.* from BookQuizs A where A.BookQuizOrder < :BookQuizOrder_1 and A.BookID=:BookID order by A.BookQuizOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from BookQuizs A where A.BookQuizOrder > :BookQuizOrder_1 and A.BookID=:BookID order by A.BookQuizOrder asc limit 0, 1 ";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookID', $BookID);
$Stmt->bindParam(':BookQuizOrder_1', $BookQuizOrder_1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookQuizID_2 = $Row["BookQuizID"];
$BookQuizOrder_2 = $Row["BookQuizOrder"];

if($BookQuizID_2) {
	//  오더 순서 변경하기
	$Sql = "update BookQuizs set BookQuizOrder=:BookQuizOrder_2 where BookQuizID=:BookQuizID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookQuizID_1', $BookQuizID_1);
	$Stmt->bindParam(':BookQuizOrder_2', $BookQuizOrder_2);
	$Stmt->execute();

	$Sql = "update BookQuizs set BookQuizOrder=:BookQuizOrder_1 where BookQuizID=:BookQuizID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookQuizOrder_1', $BookQuizOrder_1);
	$Stmt->bindParam(':BookQuizID_2', $BookQuizID_2);
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