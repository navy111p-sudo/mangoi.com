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

?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="CouponID" value="<?=$CouponID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname">상여금 일괄 등록</span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<select name="SpecialNo">
										<option value="0">상여금1</option>
										<option value="1">상여금2</option>
								</select>
								<div class="uk-width-medium-1-1">
									<label for="SpecialName1">상여금명</label>
									<input type="text" id="SpecialName1" name="SpecialName" value="" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-margin-top" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="Special1">상여금액</label>
									<select name="SpecialType">
										<option value="0">기본급의 %</option>
										<option value="1">정액</option>
									</select>
									<input type="text" id="Special1" name="Special" value="" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<hr>

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

	obj = document.RegForm.SpecialName;
	if (obj.value==""){
		UIkit.modal.alert("상여금명을 입력하세요!");
		obj.focus();
		return;
	}

	obj2 = document.RegForm.Special;

	if (obj2.value==""){
		UIkit.modal.alert("상여금액을 입력하세요!");
		obj2.focus();
		return;
	}

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "special_action.php";
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