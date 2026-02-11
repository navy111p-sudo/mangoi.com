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

<section class=\"level_wrap\">

	<div class=\"level_step_area\">
        <div class=\"level_step_area_inner\">
		  <img src=\"".$ServerPath."images/img_level_step_m.png\" alt=\"레벨테스트\" class=\"level_test_img m\">
          <img src=\"".$ServerPath."images/img_level_step_pc.png\" alt=\"레벨테스트\" class=\"level_test_img pc\">
        </div>
	</div>
	<div class=\"level_area\">
		<div class=\"level_test_1\">
            <div class=\"level_test_1_inner\">
                <h3 class=\"caption_center\">레벨테스트 소개</h3>
                <img src=\"".$ServerPath."images/img_level_intro.png\" alt=\"레벨테스트 소개\" class=\"img_level_intro\">
                <trn class='TrnTag'>레벨테스트(Initial Level Test)는 실제 수업에 들어가기에 앞서 자신의 <b>현재 어학능력을 측정하기 위한 테스트입니다.</b>병의 효과적인 치료를 위해 의사의 정확한 진단이 필요하듯, 효과적인 학습을 위해 <b>자신의 약점을 정확히 파악하는 것은무엇보다 우선시 되어야 합니다.</b></trn>
                
				<br><br>
				
				<trn class='TrnTag'>이처럼 중요한 진단을 더 이상 <b>자기소개 정도의 대략적인 평가에 의존하지 마세요. 점수만 후하게 주고 기분 좋게 만든 후 수강하게 만들려는 엉터리 레벨테스트에 의존하지 마세요.</b> 본사는 학생의 어학능력을 자연스럽고 심도 있게 측정할 수 있도록 특성화된 레벨테스트를 개발하였습니다.</trn>
				
				<br><br>

                <trn class='TrnTag'>회원가입 시 접하게 되는 <b>레벨테스트는 상당수의 가입자들에게 있어서 원어민과의 첫 대화인 경우가 많으므로, 부담감으로 인해 본래의 어학 능력을 제대로 보여주지 못하는 경우가 많습니다.</b> 레벨테스트 결과로 제시되는 성적표는 이러한 부분을 반영하지 않으므로, 본인의 생각보다 성적이 낮게 평가될 수 있습니다.</trn>
            </div>
		</div>
		<div class=\"level_test_2\">
            <div class=\"level_test_2_inner\">
                <h3 class=\"caption_center TrnTag\">레벨테스트 문제샘플</h3>
                <h3 class=\"level_test_2_caption\">Topic : Friendship</h3>

                <table class=\"level_test_2_table\">
                    <col width=\"20%\">
                    <col width=\"\">
                    <col width=\"22%\">
                    <!--
					<tr>
                        <td class=\"td_1\"><b>G</b>reeting</td>
                        <td class=\"td_2 TrnTag\">짧은 소개</td>
                        <th><a href=\"\">Sample MP3<img src=\"".$ServerPath."images/icon_sound.png\" alt=\"MP3\" class=\"icon\"></a></th>
                    </tr>
					-->
                    <tr>
                        <td class=\"td_1\"><b>Q</b>uestion 1</td>
                        <td class=\"td_2\">Do you have many friends?</td>
                        <th><a href=\"javascript:PlayLeveltestSound('1')\"><!--Sample MP3--><img src=\"".$ServerPath."images/icon_sound.png\" id=\"IconSound_1\" alt=\"MP3\" class=\"icon\"></a></th>
                    </tr>
                    <tr>
                        <td class=\"td_1\"><b>Q</b>uestion 2</td>
                        <td class=\"td_2\">Who do you consider to be your friends?</td>
                        <th><a href=\"javascript:PlayLeveltestSound('2')\"><!--Sample MP3--><img src=\"".$ServerPath."images/icon_sound.png\" id=\"IconSound_2\" alt=\"MP3\" class=\"icon\"></a></th>
                    </tr>
                    <tr>
                        <td class=\"td_1\"><b>Q</b>uestion 3</td>
                        <td class=\"td_2\">What qualities do you look for in friends?</td>
                        <th><a href=\"javascript:PlayLeveltestSound('3')\"><!--Sample MP3--><img src=\"".$ServerPath."images/icon_sound.png\" id=\"IconSound_3\" alt=\"MP3\" class=\"icon\"></a></th>
                    </tr>
                    <tr>
                        <td class=\"td_1\"><b>Q</b>uestion 4</td>
                        <td class=\"td_2\">How important are friends in your life?<br>Why do you think so?</td>
                        <th><a href=\"javascript:PlayLeveltestSound('4')\"><!--Sample MP3--><img src=\"".$ServerPath."images/icon_sound.png\" id=\"IconSound_4\" alt=\"MP3\" class=\"icon\"></a></th>
                    </tr>
                    <tr>
                        <td class=\"td_1\"><b>Q</b>uestion 5</td>
                        <td class=\"td_2\">Do you think that having more friends is better?<br>Why and in what way?</td>
                        <th><a href=\"javascript:PlayLeveltestSound('5')\"><!--Sample MP3--><img src=\"".$ServerPath."images/icon_sound.png\" id=\"IconSound_5\" alt=\"MP3\" class=\"icon\"></a></th>
                    </tr>
                </table>
            </div>
		</div>

		


		<div class=\"level_test_3\">
            <div class=\"level_test_3_inner\">
                <h3 class=\"caption_center TrnTag\">레벨테스트 채점 기준 및 고득점 전략</h3>
                <img src=\"".$ServerPath."images/img_level_high.png\" alt=\"레벨테스트 채점 기준 및 고득점 전략\" class=\"img_level_high\">
                <div class=\"level_test_strategy\">
                    <ul class=\"level_test_strategy_list\">
                        <li class='TrnTag'><b>01.</b> 발음</li>
                        <li class='TrnTag'><b>02.</b> 억양 및 강세</li>
                        <li class='TrnTag'><b>03.</b> 문법</li>
                        <li class='TrnTag'><b>04.</b> 어휘</li>
                        <li class='TrnTag'><b>05.</b> 유창성</li>
                        <li class='TrnTag'><b>06.</b> 질문에 대한 답변의 연관성</li>
                        <li class='TrnTag'><b>07.</b> 답변의 일관성</li>
                        <li class='TrnTag'><b>08.</b> 답변의 완벽성</li>
                    </ul>
                    <div class=\"level_test_strategy_text TrnTag\">각 단어의 발음을 올바르게 발음하는 것은 물론이거니와, 억양과 강세를 살리고 적당한 단위로 쉬어 읽음으로써 자신의 이미하는 바를 명확히 전달할 수 있는 능력이 요구됩니다. 또한, 문법적으로 올바른 문장을 사용하고, 의미하는 바에 대한 적절한 어휘를 선택하여 사용해야 합니다. 전반적으로 말하는 속도는 그리 중요하지 않습니다.<br>단, 생각하는 바를 영어로 표현함에 있어 큰 머뭇거림이 없어야 하며, 답변의 내용이 처음부터 끝까지 일관된 생각을 지지하고, 적절하게 결론지어져야 합니다.<br> 끝으로, 아무리 답변을 잘하더라도, 질문의 내용과 관련 없는 답변들은 점수와 무관할 뿐만 아니라, 오히려 실수를 저지를 경우 감점의 요인이 됩니다.<br>따라서 한 문장으로 답할지라도 질문의 핵심을 잘 파악하고 필요한 답변만을 하는 것이 좋습니다.</div>
                </div>
			</div>

			<div class=\"text_center button_wrap\"><a href=\"#\" class=\"btn_gradient_pink TrnTag\" onclick=\"OpenLeveltestTeacherList();\">무료 레벨테스트 신청하기</a></div>
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