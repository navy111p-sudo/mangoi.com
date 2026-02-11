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
include_once('./inc_common_form_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<?php
$ProductOrderCartID = isset($_REQUEST["ProductOrderCartID"]) ? $_REQUEST["ProductOrderCartID"] : "";


$Sql = "select 
			A.ProductSellerID
		from ProductOrderCarts A 
		where A.ProductOrderCartID=:ProductOrderCartID ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$ProductSellerID = $Row["ProductSellerID"];
?>


<div id="page_content">
	<div id="page_content_inner">

		
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-10-10">


				<form name="SearchForm" method="get">
				<div class="md-card" style="margin-bottom:10px;">
					<div class="md-card-content">
						<div class="uk-grid" data-uk-grid-margin="">
							

							<div class="uk-width-medium-4-10" style="padding-top:7px;">
								<select id="SearchProductCategoryID" name="SearchProductCategoryID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="교재그룹선택" style="width:100%;"/>
									<option value=""></option>
									<?
									$Sql2 = "select 
													A.* 
											from ProductCategories A 
											where 
												A.ProductCategoryState<>0 
												and A.ProductSellerID=$ProductSellerID 
											order by A.ProductCategoryName asc";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
									
									while($Row2 = $Stmt2->fetch()) {
										$ProductCategoryID = $Row2["ProductCategoryID"];
										$ProductCategoryName = $Row2["ProductCategoryName"];
										$SelectProductCategoryState = $Row2["ProductCategoryState"];
									?>
									<option value="<?=$ProductCategoryID?>"><?=$ProductCategoryName?></option>
									<?
									}
									$Stmt2 = null;
									?>
								</select>
							</div>

							<div class="uk-width-medium-4-10">
								<label for="SearchText"><?=$교재명[$LangID]?></label>
								<input type="text" class="md-input" id="SearchText" name="SearchText" value="">
							</div>


							<div class="uk-width-medium-2-10 uk-text-center">
								<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
							</div>
							
						</div>
					</div>
				</div>
				</form>

				<div class="md-card">

					<div class="user_content" id="DivSearchProductList">

					</div>

				</div>

				<div class="uk-form-row" style="text-align:center; margin-top:20px;">
					<a type="button" href="javascript:parent.$.fn.colorbox.close();" class="md-btn md-btn-primary"><?=$완료[$LangID]?></a>
				</div>
			</div>

		</div>


	</div>
</div>


<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<script language="javascript">

function SelectProduct(ProductID){

	url = "ajax_set_product_order_cart_detail.php";

	//location.href = url + "?ProductOrderCartID=<?=$ProductOrderCartID?>&ProductID="+ProductID;
	$.ajax(url, {
		data: {
			ProductID: ProductID,
			ProductOrderCartID: <?=$ProductOrderCartID?>
		},
		success: function (data) {
			SearchSubmit();
		},
		error: function () {

		}
	});

}


function SearchSubmit(){
	SearchProductCategoryID = document.SearchForm.SearchProductCategoryID.value;
	SearchText = document.SearchForm.SearchText.value;

	url = "ajax_get_product_search_list.php";

	//location.href = url + "?SearchProductCategoryID="+SearchProductCategoryID+"&SearchText="+SearchText;
	$.ajax(url, {
		data: {
			SearchProductCategoryID: SearchProductCategoryID,
			SearchText: SearchText,
			ProductOrderCartID: <?=$ProductOrderCartID?>,
		},
		success: function (data) {
			DivSearchProductList = data.DivSearchProductList;
			document.getElementById("DivSearchProductList").innerHTML = DivSearchProductList;
		},
		error: function () {

		}
	});

}

SearchSubmit();

</script>




<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>