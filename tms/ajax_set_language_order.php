<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$TrnLanguageID = isset($_REQUEST["TrnLanguageID"]) ? $_REQUEST["TrnLanguageID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";

//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from TrnLanguages A where A.TrnLanguageID=:TrnLanguageID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TrnLanguageID', $TrnLanguageID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TrnLanguageID = $Row["TrnLanguageID"];
$TrnLanguageID_1 = $Row["TrnLanguageID"];
$TrnLanguageOrder_1 = $Row["TrnLanguageOrder"];


//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
$AddWhere = " 1=1 ";
if ($SearchState!="100"){
	$AddWhere = $AddWhere . " A.TrnLanguageState=".$SearchState." ";
}
$AddWhere = $AddWhere . " A.TrnLanguageState<>0 ";

if ($OrderType=="1"){//위로	
	$Sql = "select A.* from TrnLanguages A where A.TrnLanguageState=1 and A.TrnLanguageOrder < :TrnLanguageOrder_1 order by A.TrnLanguageOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from TrnLanguages A where A.TrnLanguageState=1 and A.TrnLanguageOrder > :TrnLanguageOrder_1 order by A.TrnLanguageOrder asc limit 0, 1 ";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TrnLanguageOrder_1', $TrnLanguageOrder_1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TrnLanguageID_2 = $Row["TrnLanguageID"];
$TrnLanguageOrder_2 = $Row["TrnLanguageOrder"];

if($TrnLanguageID_2) {
	//  오더 순서 변경하기
	$Sql = "update TrnLanguages set TrnLanguageOrder=:TrnLanguageOrder_2 where TrnLanguageID=:TrnLanguageID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TrnLanguageID_1', $TrnLanguageID_1);
	$Stmt->bindParam(':TrnLanguageOrder_2', $TrnLanguageOrder_2);
	$Stmt->execute();

	$Sql = "update TrnLanguages set TrnLanguageOrder=:TrnLanguageOrder_1 where TrnLanguageID=:TrnLanguageID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TrnLanguageOrder_1', $TrnLanguageOrder_1);
	$Stmt->bindParam(':TrnLanguageID_2', $TrnLanguageID_2);
	$Stmt->execute();
}

$ArrValue["Result"] = 1;
$ResultValue = my_json_encode($ArrValue);
echo $ResultValue;



function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');

?>