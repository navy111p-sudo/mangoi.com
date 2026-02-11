<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/board_config.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BoardCode = isset($_REQUEST["BoardCode"]) ? $_REQUEST["BoardCode"] : "";
$BoardContentID = isset($_REQUEST["BoardContentID"]) ? $_REQUEST["BoardContentID"] : "";
$BoardCommentMemberID = isset($_REQUEST["BoardCommentMemberID"]) ? $_REQUEST["BoardCommentMemberID"] : "";
$BoardCommentWriterName = isset($_REQUEST["BoardCommentWriterName"]) ? $_REQUEST["BoardCommentWriterName"] : "";
$BoardCommentWriterPW = isset($_REQUEST["BoardCommentWriterPW"]) ? $_REQUEST["BoardCommentWriterPW"] : "";
$BoardComment = isset($_REQUEST["BoardComment"]) ? $_REQUEST["BoardComment"] : "";
$BoardCommentWriterPW = md5($BoardCommentWriterPW);


$Sql = " insert into BoardComments ( ";
	$Sql .= " BoardContentID, ";
	$Sql .= " BoardCommentMemberID, ";
	$Sql .= " BoardCommentWriterName, ";
	$Sql .= " BoardCommentWriterPW, ";
	$Sql .= " BoardComment, ";
	$Sql .= " BoardCommentRegDateTime ";
$Sql .= " ) values ( ";
	$Sql .= " :BoardContentID, ";
	$Sql .= " :BoardCommentMemberID', ";
	$Sql .= " :BoardCommentWriterName', ";
	$Sql .= " :BoardCommentWriterPW', ";
	$Sql .= " :BoardComment', ";
	$Sql .= " now() ";
$Sql .= " )";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardContentID', $BoardContentID);
$Stmt->bindParam(':BoardCommentMemberID', $BoardCommentMemberID);
$Stmt->bindParam(':BoardCommentWriterName', $BoardCommentWriterName);
$Stmt->bindParam(':BoardCommentWriterPW', $BoardCommentWriterPW);
$Stmt->bindParam(':BoardComment', $BoardComment);
$Stmt->execute();
$Stmt = null;

//echo $Sql;


if ($err_num != 0){
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>
<?php
}

include_once('./includes/dbclose.php');


if ($err_num == 0){
	header("Location: board_read.php?ListParam=$ListParam&BoardContentID=$BoardContentID&BoardCode=$BoardCode");
	exit;
}
?>


