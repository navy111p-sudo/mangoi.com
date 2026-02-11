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
$EventID = isset($_REQUEST["EventID"]) ? $_REQUEST["EventID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


$Sql = "select 
			* 
		from Events A 
		where A.EventID=:EventID";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EventID', $EventID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$today = strtotime(date("Y-m-d"));
$EventStatus = "";

$EventImageFileName = $Row["EventImageFileName"];
$EventTitle = $Row["EventTitle"];
$EventContent = $Row["EventContent"];
$TempEventRegDateTime = strtotime($Row["EventRegDateTime"]);
$TempEventStartDate = strtotime($Row["EventStartDate"]);
$TempEventEndDate = strtotime($Row["EventEndDate"]);
$StrEventRegDateTime = date("Y.m.d", $TempEventRegDateTime);

if ( $today > $TempEventEndDate ) {
	$EventStatus = "<span class=\"event_status\">종료</span>";
} elseif ($today >= $TempEventStartDate && $today <= $TempEventEndDate) {
	$EventStatus = "<span class=\"event_status ing\">진행중</span>";
} elseif ($today < $TempEventStartDate) {
	$EventStatus = "<span class=\"event_status ing\">예정</span>";
}

if($EventImageFileName!="") {
	$EventImageFileName = $AppDomain."/uploads/event_images/".$EventImageFileName;
} else {
	$EventImageFileName = $AppDomain."/uploads/event_images/no_photo_2.png";
}


$MainEventReadHTML = "";
$MainEventReadHTML .= "<div class=\"event_content_top\">";
$MainEventReadHTML .= "	<img src=\"".$EventImageFileName."\" class=\"event_content_img\">";
$MainEventReadHTML .= $EventStatus;
$MainEventReadHTML .= "</div>";
$MainEventReadHTML .= "<div class=\"event_content_area\">";
$MainEventReadHTML .= "	<h3 class=\"event_content_caption ellipsis\">".$EventTitle."</h3>";
$MainEventReadHTML .= $EventContent;
//$MainEventReadHTML .= "	- 혜택 : 망고아이 회원가입 시 1개월 체험권 지급<br>";
//$MainEventReadHTML .= "	- 기간 : 2019.06.30 ~ 2019.08.30";
$MainEventReadHTML .= "</div>";



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MainEventReadHTML"] = $MainEventReadHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>