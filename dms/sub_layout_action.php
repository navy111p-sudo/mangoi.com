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

$SubID = isset($_REQUEST["SubID"]) ? $_REQUEST["SubID"] : "";
$SubCode = isset($_REQUEST["SubCode"]) ? $_REQUEST["SubCode"] : "";
$SubName = isset($_REQUEST["SubName"]) ? $_REQUEST["SubName"] : "";
$SubLayout = isset($_REQUEST["SubLayout"]) ? $_REQUEST["SubLayout"] : "";
$SubLayoutCss = isset($_REQUEST["SubLayoutCss"]) ? $_REQUEST["SubLayoutCss"] : "";
$SubLayoutJavascript = isset($_REQUEST["SubLayoutJavascript"]) ? $_REQUEST["SubLayoutJavascript"] : "";
$SubState = isset($_REQUEST["SubState"]) ? $_REQUEST["SubState"] : "";


$SubLayout = str_replace("<textarea", "{{textarea", $SubLayout);
$SubLayout = str_replace("textarea>", "textarea}}", $SubLayout);


$SubLayout = convertRequest($SubLayout);
$SubLayoutCss = convertRequest($SubLayoutCss);
$SubLayoutJavascript = convertRequest($SubLayoutJavascript);
$SubCode = trim($SubCode);


if ($NewData=="1"){
	$Sql = " insert into Subs ( ";
		$Sql .= " SubCode, SubName, ";
		$Sql .= " SubLayout, ";
		$Sql .= " SubLayoutCss, ";
		$Sql .= " SubLayoutJavascript, ";
		$Sql .= " SubRegDateTime, ";
		$Sql .= " SubState ";
	$Sql .= " ) values ( ";
		$Sql .= " :SubCode, ";
		$Sql .= " :SubName, ";
		$Sql .= " :SubLayout, ";
		$Sql .= " :SubLayoutCss, ";
		$Sql .= " :SubLayoutJavascript, ";
		$Sql .= " now(), ";
		$Sql .= " :SubState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SubCode', $SubCode);
	$Stmt->bindParam(':SubName', $SubName);
	$Stmt->bindParam(':SubLayout', $SubLayout);
	$Stmt->bindParam(':SubLayoutCss', $SubLayoutCss);
	$Stmt->bindParam(':SubLayoutJavascript', $SubLayoutJavascript);
	$Stmt->bindParam(':SubState', $SubState);
	$Stmt->execute();
	$Stmt = null;

}else{
	
	$Sql = " update Subs set ";
		$Sql .= " SubName = :SubName, ";
		$Sql .= " SubLayout = :SubLayout, ";
		$Sql .= " SubLayoutCss = :SubLayoutCss, ";
		$Sql .= " SubLayoutJavascript = :SubLayoutJavascript, ";
		$Sql .= " SubState = :SubState ";
	$Sql .= " where SubID = :SubID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SubName', $SubName);
	$Stmt->bindParam(':SubLayout', $SubLayout);
	$Stmt->bindParam(':SubLayoutCss', $SubLayoutCss);
	$Stmt->bindParam(':SubLayoutJavascript', $SubLayoutJavascript);
	$Stmt->bindParam(':SubState', $SubState);
	$Stmt->bindParam(':SubID', $SubID);
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
	header("Location: sub_layout_list.php?$ListParam"); 
	exit;
}
?>


