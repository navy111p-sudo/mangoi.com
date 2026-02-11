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
$TeacherGroupID = isset($_REQUEST["TeacherGroupID"]) ? $_REQUEST["TeacherGroupID"] : "";
$TeacherGroupName = isset($_REQUEST["TeacherGroupName"]) ? $_REQUEST["TeacherGroupName"] : "";
$TeacherGroupManagerName = isset($_REQUEST["TeacherGroupManagerName"]) ? $_REQUEST["TeacherGroupManagerName"] : "";

$TeacherGroupPhone1_1 = isset($_REQUEST["TeacherGroupPhone1_1"]) ? $_REQUEST["TeacherGroupPhone1_1"] : "";
$TeacherGroupPhone1_2 = isset($_REQUEST["TeacherGroupPhone1_2"]) ? $_REQUEST["TeacherGroupPhone1_2"] : "";
$TeacherGroupPhone1_3 = isset($_REQUEST["TeacherGroupPhone1_3"]) ? $_REQUEST["TeacherGroupPhone1_3"] : "";
$TeacherGroupPhone2_1 = isset($_REQUEST["TeacherGroupPhone2_1"]) ? $_REQUEST["TeacherGroupPhone2_1"] : "";
$TeacherGroupPhone2_2 = isset($_REQUEST["TeacherGroupPhone2_2"]) ? $_REQUEST["TeacherGroupPhone2_2"] : "";
$TeacherGroupPhone2_3 = isset($_REQUEST["TeacherGroupPhone2_3"]) ? $_REQUEST["TeacherGroupPhone2_3"] : "";
$TeacherGroupPhone3_1 = isset($_REQUEST["TeacherGroupPhone3_1"]) ? $_REQUEST["TeacherGroupPhone3_1"] : "";
$TeacherGroupPhone3_2 = isset($_REQUEST["TeacherGroupPhone3_2"]) ? $_REQUEST["TeacherGroupPhone3_2"] : "";
$TeacherGroupPhone3_3 = isset($_REQUEST["TeacherGroupPhone3_3"]) ? $_REQUEST["TeacherGroupPhone3_3"] : "";
$TeacherGroupEmail_1 = isset($_REQUEST["TeacherGroupEmail_1"]) ? $_REQUEST["TeacherGroupEmail_1"] : "";
$TeacherGroupEmail_2 = isset($_REQUEST["TeacherGroupEmail_2"]) ? $_REQUEST["TeacherGroupEmail_2"] : "";

$TeacherGroupZip = isset($_REQUEST["TeacherGroupZip"]) ? $_REQUEST["TeacherGroupZip"] : "";
$TeacherGroupAddr1 = isset($_REQUEST["TeacherGroupAddr1"]) ? $_REQUEST["TeacherGroupAddr1"] : "";
$TeacherGroupAddr2 = isset($_REQUEST["TeacherGroupAddr2"]) ? $_REQUEST["TeacherGroupAddr2"] : "";
$TeacherGroupLogoImage = isset($_REQUEST["TeacherGroupLogoImage"]) ? $_REQUEST["TeacherGroupLogoImage"] : "";
$TeacherGroupIntroText = isset($_REQUEST["TeacherGroupIntroText"]) ? $_REQUEST["TeacherGroupIntroText"] : "";
$TeacherGroupRegDateTime = isset($_REQUEST["TeacherGroupRegDateTime"]) ? $_REQUEST["TeacherGroupRegDateTime"] : "";
$TeacherGroupState = isset($_REQUEST["TeacherGroupState"]) ? $_REQUEST["TeacherGroupState"] : "";
$TeacherGroupView = isset($_REQUEST["TeacherGroupView"]) ? $_REQUEST["TeacherGroupView"] : "";

$TeacherGroupPhone1 = $TeacherGroupPhone1_1 . "-". $TeacherGroupPhone1_2 . "-" .$TeacherGroupPhone1_3;
$TeacherGroupPhone2 = $TeacherGroupPhone2_1 . "-". $TeacherGroupPhone2_2 . "-" .$TeacherGroupPhone2_3;
$TeacherGroupPhone3 = $TeacherGroupPhone3_1 . "-". $TeacherGroupPhone3_2 . "-" .$TeacherGroupPhone3_3;
$TeacherGroupEmail = $TeacherGroupEmail_1 . "@". $TeacherGroupEmail_2;

if ($TeacherGroupView!="1"){
	$TeacherGroupView = 0;
}

if ($TeacherGroupState!="1"){
	$TeacherGroupState = 2;
}


if ($TeacherGroupID==""){

	$Sql = "select ifnull(Max(TeacherGroupOrder),0) as TeacherGroupOrder from TeacherGroups";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$TeacherGroupOrder = $Row["TeacherGroupOrder"]+1;

	$Sql = " insert into TeacherGroups ( ";
		$Sql .= " EduCenterID, ";
		$Sql .= " TeacherGroupName, ";
		$Sql .= " TeacherGroupManagerName, ";
		$Sql .= " TeacherGroupPhone1, ";
		$Sql .= " TeacherGroupPhone2, ";
		$Sql .= " TeacherGroupPhone3, ";
		$Sql .= " TeacherGroupEmail, ";
		$Sql .= " TeacherGroupZip, ";
		$Sql .= " TeacherGroupAddr1, ";
		$Sql .= " TeacherGroupAddr2, ";
		$Sql .= " TeacherGroupLogoImage, ";
		$Sql .= " TeacherGroupIntroText, ";
		$Sql .= " TeacherGroupRegDateTime, ";
		$Sql .= " TeacherGroupModiDateTime, ";
		$Sql .= " TeacherGroupState, ";
		$Sql .= " TeacherGroupView, ";
		$Sql .= " TeacherGroupOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :EduCenterID, ";
		$Sql .= " :TeacherGroupName, ";
		$Sql .= " :TeacherGroupManagerName, ";
		$Sql .= " HEX(AES_ENCRYPT(:TeacherGroupPhone1, :EncryptionKey)), ";
		$Sql .= " HEX(AES_ENCRYPT(:TeacherGroupPhone2, :EncryptionKey)), ";
		$Sql .= " HEX(AES_ENCRYPT(:TeacherGroupPhone3, :EncryptionKey)), ";;
		$Sql .= " HEX(AES_ENCRYPT(:TeacherGroupEmail, :EncryptionKey)), ";
		$Sql .= " :TeacherGroupZip, ";
		$Sql .= " :TeacherGroupAddr1, ";
		$Sql .= " :TeacherGroupAddr2, ";
		$Sql .= " :TeacherGroupLogoImage, ";
		$Sql .= " :TeacherGroupIntroText, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :TeacherGroupState, ";
		$Sql .= " :TeacherGroupView, ";
		$Sql .= " :TeacherGroupOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EduCenterID', $EduCenterID);
	$Stmt->bindParam(':TeacherGroupName', $TeacherGroupName);
	$Stmt->bindParam(':TeacherGroupManagerName', $TeacherGroupManagerName);
	$Stmt->bindParam(':TeacherGroupPhone1', $TeacherGroupPhone1);
	$Stmt->bindParam(':TeacherGroupPhone2', $TeacherGroupPhone2);
	$Stmt->bindParam(':TeacherGroupPhone3', $TeacherGroupPhone3);
	$Stmt->bindParam(':TeacherGroupEmail', $TeacherGroupEmail);
	$Stmt->bindParam(':TeacherGroupZip', $TeacherGroupZip);
	$Stmt->bindParam(':TeacherGroupAddr1', $TeacherGroupAddr1);
	$Stmt->bindParam(':TeacherGroupAddr2', $TeacherGroupAddr2);
	$Stmt->bindParam(':TeacherGroupLogoImage', $TeacherGroupLogoImage);
	$Stmt->bindParam(':TeacherGroupIntroText', $TeacherGroupIntroText);
	$Stmt->bindParam(':TeacherGroupState', $TeacherGroupState);
	$Stmt->bindParam(':TeacherGroupView', $TeacherGroupView);
	$Stmt->bindParam(':TeacherGroupOrder', $TeacherGroupOrder);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update TeacherGroups set ";
		$Sql .= " EduCenterID = :EduCenterID, ";
		$Sql .= " TeacherGroupName = :TeacherGroupName, ";
		$Sql .= " TeacherGroupManagerName = :TeacherGroupManagerName, ";
		$Sql .= " TeacherGroupPhone1 = HEX(AES_ENCRYPT(:TeacherGroupPhone1, :EncryptionKey)), ";
		$Sql .= " TeacherGroupPhone2 = HEX(AES_ENCRYPT(:TeacherGroupPhone2, :EncryptionKey)), ";
		$Sql .= " TeacherGroupPhone3 = HEX(AES_ENCRYPT(:TeacherGroupPhone3, :EncryptionKey)), ";
		$Sql .= " TeacherGroupEmail = HEX(AES_ENCRYPT(:TeacherGroupEmail, :EncryptionKey)), ";
		$Sql .= " TeacherGroupZip = :TeacherGroupZip, ";
		$Sql .= " TeacherGroupAddr1 = :TeacherGroupAddr1, ";
		$Sql .= " TeacherGroupAddr2 = :TeacherGroupAddr2, ";
		$Sql .= " TeacherGroupLogoImage = :TeacherGroupLogoImage, ";
		$Sql .= " TeacherGroupIntroText = :TeacherGroupIntroText, ";
		$Sql .= " TeacherGroupState = :TeacherGroupState, ";
		$Sql .= " TeacherGroupView = :TeacherGroupView, ";
		$Sql .= " TeacherGroupModiDateTime = now() ";
	$Sql .= " where TeacherGroupID = :TeacherGroupID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EduCenterID', $EduCenterID);
	$Stmt->bindParam(':TeacherGroupName', $TeacherGroupName);
	$Stmt->bindParam(':TeacherGroupManagerName', $TeacherGroupManagerName);
	$Stmt->bindParam(':TeacherGroupPhone1', $TeacherGroupPhone1);
	$Stmt->bindParam(':TeacherGroupPhone2', $TeacherGroupPhone2);
	$Stmt->bindParam(':TeacherGroupPhone3', $TeacherGroupPhone3);
	$Stmt->bindParam(':TeacherGroupEmail', $TeacherGroupEmail);
	$Stmt->bindParam(':TeacherGroupZip', $TeacherGroupZip);
	$Stmt->bindParam(':TeacherGroupAddr1', $TeacherGroupAddr1);
	$Stmt->bindParam(':TeacherGroupAddr2', $TeacherGroupAddr2);
	$Stmt->bindParam(':TeacherGroupLogoImage', $TeacherGroupLogoImage);
	$Stmt->bindParam(':TeacherGroupIntroText', $TeacherGroupIntroText);
	$Stmt->bindParam(':TeacherGroupState', $TeacherGroupState);
	$Stmt->bindParam(':TeacherGroupView', $TeacherGroupView);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->bindParam(':TeacherGroupID', $TeacherGroupID);
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
	header("Location: teacher_group_list.php?$ListParam"); 
	exit;
}
?>


