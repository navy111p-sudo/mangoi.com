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

<section class=\"app_class_wrap\">
	<div class=\"app_class_area\">
		<h3 class=\"caption_center\">수강신청안내</h3>
		<div class=\"app_class_price_table_wrap\">
			<table class=\"app_class_price_table left\">
				<col width=\"21%\">
				<col width=\"20%\">
				<col width=\"20%\">
				<col width=\"20%\">
				<col width=\"19%\">
				<tr>
					<th rowspan=\"2\">수강료(월)<br>20분 수업</th>
					<th colspan=\"3\">필리핀 교사</th>
					<th rowspan=\"2\" class=\"no_border none\">단체가격</th>
				</tr>
				<tr>
					<th>1:1 수업</th>
					<th>1:2 수업<div class=\"break\">(1인당)</div></th>
					<th>그룹수업</th>
				</tr>
				<tr>
					<td>주1회<div class=\"break_2\">(월4회)</div></td>
					<td><b>60,000원</b></td>
					<td><b>40,000원</b></td>
					<td><b>80,000원</b></td>
					<td rowspan=\"4\" class=\"no_border none\">별도 문의</td>
				</tr>
				<tr>
					<td>주2회<div class=\"break_2\">(월8회)</div></td>
					<td><b>120,000원</b></td>
					<td><b>80,000원</b></td>
					<td><b>160,000원</b></td>
				</tr>
				<tr>
					<td>주3회<div class=\"break_2\">(월12회)</div></td>
					<td><b>180,000원</b></td>
					<td><b>120,000원</b></td>
					<td><b>240,000원</b></td>
				</tr>
				<tr>
					<td>주5회<div class=\"break_2\">(월20회)</div></td>
					<td><b>300,000원</b></td>
					<td><b></b></td>
					<td><b></b></td>
				</tr>
			</table>
			<table class=\"app_class_price_table right\">
				<col width=\"21%\">
				<col width=\"20%\">
				<col width=\"20%\">
				<col width=\"20%\">
				<col width=\"19%\">
				<tr>
					<th rowspan=\"2\" class=\"none\">수강료(월)<br>20분 수업</th>
					<th colspan=\"3\">미국, 캐나다 교사</th>
					<th rowspan=\"2\" class=\"no_border\">단체가격</th>
				</tr>
				<tr>
					<th>1:1 수업</th>
					<th>1:2 수업<div class=\"break\">(1인당)</div></th>
					<th>그룹수업</th>
				</tr>
				<tr>
					<td class=\"none\">주1회<div class=\"break_2\">(월4회)</div></td>
					<td><b>120,000원</b></td>
					<td><b>80,000원</b></td>
					<td><b>160,000원</b></td>
					<td rowspan=\"4\" class=\"no_border\">별도 문의</td>
				</tr>
				<tr>
					<td class=\"none\">주2회<div class=\"break_2\">(월8회)</div></td>
					<td><b>240,000원</b></td>
					<td><b>160,000원</b></td>
					<td><b>320,000원</b></td>
				</tr>
				<tr>
					<td class=\"none\">주3회<div class=\"break_2\">(월12회)</div></td>
					<td><b>360,000원</b></td>
					<td><b>240,000원</b></td>
					<td><b>480,000원</b></td>
				</tr>
				<tr>
					<td class=\"none\">주5회<div class=\"break_2\">(월20회)</div></td>
					<td><b>600,000원</b></td>
					<td><b></b></td>
					<td><b></b></td>
				</tr>
			</table>
		</div>
		<ul class=\"app_class_bank\">
			<li><img src=\"".$ServerPath."images/bullet_yellow.png\"><b>결제방법 :</b> 신용카드 / 가상계좌 / 월 자동이체</li>
			<!--li><img src=\"".$ServerPath."images/bullet_yellow.png\"><b>계좌번호 :</b> 신한은행 100-027-577892 (예금주 : 에듀비전)</li-->
		</ul>

		<div class=\"app_class_noticeable\">
			<h4>유의사항</h4>
			<ul>
				<li>1개월은 4주로 간주되며, 주 5회 1개월 수강은 총 20회, 주 3회 1개월 수강은 총 12회, 주 2회 1개월 수강은 총 8회로 진행됩니다.</li>
				<li>모든 종류의 할인은 기본수강료를 기준으로 적용되며, 중복할인이 되지 않습니다.</li>
				<li>
					추가 수강료란 핸드폰 수강자를 위한 추가 통신료, 과정별로 제공되는 예복습 프로그램이나 이북컨텐츠 등의 제공을 위한
					과정별 추가비용을 말하며, 순수한 비용이므로 할인율이 적용되지 않습니다.
				</li>
				<li>화상수업으로 수강하기 위해서는 PC 데스크탑 컴퓨터의 경우 화상캠과 해드셋이 필요합니다.</li>
				<li>이벤트 상품의 경우, 특별할인(장기간 수강 할인 등)이 적용되지 않습니다.</li>
			</ul>
		</div>

		<h3 class=\"caption_center\">환불규정</h3>
		<!--ul class=\"app_class_refund\">
			<li><b>1.</b>연간 회원권은 구매일 기준 7일내 환불 가능합니다. 단, 유무료 로드맵을 1개 이상 이용 시 환불이 불가합니다.</li>
			<li><b>2.</b>연간 회원권 연장의 경우 기존 회원권 종료일로 부터 7일이 경과한 경우 환불이 불가합니다.</li>
			<li>
				<b>3.</b>프리미엄 이용권은 구매일 기준 7일내 환불 가능합니다. 단, 1개 이상 사용시(신청기준) 부분 환불은 불가합니다.<br>
				(예를 들어 프리미엄3 구매하여 프리미엄 1회 신청 후 나머지 2회만 환불 불가)
			</li>
		</ul-->
		<table class=\"app_class_refund_table\">
			<col width=\"\">
			<col width=\"31%\">
			<col width=\"32%\">
			<tr>
				<th>환불 요구 시기</th>
				<th>환불 금액(%)</th>
				<th>예) 수강료 100,000원 지불</th>
			</tr>
			<tr>
				<td>수업 시작 전</td>
				<td>납부금액의 100%</td>
				<td>100,000원 환불</td>
			</tr>
			<tr>
				<td>총 수업시간의 1/3 이전</td>
				<td>납부금액의 70%</td>
				<td>70,000원 환불</td>
			</tr>
			<tr>
				<td>총 수업시간의 1/2 이전</td>
				<td>납부금액의 50%</td>
				<td>50,000원 환불</td>
			</tr>
			<tr>
				<td>총 수업시간의 1/2 이후</td>
				<td>납부금액의 0%</td>
				<td>환불 안 됨</td>
			</tr>
		</table>

        <div class=\"app_class_btns\">
            <div class=\"app_class_btns_left\">
                <h4><b>신규수강생</b> 신청하기</h4>
                <a href=\"#\" class=\"btn_gradient_pink item-link item-content\" onclick=\"OpenClassApplyTeacherList();\">수강신청하기</a>
            </div>    
            <div class=\"app_class_btns_right\">
                <h4><b>기존수강생</b> 연장하기</h4>
                <a href=\"#\" class=\"btn_gradient_pink item-link item-content open-popup\" data-popup=\".popup-payment-list\"  onclick=\"GetMainPaymentList();\">수강연장하기</a>
            </div>
        </div>
	</div>
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