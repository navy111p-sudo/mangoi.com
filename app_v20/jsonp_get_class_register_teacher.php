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
		<li><a href=\"#\" class=\"active\"><span class=\"bar\"></span>강사 먼저 선택</a></li>
		<li><a href=\"class_register_date.html\" class=\"item-link item-content\"><span class=\"bar\"></span>날짜 먼저 선택</a></li>
	</ul>

	<h3 class=\"caption_left_common\"><b>키워드</b> 검색<span>* 아래 키워드를 선택하시면 해당 키워드의 강사들을 찾으 실 수 있습니다. (중복선택가능)</span></h3>
	<ul class=\"class_keyword_list\">
		<li class=\"class_keyword_caption\">성별 선택</li>
		<li>남자</li>
		<li class=\"active\">여자</li>
		<li>상관없음</li>
	</ul>

	<ul class=\"class_keyword_list noborder\">
		<li class=\"class_keyword_caption\">성향 선택</li>
		<li>진지한</li>
		<li>실력있는</li>
		<li>큰 목소리</li>
		<li>낙관적</li>
		<li class=\"active\">적극적</li>
		<li>사교적</li>
		<li>개방적</li>
		<li>조용한</li>
		<li>사려 깊은</li>
		<li>평온한</li>
		<li class=\"active\">생기있는</li>
		<li>감성적</li>
		<li>열정적</li>
		<li>재미있는</li>
	</ul>

	<div class=\"class_teacher_search\">
		<input type=\"text\" name=\"\" class=\"class_teacher_input\" placeholder=\"검색어를 입력하세요.\">
		<a href=\"#\"><img src=\"".$ServerPath."images/btn_zoom_black.png\" alt=\"검색\" class=\"class_teacher_search_btn\"></a>
	</div>

	<h3 class=\"caption_left_common\"><b>강사</b> 선택<span>*  강사선택 버튼을 클릭하시어 수강가능 시간을 확인하시고 수강신청 하시면 됩니다.</span></h3>

	<ul class=\"teacher_select_list accordion-list\">
		<!-- 강사 한명 -->
		<li>
			<div class=\"teacher_photo_wrap\">
				<img src=\"".$ServerPath."images/photo_teacher_ralph.jpg\" alt=\"ralph\" class=\"teacher_photo\">
				<a href=\"#\" class=\"teacher_select_btn accordion-item-toggle\">강사선택</a>
			</div>
			<div class=\"teacher_time_wrap accordion-item-content\">
				<div class=\"teacher_time_caption\"><img src=\"".$ServerPath."images/icon_time.png\">수강가능시간</div>
				<span class=\"teacher_time_line\"></span>
				<table class=\"teacher_time_table\">
					<tr>
						<th>월요일</th>
						<td>
							<span class=\"teacher_time\">12:30</span>
							<span class=\"teacher_time\">14:30</span>
							<span class=\"teacher_time active\">15:30</span>
							<span class=\"teacher_time\">16:30</span>
							<span class=\"teacher_time\">17:30</span>
							<span class=\"teacher_time\">18:30</span>
						</td>
					</tr>
					<tr>
						<th>화요일</th>
						<td>
							<span class=\"teacher_time\">12:30</span>
							<span class=\"teacher_time\">14:30</span>
							<span class=\"teacher_time\">15:30</span>
						</td>
					</tr>
					<tr>
						<th>수요일</th>
						<td>
							<span class=\"teacher_time\">12:30</span>
							<span class=\"teacher_time\">14:30</span>
							<span class=\"teacher_time\">15:30</span>
						</td>
					</tr>
					<tr>
						<th>목요일</th>
						<td></td>
					</tr>
					<tr>
						<th>금요일</th>
						<td>
							<span class=\"teacher_time\">12:30</span>
							<span class=\"teacher_time\">14:30</span>
							<span class=\"teacher_time\">15:30</span>
						</td>
					</tr>
				</table>
				<a href=\"#\" class=\"teacher_select_submit open-popup\" data-popup=\".popup-class-payment\">수강신청하기</a>
			</div>
			<div class=\"teacher_profile_wrap\">
				<a href=\"#\" class=\"teacher_greeting_btn\">인사영상 <img src=\"".$ServerPath."images/arrow_big_right.png\"></a>
				<table class=\"teacher_profile_table\">
					<tr>
						<th>Name</th>
						<td><b>Ralph</b></td>
					</tr>
					<tr>
						<th>Education</th>
						<td>Bachelor of Science in Industrial Engineering</td>
					</tr>
					<tr>
						<th>Comment</th>
						<td>I like students who want to learn English because they are very eager to learn and very studious 1310 to improve their English skills.</td>
					</tr>
				</table>
				<div class=\"teacher_select_chart\"><img src=\"".$ServerPath."images/sample_teacher_chart_1.png\"></div>
			</div>
		</li>
		<!-- 강사 한명 -->
		<li>
			<div class=\"teacher_photo_wrap\">
				<img src=\"".$ServerPath."images/photo_teacher_farrah.jpg\" alt=\"farrah\" class=\"teacher_photo\">
				<a href=\"#\" class=\"teacher_select_btn accordion-item-toggle\">강사선택</a>
			</div>
			<div class=\"teacher_time_wrap accordion-item-content\">
				<div class=\"teacher_time_caption\"><img src=\"".$ServerPath."images/icon_time.png\">수강가능시간</div>
				<table class=\"teacher_time_table\">
					<tr>
						<th>월요일</th>
						<td>
							<span class=\"teacher_time\">12:30</span>
							<span class=\"teacher_time\">14:30</span>
							<span class=\"teacher_time\">15:30</span>
							<span class=\"teacher_time\">16:30</span>
							<span class=\"teacher_time\">17:30</span>
						</td>
					</tr>
					<tr>
						<th>화요일</th>
						<td>
							<span class=\"teacher_time\">12:30</span>
							<span class=\"teacher_time\">14:30</span>
							<span class=\"teacher_time active\">15:30</span>
						</td>
					</tr>
					<tr>
						<th>수요일</th>
						<td>
							<span class=\"teacher_time\">12:30</span>
							<span class=\"teacher_time\">14:30</span>
							<span class=\"teacher_time\">15:30</span>
						</td>
					</tr>
					<tr>
						<th>목요일</th>
						<td></td>
					</tr>
					<tr>
						<th>금요일</th>
						<td>
							<span class=\"teacher_time\">12:30</span>
							<span class=\"teacher_time\">14:30</span>
							<span class=\"teacher_time\">15:30</span>
						</td>
					</tr>
				</table>
				<a href=\"#\" class=\"teacher_select_submit open-popup\" data-popup=\".popup-class-payment\">수강신청하기</a>
			</div>
			<div class=\"teacher_profile_wrap\">
				<a href=\"#\" class=\"teacher_greeting_btn\">인사영상 <img src=\"".$ServerPath."images/arrow_big_right.png\"></a>
				<table class=\"teacher_profile_table\">
					<tr>
						<th>Name</th>
						<td><b>Farrah</b></td>
					</tr>
					<tr>
						<th>Education</th>
						<td>Bachelor of Science in Hotel and Restaurant Management</td>
					</tr>
					<tr>
						<th>Comment</th>
						<td>English is the subject that I love the most and I would like to share my passion and interest with others.</td>
					</tr>
				</table>
				<div class=\"teacher_select_chart\"><img src=\"".$ServerPath."images/sample_teacher_chart_1.png\"></div>
			</div>
		</li>
		<!-- 강사 한명 -->
		<li>
			<div class=\"teacher_photo_wrap\">
				<img src=\"".$ServerPath."images/photo_teacher_donna.jpg\" alt=\"donna\" class=\"teacher_photo\">
				<a href=\"#\" class=\"teacher_select_btn accordion-item-toggle\">강사선택</a>
			</div>
			<div class=\"teacher_time_wrap accordion-item-content\">
				<div class=\"teacher_time_caption\"><img src=\"".$ServerPath."images/icon_time.png\">수강가능시간</div>
				<table class=\"teacher_time_table\">
					<tr>
						<th>월요일</th>
						<td>
							<span class=\"teacher_time\">12:30</span>
							<span class=\"teacher_time\">14:30</span>
							<span class=\"teacher_time\">15:30</span>
							<span class=\"teacher_time\">16:30</span>
							<span class=\"teacher_time\">17:30</span>
						</td>
					</tr>
					<tr>
						<th>화요일</th>
						<td>
							<span class=\"teacher_time\">12:30</span>
							<span class=\"teacher_time active\">14:30</span>
							<span class=\"teacher_time\">15:30</span>
						</td>
					</tr>
					<tr>
						<th>수요일</th>
						<td>
							<span class=\"teacher_time\">12:30</span>
							<span class=\"teacher_time\">14:30</span>
							<span class=\"teacher_time\">15:30</span>
						</td>
					</tr>
					<tr>
						<th>목요일</th>
						<td></td>
					</tr>
					<tr>
						<th>금요일</th>
						<td>
							<span class=\"teacher_time\">12:30</span>
							<span class=\"teacher_time\">14:30</span>
							<span class=\"teacher_time\">15:30</span>
						</td>
					</tr>
				</table>
				<a href=\"#\" class=\"teacher_select_submit open-popup\" data-popup=\".popup-class-payment\">수강신청하기</a>
			</div>
			<div class=\"teacher_profile_wrap\">
				<a href=\"#\" class=\"teacher_greeting_btn\">인사영상 <img src=\"".$ServerPath."images/arrow_big_right.png\"></a>
				<table class=\"teacher_profile_table\">
					<tr>
						<th>Name</th>
						<td><b>donna</b></td>
					</tr>
					<tr>
						<th>Education</th>
						<td>Bachelor of Science in Elementary Education</td>
					</tr>
					<tr>
						<th>Comment</th>
						<td>I am a very detailed person I usually dont want a project or a goal to go out on a well planned structure I made on my own.I can be very trustworthy if really neededskilled teacherwellmanneredknows when to go and when to pause in life.</td>
					</tr>
				</table>
				<div class=\"teacher_select_chart\"><img src=\"".$ServerPath."images/sample_teacher_chart_1.png\"></div>
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