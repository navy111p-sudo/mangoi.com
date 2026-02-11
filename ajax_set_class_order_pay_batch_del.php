 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$ClassOrderPayBatchID = isset($_REQUEST["ClassOrderPayBatchID"]) ? $_REQUEST["ClassOrderPayBatchID"] : "";
$MemberID = $_LINK_MEMBER_ID_;


//=========================================================================================
$Sql = " update ClassOrderPayBatchs set ";
$Sql .= "	ClassOrderPayBatchState=0, ";
$Sql .= "	ClassOrderPayBatchModiDateTime=now() ";
$Sql .= " where ClassOrderPayBatchID=:ClassOrderPayBatchID ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderPayBatchID', $ClassOrderPayBatchID);
$Stmt->execute();
$Stmt = null;
//=========================================================================================



$ArrValue["ClassOrderPayBatchID"] = $ClassOrderPayBatchID;//주문아이디

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>