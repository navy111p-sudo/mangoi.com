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

$CouponID = isset($_REQUEST["CouponID"]) ? $_REQUEST["CouponID"] : "";
$CouponName = isset($_REQUEST["CouponName"]) ? $_REQUEST["CouponName"] : "";
$CouponTypeID = isset($_REQUEST["CouponTypeID"]) ? $_REQUEST["CouponTypeID"] : "";
$CouponStartDate = isset($_REQUEST["CouponStartDate"]) ? $_REQUEST["CouponStartDate"] : "";
$CouponEndDate = isset($_REQUEST["CouponEndDate"]) ? $_REQUEST["CouponEndDate"] : "";
$CouponPrice = isset($_REQUEST["CouponPrice"]) ? $_REQUEST["CouponPrice"] : "";
$CouponState = isset($_REQUEST["CouponState"]) ? $_REQUEST["CouponState"] : "";

if ($CouponState!="1"){
	$CouponState = 2;
}


if ($CouponID==""){

	$Sql = " insert into Coupons ( ";
		$Sql .= " CouponTypeID, ";
		$Sql .= " CouponName, ";
		$Sql .= " CouponStartDate, ";
		$Sql .= " CouponEndDate, ";
		$Sql .= " CouponPrice, ";
		$Sql .= " CouponRegDateTime, ";
		$Sql .= " CouponModiDateTime, ";
		$Sql .= " CouponState ";
	$Sql .= " ) values ( ";
		$Sql .= " :CouponTypeID, ";
		$Sql .= " :CouponName, ";
		$Sql .= " :CouponStartDate, ";
		$Sql .= " :CouponEndDate, ";
		$Sql .= " :CouponPrice, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :CouponState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CouponTypeID', $CouponTypeID);
	$Stmt->bindParam(':CouponName', $CouponName);
	$Stmt->bindParam(':CouponStartDate', $CouponStartDate);
	$Stmt->bindParam(':CouponEndDate', $CouponEndDate);
	$Stmt->bindParam(':CouponPrice', $CouponPrice);
	$Stmt->bindParam(':CouponState', $CouponState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update Coupons set ";
		$Sql .= " CouponTypeID = :CouponTypeID, ";
		$Sql .= " CouponName = :CouponName, ";
		$Sql .= " CouponStartDate = :CouponStartDate, ";
		$Sql .= " CouponEndDate = :CouponEndDate, ";
		$Sql .= " CouponPrice = :CouponPrice, ";
		$Sql .= " CouponModiDateTime = now(), ";
		$Sql .= " CouponState = :CouponState ";
	$Sql .= " where CouponID = :CouponID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CouponTypeID', $CouponTypeID);
	$Stmt->bindParam(':CouponName', $CouponName);
	$Stmt->bindParam(':CouponStartDate', $CouponStartDate);
	$Stmt->bindParam(':CouponEndDate', $CouponEndDate);
	$Stmt->bindParam(':CouponPrice', $CouponPrice);
	$Stmt->bindParam(':CouponState', $CouponState);
	$Stmt->bindParam(':CouponID', $CouponID);
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

