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
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";

if ($ProductOrderCartID!=""){

	$Sql = "
			select 
					A.*,
					B.ProductSellerName
			from ProductOrderCarts A 
				inner join ProductSellers B on A.ProductSellerID=B.ProductSellerID 
			where A.ProductOrderCartID=:ProductOrderCartID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$ProductSellerID = $Row["ProductSellerID"];
	$ProductOrderCartName = $Row["ProductOrderCartName"];
	$ProductOrderCartState = $Row["ProductOrderCartState"];

	$ProductSellerName = $Row["ProductSellerName"];



}else{

	$Sql = "select 
			A.ProductSellerID 
		from ProductSellers A 
		where 
			A.ProductSellerState=1 
			and A.ProductSellerID not in (select ProductSellerID from ProductOrderCarts where MemberID=".$MemberID.")
		order by A.ProductSellerOrder asc limit 0, 1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$ProductSellerID = $Row["ProductSellerID"];


	$ProductOrderCartName = $망고아이_교재구매[$LangID];
	$ProductOrderCartState = 1;
}



$Sql = "
		select 
				A.*
		from Members A 
		where A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$MemberName = $Row["MemberName"];
?>


<div id="page_content">
	<div id="page_content_inner">

		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-10-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$MemberName?></span><span class="sub-heading" id="user_edit_position"><?=$교재_구매_바구니[$LangID]?></span></h2>
						</div>
					</div>

					<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
					<input type="hidden" name="ProductOrderCartID" value="<?=$ProductOrderCartID?>">
					<input type="hidden" name="MemberID" value="<?=$MemberID?>">
					<input type="hidden" id="ProductOrderCartName" name="ProductOrderCartName" value="<?=$ProductOrderCartName?>" class="md-input label-fixed"/>
					<div class="user_content">
						
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<?
									if ($ProductOrderCartID!=""){
										$ProductSellerCount = 1;
									?>
										<?=$ProductSellerName?>
										<input type="hidden" name="MemberID" value="<?=$MemberID?>">
									<?
									}else{
									?>
										<?
										$Sql2 = "select 
														A.* 
												from ProductSellers A 
												where 
													A.ProductSellerState=1 
													and A.ProductSellerID not in (select ProductSellerID from ProductOrderCarts where MemberID=".$MemberID.")
												order by A.ProductSellerOrder asc";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										
										$ProductSellerCount = 0;
										while($Row2 = $Stmt2->fetch()) {
											$DbProductSellerID = $Row2["ProductSellerID"];
											$DbProductSellerName = $Row2["ProductSellerName"];
										?>
											<span class="icheck-inline">
												<input type="radio" id="ProductSellerID_<?=$DbProductSellerID?>" name="ProductSellerID" value="<?=$DbProductSellerID?>" <?php if ($DbProductSellerID==$ProductSellerID) { echo "checked";}?> data-md-icheck/>
												<label for="ProductSellerID_<?=$DbProductSellerID?>" class="inline-label"><?=$DbProductSellerName?></label>
											</span>
										<?
											$ProductSellerCount++;
										}
										$Stmt2 = null;

										if ($ProductSellerCount==0){
										?>
										장바구니를 만들 수 없습니다. 기존 바구니를 이용하세요.
										<?
										}
										?>
									<?
									}
									?>
								</div>
							</div>
						</div>


						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-5-10 uk-form-row">
									<span class="icheck-inline">
										<input type="radio" id="ProductOrderCartState_1" name="ProductOrderCartState" value="1" <?php if ($ProductOrderCartState==1) { echo "checked";}?> data-md-icheck/>
										<label for="ProductOrderCartState_1" class="inline-label"><?=$숨김[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" id="ProductOrderCartState_2" name="ProductOrderCartState" value="2" <?php if ($ProductOrderCartState==2) { echo "checked";}?> data-md-icheck/>
										<label for="ProductOrderCartState_2" class="inline-label"><?=$노출[$LangID]?></label>
									</span>

									<?if ($ProductOrderCartID!=""){?>
									<span class="icheck-inline">
										<input type="radio" id="ProductOrderCartState_0" name="ProductOrderCartState" value="0" <?php if ($ProductOrderCartState==0) { echo "checked";}?> data-md-icheck/>
										<label for="ProductOrderCartState_0" class="inline-label"><?=$삭제[$LangID]?></label>
									</span>
									<?}?>

								</div>

							</div>
						</div>


						<div class="uk-margin-top" style="text-align:center;">
							<?if ($ProductSellerCount==0){?>

							<?}else{?>
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?=$저장하기[$LangID]?></a>
							<?}?>
						</div>

					</div>

					</form>


					<?if ($ProductOrderCartID!=""){?>
					
					<form id="ListForm" name="ListForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
					<div class="user_content">

							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th width="10%"><?=$번호[$LangID]?></th>
										<th><?=$교재명[$LangID]?></th>
										<th width="10%"><?=$판매가[$LangID]?></th>
										<th width="10%"><?=$수량[$LangID]?></th>
										<th width="10%"><?=$합계[$LangID]?></th>
										<th width="10%"><?=$삭제[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
								<?

								$Sql = "select 
											A.*,
											B.ProductName,
											B.ProductViewPrice,
											B.ProductPrice
										from ProductOrderCartDetails A 
											inner join Products B on A.ProductID=B.ProductID 
										where A.ProductOrderCartID=:ProductOrderCartID 
										order by A.ProductCartDetailOrder asc";

								$Stmt = $DbConn->prepare($Sql);
								$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
								$Stmt->execute();
								$Stmt->setFetchMode(PDO::FETCH_ASSOC);

								$ListCount=1;
								$ProductPriceTotSum = 0;
								while($Row = $Stmt->fetch()) {
									$ProductOrderCartDetailID = $Row["ProductOrderCartDetailID"];
									$ProductID = $Row["ProductID"];
									$ProductCount = $Row["ProductCount"];
									$RegMemberID = $Row["RegMemberID"];
									$ModiMemberID = $Row["ModiMemberID"];

									$ProductName = $Row["ProductName"];
									$ProductViewPrice = $Row["ProductViewPrice"];
									$ProductPrice = $Row["ProductPrice"];

									$ProductPriceSum = $ProductPrice * $ProductCount;

									$ProductPriceTotSum = $ProductPriceTotSum + $ProductPriceSum;

								?>
								<tr>
									<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
									<td class="uk-text-nowrap"><?=$ProductName?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductPrice,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center">
										<span onclick="ChProductCount(<?=$ProductOrderCartDetailID?>, -1)" style="cursor:pointer;">◀</span>
										<input type="text" name="ProductOrderCount_<?=$ProductOrderCartDetailID?>" id="ProductOrderCount_<?=$ProductOrderCartDetailID?>" onfocus="this.select();" onblur="ChSetProductCount(<?=$ProductOrderCartDetailID?>, this.value)" value="<?=$ProductCount?>" style="width:40px;height:20px;text-align:center;" class="allownumericwithoutdecimal">
										<span onclick="ChProductCount(<?=$ProductOrderCartDetailID?>, 1)" style="cursor:pointer;">▶</span>
									</td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductPriceSum,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:DeleteProductOrderCartDetail(<?=$ProductOrderCartDetailID?>);"><?=$삭제[$LangID]?></a></td>
								</tr>
								<?php
									$ListCount ++;
								}
								$Stmt = null;
								?>

								<tr>
									<td class="uk-text-nowrap uk-table-td-center" style="background-color:#f1f1f1;" colspan="4"><?=$합계[$LangID]?></td>
									<td class="uk-text-nowrap uk-table-td-center" style="background-color:#f1f1f1;" colspan="2"><?=number_format($ProductPriceTotSum,0)?></td>
								</tr>
							
								</tbody>
							</table>

							<div class="uk-form-row" style="text-align:center;">
								<a type="button" href="javascript:OpenProductOrderCartDetailForm(<?=$ProductOrderCartID?>)" class="md-btn md-btn-primary"><?=$교재등록[$LangID]?></a>
							</div>

					</div>
					</form>
					<?}?>


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

function ChSetProductCount(ProductOrderCartDetailID, SetValue){

	if (isNaN(SetValue)){
		SetValue = 1;
	}

	if (SetValue<1){
		SetValue = 1;
	}

	url = "ajax_set_product_order_cart_product_count.php";

	//location.href = url + "?ProductOrderCartDetailID="+ProductOrderCartDetailID;
	$.ajax(url, {
		data: {
			ProductOrderCartDetailID: ProductOrderCartDetailID,
			SetValue: SetValue
		},
		success: function (data) {
			json_data = data;
			location.reload();
		},
		error: function () {

		}
	});

}
function ChProductCount(ProductOrderCartDetailID, PlusValue){
	ProductOrderCount_ = document.getElementById("ProductOrderCount_"+ProductOrderCartDetailID).value;
	if (isNaN(ProductOrderCount_)){
		ProductOrderCount_ = 1;
	}
	ProductOrderCount_ = parseInt(ProductOrderCount_);

	if (ProductOrderCount_<1){
		ProductOrderCount_ = 1;
	}
	
	document.getElementById("ProductOrderCount_"+ProductOrderCartDetailID).value = ProductOrderCount_;

	SetValue = ProductOrderCount_ + PlusValue;

	ChSetProductCount(ProductOrderCartDetailID, SetValue);

}

function FormSubmit(){

	obj = document.RegForm.ProductOrderCartName;
	if (obj.value==""){
		UIkit.modal.alert("교재 구매 바구니명을 입력하세요.");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "product_order_cart_action.php";
			document.RegForm.submit();
		}
	);

}


function OpenProductOrderCartDetailForm(ProductOrderCartID){
	openurl = "product_order_cart_product_list.php?ProductOrderCartID="+ProductOrderCartID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1000"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){location.reload();}
		//,onComplete:function(){alert(1);}
	}); 
}

function DeleteProductOrderCartDetail(ProductOrderCartDetailID){

	UIkit.modal.confirm(
		'삭제 하시겠습니까?', 
		function(){ 
			url = "ajax_set_product_order_cart_del.php";

			//location.href = url + "?ProductOrderCartDetailID="+ProductOrderCartDetailID;
			$.ajax(url, {
				data: {
					ProductOrderCartDetailID: ProductOrderCartDetailID
				},
				success: function (data) {
					json_data = data;
					location.reload();
				},
				error: function () {

				}
			});
		}
	);		

}

</script>




<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>