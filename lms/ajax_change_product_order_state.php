 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ProductOrderID = isset($_REQUEST["ProductOrderID"]) ? $_REQUEST["ProductOrderID"] : "";
$ProductOrderState = isset($_REQUEST["ProductOrderState"]) ? $_REQUEST["ProductOrderState"] : "";



$Sql =  "";
$Sql .= " update ";
$Sql .= "	ProductOrders ";
$Sql .= " set ";
$Sql .= "	ProductOrderState=:ProductOrderState, ";
if ($ProductOrderState==33){
	$Sql .= "	CancelDateTime=now(), ";
}
$Sql .= "	ProductOrderModiDateTime=now() ";
$Sql .= " where ProductOrderID=:ProductOrderID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductOrderState', $ProductOrderState);
$Stmt->bindParam(':ProductOrderID', $ProductOrderID);
$Stmt->execute();


$ArrValue["ResultValue"] = 1;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>