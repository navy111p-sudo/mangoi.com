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

$BranchAccountID = isset($_REQUEST["BranchAccountID"]) ? $_REQUEST["BranchAccountID"] : "";
$BranchID = isset($_REQUEST["BranchID"]) ? $_REQUEST["BranchID"] : "";
$BranchAccountName = isset($_REQUEST["BranchAccountName"]) ? $_REQUEST["BranchAccountName"] : "";
$BranchAccountPrice = isset($_REQUEST["BranchAccountPrice"]) ? $_REQUEST["BranchAccountPrice"] : "";
$BranchAccountState = isset($_REQUEST["BranchAccountState"]) ? $_REQUEST["BranchAccountState"] : "";


if ($BranchAccountState!="2"){
	$BranchAccountState = 1;
}


if ($BranchAccountID==""){

	$Sql = " insert into BranchAccounts ( ";
		$Sql .= " BranchID, ";
		$Sql .= " BranchAccountName, ";
		$Sql .= " BranchAccountPrice, ";
		$Sql .= " BranchAccountRegDateTime, ";
		$Sql .= " BranchAccountModiDateTime, ";
		$Sql .= " BranchAccountState ";
	$Sql .= " ) values ( ";
		$Sql .= " :BranchID, ";
		$Sql .= " :BranchAccountName, ";
		$Sql .= " :BranchAccountPrice, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :BranchAccountState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BranchID', $BranchID);
	$Stmt->bindParam(':BranchAccountName', $BranchAccountName);
	$Stmt->bindParam(':BranchAccountPrice', $BranchAccountPrice);
	$Stmt->bindParam(':BranchAccountState', $BranchAccountState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update BranchAccounts set ";
		$Sql .= " BranchID = :BranchID, ";
		$Sql .= " BranchAccountName = :BranchAccountName, ";
		$Sql .= " BranchAccountPrice = :BranchAccountPrice, ";
		$Sql .= " BranchAccountModiDateTime = now(), ";
		$Sql .= " BranchAccountState = :BranchAccountState ";
	$Sql .= " where BranchAccountID = :BranchAccountID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BranchID', $BranchID);
	$Stmt->bindParam(':BranchAccountName', $BranchAccountName);
	$Stmt->bindParam(':BranchAccountPrice', $BranchAccountPrice);
	$Stmt->bindParam(':BranchAccountState', $BranchAccountState);
	$Stmt->bindParam(':BranchAccountID', $BranchAccountID);
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
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

