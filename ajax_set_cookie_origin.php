<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$DeviceID = isset($_REQUEST["DeviceID"]) ? $_REQUEST["DeviceID"] : "";
$Action = isset($_REQUEST["Action"]) ? $_REQUEST["Action"] : "";

$CookieName = "D_".$DeviceID;
// M_2_D_1

if($Action=="add") {


	$Result = setcookie($CookieName, $MemberID, time()+60*60*24*365, '/', ".mangoidev.hihome.kr");

	// 쿠키 생성이 성공적이라면 ?
	if($Result) {
		$ArrValue['cookie'] = $CookieName;
	} else {
		$ArrValue['cookie'] = null;
	}

	$QueryResult = my_json_encode($ArrValue);
	echo $QueryResult; 
} else if($Action=="del") {
	unset($_COOKIE[$CookieName]);
	//$Result = setcookie($CookieName, $MemberID, time()-3600, '/', ".mangoidev.hihome.kr");
	$Result = setcookie($CookieName, $MemberID, time()+60*60*24*365, '/', ".mangoidev.hihome.kr");

	// 쿠키 삭제가 성공적이라면 ?
	if($Result) {
		$ArrValue['cookie'] = "deleted";
	} else {
		$ArrValue['cookie'] = null;
	}
	$QueryResult = my_json_encode($ArrValue);
	echo $QueryResult; 
}


function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>