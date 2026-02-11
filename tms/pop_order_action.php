 <?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$OrderID = isset($_REQUEST["OrderID"]) ? $_REQUEST["OrderID"] : "";
$OrderState = isset($_REQUEST["OrderState"]) ? $_REQUEST["OrderState"] : "";



$Sql = "
		select 
				A.*
		from Orders A 
		where A.OrderID=:OrderID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':OrderID', $OrderID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$OrderDateTime = $Row["OrderDateTime"];
$PaymentDateTime = $Row["PaymentDateTime"];
$CancelRequestDateTime = $Row["CancelRequestDateTime"];
$CancelDateTime = $Row["CancelDateTime"];
$RefundRequestDateTime = $Row["RefundRequestDateTime"];
$RefundDateTime = $Row["RefundDateTime"];


$Sql = " update Orders set ";
	
	if ($OrderState==21 && $PaymentDateTime==""){
		$Sql .= " PaymentDateTime = now(), ";
	}
	if ($OrderState==31 && $CancelRequestDateTime==""){
		$Sql .= " CancelRequestDateTime = now(), ";
	}
	if ($OrderState==33 && $CancelDateTime==""){
		$Sql .= " CancelDateTime = now(), ";
	}
	if ($OrderState==41 && $RefundRequestDateTime==""){
		$Sql .= " RefundRequestDateTime = now(), ";
	}
	if ($OrderState==43 && $RefundDateTime==""){
		$Sql .= " RefundDateTime = now(), ";
	}

	$Sql .= " OrderModiDateTime = now(), ";
	$Sql .= " OrderState = :OrderState ";
$Sql .= " where OrderID = :OrderID ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':OrderID', $OrderID);
$Stmt->bindParam(':OrderState', $OrderState);
$Stmt->execute();
$Stmt = null;






include_once('./inc_header.php');
?>
</head>
<body>
<script>
//parent.$.fn.colorbox.close();
parent.location.reload();
</script>
<?php
include_once('./inc_footer.php'); 
?>
</body>
</html>
<?
include_once('../includes/dbclose.php');
?>


