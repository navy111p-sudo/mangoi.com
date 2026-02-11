<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";


$BoardCategoryID = isset($_REQUEST["BoardCategoryID"]) ? $_REQUEST["BoardCategoryID"] : "";
$BoardID = isset($_REQUEST["BoardID"]) ? $_REQUEST["BoardID"] : "";
$BoardCategoryName = isset($_REQUEST["BoardCategoryName"]) ? $_REQUEST["BoardCategoryName"] : "";


if ($BoardCategoryID==""){
	$Sql = "select ifnull(Max(BoardCategoryOrder),0) as BoardCategoryOrder from BoardCategories";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BoardCategoryOrder = $Row["BoardCategoryOrder"]+1;
}



if ($BoardCategoryID==""){

	$Sql = " insert into BoardCategories ( ";
	$Sql .= " BoardID, ";
	$Sql .= " BoardCategoryName, ";
	$Sql .= " BoardCategoryOrder ";
	$Sql .= " ) values ( ";
	$Sql .= " :BoardID, ";
	$Sql .= " :BoardCategoryName, ";
	$Sql .= " :BoardCategoryOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardID', $BoardID);
	$Stmt->bindParam(':BoardCategoryName', $BoardCategoryName);
	$Stmt->bindParam(':BoardCategoryOrder', $BoardCategoryOrder);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update BoardCategories set ";
		$Sql .= " BoardCategoryName = :BoardCategoryName ";
	$Sql .= " where BoardCategoryID = :BoardCategoryID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardCategoryName', $BoardCategoryName);
	$Stmt->bindParam(':BoardCategoryID', $BoardCategoryID);
	$Stmt->execute();
	$Stmt = null;

}


if ($err_num != 0){
	include_once('./_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
</body>
<?php
	include_once('./_footer.php');
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: board_category_list.php?BoardID=$BoardID"); 
	exit;
}
?>


