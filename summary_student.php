<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');
?>
<html>
<head>
    <meta charset="utf-8">
    <title>주저없는 선택 망고아이</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
    <link href="css/common.css" rel="stylesheet" type="text/css" />
    <link href="css/sub_style.css" rel="stylesheet" type="text/css" />

	<script src="https://www.amcharts.com/lib/4/core.js"></script>
	<script src="https://www.amcharts.com/lib/4/charts.js"></script>
	<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

</head>
<?
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";

if ($SearchYear==""){
	$SearchYear = date("Y");
}

$Sql = "
		select 
				A.*,
				B.CenterPayType,
				B.CenterStudyEndDate
		from Members A 
			inner join Centers B on A.CenterID=B.CenterID 
		where A.MemberID=:MemberID
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$MemberName = $Row["MemberName"];
$MemberNickName = $Row["MemberNickName"];
$MemberRegDateTime = substr($Row["MemberRegDateTime"],0,10);

$MemberPayType = $Row["MemberPayType"];
$CenterPayType = $Row["CenterPayType"];
$CenterStudyEndDate = $Row["CenterStudyEndDate"];




if ($CenterPayType==2 || ($CenterPayType==1 && $MemberPayType==1)){//개인결제


	$Sql2 = "select 
					A.* 
			from ClassOrders A 
			where A.MemberID=$MemberID  and (A.ClassOrderState=1 or A.ClassOrderState=2 or A.ClassOrderState=4) and A.ClassProgress=11 and A.ClassProductID=1 
			order by A.ClassOrderEndDate asc";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

	$kkk=0;
	$StrClassOrderEndDate = "";
	while($Row2 = $Stmt2->fetch()) {
		if ($kkk>0){
			$StrClassOrderEndDate = $StrClassOrderEndDate . ", ";
		}

		$ClassOrderEndDate = $Row2["ClassOrderEndDate"];
		
		if ($ClassOrderEndDate=="0000-00-00" || $ClassOrderEndDate==""){
			$ClassOrderEndDate = "[미설정]";
		}else{
			$ClassOrderEndDateDiff = (strtotime($ClassOrderEndDate) - strtotime(date("Y-m-d"))) / 86400;
			if ($ClassOrderEndDateDiff<=7){
				$ClassOrderEndDate = "<span style='color:#ff0000;'>".$ClassOrderEndDate." (".$ClassOrderEndDateDiff."일)</span>";
			}
		}
		$StrClassOrderEndDate .= $ClassOrderEndDate;
		
		
		$kkk++;
	}
	$Stmt2 = null;

}else{
	$StrClassOrderEndDate = $CenterStudyEndDate;
}

if ($StrClassOrderEndDate==""){
	$StrClassOrderEndDate = "-";
}
?>
<body>
<?
include_once('./includes/common_body_top.php');
?>
    <div class="summary_wrap">
        <h1 class="summary_title"><b><?=$MemberName?></b> 요약정보</h1>  
        
        <form name="SearchForm" method="get" onchange="SearchFormSubmit()">
		<input type="hidden" name="MemberID" id="MemberID" value="<?=$MemberID?>">
		<select class="summary_select" name="SearchYear" id="SearchYear">
            <?for ($ii=$SearchYear-1;$ii<=$SearchYear+1;$ii++){?>
			<option value="<?=$ii?>" <?if ($ii==$SearchYear){?>selected<?}?>><?=$ii?></option>
			<?}?>
        </select>
		</form>

		<script>
		function SearchFormSubmit(){
			document.SearchForm.action = "summary_student.php";
			document.SearchForm.submit();
		}
		</script>
        
        <section class="summary_section info">
            <div class="summary_photo" style="background-image:url(images/no_photo.png);"></div>
            <div class="summary_info">
                한글 : <?=$MemberName?><br>
                영문 : <?=$MemberNickName?><br>
                가입일 : <?=$MemberRegDateTime?> / 학습종료일 : <?=$StrClassOrderEndDate?>
            </div>
        </section>
        
		<?
		$Sql = "
				select 
						A.*,
						C.BookName
				from Classes A 
					left outer join BookScans B on A.BookScanID=B.BookScanID 
					left outer join Books C on B.BookID=C.BookID

				where A.MemberID=:MemberID and A.ClassState=2 and StartYear=".$SearchYear."

				order by A.StartDateTime desc limit 0, 1
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$ClassLevel = $Row["ClassLevel"];
		$BookName = $Row["BookName"];
		if (!$BookName){
			$BookName = "-";
		}
	
		
		$Sql = "
				select 
						ifnull(round(avg((A.AssmtStudentDailyScore1+A.AssmtStudentDailyScore2+A.AssmtStudentDailyScore3+A.AssmtStudentDailyScore4+A.AssmtStudentDailyScore5)/5*10)),0) as AvgAssmtStudentDailyScore
				from AssmtStudentDailyScores A 
				where A.ClassID in (select ClassID from Classes where MemberID=$MemberID and ClassState=2 and StartYear=".$SearchYear.")
		";


		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$AvgAssmtStudentDailyScore = $Row["AvgAssmtStudentDailyScore"];



		$Sql = "
				select 
					count(*) as AttendCount
				from Classes A 
				where 
					A.MemberID=:MemberID 
					and A.ClassState=2 
					and (A.ClassAttendState=1 or A.ClassAttendState=2)
					and A.StartYear=".$SearchYear."
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$AttendCount = $Row["AttendCount"];

		$Sql = "
				select 
					count(*) as AbsentCount
				from Classes A 
				where 
					A.MemberID=:MemberID 
					and A.ClassState=2 
					and A.ClassAttendState=3
					and A.StartYear=".$SearchYear."
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$AbsentCount = $Row["AbsentCount"];
		
		$TotalAttendCount = $AttendCount + $AbsentCount; 
		if ($TotalAttendCount>0){
			$AttendRatio = round(($AttendCount / $TotalAttendCount)*100,0);
		}else{
			$AttendRatio = 0;
		}
		?>

        <section class="summary_section">
            <h3 class="summary_caption_left">학습정보</h3> 
            <table class="summary_table">
                <colgroup span="4" width="25%"></colgroup>
                <tr>
                    <th>현재레벨</th>
                    <th>사용교재</th>
                    <th>출석율</th>
                    <th>평균점수</th>
                </tr>
                <tr>
                    <td>
						<?if ($ClassLevel==0){?>
							-
						<?}else{?>
							<?=$ClassLevel?> 레벨
						<?}?>
					</td>
                    <td><?=$BookName?></td>
                    <td><?=$AttendRatio?>%</td>
                    <td><?=$AvgAssmtStudentDailyScore?>점 / 100점</td>
                </tr>
            </table>
        </section>
        
        <section class="summary_section">
            <h3 class="summary_caption_left">최근학습결과</h3> 
            <table class="summary_table small">
                <colgroup span="5" width="14.28%"></colgroup>
                <tr>
                    <th>날짜</th>
                    <th>강사</th>
                    <th>출석</th>
                    <th>레슨<div class="break">비디오</div>시청</th>
                    <th>퀴즈풀이<div class="break">점수</div>(1회차)</th>
                    <th>셀프<div class="break">체크</div>점수</th>
                    <th>강사<div class="break">채점</div>점수</th>
                </tr>
				<?
				$Sql = "
					select 
						A.*,
						B.TeacherName,
						ifnull((select count(*) from ClassVideoPlayLogs where ClassID=A.ClassID),0) as ClassVideoPlayCount, 
						ifnull((select avg(AA.MyScore) from BookQuizResultDetails AA inner join BookQuizResults BB on AA.BookQuizResultID=BB.BookQuizResultID where BB.ClassID=A.ClassID and BB.QuizStudyNumber=1),'-') as AvgMyScore,
						ifnull(C.AssmtStudentSelfScore,0) *10 as AssmtStudentSelfScore,
						ifnull((select (AA.AssmtStudentDailyScore1+AA.AssmtStudentDailyScore2+AA.AssmtStudentDailyScore3+AA.AssmtStudentDailyScore4+AA.AssmtStudentDailyScore5)/5*10 from AssmtStudentDailyScores AA where AA.ClassID=A.ClassID order by AA.AssmtStudentDailyScoreID desc limit 0,1 ),'-') as AvgAssmtStudentDailyScore 
					from Classes A 
						inner join Teachers B on A.TeacherID=B.TeacherID 
						inner join ClassOrders D on A.ClassOrderID=D.ClassOrderID 
						left outer join AssmtStudentSelfScores C on A.ClassID=C.ClassID 
					where A.StartYear=".$SearchYear."
						and A.ClassState=2 
						and A.MemberID=".$MemberID." 
						and D.ClassProductID=1 
					order by A.StartDateTime desc 
				";
				?>

				<?

				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$ListCount = 1;

				while($Row = $Stmt->fetch()) {
					$ClassID = $Row["ClassID"];
					
					$StartDateTime = str_replace("-",".", substr($Row["StartDateTime"],0,10));
					$TeacherName = $Row["TeacherName"];
					
					$ClassAttendState = $Row["ClassAttendState"];
					
					$ClassVideoPlayCount = $Row["ClassVideoPlayCount"];
					$AvgMyScore = $Row["AvgMyScore"];
					$AssmtStudentSelfScore = $Row["AssmtStudentSelfScore"];
					$AvgAssmtStudentDailyScore = $Row["AvgAssmtStudentDailyScore"];

					if ($ClassAttendState==-1){
						$StrClassAttendState = "예정";
					}else if ($ClassAttendState==0){
						$StrClassAttendState = "미설정";
					}else if ($ClassAttendState==1){
						$StrClassAttendState = "출석";
					}else if ($ClassAttendState==2){
						$StrClassAttendState = "지각";
					}else if ($ClassAttendState==3){
						$StrClassAttendState = "결석";
					}else if ($ClassAttendState==4){
						$StrClassAttendState = "연기";//학생연기
					}else if ($ClassAttendState==5){
						$StrClassAttendState = "연기";//강사연기
					}else if ($ClassAttendState==6){
						$StrClassAttendState = "취소";//학생취소
					}else if ($ClassAttendState==7){
						$StrClassAttendState = "취소";//강사취소
					}else if ($ClassAttendState==8){
						$StrClassAttendState = "변경";//교사변경수업
					}else{

					}


					if ($AssmtStudentSelfScore==10){
						$StrAssmtStudentSelfScore = "매우 즐거움";
					}else if ($AssmtStudentSelfScore==20){
						$StrAssmtStudentSelfScore = "즐거움";
					}else if ($AssmtStudentSelfScore==30){
						$StrAssmtStudentSelfScore = "보통";
					}else if ($AssmtStudentSelfScore==40){
						$StrAssmtStudentSelfScore = "우울함";
					}else if ($AssmtStudentSelfScore==50){
						$StrAssmtStudentSelfScore = "매우 우울함";
					}
					
				?>

				<tr>
                    <td><?=$StartDateTime?></td>
                    <td><?=$TeacherName?></td>
                    <td><?=$StrClassAttendState?></td>
                    <td><?=$ClassVideoPlayCount?>회</td>
                    <td><?=round($AvgMyScore,0)?>점</td>
                    <td><?=$StrAssmtStudentSelfScore?></td>
                    <td><a href="javascript:OpenAssmtAssmtStudentDailyScoreDetail(<?=$ClassID?>)"><?=round($AvgAssmtStudentDailyScore,0)?>점</td>
                </tr>
				<?
					$ListCount++;
				}
				$Stmt = null;

				if ($ListCount==1){
				?>
				<tr>
                    <td colspan="7"> 학습 기록이 없습니다. </td>
                </tr>
				<?
				}
				?>


            </table>
        </section>       

        

        
        <section class="summary_section" style="display:none;">
            <h3 class="summary_caption_left">강사 피드백 차트</h3> 
            
            <div class="summary_chart"><div id="chartdiv2"></div></div>

			<style>
			#chartdiv2 {
			  width: 100%;
			  height: 300px;
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
			var chart = am4core.create("chartdiv2", am4charts.XYChart);

			// Add percent sign to all numbers
			chart.numberFormatter.numberFormat = "#.#'%'";

			// Add data
			chart.data = [{
				"country": "09.05",
				"year2004": 25,
				"year2005": 75
			}, {
				"country": "09.12",
				"year2004": 45,
				"year2005": 70
			}, {
				"country": "09.19",
				"year2004": 80,
				"year2005": 65
			}, {
				"country": "09.26",
				"year2004": 75,
				"year2005": 73
			}, {
				"country": "10.10",
				"year2004": 90,
				"year2005": 85
			}, {
				"country": "10.17",
				"year2004": 80,
				"year2005": 45
			}];

			// Create axes
			var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
			categoryAxis.dataFields.category = "country";
			categoryAxis.renderer.grid.template.location = 0;
			categoryAxis.renderer.minGridDistance = 30;

			var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
			valueAxis.title.text = "점수";
			valueAxis.title.fontWeight = 800;

			// Create series
			var series = chart.series.push(new am4charts.ColumnSeries());
			series.dataFields.valueY = "year2004";
			series.dataFields.categoryX = "country";
			series.clustered = false;
			series.tooltipText = "점수: [bold]{valueY}[/]";

			var series2 = chart.series.push(new am4charts.ColumnSeries());
			series2.dataFields.valueY = "year2005";
			series2.dataFields.categoryX = "country";
			series2.clustered = false;
			series2.columns.template.width = am4core.percent(50);
			series2.tooltipText = "평균: [bold]{valueY}[/]";

			chart.cursor = new am4charts.XYCursor();
			chart.cursor.lineX.disabled = true;
			chart.cursor.lineY.disabled = true;

			}); // end am4core.ready()
			</script>
        </section>



        <section class="summary_section" style="display:none;">
            <h3 class="summary_caption_left">학생 기분체크 차트</h3> 
            
            <div class="summary_chart"><div id="chartdiv"></div></div>

			<style>
			#chartdiv {
			  width: 100%;
			  height: 300px;
			}
			</style>

			<!-- Resources -->


			<!-- Chart code -->
			<script>
			am4core.ready(function() {

			// Themes begin
			am4core.useTheme(am4themes_animated);
			// Themes end

			am4core.options.commercialLicense = true;

			var chart = am4core.create("chartdiv", am4charts.XYChart);
			chart.paddingRight = 20;

			var data = [];
			var visits = 10;
			var previousValue;

			for (var i = 0; i < 100; i++) {
				visits += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);

				if(i > 0){
					// add color to previous data item depending on whether current value is less or more than previous value
					if(previousValue <= visits){
						data[i - 1].color = chart.colors.getIndex(0);
					}
					else{
						data[i - 1].color = chart.colors.getIndex(5);
					}
				}    
				
				data.push({ date: new Date(2018, 0, i + 1), value: visits });
				previousValue = visits;
			}

			chart.data = data;

			var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
			dateAxis.renderer.grid.template.location = 0;
			dateAxis.renderer.axisFills.template.disabled = true;
			dateAxis.renderer.ticks.template.disabled = true;

			var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
			valueAxis.tooltip.disabled = true;
			valueAxis.renderer.minWidth = 35;
			valueAxis.renderer.axisFills.template.disabled = true;
			valueAxis.renderer.ticks.template.disabled = true;

			var series = chart.series.push(new am4charts.LineSeries());
			series.dataFields.dateX = "date";
			series.dataFields.valueY = "value";
			series.strokeWidth = 2;
			series.tooltipText = "점수: {valueY}, 증감: {valueY.previousChange}";

			// set stroke property field
			series.propertyFields.stroke = "color";

			chart.cursor = new am4charts.XYCursor();

			var scrollbarX = new am4core.Scrollbar();
			chart.scrollbarX = scrollbarX;

			dateAxis.start = 0.7;
			dateAxis.keepSelection = true;


			}); // end am4core.ready()
			</script>

			<!-- HTML -->
			


        </section>
    </div>


<script>
function OpenAssmtAssmtStudentDailyScoreDetail(ClassID){
	openurl = "pop_assmt_student_daily_score_detail.php?ClassID="+ClassID;
	$.colorbox({	
		href:openurl
		,width:"75%" 
		,height:"75%"
		,maxWidth: "500"
		,maxHeight: "600"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	});
}
</script>

<!-- ====   Color Box -->

<script src="./js/jquery-2.2.4.min.js"></script>
<link rel="stylesheet" href="./js/colorbox/example2/colorbox.css" />
<script src="./js/colorbox/jquery.colorbox.js"></script>
<script>
jQuery(document).ready(function(){
$(document).bind('cbox_open', function() {
	$('html').css({ overflow: 'hidden' });
}).bind('cbox_closed', function() {
	$('html').css({ overflow: '' });
});
});
</script>
<!-- ====   Color Box   === -->

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>