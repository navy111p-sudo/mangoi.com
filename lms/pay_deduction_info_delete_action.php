<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$PayMonth = isset($_REQUEST["PayMonth"]) ? $_REQUEST["PayMonth"] : "";
$itemNumber = isset($_REQUEST["itemNumber"]) ? $_REQUEST["itemNumber"] : "";


if ($itemNumber!="" && $PayMonth != ""){
	
		$Sql = "UPDATE PayDeductionInfo SET Add".$itemNumber."Name = '' WHERE PayMonth = :PayMonth ";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':PayMonth', $PayMonth);
			
		$Stmt->execute();
		$Stmt = null;

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
?>
<script>

	document.location = "./pay_deduction_info_form.php?PayMonth=<?=$PayMonth?>";
</script>
<?
}
?>


