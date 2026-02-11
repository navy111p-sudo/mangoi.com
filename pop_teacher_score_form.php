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
<script src="./js/jquery-confirm.min.js"></script>
<link href="./css/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>
<script src="js/common.js"></script>

<body>
<?
include_once('./includes/common_body_top.php');
?>
<?
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";


$Sql = "select AssmtTeacherScoreID from AssmtTeacherScores where ClassID=:ClassID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$AssmtTeacherScoreID = $Row["AssmtTeacherScoreID"];


$SampleStudy = 0;
if ($ClassID=="-1"){
	$SampleStudy = 1;
}
?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area" style="width:90%;">
		<h3 class="caption_underline">강의 평가</h3>

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="ClassID" value="<?=$ClassID?>">
		<input type="hidden" name="FromDevice" value="<?=$FromDevice?>">
		
		<table class="level_reserve_table">
			<tr>
				<th class="TrnTag">흥미도</th>
				<td class="radio_wrap time">
					<input type="radio" id="AssmtTeacherScore1_1" class="input_radio" name="AssmtTeacherScore1" value="1"><label class="label" for="AssmtTeacherScore1_1"><span class="bullet_radio"></span>2점</label>
					<input type="radio" id="AssmtTeacherScore1_2" class="input_radio" name="AssmtTeacherScore1" value="2"><label class="label" for="AssmtTeacherScore1_2"><span class="bullet_radio"></span>4점</label>
					<input type="radio" id="AssmtTeacherScore1_3" class="input_radio" name="AssmtTeacherScore1" value="3" checked><label class="label" for="AssmtTeacherScore1_3"><span class="bullet_radio"></span>6점</label>
					<input type="radio" id="AssmtTeacherScore1_4" class="input_radio" name="AssmtTeacherScore1" value="4"><label class="label" for="AssmtTeacherScore1_4"><span class="bullet_radio"></span>8점</label>
					<input type="radio" id="AssmtTeacherScore1_5" class="input_radio" name="AssmtTeacherScore1" value="5"><label class="label" for="AssmtTeacherScore1_5"><span class="bullet_radio"></span>10점</label>
				</td>
			</tr>
			<tr>
				<th class="TrnTag">태도</th>
				<td class="radio_wrap time">
					<input type="radio" id="AssmtTeacherScore2_1" class="input_radio" name="AssmtTeacherScore2" value="1"><label class="label" for="AssmtTeacherScore2_1"><span class="bullet_radio"></span>2점</label>
					<input type="radio" id="AssmtTeacherScore2_2" class="input_radio" name="AssmtTeacherScore2" value="2"><label class="label" for="AssmtTeacherScore2_2"><span class="bullet_radio"></span>4점</label>
					<input type="radio" id="AssmtTeacherScore2_3" class="input_radio" name="AssmtTeacherScore2" value="3" checked><label class="label" for="AssmtTeacherScore2_3"><span class="bullet_radio"></span>6점</label>
					<input type="radio" id="AssmtTeacherScore2_4" class="input_radio" name="AssmtTeacherScore2" value="4"><label class="label" for="AssmtTeacherScore2_4"><span class="bullet_radio"></span>8점</label>
					<input type="radio" id="AssmtTeacherScore2_5" class="input_radio" name="AssmtTeacherScore2" value="5"><label class="label" for="AssmtTeacherScore2_5"><span class="bullet_radio"></span>10점</label>
				</td>
			</tr>
			<tr>
				<th class="TrnTag">섬세함</th>
				<td class="radio_wrap time">
					<input type="radio" id="AssmtTeacherScore3_1" class="input_radio" name="AssmtTeacherScore3" value="1"><label class="label" for="AssmtTeacherScore3_1"><span class="bullet_radio"></span>2점</label>
					<input type="radio" id="AssmtTeacherScore3_2" class="input_radio" name="AssmtTeacherScore3" value="2"><label class="label" for="AssmtTeacherScore3_2"><span class="bullet_radio"></span>4점</label>
					<input type="radio" id="AssmtTeacherScore3_3" class="input_radio" name="AssmtTeacherScore3" value="3" checked><label class="label" for="AssmtTeacherScore3_3"><span class="bullet_radio"></span>6점</label>
					<input type="radio" id="AssmtTeacherScore3_4" class="input_radio" name="AssmtTeacherScore3" value="4"><label class="label" for="AssmtTeacherScore3_4"><span class="bullet_radio"></span>8점</label>
					<input type="radio" id="AssmtTeacherScore3_5" class="input_radio" name="AssmtTeacherScore3" value="5"><label class="label" for="AssmtTeacherScore3_5"><span class="bullet_radio"></span>10점</label>
				</td>
			</tr>
			<tr>
				<th class="TrnTag">발음</th>
				<td class="radio_wrap time">
					<input type="radio" id="AssmtTeacherScore4_1" class="input_radio" name="AssmtTeacherScore4" value="1"><label class="label" for="AssmtTeacherScore4_1"><span class="bullet_radio"></span>2점</label>
					<input type="radio" id="AssmtTeacherScore4_2" class="input_radio" name="AssmtTeacherScore4" value="2"><label class="label" for="AssmtTeacherScore4_2"><span class="bullet_radio"></span>4점</label>
					<input type="radio" id="AssmtTeacherScore4_3" class="input_radio" name="AssmtTeacherScore4" value="3" checked><label class="label" for="AssmtTeacherScore4_3"><span class="bullet_radio"></span>6점</label>
					<input type="radio" id="AssmtTeacherScore4_4" class="input_radio" name="AssmtTeacherScore4" value="4"><label class="label" for="AssmtTeacherScore4_4"><span class="bullet_radio"></span>8점</label>
					<input type="radio" id="AssmtTeacherScore4_5" class="input_radio" name="AssmtTeacherScore4" value="5"><label class="label" for="AssmtTeacherScore4_5"><span class="bullet_radio"></span>10점</label>
				</td>
			</tr>
			<tr>
				<th class="TrnTag">응대</th>
				<td class="radio_wrap time">
					<input type="radio" id="AssmtTeacherScore5_1" class="input_radio" name="AssmtTeacherScore5" value="1"><label class="label" for="AssmtTeacherScore5_1"><span class="bullet_radio"></span>2점</label>
					<input type="radio" id="AssmtTeacherScore5_2" class="input_radio" name="AssmtTeacherScore5" value="2"><label class="label" for="AssmtTeacherScore5_2"><span class="bullet_radio"></span>4점</label>
					<input type="radio" id="AssmtTeacherScore5_3" class="input_radio" name="AssmtTeacherScore5" value="3" checked><label class="label" for="AssmtTeacherScore5_3"><span class="bullet_radio"></span>6점</label>
					<input type="radio" id="AssmtTeacherScore5_4" class="input_radio" name="AssmtTeacherScore5" value="4"><label class="label" for="AssmtTeacherScore5_4"><span class="bullet_radio"></span>8점</label>
					<input type="radio" id="AssmtTeacherScore5_5" class="input_radio" name="AssmtTeacherScore5" value="5"><label class="label" for="AssmtTeacherScore5_5"><span class="bullet_radio"></span>10점</label>
				</td>
			</tr>
		</table>
		</form>
		<div class="button_wrap flex_justify">
			<?if ($SampleStudy==1){?>
			<a href="javascript:FormSubmitErr();" class="button_orange_white mantoman TrnTag">평가완료</a>
			<?}else{?>
			<a href="javascript:FormSubmit();" class="button_orange_white mantoman TrnTag">평가완료</a>
			<?}?>
			<a href="javascript:CloseForm();" class="button_br_black mantoman TrnTag">취소하기</a>
		</div>
	</div>
</div>


<script language="javascript">
function FormSubmitErr(){
	<?if ($FromDevice==""){?>
		alert("평가를 완료했습니다.");
		parent.$.fn.colorbox.close();
	<?}else{?>
		$.confirm({
			title: '안내',
			content: '평가를 완료했습니다.',
			buttons: {
				확인: function () {
					window.Exit=true;
				}
			}
		});
	<?}?>
}

function FormSubmit(){

	AlertMsg = "평가를 완료 하시겠습니까?";

	if (confirm(AlertMsg)){
		document.RegForm.action = "pop_teacher_score_action.php"
		document.RegForm.submit();
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

<?if ($AssmtTeacherScoreID){?>
<script>
alert("이미 강의평가를 완료했습니다.");
parent.$.fn.colorbox.close();
</script>
<?}?>



</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>