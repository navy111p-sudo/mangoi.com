<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$TrnCollectUrlID = isset($_REQUEST["TrnCollectUrlID"]) ? $_REQUEST["TrnCollectUrlID"] : "";
$TrnCollectUrlState = isset($_REQUEST["TrnCollectUrlState"]) ? $_REQUEST["TrnCollectUrlState"] : "";
if ($TrnCollectUrlState=="1"){
	$ChTrnCollectUrlState = 2;
}else{
	$ChTrnCollectUrlState = 1;
}


$Sql = "update TrnCollectUrls set TrnCollectUrlState=:ChTrnCollectUrlState where TrnCollectUrlID=:TrnCollectUrlID";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TrnCollectUrlID', $TrnCollectUrlID);
$Stmt->bindParam(':ChTrnCollectUrlState', $ChTrnCollectUrlState);
$Stmt->execute();

$ArrValue["Result"] = 1;
$ResultValue = my_json_encode($ArrValue);
echo $ResultValue;



function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');

?>