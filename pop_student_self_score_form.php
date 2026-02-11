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
$OpenType = isset($_REQUEST["OpenType"]) ? $_REQUEST["OpenType"] : "";//1:새하 2:클래스인

if ($OpenType=="1"){
	$CommonShClassCode = isset($_REQUEST["CommonShClassCode"]) ? $_REQUEST["CommonShClassCode"] : "";
	$OnlineSiteShVersion = isset($_REQUEST["OnlineSiteShVersion"]) ? $_REQUEST["OnlineSiteShVersion"] : "";
	$MemberType = isset($_REQUEST["MemberType"]) ? $_REQUEST["MemberType"] : "";
	$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
	$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
}else{
	$CommonCiTelephoneTeacher = isset($_REQUEST["CommonCiTelephoneTeacher"]) ? $_REQUEST["CommonCiTelephoneTeacher"] : "";
	$CommonCiTelephoneStudent = isset($_REQUEST["CommonCiTelephoneStudent"]) ? $_REQUEST["CommonCiTelephoneStudent"] : "";
	$MemberType = isset($_REQUEST["MemberType"]) ? $_REQUEST["MemberType"] : "";
	$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
	$ClassName = isset($_REQUEST["ClassName"]) ? $_REQUEST["ClassName"] : "";
}

$Sql = "select AssmtStudentSelfScoreID from AssmtStudentSelfScores where ClassID=:ClassID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$AssmtStudentSelfScoreID = $Row["AssmtStudentSelfScoreID"];

?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area" style="width:90%;">
		<h3 class="caption_underline TrnTag">학생의 오늘 기분 상태는?</h3>

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="ClassID" value="<?=$ClassID?>">
		<table class="level_reserve_table">
			<tr>
				<th class="TrnTag">기분상태</th>
				<td class="radio_wrap time" style="height:100px;">
					<input type="radio" id="AssmtStudentSelfScore_1" class="input_radio" name="AssmtStudentSelfScore" value="1"><label class="label TrnTag" for="AssmtStudentSelfScore_1"><span class="bullet_radio"></span>매우 즐거움</label>
					<input type="radio" id="AssmtStudentSelfScore_2" class="input_radio" name="AssmtStudentSelfScore" value="2"><label class="label TrnTag" for="AssmtStudentSelfScore_2"><span class="bullet_radio"></span>즐거움</label>
					<input type="radio" id="AssmtStudentSelfScore_3" class="input_radio" name="AssmtStudentSelfScore" value="3"><label class="label TrnTag" for="AssmtStudentSelfScore_3"><span class="bullet_radio"></span>보통</label>
					<input type="radio" id="AssmtStudentSelfScore_4" class="input_radio" name="AssmtStudentSelfScore" value="4"><label class="label TrnTag" for="AssmtStudentSelfScore_4"><span class="bullet_radio"></span>우울함</label>
					<input type="radio" id="AssmtStudentSelfScore_5" class="input_radio" name="AssmtStudentSelfScore" value="5"><label class="label TrnTag" for="AssmtStudentSelfScore_5"><span class="bullet_radio"></span>매우 우울함</label>
				</td>
			</tr>
		</table>
		</form>

		<div class="button_wrap flex_justify">
			<a href="javascript:FormSubmit();" class="button_orange_white mantoman TrnTag">평가후 강의실 입장</a>
			<a href="javascript:parent.$.fn.colorbox.close();" class="button_br_black mantoman TrnTag">취소하기</a>
		</div>
	</div>
</div>


<script language="javascript">
function FormSubmit(){

	AssmtStudentSelfScore = 0;
	
	if (document.RegForm.AssmtStudentSelfScore[0].checked){
		AssmtStudentSelfScore = 1;
	}else if (document.RegForm.AssmtStudentSelfScore[1].checked){
		AssmtStudentSelfScore = 2;
	}else if (document.RegForm.AssmtStudentSelfScore[2].checked){
		AssmtStudentSelfScore = 3;
	}else if (document.RegForm.AssmtStudentSelfScore[3].checked){
		AssmtStudentSelfScore = 4;
	}else if (document.RegForm.AssmtStudentSelfScore[4].checked){
		AssmtStudentSelfScore = 5;
	}

	if (AssmtStudentSelfScore==0){
		alert("오늘의 기분 상태를 체크해 주세요.");
	}else{

		url = "ajax_set_student_self_score.php";
		//window.open(url + "?AssmtStudentSelfScore="+AssmtStudentSelfScore+"&ClassID=<?=$ClassID?>");
		$.ajax(url, {
			data: {
				AssmtStudentSelfScore: AssmtStudentSelfScore,
				ClassID: "<?=$ClassID?>"
			},
			success: function (data) {
				OpenLecture();
			},
			error: function () {
				//alert('오류가 발생했습니다. 다시 시도해 주세요.');
			}
		});	

	}

}


function OpenLecture(){
	<?if ($OpenType=="1"){?>
	parent.OpenClassSh("<?=$ClassID?>", "<?=$CommonShClassCode?>", "<?=$MemberType?>", "<?=$MemberName?>", "<?=$MemberLoginID?>", "<?=$OnlineSiteShVersion?>");
	<?}else{?>
	parent.OpenClassCiCheck("<?=$ClassID?>", "<?=$CommonCiTelephoneTeacher?>", "<?=$CommonCiTelephoneStudent?>", "?=$MemberType?>", "<?=$MemberName?>", "<?=$ClassName?>");
	<?}?>

	setTimeout(WinClose, 2000);
}

function WinClose(){
	parent.$.fn.colorbox.close();
}
</script>




</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>