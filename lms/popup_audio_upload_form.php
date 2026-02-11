<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
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
<style>
body{background:#fff;}
.ContentPopup{padding:30px 30px; text-align:center;}
.ContentPopup h2{border-bottom:1px solid #ccc; padding-bottom:10px; font-size:16px; color:#444; text-align:left; margin-bottom:50px;}
</style>
<div class="ContentPopup" style="text-align:center; width:500px;">
	<h2 class="Font1"> 음원 업로드</h2>
  	
	
	<?
	$SoundID = isset($_REQUEST["SoundID"]) ? $_REQUEST["SoundID"] : "";
	$FormName = isset($_REQUEST["FormName"]) ? $_REQUEST["FormName"] : "";
	$UpPath = isset($_REQUEST["UpPath"]) ? $_REQUEST["UpPath"] : "";
	?>


	
	<form name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<input type="hidden" name="SoundID" value="<?=$SoundID?>">
	<input type="hidden" name="FormName" value="<?=$FormName?>">
	<input type="hidden" name="UpPath" value="<?=$UpPath?>">
	<input type="file" name="UpFile" id="UpFile" style="width:200px; height:32px; line-height:32px; margin-bottom:20px;">
	</form>

	

	<div class="BtnJoin" style="margin-bottom:100px;text-align:center;">
    	<a href="javascript:FormSubmit();" style="margin:0 auto;display:block; background-color:#556BAC; color:#ffffff; text-align:center; width:110px; width:100px; line-height:32px; font-size:14px;">업로드</a>
    </div>
</div>

<script>

function FormSubmit(){

	document.RegForm.action = "popup_audio_upload_action.php"
	document.RegForm.submit();
}


</script>
</body>
</html>
<?php
include_once('../includes/dbclose.php');
?>





