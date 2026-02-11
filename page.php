<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');
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

$PageCode = isset($_REQUEST["PageCode"]) ? $_REQUEST["PageCode"] : "";

// if live.engedu.kr and page_code is payment --> page_code = payment_engedu
if($DomainSiteID==9 && $PageCode=="payment") {
    $PageCode = "payment_engedu";
}

$Sql = "select * from Pages where PageCode=:PageCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':PageCode', $PageCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$UseMain = $Row["UseMain"];
$UseSub = $Row["UseSub"];
$SubID = $Row["SubID"];
$PageLevel = $Row["PageLevel"];
$PageContent = $Row["PageContent"];
$PageContentCss = $Row["PageContentCss"];
$PageContentJavascript = $Row["PageContentJavascript"];


if ($PageLevel<$_LINK_MEMBER_LEVEL_ID_){
?>
<script>
location.href = "login_form.php";
</script>
<?
	//header("Location: login_form.php");
	//exit;
}



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
if($DomainSiteID==7) {
    if ($PageCode == "mangoi") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_01_gumiivyleague)}}"));
        $PageContent = convertHTML(trim("{{Piece(subpage_01_gumiivyleague)}}"));

    } else if ($PageCode == "phi_center") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_01_gumiivyleague)}}"));
        $PageContent = convertHTML(trim("{{Piece(subpage_01_2_phi_center)}}"));
    } else if ($PageCode == "company") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_01_gumiivyleague)}}"));
        $PageContent = convertHTML(trim("{{Piece(subpage_01_4_company)}}"));
    } else if ($PageCode == "level" || $PageCode == "books" || $PageCode == "support_1" || $PageCode == "support_2" || $PageCode == "support_3") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_02_gumiivyleague)}}"));
    } else if ($PageCode == "course" || $PageCode == "payment" || $PageCode == "guide") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_03_gumiivyleague)}}"));
    }
    $MainLayoutTop = convertHTML(trim("{{Piece(header_gumiivyleague)}}"));
    $PageContent = convertHTML(trim($PageContent));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_gumiivyleague_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    if ($PageCode == "mangoi") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_01_engliseed)}}"));
        $PageContent = convertHTML(trim("{{Piece(subpage_01_engliseed)}}"));

    } else if ($PageCode == "phi_center") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_01_engliseed)}}"));
        $PageContent = convertHTML(trim("{{Piece(subpage_01_2_phi_center)}}"));
    } else if ($PageCode == "company") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_01_engliseed)}}"));
        $PageContent = convertHTML(trim("{{Piece(subpage_01_4_company)}}"));
    } else if ($PageCode == "level" || $PageCode == "books" || $PageCode == "support_1" || $PageCode == "support_2" || $PageCode == "support_3") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_02_engliseed)}}"));
    } else if ($PageCode == "course" || $PageCode == "payment" || $PageCode == "guide") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_03_engliseed)}}"));
    }

    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

    $PageContent = str_replace("logo_gumiivyleague.png", "og_logo_engliseed.png", $PageContent);
    $PageContent = str_replace("아이비리그", "잉글리씨드", $PageContent);

} else if($DomainSiteID==9){ //live.engedu.kr
    if ($PageCode == "mangoi") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_01_engedu)}}"));
        $PageContent = convertHTML(trim("{{Piece(subpage_01_engedu)}}"));

    } else if ($PageCode == "phi_center") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_01_engedu)}}"));
        $PageContent = convertHTML(trim("{{Piece(subpage_01_2_phi_center)}}"));
    } else if ($PageCode == "company") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_01_engedu)}}"));
        $PageContent = convertHTML(trim("{{Piece(subpage_01_4_company)}}"));
    } else if ($PageCode == "level" || $PageCode == "books" || $PageCode == "support_1" || $PageCode == "support_2" || $PageCode == "support_3") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_02_engedu)}}"));
    } else if ($PageCode == "course" || $PageCode == "payment" || $PageCode == "guide") {
        $SubLayoutTop = convertHTML(trim("{{Piece(sub_03_engedu)}}"));
    }

    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engedu)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engedu_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));

    $PageContent = str_replace("logo_gumiivyleague.png", "og_logo_engedu.png", $PageContent);
    $PageContent = str_replace("아이비리그", "이엔지 화상영어", $PageContent);


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
echo $PageContent;
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

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>
