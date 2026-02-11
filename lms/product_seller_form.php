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
<!-- dropify -->
<link rel="stylesheet" href="assets/skins/dropify/css/dropify.css">
<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 25;
$SubMenuID = 2513;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ProductSellerID = isset($_REQUEST["ProductSellerID"]) ? $_REQUEST["ProductSellerID"] : "";


if ($ProductSellerID!=""){

	$Sql = "
			select 
					A.*
			from ProductSellers A 
			where A.ProductSellerID=:ProductSellerID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductSellerID', $ProductSellerID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$ProductSellerName = $Row["ProductSellerName"];
	$ProductSellerShipPriceType = $Row["ProductSellerShipPriceType"];
	$ProductSellerOrderTotPrice = $Row["ProductSellerOrderTotPrice"];
	$ProductSellerShipPrice = $Row["ProductSellerShipPrice"];
	$ProductSellerCancelLiminTime = $Row["ProductSellerCancelLiminTime"];
	$ProductSellerState = $Row["ProductSellerState"];

}else{
	$ProductSellerName = "";
	$ProductSellerShipPriceType = 1;
	$ProductSellerOrderTotPrice = 0;
	$ProductSellerShipPrice = 0;
	$ProductSellerCancelLiminTime = "1600";
	$ProductSellerState = 1;
}

?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="ProductSellerID" value="<?=$ProductSellerID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">

					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">

						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$교재판매구분정보[$LangID]?></span><span class="sub-heading" id="user_edit_position"></span></h2>
						</div>

					</div>

					<div class="user_content">

						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:none;">
							<li <?if ($PageTabID=="1"){?>class="uk-active"<?}?>><a href="#"><?=$교재정보[$LangID]?></a></li>
						</ul>

						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c"> 
										<?=$판매구분명[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-3-10">
											<label for="ProductSellerName"><?=$판매구분명[$LangID]?></label>
											<input type="text" id="ProductSellerName" name="ProductSellerName" value="<?=$ProductSellerName?>" class="md-input label-fixed"/>
										</div>

										<div class="uk-width-medium-3-10" style="display:none;">
											<label for="ProductSellerCancelLiminTime"><?=$취소마감시간[$LangID]?></label>
											<input type="text" id="ProductSellerCancelLiminTime" name="ProductSellerCancelLiminTime" value="<?=$ProductSellerCancelLiminTime?>" class="md-input label-fixed"/>
										</div>
									</div>


									<h3 class="full_width_in_card heading_c"> 
										<?=$배송료[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-3-10">

												<select id="ProductSellerShipPriceType" name="ProductSellerShipPriceType" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="부과타입" style="width:100%;"/>
													<option value="1" <?if ($ProductSellerShipPriceType==1){?>selected<?}?>><?=$무조건[$LangID]?></option>
													<option value="2" <?if ($ProductSellerShipPriceType==2){?>selected<?}?>><?=$부과기준_이하일때[$LangID]?></option>
													<option value="3" <?if ($ProductSellerShipPriceType==3){?>selected<?}?>><?=$부과기준_이상일때[$LangID]?></option>
												</select>

										</div>
										<div class="uk-width-medium-3-10">
											<label for="ProductSellerOrderTotPrice"><?=$부과기준[$LangID]?></label>
											<input type="text" id="ProductSellerOrderTotPrice" name="ProductSellerOrderTotPrice" value="<?=$ProductSellerOrderTotPrice?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="ProductSellerShipPrice"><?=$배송료[$LangID]?></label>
											<input type="text" id="ProductSellerShipPrice" name="ProductSellerShipPrice" value="<?=$ProductSellerShipPrice?>" class="md-input label-fixed"/>
										</div>
									</div>




								</div>
							</li>

						</ul>

					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10">
				<div class="md-card">
					<div class="md-card-content">
						<div class="uk-form-row">
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
<!--  dropify -->
<script src="bower_components/dropify/dist/js/dropify.min.js"></script>
<!--  form file input functions -->
<script src="assets/js/pages/forms_file_input.min.js"></script>
<script>
$(function() {
	if(isHighDensity()) {
		$.getScript( "assets/js/custom/dense.min.js", function(data) {
			// enable hires images
			altair_helpers.retina_images();
		});
	}
	if(Modernizr.touch) {
		// fastClick (touch devices)
		FastClick.attach(document.body);
	}
});
$window.load(function() {
	// ie fixes
	altair_helpers.ie_fix();
});
</script>


<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->

<script language="javascript">

function FormSubmit(){


	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
				document.RegForm.action = "product_seller_action.php";
				document.RegForm.submit();
		}
	);

}


window.onload = function(){

}
</script>




<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>