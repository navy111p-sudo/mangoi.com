<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$BookVideoID = isset($_REQUEST["BookVideoID"]) ? $_REQUEST["BookVideoID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";



//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from BookVideos A where A.BookVideoID=:BookVideoID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookVideoID', $BookVideoID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookVideoState = $Row["BookVideoState"];
$BookVideoID_1 = $Row["BookVideoID"];
$BookVideoOrder_1 = $Row["BookVideoOrder"];


//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
if ($OrderType=="1"){//위로	
	$Sql = "select A.* from BookVideos A where A.BookVideoState=:BookVideoState and A.BookVideoOrder < :BookVideoOrder_1 order by A.BookVideoOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from BookVideos A where A.BookVideoState=:BookVideoState and A.BookVideoOrder > :BookVideoOrder_1 order by A.BookVideoOrder asc limit 0, 1 ";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookVideoState', $BookVideoState);
$Stmt->bindParam(':BookVideoOrder_1', $BookVideoOrder_1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookVideoID_2 = $Row["BookVideoID"];
$BookVideoOrder_2 = $Row["BookVideoOrder"];

if($BookVideoID_2) {
	//  오더 순서 변경하기
	$Sql = "update BookVideos set BookVideoOrder=:BookVideoOrder_2 where BookVideoID=:BookVideoID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookVideoID_1', $BookVideoID_1);
	$Stmt->bindParam(':BookVideoOrder_2', $BookVideoOrder_2);
	$Stmt->execute();

	$Sql = "update BookVideos set BookVideoOrder=:BookVideoOrder_1 where BookVideoID=:BookVideoID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookVideoOrder_1', $BookVideoOrder_1);
	$Stmt->bindParam(':BookVideoID_2', $BookVideoID_2);
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