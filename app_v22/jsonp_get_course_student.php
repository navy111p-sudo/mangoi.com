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
		<li><a href=\"#\" class=\"active TrnTag\"><span class=\"bar\"></span>학생과정</a></li>
		<li><a href=\"course_adult.html\" class=\"item-link item-content TrnTag\"><span class=\"bar\"></span>성인과정</a></li>
	</ul>      

	<ul class=\"edu_student_list\">
		<li class=\"one\" style=\"background-image:url(".$ServerPath."images/img_student_1.png);\">
			<div class=\"edu_student_inner\">
				<h2 class=\"caption_left_underline TrnTag\"><b>헬로우 파닉스</b> 과정</h2>
				<h3 class=\"caption_left_small TrnTag\">강의소개</h3>
				<trn class='TrnTag'>영어를 처음 접하는 수강자들을 위해 소리와 기초 어취 학습을 위주로 편성된 파닉스 과정입니다.<br>각 알파벳의 대소문자와 그 쏘는 방법을 학습한 후, 다양한 관련 기초 어휘를 이용하여 복습이 진행된 후, 자음과 모음의 특성 및 각 위치에서의 소리와 관련 어휘를 학습할 수 있도록 구성되었습니다.</trn>
				<h3 class=\"caption_left_small TrnTag\">학습대상</h3>
				<trn class='TrnTag'>- 파닉스를 학습하고자 하는 주니어 학습자<br>- 영어 발음에 자신이 없는 학습자<br>- 기초 어휘도 자신이 없는 영어 입문자</trn>
			</div>
		</li>
		<li class=\"two\" style=\"background-image:url(".$ServerPath."images/img_student_2.png);\">
			<div class=\"edu_student_inner\">
				<h2 class=\"caption_left_underline TrnTag\"><b>망고아이</b> 과정</h2>
				<h3 class=\"caption_left_small\">Mangoi Overview</h3>
				<trn class='TrnTag'>망고아이교재는 사진과 멀티미디어 요소를 가미하여 즐겁고 재미있게 공부할 수 있게끔, 학생들의 English Speaking 향상에 주안을 두고 개발되었습니다.<br>English Speaking 향상을 위해서 각 Level은 4개월 과정이며, 수업은 주3회 기준(월12회)이며, 1개월 동안 3주 간의 수업과 1주일간의 리뷰 및 테스트로 구성되어 있습니다. 테스트는 한달 동안의 배운 대화의 skill에 대하여 그 수준을 가늠케 하고, 상위 레벨 학생에게는 주제에 대한 의견이나 아이디어를 말하도록 합니다.</trn>
				<h3 class=\"caption_left_small\">Mangoi Level</h3>
				<trn class='TrnTag'>망고아이 교재는 총 8단계의 레벨이며, 모든 과정 수료에 필요한 시간은 32개월입니다.</trn>
			</div>
		</li>
		<li class=\"three\" style=\"background-image:url(".$ServerPath."images/img_student_3.png);\">
			<div class=\"edu_student_inner\">
				<h2 class=\"caption_left_underline TrnTag\"><b>파워잉글리쉬 주니어회화 #1</b> (초중급 과정)</h2>
				<h3 class=\"caption_left_small TrnTag\">강의소개</h3>
				<trn class='TrnTag'>재미있는 캐릭터들로 구성되어 흥미있게 학습할 수 있으며, 파닉스로부터 어휘, 문법, 독해, 영작, 일상회화를 통합적으로 다루어 듣기, 말하기 실력향상과 함께 시험대비도 할 수 있도록 체계적으로 구성된 과정입니다.</trn>
				<h3 class=\"caption_left_small TrnTag\">학습대상</h3>
				<trn class='TrnTag'>- 어휘, 독해, 영작 등 영어를 종합적으로 배우고자 하는 학습자<br>- 재미있는 캐릭터와 그림 등으로 구성된 흥미로운 교재를 원하는 학습자<br>- 어휘와 기초회화 능력이 부족한 학습자<br>- 자세하진 않지만 회화를 위해 기본적인 문법실력이 필요한 학습자</trn>
			</div>
		</li>
		<li class=\"four\" style=\"background-image:url(".$ServerPath."images/img_student_4.png);\">
			<div class=\"edu_student_inner\">
				<h2 class=\"caption_left_underline TrnTag\"><b>파워잉글리쉬 주니어회화 #2</b> (중고급 과정)</h2>
				<h3 class=\"caption_left_small TrnTag\">강의소개</h3>
				<trn class='TrnTag'>파워잉글리쉬 초중급 과정과 마찬가지로 통합적인 영역에 대한 학습이 이루어 집니다.<br>단, 각 영역에 대해 보다 심도있게 학습하며, 특히 Reading Comprehension 능력과 Listening Comprehension 및 Writing능력을 크게 향상 할수 있도록 구성되었습니다.</trn>
				<h3 class=\"caption_left_small TrnTag\">학습대상</h3>
				<trn class='TrnTag'>- 어휘, 독해, 영작 등 영어를 종합적으로 배우고자 하는 학습자<br>- 재미있는 캐릭터와 그림 등으로 구성된 흥미로운 교재를 원하는 학습자<br>- Reading 및 Listening Comprehension 능력을 향상시키고자 하는 학습자<br>- 유창한 말하기 실력을 위한 심도 있는 문법 능력과 영작 능력을 갖추고자 하는 학습자</trn>
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