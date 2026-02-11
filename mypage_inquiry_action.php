<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');


$err_num = 0;
$err_msg = "";

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$ContentType = isset($_REQUEST["ContentType"]) ? $_REQUEST["ContentType"] : "";
$DirectQnaMemberType = isset($_REQUEST["DirectQnaMemberType"]) ? $_REQUEST["DirectQnaMemberType"] : "";
$DirectQnaMemberTitle = isset($_REQUEST["DirectQnaMemberTitle"]) ? $_REQUEST["DirectQnaMemberTitle"] : "";
$DirectQnaMemberContent = isset($_REQUEST["DirectQnaMemberContent"]) ? $_REQUEST["DirectQnaMemberContent"] : "";
$AnswerType = 1;

if ($ContentType!="1"){
	$ContentType = 0;
}


$Sql = "select A.MemberName from Members A where MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Row = $Stmt->fetch();
$Stmt = null;

$MemberName = $Row["MemberName"];

$Sql = "
	insert into DirectQnaMembers ( ";
$Sql .= " MemberID, ";
$Sql .= " ContentType, ";
$Sql .= " AnswerType, ";
$Sql .= " MemberName, ";
$Sql .= " DirectQnaMemberTitle, ";
$Sql .= " DirectQnaMemberContent, ";
$Sql .= " DirectQnaMemberRegDateTime, ";
$Sql .= " DirectQnaMemberModiDateTime, ";
$Sql .= " DirectQnaMemberState ";
$Sql .= " ) values ( ";
$Sql .= " :MemberID, ";
$Sql .= " :ContentType, ";
$Sql .= " :AnswerType, ";
$Sql .= " :MemberName, ";
$Sql .= " :DirectQnaMemberTitle, ";
$Sql .= " :DirectQnaMemberContent, ";
$Sql .= " now(), ";
$Sql .= " now(), ";
$Sql .= " 1 ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':ContentType', $ContentType);
$Stmt->bindParam(':AnswerType', $AnswerType);
$Stmt->bindParam(':MemberName', $MemberName);
$Stmt->bindParam(':DirectQnaMemberTitle', $DirectQnaMemberTitle);
$Stmt->bindParam(':DirectQnaMemberContent', $DirectQnaMemberContent);
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
location.href = "mypage_inquiry.php";
</script>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>


