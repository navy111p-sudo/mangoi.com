<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$TeacherPayTypeItemID = isset($_REQUEST["TeacherPayTypeItemID"]) ? $_REQUEST["TeacherPayTypeItemID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";



//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from TeacherPayTypeItems A where A.TeacherPayTypeItemID=:TeacherPayTypeItemID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherPayTypeItemID', $TeacherPayTypeItemID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TeacherPayTypeItemState = $Row["TeacherPayTypeItemState"];
$TeacherPayTypeItemID_1 = $Row["TeacherPayTypeItemID"];
$TeacherPayTypeItemOrder_1 = $Row["TeacherPayTypeItemOrder"];


//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
if ($OrderType=="1"){//위로	
	$Sql = "select A.* from TeacherPayTypeItems A where A.TeacherPayTypeItemState=:TeacherPayTypeItemState and A.TeacherPayTypeItemOrder < :TeacherPayTypeItemOrder_1 order by A.TeacherPayTypeItemOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from TeacherPayTypeItems A where A.TeacherPayTypeItemState=:TeacherPayTypeItemState and A.TeacherPayTypeItemOrder > :TeacherPayTypeItemOrder_1 order by A.TeacherPayTypeItemOrder asc limit 0, 1 ";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherPayTypeItemState', $TeacherPayTypeItemState);
$Stmt->bindParam(':TeacherPayTypeItemOrder_1', $TeacherPayTypeItemOrder_1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TeacherPayTypeItemID_2 = $Row["TeacherPayTypeItemID"];
$TeacherPayTypeItemOrder_2 = $Row["TeacherPayTypeItemOrder"];

if($TeacherPayTypeItemID_2) {
	//  오더 순서 변경하기
	$Sql = "update TeacherPayTypeItems set TeacherPayTypeItemOrder=:TeacherPayTypeItemOrder_2 where TeacherPayTypeItemID=:TeacherPayTypeItemID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherPayTypeItemID_1', $TeacherPayTypeItemID_1);
	$Stmt->bindParam(':TeacherPayTypeItemOrder_2', $TeacherPayTypeItemOrder_2);
	$Stmt->execute();

	$Sql = "update TeacherPayTypeItems set TeacherPayTypeItemOrder=:TeacherPayTypeItemOrder_1 where TeacherPayTypeItemID=:TeacherPayTypeItemID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherPayTypeItemOrder_1', $TeacherPayTypeItemOrder_1);
	$Stmt->bindParam(':TeacherPayTypeItemID_2', $TeacherPayTypeItemID_2);
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