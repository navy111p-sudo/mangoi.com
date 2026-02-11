<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$err_num = 0;
$err_msg = "";


$MemberID = $_LINK_MEMBER_ID_;
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";

$AssmtStudentMonthlyScoreID = isset($_REQUEST["AssmtStudentMonthlyScoreID"]) ? $_REQUEST["AssmtStudentMonthlyScoreID"] : "";

$AssmtStudentMonthlyScoreYear = isset($_REQUEST["AssmtStudentMonthlyScoreYear"]) ? $_REQUEST["AssmtStudentMonthlyScoreYear"] : "";
$AssmtStudentMonthlyScoreMonth = isset($_REQUEST["AssmtStudentMonthlyScoreMonth"]) ? $_REQUEST["AssmtStudentMonthlyScoreMonth"] : "";
$AssmtStudentMonthlyScoreLevel = isset($_REQUEST["AssmtStudentMonthlyScoreLevel"]) ? $_REQUEST["AssmtStudentMonthlyScoreLevel"] : "";

$AssmtStudentMonthlyScoreComment1 = isset($_REQUEST["AssmtStudentMonthlyScoreComment1"]) ? $_REQUEST["AssmtStudentMonthlyScoreComment1"] : "";
$AssmtStudentMonthlyScoreComment2 = isset($_REQUEST["AssmtStudentMonthlyScoreComment2"]) ? $_REQUEST["AssmtStudentMonthlyScoreComment2"] : "";
$AssmtStudentMonthlyScoreComment3 = isset($_REQUEST["AssmtStudentMonthlyScoreComment3"]) ? $_REQUEST["AssmtStudentMonthlyScoreComment3"] : "";
$AssmtStudentMonthlyScoreComment4 = isset($_REQUEST["AssmtStudentMonthlyScoreComment4"]) ? $_REQUEST["AssmtStudentMonthlyScoreComment4"] : "";
$AssmtStudentMonthlyScoreComment5 = isset($_REQUEST["AssmtStudentMonthlyScoreComment5"]) ? $_REQUEST["AssmtStudentMonthlyScoreComment5"] : "";
$AssmtStudentMonthlyScoreCommentTotal = isset($_REQUEST["AssmtStudentMonthlyScoreCommentTotal"]) ? $_REQUEST["AssmtStudentMonthlyScoreCommentTotal"] : "";
$AssmtStudentMonthlyScoreSubject = isset($_REQUEST["AssmtStudentMonthlyScoreSubject"]) ? $_REQUEST["AssmtStudentMonthlyScoreSubject"] : "";


if ($AssmtStudentMonthlyScoreID==""){

	$Sql = " insert into AssmtStudentMonthlyScores ( ";
		$Sql .= " ClassID, ";
		$Sql .= " MemberID, ";
		$Sql .= " AssmtStudentMonthlyScoreSubject, ";
		$Sql .= " AssmtStudentMonthlyScoreLevel, ";
		$Sql .= " AssmtStudentMonthlyScoreYear, ";
		$Sql .= " AssmtStudentMonthlyScoreMonth, ";
		$Sql .= " AssmtStudentMonthlyScoreComment1, ";
		$Sql .= " AssmtStudentMonthlyScoreComment2, ";
		$Sql .= " AssmtStudentMonthlyScoreComment3, ";
		$Sql .= " AssmtStudentMonthlyScoreComment4, ";
		$Sql .= " AssmtStudentMonthlyScoreComment5, ";
		$Sql .= " AssmtStudentMonthlyScoreCommentTotal, ";
		$Sql .= " AssmtStudentMonthlyScoreRegDateTime, ";
		$Sql .= " AssmtStudentMonthlyScoreModiDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :ClassID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " :AssmtStudentMonthlyScoreSubject, ";
		$Sql .= " :AssmtStudentMonthlyScoreLevel, ";
		$Sql .= " :AssmtStudentMonthlyScoreYear, ";
		$Sql .= " :AssmtStudentMonthlyScoreMonth, ";
		$Sql .= " :AssmtStudentMonthlyScoreComment1, ";
		$Sql .= " :AssmtStudentMonthlyScoreComment2, ";
		$Sql .= " :AssmtStudentMonthlyScoreComment3, ";
		$Sql .= " :AssmtStudentMonthlyScoreComment4, ";
		$Sql .= " :AssmtStudentMonthlyScoreComment5, ";
		$Sql .= " :AssmtStudentMonthlyScoreCommentTotal, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassID', $ClassID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreSubject', $AssmtStudentMonthlyScoreSubject);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreLevel', $AssmtStudentMonthlyScoreLevel);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreYear', $AssmtStudentMonthlyScoreYear);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreMonth', $AssmtStudentMonthlyScoreMonth);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreComment1', $AssmtStudentMonthlyScoreComment1);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreComment2', $AssmtStudentMonthlyScoreComment2);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreComment3', $AssmtStudentMonthlyScoreComment3);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreComment4', $AssmtStudentMonthlyScoreComment4);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreComment5', $AssmtStudentMonthlyScoreComment5);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreCommentTotal', $AssmtStudentMonthlyScoreCommentTotal);
	$Stmt->execute();
	$Stmt = null;

	// 평가서 작성 완료 알림톡 보내기
	// 먼저 학생의 memberID 가져오기
	$Sql = "SELECT MemberID FROM Classes WHERE ClassID = :ClassID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassID', $ClassID);
	$Stmt->execute();
	$Row = $Stmt->fetch();
	
	$StudentMemberID = $Row["MemberID"];

	$RowSms = GetMemberSmsInfo($StudentMemberID);

	$MemberName = $RowSms["MemberName"];
	$DecMemberPhone1 = $RowSms["DecMemberPhone1"];
	$DecMemberPhone2 = $RowSms["DecMemberPhone2"];
	
	$msg = "$MemberName 님의 평가서 작성이 완료되었습니다. 마이페이지에서 확인이 가능합니다. 감사합니다.";
	
	$tmplId="mangoi_006";  //카카오 알림톡 템플릿 아이디(문자 메시지를 바꿀려면 비즈엠에서 템플릿 등록하고 그 메시지를 위에 복사후 템플릿id도 바꿔야 함)

	if (!empty($DecMemberPhone1))
		SendAlimtalk($DecMemberPhone1, $msg,$tmplId);
	if (!empty($DecMemberPhone2))	
		SendAlimtalk($DecMemberPhone2, $msg,$tmplId);

}else{

	$Sql = " update AssmtStudentMonthlyScores set ";
		$Sql .= " AssmtStudentMonthlyScoreSubject = :AssmtStudentMonthlyScoreSubject, ";
		$Sql .= " AssmtStudentMonthlyScoreLevel = :AssmtStudentMonthlyScoreLevel, ";
		$Sql .= " AssmtStudentMonthlyScoreYear = :AssmtStudentMonthlyScoreYear, ";
		$Sql .= " AssmtStudentMonthlyScoreMonth = :AssmtStudentMonthlyScoreMonth, ";
		$Sql .= " AssmtStudentMonthlyScoreComment1 = :AssmtStudentMonthlyScoreComment1, ";
		$Sql .= " AssmtStudentMonthlyScoreComment2 = :AssmtStudentMonthlyScoreComment2, ";
		$Sql .= " AssmtStudentMonthlyScoreComment3 = :AssmtStudentMonthlyScoreComment3, ";
		$Sql .= " AssmtStudentMonthlyScoreComment4 = :AssmtStudentMonthlyScoreComment4, ";
		$Sql .= " AssmtStudentMonthlyScoreComment5 = :AssmtStudentMonthlyScoreComment5, ";
		$Sql .= " AssmtStudentMonthlyScoreCommentTotal = :AssmtStudentMonthlyScoreCommentTotal, ";
		$Sql .= " AssmtStudentMonthlyScoreModiDateTime = now() ";
	$Sql .= " where AssmtStudentMonthlyScoreID = :AssmtStudentMonthlyScoreID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreSubject', $AssmtStudentMonthlyScoreSubject);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreLevel', $AssmtStudentMonthlyScoreLevel);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreYear', $AssmtStudentMonthlyScoreYear);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreMonth', $AssmtStudentMonthlyScoreMonth);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreComment1', $AssmtStudentMonthlyScoreComment1);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreComment2', $AssmtStudentMonthlyScoreComment2);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreComment3', $AssmtStudentMonthlyScoreComment3);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreComment4', $AssmtStudentMonthlyScoreComment4);
	$Stmt->bindParam(':AssmtStudentMonthlyScoreComment5', $AssmtStudentMonthlyScoreComment5);

	$Stmt->bindParam(':AssmtStudentMonthlyScoreCommentTotal', $AssmtStudentMonthlyScoreCommentTotal);

	$Stmt->bindParam(':AssmtStudentMonthlyScoreID', $AssmtStudentMonthlyScoreID);
	$Stmt->execute();
	$Stmt = null;

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
parent.$.fn.colorbox.close();
</script>
</body>
</html>
<?
}
?>