<?php
 error_reporting( E_ALL );
  ini_set( "display_errors", 1 );

include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$FranchiseID = isset($_REQUEST["FranchiseID"]) ? $_REQUEST["FranchiseID"] : "";
$StaffID = isset($_REQUEST["StaffID"]) ? $_REQUEST["StaffID"] : "";
$StaffName = isset($_REQUEST["StaffName"]) ? $_REQUEST["StaffName"] : "";
$StaffNickName = isset($_REQUEST["StaffNickName"]) ? $_REQUEST["StaffNickName"] : "";
$StaffManageMent = isset($_REQUEST["StaffManageMent"]) ? $_REQUEST["StaffManageMent"] : "";

$StaffPhone1_1 = isset($_REQUEST["StaffPhone1_1"]) ? $_REQUEST["StaffPhone1_1"] : "";
$StaffPhone1_2 = isset($_REQUEST["StaffPhone1_2"]) ? $_REQUEST["StaffPhone1_2"] : "";
$StaffPhone1_3 = isset($_REQUEST["StaffPhone1_3"]) ? $_REQUEST["StaffPhone1_3"] : "";
$StaffPhone2_1 = isset($_REQUEST["StaffPhone2_1"]) ? $_REQUEST["StaffPhone2_1"] : "";
$StaffPhone2_2 = isset($_REQUEST["StaffPhone2_2"]) ? $_REQUEST["StaffPhone2_2"] : "";
$StaffPhone2_3 = isset($_REQUEST["StaffPhone2_3"]) ? $_REQUEST["StaffPhone2_3"] : "";
$StaffPhone3_1 = isset($_REQUEST["StaffPhone3_1"]) ? $_REQUEST["StaffPhone3_1"] : "";
$StaffPhone3_2 = isset($_REQUEST["StaffPhone3_2"]) ? $_REQUEST["StaffPhone3_2"] : "";
$StaffPhone3_3 = isset($_REQUEST["StaffPhone3_3"]) ? $_REQUEST["StaffPhone3_3"] : "";
$StaffEmail_1 = isset($_REQUEST["StaffEmail_1"]) ? $_REQUEST["StaffEmail_1"] : "";
$StaffEmail_2 = isset($_REQUEST["StaffEmail_2"]) ? $_REQUEST["StaffEmail_2"] : "";

$Jumin1 = isset($_REQUEST["Jumin1"]) ? $_REQUEST["Jumin1"] : "";
$Jumin2 = isset($_REQUEST["Jumin2"]) ? $_REQUEST["Jumin2"] : "";
$RetirementDate = isset($_REQUEST["RetirementDate"]) ? $_REQUEST["RetirementDate"] : null;
$WorkType = isset($_REQUEST["WorkType"]) ? $_REQUEST["WorkType"] : "";
if ($WorkType == 0 ){
	$EmploymentInsurance = 1;
	$IndustrialInsurance = 1;
	$HealthInsurance = 1;
	$NationalPension = 1;
} else {
	$EmploymentInsurance = 0;
	$IndustrialInsurance = 0;
	$HealthInsurance = 0;
	$NationalPension = 0;
}

$StaffZip = isset($_REQUEST["StaffZip"]) ? $_REQUEST["StaffZip"] : "";
$StaffAddr1 = isset($_REQUEST["StaffAddr1"]) ? $_REQUEST["StaffAddr1"] : "";
$StaffAddr2 = isset($_REQUEST["StaffAddr2"]) ? $_REQUEST["StaffAddr2"] : "";
$StaffLogoImage = isset($_REQUEST["StaffLogoImage"]) ? $_REQUEST["StaffLogoImage"] : "";
$StaffIntroText = isset($_REQUEST["StaffIntroText"]) ? $_REQUEST["StaffIntroText"] : "";
$StaffRegDateTime = isset($_REQUEST["StaffRegDateTime"]) ? $_REQUEST["StaffRegDateTime"] : "";
$StaffState = isset($_REQUEST["StaffState"]) ? $_REQUEST["StaffState"] : "";
$StaffView = isset($_REQUEST["StaffView"]) ? $_REQUEST["StaffView"] : "";

$StaffPhone1 = $StaffPhone1_1 . "-". $StaffPhone1_2 . "-" .$StaffPhone1_3;
$StaffPhone2 = $StaffPhone2_1 . "-". $StaffPhone2_2 . "-" .$StaffPhone2_3;
$StaffPhone3 = $StaffPhone3_1 . "-". $StaffPhone3_2 . "-" .$StaffPhone3_3;
$StaffEmail = $StaffEmail_1 . "@". $StaffEmail_2;

//Members 
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberDprtName = isset($_REQUEST["MemberDprtName"]) ? $_REQUEST["MemberDprtName"] : "";
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$MemberLoginPW = isset($_REQUEST["MemberLoginPW"]) ? $_REQUEST["MemberLoginPW"] : "";
$MemberLanguageID = isset($_REQUEST["MemberLanguageID"]) ? $_REQUEST["MemberLanguageID"] : "";
$MemberLoginNewPW = isset($_REQUEST["MemberLoginNewPW"]) ? $_REQUEST["MemberLoginNewPW"] : "";

//인사평가
$Hr_OrganLevelID = isset($_REQUEST["Hr_OrganLevelID"]) ? $_REQUEST["Hr_OrganLevelID"] : "";
$Hr_OrganTask2ID = isset($_REQUEST["Hr_OrganTask2ID"]) ? $_REQUEST["Hr_OrganTask2ID"] : "";
$Hr_OrganPositionName = isset($_REQUEST["Hr_OrganPositionName"]) ? $_REQUEST["Hr_OrganPositionName"] : "";
$Hr_OrganLevel = isset($_REQUEST["Hr_OrganLevel"]) ? $_REQUEST["Hr_OrganLevel"] : "";

$MemberLoginNewPW_hash = password_hash(sha1($MemberLoginNewPW), PASSWORD_DEFAULT);

if ($StaffView!="1"){
	$StaffView = 0;
}

if ($StaffState!="1" || $RetirementDate != null){
	$StaffState = 2;
}

 
if ($StaffID==""){

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
		$Sql = "select ifnull(Max(StaffOrder),0) as StaffOrder from Staffs";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$StaffOrder = $Row["StaffOrder"]+1;

		$Sql = " insert into Staffs ( ";
			$Sql .= " FranchiseID, ";
			$Sql .= " StaffManageMent, ";
			$Sql .= " StaffName, ";
			$Sql .= " StaffNickName, ";
			$Sql .= " StaffPhone1, ";
			$Sql .= " StaffPhone2, ";
			$Sql .= " StaffPhone3, ";
			$Sql .= " StaffEmail, ";
			$Sql .= " StaffZip, ";
			$Sql .= " StaffAddr1, ";
			$Sql .= " StaffAddr2, ";
			$Sql .= " StaffLogoImage, ";
			$Sql .= " StaffIntroText, ";
			$Sql .= " StaffRegDateTime, ";
			$Sql .= " StaffModiDateTime, ";
			$Sql .= " StaffState, ";
			$Sql .= " StaffView, ";
			$Sql .= " Jumin1, ";
			$Sql .= " Jumin2, ";
			$Sql .= " StaffOrder ";
		$Sql .= " ) values ( ";
			$Sql .= " :FranchiseID, ";
			$Sql .= " :StaffManageMent, ";
			$Sql .= " :StaffName, ";
			$Sql .= " :StaffNickName, ";
			$Sql .= " HEX(AES_ENCRYPT(:StaffPhone1, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:StaffPhone2, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:StaffPhone3, :EncryptionKey)), ";;
			$Sql .= " HEX(AES_ENCRYPT(:StaffEmail, :EncryptionKey)), ";
			$Sql .= " :StaffZip, ";
			$Sql .= " :StaffAddr1, ";
			$Sql .= " :StaffAddr2, ";
			$Sql .= " :StaffLogoImage, ";
			$Sql .= " :StaffIntroText, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " :StaffState, ";
			$Sql .= " :StaffView, ";
			$Sql .= " :Jumin1, ";
			$Sql .= " :Jumin2, ";
			$Sql .= " :StaffOrder ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':FranchiseID', $FranchiseID);
		$Stmt->bindParam(':StaffManageMent', $StaffManageMent);
		$Stmt->bindParam(':StaffName', $StaffName);
		$Stmt->bindParam(':StaffNickName', $StaffNickName);
		$Stmt->bindParam(':StaffPhone1', $StaffPhone1);
		$Stmt->bindParam(':StaffPhone2', $StaffPhone2);
		$Stmt->bindParam(':StaffPhone3', $StaffPhone3);
		$Stmt->bindParam(':StaffEmail', $StaffEmail);
		$Stmt->bindParam(':StaffZip', $StaffZip);
		$Stmt->bindParam(':StaffAddr1', $StaffAddr1);
		$Stmt->bindParam(':StaffAddr2', $StaffAddr2);
		$Stmt->bindParam(':StaffLogoImage', $StaffLogoImage);
		$Stmt->bindParam(':StaffIntroText', $StaffIntroText);
		$Stmt->bindParam(':StaffState', $StaffState);
		$Stmt->bindParam(':StaffView', $StaffView);
		$Stmt->bindParam(':StaffOrder', $StaffOrder);
		$Stmt->bindParam(':Jumin1', $Jumin1);
		$Stmt->bindParam(':Jumin2', $Jumin2);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$StaffID = $DbConn->lastInsertId();
		$Stmt = null;


		//Members 
		$MemberLevelID = 4;//직원

		$Sql = " insert into Members ( ";
			$Sql .= " StaffID, ";
			$Sql .= " MemberDprtName, ";
			$Sql .= " MemberLevelID, ";
			$Sql .= " MemberLoginID, ";
			$Sql .= " MemberLanguageID, ";
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

			$Sql .= " :StaffID, ";
			$Sql .= " :MemberDprtName, ";
			$Sql .= " :MemberLevelID, ";
			$Sql .= " :MemberLoginID, ";
			$Sql .= " :MemberLanguageID, ";
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
		$Stmt->bindParam(':StaffID', $StaffID);
		$Stmt->bindParam(':MemberDprtName', $MemberDprtName);
		$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
		$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
		$Stmt->bindParam(':MemberLanguageID', $MemberLanguageID);
		if ($MemberLoginNewPW!=""){
			$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
		}
		$Stmt->bindParam(':MemberName', $StaffName);
		$Stmt->bindParam(':MemberEmail', $StaffEmail);
		$Stmt->bindParam(':MemberView', $StaffView);
		$Stmt->bindParam(':MemberState', $StaffState);

		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$MemberID = $DbConn->lastInsertId();
		$Stmt = null;

		//PayInfo 에 신규 입력
		

		$Sql = "INSERT INTO PayInfo (MemberID, WorkType, EmploymentInsurance, IndustrialInsurance, HealthInsurance, NationalPension, regDate) 
					VALUES ('$MemberID', $WorkType, $EmploymentInsurance, $IndustrialInsurance, $HealthInsurance, $NationalPension, now());";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();

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
		$Sql = " update Staffs set ";
			$Sql .= " FranchiseID = :FranchiseID, ";
			$Sql .= " StaffManageMent = :StaffManageMent, ";
			$Sql .= " StaffName = :StaffName, ";
			$Sql .= " StaffNickName = :StaffNickName, ";
			$Sql .= " StaffPhone1 = HEX(AES_ENCRYPT(:StaffPhone1, :EncryptionKey)), ";
			$Sql .= " StaffPhone2 = HEX(AES_ENCRYPT(:StaffPhone2, :EncryptionKey)), ";
			$Sql .= " StaffPhone3 = HEX(AES_ENCRYPT(:StaffPhone3, :EncryptionKey)), ";
			$Sql .= " StaffEmail = HEX(AES_ENCRYPT(:StaffEmail, :EncryptionKey)), ";
			$Sql .= " StaffZip = :StaffZip, ";
			$Sql .= " StaffAddr1 = :StaffAddr1, ";
			$Sql .= " StaffAddr2 = :StaffAddr2, ";
			$Sql .= " StaffLogoImage = :StaffLogoImage, ";
			$Sql .= " StaffIntroText = :StaffIntroText, ";
			$Sql .= " StaffState = :StaffState, ";
			$Sql .= " StaffView = :StaffView, ";
			$Sql .= " Jumin1 = :Jumin1, ";
			$Sql .= " Jumin2 = :Jumin2, ";
			$Sql .= " StaffModiDateTime = now() ";
            if ($RetirementDate != null){
			    $Sql .= " , RetirementDate =  :RetirementDate ";
            }    
		$Sql .= " where StaffID = :StaffID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':FranchiseID', $FranchiseID);
		$Stmt->bindParam(':StaffManageMent', $StaffManageMent);
		$Stmt->bindParam(':StaffName', $StaffName);
		$Stmt->bindParam(':StaffNickName', $StaffNickName);
		$Stmt->bindParam(':StaffPhone1', $StaffPhone1);
		$Stmt->bindParam(':StaffPhone2', $StaffPhone2);
		$Stmt->bindParam(':StaffPhone3', $StaffPhone3);
		$Stmt->bindParam(':StaffEmail', $StaffEmail);
		$Stmt->bindParam(':StaffZip', $StaffZip);
		$Stmt->bindParam(':StaffAddr1', $StaffAddr1);
		$Stmt->bindParam(':StaffAddr2', $StaffAddr2);
		$Stmt->bindParam(':StaffLogoImage', $StaffLogoImage);
		$Stmt->bindParam(':StaffIntroText', $StaffIntroText);
		$Stmt->bindParam(':StaffState', $StaffState);
		$Stmt->bindParam(':StaffView', $StaffView);
		$Stmt->bindParam(':Jumin1', $Jumin1);
		$Stmt->bindParam(':Jumin2', $Jumin2);
        if ($RetirementDate != null){
		    $Stmt->bindParam(':RetirementDate', $RetirementDate);
        }    
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->bindParam(':StaffID', $StaffID);
		$Stmt->execute();
		$Stmt = null;

		//Members 
		$Sql = " update Members set ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " MemberLoginPW = :MemberLoginNewPW_hash, ";
			}
			$Sql .= " MemberDprtName = :MemberDprtName, ";
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
		$Stmt->bindParam(':MemberDprtName', $MemberDprtName);
		$Stmt->bindParam(':MemberLanguageID', $MemberLanguageID);
		$Stmt->bindParam(':MemberName', $StaffName);
		$Stmt->bindParam(':MemberEmail', $StaffEmail);
		$Stmt->bindParam(':MemberView', $StaffView);
		$Stmt->bindParam(':MemberState', $StaffState);

		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->execute();
		$Stmt = null;

		//PayInfo 
		$Sql = "update PayInfo set ";
			$Sql .= " EmploymentInsurance = :EmploymentInsurance, ";
			$Sql .= " IndustrialInsurance = :IndustrialInsurance, ";
			$Sql .= " HealthInsurance = :HealthInsurance, ";
			$Sql .= " NationalPension = :NationalPension, ";
			$Sql .= " WorkType = :WorkType ";
		$Sql .= " where MemberID = :MemberID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':EmploymentInsurance', $EmploymentInsurance);
		$Stmt->bindParam(':IndustrialInsurance', $IndustrialInsurance);
		$Stmt->bindParam(':HealthInsurance', $HealthInsurance);
		$Stmt->bindParam(':NationalPension', $NationalPension);
		$Stmt->bindParam(':WorkType', $WorkType);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->execute();
		$Stmt = null;


	}
}


if ($err_num==0 && $_LINK_ADMIN_LEVEL_ID_==0){

	$Sql2 = "select 
				A.*
			 from Hr_OrganLevelTaskMembers A 
			 where A.MemberID=:MemberID";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':MemberID', $MemberID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();

	$Hr_MemberID = $Row2["MemberID"];

	if (!$Hr_MemberID){

		//인사평가
		$Sql = " insert into Hr_OrganLevelTaskMembers ( ";
			$Sql .= " MemberID, ";
			$Sql .= " Hr_OrganLevel, ";
			$Sql .= " Hr_OrganLevelID, ";
			$Sql .= " Hr_OrganTask2ID, ";
			$Sql .= " Hr_OrganPositionName, ";
			$Sql .= " Hr_OrganLevelTaskMemberRegDateTime, ";
			$Sql .= " Hr_OrganLevelTaskMemberModiDateTime ";

		$Sql .= " ) values ( ";
			$Sql .= " :MemberID, ";
			$Sql .= " :Hr_OrganLevel, ";
			$Sql .= " :Hr_OrganLevelID, ";
			$Sql .= " :Hr_OrganTask2ID, ";
			$Sql .= " :Hr_OrganPositionName, ";
			$Sql .= " now(), ";
			$Sql .= " now() ";

		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->bindParam(':Hr_OrganLevel', $Hr_OrganLevel);
		$Stmt->bindParam(':Hr_OrganLevelID', $Hr_OrganLevelID);
		$Stmt->bindParam(':Hr_OrganTask2ID', $Hr_OrganTask2ID);
		$Stmt->bindParam(':Hr_OrganPositionName', $Hr_OrganPositionName);
		$Stmt->execute();
		$MemberID = $DbConn->lastInsertId();
		$Stmt = null;

	}else{

		//인사평가
		$Sql = " update Hr_OrganLevelTaskMembers set ";
			$Sql .= " Hr_OrganLevel = :Hr_OrganLevel, ";
			$Sql .= " Hr_OrganLevelID = :Hr_OrganLevelID, ";
			$Sql .= " Hr_OrganTask2ID = :Hr_OrganTask2ID, ";
			$Sql .= " Hr_OrganPositionName = :Hr_OrganPositionName, ";
			$Sql .= " Hr_OrganLevelTaskMemberModiDateTime = now() ";
		$Sql .= " where MemberID = :MemberID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':Hr_OrganLevel', $Hr_OrganLevel);
		$Stmt->bindParam(':Hr_OrganLevelID', $Hr_OrganLevelID);
		$Stmt->bindParam(':Hr_OrganTask2ID', $Hr_OrganTask2ID);
		$Stmt->bindParam(':Hr_OrganPositionName', $Hr_OrganPositionName);
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
	header("Location: staff_list.php?$ListParam"); 
	exit;
}
?>
