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
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


$Sql = "select count(*) as RowCount from Popups where PopupType=1 and DomainSiteID_".$DomainSiteID."=1 and AppPopup=1 and PopupState=1 and datediff( PopupStartDateNum, now())<=0 and  datediff( PopupEndDateNum, now())>=0";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SubCode', $SubCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

if ($Row["RowCount"]>0){
	$ExistPopup = 1;
} else {
	$ExistPopup = 0;
}

$PopupHTML = "";

if ($ExistPopup==1) {

	$Sql = "select * from Popups where PopupType=1 and DomainSiteID_".$DomainSiteID."=1 and AppPopup=1 and PopupState=1 and datediff( PopupStartDateNum, now())<=0 and  datediff( PopupEndDateNum, now())>=0  order by PopupID desc";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	$PopupHTML .= "<div class=\"in\">";
	$PopupHTML .= "	<div class=\"swiper-container popup_swiper\" style=\"height:auto;\">";
	$PopupHTML .= "		<div class=\"swiper-wrapper\">";

	while($Row = $Stmt->fetch()) {
		$PopupTitle = $Row["PopupTitle"];
		$PopupType = $Row["PopupType"];
		$PopupContent = $Row["PopupContent"];
		$PopupImage = $Row["PopupImage"];

		$PopupImageLink = $Row["PopupImageLink"];
		$PopupImageLinkType = $Row["PopupImageLinkType"];

		if (trim($PopupImageLink)!="") {
			$PopupImageLink = " onclick=\"OpenPopupLink('".$PopupImageLink."')\"";
		}

		$PopupHTML .= "	<img src=\"".$AppDomain."/uploads/popup_images/".$PopupImage."\" style=\"width:100%; display:block; ".$PopupImageLink." \">";

	}
	$Stmt = null;


	$PopupHTML .= "		</div>";
	$PopupHTML .= "	</div>";
	$PopupHTML .= "	<div class=\"pop_check\">";
	$PopupHTML .= "		<input type=\"checkbox\" name=\"pop_msg_close\" id=\"pop_msg_close\"> <label for=\"pop_msg_close\"><span></span> 오늘 하루 그만 보기</label>";
	$PopupHTML .= "		<a class=\"btn_pop_close\" onclick=\"PopupMsgClose();\">닫기</a>";
	$PopupHTML .= "	</div>";
	$PopupHTML .= "</div>";



}



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PopupHTML"] = $PopupHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>