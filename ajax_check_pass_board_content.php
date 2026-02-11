<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');

$StrPassword = isset($_REQUEST["StrPassword"]) ? $_REQUEST["StrPassword"] : "";
$StrID = isset($_REQUEST["StrID"]) ? $_REQUEST["StrID"] : "";

$StrPassword =  md5($StrPassword);


$Sql = "select count(*) as CountResult from BoardContents where BoardContentID=:StrID and BoardContentWriterPW=:StrPassword";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':StrID', $StrID);
$Stmt->bindParam(':StrPassword', $StrPassword);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$CountResult = $Row["CountResult"];


if ($CountResult==1){
	$QueryResult_check_id = "1";
	setcookie("BoardCheckSum",md5($StrID));
}else{
	$QueryResult_check_id = "0";
	setcookie("BoardCheckSum","");
}

$ArrValue["CheckResult"] = $QueryResult_check_id;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>