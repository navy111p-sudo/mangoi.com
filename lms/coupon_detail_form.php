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
$CouponID = isset($_REQUEST["CouponID"]) ? $_REQUEST["CouponID"] : "";
$CouponDetailID = isset($_REQUEST["CouponDetailID"]) ? $_REQUEST["CouponDetailID"] : "";

if ($CouponDetailID!=""){
	$Sql = "
			select 
				A.*
			from CouponDetails A 

			where A.CouponDetailID=:CouponDetailID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CouponDetailID', $CouponDetailID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();

	$CouponDetailNumber = $Row["CouponDetailNumber"];
	$CouponDetailState = $Row["CouponDetailState"];


}else{
	$CouponDetailNumber = "";
	$CouponDetailState = 1;
}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="CouponID" value="<?=$CouponID?>">
		<input type="hidden" name="CouponDetailID" value="<?=$CouponDetailID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$코폰번호관리[$LangID]?></span><span class="sub-heading" id="user_edit_position"><!--베스트강사--></span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<?if ($CouponDetailID!=""){?>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="CouponDetailNumber"><?=$쿠폰번호[$LangID]?></label>
									<input type="text" id="CouponDetailNumber" name="CouponDetailNumber" value="<?=$CouponDetailNumber?>" class="md-input label-fixed" readonly/>
								</div>
							</div>
						</div>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2 uk-input-group">
									<span class="icheck-inline">
										<input type="radio" class="radio_input" name="CouponDetailState" id="CouponDetailState1" value="1" <?if ($CouponDetailState==1){?>checked<?}?>/>
										<label for="CouponDetailState1" class="radio_label"><span class="radio_bullet"></span><?=$사용전[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" class="radio_input" name="CouponDetailState" id="CouponDetailState2" value="2" <?if ($CouponDetailState==2){?>checked<?}?>/>
										<label for="CouponDetailState2" class="radio_label"><span class="radio_bullet"></span><?=$사용완료[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" class="radio_input" name="CouponDetailState" id="CouponDetailState0" value="0" <?if ($CouponDetailState==0){?>checked<?}?>/>
										<label for="CouponDetailState0" class="radio_label"><span class="radio_bullet"></span><?=$삭제[$LangID]?></label>
									</span>
								</div>
							</div>
						</div>
						<?}else{?>
						
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="CouponCount"><?=$생성개수[$LangID]?></label>
									<input type="text" id="CouponCount" name="CouponCount" value="10" class="md-input label-fixed allownumericwithoutdecimal"/>
								</div>
							</div>
						</div>
						
						<?}?>


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


	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "coupon_detail_action.php";
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