<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$ProductOrderID = isset($_REQUEST["ProductOrderID"]) ? $_REQUEST["ProductOrderID"] : "";
$ReceiveName = isset($_REQUEST["ReceiveName"]) ? $_REQUEST["ReceiveName"] : "";
$ReceivePhone1 = isset($_REQUEST["ReceivePhone1"]) ? $_REQUEST["ReceivePhone1"] : "";
$ReceiveZipCode = isset($_REQUEST["ReceiveZipCode"]) ? $_REQUEST["ReceiveZipCode"] : "";
$ReceiveAddr1 = isset($_REQUEST["ReceiveAddr1"]) ? $_REQUEST["ReceiveAddr1"] : "";
$ReceiveAddr2 = isset($_REQUEST["ReceiveAddr2"]) ? $_REQUEST["ReceiveAddr2"] : "";
$ReceiveMemo = isset($_REQUEST["ReceiveMemo"]) ? $_REQUEST["ReceiveMemo"] : "";


$Sql = " update ProductOrders set ";
	$Sql .= " ReceiveName = :ReceiveName, ";
	$Sql .= " ReceivePhone1 = HEX(AES_ENCRYPT(:ReceivePhone1, :EncryptionKey)), ";
	$Sql .= " ReceiveZipCode = :ReceiveZipCode, ";
	$Sql .= " ReceiveAddr1 = :ReceiveAddr1, ";
	$Sql .= " ReceiveAddr2 = :ReceiveAddr2, ";
	$Sql .= " ReceiveMemo = :ReceiveMemo, ";
	$Sql .= " ProductOrderModiDateTime = now() ";
$Sql .= " where ProductOrderID=:ProductOrderID ";
$Stmt = $DbConn->prepare($Sql);

$Stmt->bindParam(':ReceiveName', $ReceiveName);
$Stmt->bindParam(':ReceivePhone1', $ReceivePhone1);
$Stmt->bindParam(':ReceiveZipCode', $ReceiveZipCode);
$Stmt->bindParam(':ReceiveAddr1', $ReceiveAddr1);
$Stmt->bindParam(':ReceiveAddr2', $ReceiveAddr2);
$Stmt->bindParam(':ReceiveMemo', $ReceiveMemo);

$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->bindParam(':ProductOrderID', $ProductOrderID);
$Stmt->execute();
$Stmt = null;




$ArrValue["ResultValue"] = 1;

$ResultValue = my_json_encode($ArrValue);
echo $ResultValue; 



function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>