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
$SubMenuID = 8861;
include_once('./inc_top.php');
?>


 
<?php

$AddSqlWhere = "1=1";

$SearchState    = isset($_REQUEST["SearchState"   ]) ? $_REQUEST["SearchState"   ] : "";
$KpiIndicatorID = isset($_REQUEST["KpiIndicatorID"]) ? $_REQUEST["KpiIndicatorID"] : "";
$TargetMenu     = isset($_REQUEST["TargetMenu"    ]) ? $_REQUEST["TargetMenu"    ] : "";
$ViewSW         = isset($_REQUEST["ViewSW"        ]) ? $_REQUEST["ViewSW"        ] : "";
$LevelChi       = isset($_REQUEST["LevelChi"      ]) ? $_REQUEST["LevelChi"      ] : "";
// 부분목표에서....
$MemberID       = isset($_REQUEST["MemberID"      ]) ? $_REQUEST["MemberID"      ] : "";
$EvaluationID   = isset($_REQUEST["EvaluationID"  ]) ? $_REQUEST["EvaluationID"  ] : "";
$LevelNone      = isset($_REQUEST["LevelNone"     ]) ? $_REQUEST["LevelNone"     ] : "";
$MyOrgan        = isset($_REQUEST["MyOrgan"       ]) ? $_REQUEST["MyOrgan"      ] : "";

$nonelevel1 = "";
$nonelevel2 = "";
if ($LevelNone != "") {
     $levelnone_array = explode("/",$LevelNone);                
     if (count($levelnone_array) > 1) {
           $nonelevel1 = $levelnone_array[1];
           $nonelevel2 = $levelnone_array[0];
     } else {
           if ($LevelNone=='N1') {
                 $nonelevel1 = $LevelNone;
           } else {
                 $nonelevel2 = $LevelNone;
           }
     }  
} 
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 회원 고유아이디(번호, 조직아이디) 찾기
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "select T.*,M.* from Members as M 
              left join Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
                  where M.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Hr_MemberID     = $Row["MemberID"];
$Hr_MemberName   = $Row["MemberName"];
$Hr_OrganLevel   = $Row["Hr_OrganLevel"];    
$Hr_OrganLevelID = $Row["Hr_OrganLevelID"];
?>


<div id="page_content">
    <div id="page_content_inner">
        <?
        $EvaUseYN = 'N';
        if ($ViewSW==9) {
              $EvaUseYN = 'Y';
        } 
        ?>
        <h3 class="heading_b uk-margin-bottom">[<?=$Hr_MemberName?>] <?=$업적평가[$LangID]?></h3>

        <form name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
        <input type="hidden" name="SearchState"  value="<?=$SearchState?>" />
        <input type="hidden" name="TargetMenu"   value="<?=$TargetMenu?>" />
        <input type="hidden" name="MemberID"     value="<?=$MemberID?>" />
        <input type="hidden" name="EvaluationID" value="<?=$EvaluationID?>" />
        <input type="hidden" name="LevelNone"    value="<?=$LevelNone?>" />
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
                                                                 where MemberID=".$Hr_MemberID." and 
                                                                       Hr_EvaluationID=".$SearchState." and
                                                                       Hr_EvaluationState>=9 and 
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
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="12%"><?=$측정산식[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="12%"><?=$평가척도[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="5%"><?=$가중치[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="12%"><?=$주요실적_및_산출물[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" colspan="2" style="background:#F6F6F6;" width="12%"><?=$자기평가[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="6%"><?=$P1차상사평가[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="6%"><?=$P2차상사평가[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" rowspan="2" style="background:#F6F6F6;" width="6%"><?=$최종평가[$LangID]?></td>

                                </tr>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" width="6%"><?=$원점수[$LangID]?></td>
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
                                             where MemberID=".$Hr_MemberID." and 
                                                   Hr_EvaluationID=".$SearchState." and
                                                   Hr_EvaluationState>=9 and 
                                                   Hr_UseYN='Y'";
                            $Stmt = $DbConn->prepare($Sql);
                            $Stmt->execute();
                            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                            #-------------------------------------------------------------------------------------------------------------#
                            while ($Row = $Stmt->fetch()) {
                            #-------------------------------------------------------------------------------------------------------------#
                                    $Hr_TargetID          = $Row["Hr_TargetID"       ];
                                    $Hr_KpiIndicatorID    = $Row["Hr_KpiIndicatorID" ];
                                    $Hr_TargetName        = $Row["Hr_TargetName"     ]; 
                                    $Hr_TargetAddValue    = $Row["Hr_TargetAddValue" ]; 
                                    $Hr_TargetState       = $Row["Hr_TargetState"    ]; 
                                    // 평가
                                    $Hr_GoodWork          = $Row["Hr_GoodWork"       ];   // 실적
                                    $Hr_SelfPoint         = $Row["Hr_SelfPoint"      ];   // 원점수
                                    $Hr_ChangePoint       = $Row["Hr_ChangePoint"    ];   // 자기평가(환산)점수
                                    $Hr_FirstBossPoint    = $Row["Hr_FirstBossPoint" ];   // 1차상사점수
                                    $Hr_SecondBossPoint   = $Row["Hr_SecondBossPoint"];   // 2차상사점수
                                    $Hr_EndBossPoint      = $Row["Hr_EndBossPoint"   ];   // 최종상사점수

                                    $Hr_SelfComment       = $Row["Hr_SelfComment"      ];   // 자기평가
                                    $Hr_FirstBossComment  = $Row["Hr_FirstBossComment" ];   // 1차상사평가
                                    $Hr_SecondBossComment = $Row["Hr_SecondBossComment"];   // 2차상사평가
                                    $Hr_EndBossComment    = $Row["Hr_EndBossComment"   ];   // 3차상사평가

                                    $Hr_SelfTotalPoint    = $Row["Hr_SelfTotalPoint"  ];   // 자기합계
                                    $Hr_FirstTotalPoint   = $Row["Hr_FirstTotalPoint" ];   // 1차상사 합계
                                    $Hr_SecondTotalPoint  = $Row["Hr_SecondTotalPoint"];   // 2차상사 합계
                                    $Hr_EndTotalPoint     = $Row["Hr_EndTotalPoint"   ];   // 3차상사 합계

                                    $Hr_TotalPoint        = $Hr_SelfTotalPoint;     // 총점수
                                    if ($LevelChi==1) {
                                           $Hr_TotalPoint = $Hr_FirstTotalPoint;
                                    } else if ($LevelChi==2) {
                                           $Hr_TotalPoint = $Hr_SecondTotalPoint;
                                    } else if ($LevelChi==0 or $LevelChi==3) {
                                           $Hr_TotalPoint = $Hr_EndTotalPoint;
                                    }
                                    $Hr_EvaluationState = $Row["Hr_EvaluationState"];   // 평가제출상태
                                    
                                    $EvaluationState   = $Hr_EvaluationState;
                                    if ($Hr_SelfPoint==0) {
                                            $Hr_SelfPoint = "";
                                    }
                                    if ($Hr_ChangePoint==0) {
                                            $Hr_ChangePoint = "";
                                    }
                                    if ($Hr_FirstBossPoint==0) {
                                            $Hr_FirstBossPoint = "";
                                    }
                                    if ($Hr_SecondBossPoint==0) {
                                            $Hr_SecondBossPoint = "";
                                    }
                                    if ($Hr_EndBossPoint==0) {
                                            $Hr_EndBossPoint = "";
                                    }

                                    $Hr_KpiCheck       = "checked";
                                    $readonly_val      = "";
                                    $able_val          = "";
                                    $Target_BKColor    = "#fff";
                                    if ($Hr_EvaluationState == 9) {
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
                                            #---------------------------------------------------------------------------------------------#
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
                                           <?=$Hr_TargetAddValue?>
                                           <input type="hidden" id="KPI_TARGET5_<?=$line_cnt?>" name="KPI_TARGET5_<?=$line_cnt?>" value="<?=$Hr_TargetAddValue?>" />
                                        </td>
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
                                        #-------------------------------------------------------------------------------------------------#
                                        if ($LevelChi==1) {          // 1차 상사
                                        #-------------------------------------------------------------------------------------------------#
                                        ?>
                                        <td class="uk-text-wrap uk-table-td-center">
                                              <?
                                              if ($ViewSW==1) {
                                              ?>
                                           <input type="text" id="KPI_TARGET9_<?=$line_cnt?>" name="KPI_TARGET9_<?=$line_cnt?>" value="<?=$Hr_FirstBossPoint?>"  onKeyup="PointForm_Calc(1,<?=$line_cnt?>);" style="width:60%; background:#F9F9F9; border:1px solid #cccccc; padding:2px; text-align:center; color:#555; font-size:1.0em;" />
                                              <?
                                              } else {
                                              ?>
                                           <input type="text" id="KPI_TARGET9_<?=$line_cnt?>" name="KPI_TARGET9_<?=$line_cnt?>" value="<?=$Hr_FirstBossPoint?>" readonly onKeyup="PointForm_Calc(1,<?=$line_cnt?>);" style="width:60%; background:#fff; border:0px solid #cccccc; padding:2px; text-align:center; color:#555; font-size:1.0em;" />
                                              <?
                                              }
                                              ?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
                                           <?=$Hr_SecondBossPoint?>
                                           <input type="hidden" id="KPI_TARGET10_<?=$line_cnt?>" name="KPI_TARGET10_<?=$line_cnt?>" value="<?=$Hr_SecondBossPoint?>" />
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
                                           <?=$Hr_EndBossPoint?>
                                           <input type="hidden" id="KPI_TARGET11_<?=$line_cnt?>" name="KPI_TARGET11_<?=$line_cnt?>" value="<?=$Hr_EndBossPoint?>" />
                                        </td>
                                        <?
                                        #-------------------------------------------------------------------------------------------------#
                                        } else if ($LevelChi==2) {   // 2차 상사
                                        #-------------------------------------------------------------------------------------------------#
                                        ?>
                                        <td class="uk-text-wrap uk-table-td-center">
                                           <?=$Hr_FirstBossPoint?>
                                           <input type="hidden" id="KPI_TARGET9_<?=$line_cnt?>" name="KPI_TARGET9_<?=$line_cnt?>" value="<?=$Hr_FirstBossPoint?>" />
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
                                              <?
                                              if ($ViewSW==1) {
                                              ?>
                                           <input type="text" id="KPI_TARGET10_<?=$line_cnt?>" name="KPI_TARGET10_<?=$line_cnt?>" value="<?=$Hr_SecondBossPoint?>"  onKeyup="PointForm_Calc(2,<?=$line_cnt?>);" style="width:60%; background:#F9F9F9;border:1px solid #cccccc;padding:2px; text-align:center; color:#555; font-size:1.0em;" />
                                              <?
                                              } else {
                                              ?>
                                           <input type="text" id="KPI_TARGET10_<?=$line_cnt?>" name="KPI_TARGET10_<?=$line_cnt?>" value="<?=$Hr_SecondBossPoint?>" readonly  onKeyup="PointForm_Calc(2,<?=$line_cnt?>);" style="width:60%; background:#fff;border:0px solid #cccccc;padding:2px; text-align:center; color:#555; font-size:1.0em;" />
                                              <?
                                              }
                                              ?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
                                           <?=$Hr_EndBossPoint?>
                                           <input type="hidden" id="KPI_TARGET11_<?=$line_cnt?>" name="KPI_TARGET11_<?=$line_cnt?>" value="<?=$Hr_EndBossPoint?>" />
                                        </td>
                                        <?
                                        #-------------------------------------------------------------------------------------------------#
                                        } else if ($LevelChi==0 or $LevelChi==3) {   // 최종 상사
                                        #-------------------------------------------------------------------------------------------------#
                                        ?>
                                        <td class="uk-text-wrap uk-table-td-center">
                                           <?=$Hr_FirstBossPoint?>
                                           <input type="hidden" id="KPI_TARGET9_<?=$line_cnt?>" name="KPI_TARGET9_<?=$line_cnt?>" value="<?=$Hr_FirstBossPoint?>" />
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
                                           <?=$Hr_SecondBossPoint?> 
                                           <input type="hidden" id="KPI_TARGET10_<?=$line_cnt?>" name="KPI_TARGET10_<?=$line_cnt?>" value="<?=$Hr_SecondBossPoint?>" />
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
                                              <?
                                              if ($ViewSW==1) {
                                              ?>
                                           <input type="text" id="KPI_TARGET11_<?=$line_cnt?>" name="KPI_TARGET11_<?=$line_cnt?>" value="<?=$Hr_EndBossPoint?>" onKeyup="PointForm_Calc(3,<?=$line_cnt?>);" style="width:60%; background:#F9F9F9;border:1px solid #cccccc;padding:2px; text-align:center; color:#555; font-size:1.0em;" />
                                              <?
                                              } else {
                                              ?>
                                           <input type="text" id="KPI_TARGET11_<?=$line_cnt?>" name="KPI_TARGET11_<?=$line_cnt?>" value="<?=$Hr_EndBossPoint?>" readonly onKeyup="PointForm_Calc(3,<?=$line_cnt?>);" style="width:60%; background:#fff;border:0px solid #cccccc;padding:2px; text-align:center; color:#555; font-size:1.0em;" />
                                              <?
                                              }
                                              ?>
                                        </td>
                                        <?
                                        #-------------------------------------------------------------------------------------------------#
                                        }
                                        #-------------------------------------------------------------------------------------------------#
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
                                   <td class="uk-text-wrap uk-table-td-center" colspan=2 style="background:#F6F6F6; text-align:left">▷ <?=$자기평가_의견[$LangID]?></td>
                                </tr>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" colspan=2 style="background:#fff; text-align:left"><?=$Hr_SelfComment?></td>
                                </tr>
                                   <?
                                   if ($LevelChi == 2 and !$nonelevel1) {            // 2차상사
                                   ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" colspan=2 style="background:#F6F6F6; text-align:left">▷ <?=$P1차상사_평가_의견[$LangID]?></td>
                                </tr>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" colspan=2 style="background:#fff; text-align:left"><? if ($Hr_FirstBossComment) { echo $Hr_FirstBossComment; } else { echo "평가 미실시"; } ?></td>
                                </tr>
                                   <?
                                   } else if ($LevelChi==0 or $LevelChi == 3) {    // 최종상사
                                   ?>
                                           <?
                                           if (!$nonelevel1) {  
                                           ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" colspan=2 style="background:#F6F6F6; text-align:left">▷ <?=$P1차상사_평가_의견[$LangID]?></td>
                                </tr>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" colspan=2 style="background:#fff; text-align:left"><? if ($Hr_FirstBossComment) { echo $Hr_FirstBossComment; } else { echo $평가_미실시[$LangID]; } ?></td>
                                </tr>
                                           <?
                                           }
                                           if (!$nonelevel2) {  
                                           ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" colspan=2 style="background:#F6F6F6; text-align:left">▷ <?=$P2차상사_평가_의견[$LangID]?></td>
                                </tr>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" colspan=2 style="background:#fff; text-align:left"><? if ($Hr_SecondBossComment) { echo $Hr_SecondBossComment; } else { echo $평가_미실시[$LangID]; } ?></td>
                                </tr>
                                   <?
                                           } 
                                   }  
                                   ?>

                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" width="80%" style="background:#F6F6F6; text-align:left">▷ <? if ($LevelChi < 3) { echo $LevelChi . "차"; } else { echo $최종[$LangID]; }?><?=$상사_평가_의견[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" width="20%" style="background:#F6F6F6;">▷ <?=$평가총점[$LangID]?></td>
                                </tr>
                                <tr>
                                   <?
								   $EVA_COMMENT = "";
                                   if ($LevelChi == 1) {
                                         $EVA_COMMENT = $Hr_FirstBossComment;
                                   } else if ($LevelChi == 2) {
                                         $EVA_COMMENT = $Hr_SecondBossComment;
                                   } else if ($LevelChi==0 or $LevelChi == 3) {
                                         $EVA_COMMENT = $Hr_EndBossComment;
                                   }  
                                   ?>
                                   <td class="uk-text-wrap uk-table-td-center" style="width:80%; text-align:left;">
                                   <?   
								   if ($ViewSW==1) {
								   ?>
                                       <textarea id="EVA_COMMENT" name="EVA_COMMENT" style="height:150px;width:95%; background:#F9F9F9;border:1px solid #cccccc;padding:10px;"><?=$EVA_COMMENT?></textarea>
								   <?
                                   } else {
								         echo $EVA_COMMENT;
                                   }
								   ?>
                                   </td>
                                   <td class="uk-text-wrap uk-table-td-center" width="20%">
                                       <input type="text" id="EVA_TOTAL" name="EVA_TOTAL" value="<?=$Hr_TotalPoint?>" readonly style="height:150px;width:60%; background:#fff;border:0px; color:#555; text-align:right; font-size:3.5em;">
                                       <span style="font-size:3.0em; color:#ddd">점</span>
                                   </td>
                                </tr>
                            </table>
                             <?
                             #------------------------------------------------------------------------------------------------------------#
                             }
                             #------------------------------------------------------------------------------------------------------------#
                             ?>
                        </div>
                    <?
                    #---------------------------------------------------------------------------------------------------------------------#
                    }
                    #---------------------------------------------------------------------------------------------------------------------#
                    ?>
                        <div class="uk-form-row" style="text-align:center; margin-top:20px;">
                    <?
                    #---------------------------------------------------------------------------------------------------------------------#
                    if ($ViewSW==1) {
                    #---------------------------------------------------------------------------------------------------------------------#
                    ?>
                            <a type="button" href="javascript:HREvaluationForm_Act(1,<?=$LevelChi?>,'<?=$LevelNone?>')" class="md-btn md-btn-primary" style="background:#CACACA; color:#5B5B5B;"><?=$반려[$LangID]?></a>
                            <a type="button" href="javascript:HREvaluationForm_Act(2,<?=$LevelChi?>,'<?=$LevelNone?>')" class="md-btn md-btn-primary"><?=$평가저장[$LangID]?></a>
                    <?
                    #---------------------------------------------------------------------------------------------------------------------#
                    }
                    #---------------------------------------------------------------------------------------------------------------------#
                    ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="KpiIndicatorID" name="KpiIndicatorID" value="<?=$KpiIndicatorID?>" style="width:10%;" />
        <input type="hidden" id="line_cnt"       name="line_cnt"       value="<?=$line_cnt?>" style="width:10%;" />
        </form>
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
//---------------------------------------------------------------------------------------------------------------------------//
// 숫자인지 체크
//---------------------------------------------------------------------------------------------------------------------------//
function IsNumberCalc(indata) {

      for(var i = 0; i < indata.length; i++) {
             var chr = indata.substr(i,1);
             if(chr < '0' || chr > '9') {
				 if (chr != '.') {
					   return false;
				 }
                  
             }
      }
      return true;

}
//---------------------------------------------------------------------------------------------------------------------------//
// 평가점수 환산 및 총점 구하기
//---------------------------------------------------------------------------------------------------------------------------//
function PointForm_Calc(vs,linecnt) {
      
      if (vs == 1) {         // 1차상상

              if ( !IsNumberCalc(document.getElementById("KPI_TARGET9_"+linecnt).value) ) {
                     alert("숫자만 입력 하세요!");
                     document.getElementById("KPI_TARGET9_"+linecnt).value = "";
              }

      } else if (vs == 2) {

              if ( !IsNumberCalc(document.getElementById("KPI_TARGET10_"+linecnt).value) ) {
                     alert("숫자만 입력 하세요!");
                     document.getElementById("KPI_TARGET10_"+linecnt).value = "";
              }

      } else if (vs == 3) {

              if ( !IsNumberCalc(document.getElementById("KPI_TARGET11_"+linecnt).value) ) {
                     alert("숫자만 입력 하세요!");
                     document.getElementById("KPI_TARGET11_"+linecnt).value = "";
              }

      }
      
      var linetot_cnt = document.getElementById("line_cnt").value;
      var eva_tot     = 0;
      for (var lc=1; lc <= linetot_cnt; lc++) {

            if ( !document.getElementById("KPI_TARGET8_"+lc).value ) {
                  document.getElementById("KPI_TARGET8_"+lc).value = 0;
            }
            if ( !document.getElementById("KPI_TARGET9_"+lc).value ) {
                  document.getElementById("KPI_TARGET9_"+lc).value = 0;
            }
            if ( !document.getElementById("KPI_TARGET10_"+lc).value ) {
                  document.getElementById("KPI_TARGET10_"+lc).value = 0;
            }
            if ( !document.getElementById("KPI_TARGET11_"+lc).value ) {
                  document.getElementById("KPI_TARGET11_"+lc).value = 0;
            }
             
            var self_point = parseFloat(document.getElementById("KPI_TARGET8_"+lc).value);    // 자기평가점수(환산점수)
            var cha_point1 = parseFloat(document.getElementById("KPI_TARGET9_"+lc).value);    // 1차상사 평가점수
            var cha_point2 = parseFloat(document.getElementById("KPI_TARGET10_"+lc).value);   // 2차상사 평가점수
            var cha_point3 = parseFloat(document.getElementById("KPI_TARGET11_"+lc).value);   // 최종상사 평가점수
            
		    if (vs == 1) {         // 1차상상
                    eva_tot = eva_tot + cha_point1; 
		    } else if (vs == 2) {
                    eva_tot = eva_tot + cha_point2; 
		    } else if (vs == 3) {
                    eva_tot = eva_tot + cha_point3; 
		    }

      }

      var eva_obj   = document.getElementById("EVA_TOTAL");      // 평가총점
      eva_obj.value = eva_tot;
      
      for (var lc=1; lc <= linetot_cnt; lc++) {

            if ( document.getElementById("KPI_TARGET9_"+lc).value == '0' ) {
                 document.getElementById("KPI_TARGET9_"+lc).value = "";
            }
            if ( document.getElementById("KPI_TARGET10_"+lc).value == '0' ) {
                 document.getElementById("KPI_TARGET10_"+lc).value = "";
            }
            if ( document.getElementById("KPI_TARGET11_"+lc).value == '0' ) {
                 document.getElementById("KPI_TARGET11_"+lc).value = "";
            }

      }

}
//-------------------------------------------------------------------------------------------------------------------------//
// 평가실행
//-------------------------------------------------------------------------------------------------------------------------//
function HREvaluationForm_Act(s,evachi,levelnone) {
              
       var nonelevel1 = "";
       var nonelevel2 = "";
       if (levelnone != "") {
             var levelnone_array = levelnone.split("/");                
             if (levelnone_array.length > 1) {
                   var nonelevel1 = levelnone_array[1];
                   var nonelevel2 = levelnone_array[0];
             } else {
                   if (levelnone=='N1') {
                         var nonelevel1 = levelnone;
                   } else {
                         var nonelevel2 = levelnone;
                   }
             }  
       } 
       //alert(nonelevel1+"/"+nonelevel2);

       if (evachi==1) { // 1차상사
              var evachi_na  = "<?=$P1차상사[$LangID]?>";
              var point_key  = 9;
              var retst_val  = 8;
              var savest_val = 10;
              if (nonelevel2=='N2') { // 2차상사가 없음
                   var savest_val = 11;
              } 
       } else if (evachi==2) { // 2차상사
              var evachi_na  = "<?=$P2차상사[$LangID]?>";
              var point_key  = 10;
              var retst_val  = 9;
              var savest_val = 11;
              if (nonelevel1=='N1') { // 1차상사가 없음
                   var retst_val  = 8;
              } 
       } else if (evachi==3 || evachi==0) { // 최종상사
              var evachi_na  = "<?=$최종상사[$LangID]?>";
              var point_key  = 11;
              var retst_val  = 10;
              var savest_val = 12;
              if (nonelevel2=='N2') { // 2차상사가 없음
                   var retst_val = 9;
              } 
              if (nonelevel1=='N1') { // 1차상사가 없음
                   var retst_val  = 8;
              } 
       }

       var line_cnt = document.RegForm.line_cnt.value;
       //-----------------------------------------------------------------------------------------------------------------//
       if (s == 1) {       // 반려
       //-----------------------------------------------------------------------------------------------------------------//
             UIkit.modal.confirm(
                 '<?=$제출된_평가를_반려_하시겠습니까[$LangID]?>', 
                 function(){ 

                       for (var lc=1; lc <= line_cnt; lc++) {
                            if ( !document.getElementById("KPI_TARGET9_"+lc).value ) {
                                  document.getElementById("KPI_TARGET9_"+lc).value = 0;
                            }
                            if ( !document.getElementById("KPI_TARGET10_"+lc).value ) {
                                  document.getElementById("KPI_TARGET10_"+lc).value = 0;
                            }
                            if ( !document.getElementById("KPI_TARGET11_"+lc).value ) {
                                  document.getElementById("KPI_TARGET11_"+lc).value = 0;
                            }
                       }
                       document.RegForm.action = "hr_staff_evaluation_action.php?EvaluationState=" + retst_val + "&LevelChi=" + evachi;
                       document.RegForm.submit();
                 }
             );  
       //-----------------------------------------------------------------------------------------------------------------//
       } else {            // 평가저장하기
       //-----------------------------------------------------------------------------------------------------------------//
             for(var i = 1; i <= line_cnt; i++) {

                  var kpi_targetname = document.getElementById("KPI_TARGET1_"+i).value;
                  var kpi_target = document.getElementById("KPI_TARGET"+point_key+"_"+i).value;
                  if (!kpi_target) {
                        alert(kpi_targetname + " <?=$평가점수를_입력_하세요[$LangID]?>");
                        document.getElementById("KPI_TARGET"+point_key+"_"+i).focus(); 
                        return;
                  }

             }
             if (!document.getElementById("EVA_COMMENT").value) {
                  alert(evachi_na + " <?=$평가_의견을_입력_하세요[$LangID]?>");
                  document.getElementById("EVA_COMMENT").focus(); 
                  return;
             }
             UIkit.modal.confirm(
                 '<?=$평가_제출_하시겠습니까[$LangID]?><br>※ <?=$한번_제출된_자료는_수정할_수_없습니다[$LangID]?> ※', 
                 function(){ 
                       document.RegForm.action = "hr_staff_evaluation_action.php?EvaluationState=" + savest_val + "&LevelChi=" + evachi + "&MyOrgan=" + <?=$MyOrgan?>;
                       document.RegForm.submit();
                 }
             );  
       //-----------------------------------------------------------------------------------------------------------------//
       }
       //-----------------------------------------------------------------------------------------------------------------//

}
</script>

</body>
</html>