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
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


$Sql = "
		select 
			A.*
		from Suggestions A 

		where A.MemberID=:LocalLinkMemberID and A.SuggestionState<>0 
		order by A.SuggestionRegDateTime desc";// limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':LocalLinkMemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



$PageSuggestionListHTML = "";

$ListNum = 1;
while($Row = $Stmt->fetch()) {

	$SuggestionTitle = $Row["SuggestionTitle"];
	$SuggestionContent = $Row["SuggestionContent"];
	$SuggestionAnswer = $Row["SuggestionAnswer"];
	$SuggestionState = $Row["SuggestionState"];
	$StrSuggestionState = "";
	$SuggestionRegDateTime = $Row["SuggestionRegDateTime"];
	$TempSuggestionRegDateTime = date("Y.m.d", strtotime($SuggestionRegDateTime));

	if($SuggestionState == 1) {
		$StrSuggestionState = "답변대기중";
	} else if($SuggestionState == 2) {
		$StrSuggestionState = "<a href=\"#\" class=\"mantoman_btn\">답변보기 <span class=\"mantoman_arrow \"></span></a>";
	}


	$PageSuggestionListHTML .= "<li class=\"accordion-item\">";
	$PageSuggestionListHTML .= "	<div class=\"mtm_link accordion-item-toggle\">";
	$PageSuggestionListHTML .= "		<div class=\"mtm_top\">";
	$PageSuggestionListHTML .= "			<div class=\"mtm_icon\"><img src=\"".$ServerPath."images/icon_q_white.png\" class=\"icon\"></div>";
	$PageSuggestionListHTML .= "			<div class=\"mtm_caption\">";
	$PageSuggestionListHTML .= "				".$SuggestionTitle." ";
	$PageSuggestionListHTML .= "			</div>";
	if($SuggestionState == 1) {
		$PageSuggestionListHTML .= "			<div class=\"mtm_reply_state ing TrnTag\">답변대기</div>";
	}else{
		$PageSuggestionListHTML .= "			<div class=\"mtm_reply_state complete TrnTag\">답변완료</div>";
	}	
	$PageSuggestionListHTML .= "		</div>";
	$PageSuggestionListHTML .= "		<div class=\"mtm_date\">".$TempSuggestionRegDateTime."</div>";
	$PageSuggestionListHTML .= "	</div>";
	if($SuggestionState == 1) {
		$PageSuggestionListHTML .= "	<div class=\"mtm_content accordion-item-content\" style=\"display:none;\">";
	}else{
		$PageSuggestionListHTML .= "	<div class=\"mtm_content accordion-item-content\">";
	}
	$PageSuggestionListHTML .= "		<div class=\"mtm_icon reply\"><img src=\"".$ServerPath."images/icon_a_white.png\" class=\"icon\"></div>";
	$PageSuggestionListHTML .= "		<div class=\"mtm_reply\">";
	$PageSuggestionListHTML .= "		".str_replace("\n","<br>",$SuggestionAnswer)." ";
	$PageSuggestionListHTML .= "		</div>";
	$PageSuggestionListHTML .= "	</div>";
	$PageSuggestionListHTML .= "</li>";





	$ListNum++;
}



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageSuggestionListHTML"] = $PageSuggestionListHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>