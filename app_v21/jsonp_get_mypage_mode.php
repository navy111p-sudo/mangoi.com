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
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";
$MypageTeacherModeHTML = "";

/*
if($LocalLinkMemberID) {
	$Sql = "select A.CenterDeviceName, B.MemberName from CenterDevices A 
	inner join Members B on B.MemberLoginID=:LocalLinkMemberID
	where A.CenterDeviceID=:LocalLinkDeviceID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(":LocalLinkMemberID", $LocalLinkMemberID);
	$Stmt->bindParam(":LocalLinkDeviceID", $LocalLinkDeviceID);
	$Stmt->execute();
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberName = $Row["MemberName"];
	$CenterDeviceName = $Row["CenterDeviceName"];

	$MypageTeacherModeHTML .= "<div class=\"mypage_teacher_mode_text\">현재 컴퓨터 이름은 <b class=\"color_orange\">".$CenterDeviceName."</b>입니다.</div>";
	if($LinkLoginMmemberID!=$LinkLoginAdminID) {
		$MypageTeacherModeHTML .= " <div class=\"mypage_teacher_mode_wait\"><?=$MemberName?>님 로그인 되었습니다.</div>";
	else {
		$MypageTeacherModeHTML .= " <div class=\"mypage_teacher_mode_wait\">대기중입니다.</div>";
	}
} else {
	$MypageTeacherModeHTML .= " <div class=\"mypage_teacher_mode_text\">현재 컴퓨터 이름이 <b class=\"color_orange\">설정</b>되지 않았습니다.</div>"
	$MypageTeacherModeHTML .= " <div class=\"mypage_teacher_mode_wait\">대기중입니다.</div>";
}
*/

$MypageTeacherModeHTML .= " <div class=\"mypage_teacher_mode_wait TrnTag\">대기중입니다.</div>";
$MypageTeacherModeHTML .= " <div class=\"button_wrap text_center\" style=\"padding:0 0 20px 0;\"><a type=\"button\" href=\"javascript:OpenUpLoginPopUp(<?=$LinkCenterID?>)\" class=\"button_whtie_border_arrow_2 TrnTag\">로그인</a></div>";

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MypageTeacherModeHTML"] = $MypageTeacherModeHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>


