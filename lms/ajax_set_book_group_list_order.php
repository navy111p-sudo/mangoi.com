<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$BookGroupID = isset($_REQUEST["BookGroupID"]) ? $_REQUEST["BookGroupID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";



//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from BookGroups A where A.BookGroupID=:BookGroupID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookGroupID', $BookGroupID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookGroupState = $Row["BookGroupState"];
$BookGroupID_1 = $Row["BookGroupID"];
$BookGroupOrder_1 = $Row["BookGroupOrder"];


//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
if ($OrderType=="1"){//위로	
	$Sql = "select A.* from BookGroups A where A.BookGroupState=:BookGroupState and A.BookGroupOrder < :BookGroupOrder_1 order by A.BookGroupOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from BookGroups A where A.BookGroupState=:BookGroupState and A.BookGroupOrder > :BookGroupOrder_1 order by A.BookGroupOrder asc limit 0, 1 ";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookGroupState', $BookGroupState);
$Stmt->bindParam(':BookGroupOrder_1', $BookGroupOrder_1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookGroupID_2 = $Row["BookGroupID"];
$BookGroupOrder_2 = $Row["BookGroupOrder"];

if($BookGroupID_2) {
	//  오더 순서 변경하기
	$Sql = "update BookGroups set BookGroupOrder=:BookGroupOrder_2 where BookGroupID=:BookGroupID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookGroupID_1', $BookGroupID_1);
	$Stmt->bindParam(':BookGroupOrder_2', $BookGroupOrder_2);
	$Stmt->execute();

	$Sql = "update BookGroups set BookGroupOrder=:BookGroupOrder_1 where BookGroupID=:BookGroupID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookGroupOrder_1', $BookGroupOrder_1);
	$Stmt->bindParam(':BookGroupID_2', $BookGroupID_2);
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