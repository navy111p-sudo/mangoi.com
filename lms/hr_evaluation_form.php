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
$Hr_EvaluationID = isset($_REQUEST["Hr_EvaluationID"]) ? $_REQUEST["Hr_EvaluationID"] : "";


if ($Hr_EvaluationID!=""){
	$Sql = "
			select 
				A.*
			from Hr_Evaluations A 

			where A.Hr_EvaluationID=:Hr_EvaluationID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_EvaluationID', $Hr_EvaluationID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();

	$CenterID = $Row["CenterID"];

	$Hr_EvaluationID = $Row["Hr_EvaluationID"];
	$Hr_EvaluationTypeID = $Row["Hr_EvaluationTypeID"];
	$Hr_EvaluationCycleID = $Row["Hr_EvaluationCycleID"];

	$Hr_EvaluationYear = $Row["Hr_EvaluationYear"];
	$Hr_EvaluationMonth = $Row["Hr_EvaluationMonth"];
	$Hr_EvaluationName = $Row["Hr_EvaluationName"];

	$Hr_EvaluationDate = $Row["Hr_EvaluationDate"];
	$Hr_EvaluationStartDate = $Row["Hr_EvaluationStartDate"];
	$Hr_EvaluationEndDate = $Row["Hr_EvaluationEndDate"];
	$Hr_EvaluationGoalStartDate = $Row["Hr_EvaluationGoalStartDate"];
	$Hr_EvaluationGoalEndDate = $Row["Hr_EvaluationGoalEndDate"];

	$Hr_EvaluationUseCompetency = $Row["Hr_EvaluationUseCompetency"];
	$Hr_EvaluationUseScore = $Row["Hr_EvaluationUseScore"];
	$Hr_EvaluationUseWarrant = $Row["Hr_EvaluationUseWarrant"];
	$Hr_EvaluationUseOverall = $Row["Hr_EvaluationUseOverall"];

	$Hr_EvaluationState = $Row["Hr_EvaluationState"];
	


}else{
	$CenterID = 0;

	$Hr_EvaluationID = "";
	$Hr_EvaluationTypeID = 1;
	$Hr_EvaluationCycleID = 1;

	$Hr_EvaluationYear = date("Y");
	$Hr_EvaluationMonth = date("n");
	$Hr_EvaluationName = "평가";

	$Hr_EvaluationDate = date("Y-m-d");
	$Hr_EvaluationStartDate = date("Y-m-d");
	$Hr_EvaluationEndDate = date("Y-m-d");
	$Hr_EvaluationGoalStartDate = date("Y-m-d");
	$Hr_EvaluationGoalEndDate = date("Y-m-d");

	$Hr_EvaluationUseCompetency = 0;
	$Hr_EvaluationUseScore = 1;
	$Hr_EvaluationUseWarrant = 1;
	$Hr_EvaluationUseOverall = 1;

	$Hr_EvaluationState = 1;
}
?>

    
    <style>
        .hr_wrap{display:flex; flex-wrap:wrap; align-items:center;}
        .hr_left{width:100%; padding:5px 0;}
        .hr_right{width:100%; display:flex; flex-wrap:wrap; align-items:center;}
        .hr_input{width:100%; padding:0 10px; height:32px; border:1px solid #ddd; box-sizing:border-box;}
        .hr_text{width:100%; padding:10px; height:100px; border:1px solid #ddd; box-sizing:border-box; color:#666;}
        .hr_right .radio_label{padding:5px 15px 5px 0;}

        @media all and (min-width:640px){
            .hr_left{width:23%; padding:0;}
            .hr_right{width:77%;}
        }
    </style>

<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="CenterID" value="<?=$CenterID?>">
		<input type="hidden" name="Hr_EvaluationID" value="<?=$Hr_EvaluationID?>">

		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname">평가관리</span><span class="sub-heading" id="user_edit_position"><!--베스트강사--></span></h2>
						</div>
					</div>
					<div class="user_content">


						<div class="uk-margin-top" id="Div_Hr_Evaluation_1">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10 hr_wrap">
									<label class="hr_left"><?=$평가명[$LangID]?></label>
                                    
                                    <div class="hr_right">
                                        <div style="display:inline-block; margin:0 2px 2px 2px;">
                                            <select id="Hr_EvaluationYear" name="Hr_EvaluationYear" style="width:100px;height:30px;border:1px solid #cccccc;">
                                            <?
                                            for ($ii=(date("Y")+1);$ii>=2020;$ii--){
                                            ?>
                                            <option value="<?=$ii?>" <?if ($ii==$Hr_EvaluationYear){?>selected<?}?>><?=$ii?></option>
                                            <?
                                            }
                                            ?>
                                            </select> 년 
                                        </div>
                                        <div style="display:inline-block; margin:0 2px 2px 2px;">
                                            <select id="Hr_EvaluationMonth" name="Hr_EvaluationMonth" style="width:100px;height:30px;border:1px solid #cccccc;">
                                            <?
                                            for ($ii=1;$ii<=12;$ii++){
                                            ?>
                                            <option value="<?=$ii?>" <?if ($ii==$Hr_EvaluationMonth){?>selected<?}?>><?=$ii?></option>
                                            <?
                                            }
                                            ?>
                                            </select><?=$월월[$LangID]?> 

                                        </div>
                                        <div style="display:inline-block; margin:0 2px 2px 2px;">
                                            <input type="text" id="Hr_EvaluationName" name="Hr_EvaluationName" value="<?=$Hr_EvaluationName?>" style="width:100px;height:25px;border:1px solid #cccccc;"/>
                                        </div>
                                    </div>
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top">
							<div class="uk-margin-top hr_wrap" data-uk-grid-margin>
								<label class="hr_left"><?=$평가주기[$LangID]?></label>
                                <div class="hr_right">
									<?
									$Sql2 = "select 
													A.* 
											from Hr_EvaluationCycles A 
											where 
												A.Hr_EvaluationCycleState=1 
											order by A.Hr_EvaluationCycleOrder asc";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

									while($Row2 = $Stmt2->fetch()) {
										$Db_Hr_EvaluationCycleID = $Row2["Hr_EvaluationCycleID"];
										$Db_Hr_EvaluationCycleName = $Row2["Hr_EvaluationCycleName"];
									?>
									<span class="icheck-inline">
										<input type="radio" class="radio_input" name="Hr_EvaluationCycleID" id="Hr_EvaluationCycleID<?=$Db_Hr_EvaluationCycleID?>" value="<?=$Db_Hr_EvaluationCycleID?>" <?if ($Db_Hr_EvaluationCycleID==$Hr_EvaluationCycleID){?>checked<?}?>/>
										<label for="Hr_EvaluationCycleID<?=$Db_Hr_EvaluationCycleID?>" class="radio_label"><span class="radio_bullet"></span><?=$Db_Hr_EvaluationCycleName?></label>
									</span>
									<?
									}
									$Stmt2 = null;
									?>
                                </div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top">
							<div class="uk-margin-top hr_wrap" data-uk-grid-margin>
								<label class="hr_left"><?=$평가방식[$LangID]?></label>
                                <div class="hr_right">
									<?
									$Sql2 = "select 
													A.* 
											from Hr_EvaluationTypes A 
											where 
												A.Hr_EvaluationTypeState=1 
											order by A.Hr_EvaluationTypeOrder asc";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

									while($Row2 = $Stmt2->fetch()) {
										$Db_Hr_EvaluationTypeID   = $Row2["Hr_EvaluationTypeID"];
										$Db_Hr_EvaluationTypeName = $Row2["Hr_EvaluationTypeName"];

										if ($Db_Hr_EvaluationTypeID==1) {
                                               $Db_Hr_EvaluationUseCompetency = 1;  
                                        } else {
                                               $Db_Hr_EvaluationUseCompetency = 0;  
										}
									    ?>
									<span>
										<input type="radio" class="radio_input" name="Hr_EvaluationTypeID" id="Hr_EvaluationTypeID<?=$Db_Hr_EvaluationTypeID?>" value="<?=$Db_Hr_EvaluationTypeID?>" <?if ($Db_Hr_EvaluationTypeID==$Hr_EvaluationTypeID){?>checked<?}?> onClick="Check_Hr_EvaluationUseCompetency(<?=$Db_Hr_EvaluationUseCompetency?>,2)"/>
										<label for="Hr_EvaluationTypeID<?=$Db_Hr_EvaluationTypeID?>" class="radio_label"><span class="radio_bullet"></span><?=$Db_Hr_EvaluationTypeName?></label>
									</span>
									    <?
									}
									$Stmt2 = null;
									?>
                                </div>
							</div>
						</div>
						<hr>


						<div class="uk-margin-top hr_wrap">
							<label class="hr_left"><?=$평가일[$LangID]?></label>
                            <div class="hr_right">
    							<input type="text" id="Hr_EvaluationDate" name="Hr_EvaluationDate" value="<?=$Hr_EvaluationDate?>" style="width:120px;"/>
                            </div>
						</div>
						<hr>

						<div class="uk-margin-top hr_wrap">
							<label class="hr_left"><?=$평가기간[$LangID]?></label>
                            <div class="hr_right">
                                <input type="text" id="Hr_EvaluationStartDate" name="Hr_EvaluationStartDate" value="<?=$Hr_EvaluationStartDate?>" style="width:108px;">
                                ~
                                <input type="text" id="Hr_EvaluationEndDate" name="Hr_EvaluationEndDate" value="<?=$Hr_EvaluationEndDate?>" style="width:108px;">
                            </div>
						</div>
						<hr>

						<div class="uk-margin-top hr_wrap">
							<label class="hr_left"><?=$목표설정_기간[$LangID]?></label>
                            <div class="hr_right">
                                <input type="text" id="Hr_EvaluationGoalStartDate" name="Hr_EvaluationGoalStartDate" value="<?=$Hr_EvaluationGoalStartDate?>" style="width:108px;">
                                ~
                                <input type="text" id="Hr_EvaluationGoalEndDate" name="Hr_EvaluationGoalEndDate" value="<?=$Hr_EvaluationGoalEndDate?>" style="width:108px;">
                            </div>
						</div>
						<hr>


						<div class="uk-margin-top">
							<div class="uk-margin-top hr_wrap" data-uk-grid-margin>
								<label class="hr_left"><?=$역량평가_등록_여부[$LangID]?></label>
                                <div class="hr_right">
                                    <span>
                                        <input type="radio" class="radio_input" name="Hr_EvaluationUseCompetency" id="Hr_EvaluationUseCompetency1" value="1" <?if ($Hr_EvaluationUseCompetency==1 or $Hr_EvaluationID==""){?>checked<?}?> onclick="Check_Hr_EvaluationUseCompetency(1,1)"/>
                                        <label for="Hr_EvaluationUseCompetency1" class="radio_label"><span class="radio_bullet"></span><?=$등록[$LangID]?></label>
                                    </span>
                                    <span>
                                        <input type="radio" class="radio_input" name="Hr_EvaluationUseCompetency" id="Hr_EvaluationUseCompetency0" value="0" <?if ($Hr_EvaluationUseCompetency==0 and $Hr_EvaluationID!=""){?>checked<?}?> onclick="Check_Hr_EvaluationUseCompetency(0,1)"/>
                                        <label for="Hr_EvaluationUseCompetency0" class="radio_label"><span class="radio_bullet"></span><?=$미등록[$LangID]?></label>
                                    </span>
                                </div>
							</div>
						</div>
						<hr>

						<div id="Hr_EvaluationUseCompetency_Div1" class="uk-margin-top" style="display:<?if ($Hr_EvaluationUseCompetency==0 and $Hr_EvaluationID!=""){?>none<?}?>;">
							<div class="uk-margin-top hr_wrap" data-uk-grid-margin>
								<label class="hr_left"><?=$평가자점수확인[$LangID]?></label>
                                <div class="hr_right">
                                    <span>
                                        <input type="radio" class="radio_input" name="Hr_EvaluationUseScore" id="Hr_EvaluationUseScore1" value="1" <?if ($Hr_EvaluationUseScore==1){?>checked<?}?>/>
                                        <label for="Hr_EvaluationUseScore1" class="radio_label"><span class="radio_bullet"></span><?=$등록[$LangID]?></label>
                                    </span>
                                    <span>
                                        <input type="radio" class="radio_input" name="Hr_EvaluationUseScore" id="Hr_EvaluationUseScore0" value="0" <?if ($Hr_EvaluationUseScore==0){?>checked<?}?>/>
                                        <label for="Hr_EvaluationUseScore0" class="radio_label"><span class="radio_bullet"></span><?=$미등록[$LangID]?></label>
                                    </span>
                                </div>
							</div>
						</div>
						<hr id="Hr_EvaluationUseCompetency_Div1_1" style="display:<?if ($Hr_EvaluationUseCompetency==0 and $Hr_EvaluationID!=""){?>none<?}?>;">

						<div id="Hr_EvaluationUseCompetency_Div2" class="uk-margin-top" style="display:<?if ($Hr_EvaluationUseCompetency==0 and $Hr_EvaluationID!=""){?>none<?}?>;">
							<div class="uk-margin-top hr_wrap" data-uk-grid-margin>
								<label class="hr_left"><?=$평가근거기록[$LangID]?></label>
                                <div class="hr_right">
                                    <span>
                                        <input type="radio" class="radio_input" name="Hr_EvaluationUseWarrant" id="Hr_EvaluationUseWarrant1" value="1" <?if ($Hr_EvaluationUseWarrant==1){?>checked<?}?>/>
                                        <label for="Hr_EvaluationUseWarrant1" class="radio_label"><span class="radio_bullet"></span><?=$등록[$LangID]?></label>
                                    </span>
                                    <span>
                                        <input type="radio" class="radio_input" name="Hr_EvaluationUseWarrant" id="Hr_EvaluationUseWarrant0" value="0" <?if ($Hr_EvaluationUseWarrant==0){?>checked<?}?>/>
                                        <label for="Hr_EvaluationUseWarrant0" class="radio_label"><span class="radio_bullet"></span><?=$미등록[$LangID]?></label>
                                    </span>
                                </div>
							</div>
						</div>
						<hr id="Hr_EvaluationUseCompetency_Div2_1" style="display:<?if ($Hr_EvaluationUseCompetency==0 and $Hr_EvaluationID!=""){?>none<?}?>;">

						<div id="Hr_EvaluationUseCompetency_Div3" class="uk-margin-top" style="display:<?if ($Hr_EvaluationUseCompetency==0 and $Hr_EvaluationID!=""){?>none<?}?>;">
							<div class="uk-margin-top hr_wrap" data-uk-grid-margin>
								<label class="hr_left"><?=$평가총평기록[$LangID]?></label>
                                <div class="hr_right">
                                    <span>
                                        <input type="radio" class="radio_input" name="Hr_EvaluationUseOverall" id="Hr_EvaluationUseOverall1" value="1" <?if ($Hr_EvaluationUseOverall==1){?>checked<?}?>/>
                                        <label for="Hr_EvaluationUseOverall1" class="radio_label"><span class="radio_bullet"></span><?=$등록[$LangID]?></label>
                                    </span>
                                    <span>
                                        <input type="radio" class="radio_input" name="Hr_EvaluationUseOverall" id="Hr_EvaluationUseOverall0" value="0" <?if ($Hr_EvaluationUseOverall==0){?>checked<?}?>/>
                                        <label for="Hr_EvaluationUseOverall0" class="radio_label"><span class="radio_bullet"></span><?=$미등록[$LangID]?></label>
                                    </span>
                                </div>
							</div>
						</div>
						<hr id="Hr_EvaluationUseCompetency_Div3_1" style="display:<?if ($Hr_EvaluationUseCompetency==0 and $Hr_EvaluationID!=""){?>none<?}?>;">

						
						<div class="uk-margin-top" style="display:<?if ($Hr_EvaluationID==""){?>none<?}?>;">
							<div class="hr_wrap" data-uk-grid-margin>
								<label class="hr_left"><?=$사용여부[$LangID]?></label>
                                <div class="hr_right">
                                    <span>
                                        <input type="radio" class="radio_input" name="Hr_EvaluationState" id="Hr_EvaluationState1" value="1" <?if ($Hr_EvaluationState==1){?>checked<?}?>/>
                                        <label for="Hr_EvaluationState1" class="radio_label"><span class="radio_bullet"></span><?=$사용[$LangID]?></label>
                                    </span>
                                    <span>
                                        <input type="radio" class="radio_input" name="Hr_EvaluationState" id="Hr_EvaluationState2" value="2" <?if ($Hr_EvaluationState==2){?>checked<?}?>/>
                                        <label for="Hr_EvaluationState2" class="radio_label"><span class="radio_bullet"></span><?=$미사용[$LangID]?></label>
                                    </span>
                                    <span>
                                        <input type="radio" class="radio_input" name="Hr_EvaluationState" id="Hr_EvaluationState0" value="0" <?if ($Hr_EvaluationState==0){?>checked<?}?>/>
                                        <label for="Hr_EvaluationState0" class="radio_label"><span class="radio_bullet"></span><?=$삭제[$LangID]?></label>
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

function Check_Hr_EvaluationUseCompetency(n,s){

	if (n==1){
		if (s==2) {
			 document.getElementById("Hr_EvaluationUseCompetency1").checked = true;
			 document.getElementById("Hr_EvaluationUseCompetency0").checked = false;
		}
		document.getElementById("Hr_EvaluationUseCompetency_Div1").style.display = "";
		document.getElementById("Hr_EvaluationUseCompetency_Div2").style.display = "";
		document.getElementById("Hr_EvaluationUseCompetency_Div3").style.display = "";

		document.getElementById("Hr_EvaluationUseCompetency_Div1_1").style.display = "";
		document.getElementById("Hr_EvaluationUseCompetency_Div2_1").style.display = "";
		document.getElementById("Hr_EvaluationUseCompetency_Div3_1").style.display = "";
	}else{
		if (s==2) {
			 document.getElementById("Hr_EvaluationUseCompetency1").checked = false;
			 document.getElementById("Hr_EvaluationUseCompetency0").checked = true;
		}
		document.getElementById("Hr_EvaluationUseCompetency_Div1").style.display = "none";
		document.getElementById("Hr_EvaluationUseCompetency_Div2").style.display = "none";
		document.getElementById("Hr_EvaluationUseCompetency_Div3").style.display = "none";

		document.getElementById("Hr_EvaluationUseCompetency_Div1_1").style.display = "none";
		document.getElementById("Hr_EvaluationUseCompetency_Div2_1").style.display = "none";
		document.getElementById("Hr_EvaluationUseCompetency_Div3_1").style.display = "none";
	}
}


function FormSubmit(){

	obj = document.RegForm.Hr_EvaluationName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$평가명을_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "hr_evaluation_action.php";
			document.RegForm.submit();
		}
	);

}

</script>


<!-- ====    kendo -->
<link href="../kendo/styles/kendo.common.min.css" rel="stylesheet">
<link href="../kendo/styles/kendo.default.min.css" rel="stylesheet">
<script src="../kendo/js/kendo.web.min.js"></script>
<!-- ====    kendo   === -->

<script>
	$(document).ready(function() {
		$("#Hr_EvaluationDate").kendoDatePicker({
			format: "yyyy-MM-dd"
		});

		$("#Hr_EvaluationStartDate").kendoDatePicker({
			format: "yyyy-MM-dd"
		});

		$("#Hr_EvaluationEndDate").kendoDatePicker({
			format: "yyyy-MM-dd"
		});

		$("#Hr_EvaluationGoalStartDate").kendoDatePicker({
			format: "yyyy-MM-dd"
		});

		$("#Hr_EvaluationGoalEndDate").kendoDatePicker({
			format: "yyyy-MM-dd"
		});
	});
</script>

<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>