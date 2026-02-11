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
$SubMenuID = 1122;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>


 
<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
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
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherListBestState=$SearchState ";
}

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherListBestName like '%".$SearchText."%' ";
}


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from TeacherListBests A 
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
			ifnull(A1.TeacherName, '-') as TeacherListBestTeacherName1,
			ifnull(A2.TeacherName, '-') as TeacherListBestTeacherName2,
			ifnull(A3.TeacherName, '-') as TeacherListBestTeacherName3,
			ifnull(A4.TeacherName, '-') as TeacherListBestTeacherName4,
			ifnull(A5.TeacherName, '-') as TeacherListBestTeacherName5,
			ifnull(A6.TeacherName, '-') as TeacherListBestTeacherName6,
			ifnull(A7.TeacherName, '-') as TeacherListBestTeacherName7,
			ifnull(A8.TeacherName, '-') as TeacherListBestTeacherName8,
			ifnull(A9.TeacherName, '-') as TeacherListBestTeacherName9,
			ifnull(A10.TeacherName, '-') as TeacherListBestTeacherName10
		from TeacherListBests A 
			left outer join Teachers A1 on A.TeacherListBestTeacherID1=A1.TeacherID 
			left outer join Teachers A2 on A.TeacherListBestTeacherID2=A2.TeacherID 
			left outer join Teachers A3 on A.TeacherListBestTeacherID3=A3.TeacherID 
			left outer join Teachers A4 on A.TeacherListBestTeacherID4=A4.TeacherID 
			left outer join Teachers A5 on A.TeacherListBestTeacherID5=A5.TeacherID 
			left outer join Teachers A6 on A.TeacherListBestTeacherID6=A6.TeacherID 
			left outer join Teachers A7 on A.TeacherListBestTeacherID7=A7.TeacherID 
			left outer join Teachers A8 on A.TeacherListBestTeacherID8=A8.TeacherID 
			left outer join Teachers A9 on A.TeacherListBestTeacherID9=A8.TeacherID 
			left outer join Teachers A10 on A.TeacherListBestTeacherID10=A10.TeacherID 
		where ".$AddSqlWhere." 
		order by A.TeacherListBestID desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$베스트강사_목록[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">


					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$제목[$LangID]?> </label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>

					<!--
					<div class="uk-width-medium-1-10">
						<label for="product_search_price">Price</label>
						<input type="text" class="md-input" id="product_search_price">
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
					<div class="uk-width-medium-1-10">
						<div class="uk-margin-top uk-text-nowrap">
							<input type="checkbox" name="product_search_active" id="product_search_active" data-md-icheck/>
							<label for="product_search_active" class="inline-label">Active</label>
						</div>
					</div>
					-->

					<div class="uk-width-medium-1-10 uk-text-center">
						<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?> </a>
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
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap><?=$제목[$LangID]?> </th>
										<th nowrap>1위</th>
										<th nowrap>2위</th>
										<th nowrap>3위</th>
										<th nowrap>4위</th>
										<th nowrap>5위</th>
										<th nowrap>6위</th>
										<th nowrap>7위</th>
										<th nowrap>8위</th>
										<th nowrap>9위</th>
										<th nowrap>10위</th>
										<th nowrap><?=$상태[$LangID]?> </th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$TeacherListBestID = $Row["TeacherListBestID"];
										$TeacherListBestName = $Row["TeacherListBestName"];
										
										$TeacherListBestTeacherID[1] = $Row["TeacherListBestTeacherID1"];
										$TeacherListBestTeacherID[2] = $Row["TeacherListBestTeacherID2"];
										$TeacherListBestTeacherID[3] = $Row["TeacherListBestTeacherID3"];
										$TeacherListBestTeacherID[4] = $Row["TeacherListBestTeacherID4"];
										$TeacherListBestTeacherID[5] = $Row["TeacherListBestTeacherID5"];
										$TeacherListBestTeacherID[6] = $Row["TeacherListBestTeacherID6"];
										$TeacherListBestTeacherID[7] = $Row["TeacherListBestTeacherID7"];
										$TeacherListBestTeacherID[8] = $Row["TeacherListBestTeacherID8"];
										$TeacherListBestTeacherID[9] = $Row["TeacherListBestTeacherID9"];
										$TeacherListBestTeacherID[10] = $Row["TeacherListBestTeacherID10"];

										$TeacherListBestTeacherName[1] = $Row["TeacherListBestTeacherName1"];
										$TeacherListBestTeacherName[2] = $Row["TeacherListBestTeacherName2"];
										$TeacherListBestTeacherName[3] = $Row["TeacherListBestTeacherName3"];
										$TeacherListBestTeacherName[4] = $Row["TeacherListBestTeacherName4"];
										$TeacherListBestTeacherName[5] = $Row["TeacherListBestTeacherName5"];
										$TeacherListBestTeacherName[6] = $Row["TeacherListBestTeacherName6"];
										$TeacherListBestTeacherName[7] = $Row["TeacherListBestTeacherName7"];
										$TeacherListBestTeacherName[8] = $Row["TeacherListBestTeacherName8"];
										$TeacherListBestTeacherName[9] = $Row["TeacherListBestTeacherName9"];
										$TeacherListBestTeacherName[10] = $Row["TeacherListBestTeacherName10"];

										$TeacherListBestState = $Row["TeacherListBestState"];

										if ($TeacherListBestState==1){
											$StrTeacherListBestState = "<span class=\"ListState_1\">사용중</span>";
										}else if ($TeacherListBestState==2){
											$StrTeacherListBestState = "<span class=\"ListState_2\">미사용</span>";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap"><a href="javascript:OpenTeacherBestForm('<?=$TeacherListBestID?>')"><?=$TeacherListBestName?></a></td>
										<?for ($ii=1;$ii<=10;$ii++){?>
										<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherListBestTeacherName[$ii]?></td>
										<?}?>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrTeacherListBestState?></td>
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
						include_once('./inc_pagination.php');
						?>

						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="javascript:OpenTeacherBestForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?> </a>
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
function OpenTeacherBestForm(TeacherListBestID){
	openurl = "teacher_best_form.php?TeacherListBestID="+TeacherListBestID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function SearchSubmit(){
	document.SearchForm.action = "teacher_best_list.php";
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