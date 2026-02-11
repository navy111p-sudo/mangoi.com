<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$BranchGroupID = isset($_REQUEST["BranchGroupID"]) ? $_REQUEST["BranchGroupID"] : "";
$BranchID = isset($_REQUEST["BranchID"]) ? $_REQUEST["BranchID"] : "";
$BranchName = isset($_REQUEST["BranchName"]) ? $_REQUEST["BranchName"] : "";
$BranchManagerName = isset($_REQUEST["BranchManagerName"]) ? $_REQUEST["BranchManagerName"] : "";

$BranchPhone1_1 = isset($_REQUEST["BranchPhone1_1"]) ? $_REQUEST["BranchPhone1_1"] : "";
$BranchPhone1_2 = isset($_REQUEST["BranchPhone1_2"]) ? $_REQUEST["BranchPhone1_2"] : "";
$BranchPhone1_3 = isset($_REQUEST["BranchPhone1_3"]) ? $_REQUEST["BranchPhone1_3"] : "";
$BranchPhone2_1 = isset($_REQUEST["BranchPhone2_1"]) ? $_REQUEST["BranchPhone2_1"] : "";
$BranchPhone2_2 = isset($_REQUEST["BranchPhone2_2"]) ? $_REQUEST["BranchPhone2_2"] : "";
$BranchPhone2_3 = isset($_REQUEST["BranchPhone2_3"]) ? $_REQUEST["BranchPhone2_3"] : "";
$BranchPhone3_1 = isset($_REQUEST["BranchPhone3_1"]) ? $_REQUEST["BranchPhone3_1"] : "";
$BranchPhone3_2 = isset($_REQUEST["BranchPhone3_2"]) ? $_REQUEST["BranchPhone3_2"] : "";
$BranchPhone3_3 = isset($_REQUEST["BranchPhone3_3"]) ? $_REQUEST["BranchPhone3_3"] : "";
$BranchEmail_1 = isset($_REQUEST["BranchEmail_1"]) ? $_REQUEST["BranchEmail_1"] : "";
$BranchEmail_2 = isset($_REQUEST["BranchEmail_2"]) ? $_REQUEST["BranchEmail_2"] : "";

$BranchZip = isset($_REQUEST["BranchZip"]) ? $_REQUEST["BranchZip"] : "";
$BranchAddr1 = isset($_REQUEST["BranchAddr1"]) ? $_REQUEST["BranchAddr1"] : "";
$BranchAddr2 = isset($_REQUEST["BranchAddr2"]) ? $_REQUEST["BranchAddr2"] : "";
$BranchLogoImage = isset($_REQUEST["BranchLogoImage"]) ? $_REQUEST["BranchLogoImage"] : "";
$BranchIntroText = isset($_REQUEST["BranchIntroText"]) ? $_REQUEST["BranchIntroText"] : "";
$BranchRegDateTime = isset($_REQUEST["BranchRegDateTime"]) ? $_REQUEST["BranchRegDateTime"] : "";
$BranchState = isset($_REQUEST["BranchState"]) ? $_REQUEST["BranchState"] : "";
$BranchView = isset($_REQUEST["BranchView"]) ? $_REQUEST["BranchView"] : "";

$BranchPhone1 = $BranchPhone1_1 . "-". $BranchPhone1_2 . "-" .$BranchPhone1_3;
$BranchPhone2 = $BranchPhone2_1 . "-". $BranchPhone2_2 . "-" .$BranchPhone2_3;
$BranchPhone3 = $BranchPhone3_1 . "-". $BranchPhone3_2 . "-" .$BranchPhone3_3;
$BranchEmail = $BranchEmail_1 . "@". $BranchEmail_2;

//Members 
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$MemberLoginPW = isset($_REQUEST["MemberLoginPW"]) ? $_REQUEST["MemberLoginPW"] : "";
$MemberLoginNewPW = isset($_REQUEST["MemberLoginNewPW"]) ? $_REQUEST["MemberLoginNewPW"] : "";
$MemberTimeZoneID = isset($_REQUEST["MemberTimeZoneID"]) ? $_REQUEST["MemberTimeZoneID"] : "";
$MemberLanguageID = isset($_REQUEST["MemberLanguageID"]) ? $_REQUEST["MemberLanguageID"] : "";


$MemberLoginNewPW_hash = password_hash(sha1($MemberLoginNewPW), PASSWORD_DEFAULT);

if ($BranchView!="1"){
	$BranchView = 0;
}

if ($BranchState!="1"){
	$BranchState = 2;
}


if ($BranchID==""){

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
		$Sql = "select ifnull(Max(BranchOrder),0) as BranchOrder from Branches";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$BranchOrder = $Row["BranchOrder"]+1;

		$Sql = " insert into Branches ( ";
			$Sql .= " BranchGroupID, ";
			$Sql .= " BranchName, ";
			$Sql .= " BranchManagerName, ";
			$Sql .= " BranchPhone1, ";
			$Sql .= " BranchPhone2, ";
			$Sql .= " BranchPhone3, ";
			$Sql .= " BranchEmail, ";
			$Sql .= " BranchZip, ";
			$Sql .= " BranchAddr1, ";
			$Sql .= " BranchAddr2, ";
			$Sql .= " BranchLogoImage, ";
			$Sql .= " BranchIntroText, ";
			$Sql .= " BranchRegDateTime, ";
			$Sql .= " BranchModiDateTime, ";
			$Sql .= " BranchState, ";
			$Sql .= " BranchView, ";
			$Sql .= " BranchOrder ";
		$Sql .= " ) values ( ";
			$Sql .= " :BranchGroupID, ";
			$Sql .= " :BranchName, ";
			$Sql .= " :BranchManagerName, ";
			$Sql .= " HEX(AES_ENCRYPT(:BranchPhone1, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:BranchPhone2, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:BranchPhone3, :EncryptionKey)), ";;
			$Sql .= " HEX(AES_ENCRYPT(:BranchEmail, :EncryptionKey)), ";
			$Sql .= " :BranchZip, ";
			$Sql .= " :BranchAddr1, ";
			$Sql .= " :BranchAddr2, ";
			$Sql .= " :BranchLogoImage, ";
			$Sql .= " :BranchIntroText, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " :BranchState, ";
			$Sql .= " :BranchView, ";
			$Sql .= " :BranchOrder ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':BranchGroupID', $BranchGroupID);
		$Stmt->bindParam(':BranchName', $BranchName);
		$Stmt->bindParam(':BranchManagerName', $BranchManagerName);
		$Stmt->bindParam(':BranchPhone1', $BranchPhone1);
		$Stmt->bindParam(':BranchPhone2', $BranchPhone2);
		$Stmt->bindParam(':BranchPhone3', $BranchPhone3);
		$Stmt->bindParam(':BranchEmail', $BranchEmail);
		$Stmt->bindParam(':BranchZip', $BranchZip);
		$Stmt->bindParam(':BranchAddr1', $BranchAddr1);
		$Stmt->bindParam(':BranchAddr2', $BranchAddr2);
		$Stmt->bindParam(':BranchLogoImage', $BranchLogoImage);
		$Stmt->bindParam(':BranchIntroText', $BranchIntroText);
		$Stmt->bindParam(':BranchState', $BranchState);
		$Stmt->bindParam(':BranchView', $BranchView);
		$Stmt->bindParam(':BranchOrder', $BranchOrder);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$BranchID = $DbConn->lastInsertId();
		$Stmt = null;


		//Members 
		$MemberLevelID = 9;//지사장

		$Sql = " insert into Members ( ";
			$Sql .= " BranchID, ";
			$Sql .= " MemberLanguageID, ";
			$Sql .= " MemberTimeZoneID, ";
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

			$Sql .= " :BranchID, ";
			$Sql .= " :MemberLanguageID, ";
			$Sql .= " :MemberTimeZoneID, ";
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
		$Stmt->bindParam(':BranchID', $BranchID);
		$Stmt->bindParam(':MemberLanguageID', $MemberLanguageID);
		$Stmt->bindParam(':MemberTimeZoneID', $MemberTimeZoneID);
		$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
		$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
		if ($MemberLoginNewPW!=""){
			$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
		}
		$Stmt->bindParam(':MemberName', $BranchManagerName);
		$Stmt->bindParam(':MemberEmail', $BranchEmail);
		$Stmt->bindParam(':MemberView', $BranchView);
		$Stmt->bindParam(':MemberState', $BranchState);

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
		$Sql = " update Branches set ";
			$Sql .= " BranchGroupID = :BranchGroupID, ";
			$Sql .= " BranchName = :BranchName, ";
			$Sql .= " BranchManagerName = :BranchManagerName, ";
			$Sql .= " BranchPhone1 = HEX(AES_ENCRYPT(:BranchPhone1, :EncryptionKey)), ";
			$Sql .= " BranchPhone2 = HEX(AES_ENCRYPT(:BranchPhone2, :EncryptionKey)), ";
			$Sql .= " BranchPhone3 = HEX(AES_ENCRYPT(:BranchPhone3, :EncryptionKey)), ";
			$Sql .= " BranchEmail = HEX(AES_ENCRYPT(:BranchEmail, :EncryptionKey)), ";
			$Sql .= " BranchZip = :BranchZip, ";
			$Sql .= " BranchAddr1 = :BranchAddr1, ";
			$Sql .= " BranchAddr2 = :BranchAddr2, ";
			$Sql .= " BranchLogoImage = :BranchLogoImage, ";
			$Sql .= " BranchIntroText = :BranchIntroText, ";
			$Sql .= " BranchState = :BranchState, ";
			$Sql .= " BranchView = :BranchView, ";
			$Sql .= " BranchModiDateTime = now() ";
		$Sql .= " where BranchID = :BranchID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':BranchGroupID', $BranchGroupID);
		$Stmt->bindParam(':BranchName', $BranchName);
		$Stmt->bindParam(':BranchManagerName', $BranchManagerName);
		$Stmt->bindParam(':BranchPhone1', $BranchPhone1);
		$Stmt->bindParam(':BranchPhone2', $BranchPhone2);
		$Stmt->bindParam(':BranchPhone3', $BranchPhone3);
		$Stmt->bindParam(':BranchEmail', $BranchEmail);
		$Stmt->bindParam(':BranchZip', $BranchZip);
		$Stmt->bindParam(':BranchAddr1', $BranchAddr1);
		$Stmt->bindParam(':BranchAddr2', $BranchAddr2);
		$Stmt->bindParam(':BranchLogoImage', $BranchLogoImage);
		$Stmt->bindParam(':BranchIntroText', $BranchIntroText);
		$Stmt->bindParam(':BranchState', $BranchState);
		$Stmt->bindParam(':BranchView', $BranchView);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->bindParam(':BranchID', $BranchID);
		$Stmt->execute();
		$Stmt = null;


		//Members 
		$Sql = " update Members set ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " MemberLoginPW = :MemberLoginNewPW_hash, ";
			}
			$Sql .= " MemberLanguageID = :MemberLanguageID, ";
			$Sql .= " MemberTimeZoneID = :MemberTimeZoneID, ";
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
		$Stmt->bindParam(':MemberTimeZoneID', $MemberTimeZoneID);
		$Stmt->bindParam(':MemberName', $BranchManagerName);
		$Stmt->bindParam(':MemberEmail', $BranchEmail);
		$Stmt->bindParam(':MemberView', $BranchView);
		$Stmt->bindParam(':MemberState', $BranchState);

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
	header("Location: branch_list.php?$ListParam"); 
	exit;
}
?>


