<?php
error_reporting( E_ALL );
ini_set( "display_errors", 1 );
?>
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
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->

<script src="https://bossanova.uk/jspreadsheet/v4/jexcel.js"></script>
<script src="https://jsuites.net/v4/jsuites.js"></script>
<link rel="stylesheet" href="https://bossanova.uk/jspreadsheet/v4/jexcel.css" type="text/css" />
<link rel="stylesheet" href="https://jsuites.net/v4/jsuites.css" type="text/css" />

<style>
:root {
    --jexcel_header_color: #000;
    --jexcel_header_color_highlighted: #000;
    --jexcel_header_background: #e3f2fd;
    --jexcel_header_background_highlighted: #dcdcdc;
    --jexcel_content_color: #000;
    --jexcel_content_color_highlighted: #000;
    --jexcel_content_background: #fff;
    --jexcel_content_background_highlighted: rgba(0,0,0,0.05);
    --jexcel_menu_background: #fff;
    --jexcel_menu_background_highlighted: #ebebeb;
    --jexcel_menu_color: #555;
    --jexcel_menu_color_highlighted: #555;
    --jexcel_menu_box_shadow: 2px 2px 2px 0px rgba(143, 144, 145, 1);
    --jexcel_border_color: #ccc;
    --jexcel_border_color_highlighted: #000;
    --active_color: #007aff;
}


	#chartdiv {
	  width: 100%;
	  height: 500px;
	}
</style>


</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
#--------------------------------------------------------------------------------------------------------------------#
#--------------------------------------------------------- 학생수  --------------------------------------------------#
#--------------------------------------------------------------------------------------------------------------------#
$MainMenuID = 14;
$SubMenuID  = 1410;
include_once('./inc_top.php'); 
include_once('./inc_menu_left.php');
#--------------------------------------------------------------------------------------------------------------------#

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$EduCenterID = 1;

$SearchStartYear = isset($_REQUEST["SearchStartYear"]) ? $_REQUEST["SearchStartYear"] : "";
$SearchStartMonth = isset($_REQUEST["SearchStartMonth"]) ? $_REQUEST["SearchStartMonth"] : "";


$SearchEndYear = isset($_REQUEST["SearchEndYear"]) ? $_REQUEST["SearchEndYear"] : "";
$SearchEndMonth = isset($_REQUEST["SearchEndMonth"]) ? $_REQUEST["SearchEndMonth"] : "";

$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchTeacherID = isset($_REQUEST["SearchTeacherID"]) ? $_REQUEST["SearchTeacherID"] : "";

$OrderField = isset($_REQUEST["OrderField"]) ? $_REQUEST["OrderField"] : "TeacherName";

$OrderDirect = isset($_REQUEST["OrderDirect"]) ? $_REQUEST["OrderDirect"] : "asc";

if($SearchTeacherID=="") {
	$SearchTeacherID = 100;
}

if ($SearchStartYear==""){
	$SearchStartYear = date("Y");
}
if ($SearchStartMonth==""){
	$SearchStartMonth = date("m");
}

if ($SearchEndYear==""){
	$SearchEndYear = date("Y");
}
if ($SearchEndMonth==""){
	$SearchEndMonth = date("m");
}



$SearchStartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth,-2) ;

$transDate = strtotime($SearchStartDate."-01 -1 month");

$SearchBaseDate = date("Y-m",$transDate) ;
$SearchEndDate = $SearchEndYear . "-" . substr("0".$SearchEndMonth,-2);

if($SearchTeacherID!=100 ) {
	$AddSqlWhere = $AddSqlWhere . " and C.TeacherID=$SearchTeacherID ";
}

$CurrYear = date("Y");



    // 탈락률  sum( count(distinct c.MemberID)) over (order by DATE_FORMAT(c.EndDateTime,'%Y-%m')) as tot
    $Sql = "SELECT DISTINCT t.TeacherName, @teacher := d.TeacherID as TeacherID,
                     @memsum := 0, 
                     GROUP_CONCAT(distinct m.MemberLoginID separator ', ') as LeaveID, count(distinct d.MemberID) as LeaveCount 
                    FROM Teachers t 
                    inner join Classes d on d.TeacherID = t.TeacherID
                    inner join ClassOrders o on d.ClassOrderID = o.ClassOrderID 
                    inner join Members m on d.MemberID = m.MemberID
                                            WHERE d.MemberID IN 
                                            (
                                            SELECT  DISTINCT a.MemberID
                                                FROM Classes a 
                                                WHERE  DATE_FORMAT(a.EndDateTime,'%Y-%m') = '".$SearchBaseDate."'  and TeacherID = t.TeacherID 
                                                AND a.ClassState = 2 
                                                GROUP BY a.MemberID	  
                                            )  
                                                AND d.MemberID NOT IN 
                                            (
                                            SELECT  DISTINCT a.MemberID
                                                FROM Classes a 
                                                WHERE  DATE_FORMAT(a.EndDateTime,'%Y-%m') = '".$SearchEndDate."'  and TeacherID = t.TeacherID 
                                                GROUP BY a.MemberID	  
                                            )  
                                            AND DATE_FORMAT(d.EndDateTime,'%Y-%m') = '".$SearchBaseDate."'
                                            AND t.TeacherState = 1
                                            AND o.ClassProductID <> 3
                    GROUP by d.TeacherID
                    order by ".$OrderField."  ".$OrderDirect."
                                ";
    

    $Stmt = $DbConn->prepare($Sql);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $i=0;

	$ArrTeacherName = [];
	$ArrTeacherID = [];
	$ArrLeaveCount = [];
    $ArrLeaveID = [];
    $ArrPreStudents = [];
    $ArrLeavePercent = [];

	$TeacherNum = 0;

    
	while($Row = $Stmt->fetch() ) {
		$TeacherID = $Row["TeacherID"];
		$TeacherName = $Row["TeacherName"];
		$LeaveCount = $Row["LeaveCount"];
        $LeaveID = $Row["LeaveID"];

        $Sql2 = "SELECT max(tot) as prestudents, @memsum := 0  from (                        
            SELECT count(distinct c.MemberID), DATE_FORMAT(c.EndDateTime,'%Y-%m'), 
            @memsum := @memsum + count(distinct c.MemberID) as tot  
            from Classes c 
            inner join ClassOrders co on c.ClassOrderID = co.ClassOrderID AND co.ClassProductID <> 3
            where c.TeacherID = ".$TeacherID."
            AND DATE_FORMAT(c.EndDateTime,'%Y-%m') >= '".$SearchBaseDate."' AND DATE_FORMAT(c.EndDateTime,'%Y-%m') < '".$SearchEndDate."' 
            group by DATE_FORMAT(c.EndDateTime,'%Y-%m')) z";
    
        $Stmt2 = $DbConn->prepare($Sql2);
        $Stmt2->execute();
        $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
        while ($Row2 = $Stmt2->fetch()){
            $PreStudents = $Row2["prestudents"];
        }

        //$PreStudents = $Row["pres"];

		$ArrTeacherID[$TeacherNum] = $TeacherID;
		$ArrTeacherName[$TeacherNum] = $TeacherName;
		$ArrLeaveCount[$ArrTeacherID[$TeacherNum]] = $LeaveCount;
        $ArrLeaveID[$TeacherNum] = $LeaveID;
        $ArrPreStudents[$TeacherNum] = $PreStudents;
        $ArrLeavePercent[$TeacherNum] = round($LeaveCount/$PreStudents*100,2);
		$TeacherNum++;
	}



//현재 날짜 세팅
$nowDate = date("Y-m"."-01");

?>
<div id="page_content">
    <div id="page_content_inner">
        <h3 class="heading_b uk-margin-bottom">탈락률
        </h3>

        <form name="SearchForm" method="get">
        <div class="md-card" style="margin-bottom:10px;">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin="">

                    <div class="uk-width-medium-1-10" style="padding-top:7px;">
                        <select id="SearchStartYear" name="SearchStartYear" class="uk-width-1-1" data-placeholder="년도선택" style="width:100%;height:40px;"/>
                            <option value="">년도선택</option>
                            <?
                            for ($iiii=2023;$iiii<=$SearchStartYear+1;$iiii++) {
                            ?>
                            <option value="<?=$iiii?>" <?if ($SearchStartYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
                            <?
                            }
                            ?>
                        </select>
                    </div>
                    <div class="uk-width-medium-1-10" style="padding-top:7px;">
                        <select id="SearchStartMonth" name="SearchStartMonth" class="uk-width-1-1"  data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;height:40px;"/>
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

                    <!-- <div class="uk-width-medium-5-10"></div> --> <span style="padding-top: 15px; ">~</span>
                    <div class="uk-width-medium-1-10" style="padding-top:7px; ">
                        <select id="SearchEndYear" name="SearchEndYear" class="uk-width-1-1" data-placeholder="년도선택" style="width:100%;height:40px;"/>
                            <option value="">년도선택</option>
                            <?
                            for ($iiii=2023;$iiii<=$SearchEndYear+1;$iiii++) {
                            ?>
                            <option value="<?=$iiii?>" <?if ($SearchEndYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
                            <?
                            }
                            ?>
                        </select>
                    </div>
                    <div class="uk-width-medium-1-10" style="padding-top:7px;">
                        <select id="SearchEndMonth" name="SearchEndMonth" class="uk-width-1-1"  data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;height:40px;"/>
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
                        <div class="uk-overflow-container" id="parentContainer">

                            <h3>탈락률(지난달까지 수업이 있었으나 이번달에 수업 없는 학생비율)</h3>
                            <div id="spreadsheet" ></div>  
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">

							
							

							<!-- HTML -->
							<div id="chartdiv"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

    </div>
</div>


<?php // echo $Sql2;?>
		


<?
include_once('./inc_common_list_js.php');
?>



<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->



<!-- Resources -->
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
<!-- Chart code -->

<script>
//am4core.ready(function() {
    // Themes begin
    am4core.useTheme(am4themes_animated);
    // Themes end
    var chart = am4core.create("chartdiv", am4charts.XYChart);
    var data = [];
    var open = 100;
    var close = 120;
    var names = <?=json_encode($ArrTeacherName)?>;
    var lastCount = <?=json_encode($ArrLeavePercent);?>;
    //console.log(names);
    //console.log(lastCount);

    for (var i = 0; i < names.length; i++) {
        open += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 5);
        close = open + Math.round(Math.random() * 10) + 3;
        //data.push({ category: names[i], open: open, close: close });
        data.push({ category: names[i], close: lastCount[i] });
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
        return -target.maxRight / 1.2;
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
    series.tooltipText = "탈락률: {valueY.value}";
    series.sequencedInterpolation = true;
    series.fillOpacity = 1;
    series.strokeOpacity = 0;
    series.columns.template.width = 6;
    series.tooltip.pointerOrientation = "horizontal";
    
    // 탈락률 값에 따라서 막대그래프의 색상을 변경
    series.columns.template.adapter.add("fill", function(fill, target) {
        if (target.dataItem && (target.dataItem.valueY <= 3.99)) {
            return am4core.color("#337ef5");
        } else if (target.dataItem && (target.dataItem.valueY <= 4.99)) {
            return am4core.color("#fcf758");
        } else if (target.dataItem && (target.dataItem.valueY > 5)) {    
            return am4core.color("#fa4a39");
        } else {
            return fill;
        }
    });

    var openBullet = series.bullets.create(am4charts.CircleBullet);
    openBullet.locationY = 1;
    var closeBullet = series.bullets.create(am4charts.CircleBullet);
    closeBullet.fill = chart.colors.getIndex(4);
    closeBullet.stroke = closeBullet.fill;
    chart.cursor = new am4charts.XYCursor();
    chart.scrollbarX = new am4core.Scrollbar();
    chart.scrollbarY = new am4core.Scrollbar();
//}); // end am4core.ready()
</script>

<script>
function SearchSubmit(){
	document.SearchForm.action = "leaving_out.php";
	document.SearchForm.submit();
}


    var studentData = [
<?php 
    for ($i=0;$i<count($ArrTeacherName);$i++){
        echo "['".$ArrTeacherName[$i]."',".$ArrPreStudents[$i].",".$ArrLeaveCount[$ArrTeacherID[$i]].",".$ArrLeavePercent[$i].",'".$ArrLeaveID[$i]."'],";
    } 
?>
    ];

    var pc = document.getElementById('parentContainer');
    var parentWidth = pc.clientWidth;
    var widthBasic = parentWidth / 10;
    

    // 표의 제목을 눌러서 정렬할 때, 하단의 그래프 데이타도 순서를 변경해서 다시 그린다.
    var sort = function(instance, cellNum, order) {
        
        names = Object.values(table1.getColumnData(0));

        lastCount = Object.values(table1.getColumnData(3));

        var data = [];

        for (var i = 0; i < names.length; i++) {
            open += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 5);
            close = open + Math.round(Math.random() * 10) + 3;
            //data.push({ category: names[i], open: open, close: close });
            data.push({ category: names[i], close: lastCount[i] });
        }
        chart.data = data;

    }

    var table1 = jspreadsheet(document.getElementById('spreadsheet'), {
        data:studentData,
        columns:[
            { title:'강사명',  type: 'text', width:widthBasic*1.4 },
            { title:'전달 수업한 학생수',  type: 'number', width:widthBasic },
            { title:'전달 수업했지만 \n 이번달 수업없는 학생수',  type: 'number', width:widthBasic },
            { title:'탈락률(%)', type: 'number', width:widthBasic },
            { title:'탈락한 학생 아이디', width:widthBasic*4.9 }
        ],
        onsort: sort,
    });

    

</script>


<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>