<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
?>
<link rel="stylesheet" type="text/css" href="css/common.css" />
</head>
</body>
<?

$err_num = 0;
$err_msg = "";

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$TeacherMessageType = isset($_REQUEST["TeacherMessageType"]) ? $_REQUEST["TeacherMessageType"] : "";
$TeacherMessageText = isset($_REQUEST["TeacherMessageText"]) ? $_REQUEST["TeacherMessageText"] : "";
$RequestMemberID = $_LINK_ADMIN_ID_;

$Sql = " insert into TeacherMessages ( ";
	$Sql .= " TeacherMessageType, ";
	$Sql .= " MemberID, ";
	$Sql .= " RequestMemberID, ";
	$Sql .= " TeacherMessageText, ";
	$Sql .= " TeacherMessageRegDateTime, ";
	$Sql .= " TeacherMessageModiDateTime ";
$Sql .= " ) values ( ";
	$Sql .= " :TeacherMessageType, ";
	$Sql .= " :MemberID, ";
	$Sql .= " :RequestMemberID, ";
	$Sql .= " :TeacherMessageText, ";
	$Sql .= " now(), ";
	$Sql .= " now() ";
$Sql .= " ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherMessageType', $TeacherMessageType);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':RequestMemberID', $RequestMemberID);
$Stmt->bindParam(':TeacherMessageText', $TeacherMessageText);
$Stmt->execute();
$Stmt = null;
?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>
parent.$.fn.colorbox.close();
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

