<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />

<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-2.2.4.min.js"></script>
<script src="js/common.js"></script>
<style> 
.endline{page-break-before:always}
</style>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<?
$MemberID = $_LINK_MEMBER_ID_;

$BookQuizResultID = isset($_REQUEST["BookQuizResultID"]) ? $_REQUEST["BookQuizResultID"] : "";
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";


$Sql2 = "select 
				ifnull((select count(*) from BookQuizResultDetails where BookQuizResultID=A.BookQuizResultID and MyScore=100),0) as CorrectCount,
				(select count(*) from BookQuizResultDetails where BookQuizResultID=A.BookQuizResultID and MyScore=0) as NotCorrectCount,
				(select avg(MyScore) from BookQuizResultDetails where BookQuizResultID=A.BookQuizResultID) as AvgMyScore
		from BookQuizResults A 
		where 
			A.BookQuizResultID=$BookQuizResultID 
		";


$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
$Row2 = $Stmt2->fetch();

$CorrectCount = $Row2["CorrectCount"];
$NotCorrectCount = $Row2["NotCorrectCount"];
$AvgMyScore = round($Row2["AvgMyScore"],0);
?>

<div class="">
	<div class="quiz_result_area">
		<h3 class="caption_underline TrnTag">퀴즈 결과</h3>
        
        <table class="quiz_result_summary">
            <colgroup span="8" width="12.5%"></colgroup>
            <tr>
                <th class="TrnTag">문제수</th>
                <td class="color_1"><?=$CorrectCount+$NotCorrectCount?></td>
                <th class="TrnTag">정답</th>
                <td class="color_1"><?=$CorrectCount?></td>
                <th class="TrnTag">오답</th>
                <td class="color_1"><?=$NotCorrectCount?></td>
                <th class="TrnTag">점수</th>
                <td class="color_2"><?=$AvgMyScore?></td>
            </tr>
        </table>

		<?
		
		// 퀴즈 결과를 순서에 맞게 가져온다.
		$Sql = "select 
					A.* 	
				from BookQuizResultDetails A
				where 
					A.BookQuizResultID=:BookQuizResultID 
				order by A.BookQuizDetailOrder asc";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':BookQuizResultID', $BookQuizResultID);
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
		$MyAnswer = $Row["MyAnswer"];
		$BookQuizDetailOrder = $Row["BookQuizDetailOrder"];


		if($BookQuizDetailChoiceImage1=="") {
			$StrBookQuizDetailChoiceImage1 = "";
		} else {
			
			$StrBookQuizDetailChoiceImage1 = "./uploads/book_quiz_images/".$BookQuizDetailChoiceImage1;
		}

		if($BookQuizDetailChoiceImage2=="") {
			$StrBookQuizDetailChoiceImage2 = "";
		} else {
			
			$StrBookQuizDetailChoiceImage2 = "./uploads/book_quiz_images/".$BookQuizDetailChoiceImage2;
		}

		if($BookQuizDetailChoiceImage3=="") {
			$StrBookQuizDetailChoiceImage3 = "";
		} else {
			
			$StrBookQuizDetailChoiceImage3 = "./uploads/book_quiz_images/".$BookQuizDetailChoiceImage3;
		}

		if($BookQuizDetailChoiceImage4=="") {
			$StrBookQuizDetailChoiceImage4 = "";
		} else {
			
			$StrBookQuizDetailChoiceImage4 = "./uploads/book_quiz_images/".$BookQuizDetailChoiceImage4;
		}

		?>
		
		<?if ($ii>1){?>
		<div class="endline"></div><br style="height:0; line-height:0">
		<?}?>
		<table class="quiz_result_table">
			<tr>
				<th class="quiz_result_qustion">
					<?if ($BookQuizDetailCorrectAnswer==$MyAnswer) { ?>
						<img src="./images/correct.png" class="img_correct">
					<? } else { ?>
						<img src="./images/incorrect.png" class="img_correct">
					<? } ?>
					<?=$ii?>. <?=$BookQuizDetailText?>
				</th>
			</tr>
			<tr>
				<td>
					<?if ($BookQuizDetailQuizType==2){?>
						<audio controls id="BookQuizDetailSoundFileName" style="height:50px;width:100%; max-width:600px; margin:10px auto 0 auto; display:block;">
							<source src="/uploads/book_quiz_audio/<?=$BookQuizDetailSoundFileName?>" type="audio/mpeg">
						</audio>
					<? } ?>
					<?if ($BookQuizDetailQuestionType==1) {?>
						<div class="quiz_img_area"><img src="./uploads/book_quiz_images/<?=$BookQuizDetailImageFileName?>"></div>
					<? } else if($BookQuizDetailQuestionType==2) { ?>
						<div class="basic_q"><?=$BookQuizDetailTextQuestion?></div>
					<? } ?>
				</td>
			</tr>
						
			<tr>
				<td>
					<?if ($BookQuizDetailAnswerType==1){?>
                        <ul class="quiz_result_text">
							<?if ($BookQuizDetailChoice1!=""){?>
							<li>1) <?=$BookQuizDetailChoice1?></li>
							<?}?>
							<?if ($BookQuizDetailChoice2!=""){?>
							<li>2) <?=$BookQuizDetailChoice2?></li>
							<?}?>
							<?if ($BookQuizDetailChoice3!=""){?>
							<li>3) <?=$BookQuizDetailChoice3?></li>
							<?}?>
							<?if ($BookQuizDetailChoice4!=""){?>
							<li>4) <?=$BookQuizDetailChoice4?></li>
							<?}?>
                        </ul>
					<? } else if($BookQuizDetailAnswerType==2) { ?>
                        <ul class="quiz_result_img">
							<?if ($BookQuizDetailChoiceImage1!=""){?>
							<li>1) <img src="<?=$StrBookQuizDetailChoiceImage1?>"></li>
							<?}?>
							<?if ($BookQuizDetailChoiceImage2!=""){?>
							<li>2) <img src="<?=$StrBookQuizDetailChoiceImage2?>"></li>
							<?}?>
							<?if ($BookQuizDetailChoiceImage3!=""){?>
							<li>3) <img src="<?=$StrBookQuizDetailChoiceImage3?>"></li>
							<?}?>
							<?if ($BookQuizDetailChoiceImage4!=""){?>
							<li>4) <img src="<?=$StrBookQuizDetailChoiceImage4?>"></li>
							<?}?>
                        </ul>
					<? } ?>
					<ul class="quiz_select">
						<li>문제의 정답 : <b class="color_1"><?=$BookQuizDetailCorrectAnswer?></b></li>
						<li>선택한 정답 : <b class="color_2"><?=$MyAnswer?></b></li>
					</ul>
				</td>
			</tr>
		</table>
		<?
			$ii++;
		}
		$Stmt = null;
		?>


		<div class="button_wrap flex_justify" id="DivBtn">
			<?if ($FromDevice=="app"){?>
			<a href="javascript:window.Exit=true;" class="button_br_black mantoman TrnTag" style="width:100%;">닫기</a>
			<?}else{?>
			<a href="javascript:PagePrint();" class="button_orange_white mantoman TrnTag">인쇄하기</a>
			<a href="javascript:parent.$.fn.colorbox.close();" class="button_br_black mantoman TrnTag">닫기</a>
			<?}?>
		</div>
	</div>
</div>


<script>
function PagePrint(){
	document.getElementById("DivBtn").style.display = "none";
	setTimeout(PagePrintAction, 1000);
}

function PagePrintAction(){
	print();
	document.getElementById("DivBtn").style.display = "";
}
</script>

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>