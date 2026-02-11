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
$MainMenuID = 88;
$SubMenuID = 8806;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>


 
<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
//$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";


if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 30;
}


if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}

if ($SearchState==""){
	$SearchState = "1";
}	
		
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.Hr_KpiIndicatorState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.Hr_KpiIndicatorState<>0 ";

//if ($SearchText!=""){
//	$ListParam = $ListParam . "&SearchText=" . $SearchText;
//	$AddSqlWhere = $AddSqlWhere . " and A.CouponName like '%".$SearchText."%' ";
//}


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from Hr_KpiIndicators A 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );



$Sql = "
		select 
			A.*,
			(select Hr_KpiIndicatorUnitName from Hr_KpiIndicatorUnits where Hr_KpiIndicatorUnitID=A.Hr_KpiIndicatorUnitID) as Hr_KpiIndicatorUnitName
		from Hr_KpiIndicators A 
		where ".$AddSqlWhere." 
		order by A.Hr_KpiIndicatorOrder asc";// limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$KPI_문황관리[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">

					<!--
					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$쿠폰명[$LangID]?> </label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>
					-->

					<div class="uk-width-medium-1-10">
						<div class="uk-margin-small-top">
							<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$사용중[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$미사용[$LangID]?></option>
							</select>
						</div>
					</div>

					<!--
					<div class="uk-width-medium-1-10 uk-text-center">
						<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?> </a>
					</div>
					-->
					
				</div>
			</div>
		</div>
		</form>


		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical" style="width:100%;">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap style="width:200px;"><?=$지표명[$LangID]?></th>
										<th nowrap><?=$정의[$LangID]?></th>
										<th nowrap><?=$측정산식[$LangID]?></th>
										<th nowrap><?=$평가척도[$LangID]?></th>
										<th nowrap><?=$증빙자료출처[$LangID]?></th>
										<th nowrap><?=$관련직무[$LangID]?></th>
										<th nowrap><?=$사용부서[$LangID]?></th>
										<th nowrap><?=$적용단위[$LangID]?></th>
										<th nowrap style="width:80px;"><?=$관리[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$Hr_KpiIndicatorID = $Row["Hr_KpiIndicatorID"];
										$Hr_KpiIndicatorName = $Row["Hr_KpiIndicatorName"];
										$Hr_KpiIndicatorDefine = $Row["Hr_KpiIndicatorDefine"];
										$Hr_KpiIndicatorFormula = $Row["Hr_KpiIndicatorFormula"];
										$Hr_KpiIndicatorMeasure = $Row["Hr_KpiIndicatorMeasure"];
										$Hr_KpiIndicatorSource = $Row["Hr_KpiIndicatorSource"];
										$Hr_KpiIndicatorPartName = $Row["Hr_KpiIndicatorPartName"];
										$Hr_KpiIndicatorUnitID = $Row["Hr_KpiIndicatorUnitID"];
										$Hr_KpiIndicatorState = $Row["Hr_KpiIndicatorState"];

										$Hr_KpiIndicatorUnitName = $Row["Hr_KpiIndicatorUnitName"];


										//$Hr_KpiIndicatorDefine = str_replace("\n","<br>",$Hr_KpiIndicatorDefine);
										//$Hr_KpiIndicatorFormula = str_replace("\n","<br>",$Hr_KpiIndicatorFormula);
										//$Hr_KpiIndicatorMeasure = str_replace("\n","<br>",$Hr_KpiIndicatorMeasure);

										if ($Hr_KpiIndicatorState==1){
											$Str_Hr_KpiIndicatorState = "<span class=\"ListState_1\">사용중</span>";
										}else if ($Hr_KpiIndicatorState==2){
											$Str_Hr_KpiIndicatorState = "<span class=\"ListState_2\">미사용</span>";
										}
									?>
									<tr>
										<td class="uk-text-wrap uk-table-td-center"><?=$ListCount?></td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorName?></td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorDefine?></td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorFormula?></td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorMeasure?></td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorSource?></td>
										<td class="uk-text-wrap uk-table-td-center">

										<?
										$Sql2 = "
												select 
													count(*) as CheckAllCount
												from Hr_KpiIndicatorTasks A 
												where A.Hr_KpiIndicatorID=:Hr_KpiIndicatorID and A.Hr_OrganTask2ID=0 ";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->bindParam(':Hr_KpiIndicatorID', $Hr_KpiIndicatorID);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$CheckAllCount = $Row2["CheckAllCount"];


										if ($CheckAllCount>0){
										?>
										전체
										<?
										}else{
										?>
											
											<?
											$Sql2 = "
												select 
													B.Hr_OrganTask1ID,
													B.Hr_OrganTask2ID,
													B.Hr_OrganTask2Name,
													C.Hr_OrganTask1Name

												from Hr_KpiIndicatorTasks A 
													inner join Hr_OrganTask2 B on A.Hr_OrganTask2ID=B.Hr_OrganTask2ID 
													inner join Hr_OrganTask1 C on B.Hr_OrganTask1ID=C.Hr_OrganTask1ID 
												where A.Hr_KpiIndicatorID=$Hr_KpiIndicatorID 
												order by C.Hr_OrganTask1ID asc, B.Hr_OrganTask2ID asc
											";
											$Stmt2 = $DbConn->prepare($Sql2);
											$Stmt2->execute();
											$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
											$ListCount2 = 1;
											while($Row2 = $Stmt2->fetch()) {
												$Hr_OrganTask1ID = $Row2["Hr_OrganTask1ID"];
												$Hr_OrganTask2ID = $Row2["Hr_OrganTask2ID"];
												$Hr_OrganTask2Name = $Row2["Hr_OrganTask2Name"];
												$Hr_OrganTask1Name = $Row2["Hr_OrganTask1Name"];
												if ($ListCount2>1){
													echo ", ";
												}
											?>
												<?=$Hr_OrganTask2Name?>
											<?
												$ListCount2++;
											}
											$Stmt2=null;
											?>

										<?
										}
										?>
										</td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorPartName?></td>
										<td class="uk-text-wrap uk-table-td-center"><?=$Hr_KpiIndicatorUnitName?></td>
										<td class="uk-text-wrap uk-table-td-center">
											<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:OpenKpiIndicatorForm(<?=$Hr_KpiIndicatorID?>)"><?=$관리[$LangID]?></a>
										</td>
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;
									?>


								</tbody>
							</table>
						</div>
						

						<?php			
						//include_once('./inc_pagination.php');
						?>

						<div class="uk-form-row" style="text-align:center; margin-top:20px;">
							<a type="button" href="javascript:OpenKpiIndicatorForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
function OpenKpiIndicatorForm(Hr_KpiIndicatorID){
	openurl = "hr_kpi_indicator_form.php?Hr_KpiIndicatorID="+Hr_KpiIndicatorID;
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
	document.SearchForm.action = "hr_kpi_indicator_list.php";
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