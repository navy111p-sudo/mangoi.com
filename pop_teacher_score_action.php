<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$err_num = 0;
$err_msg = "";


$MemberID = $_LINK_MEMBER_ID_;
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";

$AssmtTeacherScore1 = isset($_REQUEST["AssmtTeacherScore1"]) ? $_REQUEST["AssmtTeacherScore1"] : "";
$AssmtTeacherScore2 = isset($_REQUEST["AssmtTeacherScore2"]) ? $_REQUEST["AssmtTeacherScore2"] : "";
$AssmtTeacherScore3 = isset($_REQUEST["AssmtTeacherScore3"]) ? $_REQUEST["AssmtTeacherScore3"] : "";
$AssmtTeacherScore4 = isset($_REQUEST["AssmtTeacherScore4"]) ? $_REQUEST["AssmtTeacherScore4"] : "";
$AssmtTeacherScore5 = isset($_REQUEST["AssmtTeacherScore5"]) ? $_REQUEST["AssmtTeacherScore5"] : "";


$Sql = " insert into AssmtTeacherScores ( ";
	$Sql .= " ClassID, ";
	$Sql .= " MemberID, ";
	$Sql .= " AssmtTeacherScore1, ";
	$Sql .= " AssmtTeacherScore2, ";
	$Sql .= " AssmtTeacherScore3, ";
	$Sql .= " AssmtTeacherScore4, ";
	$Sql .= " AssmtTeacherScore5, ";
	$Sql .= " AssmtTeacherScoreRegDateTime ";
$Sql .= " ) values ( ";
	$Sql .= " :ClassID, ";
	$Sql .= " :MemberID, ";
	$Sql .= " :AssmtTeacherScore1, ";
	$Sql .= " :AssmtTeacherScore2, ";
	$Sql .= " :AssmtTeacherScore3, ";
	$Sql .= " :AssmtTeacherScore4, ";
	$Sql .= " :AssmtTeacherScore5, ";
	$Sql .= " now() ";
$Sql .= " ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':AssmtTeacherScore1', $AssmtTeacherScore1);
$Stmt->bindParam(':AssmtTeacherScore2', $AssmtTeacherScore2);
$Stmt->bindParam(':AssmtTeacherScore3', $AssmtTeacherScore3);
$Stmt->bindParam(':AssmtTeacherScore4', $AssmtTeacherScore4);
$Stmt->bindParam(':AssmtTeacherScore5', $AssmtTeacherScore5);
$Stmt->execute();
$Stmt = null;




$PointMemberID = $_LINK_MEMBER_ID_;

//================= 포인트 ======================
$Sql = "select A.MemberPointID from MemberPoints A where A.MemberPointTypeID=7 and A.MemberID=:MemberID and A.MemberPointState=1 and A.RootOrderID=:RootOrderID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $PointMemberID);
$Stmt->bindParam(':RootOrderID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberPointID = $Row["MemberPointID"];

if (!$MemberPointID){
	InsertPointWithRootOrderID(7, 0, $PointMemberID, "강의평가(웹)", "강의평가(웹)" ,$OnlineSiteTeacherAssmtPoint, $ClassID);
}
//================= 포인트 ======================



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
<?
}
?>