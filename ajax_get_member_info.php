<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";

// 디비에 넣는 작업
$Sql = "select A.MemberLevelID, A.MemberLoginID, A.MemberName from Members A where A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Row = $Stmt->fetch();

$MemberLevelID = $Row["MemberLevelID"];
$MemberLoginID = $Row["MemberLoginID"];
$MemberName = $Row["MemberName"];


$ArrValue["MemberLevelID"] = $MemberLevelID;
$ArrValue["MemberLoginID"] = $MemberLoginID;
$ArrValue["MemberName"] = $MemberName;

$QueryResult = my_json_encode($ArrValue);
//var_dump($QueryResult);
echo $QueryResult; 

$Stmt = null;

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>