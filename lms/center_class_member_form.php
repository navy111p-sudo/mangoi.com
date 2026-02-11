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
$CenterClassMemberID = isset($_REQUEST["CenterClassMemberID"]) ? $_REQUEST["CenterClassMemberID"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : ""; 


if ($CenterClassMemberID!=""){
	$Sql = "
			select 
				*, 
				B.CenterClassName,
				C.MemberID, 
				C.MemberName

			from CenterClassMembers A 
			inner join CenterClasses B on A.CenterClassID=B.CenterClassID 
			inner join Members C on A.MemberID=C.MemberID 
			where CenterClassMemberID=:CenterClassMemberID"; // limit $StartRowNum, $PageListNum";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterClassMemberID', $CenterClassMemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	$Row = $Stmt->fetch();

	
	$CenterClassID = $Row["CenterClassID"];
	$CenterClassMemberRegDateTime = $Row['CenterClassMemberRegDateTime'];
	$CenterClassMemberState = $Row['CenterClassMemberState'];
	$CenterClassMemberView = $Row['CenterClassMemberView'];
	$MemberID = $Row['MemberID'];
	$MemberName = $Row["MemberName"];
} else {
	$CenterClassID = "";
	$CenterClassMemberRegDateTime = "";
	$CenterClassMemberState = 1;
	$CenterClassMemberView = 1;
	$MemberID = 0;
	$MemberName = "";
}

?>

<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="CenterClassMemberID" value="<?=$CenterClassMemberID?>">
		<input type="hidden" name="MemberID" value="<?=$MemberID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="CenterID" value="<?=$CenterID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"> <!-- <?=$CenterClassMemberID?> --> </span><span class="sub-heading" id="user_edit_position"><?=$수업인원구성관리[$LangID]?></span></h2>
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
										<?=$인원구성관리[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10">
											<select name="CenterClassID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" style="width:100%;">
											<option value=""><?=$수업명[$LangID]?></option>
											<?php
											$Sql = "select A.CenterClassID, A.CenterClassName from CenterClasses A where A.CenterID=:CenterID";
											
											$Stmt = $DbConn->prepare($Sql);
											$Stmt->bindParam(":CenterID", $CenterID);
											$Stmt->execute();
											$Stmt->setFetchMode(PDO::FETCH_ASSOC);
											while($Row = $Stmt->fetch()) {
												$TempCenterClassID = $Row["CenterClassID"];
												$TempCenterClassName = $Row["CenterClassName"];
											?>
											<option value=<?=$TempCenterClassID?> <?if ($CenterClassID==$TempCenterClassID){?>selected<?}?>><?=$TempCenterClassName?></option>
											<?php } ?>
											</select>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="MemberName"><?=$학생명[$LangID]?></label>
											<input type="text" id="MemberName" name="MemberName" value="<?if ($CenterClassID!=''){echo $MemberName;}?>" class="md-input label-fixed" disabled/>
										</div>
										<div class="uk-width-medium-3-10">
											<div class="uk-form-row">
												<a type="button" href="javascript:OpenCenterClassMemberRegForm(<?=$CenterID?>)" class="md-btn md-btn-primary"><?=$학생등록[$LangID]?></a>
											</div>
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
							<input type="checkbox" id="CenterClassMemberView" name="CenterClassMemberView" value="1" <?php if ($CenterClassMemberView==1) { echo "checked";}?> data-switchery/>
							<label for="CenterClassMemberView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">
						
						
						<div class="uk-form-row">
							<input type="checkbox" id="CenterClassMemberState" name="CenterClassMemberState" value="1" <?php if ($CenterClassMemberState==1) { echo "checked";}?> data-switchery/>
							<label for="CenterClassMemberState" class="inline-label"><?=$운영중[$LangID]?></label>
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

	obj = document.RegForm.CenterClassID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$수업을_선택하세요[$LangID]?>");
		obj.focus();
		return;
	}


	obj = document.RegForm.MemberID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$학생을_등록하세요[$LangID]?>");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "center_class_member_action.php";
			document.RegForm.submit();
		}
	);

}

function OpenCenterClassMemberRegForm(CenterID){

	openurl = "center_class_member_form_open.php?CenterID="+CenterID;
	//openurl = "ajax_set_center_class_member_form.php?CenterClassMemberID="+CenterClassMemberID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

</script>




<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>