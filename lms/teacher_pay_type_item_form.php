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
$TeacherPayTypeItemID = isset($_REQUEST["TeacherPayTypeItemID"]) ? $_REQUEST["TeacherPayTypeItemID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : ""; 


if ($TeacherPayTypeItemID!=""){
	$Sql = "
			select 
				* 
			from TeacherPayTypeItems where TeacherPayTypeItemID=:TeacherPayTypeItemID"; // limit $StartRowNum, $PageListNum";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherPayTypeItemID', $TeacherPayTypeItemID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	$Row = $Stmt->fetch();

	$TeacherPayTypeItemTitle = $Row['TeacherPayTypeItemTitle'];
	$TeacherPayTypeItemContent = $Row['TeacherPayTypeItemContent'];
	$TeacherPayTypeItemCenterPriceX = $Row['TeacherPayTypeItemCenterPriceX'];
	$TeacherPayTypeItemState = $Row['TeacherPayTypeItemState'];
	$TeacherPayTypeItemView = $Row['TeacherPayTypeItemView'];
	$NationalFlagFile = "/images/".$Row['NationalFlagFile'];
}else{
	$TeacherPayTypeItemTitle = "";
	$TeacherPayTypeItemContent = "";
	$TeacherPayTypeItemCenterPriceX = 1;
	$TeacherPayTypeItemState = 1;
	$TeacherPayTypeItemView = 1;
	$NationalFlagFile = "";
}
?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="TeacherPayTypeItemID" value="<?=$TeacherPayTypeItemID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$TeacherPayTypeItemTitle?></span><span class="sub-heading" id="user_edit_position"><?=$강사_출신지역관리_설정[$LangID]?></span></h2>
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
										<?=$출신지역[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-3-10">
											<label for="TeacherPayTypeItemTitle"><?=$지역명[$LangID]?></label>
											<input type="text" id="TeacherPayTypeItemTitle" name="TeacherPayTypeItemTitle" value="<?=$TeacherPayTypeItemTitle?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-2-10">
											<label for="TeacherPayTypeItemCenterPriceX"><?=$기본수강료_배수[$LangID]?></label>
											<input type="text" id="TeacherPayTypeItemCenterPriceX" name="TeacherPayTypeItemCenterPriceX" value="<?=$TeacherPayTypeItemCenterPriceX?>" class="md-input label-fixed allownumericwithdecimal" />
										</div>
										<div class="uk-width-medium-5-10">
											<label for="TeacherPayTypeItemContent"><?=$설명[$LangID]?></label>
											<input type="text" id="TeacherPayTypeItemContent" name="TeacherPayTypeItemContent" value="<?=$TeacherPayTypeItemContent?>" class="md-input label-fixed" />
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
							<input type="checkbox" id="TeacherPayTypeItemView" name="TeacherPayTypeItemView" value="1" <?php if ($TeacherPayTypeItemView==1) { echo "checked";}?> data-switchery/>
							<label for="TeacherPayTypeItemView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">
						<div class="uk-form-row">
							<?php 
							if ($NationalFlagFile != "") {
							?>
							<b>국기</b> <img src="<?=$NationalFlagFile?>" style="width:80px;">
							<? } ?>
							<label for="NationalFlagFile" class="inline-label">국기파일 업로드 : </label>
							<input type="file" id="NationalFlagFile" name="NationalFlagFile" />
						</div>
						
						<div class="uk-form-row">
							<input type="checkbox" id="TeacherPayTypeItemState" name="TeacherPayTypeItemState" value="1" <?php if ($TeacherPayTypeItemState==1) { echo "checked";}?> data-switchery/>
							<label for="TeacherPayTypeItemState" class="inline-label"><?=$운영중[$LangID]?></label>
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

	obj = document.RegForm.TeacherPayTypeItemTitle;
	if (obj.value==""){
		UIkit.modal.alert("<?=$지역명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "teacher_pay_type_item_action.php";
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