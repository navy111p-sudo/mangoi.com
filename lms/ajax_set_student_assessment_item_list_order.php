<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$StudentAssessmentItemID = isset($_REQUEST["StudentAssessmentItemID"]) ? $_REQUEST["StudentAssessmentItemID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";



//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from StudentAssessmentItems A where A.StudentAssessmentItemID=:StudentAssessmentItemID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':StudentAssessmentItemID', $StudentAssessmentItemID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$StudentAssessmentItemState = $Row["StudentAssessmentItemState"];
$StudentAssessmentItemID_1 = $Row["StudentAssessmentItemID"];
$StudentAssessmentItemOrder_1 = $Row["StudentAssessmentItemOrder"];


//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
if ($OrderType=="1"){//위로	
	$Sql = "select A.* from StudentAssessmentItems A where A.StudentAssessmentItemState=:StudentAssessmentItemState and A.StudentAssessmentItemOrder < :StudentAssessmentItemOrder_1 order by A.StudentAssessmentItemOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from StudentAssessmentItems A where A.StudentAssessmentItemState=:StudentAssessmentItemState and A.StudentAssessmentItemOrder > :StudentAssessmentItemOrder_1 order by A.StudentAssessmentItemOrder asc limit 0, 1 ";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':StudentAssessmentItemState', $StudentAssessmentItemState);
$Stmt->bindParam(':StudentAssessmentItemOrder_1', $StudentAssessmentItemOrder_1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$StudentAssessmentItemID_2 = $Row["StudentAssessmentItemID"];
$StudentAssessmentItemOrder_2 = $Row["StudentAssessmentItemOrder"];

if($StudentAssessmentItemID_2) {
	//  오더 순서 변경하기
	$Sql = "update StudentAssessmentItems set StudentAssessmentItemOrder=:StudentAssessmentItemOrder_2 where StudentAssessmentItemID=:StudentAssessmentItemID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':StudentAssessmentItemID_1', $StudentAssessmentItemID_1);
	$Stmt->bindParam(':StudentAssessmentItemOrder_2', $StudentAssessmentItemOrder_2);
	$Stmt->execute();

	$Sql = "update StudentAssessmentItems set StudentAssessmentItemOrder=:StudentAssessmentItemOrder_1 where StudentAssessmentItemID=:StudentAssessmentItemID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':StudentAssessmentItemOrder_1', $StudentAssessmentItemOrder_1);
	$Stmt->bindParam(':StudentAssessmentItemID_2', $StudentAssessmentItemID_2);
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