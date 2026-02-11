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

$BookScanID = isset($_REQUEST["BookScanID"]) ? $_REQUEST["BookScanID"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";

//================= 로그 ======================
$Sql2 = "insert into ClassBookScanViewLogs (
				ClassID,
				ClassBookType,
				ClassBookScanViewLogDateTime

	) values (
				:ClassID,
				0,
				now()
	)";

$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':ClassID', $ClassID);
$Stmt2->execute();
$Stmt2 = null;
//================= 로그 ======================




$Sql = "select 
			A.* 	
		from BookScans A
		where 
			A.BookScanID=:BookScanID 
			and A.BookScanView=1 
			and A.BookScanState=1 
		order by A.BookScanOrder asc";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookScanID', $BookScanID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

while($Row = $Stmt->fetch()) {

$BookScanName = $Row["BookScanName"];
$BookScanImageFileName = $Row["BookScanImageFileName"];

}
$Stmt = null;




$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["BookScanImageFileName"] = $BookScanImageFileName;





$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>