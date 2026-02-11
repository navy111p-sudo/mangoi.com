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
$CenterClassID = isset($_REQUEST["CenterClassID"]) ? $_REQUEST["CenterClassID"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : ""; 

if ($CenterClassID!=""){
	$Sql = "
			select 
				* 
			from CenterClasses where CenterClassID=:CenterClassID"; // limit $StartRowNum, $PageListNum";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterClassID', $CenterClassID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	$Row = $Stmt->fetch();

	$CenterClassWeekNum = $Row['CenterClassWeekNum'];
	$CenterClassStartTime = $Row['CenterClassStartTime'];
	$CenterClassEndTime = $Row['CenterClassEndTime'];
	$CenterClassName = $Row['CenterClassName'];
	$CenterClassState = $Row['CenterClassState'];
	$CenterClassView = $Row['CenterClassView'];
} else {
	$CenterClassWeekNum = -1;
	$CenterClassStartTime = "";
	$CenterClassEndTime = "";
	$CenterClassName = "";
	$CenterClassState = 1;
	$CenterClassView = 1;
}

?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="CenterClassID" value="<?=$CenterClassID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="CenterID" value="<?=$CenterID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$CenterClassName?></span><span class="sub-heading" id="user_edit_position"><?=$학원수업관리[$LangID]?></span></h2>
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
										<?=$수업관리[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-7-10">
											<label for="CenterClassName"><?=$수업명[$LangID]?></label>
											<input type="text" id="CenterClassName" name="CenterClassName" value="<?=$CenterClassName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-3-10">
											<select name="CenterClassWeekNum" class="uk-width-1-1" data-md-select2 data-allow-clear="true" style="width:100%;">
											<option value=""><?=$요일[$LangID]?></option>
											<option value=1 <?if ($CenterClassWeekNum==1){?>selected<?}?>><?=$월요일[$LangID]?></option>
											<option value=2 <?if ($CenterClassWeekNum==2){?>selected<?}?>><?=$화요일[$LangID]?></option>
											<option value=3 <?if ($CenterClassWeekNum==3){?>selected<?}?>><?=$수요일[$LangID]?></option>
											<option value=4 <?if ($CenterClassWeekNum==4){?>selected<?}?>><?=$목요일[$LangID]?></option>
											<option value=5 <?if ($CenterClassWeekNum==5){?>selected<?}?>><?=$금요일[$LangID]?></option>
											<option value=6 <?if ($CenterClassWeekNum==6){?>selected<?}?>><?=$토요일[$LangID]?></option>
											<option value=0 <?if ($CenterClassWeekNum==0){?>selected<?}?>><?=$일요일[$LangID]?></option>
											</select>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="CenterClassStartTime"><?=$수업시작시간[$LangID]?></label>
											<input type="time" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" id="CenterClassStartTime" name="CenterClassStartTime" value="<?=$CenterClassStartTime?>" class="md-input label-fixed" />
										</div>
										<div class="uk-width-medium-3-10">
											<label for="CenterClassEndTime"><?=$수업종료시간[$LangID]?></label>
											<input type="time" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" id="CenterClassEndTime" name="CenterClassEndTime" value="<?=$CenterClassEndTime?>" class="md-input label-fixed" />
										</div>
										<div class="uk-width-medium-2-10">
											<label for="CenterClassEndTime"></label>
											예제) 14:00, 20:21
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
							<input type="checkbox" id="CenterClassView" name="CenterClassView" value="1" <?php if ($CenterClassView==1) { echo "checked";}?> data-switchery/>
							<label for="CenterClassView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">
						
						
						<div class="uk-form-row">
							<input type="checkbox" id="CenterClassState" name="CenterClassState" value="1" <?php if ($CenterClassState==1) { echo "checked";}?> data-switchery/>
							<label for="CenterClassState" class="inline-label"><?=$운영중[$LangID]?></label>
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

	obj = document.RegForm.CenterClassName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$수업명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.CenterClassWeekNum;
	if (obj.value==""){
		UIkit.modal.alert("<?=$수업요일을_선택하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.CenterClassStartTime;
	if (obj.value==""){
		UIkit.modal.alert("<?=$수업시작시간을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.CenterClassEndTime;
	if (obj.value==""){
		UIkit.modal.alert("<?=$수업종료시간을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "center_class_action.php";
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