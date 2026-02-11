<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);
$PageType = isset($_REQUEST["PageType"]) ? $_REQUEST["PageType"] : "";
$PageTabID = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";

$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberNumber = isset($_REQUEST["MemberNumber"]) ? $_REQUEST["MemberNumber"] : "";
$ForceUseClassIn = isset($_REQUEST["ForceUseClassIn"]) ? $_REQUEST["ForceUseClassIn"] : "";
$MemberCiTelephone = isset($_REQUEST["MemberCiTelephone"]) ? $_REQUEST["MemberCiTelephone"] : "";
$MemberLanguageID = isset($_REQUEST["MemberLanguageID"]) ? $_REQUEST["MemberLanguageID"] : "";

$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$MemberLoginPW = isset($_REQUEST["MemberLoginPW"]) ? $_REQUEST["MemberLoginPW"] : "";
$MemberLoginNewPW = isset($_REQUEST["MemberLoginNewPW"]) ? $_REQUEST["MemberLoginNewPW"] : "";
$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
$MemberNickName = isset($_REQUEST["MemberNickName"]) ? $_REQUEST["MemberNickName"] : "";
$MemberParentName = isset($_REQUEST["MemberParentName"]) ? $_REQUEST["MemberParentName"] : "";
$MemberSex = isset($_REQUEST["MemberSex"]) ? $_REQUEST["MemberSex"] : "";
$MemberCompanyName = isset($_REQUEST["MemberCompanyName"]) ? $_REQUEST["MemberCompanyName"] : "";
$MemberPhoto = isset($_REQUEST["MemberPhoto"]) ? $_REQUEST["MemberPhoto"] : "";
$MemberBirthday = isset($_REQUEST["MemberBirthday"]) ? $_REQUEST["MemberBirthday"] : "";

$MemberPhone1_1 = isset($_REQUEST["MemberPhone1_1"]) ? $_REQUEST["MemberPhone1_1"] : "";
$MemberPhone1_2 = isset($_REQUEST["MemberPhone1_2"]) ? $_REQUEST["MemberPhone1_2"] : "";
$MemberPhone1_3 = isset($_REQUEST["MemberPhone1_3"]) ? $_REQUEST["MemberPhone1_3"] : "";
$MemberPhone2_1 = isset($_REQUEST["MemberPhone2_1"]) ? $_REQUEST["MemberPhone2_1"] : "";
$MemberPhone2_2 = isset($_REQUEST["MemberPhone2_2"]) ? $_REQUEST["MemberPhone2_2"] : "";
$MemberPhone2_3 = isset($_REQUEST["MemberPhone2_3"]) ? $_REQUEST["MemberPhone2_3"] : "";
$MemberPhone3_1 = isset($_REQUEST["MemberPhone3_1"]) ? $_REQUEST["MemberPhone3_1"] : "";
$MemberPhone3_2 = isset($_REQUEST["MemberPhone3_2"]) ? $_REQUEST["MemberPhone3_2"] : "";
$MemberPhone3_3 = isset($_REQUEST["MemberPhone3_3"]) ? $_REQUEST["MemberPhone3_3"] : "";
$MemberEmail_1 = isset($_REQUEST["MemberEmail_1"]) ? $_REQUEST["MemberEmail_1"] : "";
$MemberEmail_2 = isset($_REQUEST["MemberEmail_2"]) ? $_REQUEST["MemberEmail_2"] : "";

$MemberEmail2_1 = isset($_REQUEST["MemberEmail2_1"]) ? $_REQUEST["MemberEmail2_1"] : "";
$MemberEmail2_2 = isset($_REQUEST["MemberEmail2_2"]) ? $_REQUEST["MemberEmail2_2"] : "";

$MemberZip = isset($_REQUEST["MemberZip"]) ? $_REQUEST["MemberZip"] : "";
$MemberAddr1 = isset($_REQUEST["MemberAddr1"]) ? $_REQUEST["MemberAddr1"] : "";
$MemberAddr2 = isset($_REQUEST["MemberAddr2"]) ? $_REQUEST["MemberAddr2"] : "";
$MemberTimeZoneID = isset($_REQUEST["MemberTimeZoneID"]) ? $_REQUEST["MemberTimeZoneID"] : "";
$SchoolName = isset($_REQUEST["SchoolName"]) ? $_REQUEST["SchoolName"] : "";
$SchoolGrade = isset($_REQUEST["SchoolGrade"]) ? $_REQUEST["SchoolGrade"] : "";
$MemberStudyAlarmTime = isset($_REQUEST["MemberStudyAlarmTime"]) ? (int)$_REQUEST["MemberStudyAlarmTime"] : 30;
$MemberStudyAlarmType = isset($_REQUEST["MemberStudyAlarmType"]) ? (int)$_REQUEST["MemberStudyAlarmType"] : 1;
$MemberChangeTeacher = isset($_REQUEST["MemberChangeTeacher"]) ? (int)$_REQUEST["MemberChangeTeacher"] : 1;

$MemberView = isset($_REQUEST["MemberView"]) ? $_REQUEST["MemberView"] : "";
$MemberState = isset($_REQUEST["MemberState"]) ? $_REQUEST["MemberState"] : "";
$MemberStateText = isset($_REQUEST["MemberStateText"]) ? $_REQUEST["MemberStateText"] : "";
$WithdrawalText = isset($_REQUEST["WithdrawalText"]) ? $_REQUEST["WithdrawalText"] : "";

$CenterPayType = isset($_REQUEST["CenterPayType"]) ? $_REQUEST["CenterPayType"] : "";
$MemberPayType = isset($_REQUEST["MemberPayType"]) ? $_REQUEST["MemberPayType"] : "";

$MemberPricePerTime = isset($_REQUEST["MemberPricePerTime"]) ? $_REQUEST["MemberPricePerTime"] : "";
if (!preg_match("/[0-9]/", $MemberPricePerTime)) { $MemberPricePerTime = 0; }


$MemberPhone1 = $MemberPhone1_1 . "-". $MemberPhone1_2 . "-" .$MemberPhone1_3;
$MemberPhone2 = $MemberPhone2_1 . "-". $MemberPhone2_2 . "-" .$MemberPhone2_3;
$MemberPhone3 = $MemberPhone3_1 . "-". $MemberPhone3_2 . "-" .$MemberPhone3_3;
$MemberEmail = $MemberEmail_1 . "@". $MemberEmail_2;
$MemberEmail2 = $MemberEmail2_1 . "@". $MemberEmail2_2;

if ($MemberView!="1"){
	$MemberView = 0;
}

if ($ForceUseClassIn!="1"){
	$ForceUseClassIn = 0;
}

if ($MemberNumber==""){
	$MemberNumber = "";//전화번호 뒷자리 4자
}

$MemberLevelID = 19;//학생

//if($MemberTimeZoneID=="") {
//	$MemberTimeZoneID = 1;
//}


$MemberLoginNewPW_hash = password_hash(sha1($MemberLoginNewPW), PASSWORD_DEFAULT);

if ($MemberID==""){

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
		$Sql = " insert into Members ( ";
			$Sql .= " CenterID, ";
			$Sql .= " MemberLanguageID, ";
			$Sql .= " MemberLevelID, ";
			$Sql .= " MemberNumber, ";
			$Sql .= " ForceUseClassIn, ";
			$Sql .= " MemberCiTelephone, ";
			$Sql .= " MemberLoginID, ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " MemberLoginPW, ";
			}
			$Sql .= " MemberPricePerTime, ";
			$Sql .= " MemberPayType, ";
			$Sql .= " MemberName, ";
			$Sql .= " MemberNickName, ";
			$Sql .= " MemberParentName, ";
			$Sql .= " MemberSex, ";
			$Sql .= " MemberCompanyName, ";
			$Sql .= " MemberPhoto, ";
			$Sql .= " MemberBirthday, ";
			$Sql .= " MemberPhone1, ";
			$Sql .= " MemberPhone2, ";
			$Sql .= " MemberPhone3, ";
			$Sql .= " MemberEmail, ";
			$Sql .= " MemberEmail2, ";
			$Sql .= " MemberZip, ";
			$Sql .= " MemberAddr1, ";
			$Sql .= " MemberAddr2, ";
			$Sql .= " MemberTimeZoneID, ";
			$Sql .= " SchoolName, ";
			$Sql .= " SchoolGrade, ";
			$Sql .= " MemberStudyAlarmTime, ";
			$Sql .= " MemberStudyAlarmType, ";
			$Sql .= " MemberChangeTeacher, ";
			$Sql .= " MemberView, ";
			$Sql .= " MemberState, ";
			$Sql .= " MemberStateText, ";
			$Sql .= " WithdrawalText, ";
			$Sql .= " MemberRegDateTime, ";
			$Sql .= " MemberModiDateTime ";


		$Sql .= " ) values ( ";

			$Sql .= " :CenterID, ";
			$Sql .= " :MemberLanguageID, ";
			$Sql .= " :MemberLevelID, ";
			$Sql .= " :MemberNumber, ";
			$Sql .= " :ForceUseClassIn, ";
			$Sql .= " :MemberCiTelephone, ";
			$Sql .= " :MemberLoginID, ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " :MemberLoginNewPW_hash, ";
			}
			$Sql .= " :MemberPricePerTime, ";
			$Sql .= " :MemberPayType, ";
			$Sql .= " :MemberName, ";
			$Sql .= " :MemberNickName, ";
			$Sql .= " :MemberParentName, ";
			$Sql .= " :MemberSex, ";
			$Sql .= " :MemberCompanyName, ";
			$Sql .= " :MemberPhoto, ";
			$Sql .= " :MemberBirthday, ";
			$Sql .= " HEX(AES_ENCRYPT(:MemberPhone1, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:MemberPhone2, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:MemberPhone3, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:MemberEmail2, :EncryptionKey)), ";
			$Sql .= " :MemberZip, ";
			$Sql .= " :MemberAddr1, ";
			$Sql .= " :MemberAddr2, ";
			$Sql .= " :MemberTimeZoneID, ";
			$Sql .= " :SchoolName, ";
			$Sql .= " :SchoolGrade, ";
			$Sql .= " :MemberStudyAlarmTime, ";
			$Sql .= " :MemberStudyAlarmType, ";
			$Sql .= " :MemberChangeTeacher, ";
			$Sql .= " :MemberView, ";
			$Sql .= " :MemberState, ";
			$Sql .= " :MemberStateText, ";
			$Sql .= " :WithdrawalText, ";
			$Sql .= " now(), ";
			$Sql .= " now() ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':CenterID', $CenterID);
		$Stmt->bindParam(':MemberLanguageID', $MemberLanguageID);
		$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
		$Stmt->bindParam(':MemberNumber', $MemberNumber);
		$Stmt->bindParam(':ForceUseClassIn', $ForceUseClassIn);
		$Stmt->bindParam(':MemberCiTelephone', $MemberCiTelephone);
		$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
		if ($MemberLoginNewPW!=""){
			$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
		}
		$Stmt->bindParam(':MemberPricePerTime', $MemberPricePerTime);
		$Stmt->bindParam(':MemberPayType', $MemberPayType);
		$Stmt->bindParam(':MemberName', $MemberName);
		$Stmt->bindParam(':MemberNickName', $MemberNickName);
		$Stmt->bindParam(':MemberParentName', $MemberParentName);
		$Stmt->bindParam(':MemberSex', $MemberSex);
		$Stmt->bindParam(':MemberCompanyName', $MemberCompanyName);
		$Stmt->bindParam(':MemberPhoto', $MemberPhoto);
		$Stmt->bindParam(':MemberBirthday', $MemberBirthday);
		$Stmt->bindParam(':MemberPhone1', $MemberPhone1);
		$Stmt->bindParam(':MemberPhone2', $MemberPhone2);
		$Stmt->bindParam(':MemberPhone3', $MemberPhone3);
		$Stmt->bindParam(':MemberEmail', $MemberEmail);
		$Stmt->bindParam(':MemberEmail2', $MemberEmail2);
		$Stmt->bindParam(':MemberZip', $MemberZip);
		$Stmt->bindParam(':MemberAddr1', $MemberAddr1);
		$Stmt->bindParam(':MemberAddr2', $MemberAddr2);
		$Stmt->bindParam(':MemberTimeZoneID', $MemberTimeZoneID);
		$Stmt->bindParam(':SchoolName', $SchoolName);
		$Stmt->bindParam(':SchoolGrade', $SchoolGrade);
		$Stmt->bindParam(':MemberStudyAlarmTime', $MemberStudyAlarmTime);
		$Stmt->bindParam(':MemberStudyAlarmType', $MemberStudyAlarmType);
		$Stmt->bindParam(':MemberChangeTeacher', $MemberChangeTeacher);
		$Stmt->bindParam(':MemberView', $MemberView);
		$Stmt->bindParam(':MemberState', $MemberState);
		$Stmt->bindParam(':MemberStateText', $MemberStateText);
		$Stmt->bindParam(':WithdrawalText', $WithdrawalText);

		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$MemberID = $DbConn->lastInsertId();
		$Stmt = null;

		InsertPoint(1, 0, $MemberID, "회원가입(LMS)", "회원가입(LMS)" ,$OnlineSiteMemberRegPoint);
		SendSmsWelcome($MemberID, $EncryptionKey);
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
		$Sql = " update Members set ";
			$Sql .= " CenterID = :CenterID, ";
			$Sql .= " MemberNumber = :MemberNumber, ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " MemberLoginPW = :MemberLoginNewPW_hash, ";
			}
			$Sql .= " MemberLanguageID = :MemberLanguageID, ";
			$Sql .= " ForceUseClassIn = :ForceUseClassIn, ";
			$Sql .= " MemberCiTelephone = :MemberCiTelephone, ";
			$Sql .= " MemberPricePerTime = :MemberPricePerTime, ";
			$Sql .= " MemberPayType = :MemberPayType, ";
			$Sql .= " MemberName = :MemberName, ";
			$Sql .= " MemberNickName = :MemberNickName, ";
			$Sql .= " MemberParentName = :MemberParentName, ";
			$Sql .= " MemberSex = :MemberSex, ";
			$Sql .= " MemberCompanyName = :MemberCompanyName, ";
			$Sql .= " MemberPhoto = :MemberPhoto, ";
			$Sql .= " MemberBirthday = :MemberBirthday, ";
			$Sql .= " MemberPhone1 = HEX(AES_ENCRYPT(:MemberPhone1, :EncryptionKey)), ";
			$Sql .= " MemberPhone2 = HEX(AES_ENCRYPT(:MemberPhone2, :EncryptionKey)), ";
			$Sql .= " MemberPhone3 = HEX(AES_ENCRYPT(:MemberPhone3, :EncryptionKey)), ";;
			$Sql .= " MemberEmail = HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
			$Sql .= " MemberEmail2 = HEX(AES_ENCRYPT(:MemberEmail2, :EncryptionKey)), ";
			$Sql .= " MemberZip = :MemberZip, ";
			$Sql .= " MemberAddr1 = :MemberAddr1, ";
			$Sql .= " MemberAddr2 = :MemberAddr2, ";
			$Sql .= " MemberTimeZoneID = :MemberTimeZoneID, ";
			$Sql .= " SchoolName = :SchoolName, ";
			$Sql .= " SchoolGrade = :SchoolGrade, ";
			$Sql .= " MemberStudyAlarmTime = :MemberStudyAlarmTime, ";
			$Sql .= " MemberStudyAlarmType = :MemberStudyAlarmType, ";
			$Sql .= " MemberChangeTeacher = :MemberChangeTeacher, ";
			$Sql .= " MemberView = :MemberView, ";
			$Sql .= " MemberState = :MemberState, ";
			$Sql .= " MemberStateText = :MemberStateText, ";
			$Sql .= " WithdrawalText = :WithdrawalText, ";
			if($MemberState==3) {
				$Sql .= " WithdrawalDateTime = now(), ";
			}
			$Sql .= " MemberModiDateTime = now() ";
		$Sql .= " where MemberID = :MemberID ";


		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':CenterID', $CenterID);
		$Stmt->bindParam(':MemberNumber', $MemberNumber);
		if ($MemberLoginNewPW!=""){
			$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
		}
		$Stmt->bindParam(':MemberLanguageID', $MemberLanguageID);
		$Stmt->bindParam(':ForceUseClassIn', $ForceUseClassIn);
		$Stmt->bindParam(':MemberCiTelephone', $MemberCiTelephone);
		$Stmt->bindParam(':MemberPricePerTime', $MemberPricePerTime);
		$Stmt->bindParam(':MemberPayType', $MemberPayType);
		$Stmt->bindParam(':MemberName', $MemberName);
		$Stmt->bindParam(':MemberNickName', $MemberNickName);
		$Stmt->bindParam(':MemberParentName', $MemberParentName);
		$Stmt->bindParam(':MemberSex', $MemberSex);
		$Stmt->bindParam(':MemberCompanyName', $MemberCompanyName);
		$Stmt->bindParam(':MemberPhoto', $MemberPhoto);
		$Stmt->bindParam(':MemberBirthday', $MemberBirthday);
		$Stmt->bindParam(':MemberPhone1', $MemberPhone1);
		$Stmt->bindParam(':MemberPhone2', $MemberPhone2);
		$Stmt->bindParam(':MemberPhone3', $MemberPhone3);
		$Stmt->bindParam(':MemberEmail', $MemberEmail);
		$Stmt->bindParam(':MemberEmail2', $MemberEmail2);
		$Stmt->bindParam(':MemberZip', $MemberZip);
		$Stmt->bindParam(':MemberAddr1', $MemberAddr1);
		$Stmt->bindParam(':MemberAddr2', $MemberAddr2);
		$Stmt->bindParam(':MemberTimeZoneID', $MemberTimeZoneID);
		$Stmt->bindParam(':SchoolName', $SchoolName);
		$Stmt->bindParam(':SchoolGrade', $SchoolGrade);
		$Stmt->bindParam(':MemberStudyAlarmTime', $MemberStudyAlarmTime);
		$Stmt->bindParam(':MemberStudyAlarmType', $MemberStudyAlarmType);
		$Stmt->bindParam(':MemberChangeTeacher', $MemberChangeTeacher);
		$Stmt->bindParam(':MemberView', $MemberView);
		$Stmt->bindParam(':MemberState', $MemberState);
		$Stmt->bindParam(':MemberStateText', $MemberStateText);
		$Stmt->bindParam(':WithdrawalText', $WithdrawalText);
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
//history.go(-1);
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
	if ($PageType=="POP"){
		header("Location: student_detail_form_pop.php?MemberID=$MemberID&PageTabID=$PageTabID"); 
		exit;
	}else{
		header("Location: student_detail_list.php?$ListParam"); 
		exit;
	}
}

?>


