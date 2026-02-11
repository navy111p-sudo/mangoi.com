<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/password_hash.php');


$err_num = 0;
$err_msg = "";

$FromMyPageMemberID = isset($_REQUEST["FromMyPageMemberID"]) ? $_REQUEST["FromMyPageMemberID"] : "";


$Sql = "select MemberLoginID from Members where MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $FromMyPageMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$ApplyMemberLoginID = $Row["MemberLoginID"];

//$ApplyMemberLoginID = isset($_REQUEST["ApplyMemberLoginID"]) ? $_REQUEST["ApplyMemberLoginID"] : "";
//$ApplyMemberLoginPW = isset($_REQUEST["ApplyMemberLoginPW"]) ? $_REQUEST["ApplyMemberLoginPW"] : "";
//$ApplyRememberID = isset($_REQUEST["ApplyRememberID"]) ? $_REQUEST["ApplyRememberID"] : "";
$RedirectUrl = isset($_REQUEST["RedirectUrl"]) ? $_REQUEST["RedirectUrl"] : "";


		
$Sql = "select A.MemberLevelID  
			from Members A 
			where MemberState=1 and  MemberLoginID=:ApplyMemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$MemberLevelID = $Row["MemberLevelID"];

if ($MemberLevelID<=15) {//강사 이상의 권한
	setcookie("LoginAdminID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);
	setcookie("LinkLoginAdminID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);
}else{
	setcookie("LoginAdminID", "", 0, "/", ".".$DefaultDomain2);
	setcookie("LinkLoginAdminID", "", 0, "/", ".".$DefaultDomain2);
}

setcookie("LoginMemberID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);
setcookie("LinkLoginMemberID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);

$Sql = "update Members set LastLoginDateTime=now() where MemberLoginID=:ApplyMemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
$Stmt->execute();
$Stmt = null;




if ($err_num != 0){
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<?if ($DomainSiteID==5){?>
<title>(주)englishtell</title>
<?}else{?>
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="../uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="../uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<?}?>
</head>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
<?
include_once('../includes/common_analytics.php');
?>
</body>
</html>
<?php
}

include_once('../includes/dbclose.php');

if ($err_num == 0){
	if ($RedirectUrl!=""){
		header("Location: $RedirectUrl"); 
		exit;
	} else {
		header("Location: mypage_study_room.php");
		exit;
	}
}

?>