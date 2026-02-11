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
$SubMenuID = 2512;
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
$SearchProductSellerID = isset($_REQUEST["SearchProductSellerID"]) ? $_REQUEST["SearchProductSellerID"] : "";

if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 10;
}


if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}

if ($SearchState==""){
	$SearchState = "1";
}	
		
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.ProductCategoryState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.ProductCategoryState<>0 ";

if ($SearchProductSellerID!=""){
	$ListParam = $ListParam . "&SearchProductSellerID=" . $SearchProductSellerID;
	$AddSqlWhere = $AddSqlWhere . " and A.ProductSellerID =".$SearchProductSellerID." ";
}


if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.ProductCategoryName like '%".$SearchText."%' ";
}


$PaginationParam = $ListParam;

if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}



$ListParam = str_replace("&", "^^", $ListParam);


$Sql = "select 
				count(*) TotalRowCount 
		from ProductCategories A 
			inner join ProductSellers B on A.ProductSellerID=B.ProductSellerID 
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
			ifnull((select count(*) from Products where ProductCategoryID=A.ProductCategoryID and ProductState=1),0) as ProductCount,
			B.ProductSellerName
		from ProductCategories A 
			inner join ProductSellers B on A.ProductSellerID=B.ProductSellerID 
		where ".$AddSqlWhere." order by A.ProductCategoryOrder asc";//." limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$판매교재그룹관리[$LangID]?></h3>


		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					
					<div class="uk-width-medium-4-10"></div>


					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						<select id="SearchProductSellerID" name="SearchProductSellerID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="판매구분" style="width:100%;"/>
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


					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$교재명[$LangID]?></label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>

					<div class="uk-width-medium-1-10">
						<div class="uk-margin-small-top">
							<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$사용중[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$미사용[$LangID]?></option>
							</select>
						</div>
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
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th style="width:10%;" nowrap>No</th>
										<th style="width:15%;" nowrap><?=$판매구분[$LangID]?></th>
										<th style="width:15%;" nowrap><?=$교재그룹명[$LangID]?></th>
										<th style="width:47%" nowrap><?=$설명[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$교재수[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$상태[$LangID]?></th>
										<th style="width:3%" nowrap><?=$순서[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$ProductCategoryID = $Row["ProductCategoryID"];
										$ProductCategoryName = $Row["ProductCategoryName"];
										$ProductCategoryMemo = $Row["ProductCategoryMemo"];
										$ProductCategoryState = $Row["ProductCategoryState"];

										$ProductCount = $Row["ProductCount"];

										$ProductSellerName = $Row["ProductSellerName"];
										
										if ($ProductCategoryState==1){
											$StrProductCategoryState = "<span class=\"ListState_1\">사용중</span>";
										}else if ($ProductCategoryState==2){
											$StrProductCategoryState = "<span class=\"ListState_2\">미사용</span>";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ProductCategoryID?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ProductSellerName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenProductCategoryFrom(<?=$ProductCategoryID?>)"><?=$ProductCategoryName?></a></td>
										<td class="uk-text-nowrap uk-table-td" style="white-space:normal;"><?=$ProductCategoryMemo?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ProductCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrProductCategoryState?></td>
										<td class="uk-text-nowrap uk-table-td-center">
										<?php
											if($SearchText=="" && $SearchState!="100") {
										?>
											<div class="uk-text-nowrap uk-table-td-center">
												<a href="javascript:ProductCategoryListOrder(<?=$ProductCategoryID?>, 1)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block" >arrow_drop_up</i></a>
												<a href="javascript:ProductCategoryListOrder(<?=$ProductCategoryID?>, 0)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block">arrow_drop_down</i></a>
											</div>
										<?php
											} else {
										?>
											-
										<?php
											}
										?>										
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

						<div class="uk-form-row" style="text-align:center;margin-top:20px;">
							<a type="button" href="javascript:OpenProductCategoryFrom('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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

function ProductCategoryListOrder(ProductCategoryID, OrderType) {
	url = "ajax_set_product_category_list_order.php";

	
	//location.href = url + "?TeacherCharacterItemID="+TeacherCharacterItemID+"&OrderType="+OrderType;

    $.ajax(url, {
        data: {
			ProductCategoryID: ProductCategoryID,
			OrderType: OrderType
        },
        success: function () {
			location.reload();
        },
        error: function () {
            alert('Error while contacting server, please try again');
        }

    });
}


function OpenProductCategoryFrom(ProductCategoryID){
	openurl = "product_category_form.php?ProductCategoryID="+ProductCategoryID;
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

</script>




<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "product_category_list.php";
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