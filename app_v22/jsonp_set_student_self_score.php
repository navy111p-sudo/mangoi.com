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
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$LocalMemberID = isset($_REQUEST["LocalMemberID"]) ? $_REQUEST["LocalMemberID"] : "";
$AssmtStudentSelfScore = isset($_REQUEST["AssmtStudentSelfScore"]) ? $_REQUEST["AssmtStudentSelfScore"] : "";
$DeviceType = isset($_REQUEST["DeviceType"]) ? $_REQUEST["DeviceType"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";

if ($DeviceType==""){
	$DeviceType = "Android";
}


//1:PC , 11:안드로이드 12:IOS
if ($DeviceType=="Android"){
	$DeviceType = 11;//Android
}else{
	$DeviceType = 12;//IOS
}


$Sql = " insert into AssmtStudentSelfScores ( ";
	$Sql .= " ClassID, ";
	$Sql .= " MemberID, ";
	$Sql .= " DeviceType, ";
	$Sql .= " AssmtStudentSelfScore, ";
	$Sql .= " AssmtStudentSelfScoreRegDateTime ";
$Sql .= " ) values ( ";
	$Sql .= " :ClassID, ";
	$Sql .= " :MemberID, ";
	$Sql .= " :DeviceType, ";
	$Sql .= " :AssmtStudentSelfScore, ";
	$Sql .= " now() ";
$Sql .= " ) ";


$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->bindParam(':MemberID', $LocalMemberID);
$Stmt->bindParam(':DeviceType', $DeviceType);
$Stmt->bindParam(':AssmtStudentSelfScore', $AssmtStudentSelfScore);
$Stmt->execute();
$Stmt = null;


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;

$ResultValue = my_json_encode($ArrValue);


echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');


?>