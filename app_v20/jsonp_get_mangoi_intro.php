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
				
<!-- 경영진 -->
<section class=\"manager_wrap\">
    <div class=\"mangoi_wrap\">
        <h2 class=\"sub_title\">왜 <img src=\"".$ServerPath."images/logo_text_mangoi.png\" class=\"sub_title_logo\"> 화상영어인가?</h2></div>
	<div class=\"manager_area\">
		<div class=\"manager_box\">
			<h2 class=\"caption_sub\">경영진</h2>
			<div class=\"manager_text\">
				<b class=\"bold\">20년 이상의 어학원</b> 운영 경험 보유<br>
				<div>
					콘텐츠관리, 교사관리, 학생관리의
					<div class=\"break\">노하우 <b class=\"bold\">화상영어에 접목</b></div>
				</div>
				어학교육 사업에 대한 Insight 보유<br>
				미국학부 및 대학원 수학한 경영진
			</div>
			<img src=\"".$ServerPath."images/img_why.png\" alt=\"경영진\" class=\"img_manager\">
		</div>
	</div>
</section>

<!-- 화상 영어 효과 -->
<section class=\"effect_wrap\">
	<div class=\"effect_area\">
		<div class=\"effect_left\">
			<h3 class=\"effect_left_caption\">화상영어 효과 검증</h3>
			<table class=\"effect_table\">
				<tr>
					<th><span class=\"bullet\"></span>기 &nbsp; &nbsp; &nbsp; &nbsp; 간</th>
					<td class=\"colon\">:</td>
					<td>2009년 12월 ~ 현재까지</td>
				</tr>
				<tr>
					<th><span class=\"bullet\"></span>대 &nbsp; &nbsp; &nbsp; &nbsp; 상</th>
					<td class=\"colon\">:</td>
					<td><b class=\"bold\">전국 200여곳의 학원, 학교, 기관</b>에 화상영어 제공</td>
				</tr>
				<tr>
					<th><span class=\"bullet\"></span>적용 방법</th>
					<td class=\"colon\">:</td>
					<td>학원 수업과 연계한 Speaking 학습</td>
				</tr>
			</table>
		</div>
		<div class=\"effect_right\">
			<img src=\"".$ServerPath."images/img_effect.png\" alt=\"화상영어효과\" class=\"img_effect\">
		</div>
	</div>
</section>

<!-- 업계 최고 영역 -->
<section class=\"edge_wrap\">
	<div class=\"edge_area\">
		<h2 class=\"caption_sub\">업계 최고 품질과 가격 경쟁력</h2>
		<div class=\"caption_sub_text\">
			지난 10년간 다양한 기관, 학원, 개인에 화상 프로그램을 제공하여 품질 검증.
			<div class=\"break\">학생들의 학원료를 고려하여, 단체 할인가 적용</div>
		</div>
		<img src=\"".$ServerPath."images/img_best.png\" class=\"img_best\">
	</div>
</section>

<!-- IT INFRA 영역 -->
<section class=\"infra_wrap\">
	<div class=\"infra_area\">
		<h2 class=\"caption_sub\">IT INFRA</h2>
		<div class=\"caption_sub_text\">망고아이는 화상솔루션에 최적의 환경을 제공합니다.</div>
		<ul class=\"infra_list\">
			<li>
				<div class=\"infra_img_area\"><img src=\"".$ServerPath."images/icon_infra_1.png\" class=\"infra_img\" alt=\"화상솔루션\"></div>
				<span class=\"infra_line\"></span>
				<div class=\"infra_text\"><div class=\"break\">깨끗한 영상과</div>사운드 제공<br>화상솔루션</div>
			</li>
			<li>
				<div class=\"infra_img_area\"><img src=\"".$ServerPath."images/icon_infra_2.png\" class=\"infra_img\" alt=\"화상솔루션\"></div>
				<span class=\"infra_line\"></span>
				<div class=\"infra_text\"><div class=\"break\">Inteligent 빌딩에 </div>학습 센터 입주<br><span class=\"normal\">&nbsp;</span></div>
			</li>
			<li>
				<div class=\"infra_img_area\"><img src=\"".$ServerPath."images/icon_infra_3.png\" class=\"infra_img\" alt=\"화상솔루션\"></div>
				<span class=\"infra_line\"></span>
				<div class=\"infra_text\"><div class=\"break\">화상영어 망고아이</div>프로그램<br>IDC에 상주함</div>
			</li>
		</ul>
	</div>
</section>

<!-- 3단계 학습시스템 -->
<section class=\"step_wrap\" style=\"display:none;\">
	<div class=\"step_area swiper-container swiper2\">
		<h2 class=\"caption_sub_line\"><span class=\"normal\">3단계</span> 학습시스템</h2>
		<!-- Add Pagination -->
		<div class=\"swiper_page_wrap\">
			<div class=\"swiper-pagination\"></div>
			<ul class=\"swiper_line\">
				<li class=\"line_one\">레벨테스트</li>
				<li class=\"line_two\">화상강의</li>
				<li class=\"line_three\">월평가서</li>
			</ul>
		</div>
		<!-- Add Arrows -->
		<div class=\"swiper_wrap\">
			<div class=\"swiper-button-next\"></div>
			<div class=\"swiper-button-prev\"></div>
		</div>
		<div class=\"swiper-wrapper\">
			<!-- STEP 01. -->
			<section class=\"step1_wrap swiper-slide\">
				<div class=\"step1_area\">
					<div class=\"step1_left\">
						<h3 class=\"step1_left_caption\"><span class=\"normal\">STEP01.</span> 레벨테스트</h3>
						<ul class=\"step1_list\">
							<li>Speaking / Listening / Vocaburary Test</li>
							<li>Level Report 제공</li>
							<li>학습방향 설정</li>
							<li>Sample Level Test Report</li>
						</ul>
					</div>
					<div class=\"step1_right\">
						<img src=\"".$ServerPath."images/img_step_1.png\" alt=\"레벨테스트\" class=\"img_step1\">
					</div>
				</div>
			</section>

			<!-- STEP 02. -->
			<section class=\"step2_wrap swiper-slide\">
				<div class=\"step2_area\">
					<div class=\"step2_left\">
						<h3 class=\"step2_left_caption\"><span class=\"normal\">STEP02.</span> 화상강의</h3>
						<ul class=\"step2_list\">
							<li>Speaking / Listening 중심수업</li>
							<li>망고아이전용 화상 교재로 수업</li>
							<li>수업녹화후 반복 청취</li>
							<li>실시간 첨삭 가능</li>
						</ul>
					</div>
				</div>
			</section>

			<!-- STEP 03. -->
			<section class=\"step3_wrap swiper-slide\">
				<div class=\"step3_area\">
					<div class=\"step3_left\">
						<h3 class=\"step3_left_caption\"><span class=\"normal\">STEP03.</span> 월 평가서</h3>
						<ul class=\"step3_list\">
							<li>월단위 객관식 평가제공</li>
							<li>홈페이지 월 평가서 확인가능</li>
							<li>Study Manager와 월 평가서 상담</li>
						</ul>
					</div>
					<div class=\"step3_right\">
						<img src=\"".$ServerPath."images/img_step_3.png\" alt=\"레벨테스트\" class=\"img_step3\">
					</div>
				</div>
			</section>

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