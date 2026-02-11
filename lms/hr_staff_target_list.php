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
$SubMenuID = 8851;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

$MemberLoginID     = isset($_COOKIE["LoginMemberID"   ]) ? $_COOKIE["LoginMemberID"   ] : "";
$SearchState       = isset($_REQUEST["SearchState"    ]) ? $_REQUEST["SearchState"    ] : "";
$CallSearchState   = isset($_REQUEST["CallSearchState"]) ? $_REQUEST["CallSearchState"] : "";
$KpiIndicatorID    = isset($_REQUEST["KpiIndicatorID" ]) ? $_REQUEST["KpiIndicatorID" ] : "";
$TargetState       = isset($_REQUEST["TargetState"    ]) ? $_REQUEST["TargetState"    ] : "";
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

        <h3 class="heading_b uk-margin-bottom">[<?=$My_MemberName?>] 
        <?
		if ($My_OrganLevel > 1 and $My_OrganLevel < 4) {
				?>
				<a type="button" href="hr_staff_target_list.php?TargetMenu=1" class="md-btn md-btn-primary" style="background:<?if ( $TargetMenu==1 ){?>#2196F3<?} else {?>#6FB9F7<?}?>;"><?=$개인[$LangID]?></a>
				<a type="button" href="hr_staff_target_list.php?TargetMenu=2" class="md-btn md-btn-primary" style="background:<?if ( $TargetMenu==2 ){?>#2196F3<?} else {?>#6FB9F7<?}?>;"><?=$부문[$LangID]?></a>
				<?=$목표설정[$LangID]?>
				<?
		} else if ($My_OrganLevel==1) {   // 최종상사인 경우

                $TargetMenu = 2; 
                ?>
                <?=$부문[$LangID]?> <?=$목표설정[$LangID]?>
		        <?

		} else {

                ?>
                <?=$개인[$LangID]?> <?=$목표설정[$LangID]?>
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
                                        <option value="<?=$Hr_EvaluationID?>" <? if ($Hr_EvaluationID==$SearchState){?> selected <?}?>><?=$Str_Hr_EvaluationYear?></option>
                                        <?php
                                        $ListCount ++;
                                    }
                                    $Stmt = null;
                                    ?>

                            </select> 
                        </div>
                    </div>
        <?
        #---------------------------------------------------------------------------------------------------------------------------------#
		if ($TargetMenu==1) {
        #---------------------------------------------------------------------------------------------------------------------------------#
        ?>
                    <div id="KPI_Button" class="uk-width-medium-2-3">
                    <? if ($SearchState and $TargetState != 9 and $Hr_TargetState != 9) { 
						if (!$KpiIndicatorID) { 
						     ?>
						     <a href="javascript:CallKPIForm()" class="md-btn md-btn-primary" style="float:right; margin-right:30px; margin-top:10px; background:#F0F0F0; color:#5B5B5B;">＋<?=$최종_작성내용_불러오기[$LangID]?></a>
                             <? 
						} 
						?>
                        <a href="javascript:OpenKPIForm()" class="md-btn md-btn-primary" style="float:right; margin-right:30px; margin-top:10px; background:#1CC6EA;"><?=$KPI검색[$LangID]?></a>
                    <? } ?>
                    </div>

                </div>
        <?
        #---------------------------------------------------------------------------------------------------------------------------------#
        }
        #---------------------------------------------------------------------------------------------------------------------------------#
        ?>
            </div>
        </div>
        </form>

        <?
        #=================================================================================================================================#
        #================================================================ 개인목표설정 =======================================================#
        #=================================================================================================================================#
		if ($TargetMenu==1) {
        #=================================================================================================================================#
        ?>	
		<form name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" id="TargetMenu"          name="TargetMenu"          value="<?=$TargetMenu?>" />
		<input type="hidden" id="SearchState"         name="SearchState"         value="<?=$SearchState?>" style="width:10%;" />
        <input type="hidden" id="Hr_EvaluationID"     name="Hr_EvaluationID"     value="<?=$SearchState?>" style="width:10%;" />
        <input type="hidden" id="Hr_EvaluationTypeID" name="Hr_EvaluationTypeID" value="<?=$Hr_EvaluationTypeID?>" style="width:10%;" />
        <input type="hidden" id="Hr_MemberID"         name="Hr_MemberID"         value="<?=$My_MemberID?>" style="width:10%;" />
        <input type="hidden" id="Hr_OrganLevelID"     name="Hr_OrganLevelID"     value="<?=$My_OrganLevelID?>" style="width:10%;" />
        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div id="kpi_listup" class="uk-overflow-container">
                    <? 
                    $line_cnt = 0;
					$Target_Boarder = 1;
                    #---------------------------------------------------------------------------------------------------------------------#
					if ($SearchState and $KpiIndicatorID) { 
                    #---------------------------------------------------------------------------------------------------------------------#
                            $KpiIndicatorID_array = explode("/",$KpiIndicatorID);  
                            #-------------------------------------------------------------------------------------------------------------#
                            for ($i=0; $i < count($KpiIndicatorID_array); $i++) {
                            #-------------------------------------------------------------------------------------------------------------#
                                    $Sql = "select A.*,
                                                   (select Hr_KpiIndicatorUnitName from Hr_KpiIndicatorUnits where Hr_KpiIndicatorUnitID=A.Hr_KpiIndicatorUnitID) as Hr_KpiIndicatorUnitName
                                              from Hr_KpiIndicators A 
                                              where A.Hr_KpiIndicatorID=:Hr_KpiIndicatorID";
                                    $Stmt = $DbConn->prepare($Sql);
									$Stmt->bindParam(':Hr_KpiIndicatorID', $KpiIndicatorID_array[$i]);
                                    $Stmt->execute();
                                    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                                    $Row = $Stmt->fetch();  
                                    #-----------------------------------------------------------------------------------------------------#
									if ($Row) {
                                    #-----------------------------------------------------------------------------------------------------#
                                            $Hr_KpiIndicatorID       = $Row["Hr_KpiIndicatorID"];
                                            $Hr_KpiIndicatorName     = $Row["Hr_KpiIndicatorName"];
                                            $Hr_KpiIndicatorDefine   = $Row["Hr_KpiIndicatorDefine"];
                                            $Hr_KpiIndicatorFormula  = $Row["Hr_KpiIndicatorFormula"];
                                            $Hr_KpiIndicatorMeasure  = $Row["Hr_KpiIndicatorMeasure"];
                                            $Hr_KpiIndicatorSource   = $Row["Hr_KpiIndicatorSource"];
                                            $Hr_KpiIndicatorPartName = $Row["Hr_KpiIndicatorPartName"];
                                            $Hr_KpiIndicatorUnitID   = $Row["Hr_KpiIndicatorUnitID"];
                                            $Hr_KpiIndicatorState    = $Row["Hr_KpiIndicatorState"];
                                            $Hr_KpiIndicatorUnitName = $Row["Hr_KpiIndicatorUnitName"];
                                            #---------------------------------------------------------------------------------------------#
                                            $Hr_TargetName     = ""; 
                                            $Hr_TargetAddValue = ""; 
                                            #---------------------------------------------------------------------------------------------#
                                            $EvaluationID = $SearchState; 
											if ($CallSearchState) {
                                                   $EvaluationID = $CallSearchState; 
											}
											$Hr_KpiCheck       = "checked";
											$readonly_val      = "";
											$able_val          = "";
											$Target_BKColor    = "#F0F0F0";
                                            #---------------------------------------------------------------------------------------------#
											$Sql2 = "select * from Hr_Staff_Target 
											                 where MemberID=".$My_MemberID." and 
															       Hr_EvaluationID=".$EvaluationID." and 
															       Hr_KpiIndicatorID=".$Hr_KpiIndicatorID;
											$Stmt2 = $DbConn->prepare($Sql2);
											$Stmt2->execute();
											$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
											$Row2 = $Stmt2->fetch();
											if ($Row2) {
                                                    $Hr_TargetName     = $Row2["Hr_TargetName"]; 
                                                    $Hr_TargetAddValue = $Row2["Hr_TargetAddValue"]; 
													$Hr_TargetState    = $Row2["Hr_TargetState"]; 
													if ($Hr_TargetState == 9) {
															$Hr_KpiCheck    = "";
															$readonly_val   = "readonly";
															$able_val       = "disabled";
															$Target_BKColor = "#fff";
															$Target_Boarder = 0;
													}
												    $DB_KpiIndicatorID = 'Y';
											}
                                            $Stmt2 = null;
                                            #---------------------------------------------------------------------------------------------#
                                            $line_cnt++;
                                            ?>
                            <table class="uk-table uk-table-align-vertical">
                                <tbody>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center" width="5%" rowspan="5"><input type="checkbox" id="KPI_CHECKID_<?=$line_cnt?>" name="KPI_CHECKID_<?=$line_cnt?>" value="<?=$Hr_KpiIndicatorID?>" <?=$Hr_KpiCheck?> <?=$able_val?> style="width:20px; height:20px;"></td>
                                        <td class="uk-text-wrap uk-table-td-center"><?=$업적목표[$LangID]?></td>
                                        <td class="uk-text-wrap uk-table-td-center" colspan="3">
                                           <input type="text" id="KPI_TARGET1_<?=$line_cnt?>" name="KPI_TARGET1_<?=$line_cnt?>" value="<?=$Hr_TargetName?>" <?=$readonly_val?> style="height:25px;width:95%;border:<?=$Target_Boarder?>px solid #cccccc; padding-left:10px;padding-right:10px;"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center">KPI(<?=$핵심성과지표[$LangID]?>)</td>
                                        <td class="uk-text-wrap uk-table-td-center" colspan="3">
                                           <input type="text" id="KPI_TARGET2_<?=$line_cnt?>" name="KPI_TARGET2_<?=$line_cnt?>" readonly value="<?=$Hr_KpiIndicatorName?>" style="width:95%;  background:<?=$Target_BKColor?>; border:<?=$Target_Boarder?>px solid #cccccc; padding:10px;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center"><?=$측정산식[$LangID]?></td>
                                        <td class="uk-text-wrap uk-table-td-center" colspan="3">
                                           <textarea id="KPI_TARGET3_<?=$line_cnt?>" name="KPI_TARGET3_<?=$line_cnt?>" readonly style="height:100px;width:95%;  background:<?=$Target_BKColor?>; border:<?=$Target_Boarder?>px solid #cccccc; padding:10px;"><?=$Hr_KpiIndicatorFormula?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center"><?=$평가척도[$LangID]?></td>
                                        <td class="uk-text-wrap uk-table-td-center" colspan="3">
                                           <textarea id="KPI_TARGET4_<?=$line_cnt?>" name="KPI_TARGET4_<?=$line_cnt?>" readonly style="height:100px;width:95%;  background:<?=$Target_BKColor?>; border:<?=$Target_Boarder?>px solid #cccccc; padding:10px;"><?=$Hr_KpiIndicatorMeasure?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center" width="15%"><?=$가중치[$LangID]?></td>
                                        <td class="uk-text-wrap uk-table-td-center" width="35%" style="text-align:left; padding-left:20px;">
                                           <input type="text" id="KPI_TARGET5_<?=$line_cnt?>" name="KPI_TARGET5_<?=$line_cnt?>" value="<?=$Hr_TargetAddValue?>" <?=$readonly_val?> style="width:10%; border:<?=$Target_Boarder?>px solid #cccccc; padding:10px;">%
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center" width="15%"><?=$평가부서[$LangID]?>(<?=$부문[$LangID]?>)</td>
                                        <td class="uk-text-wrap uk-table-td-center" width="30%">
                                           <input type="text" id="KPI_TARGET6_<?=$line_cnt?>" name="KPI_TARGET6_<?=$line_cnt?>" value="<?=$Str_Hr_OrganLevelName?>" readonly style="width:90%;  background:<?=$Target_BKColor?>; border:<?=$Target_Boarder?>px solid #cccccc; padding:10px;" />
                                        </td>
                                </tbody>
                            </table>

								    <?
                                    #-----------------------------------------------------------------------------------------------------#
								    }
                                    #-----------------------------------------------------------------------------------------------------#
									$Stmt = null;
                            #-------------------------------------------------------------------------------------------------------------#
                            }
                    #---------------------------------------------------------------------------------------------------------------------#
					} else if ($SearchState and !$KpiIndicatorID) { 
                    #---------------------------------------------------------------------------------------------------------------------#
							$Sql = "select * from Hr_Staff_Target 
											 where MemberID=".$My_MemberID." and 
												   Hr_EvaluationID=".$SearchState;
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
                            #-------------------------------------------------------------------------------------------------------------#
							while ($Row = $Stmt->fetch()) {
                            #-------------------------------------------------------------------------------------------------------------#
								    $Hr_KpiIndicatorID = $Row["Hr_KpiIndicatorID"];
                                    $Hr_TargetName     = $Row["Hr_TargetName"]; 
                                    $Hr_TargetAddValue = $Row["Hr_TargetAddValue"]; 
                                    $Hr_TargetState    = $Row["Hr_TargetState"]; 
                                    $Hr_KpiCheck       = "checked";
									$readonly_val      = "";
									$able_val          = "";
									$Target_BKColor    = "#F0F0F0";
									if ($Hr_TargetState == 9) {
									        $Hr_KpiCheck    = "";
											$readonly_val   = "readonly";
											$able_val       = "disabled";
											$Target_BKColor = "#fff";
											$Target_Boarder = 0;
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
                            <table class="uk-table uk-table-align-vertical">
                                <tbody>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center" width="5%" rowspan="5"><input type="checkbox" id="KPI_CHECKID_<?=$line_cnt?>" name="KPI_CHECKID_<?=$line_cnt?>" value="<?=$Hr_KpiIndicatorID?>" <?=$Hr_KpiCheck?> <?=$able_val?> style="width:20px; height:20px;"></td>
                                        <td class="uk-text-wrap uk-table-td-center"><?=$업적목표[$LangID]?></td>
                                        <td class="uk-text-wrap uk-table-td-center" colspan="3">
                                           <input type="text" id="KPI_TARGET1_<?=$line_cnt?>" name="KPI_TARGET1_<?=$line_cnt?>" value="<?=$Hr_TargetName?>" <?=$readonly_val?> style="height:25px;width:95%; background:<?=$Target_BKColor?>; border:<?=$Target_Boarder?>px solid #cccccc; padding-left:10px;padding-right:10px;"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center">KPI(<?=$핵심성과지표[$LangID]?>)</td>
                                        <td class="uk-text-wrap uk-table-td-center" colspan="3">
                                           <input type="text" id="KPI_TARGET2_<?=$line_cnt?>" name="KPI_TARGET2_<?=$line_cnt?>" readonly value="<?=$Hr_KpiIndicatorName?>" style="width:95%;  background:<?=$Target_BKColor?>; border:<?=$Target_Boarder?>px solid #cccccc; padding:10px;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center"><?=$측정산식[$LangID]?></td>
                                        <td class="uk-text-wrap uk-table-td-center" colspan="3">
                                           <textarea id="KPI_TARGET3_<?=$line_cnt?>" name="KPI_TARGET3_<?=$line_cnt?>" readonly style="height:100px;width:95%; background:<?=$Target_BKColor?>; border:<?=$Target_Boarder?>px solid #cccccc; padding:10px;"><?=$Hr_KpiIndicatorFormula?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center"><?=$평가척도[$LangID]?></td>
                                        <td class="uk-text-wrap uk-table-td-center" colspan="3">
                                           <textarea id="KPI_TARGET4_<?=$line_cnt?>" name="KPI_TARGET4_<?=$line_cnt?>" readonly style="height:100px;width:95%; background:<?=$Target_BKColor?>; border:<?=$Target_Boarder?>px solid #cccccc; padding:10px;"><?=$Hr_KpiIndicatorMeasure?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center" width="15%"><?=$가중치[$LangID]?></td>
                                        <td class="uk-text-wrap uk-table-td-center" width="35%" style="text-align:left; padding-left:20px;">
                                           <input type="text" id="KPI_TARGET5_<?=$line_cnt?>" name="KPI_TARGET5_<?=$line_cnt?>" value="<?=$Hr_TargetAddValue?>" <?=$readonly_val?>  style="width:10%; background:<?=$Target_BKColor?>; border:<?=$Target_Boarder?>px solid #cccccc; padding:10px;">%
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center" width="15%"><?=$평가부서[$LangID]?>(<?=$부문[$LangID]?>)</td>
                                        <td class="uk-text-wrap uk-table-td-center" width="30%">
                                           <input type="text" id="KPI_TARGET6_<?=$line_cnt?>" name="KPI_TARGET6_<?=$line_cnt?>" value="<?=$Str_Hr_OrganLevelName?>" readonly style="width:90%;  background:<?=$Target_BKColor?>; border:<?=$Target_Boarder?>px solid #cccccc; padding:10px;" />
                                        </td>
                                </tbody>
                            </table>
							        <? 
                                    #-----------------------------------------------------------------------------------------------------#
								    }
                            #-------------------------------------------------------------------------------------------------------------#
                            }
                            #-------------------------------------------------------------------------------------------------------------#
						    $Stmt = null;
                    #---------------------------------------------------------------------------------------------------------------------#
                    }
                    #---------------------------------------------------------------------------------------------------------------------#
                    ?>
                        </div>
                    <? 
                    if ($KpiIndicatorID and $TargetState != 9 and $Hr_TargetState != 9) { 
                            
                             if ($Hr_TargetState == 8) { 
                                  ?>
                        <div class="uk-form-row" style="text-align:center; padding:20px 0 0 0; color:#BCBCBC; font-size:1.2em;">
                             <?=$반려된_목표설정_입니다[$LangID]?>
                        </div>
                                   <? 
							 }
                             ?>
                        <div class="uk-form-row" style="text-align:center; margin-top:20px;">
							<a type="button" href="javascript:HRTarget_Act(1)" class="md-btn md-btn-primary" style="background:#1CC6EA;"><?=$추가하기[$LangID]?></a>
							<a type="button" href="javascript:HRTarget_Act(2)" class="md-btn md-btn-primary" style="background:#F0F0F0; color:#5B5B5B;"><?=$삭제하기[$LangID]?></a>
							<a type="button" href="javascript:HRTarget_Act(3)" class="md-btn md-btn-primary" style="background:#CACACA; color:#5B5B5B;"><?=$임시저장[$LangID]?></a>
							<a type="button" href="javascript:HRTarget_Act(4)" class="md-btn md-btn-primary"><?=$제출하기[$LangID]?></a>
                        </div>
                    <? 
                    } else if ($KpiIndicatorID and ($TargetState == 9 or $Hr_TargetState == 9)) { 
                    ?>
                        <div class="uk-form-row" style="text-align:center; padding:30px; color:#BCBCBC; font-size:1.5em;">
                             <?=$최종_제출된_목표설정_목록_입니다[$LangID]?> 
                        </div>
						<script type="text/javascript">
						document.getElementById("KPI_Button").style.display = "none";
						</script>
                    <? 
                    } else {
                    ?>
                        <div class="uk-form-row" style="text-align:center; padding:30px; color:#BCBCBC; font-size:1.5em;">
                             <?=$평가리스트를_선택하시면_목표설정_확인_이_가능합니다[$LangID]?> 
                        </div>
                    <? 
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div>
		<input type="hidden" id="KpiIndicatorID"    name="KpiIndicatorID"    value="<?=$KpiIndicatorID?>" style="width:10%;" />
		<input type="hidden" id="DB_KpiIndicatorID" name="DB_KpiIndicatorID" value="<?=$DB_KpiIndicatorID?>" style="width:10%;" />
        <input type="hidden" id="line_cnt"          name="line_cnt"          value="<?=$line_cnt?>" style="width:10%;" />
        </form>
		<?
        #=================================================================================================================================#
        #================================================== 부문(소속부서) 목표설정(확인) ========================================================#
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
										TT.Hr_EvaluationID,
										TT.Hr_UseYN,

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
										inner join Hr_Staff_Target TT on TT.MemberID=B.MemberID and TT.Hr_EvaluationID=".$SearchState." and TT.Hr_TargetState='9' 

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
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="10%"><?=$성명[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="50%"><?=$소속부서[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="20%"><?=$직무[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="10%"><?=$상사확인[$LangID]?></td>
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

									$Hr_EvaluationID = $Row["Hr_EvaluationID"];
									$Hr_UseYN        = $Row["Hr_UseYN"];
									//=================== 자기 자신 ======================
									$MemberID = $Row["MemberID"];

									$Hr_OrganLevel    = $Row["Hr_OrganLevel"];
									$Hr_OrganLevel1ID = $Row["Hr_OrganLevel1ID"];
									$Hr_OrganLevel2ID = $Row["Hr_OrganLevel2ID"];
									$Hr_OrganLevel3ID = $Row["Hr_OrganLevel3ID"];
									$Hr_OrganLevel4ID = $Row["Hr_OrganLevel4ID"];

									$Level_Chi = $Hr_OrganLevel - $My_OrganLevel;

									$Hr_OrganTask2ID  = $Row["Hr_OrganTask2ID"];
									$Hr_OrganPositionName = $Row["Hr_OrganPositionName"];

									$MemberName = $Row["MemberName"];

									$Hr_OrganTask2Name = $Row["Hr_OrganTask2Name"];
									$Hr_OrganTask1Name = $Row["Hr_OrganTask1Name"];


									$Hr_OrganLevelName1 = $Row["Hr_OrganLevelName1"];
									$Hr_OrganLevelName2 = $Row["Hr_OrganLevelName2"];
									$Hr_OrganLevelName3 = $Row["Hr_OrganLevelName3"];
									$Hr_OrganLevelName4 = $Row["Hr_OrganLevelName4"];

									$Str_Hr_OrganLevelID = $Hr_OrganLevel1ID;
									if ($Hr_OrganLevel2ID!=""){
										$Str_Hr_OrganLevelID .= " > " . $Hr_OrganLevel2ID;
									}
									if ($Hr_OrganLevel3ID!=""){
										$Str_Hr_OrganLevelID .= " > " . $Hr_OrganLevel3ID;
									}
									if ($Hr_OrganLevel4ID!=""){
										$Str_Hr_OrganLevelID .= " > " . $Hr_OrganLevel4ID;
									}

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
                                    $BossCount = $Row["T_BossCount"];
									//=================== 자기 자신 ======================
                                    ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$line_cnt?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$MemberName?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Str_Hr_OrganLevelName?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_OrganTask1Name?></td>
                                   <td class="uk-text-wrap uk-table-td-center">
								    <?
                                    if ($Hr_UseYN=='N') {
										 if ($Level_Chi==1 or $BossCount == 0 or $BossCount == 1) {
									     ?>
							<a type="button" href="javascript:HRTarget_ViewOpen(1,<?=$MemberID?>,<?=$Hr_EvaluationID?>)" class="md-btn md-btn-primary" style="background:#1CC6EA;"><?=$검토하기[$LangID]?></a>
									     <?
									     } else {
									     ?>
							<a type="button" href="javascript:HRTarget_ViewOpen(2,<?=$MemberID?>,<?=$Hr_EvaluationID?>)" class="md-btn md-btn-primary" style="background:#408080;"><?=$미실시[$LangID]?></a>
									     <?
										 }
									} else { 
									?>
							<a type="button" href="javascript:HRTarget_ViewOpen(9,<?=$MemberID?>,<?=$Hr_EvaluationID?>)" class="md-btn md-btn-primary" style="background:#CACACA; color:#5B5B5B;"><?=$검토완료[$LangID]?></a>
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
                                   <td class="uk-text-wrap uk-table-td-center" style="text-align:center; padding:20px; color:#BCBCBC; font-size:1.5em;" colspan=10>
									  <?=$목표설정_대상이_없습니다[$LangID]?>
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
// 평가리스트 선택
//-------------------------------------------------------------------------------------------------------------------------//
function SearchSubmit(){
	   document.SearchForm.action = "hr_staff_target_list.php";
	   document.SearchForm.submit();

}
//-------------------------------------------------------------------------------------------------------------------------//
// KPI 오픈
//-------------------------------------------------------------------------------------------------------------------------//
function OpenKPIForm() {
    
	var SearchState    = document.RegForm.SearchState.value;
	var KpiIndicatorID = document.RegForm.KpiIndicatorID.value;
	var TargetMenu     = document.RegForm.TargetMenu.value;

	openurl = "hr_staff_target_kpi_list.php?SearchState=" + SearchState + "&TargetMenu=" + TargetMenu + "&KpiIndicatorID=" + KpiIndicatorID;

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
//-------------------------------------------------------------------------------------------------------------------------//
// 최종 작성물 내용 불러오기
//-------------------------------------------------------------------------------------------------------------------------//
function CallKPIForm() {

	   var SearchState = document.RegForm.SearchState.value;
	   var TargetMenu  = document.RegForm.TargetMenu.value;
	
	   UIkit.modal.confirm(
			"<?=$최종_작성된_목표설정_목록을_불러서_사용_하시겠습니까[$LangID]?>", 
			function(){ 
				  document.RegForm.action = "hr_staff_target_call.php?SearchState=" + SearchState + "&TargetMenu=" + TargetMenu;
				  document.RegForm.submit();
			}
	  );  

}
//-------------------------------------------------------------------------------------------------------------------------//
// 목표설정
//-------------------------------------------------------------------------------------------------------------------------//
function HRTarget_Act(s) {
   
	   var SearchState     = document.RegForm.SearchState.value;
	   var KpiIndicatorID  = document.RegForm.KpiIndicatorID.value;
	   var TargetMenu      = document.RegForm.TargetMenu.value;
	   var OrganLevelID    = document.RegForm.Hr_OrganLevelID.value;
	   //-----------------------------------------------------------------------------------------------------------------//
	   if (!OrganLevelID && s > 2) {
		      alert("<?=$업적목표설정은_소속부서가_설정되어_있어야_가능_합니다[$LangID]?>");
			  return;
	   }
	   //-----------------------------------------------------------------------------------------------------------------//
	   if (s == 1) {              // 추가
	   //-----------------------------------------------------------------------------------------------------------------//
			 
			 OpenKPIForm();

	   //-----------------------------------------------------------------------------------------------------------------//
	   } else if (s == 2) {       // 삭제
	   //-----------------------------------------------------------------------------------------------------------------//
			 var KpiIndicatorID    = "";
			 var KpiIndicatorDelID = 0;
			 var DB_KpiIndicatorID = document.getElementById("DB_KpiIndicatorID").value;
			 var line_cnt          = document.RegForm.line_cnt.value;
			 for(var i = 1; i <= line_cnt; i++) {
					if (document.getElementById("KPI_CHECKID_"+i).checked==true) {
						  KpiIndicatorID  = KpiIndicatorID + jviif(KpiIndicatorID,"/","") + document.getElementById("KPI_CHECKID_"+i).value;
					} else if (document.getElementById("KPI_CHECKID_"+i).checked==false) {
						  KpiIndicatorDelID++;
					}
			 }
			 if (KpiIndicatorDelID==0) {
					alert("삭제할 항목을 체크해제 하세요!");
					return;
			 }
			 document.getElementById("KpiIndicatorID").value = KpiIndicatorID;

			 UIkit.modal.confirm(
				 "체크해제 한 목표설정 항목을 삭제 하시겠습니까?", 
				 function(){ 
					   if (DB_KpiIndicatorID=='Y') {
							  document.RegForm.action = "hr_staff_target_action.php?TargetState=1";
					   } else {
							  document.RegForm.action = "hr_staff_target_list.php";
					   }
					   document.RegForm.submit();
				 }
			 );  
	   //-----------------------------------------------------------------------------------------------------------------//
	   } else if (s == 3) {       // 임시저장
	   //-----------------------------------------------------------------------------------------------------------------//
			 var KpiIndicatorID    = "";
			 var line_cnt          = document.RegForm.line_cnt.value;
			 for(var i = 1; i <= line_cnt; i++) {

					if (document.getElementById("KPI_CHECKID_"+i).checked==true) {
						  KpiIndicatorID  = KpiIndicatorID + jviif(KpiIndicatorID,"/","") + document.getElementById("KPI_CHECKID_"+i).value;
						  
						  var kpi_target1 = document.getElementById("KPI_TARGET1_"+i).value;
						  var kpi_target2 = document.getElementById("KPI_TARGET2_"+i).value;
						  if (!kpi_target1) {
								alert(kpi_target2 + " <?=$업적목표를_입력_하세요[$LangID]?>");
								document.getElementById("KPI_TARGET1_"+i).focus(); 
								return;
						  }
						  var kpi_target5 = document.getElementById("KPI_TARGET5_"+i).value;
						  if (!kpi_target5) {
								alert(kpi_target2 + " <?=$가중치를_입력_하세요[$LangID]?>");
								document.getElementById("KPI_TARGET5_"+i).focus(); 
								return;
						  }

					}

			 }

			 if (KpiIndicatorID=="") {
					alert("<?=$임시_저장할_항목들을_체크_하세요[$LangID]?>");
					return;
			 }
			 document.getElementById("KpiIndicatorID").value = KpiIndicatorID;

			 UIkit.modal.confirm(
				 '<?=$선택한_목표설정_항목들을_임시저장_하시겠습니까[$LangID]?>', 
				 function(){ 
					   document.RegForm.action = "hr_staff_target_action.php?TargetState=1";
					   document.RegForm.submit();
				 }
			 );  
	   //-----------------------------------------------------------------------------------------------------------------//
	   } else {                   // 제출하기
	   //-----------------------------------------------------------------------------------------------------------------//
			 var KpiIndicatorID    = "";
			 var line_cnt          = document.RegForm.line_cnt.value;
			 for(var i = 1; i <= line_cnt; i++) {

					if (document.getElementById("KPI_CHECKID_"+i).checked==true) {
						  KpiIndicatorID  = KpiIndicatorID + jviif(KpiIndicatorID,"/","") + document.getElementById("KPI_CHECKID_"+i).value;
						  
						  var kpi_target1 = document.getElementById("KPI_TARGET1_"+i).value;
						  var kpi_target2 = document.getElementById("KPI_TARGET2_"+i).value;
						  if (!kpi_target1) {
								alert(kpi_target2 + " <?=$업적목표를_입력_하세요[$LangID]?>");
								document.getElementById("KPI_TARGET1_"+i).focus(); 
								return;
						  }
						  var kpi_target5 = document.getElementById("KPI_TARGET5_"+i).value;
						  if (!kpi_target5) {
								alert(kpi_target2 + " <?=$가중치를_입력_하세요[$LangID]?>");
								document.getElementById("KPI_TARGET5_"+i).focus(); 
								return;
						  }

					}

			 }

			 if (KpiIndicatorID=="") {
					alert("<?=$최종_제출할_항목들을_체크_하세요[$LangID]?>");
					return;
			 }
			 document.getElementById("KpiIndicatorID").value = KpiIndicatorID;

			 UIkit.modal.confirm(
				 '<?=$선택한_목표설정_항목들을_제출_하시겠습니까[$LangID]?><br><?=$한번_제출된_자료는_수정할_수_없습니다[$LangID]?>', 
				 function(){ 
					   document.RegForm.action = "hr_staff_target_action.php?TargetState=9";
					   document.RegForm.submit();
				 }
			 );  
	   //-----------------------------------------------------------------------------------------------------------------//
	   }
	   //-----------------------------------------------------------------------------------------------------------------//

}
//-------------------------------------------------------------------------------------------------------------------------//
// 부문목표설정
//-------------------------------------------------------------------------------------------------------------------------//
function HRTarget_ViewOpen(vs,MemberID,EvaluationID) {

	var SearchState = document.RegForm.SearchState.value;
	var TargetMenu  = document.RegForm.TargetMenu.value;
    
	openurl = "hr_staff_target_kpi_list.php?ViewSW=" + vs + "&SearchState=" + SearchState + "&TargetMenu=" + TargetMenu + "&MemberID=" + MemberID + "&EvaluationID=" + EvaluationID;

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