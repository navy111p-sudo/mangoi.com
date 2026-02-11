<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$PayMonth = isset($_REQUEST["PayMonth"]) ? $_REQUEST["PayMonth"] : "";
$BasePay = isset($_REQUEST["BasePay"]) ? $_REQUEST["BasePay"] : 0;
$SpecialDutyPay = isset($_REQUEST["SpecialDutyPay"]) ? $_REQUEST["SpecialDutyPay"] : 0;
$PositionPay = isset($_REQUEST["PositionPay"]) ? $_REQUEST["PositionPay"] : 0;
$OverTimePay = isset($_REQUEST["OverTimePay"]) ? $_REQUEST["OverTimePay"] : 0;
$ReplacePay = isset($_REQUEST["ReplacePay"]) ? $_REQUEST["ReplacePay"] : 0;
$IncentivePay = isset($_REQUEST["IncentivePay"]) ? $_REQUEST["IncentivePay"] : 0;
$Special1 = isset($_REQUEST["Special1"]) ? $_REQUEST["Special1"] : 0;
$Special2 = isset($_REQUEST["Special2"]) ? $_REQUEST["Special2"] : 0;
$Add1 = isset($_REQUEST["Add1"]) ? $_REQUEST["Add1"] : 0;
$Add2 = isset($_REQUEST["Add2"]) ? $_REQUEST["Add2"] : 0;
$Add3 = isset($_REQUEST["Add3"]) ? $_REQUEST["Add3"] : 0;
$Add4 = isset($_REQUEST["Add4"]) ? $_REQUEST["Add4"] : 0;
$Add5 = isset($_REQUEST["Add5"]) ? $_REQUEST["Add5"] : 0;
$Add6 = isset($_REQUEST["Add6"]) ? $_REQUEST["Add6"] : 0;
$Add7 = isset($_REQUEST["Add7"]) ? $_REQUEST["Add7"] : 0;
$Add1Name = isset($_REQUEST["Add1Name"]) ? $_REQUEST["Add1Name"] : "";
$Add2Name = isset($_REQUEST["Add2Name"]) ? $_REQUEST["Add2Name"] : "";
$Add3Name = isset($_REQUEST["Add3Name"]) ? $_REQUEST["Add3Name"] : "";
$Add4Name = isset($_REQUEST["Add4Name"]) ? $_REQUEST["Add4Name"] : "";
$Add5Name = isset($_REQUEST["Add5Name"]) ? $_REQUEST["Add5Name"] : "";
$Add6Name = isset($_REQUEST["Add6Name"]) ? $_REQUEST["Add6Name"] : "";
$Add7Name = isset($_REQUEST["Add7Name"]) ? $_REQUEST["Add7Name"] : "";



	$Sql = " UPDATE PayTaxInfo set ";
		$Sql .= " BasePay = :BasePay, ";
		$Sql .= " SpecialDutyPay = :SpecialDutyPay, ";
		$Sql .= " PositionPay = :PositionPay, ";
		$Sql .= " OverTimePay = :OverTimePay, ";
		$Sql .= " ReplacePay = :ReplacePay, ";
		$Sql .= " IncentivePay = :IncentivePay, ";
		$Sql .= " Special1 = :Special1, ";
		$Sql .= " Special2 = :Special2, ";
		$Sql .= " Add1 = :Add1, ";
		//$Sql .= " Add1Name = :Add1Name, ";
		$Sql .= " Add2 = :Add2, ";
		//$Sql .= " Add1Name = :Add2Name, ";
		$Sql .= " Add3 = :Add3, ";
		//$Sql .= " Add1Name = :Add3Name, ";
		$Sql .= " Add4 = :Add4, ";
		//$Sql .= " Add1Name = :Add4Name, ";
		$Sql .= " Add5 = :Add5, ";
		// $Sql .= " Add1Name = :Add5Name, ";
		$Sql .= " Add6 = :Add6, ";
		// $Sql .= " Add1Name = :Add6Name, ";
		$Sql .= " Add7 = :Add7 ";
		// $Sql .= " Add1Name = :Add7Name ";
	$Sql .= " WHERE PayMonth = :PayMonth ";

	

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':PayMonth', $PayMonth);
	$Stmt->bindParam(':BasePay', $BasePay);
	$Stmt->bindParam(':SpecialDutyPay', $SpecialDutyPay);
	$Stmt->bindParam(':PositionPay', $PositionPay);
	$Stmt->bindParam(':OverTimePay', $OverTimePay);
	$Stmt->bindParam(':ReplacePay', $ReplacePay);
	$Stmt->bindParam(':IncentivePay', $IncentivePay);
	$Stmt->bindParam(':Special1', $Special1);
	$Stmt->bindParam(':Special2', $Special2);
	$Stmt->bindParam(':Add1', $Add1);
	// $Stmt->bindParam(':Add1Name', $Add1Name);
	$Stmt->bindParam(':Add2', $Add2);
	// $Stmt->bindParam(':Add2Name', $Add2Name);
	$Stmt->bindParam(':Add3', $Add3);
	// $Stmt->bindParam(':Add3Name', $Add3Name);
	$Stmt->bindParam(':Add4', $Add4);
	// $Stmt->bindParam(':Add4Name', $Add4Name);
	$Stmt->bindParam(':Add5', $Add5);
	// $Stmt->bindParam(':Add5Name', $Add5Name);
	$Stmt->bindParam(':Add6', $Add6);
	// $Stmt->bindParam(':Add6Name', $Add6Name);
	$Stmt->bindParam(':Add7', $Add7);
	// $Stmt->bindParam(':Add7Name', $Add7Name);

	//echo $Sql;
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
	header("Location: pay_tax_info_form.php?$ListParam"); 
	exit;
}
?>


