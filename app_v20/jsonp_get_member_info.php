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
			date_format(A.MemberRegDateTime,'%Y년 %m월 %d일') as MemberRegDate,
			ifnull((select sum(AA.MemberPoint) from MemberPoints AA where AA.MemberID=A.MemberID and AA.MemberPointState=1),0) as TotalMemberPoint
		from Members A 
		where A.MemberID=:MemberID and A.MemberState=1";



$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberName = $Row["MemberName"]; 
$MemberLoginID = $Row["MemberLoginID"]; 
$MemberRegDate = $Row["MemberRegDate"]; 
$MemberPhoto = $Row["MemberPhoto"];
$TotalMemberPoint = number_format($Row["TotalMemberPoint"],0);


$Sql = "select A.MemberPointID from MemberPoints A where A.MemberPointTypeID=2 and A.MemberID=:MemberID and A.MemberPointState=1 and datediff(A.MemberPointRegDateTime, now())=0";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberPointID = $Row["MemberPointID"];
 
if (!$MemberPointID){
	InsertPoint(2, 0, $LocalLinkMemberID, "앱접속(앱)", "앱접속(앱)" ,$OnlineSiteMemberLoginPoint);
}



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MemberName"] = $MemberName;
$ArrValue["MemberLoginID"] = $MemberLoginID;
$ArrValue["MemberRegDate"] = $MemberRegDate;
$ArrValue["MemberPhoto"] = $MemberPhoto;
$ArrValue["TotalMemberPoint"] = $TotalMemberPoint;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>