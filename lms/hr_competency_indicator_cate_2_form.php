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
$Hr_CompetencyIndicatorCate1ID = isset($_REQUEST["Hr_CompetencyIndicatorCate1ID"]) ? $_REQUEST["Hr_CompetencyIndicatorCate1ID"] : "";
$Hr_CompetencyIndicatorCate2ID = isset($_REQUEST["Hr_CompetencyIndicatorCate2ID"]) ? $_REQUEST["Hr_CompetencyIndicatorCate2ID"] : "";

$Sql = "
		select 
			A.*
		from Hr_CompetencyIndicatorCate1 A 

		where A.Hr_CompetencyIndicatorCate1ID=:Hr_CompetencyIndicatorCate1ID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':Hr_CompetencyIndicatorCate1ID', $Hr_CompetencyIndicatorCate1ID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Hr_CompetencyIndicatorCate1Name = $Row["Hr_CompetencyIndicatorCate1Name"];


if ($Hr_CompetencyIndicatorCate2ID!=""){
	$Sql = "
			select 
				A.*
			from Hr_CompetencyIndicatorCate2 A 

			where A.Hr_CompetencyIndicatorCate2ID=:Hr_CompetencyIndicatorCate2ID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Hr_CompetencyIndicatorCate1ID = $Row["Hr_CompetencyIndicatorCate1ID"];
	$Hr_CompetencyIndicatorCate2Name = $Row["Hr_CompetencyIndicatorCate2Name"];
	$Hr_CompetencyIndicatorCate2State = $Row["Hr_CompetencyIndicatorCate2State"];

}else{
	$Hr_CompetencyIndicatorCate2Name = "";
	$Hr_CompetencyIndicatorCate2State = 1;
}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="Hr_CompetencyIndicatorCate1ID" value="<?=$Hr_CompetencyIndicatorCate1ID?>">
		<input type="hidden" name="Hr_CompetencyIndicatorCate2ID" value="<?=$Hr_CompetencyIndicatorCate2ID?>">

		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$역량관리[$LangID]?></span><span class="sub-heading" id="user_edit_position"><?=$Hr_CompetencyIndicatorCate1Name?></span></h2>
						</div>
					</div>
					<div class="user_content">


						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-2">
									<label for="Hr_CompetencyIndicatorCate2Name"><?=$역량명[$LangID]?></label>
									<input type="text" id="Hr_CompetencyIndicatorCate2Name" name="Hr_CompetencyIndicatorCate2Name" value="<?=$Hr_CompetencyIndicatorCate2Name?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>


						
						<div class="uk-margin-top" style="display:<?if ($Hr_CompetencyIndicatorCate2ID==""){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_CompetencyIndicatorCate2State" id="Hr_CompetencyIndicatorCate2State1" value="1" <?if ($Hr_CompetencyIndicatorCate2State==1){?>checked<?}?>/>
									<label for="Hr_CompetencyIndicatorCate2State1" class="radio_label"><span class="radio_bullet"></span><?=$사용[$LangID]?></label>
								</span>
								<!--
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_CompetencyIndicatorCate2State" id="Hr_CompetencyIndicatorCate2State2" value="2" <?if ($Hr_CompetencyIndicatorCate2State==2){?>checked<?}?>/>
									<label for="Hr_CompetencyIndicatorCate2State2" class="radio_label"><span class="radio_bullet"></span><?=$미사용[$LangID]?></label>
								</span>
								-->
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_CompetencyIndicatorCate2State" id="Hr_CompetencyIndicatorCate2State0" value="0" <?if ($Hr_CompetencyIndicatorCate2State==0){?>checked<?}?>/>
									<label for="Hr_CompetencyIndicatorCate2State0" class="radio_label"><span class="radio_bullet"></span><?=$삭제[$LangID]?></label>
								</span>
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
	obj = document.RegForm.Hr_CompetencyIndicatorCate2Name;
	if (obj.value==""){
		UIkit.modal.alert("<?=$역량군명을_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "hr_competency_indicator_cate_2_action.php";
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