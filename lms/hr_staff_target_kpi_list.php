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
?>

<?
include_once('./inc_common_list_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
</head>

<body class="disable_transitions sidebar_main_swipe">
<?php
$MainMenuID = 88;
$SubMenuID = 88061;
include_once('./inc_top.php');
?>


 
<?php

$AddSqlWhere = "1=1";

$SearchState    = isset($_REQUEST["SearchState"   ]) ? $_REQUEST["SearchState"   ] : "";
$KpiIndicatorID = isset($_REQUEST["KpiIndicatorID"]) ? $_REQUEST["KpiIndicatorID"] : "";
$TargetMenu     = isset($_REQUEST["TargetMenu"    ]) ? $_REQUEST["TargetMenu"    ] : "";
$ViewSW         = isset($_REQUEST["ViewSW"        ]) ? $_REQUEST["ViewSW"        ] : "";
// 부분목표에서....
$MemberID       = isset($_REQUEST["MemberID"      ]) ? $_REQUEST["MemberID"      ] : "";
$EvaluationID   = isset($_REQUEST["EvaluationID"  ]) ? $_REQUEST["EvaluationID"  ] : "";
?>


<div id="page_content">
	<div id="page_content_inner">
<?
#----------------------------------------------------------------------------------------------------------------------------------#
if ($TargetMenu==1) {
#----------------------------------------------------------------------------------------------------------------------------------#
?>
		<h3 class="heading_b uk-margin-bottom"><?=$KPI_문항선택[$LangID]?></h3>

		<form name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="SearchState"    value="<?=$SearchState?>" />
		<input type="hidden" name="TargetMenu"     value="<?=$TargetMenu?>" />
		<input type="hidden" name="KpiIndicatorID" value="<?=$KpiIndicatorID?>" />
		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical" style="width:100%;">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap style="width:200px;"><?=$지표명[$LangID]?></th>
										<th nowrap><?=$정의[$LangID]?></th>
										<th nowrap><?=$측정산식[$LangID]?></th>
										<th nowrap><?=$평가척도[$LangID]?></th>
										<th nowrap><?=$증빙자료출처[$LangID]?></th>
										<th nowrap><?=$관련직무[$LangID]?></th>
										<th nowrap><?=$사용부서[$LangID]?></th>
										<th nowrap><?=$적용단위[$LangID]?></th>
										<th nowrap style="width:80px;"><?=$선택[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									$Sql = "select A.*, (select Hr_KpiIndicatorUnitName from Hr_KpiIndicatorUnits where Hr_KpiIndicatorUnitID=A.Hr_KpiIndicatorUnitID) as Hr_KpiIndicatorUnitName
												   from Hr_KpiIndicators A 
												   where ".$AddSqlWhere." 
												   order by A.Hr_KpiIndicatorOrder asc";

									$Stmt = $DbConn->prepare($Sql);
									$Stmt->execute();
									$Stmt->setFetchMode(PDO::FETCH_ASSOC);
                                    #----------------------------------------------------------------------------------------------#
									while($Row = $Stmt->fetch()) {
                                    #----------------------------------------------------------------------------------------------#
										$Hr_KpiIndicatorID = $Row["Hr_KpiIndicatorID"];
										$Hr_KpiIndicatorID_Checked = ""; 
										if ($KpiIndicatorID) {
                                               $KpiIndicatorID_array = explode("/",$KpiIndicatorID);
											   for ($k=0; $k < count($KpiIndicatorID_array); $k++) {
                                                     if ($KpiIndicatorID_array[$k]==$Hr_KpiIndicatorID) {
														    $Hr_KpiIndicatorID_Checked = "checked";
															break;
													 }
											   }
										}

										$Hr_KpiIndicatorName = $Row["Hr_KpiIndicatorName"];
										$Hr_KpiIndicatorDefine = $Row["Hr_KpiIndicatorDefine"];
										$Hr_KpiIndicatorFormula = $Row["Hr_KpiIndicatorFormula"];
										$Hr_KpiIndicatorMeasure = $Row["Hr_KpiIndicatorMeasure"];
										$Hr_KpiIndicatorSource = $Row["Hr_KpiIndicatorSource"];
										$Hr_KpiIndicatorPartName = $Row["Hr_KpiIndicatorPartName"];
										$Hr_KpiIndicatorUnitID = $Row["Hr_KpiIndicatorUnitID"];
										$Hr_KpiIndicatorState = $Row["Hr_KpiIndicatorState"];

										$Hr_KpiIndicatorUnitName = $Row["Hr_KpiIndicatorUnitName"];

										if ($Hr_KpiIndicatorState==1){
											$Str_Hr_KpiIndicatorState = "<span class=\"ListState_1\">사용중</span>";
										}else if ($Hr_KpiIndicatorState==2){
											$Str_Hr_KpiIndicatorState = "<span class=\"ListState_2\">미사용</span>";
										}
									?>
									<tr>
										<td class="uk-text-wrap uk-table-td-center"><?=$ListCount?></td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorName?></td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorDefine?></td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorFormula?></td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorMeasure?></td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorSource?></td>
										<td class="uk-text-wrap uk-table-td-center">

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


										if ($CheckAllCount>0){
										?>
										전체
										<?
										}else{
										?>
											
											<?
											$Sql2 = "
												select 
													B.Hr_OrganTask1ID,
													B.Hr_OrganTask2ID,
													B.Hr_OrganTask2Name,
													C.Hr_OrganTask1Name

												from Hr_KpiIndicatorTasks A 
													inner join Hr_OrganTask2 B on A.Hr_OrganTask2ID=B.Hr_OrganTask2ID 
													inner join Hr_OrganTask1 C on B.Hr_OrganTask1ID=C.Hr_OrganTask1ID 
												where A.Hr_KpiIndicatorID=$Hr_KpiIndicatorID 
												order by C.Hr_OrganTask1ID asc, B.Hr_OrganTask2ID asc
											";
											$Stmt2 = $DbConn->prepare($Sql2);
											$Stmt2->execute();
											$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
											$ListCount2 = 1;
											while($Row2 = $Stmt2->fetch()) {
												$Hr_OrganTask1ID = $Row2["Hr_OrganTask1ID"];
												$Hr_OrganTask2ID = $Row2["Hr_OrganTask2ID"];
												$Hr_OrganTask2Name = $Row2["Hr_OrganTask2Name"];
												$Hr_OrganTask1Name = $Row2["Hr_OrganTask1Name"];
												if ($ListCount2>1){
													echo ", ";
												}
											    ?>
												<?=$Hr_OrganTask2Name?>
											    <?
												$ListCount2++;
											}
											$Stmt2=null;
											?>

										<?
										}
										?>
										</td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorPartName?></td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorUnitName?></td>
										<td class="uk-text-wrap uk-table-td-center">
											<input type="checkbox" id="KPI_CHECKID[]" name="KPI_CHECKID[]" value="<?=$Hr_KpiIndicatorID?>" <?=$Hr_KpiIndicatorID_Checked?> style="width:20px; height:20px; vertical-align:middle;"> <?=$선택[$LangID]?></a>
										</td>
									</tr>
									<?php
										$ListCount ++;
                                    #----------------------------------------------------------------------------------------------#
									}
                                    #----------------------------------------------------------------------------------------------#
									$Stmt = null;
									?>


								</tbody>
							</table>
						</div>

						<div class="uk-form-row" style="text-align:center; margin-top:20px;">
							<a type="button" href="javascript:SaveKpiIndicatorAction()" class="md-btn md-btn-primary">목표선택</a>
						</div>

					</div>
				</div>
			</div>
		</div>
        </form>

<?
#----------------------------------------------------------------------------------------------------------------------------------#
} else if ($TargetMenu==2) {
#----------------------------------------------------------------------------------------------------------------------------------#
		$Sql = "select * from Members 
						   where MemberID=:MemberID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Hr_MemberName = $Row["MemberName"];
        
		$UseYN = 'N';
		if ($ViewSW==9) {
              $UseYN = 'Y';
		} 
		?>
		<h3 class="heading_b uk-margin-bottom">[<?=$Hr_MemberName?>] <?=$목표설정[$LangID]?></h3>

		<form name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="SearchState"  value="<?=$SearchState?>" />
		<input type="hidden" name="TargetMenu"   value="<?=$TargetMenu?>" />
		<input type="hidden" name="MemberID"     value="<?=$MemberID?>" />
		<input type="hidden" name="EvaluationID" value="<?=$EvaluationID?>" />
		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical" style="width:100%;">
								<thead>
									<tr>
										<th nowrap width="5%">No</th>
										<th nowrap width="20%"><?=$지표명[$LangID]?></th>
										<th nowrap width="20%"><?=$정의[$LangID]?></th>
										<th nowrap width="20%"><?=$측정산식[$LangID]?></th>
										<th nowrap width="25%"><?=$평가척도[$LangID]?></th>
										<th nowrap width="10%"><?=$가중치[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
                            <?
							$Sql = "select * from Hr_Staff_Target 
											 where MemberID=".$MemberID." and 
												   Hr_EvaluationID=".$SearchState." and
												   Hr_TargetState='9' and 
												   Hr_UseYN='".$UseYN."'";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
							$line_cnt = 0;
                            #-------------------------------------------------------------------------------------------------------------#
							while ($Row = $Stmt->fetch()) {
                            #-------------------------------------------------------------------------------------------------------------#
							        $Hr_TargetID       = $Row["Hr_TargetID"       ];
								    $Hr_KpiIndicatorID = $Row["Hr_KpiIndicatorID" ];
                                    $Hr_TargetName     = $Row["Hr_TargetName"     ]; 
                                    $Hr_TargetAddValue = $Row["Hr_TargetAddValue" ]; 
                                    $Hr_TargetState    = $Row["Hr_TargetState"    ]; 
                                    #-----------------------------------------------------------------------------------------------------#
                                    $Sql2 = "select A.*,
                                                   (select Hr_KpiIndicatorUnitName from Hr_KpiIndicatorUnits where Hr_KpiIndicatorUnitID=A.Hr_KpiIndicatorUnitID) as Hr_KpiIndicatorUnitName
                                              from Hr_KpiIndicators A 
                                              where A.Hr_KpiIndicatorID=:Hr_KpiIndicatorID";
                                    $Stmt2 = $DbConn->prepare($Sql2);
								    $Stmt2->bindParam(':Hr_KpiIndicatorID', $Hr_KpiIndicatorID);
                                    $Stmt2->execute();
                                    $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                                    $Row2 = $Stmt2->fetch();  
                                    #-----------------------------------------------------------------------------------------------------#
									if ($Row2) {
                                    #-----------------------------------------------------------------------------------------------------#
                                            $Hr_KpiIndicatorID       = $Row2["Hr_KpiIndicatorID"];
                                            $Hr_KpiIndicatorName     = $Row2["Hr_KpiIndicatorName"];
                                            $Hr_KpiIndicatorDefine   = $Row2["Hr_KpiIndicatorDefine"];
                                            $Hr_KpiIndicatorFormula  = $Row2["Hr_KpiIndicatorFormula"];
                                            $Hr_KpiIndicatorMeasure  = $Row2["Hr_KpiIndicatorMeasure"];
                                            $Hr_KpiIndicatorSource   = $Row2["Hr_KpiIndicatorSource"];
                                            $Hr_KpiIndicatorPartName = $Row2["Hr_KpiIndicatorPartName"];
                                            $Hr_KpiIndicatorUnitID   = $Row2["Hr_KpiIndicatorUnitID"];
                                            $Hr_KpiIndicatorState    = $Row2["Hr_KpiIndicatorState"];
                                            $Hr_KpiIndicatorUnitName = $Row2["Hr_KpiIndicatorUnitName"];
                                            #---------------------------------------------------------------------------------------------#
											$line_cnt++;
                                            ?>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center"> 
										   <?=$line_cnt?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_TargetName?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_KpiIndicatorName?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_KpiIndicatorFormula?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_KpiIndicatorMeasure?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_TargetAddValue?>%
                                        </td>
                                    </tr>
							        <? 
                                    #-----------------------------------------------------------------------------------------------------#
								    }
                            #-------------------------------------------------------------------------------------------------------------#
							}
                            #-------------------------------------------------------------------------------------------------------------#
                            ?>
								</tbody>
							</table>
						</div>
                            <?
							if ($ViewSW==1) {
							?>
						<div class="uk-form-row" style="text-align:center; margin-top:20px;">
							<a type="button" href="javascript:PassKpiIndicatorAction(1,'<?=$Hr_MemberName?>')" class="md-btn md-btn-primary" style="background:#F0F0F0; color:#5B5B5B;"><?=$반려[$LangID]?></a>
							<a type="button" href="javascript:PassKpiIndicatorAction(2,'<?=$Hr_MemberName?>')" class="md-btn md-btn-primary" style="background:#1CC6EA;"><?=$확인[$LangID]?></a>
						</div>
						    <?
							}
                            ?>
					</div> 
				</div>
			</div>
		</div>
        </form>
<?
#----------------------------------------------------------------------------------------------------------------------------------#
}
#----------------------------------------------------------------------------------------------------------------------------------#
?>

	</div>
</div>


<?
include_once('./inc_common_list_js.php');
?>
<!-- ============== only this page js ============== -->

<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "hr_staff_target_kpi_list.php";
	document.SearchForm.submit();
}
//-------------------------------------------------------------------------------------------------------------------------//
// 단문비교함수
//-------------------------------------------------------------------------------------------------------------------------//
function jviif( sw, a, b ) {

      if (sw) {
            return a;
      } else {
            return b;
      }

}
//-------------------------------------------------------------------------------------------------------------------------//
// KPI 선택문항 저장하기
//-------------------------------------------------------------------------------------------------------------------------//
function SaveKpiIndicatorAction() {
     
    var form           = document.RegForm;
	var SearchState    = form.SearchState.value;
	var KpiIndicatorID = "";
    var check_nums     = form.elements.length;
    for(var i = 0; i < check_nums; i++) {                // 폼안에 들어있는 요소(필드명)들을 읽어들인다.
            if (form.elements[i].name == 'KPI_CHECKID[]' && form.elements[i].checked==true) {
                  KpiIndicatorID  = KpiIndicatorID + jviif(KpiIndicatorID,"/","") + form.elements[i].value;
            }
    }
	if (KpiIndicatorID=="") {
		    alert("<?=$한개_이상의_KPI_문항을_선택_하세요[$LangID]?>");
			return;
	}
	form.KpiIndicatorID.value = KpiIndicatorID;

	UIkit.modal.confirm(
		'<?=$선택된_항목을_목표설정_하시겠습니까[$LangID]?>', 
		function(){ 
			document.RegForm.target = "_top";
			document.RegForm.action = "hr_staff_target_list.php";
			document.RegForm.submit();
		}
	);

}
//-------------------------------------------------------------------------------------------------------------------------//
// 부분 업적목표 반려 또는 승인처리
//-------------------------------------------------------------------------------------------------------------------------//
function PassKpiIndicatorAction(p,memname) {
    
	if (p == 1) {
			UIkit.modal.confirm(
				'['+memname+'] <?=$목표설정을_반려_하시겠습니까[$LangID]?>', 
				function(){ 
					document.RegForm.target = "_top";
					document.RegForm.action = "hr_staff_target_action.php?Eva_Pass=1";
					document.RegForm.submit();
				}
			);
	} else {
			UIkit.modal.confirm(
				'['+memname+'] <?=$목표설정을_승인_하시겠습니까[$LangID]?>', 
				function(){ 
					document.RegForm.target = "_top";
					document.RegForm.action = "hr_staff_target_action.php?Eva_Pass=2";
					document.RegForm.submit();
				}
			);
	}

}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>