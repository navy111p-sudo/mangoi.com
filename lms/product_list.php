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
$MainMenuID = 25;
$SubMenuID = 2511;
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
$SearchProductCategoryID = isset($_REQUEST["SearchProductCategoryID"]) ? $_REQUEST["SearchProductCategoryID"] : "";
$SearchProductSellerID = isset($_REQUEST["SearchProductSellerID"]) ? $_REQUEST["SearchProductSellerID"] : "";


if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 100;
}


if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}

if ($SearchState==""){
	$SearchState = "100";
}	
		
if ($SearchState!="100"){
	$AddSqlWhere = $AddSqlWhere . " and A.ProductState=$SearchState ";
}
$ListParam = $ListParam . "&SearchState=" . $SearchState;
$AddSqlWhere = $AddSqlWhere . " and A.ProductState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and B.ProductCategoryState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.ProductName like '%".$SearchText."%' ";
}


if ($SearchProductSellerID!=""){
	$ListParam = $ListParam . "&SearchProductSellerID=" . $SearchProductSellerID;
	$AddSqlWhere = $AddSqlWhere . " and C.ProductSellerID=$SearchProductSellerID ";
}


if ($SearchProductCategoryID!=""){
	$ListParam = $ListParam . "&SearchProductCategoryID=" . $SearchProductCategoryID;
	$AddSqlWhere = $AddSqlWhere . " and A.ProductCategoryID=$SearchProductCategoryID ";
}

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
			count(*) TotalRowCount 
		from Products A 
			inner join ProductCategories B on A.ProductCategoryID=B.ProductCategoryID 
			inner join ProductSellers C on B.ProductSellerID=C.ProductSellerID 
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
			B.ProductSellerID,
			B.ProductCategoryName,
			C.ProductSellerName
		from Products A 
			inner join ProductCategories B on A.ProductCategoryID=B.ProductCategoryID 
			inner join ProductSellers C on B.ProductSellerID=C.ProductSellerID 
		where ".$AddSqlWhere." 
		order by A.ProductOrder desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$판매교재관리[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					
					<div class="uk-width-medium-2-10"></div>

					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						<select id="SearchProductSellerID" name="SearchProductSellerID" class="uk-width-1-1" onchange="SearchSubmit_1()" data-md-select2 data-allow-clear="true" data-placeholder="판매구분" style="width:100%;"/>
							<option value=""></option>
							<?
							$Sql2 = "select 
											A.* 
									from ProductSellers A 
									order by A.ProductSellerOrder asc";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldProductSellerState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$ProductSellerID = $Row2["ProductSellerID"];
								$ProductSellerName = $Row2["ProductSellerName"];
								$ProductSellerState = $Row2["ProductSellerState"];

								if ($OldProductSellerState!=$ProductSellerState){
									if ($OldSelectProductCategoryState!=-1){
										echo "</optgroup>";
									}
									
									if ($ProductSellerState==1){
										echo "<optgroup label=\"판매구분(사용중)\">";
									}else if ($SelectProductCategoryState==2){
										echo "<optgroup label=\"판매구분(미사용)\">";
									}
								} 
								$OldProductSellerState = $ProductSellerState;
							?>

							<option value="<?=$ProductSellerID?>" <?if ($SearchProductSellerID==$ProductSellerID){?>selected<?}?>><?=$ProductSellerName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>


					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						<select id="SearchProductCategoryID" name="SearchProductCategoryID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="교재그룹선택" style="width:100%;"/>
							<option value=""></option>
							<?
							if ($SearchProductSellerID!=""){
								$Sql2 = "select 
												A.* 
										from ProductCategories A 
										where A.ProductCategoryState<>0 and A.ProductSellerID=".$SearchProductSellerID."
										order by A.ProductCategoryState asc, A.ProductCategoryName asc";
								$Stmt2 = $DbConn->prepare($Sql2);
								$Stmt2->execute();
								$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
								
								$OldSelectProductCategoryState = -1;
								while($Row2 = $Stmt2->fetch()) {
									$SelectProductCategoryID = $Row2["ProductCategoryID"];
									$SelectProductCategoryName = $Row2["ProductCategoryName"];
									$SelectProductCategoryState = $Row2["ProductCategoryState"];

									if ($OldSelectProductCategoryState!=$SelectProductCategoryState){
										if ($OldSelectProductCategoryState!=-1){
											echo "</optgroup>";
										}
										
										if ($SelectProductCategoryState==1){
											echo "<optgroup label=\"교재그룹(사용중)\">";
										}else if ($SelectProductCategoryState==2){
											echo "<optgroup label=\"교재그룹(미사용)\">";
										}
									} 
									$OldSelectProductCategoryState = $SelectProductCategoryState;
							?>

							<option value="<?=$SelectProductCategoryID?>" <?if ($SearchProductCategoryID==$SelectProductCategoryID){?>selected<?}?>><?=$SelectProductCategoryName?></option>
							<?
								}
								$Stmt2 = null;
							}
							?>
						</select>
					</div>

					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$교재명[$LangID]?></label>
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
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$판매[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$미판매[$LangID]?></option>
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


							<!--
							<div class="uk-width-medium-2-10 uk-text-center">
								<a type="button" href="product_list_allbooks_excel.php" class="md-btn md-btn-primary uk-margin-small-top" style="width:100%;background-color:#c1c1c1;">올북스교재 다운로드</a>
							</div>
							-->


							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th style="width:10%;" nowrap>No</th>
										<th style="width:15%;" nowrap><?=$판매구분[$LangID]?></th>
										<th style="width:15%;" nowrap><?=$교재그룹명[$LangID]?></th>
										<th nowrap><?=$교재명[$LangID]?></th>
										<th nowrap><?=$고유코드[$LangID]?></th>
										<th style="width:10%;" nowrap>ISBN</th>
										<th style="width:10%;" nowrap><?=$판매가[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$올북스보유[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$ProductSellerID = $Row["ProductSellerID"];
										$ProductSellerBookID = $Row["ProductSellerBookID"];
										$ProductISBN = $Row["ProductISBN"];
										$ProductID = $Row["ProductID"];
										$ProductName = $Row["ProductName"];
										$ProductPrice = $Row["ProductPrice"];
										$ProductState = $Row["ProductState"];
										$ProductView = $Row["ProductView"];
										$ProductCategoryName = $Row["ProductCategoryName"];
										$ProductSellerName = $Row["ProductSellerName"];

										if ($ProductSellerID==2){
											if ($ProductView==1){
												$StrProductView = "<span class=\"ListState_1\">".$보유[$LangID]."</span>";
											}else{
												$StrProductView = "<span class=\"ListState_2\">".$재고없음[$LangID]."</span>";
											}
										}else{
											$StrProductView = "-";
										}

										if ($ProductState==1){
											$StrProductState = "<span class=\"ListState_1\">".$판매[$LangID]."</span>";
										}else if ($ProductState==2){
											$StrProductState = "<span class=\"ListState_2\">".$미판매[$LangID]."</span>";
										}


									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ProductSellerName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ProductCategoryName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="product_form.php?ListParam=<?=$ListParam?>&ProductID=<?=$ProductID?>"><?=$ProductName?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ProductSellerBookID?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ProductISBN?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrProductView?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrProductState?></td>
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
							<a type="button" href="product_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a> 
						</div>
						<div class="uk-form-row" style="text-align:center;">
							올북스 교재는 자동으로 추가됩니다. 신규등록으로는 추가할 수 없습니다.
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
function SearchSubmit_1(){
	document.SearchForm.SearchProductCategoryID.value = "";
	SearchSubmit();
}

function SearchSubmit(){
	document.SearchForm.action = "product_list.php";
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