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

$LocalLinkMemberID = isset($_REQUEST["LocalLinkMemberID"]) ? $_REQUEST["LocalLinkMemberID"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$ClassVideoType = isset($_REQUEST["ClassVideoType"]) ? $_REQUEST["ClassVideoType"] : "";


$PointMemberID = $LocalLinkMemberID;

$Validate = $ClassID."|".$ClassVideoType;
InsertNewTypePoint(4, 0, $PointMemberID, $Validate);

//================= 포인트 ======================
$Sql = "select A.MemberPointID from MemberPoints A where A.MemberPointTypeID=4 and A.MemberID=:MemberID and A.MemberPointState=1 and A.RootOrderID=:RootOrderID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $PointMemberID);
$Stmt->bindParam(':RootOrderID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberPointID = $Row["MemberPointID"];

/*
if (!$MemberPointID){
	InsertPointWithRootOrderID(4, 0, $PointMemberID, "레슨비디오시청(앱)", "레슨비디오시청(앱)" ,$OnlineSitePreStudyPoint, $ClassID);
}
*/
//================= 포인트 ======================


//================= 로그 ======================
$Sql2 = "insert into ClassVideoPlayLogs (
				ClassID,
				ClassVideoType,
				ClassVideoPlayLogDateTime

	) values (
				:ClassID,
				:ClassVideoType,
				now()
	)";

$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':ClassID', $ClassID);
$Stmt2->bindParam(':ClassVideoType', $ClassVideoType);
$Stmt2->execute();
$Stmt2 = null;
//================= 로그 ======================





$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;





$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>