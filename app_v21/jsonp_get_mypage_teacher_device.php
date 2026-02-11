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
$TempLocalLinkDeviceID = isset($_REQUEST["TempLocalLinkDeviceID"]) ? $_REQUEST["TempLocalLinkDeviceID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";
$MypageTeacherDeviceList = "";
$MypageTeacherDeviceName = "";


if($TempLocalLinkDeviceID) { // 디바이스ID 값이 있다면
	$Sql_Name = "select CenterDeviceName from CenterDevices where CenterDeviceID=:TempLocalLinkDeviceID";
	$Stmt_Name = $DbConn->prepare($Sql_Name);
	$Stmt_Name->bindParam(":TempLocalLinkDeviceID", $TempLocalLinkDeviceID);
	$Stmt_Name->execute();
	$Row_Name = $Stmt_Name->fetch();
	$Stmt_Name = null;

	$CenterDeviceName = $Row_Name["CenterDeviceName"];

	$MypageTeacherDeviceName .= "현재 디바이스 이름은 <b class=\"color_orange\">".$CenterDeviceName."</b>입니다.";
} else { // 디바이스ID 값이 없다면
	$MypageTeacherDeviceName .= "현재 디바이스 이름이 <b class=\"color_orange\">설정</b>되지 않았습니다.";
}

$Sql_List = "select A.* from CenterDevices A where A.CenterID=:_LINK_MEMBER_CENTER_ID_";
$Stmt_List = $DbConn->prepare($Sql_List);
$Stmt_List->bindParam(':_LINK_MEMBER_CENTER_ID_', $_LINK_MEMBER_CENTER_ID_);
$Stmt_List->execute();
$Stmt_List->setFetchMode(PDO::FETCH_ASSOC);

$MypageTeacherDeviceList .= "<option value=\"\">디바이스를 선택하세요.</option>";

while($Row_List = $Stmt_List->fetch()) {
	$CenterDeviceID = $Row_List["CenterDeviceID"];
	$CenterDeviceName = $Row_List["CenterDeviceName"];
	//$MypageTeacherDeviceList .= "<option value=\"".$CenterDeviceID."\">[".$CenterDeviceID."] ".$CenterDeviceName."</option>";
	//$MypageTeacherDeviceList .= "<option value=\"".$CenterDeviceID."\""if($TempLocalLinkDeviceID==$LocalLinkDeviceID) echo "selected"; ">[".$CenterDeviceID."] ".$CenterDeviceName."</option>";
	$MypageTeacherDeviceList .= "<option value=\"".$CenterDeviceID."\"";
	if($TempLocalLinkDeviceID==$CenterDeviceName) {
		$MypageTeacherDeviceList .= " selected";
	}
	$MypageTeacherDeviceList .= ">[".$CenterDeviceID."] ".$CenterDeviceName."</option>";
}

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MyPageTeacherDeviceName"] = $MypageTeacherDeviceName;
$ArrValue["MypageTeacherDeviceList"] = $MypageTeacherDeviceList;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>


