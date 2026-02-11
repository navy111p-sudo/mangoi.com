<?
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$LocalLinkMemberID = isset($_REQUEST["LocalLinkMemberID"]) ? $_REQUEST["LocalLinkMemberID"] : "";
$MemberID = $LocalLinkMemberID;

$Sql = "
	select 
		A.ClassID 
	from Classes A 
		inner join Teachers B on A.TeacherID=B.TeacherID 
	where 
		A.MemberID=$MemberID and A.BookQuizID!=0 
		and (A.ClassState=2 or datediff(A.StartDateTime, now())<=0) 
	order by A.ClassID desc
	limit 0,1
";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();

$ClassID = $Row["ClassID"];

if($ClassID) {
	$Sql2 = "
		select 
			A.*
		from BookQuizResults A 
		where 
			A.ClassID=$ClassID
		order by A.QuizStudyNumber desc
		limit 0,1
	";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

	$QuizStudyNumber=0;
	$BookQuizResultState = 1;
	$BookQuizID = 0;
	$Row2 = $Stmt2->fetch();

	$BookQuizResultID = $Row2["BookQuizResultID"];
	$QuizStudyNumber = $Row2["QuizStudyNumber"];
	$BookQuizResultState = $Row2["BookQuizResultState"];
	$BookQuizID = $Row2["BookQuizID"];
	$FromDevice = "app";
} else {
	$BookQuizResultID = 0;
	$BookQuizID = 0;
	$BookQuizResultState = 0;
}

//  /*
// 결과 가 있다면 
if($BookQuizResultState==2) {
		$QuizStudyNumber = $QuizStudyNumber+1;
		header("Location: ./pop_quiz_study_preset.php?BookQuizID=$BookQuizID&ClassID=$ClassID&QuizStudyNumber=$QuizStudyNumber&FromDevice=$FromDevice");
} else {
	if($BookQuizResultState==1) {
		header("Location: pop_quiz_study.php?BookQuizResultID=$BookQuizResultID&FromDevice=$FromDevice&MemberID=$MemberID"); 
		//header("Location: ./pop_quiz_study_preset.php?BookQuizID=$BookQuizID&ClassID=$ClassID&QuizStudyNumber=$QuizStudyNumber&FromDevice=$FromDevice");
	} else {
		echo "<script>window.Exit=true;alert('수업이 없습니다.'); </script>";
	}
}
// */


?>