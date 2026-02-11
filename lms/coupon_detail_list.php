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
$SubMenuID = 1123;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>


 
<?php

$AddSqlWhere = "1=1";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$CouponID = isset($_REQUEST["CouponID"]) ? $_REQUEST["CouponID"] : "";


$Sql = "select 
				A.CouponName
		from Coupons A 
		where CouponID=:CouponID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':CouponID', $CouponID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$CouponName = $Row["CouponName"];



$AddSqlWhere .= " and A.CouponID=$CouponID ";

$Sql = "select 
				count(*) TotalRowCount 
		from CouponDetails A 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalRowCount = $Row["TotalRowCount"];



$Sql = "
		select 
			A.*,
			ifnull(B.MemberName,'-') as CouponDetailUseMemberName 
		from CouponDetails A 
			left outer join Members B on A.CouponDetailUseMemberID=B.MemberID 
		where ".$AddSqlWhere." 
		order by A.CouponDetailID desc";// limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><b><?=$CouponName?></b><?=$쿠폰번호_목록[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">

					<!--
					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$쿠폰명[$LangID]?></label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>

					
					<div class="uk-width-medium-1-10">
						<label for="product_search_price">Price</label>
						<input type="text" class="md-input" id="product_search_price">
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
					-->

					<!--
					<div class="uk-width-medium-1-10">
						<div class="uk-margin-top uk-text-nowrap">
							<input type="checkbox" name="product_search_active" id="product_search_active" data-md-icheck/>
							<label for="product_search_active" class="inline-label">Active</label>
						</div>
					</div>
					

					<div class="uk-width-medium-1-10 uk-text-center">
						<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
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
										<th nowrap><?=$쿠폰번호[$LangID]?></th>
										<th nowrap><?=$쿠폰사용자[$LangID]?></th>
										<th nowrap><?=$쿠폰사용일[$LangID]?></th>
										<th nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $ListCount + 1;

										$CouponID = $Row["CouponID"];
										$CouponDetailID = $Row["CouponDetailID"];
										$CouponDetailUseMemberID = $Row["CouponDetailUseMemberID"];
										$CouponDetailUseDateTime = $Row["CouponDetailUseDateTime"];
										$CouponDetailNumber = $Row["CouponDetailNumber"];
										$CouponDetailState = $Row["CouponDetailState"];

										$CouponDetailUseMemberName = $Row["CouponDetailUseMemberName"];

										if ($CouponDetailState==1){
											$StrCouponDetailState = "<span class=\"ListState_1\">사용전</span>";
										}else if ($CouponDetailState==2){
											$StrCouponDetailState = "<span class=\"ListState_2\">사용완료</span>";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenCouponDetailForm('<?=$CouponID?>','<?=$CouponDetailID?>')"><?=$CouponDetailNumber?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CouponDetailUseMemberName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CouponDetailUseDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrCouponDetailState?></td>
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

						<div class="uk-form-row" style="text-align:center;margin-top:30px;">
							<a type="button" href="coupon_list.php?ListParam=<?=str_replace("^^","&",$ListParam)?>" class="md-btn md-btn-gray"><?=$쿠폰목록[$LangID]?></a> 
							<a type="button" href="javascript:OpenCouponDetailForm('<?=$CouponID?>','')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
function OpenCouponDetailForm(CouponID, CouponDetailID){
	openurl = "coupon_detail_form.php?CouponID="+CouponID+"&CouponDetailID="+CouponDetailID;
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
	document.SearchForm.action = "coupon_detail_list.php";
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