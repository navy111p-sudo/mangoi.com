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
$TeacherHolidayDate = isset($_REQUEST["TeacherHolidayDate"]) ? $_REQUEST["TeacherHolidayDate"] : "";



$Sql = "
		select 
				A.TeacherHolidayID
		from TeacherHolidays A 
		where A.TeacherID=:TeacherID and TeacherHolidayDate=:TeacherHolidayDate and TeacherHolidayState=1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherID', $TeacherID);
$Stmt->bindParam(':TeacherHolidayDate', $TeacherHolidayDate);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TeacherHolidayID = $Row["TeacherHolidayID"];


if ($TeacherHolidayID!=""){

	$Sql = "
			select 
					A.*
			from TeacherHolidays A 
			where A.TeacherHolidayID=:TeacherHolidayID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherHolidayID', $TeacherHolidayID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$TeacherHolidayName = $Row["TeacherHolidayName"];


}else{
	$TeacherHolidayName = "";
}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="TeacherID" value="<?=$TeacherID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="TeacherHolidayDate" value="<?=$TeacherHolidayDate?>">
		<input type="hidden" name="TeacherHolidayID" value="<?=$TeacherHolidayID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$TeacherHolidayDate?></span><span class="sub-heading" id="user_edit_position"><?=$휴일설정[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="CenterName"><?=$휴일내용[$LangID]?></label>
									<input type="text" id="TeacherHolidayName" name="TeacherHolidayName" value="<?=$TeacherHolidayName?>" class="md-input label-fixed"/>
								</div>
							</div>
							<?if ($TeacherHolidayID!=""){?>
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<input type="checkbox" name="DelTeacherHoliday" id="DelTeacherHoliday" value="1" data-md-icheck/>
									<label for="DelTeacherHoliday" class="inline-label">삭제</label>
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

	obj = document.RegForm.TeacherHolidayName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$휴일_내용을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "teacher_holiday_action.php";
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