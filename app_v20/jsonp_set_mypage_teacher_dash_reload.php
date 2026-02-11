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
//$CenterClassID = isset($_REQUEST["CenterClassID"]) ? $_REQUEST["CenterClassID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


// 연결( Connection ) 내역 부분
$MypageTeacherDashList = "<tr><th>디바이스</th><th>학생명</th><th>설정</th></tr>";
$Sql_List = "SELECT A.*, B.MemberName, B.MemberID 
FROM CenterDevices A
INNER JOIN Members B ON A.CenterDeviceLastLoginMember=B.MemberID 
WHERE A.CenterID=:_LINK_MEMBER_CENTER_ID_";
$Stmt_List = $DbConn->prepare($Sql_List);
$Stmt_List->bindParam(':_LINK_MEMBER_CENTER_ID_', $_LINK_MEMBER_CENTER_ID_);
$Stmt_List->execute();
$Stmt_List->setFetchMode(PDO::FETCH_ASSOC);

while($Row_List = $Stmt_List->fetch()) {
	$CenterDeviceID = $Row_List["CenterDeviceID"];
	$CenterDeviceName = $Row_List["CenterDeviceName"];
	$CenterDeviceLastLoginMember = $Row_List["CenterDeviceLastLoginMember"];
	$MemberName = $Row_List["MemberName"];
	$MemberID = $Row_List["MemberID"];
	$MypageTeacherDashList .= "<tr><td>".$CenterDeviceName."</td><td id=\"".$MemberID."\">".$MemberName."</td>";

	if ($CenterDeviceLastLoginMember) {
		$MypageTeacherDashList .= "<td><a href=\"#\" class=\"btn_br_black\" onclick=\"SetMypageDashConnectOff(".$MemberID.", ".$CenterDeviceID.")\">로그아웃</a></td></tr>";
	} else {
		$MypageTeacherDashList .= "<td><a href=\"#\" class=\"btn_br_black\" onclick=\"SetMypageDashConnectOff(".$MemberID.", ".$CenterDeviceID.")\">미사용</a></td></tr>";
	}
}

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MypageTeacherDashList"] = $MypageTeacherDashList;

$ResultValue = my_json_encode($ArrValue);

echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>


