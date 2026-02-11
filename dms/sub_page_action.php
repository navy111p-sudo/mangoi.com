<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);
$NewData = isset($_REQUEST["NewData"]) ? $_REQUEST["NewData"] : "";

$UseMain = isset($_REQUEST["UseMain"]) ? $_REQUEST["UseMain"] : "";
$UseSub = isset($_REQUEST["UseSub"]) ? $_REQUEST["UseSub"] : "";
$PageLevel = isset($_REQUEST["PageLevel"]) ? $_REQUEST["PageLevel"] : "";
$SubID = isset($_REQUEST["SubID"]) ? $_REQUEST["SubID"] : "";
$PageID = isset($_REQUEST["PageID"]) ? $_REQUEST["PageID"] : "";
$PageCode = isset($_REQUEST["PageCode"]) ? $_REQUEST["PageCode"] : "";
$PageName = isset($_REQUEST["PageName"]) ? $_REQUEST["PageName"] : "";
$PageContent = isset($_REQUEST["PageContent"]) ? $_REQUEST["PageContent"] : "";
$PageContentCss = isset($_REQUEST["PageContentCss"]) ? $_REQUEST["PageContentCss"] : "";
$PageContentJavascript = isset($_REQUEST["PageContentJavascript"]) ? $_REQUEST["PageContentJavascript"] : "";
$PageState = isset($_REQUEST["PageState"]) ? $_REQUEST["PageState"] : "";

$PageContent = str_replace("<textarea", "{{textarea", $PageContent);
$PageContent = str_replace("textarea>", "textarea}}", $PageContent);

$PageContent = convertRequest($PageContent);
$PageContentCss = convertRequest($PageContentCss);
$PageContentJavascript = convertRequest($PageContentJavascript);
$PageCode = trim($PageCode);




if ($NewData=="1"){

	$Sql = " insert into Pages ( ";
		$Sql .= " UseMain, ";
		$Sql .= " UseSub, ";
		$Sql .= " PageLevel, ";
		$Sql .= " SubID, ";
		$Sql .= " PageCode, ";
		$Sql .= " PageName, ";
		$Sql .= " PageContent, ";
		$Sql .= " PageContentCss, ";
		$Sql .= " PageContentJavascript, ";
		$Sql .= " PageRegDateTime, ";
		$Sql .= " PageState";
	$Sql .= " ) values ( ";
		$Sql .= " :UseMain, ";
		$Sql .= " :UseSub, ";
		$Sql .= " :SubID, ";
		$Sql .= " :PageLevel, ";
		$Sql .= " :PageCode, ";
		$Sql .= " :PageName, ";
		$Sql .= " :PageContent, ";
		$Sql .= " :PageContentCss, ";
		$Sql .= " :PageContentJavascript, ";
		$Sql .= " now(), ";
		$Sql .= " :PageState";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':UseMain', $UseMain);
	$Stmt->bindParam(':UseSub', $UseSub);
	$Stmt->bindParam(':SubID', $SubID);
	$Stmt->bindParam(':PageLevel', $PageLevel);
	$Stmt->bindParam(':PageCode', $PageCode);
	$Stmt->bindParam(':PageName', $PageName);
	$Stmt->bindParam(':PageContent', $PageContent);
	$Stmt->bindParam(':PageContentCss', $PageContentCss);
	$Stmt->bindParam(':PageContentJavascript', $PageContentJavascript);
	$Stmt->bindParam(':PageState', $PageState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update Pages set ";
		$Sql .= " UseMain = :UseMain, ";
		$Sql .= " UseSub = :UseSub, ";
		$Sql .= " SubID = :SubID, ";
		$Sql .= " PageLevel = :PageLevel, ";
		$Sql .= " PageCode = :PageCode, ";
		$Sql .= " PageName = :PageName, ";
		$Sql .= " PageContent = :PageContent, ";
		$Sql .= " PageContentCss = :PageContentCss, ";
		$Sql .= " PageContentJavascript = :PageContentJavascript, ";
		$Sql .= " PageState = :PageState ";
	$Sql .= " where PageID = :PageID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':UseMain', $UseMain);
	$Stmt->bindParam(':UseSub', $UseSub);
	$Stmt->bindParam(':SubID', $SubID);
	$Stmt->bindParam(':PageLevel', $PageLevel);
	$Stmt->bindParam(':PageCode', $PageCode);
	$Stmt->bindParam(':PageName', $PageName);
	$Stmt->bindParam(':PageContent', $PageContent);
	$Stmt->bindParam(':PageContentCss', $PageContentCss);
	$Stmt->bindParam(':PageContentJavascript', $PageContentJavascript);
	$Stmt->bindParam(':PageState', $PageState);
	$Stmt->bindParam(':PageID', $PageID);
	$Stmt->execute();
	$Stmt = null;

}

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
	header("Location: sub_page_list.php?$ListParam"); 
	exit;
}
?>


