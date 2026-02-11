<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$ClassOrderPayNumber = isset($_REQUEST["ClassOrderPayNumber"]) ? $_REQUEST["ClassOrderPayNumber"] : "";
$ClassOrderPayNumber_Origin = isset($_REQUEST["ClassOrderPayNumber_Origin"]) ? $_REQUEST["ClassOrderPayNumber_Origin"] : "";
$PayActionNum = isset($_REQUEST["PayActionNum"]) ? $_REQUEST["PayActionNum"] : "";


$NewClassOrderPayNumber = $ClassOrderPayNumber_Origin ."-". $PayActionNum;
$Sql = "update ClassOrderPays set 
			ClassOrderPayNumber=:NewClassOrderPayNumber,
			ClassOrderPayModiDateTime=now() 
		where ClassOrderPayNumber=:ClassOrderPayNumber ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':NewClassOrderPayNumber', $NewClassOrderPayNumber);
$Stmt->bindParam(':ClassOrderPayNumber', $ClassOrderPayNumber);
$Stmt->execute();
$Stmt = null;



$Sql = "
		select 
			A.*
		from ClassOrderPayDetails A 
		where A.ClassOrderPayID=(select ClassOrderPayID from ClassOrderPays where ClassOrderPayNumber='$NewClassOrderPayNumber') and A.ClassOrderPayDetailState=1 
		order by A.ClassOrderPayDetailID asc limit 0,1 
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ClassOrderPayMonthNumberID = $Row["ClassOrderPayMonthNumberID"];

$ArrValue["NewClassOrderPayNumber"] = $NewClassOrderPayNumber;
$ArrValue["AjaxCheckedClassOrderPayMonthNumberID"] = $ClassOrderPayMonthNumberID;


$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>