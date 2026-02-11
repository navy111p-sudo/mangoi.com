<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');

$NewID = isset($_REQUEST["NewID"]) ? $_REQUEST["NewID"] : "";
$MemberLevelID = isset($_REQUEST["MemberLevelID"]) ? $_REQUEST["MemberLevelID"] : "";
$CampusID = isset($_REQUEST["CampusID"]) ? $_REQUEST["CampusID"] : "";

$NewID = trim($NewID);

$ErrNum = 0;
$ErrMsg = "";

$Sql = "select count(*) as ExistCount from Members where MemberLoginID=:NewID and MemberLevelID=:MemberLevelID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':NewID', $NewID);
$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ExistCount = $Row["ExistCount"];

if ($ExistCount==0){
	$ErrNum = 1;
	$ErrMsg = "아이디를 잘못 입력했습니다.";
	$QueryResult_check_id = 0;
}

if ($ErrNum==0){
	$Sql = "select count(*) as ExistCount 
				from CampusMembers A 
					inner join Members B on A.MemberID=B.MemberID 
			where 
				B.MemberLoginID=:NewID 
				and B.MemberLevelID=:MemberLevelID 
				and A.CampusID=:CampusID 
				";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':NewID', $NewID);
	$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
	$Stmt->bindParam(':CampusID', $CampusID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$ExistCount = $Row["ExistCount"];

	if ($ExistCount==0){
		$QueryResult_check_id = "1";
		$ErrMsg = "등록가능합니다.";
	}else{
		$QueryResult_check_id = "0";
		if ($MemberLevelID=="8"){
			$ErrMsg = "이미 등록된 학부모 입니다.";
		}else if ($MemberLevelID=="9"){
			$ErrMsg = "이미 등록된 학생 입니다.";
		}
	}
}


if ($ErrNum==0){
	$Sql = "select * from Members where MemberLoginID=:NewID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':NewID', $NewID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberID = $Row["MemberID"];
	$MemberName = $Row["MemberName"];
}

$ArrValue["CheckResult"] = $QueryResult_check_id;
$ArrValue["MemberID"] = $MemberID;
$ArrValue["MemberName"] = $MemberName;
$ArrValue["ErrMsg"] = $ErrMsg;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>