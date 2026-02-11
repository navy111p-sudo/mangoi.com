<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$AssmtStudentMonthlyScoreID = isset($_REQUEST["AssmtStudentMonthlyScoreID"]) ? $_REQUEST["AssmtStudentMonthlyScoreID"] : "";


$Sql = "select 
                A.*
        from AssmtStudentMonthlyScores A 
		where A.AssmtStudentMonthlyScoreID=$AssmtStudentMonthlyScoreID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ClassID = $Row["ClassID"];
$AssmtStudentMonthlyScoreSubject = $Row["AssmtStudentMonthlyScoreSubject"];
$AssmtStudentMonthlyScoreYear = $Row["AssmtStudentMonthlyScoreYear"];
$AssmtStudentMonthlyScoreMonth = $Row["AssmtStudentMonthlyScoreMonth"];
$AssmtStudentMonthlyScoreLevel = $Row["AssmtStudentMonthlyScoreLevel"];
$AssmtStudentMonthlyScoreComment1 = $Row["AssmtStudentMonthlyScoreComment1"];
$AssmtStudentMonthlyScoreComment2 = $Row["AssmtStudentMonthlyScoreComment2"];
$AssmtStudentMonthlyScoreComment3 = $Row["AssmtStudentMonthlyScoreComment3"];
$AssmtStudentMonthlyScoreComment4 = $Row["AssmtStudentMonthlyScoreComment4"];
$AssmtStudentMonthlyScoreComment5 = $Row["AssmtStudentMonthlyScoreComment5"];
$AssmtStudentMonthlyScoreCommentTotal = $Row["AssmtStudentMonthlyScoreCommentTotal"];



$Sql = "select 
                A.*,
				B.ClassOrderTimeTypeID,
				C.MemberLoginID,
				C.MemberName 
        from Classes A 
			inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID 
			inner join Members C on A.MemberID=C.MemberID 
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
			<?=$AssmtStudentMonthlyScoreSubject?> (<?=$AssmtStudentMonthlyScoreYear?>.<?=substr("0".$AssmtStudentMonthlyScoreMonth,-2)?>)
        </h1>
        <div class="report_studen_name">Student : <b><?=$MemberLoginID?> (<?=$MemberName?>)</b></div>


        
        <div class="report_top">
            <div class="report_top_left">

				<?
				$Sql = "select 
							A.* 
						from Classes A 
						where 
							A.ClassOrderID=".$ClassOrderID." 
							and timestampdiff(minute, A.StartDateTime, '".$StartDateTime."')>0 
							and A.ClassState=2 
						order by A.StartDateTime desc limit 0, 8";

				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);

				$AttendClassCount = 0;
				$AbsentClassCount = 0;
				$SqlWhereClassID = "";
				$MinStartDateTimeStamp = 0;
				$MaxStartDateTimeStamp = 0;
				while($Row = $Stmt->fetch()) {
					$ClassAttendState = $Row["ClassAttendState"];
					$ClassID = $Row["ClassID"];
					$StartDateTimeStamp = $Row["StartDateTimeStamp"];

					if ($MaxStartDateTimeStamp==0){
						$MaxStartDateTimeStamp = $StartDateTimeStamp;
					}
					$MinStartDateTimeStamp = $StartDateTimeStamp;
					
					
					if ($ClassAttendState==1 || $ClassAttendState==2){
						$AttendClassCount++;

						if ($SqlWhereClassID!=""){
							$SqlWhereClassID = $SqlWhereClassID . " or ";
						}

						$SqlWhereClassID = $SqlWhereClassID . " A.ClassID=".$ClassID." ";

					}else if ($ClassAttendState==3){
						$AbsentClassCount++;
					}

				}
				$Stmt = null;
				if ($SqlWhereClassID!=""){
					
					$SqlWhereClassID = "(" . $SqlWhereClassID. ")";
				
				}

				$AttendClassCountTime = ($ClassOrderTimeTypeID * 10) * $AttendClassCount;
				$AbsentClassCountTime = ($ClassOrderTimeTypeID * 10) * $AbsentClassCount;
				?>

                <h3 class="report_caption_left">Attendance</h3>
                <table class="report_table">
                    <col width="">
                    <col width="33%">
                    <col width="33%">
                    <tr>
                        <th class="bg_green_1">Percentage</th>
                        <th class="bg_green_1">Times</th>
                        <th class="bg_green_1">Minutes</th>
                    </tr>
                    <tr>
                        <td class="bg_green_2">
							<?if ($AttendClassCount+$AbsentClassCount==0){?>
								<b>-</b>
							<?}else{?>
								<b><?=round(100*$AttendClassCount/($AttendClassCount+$AbsentClassCount),0)?>%</b>
							<?}?>
						</td>
                        <td class="bg_green_2">
							<?if ($AttendClassCount+$AbsentClassCount==0){?>
								<b>-</b>
							<?}else{?>
								<b><?=$AttendClassCount?>/(<?=$AttendClassCount+$AbsentClassCount?>)</b>
							<?}?>
						</td>
                        <td class="bg_green_2"><b><?=number_format($AttendClassCountTime,0)?>min / <?=number_format($AttendClassCountTime+$AbsentClassCountTime,0)?>min</b></td>
                    </tr>
                </table>
            </div>
            <div class="report_top_right">


				<?
				if ($SqlWhereClassID!=""){
					$Sql = "
						select 
							sum((AssmtStudentDailyScore1 + AssmtStudentDailyScore2 + AssmtStudentDailyScore3 + AssmtStudentDailyScore4 + AssmtStudentDailyScore5 )/5) as AssmtStudentDailyScore
						from AssmtStudentDailyScores A
						where ".$SqlWhereClassID."
					";

					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					$Row = $Stmt->fetch();
					$Stmt = null;
					$AssmtStudentDailyScore = round($Row["AssmtStudentDailyScore"],0);

					$TotAssmtStudentDailyScore = 10 * $AttendClassCount;
				}else{
					$AssmtStudentDailyScore = 0;
					$TotAssmtStudentDailyScore = 0;
				}
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
                        <th class="bg_pink_1">Level</th>
                    </tr>
                    <tr>
                        <td class="bg_pink_2">
							<?if ($TotAssmtStudentDailyScore==0){?>
								<b>-</b>
							<?}else{?>
								<b><?=$AssmtStudentDailyScore?> / <?=$TotAssmtStudentDailyScore?></b>
							<?}?>
						</td>
                        <td class="bg_pink_2">
							<?if ($TotAssmtStudentDailyScore==0){?>
								<b>-</b>
							<?}else{?>
								<b><?=round(100*$AssmtStudentDailyScore/$TotAssmtStudentDailyScore,0)?>%</b>
							<?}?>
						</td>
                        <td class="bg_pink_2"><b><?=$AssmtStudentMonthlyScoreLevel?> LEVEL</b></td>
                    </tr>
                </table>
            </div>
        </div>
		
		<div class="report_top">


				<?
				if ($SqlWhereClassID!=""){
					
					$Sql = "
						select 
							count(*) as VideoCount
						from ClassVideoPlayLogs A
						where ".$SqlWhereClassID."
					";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					$Row = $Stmt->fetch();
					$Stmt = null;
					$VideoCount = round($Row["VideoCount"],0);

					$Sql = "
						select 
							count(*) as QuizCount
						from BookQuizResults A
						where ".$SqlWhereClassID."
					";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					$Row = $Stmt->fetch();
					$Stmt = null;
					$QuizCount = round($Row["QuizCount"],0);
				
				}else{

					$VideoCount = 0;
					$QuizCount = 0;

				}
				
				?>
                <h3 class="report_caption_left">Lesson & Review</h3>
                <table class="report_table">
                    <col width="">
                    <col width="50%">
                    <tr>
                        <th class="bg_yellow_1">Lesson Video</th>
                        <th class="bg_yellow_1">Review Quiz</th>
                    </tr>
                    <tr>
                        <td class="bg_yellow_2"><b><?=$VideoCount?></b></td>
                        <td class="bg_yellow_2"><b><?=$QuizCount?></b></td>
                    </tr>
                </table>
        </div>
        
        <h3 class="report_caption_left">Total Comment</h3>
        <div class="report_comment"><?=str_replace("\n","<br>",$AssmtStudentMonthlyScoreCommentTotal)?></div>
		<?
		if ($SqlWhereClassID!=""){

			$Sql = "
				select 
					avg(AssmtStudentDailyScore1) as AvgAssmtStudentDailyScore1,
					avg(AssmtStudentDailyScore2) as AvgAssmtStudentDailyScore2,
					avg(AssmtStudentDailyScore3) as AvgAssmtStudentDailyScore3,
					avg(AssmtStudentDailyScore4) as AvgAssmtStudentDailyScore4,
					avg(AssmtStudentDailyScore5) as AvgAssmtStudentDailyScore5
				from AssmtStudentDailyScores A
				where ".$SqlWhereClassID." 
			";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;
			$AvgAssmtStudentDailyScore1 = round($Row["AvgAssmtStudentDailyScore1"],0);
			$AvgAssmtStudentDailyScore2 = round($Row["AvgAssmtStudentDailyScore2"],0);
			$AvgAssmtStudentDailyScore3 = round($Row["AvgAssmtStudentDailyScore3"],0);
			$AvgAssmtStudentDailyScore4 = round($Row["AvgAssmtStudentDailyScore4"],0);
			$AvgAssmtStudentDailyScore5 = round($Row["AvgAssmtStudentDailyScore5"],0);

		}else{
			$AvgAssmtStudentDailyScore1 = 0;
			$AvgAssmtStudentDailyScore2 = 0;
			$AvgAssmtStudentDailyScore3 = 0;
			$AvgAssmtStudentDailyScore4 = 0;
			$AvgAssmtStudentDailyScore5 = 0;
		}


		$Sql = "
			select 
				avg(AssmtStudentDailyScore1) as AvgAssmtStudentDailyScore1,
				avg(AssmtStudentDailyScore2) as AvgAssmtStudentDailyScore2,
				avg(AssmtStudentDailyScore3) as AvgAssmtStudentDailyScore3,
				avg(AssmtStudentDailyScore4) as AvgAssmtStudentDailyScore4,
				avg(AssmtStudentDailyScore5) as AvgAssmtStudentDailyScore5
			from AssmtStudentDailyScores A
			where A.ClassID in (select ClassID from Classes where StartDateTimeStamp>=".$MinStartDateTimeStamp." and StartDateTimeStamp<=".$MaxStartDateTimeStamp." and ClassState=2) 
		";



		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$AllAvgAssmtStudentDailyScore1 = round($Row["AvgAssmtStudentDailyScore1"],0);
		$AllAvgAssmtStudentDailyScore2 = round($Row["AvgAssmtStudentDailyScore2"],0);
		$AllAvgAssmtStudentDailyScore3 = round($Row["AvgAssmtStudentDailyScore3"],0);
		$AllAvgAssmtStudentDailyScore4 = round($Row["AvgAssmtStudentDailyScore4"],0);
		$AllAvgAssmtStudentDailyScore5 = round($Row["AvgAssmtStudentDailyScore5"],0);
		?>
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
                <td class="bg_yellow_2"><b><?=$AvgAssmtStudentDailyScore1?>/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentMonthlyScoreComment1)?>
                </td>
            </tr>
            <tr>
                <td class="bg_yellow_1">Grammar</td>
                <td class="bg_yellow_2"><b><?=$AvgAssmtStudentDailyScore2?>/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentMonthlyScoreComment2)?>
                </td>
            </tr>
            <tr>
                <td class="bg_yellow_1">Vocabulary</td>
                <td class="bg_yellow_2"><b><?=$AvgAssmtStudentDailyScore3?>/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentMonthlyScoreComment3)?> 
                </td>
            </tr>
            <tr>
                <td class="bg_yellow_1">Attitude</td>
                <td class="bg_yellow_2"><b><?=$AvgAssmtStudentDailyScore4?>/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentMonthlyScoreComment4)?>
                </td>
            </tr>
			<tr>
                <td class="bg_yellow_1">Fluency</td>
                <td class="bg_yellow_2"><b><?=$AvgAssmtStudentDailyScore5?>/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentMonthlyScoreComment5)?>
                </td>
            </tr>
        </table>

        <div class="report_chart_wrap">
            <div class="report_chart_1" id="chartdiv_1"><!--<img src="images/sample_chart_1.png" alt="차트" class="img">--></div>

			<style>
			#chartdiv_1 {
			  width: 100%;
			  height: 300px;
			}
			</style>

			<script>
			am4core.ready(function() {

			// Themes begin
			am4core.useTheme(am4themes_animated);
			// Themes end

			am4core.options.commercialLicense = true;

			/* Create chart instance */
			var chart = am4core.create("chartdiv_1", am4charts.RadarChart);

			/* Add data */
			chart.data = [ {
			  "direction": "Pronunciation",
			  "value": <?=$AvgAssmtStudentDailyScore1?>
			}, {
			  "direction": "Grammar",
			  "value": <?=$AvgAssmtStudentDailyScore2?>
			}, {
			  "direction": "Vocabulary",
			  "value": <?=$AvgAssmtStudentDailyScore3?>
			}, {
			  "direction": "Attitude",
			  "value": <?=$AvgAssmtStudentDailyScore4?>
			}, {
			  "direction": "Fluency",
			  "value": <?=$AvgAssmtStudentDailyScore5?>
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



            <div class="report_chart_2" id="chartdiv_2"></div>
			<div style="line-height:1.5;"><span style="color:#6894D9;">■</span> 나의 점수 <span style="color:#69B7DA;">■</span> 평균</div>

			<style>
			#chartdiv_2 {
			  width: 100%;
			  height: 270px;
			}
			</style>

			<!-- Chart code -->
			<script>
			am4core.ready(function() {

			// Themes begin
			am4core.useTheme(am4themes_animated);
			// Themes end

			am4core.options.commercialLicense = true;

			// Create chart instance
			var chart = am4core.create("chartdiv_2", am4charts.XYChart);

			// Add percent sign to all numbers
			//chart.numberFormatter.numberFormat = "#.#'%'";

			// Add data
			chart.data = [{
				"country": "Pronunciation",
				"AvgScore": <?=$AllAvgAssmtStudentDailyScore1 * 10?>,
				"MyScore": <?=$AvgAssmtStudentDailyScore1 * 10?>
			}, {
				"country": "Grammar",
				"AvgScore": <?=$AllAvgAssmtStudentDailyScore2 * 10?>,
				"MyScore": <?=$AvgAssmtStudentDailyScore2 * 10?>
			}, {
				"country": "Vocabulary",
				"AvgScore": <?=$AllAvgAssmtStudentDailyScore3 * 10?>,
				"MyScore": <?=$AvgAssmtStudentDailyScore3 * 10?>
			}, {
				"country": "Attitude",
				"AvgScore": <?=$AllAvgAssmtStudentDailyScore4 * 10?>,
				"MyScore": <?=$AvgAssmtStudentDailyScore4 * 10?>
			}, {
				"country": "Fluency",
				"AvgScore": <?=$AllAvgAssmtStudentDailyScore5 * 10?>,
				"MyScore": <?=$AvgAssmtStudentDailyScore5 * 10?>
			}, {
				"country": "Average",
				"AvgScore": <?=round( ($AllAvgAssmtStudentDailyScore1 + $AllAvgAssmtStudentDailyScore2 + $AllAvgAssmtStudentDailyScore3 + $AllAvgAssmtStudentDailyScore4 + $AllAvgAssmtStudentDailyScore5) / 5 * 10, 0)?>,
				"MyScore": <?=round( ($AvgAssmtStudentDailyScore1 + $AvgAssmtStudentDailyScore2 + $AvgAssmtStudentDailyScore3 + $AvgAssmtStudentDailyScore4 + $AvgAssmtStudentDailyScore5) / 5 * 10, 0)?>
			}];

			// Create axes
			var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
			categoryAxis.dataFields.category = "country";
			categoryAxis.renderer.grid.template.location = 0;
			categoryAxis.renderer.minGridDistance = 30;

			var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
			valueAxis.title.text = "점수";
			valueAxis.title.fontWeight = 800;
			valueAxis.max = 100;
			valueAxis.min = 0;

			// Create series
			var series = chart.series.push(new am4charts.ColumnSeries());
			series.dataFields.valueY = "AvgScore";
			series.dataFields.categoryX = "country";
			series.clustered = false;
			series.tooltipText = "점수: [bold]{valueY}[/]";

			var series2 = chart.series.push(new am4charts.ColumnSeries());
			series2.dataFields.valueY = "MyScore";
			series2.dataFields.categoryX = "country";
			series2.clustered = false;
			series2.columns.template.width = am4core.percent(50);
			series2.tooltipText = "평균: [bold]{valueY}[/]";

			chart.cursor = new am4charts.XYCursor();
			chart.cursor.lineX.disabled = true;
			chart.cursor.lineY.disabled = true;

			}); // end am4core.ready()
			</script>

        </div>
    </div>







</section>



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