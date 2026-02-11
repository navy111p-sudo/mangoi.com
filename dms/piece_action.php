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

$PieceID = isset($_REQUEST["PieceID"]) ? $_REQUEST["PieceID"] : "";
$PieceCode = isset($_REQUEST["PieceCode"]) ? $_REQUEST["PieceCode"] : "";
$PieceName = isset($_REQUEST["PieceName"]) ? $_REQUEST["PieceName"] : "";
$PieceLayout = isset($_REQUEST["PieceLayout"]) ? $_REQUEST["PieceLayout"] : "";
$PieceState = isset($_REQUEST["PieceState"]) ? $_REQUEST["PieceState"] : "";

$PieceLayout = str_replace("<textarea", "{{textarea", $PieceLayout);
$PieceLayout = str_replace("textarea>", "textarea}}", $PieceLayout);

$PieceLayout = convertRequest($PieceLayout);
$PieceCode = trim($PieceCode);


if ($NewData=="1"){

	$Sql = " insert into Pieces ( ";
		$Sql .= " PieceCode, ";
		$Sql .= " PieceName, ";
		$Sql .= " PieceLayout, ";
		$Sql .= " PieceRegDateTime, ";
		$Sql .= " PieceState ";
	$Sql .= " ) values ( ";
		$Sql .= " :PieceCode, ";
		$Sql .= " :PieceName, ";
		$Sql .= " :PieceLayout, ";
		$Sql .= " now(), ";
		$Sql .= " :PieceState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':PieceCode', $PieceCode);
	$Stmt->bindParam(':PieceName', $PieceName);
	$Stmt->bindParam(':PieceLayout', $PieceLayout);
	$Stmt->bindParam(':PieceState', $PieceState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = "update Pieces set ";
		$Sql .= " PieceName = :PieceName, ";
		$Sql .= " PieceLayout = :PieceLayout, ";
		$Sql .= " PieceState = :PieceState ";
	$Sql .= " where PieceID = :PieceID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':PieceName', $PieceName);
	$Stmt->bindParam(':PieceLayout', $PieceLayout);
	$Stmt->bindParam(':PieceState', $PieceState);
	$Stmt->bindParam(':PieceID', $PieceID);
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
	header("Location: piece_list.php?$ListParam"); 
	exit;
}
?>





