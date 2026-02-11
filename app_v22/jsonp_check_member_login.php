<?
session_start();
$_SESSION['session_test'] = 'session_test';

header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/password_hash.php');


$ErrNum = 0;
$ErrMsg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$ApplyMemberLoginID = isset($_REQUEST["ApplyMemberLoginID"]) ? $_REQUEST["ApplyMemberLoginID"] : "";
$ApplyMemberLoginPW = isset($_REQUEST["ApplyMemberLoginPW"]) ? $_REQUEST["ApplyMemberLoginPW"] : "";
$DeviceType = isset($_REQUEST["DeviceType"]) ? $_REQUEST["DeviceType"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";

$MemberID = "";
$CenterID = "";
$MemberLevelID = "";
$LoginIP = $_SERVER['REMOTE_ADDR'];



$Sql = "select count(*) as TotalRowCount from Members where MemberState=1 and MemberLevelID<=19 and MemberLoginID=:ApplyMemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalRowCount = $Row["TotalRowCount"];


if ($TotalRowCount==0){
	$MemberLoginPW = "";
	$ErrNum = 1;
	$ErrMsg = "아이디를 잘못 입력하셨습니다.";
	
}else{

	
	$Sql = "select MemberLoginPW as MemberLoginPW_hash from Members where MemberState=1 and MemberLoginID=:ApplyMemberLoginID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$MemberLoginPW_hash = $Row["MemberLoginPW_hash"];

	$VerifyResult = password_verify(sha1($ApplyMemberLoginPW), $MemberLoginPW_hash);
	$Stmt = null;

	
	/*
	$Sql = "select count(*) as TotalRowCount from Members where MemberState=1 and  MemberLoginID=:ApplyMemberLoginID and MemberLoginPW=HEX(AES_ENCRYPT(:ApplyMemberLoginPW1, MD5(:ApplyMemberLoginPW2)))";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
	$Stmt->bindParam(':ApplyMemberLoginPW1', $ApplyMemberLoginPW);
	$Stmt->bindParam(':ApplyMemberLoginPW2', $ApplyMemberLoginPW);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$TotalRowCount = $Row["TotalRowCount"];
	*/

	if ($VerifyResult==false){
		$ErrNum = 2;
		$ErrMsg = "비밀번호를 잘못 입력하셨습니다.";

	//if ($TotalRowCount==0){
	//	$ErrNum = 2;
	//	$ErrMsg = "비밀번호를 잘못 입력하셨습니다.";
	}else{
		
		$Sql = "select A.MemberLevelID  
					from Members A 
					where MemberLoginID=:ApplyMemberLoginID and MemberState=1";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$MemberLevelID = $Row["MemberLevelID"];

		$Sql = "update Members set LastLoginDateTime=now() where MemberLoginID=:ApplyMemberLoginID and MemberState=1";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
		$Stmt->execute();
		$Stmt = null;


		$Sql = "select MemberID, CenterID from Members where MemberLoginID=:ApplyMemberLoginID and MemberState=1";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$MemberID = $Row["MemberID"];
		$CenterID = $Row["CenterID"];

		$Sql = " insert into MemberLoginIPs ( ";
			$Sql .= " MemberID, ";
			$Sql .= " MemberLoginType, ";
			$Sql .= " MemberLoginIP, ";
			$Sql .= " RegDateTime ";
		$Sql .= " ) values ( ";
			$Sql .= " :MemberID, ";
			$Sql .= " 3, ";
			$Sql .= " :LoginIP, ";
			$Sql .= " now() ";
		$Sql .= " ) ";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->bindParam(':LoginIP', $LoginIP);
		$Stmt->execute();
		$Stmt = null;

	}

}



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MemberID"] = $MemberID;
$ArrValue["CenterID"] = $CenterID;
$ArrValue["MemberLevelID"] = $MemberLevelID;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>