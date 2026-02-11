 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ProductID = isset($_REQUEST["ProductID"]) ? $_REQUEST["ProductID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";

$Sql = "
		select 
				B.ProductSellerID
		from Products A 
			inner join ProductCategories B on A.ProductCategoryID=B.ProductCategoryID 
		where A.ProductID=:ProductID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductID', $ProductID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ProductSellerID = $Row["ProductSellerID"];



$Sql = "
		select 
				A.ProductOrderCartID
		from ProductOrderCarts A 
		where A.ProductSellerID=:ProductSellerID and A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductSellerID', $ProductSellerID);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ProductOrderCartID = $Row["ProductOrderCartID"];
if (!$ProductOrderCartID){


	$Sql = "select ifnull(Max(ProductOrderCartOrder),0) as ProductOrderCartOrder from ProductOrderCarts";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	
	$ProductOrderCartOrder = $Row["ProductOrderCartOrder"]+1;
	$ProductOrderCartName = "망고아이 교재구매";
	$ProductOrderCartState = 2;

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
	$ProductOrderCartID = $DbConn->lastInsertId();
	$Stmt = null;

}


$RegMemberID = $_LINK_ADMIN_ID_;
$ModiMemberID = $_LINK_ADMIN_ID_;

$Sql = "select ifnull(Max(ProductCartDetailOrder),0) as ProductCartDetailOrder from ProductOrderCartDetails where ProductOrderCartID=$ProductOrderCartID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ProductCartDetailOrder = $Row["ProductCartDetailOrder"]+1;

	
$Sql = " insert into ProductOrderCartDetails ( ";
	$Sql .= " ProductOrderCartID, ";
	$Sql .= " ProductID, ";
	$Sql .= " ProductCount, ";
	$Sql .= " RegMemberID, ";
	$Sql .= " ModiMemberID, ";
	$Sql .= " ProductCartDetailRegDateTime, ";
	$Sql .= " ProductCartDetailModiDateTime, ";
	$Sql .= " ProductCartDetailOrder ";
$Sql .= " ) values ( ";
	$Sql .= " :ProductOrderCartID, ";
	$Sql .= " :ProductID, ";
	$Sql .= " 1, ";
	$Sql .= " :RegMemberID, ";
	$Sql .= " :ModiMemberID, ";
	$Sql .= " now(), ";
	$Sql .= " now(), ";
	$Sql .= " :ProductCartDetailOrder ";
$Sql .= " ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
$Stmt->bindParam(':ProductID', $ProductID);
$Stmt->bindParam(':RegMemberID', $RegMemberID);
$Stmt->bindParam(':ModiMemberID', $ModiMemberID);
$Stmt->bindParam(':ProductCartDetailOrder', $ProductCartDetailOrder);
$Stmt->execute();
$Stmt = null;



$ArrValue["CheckResult"] = "";

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>