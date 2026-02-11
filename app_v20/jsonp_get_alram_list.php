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
			A.*,
			B.MemberName,
			B.MemberLoginID, 
			ifnull(H.MemberName,'시스템') as SendMemberName,
			ifnull(H.MemberLoginID, '-') as SendMemberLoginID,
			date_format(A.SendMessageDateTime, '%Y.%m.%d') as SendMessageDate
		from SendMessageLogs A
			inner join Members B on A.MemberID=B.MemberID 
			left outer join Members H on A.SendMemberID=H.MemberID 
		where A.MemberID=:LocalLinkMemberID and timestampdiff(second, A.SendMessageDateTime, now())>=0 
		order by A.SendMessageDateTime desc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':LocalLinkMemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



$PageAlramListHTML = "";

$ListNum = 1;
while($Row = $Stmt->fetch()) {

	$SendMessageLogID = $Row["SendMessageLogID"];
	$MemberID = $Row["MemberID"];
	$SendMemberID = $Row["SendMemberID"];
	$SendTitle = $Row["SendTitle"];
	$SendMessage = $Row["SendMessage"];
	
	$MemberName = $Row["MemberName"];
	$MemberLoginID = $Row["MemberLoginID"];
	$SendMemberName = $Row["SendMemberName"];
	$SendMemberLoginID = $Row["SendMemberLoginID"];

	$SendMessageDate = $Row["SendMessageDate"];
	$AppReadState = $Row["AppReadState"];
									
	if ($AppReadState==0){
		$AddStyle = "background-color:#F7F7F7;";
	}else{
		$AddStyle = "";
	}

	$PageAlramListHTML .= "<li class=\"accordion-item\" id=\"DivAlramListItem_".$SendMessageLogID."\" onclick=\"SetAlramCount(".$SendMessageLogID.")\" style=\"".$AddStyle."\">";
	$PageAlramListHTML .= "	<div class=\"bbs_link accordion-item-toggle\">";
	//$PageAlramListHTML .= "	<div class=\"bbs_link\">";
	$PageAlramListHTML .= "		<div class=\"bbs_top\">";
	$PageAlramListHTML .= "			<div class=\"bbs_icon\"><img src=\"".$ServerPath."images/icon_bbs_1.png\" class=\"img\"></div>";
	$PageAlramListHTML .= "			<div class=\"bbs_caption\">";
	$PageAlramListHTML .= "				".$SendMessage." ";
	$PageAlramListHTML .= "			</div>";
	$PageAlramListHTML .= "			<div class=\"bbs_arrow\"></div>";
	$PageAlramListHTML .= "		</div>";
	$PageAlramListHTML .= "		<div class=\"bbs_date\">".$SendMessageDate ."</div>";
	$PageAlramListHTML .= "	</div>";
	$PageAlramListHTML .= "	<div class=\"bbs_content accordion-item-content\">";
	$PageAlramListHTML .= "		".str_replace("\n","<br>",$SendMessage)." ";
	$PageAlramListHTML .= "	</div>";
	$PageAlramListHTML .= "</li>";


	$ListNum++;
}

if ($ListNum==1){

	$PageAlramListHTML .= "<li class=\"accordion-item\">";
	//$PageAlramListHTML .= "	<div class=\"bbs_link accordion-item-toggle\">";
	$PageAlramListHTML .= "	<div class=\"bbs_link\">";
	$PageAlramListHTML .= "		<div class=\"bbs_top\">";
	$PageAlramListHTML .= "			<div class=\"bbs_icon\"><img src=\"".$ServerPath."images/icon_bbs_1.png\" class=\"img\"></div>";
	$PageAlramListHTML .= "			<div class=\"bbs_caption\">";
	$PageAlramListHTML .= "				수신된 알림장이 없습니다.";
	$PageAlramListHTML .= "			</div>";
	$PageAlramListHTML .= "			<div class=\"bbs_arrow\"></div>";
	$PageAlramListHTML .= "		</div>";
	$PageAlramListHTML .= "		<div class=\"bbs_date\">Have a nice day!!</div>";
	$PageAlramListHTML .= "	</div>";
	//$PageAlramListHTML .= "	<div class=\"bbs_content accordion-item-content\">";
	//$PageAlramListHTML .= "		전화가 오지 않았는데 해당일에 수업 연결 녹취가 있고, 결석처리가 되었다면, 인터넷이나 전화 연결이 강사님 쪽이나, 회원님 쪽에서 간혹 오류가 발생할 수 있습니다.<br>";
	//$PageAlramListHTML .= "	</div>";
	$PageAlramListHTML .= "</li>";
}

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageAlramListHTML"] = $PageAlramListHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>