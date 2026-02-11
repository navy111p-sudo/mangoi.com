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
	<h2 class="Font1"> 증빙자료 업로드</h2>
  	
	
	<?
	$FormName1 = isset($_REQUEST["FormName1"]) ? $_REQUEST["FormName1"] : "";
	$FormName2 = isset($_REQUEST["FormName2"]) ? $_REQUEST["FormName2"] : "";
	$FormName3 = isset($_REQUEST["FormName3"]) ? $_REQUEST["FormName3"] : "";
	$UpPath = isset($_REQUEST["UpPath"]) ? $_REQUEST["UpPath"] : "";
	?>


	
	<form name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<input type="hidden" name="FormName1" value="<?=$FormName1?>">
	<input type="hidden" name="FormName2" value="<?=$FormName2?>">
	<input type="hidden" name="FormName3" value="<?=$FormName3?>">
	<input type="hidden" name="UpPath" value="<?=$UpPath?>">
	<input type="file" name="UpFile" id="UpFile" style="width:200px; height:32px; line-height:32px; margin-bottom:20px;">
	</form>

	

	<div class="BtnJoin" style="margin-bottom:100px;text-align:center;">
    	<a href="javascript:FormSubmit();" style="margin:0 auto;display:block; background-color:#556BAC; color:#ffffff; text-align:center; width:110px; width:100px; line-height:32px; font-size:14px;">업로드</a>
    </div>
</div>

<script>

function FormSubmit(){
	if (document.RegForm.UpFile.value!=""){
		document.RegForm.action = "popup_doc_file_upload_action.php"
		document.RegForm.submit();
	}else{
		alert("파일을 선택하세요.");
	}
}


</script>
</body>
</html>
<?php
include_once('../includes/dbclose.php');
?>





