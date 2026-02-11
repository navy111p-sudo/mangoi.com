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
$SubMenuID = 2513;
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
	$AddSqlWhere = $AddSqlWhere . " and A.ProductSellerState=$SearchState ";
}
$ListParam = $ListParam . "&SearchState=" . $SearchState;
$AddSqlWhere = $AddSqlWhere . " and A.ProductSellerState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.ProductSellerName like '%".$SearchText."%' ";
}





$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
			count(*) TotalRowCount 
		from ProductSellers A 
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
			A.*
		from ProductSellers A 
		where ".$AddSqlWhere." 
		order by A.ProductSellerOrder desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$교재판매구분_관리[$LangID]?></h3>

		<form name="SearchForm" method="get" style="display:none;">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					
					<div class="uk-width-medium-8-10"></div>

					

					<!--
					<div class="uk-width-medium-1-10">
						<label for="product_seller_search_price">Price</label>
						<input type="text" class="md-input" id="product_seller_search_price">
					</div>
					-->

					<div class="uk-width-medium-1-10" style="display:none;">
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
							<input type="checkbox" name="product_seller_search_active" id="product_seller_search_active" data-md-icheck/>
							<label for="product_seller_search_active" class="inline-label">Active</label>
						</div>
					</div>
					-->

					<div class="uk-width-medium-1-10 uk-text-center" style="display:none;">
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
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th style="width:10%;" nowrap>No</th>
										<th nowrap><?=$판매구분명[$LangID]?></th>
										<th style="width:15%;" nowrap><?=$배송료_부과타입[$LangID]?></th>
										<th style="width:15%;" nowrap><?=$배송료_부과기준[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$배송료[$LangID]?></th>
										<!--<th style="width:10%;" nowrap>취소마감시간</th>-->
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$ProductSellerID = $Row["ProductSellerID"];
										$ProductSellerName = $Row["ProductSellerName"];
										$ProductSellerShipPriceType = $Row["ProductSellerShipPriceType"];
										$ProductSellerOrderTotPrice = $Row["ProductSellerOrderTotPrice"];
										$ProductSellerShipPrice = $Row["ProductSellerShipPrice"];
										$ProductSellerCancelLiminTime = $Row["ProductSellerCancelLiminTime"];

										if ($ProductSellerShipPriceType==1){
											$StrProductSellerShipPriceType = "무조건";
										}else if ($ProductSellerShipPriceType==2){
											$StrProductSellerShipPriceType = "부과기준 이하일때";
										}else if ($ProductSellerShipPriceType==3){
											$StrProductSellerShipPriceType = "부과기준 이상일때";
										}

										$StrProductSellerCancelLiminTime = substr($ProductSellerCancelLiminTime, 0,2).":".substr($ProductSellerCancelLiminTime, -2);
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="product_seller_form.php?ListParam=<?=$ListParam?>&ProductSellerID=<?=$ProductSellerID?>"><?=$ProductSellerName?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrProductSellerShipPriceType?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductSellerOrderTotPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductSellerShipPrice,0)?></td>
										<!--<td class="uk-text-nowrap uk-table-td-center"><?=$StrProductSellerCancelLiminTime?></td>-->
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

						<!--
						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="product_seller_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
						</div>
						-->

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
	document.SearchForm.action = "product_seller_list.php";
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