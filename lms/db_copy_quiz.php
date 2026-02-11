<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<body>
<?
$Sql = "
		select 
			A.*
		from BookQuizs A 
			where BookID=9
		order by A.BookQuizOrder asc 
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListCount = 1;
while($Row = $Stmt->fetch()) {

	$BookID = $Row["BookID"];
	$BookQuizID = $Row["BookQuizID"];
	$BookQuizName = $Row["BookQuizName"];
	$BookQuizMemo = $Row["BookQuizMemo"];
	$BookQuizView = $Row["BookQuizView"];
	$BookQuizState = $Row["BookQuizState"];

	$BookQuizName = str_replace("MES", "BTS", $BookQuizName);
	$BookQuizMemo = str_replace("MES", "BTS", $BookQuizMemo);

		$Sql2 = "select ifnull(Max(BookQuizOrder),0) as BookQuizOrder from BookQuizs";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		$Row2 = $Stmt2->fetch();
		$Stmt2 = null;
		
		$BookQuizOrder = $Row2["BookQuizOrder"]+1;

		$InsertBookID = 13;

		$Sql2 = " insert into BookQuizs ( ";
			$Sql2 .= " BookID, ";
			$Sql2 .= " BookQuizName, ";
			$Sql2 .= " BookQuizMemo, ";
			$Sql2 .= " BookQuizRegDateTime, ";
			$Sql2 .= " BookQuizModiDateTime, ";
			$Sql2 .= " BookQuizOrder, ";
			$Sql2 .= " BookQuizView, ";
			$Sql2 .= " BookQuizState ";
		$Sql2 .= " ) values ( ";
			$Sql2 .= " :BookID, ";
			$Sql2 .= " :BookQuizName, ";
			$Sql2 .= " :BookQuizMemo, ";
			$Sql2 .= " now(), ";
			$Sql2 .= " now(), ";
			$Sql2 .= " :BookQuizOrder, ";
			$Sql2 .= " :BookQuizView, ";
			$Sql2 .= " :BookQuizState ";
		$Sql2 .= " ) ";

		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':BookID', $InsertBookID);
		$Stmt2->bindParam(':BookQuizName', $BookQuizName);
		$Stmt2->bindParam(':BookQuizMemo', $BookQuizMemo);
		$Stmt2->bindParam(':BookQuizOrder', $BookQuizOrder);
		$Stmt2->bindParam(':BookQuizView', $BookQuizView);
		$Stmt2->bindParam(':BookQuizState', $BookQuizState);
		$Stmt2->execute();
		$InsertBookQuizID = $DbConn->lastInsertId();
		$Stmt2 = null;


		$Sql2 = "
				select 
					A.*
				from BookQuizDetails A 
					where BookQuizID=$BookQuizID
				order by A.BookQuizDetailOrder asc 
				";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		while($Row2 = $Stmt2->fetch()) {


			$BookQuizDetailQuizType = $Row2["BookQuizDetailQuizType"];
			$BookQuizDetailSoundFileName = $Row2["BookQuizDetailSoundFileName"];
			$BookQuizDetailSoundFileRealName = $Row2["BookQuizDetailSoundFileRealName"];
			$BookQuizDetailText = $Row2["BookQuizDetailText"];
			$BookQuizDetailQuestionType = $Row2["BookQuizDetailQuestionType"];
			$BookQuizDetailImageFileName = $Row2["BookQuizDetailImageFileName"];
			$BookQuizDetailImageFileRealName = $Row2["BookQuizDetailImageFileRealName"];
			$BookQuizDetailTextQuestion = $Row2["BookQuizDetailTextQuestion"];
			$BookQuizDetailAnswerType = $Row2["BookQuizDetailAnswerType"];
			$BookQuizDetailChoice1 = $Row2["BookQuizDetailChoice1"];
			$BookQuizDetailChoice2 = $Row2["BookQuizDetailChoice2"];
			$BookQuizDetailChoice3 = $Row2["BookQuizDetailChoice3"];
			$BookQuizDetailChoice4 = $Row2["BookQuizDetailChoice4"];
			$BookQuizDetailChoiceImage1 = $Row2["BookQuizDetailChoiceImage1"];
			$BookQuizDetailChoiceImage2 = $Row2["BookQuizDetailChoiceImage2"];
			$BookQuizDetailChoiceImage3 = $Row2["BookQuizDetailChoiceImage3"];
			$BookQuizDetailChoiceImage4 = $Row2["BookQuizDetailChoiceImage4"];
			$BookQuizDetailCorrectAnswer = $Row2["BookQuizDetailCorrectAnswer"];
			$BookQuizDetailView = $Row2["BookQuizDetailView"];
			$BookQuizDetailState = $Row2["BookQuizDetailState"];



			$Sql3 = "select ifnull(Max(BookQuizDetailOrder),0) as BookQuizDetailOrder from BookQuizDetails";
			$Stmt3 = $DbConn->prepare($Sql3);
			$Stmt3->execute();
			$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
			$Row3 = $Stmt3->fetch();
			$Stmt3 = null;
			
			$InsertBookQuizDetailOrder = $Row3["BookQuizDetailOrder"]+1;


			$Sql3 = " insert into BookQuizDetails ( ";
				$Sql3 .= " BookQuizID, ";
				$Sql3 .= " BookQuizDetailQuizType, ";
				$Sql3 .= " BookQuizDetailSoundFileName, ";
				$Sql3 .= " BookQuizDetailText, ";
				$Sql3 .= " BookQuizDetailQuestionType, ";
				$Sql3 .= " BookQuizDetailImageFileName, ";
				$Sql3 .= " BookQuizDetailImageFileRealName, ";
				$Sql3 .= " BookQuizDetailTextQuestion, ";
				$Sql3 .= " BookQuizDetailAnswerType, ";
				$Sql3 .= " BookQuizDetailChoice1, ";
				$Sql3 .= " BookQuizDetailChoice2, ";
				$Sql3 .= " BookQuizDetailChoice3, ";
				$Sql3 .= " BookQuizDetailChoice4, ";
				$Sql3 .= " BookQuizDetailChoiceImage1, ";
				$Sql3 .= " BookQuizDetailChoiceImage2, ";
				$Sql3 .= " BookQuizDetailChoiceImage3, ";
				$Sql3 .= " BookQuizDetailChoiceImage4, ";
				$Sql3 .= " BookQuizDetailCorrectAnswer, ";
				$Sql3 .= " BookQuizDetailRegDateTime, ";
				$Sql3 .= " BookQuizDetailModiDateTime, ";
				$Sql3 .= " BookQuizDetailOrder, ";
				$Sql3 .= " BookQuizDetailView, ";
				$Sql3 .= " BookQuizDetailState ";

			$Sql3 .= " ) values ( ";
				$Sql3 .= " :BookQuizID, ";
				$Sql3 .= " :BookQuizDetailQuizType, ";
				$Sql3 .= " :BookQuizDetailSoundFileName, ";
				$Sql3 .= " :BookQuizDetailText, ";
				$Sql3 .= " :BookQuizDetailQuestionType, ";
				$Sql3 .= " :BookQuizDetailImageFileName, ";
				$Sql3 .= " :BookQuizDetailImageFileRealName, ";
				$Sql3 .= " :BookQuizDetailTextQuestion, ";
				$Sql3 .= " :BookQuizDetailAnswerType, ";
				$Sql3 .= " :BookQuizDetailChoice1, ";
				$Sql3 .= " :BookQuizDetailChoice2, ";
				$Sql3 .= " :BookQuizDetailChoice3, ";
				$Sql3 .= " :BookQuizDetailChoice4, ";
				$Sql3 .= " :BookQuizDetailChoiceImage1, ";
				$Sql3 .= " :BookQuizDetailChoiceImage2, ";
				$Sql3 .= " :BookQuizDetailChoiceImage3, ";
				$Sql3 .= " :BookQuizDetailChoiceImage4, ";
				$Sql3 .= " :BookQuizDetailCorrectAnswer, ";
				$Sql3 .= " now(), ";
				$Sql3 .= " now(), ";
				$Sql3 .= " :BookQuizDetailOrder, ";
				$Sql3 .= " :BookQuizDetailView, ";
				$Sql3 .= " :BookQuizDetailState ";
			$Sql3 .= " ) ";

			$Stmt3 = $DbConn->prepare($Sql3);
			$Stmt3->bindParam(':BookQuizID', $InsertBookQuizID);
			$Stmt3->bindParam(':BookQuizDetailQuizType', $BookQuizDetailQuizType);
			$Stmt3->bindParam(':BookQuizDetailSoundFileName', $BookQuizDetailSoundFileName);
			$Stmt3->bindParam(':BookQuizDetailText', $BookQuizDetailText);
			$Stmt3->bindParam(':BookQuizDetailQuestionType', $BookQuizDetailQuestionType);
			$Stmt3->bindParam(':BookQuizDetailImageFileName', $BookQuizDetailImageFileName);
			$Stmt3->bindParam(':BookQuizDetailImageFileRealName', $BookQuizDetailImageFileRealName);
			$Stmt3->bindParam(':BookQuizDetailTextQuestion', $BookQuizDetailTextQuestion);
			$Stmt3->bindParam(':BookQuizDetailAnswerType', $BookQuizDetailAnswerType);
			$Stmt3->bindParam(':BookQuizDetailChoice1', $BookQuizDetailChoice1);
			$Stmt3->bindParam(':BookQuizDetailChoice2', $BookQuizDetailChoice2);
			$Stmt3->bindParam(':BookQuizDetailChoice3', $BookQuizDetailChoice3);
			$Stmt3->bindParam(':BookQuizDetailChoice4', $BookQuizDetailChoice4);
			$Stmt3->bindParam(':BookQuizDetailChoiceImage1', $DbMyFileName1);
			$Stmt3->bindParam(':BookQuizDetailChoiceImage2', $DbMyFileName2);
			$Stmt3->bindParam(':BookQuizDetailChoiceImage3', $DbMyFileName3);
			$Stmt3->bindParam(':BookQuizDetailChoiceImage4', $DbMyFileName4);
			$Stmt3->bindParam(':BookQuizDetailCorrectAnswer', $BookQuizDetailCorrectAnswer);
			$Stmt3->bindParam(':BookQuizDetailOrder', $InsertBookQuizDetailOrder);
			$Stmt3->bindParam(':BookQuizDetailView', $BookQuizDetailView);
			$Stmt3->bindParam(':BookQuizDetailState', $BookQuizDetailState);
			$Stmt3->execute();
			$Stmt3 = null;

		}
		$Stmt2 = null;

}
$Stmt = null;
?>

</body>
</html>
<?
include_once('../includes/dbclose.php');
?>