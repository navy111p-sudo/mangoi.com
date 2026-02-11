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



$MemberID = $LocalLinkMemberID;

$Sql = "
		select 
				count(*) as TotalCount
		from AssmtStudentMonthlyScores A 

		where A.ClassID in (select ClassID from Classes where MemberID=$MemberID)  
		
		
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalCount = $Row["TotalCount"];


$PageReportMonthlyListHTML = "";

if ($TotalCount==0){

	$PageReportMonthlyListHTML .= "<li>";
	$PageReportMonthlyListHTML .= "	<div class=\"report_caption\">2019년 09월 평가서(샘플)</div>";
	$PageReportMonthlyListHTML .= "	<a href=\"javascript:OpenStudentScoreMonthlyReportSample();\" class=\"report_view_btn open-popup item-link item-content\" data-popup=\".popup-report\"><img src=\"images/icon_report_black.png\" class=\"icon\"></a>";
	$PageReportMonthlyListHTML .= "</li>";

}else{

		$Sql = "select 
					A.*
				from AssmtStudentMonthlyScores A 
				where A.ClassID in (select ClassID from Classes where MemberID=$MemberID)  
				order by A.AssmtStudentMonthlyScoreRegDateTime desc";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);


		$ii=1;
		while($Row = $Stmt->fetch()) {
			
			$AssmtStudentMonthlyScoreID = $Row["AssmtStudentMonthlyScoreID"];
			$AssmtStudentMonthlyScoreSubject = $Row["AssmtStudentMonthlyScoreSubject"];
			$AssmtStudentMonthlyScoreYear = $Row["AssmtStudentMonthlyScoreYear"];
			$AssmtStudentMonthlyScoreMonth = $Row["AssmtStudentMonthlyScoreMonth"];


			$PageReportMonthlyListHTML .= "<li>";
			$PageReportMonthlyListHTML .= "	<div class=\"report_caption\">".$AssmtStudentMonthlyScoreSubject."(".$AssmtStudentMonthlyScoreYear.".".substr("0".$AssmtStudentMonthlyScoreMonth,-2).")</div>";
			$PageReportMonthlyListHTML .= "	<a href=\"javascript:OpenStudentScoreMonthlyReport(".$AssmtStudentMonthlyScoreID.");\" class=\"report_view_btn open-popup item-link item-content\" data-popup=\".popup-report\"><img src=\"images/icon_report_black.png\" class=\"icon\"></a>";
			$PageReportMonthlyListHTML .= "</li>";

			$ii++;	
		}
		$Stmt = null;

}



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageReportMonthlyListHTML"] = $PageReportMonthlyListHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>