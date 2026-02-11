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

$RecentID = isset($_REQUEST["RecentID"]) ? $_REQUEST["RecentID"] : "";
$RecentCode = isset($_REQUEST["RecentCode"]) ? $_REQUEST["RecentCode"] : "";
$RecentName = isset($_REQUEST["RecentName"]) ? $_REQUEST["RecentName"] : "";
$RecentLayout = isset($_REQUEST["RecentLayout"]) ? $_REQUEST["RecentLayout"] : "";
$RecentState = isset($_REQUEST["RecentState"]) ? $_REQUEST["RecentState"] : "";

$RecentLayout = convertRequest($RecentLayout);
$RecentCode = trim($RecentCode);


if ($NewData=="1"){

	$Sql = " insert into Recents ( ";
		$Sql .= " RecentCode, ";
		$Sql .= " RecentName, ";
		$Sql .= " RecentLayout, ";
		$Sql .= " RecentRegDateTime, ";
		$Sql .= " RecentState ";
	$Sql .= " ) values ( ";
		$Sql .= " :RecentCode, ";
		$Sql .= " :RecentName, ";
		$Sql .= " :RecentLayout, ";
		$Sql .= " now(), ";
		$Sql .= " :RecentState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':RecentCode', $RecentCode);
	$Stmt->bindParam(':RecentName', $RecentName);
	$Stmt->bindParam(':RecentLayout', $RecentLayout);
	$Stmt->bindParam(':RecentState', $RecentState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update Recents set ";
		$Sql .= " RecentName = :RecentName, ";
		$Sql .= " RecentLayout = :RecentLayout, ";
		$Sql .= " RecentState = :RecentState ";
	$Sql .= " where RecentID = :RecentID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':RecentName', $RecentName);
	$Stmt->bindParam(':RecentLayout', $RecentLayout);
	$Stmt->bindParam(':RecentState', $RecentState);
	$Stmt->bindParam(':RecentID', $RecentID);
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
	header("Location: recent_list.php?$ListParam"); 
	exit;
}
?>





