<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');


$FavoriteMenuID = isset($_REQUEST["FavoriteMenuID"]) ? $_REQUEST["FavoriteMenuID"] : "";
$MemberID = $_LINK_ADMIN_ID_;


$Sql = "update Favorites set FavoriteState=0 where FavoriteMenuID=:FavoriteMenuID and MemberID=:MemberID";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':FavoriteMenuID', $FavoriteMenuID);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();


$ArrValue["Result"] = 1;
$QueryResult = my_json_encode($ArrValue);
echo $QueryResult;



function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');

?>