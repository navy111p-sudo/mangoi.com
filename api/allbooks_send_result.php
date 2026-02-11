<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ActionMode = "Real";//리얼모드
//$ActionMode = "Test";//테스트 모드
if ($ActionMode=="Test"){
	
	$DbConn = null;

	$DbHost = "localhost";
	$DbName = "mangoi_dev";
	$DbUser = "mangoi";
	$DbPass = "mi!@#2019";

	try {
		$DbConn = new PDO("mysql:host=$DbHost;dbname=$DbName;charset=utf8", $DbUser, $DbPass);
		$DbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e){
		echo "Connection failed: " . $e->getMessage();
	}

}


$ShipResults = isset($_REQUEST["ShipResults"]) ? $_REQUEST["ShipResults"] : "";
$ShipResultSendDateTime = isset($_REQUEST["ShipResultSendDateTime"]) ? $_REQUEST["ShipResultSendDateTime"] : "";
$ProductOrderFromAllbooksText = $ShipResults . "\n\n" . $ShipResultSendDateTime;


$ShipResults = "/**/".$ShipResults;
$ArrShipResults = explode("/**/", $ShipResults);

for ($ii=1;$ii<=count($ArrShipResults)-1;$ii++){//===AAA

	$StrArrShipResults = trim($ArrShipResults[$ii]);

	if ($StrArrShipResults!="" && strpos($StrArrShipResults, "/*/")!==false){//===BBB

		$ArrArrShipResults = explode("/*/", $StrArrShipResults);
		$OrderNumber = trim($ArrArrShipResults[0]);
		$ShipName = trim($ArrArrShipResults[1]);
		$ShipNumber = trim($ArrArrShipResults[2]);


		if ($OrderNumber!="" && $ShipNumber!=""){//===CCC

			//echo $OrderNumber." - " . $ShipName." - " . $ShipNumber . " <br> ";
	
			$Sql = " update ProductOrders set ";
				$Sql .= " ProductOrderShipState = 21, ";
				$Sql .= " ProductOrderShipNumber = :ProductOrderShipNumber, ";
				$Sql .= " ProductOrderShipName = :ProductOrderShipName, ";
				$Sql .= " ProductSellerProductOrderShipUpdateCount = ProductSellerProductOrderShipUpdateCount+1, ";
				$Sql .= " ProductSellerProductOrderShipResultSendDateTime = :ProductSellerProductOrderShipResultSendDateTime, ";
				$Sql .= " ShipDateTime = now(), ";
				$Sql .= " ProductSellerProductOrderShipUpdateDateTime = now(), ";
				$Sql .= " ProductOrderModiDateTime = now() ";
			$Sql .= " where ProductOrderNumber = :ProductOrderNumber ";
			$Sql .= "		and ProductSellerProductOrderShipUpdateCount<1 ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ProductOrderShipNumber', $ShipNumber);
			$Stmt->bindParam(':ProductOrderShipName', $ShipName);
			$Stmt->bindParam(':ProductSellerProductOrderShipResultSendDateTime', $ShipResultSendDateTime);
			$Stmt->bindParam(':ProductOrderNumber', $OrderNumber);
			$Stmt->execute();
			$Stmt = null;

	
		}//===CCC
	
	}//===BBB

}//===AAA



//============= 로그 남기기 =============
$Sql = " insert into ProductOrderFromAllbooksLogs (ProductOrderFromAllbooksText, ProductOrderFromAllbooksLogDateTime) values (:ProductOrderFromAllbooksText, now()) ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductOrderFromAllbooksText', $ProductOrderFromAllbooksText);
$Stmt->execute();
$Stmt = null;
//============= 로그 남기기 =============

include_once('../includes/dbclose.php');

echo "OK";
?>