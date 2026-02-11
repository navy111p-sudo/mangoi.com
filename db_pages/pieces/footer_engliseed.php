<!-- 하단 공통 영역 -->
<section>
	<div class="common_bottom">
    	<div class="notice_wrap">
        	<h3 class="TrnTagCom">공지사항<a href="board_list.php?BoardCode=notice"><img src="images/btn_bbs_more.png" alt="더보기" class="btn_bbs_more"></a></h3>
            <ul class="notice_list" id="MainFooterNotice">
            	<!-- 공지사항 영역-->
            </ul>
        </div>
        <div class="faq_wrap">
        	<h3 class="TrnTagCom">자주묻는질문<a href="faq.php"><img src="images/btn_bbs_more.png" alt="더보기" class="btn_bbs_more"></a></h3>
            <ul class="faq_list" id="MainFooterQna">
            	<!-- QnA 영역-->
            </ul>
        </div>
        <div class="center_wrap">
        	<h3 class="TrnTagCom">학습지원센터</h3>
            <img src="images/icon_talk.png" alt="학습센터" class="img_center">
            <b class="tel_bold">1644-0561</b>
            <div class="work_date">
                <trn  class="TrnTagCom">월 ~ 금(주말 및 공휴일 휴무)<br></trn>
                <trn  class="TrnTagCom">운영시간 10:00 ~ 20:00<br></trn>
                <trn  class="TrnTagCom">점심시간 12:30 ~ 13:30<br></trn>
                <trn  class="TrnTagCom">수업시간 14:00 ~23:00</trn>
            </div>
        </div>
    </div>
</section>

<!-- 푸터 영역 -->
<footer>
	<div class="foot_1">
    	<ul class="foot_menu">
        	<li><a href="page.php?PageCode=mangoi" class="TrnTagCom">망고아이 소개</a></li>
            <li><a href="page.php?PageCode=provision" class="TrnTagCom">이용약관</a></li>
            <li><a href="page.php?PageCode=privacy" class="TrnTagCom">개인정보 보호정책</a></li>
            <li><a href="page.php?PageCode=email" class="TrnTagCom">이메일 무단수집 거부</a></li>
        </ul>
        <div class="foot_video"><a href="javascript:OpenSampleMain();"  class="TrnTagCom">망고아이 수업영상 보러가기 <img src="images/btn_play_orange.png" alt="망고아이 수업영상 보러가기" class="img"></a></div>
    </div>
    <div class="foot_2">
    	<div class="foot_2_wrap">
        	<div class="foot_left">
            	{{FooterLogo}}
                    <div class="foot_add">
	   {{FooterAddr1}}
                    <trn  class="TrnTagCom">통신판매업신고번호 : 제 2010-경기안산-0634호</trn>
                    <div class="break">개인정보 보호 책임자 : 장지웅(jangjiwoong@mangoi.com)</div>
                </div>
            </div>
            <div class="foot_right">
            	<b class="foot_center"><span class="TrnTagCom">고객만족센터</span>1644-0561</b>
                <div class="TrnTagCom">운영시간 10:00 ~ 20:00 (주말 및 공휴일 휴무)</div>
            </div>
        </div>
    </div>
	<div class="foot_3">COPYRIGHT (C) 2013 EDUVISION Corp.<div class="break">ALL RIGHTS RESERVED.</div></div>
</footer>

<!-- 라이트 박스 유튜브 영상 -->
<div class="light_box_wrap_youtube" style="display:none;">
    <div class="light_box_area">
        <a href="#"><img src="images/btn_close_white.png" alt="닫기" class="light_box_close"></a>
        <div class="light_box_box">
            <div class="youtube_wrap">
                <div class="youtube_area">
                    <!--h3 class="caption_underline" class="TrnTagCom">망고잉글리시 수업동영상</h3-->
                    <iframe class="main_youtube" src="https://www.youtube.com/embed/tBhVouBX-9A" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 라이트 박스 -->

<script language="javascript">
function OpenMangoiVideo() {

	var OpenUrl = "https://www.youtube.com/embed/4MOL-zfIub4";

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
</script>
<script>
    function dictionary() {
        window.open("https://small.dic.daum.net/", "dictionary", "width=400,height=500");
    }
</script>