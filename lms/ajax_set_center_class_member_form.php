<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ErrNum = 0;
$ErrMsg = "성공";

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$CenterClassMemberID = isset($_REQUEST["CenterClassMemberID"]) ? $_REQUEST["CenterClassMemberID"] : "";


$Sql = "select * from CenterCenterClassMembers where MemberID=:MemberID and CenterClassMemberID=:CenterClassMemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':CenterClassMemberID', $CenterClassMemberID);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$CenterClassMemberID = $Row["CenterClassMemberID"];


//$Sql = "select ifnull(Max(CenterClassMemberOrder),0) AS CenterClassMemberOrder from CenterCenterClassMembers";
$Sql = "select ifnull(Max(CenterClassMemberSeatNum),0) AS CenterClassMemberSeatNum from CenterCenterClassMembers";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$CenterClassMemberSeatNum = $Row["CenterClassMemberSeatNum"]+1;


if ($CenterClassMemberID){
	$Sql = "update CenterCenterClassMembers set 
					CenterClassMemberState=1,
					CenterClassMemberSeatNum=:CenterClassMemberSeatNum,
					CenterClassMemberModiDateTime=now() 
			where CenterClassMemberID=:CenterClassMemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterClassMemberSeatNum', $CenterClassMemberSeatNum);
	$Stmt->bindParam(':CenterClassMemberID', $CenterClassMemberID);
	$Stmt->execute();
	$Stmt = null;
}else{
	$Sql = "insert into CenterCenterClassMembers (
					CenterClassMemberID, 
					MemberID, 
					CenterClassMemberState, 
					CenterClassMemberOrder, 
					CenterClassMemberRegDateTime, 
					CenterClassMemberModiDateTime
			) values (
					:CenterClassMemberID, 
					:MemberID, 
					1, 
					:CenterClassMemberOrder, 
					now(), 
					now()
	)";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterClassMemberID', $CenterClassMemberID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':CenterClassMemberOrder', $CenterClassMemberOrder);
	$Stmt->execute();
	$Stmt = null;
}


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>