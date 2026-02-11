<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$YearNumber = isset($_REQUEST["YearNumber"]) ? $_REQUEST["YearNumber"] : "";
$MonthNumber = isset($_REQUEST["MonthNumber"]) ? $_REQUEST["MonthNumber"] : "";

$SetDate = $YearNumber . "-" . substr("0".$MonthNumber, -2). "-01";

$LastDay = date("t", strtotime($SetDate));





$ArrValue["LastDay"] = $LastDay;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>