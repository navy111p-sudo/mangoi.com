 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ProductID = isset($_REQUEST["ProductID"]) ? $_REQUEST["ProductID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";

$Sql = "
		select 
				B.ProductOrderCartID,
				A.ProductOrderCartDetailID
		from ProductOrderCartDetails A 
			inner join ProductOrderCarts B on A.ProductOrderCartID=B.ProductOrderCartID
		where A.ProductID=:ProductID and B.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductID', $ProductID);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ProductOrderCartID = $Row["ProductOrderCartID"];
$ProductOrderCartDetailID = $Row["ProductOrderCartDetailID"];


$Sql = " delete from ProductOrderCartDetails where ProductOrderCartDetailID=:ProductOrderCartDetailID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductOrderCartDetailID', $ProductOrderCartDetailID);
$Stmt->execute();
$Stmt = null;



$Sql = "
		select 
				count(*) as ProductOrderCartDetailCount
		from ProductOrderCartDetails A 
		where A.ProductOrderCartID=:ProductOrderCartID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ProductOrderCartDetailCount = $Row["ProductOrderCartDetailCount"];


if ($ProductOrderCartDetailCount==0){
	$Sql = " delete from ProductOrderCarts where ProductOrderCartID=:ProductOrderCartID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
	$Stmt->execute();
	$Stmt = null;
}



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