<?
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./includes/board_config.php');

$err_num = 0;
$err_msg = "";


$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BoardCode = isset($_REQUEST["BoardCode"]) ? $_REQUEST["BoardCode"] : "";
$BoardContentID = isset($_REQUEST["BoardContentID"]) ? $_REQUEST["BoardContentID"] : "";
$BoardCommentID = isset($_REQUEST["BoardCommentID"]) ? $_REQUEST["BoardCommentID"] : "";




$Sql = "delete from BoardComments where BoardCommentID=:BoardCommentID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardCommentID', $BoardCommentID);
$Stmt->execute();
$Stmt = null;




if ($err_num != 0){
	include_once('./inc_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
</body>
<?
	include_once('./inc_footer.php');
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: board_read.php?ListParam=$ListParam&BoardContentID=$BoardContentID&BoardCode=$BoardCode");
	exit;
}
?>





