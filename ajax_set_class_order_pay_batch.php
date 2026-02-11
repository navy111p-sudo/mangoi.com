 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$ClassOrderMode = isset($_REQUEST["ClassOrderMode"]) ? $_REQUEST["ClassOrderMode"] : "";
$MemberID = $_LINK_MEMBER_ID_;

if ($ClassOrderMode=="LMS"){
	$ClassOrderPayBatchNumber = "BCL".date("YmdHis").substr("0000000000".$MemberID,-10); // ML -> Mangoi Lms
}else{
	$ClassOrderPayBatchNumber = "BCH".date("YmdHis").substr("0000000000".$MemberID,-10); // ML -> Mangoi Home
}

$good_name = "망고아이 정기결제";
$buyr_name = $MemberID;
//=========================================================================================
$Sql = " insert into ClassOrderPayBatchs ( ";
	$Sql .= " ClassOrderID, ";
	$Sql .= " ClassOrderPayBatchNumber, ";
	$Sql .= " good_name, ";
	$Sql .= " buyr_name, ";
	$Sql .= " ClassOrderPayBatchMonth, ";
	$Sql .= " ClassOrderPayBatchState, ";
	$Sql .= " ClassOrderPayBatchRegDateTime, ";
	$Sql .= " ClassOrderPayBatchModiDateTime ";
$Sql .= " ) values ( ";
	$Sql .= " :ClassOrderID, "; 
	$Sql .= " :ClassOrderPayBatchNumber, ";
	$Sql .= " :good_name, ";
	$Sql .= " :buyr_name, ";
	$Sql .= " 1, ";
	$Sql .= " 0, ";
	$Sql .= " now(), ";
	$Sql .= " now() ";
$Sql .= " ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
$Stmt->bindParam(':ClassOrderPayBatchNumber', $ClassOrderPayBatchNumber);
$Stmt->bindParam(':good_name', $good_name);
$Stmt->bindParam(':buyr_name', $buyr_name);
$Stmt->execute();
$ClassOrderPayBatchID = $DbConn->lastInsertId();
$Stmt = null;
//=========================================================================================



$ArrValue["ClassOrderPayBatchID"] = $ClassOrderPayBatchID;//주문아이디
$ArrValue["ClassOrderPayBatchNumber"] = $ClassOrderPayBatchNumber;//주문번호

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>