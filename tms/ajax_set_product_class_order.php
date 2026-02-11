<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ProductClassID = isset($_REQUEST["ProductClassID"]) ? $_REQUEST["ProductClassID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";


//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from ProductClasses A where A.ProductClassID=:ProductClassID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductClassID', $ProductClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ProductID = $Row["ProductID"];
$ProductClassID_1 = $Row["ProductClassID"];
$ProductClassOrder_1 = $Row["ProductClassOrder"];


//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
if ($OrderType=="1"){//위로	
	$Sql = "select A.* from ProductClasses A where A.ProductClassState=1 and A.ProductClassOrder < :ProductClassOrder_1 and A.ProductID=:ProductID order by A.ProductClassOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from ProductClasses A where A.ProductClassState=1 and A.ProductClassOrder > :ProductClassOrder_1 and A.ProductID=:ProductID order by A.ProductClassOrder asc limit 0, 1 ";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductClassOrder_1', $ProductClassOrder_1);
$Stmt->bindParam(':ProductID', $ProductID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ProductClassID_2 = $Row["ProductClassID"];
$ProductClassOrder_2 = $Row["ProductClassOrder"];

if($ProductClassID_2) {
	//  오더 순서 변경하기
	$Sql = "update ProductClasses set ProductClassOrder=:ProductClassOrder_2 where ProductClassID=:ProductClassID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductClassID_1', $ProductClassID_1);
	$Stmt->bindParam(':ProductClassOrder_2', $ProductClassOrder_2);
	$Stmt->execute();

	$Sql = "update ProductClasses set ProductClassOrder=:ProductClassOrder_1 where ProductClassID=:ProductClassID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductClassOrder_1', $ProductClassOrder_1);
	$Stmt->bindParam(':ProductClassID_2', $ProductClassID_2);
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