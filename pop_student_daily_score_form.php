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

<body>
<?
include_once('./includes/common_body_top.php');
?>
<?
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";


$Sql = "select * from AssmtStudentDailyScores where ClassID=:ClassID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

if ($Row["AssmtStudentDailyScoreID"]) {
	$AssmtStudentDailyScoreID = $Row["AssmtStudentDailyScoreID"];
	$AssmtStudentDailyScore1 = $Row["AssmtStudentDailyScore1"];
	$AssmtStudentDailyScore2 = $Row["AssmtStudentDailyScore2"];
	$AssmtStudentDailyScore3 = $Row["AssmtStudentDailyScore3"];
	$AssmtStudentDailyScore4 = $Row["AssmtStudentDailyScore4"];
	$AssmtStudentDailyScore5 = $Row["AssmtStudentDailyScore5"];
	$AssmtStudentDailyComment = $Row["AssmtStudentDailyComment"];
}else{
	$AssmtStudentDailyScoreID = "";
	$AssmtStudentDailyScore1 = "";
	$AssmtStudentDailyScore2 = "";
	$AssmtStudentDailyScore3 = "";
	$AssmtStudentDailyScore4 = "";
	$AssmtStudentDailyScore5 = "";
	$AssmtStudentDailyComment = "";
}

?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area" style="width:90%;">
		<h3 class="caption_underline TrnTag">학생 평가</h3>

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="AssmtStudentDailyScoreID" value="<?=$AssmtStudentDailyScoreID?>">
		<input type="hidden" name="ClassID" value="<?=$ClassID?>">
		<input type="hidden" name="FromDevice" value="<?=$FromDevice?>">
		
		<table class="level_reserve_table">
			<tr>
				<th style="padding:10px;">Pronunciation</th>
				<td class="radio_wrap time">
					<input type="radio" id="AssmtStudentDailyScore1_1" class="input_radio" name="AssmtStudentDailyScore1" value="2" <?if ($AssmtStudentDailyScore1==2) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore1_1"><span class="bullet_radio"></span>2점</label>
					<input type="radio" id="AssmtStudentDailyScore1_2" class="input_radio" name="AssmtStudentDailyScore1" value="4" <?if ($AssmtStudentDailyScore1==4) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore1_2"><span class="bullet_radio"></span>4점</label>
					<input type="radio" id="AssmtStudentDailyScore1_3" class="input_radio" name="AssmtStudentDailyScore1" value="6" <?if ($AssmtStudentDailyScore1==6) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore1_3"><span class="bullet_radio"></span>6점</label>
					<input type="radio" id="AssmtStudentDailyScore1_4" class="input_radio" name="AssmtStudentDailyScore1" value="8" <?if ($AssmtStudentDailyScore1==8) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore1_4"><span class="bullet_radio"></span>8점</label>
					<input type="radio" id="AssmtStudentDailyScore1_5" class="input_radio" name="AssmtStudentDailyScore1" value="10" <?if ($AssmtStudentDailyScore1==10) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore1_5"><span class="bullet_radio"></span>10점</label>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Grammar</th>
				<td class="radio_wrap time">
					<input type="radio" id="AssmtStudentDailyScore2_1" class="input_radio" name="AssmtStudentDailyScore2" value="2" <?if ($AssmtStudentDailyScore2==2) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore2_1"><span class="bullet_radio"></span>2점</label>
					<input type="radio" id="AssmtStudentDailyScore2_2" class="input_radio" name="AssmtStudentDailyScore2" value="4" <?if ($AssmtStudentDailyScore2==4) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore2_2"><span class="bullet_radio"></span>4점</label>
					<input type="radio" id="AssmtStudentDailyScore2_3" class="input_radio" name="AssmtStudentDailyScore2" value="6" <?if ($AssmtStudentDailyScore2==6) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore2_3"><span class="bullet_radio"></span>6점</label>
					<input type="radio" id="AssmtStudentDailyScore2_4" class="input_radio" name="AssmtStudentDailyScore2" value="8" <?if ($AssmtStudentDailyScore2==8) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore2_4"><span class="bullet_radio"></span>8점</label>
					<input type="radio" id="AssmtStudentDailyScore2_5" class="input_radio" name="AssmtStudentDailyScore2" value="10" <?if ($AssmtStudentDailyScore2==10) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore2_5"><span class="bullet_radio"></span>10점</label>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Vocabulary</th>
				<td class="radio_wrap time">
					<input type="radio" id="AssmtStudentDailyScore3_1" class="input_radio" name="AssmtStudentDailyScore3" value="2" <?if ($AssmtStudentDailyScore3==2) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore3_1"><span class="bullet_radio"></span>2점</label>
					<input type="radio" id="AssmtStudentDailyScore3_2" class="input_radio" name="AssmtStudentDailyScore3" value="4" <?if ($AssmtStudentDailyScore3==4) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore3_2"><span class="bullet_radio"></span>4점</label>
					<input type="radio" id="AssmtStudentDailyScore3_3" class="input_radio" name="AssmtStudentDailyScore3" value="6" <?if ($AssmtStudentDailyScore3==6) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore3_3"><span class="bullet_radio"></span>6점</label>
					<input type="radio" id="AssmtStudentDailyScore3_4" class="input_radio" name="AssmtStudentDailyScore3" value="8" <?if ($AssmtStudentDailyScore3==8) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore3_4"><span class="bullet_radio"></span>8점</label>
					<input type="radio" id="AssmtStudentDailyScore3_5" class="input_radio" name="AssmtStudentDailyScore3" value="10" <?if ($AssmtStudentDailyScore3==10) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore3_5"><span class="bullet_radio"></span>10점</label>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Attitude</th>
				<td class="radio_wrap time">
					<input type="radio" id="AssmtStudentDailyScore4_1" class="input_radio" name="AssmtStudentDailyScore4" value="2" <?if ($AssmtStudentDailyScore4==2) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore4_1"><span class="bullet_radio"></span>2점</label>
					<input type="radio" id="AssmtStudentDailyScore4_2" class="input_radio" name="AssmtStudentDailyScore4" value="4" <?if ($AssmtStudentDailyScore4==4) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore4_2"><span class="bullet_radio"></span>4점</label>
					<input type="radio" id="AssmtStudentDailyScore4_3" class="input_radio" name="AssmtStudentDailyScore4" value="6" <?if ($AssmtStudentDailyScore4==6) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore4_3"><span class="bullet_radio"></span>6점</label>
					<input type="radio" id="AssmtStudentDailyScore4_4" class="input_radio" name="AssmtStudentDailyScore4" value="8" <?if ($AssmtStudentDailyScore4==8) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore4_4"><span class="bullet_radio"></span>8점</label>
					<input type="radio" id="AssmtStudentDailyScore4_5" class="input_radio" name="AssmtStudentDailyScore4" value="10" <?if ($AssmtStudentDailyScore4==10) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore4_5"><span class="bullet_radio"></span>10점</label>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Fluency</th>
				<td class="radio_wrap time">
					<input type="radio" id="AssmtStudentDailyScore5_1" class="input_radio" name="AssmtStudentDailyScore5" value="2" <?if ($AssmtStudentDailyScore5==2) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore5_1"><span class="bullet_radio"></span>2점</label>
					<input type="radio" id="AssmtStudentDailyScore5_2" class="input_radio" name="AssmtStudentDailyScore5" value="4" <?if ($AssmtStudentDailyScore5==4) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore5_2"><span class="bullet_radio"></span>4점</label>
					<input type="radio" id="AssmtStudentDailyScore5_3" class="input_radio" name="AssmtStudentDailyScore5" value="6" <?if ($AssmtStudentDailyScore5==6) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore5_3"><span class="bullet_radio"></span>6점</label>
					<input type="radio" id="AssmtStudentDailyScore5_4" class="input_radio" name="AssmtStudentDailyScore5" value="8" <?if ($AssmtStudentDailyScore5==8) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore5_4"><span class="bullet_radio"></span>8점</label>
					<input type="radio" id="AssmtStudentDailyScore5_5" class="input_radio" name="AssmtStudentDailyScore5" value="10" <?if ($AssmtStudentDailyScore5==10) {?>checked<?}?>><label class="label" for="AssmtStudentDailyScore5_5"><span class="bullet_radio"></span>10점</label>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Comment</th>
				<td class="radio_wrap time" style="padding:10px;">
					<textarea name="AssmtStudentDailyComment" id="AssmtStudentDailyComment" style="width:100%;"><?=$AssmtStudentDailyComment?></textarea>
				</td>
			</tr>
		</table>
		</form>
		<div class="button_wrap flex_justify">
			<a href="javascript:FormSubmit();" class="button_orange_white mantoman TrnTag">평가완료</a>
			<a href="javascript:CloseForm();" class="button_br_black mantoman TrnTag">취소하기</a>
		</div>
	</div>
</div>


<script language="javascript">
function FormSubmit(){

	CheckOk = 1;
	
	if (document.RegForm.AssmtStudentDailyScore1[0].checked==false && document.RegForm.AssmtStudentDailyScore1[1].checked==false && document.RegForm.AssmtStudentDailyScore1[2].checked==false && document.RegForm.AssmtStudentDailyScore1[3].checked==false && document.RegForm.AssmtStudentDailyScore1[4].checked==false){
		CheckOk = 0;
	}

	if (document.RegForm.AssmtStudentDailyScore2[0].checked==false && document.RegForm.AssmtStudentDailyScore2[1].checked==false && document.RegForm.AssmtStudentDailyScore2[2].checked==false && document.RegForm.AssmtStudentDailyScore2[3].checked==false && document.RegForm.AssmtStudentDailyScore2[4].checked==false){
		CheckOk = 0;
	}

	if (document.RegForm.AssmtStudentDailyScore3[0].checked==false && document.RegForm.AssmtStudentDailyScore3[1].checked==false && document.RegForm.AssmtStudentDailyScore3[2].checked==false && document.RegForm.AssmtStudentDailyScore3[3].checked==false && document.RegForm.AssmtStudentDailyScore3[4].checked==false){
		CheckOk = 0;
	}

	if (document.RegForm.AssmtStudentDailyScore4[0].checked==false && document.RegForm.AssmtStudentDailyScore4[1].checked==false && document.RegForm.AssmtStudentDailyScore4[2].checked==false && document.RegForm.AssmtStudentDailyScore4[3].checked==false && document.RegForm.AssmtStudentDailyScore4[4].checked==false){
		CheckOk = 0;
	}

	if (document.RegForm.AssmtStudentDailyScore5[0].checked==false && document.RegForm.AssmtStudentDailyScore5[1].checked==false && document.RegForm.AssmtStudentDailyScore5[2].checked==false && document.RegForm.AssmtStudentDailyScore5[3].checked==false && document.RegForm.AssmtStudentDailyScore5[4].checked==false){
		CheckOk = 0;
	}


	
	if (CheckOk==1){

		AlertMsg = "평가를 완료 하시겠습니까?";

		if (confirm(AlertMsg)){
			document.RegForm.action = "pop_student_daily_score_action.php"
			document.RegForm.submit();
		}
	
	}else{
		alert("모든 항목을 평가해 주세요.");
	}
}


function CloseForm(){
	<?if ($FromDevice==""){?>
	parent.$.fn.colorbox.close();
	<?}else{?>
	window.Exit=true;
	<?}?>
}

</script>





</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>