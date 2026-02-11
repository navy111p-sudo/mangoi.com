<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$err_num = 0;
$err_msg = "";



$SelectedCampusID = isset($_REQUEST["SelectedCampusID"]) ? $_REQUEST["SelectedCampusID"] : "";
$CampusID = isset($_REQUEST["CampusID"]) ? $_REQUEST["CampusID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberInviteID = isset($_REQUEST["MemberInviteID"]) ? $_REQUEST["MemberInviteID"] : "";
$MemberLevelID = isset($_REQUEST["MemberLevelID"]) ? $_REQUEST["MemberLevelID"] : "";
$MemberType = isset($_REQUEST["MemberType"]) ? $_REQUEST["MemberType"] : "";
$MemberLoginPW = isset($_REQUEST["MemberLoginPW"]) ? $_REQUEST["MemberLoginPW"] : "";
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$MemberLoginNewPW = isset($_REQUEST["MemberLoginNewPW"]) ? $_REQUEST["MemberLoginNewPW"] : "";
$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
$MemberNickName = isset($_REQUEST["MemberNickName"]) ? $_REQUEST["MemberNickName"] : "";
$MemberSex = isset($_REQUEST["MemberSex"]) ? $_REQUEST["MemberSex"] : "";

$MemberPhone1_1 = isset($_REQUEST["MemberPhone1_1"]) ? $_REQUEST["MemberPhone1_1"] : "";
$MemberPhone1_2 = isset($_REQUEST["MemberPhone1_2"]) ? $_REQUEST["MemberPhone1_2"] : "";
$MemberPhone1_3 = isset($_REQUEST["MemberPhone1_3"]) ? $_REQUEST["MemberPhone1_3"] : "";
$MemberPhone1Agree = isset($_REQUEST["MemberPhone1Agree"]) ? $_REQUEST["MemberPhone1Agree"] : "0";

$MemberPhone2_1 = isset($_REQUEST["MemberPhone2_1"]) ? $_REQUEST["MemberPhone2_1"] : "";
$MemberPhone2_2 = isset($_REQUEST["MemberPhone2_2"]) ? $_REQUEST["MemberPhone2_2"] : "";
$MemberPhone2_3 = isset($_REQUEST["MemberPhone2_3"]) ? $_REQUEST["MemberPhone2_3"] : "";
$MemberPhone2Agree = isset($_REQUEST["MemberPhone2Agree"]) ? $_REQUEST["MemberPhone2Agree"] : "0";

$MemberPhone3_1 = isset($_REQUEST["MemberPhone3_1"]) ? $_REQUEST["MemberPhone3_1"] : "";
$MemberPhone3_2 = isset($_REQUEST["MemberPhone3_2"]) ? $_REQUEST["MemberPhone3_2"] : "";
$MemberPhone3_3 = isset($_REQUEST["MemberPhone3_3"]) ? $_REQUEST["MemberPhone3_3"] : "";

$MemberEmail_1 = isset($_REQUEST["MemberEmail_1"]) ? $_REQUEST["MemberEmail_1"] : "";
$MemberEmail_2 = isset($_REQUEST["MemberEmail_2"]) ? $_REQUEST["MemberEmail_2"] : "";
$MemberEmailAgree = isset($_REQUEST["MemberEmailAgree"]) ? $_REQUEST["MemberEmailAgree"] : "0";

$MemberEmail2_1 = isset($_REQUEST["MemberEmail2_1"]) ? $_REQUEST["MemberEmail2_1"] : "";
$MemberEmail2_2 = isset($_REQUEST["MemberEmail2_2"]) ? $_REQUEST["MemberEmail2_2"] : "";
$MemberEmail2Agree = isset($_REQUEST["MemberEmail2Agree"]) ? $_REQUEST["MemberEmail2Agree"] : "0";

$ReceiveEmail = isset($_REQUEST["ReceiveEmail"]) ? $_REQUEST["ReceiveEmail"] : "";
$MemberBirthday = isset($_REQUEST["MemberBirthday"]) ? $_REQUEST["MemberBirthday"] : "";
$SchoolName = isset($_REQUEST["SchoolName"]) ? $_REQUEST["SchoolName"] : "";
$SchoolGrade = isset($_REQUEST["SchoolGrade"]) ? $_REQUEST["SchoolGrade"] : "";
$MemberZip = isset($_REQUEST["MemberZip"]) ? $_REQUEST["MemberZip"] : "";
$MemberAddr1 = isset($_REQUEST["MemberAddr1"]) ? $_REQUEST["MemberAddr1"] : "";
$MemberAddr2 = isset($_REQUEST["MemberAddr2"]) ? $_REQUEST["MemberAddr2"] : "";
$MemberPhoto = isset($_REQUEST["MemberPhoto"]) ? $_REQUEST["MemberPhoto"] : "";
$MemberState = isset($_REQUEST["MemberState"]) ? $_REQUEST["MemberState"] : "";
$MemberStateText = isset($_REQUEST["MemberStateText"]) ? $_REQUEST["MemberStateText"] : "";
$MemberStudyAlarmTime = isset($_REQUEST["MemberStudyAlarmTime"]) ? $_REQUEST["MemberStudyAlarmTime"] : "";
$MemberStudyAlarmType = isset($_REQUEST["MemberStudyAlarmType"]) ? $_REQUEST["MemberStudyAlarmType"] : "";
$MemberChangeTeacher = isset($_REQUEST["MemberChangeTeacher"]) ? $_REQUEST["MemberChangeTeacher"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$MemberParentName = isset($_REQUEST["MemberParentName"]) ? $_REQUEST["MemberParentName"] : "";


//  부모 휴대폰 및 이메일 수신동의

$MemberPhone1 = $MemberPhone1_1 . "-" . $MemberPhone1_2 . "-" . $MemberPhone1_3; 
$MemberPhone2 = $MemberPhone2_1 . "-" . $MemberPhone2_2 . "-" . $MemberPhone2_3; 
$MemberPhone3 = $MemberPhone3_1 . "-" . $MemberPhone3_2 . "-" . $MemberPhone3_3; 
$MemberNumber = $MemberPhone1_3;

$MemberEmail = $MemberEmail_1 . "@" . $MemberEmail_2;
$MemberEmail2 = $MemberEmail2_1 . "@" . $MemberEmail2_2;
//echo $MemberEmail2;

$MemberLoginNewPW_hash = password_hash(sha1($MemberLoginNewPW), PASSWORD_DEFAULT);

if (!preg_match("/[0-9]/", $CampusID)) { $CampusID = 0; }
if (!preg_match("/[0-9]/", $SchoolGrade)) { $SchoolGrade = 0; }

if ($MemberID==""){

	$Sql = " insert into Members ( ";
		//$Sql .= " CampusID, ";
		$Sql .= " CenterID, ";
		$Sql .= " MemberInviteID, ";
		$Sql .= " MemberLevelID, ";
		$Sql .= " MemberLoginID, ";
		$Sql .= " MemberName, ";
		$Sql .= " MemberNickName, ";
		$Sql .= " MemberParentName, ";
		$Sql .= " MemberNumber, ";
		if ($MemberLoginNewPW!=""){
			$Sql .= " MemberLoginPW, ";
		}
		$Sql .= " MemberPhone1, ";
		$Sql .= " MemberPhone1Agree, ";
		$Sql .= " MemberPhone2, ";
		$Sql .= " MemberPhone2Agree, ";
		$Sql .= " MemberPhone3, ";
		$Sql .= " MemberEmail, ";
		$Sql .= " MemberEmailAgree, ";
		$Sql .= " MemberEmail2, ";
		$Sql .= " MemberEmail2Agree, ";
		$Sql .= " MemberBirthday, ";
		$Sql .= " SchoolName, ";
		$Sql .= " SchoolGrade, ";
		$Sql .= " MemberStudyAlarmTime, ";
		$Sql .= " MemberStudyAlarmType, ";
		$Sql .= " MemberChangeTeacher, ";
		$Sql .= " MemberZip, ";
		$Sql .= " MemberAddr1, ";
		$Sql .= " MemberAddr2, ";
		$Sql .= " MemberSex, ";
		$Sql .= " MemberPhoto, ";
		$Sql .= " MemberStateText, ";
		$Sql .= " MemberCompanyName, ";
		$Sql .= " MemberRegDateTime, ";
		$Sql .= " MemberModiDateTime, ";
		$Sql .= " MemberState ";
	$Sql .= " ) values ( ";
		//$Sql .= " :SelectedCampusID, ";
		$Sql .= " :CenterID, ";
		$Sql .= " :MemberInviteID, ";
		$Sql .= " :MemberLevelID, ";
		$Sql .= " :MemberLoginID, ";
		$Sql .= " :MemberName, ";
		$Sql .= " :MemberNickName, ";
		$Sql .= " :MemberParentName, ";
		$Sql .= " :MemberNumber, ";
		if ($MemberLoginNewPW!=""){
			$Sql .= " :MemberLoginNewPW_hash, ";
		}
		$Sql .= " HEX(AES_ENCRYPT(:MemberPhone1, :EncryptionKey)), ";
		$Sql .= " :MemberPhone1Agree, ";
		$Sql .= " HEX(AES_ENCRYPT(:MemberPhone2, :EncryptionKey)), ";
		$Sql .= " :MemberPhone2Agree, ";
		$Sql .= " HEX(AES_ENCRYPT(:MemberPhone3, :EncryptionKey)), ";
		$Sql .= " HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
		$Sql .= " :MemberEmailAgree, ";
		$Sql .= " HEX(AES_ENCRYPT(:MemberEmail2, :EncryptionKey)), ";
		$Sql .= " :MemberEmail2Agree, ";
		$Sql .= " :MemberBirthday, ";
		$Sql .= " :SchoolName, ";
		$Sql .= " :SchoolGrade, ";
		$Sql .= " :MemberStudyAlarmTime, ";
		$Sql .= " :MemberStudyAlarmType, ";
		$Sql .= " :MemberChangeTeacher, ";
		$Sql .= " :MemberZip, ";
		$Sql .= " :MemberAddr1, ";
		$Sql .= " :MemberAddr2, ";
		$Sql .= " :MemberSex, ";
		$Sql .= " :MemberPhoto, ";
		$Sql .= " :MemberStateText, ";
		$Sql .= " :MemberCompanyName, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " 1 ";
	$Sql .= " )";

	$Stmt = $DbConn->prepare($Sql);
	//$Stmt->bindParam(':SelectedCampusID', $SelectedCampusID);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':MemberInviteID', $MemberInviteID);
	$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
	$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
	$Stmt->bindParam(':MemberName', $MemberName);
	$Stmt->bindParam(':MemberNickName', $MemberNickName);
	$Stmt->bindParam(':MemberParentName', $MemberParentName);
	$Stmt->bindParam(':MemberNumber', $MemberNumber);
	if ($MemberLoginNewPW!=""){
		$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
	}
	$Stmt->bindParam(':MemberPhone1', $MemberPhone1);
	$Stmt->bindParam(':MemberPhone1Agree', $MemberPhone1Agree);
	$Stmt->bindParam(':MemberPhone2', $MemberPhone1);
	$Stmt->bindParam(':MemberPhone2Agree', $MemberPhone2Agree);
	$Stmt->bindParam(':MemberPhone3', $MemberPhone3);
	$Stmt->bindParam(':MemberEmail', $MemberEmail);
	$Stmt->bindParam(':MemberEmailAgree', $MemberEmailAgree);
	$Stmt->bindParam(':MemberEmail2', $MemberEmail2);
	$Stmt->bindParam(':MemberEmail2Agree', $MemberEmail2Agree);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->bindParam(':MemberBirthday', $MemberBirthday);
	$Stmt->bindParam(':SchoolName', $SchoolName);
	$Stmt->bindParam(':SchoolGrade', $SchoolGrade);
	$Stmt->bindParam(':MemberStudyAlarmTime', $MemberStudyAlarmTime);
	$Stmt->bindParam(':MemberStudyAlarmType', $MemberStudyAlarmType);
	$Stmt->bindParam(':MemberChangeTeacher', $MemberChangeTeacher);
	$Stmt->bindParam(':MemberZip', $MemberZip);
	$Stmt->bindParam(':MemberAddr1', $MemberAddr1);
	$Stmt->bindParam(':MemberAddr2', $MemberAddr2);
	$Stmt->bindParam(':MemberSex', $MemberSex);
	$Stmt->bindParam(':MemberPhoto', $MemberPhoto);
	$Stmt->bindParam(':MemberStateText', $MemberStateText);
	$Stmt->bindParam(':MemberCompanyName', $MemberCompanyName);
	$Stmt->execute();
	$MemberID = $DbConn->lastInsertId();
	$Stmt = null;

	$NewData = 1;
	
	$AlertMsg = "등록 하였습니다.";

	InsertPoint(1, 0, $MemberID, "회원가입(앱)", "회원가입(앱)" ,$OnlineSiteMemberRegPoint);
	SendSmsWelcome($MemberID, $EncryptionKey);

}else{

	$Sql = " update Members set ";
		$Sql .= " CenterID =:CenterID, ";
		$Sql .= " MemberInviteID =:MemberInviteID, ";
		$Sql .= " MemberLevelID = :MemberLevelID, ";
		$Sql .= " MemberName = :MemberName, ";
		$Sql .= " MemberNickName = :MemberNickName, ";
		$Sql .= " MemberParentName = :MemberParentName, ";
		$Sql .= " MemberNumber = :MemberNumber, ";
		if ($MemberLoginNewPW!=""){
			$Sql .= " MemberLoginPW = :MemberLoginNewPW_hash, ";
		}
		$Sql .= " MemberBirthday = :MemberBirthday, ";
		$Sql .= " MemberPhone1 = HEX(AES_ENCRYPT(:MemberPhone1, :EncryptionKey)), ";
		$Sql .= " MemberPhone1Agree = :MemberPhone1Agree, ";
		$Sql .= " MemberPhone2 = HEX(AES_ENCRYPT(:MemberPhone2, :EncryptionKey)), ";
		$Sql .= " MemberPhone2Agree = :MemberPhone2Agree, ";
		$Sql .= " MemberPhone3 = HEX(AES_ENCRYPT(:MemberPhone3, :EncryptionKey)), ";
		$Sql .= " MemberEmail = HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
		$Sql .= " MemberEmailAgree = :MemberEmailAgree, ";
		$Sql .= " MemberEmail2 = HEX(AES_ENCRYPT(:MemberEmail2, :EncryptionKey)), ";
		$Sql .= " MemberEmail2Agree = :MemberEmail2Agree, ";
		$Sql .= " SchoolName = :SchoolName, ";
		$Sql .= " SchoolGrade = :SchoolGrade, ";
		$Sql .= " MemberStudyAlarmTime = :MemberStudyAlarmTime, ";
		$Sql .= " MemberStudyAlarmType = :MemberStudyAlarmType, ";
		$Sql .= " MemberChangeTeacher = :MemberChangeTeacher, ";
		$Sql .= " MemberZip = :MemberZip, ";
		$Sql .= " MemberAddr1 = :MemberAddr1, ";
		$Sql .= " MemberAddr2 = :MemberAddr2, ";
		$Sql .= " MemberSex = :MemberSex, ";
		$Sql .= " MemberStateText = :MemberStateText, ";
		$Sql .= " MemberCompanyName = :MemberCompanyName, ";
		$Sql .= " MemberModiDateTime = now() ";
	$Sql .= " where MemberID = :MemberID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':MemberInviteID', $MemberInviteID);
	$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
	$Stmt->bindParam(':MemberName', $MemberName);
	$Stmt->bindParam(':MemberNickName', $MemberNickName);
	$Stmt->bindParam(':MemberParentName', $MemberParentName);
	$Stmt->bindParam(':MemberNumber', $MemberNumber);
	if ($MemberLoginNewPW!=""){
		$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
	}
	$Stmt->bindParam(':MemberPhone1', $MemberPhone1);
	$Stmt->bindParam(':MemberPhone1Agree', $MemberPhone1Agree);
	$Stmt->bindParam(':MemberPhone2', $MemberPhone2);
	$Stmt->bindParam(':MemberPhone2Agree', $MemberPhone2Agree);
	$Stmt->bindParam(':MemberPhone3', $MemberPhone3);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->bindParam(':MemberEmail', $MemberEmail);
	$Stmt->bindParam(':MemberEmailAgree', $MemberEmailAgree);
	$Stmt->bindParam(':MemberEmail2', $MemberEmail2);
	$Stmt->bindParam(':MemberEmail2Agree', $MemberEmail2Agree);
	$Stmt->bindParam(':MemberBirthday', $MemberBirthday);
	$Stmt->bindParam(':SchoolName', $SchoolName);
	$Stmt->bindParam(':SchoolGrade', $SchoolGrade);
	$Stmt->bindParam(':MemberStudyAlarmTime', $MemberStudyAlarmTime);
	$Stmt->bindParam(':MemberStudyAlarmType', $MemberStudyAlarmType);
	$Stmt->bindParam(':MemberChangeTeacher', $MemberChangeTeacher);
	$Stmt->bindParam(':MemberZip', $MemberZip);
	$Stmt->bindParam(':MemberAddr1', $MemberAddr1);
	$Stmt->bindParam(':MemberAddr2', $MemberAddr2);
	$Stmt->bindParam(':MemberSex', $MemberSex);
	$Stmt->bindParam(':MemberPhoto', $MemberPhoto);
	$Stmt->bindParam(':MemberStateText', $MemberStateText);
	$Stmt->bindParam(':MemberCompanyName', $MemberCompanyName);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->execute();
	$Stmt = null;

	$NewData = 0;

	$AlertMsg = "회원정보가 수정되었습니다.";
}



if ($err_num != 0){
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
</body>
</html>
<?php
}

include_once('../includes/dbclose.php');

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>
<script>
window.Exit=true;
</script>
</body>
</html>
