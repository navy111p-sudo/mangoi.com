<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$StudentAssessmentItemID = isset($_REQUEST["StudentAssessmentItemID"]) ? $_REQUEST["StudentAssessmentItemID"] : "";
$StudentAssessmentItemTitle = isset($_REQUEST["StudentAssessmentItemTitle"]) ? $_REQUEST["StudentAssessmentItemTitle"] : "";
$StudentAssessmentItemContent = isset($_REQUEST["StudentAssessmentItemContent"]) ? $_REQUEST["StudentAssessmentItemContent"] : "";
$StudentAssessmentItemState = isset($_REQUEST["StudentAssessmentItemState"]) ? $_REQUEST["StudentAssessmentItemState"] : "";
$StudentAssessmentItemView = isset($_REQUEST["StudentAssessmentItemView"]) ? $_REQUEST["StudentAssessmentItemView"] : "";

if ($StudentAssessmentItemView!="1"){
	$StudentAssessmentItemView = 0;
}

if ($StudentAssessmentItemState!="1"){
	$StudentState = 2;
}


if ($StudentAssessmentItemID==""){

	$Sql = "select ifnull(Max(StudentAssessmentItemOrder),0) as StudentAssessmentItemOrder from StudentAssessmentItems";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$StudentAssessmentItemOrder = $Row["StudentAssessmentItemOrder"]+1;

	$Sql = " insert into StudentAssessmentItems ( ";
		$Sql .= " StudentAssessmentItemTitle, ";
		$Sql .= " StudentAssessmentItemContent, ";
		$Sql .= " StudentAssessmentItemRegDateTime, ";
		$Sql .= " StudentAssessmentItemModiDateTime, ";
		$Sql .= " StudentAssessmentItemState, ";
		$Sql .= " StudentAssessmentItemView, ";
		$Sql .= " StudentAssessmentItemOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :StudentAssessmentItemTitle, ";
		$Sql .= " :StudentAssessmentItemContent, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :StudentAssessmentItemState, ";
		$Sql .= " :StudentAssessmentItemView, ";
		$Sql .= " :StudentAssessmentItemOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':StudentAssessmentItemTitle', $StudentAssessmentItemTitle);
	$Stmt->bindParam(':StudentAssessmentItemContent', $StudentAssessmentItemContent);
	$Stmt->bindParam(':StudentAssessmentItemState', $StudentAssessmentItemState);
	$Stmt->bindParam(':StudentAssessmentItemView', $StudentAssessmentItemView);
	$Stmt->bindParam(':StudentAssessmentItemOrder', $StudentAssessmentItemOrder);
	$Stmt->execute();
	$StudentAssessmentItemID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = " update StudentAssessmentItems set ";
		$Sql .= " StudentAssessmentItemTitle = :StudentAssessmentItemTitle, ";
		$Sql .= " StudentAssessmentItemContent = :StudentAssessmentItemContent, ";
		$Sql .= " StudentAssessmentItemModiDateTime = now(), ";
		$Sql .= " StudentAssessmentItemState = :StudentAssessmentItemState, ";
		$Sql .= " StudentAssessmentItemView = :StudentAssessmentItemView ";
	$Sql .= " where StudentAssessmentItemID = :StudentAssessmentItemID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':StudentAssessmentItemTitle', $StudentAssessmentItemTitle);
	$Stmt->bindParam(':StudentAssessmentItemContent', $StudentAssessmentItemContent);
	$Stmt->bindParam(':StudentAssessmentItemState', $StudentAssessmentItemState);
	$Stmt->bindParam(':StudentAssessmentItemView', $StudentAssessmentItemView);
	$Stmt->bindParam(':StudentAssessmentItemID', $StudentAssessmentItemID);
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


