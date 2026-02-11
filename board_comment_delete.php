<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/board_config.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BoardCode = isset($_REQUEST["BoardCode"]) ? $_REQUEST["BoardCode"] : "";
$BoardContentID = isset($_REQUEST["BoardContentID"]) ? $_REQUEST["BoardContentID"] : "";
$BoardCommentID = isset($_REQUEST["BoardCommentID"]) ? $_REQUEST["BoardCommentID"] : "";
$CheckSumRequest = isset($_COOKIE["BoardCheckSum"]) ? $_COOKIE["BoardCheckSum"] : "";
$CheckSumResult = md5($BoardCommentID);


setcookie("BoardCheckSum","");

if ($CheckSumRequest==$CheckSumResult){
	$Sql = "delete from BoardComments where BoardCommentID=:BoardCommentID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardCommentID', $BoardCommentID);
	$Stmt->execute();
	$Stmt = null;
}else{
	$err_num = 1;
	$err_msg = "잘못된 접근 입니다.";
}




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


