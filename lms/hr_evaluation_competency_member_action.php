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

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$Hr_EvaluationCompetencyMemberID = isset($_REQUEST["Hr_EvaluationCompetencyMemberID"]) ? $_REQUEST["Hr_EvaluationCompetencyMemberID"] : "";
$Hr_EvaluationCompetencyMemberType = isset($_REQUEST["Hr_EvaluationCompetencyMemberType"]) ? $_REQUEST["Hr_EvaluationCompetencyMemberType"] : "";


$Sql = "
		select 
			count(*) as RowCount
		from Hr_EvaluationCompetencyMembers A 
		where A.MemberID=:MemberID and A.Hr_EvaluationCompetencyMemberID=:Hr_EvaluationCompetencyMemberID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':Hr_EvaluationCompetencyMemberID', $Hr_EvaluationCompetencyMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$RowCount = $Row["RowCount"];


if ($RowCount==0){

	$Sql = " insert into Hr_EvaluationCompetencyMembers ( ";
		$Sql .= " MemberID, ";
		$Sql .= " Hr_EvaluationCompetencyMemberID, ";
		$Sql .= " Hr_EvaluationCompetencyMemberType, ";
		$Sql .= " Hr_EvaluationCompetencyMemberRegDateTime, ";
		$Sql .= " Hr_EvaluationCompetencyMemberModiDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :MemberID, ";
		$Sql .= " :Hr_EvaluationCompetencyMemberID, ";
		$Sql .= " :Hr_EvaluationCompetencyMemberType, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':Hr_EvaluationCompetencyMemberID', $Hr_EvaluationCompetencyMemberID);
	$Stmt->bindParam(':Hr_EvaluationCompetencyMemberType', $Hr_EvaluationCompetencyMemberType);
	$Stmt->execute();
	$Hr_OrganTask2ID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = " update Hr_EvaluationCompetencyMembers set ";
		$Sql .= " Hr_EvaluationCompetencyMemberType = :Hr_EvaluationCompetencyMemberType, ";
		$Sql .= " Hr_EvaluationCompetencyMemberModiDateTime = now() ";
	$Sql .= " where MemberID = :MemberID and Hr_EvaluationCompetencyMemberID = :Hr_EvaluationCompetencyMemberID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':Hr_EvaluationCompetencyMemberID', $Hr_EvaluationCompetencyMemberID);
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
parent.$.fn.colorbox.close();
//parent.location.reload();
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

