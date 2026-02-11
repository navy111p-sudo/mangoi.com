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
$DeductionInfoID = isset($_REQUEST["DeductionInfoID"]) ? $_REQUEST["DeductionInfoID"] : "";
?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="DeductionInfoID" value="<?=$DeductionInfoID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname">공제항목 관리</span></h2>
						</div>
					</div>
					<div class="user_content">	
							<div class="uk-margin-top">
									<div class="uk-margin-top">
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-6-10">
											<label for="DeductionInfoItemTitle"><?=$항목[$LangID]?></label>
											<input type="text" id="DeductionInfoItemTitle" name="DeductionInfoItemTitle" value="" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-4-10">
											<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary">추가하기</a>
										</div>
									</div>
								</div>
							</div>
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

	obj = document.RegForm.DeductionInfoItemTitle;
	if (obj.value==""){
		UIkit.modal.alert("항목을 입력하세요.");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'추가 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "pay_deduction_info_write_action.php";
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