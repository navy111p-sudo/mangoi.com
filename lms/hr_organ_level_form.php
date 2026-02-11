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
$Hr_OrganLevelID = isset($_REQUEST["Hr_OrganLevelID"]) ? $_REQUEST["Hr_OrganLevelID"] : "";


if ($Hr_OrganLevelID!=""){
	$Sql = "
			select 
				A.*
			from Hr_OrganLevels A 

			where A.Hr_OrganLevelID=:Hr_OrganLevelID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_OrganLevelID', $Hr_OrganLevelID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();

	$CenterID = $Row["CenterID"];
	$Hr_OrganLevel1ID = $Row["Hr_OrganLevel1ID"];
	$Hr_OrganLevel2ID = $Row["Hr_OrganLevel2ID"];
	$Hr_OrganLevel3ID = $Row["Hr_OrganLevel3ID"];
	$Hr_OrganLevel4ID = $Row["Hr_OrganLevel4ID"];

	$Hr_Incentive1 = $Row["Hr_Incentive1"];
	$Hr_Incentive2 = $Row["Hr_Incentive2"];
	$Hr_Incentive3 = $Row["Hr_Incentive3"];
	$Hr_Incentive4 = $Row["Hr_Incentive4"];
	$Hr_Incentive5 = $Row["Hr_Incentive5"];

	$Hr_OrganLevel = $Row["Hr_OrganLevel"];
	$Hr_OrganLevelName = $Row["Hr_OrganLevelName"];
	$Hr_OrganLevelState = $Row["Hr_OrganLevelState"];

}else{
	$CenterID = 0;
	$Hr_OrganLevel1ID = 0;
	$Hr_OrganLevel2ID = 0;
	$Hr_OrganLevel3ID = 0;
	$Hr_OrganLevel4ID = 0;

	$Hr_Incentive1 = 0;
	$Hr_Incentive2 = 0;
	$Hr_Incentive3 = 0;
	$Hr_Incentive4 = 0;
	$Hr_Incentive5 = 0;


	$Hr_OrganLevel = 0;
	$Hr_OrganLevelName = "";
	$Hr_OrganLevelState = 1;
}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="CenterID" value="<?=$CenterID?>">
		<input type="hidden" name="Hr_OrganLevelID" value="<?=$Hr_OrganLevelID?>">
		<input type="hidden" name="Hr_OrganLevel4ID" value="<?=$Hr_OrganLevel4ID?>">
		<input type="hidden" name="Hr_OrganLevel_Check" value="<?=$Hr_OrganLevel?>">

		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$조직관리[$LangID]?></span><span class="sub-heading" id="user_edit_position"><!--베스트강사--></span></h2>
						</div>
					</div>
					<div class="user_content">

						<div class="uk-margin-top">
							<div class="uk-margin-top" data-uk-grid-margin>
								<?if ($Hr_OrganLevelID!=""){?>

									<?if ($Hr_OrganLevel==1){?>
									LEVEL 1(경영진)
									<?}else if ($Hr_OrganLevel==2){?>
									LEVEL 2(부문)
									<?}else if ($Hr_OrganLevel==3){?>
									LEVEL 3(부서)
									<?}else if ($Hr_OrganLevel==4){?>
									LEVEL 4(파트)
									<?}?>
									<input type="hidden" name="Hr_OrganLevel" value="<?=$Hr_OrganLevel?>">

								<?}else{?>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganLevel" id="Hr_OrganLevel1" value="1" <?if ($Hr_OrganLevel==1){?>checked<?}?> onclick="Check_Hr_OrganLevel(1)"/>
									<label for="Hr_OrganLevel1" class="radio_label"><span class="radio_bullet"></span>LEVEL 1(경영진)</label>
								</span>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganLevel" id="Hr_OrganLevel2" value="2" <?if ($Hr_OrganLevel==2){?>checked<?}?> onclick="Check_Hr_OrganLevel(2)"/>
									<label for="Hr_OrganLevel2" class="radio_label"><span class="radio_bullet"></span>LEVEL 2(부문)</label>
								</span>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganLevel" id="Hr_OrganLevel3" value="3" <?if ($Hr_OrganLevel==3){?>checked<?}?> onclick="Check_Hr_OrganLevel(3)"/>
									<label for="Hr_OrganLevel3" class="radio_label"><span class="radio_bullet"></span>LEVEL 3(부서)</label>
								</span>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganLevel" id="Hr_OrganLevel4" value="4" <?if ($Hr_OrganLevel==4){?>checked<?}?> onclick="Check_Hr_OrganLevel(4)"/>
									<label for="Hr_OrganLevel4" class="radio_label"><span class="radio_bullet"></span>LEVEL 4(파트)</label>
								</span>
								<?}?>
							</div>
						</div>

						<hr>


						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-2">
									<label for="Hr_OrganLevelName"><?=$조직명[$LangID]?></label>
									<input type="text" id="Hr_OrganLevelName" name="Hr_OrganLevelName" value="<?=$Hr_OrganLevelName?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>



						<div class="uk-margin-top" id="Div_Hr_OrganLevel_1" style="display:<?if ($Hr_OrganLevel<1){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="Hr_OrganLevel1ID" style="padding:2px 5px 2px 0;">소속 LEVEL 1</label>
									<select id="Hr_OrganLevel1ID" name="Hr_OrganLevel1ID" style="width:200px;height:32px;border:1px solid #e0e0e0; padding-left:8px; margin:3px 0;">
									<?
									$Sql2 = "select 
													A.* 
											from Hr_OrganLevels A 
											where 
												A.Hr_OrganLevel=1 
												and (A.Hr_OrganLevelState=1 or A.Hr_OrganLevel1ID=:Hr_OrganLevel1ID)
											order by A.Hr_OrganLevelName asc";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->bindParam(':Hr_OrganLevel1ID', $Hr_OrganLevel1ID);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

									while($Row2 = $Stmt2->fetch()) {
									?>
									<option value="<?=$Row2["Hr_OrganLevelID"]?>" <?if ($Row2["Hr_OrganLevelID"]==$Hr_OrganLevel1ID){?>selected<?}?>><?=$Row2["Hr_OrganLevelName"]?></option>
									<?
									}
									$Stmt2 = null;
									?>
									</select>
								</div>
							</div>
						</div>


						<div class="uk-margin-top" id="Div_Hr_OrganLevel_2" style="display:<?if ($Hr_OrganLevel<2){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="Hr_OrganLevel2ID" style="padding:2px 5px 2px 0;">소속 LEVEL 2</label>
									<select id="Hr_OrganLevel2ID" name="Hr_OrganLevel2ID" style="width:200px;height:32px; border:1px solid #e0e0e0; padding-left:8px; margin:3px 0;">
									<option value="0">선택안함</option>
									<?
									$Sql2 = "select 
													A.* 
											from Hr_OrganLevels A 
											where 
												A.Hr_OrganLevel=2 
												and (A.Hr_OrganLevelState=1 or A.Hr_OrganLevel2ID=:Hr_OrganLevel2ID)
											order by A.Hr_OrganLevelName asc";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->bindParam(':Hr_OrganLevel2ID', $Hr_OrganLevel2ID);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

									while($Row2 = $Stmt2->fetch()) {
									?>
									<option value="<?=$Row2["Hr_OrganLevelID"]?>" <?if ($Row2["Hr_OrganLevelID"]==$Hr_OrganLevel2ID){?>selected<?}?>><?=$Row2["Hr_OrganLevelName"]?></option>
									<?
									}
									$Stmt2 = null;
									?>
									</select>
								</div>
							</div>
						</div>

						<div class="uk-margin-top" id="Div_Hr_OrganLevel_3" style="display:<?if ($Hr_OrganLevel<3){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="Hr_OrganLevel3ID" style="padding:2px 5px 2px 0;">소속 LEVEL 3</label>
									<select id="Hr_OrganLevel3ID" name="Hr_OrganLevel3ID" style="width:200px;height:32px;border:1px solid #e0e0e0; padding-left:8px; margin:3px 0;">
									<option value="0"><?=$선택안함[$LangID]?></option>
									<?
									$Sql2 = "select 
													A.* 
											from Hr_OrganLevels A 
											where 
												A.Hr_OrganLevel=3 
												and (A.Hr_OrganLevelState=1 or A.Hr_OrganLevel3ID=:Hr_OrganLevel3ID)
											order by A.Hr_OrganLevelName asc";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->bindParam(':Hr_OrganLevel3ID', $Hr_OrganLevel3ID);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

									while($Row2 = $Stmt2->fetch()) {
									?>
									<option value="<?=$Row2["Hr_OrganLevelID"]?>" <?if ($Row2["Hr_OrganLevelID"]==$Hr_OrganLevel3ID){?>selected<?}?>><?=$Row2["Hr_OrganLevelName"]?></option>
									<?
									}
									$Stmt2 = null;
									?>
									</select>
								</div>
							</div>
						</div>

						
						<hr id="Div_Line_Incentive_Top" style="display:<?if ($Hr_OrganLevel<=1){?>none<?}?>;">
						<div class="uk-margin-top" id="Div_Line_Incentive" style="display:<?if ($Hr_OrganLevel<=1){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-5">
									<label for="Hr_Incentive1"><?=$인센티브_S[$LangID]?></label>
									<input type="text" id="Hr_Incentive1" name="Hr_Incentive1" value="<?=$Hr_Incentive1?>" class="md-input label-fixed allownumericwithoutdecimal"/>
								</div>
								<div class="uk-width-medium-1-5">
									<label for="Hr_Incentive2"><?=$인센티브_A[$LangID]?></label>
									<input type="text" id="Hr_Incentive2" name="Hr_Incentive2" value="<?=$Hr_Incentive2?>" class="md-input label-fixed allownumericwithoutdecimal"/>
								</div>
								<div class="uk-width-medium-1-5">
									<label for="Hr_Incentive3"><?=$인센티브_B[$LangID]?></label>
									<input type="text" id="Hr_Incentive3" name="Hr_Incentive3" value="<?=$Hr_Incentive3?>" class="md-input label-fixed allownumericwithoutdecimal"/>
								</div>
								<div class="uk-width-medium-1-5">
									<label for="Hr_Incentive4"><?=$인센티브_C[$LangID]?></label>
									<input type="text" id="Hr_Incentive4" name="Hr_Incentive4" value="<?=$Hr_Incentive4?>" class="md-input label-fixed allownumericwithoutdecimal"/>
								</div>
								<div class="uk-width-medium-1-5">
									<label for="Hr_Incentive5"><?=$인센티브_D[$LangID]?></label>
									<input type="text" id="Hr_Incentive5" name="Hr_Incentive5" value="<?=$Hr_Incentive5?>" class="md-input label-fixed allownumericwithoutdecimal"/>
								</div>
							</div>
						</div>

						<hr style="display:<?if ($Hr_OrganLevelID==""){?>none<?}?>;">
						<div class="uk-margin-top" style="display:<?if ($Hr_OrganLevelID==""){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganLevelState" id="Hr_OrganLevelState1" value="1" <?if ($Hr_OrganLevelState==1){?>checked<?}?>/>
									<label for="Hr_OrganLevelState1" class="radio_label"><span class="radio_bullet"></span>사용</label>
								</span>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganLevelState" id="Hr_OrganLevelState2" value="2" <?if ($Hr_OrganLevelState==2){?>checked<?}?>/>
									<label for="Hr_OrganLevelState2" class="radio_label"><span class="radio_bullet"></span>미사용</label>
								</span>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_OrganLevelState" id="Hr_OrganLevelState0" value="0" <?if ($Hr_OrganLevelState==0){?>checked<?}?>/>
									<label for="Hr_OrganLevelState0" class="radio_label"><span class="radio_bullet"></span>삭제</label>
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

function Check_Hr_OrganLevel(Hr_OrganLevel){
	document.RegForm.Hr_OrganLevel_Check.value = Hr_OrganLevel;

	if (Hr_OrganLevel==1){
		document.getElementById("Div_Line_Incentive_Top").style.display = "none";
		document.getElementById("Div_Line_Incentive").style.display = "none";

		document.getElementById("Div_Hr_OrganLevel_1").style.display = "none";
		document.getElementById("Div_Hr_OrganLevel_2").style.display = "none";
		document.getElementById("Div_Hr_OrganLevel_3").style.display = "none";
	}else if (Hr_OrganLevel==2){
		document.getElementById("Div_Line_Incentive_Top").style.display = "";
		document.getElementById("Div_Line_Incentive").style.display = "";

		document.getElementById("Div_Hr_OrganLevel_1").style.display = "";
		document.getElementById("Div_Hr_OrganLevel_2").style.display = "none";
		document.getElementById("Div_Hr_OrganLevel_3").style.display = "none";	
	}else if (Hr_OrganLevel==3){
		document.getElementById("Div_Line_Incentive_Top").style.display = "";
		document.getElementById("Div_Line_Incentive").style.display = "";

		document.getElementById("Div_Hr_OrganLevel_1").style.display = "";
		document.getElementById("Div_Hr_OrganLevel_2").style.display = "";
		document.getElementById("Div_Hr_OrganLevel_3").style.display = "none";	
	}else if (Hr_OrganLevel==4){
		document.getElementById("Div_Line_Incentive_Top").style.display = "";
		document.getElementById("Div_Line_Incentive").style.display = "";

		document.getElementById("Div_Hr_OrganLevel_1").style.display = "";
		document.getElementById("Div_Hr_OrganLevel_2").style.display = "";
		document.getElementById("Div_Hr_OrganLevel_3").style.display = "";	
	}
}


function FormSubmit(){

	Hr_OrganLevel = document.RegForm.Hr_OrganLevel_Check.value;
	


	obj = document.RegForm.Hr_OrganLevelName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$조직명을_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.Hr_OrganLevel_Check;
	if (obj.value=="0"){
		UIkit.modal.alert("LEVEL을 선택하세요");
		obj.focus();
		return;
	}


	if (Hr_OrganLevel=="1"){

	}else {

		obj = document.RegForm.Hr_OrganLevel1ID;
		if (obj.value==""){
			UIkit.modal.alert("소속 LEVEL 1을 선택하세요.");
			obj.focus();
			return;
		}

	}

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "hr_organ_level_action.php";
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