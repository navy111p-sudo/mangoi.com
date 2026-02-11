<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');
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

<?php
include_once('./includes/common_header.php');
?>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="./js/jquery-confirm.min.js"></script>
<link href="./css/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<?
$Unit = isset($_REQUEST["Unit"]) ? $_REQUEST["Unit"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";

//================= 로그 ======================
$Sql2 = "insert into ClassBookScanViewLogs (
				ClassID,
				ClassBookType,
				ClassBookScanViewLogDateTime

	) values (
				:ClassID,
				1,
				now()
	)";

$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':ClassID', $ClassID);
$Stmt2->execute();
$Stmt2 = null;
//================= 로그 ======================
?>
<header class="header_app_wrap">
    <h1 class="header_app_title">학습교재보기</h1>
    <a href="javascript:window.Exit=true" class="header_app_close header_app_gray"><img src="images/btn_app_close_black.png" class="icon"></a>
</header>
<div class="sub_wrap bg_gray padding_app" style="border:0;">
    <section class="mypage_wrap">
        <div class="mypage_area">
		<style>
		.iframe100 {
		  display: block;
		  border: none;
		  height: 100vh;
		  width: 100vw;
		}
		</style>
		<iframe id="myframe" src="" class="iframe100"></iframe>
        </div>
    </section>

</div>


<script>
function OpenWebook() {
    url = "ajax_set_book_scan_view_logs.php";

    $.ajax(url,{    
		data: {
			ClassID: "<?=$ClassID?>",
			ClassBookType: 1
		},
		success: function() {

			var StrContentType = "일반교재";

			$.post( "./webook/_get_unit_content.php", { content_type:"학생", MemberLoginID: "<?=$_LINK_MEMBER_LOGIN_ID_?>", unit_id:"<?=$Unit?>", api_extension:'', width:"100%", height: "100%", unit_contents_type:StrContentType })
			.done(function( data ) {

				var iframe = data;
				var myFrame = $("#myframe").contents().find('body');
				myFrame.html(iframe);
			}
		},
		error: function() {
			alert("ERROR!");
		}
    });
}

window.onload = function(){
	OpenWebook();
}
</script>
</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>








