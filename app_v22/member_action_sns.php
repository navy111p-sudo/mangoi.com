<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$err_num = 0;
$err_msg = "";

$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberInviteID = isset($_REQUEST["MemberInviteID"]) ? $_REQUEST["MemberInviteID"] : "";
$MemberLevelID = isset($_REQUEST["MemberLevelID"]) ? $_REQUEST["MemberLevelID"] : "";
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

$MemberEmail2_1 = isset($_REQUEST["MemberEmail2_1"]) ? $_REQUEST["MemberEmail2_1"] : "";
$MemberEmail2_2 = isset($_REQUEST["MemberEmail2_2"]) ? $_REQUEST["MemberEmail2_2"] : "";
$MemberEmail2Agree = isset($_REQUEST["MemberEmail2Agree"]) ? $_REQUEST["MemberEmail2Agree"] : "0";

$MemberBirthday = isset($_REQUEST["MemberBirthday"]) ? $_REQUEST["MemberBirthday"] : "";
$SchoolName = isset($_REQUEST["SchoolName"]) ? $_REQUEST["SchoolName"] : "";
$MemberZip = isset($_REQUEST["MemberZip"]) ? $_REQUEST["MemberZip"] : "";
$MemberAddr1 = isset($_REQUEST["MemberAddr1"]) ? $_REQUEST["MemberAddr1"] : "";
$MemberAddr2 = isset($_REQUEST["MemberAddr2"]) ? $_REQUEST["MemberAddr2"] : "";
$MemberPhoto = isset($_REQUEST["MemberPhoto"]) ? $_REQUEST["MemberPhoto"] : "";
$MemberState = isset($_REQUEST["MemberState"]) ? $_REQUEST["MemberState"] : "";
$MemberStateText = isset($_REQUEST["MemberStateText"]) ? $_REQUEST["MemberStateText"] : "";
$MemberStudyAlarmTime = isset($_REQUEST["MemberStudyAlarmTime"]) ? $_REQUEST["MemberStudyAlarmTime"] : "";
$MemberStudyAlarmType = isset($_REQUEST["MemberStudyAlarmType"]) ? $_REQUEST["MemberStudyAlarmType"] : "";
$MemberChangeTeacher = isset($_REQUEST["MemberChangeTeacher"]) ? $_REQUEST["MemberChangeTeacher"] : "";
$MemberParentName = isset($_REQUEST["MemberParentName"]) ? $_REQUEST["MemberParentName"] : "";


//  부모 휴대폰 및 이메일 수신동의

$MemberPhone1 = $MemberPhone1_1 . "-" . $MemberPhone1_2 . "-" . $MemberPhone1_3; 
$MemberPhone2 = $MemberPhone2_1 . "-" . $MemberPhone2_2 . "-" . $MemberPhone2_3; 

$MemberEmail2 = $MemberEmail2_1 . "@" . $MemberEmail2_2;
//echo $MemberEmail2;


$Sql = " update Members set ";
	$Sql .= " CenterID =:CenterID, ";
	$Sql .= " MemberInviteID = :MemberInviteID, ";
	$Sql .= " MemberLoginInit = 1, ";
	$Sql .= " MemberLevelID = :MemberLevelID, ";
	$Sql .= " MemberNickName = :MemberNickName, ";
	$Sql .= " MemberParentName = :MemberParentName, ";
	$Sql .= " MemberBirthday = :MemberBirthday, ";
	$Sql .= " MemberPhone1 = HEX(AES_ENCRYPT(:MemberPhone1, :EncryptionKey)), ";
	$Sql .= " MemberPhone1Agree = :MemberPhone1Agree, ";
	$Sql .= " MemberPhone2 = HEX(AES_ENCRYPT(:MemberPhone2, :EncryptionKey)), ";
	$Sql .= " MemberPhone2Agree = :MemberPhone2Agree, ";
	$Sql .= " MemberEmail2 = HEX(AES_ENCRYPT(:MemberEmail2, :EncryptionKey)), ";
	$Sql .= " MemberEmail2Agree = :MemberEmail2Agree, ";
	$Sql .= " SchoolName = :SchoolName, ";
	$Sql .= " MemberStudyAlarmTime = :MemberStudyAlarmTime, ";
	$Sql .= " MemberStudyAlarmType = :MemberStudyAlarmType, ";
	$Sql .= " MemberChangeTeacher = :MemberChangeTeacher, ";
	$Sql .= " MemberZip = :MemberZip, ";
	$Sql .= " MemberAddr1 = :MemberAddr1, ";
	$Sql .= " MemberAddr2 = :MemberAddr2, ";
	$Sql .= " MemberSex = :MemberSex, ";
	$Sql .= " MemberModiDateTime = now() ";
$Sql .= " where MemberID = :MemberID ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':CenterID', $CenterID);
$Stmt->bindParam(':MemberInviteID', $MemberInviteID);
$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
$Stmt->bindParam(':MemberNickName', $MemberNickName);
$Stmt->bindParam(':MemberParentName', $MemberParentName);
$Stmt->bindParam(':MemberPhone1', $MemberPhone1);
$Stmt->bindParam(':MemberPhone1Agree', $MemberPhone1Agree);
$Stmt->bindParam(':MemberPhone2', $MemberPhone2);
$Stmt->bindParam(':MemberPhone2Agree', $MemberPhone2Agree);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->bindParam(':MemberEmail2', $MemberEmail2);
$Stmt->bindParam(':MemberEmail2Agree', $MemberEmail2Agree);
$Stmt->bindParam(':MemberBirthday', $MemberBirthday);
$Stmt->bindParam(':SchoolName', $SchoolName);
$Stmt->bindParam(':MemberStudyAlarmTime', $MemberStudyAlarmTime);
$Stmt->bindParam(':MemberStudyAlarmType', $MemberStudyAlarmType);
$Stmt->bindParam(':MemberChangeTeacher', $MemberChangeTeacher);
$Stmt->bindParam(':MemberZip', $MemberZip);
$Stmt->bindParam(':MemberAddr1', $MemberAddr1);
$Stmt->bindParam(':MemberAddr2', $MemberAddr2);
$Stmt->bindParam(':MemberSex', $MemberSex);
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
