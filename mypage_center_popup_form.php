<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<?
include_once('./includes/common_header.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">
<?
include_once('./includes/common_body_top.php');
?>
<!-- 라이트 박스 -->
<div class="light_box_wrap">
	<div class="light_box_area">
		<a href="#"><img src="images/btn_close_white.png" alt="닫기" class="light_box_close"></a>
		<div class="light_box_box">
			<div class="mantoman_write_wrap">
				<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
					<input type="hidden" name="MemberID" value="<?=$MemberID?>">
					<div class="mantoman_write_area">
						<h3 class="caption_underline TrnTag">1:1 친절상담 문의하기</h3>
						<ul class="mantoman_write_list">
							<li class="TrnTag">서비스, 홈페이지, 학습방법 등 여러분의 여러 궁금증을 1:1로 상담하여 드립니다. </li>
							<li class="TrnTag">이곳에 문의하신 내용은 100% 비밀이 보장됩니다. </li>
							<li class="TrnTag">상담가능시간 : 평일 AM 10:00 ~ PM 9:00</li>
						</ul>
						<table class="mantoman_write_table">
							<tr style="display:none;">
								<th class="TrnTag">분류</th>
								<td>
									<select class="mantoman_select" name="QnaMemberType">
										<option name="QnaMemberType" value="0" class="TrnTag">선택하세요</option>
										<option name="QnaMemberType" value="1" class="TrnTag">수강신청관련</option>
									</select>
								</td>
							</tr>
							<tr>
								<th class="TrnTag">제목</th>
								<td><input type="text" class="mantoman_input" name="QnaMemberTitle"></td>
							</tr>
							<tr>
								<th class="TrnTag">내용</th>
								<td><textarea class="mantoman_textarea" name="QnaMemberContent"></textarea></td>
							</tr>
							<!--
							<tr>
								<th>파일</th>
								<td><input type="file" class="mantoman_file"></td>
							</tr>
							-->
						</table>
						<div class="button_wrap flex_justify">
							<a href="javascript:FormSubmit();" class="button_orange_white mantoman TrnTag">신청하기</a>
							<a href="#" class="button_br_black mantoman light_box_cancle TrnTag">취소하기</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!--
<div id="page_content">
	<div id="page_content_inner">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-1-1">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"></span><span class="sub-heading" id="user_edit_position">관리자 인증</span></h2>
						</div>
					</div>
					<div class="user_content">	
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:none;">
							<li class="uk-active"><a href="#">Basic</a></li>
							<li><a href="#">Groups</a></li>
							<li><a href="#">Todo</a></li>
						</ul>
						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c">
										인증
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-7-10">
											<label for="CenterClassName">패스워드</label>
											<input type="text" id="CenterClassName" name="CenterClassName" value="<?=$CenterClassName?>" class="md-input label-fixed"/>
										</div>
									</div>
								</div>
								</div>
							</li>

						</ul>
					</div>
				</div>
			</div>

		</div>


	</div>
</div>
-->


<style>
.iframe_wrap{position:relative; padding-bottom: 56.25%; /* 16:9 */ padding-top: 25px; height: 0;}	
.iframe_video{position:absolute; top:0; left:0; width:100%; height:100%; border:0;}	
</style>

<?
//include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->



<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->

<?php
include_once('./includes/dbclose.php');
?>
</body>
</html>