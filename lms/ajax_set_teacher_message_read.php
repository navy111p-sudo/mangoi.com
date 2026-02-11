<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$TeacherMessageID = isset($_REQUEST["TeacherMessageID"]) ? $_REQUEST["TeacherMessageID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
	
$Sql = " insert into TeacherMessageReads ( ";
	$Sql .= " TeacherMessageID, ";
	$Sql .= " MemberID, ";
	$Sql .= " TeacherMessageReadDateTime ";
$Sql .= " ) values ( ";
	$Sql .= " :TeacherMessageID, ";
	$Sql .= " :MemberID, ";
	$Sql .= " now() ";
$Sql .= " ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherMessageID', $TeacherMessageID);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt = null;



$ArrValue["TeacherMessageReadDateTime"] = date("Y-m-d H:i:s");

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>