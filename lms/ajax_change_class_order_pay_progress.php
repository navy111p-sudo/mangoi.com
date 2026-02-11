 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ClassOrderPayID = isset($_REQUEST["ClassOrderPayID"]) ? $_REQUEST["ClassOrderPayID"] : "";
$ClassOrderPayProgress = isset($_REQUEST["ClassOrderPayProgress"]) ? $_REQUEST["ClassOrderPayProgress"] : "";



$Sql =  "";
$Sql .= " update ";
$Sql .= "	ClassOrderPays ";
$Sql .= " set ";
$Sql .= "	ClassOrderPayProgress=:ClassOrderPayProgress, ";
if ($ClassOrderPayProgress==33){
	$Sql .= "	ClassOrderPayCencelDateTime=now(), ";
}
$Sql .= "	ClassOrderPayModiDateTime=now() ";
$Sql .= " where ClassOrderPayID=:ClassOrderPayID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderPayProgress', $ClassOrderPayProgress);
$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
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