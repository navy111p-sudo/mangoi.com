<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";


$MemberID = $_LINK_MEMBER_ID_;
$ReportID = 1;

if ($MemberID==""){
	$MemberLoginID = "MANGOI";
	$MemberName = "망고아이";
}else{
	$Sql = "select 
					A.MemberID,
					A.MemberLoginID,
					A.MemberName
			from Members A 
			where A.MemberID=$MemberID  ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberLoginID = $Row["MemberLoginID"];
	$MemberName = $Row["MemberName"];
}


$AssmtStudentMonthlyScoreYear = 2019;
$AssmtStudentMonthlyScoreMonth = 9;


$ClassID = 0;
$Sql = "select 
                A.*
        from AssmtStudentMonthlyScores A 
		where A.ClassID=$ClassID 
		order by A.AssmtStudentMonthlyScoreID desc limit 0,1 ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$AssmtStudentMonthlyScoreLevel = $Row["AssmtStudentMonthlyScoreLevel"];
$AssmtStudentMonthlyScoreComment1 = $Row["AssmtStudentMonthlyScoreComment1"];
$AssmtStudentMonthlyScoreComment2 = $Row["AssmtStudentMonthlyScoreComment2"];
$AssmtStudentMonthlyScoreComment3 = $Row["AssmtStudentMonthlyScoreComment3"];
$AssmtStudentMonthlyScoreComment4 = $Row["AssmtStudentMonthlyScoreComment4"];
$AssmtStudentMonthlyScoreComment5 = $Row["AssmtStudentMonthlyScoreComment5"];
$AssmtStudentMonthlyScoreCommentTotal = $Row["AssmtStudentMonthlyScoreCommentTotal"];


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
			레벨테스트 리포트
        </h1>
        <div class="report_studen_name">Student : <b><?=$MemberLoginID?> (<?=$MemberName?>)</b></div>


        
        <div class="report_top">
            <div class="report_top_left">

				<?
				$Sql = "
					select 
						count(*) as AttendClassCount
					from Classes A
						inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID
					where A.ClassOrderID=(select ClassOrderID from Classes where ClassID=$ClassID)
						and A.StartYear=$AssmtStudentMonthlyScoreYear and A.StartMonth=$AssmtStudentMonthlyScoreMonth 
						and (A.ClassState=1 or A.ClassState=2)
				";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$AttendClassCount = $Row["AttendClassCount"];

				$Sql = "
					select 
						count(*) as AbsentClassCount
					from Classes A
						inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID
					where A.ClassOrderID=(select ClassOrderID from Classes where ClassID=$ClassID)
						and A.StartYear=$AssmtStudentMonthlyScoreYear and A.StartMonth=$AssmtStudentMonthlyScoreMonth 
						and (ClassState=3)
				";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$AbsentClassCount = $Row["AbsentClassCount"];


				$Sql = "
					select 
						sum(B.ClassOrderTimeTypeID*10) as AttendClassTime
					from Classes A
						inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID
					where A.ClassOrderID=(select ClassOrderID from Classes where ClassID=$ClassID)
						and A.StartYear=$AssmtStudentMonthlyScoreYear and A.StartMonth=$AssmtStudentMonthlyScoreMonth 
						and (A.ClassState=1 or A.ClassState=2)
				";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$AttendClassTime = $Row["AttendClassTime"];
				?>

                <h3 class="report_caption_left">Leveltest Info</h3>
                <table class="report_table">
                    <col width="">
                    <col width="33%">
                    <col width="33%">
                    <tr>
                        <th  class="bg_green_1">Level</th>
                        <th class="bg_green_1">Teacher</th>
                        <th class="bg_green_1">Date</th>
                    </tr>
                    <tr>
                        <td class="bg_green_2"><b>Level 5</b></td>
                        <td class="bg_green_2"><b>Kristina</b></td>
                        <td class="bg_green_2"><b>2019.07.28</b></td>
                    </tr>
                </table>
            </div>
            <div class="report_top_right">


				<?
				$Sql = "
					select 
						sum((AssmtStudentDailyScore1+AssmtStudentDailyScore2+AssmtStudentDailyScore3+AssmtStudentDailyScore4)/4) as AssmtStudentDailyScore
					from AssmtStudentDailyScores A
						inner join Classes B on A.ClassID=B.ClassID 
						inner join ClassOrders C on B.ClassOrderID=C.ClassOrderID
					where B.ClassOrderID=(select ClassOrderID from Classes where ClassID=$ClassID)
						and B.StartYear=$AssmtStudentMonthlyScoreYear and B.StartMonth=$AssmtStudentMonthlyScoreMonth 
				";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$AssmtStudentDailyScore = round($Row["AssmtStudentDailyScore"],0);

				$TotAssmtStudentDailyScore = 10 * $AttendClassCount;
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
                        <td class="bg_pink_2"><b>8 / 10</b></td>
                        <td class="bg_pink_2"><b>80%</b></td>
                        <td class="bg_pink_2"><b>PASS</b></td>
                    </tr>
                </table>
            </div>
        </div>
		

        
        <h3 class="report_caption_left">Total Comment</h3>
        <div class="report_comment"><?=str_replace("\n","<br>",$AssmtStudentMonthlyScoreCommentTotal)?></div>
		<?
		$Sql = "
			select 
				avg(AssmtStudentDailyScore1) as AvgAssmtStudentDailyScore1,
				avg(AssmtStudentDailyScore2) as AvgAssmtStudentDailyScore2,
				avg(AssmtStudentDailyScore3) as AvgAssmtStudentDailyScore3,
				avg(AssmtStudentDailyScore4) as AvgAssmtStudentDailyScore4,
				avg(AssmtStudentDailyScore5) as AvgAssmtStudentDailyScore5
			from AssmtStudentDailyScores A
				inner join Classes B on A.ClassID=B.ClassID 
				inner join ClassOrders C on B.ClassOrderID=C.ClassOrderID
			where B.ClassOrderID=(select ClassOrderID from Classes where ClassID=$ClassID)
				and B.StartYear=$AssmtStudentMonthlyScoreYear and B.StartMonth=$AssmtStudentMonthlyScoreMonth 
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
                <td class="bg_yellow_2"><b>7/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentMonthlyScoreComment1)?>
                </td>
            </tr>
            <tr>
                <td class="bg_yellow_1">Grammar</td>
                <td class="bg_yellow_2"><b>9/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentMonthlyScoreComment2)?>
                </td>
            </tr>
            <tr>
                <td class="bg_yellow_1">Vocabulary</td>
                <td class="bg_yellow_2"><b>10/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentMonthlyScoreComment3)?> 
                </td>
            </tr>
            <tr>
                <td class="bg_yellow_1">Attitude</td>
                <td class="bg_yellow_2"><b>7/10</b></td>
                <td class="text_left bg_yellow_3">
                    <?=str_replace("\n","<br>",$AssmtStudentMonthlyScoreComment4)?>
                </td>
            </tr>
			<tr>
                <td class="bg_yellow_1">Fluency</td>
                <td class="bg_yellow_2"><b>8/10</b></td>
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
			  "value": 7
			}, {
			  "direction": "Grammar",
			  "value": 9
			}, {
			  "direction": "Vocabulary",
			  "value": 10
			}, {
			  "direction": "Attitude",
			  "value": 7
			}, {
			  "direction": "Fluency",
			  "value": 8
			} ];

			/* Create axes */
			var categoryAxis2 = chart.xAxes.push(new am4charts.CategoryAxis());
			categoryAxis2.dataFields.category = "direction";

			var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
			//valueAxis.renderer.gridType = "polygons";

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

			var range3 = categoryAxis2.axisRanges.create();
			range3.category = "Attitude";
			range3.endCategory = "Fluency";
			range3.axisFill.fill = am4core.color("#ff3333");
			range3.axisFill.fillOpacity = 0.3;
			range3.locations.endCategory = 0;

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