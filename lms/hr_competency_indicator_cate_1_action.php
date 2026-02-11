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

$Hr_CompetencyIndicatorCate1ID = isset($_REQUEST["Hr_CompetencyIndicatorCate1ID"]) ? $_REQUEST["Hr_CompetencyIndicatorCate1ID"] : "";
$Hr_CompetencyIndicatorCate1Name = isset($_REQUEST["Hr_CompetencyIndicatorCate1Name"]) ? $_REQUEST["Hr_CompetencyIndicatorCate1Name"] : "";
$Hr_CompetencyIndicatorCate1State = isset($_REQUEST["Hr_CompetencyIndicatorCate1State"]) ? $_REQUEST["Hr_CompetencyIndicatorCate1State"] : "";


if ($Hr_CompetencyIndicatorCate1ID==""){



	$Sql = "select ifnull(Max(Hr_CompetencyIndicatorCate1Order),0) as Hr_CompetencyIndicatorCate1Order from Hr_CompetencyIndicatorCate1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$Hr_CompetencyIndicatorCate1Order = $Row["Hr_CompetencyIndicatorCate1Order"]+1;



	$Sql = " insert into Hr_CompetencyIndicatorCate1 ( ";
		$Sql .= " Hr_CompetencyIndicatorCate1Name, ";
		$Sql .= " Hr_CompetencyIndicatorCate1RegDateTime, ";
		$Sql .= " Hr_CompetencyIndicatorCate1ModiDateTime, ";
		$Sql .= " Hr_CompetencyIndicatorCate1Order, ";
		$Sql .= " Hr_CompetencyIndicatorCate1State ";
	$Sql .= " ) values ( ";
		$Sql .= " :Hr_CompetencyIndicatorCate1Name, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :Hr_CompetencyIndicatorCate1Order, ";
		$Sql .= " :Hr_CompetencyIndicatorCate1State ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate1Name', $Hr_CompetencyIndicatorCate1Name);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate1Order', $Hr_CompetencyIndicatorCate1Order);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate1State', $Hr_CompetencyIndicatorCate1State);
	$Stmt->execute();
	$Hr_CompetencyIndicatorCate1ID = $DbConn->lastInsertId();
	$Stmt = null;



}else{

	$Sql = " update Hr_CompetencyIndicatorCate1 set ";
		$Sql .= " Hr_CompetencyIndicatorCate1Name = :Hr_CompetencyIndicatorCate1Name, ";
		$Sql .= " Hr_CompetencyIndicatorCate1ModiDateTime = now(), ";
		$Sql .= " Hr_CompetencyIndicatorCate1State = :Hr_CompetencyIndicatorCate1State ";
	$Sql .= " where Hr_CompetencyIndicatorCate1ID = :Hr_CompetencyIndicatorCate1ID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate1Name', $Hr_CompetencyIndicatorCate1Name);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate1State', $Hr_CompetencyIndicatorCate1State);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate1ID', $Hr_CompetencyIndicatorCate1ID);
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

