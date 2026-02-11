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
$LinkMemberID = isset($_REQUEST["LinkMemberID"]) ? $_REQUEST["LinkMemberID"] : "";
$LinkDeviceID = isset($_REQUEST["LinkDeviceID"]) ? $_REQUEST["LinkDeviceID"] : "";
$LinkCenterID = isset($_REQUEST["LinkCenterID"]) ? $_REQUEST["LinkCenterID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";
$MypageTeacherModeName = "";
$MypageTeacherModeState = "";

/*
if($LocalLinkDeviceID) {
	$Sql = "select A.CenterDeviceName, B.MemberName from CenterDevices A 
	inner join Members B on B.MemberID=:LocalLinkMemberID
	where A.CenterDeviceID=:LocalLinkDeviceID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(":LocalLinkMemberID", $LocalLinkMemberID);
	$Stmt->bindParam(":LocalLinkDeviceID", $LocalLinkDeviceID);
	$Stmt->execute();
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberName = $Row["MemberName"];
	$CenterDeviceName = $Row["CenterDeviceName"];

	$MypageTeacherModeName .= "현재 컴퓨터 이름은 <b class=\"color_orange\">".$CenterDeviceName."</b>입니다.";
	if($LocalLinkMemberID!=$LocalMemberID) {
		$MypageTeacherModeState .= "".$MemberName."님 로그인 되었습니다.";
	} else {
		$MypageTeacherModeState .= "대기중입니다.</div>";
	}
} else {
	$MypageTeacherModeName .= "현재 컴퓨터 이름이 <b class=\"color_orange\">설정</b>되지 않았습니다.";
	$MypageTeacherModeState .= "대기중입니다.";
}
*/


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MypageTeacherModeName"] = $MypageTeacherModeName;
//$ArrValue["MypageTeacherModeState"] = $MypageTeacherModeState;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>


