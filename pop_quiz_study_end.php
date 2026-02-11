<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$err_num = 0;
$err_msg = "";


$BookQuizResultID = isset($_REQUEST["BookQuizResultID"]) ? $_REQUEST["BookQuizResultID"] : "";
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";

$Sql = "update BookQuizResults set 
			BookQuizResultState=2,
			BookQuizResultEndDateTime=now()
		where BookQuizResultID=$BookQuizResultID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt = null;


$Sql = "
		select 
			A.ClassID
		from BookQuizResults A 
		where 
			 A.BookQuizResultID=:BookQuizResultID
	";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookQuizResultID', $BookQuizResultID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ClassID = $Row["ClassID"];


$PointMemberID = $_LINK_MEMBER_ID_;



//================= 포인트 ======================
InsertNewTypePoint(2, 0, $PointMemberID, $ClassID);
/*
$Sql = "select A.MemberPointID from MemberPoints A where A.MemberPointTypeID=6 and A.MemberID=:MemberID and A.MemberPointState=1 and A.RootOrderID=:RootOrderID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $PointMemberID);
$Stmt->bindParam(':RootOrderID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberPointID = $Row["MemberPointID"];

if (!$MemberPointID){
	InsertPointWithRootOrderID(6, 0, $PointMemberID, "리뷰퀴즈풀기(웹)", "리뷰퀴즈풀기(웹)" ,$OnlineSiteReStudyPoint, $ClassID);
}*/
//================= 포인트 ======================


?>
<?php
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
<?if ($FromDevice==""){?>
parent.$.fn.colorbox.close();
<?}else{?>
window.Exit=true;
<?}?>
</script>
</body>
</html>



 