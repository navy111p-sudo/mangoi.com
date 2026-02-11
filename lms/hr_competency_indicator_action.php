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

$Hr_CompetencyIndicatorCate2ID = isset($_REQUEST["Hr_CompetencyIndicatorCate2ID"]) ? $_REQUEST["Hr_CompetencyIndicatorCate2ID"] : "";
$Hr_CompetencyIndicatorID = isset($_REQUEST["Hr_CompetencyIndicatorID"]) ? $_REQUEST["Hr_CompetencyIndicatorID"] : "";
$Hr_CompetencyIndicatorName = isset($_REQUEST["Hr_CompetencyIndicatorName"]) ? $_REQUEST["Hr_CompetencyIndicatorName"] : "";
$Hr_CompetencyIndicatorState = isset($_REQUEST["Hr_CompetencyIndicatorState"]) ? $_REQUEST["Hr_CompetencyIndicatorState"] : "";

if ($Hr_CompetencyIndicatorID==""){

	$Sql = "select ifnull(Max(Hr_CompetencyIndicatorOrder),0) as Hr_CompetencyIndicatorOrder from Hr_CompetencyIndicators";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$Hr_CompetencyIndicatorOrder = $Row["Hr_CompetencyIndicatorOrder"]+1;

	$Sql = " insert into Hr_CompetencyIndicators ( ";
		$Sql .= " Hr_CompetencyIndicatorCate2ID, ";
		$Sql .= " Hr_CompetencyIndicatorName, ";
		$Sql .= " Hr_CompetencyIndicatorRegDateTime, ";
		$Sql .= " Hr_CompetencyIndicatorModiDateTime, ";
		$Sql .= " Hr_CompetencyIndicatorOrder, ";
		$Sql .= " Hr_CompetencyIndicatorState ";
	$Sql .= " ) values ( ";
		$Sql .= " :Hr_CompetencyIndicatorCate2ID, ";
		$Sql .= " :Hr_CompetencyIndicatorName, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :Hr_CompetencyIndicatorOrder, ";
		$Sql .= " :Hr_CompetencyIndicatorState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
	$Stmt->bindParam(':Hr_CompetencyIndicatorName', $Hr_CompetencyIndicatorName);
	$Stmt->bindParam(':Hr_CompetencyIndicatorOrder', $Hr_CompetencyIndicatorOrder);
	$Stmt->bindParam(':Hr_CompetencyIndicatorState', $Hr_CompetencyIndicatorState);
	$Stmt->execute();
	$Hr_CompetencyIndicatorID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = " update Hr_CompetencyIndicators set ";
		$Sql .= " Hr_CompetencyIndicatorName = :Hr_CompetencyIndicatorName, ";
		$Sql .= " Hr_CompetencyIndicatorModiDateTime = now(), ";
		$Sql .= " Hr_CompetencyIndicatorState = :Hr_CompetencyIndicatorState ";
	$Sql .= " where Hr_CompetencyIndicatorID = :Hr_CompetencyIndicatorID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_CompetencyIndicatorName', $Hr_CompetencyIndicatorName);
	$Stmt->bindParam(':Hr_CompetencyIndicatorState', $Hr_CompetencyIndicatorState);
	$Stmt->bindParam(':Hr_CompetencyIndicatorID', $Hr_CompetencyIndicatorID);
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

