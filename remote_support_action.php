<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');


$err_num = 0;
$err_msg = "";

$MemberID = (int)isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
$RemoteSupportMemberTitle = isset($_REQUEST["RemoteSupportMemberTitle"]) ? $_REQUEST["RemoteSupportMemberTitle"] : "";
$RemoteSupportMemberStartDateTime = isset($_REQUEST["RemoteSupportMemberStartDateTime"]) ? $_REQUEST["RemoteSupportMemberStartDateTime"] : "";
$RemoteSupportMemberEndDateTime = isset($_REQUEST["RemoteSupportMemberEndDateTime"]) ? $_REQUEST["RemoteSupportMemberEndDateTime"] : "";
$RemoteSupportMemberContent = isset($_REQUEST["RemoteSupportMemberContent"]) ? $_REQUEST["RemoteSupportMemberContent"] : "";
$RemoteSupportMemberPhone_1 = isset($_REQUEST["RemoteSupportMemberPhone_1"]) ? $_REQUEST["RemoteSupportMemberPhone_1"] : "";
$RemoteSupportMemberPhone_2 = isset($_REQUEST["RemoteSupportMemberPhone_2"]) ? $_REQUEST["RemoteSupportMemberPhone_2"] : "";
$RemoteSupportMemberPhone_3 = isset($_REQUEST["RemoteSupportMemberPhone_3"]) ? $_REQUEST["RemoteSupportMemberPhone_3"] : "";

$RemoteSupportMemberPhone = $RemoteSupportMemberPhone_1 . "-" . $RemoteSupportMemberPhone_2 . "-" . $RemoteSupportMemberPhone_3;

$RemoteSupportMemberEndDateTime = $RemoteSupportMemberStartDateTime;


$Sql = "
	insert into RemoteSupportMembers ( ";
$Sql .= " MemberID, ";
$Sql .= " MemberName, ";
$Sql .= " RemoteSupportMemberTitle, ";
$Sql .= " RemoteSupportMemberContent, ";
$Sql .= " RemoteSupportMemberStartDateTime, ";
$Sql .= " RemoteSupportMemberEndDateTime, ";
$Sql .= " RemoteSupportMemberPhone, ";
$Sql .= " RemoteSupportMemberRegDateTime, ";
$Sql .= " RemoteSupportMemberModiDateTime, ";
$Sql .= " RemoteSupportMemberState ";
$Sql .= " ) values ( ";
$Sql .= " :MemberID, ";
$Sql .= " :MemberName, ";
$Sql .= " :RemoteSupportMemberTitle, ";
$Sql .= " :RemoteSupportMemberContent, ";
$Sql .= " :RemoteSupportMemberStartDateTime, ";
$Sql .= " :RemoteSupportMemberEndDateTime, ";
$Sql .= " :RemoteSupportMemberPhone, ";
$Sql .= " now(), ";
$Sql .= " now(), ";
$Sql .= " 1 ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':MemberName', $MemberName);
$Stmt->bindParam(':RemoteSupportMemberTitle', $RemoteSupportMemberTitle);
$Stmt->bindParam(':RemoteSupportMemberContent', $RemoteSupportMemberContent);
$Stmt->bindParam(':RemoteSupportMemberStartDateTime', $RemoteSupportMemberStartDateTime);
$Stmt->bindParam(':RemoteSupportMemberEndDateTime', $RemoteSupportMemberEndDateTime);
$Stmt->bindParam(':RemoteSupportMemberPhone', $RemoteSupportMemberPhone);
$Stmt->execute();


include_once('./includes/dbclose.php');


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
alert("상담신청을 완료하였습니다..");
location.href = "remote_support.php";
</script>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>


