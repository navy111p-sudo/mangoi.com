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

$ProductSellerID = isset($_REQUEST["ProductSellerID"]) ? $_REQUEST["ProductSellerID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$ProductOrderCartID = isset($_REQUEST["ProductOrderCartID"]) ? $_REQUEST["ProductOrderCartID"] : "";
$ProductOrderCartName = isset($_REQUEST["ProductOrderCartName"]) ? $_REQUEST["ProductOrderCartName"] : "";
$ProductOrderCartState = isset($_REQUEST["ProductOrderCartState"]) ? $_REQUEST["ProductOrderCartState"] : "";


if ($ProductOrderCartID==""){

	$Sql = "select ifnull(Max(ProductOrderCartOrder),0) as ProductOrderCartOrder from ProductOrderCarts";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	
	$ProductOrderCartOrder = $Row["ProductOrderCartOrder"]+1;


	$Sql = " insert into ProductOrderCarts ( ";
		$Sql .= " ProductSellerID, ";
		$Sql .= " MemberID, ";
		$Sql .= " ProductOrderCartName, ";
		$Sql .= " ProductOrderCartState, ";
		$Sql .= " ProductOrderCartRegDateTime, ";
		$Sql .= " ProductOrderCartModiDateTime, ";
		$Sql .= " ProductOrderCartOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :ProductSellerID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " :ProductOrderCartName, ";
		$Sql .= " :ProductOrderCartState, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :ProductOrderCartOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductSellerID', $ProductSellerID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':ProductOrderCartName', $ProductOrderCartName);
	$Stmt->bindParam(':ProductOrderCartState', $ProductOrderCartState);
	$Stmt->bindParam(':ProductOrderCartOrder', $ProductOrderCartOrder);
	$Stmt->execute();
	$Stmt = null;

}else{

	if ($ProductOrderCartState=="0"){

		$Sql = " delete from ProductOrderCartDetails where ProductOrderCartID=:ProductOrderCartID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
		$Stmt->execute();
		$Stmt = null;

		$Sql = " delete from ProductOrderCarts where ProductOrderCartID=:ProductOrderCartID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
		$Stmt->execute();
		$Stmt = null;

	}else{

		$Sql = " update ProductOrderCarts set ";
			$Sql .= " ProductOrderCartName = :ProductOrderCartName, ";
			$Sql .= " ProductOrderCartState = :ProductOrderCartState, ";
			$Sql .= " ProductOrderCartModiDateTime = now() ";
		$Sql .= " where ProductOrderCartID = :ProductOrderCartID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ProductOrderCartName', $ProductOrderCartName);
		$Stmt->bindParam(':ProductOrderCartState', $ProductOrderCartState);
		$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
		$Stmt->execute();
		$Stmt = null;
	}
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

