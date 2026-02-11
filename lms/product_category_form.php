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
$ProductCategoryID = isset($_REQUEST["ProductCategoryID"]) ? $_REQUEST["ProductCategoryID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : ""; 


if ($ProductCategoryID!=""){
	$Sql = "
			select 
				* 
			from ProductCategories where ProductCategoryID=:ProductCategoryID"; // limit $StartRowNum, $PageListNum";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductCategoryID', $ProductCategoryID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	$Row = $Stmt->fetch();

	$ProductSellerID = $Row['ProductSellerID'];
	$ProductCategoryName = $Row['ProductCategoryName'];
	$ProductCategoryMemo = $Row['ProductCategoryMemo'];
	$ProductCategoryState = $Row['ProductCategoryState'];
	$ProductCategoryView = $Row['ProductCategoryView'];
}else{
	$ProductSellerID = 0;
	$ProductCategoryName = "";
	$ProductCategoryMemo = "";
	$ProductCategoryState = 1;
	$ProductCategoryView = 1;

	$Sql = "select 
			A.ProductSellerID 
		from ProductSellers A 
		where 
			A.ProductSellerState=1 and A.ProductSellerID<>2
		order by A.ProductSellerOrder asc limit 0, 1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$ProductSellerID = $Row["ProductSellerID"];
}
?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="ProductCategoryID" value="<?=$ProductCategoryID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$ProductCategoryName?></span><span class="sub-heading" id="user_edit_position"><?=$교재그룹_설정[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">	
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:none;">
							<li class="uk-active"><a href="#">Basic</a></li>
							<li><a href="#">Groups</a></li>
							<li><a href="#">Todo</a></li>
						</ul>
						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c">
										<?=$교재그룹_관리[$LangID]?>
									</h3>
									
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-10-10">
											<?if ($ProductSellerID==2){?>
												<input type="hidden" name="ProductSellerID" value="<?=$ProductSellerID?>">
												<div style="margin-bottom:20px;"><?=$올북스_배송상품[$LangID]?></div>

											<?}else{?>
												<?
												$Sql2 = "select 
																A.* 
														from ProductSellers A 
														where 
															(A.ProductSellerState=1 or A.ProductSellerID=$ProductSellerID)
															and A.ProductSellerID<>2
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
												?>
											<?}?>
										</div>
									</div>

									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-10-10">
											<label for="ProductCategoryName"><?=$교재그룹명[$LangID]?></label>
											<input type="text" id="ProductCategoryName" name="ProductCategoryName" value="<?=$ProductCategoryName?>" class="md-input label-fixed"/>
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<label for="ProductCategoryMemo"><?=$설명[$LangID]?></label>
											<textarea class="md-input" name="ProductCategoryMemo" id="ProductCategoryMemo" cols="30" rows="4"><?=$ProductCategoryMemo?></textarea>
										</div>
									</div>
								</div>

							</li>

						</ul>
					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10">
				<div class="md-card">
					<div class="md-card-content">
						<h3 class="heading_c uk-margin-medium-bottom"><?=$상태설정[$LangID]?></h3>
						
						<div class="uk-form-row" style="display:none;">
							<input type="checkbox" id="ProductCategoryView" name="ProductCategoryView" value="1" <?php if ($ProductCategoryView==1) { echo "checked";}?> data-switchery/>
							<label for="ProductCategoryView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">
						
						
						<div class="uk-form-row">
							<input type="checkbox" id="ProductCategoryState" name="ProductCategoryState" value="1" <?php if ($ProductCategoryState==1) { echo "checked";}?> data-switchery/>
							<label for="ProductCategoryState" class="inline-label"><?=$사용중[$LangID]?></label>
						</div>
						<hr class="md-hr">
						<div class="uk-form-row">
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?=$저장하기[$LangID]?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		</form>
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

function FormSubmit(){

	obj = document.RegForm.ProductCategoryName;
	if (obj.value==""){
		UIkit.modal.alert("항목을 입력하세요.");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "product_category_action.php";
			document.RegForm.submit();
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