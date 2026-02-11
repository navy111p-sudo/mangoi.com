 <?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$TrnBrowserLocCodeID = isset($_REQUEST["TrnBrowserLocCodeID"]) ? $_REQUEST["TrnBrowserLocCodeID"] : "";
$TrnBrowserLocCode = isset($_REQUEST["TrnBrowserLocCode"]) ? $_REQUEST["TrnBrowserLocCode"] : "";
$TrnBrowserLocCodeName = isset($_REQUEST["TrnBrowserLocCodeName"]) ? $_REQUEST["TrnBrowserLocCodeName"] : "";
$TrnLanguageID = isset($_REQUEST["TrnLanguageID"]) ? $_REQUEST["TrnLanguageID"] : "";
$TrnBrowserLocCodeState = isset($_REQUEST["TrnBrowserLocCodeState"]) ? $_REQUEST["TrnBrowserLocCodeState"] : "";


if ($TrnBrowserLocCodeID==""){

	$Sql = "select ifnull(Max(TrnBrowserLocCodeOrder),0) as TrnBrowserLocCodeOrder from TrnBrowserLocCodes";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TrnBrowserLocCodeOrder = $Row["TrnBrowserLocCodeOrder"]+1;

	$Sql = " insert into TrnBrowserLocCodes ( ";
		$Sql .= " TrnLanguageID, ";
		$Sql .= " TrnBrowserLocCode, ";
		$Sql .= " TrnBrowserLocCodeName, ";
		$Sql .= " TrnBrowserLocCodeRegDateTime, ";
		$Sql .= " TrnBrowserLocCodeModiDateTime, ";
		$Sql .= " TrnBrowserLocCodeOrder, ";
		$Sql .= " TrnBrowserLocCodeState ";
	$Sql .= " ) values ( ";
		$Sql .= " :TrnLanguageID, ";
		$Sql .= " :TrnBrowserLocCode, ";
		$Sql .= " :TrnBrowserLocCodeName, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :TrnBrowserLocCodeOrder, ";
		$Sql .= " 1 ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TrnLanguageID', $TrnLanguageID);
	$Stmt->bindParam(':TrnBrowserLocCode', $TrnBrowserLocCode);
	$Stmt->bindParam(':TrnBrowserLocCodeName', $TrnBrowserLocCodeName);
	$Stmt->bindParam(':TrnBrowserLocCodeOrder', $TrnBrowserLocCodeOrder);

	$Stmt->execute();
	$TrnBrowserLocCodeID = $DbConn->lastInsertId();
	$Stmt = null;

}else{

	$Sql = " update TrnBrowserLocCodes set ";
		$Sql .= " TrnLanguageID = :TrnLanguageID, ";
		$Sql .= " TrnBrowserLocCode = :TrnBrowserLocCode, ";
		$Sql .= " TrnBrowserLocCodeName = :TrnBrowserLocCodeName, ";
		$Sql .= " TrnBrowserLocCodeModiDateTime = now(), ";
		$Sql .= " TrnBrowserLocCodeState = :TrnBrowserLocCodeState ";
	$Sql .= " where TrnBrowserLocCodeID = :TrnBrowserLocCodeID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TrnLanguageID', $TrnLanguageID);
	$Stmt->bindParam(':TrnBrowserLocCode', $TrnBrowserLocCode);
	$Stmt->bindParam(':TrnBrowserLocCodeName', $TrnBrowserLocCodeName);
	$Stmt->bindParam(':TrnBrowserLocCodeState', $TrnBrowserLocCodeState);
	$Stmt->bindParam(':TrnBrowserLocCodeID', $TrnBrowserLocCodeID);
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


