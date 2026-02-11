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

$MemberPointTypeID = isset($_REQUEST["MemberPointTypeID"]) ? $_REQUEST["MemberPointTypeID"] : "";
$MemberPoint = isset($_REQUEST["MemberPoint"]) ? $_REQUEST["MemberPoint"] : "";
$MemberPointTypeType = isset($_REQUEST["MemberPointTypeType"]) ? $_REQUEST["MemberPointTypeType"] : "";
$MemberPointTypeMethod = isset($_REQUEST["MemberPointTypeMethod"]) ? $_REQUEST["MemberPointTypeMethod"] : "";
$MemberPointTypeName = isset($_REQUEST["MemberPointTypeName"]) ? $_REQUEST["MemberPointTypeName"] : "";
$MemberPointTypeText = isset($_REQUEST["MemberPointTypeText"]) ? $_REQUEST["MemberPointTypeText"] : "";



$Sql = " update MemberPointNewTypes set ";
	$Sql .= " MemberPoint = :MemberPoint, ";
	$Sql .= " MemberPointTypeType = :MemberPointTypeType, ";
	$Sql .= " MemberPointTypeMethod = :MemberPointTypeMethod, ";
	$Sql .= " MemberPointTypeName = :MemberPointTypeName, ";
	$Sql .= " MemberPointTypeText = :MemberPointTypeText ";
$Sql .= " where MemberPointTypeID = :MemberPointTypeID ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberPoint', $MemberPoint);
$Stmt->bindParam(':MemberPointTypeType', $MemberPointTypeType);
$Stmt->bindParam(':MemberPointTypeMethod', $MemberPointTypeMethod);
$Stmt->bindParam(':MemberPointTypeName', $MemberPointTypeName);
$Stmt->bindParam(':MemberPointTypeText', $MemberPointTypeText);
$Stmt->bindParam(':MemberPointTypeID', $MemberPointTypeID);
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
//parent.$.fn.colorbox.close();
parent.location.reload();
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

