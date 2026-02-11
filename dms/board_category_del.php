<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$BoardCategoryID = isset($_REQUEST["BoardCategoryID"]) ? $_REQUEST["BoardCategoryID"] : "";
$BoardID = isset($_REQUEST["BoardID"]) ? $_REQUEST["BoardID"] : "";


$Sql = "delete from BoardCategories where BoardCategoryID=:BoardCategoryID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardCategoryID', $BoardCategoryID);
$Stmt->execute();
$Stmt = null;

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