<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
?>
<!doctype html>
<html>
<head>
<?
include_once('./includes/common_meta_tag.php');
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./css/common.css">
<style>
html{height:100%;}
</style>
</head>

<body class="body_login">
<div class="wrap_login">
	<div class="page_login" style="padding-top:70px;">
		<!--<img src="images/logo.png" style="margin-bottom:10px;width:150px;">-->
		<h2>다국어 관리 시스템<br><br><b>ADMIN LOGIN</b></h2>
		
		<form name="LoginForm" method="post" id="LoginForm" autocomplete="off">
		<div class="box_login">
			<input type="email" id="ApplyMemberLoginID" name="ApplyMemberLoginID" value="<?php if (isset($_COOKIE["RememberAdminID"])){ echo $_COOKIE["RememberAdminID"]; }?>" onKeyPress="FormSubmitEn()" placeholder="아이디">
			<input type="password"  id="ApplyMemberLoginPW" name="ApplyMemberLoginPW" onKeyPress="FormSubmitEn()" placeholder="비밀번호">
			<a href="javascript:FormSubmit();" class="btn_login">로그인</a>
			<div class="bottom">
				<div class="left"><input type="checkbox" id="ApplyRememberID" name="ApplyRememberID" <?php if (isset($_COOKIE["RememberAdminID"])){ ?>checked<?php }?>> <label for="ApplyRememberID"><span></span> 아이디 저장</label></div>
				<!--<a href="#" class="right"><span><img src="images/icon_zoom_black.png"></span> 아이디/비밀번호 찾기</a>-->                  
			</div>
			<div class="login_slogan">Multilingual Management System</div>
		</div>
		</form>

	</div>
</div>

<script language="javascript">
function FormSubmit(){
	obj = document.LoginForm.ApplyMemberLoginID;
	if (obj.value==""){
		alert('아이디를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.LoginForm.ApplyMemberLoginPW;
	if (obj.value==""){
		alert('비밀번호를 입력하세요.');
		obj.focus();
		return;
	}

	document.LoginForm.action = "login_action.php";
	document.LoginForm.submit();

}

function FormSubmitEn(){
	if (event.keyCode == 13){
		FormSubmit();
	}
}

</script>

</body>
</html>
<?php
include_once('../includes/dbclose.php');
?>
