<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/main_style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/swiper.css">
<?php
include_once('./includes/common_header.php');

$Sql = "select * from Main limit 0,1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$MainLayout = $Row["MainLayout"];
$MainLayoutCss = $Row["MainLayoutCss"];
$MainLayoutJavascript = $Row["MainLayoutJavascript"];
$MainContent = $Row["MainContent"];
$MainContentCss = $Row["MainContentCss"];
$MainContentJavascript = $Row["MainContentJavascript"];
list($MainLayoutTop, $MainLayoutBottom) = explode("{{Page}}", $MainLayout);


if (trim($MainLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $MainLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

if (trim($MainContentCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $MainContentCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

?>

<?
//==== 모바일 기기 확인 =========================================== 
?>
<script type="text/javascript">
function isMobile(){
	var UserAgent = navigator.userAgent;

	if (UserAgent.match(/iPhone|iPod|Android|Windows CE|BlackBerry|Symbian|Windows Phone|webOS|Opera Mini|Opera Mobi|POLARIS|IEMobile|lgtelecom|nokia|SonyEricsson/i) != null || UserAgent.match(/LG|SAMSUNG|Samsung/) != null)
	{
		return true;
	}else{
		return false;
	}
}
</script>
<?
//==== 모바일 기기 확인 =========================================== 
?>



<?
//==== 팝업 관련 =========================================== 
?>
<script>
function PopupGetCookie( name ){
	var nameOfCookie = name + "=";
	var x = 0;
	while ( x <= document.cookie.length ){
		var y = (x+nameOfCookie.length);
		if ( document.cookie.substring( x, y ) == nameOfCookie ) {
			if ( (endOfCookie=document.cookie.indexOf( ";", y )) == -1 )
				endOfCookie = document.cookie.length;
			return unescape( document.cookie.substring( y, endOfCookie ) );
		}
		x = document.cookie.indexOf( " ", x ) + 1;
		if ( x == 0 )
			break;
	}
	return "";
}

function PopupSetCookie( name, value, expiredays ){ 

	var todayDate = new Date(); 
	todayDate.setDate( todayDate.getDate() + expiredays ); 
	document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";" 
} 

function PopupClose(PopupID,CloasType){
	if (CloasType==1){
		PopupSetCookie( 'Pop'+PopupID, 'Pop'+PopupID , 1);
	}
	document.getElementById("Popup"+PopupID).style.display = "none";
}

</script>
<?php
//==== 팝업 관련 =========================================== 
?>
</head>
<body class="main_body">
<?
include_once('./includes/common_body_top.php');
?>

<?
//==== 팝업 관련 =========================================== 

	$Sql = "select * from Popups where WebPopup=1 and PopupState=1 and DomainSiteID_".$DomainSiteID."=1 and datediff( PopupStartDateNum, now())<=0 and  datediff( PopupEndDateNum, now())>=0  order by PopupID desc";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);


	while($Row = $Stmt->fetch()) {
		
		$PopupTitle = $Row["PopupTitle"];
		$PopupType = $Row["PopupType"];
		$PopupContent = $Row["PopupContent"];
		$PopupImage = $Row["PopupImage"];

		$PopupImageLink = $Row["PopupImageLink"];
		$PopupImageLinkType = $Row["PopupImageLinkType"];

?>
		<div id="Popup<?=$Row["PopupID"]?>" style="z-index:9999; top:<?=$Row["PopupTop"]?>px; left:<?=$Row["PopupLeft"]?>px; width:<?=$Row["PopupWidth"]?>px;  position:absolute;
			display:; border:1px solid #ddd; background:rgba(255,255,255,0.92); margin:0; z-index:99999;">
			
			<p style="width:100%; padding:5px 12px 5px 5px; text-align:left; background:#333; color:#fff;box-sizing:border-box; margin:0;">
			<?=$PopupTitle?>
			</p>	

				<?php
				if ($PopupType==1){
				?>
				<div><?if (trim($PopupImageLink)!="") {?><a href="<?=$PopupImageLink?>" target="<?if ($PopupImageLinkType==1){?>_self<?}else{?>_blank<?}?>"><?}?><img src="./uploads/popup_images/<?=$PopupImage?>" style="display:block; margin:0; padding:0;"><?if (trim($PopupImageLink)!="") {?></a><?}?>
				</div>
				<?php
				}else{
				?>
				<div style="width:<?=$Row["PopupWidth"]?>px; height:<?=$Row["PopupHeight"]?>px;">	
					<?=$PopupContent?>
				</div>
				<?
				}
				?>
			
			<p style="width:100%; padding:5px 12px 5px 0; text-align:right; background:#333; margin:0;">
				<span style="width:70%;display:inline-block;vetical-align:middle;">
					<a href="javascript:PopupClose(<?=$Row["PopupID"]?>,1);" style="font-size:12px; color:#fff; vetical-align:top;" class="TrnTag">오늘 하루 그만 보기</a>
					<input type="checkbox" id="chk1" name="chk" onclick="PopupClose(<?=$Row["PopupID"]?>,1)" style="margin:0 3px 0 3px; vetical-align:top; top:2px; position:relative;">
					<a href="javascript:PopupClose(<?=$Row["PopupID"]?>,0);" style="font-size:12px; color:#fff; vetical-align:top;"class="TrnTag">[닫기]</a>
				</span>
			</p>
		</div>

		<script>
		if ( PopupGetCookie( "Pop<?=$Row["PopupID"]?>" ) != "Pop<?=$Row["PopupID"]?>" && isMobile()==false){
			document.getElementById("Popup<?=$Row["PopupID"]?>").style.display = "block";
		}else{
			document.getElementById("Popup<?=$Row["PopupID"]?>").style.display = "none";
		}
		</script>

<?
	}


//==== 팝업 관련 =========================================== 
?>



<?php
// $MainLayoutTop = convertHTML(trim($MainLayoutTop));
// $MainContent = convertHTML(trim($MainContent));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $MainLayoutTop = convertHTML(trim("{{Piece(header_gumiivyleague)}}"));
    $MainContent = convertHTML(trim($MainContent));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
    $MainContent = convertHTML(trim($MainContent));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.engedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
    $MainContent = convertHTML(trim($MainContent));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));

} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $MainContent = convertHTML(trim($MainContent));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }
echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $MainContent;
?>


<!-- 인트로 -->
<?
$HideHomeIntro =  isset($_COOKIE["HideHomeIntro"]) ? $_COOKIE["HideHomeIntro"] : "";
?>
<script>
function CloseHomeIntro(CloseType){
	location.href="index_intro_check.php?CloseType="+CloseType;
}
</script>
<div class="intro_wrap" style="display:<?if ($HideHomeIntro=="1"){?>none<?}?>;">
    <div class="intro_area">
        <div class="intro_logo">
            <?if ($DomainSiteID==0){//본사?>
				<img src="images/logo_mangi_text.png" class="logo_mangi_text" alt="로고">
			<?}else if ($DomainSiteID==1){//SLP?>
				<img src="images/logo_slp.png" class="logo_mangi_text" alt="로고">
			<?}else if ($DomainSiteID==2){//EIE?>
				<img src="images/logo_eie.png" class="logo_mangi_text" alt="로고">
			<?}else if ($DomainSiteID==3){//DREAM?>
				<img src="images/logo_dream.png" class="logo_mangi_text" alt="로고">
			<?}else if ($DomainSiteID==4){//THOMAS?>
				<img src="images/logo_mangi_text.png" class="logo_mangi_text" alt="로고">
			<?}else if ($DomainSiteID==5){//ENGLISHTELL?>
				<img src="images/logo_mangi_text.png" class="logo_mangi_text" alt="로고">
			<?}else{?>
				<img src="images/logo_mangi_text.png" class="logo_mangi_text" alt="로고">
			<?}?>
            <h4 class="intro_slogan">즐거운 화상영어 망고아이!</h4>
        </div>
        <ul class="intro_btns">
            
			<li id="BtnAppStore" style="display:none;margin-bottom:50px;"><a href="javascript:GoAppStore();" class="intro_go_btn bg"><img src="images/icon_intro_app.png" class="icon_intro" alt="로그인">앱으로 학습하기</a></li>
			
			<li><a href="javascript:CloseHomeIntro(0);" class="intro_go_btn"><img src="images/icon_intro_home.png" class="icon_intro" alt="메인화면">메인화면 바로가기</a></li>
            <li><a href="javascript:CloseHomeIntro(1);" class="intro_go_btn"><img src="images/icon_intro_login.png" class="icon_intro" alt="로그인">로그인후 수업받기</a></li>


        </ul>
    </div>
</div>


<?if ($DomainSiteID!=1){?>

<!-- 메인 비주얼 본사 -->
<div class="main_visual_wrap">
    <div class="dot_wrap">
        <div class="swiper-pagination"></div>
    </div>
    <div class="main_visual_cover"></div>
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide first" style="background-image:url('images/main_visual_1.jpg')">
                <div class="main_visual_text first">
                    <h4 class="main_visual_small TrnTag">즐거운 화상영어</h4>
                    <h1 class="main_visual_big TrnTag">망고아이</h1>
                    <div class="main_text TrnTag">망고아이는 비교할 수 없는<br>특별함을 선물합니다.</div>
                </div>
            </div>
            <div class="swiper-slide second" style="background-image:url('images/main_visual_2.jpg')">
                <div class="main_visual_text second">
                    <h4 class="main_visual_small TrnTag">주저없는 선택!</h4>
                    <h1 class="main_visual_big TrnTag">망고아이</h1>
                    <div class="main_text TrnTag">재미있고 효과적인 교육</div>
                </div>
            </div>
            <div class="swiper-slide third" style="background-image:url('images/main_visual_3.jpg')">
                <div class="main_visual_text third">
                    <h4 class="main_visual_small TrnTag">특별한 화상영어</h4>
                    <h1 class="main_visual_big TrnTag">망고아이</h1>
                    <div class="main_text TrnTag">자연스러운 반복학습의 효과</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?}else if ($DomainSiteID==1){//SLP?>

<!-- 메인 비주얼 SLP --> 
<div class="main_visual_wrap">
    <div class="dot_wrap">
        <div class="swiper-pagination"></div>
    </div>
    <div class="main_visual_cover"></div>
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide first" style="background-image:url('images/branch_visual_1.jpg')">
                <div class="main_visual_text slp first">
                    <h4 class="main_visual_small TrnTag">즐거운 화상영어</h4>
                    <h1 class="main_visual_big TrnTag">망고아이</h1>
                    <div class="main_text TrnTag">망고아이는 비교할 수 없는<br>특별함을 선물합니다.</div>
                </div>
            </div>
            <div class="swiper-slide slp second" style="background-image:url('images/branch_visual_2.jpg')">
                <div class="main_visual_text slp second">
                    <h4 class="main_visual_small TrnTag">주저없는 선택!</h4>
                    <h1 class="main_visual_big TrnTag">망고아이</h1>
                    <div class="main_text TrnTag">재미있고 효과적인 교육</div>
                </div>
            </div>
            <div class="swiper-slide slp third" style="background-image:url('images/branch_visual_3.jpg')">
                <div class="main_visual_text slp third">
                    <h4 class="main_visual_small TrnTag">특별한 화상영어</h4>
                    <h1 class="main_visual_big TrnTag">망고아이</h1>
                    <div class="main_text TrnTag">자연스러운 반복학습의 효과</div>
                </div>
            </div>
        </div>
    </div>
</div>  
<?}?>

<div class="main_wrap">    
    <!-- 레슨 비디오 영역 -->
    <section class="main_navi_wrap">
        <div class="main_navi_area">
            <ul class="main_go_left">
                <?if ($DomainSiteID==6){?>
					<li><a href="page.php?PageCode=books"><img src="images/icon_main_go_4.png" class="img" alt="교재열람"><div class="text TrnTag">교재열람</div></a></li>
					<li><a href="lesson_videos.php"><img src="images/icon_main_go_2.png" class="img" alt="Lesson Video"><div class="text TrnTag">Lesson Video</div></a></li>
				<?}else{?>
					<li><a href="javascript:OpenSampleMain();"><img src="images/icon_main_go_1.png" class="img" alt="Class Sample"><div class="text TrnTag">Class Sample</div></a></li>
					<li><a href="lesson_videos.php"><img src="images/icon_main_go_2.png" class="img" alt="Lesson Video"><div class="text TrnTag">Lesson Video</div></a></li>
				<?}?>
            </ul>
            <ul class="main_go_right">
                <?if ($DomainSiteID==6){?>
					<li><a href="javascript:OpenHiQuiz();"><img src="images/icon_main_go_3.png" class="img" alt="Review Quiz"><div class="text TrnTag">온라인학습</div></a></li>
					<li><a href="javascript:OpenHiDownload();"><img src="images/icon_main_go_5.png" class="img" alt="알스영어 다운로드"><div class="text TrnTag">알스영어 다운로드</div></a></li>
				<?}else{?>
					<li><a href="javascript:OpenSampleQuiz();"><img src="images/icon_main_go_3.png" class="img" alt="Review Quiz"><div class="text TrnTag">Review Quiz</div></a></li>
					<li><a href="page.php?PageCode=books"><img src="images/icon_main_go_4.png" class="img" alt="교재열람"><div class="text TrnTag">교재열람</div></a></li>
				<?}?>
            </ul>
			<div class="main_navi_box">
				<h2 class="main_capiton TrnTag">특별한 화상영어 프로그램, <b>망고아이</b></h2>
				<div class="main_caption_text TrnTag">엄마들의 주저 없는 선택 스마트한 엄마들의 절대적인 믿음</div>
				<ul class="main_navi_list">
					<li><a href="page.php?PageCode=mangoi"><img src="images/navi_main_1.gif" alt="화상영어란" class="img"></a></li>
					<li><a href="page.php?PageCode=mangoi#feature"><img src="images/navi_main_2.gif" alt="망고아이특장점" class="img"></a></li>
					<li><a href="page.php?PageCode=phi_center"><img src="images/navi_main_3.gif" alt="필리핀학습센터" class="img"></a></li>
					<li><a href="page.php?PageCode=level"><img src="images/navi_main_4.gif" alt="교육과정" class="img"></a></li>
					<li><a href="page.php?PageCode=payment"><img src="images/navi_main_5.gif" alt="수강신청" class="img"></a></li>
					<li><a href="faq.php"><img src="images/navi_main_6.gif" alt="자주묻는질문" class="img"></a></li>
				</ul>
			</div>
        </div>
    </section>  

    <!-- PC 세팅 영역 -->
    <section class="main_pc_wrap">
        <h2 class="main_capiton TrnTag">지금 바로 <b>망고아이</b>를 만나보세요.</h2>
        <div class="main_caption_text TrnTag">원활한 망고아이 화상영어를 위한 절차 및 안내입니다.</div>
        <ul class="main_pc_list">
            <li>
                <img src="images/icon_device_1.png" alt="수강절차" class="main_pc_img">
                <span class="pc_line"></span>
                <h3 class="TrnTag">수강절차</h3>
                <div class="main_pc_text TrnTag">망고아이 화상영어<br>수강절차</div>
                <a href="page.php?PageCode=course" class="main_pc_btn TrnTag">바로가기 <img src="images/arrow_go.png" alt="바로가기"></a>
            </li>
            <li>
                <img src="images/icon_device_2.png" alt="1:1 문의" class="main_pc_img">
                <span class="pc_line"></span>
                <h3 class="TrnTag">1:1 문의</h3>
                <div class="main_pc_text TrnTag">궁금하신 내용을 남겨주세요<br>친절히 답변드리겠습니다</div>
                <a href="javascript:window.open('https://pf.kakao.com/_xlqnSxd/chat', '_blank', 'location=no');" class="main_pc_btn TrnTag">바로가기 <img src="images/arrow_go.png" alt="바로가기"></a>
            </li>
            <li>
                <img src="images/icon_device_3.png" alt="묻고 답하기" class="main_pc_img">
                <span class="pc_line"></span>
                <h3 class="TrnTag">묻고 답하기</h3>
                <div class="main_pc_text TrnTag">망고아이에 궁금한 내용을<br>무엇이든 물어보세요</div>
                <a href="board_list.php?BoardCode=qna" class="main_pc_btn TrnTag">바로가기 <img src="images/arrow_go.png" alt="바로가기"></a>
            </li>
            <li>
                <img src="images/icon_device_4.png" alt="PC 원격지원" class="main_pc_img">
                <span class="pc_line"></span>
                <h3 class="TrnTag">PC 원격지원</h3>
                <div class="main_pc_text TrnTag">설치가 제대로 되지 않았다면<br>PC원격지원을 신청해 주세요</div>
                <a href="remote_support.php" class="main_pc_btn TrnTag">바로가기 <img src="images/arrow_go.png" alt="바로가기"></a>
            </li>
        </ul>
    </section>

    <!-- 비즈니스 영어 영역 -->
    <section class="main_banner_wrap">        
        <div class="main_banner_fade">
            <div class="main_banner_arrow">
                <a href="#"><img src="images/arrow_course_left.png" alt="prev" class="banner_prev"></a><a href="#"><img src="images/arrow_course_right.png" alt="next" class="banner_next"></a>
            </div>
            <div class="main_banner_area" style="background-image:url(images/img_main_course_1.png); display:block;">
                <h3 class="main_banner_caption_1 TrnTag">BUSINESS COURSE</h3>
                <h2 class="main_banner_caption_2 TrnTag">비즈니스 과정</h2>
                <span class="banner_line"></span>
                <div class="main_banner_text TrnTag">업무상 영어를 사용할 일이 많으신 분들을 위한<br>비즈니스 실무영어입니다.</div>
                <a class="main_white_btn" style="background:none; text-align:center; padding:0;">준비중입니다</a>
            </div>
            <div class="main_banner_area" style="background-image:url(images/img_main_course_2.png);">
                <h3 class="main_banner_caption_1 TrnTag">THEME TALK COURSE</h3>
                <h2 class="main_banner_caption_2 TrnTag">테마토크 주제토론 과정</h2>
                <span class="banner_line"></span>
                <div class="main_banner_text TrnTag">스토리를 바탕으로 내용을 점검, 주어진 토론 질문에<br>대한 자신의 의견을 표현하는 법을 학습하는 과정입니다.</div>
                <a class="main_white_btn TrnTag" style="background:none; text-align:center; padding:0;">준비중입니다</a>
            </div>
            <div class="main_banner_area" style="background-image:url(images/img_main_course_3.png);">
                <h3 class="main_banner_caption_1 TrnTag">ISPEAK 101 COURSE</h3>
                <h2 class="main_banner_caption_2 TrnTag">일반회화 과정</h2>
                <span class="banner_line"></span>
                <div class="main_banner_text TrnTag">비교적 간단한 문법과 기초 표현을 위주로<br>초중급 회화를 학습하기 위한 과정입니다.</div>
                <a class="main_white_btn TrnTag" style="background:none; text-align:center; padding:0;">준비중입니다</a>
            </div>
            <div class="main_banner_area" style="background-image:url(images/img_main_course_4.png);">
                <h3 class="main_banner_caption_1 TrnTag">HELLO PHONICS COURSE</h3>
                <h2 class="main_banner_caption_2 TrnTag">헬로 파닉스 과정</h2>
                <span class="banner_line"></span>
                <div class="main_banner_text TrnTag">영어를 처음 접하는 수강자들을 위해서 소리와<br>기초 어휘 학습을 위주로 편성된 파닉스 과정입니다.</div>
                <a class="main_white_btn TrnTag" style="background:none; text-align:center; padding:0;">준비중입니다</a>
            </div>
            <div class="main_banner_area" style="background-image:url(images/img_main_course_5.png);">
                <h3 class="main_banner_caption_1 TrnTag">POWER ENGLISH JR. COURSE</h3>
                <h2 class="main_banner_caption_2 TrnTag">파워잉글리쉬 주니어 회화 과정</h2>
                <span class="banner_line"></span>
                <div class="main_banner_text TrnTag">어휘, 문법, 독해, 영작, 일상회화를 통합적으로 다루어<br>실력향상과 함께 시험대비도 할 수 있도록<br>체계적으로 구성된 과정입니다.</div>
                <a class="main_white_btn TrnTag" style="background:none; text-align:center; padding:0;">준비중입니다</a>
            </div>
            <div class="main_banner_area" style="background-image:url(images/img_main_course_6.png);">
                <h3 class="main_banner_caption_1 TrnTag">AMERICA TEXTBOOK COURSE</h3>
                <h2 class="main_banner_caption_2 TrnTag">미국 초등교과 과정</h2>
                <span class="banner_line"></span>
                <div class="main_banner_text TrnTag">미국 교과서 My Pals are here! English시리즈를 이용하여<br>총체적인 영어학습이 가능하도록 구성된 과정입니다.</div>
                <a class="main_white_btn TrnTag" style="background:none; text-align:center; padding:0;">준비중입니다</a>
            </div>
        </div>
    </section>    

	<!-- 레슨 비디오 영역 -->
    <section class="main_video_wrap">
        <h2 class="main_capiton TrnTag">지금 바로 <b>망고아이</b>를 만나보세요.</h2>
        <div class="main_caption_text TrnTag">교재 + 레슨비디오 + 가이드를 통한 자연스러운 반복 학습의 효과</div>
        <div class="main_video_area">
            <div class="main_video_left">
                <h4 class="main_video_caption_1 TrnTag">LESSON VIDEO</h4>
                <h3 class="main_video_caption_2 TrnTag">망고아이 레슨 비디오 안내</h3>
                <div class="main_video_text TrnTag">학생들의 흥미도와 성취도를 높여주기 위해<br>예습과 복습개념으로 망고아이 선생님들이<br>직접 만든 영상입니다.</div>
                <!--<a href="javascript:OpenSampleLesson();" class="main_white_btn">레슨 비디오 보러가기</a>-->
				<a href="lesson_videos.php" class="main_white_btn TrnTag">레슨 비디오 보러가기</a>
            </div>
            <div class="main_video_right">
                <iframe class="main_video_iframe" src="https://www.youtube.com/embed/z8LskGDfrHU" frameborder="0" gesture="media" allowfullscreen></iframe>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript" src="js/swiper.js"></script>
<script>
    $(".intro_go_btn.main").click(function () {
		$(".intro_wrap").fadeOut(500);
	}) 
    
    function OpenSampleLesson() {
        var OpenUrl = "https://www.youtube.com/embed/zTP3jKmSxa8";

        $.colorbox({	
            href:OpenUrl
            ,width:"95%" 
            ,height:"95%"
            ,maxWidth: "850"
            ,maxHeight: "536"
            ,title:""
            ,iframe:true 
            ,scrolling:false
            //,onClosed:function(){location.reload(true);}
            //,onComplete:function(){alert(1);}
        }); 
    }

    function OpenSampleMain() {
        var OpenUrl = "https://www.youtube.com/embed/i2dYhNUS1kE";

        $.colorbox({	
            href:OpenUrl
            ,width:"95%" 
            ,height:"95%"
            ,maxWidth: "850"
            ,maxHeight: "536"
            ,title:""
            ,iframe:true 
            ,scrolling:false
            //,onClosed:function(){location.reload(true);}
            //,onComplete:function(){alert(1);}
        }); 
    }

    function OpenSampleQuiz(){
        var OpenUrl = "pop_quiz_study_sample.php";

        $.colorbox({	
            href:OpenUrl
            ,width:"95%" 
            ,height:"95%"
            ,maxWidth: "1000"
            ,maxHeight: "850"
            ,title:""
            ,iframe:true 
            ,scrolling:true
            //,onClosed:function(){location.reload(true);}
            //,onComplete:function(){alert(1);}
        }); 
    }

	function OpenHiQuiz(){
		window.open("http://www.arls.co.kr/data/P_mangoi.exe", "_blank"); 
	}

	function OpenHiDownload(){
		location.href = "http://arls.co.kr/data/arls_software/arls_setup.exe";
	}
</script>

<?
echo "\n";
echo $MainLayoutBottom;
echo "\n";
?>


<?
function CutStr($value){
	if ( mb_strlen($value, 'utf-8')>14 ){
		$value=mb_substr($value, 0, 12, "UTF-8") . "...";
	}
	return $value;

}
?>

<!----- 모바일 레이어 팝업------->
<?php

	$CookieMobilePopup =  isset($_COOKIE["MobilePopup"]) ? $_COOKIE["MobilePopup"] : "";

	$ExistPopup = 0;
	$Sql = "select count(*) as RowCount from Popups where PopupType=1 and DomainSiteID_".$DomainSiteID."=1 and MobilePopup=1 and PopupState=1 and datediff( PopupStartDateNum, now())<=0 and  datediff( PopupEndDateNum, now())>=0";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SubCode', $SubCode);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	

	if ($Row["RowCount"]>0){
		$ExistPopup = 1;
	}

	$useragent=$_SERVER['HTTP_USER_AGENT'];

	if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){

		if ($ExistPopup==1 && $CookieMobilePopup==""){
?>

	<!-- 모바일 레이어 팝업 -->
	<div class="popup_mobile">
		<div class="in">
			<!--div class="pop_top"><a href="#">skip</a></div-->
			<!-- 이 부분이 변경되었음 -->
			<div class="swiper-container popup_swiper" style="height:auto;">
				<div class="swiper-wrapper">
				<?
				$Sql = "select * from Popups where PopupType=1 and DomainSiteID_".$DomainSiteID."=1 and MobilePopup=1 and PopupState=1 and datediff( PopupStartDateNum, now())<=0 and  datediff( PopupEndDateNum, now())>=0  order by PopupID desc";

				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);

				while($Row = $Stmt->fetch()) {
					$PopupTitle = $Row["PopupTitle"];
					$PopupType = $Row["PopupType"];
					$PopupContent = $Row["PopupContent"];
					$PopupImage = $Row["PopupImage"];

					$PopupImageLink = $Row["PopupImageLink"];
					$PopupImageLinkType = $Row["PopupImageLinkType"];
				?>

					<a href="<?if (trim($PopupImageLink)!="") {?><?=$PopupImageLink?><?}else{?>#<?}?>" target="<?if ($PopupImageLinkType==1){?>_self<?}else{?>_blank<?}?>" class="swiper-slide popup_mobile_img"><img src="../uploads/popup_images/<?=$PopupImage?>" style="width:100%; display:block;"></a>
				<?
				}
				?>
				</div>
				<!-- Add Pagination 
				<div class="swiper-pagination"></div-->
			</div>
			<!-- 이 부분이 변경되었음 -->
			<div class="pop_check">
				<input type="checkbox" name="" id="pop_close" onclick="javascript:MobilePopupClose();"> <label for="pop_close"><span></span> 오늘 하루 그만 보기</label>
				<a class="btn_pop_close">닫기</a>
			</div>
		</div>
	</div>

	<!-- 이부분이 추가되었음 -->
	<link rel="stylesheet" type="text/css" href="js/swiper_popup/swiper.css">
	<script>
		$(".btn_pop_close").click(function () {
			$(".popup_mobile").fadeOut(500);
		});
	</script>
	<!--script src="js/swiper_popup/swiper.js"></script -->
	<!--script>
		var popup_swiper = new Swiper('.popup_swiper', {
			loop: true,
			autoplay: {
				delay: 4000,
				disableOnInteraction: false,
			},
			pagination: {
				el: '.swiper-pagination',
				clickable: true,
			}
		});
	</script-->
	<!-- 이부분이 추가되었음 -->

	<!-- 모바일 레이어 팝업 -->



	<script>
	function MobilePopupClose(){
		PopupSetCookie( 'MobilePopup', 'MobilePopup' , 1);
		$(".popup_mobile").fadeOut(500);
	}
	</script>


<?
		}
	
?>	
	<script>
	var varUA = navigator.userAgent.toLowerCase(); //userAgent 값 얻기

	//if ( varUA.indexOf('android') > -1 || varUA.indexOf("iphone") > -1 || varUA.indexOf("ipad") > -1 || varUA.indexOf("ipod") > -1 ) {
	if ( varUA.indexOf('android') > -1) {
		document.getElementById("BtnAppStore").style.display = "";
	}

	function GoAppStore(){
	 
		if ( varUA.indexOf('android') > -1) {
			//안드로이드
			location.href = "Intent://kr.ahsol.mangoi#Intent;scheme=mangoi;package=kr.ahsol.mangoi;end";
		
		} else if ( varUA.indexOf("iphone") > -1 || varUA.indexOf("ipad") > -1 || varUA.indexOf("ipod") > -1 ) {
			//IOS
			//location.href = "https://itunes.apple.com/app/id앱스토어아이디";
		} else {
			//아이폰, 안드로이드 외
			
		}

	}
	</script>	
<?	
	}


?>
<!----- 모바일 레이어 팝업------->



<?php
include_once('./includes/common_footer.php');

if (trim($MainContentJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $MainContentJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}

if (trim($MainLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $MainLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}
?>


</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>

