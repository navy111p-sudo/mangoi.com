<?
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/dbclose.php');


$ErrNum = 0;
$ErrMsg = "";

setcookie("LoginAdminID", "", 0, "/", ".".$DefaultDomain2);
setcookie("LoginMemberID", "", 0, "/", ".".$DefaultDomain2);

setcookie("LinkLoginAdminID", "", 0, "/", ".".$DefaultDomain2);
setcookie("LinkLoginMemberID", "", 0, "/", ".".$DefaultDomain2);


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;


$ResultValue = my_json_encode($ArrValue);
echo $ResultValue; 

function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>