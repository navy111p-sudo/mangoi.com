<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$ProductSellerID = isset($_REQUEST["ProductSellerID"]) ? $_REQUEST["ProductSellerID"] : "";
$ProductSellerName = isset($_REQUEST["ProductSellerName"]) ? $_REQUEST["ProductSellerName"] : "";
$ProductSellerCancelLiminTime = isset($_REQUEST["ProductSellerCancelLiminTime"]) ? $_REQUEST["ProductSellerCancelLiminTime"] : "";
$ProductSellerShipPriceType = isset($_REQUEST["ProductSellerShipPriceType"]) ? $_REQUEST["ProductSellerShipPriceType"] : "";
$ProductSellerOrderTotPrice = isset($_REQUEST["ProductSellerOrderTotPrice"]) ? $_REQUEST["ProductSellerOrderTotPrice"] : "";
$ProductSellerShipPrice = isset($_REQUEST["ProductSellerShipPrice"]) ? $_REQUEST["ProductSellerShipPrice"] : "";
$ProductSellerState = 1;


if ($ProductSellerID==""){

	$Sql = "select ifnull(Max(ProductSellerOrder),0) as ProductSellerOrder from ProductSellers";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$ProductSellerOrder = $Row["ProductSellerOrder"]+1;

	$Sql = " insert into ProductSellers ( ";
		$Sql .= " ProductSellerName, ";
		$Sql .= " ProductSellerCancelLiminTime, ";
		$Sql .= " ProductSellerShipPriceType, ";
		$Sql .= " ProductSellerOrderTotPrice, ";
		$Sql .= " ProductSellerShipPrice, ";
		$Sql .= " ProductSellerRegDateTime, ";
		$Sql .= " ProductSellerModiDateTime, ";
		$Sql .= " ProductSellerState, ";
		$Sql .= " ProductSellerOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :ProductSellerName, ";
		$Sql .= " :ProductSellerCancelLiminTime, ";
		$Sql .= " :ProductSellerShipPriceType, ";
		$Sql .= " :ProductSellerOrderTotPrice, ";
		$Sql .= " :ProductSellerShipPrice, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :ProductSellerState, ";
		$Sql .= " :ProductSellerOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductSellerName', $ProductSellerName);
	$Stmt->bindParam(':ProductSellerCancelLiminTime', $ProductSellerCancelLiminTime);
	$Stmt->bindParam(':ProductSellerShipPriceType', $ProductSellerShipPriceType);
	$Stmt->bindParam(':ProductSellerOrderTotPrice', $ProductSellerOrderTotPrice);
	$Stmt->bindParam(':ProductSellerShipPrice', $ProductSellerShipPrice);
	$Stmt->bindParam(':ProductSellerState', $ProductSellerState);
	$Stmt->bindParam(':ProductSellerOrder', $ProductSellerOrder);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update ProductSellers set ";
		$Sql .= " ProductSellerName = :ProductSellerName, ";
		$Sql .= " ProductSellerCancelLiminTime = :ProductSellerCancelLiminTime, ";
		$Sql .= " ProductSellerShipPriceType = :ProductSellerShipPriceType, ";
		$Sql .= " ProductSellerOrderTotPrice = :ProductSellerOrderTotPrice, ";
		$Sql .= " ProductSellerShipPrice = :ProductSellerShipPrice, ";
		$Sql .= " ProductSellerState = :ProductSellerState, ";
		$Sql .= " ProductSellerModiDateTime = now() ";
	$Sql .= " where ProductSellerID = :ProductSellerID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductSellerName', $ProductSellerName);
	$Stmt->bindParam(':ProductSellerCancelLiminTime', $ProductSellerCancelLiminTime);
	$Stmt->bindParam(':ProductSellerShipPriceType', $ProductSellerShipPriceType);
	$Stmt->bindParam(':ProductSellerOrderTotPrice', $ProductSellerOrderTotPrice);
	$Stmt->bindParam(':ProductSellerShipPrice', $ProductSellerShipPrice);
	$Stmt->bindParam(':ProductSellerState', $ProductSellerState);
	$Stmt->bindParam(':ProductSellerID', $ProductSellerID);
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
	header("Location: product_seller_list.php?$ListParam"); 
	exit;
}
?>


