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
	<!--ul class=\"edu_tabs\">
		<li><a href=\"#\" class=\"active TrnTag\"><span class=\"bar\"></span>레벨구성</a></li>
		<li><a href=\"course_student.html\" class=\"item-link item-content TrnTag\"><span class=\"bar\"></span>학생과정</a></li>
		<li><a href=\"course_adult.html\" class=\"item-link item-content TrnTag\"><span class=\"bar\"></span>성인과정</a></li>
	</ul-->
	
	<section class=\"level_wrap_2\">
		<div class=\"level_area_2\">
			<h3 class=\"level_caption_2 TrnTag\">나는 초보라서 원어민과 수업이 힘들다??</h3>
			<div class=\"level_text TrnTag\">초보이기 때문에 더더욱 <div class=\"break\">원어민이 필요합니다.</div>
                <div>머뭇거리지 마시고 지금 바로<div class=\"break\">레벨 테스트 신청하세요.</div></div>
			</div>
			<a href=\"leveltest_register_intro.html\" class=\"btn_gradient_pink TrnTag\" >레벨테스트 신청하기</a>
		</div>
	</section>
    <h2 class=\"caption_center_underline\">8단계 심층 레벨 시스템</h2>
	<img src=\"".$ServerPath."images/table_level8.png\" class=\"img_level_table\" alt=\"8단계 심층 레벨 시스템\">
	<section class=\"level_wrap_1\">
		<div class=\"level_area_1\">
			<h3 class=\"level_caption\"><b>Level 1</b> (입문 : Novice Level)</h3>
			영어에 대한 단편적인 지식만을 갖추고 있거나, 지식은 많더라도 머릿속에만 존재하는 상태,
			또는 그동안 영어에 무관심하여 처음 배우는 것과 다름없는 상태입니다. 문장단위로 들리지
			않고 친숙한 단어나 표현만 들리며, 들린다고 할지라도 문장 속에서 의미하는 바가 아닌
			다른 뜻으로 받아들일 수 있는 단계입니다. 이해를 위해서 여러 번의 반복 훈련이 필요하며,
			질문을 받으면 문장을 구성하지 못하고 어색한 발음의 간단한 단어로 답하게 됩니다.

			<h3 class=\"level_caption\"><b>Level 2</b> (초급1 : Lower Beginner Level)</h3>
			흔히 쓰이는 단어나 표현만 이해할 수 있지만, 여전히 자신의 생각을 표현하는데 어려움이
			많으며, 적절한 시제나 표현으로 완벽한 문장을 만드는 것에도 어려움이 많은 상태입니다.
			학생의 이해를 위해서는 문장을 반복하거나 단순화시켜야 할 때가 많고, 질문 역시 간단해야
			이해가 가능한 경우가 많습니다.

			<h3 class=\"level_caption\"><b>Level 3</b> (초급2 : Upper Beginner Level)</h3>
			평소 잘 알고 학습한 상황들에 대해서 문장들을 잘 이해할 수 있고 여전히 제한된 어휘력을
			갖고 있지만, 남들이 이해할 수 있도록 자기의 생각을 표현할 수 있는 상태입니다. 간단한
			문장이나 질문을 만들 수 있지만, 여전히 명확한 이해를 위해서는 선생님의 반복이 필요한 단계입니다.

			<h3 class=\"level_caption\"><b>Level 4</b> (중급 1 : Lower Intermediate Level)</h3>
			자신이 마주치게 되는 거의 모든 말들을 이해할 수 있는 단계입니다. 표현함에 있어
			다소 어려움은 존재하지만, 다른 이들에게 자신의 생각을 전달 할 수 있습니다.
			읽고 이해함에 있어 문법이해 수준이 향상 되었으며, 어휘력 역시 좋은 편입니다.

			<h3 class=\"level_caption\"><b>Level 5</b> (중급 2 : Intermediate Level)</h3>
			상대방의 말을 쉽게 이해하고, 복잡한 질문에도 답할 수 있으며, 자신의 생각을 표현함에
			있어서 큰 어려움이 없는 상태입니다. 문장 구조의 문법적인 형태들에 대한 기초적인
			구사력을 습득한 상태이며, 어휘력이 좋기 때문에 더 복잡한 어휘나 다양한 표현들을
			이해하고 구사할 수 있는 수준입니다.

			<h3 class=\"level_caption\"><b>Level 6</b> (중급 3 : Upper Intermediate Level)</h3>
			일반적인 상황에서 완벽하게 상대방의 말을 이해하고, 자신의 생각을 말하며, 질문을 할 수
			있으며, 문법지식과 어휘력이 좋기 때문에 읽고 이해하거나, 자신의 생각을 표현하는데 있어
			별 무리가 없는 상태입니다. 자신감 있게 자신의 의사를 표출할 수 있으며, 거의 유창한
			단계라고 볼 수 있습니다.

			<h3 class=\"level_caption\"><b>Level 7</b> (상급 : Advanced Level)</h3>
			다양한 상황에서도 상대방의 말을 완벽하게 이해할 수 있으며, 자신의 생각을 다양한
			방법으로 표현할 수 있는 능력을 갖춘 상태입니다. 수준 있는 글이나 복잡한 구조의
			글들을 읽고 이해하는데 전혀 문제가 없으므로, 유창하고 자신감 있게 영어를 구사할 수
			있는 수준입니다.

			<h3 class=\"level_caption\"><b>Level 8</b> (최상급 : Proficient Level)</h3>
			실제 교양 있는 원어민에 버금갈 정도로 사적인 주제뿐만 아니라 일반적이고 사회적인
			내용에 이르기까지 유창하고 또한 자연스럽게 대화할 수 있는 상태입니다. 이미 영어구사에
			있어 상당한 수준에 이르렀기 때문에 아주 쉽게 수준 있는 내용들을 읽고 이해할 수 있으며,
			숙달된 문법지식과 구조분석력이 작문에도 그대로 드러나는 수준이라고 말할 수 있습니다.
			따라서 원어민들과 함께 일하는 전문적인 업무 환경에서도 능동적으로 잘 대처할 수 있습니다.
		</div>
	</section>	

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