<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$EduCenterID = isset($_REQUEST["EduCenterID"]) ? $_REQUEST["EduCenterID"] : "";
$ClassTypeID = isset($_REQUEST["ClassTypeID"]) ? $_REQUEST["ClassTypeID"] : "";
$ClassTypeName = isset($_REQUEST["ClassTypeName"]) ? $_REQUEST["ClassTypeName"] : "";
$ClassTypeStudyMinute = isset($_REQUEST["ClassTypeStudyMinute"]) ? $_REQUEST["ClassTypeStudyMinute"] : "";
$ClassTypePreviewMinute = isset($_REQUEST["ClassTypePreviewMinute"]) ? $_REQUEST["ClassTypePreviewMinute"] : "";
$ClassTypeReviewMinute = isset($_REQUEST["ClassTypeReviewMinute"]) ? $_REQUEST["ClassTypeReviewMinute"] : "";
$ClassTypeIntroText = isset($_REQUEST["ClassTypeIntroText"]) ? $_REQUEST["ClassTypeIntroText"] : "";
$ClassTypeRegDateTime = isset($_REQUEST["ClassTypeRegDateTime"]) ? $_REQUEST["ClassTypeRegDateTime"] : "";
$ClassTypeState = isset($_REQUEST["ClassTypeState"]) ? $_REQUEST["ClassTypeState"] : "";
$ClassTypeView = isset($_REQUEST["ClassTypeView"]) ? $_REQUEST["ClassTypeView"] : "";



if ($ClassTypeView!="1"){
	$ClassTypeView = 0;
}

if ($ClassTypeState!="1"){
	$ClassTypeState = 2;
}


if ($ClassTypeID==""){

	$Sql = "select ifnull(Max(ClassTypeOrder),0) as ClassTypeOrder from ClassTypes";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$ClassTypeOrder = $Row["ClassTypeOrder"]+1;

	$Sql = " insert into ClassTypes ( ";
		$Sql .= " EduCenterID, ";
		$Sql .= " ClassTypeName, ";
		$Sql .= " ClassTypeStudyMinute, ";
		$Sql .= " ClassTypePreviewMinute, ";
		$Sql .= " ClassTypeReviewMinute, ";
		$Sql .= " ClassTypeIntroText, ";
		$Sql .= " ClassTypeRegDateTime, ";
		$Sql .= " ClassTypeModiDateTime, ";
		$Sql .= " ClassTypeState, ";
		$Sql .= " ClassTypeView, ";
		$Sql .= " ClassTypeOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :EduCenterID, ";
		$Sql .= " :ClassTypeName, ";
		$Sql .= " :ClassTypeStudyMinute, ";
		$Sql .= " :ClassTypePreviewMinute, ";
		$Sql .= " :ClassTypeReviewMinute, ";
		$Sql .= " :ClassTypeIntroText, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :ClassTypeState, ";
		$Sql .= " :ClassTypeView, ";
		$Sql .= " :ClassTypeOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EduCenterID', $EduCenterID);
	$Stmt->bindParam(':ClassTypeName', $ClassTypeName);
	$Stmt->bindParam(':ClassTypeStudyMinute', $ClassTypeStudyMinute);
	$Stmt->bindParam(':ClassTypePreviewMinute', $ClassTypePreviewMinute);
	$Stmt->bindParam(':ClassTypeReviewMinute', $ClassTypeReviewMinute);
	$Stmt->bindParam(':ClassTypeIntroText', $ClassTypeIntroText);
	$Stmt->bindParam(':ClassTypeState', $ClassTypeState);
	$Stmt->bindParam(':ClassTypeView', $ClassTypeView);
	$Stmt->bindParam(':ClassTypeOrder', $ClassTypeOrder);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update ClassTypes set ";
		$Sql .= " EduCenterID = :EduCenterID, ";
		$Sql .= " ClassTypeName = :ClassTypeName, ";
		$Sql .= " ClassTypeStudyMinute = :ClassTypeStudyMinute, ";
		$Sql .= " ClassTypePreviewMinute = :ClassTypePreviewMinute, ";
		$Sql .= " ClassTypeReviewMinute = :ClassTypeReviewMinute, ";
		$Sql .= " ClassTypeIntroText = :ClassTypeIntroText, ";
		$Sql .= " ClassTypeState = :ClassTypeState, ";
		$Sql .= " ClassTypeView = :ClassTypeView, ";
		$Sql .= " ClassTypeModiDateTime = now() ";
	$Sql .= " where ClassTypeID = :ClassTypeID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EduCenterID', $EduCenterID);
	$Stmt->bindParam(':ClassTypeName', $ClassTypeName);
	$Stmt->bindParam(':ClassTypeStudyMinute', $ClassTypeStudyMinute);
	$Stmt->bindParam(':ClassTypePreviewMinute', $ClassTypePreviewMinute);
	$Stmt->bindParam(':ClassTypeReviewMinute', $ClassTypeReviewMinute);
	$Stmt->bindParam(':ClassTypeIntroText', $ClassTypeIntroText);
	$Stmt->bindParam(':ClassTypeState', $ClassTypeState);
	$Stmt->bindParam(':ClassTypeView', $ClassTypeView);
	$Stmt->bindParam(':ClassTypeID', $ClassTypeID);
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
	header("Location: class_type_list.php?$ListParam"); 
	exit;
}
?>


