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

$GetHTML = "

<section class=\"class_register_area\">
	<ul class=\"class_register_tabs\">
		<li><a href=\"class_register_teacher.html\" class=\"item-link item-content\"><span class=\"bar\"></span>강사 먼저 선택</a></li>
		<li><a href=\"#\" class=\"active\"><span class=\"bar\"></span>날짜 먼저 선택</a></li>
	</ul>

	<h3 class=\"caption_left_common\"><b>수업 예약</b> 신청서</h3>

	<table class=\"class_register_table\">
		<tr>
			<th>신규/연장 선택</th>
			<td>
				<div class=\"radio_wrap register\">
					<input type=\"radio\" id=\"register_1\" class=\"input_radio\" checked name=\"register\"><label class=\"label\" for=\"register_1\"><span class=\"bullet_radio\"></span>신규</label>
					<input type=\"radio\" id=\"register_2\" class=\"input_radio\" name=\"register\"><label class=\"label\" for=\"register_2\"><span class=\"bullet_radio\"></span>연장 (기존 수강생일 경우 선택) </label>
				</div>
				<div class=\"class_register_text\">
					※ 수업 연장일 경우에는 다음날 부터 신청일 지정이 가능합니다.<br>
					※ 수업연장은 수업종료 3영업일 전까지 신청해주셔야 기존수업을 보장받을 수 있습니다.
				</div>
			</td>
		</tr>
		<tr>
			<th>수강 시작 희망일</th>
			<td>

				<div class=\"class_register_text\">
					※ 수업 시작일은 이틀 후로 신청가능하며 신청일 보다 더 늦춰질 수 있습니다.
				</div>
			</td>
		</tr>
		<tr>
			<th>수강 희망 시간대</th>
			<td>
				<select class=\"class_select\">
					<option>1차 희망시간대</option>
					<option>18:00~18:50</option>
				</select>
				<select class=\"class_select\">
					<option>2차 희망시간대</option>
					<option>18:00~18:50</option>
				</select>
				<div class=\"class_register_text_box\">
					※ 선택하신 희망 시간대에 부가 설명이 필요할 경우 요청사항을 남겨 주세요.<br>
					예1) 6시부터 6시 반까지와 저녁 9시30분 부터 10시 30분까지만 가능할 것 같아요.<br>
					예2) 저녁 7시 이후부터는 아무시간이나 상관 없습니다.<br>
					예3) 오후6시에서 6시 반 또는 저녁 10시 이후로 스케줄을 잡아주세요.
				</div>
			</td>
		</tr>
		<tr>
			<th>수업 관련 전달 메시지<br>(200자 이내)</th>
			<td>
				<textarea class=\"class_textarea\" placeholder=\"※ 기타 수업관련 또는 문의하실 메시지 내용을 입력하고자 하실 경우 문의사항을 남겨 주세요.\"></textarea>
			</td>
		</tr>
	</table>

	<a href=\"#\" class=\"btn_orange_white open-popup\" data-popup=\".popup-class-payment\">수강신청</a>

</section>


";


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["GetHTML"] = $GetHTML;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>