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


if ($CouponID!=""){
	$Sql = "
			select 
				A.*
			from Coupons A 

			where A.CouponID=:CouponID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CouponID', $CouponID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();

	$CouponTypeID = $Row["CouponTypeID"];
	$CouponName = $Row["CouponName"];
	$CouponStartDate = $Row["CouponStartDate"];
	$CouponEndDate = $Row["CouponEndDate"];
	$CouponPrice = $Row["CouponPrice"];
	$CouponState = $Row["CouponState"];

}else{
	$CouponTypeID = 1;
	$CouponName = "";
	$CouponStartDate = date("Y-m-d");
	$CouponEndDate = date("Y-m-d");
	$CouponPrice = 0;
	$CouponState = 1;
}
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
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$쿠폰관리[$LangID]?></span><span class="sub-heading" id="user_edit_position"><!--베스트강사--></span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="CouponName"><?=$쿠폰명[$LangID]?></label>
									<input type="text" id="CouponName" name="CouponName" value="<?=$CouponName?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-margin-top" data-uk-grid-margin>
								<?
								$Sql2 = "select 
												A.* 
										from CouponTypes A 
										order by A.CouponTypeID asc";
								$Stmt2 = $DbConn->prepare($Sql2);
								$Stmt2->execute();
								$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
								
								while($Row2 = $Stmt2->fetch()) {
								?>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="CouponTypeID" id="CouponTypeID<?=$Row2["CouponTypeID"]?>" value="<?=$Row2["CouponTypeID"]?>" <?if ($CouponTypeID==$Row2["CouponTypeID"]){?>checked<?}?>/>
									<label for="CouponTypeID<?=$Row2["CouponTypeID"]?>" class="radio_label"><span class="radio_bullet"></span><?=$Row2["CouponTypeName"]?></label>
								</span>
								<?
								}
								$Stmt2 = null;
								?>
							</div>
						</div>

						<hr>

						<div class="uk-margin-top" data-uk-grid-margin>
							<div class="uk-width-medium-1-2 uk-input-group">
								<label for="CouponStartDate"><?=$시작일[$LangID]?></label>
								<input type="text" id="CouponStartDate" name="CouponStartDate" value="<?=$CouponStartDate?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}">
								<span class="uk-input-group-addon"><i class="uk-input-group-icon uk-icon-calendar"></i></span>
							</div>
							<div class="uk-width-medium-1-2 uk-input-group">
								<label for="CouponEndDate"><?=$종료일[$LangID]?></label>
								<input type="text" id="CouponEndDate" name="CouponEndDate" value="<?=$CouponEndDate?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}">
								<span class="uk-input-group-addon"><i class="uk-input-group-icon uk-icon-calendar"></i></span>
							</div>
						</div>


						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="CouponPrice"><?=$적립금액[$LangID]?></label>
									<input type="text" id="CouponPrice" name="CouponPrice" value="<?=$CouponPrice?>" class="md-input label-fixed allownumericwithoutdecimal"/>
								</div>
							</div>
						</div>


						
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2 uk-input-group">
									<input type="checkbox" id="CouponState" name="CouponState" value="1" <?php if ($CouponState==1) { echo "checked";}?> data-switchery/>
									<label for="CouponState" class="inline-label"><?=$사용[$LangID]?></label>
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

	obj = document.RegForm.CouponName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$쿠폰명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "coupon_action.php";
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