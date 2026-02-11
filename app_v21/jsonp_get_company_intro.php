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

	

	<!-- 회사소개 -->
	<section class=\"company_wrap\">
        <h2 class=\"sub_title_company TrnTag\">왜 <img src=\"".$ServerPath."images/logo_text_mangoi.png\" alt=\"망고아이\"> 화상영어인가?</h2>
		<div class=\"company_area\">
			<table class=\"company_table\">
				<tr>
					<td>
						<h2 class=\"caption_company\">Vision</h2>
						<h4 class='TrnTag'>글로벌 커뮤니케이션 인재 양성</h4>
						<h2 class=\"caption_company goal\">Goal</h2>
						<ul class=\"company_flag\">
							<li><img src=\"".$ServerPath."images/flag_usa.png\" alt=\"USA\"></li>
							<li><img src=\"".$ServerPath."images/flag_ch.png\" alt=\"CHINA\"></li>
							<li><img src=\"".$ServerPath."images/flag_jp.png\" alt=\"JAPAN\"></li>
							<li><img src=\"".$ServerPath."images/flag_kr.png\" alt=\"KOREA\"></li>
						</ul>
						<h4 class='TrnTag'>영어, 중국어, 일본어, 한국어</h4>
						<ul class=\"company_language\">
							<li class='TrnTag'>중국, 일본, 한국에 Triangle Languages Service Provider</li>
							<li class='TrnTag'>랭귀지 서비스를 통한 Communication enabler 포지셔닝</li>
						</ul>
					</td>
					<th><img src=\"".$ServerPath."images/img_mangoi_vision.jpg\" class=\"img_company\" alt=\"망고아이\"></th>
				</tr>
			</table>
			<h2 class=\"caption_company\">Strategy</h2>
			<img src=\"".$ServerPath."images/img_strategy_m.png\" alt=\"Strategy\" class=\"img_strategy m\">
            <img src=\"".$ServerPath."images/img_strategy_pc.png\" alt=\"Strategy\" class=\"img_strategy pc\">
			<h2 class=\"caption_company\">Key Success Factor</h2>
			<img src=\"".$ServerPath."images/img_factor_m.png\" alt=\"Key Success Factor\" class=\"img_facotr m\">
            <img src=\"".$ServerPath."images/img_factor_pc.png\" alt=\"Key Success Factor\" class=\"img_facotr pc\">
		</div>
	</section>

	<!-- 경영진 -->
	<section class=\"company_manager_wrap\">
		<div class=\"company_manager_area\">
			<h2 class=\"caption_sub\"><span class=\"normal TrnTag\">경영진</span></h2>
			<ul class=\"company_manager_list\">
				<li>
					<table class=\"company_manager_table\">
						<tr>
							<th class=\"company_manager_left\">
								<img src=\"".$ServerPath."images/photo_team_2.jpg\" alt=\"정우영대표\" class=\"photo_manager\">
								<h3 class='TrnTag'>정우영 <span class=\"break\">대표이사</span></h3>
							</th>
							<td>
								<ol class=\"company_manager_right\">
									<li class='TrnTag'>안산 SLP 원장</li>
									<li class='TrnTag'>성균관대 겸임교수 (현)</li>
									<li class='TrnTag'>성균관대 경영학 박사</li>
									<li class='TrnTag'>New York University 경제학 전공</li>
								</ol>
							</td>
						</tr>
					</table>
				</li>
				<li>
					<table class=\"company_manager_table\">
						<tr>
							<th class=\"company_manager_left\">
								<img src=\"".$ServerPath."images/photo_team_1.jpg\" alt=\"이병엽이사\" class=\"photo_manager\">
								<h3 class='TrnTag'>이병엽 <span class=\"break\">이사</span></h3>
							</th>
							<td>
								<ol class=\"company_manager_right\">
									<li class='TrnTag'>前 외대어학원 본원 원장</li>
									<li class='TrnTag'>Brighton 어학원 원장</li>
									<li class='TrnTag'>박학천 강남 직영원 원장</li>
									<li class='TrnTag'>톡스톡스 화상영어 본부장</li>
									<li class='TrnTag'>샘과나무 화상영어 총괄 본부장 역임</li>
								</ol>
							</td>
						</tr>
					</table>
				</li>
				<li>
					<table class=\"company_manager_table\">
						<tr>
							<th class=\"company_manager_left\">
								<img src=\"".$ServerPath."images/photo_team_1.jpg\" alt=\"장지웅 실장\" class=\"photo_manager\">
								<h3 class='TrnTag'>장지웅 <span class=\"break\">기획실장</span></h3>
							</th>
							<td>
								<ol class=\"company_manager_right\">
									<li class='TrnTag'>에듀비전 상담실장</li>
									<li class='TrnTag'>전) 망고아이 콜센터장</li>
									<li class='TrnTag'>리시움 어학원 강사</li>
								</ol>
							</td>
						</tr>
					</table>
				</li>
				<li>
					<table class=\"company_manager_table\">
						<tr>
							<th class=\"company_manager_left\">
								<img src=\"".$ServerPath."images/photo_team_3.jpg\" alt=\"Kristina\" class=\"photo_manager\">
								<h3>Kristina <span class=\"break\">General Manager</span></h3>
							</th>
							<td>
								<ol class=\"company_manager_right\">
									<li>Bachelor of Science in Nursing</li>
									<li>Licensed Nurse</li>
									<li>Mangoi teacher</li>
								</ol>
							</td>
						</tr>
					</table>
				</li>
				<li>
					<table class=\"company_manager_table\">
						<tr>
							<th class=\"company_manager_left\">
								<img src=\"".$ServerPath."images/photo_team_4.jpg\" alt=\"ED Deluna\" class=\"photo_manager\">
								<h3>ED Deluna <span class=\"break\">Trainer</span></h3>
							</th>
							<td>
								<ol class=\"company_manager_right\">
									<li>New York University Bachelor of Science in Professional </li>
									<li>Honors New York University Recipient of the American Hospitality</li>
									<li>Association Scholarship Hospitality University of santo Tomas</li>
								</ol>
							</td>
						</tr>
					</table>
				</li>
			</ul>
		</div>
	</section>

	<!-- 설립이념 -->
	<section class=\"company_mean_wrap\">
		<div class=\"company_mean_area\">
			<h2 class=\"caption_sub\"><span class=\"normal TrnTag\">망고아이 설립이념</span></h2>
			<h3 class=\"caption_mean TrnTag\">설립이념</h3>
			<div class=\"company_mean_text_1 TrnTag\">망고아이는 우리나라에 올바른 언어교육을 정착시키고자 설립되었습니다.<br>현재 한국의 주입식, 입시위주, 암기식 교육에서 벗어나 통합적인 교육을 통해 진정한 영어교육의 정도를 제시합니다. 교사, 교재, 교수법의 3요소가 적절하게 적용되어 수업 할 수 있도록 끊임없이 연구하고, 학생이 언어를 즐겁게 배울 수 있는 것을 목표로 합니다.</div>
			<h3 class=\"caption_mean TrnTag\">교육목표</h3>
			<ul class=\"company_mean_text_2\">
				<li class='TrnTag'><b>01.</b> 학습의 좋은 결과를 위하여 과정에 더 집중하여 교육한다.</li>
                <li class='TrnTag'><b>02.</b> 성취도와 흥미도를 모두 고려하여 교육한다.</li>
                <li class='TrnTag'><b>03.</b> 획일화된 교수법에서 벗어나 학생의 흥미, 적성, 가치관을 고려하여 교육한다.</li>
                <li class='TrnTag'><b>04.</b> 학교뿐만 아니라 회사나 사회에서 평생 사용할 수 있는 실용적인 교육을 추구한다.</li>
                <li class='TrnTag'><b>05.</b> 인성과 사회성 그리고 자유와 평등 같은 인간의 보편적 가치를 추구하는 교육을 한다.</li>
                <li class='TrnTag'><b>06.</b> 단기적인 성적 향상과 입시 진학 그리고 취업에만 국한 되지 않고 평생 교육을 목표로 지속적으로 성장하고 발전하는 실용적인 교육을 추구한다.</li>
                <li class='TrnTag'><b>07.</b> 인류와 사회에 공헌 할 수 있는 글로벌 인재를 배출하기 위해 교육한다.</li>
                <li class='TrnTag'><b>08.</b> 인성과 실력을 갖춘 우수한 강사진과 체계적인 관리시스템, 끊임없는 교재 연구로 교육의 질을 향상 시키는데 노력한다.</li>
            </ul>
		</div>
	</section>

	<!-- 연혁 -->
	<section class=\"company_history_wrap\">
		<div class=\"company_history_area\">
			<h2 class=\"caption_sub\"><span class=\"normal TrnTag\">연혁</span></h2>
			<ul class=\"company_history_list\">
				<li>
					<div class=\"year right TrnTag\"><h3 class=\"caption\">2019.07</h3>망고아이 Application 제작 설치</div>
					<div class=\"circle\"><div class=\"line\"></div><img src=\"".$ServerPath."images/bullet_history.png\" class=\"bullet\"></div>
					<div class=\"blank\"></div>
				</li>
				<li>
					<div class=\"blank\"></div>
					<div class=\"circle\"><div class=\"line\"></div><img src=\"".$ServerPath."images/bullet_history.png\" class=\"bullet\"></div>
					<div class=\"year left TrnTag\"><h3 class=\"caption\">2017.12</h3>망고아이 예습복습 비디오 및 퀴즈 제작 탑재</div>
				</li>
				<li>
					<div class=\"year right TrnTag\"><h3 class=\"caption\">2014.06</h3>중국 천진 법인설립</div>
					<div class=\"circle\"><div class=\"line\"></div><img src=\"".$ServerPath."images/bullet_history.png\" class=\"bullet\"></div>
					<div class=\"blank\"></div>
				</li>
				<li>
					<div class=\"blank\"></div>
					<div class=\"circle\"><div class=\"line\"></div><img src=\"".$ServerPath."images/bullet_history.png\" class=\"bullet\"></div>
					<div class=\"year left TrnTag\"><h3 class=\"caption\">2012.05</h3>홈페이지, 솔루션, 학생관리체계(LMS) Version-Up</div>
				</li>
				<li>
					<div class=\"year right TrnTag\"><h3 class=\"caption\">2011.06</h3>화상영어 콜센타 Ortigas Center로 이전</div>
					<div class=\"circle\"><div class=\"line\"></div><img src=\"".$ServerPath."images/bullet_history.png\" class=\"bullet\"></div>
					<div class=\"blank\"></div>
				</li>
				<li>
					<div class=\"blank\"></div>
					<div class=\"circle\"><div class=\"line\"></div><img src=\"".$ServerPath."images/bullet_history.png\" class=\"bullet\"></div>
					<div class=\"year left TrnTag\"><h3 class=\"caption\">2010.12</h3>전국 SLP 어학원에 화상 수업 실시 (약 1,300 회원)</div>
				</li>
				<li>
					<div class=\"year right TrnTag\"><h3 class=\"caption\">2009.12</h3>화상영어 Mangoi 서비스 개시</div>
					<div class=\"circle\"><div class=\"line\"></div><img src=\"".$ServerPath."images/bullet_history.png\" class=\"bullet\"></div>
					<div class=\"blank\"></div>
				</li>
				<li>
					<div class=\"blank\"></div>
					<div class=\"circle\"><div class=\"line\"></div><img src=\"".$ServerPath."images/bullet_history.png\" class=\"bullet\"></div>
					<div class=\"year left TrnTag\"><h3 class=\"caption\">2007.09</h3>실크로드 중국어 어학원 개원</div>
				</li>
				<li>
					<div class=\"year right TrnTag\"><h3 class=\"caption\">2006.03</h3>(주)에듀비전 법인설립</div>
					<div class=\"circle\"><div class=\"line\"></div><img src=\"".$ServerPath."images/bullet_history.png\" class=\"bullet\"></div>
					<div class=\"blank\"></div>
				</li>
				<li>
					<div class=\"blank\"></div>
					<div class=\"circle\"><div class=\"line\"></div><img src=\"".$ServerPath."images/bullet_history.png\" class=\"bullet\"></div>
					<div class=\"year left TrnTag\"><h3 class=\"caption\">2001.09</h3>안산SLP 영어 어학원 개원</div>
				</li>
			</ul>
		</div>
	</section>

	<!-- 조직도 -->
	<section class=\"company_organization_wrap\">
		<div class=\"company_organization_area\">
			<h2 class=\"caption_sub\"><span class=\"normal TrnTag\">조직도</span></h2>
			<img src=\"".$ServerPath."images/img_organization_m.png\" alt=\"조직도\" class=\"img_company_organization m\">
            <img src=\"".$ServerPath."images/img_organization_pc.png\" alt=\"조직도\" class=\"img_company_organization pc\">
		</div>
	</section>

	<!-- 주요사업 카테고리 -->
	<section class=\"company_main_wrap\">
		<div class=\"company_main_area\">
			<h2 class=\"caption_sub TrnTag\">주요사업 <span class=\"normal\">Categories</span></h2>
			<img src=\"".$ServerPath."images/img_categories.png\" alt=\"주요사업 Categories\" class=\"img_company_main\">
		</div>
	</section>

	<!-- 회사소개 CSR 갤러리 -->
	<section class=\"company_csr_wrap\">
		<div class=\"company_csr_area\">
			<h2 class=\"caption_sub\">CSR</h2>
			<div class=\"company_csr_text\">
				<trn class='TrnTag'>Mangoi 필리핀 민다나오 지역사회를 돕고자 다양한 봉사활동과  기부 행사를 진행하고 있습니다.</trn>

				<!--h4>Tahanan ng Pagmamahal Children’s Home</h4>
				Address : 146, Saint Francis St., Bgy. Orando, Pasig City Pasig, 1605 Philippines<br>
				Mobile Phone : 09275010605 -->
			</div>
	       <iframe src=\"".$ServerPath."iframe_csr.php\" class=\"iframe_csr\"></iframe>
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