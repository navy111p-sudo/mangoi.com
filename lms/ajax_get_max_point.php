<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$RegMemberID = isset($_REQUEST["RegMemberID"]) ? $_REQUEST["RegMemberID"] : "";
$value = isset($_REQUEST["value"]) ? $_REQUEST["value"] : "";

$Sql = "
	select 
		ifnull(sum(A.MemberPoint),0) as TotalPoint 
	from MemberPoints A 
	where 
		A.MemberID=:MemberID
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $RegMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalPoint = $Row["TotalPoint"];

if($value > $TotalPoint) {
	$result = $TotalPoint;
	$code = 0;
} else {
	$result = $value;
	$code = 1;
}

$ArrValue["result"] = $result;
$ArrValue["code"] = $code;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>