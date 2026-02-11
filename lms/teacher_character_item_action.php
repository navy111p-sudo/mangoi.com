<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$TeacherCharacterItemID = isset($_REQUEST["TeacherCharacterItemID"]) ? $_REQUEST["TeacherCharacterItemID"] : "";
$TeacherCharacterItemTitle = isset($_REQUEST["TeacherCharacterItemTitle"]) ? $_REQUEST["TeacherCharacterItemTitle"] : "";
$TeacherCharacterItemContent = isset($_REQUEST["TeacherCharacterItemContent"]) ? $_REQUEST["TeacherCharacterItemContent"] : "";
$TeacherCharacterItemState = isset($_REQUEST["TeacherCharacterItemState"]) ? $_REQUEST["TeacherCharacterItemState"] : "";
$TeacherCharacterItemView = isset($_REQUEST["TeacherCharacterItemView"]) ? $_REQUEST["TeacherCharacterItemView"] : "";

if ($TeacherCharacterItemView!="1"){
	$TeacherCharacterItemView = 0;
}

if ($TeacherCharacterItemState!="1"){
	$TeacherCharacterItemState = 2;
}


if ($TeacherCharacterItemID==""){

	$Sql = "select ifnull(Max(TeacherCharacterItemOrder),0) as TeacherCharacterItemOrder from TeacherCharacterItems";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$TeacherCharacterItemOrder = $Row["TeacherCharacterItemOrder"]+1;

	$Sql = " insert into TeacherCharacterItems ( ";
		$Sql .= " TeacherCharacterItemTitle, ";
		$Sql .= " TeacherCharacterItemContent, ";
		$Sql .= " TeacherCharacterItemRegDateTime, ";
		$Sql .= " TeacherCharacterItemModiDateTime, ";
		$Sql .= " TeacherCharacterItemState, ";
		$Sql .= " TeacherCharacterItemView, ";
		$Sql .= " TeacherCharacterItemOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :TeacherCharacterItemTitle, ";
		$Sql .= " :TeacherCharacterItemContent, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :TeacherCharacterItemState, ";
		$Sql .= " :TeacherCharacterItemView, ";
		$Sql .= " :TeacherCharacterItemOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherCharacterItemTitle', $TeacherCharacterItemTitle);
	$Stmt->bindParam(':TeacherCharacterItemContent', $TeacherCharacterItemContent);
	$Stmt->bindParam(':TeacherCharacterItemState', $TeacherCharacterItemState);
	$Stmt->bindParam(':TeacherCharacterItemView', $TeacherCharacterItemView);
	$Stmt->bindParam(':TeacherCharacterItemOrder', $TeacherCharacterItemOrder);
	$Stmt->execute();
	$TeacherCharacterItemID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = " update TeacherCharacterItems set ";
		$Sql .= " TeacherCharacterItemTitle = :TeacherCharacterItemTitle, ";
		$Sql .= " TeacherCharacterItemContent = :TeacherCharacterItemContent, ";
		$Sql .= " TeacherCharacterItemModiDateTime = now(), ";
		$Sql .= " TeacherCharacterItemState = :TeacherCharacterItemState, ";
		$Sql .= " TeacherCharacterItemView = :TeacherCharacterItemView ";
	$Sql .= " where TeacherCharacterItemID = :TeacherCharacterItemID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherCharacterItemTitle', $TeacherCharacterItemTitle);
	$Stmt->bindParam(':TeacherCharacterItemContent', $TeacherCharacterItemContent);
	$Stmt->bindParam(':TeacherCharacterItemState', $TeacherCharacterItemState);
	$Stmt->bindParam(':TeacherCharacterItemView', $TeacherCharacterItemView);
	$Stmt->bindParam(':TeacherCharacterItemID', $TeacherCharacterItemID);
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


