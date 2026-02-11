<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$delete = isset($_REQUEST["delete"]) ? $_REQUEST["delete"] : "";
$Year = isset($_REQUEST["Year"]) ? $_REQUEST["Year"] : "";
$InsuranceID = isset($_REQUEST["InsuranceID"]) ? $_REQUEST["InsuranceID"] : "";
$EmploymentInsurance = isset($_REQUEST["EmploymentInsurance"]) ? $_REQUEST["EmploymentInsurance"] : "";
$HealthInsurance = isset($_REQUEST["HealthInsurance"]) ? $_REQUEST["HealthInsurance"] : "";
$CareInsurance = isset($_REQUEST["CareInsurance"]) ? $_REQUEST["CareInsurance"] : "";
$NationalPension = isset($_REQUEST["NationalPension"]) ? $_REQUEST["NationalPension"] : "";

//만약 삭제요청시 해당 레코드를 삭제한다.
if ($delete == 'true'){
	$Sql = " DELETE FROM PayInsuranceRate 
				WHERE InsuranceID = :InsuranceID  ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':InsuranceID', $InsuranceID);
	$Stmt->execute();
	$Stmt = null;

} else {

	if ($InsuranceID==""){

		$Sql = " INSERT into PayInsuranceRate ( 
			Year,
			EmploymentInsurance, 
			HealthInsurance, 
			CareInsurance,
			NationalPension 
		) values ( 
			:Year,
			:EmploymentInsurance, 
			:HealthInsurance, 
			:CareInsurance,
			:NationalPension ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':Year', $Year);
		$Stmt->bindParam(':EmploymentInsurance', $EmploymentInsurance);
		$Stmt->bindParam(':HealthInsurance', $HealthInsurance);
		$Stmt->bindParam(':CareInsurance', $CareInsurance);
		$Stmt->bindParam(':NationalPension', $NationalPension);
		$Stmt->execute();
		$Stmt = null;

	} else {

		$Sql = " UPDATE PayInsuranceRate set ";
			$Sql .= " Year = :Year, ";
			$Sql .= " EmploymentInsurance = :EmploymentInsurance, ";
			$Sql .= " HealthInsurance = :HealthInsurance, ";
			$Sql .= " CareInsurance = :CareInsurance, ";
			$Sql .= " NationalPension = :NationalPension ";
		$Sql .= " where InsuranceID = :InsuranceID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':InsuranceID', $InsuranceID);
		$Stmt->bindParam(':Year', $Year);
		$Stmt->bindParam(':EmploymentInsurance', $EmploymentInsurance);
		$Stmt->bindParam(':HealthInsurance', $HealthInsurance);
		$Stmt->bindParam(':CareInsurance', $CareInsurance);
		$Stmt->bindParam(':NationalPension', $NationalPension);
		$Stmt->execute();
		$Stmt = null;

	}
}

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
	header("Location: pay_insurance_rate_list.php?$ListParam"); 
	exit;
}
?>


