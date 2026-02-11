<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->


</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$TeacherBreakTimeTempID = isset($_REQUEST["TeacherBreakTimeTempID"]) ? $_REQUEST["TeacherBreakTimeTempID"] : "";



if ($TeacherBreakTimeTempID!=""){

	$Sql = "
			select 
					A.*
			from TeacherBreakTimeTemps A 
			where A.TeacherBreakTimeTempID=:TeacherBreakTimeTempID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherBreakTimeTempID', $TeacherBreakTimeTempID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$TeacherBreakTimeTempID = $Row["TeacherBreakTimeTempID"];
	$TeacherBreakTimeTempStartDate = $Row["TeacherBreakTimeTempStartDate"];
	$TeacherBreakTimeTempEndDate = $Row["TeacherBreakTimeTempEndDate"];
	$TeacherBreakTimeTempWeek = $Row["TeacherBreakTimeTempWeek"];
	$TeacherBreakTimeTempStartHour = $Row["TeacherBreakTimeTempStartHour"];
	$TeacherBreakTimeTempStartMinute = $Row["TeacherBreakTimeTempStartMinute"];
	$TeacherBreakTimeTempEndHour = $Row["TeacherBreakTimeTempEndHour"];
	$TeacherBreakTimeTempEndMinute = $Row["TeacherBreakTimeTempEndMinute"];
	$TeacherBreakTimeTempType = $Row["TeacherBreakTimeTempType"];


}else{
	$TeacherBreakTimeTempID = "";
	$TeacherBreakTimeTempStartDate = date("Y-m-d");
	$TeacherBreakTimeTempEndDate = date("Y-m-d");
	$TeacherBreakTimeTempWeek = 1;
	$TeacherBreakTimeTempStartHour = 2;
	$TeacherBreakTimeTempStartMinute = 0;
	$TeacherBreakTimeTempEndHour = 3;
	$TeacherBreakTimeTempEndMinute = 0;
	$TeacherBreakTimeTempType = 2;
}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="TeacherID" value="<?=$TeacherID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="TeacherBreakTimeTempID" value="<?=$TeacherBreakTimeTempID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$기간별[$LangID]?></span><span class="sub-heading" id="user_edit_position"><?=$휴일설정[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									
									<span class="icheck-inline">
										<input type="radio" id="TeacherBreakTimeTempType2" name="TeacherBreakTimeTempType" <?php if ($TeacherBreakTimeTempType==2) { echo "checked";}?> value="2" data-md-icheck/>
										<label for="TeacherBreakTimeTempType2" class="inline-label"><?=$식사[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" id="TeacherBreakTimeTempType3" name="TeacherBreakTimeTempType" <?php if ($TeacherBreakTimeTempType==3) { echo "checked";}?> value="3" data-md-icheck/>
										<label for="TeacherBreakTimeTempType3" class="inline-label"><?=$휴식[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" id="TeacherBreakTimeTempType4" name="TeacherBreakTimeTempType" <?php if ($TeacherBreakTimeTempType==4) { echo "checked";}?> value="4" data-md-icheck/>
										<label for="TeacherBreakTimeTempType4" class="inline-label"><?=$블락[$LangID]?></label>
									</span>
								</div>

							</div>
							
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="TeacherBreakTimeTempStartDate"><?=$시작일[$LangID]?></label>
									<input type="text" id="TeacherBreakTimeTempStartDate" name="TeacherBreakTimeTempStartDate" value="<?=$TeacherBreakTimeTempStartDate?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}" readonly style="width:100%;">
								</div>
								<div class="uk-width-medium-1-2">
									<label for="TeacherBreakTimeTempEndDate"><?=$종료일[$LangID]?></label>
									<input type="text" id="TeacherBreakTimeTempEndDate" name="TeacherBreakTimeTempEndDate" value="<?=$TeacherBreakTimeTempEndDate?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}" readonly style="width:100%;">
								</div>
							</div>

							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									
									<span class="icheck-inline">
										<input type="radio" id="TeacherBreakTimeTempWeek0" name="TeacherBreakTimeTempWeek" <?php if ($TeacherBreakTimeTempWeek==0) { echo "checked";}?> value="0" data-md-icheck/>
										<label for="TeacherBreakTimeTempWeek0" class="inline-label"><?=$일[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" id="TeacherBreakTimeTempWeek1" name="TeacherBreakTimeTempWeek" <?php if ($TeacherBreakTimeTempWeek==1) { echo "checked";}?> value="1" data-md-icheck/>
										<label for="TeacherBreakTimeTempWeek1" class="inline-label"><?=$월[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" id="TeacherBreakTimeTempWeek2" name="TeacherBreakTimeTempWeek" <?php if ($TeacherBreakTimeTempWeek==2) { echo "checked";}?> value="2" data-md-icheck/>
										<label for="TeacherBreakTimeTempWeek2" class="inline-label"><?=$화[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" id="TeacherBreakTimeTempWeek3" name="TeacherBreakTimeTempWeek" <?php if ($TeacherBreakTimeTempWeek==3) { echo "checked";}?> value="3" data-md-icheck/>
										<label for="TeacherBreakTimeTempWeek3" class="inline-label"><?=$수[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" id="TeacherBreakTimeTempWeek4" name="TeacherBreakTimeTempWeek" <?php if ($TeacherBreakTimeTempWeek==4) { echo "checked";}?> value="4" data-md-icheck/>
										<label for="TeacherBreakTimeTempWeek4" class="inline-label"><?=$목[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" id="TeacherBreakTimeTempWeek5" name="TeacherBreakTimeTempWeek" <?php if ($TeacherBreakTimeTempWeek==5) { echo "checked";}?> value="5" data-md-icheck/>
										<label for="TeacherBreakTimeTempWeek5" class="inline-label"><?=$금[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" id="TeacherBreakTimeTempWeek6" name="TeacherBreakTimeTempWeek" <?php if ($TeacherBreakTimeTempWeek==6) { echo "checked";}?> value="6" data-md-icheck/>
										<label for="TeacherBreakTimeTempWeek6" class="inline-label"><?=$토[$LangID]?></label>
									</span>
								</div>

							</div>

							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="TeacherBreakTimeTempStartDate">시작시간</label>
									<select name="TeacherBreakTimeTempStartHour" style="height:30px;">
										<?for ($iiii=0;$iiii<=23;$iiii++){?>
										<option value="<?=$iiii?>" <?if ($TeacherBreakTimeTempStartHour==$iiii) {?>selected<?}?>><?=$iiii?></option>
										<?}?>
									</select><?=$시[$LangID]?> 
									<select name="TeacherBreakTimeTempStartMinute" style="height:30px;">
										<?for ($iiii=0;$iiii<=50;$iiii=$iiii+10){?>
										<option value="<?=$iiii?>" <?if ($TeacherBreakTimeTempStartMinute==$iiii) {?>selected<?}?>><?=$iiii?></option>
										<?}?>
									</select><?=$분[$LangID]?> 
								</div>
								<div class="uk-width-medium-1-2">
									<label for="TeacherBreakTimeTempEndDate">종료시간</label>
									<select name="TeacherBreakTimeTempEndHour" style="height:30px;">
										<?for ($iiii=0;$iiii<=23;$iiii++){?>
										<option value="<?=$iiii?>" <?if ($TeacherBreakTimeTempEndHour==$iiii) {?>selected<?}?>><?=$iiii?></option>
										<?}?>
									</select><?=$시[$LangID]?> 
									<select name="TeacherBreakTimeTempEndMinute" style="height:30px;">
										<?for ($iiii=0;$iiii<=50;$iiii=$iiii+10){?>
										<option value="<?=$iiii?>" <?if ($TeacherBreakTimeTempEndMinute==$iiii) {?>selected<?}?>><?=$iiii?></option>
										<?}?>
									</select><?=$분[$LangID]?> 
								</div>
							</div>

							<?if ($TeacherBreakTimeTempID!=""){?>
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<input type="checkbox" name="DelTeacherBreakTimeTemp" id="DelTeacherBreakTimeTemp" value="1" data-md-icheck/>
									<label for="DelTeacherBreakTimeTemp" class="inline-label"><?=$삭제[$LangID]?></label>
								</div>
							</div>
							<?}else{?>
							<div class="uk-grid" data-uk-grid-margin style="margin-bottom:50px;">
							</div>
							<?}?>
						</div>

						<div class="uk-margin-top" style="text-align:center;">
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?=$저장하기[$LangID]?></a>
						</div>

					</div>
				</div>
			</div>

		</div>
		</form>

	</div>
</div>


<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<script language="javascript">

function FormSubmit(){

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "teacher_break_time_temp_action.php";
			document.RegForm.submit();
		}
	);

}

</script>




<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>