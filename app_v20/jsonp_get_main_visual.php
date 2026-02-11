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


$MainVisualHTML = "

	<div class=\"swiper-slide first\" style=\"background-image:url('".$ServerPath."images/main_visual_1.jpg');\">
		<div class=\"main_visual_text_1\">
			<div class=\"main_text_1\">망고아이 리뉴얼</div>
			<div class=\"main_text_2\">OPEN</div>
			<div class=\"main_text_3\">망고아이 오픈기념<br>회원가입 시<br>1개월 체험권 지급</div>
		</div>
	</div>
	<div class=\"swiper-slide second\" style=\"background-image:url('".$ServerPath."images/main_visual_2.jpg');\">
		<div class=\"main_visual_text_2\">
			<div class=\"main_text_1\">즐거운 화상영어</div>
			<div class=\"main_text_2\">망고아이</div>
			<div class=\"main_text_3\">망고아이는 비교할 수 없는<br>특별함을 선물합니다. </div>
		</div>
	</div>
	<div class=\"swiper-slide third\" style=\"background-image:url('".$ServerPath."images/main_visual_3.jpg');\">
		<div class=\"main_visual_text_3\">
			<div class=\"main_text_1\">특별한 화상영어</div>
			<div class=\"main_text_2\">망고아이</div>
			<div class=\"main_text_3\">자연스러운 반복 학습의 효과</div>
		</div>
	</div>

";



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MainVisualHTML"] = $MainVisualHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>