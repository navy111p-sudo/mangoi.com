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

$DirectQnaMemberID = isset($_REQUEST["DirectQnaMemberID"]) ? $_REQUEST["DirectQnaMemberID"] : "";
$AnswerType = isset($_REQUEST["AnswerType"]) ? $_REQUEST["AnswerType"] : "";
$AnswerMemberID = isset($_REQUEST["AnswerMemberID"]) ? $_REQUEST["AnswerMemberID"] : "";
$AnswerMemberName = isset($_REQUEST["AnswerMemberName"]) ? $_REQUEST["AnswerMemberName"] : "";
$DirectQnaMemberAnswer = isset($_REQUEST["DirectQnaMemberAnswer"]) ? $_REQUEST["DirectQnaMemberAnswer"] : "";
$DirectQnaMemberState = isset($_REQUEST["DirectQnaMemberState"]) ? $_REQUEST["DirectQnaMemberState"] : "";


$Sql = "
		select 
				A.*
		from DirectQnaMembers A 
		where A.DirectQnaMemberID=:DirectQnaMemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':DirectQnaMemberID', $DirectQnaMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$DirectQnaMemberAnswerRegDateTime = $Row["DirectQnaMemberAnswerRegDateTime"];		


$Sql = " update DirectQnaMembers set ";
	$Sql .= " AnswerType = :AnswerType, ";
	$Sql .= " AnswerMemberID = :AnswerMemberID, ";
	$Sql .= " AnswerMemberName = :AnswerMemberName, ";
	$Sql .= " DirectQnaMemberAnswer = :DirectQnaMemberAnswer, ";

	if ($DirectQnaMemberAnswerRegDateTime==""){
		$Sql .= " DirectQnaMemberAnswerRegDateTime = now(), ";
	}
	$Sql .= " DirectQnaMemberAnswerModiDateTime = now(), ";
	$Sql .= " DirectQnaMemberState = :DirectQnaMemberState ";
$Sql .= " where DirectQnaMemberID = :DirectQnaMemberID ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':AnswerType', $AnswerType);
$Stmt->bindParam(':AnswerMemberID', $AnswerMemberID);
$Stmt->bindParam(':AnswerMemberName', $AnswerMemberName);
$Stmt->bindParam(':DirectQnaMemberAnswer', $DirectQnaMemberAnswer);
$Stmt->bindParam(':DirectQnaMemberState', $DirectQnaMemberState);
$Stmt->bindParam(':DirectQnaMemberID', $DirectQnaMemberID);
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

