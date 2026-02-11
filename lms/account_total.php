<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

if ($_LINK_ADMIN_LEVEL_ID_>7){
	header("Location: branch_form.php?BranchID=".$_LINK_ADMIN_BRANCH_ID_); 
	exit;
}
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
$MainMenuID = 21;
$SubMenuID = 2102;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>

 

<?php

$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";

$CellID = isset($_REQUEST["CellID"]) ? $_REQUEST["CellID"] : "";
$CellOrder = isset($_REQUEST["CellOrder"]) ? $_REQUEST["CellOrder"] : "";
$OldCellID = isset($_REQUEST["OldCellID"]) ? $_REQUEST["OldCellID"] : "";
$OldCellOrder = isset($_REQUEST["OldCellOrder"]) ? $_REQUEST["OldCellOrder"] : "";


if ($CellID==""){
	$CellID = "5";
	$OldCellID = "5";
}

if ($CellOrder==""){
	$CellOrder = "1";
}

if ($SearchYear==""){
	$SearchYear = date("Y");
}


$ViewTable = "
		select 
			date_format(A.ClassOrderPayPaymentDateTime, '%m') as ClassMonth,
			
			sum(A.ClassOrderPayUseCashPrice) as SumClassOrderPayUseCashPrice,
			
			sum(A.ClassOrderPayUseCashPrice*(A.ClassOrderPayPgFeeRatio/100)) as SumPgFeeRatio,
			
			sum(A.ClassOrderPayPgFeePrice) as SumPgFeePrice,

			sum((A.ClassOrderPayUseCashPrice-A.ClassOrderPayPgFeePrice-(A.ClassOrderPayUseCashPrice * ClassOrderPayPgFeeRatio / 100)) * ( (A.CenterPricePerTime - A.CompanyPricePerTime) / A.CenterPricePerTime )) * 0.967  as SumBranchFee,
			
			sum(
				A.ClassOrderPayUseCashPrice 
				- (A.ClassOrderPayUseCashPrice*(A.ClassOrderPayPgFeeRatio/100)) 
				- A.ClassOrderPayPgFeePrice 
				- (
					(	A.ClassOrderPayUseCashPrice
						-(A.ClassOrderPayUseCashPrice * A.ClassOrderPayPgFeeRatio / 100)
						-A.ClassOrderPayPgFeePrice
					) 
					* 
					( 
						(A.CenterPricePerTime - A.CompanyPricePerTime) / A.CenterPricePerTime 
					)
					* 0.967 
				  ) 
			) as RealSumClassOrderPayUseCashPrice


		from ClassOrderPays A
		
			inner join Members B on A.ClassOrderPayPaymentMemberID=B.MemberID 
			inner join Centers C on A.CenterID=C.CenterID 
			inner join Branches D on C.BranchID=D.BranchID
			inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
			inner join Companies F on E.CompanyID=F.CompanyID 
			inner join Franchises G on F.FranchiseID=G.FranchiseID 

		where 
			date_format(A.ClassOrderPayPaymentDateTime, '%Y')='".$SearchYear."' 
			and (A.ClassOrderPayProgress=21 or A.ClassOrderPayProgress=31 or A.ClassOrderPayProgress=41)
		group by date_format(A.ClassOrderPayPaymentDateTime, '%m') 
		order by date_format(A.ClassOrderPayPaymentDateTime, '%m') asc 
";

$AddSqlWhere3 = ($CellOrder==1)? "desc":"asc";
if ($CellID=="1"){
	$Sql = "select * from ($ViewTable) V order by V.SumClassOrderPayUseCashPrice ".$AddSqlWhere3;
} else if($CellID=="2"){
	$Sql = "select * from ($ViewTable) V order by V.SumBranchFee ".$AddSqlWhere3;
} else if($CellID=="3"){
	$Sql = "select * from ($ViewTable) V order by V.SumPgFeeRatio ".$AddSqlWhere3;
} else if($CellID=="4"){
	$Sql = "select * from ($ViewTable) V order by V.SumPgFeePrice ".$AddSqlWhere3;
} else if($CellID=="5"){
	$Sql = "select * from ($ViewTable) V order by V.RealSumClassOrderPayUseCashPrice ".$AddSqlWhere3;
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$본사정산[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<input type="hidden" name="CellID" id="CellID" value="<?=$CellID?>"/>
		<input type="hidden" name="CellOrder" id="CellOrder" value="<?=$CellOrder?>"/>
		<input type="hidden" name="OldCellID" id="OldCellID" value="<?=$OldCellID?>"/>
		<input type="hidden" name="OldCellOrder" id="OldCellOrder" value="<?=$OldCellOrder?>"/>

		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					


					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
						<select id="SearchYear" name="SearchYear" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%;"/>
							<option value=""><?=$년도선택[$LangID]?></option>
							<?
							for ($iiii=2018;$iiii<=date("Y");$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchYear==$iiii){?>selected<?}?>><?=$iiii?><?=$년[$LangID]?></option>
							<?
							}
							?>
						</select>
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
							<div style="text-align:right;">※ 커미션은 결제(PG)수수료와 3.3% 세금을 제외한 금액입니다.</div>
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap><?=$월월[$LangID]?></th>
										<th nowrap><a href="javascript:SetOrderList(1);" ><?=$실결제액[$LangID]?> <?if ($CellOrder=="1" && $CellID=="1"){?>▼<?} else if($CellOrder=="2" && $CellID=="1") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(3);" ><?=$카드수수료_퍼센트[$LangID]?> <?if ($CellOrder=="1" && $CellID=="3"){?>▼<?} else if($CellOrder=="2" && $CellID=="3") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(4);" ><?=$PG수수료_정액[$LangID]?><?if ($CellOrder=="1" && $CellID=="4"){?>▼<?} else if($CellOrder=="2" && $CellID=="4") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(2);" ><?=$커미션_3점3_제외[$LangID]?> <?if ($CellOrder=="1" && $CellID=="2"){?>▼<?} else if($CellOrder=="2" && $CellID=="2") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(5);" ><?=$실매출액[$LangID]?> <?if ($CellOrder=="1" && $CellID=="5"){?>▼<?} else if($CellOrder=="2" && $CellID=="5") {?>▲<?}?></a></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									$SumSumClassOrderPayUseCashPrice = 0;
									$SumSumBranchFee = 0;
									$SumSumPgFeeRatio = 0;
									$SumSumPgFeePrice = 0;
									$SumRealSumClassOrderPayUseCashPrice = 0;
									while($Row = $Stmt->fetch()) {


										$ClassMonth = $Row["ClassMonth"];
										$SumClassOrderPayUseCashPrice = $Row["SumClassOrderPayUseCashPrice"];
										$SumBranchFee = $Row["SumBranchFee"];
										
										$SumPgFeeRatio = $Row["SumPgFeeRatio"];
										$SumPgFeePrice = $Row["SumPgFeePrice"];
										$RealSumClassOrderPayUseCashPrice = $Row["RealSumClassOrderPayUseCashPrice"];


										$SumSumClassOrderPayUseCashPrice = $SumSumClassOrderPayUseCashPrice + $SumClassOrderPayUseCashPrice;
										$SumSumBranchFee = $SumSumBranchFee + $SumBranchFee;
										$SumSumPgFeeRatio = $SumSumPgFeeRatio + $SumPgFeeRatio;
										$SumSumPgFeePrice = $SumSumPgFeePrice + $SumPgFeePrice;
										$SumRealSumClassOrderPayUseCashPrice = $SumRealSumClassOrderPayUseCashPrice + $RealSumClassOrderPayUseCashPrice;

									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassMonth?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumClassOrderPayUseCashPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumPgFeeRatio,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumPgFeePrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumBranchFee,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($RealSumClassOrderPayUseCashPrice,0)?></td>
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;

									if ($ListCount==1){
										$AvgSumClassOrderPayUseCashPrice = 0;
										$AvgSumBranchFee = 0;
										$AvgSumPgFeeRatio = 0;
										$AvgSumPgFeePrice = 0;
										$AvgRealSumClassOrderPayUseCashPrice = 0;
									}else{
										$AvgSumClassOrderPayUseCashPrice = $SumSumClassOrderPayUseCashPrice / ($ListCount-1);
										$AvgSumBranchFee = $SumSumBranchFee / ($ListCount-1);
										$AvgSumPgFeeRatio = $SumSumPgFeeRatio / ($ListCount-1);
										$AvgSumPgFeePrice = $SumSumPgFeePrice / ($ListCount-1);
										$AvgRealSumClassOrderPayUseCashPrice = $SumRealSumClassOrderPayUseCashPrice / ($ListCount-1);
									}
									?>

									<tr style="background-color:#f1f1f1;">
										<td class="uk-text-nowrap uk-table-td-center"><?=$합계[$LangID]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumSumClassOrderPayUseCashPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumSumPgFeeRatio,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumSumPgFeePrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumSumBranchFee,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumRealSumClassOrderPayUseCashPrice,0)?></td>
									</tr>
									<tr style="background-color:#f1f1f1;">
										<td class="uk-text-nowrap uk-table-td-center"><?=$평균[$LangID]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgSumClassOrderPayUseCashPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgSumPgFeeRatio,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgSumPgFeePrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgSumBranchFee,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgRealSumClassOrderPayUseCashPrice,0)?></td>
									</tr>

								</tbody>
							</table>
						</div>
						

						<?php			
						//include_once('./inc_pagination.php');
						?>
						<!--
						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="branch_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary">신규등록</a>
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
function SetOrderList(ID) {
	// .value 를 붙이면 단순 문자열 또는 숫자로 인식.
	var CellID = document.SearchForm.CellID;
	var CellOrder = document.SearchForm.CellOrder;
	var OldCellID = document.SearchForm.OldCellID;
	var OldCellOrder = document.SearchForm.OldCellOrder;

	// 클릭했었던 값은 Old 에 대입
	OldCellOrder.value = CellOrder.value;
	OldCellID.value = CellID.value;
	CellID.value = ID;

	//alert("CellID : "+CellID.value);
	//alert("CellOrder : "+CellOrder.value);
	//alert("OldCellID : "+OldCellID.value);
	//alert("OldCellOrder : "+OldCellOrder.value);
	//alert(document.SearchForm.OldCellOrder.value);

	// 동일한 CellID 를 눌렀다면 
	if (CellID.value==OldCellID.value) {
		// 기존값이 1,2 인지 확인 후 2 또는 1 대입
		CellOrder.value = (OldCellOrder.value==1)? 2:1;
		//alert("after if : "+CellOrder.value);
	} else { // 기존 Cell 과 누른 Cell 이 같지 않다면
		CellOrder.value = 1;
		//alert("after if : "+CellOrder.value);
	}




	SearchSubmit();
}

function SearchSubmit(){
	document.SearchForm.action = "account_total.php";
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