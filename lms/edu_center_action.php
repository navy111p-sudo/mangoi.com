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
$FranchiseID = isset($_REQUEST["FranchiseID"]) ? $_REQUEST["FranchiseID"] : "";
$EduCenterName = isset($_REQUEST["EduCenterName"]) ? $_REQUEST["EduCenterName"] : "";
$EduCenterManagerName = isset($_REQUEST["EduCenterManagerName"]) ? $_REQUEST["EduCenterManagerName"] : "";

$EduCenterPhone1_1 = isset($_REQUEST["EduCenterPhone1_1"]) ? $_REQUEST["EduCenterPhone1_1"] : "";
$EduCenterPhone1_2 = isset($_REQUEST["EduCenterPhone1_2"]) ? $_REQUEST["EduCenterPhone1_2"] : "";
$EduCenterPhone1_3 = isset($_REQUEST["EduCenterPhone1_3"]) ? $_REQUEST["EduCenterPhone1_3"] : "";
$EduCenterPhone2_1 = isset($_REQUEST["EduCenterPhone2_1"]) ? $_REQUEST["EduCenterPhone2_1"] : "";
$EduCenterPhone2_2 = isset($_REQUEST["EduCenterPhone2_2"]) ? $_REQUEST["EduCenterPhone2_2"] : "";
$EduCenterPhone2_3 = isset($_REQUEST["EduCenterPhone2_3"]) ? $_REQUEST["EduCenterPhone2_3"] : "";
$EduCenterPhone3_1 = isset($_REQUEST["EduCenterPhone3_1"]) ? $_REQUEST["EduCenterPhone3_1"] : "";
$EduCenterPhone3_2 = isset($_REQUEST["EduCenterPhone3_2"]) ? $_REQUEST["EduCenterPhone3_2"] : "";
$EduCenterPhone3_3 = isset($_REQUEST["EduCenterPhone3_3"]) ? $_REQUEST["EduCenterPhone3_3"] : "";
$EduCenterEmail_1 = isset($_REQUEST["EduCenterEmail_1"]) ? $_REQUEST["EduCenterEmail_1"] : "";
$EduCenterEmail_2 = isset($_REQUEST["EduCenterEmail_2"]) ? $_REQUEST["EduCenterEmail_2"] : "";

$EduCenterZip = isset($_REQUEST["EduCenterZip"]) ? $_REQUEST["EduCenterZip"] : "";
$EduCenterAddr1 = isset($_REQUEST["EduCenterAddr1"]) ? $_REQUEST["EduCenterAddr1"] : "";
$EduCenterAddr2 = isset($_REQUEST["EduCenterAddr2"]) ? $_REQUEST["EduCenterAddr2"] : "";
$EduCenterLogoImage = isset($_REQUEST["EduCenterLogoImage"]) ? $_REQUEST["EduCenterLogoImage"] : "";
$EduCenterIntroText = isset($_REQUEST["EduCenterIntroText"]) ? $_REQUEST["EduCenterIntroText"] : "";
$EduCenterStartHour = isset($_REQUEST["EduCenterStartHour"]) ? $_REQUEST["EduCenterStartHour"] : 0;
$EduCenterEndHour = isset($_REQUEST["EduCenterEndHour"]) ? $_REQUEST["EduCenterEndHour"] : 0;
$EduCenterHoliday0 = isset($_REQUEST["EduCenterHoliday0"]) ? $_REQUEST["EduCenterHoliday0"] : "";
$EduCenterHoliday1 = isset($_REQUEST["EduCenterHoliday1"]) ? $_REQUEST["EduCenterHoliday1"] : "";
$EduCenterHoliday2 = isset($_REQUEST["EduCenterHoliday2"]) ? $_REQUEST["EduCenterHoliday2"] : "";
$EduCenterHoliday3 = isset($_REQUEST["EduCenterHoliday3"]) ? $_REQUEST["EduCenterHoliday3"] : "";
$EduCenterHoliday4 = isset($_REQUEST["EduCenterHoliday4"]) ? $_REQUEST["EduCenterHoliday4"] : "";
$EduCenterHoliday5 = isset($_REQUEST["EduCenterHoliday5"]) ? $_REQUEST["EduCenterHoliday5"] : "";
$EduCenterHoliday6 = isset($_REQUEST["EduCenterHoliday6"]) ? $_REQUEST["EduCenterHoliday6"] : "";
$EduCenterRegDateTime = isset($_REQUEST["EduCenterRegDateTime"]) ? $_REQUEST["EduCenterRegDateTime"] : "";
$EduCenterState = isset($_REQUEST["EduCenterState"]) ? $_REQUEST["EduCenterState"] : "";
$EduCenterView = isset($_REQUEST["EduCenterView"]) ? $_REQUEST["EduCenterView"] : "";


$EduCenterPhone1 = $EduCenterPhone1_1 . "-". $EduCenterPhone1_2 . "-" .$EduCenterPhone1_3;
$EduCenterPhone2 = $EduCenterPhone2_1 . "-". $EduCenterPhone2_2 . "-" .$EduCenterPhone2_3;
$EduCenterPhone3 = $EduCenterPhone3_1 . "-". $EduCenterPhone3_2 . "-" .$EduCenterPhone3_3;
$EduCenterEmail = $EduCenterEmail_1 . "@". $EduCenterEmail_2;

if ($EduCenterHoliday0!="1"){
	$EduCenterHoliday0 = 0;
}
if ($EduCenterHoliday1!="1"){
	$EduCenterHoliday1 = 0;
}
if ($EduCenterHoliday2!="1"){
	$EduCenterHoliday2 = 0;
}
if ($EduCenterHoliday3!="1"){
	$EduCenterHoliday3 = 0;
}
if ($EduCenterHoliday4!="1"){
	$EduCenterHoliday4 = 0;
}
if ($EduCenterHoliday5!="1"){
	$EduCenterHoliday5 = 0;
}
if ($EduCenterHoliday6!="1"){
	$EduCenterHoliday6 = 0;
}

if ($EduCenterView!="1"){
	$EduCenterView = 0;
}

if ($EduCenterState!="1"){
	$EduCenterState = 2;
}


if ($EduCenterID==""){

	$Sql = "select ifnull(Max(EduCenterOrder),0) as EduCenterOrder from EduCenters";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$EduCenterOrder = $Row["EduCenterOrder"]+1;

	$Sql = " insert into EduCenters ( ";
		$Sql .= " FranchiseID, ";
		$Sql .= " EduCenterName, ";
		$Sql .= " EduCenterManagerName, ";
		$Sql .= " EduCenterPhone1, ";
		$Sql .= " EduCenterPhone2, ";
		$Sql .= " EduCenterPhone3, ";
		$Sql .= " EduCenterEmail, ";
		$Sql .= " EduCenterZip, ";
		$Sql .= " EduCenterAddr1, ";
		$Sql .= " EduCenterAddr2, ";
		$Sql .= " EduCenterLogoImage, ";
		$Sql .= " EduCenterIntroText, ";
		$Sql .= " EduCenterStartHour, ";
		$Sql .= " EduCenterEndHour, ";
		$Sql .= " EduCenterHoliday0, ";
		$Sql .= " EduCenterHoliday1, ";
		$Sql .= " EduCenterHoliday2, ";
		$Sql .= " EduCenterHoliday3, ";
		$Sql .= " EduCenterHoliday4, ";
		$Sql .= " EduCenterHoliday5, ";
		$Sql .= " EduCenterHoliday6, ";
		$Sql .= " EduCenterRegDateTime, ";
		$Sql .= " EduCenterModiDateTime, ";
		$Sql .= " EduCenterState, ";
		$Sql .= " EduCenterView, ";
		$Sql .= " EduCenterOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :FranchiseID, ";
		$Sql .= " :EduCenterName, ";
		$Sql .= " :EduCenterManagerName, ";
		$Sql .= " HEX(AES_ENCRYPT(:EduCenterPhone1, :EncryptionKey)), ";
		$Sql .= " HEX(AES_ENCRYPT(:EduCenterPhone2, :EncryptionKey)), ";
		$Sql .= " HEX(AES_ENCRYPT(:EduCenterPhone3, :EncryptionKey)), ";;
		$Sql .= " HEX(AES_ENCRYPT(:EduCenterEmail, :EncryptionKey)), ";
		$Sql .= " :EduCenterZip, ";
		$Sql .= " :EduCenterAddr1, ";
		$Sql .= " :EduCenterAddr2, ";
		$Sql .= " :EduCenterLogoImage, ";
		$Sql .= " :EduCenterIntroText, ";
		$Sql .= " :EduCenterStartHour, ";
		$Sql .= " :EduCenterEndHour, ";
		$Sql .= " :EduCenterHoliday0, ";
		$Sql .= " :EduCenterHoliday1, ";
		$Sql .= " :EduCenterHoliday2, ";
		$Sql .= " :EduCenterHoliday3, ";
		$Sql .= " :EduCenterHoliday4, ";
		$Sql .= " :EduCenterHoliday5, ";
		$Sql .= " :EduCenterHoliday6, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :EduCenterState, ";
		$Sql .= " :EduCenterView, ";
		$Sql .= " :EduCenterOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':FranchiseID', $FranchiseID);
	$Stmt->bindParam(':EduCenterName', $EduCenterName);
	$Stmt->bindParam(':EduCenterManagerName', $EduCenterManagerName);
	$Stmt->bindParam(':EduCenterPhone1', $EduCenterPhone1);
	$Stmt->bindParam(':EduCenterPhone2', $EduCenterPhone2);
	$Stmt->bindParam(':EduCenterPhone3', $EduCenterPhone3);
	$Stmt->bindParam(':EduCenterEmail', $EduCenterEmail);
	$Stmt->bindParam(':EduCenterZip', $EduCenterZip);
	$Stmt->bindParam(':EduCenterAddr1', $EduCenterAddr1);
	$Stmt->bindParam(':EduCenterAddr2', $EduCenterAddr2);
	$Stmt->bindParam(':EduCenterLogoImage', $EduCenterLogoImage);
	$Stmt->bindParam(':EduCenterIntroText', $EduCenterIntroText);
	$Stmt->bindParam(':EduCenterStartHour', $EduCenterStartHour);
	$Stmt->bindParam(':EduCenterEndHour', $EduCenterEndHour);
	$Stmt->bindParam(':EduCenterHoliday0', $EduCenterHoliday0);
	$Stmt->bindParam(':EduCenterHoliday1', $EduCenterHoliday1);
	$Stmt->bindParam(':EduCenterHoliday2', $EduCenterHoliday2);
	$Stmt->bindParam(':EduCenterHoliday3', $EduCenterHoliday3);
	$Stmt->bindParam(':EduCenterHoliday4', $EduCenterHoliday4);
	$Stmt->bindParam(':EduCenterHoliday5', $EduCenterHoliday5);
	$Stmt->bindParam(':EduCenterHoliday6', $EduCenterHoliday6);
	$Stmt->bindParam(':EduCenterState', $EduCenterState);
	$Stmt->bindParam(':EduCenterView', $EduCenterView);
	$Stmt->bindParam(':EduCenterOrder', $EduCenterOrder);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update EduCenters set ";
		$Sql .= " FranchiseID = :FranchiseID, ";
		$Sql .= " EduCenterName = :EduCenterName, ";
		$Sql .= " EduCenterManagerName = :EduCenterManagerName, ";
		$Sql .= " EduCenterPhone1 = HEX(AES_ENCRYPT(:EduCenterPhone1, :EncryptionKey)), ";
		$Sql .= " EduCenterPhone2 = HEX(AES_ENCRYPT(:EduCenterPhone2, :EncryptionKey)), ";
		$Sql .= " EduCenterPhone3 = HEX(AES_ENCRYPT(:EduCenterPhone3, :EncryptionKey)), ";
		$Sql .= " EduCenterEmail = HEX(AES_ENCRYPT(:EduCenterEmail, :EncryptionKey)), ";
		$Sql .= " EduCenterZip = :EduCenterZip, ";
		$Sql .= " EduCenterAddr1 = :EduCenterAddr1, ";
		$Sql .= " EduCenterAddr2 = :EduCenterAddr2, ";
		$Sql .= " EduCenterLogoImage = :EduCenterLogoImage, ";
		$Sql .= " EduCenterIntroText = :EduCenterIntroText, ";
		$Sql .= " EduCenterStartHour = :EduCenterStartHour, ";
		$Sql .= " EduCenterEndHour = :EduCenterEndHour, ";
		$Sql .= " EduCenterHoliday0 = :EduCenterHoliday0, ";
		$Sql .= " EduCenterHoliday1 = :EduCenterHoliday1, ";
		$Sql .= " EduCenterHoliday2 = :EduCenterHoliday2, ";
		$Sql .= " EduCenterHoliday3 = :EduCenterHoliday3, ";
		$Sql .= " EduCenterHoliday4 = :EduCenterHoliday4, ";
		$Sql .= " EduCenterHoliday5 = :EduCenterHoliday5, ";
		$Sql .= " EduCenterHoliday6 = :EduCenterHoliday6, ";
		$Sql .= " EduCenterState = :EduCenterState, ";
		$Sql .= " EduCenterView = :EduCenterView, ";
		$Sql .= " EduCenterModiDateTime = now() ";
	$Sql .= " where EduCenterID = :EduCenterID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':FranchiseID', $FranchiseID);
	$Stmt->bindParam(':EduCenterName', $EduCenterName);
	$Stmt->bindParam(':EduCenterManagerName', $EduCenterManagerName);
	$Stmt->bindParam(':EduCenterPhone1', $EduCenterPhone1);
	$Stmt->bindParam(':EduCenterPhone2', $EduCenterPhone2);
	$Stmt->bindParam(':EduCenterPhone3', $EduCenterPhone3);
	$Stmt->bindParam(':EduCenterEmail', $EduCenterEmail);
	$Stmt->bindParam(':EduCenterZip', $EduCenterZip);
	$Stmt->bindParam(':EduCenterAddr1', $EduCenterAddr1);
	$Stmt->bindParam(':EduCenterAddr2', $EduCenterAddr2);
	$Stmt->bindParam(':EduCenterLogoImage', $EduCenterLogoImage);
	$Stmt->bindParam(':EduCenterIntroText', $EduCenterIntroText);
	$Stmt->bindParam(':EduCenterStartHour', $EduCenterStartHour);
	$Stmt->bindParam(':EduCenterEndHour', $EduCenterEndHour);
	$Stmt->bindParam(':EduCenterHoliday0', $EduCenterHoliday0);
	$Stmt->bindParam(':EduCenterHoliday1', $EduCenterHoliday1);
	$Stmt->bindParam(':EduCenterHoliday2', $EduCenterHoliday2);
	$Stmt->bindParam(':EduCenterHoliday3', $EduCenterHoliday3);
	$Stmt->bindParam(':EduCenterHoliday4', $EduCenterHoliday4);
	$Stmt->bindParam(':EduCenterHoliday5', $EduCenterHoliday5);
	$Stmt->bindParam(':EduCenterHoliday6', $EduCenterHoliday6);
	$Stmt->bindParam(':EduCenterState', $EduCenterState);
	$Stmt->bindParam(':EduCenterView', $EduCenterView);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->bindParam(':EduCenterID', $EduCenterID);
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
	header("Location: edu_center_list.php?$ListParam"); 
	exit;
}
?>


