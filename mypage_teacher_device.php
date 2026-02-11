<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
$DenyGuest = true;
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_10";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
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
<!-- amchart4 -->
<script src="./amcharts4/core.js"></script>
<script src="./amcharts4/charts.js"></script>
<script src="./amcharts4/themes/animated.js"></script>
<!-- amchart4 -->
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
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_04_gumiivyleague)}}"));
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


$LinkLoginDeviceID = isset($_COOKIE["LinkLoginDeviceID"]) ? $_COOKIE["LinkLoginDeviceID"] : "";
?>

<!-- 비밀번호 입력 파일 : mypage_teacher_password.php -->

<div class="sub_wrap">   
    <div class="tab_teacher_wrap">
        <ul class="tab_teacher">
            <li class="one"><a href="mypage_teacher_mode.php">학생모드</a></li>
            <li class="two"><a href="mypage_teacher_dashboard.php">대시보드</a></li>
            <li class="three"><a href="mypage_teacher_device.php">디바이스 설정</a></li>
        </ul>
    </div>
    <div class="sub_title_common_wrap"><h2 class="sub_title_common"><b>디바이스</b> 설정</h2></div>

    <section class="mypage_teacher_wrap">
        <div class="mypage_teacher_area">
            <div class="mypage_teacher_device_box">
                <img src="images/img_computer.png" alt="" class="mypage_teacher_device_img">
				<?php if($LinkLoginDeviceID) { 
					$Sql = "select CenterDeviceName from CenterDevices where CenterDeviceID=:LinkLoginDeviceID";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->bindParam(":LinkLoginDeviceID", $LinkLoginDeviceID);
					$Stmt->execute();
					$Row = $Stmt->fetch();
					$Stmt = null;

					$CenterDeviceName = $Row["CenterDeviceName"];
					?>
                <div class="mypage_teacher_mode_text">현재 컴퓨터 이름은 <b class="color_orange"><?=$CenterDeviceName?></b>입니다.</div>
				<?php } else { ?>
				<div class="mypage_teacher_mode_text">현재 컴퓨터 이름이 <b class="color_orange">설정</b>되지 않았습니다.</div>
				<?php } ?>
                <select class="mypage_teacher_device_select" name="CenterDeviceID" id="CenterDeviceID">
					<option value="">디바이스를 선택하세요.</option>
					<?php

						//  멤버와 타임존을 조인하여 값을 가져올 것. id 가 매칭이 된다면 default 로 지정
						$Sql2 = "select A.* from CenterDevices A where A.CenterID=:_LINK_MEMBER_CENTER_ID_";

						$Stmt2 = $DbConn->prepare($Sql2);
						$Stmt2->bindParam(':_LINK_MEMBER_CENTER_ID_', $_LINK_MEMBER_CENTER_ID_);
						$Stmt2->execute();
						$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
						while($Row2 = $Stmt2->fetch()) {
							$CenterDeviceID = $Row2["CenterDeviceID"];
							$CenterDeviceName = $Row2["CenterDeviceName"];
					?>
					<option value="<?=$CenterDeviceID?>">[<?=$CenterDeviceID?>] <?=$CenterDeviceName?></option>
					<?php
						}
					?>
                </select>
                <a href="javascript:AddDeviceCookie();" class="button_yellow_black">설정하기</a>
            </div>
        </div>
    </section>
</div>

<style>
/** 마이 선생님 탭메뉴 **/
.tab_teacher li.one{border-left:1px solid #e3e3e3;}
.tab_teacher li.two{border-right:0;}
.tab_teacher li.three{border:0; border:2px solid #333; border-bottom:0; position:relative; line-height:39px;}
.tab_teacher li.three:after{content:''; position:absolute; background-color:#fff; bottom:-1px; left:0; right:0; height:1px;}

@media all and (min-width:640px){
.tab_teacher li.three{line-height:43px;}
}
@media all and (min-width:768px){
.tab_teacher li.three{line-height:43px;}
}
@media all and (min-width:1024px){
.tab_teacher li.three{line-height:48px;}
}
@media all and (min-width:1280px){
.tab_teacher li.three{line-height:48px;}
}
</style>

<script language="javascript">
$('.sub_visual_navi .three').addClass('active');

function AddDeviceCookie(CenterDeviceID) {
	var CenterDeviceID = document.getElementById("CenterDeviceID").value;
	setCookie('LinkLoginDeviceID', CenterDeviceID, '.mangoidev.hihome.kr', 8760);
	location.reload(true);
}

// 쿠키 함수 !
function setCookie(name, value, domain, expire) {
	var date = new Date();
	date.setTime(date.getTime() + (expire * 60 * 60 * 1000));
	var expire = date.toGMTString();
	//document.cookie = namae + '=' + value + ';expires=' + date.toUTCString() + ';path=/';
	document.cookie = name + '=' + value+';domain='+domain+'; expires='+expire;
}

function getCookie(name) {
	var value = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
	return value? value[2] : null;
};

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
<script src="js/animatescroll.min.js"></script>
</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>





