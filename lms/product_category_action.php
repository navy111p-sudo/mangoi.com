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
$ProductCategoryID = isset($_REQUEST["ProductCategoryID"]) ? $_REQUEST["ProductCategoryID"] : "";
$ProductCategoryName = isset($_REQUEST["ProductCategoryName"]) ? $_REQUEST["ProductCategoryName"] : "";
$ProductCategoryMemo = isset($_REQUEST["ProductCategoryMemo"]) ? $_REQUEST["ProductCategoryMemo"] : "";
$ProductCategoryState = isset($_REQUEST["ProductCategoryState"]) ? $_REQUEST["ProductCategoryState"] : "";
$ProductCategoryView = isset($_REQUEST["ProductCategoryView"]) ? $_REQUEST["ProductCategoryView"] : "";


if ($ProductCategoryView!="1"){
	$ProductCategoryView = 0;
}

if ($ProductCategoryState!="1"){
	$ProductCategoryState = 2;
}


if ($ProductCategoryID==""){

	$Sql = "select ifnull(Max(ProductCategoryOrder),0) as ProductCategoryOrder from ProductCategories";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$ProductCategoryOrder = $Row["ProductCategoryOrder"]+1;

	$Sql = " insert into ProductCategories ( ";
		$Sql .= " ProductSellerID, ";
		$Sql .= " ProductCategoryName, ";
		$Sql .= " ProductCategoryMemo, ";
		$Sql .= " ProductCategoryRegDateTime, ";
		$Sql .= " ProductCategoryModiDateTime, ";
		$Sql .= " ProductCategoryState, ";
		$Sql .= " ProductCategoryView, ";
		$Sql .= " ProductCategoryOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :ProductSellerID, ";
		$Sql .= " :ProductCategoryName, ";
		$Sql .= " :ProductCategoryMemo, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :ProductCategoryState, ";
		$Sql .= " :ProductCategoryView, ";
		$Sql .= " :ProductCategoryOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductSellerID', $ProductSellerID);
	$Stmt->bindParam(':ProductCategoryName', $ProductCategoryName);
	$Stmt->bindParam(':ProductCategoryMemo', $ProductCategoryMemo);
	$Stmt->bindParam(':ProductCategoryState', $ProductCategoryState);
	$Stmt->bindParam(':ProductCategoryView', $ProductCategoryView);
	$Stmt->bindParam(':ProductCategoryOrder', $ProductCategoryOrder);
	$Stmt->execute();
	$ProductCategoryID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = " update ProductCategories set ";
		$Sql .= " ProductSellerID = :ProductSellerID, ";
		$Sql .= " ProductCategoryName = :ProductCategoryName, ";
		$Sql .= " ProductCategoryMemo = :ProductCategoryMemo, ";
		$Sql .= " ProductCategoryModiDateTime = now(), ";
		$Sql .= " ProductCategoryState = :ProductCategoryState, ";
		$Sql .= " ProductCategoryView = :ProductCategoryView ";
	$Sql .= " where ProductCategoryID = :ProductCategoryID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductSellerID', $ProductSellerID);
	$Stmt->bindParam(':ProductCategoryName', $ProductCategoryName);
	$Stmt->bindParam(':ProductCategoryMemo', $ProductCategoryMemo);
	$Stmt->bindParam(':ProductCategoryState', $ProductCategoryState);
	$Stmt->bindParam(':ProductCategoryView', $ProductCategoryView);
	$Stmt->bindParam(':ProductCategoryID', $ProductCategoryID);
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
?>
<script>
parent.$.fn.colorbox.close();
</script>
<?
}
?>


