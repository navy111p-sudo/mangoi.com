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
$ClassTeacherEnterID = isset($_REQUEST["ClassTeacherEnterID"]) ? $_REQUEST["ClassTeacherEnterID"] : "";
$ReasonType = isset($_REQUEST["ReasonType"]) ? $_REQUEST["ReasonType"] : "";


$Sql = "
		select 
				A.*
		from ClassTeacherEnters A 
		where A.ClassTeacherEnterID=:ClassTeacherEnterID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassTeacherEnterID', $ClassTeacherEnterID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ClassEnterLateReason = $Row["ClassEnterLateReason"];
$ClassEnterLateReasonAnswer = $Row["ClassEnterLateReasonAnswer"];

if ($ReasonType=="1"){
	$ReasonText = $ClassEnterLateReason;
}else{
	$ReasonText = $ClassEnterLateReasonAnswer;
}

if ( strpos($ReasonText, "|||")!==false) {
	// 포함
	$ArrReasonText = explode("|||", $ReasonText);
	$NewClassEnterLateReason = explode("||", $ArrReasonText[0]); // 사유을 내용|데이트타임으로 나눔
	$ReasonText = $NewClassEnterLateReason[0];
}

?>


<div id="page_content">
	<div id="page_content_inner">
		
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="ClassTeacherEnterID" value="<?=$ClassTeacherEnterID?>">
		<input type="hidden" name="ReasonType" value="<?=$ReasonType?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<?if ($ReasonType=="1"){?>
							<h2 class="heading_b"><span class="sub-heading" id="user_edit_position">Write late reason</span></h2>
							<?}else{?>
							<h2 class="heading_b"><span class="sub-heading" id="user_edit_position">답변등록</span></h2>
							<?}?>
						</div>
					</div>
					<div class="user_content">
						

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="ReasonText"><?if ($ReasonType=="1"){?>Reason<?}else{?>답변<?}?></label>
									<input type="text" class="md-input" name="ReasonText" id="ReasonText" value="<?=$ReasonText?>">
								</div>
							</div>
						</div>


						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
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
	<?if ($ReasonType=="1"){?>
		Msg = "Please enter something";
		Msg2 = "Please enter something";
	<?}else{?>
		Msg = "You can not modify after saving. Do you want to save?";
		Msg2 = "저장 하시겠습니까?";
	<?}?>

	obj = document.RegForm.ReasonText;
	if (obj.value==""){
		UIkit.modal.alert(Msg);
		obj.focus();
		return;
	}
	

	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "teacher_enter_late_reason_action.php";
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