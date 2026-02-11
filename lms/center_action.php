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

$OnlineSiteID = isset($_REQUEST["OnlineSiteID"]) ? $_REQUEST["OnlineSiteID"] : "";
$ManagerID = isset($_REQUEST["ManagerID"]) ? $_REQUEST["ManagerID"] : "";
$BranchID = isset($_REQUEST["BranchID"]) ? $_REQUEST["BranchID"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$CenterName = isset($_REQUEST["CenterName"]) ? $_REQUEST["CenterName"] : "";
$CenterManagerName = isset($_REQUEST["CenterManagerName"]) ? $_REQUEST["CenterManagerName"] : "";

$CenterPhone1_1 = isset($_REQUEST["CenterPhone1_1"]) ? $_REQUEST["CenterPhone1_1"] : "";
$CenterPhone1_2 = isset($_REQUEST["CenterPhone1_2"]) ? $_REQUEST["CenterPhone1_2"] : "";
$CenterPhone1_3 = isset($_REQUEST["CenterPhone1_3"]) ? $_REQUEST["CenterPhone1_3"] : "";
$CenterPhone2_1 = isset($_REQUEST["CenterPhone2_1"]) ? $_REQUEST["CenterPhone2_1"] : "";
$CenterPhone2_2 = isset($_REQUEST["CenterPhone2_2"]) ? $_REQUEST["CenterPhone2_2"] : "";
$CenterPhone2_3 = isset($_REQUEST["CenterPhone2_3"]) ? $_REQUEST["CenterPhone2_3"] : "";
$CenterPhone3_1 = isset($_REQUEST["CenterPhone3_1"]) ? $_REQUEST["CenterPhone3_1"] : "";
$CenterPhone3_2 = isset($_REQUEST["CenterPhone3_2"]) ? $_REQUEST["CenterPhone3_2"] : "";
$CenterPhone3_3 = isset($_REQUEST["CenterPhone3_3"]) ? $_REQUEST["CenterPhone3_3"] : "";
$CenterEmail_1 = isset($_REQUEST["CenterEmail_1"]) ? $_REQUEST["CenterEmail_1"] : "";
$CenterEmail_2 = isset($_REQUEST["CenterEmail_2"]) ? $_REQUEST["CenterEmail_2"] : "";

$CenterZip = isset($_REQUEST["CenterZip"]) ? $_REQUEST["CenterZip"] : "";
$CenterAddr1 = isset($_REQUEST["CenterAddr1"]) ? $_REQUEST["CenterAddr1"] : "";
$CenterAddr2 = isset($_REQUEST["CenterAddr2"]) ? $_REQUEST["CenterAddr2"] : "";
$CenterLogoImage = isset($_REQUEST["CenterLogoImage"]) ? $_REQUEST["CenterLogoImage"] : "";
$CenterIntroText = isset($_REQUEST["CenterIntroText"]) ? $_REQUEST["CenterIntroText"] : "";
$CenterRegDateTime = isset($_REQUEST["CenterRegDateTime"]) ? $_REQUEST["CenterRegDateTime"] : "";
$CenterState = isset($_REQUEST["CenterState"]) ? $_REQUEST["CenterState"] : "";
$CenterUseMyRank = isset($_REQUEST["CenterUseMyRank"]) ? $_REQUEST["CenterUseMyRank"] : "";
$CenterView = isset($_REQUEST["CenterView"]) ? $_REQUEST["CenterView"] : "";
$CenterFreeTrialCount = isset($_REQUEST["CenterFreeTrialCount"]) ? $_REQUEST["CenterFreeTrialCount"] : "";
$CenterPricePerGroup = isset($_REQUEST["CenterPricePerGroup"]) ? $_REQUEST["CenterPricePerGroup"] : "";
$CenterPricePerTime = isset($_REQUEST["CenterPricePerTime"]) ? $_REQUEST["CenterPricePerTime"] : "";
$CenterAcceptSms = isset($_REQUEST["CenterAcceptSms"]) ? $_REQUEST["CenterAcceptSms"] : "";
$CenterAcceptJoin = isset($_REQUEST["CenterAcceptJoin"]) ? $_REQUEST["CenterAcceptJoin"] : "";
$MemberAcceptCallByTeacher = isset($_REQUEST["MemberAcceptCallByTeacher"]) ? $_REQUEST["MemberAcceptCallByTeacher"] : "";

$CenterPerShVersion = isset($_REQUEST["CenterPerShVersion"]) ? $_REQUEST["CenterPerShVersion"] : "1";
$CenterPerShAllow = isset($_REQUEST["CenterPerShAllow"]) ? $_REQUEST["CenterPerShAllow"] : "1";


$CenterPayType = isset($_REQUEST["CenterPayType"]) ? $_REQUEST["CenterPayType"] : "";
$CenterRenewType = isset($_REQUEST["CenterRenewType"]) ? $_REQUEST["CenterRenewType"] : "";
if ($CenterPayType=="2"){
	$CenterRenewType = 1;
}

$CenterRenewStartYearMonthNum = isset($_REQUEST["CenterRenewStartYearMonthNum"]) ? $_REQUEST["CenterRenewStartYearMonthNum"] : "";

$CenterStudyEndDate = isset($_REQUEST["CenterStudyEndDate"]) ? $_REQUEST["CenterStudyEndDate"] : "";

$CenterPhone1 = $CenterPhone1_1 . "-". $CenterPhone1_2 . "-" .$CenterPhone1_3;
$CenterPhone2 = $CenterPhone2_1 . "-". $CenterPhone2_2 . "-" .$CenterPhone2_3;
$CenterPhone3 = $CenterPhone3_1 . "-". $CenterPhone3_2 . "-" .$CenterPhone3_3;
$CenterEmail = $CenterEmail_1 . "@". $CenterEmail_2;



//Members 
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$MemberLoginPW = isset($_REQUEST["MemberLoginPW"]) ? $_REQUEST["MemberLoginPW"] : "";
$MemberLoginNewPW = isset($_REQUEST["MemberLoginNewPW"]) ? $_REQUEST["MemberLoginNewPW"] : "";
$MemberLanguageID = isset($_REQUEST["MemberLanguageID"]) ? $_REQUEST["MemberLanguageID"] : "";
$MemberTimeZoneID = isset($_REQUEST["MemberTimeZoneID"]) ? $_REQUEST["MemberTimeZoneID"] : "";

$MemberLoginNewPW_hash = password_hash(sha1($MemberLoginNewPW), PASSWORD_DEFAULT);

// ===== Permission context =====
$AdminLevel = isset($_LINK_ADMIN_LEVEL_ID_) ? $_LINK_ADMIN_LEVEL_ID_ : 20;
$canEditFee = ($AdminLevel < 12); // 대리점/학생은 수강료 수정 불가

// Helper: get center scope
function _get_center_scope($DbConn, $CenterID) {
    $Sql = "select A.CenterID, A.BranchID, B.BranchGroupID from Centers A inner join Branches B on A.BranchID=B.BranchID where A.CenterID=:CenterID";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':CenterID', $CenterID);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;
    return $Row ?: ["CenterID"=>0, "BranchID"=>0, "BranchGroupID"=>0];
}

function _branch_belongs_to_group($DbConn, $BranchID, $BranchGroupID) {
    $Sql = "select count(*) as Cnt from Branches where BranchID=:BranchID and BranchGroupID=:BranchGroupID";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':BranchID', $BranchID);
    $Stmt->bindParam(':BranchGroupID', $BranchGroupID);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;
    return ($Row && intval($Row['Cnt'])>0);
}


if ($CenterView!="1"){
	$CenterView = 0; 
}


if ($CenterUseMyRank!="1"){
	$CenterUseMyRank = 0;
}


if ($CenterID==""){

	// ===== INSERT scope checks =====
	if ($AdminLevel >= 12){
		$err_num = 1;
		$err_msg = "권한이 없습니다: 대리점은 대리점 생성/수강료 수정 불가";
	}
	if ($err_num==0 && ($AdminLevel==9 || $AdminLevel==10)){
		if (!isset($_LINK_ADMIN_BRANCH_ID_) || $_LINK_ADMIN_BRANCH_ID_ != $BranchID){
			$err_num = 1;
			$err_msg = "권한이 없습니다: 해당 지사 소속만 생성 가능";
		}
	}
	if ($err_num==0 && ($AdminLevel==6 || $AdminLevel==7)){
		if (!isset($_LINK_ADMIN_BRANCH_GROUP_ID_) || !_branch_belongs_to_group($DbConn, $BranchID, $_LINK_ADMIN_BRANCH_GROUP_ID_)){
			$err_num = 1;
			$err_msg = "권한이 없습니다: 대표지사 산하 지사만 생성 가능";
		}
	}

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
		$CenterRegMemberID = $_LINK_ADMIN_ID_;
		
		$Sql = "select ifnull(Max(CenterOrder),0) as CenterOrder from Centers";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$CenterOrder = $Row["CenterOrder"]+1;

		$Sql = " insert into Centers ( ";
			$Sql .= " CenterRegMemberID, ";
			$Sql .= " OnlineSiteID, ";
			$Sql .= " ManagerID, ";
			$Sql .= " BranchID, ";
			$Sql .= " CenterPayType, ";
			$Sql .= " CenterRenewType, ";
			$Sql .= " CenterRenewStartYearMonthNum, ";
			$Sql .= " CenterStudyEndDate, ";
			$Sql .= " CenterName, ";
			$Sql .= " CenterManagerName, ";
			$Sql .= " CenterUseMyRank, ";
			$Sql .= " CenterPhone1, ";
			$Sql .= " CenterPhone2, ";
			$Sql .= " CenterPhone3, ";
			$Sql .= " CenterEmail, ";
			$Sql .= " CenterPricePerGroup, ";
			$Sql .= " CenterPricePerTime, ";
			$Sql .= " CenterFreeTrialCount, ";
			$Sql .= " CenterAcceptSms, ";
			$Sql .= " CenterAcceptJoin, ";
			$Sql .= " CenterPerShVersion, ";
			$Sql .= " CenterPerShAllow, ";
			$Sql .= " MemberAcceptCallByTeacher, ";
			$Sql .= " CenterZip, ";
			$Sql .= " CenterAddr1, ";
			$Sql .= " CenterAddr2, ";
			$Sql .= " CenterLogoImage, ";
			$Sql .= " CenterIntroText, ";
			$Sql .= " CenterRegDateTime, ";
			$Sql .= " CenterModiDateTime, ";
			$Sql .= " CenterState, ";
			$Sql .= " CenterView, ";
			$Sql .= " CenterOrder ";
		$Sql .= " ) values ( ";
			$Sql .= " :CenterRegMemberID, ";
			$Sql .= " :OnlineSiteID, ";
			$Sql .= " :ManagerID, ";
			$Sql .= " :BranchID, ";
			$Sql .= " :CenterPayType, ";
			$Sql .= " :CenterRenewType, ";
			$Sql .= " :CenterRenewStartYearMonthNum, ";
			$Sql .= " :CenterStudyEndDate, ";
			$Sql .= " :CenterName, ";
			$Sql .= " :CenterManagerName, ";
			$Sql .= " :CenterUseMyRank, ";
			$Sql .= " HEX(AES_ENCRYPT(:CenterPhone1, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:CenterPhone2, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:CenterPhone3, :EncryptionKey)), ";;
			$Sql .= " HEX(AES_ENCRYPT(:CenterEmail, :EncryptionKey)), ";
			$Sql .= " :CenterPricePerGroup, ";
			$Sql .= " :CenterPricePerTime, ";
			$Sql .= " :CenterFreeTrialCount, ";
			$Sql .= " :CenterAcceptSms, ";
			$Sql .= " :CenterAcceptJoin, ";
			$Sql .= " :CenterPerShVersion, ";
			$Sql .= " :CenterPerShAllow, ";
			$Sql .= " :MemberAcceptCallByTeacher, ";
			$Sql .= " :CenterZip, ";
			$Sql .= " :CenterAddr1, ";
			$Sql .= " :CenterAddr2, ";
			$Sql .= " :CenterLogoImage, ";
			$Sql .= " :CenterIntroText, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " :CenterState, ";
			$Sql .= " :CenterView, ";
			$Sql .= " :CenterOrder ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':CenterRegMemberID', $CenterRegMemberID);
		$Stmt->bindParam(':OnlineSiteID', $OnlineSiteID);
		$Stmt->bindParam(':ManagerID', $ManagerID);
		$Stmt->bindParam(':BranchID', $BranchID);
		$Stmt->bindParam(':CenterPayType', $CenterPayType);
		$Stmt->bindParam(':CenterRenewType', $CenterRenewType);
		$Stmt->bindParam(':CenterRenewStartYearMonthNum', $CenterRenewStartYearMonthNum);
		$Stmt->bindParam(':CenterStudyEndDate', $CenterStudyEndDate);
		$Stmt->bindParam(':CenterName', $CenterName);
		$Stmt->bindParam(':CenterManagerName', $CenterManagerName);
		$Stmt->bindParam(':CenterUseMyRank', $CenterUseMyRank);
		$Stmt->bindParam(':CenterPhone1', $CenterPhone1);
		$Stmt->bindParam(':CenterPhone2', $CenterPhone2);
		$Stmt->bindParam(':CenterPhone3', $CenterPhone3);
		$Stmt->bindParam(':CenterEmail', $CenterEmail);
		$Stmt->bindParam(':CenterPricePerGroup', $CenterPricePerGroup);
		$Stmt->bindParam(':CenterPricePerTime', $CenterPricePerTime);
		$Stmt->bindParam(':CenterFreeTrialCount', $CenterFreeTrialCount);
		$Stmt->bindParam(':CenterAcceptSms', $CenterAcceptSms);
		$Stmt->bindParam(':CenterAcceptJoin', $CenterAcceptJoin);
		$Stmt->bindParam(':CenterPerShVersion', $CenterPerShVersion);
		$Stmt->bindParam(':CenterPerShAllow', $CenterPerShAllow);
		$Stmt->bindParam(':MemberAcceptCallByTeacher', $MemberAcceptCallByTeacher);
		$Stmt->bindParam(':CenterZip', $CenterZip);
		$Stmt->bindParam(':CenterAddr1', $CenterAddr1);
		$Stmt->bindParam(':CenterAddr2', $CenterAddr2);
		$Stmt->bindParam(':CenterLogoImage', $CenterLogoImage);
		$Stmt->bindParam(':CenterIntroText', $CenterIntroText);
		$Stmt->bindParam(':CenterState', $CenterState);
		$Stmt->bindParam(':CenterView', $CenterView);
		$Stmt->bindParam(':CenterOrder', $CenterOrder);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		if ($err_num==0){
			$Stmt->execute();
		}
		$CenterID = $DbConn->lastInsertId();
		$Stmt = null;


		//Members 
		$MemberLevelID = 12;//센터장(대리점장)

		$Sql = " insert into Members ( ";
			$Sql .= " CenterID, ";
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

			$Sql .= " :CenterID, ";
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
		$Stmt->bindParam(':CenterID', $CenterID);
		$Stmt->bindParam(':MemberLanguageID', $MemberLanguageID);
		$Stmt->bindParam(':MemberTimeZoneID', $MemberTimeZoneID);
		$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
		$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
		if ($MemberLoginNewPW!=""){
			$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
		}
		$Stmt->bindParam(':MemberName', $CenterManagerName);
		$Stmt->bindParam(':MemberEmail', $CenterEmail);
		$Stmt->bindParam(':MemberView', $CenterView);
		$Stmt->bindParam(':MemberState', $CenterState);

		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		if ($err_num==0){
			$Stmt->execute();
		}
		$Stmt = null;

		$MasterMessageType = 1;//대리점 신규 등록
		$MasterMessageText = $CenterName . "(".$CenterManagerName.") 신규등록 ";
		InsertMasterMessage($MasterMessageType, $MasterMessageText);
	}

}else{

	// ===== UPDATE scope checks =====
	$Scope = _get_center_scope($DbConn, $CenterID);
	$CenterBranchID = intval($Scope['BranchID']);
	$CenterBranchGroupID = intval($Scope['BranchGroupID']);

	if ($AdminLevel==9 || $AdminLevel==10){
		if (!isset($_LINK_ADMIN_BRANCH_ID_) || intval($_LINK_ADMIN_BRANCH_ID_) != $CenterBranchID){
			$err_num = 1;
			$err_msg = "권한이 없습니다: 해당 지사 소속만 수정 가능";
		}
	}else if ($AdminLevel==6 || $AdminLevel==7){
		if (!isset($_LINK_ADMIN_BRANCH_GROUP_ID_) || intval($_LINK_ADMIN_BRANCH_GROUP_ID_) != $CenterBranchGroupID){
			$err_num = 1;
			$err_msg = "권한이 없습니다: 대표지사 산하 지사만 수정 가능";
		}
	}

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
		// 대리점(또는 학생)인 경우 수강료 필드 무시 (변경 불가)
		if ($canEditFee==false){
			$SqlKeep = "select CenterPricePerGroup, CenterPricePerTime from Centers where CenterID=:CenterID";
			$StmtKeep = $DbConn->prepare($SqlKeep);
			$StmtKeep->bindParam(':CenterID', $CenterID);
			$StmtKeep->execute();
			$StmtKeep->setFetchMode(PDO::FETCH_ASSOC);
			$RowKeep = $StmtKeep->fetch();
			$StmtKeep = null;
			if ($RowKeep){
				$CenterPricePerGroup = $RowKeep['CenterPricePerGroup'];
				$CenterPricePerTime = $RowKeep['CenterPricePerTime'];
			}
		}
		$Sql = " update Centers set ";
			$Sql .= " OnlineSiteID = :OnlineSiteID, ";
			$Sql .= " ManagerID = :ManagerID, ";
			$Sql .= " BranchID = :BranchID, ";
			$Sql .= " CenterPayType = :CenterPayType, ";
			$Sql .= " CenterRenewType = :CenterRenewType, ";
			$Sql .= " CenterRenewStartYearMonthNum = :CenterRenewStartYearMonthNum, ";
			$Sql .= " CenterStudyEndDate = :CenterStudyEndDate, ";
			$Sql .= " CenterName = :CenterName, ";
			$Sql .= " CenterManagerName = :CenterManagerName, ";
			$Sql .= " CenterUseMyRank = :CenterUseMyRank, ";
			$Sql .= " CenterPhone1 = HEX(AES_ENCRYPT(:CenterPhone1, :EncryptionKey)), ";
			$Sql .= " CenterPhone2 = HEX(AES_ENCRYPT(:CenterPhone2, :EncryptionKey)), ";
			$Sql .= " CenterPhone3 = HEX(AES_ENCRYPT(:CenterPhone3, :EncryptionKey)), ";
			$Sql .= " CenterEmail = HEX(AES_ENCRYPT(:CenterEmail, :EncryptionKey)), ";
			$Sql .= " CenterPricePerGroup = :CenterPricePerGroup, ";
			$Sql .= " CenterPricePerTime = :CenterPricePerTime, ";
			$Sql .= " CenterFreeTrialCount = :CenterFreeTrialCount, ";
			$Sql .= " CenterAcceptSms = :CenterAcceptSms, ";
			$Sql .= " CenterAcceptJoin = :CenterAcceptJoin, ";
			$Sql .= " CenterPerShVersion = :CenterPerShVersion, ";
			$Sql .= " CenterPerShAllow = :CenterPerShAllow, ";
			$Sql .= " MemberAcceptCallByTeacher = :MemberAcceptCallByTeacher, ";
			$Sql .= " CenterZip = :CenterZip, ";
			$Sql .= " CenterAddr1 = :CenterAddr1, ";
			$Sql .= " CenterAddr2 = :CenterAddr2, ";
			$Sql .= " CenterLogoImage = :CenterLogoImage, ";
			$Sql .= " CenterIntroText = :CenterIntroText, ";
			$Sql .= " CenterState = :CenterState, ";
			$Sql .= " CenterView = :CenterView, ";
			$Sql .= " CenterModiDateTime = now() ";
		$Sql .= " where CenterID = :CenterID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':OnlineSiteID', $OnlineSiteID);
		$Stmt->bindParam(':ManagerID', $ManagerID);
		$Stmt->bindParam(':BranchID', $BranchID);
		$Stmt->bindParam(':CenterPayType', $CenterPayType);
		$Stmt->bindParam(':CenterRenewType', $CenterRenewType);
		$Stmt->bindParam(':CenterRenewStartYearMonthNum', $CenterRenewStartYearMonthNum);
		$Stmt->bindParam(':CenterStudyEndDate', $CenterStudyEndDate);
		$Stmt->bindParam(':CenterName', $CenterName);
		$Stmt->bindParam(':CenterManagerName', $CenterManagerName);
		$Stmt->bindParam(':CenterUseMyRank', $CenterUseMyRank);
		$Stmt->bindParam(':CenterPhone1', $CenterPhone1);
		$Stmt->bindParam(':CenterPhone2', $CenterPhone2);
		$Stmt->bindParam(':CenterPhone3', $CenterPhone3);
		$Stmt->bindParam(':CenterEmail', $CenterEmail);
		$Stmt->bindParam(':CenterPricePerGroup', $CenterPricePerGroup);
		$Stmt->bindParam(':CenterPricePerTime', $CenterPricePerTime);
		$Stmt->bindParam(':CenterFreeTrialCount', $CenterFreeTrialCount);
		$Stmt->bindParam(':CenterAcceptSms', $CenterAcceptSms);
		$Stmt->bindParam(':CenterAcceptJoin', $CenterAcceptJoin);
		$Stmt->bindParam(':CenterPerShVersion', $CenterPerShVersion);
		$Stmt->bindParam(':CenterPerShAllow', $CenterPerShAllow);
		$Stmt->bindParam(':MemberAcceptCallByTeacher', $MemberAcceptCallByTeacher);
		$Stmt->bindParam(':CenterZip', $CenterZip);
		$Stmt->bindParam(':CenterAddr1', $CenterAddr1);
		$Stmt->bindParam(':CenterAddr2', $CenterAddr2);
		$Stmt->bindParam(':CenterLogoImage', $CenterLogoImage);
		$Stmt->bindParam(':CenterIntroText', $CenterIntroText);
		$Stmt->bindParam(':CenterState', $CenterState);
		$Stmt->bindParam(':CenterView', $CenterView);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->bindParam(':CenterID', $CenterID);
		if ($err_num==0){
			$Stmt->execute();
		}
		$Stmt = null;


		//Members 
		$Sql = " update Members set ";
			$Sql .= " MemberLanguageID = :MemberLanguageID, ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " MemberLoginPW = :MemberLoginNewPW_hash, ";
			}
			$Sql .= " MemberTimeZoneID = :MemberTimeZoneID, ";
			$Sql .= " MemberName = :MemberName, ";
			$Sql .= " MemberEmail = HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
			$Sql .= " MemberView = :MemberView, ";
			$Sql .= " MemberState = :MemberState, ";
			$Sql .= " MemberModiDateTime = now() ";
		$Sql .= " where MemberID = :MemberID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberLanguageID', $MemberLanguageID);
		if ($MemberLoginNewPW!=""){
			$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
		}
		$Stmt->bindParam(':MemberTimeZoneID', $MemberTimeZoneID);
		$Stmt->bindParam(':MemberName', $CenterManagerName);
		$Stmt->bindParam(':MemberEmail', $CenterEmail);
		$Stmt->bindParam(':MemberView', $CenterView);
		$Stmt->bindParam(':MemberState', $CenterState);

		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->bindParam(':MemberID', $MemberID);
		if ($err_num==0){
			$Stmt->execute();
		}
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
	header("Location: center_list.php?$ListParam"); 
	exit;
}
?>


