<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "Sub_Layout";

?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
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

if (trim($PageContentCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $PageContentCss;
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
// $PageContent = convertHTML(trim($PageContent));
// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $PageContent = convertHTML(trim($PageContent));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));

} else if($DomainSiteID==8){ //engliseed.kr
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $PageContent = convertHTML(trim($PageContent));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));

} else if($DomainSiteID==9){ //live.engedu.kr
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $PageContent = convertHTML(trim($PageContent));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));

} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $PageContent = convertHTML(trim($PageContent));
	$SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }

echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";
?>


  <div class="main-content pt-90">

    <!-- Section: inner-header -->
    <section class="inner-header divider parallax layer-overlay overlay-white-2" data-bg-img="images/Sub_Visual_2.jpg">
      <div class="container pt-60 pb-60">
        <!-- Section Content -->
        <div class="section-content">
          <div class="row">
            <div class="col-md-12 text-center">
              <h2 class="title TrnTag">회원가입</h2>
              <ol class="breadcrumb text-center text-black mt-10">
                <li><a href="#">Home</a></li>
                <li class="active text-theme-colored TrnTag">회원가입</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

    </section>
	
	<div id="container" data-menu="7" data-sub="2" style="display:none;">
		<div id="nav">
			<div class="inner">
				<!-- 홈 링크 -->
				<div class="title">
					<a href="/"><img src="images/Icon_Home.png"></a>
				</div>
				<!-- // 홈 링크 -->
				<!-- 1차 메뉴 -->
				<div class="dropdown depth1">
					<a href="javascript:;">
						<span></span>
					</a>
					<ul class="mnuList">
						<li><a href="page.php?pagecode=edu_who"><span>EDU WHO</span></a></li>
						<li><a href="page.php?pagecode=trineed"><span>PROGRAM</span></a></li>
						<li><a href="board_list.php?BoardCode=notice"><span>NEWS</span></a></li>
						<li><a href="board_list.php?BoardCode=video"><span>LIBRARY</span></a></li>
						<li><a href="board_list.php?BoardCode=reference"><span>ADMISION</span></a></li>
						<li><a href="reservation.php"><span>CONSULTING</span></a></li>
						<li><a href="mypage.php"><span>MY PAGE</span></a></li>
					</ul>
				</div>
				<!-- // 1차 메뉴 -->
				<!-- 2차 메뉴 -->
				<div class="dropdown depth2">
					<a href="javascript:;">
						<span></span>
					</a>
					<ul class="mnuList">
						<li><a href="mypage.php"><span>MY PAGE</span></a></li>
						<li><a href="member_form.php"><span>내정보 수정</span></a></li>
						<li><a href="login_form.php"><span>로그인</span></a></li>
					</ul>
				</div>
				<!-- // 2차 메뉴 -->
			</div>
		</div>
	</div>	

    <!-- Section:  -->
    <section>
        <div class="container pb-0 pt-40 mb-0">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="text-center Page_Title"><span>[</span> 회원가입 <span>]</span></h3>
                    <div class="text-center Page_Txt"><span>Always as first !</span> Always as last ! </div>
                    <hr>   
               
					<div class="pt-30 pb-40">
						<div class="BoxJoin">
                			<img src="images/IconJoinEnd.png">
							<h3>회원가입이 완료되었습니다.<br>에듀후의 회원이 되신것을 환영합니다.</h3>
						</div>
						<div class="text-center pt-20"><a href="login_form.php" class="Btn_idpw_Search font-18" style="width:130px; height:42px; line-height:42px;">로그인</a></div>
					</div>

            </div>
        </div>
    </section>
    
  </div>


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
</body>

<?php
include_once('./includes/common_footer.php');

if (trim($PageContentJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $PageContentJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
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
</html>
<?php
include_once('./includes/dbclose.php');
?>






