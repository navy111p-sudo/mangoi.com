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
$Hr_OrganTask1ID = isset($_REQUEST["Hr_OrganTask1ID"]) ? $_REQUEST["Hr_OrganTask1ID"] : "";
$Hr_OrganTask2ID = isset($_REQUEST["Hr_OrganTask2ID"]) ? $_REQUEST["Hr_OrganTask2ID"] : "";

$Sql = "
		select 
			A.*
		from Hr_OrganTask1 A 

		where A.Hr_OrganTask1ID=:Hr_OrganTask1ID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':Hr_OrganTask1ID', $Hr_OrganTask1ID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Hr_OrganTask1Name = $Row["Hr_OrganTask1Name"];


if ($Hr_OrganTask2ID!=""){
	$Sql = "
			select 
				A.*
			from Hr_OrganTask2 A 

			where A.Hr_OrganTask2ID=:Hr_OrganTask2ID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_OrganTask2ID', $Hr_OrganTask2ID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();

	$Hr_OrganLevel = $Row["Hr_OrganLevel"];
	$Hr_OrganTask2Name = $Row["Hr_OrganTask2Name"];
	$Hr_OrganTaskCheckGoalTypeID = $Row["Hr_OrganTaskCheckGoalTypeID"];
	$Hr_OrganTaskCheckPerformTypeID = $Row["Hr_OrganTaskCheckPerformTypeID"];
	$Hr_OrganTaskCheckAbilityTypeID = $Row["Hr_OrganTaskCheckAbilityTypeID"];
	$Hr_OrganTask2KpiRatio1 = $Row["Hr_OrganTask2KpiRatio1"];
	$Hr_OrganTask2KpiRatio2 = $Row["Hr_OrganTask2KpiRatio2"];
	$Hr_OrganTask2CompetencyRatio1 = $Row["Hr_OrganTask2CompetencyRatio1"];
	$Hr_OrganTask2CompetencyRatio2 = $Row["Hr_OrganTask2CompetencyRatio2"];
	$Hr_OrganTask2CompetencyRatio3 = $Row["Hr_OrganTask2CompetencyRatio3"];
	$Hr_OrganTask2State = $Row["Hr_OrganTask2State"];

}else{
	$Hr_OrganLevel = 1;
	$Hr_OrganTask2Name = "";
	$Hr_OrganTaskCheckGoalTypeID = 1;
	$Hr_OrganTaskCheckPerformTypeID = 1;
	$Hr_OrganTaskCheckAbilityTypeID = 1;
	$Hr_OrganTask2KpiRatio1 = 0;
	$Hr_OrganTask2KpiRatio2 = 0;
	$Hr_OrganTask2CompetencyRatio1 = 0;
	$Hr_OrganTask2CompetencyRatio2 = 0;
	$Hr_OrganTask2CompetencyRatio3 = 0;
	$Hr_OrganTask2State = 1;
}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="Hr_OrganTask1ID" value="<?=$Hr_OrganTask1ID?>">
		<input type="hidden" name="Hr_OrganTask2ID" value="<?=$Hr_OrganTask2ID?>">

		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$직무관리[$LangID]?></span><span class="sub-heading" id="user_edit_position"><?=$Hr_OrganTask1Name?></span></h2>
						</div>
					</div>
					<div class="user_content">


						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-2">
									<label for="Hr_OrganTask2Name">직무명</label>
									<input type="text" id="Hr_OrganTask2Name" name="Hr_OrganTask2Name" value="<?=$Hr_OrganTask2Name?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-margin-top" data-uk-grid-margin>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganLevel" id="Hr_OrganLevel1" value="1" <?if ($Hr_OrganLevel==1){?>checked<?}?>/>
									<label for="Hr_OrganLevel1" class="radio_label"><span class="radio_bullet"></span>LEVEL 1(경영진)</label>
								</span>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganLevel" id="Hr_OrganLevel2" value="2" <?if ($Hr_OrganLevel==2){?>checked<?}?>/>
									<label for="Hr_OrganLevel2" class="radio_label"><span class="radio_bullet"></span>LEVEL 2(부문)</label>
								</span>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganLevel" id="Hr_OrganLevel3" value="3" <?if ($Hr_OrganLevel==3){?>checked<?}?>/>
									<label for="Hr_OrganLevel3" class="radio_label"><span class="radio_bullet"></span>LEVEL 3(부서)</label>
								</span>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganLevel" id="Hr_OrganLevel4" value="4" <?if ($Hr_OrganLevel==4){?>checked<?}?>/>
									<label for="Hr_OrganLevel4" class="radio_label"><span class="radio_bullet"></span>LEVEL 4(파트)</label>
								</span>
							</div>
						</div>
						<hr>


						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="Hr_OrganTaskCheckGoalTypeID" style="padding:2px 0;"><?=$목표설정_기간[$LangID]?></label>
									<select id="Hr_OrganTaskCheckGoalTypeID" name="Hr_OrganTaskCheckGoalTypeID" style="width:200px;height:32px;border:1px solid #e0e0e0; padding-left:8px;">
									<?
									$Sql2 = "select 
													A.* 
											from Hr_OrganTaskCheckGoalTypes A 
											where A.Hr_OrganTaskCheckGoalTypeState=1 
											order by A.Hr_OrganTaskCheckGoalTypeOrder asc";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

									while($Row2 = $Stmt2->fetch()) {
									?>
									<option value="<?=$Row2["Hr_OrganTaskCheckGoalTypeID"]?>" <?if ($Row2["Hr_OrganTaskCheckGoalTypeID"]==$Hr_OrganTaskCheckGoalTypeID){?>selected<?}?>><?=$Row2["Hr_OrganTaskCheckGoalTypeName"]?></option>
									<?
									}
									$Stmt2 = null;
									?>
									</select>
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="Hr_OrganTaskCheckPerformTypeID" style="padding:2px 0;"><?=$업적평가_기간[$LangID]?></label>
									<select id="Hr_OrganTaskCheckPerformTypeID" name="Hr_OrganTaskCheckPerformTypeID" style="width:200px;height:32px;border:1px solid #e0e0e0; padding-left:8px;">
									<?
									$Sql2 = "select 
													A.* 
											from Hr_OrganTaskCheckPerformTypes A 
											where A.Hr_OrganTaskCheckPerformTypeState=1 
											order by A.Hr_OrganTaskCheckPerformTypeOrder asc";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

									while($Row2 = $Stmt2->fetch()) {
									?>
									<option value="<?=$Row2["Hr_OrganTaskCheckPerformTypeID"]?>" <?if ($Row2["Hr_OrganTaskCheckPerformTypeID"]==$Hr_OrganTaskCheckPerformTypeID){?>selected<?}?>><?=$Row2["Hr_OrganTaskCheckPerformTypeName"]?></option>
									<?
									}
									$Stmt2 = null;
									?>
									</select>
								</div>
							</div>
						</div>
						<hr>


						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="Hr_OrganTaskCheckAbilityTypeID" style="padding:2px 0;"><?=$업적평가_기간[$LangID]?></label>
									<select id="Hr_OrganTaskCheckAbilityTypeID" name="Hr_OrganTaskCheckAbilityTypeID" style="width:200px;height:32px;border:1px solid #e0e0e0; padding-left:8px;">
									<?
									$Sql2 = "select 
													A.* 
											from Hr_OrganTaskCheckAbilityTypes A 
											where A.Hr_OrganTaskCheckAbilityTypeState=1 
											order by A.Hr_OrganTaskCheckAbilityTypeOrder asc";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

									while($Row2 = $Stmt2->fetch()) {
									?>
									<option value="<?=$Row2["Hr_OrganTaskCheckAbilityTypeID"]?>" <?if ($Row2["Hr_OrganTaskCheckAbilityTypeID"]==$Hr_OrganTaskCheckAbilityTypeID){?>selected<?}?>><?=$Row2["Hr_OrganTaskCheckAbilityTypeName"]?></option>
									<?
									}
									$Stmt2 = null;
									?>
									</select>
								</div>
							</div>
						</div>
						<hr>



						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-3-10">
									<label for="Hr_OrganTask2KpiRatio1"><?=$업적평가_개인평가[$LangID]?></label>
									<input type="text" id="Hr_OrganTask2KpiRatio1" name="Hr_OrganTask2KpiRatio1" value="<?=$Hr_OrganTask2KpiRatio1?>" class="md-input label-fixed allownumericwithoutdecimal"/>
								</div>

								<div class="uk-width-medium-3-10">
									<label for="Hr_OrganTask2KpiRatio2"><?=$업적평가_부서평가[$LangID]?></label>
									<input type="text" id="Hr_OrganTask2KpiRatio2" name="Hr_OrganTask2KpiRatio2" value="<?=$Hr_OrganTask2KpiRatio2?>" class="md-input label-fixed allownumericwithoutdecimal"/>
								</div>
							</div>
						</div>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-3-10">
									<label for="Hr_OrganTask2CompetencyRatio1"><?=$역량평가_부하평가[$LangID]?></label>
									<input type="text" id="Hr_OrganTask2CompetencyRatio1" name="Hr_OrganTask2CompetencyRatio1" value="<?=$Hr_OrganTask2CompetencyRatio1?>" class="md-input label-fixed allownumericwithoutdecimal"/>
								</div>

								<div class="uk-width-medium-3-10">
									<label for="Hr_OrganTask2CompetencyRatio2"><?=$역량평가_동료평가[$LangID]?></label>
									<input type="text" id="Hr_OrganTask2CompetencyRatio2" name="Hr_OrganTask2CompetencyRatio2" value="<?=$Hr_OrganTask2CompetencyRatio2?>" class="md-input label-fixed allownumericwithoutdecimal"/>
								</div>

								<div class="uk-width-medium-3-10">
									<label for="Hr_OrganTask2CompetencyRatio3"><?=$역량평가_상사평가[$LangID]?></label>
									<input type="text" id="Hr_OrganTask2CompetencyRatio3" name="Hr_OrganTask2CompetencyRatio3" value="<?=$Hr_OrganTask2CompetencyRatio3?>" class="md-input label-fixed allownumericwithoutdecimal"/>
								</div>
							</div>
						</div>



						
						<div class="uk-margin-top" style="display:<?if ($Hr_OrganTask2ID==""){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganTask2State" id="Hr_OrganTask2State1" value="1" <?if ($Hr_OrganTask2State==1){?>checked<?}?>/>
									<label for="Hr_OrganTask2State1" class="radio_label"><span class="radio_bullet"></span>사용</label>
								</span>
								<!--
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganTask2State" id="Hr_OrganTask2State2" value="2" <?if ($Hr_OrganTask2State==2){?>checked<?}?>/>
									<label for="Hr_OrganTask2State2" class="radio_label"><span class="radio_bullet"></span>미사용</label>
								</span>
								-->
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganTask2State" id="Hr_OrganTask2State0" value="0" <?if ($Hr_OrganTask2State==0){?>checked<?}?>/>
									<label for="Hr_OrganTask2State0" class="radio_label"><span class="radio_bullet"></span>삭제</label>
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
	obj = document.RegForm.Hr_OrganTask2Name;
	if (obj.value==""){
		UIkit.modal.alert("<?=$직무명을_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "hr_organ_task_2_action.php";
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