<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$ManagerID = isset($_REQUEST["ManagerID"]) ? $_REQUEST["ManagerID"] : "";
$FranchiseID = isset($_REQUEST["FranchiseID"]) ? $_REQUEST["FranchiseID"] : "";
$ManagerName = isset($_REQUEST["ManagerName"]) ? $_REQUEST["ManagerName"] : "";
$ManagerCompanyName = isset($_REQUEST["ManagerCompanyName"]) ? $_REQUEST["ManagerCompanyName"] : "";

$ManagerPhone1_1 = isset($_REQUEST["ManagerPhone1_1"]) ? $_REQUEST["ManagerPhone1_1"] : "";
$ManagerPhone1_2 = isset($_REQUEST["ManagerPhone1_2"]) ? $_REQUEST["ManagerPhone1_2"] : "";
$ManagerPhone1_3 = isset($_REQUEST["ManagerPhone1_3"]) ? $_REQUEST["ManagerPhone1_3"] : "";
$ManagerPhone2_1 = isset($_REQUEST["ManagerPhone2_1"]) ? $_REQUEST["ManagerPhone2_1"] : "";
$ManagerPhone2_2 = isset($_REQUEST["ManagerPhone2_2"]) ? $_REQUEST["ManagerPhone2_2"] : "";
$ManagerPhone2_3 = isset($_REQUEST["ManagerPhone2_3"]) ? $_REQUEST["ManagerPhone2_3"] : "";
$ManagerPhone3_1 = isset($_REQUEST["ManagerPhone3_1"]) ? $_REQUEST["ManagerPhone3_1"] : "";
$ManagerPhone3_2 = isset($_REQUEST["ManagerPhone3_2"]) ? $_REQUEST["ManagerPhone3_2"] : "";
$ManagerPhone3_3 = isset($_REQUEST["ManagerPhone3_3"]) ? $_REQUEST["ManagerPhone3_3"] : "";
$ManagerEmail_1 = isset($_REQUEST["ManagerEmail_1"]) ? $_REQUEST["ManagerEmail_1"] : "";
$ManagerEmail_2 = isset($_REQUEST["ManagerEmail_2"]) ? $_REQUEST["ManagerEmail_2"] : "";

$ManagerZip = isset($_REQUEST["ManagerZip"]) ? $_REQUEST["ManagerZip"] : "";
$ManagerAddr1 = isset($_REQUEST["ManagerAddr1"]) ? $_REQUEST["ManagerAddr1"] : "";
$ManagerAddr2 = isset($_REQUEST["ManagerAddr2"]) ? $_REQUEST["ManagerAddr2"] : "";
$ManagerLogoImage = isset($_REQUEST["ManagerLogoImage"]) ? $_REQUEST["ManagerLogoImage"] : "";
$ManagerIntroText = isset($_REQUEST["ManagerIntroText"]) ? $_REQUEST["ManagerIntroText"] : "";
$ManagerRegDateTime = isset($_REQUEST["ManagerRegDateTime"]) ? $_REQUEST["ManagerRegDateTime"] : "";
$ManagerState = isset($_REQUEST["ManagerState"]) ? $_REQUEST["ManagerState"] : "";
$ManagerView = isset($_REQUEST["ManagerView"]) ? $_REQUEST["ManagerView"] : "";

$ManagerPhone1 = $ManagerPhone1_1 . "-". $ManagerPhone1_2 . "-" .$ManagerPhone1_3;
$ManagerPhone2 = $ManagerPhone2_1 . "-". $ManagerPhone2_2 . "-" .$ManagerPhone2_3;
$ManagerPhone3 = $ManagerPhone3_1 . "-". $ManagerPhone3_2 . "-" .$ManagerPhone3_3;
$ManagerEmail = $ManagerEmail_1 . "@". $ManagerEmail_2;

//Members 
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$MemberLoginPW = isset($_REQUEST["MemberLoginPW"]) ? $_REQUEST["MemberLoginPW"] : "";
$MemberLoginNewPW = isset($_REQUEST["MemberLoginNewPW"]) ? $_REQUEST["MemberLoginNewPW"] : "";
$MemberLanguageID = isset($_REQUEST["MemberLanguageID"]) ? $_REQUEST["MemberLanguageID"] : "";

$MemberLoginNewPW_hash = password_hash(sha1($MemberLoginNewPW), PASSWORD_DEFAULT);


if ($ManagerView!="1"){
	$ManagerView = 0;
}

if ($ManagerState!="1"){
	$ManagerState = 2;
} 


if ($ManagerID==""){

	$Sql = "select count(*) as TotalRowCount from Members where MemberLoginID=:MemberLoginID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TotalRowCount = $Row["TotalRowCount"];

	if ($TotalRowCount > 0){
		$err_num = 1;
		echo "<script>alert('Error : ID is a duplicate!!');history.go(-1);</script>";
	}else{
		$Sql = "select ifnull(Max(ManagerOrder),0) as ManagerOrder from Managers";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$ManagerOrder = $Row["ManagerOrder"]+1;

		$Sql = " insert into Managers ( ";
			$Sql .= " FranchiseID, ";
			$Sql .= " ManagerName, ";
			$Sql .= " ManagerCompanyName, ";
			$Sql .= " ManagerPhone1, ";
			$Sql .= " ManagerPhone2, ";
			$Sql .= " ManagerPhone3, ";
			$Sql .= " ManagerEmail, ";
			$Sql .= " ManagerZip, ";
			$Sql .= " ManagerAddr1, ";
			$Sql .= " ManagerAddr2, ";
			$Sql .= " ManagerLogoImage, ";
			$Sql .= " ManagerIntroText, ";
			$Sql .= " ManagerRegDateTime, ";
			$Sql .= " ManagerModiDateTime, ";
			$Sql .= " ManagerState, ";
			$Sql .= " ManagerView, ";
			$Sql .= " ManagerOrder ";
		$Sql .= " ) values ( ";
			$Sql .= " :FranchiseID, ";
			$Sql .= " :ManagerName, ";
			$Sql .= " :ManagerCompanyName, ";
			$Sql .= " HEX(AES_ENCRYPT(:ManagerPhone1, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:ManagerPhone2, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:ManagerPhone3, :EncryptionKey)), ";;
			$Sql .= " HEX(AES_ENCRYPT(:ManagerEmail, :EncryptionKey)), ";
			$Sql .= " :ManagerZip, ";
			$Sql .= " :ManagerAddr1, ";
			$Sql .= " :ManagerAddr2, ";
			$Sql .= " :ManagerLogoImage, ";
			$Sql .= " :ManagerIntroText, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " :ManagerState, ";
			$Sql .= " :ManagerView, ";
			$Sql .= " :ManagerOrder ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':FranchiseID', $FranchiseID);
		$Stmt->bindParam(':ManagerName', $ManagerName);
		$Stmt->bindParam(':ManagerCompanyName', $ManagerCompanyName);
		$Stmt->bindParam(':ManagerPhone1', $ManagerPhone1);
		$Stmt->bindParam(':ManagerPhone2', $ManagerPhone2);
		$Stmt->bindParam(':ManagerPhone3', $ManagerPhone3);
		$Stmt->bindParam(':ManagerEmail', $ManagerEmail);
		$Stmt->bindParam(':ManagerZip', $ManagerZip);
		$Stmt->bindParam(':ManagerAddr1', $ManagerAddr1);
		$Stmt->bindParam(':ManagerAddr2', $ManagerAddr2);
		$Stmt->bindParam(':ManagerLogoImage', $ManagerLogoImage);
		$Stmt->bindParam(':ManagerIntroText', $ManagerIntroText);
		$Stmt->bindParam(':ManagerState', $ManagerState);
		$Stmt->bindParam(':ManagerView', $ManagerView);
		$Stmt->bindParam(':ManagerOrder', $ManagerOrder);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$ManagerID = $DbConn->lastInsertId();
		$Stmt = null;

		//Members 
		$MemberLevelID = 5;//영업본부

		$Sql = " insert into Members ( ";
			$Sql .= " ManagerID, ";
			$Sql .= " MemberLanguageID, ";
			$Sql .= " MemberLevelID, ";
			$Sql .= " MemberLoginID, ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " MemberLoginPW, ";
			}
			$Sql .= " MemberName, ";
			$Sql .= " MemberEmail, ";
			$Sql .= " MemberView, ";
			$Sql .= " MemberState, ";
			$Sql .= " MemberRegDateTime, ";
			$Sql .= " MemberModiDateTime ";

		$Sql .= " ) values ( ";

			$Sql .= " :ManagerID, ";
			$Sql .= " :MemberLanguageID, ";
			$Sql .= " :MemberLevelID, ";
			$Sql .= " :MemberLoginID, ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " :MemberLoginNewPW_hash, ";
			}
			$Sql .= " :MemberName, ";
			$Sql .= " HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
			$Sql .= " :MemberView, ";
			$Sql .= " :MemberState, ";
			$Sql .= " now(), ";
			$Sql .= " now() ";

		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ManagerID', $ManagerID);
		$Stmt->bindParam(':MemberLanguageID', $MemberLanguageID);
		$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
		$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
		if ($MemberLoginNewPW!=""){
			$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
		}
		$Stmt->bindParam(':MemberName', $ManagerName);
		$Stmt->bindParam(':MemberEmail', $ManagerEmail);
		$Stmt->bindParam(':MemberView', $ManagerView);
		$Stmt->bindParam(':MemberState', $ManagerState);

		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$Stmt = null;
	}

}else{

	$Sql = "select count(*) as TotalRowCount from Members where MemberLoginID=:MemberLoginID and MemberID<>:MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TotalRowCount = $Row["TotalRowCount"];

	if ($TotalRowCount > 0){
		$err_num = 1;
		echo "<script>alert('Error : ID is a duplicate!!');history.go(-1);</script>";
	}else{
		$Sql = " update Managers set ";
			$Sql .= " FranchiseID = :FranchiseID, ";
			$Sql .= " ManagerName = :ManagerName, ";
			$Sql .= " ManagerCompanyName = :ManagerCompanyName, ";
			$Sql .= " ManagerPhone1 = HEX(AES_ENCRYPT(:ManagerPhone1, :EncryptionKey)), ";
			$Sql .= " ManagerPhone2 = HEX(AES_ENCRYPT(:ManagerPhone2, :EncryptionKey)), ";
			$Sql .= " ManagerPhone3 = HEX(AES_ENCRYPT(:ManagerPhone3, :EncryptionKey)), ";
			$Sql .= " ManagerEmail = HEX(AES_ENCRYPT(:ManagerEmail, :EncryptionKey)), ";
			$Sql .= " ManagerZip = :ManagerZip, ";
			$Sql .= " ManagerAddr1 = :ManagerAddr1, ";
			$Sql .= " ManagerAddr2 = :ManagerAddr2, ";
			$Sql .= " ManagerLogoImage = :ManagerLogoImage, ";
			$Sql .= " ManagerIntroText = :ManagerIntroText, ";
			$Sql .= " ManagerState = :ManagerState, ";
			$Sql .= " ManagerView = :ManagerView, ";
			$Sql .= " ManagerModiDateTime = now() ";
		$Sql .= " where ManagerID = :ManagerID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':FranchiseID', $FranchiseID);
		$Stmt->bindParam(':ManagerName', $ManagerName);
		$Stmt->bindParam(':ManagerCompanyName', $ManagerCompanyName);
		$Stmt->bindParam(':ManagerPhone1', $ManagerPhone1);
		$Stmt->bindParam(':ManagerPhone2', $ManagerPhone2);
		$Stmt->bindParam(':ManagerPhone3', $ManagerPhone3);
		$Stmt->bindParam(':ManagerEmail', $ManagerEmail);
		$Stmt->bindParam(':ManagerZip', $ManagerZip);
		$Stmt->bindParam(':ManagerAddr1', $ManagerAddr1);
		$Stmt->bindParam(':ManagerAddr2', $ManagerAddr2);
		$Stmt->bindParam(':ManagerLogoImage', $ManagerLogoImage);
		$Stmt->bindParam(':ManagerIntroText', $ManagerIntroText);
		$Stmt->bindParam(':ManagerState', $ManagerState);
		$Stmt->bindParam(':ManagerView', $ManagerView);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->bindParam(':ManagerID', $ManagerID);
		$Stmt->execute();
		$Stmt = null;


		//Members 
		$Sql = " update Members set ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " MemberLoginPW = :MemberLoginNewPW_hash, ";
			}
			$Sql .= " MemberLanguageID = :MemberLanguageID, ";
			$Sql .= " MemberName = :MemberName, ";
			$Sql .= " MemberEmail = HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
			$Sql .= " MemberView = :MemberView, ";
			$Sql .= " MemberState = :MemberState, ";
			$Sql .= " MemberModiDateTime = now() ";
		$Sql .= " where MemberID = :MemberID ";

		$Stmt = $DbConn->prepare($Sql);
		if ($MemberLoginNewPW!=""){
			$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
		}
		$Stmt->bindParam(':MemberLanguageID', $MemberLanguageID);
		$Stmt->bindParam(':MemberName', $ManagerName);
		$Stmt->bindParam(':MemberEmail', $ManagerEmail);
		$Stmt->bindParam(':MemberView', $ManagerView);
		$Stmt->bindParam(':MemberState', $ManagerState);

		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->execute();
		$Stmt = null;
	}
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
	header("Location: manager_list.php?$ListParam"); 
	exit;
}
?>


