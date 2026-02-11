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

<style>
#chartdiv {
  width: 100%;
  height: 600px;
}
#chartsubdiv {
  /*display: none;*/
  width:  100%;
  height: 600px;
}

#categorydiv {
  width: 100%;
  height: 50px;
}
</style>
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
#--------------------------------------------------------------------------------------------------------------------#
$MainMenuID = 21;
$SubMenuID  = 2103;
include_once('./inc_top.php'); 
include_once('./inc_menu_left.php');
#--------------------------------------------------------------------------------------------------------------------#
$AddSqlWhere             = "1=1";
$AddSqlWhere2            = "1=1";
$ListParam               = "1=1";
$TotalRowCount           = "";
#--------------------------------------------------------------------------------------------------------------------#
$OldSearchStandard       = isset($_REQUEST["OldSearchStandard"      ]) ? $_REQUEST["OldSearchStandard"      ] : "";
$SearchStandard          = isset($_REQUEST["SearchStandard"         ]) ? $_REQUEST["SearchStandard"         ] : "";
$SearchStandardDetail    = isset($_REQUEST["SearchStandardDetail"   ]) ? $_REQUEST["SearchStandardDetail"   ] : "";
$SearchStandardDetailSub = isset($_REQUEST["SearchStandardDetailSub"]) ? $_REQUEST["SearchStandardDetailSub"] : "";
$SearchDate              = isset($_REQUEST["SearchDate"             ]) ? $_REQUEST["SearchDate"             ] : "";
#--------------------------------------------------------------------------------------------------------------------#
$SearchStartYear         = isset($_REQUEST["SearchStartYear"        ]) ? $_REQUEST["SearchStartYear"        ] : "";
$SearchStartMonth        = isset($_REQUEST["SearchStartMonth"       ]) ? $_REQUEST["SearchStartMonth"       ] : "";
$SearchStartDay          = isset($_REQUEST["SearchStartDay"         ]) ? $_REQUEST["SearchStartDay"         ] : "";
#--------------------------------------------------------------------------------------------------------------------#
$SearchEndYear           = isset($_REQUEST["SearchEndYear"          ]) ? $_REQUEST["SearchEndYear"          ] : "";
$SearchEndMonth          = isset($_REQUEST["SearchEndMonth"         ]) ? $_REQUEST["SearchEndMonth"         ] : "";
$SearchEndDay            = isset($_REQUEST["SearchEndDay"           ]) ? $_REQUEST["SearchEndDay"           ] : "";
#--------------------------------------------------------------------------------------------------------------------#
$SearchClassCntSW        = isset($_REQUEST["SearchClassCntSW"       ]) ? $_REQUEST["SearchClassCntSW"       ] : "";
#--------------------------------------------------------------------------------------------------------------------#
if ($OldSearchStandard != $SearchStandard) {
        $SearchStandardDetail    = "";
        $SearchStandardDetailSub = "";
        $SearchDate              = "";
}
if($SearchStandard=="") {
        $SearchStandard = 1;
}
if ($SearchStandardDetail=="") {
        $SearchStandardDetail = 1;
}
if($SearchStandardDetailSub=="") {
        $SearchStandardDetailSub = 0;
}
if($SearchDate=="") {
        $SearchDate = 1;
}
if($SearchClassCntSW=="") {
        $SearchClassCntSW = 1;
}
#--------------------------------------------------------------------------------------------------------------------#
if( ($SearchStandard==1 && ($SearchStandardDetail==5 || $SearchStandardDetail==6 || $SearchStandardDetail==7)) || 
                            $SearchStandard==2 || $SearchStandard==3 ||
                           ($SearchStandard==4 && $SearchStandardDetail < 5) || 
                           ($SearchStandard==4 && $SearchStandardDetail==6) ) {
        $SearchDate = 3;
}
$Point_Level1 = 0;
$Point_Level2 = 0;
if ($SearchStandard==4 && $SearchStandardDetail==5 && $SearchDate=="") {
        $SearchDate = 1;
}
if ($SearchStandard==4 && $SearchStandardDetail==5 && $SearchDate==1) {  // 1000 포인트이상
        $Point_Level1 = 1000;
        $Point_Level2 = 1000000;
} else if ($SearchStandard==4 && $SearchStandardDetail==5 && $SearchDate==2) {  // 1000 ~ 500 포인트이상
        $Point_Level1 = 500;
        $Point_Level2 = 1000;
} else if ($SearchStandard==4 && $SearchStandardDetail==5 && $SearchDate==3) {  // 400 ~ 500 포인트이상
        $Point_Level1 = 400;
        $Point_Level2 = 500;
} else if ($SearchStandard==4 && $SearchStandardDetail==5 && $SearchDate==4) {  // 400 ~ 500 포인트이상
        $Point_Level1 = 300;
        $Point_Level2 = 400;
} else if ($SearchStandard==4 && $SearchStandardDetail==5 && $SearchDate==5) {  // 300 ~ 400 포인트이상
        $Point_Level1 = 200;
        $Point_Level2 = 300;
} else if ($SearchStandard==4 && $SearchStandardDetail==5 && $SearchDate==6) {  // 200 ~ 300 포인트이상
        $Point_Level1 = 100;
        $Point_Level2 = 200;
} else if ($SearchStandard==4 && $SearchStandardDetail==5 && $SearchDate==7) {  // 1200 포인트 이하
        $Point_Level1 = 1;
        $Point_Level2 = 100;
}
#--------------------------------------------------------------------------------------------------------------------#
if ($SearchStartYear==""){
        $SearchStartYear  = date("Y");
}
if ($SearchStartMonth==""){
        $SearchStartMonth = date("m");
}
if ($SearchStartDay==""){
        $SearchStartDay  = 1; //date("d");
}
#--------------------------------------------------------------------------------------------------------------------#
if ($SearchDate==3) {    // 월별
        $SearchStartDay   = 1;
}
#--------------------------------------------------------------------------------------------------------------------#
if ($SearchEndYear==""){
        $SearchEndYear  = date("Y");
}
if ($SearchEndMonth==""){
        $SearchEndMonth = date("m");
}
if ($SearchEndDay==""){
        $SearchEndDay   = date("d");
}
if ($SearchDate==1) {    // 일별
        $SearchStartMonth = $SearchEndMonth;
        $SearchStartDay   = 1;
}
#--------------------------------------------------------------------------------------------------------------------#
// 서치폼 감추기
$HideSearchForm = 0;
$HideStartMonth = 0;
$HideStartDay   = 0;
$HideEndYear    = 0;
$HideEndMonth   = 0;
$HideEndDay     = 0;
$HideClassCntSW = 1;
if ($SearchStandard==4 || ($SearchStandard==3 && $SearchStandardDetail > 2)) {
       $HideSearchForm = 1;
}  
if ($SearchStandard==3 && $SearchStandardDetail==5) {
       $HideClassCntSW = 0;
}
#--------------------------------------------------------------------------------------------------------------------#
if ($SearchDate==3) {    // 월별
        if (($SearchStandard==4 && $SearchStandardDetail==9) || $SearchStandard=="3") {    //본사 > 연간 신규생 비교, 강사    
		} else {
				$SearchEndYear  = $SearchStartYear;
        } 
        if (!$SearchEndMonth) {
                $SearchEndMonth = date("n");
        }
        if (!$SearchEndDay) {
                $SearchEndDay   = 31;
        }
        $HideStartMonth = 1;
        $HideStartDay   = 1;
        $HideEndYear    = 1;
        $HideEndDay     = 1;
        if ( ($SearchStandard==1 && $SearchStandardDetail < 5) || 
             ($SearchStandard==2 && $SearchStandardDetail < 5) || 
             ($SearchStandard==4 && $SearchStandardDetail < 5) ) {
              $HideStartMonth = 0;

        } else if($SearchStandard=="3") {  // 강사
              $HideStartMonth = 0;
              $HideEndYear    = 0;
		}

}
if ($SearchStandard==4 && $SearchStandardDetail>=5) {
        $HideStartMonth = 1;
        $HideStartDay   = 1;
        $HideEndYear    = 1;
        $HideEndMonth   = 1;
        $HideEndDay     = 1;
        if ($SearchStandardDetail==9) {
             $HideEndYear    = 0;
        }
}
#--------------------------------------------------------------------------------------------------------------------#
$SearchStartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth,-2) . "-" . substr("0".$SearchStartDay,-2);
$SearchEndDate   = $SearchEndYear . "-" . substr("0".$SearchEndMonth,-2) . "-" . substr("0".$SearchEndDay,-2);
if ($SearchDate==3) {    // 월별
        $SearchStartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth,-2);
        $SearchEndDate   = $SearchEndYear . "-" . substr("0".$SearchEndMonth,-2);
}
#--------------------------------------------------------------------------------------------------------------------#
$Search_Parameter = "";
#--------------------------------------------------------------------------------------------------------------------#
// 기관 (Standard) 에 따른 컨텐츠 정의 및 Sql 정의
$ArrSearchStandard   = "";
$idx = 0;
/*
$SearchStandard      = "";
$SearchStandard_Name = "";
#--------------------------------------------------------------------------------------------------------------------#
if($_ADMIN_LEVEL_ID_==12 || $_ADMIN_LEVEL_ID_==13) {        // 학원(대리점) 
#--------------------------------------------------------------------------------------------------------------------#
        $SearchStandard = "1";
        $SearchStandard_Name = "학원(대리점)";
#--------------------------------------------------------------------------------------------------------------------#
} else if($_ADMIN_LEVEL_ID_==9 || $_ADMIN_LEVEL_ID_==10) {  // 지사
#--------------------------------------------------------------------------------------------------------------------#
        $SearchStandard = "2";
        $SearchStandard_Name = "지사";
#--------------------------------------------------------------------------------------------------------------------#
} else if($_ADMIN_LEVEL_ID_==15) {                          // 강사
#--------------------------------------------------------------------------------------------------------------------#
        $SearchStandard = "3";
        $SearchStandard_Name = "강사";
#--------------------------------------------------------------------------------------------------------------------#
} else if($_ADMIN_LEVEL_ID_==0 || $_ADMIN_LEVEL_ID_==1) {   // 본사
#--------------------------------------------------------------------------------------------------------------------#
        $SearchStandard = "4";
        $SearchStandard_Name = "본사";
#--------------------------------------------------------------------------------------------------------------------#
}
*/
#--------------------------------------------------------------------------------------------------------------------#
if($SearchStandard=="1") {                           // 학원(대리점) 
#--------------------------------------------------------------------------------------------------------------------#
        $ArrSearchStandardDetail = array(
                1=> "1.수강생",
                2=> "2.신규&탈락생",
                3=> "3.수업수",
                4=> "4.수강료",
                5=> "5.학원별 수강생 등수",
                6=> "6.학원별 수수료 등수",
                7=> "7.레슨&퀴즈 비율"
        );
        $Sql_StandardDetailSub   = "select * from Centers A where A.CenterState=1 order by A.CenterState asc, A.CenterName asc";
        $StandardDetailSubID     = "CenterID";
        $StandardDetailSubName   = "CenterName";
        $StandardDetailPlaceName = "학원(대리점)선택";
#--------------------------------------------------------------------------------------------------------------------#
} else if($SearchStandard=="2") {                    // 지사 
#--------------------------------------------------------------------------------------------------------------------#
        $ArrSearchStandardDetail = array(
                1=> "1.월별 매출",
                2=> "2.월별 커미션",
                3=> "3.월별 수강생",
                4=> "4.월별 신규&탈락생",
                5=> "5.월별 지사별 수강생 등수",
                6=> "6.월별 지사별 수수료 등수",
                7=> "7.월별 SLP 수강생 등수",
                8=> "8.월별 SLP 매출 등수",
                9=> "9.월별 SLP 수업수",
               10=> "10.월별 지사 수업수"
        );
        $Sql_StandardDetailSub   = "select * from Branches A where A.BranchState=1";
        $StandardDetailSubID     = "BranchID";
        $StandardDetailSubName   = "BranchName";
        $StandardDetailPlaceName = "지사선택";
#--------------------------------------------------------------------------------------------------------------------#
} else if($SearchStandard=="3") {                    // 강사
#--------------------------------------------------------------------------------------------------------------------#
        $ArrSearchStandardDetail = array(
                1=> "1.월별 수강생수",
                2=> "2.월별 탈락률",
                3=> "3.월간 강사별 탈락률",
                4=> "4.월간 강사별 만족도",
                5=> "5.월간 강사별 수업시수"
        );
        $Sql_StandardDetailSub   = "select * from Teachers A where A.TeacherState=1";
        $StandardDetailSubID     = "TeacherID";
        $StandardDetailSubName   = "TeacherName";
        $StandardDetailPlaceName = "강사선택";
#--------------------------------------------------------------------------------------------------------------------#
} else if($SearchStandard=="4") {                   // 본사
#--------------------------------------------------------------------------------------------------------------------#
        $ArrSearchStandardDetail = array(
                1=> "1.전체 등록생",
                2=> "2.전체 탈락생",
                3=> "3.전체 휴원생수",
                4=> "4.월별 신규생&탈락생",
                5=> "5.월간 포인트 학생 등수",
                6=> "6.월간 수익성 추이", 
                7=> "7.B2C 월간신규(결제기준)",
                8=> "8.B2B 월간신규(수업배정)",
                9=> "9.연간신규생비교" 
        );
        $Sql_StandardDetailSub   = "select * from Teachers A where A.TeacherState=1";
        $StandardDetailSubID     = "TeacherID";
        $StandardDetailSubName   = "TeacherName";
        $StandardDetailPlaceName = "강사선택";
#--------------------------------------------------------------------------------------------------------------------#
}
#--------------------------------------------------------------------------------------------------------------------#
$ArrTotalResultCount   = [];
$ArrTotalSubResultCont = [];
$imsiArrTotalResultCount = [];
$category_disname      = "";
$CurrYear = date('Y');
$Search_Parameter = $SearchStandard . "-" . $SearchStandardDetail;
?>
<div id="page_content">
    <div id="page_content_inner">
        <h3 class="heading_b uk-margin-bottom"><?=$통계그래프[$LangID]?>
        <?
        if($SearchStandard=="1" and $SearchStandardDetail=="7") {
             //echo $ArrTotalSubResultCont["Lesson"] ." / ". $ArrTotalSubResultCont["Quiz"];
        }
        ?> 
        </h3>

        <form name="SearchForm" method="get">
        <input type="hidden" id="OldSearchStandard" name="OldSearchStandard" value="<?=$SearchStandard?>">
        <div class="md-card" style="margin-bottom:10px;">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin="">

                    <div class="uk-width-medium-2-10" style="padding-top:7px;">
                        <select id="SearchStandard" name="SearchStandard" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$통계대상선택[$LangID]?>" style="width:100%;"/>
                            <option value=""></option> 
                            <option value="1" <?if ($SearchStandard=="1"){?>selected<?}?>><?=$학원_대리점[$LangID]?></option>
                            <option value="2" <?if ($SearchStandard=="2"){?>selected<?}?>><?=$지사[$LangID]?></option>
                            <option value="3" <?if ($SearchStandard=="3"){?>selected<?}?>><?=$강사[$LangID]?></option>
                            <option value="4" <?if ($SearchStandard=="4"){?>selected<?}?>><?=$본사[$LangID]?></option>
                        </select>
                    </div>


                    <div class="uk-width-medium-2-10" style="padding-top:7px;">
                        <select id="SearchStandardDetail" name="SearchStandardDetail" onchange="SearchSubmit()" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="<?=$표시내용선택[$LangID]?>" style="width:100%;"/>
                            <option value=""></option>
                            <? for($i=1; $i<= count($ArrSearchStandardDetail);$i++) {?>
                            <option value="<?=$i?>" <?if ($SearchStandardDetail==$i){?>selected<?}?>><?=$ArrSearchStandardDetail[$i]?></option>
                            <?}?>
                        </select>
                    </div>

                    <div class="uk-width-medium-2-10" style="padding-top:7px; display:<?if($HideSearchForm==1) { ?> none<? } ?>">
                        <select id="SearchStandardDetailSub" name="SearchStandardDetailSub" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$StandardDetailPlaceName?>" style="width:100%;"/>
                            <option value=""></option>
                                <?
                                $categoryname_find = "";
                                if($Sql_StandardDetailSub) {
                                    $Stmt_StandardDetailSub = $DbConn->prepare($Sql_StandardDetailSub);
                                    $Stmt_StandardDetailSub->execute();
                                    $Stmt_StandardDetailSub->setFetchMode(PDO::FETCH_ASSOC);
                                    while($Row_StandardDetailSub = $Stmt_StandardDetailSub->fetch() ) {
                                        $StandardDetailSubMenuName = $Row_StandardDetailSub[$StandardDetailSubName];
                                        $StandardDetailSubMenuID   = $Row_StandardDetailSub[$StandardDetailSubID];
                                        ?>
                                    <option value="<?=$StandardDetailSubMenuID?>" <?if($SearchStandardDetailSub==$StandardDetailSubMenuID) {?> selected <?}?> ><?=$StandardDetailSubMenuName?>(<?=$StandardDetailSubMenuID?>)</option>
                                        <?php
                                        if (!$categoryname_find and $SearchStandardDetailSub==$StandardDetailSubMenuID) {
                                              $categoryname_find = $StandardDetailSubMenuName;
                                        }
                                    } 
                                }
                                if ($categoryname_find) { 
                                       $category_disname .= " ＞ " . $categoryname_find;
                                } else {
                                       $category_disname .= " ＞ 전체";
                                }
                                ?>
                        </select>
                    </div>


                    <div class="uk-width-medium-2-10" style="padding-top:7px;">
                        <select id="SearchDate" name="SearchDate" class="uk-width-1-1" onchange="SearchDateChange()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$기간별선택[$LangID]?>" style="width:100%;"/>
                            <option value=""></option>
                            <?
                            if ($SearchStandard==2 || $SearchStandard==3 || ($SearchStandard==4 and $SearchStandardDetail!=5)) {
                            ?>
                            <option value="3" <?if ($SearchDate==3){?>selected<?}?>><?=$월별[$LangID]?></option>
                            <?
                            } else if ($SearchStandard==4 and $SearchStandardDetail==5) {  // 본사 > 전체학생포인트 순위
                            ?>
                            <option value="1" <?if ($SearchDate==1){?>selected<?}?>>1000 포인트 이상</option>
                            <option value="2" <?if ($SearchDate==2){?>selected<?}?>>500~1000 포인트</option>
                            <option value="3" <?if ($SearchDate==3){?>selected<?}?>>400~500 포인트</option>
                            <option value="4" <?if ($SearchDate==4){?>selected<?}?>>300~400 포인트</option>
                            <option value="5" <?if ($SearchDate==5){?>selected<?}?>>200~300 포인트</option>
                            <option value="6" <?if ($SearchDate==6){?>selected<?}?>>100~200 포인트</option>
                            <option value="7" <?if ($SearchDate==7){?>selected<?}?>>100 포인트 이하</option>
                            <?
                            } else {
                            ?>
                            <option value="1" <?if ($SearchDate==1){?>selected<?}?>><?=$일별[$LangID]?></option>
                            <option value="3" <?if ($SearchDate==3){?>selected<?}?>><?=$월별[$LangID]?></option>
                            <?
                            } 
                            ?>
                        </select>
                    </div>
 
                    <div class="uk-width-medium-1-1" style="margin:0px; padding:0px; height:0px;"></div>

                    <div class="uk-width-medium-2-10" style="padding-top:7px;">
                        <select id="SearchStartYear" name="SearchStartYear" class="uk-width-1-1" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%; height:40px; border:1px solid #e0e0e0; padding-left:8px;"/>
                            <option value=""><?=$년도선택[$LangID]?></option>
                            <?
                            for ($iiii=2019;$iiii<=$CurrYear;$iiii++) {
                            ?>
                            <option value="<?=$iiii?>" <?if ($SearchStartYear==$iiii){?>selected<?}?>><?=$iiii?><?=$년[$LangID]?></option>
                            <?
                            }
                            ?>
                        </select>
                    </div>
                    <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideStartMonth==1,"none","")?>;">
                        <select id="SearchStartMonth" name="SearchStartMonth" class="uk-width-1-1" onchange="ChSearchStartMonth(1, this.value);" data-placeholder="<?=$월선택[$LangID]?>"  style="width:100%; height:40px; border:1px solid #e0e0e0; padding-left:8px;"/>
                            <option value=""><?=$월선택[$LangID]?></option>
                            <?
                            for ($iiii=1;$iiii<=12;$iiii++) {
                            ?>
                            <option value="<?=$iiii?>" <?if ($SearchStartMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
                            <?
                            }
                            ?>
                        </select>
                    </div>
                    <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideStartDay==1,"none","")?>;">
                        <select id="SearchStartDay" name="SearchStartDay" onchange="SearchSubmit()" class="uk-width-1-1" data-placeholder="<?=$일선택[$LangID]?>"  style="width:100%; height:40px; border:1px solid #e0e0e0; padding-left:8px;"/>
                            <option value=""><?=$일선택[$LangID]?></option>
                        </select>
                    </div>

                    <span style="padding-top: 15px; ">~</span>
                    
                    <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideEndYear==1,"none","")?>;">
                        <select id="SearchEndYear" name="SearchEndYear" class="uk-width-1-1" data-placeholder="<?=$년도선택[$LangID]?>"  style="width:100%; height:40px; border:1px solid #e0e0e0; padding-left:8px;"/>
                            <option value=""><?=$년도선택[$LangID]?></option>
                            <?
                            for ($iiii=2019;$iiii<=$CurrYear;$iiii++) {
                            ?>
                            <option value="<?=$iiii?>" <?if ($SearchEndYear==$iiii){?>selected<?}?>><?=$iiii?><?=$년[$LangID]?></option>
                            <?
                            }
                            ?>
                        </select>
                    </div>
                    <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideEndMonth==1,"none","")?>;">
                        <select id="SearchEndMonth" name="SearchEndMonth" class="uk-width-1-1" onchange="ChSearchStartMonth(2, this.value);" data-placeholder="<?=$월선택[$LangID]?>"  style="width:100%; height:40px; border:1px solid #e0e0e0; padding-left:8px;"/>
                            <option value=""><?=$월선택[$LangID]?></option>
                            <?
                            for ($iiii=1;$iiii<=12;$iiii++) {
                            ?>
                            <option value="<?=$iiii?>" <?if ($SearchEndMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
                            <?
                            }
                            ?>
                        </select>
                    </div>
                    <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideEndDay==1,"none","")?>;">
                        <select id="SearchEndDay" name="SearchEndDay" onchange="SearchSubmit()" class="uk-width-1-1" data-placeholder="<?=$일선택[$LangID]?>"  style="width:100%; height:40px; border:1px solid #e0e0e0; padding-left:8px;"/>
                            <option value=""><?=$일선택[$LangID]?></option>
                        </select>
                    </div>

                    <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideClassCntSW==1,"none","")?>;">
                        <select id="SearchClassCntSW" name="SearchClassCntSW" onchange="SearchSubmit()" class="uk-width-1-1" data-placeholder="<?=$일선택[$LangID]?>"  style="width:100%; height:40px; border:1px solid #e0e0e0; padding-left:8px;"/>
                            <option value="1" <?if ($SearchClassCntSW==1){?>selected<?}?>><?=$시수[$LangID]?></option>
                            <option value="2" <?if ($SearchClassCntSW==2){?>selected<?}?>><?=$슬럿[$LangID]?></option>
                        </select>
                    </div>
                    <? 
                    $categorydate_find = "";
                    if ($SearchStartYear) {
                          
                          $categorydate_find .= $SearchStartYear;
                    
                    }
                    if ($SearchStartMonth) {
                    
                          $categorydate_find .= "-" . $SearchStartMonth;
                    
                    }

                    if ($SearchDate==3) {    // 월별
                          
                          $categorydate_find .= " ~ " . $SearchEndMonth;

                    } else {   
                          if ($SearchStartDay) {
                                 $categorydate_find .= "-" . $SearchStartDay;
                          }
                          if ($SearchEndYear) {
                                 $categorydate_find .= " ~ " . $SearchEndYear;
                          }
                          if ($SearchEndMonth) {
                                 $categorydate_find .= "-" . $SearchEndMonth;
                          }
                          if ($SearchEndDay) {
                                 $categorydate_find .= "-" . $SearchEndDay;
                          }

                    }
                    if ($categorydate_find) { 
                          $category_disname .= " ＞ " . $categorydate_find;
                    }
                    ?>
                    <div class="uk-width-medium-1-10" style="padding-top:7px;">
                        <a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <div id="loading_dis" style="display:none; width:100%; padding:50px; text-align:center;"></div>

<?php
#====================================================================================================================#
#------------------------------------------------- 학원(대리점) --------------------------------------------------------#
#====================================================================================================================#
if($SearchStandard=="1") { 
#====================================================================================================================#
      if($SearchStandardDetail=="1") {                // 월별 수강생(일,주,월,분기,반기,연간)
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "학원(대리점) ＞ 일별 수강생";
            #--------------------------------------------------------------------------------------------------------#
            $Sql = "select count(*) as TotalResultCount,
                           date_format(B.ClassOrderRegDateTime, '%Y-%m-%d') as CurrentDate
                        from Members A 
                  inner join ClassOrders B on A.MemberID=B.MemberID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 " . 
                              iif($SearchStandardDetailSub > 0, " and A.CenterID=$SearchStandardDetailSub","") . " and 
                              B.ClassOrderState=1 and B.ClassProgress=11 and 
                              date_format(B.ClassOrderRegDateTime, '%Y-%m-%d') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by date_format(B.ClassOrderRegDateTime, '%Y-%m-%d')
                        order by B.ClassOrderRegDateTime asc";
            #--------------------------------------------------------------------------------------------------------#
            if ($SearchDate==3) {    // 월별
            #--------------------------------------------------------------------------------------------------------#
                    $category_disname = "학원(대리점) ＞ 월별 수강생";
                    #------------------------------------------------------------------------------------------------#
                    $Sql = "select count(*) as TotalResultCount,
                                   date_format(B.ClassOrderRegDateTime, '%Y-%m') as CurrentDate
                                from Members A 
                          inner join ClassOrders B on A.MemberID=B.MemberID
                                where A.MemberLevelID=19 and 
                                      A.MemberState=1 " . 
                                      iif($SearchStandardDetailSub > 0, " and A.CenterID=$SearchStandardDetailSub","") . " and 
                                      B.ClassOrderState=1 and B.ClassProgress=11 and 
                                      date_format(B.ClassOrderRegDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                                group by date_format(B.ClassOrderRegDateTime, '%Y-%m') 
                                order by B.ClassOrderRegDateTime asc";
            #--------------------------------------------------------------------------------------------------------#
            }
            #--------------------------------------------------------------------------------------------------------#
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);

            while($Row = $Stmt->fetch()) {
                
                $ArrCurrentDate = $Row["CurrentDate"];
                $ArrTotalResultCount[$ArrCurrentDate] = $Row["TotalResultCount"];
                
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="2") {         // 월별 신규샐 및 탈락생 (꺽은선은 신규-탈락=가감)
      #--------------------------------------------------------------------------------------------------------------#      
            $category_disname = "학원(대리점) ＞ 일별 신규샐 및 탈락생";
            #--------------------------------------------------------------------------------------------------------#
            # 신규생
            #--------------------------------------------------------------------------------------------------------#
            $Sql = "select ifnull(count(*), 0) as TotalResultCountReg,
                           date_format(B.ClassOrderRegDateTime, '%Y-%m-%d') as CurrentDate
                        from Members A 
                  inner join ClassOrders B on A.MemberID=B.MemberID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 " . 
                              iif($SearchStandardDetailSub > 0, " and A.CenterID=$SearchStandardDetailSub","") . " and 
                              B.ClassOrderState=1 and B.ClassProgress=11 and 
                              date_format(B.ClassOrderRegDateTime, '%Y-%m-%d') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by date_format(B.ClassOrderRegDateTime, '%Y-%m-%d')
                        order by B.ClassOrderRegDateTime asc";
            #--------------------------------------------------------------------------------------------------------#
            if ($SearchDate==3) {    // 월별
            #--------------------------------------------------------------------------------------------------------#
                    $Sql = "select ifnull(count(*), 0) as TotalResultCountReg,
                                   date_format(B.ClassOrderRegDateTime, '%Y-%m') as CurrentDate
                                from Members A 
                          inner join ClassOrders B on A.MemberID=B.MemberID
                                where A.MemberLevelID=19 and 
                                      A.MemberState=1 " . 
                                      iif($SearchStandardDetailSub > 0, " and A.CenterID=$SearchStandardDetailSub","") . " and 
                                      B.ClassOrderState=1 and B.ClassProgress=11 and 
                                      date_format(B.ClassOrderRegDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                                group by date_format(B.ClassOrderRegDateTime, '%Y-%m')
                                order by B.ClassOrderRegDateTime asc";
            #--------------------------------------------------------------------------------------------------------#
            }
            #--------------------------------------------------------------------------------------------------------#
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            $idx = 0;
            while($Row = $Stmt->fetch()) {
                  $CurrentDate = $Row["CurrentDate"];
                  $ArrTotalResultCount[$CurrentDate]["신규"] = $Row["TotalResultCountReg"];
            }
            #--------------------------------------------------------------------------------------------------------#
            # 탈락생
            #--------------------------------------------------------------------------------------------------------#
            $Sql = "select ifnull(count(*), 0) as TotalResultCountDrawal,
                           date_format(B.ClassOrderModiDateTime, '%Y-%m-%d') as CurrentDate
                        from Members A 
                  inner join ClassOrders B on A.MemberID=B.MemberID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 " . 
                              iif($SearchStandardDetailSub > 0, " and A.CenterID=$SearchStandardDetailSub","") . " and 
                              B.ClassOrderState=3 and 
                              date_format(B.ClassOrderModiDateTime, '%Y-%m-%d') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by date_format(B.ClassOrderModiDateTime, '%Y-%m-%d')
                        order by B.ClassOrderModiDateTime asc";
            #--------------------------------------------------------------------------------------------------------#
            if ($SearchDate==3) {    // 월별
            #--------------------------------------------------------------------------------------------------------#
                    $Sql = "select ifnull(count(*), 0) as TotalResultCountDrawal,
                                   date_format(B.ClassOrderModiDateTime, '%Y-%m') as CurrentDate
                                from Members A 
                          inner join ClassOrders B on A.MemberID=B.MemberID
                                where A.MemberLevelID=19 and 
                                      A.MemberState=1 " . 
                                      iif($SearchStandardDetailSub > 0, " and A.CenterID=$SearchStandardDetailSub","") . " and 
                                      B.ClassOrderState=3 and 
                                      date_format(B.ClassOrderModiDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                                group by date_format(B.ClassOrderModiDateTime, '%Y-%m')
                                order by B.ClassOrderModiDateTime asc";
            #--------------------------------------------------------------------------------------------------------#
            }
            #--------------------------------------------------------------------------------------------------------#
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            $idx = 0;
            while($Row = $Stmt->fetch()) {
                  $CurrentDate = $Row["CurrentDate"];
                  $ArrTotalResultCount[$CurrentDate]["탈락"] = $Row["TotalResultCountDrawal"];
                  if (isset($ArrTotalResultCount[$CurrentDate]["신규"])) {
                       $ArrTotalResultCount[$CurrentDate]["quantity"] = $ArrTotalResultCount[$CurrentDate]["신규"] - $Row["TotalResultCountDrawal"];
                  } else {
                       $ArrTotalResultCount[$CurrentDate]["quantity"] = 0 - $Row["TotalResultCountDrawal"];
                  }
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="3") {         // 월별 수업수(일,주,월,분기,반기,연간)
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "학원(대리점) ＞ 일별 수업수";
            #--------------------------------------------------------------------------------------------------------#
            $Sql = "select count(*) as TotalResultCount,
                           date_format(B.StartDateTime, '%Y-%m-%d') as CurrentDate
                        from Members A 
                  inner join Classes B on A.MemberID=B.MemberID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 " . 
                              iif($SearchStandardDetailSub > 0, " and A.CenterID=$SearchStandardDetailSub","") . " and  
                              date_format(B.StartDateTime, '%Y-%m-%d') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."' 
                        group by date_format(B.StartDateTime, '%Y-%m-%d') 
                        order by B.StartDateTime asc";
            #--------------------------------------------------------------------------------------------------------#
            if ($SearchDate==3) {    // 월별
            #--------------------------------------------------------------------------------------------------------#
                    $category_disname = "학원(대리점) ＞ 월별 수업수";
                    #------------------------------------------------------------------------------------------------#
                    $Sql = "select count(*) as TotalResultCount,
                                   date_format(B.StartDateTime, '%Y-%m') as CurrentDate
                                from Members A 
                          inner join Classes B on A.MemberID=B.MemberID
                                where A.MemberLevelID=19 and 
                                      A.MemberState=1 " . 
                                      iif($SearchStandardDetailSub > 0, " and A.CenterID=$SearchStandardDetailSub","") . " and  
                                      date_format(B.StartDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."' 
                                group by date_format(B.StartDateTime, '%Y-%m') 
                                order by B.StartDateTime asc";
            #--------------------------------------------------------------------------------------------------------#
            }
            #--------------------------------------------------------------------------------------------------------#
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CurrentDate = $Row["CurrentDate"];
                   $ArrTotalResultCount[$CurrentDate] = $Row["TotalResultCount"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="4") {         // 월별 수강료
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "학원(대리점) ＞ 일별 수강료";
            #--------------------------------------------------------------------------------------------------------#
            //B.ClassOrderPayPaymentPrice
            $Sql = "select sum(round(B.ClassOrderPayUseCashPrice-B.ClassOrderPayPgFeePrice-(B.ClassOrderPayUseCashPrice * B.ClassOrderPayPgFeeRatio / 100))) as TotalResultCount,
                           date_format(B.ClassOrderPayPaymentDateTime, '%Y-%m-%d') as CurrentDate
                      from Members A 
                inner join ClassOrderPays B on A.MemberID=B.ClassOrderPayPaymentMemberID
                     where A.MemberState=1 and 
                           B.ClassOrderPayProgress=21 " . 
                           iif($SearchStandardDetailSub > 0, " and A.CenterID=$SearchStandardDetailSub","") . " and 
                           date_format(B.ClassOrderPayPaymentDateTime, '%Y-%m-%d') BETWEEN '".$SearchStartDate."' and '".$SearchEndDate."' 
                  group by date_format(B.ClassOrderPayPaymentDateTime, '%Y-%m-%d') 
                  order by B.ClassOrderPayPaymentDateTime asc";
            #--------------------------------------------------------------------------------------------------------#
            if ($SearchDate==3) {    // 월별
            #--------------------------------------------------------------------------------------------------------#
                    $category_disname = "학원(대리점) ＞ 월별 수강료";
                    #------------------------------------------------------------------------------------------------#
                    $Sql = "select sum(round(B.ClassOrderPayUseCashPrice-B.ClassOrderPayPgFeePrice-(B.ClassOrderPayUseCashPrice * B.ClassOrderPayPgFeeRatio / 100))) as TotalResultCount,
                                   date_format(B.ClassOrderPayPaymentDateTime, '%Y-%m') as CurrentDate
                              from Members A 
                        inner join ClassOrderPays B on A.MemberID=B.ClassOrderPayPaymentMemberID
                             where A.MemberState=1 and 
                                   B.ClassOrderPayProgress=21 " . 
                                   iif($SearchStandardDetailSub > 0, " and A.CenterID=$SearchStandardDetailSub","") . " and 
                                   date_format(B.ClassOrderPayPaymentDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' and '".$SearchEndDate."' 
                          group by date_format(B.ClassOrderPayPaymentDateTime, '%Y-%m') 
                          order by B.ClassOrderPayPaymentDateTime asc";
            #--------------------------------------------------------------------------------------------------------#
            }
            #--------------------------------------------------------------------------------------------------------#
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CurrentDate = $Row["CurrentDate"];
                   $ArrTotalResultCount[$CurrentDate] = $Row["TotalResultCount"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="5") {         // 월별 학원별 수강생 등수
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "학원(대리점) ＞ 학원별 수강생 등수";
            #--------------------------------------------------------------------------------------------------------#
            # 수강생
            #--------------------------------------------------------------------------------------------------------#
            $Sql = "select count(*) as TotalResultCount,
                           C.CenterName as CenterName
                        from Members A 
                  inner join Centers C     on C.CenterID=A.CenterID
                  inner join ClassOrders B on A.MemberID=B.MemberID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 and  
                              B.ClassOrderState=1 and B.ClassProgress=11 and 
                              date_format(B.ClassOrderRegDateTime, '%Y-%m')='".$SearchEndDate."'
                        group by C.CenterName, date_format(B.ClassOrderRegDateTime, '%Y-%m') 
                        order by B.ClassOrderRegDateTime asc";

		echo $Sql;

            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CenterName = $Row["CenterName"];
                   $ArrTotalResultCount[$CenterName] = $Row["TotalResultCount"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="6") {         // 월별 학원별 수수료 등수
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "학원(대리점) ＞ 학원별 수수료 등수";
            #--------------------------------------------------------------------------------------------------------#
            $Sql = "select  sum(B.ClassOrderPayPaymentPrice) as TotalResultCount,
                            A.CenterName as CenterName
                        from Centers A 
                  inner join ClassOrderPays B on A.CenterID=B.CenterID
                       where B.ClassOrderPayProgress=21 and 
                             date_format(B.ClassOrderPayPaymentDateTime, '%Y-%m')='".$SearchEndDate."'
                    group by A.CenterName, date_format(B.ClassOrderPayPaymentDateTime, '%Y-%m')
                    order by B.ClassOrderPayPaymentDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CenterName = $Row["CenterName"];
                   $ArrTotalResultCount[$CenterName] = $Row["TotalResultCount"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="7") {         // 월별 레슨&퀴즈 비율
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "학원(대리점) ＞ 월별 레슨&퀴즈 비율";
            $Imsi_ArrTotalResultCount = [];
            $Hapge_Member = 0;
            $Hapge_Lesson = 0;
            $Hapge_Quiz   = 0;
            $Lesson_Ratio = 0;
            $Quiz_Ratio   = 0;
            #--------------------------------------------------------------------------------------------------------#
            # 레슨
            #--------------------------------------------------------------------------------------------------------#
            $Sql = "select  count(*) as TotalResultCount,
                            date_format(A.ClassVideoPlayLogDateTime, '%Y-%m') as CurrentDate
                        from ClassVideoPlayLogs A
                  inner join Classes B on A.ClassID=B.ClassID
                  inner join Members C on B.MemberID=C.MemberID 
                      where 1=1 " . 
                            iif($SearchStandardDetailSub > 0, " and C.CenterID=$SearchStandardDetailSub","") . " and 
                            date_format(A.ClassVideoPlayLogDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' and '".$SearchEndDate."' 
                        group by date_format(A.ClassVideoPlayLogDateTime, '%Y-%m')
                        order by A.ClassVideoPlayLogDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CurrentDate = $Row["CurrentDate"];
                   $Imsi_ArrTotalResultCount[$CurrentDate]["Lesson"] = $Row["TotalResultCount"];
            }
            #--------------------------------------------------------------------------------------------------------#
            # 퀴즈
            #--------------------------------------------------------------------------------------------------------#
            $Sql = "select  count(*) as TotalResultCount,
                            date_format(A.BookQuizResultEndDateTime, '%Y-%m') as CurrentDate
                        from BookQuizResults A
                  inner join Classes B on A.ClassID=B.ClassID
                  inner join Members C on B.MemberID=C.MemberID 
                       where
                            A.QuizStudyNumber=1 and A.BookQuizResultState=2 " . 
                            iif($SearchStandardDetailSub > 0, " and C.CenterID=$SearchStandardDetailSub","") . " and 
                            date_format(A.BookQuizResultEndDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' and '".$SearchEndDate."'
                        group by date_format(A.BookQuizResultEndDateTime, '%Y-%m')
                        order by A.BookQuizResultEndDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CurrentDate = $Row["CurrentDate"];
                   $Imsi_ArrTotalResultCount[$CurrentDate]["Quiz"] = $Row["TotalResultCount"];

                   $Total_Member = $Imsi_ArrTotalResultCount[$CurrentDate]["Lesson"] + $Imsi_ArrTotalResultCount[$CurrentDate]["Quiz"];
                   $Lesson_Ratio = round(($Imsi_ArrTotalResultCount[$CurrentDate]["Lesson"] / $Total_Member) * 100);
                   $Quiz_Ratio   = round(($Imsi_ArrTotalResultCount[$CurrentDate]["Quiz"] / $Total_Member) * 100);

                   $ArrTotalResultCount[$CurrentDate]["Lesson"] = $Lesson_Ratio;
                   $ArrTotalResultCount[$CurrentDate]["Quiz"] = $Quiz_Ratio;
                   
                   $Hapge_Member  = $Hapge_Member + $Total_Member;
                   $Hapge_Lesson  = $Hapge_Lesson + $Imsi_ArrTotalResultCount[$CurrentDate]["Lesson"];
                   $Hapge_Quiz    = $Hapge_Quiz   + $Imsi_ArrTotalResultCount[$CurrentDate]["Quiz"];

            }

            if ($Hapge_Member > 0 && $Hapge_Lesson > 0 && $Hapge_Quiz > 0) {
                  $Lesson_Ratio = round(($Hapge_Lesson / $Hapge_Member) * 100);
                  $Quiz_Ratio   = round(($Hapge_Quiz / $Hapge_Member) * 100);
            }

            $ArrTotalSubResultCont = array("Lesson" => $Lesson_Ratio, "Quiz" => $Quiz_Ratio);
      #--------------------------------------------------------------------------------------------------------------#
      }
#====================================================================================================================#
#----------------------------------------------------- 지사 ----------------------------------------------------------#
#====================================================================================================================#
} else if($SearchStandard=="2") {     
#====================================================================================================================#
      if($SearchStandardDetail=="1") {                // 월별 매출
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "지사 ＞ 월별 매출";
            #--------------------------------------------------------------------------------------------------------#
            $ViewTable = "select D.BranchID, 
                                 sum(round(A.ClassOrderPayUseCashPrice-A.ClassOrderPayPgFeePrice-(A.ClassOrderPayUseCashPrice * A.ClassOrderPayPgFeeRatio / 100))) as SumClassOrderPayUseCashPrice,
                                 date_format(A.ClassOrderPayDateTime, '%Y-%m') as CurrentDate
                        from ClassOrderPays A 
                            inner join Members B on A.ClassOrderPayPaymentMemberID=B.MemberID 
                            inner join Centers C on A.CenterID=C.CenterID 
                            inner join Branches D on C.BranchID=D.BranchID
                            inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
                            inner join Companies F on E.CompanyID=F.CompanyID 
                            inner join Franchises G on F.FranchiseID=G.FranchiseID 
                        where (A.ClassOrderPayProgress=21 or A.ClassOrderPayProgress=31 or A.ClassOrderPayProgress=41) " .
                               iif($SearchStandardDetailSub > 0, " and D.BranchID=$SearchStandardDetailSub","")  . " and 
                               date_format(A.ClassOrderPayDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' and '".$SearchEndDate."' 
                        group by date_format(A.ClassOrderPayDateTime, '%Y-%m')";

            $Sql = "select AA.SumClassOrderPayUseCashPrice,
                           AA.CurrentDate,
                           AA.BranchID 
                    from ($ViewTable) AA 
                        inner join Branches BB on AA.BranchID=BB.BranchID
                    order by AA.CurrentDate asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CurrentDate = $Row["CurrentDate"];
                   $ArrTotalResultCount[$CurrentDate] = $Row["SumClassOrderPayUseCashPrice"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="2") {         // 월별 커미션
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "지사 ＞ 월별 커미션";
            #--------------------------------------------------------------------------------------------------------#
            $ViewTable = "select D.BranchID, 
                                 round(sum(round((A.ClassOrderPayUseCashPrice-A.ClassOrderPayPgFeePrice-(A.ClassOrderPayUseCashPrice * A.ClassOrderPayPgFeeRatio / 100)) * ( (A.CenterPricePerTime - A.CompanyPricePerTime) / A.CenterPricePerTime ))) * 0.967) as SumClassOrderPayPgFeeRatio,
                                 date_format(A.ClassOrderPayDateTime, '%Y-%m') as CurrentDate
                        from ClassOrderPays A 
                            inner join Members B on A.ClassOrderPayPaymentMemberID=B.MemberID 
                            inner join Centers C on A.CenterID=C.CenterID 
                            inner join Branches D on C.BranchID=D.BranchID
                            inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
                            inner join Companies F on E.CompanyID=F.CompanyID 
                            inner join Franchises G on F.FranchiseID=G.FranchiseID 
                        where (A.ClassOrderPayProgress=21 or A.ClassOrderPayProgress=31 or A.ClassOrderPayProgress=41) " .
                               iif($SearchStandardDetailSub > 0, " and D.BranchID=$SearchStandardDetailSub","")  . " and 
                               date_format(A.ClassOrderPayDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' and '".$SearchEndDate."' 
                        group by date_format(A.ClassOrderPayDateTime, '%Y-%m')";

            $Sql = "select AA.SumClassOrderPayPgFeeRatio,
                           AA.CurrentDate,
                           AA.BranchID 
                    from ($ViewTable) AA 
                        inner join Branches BB on AA.BranchID=BB.BranchID
                    order by AA.CurrentDate asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CurrentDate = $Row["CurrentDate"];
                   $ArrTotalResultCount[$CurrentDate] = $Row["SumClassOrderPayPgFeeRatio"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="3") {         // 월별 신규생
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "지사 ＞ 월별 수강생";
            #------------------------------------------------------------------------------------------------#
            $Sql = "select count(*) as TotalResultCount,
                           date_format(B.ClassOrderRegDateTime, '%Y-%m') as CurrentDate
                        from Members A 
                  inner join ClassOrders B on A.MemberID=B.MemberID
                  inner join Centers     C on A.CenterID=C.CenterID 
                  inner join Branches    D on C.BranchID=D.BranchID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 " . 
                              iif($SearchStandardDetailSub > 0, " and D.BranchID=$SearchStandardDetailSub","") . " and 
                              B.ClassOrderState=1 and B.ClassProgress=11 and 
                              date_format(B.ClassOrderRegDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by date_format(B.ClassOrderRegDateTime, '%Y-%m') 
                        order by B.ClassOrderRegDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CurrentDate = $Row["CurrentDate"];
                   $ArrTotalResultCount[$CurrentDate] = $Row["TotalResultCount"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="4") {         // 월별 신규생&탈락생 (꺽은선은 신규-탈락=가감)
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "지사 ＞ 일별 신규샐 및 탈락생";
            #--------------------------------------------------------------------------------------------------------#
            # 신규생
            #--------------------------------------------------------------------------------------------------------#
            $Sql = "select ifnull(count(*), 0) as TotalResultCountReg,
                           date_format(B.ClassOrderRegDateTime, '%Y-%m') as CurrentDate
                        from Members A 
                  inner join ClassOrders B on A.MemberID=B.MemberID
                  inner join Centers     C on A.CenterID=C.CenterID 
                  inner join Branches    D on C.BranchID=D.BranchID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 " . 
                              iif($SearchStandardDetailSub > 0, " and D.BranchID=$SearchStandardDetailSub","") . " and 
                              B.ClassOrderState=1 and B.ClassProgress=11 and 
                              date_format(B.ClassOrderRegDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by date_format(B.ClassOrderRegDateTime, '%Y-%m')
                        order by B.ClassOrderRegDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            $idx = 0;
            while($Row = $Stmt->fetch()) {
                  $CurrentDate = $Row["CurrentDate"];
                  $ArrTotalResultCount[$CurrentDate]["신규"] = $Row["TotalResultCountReg"];
            }
            #--------------------------------------------------------------------------------------------------------#
            # 탈락생
            #--------------------------------------------------------------------------------------------------------#
            $Sql = "select ifnull(count(*), 0) as TotalResultCountDrawal,
                           date_format(B.ClassOrderModiDateTime, '%Y-%m') as CurrentDate
                        from Members A 
                  inner join ClassOrders B on A.MemberID=B.MemberID
                  inner join Centers     C on A.CenterID=C.CenterID 
                  inner join Branches    D on C.BranchID=D.BranchID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 " . 
                              iif($SearchStandardDetailSub > 0, " and D.BranchID=$SearchStandardDetailSub","") . " and 
                              B.ClassOrderState=3 and 
                              date_format(B.ClassOrderModiDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by date_format(B.ClassOrderModiDateTime, '%Y-%m')
                        order by B.ClassOrderModiDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            $idx = 0;
            while($Row = $Stmt->fetch()) {
                  $CurrentDate = $Row["CurrentDate"];
                  $ArrTotalResultCount[$CurrentDate]["탈락"] = $Row["TotalResultCountDrawal"];
                  if (isset($ArrTotalResultCount[$CurrentDate]["신규"])) {
                       $ArrTotalResultCount[$CurrentDate]["quantity"] = $ArrTotalResultCount[$CurrentDate]["신규"] - $Row["TotalResultCountDrawal"];
                  } else {
                       $ArrTotalResultCount[$CurrentDate]["quantity"] = 0 - $Row["TotalResultCountDrawal"];
                  }
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="5") {         // 월별 지사별 수강생 등수
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "지사 ＞ 지사별 수강생 등수";
            #--------------------------------------------------------------------------------------------------------#
            $Sql = "select count(*) as TotalResultCount,
                           D.BranchName as BranchName
                        from Members A 
                  inner join ClassOrders B on A.MemberID=B.MemberID
                  inner join Centers     C on C.CenterID=A.CenterID
                  inner join Branches    D on C.BranchID=D.BranchID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 and  
                              B.ClassOrderState=1 and B.ClassProgress=11 " . 
                              iif($SearchStandardDetailSub > 0, " and D.BranchID=$SearchStandardDetailSub","") . " and 
                              date_format(B.ClassOrderRegDateTime, '%Y-%m')='".$SearchEndDate."'
                        group by D.BranchName, date_format(B.ClassOrderRegDateTime, '%Y-%m') 
                        order by B.ClassOrderRegDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $BranchName = $Row["BranchName"];
                   $ArrTotalResultCount[$BranchName] = $Row["TotalResultCount"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="6") {         // 월별 지사별 수수료 등수
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "지사 ＞ 지사별 수수료 등수";
            #--------------------------------------------------------------------------------------------------------#
            $ViewTable = "select D.BranchID, 
                                 sum(A.ClassOrderPayPaymentPrice) as SumClassOrderPayUseCashPrice
                            from ClassOrderPays A 
                      inner join Members B on A.ClassOrderPayPaymentMemberID=B.MemberID 
                      inner join Centers C on A.CenterID=C.CenterID 
                      inner join Branches D on C.BranchID=D.BranchID
                      inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
                      inner join Companies F on E.CompanyID=F.CompanyID 
                      inner join Franchises G on F.FranchiseID=G.FranchiseID 
                           where date_format(A.ClassOrderPayDateTime, '%Y-%m')='".$SearchEndDate."' and
                                 (A.ClassOrderPayProgress=21 or A.ClassOrderPayProgress=31 or A.ClassOrderPayProgress=41) 
                        group by D.BranchID";

            $Sql = "select AA.BranchID,
                           AA.SumClassOrderPayUseCashPrice,
                           BB.BranchName
                     from ($ViewTable) AA 
               inner join Branches BB on AA.BranchID=BB.BranchID
                 order by AA.SumClassOrderPayUseCashPrice asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $BranchName = $Row["BranchName"];
                   $ArrTotalResultCount[$BranchName] = $Row["SumClassOrderPayUseCashPrice"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="7") {         // 월별 SLP 수강생등수 
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "지사 ＞ 월별 SLP 수강생 등수";
            #------------------------------------------------------------------------------------------------#
            $SLP_BranchID_0 = 42;   //gangseo
            $SLP_BranchID_1 = 107;  //seodaemoon
            $SLP_BranchID_2 = 113;  //slp
            $SLP_BranchID_3 = 114;  //soowon
            #------------------------------------------------------------------------------------------------#
            $Sql = "select count(*) as TotalResultCount,
                           C.CenterName as CenterName
                        from Members A 
                  inner join ClassOrders B on A.MemberID=B.MemberID
                  inner join Centers     C on C.CenterID=A.CenterID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 and  
                              B.ClassOrderState=1 and B.ClassProgress=11 and 
                             (C.BranchID=".$SLP_BranchID_0." or C.BranchID=".$SLP_BranchID_1." or C.BranchID=".$SLP_BranchID_2." or C.BranchID=".$SLP_BranchID_3.") and 
                              date_format(B.ClassOrderRegDateTime, '%Y-%m')='".$SearchEndDate."'
                        group by C.CenterName, date_format(B.ClassOrderRegDateTime, '%Y-%m') 
                        order by B.ClassOrderRegDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CenterName = $Row["CenterName"];
                   $ArrTotalResultCount[$CenterName] = $Row["TotalResultCount"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="8") {         // 월별 SLP 매출 등수 
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "지사 ＞ 월별 SLP 매출 등수";
            #------------------------------------------------------------------------------------------------#
            $SLP_BranchID_0 = 42;   //gangseo
            $SLP_BranchID_1 = 107;  //seodaemoon
            $SLP_BranchID_2 = 113;  //slp
            $SLP_BranchID_3 = 114;  //soowon
            #------------------------------------------------------------------------------------------------#
            $AddSqlWhere = "A.CenterState<>0 ";
            $AddSqlWhere = $AddSqlWhere . " and B.BranchState<>0 ";
            $AddSqlWhere = $AddSqlWhere . " and C.BranchGroupState<>0 ";
            $AddSqlWhere = $AddSqlWhere . " and D.CompanyState<>0 ";
            $AddSqlWhere = $AddSqlWhere . " and H.FranchiseState<>0 ";
            $AddSqlWhere = $AddSqlWhere . " and (E.OnlineSiteState<>0 or E.OnlineSiteState is null)";
            $AddSqlWhere = $AddSqlWhere . " and (F.ManagerState<>0 or F.ManagerState is null)";
            $AddSqlWhere = $AddSqlWhere . " and (A.BranchID=".$SLP_BranchID_0." or A.BranchID=".$SLP_BranchID_1." or A.BranchID=".$SLP_BranchID_2." or A.BranchID=".$SLP_BranchID_3.") ";
            #------------------------------------------------------------------------------------------------#
            $AddSqlWhere2 = "date_format(AAA.ClassOrderPayPaymentDateTime, '%Y-%m')='".$SearchEndDate."' and 
                             (AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41)";
            #------------------------------------------------------------------------------------------------#
            $ViewTable = "select A.CenterName as CenterName,
                                 (select sum(round(AAA.ClassOrderPayUseCashPrice-AAA.ClassOrderPayPgFeePrice-(AAA.ClassOrderPayUseCashPrice * AAA.ClassOrderPayPgFeeRatio / 100))) from ClassOrderPays AAA inner join Members BBB on AAA.ClassOrderPayPaymentMemberID=BBB.MemberID inner join Centers CCC on AAA.CenterID=CCC.CenterID where ".$AddSqlWhere2." and CCC.CenterID=A.CenterID) as TotalClassOrderPayUseCashPrice,
                                 (select sum((AAA.ClassOrderPayUseCashPrice-AAA.ClassOrderPayPgFeePrice-(AAA.ClassOrderPayUseCashPrice * AAA.ClassOrderPayPgFeeRatio / 100)) * ( (AAA.CenterPricePerTime - AAA.CompanyPricePerTime) / AAA.CenterPricePerTime )) * 0.967 from ClassOrderPays AAA inner join Members BBB on AAA.ClassOrderPayPaymentMemberID=BBB.MemberID inner join Centers CCC on AAA.CenterID=CCC.CenterID where ".$AddSqlWhere2." and CCC.CenterID=A.CenterID) as TotalBranchFee
                            from Centers A 
                      inner join Branches B on A.BranchID=B.BranchID 
                      inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
                      inner join Companies D on C.CompanyID=D.CompanyID 
                      left outer join OnlineSites E on A.OnlineSiteID=E.OnlineSiteID 
                      left outer join Managers F on A.ManagerID=F.ManagerID 
                      inner join Franchises H on D.FranchiseID=H.FranchiseID 
                      inner join Members G on A.CenterID=G.CenterID and G.MemberLevelID=12 
                           where ".$AddSqlWhere." ";
            
            $Sql = "select * from ($ViewTable) V 
                         order by V.TotalClassOrderPayUseCashPrice asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CenterName = $Row["CenterName"];
                   $ArrTotalResultCount[$CenterName] = $Row["TotalClassOrderPayUseCashPrice"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="9") {         // 월별 SLP 수업수
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "지사 ＞ 월별 SLP 수업수";
            #------------------------------------------------------------------------------------------------#
            $SLP_BranchID_0 = 42;   //gangseo
            $SLP_BranchID_1 = 107;  //seodaemoon
            $SLP_BranchID_2 = 113;  //slp
            $SLP_BranchID_3 = 114;  //soowon
            #------------------------------------------------------------------------------------------------#
            $Sql = "select count(*) as TotalResultCount, 
                           C.CenterName as CenterName
                    from Members A 
              inner join Classes      B on A.MemberID=B.MemberID
              inner join ClassOrders BB on B.ClassOrderID=BB.ClassOrderID
              inner join Centers      C on A.CenterID=C.CenterID 
              inner join Branches     D on C.BranchID=D.BranchID 
                   where A.MemberLevelID=19 and 
                         A.MemberState=1 and 
                        (D.BranchID=".$SLP_BranchID_0." or D.BranchID=".$SLP_BranchID_1." or D.BranchID=".$SLP_BranchID_2." or D.BranchID=".$SLP_BranchID_3.") and 
                         B.ClassState=2 and date_format(B.StartDateTime, '%Y-%m')='".$SearchEndDate."' and 
                        (B.ClassAttendState=1 or B.ClassAttendState=2 or B.ClassAttendState=3) and 
                         BB.ClassProductID=1 
                   group by C.CenterName, date_format(B.StartDateTime, '%Y-%m') 
                   order by B.StartDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CenterName = $Row["CenterName"];
                   $ArrTotalResultCount[$CenterName] = $Row["TotalResultCount"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="10") {         // 월별 지사별 수업수
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "지사 ＞ 월별 지사 수업수";
            #------------------------------------------------------------------------------------------------#
            $Sql = "select count(*) as TotalResultCount,
                           D.BranchName as BranchName
                        from Members A 
                  inner join Classes  B on A.MemberID=B.MemberID
                  inner join Centers  C on C.CenterID=A.CenterID
                  inner join Branches D on C.BranchID=D.BranchID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 and 
                              date_format(B.StartDateTime, '%Y-%m')='".$SearchEndDate."' 
                        group by D.BranchName, date_format(B.StartDateTime, '%Y-%m') 
                        order by B.StartDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $BranchName = $Row["BranchName"];
                   $ArrTotalResultCount[$BranchName] = $Row["TotalResultCount"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      }
#====================================================================================================================#
#----------------------------------------------------- 강사 ----------------------------------------------------------#
#====================================================================================================================#
} else if($SearchStandard=="3") {         
#====================================================================================================================#
      if($SearchStandardDetail=="1") {                 // 월별 수강생수
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "강사 ＞ 월별 수강생";
            #------------------------------------------------------------------------------------------------#
            $Sql = "select count(*) as TotalResultCount, 
                           date_format(B.ClassOrderPayPaymentDateTime, '%Y-%m') as CurrentDate
                     from ClassOrderPayDetails A 
               inner join ClassOrderPays B on A.ClassOrderPayID=B.ClassOrderPayID
                    where ".iif($SearchStandardDetailSub,"A.TeacherID=".$SearchStandardDetailSub." and ","")."
                          B.ClassOrderPayProgress=21 and 
                          date_format(B.ClassOrderPayPaymentDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                 group by date_format(B.ClassOrderPayPaymentDateTime, '%Y-%m')";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                    $CurrentDate = $Row["CurrentDate"];
                    $ArrTotalResultCount[$CurrentDate] = $Row["TotalResultCount"];
            }


            /*
            
            $Sql = "select A.TeacherName as TeacherName,
                           date_format(AAA.StartDateTime, '%Y-%m') as CurrentDate,
                         ifnull((select count(*) 
                                      from Members     MMM 
                                 left join Classes     AAA on AAA.MemberID=MMM.MemberID
                                 left join ClassOrders BBB on AAA.ClassOrderID=BBB.ClassOrderID and BBB.MemberID=MMM.MemberID
                                 where MMM.MemberLevelID=19 and 
                                       date_format(AAA.StartDateTime, '%Y-%m')='".$SearchEndDate."' and 
                                       AAA.TeacherID=A.TeacherID and 
                                      (AAA.ClassAttendState=1 or AAA.ClassAttendState=2 or AAA.ClassAttendState=3)),0) as TotalMemberCounter
                    from Teachers A 
              inner join Members  M on A.TeacherID=M.TeacherID and M.MemberLevelID=15 
                    where A.TeacherState=1
                    group by date_format(AAA.StartDateTime, '%Y-%m')
                    order by A.TeacherName asc "; 
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CurrentDate = $Row["CurrentDate"];
                   $ArrTotalResultCount[$CurrentDate] = $Row["TotalMemberCounter"];
            }
            */
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="2") {         // 월별 탈락률
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "강사 ＞ 월별 탈락률";
            #------------------------------------------------------------------------------------------------#
            $Sql = "select ifnull(count(*), 0) as TotalResultCountReg,
                           date_format(B.ClassOrderRegDateTime, '%Y-%m') as CurrentDate
                        from Members A 
                  inner join ClassOrders  B  on A.MemberID=B.MemberID
                  inner join Classes      C  on B.ClassOrderID=C.ClassOrderID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 and 
                              B.ClassOrderState=1 and 
                              B.ClassProgress=11".
                              iif($SearchStandardDetailSub," and C.TeacherID=".$SearchStandardDetailSub,"")." and 
                              date_format(B.ClassOrderRegDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by date_format(B.ClassOrderRegDateTime, '%Y-%m') 
                        order by B.ClassOrderRegDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                
                $CurrentDate = $Row["CurrentDate"];
                $ArrTotalResultCount[$CurrentDate]["신규"] = $Row["TotalResultCountReg"];
                
            }
            #------------------------------------------------------------------------------------------------#
            $Sql = "select ifnull(count(*), 0) as TotalResultCountDrawal,
                           date_format(B.ClassOrderRegDateTime, '%Y-%m') as CurrentDate
                        from Members A 
                  inner join ClassOrders  B  on A.MemberID=B.MemberID
                  inner join Classes      C  on B.ClassOrderID=C.ClassOrderID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 and 
                              B.ClassOrderState=3 ".
                              iif($SearchStandardDetailSub," and C.TeacherID=".$SearchStandardDetailSub,"")." and  
                              date_format(B.ClassOrderRegDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by date_format(B.ClassOrderRegDateTime, '%Y-%m')
                        order by B.ClassOrderRegDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            $idx = 0;
            while($Row = $Stmt->fetch()) {
                  $CurrentDate = $Row["CurrentDate"];
                  $ArrTotalResultCount[$CurrentDate]["탈락"] = $Row["TotalResultCountDrawal"];
                  $TotalResultCountDrawal_Ratio =  round(($Row["TotalResultCountDrawal"] / $ArrTotalResultCount[$CurrentDate]["신규"]) * 100);
                  $ArrTotalResultCount[$CurrentDate]["quantity"] = "" . $TotalResultCountDrawal_Ratio . "";
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="3") {         // 월간 강사별 탈락률
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "강사 ＞ 월별 강사별 탈락률";
            #------------------------------------------------------------------------------------------------#
            $imsi_ArrTotalResultCount = [];
            $Sql = "select ifnull(count(*), 0) as TotalResultCountReg,
                           T.TeacherName as TeacherName
                        from Members A 
                  inner join ClassOrders B on A.MemberID=B.MemberID
                  inner join Classes     C on B.ClassOrderID=C.ClassOrderID
                  inner join Teachers    T on C.TeacherID=T.TeacherID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 and 
                              B.ClassOrderState=1 and 
                              B.ClassProgress=11 and 
                              date_format(B.ClassOrderRegDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by C.TeacherID, date_format(B.ClassOrderRegDateTime, '%Y-%m') 
                        order by B.ClassOrderRegDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                
                $TeacherName = $Row["TeacherName"];
                //$ArrTotalResultCount[$TeacherName]["신규"] = $Row["TotalResultCountReg"];
                $imsiArrTotalResultCount[$TeacherName] = $Row["TotalResultCountReg"];
                
            }
            #------------------------------------------------------------------------------------------------#
            $Sql = "select ifnull(count(*), 0) as TotalResultCountDrawal,
                           T.TeacherName as TeacherName
                        from Members A 
                  inner join ClassOrders B on A.MemberID=B.MemberID
                  inner join Classes     C on B.ClassOrderID=C.ClassOrderID
                  inner join Teachers    T on C.TeacherID=T.TeacherID
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 and 
                              B.ClassOrderState=3 and  
                              date_format(B.ClassOrderRegDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by C.TeacherID, date_format(B.ClassOrderRegDateTime, '%Y-%m')
                        order by B.ClassOrderRegDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                  $TeacherName = $Row["TeacherName"];
                  //$ArrTotalResultCount[$TeacherName]["탈락"] = $Row["TotalResultCountDrawal"];
                  //$TotalResultCountDrawal_Ratio =  round(($Row["TotalResultCountDrawal"] / $ArrTotalResultCount[$TeacherName]["신규"]) * 100);

                  //$ArrTotalResultCount[$TeacherName]["quantity"] = $TotalResultCountDrawal_Ratio;
				  $TotalResultCountDrawal_Ratio = 0;
                  if ($imsiArrTotalResultCount[$TeacherName] > 0) {
                        $TotalResultCountDrawal_Ratio =  round(($Row["TotalResultCountDrawal"] / $imsiArrTotalResultCount[$TeacherName]) * 100);
				  }
                  $ArrTotalResultCount[$TeacherName] = iif($TotalResultCountDrawal_Ratio > 100,"-","") . $TotalResultCountDrawal_Ratio . "";

            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="4") {         // 월간 강사별 만족도
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "강사 ＞ 월별 강사별 만족도";
            #------------------------------------------------------------------------------------------------#
            $Sql = "select A.TeacherID   as TeacherID,
                           A.TeacherName as TeacherName
                      from Teachers A 
                  order by A.TeacherOrder desc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                
                $TeacherID   = $Row["TeacherID"];
                $TeacherName = $Row["TeacherName"];
                
                $Sql2 = "
                    select 
                        count(*) as TeacherClassCount
                    from ClassOrderPayDetails AA 
                        inner join ClassOrderPays AAA on AA.ClassOrderPayID=AAA.ClassOrderPayID 
                        inner join ClassOrders A on AA.ClassOrderID=A.ClassOrderID  
                        inner join Members B on A.MemberID=B.MemberID 
                    where 
                        (AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41)
                        and AA.ClassOrderPayDetailType=1 
                        and A.ClassOrderID in (select ClassOrderID from ClassOrderSlots where TeacherID=$TeacherID and ClassOrderSlotType=1) 
                        and A.ClassProgress=11";
                $Stmt2 = $DbConn->prepare($Sql2);
                $Stmt2->execute();
                $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                $Row2 = $Stmt2->fetch();
                $Stmt2 = null;
                $TeacherClassCount1 = $Row2["TeacherClassCount"];

                $Sql2 = "
                    select 
                        count(*) as TeacherClassCount
                    from ClassOrders AA 
                        inner join Members B on AA.MemberID=B.MemberID 
                    where 
                        AA.ClassOrderID in (select ClassOrderID from ClassOrderSlots where TeacherID=$TeacherID and ClassOrderSlotType=1) 
                        and AA.ClassProgress=11 
                        and ClassOrderID not in (select AAAAA.ClassOrderID from ClassOrderPayDetails AAAAA inner join ClassOrderPays BBBBB on AAAAA.ClassOrderPayID=BBBBB.ClassOrderPayID and BBBBB.ClassOrderPayProgress>=21) 
                        and ClassOrderID not in (select AAAAA.ClassOrderID from ClassOrderPayB2bs    AAAAA inner join ClassOrderPays BBBBB on AAAAA.ClassOrderPayID=BBBBB.ClassOrderPayID and BBBBB.ClassOrderPayProgress>=21)";
                $Stmt2 = $DbConn->prepare($Sql2);
                $Stmt2->execute();
                $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                $Row2 = $Stmt2->fetch();
                $Stmt2 = null;
                $TeacherClassCount1 = $TeacherClassCount1 + $Row2["TeacherClassCount"];

                $Sql2 = "
                    select 
                        count(*) as TeacherClassCount
                    from ClassOrderPayDetails AA 
                        inner join ClassOrderPays AAA on AA.ClassOrderPayID=AAA.ClassOrderPayID 
                        inner join ClassOrders A on AA.ClassOrderID=A.ClassOrderID  
                        inner join Members B on A.MemberID=B.MemberID 
                        inner join Centers C on B.CenterID=C.CenterID 
                    where 
                        (AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41) 
                        and AA.ClassOrderPayDetailType=2 
                        and A.ClassOrderID in (select ClassOrderID from ClassOrderSlots where TeacherID=$TeacherID and ClassOrderSlotType=1) 
                        and A.ClassProgress=11";
                $Stmt2 = $DbConn->prepare($Sql2);
                $Stmt2->execute();
                $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                $Row2 = $Stmt2->fetch();
                $Stmt2 = null;
                $TeacherClassCount2 = $Row2["TeacherClassCount"];

                //단체 연장
                $Sql2 = "
                    select 
                        count(*) as TeacherClassCount
                    from ClassOrderPayB2bs AA 
                        inner join ClassOrderPays AAA on AA.ClassOrderPayID=AAA.ClassOrderPayID 
                        inner join ClassOrders A on AA.ClassOrderID=A.ClassOrderID  
                        inner join Members B on A.MemberID=B.MemberID 
                        inner join Centers C on B.CenterID=C.CenterID 
                    where 
                        (AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41) 
                        and A.ClassOrderID in (select ClassOrderID from ClassOrderSlots where TeacherID=$TeacherID and ClassOrderSlotType=1) 
                        and A.ClassProgress=11 
                ";
                $Stmt2 = $DbConn->prepare($Sql2);
                $Stmt2->execute();
                $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                $Row2 = $Stmt2->fetch();
                $Stmt2 = null;
                $TeacherClassCount2 = $TeacherClassCount2 + $Row2["TeacherClassCount"];

                if ($TeacherClassCount1!=0){
                     $TeacherClassRelRatio = 100 * $TeacherClassCount2 / $TeacherClassCount1;
                     $ArrTotalResultCount[$TeacherName] = "" . round($TeacherClassRelRatio) . "";
                }

            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="5") {         // 월간 강사별 수업수
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "강사 ＞ 월별 강사별 수업수";
            #------------------------------------------------------------------------------------------------#
            $AddSqlWhere = "1=1";
            $AddSqlWhere = $AddSqlWhere . " and A.TeacherState<>0 ";
            $AddSqlWhere = $AddSqlWhere . " and B.TeacherGroupState<>0 ";
            $AddSqlWhere = $AddSqlWhere . " and C.EduCenterState<>0 ";
            $AddSqlWhere = $AddSqlWhere . " and D.FranchiseState<>0 ";
            #------------------------------------------------------------------------------------------------#
            $Sql = "select A.*
                    from Teachers A 
                        inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
                        inner join EduCenters C on B.EduCenterID=C.EduCenterID 
                        inner join Franchises D on C.FranchiseID=D.FranchiseID 
                        inner join Members G on A.TeacherID=G.TeacherID and G.MemberLevelID=15 
                        left outer join MemberTimeZones Z on G.MemberTimeZoneID=Z.MemberTimeZoneID 
                        left outer join TeacherPayTypeItems I on A.TeacherPayTypeItemID=I.TeacherPayTypeItemID
                    where ".$AddSqlWhere." 
                    order by A.TeacherOrder asc "; 
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->bindParam(':EncryptionKey', $EncryptionKey);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);

            while($Row = $Stmt->fetch()) {
                
                $TeacherID   = $Row["TeacherID"];
                $TeacherName = $Row["TeacherName"];
				?>
				<script>
				  var teacher_name = '▷▷▷▷▷ ' + '<?=$TeacherName?>' + ' 강사 자료 집계 중 입니다! 잠시만 기다려 주세요!▷▷▷▷▷';
				  document.getElementById('loading_dis').style.display = '';
				  document.getElementById('loading_dis').innerText = teacher_name;	
				</script>
				<?php
				$SearchYear  = $SearchStartYear;
                $SearchMonth = $SearchEndMonth; 

				$MonthEndDay = date('t', strtotime($SearchYear."-".substr("0".$SearchMonth,-2)."-01"));
				$TotalClassCount = 0;
				$TotalMinuteCount = 0;

				for ($ii=1;$ii<=$MonthEndDay;$ii++){

						$SelectDate = $SearchYear."-".substr("0".$SearchMonth,-2)."-".$ii;  
						$SelectWeek = date('w', strtotime($SelectDate));
						
						$ViewTable = "select ClassOrderTimeTypeID
										from ClassOrderSlots COS 
												left outer join Classes CLS on COS.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$SearchYear." and CLS.StartMonth=".$SearchMonth." and CLS.StartDay=".$ii." and CLS.StartHour=COS.StudyTimeHour and CLS.StartMinute=COS.StudyTimeMinute and CLS.ClassAttendState<>99 
												inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
												inner join Members MB on CO.MemberID=MB.MemberID 
												inner join Centers CT on MB.CenterID=CT.CenterID 
												inner join Branches BR on CT.BranchID=BR.BranchID 
												inner join BranchGroups BRG on BR.BranchGroupID=BRG.BranchGroupID 
												inner join Companies COM on BRG.CompanyID=COM.CompanyID 
												inner join Franchises FR on COM.FranchiseID=FR.FranchiseID 
												inner join Teachers TEA on COS.TeacherID=TEA.TeacherID 
												left outer join Teachers TEA2 on CLS.TeacherID=TEA2.TeacherID 
												inner join Members MB2 on TEA.TeacherID=MB2.TeacherID 
												left outer join Members MB3 on CT.CenterID=MB3.CenterID and MB3.MemberLevelID=12 
									where TEA.TeacherState=1 
											and COS.TeacherID=".$TeacherID." 
											and COS.ClassOrderSlotMaster=1 
											and ( 
													(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

													or 
													(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

													or 
													(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

													or 
													(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

													or 
													(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SelectDate."')=0 )   
												)  
											and COS.ClassOrderSlotState=1 
											and CO.ClassProgress=11 
											and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or CO.ClassOrderState=5 or CO.ClassOrderState=6)

											and (
													(CT.CenterPayType=1 and MB.MemberPayType=0 and ((CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=5 or CO.ClassOrderState=6) or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$SelectDate."')>=0) )) 
													or 
													( 
														( CT.CenterPayType=2 and datediff(CO.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
														or 
														( CT.CenterPayType=1 and MB.MemberPayType=1 and datediff(CO.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
													)
													or
													CO.ClassProductID=2 
													or 
													CO.ClassProductID=3 
													or 
													(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SelectDate."')=0) 
												)
						
										GROUP BY COS.StudyTimeHour, COS.StudyTimeMinute";

						
							$Sql2 = "select 
											sum(ClassOrderTimeTypeID/ClassOrderTimeTypeID) as ClassCount,
											sum(ClassOrderTimeTypeID) as MinuteCount 
									from ($ViewTable) V 
							";

							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							$Row2 = $Stmt2->fetch();
							$Stmt2 = null;

							$MinuteCount = $Row2["MinuteCount"];
							$ClassCount = round($Row2["ClassCount"],0);
				            
				            $TotalMinuteCount = $TotalMinuteCount + $MinuteCount;
							$TotalClassCount  = $TotalClassCount + $ClassCount;


                }

				if ($TotalClassCount > 0 and $SearchClassCntSW==1) {
                        $ArrTotalResultCount[$TeacherName] = "" . $TotalClassCount . "";
				} else if ($TotalMinuteCount and $SearchClassCntSW==2) {
                        $ArrTotalResultCount[$TeacherName] = "" . $TotalMinuteCount . "";
                }

            }
			?>
				<script>
				  document.getElementById('loading_dis').innerText = "";	
			      document.getElementById('loading_dis').style.display = 'none';
				</script>
			<?php
      #--------------------------------------------------------------------------------------------------------------#
      }
#====================================================================================================================#
#----------------------------------------------------- 본사 ----------------------------------------------------------#
#====================================================================================================================#
} else if($SearchStandard=="4") { 
#====================================================================================================================#
      if($SearchStandardDetail=="1") {                 // 등록생
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "본사 ＞ 등록생";
            #------------------------------------------------------------------------------------------------#
            $Sql = "select count(*) as TotalResultCount,
                           date_format(B.ClassOrderRegDateTime, '%Y-%m') as CurrentDate
                        from Members A 
                  inner join ClassOrders       B  on A.MemberID=B.MemberID
                  left outer join Centers      CT on A.CenterID=CT.CenterID 
                  left outer join Branches     BR on CT.BranchID=BR.BranchID 
                  left outer join BranchGroups BG on BR.BranchGroupID=BG.BranchGroupID 
                  left outer join Companies    E  on BG.CompanyID=E.CompanyID 
                  left outer join Franchises   F  on E.FranchiseID=F.FranchiseID 
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 and 
                              F.FranchiseID=1 and 
                              B.ClassOrderState=1 and B.ClassProgress=11 and 
                              date_format(B.ClassOrderRegDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by date_format(B.ClassOrderRegDateTime, '%Y-%m') 
                        order by B.ClassOrderRegDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);

            while($Row = $Stmt->fetch()) {
                
                $CurrentDate = $Row["CurrentDate"];
                $ArrTotalResultCount[$CurrentDate] = $Row["TotalResultCount"];
                
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="2") {          // 탈락생
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "본사 ＞ 탈락생";
            #------------------------------------------------------------------------------------------------#
            $Sql = "select ifnull(count(*), 0) as TotalResultCount,
                           date_format(B.ClassOrderModiDateTime, '%Y-%m') as CurrentDate
                        from Members A 
                  inner join ClassOrders       B  on A.MemberID=B.MemberID
                  left outer join Centers      CT on A.CenterID=CT.CenterID 
                  left outer join Branches     BR on CT.BranchID=BR.BranchID 
                  left outer join BranchGroups BG on BR.BranchGroupID=BG.BranchGroupID 
                  left outer join Companies    E  on BG.CompanyID=E.CompanyID 
                  left outer join Franchises   F  on E.FranchiseID=F.FranchiseID 
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 and 
                              F.FranchiseID=1 and 
                              B.ClassOrderState=3 and 
                              date_format(B.ClassOrderModiDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by date_format(B.ClassOrderModiDateTime, '%Y-%m')
                        order by B.ClassOrderModiDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);

            while($Row = $Stmt->fetch()) {
                
                $ArrCurrentDate = $Row["CurrentDate"];
                $ArrTotalResultCount[$ArrCurrentDate] = $Row["TotalResultCount"];
                
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="3") {          // 휴원생수
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "본사 ＞ 휴원생수";
            #------------------------------------------------------------------------------------------------#
            $Sql = "select ifnull(count(*), 0) as TotalResultCount,
                           date_format(B.ClassOrderModiDateTime, '%Y-%m') as CurrentDate
                        from Members A 
                  inner join ClassOrders       B  on A.MemberID=B.MemberID
                  left outer join Centers      CT on A.CenterID=CT.CenterID 
                  left outer join Branches     BR on CT.BranchID=BR.BranchID 
                  left outer join BranchGroups BG on BR.BranchGroupID=BG.BranchGroupID 
                  left outer join Companies    E  on BG.CompanyID=E.CompanyID 
                  left outer join Franchises   F  on E.FranchiseID=F.FranchiseID 
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 and 
                              F.FranchiseID=1 and 
                              B.ClassOrderState=4 and 
                              date_format(B.ClassOrderModiDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by date_format(B.ClassOrderModiDateTime, '%Y-%m')
                        order by B.ClassOrderModiDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);

            while($Row = $Stmt->fetch()) {
                
                $ArrCurrentDate = $Row["CurrentDate"];
                $ArrTotalResultCount[$ArrCurrentDate] = $Row["TotalResultCount"];
                
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="4") {          // 월별 전체 신규생&탈락생 (꺽은선은 신규-탈락=가감)
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "본사 ＞ 신규생&탈락생";
            #------------------------------------------------------------------------------------------------#
            $Sql = "select ifnull(count(*), 0) as TotalResultCountReg,
                           date_format(B.ClassOrderRegDateTime, '%Y-%m') as CurrentDate
                        from Members A 
                  inner join ClassOrders       B  on A.MemberID=B.MemberID
                  inner join Centers      CT on A.CenterID=CT.CenterID 
                  inner join Branches     BR on CT.BranchID=BR.BranchID 
                  inner join BranchGroups BG on BR.BranchGroupID=BG.BranchGroupID 
                  inner join Companies    E  on BG.CompanyID=E.CompanyID 
                  inner join Franchises   F  on E.FranchiseID=F.FranchiseID 
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 and 
                              F.FranchiseID=1 and 
                              B.ClassOrderState=1 and B.ClassProgress=11 and 
                              date_format(B.ClassOrderRegDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by date_format(B.ClassOrderRegDateTime, '%Y-%m') 
                        order by B.ClassOrderRegDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                
                $CurrentDate = $Row["CurrentDate"];
                $ArrTotalResultCount[$CurrentDate]["신규"] = $Row["TotalResultCountReg"];
                
            }
            #------------------------------------------------------------------------------------------------#
            $Sql = "select ifnull(count(*), 0) as TotalResultCountDrawal,
                           date_format(B.ClassOrderModiDateTime, '%Y-%m') as CurrentDate
                        from Members A 
                  inner join ClassOrders       B  on A.MemberID=B.MemberID
                  inner join Centers      CT on A.CenterID=CT.CenterID 
                  inner join Branches     BR on CT.BranchID=BR.BranchID 
                  inner join BranchGroups BG on BR.BranchGroupID=BG.BranchGroupID 
                  inner join Companies    E  on BG.CompanyID=E.CompanyID 
                  inner join Franchises   F  on E.FranchiseID=F.FranchiseID 
                        where A.MemberLevelID=19 and 
                              A.MemberState=1 and 
                              F.FranchiseID=1 and 
                              B.ClassOrderState=3 and 
                              date_format(B.ClassOrderModiDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."'
                        group by date_format(B.ClassOrderModiDateTime, '%Y-%m')
                        order by B.ClassOrderModiDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                  $CurrentDate = $Row["CurrentDate"];
                  $ArrTotalResultCount[$CurrentDate]["탈락"] = $Row["TotalResultCountDrawal"];
                  if (isset($ArrTotalResultCount[$CurrentDate]["신규"])) {
                       $ArrTotalResultCount[$CurrentDate]["quantity"] = $ArrTotalResultCount[$CurrentDate]["신규"] - $Row["TotalResultCountDrawal"];
                  } else {
                       $ArrTotalResultCount[$CurrentDate]["quantity"] = 0 - $Row["TotalResultCountDrawal"];
                  }
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="5") {          // 월간 포인트 학생 등수
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "본사 ＞ 포인트 학생 등수";
            #------------------------------------------------------------------------------------------------#
            $ViewTable = "select A.MemberID, 
                                  sum(A.MemberPoint) as MemberTotalPoint 
                            from MemberPoints A 
                      inner join Members C on A.MemberID=C.MemberID 
                      inner join Centers D on C.CenterID=D.CenterID 
                      inner join Branches E on D.BranchID=E.BranchID
                      inner join BranchGroups F on E.BranchGroupID=F.BranchGroupID 
                      inner join Companies G on F.CompanyID=G.CompanyID 
                      inner join Franchises H on G.FranchiseID=H.FranchiseID 
                           where 1=1
                           group by A.MemberID";

            $Sql = "select  A.MemberTotalPoint, B.MemberName
                            from (".$ViewTable.") A 
                      inner join Members B on A.MemberID=B.MemberID 
                      inner join Centers C on B.CenterID=C.CenterID 
                           where B.MemberLevelID=19 and   
                                 A.MemberTotalPoint >=".$Point_Level1." and A.MemberTotalPoint <=".$Point_Level2."
                        order by A.MemberTotalPoint desc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                
                  $MemberName = $Row["MemberName"];
                  $ArrTotalResultCount[$MemberName] = $Row["MemberTotalPoint"];
                
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="6") {          // 수익장부
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "본사 ＞ 월간 수익성 추이";
            #------------------------------------------------------------------------------------------------#
            $Sql = "select left(A.AccBookDate,7) as CurrentDate,
                            sum(A.AccBookMoney) as TotalResultCountReg
                         from account_book A 
                        where left(A.AccBookDate,7) >= '".$SearchStartDate."' AND left(A.AccBookDate,7) <= '".$SearchEndDate."' and A.AccBookType=1
                     group by left(A.AccBookDate,7), A.AccBookType 
                     order by A.AccBookDate asc"; 
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                  $CurrentDate = $Row["CurrentDate"];
                  $ArrTotalResultCount[$CurrentDate]["매출"] = $Row["TotalResultCountReg"];
            }
            #------------------------------------------------------------------------------------------------#
            $Sql = "select left(A.AccBookDate,7) as CurrentDate,
                            sum(A.AccBookMoney) as TotalResultCountDrawal
                         from account_book A 
                        where left(A.AccBookDate,7) >= '".$SearchStartDate."' AND left(A.AccBookDate,7) <= '".$SearchEndDate."' and A.AccBookType=2
                     group by left(A.AccBookDate,7), A.AccBookType 
                     order by A.AccBookDate asc"; 
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                  $CurrentDate = $Row["CurrentDate"];
                  $ArrTotalResultCount[$CurrentDate]["비용"] = $Row["TotalResultCountDrawal"];
                  if (isset($ArrTotalResultCount[$CurrentDate]["매출"])) {
                       $ArrTotalResultCount[$CurrentDate]["quantity"] = $ArrTotalResultCount[$CurrentDate]["매출"] - $Row["TotalResultCountDrawal"];
                  } else {
                       $ArrTotalResultCount[$CurrentDate]["quantity"] = 0 - $Row["TotalResultCountDrawal"];
                  }
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="7") {          // B2C 월간신규(결제기준)
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "본사 ＞ B2C 월간신규(결제기준)";
            #------------------------------------------------------------------------------------------------#
            $Sql = "select sum(B.ClassOrderPayDetailPaymentPrice) as TotalB2CPayment,
                           left(A.ClassOrderPayPaymentDateTime,7) as CurrentDate
                        from ClassOrderPays A 
                        left join ClassOrderPayDetails B on B.ClassOrderPayID=A.ClassOrderPayID
                       where left(A.ClassOrderPayPaymentDateTime,7) >= '".$SearchStartDate."' AND left(A.ClassOrderPayPaymentDateTime,7) <= '".$SearchEndDate."' 
                       group by left(A.ClassOrderPayPaymentDateTime,7)
                       order by A.ClassOrderPayPaymentDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->bindParam(':EncryptionKey', $EncryptionKey);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CurrentDate = $Row["CurrentDate"];
                   $ArrTotalResultCount[$CurrentDate] = $Row["TotalB2CPayment"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="8") {          // B2B 월간신규(수업배정)
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "본사 ＞ B2B 월간신규(수업배정)";
            #------------------------------------------------------------------------------------------------#
            /*
            $Sql = "select sum(D.ClassOrderPayDetailPaymentPrice) as TotalB2BPayment,
                           B.ClassOrderPayYear  as CurrentYear,
                           B.ClassOrderPayMonth as CurrentMonth
                        from ClassOrders A
                             left join ClassOrderPayB2bs B on B.ClassOrderID=A.ClassOrderID
                             left join ClassOrderPayB2bDetails D on D.ClassOrderPayB2bID=B.ClassOrderPayB2bID
                       where (B.ClassOrderPayYear >= '".$SearchStartYear."' and B.ClassOrderPayMonth >= '".$SearchStartMonth."') and 
                             (B.ClassOrderPayYear <= '".$SearchEndYear."' and B.ClassOrderPayMonth <= '".$SearchEndMonth."')
                       group by B.ClassOrderPayYear, B.ClassOrderPayMonth 
                       order by B.ClassOrderPayYear asc, B.ClassOrderPayMonth asc";
            */
            $ViewTable = " select A.* from ClassOrderPays A where A.ClassOrderPayType=1";
            $Sql = "select sum(A.ClassOrderPayUseCashPrice) as TotalB2BPayment,
                           left(A.ClassOrderPayPaymentDateTime,7) as CurrentDate
                    from ($ViewTable) A
                        inner join Centers C on A.CenterID=C.CenterID and C.CenterPayType=1
                       where left(A.ClassOrderPayPaymentDateTime,7) >= '".$SearchStartDate."' AND left(A.ClassOrderPayPaymentDateTime,7) <= '".$SearchEndDate."' 
                       group by left(A.ClassOrderPayPaymentDateTime,7)
                       order by A.ClassOrderPayPaymentDateTime asc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->bindParam(':EncryptionKey', $EncryptionKey);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   //$CurrentDate = $Row["CurrentYear"] . iif($Row["CurrentMonth"] < 10,"-0","-") . $Row["CurrentMonth"];
                   $CurrentDate = $Row["CurrentDate"];
                   $ArrTotalResultCount[$CurrentDate] = $Row["TotalB2BPayment"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      } else if($SearchStandardDetail=="9") {          // 연간 신규생비교
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "본사 ＞ 연간 신규생비교";
            #------------------------------------------------------------------------------------------------#
			if ($SearchEndYear > $SearchStartYear) {

				 for ($yy=$SearchStartYear; $yy <= $SearchEndYear; $yy++) {

						$StartYYMM = $yy . "-01";
						$EndYYMM   = $yy . "-12";
						$Sql = "select ifnull(count(*), 0) as TotalResultCountReg,
									   date_format(B.ClassOrderRegDateTime, '%m') as CurrentMM
									from Members A 
							  inner join ClassOrders       B  on A.MemberID=B.MemberID
							  inner join Centers      CT on A.CenterID=CT.CenterID 
							  inner join Branches     BR on CT.BranchID=BR.BranchID 
							  inner join BranchGroups BG on BR.BranchGroupID=BG.BranchGroupID 
							  inner join Companies    E  on BG.CompanyID=E.CompanyID 
							  inner join Franchises   F  on E.FranchiseID=F.FranchiseID 
									where A.MemberLevelID=19 and 
										  A.MemberState=1 and 
										  F.FranchiseID=1 and 
										  B.ClassOrderState=1 and B.ClassProgress=11 and 
										  date_format(B.ClassOrderRegDateTime, '%Y-%m') BETWEEN '".$StartYYMM."' AND '".$EndYYMM."'
									group by date_format(B.ClassOrderRegDateTime, '%Y-%m') 
									order by B.ClassOrderRegDateTime asc";
						$Stmt = $DbConn->prepare($Sql);
						$Stmt->execute();
						$Stmt->setFetchMode(PDO::FETCH_ASSOC);
						while($Row = $Stmt->fetch()) {
							
							$CurrentMM = (int)$Row["CurrentMM"];
							$ArrTotalResultCount[$CurrentMM][$yy] = $Row["TotalResultCountReg"];
							
						}

				 }

			} else if ($SearchEndYear == $SearchStartYear) {

						$StartYYMM = $SearchEndYear . "-01";
						$EndYYMM   = $SearchEndYear . "-12";
						$Sql = "select ifnull(count(*), 0) as TotalResultCountReg,
									   date_format(B.ClassOrderRegDateTime, '%m') as CurrentMM
									from Members A 
							  inner join ClassOrders       B  on A.MemberID=B.MemberID
							  inner join Centers      CT on A.CenterID=CT.CenterID 
							  inner join Branches     BR on CT.BranchID=BR.BranchID 
							  inner join BranchGroups BG on BR.BranchGroupID=BG.BranchGroupID 
							  inner join Companies    E  on BG.CompanyID=E.CompanyID 
							  inner join Franchises   F  on E.FranchiseID=F.FranchiseID 
									where A.MemberLevelID=19 and 
										  A.MemberState=1 and 
										  F.FranchiseID=1 and 
										  B.ClassOrderState=1 and B.ClassProgress=11 and 
										  date_format(B.ClassOrderRegDateTime, '%Y-%m') BETWEEN '".$StartYYMM."' AND '".$EndYYMM."'
									group by date_format(B.ClassOrderRegDateTime, '%Y-%m') 
									order by B.ClassOrderRegDateTime asc";
						$Stmt = $DbConn->prepare($Sql);
						$Stmt->execute();
						$Stmt->setFetchMode(PDO::FETCH_ASSOC);
						while($Row = $Stmt->fetch()) {
							
							$CurrentMM = (int)$Row["CurrentMM"];
							$ArrTotalResultCount[$CurrentMM][$SearchEndYear] = $Row["TotalResultCountReg"];
							
						}

			}
      #--------------------------------------------------------------------------------------------------------------#
      }
#====================================================================================================================#
}
#====================================================================================================================#
?>
        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div class="uk-overflow-container">

                            <div id="categorydiv">● <?=$category_disname?></div>
                            <?
                            if($SearchStandard && $SearchStandardDetail) {
                                  
                                  $ArrTotalResultCount   = json_encode($ArrTotalResultCount);
                                  $ArrTotalSubResultCont = json_encode($ArrTotalSubResultCont);
                                  /*
                                  if($SearchStandard==4 && $SearchStandardDetail==9) {
                                       echo $ArrTotalResultCount;
                                  }
                                  */
								  
                            }
                            ?>
                            <!-- Resources -->
                            <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
                            <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
                            <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

                            <!-- Chart code -->
                            <script type="text/javascript">
                            var mainwhat   = <?=$SearchStandard?>;
                            var subwhat    = <?=$SearchStandardDetail?>;
                            var basic_data = <?=$ArrTotalResultCount?>;
                            /*==========================================================================================*/
                            am4core.ready(function() {
                            /*==========================================================================================*/
                                //am4core.options.commercialLicense = true;  // 라이센스여부
                                am4core.useTheme(am4themes_animated);
                                /*------------------------------------------------------------------------------------*/
                                if ((mainwhat==1 && (subwhat==2 || subwhat==7)) || 
                                    (mainwhat==2 && subwhat==4) ||
                                    (mainwhat==3 && subwhat==2) ||
                                    (mainwhat==4 && (subwhat==4 || subwhat==6)) ) {
                                /*------------------------------------------------------------------------------------*/
                                        var chart = am4core.create("chartdiv", am4charts.XYChart);
                                        chart.scrollbarX = new am4core.Scrollbar();

                                        // some extra padding for range labels
                                        chart.paddingBottom = 50;

                                        chart.cursor = new am4charts.XYCursor();
                                        chart.scrollbarX = new am4core.Scrollbar();

                                        // will use this to store colors of the same items
                                        var colors = {};

                                        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                                        categoryAxis.dataFields.category = "category";
                                        categoryAxis.renderer.grid.template.location = 0;
                                        categoryAxis.dataItems.template.text = "{realName}";
                                        categoryAxis.adapter.add("tooltipText", function(tooltipText, target){
                                          return categoryAxis.tooltipDataItem.dataContext.realName;
                                        })

                                        categoryAxis.renderer.labels.template.rotation = 290;
                                        categoryAxis.renderer.labels.template.hideOversized = false;
                                        categoryAxis.renderer.minGridDistance = 20;
                                        categoryAxis.renderer.labels.template.horizontalCenter = "right";
                                        categoryAxis.renderer.labels.template.verticalCenter   = "right";
                                        categoryAxis.tooltip.label.rotation = 290;
                                        categoryAxis.tooltip.label.horizontalCenter = "right";
                                        categoryAxis.tooltip.label.verticalCenterb  = "right";


                                        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                                        valueAxis.tooltip.disabled = true;
                                        valueAxis.min = 0;
                                        if (mainwhat==4 && subwhat==6) {
                                               valueAxis.title.text = "단위 : 원";
                                        } else {
                                               valueAxis.title.text = "단위 : %";
                                        }
                                        valueAxis.title.fontWeight = "normal";

                                        // single column series for all data
                                        var columnSeries = chart.series.push(new am4charts.ColumnSeries());
                                        columnSeries.columns.template.width = am4core.percent(80);
                                        columnSeries.tooltipText = "{provider}: {realName}, {valueY}";
                                        columnSeries.dataFields.categoryX = "category";
                                        columnSeries.dataFields.valueY = "value";

                                        // second value axis for quantity
                                        var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
                                        valueAxis2.renderer.opposite = true;
                                        valueAxis2.syncWithAxis = valueAxis;
                                        valueAxis2.tooltip.disabled = true;

                                        // quantity line series
                                        var lineSeries = chart.series.push(new am4charts.LineSeries());
                                        lineSeries.tooltipText = "{valueY}";
                                        lineSeries.dataFields.categoryX = "category";
                                        lineSeries.dataFields.valueY = "quantity";
                                        lineSeries.yAxis = valueAxis2;
                                        lineSeries.bullets.push(new am4charts.CircleBullet());
                                        lineSeries.stroke = chart.colors.getIndex(13);
                                        lineSeries.fill = lineSeries.stroke;
                                        lineSeries.strokeWidth = 2;
                                        lineSeries.snapTooltip = true;

                                        // when data validated, adjust location of data item based on count
                                        lineSeries.events.on("datavalidated", function(){
                                             lineSeries.dataItems.each(function(dataItem){
                                                   // if count divides by two, location is 0 (on the grid)
                                                   if(dataItem.dataContext.count / 2 == Math.round(dataItem.dataContext.count / 2)){
                                                        dataItem.setLocation("categoryX", 0);
                                                   }
                                                   // otherwise location is 0.5 (middle)
                                                   else{
                                                        dataItem.setLocation("categoryX", 0.5);
                                                   }
                                             })
                                        })

                                        // fill adapter, here we save color value to colors object so that each time the item has the same name, the same color is used
                                        columnSeries.columns.template.adapter.add("fill", function(fill, target) {
                                             var name = target.dataItem.dataContext.realName;
                                             if (!colors[name]) {
                                               colors[name] = chart.colors.next();
                                             }
                                             target.stroke = colors[name];
                                             return colors[name];
                                        })

                                        var rangeTemplate = categoryAxis.axisRanges.template;
                                        rangeTemplate.tick.disabled      = false;
                                        rangeTemplate.tick.location      = 0;
                                        rangeTemplate.tick.strokeOpacity = 0.6;
                                        rangeTemplate.tick.length        = 60;
                                        rangeTemplate.grid.strokeOpacity = 0.5;
                                        rangeTemplate.label.tooltip      = new am4core.Tooltip();
                                        rangeTemplate.label.tooltip.dy   = -10;
                                        rangeTemplate.label.cloneTooltip = false;

                                        ///// DATA
                                        var chartData = [];
                                        var lineSeriesData = [];

                                        // process data ant prepare it for the chart
                                        /*--------------------------------------------------*/
                                        for (var providerName in basic_data) {
                                        /*--------------------------------------------------*/
                                             var providerData = basic_data[providerName];

                                             // add data of one provider to temp array
                                             var tempArray = [];
                                             var count = 0;
                                             // add items
                                             if (subwhat==7) {
                                                     for (var itemName in providerData) {
                                                           if(itemName != "quantity"){
                                                               count++;
                                                               // we generate unique category for each column (providerName + "_" + itemName) and store realName
                                                               tempArray.push({ category: providerName + "_" + itemName, 
                                                                                realName: itemName, 
                                                                                value: providerData[itemName]+'%', 
                                                                                provider: providerName
                                                                              })
                                                           }
                                                     }
                                             } else {
                                                     for (var itemName in providerData) {
                                                           if(itemName != "quantity"){
                                                               count++;
                                                               // we generate unique category for each column (providerName + "_" + itemName) and store realName
                                                               tempArray.push({ category: providerName + "_" + itemName, 
                                                                                realName: itemName, 
                                                                                value: providerData[itemName], 
                                                                                provider: providerName
                                                                             })
                                                           }
                                                     }
                                             }

                                             // add quantity and count to middle data item (line series uses it)
                                             var lineSeriesDataIndex = Math.floor(count / 2);
                                             tempArray[lineSeriesDataIndex].quantity = providerData.quantity;
                                             tempArray[lineSeriesDataIndex].count    = count;
                                             // push to the final data
                                             am4core.array.each(tempArray, function(item) {
                                                  chartData.push(item);
                                             })
                                             /* sort temp array */

                                             // create range (the additional label at the bottom)
                                             var range = categoryAxis.axisRanges.create();
                                             range.category    = tempArray[0].category;
                                             range.endCategory = tempArray[tempArray.length - 1].category;
                                             range.label.text  = tempArray[0].provider;
                                             range.label.dy    = 30;
                                             range.label.truncate    = true;
                                             range.label.fontWeight  = "bold";
                                             range.label.tooltipText = tempArray[0].provider;

                                             range.label.adapter.add("maxWidth", function(maxWidth, target){
                                               var range = target.dataItem;
                                               var startPosition = categoryAxis.categoryToPosition(range.category, 0);
                                               var endPosition   = categoryAxis.categoryToPosition(range.endCategory, 1);
                                               var startX        = categoryAxis.positionToCoordinate(startPosition);
                                               var endX          = categoryAxis.positionToCoordinate(endPosition);
                                               return endX - startX;
                                             })
                                        /*--------------------------------------------------*/
                                        }
                                        /*--------------------------------------------------*/
                                        // data sorting....
                                        chartData.sort(function(a, b) {
                                             return parseFloat(b.yValue) - parseFloat(a.yValue);
                                        });

                                        chart.data = chartData;

                                        // last tick
                                        var range = categoryAxis.axisRanges.create();
                                        range.category = chart.data[chart.data.length - 1].category;
                                        range.label.disabled = true;
                                        range.tick.location = 1;
                                        range.grid.location = 1;
                                /*------------------------------------------------------------------------------------*/
                                } else if (mainwhat==1 && subwhat==4) {
                                /*------------------------------------------------------------------------------------*/
                                        var chart = am4core.create("chartdiv", am4charts.XYChart3D);
                                        chart.paddingBottom = 30;
                                        chart.angle = 35;

                                        // Create axes
                                        var chartData = [];
                                        /*--------------------------------------------------*/
                                        // 데이터 재구성  
                                        /*--------------------------------------------------*/
                                        for (var providerName in basic_data) {
                                        /*--------------------------------------------------*/
                                                 var providerData = basic_data[providerName]; 

                                                 var tempArray = []; 
                                                 for (var itemValue in providerData) {
                                                       tempArray.push({ 
                                                            xName:   providerName,
                                                            yValue:  providerData 
                                                       })
                                                 }
                                                 // push to the final data
                                                 am4core.array.each(tempArray, function(item) {
                                                       chartData.push(item);
                                                 })
                                        /*--------------------------------------------------*/
                                        }
                                        /*--------------------------------------------------*/
                                        
                                        /*--------------------------------------------------*/
                                        if (mainwhat==1 && (subwhat==5 || subwhat==6)) {
                                        /*--------------------------------------------------*/
                                                // data sorting....
                                                chartData.sort(function(a, b) {
                                                      return parseFloat(b.yValue) - parseFloat(a.yValue);
                                                });
                                        /*--------------------------------------------------*/
                                        }
                                        /*--------------------------------------------------*/
                                        chart.data = chartData;

                                        var useJson = JSON.stringify(chartData); 
                                        console.log(useJson) 
                                        /*--------------------------------------------------*/
                                        // Create axes
                                        let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                                        categoryAxis.dataFields.category = "xName";
                                        categoryAxis.renderer.labels.template.rotation = 290;
                                        categoryAxis.renderer.labels.template.hideOversized = false;
                                        categoryAxis.renderer.minGridDistance = 20;
                                        categoryAxis.renderer.labels.template.horizontalCenter = "right";
                                        categoryAxis.renderer.labels.template.verticalCenter = "middle";
                                        categoryAxis.tooltip.label.rotation = 290;
                                        categoryAxis.tooltip.label.horizontalCenter = "right";
                                        categoryAxis.tooltip.label.verticalCenterb  = "middle";

                                        let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                                        valueAxis.title.text       = "수강료(수수료제외)";
                                        valueAxis.title.fontWeight = "normal";

                                        // Create series
                                        var series = chart.series.push(new am4charts.ColumnSeries3D());
                                        series.dataFields.valueY    = "yValue";
                                        series.dataFields.categoryX = "xName";
                                        series.name = "Values";
                                        series.tooltipText = "{categoryX}: [bold]{valueY}[/]";
                                        series.columns.template.fillOpacity = .8;

                                        var columnTemplate = series.columns.template;
                                        columnTemplate.strokeWidth = 2;
                                        columnTemplate.strokeOpacity = 1;
                                        columnTemplate.stroke = am4core.color("#FFFFFF");

                                        columnTemplate.adapter.add("fill", function(fill, target) {
                                          return chart.colors.getIndex(target.dataItem.index);
                                        })

                                        columnTemplate.adapter.add("stroke", function(stroke, target) {
                                              return chart.colors.getIndex(target.dataItem.index);
                                        })

                                        chart.cursor = new am4charts.XYCursor();
                                        chart.cursor.lineX.strokeOpacity = 0;
                                        chart.cursor.lineY.strokeOpacity = 0;
                                /*------------------------------------------------------------------------------------*/
                                } else if (mainwhat==4 && subwhat==9) {            // 연간 신규생 비교(다중 꺽은선 그래프)
                                /*------------------------------------------------------------------------------------*/
                                        var chart = am4core.create("chartdiv", am4charts.XYChart);
                                        // Create axes
                                        var chartData = [];
										var start_year = <?=$SearchStartYear?>;
										var end_year   = <?=$SearchEndYear?>;
                                        /*--------------------------------------------------*/
                                        // 데이터 재구성  
                                        /*--------------------------------------------------*/
                                        for (var providerName in basic_data) {
                                        /*--------------------------------------------------*/
												 var providerData = basic_data[providerName]; 
                                                 
                                                 var provid_obj = {};
                                                 provid_obj['Month'] = providerName;
												 for (var YearName in providerData) {
													   var YearData = providerData[YearName]; 
													   provid_obj[YearName] = YearData;
                                                 }

												 var tempArray = [];
												 tempArray.push(provid_obj);

												 // push to the final data
												 am4core.array.each(tempArray, function(item) {
													   chartData.push(item);
												 })
                                        /*--------------------------------------------------*/
                                        }
                                        /*--------------------------------------------------*/
                                        // data sorting....
                                        chart.data = chartData;

                                        var useJson = JSON.stringify(chartData); 
                                        console.log(useJson) 
                                        /*--------------------------------------------------*/
										// Create category axis
										var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
										categoryAxis.dataFields.category = "Month";
										categoryAxis.renderer.opposite = false;

										// Create value axis
										var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
										valueAxis.renderer.inversed = false;
										valueAxis.title.text = "신규생";
										valueAxis.renderer.minLabelPosition = 0.01;

										// Create series
										for (yy=start_year; yy<=end_year; yy++)	{
										        var series_vars = "series_"+yy;
												series_vars = chart.series.push(new am4charts.LineSeries());
												series_vars.dataFields.valueY = yy;
												series_vars.dataFields.categoryX = "Month";
												series_vars.name = yy + '년';
												series_vars.bullets.push(new am4charts.CircleBullet());
												series_vars.tooltipText = "{name} {categoryX}월 신규생 {valueY}명";
												series_vars.legendSettings.valueText = "{valueY}";
												series_vars.visible  = false;

												let hs = series_vars.segments.template.states.create("hover")
												hs.properties.strokeWidth = 5;
												series_vars.segments.template.strokeWidth = 1;
										}

										// Add chart cursor
										chart.cursor = new am4charts.XYCursor();
										chart.cursor.behavior = "zoomY";
                                        /*
										for (yy=start_year; yy<=end_year; yy++)	{
											    var series_vars = "series_"+yy;
												let hs = series_vars.segments.template.states.create("hover")
												hs.properties.strokeWidth = 5;
												series_vars.segments.template.strokeWidth = 1;
                                        }
										*/

										// Add legend
										chart.legend = new am4charts.Legend();
										chart.legend.itemContainers.template.events.on("over", function(event){
										  var segments = event.target.dataItem.dataContext.segments;
										  segments.each(function(segment){
											segment.isHover = true;
										  })
										})

										chart.legend.itemContainers.template.events.on("out", function(event){
										  var segments = event.target.dataItem.dataContext.segments;
										  segments.each(function(segment){
											segment.isHover = false;
										  })
										})


                                /*------------------------------------------------------------------------------------*/
                                } else {
                                /*------------------------------------------------------------------------------------*/
                                        var chart = am4core.create("chartdiv", am4charts.XYChart);
                                        chart.scrollbarX = new am4core.Scrollbar();

                                        // Create axes
                                        var chartData = [];
                                        /*--------------------------------------------------*/
                                        // 데이터 재구성  
                                        /*--------------------------------------------------*/
                                        for (var providerName in basic_data) {
                                        /*--------------------------------------------------*/
                                                 var providerData = basic_data[providerName]; 

                                                 var tempArray = []; 
                                                 for (var itemValue in providerData) {
                                                       tempArray.push({ 
                                                            xName:   providerName,
                                                            yValue:  providerData 
                                                       })
                                                 }
                                                 // push to the final data
                                                 am4core.array.each(tempArray, function(item) {
                                                       chartData.push(item);
                                                 })
                                        /*--------------------------------------------------*/
                                        }
                                        /*--------------------------------------------------*/
                                        
                                        /*--------------------------------------------------*/
                                        if ( (mainwhat==1 && (subwhat==5  || subwhat==6)) || 
                                             (mainwhat==2 && subwhat > 4) || 
                                             (mainwhat==3 && (subwhat==3  || subwhat==4 || subwhat==5)) || 
                                             (mainwhat==4 && subwhat==5) ) {
                                        /*--------------------------------------------------*/
                                                // data sorting....
                                                chartData.sort(function(a, b) {
                                                      return parseFloat(b.yValue) - parseFloat(a.yValue);
                                                });
                                        /*--------------------------------------------------*/
                                        }
                                        /*--------------------------------------------------*/
                                        chart.data = chartData;

                                        var useJson = JSON.stringify(chartData); 
                                        console.log(useJson) 
                                        // alert(useJson);
                                        /*--------------------------------------------------*/
                                        // Create axes
                                        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                                        categoryAxis.dataFields.category = "xName";
                                        categoryAxis.renderer.grid.template.location = 0;
                                        categoryAxis.renderer.minGridDistance        = 30;
                                        categoryAxis.renderer.labels.template.horizontalCenter = "right";     // left, middle, right
                                        categoryAxis.renderer.labels.template.verticalCenter   = "middle";    // left, middle, right
                                        categoryAxis.renderer.labels.template.rotation = 290;                 // 0 ~ 360
                                        categoryAxis.tooltip.disabled   = true;
                                        categoryAxis.renderer.minHeight = 30;

                                        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                                        valueAxis.renderer.minWidth = 50;

                                        // Create series
                                        var series = chart.series.push(new am4charts.ColumnSeries());
                                        series.sequencedInterpolation = true;
                                        series.dataFields.valueY      = "yValue";
                                        series.dataFields.categoryX   = "xName";
                                        series.tooltipText = "{categoryX}: {valueY}";   //"[{categoryX}: bold]{valueY}[/]";
                                        series.columns.template.strokeWidth = 0;

                                        series.tooltip.pointerOrientation = "vertical";

                                        series.columns.template.column.cornerRadiusTopLeft  = 15;
                                        series.columns.template.column.cornerRadiusTopRight = 15;
                                        series.columns.template.column.fillOpacity = 0.8;

                                        // on hover, make corner radiuses bigger
                                        var hoverState = series.columns.template.column.states.create("hover");
                                        hoverState.properties.cornerRadiusTopLeft  = 0;
                                        hoverState.properties.cornerRadiusTopRight = 0;
                                        hoverState.properties.fillOpacity          = 1;

                                        series.columns.template.adapter.add("fill", function(fill, target) {
                                                return chart.colors.getIndex(target.dataItem.index);
                                        });

                                        // Cursor
                                        chart.cursor = new am4charts.XYCursor();
                                /*------------------------------------------------------------------------------------*/
                                }
                                /*------------------------------------------------------------------------------------*/

                            /*==========================================================================================*/
                            });
                            /*==========================================================================================*/
                            </script>
                            <!------ GRAPH DIV ----->
                            <div id="chartdiv"></div>
                            
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
var ListCount = <?=$ListCount-1?>;
function CheckListAll(obj){

    for (ii=1;ii<=ListCount;ii++){
        if (obj.checked){
            document.getElementById("CheckBox_"+ii).checked = true;
        }else{
            document.getElementById("CheckBox_"+ii).checked = false;
        }   
    }
}

function SearchDateChange() {

    if (document.SearchForm.SearchDate.selectedIndex==1){
         document.SearchForm.SearchStartMonth.value = document.SearchForm.SearchEndMonth.value;
    }
    document.SearchForm.action = "account_graph_total.php";
    document.SearchForm.submit();

}

function SendMessageForm(){

    if (ListCount==0){
        alert("선택한 목록이 없습니다.");
    }else{
        
        MemberIDs = "|";
        for (ii=1;ii<=ListCount;ii++){
            if (document.getElementById("CheckBox_"+ii).checked){
                MemberIDs = MemberIDs + document.getElementById("CheckBox_"+ii).value + "|";
            }   
        }

    
        if (MemberIDs=="|"){
            alert("선택한 목록이 없습니다.");
        }else{

            openurl = "send_message_log_multi_form.php?MemberIDs="+MemberIDs;
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
    }
}

function ChSearchStartMonth(MonthType, MonthNumber){
    
    if (MonthType==1){
        YearNumber = document.SearchForm.SearchStartYear.value;
    }else{
        YearNumber = document.SearchForm.SearchEndYear.value;
    }
    url = "ajax_get_month_last_day.php";

    //location.href = url + "?YearNumber="+YearNumber+"&MonthNumber"+MonthNumber;
    $.ajax(url, {
        data: {
            YearNumber: YearNumber,
            MonthNumber: MonthNumber
        },
        success: function (data) {

            if (MonthType==1){

                SelBoxInitOption('SearchStartDay');

                SelBoxAddOption( 'SearchStartDay', '<?=$일선택[$LangID]?>', "", "");
                for (ii=1 ; ii<=data.LastDay ; ii++ ){
                    ArrOptionText     = ii + "<?=$일일[$LangID]?>";
                    ArrOptionValue    = ii;

                    ArrOptionSelected = "";
                    if (ii==<?=(int)$SearchStartDay?>){
                        ArrOptionSelected = "selected";
                    }

                    SelBoxAddOption( 'SearchStartDay', ArrOptionText, ArrOptionValue, ArrOptionSelected );
                }

            }else{

                SelBoxInitOption('SearchEndDay');

                SelBoxAddOption( 'SearchEndDay', '<?=$일선택[$LangID]?>', "", "");
                for (ii=1 ; ii<=data.LastDay ; ii++ ){
                    ArrOptionText     = ii + "<?=$일일[$LangID]?>";
                    ArrOptionValue    = ii;
                    
                    ArrOptionSelected = "";
                    if (ii==<?=(int)$SearchEndDay?>){
                        ArrOptionSelected = "selected";
                    }
                        
                    SelBoxAddOption( 'SearchEndDay', ArrOptionText, ArrOptionValue, ArrOptionSelected );
                }

            }

        },
        error: function () {

        }
    });
}

/** ===================================== 기본함수 ===================================== **/
// Option객체를 생성해서 Return
function SelBoxCreateOption( text, value, selected )
{
    var oOption = document.createElement("OPTION"); // Option 객체를 생성
    oOption.text = text; // Text(Keyword)를 입력
    oOption.value = value; // Value를 입력
    if (selected=="selected"){
        oOption.selected = true;
    }
    return oOption;
}

// SelectBox의 Option을 초기화
function SelBoxInitOption( ObjId ){
    var SelectObj = document.getElementById( ObjId );
    if ( SelectObj == null ) return; // 객체가 존재하지 않으면 취소

    SelectObj.options.length = 0; // 길이를 0으로 하면 초기화
}

// Option을 추가
function SelBoxAddOption( ObjId, text, value, selected ){
    var SelectObj = document.getElementById( ObjId );

    SelectObj.add( SelBoxCreateOption( text , value, selected ) );
    text     = "";
    value    = "";
    selected = "";
}
/** ===================================== 기본함수 ===================================== **/

</script>






<script>
function SearchSubmit(){
    document.SearchForm.action = "account_graph_total.php";
    document.SearchForm.submit();
}

window.onload = function(){
    ChSearchStartMonth(1, <?=(int)$SearchStartMonth?>);
    ChSearchStartMonth(2, <?=(int)$SearchEndMonth?>);
}

</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>