<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$TeacherAssessmentItemID = isset($_REQUEST["TeacherAssessmentItemID"]) ? $_REQUEST["TeacherAssessmentItemID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";



//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from TeacherAssessmentItems A where A.TeacherAssessmentItemID=:TeacherAssessmentItemID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherAssessmentItemID', $TeacherAssessmentItemID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TeacherAssessmentItemState = $Row["TeacherAssessmentItemState"];
$TeacherAssessmentItemID_1 = $Row["TeacherAssessmentItemID"];
$TeacherAssessmentItemOrder_1 = $Row["TeacherAssessmentItemOrder"];


//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
if ($OrderType=="1"){//위로	
	$Sql = "select A.* from TeacherAssessmentItems A where A.TeacherAssessmentItemState=:TeacherAssessmentItemState and A.TeacherAssessmentItemOrder < :TeacherAssessmentItemOrder_1 order by A.TeacherAssessmentItemOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from TeacherAssessmentItems A where A.TeacherAssessmentItemState=:TeacherAssessmentItemState and A.TeacherAssessmentItemOrder > :TeacherAssessmentItemOrder_1 order by A.TeacherAssessmentItemOrder asc limit 0, 1 ";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherAssessmentItemState', $TeacherAssessmentItemState);
$Stmt->bindParam(':TeacherAssessmentItemOrder_1', $TeacherAssessmentItemOrder_1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TeacherAssessmentItemID_2 = $Row["TeacherAssessmentItemID"];
$TeacherAssessmentItemOrder_2 = $Row["TeacherAssessmentItemOrder"];

if($TeacherAssessmentItemID_2) {
	//  오더 순서 변경하기
	$Sql = "update TeacherAssessmentItems set TeacherAssessmentItemOrder=:TeacherAssessmentItemOrder_2 where TeacherAssessmentItemID=:TeacherAssessmentItemID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherAssessmentItemID_1', $TeacherAssessmentItemID_1);
	$Stmt->bindParam(':TeacherAssessmentItemOrder_2', $TeacherAssessmentItemOrder_2);
	$Stmt->execute();

	$Sql = "update TeacherAssessmentItems set TeacherAssessmentItemOrder=:TeacherAssessmentItemOrder_1 where TeacherAssessmentItemID=:TeacherAssessmentItemID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherAssessmentItemOrder_1', $TeacherAssessmentItemOrder_1);
	$Stmt->bindParam(':TeacherAssessmentItemID_2', $TeacherAssessmentItemID_2);
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