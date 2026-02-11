<?php

// error_reporting( E_ALL );
// ini_set( "display_errors", 1 );

include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/password_hash.php');


$err_num = 0;
$err_msg = "";


$SelectedCampusID = isset($_REQUEST["SelectedCampusID"]) ? $_REQUEST["SelectedCampusID"] : "";
$CampusID = isset($_REQUEST["CampusID"]) ? $_REQUEST["CampusID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberLevelID = isset($_REQUEST["MemberLevelID"]) ? $_REQUEST["MemberLevelID"] : "";
$MemberType = isset($_REQUEST["MemberType"]) ? $_REQUEST["MemberType"] : "";
/* SNS 계정은 ID, PW, 이름 가 필요없음 
$MemberLoginPW = isset($_REQUEST["MemberLoginPW"]) ? $_REQUEST["MemberLoginPW"] : "";
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$MemberLoginNewPW = isset($_REQUEST["MemberLoginNewPW"]) ? $_REQUEST["MemberLoginNewPW"] : "";
$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
*/
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
$MemberPhone3_3 = isset($_REQUEST["MemberPhone2_3"]) ? $_REQUEST["MemberPhone3_3"] : "";

/*
$MemberEmail_1 = isset($_REQUEST["MemberEmail_1"]) ? $_REQUEST["MemberEmail_1"] : "";
$MemberEmail_2 = isset($_REQUEST["MemberEmail_2"]) ? $_REQUEST["MemberEmail_2"] : "";
$MemberEmailAgree = isset($_REQUEST["MemberEmailAgree"]) ? $_REQUEST["MemberEmailAgree"] : "0";
*/

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

$MemberEmail2 = $MemberEmail2_1 . "@" . $MemberEmail2_2;

if (!preg_match("/[0-9]/", $MemberChangeTeacher)) { $MemberChangeTeacher = 1; }
if (!preg_match("/[0-9]/", $MemberStudyAlarmType)) { $MemberStudyAlarmType = 1; }
if (!preg_match("/[0-9]/", $CampusID)) { $CampusID = 0; }
if (!preg_match("/[0-9]/", $SchoolGrade)) { $SchoolGrade = 0; }

$Sql = " update Members set ";
	$Sql .= " MemberLoginInit = 1, ";
	$Sql .= " MemberNickName = :MemberNickName, ";
	$Sql .= " MemberParentName = :MemberParentName, ";
	$Sql .= " MemberNumber = :MemberNumber, ";
	$Sql .= " MemberPhone1 = HEX(AES_ENCRYPT(:MemberPhone1, :EncryptionKey)), ";
	$Sql .= " MemberPhone1Agree = :MemberPhone1Agree, ";
	$Sql .= " MemberPhone2 = HEX(AES_ENCRYPT(:MemberPhone2, :EncryptionKey)), ";
	$Sql .= " MemberPhone2Agree = :MemberPhone2Agree, ";
	$Sql .= " MemberPhone3 = HEX(AES_ENCRYPT(:MemberPhone3, :EncryptionKey)), ";
	$Sql .= " MemberEmail2 = HEX(AES_ENCRYPT(:MemberEmail2, :EncryptionKey)), ";
	$Sql .= " MemberEmail2Agree = :MemberEmail2Agree, ";
	$Sql .= " MemberBirthday = :MemberBirthday, ";
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
$Stmt->bindParam(':MemberNickName', $MemberNickName);
$Stmt->bindParam(':MemberParentName', $MemberParentName);
$Stmt->bindParam(':MemberNumber', $MemberNumber);
$Stmt->bindParam(':MemberPhone1', $MemberPhone1);
$Stmt->bindParam(':MemberPhone1Agree', $MemberPhone1Agree);
$Stmt->bindParam(':MemberPhone2', $MemberPhone2);
$Stmt->bindParam(':MemberPhone2Agree', $MemberPhone2Agree);
$Stmt->bindParam(':MemberPhone3', $MemberPhone3);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
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

SendSmsWelcome($MemberID, $EncryptionKey);

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

include_once('./includes/dbclose.php');


if ($err_num == 0){
	if ($NewData==1){
		header("Location: /"); 
		exit;
	}else{
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
alert("회원정보가 수정되었습니다..");
location.href = "index.php";
</script>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>
<?
	}

}


?>





