<?
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/member_check.php');

$err_num = 0;
$err_msg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$DeviceID = isset($_REQUEST["DeviceID"]) ? $_REQUEST["DeviceID"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";

// 디비에 넣는 작업
$Sql = "update CenterDevices set CenterDeviceLastLoginMember=:MemberID where CenterDeviceID=:DeviceID and CenterID=:CenterID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':DeviceID', $DeviceID);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':CenterID', $CenterID);
$Stmt->execute();
// $Stmt->setFetchMode(PDO::MYSQL_ATTR_FOUND_ROWS);
// $RowCount = $Stmt->rowCount();// 업데이트 된 로우값


// 업데이트 되었는지 확인하는 코드 추가할것..
$ArrValue["Result"] = '1';

$ResultValue = my_json_encode($ArrValue);

$Stmt = null;

echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>
