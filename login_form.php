<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');

require_once 'lms/vendor/autoload.php';
  
// 구글 로그인 init configuration
$clientID = '295330190065-e47pvh55mpgvtf5ea37qn9t273ugp611.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'https://www.mangoi.co.kr/callback_google.php';
   
// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");


//카카오 로그인
$restAPIKey = "53b552c616b3e023cf1bfb07b152c621"; //본인의 REST API KEY를 입력해주세요
$callbacURI = urlencode("https://mangoi.co.kr/callback_kakao.php"); //콜백 URL을 입력해주세요
$kakaoLoginUrl = "https://kauth.kakao.com/oauth/authorize?client_id=".$restAPIKey."&redirect_uri=".$callbacURI."&response_type=code";
  

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_08";

?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />

<script src="https://apis.google.com/js/platform.js" async defer></script>
<meta name="google-signin-client_id" content="295330190065-e47pvh55mpgvtf5ea37qn9t273ugp611.apps.googleusercontent.com">
<?php
include_once('./includes/common_header.php');

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
<body>
<?
include_once('./includes/common_body_top.php');
?>
<?php
// $MainLayoutTop = convertHTML(trim($MainLayoutTop));
// $SubLayoutTop = convertHTML(trim($SubLayoutTop));
// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $MainLayoutTop = convertHTML(trim("{{Piece(header_gumiivyleague)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_08_gumiivyleague)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_gumiivyleague_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.engedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engedu)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engedu_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));


} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }
echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";
?>


         
<?
$RedirectUrl = isset($_REQUEST["RedirectUrl"]) ? $_REQUEST["RedirectUrl"] : "";
?> 

<?if($DomainSiteID==7){?>
<div class="sub_wrap">       
<section class="login_wrap">
	<div class="login_area">
		<h2 class="login_caption">LOG<span class="normal">IN</span></h2>
		<div class="login_caption_text TrnTag">Welcome 아이비리그에 오신 것을 환영합니다!</div>
		<form name="LoginForm" method="post" class="pt-30 pb-30">
			<input type="hidden" id="RedirectUrl" name="RedirectUrl" value="<?=$RedirectUrl?>">
			<div class="login_box">
				<input type="email" id="ApplyMemberLoginID" name="ApplyMemberLoginID" placeholder="ID" onkeyup="FormSubmitEn()" value="<?php if (isset($_COOKIE["RememberMemberID"])){ echo $_COOKIE["RememberMemberID"]; }?>" class="input_login">
				<input type="password" id="ApplyMemberLoginPW" name="ApplyMemberLoginPW" placeholder="PW" onkeyup="FormSubmitEn()" class="input_login">
			</div>
			<a href="javascript:FormSubmit();" class="login_btn TrnTag">로그인</a>
		
			<div class="login_bottom">
				<div class="login_save check_wrap"><input type="checkbox" id="ApplyRememberID" class="input_check" name="ApplyRememberID" <?php if (isset($_COOKIE["RememberMemberID"])){ ?>checked<?php }?>><label class="label" for="ApplyRememberID"><span class="bullet_check TrnTag"></span>로그인 상태유지</label></div>
				<ul class="login_navi">
					<li><a href="member_form_agree.php" class="TrnTag">회원가입</a><span></span></li>
					<li><a href="search_id.php" class="TrnTag">아이디 찾기</a><span></span></li>
					<li><a href="search_pw.php" class="TrnTag">비밀번호 찾기</a></li>
				</ul>
			</div>
			<br>
			<div class="login_bottom" style="min-height:200px;position:relative;justify-content: center;text-align:center">
				<?php
				// 네이버 로그인 접근토큰 요청
				$client_id = "V20F4sbnagaPauTDXKMh";
				$redirectURI = urlencode("http://www.mangoi.co.kr/callback_member.php");
				$state = "RAMDOM_STATE";
				$apiURL = "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=".$client_id."&redirect_uri=".$redirectURI."&state=".$state;
				?>
				<!--카카오 로그인 버튼 -->
				<div style="display:inline-block">
					<a href="#" onclick="window.open('<?= $kakaoLoginUrl ?>','카카오톡 로그인','width=700, height=800, toolbar=no, menubar=no, scrollbars=no, resizable=yes');return false;"><img src="images/btn_kakao.png" style="width:290px" class="btn_kakao"></a>
				</div>
				<!--네이버 로그인 버튼 -->
				<div style="margin-top:5px;display:inline-block">
					<a href="#" onclick="window.open('<?php echo $apiURL ?>','네이버 로그인','width=700, height=800, toolbar=no, menubar=no, scrollbars=no, resizable=yes');return false;" ><img src="images/naver_login_btn.png" style="width:290px"></a>
				</div>	

				<!-- 구글 로그인 버튼 -->
				<div style="margin-top:5px;display:inline-block">
					<a  href="#" onclick="window.open('<?=$client->createAuthUrl()?>','구글 로그인','width=700, height=800, toolbar=no, menubar=no, scrollbars=no, resizable=yes');return false;" ><img src="images/btn_google.png" class="btn_google"  style="width:290px"></a>
				</div>
				
				<!--
				<ul class="login_link">
					<li>
					<?=$_SERVER['HTTP_USER_AGENT']?>
						<a href="javascript:LoginWithGoogle()"><img src="images/btn_google.png" class="btn_google"></a>
					</li>
					<li>
						<a id="custom-login-btn" href="javascript:LoginWithKakao()"><img src="images/btn_kakao.png" class="btn_kakao"></a>
					</li>
				</ul>
				-->
			</div>	

		</form>
	</div>
</section>
</div>
<?} else if($DomainSiteID==8){?>
    <div class="sub_wrap">
        <section class="login_wrap">
            <div class="login_area">
                <h2 class="login_caption">LOG<span class="normal">IN</span></h2>
                <div class="login_caption_text TrnTag">Welcome 잉글리씨드에 오신 것을 환영합니다!</div>
                <form name="LoginForm" method="post" class="pt-30 pb-30">
                    <input type="hidden" id="RedirectUrl" name="RedirectUrl" value="<?=$RedirectUrl?>">
                    <div class="login_box">
                        <input type="email" id="ApplyMemberLoginID" name="ApplyMemberLoginID" placeholder="ID" onkeyup="FormSubmitEn()" value="<?php if (isset($_COOKIE["RememberMemberID"])){ echo $_COOKIE["RememberMemberID"]; }?>" class="input_login">
                        <input type="password" id="ApplyMemberLoginPW" name="ApplyMemberLoginPW" placeholder="PW" onkeyup="FormSubmitEn()" class="input_login">
                    </div>
                    <a href="javascript:FormSubmit();" class="login_btn TrnTag">로그인</a>

                    <div class="login_bottom">
                        <div class="login_save check_wrap"><input type="checkbox" id="ApplyRememberID" class="input_check" name="ApplyRememberID" <?php if (isset($_COOKIE["RememberMemberID"])){ ?>checked<?php }?>><label class="label" for="ApplyRememberID"><span class="bullet_check TrnTag"></span>로그인 상태유지</label></div>
                        <ul class="login_navi">
                            <li><a href="member_form_agree.php" class="TrnTag">회원가입</a><span></span></li>
                            <li><a href="search_id.php" class="TrnTag">아이디 찾기</a><span></span></li>
                            <li><a href="search_pw.php" class="TrnTag">비밀번호 찾기</a></li>
                        </ul>
                    </div>
                    <br>
                    <div class="login_bottom" style="min-height:200px;position:relative;justify-content: center;text-align:center">
                        <?php
                        // 네이버 로그인 접근토큰 요청
                        $client_id = "V20F4sbnagaPauTDXKMh";
                        $redirectURI = urlencode("http://www.mangoi.co.kr/callback_member.php");
                        $state = "RAMDOM_STATE";
                        $apiURL = "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=".$client_id."&redirect_uri=".$redirectURI."&state=".$state;
                        ?>
                        <!--카카오 로그인 버튼 -->
                        <div style="display:inline-block">
                            <a href="#" onclick="window.open('<?= $kakaoLoginUrl ?>','카카오톡 로그인','width=700, height=800, toolbar=no, menubar=no, scrollbars=no, resizable=yes');return false;"><img src="images/btn_kakao.png" style="width:290px" class="btn_kakao"></a>
                        </div>
                        <!--네이버 로그인 버튼 -->
                        <div style="margin-top:5px;display:inline-block">
                            <a href="#" onclick="window.open('<?php echo $apiURL ?>','네이버 로그인','width=700, height=800, toolbar=no, menubar=no, scrollbars=no, resizable=yes');return false;" ><img src="images/naver_login_btn.png" style="width:290px"></a>
                        </div>

                        <!-- 구글 로그인 버튼 -->
                        <div style="margin-top:5px;display:inline-block">
                            <a  href="#" onclick="window.open('<?=$client->createAuthUrl()?>','구글 로그인','width=700, height=800, toolbar=no, menubar=no, scrollbars=no, resizable=yes');return false;" ><img src="images/btn_google.png" class="btn_google"  style="width:290px"></a>
                        </div>

                        <!--
				<ul class="login_link">
					<li>
					<?=$_SERVER['HTTP_USER_AGENT']?>
						<a href="javascript:LoginWithGoogle()"><img src="images/btn_google.png" class="btn_google"></a>
					</li>
					<li>
						<a id="custom-login-btn" href="javascript:LoginWithKakao()"><img src="images/btn_kakao.png" class="btn_kakao"></a>
					</li>
				</ul>
				-->
                    </div>

                </form>
            </div>
        </section>
    </div>
<?} else if($DomainSiteID==9){?>
    <div class="sub_wrap">
        <section class="login_wrap">
            <div class="login_area">
                <h2 class="login_caption">LOG<span class="normal">IN</span></h2>
                <div class="login_caption_text TrnTag">Welcome 이엔지 화상영어에 오신 것을 환영합니다!</div>
                <form name="LoginForm" method="post" class="pt-30 pb-30">
                    <input type="hidden" id="RedirectUrl" name="RedirectUrl" value="<?=$RedirectUrl?>">
                    <div class="login_box">
                        <input type="email" id="ApplyMemberLoginID" name="ApplyMemberLoginID" placeholder="ID" onkeyup="FormSubmitEn()" value="<?php if (isset($_COOKIE["RememberMemberID"])){ echo $_COOKIE["RememberMemberID"]; }?>" class="input_login">
                        <input type="password" id="ApplyMemberLoginPW" name="ApplyMemberLoginPW" placeholder="PW" onkeyup="FormSubmitEn()" class="input_login">
                    </div>
                    <a href="javascript:FormSubmit();" class="login_btn TrnTag">로그인</a>

                    <div class="login_bottom">
                        <div class="login_save check_wrap"><input type="checkbox" id="ApplyRememberID" class="input_check" name="ApplyRememberID" <?php if (isset($_COOKIE["RememberMemberID"])){ ?>checked<?php }?>><label class="label" for="ApplyRememberID"><span class="bullet_check TrnTag"></span>로그인 상태유지</label></div>
                        <ul class="login_navi">
                            <li><a href="member_form_agree.php" class="TrnTag">회원가입</a><span></span></li>
                            <li><a href="search_id.php" class="TrnTag">아이디 찾기</a><span></span></li>
                            <li><a href="search_pw.php" class="TrnTag">비밀번호 찾기</a></li>
                        </ul>
                    </div>
                    <br>
                    <div class="login_bottom" style="min-height:200px;position:relative;justify-content: center;text-align:center">
                        <?php
                        // 네이버 로그인 접근토큰 요청
                        $client_id = "V20F4sbnagaPauTDXKMh";
                        $redirectURI = urlencode("http://www.mangoi.co.kr/callback_member.php");
                        $state = "RAMDOM_STATE";
                        $apiURL = "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=".$client_id."&redirect_uri=".$redirectURI."&state=".$state;
                        ?>
                        <!--카카오 로그인 버튼 -->
                        <div style="display:inline-block">
                            <a href="#" onclick="window.open('<?= $kakaoLoginUrl ?>','카카오톡 로그인','width=700, height=800, toolbar=no, menubar=no, scrollbars=no, resizable=yes');return false;"><img src="images/btn_kakao.png" style="width:290px" class="btn_kakao"></a>
                        </div>
                        <!--네이버 로그인 버튼 -->
                        <div style="margin-top:5px;display:inline-block">
                            <a href="#" onclick="window.open('<?php echo $apiURL ?>','네이버 로그인','width=700, height=800, toolbar=no, menubar=no, scrollbars=no, resizable=yes');return false;" ><img src="images/naver_login_btn.png" style="width:290px"></a>
                        </div>

                        <!-- 구글 로그인 버튼 -->
                        <div style="margin-top:5px;display:inline-block">
                            <a  href="#" onclick="window.open('<?=$client->createAuthUrl()?>','구글 로그인','width=700, height=800, toolbar=no, menubar=no, scrollbars=no, resizable=yes');return false;" ><img src="images/btn_google.png" class="btn_google"  style="width:290px"></a>
                        </div>

                        <!--
				<ul class="login_link">
					<li>
					<?=$_SERVER['HTTP_USER_AGENT']?>
						<a href="javascript:LoginWithGoogle()"><img src="images/btn_google.png" class="btn_google"></a>
					</li>
					<li>
						<a id="custom-login-btn" href="javascript:LoginWithKakao()"><img src="images/btn_kakao.png" class="btn_kakao"></a>
					</li>
				</ul>
				-->
                    </div>

                </form>
            </div>
        </section>
    </div>
<?}else{?>
<div class="sub_wrap">       

    <section class="login_wrap">
        <div class="login_area">
            <h2 class="login_caption">LOG<span class="normal">IN</span></h2>
            <div class="login_caption_text TrnTag">Welcome 망고아이에 오신 것을 환영합니다!</div>
			<form name="LoginForm" method="post" class="pt-30 pb-30">
				<input type="hidden" id="RedirectUrl" name="RedirectUrl" value="<?=$RedirectUrl?>">
				<div class="login_box">
					<input type="email" id="ApplyMemberLoginID" name="ApplyMemberLoginID" placeholder="ID" onkeyup="FormSubmitEn()" value="<?php if (isset($_COOKIE["RememberMemberID"])){ echo $_COOKIE["RememberMemberID"]; }?>" class="input_login">
					<input type="password" id="ApplyMemberLoginPW" name="ApplyMemberLoginPW" placeholder="PW" onkeyup="FormSubmitEn()" class="input_login">
				</div>
				<a href="javascript:FormSubmit();" class="login_btn TrnTag">로그인</a>
			
				<div class="login_bottom">
					<div class="login_save check_wrap"><input type="checkbox" id="ApplyRememberID" class="input_check" name="ApplyRememberID" <?php if (isset($_COOKIE["RememberMemberID"])){ ?>checked<?php }?>><label class="label" for="ApplyRememberID"><span class="bullet_check TrnTag"></span>로그인 상태유지</label></div>
					<ul class="login_navi">
						<li><a href="member_form_agree.php" class="TrnTag">회원가입</a><span></span></li>
						<li><a href="search_id.php" class="TrnTag">아이디 찾기</a><span></span></li>
						<li><a href="search_pw.php" class="TrnTag">비밀번호 찾기</a></li>
					</ul>
				</div>
				<br>
				<div class="login_bottom" style="min-height:200px;position:relative;justify-content: center;text-align:center">
					<?php
					// 네이버 로그인 접근토큰 요청
					$client_id = "V20F4sbnagaPauTDXKMh";
					$redirectURI = urlencode("http://www.mangoi.co.kr/callback_member.php");
					$state = "RAMDOM_STATE";
					$apiURL = "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=".$client_id."&redirect_uri=".$redirectURI."&state=".$state;
					?>
					<!--카카오 로그인 버튼 -->
					<div style="display:inline-block">
						<a href="#" onclick="window.open('<?= $kakaoLoginUrl ?>','카카오톡 로그인','width=700, height=800, toolbar=no, menubar=no, scrollbars=no, resizable=yes');return false;"><img src="images/btn_kakao.png" style="width:290px" class="btn_kakao"></a>
					</div>
					<!--네이버 로그인 버튼 -->
					<div style="margin-top:5px;display:inline-block">
						<a href="#" onclick="window.open('<?php echo $apiURL ?>','네이버 로그인','width=700, height=800, toolbar=no, menubar=no, scrollbars=no, resizable=yes');return false;" ><img src="images/naver_login_btn.png" style="width:290px"></a>
					</div>	

					<!-- 구글 로그인 버튼 -->
					<div style="margin-top:5px;display:inline-block">
						<a  href="#" onclick="window.open('<?=$client->createAuthUrl()?>','구글 로그인','width=700, height=800, toolbar=no, menubar=no, scrollbars=no, resizable=yes');return false;" ><img src="images/btn_google.png" class="btn_google"  style="width:290px"></a>
					</div>
					
					<!--
					<ul class="login_link">
						<li>
						<?=$_SERVER['HTTP_USER_AGENT']?>
							<a href="javascript:LoginWithGoogle()"><img src="images/btn_google.png" class="btn_google"></a>
						</li>
						<li>
							<a id="custom-login-btn" href="javascript:LoginWithKakao()"><img src="images/btn_kakao.png" class="btn_kakao"></a>
						</li>
					</ul>
					-->
				</div>	

			</form>
        </div>
    </section>
</div>
<?}?>

<!-- 카카오 API -->
<!--
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
-->
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
include_once('./includes/common_analytics.php');
?>


<?php
include_once('./includes/common_footer.php');


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
include_once('./includes/dbclose.php');
?>







