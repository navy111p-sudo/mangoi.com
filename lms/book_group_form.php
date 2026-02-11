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
$BookGroupID = isset($_REQUEST["BookGroupID"]) ? $_REQUEST["BookGroupID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : ""; 


if ($BookGroupID!=""){
	$Sql = "
			select 
				* 
			from BookGroups where BookGroupID=:BookGroupID"; // limit $StartRowNum, $PageListNum";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookGroupID', $BookGroupID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	$Row = $Stmt->fetch();

	$BookGroupName = $Row['BookGroupName'];
	$BookGroupMemo = $Row['BookGroupMemo'];
	$BookGroupState = $Row['BookGroupState'];
	$BookGroupView = $Row['BookGroupView'];
}else{
	$BookGroupName = "";
	$BookGroupMemo = "";
	$BookGroupState = 1;
	$BookGroupView = 1;
}
?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="BookGroupID" value="<?=$BookGroupID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$BookGroupName?></span><span class="sub-heading" id="user_edit_position"><?=$교재그룹_설정[$LangID]?></span></h2>
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
										<?=$교재그룹_관리[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-10-10">
											<label for="BookGroupName"><?=$교재그룹명[$LangID]?></label>
											<input type="text" id="BookGroupName" name="BookGroupName" value="<?=$BookGroupName?>" class="md-input label-fixed"/>
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<label for="BookGroupMemo"><?=$설명[$LangID]?></label>
											<textarea class="md-input" name="BookGroupMemo" id="BookGroupMemo" cols="30" rows="4"><?=$BookGroupMemo?></textarea>
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
							<input type="checkbox" id="BookGroupView" name="BookGroupView" value="1" <?php if ($BookGroupView==1) { echo "checked";}?> data-switchery/>
							<label for="BookGroupView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">
						
						
						<div class="uk-form-row">
							<input type="checkbox" id="BookGroupState" name="BookGroupState" value="1" <?php if ($BookGroupState==1) { echo "checked";}?> data-switchery/>
							<label for="BookGroupState" class="inline-label"><?=$사용중[$LangID]?></label>
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

	obj = document.RegForm.BookGroupName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$항목을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "book_group_action.php";
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