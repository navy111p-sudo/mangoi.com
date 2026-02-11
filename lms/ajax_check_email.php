<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberEmail = isset($_REQUEST["MemberEmail"]) ? $_REQUEST["MemberEmail"] : "";//앱에서 보낸것
$MemberEmail_1 = isset($_REQUEST["MemberEmail_1"]) ? $_REQUEST["MemberEmail_1"] : "";
$MemberEmail_2 = isset($_REQUEST["MemberEmail_2"]) ? $_REQUEST["MemberEmail_2"] : "";

if ($MemberEmail==""){
	$MemberEmail = $MemberEmail_1 . "@". $MemberEmail_2;
}

if ($MemberID!=""){
	$Sql = "select count(*) as ExistCount from Members where MemberEmail=HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)) and MemberID<>:MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberEmail', $MemberEmail);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->bindParam(':MemberID', $MemberID);
}else{
	$Sql = "select count(*) as ExistCount from Members where MemberEmail=HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey))";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberEmail', $MemberEmail);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
}
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ExistCount = $Row["ExistCount"];

if ($ExistCount==0){
	$QueryResult_check_id = "1";
}else{
	$QueryResult_check_id = "0";
}

$ArrValue["CheckResult"] = $QueryResult_check_id;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>