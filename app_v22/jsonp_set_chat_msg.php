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
$LocalLinkMemberID = isset($_REQUEST["LocalLinkMemberID"]) ? $_REQUEST["LocalLinkMemberID"] : "";
$MangoTalkID = isset($_REQUEST["MangoTalkID"]) ? $_REQUEST["MangoTalkID"] : "";
$MangoTalkMsgType = isset($_REQUEST["MangoTalkMsgType"]) ? $_REQUEST["MangoTalkMsgType"] : "";
$ChatMsg = isset($_REQUEST["ChatMsg"]) ? $_REQUEST["ChatMsg"] : "";
$UploadedImage = isset($_REQUEST["UploadedImage"]) ? $_REQUEST["UploadedImage"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";

if ($MangoTalkMsgType==""){
	$MangoTalkMsgType = 1;//1:text 2:이미지
}



$MangoTalkImageName = "";
$MangoTalkImageSaveName = "";
if ($MangoTalkMsgType=="2"){
	$ArrUploadedImage = explode("|", $UploadedImage);
	$MangoTalkImageName = $ArrUploadedImage[1];
	$MangoTalkImageSaveName = $ArrUploadedImage[0];

	$ChatMsg = "[image]";
}



$Sql = "
	insert into MangoTalkMsgs ( ";
$Sql .= " MangoTalkID, ";
$Sql .= " MangoTalkMsgType, ";
$Sql .= " MemberID, ";
$Sql .= " MangoTalkMsg, ";
$Sql .= " MangoTalkImageName, ";
$Sql .= " MangoTalkImageSaveName, ";
$Sql .= " MangoTalkMsgRegDateTime ";
$Sql .= " ) values ( ";
$Sql .= " :MangoTalkID, ";
$Sql .= " :MangoTalkMsgType, ";
$Sql .= " :MemberID, ";
$Sql .= " :MangoTalkMsg, ";
$Sql .= " :MangoTalkImageName, ";
$Sql .= " :MangoTalkImageSaveName, ";
$Sql .= " now() ";
$Sql .= " ) ";
 
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MangoTalkID', $MangoTalkID);
$Stmt->bindParam(':MangoTalkMsgType', $MangoTalkMsgType);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->bindParam(':MangoTalkMsg', $ChatMsg);
$Stmt->bindParam(':MangoTalkImageName', $MangoTalkImageName);
$Stmt->bindParam(':MangoTalkImageSaveName', $MangoTalkImageSaveName);
$Stmt->execute();



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;



$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>