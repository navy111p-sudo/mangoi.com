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


$Sql = "select 
			A.*,
			ifnull(B.CenterUseMyRank,0) as CenterUseMyRank, 
			date_format(A.MemberRegDateTime,'%Y년 %m월 %d일') as MemberRegDate,
			ifnull((select sum(AA.MemberPoint) from MemberPoints AA where AA.MemberID=A.MemberID and AA.MemberPointState=1),0) as TotalMemberPoint
		from Members A 
			left outer join Centers B on A.CenterID=B.CenterID 
		where A.MemberID=:MemberID";



$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberName = $Row["MemberName"]; 
$MemberLoginID = $Row["MemberLoginID"]; 
$CenterUseMyRank = $Row["CenterUseMyRank"];  
$MemberRegDate = $Row["MemberRegDate"]; 
$MemberPhoto = $Row["MemberPhoto"];
$TotalMemberPoint = number_format($Row["TotalMemberPoint"],0);



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

$Sql = "select ifnull(V.MemberTotalPoint,0) as MyPoint1 from ($ViewTable1) V where V.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MyPoint1 = $Row["MyPoint1"];


$Sql = "select count(*) as MyRank1 from ($ViewTable1) V where V.MemberTotalPoint>:MyPoint1";
$Stmt = $DbConn->prepare($Sql);
//$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->bindParam(':MyPoint1', $MyPoint1);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MyRank1 = $Row["MyRank1"]+1;
$MyRank1 = number_format($MyRank1,0);




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

$Sql = "select ifnull(V.MemberTotalPoint,0) as MyPoint2 from ($ViewTable2) V where V.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MyPoint2 = $Row["MyPoint2"];

$Sql = "select count(*) as MyRank2 from ($ViewTable2) V where V.MemberTotalPoint>:MyPoint2";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MyPoint2', $MyPoint2);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MyRank2 = $Row["MyRank2"]+1;
$MyRank2 = number_format($MyRank2,0);



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

$Sql = "select ifnull(V.MemberTotalPoint,0) as MyPoint3 from ($ViewTable3) V where V.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MyPoint3 = $Row["MyPoint3"];

$Sql = "select count(*) as MyRank3 from ($ViewTable3) V where V.MemberTotalPoint>:MyPoint3";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MyPoint3', $MyPoint3);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MyRank3 = $Row["MyRank3"]+1;
$MyRank3 = number_format($MyRank3,0);



$MainMyPointRank = "";

$MainMyPointRank .= "<ul class=\"main_my_point_list\">";
$MainMyPointRank .= "	<li>";
$MainMyPointRank .= "		<div class=\"main_my_point_caption\">나의 포인트</div>";
$MainMyPointRank .= "		<b class=\"main_my_point_num color_sea\">".$TotalMemberPoint." P</b>";
$MainMyPointRank .= "	</li>";
$MainMyPointRank .= "	<li>";
$MainMyPointRank .= "		<div class=\"main_my_point_caption\">주간 순위</div>";
$MainMyPointRank .= "		<b class=\"main_my_point_num color_pink\">".$MyRank1."위</b>";
$MainMyPointRank .= "	</li>";
$MainMyPointRank .= "	<li>";
$MainMyPointRank .= "		<div class=\"main_my_point_caption\">월간 순위</div>";
$MainMyPointRank .= "		<b class=\"main_my_point_num color_pink\">".$MyRank2."위</b>";
$MainMyPointRank .= "	</li>";
$MainMyPointRank .= "	<li>";
$MainMyPointRank .= "		<div class=\"main_my_point_caption\">전체 순위</div>";
$MainMyPointRank .= "		<b class=\"main_my_point_num color_pink\">".$MyRank3."위</b>";
$MainMyPointRank .= "	</li>";
$MainMyPointRank .= "</ul>";


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["CenterUseMyRank"] = $CenterUseMyRank;
$ArrValue["MainMyPointRank"] = $MainMyPointRank;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>