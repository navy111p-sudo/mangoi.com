<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');
include_once('./includes/board_config.php');

if (!$AuthList){
	header("Location: login_form.php"); 
	exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/board.css" rel="stylesheet" type="text/css">
<link href="css/sub_style.css" rel="stylesheet" type="text/css">
<?php
include_once('./includes/common_header.php');

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


list($BoardLayoutTop, $BoardLayoutBottom) = explode("{{Board}}", $BoardLayout);



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

if (trim($BoardCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $BoardCss;
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
// $BoardLayoutTop = convertHTML(trim($BoardLayoutTop));
// $BoardLayoutBottom = convertHTML(trim($BoardLayoutBottom));
// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $MainLayoutTop = convertHTML(trim("{{Piece(header_gumiivyleague)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_gumiivyleague)}}"));
    $BoardLayoutTop = convertHTML(trim($BoardLayoutTop));
    $BoardLayoutBottom = convertHTML(trim($BoardLayoutBottom));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_gumiivyleague_bottom)}}")); 
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
    $BoardLayoutTop = convertHTML(trim($BoardLayoutTop));
    $BoardLayoutBottom = convertHTML(trim($BoardLayoutBottom));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.engedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engedu)}}"));
    $BoardLayoutTop = convertHTML(trim($BoardLayoutTop));
    $BoardLayoutBottom = convertHTML(trim($BoardLayoutBottom));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engedu_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));

} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $BoardLayoutTop = convertHTML(trim($BoardLayoutTop));
    $BoardLayoutBottom = convertHTML(trim($BoardLayoutBottom));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }



echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";
echo $BoardLayoutTop;
echo "\n";
?>

<div style="min-height:400px;">
<?php
switch ($BoardCode){
	case "cti" : 
	case "rfid" : 
		$inc_board_list = "./inc_board_list_info.php";
		break;
	case "soft" : 
		$inc_board_list = "./inc_board_list_info_2.php";
		break;
	case "faq" : 
		$inc_board_list = "./inc_board_list_faq.php";
		break;
	case "gallery" : //앨범
		$inc_board_list = "./inc_board_list_album.php";
		break;
	case "video" : //비디오
		$inc_board_list = "./inc_board_list_video.php";
		break;
	default : //기타 게시판
		$inc_board_list = "./inc_board_list.php";
		break;
}


include_once($inc_board_list);
?>
</div>




<?php
echo $BoardLayoutBottom;
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

if (trim($BoardJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $BoardJavascript;
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