<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$SearchProductSellerID = isset($_REQUEST["SearchProductSellerID"]) ? $_REQUEST["SearchProductSellerID"] : "";
$SearchProductCategoryID = isset($_REQUEST["SearchProductCategoryID"]) ? $_REQUEST["SearchProductCategoryID"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";



$DivSearchProductList = "";


$AddSqlWhere = " 1=1 ";

$AddSqlWhere = $AddSqlWhere . " and A.ProductState=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.ProductView=1 ";
$AddSqlWhere = $AddSqlWhere . " and B.ProductCategoryState=1 ";

if ($SearchText!=""){
	$AddSqlWhere = $AddSqlWhere . " and A.ProductName like '%".$SearchText."%' ";
}

if ($SearchProductSellerID!=""){
	$AddSqlWhere = $AddSqlWhere . " and B.ProductSellerID=$SearchProductSellerID ";
}

if ($SearchProductCategoryID!=""){
	$AddSqlWhere = $AddSqlWhere . " and A.ProductCategoryID=$SearchProductCategoryID ";
}


$Sql = "select 
			count(*) TotalRowCount 
		from Products A 
			inner join ProductCategories B on A.ProductCategoryID=B.ProductCategoryID 
			inner join ProductSellers C on B.ProductSellerID=C.ProductSellerID 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalRowCount = $Row["TotalRowCount"];


$Sql = "
		select 
			A.*,
			B.ProductCategoryName,
			C.ProductSellerName,
			(select count(*) from ProductOrderCartDetails AA inner join ProductOrderCarts BB on AA.ProductOrderCartID=BB.ProductOrderCartID where BB.MemberID=$MemberID and AA.ProductID=A.ProductID) as ProductCartCount,
			(select count(*) from ProductOrderDetails AA inner join ProductOrders BB on AA.ProductOrderID=BB.ProductOrderID where BB.MemberID=$MemberID and (BB.ProductOrderState=11 or BB.ProductOrderState=21 ) and AA.ProductID=A.ProductID and AA.ProductOrderDetailState=1 ) as ProductOrderCount
		from Products A 
			inner join ProductCategories B on A.ProductCategoryID=B.ProductCategoryID 
			inner join ProductSellers C on B.ProductSellerID=C.ProductSellerID 
		where ".$AddSqlWhere." 
		order by A.ProductName asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



$DivSearchProductList .= "<table class=\"uk-table uk-table-align-vertical\">";
$DivSearchProductList .= "	<thead>";
$DivSearchProductList .= "		<tr>";
$DivSearchProductList .= "			<th width=\"6%\">NO</th>";
$DivSearchProductList .= "			<th width=\"17%\">구분</th>";
$DivSearchProductList .= "			<th width=\"17%\">그룹명</th>";
$DivSearchProductList .= "			<th>교재명</th>";
$DivSearchProductList .= "			<th width=\"15%\">구매이력</th>";
$DivSearchProductList .= "			<th width=\"10%\">장바구니</th>";
$DivSearchProductList .= "		</tr>";
$DivSearchProductList .= "	</thead>";
$DivSearchProductList .= "	<tbody>";



$ListCount = 1;
while($Row = $Stmt->fetch()) {


	$ProductCategoryID = $Row["ProductCategoryID"];
	$ProductID = $Row["ProductID"];
	$ProductName = $Row["ProductName"];
	$ProductImageFileName = $Row["ProductImageFileName"];
	$ProductImageFileRealName = $Row["ProductImageFileRealName"];
	$ProductViewPrice = $Row["ProductViewPrice"];
	$ProductPrice = $Row["ProductPrice"];
	$ProductCategoryName = $Row["ProductCategoryName"];
	$ProductSellerName = $Row["ProductSellerName"];

	$ProductCartCount = $Row["ProductCartCount"];
	$ProductOrderCount = $Row["ProductOrderCount"];

	if ($ProductOrderCount>0){
		$StrProductOrderCount = "구매했음";
	}else{
		$StrProductOrderCount = "없음";
	}
	

	$DivSearchProductList .= "	<tr>";
	$DivSearchProductList .= "		<td class=\"uk-text-nowrap uk-table-td-center\">".$ListCount."</td>";
	$DivSearchProductList .= "		<td class=\"uk-text-nowrap uk-table-td-center\">".$ProductSellerName."</td>";
	$DivSearchProductList .= "		<td class=\"uk-text-nowrap uk-table-td-center\">".$ProductCategoryName."</td>";
	$DivSearchProductList .= "		<td class=\"uk-text-nowrap\">".$ProductName."</td>";
	$DivSearchProductList .= "		<td class=\"uk-text-nowrap uk-table-td-center\">".$StrProductOrderCount."</td>";
	if ($ProductCartCount>0){
		$DivSearchProductList .= "		<td class=\"uk-text-nowrap uk-table-td-center\"><a href=\"javascript:SelectProductDel(".$ProductID.");\" style=\"color:#cc0000;\">삭제</a></td>";
	}else{
		$DivSearchProductList .= "		<td class=\"uk-text-nowrap uk-table-td-center\"><a href=\"javascript:SelectProduct(".$ProductID.");\">선택</a></td>";
	}
	$DivSearchProductList .= "	</tr>";

	$ListCount ++;

}
$Stmt = null;



$DivSearchProductList .= "	</tbody>";
$DivSearchProductList .= "</table>";




$ArrValue["DivSearchProductList"] = $DivSearchProductList;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>