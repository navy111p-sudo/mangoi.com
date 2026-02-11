<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$CompanyID = isset($_REQUEST["CompanyID"]) ? $_REQUEST["CompanyID"] : "";
$FranchiseID = isset($_REQUEST["FranchiseID"]) ? $_REQUEST["FranchiseID"] : "";
$CompanyName = isset($_REQUEST["CompanyName"]) ? $_REQUEST["CompanyName"] : "";
$CompanyManagerName = isset($_REQUEST["CompanyManagerName"]) ? $_REQUEST["CompanyManagerName"] : "";

$CompanyPhone1_1 = isset($_REQUEST["CompanyPhone1_1"]) ? $_REQUEST["CompanyPhone1_1"] : "";
$CompanyPhone1_2 = isset($_REQUEST["CompanyPhone1_2"]) ? $_REQUEST["CompanyPhone1_2"] : "";
$CompanyPhone1_3 = isset($_REQUEST["CompanyPhone1_3"]) ? $_REQUEST["CompanyPhone1_3"] : "";
$CompanyPhone2_1 = isset($_REQUEST["CompanyPhone2_1"]) ? $_REQUEST["CompanyPhone2_1"] : "";
$CompanyPhone2_2 = isset($_REQUEST["CompanyPhone2_2"]) ? $_REQUEST["CompanyPhone2_2"] : "";
$CompanyPhone2_3 = isset($_REQUEST["CompanyPhone2_3"]) ? $_REQUEST["CompanyPhone2_3"] : "";
$CompanyPhone3_1 = isset($_REQUEST["CompanyPhone3_1"]) ? $_REQUEST["CompanyPhone3_1"] : "";
$CompanyPhone3_2 = isset($_REQUEST["CompanyPhone3_2"]) ? $_REQUEST["CompanyPhone3_2"] : "";
$CompanyPhone3_3 = isset($_REQUEST["CompanyPhone3_3"]) ? $_REQUEST["CompanyPhone3_3"] : "";
$CompanyEmail_1 = isset($_REQUEST["CompanyEmail_1"]) ? $_REQUEST["CompanyEmail_1"] : "";
$CompanyEmail_2 = isset($_REQUEST["CompanyEmail_2"]) ? $_REQUEST["CompanyEmail_2"] : "";

$CompanyPricePerTime = isset($_REQUEST["CompanyPricePerTime"]) ? $_REQUEST["CompanyPricePerTime"] : "";
$CompanyZip = isset($_REQUEST["CompanyZip"]) ? $_REQUEST["CompanyZip"] : "";
$CompanyAddr1 = isset($_REQUEST["CompanyAddr1"]) ? $_REQUEST["CompanyAddr1"] : "";
$CompanyAddr2 = isset($_REQUEST["CompanyAddr2"]) ? $_REQUEST["CompanyAddr2"] : "";
$CompanyLogoImage = isset($_REQUEST["CompanyLogoImage"]) ? $_REQUEST["CompanyLogoImage"] : "";
$CompanyIntroText = isset($_REQUEST["CompanyIntroText"]) ? $_REQUEST["CompanyIntroText"] : "";
$CompanyRegDateTime = isset($_REQUEST["CompanyRegDateTime"]) ? $_REQUEST["CompanyRegDateTime"] : "";
$CompanyState = isset($_REQUEST["CompanyState"]) ? $_REQUEST["CompanyState"] : "";
$CompanyView = isset($_REQUEST["CompanyView"]) ? $_REQUEST["CompanyView"] : "";

$CompanyPhone1 = $CompanyPhone1_1 . "-". $CompanyPhone1_2 . "-" .$CompanyPhone1_3;
$CompanyPhone2 = $CompanyPhone2_1 . "-". $CompanyPhone2_2 . "-" .$CompanyPhone2_3;
$CompanyPhone3 = $CompanyPhone3_1 . "-". $CompanyPhone3_2 . "-" .$CompanyPhone3_3;
$CompanyEmail = $CompanyEmail_1 . "@". $CompanyEmail_2;


if ($CompanyView!="1"){
	$CompanyView = 0;
}

if ($CompanyState!="1"){
	$CompanyState = 2;
}


if ($CompanyID==""){

	$Sql = "select ifnull(Max(CompanyOrder),0) as CompanyOrder from Companies";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$CompanyOrder = $Row["CompanyOrder"]+1;

	$Sql = " insert into Companies ( ";
		$Sql .= " FranchiseID, ";
		$Sql .= " CompanyName, ";
		$Sql .= " CompanyManagerName, ";
		$Sql .= " CompanyPhone1, ";
		$Sql .= " CompanyPhone2, ";
		$Sql .= " CompanyPhone3, ";
		$Sql .= " CompanyEmail, ";
		$Sql .= " CompanyPricePerTime, ";
		$Sql .= " CompanyZip, ";
		$Sql .= " CompanyAddr1, ";
		$Sql .= " CompanyAddr2, ";
		$Sql .= " CompanyLogoImage, ";
		$Sql .= " CompanyIntroText, ";
		$Sql .= " CompanyRegDateTime, ";
		$Sql .= " CompanyModiDateTime, ";
		$Sql .= " CompanyState, ";
		$Sql .= " CompanyView, ";
		$Sql .= " CompanyOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :FranchiseID, ";
		$Sql .= " :CompanyName, ";
		$Sql .= " :CompanyManagerName, ";
		$Sql .= " HEX(AES_ENCRYPT(:CompanyPhone1, :EncryptionKey)), ";
		$Sql .= " HEX(AES_ENCRYPT(:CompanyPhone2, :EncryptionKey)), ";
		$Sql .= " HEX(AES_ENCRYPT(:CompanyPhone3, :EncryptionKey)), ";;
		$Sql .= " HEX(AES_ENCRYPT(:CompanyEmail, :EncryptionKey)), ";
		$Sql .= " :CompanyPricePerTime, ";
		$Sql .= " :CompanyZip, ";
		$Sql .= " :CompanyAddr1, ";
		$Sql .= " :CompanyAddr2, ";
		$Sql .= " :CompanyLogoImage, ";
		$Sql .= " :CompanyIntroText, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :CompanyState, ";
		$Sql .= " :CompanyView, ";
		$Sql .= " :CompanyOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':FranchiseID', $FranchiseID);
	$Stmt->bindParam(':CompanyName', $CompanyName);
	$Stmt->bindParam(':CompanyManagerName', $CompanyManagerName);
	$Stmt->bindParam(':CompanyPhone1', $CompanyPhone1);
	$Stmt->bindParam(':CompanyPhone2', $CompanyPhone2);
	$Stmt->bindParam(':CompanyPhone3', $CompanyPhone3);
	$Stmt->bindParam(':CompanyEmail', $CompanyEmail);
	$Stmt->bindParam(':CompanyPricePerTime', $CompanyPricePerTime);
	$Stmt->bindParam(':CompanyZip', $CompanyZip);
	$Stmt->bindParam(':CompanyAddr1', $CompanyAddr1);
	$Stmt->bindParam(':CompanyAddr2', $CompanyAddr2);
	$Stmt->bindParam(':CompanyLogoImage', $CompanyLogoImage);
	$Stmt->bindParam(':CompanyIntroText', $CompanyIntroText);
	$Stmt->bindParam(':CompanyState', $CompanyState);
	$Stmt->bindParam(':CompanyView', $CompanyView);
	$Stmt->bindParam(':CompanyOrder', $CompanyOrder);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update Companies set ";
		$Sql .= " FranchiseID = :FranchiseID, ";
		$Sql .= " CompanyName = :CompanyName, ";
		$Sql .= " CompanyManagerName = :CompanyManagerName, ";
		$Sql .= " CompanyPhone1 = HEX(AES_ENCRYPT(:CompanyPhone1, :EncryptionKey)), ";
		$Sql .= " CompanyPhone2 = HEX(AES_ENCRYPT(:CompanyPhone2, :EncryptionKey)), ";
		$Sql .= " CompanyPhone3 = HEX(AES_ENCRYPT(:CompanyPhone3, :EncryptionKey)), ";
		$Sql .= " CompanyEmail = HEX(AES_ENCRYPT(:CompanyEmail, :EncryptionKey)), ";
		$Sql .= " CompanyPricePerTime = :CompanyPricePerTime, ";
		$Sql .= " CompanyZip = :CompanyZip, ";
		$Sql .= " CompanyAddr1 = :CompanyAddr1, ";
		$Sql .= " CompanyAddr2 = :CompanyAddr2, ";
		$Sql .= " CompanyLogoImage = :CompanyLogoImage, ";
		$Sql .= " CompanyIntroText = :CompanyIntroText, ";
		$Sql .= " CompanyState = :CompanyState, ";
		$Sql .= " CompanyView = :CompanyView, ";
		$Sql .= " CompanyModiDateTime = now() ";
	$Sql .= " where CompanyID = :CompanyID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':FranchiseID', $FranchiseID);
	$Stmt->bindParam(':CompanyName', $CompanyName);
	$Stmt->bindParam(':CompanyManagerName', $CompanyManagerName);
	$Stmt->bindParam(':CompanyPhone1', $CompanyPhone1);
	$Stmt->bindParam(':CompanyPhone2', $CompanyPhone2);
	$Stmt->bindParam(':CompanyPhone3', $CompanyPhone3);
	$Stmt->bindParam(':CompanyEmail', $CompanyEmail);
	$Stmt->bindParam(':CompanyPricePerTime', $CompanyPricePerTime);
	$Stmt->bindParam(':CompanyZip', $CompanyZip);
	$Stmt->bindParam(':CompanyAddr1', $CompanyAddr1);
	$Stmt->bindParam(':CompanyAddr2', $CompanyAddr2);
	$Stmt->bindParam(':CompanyLogoImage', $CompanyLogoImage);
	$Stmt->bindParam(':CompanyIntroText', $CompanyIntroText);
	$Stmt->bindParam(':CompanyState', $CompanyState);
	$Stmt->bindParam(':CompanyView', $CompanyView);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->bindParam(':CompanyID', $CompanyID);
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
	header("Location: company_list.php?$ListParam"); 
	exit;
}
?>


