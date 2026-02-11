 <?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$TrnLanguageID = isset($_REQUEST["TrnLanguageID"]) ? $_REQUEST["TrnLanguageID"] : "";
$TrnLanguageName = isset($_REQUEST["TrnLanguageName"]) ? $_REQUEST["TrnLanguageName"] : "";
$TrnLanguageState = isset($_REQUEST["TrnLanguageState"]) ? $_REQUEST["TrnLanguageState"] : "";


if ($TrnLanguageID==""){

	$Sql = "select ifnull(Max(TrnLanguageOrder),0) as TrnLanguageOrder from TrnLanguages";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TrnLanguageOrder = $Row["TrnLanguageOrder"]+1;

	$Sql = " insert into TrnLanguages ( ";
		$Sql .= " TrnLanguageName, ";
		$Sql .= " TrnLanguageRegDateTime, ";
		$Sql .= " TrnLanguageModiDateTime, ";
		$Sql .= " TrnLanguageOrder, ";
		$Sql .= " TrnLanguageState ";
	$Sql .= " ) values ( ";
		$Sql .= " :TrnLanguageName, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :TrnLanguageOrder, ";
		$Sql .= " 1 ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TrnLanguageName', $TrnLanguageName);
	$Stmt->bindParam(':TrnLanguageOrder', $TrnLanguageOrder);

	$Stmt->execute();
	$TrnLanguageID = $DbConn->lastInsertId();
	$Stmt = null;

}else{

	$Sql = " update TrnLanguages set ";
		$Sql .= " TrnLanguageName = :TrnLanguageName, ";
		$Sql .= " TrnLanguageModiDateTime = now(), ";
		$Sql .= " TrnLanguageState = :TrnLanguageState ";
	$Sql .= " where TrnLanguageID = :TrnLanguageID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TrnLanguageName', $TrnLanguageName);
	$Stmt->bindParam(':TrnLanguageState', $TrnLanguageState);
	$Stmt->bindParam(':TrnLanguageID', $TrnLanguageID);
	$Stmt->execute();
	$Stmt = null;

}





include_once('./inc_header.php');
?>
</head>
<body>
<script>
//parent.$.fn.colorbox.close();
parent.location.reload();
</script>
<?php
include_once('./inc_footer.php'); 
?>
</body>
</html>
<?
include_once('../includes/dbclose.php');
?>


