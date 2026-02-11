<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$CountryCode = isset($_REQUEST["CountryCode"]) ? $_REQUEST["CountryCode"] : "";
$CountryName = isset($_REQUEST["CountryName"]) ? $_REQUEST["CountryName"] : "";
$Currency = isset($_REQUEST["Currency"]) ? $_REQUEST["Currency"] : "";





	$Sql = " UPDATE Currency set ";
		$Sql .= " CountryName = :CountryName, ";
		$Sql .= " Currency = :Currency ";
	$Sql .= " where CountryCode = :CountryCode ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CountryCode', $CountryCode);
	$Stmt->bindParam(':CountryName', $CountryName);
	$Stmt->bindParam(':Currency', $Currency);
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
	header("Location: currency_form.php?CountryCode=$CountryCode"); 
	exit;
}
?>


