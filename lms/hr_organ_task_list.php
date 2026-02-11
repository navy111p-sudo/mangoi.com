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
$SubMenuID = 8803;
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
	$AddSqlWhere = $AddSqlWhere . " and A.Hr_OrganTask1State=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.Hr_OrganTask1State<>0 ";

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
		from Hr_OrganTask1 A 
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
			ifnull(B.CenterName, '-') as CenterName
		from Hr_OrganTask1 A 
			left outer join Centers B on A.CenterID=B.CenterID 
		where ".$AddSqlWhere." 
		order by A.Hr_OrganTask1Name asc";// limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$직무_관리[$LangID]?></h3>

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
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap style="width:30px;">No</th>
										<th nowrap style="width:150px;"><?=$직무군명[$LangID]?></th>
										<th nowrap style="width:50px;"><?=$관리[$LangID]?></th>
										<th nowrap style="width:50px;"><?=$상태[$LangID]?></th>
										<th nowrap><?=$직무목록[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$Hr_OrganTask1ID = $Row["Hr_OrganTask1ID"];
										$Hr_OrganTask1State = $Row["Hr_OrganTask1State"];
										
										$CenterName = $Row["CenterName"];
										$Hr_OrganTask1Name = $Row["Hr_OrganTask1Name"];
										

										if ($Hr_OrganTask1State==1){
											$Str_Hr_OrganTask1State = "<span class=\"ListState_1\">사용중</span>";
										}else if ($Hr_OrganTask1State==2){
											$Str_Hr_OrganTask1State = "<span class=\"ListState_2\">미사용</span>";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganTask1Name?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:OpenOrganTask1Form(<?=$Hr_OrganTask1ID?>)"><?=$관리[$LangID]?></a>
										</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_OrganTask1State?></td>
										<td class="uk-text-nowrap uk-table-td-center">



											<table class="uk-table uk-table-align-vertical">
												<thead>
													<tr>
														<th nowrap style="width:30px;">No</th>
														<th nowrap><?=$직무명[$LangID]?></th>
														<th nowrap style="width:80px;"><?=$레벨[$LangID]?></th>
														<th nowrap style="width:80px;"><?=$목표설정[$LangID]?><br><?=$기간[$LangID]?></th>
														<th nowrap style="width:80px;"><?=$업적평가[$LangID]?><br><?=$기간[$LangID]?></th>
														<th nowrap style="width:80px;"><?=$역량평가[$LangID]?><br><?=$기간[$LangID]?></th>
														<th nowrap style="width:60px;"><?=$업적[$LangID]?><br>(<?=$개인[$LangID]?>)</th>
														<th nowrap style="width:60px;"><?=$업적[$LangID]?><br>(<?=$부서[$LangID]?>)</th>
														<th nowrap style="width:60px;"><?=$역량[$LangID]?><br>(<?=$부하[$LangID]?>)</th>
														<th nowrap style="width:60px;"><?=$역량[$LangID]?><br>(<?=$동료[$LangID]?>)</th>
														<th nowrap style="width:60px;"><?=$역량[$LangID]?><br>(<?=$상사[$LangID]?>)</th>

														<th nowrap style="width:50px;"><?=$관리[$LangID]?></th>
													</tr>
												</thead>
												<tbody>
													
													<?php
													$Sql2 = "
															select 
																A.*,
																B.Hr_OrganTaskCheckGoalTypeName,
																C.Hr_OrganTaskCheckPerformTypeName,
																D.Hr_OrganTaskCheckAbilityTypeName
															from Hr_OrganTask2 A 
																inner join Hr_OrganTaskCheckGoalTypes B on A.Hr_OrganTaskCheckGoalTypeID=B.Hr_OrganTaskCheckGoalTypeID 
																inner join Hr_OrganTaskCheckPerformTypes C on A.Hr_OrganTaskCheckPerformTypeID=C.Hr_OrganTaskCheckPerformTypeID 
																inner join Hr_OrganTaskCheckAbilityTypes D on A.Hr_OrganTaskCheckAbilityTypeID=D.Hr_OrganTaskCheckAbilityTypeID 
															where A.Hr_OrganTask1ID=:Hr_OrganTask1ID 
															order by A.Hr_OrganTask2Name asc";// limit $StartRowNum, $PageListNum";

													$Stmt2 = $DbConn->prepare($Sql2);
													$Stmt2->bindParam(':Hr_OrganTask1ID', $Hr_OrganTask1ID);
													$Stmt2->execute();
													$Stmt2->setFetchMode(PDO::FETCH_ASSOC);


													$ListCount2 = 1;
													while($Row2 = $Stmt2->fetch()) {
														
														$Hr_OrganTask2ID = $Row2["Hr_OrganTask2ID"];
														$Hr_OrganTask2State = $Row2["Hr_OrganTask2State"];
														$Hr_OrganTask2Name = $Row2["Hr_OrganTask2Name"];
														$Hr_OrganLevel = $Row2["Hr_OrganLevel"];

														$Hr_OrganTask2KpiRatio1 = $Row2["Hr_OrganTask2KpiRatio1"];
														$Hr_OrganTask2KpiRatio2 = $Row2["Hr_OrganTask2KpiRatio2"];
														$Hr_OrganTask2CompetencyRatio1 = $Row2["Hr_OrganTask2CompetencyRatio1"];
														$Hr_OrganTask2CompetencyRatio2 = $Row2["Hr_OrganTask2CompetencyRatio2"];
														$Hr_OrganTask2CompetencyRatio3 = $Row2["Hr_OrganTask2CompetencyRatio3"];

														$Hr_OrganTaskCheckGoalTypeName = $Row2["Hr_OrganTaskCheckGoalTypeName"];
														$Hr_OrganTaskCheckPerformTypeName = $Row2["Hr_OrganTaskCheckPerformTypeName"];
														$Hr_OrganTaskCheckAbilityTypeName = $Row2["Hr_OrganTaskCheckAbilityTypeName"];
													?>
													<tr style="border-bottom:1px solid #cccccc;">
														<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount2?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganTask2Name?></td>
														<td class="uk-text-nowrap uk-table-td-center">LEVEL <?=$Hr_OrganLevel?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganTaskCheckGoalTypeName?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganTaskCheckPerformTypeName?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganTaskCheckAbilityTypeName?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganTask2KpiRatio1?> %</td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganTask2KpiRatio2?> %</td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganTask2CompetencyRatio1?> %</td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganTask2CompetencyRatio2?> %</td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganTask2CompetencyRatio3?> %</td>

														<td class="uk-text-nowrap uk-table-td-center">
															<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:OpenOrganTask2Form(<?=$Hr_OrganTask1ID?>, <?=$Hr_OrganTask2ID?>)"><?=$관리[$LangID]?></a>
														</td>
													</tr>
													<?php
														$ListCount2 ++;
													}
													$Stmt2 = null;
													?>


												</tbody>
											</table>

											<div class="uk-form-row" style="text-align:right; margin-top:20px;">
												<a type="button" href="javascript:OpenOrganTask2Form(<?=$Hr_OrganTask1ID?>, '')" class="md-btn md-btn-primary" style="background-color:#556BAC;"><?=$직무[$LangID]?> <?=$신규등록[$LangID]?></a>
											</div>



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
							<a type="button" href="javascript:OpenOrganTask1Form('')" class="md-btn md-btn-primary"><?=$직무군[$LangID]?> <?=$신규등록[$LangID]?></a>
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
function OpenOrganTask1Form(Hr_OrganTask1ID){
	openurl = "hr_organ_task_1_form.php?Hr_OrganTask1ID="+Hr_OrganTask1ID;
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

function OpenOrganTask2Form(Hr_OrganTask1ID, Hr_OrganTask2ID){
	openurl = "hr_organ_task_2_form.php?Hr_OrganTask1ID="+Hr_OrganTask1ID+"&Hr_OrganTask2ID="+Hr_OrganTask2ID;
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
	document.SearchForm.action = "hr_organ_task_list.php";
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