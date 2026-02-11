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
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<link href="css/board.css" rel="stylesheet" type="text/css" />   
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


<script type="text/javascript">
function getScreen( url, size ) {
	if(url === null){
		return ""; 
	} 
	size = (size === null) ? "big" : size; 
	var vid; 
	var results;
	results = url.match("[\\?&]v=([^&#]*)");
	vid = ( results === null ) ? url : results[1];
	if(size == "small"){
		return "https://img.youtube.com/vi/"+vid+"/2.jpg"; }
	else {
		return "https://img.youtube.com/vi/"+vid+"/0.jpg"; }
}
</script>

<?
$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchBookGroupID = isset($_REQUEST["SearchBookGroupID"]) ? $_REQUEST["SearchBookGroupID"] : "";
$SearchBookID = isset($_REQUEST["SearchBookID"]) ? $_REQUEST["SearchBookID"] : "";

if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 12;
}


if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}


if ($SearchBookGroupID!=""){
	$ListParam = $ListParam . "&SearchBookGroupID=" . $SearchBookGroupID;
	$AddSqlWhere = $AddSqlWhere . " and B.BookGroupID=$SearchBookGroupID ";
}

if ($SearchBookID!=""){
	$ListParam = $ListParam . "&SearchBookID=" . $SearchBookID;
	$AddSqlWhere = $AddSqlWhere . " and A.BookID=$SearchBookID ";
}

$AddSqlWhere = $AddSqlWhere . " and C.BookGroupView=1 ";
$AddSqlWhere = $AddSqlWhere . " and C.BookGroupState=1 ";
$AddSqlWhere = $AddSqlWhere . " and B.BookView=1 ";
$AddSqlWhere = $AddSqlWhere . " and B.BookState=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.BookVideoView=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.BookVideoState=1 ";

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);


$Sql = "select 
			count(*) as TotalRowCount 
		from BookVideos A 
			inner join Books B on A.BookID=B.BookID 
			inner join BookGroups C on B.BookGroupID=C.BookGroupID 
		where ".$AddSqlWhere."  ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );

$Sql = "
		select 
			A.* 
		from BookVideos A 
			inner join Books B on A.BookID=B.BookID 
			inner join BookGroups C on B.BookGroupID=C.BookGroupID 
		where ".$AddSqlWhere." 
		order by C.BookGroupOrder asc, B.BookOrder asc, A.BookVideoOrder asc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<!-- 헤더(앱) 영역 -->
<header class="header_app_wrap">
    <h1 class="header_app_title TrnTag">레슨 비디오</h1>
    <a href="javascript:window.Exit=true" class="header_app_close header_app_gray"><img src="images/btn_app_close_black.png" class="icon"></a>
</header>

<div class="sub_wrap bg_gray padding_app" style="border:0;">   
    <section class="level_application_wrap">
        <div class="level_application_area">



			<!-- 게시판 관련 -->
			<section class="bbs_wrap">
				<div class="bbs_area">
					<div id="bbs">
						
						<!-- 여기부터 -->
						<div class="bbs_lesson_select_wrap">
							<form name="SearchForm" method="get">
							<select class="bbs_lesson_select" name="SearchBookGroupID" onchange="SearchSubmit(1)">
								<option value="" class="TrnTag">그룹선택</option>
								<?
								$Sql2 = "select A.* from BookGroups A where A.BookGroupView=1 and A.BookGroupState=1 and ( A.BookGroupID<>7 and A.BookGroupID<>8 ) order by A.BookGroupOrder asc";
								$Stmt2 = $DbConn->prepare($Sql2);
								$Stmt2->execute();
								$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
								while($Row2 = $Stmt2->fetch()) {
								?>
								<option value="<?=$Row2["BookGroupID"]?>" <?if ($SearchBookGroupID==$Row2["BookGroupID"]){?>selected<?}?>><?=$Row2["BookGroupName"]?></option>
								<?
								}
								$Stmt2 = null;
								?>
							</select>
							<select class="bbs_lesson_select" name="SearchBookID" onchange="SearchSubmit(2)">
								<option value="" class="TrnTag">교재선택</option>
								<?
								if ($SearchBookGroupID!=""){
									$Sql2 = "select A.* from Books A where A.BookGroupID=".$SearchBookGroupID." and A.BookView=1 and A.BookState=1 order by A.BookOrder asc";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
									while($Row2 = $Stmt2->fetch()) {
								?>
								<option value="<?=$Row2["BookID"]?>" <?if ($SearchBookID==$Row2["BookID"]){?>selected<?}?>><?=$Row2["BookName"]?></option>
								<?
								}
									$Stmt2 = null;
								}
								?>
							</select>
							</form>
						</div>                
						
						<ul class="bbs_lesson_list">
							<?
							$ListCount = 1;
							while($Row = $Stmt->fetch()) {
							
								$BookVideoName = $Row["BookVideoName"];
								$BookVideoType = $Row["BookVideoType"];
								$BookVideoCode = $Row["BookVideoCode"];
								$BookVideoType2 = $Row["BookVideoType2"];
								$BookVideoCode2 = $Row["BookVideoCode2"];
							?>

							 <li>								
								<div class="bbs_lesson_photo_wrap">
                                    <div class="bbs_lesson_photo" id="YoutubeBgImg_<?=$ListCount?>" style="background-image:url()"></div>
                                    <div class="bbs_lesson_photo" id="YoutubeBgImg_<?=$ListCount?>_2" style="background-image:url()"></div>
                                </div>
								<div class="bbs_lesson_inner">
									<h4 class="bbs_lesson_caption"><?=$BookVideoName?></h4>
									<small class="bbs_lesson_date"></small>
                                    <div class="bbs_lesson_btns">
                                        <a href="javascript:OpenLessonVideo(<?=$BookVideoType?>, '<?=$BookVideoCode?>');" class="bbs_lesson_btn">A-Type<img src="images/arrow_go.png" class="arrow"></a>
                                        <a href="javascript:OpenLessonVideo(<?=$BookVideoType2?>, '<?=$BookVideoCode2?>');" class="bbs_lesson_btn">B-Type<img src="images/arrow_go.png" class="arrow"></a>
                                    </div>
									<script>
									var url = "http://www.youtube.com/watch?v=<?=$BookVideoCode?>";
									var imgUrlbig = getScreen(url);
									document.getElementById("YoutubeBgImg_<?=$ListCount?>").style.backgroundImage  = "url('"+imgUrlbig+"')";
                                        
                                    var url = "http://www.youtube.com/watch?v=<?=$BookVideoCode2?>";
									var imgUrlbig = getScreen(url);
									document.getElementById("YoutubeBgImg_<?=$ListCount?>_2").style.backgroundImage  = "url('"+imgUrlbig+"')";
									</script>
								</div>
							</li>


							<?
								$ListCount++;
							}
							$Stmt = null;
							?>

						</ul>
						<!-- 여기까지 -->
						
						<?include_once('./inc_pagination.php');?>

					</div>
				</div>
			</section>

        </div>
    </section>

</div>


<script language="javascript">
var VideoWidth = "720";
var VideoHeight = "464";
var windowWidth = $( window ).width();
// var varUA = navigator.userAgent.toLowerCase();


if(windowWidth < 780) {
	VideoWidth = "360";
	VideoHeight = "256";
}

function OpenLessonVideo(TeacherVideoType, TeacherVideoCode) {
	if (TeacherVideoCode==""){
		$.alert({title: "안내", content: "레슨비디오 준비중 입니다."});
	}else{
		if(/Android|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			//안드로이드
			cordova_iab.OpenYoutube(1, TeacherVideoCode);
//			(userAgent.match(/(iPad)/) /* iOS pre 13 */ || 
  //(navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1) /* iPad OS 13 */);
		} else if( (/iPhone|iPad|iPod/i.test(navigator.userAgent) ) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1) ) {
			//IOS
			var message = {
				command: 'openVideo',
				value: TeacherVideoCode
			};
			window.webkit.messageHandlers.cordova_iab.postMessage(message);
		} else {
			alert("해당 기종은 준비 중입니다.");
		}
		/*
		if ( varUA.indexOf('android') > -1) {
			//안드로이드
			cordova_iab.OpenYoutube(1, TeacherVideoCode);
		} else if ( varUA.indexOf("iphone") > -1||varUA.indexOf("ipad") > -1||varUA.indexOf("ipod") > -1 ) {
			//IOS
			var message = {
				command: 'openVideo',
				value: TeacherVideoCode
			};
			window.webkit.messageHandlers.cordova_iab.postMessage(message);
		} else {
			//아이폰, 안드로이드 외
			alert("해당 기종은 준비 중입니다.");
		}*/
		
	}
}
/*
function OpenLessonVideo(TeacherVideoType, TeacherVideoCode) {

	if (TeacherVideoCode==""){
		$.alert({title: "안내", content: "레슨비디오 준비중 입니다."});
	}else{
		var OpenUrl = "pop_video_player.php?TeacherVideoType="+TeacherVideoType+"&TeacherVideoCode="+TeacherVideoCode;

		$.colorbox({	
			href:OpenUrl
			,width:VideoWidth 
			,height:VideoHeight
			,title:""
			,iframe:true 
			,scrolling:false
			//,onClosed:function(){location.reload(true);}
			//,onComplete:function(){alert(1);}
		}); 
	}
}
*/
</script>

<script>
function SearchSubmit(SearchType){
	if (SearchType==1){
		document.SearchForm.SearchBookID.value = "";
	}
	document.SearchForm.target = "_self";
	document.SearchForm.submit();
}
</script>

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


