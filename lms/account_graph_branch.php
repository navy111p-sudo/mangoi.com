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
#--------------------------------------------------------- 지사그래프 --------------------------------------------------#
#--------------------------------------------------------------------------------------------------------------------#
$MainMenuID = 21;
$SubMenuID  = 2104;
include_once('./inc_top.php'); 
include_once('./inc_menu_left.php');
#--------------------------------------------------------------------------------------------------------------------#
$AddSqlWhere             = "1=1";
$AddSqlWhere2            = "1=1";
$ListParam               = "1=1";
$TotalRowCount           = "";
#--------------------------------------------------------------------------------------------------------------------#
$SearchStandard          = 2;
$SearchStandardDetail    = isset($_REQUEST["SearchStandardDetail"   ]) ? $_REQUEST["SearchStandardDetail"   ] : "";
$SearchStandardDetailSub = $_ADMIN_BRANCH_ID_;
$SearchDate              = 3;
#--------------------------------------------------------------------------------------------------------------------#
$SearchStartYear         = isset($_REQUEST["SearchStartYear"        ]) ? $_REQUEST["SearchStartYear"        ] : "";
$SearchStartMonth        = isset($_REQUEST["SearchStartMonth"       ]) ? $_REQUEST["SearchStartMonth"       ] : "";
$SearchStartDay          = isset($_REQUEST["SearchStartDay"         ]) ? $_REQUEST["SearchStartDay"         ] : "";
#--------------------------------------------------------------------------------------------------------------------#
$SearchEndYear           = isset($_REQUEST["SearchEndYear"          ]) ? $_REQUEST["SearchEndYear"          ] : "";
$SearchEndMonth          = isset($_REQUEST["SearchEndMonth"         ]) ? $_REQUEST["SearchEndMonth"         ] : "";
$SearchEndDay            = isset($_REQUEST["SearchEndDay"           ]) ? $_REQUEST["SearchEndDay"           ] : "";
#--------------------------------------------------------------------------------------------------------------------#
if ($SearchStandardDetail=="") {
        $SearchStandardDetail = 1;
}
if($SearchDate=="") {
        $SearchDate = 1;
}
$Point_Level1 = 0;
$Point_Level2 = 0;
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
        $SearchStartMonth = 1;
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
#--------------------------------------------------------------------------------------------------------------------#
// 서치폼 감추기
$HideSearchForm = 0;
$HideStartMonth = 0;
$HideStartDay   = 0;
$HideEndYear    = 0;
$HideEndMonth   = 0;
$HideEndDay     = 0;
#--------------------------------------------------------------------------------------------------------------------#
if ($SearchDate==3) {    // 월별
        $SearchEndYear  = $SearchStartYear;
        if (!$SearchEndMonth) {
                $SearchEndMonth = date("n");
        }
        if (!$SearchEndDay) {
                $SearchEndDay   = 31;
        }
		if ($SearchStandardDetail=="5") {
				$HideStartMonth = 1;
		}
		$HideStartDay   = 1;
		$HideEndYear    = 1;
		$HideEndDay     = 1;
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
$ArrSearchStandard   = "";
$idx = 0;
#--------------------------------------------------------------------------------------------------------------------#
$ArrSearchStandardDetail = array(
		1=> "1.월별 매출",
		2=> "2.월별 커미션",
		3=> "3.월별 수강생",
		4=> "4.월별 신규&탈락생",
		5=> "5.월별 대리점정산순위");
$Sql_StandardDetailSub   = "select * from Branches A where A.BranchState=1";
$StandardDetailSubID     = "BranchID";
$StandardDetailSubName   = "BranchName";
$StandardDetailPlaceName = "지사선택";
#--------------------------------------------------------------------------------------------------------------------#
$ArrTotalResultCount   = [];
$ArrTotalSubResultCont = [];
$category_disname      = "";
$CurrYear = date('Y');
#====================================================================================================================#
#----------------------------------------------------- 지사 ----------------------------------------------------------#
#====================================================================================================================#
if($_ADMIN_LEVEL_ID_==9 || $_ADMIN_LEVEL_ID_==10) {  // 지사
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
                        where (A.ClassOrderPayProgress=21 or A.ClassOrderPayProgress=31 or A.ClassOrderPayProgress=41) and 
						       D.BranchID=$SearchStandardDetailSub and 
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
                        where (A.ClassOrderPayProgress=21 or A.ClassOrderPayProgress=31 or A.ClassOrderPayProgress=41) and 
						       D.BranchID=$SearchStandardDetailSub and 
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
                              A.MemberState=1 and 
							  D.BranchID=$SearchStandardDetailSub and 
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
                              A.MemberState=1 and 
						       D.BranchID=$SearchStandardDetailSub and  
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
                              A.MemberState=1 and 
						       D.BranchID=$SearchStandardDetailSub and 
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
      } else if($SearchStandardDetail=="5") {         // 대리점별 정산
      #--------------------------------------------------------------------------------------------------------------#
            $category_disname = "지사 ＞ 월별 대리점별 정산";
            #--------------------------------------------------------------------------------------------------------#
			$AddSqlWhere = "A.BranchID=" . $SearchStandardDetailSub . " and A.CenterState<>0  and B.BranchState<>0 and C.BranchGroupState<>0 ";
			
			$AddSqlWhere2 = "date_format(AAA.ClassOrderPayPaymentDateTime, '%Y-%m')='".$SearchEndDate."' ";
			$AddSqlWhere2 = $AddSqlWhere2 . " and (AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41) ";

			$ViewTable = "select A.CenterName, 

								(select sum(round(AAA.ClassOrderPayUseCashPrice-AAA.ClassOrderPayPgFeePrice-(AAA.ClassOrderPayUseCashPrice * AAA.ClassOrderPayPgFeeRatio / 100))) from ClassOrderPays AAA inner join Members BBB on AAA.ClassOrderPayPaymentMemberID=BBB.MemberID inner join Centers CCC on AAA.CenterID=CCC.CenterID where ".$AddSqlWhere2." and CCC.CenterID=A.CenterID) as TotalClassOrderPayUseCashPrice,

								(select sum((AAA.ClassOrderPayUseCashPrice-AAA.ClassOrderPayPgFeePrice-(AAA.ClassOrderPayUseCashPrice * AAA.ClassOrderPayPgFeeRatio / 100)) * ( (AAA.CenterPricePerTime - AAA.CompanyPricePerTime) / AAA.CenterPricePerTime )) * 0.967 from ClassOrderPays AAA inner join Members BBB on AAA.ClassOrderPayPaymentMemberID=BBB.MemberID inner join Centers CCC on AAA.CenterID=CCC.CenterID where ".$AddSqlWhere2." and CCC.CenterID=A.CenterID) as TotalBranchFee

							from Centers A 
								inner join Branches B on A.BranchID=B.BranchID 
								inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
							where ".$AddSqlWhere;

			$Sql = "select * from ($ViewTable) V order by V.TotalClassOrderPayUseCashPrice desc";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            while($Row = $Stmt->fetch()) {
                   $CenterName = $Row["CenterName"];
                   $ArrTotalResultCount[$CenterName] = $Row["TotalClassOrderPayUseCashPrice"];
            }
      #--------------------------------------------------------------------------------------------------------------#
      }  
#====================================================================================================================#
}
#====================================================================================================================#
$Search_Parameter = $SearchStandard . "-" . $SearchStandardDetail;

echo $SearchStandardDetailSub;
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
        <div class="md-card" style="margin-bottom:10px;">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin="">

                    <div class="uk-width-medium-2-10" style="padding-top:7px;">
                        <select id="SearchStandardDetail" name="SearchStandardDetail" onchange="SearchSubmit()" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="<?=$표시내용선택[$LangID]?>" style="width:100%;"/>
                            <option value=""></option>
                            <? for($i=1; $i<= count($ArrSearchStandardDetail);$i++) {?>
                            <option value="<?=$i?>" <?if ($SearchStandardDetail==$i){?>selected<?}?>><?=$ArrSearchStandardDetail[$i]?></option>
                            <?}?>
                        </select>
                    </div>

                    <div class="uk-width-medium-2-10" style="padding-top:7px;">
                        <select id="SearchStartYear" name="SearchStartYear" class="uk-width-1-1" data-placeholder="년도선택" style="width:100%;height:40px;"/>
                            <option value=""><?=$년도선택[$LangID]?></option>
                            <?
                            for ($iiii=2019;$iiii<=$CurrYear;$iiii++) {
                            ?>
                            <option value="<?=$iiii?>" <?if ($SearchStartYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
                            <?
                            }
                            ?>
                        </select>
                    </div>
                    <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideStartMonth==1,"none","")?>;">
                        <select id="SearchStartMonth" name="SearchStartMonth" class="uk-width-1-1" onchange="ChSearchStartMonth(1, this.value);" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;height:40px;"/>
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
                        <select id="SearchStartDay" name="SearchStartDay" onchange="SearchSubmit()" class="uk-width-1-1" data-placeholder="<?=$일선택[$LangID]?>" style="width:100%;height:40px;"/>
                            <option value=""><?=$일선택[$LangID]?></option>
                        </select>
                    </div>

                    <span style="padding-top: 15px; ">~</span>
                    
                    <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideEndYear==1,"none","")?>;">
                        <select id="SearchEndYear" name="SearchEndYear" class="uk-width-1-1" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%;height:40px;"/>
                            <option value=""><?=$년도선택[$LangID]?></option>
                            <?
                            for ($iiii=2019;$iiii<=$CurrYear;$iiii++) {
                            ?>
                            <option value="<?=$iiii?>" <?if ($SearchEndYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
                            <?
                            }
                            ?>
                        </select>
                    </div>
                    <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideEndMonth==1,"none","")?>;">
                        <select id="SearchEndMonth" name="SearchEndMonth" class="uk-width-1-1" onchange="ChSearchStartMonth(2, this.value);" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;height:40px;"/>
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
                        <select id="SearchEndDay" name="SearchEndDay" onchange="SearchSubmit()" class="uk-width-1-1" data-placeholder="<?=$일선택[$LangID]?>" style="width:100%;height:40px;"/>
                            <option value=""><?=$일선택[$LangID]?></option>
                        </select>
                    </div>
                    
                    <? 
                    $categorydate_find = "";
                    if ($SearchStartYear) {
                          
                          $categorydate_find .= $SearchStartYear;
                    
                    }
                    if ($SearchStartMonth and $SearchStandardDetail < 5) {
                    
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
                                  
                                  //echo $ArrTotalResultCount;
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
									(mainwhat==3 && (subwhat==2 || subwhat==3)) ||
									(mainwhat==4 && subwhat==4)) {
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
                                        valueAxis.title.text       = "단위 : %";
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
                                             /* sort temp array
                                             tempArray.sort(function(a, b) {
                                                   if (a.value > b.value) {
                                                       return 1;
                                                   }
                                                   else if (a.value < b.value) {
                                                       return -1
                                                   }
                                                   else {
                                                       return 0;
                                                   }
                                             })
                                             */

                                             // add quantity and count to middle data item (line series uses it)
                                             var lineSeriesDataIndex = Math.floor(count / 2);
                                             tempArray[lineSeriesDataIndex].quantity = providerData.quantity;
                                             tempArray[lineSeriesDataIndex].count    = count;
                                             // push to the final data
                                             am4core.array.each(tempArray, function(item) {
                                                  chartData.push(item);
                                             })

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
                                        valueAxis.title.text       = "<?=$수강료_수수료제외[$LangID]?>";
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
        alert("<?=$선택한_목록이_없습니다[$LangID]?>");
    }else{
        
        MemberIDs = "|";
        for (ii=1;ii<=ListCount;ii++){
            if (document.getElementById("CheckBox_"+ii).checked){
                MemberIDs = MemberIDs + document.getElementById("CheckBox_"+ii).value + "|";
            }   
        }

    
        if (MemberIDs=="|"){
            alert("<?=$선택한_목록이_없습니다[$LangID]?>");
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
    document.SearchForm.action = "account_graph_branch.php";
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