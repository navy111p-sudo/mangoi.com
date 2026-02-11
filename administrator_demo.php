<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용  
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_04";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />

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

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="./js/jquery-confirm.min.js"></script>
<link href="./css/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>
</head>
<body style="background-color:#fcf9f0;">
<?php
// $MainLayoutTop = convertHTML(trim($MainLayoutTop));
// $SubLayoutTop = convertHTML(trim($SubLayoutTop));
// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $MainLayoutTop = convertHTML(trim("{{Piece(header_gumiivyleague)}}"));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
	$SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.engedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
	$SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));

} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }

echo "\n";
//echo $MainLayoutTop;
echo "\n";
//echo $SubLayoutTop;
echo "\n";
?>


<?
$TempPw = isset($_REQUEST["TempPw"]) ? $_REQUEST["TempPw"] : "";

if ($TempPw==""){
?>
<div class="sub_wrap bg_gray padding_app" style="border:0;margin-top:-60px;">   
    <section class="level_application_wrap">
        <div class="level_application_area" style="padding:20px;">

			<h3 class="caption_underline" style="margin-top:30px;">로그인</h3>


			<form name="LoginForm" method="post">
			<input type="password" name="TempPw" id="TempPW" style="height:50px;width:100%;margin-top:100px;">
			<div style="background-color:#888888;color:#ffffff;margin-top:30px;height:50px;text-align:center;line-height:50px;cursor:pointer;" onclick="FormSubmit()">로그인</div>
			</form>

        </div>
    </section>
</div>

<script>
function FormSubmit(){
	obj = document.LoginForm.TempPw;
	if (obj.value=="") {
		alert('비밀번호를 입력하세요.');
		obj.focus();
		return;
	}

	document.LoginForm.action = "administrator_demo.php";
	document.LoginForm.submit();
}
</script>

<?
}else if ($TempPw!="" && $TempPw!="0505"){
?>
<script>
alert("비밀번호가 맞지 않습니다.");
location.href = "administrator_demo.php";
</script>
<?
}else{


	$Sql = "
		select 
			A.OnlineSiteShVersionDemo
		from OnlineSites A
		where
			A.OnlineSiteID=1
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$OnlineSiteShVersionDemo = $Row["OnlineSiteShVersionDemo"];

	$ShLanguage = "ko";//사용안함

?>

<!--- api post form -->
<div style="display:none;">
    <!-- <form name="ShClassForm" id="ShClassForm" action="http://180.150.230.195/sso/type1.do" method="POST"> -->
    <form name="ShClassForm" id="ShClassForm" action="https://www.mangoiclass.co.kr:8080/sso/type1.do" method="POST">
        <input type="text" name="userid" value="" />
        <input type="text" name="username" value="" />
        <input type="text" name="usertype" value="" />
        <input type="text" name="remote" value="1" />
        <input type="text" name="confcode" value="" />
        <input type="text" name="conftype" value="2" />
    </form>

	<form id="openJoinForm" data-mv-api="openJoin">
	<article>
		<div class="body">
			<div class="input-section">
				<input type="text" name="roomCode" value=""> <!-- 멀티룸 코드 -->
				<input type="text" name="template" value="1"> <!-- 템플릿 번호 -->
				<input type="text" name="title" value=""> <!-- 멀티룸 제목 -->
				<input type="text" name="openOption" value="0">
				<input type="text" name="joinUserType" value=""> <!-- 입장 사용자 타입 -->
				<input type="text" name="userId" value=""> <!-- 사용자 아이디 -->
				<input type="text" name="userName" value=""> <!-- 사용자 이름 -->
				<input type="text" name="roomOption" value=""> <!-- 멀티룸 옵션 -->
				<input type="text" name="extraMsg" value=""> <!-- 확장 메시지 -->
			</div>
		</div>
	</article>
	</form>
</div>


<div class="sub_wrap bg_gray padding_app" style="border:0;margin-top:-60px;">   
    <section class="level_application_wrap">
        <div class="level_application_area" style="padding:20px;">

			<h3 class="caption_underline" style="margin-top:30px;">망고아이 수업 DEMO</h3>
		


			<table class="level_reserve_table">
				<tr>
					<th>Democlass 1</th>
					<th>Democlass 2</th>
					<th>Democlass 3</th>
				</tr>
				<tr>
					<td class="radio_wrap reset" style="text-align:center;">
						<?	
						$CommonShClassCode = "37_20101101_20_10";
						$TeacherName = "Teacher01";
						$StudentName = "Student01_0";
						?>
						
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 1, '<?=$TeacherName?>', '<?=$TeacherName?>');">Teacher</a>
						<br>
						<?for($i=1; $i<=15; $i++) {?>
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 0, '<?=$StudentName.$i?>', '<?=$StudentName.$i?>');">Student0<?=$i?></a>
						<br>
						<?}?>
					</td>
					<td class="radio_wrap reset" style="text-align:center;">
						<?	
						$CommonShClassCode = "37_20111101_20_10";
						$TeacherName = "Teacher02";
						$StudentName = "Student02_0";
						?>
						
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 1, '<?=$TeacherName?>', '<?=$TeacherName?>');">Teacher</a>
						<br>
						<?for($i=1; $i<=15; $i++) {?>
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 0, '<?=$StudentName.$i?>', '<?=$StudentName.$i?>');">Student0<?=$i?></a>
						<br>
						<?}?>
					</td>
					<td class="radio_wrap reset" style="text-align:center;">
						<?	
						$CommonShClassCode = "37_20121101_20_10";
						$TeacherName = "Teacher03";
						$StudentName = "Student03_0";
						?>
						
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 1, '<?=$TeacherName?>', '<?=$TeacherName?>');">Teacher</a>
						<br>
						<?for($i=1; $i<=15; $i++) {?>
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 0, '<?=$StudentName.$i?>', '<?=$StudentName.$i?>');">Student0<?=$i?></a>
						<br>
						<?}?>
					</td>
				</tr>

				<tr>
					<th>Democlass 4</th>
					<th>Democlass 5</th>
					<th>Democlass 6</th>
				</tr>
				<tr>
					<td class="radio_wrap reset" style="text-align:center;" valign="top">
						<?	
						$CommonShClassCode = "37_20131101_20_10";
						$TeacherName = "Teacher04";
						$StudentName = "Student04_0";
						?>
						
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 1, '<?=$TeacherName?>', '<?=$TeacherName?>');">Teacher</a>
						<br>
						<?for($i=1; $i<=15; $i++) {?>
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 0, '<?=$StudentName.$i?>', '<?=$StudentName.$i?>');">Student0<?=$i?></a>
						<br>
						<?}?>
					</td>
					<td class="radio_wrap reset" style="text-align:center;" valign="top">
						<?	
						$CommonShClassCode = "37_20141101_20_10";
						$TeacherName = "Teacher05";
						$StudentName = "Student05_";
						?>
						
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 1, '<?=$TeacherName?>', '<?=$TeacherName?>');">Teacher</a>
						<br>
						<?for($i=1; $i<=15; $i++) {?>
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 0, '<?=$StudentName.substr("0".$i,-2)?>', '<?=$StudentName.substr("0".$i,-2)?>');">Student<?=substr("0".$i,-2)?></a>
						<br>
						<?}?>
					</td>
					<td class="radio_wrap reset" style="text-align:center;" valign="top">
						<?	
						$CommonShClassCode = "37_20151101_20_10";
						$TeacherName = "Teacher06";
						$StudentName = "Student06_";
						?>
						
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 1, '<?=$TeacherName?>', '<?=$TeacherName?>');">Teacher</a>
						<br>
						<?for($i=1; $i<=15; $i++) {?>
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 0, '<?=$StudentName.substr("0".$i,-2)?>', '<?=$StudentName.substr("0".$i,-2)?>');">Student<?=substr("0".$i,-2)?></a>
						<br>
						<?}?>
					</td>
				</tr>

				<tr>
					<th>Democlass 7</th>
					<th>Democlass 8</th>
					<th>Democlass 9</th>
				</tr>
				<tr>
					<td class="radio_wrap reset" style="text-align:center;" valign="top">
						<?	
						$CommonShClassCode = "37_20161101_20_10";
						$TeacherName = "Teacher07";
						$StudentName = "Student07_0";
						?>
						
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 1, '<?=$TeacherName?>', '<?=$TeacherName?>');">Teacher</a>
						<br>
						<?for($i=1; $i<=15; $i++) {?>
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 0, '<?=$StudentName.$i?>', '<?=$StudentName.$i?>');">Student0<?=$i?></a>
						<br>
						<?}?>
					</td>
					<td class="radio_wrap reset" style="text-align:center;" valign="top">
						<?	
						$CommonShClassCode = "37_20171101_20_10";
						$TeacherName = "Teacher08";
						$StudentName = "Student08_";
						?>
						
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 1, '<?=$TeacherName?>', '<?=$TeacherName?>');">Teacher</a>
						<br>
						<?for($i=1; $i<=15; $i++) {?>
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 0, '<?=$StudentName.substr("0".$i,-2)?>', '<?=$StudentName.substr("0".$i,-2)?>');">Student<?=substr("0".$i,-2)?></a>
						<br>
						<?}?>
					</td>
					<td class="radio_wrap reset" style="text-align:center;" valign="top">
						<?	
						$CommonShClassCode = "37_20181101_20_10";
						$TeacherName = "Teacher09";
						$StudentName = "Student09_";
						?>
						
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 1, '<?=$TeacherName?>', '<?=$TeacherName?>');">Teacher</a>
						<br>
						<?for($i=1; $i<=15; $i++) {?>
						<a href="javascript:OpenClassSh('<?=$CommonShClassCode?>', 0, '<?=$StudentName.substr("0".$i,-2)?>', '<?=$StudentName.substr("0".$i,-2)?>');">Student<?=substr("0".$i,-2)?></a>
						<br>
						<?}?>
					</td>
				</tr>

			</table>


        </div>
    </section>
</div>

<script src="/js/mvapi.min.js"></script>

<div style="display:none;">
    <!-- <form name="ShClassForm" id="ShClassForm" action="http://180.150.230.195/sso/type1.do" method="POST"> -->
    <form name="ShClassForm" id="ShClassForm" action="https://www.mangoiclass.co.kr:8080/sso/type1.do" method="POST">
        <input type="text" name="userid" value="" />
        <input type="text" name="username" value="" />
        <input type="text" name="usertype" value="" />
        <input type="text" name="remote" value="1" />
        <input type="text" name="confcode" value="" />
        <input type="text" name="conftype" value="2" />
    </form>

    <form name="CiClassForm" id="CiClassForm" method="POST">
        <input type="text" name="ClassID" id="ClassID" value="">
        <input type="text" name="ClassName" id="ClassName" value="">
        <input type="text" name="MemberType" id="MemberType" value="">
    </form>
</div>


<script>
var OnlineSiteShVersion = <?=$OnlineSiteShVersionDemo?>;

function OpenClassSh(CommonShClassCode, MemberType, MemberName, MemberLoginID) {

	OnlineSiteShVersion=1;

	if(OnlineSiteShVersion==1) {

		ShLanguage = "ko";
		if(MemberType==0) {
			MemberType = 22;
			ShLanguage = "ko";
		} else {
			MemberType = 21;
			ShLanguage = "en";
		}
		
		MvApi.defaultSettings({
			debug: false,
			// tcps: {key: 'MTgwLjE1MC4yMzAuMTk1OjcwMDE'},
			tcps: {key: 'MTIxLjE3MC4xNjQuMjMxOjcwMDE'},
			installPagePopup: "popup",
			company: {code: 2, authKey: '1577840400'},
			//web: {url: 'http://180.150.230.195:8080'},
			web: {url: 'https://www.mangoiclass.co.kr:8080'},
			
			// 클라이언트 설정 정보
			client: {
				// 암호화 사용 여부 - 유효성 검사를 수행하지 않는다.
				encrypt: false,
				// Windows Client 설정
				windows: {
					// 프로그램 이름
					product: 'BODA'
				}, 
				// Mobile Client 설정
				mobile: {
					// 스토어 배포 방식. true: Store 배포, false: 사설 배포
					store: true, 
					// 스킴 이름
					scheme: 'cloudboda',
					// 패키지 이름
					packagename: 'zone.cloudboda',
				},
				// Mac Client 설정 - V7.3.0
				mac: {
					// 스토어 배포 방식. true: Store 배포, false: 사설 배포 - V7.3.1
					store: false, 
					// 스킴 이름
					scheme: 'cloudboda',
					// 패키지 이름
					packagename: 'zone.cloudboda',
				},
				// 사용언어 - 없으면 한국어
				language: '<?=$ShLanguage?>',
				// 테마 - 클라이언트의 테마 코드 값 - v7.1.3
				theme: 3,
				// 버튼 타입 - 버튼을 표시하는 방식 - v7.1.3
				btnType: 1,
				// 어플리케이션 모드 - 회의,교육 등 동작 모드 설정 - v7.1.4
				appMode: 2
			},
			

		});

		CommonShClassCode = "000002" + CommonShClassCode;//망고아이 코드로 변경



		$('input[name=userId]').val(MemberLoginID);
		//$('input[name=title]').val("망고아이 수업");
		$('input[name=title]').val(CommonShClassCode);
		$('input[name=userName]').val(MemberName);
		$('input[name=joinUserType]').val(MemberType);
		$('input[name=roomCode]').val(CommonShClassCode);


		// 기능 실행 버큰 클릭 시 처리
		$('form[data-mv-api]').submit(function(){
			var $this = $(this);
			var api = $this.data('mvApi');
			
			// 요청 메시지 정보 설정
			var requestMsg = {};
			var parameters = $this.serializeArray();
			$.each(parameters, function(index, parameter){
				requestMsg[parameter.name] = parameter.value;
			})
					
			// API 호출
			MvApi[api](
					// 요청메시지
					requestMsg,
					// 성공 callback
					function(){
						console.log('success.');
					},
					// 오류 callback
					function(errorCode, reason){
						//console.error('error.', errorCode, reason);
						//alert('error :' + errorCode +" / "+ reason);
					}
			);
			return false;
		});

		$('form[data-mv-api]').submit();

	}else{
		var FormData = document.getElementById("ShClassForm");
		FormData.userid.value = MemberLoginID;
		FormData.username.value = MemberName;
		FormData.usertype.value = MemberType;  // 강사,학생
		FormData.confcode.value = CommonShClassCode;
		
		var newwin = window.open("", "newwin_"+CommonShClassCode, "toolbar=no,scrollbars=no,resizable=no,top=100,left=500,width=600,height=600");
		FormData.target = "newwin_"+CommonShClassCode;
		FormData.submit();
	}
}
</script>

<?
}
?>


<?php
echo "\n";
//echo $SubLayoutBottom;
echo "\n";
//echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('./includes/common_analytics.php');
?>


<?php
//include_once('./includes/common_footer.php');

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


<!-- ====    kendo -->
<link href="./kendo/styles/kendo.common.min.css" rel="stylesheet">
<link href="./kendo/styles/kendo.default.min.css" rel="stylesheet">
<script src="./kendo/js/kendo.web.min.js"></script>
<!-- ====    kendo   === -->


<!-- ====   Color Box -->
<?
$ColorBox = isset($ColorBox) ? $ColorBox : "";
if ($ColorBox==""){
	$ColorBox = "example2";
}
?>
<link rel="stylesheet" href="../js/colorbox/<?=$ColorBox?>/colorbox.css" />
<script src="../js/colorbox/jquery.colorbox.js"></script>
<script>
jQuery(document).ready(function(){
$(document).bind('cbox_open', function() {
    $('html').css({ overflow: 'hidden' });
}).bind('cbox_closed', function() {
    $('html').css({ overflow: '' });
});
});
</script>
<!-- ====   Color Box   === -->

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>
