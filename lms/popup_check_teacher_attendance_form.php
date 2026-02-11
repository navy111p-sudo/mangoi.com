<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
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
.ContentPopup h2{border-bottom:1px solid #ccc; padding-bottom:10px; font-size:16px; color:#444; text-align:left; margin-bottom:30px;}
</style>
<div class="ContentPopup" style="text-align:center;">
	<h2 class="Font1"> Check attendance</h2>
  	
	<form name="RegForm">
		<input type="hidden" name="TeacherID" id="TeacherID" value="<?=$TeacherID?>">
	</form>
	
	<?

	?>
	<div>
		<p style="font-size:18px;">Click the "attended" button to make sure your attendance</p>
	</div>
	<div>
		<p style="font-size:14px;">If you don't press "Attend" button, you can be considered "absent"</p>
	</div>

	<div class="BtnJoin" style="text-align:center; display: inline-block; margin-top: 30px;">
    	<a href="javascript:FormSubmit();" style=" display:inline-block; background-color:#AA0000; color:#ffffff; text-align:center; width:300px; height:40px; line-height:40px; font-size:20px; padding: 11px; text-decoration: none"><strong>Attended</strong></a>
		
		<!--
		<a href="javascript:CloseWindow();" style=" background-color:#556BAC; color:#ffffff; text-align:center; width:200px; line-height:34px; font-size:14px; margin-right: 10px; padding: 11px; text-decoration: none">Cancel</a>
		<a href="javascript:Logout();" style=" background-color:#556BAC; color:#ffffff; text-align:center; width:200px; line-height:34px; font-size:14px; padding: 11px; text-decoration: none">Logout</a>
		-->
    </div>
</div>

<script>

function FormSubmit(){ // 출석체크
//margin:0 auto;
	document.RegForm.action = "popup_check_teacher_attendance_action.php";
	document.RegForm.submit();
}


function CloseWindow() { // 창 닫기
	parent.$.fn.colorbox.close();
}

function Logout() { // LMS 로그인 페이지로 이동
	var date = new Date();
	document.cookie = "LinkLoginAdminID= " + "; expires=" + date.toUTCString() + "; path=/";
	document.cookie = "LinkLoginMemberID= " + "; expires=" + date.toUTCString() + "; path=/";
	document.cookie = "LoginAdminID= " + "; expires=" + date.toUTCString() + "; path=/";
	document.cookie = "LoginMemberID= " + "; expires=" + date.toUTCString() + "; path=/";

	parent.location.href = "login_form.php";
}

</script>
</body>
</html>
<?php
include_once('../includes/dbclose.php');
?>





