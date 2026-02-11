<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');


$err_num = 0;
$err_msg = "";

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$ReviewClassMemberTitle = isset($_REQUEST["ReviewClassMemberTitle"]) ? $_REQUEST["ReviewClassMemberTitle"] : "";
$ReviewClassMemberContent = isset($_REQUEST["ReviewClassMemberContent"]) ? $_REQUEST["ReviewClassMemberContent"] : "";


$Sql = "select A.MemberName from Members A where MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Row = $Stmt->fetch();
$Stmt = null;

$MemberName = $Row["MemberName"];

$Sql = "
	insert into ReviewClassMembers ( ";
$Sql .= " MemberID, ";
$Sql .= " MemberName, ";
$Sql .= " ReviewClassMemberType, ";
$Sql .= " ReviewClassMemberTitle, ";
$Sql .= " ReviewClassMemberContent, ";
$Sql .= " ReviewClassMemberRegDateTime, ";
$Sql .= " ReviewClassMemberModiDateTime, ";
$Sql .= " ReviewClassMemberState ";
$Sql .= " ) values ( ";
$Sql .= " :MemberID, ";
$Sql .= " :MemberName, ";
$Sql .= " 2, ";
$Sql .= " :ReviewClassMemberTitle, ";
$Sql .= " :ReviewClassMemberContent, ";
$Sql .= " now(), ";
$Sql .= " now(), ";
$Sql .= " 1 ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':MemberName', $MemberName);
$Stmt->bindParam(':ReviewClassMemberTitle', $ReviewClassMemberTitle);
$Stmt->bindParam(':ReviewClassMemberContent', $ReviewClassMemberContent);
$Stmt->execute();

InsertNewTypePoint(7, 0, $MemberID, "");


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
alert("수강후기를 작성하였습니다..");
location.href = "mypage_review_list.php";
</script>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>


