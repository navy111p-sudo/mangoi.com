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
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$MemberPointTypeID = isset($_REQUEST["MemberPointTypeID"]) ? $_REQUEST["MemberPointTypeID"] : "";


$Sql = "
		select 
				A.*
		from MemberPointNewTypes A 
		where A.MemberPointTypeID=:MemberPointTypeID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberPointTypeID', $MemberPointTypeID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$MemberPointTypeID = $Row["MemberPointTypeID"];
$MemberPoint = $Row["MemberPoint"];
$MemberPointTypeType = $Row["MemberPointTypeType"];
$MemberPointTypeMethod = $Row["MemberPointTypeMethod"];
$MemberPointTypeName = $Row["MemberPointTypeName"];
$MemberPointTypeText = $Row["MemberPointTypeText"];


?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="MemberPointTypeID" value="<?=$MemberPointTypeID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$포인트_항목_관리[$LangID]?></span><span class="sub-heading" id="user_edit_position"><!--질문관리--></span></h2>
						</div>
					</div>
					<div class="user_content">
						<h3 class="full_width_in_card heading_c"> 
							<?=$포인트_내용[$LangID]?>
						</h3>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-4-5">
									<label for="MemberPointTypeName"><?=$포인트명[$LangID]?></label>
									<input type="text" id="MemberPointTypeName" name="MemberPointTypeName" value="<?=$MemberPointTypeName?>" class="md-input label-fixed" readonly/>
								</div>
								<div class="uk-width-medium-1-5">
									<label for="MemberPoint"><?=$포인트[$LangID]?></label>
									<input type="text" id="MemberPoint" name="MemberPoint" value="<?=$MemberPoint?>" class="md-input label-fixed " style="text-align:center;"/> 
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="MemberPointTypeText"><?=$메시지[$LangID]?></label>
									<input type="text" id="MemberPointTypeText" name="MemberPointTypeText" value="<?=$MemberPointTypeText?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>



						<h3 class="full_width_in_card heading_c"> 
							<?=$대상_및_방식[$LangID]?>
						</h3>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<span class="icheck-inline">
										<input type="radio" name="MemberPointTypeType" id="MemberPointTypeType1" value="1" <?if ($MemberPointTypeType==1){?>checked<?}?> data-md-icheck />
										<label for="MemberPointTypeType1" class="inline-label"><?=$학생[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" name="MemberPointTypeType" id="MemberPointTypeType2" value="2" <?if ($MemberPointTypeType==2){?>checked<?}?> data-md-icheck />
										<label for="MemberPointTypeType2" class="inline-label"><?=$학부모[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" name="MemberPointTypeType" id="MemberPointTypeType3" value="3" <?if ($MemberPointTypeType==3){?>checked<?}?> data-md-icheck />
										<label for="MemberPointTypeType3" class="inline-label"><?=$대리점[$LangID]?></label>
									</span>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<span class="icheck-inline">
										<input type="radio" name="MemberPointTypeMethod" id="MemberPointTypeMethod1" value="1" <?if ($MemberPointTypeMethod==1){?>checked<?}?> data-md-icheck />
										<label for="MemberPointTypeMethod1" class="inline-label"><?=$자동[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" name="MemberPointTypeMethod" id="MemberPointTypeMethod2" value="2" <?if ($MemberPointTypeMethod==2){?>checked<?}?> data-md-icheck />
										<label for="MemberPointTypeMethod2" class="inline-label"><?=$수동[$LangID]?></label>
									</span>
								</div>
							</div>
						</div>


						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
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




	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>', 
		function(){ 
			document.RegForm.action = "point_type_action.php";
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