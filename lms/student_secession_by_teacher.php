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
$MainMenuID = 11;
$SubMenuID = 1109;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$EduCenterID = 1;

$SearchStartYear = isset($_REQUEST["SearchStartYear"]) ? $_REQUEST["SearchStartYear"] : "";
$SearchStartMonth = isset($_REQUEST["SearchStartMonth"]) ? $_REQUEST["SearchStartMonth"] : "";
$SearchStartDay = isset($_REQUEST["SearchStartDay"]) ? $_REQUEST["SearchStartDay"] : "";

$SearchEndYear = isset($_REQUEST["SearchEndYear"]) ? $_REQUEST["SearchEndYear"] : "";
$SearchEndMonth = isset($_REQUEST["SearchEndMonth"]) ? $_REQUEST["SearchEndMonth"] : "";
$SearchEndDay = isset($_REQUEST["SearchEndDay"]) ? $_REQUEST["SearchEndDay"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchTeacherID = isset($_REQUEST["SearchTeacherID"]) ? $_REQUEST["SearchTeacherID"] : "";

if($SearchTeacherID=="") {
	$SearchTeacherID = 100;
}

if ($SearchStartYear==""){
	$SearchStartYear = date("Y");
}
if ($SearchStartMonth==""){
	$SearchStartMonth = date("m");
}
if ($SearchStartDay==""){
	$SearchStartDay = date("d");
}

if ($SearchEndYear==""){
	$SearchEndYear = date("Y");
}
if ($SearchEndMonth==""){
	$SearchEndMonth = date("m");
}
if ($SearchEndDay==""){
	$SearchEndDay = date("d");
}

//$SearchStartDate = $SearchStartYear."-".$SearchStartMonth."-".$SearchStartDay;
//$SearchEndDate = $SearchEndYear."-".$SearchEndMonth."-".$SearchEndDay;
$SearchStartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth,-2) . "-" . substr("0".$SearchStartDay,-2);
$SearchEndDate = $SearchEndYear . "-" . substr("0".$SearchEndMonth,-2) . "-" . substr("0".$SearchEndDay,-2);

if($SearchTeacherID!=100 ) {
	$AddSqlWhere = $AddSqlWhere . " and C.TeacherID=$SearchTeacherID ";
}
//$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "
	select 
		C.TeacherID,
		C.TeacherName,
		count(C.TeacherID) as LeaveCount
	from ClassOrders A 
		inner join ClassOrderSlots B on A.ClassOrderID=B.ClassOrderID 
		inner join Teachers C on B.TeacherID=C.TeacherID 
	where
		$AddSqlWhere
		and
		datediff(A.ClassOrderEndDate, :SearchStartDate) >=0
		and
		datediff(A.ClassOrderEndDate, :SearchEndDate) <=0
		and
		A.ClassOrderState=3
		and
		B.ClassOrderSlotType=1
		and
		B.ClassOrderSlotMaster=1
		and
		B.ClassOrderSlotEndDate is null
	group by
		C.TeacherID 
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SearchStartDate', $SearchStartDate);
$Stmt->bindParam(':SearchEndDate', $SearchEndDate);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

//echo $Sql;

?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom">강사별 수업종료 현황</h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">

					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchStartYear" name="SearchStartYear" class="uk-width-1-1" data-placeholder="년도선택" style="width:100%;height:40px;"/>
							<option value="">년도선택</option>
							<?
							for ($iiii=2019;$iiii<=$SearchStartYear+1;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchStartYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchStartMonth" name="SearchStartMonth" class="uk-width-1-1" onchange="ChSearchStartMonth(1, this.value);" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;height:40px;"/>
							<option value="">월선택</option>
							<?
							for ($iiii=1;$iiii<=12;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchStartMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchStartDay" name="SearchStartDay" class="uk-width-1-1" data-placeholder="일선택" style="width:100%;height:40px;"/>
							<option value="">일선택</option>
						</select>
					</div>

					<!-- <div class="uk-width-medium-5-10"></div> --> <span style="padding-top: 15px; ">~</span>
					<div class="uk-width-medium-1-10" style="padding-top:7px; ">
						<select id="SearchEndYear" name="SearchEndYear" class="uk-width-1-1" data-placeholder="년도선택" style="width:100%;height:40px;"/>
							<option value="">년도선택</option>
							<?
							for ($iiii=2019;$iiii<=$SearchEndYear+1;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchEndYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchEndMonth" name="SearchEndMonth" class="uk-width-1-1" onchange="ChSearchStartMonth(2, this.value);" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;height:40px;"/>
							<option value="">월선택</option>
							<?
							for ($iiii=1;$iiii<=12;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchEndMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchEndDay" name="SearchEndDay" class="uk-width-1-1" data-placeholder="일선택" style="width:100%;height:40px;"/>
							<option value="">일선택</option>
						</select>
					</div>
				</div>

				<div class="uk-grid" data-uk-grid-margin="">
					<div class="uk-width-medium-1-10" style="padding-top:7px;display:<?if ($_LINK_ADMIN_LEVEL_ID_==15){?>none<?}?>;">
						<select id="SearchTeacherID" name="SearchTeacherID" class="uk-width-1-1" style="width:100%;height:40px;"/>
							<option value="">전체선택</option>
							<?
							$Sql2 = "select 
											A.*
									from Teachers A 
										inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
									where 
										A.TeacherState=1
										and A.TeacherView=1
										and B.EduCenterID=$EduCenterID 
										and B.TeacherGroupState<>0 
										and A.TeacherState<>0 
									order by A.TeacherName asc";

						
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

							while($Row2 = $Stmt2->fetch()) {
							?>
							<option value="<?=$Row2["TeacherID"]?>" <?if ($SearchTeacherID==$Row2["TeacherID"]){?>selected<?}?>><?=$Row2["TeacherName"]?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>

					<!--
					<div class="uk-width-medium-2-10">
						<label for="SearchText">교사명</label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>
					-->
					<div class="uk-width-medium-1-10 uk-text-center">
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

							<?
								$ArrTeacherName = [];
								$ArrTeacherID = [];
								$ArrLeaveCount = [];

								$TeacherNum = 0;
								while($Row = $Stmt->fetch() ) {
									$TeacherID = $Row["TeacherID"];
									$TeacherName = $Row["TeacherName"];
									$LeaveCount = $Row["LeaveCount"];

									$ArrTeacherID[$TeacherNum] = $TeacherID;
									$ArrTeacherName[$TeacherNum] = $TeacherName;
									$ArrLeaveCount[$ArrTeacherID[$TeacherNum]] = $LeaveCount;

									$TeacherNum++;
								}

							?>
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
							<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

							<!-- Chart code -->
							<script>
							am4core.ready(function() {

							// Themes begin
							am4core.useTheme(am4themes_animated);
							// Themes end

							var chart = am4core.create("chartdiv", am4charts.XYChart);

							var data = [];
							var open = 100;
							var close = 120;

							var names = <?=json_encode($ArrTeacherName)?>;
							var ids = <?=json_encode($ArrTeacherID);?>;
							var lastCount = <?=json_encode($ArrLeaveCount);?>;

							//console.log(names);
							//console.log(ids);
							//console.log(lastCount);

							for (var i = 0; i < names.length; i++) {

							  open += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 5);
							  close = open + Math.round(Math.random() * 10) + 3;
							  //data.push({ category: names[i], open: open, close: close });
							  data.push({ category: names[i], close: lastCount[ids[i]] });
							}

							chart.data = data;
							var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
							categoryAxis.renderer.grid.template.location = 0;
							categoryAxis.dataFields.category = "category";
							categoryAxis.renderer.minGridDistance = 15;
							categoryAxis.renderer.grid.template.location = 0.5;
							categoryAxis.renderer.grid.template.strokeDasharray = "1,3";
							categoryAxis.renderer.labels.template.rotation = -90;
							categoryAxis.renderer.labels.template.horizontalCenter = "left";
							categoryAxis.renderer.labels.template.location = 0.5;
							categoryAxis.renderer.inside = true;

							categoryAxis.renderer.labels.template.adapter.add("dx", function(dx, target) {
								return -target.maxRight / 2;
							})

							var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
							valueAxis.tooltip.disabled = true;
							valueAxis.renderer.ticks.template.disabled = true;
							valueAxis.renderer.axisFills.template.disabled = true;

							var series = chart.series.push(new am4charts.ColumnSeries());
							series.dataFields.categoryX = "category";
							//series.dataFields.openValueY = "open";
							series.dataFields.valueY = "close";
							//series.tooltipText = "수업수: {openValueY.value} 지각수: {valueY.value}";
							series.tooltipText = "수업종료된회원수: {valueY.value}";
							series.sequencedInterpolation = true;
							series.fillOpacity = 0;
							series.strokeOpacity = 1;
							series.columns.template.width = 0.01;
							series.tooltip.pointerOrientation = "horizontal";

							var openBullet = series.bullets.create(am4charts.CircleBullet);
							openBullet.locationY = 1;

							var closeBullet = series.bullets.create(am4charts.CircleBullet);

							closeBullet.fill = chart.colors.getIndex(4);
							closeBullet.stroke = closeBullet.fill;

							chart.cursor = new am4charts.XYCursor();

							chart.scrollbarX = new am4core.Scrollbar();
							chart.scrollbarY = new am4core.Scrollbar();


							}); // end am4core.ready()
							</script>

							<!-- HTML -->
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
<script>



</script>



<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "student_secession_by_teacher.php";
	document.SearchForm.submit();
}

function ChangeState(ProductOrderID, ProductOrderState) {

	if (confirm("상태를 변경하겠습니까?")){
	
			url = "ajax_change_product_order_state.php";
			//location.href = url + "?ProductOrderID="+ProductOrderID+"&ProductOrderState="+ProductOrderState;
			$.ajax(url, {
				data: {
					ProductOrderID: ProductOrderID,
					ProductOrderState: ProductOrderState 
				},
				success: function (data) {

					location.reload();
				},
				error: function () {

				}
			});

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

				SelBoxAddOption( 'SearchStartDay', '일선택', "", "");
				for (ii=1 ; ii<=data.LastDay ; ii++ ){
					ArrOptionText     = ii + "일";
					ArrOptionValue    = ii;

					ArrOptionSelected = "";
					if (ii==<?=(int)$SearchStartDay?>){
						ArrOptionSelected = "selected";
					}

					SelBoxAddOption( 'SearchStartDay', ArrOptionText, ArrOptionValue, ArrOptionSelected );
				}

			}else{

				SelBoxInitOption('SearchEndDay');

				SelBoxAddOption( 'SearchEndDay', '일선택', "", "");
				for (ii=1 ; ii<=data.LastDay ; ii++ ){
					ArrOptionText     = ii + "일";
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