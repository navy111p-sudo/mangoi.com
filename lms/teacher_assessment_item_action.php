<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$TeacherAssessmentItemID = isset($_REQUEST["TeacherAssessmentItemID"]) ? $_REQUEST["TeacherAssessmentItemID"] : "";
$TeacherAssessmentItemTitle = isset($_REQUEST["TeacherAssessmentItemTitle"]) ? $_REQUEST["TeacherAssessmentItemTitle"] : "";
$TeacherAssessmentItemContent = isset($_REQUEST["TeacherAssessmentItemContent"]) ? $_REQUEST["TeacherAssessmentItemContent"] : "";
$TeacherAssessmentItemState = isset($_REQUEST["TeacherAssessmentItemState"]) ? $_REQUEST["TeacherAssessmentItemState"] : "";
$TeacherAssessmentItemView = isset($_REQUEST["TeacherAssessmentItemView"]) ? $_REQUEST["TeacherAssessmentItemView"] : "";

if ($TeacherAssessmentItemView!="1"){
	$TeacherAssessmentItemView = 0;
}

if ($TeacherAssessmentItemState!="1"){
	$TeacherState = 2;
}


if ($TeacherAssessmentItemID==""){

	$Sql = "select ifnull(Max(TeacherAssessmentItemOrder),0) as TeacherAssessmentItemOrder from TeacherAssessmentItems";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$TeacherAssessmentItemOrder = $Row["TeacherAssessmentItemOrder"]+1;

	$Sql = " insert into TeacherAssessmentItems ( ";
		$Sql .= " TeacherAssessmentItemTitle, ";
		$Sql .= " TeacherAssessmentItemContent, ";
		$Sql .= " TeacherAssessmentItemRegDateTime, ";
		$Sql .= " TeacherAssessmentItemModiDateTime, ";
		$Sql .= " TeacherAssessmentItemState, ";
		$Sql .= " TeacherAssessmentItemView, ";
		$Sql .= " TeacherAssessmentItemOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :TeacherAssessmentItemTitle, ";
		$Sql .= " :TeacherAssessmentItemContent, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :TeacherAssessmentItemState, ";
		$Sql .= " :TeacherAssessmentItemView, ";
		$Sql .= " :TeacherAssessmentItemOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherAssessmentItemTitle', $TeacherAssessmentItemTitle);
	$Stmt->bindParam(':TeacherAssessmentItemContent', $TeacherAssessmentItemContent);
	$Stmt->bindParam(':TeacherAssessmentItemState', $TeacherAssessmentItemState);
	$Stmt->bindParam(':TeacherAssessmentItemView', $TeacherAssessmentItemView);
	$Stmt->bindParam(':TeacherAssessmentItemOrder', $TeacherAssessmentItemOrder);
	$Stmt->execute();
	$TeacherAssessmentItemID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = " update TeacherAssessmentItems set ";
		$Sql .= " TeacherAssessmentItemTitle = :TeacherAssessmentItemTitle, ";
		$Sql .= " TeacherAssessmentItemContent = :TeacherAssessmentItemContent, ";
		$Sql .= " TeacherAssessmentItemModiDateTime = now(), ";
		$Sql .= " TeacherAssessmentItemState = :TeacherAssessmentItemState, ";
		$Sql .= " TeacherAssessmentItemView = :TeacherAssessmentItemView ";
	$Sql .= " where TeacherAssessmentItemID = :TeacherAssessmentItemID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherAssessmentItemTitle', $TeacherAssessmentItemTitle);
	$Stmt->bindParam(':TeacherAssessmentItemContent', $TeacherAssessmentItemContent);
	$Stmt->bindParam(':TeacherAssessmentItemState', $TeacherAssessmentItemState);
	$Stmt->bindParam(':TeacherAssessmentItemView', $TeacherAssessmentItemView);
	$Stmt->bindParam(':TeacherAssessmentItemID', $TeacherAssessmentItemID);
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


