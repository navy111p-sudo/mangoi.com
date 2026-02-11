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
		from ReviewClassMembers A 

		where A.ReviewClassMemberState<>0 
		order by A.ReviewClassMemberRegDateTime desc";// limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':LocalLinkMemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



$PageMypageReviewListHTML = "";

$ListNum = 1;
while($Row = $Stmt->fetch()) {

	$MemberName = $Row["MemberName"];
	$AnswerMemberName = $Row["AnswerMemberName"];
	$ReviewClassMemberTitle = $Row["ReviewClassMemberTitle"];
	$ReviewClassMemberContent = $Row["ReviewClassMemberContent"];
	$ReviewClassMemberAnswer = $Row["ReviewClassMemberAnswer"];
	$ReviewClassMemberState = $Row["ReviewClassMemberState"];
	$StrReviewClassMemberState = "";
	$ReviewClassMemberRegDateTime = $Row["ReviewClassMemberRegDateTime"];
	$TempReviewClassMemberRegDateTime = date("Y.m.d", strtotime($ReviewClassMemberRegDateTime));

	$MemberNameCount = mb_strlen($MemberName, "UTF-8");
	$StrMemberName = iconv_substr($MemberName, 0,$MemberNameCount-1, "utf-8");
	$StrMemberName = $StrMemberName . "＊";

	/*
	$ReviewClassMemberTitle = str_replace("{{QUT}}", "&", $ReviewClassMemberTitle);
	$ReviewClassMemberTitle = str_replace("{{NL}}", "</br>", $ReviewClassMemberTitle);
	$ReviewClassMemberTitle = str_replace("{{AND}}", "?", $ReviewClassMemberTitle);
	
	$ReviewClassMemberContent = str_replace("{{QUT}}", "&", $ReviewClassMemberContent);
	$ReviewClassMemberContent = str_replace("{{NL}}", "</br>", $ReviewClassMemberContent);
	$ReviewClassMemberContent = str_replace("{{AND}}", "?", $ReviewClassMemberContent);
	*/

	$PageMypageReviewListHTML .= "<li class=\"accordion-item\">";
	$PageMypageReviewListHTML .= "	<div class=\"bbs_link accordion-item-toggle\">";
	//$PageMypageReviewListHTML .= "<li>";
	//$PageMypageReviewListHTML .= "	<a href=\"#\" class=\"bbs_link\">";
	$PageMypageReviewListHTML .= "		<div class=\"bbs_top\">";
	$PageMypageReviewListHTML .=	 "			<div class=\"bbs_icon review\">".$StrMemberName."</div>";
	$PageMypageReviewListHTML .=	 "			<div class=\"bbs_caption review\">".$ReviewClassMemberTitle."</div>";
	$PageMypageReviewListHTML .= "			<div class=\"bbs_arrow\"></div>";
	$PageMypageReviewListHTML .= "		</div>";
	$PageMypageReviewListHTML .= "		<div class=\"bbs_date review\">".$TempReviewClassMemberRegDateTime."</div>";
//	$PageMypageReviewListHTML .= "	</a>";
	$PageMypageReviewListHTML .= "	</div>";
	$PageMypageReviewListHTML .= "	<div class=\"bbs_content accordion-item-content\">".$ReviewClassMemberContent." ";

	// 답변
	if($ReviewClassMemberState==2) {
		//$PageMypageReviewListHTML .= "		<div class=\"bbs_top\">";
		$PageMypageReviewListHTML .= "			<table class=\"bbs_content_reply\">";
		$PageMypageReviewListHTML .= "				<tr>";
		$PageMypageReviewListHTML .= "					<th><img src=\"images/icon_reivew_reply.png\" class=\"icon_reivew_reply\"></th>";
		$PageMypageReviewListHTML .= "					<td>".$ReviewClassMemberAnswer."</td>";
		$PageMypageReviewListHTML .= "				</tr>";
		$PageMypageReviewListHTML .= "			</table>";
		//$PageMypageReviewListHTML .= "		</div>";
		$PageMypageReviewListHTML .= "	</div>";
	}
	/*
	if($ReviewClassMemberState == 1) {
		$StrReviewClassMemberState = "답변대기중";
	} else if($ReviewClassMemberState == 2) {
		$StrReviewClassMemberState = "<a href=\"#\" class=\"mantoman_btn\">답변보기 <span class=\"mantoman_arrow \"></span></a>";
	}


	$PageMypageReviewListHTML .= "<li class=\"accordion-item\">";
	$PageMypageReviewListHTML .= "	<div class=\"bbs_link accordion-item-toggle\">";
	$PageMypageReviewListHTML .= "		<div class=\"bbs_top\">";
	$PageMypageReviewListHTML .= "			<div class=\"bbs_icon review\">".$MemberName."</div>";//<img src=\"".$ServerPath."images/icon_q_white.png\" class=\"icon\"> class=\"mtm_icon\"
	$PageMypageReviewListHTML .= "			<div class=\"bbs_caption review\">";
	$PageMypageReviewListHTML .= "				".$ReviewClassMemberTitle." ";
	$PageMypageReviewListHTML .= "			</div>";

	if($ReviewClassMemberState == 1) {
		$PageMypageReviewListHTML .= "			<div class=\"mtm_reply_state ing\">답변대기</div>";
	}else{
		$PageMypageReviewListHTML .= "			<div class=\"mtm_reply_state complete\">답변완료</div>";
	}	

	//$PageMypageReviewListHTML .= "			<div class=\"mtm_reply_state complete\">".$MemberName."</div>";
	$PageMypageReviewListHTML .= "		</div>";
	$PageMypageReviewListHTML .= "		<div class=\"bbs_date review\">".$TempReviewClassMemberRegDateTime."</div>";
	$PageMypageReviewListHTML .= "	</div>";

	$PageMypageReviewListHTML .= "	<div class=\"bbs_content accordion-item-content\">";
	$PageMypageReviewListHTML .= "		<div></div>";//<img src=\"".$ServerPath."images/icon_a_white.png\" class=\"icon\"> class=\"mtm_icon reply\"
	$PageMypageReviewListHTML .= "		<div class=\"mtm_reply\">";
	$PageMypageReviewListHTML .= "		".str_replace("\n","<br>",$ReviewClassMemberContent)." ";
	$PageMypageReviewListHTML .= "		</div>";
	$PageMypageReviewListHTML .= "	</div>";


	$PageMypageReviewListHTML .= "	<div style=\"background-color: white;\">";
	$PageMypageReviewListHTML .= "		<div class=\"bbs_content accordion-item-content\">";
	$PageMypageReviewListHTML .= "			<div></div>";//<img src=\"".$ServerPath."images/icon_a_white.png\" class=\"icon\"> class=\"mtm_icon reply\"
	$PageMypageReviewListHTML .= "			<div class=\"mtm_reply\">";
	$PageMypageReviewListHTML .= "			".str_replace("\n","<br>",$AnswerMemberName)." ";
	$PageMypageReviewListHTML .= "			</div>";
	$PageMypageReviewListHTML .= "		</div>";
	$PageMypageReviewListHTML .= "		<div class=\"mtm_content accordion-item-content\">";
	$PageMypageReviewListHTML .= "			<div></div>";//<img src=\"".$ServerPath."images/icon_a_white.png\" class=\"icon\"> class=\"mtm_icon reply\"
	$PageMypageReviewListHTML .= "			<div class=\"mtm_reply\">";
	$PageMypageReviewListHTML .= "			".str_replace("\n","<br>",$ReviewClassMemberAnswer)." ";
	$PageMypageReviewListHTML .= "			</div>";
	$PageMypageReviewListHTML .= "		</div>";
	$PageMypageReviewListHTML .= "	</div>";
*/

	$PageMypageReviewListHTML .= "</li>";

	$ListNum++;
}



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageMypageReviewListHTML"] = $PageMypageReviewListHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>