<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />

<link rel="stylesheet" type="text/css" href="./css/common.css">
</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<style>
body{background:#fff;}
.ContentPopup{padding:30px 30px; text-align:center;}
.ContentPopup h2{border-bottom:1px solid #ccc; padding-bottom:10px; font-size:16px; color:#444; text-align:left; margin-bottom:50px;}
</style>
<div class="ContentPopup" style="text-align:center;">
	<h2 class="Font1"> 이미지 업로드</h2>
  	
	
	<?
	$ImgID = isset($_REQUEST["ImgID"]) ? $_REQUEST["ImgID"] : "";
	$FormName = isset($_REQUEST["FormName"]) ? $_REQUEST["FormName"] : "";
	$Path = isset($_REQUEST["Path"]) ? $_REQUEST["Path"] : "";
	$ReScale = isset($_REQUEST["ReScale"]) ? $_REQUEST["ReScale"] : "";
	?>


	
	<form name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<input type="hidden" name="ImgID" value="<?=$ImgID?>">
	<input type="hidden" name="FormName" value="<?=$FormName?>">
	<input type="hidden" name="Path" value="<?=$Path?>">
	<input type="hidden" name="ReScale" value="<?=$ReScale?>">
	<input type="file" name="UpFile" id="UpFile" style="width:200px; height:32px; line-height:32px; margin-bottom:20px;">
	</form>

	

	<div class="BtnJoin" style="margin-bottom:100px;text-align:center;">
    	<a href="javascript:FormSubmit();" style="margin:0 auto;display:block; background-color:#556BAC; color:#ffffff; text-align:center; width:110px; width:100px; line-height:32px; font-size:14px;">업로드</a>
    </div>
</div>

<script>

function FormSubmit(){

	document.RegForm.action = "pop_image_upload_action.php"
	document.RegForm.submit();
}


</script>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>





