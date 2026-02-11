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


$MainEventListHTML = "";

$Sql = "select 
			* 
		from Events A 
		where A.EventView=1 and A.EventState=1
		order by A.EventStartDate desc, A.EventEndDate desc limit 0,2";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


$today = strtotime(date("Y-m-d"));
$EventStatus = "";

while($Row = $Stmt->fetch()) {

	$TempEventRegDateTime = strtotime($Row["EventRegDateTime"]);
	$TempEventStartDate = strtotime($Row["EventStartDate"]);
	$TempEventEndDate = strtotime($Row["EventEndDate"]);

	if ( $today > $TempEventEndDate ) {
		$EventStatus = "<span class=\"event_status TrnTag\">종료</span>";
	} elseif ($today >= $TempEventStartDate && $today <= $TempEventEndDate) {
		$EventStatus = "<span class=\"event_status ing TrnTag\">진행중</span>";
	} elseif ($today < $TempEventStartDate) {
		$EventStatus = "<span class=\"event_status ing TrnTag\">예정</span>";
	}

	$EventID = $Row['EventID'];

	$EventImageFileName = $Row["EventImageFileName"];
	$EventTitle = $Row["EventTitle"];
	$EventContentSummary = $Row["EventContentSummary"];
	$EventContent = $Row["EventContent"];
	$StrEventRegDateTime = date("Y.m.d", $TempEventRegDateTime);

	if($EventImageFileName!="") {
		$EventImageFileName = $EventImageFileName;
	} else {
		$EventImageFileName = 'no_photo_2.png';
	}

	$MainEventListHTML .= "<li>";
	$MainEventListHTML .= "<a href=\"#\" class=\"item-link item-content open-popup\" data-popup=\".popup-event\" style=\"background-image:url(".$AppDomain."/uploads/event_images/".$EventImageFileName.")\" onclick=\"GetMainEventRead(".$EventID.")\"></a>";
	$MainEventListHTML .= "</li>";
}
$Stmt = null;

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MainEventListHTML"] = $MainEventListHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>