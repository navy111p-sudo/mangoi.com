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

$ReviewClassMemberID = isset($_REQUEST["ReviewClassMemberID"]) ? $_REQUEST["ReviewClassMemberID"] : "";
$AnswerMemberID = isset($_REQUEST["AnswerMemberID"]) ? $_REQUEST["AnswerMemberID"] : "";
$AnswerMemberName = isset($_REQUEST["AnswerMemberName"]) ? $_REQUEST["AnswerMemberName"] : "";
$ReviewClassMemberAnswer = isset($_REQUEST["ReviewClassMemberAnswer"]) ? $_REQUEST["ReviewClassMemberAnswer"] : "";
$ReviewClassMemberState = isset($_REQUEST["ReviewClassMemberState"]) ? $_REQUEST["ReviewClassMemberState"] : "";


$Sql = "
		select 
				A.*
		from ReviewClassMembers A 
		where A.ReviewClassMemberID=:ReviewClassMemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ReviewClassMemberID', $ReviewClassMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberID = $Row["MemberID"];
$ReviewClassMemberAnswerRegDateTime = $Row["ReviewClassMemberAnswerRegDateTime"];


$Sql = " update ReviewClassMembers set ";
	$Sql .= " AnswerMemberID = :AnswerMemberID, ";
	$Sql .= " AnswerMemberName = :AnswerMemberName, ";
	$Sql .= " ReviewClassMemberAnswer = :ReviewClassMemberAnswer, ";

	if ($ReviewClassMemberAnswerRegDateTime==""){
		$Sql .= " ReviewClassMemberAnswerRegDateTime = now(), ";
	}
	$Sql .= " ReviewClassMemberAnswerModiDateTime = now(), ";
	$Sql .= " ReviewClassMemberState = :ReviewClassMemberState ";
$Sql .= " where ReviewClassMemberID = :ReviewClassMemberID ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':AnswerMemberID', $AnswerMemberID);
$Stmt->bindParam(':AnswerMemberName', $AnswerMemberName);
$Stmt->bindParam(':ReviewClassMemberAnswer', $ReviewClassMemberAnswer);
$Stmt->bindParam(':ReviewClassMemberState', $ReviewClassMemberState);
$Stmt->bindParam(':ReviewClassMemberID', $ReviewClassMemberID);
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

