<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberEmail = isset($_REQUEST["MemberEmail"]) ? $_REQUEST["MemberEmail"] : "";

if ($MemberID!=""){
	$sql = "select count(*) as ExistCount from Members where MemberEmail=HEX(AES_ENCRYPT('$MemberEmail', '$EncryptionKey')) and MemberID<>$MemberID";
}else{
	$sql = "select count(*) as ExistCount from Members where MemberEmail=HEX(AES_ENCRYPT('$MemberEmail', '$EncryptionKey'))";
}
$rs = mysql_query($sql);
$ExistCount = current(mysql_fetch_array($rs));


if ($ExistCount==0){
	$ResultValue = "1";
}else{
	$ResultValue = "0";
}

$ArrValue["CheckResult"] = $ResultValue;


$Result = my_json_encode($ArrValue);
echo $Result; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>