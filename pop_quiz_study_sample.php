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
<link href="css/leveltest_report.css" rel="stylesheet" type="text/css">
<link href="css/leveltest_red.css" rel="stylesheet" type="text/css">
<?

$err_num = 0;
$err_msg = "";

$BookQuizDetailID = 1013;

$Sql = "select 
	A.*
from BookQuizDetails A 
where A.BookQuizDetailID=".$BookQuizDetailID." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

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

if ($BookQuizDetailAnswerType==2){
	$BookQuizDetailChoice1 = $BookQuizDetailChoiceImage1;
	$BookQuizDetailChoice2 = $BookQuizDetailChoiceImage2;
	$BookQuizDetailChoice3 = $BookQuizDetailChoiceImage3;
	$BookQuizDetailChoice4 = $BookQuizDetailChoiceImage4;
}

?>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<div class="pop_quiz_wrap">
	<div class="">
		<h3 class="caption_underline" style="margin-bottom:0px;">퀴즈풀기</h3>



		<div class="t_wrap">
			
			<!------- 검사 영역 --------->
			<div class="t_content"> 
			  
				
				<div class="bottom">
					<h1 class="t_number auto_height">
						01<span></span>
					</h1>
					
					
					<div class="page"><span class="color">1 </span> / 12</div>
					
					<?if ($BookQuizDetailText!=""){?>
						<div class="basic_q" style="text-align:left;font-size:20px;"><?=$BookQuizDetailText?></div>
					<?}?>
					
					<!-- 타입이 듣기평가라면 -->
					<?if ($BookQuizDetailQuizType==2) {?>
						<audio controls id="BookQuizDetailSoundFileName" style="height:50px;width:100%;margin-top:20px;">
							<source src="/uploads/book_quiz_audio/<?=$BookQuizDetailSoundFileName?>" type="audio/mpeg">
						</audio>
					<?}?>

					<?if ($BookQuizDetailQuestionType==1){?>
						<?if ($BookQuizDetailImageFileName!=""){?>
							<div class="quiz_img_area" style="margin-top: 20px;"><img src="./uploads/book_quiz_images/<?=$BookQuizDetailImageFileName?>"></div>
						<?}?>
					<?} else {?>
						<?if ($BookQuizDetailTextQuestion!=""){?>
							<div class="basic_q"><?=$BookQuizDetailTextQuestion?></div>
						<?}?>
					<? } ?>


					
					
						<?if ($BookQuizDetailAnswerType==1){?>
						<ul class="choose_0">
							<?if (trim($BookQuizDetailChoice1)!=""){?>
								<li><a id="Choice_1" href="javascript:SelectAnswer(0,1);"><span>A</span> <?=$BookQuizDetailChoice1?></a></li>
							<?}?>
							<?if (trim($BookQuizDetailChoice2)!=""){?>
								<li><a id="Choice_2" href="javascript:SelectAnswer(0,2);"><span>B</span> <?=$BookQuizDetailChoice2?></a></li>
							<?}?>
							<?if (trim($BookQuizDetailChoice3)!=""){?>
								<li><a id="Choice_3" href="javascript:SelectAnswer(0,3);"><span>C</span> <?=$BookQuizDetailChoice3?></a></li>
							<?}?>
							<?if (trim($BookQuizDetailChoice4)!=""){?>
								<li><a id="Choice_4" href="javascript:SelectAnswer(0,4);"><span>D</span> <?=$BookQuizDetailChoice4?></a></li>
							<?}?>
							<li></li>
						</ul>
						<? } else { ?>
						<ul class="choose_0 images">
							<?if (trim($StrBookQuizDetailChoiceImage1)!=""){?>
								<li><a id="Choice_1" href="javascript:SelectAnswer(0,1);"><span>A</span> <img src="<?=$StrBookQuizDetailChoiceImage1?>" class="choose_img"> </a></li>

							<?}?>

							<?if (trim($StrBookQuizDetailChoiceImage2)!=""){?>
								<li><a id="Choice_2" href="javascript:SelectAnswer(0,2);"><span>B</span> <img src="<?=$StrBookQuizDetailChoiceImage2?>" class="choose_img"> </a></li>
							<?}?>

							<?if (trim($StrBookQuizDetailChoiceImage3)!=""){?>
								<li><a id="Choice_3" href="javascript:SelectAnswer(0,3);"><span>C</span> <img src="<?=$StrBookQuizDetailChoiceImage3?>" class="choose_img"></a></li>
							<?}?>

							<?if (trim($StrBookQuizDetailChoiceImage4)!=""){?>
								<li><a id="Choice_4" href="javascript:SelectAnswer(0,4);"><span>D</span> <img src="<?=$StrBookQuizDetailChoiceImage4?>" class="choose_img"></a></li>
							<?}?>
							<li></li>
						</ul>
						<? } ?>


			
				</div>  
			</div>     


			<div class="text_center">
					<a href="javascript:parent.$.fn.colorbox.close();" class="button_orange_white mantoman">닫기</a>
			</div>

        
		</div>

		

	</div>
</div>



<script>
function SelectAnswer(BookQuizResultDetailID,MyAnswer){

	<?if (trim($BookQuizDetailChoice1)!=""){?>
		document.getElementById("Choice_1").className = "";
	<?}?>
	<?if (trim($BookQuizDetailChoice2)!=""){?>
		document.getElementById("Choice_2").className = "";
	<?}?>
	<?if (trim($BookQuizDetailChoice3)!=""){?>
		document.getElementById("Choice_3").className = "";
	<?}?>
	<?if (trim($BookQuizDetailChoice4)!=""){?>
		document.getElementById("Choice_4").className = "";
	<?}?>
	
	document.getElementById("Choice_"+MyAnswer).className = "active";

	document.getElementById("MyAnswer").value = MyAnswer;
}
</script>




</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>