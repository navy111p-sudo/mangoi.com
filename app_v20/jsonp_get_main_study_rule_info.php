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
$LocalLinkMemberID = isset($_REQUEST["LocalLinkMemberID"]) ? $_REQUEST["LocalLinkMemberID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";



$MainStudyRuleInfo = "
	<section class=\"app_class_wrap pop\" style=\"margin-top:-50px;\">
		<div class=\"app_class_area guide\">
			<h3 class=\"caption_left_common center\">수강<b>안내</b></h3>
			<div class=\"class_guide_box\">
				<ul class=\"class_guide_list\">
					<li class=\"table\">
						<table class=\"class_guide_list_table one\">
							<th>수업 준비물 :</th>
							<td>테블릿 PC , 노트북 또는 데스크 탑 컴퓨터(웹캠, 헤드셋 포함)</td>
						</table>
					</li>
					<li class=\"table\">
						<table class=\"class_guide_list_table two\">
							<th>교재 :</th>
							<td>1. 망고아이 교재 / 2. 학당(어학원) 전용교재</td>
						</table>
					</li>
					<li class=\"table\">
						<table class=\"class_guide_list_table three\">
							<th>수업 스케쥴 :</th>
							<td>오전반(07시 ~ 14시, 월~금요일) / 오후반(15시 ~ 23시, 월 ~ 금요일)</td>
						</table>
					</li>
					<li class=\"table\">
						<table class=\"class_guide_list_table four\">
							<th>주 2회~5회 가능 :</th>
							<td>월 수업 수 계산으로 정산(예 : 주2회는 월 8회 수업)</td>
						</table>
					</li>
					<li>1회 수업은 20분, 2번 연속하여 40분 수업도 가능</li>
				</ul>
			</div>

			<ul class=\"class_guide_list one\">
				<li>
					고정예약 수업연기는 제한이 있습니다.<br>
					(주1회 ~ 주2회 수업 1회, 주3회 ~5회 수업 2회)<br>
					ex) 주5회 수업 일정이 3/19 ~ 4/13 한달 20회가 등록되시면 그 기간 동안 연기는 2번만 가능합니다.
				</li>
			</ul>

			<ul class=\"class_guide_list two\">
				<li>수업연기요청은 수업시작 30분전까지 교사의 공강 여부에 따라 가능합니다.</li>
				<li>수강기간은 일수가 아닌 횟수로 정해집니다. <b>(주5회 수업 - 20회)</b></li>
				<li>한국 공휴일은 공식적으로 휴강이고 <b>해당국가의 사정에 따라 휴강</b> 될수 있으며 수업 일수에 포함되지 않습니다.</li>
				<li>
					재수강 등록은 수업 종료일 3일전까지이며 재수강 등록 시 기존 강사와의 수업시간은 그대로 유지됩니다.<br>
					<b>수업 종료일 이후 등록시에는 타수강자의 등록으로 인하여 강사와 시간이 변경될 수 있습니다.</b>
				</li>
			</ul>

			<ul class=\"class_guide_list three\">
				<li>월별 평가서는 총 10회 이상의 수강 진행 후 제공되며 주당 수업횟수에 따라 다를 수 있습니다.</li>
				<li>월 10회 이하 수강시 학생의 평가가 어려운점 양해 부탁드립니다.</li>
			</ul>

			<ul class=\"class_guide_list\">
				<li>환불규정</li>
			</ul>

			<table class=\"app_class_refund_table orange\">
				<col width=\"33.3%\">
				<col width=\"33.3%\">
				<col width=\"\">
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

			<h3 class=\"caption_left_common center two\">수강<b>규정</b></h3>
			<div class=\"class_guide_box center\">
				<h3>맞춤식 고정 수업 안내</h3>
				강사 한분과 매일 또는 주3회, 주2회 정해진 시간에 규칙적으로 꾸준히 수업하려는 목표를 가진분을 위한 시스템입니다.
			</div>

			<h4 class=\"class_rule_caption orange\">고정 수업 가이드</h4>
			<ul class=\"class_rule_list\">
				<li><span>01.</span> 수강료 결제를 완료하시면 24시간 내에 학습매니저가 회원님에게 전화를 드려 강사와 시간 등에 대한 스케쥴을 잡아 수강등록을 해드립니다.</li>
				<li><span>02.</span> 수업스테쥴이 잡히면 바로 수강하실 수 있으며, 교재 구입전이라도 온라인상의 샘플교재를 이용하여 수강하실 수 있습니다.</li>
				<li><span>03.</span> 주3회 수업은 월, 수, 금요일에 주2회 수업은 화, 목요일에 이루어 집니다.</li>
				<li><span>04.</span> 수강기간은 횟수로 정해집니다. (1개월 수업의 경우 주5회는 20회 수업, 주3회 수업은 12회 수업, 주2회 수업은 8회 수업)</li>
			</ul>

			<h4 class=\"class_rule_caption orange\">일일 수업 연기</h4>
			<ul class=\"class_rule_list\">
				<li><span>01.</span> 상황이 불가피하여 수업을 받을 수 없는 경우에 수업을 연기할 수 있습니다. </li>
				<li><span>02.</span> 수업연기 횟수는 진도와 향상을 위해서 전체 월 수업 횟수의 ½만 가능합니다.</li>
				<li><span>03.</span> 수업연기는 마이 페이지(My Page) > 공부방 입장 > 수업 연기요청으로 들어가시면 됩니다.
	당일 수업의 연기는 교사의 수업상황에 따라서 수업 시작 30분 전까지 가능합니다.</li>
			</ul>

			<h4 class=\"class_rule_caption orange\">수업 시간 변경</h4>
			<ul class=\"class_rule_list\">
				<li><span>01.</span> 수업시간을 변경하고자 하는 경우에는 어플에서 연기신청을 눌러서 교사 또는 수업 시간 둘 중 선택 가능합니다.</li>
				<li><span>02.</span> 수업시간을 변경하시면 담당강사가 변결될 수 있으며, 변경신청 시 유념하시기 바랍니다.</li>
				<li><span>03.</span> 수업시간 변경은 전체 월 수업시간의 ½회 가능합니다.</li>
			</ul>

			<h4 class=\"class_rule_caption orange\">담당 강사 변경</h4>
			<ul class=\"class_rule_list\">
				<li><span>01.</span> 담당강사를 바꾸자 하는 경우에는 [학습매니저 1:1게시판]을 이용하여 변경신청이 가능합니다.</li>
				<li><span>02.</span> 담당강사 변경은 당일 수업의 1일 전까지 가능합니다.</li>
				<li><span>03.</span> 담당강사 변경은 월1회 가능합니다. (단, 강사의 불성실로 인한 교체는 제한 횟수에 불포함)</li>
			</ul>

			<h4 class=\"class_rule_caption orange\">결석 처리 안내</h4>
			<ul class=\"class_rule_list\">
				<li><span>01.</span> 전화영어, 휴대전화영어 수업은 총3회 전화하여 응답이 없을 경우 결석처리 됩니다.</li>
				<li><span>02.</span> 화상영어 수업은 강사가 10분간 대기하여도 강의실에 입장하지 않으면 결석처리 됩니다.</li>
				<li><span>03.</span> 수강생의 과실로 인한 결석은 별도의 보강이 제공되지 않습니다.</li>
				<li><span>04.</span> 수강생의 사정으로 인해 수업시작이 지연된 경우에는 수업시간을 채우지 못했다 할지라도 예정된 종료시간에 수업이 종료됩니다.<br>
	   (뒤이어 예약된 학생의 수업에 지장을 끼칠 우려가 있으므로 불가피함)</li>
			</ul>

		</div>
	</section>
";



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MainStudyRuleInfo"] = $MainStudyRuleInfo;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : ''); 


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>