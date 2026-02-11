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
$TempMypageTeacherDashClass = isset($_REQUEST["TempMypageTeacherDashClass"]) ? $_REQUEST["TempMypageTeacherDashClass"] : "";
$TempMypageTeacherDashDevice = isset($_REQUEST["TempMypageTeacherDashDevice"]) ? $_REQUEST["TempMypageTeacherDashDevice"] : "";
$TempMypageTeacherDashMember = isset($_REQUEST["TempMypageTeacherDashMember"]) ? $_REQUEST["TempMypageTeacherDashMember"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


// ================================== 클래스는 클래스 아이디에 상관없이 실행됨.
$Sql_Class = "select * from CenterClasses where CenterID=:_LINK_MEMBER_CENTER_ID_";
$MypageTeacherDashClass = "";

$Stmt_Class = $DbConn->prepare($Sql_Class);
$Stmt_Class->bindParam(':_LINK_MEMBER_CENTER_ID_', $_LINK_MEMBER_CENTER_ID_);
$Stmt_Class->execute();
$Stmt_Class->setFetchMode(PDO::FETCH_ASSOC);

$MypageTeacherDashClass .= "<option class='TrnTag'>클래스를 선택하세요.</option>";
while($Row_Class = $Stmt_Class->fetch()){
	$CenterClassID = $Row_Class["CenterClassID"];
	$CenterClassName = $Row_Class["CenterClassName"];
	//$MypageTeacherDashClass .= "<option value=\"".$TempCenterClassID."\"</option>";
	$MypageTeacherDashClass .= "<option value=\"".$CenterClassID."\"";
	if($CenterClassID==$TempMypageTeacherDashClass) {
		$MypageTeacherDashClass .= " selected";
	}
	$MypageTeacherDashClass .= ">".$CenterClassName."</option>";
}
$Stmt_Class = null;


// ================================== 디바이스는 클래스 아이디에 상관없이 실행됨.
$Sql_Device = "select A.* from CenterDevices A where A.CenterID=:_LINK_MEMBER_CENTER_ID_";
$MypageTeacherDashDevice = "";

$Stmt_Device = $DbConn->prepare($Sql_Device);
$Stmt_Device->bindParam(':_LINK_MEMBER_CENTER_ID_', $_LINK_MEMBER_CENTER_ID_);
$Stmt_Device->execute();
$Stmt_Device->setFetchMode(PDO::FETCH_ASSOC);

$MypageTeacherDashDevice .= "<option value=\"\" class='TrnTag'>디바이스를 선택하세요.</option>";
while($Row_Device = $Stmt_Device->fetch()){
	$CenterDeviceID = $Row_Device["CenterDeviceID"];
	$CenterDeviceName = $Row_Device["CenterDeviceName"];
	//$MypageTeacherDeviceList .= "<option value=\"".$CenterDeviceID."\">[".$CenterDeviceID."] ".$CenterDeviceName."</option>";
	//$MypageTeacherDeviceList .= "<option value=\"".$CenterDeviceID."\""if($TempLocalLinkDeviceID==$LocalLinkDeviceID) echo "selected"; ">[".$CenterDeviceID."] ".$CenterDeviceName."</option>";
	$MypageTeacherDashDevice .= "<option value=\"".$CenterDeviceID."\"";
	if($TempMypageTeacherDashDevice==$CenterDeviceID) {
		$MypageTeacherDashDevice .= " selected";
	}
	$MypageTeacherDashDevice .= ">[".$CenterDeviceID."] ".$CenterDeviceName."</option>";
}
$Stmt_Device = null;


// 클래스 아이디가 넘어왔다면 실행
$MypageTeacherDashMember = "";
if($TempMypageTeacherDashClass) {
	$Sql_Dash = "select A.CenterClassMemberID, B.MemberName, B.MemberID, B.MemberLoginID, B.CenterID from CenterClassMembers A inner join Members B on A.MemberID=B.MemberID  where A.CenterClassID=:TempMypageTeacherDashClass and B.CenterID=:_LINK_MEMBER_CENTER_ID_";


	$Stmt_Dash = $DbConn->prepare($Sql_Dash);
	$Stmt_Dash->bindParam(':TempMypageTeacherDashClass', $TempMypageTeacherDashClass);
	$Stmt_Dash->bindParam(':_LINK_MEMBER_CENTER_ID_', $_LINK_MEMBER_CENTER_ID_);
	$Stmt_Dash->execute();
	$Stmt_Dash->setFetchMode(PDO::FETCH_ASSOC);

	$MypageTeacherDashMember .= "<option>학생을 선택하세요.</option>";
	while($Row_Dash = $Stmt_Dash->fetch()){
		$CenterClassMemberID = $Row_Dash["CenterClassMemberID"];
		$MemberName = $Row_Dash["MemberName"];
		$MemberLoginID = $Row_Dash["MemberLoginID"];
		$MemberID = $Row_Dash["MemberID"];

		$MypageTeacherDashMember .= "<option id=\"".$MemberLoginID."\" value=\"".$MemberID."\"";
		if($TempMypageTeacherDashMember==$MemberID) {
			$MypageTeacherDashMember .= " selected";
		}
		$MypageTeacherDashMember .= ">".$MemberName."</option>";

	}
	$Stmt_Dash = null;
} else { // 클래스 아이디가 없다면 실행
	$MypageTeacherDashMember .= "<option class='TrnTag'>학생을 선택하세요.</option>";
}


// 연결( Connection ) 내역 부분
$MypageTeacherDashList = "<tr><th class='TrnTag'>디바이스</th><th class='TrnTag'>학생명</th><th class='TrnTag'>설정</th></tr>";
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
		$MypageTeacherDashList .= "<td><a href=\"#\" class=\"btn_br_black TrnTag\" onclick=\"SetMypageDashConnectOff(".$MemberID.", ".$CenterDeviceID.")\">로그아웃</a></td></tr>";
	} else {
		$MypageTeacherDashList .= "<td><a href=\"#\" class=\"btn_br_black TrnTag\" onclick=\"SetMypageDashConnectOff(".$MemberID.", ".$CenterDeviceID.")\">미사용</a></td></tr>";
	}
}

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MypageTeacherDashClass"] = $MypageTeacherDashClass;
$ArrValue["MypageTeacherDashDevice"] = $MypageTeacherDashDevice;
$ArrValue["MypageTeacherDashMember"] = $MypageTeacherDashMember;
$ArrValue["MypageTeacherDashList"] = $MypageTeacherDashList;

$ResultValue = my_json_encode($ArrValue);

echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>


