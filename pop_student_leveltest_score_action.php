<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$err_num = 0;
$err_msg = "";


$MemberID = $_LINK_MEMBER_ID_;
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";

$AssmtStudentLeveltestScoreID = isset($_REQUEST["AssmtStudentLeveltestScoreID"]) ? $_REQUEST["AssmtStudentLeveltestScoreID"] : "";

$AssmtStudentLeveltestScoreYear = isset($_REQUEST["AssmtStudentLeveltestScoreYear"]) ? $_REQUEST["AssmtStudentLeveltestScoreYear"] : "";
$AssmtStudentLeveltestScoreMonth = isset($_REQUEST["AssmtStudentLeveltestScoreMonth"]) ? $_REQUEST["AssmtStudentLeveltestScoreMonth"] : "";
$AssmtStudentLeveltestScoreDay = isset($_REQUEST["AssmtStudentLeveltestScoreDay"]) ? $_REQUEST["AssmtStudentLeveltestScoreDay"] : "";
$AssmtStudentLeveltestScoreLevel = isset($_REQUEST["AssmtStudentLeveltestScoreLevel"]) ? $_REQUEST["AssmtStudentLeveltestScoreLevel"] : "";

$AssmtStudentLeveltestPass = isset($_REQUEST["AssmtStudentLeveltestPass"]) ? $_REQUEST["AssmtStudentLeveltestPass"] : "";
$AssmtStudentLeveltestScore1 = isset($_REQUEST["AssmtStudentLeveltestScore1"]) ? $_REQUEST["AssmtStudentLeveltestScore1"] : "";
$AssmtStudentLeveltestScore2 = isset($_REQUEST["AssmtStudentLeveltestScore2"]) ? $_REQUEST["AssmtStudentLeveltestScore2"] : "";
$AssmtStudentLeveltestScore3 = isset($_REQUEST["AssmtStudentLeveltestScore3"]) ? $_REQUEST["AssmtStudentLeveltestScore3"] : "";
$AssmtStudentLeveltestScore4 = isset($_REQUEST["AssmtStudentLeveltestScore4"]) ? $_REQUEST["AssmtStudentLeveltestScore4"] : "";
$AssmtStudentLeveltestScore5 = isset($_REQUEST["AssmtStudentLeveltestScore5"]) ? $_REQUEST["AssmtStudentLeveltestScore5"] : "";

$AssmtStudentLeveltestScoreComment1 = isset($_REQUEST["AssmtStudentMonthlyScoreComment1"]) ? $_REQUEST["AssmtStudentMonthlyScoreComment1"] : "";
$AssmtStudentLeveltestScoreComment2 = isset($_REQUEST["AssmtStudentMonthlyScoreComment2"]) ? $_REQUEST["AssmtStudentMonthlyScoreComment2"] : "";
$AssmtStudentLeveltestScoreComment3 = isset($_REQUEST["AssmtStudentMonthlyScoreComment3"]) ? $_REQUEST["AssmtStudentMonthlyScoreComment3"] : "";
$AssmtStudentLeveltestScoreComment4 = isset($_REQUEST["AssmtStudentMonthlyScoreComment4"]) ? $_REQUEST["AssmtStudentMonthlyScoreComment4"] : "";
$AssmtStudentLeveltestScoreComment5 = isset($_REQUEST["AssmtStudentMonthlyScoreComment5"]) ? $_REQUEST["AssmtStudentMonthlyScoreComment5"] : "";
$AssmtStudentLeveltestScoreCommentTotal = isset($_REQUEST["AssmtStudentMonthlyScoreCommentTotal"]) ? $_REQUEST["AssmtStudentMonthlyScoreCommentTotal"] : "";


if ($AssmtStudentLeveltestScoreID==""){


	$Sql = " insert into AssmtStudentLeveltestScores ( ";
		$Sql .= " ClassID, ";
		$Sql .= " MemberID, ";
		$Sql .= " AssmtStudentLeveltestScoreLevel, ";
		$Sql .= " AssmtStudentLeveltestScoreYear, ";
		$Sql .= " AssmtStudentLeveltestScoreMonth, ";
		$Sql .= " AssmtStudentLeveltestScoreDay, ";

		$Sql .= " AssmtStudentLeveltestPass, ";
		$Sql .= " AssmtStudentLeveltestScore1, ";
		$Sql .= " AssmtStudentLeveltestScore2, ";
		$Sql .= " AssmtStudentLeveltestScore3, ";
		$Sql .= " AssmtStudentLeveltestScore4, ";
		$Sql .= " AssmtStudentLeveltestScore5, ";

		$Sql .= " AssmtStudentLeveltestScoreComment1, ";
		$Sql .= " AssmtStudentLeveltestScoreComment2, ";
		$Sql .= " AssmtStudentLeveltestScoreComment3, ";
		$Sql .= " AssmtStudentLeveltestScoreComment4, ";
		$Sql .= " AssmtStudentLeveltestScoreComment5, ";
		$Sql .= " AssmtStudentLeveltestScoreCommentTotal, ";
		$Sql .= " AssmtStudentLeveltestScoreRegDateTime, ";
		$Sql .= " AssmtStudentLeveltestScoreModiDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :ClassID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " :AssmtStudentLeveltestScoreLevel, ";
		$Sql .= " :AssmtStudentLeveltestScoreYear, ";
		$Sql .= " :AssmtStudentLeveltestScoreMonth, ";
		$Sql .= " :AssmtStudentLeveltestScoreDay, ";

		$Sql .= " :AssmtStudentLeveltestPass, ";
		$Sql .= " :AssmtStudentLeveltestScore1, ";
		$Sql .= " :AssmtStudentLeveltestScore2, ";
		$Sql .= " :AssmtStudentLeveltestScore3, ";
		$Sql .= " :AssmtStudentLeveltestScore4, ";
		$Sql .= " :AssmtStudentLeveltestScore5, ";

		$Sql .= " :AssmtStudentLeveltestScoreComment1, ";
		$Sql .= " :AssmtStudentLeveltestScoreComment2, ";
		$Sql .= " :AssmtStudentLeveltestScoreComment3, ";
		$Sql .= " :AssmtStudentLeveltestScoreComment4, ";
		$Sql .= " :AssmtStudentLeveltestScoreComment5, ";
		$Sql .= " :AssmtStudentLeveltestScoreCommentTotal, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassID', $ClassID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreLevel', $AssmtStudentLeveltestScoreLevel);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreYear', $AssmtStudentLeveltestScoreYear);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreMonth', $AssmtStudentLeveltestScoreMonth);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreDay', $AssmtStudentLeveltestScoreDay);

	$Stmt->bindParam(':AssmtStudentLeveltestPass', $AssmtStudentLeveltestPass);
	$Stmt->bindParam(':AssmtStudentLeveltestScore1', $AssmtStudentLeveltestScore1);
	$Stmt->bindParam(':AssmtStudentLeveltestScore2', $AssmtStudentLeveltestScore2);
	$Stmt->bindParam(':AssmtStudentLeveltestScore3', $AssmtStudentLeveltestScore3);
	$Stmt->bindParam(':AssmtStudentLeveltestScore4', $AssmtStudentLeveltestScore4);
	$Stmt->bindParam(':AssmtStudentLeveltestScore5', $AssmtStudentLeveltestScore5);

	$Stmt->bindParam(':AssmtStudentLeveltestScoreComment1', $AssmtStudentLeveltestScoreComment1);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreComment2', $AssmtStudentLeveltestScoreComment2);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreComment3', $AssmtStudentLeveltestScoreComment3);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreComment4', $AssmtStudentLeveltestScoreComment4);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreComment5', $AssmtStudentLeveltestScoreComment5);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreCommentTotal', $AssmtStudentLeveltestScoreCommentTotal);
	$Stmt->execute();
	$Stmt = null;


	$Sql = " update Classes set ";
		$Sql .= " ClassState=2 ";
	$Sql .= " where ClassID=:ClassID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassID', $ClassID);
	$Stmt->execute();
	$Stmt = null;

	// 레벨 테스트 작성 완료 알림톡 보내기
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
		
	$msg = "$MemberName 님의 레벨테스트 결과 작성이 완료되었습니다. PC 또는 어플리케이션 통해 로그인 하신 후 마이페이지에서 확인이 가능합니다.";
		
	$tmplId="mangoi_003";  //카카오 알림톡 템플릿 아이디(문자 메시지를 바꿀려면 비즈엠에서 템플릿 등록하고 그 메시지를 위에 복사후 템플릿id도 바꿔야 함)
	
	if (!empty($DecMemberPhone1))
		SendAlimtalk($DecMemberPhone1, $msg,$tmplId);
	if (!empty($DecMemberPhone2))	
		SendAlimtalk($DecMemberPhone2, $msg,$tmplId);
	

}else{

	$Sql = " update AssmtStudentLeveltestScores set ";
		$Sql .= " AssmtStudentLeveltestScoreLevel = :AssmtStudentLeveltestScoreLevel, ";
		$Sql .= " AssmtStudentLeveltestPass = :AssmtStudentLeveltestPass, ";
		$Sql .= " AssmtStudentLeveltestScore1 = :AssmtStudentLeveltestScore1, ";
		$Sql .= " AssmtStudentLeveltestScore2 = :AssmtStudentLeveltestScore2, ";
		$Sql .= " AssmtStudentLeveltestScore3 = :AssmtStudentLeveltestScore3, ";
		$Sql .= " AssmtStudentLeveltestScore4 = :AssmtStudentLeveltestScore4, ";
		$Sql .= " AssmtStudentLeveltestScore5 = :AssmtStudentLeveltestScore5, ";
		$Sql .= " AssmtStudentLeveltestScoreComment1 = :AssmtStudentLeveltestScoreComment1, ";
		$Sql .= " AssmtStudentLeveltestScoreComment2 = :AssmtStudentLeveltestScoreComment2, ";
		$Sql .= " AssmtStudentLeveltestScoreComment3 = :AssmtStudentLeveltestScoreComment3, ";
		$Sql .= " AssmtStudentLeveltestScoreComment4 = :AssmtStudentLeveltestScoreComment4, ";
		$Sql .= " AssmtStudentLeveltestScoreComment5 = :AssmtStudentLeveltestScoreComment5, ";
		$Sql .= " AssmtStudentLeveltestScoreCommentTotal = :AssmtStudentLeveltestScoreCommentTotal, ";
		$Sql .= " AssmtStudentLeveltestScoreModiDateTime = now() ";
	$Sql .= " where AssmtStudentLeveltestScoreID = :AssmtStudentLeveltestScoreID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreLevel', $AssmtStudentLeveltestScoreLevel);
	$Stmt->bindParam(':AssmtStudentLeveltestPass', $AssmtStudentLeveltestPass);
	$Stmt->bindParam(':AssmtStudentLeveltestScore1', $AssmtStudentLeveltestScore1);
	$Stmt->bindParam(':AssmtStudentLeveltestScore2', $AssmtStudentLeveltestScore2);
	$Stmt->bindParam(':AssmtStudentLeveltestScore3', $AssmtStudentLeveltestScore3);
	$Stmt->bindParam(':AssmtStudentLeveltestScore4', $AssmtStudentLeveltestScore4);
	$Stmt->bindParam(':AssmtStudentLeveltestScore5', $AssmtStudentLeveltestScore5);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreComment1', $AssmtStudentLeveltestScoreComment1);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreComment2', $AssmtStudentLeveltestScoreComment2);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreComment3', $AssmtStudentLeveltestScoreComment3);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreComment4', $AssmtStudentLeveltestScoreComment4);
	$Stmt->bindParam(':AssmtStudentLeveltestScoreComment5', $AssmtStudentLeveltestScoreComment5);

	$Stmt->bindParam(':AssmtStudentLeveltestScoreCommentTotal', $AssmtStudentLeveltestScoreCommentTotal);

	$Stmt->bindParam(':AssmtStudentLeveltestScoreID', $AssmtStudentLeveltestScoreID);
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
parent.location.reload();
//parent.$.fn.colorbox.close();
</script>
</body>
</html>
<?
}
?>