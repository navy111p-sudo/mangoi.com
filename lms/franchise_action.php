<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$FranchiseID = isset($_REQUEST["FranchiseID"]) ? $_REQUEST["FranchiseID"] : "";
$FranchiseName = isset($_REQUEST["FranchiseName"]) ? $_REQUEST["FranchiseName"] : "";
$FranchiseManagerName = isset($_REQUEST["FranchiseManagerName"]) ? $_REQUEST["FranchiseManagerName"] : "";

$FranchisePhone1_1 = isset($_REQUEST["FranchisePhone1_1"]) ? $_REQUEST["FranchisePhone1_1"] : "";
$FranchisePhone1_2 = isset($_REQUEST["FranchisePhone1_2"]) ? $_REQUEST["FranchisePhone1_2"] : "";
$FranchisePhone1_3 = isset($_REQUEST["FranchisePhone1_3"]) ? $_REQUEST["FranchisePhone1_3"] : "";
$FranchisePhone2_1 = isset($_REQUEST["FranchisePhone2_1"]) ? $_REQUEST["FranchisePhone2_1"] : "";
$FranchisePhone2_2 = isset($_REQUEST["FranchisePhone2_2"]) ? $_REQUEST["FranchisePhone2_2"] : "";
$FranchisePhone2_3 = isset($_REQUEST["FranchisePhone2_3"]) ? $_REQUEST["FranchisePhone2_3"] : "";
$FranchisePhone3_1 = isset($_REQUEST["FranchisePhone3_1"]) ? $_REQUEST["FranchisePhone3_1"] : "";
$FranchisePhone3_2 = isset($_REQUEST["FranchisePhone3_2"]) ? $_REQUEST["FranchisePhone3_2"] : "";
$FranchisePhone3_3 = isset($_REQUEST["FranchisePhone3_3"]) ? $_REQUEST["FranchisePhone3_3"] : "";
$FranchiseEmail_1 = isset($_REQUEST["FranchiseEmail_1"]) ? $_REQUEST["FranchiseEmail_1"] : "";
$FranchiseEmail_2 = isset($_REQUEST["FranchiseEmail_2"]) ? $_REQUEST["FranchiseEmail_2"] : "";

$FranchiseZip = isset($_REQUEST["FranchiseZip"]) ? $_REQUEST["FranchiseZip"] : "";
$FranchiseAddr1 = isset($_REQUEST["FranchiseAddr1"]) ? $_REQUEST["FranchiseAddr1"] : "";
$FranchiseAddr2 = isset($_REQUEST["FranchiseAddr2"]) ? $_REQUEST["FranchiseAddr2"] : "";
$FranchiseSmsID = isset($_REQUEST["FranchiseSmsID"]) ? $_REQUEST["FranchiseSmsID"] : "";
$FranchiseSmsPW = isset($_REQUEST["FranchiseSmsPW"]) ? $_REQUEST["FranchiseSmsPW"] : "";
$FranchiseSendNumber = isset($_REQUEST["FranchiseSendNumber"]) ? $_REQUEST["FranchiseSendNumber"] : "";
$FranchiseReceiveNumber = isset($_REQUEST["FranchiseReceiveNumber"]) ? $_REQUEST["FranchiseReceiveNumber"] : "";
$FranchiseLogoImage = isset($_REQUEST["FranchiseLogoImage"]) ? $_REQUEST["FranchiseLogoImage"] : "";
$FranchiseIntroText = isset($_REQUEST["FranchiseIntroText"]) ? $_REQUEST["FranchiseIntroText"] : "";
$FranchiseRegDateTime = isset($_REQUEST["FranchiseRegDateTime"]) ? $_REQUEST["FranchiseRegDateTime"] : "";
$FranchiseState = isset($_REQUEST["FranchiseState"]) ? $_REQUEST["FranchiseState"] : "";
$FranchiseView = isset($_REQUEST["FranchiseView"]) ? $_REQUEST["FranchiseView"] : "";

$FranchiseSmsPW = trim($FranchiseSmsPW);

$FranchisePhone1 = $FranchisePhone1_1 . "-". $FranchisePhone1_2 . "-" .$FranchisePhone1_3;
$FranchisePhone2 = $FranchisePhone2_1 . "-". $FranchisePhone2_2 . "-" .$FranchisePhone2_3;
$FranchisePhone3 = $FranchisePhone3_1 . "-". $FranchisePhone3_2 . "-" .$FranchisePhone3_3;
$FranchiseEmail = $FranchiseEmail_1 . "@". $FranchiseEmail_2;

if ($FranchiseView!="1"){
	$FranchiseView = 0;
}

if ($FranchiseState!="1"){
	$FranchiseState = 2;
}


if ($FranchiseID==""){

	$Sql = "select ifnull(Max(FranchiseOrder),0) as FranchiseOrder from Franchises";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$FranchiseOrder = $Row["FranchiseOrder"]+1;

	$Sql = " insert into Franchises ( ";
		$Sql .= " FranchiseName, ";
		$Sql .= " FranchiseManagerName, ";
		$Sql .= " FranchisePhone1, ";
		$Sql .= " FranchisePhone2, ";
		$Sql .= " FranchisePhone3, ";
		$Sql .= " FranchiseEmail, ";
		$Sql .= " FranchiseZip, ";
		$Sql .= " FranchiseAddr1, ";
		$Sql .= " FranchiseAddr2, ";
		$Sql .= " FranchiseSmsID, ";
		if ($FranchiseSmsPW!=""){
			$Sql .= " FranchiseSmsPW, ";
		}
		$Sql .= " FranchiseSendNumber, ";
		$Sql .= " FranchiseReceiveNumber, ";
		$Sql .= " FranchiseLogoImage, ";
		$Sql .= " FranchiseIntroText, ";
		$Sql .= " FranchiseRegDateTime, ";
		$Sql .= " FranchiseModiDateTime, ";
		$Sql .= " FranchiseState, ";
		$Sql .= " FranchiseView, ";
		$Sql .= " FranchiseOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :FranchiseName, ";
		$Sql .= " :FranchiseManagerName, ";
		$Sql .= " HEX(AES_ENCRYPT(:FranchisePhone1, :EncryptionKey)), ";
		$Sql .= " HEX(AES_ENCRYPT(:FranchisePhone2, :EncryptionKey)), ";
		$Sql .= " HEX(AES_ENCRYPT(:FranchisePhone3, :EncryptionKey)), ";;
		$Sql .= " HEX(AES_ENCRYPT(:FranchiseEmail, :EncryptionKey)), ";
		$Sql .= " :FranchiseZip, ";
		$Sql .= " :FranchiseAddr1, ";
		$Sql .= " :FranchiseAddr2, ";
		$Sql .= " :FranchiseSmsID, ";
		if ($FranchiseSmsPW!=""){
			$Sql .= " :FranchiseSmsPW, ";
		}
		$Sql .= " :FranchiseSendNumber, ";
		$Sql .= " :FranchiseReceiveNumber, ";
		$Sql .= " :FranchiseLogoImage, ";
		$Sql .= " :FranchiseIntroText, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :FranchiseState, ";
		$Sql .= " :FranchiseView, ";
		$Sql .= " :FranchiseOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':FranchiseName', $FranchiseName);
	$Stmt->bindParam(':FranchiseManagerName', $FranchiseManagerName);
	$Stmt->bindParam(':FranchisePhone1', $FranchisePhone1);
	$Stmt->bindParam(':FranchisePhone2', $FranchisePhone2);
	$Stmt->bindParam(':FranchisePhone3', $FranchisePhone3);
	$Stmt->bindParam(':FranchiseEmail', $FranchiseEmail);
	$Stmt->bindParam(':FranchiseZip', $FranchiseZip);
	$Stmt->bindParam(':FranchiseAddr1', $FranchiseAddr1);
	$Stmt->bindParam(':FranchiseAddr2', $FranchiseAddr2);
	$Stmt->bindParam(':FranchiseSmsID', $FranchiseSmsID);
	if ($FranchiseSmsPW!=""){
		$Stmt->bindParam(':FranchiseSmsPW', $FranchiseSmsPW);
	}
	$Stmt->bindParam(':FranchiseSendNumber', $FranchiseSendNumber);
	$Stmt->bindParam(':FranchiseReceiveNumber', $FranchiseReceiveNumber);
	$Stmt->bindParam(':FranchiseLogoImage', $FranchiseLogoImage);
	$Stmt->bindParam(':FranchiseIntroText', $FranchiseIntroText);
	$Stmt->bindParam(':FranchiseState', $FranchiseState);
	$Stmt->bindParam(':FranchiseView', $FranchiseView);
	$Stmt->bindParam(':FranchiseOrder', $FranchiseOrder);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update Franchises set ";
		$Sql .= " FranchiseName = :FranchiseName, ";
		$Sql .= " FranchiseManagerName = :FranchiseManagerName, ";
		$Sql .= " FranchisePhone1 = HEX(AES_ENCRYPT(:FranchisePhone1, :EncryptionKey)), ";
		$Sql .= " FranchisePhone2 = HEX(AES_ENCRYPT(:FranchisePhone2, :EncryptionKey)), ";
		$Sql .= " FranchisePhone3 = HEX(AES_ENCRYPT(:FranchisePhone3, :EncryptionKey)), ";
		$Sql .= " FranchiseEmail = HEX(AES_ENCRYPT(:FranchiseEmail, :EncryptionKey)), ";
		$Sql .= " FranchiseZip = :FranchiseZip, ";
		$Sql .= " FranchiseAddr1 = :FranchiseAddr1, ";
		$Sql .= " FranchiseAddr2 = :FranchiseAddr2, ";
		$Sql .= " FranchiseSmsID = :FranchiseSmsID, ";
		if ($FranchiseSmsPW!=""){
			$Sql .= " FranchiseSmsPW = :FranchiseSmsPW, ";
		}
		$Sql .= " FranchiseSendNumber = :FranchiseSendNumber, ";
		$Sql .= " FranchiseReceiveNumber = :FranchiseReceiveNumber, ";
		$Sql .= " FranchiseLogoImage = :FranchiseLogoImage, ";
		$Sql .= " FranchiseIntroText = :FranchiseIntroText, ";
		$Sql .= " FranchiseState = :FranchiseState, ";
		$Sql .= " FranchiseView = :FranchiseView, ";
		$Sql .= " FranchiseModiDateTime = now() ";
	$Sql .= " where FranchiseID = :FranchiseID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':FranchiseName', $FranchiseName);
	$Stmt->bindParam(':FranchiseManagerName', $FranchiseManagerName);
	$Stmt->bindParam(':FranchisePhone1', $FranchisePhone1);
	$Stmt->bindParam(':FranchisePhone2', $FranchisePhone2);
	$Stmt->bindParam(':FranchisePhone3', $FranchisePhone3);
	$Stmt->bindParam(':FranchiseEmail', $FranchiseEmail);
	$Stmt->bindParam(':FranchiseZip', $FranchiseZip);
	$Stmt->bindParam(':FranchiseAddr1', $FranchiseAddr1);
	$Stmt->bindParam(':FranchiseAddr2', $FranchiseAddr2);
	$Stmt->bindParam(':FranchiseSmsID', $FranchiseSmsID);
	if ($FranchiseSmsPW!=""){
		$Stmt->bindParam(':FranchiseSmsPW', $FranchiseSmsPW);
	}
	$Stmt->bindParam(':FranchiseSendNumber', $FranchiseSendNumber);
	$Stmt->bindParam(':FranchiseReceiveNumber', $FranchiseReceiveNumber);
	$Stmt->bindParam(':FranchiseLogoImage', $FranchiseLogoImage);
	$Stmt->bindParam(':FranchiseIntroText', $FranchiseIntroText);
	$Stmt->bindParam(':FranchiseState', $FranchiseState);
	$Stmt->bindParam(':FranchiseView', $FranchiseView);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->bindParam(':FranchiseID', $FranchiseID);
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
	header("Location: franchise_list.php?$ListParam"); 
	exit;
}
?>


