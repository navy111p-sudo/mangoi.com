<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$ProductCategoryID = isset($_REQUEST["ProductCategoryID"]) ? $_REQUEST["ProductCategoryID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";



//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from ProductCategories A where A.ProductCategoryID=:ProductCategoryID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductCategoryID', $ProductCategoryID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ProductCategoryState = $Row["ProductCategoryState"];
$ProductCategoryID_1 = $Row["ProductCategoryID"];
$ProductCategoryOrder_1 = $Row["ProductCategoryOrder"];


//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
if ($OrderType=="1"){//위로	
	$Sql = "select A.* from ProductCategories A where A.ProductCategoryState=:ProductCategoryState and A.ProductCategoryOrder < :ProductCategoryOrder_1 order by A.ProductCategoryOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from ProductCategories A where A.ProductCategoryState=:ProductCategoryState and A.ProductCategoryOrder > :ProductCategoryOrder_1 order by A.ProductCategoryOrder asc limit 0, 1 ";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductCategoryState', $ProductCategoryState);
$Stmt->bindParam(':ProductCategoryOrder_1', $ProductCategoryOrder_1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ProductCategoryID_2 = $Row["ProductCategoryID"];
$ProductCategoryOrder_2 = $Row["ProductCategoryOrder"];

if($ProductCategoryID_2) {
	//  오더 순서 변경하기
	$Sql = "update ProductCategories set ProductCategoryOrder=:ProductCategoryOrder_2 where ProductCategoryID=:ProductCategoryID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductCategoryID_1', $ProductCategoryID_1);
	$Stmt->bindParam(':ProductCategoryOrder_2', $ProductCategoryOrder_2);
	$Stmt->execute();

	$Sql = "update ProductCategories set ProductCategoryOrder=:ProductCategoryOrder_1 where ProductCategoryID=:ProductCategoryID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductCategoryOrder_1', $ProductCategoryOrder_1);
	$Stmt->bindParam(':ProductCategoryID_2', $ProductCategoryID_2);
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