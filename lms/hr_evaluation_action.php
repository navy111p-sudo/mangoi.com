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

$Hr_EvaluationID = isset($_REQUEST["Hr_EvaluationID"]) ? $_REQUEST["Hr_EvaluationID"] : "";
$Hr_EvaluationTypeID = isset($_REQUEST["Hr_EvaluationTypeID"]) ? $_REQUEST["Hr_EvaluationTypeID"] : "";
$Hr_EvaluationCycleID = isset($_REQUEST["Hr_EvaluationCycleID"]) ? $_REQUEST["Hr_EvaluationCycleID"] : "";

$Hr_EvaluationYear = isset($_REQUEST["Hr_EvaluationYear"]) ? $_REQUEST["Hr_EvaluationYear"] : "";
$Hr_EvaluationMonth = isset($_REQUEST["Hr_EvaluationMonth"]) ? $_REQUEST["Hr_EvaluationMonth"] : "";
$Hr_EvaluationName = isset($_REQUEST["Hr_EvaluationName"]) ? $_REQUEST["Hr_EvaluationName"] : "";

$Hr_EvaluationDate = isset($_REQUEST["Hr_EvaluationDate"]) ? $_REQUEST["Hr_EvaluationDate"] : "";
$Hr_EvaluationStartDate = isset($_REQUEST["Hr_EvaluationStartDate"]) ? $_REQUEST["Hr_EvaluationStartDate"] : "";
$Hr_EvaluationEndDate = isset($_REQUEST["Hr_EvaluationEndDate"]) ? $_REQUEST["Hr_EvaluationEndDate"] : "";
$Hr_EvaluationGoalStartDate = isset($_REQUEST["Hr_EvaluationGoalStartDate"]) ? $_REQUEST["Hr_EvaluationGoalStartDate"] : "";
$Hr_EvaluationGoalEndDate = isset($_REQUEST["Hr_EvaluationGoalEndDate"]) ? $_REQUEST["Hr_EvaluationGoalEndDate"] : "";

$Hr_EvaluationUseCompetency = isset($_REQUEST["Hr_EvaluationUseCompetency"]) ? $_REQUEST["Hr_EvaluationUseCompetency"] : "";
$Hr_EvaluationUseScore = isset($_REQUEST["Hr_EvaluationUseScore"]) ? $_REQUEST["Hr_EvaluationUseScore"] : "";
$Hr_EvaluationUseWarrant = isset($_REQUEST["Hr_EvaluationUseWarrant"]) ? $_REQUEST["Hr_EvaluationUseWarrant"] : "";
$Hr_EvaluationUseOverall = isset($_REQUEST["Hr_EvaluationUseOverall"]) ? $_REQUEST["Hr_EvaluationUseOverall"] : "";

$Hr_EvaluationState = isset($_REQUEST["Hr_EvaluationState"]) ? $_REQUEST["Hr_EvaluationState"] : "";



if ($Hr_EvaluationID==""){


	$Sql = " insert into Hr_Evaluations ( ";
		$Sql .= " CenterID, ";
		$Sql .= " Hr_EvaluationTypeID, ";
		$Sql .= " Hr_EvaluationCycleID, ";
		$Sql .= " Hr_EvaluationYear, ";
		$Sql .= " Hr_EvaluationMonth, ";
		$Sql .= " Hr_EvaluationName, ";
		$Sql .= " Hr_EvaluationDate, ";
		$Sql .= " Hr_EvaluationStartDate, ";
		$Sql .= " Hr_EvaluationEndDate, ";
		$Sql .= " Hr_EvaluationGoalStartDate, ";
		$Sql .= " Hr_EvaluationGoalEndDate, ";
		$Sql .= " Hr_EvaluationUseCompetency, ";
		$Sql .= " Hr_EvaluationUseScore, ";
		$Sql .= " Hr_EvaluationUseWarrant, ";
		$Sql .= " Hr_EvaluationUseOverall, ";
		$Sql .= " Hr_EvaluationRegDateTime, ";
		$Sql .= " Hr_EvaluationModiDateTime, ";
		$Sql .= " Hr_EvaluationState ";
	$Sql .= " ) values ( ";
		$Sql .= " :CenterID, ";
		$Sql .= " :Hr_EvaluationTypeID, ";
		$Sql .= " :Hr_EvaluationCycleID, ";
		$Sql .= " :Hr_EvaluationYear, ";
		$Sql .= " :Hr_EvaluationMonth, ";
		$Sql .= " :Hr_EvaluationName, ";
		$Sql .= " :Hr_EvaluationDate, ";
		$Sql .= " :Hr_EvaluationStartDate, ";
		$Sql .= " :Hr_EvaluationEndDate, ";
		$Sql .= " :Hr_EvaluationGoalStartDate, ";
		$Sql .= " :Hr_EvaluationGoalEndDate, ";
		$Sql .= " :Hr_EvaluationUseCompetency, ";
		$Sql .= " :Hr_EvaluationUseScore, ";
		$Sql .= " :Hr_EvaluationUseWarrant, ";
		$Sql .= " :Hr_EvaluationUseOverall, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :Hr_EvaluationState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':Hr_EvaluationTypeID', $Hr_EvaluationTypeID);
	$Stmt->bindParam(':Hr_EvaluationCycleID', $Hr_EvaluationCycleID);
	$Stmt->bindParam(':Hr_EvaluationYear', $Hr_EvaluationYear);
	$Stmt->bindParam(':Hr_EvaluationMonth', $Hr_EvaluationMonth);
	$Stmt->bindParam(':Hr_EvaluationName', $Hr_EvaluationName);
	$Stmt->bindParam(':Hr_EvaluationDate', $Hr_EvaluationDate);
	$Stmt->bindParam(':Hr_EvaluationStartDate', $Hr_EvaluationStartDate);
	$Stmt->bindParam(':Hr_EvaluationEndDate', $Hr_EvaluationEndDate);
	$Stmt->bindParam(':Hr_EvaluationGoalStartDate', $Hr_EvaluationGoalStartDate);
	$Stmt->bindParam(':Hr_EvaluationGoalEndDate', $Hr_EvaluationGoalEndDate);
	$Stmt->bindParam(':Hr_EvaluationUseCompetency', $Hr_EvaluationUseCompetency);
	$Stmt->bindParam(':Hr_EvaluationUseScore', $Hr_EvaluationUseScore);
	$Stmt->bindParam(':Hr_EvaluationUseWarrant', $Hr_EvaluationUseWarrant);
	$Stmt->bindParam(':Hr_EvaluationUseOverall', $Hr_EvaluationUseOverall);
	$Stmt->bindParam(':Hr_EvaluationState', $Hr_EvaluationState);
	$Stmt->execute();
	$Hr_EvaluationID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = " update Hr_Evaluations set ";
		$Sql .= " Hr_EvaluationTypeID = :Hr_EvaluationTypeID, ";
		$Sql .= " Hr_EvaluationCycleID = :Hr_EvaluationCycleID, ";
		$Sql .= " Hr_EvaluationYear = :Hr_EvaluationYear, ";
		$Sql .= " Hr_EvaluationMonth = :Hr_EvaluationMonth, ";
		$Sql .= " Hr_EvaluationName = :Hr_EvaluationName, ";
		$Sql .= " Hr_EvaluationDate = :Hr_EvaluationDate, ";
		$Sql .= " Hr_EvaluationStartDate = :Hr_EvaluationStartDate, ";
		$Sql .= " Hr_EvaluationEndDate = :Hr_EvaluationEndDate, ";
		$Sql .= " Hr_EvaluationGoalStartDate = :Hr_EvaluationGoalStartDate, ";
		$Sql .= " Hr_EvaluationGoalEndDate = :Hr_EvaluationGoalEndDate, ";
		$Sql .= " Hr_EvaluationUseCompetency = :Hr_EvaluationUseCompetency, ";
		$Sql .= " Hr_EvaluationUseScore = :Hr_EvaluationUseScore, ";
		$Sql .= " Hr_EvaluationUseWarrant = :Hr_EvaluationUseWarrant, ";
		$Sql .= " Hr_EvaluationUseOverall = :Hr_EvaluationUseOverall, ";
		$Sql .= " Hr_EvaluationModiDateTime = now(), ";
		$Sql .= " Hr_EvaluationState = :Hr_EvaluationState ";
	$Sql .= " where Hr_EvaluationID = :Hr_EvaluationID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_EvaluationTypeID', $Hr_EvaluationTypeID);
	$Stmt->bindParam(':Hr_EvaluationCycleID', $Hr_EvaluationCycleID);
	$Stmt->bindParam(':Hr_EvaluationYear', $Hr_EvaluationYear);
	$Stmt->bindParam(':Hr_EvaluationMonth', $Hr_EvaluationMonth);
	$Stmt->bindParam(':Hr_EvaluationName', $Hr_EvaluationName);
	$Stmt->bindParam(':Hr_EvaluationDate', $Hr_EvaluationDate);
	$Stmt->bindParam(':Hr_EvaluationStartDate', $Hr_EvaluationStartDate);
	$Stmt->bindParam(':Hr_EvaluationEndDate', $Hr_EvaluationEndDate);
	$Stmt->bindParam(':Hr_EvaluationGoalStartDate', $Hr_EvaluationGoalStartDate);
	$Stmt->bindParam(':Hr_EvaluationGoalEndDate', $Hr_EvaluationGoalEndDate);
	$Stmt->bindParam(':Hr_EvaluationUseCompetency', $Hr_EvaluationUseCompetency);
	$Stmt->bindParam(':Hr_EvaluationUseScore', $Hr_EvaluationUseScore);
	$Stmt->bindParam(':Hr_EvaluationUseWarrant', $Hr_EvaluationUseWarrant);
	$Stmt->bindParam(':Hr_EvaluationUseOverall', $Hr_EvaluationUseOverall);
	$Stmt->bindParam(':Hr_EvaluationState', $Hr_EvaluationState);
	$Stmt->bindParam(':Hr_EvaluationID', $Hr_EvaluationID);
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

