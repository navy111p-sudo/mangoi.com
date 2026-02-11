<!-- 헤더 영역 -->
<header class="header_wrap">
    <div class="header_area">
        <a href="/"><img src="images/logo_engliseed.png" alt="logo" class="header_logo"></a>
		<div>
        <a href="#" class="navi_mobile_bar"><i class="fas fa-bars"></i></a>
		{{BeforeLogin}}
        <ul class="navi_gnb">
            <li><a href="login_form.php" class="gnb_link" class="TrnTagCom">로그인</a></li>
            <!--li class="gnb_sns_wrap">
                <a href="#"><img src="images/btn_sns_face.png" alt="facebook" class="gnb_sns"></a>
                <a href="#"><img src="images/btn_sns_insta.png" alt="instagram" class="gnb_sns"></a>
                <a href="#"><img src="images/btn_sns_blog.png" alt="blog" class="gnb_sns"></a>
                <a href="#"><img src="images/btn_sns_you.png" alt="youtube" class="gnb_sns"></a>
            </li-->
            <li class="gnb_language" id="BtnTrnBox" style="display:none;">
                <a href="javascript:SetCkTrnLang(0)">한국어</a>
                <a href="javascript:SetCkTrnLang(1)">English</a>
                <a href="javascript:SetCkTrnLang(2)">中文</a>
            </li>
        </ul>
		{{/BeforeLogin}}
        {{AfterLogin}}
        <ul class="navi_gnb">
            <li><a href="logout.php" class="gnb_link" class="TrnTagCom">로그아웃</a></li>
            <li><a href="mypage.php" class="gnb_link" class="TrnTagCom">마이페이지</a></li>
            <!--li class="gnb_sns_wrap">
                <a href="#"><img src="images/btn_sns_face.png" alt="facebook" class="gnb_sns"></a>
                <a href="#"><img src="images/btn_sns_insta.png" alt="instagram" class="gnb_sns"></a>
                <a href="#"><img src="images/btn_sns_blog.png" alt="blog" class="gnb_sns"></a>
                <a href="#"><img src="images/btn_sns_you.png" alt="youtube" class="gnb_sns"></a>
            </li-->
            <li class="gnb_language" id="BtnTrnBox" style="display:none;">
                <a href="javascript:SetCkTrnLang(0)">한국어</a>
                <a href="javascript:SetCkTrnLang(1)">English</a>
                <a href="javascript:SetCkTrnLang(2)">中文</a>
            </li>
        </ul>
		{{/AfterLogin}}
        <ul class="navi_lnb">
            <li class="toggle_navi one">
                <a href="page.php?PageCode=mangoi" class="TrnTagCom">잉글리씨드 화상영어란</a>
            </li>
            <li class="toggle_navi two">
                <a href="#" class="TrnTagCom">교육과정</a>
                <ul class="sub_navi">
                    <li class="one"><a href="page.php?PageCode=level" class="TrnTagCom">레벨구성</a></li>
                    <li class="two"><a href="page.php?PageCode=books" class="TrnTagCom">교재안내</a></li>
                </ul>
            </li>
            <li class="toggle_navi three">
                <a href="teacher_intro.php" class="TrnTagCom">강사 소개</a>
            </li>
            <li class="toggle_navi four">
                <a href="avset.php" class="TrnTagCom">프로그램 자가진단</a>
            </li>
            <li class="toggle_navi five">
                <a href="remote_support.php" class="TrnTagCom">원격지원</a>
            </li>
            <li class="toggle_navi six">
                <a href="./lms" class="TrnTagCom" target="_blank">관리자</a>
            </li>
            <li class="toggle_navi seven navi_mypage">
                <a href="#" class="TrnTagCom">마이페이지</a>
                <ul class="sub_navi">
                    <li class="one"><a href="mypage.php" class="TrnTagCom">마이페이지</a></li>
                    <li class="two"><a href="mypage_study_room.php" class="TrnTagCom">나의공부방</a></li>
                    <li class="three"><a href="mypage_monthly_report.php" class="TrnTagCom">평가보고서</a></li>
                    <li class="four"><a href="javascript:window.open('https://pf.kakao.com/_xlqnSxd/chat', '_blank', 'location=no');" class="TrnTagCom">1:1 문의</a></li>
                    <li class="five"><a href="page.php?PageCode=payment" class="TrnTagCom">수강신청</a></li>
                </ul>
            </li>
            <!--li class="toggle_navi eight"><a href="product_order_cart.php" class="TrnTagCom">교재구매</a></li-->
        </ul>
    </div>
</header>
<script>
$(document).ready(function(){
	MvApi.defaultSettings({
			debug: false,
			// tcps: {key: 'MTgwLjE1MC4yMzAuMTk1OjcwMDE'},
			tcps: {key: 'MTIxLjE3MC4xNjQuMjMxOjcwMDE'},
			installPagePopup: "popup",
			company: {code: 2, authKey: '1577840400'},
			//web: {url: 'http://180.150.230.195:8080'},
			web: {url: 'https://www.mangoiclass.co.kr:8080'},

			// 클라이언트 설정 정보
			client: {
				// 암호화 사용 여부 - 유효성 검사를 수행하지 않는다.
				encrypt: false,
				// Windows Client 설정
				windows: {
					// 프로그램 이름
					product: 'BODA'
				},
				// Mobile Client 설정
				mobile: {
					// 스토어 배포 방식. true: Store 배포, false: 사설 배포
					store: true,
					// 스킴 이름
					scheme: 'cloudboda',
					// 패키지 이름
					packagename: 'zone.cloudboda',
				},
				// Mac Client 설정 - V7.3.0
				mac: {
					// 스토어 배포 방식. true: Store 배포, false: 사설 배포 - V7.3.1
					store: false,
					// 스킴 이름
					scheme: 'mangoi',
					// 패키지 이름
					packagename: 'zone.mangoi',
				},
				// 사용언어 - 없으면 한국어
				language: '<?=$ShLanguage?>',
				// 테마 - 클라이언트의 테마 코드 값 - v7.1.3
				theme: 3,
				// 버튼 타입 - 버튼을 표시하는 방식 - v7.1.3
				btnType: 1,
				// 어플리케이션 모드 - 회의,교육 등 동작 모드 설정 - v7.1.4
				appMode: 2
			},


		});
	$("#selfcheck").click(function () {
		console.log('test');
		MvApi.avset({
		}, function(){
		  // 성공 시 처리
		}, function(errorCode, reason){
		  // 오류 시 처리
		  location.href='https://www.mangoiclass.co.kr:8080/program/installGuide.do?groupcode=2&language=ko&platform=WINDOWS';
		  return;
		});



	});
});
</script>