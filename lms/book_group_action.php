<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$BookGroupID = isset($_REQUEST["BookGroupID"]) ? $_REQUEST["BookGroupID"] : "";
$BookGroupName = isset($_REQUEST["BookGroupName"]) ? $_REQUEST["BookGroupName"] : "";
$BookGroupMemo = isset($_REQUEST["BookGroupMemo"]) ? $_REQUEST["BookGroupMemo"] : "";
$BookGroupState = isset($_REQUEST["BookGroupState"]) ? $_REQUEST["BookGroupState"] : "";
$BookGroupView = isset($_REQUEST["BookGroupView"]) ? $_REQUEST["BookGroupView"] : "";

if ($BookGroupView!="1"){
	$BookGroupView = 0;
}

if ($BookGroupState!="1"){
	$BookGroupState = 2;
}


if ($BookGroupID==""){

	$Sql = "select ifnull(Max(BookGroupOrder),0) as BookGroupOrder from BookGroups";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BookGroupOrder = $Row["BookGroupOrder"]+1;

	$Sql = " insert into BookGroups ( ";
		$Sql .= " BookGroupName, ";
		$Sql .= " BookGroupMemo, ";
		$Sql .= " BookGroupRegDateTime, ";
		$Sql .= " BookGroupModiDateTime, ";
		$Sql .= " BookGroupState, ";
		$Sql .= " BookGroupView, ";
		$Sql .= " BookGroupOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :BookGroupName, ";
		$Sql .= " :BookGroupMemo, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :BookGroupState, ";
		$Sql .= " :BookGroupView, ";
		$Sql .= " :BookGroupOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookGroupName', $BookGroupName);
	$Stmt->bindParam(':BookGroupMemo', $BookGroupMemo);
	$Stmt->bindParam(':BookGroupState', $BookGroupState);
	$Stmt->bindParam(':BookGroupView', $BookGroupView);
	$Stmt->bindParam(':BookGroupOrder', $BookGroupOrder);
	$Stmt->execute();
	$BookGroupID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = " update BookGroups set ";
		$Sql .= " BookGroupName = :BookGroupName, ";
		$Sql .= " BookGroupMemo = :BookGroupMemo, ";
		$Sql .= " BookGroupModiDateTime = now(), ";
		$Sql .= " BookGroupState = :BookGroupState, ";
		$Sql .= " BookGroupView = :BookGroupView ";
	$Sql .= " where BookGroupID = :BookGroupID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookGroupName', $BookGroupName);
	$Stmt->bindParam(':BookGroupMemo', $BookGroupMemo);
	$Stmt->bindParam(':BookGroupState', $BookGroupState);
	$Stmt->bindParam(':BookGroupView', $BookGroupView);
	$Stmt->bindParam(':BookGroupID', $BookGroupID);
	$Stmt->execute();
	$Stmt = null;

}


if ($err_num != 0){
	include_once('./inc_header.php');
?>
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
?>
<script>
parent.$.fn.colorbox.close();
</script>
<?
}
?>


