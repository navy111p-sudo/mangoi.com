<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

// 에러를 모두 출력하는 코드. 에러를 잡기 위해.
// error_reporting(E_ALL);
// ini_set('display_errors','1');
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
<script type="text/javascript" src="../amcharts4/core.js"></script>
<script type="text/javascript" src="../amcharts4/charts.js"></script>
<script type="text/javascript" src="../amcharts4/themes/animated.js"></script>

<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 88;
$SubMenuID = 8863;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
#-----------------------------------------------------------------------------------------------------------------------------------------#
$MemberLoginID     = isset($_COOKIE["LoginMemberID"   ]) ? $_COOKIE["LoginMemberID"   ] : "";
$SearchState       = isset($_REQUEST["SearchState"    ]) ? $_REQUEST["SearchState"    ] : "";
$MemberID          = isset($_REQUEST["MemberID"    ]) ? $_REQUEST["MemberID"    ] : "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 회원 고유아이디(번호, 조직아이디) 찾기
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "select T.*,M.* from Members as M 
			  left join Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
			      where M.MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
if ($MemberID != "") $Stmt->bindParam(':MemberLoginID', $MemberID);
    else $Stmt->bindParam(':MemberLoginID', $MemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$My_MemberID     = $Row["MemberID"];
$My_MemberName   = $Row["MemberName"];
$My_OrganLevel   = $Row["Hr_OrganLevel"];    
$My_OrganLevelID = $Row["Hr_OrganLevelID"];
$My_OrganTask2ID = $Row["Hr_OrganTask2ID"]; 
#-----------------------------------------------------------------------------------------------------------------------------------------#
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom">[<?=$My_MemberName?>] 
        <?
		if ($My_OrganLevel >= 1 and $My_OrganLevel <= 4) {
		?>
				<a type="button" href="hr_staff_indicator_list.php" class="md-btn md-btn-primary" style="background:#2196F3"><?=$개인[$LangID]?></a>
        <?
            if ($My_OrganLevel <= 3) {
            ?>        
                    <a type="button" href="hr_staff_organ_indicator_list.php" class="md-btn md-btn-primary" style="background:#6FB9F7"><?=$부문[$LangID]?></a>
            <? }
                echo $평가결과[$LangID];
		//} 
        // else if ($My_OrganLevel==1) {   // 최종상사인 경우
        //         echo $부문[$LangID]." ".$평가결과[$LangID];
		} else {
                echo $평가결과[$LangID];
		}
		?>
    
        </h3>

		<form name="SearchForm" method="get">
            <input type='hidden' name='MemberID' value='<?=$MemberID?>'>
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
					$line_cnt = 0;
                    #---------------------------------------------------------------------------------------------------------------------#
					if ($SearchState) { 
                    #---------------------------------------------------------------------------------------------------------------------#
                        // 소속된 조직의 점수를 가지고 오기 위한 쿼리
                        $Sql = "SELECT AVG(Hr_EndEvaluationPoint) AS EP, AVG(Hr_EvaluationCompetencyEndPoint) AS CP, AVG(Hr_ResultTotalPoint) AS RP  
                                    FROM Hr_Staff_ResultEvaluation AS A 
                                    WHERE A.Hr_EvaluationID = :Hr_EvaluationID AND A.MemberID IN 
                                    (SELECT MemberID FROM Hr_OrganLevelTaskMembers 
                                        WHERE Hr_OrganLevelID = :Hr_OrganLevelID)";
                        $Stmt = $DbConn->prepare($Sql);
                        $Stmt->bindParam(':Hr_OrganLevelID', $My_OrganLevelID);
                        $Stmt->bindParam(':Hr_EvaluationID', $SearchState);
                        $Stmt->execute();
                        $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                        $Row = $Stmt->fetch();


                        $OrgEP = isset($Row["EP"])?$Row["EP"]:0;  //조직 전체의 업적 평균 점수
                        $OrgCP = isset($Row["CP"])?$Row["CP"]:0;  //조직 전체의 역량 평균 점수
                        $OrgRP = isset($Row["RP"])?$Row["RP"]:0;  //조직 전체의 성과 평균 점수


					       ?>       
                            <h4>▷ <?=$성과평가결과[$LangID]?></h4>
                            
                            <div class='uk-width-1-2' style='float:left'>
                            
							<table class="uk-table uk-table-align-vertical">
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="25%"><?=$업적평가점수[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="25%"><?=$역량평가점수[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="25%"><?=$성과평가종합점수[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="25%"><?=$성과평가_종합평가_등급[$LangID]?></td>
                                </tr>
                                <tbody>
                            <?
                            #-------------------------------------------------------------------------------------------------------------#
							$Sql = "select * from Hr_Staff_ResultEvaluation 
										    where MemberID=".$My_MemberID." and 
												  Hr_EvaluationID=".$SearchState." ";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
							$Row = $Stmt->fetch();
							$Stmt = null;
                            #-------------------------------------------------------------------------------------------------------------#
					        if ($Row["Hr_ResultTotalPoint"] > 0)  {
                            #-------------------------------------------------------------------------------------------------------------#
							       ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_EndEvaluationPoint"]?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_EvaluationCompetencyEndPoint"]?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_ResultTotalPoint"]?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_ResultLevel"]?></td>
                                </tr>
                                   <?
                            #-------------------------------------------------------------------------------------------------------------#
							} else {
                            #-------------------------------------------------------------------------------------------------------------#
									?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" colspan=4><?=$성과결과_자료가_없습니다[$LangID]?></td>
                                </tr>
									<?
							#-------------------------------------------------------------------------------------------------------------#
					        }
							#-------------------------------------------------------------------------------------------------------------#
							?>
                                </tbody>
                            </table>
                            <div> &#8251 <?=$업적평가_설명1[$LangID]?><br>
                            &#8251 <?=$업적평가_설명2[$LangID]?>  
                            </div>
                            </div>
                            <div class="report_chart_1" style="height: 300px">
                                <div id="chartdiv_1"></div>
                                <ul class="report_chart_mark" >
                                    <li style="margin-left:20px;margin-top:0px;list-style:none;"><span style="color:#69B7DA;">■</span> <?=$본인[$LangID]?></li>
                                    <li style="margin-left:20px;margin-top:10px;list-style:none;"><span style="color:#F1A2B5;">■</span> <?=$부서[$LangID]?></li>
                                </ul>
                            </div>  
                            <?    
                            $extHeight = 0;
                            if (isset($Row["Hr_EndEvaluationPoint"])) {
                                $extHeight = 300;// 하단의 크기를 키우기 위해
                            ?>
                            <div style="position:relative;">
                                <div id="chartdivPie" style="position:absolute;left:0;top:0;width: 40%; height: 300px;"></div>
                                <div class="report_chart_mark" style="position:absolute;top:220px;left:28%;" >
                                    <table class="uk-table uk-table-align-vertical" style="width:150px;">
                                        <tr><td align=center>성과 점수</td></tr>
                                        <tr><td align=center><?=isset($Row["Hr_ResultTotalPoint"])?$Row["Hr_ResultTotalPoint"]:"-"?></td></tr>
                                    </table>
                                </div>
                                <div id="chartdivPie2" style="position:absolute;right:100px;top:0;width: 40%; height: 300px;"></div>
                                
                            </div>   

                            <script>

                            am4core.ready(function() {

                                // Themes begin
                                am4core.useTheme(am4themes_animated);
                                // Themes end
                                am4core.options.commercialLicense = true;
                                // Create chart instance
                                var chart = am4core.create("chartdivPie", am4charts.PieChart);
                                
                                // Add data
                                chart.data = [{
                                "type": "<?=$업적평가[$LangID]?>",
                                "point": <?=isset($Row["Hr_EndEvaluationPoint"])?$Row["Hr_EndEvaluationPoint"]:0?>,
                                "color": am4core.color("#cce8f3")
                                }, {
                                "type": "<?=$역량평가[$LangID]?>",
                                "point": <?=isset($Row["Hr_EvaluationCompetencyEndPoint"])?$Row["Hr_EvaluationCompetencyEndPoint"]:0?>,
                                "color": am4core.color("#efb7b4")
                                }, 

                                ];

                                // Add and configure Series
                                var pieSeries = chart.series.push(new am4charts.PieSeries());
                                pieSeries.dataFields.value = "point";
                                pieSeries.dataFields.category = "type";
                                pieSeries.slices.template.propertyFields.fill = "color";


                                // var label = chart.chartContainer.createChild(am4core.Label);
                                // label.text = "성과 평가 결과(개인)";
                                // label.align = "center";
                                var title = chart.titles.create();
                                title.text = "성과평가 결과(개인)";
                                title.fontSize = 16;
                                title.marginBottom = 2;
                            });    
                            

                            am4core.ready(function() {

                                // Themes begin
                                am4core.useTheme(am4themes_animated);
                                // Themes end
                                am4core.options.commercialLicense = true;
                                // Create chart instance
                                var chart = am4core.create("chartdivPie2", am4charts.PieChart);

                                // Add data
                                chart.data = [{
                                "type": "<?=$업적평가[$LangID]?>",
                                "point": <?=$OrgEP?>,
                                "color": am4core.color("#cce8f3")
                                }, {
                                "type": "<?=$역량평가[$LangID]?>",
                                "point": <?=$OrgCP?>,
                                "color": am4core.color("#efb7b4")
                                }, 

                                ];

                                // Add and configure Series
                                var pieSeries = chart.series.push(new am4charts.PieSeries());
                                pieSeries.dataFields.value = "point";
                                pieSeries.dataFields.category = "type";
                                pieSeries.slices.template.propertyFields.fill = "color";


                                // var label = chart.chartContainer.createChild(am4core.Label);
                                // label.text = "성과 평가 결과(개인)";
                                // label.align = "center";
                                var title = chart.titles.create();
                                title.text = "성과평가 결과(부서평균)";
                                title.fontSize = 16;
                                title.marginBottom = 2;
                                });   

                            </script> 
                            <? } ?>
                            <style>
                                #chartdiv_1 {
                                    float:left;
                                    width: 40%;
                                    height: 300px;
                                }    
                            </style>

                            <script>
                                am4core.ready(function() {

                                // Themes begin
                                am4core.useTheme(am4themes_animated);
                                // Themes end

                                am4core.options.commercialLicense = true;

                                // Create chart instance
                                var chart = am4core.create("chartdiv_1", am4charts.XYChart);

                                // Add data
                                chart.data = [{
                                    "division": "<?=$업적평가[$LangID]?>",
                                    "mine": <?=isset($Row["Hr_EndEvaluationPoint"])?$Row["Hr_EndEvaluationPoint"]:0?>,
                                    "organization": <?=$OrgEP?>,
                                }, {
                                    "division": "<?=$역량평가[$LangID]?>",
                                    "mine": <?=isset($Row["Hr_EvaluationCompetencyEndPoint"])?$Row["Hr_EvaluationCompetencyEndPoint"]:0?>,
                                    "organization": <?=$OrgCP?>,
                                }, {
                                    "division": "<?=$성과평가[$LangID]?>",
                                    "mine": <?=isset($Row["Hr_ResultTotalPoint"])?$Row["Hr_ResultTotalPoint"]:0?>,
                                    "organization": <?=$OrgRP?>,
                                } ];

                                // Create axes
                                var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                                categoryAxis.dataFields.category = "division";
                                categoryAxis.renderer.grid.template.location = 0;
                                categoryAxis.renderer.minGridDistance = 30;

                                var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                                valueAxis.title.text = "점수";
                                valueAxis.title.fontWeight = 800;

                                // Create series
                                var series = chart.series.push(new am4charts.ColumnSeries());
                                series.dataFields.valueY = "mine";
                                series.dataFields.categoryX = "division";
                                //series.clustered = false;
                                series.columns.template.width = am4core.percent(45);
                                series.tooltipText = "점수: [bold]{valueY}[/]";

                                var series2 = chart.series.push(new am4charts.ColumnSeries());
                                series2.dataFields.valueY = "organization";
                                series2.dataFields.categoryX = "division";
                                //series2.clustered = false;
                                series2.columns.template.width = am4core.percent(45);
                                series2.columns.template.fill = am4core.color("#F1A2B5");
                                series2.stroke = am4core.color("#F1A2B5");
                                series2.tooltipText = "평균: [bold]{valueY}[/]";

                                chart.cursor = new am4charts.XYCursor();
                                chart.cursor.lineX.disabled = true;
                                chart.cursor.lineY.disabled = true;

                                }); // end am4core.ready()
                            </script>

                            <br>    
                            <div style='float:none;display:block;height:<?=$extHeight?>px;'>&nbsp;</div>
                            <h4 class="uk-width-medium-1-1">▷ <?=$업적평가결과[$LangID]?></h4>
							<table class="uk-table uk-table-align-vertical">
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="30%"><?=$KPI_핵심성과지표[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="15%"><?=$자기평가[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="15%"><?=$P1차상사[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="15%"><?=$P2차상사[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="15%"><?=$최종상사[$LangID]?><br><?=$평가점수[$LangID]?></td>
                                </tr>
                                <tbody>
                            <?
                            #-------------------------------------------------------------------------------------------------------------#
							$Sql = "select count(*) TotalRowCount from Hr_Staff_Target 
											 where MemberID=".$My_MemberID." and 
												   Hr_EvaluationID=".$SearchState." and
												   Hr_TargetState='9' and 
												   Hr_UseYN='Y'";
							$Stmt = $DbConn->prepare($Sql);
						    $Stmt->execute();
						    $Row = $Stmt->fetch();
						    $TotalRowCount = $Row["TotalRowCount"];
							$Stmt = null;
                            #-------------------------------------------------------------------------------------------------------------#
							$Sql = "select * from Hr_Staff_Target 
											 where MemberID=".$My_MemberID." and 
												   Hr_EvaluationID=".$SearchState." and
												   Hr_TargetState='9' and 
												   Hr_UseYN='Y'";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
							$line_cnt = 0;
                            #-------------------------------------------------------------------------------------------------------------#
							while ($Row = $Stmt->fetch()) {
                            #-------------------------------------------------------------------------------------------------------------#
							        $Hr_TargetID        = $Row["Hr_TargetID"       ];
								    $Hr_KpiIndicatorID  = $Row["Hr_KpiIndicatorID" ];
                                    $Hr_TargetName      = $Row["Hr_TargetName"     ]; 
                                    $Hr_TargetAddValue  = $Row["Hr_TargetAddValue" ]; 
                                    $Hr_SelfPoint       = $Row["Hr_ChangePoint"    ]; 
                                    $Hr_FirstBossPoint  = $Row["Hr_FirstBossPoint" ]; 
                                    $Hr_SecondBossPoint = $Row["Hr_SecondBossPoint"]; 
									$Hr_EndTotalPoint   = $Row["Hr_EndTotalPoint"  ]; 
                                    
									$Hr_SelfComment       = $Row["Hr_SelfComment"      ];  
									$Hr_FirstBossComment  = $Row["Hr_FirstBossComment" ]; 
									$Hr_SecondBossComment = $Row["Hr_SecondBossComment"]; 
									$Hr_EndBossComment    = $Row["Hr_EndBossComment"   ]; 
                                    #-----------------------------------------------------------------------------------------------------#
                                    $Sql2 = "SELECT A.*,
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
                                            $Hr_KpiIndicatorName     = $Row2["Hr_KpiIndicatorName"];
                                            #---------------------------------------------------------------------------------------------#
											$line_cnt++;
                                            ?>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_KpiIndicatorName?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=$Hr_SelfPoint?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=($Hr_FirstBossPoint!=0)?$Hr_FirstBossPoint:"-"?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-center">
										   <?=($Hr_SecondBossPoint!=0)?$Hr_SecondBossPoint:"-"?>
                                        </td>
                                           <?
										   if ($line_cnt==1) {
										   ?>
                                        <td class="uk-text-wrap uk-table-td-center" rowspan="<?=$TotalRowCount?>">
										   <?=($Hr_EndTotalPoint!=0)?$Hr_EndTotalPoint:"-"?>
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
                            if ($line_cnt > 0) { 
							?>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;">
										   <?=$업적평가_총평[$LangID]?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-left" style="background:#F6F6F6; vertical-align:top;">
										   <?=$Hr_SelfComment?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-left" style="background:#F6F6F6; vertical-align:top;">
										   <?=$Hr_FirstBossComment?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-left" style="background:#F6F6F6; vertical-align:top;">
										   <?=$Hr_SecondBossComment?>
                                        </td>
                                        <td class="uk-text-wrap uk-table-td-left" style="background:#F6F6F6; vertical-align:top;">
										   <?=$Hr_EndBossComment?>
                                        </td>
                                    </tr>
									<?
							} else {
							        ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" colspan=5><?=$업적결과_자료가_없습니다[$LangID]?></td>
                                </tr>
									<?
							} 
							?>
                                </tbody>
                            </table>
                            <h4>▷ <?=$역량평가_결과[$LangID]?></h4>
                            <?php
                            // 로그인한 사용자의 직무별 역량 카테고리2와 카테고리1 가져오기 

                            // 1차 카테고리의 갯수 세기 (표 그릴 때 1차 카테고리 영역 표시하기 위해)
                            // 1차 카테고리 갯수를 연계배열에 입력한다.
                            $Sql3 = "SELECT B.Hr_CompetencyIndicatorCate1ID AS CATE1ID, Count(B.Hr_CompetencyIndicatorCate1ID) AS CATE1COUNT
                                        FROM Hr_CompetencyIndicatorTasks AS A
                                        LEFT JOIN Hr_CompetencyIndicatorCate2 AS B ON A.Hr_CompetencyIndicatorCate2ID = B.Hr_CompetencyIndicatorCate2ID
                                        LEFT JOIN Hr_CompetencyIndicatorCate1 AS C ON B.Hr_CompetencyIndicatorCate1ID = C.Hr_CompetencyIndicatorCate1ID
                                        WHERE A.Hr_OrganTask2ID = :Hr_OrganTask2ID
                                        GROUP BY(B.Hr_CompetencyIndicatorCate1ID)";
                            $Stmt3 = $DbConn->prepare($Sql3);
                            $Stmt3->bindParam(':Hr_OrganTask2ID', $My_OrganTask2ID);
                            $Stmt3->execute();
                            $Stmt3->setFetchMode(PDO::FETCH_ASSOC);
                            
                            while($Row3 = $Stmt3->fetch()) {
                                $cate1[$Row3["CATE1ID"]] = (int)$Row3["CATE1COUNT"];
                            }

                            ?>
                            <div id="chartdiv_2"></div>
                            <div id="chartdivPie3" style="float:left;width: 45%; height: 400px;margin-bottom:10px;"></div>
                            <table class="uk-table uk-table-align-vertical" style="width:100%;">
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="30%" colspan="2"><?=$역량[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="15%"><?=$부하평가[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="15%"><?=$동료평가[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="15%"><?=$상사평가[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="15%"><?=$역량별_점수[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F1A2B5;" width="15%"><?=$역량평가_점수[$LangID]?></td>
                                </tr>
                            <tbody>

                            <?

                            // 2차 카테고리별로 카테고리 이름을 가지고 온다. while 루프를 돌면서 실제 표를 그린다. 
                            $Sql3 = "SELECT B.Hr_CompetencyIndicatorCate2Name, C.Hr_CompetencyIndicatorCate1Name, 
                                            B.Hr_CompetencyIndicatorCate2ID, B.Hr_CompetencyIndicatorCate1ID 
                                        FROM Hr_CompetencyIndicatorTasks AS A
                                        LEFT JOIN Hr_CompetencyIndicatorCate2 AS B ON A.Hr_CompetencyIndicatorCate2ID = B.Hr_CompetencyIndicatorCate2ID
                                        LEFT JOIN Hr_CompetencyIndicatorCate1 AS C  ON B.Hr_CompetencyIndicatorCate1ID = C.Hr_CompetencyIndicatorCate1ID
                                        WHERE A.Hr_OrganTask2ID = :Hr_OrganTask2ID
                                        ORDER BY C.Hr_CompetencyIndicatorCate1ID, B.Hr_CompetencyIndicatorCate2ID";

                            $Stmt3 = $DbConn->prepare($Sql3);
                            $Stmt3->bindParam(':Hr_OrganTask2ID', $My_OrganTask2ID);
                            $Stmt3->execute();
                            $Stmt3->setFetchMode(PDO::FETCH_ASSOC);
                            $countLine = $Stmt3->rowCount();  // 출력한 라인 갯수를 가져온다.
                            $rowspanCount = 1;  // rowspan 을 처리하기 위한 카운트
                            $sumOfPoint = 0;    // 전체 점수 합산
                            $firstLine = true; //제일 마지막 합계 점수 부분 처리하기 위한 불린


                            $sumPointPerType1 =0; // 부하평가 합산
                            $sumPointPerType2 =0; // 동료평가 합산
                            $sumPointPerType3 =0; // 동료평가 합산
                            $sumAvgOfPoint =0;    // 역량별점수 합산

                            while($Row3 = $Stmt3->fetch()) {
                                $Cate1ID        = $Row3["Hr_CompetencyIndicatorCate1ID"];
                                $Cate1Name      = $Row3["Hr_CompetencyIndicatorCate1Name"];
                                $Cate2ID        = $Row3["Hr_CompetencyIndicatorCate2ID"];
                                $Cate2Name      = $Row3["Hr_CompetencyIndicatorCate2Name"];
                            ?>    
                                <tr>
                            <?
                                if ($rowspanCount == 1) {
                                    echo ("
                                    <td class='uk-text-wrap uk-table-td-center' rowspan='".$cate1[$Cate1ID]."'>
                                        ".$Cate1Name."
                                    </td>
                                    ");
                                }

                                // 상사, 부하, 동료별 점수를 가져오기 위한 쿼리
                                $Sql4 = "SELECT Hr_EvaluationCompetencyMemberType,AVG(Hr_CompetencyIndicatorPoint) AS AVGPOINT
                                            FROM Hr_Staff_Compentency AS A 
                                            LEFT JOIN Hr_CompetencyIndicators AS B ON A.Hr_CompetencyIndicatorID = B.Hr_CompetencyIndicatorID
                                            LEFT JOIN Hr_EvaluationCompetencyMembers as C ON A.MemberID = C.MemberID
                                            WHERE A.MemberID = :MemberID and B.Hr_CompetencyIndicatorCate2ID = :Cate2ID  and A.Hr_EvaluationID = :Hr_EvaluationID 
                                                    AND A.Hr_CompetencyIndicatorState = 9
                                            GROUP BY Hr_EvaluationCompetencyMemberType
                                        ";

                                $Stmt4 = $DbConn->prepare($Sql4);
                                $Stmt4->bindParam(':MemberID', $My_MemberID);
                                $Stmt4->bindParam(':Cate2ID', $Cate2ID);
                                $Stmt4->bindParam(':Hr_EvaluationID', $SearchState);
                                $Stmt4->execute();
                                $Stmt4->setFetchMode(PDO::FETCH_ASSOC);
                                
                                while($Row4 = $Stmt4->fetch()){
                                    $pointPerType[$Row4["Hr_EvaluationCompetencyMemberType"]] = $Row4["AVGPOINT"];
                                    
                                }
                                $sumOfPointOfType = 0;
                                $checkCount = 0;
                                if (isset($pointPerType[1])) {
                                    $checkCount++; 
                                    $sumOfPointOfType+=$pointPerType[1];
                                }    
                                if (isset($pointPerType[2])) {
                                    $checkCount++; 
                                    $sumOfPointOfType+=$pointPerType[2];
                                }                                    
                                if (isset($pointPerType[3])) {
                                    $checkCount++; 
                                    $sumOfPointOfType+=$pointPerType[3];
                                }    


                                if ($checkCount != 0) {
                                    $avgOfPoint = round(($sumOfPointOfType / $checkCount),2);
                                    $typePoint[$Cate2Name] = $avgOfPoint;
                                } else {
                                    $avgOfPoint =0;
                                    $typePoint["업무혁신"] = 0;
                                } 
                                

                                // 토탈 점수 합산
                                $sumOfPoint += $avgOfPoint;


                                // 조직의 점수를 가져오기 위한 쿼리
                                $Sql = "SELECT AVG(Hr_CompetencyIndicatorPoint) AS AVGPOINT
                                            FROM Hr_Staff_Compentency AS A LEFT JOIN Hr_CompetencyIndicators AS B
                                            ON A.Hr_CompetencyIndicatorID = B.Hr_CompetencyIndicatorID
                                            LEFT JOIN Hr_EvaluationCompetencyMembers as C ON A.MemberID = C.MemberID
                                            WHERE B.Hr_CompetencyIndicatorCate2ID = :Cate2ID  and A.Hr_EvaluationID = :Hr_EvaluationID 
                                                    AND A.MemberID IN (SELECT MemberID FROM Hr_OrganLevelTaskMembers WHERE Hr_OrganLevelID = :Hr_OrganLevelID)
                                            ";
                                $Stmt = $DbConn->prepare($Sql);
                                $Stmt->bindParam(':Hr_OrganLevelID', $My_OrganLevelID);
                                $Stmt->bindParam(':Cate2ID', $Cate2ID);
                                $Stmt->bindParam(':Hr_EvaluationID', $SearchState);
                                $Stmt->execute();
                                $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                                $Row = $Stmt->fetch();

                                $OrganAvg[] = round($Row["AVGPOINT"]);


                            ?>
                                    <td class="uk-text-wrap uk-table-td-center">
                                        <?=$Cate2Name?>
                                    </td>
                                    <td class="uk-text-wrap uk-table-td-center">
                                        <?=isset($pointPerType[1])?round($pointPerType[1],2):"-"?>
                                    </td>
                                    <td class="uk-text-wrap uk-table-td-center">
                                        <?=isset($pointPerType[2])?round($pointPerType[2],2):"-"?>
                                    </td>
                                    <td class="uk-text-wrap uk-table-td-center">
                                        <?=isset($pointPerType[3])?round($pointPerType[3],2):"-"?>
                                    </td>
                                    <td class="uk-text-wrap uk-table-td-center">
                                        <?=($avgOfPoint!=0)?$avgOfPoint:"-"?>
                                    </td>
                            <? $sumPointPerType1 += isset($pointPerType[1])?$pointPerType[1]:0;
                               $sumPointPerType2 += isset($pointPerType[2])?$pointPerType[2]:0;
                               $sumPointPerType3 += isset($pointPerType[3])?$pointPerType[3]:0;
                               $sumAvgOfPoint += isset($avgOfPoint)?$avgOfPoint:0;
                            if ($firstLine) {
                                
                                $firstLine = false;
                            ?>
                                    <td class="uk-text-wrap uk-table-td-center" bgcolor="#F1A2B5" rowspan='<?=($countLine+2)?>' >
                                        <div id='my_div'>
                                            <span>-</span>
                                        </div>
                                        
                                    </td>
                            <? } ?>        
                                </tr>
                            <?
                                if ($rowspanCount == $cate1[$Cate1ID]) $rowspanCount = 1;
                                    else $rowspanCount++;
                            }
                            ?>
                            <tr>
                                <td class="uk-text-wrap uk-table-td-center" colspan=2>총점</td>
                                <td class="uk-text-wrap uk-table-td-center"><?=round($sumPointPerType1,2)?></td>
                                <td class="uk-text-wrap uk-table-td-center"><?=round($sumPointPerType2,2)?></td>
                                <td class="uk-text-wrap uk-table-td-center"><?=round($sumPointPerType3,2)?></td>
                                <td class="uk-text-wrap uk-table-td-center"><?=round($sumAvgOfPoint,2)?></td>
                            </tr>
                            <tr>
                                <td class="uk-text-wrap uk-table-td-center" colspan=2>평균</td>
                                <td class="uk-text-wrap uk-table-td-center" style="color:red;"><?=round($sumPointPerType1/$countLine,2)?></td>
                                <td class="uk-text-wrap uk-table-td-center" style="color:red;"><?=round($sumPointPerType2/$countLine,2)?></td>
                                <td class="uk-text-wrap uk-table-td-center" style="color:red;"><?=round($sumPointPerType3/$countLine,2)?></td>
                                <td class="uk-text-wrap uk-table-td-center" style="color:red;"><?=round($sumAvgOfPoint/$countLine,2)?></td>
                            </tr>
                            </tbody>
                            </table>

                            <script>
                                // 최종 합계를 계산해서 추후에 넣기 위한 스크립트
                                const element = document.getElementById('my_div');
                                element.innerText = '<?=(isset($sumOfPoint) && $sumOfPoint !=0)?round(($sumOfPoint/$countLine),2):"-"?>';
                            </script>


                            
                            <style>
                                #chartdiv_2 {
                                float:left;
                                width: 45%;
                                height: 400px;
                            }
                            </style>

                            <script>
                            am4core.ready(function() {

                            // Themes begin
                            am4core.useTheme(am4themes_animated);
                            // Themes end

                            am4core.options.commercialLicense = true;

                            /* Create chart instance */
                            var chart = am4core.create("chartdiv_2", am4charts.RadarChart);

                            /* 그래프에 그려질 데이타를 넣어준다. */
                            chart.data = [ 
                            <?
                            $t=0;
                            foreach ($typePoint as $key => $value) {
                                echo "{'direction': '".$key."', 'value': ".$value.", 'value2': ".$OrganAvg[$t]."},";
                                $t++;
                            }
                            ?>        
                            ];

                            chart.legend = new am4charts.Legend();

                            /* Create axes */
                            var categoryAxis2 = chart.xAxes.push(new am4charts.CategoryAxis());
                            categoryAxis2.dataFields.category = "direction";

                            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                            valueAxis.min = 0;
                            valueAxis.max = 5;
                            valueAxis.strictMinMax = true;
                            valueAxis.renderer.minGridDistance = 20;


                            /* Create and configure series */
                            var series = chart.series.push(new am4charts.RadarSeries());
                            series.dataFields.valueY = "value";
                            series.dataFields.categoryX = "direction";
                            series.name = "<?=$개인[$LangID]?>";
                            series.strokeWidth = 3;
                            series.fillOpacity = 0.2;


                            /* Create and configure series */
                            var series2 = chart.series.push(new am4charts.RadarSeries());
                            series2.dataFields.valueY = "value2";
                            
                            series2.dataFields.categoryX = "direction";
                            series2.name = "<?=$부서평균[$LangID]?>";
                            series2.strokeWidth = 3;
                            series2.fillOpacity = 0.2;
                            series2.stroke = am4core.color("#f16cb3");
                            series2.fill = am4core.color("#F1A2B5");

                            }); // end am4core.ready()
                            </script>

                            
    
                                <script>
    
                                am4core.ready(function() {
    
                                    // Themes begin
                                    am4core.useTheme(am4themes_animated);
                                    // Themes end
                                    am4core.options.commercialLicense = true;
                                    // Create chart instance
                                    var chart = am4core.create("chartdivPie3", am4charts.PieChart);
                                    
                                    // Add data
                                    chart.data = [
                                        <? if($sumPointPerType1>0) { ?>
                                        {
                                        "type": "부하",
                                        "point": <?=round($sumPointPerType1/$countLine,2)?>,
                                        "color": am4core.color("#cce8f3")
                                        }, 
                                        <? } ?>
                                        <? if($sumPointPerType2>0) { ?>
                                        {
                                        "type": "동료",
                                        "point": <?=round($sumPointPerType2/$countLine,2)?>,
                                        "color": am4core.color("#efb7b4")
                                        }, 
                                        <? } ?>
                                        <? if($sumPointPerType3>0) { ?>
                                        {
                                        "type": "상사",
                                        "point": <?=round($sumPointPerType3/$countLine,2)?>,
                                        "color": am4core.color("#87CEEB")
                                        }, 
                                        <? } ?>
                                    ];

                                    chart.legend = new am4charts.Legend();
    
                                    // Add and configure Series
                                    var pieSeries = chart.series.push(new am4charts.PieSeries());
                                    pieSeries.dataFields.value = "point";
                                    pieSeries.dataFields.category = "type";
                                    pieSeries.slices.template.propertyFields.fill = "color";
    
    
                                    // var label = chart.chartContainer.createChild(am4core.Label);
                                    // label.text = "성과 평가 결과(개인)";
                                    // label.align = "center";
                                    var title = chart.titles.create();
                                    title.text = "<?=$역량평가_평가자_비중[$LangID]?>";
                                    title.fontSize = 16;
                                    title.marginBottom = 2;
                                });    
                                </script>
    
                                


                            <!---------- 역량평가 세부 점수표 --------------------->


							<table class="uk-table uk-table-align-vertical">
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="12%"><?=$가중치_합계[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="12%"><?=$등급[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="12%"><?=$최종점수[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="12%"><?=$가중치_반영점수[$LangID]?></td>
                                </tr>
                                <tbody>
                            <?
                            #-------------------------------------------------------------------------------------------------------------#
							$Sql = "select * from Hr_Staff_ResultEvaluation 
										    where MemberID=".$My_MemberID." and 
												  Hr_EvaluationID=".$SearchState." and
												  Hr_EvaluationCompetencyEndPoint is NOT NULL";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
							$Row = $Stmt->fetch();
							$Stmt = null;
                            #-------------------------------------------------------------------------------------------------------------#
					        if ($Row["Hr_EvaluationCompetencyEndPoint"] > 0)  {
                            #-------------------------------------------------------------------------------------------------------------#
							       ?>
                                <tr>                                                     
                                   <td class="uk-text-wrap uk-table-td-center"><?=round($Row["Hr_EvaluationCompetencyAddTotalPoint"],2)?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_EvaluationCompetencyLevel"]?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_EvaluationCompetencyEndPoint"]?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_ResultPoint2"]?></td>
                                </tr>
                                   <?
                            #-------------------------------------------------------------------------------------------------------------#
							} else {
                            #-------------------------------------------------------------------------------------------------------------#
									?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" colspan=4><?=$역량평가_자료가_없습니다[$LangID]?></td>
                                </tr>
									<?
							#-------------------------------------------------------------------------------------------------------------#
					        }
							#-------------------------------------------------------------------------------------------------------------#
							?>
                                </tbody>
                            </table>

                            <h4>▷ <?=$성과평가_월별[$LangID]?></h4>

                            <?
                            $Sql = "SELECT
                                        A.Hr_EvaluationYear, A.Hr_EvaluationMonth, A.Hr_EvaluationID, B.Hr_ResultTotalPoint, B.Hr_ResultLevel
                                        FROM Hr_Evaluations AS A LEFT JOIN (SELECT * FROM Hr_Staff_ResultEvaluation WHERE MemberID = :MemberID) AS B
                                        ON A.Hr_EvaluationID = B.Hr_EvaluationID
                                        WHERE A.Hr_EvaluationState=1 AND DATE_ADD(Hr_EvaluationDate, INTERVAL 1 YEAR) >= now() 
                                        ORDER BY Hr_EvaluationYear, Hr_EvaluationMonth, Hr_EvaluationDate 
                                        LIMIT 12";
                            $Stmt = $DbConn->prepare($Sql);
                            $Stmt->bindParam(':MemberID', $My_MemberID);
                            $Stmt->execute();
                            $countLine = $Stmt->rowCount();  // 전체 갯수를 가져온다.
                            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                            $yearRow = array();
                            $monthRow = array();
                            $pointRow = array();
                            $levelRow = array();
                            $evaluationIDRow = array();
                            $organRow = array();

                            while($Row = $Stmt->fetch()){
                                array_push($yearRow, $Row["Hr_EvaluationYear"]);
                                array_push($monthRow, $Row["Hr_EvaluationMonth"]);
                                array_push($pointRow, $Row["Hr_ResultTotalPoint"]);
                                array_push($levelRow, $Row["Hr_ResultLevel"]);
                                array_push($evaluationIDRow, $Row["Hr_EvaluationID"]);
                                
                                // 소속된 조직의 점수를 가지고 오기 위한 쿼리
                                $Sql2 = "SELECT AVG(Hr_ResultTotalPoint) AS ORGAN_AVG
                                            FROM Hr_Staff_ResultEvaluation AS A 
                                            WHERE A.Hr_EvaluationID = :Hr_EvaluationID AND A.MemberID IN 
                                            (SELECT MemberID FROM Hr_OrganLevelTaskMembers 
                                                WHERE Hr_OrganLevelID = :Hr_OrganLevelID)";
                                $Stmt2 = $DbConn->prepare($Sql2);
                                $Stmt2->bindParam(':Hr_OrganLevelID', $My_OrganLevelID);
                                $Stmt2->bindParam(':Hr_EvaluationID', $Row["Hr_EvaluationID"]);
                                $Stmt2->execute();
                                $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                                $Row2 = $Stmt2->fetch();
                                array_push($organRow, $Row2["ORGAN_AVG"]); 
                            }


                            ?>
                            <table class="uk-table uk-table-align-vertical">
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="16%" rowspan="2"><?=$항목[$LangID]?></td>
                                   <?
                                    for ($i=0; $i<$countLine; $i++) {
                                    ?>
                                    <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="7%"><?=$yearRow[$i]?>년</td>
                                    <?
                                    }
                                   ?>
                                </tr>
                                <tr>
                                   <?
                                    for ($i=0; $i<$countLine; $i++) {
                                    ?>
                                    <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="7%"><?=$monthRow[$i]?>월</td>
                                    <?
                                    }
                                   ?>
                                </tr>
                                <tbody>                                
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center"  width="7%"><?=$개인_종합점수[$LangID]?></td>
                                   <?
                                    for ($i=0; $i<$countLine; $i++) {
                                    ?>
                                    <td class="uk-text-wrap uk-table-td-center" width="7%"><?=isset($pointRow[$i])?$pointRow[$i]:"-";?></td>
                                    <?
                                    }
                                   ?>
                                </tr>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" width="7%" ><?=$개인_평가등급[$LangID]?></td>
                                   <?
                                    for ($i=0; $i<$countLine; $i++) {
                                    ?>
                                    <td class="uk-text-wrap uk-table-td-center" width="7%"><?=isset($levelRow[$i])?$levelRow[$i]:"-";?></td>
                                    <?
                                    }
                                   ?>
                                </tr>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" width="7%" ><?=$부서_종합점수[$LangID]?></td>
                                   <?
                                    for ($i=0; $i<$countLine; $i++) {
                                    ?>
                                    <td class="uk-text-wrap uk-table-td-center" width="7%"><?=isset($organRow[$i])?round($organRow[$i],2):"-";?></td>
                                    <?
                                    }
                                   ?>
                                </tr>
                                </tbody>
                            </table>    
                            <div id="chartdiv3"></div>
                            <style>
                            #chartdiv3 {
                                width: 100%;
                                height: 350px;
                                }
                            </style>

                            <script>
                                am4core.useTheme(am4themes_animated);
                                
                                am4core.options.commercialLicense = true;

                                // Create chart instance
                                var chart = am4core.create("chartdiv3", am4charts.XYChart);

                                // Add data
                                chart.data = [
                                <?    
                                for ($i=0;$i<$countLine;$i++) {
                                    if (isset($pointRow[$i])) $point = $pointRow[$i];
                                        else $point = 0;
                                    if (isset($organRow[$i])) $organPoint = $organRow[$i];
                                        else $organPoint = 0;    
                                    echo "
                                        {
                                            'value': ".$point.",
                                            'value2': ".$organPoint.",
                                            'date': '".$yearRow[$i]."년 ".$monthRow[$i]."월' 
                                        },    
                                    ";
                                } 
                                ?>
                                ];

                                //chart.dateFormatter.inputDateFormat = "yyyy/mm";

                                // Create axes
                                var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                                categoryAxis.dataFields.category = "date";
                                //categoryAxis.renderer.grid.template.location = 0;
                                //categoryAxis.renderer.minGridDistance = 30;

                                var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

                                // Create series
                                var series = chart.series.push(new am4charts.LineSeries());
                                series.name = '<?=$개인_종합점수[$LangID]?>';
                                var series2 = chart.series.push(new am4charts.LineSeries());
                                series2.name = '<?=$부서_종합점수[$LangID]?>';

                                series.dataFields.valueY = "value";
                                series2.dataFields.valueY = "value2";
                                series.dataFields.categoryX = "date";
                                series2.dataFields.categoryX = "date";
                                series.tooltipText = "{categoryX}: [b]{valueY}[/]";
                                series2.tooltipText = "{categoryX}: [b]{valueY}[/]";
                                series.strokeWidth = 2;
                                series2.strokeWidth = 2;
                                series2.stroke = am4core.color("#F1A2B5");
                                series2.tooltip.getFillFromObject = false;
                                series2.tooltip.background.fill = am4core.color("#F1A2B5");

                                var bullet = series.bullets.push(new am4charts.CircleBullet());
                                bullet.circle.stroke = am4core.color("#fff");
                                bullet.circle.strokeWidth = 2;

                                chart.cursor = new am4charts.XYCursor();
                                chart.legend = new am4charts.Legend();


                            </script>

                            <?
                    #---------------------------------------------------------------------------------------------------------------------#
					} else { 
                    #---------------------------------------------------------------------------------------------------------------------#
							?>
                        <div class="uk-form-row" style="text-align:center; padding:30px; color:#BCBCBC; font-size:1.5em;">
                             <?=$먼저_평가리스트를_선택하시면_성과결과를_볼_수_있습니다[$LangID]?>
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
	document.SearchForm.action = "hr_staff_indicator_list.php";
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