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


$ArrMainTodayMsg[1] = "우리의 지식은 세상을 변화시킬 수 있다<span class=\"main_famous_name\">- 스티븐 호킹 -</span>";
$ArrMainTodayMsg[2] = "신념은 단순이 갖고 있는 것만이 아닌 실천하는 무엇이다<span class=\"main_famous_name\">- 버락 오바마 -</span>";
$ArrMainTodayMsg[3] = "교육은 노후를 위한 최상의 양식이다<span class=\"main_famous_name\">- 아리스토텔레스 -</span>";
$ArrMainTodayMsg[4] = "끊임없이 꿈을 향해 나아가라<span class=\"main_famous_name\">- 오프라 윈프리 -</span>";
$ArrMainTodayMsg[5] = "항상 갈구하라, 바보짓을 두려워 말라<span class=\"main_famous_name\">- 스티브 잡스 -</span>";
$ArrMainTodayMsg[6] = "꿈을 이뤄내기 위해 두려움을 이겨내는 것, 그것이 진정한 용기다.<span class=\"main_famous_name\">- 김연아 -</span>";

$ArrNum = mt_rand(1, 6);

$MainTodayMsgHTML = $ArrMainTodayMsg[$ArrNum];


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MainTodayMsgHTML"] = $MainTodayMsgHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>