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
$SubMenuID  = 8834;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

$DownMenu    = isset($_REQUEST["DownMenu"   ]) ? $_REQUEST["DownMenu"   ] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
?>
<div id="page_content">
    <div id="page_content_inner">

        <h3 class="heading_b uk-margin-bottom">DB 다운로드</h3>

        <form name="SearchForm" method="post" ENCTYPE="multipart/form-data">
        <div class="md-card" style="margin-bottom:10px;">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin="">

                    <div class="uk-width-medium-3-10">
                        <div class="uk-margin-small-top">
                            <select id="DownMenu" name="DownMenu" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
                                <option value=""><?=$선택[$LangID]?></option>
                                <option value="1" <? if ($DownMenu==1){?> selected <?}?>>성과평가 최종결과</option>
                                <option value="2" <? if ($DownMenu==2){?> selected <?}?>>원데이터(5점만점)</option>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-medium-2-10">
                        <div class="uk-margin-small-top">
                            <select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
                                <option value=""><?=$선택[$LangID]?></option>
                                    <?php
                                    $Hr_EvaluationID       = "";
                                    $Hr_EvaluationTypeID   = "";
                                    $Hr_EvaluationTypeName = "";

                                    $AddSqlWhere = "1=1";
                                    $AddSqlWhere = $AddSqlWhere . " and A.Hr_EvaluationState=1";

                                    $Sql = "SELECT 
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
                                        <option value="<?=$Hr_EvaluationID?>" <? if ($Hr_EvaluationID==$SearchState){?> selected <?}?>><?=$Str_Hr_EvaluationYear?></option>
                                        <?php
                                        $ListCount ++;
                                    }
                                    $Stmt = null;
                                    ?>

                            </select> 
                        </div>
                    </div>
                    <div class="uk-width-medium-3-10 uk-text-center">
                        <a href="javascript:ExcelDown();" class="md-btn md-btn-primary uk-margin-small-top">EXCEL DOWN</a>
                    </div>
                </div>
            </div>
        </div>
        </form>

        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div class="uk-overflow-container">

                    <?
                    #=====================================================================================================================#
                    # 성과평가결과
                    #=====================================================================================================================#
                    if ($DownMenu==1 and $SearchState) { 
                    #=====================================================================================================================#
                           ?>       
                            <h4>▷ 성과평가결과</h4>
                            <table class="uk-table uk-table-align-vertical">
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="20%"><?=$성명[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="20%"><?=$업적평가점수[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="20%"><?=$역량평가점수[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="20%"><?=$성과평가종합점수[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="20%"><?=$성과평가_종합평가_등급[$LangID]?></td>
                                </tr>
                                <tbody>
                            <?
                            #-------------------------------------------------------------------------------------------------------------#
                            $Sql = "select count(*) TotalRowCount
                                             from Hr_Staff_ResultEvaluation 
                                            where Hr_EvaluationID=".$SearchState." ";
                            $Stmt = $DbConn->prepare($Sql);
                            $Stmt->execute();
                            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                            $Row = $Stmt->fetch();
                            $Stmt = null;
                            $TotalRowCount = $Row["TotalRowCount"];
                            #-------------------------------------------------------------------------------------------------------------#
                            $Sql = "select SR.*,MM.MemberName from Hr_Staff_ResultEvaluation as SR
                                       inner join Members MM on MM.MemberID=SR.MemberID
                                            where SR.Hr_EvaluationID=".$SearchState." ";
                            $Stmt = $DbConn->prepare($Sql);
                            $Stmt->execute();
                            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                            #-------------------------------------------------------------------------------------------------------------#
                            while ($Row = $Stmt->fetch())  {
                            #-------------------------------------------------------------------------------------------------------------#
                                   ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["MemberName"]?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_EndEvaluationPoint"]?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_EvaluationCompetencyEndPoint"]?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_ResultTotalPoint"]?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_ResultLevel"]?></td>
                                </tr>
                                   <?
                            #-------------------------------------------------------------------------------------------------------------#
                            }
                            #-------------------------------------------------------------------------------------------------------------#
                            $Stmt = null;
                            #-------------------------------------------------------------------------------------------------------------#
                            if (!$TotalRowCount) {
                            #-------------------------------------------------------------------------------------------------------------#
                                    ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" colspan="5"><?=$성과결과_자료가_없습니다[$LangID]?></td>
                                </tr>
                                    <?
                            #-------------------------------------------------------------------------------------------------------------#
                            }
                            #-------------------------------------------------------------------------------------------------------------#
                            ?>
                                </tbody>
                            </table>
                            <?
                    #=====================================================================================================================#
                    # 원데이터(5점만점)
                    #=====================================================================================================================#
                    } else if ($DownMenu==2 and $SearchState) { 
                    #=====================================================================================================================#
                            $Sql  = "select count(*) TotalRowCount from Hr_CompetencyIndicatorCate1 A  where A.Hr_CompetencyIndicatorCate1State=1";
                            $Stmt = $DbConn->prepare($Sql);
                            $Stmt->execute();
                            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                            $Row = $Stmt->fetch();
                            $Stmt = null;

                            $TotalRowCount = $Row["TotalRowCount"];
                            ?>
                            <h4 id="result2_dis">▷ 원데이터(5점만점) <?=$SearchState?> - <b style="color:#d80000;">집계하는데 시간이 걸립니다. 잠시만 기다려 주세요....!</b></h4>
                            <table class="uk-table uk-table-align-vertical">
                                <thead>
                                    <tr>
                                        <th nowrap colspan="4" style="border-bottom:0px;"><?=$평가대상자[$LangID]?></th>
                                        <th nowrap colspan="5" style="border-bottom:0px;"><?=$평가자[$LangID]?></th>
                                        <?
                                        if ($TotalRowCount > 1) {

                                                $Sql = "select A.* from Hr_CompetencyIndicatorCate1 A 
                                                                  where A.Hr_CompetencyIndicatorCate1State=1  
                                                               order by A.Hr_CompetencyIndicatorCate1Order asc"; 
                                                $Stmt = $DbConn->prepare($Sql);
                                                $Stmt->execute();
                                                $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                                                while($Row = $Stmt->fetch()) {
                                                    $Hr_CompetencyIndicatorCate1ID    = $Row["Hr_CompetencyIndicatorCate1ID"];
                                                    $Hr_CompetencyIndicatorCate1Name  = $Row["Hr_CompetencyIndicatorCate1Name"];

                                                    $Sql2 = "select A.* from Hr_CompetencyIndicatorCate2 A 
                                                                        where A.Hr_CompetencyIndicatorCate1ID=:Hr_CompetencyIndicatorCate1ID
                                                                        order by A.Hr_CompetencyIndicatorCate2Order asc";
                                                    $Stmt2 = $DbConn->prepare($Sql2);
                                                    $Stmt2->bindParam(':Hr_CompetencyIndicatorCate1ID', $Hr_CompetencyIndicatorCate1ID);
                                                    $Stmt2->execute();
                                                    $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                                                    $TotalRowCount2 = 0;
                                                    while($Row2 = $Stmt2->fetch()) {
                                                          $Hr_CompetencyIndicatorCate2ID   = $Row2["Hr_CompetencyIndicatorCate2ID"];

                                                          $Sql3  = "select count(*) TotalRowCount3 from Hr_CompetencyIndicators A 
                                                                                                  where A.Hr_CompetencyIndicatorCate2ID=:Hr_CompetencyIndicatorCate2ID "; 
                                                          $Stmt3 = $DbConn->prepare($Sql3);
                                                          $Stmt3->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
                                                          $Stmt3->execute();
                                                          $Stmt3->setFetchMode(PDO::FETCH_ASSOC);
                                                          $Row3  = $Stmt3->fetch();
                                                          $Stmt3 = null;
                                                          $TotalRowCount2 = $TotalRowCount2 + $Row3["TotalRowCount3"] + 1;
                                                                 
                                                    }
                                                    ?> 
                                        <th nowrap colspan="<?=$TotalRowCount2?>"><?=$Hr_CompetencyIndicatorCate1Name?></th>
                                                    <?
                                                } 
                                                    
                                        } else {

                                                ?>
                                        <th nowrap style="border-bottom:0px;"><?=$비고[$LangID]?></th>
                                                <?

                                        }
                                        ?>
                                        <th rowspan=3>전체역량평균</th>
                                    </tr>
                                    <tr>
                                        <th rowspan=2 valign="middle"><?=$번호[$LangID]?></th>
                                        <th rowspan=2 valign="middle"><?=$성명[$LangID]?></th>
                                        <th rowspan=2 valign="middle"><?=$직급_직책[$LangID]?></th>
                                        <th rowspan=2 valign="middle"><?=$직무[$LangID]?></th>
                                        <th rowspan=2 valign="middle"><?=$성명[$LangID]?></th>
                                        <th rowspan=2 valign="middle"><?=$유형[$LangID]?></th>
                                        <th rowspan=2 valign="middle"><?=$직무[$LangID]?></th>
                                        <th rowspan=2 valign="middle"><?=$가중치[$LangID]?>(%)</th>
                                        <th rowspan=2 valign="middle"><?=$가중치반영점수[$LangID]?></th>
                                        <?
                                        $Sql = "select A.* from Hr_CompetencyIndicatorCate1 A 
                                                          where A.Hr_CompetencyIndicatorCate1State=1  
                                                       order by A.Hr_CompetencyIndicatorCate1Order asc"; 
                                        $Stmt = $DbConn->prepare($Sql);
                                        $Stmt->execute();
                                        $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                                        while($Row = $Stmt->fetch()) {

                                            $Hr_CompetencyIndicatorCate1ID    = $Row["Hr_CompetencyIndicatorCate1ID"];
                                            
                                            $Sql2 = "select A.* from Hr_CompetencyIndicatorCate2 A 
                                                                where A.Hr_CompetencyIndicatorCate1ID=:Hr_CompetencyIndicatorCate1ID
                                                                order by A.Hr_CompetencyIndicatorCate2Order asc";
                                            $Stmt2 = $DbConn->prepare($Sql2);
                                            $Stmt2->bindParam(':Hr_CompetencyIndicatorCate1ID', $Hr_CompetencyIndicatorCate1ID);
                                            $Stmt2->execute();
                                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                                            while($Row2 = $Stmt2->fetch()) {
                                                  $Hr_CompetencyIndicatorCate2ID   = $Row2["Hr_CompetencyIndicatorCate2ID"];
                                                  $Hr_CompetencyIndicatorCate2Name = $Row2["Hr_CompetencyIndicatorCate2Name"];

                                                  $Sql3  = "select count(*) TotalRowCount3 from Hr_CompetencyIndicators A 
                                                                                          where A.Hr_CompetencyIndicatorCate2ID=:Hr_CompetencyIndicatorCate2ID "; 
                                                  $Stmt3 = $DbConn->prepare($Sql3);
                                                  $Stmt3->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
                                                  $Stmt3->execute();
                                                  $Stmt3->setFetchMode(PDO::FETCH_ASSOC);
                                                  $Row3  = $Stmt3->fetch();
                                                  $Stmt3 = null;
                                                  $TotalRowCount3 = $Row3["TotalRowCount3"] + 1;
                                                  ?> 
                                        <th nowrap colspan="<?=$TotalRowCount3?>"><?=$Hr_CompetencyIndicatorCate2Name?></th>
                                                  <?
                                            }
                                        } 
                                        ?>
                                    </tr>
                                    <tr>
                                        <?
                                        $Snno = 0;

                                        $Sql = "select A.* from Hr_CompetencyIndicatorCate1 A 
                                                          where A.Hr_CompetencyIndicatorCate1State=1  
                                                       order by A.Hr_CompetencyIndicatorCate1Order asc"; 
                                        $Stmt = $DbConn->prepare($Sql);
                                        $Stmt->execute();
                                        $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                                        while($Row = $Stmt->fetch()) {

                                            $Hr_CompetencyIndicatorCate1ID    = $Row["Hr_CompetencyIndicatorCate1ID"];
                                            
                                            $Sql2 = "select A.* from Hr_CompetencyIndicatorCate2 A 
                                                                where A.Hr_CompetencyIndicatorCate1ID=:Hr_CompetencyIndicatorCate1ID
                                                                order by A.Hr_CompetencyIndicatorCate2Order asc";
                                            $Stmt2 = $DbConn->prepare($Sql2);
                                            $Stmt2->bindParam(':Hr_CompetencyIndicatorCate1ID', $Hr_CompetencyIndicatorCate1ID);
                                            $Stmt2->execute();
                                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                                            while($Row2 = $Stmt2->fetch()) {

                                                  $Hr_CompetencyIndicatorCate2ID   = $Row2["Hr_CompetencyIndicatorCate2ID"];
                                                  $Hr_CompetencyIndicatorCate2Name = $Row2["Hr_CompetencyIndicatorCate2Name"];

                                                  $Sql3  = "select A.* from Hr_CompetencyIndicators A 
                                                                      where A.Hr_CompetencyIndicatorCate2ID=:Hr_CompetencyIndicatorCate2ID
                                                                      order by A.Hr_CompetencyIndicatorOrder asc"; 
                                                  $Stmt3 = $DbConn->prepare($Sql3);
                                                  $Stmt3->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
                                                  $Stmt3->execute();
                                                  while($Row3 = $Stmt3->fetch()) {
                                                       $Snno++;         
                                                       $Hr_CompetencyIndicatorID3 = $Row3["Hr_CompetencyIndicatorID"];
                                                       ?>
                                        <th nowrap><?=$Snno?></th>
                                                       <? 
                                                  }
                                                  ?>
                                        <th nowrap>평균</th>
                                                  <?
                                            }
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                            <?
                            #-------------------------------------------------------------------------------------------------------------#
                            $ViweTable2 = "select AAAAA.* from Hr_OrganLevelTaskMembers AAAAA 
                                                        inner join Members BBBBB on AAAAA.MemberID=BBBBB.MemberID and BBBBB.MemberState=1";
                            $ViweTable = "select AAAA.* from Hr_EvaluationCompetencyMembers AAAA 
                                                        inner join Members BBBB on AAAA.MemberID=BBBB.MemberID and BBBB.MemberState=1";
                            $Sql = "select count(*) TotalRowCount from ($ViweTable) A 
                                                            inner join Members B on A.MemberID=B.MemberID 
                                                            left outer join ($ViweTable2) A_1 on A.MemberID=A_1.MemberID 
                                                            left outer join Hr_OrganLevels C on A_1.Hr_OrganLevelID=C.Hr_OrganLevelID 
                                                            left outer join Hr_OrganTask2 D on A_1.Hr_OrganTask2ID=D.Hr_OrganTask2ID 
                                                            left outer join Hr_OrganTask1 E on D.Hr_OrganTask1ID=E.Hr_OrganTask1ID 

                                                            left outer join ($ViweTable) AA on A.Hr_EvaluationCompetencyMemberID=AA.MemberID 
                                                            left outer join Members BB on AA.MemberID=BB.MemberID and BB.MemberState=1 
                                                            left outer join ($ViweTable2) AA_1 on AA.MemberID=AA_1.MemberID 
                                                            left outer join Hr_OrganLevels CC on AA_1.Hr_OrganLevelID=CC.Hr_OrganLevelID 
                                                            left outer join Hr_OrganTask2 DD on AA_1.Hr_OrganTask2ID=DD.Hr_OrganTask2ID 
                                                            left outer join Hr_OrganTask1 EE on DD.Hr_OrganTask1ID=EE.Hr_OrganTask1ID 
                                                                      where A.Hr_EvaluationID=".$SearchState;
                            $Stmt = $DbConn->prepare($Sql);
                            $Stmt->execute();
                            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                            $Row = $Stmt->fetch();
                            $Stmt = null;

                            $TotalRowCount = $Row["TotalRowCount"];

                            $Sql = "select  A.*,
                                            A_1.Hr_OrganLevel,
                                            A_1.Hr_OrganPositionName,
                                            A_1.Hr_OrganLevelID,
                                            A_1.Hr_OrganTask2ID,
                                            B.MemberName,

                                            ifnull(D.Hr_OrganTask2Name, '미지정') as Hr_OrganTask2Name,
                                            ifnull(E.Hr_OrganTask1ID,   ''    ) as Hr_OrganTask1ID,
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
                                            ifnull(DD.Hr_OrganTask2ID,  ''    ) as T_Hr_OrganTask2ID,
                                            ifnull(EE.Hr_OrganTask1ID,  ''    ) as T_Hr_OrganTask1ID,

                                            ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel1ID), '') as T_Hr_OrganLevelName1, 
                                            ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel2ID), '') as T_Hr_OrganLevelName2,
                                            ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel3ID), '') as T_Hr_OrganLevelName3,
                                            ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel4ID), '') as T_Hr_OrganLevelName4,

                                            (select count(*) from Members VVVV where VVVV.MemberID in (select VVVVV.Hr_EvaluationCompetencyMemberID from ($ViweTable) VVVVV where MemberID=A.MemberID ) ) as TM_MemberCount,

                                            ( (select count(*) from ($ViweTable) VVVVV where VVVVV.MemberID=A.MemberID and VVVVV.Hr_EvaluationID=".$SearchState." ) ) as T_MemberCount

                                        from ($ViweTable) A 

                                            inner join Members B on A.MemberID=B.MemberID 
                                            left outer join ($ViweTable2) A_1 on A.MemberID=A_1.MemberID 
                                            left outer join Hr_OrganLevels C on A_1.Hr_OrganLevelID=C.Hr_OrganLevelID 
                                            left outer join Hr_OrganTask2 D on A_1.Hr_OrganTask2ID=D.Hr_OrganTask2ID 
                                            left outer join Hr_OrganTask1 E on D.Hr_OrganTask1ID=E.Hr_OrganTask1ID 

                                            left outer join Members BB on A.Hr_EvaluationCompetencyMemberID=BB.MemberID and BB.MemberState=1 
                                            left outer join ($ViweTable2) AA_1 on BB.MemberID=AA_1.MemberID 
                                            left outer join Hr_OrganLevels CC on AA_1.Hr_OrganLevelID=CC.Hr_OrganLevelID 
                                            left outer join Hr_OrganTask2 DD on AA_1.Hr_OrganTask2ID=DD.Hr_OrganTask2ID 
                                            left outer join Hr_OrganTask1 EE on DD.Hr_OrganTask1ID=EE.Hr_OrganTask1ID 

                                        where A.Hr_EvaluationID=".$SearchState." 
                                        order by A.MemberID asc, 
                                            A_1.Hr_OrganLevel asc, A.Hr_EvaluationCompetencyMemberType desc, C.Hr_OrganLevel1ID asc, C.Hr_OrganLevel2ID asc, C.Hr_OrganLevel3ID asc, C.Hr_OrganLevel4ID asc, AA_1.Hr_OrganLevel asc";
                                $Stmt = $DbConn->prepare($Sql);
                                $Stmt->execute();
                                $Stmt->setFetchMode(PDO::FETCH_ASSOC);

                                $ListCount   = 0;
                                $ListCount2  = 0;
                                $OldMemberID = 0;
                                $H_AddValue  = 0;
                                $H_AddTotalPoint = 0;

                                while($Row = $Stmt->fetch()) {
                                        
                                        $ListCount ++;

                                        //=================== 자기 자신 ======================
                                        $Hr_EvaluationCompetencyMemberType = $Row["Hr_EvaluationCompetencyMemberType"];
                                        
                                        $MemberID = $Row["MemberID"];

                                        $Hr_OrganLevel = $Row["Hr_OrganLevel"];
                                        $Hr_OrganLevelID = $Row["Hr_OrganLevelID"];
                                        $Hr_OrganTask1ID = $Row["Hr_OrganTask1ID"];
                                        $Hr_OrganTask2ID = $Row["Hr_OrganTask2ID"];
                                        $Hr_OrganPositionName = $Row["Hr_OrganPositionName"];

                                        $MemberName = $Row["MemberName"];

                                        $Hr_OrganTask2Name = $Row["Hr_OrganTask2Name"];
                                        $Hr_OrganTask1Name = $Row["Hr_OrganTask1Name"];


                                        $Hr_OrganLevelName1 = $Row["Hr_OrganLevelName1"];
                                        $Hr_OrganLevelName2 = $Row["Hr_OrganLevelName2"];
                                        $Hr_OrganLevelName3 = $Row["Hr_OrganLevelName3"];
                                        $Hr_OrganLevelName4 = $Row["Hr_OrganLevelName4"];

                                        $Str_Hr_OrganLevelName = $Hr_OrganLevelName1;
                                        if ($Hr_OrganLevelName2!=""){
                                            $Str_Hr_OrganLevelName .= " > " . $Hr_OrganLevelName2;
                                        }
                                        if ($Hr_OrganLevelName3!=""){
                                            $Str_Hr_OrganLevelName .= " > " . $Hr_OrganLevelName3;
                                        }
                                        if ($Hr_OrganLevelName4!=""){
                                            $Str_Hr_OrganLevelName .= " > " . $Hr_OrganLevelName4;
                                        }

                                        $Str_OrganTaskName = $Hr_OrganTask1Name;
                                        if ($Hr_OrganTask2Name!=""){
                                            $Str_OrganTaskName .= " > " . $Hr_OrganTask2Name;
                                        }

                                        //1:부하 2:동료 3:상사 4:고객 5:본인
                                        if ($Hr_EvaluationCompetencyMemberType==1){
                                            $Str_Hr_EvaluationCompetencyMemberType = $부하[$LangID];
                                        }else if ($Hr_EvaluationCompetencyMemberType==2){
                                            $Str_Hr_EvaluationCompetencyMemberType = $동료[$LangID];
                                        }else if ($Hr_EvaluationCompetencyMemberType==3){
                                            $Str_Hr_EvaluationCompetencyMemberType = $상사[$LangID];
                                        }else if ($Hr_EvaluationCompetencyMemberType==4){
                                            $Str_Hr_EvaluationCompetencyMemberType = $고객[$LangID];
                                        }else if ($Hr_EvaluationCompetencyMemberType==5){
                                            $Str_Hr_EvaluationCompetencyMemberType = $본인[$LangID];
                                        }
                                        //=================== 자기 자신 ======================

                                        //=================== 동 료 ======================
                                        $T_MemberID = $Row["T_MemberID"];

                                        $T_Hr_OrganLevel = $Row["T_Hr_OrganLevel"];
                                        $T_Hr_OrganPositionName = $Row["T_Hr_OrganPositionName"];

                                        $T_MemberName = $Row["T_MemberName"];

                                        $T_Hr_OrganTask2Name = $Row["T_Hr_OrganTask2Name"];
                                        $T_Hr_OrganTask1Name = $Row["T_Hr_OrganTask1Name"];
                                        $T_Hr_OrganTask2ID   = $Row["T_Hr_OrganTask2ID"];
                                        $T_Hr_OrganTask1ID   = $Row["T_Hr_OrganTask1ID"];


                                        $T_Hr_OrganLevelName1 = $Row["T_Hr_OrganLevelName1"];
                                        $T_Hr_OrganLevelName2 = $Row["T_Hr_OrganLevelName2"];
                                        $T_Hr_OrganLevelName3 = $Row["T_Hr_OrganLevelName3"];
                                        $T_Hr_OrganLevelName4 = $Row["T_Hr_OrganLevelName4"];

                                        $T_Str_Hr_OrganLevelName = $T_Hr_OrganLevelName1;
                                        if ($T_Hr_OrganLevelName2!=""){
                                            $T_Str_Hr_OrganLevelName .= " > " . $T_Hr_OrganLevelName2;
                                        }
                                        if ($T_Hr_OrganLevelName3!=""){
                                            $T_Str_Hr_OrganLevelName .= " > " . $T_Hr_OrganLevelName3;
                                        }
                                        if ($T_Hr_OrganLevelName4!=""){
                                            $T_Str_Hr_OrganLevelName .= " > " . $T_Hr_OrganLevelName4;
                                        }


                                        $T_Str_OrganTaskName = $T_Hr_OrganTask1Name;
                                        if ($T_Hr_OrganTask2Name!=""){
                                            $T_Str_OrganTaskName .= " > " . $T_Hr_OrganTask2Name;
                                        }
                                        
                                        
                                        //=================== 동 료 ======================

                                        $T_MemberCount = $Row["T_MemberCount"];
                                        $TT_MemberCount = $Row["T_MemberCount"];
                                        /*
                                        if ($T_MemberCount==0){
                                              $T_MemberCount = 1;
                                        } else {
                                              $T_MemberCount = $T_MemberCount + 1; 
                                        }
                                        */
                                        $T_AddValue      = $Row["Hr_EvaluationCompetencyAddValue"];
                                        $T_AddTotalPoint = $Row["Hr_EvaluationCompetencyAddTotalPoint"];
                                        
                                        $PrintMember = 0;
                                        if ($OldMemberID!=$MemberID){
                                            /*
                                            if ($H_AddValue > 0) { 
                                            ?>
                                    <tr>
                                        <td colspan="3" class="uk-text-nowrap uk-table-td-right"><?=$평가자_가중치_소계[$LangID]?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><b style="color:#7CB342; font-size:1.1em;"><?=$H_AddValue?>%</b></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=iif($H_AddTotalPoint > 0,"".number_format($H_AddTotalPoint,2)."","-")?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"></td>
                                    </tr>
                                            <?php
                                            }
                                            */
                                            $OldMemberID = $MemberID;
                                            $PrintMember = 1;
                                            $ListCount2++;

                                            $H_AddValue  = 0; 
                                            $H_AddTotalPoint = 0;
                                        }

                                        $H_AddValue      = $H_AddValue + $T_AddValue; 
                                        $H_AddTotalPoint = $H_AddTotalPoint + $T_AddTotalPoint; 
                                    ?>
                                    <tr>
                                        <?if ($PrintMember==1){?>
                                            <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$T_MemberCount?>"><?=$ListCount2?></td>
                                            <td class="uk-text-nowrap uk-table-td-left"   rowspan="<?=$T_MemberCount?>"><?=$MemberName?>[<?=$MemberID?>]</td>
                                            <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$T_MemberCount?>"><?=$Hr_OrganPositionName?></td>
                                            <td class="uk-text-nowrap uk-table-td-left"   rowspan="<?=$T_MemberCount?>"><?=$Str_OrganTaskName?></td>
                                        <?}?>

                                        <td class="uk-text-nowrap uk-table-td-left"><?=$T_MemberName?>[<?=$T_MemberID?>]</td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_EvaluationCompetencyMemberType?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$T_Str_OrganTaskName?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$T_AddValue?>%</td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=iif($T_AddTotalPoint > 0,"".number_format($T_AddTotalPoint,2)."","-")?></td>

                                        <?
                                        $CPTOT_CNT = 0;
                                        $CPTOT_HAP = 0;

                                        $Sql1 = "select A.* from Hr_CompetencyIndicatorCate1 A 
                                                          where A.Hr_CompetencyIndicatorCate1State=1  
                                                       order by A.Hr_CompetencyIndicatorCate1Order asc"; 
                                        $Stmt1 = $DbConn->prepare($Sql1);
                                        $Stmt1->execute();
                                        $Stmt1->setFetchMode(PDO::FETCH_ASSOC);
                                        while($Row1 = $Stmt1->fetch()) {

                                            $Hr_CompetencyIndicatorCate1ID = $Row1["Hr_CompetencyIndicatorCate1ID"];
                                            
                                            $Sql2 = "select A.* from Hr_CompetencyIndicatorCate2 A 
                                                                where A.Hr_CompetencyIndicatorCate1ID=:Hr_CompetencyIndicatorCate1ID
                                                                order by A.Hr_CompetencyIndicatorCate2Order asc";
                                            $Stmt2 = $DbConn->prepare($Sql2);
                                            $Stmt2->bindParam(':Hr_CompetencyIndicatorCate1ID', $Hr_CompetencyIndicatorCate1ID);
                                            $Stmt2->execute();
                                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                                            while($Row2 = $Stmt2->fetch()) {

                                                  $Hr_CompetencyIndicatorCate2ID   = $Row2["Hr_CompetencyIndicatorCate2ID"];

                                                  $Sql3  = "select A.* from Hr_CompetencyIndicators A 
                                                                      where A.Hr_CompetencyIndicatorCate2ID=:Hr_CompetencyIndicatorCate2ID
                                                                      order by A.Hr_CompetencyIndicatorOrder asc"; 
                                                  $Stmt3 = $DbConn->prepare($Sql3);
                                                  $Stmt3->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
                                                  $Stmt3->execute();
                                                  $CP_CNT = 0;
                                                  $CP_HAP = 0;
                                                  while($Row3 = $Stmt3->fetch()) {
                                                            
                                                        $Hr_CompetencyIndicatorID3 = $Row3["Hr_CompetencyIndicatorID"];
                                                       
                                                        #-------------------------------------------------------------------------------------------------------------#
                                                        $Sql4 = "select A.Hr_CompetencyIndicatorPoint as CPPoint 
                                                                     from Hr_Staff_Compentency A 
                                                                    where A.Hr_CompetencyIndicatorID=".$Hr_CompetencyIndicatorID3." and
																		  A.MyMemberID=".$T_MemberID." and 
																		  A.MemberID=".$MemberID." and
																		  A.Hr_EvaluationID=".$SearchState." and 
																		  A.Hr_OrganTask1ID=".$Hr_OrganTask1ID." and 
																		  A.Hr_OrganTask2ID=".$Hr_OrganTask2ID;

                                                        $Stmt4 = $DbConn->prepare($Sql4);
                                                        $Stmt4->execute();
                                                        $Stmt4->setFetchMode(PDO::FETCH_ASSOC);
                                                        $Row4 = $Stmt4->fetch();
                                                        $CPPoint = 0;
                                                        if ($Row4) {
                                                              $CPPoint   = $Row4["CPPoint"];
                                                              $CP_HAP    = $CP_HAP + $Row4["CPPoint"];
                                                              $CPTOT_HAP = $CPTOT_HAP + $Row4["CPPoint"];
                                                              $CPTOT_CNT++;
                                                              $CP_CNT++;
                                                        }
                                                        #-------------------------------------------------------------------------------------------------------------#
                                                        ?>
                                        <td align="center"><?=iif($CPPoint>0,$CPPoint,"")?></td>
                                                       <? 
                                                  }
                                                  $CP_AVG = 0;
                                                  if ($CP_HAP > 0) {
                                                       $CP_AVG = $CP_HAP / $CP_CNT;
                                                  }
                                                  ?>
                                        <td align="center"><?=iif($CP_AVG>0,"".number_format($CP_AVG,2)."","")?></td>
                                                  <?
                                            }
                                        }
                                        $CPTOT_AVG = 0;
                                        if ($CPTOT_HAP > 0) {
                                               $CPTOT_AVG = $CPTOT_HAP / $CPTOT_CNT;
                                        }
                                        ?>
                                        <td align="center"><?=iif($CPTOT_AVG>0,"".number_format($CPTOT_AVG,2)."","")?></td>
                                        
                                    </tr>

                                <?php
                                }
                                /*
                                if ($H_AddValue > 0) { 
                                ?>
                                    <tr>
                                        <td colspan="3" class="uk-text-nowrap uk-table-td-right"><?=$평가자_가중치_소계[$LangID]?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><b style="color:#7CB342; font-size:1.1em;"><?=$H_AddValue?>%</b></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=iif($H_AddTotalPoint > 0,"".$H_AddTotalPoint."","-")?></td>
                                    </tr>
                                <?php
                                }
                                */
                                $Stmt = null;

                                if ($ListCount==0) {
                                ?>
                                    <tr>
                                        <td class="uk-text-nowrap uk-table-td-center" colspan="100" style="height:50px;"><?=$해당_역량평가자료가_없습니다[$LangID]?></td>
                                    </tr>
                                <? 
                                }
                                ?>
                                </tbody>
                            </table>

                            <?
                    #=====================================================================================================================#
                    } else { 
                    #=====================================================================================================================#
                            ?>
                        <div class="uk-form-row" style="text-align:center; padding:30px; color:#BCBCBC; font-size:1.5em;">
                            결과자료가 없습니다!
                        </div>
                            <?
                    #=====================================================================================================================#
                    } 
                    #=====================================================================================================================#
                    ?>
                    <script type="text/javascript">
                     document.getElementById("result2_dis").innerHTML = "▷ 원데이터(5점만점) - <b style='color:#4677C4;'>(집계를 마쳤습니다! 현황표를 보셔도 됩니다!)</b>";
                    </script>  
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
    document.SearchForm.action = "hr_staffall_dbdownload.php";
    document.SearchForm.submit();
}

function ExcelDown(){
    location.href = "hr_staffall_dbdownload_excel.php?SearchState=<?=$SearchState?>&DownMenu=<?=$DownMenu?>";
}

</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>