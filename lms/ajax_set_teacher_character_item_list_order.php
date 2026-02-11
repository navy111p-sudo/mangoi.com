<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$TeacherCharacterItemID = isset($_REQUEST["TeacherCharacterItemID"]) ? $_REQUEST["TeacherCharacterItemID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";



//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from TeacherCharacterItems A where A.TeacherCharacterItemID=:TeacherCharacterItemID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherCharacterItemID', $TeacherCharacterItemID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TeacherCharacterItemState = $Row["TeacherCharacterItemState"];
$TeacherCharacterItemID_1 = $Row["TeacherCharacterItemID"];
$TeacherCharacterItemOrder_1 = $Row["TeacherCharacterItemOrder"];


//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
if ($OrderType=="1"){//위로	
	$Sql = "select A.* from TeacherCharacterItems A where A.TeacherCharacterItemState=:TeacherCharacterItemState and A.TeacherCharacterItemOrder < :TeacherCharacterItemOrder_1 order by A.TeacherCharacterItemOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from TeacherCharacterItems A where A.TeacherCharacterItemState=:TeacherCharacterItemState and A.TeacherCharacterItemOrder > :TeacherCharacterItemOrder_1 order by A.TeacherCharacterItemOrder asc limit 0, 1 ";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherCharacterItemState', $TeacherCharacterItemState);
$Stmt->bindParam(':TeacherCharacterItemOrder_1', $TeacherCharacterItemOrder_1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TeacherCharacterItemID_2 = $Row["TeacherCharacterItemID"];
$TeacherCharacterItemOrder_2 = $Row["TeacherCharacterItemOrder"];

if($TeacherCharacterItemID_2) {
	//  오더 순서 변경하기
	$Sql = "update TeacherCharacterItems set TeacherCharacterItemOrder=:TeacherCharacterItemOrder_2 where TeacherCharacterItemID=:TeacherCharacterItemID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherCharacterItemID_1', $TeacherCharacterItemID_1);
	$Stmt->bindParam(':TeacherCharacterItemOrder_2', $TeacherCharacterItemOrder_2);
	$Stmt->execute();

	$Sql = "update TeacherCharacterItems set TeacherCharacterItemOrder=:TeacherCharacterItemOrder_1 where TeacherCharacterItemID=:TeacherCharacterItemID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherCharacterItemOrder_1', $TeacherCharacterItemOrder_1);
	$Stmt->bindParam(':TeacherCharacterItemID_2', $TeacherCharacterItemID_2);
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