<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$SelfAssessmentItemID = isset($_REQUEST["SelfAssessmentItemID"]) ? $_REQUEST["SelfAssessmentItemID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";



//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from SelfAssessmentItems A where A.SelfAssessmentItemID=:SelfAssessmentItemID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SelfAssessmentItemID', $SelfAssessmentItemID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$SelfAssessmentItemState = $Row["SelfAssessmentItemState"];
$SelfAssessmentItemID_1 = $Row["SelfAssessmentItemID"];
$SelfAssessmentItemOrder_1 = $Row["SelfAssessmentItemOrder"];


//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
if ($OrderType=="1"){//위로	
	$Sql = "select A.* from SelfAssessmentItems A where A.SelfAssessmentItemState=:SelfAssessmentItemState and A.SelfAssessmentItemOrder < :SelfAssessmentItemOrder_1 order by A.SelfAssessmentItemOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from SelfAssessmentItems A where A.SelfAssessmentItemState=:SelfAssessmentItemState and A.SelfAssessmentItemOrder > :SelfAssessmentItemOrder_1 order by A.SelfAssessmentItemOrder asc limit 0, 1 ";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SelfAssessmentItemState', $SelfAssessmentItemState);
$Stmt->bindParam(':SelfAssessmentItemOrder_1', $SelfAssessmentItemOrder_1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$SelfAssessmentItemID_2 = $Row["SelfAssessmentItemID"];
$SelfAssessmentItemOrder_2 = $Row["SelfAssessmentItemOrder"];

if($SelfAssessmentItemID_2) {
	//  오더 순서 변경하기
	$Sql = "update SelfAssessmentItems set SelfAssessmentItemOrder=:SelfAssessmentItemOrder_2 where SelfAssessmentItemID=:SelfAssessmentItemID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SelfAssessmentItemID_1', $SelfAssessmentItemID_1);
	$Stmt->bindParam(':SelfAssessmentItemOrder_2', $SelfAssessmentItemOrder_2);
	$Stmt->execute();

	$Sql = "update SelfAssessmentItems set SelfAssessmentItemOrder=:SelfAssessmentItemOrder_1 where SelfAssessmentItemID=:SelfAssessmentItemID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SelfAssessmentItemOrder_1', $SelfAssessmentItemOrder_1);
	$Stmt->bindParam(':SelfAssessmentItemID_2', $SelfAssessmentItemID_2);
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