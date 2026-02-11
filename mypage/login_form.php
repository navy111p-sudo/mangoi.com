<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/member_check.php');


$UseMain = 0;
$UseSub = 0;
$SubCode = "sub_08";

?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="google-signin-client_id" content="950462494416-92ppoda203fvs2ghu0qjr2q592epuqsk.apps.googleusercontent.com">
<?if ($DomainSiteID==5){?>
<title>(주)englishtell</title>
<?}else{?>
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="../uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="../uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<?}?>
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />


<?php
include_once('../includes/common_header.php');

$Sql = "select SubID from Subs where SubCode=:SubCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SubCode', $SubCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$SubID = $Row["SubID"];

if ($UseMain==1){
	$Sql = "select * from Main limit 0,1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MainLayout = $Row["MainLayout"];
	$MainLayoutCss = $Row["MainLayoutCss"];
	$MainLayoutJavascript = $Row["MainLayoutJavascript"];
	list($MainLayoutTop, $MainLayoutBottom) = explode("{{Page}}", $MainLayout);
}else{
	$MainLayoutTop = "";
	$MainLayoutBottom = "";
	$MainLayoutCss = "";
	$MainLayoutJavascript = "";
}


if ($UseSub==1){
	$Sql = "select * from Subs where SubID=:SubID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SubID', $SubID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$SubLayout = $Row["SubLayout"];
	$SubLayoutCss = $Row["SubLayoutCss"];
	$SubLayoutJavascript = $Row["SubLayoutJavascript"];
	list($SubLayoutTop, $SubLayoutBottom) = explode("{{Page}}", $SubLayout);
}else{
	$SubLayoutTop = "";
	$SubLayoutBottom = "";
	$SubLayoutCss = "";
	$SubLayoutJavascript = "";
}


if (trim($MainLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $MainLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

if (trim($SubLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $SubLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}


?>
</head>
<body class="login_body">
<?
include_once('../includes/common_body_top.php');
?>
<?php
$MainLayoutTop = convertHTML(trim($MainLayoutTop));
$SubLayoutTop = convertHTML(trim($SubLayoutTop));
$SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
$MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";
?>


         
<?
$RedirectUrl = isset($_REQUEST["RedirectUrl"]) ? $_REQUEST["RedirectUrl"] : "";
?> 

<div class="sub_wrap">       

    <section class="login_wrap">
        <div class="login_area">
            <h2 class="login_caption">LOG<span class="normal">IN</span></h2>
            <div class="login_caption_text">
				<?if ($DomainSiteID==4){?>
				<trn class="TrnTag">Thomas Mangoi 화상영어에 오신 것을 환영합니다.</trn>
				<?}else if ($DomainSiteID==5){?>
				<trn class="TrnTag">English TELL 화상영어에 오신 것을 환영합니다.</trn>
				<?}?>
			</div>
			<form name="LoginForm" method="post" class="pt-30 pb-30">
				<input type="hidden" id="RedirectUrl" name="RedirectUrl" value="<?=$RedirectUrl?>">
				<div class="login_box">
					<input type="email" id="ApplyMemberLoginID" name="ApplyMemberLoginID" placeholder="ID" onkeyup="FormSubmitEn()" value="<?php if (isset($_COOKIE["RememberMemberID"])){ echo $_COOKIE["RememberMemberID"]; }?>" class="input_login">
					<input type="password" id="ApplyMemberLoginPW" name="ApplyMemberLoginPW" placeholder="PW" onkeyup="FormSubmitEn()" class="input_login">
				</div>
				<a href="javascript:FormSubmit();" class="login_btn TrnTag">로그인</a>
			
				<div class="login_bottom">
					<div class="login_save check_wrap"><input type="checkbox" id="ApplyRememberID" class="input_check" name="ApplyRememberID" <?php if (isset($_COOKIE["RememberMemberID"])){ ?>checked<?php }?>><label class="label TrnTag" for="ApplyRememberID"><span class="bullet_check"></span>아이디 저장</label></div>
					<!--ul class="login_navi">
						<li><a href="member_form_agree.php">회원가입</a><span></span></li>
						<li><a href="search_id.php">아이디 찾기</a><span></span></li>
						<li><a href="search_pw.php">비밀번호 찾기</a></li>
					</ul-->
				</div>
                <!--ul class="login_link">
					<li>
						<a href="javascript:LoginWithGoogle()"><img src="images/btn_google.png" class="btn_google"></a>
					</li>
                    <li>
                        <a id="custom-login-btn" href="javascript:LoginWithKakao()"><img src="images/btn_kakao.png" class="btn_kakao"></a>
                    </li>
                </ul-->
			</form>
        </div>
    </section>

</div>

<!-- google login api -->
<script type="text/javascript">
var GoogleAuth = "";
gapi.load('auth2', function() {
	GoogleAuth = gapi.auth2.init( {
		client_id: '950462494416-92ppoda203fvs2ghu0qjr2q592epuqsk.apps.googleusercontent.com',
		cookie_policy: "none",
		scope: "profile email openid",
		fetch_basic_profile: true
	} );		
});

	function LoginWithGoogle() {
		GoogleAuth.then(function(suc) {
			var Gauth = gapi.auth2.getAuthInstance();
			Gauth.signIn({ // 로그인, select 프롬프트가 보이게끔
				prompt:"select_account"
			}).then(
				function onInit() { // 로그인 성공 시,
					var IsSigned = Gauth.isSignedIn.get();
					if(IsSigned) {
						var Profile = Gauth.currentUser.get().getBasicProfile();

						var Id = Profile.getId();
						var Name = Profile.getName();
						var GivenName = Profile.getGivenName();
						var FamilyName = Profile.getFamilyName();
						var ImageUrl = Profile.getImageUrl();
						var Email = Profile.getEmail();

						CheckAccount(Id, Email, Name, 3);
					} else {
						alert("로그인에 실패하였습니다.");
					}
				},
				function onError() {
					//alert("failed");
				}
			);
		}, function() {
			alert("OnError : ");
		});
	}

</script>
<!-- //google login api -->


<!-- 카카오 API -->
<script type='text/javascript'>
Kakao.init('bec8f62e0063487d792fd833b37984d3');

function LoginWithKakao() {
	// 로그인 창을 띄웁니다.
	Kakao.Auth.loginForm({
		success: function(authObj) {
			Kakao.Auth.getStatusInfo(function callback(statusObj) {
				var KakaoEmail = statusObj["user"]["kakao_account"]["email"];
				var KakaoId = statusObj["user"]["id"];
				var KakaoName = statusObj["user"]["properties"]["nickname"];

				CheckAccount(KakaoId, KakaoEmail, KakaoName, 1);
			});
		},
		fail: function(err) {
			alert("로그인에 실패하였습니다.");
			//alert(JSON.stringify(err));
		}
	});
};

function CheckAccount(Id, Email, Name, Type) {
	//var url = "ajax_check_kakao_account.php";
	var url = "check_sns_account.php?Id="+Id+"&Email="+Email+"&Name="+Name+"&Type="+Type;
	location.href = url;
}

</script>
<!-- //카카오 API -->

<script language="javascript">
$('.sub_visual_navi .one').addClass('active');

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


<?php
echo "\n";
echo $SubLayoutBottom;
echo "\n";
echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('../includes/common_analytics.php');
?>


<?php
include_once('../includes/common_footer.php');


if (trim($SubLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $SubLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}

if (trim($MainLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $MainLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}
?>

</body>
</html>
<?php
include_once('../includes/dbclose.php');
?>







