<?
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


$OrderNumber = isset($_REQUEST["OrderNumber"]) ? $_REQUEST["OrderNumber"] : "";
$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
$RcMemberName = isset($_REQUEST["RcMemberName"]) ? $_REQUEST["RcMemberName"] : "";
$RcPhone1 = isset($_REQUEST["RcPhone1"]) ? $_REQUEST["RcPhone1"] : "";
$RcPhone2 = isset($_REQUEST["RcPhone2"]) ? $_REQUEST["RcPhone2"] : "";
$RcZipCode = isset($_REQUEST["RcZipCode"]) ? $_REQUEST["RcZipCode"] : "";
$RcAddr1 = isset($_REQUEST["RcAddr1"]) ? $_REQUEST["RcAddr1"] : "";
$RcAddr2 = isset($_REQUEST["RcAddr2"]) ? $_REQUEST["RcAddr2"] : "";
$RcMemo = isset($_REQUEST["RcMemo"]) ? $_REQUEST["RcMemo"] : "";

$OrderProducts = isset($_REQUEST["OrderProducts"]) ? $_REQUEST["OrderProducts"] : "";//상품 정보 : 상품아이디/*/상품명/*/수량 형식, 여러개 일경우 '/**/'로 구분, 상품아이디/*/상품명/*/수량/**/상품아이디/*/상품명/*/수량/**/상품아이디/*/상품명/*/수량
$OrderProductCount = isset($_REQUEST["OrderProductCount"]) ? $_REQUEST["OrderProductCount"] : "";//상품 수

$OrderProducts = "/**/".$OrderProducts;
$ArrOrderProduct = explode("/**/", $OrderProducts);

for ($ii=1;$ii<=count($ArrOrderProduct)-1;$ii++){

	if ($ArrOrderProduct[$ii]!="" && strpos($ArrOrderProduct[$ii], "/*/")!==false){

		$ArrArrOrderProduct =  explode("/*/",$ArrOrderProduct[$ii]);
		
		$ProductID = $ArrArrOrderProduct[0];
		$ProductName = $ArrArrOrderProduct[1];
		$ProductCount = $ArrArrOrderProduct[2];


		$Sql = "
			insert into ProductOrderAllbooksSamples_____
			(OrderNumber, MemberName, RcMemberName, RcPhone1, RcPhone2, RcZipCode, RcAddr1, RcAddr2, ProductID, ProductName, ProductCount, RegDateTime)
			values
			(:OrderNumber, :MemberName, :RcMemberName, :RcPhone1, :RcPhone2, :RcZipCode, :RcAddr1, :RcAddr2, :ProductID, :ProductName, :ProductCount, now())
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':OrderNumber', $OrderNumber);
		$Stmt->bindParam(':MemberName', $MemberName);
		$Stmt->bindParam(':RcMemberName', $RcMemberName);
		$Stmt->bindParam(':RcPhone1', $RcPhone1);
		$Stmt->bindParam(':RcPhone2', $RcPhone2);
		$Stmt->bindParam(':RcZipCode', $RcZipCode);
		$Stmt->bindParam(':RcAddr1', $RcAddr1);
		$Stmt->bindParam(':RcAddr2', $RcAddr2);
		$Stmt->bindParam(':ProductID', $ProductID);
		$Stmt->bindParam(':ProductName', $ProductName);
		$Stmt->bindParam(':ProductCount', $ProductCount);
		$Stmt->execute();
		$Stmt = null;


		
	
	}

}

$DbConn = null;

echo "OK";
?>
