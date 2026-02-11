<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$BookQuizDetailID = isset($_REQUEST["BookQuizDetailID"]) ? $_REQUEST["BookQuizDetailID"] : "";
$OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : "";
$BookQuizID = isset($_REQUEST["BookQuizID"]) ? $_REQUEST["BookQuizID"] : "";



//  인자로 넘겨받은 아이템의 상태값과 순서값 가져오기
$Sql = "select A.* from BookQuizDetails A where A.BookQuizDetailID=:BookQuizDetailID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookQuizDetailID', $BookQuizDetailID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookQuizDetailID_1 = $Row["BookQuizDetailID"];
$BookQuizDetailOrder_1 = $Row["BookQuizDetailOrder"];

//echo "1 ID : ".$BookQuizDetailID_1;
//echo "1 order : ".$BookQuizDetailOrder_1;
//echo $OrderType;

//  동일한 상태의 바로 위 또는 아래의 order 값을 가져오기
if ($OrderType=="1"){//위로	
	$Sql = "select A.* from BookQuizDetails A where A.BookQuizDetailOrder < :BookQuizDetailOrder_1 and A.BookQuizID=:BookQuizID order by A.BookQuizDetailOrder desc limit 0, 1 ";
}else{
	$Sql = "select A.* from BookQuizDetails A where A.BookQuizDetailOrder > :BookQuizDetailOrder_1 and A.BookQuizID=:BookQuizID order by A.BookQuizDetailOrder asc limit 0, 1 ";
}


//echo "select A.* from BookQuizDetails A where A.BookQuizDetailOrder > $BookQuizDetailOrder_1 and A.BookQuizID=$BookQuizID order by A.BookQuizDetailOrder asc limit 0, 1";


$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookQuizID', $BookQuizID);
$Stmt->bindParam(':BookQuizDetailOrder_1', $BookQuizDetailOrder_1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookQuizDetailID_2 = $Row["BookQuizDetailID"];
$BookQuizDetailOrder_2 = $Row["BookQuizDetailOrder"];

//echo "2 ID ".$BookQuizDetailID_2;
//echo "2 order : ".$BookQuizDetailOrder_2;


if($BookQuizDetailID_2) {
	//  오더 순서 변경하기
	
	$Sql = "update BookQuizDetails set BookQuizDetailOrder=:BookQuizDetailOrder_2 where BookQuizDetailID=:BookQuizDetailID_1";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookQuizDetailID_1', $BookQuizDetailID_1);
	$Stmt->bindParam(':BookQuizDetailOrder_2', $BookQuizDetailOrder_2);
	$Stmt->execute();

	$Sql = "update BookQuizDetails set BookQuizDetailOrder=:BookQuizDetailOrder_1 where BookQuizDetailID=:BookQuizDetailID_2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookQuizDetailOrder_1', $BookQuizDetailOrder_1);
	$Stmt->bindParam(':BookQuizDetailID_2', $BookQuizDetailID_2);
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