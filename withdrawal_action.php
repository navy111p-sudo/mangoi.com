<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');


$err_num = 0;
$err_msg = "";

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$WithdrawalText = isset($_REQUEST["WithdrawalText"]) ? $_REQUEST["WithdrawalText"] : "";


if ($MemberID!=$_LINK_MEMBER_ID_) {
	$err_num = 1;
	$err_msg = "잘못된 접근입니다.";
}else{


	$Sql = "update Members set 
					MemberState=3, 
					WithdrawalText='$WithdrawalText',
					WithdrawalDateTime=now()
					
					where MemberID=$MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt = null;

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
</body>
</html>
<?php
}

include_once('./includes/dbclose.php');


if ($err_num == 0){
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
alert("탈퇴처리 되었습니다. 감사합니다.");
location.href = "logout.php";
</script>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>
<?
}
?>





