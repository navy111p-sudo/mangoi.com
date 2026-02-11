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

$ClassTeacherEnterID = isset($_REQUEST["ClassTeacherEnterID"]) ? $_REQUEST["ClassTeacherEnterID"] : "";
$ReasonType = isset($_REQUEST["ReasonType"]) ? $_REQUEST["ReasonType"] : "";
$ReasonText = isset($_REQUEST["ReasonText"]) ? $_REQUEST["ReasonText"] : "";

// ReasonType1 = 지각사유 , ReasonTyp2 = 지각사유에 대한 답변 
if ($ReasonType=="1"){
	$Sql_select = " select A.ClassEnterLateReason from ClassTeacherEnters A where A.ClassTeacherEnterID=:ClassTeacherEnterID ";
	$Stmt_select = $DbConn->prepare($Sql_select);
	$Stmt_select->bindParam(':ClassTeacherEnterID', $ClassTeacherEnterID);
	$Stmt_select->execute();
	$Row_select = $Stmt_select->fetch();
	$ClassEnterLateReason = $Row_select["ClassEnterLateReason"];
	$Stmt_select = null;

	$timestamp = strtotime("Now");
	$NowDateTime = date("Y-m-d H:i:s", $timestamp);

	// 운영쪽은 콘텐츠 하나만 있기에 ||| 에러핸들링
	if ( strpos($ClassEnterLateReason, "|||") !== true ) {
		$ClassEnterLateReason = $ClassEnterLateReason."|||";
	}

	$ReasonText = $ReasonText . "||". $NowDateTime . "|||" . $ClassEnterLateReason;
	$Sql = " update ClassTeacherEnters set ";
		$Sql .= " ClassEnterLateReason = :ClassEnterLateReason, ";
		$Sql .= " ClassEnterLateReasonDateTime = now() ";
	$Sql .= " where ClassTeacherEnterID = :ClassTeacherEnterID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassEnterLateReason', $ReasonText);
	$Stmt->bindParam(':ClassTeacherEnterID', $ClassTeacherEnterID);
	$Stmt->execute();
	$Stmt = null;
}else{
	$Sql = " update ClassTeacherEnters set ";
		$Sql .= " ClassEnterLateReasonAnswer = :ClassEnterLateReasonAnswer, ";
		$Sql .= " ClassEnterLateReasonAnswerDateTime = now() ";
	$Sql .= " where ClassTeacherEnterID = :ClassTeacherEnterID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassEnterLateReasonAnswer', $ReasonText);
	$Stmt->bindParam(':ClassTeacherEnterID', $ClassTeacherEnterID);
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
parent.location.reload();
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

