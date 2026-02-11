<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";

include_once('./inc_common_list_css.php');
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

<br/>
<div class="ContentPopup" style="text-align:center;margin-top:-30px;">
	<h2 class="Font1"> 엑셀파일 업로드</h2>

	<form name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
		<div style="margin-bottom:20px;line-height:1.5;">
			<span style="color:#ff0000;">
			업로드 시간이 오래걸릴수 있습니다
			<br>업로드 후 창이 닫힐때까지 기다려 주세요.
			<br>
			<br>
			</span>
			<span>
			<a href="javascript:window.open('../excel_sample/class_order_bulk_form.xls', '_system');" style="margin:0 auto;display:block; background-color:#888888; color:#ffffff; text-align:center; width:110px; width:100px; line-height:32px; font-size:14px; " download >양식다운로드</a>
			<!-- <a href="javascript:DownExcel();" style="margin:0 auto;display:block; background-color:#888888; color:#ffffff; text-align:center; width:110px; width:100px; line-height:32px; font-size:14px;">양식다운로드</a> -->
			<br>
			<br>
			반드시 위 양식을 다운받아 작성후 업로드 해주세요.
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			
			</span>
		</div>
		<input type="file" name="UpFile" id="UpFile" style="width:200px; height:32px; line-height:32px; margin-bottom:20px;">
	</form>
	

	<div class="BtnJoin" style="margin-bottom:100px;text-align:center;" id="BtnUpload">
    	<a href="javascript:FormSubmit();" style="margin:0 auto;display:block; background-color:#556BAC; color:#ffffff; text-align:center; width:110px; width:100px; line-height:32px; font-size:14px;" download>업로드</a>
    </div>
</div>

<script>

function DownExcel(){
	//window.open("class_order_bulk_excel_download.php", "_system", "location=yes");
	location.href = "class_order_bulk_excel_download.php";
}

function FormSubmit(){
	if (document.RegForm.UpFile.value=="")	{
		alert("엑셀 파일을 선택해 주세요.");
	}else{
		document.getElementById("BtnUpload").innerHTML = "<img src='images/uploading_ing.gif'><br><br>수강신청서 분석중 입니다.";
		document.RegForm.action = "class_order_bulk_value_check.php";
		document.RegForm.submit();
	}
}

function SearchSubmit(){
	document.RegForm.submit();
}



parent.$.colorbox.resize({width:"95%", height:"95%", maxWidth: "750", maxHeight: "650"});
</script>
</body>
</html>
<?php
include_once('../includes/dbclose.php');
?>





