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
		from AssmtStudentLeveltestScores A 

		where A.ClassID in (select ClassID from Classes where MemberID=$MemberID)  
		
		
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalCount = $Row["TotalCount"];


$PageReportLeveltestListHTML = "";

if ($TotalCount==0){

	$PageReportLeveltestListHTML .= "<li>";
	$PageReportLeveltestListHTML .= "	<div class=\"level_report_caption\">";
	$PageReportLeveltestListHTML .= "		망고아이 레벨테스트(샘플)";
	$PageReportLeveltestListHTML .= "		<div class=\"level_date\">평가일 : 2019.09.28</div>";
	$PageReportLeveltestListHTML .= "	</div>";
	$PageReportLeveltestListHTML .= "	<a href=\"javascript:OpenStudentScoreLeveltestReportSample();\" class=\"level_view_btn open-popup\" data-popup=\".popup-report-level\"><img src=\"images/icon_report_black.png\" class=\"icon\"></a>";
	$PageReportLeveltestListHTML .= "</li>";

}else{

		$Sql = "select 
					A.*
				from AssmtStudentLeveltestScores A 
				where A.ClassID in (select ClassID from Classes where MemberID=$MemberID)  
				order by A.AssmtStudentLeveltestScoreRegDateTime desc";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);

		$ii=1;
		while($Row = $Stmt->fetch()) {
			$AssmtStudentLeveltestScoreID = $Row["AssmtStudentLeveltestScoreID"];
			$AssmtStudentLeveltestScoreYear = $Row["AssmtStudentLeveltestScoreYear"];
			$AssmtStudentLeveltestScoreMonth = $Row["AssmtStudentLeveltestScoreMonth"];
			$AssmtStudentLeveltestScoreDay = $Row["AssmtStudentLeveltestScoreDay"];
			$AssmtStudentLeveltestScoreLevel = $Row["AssmtStudentLeveltestScoreLevel"];


			$PageReportLeveltestListHTML .= "<li>";
			$PageReportLeveltestListHTML .= "	<div class=\"level_report_caption\">";
			$PageReportLeveltestListHTML .= "		레벨테스트 (LEVEL ".$AssmtStudentLeveltestScoreLevel.")";
			$PageReportLeveltestListHTML .= "		<div class=\"level_date\">평가일 : ".$AssmtStudentLeveltestScoreYear.".".substr("0".$AssmtStudentLeveltestScoreMonth,-2).".".substr("0".$AssmtStudentLeveltestScoreDay,-2)."</div>";
			$PageReportLeveltestListHTML .= "	</div>";
			$PageReportLeveltestListHTML .= "	<a href=\"javascript:OpenStudentScoreLeveltestReport(".$AssmtStudentLeveltestScoreID.");\" class=\"level_view_btn open-popup\" data-popup=\".popup-report-level\"><img src=\"images/icon_report_black.png\" class=\"icon\"></a>";
			$PageReportLeveltestListHTML .= "</li>";

			$ii++;	
		}
		$Stmt = null;

}



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageReportLeveltestListHTML"] = $PageReportLeveltestListHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>