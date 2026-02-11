<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$OnlineSiteID = isset($_REQUEST["OnlineSiteID"]) ? $_REQUEST["OnlineSiteID"] : "";
$FranchiseID = isset($_REQUEST["FranchiseID"]) ? $_REQUEST["FranchiseID"] : "";
$OnlineSiteName = isset($_REQUEST["OnlineSiteName"]) ? $_REQUEST["OnlineSiteName"] : "";
$OnlineSiteManagerName = isset($_REQUEST["OnlineSiteManagerName"]) ? $_REQUEST["OnlineSiteManagerName"] : "";
$OnlineSiteDomain = isset($_REQUEST["OnlineSiteDomain"]) ? $_REQUEST["OnlineSiteDomain"] : "";

$OnlineSitePhone1_1 = isset($_REQUEST["OnlineSitePhone1_1"]) ? $_REQUEST["OnlineSitePhone1_1"] : "";
$OnlineSitePhone1_2 = isset($_REQUEST["OnlineSitePhone1_2"]) ? $_REQUEST["OnlineSitePhone1_2"] : "";
$OnlineSitePhone1_3 = isset($_REQUEST["OnlineSitePhone1_3"]) ? $_REQUEST["OnlineSitePhone1_3"] : "";
$OnlineSitePhone2_1 = isset($_REQUEST["OnlineSitePhone2_1"]) ? $_REQUEST["OnlineSitePhone2_1"] : "";
$OnlineSitePhone2_2 = isset($_REQUEST["OnlineSitePhone2_2"]) ? $_REQUEST["OnlineSitePhone2_2"] : "";
$OnlineSitePhone2_3 = isset($_REQUEST["OnlineSitePhone2_3"]) ? $_REQUEST["OnlineSitePhone2_3"] : "";
$OnlineSitePhone3_1 = isset($_REQUEST["OnlineSitePhone3_1"]) ? $_REQUEST["OnlineSitePhone3_1"] : "";
$OnlineSitePhone3_2 = isset($_REQUEST["OnlineSitePhone3_2"]) ? $_REQUEST["OnlineSitePhone3_2"] : "";
$OnlineSitePhone3_3 = isset($_REQUEST["OnlineSitePhone3_3"]) ? $_REQUEST["OnlineSitePhone3_3"] : "";
$OnlineSiteEmail_1 = isset($_REQUEST["OnlineSiteEmail_1"]) ? $_REQUEST["OnlineSiteEmail_1"] : "";
$OnlineSiteEmail_2 = isset($_REQUEST["OnlineSiteEmail_2"]) ? $_REQUEST["OnlineSiteEmail_2"] : "";

$OnlineSiteSincerityPayStartDate = isset($_REQUEST["OnlineSiteSincerityPayStartDate"]) ? $_REQUEST["OnlineSiteSincerityPayStartDate"] : "";
$OnlineSiteSincerityPayEndDate = isset($_REQUEST["OnlineSiteSincerityPayEndDate"]) ? $_REQUEST["OnlineSiteSincerityPayEndDate"] : "";
$OnlineSiteMemberRegPoint = isset($_REQUEST["OnlineSiteMemberRegPoint"]) ? $_REQUEST["OnlineSiteMemberRegPoint"] : "";
$OnlineSiteMemberLoginPoint = isset($_REQUEST["OnlineSiteMemberLoginPoint"]) ? $_REQUEST["OnlineSiteMemberLoginPoint"] : "";
$OnlineSitePaymentPointRatio = isset($_REQUEST["OnlineSitePaymentPointRatio"]) ? $_REQUEST["OnlineSitePaymentPointRatio"] : "";
$OnlineSiteStudyPoint = isset($_REQUEST["OnlineSiteStudyPoint"]) ? $_REQUEST["OnlineSiteStudyPoint"] : "";
$OnlineSitePreStudyPoint = isset($_REQUEST["OnlineSitePreStudyPoint"]) ? $_REQUEST["OnlineSitePreStudyPoint"] : "";
$OnlineSiteReStudyPoint = isset($_REQUEST["OnlineSiteReStudyPoint"]) ? $_REQUEST["OnlineSiteReStudyPoint"] : "";
$OnlineSitePgCardFeeRatio = isset($_REQUEST["OnlineSitePgCardFeeRatio"]) ? $_REQUEST["OnlineSitePgCardFeeRatio"] : "";
$OnlineSitePgDirectFeePrice = isset($_REQUEST["OnlineSitePgDirectFeePrice"]) ? $_REQUEST["OnlineSitePgDirectFeePrice"] : "";
$OnlineSitePgDirectFeeRatio = isset($_REQUEST["OnlineSitePgDirectFeeRatio"]) ? $_REQUEST["OnlineSitePgDirectFeeRatio"] : "";
$OnlineSitePgVBankFeePrice = isset($_REQUEST["OnlineSitePgVBankFeePrice"]) ? $_REQUEST["OnlineSitePgVBankFeePrice"] : "";

$OnlineSiteShipPrice = isset($_REQUEST["OnlineSiteShipPrice"]) ? $_REQUEST["OnlineSiteShipPrice"] : "";

$OnlineSiteGuideVideoType = isset($_REQUEST["OnlineSiteGuideVideoType"]) ? $_REQUEST["OnlineSiteGuideVideoType"] : "";
$OnlineSiteGuideVideoCode = isset($_REQUEST["OnlineSiteGuideVideoCode"]) ? $_REQUEST["OnlineSiteGuideVideoCode"] : "";

$OnlineSiteZip = isset($_REQUEST["OnlineSiteZip"]) ? $_REQUEST["OnlineSiteZip"] : "";
$OnlineSiteAddr1 = isset($_REQUEST["OnlineSiteAddr1"]) ? $_REQUEST["OnlineSiteAddr1"] : "";
$OnlineSiteAddr2 = isset($_REQUEST["OnlineSiteAddr2"]) ? $_REQUEST["OnlineSiteAddr2"] : "";
$OnlineSiteSmsID = isset($_REQUEST["OnlineSiteSmsID"]) ? $_REQUEST["OnlineSiteSmsID"] : "";
$OnlineSiteSmsPW = isset($_REQUEST["OnlineSiteSmsPW"]) ? $_REQUEST["OnlineSiteSmsPW"] : "";
$OnlineSiteSendNumber = isset($_REQUEST["OnlineSiteSendNumber"]) ? $_REQUEST["OnlineSiteSendNumber"] : "";
$OnlineSiteReceiveNumber = isset($_REQUEST["OnlineSiteReceiveNumber"]) ? $_REQUEST["OnlineSiteReceiveNumber"] : "";
$OnlineSiteLogoImage = isset($_REQUEST["OnlineSiteLogoImage"]) ? $_REQUEST["OnlineSiteLogoImage"] : "";
$OnlineSiteIntroText = isset($_REQUEST["OnlineSiteIntroText"]) ? $_REQUEST["OnlineSiteIntroText"] : "";
$OnlineSiteRegDateTime = isset($_REQUEST["OnlineSiteRegDateTime"]) ? $_REQUEST["OnlineSiteRegDateTime"] : "";
$OnlineSiteState = isset($_REQUEST["OnlineSiteState"]) ? $_REQUEST["OnlineSiteState"] : "";
$OnlineSiteView = isset($_REQUEST["OnlineSiteView"]) ? $_REQUEST["OnlineSiteView"] : "";
$OnlineSiteShVersion = isset($_REQUEST["OnlineSiteShVersion"]) ? $_REQUEST["OnlineSiteShVersion"] : "1";
$OnlineSiteShVersionDemo = isset($_REQUEST["OnlineSiteShVersionDemo"]) ? $_REQUEST["OnlineSiteShVersionDemo"] : "1";

$OnlineSitePhone1 = $OnlineSitePhone1_1 . "-". $OnlineSitePhone1_2 . "-" .$OnlineSitePhone1_3;
$OnlineSitePhone2 = $OnlineSitePhone2_1 . "-". $OnlineSitePhone2_2 . "-" .$OnlineSitePhone2_3;
$OnlineSitePhone3 = $OnlineSitePhone3_1 . "-". $OnlineSitePhone3_2 . "-" .$OnlineSitePhone3_3;
$OnlineSiteEmail = $OnlineSiteEmail_1 . "@". $OnlineSiteEmail_2;

if ($OnlineSiteView!="1"){
	$OnlineSiteView = 0;
}

if ($OnlineSiteState!="1"){
	$OnlineSiteState = 2;
}

$OnlineSiteGuideVideoCode = trim($OnlineSiteGuideVideoCode);

if ($OnlineSiteID==""){

	$Sql = "select ifnull(Max(OnlineSiteOrder),0) as OnlineSiteOrder from OnlineSites";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$OnlineSiteOrder = $Row["OnlineSiteOrder"]+1;

	$Sql = " insert into OnlineSites ( ";
		$Sql .= " FranchiseID, ";
		$Sql .= " OnlineSiteName, ";
		$Sql .= " OnlineSiteManagerName, ";
		$Sql .= " OnlineSiteDomain, ";
		$Sql .= " OnlineSitePhone1, ";
		$Sql .= " OnlineSitePhone2, ";
		$Sql .= " OnlineSitePhone3, ";
		$Sql .= " OnlineSiteEmail, ";
		$Sql .= " OnlineSiteSincerityPayStartDate, ";
		$Sql .= " OnlineSiteSincerityPayEndDate, ";
		$Sql .= " OnlineSiteMemberRegPoint, ";
		$Sql .= " OnlineSiteMemberLoginPoint, ";
		$Sql .= " OnlineSitePaymentPointRatio, ";
		$Sql .= " OnlineSiteStudyPoint, ";
		$Sql .= " OnlineSitePreStudyPoint, ";
		$Sql .= " OnlineSiteReStudyPoint, ";
		$Sql .= " OnlineSitePgCardFeeRatio, ";
		$Sql .= " OnlineSitePgDirectFeePrice, ";
		$Sql .= " OnlineSitePgDirectFeeRatio, ";
		$Sql .= " OnlineSitePgVBankFeePrice, ";
		$Sql .= " OnlineSiteShipPrice, ";
		$Sql .= " OnlineSiteGuideVideoType, ";
		$Sql .= " OnlineSiteGuideVideoCode, ";
		$Sql .= " OnlineSiteShVersion, ";
		$Sql .= " OnlineSiteShVersionDemo, ";
		$Sql .= " OnlineSiteZip, ";
		$Sql .= " OnlineSiteAddr1, ";
		$Sql .= " OnlineSiteAddr2, ";
		$Sql .= " OnlineSiteSmsID, ";
		if ($OnlineSiteSmsPW!=""){
			$Sql .= " OnlineSiteSmsPW, ";
		}
		$Sql .= " OnlineSiteSendNumber, ";
		$Sql .= " OnlineSiteReceiveNumber, ";
		$Sql .= " OnlineSiteLogoImage, ";
		$Sql .= " OnlineSiteIntroText, ";
		$Sql .= " OnlineSiteRegDateTime, ";
		$Sql .= " OnlineSiteModiDateTime, ";
		$Sql .= " OnlineSiteState, ";
		$Sql .= " OnlineSiteView, ";
		$Sql .= " OnlineSiteOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :FranchiseID, ";
		$Sql .= " :OnlineSiteName, ";
		$Sql .= " :OnlineSiteManagerName, ";
		$Sql .= " :OnlineSiteDomain, ";
		$Sql .= " HEX(AES_ENCRYPT(:OnlineSitePhone1, :EncryptionKey)), ";
		$Sql .= " HEX(AES_ENCRYPT(:OnlineSitePhone2, :EncryptionKey)), ";
		$Sql .= " HEX(AES_ENCRYPT(:OnlineSitePhone3, :EncryptionKey)), ";;
		$Sql .= " HEX(AES_ENCRYPT(:OnlineSiteEmail, :EncryptionKey)), ";
		$Sql .= " :OnlineSiteSincerityPayStartDate, ";
		$Sql .= " :OnlineSiteSincerityPayEndDate, ";
		$Sql .= " :OnlineSiteMemberRegPoint, ";
		$Sql .= " :OnlineSiteMemberLoginPoint, ";
		$Sql .= " :OnlineSitePaymentPointRatio, ";
		$Sql .= " :OnlineSiteStudyPoint, ";
		$Sql .= " :OnlineSitePreStudyPoint, ";
		$Sql .= " :OnlineSiteReStudyPoint, ";
		$Sql .= " :OnlineSitePgCardFeeRatio, ";
		$Sql .= " :OnlineSitePgDirectFeePrice, ";
		$Sql .= " :OnlineSitePgDirectFeeRatio, ";
		$Sql .= " :OnlineSitePgVBankFeePrice, ";
		$Sql .= " :OnlineSiteShipPrice, ";
		$Sql .= " :OnlineSiteGuideVideoType, ";
		$Sql .= " :OnlineSiteGuideVideoCode, ";
		$Sql .= " :OnlineSiteShVersion, ";
		$Sql .= " :OnlineSiteShVersionDemo, ";
		$Sql .= " :OnlineSiteZip, ";
		$Sql .= " :OnlineSiteAddr1, ";
		$Sql .= " :OnlineSiteAddr2, ";
		$Sql .= " :OnlineSiteSmsID, ";
		if ($OnlineSiteSmsPW!=""){
			$Sql .= " :OnlineSiteSmsPW, ";
		}
		$Sql .= " :OnlineSiteSendNumber, ";
		$Sql .= " :OnlineSiteReceiveNumber, ";
		$Sql .= " :OnlineSiteLogoImage, ";
		$Sql .= " :OnlineSiteIntroText, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :OnlineSiteState, ";
		$Sql .= " :OnlineSiteView, ";
		$Sql .= " :OnlineSiteOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':FranchiseID', $FranchiseID);
	$Stmt->bindParam(':OnlineSiteName', $OnlineSiteName);
	$Stmt->bindParam(':OnlineSiteManagerName', $OnlineSiteManagerName);
	$Stmt->bindParam(':OnlineSiteDomain', $OnlineSiteDomain);
	$Stmt->bindParam(':OnlineSitePhone1', $OnlineSitePhone1);
	$Stmt->bindParam(':OnlineSitePhone2', $OnlineSitePhone2);
	$Stmt->bindParam(':OnlineSitePhone3', $OnlineSitePhone3);
	$Stmt->bindParam(':OnlineSiteEmail', $OnlineSiteEmail);
	$Stmt->bindParam(':OnlineSiteSincerityPayStartDate', $OnlineSiteSincerityPayStartDate);
	$Stmt->bindParam(':OnlineSiteSincerityPayEndDate', $OnlineSiteSincerityPayEndDate);
	$Stmt->bindParam(':OnlineSiteMemberRegPoint', $OnlineSiteMemberRegPoint);
	$Stmt->bindParam(':OnlineSiteMemberLoginPoint', $OnlineSiteMemberLoginPoint);
	$Stmt->bindParam(':OnlineSitePaymentPointRatio', $OnlineSitePaymentPointRatio);
	$Stmt->bindParam(':OnlineSiteStudyPoint', $OnlineSiteStudyPoint);
	$Stmt->bindParam(':OnlineSitePreStudyPoint', $OnlineSitePreStudyPoint);
	$Stmt->bindParam(':OnlineSiteReStudyPoint', $OnlineSiteReStudyPoint);
	$Stmt->bindParam(':OnlineSitePgCardFeeRatio', $OnlineSitePgCardFeeRatio);
	$Stmt->bindParam(':OnlineSitePgDirectFeePrice', $OnlineSitePgDirectFeePrice);
	$Stmt->bindParam(':OnlineSitePgDirectFeeRatio', $OnlineSitePgDirectFeeRatio);
	$Stmt->bindParam(':OnlineSitePgVBankFeePrice', $OnlineSitePgVBankFeePrice);
	$Stmt->bindParam(':OnlineSiteShipPrice', $OnlineSiteShipPrice);
	$Stmt->bindParam(':OnlineSiteGuideVideoType', $OnlineSiteGuideVideoType);
	$Stmt->bindParam(':OnlineSiteGuideVideoCode', $OnlineSiteGuideVideoCode);
	$Stmt->bindParam(':OnlineSiteShVersion', $OnlineSiteShVersion);
	$Stmt->bindParam(':OnlineSiteShVersionDemo', $OnlineSiteShVersionDemo);
	$Stmt->bindParam(':OnlineSiteZip', $OnlineSiteZip);
	$Stmt->bindParam(':OnlineSiteAddr1', $OnlineSiteAddr1);
	$Stmt->bindParam(':OnlineSiteAddr2', $OnlineSiteAddr2);
	$Stmt->bindParam(':OnlineSiteSmsID', $OnlineSiteSmsID);
	if ($OnlineSiteSmsPW!=""){
		$Stmt->bindParam(':OnlineSiteSmsPW', $OnlineSiteSmsPW);
	}
	$Stmt->bindParam(':OnlineSiteSendNumber', $OnlineSiteSendNumber);
	$Stmt->bindParam(':OnlineSiteReceiveNumber', $OnlineSiteReceiveNumber);
	$Stmt->bindParam(':OnlineSiteLogoImage', $OnlineSiteLogoImage);
	$Stmt->bindParam(':OnlineSiteIntroText', $OnlineSiteIntroText);
	$Stmt->bindParam(':OnlineSiteState', $OnlineSiteState);
	$Stmt->bindParam(':OnlineSiteView', $OnlineSiteView);
	$Stmt->bindParam(':OnlineSiteOrder', $OnlineSiteOrder);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update OnlineSites set ";
		$Sql .= " FranchiseID = :FranchiseID, ";
		$Sql .= " OnlineSiteName = :OnlineSiteName, ";
		$Sql .= " OnlineSiteManagerName = :OnlineSiteManagerName, ";
		$Sql .= " OnlineSiteDomain = :OnlineSiteDomain, ";
		$Sql .= " OnlineSitePhone1 = HEX(AES_ENCRYPT(:OnlineSitePhone1, :EncryptionKey)), ";
		$Sql .= " OnlineSitePhone2 = HEX(AES_ENCRYPT(:OnlineSitePhone2, :EncryptionKey)), ";
		$Sql .= " OnlineSitePhone3 = HEX(AES_ENCRYPT(:OnlineSitePhone3, :EncryptionKey)), ";
		$Sql .= " OnlineSiteEmail = HEX(AES_ENCRYPT(:OnlineSiteEmail, :EncryptionKey)), ";
		$Sql .= " OnlineSiteSincerityPayStartDate = :OnlineSiteSincerityPayStartDate, ";
		$Sql .= " OnlineSiteSincerityPayEndDate = :OnlineSiteSincerityPayEndDate, ";
		$Sql .= " OnlineSiteMemberRegPoint = :OnlineSiteMemberRegPoint, ";
		$Sql .= " OnlineSiteMemberLoginPoint = :OnlineSiteMemberLoginPoint, ";
		$Sql .= " OnlineSitePaymentPointRatio = :OnlineSitePaymentPointRatio, ";
		$Sql .= " OnlineSiteStudyPoint = :OnlineSiteStudyPoint, ";
		$Sql .= " OnlineSitePreStudyPoint = :OnlineSitePreStudyPoint, ";
		$Sql .= " OnlineSiteReStudyPoint = :OnlineSiteReStudyPoint, ";
		$Sql .= " OnlineSitePgCardFeeRatio = :OnlineSitePgCardFeeRatio, ";
		$Sql .= " OnlineSitePgDirectFeePrice = :OnlineSitePgDirectFeePrice, ";
		$Sql .= " OnlineSitePgDirectFeeRatio = :OnlineSitePgDirectFeeRatio, ";
		$Sql .= " OnlineSitePgVBankFeePrice = :OnlineSitePgVBankFeePrice, ";
		$Sql .= " OnlineSiteShipPrice = :OnlineSiteShipPrice, ";
		$Sql .= " OnlineSiteGuideVideoType = :OnlineSiteGuideVideoType, ";
		$Sql .= " OnlineSiteGuideVideoCode = :OnlineSiteGuideVideoCode, ";
		$Sql .= " OnlineSiteShVersion = :OnlineSiteShVersion, ";
		$Sql .= " OnlineSiteShVersionDemo = :OnlineSiteShVersionDemo, ";
		$Sql .= " OnlineSiteZip = :OnlineSiteZip, ";
		$Sql .= " OnlineSiteAddr1 = :OnlineSiteAddr1, ";
		$Sql .= " OnlineSiteAddr2 = :OnlineSiteAddr2, ";
		$Sql .= " OnlineSiteSmsID = :OnlineSiteSmsID, ";
		if ($OnlineSiteSmsPW!=""){
			$Sql .= " OnlineSiteSmsPW = :OnlineSiteSmsPW, ";
		}
		$Sql .= " OnlineSiteSendNumber = :OnlineSiteSendNumber, ";
		$Sql .= " OnlineSiteReceiveNumber = :OnlineSiteReceiveNumber, ";
		$Sql .= " OnlineSiteLogoImage = :OnlineSiteLogoImage, ";
		$Sql .= " OnlineSiteIntroText = :OnlineSiteIntroText, ";
		$Sql .= " OnlineSiteState = :OnlineSiteState, ";
		$Sql .= " OnlineSiteView = :OnlineSiteView, ";
		$Sql .= " OnlineSiteModiDateTime = now() ";
	$Sql .= " where OnlineSiteID = :OnlineSiteID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':FranchiseID', $FranchiseID);
	$Stmt->bindParam(':OnlineSiteName', $OnlineSiteName);
	$Stmt->bindParam(':OnlineSiteManagerName', $OnlineSiteManagerName);
	$Stmt->bindParam(':OnlineSiteDomain', $OnlineSiteDomain);
	$Stmt->bindParam(':OnlineSitePhone1', $OnlineSitePhone1);
	$Stmt->bindParam(':OnlineSitePhone2', $OnlineSitePhone2);
	$Stmt->bindParam(':OnlineSitePhone3', $OnlineSitePhone3);
	$Stmt->bindParam(':OnlineSiteEmail', $OnlineSiteEmail);
	$Stmt->bindParam(':OnlineSiteSincerityPayStartDate', $OnlineSiteSincerityPayStartDate);
	$Stmt->bindParam(':OnlineSiteSincerityPayEndDate', $OnlineSiteSincerityPayEndDate);
	$Stmt->bindParam(':OnlineSiteMemberRegPoint', $OnlineSiteMemberRegPoint);
	$Stmt->bindParam(':OnlineSiteMemberLoginPoint', $OnlineSiteMemberLoginPoint);
	$Stmt->bindParam(':OnlineSitePaymentPointRatio', $OnlineSitePaymentPointRatio);
	$Stmt->bindParam(':OnlineSiteStudyPoint', $OnlineSiteStudyPoint);
	$Stmt->bindParam(':OnlineSitePreStudyPoint', $OnlineSitePreStudyPoint);
	$Stmt->bindParam(':OnlineSiteReStudyPoint', $OnlineSiteReStudyPoint);
	$Stmt->bindParam(':OnlineSitePgCardFeeRatio', $OnlineSitePgCardFeeRatio);
	$Stmt->bindParam(':OnlineSitePgDirectFeePrice', $OnlineSitePgDirectFeePrice);
	$Stmt->bindParam(':OnlineSitePgDirectFeeRatio', $OnlineSitePgDirectFeeRatio);
	$Stmt->bindParam(':OnlineSitePgVBankFeePrice', $OnlineSitePgVBankFeePrice);
	$Stmt->bindParam(':OnlineSiteShipPrice', $OnlineSiteShipPrice);
	$Stmt->bindParam(':OnlineSiteGuideVideoType', $OnlineSiteGuideVideoType);
	$Stmt->bindParam(':OnlineSiteGuideVideoCode', $OnlineSiteGuideVideoCode);
	$Stmt->bindParam(':OnlineSiteShVersion', $OnlineSiteShVersion);
	$Stmt->bindParam(':OnlineSiteShVersionDemo', $OnlineSiteShVersionDemo);
	$Stmt->bindParam(':OnlineSiteZip', $OnlineSiteZip);
	$Stmt->bindParam(':OnlineSiteAddr1', $OnlineSiteAddr1);
	$Stmt->bindParam(':OnlineSiteAddr2', $OnlineSiteAddr2);
	$Stmt->bindParam(':OnlineSiteSmsID', $OnlineSiteSmsID);
	if ($OnlineSiteSmsPW!=""){
		$Stmt->bindParam(':OnlineSiteSmsPW', $OnlineSiteSmsPW);
	}
	$Stmt->bindParam(':OnlineSiteSendNumber', $OnlineSiteSendNumber);
	$Stmt->bindParam(':OnlineSiteReceiveNumber', $OnlineSiteReceiveNumber);
	$Stmt->bindParam(':OnlineSiteLogoImage', $OnlineSiteLogoImage);
	$Stmt->bindParam(':OnlineSiteIntroText', $OnlineSiteIntroText);
	$Stmt->bindParam(':OnlineSiteState', $OnlineSiteState);
	$Stmt->bindParam(':OnlineSiteView', $OnlineSiteView);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->bindParam(':OnlineSiteID', $OnlineSiteID);
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
	header("Location: online_site_list.php?$ListParam"); 
	exit;
}
?>


