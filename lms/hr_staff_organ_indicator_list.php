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
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 회원 고유아이디(번호, 조직아이디) 찾기
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "select T.*,M.* from Members as M 
			  left join Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
			      where M.MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
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
				<a type="button" href="hr_staff_indicator_list.php" class="md-btn md-btn-primary" style="background:#6FB9F7"><?=$개인[$LangID]?></a>
				<a type="button" href="hr_staff_organ_indicator_list.php" class="md-btn md-btn-primary" style="background:#2196F3"><?=$부문[$LangID]?></a>
		<? 
                echo $평가결과[$LangID];
		// } else if ($My_OrganLevel==1) {   // 최종상사인 경우
        //         echo $부문[$LangID]." ".$평가결과[$LangID];
		} else {
                echo $평가결과[$LangID];
		}
		?>
    
        </h3>

		<form name="SearchForm" method="get">
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


                        $OrgEP = isset($Row["EP"])?$Row["EP"]:0;  //조직 업적 평균 점수
                        $OrgCP = isset($Row["CP"])?$Row["CP"]:0;  //조직 역량 평균 점수
                        $OrgRP = isset($Row["RP"])?$Row["RP"]:0;  //조직 성과 평균 점수

                        // 소속된 조직의 MemberID 가져오기
                        $Sql = "SELECT MemberID FROM Hr_OrganLevelTaskMembers 
                                    WHERE Hr_OrganLevelID = :Hr_OrganLevelID";
                        $Stmt = $DbConn->prepare($Sql);
                        $Stmt->bindParam(':Hr_OrganLevelID', $My_OrganLevelID);
                        $Stmt->execute();
                        $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                        $countLine = $Stmt->rowCount();  // 출력한 라인 갯수를 가져온다.


					       ?>       
                            <h4>▷ 부서별 결과</h4>

                            <div class="report_chart_1">
                                <div id="chartdiv_1"></div>
                            </div>   

                            <style>
                                #chartdiv_1 {
                                    float:left;
                                    width: 100%;
                                    height: 300px;
                                }    
                            </style>

                            

							<table class="uk-table uk-table-align-vertical">
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="14%">성명(ID)</td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="15%">직무</td>
                                   <!--<td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="14%">소속부서</td>-->
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="14%">업적평가<br>점수</td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="14%">역량평가<br>점수</td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="14%">성과평가<br>점수</td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="14%">성과평가<br>등급</td>
                                </tr>
                                <tbody>
                            <?
                            $organPoint = array();
                            $organName = array();
                            array_push($organName, "부서평균");
                            array_push($organPoint, $OrgRP);
                            while($Row = $Stmt->fetch()) {
                                
                                // 위 쿼리에서 가져온 조직원의 MemberID를 $organMemberID에 넣어준다.
                                $organMemberID = $Row['MemberID'];

                                #-------------------------------------------------------------------------------------------------------------#
                                $Sql2 = "SELECT MemberName, B.Hr_EndEvaluationPoint, B.Hr_EvaluationCompetencyEndPoint, 
                                                 B.Hr_ResultTotalPoint, B.Hr_ResultLevel, Hr_OrganPositionName
                                            FROM Members  AS A
                                            LEFT JOIN (SELECT * FROM Hr_Staff_ResultEvaluation WHERE Hr_EvaluationID = :Hr_EvaluationID)  AS B ON A.MemberID = B.MemberID
                                            LEFT JOIN Hr_OrganLevelTaskMembers AS C ON A.MemberID = C.MemberID
                            
                                            WHERE A.MemberID = :MemberID ";
                                $Stmt2 = $DbConn->prepare($Sql2);
                                //$Stmt2->bindParam(':Hr_OrganLevelID', $Hr_OrganLevelID);
                                $Stmt2->bindParam(':Hr_EvaluationID', $SearchState);
                                $Stmt2->bindParam(':MemberID', $organMemberID);
                                $Stmt2->execute();
                                $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                                $Row2 = $Stmt2->fetch();
                                array_push($organName, isset($Row2["MemberName"])?$Row2["MemberName"]:"-");
                                array_push($organPoint, isset($Row2["Hr_ResultTotalPoint"])?$Row2["Hr_ResultTotalPoint"]:0);
                                $Stmt2 = null;
                                ?>
                                    <tr>
                                        <td class="uk-text-wrap uk-table-td-center"><?=$Row2["MemberName"]?></td>
                                        <td class="uk-text-wrap uk-table-td-center"><?=$Row2["Hr_OrganPositionName"]?></td>
                                        <td class="uk-text-wrap uk-table-td-center"><?=isset($Row2["Hr_EndEvaluationPoint"])?$Row2["Hr_EndEvaluationPoint"]:"-"?></td>
                                        <td class="uk-text-wrap uk-table-td-center"><?=isset($Row2["Hr_EvaluationCompetencyEndPoint"])?$Row2["Hr_EvaluationCompetencyEndPoint"]:"-"?></td>
                                        <td class="uk-text-wrap uk-table-td-center"><?=isset($Row2["Hr_ResultTotalPoint"])?$Row2["Hr_ResultTotalPoint"]:"-"?></td>
                                        <td class="uk-text-wrap uk-table-td-center"><?=isset($Row2["Hr_ResultLevel"])?$Row2["Hr_ResultLevel"]:"-"?></td>

                                    </tr>
                                <?

                            }    
							?>
                                </tbody>
                            </table>


                            <script>
                                am4core.ready(function() {

                                // Themes begin
                                am4core.useTheme(am4themes_animated);
                                // Themes end

                                am4core.options.commercialLicense = true;

                                // Create chart instance
                                var chart = am4core.create("chartdiv_1", am4charts.XYChart);

                                // Add data
                                chart.data = [
                                <?    
                                for ($i=0;$i<$countLine;$i++) {
                                    
                                    echo "
                                        {
                                            'division': '".$organName[$i]."',
                                            'mine': ".$organPoint[$i]."
                                        },    
                                    ";
                                } 
                                ?>

                                 ];

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

                                chart.cursor = new am4charts.XYCursor();
                                chart.cursor.lineX.disabled = true;
                                chart.cursor.lineY.disabled = true;

                                }); // end am4core.ready()
                            </script>


                            <div style='float:unset;height:250px;'>&nbsp;</div>

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
	document.SearchForm.action = "hr_staff_organ_indicator_list.php";
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