<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');
include_once('./includes/board_config.php');

if (!$AuthWrite){
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

<!-- ===========================================   froala_editor   =========================================== -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/froala_editor.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/froala_style.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/code_view.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/draggable.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/colors.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/emoticons.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/image_manager.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/image.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/line_breaker.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/table.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/char_counter.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/video.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/fullscreen.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/file.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/quick_insert.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/help.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/third_party/spell_checker.css">
<link rel="stylesheet" href="./editors/froala_editor_3/css/plugins/special_characters.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.css">
<!-- ===========================================   froala_editor   =========================================== -->
 
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
	case "cti" : 
	case "rfid" : 
		$inc_board_form = "./inc_board_form_info.php";
		break;
	case "soft" : 
		$inc_board_form = "./inc_board_form_info_2.php";
		break;
	default : //기타 게시판
		$inc_board_form = "./inc_board_form.php";
		break;
}


include_once($inc_board_form);
?>




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



<!-- ===========================================   froala_editor   =========================================== -->
<style>
#BoardContent {
  width: 81%;
  margin: auto;
  text-align: left;
}
</style>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/mode/xml/xml.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/froala_editor.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/align.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/char_counter.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/code_beautifier.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/code_view.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/colors.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/draggable.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/emoticons.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/entities.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/file.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/font_size.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/font_family.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/fullscreen.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/image.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/image_manager.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/line_breaker.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/inline_style.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/link.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/lists.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/paragraph_format.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/paragraph_style.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/quick_insert.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/quote.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/table.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/save.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/url.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/video.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/help.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/print.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/third_party/spell_checker.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/special_characters.min.js"></script>
<script type="text/javascript" src="./editors/froala_editor_3/js/plugins/word_paste.min.js"></script>

<script>
(function () {
  const editorInstance = new FroalaEditor('#BoardContent', {
	key: "xGE6oB4B3C3A6D6E5fLUQZf1ASFb1EFRNh1Hb1BCCQDUHnA8B6E5B4B1C3I3A1B8A6==",
	enter: FroalaEditor.ENTER_BR,
	heightMin: 300,
	fileUploadURL: './froala_editor_file_upload.php',
	imageUploadURL: './froala_editor_image_upload.php',
	placeholderText: null,
	events: {
	  initialized: function () {
		const editor = this
		this.el.closest('form').addEventListener('submit', function (e) {
		  console.log(editor.$oel.val())
		  e.preventDefault()
		})
	  }
	}
  })
})()


</script>
<!-- ===========================================   froala_editor   =========================================== -->

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>