<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$SelfAssessmentItemID = isset($_REQUEST["SelfAssessmentItemID"]) ? $_REQUEST["SelfAssessmentItemID"] : "";
$SelfAssessmentItemTitle = isset($_REQUEST["SelfAssessmentItemTitle"]) ? $_REQUEST["SelfAssessmentItemTitle"] : "";
$SelfAssessmentItemContent = isset($_REQUEST["SelfAssessmentItemContent"]) ? $_REQUEST["SelfAssessmentItemContent"] : "";
$SelfAssessmentItemState = isset($_REQUEST["SelfAssessmentItemState"]) ? $_REQUEST["SelfAssessmentItemState"] : "";
$SelfAssessmentItemView = isset($_REQUEST["SelfAssessmentItemView"]) ? $_REQUEST["SelfAssessmentItemView"] : "";


if ($SelfAssessmentItemView!="1"){
	$SelfAssessmentItemView = 0;
}



if ($SelfAssessmentItemState!="1"){
	$SelfAssessmentItemState = 2;
}


if ($SelfAssessmentItemID==""){

	$Sql = "select ifnull(Max(SelfAssessmentItemOrder),0) as SelfAssessmentItemOrder from SelfAssessmentItems";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$SelfAssessmentItemOrder = $Row["SelfAssessmentItemOrder"]+1;

	$Sql = " insert into SelfAssessmentItems ( ";
		$Sql .= " SelfAssessmentItemTitle, ";
		$Sql .= " SelfAssessmentItemContent, ";
		$Sql .= " SelfAssessmentItemRegDateTime, ";
		$Sql .= " SelfAssessmentItemModiDateTime, ";
		$Sql .= " SelfAssessmentItemState, ";
		$Sql .= " SelfAssessmentItemView, ";
		$Sql .= " SelfAssessmentItemOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :SelfAssessmentItemTitle, ";
		$Sql .= " :SelfAssessmentItemContent, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :SelfAssessmentItemState, ";
		$Sql .= " :SelfAssessmentItemView, ";
		$Sql .= " :SelfAssessmentItemOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SelfAssessmentItemTitle', $SelfAssessmentItemTitle);
	$Stmt->bindParam(':SelfAssessmentItemContent', $SelfAssessmentItemContent);
	$Stmt->bindParam(':SelfAssessmentItemState', $SelfAssessmentItemState);
	$Stmt->bindParam(':SelfAssessmentItemView', $SelfAssessmentItemView);
	$Stmt->bindParam(':SelfAssessmentItemOrder', $SelfAssessmentItemOrder);
	$Stmt->execute();
	$SelfAssessmentItemID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = " update SelfAssessmentItems set ";
		$Sql .= " SelfAssessmentItemTitle = :SelfAssessmentItemTitle, ";
		$Sql .= " SelfAssessmentItemContent = :SelfAssessmentItemContent, ";
		$Sql .= " SelfAssessmentItemRegDateTime = now(), ";
		$Sql .= " SelfAssessmentItemModiDateTime = now(), ";
		$Sql .= " SelfAssessmentItemState = :SelfAssessmentItemState, ";
		$Sql .= " SelfAssessmentItemView = :SelfAssessmentItemView ";
	$Sql .= " where SelfAssessmentItemID = :SelfAssessmentItemID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SelfAssessmentItemTitle', $SelfAssessmentItemTitle);
	$Stmt->bindParam(':SelfAssessmentItemContent', $SelfAssessmentItemContent);
	$Stmt->bindParam(':SelfAssessmentItemState', $SelfAssessmentItemState);
	$Stmt->bindParam(':SelfAssessmentItemView', $SelfAssessmentItemView);
	$Stmt->bindParam(':SelfAssessmentItemID', $SelfAssessmentItemID);
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


