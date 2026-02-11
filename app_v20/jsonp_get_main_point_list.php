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


if ($LocalLinkMemberID==""){//비회원일경우 아무것도 보여주지 않는다.
	$LocalLinkMemberID = -1;
}



$Sql = "
		select 
			A.*,
			date_format(A.MemberPointRegDateTime, '%Y.%m.%d') as MemberPointRegDate,
			(select sum(AA.MemberPoint) from MemberPoints AA where AA.MemberID=A.MemberID and AA.MemberPointState=1 and AA.MemberPointID<=A.MemberPointID) as TotalMemberPoint 
		from MemberPoints A
			inner join Members B on A.MemberID=B.MemberID 
		where A.MemberID=:LocalLinkMemberID and A.MemberPointState=1 
		order by A.MemberPointRegDateTime desc";// limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':LocalLinkMemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);		


$MainPointListHTML = "";
$MainPointListHTML .= "<div class=\"mypage_point_text\">포인트는 <b>1,000점 이상일 경우 현금</b>처럼 사용할 수 있습니다.</div>";
$MainPointListHTML .= "<table class=\"mypage_point_table\">";
$MainPointListHTML .= "	<col width=\"21%\">";
$MainPointListHTML .= "	<col width=\"\">";
$MainPointListHTML .= "	<col width=\"21%\">";
$MainPointListHTML .= "	<col width=\"21%\">";
$MainPointListHTML .= "	<tr>";
$MainPointListHTML .= "		<th>날짜</th>";
$MainPointListHTML .= "		<th>포인트내역</th>";
$MainPointListHTML .= "		<th>적립포인트</th>";
$MainPointListHTML .= "		<th>누적포인트</th>";
$MainPointListHTML .= "	</tr>";


while($Row = $Stmt->fetch()) {

	$MemberPointID = $Row["MemberPointID"];
	$MemberID = $Row["MemberID"];
	$RegMemberID = $Row["RegMemberID"];
	$MemberPointName = $Row["MemberPointName"];
	$MemberPointText = $Row["MemberPointText"];
	$MemberPoint = $Row["MemberPoint"];
	$MemberPointRegDateTime = $Row["MemberPointRegDateTime"];
	$MemberPointRegDate = $Row["MemberPointRegDate"];	
	$TotalMemberPoint = $Row["TotalMemberPoint"];


	$MainPointListHTML .= "	<tr>";
	$MainPointListHTML .= "		<td>".$MemberPointRegDate."</td>";
	$MainPointListHTML .= "		<td>".$MemberPointText."</td>";
	if ($MemberPoint>=0){
		$MainPointListHTML .= "		<td>".number_format($MemberPoint,0)."P</td>";
	}else{
		$MainPointListHTML .= "		<td class=\"point_minus\">- ".number_format(($MemberPoint*-1),0)."P</td>";
	}
	$MainPointListHTML .= "		<td>".number_format($TotalMemberPoint,0)."P</td>";
	$MainPointListHTML .= "	</tr>";
}
$Stmt = null;

$MainPointListHTML .= "</table>";


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MainPointListHTML"] = $MainPointListHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>