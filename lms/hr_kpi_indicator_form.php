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
$Hr_KpiIndicatorID = isset($_REQUEST["Hr_KpiIndicatorID"]) ? $_REQUEST["Hr_KpiIndicatorID"] : "";


if ($Hr_KpiIndicatorID!=""){
	$Sql = "
			select 
				A.*
			from Hr_KpiIndicators A 
			where A.Hr_KpiIndicatorID=:Hr_KpiIndicatorID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_KpiIndicatorID', $Hr_KpiIndicatorID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();

	$Hr_KpiIndicatorID = $Row["Hr_KpiIndicatorID"];
	$Hr_KpiIndicatorName = $Row["Hr_KpiIndicatorName"];
	$Hr_KpiIndicatorDefine = $Row["Hr_KpiIndicatorDefine"];
	$Hr_KpiIndicatorFormula = $Row["Hr_KpiIndicatorFormula"];
	$Hr_KpiIndicatorMeasure = $Row["Hr_KpiIndicatorMeasure"];
	$Hr_KpiIndicatorSource = $Row["Hr_KpiIndicatorSource"];
	$Hr_KpiIndicatorPartName = $Row["Hr_KpiIndicatorPartName"];
	$Hr_KpiIndicatorUnitID = $Row["Hr_KpiIndicatorUnitID"];
	$Hr_KpiIndicatorState = $Row["Hr_KpiIndicatorState"];

}else{
	$Hr_KpiIndicatorID = "";
	$Hr_KpiIndicatorName = "";
	$Hr_KpiIndicatorDefine = "";
	$Hr_KpiIndicatorFormula = "";
	$Hr_KpiIndicatorMeasure = "";
	$Hr_KpiIndicatorSource = "";
	$Hr_KpiIndicatorPartName = "";
	$Hr_KpiIndicatorUnitID = 0;
	$Hr_KpiIndicatorState = 1;
}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="CenterID" value="<?=$CenterID?>">
		<input type="hidden" name="Hr_KpiIndicatorID" value="<?=$Hr_KpiIndicatorID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname">KPI 문항관리</span><span class="sub-heading" id="user_edit_position"><!--베스트강사--></span></h2>
						</div>
					</div>
					<div class="user_content">

                        <style>
                            .kpi_wrap{display:flex; flex-wrap:wrap; align-items:center;}
                            .kpi_left{width:100%; padding:5px 0;}
                            .kpi_right{width:100%; display:flex; flex-wrap:wrap; align-items:center;}
                            .kpi_input{width:100%; padding:0 10px; height:32px; border:1px solid #ddd; box-sizing:border-box;}
                            .kpi_text{width:100%; padding:10px; height:100px; border:1px solid #ddd; box-sizing:border-box; color:#666;}
                            .kpi_right .radio_label{padding:5px 15px 5px 0;}
                            
                            @media all and (min-width:640px){
                                .kpi_left{width:15%; padding:0;}
                                .kpi_right{width:85%;}
                            }
                        </style>


						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10 kpi_wrap">
									<label class="kpi_left"><?=$지표명[$LangID]?></label>
									<div class="kpi_right">
										<input type="text" id="Hr_KpiIndicatorName" name="Hr_KpiIndicatorName" value="<?=$Hr_KpiIndicatorName?>" class="kpi_input"/>
									</div>
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10 kpi_wrap">
									<label class="kpi_left"><?=$정의[$LangID]?></label>

									<div class="kpi_right">
										<textarea id="Hr_KpiIndicatorDefine" name="Hr_KpiIndicatorDefine" class="kpi_text"><?=$Hr_KpiIndicatorDefine?></textarea>
									</div>
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10 kpi_wrap">
									<label class="kpi_left"><?=$측정산식[$LangID]?></label>

									<div class="kpi_right">
										<textarea id="Hr_KpiIndicatorFormula" name="Hr_KpiIndicatorFormula"  class="kpi_text"><?=$Hr_KpiIndicatorFormula?></textarea>
									</div>
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10 kpi_wrap">
									<label class="kpi_left"><?=$평가척도[$LangID]?></label>

									<div class="kpi_right">
										<textarea id="Hr_KpiIndicatorMeasure" name="Hr_KpiIndicatorMeasure"  class="kpi_text"><?=$Hr_KpiIndicatorMeasure?></textarea>
									</div>
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10 kpi_wrap">
									<label class="kpi_left"><?=$증빙자료[$LangID]?></label>

									<div class="kpi_right">
										<input type="text" id="Hr_KpiIndicatorSource" name="Hr_KpiIndicatorSource" value="<?=$Hr_KpiIndicatorSource?>" class="kpi_input">
									</div>
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10 kpi_wrap">
									<label class="kpi_left"><?=$관련직무[$LangID]?></label>

									<div class="kpi_right">
										
										<?

										$Sql2 = "
												select 
													count(*) as CheckAllCount
												from Hr_KpiIndicatorTasks A 
												where A.Hr_KpiIndicatorID=:Hr_KpiIndicatorID and A.Hr_OrganTask2ID=0 ";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->bindParam(':Hr_KpiIndicatorID', $Hr_KpiIndicatorID);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$CheckAllCount = $Row2["CheckAllCount"];

										?>
										<span>
											<input type="checkbox" class="radio_input" name="Hr_OrganTask2ID_0" id="Hr_OrganTask2ID_0" value="0" <?if ($CheckAllCount>0){?>checked<?}?> onclick="CheckAll_Hr_OrganTask2ID()"/>
											<label for="Hr_OrganTask2ID_0" class="radio_label"><span class="radio_bullet"></span><?=$전체[$LangID]?></label>
										</span>
					
										<?
										if ($Hr_KpiIndicatorID==""){
											$Temp_Hr_KpiIndicatorID=0;
										}else{
											$Temp_Hr_KpiIndicatorID=$Hr_KpiIndicatorID;
										}

										$Sql2 = "
											select 
												A.Hr_OrganTask1ID,
												A.Hr_OrganTask2ID,
												A.Hr_OrganTask2Name,
												B.Hr_OrganTask1Name,

												ifnull(C.Hr_KpiIndicatorID,0) as Set_Hr_KpiIndicatorID

											from Hr_OrganTask2 A 
												inner join Hr_OrganTask1 B on A.Hr_OrganTask1ID=B.Hr_OrganTask1ID 
												left outer join Hr_KpiIndicatorTasks C on A.Hr_OrganTask2ID=C.Hr_OrganTask2ID and Hr_KpiIndicatorID=$Temp_Hr_KpiIndicatorID
											where A.Hr_OrganTask2State=1 and B.Hr_OrganTask1State=1 
											order by B.Hr_OrganTask1ID asc, A.Hr_OrganTask2ID asc
										";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$ListCount2 = 1;
										
										$Hr_OrganTask2IDs = "|";
										while($Row2 = $Stmt2->fetch()) {
											$Hr_OrganTask1ID = $Row2["Hr_OrganTask1ID"];
											$Hr_OrganTask2ID = $Row2["Hr_OrganTask2ID"];
											$Hr_OrganTask2Name = $Row2["Hr_OrganTask2Name"];
											$Hr_OrganTask1Name = $Row2["Hr_OrganTask1Name"];

											$Set_Hr_KpiIndicatorID = $Row2["Set_Hr_KpiIndicatorID"];

											$Hr_OrganTask2IDs = $Hr_OrganTask2IDs . $Hr_OrganTask2ID . "|";
										?>
										<span>
											<input type="checkbox" class="radio_input" name="Hr_OrganTask2ID_<?=$Hr_OrganTask2ID?>" id="Hr_OrganTask2ID_<?=$Hr_OrganTask2ID?>" value="<?=$Hr_OrganTask2ID?>" <?if ($Set_Hr_KpiIndicatorID!=0){?>checked<?}?> <?if ($CheckAllCount>0){?>disabled<?}?>/>
											<label for="Hr_OrganTask2ID_<?=$Hr_OrganTask2ID?>" class="radio_label" id="Label_Hr_OrganTask2ID_<?=$Hr_OrganTask2ID?>" style="<?if ($CheckAllCount>0){?>color:#cccccc;<?}?>"><span class="radio_bullet"></span><?=$Hr_OrganTask2Name?></label>
										</span>
										<?
											$ListCount2++;
										}
										$Stmt2=null;
										?>


										<script>
										function CheckAll_Hr_OrganTask2ID(){
											Hr_OrganTask2IDs = "<?=$Hr_OrganTask2IDs?>";
											CheckAll = document.RegForm.Hr_OrganTask2ID_0.checked;

											Arr_Hr_OrganTask2ID = Hr_OrganTask2IDs.split("|");

											for (ii=1;ii<=Arr_Hr_OrganTask2ID.length-2;ii++){
												
												if (CheckAll){
													document.getElementById("Hr_OrganTask2ID_"+Arr_Hr_OrganTask2ID[ii]).checked = false;
													document.getElementById("Hr_OrganTask2ID_"+Arr_Hr_OrganTask2ID[ii]).disabled = true;
													document.getElementById("Label_Hr_OrganTask2ID_"+Arr_Hr_OrganTask2ID[ii]).style.color = "#cccccc";
												}else{
													document.getElementById("Hr_OrganTask2ID_"+Arr_Hr_OrganTask2ID[ii]).disabled = false;
													document.getElementById("Label_Hr_OrganTask2ID_"+Arr_Hr_OrganTask2ID[ii]).style.color = "";
												}
											
											}
										}
										</script>
									</div>
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10 kpi_wrap">
									<label class="kpi_left"><?=$사용부서[$LangID]?></label>

									<div class="kpi_right">
										<input type="text" id="Hr_KpiIndicatorPartName" name="Hr_KpiIndicatorPartName" value="<?=$Hr_KpiIndicatorPartName?>" class="kpi_input">
									</div>
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10 kpi_wrap">
									<label class="kpi_left"><?=$적용단위[$LangID]?></label>
                                    <div class="kpi_right">
                                        <?
                                        $Sql2 = "select 
                                                        A.* 
                                                from Hr_KpiIndicatorUnits A 
                                                where 
                                                    A.Hr_KpiIndicatorUnitState=1 
                                                order by A.Hr_KpiIndicatorUnitOrder asc";
                                        $Stmt2 = $DbConn->prepare($Sql2);
                                        $Stmt2->execute();
                                        $Stmt2->setFetchMode(PDO::FETCH_ASSOC);

                                        while($Row2 = $Stmt2->fetch()) {
                                            $Db_Hr_KpiIndicatorUnitID = $Row2["Hr_KpiIndicatorUnitID"];
                                            $Db_Hr_KpiIndicatorUnitName = $Row2["Hr_KpiIndicatorUnitName"];
                                        ?>
                                        <span>
                                            <input type="radio" class="radio_input" name="Hr_KpiIndicatorUnitID" id="Hr_KpiIndicatorUnitID<?=$Db_Hr_KpiIndicatorUnitID?>" value="<?=$Db_Hr_KpiIndicatorUnitID?>" <?if ($Db_Hr_KpiIndicatorUnitID==$Hr_KpiIndicatorUnitID){?>checked<?}?>/>
                                            <label for="Hr_KpiIndicatorUnitID<?=$Db_Hr_KpiIndicatorUnitID?>" class="radio_label"><span class="radio_bullet"></span><?=$Db_Hr_KpiIndicatorUnitName?></label>
                                        </span>
                                        <?
                                        }
                                        $Stmt2 = null;
                                        ?>
                                    </div>
								</div>
							</div>
						</div>
						<hr>


						
						<div class="uk-margin-top" style="display:<?if ($Hr_KpiIndicatorID==""){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-10-10 kpi_wrap">
                                    <label class="kpi_left"><?=$사용여부[$LangID]?></label>
                                    <div class="kpi_right">
                                        <span>
                                            <input type="radio" class="radio_input" name="Hr_KpiIndicatorState" id="Hr_KpiIndicatorState1" value="1" <?if ($Hr_KpiIndicatorState==1){?>checked<?}?>/>
                                            <label for="Hr_KpiIndicatorState1" class="radio_label"><span class="radio_bullet"></span><?=$사용[$LangID]?></label>
                                        </span>
                                        <span>
                                            <input type="radio" class="radio_input" name="Hr_KpiIndicatorState" id="Hr_KpiIndicatorState2" value="2" <?if ($Hr_KpiIndicatorState==2){?>checked<?}?>/>
                                            <label for="Hr_KpiIndicatorState2" class="radio_label"><span class="radio_bullet"></span><?=$미사용[$LangID]?></label>
                                        </span>
                                        <span>
                                            <input type="radio" class="radio_input" name="Hr_KpiIndicatorState" id="Hr_KpiIndicatorState0" value="0" <?if ($Hr_KpiIndicatorState==0){?>checked<?}?>/>
                                            <label for="Hr_KpiIndicatorState0" class="radio_label"><span class="radio_bullet"></span><?=$삭제[$LangID]?></label>
                                        </span>
                                    </div>
                                </div>
							</div>
						</div>
						<hr style="display:<?if ($Hr_KpiIndicatorID==""){?>none<?}?>;">

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

	obj = document.RegForm.Hr_KpiIndicatorName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$지표명을_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.Hr_KpiIndicatorDefine;
	if (obj.value==""){
		UIkit.modal.alert("<?=$정의를_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.Hr_KpiIndicatorFormula;
	if (obj.value==""){
		UIkit.modal.alert("<?=$측정산식을_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.Hr_KpiIndicatorMeasure;
	if (obj.value==""){
		UIkit.modal.alert("<?=$평가척도를_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.Hr_KpiIndicatorSource;
	if (obj.value==""){
		UIkit.modal.alert("<?=$증빙자료를_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}


	obj = document.RegForm.Hr_KpiIndicatorPartName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$사용부서를_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}



	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "hr_kpi_indicator_action.php";
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