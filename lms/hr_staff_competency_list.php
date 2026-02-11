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
include_once('./inc_common_list_css.php');
?>
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 88;
$SubMenuID = 8862;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

$MemberLoginID     = isset($_COOKIE["LoginMemberID"   ]) ? $_COOKIE["LoginMemberID"   ] : "";
$SearchState       = isset($_REQUEST["SearchState"    ]) ? $_REQUEST["SearchState"    ] : "";
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
?>


<div id="page_content">
    <div id="page_content_inner">

        <h3 class="heading_b uk-margin-bottom"><?=$My_MemberName?>[<?=$My_MemberID?>] <?=$역량평가실시[$LangID]?></h3>

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
                    <div id="KPI_Button" class="uk-width-medium-2-3" style="line-height:200%;">
                        <?=$역량평가를_실시하는_페이지_입니다[$LangID]?><br>
                        <?=$평가리스트검색에서_평가를_선택하신_후_나타나는_리스트에_해당하는_모든_사람에_대한_평가를_완료합니다[$LangID]?>
                    </div>
                </div>
            </div>
        </div>
        </form>


        <form name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
        <input type="hidden" id="SearchState" name="SearchState" value="<?=$SearchState?>" style="width:10%;" />
        <input type="hidden" id="My_MemberID" name="My_MemberID" value="<?=$My_MemberID?>" style="width:10%;" />
        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div id="kpi_listup" class="uk-overflow-container">
                    <?
                    #---------------------------------------------------------------------------------------------------------------------#
                    if ($SearchState) { 
                    #---------------------------------------------------------------------------------------------------------------------#
                            $ViweTable2 = "select AAAAA.* 
                                            from Hr_OrganLevelTaskMembers AAAAA 
                                            inner join Members BBBBB on AAAAA.MemberID=BBBBB.MemberID and BBBBB.MemberState=1";

                            $ViweTable = "select AAAA.* 
                                            from Hr_EvaluationCompetencyMembers AAAA 
                                            inner join Members BBBB on AAAA.MemberID=BBBB.MemberID and BBBB.MemberState=1";  

                            $Sql = "select 
                                        A.*,
                                        A_1.Hr_OrganLevel,
                                        A_1.Hr_OrganPositionName,
                                        A_1.Hr_OrganLevelID,
                                        A_1.Hr_OrganTask2ID,
                                        B.MemberName,

                                        ifnull(D.Hr_OrganTask2ID, '')       as Hr_OrganTask2ID,
                                        ifnull(E.Hr_OrganTask1ID, '')       as Hr_OrganTask1ID,
                                        ifnull(D.Hr_OrganTask2Name, '미지정') as Hr_OrganTask2Name,
                                        ifnull(E.Hr_OrganTask1Name, '미지정') as Hr_OrganTask1Name,

                                        ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel1ID), '') as Hr_OrganLevelName1, 
                                        ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel2ID), '') as Hr_OrganLevelName2,
                                        ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel3ID), '') as Hr_OrganLevelName3,
                                        ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel4ID), '') as Hr_OrganLevelName4,

                                        BB.MemberID as T_MemberID,
                                        AA_1.Hr_OrganLevel as T_Hr_OrganLevel,
                                        ifnull(AA_1.Hr_OrganPositionName, '미지정') as T_Hr_OrganPositionName,
                                        BB.MemberName as T_MemberName,

                                        ifnull(DD.Hr_OrganTask2Name,'미지정') as T_Hr_OrganTask2Name,
                                        ifnull(EE.Hr_OrganTask1Name,'미지정') as T_Hr_OrganTask1Name,

                                        ifnull((select Hr_OrganLevelID   from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel1ID), '') as Hr_OrganLevel1ID, 
                                        ifnull((select Hr_OrganLevelID   from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel2ID), '') as Hr_OrganLevel2ID,
                                        ifnull((select Hr_OrganLevelID   from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel3ID), '') as Hr_OrganLevel3ID,
                                        ifnull((select Hr_OrganLevelID   from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel4ID), '') as Hr_OrganLevel4ID,
                                        ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel1ID), '') as Hr_OrganLevelName1, 
                                        ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel2ID), '') as Hr_OrganLevelName2,
                                        ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel3ID), '') as Hr_OrganLevelName3,
                                        ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel4ID), '') as Hr_OrganLevelName4,

                                        (select count(*) from Members VVVV where VVVV.MemberID in (select VVVVV.Hr_EvaluationCompetencyMemberID from ($ViweTable) VVVVV where MemberID=A.MemberID ) ) as T_MemberCount,

                                        ( select count(*) from Hr_Staff_Compentency where MyMemberID=A.Hr_EvaluationCompetencyMemberID and MemberID=A.MemberID and Hr_EvaluationID=$SearchState) as PointCnt,
                                        ( select sum(Hr_CompetencyIndicatorPoint) from Hr_Staff_Compentency where MyMemberID=A.Hr_EvaluationCompetencyMemberID and MemberID=A.MemberID and Hr_EvaluationID=$SearchState) as PointTotal


                                    from ($ViweTable) A 

                                        inner join Members B on A.MemberID=B.MemberID
                                        left outer join ($ViweTable2) A_1 on A.MemberID=A_1.MemberID 
                                        left outer join Hr_OrganLevels C on A_1.Hr_OrganLevelID=C.Hr_OrganLevelID 
                                        left outer join Hr_OrganTask2 D on A_1.Hr_OrganTask2ID=D.Hr_OrganTask2ID 
                                        left outer join Hr_OrganTask1 E on D.Hr_OrganTask1ID=E.Hr_OrganTask1ID 

                                        inner join Members BB on A.Hr_EvaluationCompetencyMemberID=BB.MemberID and BB.MemberState=1 and BB.MemberID=".$My_MemberID."
                                        left outer join ($ViweTable2) AA_1 on BB.MemberID=AA_1.MemberID and AA_1.MemberID=".$My_MemberID." 
                                        left outer join Hr_OrganLevels CC on AA_1.Hr_OrganLevelID=CC.Hr_OrganLevelID 
                                        left outer join Hr_OrganTask2 DD on AA_1.Hr_OrganTask2ID=DD.Hr_OrganTask2ID 
                                        left outer join Hr_OrganTask1 EE on DD.Hr_OrganTask1ID=EE.Hr_OrganTask1ID
									where A.Hr_EvaluationID=" . $SearchState;

                            ?>
                            <table class="uk-table uk-table-align-vertical">
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="5%">NO</td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="10%"><?=$소속부서[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="10%"><?=$직무[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="10%"><?=$성명[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="10%"><?=$평가여부[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="10%"><?=$전체역량[$LangID]?><br><?=$평균점수[$LangID]?></td>
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
                                    //=================== 동 료 ======================
                                    $MemberID = $Row["MemberID"];

                                    $Hr_OrganLevel    = $Row["Hr_OrganLevel"];
                                    $Hr_OrganLevel1ID = $Row["Hr_OrganLevel1ID"];
                                    $Hr_OrganLevel2ID = $Row["Hr_OrganLevel2ID"];
                                    $Hr_OrganLevel3ID = $Row["Hr_OrganLevel3ID"];
                                    $Hr_OrganLevel4ID = $Row["Hr_OrganLevel4ID"];

                                    $MemberName = $Row["MemberName"];

                                    $Hr_OrganTask1ID    = $Row["Hr_OrganTask1ID"];
                                    $Hr_OrganTask2ID    = $Row["Hr_OrganTask2ID"];
                                    $Hr_OrganTask1Name  = $Row["Hr_OrganTask1Name"];
                                    $Hr_OrganTask2Name  = $Row["Hr_OrganTask2Name"];

                                    $Hr_OrganLevelName1 = $Row["Hr_OrganLevelName1"];
                                    $Hr_OrganLevelName2 = $Row["Hr_OrganLevelName2"];
                                    $Hr_OrganLevelName3 = $Row["Hr_OrganLevelName3"];
                                    $Hr_OrganLevelName4 = $Row["Hr_OrganLevelName4"];

                                    $Str_Hr_OrganLevelName = "";   //$Hr_OrganLevelName1;
                                    if ($Hr_OrganLevelName2!=""){
                                        $Str_Hr_OrganLevelName .= $Hr_OrganLevelName2;
                                    }
                                    if ($Hr_OrganLevelName3!=""){
                                        if ($Str_Hr_OrganLevelName) { 
                                              $Str_Hr_OrganLevelName .= " > ";
                                        }
                                        $Str_Hr_OrganLevelName .= $Hr_OrganLevelName3;
                                    }
                                    if ($Hr_OrganLevelName4!=""){
                                        if ($Str_Hr_OrganLevelName) { 
                                              $Str_Hr_OrganLevelName .= " > ";
                                        }
                                        $Str_Hr_OrganLevelName .= $Hr_OrganLevelName4;
                                    }

                                    $CPPoint_Cnt = $Row["PointCnt"];
                                    $CPPoint_Hap = $Row["PointTotal"];
									if ($CPPoint_Hap > 0 and $CPPoint_Cnt > 0) {
                                           $CPPoint_Avg = $CPPoint_Hap / $CPPoint_Cnt;
									} else {
                                           $CPPoint_Avg = 0;
									}
                                    //==================== 동 료 ========================

                                    //=================== 자기 자신 ======================
                                    $T_MemberID = $Row["T_MemberID"];

                                    $T_Hr_OrganLevel = $Row["T_Hr_OrganLevel"];
                                    $T_Hr_OrganPositionName = $Row["T_Hr_OrganPositionName"];

                                    $T_MemberName = $Row["T_MemberName"];
                                    //=================== 자기 자신 ======================
                                    
                                    //=================== 평가 여부 ======================
                                    $Sql2 = "select * from Hr_Staff_Compentency 
                                                    where MyMemberID=:MyMemberID and 
                                                          MemberID=:MemberID and 
                                                          Hr_EvaluationID=:Hr_EvaluationID and 
                                                          Hr_OrganTask1ID=:Hr_OrganTask1ID and
                                                          Hr_OrganTask2ID=:Hr_OrganTask2ID";
                                    $Stmt2 = $DbConn->prepare($Sql2);
                                    $Stmt2->bindParam(':MyMemberID',      $My_MemberID);
                                    $Stmt2->bindParam(':MemberID',        $MemberID);
                                    $Stmt2->bindParam(':Hr_EvaluationID', $SearchState);
                                    $Stmt2->bindParam(':Hr_OrganTask1ID', $Hr_OrganTask1ID);
                                    $Stmt2->bindParam(':Hr_OrganTask2ID', $Hr_OrganTask2ID);
                                    $Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
									$Row2 = $Stmt2->fetch();
									$CPState = $Row2["Hr_CompetencyIndicatorState"];
									$CPUseYN = $Row2["Hr_CompetencyIndicatorUseYN"];
									$CP_But  = "<a type='button' href=\"javascript:HRCompetency_ViewOpen(".$MemberID.",'".$MemberName."',".$Hr_OrganTask1ID.",".$Hr_OrganTask2ID.",'".$Hr_OrganTask1Name."','".$Hr_OrganTask2Name."')\" class='md-btn md-btn-primary' style='background:#1CC6EA;'>평가하기</a>";
									if ($CPState == 9 and $CPUseYN=='Y') {
									      $CP_But  = "<a type='button' href=\"javascript:HRCompetency_ViewOpen(".$MemberID.",'".$MemberName."',".$Hr_OrganTask1ID.",".$Hr_OrganTask2ID.",'".$Hr_OrganTask1Name."','".$Hr_OrganTask2Name."')\" class='md-btn md-btn-primary' style='background:#808080;'>평가완료</a>";
                                    }
                                    ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$line_cnt?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_OrganTask1Name?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_OrganTask2Name?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$MemberName?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$CP_But?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=number_format($CPPoint_Avg,2)?></td>
                                </tr>

                                    <?
                            #-------------------------------------------------------------------------------------------------------------#
                            }
                            #-------------------------------------------------------------------------------------------------------------#
                            $Stmt = null;
                            if (!$line_cnt) {
                            ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="text-align:center; padding:20px; color:#BCBCBC; font-size:1.5em;" colspan=10>
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
function HRCompetency_ViewOpen(MemberID,MemberName,OrganTask1ID,OrganTask2ID,OrganTask1Name,OrganTask2Name){

    var SearchState = document.RegForm.SearchState.value;
    var My_MemberID = document.RegForm.My_MemberID.value;

    openurl = "hr_staff_competency_form.php?SearchState=" + SearchState + "&My_MemberID=" + My_MemberID + "&MemberID="+MemberID + "&MemberName=" + MemberName + "&OrganTask1ID="+OrganTask1ID + "&OrganTask2ID="+OrganTask2ID + "&OrganTask1Name="+OrganTask1Name + "&OrganTask2Name="+OrganTask2Name;

    $.colorbox({    
        href:openurl
        ,width:"95%" 
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


function SearchSubmit(){
	document.SearchForm.action = "hr_staff_competency_list.php";
    document.SearchForm.submit();
}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>