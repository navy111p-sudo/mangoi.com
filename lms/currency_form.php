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
$MainMenuID = 11;
$SubMenuID = 1177;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');


$CountryCode = isset($_REQUEST["CountryCode"]) ? $_REQUEST["CountryCode"] : "";



if ($CountryCode!=""){

	$Sql = "SELECT  
					*
			from Currency  
			where CountryCode=:CountryCode";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CountryCode', $CountryCode);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$CountryName = $Row["CountryName"];
	$Currency = $Row["Currency"];

}else{
	$CountryCode = "";
	$CountryName = "";
	$Currency = "";
}


?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="CountryCode" value="<?=$CountryCode?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-10-10">
				<div class="md-card">
					<div class="user_content">
						
						<ul class="uk-margin">
							<div>
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c"> 
										환율 관리
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10">
											<label for="CountryName">국가명</label>
											<input type="text" id="CountryName" name="CountryName" value="<?=$CountryName?>" readonly/>
										</div>
										<div class="uk-width-medium-4-10">
											<label for="Currency">환율</label>
											<input type="text" id="Currency" name="Currency" value="<?=$Currency?>"/>
										</div>
									</div>
									<div class="uk-form-row">
								</div>
							</div>
						</ul>
					</div>
				</div>
			</div>
			

		</div>
		</form>
		<div class="uk-form-row" style="text-align:center;margin-top:15px">
			<a type="button" onClick="FormSubmit();" class="md-btn md-btn-primary"><?=$등록[$LangID]?></a>
		</div>
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

	obj = document.RegForm.CountryName;
	if (obj.value==""){
		UIkit.modal.alert("국가명을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.Currency;
	if (obj.value==""){
		UIkit.modal.alert("환율을 입력하세요.");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'<?=$저장하시겠습니까[$LangID]?>?', 
		function(){ 
				document.RegForm.action = "currency_action.php";
				document.RegForm.submit();
		}
	);

}


</script>



<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>