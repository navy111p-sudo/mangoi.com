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
$SubMenuID = 1199;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
include_once('./inc_departments.php');

$departments = getDepartments($LangID);
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$DocumentType = isset($_REQUEST["DocumentType"]) ? $_REQUEST["DocumentType"] : "";
$DocumentReportMemberID = array();



if ($DocumentType!=""){

	$Sql = "SELECT  A.*, C.StaffManagement 
			from FixedApprovalLine A
			LEFT JOIN Members B ON A.MemberID = B.MemberID
			LEFT JOIN Staffs C ON B.StaffID = C.StaffID
			where DocumentType=:DocumentType order by ApprovalSequence";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentType', $DocumentType);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	while($Row = $Stmt->fetch()) {
		$ApprovalID = $Row["ApprovalID"];
		$ApprovalSequence = $Row["ApprovalSequence"];
		$MemberID = $Row["MemberID"];
		array_push($DocumentReportMemberID,[$Row["MemberID"] => $Row["StaffManagement"]]);
		
	}
	
	switch ($DocumentType) {
		case "0" :
			$DocumentName = "지출품의서 고정 결재라인";
			$FixedNumbers = 2;
			break;
		case "1" :
			$DocumentName = "휴가계획서 고정 결재라인";
			$FixedNumbers = 3;
			break;
		case "2" :
			$DocumentName = "필리핀 강사 지출품의서 고정 결재라인";
			$FixedNumbers = 2;
			break;
		case "3" :
			$DocumentName = "필리핀 강사 휴가계획서 고정 결재라인";
			$FixedNumbers = 3;
			break;
	}
	

}

?>
<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="DocumentType" value="<?=$DocumentType?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-10-10">
				<div class="md-card">
					<div class="user_content">
						
						<ul class="uk-margin">
							<div>
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c"> 
										<?=$DocumentName?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10">
											<label for="FixedApprovalLine">고정 결재라인</label>
											<table class="draft_approval" style="margin-left:60px">
												<tr style="height:60px;">
													<? for ($tdCount=0;$tdCount<$FixedNumbers;$tdCount++) { ?>

														<td style="width:300px;">
																<select id="category<?=$tdCount?>" onchange="javascript:categoryChange(this,'DocumentReportMemberID<?=$tdCount?>',0)">
																	<option value="none">선택안함</option>
																	<option><?=$부서선택[$LangID]?></option>
																	<?		
																		foreach($departments as $key => $value){
																			echo "<option value='{$key}'>{$value}</option>";
																		}
																	?>
																</select>
																<select id="DocumentReportMemberID<?=$tdCount?>" name="DocumentReportMemberID<?=$tdCount?>">
																	<option value=""><?=$직원선택[$LangID]?></option>
																</select>
														</td>

														<? } ?>
												</tr>
											</table>
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

	UIkit.modal.confirm(
		'<?=$저장하시겠습니까[$LangID]?>?', 
		function(){ 
				document.RegForm.action = "approval_line_action.php";
				document.RegForm.submit();
		}
	);

}


</script>



<?php
include_once('./inc_category_change.php');
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>