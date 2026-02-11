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
$SubMenuID = 8802;
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
	$AddSqlWhere = $AddSqlWhere . " and A.Hr_OrganLevelState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.Hr_OrganLevelState<>0 ";

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
		from Hr_OrganLevels A 
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
			ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=A.Hr_OrganLevel1ID), '-') as Hr_OrganLevelName1, 
			ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=A.Hr_OrganLevel2ID), '-') as Hr_OrganLevelName2,
			ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=A.Hr_OrganLevel3ID), '-') as Hr_OrganLevelName3,
			ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=A.Hr_OrganLevel4ID), '-') as Hr_OrganLevelName4
		from Hr_OrganLevels A 
			left outer join Centers B on A.CenterID=B.CenterID 
		where ".$AddSqlWhere." 
		order by A.Hr_OrganLevel1ID asc, A.Hr_OrganLevel2ID asc, A.Hr_OrganLevel3ID asc, A.Hr_OrganLevel4ID asc";// limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$조직_관리[$LangID]?></h3>

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
										<th nowrap>LEVEL 1(경영진)</th>
										<th nowrap>Level 2(부문)</th>
										<th nowrap>Level 3(부서)</th>
										<th nowrap>Level 4(파트)</th>

										<th nowrap><?=$인센티브_S[$LangID]?></th>
										<th nowrap><?=$인센티브_A[$LangID]?></th>
										<th nowrap><?=$인센티브_B[$LangID]?></th>
										<th nowrap><?=$인센티브_C[$LangID]?></th>
										<th nowrap><?=$인센티브_D[$LangID]?></th>

										<th nowrap><?=$관리[$LangID]?></th>
										<th nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$Hr_OrganLevelID = $Row["Hr_OrganLevelID"];
										$Hr_OrganLevelState = $Row["Hr_OrganLevelState"];
										$Hr_Incentive1 = $Row["Hr_Incentive1"];
										$Hr_Incentive2 = $Row["Hr_Incentive2"];
										$Hr_Incentive3 = $Row["Hr_Incentive3"];
										$Hr_Incentive4 = $Row["Hr_Incentive4"];
										$Hr_Incentive5 = $Row["Hr_Incentive5"];
										
										$CenterName = $Row["CenterName"];
										$Hr_OrganLevelName1 = $Row["Hr_OrganLevelName1"];
										$Hr_OrganLevelName2 = $Row["Hr_OrganLevelName2"];
										$Hr_OrganLevelName3 = $Row["Hr_OrganLevelName3"];
										$Hr_OrganLevelName4 = $Row["Hr_OrganLevelName4"];

										$Str_Hr_Incentive1 = number_format($Hr_Incentive1,0);
										$Str_Hr_Incentive2 = number_format($Hr_Incentive2,0);
										$Str_Hr_Incentive3 = number_format($Hr_Incentive3,0);
										$Str_Hr_Incentive4 = number_format($Hr_Incentive4,0);
										$Str_Hr_Incentive5 = number_format($Hr_Incentive5,0);

										if ($Hr_OrganLevelID==1){
											$Str_Hr_Incentive1 = "-";
											$Str_Hr_Incentive2 = "-";
											$Str_Hr_Incentive3 = "-";
											$Str_Hr_Incentive4 = "-";
											$Str_Hr_Incentive5 = "-";
										}
										

										if ($Hr_OrganLevelState==1){
											$Str_Hr_OrganLevelState = "<span class=\"ListState_1\">사용중</span>";
										}else if ($Hr_OrganLevelState==2){
											$Str_Hr_OrganLevelState = "<span class=\"ListState_2\">미사용</span>";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganLevelName1?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganLevelName2?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganLevelName3?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganLevelName4?></td>

										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_Incentive1?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_Incentive2?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_Incentive3?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_Incentive4?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_Incentive5?></td>

										<td class="uk-text-nowrap uk-table-td-center">
											<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:OpenOrganlevelForm(<?=$Hr_OrganLevelID?>)"><?=$관리[$LangID]?></a>
										</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_OrganLevelState?></td>
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
							<a type="button" href="javascript:OpenOrganlevelForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
function OpenOrganlevelForm(Hr_OrganLevelID){
	openurl = "hr_organ_level_form.php?Hr_OrganLevelID="+Hr_OrganLevelID;
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
	document.SearchForm.action = "hr_organ_level_list.php";
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