<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');


$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";

$Sql = "select A.MemberLoginID from Members A where A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$MemberLoginID = $Row["MemberLoginID"];


// 본계정 아이디는 학원장이여야함
//setcookie("LoginMemberID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);
setcookie("LinkLoginMemberID", $MemberLoginID, time()+(3600*24*30), "/", ".".$DefaultDomain2);
setcookie("LinkLoginAdminID", $MemberLoginID, time()+(3600*24*30), "/", ".".$DefaultDomain2);

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
<?
include_once('./includes/common_body_top.php');
?>
<script>

	parent.location.href = "mypage.php";
	//location.href = "mypage.php";
</script>
