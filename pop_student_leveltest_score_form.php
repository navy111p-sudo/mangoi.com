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



$Sql = "select * from AssmtStudentLeveltestScores where ClassID=:ClassID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

if ($Row["AssmtStudentLeveltestScoreID"]) {
	$AssmtStudentLeveltestScoreID = $Row["AssmtStudentLeveltestScoreID"];

	$AssmtStudentLeveltestScoreYear = $Row["AssmtStudentLeveltestScoreYear"];
	$AssmtStudentLeveltestScoreMonth = $Row["AssmtStudentLeveltestScoreMonth"];
	$AssmtStudentLeveltestScoreDay = $Row["AssmtStudentLeveltestScoreDay"];
	$AssmtStudentLeveltestScoreLevel = $Row["AssmtStudentLeveltestScoreLevel"];

	$AssmtStudentLeveltestPass = $Row["AssmtStudentLeveltestPass"];
	$AssmtStudentLeveltestScore1 = $Row["AssmtStudentLeveltestScore1"];
	$AssmtStudentLeveltestScore2 = $Row["AssmtStudentLeveltestScore2"];
	$AssmtStudentLeveltestScore3 = $Row["AssmtStudentLeveltestScore3"];
	$AssmtStudentLeveltestScore4 = $Row["AssmtStudentLeveltestScore4"];
	$AssmtStudentLeveltestScore5 = $Row["AssmtStudentLeveltestScore5"];

	$AssmtStudentLeveltestScoreComment1 = $Row["AssmtStudentLeveltestScoreComment1"];
	$AssmtStudentLeveltestScoreComment2 = $Row["AssmtStudentLeveltestScoreComment2"];
	$AssmtStudentLeveltestScoreComment3 = $Row["AssmtStudentLeveltestScoreComment3"];
	$AssmtStudentLeveltestScoreComment4 = $Row["AssmtStudentLeveltestScoreComment4"];
	$AssmtStudentLeveltestScoreComment5 = $Row["AssmtStudentLeveltestScoreComment5"];

	$AssmtStudentLeveltestScoreCommentTotal = $Row["AssmtStudentLeveltestScoreCommentTotal"];

}else{

	$Sql = "
		select 
			A.*,
			B.ClassOrderLeveltestApplyLevel
		from Classes A
			inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID
		where 
			A.ClassID=$ClassID
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$StartYear = $Row["StartYear"];
	$StartMonth = $Row["StartMonth"];
	$StartDay = $Row["StartDay"];
	$ClassOrderLeveltestApplyLevel = $Row["ClassOrderLeveltestApplyLevel"];

	$AssmtStudentLeveltestScoreYear = $StartYear;
	$AssmtStudentLeveltestScoreMonth = $StartMonth;
	$AssmtStudentLeveltestScoreDay = $StartDay;
	$AssmtStudentLeveltestScoreLevel = $ClassOrderLeveltestApplyLevel;


	$AssmtStudentLeveltestScoreID = "";

	$AssmtStudentLeveltestPass = 1;
	$AssmtStudentLeveltestScore1 = "";
	$AssmtStudentLeveltestScore2 = "";
	$AssmtStudentLeveltestScore3 = "";
	$AssmtStudentLeveltestScore4 = "";
	$AssmtStudentLeveltestScore5 = "";

	$AssmtStudentLeveltestScoreComment1 = "";
	$AssmtStudentLeveltestScoreComment2 = "";
	$AssmtStudentLeveltestScoreComment3 = "";
	$AssmtStudentLeveltestScoreComment4 = "";
	$AssmtStudentLeveltestScoreComment5 = "";

	$AssmtStudentLeveltestScoreCommentTotal = "";
}
?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area" style="width:90%;">
		<h3 class="caption_underline TrnTag">레벨테스트 평가</h3>

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="AssmtStudentLeveltestScoreID" value="<?=$AssmtStudentLeveltestScoreID?>">
		<input type="hidden" name="ClassID" value="<?=$ClassID?>">
		<input type="hidden" name="AssmtStudentLeveltestScoreYear" value="<?=$AssmtStudentLeveltestScoreYear?>">
		<input type="hidden" name="AssmtStudentLeveltestScoreMonth" value="<?=$AssmtStudentLeveltestScoreMonth?>">
		<input type="hidden" name="AssmtStudentLeveltestScoreDay" value="<?=$AssmtStudentLeveltestScoreDay?>">
		<table class="level_reserve_table">
			<tr>
				<th style="padding:10px;">Date</th>
				<td class="radio_wrap time" style="padding:10px;">
					<?=$AssmtStudentLeveltestScoreYear?>. <?=substr("0".$AssmtStudentLeveltestScoreMonth,-2)?>. <?=substr("0".$AssmtStudentLeveltestScoreDay,-2)?>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">LEVEL</th>
				<td class="radio_wrap time" style="padding:10px;">
					<select name="AssmtStudentLeveltestScoreLevel" style="width:120px;height:34px;">
						<option value="1" <?if ($AssmtStudentLeveltestScoreLevel==1){?>selected<?}?>>1 LEVEL</option>
						<option value="2" <?if ($AssmtStudentLeveltestScoreLevel==2){?>selected<?}?>>2 LEVEL</option>
						<option value="3" <?if ($AssmtStudentLeveltestScoreLevel==3){?>selected<?}?>>3 LEVEL</option>
						<option value="4" <?if ($AssmtStudentLeveltestScoreLevel==4){?>selected<?}?>>4 LEVEL</option>
						<option value="5" <?if ($AssmtStudentLeveltestScoreLevel==5){?>selected<?}?>>5 LEVEL</option>
						<option value="6" <?if ($AssmtStudentLeveltestScoreLevel==6){?>selected<?}?>>6 LEVEL</option>
						<option value="7" <?if ($AssmtStudentLeveltestScoreLevel==7){?>selected<?}?>>7 LEVEL</option>
						<option value="8" <?if ($AssmtStudentLeveltestScoreLevel==8){?>selected<?}?>>8 LEVEL</option>
					</select>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">PASS / FAIL</th>
				<td class="radio_wrap time" style="padding:10px;">
					<select name="AssmtStudentLeveltestPass" style="width:120px;height:34px;">
						<option value="1" <?if ($AssmtStudentLeveltestPass==1){?>selected<?}?>>PASS</option>
						<option value="0" <?if ($AssmtStudentLeveltestPass==0){?>selected<?}?>>FAIL</option>
					</select>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Pronunciation</th>
				<td class="radio_wrap time">
					<div style="text-align:right;">
						<div style="display:inline-block:height:30px;line-height:30px;background-color:#f1f1f1;text-align:center;margin-bottom:5px;cursor:pointer;" onclick="OpenSampleComment(1)">코멘트 Comment</div>
					</div>
					<textarea name="AssmtStudentMonthlyScoreComment1" style="width:100%;height:120px;"><?=$AssmtStudentLeveltestScoreComment1?></textarea>

					SCORE &nbsp;<select name="AssmtStudentLeveltestScore1" style="width:120px;height:34px;">
						<option value="10" <?if ($AssmtStudentLeveltestScore1==10){?>selected<?}?>>10</option>
						<option value="8" <?if ($AssmtStudentLeveltestScore1==8){?>selected<?}?>>8</option>
						<option value="6" <?if ($AssmtStudentLeveltestScore1==6){?>selected<?}?>>6</option>
						<option value="4" <?if ($AssmtStudentLeveltestScore1==4){?>selected<?}?>>4</option>
						<option value="2" <?if ($AssmtStudentLeveltestScore1==2){?>selected<?}?>>2</option>
					</select>

				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Grammar</th>
				<td class="radio_wrap time">
					<div style="text-align:right;">
						<div style="display:inline-block:height:30px;line-height:30px;background-color:#f1f1f1;text-align:center;margin-bottom:5px;cursor:pointer;" onclick="OpenSampleComment(2)">코멘트 Comment</div>
					</div>
					<textarea name="AssmtStudentMonthlyScoreComment2" style="width:100%;height:120px;"><?=$AssmtStudentLeveltestScoreComment2?></textarea>

					SCORE &nbsp;<select name="AssmtStudentLeveltestScore2" style="width:120px;height:34px;">
						<option value="10" <?if ($AssmtStudentLeveltestScore2==10){?>selected<?}?>>10</option>
						<option value="8" <?if ($AssmtStudentLeveltestScore2==8){?>selected<?}?>>8</option>
						<option value="6" <?if ($AssmtStudentLeveltestScore2==6){?>selected<?}?>>6</option>
						<option value="4" <?if ($AssmtStudentLeveltestScore2==4){?>selected<?}?>>4</option>
						<option value="2" <?if ($AssmtStudentLeveltestScore2==2){?>selected<?}?>>2</option>
					</select>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Vocabulary</th>
				<td class="radio_wrap time">
					<div style="text-align:right;">
						<div style="display:inline-block:height:30px;line-height:30px;background-color:#f1f1f1;text-align:center;margin-bottom:5px;cursor:pointer;" onclick="OpenSampleComment(3)">코멘트 Comment</div>
					</div>
					<textarea name="AssmtStudentMonthlyScoreComment3" style="width:100%;height:120px;"><?=$AssmtStudentLeveltestScoreComment3?></textarea>

					SCORE &nbsp;<select name="AssmtStudentLeveltestScore3" style="width:120px;height:34px;">
						<option value="10" <?if ($AssmtStudentLeveltestScore3==10){?>selected<?}?>>10</option>
						<option value="8" <?if ($AssmtStudentLeveltestScore3==8){?>selected<?}?>>8</option>
						<option value="6" <?if ($AssmtStudentLeveltestScore3==6){?>selected<?}?>>6</option>
						<option value="4" <?if ($AssmtStudentLeveltestScore3==4){?>selected<?}?>>4</option>
						<option value="2" <?if ($AssmtStudentLeveltestScore3==2){?>selected<?}?>>2</option>
					</select>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Attitude</th>
				<td class="radio_wrap time">
					<div style="text-align:right;">
						<div style="display:inline-block:height:30px;line-height:30px;background-color:#f1f1f1;text-align:center;margin-bottom:5px;cursor:pointer;" onclick="OpenSampleComment(4)">코멘트 Comment</div>
					</div>
					<textarea name="AssmtStudentMonthlyScoreComment4" style="width:100%;height:120px;"><?=$AssmtStudentLeveltestScoreComment4?></textarea>

					SCORE &nbsp;<select name="AssmtStudentLeveltestScore4" style="width:120px;height:34px;">
						<option value="10" <?if ($AssmtStudentLeveltestScore4==10){?>selected<?}?>>10</option>
						<option value="8" <?if ($AssmtStudentLeveltestScore4==8){?>selected<?}?>>8</option>
						<option value="6" <?if ($AssmtStudentLeveltestScore4==6){?>selected<?}?>>6</option>
						<option value="4" <?if ($AssmtStudentLeveltestScore4==4){?>selected<?}?>>4</option>
						<option value="2" <?if ($AssmtStudentLeveltestScore4==2){?>selected<?}?>>2</option>
					</select>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Fluency</th>
				<td class="radio_wrap time">
					<div style="text-align:right;">
						<div style="display:inline-block:height:30px;line-height:30px;background-color:#f1f1f1;text-align:center;margin-bottom:5px;cursor:pointer;" onclick="OpenSampleComment(5)">코멘트 Comment</div>
					</div>
					<textarea name="AssmtStudentMonthlyScoreComment5" style="width:100%;height:120px;"><?=$AssmtStudentLeveltestScoreComment5?></textarea>

					SCORE &nbsp;<select name="AssmtStudentLeveltestScore5" style="width:120px;height:34px;">
						<option value="10" <?if ($AssmtStudentLeveltestScore5==10){?>selected<?}?>>10</option>
						<option value="8" <?if ($AssmtStudentLeveltestScore5==8){?>selected<?}?>>8</option>
						<option value="6" <?if ($AssmtStudentLeveltestScore5==6){?>selected<?}?>>6</option>
						<option value="4" <?if ($AssmtStudentLeveltestScore5==4){?>selected<?}?>>4</option>
						<option value="2" <?if ($AssmtStudentLeveltestScore5==2){?>selected<?}?>>2</option>
					</select>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Total Comments</th>
				<td class="radio_wrap time" style="padding:10px;">
					<div style="text-align:right;">
						<div style="display:inline-block:height:30px;line-height:30px;background-color:#f1f1f1;text-align:center;margin-bottom:5px;cursor:pointer;" onclick="OpenSampleComment('Total')">코멘트 Comment</div>
					</div>
					<textarea name="AssmtStudentMonthlyScoreCommentTotal" style="width:100%;height:120px;"><?=$AssmtStudentLeveltestScoreCommentTotal?></textarea>
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
function OpenSampleComment(CommentSection){
	
	AssmtStudentLeveltestScoreLevel = document.RegForm.AssmtStudentLeveltestScoreLevel.value;
	if (AssmtStudentLeveltestScoreLevel=="1" || AssmtStudentLeveltestScoreLevel=="2"){
		AssmtStudentCommentLevel = 12;	
	}else if (AssmtStudentLeveltestScoreLevel=="3" || AssmtStudentLeveltestScoreLevel=="4"){
		AssmtStudentCommentLevel = 34;	
	}else if (AssmtStudentLeveltestScoreLevel=="5" || AssmtStudentLeveltestScoreLevel=="6"){
		AssmtStudentCommentLevel = 56;	
	}else if (AssmtStudentLeveltestScoreLevel=="7" || AssmtStudentLeveltestScoreLevel=="8"){
		AssmtStudentCommentLevel = 78;	
	}

	
	openurl = "./pop_student_score_comment_sample.php?AssmtStudentCommentLevel="+AssmtStudentCommentLevel+"&CommentSection="+CommentSection;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	});
}

function CloseForm(){
	parent.$.fn.colorbox.close();
}

function FormSubmit(){

	AlertMsg = "평가를 완료 하시겠습니까?";

	if (confirm(AlertMsg)){
		document.RegForm.action = "pop_student_leveltest_score_action.php"
		document.RegForm.submit();
	}

}

</script>



<link rel="stylesheet" href="../js/colorbox/example2/colorbox.css" />
<script src="../js/colorbox/jquery.colorbox.js"></script>
<script>
jQuery(document).ready(function(){
$(document).bind('cbox_open', function() {
	$('html').css({ overflow: 'hidden' });
}).bind('cbox_closed', function() {
	$('html').css({ overflow: '' });
});
});

/*
var cboxOptions = {
  width: '95%',
  height: '95%',
  maxWidth: '850px',
  maxHeight: '750px',
}

$('.cbox-link').colorbox(cboxOptions);

$(window).resize(function(){
	$.colorbox.resize({
	  width: window.innerWidth > parseInt(cboxOptions.maxWidth) ? cboxOptions.maxWidth : cboxOptions.width,
	  height: window.innerHeight > parseInt(cboxOptions.maxHeight) ? cboxOptions.maxHeight : cboxOptions.height
	});
});
*/
</script>
</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>