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
	<script src="https://www.amcharts.com/lib/4/themes/kelly.js"></script>
	<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
</head>
<?

$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";

if ($SearchYear==""){
	$SearchYear = date("Y");
}


$Sql = "
		select 
				A.*
		from Centers A 
		where A.CenterID=:CenterID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':CenterID', $CenterID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$CenterName = $Row["CenterName"];

// ================================================= 각 월별로 신규가입학생 추출
/*
1 ~ 12월
총원, 추가학생
총원은 마지막 날짜 이전의 모든 학생
추가학생은 각 월별 첫날부터 마지막날까지의 학생
그룹화 ( 루프는 부하 ) 
총원 
추가학생은 년-월 가준의 학생들
*/

// 값 초기화
for($ii=1; $ii<=12; $ii++) {
	$ArrNewStudent[$ii] = 0;
	$ArrAllStudent[$ii] = 0;
	$ArrwithdrawalStudent[$ii] = 0;
}

// 신규 학생
$Sql2 = "select 
			date_format(A.MemberRegDateTime, '%m') AS months, 
			COUNT(*) as NewStudent 

		from Members A  
			inner join Centers B on A.CenterID=B.CenterID 

		where 
			A.CenterID=:CenterID and B.CenterState=1 and A.MemberState=1 AND 
			date_format(A.MemberRegDateTime, '%Y')=".$SearchYear."  

		group by months
		ORDER BY months ASC
		";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':CenterID', $CenterID);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

while($Row2 = $Stmt2->fetch()) {
	$months = $Row2["months"];
	$month = (int)$months;
	$NewStudent = $Row2["NewStudent"];
	$ArrNewStudent[$month] = $NewStudent;
}
$Stmt2 = null;

// 탈퇴 학생
$Sql2 = "select 
			date_format(A.WithdrawalDateTime, '%m') AS months, 
			COUNT(*) as withdrawalStudent 

		from Members A  
			inner join Centers B on A.CenterID=B.CenterID 

		where 
			A.CenterID=:CenterID and B.CenterState=1 and A.MemberState=3 AND A.WithdrawalDateTime is not null 
			and date_format(A.WithdrawalDateTime, '%Y')=".$SearchYear."  

		group by months
		ORDER BY months ASC
		";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':CenterID', $CenterID);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

while($Row2 = $Stmt2->fetch()) {
	$months = $Row2["months"];
	$month = (int)$months;
	$withdrawalStudent = $Row2["withdrawalStudent"];
	$ArrwithdrawalStudent[$month] = $withdrawalStudent;
}
$Stmt2 = null;

//	==================================================

// ================================================= 각 월별로 총학생수
// 총학생 ( 2019 년 이전의 총학생 수 구하기 )
$Sql2 = "select 
				COUNT(*) as AllStudent 
			from Members A  
				inner join Centers B on A.CenterID=B.CenterID 
			where 
				A.CenterID=:CenterID and B.CenterState=1 and A.MemberState=1 
				AND date_format(A.MemberRegDateTime, '%Y') < ".$SearchYear."   
		";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':CenterID', $CenterID);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
$Row2 = $Stmt2->fetch();
$AllStudent = $Row2["AllStudent"];

// 값 초기화
for($ii=1; $ii<=12; $ii++) {
	if($ii==1) {
		$ArrAllStudent[$ii] = $AllStudent + $ArrNewStudent[$ii] - $ArrwithdrawalStudent[$ii];
	} else {
		$ArrAllStudent[$ii] = $ArrAllStudent[$ii-1] + $ArrNewStudent[$ii] - $ArrwithdrawalStudent[$ii];
	}
}

$Stmt2 = null;

//	==================================================
?>
<body>
<?
include_once('./includes/common_body_top.php');
?>
    <div class="summary_wrap">
        <h1 class="summary_title"><b><?=$CenterName?></b> 요약정보</h1>  
        
        <form name="SearchForm" method="get" onchange="SearchFormSubmit()">
		<input type="hidden" name="CenterID" id="CenterID" value="<?=$CenterID?>">
		<select class="summary_select" name="SearchYear" id="SearchYear">
            <?for ($ii=$SearchYear-1;$ii<=$SearchYear+1;$ii++){?>
			<option value="<?=$ii?>" <?if ($ii==$SearchYear){?>selected<?}?>><?=$ii?></option>
			<?}?>
        </select>
		</form>
 
		<script>
		function SearchFormSubmit(){
			document.SearchForm.action = "summary_center.php";
			document.SearchForm.submit();
		}
		</script>


		<?
		$Sql = "
				select 
						count(*) as StudentCount
				from Members A 

				where A.CenterID=$CenterID 
					and A.MemberLevelID=19
					and A.MemberState=1 
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$StudentCount = $Row["StudentCount"];


		$Sql = "
				select 
						count(*) as StudyStudentCount
				from Members A 

				where 
					A.MemberID in (select MemberID from ClassOrders where ClassProgress=11 and (ClassOrderState=1 or ClassOrderState=2 or ClassOrderState=4)) 
					and A.CenterID=$CenterID 
					and A.MemberLevelID=19
					and A.MemberState=1 
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$StudyStudentCount = $Row["StudyStudentCount"];



		$Sql = "
				select 
					count(*) as AttendCount
				from Classes A 
				where 
					A.MemberID in (select MemberID from Members where CenterID=$CenterID) 
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
					A.MemberID in (select MemberID from Members where CenterID=$CenterID) 
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
            <h3 class="summary_caption_left">학원정보</h3> 
            <table class="summary_table">
                <colgroup span="3" width="33.3%"></colgroup>
                <tr>
                    <th>학생수</th>
                    <th>수강생</th>
                    <th>출석율</th>
                </tr>
                <tr>
                    <td><?=number_format($StudentCount,0)?></td>
                    <td><?=number_format($StudyStudentCount,0)?></td>
                    <td><?=$AttendRatio?>%</td>
                </tr>
            </table>
        </section>
        



		<?
		$ArrClassOrderWeekCountIDCount[1] = 0;
		$ArrClassOrderWeekCountIDCount[2] = 0;
		$ArrClassOrderWeekCountIDCount[3] = 0;
		$ArrClassOrderWeekCountIDCount[4] = 0;
		$ArrClassOrderWeekCountIDCount[5] = 0;

		$Sql = "select 
					A.ClassOrderWeekCountID, count(ClassOrderWeekCountID) as ClassOrderWeekCountIDCount 
				from ClassOrders A 
					inner join Members B on A.MemberID=B.MemberID 
				where 
					A.ClassProgress=11 
					and (A.ClassOrderState=1 or A.ClassOrderState=2 or A.ClassOrderState=4) 
					and B.CenterID=$CenterID 
				group by ClassOrderWeekCountID 

				order by ClassOrderWeekCountID
			";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);					
		while($Row = $Stmt->fetch()) {		
			$ArrClassOrderWeekCountIDCount[$Row["ClassOrderWeekCountID"]] = $Row["ClassOrderWeekCountIDCount"];
		}
		$Stmt = null;

		?>
        <section class="summary_section">
            <h3 class="summary_caption_left">수강현황</h3> 
            <table class="summary_table">
                <colgroup span="5" width="20%"></colgroup>
                <tr>
                    <th>주1회</th>
                    <th>주2회</th>
                    <th>주3회</th>
                    <th>주4회</th>
                    <th>주5회</th>
                </tr>
                <tr>
                    <?for ($ii=1;$ii<=5;$ii++){?>
					<td><?=$ArrClassOrderWeekCountIDCount[$ii]?></td>
					<?}?>
                </tr>
            </table>

			<div style="margin-top:10px;">※ 한학생이 여러 강좌를 수강할 경우 수강생수 합과 일치하지 않을 수 있습니다.</div>
        </section>
 
		
		<?
		$PP_SearchYear = $SearchYear-2;
		$P_SearchYear = $SearchYear-1;
		
		$Sql = "
				select 
						count(*) as StudentCount
				from Members A 

				where A.CenterID=$CenterID 
					and A.MemberLevelID=19
					and A.MemberState=1 
					and date_format(A.MemberRegDateTime, '%Y')=".$PP_SearchYear."
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$PP_StudentCount = $Row["StudentCount"];

		$Sql = "
				select 
						count(*) as StudentCount
				from Members A 

				where A.CenterID=$CenterID 
					and A.MemberLevelID=19
					and A.MemberState=1 
					and date_format(A.MemberRegDateTime, '%Y')=".$P_SearchYear."
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$P_StudentCount = $Row["StudentCount"];

		$Sql = "
				select 
						count(*) as StudentCount
				from Members A 

				where A.CenterID=$CenterID 
					and A.MemberLevelID=19
					and A.MemberState=1 
					and date_format(A.MemberRegDateTime, '%Y')=".$SearchYear."
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$Now_StudentCount = $Row["StudentCount"];


		?>
        <section class="summary_section">
            <h3 class="summary_caption_left">최근 3년 가입현황</h3> 
            <table class="summary_table">
                <colgroup span="3" width="33.3%"></colgroup>
                <tr>
                    <th><?=$PP_SearchYear?></th>
                    <th><?=$P_SearchYear?></th>
                    <th><?=$SearchYear?></th>
                </tr>
                <tr>
                    <td><?=$PP_StudentCount?>명</td>
                    <td><?=$P_StudentCount?>명</td>
                    <td><?=$Now_StudentCount?>명</td>
                </tr>
            </table>
        </section>

		<?

		?>
		<!-- ====================================================================== -->
        <section class="summary_section">
            <h3 class="summary_caption_left">월별 학생 현황(<?=$SearchYear?>)</h3> 
     
		<div class="report_chart_2">
			<div id="chartdiv_2"></div>
			<ul class="report_chart_mark">
				<li><span style="color:#69B7DA;">■</span> 총학생</li>
				<li><span style="color:#6894D9;">■</span> 신규학생</li>
			</ul>
		</div>    

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
			"country": "1월",
			"총학생": <?=$ArrNewStudent[1]?>,
			"신규학생": <?=$ArrAllStudent[1]?>
		  }, {
			"country": "2월",
			"총학생": <?=$ArrNewStudent[2]?>,
			"신규학생": <?=$ArrAllStudent[2]?>
		  }, {
			"country": "3월",
			"총학생": <?=$ArrNewStudent[3]?>,
			"신규학생": <?=$ArrAllStudent[3]?>
		  }, {
			"country": "4월",
			"총학생": <?=$ArrNewStudent[4]?>,
			"신규학생": <?=$ArrAllStudent[4]?>
		  }, {
			"country": "5월",
			"총학생": <?=$ArrNewStudent[5]?>,
			"신규학생": <?=$ArrAllStudent[5]?>
		  }, {
			"country": "6월",
			"총학생": <?=$ArrNewStudent[6]?>,
			"신규학생": <?=$ArrAllStudent[6]?>
		  }, {
			"country": "7월",
			"총학생": <?=$ArrNewStudent[7]?>,
			"신규학생": <?=$ArrAllStudent[7]?>
		  }, {
			"country": "8월",
			"총학생": <?=$ArrNewStudent[8]?>,
			"신규학생": <?=$ArrAllStudent[8]?>
		  }, {
			"country": "9월",
			"총학생": <?=$ArrNewStudent[9]?>,
			"신규학생": <?=$ArrAllStudent[9]?>
		  }, {
			"country": "10월",
			"총학생": <?=$ArrNewStudent[10]?>,
			"신규학생": <?=$ArrAllStudent[10]?>
		  }, {
			"country": "11월",
			"총학생": <?=$ArrNewStudent[11]?>,
			"신규학생": <?=$ArrAllStudent[11]?>
		  }, {
			"country": "12월",
			"총학생": <?=$ArrNewStudent[12]?>,
			"신규학생": <?=$ArrAllStudent[12]?>
		  }];

		  // Create axes
		  var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
		  categoryAxis.dataFields.category = "country";
		  categoryAxis.renderer.grid.template.location = 0;
		  categoryAxis.renderer.minGridDistance = 30;

		  var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
		  valueAxis.min = 0;
		  valueAxis.title.text = "인원수";
		  valueAxis.title.fontWeight = 800;

		  // Create series
		  var series = chart.series.push(new am4charts.ColumnSeries());
		  series.dataFields.valueY = "신규학생";
		  series.dataFields.categoryX = "country";
		  series.clustered = false;
		  series.tooltipText = "총학생: [bold]{valueY}[/]";

		  var series2 = chart.series.push(new am4charts.ColumnSeries());
		  series2.dataFields.valueY = "총학생";
		  series2.dataFields.categoryX = "country";
		  series2.clustered = false;
		  series2.columns.template.width = am4core.percent(50);
		  series2.tooltipText = "신규학생: [bold]{valueY}[/]";

		  chart.cursor = new am4charts.XYCursor();
		  chart.cursor.lineX.disabled = true;
		  chart.cursor.lineY.disabled = true;

		  }); // end am4core.ready()
		</script>
		
        <section class="summary_section" style="margin-top:10px;">
            <h3 class="summary_caption_left">월별 학생 현황표(<?=$SearchYear?>)</h3> 
            <table class="summary_table">

                <tr>
					<th></th>
                    <th>1월</th>
                    <th>2월</th>
                    <th>3월</th>
					<th>4월</th>
					<th>5월</th>
					<th>6월</th>
					<th>7월</th>
					<th>8월</th>
					<th>9월</th>
					<th>10월</th>
					<th>11월</th>
					<th>12월</th>
                </tr>
                <tr>
					<td>총원</td>
                    <td><?=$ArrAllStudent[1]?>명</td>
                    <td><?=$ArrAllStudent[2]?>명</td>
                    <td><?=$ArrAllStudent[3]?>명</td>
					<td><?=$ArrAllStudent[4]?>명</td>
					<td><?=$ArrAllStudent[5]?>명</td>
					<td><?=$ArrAllStudent[6]?>명</td>
					<td><?=$ArrAllStudent[7]?>명</td>
					<td><?=$ArrAllStudent[8]?>명</td>
					<td><?=$ArrAllStudent[9]?>명</td>
					<td><?=$ArrAllStudent[10]?>명</td>
					<td><?=$ArrAllStudent[11]?>명</td>
					<td><?=$ArrAllStudent[12]?>명</td>
                </tr>
                <tr>
					<td>신규</td>
                    <td><?=$ArrNewStudent[1]?>명</td>
                    <td><?=$ArrNewStudent[2]?>명</td>
                    <td><?=$ArrNewStudent[3]?>명</td>
					<td><?=$ArrNewStudent[4]?>명</td>
					<td><?=$ArrNewStudent[5]?>명</td>
					<td><?=$ArrNewStudent[6]?>명</td>
					<td><?=$ArrNewStudent[7]?>명</td>
					<td><?=$ArrNewStudent[8]?>명</td>
					<td><?=$ArrNewStudent[9]?>명</td>
					<td><?=$ArrNewStudent[10]?>명</td>
					<td><?=$ArrNewStudent[11]?>명</td>
					<td><?=$ArrNewStudent[12]?>명</td>
                </tr>
            </table>
        </section>
		</div>
        </section>
		<!-- ======================================================= -->
		<br><br><br><br>
        
		<div style="display:none;">
				<section class="summary_section">
					<h3 class="summary_caption_left">최근 3년 매출현황</h3> 
					<table class="summary_table">
						<colgroup span="3" width="33.3%"></colgroup>
						<tr>
							<th>2017</th>
							<th>2018</th>
							<th>2019</th>
						</tr>
						<tr>
							<td>23,054,000</td>
							<td>42,350,000</td>
							<td>20,541,000</td>
						</tr>
					</table>
				</section>
				
				<section class="summary_section">
					<h3 class="summary_caption_left">월별 매출현황 (2019)</h3> 
					
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
					
					<div class="summary_table_overflow">
						<table class="summary_table one">
							<colgroup span="3" width="25%"></colgroup>
							<tr>
								<th>1월</th>
								<th>2월</th>
								<th>3월</th>
								<th>4월</th>
							</tr>
							<tr>
								<td>2,850,400</td>
								<td>1,850,100</td>
								<td>3,000,000</td>
								<td>1,750,800</td>
							</tr>
						</table>
						<table class="summary_table two">
							<colgroup span="3" width="25%"></colgroup>
							<tr>
								<th>5월</th>
								<th>6월</th>
								<th>7월</th>
								<th>8월</th>
							</tr>
							<tr>
								<td>4,140,400</td>
								<td>3,236,400</td>
								<td>1,690,400</td>
								<td>2,450,400</td>
							</tr>
						</table> 
						<table class="summary_table three">
							<colgroup span="3" width="25%"></colgroup>
							<tr>
								<th>9월</th>
								<th>10월</th>
								<th>11월</th>
								<th>12월</th>
							</tr>
							<tr>
								<td>1,136,000</td>
								<td>853,000</td>
								<td>-</td>
								<td>-</td>
							</tr>
						</table>
					</div>
				</section>
		</div>
    </div>
</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>