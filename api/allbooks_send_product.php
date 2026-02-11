<?php
//================================================================================================================
//이 파일은 crontab 에서 매시 정각에 실행 됩니다.
//================================================================================================================

$ProductSellerID = 2;//올북스 아이디
$ProductCategoryID = 2;//올북스 교재 카테고리 아이디

//====================================================== DB ======================================================
$ActionMode = "Real";//리얼모드
//$ActionMode = "Test";//테스트 모드

if ($ActionMode=="Test"){
	$DbHost = "localhost";
	$DbName = "mangoi_dev";
	$DbUser = "mangoi";
	$DbPass = "mi!@#2019";

}else{

	$DbHost = "localhost";
	$DbName = "mangoi";
	$DbUser = "mangoi";
	$DbPass = "mi!@#2019";
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


$url = 'https://www.allpod.net/api/mangoi_product';
$json_string = file_get_contents($url);
$R = json_decode($json_string, true);

$ProductOrderFromAllbooksBooksText = $json_string;


$R_Count = 0;
foreach ($R as $GetRow) {
	$R_Count++;
}

if ($R_Count>0){
	//보유 도서를 일단 다 지운다
	$Sql = "delete from ProductSellerBooks where ProductSellerID=:ProductSellerID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductSellerID', $ProductSellerID);
	$Stmt->execute();
	$Stmt = null;
	//보유 도서를 일단 다 지운다
}

foreach ($R as $GetRow) {
    
	echo $GetRow['no'];
	echo ' , ';
    echo $GetRow['name'];
    echo ' , ';
    echo $GetRow['isbn'];
    echo ' , ';
    echo $GetRow['price'];
    echo '<br />';

	$ProductSellerBookID = trim($GetRow['no']);
	$ProductName = trim($GetRow['name']);
	$ProductISBN = trim($GetRow['isbn']);
	$ProductCostPrice = trim($GetRow['price']);

	//보유 도서를 새로 넣는다
	$Sql = " insert into ProductSellerBooks ( ";
		$Sql .= " ProductSellerID, ";
		$Sql .= " ProductSellerBookID, ";
		$Sql .= " ProductSellerBookRegDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :ProductSellerID, ";
		$Sql .= " :ProductSellerBookID, ";
		$Sql .= " now() ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductSellerID', $ProductSellerID);
	$Stmt->bindParam(':ProductSellerBookID', $ProductSellerBookID);
	$Stmt->execute();
	$Stmt = null;
	//보유 도서를 새로 넣는다


	$Sql = "select 
				count(*) as ProductCount
			from Products A 
				inner join ProductCategories B on A.ProductCategoryID=B.ProductCategoryID 
			where 
				B.ProductSellerID=:ProductSellerID
				and A.ProductSellerBookID=:ProductSellerBookID 
			";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductSellerID', $ProductSellerID);
	$Stmt->bindParam(':ProductSellerBookID', $ProductSellerBookID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$ProductCount = $Row["ProductCount"];

	if ($ProductCount==0){


		$Sql = "select ifnull(Max(ProductOrder),0) as ProductOrder from Products";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$ProductOrder = $Row["ProductOrder"]+1;
		
		$ProductMemo = "";
		$ProductViewPrice = 0;
		$ProductPrice = 0;
		$ProductImageFileRealName = "";
		$ProductImageFileName = "";
		$ProductState = 2;
		$ProductView = 1;//실시간 올북스 보유

		$Sql = " insert into Products ( ";
			$Sql .= " ProductCategoryID, ";
			$Sql .= " ProductSellerBookID, ";
			$Sql .= " ProductISBN, ";
			$Sql .= " ProductName, ";
			$Sql .= " ProductMemo, ";
			$Sql .= " ProductImageFileName, ";
			$Sql .= " ProductImageFileRealName, ";
			$Sql .= " ProductCostPrice, ";
			$Sql .= " ProductViewPrice, ";
			$Sql .= " ProductPrice, ";
			$Sql .= " ProductState, ";
			$Sql .= " ProductView, ";
			$Sql .= " ProductOrder, ";
			$Sql .= " ProductRegDateTime, ";
			$Sql .= " ProductModiDateTime ";
		$Sql .= " ) values ( ";
			$Sql .= " :ProductCategoryID, ";
			$Sql .= " :ProductSellerBookID, ";
			$Sql .= " :ProductISBN, ";
			$Sql .= " :ProductName, ";
			$Sql .= " :ProductMemo, ";
			$Sql .= " :ProductImageFileName, ";
			$Sql .= " :ProductImageFileRealName, ";
			$Sql .= " :ProductCostPrice, ";
			$Sql .= " :ProductViewPrice, ";
			$Sql .= " :ProductPrice, ";
			$Sql .= " :ProductState, ";
			$Sql .= " :ProductView, ";
			$Sql .= " :ProductOrder, ";
			$Sql .= " now(), ";
			$Sql .= " now() ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ProductCategoryID', $ProductCategoryID);
		$Stmt->bindParam(':ProductSellerBookID', $ProductSellerBookID);
		$Stmt->bindParam(':ProductISBN', $ProductISBN);
		$Stmt->bindParam(':ProductName', $ProductName);
		$Stmt->bindParam(':ProductMemo', $ProductMemo);
		$Stmt->bindParam(':ProductImageFileName', $ProductImageFileName);
		$Stmt->bindParam(':ProductImageFileRealName', $ProductImageFileRealName);
		$Stmt->bindParam(':ProductCostPrice', $ProductCostPrice);
		$Stmt->bindParam(':ProductViewPrice', $ProductViewPrice);
		$Stmt->bindParam(':ProductPrice', $ProductPrice);
		$Stmt->bindParam(':ProductState', $ProductState);
		$Stmt->bindParam(':ProductView', $ProductView);
		$Stmt->bindParam(':ProductOrder', $ProductOrder);
		$Stmt->execute();
		$Stmt = null;

	}else{

		$ProductView = 1;//실시간 올북스 보유

		$Sql = " update Products set ";
			$Sql .= " ProductISBN = :ProductISBN, ";
			$Sql .= " ProductName = :ProductName, ";
			$Sql .= " ProductCostPrice = :ProductCostPrice, ";
			$Sql .= " ProductView = :ProductView, ";
			$Sql .= " ProductModiDateTime = now() ";
		$Sql .= " where ProductSellerBookID = :ProductSellerBookID";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ProductISBN', $ProductISBN);
		$Stmt->bindParam(':ProductName', $ProductName);
		$Stmt->bindParam(':ProductCostPrice', $ProductCostPrice);
		$Stmt->bindParam(':ProductView', $ProductView);
		$Stmt->bindParam(':ProductSellerBookID', $ProductSellerBookID);
		$Stmt->execute();
		$Stmt = null;

	}

}

if ($R_Count>0){
	//보유 도서에 없는 도서는 재고없음으로 변경한다.
	$Sql = " update Products set ";
		$Sql .= " ProductView = 0, ";
		$Sql .= " ProductModiDateTime = now() ";
	$Sql .= " where 
				ProductCategoryID=:ProductCategoryID
				and ProductSellerBookID not in (select ProductSellerBookID from ProductSellerBooks where ProductSellerID=:ProductSellerID)
		";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductCategoryID', $ProductCategoryID);
	$Stmt->bindParam(':ProductSellerID', $ProductSellerID);
	$Stmt->execute();
	$Stmt = null;
	//보유 도서에 없는 도서는 재고없음으로 변경한다.
}



//============= 크론탭 로그 남기기 =============
$Sql = " insert into ProductOrderFromAllbooksBooksLogs (ProductOrderFromAllbooksBooksText, ProductOrderFromAllbooksBooksLogDateTime) values (:ProductOrderFromAllbooksBooksText, now()) ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductOrderFromAllbooksBooksText', $ProductOrderFromAllbooksBooksText);
$Stmt->execute();
$Stmt = null;
//============= 크론탭 로그 남기기 =============



$DbConn = null;
?>
