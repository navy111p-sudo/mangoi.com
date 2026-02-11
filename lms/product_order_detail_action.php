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

$ProductOrderID = isset($_REQUEST["ProductOrderID"]) ? $_REQUEST["ProductOrderID"] : "";
$ReceiveName = isset($_REQUEST["ReceiveName"]) ? $_REQUEST["ReceiveName"] : "";
$ReceivePhone1 = isset($_REQUEST["ReceivePhone1"]) ? $_REQUEST["ReceivePhone1"] : "";
$ProductOrderEmail = isset($_REQUEST["ProductOrderEmail"]) ? $_REQUEST["ProductOrderEmail"] : "";
$ReceiveZipCode = isset($_REQUEST["ReceiveZipCode"]) ? $_REQUEST["ReceiveZipCode"] : "";
$ReceiveAddr1 = isset($_REQUEST["ReceiveAddr1"]) ? $_REQUEST["ReceiveAddr1"] : "";
$ReceiveAddr2 = isset($_REQUEST["ReceiveAddr2"]) ? $_REQUEST["ReceiveAddr2"] : "";
$ReceiveMemo = isset($_REQUEST["ReceiveMemo"]) ? $_REQUEST["ReceiveMemo"] : "";

$ProductOrderState = isset($_REQUEST["ProductOrderState"]) ? $_REQUEST["ProductOrderState"] : "";
$ProductOrderShipState = isset($_REQUEST["ProductOrderShipState"]) ? $_REQUEST["ProductOrderShipState"] : "";
$ProductOrderShipNumber = isset($_REQUEST["ProductOrderShipNumber"]) ? $_REQUEST["ProductOrderShipNumber"] : "";

$CancelDateTime = isset($_REQUEST["CancelDateTime"]) ? $_REQUEST["CancelDateTime"] : "";
$ShipDateTime = isset($_REQUEST["ShipDateTime"]) ? $_REQUEST["ShipDateTime"] : "";


$Sql = " update ProductOrders set ";
	$Sql .= " ReceiveName = :ReceiveName, ";
	$Sql .= " ReceivePhone1 = HEX(AES_ENCRYPT(:ReceivePhone1, :EncryptionKey)), ";
	$Sql .= " ProductOrderEmail = :ProductOrderEmail, ";
	$Sql .= " ReceiveZipCode = :ReceiveZipCode, ";
	$Sql .= " ReceiveAddr1 = :ReceiveAddr1, ";
	$Sql .= " ReceiveAddr2 = :ReceiveAddr2, ";
	$Sql .= " ReceiveMemo = :ReceiveMemo, ";

	$Sql .= " ProductOrderState = :ProductOrderState, ";
	$Sql .= " ProductOrderShipState = :ProductOrderShipState, ";
	$Sql .= " ProductOrderShipNumber = :ProductOrderShipNumber, ";

	if ($CancelDateTime!=""){
		$Sql .= " CancelDateTime = :CancelDateTime, ";
	}else{
		$Sql .= " CancelDateTime = null, ";
	}
	if ($ShipDateTime!=""){
		$Sql .= " ShipDateTime = :ShipDateTime, ";
	}else{
		$Sql .= " ShipDateTime = null, ";
	}

	$Sql .= " ProductOrderModiDateTime = now() ";
$Sql .= " where ProductOrderID = :ProductOrderID ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ReceiveName', $ReceiveName);
$Stmt->bindParam(':ReceivePhone1', $ReceivePhone1);
$Stmt->bindParam(':ProductOrderEmail', $ProductOrderEmail);
$Stmt->bindParam(':ReceiveZipCode', $ReceiveZipCode);
$Stmt->bindParam(':ReceiveAddr1', $ReceiveAddr1);
$Stmt->bindParam(':ReceiveAddr2', $ReceiveAddr2);
$Stmt->bindParam(':ReceiveMemo', $ReceiveMemo);

$Stmt->bindParam(':ProductOrderState', $ProductOrderState);
$Stmt->bindParam(':ProductOrderShipState', $ProductOrderShipState);
$Stmt->bindParam(':ProductOrderShipNumber', $ProductOrderShipNumber);

if ($CancelDateTime!=""){
	$Stmt->bindParam(':CancelDateTime', $CancelDateTime);
}
if ($ShipDateTime!=""){
	$Stmt->bindParam(':ShipDateTime', $ShipDateTime);
}

$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->bindParam(':ProductOrderID', $ProductOrderID);
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
//parent.$.fn.colorbox.close();
parent.location.reload();
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

