<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');


$PayMonth = isset($_REQUEST["PayMonth"]) ? $_REQUEST["PayMonth"] : "";
$EmploymentInsurance = isset($_REQUEST["EmploymentInsurance"]) ? $_REQUEST["EmploymentInsurance"] : 0;
$HealthInsurance = isset($_REQUEST["HealthInsurance"]) ? $_REQUEST["HealthInsurance"] : 0;
$CareInsurance = isset($_REQUEST["CareInsurance"]) ? $_REQUEST["CareInsurance"] : 0;
$NationalPension = isset($_REQUEST["NationalPension"]) ? $_REQUEST["NationalPension"] : 0;
$Add1 = isset($_REQUEST["Add1"]) ? $_REQUEST["Add1"] : 0;
$Add2 = isset($_REQUEST["Add2"]) ? $_REQUEST["Add2"] : 0;
$Add3 = isset($_REQUEST["Add3"]) ? $_REQUEST["Add3"] : 0;
$Add4 = isset($_REQUEST["Add4"]) ? $_REQUEST["Add4"] : 0;



	$Sql = " UPDATE PayDeductionInfo set ";
		$Sql .= " EmploymentInsurance = :EmploymentInsurance, ";
		$Sql .= " HealthInsurance = :HealthInsurance, ";
		$Sql .= " CareInsurance = :CareInsurance, ";
		$Sql .= " NationalPension = :NationalPension, ";
		$Sql .= " Add1 = :Add1, ";
		$Sql .= " Add2 = :Add2, ";
		$Sql .= " Add3 = :Add3, ";
		$Sql .= " Add4 = :Add4 ";
	$Sql .= " WHERE PayMonth = :PayMonth ";

	

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EmploymentInsurance', $EmploymentInsurance);
	$Stmt->bindParam(':HealthInsurance', $HealthInsurance);
	$Stmt->bindParam(':CareInsurance', $CareInsurance);
	$Stmt->bindParam(':NationalPension', $NationalPension);
	$Stmt->bindParam(':Add1', $Add1);
	$Stmt->bindParam(':Add2', $Add2);
	$Stmt->bindParam(':Add3', $Add3);
	$Stmt->bindParam(':Add4', $Add4);
	$Stmt->bindParam(':PayMonth', $PayMonth);
	$Stmt->execute();
	$Stmt = null;



if ($err_num != 0){
	include_once('./inc_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
<?php
	include_once('./inc_footer.php'); 
?>
</body>
</html>
<?
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: pay_deduction_info_form.php?$ListParam"); 
	exit;
}
?>


