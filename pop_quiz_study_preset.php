<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$err_num = 0;
$err_msg = "";

$BookQuizID = isset($_REQUEST["BookQuizID"]) ? $_REQUEST["BookQuizID"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$QuizStudyNumber = isset($_REQUEST["QuizStudyNumber"]) ? $_REQUEST["QuizStudyNumber"] : "";
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";

$Sql = "
		select 
			A.BookQuizResultID,
			A.BookQuizResultState
		from BookQuizResults A 
		where 
			A.ClassID=:ClassID 
			and A.QuizStudyNumber=:QuizStudyNumber
			and A.BookQuizResultState=2 
	";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->bindParam(':QuizStudyNumber', $QuizStudyNumber);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$CheckBookQuizResultID = $Row["BookQuizResultID"];


$Sql = "
		select 
			A.BookQuizResultID,
			A.BookQuizResultState
		from BookQuizResults A 
		where 
			A.ClassID=:ClassID 
			and A.QuizStudyNumber=:QuizStudyNumber 
	";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->bindParam(':QuizStudyNumber', $QuizStudyNumber);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$BookQuizResultID = $Row["BookQuizResultID"];
$BookQuizResultState = $Row["BookQuizResultState"];

if (!$BookQuizResultID){
	$Sql = "insert into BookQuizResults (
					ClassID,
					BookQuizID,
					QuizStudyNumber,
					BookQuizResultState,
					BookQuizCurrentPage,
					BookQuizResultRegDateTime,
					BookQuizResultModiDateTime

		) values (
					:ClassID,
					:BookQuizID,
					:QuizStudyNumber,
					1,
					1,
					now(),
					now()
		)";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassID', $ClassID);
	$Stmt->bindParam(':BookQuizID', $BookQuizID);
	$Stmt->bindParam(':QuizStudyNumber', $QuizStudyNumber);
	$Stmt->execute();
	$BookQuizResultID = $DbConn->lastInsertId();
	$Stmt = null;

	// 듣기문제 2개 랜덤 선별
	$Sql = "select 
				A.* 	
			from BookQuizDetails A
			where 
				A.BookQuizID=:BookQuizID 
				and A.BookQuizDetailView=1 
				and A.BookQuizDetailState=1 
				and A.BookQuizDetailQuizType=2 
			order by rand() limit 0, 2";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookQuizID', $BookQuizID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	$ii=1;
	while($Row = $Stmt->fetch()) {
		
		$BookQuizDetailID = $Row["BookQuizDetailID"];
		$BookQuizDetailQuizType = $Row["BookQuizDetailQuizType"];
		$BookQuizDetailSoundFileName = $Row["BookQuizDetailSoundFileName"];
		$BookQuizDetailSoundFileRealName = $Row["BookQuizDetailSoundFileRealName"];
		$BookQuizDetailText = $Row["BookQuizDetailText"];
		$BookQuizDetailQuestionType = $Row["BookQuizDetailQuestionType"];
		$BookQuizDetailImageFileName = $Row["BookQuizDetailImageFileName"];
		$BookQuizDetailImageFileRealName = $Row["BookQuizDetailImageFileRealName"];
		$BookQuizDetailTextQuestion = $Row["BookQuizDetailTextQuestion"];
		$BookQuizDetailVideoCode = $Row["BookQuizDetailVideoCode"];
		$BookQuizDetailAnswerType = $Row["BookQuizDetailAnswerType"];
		$BookQuizDetailChoice1 = $Row["BookQuizDetailChoice1"];
		$BookQuizDetailChoice2 = $Row["BookQuizDetailChoice2"];
		$BookQuizDetailChoice3 = $Row["BookQuizDetailChoice3"];
		$BookQuizDetailChoice4 = $Row["BookQuizDetailChoice4"];
		$BookQuizDetailChoiceImage1 = $Row["BookQuizDetailChoiceImage1"];
		$BookQuizDetailChoiceImage2 = $Row["BookQuizDetailChoiceImage2"];
		$BookQuizDetailChoiceImage3 = $Row["BookQuizDetailChoiceImage3"];
		$BookQuizDetailChoiceImage4 = $Row["BookQuizDetailChoiceImage4"];
		$BookQuizDetailCorrectAnswer = $Row["BookQuizDetailCorrectAnswer"];
		$BookQuizDetailOrder = $ii;
		

		$Sql2 = "insert into BookQuizResultDetails (
						BookQuizResultID,
						BookQuizDetailID,
						BookQuizDetailQuizType,
						BookQuizDetailSoundFileName,
						BookQuizDetailSoundFileRealName,
						BookQuizDetailText,
						BookQuizDetailQuestionType,
						BookQuizDetailImageFileName,
						BookQuizDetailImageFileRealName,
						BookQuizDetailTextQuestion,
						BookQuizDetailVideoCode,
						BookQuizDetailAnswerType,
						BookQuizDetailChoice1,
						BookQuizDetailChoice2,
						BookQuizDetailChoice3,
						BookQuizDetailChoice4,
						BookQuizDetailChoiceImage1,
						BookQuizDetailChoiceImage2,
						BookQuizDetailChoiceImage3,
						BookQuizDetailChoiceImage4,
						BookQuizDetailCorrectAnswer,
						BookQuizDetailOrder,
						MyAnswer,
						MyScore

			) values (
						:BookQuizResultID,
						:BookQuizDetailID,
						:BookQuizDetailQuizType,
						:BookQuizDetailSoundFileName,
						:BookQuizDetailSoundFileRealName,
						:BookQuizDetailText,
						:BookQuizDetailQuestionType,
						:BookQuizDetailImageFileName,
						:BookQuizDetailImageFileRealName,
						:BookQuizDetailTextQuestion,
						:BookQuizDetailVideoCode,
						:BookQuizDetailAnswerType,
						:BookQuizDetailChoice1,
						:BookQuizDetailChoice2,
						:BookQuizDetailChoice3,
						:BookQuizDetailChoice4,
						:BookQuizDetailChoiceImage1,
						:BookQuizDetailChoiceImage2,
						:BookQuizDetailChoiceImage3,
						:BookQuizDetailChoiceImage4,
						:BookQuizDetailCorrectAnswer,
						:BookQuizDetailOrder,
						0,
						0
			)";

		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':BookQuizResultID', $BookQuizResultID);
		$Stmt2->bindParam(':BookQuizDetailID', $BookQuizDetailID);
		$Stmt2->bindParam(':BookQuizDetailQuizType', $BookQuizDetailQuizType);
		$Stmt2->bindParam(':BookQuizDetailSoundFileName', $BookQuizDetailSoundFileName);
		$Stmt2->bindParam(':BookQuizDetailSoundFileRealName', $BookQuizDetailSoundFileRealName);
		$Stmt2->bindParam(':BookQuizDetailQuestionType', $BookQuizDetailQuestionType);
		$Stmt2->bindParam(':BookQuizDetailTextQuestion', $BookQuizDetailTextQuestion);
		$Stmt2->bindParam(':BookQuizDetailVideoCode', $BookQuizDetailVideoCode);
		$Stmt2->bindParam(':BookQuizDetailAnswerType', $BookQuizDetailAnswerType);
		$Stmt2->bindParam(':BookQuizDetailChoiceImage1', $BookQuizDetailChoiceImage1);
		$Stmt2->bindParam(':BookQuizDetailChoiceImage2', $BookQuizDetailChoiceImage2);
		$Stmt2->bindParam(':BookQuizDetailChoiceImage3', $BookQuizDetailChoiceImage3);
		$Stmt2->bindParam(':BookQuizDetailChoiceImage4', $BookQuizDetailChoiceImage4);
		$Stmt2->bindParam(':BookQuizDetailText', $BookQuizDetailText);
		$Stmt2->bindParam(':BookQuizDetailImageFileName', $BookQuizDetailImageFileName);
		$Stmt2->bindParam(':BookQuizDetailImageFileRealName', $BookQuizDetailImageFileRealName);
		$Stmt2->bindParam(':BookQuizDetailChoice1', $BookQuizDetailChoice1);
		$Stmt2->bindParam(':BookQuizDetailChoice2', $BookQuizDetailChoice2);
		$Stmt2->bindParam(':BookQuizDetailChoice3', $BookQuizDetailChoice3);
		$Stmt2->bindParam(':BookQuizDetailChoice4', $BookQuizDetailChoice4);
		$Stmt2->bindParam(':BookQuizDetailCorrectAnswer', $BookQuizDetailCorrectAnswer);
		$Stmt2->bindParam(':BookQuizDetailOrder', $BookQuizDetailOrder);
		$Stmt2->execute();
		$Stmt2 = null;

		$ii++;
	}
	$Stmt = null;


	// 일반문제 10개 랜덤 선별
	$Sql3 = "select 
				A.* 	
			from BookQuizDetails A
			where 
				A.BookQuizID=:BookQuizID 
				and A.BookQuizDetailView=1 
				and A.BookQuizDetailState=1 
				and A.BookQuizDetailQuizType=1 
			order by rand() limit 0, 10";

	$Stmt3 = $DbConn->prepare($Sql3);
	$Stmt3->bindParam(':BookQuizID', $BookQuizID);
	$Stmt3->execute();
	$Stmt3->setFetchMode(PDO::FETCH_ASSOC);

	while($Row3 = $Stmt3->fetch()) {
		
		$BookQuizDetailID = $Row3["BookQuizDetailID"];
		$BookQuizDetailQuizType = $Row3["BookQuizDetailQuizType"];
		$BookQuizDetailSoundFileName = $Row3["BookQuizDetailSoundFileName"];
		$BookQuizDetailSoundFileRealName = $Row3["BookQuizDetailSoundFileRealName"];
		$BookQuizDetailText = $Row3["BookQuizDetailText"];
		$BookQuizDetailQuestionType = $Row3["BookQuizDetailQuestionType"];
		$BookQuizDetailImageFileName = $Row3["BookQuizDetailImageFileName"];
		$BookQuizDetailImageFileRealName = $Row3["BookQuizDetailImageFileRealName"];
		$BookQuizDetailVideoCode = $Row3["BookQuizDetailVideoCode"];
		$BookQuizDetailTextQuestion = $Row3["BookQuizDetailTextQuestion"];
		$BookQuizDetailAnswerType = $Row3["BookQuizDetailAnswerType"];
		$BookQuizDetailChoice1 = $Row3["BookQuizDetailChoice1"];
		$BookQuizDetailChoice2 = $Row3["BookQuizDetailChoice2"];
		$BookQuizDetailChoice3 = $Row3["BookQuizDetailChoice3"];
		$BookQuizDetailChoice4 = $Row3["BookQuizDetailChoice4"];
		$BookQuizDetailChoiceImage1 = $Row3["BookQuizDetailChoiceImage1"];
		$BookQuizDetailChoiceImage2 = $Row3["BookQuizDetailChoiceImage2"];
		$BookQuizDetailChoiceImage3 = $Row3["BookQuizDetailChoiceImage3"];
		$BookQuizDetailChoiceImage4 = $Row3["BookQuizDetailChoiceImage4"];
		$BookQuizDetailCorrectAnswer = $Row3["BookQuizDetailCorrectAnswer"];
		$BookQuizDetailOrder = $ii;
		

		$Sql4 = "insert into BookQuizResultDetails (
						BookQuizResultID,
						BookQuizDetailID,
						BookQuizDetailQuizType,
						BookQuizDetailSoundFileName,
						BookQuizDetailSoundFileRealName,
						BookQuizDetailText,
						BookQuizDetailQuestionType,
						BookQuizDetailImageFileName,
						BookQuizDetailImageFileRealName,
						BookQuizDetailTextQuestion,
						BookQuizDetailVideoCode,
						BookQuizDetailAnswerType,
						BookQuizDetailChoice1,
						BookQuizDetailChoice2,
						BookQuizDetailChoice3,
						BookQuizDetailChoice4,
						BookQuizDetailChoiceImage1,
						BookQuizDetailChoiceImage2,
						BookQuizDetailChoiceImage3,
						BookQuizDetailChoiceImage4,
						BookQuizDetailCorrectAnswer,
						BookQuizDetailOrder,
						MyAnswer,
						MyScore

			) values (
						:BookQuizResultID,
						:BookQuizDetailID,
						:BookQuizDetailQuizType,
						:BookQuizDetailSoundFileName,
						:BookQuizDetailSoundFileRealName,
						:BookQuizDetailText,
						:BookQuizDetailQuestionType,
						:BookQuizDetailImageFileName,
						:BookQuizDetailImageFileRealName,
						:BookQuizDetailTextQuestion,
						:BookQuizDetailVideoCode,
						:BookQuizDetailAnswerType,
						:BookQuizDetailChoice1,
						:BookQuizDetailChoice2,
						:BookQuizDetailChoice3,
						:BookQuizDetailChoice4,
						:BookQuizDetailChoiceImage1,
						:BookQuizDetailChoiceImage2,
						:BookQuizDetailChoiceImage3,
						:BookQuizDetailChoiceImage4,
						:BookQuizDetailCorrectAnswer,
						:BookQuizDetailOrder,
						0,
						0
			)";

		$Stmt4 = $DbConn->prepare($Sql4);
		$Stmt4->bindParam(':BookQuizResultID', $BookQuizResultID);
		$Stmt4->bindParam(':BookQuizDetailID', $BookQuizDetailID);
		$Stmt4->bindParam(':BookQuizDetailQuizType', $BookQuizDetailQuizType);
		$Stmt4->bindParam(':BookQuizDetailSoundFileName', $BookQuizDetailSoundFileName);
		$Stmt4->bindParam(':BookQuizDetailSoundFileRealName', $BookQuizDetailSoundFileRealName);
		$Stmt4->bindParam(':BookQuizDetailQuestionType', $BookQuizDetailQuestionType);
		$Stmt4->bindParam(':BookQuizDetailTextQuestion', $BookQuizDetailTextQuestion);
		$Stmt4->bindParam(':BookQuizDetailVideoCode', $BookQuizDetailVideoCode);
		$Stmt4->bindParam(':BookQuizDetailAnswerType', $BookQuizDetailAnswerType);
		$Stmt4->bindParam(':BookQuizDetailChoiceImage1', $BookQuizDetailChoiceImage1);
		$Stmt4->bindParam(':BookQuizDetailChoiceImage2', $BookQuizDetailChoiceImage2);
		$Stmt4->bindParam(':BookQuizDetailChoiceImage3', $BookQuizDetailChoiceImage3);
		$Stmt4->bindParam(':BookQuizDetailChoiceImage4', $BookQuizDetailChoiceImage4);
		$Stmt4->bindParam(':BookQuizDetailText', $BookQuizDetailText);
		$Stmt4->bindParam(':BookQuizDetailImageFileName', $BookQuizDetailImageFileName);
		$Stmt4->bindParam(':BookQuizDetailImageFileRealName', $BookQuizDetailImageFileRealName);
		$Stmt4->bindParam(':BookQuizDetailChoice1', $BookQuizDetailChoice1);
		$Stmt4->bindParam(':BookQuizDetailChoice2', $BookQuizDetailChoice2);
		$Stmt4->bindParam(':BookQuizDetailChoice3', $BookQuizDetailChoice3);
		$Stmt4->bindParam(':BookQuizDetailChoice4', $BookQuizDetailChoice4);
		$Stmt4->bindParam(':BookQuizDetailCorrectAnswer', $BookQuizDetailCorrectAnswer);
		$Stmt4->bindParam(':BookQuizDetailOrder', $BookQuizDetailOrder);
		$Stmt4->execute();
		$Stmt4 = null;
		echo "Sql4";
		$ii++;
	}
	$Stmt3 = null;
}


include_once('./includes/dbclose.php');

if (!$CheckBookQuizResultID){
	header("Location: pop_quiz_study.php?BookQuizResultID=$BookQuizResultID&FromDevice=$FromDevice"); 
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
<?if($FromDevice!="app"){?>
	alert("이미 퀴즈풀이를 완료했습니다. 학습이력에서 재응시 가능합니다.");
<?}?>

<?if ($FromDevice=="app"){?>
	window.Exit=true;
<?}else{?>
	parent.$.fn.colorbox.close();
<?}?>
</script>
</body>
</html>

<?
}
?>





 