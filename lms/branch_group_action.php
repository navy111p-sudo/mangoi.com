<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('../includes/password_hash.php');


$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$CompanyID = isset($_REQUEST["CompanyID"]) ? $_REQUEST["CompanyID"] : "";
$BranchGroupID = isset($_REQUEST["BranchGroupID"]) ? $_REQUEST["BranchGroupID"] : "";
$BranchGroupName = isset($_REQUEST["BranchGroupName"]) ? $_REQUEST["BranchGroupName"] : "";
$BranchGroupManagerName = isset($_REQUEST["BranchGroupManagerName"]) ? $_REQUEST["BranchGroupManagerName"] : "";

$BranchGroupPhone1_1 = isset($_REQUEST["BranchGroupPhone1_1"]) ? $_REQUEST["BranchGroupPhone1_1"] : "";
$BranchGroupPhone1_2 = isset($_REQUEST["BranchGroupPhone1_2"]) ? $_REQUEST["BranchGroupPhone1_2"] : "";
$BranchGroupPhone1_3 = isset($_REQUEST["BranchGroupPhone1_3"]) ? $_REQUEST["BranchGroupPhone1_3"] : "";
$BranchGroupPhone2_1 = isset($_REQUEST["BranchGroupPhone2_1"]) ? $_REQUEST["BranchGroupPhone2_1"] : "";
$BranchGroupPhone2_2 = isset($_REQUEST["BranchGroupPhone2_2"]) ? $_REQUEST["BranchGroupPhone2_2"] : "";
$BranchGroupPhone2_3 = isset($_REQUEST["BranchGroupPhone2_3"]) ? $_REQUEST["BranchGroupPhone2_3"] : "";
$BranchGroupPhone3_1 = isset($_REQUEST["BranchGroupPhone3_1"]) ? $_REQUEST["BranchGroupPhone3_1"] : "";
$BranchGroupPhone3_2 = isset($_REQUEST["BranchGroupPhone3_2"]) ? $_REQUEST["BranchGroupPhone3_2"] : "";
$BranchGroupPhone3_3 = isset($_REQUEST["BranchGroupPhone3_3"]) ? $_REQUEST["BranchGroupPhone3_3"] : "";
$BranchGroupEmail_1 = isset($_REQUEST["BranchGroupEmail_1"]) ? $_REQUEST["BranchGroupEmail_1"] : "";
$BranchGroupEmail_2 = isset($_REQUEST["BranchGroupEmail_2"]) ? $_REQUEST["BranchGroupEmail_2"] : "";

$BranchGroupZip = isset($_REQUEST["BranchGroupZip"]) ? $_REQUEST["BranchGroupZip"] : "";
$BranchGroupAddr1 = isset($_REQUEST["BranchGroupAddr1"]) ? $_REQUEST["BranchGroupAddr1"] : "";
$BranchGroupAddr2 = isset($_REQUEST["BranchGroupAddr2"]) ? $_REQUEST["BranchGroupAddr2"] : "";
$BranchGroupLogoImage = isset($_REQUEST["BranchGroupLogoImage"]) ? $_REQUEST["BranchGroupLogoImage"] : "";
$BranchGroupIntroText = isset($_REQUEST["BranchGroupIntroText"]) ? $_REQUEST["BranchGroupIntroText"] : "";
$BranchGroupRegDateTime = isset($_REQUEST["BranchGroupRegDateTime"]) ? $_REQUEST["BranchGroupRegDateTime"] : "";
$BranchGroupState = isset($_REQUEST["BranchGroupState"]) ? $_REQUEST["BranchGroupState"] : "";
$BranchGroupView = isset($_REQUEST["BranchGroupView"]) ? $_REQUEST["BranchGroupView"] : "";

$BranchGroupPhone1 = $BranchGroupPhone1_1 . "-". $BranchGroupPhone1_2 . "-" .$BranchGroupPhone1_3;
$BranchGroupPhone2 = $BranchGroupPhone2_1 . "-". $BranchGroupPhone2_2 . "-" .$BranchGroupPhone2_3;
$BranchGroupPhone3 = $BranchGroupPhone3_1 . "-". $BranchGroupPhone3_2 . "-" .$BranchGroupPhone3_3;
$BranchGroupEmail = $BranchGroupEmail_1 . "@". $BranchGroupEmail_2;

//Members 
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$MemberLoginPW = isset($_REQUEST["MemberLoginPW"]) ? $_REQUEST["MemberLoginPW"] : "";
$MemberLoginNewPW = isset($_REQUEST["MemberLoginNewPW"]) ? $_REQUEST["MemberLoginNewPW"] : "";
$MemberTimeZoneID = isset($_REQUEST["MemberTimeZoneID"]) ? $_REQUEST["MemberTimeZoneID"] : "";
$MemberLanguageID = isset($_REQUEST["MemberLanguageID"]) ? $_REQUEST["MemberLanguageID"] : "";

$MemberLoginNewPW_hash = password_hash(sha1($MemberLoginNewPW), PASSWORD_DEFAULT);

if ($BranchGroupView!="1"){
	$BranchGroupView = 0;
}

if ($BranchGroupState!="1"){
	$BranchGroupState = 2;
}


if ($BranchGroupID==""){

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
		$Sql = "select ifnull(Max(BranchGroupOrder),0) as BranchGroupOrder from BranchGroups";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$BranchGroupOrder = $Row["BranchGroupOrder"]+1;

		$Sql = " insert into BranchGroups ( ";
			$Sql .= " CompanyID, ";
			$Sql .= " BranchGroupName, ";
			$Sql .= " BranchGroupManagerName, ";
			$Sql .= " BranchGroupPhone1, ";
			$Sql .= " BranchGroupPhone2, ";
			$Sql .= " BranchGroupPhone3, ";
			$Sql .= " BranchGroupEmail, ";
			$Sql .= " BranchGroupZip, ";
			$Sql .= " BranchGroupAddr1, ";
			$Sql .= " BranchGroupAddr2, ";
			$Sql .= " BranchGroupLogoImage, ";
			$Sql .= " BranchGroupIntroText, ";
			$Sql .= " BranchGroupRegDateTime, ";
			$Sql .= " BranchGroupModiDateTime, ";
			$Sql .= " BranchGroupState, ";
			$Sql .= " BranchGroupView, ";
			$Sql .= " BranchGroupOrder ";
		$Sql .= " ) values ( ";
			$Sql .= " :CompanyID, ";
			$Sql .= " :BranchGroupName, ";
			$Sql .= " :BranchGroupManagerName, ";
			$Sql .= " HEX(AES_ENCRYPT(:BranchGroupPhone1, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:BranchGroupPhone2, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:BranchGroupPhone3, :EncryptionKey)), ";;
			$Sql .= " HEX(AES_ENCRYPT(:BranchGroupEmail, :EncryptionKey)), ";
			$Sql .= " :BranchGroupZip, ";
			$Sql .= " :BranchGroupAddr1, ";
			$Sql .= " :BranchGroupAddr2, ";
			$Sql .= " :BranchGroupLogoImage, ";
			$Sql .= " :BranchGroupIntroText, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " :BranchGroupState, ";
			$Sql .= " :BranchGroupView, ";
			$Sql .= " :BranchGroupOrder ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':CompanyID', $CompanyID);
		$Stmt->bindParam(':BranchGroupName', $BranchGroupName);
		$Stmt->bindParam(':BranchGroupManagerName', $BranchGroupManagerName);
		$Stmt->bindParam(':BranchGroupPhone1', $BranchGroupPhone1);
		$Stmt->bindParam(':BranchGroupPhone2', $BranchGroupPhone2);
		$Stmt->bindParam(':BranchGroupPhone3', $BranchGroupPhone3);
		$Stmt->bindParam(':BranchGroupEmail', $BranchGroupEmail);
		$Stmt->bindParam(':BranchGroupZip', $BranchGroupZip);
		$Stmt->bindParam(':BranchGroupAddr1', $BranchGroupAddr1);
		$Stmt->bindParam(':BranchGroupAddr2', $BranchGroupAddr2);
		$Stmt->bindParam(':BranchGroupLogoImage', $BranchGroupLogoImage);
		$Stmt->bindParam(':BranchGroupIntroText', $BranchGroupIntroText);
		$Stmt->bindParam(':BranchGroupState', $BranchGroupState);
		$Stmt->bindParam(':BranchGroupView', $BranchGroupView);
		$Stmt->bindParam(':BranchGroupOrder', $BranchGroupOrder);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$BranchGroupID = $DbConn->lastInsertId();
		$Stmt = null;


		//Members 
		$MemberLevelID = 6;//대표지사장

		$Sql = " insert into Members ( ";
			$Sql .= " BranchGroupID, ";
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

			$Sql .= " :BranchGroupID, ";
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
		$Stmt->bindParam(':BranchGroupID', $BranchGroupID);
		$Stmt->bindParam(':MemberLanguageID', $MemberLanguageID);
		$Stmt->bindParam(':MemberTimeZoneID', $MemberTimeZoneID);
		$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
		$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
		if ($MemberLoginNewPW!=""){
			$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
		}
		$Stmt->bindParam(':MemberName', $BranchGroupManagerName);
		$Stmt->bindParam(':MemberEmail', $BranchGroupEmail);
		$Stmt->bindParam(':MemberView', $BranchGroupView);
		$Stmt->bindParam(':MemberState', $BranchGroupState);

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
		$Sql = " update BranchGroups set ";
			$Sql .= " CompanyID = :CompanyID, ";
			$Sql .= " BranchGroupName = :BranchGroupName, ";
			$Sql .= " BranchGroupManagerName = :BranchGroupManagerName, ";
			$Sql .= " BranchGroupPhone1 = HEX(AES_ENCRYPT(:BranchGroupPhone1, :EncryptionKey)), ";
			$Sql .= " BranchGroupPhone2 = HEX(AES_ENCRYPT(:BranchGroupPhone2, :EncryptionKey)), ";
			$Sql .= " BranchGroupPhone3 = HEX(AES_ENCRYPT(:BranchGroupPhone3, :EncryptionKey)), ";
			$Sql .= " BranchGroupEmail = HEX(AES_ENCRYPT(:BranchGroupEmail, :EncryptionKey)), ";
			$Sql .= " BranchGroupZip = :BranchGroupZip, ";
			$Sql .= " BranchGroupAddr1 = :BranchGroupAddr1, ";
			$Sql .= " BranchGroupAddr2 = :BranchGroupAddr2, ";
			$Sql .= " BranchGroupLogoImage = :BranchGroupLogoImage, ";
			$Sql .= " BranchGroupIntroText = :BranchGroupIntroText, ";
			$Sql .= " BranchGroupState = :BranchGroupState, ";
			$Sql .= " BranchGroupView = :BranchGroupView, ";
			$Sql .= " BranchGroupModiDateTime = now() ";
		$Sql .= " where BranchGroupID = :BranchGroupID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':CompanyID', $CompanyID);
		$Stmt->bindParam(':BranchGroupName', $BranchGroupName);
		$Stmt->bindParam(':BranchGroupManagerName', $BranchGroupManagerName);
		$Stmt->bindParam(':BranchGroupPhone1', $BranchGroupPhone1);
		$Stmt->bindParam(':BranchGroupPhone2', $BranchGroupPhone2);
		$Stmt->bindParam(':BranchGroupPhone3', $BranchGroupPhone3);
		$Stmt->bindParam(':BranchGroupEmail', $BranchGroupEmail);
		$Stmt->bindParam(':BranchGroupZip', $BranchGroupZip);
		$Stmt->bindParam(':BranchGroupAddr1', $BranchGroupAddr1);
		$Stmt->bindParam(':BranchGroupAddr2', $BranchGroupAddr2);
		$Stmt->bindParam(':BranchGroupLogoImage', $BranchGroupLogoImage);
		$Stmt->bindParam(':BranchGroupIntroText', $BranchGroupIntroText);
		$Stmt->bindParam(':BranchGroupState', $BranchGroupState);
		$Stmt->bindParam(':BranchGroupView', $BranchGroupView);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->bindParam(':BranchGroupID', $BranchGroupID);
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
		$Stmt->bindParam(':MemberName', $BranchGroupManagerName);
		$Stmt->bindParam(':MemberEmail', $BranchGroupEmail);
		$Stmt->bindParam(':MemberView', $BranchGroupView);
		$Stmt->bindParam(':MemberState', $BranchGroupState);

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
	header("Location: branch_group_list.php?$ListParam"); 
	exit;
}
?>


