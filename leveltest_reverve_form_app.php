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

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="./js/jquery-confirm.min.js"></script>
<link href="./css/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<?
$MemberID = $_LINK_MEMBER_ID_;

$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$ClassOrderLeveltestApplyDate = isset($_REQUEST["LeveltestApplyDate"]) ? $_REQUEST["LeveltestApplyDate"] : "";
$LeveltestTimeHour = isset($_REQUEST["LeveltestTimeHour"]) ? $_REQUEST["LeveltestTimeHour"] : "";
$LeveltestTimeMinute = isset($_REQUEST["LeveltestTimeMinute"]) ? $_REQUEST["LeveltestTimeMinute"] : "";


$LeveltestTimeWeek = date('w', strtotime($ClassOrderLeveltestApplyDate));
$ArrWeekDayStr = explode(",","일요일,월요일,화요일,수요일,목요일,금요일,토요일");
$WeekDayStr = $ArrWeekDayStr[$LeveltestTimeWeek];
$ArrClassOrderLeveltestApplyDate = explode("-",$ClassOrderLeveltestApplyDate);
$StrClassOrderLeveltestApplyDate = $ArrClassOrderLeveltestApplyDate[0]."년 ".$ArrClassOrderLeveltestApplyDate[1]."월 ".$ArrClassOrderLeveltestApplyDate[2]." 일 (".$WeekDayStr.") ".substr("0".$LeveltestTimeHour,-2)."시 ".substr("0".$LeveltestTimeMinute,-2)."분 ";

$Sql = "
		select 
				A.MemberName
		from Members A 
		where A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberName = $Row["MemberName"];


$Sql = "
		select 
				A.TeacherName
		from Teachers A 
		where A.TeacherID=:TeacherID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherID', $TeacherID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TeacherName = $Row["TeacherName"];
?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area">
		<h3 class="caption_underline">레벨테스트 예약하기</h3>

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="MemberID" value="<?=$MemberID?>">
		<input type="hidden" name="TeacherID" value="<?=$TeacherID?>">
		<input type="hidden" name="ClassOrderLeveltestApplyDate" value="<?=$ClassOrderLeveltestApplyDate?>">
		<input type="hidden" name="LeveltestTimeWeek" value="<?=$LeveltestTimeWeek?>">
		<input type="hidden" name="LeveltestTimeHour" value="<?=$LeveltestTimeHour?>">
		<input type="hidden" name="LeveltestTimeMinute" value="<?=$LeveltestTimeMinute?>">
		<input type="hidden" name="LeveltestCounselState" value="1">
		<input type="hidden" name="ClassOrderLeveltestApplyState" value="1">
		<table class="level_reserve_table">
            <tr>
                <th class="caption TrnTag">신청자</th>
            </tr>
			<tr>
				<td><?=$MemberName?></td>
			</tr>
            <tr>
                <th class="caption TrnTag">신청동기</th>
            </tr>
			<tr>
				<td>
					<select id="ClassOrderLeveltestApplyTypeID" name="ClassOrderLeveltestApplyTypeID" style="height:30px; width:100%; border:0; background-color:#fff;">
						<option value="" class="TrnTag">신청동기를 선택해 주세요</option>
						<?
						$Sql2 = "select 
										A.* 
								from ClassOrderLeveltestApplyTypes A 
								order by A.ClassOrderLeveltestApplyTypeID asc";
						$Stmt2 = $DbConn->prepare($Sql2);
						$Stmt2->execute();
						$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
						
						while($Row2 = $Stmt2->fetch()) {
							$SelectClassOrderLeveltestApplyTypeID = $Row2["ClassOrderLeveltestApplyTypeID"];
							$SelectClassOrderLeveltestApplyTypeName = $Row2["ClassOrderLeveltestApplyTypeName"];
						
						?>
						<option value="<?=$SelectClassOrderLeveltestApplyTypeID?>"><?=$SelectClassOrderLeveltestApplyTypeName?></option>
						<?
						}
						$Stmt2 = null;
						?>
					</select>				
				</td>
			</tr>
            <tr>
                <th class="caption TrnTag">강사명</th>
            </tr>
			<tr>
				<td><?=$TeacherName?></td>
			</tr>
            <tr>
                <th class="caption TrnTag">선택날짜</th>
            </tr>
			<tr>
				<td><?=$StrClassOrderLeveltestApplyDate?></td>
			</tr>
            
            <tr>
                <th class="caption TrnTag">신청레벨</th>
            </tr>
			
			<tr>
				<td class="radio_wrap level_test">
					<input type="radio" id="ClassOrderLeveltestApplyLevel1" class="input_radio" name="ClassOrderLeveltestApplyLevel" value="1" checked><label class="label TrnTag" for="ClassOrderLeveltestApplyLevel1"><span class="bullet_radio"></span>1레벨</label>
					<input type="radio" id="ClassOrderLeveltestApplyLevel2" class="input_radio" name="ClassOrderLeveltestApplyLevel" value="2"><label class="label TrnTag" for="ClassOrderLeveltestApplyLevel2"><span class="bullet_radio"></span>2레벨</label>
					<input type="radio" id="ClassOrderLeveltestApplyLevel3" class="input_radio" name="ClassOrderLeveltestApplyLevel" value="3"><label class="label TrnTag" for="ClassOrderLeveltestApplyLevel3"><span class="bullet_radio"></span>3레벨</label>
					<input type="radio" id="ClassOrderLeveltestApplyLevel4" class="input_radio" name="ClassOrderLeveltestApplyLevel" value="4"><label class="label TrnTag" for="ClassOrderLeveltestApplyLevel4"><span class="bullet_radio"></span>4레벨</label>
					<input type="radio" id="ClassOrderLeveltestApplyLevel5" class="input_radio" name="ClassOrderLeveltestApplyLevel" value="5"><label class="label TrnTag" for="ClassOrderLeveltestApplyLevel5"><span class="bullet_radio"></span>5레벨</label>
					<input type="radio" id="ClassOrderLeveltestApplyLevel6" class="input_radio" name="ClassOrderLeveltestApplyLevel" value="6"><label class="label TrnTag" for="ClassOrderLeveltestApplyLevel6"><span class="bullet_radio"></span>6레벨</label>
					<input type="radio" id="ClassOrderLeveltestApplyLevel7" class="input_radio" name="ClassOrderLeveltestApplyLevel" value="7"><label class="label TrnTag" for="ClassOrderLeveltestApplyLevel7"><span class="bullet_radio"></span>7레벨</label>
					<input type="radio" id="ClassOrderLeveltestApplyLevel8" class="input_radio" name="ClassOrderLeveltestApplyLevel" value="8"><label class="label TrnTag" for="ClassOrderLeveltestApplyLevel8"><span class="bullet_radio"></span>8레벨</label>
				</td>
			</tr>
            
            <tr>
                <th class="caption">영어경력</th>
            </tr>
            
			<tr>
				<td class="radio_wrap level_test">
					<input type="radio" id="ClassOrderLeveltestApplyOverseaTypeID1" class="input_radio" name="ClassOrderLeveltestApplyOverseaTypeID" value="1" checked><label class="label TrnTag" for="ClassOrderLeveltestApplyOverseaTypeID1"><span class="bullet_radio"></span>없음</label>
					<input type="radio" id="ClassOrderLeveltestApplyOverseaTypeID2" class="input_radio" name="ClassOrderLeveltestApplyOverseaTypeID" value="2"><label class="label TrnTag" for="ClassOrderLeveltestApplyOverseaTypeID2"><span class="bullet_radio"></span>3개월</label>
					<input type="radio" id="ClassOrderLeveltestApplyOverseaTypeID3" class="input_radio" name="ClassOrderLeveltestApplyOverseaTypeID" value="3"><label class="label TrnTag" for="ClassOrderLeveltestApplyOverseaTypeID3"><span class="bullet_radio"></span>6개월</label>
					<input type="radio" id="ClassOrderLeveltestApplyOverseaTypeID4" class="input_radio" name="ClassOrderLeveltestApplyOverseaTypeID" value="4"><label class="label TrnTag" for="ClassOrderLeveltestApplyOverseaTypeID4"><span class="bullet_radio"></span>1년</label>
					<input type="radio" id="ClassOrderLeveltestApplyOverseaTypeID5" class="input_radio" name="ClassOrderLeveltestApplyOverseaTypeID" value="5"><label class="label TrnTag" for="ClassOrderLeveltestApplyOverseaTypeID5"><span class="bullet_radio"></span>2년</label>
					<input type="radio" id="ClassOrderLeveltestApplyOverseaTypeID6" class="input_radio" name="ClassOrderLeveltestApplyOverseaTypeID" value="6"><label class="label TrnTag" for="ClassOrderLeveltestApplyOverseaTypeID6"><span class="bullet_radio"></span>3년이상</label>
				</td>
			</tr>

            <tr>
                <th class="caption TrnTag">참고사항</th>
            </tr>
			<tr>
				<td style="padding:5px;"><textarea name="ClassOrderLeveltestApplyText" id="ClassOrderLeveltestApplyText" style="width:100%; height:100px; display:block; border:1px solid #dadada;"></textarea></td>
			</tr>
		</table>
		</form>
		<div class="button_wrap flex_justify">
			<a href="javascript:FormSubmit();" class="button_orange_white mantoman TrnTag">신청하기</a>
			<a href="javascript:parent.$.fn.colorbox.close();" class="button_br_black mantoman TrnTag">취소하기</a>
		</div>
	</div>
</div>



<script language="javascript">
function FormSubmit(){

	obj = document.RegForm.ClassOrderLeveltestApplyTypeID;
	if (obj.value==""){
		$.alert({title: "안내", content: "신청 동기를 선택해 주세요."});
		return;
	}





	AlertMsg = "레벨테스트 예약을 진행하시겠습니까?";

	$.confirm({
		title: "안내",
		content: AlertMsg,
		buttons: {
			확인: function () {
				document.RegForm.action = "leveltest_reverve_action_app.php"
				document.RegForm.submit();
			}
		}
	});	

}


</script>



<!-- ====    kendo -->
<link href="./kendo/styles/kendo.common.min.css" rel="stylesheet">
<link href="./kendo/styles/kendo.default.min.css" rel="stylesheet">
<script src="./kendo/js/kendo.web.min.js"></script>
<!-- ====    kendo   === -->


<!-- ====   Color Box -->
<?
$ColorBox = isset($ColorBox) ? $ColorBox : "";
if ($ColorBox==""){
	$ColorBox = "example2";
}
?>
<link rel="stylesheet" href="../js/colorbox/<?=$ColorBox?>/colorbox.css" />
<script src="../js/colorbox/jquery.colorbox.js"></script>
<script>
jQuery(document).ready(function(){
$(document).bind('cbox_open', function() {
    $('html').css({ overflow: 'hidden' });
}).bind('cbox_closed', function() {
    $('html').css({ overflow: '' });
});
});
</script>
<!-- ====   Color Box   === -->
</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>