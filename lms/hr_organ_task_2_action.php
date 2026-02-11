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

$Hr_OrganTask1ID = isset($_REQUEST["Hr_OrganTask1ID"]) ? $_REQUEST["Hr_OrganTask1ID"] : "";
$Hr_OrganTask2ID = isset($_REQUEST["Hr_OrganTask2ID"]) ? $_REQUEST["Hr_OrganTask2ID"] : "";
$Hr_OrganTask2Name = isset($_REQUEST["Hr_OrganTask2Name"]) ? $_REQUEST["Hr_OrganTask2Name"] : "";
$Hr_OrganLevel = isset($_REQUEST["Hr_OrganLevel"]) ? $_REQUEST["Hr_OrganLevel"] : "";
$Hr_OrganTaskCheckGoalTypeID = isset($_REQUEST["Hr_OrganTaskCheckGoalTypeID"]) ? $_REQUEST["Hr_OrganTaskCheckGoalTypeID"] : "";
$Hr_OrganTaskCheckPerformTypeID = isset($_REQUEST["Hr_OrganTaskCheckPerformTypeID"]) ? $_REQUEST["Hr_OrganTaskCheckPerformTypeID"] : "";
$Hr_OrganTaskCheckAbilityTypeID = isset($_REQUEST["Hr_OrganTaskCheckAbilityTypeID"]) ? $_REQUEST["Hr_OrganTaskCheckAbilityTypeID"] : "";
$Hr_OrganTask2KpiRatio1 = isset($_REQUEST["Hr_OrganTask2KpiRatio1"]) ? $_REQUEST["Hr_OrganTask2KpiRatio1"] : "";
$Hr_OrganTask2KpiRatio2 = isset($_REQUEST["Hr_OrganTask2KpiRatio2"]) ? $_REQUEST["Hr_OrganTask2KpiRatio2"] : "";
$Hr_OrganTask2CompetencyRatio1 = isset($_REQUEST["Hr_OrganTask2CompetencyRatio1"]) ? $_REQUEST["Hr_OrganTask2CompetencyRatio1"] : "";
$Hr_OrganTask2CompetencyRatio2 = isset($_REQUEST["Hr_OrganTask2CompetencyRatio2"]) ? $_REQUEST["Hr_OrganTask2CompetencyRatio2"] : "";
$Hr_OrganTask2CompetencyRatio3 = isset($_REQUEST["Hr_OrganTask2CompetencyRatio3"]) ? $_REQUEST["Hr_OrganTask2CompetencyRatio3"] : "";
$Hr_OrganTask2State = isset($_REQUEST["Hr_OrganTask2State"]) ? $_REQUEST["Hr_OrganTask2State"] : "";

if ($Hr_OrganTask2ID==""){

	$Sql = " insert into Hr_OrganTask2 ( ";
		$Sql .= " Hr_OrganLevel, ";
		$Sql .= " Hr_OrganTask1ID, ";
		$Sql .= " Hr_OrganTask2Name, ";
		$Sql .= " Hr_OrganTaskCheckGoalTypeID, ";
		$Sql .= " Hr_OrganTaskCheckPerformTypeID, ";
		$Sql .= " Hr_OrganTaskCheckAbilityTypeID, ";
		$Sql .= " Hr_OrganTask2KpiRatio1, ";
		$Sql .= " Hr_OrganTask2KpiRatio2, ";
		$Sql .= " Hr_OrganTask2CompetencyRatio1, ";
		$Sql .= " Hr_OrganTask2CompetencyRatio2, ";
		$Sql .= " Hr_OrganTask2CompetencyRatio3, ";
		$Sql .= " Hr_OrganTask2RegDateTime, ";
		$Sql .= " Hr_OrganTask2ModiDateTime, ";
		$Sql .= " Hr_OrganTask2State ";
	$Sql .= " ) values ( ";
		$Sql .= " :Hr_OrganLevel, ";
		$Sql .= " :Hr_OrganTask1ID, ";
		$Sql .= " :Hr_OrganTask2Name, ";
		$Sql .= " :Hr_OrganTaskCheckGoalTypeID, ";
		$Sql .= " :Hr_OrganTaskCheckPerformTypeID, ";
		$Sql .= " :Hr_OrganTaskCheckAbilityTypeID, ";
		$Sql .= " :Hr_OrganTask2KpiRatio1, ";
		$Sql .= " :Hr_OrganTask2KpiRatio2, ";
		$Sql .= " :Hr_OrganTask2CompetencyRatio1, ";
		$Sql .= " :Hr_OrganTask2CompetencyRatio2, ";
		$Sql .= " :Hr_OrganTask2CompetencyRatio3, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :Hr_OrganTask2State ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_OrganLevel', $Hr_OrganLevel);
	$Stmt->bindParam(':Hr_OrganTask1ID', $Hr_OrganTask1ID);
	$Stmt->bindParam(':Hr_OrganTask2Name', $Hr_OrganTask2Name);
	$Stmt->bindParam(':Hr_OrganTaskCheckGoalTypeID', $Hr_OrganTaskCheckGoalTypeID);
	$Stmt->bindParam(':Hr_OrganTaskCheckPerformTypeID', $Hr_OrganTaskCheckPerformTypeID);
	$Stmt->bindParam(':Hr_OrganTaskCheckAbilityTypeID', $Hr_OrganTaskCheckAbilityTypeID);
	$Stmt->bindParam(':Hr_OrganTask2KpiRatio1', $Hr_OrganTask2KpiRatio1);
	$Stmt->bindParam(':Hr_OrganTask2KpiRatio2', $Hr_OrganTask2KpiRatio2);
	$Stmt->bindParam(':Hr_OrganTask2CompetencyRatio1', $Hr_OrganTask2CompetencyRatio1);
	$Stmt->bindParam(':Hr_OrganTask2CompetencyRatio2', $Hr_OrganTask2CompetencyRatio2);
	$Stmt->bindParam(':Hr_OrganTask2CompetencyRatio3', $Hr_OrganTask2CompetencyRatio3);
	$Stmt->bindParam(':Hr_OrganTask2State', $Hr_OrganTask2State);
	$Stmt->execute();
	$Hr_OrganTask2ID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = " update Hr_OrganTask2 set ";
		$Sql .= " Hr_OrganLevel = :Hr_OrganLevel, ";
		$Sql .= " Hr_OrganTask2Name = :Hr_OrganTask2Name, ";
		$Sql .= " Hr_OrganTaskCheckGoalTypeID = :Hr_OrganTaskCheckGoalTypeID, ";
		$Sql .= " Hr_OrganTaskCheckPerformTypeID = :Hr_OrganTaskCheckPerformTypeID, ";
		$Sql .= " Hr_OrganTaskCheckAbilityTypeID = :Hr_OrganTaskCheckAbilityTypeID, ";
		$Sql .= " Hr_OrganTask2KpiRatio1 = :Hr_OrganTask2KpiRatio1, ";
		$Sql .= " Hr_OrganTask2KpiRatio2 = :Hr_OrganTask2KpiRatio2, ";
		$Sql .= " Hr_OrganTask2CompetencyRatio1 = :Hr_OrganTask2CompetencyRatio1, ";
		$Sql .= " Hr_OrganTask2CompetencyRatio2 = :Hr_OrganTask2CompetencyRatio2, ";
		$Sql .= " Hr_OrganTask2CompetencyRatio3 = :Hr_OrganTask2CompetencyRatio3, ";
		$Sql .= " Hr_OrganTask2ModiDateTime = now(), ";
		$Sql .= " Hr_OrganTask2State = :Hr_OrganTask2State ";
	$Sql .= " where Hr_OrganTask2ID = :Hr_OrganTask2ID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_OrganLevel', $Hr_OrganLevel);
	$Stmt->bindParam(':Hr_OrganTask2Name', $Hr_OrganTask2Name);
	$Stmt->bindParam(':Hr_OrganTaskCheckGoalTypeID', $Hr_OrganTaskCheckGoalTypeID);
	$Stmt->bindParam(':Hr_OrganTaskCheckPerformTypeID', $Hr_OrganTaskCheckPerformTypeID);
	$Stmt->bindParam(':Hr_OrganTaskCheckAbilityTypeID', $Hr_OrganTaskCheckAbilityTypeID);
	$Stmt->bindParam(':Hr_OrganTask2KpiRatio1', $Hr_OrganTask2KpiRatio1);
	$Stmt->bindParam(':Hr_OrganTask2KpiRatio2', $Hr_OrganTask2KpiRatio2);
	$Stmt->bindParam(':Hr_OrganTask2CompetencyRatio1', $Hr_OrganTask2CompetencyRatio1);
	$Stmt->bindParam(':Hr_OrganTask2CompetencyRatio2', $Hr_OrganTask2CompetencyRatio2);
	$Stmt->bindParam(':Hr_OrganTask2CompetencyRatio3', $Hr_OrganTask2CompetencyRatio3);
	$Stmt->bindParam(':Hr_OrganTask2State', $Hr_OrganTask2State);
	$Stmt->bindParam(':Hr_OrganTask2ID', $Hr_OrganTask2ID);
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

