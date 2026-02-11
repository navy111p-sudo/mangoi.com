<?
//================================================================================================================
//이 파일은 crontab 에서 매시 정각에 실행 됩니다.
//================================================================================================================

$ProductSellerID = 2;//올북스 아이디
$ProductOrderSendMin = 120;//결제 후 2시간이 경과하면 올북스에 전송한다.
//$ProductOrderSendMin = 0;


//====================================================== DB ======================================================
$ActionMode = "Real";//리얼모드
//$ActionMode = "Test";//테스트 모드

if ($ActionMode=="Test"){
	$DbHost = "localhost";
	$DbName = "mangoi_dev";
	$DbUser = "mangoi";
	$DbPass = "mi!@#2019";

	$RemoteHost1 = "ssl://www.eptest.org";
	$RemoteHost2 = "www.eptest.org";
	$RemotePath = "/api_mangoi/____allbooks_send_order_action_sample.php";
	$RemotePort = 443;

}else{

	$DbHost = "localhost";
	$DbName = "mangoi";
	$DbUser = "mangoi";
	$DbPass = "mi!@#2019";

	$RemoteHost1 = "ssl://www.allpod.net";
	$RemoteHost2 = "www.allpod.net";
	$RemotePath = "/api/mangoi_order";//올북스에서 알려준 주소를 입력해준다.
	$RemotePort = 443;
}


try {
	$DbConn = new PDO("mysql:host=$DbHost;dbname=$DbName;charset=utf8", $DbUser, $DbPass);
	$DbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
	echo "Connection failed: " . $e->getMessage();
}

//====================================================== DB ======================================================
//================================================================================================================
$EncryptionKey = md5("kr.ahsol");//절대 변경 불가(변경되면 회원정보 복구 불가)
//================================================================================================================



$ProductOrderToAllbooksText = "";
$SqlWhereAdd = "";
if ($ProductOrderSendMin>0){
	$SqlWhereAdd = " and timestampdiff(minute, A.PaymentDateTime, now())>=".$ProductOrderSendMin." ";
}



$Sql = "select 
			A.*,
			AES_DECRYPT(UNHEX(A.ReceivePhone1),'$EncryptionKey') as DecReceivePhone1,
			AES_DECRYPT(UNHEX(A.ReceivePhone2),'$EncryptionKey') as DecReceivePhone2
		from ProductOrders A 

		where 
			A.ProductSellerProductOrderSendCount<1 
			and A.ProductOrderState=21 
			and A.ProductSellerID=$ProductSellerID 
			".$SqlWhereAdd."
		order by A.ProductOrderID asc";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ProductOrderCount = 1;
while($Row = $Stmt->fetch()) {

	$ProductOrderID = $Row["ProductOrderID"];;

	$OrderNumber = $Row["ProductOrderNumber"];
	$OrderTitle = $Row["ProductOrderName"];
	$MemberName = $Row["MemberName"];
	$RcMemberName = $Row["ReceiveName"];
	$RcPhone1 = $Row["DecReceivePhone1"];
	$RcPhone2 = $Row["DecReceivePhone2"];
	$RcZipCode = $Row["ReceiveZipCode"];
	$RcAddr1 = $Row["ReceiveAddr1"];
	$RcAddr2 = $Row["ReceiveAddr2"];
	$RcMemo = $Row["ReceiveMemo"];


	$Sql2 = "select 
				A.*
			from ProductOrderDetails A 
			where 
				A.ProductOrderID=:ProductOrderID 
				and A.ProductOrderDetailState=1 
			order by A.ProductOrderDetailID asc";

	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':ProductOrderID', $ProductOrderID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

	
	$OrderProducts = "";
	$ListCount = 0;
	while($Row2 = $Stmt2->fetch()) {
		$ProductSellerBookID = $Row2["ProductSellerBookID"];
		$ProductName = $Row2["ProductName"];
		$ProductCount = $Row2["ProductCount"];

		if ($ListCount>0){
			$OrderProducts = $OrderProducts . "/**/";
		}

		$OrderProducts = $OrderProducts . $ProductSellerBookID . "/*/" . $ProductName . "/*/" . $ProductCount;

		
		$ListCount++;
	}
	$Stmt2 = null;

	$OrderProductCount = $ListCount;


	// post 전송 ===========================
	$param = "OrderNumber=".$OrderNumber;
	$param .= "&OrderTitle=".$OrderTitle;
	$param .= "&MemberName=".$MemberName;
	$param .= "&RcMemberName=".$RcMemberName;
	$param .= "&RcPhone1=".$RcPhone1;
	$param .= "&RcPhone2=".$RcPhone2;
	$param .= "&RcZipCode=".$RcZipCode;
	$param .= "&RcAddr1=".$RcAddr1;
	$param .= "&RcAddr2=".$RcAddr2;
	$param .= "&RcMemo=".$RcMemo;
	$param .= "&OrderProducts=".$OrderProducts;
	$param .= "&OrderProductCount=".$OrderProductCount;


	$fp = @fsockopen($RemoteHost1,$RemotePort,$errno,$errstr,30);
	$return = "";
	if (!$fp) {
		echo $errstr."(".$errno.")";
	} else {
		fputs($fp, "POST ".$RemotePath." HTTP/1.1\r\n");
		fputs($fp, "Host: ".$RemoteHost2."\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ".strlen($param)."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $param."\r\n\r\n");
		while(!feof($fp)) $return .= fgets($fp,4096);
	}
	fclose ($fp);
	// post 전송 ===========================

	if ($ProductOrderCount>1){
		$ProductOrderToAllbooksText = $ProductOrderToAllbooksText . "\n";
	}
	$ProductOrderToAllbooksText = $ProductOrderToAllbooksText . $param;

	

	//전송 상태 업데이트 =========================== 
	$return = str_replace("\r", "", $return);
	$return = str_replace("\n", "", $return);
	$return = str_replace("\t", "", $return);
	$return = trim($return);
	
	echo "SEND : " . $ProductOrderID . "--".$return."<br>";
	
	if (substr($return,-2)=="OK") {
		echo "OKOK : " . $ProductOrderID . "--".$return."<br><br>";
		
		$Sql2 = " update ProductOrders set ";
			$Sql2 .= " ProductSellerProductOrderSendCount = ProductSellerProductOrderSendCount+1, ";
			$Sql2 .= " ProductSellerProductOrderSendDateTime = now(), ";
			$Sql2 .= " ProductOrderModiDateTime = now() ";
		$Sql2 .= " where ProductOrderID = :ProductOrderID ";

		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':ProductOrderID', $ProductOrderID);
		$Stmt2->execute();
		$Stmt2 = null;
		
		
	}
	//전송 상태 업데이트 =========================== 


	$ProductOrderCount++;
}
$Stmt = null;



//============= 크론탭 로그 남기기 =============
$Sql = " insert into ProductOrderToAllbooksLogs (ProductOrderToAllbooksText, ProductOrderToAllbooksLogDateTime) values (:ProductOrderToAllbooksText, now()) ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductOrderToAllbooksText', $ProductOrderToAllbooksText);
$Stmt->execute();
$Stmt = null;
//============= 크론탭 로그 남기기 =============


$DbConn = null;
?>