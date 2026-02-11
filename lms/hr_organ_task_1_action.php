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

$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$Hr_OrganTask1ID = isset($_REQUEST["Hr_OrganTask1ID"]) ? $_REQUEST["Hr_OrganTask1ID"] : "";
$Hr_OrganTask1Name = isset($_REQUEST["Hr_OrganTask1Name"]) ? $_REQUEST["Hr_OrganTask1Name"] : "";
$Hr_OrganTask1State = isset($_REQUEST["Hr_OrganTask1State"]) ? $_REQUEST["Hr_OrganTask1State"] : "";


if ($Hr_OrganTask1ID==""){

	$Sql = " insert into Hr_OrganTask1 ( ";
		$Sql .= " CenterID, ";
		$Sql .= " Hr_OrganTask1Name, ";
		$Sql .= " Hr_OrganTask1RegDateTime, ";
		$Sql .= " Hr_OrganTask1ModiDateTime, ";
		$Sql .= " Hr_OrganTask1State ";
	$Sql .= " ) values ( ";
		$Sql .= " :CenterID, ";
		$Sql .= " :Hr_OrganTask1Name, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :Hr_OrganTask1State ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':Hr_OrganTask1Name', $Hr_OrganTask1Name);
	$Stmt->bindParam(':Hr_OrganTask1State', $Hr_OrganTask1State);
	$Stmt->execute();
	$Hr_OrganTask1ID = $DbConn->lastInsertId();
	$Stmt = null;



}else{

	$Sql = " update Hr_OrganTask1 set ";
		$Sql .= " Hr_OrganTask1Name = :Hr_OrganTask1Name, ";
		$Sql .= " Hr_OrganTask1ModiDateTime = now(), ";
		$Sql .= " Hr_OrganTask1State = :Hr_OrganTask1State ";
	$Sql .= " where Hr_OrganTask1ID = :Hr_OrganTask1ID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_OrganTask1Name', $Hr_OrganTask1Name);
	$Stmt->bindParam(':Hr_OrganTask1State', $Hr_OrganTask1State);
	$Stmt->bindParam(':Hr_OrganTask1ID', $Hr_OrganTask1ID);
	$Stmt->execute();
	$Stmt = null;

}
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

