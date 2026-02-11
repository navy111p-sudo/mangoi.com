<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');
include_once('./includes/board_config.php');

if (!$AuthRead){
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

<!-- ====   froala_editor   === -->
<!--
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0"/>
-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="./editors/froala_editor/css/froala_editor.css">
<link rel="stylesheet" href="./editors/froala_editor/css/froala_style.css">
<link rel="stylesheet" href="./editors/froala_editor/css/plugins/code_view.css">
<link rel="stylesheet" href="./editors/froala_editor/css/plugins/image_manager.css">
<link rel="stylesheet" href="./editors/froala_editor/css/plugins/image.css">
<link rel="stylesheet" href="./editors/froala_editor/css/plugins/table.css">
<link rel="stylesheet" href="./editors/froala_editor/css/plugins/video.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.css">
<!-- ====   froala_editor   === -->

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


<?php
switch ($BoardCode){
	case "supporting" : //6차산업지원정책
		$inc_board_read = "./inc_board_read_supporting.php";
		break;
	case "example" : //우수사례
		$inc_board_read = "./inc_board_read_example.php";
		break;
	case "advice" : //자문상담
		$inc_board_read = "./inc_board_read_advice.php";
		break;
	case "coaching" : //현장코칭
		$inc_board_read = "./inc_board_read_coaching.php";
		break;
	case "album" : //앨범
		$inc_board_read = "./inc_board_read_album.php";
		break;

	case "mylecture" : //강의
		$inc_board_read = "./inc_board_read_lecture.php";
		break;
	case "manual" : //비디오
		$inc_board_read = "./inc_board_read_video.php";
		break;

	default : //기타 게시판
		$inc_board_read = "./inc_board_read.php";
		break;
}


include_once($inc_board_read);
?>



<script>

var isMobile = {
	Android: function() {
		return navigator.userAgent.match(/Android/i);
	},
	BlackBerry: function() {
		return navigator.userAgent.match(/BlackBerry/i);
	},
	iOS: function() {
		return navigator.userAgent.match(/iPhone|iPad|iPod/i);
	},
	Opera: function() {
		return navigator.userAgent.match(/Opera Mini/i);
	},
	Windows: function() {
		return navigator.userAgent.match(/IEMobile/i);
	},
	any: function() {
		return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
	}
};


if( isMobile.any() ) {
	
	if (document.getElementsByClassName('fr-dib')!=null){
		if (document.getElementsByClassName('fr-dib').length==1){
			document.getElementsByClassName('fr-dib').style.width="100%";
		}else{
			for (ii=0;ii<=document.getElementsByClassName('fr-dib').length-1 ; ii++){
				document.getElementsByClassName('fr-dib')[ii].style.width = "100%";
			}
			
		}
	}				
	
	if (document.getElementsByTagName('iframe')[1].width>320){
		document.getElementsByTagName('iframe')[1].width="320";
		document.getElementsByTagName('iframe')[1].height="180";
	}
	
}



</script>

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


<style>

.outline *{margin:0; padding:0; color:#3c3c3c;font-family:'Noto Sans KR', sans-serif;}
.outline a{text-decoration:none;}
.outline h1{font-size:24px; font-weight:500; position:relative; border-bottom:2px solid #1b478a; padding-bottom:10px; color:#222; padding-left:10px; margin-top:20px;}
.outline h1 span{font-weight:400; font-size:20px;}
.outline h1 div{position:absolute; right:10px; top:12px; font-size:14px;}

.outline .list{overflow:hidden; font-size:16px;}
.outline .list div{float:left; width:49.8%; position:relative; height:50px; line-height:50px; border-bottom:1px dashed #bbb;padding-left:30px; box-sizing:border-box; margin:0 0.1%;}
.outline .list .btn{position:absolute; right:50px; top:10px; display:inline-block; background:#1b478a; width:80px; height:30px; line-height:30px; text-align:center; color:#fff; border-radius:2px; font-size:14px;}

@media only screen and (max-width:992px) {
.outline{width:760px;}
.outline .list{font-size:15px;}
.outline .list > div{padding-left:20px;}
.outline .list .btn{right:25px;}
}
@media only screen and (max-width:768px) {
.outline{width:100%;}
.outline .list{font-size:13px;}
.outline .list > div{padding-left:10px;}
.outline .list .btn{right:12px; width:64px; font-size:12px;}
}
@media only screen and (max-width:640px) {
.outline .list{font-size:16px;}
.outline .list > div{float:none; width:100%; padding-left:25px;}
.outline .list > div:nth-child(2n){width:100%; margin-left:0;}
.outline .list .btn{right:30px; width:80px; height:30px; line-height:30px; font-size:14px;}
}
@media only screen and (max-width:480px) {
.outline .list{font-size:15px;}
.outline .list > div{padding-left:5px;}
.outline .list .btn{right:5px; width:70px; font-size:13px;}
.outline h1{font-size:18px; padding-bottom:6px; padding-left:4px;}
.outline h1 span{font-size:16px;}
.outline h1 div{right:5px; top:5px; font-size:13px;}
}
@media only screen and (max-width:360px) {
.outline .list{font-size:14px;}
.outline .list .btn{width:66px;}
}

.outline .list br{display:none;}
</style>


</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>
