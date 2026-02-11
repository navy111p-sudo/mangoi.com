<?
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/member_check.php');

$ErrNum = 0;
$ErrMsg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


// 연결( Connection ) 내역 부분
$MemberFormSnsData = '<option value="140">소속단체 없음</option>';

$Sql = "select A.CenterID, A.CenterName from Centers A where OnlineSiteID=$OnlineSiteID and CenterState=1 and CenterView=1 and CenterID<>140";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

while($Row = $Stmt->fetch()) {
	$CenterName = $Row["CenterName"];
	$CenterID = $Row["CenterID"];
	$MemberFormSnsData .= '<option value="'.$CenterID.'">'.$CenterName.'</option>';
}

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MemberFormSnsData"] = $MemberFormSnsData;

$ResultValue = my_json_encode($ArrValue);

echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>


