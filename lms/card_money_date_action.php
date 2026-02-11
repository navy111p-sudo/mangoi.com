<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$StartDay = isset($_REQUEST["StartDay"]) ? $_REQUEST["StartDay"] : "";
$EndDay = isset($_REQUEST["EndDay"]) ? $_REQUEST["EndDay"] : "";



	$Sql = " UPDATE CardMoneyDate set ";
		$Sql .= " StartDay = :StartDay, ";
		$Sql .= " EndDay = :EndDay ";
	

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':StartDay', $StartDay);
	$Stmt->bindParam(':EndDay', $EndDay);
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
	header("Location: card_money_date_form.php"); 
	exit;
}
?>


