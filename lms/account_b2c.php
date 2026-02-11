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
$MainMenuID = 21;
$SubMenuID = 2103;
include_once('./inc_top.php'); 
include_once('./inc_menu_left.php');
#===== 모바일 결제창에서 결제하지 않고 다시 돌아올경우 셀프페이에 남겨진 고유코드를 다시 재사용하기위한 변수 입니다. =====#
$ReqUrl = isset($_REQUEST["ReqUrl"]) ? $_REQUEST["ReqUrl"] : ""; //' 결제창에서 결제실행전 돌아올때
?>



<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";


$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

$CellID = isset($_REQUEST["CellID"]) ? $_REQUEST["CellID"] : "";
$CellOrder = isset($_REQUEST["CellOrder"]) ? $_REQUEST["CellOrder"] : "";
$OldCellID = isset($_REQUEST["OldCellID"]) ? $_REQUEST["OldCellID"] : "";
$OldCellOrder = isset($_REQUEST["OldCellOrder"]) ? $_REQUEST["OldCellOrder"] : "";


if ($CellID==""){
	$CellID = "1";
	$OldCellID = "1";
}

if ($CellOrder==""){
	$CellOrder = "1";
}

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
		

$AddSqlWhere = $AddSqlWhere . " and (A.ClassOrderPayProgress=21 or A.ClassOrderPayProgress=31 or A.ClassOrderPayProgress=41) ";
$AddSqlWhere = $AddSqlWhere . " and C.CenterID=1 ";//비투씨학원
//$AddSqlWhere = $AddSqlWhere . " and B.MemberLevelID=19 ";//비투씨학원

if ($SearchYear!=""){
	$ListParam = $ListParam . "&SearchYear=" . $SearchYear;
	$AddSqlWhere = $AddSqlWhere . " and date_format(A.ClassOrderPayPaymentDateTime, '%Y')='".$SearchYear."' ";
}

if ($SearchMonth!=""){
	$ListParam = $ListParam . "&SearchMonth=" . $SearchMonth;
	$AddSqlWhere = $AddSqlWhere . " and date_format(A.ClassOrderPayPaymentDateTime, '%m')='".substr("0".$SearchMonth,-2)."' ";
}



$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from ClassOrderPays A 
			inner join Members B on A.ClassOrderPayPaymentMemberID=B.MemberID 
			inner join Centers C on A.CenterID=C.CenterID 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$ViewTable = "
		select 
			A.*,
			B.MemberName,
			C.CenterName
		from ClassOrderPays A 
			inner join Members B on A.ClassOrderPayPaymentMemberID=B.MemberID 
			inner join Centers C on A.CenterID=C.CenterID 
		where ".$AddSqlWhere." 
		";//order by A.MemberRegDateTime desc limit $StartRowNum, $PageListNum";

$AddSqlWhere3 = ($CellOrder==1)? "desc":"asc";
if ($CellID=="1"){
	$Sql = "select * from ($ViewTable) V order by V.ClassOrderPayPaymentPrice ".$AddSqlWhere3;
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$B2C_결제[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<input type="hidden" name="CellID" id="CellID" value="<?=$CellID?>"/>
		<input type="hidden" name="CellOrder" id="CellOrder" value="<?=$CellOrder?>"/>
		<input type="hidden" name="OldCellID" id="OldCellID" value="<?=$OldCellID?>"/>
		<input type="hidden" name="OldCellOrder" id="OldCellOrder" value="<?=$OldCellOrder?>"/>

		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">

					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
						<select id="SearchYear" name="SearchYear" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="년도선택" style="width:100%;"/>
							<option value="">년도선택</option>
							<?
							for ($iiii=2018;$iiii<=2020;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
						<select id="SearchMonth" name="SearchMonth" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;"/>
							<option value=""><?=$월선택[$LangID]?></option>
							<?
							for ($iiii=1;$iiii<=12;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
							<?
							}
							?>
						</select>
					</div>


					<!--						
					<div class="uk-width-medium-2-10">
						<label for="SearchText">학생명 또는 아이디</label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>


					<div class="uk-width-medium-1-10">
						<label for="product_search_price">Price</label>
						<input type="text" class="md-input" id="product_search_price">
					</div>


					<div class="uk-width-medium-1-10">
						<div class="uk-margin-small-top">
							<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>>전체</option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>>정상</option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>>휴면</option>
								<option value="3" <?if ($SearchState=="3"){?>selected<?}?>>탈퇴</option>
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
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap><?=$학생명[$LangID]?></th>
										<th nowrap><?=$주문일시[$LangID]?></th>
										<th nowrap><?=$결제일시[$LangID]?></th>
										<th nowrap><a href="javascript:SetOrderList(1);" ><?=$결제금액[$LangID]?> <?if ($CellOrder=="1" && $CellID=="1"){?>▼<?} else if($CellOrder=="2" && $CellID=="1") {?>▲<?}?></a></th>
										<th nowrap><?=$상태[$LangID]?></th>
										<th nowrap><?=$영수증[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									$SumClassOrderPayPaymentPrice = 0;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$ClassOrderPayDateTime = $Row["ClassOrderPayDateTime"];
										$ClassOrderPayPaymentDateTime = $Row["ClassOrderPayPaymentDateTime"];
										$ClassOrderPayPaymentPrice = $Row["ClassOrderPayPaymentPrice"];
										$ClassOrderPayProgress = $Row["ClassOrderPayProgress"];
										$tno = $Row["tno"];
										$ClassOrderPayNumber = $Row["ClassOrderPayNumber"];
										$use_pay_method = $Row["use_pay_method"];

										if ($ClassOrderPayProgress==11){
											$StrClassOrderPayProgress = "주문완료";
										}else if ($ClassOrderPayProgress==21){
											$StrClassOrderPayProgress = "결제완료";
										}else if ($ClassOrderPayProgress==31){
											$StrClassOrderPayProgress = "취소요청";
										}else if ($ClassOrderPayProgress==33){
											$StrClassOrderPayProgress = "취소완료";
										}else if ($ClassOrderPayProgress==41){
											$StrClassOrderPayProgress = "환불요청";
										}else if ($ClassOrderPayProgress==43){
											$StrClassOrderPayProgress = "환불완료";
										}
										
										
										$MemberName = $Row["MemberName"];
										$CenterName = $Row["CenterName"];

										$SumClassOrderPayPaymentPrice = $SumClassOrderPayPaymentPrice + $ClassOrderPayPaymentPrice;
										/*
										if ($MemberState==1){
											$StrCenterState = "<span class=\"ListState_1\">정상</span>";
										}else if ($MemberState==2){
											$StrCenterState = "<span class=\"ListState_2\">휴면</span>";
										}else if ($MemberState==3){
											$StrCenterState = "<span class=\"ListState_3\">탈퇴</span>";
										}
										*/
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderPayDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderPayPaymentDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ClassOrderPayPaymentPrice,0)?> 원</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrClassOrderPayProgress?></td>
										<td class="uk-text-nowrap uk-table-td-center">
										
												<?if ($ClassOrderPayProgress==21){?>
													<?if ($use_pay_method=="100000000000"){//신용카드?>
													<a href="javascript:OpenInvoiceCard('<?=$tno?>', '<?=$ClassOrderPayNumber?>', '<?=$ClassOrderPayPaymentPrice?>')" class="button_br_gray"><?=$신용카드_영수증[$LangID]?></a>
													<?}else if ($use_pay_method=="010000000000"){//계좌이체?>
													<a href="javascript:OpenInvoiceEtc('<?=$tno?>', '<?=$ClassOrderPayNumber?>', '<?=$ClassOrderPayPaymentPrice?>')" class="button_br_gray"><?=$신용카드_영수증[$LangID]?></a>
													<?}else if ($use_pay_method=="001000000000"){//가상계좌?>
													<a href="javascript:OpenInvoiceEtc('<?=$tno?>', '<?=$ClassOrderPayNumber?>', '<?=$ClassOrderPayPaymentPrice?>')" class="button_br_gray"><?=$신용카드_영수증[$LangID]?></a>
													<?}?>
												
												<?}else{?>
													-
												<?}?>				
										</td>
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;

									if ($ListCount==1){
										$AvgClassOrderPayPaymentPrice = 0;
									}else{
										$AvgClassOrderPayPaymentPrice = $SumClassOrderPayPaymentPrice / ($ListCount-1);
									}
									?>
									<tr style="background-color:#f1f1f1;">
										<td class="uk-text-nowrap uk-table-td-center" colspan="4"><?=$합계[$LangID]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumClassOrderPayPaymentPrice,0)?> 원</td>
										<td class="uk-text-nowrap uk-table-td-center" colspan="2"></td>
									</tr>
									<tr style="background-color:#f1f1f1;">
										<td class="uk-text-nowrap uk-table-td-center" colspan="4"><?=$평균[$LangID]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgClassOrderPayPaymentPrice,0)?> 원</td>
										<td class="uk-text-nowrap uk-table-td-center" colspan="2"></td>
									</tr>

								</tbody>
							</table>
						</div>
						

						<?php			
						//include_once('./inc_pagination.php');
						?>

						<!--
						<div class="uk-form-row" style="text-align:center;">
`							<a type="button" href="student_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary">신규등록</a>
						</div>
						-->

					</div>
				</div>
			</div>
		</div>

	</div>
</div>


<script>
function OpenInvoiceCard(tno, order_no, trade_mony){
	
	openurl = "http://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=card_bill&tno="+tno+"&order_no="+order_no+"&trade_mony="+trade_mony;
	window.open(openurl,'OpenInvoiceCard','width=470,height=815,toolbar=no,top=100,left=100');
	
}
function OpenInvoiceEtc(tno, order_no, trade_mony){
	
	openurl = "https://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=vcnt_bill&tno="+tno+"&&order_no="+order_no+"&trade_mony="+trade_mony;
	window.open(openurl,'OpenInvoiceCard','width=470,height=815,toolbar=no,top=100,left=100');
	
}
</script>

<?
include_once('./inc_common_list_js.php');
?>
<!-- ============== only this page js ============== -->
<script>
function OpenClassOrderForm1(MemberID,ReqUrl){
	openurl = "class_order_form.php?MemberID="+MemberID+"&ReqUrl="+ReqUrl;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenLeveltestApplyForm(MemberID){
	openurl = "leveltest_apply_form.php?MemberID="+MemberID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function OpenMemberPointForm(MemberID){
	openurl = "member_point_form.php?MemberID="+MemberID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function OpenCounselForm(MemberID){
	openurl = "counsel_form.php?MemberID="+MemberID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}
</script>

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
	document.SearchForm.action = "account_b2c.php";
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