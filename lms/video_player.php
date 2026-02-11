<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
?>
<!-- ============== only this page css ============== -->
<link rel="stylesheet" href="bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
<link rel="stylesheet" href="bower_components/kendo-ui/styles/kendo.material.min.css" id="kendoCSS"/>
<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->



</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<?php

$VideoType = isset($_REQUEST["VideoType"]) ? $_REQUEST["VideoType"] : "";
$VideoCode = isset($_REQUEST["VideoCode"]) ? $_REQUEST["VideoCode"] : "";

$VideoTypeName = ($VideoType==1) ? "Youtube" : "Vimeo";
$VideoCode = trim($VideoCode);

?>


<div id="page_content">
	<div id="page_content_inner">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$VideoTypeName?></span><span class="sub-heading" id="user_edit_position"></span></h2>
						</div>
					</div>
					<div class="user_content" style="text-align:center;">
					<?php if ($VideoType == 1) { ?>
						<iframe id="YoutubePlayer" width="640" height="360" src="https://www.youtube.com/embed/<?=$VideoCode?>?autoplay=1" frameborder="0" allowfullscreen></iframe>
					<?php } else { ?>
						<iframe id="VimeoPlayer" src="https://player.vimeo.com/video/<?php echo $VideoCode ?>?autoplay=1" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
					<?php }?>
					</div>
				</div>
			</div>

		</div>


	</div>
</div>


<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->



<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->

<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>