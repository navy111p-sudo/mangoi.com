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

$SearchState                       = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
$MemberID                          = isset($_REQUEST["MemberID"   ]) ? $_REQUEST["MemberID"   ] : "";
$Hr_EvaluationCompetencyMemberID   = isset($_REQUEST["Hr_EvaluationCompetencyMemberID"  ]) ? $_REQUEST["Hr_EvaluationCompetencyMemberID"  ] : "";
$Hr_EvaluationCompetencyMemberType = isset($_REQUEST["Hr_EvaluationCompetencyMemberType"]) ? $_REQUEST["Hr_EvaluationCompetencyMemberType"] : "";
$Hr_EvaluationCompetencyAddValue   = isset($_REQUEST["Hr_EvaluationCompetencyAddValue"  ]) ? $_REQUEST["Hr_EvaluationCompetencyAddValue"  ] : "";

$Sql = "select count(*) as RowCount
            from Hr_EvaluationCompetencyMembers 
		   where Hr_EvaluationID=:Hr_EvaluationID and 
		         MemberID=:MemberID and 
				 Hr_EvaluationCompetencyMemberID=:Hr_EvaluationCompetencyMemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':Hr_EvaluationID', $SearchState);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':Hr_EvaluationCompetencyMemberID', $Hr_EvaluationCompetencyMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$RowCount = $Row["RowCount"];
if ($RowCount==0){
	
		$Sql = " insert into Hr_EvaluationCompetencyMembers ( ";
			$Sql .= " Hr_EvaluationID, ";
			$Sql .= " MemberID, ";
			$Sql .= " Hr_EvaluationCompetencyMemberID, ";
			$Sql .= " Hr_EvaluationCompetencyMemberType, ";
			$Sql .= " Hr_EvaluationCompetencyAddValue, ";
			$Sql .= " Hr_EvaluationCompetencyMemberRegDateTime, ";
			$Sql .= " Hr_EvaluationCompetencyMemberModiDateTime ";
		$Sql .= " ) values ( ";
			$Sql .= " :Hr_EvaluationID, ";
			$Sql .= " :MemberID, ";
			$Sql .= " :Hr_EvaluationCompetencyMemberID, ";
			$Sql .= " :Hr_EvaluationCompetencyMemberType, ";
			$Sql .= " :Hr_EvaluationCompetencyAddValue, ";
			$Sql .= " now(), ";
			$Sql .= " now() ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':Hr_EvaluationID',       $SearchState);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->bindParam(':Hr_EvaluationCompetencyMemberID',   $Hr_EvaluationCompetencyMemberID);
		$Stmt->bindParam(':Hr_EvaluationCompetencyMemberType', $Hr_EvaluationCompetencyMemberType);
		$Stmt->bindParam(':Hr_EvaluationCompetencyAddValue',   $Hr_EvaluationCompetencyAddValue);
		$Stmt->execute();
		$Hr_OrganTask2ID = $DbConn->lastInsertId();
		$Stmt = null;

} else {

		$Sql = " update Hr_EvaluationCompetencyMembers set ";
			$Sql .= " Hr_EvaluationCompetencyMemberType = ".$Hr_EvaluationCompetencyMemberType.", ";
			$Sql .= " Hr_EvaluationCompetencyAddValue   = ".$Hr_EvaluationCompetencyAddValue.", ";
			$Sql .= " Hr_EvaluationCompetencyMemberModiDateTime = now() ";
		$Sql .= " where Hr_EvaluationID=:Hr_EvaluationID and MemberID=:MemberID and Hr_EvaluationCompetencyMemberID=:Hr_EvaluationCompetencyMemberID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':Hr_EvaluationID', $SearchState);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->bindParam(':Hr_EvaluationCompetencyMemberID', $Hr_EvaluationCompetencyMemberID);
		$Stmt->execute();
		$Stmt = null;

}
?>


<script>

</script>
<?
include_once('../includes/dbclose.php');
?>
<form name="RegForm" method="post">
<input type="hidden" name="SearchState"  value="<?=$SearchState?>" />
</form>

<script>
 document.RegForm.target = "_top";
 document.RegForm.action = "hr_evaluation_competency_table.php";
 document.RegForm.submit();
</script>

</body>
</html>

