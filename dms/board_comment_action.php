<?php
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
$BoardCommentMemberID = isset($_REQUEST["BoardCommentMemberID"]) ? $_REQUEST["BoardCommentMemberID"] : "";
$BoardCommentWriterName = isset($_REQUEST["BoardCommentWriterName"]) ? $_REQUEST["BoardCommentWriterName"] : "";
$BoardComment = isset($_REQUEST["BoardComment"]) ? $_REQUEST["BoardComment"] : "";


$Sql = " insert into BoardComments ( ";
	$Sql .= " BoardContentID, ";
	$Sql .= " BoardCommentMemberID, ";
	$Sql .= " BoardCommentWriterName, ";
	$Sql .= " BoardCommentWriterPW, ";
	$Sql .= " BoardComment, ";
	$Sql .= " BoardCommentRegDateTime ";
$Sql .= " ) values ( ";
	$Sql .= " :BoardContentID, ";
	$Sql .= " :BoardCommentMemberID, ";
	$Sql .= " :BoardCommentWriterName, ";
	$Sql .= " :BoardCommentWriterPW, ";
	$Sql .= " :BoardComment, ";
	$Sql .= " now() ";
$Sql .= " ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardContentID', $BoardContentID);
$Stmt->bindParam(':BoardCommentMemberID', $BoardCommentMemberID);
$Stmt->bindParam(':BoardCommentWriterName', $BoardCommentWriterName);
$Stmt->bindParam(':BoardCommentWriterPW', $BoardCommentWriterPW);
$Stmt->bindParam(':BoardComment', $BoardComment);
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
	header("Location: board_read.php?ListParam=$ListParam&BoardContentID=$BoardContentID&BoardCode=$BoardCode");
	exit;
}
?>





