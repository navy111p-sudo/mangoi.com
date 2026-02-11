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

<section class=\"course_area\">
	<ul class=\"edu_tabs\">
		<li><a href=\"course_level.html\" class=\"item-link item-content TrnTag\"><span class=\"bar\"></span>레벨구성</a></li>
		<li><a href=\"course_student.html\" class=\"item-link item-content TrnTag\"><span class=\"bar\"></span>학생과정</a></li>
		<li><a href=\"#\" class=\"active TrnTag\"><span class=\"bar\"></span>성인과정</a></li>
	</ul>

	<ul class=\"edu_adult_list\">
		<li class=\"one\" style=\"background-image:url(".$ServerPath."images/img_adult_1.png);\">
			<div class=\"edu_adult_inner\">
				<h2 class=\"caption_left_underline TrnTag\"><b>iSpeak 101 일반회화과정 #1</b> (초중급 과정)</h2>
				<h3 class=\"caption_left_small TrnTag\">강의소개</h3>
				<trn class='TrnTag'>비교적 간단한 문법과 기초 표현을 위주로 초중급 회화를 학습하기 위한 과정으로서, 원어민 강사와의 학습을 위한 파트는 예복습용으로 활용될 수 있는 풍부한 한글 설명과 연습문제까지 겸비되어 있어 전화/화상 영어 수업에 최적화된 과정입니다.</trn>
				<h3 class=\"caption_left_small TrnTag\">학습대상</h3>
				<trn class='TrnTag'>- 영어 기초가 부족한 학습자<br>- 기초 문법 및 기본 회화 표현을 학습하고자 하는 학습자<br>- 매 레슨에 대한 한글 설명이 필요한 학습자<br>- 매 레슨에 대한 풍부한 연습문제를 희망하는 학습자</trn>
			</div>
		</li>
		<li class=\"two\" style=\"background-image:url(".$ServerPath."images/img_adult_2.png);\">
			<div class=\"edu_adult_inner\">
				<h2 class=\"caption_left_underline TrnTag\"><b>테마토크 주제토론</b> 과정</h2>
				<h3 class=\"caption_left_small TrnTag\">강의소개</h3>
				<trn class='TrnTag'>단순히 암기한 것을 말하는 영어가 아니라, 읽고 > 생각하고 > 말하는 연습을 가능하게 함으로써 영어를 실제 ‘언어’로 받아들이는 두뇌훈련을 하도록 훈련하는 과정으로서, 스토리를 바탕으로 내용을 점검하고 주어진 토론 질문에 대한 자신의 의견을 표현하는 법을 학습하는 과정입니다.</trn>
				<h3 class=\"caption_left_small TrnTag\">학습대상</h3>
				<trn class='TrnTag'>- 자신의 생각이나 주장을 영어로 표현하는데 어려움이 있는 학습자<br>- 재미, 교훈 등의 스토리를 바탕으로 토론하고자 하는 학습자<br>- 프리토킹을 희망하는 학습자<br>- 매일 연습에 대한 큰 부담없이 학습하고 하는 학습자</trn>
			</div>
		</li>
		<li class=\"three\" style=\"background-image:url(".$ServerPath."images/img_adult_3.png);\">
			<div class=\"edu_adult_inner\">
				<h2 class=\"caption_left_underline TrnTag\"><b>비즈니스</b> 과정</h2>
				<h3 class=\"caption_left_small TrnTag\">강의소개</h3>
				<trn class='TrnTag'>업무상 영어를 사용할 일이 많으신 분들을 위한 비지니스 실무영어입니다.<br>특히 Biz Phone은 전화 업무 수행을 위한 영어를 , Biz Upgrade는 토론 또는 프리젠테이션을 위한 영어를, Business English 1, 2권은 상황별 Dialogue를 바탕으로 한 표현 학습에 중점을 두고 있습니다.</trn>
				<h3 class=\"caption_left_small TrnTag\">학습대상</h3>
				<trn class='TrnTag'>- 비지니스 전화수행업무를 학습하고자 하는 학습자<br>- 비지니스 토론/프레젠테이션 업무를 학습하고자 하는 학습자<br>- 비지니스 상황별 표현을 학습하고자 하는 학습자</trn>
			</div>
		</li>
	</ul>

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