<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');


header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = AllbooksList.xls" );
header( "Content-Description: PHP4 Generated Data" );
?>
<!doctype html>
<head>
<meta charset="utf-8">
</head>
<body>
<?php

$ProductSellerID = 2;//올북스

$Sql = "select 
				count(*) TotalRowCount 
		from Products A 
			inner join ProductCategories B on A.ProductCategoryID=B.ProductCategoryID 
		where A.ProductState=1 and B.ProductCategoryState=1 ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalRowCount = $Row["TotalRowCount"];



$Sql = "
		select 
			A.*,
			B.ProductCategoryName
		from Products A 
			inner join ProductCategories B on A.ProductCategoryID=B.ProductCategoryID 
		where A.ProductState<>0 and B.ProductCategoryState<>0 and B.ProductSellerID=".$ProductSellerID."
		order by B.ProductCategoryName asc, A.ProductName asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

?>




<table border="1">
<thead>
	<tr>
		<th>No</th>
		<th>DB번호(수정금지)</th>
		<th>ISBN</th>
		<th>그룹명</th>
		<th>교재명</th>
		<th>올북스원가</th>
		<th>판매가</th>
		<th>올북스 고유번호</th>
	</tr>
</thead>
<tbody>
	<?php
	$ListCount = 1;
	
	while($Row = $Stmt->fetch()) {

		$ListNumber = $TotalRowCount - $ListCount + 1;

		$ProductID = $Row["ProductID"];
		$ProductISBN = $Row["ProductISBN"];
		$ProductName = $Row["ProductName"];
		$ProductSellerBookID = $Row["ProductSellerBookID"];

		$ProductCostPrice = $Row["ProductCostPrice"];
		$ProductPrice = $Row["ProductPrice"];

		$ProductCategoryName = $Row["ProductCategoryName"];

	?>
	<tr>
		<td><?=$ListCount?></td>
		<td><?=$ProductID?></td>
		<td><?=$ProductISBN?></td>
		<td><?=$ProductCategoryName?></td>
		<td><?=$ProductName?></td>
		<td><?=$ProductCostPrice?></td>
		<td><?=$ProductPrice?></td>
		<td style="mso-number-format:'\@'"><?=$ProductSellerBookID?></td>
	</tr>
	<?php
		$ListCount ++;
	}
	$Stmt = null;
	?>
</tbody>
</table>

<?php
include_once('../includes/dbclose.php');
?>
</body>
</html>