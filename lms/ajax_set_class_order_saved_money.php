<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";

$SavedMoney = isset($_REQUEST["SavedMoney"]) ? $_REQUEST["SavedMoney"] : "";

$RegMemberID = $_LINK_ADMIN_ID_;

$SavedMoneyPayNumber = "ML".date("YmdHis").substr("0000000000".$_LINK_ADMIN_ID_,-10); // ML -> Mangoi Lms

//=========================================================================================
$Sql = "INSERT INTO SavedMoney (
				SavedMoneyType, 
				CenterID,
				SavedMoney,
				SavedMoneyPayNumber,
				SavedMoneyRegDateTime,
				RegMemberID
			) 
			VALUES ( 
				1,
				:CenterID,
				:SavedMoney,
				:SavedMoneyPayNumber,
				now(),
				:RegMemberID
			)";


$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':CenterID', $CenterID);
$Stmt->bindParam(':SavedMoney', $SavedMoney);
$Stmt->bindParam(':SavedMoneyPayNumber', $SavedMoneyPayNumber);
$Stmt->bindParam(':RegMemberID', $RegMemberID);
$Stmt->execute();
$SavedMoneyID = $DbConn->lastInsertId();
$Stmt = null;



//=========================================================================================

$ArrValue["SavedMoneyID"] = $SavedMoneyID;		//충전금아이디
$ArrValue["SavedMoney"] = $SavedMoney;			//충전금액
$ArrValue["SavedMoneyPayNumber"] = $SavedMoneyPayNumber;	//충전넘버
//$ArrValue["Sql"] = $Sql;	//충전넘버


$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>