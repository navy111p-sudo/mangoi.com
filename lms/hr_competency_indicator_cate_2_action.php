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
$Hr_CompetencyIndicatorCate2ID = isset($_REQUEST["Hr_CompetencyIndicatorCate2ID"]) ? $_REQUEST["Hr_CompetencyIndicatorCate2ID"] : "";
$Hr_CompetencyIndicatorCate2Name = isset($_REQUEST["Hr_CompetencyIndicatorCate2Name"]) ? $_REQUEST["Hr_CompetencyIndicatorCate2Name"] : "";
$Hr_CompetencyIndicatorCate2State = isset($_REQUEST["Hr_CompetencyIndicatorCate2State"]) ? $_REQUEST["Hr_CompetencyIndicatorCate2State"] : "";

if ($Hr_CompetencyIndicatorCate2ID==""){

	$Sql = "select ifnull(Max(Hr_CompetencyIndicatorCate2Order),0) as Hr_CompetencyIndicatorCate2Order from Hr_CompetencyIndicatorCate2";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$Hr_CompetencyIndicatorCate2Order = $Row["Hr_CompetencyIndicatorCate2Order"]+1;

	$Sql = " insert into Hr_CompetencyIndicatorCate2 ( ";
		$Sql .= " Hr_CompetencyIndicatorCate1ID, ";
		$Sql .= " Hr_CompetencyIndicatorCate2Name, ";
		$Sql .= " Hr_CompetencyIndicatorCate2RegDateTime, ";
		$Sql .= " Hr_CompetencyIndicatorCate2ModiDateTime, ";
		$Sql .= " Hr_CompetencyIndicatorCate2Order, ";
		$Sql .= " Hr_CompetencyIndicatorCate2State ";
	$Sql .= " ) values ( ";
		$Sql .= " :Hr_CompetencyIndicatorCate1ID, ";
		$Sql .= " :Hr_CompetencyIndicatorCate2Name, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :Hr_CompetencyIndicatorCate2Order, ";
		$Sql .= " :Hr_CompetencyIndicatorCate2State ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate1ID', $Hr_CompetencyIndicatorCate1ID);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate2Name', $Hr_CompetencyIndicatorCate2Name);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate2Order', $Hr_CompetencyIndicatorCate2Order);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate2State', $Hr_CompetencyIndicatorCate2State);
	$Stmt->execute();
	$Hr_CompetencyIndicatorCate2ID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = " update Hr_CompetencyIndicatorCate2 set ";
		$Sql .= " Hr_CompetencyIndicatorCate2Name = :Hr_CompetencyIndicatorCate2Name, ";
		$Sql .= " Hr_CompetencyIndicatorCate2ModiDateTime = now(), ";
		$Sql .= " Hr_CompetencyIndicatorCate2State = :Hr_CompetencyIndicatorCate2State ";
	$Sql .= " where Hr_CompetencyIndicatorCate2ID = :Hr_CompetencyIndicatorCate2ID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate2Name', $Hr_CompetencyIndicatorCate2Name);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate2State', $Hr_CompetencyIndicatorCate2State);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
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

