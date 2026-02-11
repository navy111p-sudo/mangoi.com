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
#-----------------------------------------------------------------------------------------------------------------------------------------#
$SearchState    = isset($_REQUEST["SearchState"   ]) ? $_REQUEST["SearchState"   ] : "";
$My_MemberID    = isset($_REQUEST["My_MemberID"   ]) ? $_REQUEST["My_MemberID"   ] : "";
$MemberID       = isset($_REQUEST["MemberID"      ]) ? $_REQUEST["MemberID"      ] : "";
$MemberName     = isset($_REQUEST["MemberName"    ]) ? $_REQUEST["MemberName"    ] : "";
$OrganTask1ID   = isset($_REQUEST["OrganTask1ID"  ]) ? $_REQUEST["OrganTask1ID"  ] : "";
$OrganTask2ID   = isset($_REQUEST["OrganTask2ID"  ]) ? $_REQUEST["OrganTask2ID"  ] : "";
$OrganTask1Name = isset($_REQUEST["OrganTask1Name"]) ? $_REQUEST["OrganTask1Name"] : "";
$OrganTask2Name = isset($_REQUEST["OrganTask2Name"]) ? $_REQUEST["OrganTask2Name"] : "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 역량평가 대상 직무 찾기
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 직무별 역량
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "select count(*) as TotalRowCount
            from Hr_CompetencyIndicatorCate1 A 
                left outer join Hr_CompetencyIndicatorCate2 B on A.Hr_CompetencyIndicatorCate1ID=B.Hr_CompetencyIndicatorCate1ID 
                left outer join Hr_CompetencyIndicatorTasks T on T.Hr_CompetencyIndicatorCate2ID=B.Hr_CompetencyIndicatorCate2ID 
                left outer join Hr_CompetencyIndicators C     on C.Hr_CompetencyIndicatorCate2ID=B.Hr_CompetencyIndicatorCate2ID 
            where A.Hr_CompetencyIndicatorCate1State=1 and (B.Hr_CompetencyIndicatorCate2State=1 or B.Hr_CompetencyIndicatorCate2State is null) and
                  B.Hr_CompetencyIndicatorCate2ID > 0 and T.Hr_OrganTask2ID=".$OrganTask2ID;
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalRowCount = $Row["TotalRowCount"];
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 역량평가여부 알아보기
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql2 = "select count(*) as TotalRowCount 
                from Hr_Staff_Compentency 
				where MyMemberID=:MyMemberID and 
					  MemberID=:MemberID and 
					  Hr_EvaluationID=:Hr_EvaluationID and 
					  Hr_OrganTask1ID=:Hr_OrganTask1ID and
					  Hr_OrganTask2ID=:Hr_OrganTask2ID";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':MyMemberID',      $My_MemberID);
$Stmt2->bindParam(':MemberID',        $MemberID);
$Stmt2->bindParam(':Hr_EvaluationID', $SearchState);
$Stmt2->bindParam(':Hr_OrganTask1ID', $OrganTask1ID);
$Stmt2->bindParam(':Hr_OrganTask2ID', $OrganTask2ID);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
$Row2 = $Stmt2->fetch();
$Stmt2 = null;
$TotalRowCount2 = $Row2["TotalRowCount"];
?>


<div id="page_content">
    <div id="page_content_inner">
        <h3 class="heading_b uk-margin-bottom">[<?=$MemberName?>] <span style="font-size:0.6em;">(<?=$OrganTask1Name?> > <?=$OrganTask2Name?>)</span> <?=$역량평가[$LangID]?></h3>

        <form name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
        <input type="hidden" name="SearchState"  value="<?=$SearchState?>" />
        <input type="hidden" name="My_MemberID"  value="<?=$My_MemberID?>" />
        <input type="hidden" name="MemberID"     value="<?=$MemberID?>" />
		<input type="hidden" name="MemberName"   value="<?=$MemberName?>" />
        <input type="hidden" name="OrganTask1ID" value="<?=$OrganTask1ID?>" />
        <input type="hidden" name="OrganTask2ID" value="<?=$OrganTask2ID?>" />
        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div class="uk-overflow-container">
                    <?
                    $line_cnt = 0;
                    #---------------------------------------------------------------------------------------------------------------------#
                    if ($SearchState and $TotalRowCount > 0) { 
                    #---------------------------------------------------------------------------------------------------------------------#
                    ?>
                            <table class="uk-table uk-table-align-vertical">
                                <thead>
                                    <tr>
                                        <th nowrap style="width:5%;">No</th>
                                        <th nowrap style="width:60%;"><?=$문항[$LangID]?></th>
                                        <th nowrap style="width:6%;"><?=$P5점[$LangID]?></th>
                                        <th nowrap style="width:6%;"><?=$P4점[$LangID]?></th>
                                        <th nowrap style="width:6%;"><?=$P3점[$LangID]?></th>
                                        <th nowrap style="width:6%;"><?=$P2점[$LangID]?></th>
                                        <th nowrap style="width:6%;"><?=$P1점[$LangID]?></th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?
                            #-------------------------------------------------------------------------------------------------------------#
                            $Sql = "SELECT 
                                        A.Hr_CompetencyIndicatorCate1ID,
                                        A.Hr_CompetencyIndicatorCate1Name,
                                        ifnull(B.Hr_CompetencyIndicatorCate2ID, 0) as Hr_CompetencyIndicatorCate2ID,
                                        B.Hr_CompetencyIndicatorCate2Name,
                                        ( select count(*) from Hr_CompetencyIndicatorCate2 
                                                         where Hr_CompetencyIndicatorCate1ID=A.Hr_CompetencyIndicatorCate1ID and 
                                                              (Hr_CompetencyIndicatorCate2State=1 or Hr_CompetencyIndicatorCate2State is null) ) as Hr_CompetencyIndicatorCate2Count,
                                        C.Hr_CompetencyIndicatorID,
                                        C.Hr_CompetencyIndicatorName,

										CP.Hr_CompetencyIndicatorPoint as CPPoint,
										CP.Hr_CompetencyIndicatorComment as CPComment,
										CP.Hr_CompetencyIndicatorState as CPState,
										CP.Hr_CompetencyIndicatorUseYN as CPUseYN

                                    from Hr_CompetencyIndicatorCate1 A 

                                        left outer join Hr_CompetencyIndicatorCate2 B on A.Hr_CompetencyIndicatorCate1ID=B.Hr_CompetencyIndicatorCate1ID 
                                        left outer join Hr_CompetencyIndicatorTasks T on T.Hr_CompetencyIndicatorCate2ID=B.Hr_CompetencyIndicatorCate2ID 
                                        left outer join Hr_CompetencyIndicators C     on C.Hr_CompetencyIndicatorCate2ID=B.Hr_CompetencyIndicatorCate2ID                                         
										left outer join Hr_Staff_Compentency CP       on CP.Hr_CompetencyIndicatorID=C.Hr_CompetencyIndicatorID  
                                                                                         and    CP.MyMemberID=".$My_MemberID." 
                                                                                         and    CP.MemberID=".$MemberID." 
                                                                                         and    CP.Hr_EvaluationID=".$SearchState."  
                                    
                                    where A.Hr_CompetencyIndicatorCate1State=1 and (B.Hr_CompetencyIndicatorCate2State=1 or B.Hr_CompetencyIndicatorCate2State is null) and
                                          B.Hr_CompetencyIndicatorCate2ID > 0 and T.Hr_OrganTask2ID=".$OrganTask2ID."
                                    order by A.Hr_CompetencyIndicatorCate1Order asc, B.Hr_CompetencyIndicatorCate2Order asc";

                            // 원래 코드에 있던 부분을 삭제 . 잘못된 sql 이라 판단됨.
                            /*
                                                                                         and    CP.Hr_OrganTask1ID=".$OrganTask1ID."  
                                                                                         and    CP.Hr_OrganTask2ID=".$OrganTask2ID." 
        
                             */
                            $Stmt = $DbConn->prepare($Sql);
                            $Stmt->execute();
                            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                            $line_cnt = 0;
							$COMP_COMMENT = "";
							$COMP_State   = "";
                            $COMP_UseYN   = "N";
							$CP_Readonly  = "";
							$CP_Disable   = "";
							$CP_BKColor   = "#F9F9F9";
							$CP_Boarder   = 1;
							$CP_Height    = 150;

							$CPPoint_Hap  = 0;
                            #-------------------------------------------------------------------------------------------------------------#
                            while($Row = $Stmt->fetch()) {
                            #-------------------------------------------------------------------------------------------------------------#
                                    $line_cnt ++;
                                    /* 
                                    $Hr_CompetencyIndicatorCate1ID   = $Row["Hr_CompetencyIndicatorCate1ID"];
                                    $Hr_CompetencyIndicatorCate1Name = $Row["Hr_CompetencyIndicatorCate1Name"];
                                    $Hr_CompetencyIndicatorCate2ID   = $Row["Hr_CompetencyIndicatorCate2ID"];
                                    $Hr_CompetencyIndicatorCate2Name = $Row["Hr_CompetencyIndicatorCate2Name"];

                                    $Hr_CompetencyIndicatorCate2Count = $Row["Hr_CompetencyIndicatorCate2Count"];
                                    if ($Hr_CompetencyIndicatorCate2Count==0){
                                          $Hr_CompetencyIndicatorCate2Count = 1;
                                    }
									*/
                                    $Hr_CompetencyIndicatorID   = $Row["Hr_CompetencyIndicatorID"];
                                    $Hr_CompetencyIndicatorName = $Row["Hr_CompetencyIndicatorName"];

                                    $CPCHK5 = "";
                                    $CPCHK4 = "";
                                    $CPCHK3 = "";
                                    $CPCHK2 = "";
                                    $CPCHK1 = "";
									$CPPoint = $Row["CPPoint"];
                                    if ($CPPoint==5) {
									       $CPCHK5 = "checked";
									} else if ($CPPoint==4) {
									       $CPCHK4 = "checked";
									} else if ($CPPoint==3) {
									       $CPCHK3 = "checked";
									} else if ($CPPoint==2) {
									       $CPCHK2 = "checked";
									} else if ($CPPoint==1) {
									       $CPCHK1 = "checked";
									}
									$CPPoint_Hap  = $CPPoint_Hap + $CPPoint;

									$COMP_COMMENT = $Row["CPComment"];
							        $COMP_State   = $Row["CPState"];
                                    $COMP_UseYN   = $Row["CPUseYN"];
									if ($COMP_State==9 and $COMP_UseYN=='Y') {
										$CP_Readonly = "readonly";
										$CP_Disable  = "disabled";
										$CP_BKColor  = "#fff";
										$CP_Boarder  = 0;
										$CP_Height   = 100;
                                    } 
									?>
                                    <tr>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$line_cnt?></td>
                                        <td class="uk-text-nowrap uk-table-td-center" style="text-align:left;"><?=$Hr_CompetencyIndicatorName?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><input type="radio" id="CPoint1_<?=$line_cnt?>" name="CPoint_<?=$line_cnt?>" <?=$CPCHK5?> <?=$CP_Disable?> value="5" style="width:20px; height:20px;"></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><input type="radio" id="CPoint2_<?=$line_cnt?>" name="CPoint_<?=$line_cnt?>" <?=$CPCHK4?> <?=$CP_Disable?> value="4" style="width:20px; height:20px;"></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><input type="radio" id="CPoint3_<?=$line_cnt?>" name="CPoint_<?=$line_cnt?>" <?=$CPCHK3?> <?=$CP_Disable?> value="3" style="width:20px; height:20px;"></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><input type="radio" id="CPoint4_<?=$line_cnt?>" name="CPoint_<?=$line_cnt?>" <?=$CPCHK2?> <?=$CP_Disable?> value="2" style="width:20px; height:20px;"></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><input type="radio" id="CPoint5_<?=$line_cnt?>" name="CPoint_<?=$line_cnt?>" <?=$CPCHK1?> <?=$CP_Disable?> value="1" style="width:20px; height:20px;"></td>
                                    </tr>
                                    <input type="hidden" name="CompetencyIndicatorID_<?=$line_cnt?>" value="<?=$Hr_CompetencyIndicatorID?>" />
                                    <?
                            #-------------------------------------------------------------------------------------------------------------#
                            }
                            #-------------------------------------------------------------------------------------------------------------#
							if ($COMP_State==9 and $COMP_UseYN=='Y') {
                                    $CPPoint_Avg = $CPPoint_Hap / $line_cnt;
							        ?>
                                    <tr>
                                        <td class="uk-text-nowrap uk-table-td-center" colspan="2"><?=$평균[$LangID]?></td>
                                        <td class="uk-text-nowrap uk-table-td-center" style="text-align:center;" colspan="5"><?=number_format($CPPoint_Avg,2)?></td>
                                    </tr>
                            <?
							} 
                            $Stmt = null;
							?>
                                </tbody>
                            </table>
                            <div class="uk-form-row" style="text-align:left; padding:10px; color:#BCBCBC;">
                               ▷ <?=$최종평가의견을_입력_하세요[$LangID]?><br>
                               <textarea id="COMP_COMMENT" name="COMP_COMMENT" <?=$CP_Readonly?> style="height:<?=$CP_Height?>px;width:99%; background:<?=$CP_BKColor?>; border:<?=$CP_Boarder?>px solid #cccccc;padding:10px;"><?=$COMP_COMMENT?></textarea>
                            </div>
							<?
                            if ($COMP_State==9) {
							?>
                            <div class="uk-form-row" style="text-align:center; padding:20px; color:#BCBCBC; font-size:1.5em;">
                                 [<?=$MemberName?>] <?=$평가완료_되었습니다[$LangID]?> &nbsp; &nbsp; 
                            </div>
							<?
							}
							?>
                            <div class="uk-form-row" style="text-align:center; padding:20px; color:#BCBCBC; font-size:1.5em;">
                                 <a type="button" href="javascript:Competency_Clear()" class="md-btn md-btn-primary"><?=$평가삭제[$LangID]?></a>
                            </div>
                    <?
                    #---------------------------------------------------------------------------------------------------------------------#
                    } else {
                    #---------------------------------------------------------------------------------------------------------------------#
                    ?>
                            <div class="uk-form-row" style="text-align:center; padding:30px; color:#BCBCBC; font-size:1.5em;">
                                <?=$해당직무로_등록된_역량_평가서가_없습니다[$LangID]?>
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
        <input type="hidden" id="line_cnt" name="line_cnt" value="<?=$line_cnt?>" style="width:10%;" />
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
// 평가실행
//-------------------------------------------------------------------------------------------------------------------------//
function Competency_Clear() {
              
      UIkit.modal.confirm(
            '<?=$역량평가를_초기화_하시겠습니까[$LangID]?><br><?=$한번_삭제된_자료는_복구할_수_없습니다[$LangID]?>', 
           function(){ 
                 document.RegForm.action = "hr_staffall_evaluation_competency_clearaction.php";
                 document.RegForm.submit();
           }

      );  

}
</script>

</body>
</html>