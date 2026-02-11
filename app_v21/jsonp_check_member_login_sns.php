<?
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
$Type = isset($_REQUEST["Type"]) ? $_REQUEST["Type"] : "";
$Id = isset($_REQUEST["Id"]) ? $_REQUEST["Id"] : "";
$Email = isset($_REQUEST["Email"]) ? $_REQUEST["Email"] : "";
$Name = isset($_REQUEST["Name"]) ? $_REQUEST["Name"] : "";
$DeviceType = isset($_REQUEST["DeviceType"]) ? $_REQUEST["DeviceType"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


$ApplyMemberLoginID = "";
$ApplyMemberLoginPW = "";

$MemberID = "";
$CenterID = "";
$MemberLevelID = "";
$LoginIP = $_SERVER['REMOTE_ADDR'];


$SnsName = "";
if($Type==1) {
    $SnsName = "kakao_";
} else if($Type==2) {
    $SnsName = "naver_";
} else if($Type==3) {
    $SnsName = "google_";
} else if($Type==4) {
    $SnsName = "facebook_";
}

$Id = $SnsName."".$Id;
$Email = $SnsName."".$Email;
$Pw = $Id;
$Pw_hash = password_hash(sha1($Pw), PASSWORD_DEFAULT);


$Sql = "select count(*) as TotalRowCount from Members where MemberState=1 and MemberLoginID=:Id";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':Id', $Id);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalRowCount = $Row["TotalRowCount"];
$MemberLoginInit = 0;

	// 계정이 없으면 만든다.
if ($TotalRowCount==0){

    $Sql = "insert into Members (
    `CenterID`, `MemberID`, `MemberLoginType`, `MemberLoginInit`, `MemberLoginID`, `MemberLoginPW`, `MemberName`, `MemberNickName`, `MemberBirthday`, `MemberPhone1`, `MemberEmail`, `MemberView`, `MemberState`, `MemberRegDateTime`, `MemberModiDateTime`
    ) values (
    1, NULL, :Type, :MemberLoginInit, :Id, :Pw_hash, :Name, :Name, '1991-01-01', HEX(AES_ENCRYPT('010--', :EncryptionKey)), HEX(AES_ENCRYPT(:Email, :EncryptionKey)), 1, 1, now(), now()
    )";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':Id', $Id);
	$Stmt->bindParam(':Type', $Type);
    $Stmt->bindParam(':Email', $Email);
    $Stmt->bindParam(':Name', $Name);
    $Stmt->bindParam(':Pw_hash', $Pw_hash);
	$Stmt->bindParam(':MemberLoginInit', $MemberLoginInit);
    $Stmt->bindParam(':EncryptionKey', $EncryptionKey);
    $Stmt->execute();
    $Stmt = null;


	
}else{ // 계정이 있다면


		$Sql = "select A.MemberID, A.MemberLevelID  
					from Members A 
					where MemberLoginID=:Id and MemberState=1";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':Id', $Id);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$MemberLevelID = $Row["MemberLevelID"];
		$MemberID = $Row["MemberID"];


		$Sql = "update Members set LastLoginDateTime=now() where MemberLoginID=:Id and MemberState=1";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':Id', $Id);
		$Stmt->execute();
		$Stmt = null;

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


$Sql = "select MemberID, CenterID, MemberLoginInit, MemberLoginID, MemberLoginPW from Members where MemberLoginID=:Id and MemberState=1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':Id', $Id);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;


$MemberID = $Row["MemberID"];
$CenterID = $Row["CenterID"];
$MemberLoginInit = $Row["MemberLoginInit"];
$MemberLoginID = $Row["MemberLoginID"];
$MemberLoginPW = $Row["MemberLoginPW"];


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MemberID"] = $MemberID;
$ArrValue["CenterID"] = $CenterID;
$ArrValue["MemberLevelID"] = $MemberLevelID;
$ArrValue["MemberLoginInit"] = $MemberLoginInit;

$ArrValue["ApplyMemberLoginID"] = $MemberLoginID;
$ArrValue["ApplyMemberLoginPW"] = $MemberLoginPW;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>