<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
?>
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>
<!-- uikit -->
<link rel="stylesheet" href="bower_components/uikit/css/uikit.almost-flat.min.css"/>
<!-- altair admin login page -->
<link rel="stylesheet" href="assets/css/login_page.min.css" />

<!-- ==============  common.css ============== -->
<link rel="stylesheet" href="css/common.css" />
<!-- ==============  common.css ============== -->
</head>
<body class="login_page login_page_v2" >

<!-- style="background-image: url('images/book-1549589_1920.jpg');background-size:cover;" -->
<div class="uk-container uk-container-center" style="margin-top:100px;">
	<div class="md-card">
		<div class="md-card-content padding-reset">
			<div class="uk-grid uk-grid-collapse">
				<div class="uk-width-large-2-3 uk-hidden-medium uk-hidden-small">
					<div class="login_page_info uk-height-1-1" style="background-image: url('images/school-2761394_1920.jpg')">
						<div class="info_content">
							<h1 class="heading_b">Login</h1>
							Welcome to the admin page!
							<p>
								<a class="md-btn md-btn-success md-btn-small md-btn-wave" href="javascript:void(0)" style="display:none;">More info</a>
							</p>
						</div>
					</div>
				</div>
				<div class="uk-width-large-1-3 uk-width-medium-2-3 uk-container-center">
					<div class="login_page_forms">
						<div id="login_card">
							<div id="login_form">
								<div class="login_heading">
									<!--<div class="user_avatar"></div>-->
<!--                                    if($DomainSiteID == 8) -> 잉글리씨드 로고 출력-->
                                    <?php if (isset($DomainSiteID) && $DomainSiteID == 8): ?>
                                        <img src="../images/logo_engliseed.png" style="margin-top:50px;">
                                    <?php elseif (isset($DomainSiteID) && $DomainSiteID == 9): ?>
                                        <img src="../images/eng-edu-logo.png" style="margin-top:50px;">
                                    <?php else: ?>
                                        <img src="../images/logo_mangoi.png" style="margin-top:50px;">
                                    <?php endif; ?>
								</div>
								<form name="LoginForm" method="post" id="LoginForm" autocomplete="off">
									<div class="uk-form-row">
										<label for="ApplyMemberLoginID">Username</label>
										<input class="md-input" type="email" id="ApplyMemberLoginID" name="ApplyMemberLoginID" value="<?php if (isset($_COOKIE["RememberAdminID"])){ echo $_COOKIE["RememberAdminID"]; }?>" onKeyPress="FormSubmitEn()" placeholder=""/>
									</div>
									<div class="uk-form-row">
										<label for="ApplyMemberLoginPW">Password</label>
										<input class="md-input" type="password" id="ApplyMemberLoginPW" name="ApplyMemberLoginPW" onKeyPress="FormSubmitEn()" placeholder=""/>
									</div>
									<div class="uk-margin-medium-top">
										<a href="javascript:FormSubmit();" class="md-btn md-btn-primary md-btn-block md-btn-large">Sign In</a>
									</div>
									<!--
									<div class="uk-grid uk-grid-width-1-3 uk-grid-small uk-margin-top" data-uk-grid-margin>
										<div><a href="#" class="md-btn md-btn-block md-btn-facebook" data-uk-tooltip="{pos:'bottom'}" title="Sign in with Facebook"><i class="uk-icon-facebook uk-margin-remove"></i></a></div>
										<div><a href="#" class="md-btn md-btn-block md-btn-twitter" data-uk-tooltip="{pos:'bottom'}" title="Sign in with Twitter"><i class="uk-icon-twitter uk-margin-remove"></i></a></div>
										<div><a href="#" class="md-btn md-btn-block md-btn-gplus" data-uk-tooltip="{pos:'bottom'}" title="Sign in with Google+"><i class="uk-icon-google-plus uk-margin-remove"></i></a></div>
									</div>
									-->
									<div class="uk-margin-top">
										<a href="javascript:OpenAdminDemo();" id="login_help_show__" class="uk-float-right" >Administrator Demo</a>
										<span class="icheck-inline">
											<input type="checkbox" name="ApplyRememberID" id="ApplyRememberID"  data-md-icheck  <?php if (isset($_COOKIE["RememberAdminID"])){ ?>checked<?php }?>>
											<label for="ApplyRememberID" class="inline-label">Remember me</label>
										</span>
									</div>
								</form>
							</div>
							<div class="uk-position-relative" id="login_help" style="display: none">
								<button type="button" class="uk-position-top-right uk-close uk-margin-right back_to_login"></button>
								<h2 class="heading_b uk-text-success">Can't log in?</h2>
								<p>Here’s the info to get you back in to your account as quickly as possible.</p>
								<p>First, try the easiest thing: if you remember your password but it isn’t working, make sure that Caps Lock is turned off, and that your username is spelled correctly, and then try again.</p>
								<p>If your password still isn’t working, it’s time to <a href="#" id="password_reset_show">reset your password</a>.</p>
							</div>
							<div id="login_password_reset" style="display: none">
								<button type="button" class="uk-position-top-right uk-close uk-margin-right back_to_login"></button>
								<h2 class="heading_a uk-margin-large-bottom">Reset password</h2>
								<form>
									<div class="uk-form-row">
										<label for="login_email_reset">Your email address</label>
										<input class="md-input" type="text" id="login_email_reset" name="login_email_reset" />
									</div>
									<div class="uk-margin-medium-top">
										<a href="#" class="md-btn md-btn-primary md-btn-block">Reset password</a>
									</div>
								</form>
							</div>
							<div id="register_form" style="display: none">
								<button type="button" class="uk-position-top-right uk-close uk-margin-right back_to_login"></button>
								<h2 class="heading_a uk-margin-medium-bottom">Create an account</h2>
								<form>
									<div class="uk-form-row">
										<label for="register_username">Username</label>
										<input class="md-input" type="text" id="register_username" name="register_username" />
									</div>
									<div class="uk-form-row">
										<label for="register_password">Password</label>
										<input class="md-input" type="password" id="register_password" name="register_password" />
									</div>
									<div class="uk-form-row">
										<label for="register_password_repeat">Repeat Password</label>
										<input class="md-input" type="password" id="register_password_repeat" name="register_password_repeat" />
									</div>
									<div class="uk-form-row">
										<label for="register_email">E-mail</label>
										<input class="md-input" type="text" id="register_email" name="register_email" />
									</div>
									<div class="uk-margin-medium-top">
										<a href="index.html" class="md-btn md-btn-primary md-btn-block md-btn-large">Sign Up</a>
									</div>
								</form>
							</div>
						</div>
						<!--
						<div class="uk-margin-top uk-text-center">
							<a href="#" id="signup_form_show">Create an account</a>
						</div>
						-->
					</div>
				</div>
			</div>
		</div>
	</div>

</div>



<!-- common functions -->
<script src="assets/js/common.min.js"></script>
<!-- uikit functions -->
<script src="assets/js/uikit_custom.min.js"></script>
<!-- altair core functions -->
<script src="assets/js/altair_admin_common.min.js"></script>
<!-- altair login page functions -->
<script src="assets/js/pages/login.min.js"></script>
<script>
	// check for theme
	if (typeof(Storage) !== "undefined") {
		var root = document.getElementsByTagName( 'html' )[0],
			theme = localStorage.getItem("altair_theme");
		if(theme == 'app_theme_dark' || root.classList.contains('app_theme_dark')) {
			root.className += ' app_theme_dark';
		}
	}
</script>

<!-- ==============  common.js ============== -->
<script src="js/common.js"></script>
<!-- ==============  common.js ============== -->

<script language="javascript">
function FormSubmit(){
	obj = document.LoginForm.ApplyMemberLoginID;
	if (obj.value=="") {
		alert('아이디를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.LoginForm.ApplyMemberLoginPW;
	if (obj.value=="") {
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


function OpenAdminDemo(){
	openurl = "../administrator_demo.php";
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"80%"
		,maxWidth:"1024" 
		,maxHeight:"800"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}
</script>



<link rel="stylesheet" href="../js/colorbox/example2/colorbox.css" />
<script src="../js/colorbox/jquery.colorbox.js"></script>
<script>
jQuery(document).ready(function(){
$(document).bind('cbox_open', function() {
	$('html').css({ overflow: 'hidden' });
}).bind('cbox_closed', function() {
	$('html').css({ overflow: '' });
});
});

<?php
include_once('../includes/dbclose.php');
?>
</body>
</html>