<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
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

<?php

$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$TeacherVideoType = isset($_REQUEST["TeacherVideoType"]) ? $_REQUEST["TeacherVideoType"] : "";
$TeacherVideoCode = isset($_REQUEST["TeacherVideoCode"]) ? $_REQUEST["TeacherVideoCode"] : "";


?>


<div id="page_content">
	<div id="page_content_inner">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-1-1">
				<div class="md-card">
				<!--
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"></span><span class="sub-heading" id="user_edit_position"></span></h2>
						</div>
					</div>
				-->
					<div class="user_content iframe_wrap" style="">
					<?php if ($TeacherVideoType == 1) { ?>
						<iframe id="YoutubePlayer" width="100%" height="100%" class="iframe_video" src="https://www.youtube.com/embed/<?=$TeacherVideoCode?>?autoplay=1&cc_load_policy=1" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
					<?php } else { ?>
						<iframe id="VimeoPlayer" width="100%" height="100%" class="iframe_video" src="https://player.vimeo.com/video/<?=$TeacherVideoCode?>?autoplay=1" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
					<?php }?>
					</div>
				</div>
			</div>

		</div>


	</div>
</div>

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