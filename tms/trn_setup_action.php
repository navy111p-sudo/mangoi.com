<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";


$TrnCollectDomain = isset($_REQUEST["TrnCollectDomain"]) ? $_REQUEST["TrnCollectDomain"] : "";
$TrnCollectTextMode = isset($_REQUEST["TrnCollectTextMode"]) ? $_REQUEST["TrnCollectTextMode"] : "";
$TrnCollectTextExplodeIndex = isset($_REQUEST["TrnCollectTextExplodeIndex"]) ? $_REQUEST["TrnCollectTextExplodeIndex"] : "";
$TrnCollectByFullUrl = isset($_REQUEST["TrnCollectByFullUrl"]) ? $_REQUEST["TrnCollectByFullUrl"] : "";
$TrnSiteLocCode = isset($_REQUEST["TrnSiteLocCode"]) ? $_REQUEST["TrnSiteLocCode"] : "";
$TrnIndexCode = isset($_REQUEST["TrnIndexCode"]) ? $_REQUEST["TrnIndexCode"] : "";
$TrnIndexCodeCommon = isset($_REQUEST["TrnIndexCodeCommon"]) ? $_REQUEST["TrnIndexCodeCommon"] : "";
$TrnIndexCodeCommonUrl = isset($_REQUEST["TrnIndexCodeCommonUrl"]) ? $_REQUEST["TrnIndexCodeCommonUrl"] : "";
$TrnTranslationMode = isset($_REQUEST["TrnTranslationMode"]) ? $_REQUEST["TrnTranslationMode"] : "";
$TrnTranslationModeApp = isset($_REQUEST["TrnTranslationModeApp"]) ? $_REQUEST["TrnTranslationModeApp"] : "";
$TrnRunType = isset($_REQUEST["TrnRunType"]) ? $_REQUEST["TrnRunType"] : "";
$TrnDefaultLanguageID = isset($_REQUEST["TrnDefaultLanguageID"]) ? $_REQUEST["TrnDefaultLanguageID"] : "";

$Sql = " update TrnSetup set ";
	$Sql .= " TrnCollectDomain = :TrnCollectDomain, ";
	$Sql .= " TrnCollectTextMode = :TrnCollectTextMode, ";
	$Sql .= " TrnCollectTextExplodeIndex = :TrnCollectTextExplodeIndex, ";
	$Sql .= " TrnCollectByFullUrl = :TrnCollectByFullUrl, ";
	$Sql .= " TrnSiteLocCode = :TrnSiteLocCode, ";
	$Sql .= " TrnIndexCode = :TrnIndexCode, ";
	$Sql .= " TrnIndexCodeCommon = :TrnIndexCodeCommon, ";
	$Sql .= " TrnIndexCodeCommonUrl = :TrnIndexCodeCommonUrl, ";
	$Sql .= " TrnTranslationMode = :TrnTranslationMode, ";
	$Sql .= " TrnTranslationModeApp = :TrnTranslationModeApp, ";
	$Sql .= " TrnRunType = :TrnRunType, ";
	$Sql .= " TrnDefaultLanguageID = :TrnDefaultLanguageID, ";
	$Sql .= " TrnSetupModiDateTime = now() ";
$Sql .= " where TrnSetupID = 1 ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TrnCollectDomain', $TrnCollectDomain);
$Stmt->bindParam(':TrnCollectTextMode', $TrnCollectTextMode);
$Stmt->bindParam(':TrnCollectTextExplodeIndex', $TrnCollectTextExplodeIndex);
$Stmt->bindParam(':TrnCollectByFullUrl', $TrnCollectByFullUrl);
$Stmt->bindParam(':TrnSiteLocCode', $TrnSiteLocCode);
$Stmt->bindParam(':TrnIndexCode', $TrnIndexCode);
$Stmt->bindParam(':TrnIndexCodeCommon', $TrnIndexCodeCommon);
$Stmt->bindParam(':TrnIndexCodeCommonUrl', $TrnIndexCodeCommonUrl);
$Stmt->bindParam(':TrnTranslationMode', $TrnTranslationMode);
$Stmt->bindParam(':TrnTranslationModeApp', $TrnTranslationModeApp);
$Stmt->bindParam(':TrnRunType', $TrnRunType);
$Stmt->bindParam(':TrnDefaultLanguageID', $TrnDefaultLanguageID);
$Stmt->execute();
$Stmt = null;



if ($err_num != 0){
	include_once('./inc_header.php');
?>
</head>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
<?php
	include_once('./inc_footer.php');
?>
</body>
</html>
<?
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: trn_setup_form.php"); 
	exit;
}
?>


