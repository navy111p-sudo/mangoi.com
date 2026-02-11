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

$SpecialNo = isset($_REQUEST["SpecialNo"]) ? $_REQUEST["SpecialNo"] : "";
$SpecialName = isset($_REQUEST["SpecialName"]) ? $_REQUEST["SpecialName"] : "";
$Special = isset($_REQUEST["Special"]) ? $_REQUEST["Special"] : "";
$SpecialType = isset($_REQUEST["SpecialType"]) ? $_REQUEST["SpecialType"] : "";

// 상여금이 % 인지 정액인지에 따라 sql문에 추가한다.
if ($SpecialType == "0") {
	$addSql = " * BasePay * 0.01";
} else {
	$addSql = "";
}

$SpecialNo += 1; 

$Sql = "UPDATE Pay SET 
				Special".$SpecialNo." = :Special ".$addSql.",
				SpecialName".$SpecialNo." = :SpecialName 
		";	

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':Special', $Special);
$Stmt->bindParam(':SpecialName', $SpecialName);
	
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

