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

$BookQuizResultID = isset($_REQUEST["BookQuizResultID"]) ? $_REQUEST["BookQuizResultID"] : "";
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";
$Page = isset($_REQUEST["Page"]) ? $_REQUEST["Page"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";



if ($Page!=""){
	$BookQuizCurrentPage = $Page;
}else{
	$Sql = "select 
		A.*
	from BookQuizResults A 
	where A.BookQuizResultID=".$BookQuizResultID." ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$BookQuizCurrentPage = $Row["BookQuizCurrentPage"];
}


$Sql = "select 
	count(*) as QuestionCount
from BookQuizResultDetails A 
where A.BookQuizResultID=".$BookQuizResultID." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$QuestionCount = $Row["QuestionCount"];

$Sql = "select 
	A.*
from BookQuizResultDetails A 
where A.BookQuizResultID=".$BookQuizResultID." and A.BookQuizDetailOrder=$BookQuizCurrentPage ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookQuizDetailVideoCode = $Row["BookQuizDetailVideoCode"];
$BookQuizResultID = $Row["BookQuizResultID"];
$BookQuizDetailID = $Row["BookQuizDetailID"];
$BookQuizResultDetailID = $Row["BookQuizResultDetailID"];
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
$BookQuizDetailOrder = $Row["BookQuizDetailOrder"];
$MyAnswer = $Row["MyAnswer"];
$MyScore = $Row["MyScore"];


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
		<h3 class="caption_underline TrnTag" style="margin-bottom:0px;">퀴즈풀기</h3>



		<div class="t_wrap">
			
			<!------- 검사 영역 --------->
			<div class="t_content"> 
			  
				
				<div class="bottom">
					<h1 class="t_number auto_height">
						<?=substr("0".$BookQuizDetailOrder,-2)?><span></span>
					</h1>
					
					<input type="hidden" name="MyAnswer" id="MyAnswer" value="<?=$MyAnswer?>">
					
					<div class="page"><span class="color"><?=$BookQuizDetailOrder?> </span> / <?=$QuestionCount?></div>
					
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
							<div class="quiz_img_area" style="margin-top: 20px;"><img style="max-width: 100%;" src="./uploads/book_quiz_images/<?=$BookQuizDetailImageFileName?>"></div>
						<?}?>
					<?} else if($BookQuizDetailQuestionType==2) {?>
						<?if ($BookQuizDetailTextQuestion!=""){?>
							<div class="basic_q"><?=$BookQuizDetailTextQuestion?></div>
						<?}?>
					<? } else if($BookQuizDetailQuestionType==4) {?>
						<div class="basic_q">
							<iframe id="YoutubePlayer" width="100%;" height="360" src="https://www.youtube.com/embed/<?=$BookQuizDetailVideoCode?>?autoplay=1" frameborder="0" allowfullscreen></iframe>
						</div>
					<? } ?>


					
					
						<?if ($BookQuizDetailAnswerType==1){?>
						<ul class="choose_0">
							<?if (trim($BookQuizDetailChoice1)!=""){?>
								<li><a id="Choice_1" href="javascript:SelectAnswer(<?=$BookQuizResultDetailID?>,1);" <?if (trim($MyAnswer)=="1"){?>class="active"<?}?>><span>1</span> <?=$BookQuizDetailChoice1?></a></li>
							<?}?>
							<?if (trim($BookQuizDetailChoice2)!=""){?>
								<li><a id="Choice_2" href="javascript:SelectAnswer(<?=$BookQuizResultDetailID?>,2);" <?if (trim($MyAnswer)=="2"){?>class="active"<?}?>><span>2</span> <?=$BookQuizDetailChoice2?></a></li>
							<?}?>
							<?if (trim($BookQuizDetailChoice3)!=""){?>
								<li><a id="Choice_3" href="javascript:SelectAnswer(<?=$BookQuizResultDetailID?>,3);" <?if (trim($MyAnswer)=="3"){?>class="active"<?}?>><span>3</span> <?=$BookQuizDetailChoice3?></a></li>
							<?}?>
							<?if (trim($BookQuizDetailChoice4)!=""){?>
								<li><a id="Choice_4" href="javascript:SelectAnswer(<?=$BookQuizResultDetailID?>,4);" <?if (trim($MyAnswer)=="4"){?>class="active"<?}?>><span>4</span> <?=$BookQuizDetailChoice4?></a></li>
							<?}?>
							<li></li>
						</ul>
						<? } else { ?>
						<ul class="choose_0 images">
							<?if (trim($StrBookQuizDetailChoiceImage1)!=""){?>
								<li><a id="Choice_1" href="javascript:SelectAnswer(<?=$BookQuizResultDetailID?>,1);" <?if (trim($MyAnswer)=="1"){?>class="active"<?}?>><span>1</span> <img src="<?=$StrBookQuizDetailChoiceImage1?>" class="choose_img"> </a></li>

							<?}?>

							<?if (trim($StrBookQuizDetailChoiceImage2)!=""){?>
								<li><a id="Choice_2" href="javascript:SelectAnswer(<?=$BookQuizResultDetailID?>,2);" <?if (trim($MyAnswer)=="2"){?>class="active"<?}?>><span>2</span> <img src="<?=$StrBookQuizDetailChoiceImage2?>" class="choose_img"> </a></li>
							<?}?>

							<?if (trim($StrBookQuizDetailChoiceImage3)!=""){?>
								<li><a id="Choice_3" href="javascript:SelectAnswer(<?=$BookQuizResultDetailID?>,3);" <?if (trim($MyAnswer)=="3"){?>class="active"<?}?>><span>3</span> <img src="<?=$StrBookQuizDetailChoiceImage3?>" class="choose_img"></a></li>
							<?}?>

							<?if (trim($StrBookQuizDetailChoiceImage4)!=""){?>
								<li><a id="Choice_4" href="javascript:SelectAnswer(<?=$BookQuizResultDetailID?>,4);" <?if (trim($MyAnswer)=="4"){?>class="active"<?}?>><span>4</span> <img src="<?=$StrBookQuizDetailChoiceImage4?>" class="choose_img"></a></li>
							<?}?>
							<li></li>
						</ul>
						<? } ?>


			
				</div>  
			</div>     


			<?if ($BookQuizCurrentPage==1 && $QuestionCount>1) {?>
				<div class="text_center">
					<a href="javascript:GoNextPage(<?=$BookQuizResultID?>,<?=$BookQuizCurrentPage+1?>);" class="button_orange_white mantoman TrnTag">다음</a>
				</div>
				<?if ($FromDevice=="app"){?>
				<!--
				<div class="text_center" style="margin-top:20px;">
						<a href="javascript:CloseWindow();" class="button_br_black mantoman TrnTag" style="width:100%;">닫기</a>
				</div>
				-->
				<?}?>
			<?}else if ($BookQuizCurrentPage==$QuestionCount){?>
				<div class="flex_justify">
					<a href="javascript:GoPrevPage(<?=$BookQuizResultID?>,<?=$BookQuizCurrentPage-1?>);" class="button_br_black mantoman TrnTag">이전</a> 
					<a href="javascript:GoEndPage(<?=$BookQuizResultID?>);" class="button_orange_white mantoman TrnTag">종료</a>
				</div> 
				<?if ($FromDevice=="app"){?>
				<!--
				<div class="text_center" style="margin-top:20px;">
						<a href="javascript:CloseWindow();" class="button_br_black mantoman TrnTag" style="width:100%;">닫기</a>
				</div>
				-->
				<?}?>
			<?}else{?>
				<div class="flex_justify">
					<a href="javascript:GoPrevPage(<?=$BookQuizResultID?>,<?=$BookQuizCurrentPage-1?>);" class="button_br_black mantoman TrnTag">이전</a> 
					<a href="javascript:GoNextPage(<?=$BookQuizResultID?>,<?=$BookQuizCurrentPage+1?>);" class="button_orange_white mantoman TrnTag">다음</a>
				</div> 
				<?if ($FromDevice=="app"){?>
				<!--
				<div class="text_center" style="margin-top:20px;">
						<a href="javascript:CloseWindow();" class="button_br_black mantoman TrnTag" style="width:100%;">닫기</a>
				</div>
				-->
				<?}?>
			<?}?>

        
		</div>

		

	</div>
</div>



<script>
function CloseWindow() {
	AnswerSave(LocationHref);
	window.Exit=true;
}

function GoNextPage(BookQuizResultID,Page){
	MyAnswer = document.getElementById("MyAnswer").value;
	if (MyAnswer=="" || MyAnswer=="0"){
		alert("답안을 선택해 주세요.");
	}else{
		LocationHref = "pop_quiz_study.php?BookQuizResultID="+BookQuizResultID+"&Page="+Page+"&FromDevice=<?=$FromDevice?>";
		AnswerSave(LocationHref);
	}
}

function GoPrevPage(BookQuizResultID,Page){
	location.href = "pop_quiz_study.php?BookQuizResultID="+BookQuizResultID+"&Page="+Page+"&FromDevice=<?=$FromDevice?>";
}

function GoEndPage(BookQuizResultID){
	MyAnswer = document.getElementById("MyAnswer").value;
	if (MyAnswer=="" || MyAnswer=="0"){
		alert("답안을 선택해 주세요.");
	}else{
		if (confirm("종료 하시겠습니까?")){
			LocationHref = "pop_quiz_study_end.php?BookQuizResultID="+BookQuizResultID+"&FromDevice=<?=$FromDevice?>&MemberID=<?=$MemberID?>";
			AnswerSave(LocationHref);
		}
		
	}
}

function AnswerSave(LocationHref){
	
	MyAnswer = document.getElementById("MyAnswer").value;
	url = "./ajax_set_quiz_study_answer.php";

	//window.open(url + "?BookQuizResultDetailID=<?=$BookQuizResultDetailID?>&MyAnswer="+MyAnswer);
	$.ajax(url, {
		data: {
			BookQuizResultDetailID: <?=$BookQuizResultDetailID?>,
			MyAnswer: MyAnswer
		},
		success: function (data) {
			location.href = LocationHref;
		},
		error: function () {
			//alert('Error while contacting server, please try again');
		}
	});		
}

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