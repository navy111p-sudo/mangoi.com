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


//주간 포인트
$TodayWeek = date('w', strtotime(date("Y-m-d")));
$PointStartDate = date("Y-m-d", strtotime("-".$TodayWeek." day", strtotime(date("Y-m-d"))));
$PointEndDate = date("Y-m-d", strtotime((6-$TodayWeek)." day", strtotime(date("Y-m-d"))));

$ViewTable1 = "
	select 
		A.MemberID, 
		sum(A.MemberPoint) as MemberTotalPoint 
	from MemberPoints A 
	where 
		A.MemberPointState=1 
		and datediff(A.MemberPointRegDateTime, '".$PointStartDate."')>=0 and datediff(A.MemberPointRegDateTime, '".$PointEndDate."')<=0 
	group by A.MemberID 
";


//월간 포인트
$PointStartDate = date("Y-m-01");
$PointEndDate = date("Y-m-").date('t', strtotime(date("Y-m-01")));

$ViewTable2 = "
	select 
		A.MemberID, 
		sum(A.MemberPoint) as MemberTotalPoint 
	from MemberPoints A 
	where 
		A.MemberPointState=1 
		and datediff(A.MemberPointRegDateTime, '".$PointStartDate."')>=0 and datediff(A.MemberPointRegDateTime, '".$PointEndDate."')<=0 
	group by A.MemberID 
";


//전체 포인트
$ViewTable3 = "
	select 
		A.MemberID, 
		sum(A.MemberPoint) as MemberTotalPoint 
	from MemberPoints A 
	where 
		A.MemberPointState=1 
	group by A.MemberID 
";




//주간 포인트 HTML 시작 ==============================
$MainPointRankList = "";
$MainPointRankList .= "<div id=\"tab1\" class=\"tab active point_rank_box\">";
$MainPointRankList .= "	<table class=\"point_rank_table\">";
$MainPointRankList .= "		<col width=\"12%\">";
$MainPointRankList .= "		<col width=\"30%\">";
$MainPointRankList .= "		<col width=\"\">";

$Sql = "
	select 
		A.MemberID,
		A.MemberTotalPoint,
		B.MemberLoginID,
		B.MemberName,
		C.CenterName
	from (".$ViewTable1.") A 
		inner join Members B on A.MemberID=B.MemberID 
		inner join Centers C on B.CenterID=C.CenterID 
	where B.MemberLevelID=19 
	order by A.MemberTotalPoint desc limit 0, 10
";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListCount = 1;
while($Row = $Stmt->fetch()) {
	$MemberID = $Row["MemberID"];
	$MemberLoginID = $Row["MemberLoginID"];
	$MemberName = $Row["MemberName"];
	$MemberTotalPoint = $Row["MemberTotalPoint"];
	$CenterName = $Row["CenterName"];

	if ($ListCount==1){
		$TopPoint = $MemberTotalPoint;
	}
	if ($TopPoint>0){
		$PointChartLength = round( (($MemberTotalPoint / $TopPoint) * 100) ,2);
	}else{
		$PointChartLength = 0;
	}

	$MainPointRankList .= "		<tr>";
	$MainPointRankList .= "			<td><span class=\"point_rank_number\">".$ListCount."</span></td>";
	$MainPointRankList .= "			<td><small class=\"point_rank_academy\">".$CenterName."</small>".$MemberName."</td>";
	$MainPointRankList .= "			<td>";
	$MainPointRankList .= "				<span class=\"point_rank_score\">".number_format($MemberTotalPoint,0)."</span>";
	$MainPointRankList .= "				<div class=\"bar_back\">";
	$MainPointRankList .= "					<div class=\"bar_front\" style=\"width:".$PointChartLength."%;\"></div>";
	$MainPointRankList .= "				</div>";
	$MainPointRankList .= "			</td>";
	$MainPointRankList .= "		</tr>";

	$ListCount++;
}
$Stmt = null;


$MainPointRankList .= "	</table>";
$MainPointRankList .= "</div>";


//월간 포인트 HTML 시작 ==============================
$MainPointRankList .= "<div id=\"tab2\" class=\"tab point_rank_box\">";
$MainPointRankList .= "	<table class=\"point_rank_table\">";
$MainPointRankList .= "		<col width=\"12%\">";
$MainPointRankList .= "		<col width=\"30%\">";
$MainPointRankList .= "		<col width=\"\">";

$Sql = "
	select 
		A.MemberID,
		A.MemberTotalPoint,
		B.MemberLoginID,
		B.MemberName,
		C.CenterName
	from (".$ViewTable2.") A 
		inner join Members B on A.MemberID=B.MemberID 
		inner join Centers C on B.CenterID=C.CenterID 
	where B.MemberLevelID=19 
	order by A.MemberTotalPoint desc limit 0, 10
";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListCount = 1;
while($Row = $Stmt->fetch()) {
	$MemberID = $Row["MemberID"];
	$MemberLoginID = $Row["MemberLoginID"];
	$MemberName = $Row["MemberName"];
	$MemberTotalPoint = $Row["MemberTotalPoint"];
	$CenterName = $Row["CenterName"];

	if ($ListCount==1){
		$TopPoint = $MemberTotalPoint;
	}
	if ($TopPoint>0){
		$PointChartLength = round( (($MemberTotalPoint / $TopPoint) * 100) ,2);
	}else{
		$PointChartLength = 0;
	}

	$MainPointRankList .= "		<tr>";
	$MainPointRankList .= "			<td><span class=\"point_rank_number\">".$ListCount."</span></td>";
	$MainPointRankList .= "			<td><small class=\"point_rank_academy\">".$CenterName."</small>".$MemberName."</td>";
	$MainPointRankList .= "			<td>";
	$MainPointRankList .= "				<span class=\"point_rank_score\">".number_format($MemberTotalPoint,0)."</span>";
	$MainPointRankList .= "				<div class=\"bar_back\">";
	$MainPointRankList .= "					<div class=\"bar_front\" style=\"width:".$PointChartLength."%;\"></div>";
	$MainPointRankList .= "				</div>";
	$MainPointRankList .= "			</td>";
	$MainPointRankList .= "		</tr>";

	$ListCount++;
}
$Stmt = null;


$MainPointRankList .= "	</table>";
$MainPointRankList .= "</div>";


//전체 포인트 HTML 시작 ==============================
$MainPointRankList .= "<div id=\"tab3\" class=\"tab point_rank_box\">";
$MainPointRankList .= "	<table class=\"point_rank_table\">";
$MainPointRankList .= "		<col width=\"12%\">";
$MainPointRankList .= "		<col width=\"30%\">";
$MainPointRankList .= "		<col width=\"\">";

$Sql = "
	select 
		A.MemberID,
		A.MemberTotalPoint,
		B.MemberLoginID,
		B.MemberName,
		C.CenterName
	from (".$ViewTable3.") A 
		inner join Members B on A.MemberID=B.MemberID 
		inner join Centers C on B.CenterID=C.CenterID 
	where B.MemberLevelID=19 
	order by A.MemberTotalPoint desc limit 0, 10
";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListCount = 1;
while($Row = $Stmt->fetch()) {
	$MemberID = $Row["MemberID"];
	$MemberLoginID = $Row["MemberLoginID"];
	$MemberName = $Row["MemberName"];
	$MemberTotalPoint = $Row["MemberTotalPoint"];
	$CenterName = $Row["CenterName"];

	if ($ListCount==1){
		$TopPoint = $MemberTotalPoint;
	}
	if ($TopPoint>0){
		$PointChartLength = round( (($MemberTotalPoint / $TopPoint) * 100) ,2);
	}else{
		$PointChartLength = 0;
	}

	$MainPointRankList .= "		<tr>";
	$MainPointRankList .= "			<td><span class=\"point_rank_number\">".$ListCount."</span></td>";
	$MainPointRankList .= "			<td><small class=\"point_rank_academy\">".$CenterName."</small>".$MemberName."</td>";
	$MainPointRankList .= "			<td>";
	$MainPointRankList .= "				<span class=\"point_rank_score\">".number_format($MemberTotalPoint,0)."</span>";
	$MainPointRankList .= "				<div class=\"bar_back\">";
	$MainPointRankList .= "					<div class=\"bar_front\" style=\"width:".$PointChartLength."%;\"></div>";
	$MainPointRankList .= "				</div>";
	$MainPointRankList .= "			</td>";
	$MainPointRankList .= "		</tr>";

	$ListCount++;
}
$Stmt = null;

$MainPointRankList .= "	</table>";
$MainPointRankList .= "</div>";




$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MainPointRankList"] = $MainPointRankList;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>