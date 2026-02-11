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
$SubMenuID = 8811;
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
	$AddSqlWhere = $AddSqlWhere . " and A.Hr_EvaluationState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.Hr_EvaluationState<>0 ";

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
		from Hr_Evaluations A 
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
			ifnull(B.CenterName, '-') as CenterName,
			C.Hr_EvaluationTypeName,
			D.Hr_EvaluationCycleName
		from Hr_Evaluations A 
			left outer join Centers B on A.CenterID=B.CenterID 
			inner join Hr_EvaluationTypes C on A.Hr_EvaluationTypeID=C.Hr_EvaluationTypeID 
			inner join Hr_EvaluationCycles D on A.Hr_EvaluationCycleID=D.Hr_EvaluationCycleID 
		where ".$AddSqlWhere." 
		order by A.Hr_EvaluationYear desc, A.Hr_EvaluationMonth desc, A.Hr_EvaluationDate desc";// limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$평가_관리[$LangID]?></h3>

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
										<th nowrap>No</th>
										<th nowrap><?=$평가명[$LangID]?></th>
										<th nowrap><?=$평가일[$LangID]?></th>
										<th nowrap><?=$평가주기[$LangID]?></th>
										<th nowrap><?=$평가방식[$LangID]?></th>

										<th nowrap><?=$평가기간[$LangID]?></th>
										<th nowrap><?=$목표설정기간[$LangID]?></th>

										<th nowrap><?=$역량평가등록여부[$LangID]?></th>
										<th nowrap><?=$평가자점수확인[$LangID]?></th>
										<th nowrap><?=$평가근거기록[$LangID]?></th>
										<th nowrap><?=$평가총평기록[$LangID]?></th>

										<th nowrap><?=$관리[$LangID]?></th>
										<th nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$CenterID = $Row["CenterID"];

										$Hr_EvaluationID = $Row["Hr_EvaluationID"];
										$Hr_EvaluationTypeID = $Row["Hr_EvaluationTypeID"];
										$Hr_EvaluationCycleID = $Row["Hr_EvaluationCycleID"];

										$Hr_EvaluationYear = $Row["Hr_EvaluationYear"];
										$Hr_EvaluationMonth = $Row["Hr_EvaluationMonth"];
										$Hr_EvaluationName = $Row["Hr_EvaluationName"];
										$Str_Hr_EvaluationYear = $Hr_EvaluationYear."년 ".substr("0".$Hr_EvaluationMonth,-2)."월 ".$Hr_EvaluationName;

										$Hr_EvaluationDate = $Row["Hr_EvaluationDate"];
										$Hr_EvaluationStartDate = $Row["Hr_EvaluationStartDate"];
										$Hr_EvaluationEndDate = $Row["Hr_EvaluationEndDate"];
										$Hr_EvaluationGoalStartDate = $Row["Hr_EvaluationGoalStartDate"];
										$Hr_EvaluationGoalEndDate = $Row["Hr_EvaluationGoalEndDate"];

										$Hr_EvaluationUseCompetency = $Row["Hr_EvaluationUseCompetency"];
										$Hr_EvaluationUseScore = $Row["Hr_EvaluationUseScore"];
										$Hr_EvaluationUseWarrant = $Row["Hr_EvaluationUseWarrant"];
										$Hr_EvaluationUseOverall = $Row["Hr_EvaluationUseOverall"];
										
										$Hr_EvaluationState = $Row["Hr_EvaluationState"];
										
										$CenterName = $Row["CenterName"];
										$Hr_EvaluationTypeName = $Row["Hr_EvaluationTypeName"];
										$Hr_EvaluationCycleName = $Row["Hr_EvaluationCycleName"];

										if ($Hr_EvaluationUseCompetency==1){
											$Str_Hr_EvaluationUseCompetency = "<?=$등록[$LangID]?>";

											if ($Hr_EvaluationUseScore==1){
												$Str_Hr_EvaluationUseScore = "O";
											}else{
												$Str_Hr_EvaluationUseScore = "X";
											}
											if ($Hr_EvaluationUseWarrant==1){
												$Str_Hr_EvaluationUseWarrant = "O";
											}else{
												$Str_Hr_EvaluationUseWarrant = "X";
											}
											if ($Hr_EvaluationUseOverall==1){
												$Str_Hr_EvaluationUseOverall = "O";
											}else{
												$Str_Hr_EvaluationUseOverall = "X";
											}

										}else{
											$Str_Hr_EvaluationUseCompetency = "<?=$등록안함[$LangID]?>";

											$Str_Hr_EvaluationUseScore = "-";
											$Str_Hr_EvaluationUseWarrant = "-";
											$Str_Hr_EvaluationUseOverall = "-";
										}


										if ($Hr_EvaluationState==1){
											$Str_Hr_EvaluationState = "<span class=\"ListState_1\"><?=$사용중[$LangID]?></span>";
										}else if ($Hr_EvaluationState==2){
											$Str_Hr_EvaluationState = "<span class=\"ListState_2\"><?=$미사용[$LangID]?></span>";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_EvaluationYear?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_EvaluationDate?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_EvaluationCycleName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_EvaluationTypeName?></td>

										<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_EvaluationStartDate?><br><?=$Hr_EvaluationEndDate?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_EvaluationGoalStartDate?><br><?=$Hr_EvaluationGoalEndDate?></td>

										<td class="uk-text-nowrap uk-table-td-center">
											<?=$Str_Hr_EvaluationUseCompetency?>
										</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_EvaluationUseScore?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_EvaluationUseWarrant?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_EvaluationUseOverall?></td>

										<td class="uk-text-nowrap uk-table-td-center">
											<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:OpenEvaluationForm(<?=$Hr_EvaluationID?>)"><?=$평가관리[$LangID]?></a>
										</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_EvaluationState?></td>
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
							<a type="button" href="javascript:OpenEvaluationForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
	document.SearchForm.action = "hr_evaluation_list.php";
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