<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);


$DocumentID = isset($_REQUEST["DocumentID"]) ? $_REQUEST["DocumentID"] : "";
$DocumentName = isset($_REQUEST["DocumentName"]) ? $_REQUEST["DocumentName"] : "";
$DocumentContent = isset($_REQUEST["DocumentContent"]) ? $_REQUEST["DocumentContent"] : "";

$DocumentView = isset($_REQUEST["DocumentView"]) ? $_REQUEST["DocumentView"] : "";
$DocumentState = isset($_REQUEST["DocumentState"]) ? $_REQUEST["DocumentState"] : "";

if ($DocumentView!="1"){
	$DocumentView = 0;
}

if ($DocumentState!="1"){
	$DocumentState = 2;
}

 
if ($DocumentID==""){

	$Sql = "select ifnull(Max(DocumentOrder),0) as DocumentOrder from Documents";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$DocumentOrder = $Row["DocumentOrder"]+1;

	$Sql = " insert into Documents ( ";
		$Sql .= " DocumentName, ";
		$Sql .= " DocumentContent, ";
		$Sql .= " DocumentRegDateTime, ";
		$Sql .= " DocumentModiDateTime, ";
		$Sql .= " DocumentState, ";
		$Sql .= " DocumentView, ";
		$Sql .= " DocumentOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :DocumentName, ";
		$Sql .= " :DocumentContent, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :DocumentState, ";
		$Sql .= " :DocumentView, ";
		$Sql .= " :DocumentOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentName', $DocumentName);
	$Stmt->bindParam(':DocumentContent', $DocumentContent);
	$Stmt->bindParam(':DocumentState', $DocumentState);
	$Stmt->bindParam(':DocumentView', $DocumentView);
	$Stmt->bindParam(':DocumentOrder', $DocumentOrder);
	$Stmt->execute();
	$DocumentID = $DbConn->lastInsertId();
	$Stmt = null;



}else{

	$Sql = " update Documents set ";
		$Sql .= " DocumentName = :DocumentName, ";
		$Sql .= " DocumentContent = :DocumentContent, ";
		$Sql .= " DocumentState = :DocumentState, ";
		$Sql .= " DocumentView = :DocumentView, ";
		$Sql .= " DocumentModiDateTime = now() ";
	$Sql .= " where DocumentID = :DocumentID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentName', $DocumentName);
	$Stmt->bindParam(':DocumentContent', $DocumentContent);
	$Stmt->bindParam(':DocumentState', $DocumentState);
	$Stmt->bindParam(':DocumentView', $DocumentView);
	$Stmt->bindParam(':DocumentID', $DocumentID);
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
	header("Location: document_list.php?$ListParam"); 
	exit;
}
?>