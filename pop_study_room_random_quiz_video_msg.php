<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
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

<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-2.2.4.min.js"></script>
<script src="js/common.js"></script>

<body>
<?
include_once('./includes/common_body_top.php');
?>
<?
$PopType = isset($_REQUEST["PopType"]) ? $_REQUEST["PopType"] : "";

$VideoType = isset($_REQUEST["VideoType"]) ? $_REQUEST["VideoType"] : "";
$VideoCode = isset($_REQUEST["VideoCode"]) ? $_REQUEST["VideoCode"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$ClassVideoType = isset($_REQUEST["ClassVideoType"]) ? $_REQUEST["ClassVideoType"] : "";

$BookQuizID = isset($_REQUEST["BookQuizID"]) ? $_REQUEST["BookQuizID"] : "";
//$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area" style="width:90%;">
		<h3 class="caption_underline">안내</h3>

		<table class="level_reserve_table">
			<tr>
				<td class="radio_wrap time TrnTag" style="height:100px;padding:20px;line-height:2;text-align:center;">MES 교재 또는 BTS 교재를 사용하면 학생 레벨에 맞는 레슨 비디오와 리뷰퀴즈를 활용할 수 있습니다.<br>많은 이용 부탁드립니다.</td>
			</tr>
		</table>

		<div class="button_wrap flex_justify">
			<a href="javascript:FormSubmit();" class="button_orange_white mantoman TrnTag" style="width:100%;"><?if ($PopType=="Quiz"){?>퀴즈풀기<?}else{?>시청하기<?}?></a>
		</div>
	</div>
</div>


<script language="javascript">
function FormSubmit(){
	<?if ($PopType=="Quiz"){?>
		parent.OpenStudyQuizAction(<?=$BookQuizID?>, <?=$ClassID?>);;
	<?}else{?>
		parent.OpenStudyVideoAction(<?=$VideoType?>, '<?=$VideoCode?>', <?=$ClassID?>, <?=$ClassVideoType?>);
	<?}?>

	//parent.$.fn.colorbox.close();
}
</script>




</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>