<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./_header.php');
?>
<body class="login">


<form name="LoginForm" method="post">
<div class="logbox" style="height:250px;">
	<h1 style="padding:20px 0 8px 0;"><img src="./images/logo.png" style="width:210px;"></h1>
	
	<table width="350" border="0" cellpadding="0" cellspacing="0">
	  <tr>
		<th>아이디</th>
		<td><input type="text" id="ApplyMemberLoginID" name="ApplyMemberLoginID" size="40" value="<?php if (isset($_COOKIE["RememberAdminID"])){ echo $_COOKIE["RememberAdminID"]; }?>" onKeyPress="FormSubmitEn()"></td>
		<td rowspan="3"><div class="btn_log" onclick="FormSubmit()" style="cursor:pointer;">LOGIN</div></td>
	  </tr>
	  <tr>
		<th height="6"></th>
		<td></td>
	  </tr>
	  <tr>
		<th>패스워드</th>
		<td><input type="password" id="ApplyMemberLoginPW" name="ApplyMemberLoginPW" size="40" value="" onKeyPress="FormSubmitEn()"></td>
	  </tr>
	</table>
    <div style="margin:5px 0 0 210px;">
	<input type="checkbox" id="ApplyRememberID" name="ApplyRememberID" <?php if (isset($_COOKIE["RememberAdminID"])){ ?>checked<?php }?> style="width:14px; height:14px; border:none; vertical-align:-5px;"><label for="ApplyRememberID">Remember me</label></div>
</div>
</form>


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

window.onload = function(){
	document.LoginForm.ApplyMemberLoginID.focus();
}
</script>
</body>
<?php
include_once('./_footer.php');
include_once('../includes/dbclose.php');
?>