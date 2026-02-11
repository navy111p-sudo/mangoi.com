<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$err_num = 0;
$err_msg = "";

$ClassOrderPayID = isset($_REQUEST["ClassOrderPayID"]) ? $_REQUEST["ClassOrderPayID"] : "";
$ClassOrderPayProgress = isset($_REQUEST["ClassOrderPayProgress"]) ? $_REQUEST["ClassOrderPayProgress"] : "";
$ClassOrderPayPaymentDateTime = isset($_REQUEST["ClassOrderPayPaymentDateTime"]) ? $_REQUEST["ClassOrderPayPaymentDateTime"] : "";
$ClassOrderPayCencelDateTime = isset($_REQUEST["ClassOrderPayCencelDateTime"]) ? $_REQUEST["ClassOrderPayCencelDateTime"] : "";

if ($ClassOrderPayPaymentDateTime==""){
	$ClassOrderPayPaymentDateTime = date("Y-m-d");
}

if ($ClassOrderPayCencelDateTime==""){
	$ClassOrderPayCencelDateTime = date("Y-m-d");
}

$Sql = "";
$Sql .= "update ClassOrderPays set ";
$Sql .= "	ClassOrderPayProgress=:ClassOrderPayProgress, ";

if ($ClassOrderPayProgress==1){
	$Sql .= "	ClassOrderPayPaymentDateTime=null, ";
	$Sql .= "	ClassOrderPayCencelDateTime=null, ";
}else if ($ClassOrderPayProgress==21){
	$Sql .= "	ClassOrderPayPaymentDateTime='".$ClassOrderPayPaymentDateTime."', ";
	$Sql .= "	ClassOrderPayCencelDateTime=null, ";
}else if ($ClassOrderPayProgress==33){
	$Sql .= "	ClassOrderPayCencelDateTime='".$ClassOrderPayCencelDateTime."', ";
}

$Sql .= "	ClassOrderPayModiDateTime=now() ";
$Sql .= "where ClassOrderPayID=:ClassOrderPayID ";


$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderPayProgress', $ClassOrderPayProgress);
$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
$Stmt->execute();
$Stmt = null;


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
parent.location.reload();
</script>
</body>
</html>
<?
}
?>