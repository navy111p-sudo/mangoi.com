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
$SelfAssessmentItemID = isset($_REQUEST["SelfAssessmentItemID"]) ? $_REQUEST["SelfAssessmentItemID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : ""; 


if ($SelfAssessmentItemID!=""){
	$Sql = "
			select 
				* 
			from SelfAssessmentItems where SelfAssessmentItemID=:SelfAssessmentItemID"; // limit $StartRowNum, $PageListNum";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SelfAssessmentItemID', $SelfAssessmentItemID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	$Row = $Stmt->fetch();

	$SelfAssessmentItemTitle = $Row['SelfAssessmentItemTitle'];
	$SelfAssessmentItemContent = $Row['SelfAssessmentItemContent'];
	$SelfAssessmentItemState = $Row['SelfAssessmentItemState'];
	$SelfAssessmentItemView = $Row['SelfAssessmentItemView'];
}else{
	$SelfAssessmentItemTitle = "";
	$SelfAssessmentItemContent = "";
	$SelfAssessmentItemState = 1;
	$SelfAssessmentItemView = 1;
}
?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="SelfAssessmentItemID" value="<?=$SelfAssessmentItemID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$SelfAssessmentItemTitle?></span><span class="sub-heading" id="user_edit_position"><?=$학생_셀프평가항목관리_설정[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">	
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:none;">
							<li class="uk-active"><a href="#">Basic</a></li>
							<li><a href="#">Groups</a></li>
							<li><a href="#">Todo</a></li>
						</ul>
						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c">
										<?=$항목_관리[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-3-10">
											<label for="SelfAssessmentItemTitle"><?=$항목[$LangID]?></label>
											<input type="text" id="SelfAssessmentItemTitle" name="SelfAssessmentItemTitle" value="<?=$SelfAssessmentItemTitle?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-7-10">
											<label for="SelfAssessmentItemContent"><?=$항목설명[$LangID]?></label>
											<input type="text" id="SelfAssessmentItemContent" name="SelfAssessmentItemContent" value="<?=$SelfAssessmentItemContent?>" class="md-input label-fixed" />
										</div>
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
						<h3 class="heading_c uk-margin-medium-bottom"><?=$상태설정[$LangID]?></h3>
						
						<div class="uk-form-row" style="display:none;">
							<input type="checkbox" id="SelfAssessmentItemView" name="SelfAssessmentItemView" value="1" <?php if ($SelfAssessmentItemView==1) { echo "checked";}?> data-switchery/>
							<label for="SelfAssessmentItemView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">
						
						
						<div class="uk-form-row">
							<input type="checkbox" id="SelfAssessmentItemState" name="SelfAssessmentItemState" value="1" <?php if ($SelfAssessmentItemState==1) { echo "checked";}?> data-switchery/>
							<label for="SelfAssessmentItemState" class="inline-label"><?=$항목설명[$LangID]?></label>
						</div>
						<hr class="md-hr">
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

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<script language="javascript">

function FormSubmit(){

	obj = document.RegForm.SelfAssessmentItemTitle;
	if (obj.value==""){
		UIkit.modal.alert("항목을 입력하세요.");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "self_assessment_item_action.php";
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