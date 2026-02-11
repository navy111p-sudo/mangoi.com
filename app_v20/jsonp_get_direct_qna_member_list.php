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
		from DirectQnaMembers A 

		where A.MemberID=:LocalLinkMemberID and A.DirectQnaMemberState<>0 
		order by A.DirectQnaMemberRegDateTime desc";// limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':LocalLinkMemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



$PageDirectQnaListHTML = "";

$ListNum = 1;
while($Row = $Stmt->fetch()) {

	$DirectQnaMemberTitle = $Row["DirectQnaMemberTitle"];
	$DirectQnaMemberContent = $Row["DirectQnaMemberContent"];
	$DirectQnaMemberAnswer = $Row["DirectQnaMemberAnswer"];
	$DirectQnaMemberState = $Row["DirectQnaMemberState"];
	$StrDirectQnaMemberState = "";
	$DirectQnaMemberRegDateTime = $Row["DirectQnaMemberRegDateTime"];
	$TempDirectQnaMemberRegDateTime = date("Y.m.d", strtotime($DirectQnaMemberRegDateTime));

	if($DirectQnaMemberState == 1) {
		$StrDirectQnaMemberState = "답변대기중";
	} else if($DirectQnaMemberState == 2) {
		$StrDirectQnaMemberState = "<a href=\"#\" class=\"mantoman_btn\">답변보기 <span class=\"mantoman_arrow \"></span></a>";
	}


	$PageDirectQnaListHTML .= "<li class=\"accordion-item\">";
	$PageDirectQnaListHTML .= "	<div class=\"mtm_link accordion-item-toggle\">";
	$PageDirectQnaListHTML .= "		<div class=\"mtm_top\">";
	$PageDirectQnaListHTML .= "			<div class=\"mtm_icon\"><img src=\"".$ServerPath."images/icon_q_white.png\" class=\"icon\"></div>";
	$PageDirectQnaListHTML .= "			<div class=\"mtm_caption\">";
	$PageDirectQnaListHTML .= "				".$DirectQnaMemberTitle." ";
	$PageDirectQnaListHTML .= "			</div>";
	if($DirectQnaMemberState == 1) {
		$PageDirectQnaListHTML .= "			<div class=\"mtm_reply_state ing\">답변대기</div>";
	}else{
		$PageDirectQnaListHTML .= "			<div class=\"mtm_reply_state complete\">답변완료</div>";
	}	
	$PageDirectQnaListHTML .= "		</div>";
	$PageDirectQnaListHTML .= "		<div class=\"mtm_date\">".$TempDirectQnaMemberRegDateTime."</div>";
	$PageDirectQnaListHTML .= "	</div>";
	if($DirectQnaMemberState == 1) {
		$PageDirectQnaListHTML .= "	<div class=\"mtm_content accordion-item-content\" style=\"display:none;\">";
	}else{
		$PageDirectQnaListHTML .= "	<div class=\"mtm_content accordion-item-content\">";
	}
	$PageDirectQnaListHTML .= "		<div class=\"mtm_icon reply\"><img src=\"".$ServerPath."images/icon_a_white.png\" class=\"icon\"></div>";
	$PageDirectQnaListHTML .= "		<div class=\"mtm_reply\">";
	$PageDirectQnaListHTML .= "		".str_replace("\n","<br>",$DirectQnaMemberAnswer)." ";
	$PageDirectQnaListHTML .= "		</div>";
	$PageDirectQnaListHTML .= "	</div>";
	$PageDirectQnaListHTML .= "</li>";





	$ListNum++;
}



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageDirectQnaListHTML"] = $PageDirectQnaListHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>