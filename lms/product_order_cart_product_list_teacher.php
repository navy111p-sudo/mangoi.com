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
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";

?>


<div id="page_content">
	<div id="page_content_inner">

		
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-10-10">


				<form name="SearchForm" method="get">
				<div class="md-card" style="margin-bottom:10px;">
					<div class="md-card-content">
						<div class="uk-grid" data-uk-grid-margin="">
							

							<div class="uk-width-medium-3-10" style="padding-top:7px; display:inline-block;">
								<select id="SearchProductSellerID" name="SearchProductSellerID" class="uk-width-1-1" onchange="SearchSubmit_1()"  data-placeholder="교재판매기준" style="height:30px;width:100%;"/>
									<option value=""><?=$전체[$LangID]?></option>
									<?
									$Sql2 = "select 
													A.* 
											from ProductSellers A 
											where 
												A.ProductSellerState=1 
											order by A.ProductSellerOrder asc";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
									
									while($Row2 = $Stmt2->fetch()) {
										$ProductSellerID = $Row2["ProductSellerID"];
										$ProductSellerName = $Row2["ProductSellerName"];
									?>
									<option value="<?=$ProductSellerID?>"><?=$ProductSellerName?></option>
									<?
									}
									$Stmt2 = null;
									?>
								</select>
							</div>

							<div class="uk-width-medium-3-10" style="padding-top:7px; display:inline-block;">
								<select id="SearchProductCategoryID" name="SearchProductCategoryID" class="uk-width-1-1" onchange="SearchSubmit()"  data-placeholder="교재그룹선택" style="height:30px;width:100%;"/>
									<option value=""><?=$전체[$LangID]?></option>
								</select>
							</div>

							<div class="uk-width-medium-2-10" style=" display:inline-block;">
								<label for="SearchText"><?=$교재명[$LangID]?></label>
								<input type="text" class="md-input" id="SearchText" name="SearchText" value="">
							</div>


							<div class="uk-width-medium-2-10 uk-text-center" style=" display:inline-block;"> 
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

	url = "ajax_set_product_order_cart_detail_teacher.php";

	//location.href = url + "?MemberID=<?=$MemberID?>&ProductID="+ProductID;
	$.ajax(url, {
		data: {
			ProductID: ProductID,
			MemberID: <?=$MemberID?>
		},
		success: function (data) {
			SearchSubmit();
		},
		error: function () {

		}
	});

}

function SelectProductDel(ProductID){
	url = "ajax_set_product_order_cart_detail_teacher_del.php";

	//location.href = url + "?MemberID=<?=$MemberID?>&ProductID="+ProductID;
	$.ajax(url, {
		data: {
			ProductID: ProductID,
			MemberID: <?=$MemberID?>
		},
		success: function (data) {
			SearchSubmit();
		},
		error: function () {

		}
	});
}


function SearchSubmit_1(){
	SearchProductSellerID = document.SearchForm.SearchProductSellerID.value;

	url = "ajax_get_product_category_id.php";
	//window.open(url + "?SearchProductSellerID="+SearchProductSellerID);
	$.ajax(url, {
		data: {
			SearchProductSellerID: SearchProductSellerID
		},
		success: function (data) {

			ArrOption = data.ProductCategoryIDs.split("{{|}}");
			SelBoxInitOption('SearchProductCategoryID');

			SelBoxAddOption( 'SearchProductCategoryID', '전체', "", "");
			for (ii=2 ; ii<=ArrOption.length-1 ; ii++ ){
				ArrOptionText     = ArrOption[ii].split("{|}")[0];
				ArrOptionValue    = ArrOption[ii].split("{|}")[1];
				ArrOptionSelected = ArrOption[ii].split("{|}")[2];
				SelBoxAddOption( 'SearchProductCategoryID', ArrOptionText, ArrOptionValue, ArrOptionSelected );
			}

			SearchSubmit();
		},
		error: function () {
			//alert('오류가 발생했습니다. 다시 시도해 주세요.');
		}
	});	


	
}


function SearchSubmit(){

	SearchProductSellerID = document.SearchForm.SearchProductSellerID.value;
	SearchProductCategoryID = document.SearchForm.SearchProductCategoryID.value;
	SearchText = document.SearchForm.SearchText.value;

	url = "ajax_get_product_search_list_teacher.php";

	//location.href = url + "?SearchProductSellerID="+SearchProductSellerID+"&SearchProductCategoryID="+SearchProductCategoryID+"&SearchText="+SearchText+"&MemberID=<?=$MemberID?>";
	$.ajax(url, {
		data: {
			SearchProductSellerID : SearchProductSellerID,
			SearchProductCategoryID: SearchProductCategoryID,
			SearchText: SearchText,
			MemberID: <?=$MemberID?>
		},
		success: function (data) {
			DivSearchProductList = data.DivSearchProductList;
			document.getElementById("DivSearchProductList").innerHTML = DivSearchProductList;
		},
		error: function () {

		}
	});

}

window.onload = function(){
	SearchSubmit();
}




/** ===================================== 기본함수 ===================================== **/
// Option객체를 생성해서 Return
function SelBoxCreateOption( text, value, selected )
{
	var oOption = document.createElement("OPTION"); // Option 객체를 생성
	oOption.text = text; // Text(Keyword)를 입력
	oOption.value = value; // Value를 입력
	if (selected=="selected"){
		oOption.selected = true;
	}
	return oOption;
}

// SelectBox의 Option을 초기화
function SelBoxInitOption( ObjId ){
	var SelectObj = document.getElementById( ObjId );
	if ( SelectObj == null ) return; // 객체가 존재하지 않으면 취소

	SelectObj.options.length = 0; // 길이를 0으로 하면 초기화
}

// Option을 추가
function SelBoxAddOption( ObjId, text, value, selected ){
	var SelectObj = document.getElementById( ObjId );

	SelectObj.add( SelBoxCreateOption( text , value, selected ) );
	text     = "";
	value    = "";
	selected = "";
}
/** ===================================== 기본함수 ===================================== **/
</script>




<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>