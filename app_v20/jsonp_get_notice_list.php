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


$Sql = "select 
				*,
				date_format(BoardContentRegDateTime, '%Y.%m.%d') as BoardContentRegDate
		from BoardContents where BoardID=1 and BoardContentState=1 order by BoardContentID DESC LIMIT 0,5";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



$PageNoticeListHTML = "";

$ListNum = 1;
while($Row = $Stmt->fetch()){

	$BoardContentID = $Row["BoardContentID"];
	$BoardContentSubject = $Row["BoardContentSubject"];
	$BoardContent = $Row["BoardContent"];
	$BoardContentRegDate = $Row["BoardContentRegDate"];

	$PageNoticeListHTML .= "<li class=\"accordion-item\">";
	$PageNoticeListHTML .= "	<div class=\"bbs_link accordion-item-toggle\">";
	$PageNoticeListHTML .= "		<div class=\"bbs_top\">";
	$PageNoticeListHTML .= "			<div class=\"bbs_icon\"><img src=\"".$ServerPath."images/icon_bbs_1.png\" class=\"img\"></div>";
	$PageNoticeListHTML .= "			<div class=\"bbs_caption\">";
	$PageNoticeListHTML .= "				".$BoardContentSubject." ";
	$PageNoticeListHTML .= "			</div>";
	$PageNoticeListHTML .= "			<div class=\"bbs_arrow\"></div>";
	$PageNoticeListHTML .= "		</div>";
	$PageNoticeListHTML .= "		<div class=\"bbs_date\">".$BoardContentRegDate."</div>";
	$PageNoticeListHTML .= "	</div>";
	$PageNoticeListHTML .= "	<div class=\"bbs_content accordion-item-content\">";
	$PageNoticeListHTML .= "		".$BoardContent." ";
	$PageNoticeListHTML .= "	</div>";
	$PageNoticeListHTML .= "</li>";


	$ListNum++;
}



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageNoticeListHTML"] = $PageNoticeListHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>