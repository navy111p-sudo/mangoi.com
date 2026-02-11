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
$SubMenuID = 1189;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$DepartmentID = isset($_REQUEST["DepartmentID"]) ? $_REQUEST["DepartmentID"] : "";



if ($DepartmentID!=""){

	$Sql = "SELECT  
					*
			from Departments 
			where DepartmentID=:DepartmentID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DepartmentID', $DepartmentID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$DepartmentID = $Row["DepartmentID"];
	$DepartmentName = $Row["DepartmentName"];
	$DepartmentNameEng = $Row["DepartmentNameEng"];
	$InUse = $Row["InUse"];

}else{
	$DepartmentID = "";
	$DepartmentName = "";
	$DepartmentNameEng = "";
	$InUse = 1;
}


?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="DepartmentID" value="<?=$DepartmentID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-10-10">
				<div class="md-card">
					<div class="user_content">
						
						<ul class="uk-margin">
							<div>
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c"> 
										<?=$부서명[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10">
											<label for="DepartmentName"><?=$부서명[$LangID]?></label>
											<input type="text" id="DepartmentName" name="DepartmentName" value="<?=$DepartmentName?>"/>
										</div>
										<div class="uk-width-medium-4-10">
											<label for="DepartmentNameEng"><?=$부서영문명[$LangID]?></label>
											<input type="text" id="DepartmentNameEng" name="DepartmentNameEng" value="<?=$DepartmentNameEng?>"/>
										</div>
										<div class="uk-width-medium-2-10">
											<input type="checkbox" id="InUse" name="InUse" value="1" <?php if ($InUse==1) { echo "checked";}?> data-switchery/>
											<label for="InUse" class="inline-label"><?=$사용중[$LangID]?></label>
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

	obj = document.RegForm.DepartmentName;
	if (obj.value==""){
		UIkit.modal.alert("부서명을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.DepartmentNameEng;
	if (obj.value==""){
		UIkit.modal.alert("부서 영문명을 입력하세요.");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'<?=$저장하시겠습니까[$LangID]?>?', 
		function(){ 
				document.RegForm.action = "departments_action.php";
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