<?
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ErrNum = 0;
$ErrMsg = "성공";

$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$LocalMemberID = isset($_REQUEST["LocalMemberID"]) ? $_REQUEST["LocalMemberID"] : "";
$DeviceToken = isset($_REQUEST["DeviceToken"]) ? $_REQUEST["DeviceToken"] : "";
$DeviceType = isset($_REQUEST["DeviceType"]) ? $_REQUEST["DeviceType"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


if (!preg_match("/[0-9]/", $LocalMemberID)) { $LocalMemberID = 0; }

if ($DeviceToken!="null" && $DeviceToken!=""){

	$Sql = "select 
				count(*) as RowCount 
			from DeviceTokens
			where DeviceToken=:DeviceToken ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DeviceToken', $DeviceToken);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$RowCount = $Row["RowCount"];

	if ($RowCount>0){
		$Sql = "update DeviceTokens set MemberID=:LocalMemberID, ModiDateTime=now() where DeviceToken=:DeviceToken ";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':LocalMemberID', $LocalMemberID);
		$Stmt->bindParam(':DeviceToken', $DeviceToken);
		$Stmt->execute();
		$Stmt = null;
	}else{
		$Sql = "insert into DeviceTokens (MemberID, DeviceToken, DeviceType, RegistDateTime, ModiDateTime) values (:LocalMemberID, :DeviceToken, :DeviceType, now(), now())";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':LocalMemberID', $LocalMemberID);
		$Stmt->bindParam(':DeviceToken', $DeviceToken);
		$Stmt->bindParam(':DeviceType', $DeviceType);
		$Stmt->execute();
		$Stmt = null;
	}

}

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>