<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$BookScanID = isset($_REQUEST["BookScanID"]) ? $_REQUEST["BookScanID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";



//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from BookScans A where A.BookScanID=:BookScanID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookScanID', $BookScanID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookScanState = $Row["BookScanState"];
$BookScanID_1 = $Row["BookScanID"];
$BookScanOrder_1 = $Row["BookScanOrder"];


//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
if ($OrderType=="1"){//위로	
	$Sql = "select A.* from BookScans A where A.BookScanState=:BookScanState and A.BookScanOrder < :BookScanOrder_1 order by A.BookScanOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from BookScans A where A.BookScanState=:BookScanState and A.BookScanOrder > :BookScanOrder_1 order by A.BookScanOrder asc limit 0, 1 ";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookScanState', $BookScanState);
$Stmt->bindParam(':BookScanOrder_1', $BookScanOrder_1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookScanID_2 = $Row["BookScanID"];
$BookScanOrder_2 = $Row["BookScanOrder"];

if($BookScanID_2) {
	//  오더 순서 변경하기
	$Sql = "update BookScans set BookScanOrder=:BookScanOrder_2 where BookScanID=:BookScanID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookScanID_1', $BookScanID_1);
	$Stmt->bindParam(':BookScanOrder_2', $BookScanOrder_2);
	$Stmt->execute();

	$Sql = "update BookScans set BookScanOrder=:BookScanOrder_1 where BookScanID=:BookScanID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookScanOrder_1', $BookScanOrder_1);
	$Stmt->bindParam(':BookScanID_2', $BookScanID_2);
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