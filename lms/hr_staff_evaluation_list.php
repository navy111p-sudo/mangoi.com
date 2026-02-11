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

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 88;
$SubMenuID = 8861;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');


$MemberLoginID     = isset($_COOKIE["LoginMemberID"   ]) ? $_COOKIE["LoginMemberID"   ] : "";
$SearchState       = isset($_REQUEST["SearchState"    ]) ? $_REQUEST["SearchState"    ] : "";
$CallSearchState   = isset($_REQUEST["CallSearchState"]) ? $_REQUEST["CallSearchState"] : "";
$KpiIndicatorID    = isset($_REQUEST["KpiIndicatorID" ]) ? $_REQUEST["KpiIndicatorID" ] : "";
$EvaluationState   = isset($_REQUEST["EvaluationState"]) ? $_REQUEST["EvaluationState"] : "";
$TargetMenu        = isset($_REQUEST["TargetMenu"     ]) ? $_REQUEST["TargetMenu"     ] : "";
if (!$TargetMenu) {
       $TargetMenu = 1; 
}
$Hr_TargetState    = 1;
$DB_KpiIndicatorID = "N";
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 회원 고유아이디(번호, 조직아이디) 찾기
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "select T.*,M.* from Members as M 
			  left join Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
			      where M.MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$My_MemberID     = $Row["MemberID"];
$My_MemberName   = $Row["MemberName"];
$My_OrganLevel   = $Row["Hr_OrganLevel"];    
$My_OrganLevelID = $Row["Hr_OrganLevelID"];
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 찾은회원으로 조직아이디로 조직명칭 찾기
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "select A.*,
				ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=A.Hr_OrganLevel1ID), '') as Hr_OrganLevelName1, 
				ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=A.Hr_OrganLevel2ID), '') as Hr_OrganLevelName2,
				ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=A.Hr_OrganLevel3ID), '') as Hr_OrganLevelName3,
				ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=A.Hr_OrganLevel4ID), '') as Hr_OrganLevelName4
		from Hr_OrganLevels A 
		where A.Hr_OrganLevelState=1 or A.Hr_OrganLevelID=:Hr_OrganLevelID";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':Hr_OrganLevelID', $My_OrganLevelID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$Db_Hr_OrganLevel1ID   = "";
$Db_Hr_OrganLevel2ID   = "";
$Db_Hr_OrganLevel3ID   = "";
$Db_Hr_OrganLevel4ID   = "";
$Str_Hr_OrganLevelName = "";
while ($Row2 = $Stmt->fetch()) {
	
	$Db_Hr_OrganLevelID    = $Row2["Hr_OrganLevelID"];
	$Db_Hr_OrganLevel1ID   = $Row2["Hr_OrganLevel1ID"];
	$Db_Hr_OrganLevel2ID   = $Row2["Hr_OrganLevel2ID"];
	$Db_Hr_OrganLevel3ID   = $Row2["Hr_OrganLevel3ID"];
	$Db_Hr_OrganLevel4ID   = $Row2["Hr_OrganLevel4ID"];
	$Db_Hr_OrganLevelName1 = $Row2["Hr_OrganLevelName1"];
	$Db_Hr_OrganLevelName2 = $Row2["Hr_OrganLevelName2"];
	$Db_Hr_OrganLevelName3 = $Row2["Hr_OrganLevelName3"];
	$Db_Hr_OrganLevelName4 = $Row2["Hr_OrganLevelName4"];

	$Str_Db_Hr_OrganLevelName = $Db_Hr_OrganLevelName1;
	if ($Db_Hr_OrganLevelName2!=""){
		$Str_Db_Hr_OrganLevelName .= " > " . $Db_Hr_OrganLevelName2;
	}
	if ($Db_Hr_OrganLevelName3!=""){
		$Str_Db_Hr_OrganLevelName .= " > " . $Db_Hr_OrganLevelName3;
	}
	if ($Db_Hr_OrganLevelName4!=""){
		$Str_Db_Hr_OrganLevelName .= " > " . $Db_Hr_OrganLevelName4;
	}
	if ($Db_Hr_OrganLevelID==$My_OrganLevelID and !$Str_Hr_OrganLevelName) {
		   $Str_Hr_OrganLevelName = $Str_Db_Hr_OrganLevelName;
		   break;
	}  
	
}
$Stmt = null;
?>


<div id="page_content">
	<div id="page_content_inner">

        <h3 class="heading_b uk-margin-bottom"><?=$My_MemberName?><!---span style="font-size:10px;">(LEVEL : <?=$My_OrganLevel?>. <?=$Str_Hr_OrganLevelName?> | LVID : <?=$Db_Hr_OrganLevel1ID?> > <?=$Db_Hr_OrganLevel2ID?> > <?=$Db_Hr_OrganLevel3ID?> > <?=$Db_Hr_OrganLevel4ID?> )</span---->님 
        <?
		if ($My_OrganLevel > 1 and $My_OrganLevel < 4) {
				?>

				<a type="button" href="hr_staff_evaluation_list.php?TargetMenu=1" class="md-btn md-btn-primary" style="background:<?if ( $TargetMenu==1 ){?>#2196F3<?} else {?>#6FB9F7<?}?>;"><?=$개인[$LangID]?></a>
				<a type="button" href="hr_staff_evaluation_list.php?TargetMenu=2" class="md-btn md-btn-primary" style="background:<?if ( $TargetMenu==2 ){?>#2196F3<?} else {?>#6FB9F7<?}?>;"><?=$부문[$LangID]?></a>
				<?=$업적평가실시[$LangID]?>

				<?
		} else if ($My_OrganLevel==1) {   // 최종상사인 경우

                $TargetMenu = 2; 
                ?>
                <?=$부문[$LangID]?> <?=$업적평가실시[$LangID]?>
		        <?

		} else {

                ?>
                <?=$업적평가실시[$LangID]?>
		        <?
		}
		?>
		</h3>

        <form name="SearchForm" method="get">
		<input type="hidden" id="TargetMenu" name="TargetMenu" value="<?=$TargetMenu?>" />
        <div class="md-card" style="margin-bottom:10px;">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin="">

                    <div class="uk-width-medium-1-3">
                        <div class="uk-margin-small-top">
                            <?=$평가리스트_선택[$LangID]?>
                            <select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
                                <option value=""><?=$선택[$LangID]?></option>
                                    <?php
                                    $Hr_EvaluationID       = "";
									$Hr_EvaluationTypeID   = "";
									$Hr_EvaluationTypeName = "";

                                    $AddSqlWhere = "1=1";
                                    $AddSqlWhere = $AddSqlWhere . " and A.Hr_EvaluationState=1";

                                    $Sql = "
                                            select 
                                                A.*,
                                                ifnull(B.CenterName, '-') as CenterName,
                                                C.Hr_EvaluationTypeName,
                                                D.Hr_EvaluationCycleName
                                            from Hr_Evaluations A 
                                                left outer join Centers B on A.CenterID=B.CenterID 
                                                inner join Hr_EvaluationTypes C on A.Hr_EvaluationTypeID=C.Hr_EvaluationTypeID 
                                                inner join Hr_EvaluationCycles D on A.Hr_EvaluationCycleID=D.Hr_EvaluationCycleID 
                                            where ".$AddSqlWhere." 
                                            order by A.Hr_EvaluationYear desc, A.Hr_EvaluationMonth desc, A.Hr_EvaluationDate desc";

                                    $Stmt = $DbConn->prepare($Sql);
                                    $Stmt->execute();
                                    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                                    $ListCount = 1;
                                    while($Row = $Stmt->fetch()) {
                                        
                                        $Hr_EvaluationID       = $Row["Hr_EvaluationID"];
                                        $Hr_EvaluationYear     = $Row["Hr_EvaluationYear"];
                                        $Hr_EvaluationMonth    = $Row["Hr_EvaluationMonth"];
                                        $Hr_EvaluationName     = $Row["Hr_EvaluationName"];

                                        $Str_Hr_EvaluationYear = $Hr_EvaluationYear."년 ".substr("0".$Hr_EvaluationMonth,-2)."월 ".$Hr_EvaluationName;
										
										$Hr_EvaluationTypeID   = $Row["Hr_EvaluationTypeID"];
										$Hr_EvaluationTypeName = $Row["Hr_EvaluationTypeName"];
                                        ?>
                                        <option value="<?=$Hr_EvaluationID?>" <? if ($Hr_EvaluationID==$SearchState){?> selected <?}?>><?=$Str_Hr_EvaluationYear?> <?=$평가[$LangID]?></option>
                                        <?php
                                        $ListCount ++;
                                    }
                                    $Stmt = null;
                                    ?>

                            </select> 
                        </div>
                    </div>
                    <div id="KPI_Button" class="uk-width-medium-2-3" style="line-height:160%;">
						<?=$주요실적_및_산출물을_작성하시고_측정산식과_평가척도에_맞추어_원점수를_입력해주세요[$LangID]?><br>
						<?=$원점수는_주요실적_및_산출물에서_계산된_자기_평가_점수입니다[$LangID]?><br>
						<?=$환산점수는_원점수에_가중치를_반영한_점수입니다[$LangID]?>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <?
        #=================================================================================================================================#
        #================================================================ 개인목표평가 =======================================================#
        #=================================================================================================================================#
		if ($TargetMenu==1) {
        #=================================================================================================================================#
        ?>	
		<form name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" id="SearchState"         name="SearchState"         value="<?=$SearchState?>" style="width:10%;" />
        <input type="hidden" id="Hr_EvaluationID"     name="Hr_EvaluationID"     value="<?=$SearchState?>" style="width:10%;" />
        <input type="hidden" id="Hr_EvaluationTypeID" name="Hr_EvaluationTypeID" value="<?=$Hr_EvaluationTypeID?>" style="width:10%;" />
        <input type="hidden" id="Hr_MemberID"         name="Hr_MemberID"         value="<?=$My_MemberID?>" style="width:10%;" />
        <input type="hidden" id="Hr_OrganLevelID"     name="Hr_OrganLevelID"     value="<?=$My_OrganLevelID?>" style="width:10%;" />
		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
                    <?
					$line_cnt = 0;
                    #---------------------------------------------------------------------------------------------------------------------#
					if ($SearchState) { 
                    #---------------------------------------------------------------------------------------------------------------------#
							$Sql = "select count(*) TotalRowCount from Hr_Staff_Target 
											                     where MemberID=".$My_MemberID." and 
												                       Hr_EvaluationID=".$SearchState." and
																	   Hr_TargetState='9' and 
												                       Hr_UseYN='Y'";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
							$Row = $Stmt->fetch();
							$Stmt = null;
                            #-------------------------------------------------------------------------------------------------------------#
					        if ($Row["TotalRowCount"] > 0)  {
                            #-------------------------------------------------------------------------------------------------------------#
					              ?>       
                            <h4>▷ <?=$항목별_평가[$LangID]?></h4>
							<table class="uk-table uk-table-align-vertical">
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="5%"><?=$연번[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="10%"><?=$업적목표[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="10%"><?=$KPI_핵심성과지표[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="19%"><?=$측정산식[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="19%"><?=$평가척도[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="5%"><?=$가중치[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="20%"><?=$주요실적_및_산출물[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" colspan="2" style="background:#F6F6F6;" width="12%"><?=$평가[$LangID]?></td>
                                </tr>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" width="6%"><?=$평가[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" width="6%"><?=$환산점수[$LangID]?></td>
                                </tr>
                                <tbody>
                                  <?
                            #-------------------------------------------------------------------------------------------------------------#
							}
                            #-------------------------------------------------------------------------------------------------------------#
                            $Hr_SelfComment = "";   // 자기평가
                            $Hr_TotalPoint  = 0;    // 총점수
                            #-------------------------------------------------------------------------------------------------------------#
							$Sql = "select * from Hr_Staff_Target 
											 where MemberID=".$My_MemberID." and 
												   Hr_EvaluationID=".$SearchState." and
												   Hr_TargetState='9' and 
												   Hr_UseYN='Y'";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
                            #-------------------------------------------------------------------------------------------------------------#
							while ($Row = $Stmt->fetch()) {
                            #-------------------------------------------------------------------------------------------------------------#
							        $Hr_TargetID       = $Row["Hr_TargetID"       ];
								    $Hr_KpiIndicatorID = $Row["Hr_KpiIndicatorID" ];
                                    $Hr_TargetName     = $Row["Hr_TargetName"     ]; 
                                    $Hr_TargetAddValue = $Row["Hr_TargetAddValue" ]; 
                                    $Hr_TargetState    = $Row["Hr_TargetState"    ]; 
                                    // 평가
									$Hr_GoodWork       = $Row["Hr_GoodWork"       ];   // 실적
									$Hr_SelfPoint      = $Row["Hr_SelfPoint"      ];   // 원점수
									$Hr_ChangePoint    = $Row["Hr_ChangePoint"    ];   // 환산점수
                                    $Hr_SelfComment    = $Row["Hr_SelfComment"    ];   // 자기평가
                                    $Hr_TotalPoint     = $Row["Hr_SelfTotalPoint" ];   // 자기합계
                                    $Hr_EvaluationState= $Row["Hr_EvaluationState"];   // 평가제출상태
                                    
									$EvaluationState   = $Hr_EvaluationState;
									if ($Hr_SelfPoint==0) {
                                            $Hr_SelfPoint = "";
									}
									if ($Hr_ChangePoint==0) {
                                            $Hr_ChangePoint = "";
									}
                                    $Hr_KpiCheck       = "checked";
									$readonly_val      = "";
									$able_val          = "";
									$Target_BKColor    = "#fff";
									if ($Hr_EvaluationState >= 9) {
									        $Hr_KpiCheck    = "";
											$readonly_val   = "readonly";
											$able_val       = "disabled";
											$Target_BKColor = "#F9F9F9";
									}
									$DB_KpiIndicatorID = 'Y';
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
											if ($KpiIndicatorID) {
											       $KpiIndicatorID .= "/";
											}
											$KpiIndicatorID .= $Hr_KpiIndicatorID;
                                            $line_cnt++;
                                            ?>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center"> 
										   <?=$line_cnt?>
										   <input type="hidden" id="Hr_TargetID_<?=$line_cnt?>" name="Hr_TargetID_<?=$line_cnt?>" value="<?=$Hr_TargetID?>" />
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_TargetName?>
                                           <input type="hidden" id="KPI_TARGET1_<?=$line_cnt?>" name="KPI_TARGET1_<?=$line_cnt?>" value="<?=$Hr_TargetName?>" />
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_KpiIndicatorName?>
                                           <input type="hidden" id="KPI_TARGET2_<?=$line_cnt?>" name="KPI_TARGET2_<?=$line_cnt?>" value="<?=$Hr_KpiIndicatorName?>" />
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_KpiIndicatorFormula?>
                                           <input type="hidden" id="KPI_TARGET3_<?=$line_cnt?>" name="KPI_TARGET3_<?=$line_cnt?>" value="<?=$Hr_KpiIndicatorFormula?>" />
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_KpiIndicatorMeasure?>
                                           <input type="hidden" id="KPI_TARGET4_<?=$line_cnt?>" name="KPI_TARGET4_<?=$line_cnt?>" value="<?=$Hr_KpiIndicatorMeasure?>" />
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_TargetAddValue?>%
                                           <input type="hidden" id="KPI_TARGET5_<?=$line_cnt?>" name="KPI_TARGET5_<?=$line_cnt?>" value="<?=$Hr_TargetAddValue?>" />
                                        </td>
                                         <? 
										 if ($Hr_EvaluationState>=9) {   // 제출상태
										         ?>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_GoodWork?>
										   <input type="hidden" id="KPI_TARGET6_<?=$line_cnt?>" name="KPI_TARGET6_<?=$line_cnt?>" value="<?=$Hr_GoodWork?>" />
										</td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_SelfPoint?>
										   <input type="hidden" id="KPI_TARGET7_<?=$line_cnt?>" name="KPI_TARGET7_<?=$line_cnt?>" value="<?=$Hr_SelfPoint?>" />
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_ChangePoint?>
                                           <input type="hidden" id="KPI_TARGET8_<?=$line_cnt?>" name="KPI_TARGET8_<?=$line_cnt?>" value="<?=$Hr_ChangePoint?>" />
                                        </td>
                                                 <? 

                                         } else {

										         ?>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <textarea id="KPI_TARGET6_<?=$line_cnt?>" name="KPI_TARGET6_<?=$line_cnt?>" style="height:120px;width:90%; background:#F9F9F9;border:1px solid #cccccc;padding:10px;"><?=$Hr_GoodWork?></textarea>
										</td>
                                        <td class="uk-text-wrap uk-table-td-center">
                                           <input type="text" id="KPI_TARGET7_<?=$line_cnt?>" name="KPI_TARGET7_<?=$line_cnt?>" value="<?=$Hr_SelfPoint?>" onKeyup="Point_Calc(<?=$line_cnt?>);" style="width:60%; background:#F9F9F9;border:1px solid #cccccc;padding:10px; text-align:center; color:#555; font-size:1.2em;"> 
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
                                           <input type="text" id="KPI_TARGET8_<?=$line_cnt?>" name="KPI_TARGET8_<?=$line_cnt?>" readonly value="<?=$Hr_ChangePoint?>" style="width:60%; background:#ffffff;border:0px solid #cccccc;padding:10px; text-align:center; color:#555; font-size:1.5em;"> 
                                        </td>
									             <?
										 } 
										 ?>
                                    </tr>
							        <? 
                                    #-----------------------------------------------------------------------------------------------------#
								    }
                            #-------------------------------------------------------------------------------------------------------------#
                            }
                            #-------------------------------------------------------------------------------------------------------------#
						    $Stmt = null;
                            ?>
                                </tbody>
                            </table>
                            <?
                            #-------------------------------------------------------------------------------------------------------------#
					        if ($line_cnt > 0) { 
                            #-------------------------------------------------------------------------------------------------------------#
					        ?>
							<table class="uk-table uk-table-align-vertical" border=0>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" width="80%" style="background:#F6F6F6; text-align:left">▷ <?=$자기_평가_의견_우수한_점_등_작성[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" width="20%" style="background:#F6F6F6;">▷ <?=$자기평가_총점[$LangID]?></td>
                                </tr>
								   <?
								   if ($EvaluationState >= 9) {
								   ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" width="80%" style="text-align:left;">
                                       <?=$Hr_SelfComment?>
								   </td>
                                   <td class="uk-text-wrap uk-table-td-center" width="20%">
                                       <span style="font-size:5.0em; color:#555;"><?=$Hr_TotalPoint?></span>
									   <span style="font-size:4.0em; color:#ddd">점</span>
								   </td>
                                </tr>
								   <?
							       } else {
								   ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" width="80%">
                                       <textarea id="EVA_COMMENT" name="EVA_COMMENT" style="height:150px;width:95%; background:#F9F9F9;border:1px solid #cccccc;padding:10px;"><?=$Hr_SelfComment?></textarea>
								   </td>
                                   <td class="uk-text-wrap uk-table-td-center" width="20%">
                                       <input type="text" id="EVA_TOTAL" name="EVA_TOTAL" value="<?=$Hr_TotalPoint?>" readonly style="height:150px;width:60%; background:#fff;border:0px; color:#555; text-align:right; font-size:5.0em;">
									   <span style="font-size:4.0em; color:#ddd">점</span>
								   </td>
                                </tr>
								   <?
							       }  
								   ?>
                            </table>
                            <?
                            #-------------------------------------------------------------------------------------------------------------#
					        }
                            #-------------------------------------------------------------------------------------------------------------#
					        ?>
						</div>
                    <?
                    #---------------------------------------------------------------------------------------------------------------------#
                    }
                    #---------------------------------------------------------------------------------------------------------------------#

                    #---------------------------------------------------------------------------------------------------------------------#
					if ($line_cnt > 0 and $EvaluationState < 9) { 
                    #---------------------------------------------------------------------------------------------------------------------#
					?>
 						<div class="uk-form-row" style="text-align:center; margin-top:20px;">
							<a type="button" href="javascript:HREvaluation_Act(1)" class="md-btn md-btn-primary" style="background:#CACACA; color:#5B5B5B;"><?=$임시저장[$LangID]?></a>
							<a type="button" href="javascript:HREvaluation_Act(2)" class="md-btn md-btn-primary"><?=$평가제출[$LangID]?></a>
						</div>
                    <?
                    #---------------------------------------------------------------------------------------------------------------------#
					} else if ($line_cnt > 0 and $EvaluationState >= 9) { 
                    #---------------------------------------------------------------------------------------------------------------------#
					       $eva_msg = $개인평가가_제출_되었습니다[$LangID];
						   if ($EvaluationState == 10) {
                                   $eva_msg = $P1차상사_평가가_완료_되었습니다[$LangID];
						   } else if ($EvaluationState == 11) {
                                   $eva_msg = $P2차상사_평가가_완료_되었습니다[$LangID];
						   } else if ($EvaluationState == 12) {
						           $eva_msg = $최종상사_평가가_완료_되었습니다[$LangID];
						   }
					?>
                        <div class="uk-form-row" style="text-align:center; padding:30px; color:#BCBCBC; font-size:1.5em;">
                             <?=$eva_msg?>
                        </div>
                    <?
                    #---------------------------------------------------------------------------------------------------------------------#
					} else { 
                    #---------------------------------------------------------------------------------------------------------------------#
							?>
                        <div class="uk-form-row" style="text-align:center; padding:30px; color:#BCBCBC; font-size:1.5em;">
                             <?=$목표설정이_미등록_혹은_승인되지_않았습니다_등록_승인_후_평가가_가능합니다[$LangID]?>
                        </div>
							<?
                    #---------------------------------------------------------------------------------------------------------------------#
                    } 
                    #---------------------------------------------------------------------------------------------------------------------#
                    ?>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" id="KpiIndicatorID" name="KpiIndicatorID" value="<?=$KpiIndicatorID?>" style="width:10%;" />
        <input type="hidden" id="line_cnt"       name="line_cnt"       value="<?=$line_cnt?>" style="width:10%;" />
        </form>
		<?
        #=================================================================================================================================#
        #================================================== 부문(소속부서) 목표평가(확인) ========================================================#
        #=================================================================================================================================#
        } else {
        #=================================================================================================================================#
        ?>	
		<form name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" id="TargetMenu"          name="TargetMenu"          value="<?=$TargetMenu?>" />
		<input type="hidden" id="SearchState"         name="SearchState"         value="<?=$SearchState?>" style="width:10%;" />
        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div id="kpi_listup" class="uk-overflow-container">
		            <?
                    #---------------------------------------------------------------------------------------------------------------------#
					if ($SearchState) { 
                    #---------------------------------------------------------------------------------------------------------------------#
							$AddWhere_Level = "";
                            if ($Db_Hr_OrganLevel1ID > 0) {
								   $AddWhere_Level .= " and C.Hr_OrganLevel1ID=" . $Db_Hr_OrganLevel1ID;
                            }
							if ($Db_Hr_OrganLevel2ID > 0) {
                                   $AddWhere_Level .= " and C.Hr_OrganLevel2ID=" . $Db_Hr_OrganLevel2ID;
                            }
							if ($Db_Hr_OrganLevel3ID > 0) {
                                   $AddWhere_Level .= " and C.Hr_OrganLevel3ID=" . $Db_Hr_OrganLevel3ID;
                            }
							if ($Db_Hr_OrganLevel4ID > 0) {
                                   $AddWhere_Level .= " and C.Hr_OrganLevel4ID=" . $Db_Hr_OrganLevel4ID;
                            }
							$ViweTable = "select AAAA.* 
											from Hr_OrganLevelTaskMembers AAAA 
											inner join Members BBBB on AAAA.MemberID=BBBB.MemberID and BBBB.MemberState=1 and AAAA.MemberID<>" . $My_MemberID;
							$Sql = "select 
										A.*,
										B.MemberName,
										TT.*,
										ifnull((select sum(Hr_ChangePoint) from Hr_Staff_Target where MemberID=B.MemberID and Hr_EvaluationID=".$SearchState." and Hr_EvaluationState>=9),'') as TT_SelfPoint,
										ifnull((select sum(Hr_FirstBossPoint) from Hr_Staff_Target where MemberID=B.MemberID and Hr_EvaluationID=".$SearchState." and Hr_EvaluationState>=9),'') as TT_FirstBossPoint,
										ifnull((select sum(Hr_SecondBossPoint) from Hr_Staff_Target where MemberID=B.MemberID and Hr_EvaluationID=".$SearchState." and Hr_EvaluationState>=9),'') as TT_SecondBossPoint,
										ifnull((select sum(Hr_EndBossPoint) from Hr_Staff_Target where MemberID=B.MemberID and Hr_EvaluationID=".$SearchState." and Hr_EvaluationState>=9),'') as TT_EndBossPoint,

										ifnull(D.Hr_OrganTask2Name, '') as Hr_OrganTask2Name,
										ifnull(E.Hr_OrganTask1Name, '') as Hr_OrganTask1Name,

										ifnull((select Hr_OrganLevelID   from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel1ID), '') as Hr_OrganLevel1ID, 
										ifnull((select Hr_OrganLevelID   from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel2ID), '') as Hr_OrganLevel2ID,
										ifnull((select Hr_OrganLevelID   from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel3ID), '') as Hr_OrganLevel3ID,
										ifnull((select Hr_OrganLevelID   from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel4ID), '') as Hr_OrganLevel4ID,
										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel1ID), '') as Hr_OrganLevelName1, 
										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel2ID), '') as Hr_OrganLevelName2,
										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel3ID), '') as Hr_OrganLevelName3,
										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel4ID), '') as Hr_OrganLevelName4,

										AA.MemberID as T_MemberID,
										AA.Hr_OrganLevel as T_Hr_OrganLevel,
										AA.Hr_OrganPositionName as T_Hr_OrganPositionName,
										BB.MemberName as T_MemberName,

										ifnull(DD.Hr_OrganTask2Name,'') as T_Hr_OrganTask2Name,
										ifnull(EE.Hr_OrganTask1Name,'') as T_Hr_OrganTask1Name,

										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel1ID), '') as T_Hr_OrganLevelName1, 
										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel2ID), '') as T_Hr_OrganLevelName2,
										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel3ID), '') as T_Hr_OrganLevelName3,
										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel4ID), '') as T_Hr_OrganLevelName4,

										(select count(*) from ($ViweTable) VVVV where VVVV.Hr_OrganLevel<A.Hr_OrganLevel and (VVVV.Hr_OrganLevelID=C.Hr_OrganLevel1ID or VVVV.Hr_OrganLevelID=C.Hr_OrganLevel2ID or VVVV.Hr_OrganLevelID=C.Hr_OrganLevel3ID)) as T_BossCount

									from ($ViweTable) A 

										inner join Members B on A.MemberID=B.MemberID 
										inner join Hr_Staff_Target TT on TT.MemberID=B.MemberID and TT.Hr_EvaluationID=".$SearchState." and TT.Hr_EvaluationState>=9

										inner join Hr_OrganLevels C on C.Hr_OrganLevelID=A.Hr_OrganLevelID ".$AddWhere_Level."
										left outer join Hr_OrganTask2 D on D.Hr_OrganTask2ID=A.Hr_OrganTask2ID 
										left outer join Hr_OrganTask1 E on E.Hr_OrganTask1ID=D.Hr_OrganTask1ID

										left outer join ($ViweTable) AA on AA.Hr_OrganLevel < A.Hr_OrganLevel and AA.MemberID=" . $My_MemberID . "
										left outer join Members BB on AA.MemberID=BB.MemberID
										left outer join Hr_OrganLevels CC on AA.Hr_OrganLevelID=CC.Hr_OrganLevelID 
										left outer join Hr_OrganTask2 DD on AA.Hr_OrganTask2ID=DD.Hr_OrganTask2ID 
										left outer join Hr_OrganTask1 EE on DD.Hr_OrganTask1ID=EE.Hr_OrganTask1ID 
									
									Group By TT.MemberID";
							?>

							<table class="uk-table uk-table-align-vertical">
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="5%">NO</td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="8%"><?=$성명[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="15%"><?=$소속부서[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="10%"><?=$직무[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="6%"><?=$자기평가[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="6%"><?=$P1차상사[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="6%"><?=$P2차상사[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="6%"><?=$최종상사[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="6%"><?=$등급[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="6%"><?=$최종점수[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="12%"><?=$평가여부[$LangID]?></td>
                                </tr>
                                <tbody>
							<?
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
                            $line_cnt = 0;
							#-------------------------------------------------------------------------------------------------------------#
							while($Row = $Stmt->fetch()) {
							#-------------------------------------------------------------------------------------------------------------#
                                    $line_cnt++;

									$Hr_EvaluationID    = $Row["Hr_EvaluationID"];
									$Hr_EvaUseYN        = $Row["Hr_EvaUseYN"];
									$Hr_EvaluationState = $Row["Hr_EvaluationState"];
									$T_BossCount        = $Row["T_BossCount"];          // 평가할 라인에 있는 상사의 숫자 - 1
									//=================== 자기 자신 ======================
									$MemberID = $Row["MemberID"];

									$Hr_OrganLevel    = $Row["Hr_OrganLevel"];  
									$Hr_OrganLevel1ID = $Row["Hr_OrganLevel1ID"];
									$Hr_OrganLevel2ID = $Row["Hr_OrganLevel2ID"];
									$Hr_OrganLevel3ID = $Row["Hr_OrganLevel3ID"];
									$Hr_OrganLevel4ID = $Row["Hr_OrganLevel4ID"];

									$Level_Chi = $Hr_OrganLevel - $My_OrganLevel;       // 목록 평가의 소유자와 현재 로그인한 사용자간의 레벨 차이


									// 평가점수
									$Hr_SelfPoint       = $Row["TT_SelfPoint"      ];   // 자기 평가점수
									$Hr_FirstBossPoint  = $Row["TT_FirstBossPoint" ];   // 1차상사 평가점수
									$Hr_SecondBossPoint = $Row["TT_SecondBossPoint"];   // 2차상사 평가점수
									$Hr_EndBossPoint    = $Row["TT_EndBossPoint"   ];   // 최종상사 평가점수

									$MemberName = $Row["MemberName"];

									$Hr_OrganTask2Name  = $Row["Hr_OrganTask2Name"];
									$Hr_OrganTask1Name  = $Row["Hr_OrganTask1Name"];


									$Hr_OrganLevelName1 = $Row["Hr_OrganLevelName1"];
									$Hr_OrganLevelName2 = $Row["Hr_OrganLevelName2"];
									$Hr_OrganLevelName3 = $Row["Hr_OrganLevelName3"];
									$Hr_OrganLevelName4 = $Row["Hr_OrganLevelName4"];

									$Str_Hr_OrganLevelID = $Hr_OrganLevel1ID;
									$Level_None = "";
									if ($Hr_OrganLevel2ID!=""){
										$Str_Hr_OrganLevelID = $Hr_OrganLevel2ID;
									} else {
                                        $Level_None .= "N2"; 
									}
									if ($Hr_OrganLevel3ID!=""){
										$Str_Hr_OrganLevelID = $Hr_OrganLevel3ID;
									} else {
										if ($Level_None) {
                                               $Level_None .= "/";
										}
                                        $Level_None .= "N1"; 
									}
									if ($Hr_OrganLevel4ID!=""){
										$Str_Hr_OrganLevelID = $Hr_OrganLevel4ID;
									}

									$Str_Hr_OrganLevelName = $Hr_OrganLevelName1;
									if ($Hr_OrganLevelName2!=""){
										$Str_Hr_OrganLevelName = $Hr_OrganLevelName2;
									}
									if ($Hr_OrganLevelName3!=""){
										$Str_Hr_OrganLevelName = $Hr_OrganLevelName3;
									}
									if ($Hr_OrganLevelName4!=""){
										$Str_Hr_OrganLevelName = $Hr_OrganLevelName4;
									}
									//====================================================//
									//=================== 업적평가계산식 ======================//
									//====================================================//
									$Hr_EvaluationLevel    = $Row["Hr_EvaluationLevel"   ];     // 최종 업적 등급
									$Hr_EndEvaluationPoint = $Row["Hr_EndEvaluationPoint"];     // 최종 업적 점수
                                    ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$line_cnt?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$MemberName?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Str_Hr_OrganLevelName?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_OrganTask1Name?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_SelfPoint?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_FirstBossPoint?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_SecondBossPoint?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_EndBossPoint?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_EvaluationLevel?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_EndEvaluationPoint?></td>
                                   <td class="uk-text-wrap uk-table-td-center">
								    <?
                                    if ($Hr_EvaUseYN=='N') {

										 if (($T_BossCount==0 and $Hr_EvaluationState==9) or ($T_BossCount==1 and $Hr_EvaluationState==9 and $Level_Chi==2) or ($Level_Chi==1 and $Hr_EvaluationState==9) or ($Level_Chi==2 and $Hr_EvaluationState==10) or ($Level_Chi==3 and $Hr_EvaluationState==11) ) {
											if ($T_BossCount==0) $Level_Chi = 0;
									     ?>
											<a type="button" href="javascript:HRTargetEva_ViewOpen(1,<?=$Level_Chi?>,<?=$MemberID?>,<?=$Hr_EvaluationID?>,'<?=$Level_None?>','<?=$My_OrganLevel?>')" class="md-btn md-btn-primary" style="background:#1CC6EA;"><?=$검토하기[$LangID]?></a>
									     <?
									     } else {
											    if ($Level_Chi==1 and $Hr_EvaluationState>=10) {
												       ?>
											<a type="button" href="javascript:HRTargetEva_ViewOpen(2,<?=$Level_Chi?>,<?=$MemberID?>,<?=$Hr_EvaluationID?>,'<?=$Level_None?>','<?=$My_OrganLevel?>')" class="md-btn md-btn-primary" style="background:#0080c0;"><?=$P1차상사_검토완료[$LangID]?></a>
									                   <?
												} else if ($Level_Chi==2 and $Hr_EvaluationState>=11) {
												       ?>
											<a type="button" href="javascript:HRTargetEva_ViewOpen(2,<?=$Level_Chi?>,<?=$MemberID?>,<?=$Hr_EvaluationID?>,'<?=$Level_None?>','<?=$My_OrganLevel?>')" class="md-btn md-btn-primary" style="background:#0080c0;"><?=$P2차상사_검토완료[$LangID]?></a>
									                   <?
												} else if ($Level_Chi==3 and $Hr_EvaluationState==12) {
												       ?>
											<a type="button" href="javascript:HRTargetEva_ViewOpen(2,<?=$Level_Chi?>,<?=$MemberID?>,<?=$Hr_EvaluationID?>,'<?=$Level_None?>','<?=$My_OrganLevel?>')" class="md-btn md-btn-primary" style="background:#0080c0;"><?=$최종상사_검토중[$LangID]?></a>
									                   <?
												} else {
												       ?>
											<a type="button" href="javascript:HRTargetEva_ViewOpen(2,<?=$Level_Chi?>,<?=$MemberID?>,<?=$Hr_EvaluationID?>,'<?=$Level_None?>','<?=$My_OrganLevel?>')" class="md-btn md-btn-primary" style="background:#408080;"><?=$미실시[$LangID]?></a>
									                   <?
										        }
										 }

									} else {
										 
									     ?>
											<a type="button" href="javascript:HRTargetEva_ViewOpen(9,<?=$Level_Chi?>,<?=$MemberID?>,<?=$Hr_EvaluationID?>)" class="md-btn md-btn-primary" style="background:#008040; color:#FFF;"><?=$평가완료[$LangID]?></a>
									
									<?
                                    }
								    ?>
								   </td>
                                </tr>
                                    <?
                            #-------------------------------------------------------------------------------------------------------------#
                            }
                            #-------------------------------------------------------------------------------------------------------------#
						    $Stmt = null;
							if (!$line_cnt) {
							?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="text-align:center; padding:20px; color:#BCBCBC; font-size:1.5em;" colspan=12>
									  <?=$평가대상이_없습니다[$LangID]?>
								   </td>
                                </tr>
							<?
							}
							?>

                                </tbody>
                            </table>
					<? 
                    #---------------------------------------------------------------------------------------------------------------------#
                    }
                    #---------------------------------------------------------------------------------------------------------------------#
					?>
                        </div>
                        <div class="uk-form-row" style="text-align:center; padding:30px; color:#BCBCBC; font-size:1.5em;">
                    <?
					if (!$SearchState) {
                    ?>
                              <?=$평가리스트를_선택하시면_평가대상을_볼_수_있습니다[$LangID]?>
					<?
					}
                    ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		</form>
        <?
        #=================================================================================================================================#
        }
        #=================================================================================================================================#
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
function OpenEvaluationForm(Hr_EvaluationID){
	openurl = "hr_evaluation_form.php?Hr_EvaluationID="+Hr_EvaluationID;
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

function SearchSubmit(){
	document.SearchForm.action = "hr_staff_evaluation_list.php";
	document.SearchForm.submit();

}

//---------------------------------------------------------------------------------------------------------------------------//
// 숫자인지 체크
//---------------------------------------------------------------------------------------------------------------------------//
function IsNumberCalc(indata) {

      for(var i = 0; i < indata.length; i++) {
             var chr = indata.substr(i,1);
             if(chr < '0' || chr > '9') {
                  return false;
             }
      }
      return true;

}
//---------------------------------------------------------------------------------------------------------------------------//
// 평가점수 환산 및 총점 구하기
//---------------------------------------------------------------------------------------------------------------------------//
function Point_Calc(linecnt) {
      
	  var isnum = 'Y';
      if ( !IsNumberCalc(document.getElementById("KPI_TARGET7_"+linecnt).value) ) {
		     alert("숫자만 입력 하세요!");
			 isnum = 'N';
             document.getElementById("KPI_TARGET7_"+linecnt).value = "";
      } else {
		     if (document.getElementById("KPI_TARGET7_"+linecnt).value > 110) {
				   alert("S등급 110점을 초과할 수 없습니다!");
                   document.getElementById("KPI_TARGET7_"+linecnt).value = "";
			       isnum = 'N';
		     }
           
	  }
	  if ( !document.getElementById("KPI_TARGET7_"+linecnt).value ) {
		    document.getElementById("KPI_TARGET7_"+linecnt).value = 0;
	  }

      var add_val = parseInt(document.getElementById("KPI_TARGET5_"+linecnt).value);   // 가중치
      var org_val = parseInt(document.getElementById("KPI_TARGET7_"+linecnt).value);   // 원점수
	  var exc_obj = document.getElementById("KPI_TARGET8_"+linecnt);                   // 환산점수필드

	  var exc_val = org_val * (add_val / 100);
	  var exc_val = parseFloat(exc_val).toFixed(2);  // (소숫점2자리) <-> Math.ceil(exc_val)
	  exc_obj.value = exc_val;   // 환산점수 

	  var linetot_cnt = document.getElementById("line_cnt").value;
	  var eva_tot     = 0;
      for (lc=1; lc <= linetot_cnt; lc++) {
		   if ( !document.getElementById("KPI_TARGET8_"+lc).value ) {
				 document.getElementById("KPI_TARGET8_"+lc).value = 0;
		   }
	       eva_tot = eva_tot + parseFloat(document.getElementById("KPI_TARGET8_"+lc).value);   // 환산점수 누계
      }

	  var eva_obj   = document.getElementById("EVA_TOTAL");                    // 평가총점
      eva_obj.value = eva_tot.toFixed(2);
      
	  if ( isnum == 'N' ) {
		    document.getElementById("KPI_TARGET7_"+linecnt).value = "";
            return;
	  }
}

//-------------------------------------------------------------------------------------------------------------------------//
// 목표설정
//-------------------------------------------------------------------------------------------------------------------------//
function HREvaluation_Act(s) {
        
       var line_cnt = document.RegForm.line_cnt.value;
	   for(var i = 1; i <= line_cnt; i++) {

			  var kpi_target1 = document.getElementById("KPI_TARGET1_"+i).value;
			  var kpi_target6 = document.getElementById("KPI_TARGET6_"+i).value;
			  if (!kpi_target6) {
					alert(kpi_target1 + " 주요실적 및 산출물을 입력 하세요!");
					document.getElementById("KPI_TARGET6_"+i).focus(); 
					return;
			  }
			  var kpi_target7 = document.getElementById("KPI_TARGET7_"+i).value;
			  if (!kpi_target7) {
					alert(kpi_target1 + " 원점수를 입력 하세요!");
					document.getElementById("KPI_TARGET7_"+i).focus(); 
					return;
			  }

	   }
	   if (!document.getElementById("EVA_COMMENT").value) {
			  alert("자기 평가 의견을 입력 하세요!");
			  document.getElementById("EVA_COMMENT").focus(); 
			  return;
	   }
	   //-----------------------------------------------------------------------------------------------------------------//
	   if (s == 1) {       // 임시저장
	   //-----------------------------------------------------------------------------------------------------------------//
			 UIkit.modal.confirm(
				 '평가 항목들을 임시저장 하시겠습니까?', 
				 function(){ 
					   document.RegForm.action = "hr_staff_evaluation_action.php?EvaluationState=1";
					   document.RegForm.submit();
				 }
			 );  
	   //-----------------------------------------------------------------------------------------------------------------//
	   } else {            // 제출하기
	   //-----------------------------------------------------------------------------------------------------------------//
			 UIkit.modal.confirm(
				 '평가 항목들을 제출 하시겠습니까?<br>※ 한번 제출된 자료(9)는 수정할 수 없습니다! 주의 하세요! ※', 
				 function(){ 
					   document.RegForm.action = "hr_staff_evaluation_action.php?EvaluationState=9";
					   document.RegForm.submit();
				 }
			 );  
	   //-----------------------------------------------------------------------------------------------------------------//
	   }
	   //-----------------------------------------------------------------------------------------------------------------//

}
//-------------------------------------------------------------------------------------------------------------------------//
// 부문업적평가
//-------------------------------------------------------------------------------------------------------------------------//
function HRTargetEva_ViewOpen(vs,lvchi,MemberID,EvaluationID,LevelNone,MyOrgan) {

	var SearchState = document.RegForm.SearchState.value;
	var TargetMenu  = document.RegForm.TargetMenu.value;
    
	openurl = "hr_staff_evaluation_form.php?ViewSW=" + vs + "&LevelChi=" + lvchi + "&SearchState=" + SearchState + "&TargetMenu=" + TargetMenu + "&MemberID=" + MemberID + "&EvaluationID=" + EvaluationID + "&LevelNone=" + LevelNone + "&MyOrgan=" + MyOrgan;

    $.colorbox({    
        href:openurl
        ,width:"98%" 
        ,height:"95%"
        ,maxWidth: "1280"
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
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>