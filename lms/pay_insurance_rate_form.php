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
$MainMenuID = 77;
$SubMenuID = 7708;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$InsuranceID = isset($_REQUEST["InsuranceID"]) ? $_REQUEST["InsuranceID"] : "";



if ($InsuranceID!=""){

	$Sql = "SELECT  
					*
			from PayInsuranceRate  
			where InsuranceID=:InsuranceID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':InsuranceID', $InsuranceID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$InsuranceID = $Row["InsuranceID"];
	$Year = $Row["Year"];
	$EmploymentInsurance = $Row["EmploymentInsurance"];
	$HealthInsurance = $Row["HealthInsurance"];
	$CareInsurance = $Row["CareInsurance"];
	$NationalPension = $Row["NationalPension"];
	

}else{
	$InsuranceID = "";
	$Year = "";
	$EmploymentInsurance = "";
	$HealthInsurance = "";
	$CareInsurance = "";
	$NationalPension = "";
}


?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="InsuranceID" value="<?=$InsuranceID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-10-10">
				<div class="md-card">
					<div class="user_content">
						
						<ul class="uk-margin">
							<div>
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c"> 
										<?=$사대보험요율관리[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-10-10">
											<label for="Year"><?=$귀속년도[$LangID]?></label>
											<input type="text" id="Year" name="Year" value="<?=$Year?>"/>
										</div>
										<div class="uk-width-medium-2-10">
											<label for="EmploymentInsurance"><?=$고용보험[$LangID]?></label>
											<input type="text" id="EmploymentInsurance" name="EmploymentInsurance" value="<?=$EmploymentInsurance?>"/>
										</div>
										<div class="uk-width-medium-2-10">
											<label for="HealthInsurance"><?=$건강보험[$LangID]?></label>
											<input type="text" id="HealthInsurance" name="HealthInsurance" value="<?=$HealthInsurance?>"/>
										</div>
										<div class="uk-width-medium-2-10">
											<label for="CareInsurance"><?=$장기요양보험[$LangID]?></label>
											<input type="text" id="CareInsurance" name="CareInsurance" value="<?=$CareInsurance?>"/>
										</div>
										<div class="uk-width-medium-2-10">
											<label for="NationalPension"><?=$국민연금[$LangID]?></label>
											<input type="text" id="NationalPension" name="NationalPension" value="<?=$NationalPension?>"/>
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
			<a type="button" onClick="DeleteSubmit();" class="md-btn md-btn-primary"><?=$삭제[$LangID]?></a>
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

	obj = document.RegForm.Year;
	if (obj.value==""){
		UIkit.modal.alert("귀속년도를 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.EmploymentInsurance;
	if (obj.value==""){
		UIkit.modal.alert("고용보험을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.HealthInsurance;
	if (obj.value==""){
		UIkit.modal.alert("간강보험을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.CareInsurance;
	if (obj.value==""){
		UIkit.modal.alert("장기요양보험을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.NationalPension;
	if (obj.value==""){
		UIkit.modal.alert("국민연금을 입력하세요.");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'<?=$저장하시겠습니까[$LangID]?>?', 
		function(){ 
				document.RegForm.action = "pay_insurance_rate_action.php";
				document.RegForm.submit();
		}
	);

}

function DeleteSubmit(){


UIkit.modal.confirm(
	'<?=$삭제하시겠습니까[$LangID]?>?', 
	function(){ 
			document.RegForm.action = "pay_insurance_rate_action.php?delete=true";
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