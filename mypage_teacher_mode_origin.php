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
$LinkLoginAdminID = isset($_COOKIE["LinkLoginAdminID"]) ? $_COOKIE["LinkLoginAdminID"] : "";
$LinkLoginMemberID = isset($_COOKIE["LinkLoginMemberID"]) ? $_COOKIE["LinkLoginMemberID"] : "";
?>


<div class="sub_wrap">   
    <div class="tab_teacher_wrap">
        <ul class="tab_teacher">
            <li class="one"><a href="mypage_teacher_mode.php">학생모드</a></li>
			<li class="two"><a href="mypage_teacher_password.php?Section=dashboard">대시보드</a></li>
            <!-- <li class="three"><a href="javascript:OpenUpPopUp('device');">디바이스 설정</a></li> -->
			<li class="three"><a href="mypage_teacher_password.php?Section=device">디바이스 설정</a></li>
        </ul>
    </div>
    <div class="sub_title_common_wrap"><h2 class="sub_title_common"><b>학생</b>모드</h2></div>

    <section class="mypage_teacher_wrap">
        <div class="mypage_teacher_area">
            <div class="mypage_teacher_mode_box">
                <img src="images/img_computer.png" alt="" class="mypage_teacher_mode_img">
				<?php if($LinkLoginDeviceID) { 
					$Sql = "select A.CenterDeviceName, B.MemberName from CenterDevices A 
					inner join Members B on B.MemberLoginID=:LinkLoginMemberID
					where CenterDeviceID=:LinkLoginDeviceID";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->bindParam(":LinkLoginMemberID", $LinkLoginMemberID);
					$Stmt->bindParam(":LinkLoginDeviceID", $LinkLoginDeviceID);
					$Stmt->execute();
					$Row = $Stmt->fetch();
					$Stmt = null;

					$MemberName = $Row["MemberName"];
					$CenterDeviceName = $Row["CenterDeviceName"];
					?>
                <div class="mypage_teacher_mode_text">현재 컴퓨터 이름은 <b class="color_orange"><?=$CenterDeviceName?></b>입니다.</div>
					<?php if($LinkLoginAdminID!=$LinkLoginMemberID) { ?>
					<div class="mypage_teacher_mode_wait"><?=$MemberName?>님 로그인 되었습니다.</div>
					<?php } else { ?>
					<div class="mypage_teacher_mode_wait">대기중입니다.</div>
					<?php } ?>
				<?php } else { ?>
				<div class="mypage_teacher_mode_text">현재 컴퓨터 이름이 <b class="color_orange">설정</b>되지 않았습니다.</div>
                <div class="mypage_teacher_mode_wait">대기중입니다.</div>
				<?php } ?>

            </div>
        </div>
    </section>
</div>

<style>
/** 마이 선생님 탭메뉴 **/
.tab_teacher li.one{border:0; border:2px solid #333; border-bottom:0; position:relative; line-height:39px;}
.tab_teacher li.one:after{content:''; position:absolute; background-color:#fff; bottom:-1px; left:0; right:0; height:1px;}

@media all and (min-width:640px) and (max-width:767px){
.tab_teacher li.one{line-height:43px;}
}
@media all and (min-width:768px) and (max-width:1023px){
.tab_teacher li.one{line-height:43px;}
}
@media all and (min-width:1024px) and (max-width:1279px){
.tab_teacher li.one{line-height:48px;}
}
@media all and (min-width:1280px){
.tab_teacher li.one{line-height:48px;}
}
</style>

<script language="javascript">
$('.sub_visual_navi .one').addClass('active');
</script>

<script>

</script>

<link rel="stylesheet" href="./js/colorbox/example2/colorbox.css" />
<script src="./js/colorbox/jquery.colorbox.js"></script>

<script>
// 패스워드 입력창 띄우기
/*
function OpenUpPopUp(section) {
	openurl = "mypage_teacher_password.php";
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}
*/
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

if($ResponseLogoutCookie) {
 ?>
<script></script>
<?php
}

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





