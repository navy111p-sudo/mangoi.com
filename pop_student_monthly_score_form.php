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

$Sql = "select * from AssmtStudentMonthlyScores where ClassID=:ClassID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;


if ($Row["AssmtStudentMonthlyScoreID"]) {
	$AssmtStudentMonthlyScoreID = $Row["AssmtStudentMonthlyScoreID"];

	$AssmtStudentMonthlyScoreSubject = $Row["AssmtStudentMonthlyScoreSubject"];
	$AssmtStudentMonthlyScoreYear = $Row["AssmtStudentMonthlyScoreYear"];
	$AssmtStudentMonthlyScoreMonth = $Row["AssmtStudentMonthlyScoreMonth"];
	$AssmtStudentMonthlyScoreLevel = $Row["AssmtStudentMonthlyScoreLevel"];

	$AssmtStudentMonthlyScoreComment1 = $Row["AssmtStudentMonthlyScoreComment1"];
	$AssmtStudentMonthlyScoreComment2 = $Row["AssmtStudentMonthlyScoreComment2"];
	$AssmtStudentMonthlyScoreComment3 = $Row["AssmtStudentMonthlyScoreComment3"];
	$AssmtStudentMonthlyScoreComment4 = $Row["AssmtStudentMonthlyScoreComment4"];
	$AssmtStudentMonthlyScoreComment5 = $Row["AssmtStudentMonthlyScoreComment5"];

	$AssmtStudentMonthlyScoreCommentTotal = $Row["AssmtStudentMonthlyScoreCommentTotal"];


	$SearchYear = $AssmtStudentMonthlyScoreYear;
	$SearchMonth = $AssmtStudentMonthlyScoreMonth;

}else{

	$AssmtStudentMonthlyScoreID = "";

	$AssmtStudentMonthlyScoreSubject = "";
	$AssmtStudentMonthlyScoreLevel = 1;

	$AssmtStudentMonthlyScoreComment1 = "";
	$AssmtStudentMonthlyScoreComment2 = "";
	$AssmtStudentMonthlyScoreComment3 = "";
	$AssmtStudentMonthlyScoreComment4 = "";
	$AssmtStudentMonthlyScoreComment5 = "";

	$AssmtStudentMonthlyScoreCommentTotal = "";


	$SearchYear = date("Y");
	$SearchMonth = date("n");

}

?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area" style="width:90%;">
		<h3 class="caption_underline">Regular Report</h3>

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="AssmtStudentMonthlyScoreID" value="<?=$AssmtStudentMonthlyScoreID?>">
		<input type="hidden" name="ClassID" value="<?=$ClassID?>">
		<table class="level_reserve_table">
			<tr>
				<th style="padding:10px;">Year / Month</th>
				<td class="radio_wrap time" style="padding:10px;">
					<select name="AssmtStudentMonthlyScoreYear" style="width:120px;height:34px;">
						<?for ($ii=2019;$ii<=$SearchYear+1;$ii++){?>
						<option value="<?=$ii?>" <?if ($SearchYear==$ii){?>selected<?}?>><?=$ii?></option>
						<?}?>
					</select>
					<select name="AssmtStudentMonthlyScoreMonth" style="width:120px;height:34px;">
						<?for ($ii=1;$ii<=12;$ii++){?>
						<option value="<?=$ii?>" <?if ($SearchMonth==$ii){?>selected<?}?>><?=$ii?></option>
						<?}?>
					</select>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Report Title</th>
				<td class="radio_wrap time" style="padding:10px;">
					<input type="text" name="AssmtStudentMonthlyScoreSubject" id="AssmtStudentMonthlyScoreSubject" value="<?=$AssmtStudentMonthlyScoreSubject?>" style="height:34px;width:100%;">
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">LEVEL</th>
				<td class="radio_wrap time" style="padding:10px;">
					<select name="AssmtStudentMonthlyScoreLevel" style="width:120px;height:34px;">
						<option value="1" <?if ($AssmtStudentMonthlyScoreLevel==1){?>selected<?}?>>1 LEVEL</option>
						<option value="2" <?if ($AssmtStudentMonthlyScoreLevel==2){?>selected<?}?>>2 LEVEL</option>
						<option value="3" <?if ($AssmtStudentMonthlyScoreLevel==3){?>selected<?}?>>3 LEVEL</option>
						<option value="4" <?if ($AssmtStudentMonthlyScoreLevel==4){?>selected<?}?>>4 LEVEL</option>
						<option value="5" <?if ($AssmtStudentMonthlyScoreLevel==5){?>selected<?}?>>5 LEVEL</option>
						<option value="6" <?if ($AssmtStudentMonthlyScoreLevel==6){?>selected<?}?>>6 LEVEL</option>
						<option value="7" <?if ($AssmtStudentMonthlyScoreLevel==7){?>selected<?}?>>7 LEVEL</option>
						<option value="8" <?if ($AssmtStudentMonthlyScoreLevel==8){?>selected<?}?>>8 LEVEL</option>
					</select>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Pronunciation</th>
				<td class="radio_wrap time">
					<div style="text-align:right;">
						<div style="display:inline-block:height:30px;line-height:30px;background-color:#f1f1f1;text-align:center;margin-bottom:5px;cursor:pointer;" onclick="OpenSampleComment(1)">코멘트 Comment</div>
					</div>
					<textarea name="AssmtStudentMonthlyScoreComment1" style="width:100%;height:120px;"><?=$AssmtStudentMonthlyScoreComment1?></textarea>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Grammar</th>
				<td class="radio_wrap time">
					<div style="text-align:right;">
						<div style="display:inline-block:height:30px;line-height:30px;background-color:#f1f1f1;text-align:center;margin-bottom:5px;cursor:pointer;" onclick="OpenSampleComment(2)">코멘트 Comment</div>
					</div>
					<textarea name="AssmtStudentMonthlyScoreComment2" style="width:100%;height:120px;"><?=$AssmtStudentMonthlyScoreComment2?></textarea>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Vocabulary</th>
				<td class="radio_wrap time">
					<div style="text-align:right;">
						<div style="display:inline-block:height:30px;line-height:30px;background-color:#f1f1f1;text-align:center;margin-bottom:5px;cursor:pointer;" onclick="OpenSampleComment(3)">코멘트 Comment</div>
					</div>
					<textarea name="AssmtStudentMonthlyScoreComment3" style="width:100%;height:120px;"><?=$AssmtStudentMonthlyScoreComment3?></textarea>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Attitude</th>
				<td class="radio_wrap time">
					<div style="text-align:right;">
						<div style="display:inline-block:height:30px;line-height:30px;background-color:#f1f1f1;text-align:center;margin-bottom:5px;cursor:pointer;" onclick="OpenSampleComment(4)">코멘트 Comment</div>
					</div>
					<textarea name="AssmtStudentMonthlyScoreComment4" style="width:100%;height:120px;"><?=$AssmtStudentMonthlyScoreComment4?></textarea>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Fluency</th>
				<td class="radio_wrap time">
					<div style="text-align:right;">
						<div style="display:inline-block:height:30px;line-height:30px;background-color:#f1f1f1;text-align:center;margin-bottom:5px;cursor:pointer;" onclick="OpenSampleComment(5)">코멘트 Comment</div>
					</div>
					<textarea name="AssmtStudentMonthlyScoreComment5" style="width:100%;height:120px;"><?=$AssmtStudentMonthlyScoreComment5?></textarea>
				</td>
			</tr>
			<tr>
				<th style="padding:10px;">Total Comments</th>
				<td class="radio_wrap time" style="padding:10px;">
					<div style="text-align:right;">
						<div style="display:inline-block:height:30px;line-height:30px;background-color:#f1f1f1;text-align:center;margin-bottom:5px;cursor:pointer;" onclick="OpenSampleComment('Total')">코멘트 Comment</div>
					</div>
					<textarea name="AssmtStudentMonthlyScoreCommentTotal" style="width:100%;height:120px;"><?=$AssmtStudentMonthlyScoreCommentTotal?></textarea>
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
	
	AssmtStudentMonthlyScoreLevel = document.RegForm.AssmtStudentMonthlyScoreLevel.value;
	if (AssmtStudentMonthlyScoreLevel=="1" || AssmtStudentMonthlyScoreLevel=="2"){
		AssmtStudentCommentLevel = 12;	
	}else if (AssmtStudentMonthlyScoreLevel=="3" || AssmtStudentMonthlyScoreLevel=="4"){
		AssmtStudentCommentLevel = 34;	
	}else if (AssmtStudentMonthlyScoreLevel=="5" || AssmtStudentMonthlyScoreLevel=="6"){
		AssmtStudentCommentLevel = 56;	
	}else if (AssmtStudentMonthlyScoreLevel=="7" || AssmtStudentMonthlyScoreLevel=="8"){
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

	obj = document.RegForm.AssmtStudentMonthlyScoreSubject;
	if (obj.value==""){
		alert('제목을 입력해 주세요.');
		obj.focus();
		return;
	}
	

	AlertMsg = "평가를 완료 하시겠습니까?";

	if (confirm(AlertMsg)){
		document.RegForm.action = "pop_student_monthly_score_action.php"
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