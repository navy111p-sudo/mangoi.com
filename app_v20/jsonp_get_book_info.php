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



$PageBookInfoHTML = "

 
		<section class=\"books_wrap one\">
			<h2 class=\"caption_sub\">Mango English Study (MES) : 초급, 중급</h2>
			<ul class=\"books_intro_list\">
				<li><b>01.</b>초급부터 중급까지 영어를 처음 시작하는 학생들을 위해 흥미로운 사진들과 함께 대화 형식으로 배우는 망고아이 자체 개발 교재</li>
				<li><b>02.</b>풍부한 ESL경험을 가진 미국교사분들이 약 2년에 걸쳐 개발하여 지난 10년 동안 망고아이에서 애용한 최고의 컨텐츠 교재</li>
				<li><b>03.</b>예복습 비디오와 테스트가 함께 있어 완전 학습이 가능하며 문장 위주의 수업으로 자연스러운 의사소통을 유도함</li>
				<li><b>04.</b>총 102개의 레슨으로 구성되어 있음</li>
			</ul>
            <ul class=\"books_img_list four\">
				<li><img src=\"".$ServerPath."../images/book_mes_1.jpg\" class=\"book\"></li>
				<li><img src=\"".$ServerPath."../images/book_mes_2.jpg\" class=\"book\"></li>
				<li><img src=\"".$ServerPath."../images/book_mes_3.jpg\" class=\"book\"></li>
				<li><img src=\"".$ServerPath."../images/book_mes_4.jpg\" class=\"book\"></li>
			</ul>
		</section>
		
		<section class=\"books_wrap\">
			<h2 class=\"caption_sub\">Bubble Tea Study (BTS) : 초급, 중급</h2>
			<ul class=\"books_intro_list\">
				<li><b>01.</b>기초부터 중급까지 다양하고 독특한 이미지들로 구성된 2019년 출판된 최신 교재</li>
				<li><b>02.</b>기초 문법과 발음 교정 (Tongue Twist)를 포함하고 음악적인 재미를 위해 각 lesson마다 영어 노래를 삽입하였고 QR 코드로 언제든지 유투브에서 감상할 수 있음</li>
				<li><b>03.</b>실생활에서 많이 사용하는 관용 문장과 단어들을 독특하고 흥미로운 사진과 그림들과 함께 연상하여서 배우는 망고아이 자체 개발의 최신판</li>
				<li><b>04.</b>예복습 비디오와 퀴즈가 혼합되어 있어서 충분한 학습량을 통해서 영어의 듣기와 말하기의 향상에 큰 도움이 됨</li>
				<li><b>05.</b>현재 교사들과 교재 개발 전문가들이 현장 경험과 영어 회화와 음성이론을 바탕으로 듣기와 말하기 화상 수업에 최적화 하여 만든 컨텐츠</li>
				<li><b>06.</b>Homework Book 이 있어서 집에서도 학원에서도 글로 쓰면서 예복습도 가능한 온오프라인의 교재 컨텐츠</li>
				<li><b>07.</b>총 102개의 레슨으로 구성되어 있음</li>
			</ul>
			<ul class=\"books_img_list four\">
				<li><img src=\"".$ServerPath."../images/book_bts_1.jpg\" class=\"book\"></li>
				<li><img src=\"".$ServerPath."../images/book_bts_2.jpg\" class=\"book\"></li>
				<li><img src=\"".$ServerPath."../images/book_bts_3.jpg\" class=\"book\"></li>
				<li><img src=\"".$ServerPath."../images/book_bts_4.jpg\" class=\"book\"></li>
			</ul>
		</section>
		
		<section class=\"books_wrap\">
			<h2 class=\"caption_sub\">Shake It Up (SIU) : 중급, 고급</h2>
			<ul class=\"books_intro_list\">
				<li><b>01.</b>Bubble Tea Study (BTS)를 마친 학생들이 주제(토픽)을 가지고 문법과 패턴에 맞게 자유롭게 긴 문장을 사용하며 대화하는 능력을 키우는 교재</li>
				<li><b>02.</b>미국에서 수년간 현직 교사 생활을 한 개발원이 학생들이 가장 관심있어 하는 주제들을 중심으로 하여서 대화를 이어가며 수업할 수 있는 프로그램</li>
				<li><b>03.</b>화상영어 수업에 최적화된 구성으로 흥미로운 사진과 이미지들을 중요 문장들을 함께 문법에 맞게 익히는 능력을 키우는 중급이상 단계</li>
				<li><b>04.</b>단순한 회화 수업을 넘어서 중요한 문법과 어휘도 함께 사용하며 문장을 익힐 수 있는 통합식 영어 교재</li>
				<li><b>05.</b>총 102개의 레슨으로 구성되어 있음</li>
			</ul>
			<ul class=\"books_img_list three\">
				<li><img src=\"".$ServerPath."../images/book_siu_h_1.jpg\" class=\"book\"></li>
				<li><img src=\"".$ServerPath."../images/book_siu_h_2.jpg\" class=\"book\"></li>
				<li><img src=\"".$ServerPath."../images/book_siu_h_3.jpg\" class=\"book\"></li>
			</ul>
			<ul class=\"books_img_list three\">
				<li><img src=\"".$ServerPath."../images/book_siu_v_1.jpg\" class=\"book\"></li>
				<li><img src=\"".$ServerPath."../images/book_siu_v_2.jpg\" class=\"book\"></li>
                <li><img src=\"".$ServerPath."../images/book_siu_v_3.jpg\" class=\"book\"></li>
			</ul>
		</section>
		



";






$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageBookInfoHTML"] = $PageBookInfoHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>