<?php

header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$OrganType = isset($_REQUEST["OrganType"]) ? $_REQUEST["OrganType"] : "";
$OrganID = isset($_REQUEST["OrganID"]) ? $_REQUEST["OrganID"] : "";

$Sql = "
		select 
			A.*
		from TaxMemberInfos A
		where A.OrganType=:OrganType and A.OrganID=:OrganID
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':OrganType', $OrganType);
$Stmt->bindParam(':OrganID', $OrganID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TaxMemberInfoID= $Row["TaxMemberInfoID"];

if ($TaxMemberInfoID){
	$TaxMemberInfoID= $Row["TaxMemberInfoID"];
	$CorpName = $Row["CorpName"];
	$CorpNum = $Row["CorpNum"];
	$TaxRegID = $Row["TaxRegID"];
	$CEOName = $Row["CEOName"];
	$TEL1 = $Row["TEL1"];
	$HP1 = $Row["HP1"];
	$Addr = $Row["Addr"];
	$BizType = $Row["BizType"];
	$BizClass = $Row["BizClass"];
	$ContactName1 = $Row["ContactName1"];
	$Email1 = $Row["Email1"];
	$ContactName2 = $Row["ContactName2"];
	$Email2 = $Row["Email2"];
}else{
	$TaxMemberInfoID = 0;
	$CorpName =  "";
	$CorpNum =  "";
	$TaxRegID =  "";
	$CEOName =  "";
	$TEL1 =  "";
	$HP1 =  "";
	$Addr =  "";
	$BizType =  "";
	$BizClass =  "";
	$ContactName1 =  "";
	$Email1 =  "";
	$ContactName2 =  "";
	$Email2 =  "";
}

$CorpNum = substr($CorpNum, 0, 3)."-".substr($CorpNum, 3, 2)."-".substr($CorpNum, 5, 5);

$ArrValue["TaxMemberInfoID"] = $TaxMemberInfoID;
$ArrValue["CorpName"] = $CorpName;
$ArrValue["CorpNum"] = $CorpNum;
$ArrValue["TaxRegID"] = $TaxRegID;
$ArrValue["CEOName"] = $CEOName;
$ArrValue["TEL1"] = $TEL1;
$ArrValue["HP1"] = $HP1;
$ArrValue["Addr"] = $Addr;
$ArrValue["BizType"] = $BizType;
$ArrValue["BizClass"] = $BizClass;
$ArrValue["ContactName1"] = $ContactName1;
$ArrValue["Email1"] = $Email1;
$ArrValue["ContactName2"] = $ContactName2;
$ArrValue["Email2"] = $Email2;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult;


function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');

?>