<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$AssmtStudentLeveltestScoreID = isset($_REQUEST["AssmtStudentLeveltestScoreID"]) ? $_REQUEST["AssmtStudentLeveltestScoreID"] : "";

$Sql = "select 
                A.*
        from AssmtStudentLeveltestScores A 
		where A.AssmtStudentLeveltestScoreID=$AssmtStudentLeveltestScoreID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ClassID = $Row["ClassID"];
$AssmtStudentLeveltestScoreYear = $Row["AssmtStudentLeveltestScoreYear"];
$AssmtStudentLeveltestScoreMonth = $Row["AssmtStudentLeveltestScoreMonth"];
$AssmtStudentLeveltestScoreDay = $Row["AssmtStudentLeveltestScoreDay"];
$AssmtStudentLeveltestScoreLevel = $Row["AssmtStudentLeveltestScoreLevel"];
$AssmtStudentLeveltestPass = $Row["AssmtStudentLeveltestPass"];
$AssmtStudentLeveltestScore1 = $Row["AssmtStudentLeveltestScore1"];
$AssmtStudentLeveltestScore2 = $Row["AssmtStudentLeveltestScore2"];
$AssmtStudentLeveltestScore3 = $Row["AssmtStudentLeveltestScore3"];
$AssmtStudentLeveltestScore4 = $Row["AssmtStudentLeveltestScore4"];
$AssmtStudentLeveltestScore5 = $Row["AssmtStudentLeveltestScore5"];
$AssmtStudentLeveltestScoreComment1 = $Row["AssmtStudentLeveltestScoreComment1"];
$AssmtStudentLeveltestScoreComment2 = $Row["AssmtStudentLeveltestScoreComment2"];
$AssmtStudentLeveltestScoreComment3 = $Row["AssmtStudentLeveltestScoreComment3"];
$AssmtStudentLeveltestScoreComment4 = $Row["AssmtStudentLeveltestScoreComment4"];
$AssmtStudentLeveltestScoreComment5 = $Row["AssmtStudentLeveltestScoreComment5"];
$AssmtStudentLeveltestScoreCommentTotal = $Row["AssmtStudentLeveltestScoreCommentTotal"];


$Sql = "select 
                A.*,
				B.ClassOrderTimeTypeID,
				C.MemberLoginID,
				C.MemberName,
				D.TeacherName
        from Classes A 
			inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID 
			inner join Members C on A.MemberID=C.MemberID 
			inner join Teachers D on A.TeacherID=D.TeacherID 
		where A.ClassID=$ClassID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ClassOrderID = $Row["ClassOrderID"];
$StartDateTime = $Row["StartDateTime"];
$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
$MemberLoginID = $Row["MemberLoginID"];
$MemberName = $Row["MemberName"];
$TeacherName = $Row["TeacherName"];
?>
<html>
<head>
    <meta charset="utf-8">
    <title>주저없는 선택 망고아이</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
    <link href="css/common.css" rel="stylesheet" type="text/css" />
    <link href="css/sub_style.css" rel="stylesheet" type="text/css" />
    <title></title>

<!-- Styles -->
<style>
#chartdiv {
  width: 100%;
  height: 500px;
}

</style>

<!-- Resources -->
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/kelly.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
    <div class="report_wrap">
        <h1 class="report_month_caption">
            <a href="javascript:PrintPage()" class="report_print_btn"><img src="images/icon_print_black.png" class="icon">Print</a>
			<trn class="TrnTag">레벨테스트 리포트</trn>
        </h1>
        <div class="report_studen_name">Student : <b><?=$MemberLoginID?> (<?=$MemberName?>)</b></div>


        
        <div class="report_top">
            <div class="report_top_left">

                <h3 class="report_caption_left">Leveltest Info</h3>
                <table class="report_table">
                    <col width="">
                    <col width="33%">
                    <col width="33%">
                    <tr>
                        <th class="bg_green_1">Level</th>
                        <th class="bg_green_1">Teacher</th>
                        <th class="bg_green_1">Date</th>
                    </tr>
                    <tr>
                        <td class="bg_green_2"><b>Level <?=$AssmtStudentLeveltestScoreLevel?></b></td>
                        <td class="bg_green_2"><b><?=$TeacherName?></b></td>
                        <td class="bg_green_2"><b><?=str_replace("-",".",substr($StartDateTime,0,10))?></b></td>
                    </tr>
                </table>
            </div>
            <div class="report_top_right">


				<?

				?>

                <h3 class="report_caption_left">Progress</h3>
                <table class="report_table">
                    <col width="25%">
                    <col width="25%">
                    <col width="25%">
                    <col width="25%">
                    <tr>
                        <th class="bg_pink_1">Total Score</th>
                        <th class="bg_pink_1">Percentile Score</th>
                        <th class="bg_pink_1">Result</th>
                    </tr>
                    <tr>
                        <td class="bg_pink_2"><b><?=round(($AssmtStudentLeveltestScore1+$AssmtStudentLeveltestScore2+$AssmtStudentLeveltestScore3+$AssmtStudentLeveltestScore4+$AssmtStudentLeveltestScore5)/5,0)?> / 10</b></td>
                        <td class="bg_pink_2"><b><?=round(($AssmtStudentLeveltestScore1+$AssmtStudentLeveltestScore2+$AssmtStudentLeveltestScore3+$AssmtStudentLeveltestScore4+$AssmtStudentLeveltestScore5)/5,0)*10?>%</b></td>
                        <td class="bg_pink_2">
							<?if ($AssmtStudentLeveltestPass==1){?>
							<b>PASS</b>
							<?}else{?>
							<b>FAIL</b>
							<?}?>
						</td>
                    </tr>
                </table>
            </div>
        </div>
		

        
        <h3 class="report_caption_left">Total Comment</h3>
        <div class="report_comment"><?=str_replace("\n","<br>",$AssmtStudentLeveltestScoreCommentTotal)?></div>
        <table class="report_table">
            <col width="20%">
            <col width="15%">
            <col width="">
            <tr>
                <th class="bg_yellow_1">Criteria</th>
                <th class="bg_yellow_2">Evaluation</th>
                <th class="bg_yellow_3">Comment</th>
            </tr>
            <tr>
                <td class="bg_yellow_1">Pronunciation</td>
                <td class="bg_yellow_2"><b><?=$AssmtStudentLeveltestScore1?>/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentLeveltestScoreComment1)?>
                </td>
            </tr>
            <tr>
                <td class="bg_yellow_1">Grammar</td>
                <td class="bg_yellow_2"><b><?=$AssmtStudentLeveltestScore2?>/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentLeveltestScoreComment2)?>
                </td>
            </tr>
            <tr>
                <td class="bg_yellow_1">Vocabulary</td>
                <td class="bg_yellow_2"><b><?=$AssmtStudentLeveltestScore3?>/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentLeveltestScoreComment3)?> 
                </td>
            </tr>
            <tr>
                <td class="bg_yellow_1">Attitude</td>
                <td class="bg_yellow_2"><b><?=$AssmtStudentLeveltestScore4?>/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentLeveltestScoreComment4)?>
                </td>
            </tr>
			<tr>
                <td class="bg_yellow_1">Fluency</td>
                <td class="bg_yellow_2"><b><?=$AssmtStudentLeveltestScore5?>/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentLeveltestScoreComment5)?>
                </td>
            </tr>
        </table>

        <div class="" >
            <div class="" id="chartdiv_3"><!--<img src="images/sample_chart_1.png" alt="차트" class="img">--></div>

			<style>
            
			#chartdiv_3 {
			  width: 320px; display:block;
			  height: 300px; margin:0 auto; text-align:center; padding:0;
			 }
                @media all and (min-width:480px){
                    #chartdiv_3{width:460px; height:440px;} 
                }
                @media all and (min-width:640px){
                    #chartdiv_3{width:550px; height:500px;} 
                }
			</style>

			<script>
			am4core.ready(function() {

			// Themes begin
			am4core.useTheme(am4themes_animated);
			// Themes end

			am4core.options.commercialLicense = true;

			/* Create chart instance */
			var chart = am4core.create("chartdiv_3", am4charts.RadarChart);

			/* Add data */
			chart.data = [ {
			  "direction": "Pronunciation",
			  "value": <?=$AssmtStudentLeveltestScore1?>
			}, {
			  "direction": "Grammar",
			  "value": <?=$AssmtStudentLeveltestScore2?>
			}, {
			  "direction": "Vocabulary",
			  "value": <?=$AssmtStudentLeveltestScore3?>
			}, {
			  "direction": "Attitude",
			  "value": <?=$AssmtStudentLeveltestScore4?>
			}, {
			  "direction": "Fluency",
			  "value": <?=$AssmtStudentLeveltestScore5?>
			} ];

			/* Create axes */
			var categoryAxis2 = chart.xAxes.push(new am4charts.CategoryAxis());
			categoryAxis2.dataFields.category = "direction";
			categoryAxis2.maximum = 10;

			var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
			//valueAxis.renderer.gridType = "polygons";
			valueAxis.max = 10;
			valueAxis.min = 0;

			var range = categoryAxis2.axisRanges.create();
			range.category = "Pronunciation";
			range.endCategory = "Grammar";
			range.axisFill.fill = am4core.color("#EFDEEA");
			range.axisFill.fillOpacity = 0.3;

			var range2 = categoryAxis2.axisRanges.create();
			range2.category = "Grammar";
			range2.endCategory = "Vocabulary";
			range2.axisFill.fill = am4core.color("#CDE0F3");
			range2.axisFill.fillOpacity = 0.3;

			var range3 = categoryAxis2.axisRanges.create();
			range3.category = "Vocabulary";
			range3.endCategory = "Attitude";
			range3.axisFill.fill = am4core.color("#DFFDE8");
			range3.axisFill.fillOpacity = 0.3;
			range3.locations.endCategory = 0;

			var range4 = categoryAxis2.axisRanges.create();
			range4.category = "Attitude";
			range4.endCategory = "Fluency";
			range4.axisFill.fill = am4core.color("#ff3333");
			range4.axisFill.fillOpacity = 0.3;
			range4.locations.endCategory = 0;

			/* Create and configure series */
			var series = chart.series.push(new am4charts.RadarSeries());
			series.dataFields.valueY = "value";
			series.dataFields.categoryX = "direction";
			series.name = "Wind direction";
			series.strokeWidth = 3;
			series.fillOpacity = 0.2;

			}); // end am4core.ready()
			</script>

		</div>	



    </div>











<script>
function PrintPage(){
	print();
}
</script>

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>