<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>세금계산서</title>
<style>
*{margin:0; padding:0; box-sizing:border-box;}
#mainWrap{width:980px;}
#mainWrap #contents #TaxFormWrap #tax_item_wrap {width: 100%; padding-top: 10px; }
#mainWrap #contents #TaxFormWrap #tax_bottom_wrap {width: 100%; padding-top: 30px; }
#mainWrap #contents #TaxFormWrap #tax_button_wrap {width: 100%; padding-top: 30px; text-align: center; }
.tax_button{display:inline-block; width:200px; height:50px; line-height:50px; background-color:#444; color:#fff; font-size:18px; text-align:center; text-decoration:none;}
#mainWrap #contents #TaxFormWrap table { border-left: 1px solid #ccc; border-top: 1px solid #ccc; }
#mainWrap #contents #TaxFormWrap table.no_top_border { border-top: 0; }
#mainWrap #contents #TaxFormWrap table td {height: 30px; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc; text-align: center; padding:0 5px; font-size:14px;}
#mainWrap #contents #TaxFormWrap table td input { height:20px; width:100%; padding: 2px 3px; border: 1px solid #ccc; }
#mainWrap #contents #TaxFormWrap table td input.money { text-align: right; }
#mainWrap #contents #TaxFormWrap table .tx_bg { background: #f2f2f2; }
#mainWrap #contents #TaxFormWrap table .tx_subject { width: 540px; font-weight: bold; }
#mainWrap #contents #TaxFormWrap table .tx_no_label { width: 86px; }
#mainWrap #contents #TaxFormWrap table .tx_no_field { width: 160px; }
#mainWrap #contents #TaxFormWrap table .tx_info_vertical { width: 20px; line-height: 180%; font-weight: bold; }
#mainWrap #contents #TaxFormWrap table .tx_info_label1 { width: 60px; height: 50px; }
#mainWrap #contents #TaxFormWrap table .tx_info_label2 { width: 60px; }
#mainWrap #contents #TaxFormWrap table .tx_info_field { width: 125px; }
#mainWrap #contents #TaxFormWrap table .tx_sum_item1 { width: 196px; }
#mainWrap #contents #TaxFormWrap table .tx_sum_item2 { width: 197px; }
#mainWrap #contents #TaxFormWrap table .tx_item_label1 { width: 86px; height: 40px; }
#mainWrap #contents #TaxFormWrap table .tx_item_label2 { width: 89px; }
#mainWrap #contents #TaxFormWrap table .tx_btm_label1 { width: 126px; }
#mainWrap #contents #TaxFormWrap table .tx_btm_label2 { width: 153px; }
table{width:100%;}
</style>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<!-- ====    kendo   === -->
<link href="../kendo/styles/kendo.common.min.css" rel="stylesheet">
<link href="../kendo/styles/kendo.default.min.css" rel="stylesheet">
<link href="../kendo/styles/kendo.default.mobile.min.css" rel="stylesheet">
<script src="../kendo/js/kendo.web.min.js"></script>
<!-- ====    kendo   === -->
</head>

<body>

<?
$TaxInvoiceType = isset($_REQUEST["TaxInvoiceType"]) ? $_REQUEST["TaxInvoiceType"] : "";
$TaxInvoicePayID = isset($_REQUEST["TaxInvoicePayID"]) ? $_REQUEST["TaxInvoicePayID"] : "";
$TaxInvoiceID = isset($_REQUEST["TaxInvoiceID"]) ? $_REQUEST["TaxInvoiceID"] : "";


$ErrNum=0;
$ErrMsg="";

$Sql = "
		select 
			A.*
		from TaxMemberInfos A
		where A.OrganType=9 and A.OrganID=1
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$Com_CorpName= $Row["CorpName"];
$Com_CorpNum = $Row["CorpNum"];
$Com_TaxRegID = $Row["TaxRegID"];
$Com_CEOName = $Row["CEOName"];
$Com_TEL1 = $Row["TEL1"];
$Com_HP1 = $Row["HP1"];
$Com_Addr = $Row["Addr"];
$Com_BizType = $Row["BizType"];
$Com_BizClass = $Row["BizClass"];
$Com_ContactName1 = $Row["ContactName1"];
$Com_Email1 = $Row["Email1"];
$Com_ContactName2 = $Row["ContactName2"];
$Com_Email2 = $Row["Email2"];



if ($TaxInvoiceType=="1"){//lms b2b 결제


	$Sql = "
			select 
				A.*
			from ClassOrderPays A
			where A.ClassOrderPayID=:ClassOrderPayID 
			";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderPayID', $TaxInvoicePayID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$CenterID = $Row["CenterID"];
	$PayGoods = $Row["PayGoods"];
	$ClassOrderPayUseCashPrice = $Row["ClassOrderPayUseCashPrice"];

	$OrganType = 1;
	$OrganID = $CenterID;


	if ($TaxInvoiceID!=""){//재발행

		$Sql = "
				select 
					A.*
				from TaxInvoices A
				where A.TaxInvoiceID=:TaxInvoiceID 
				";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':TaxInvoiceID', $TaxInvoiceID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$TaxInvoiceRegType = $Row["TaxInvoiceRegType"];
		$TaxInvoiceType = $Row["TaxInvoiceType"];
		$TaxInvoicePayID = $Row["TaxInvoicePayID"];
		
		$memo = $Row["memo"];
		$emailSubject = $Row["emailSubject"];
		$writeDate = $Row["writeDate"];
		$issueType = $Row["issueType"];
		$chargeDirection = $Row["chargeDirection"];
		$purposeType = $Row["purposeType"];
		$taxType = $Row["taxType"];
		$issueTiming = $Row["issueTiming"];

		$invoicerMgtKey = $Row["invoicerMgtKey"];
		$invoicerCorpName = $Row["invoicerCorpName"];
		$invoicerCorpNum = $Row["invoicerCorpNum"];
		$invoicerTaxRegID = $Row["invoicerTaxRegID"];
		$invoicerCEOName = $Row["invoicerCEOName"];
		$invoicerTEL = $Row["invoicerTEL"];
		$invoicerHP = $Row["invoicerHP"];
		$invoicerAddr = $Row["invoicerAddr"];
		$invoicerBizType = $Row["invoicerBizType"];
		$invoicerBizClass = $Row["invoicerBizClass"];
		$invoicerContactName1 = $Row["invoicerContactName1"];
		$invoicerEmail1 = $Row["invoicerEmail1"];
		$invoicerContactName2 = $Row["invoicerContactName2"];
		$invoicerEmail2 = $Row["invoicerEmail2"];

		$invoiceeType = $Row["invoiceeType"];
		$invoiceeMgtKey = $Row["invoiceeMgtKey"];
		$invoiceeCorpName = $Row["invoiceeCorpName"];
		$invoiceeCorpNum = $Row["invoiceeCorpNum"];
		$invoiceeTaxRegID = $Row["invoiceeTaxRegID"];
		$invoiceeCEOName = $Row["invoiceeCEOName"];
		$invoiceeTEL = $Row["invoiceeTEL"];
		$invoiceeHP = $Row["invoiceeHP"];
		$invoiceeAddr = $Row["invoiceeAddr"];
		$invoiceeBizType = $Row["invoiceeBizType"];
		$invoiceeBizClass = $Row["invoiceeBizClass"];
		$invoiceeContactName1 = $Row["invoiceeContactName1"];
		$invoiceeEmail1 = $Row["invoiceeEmail1"];
		$invoiceeContactName2 = $Row["invoiceeContactName2"];
		$invoiceeEmail2 = $Row["invoiceeEmail2"];

		$supplyCostTotal = $Row["supplyCostTotal"];
		$taxTotal = $Row["taxTotal"];
		$totalAmount = $Row["totalAmount"];
		$serialNum_ = $Row["serialNum"];
		$cash = $Row["cash"];
		$chkBill = $Row["chkBill"];
		$note = $Row["note"];
		$credit = $Row["credit"];
		$remark1 = $Row["remark1"];
		$remark2 = $Row["remark2"];
		$remark3 = $Row["remark3"];
		$kwon = $Row["kwon"];
		$ho = $Row["ho"];

		$code = $Row["code"];
		$message = $Row["message"];
		$ntsConfirmNum = $Row["ntsConfirmNum"];

		$writeDate = substr($writeDate, 0,4)."-".substr($writeDate, 4,2)."-".substr($writeDate, 6,2);



		for ($ii=1;$ii<=5;$ii++){

			$serialNum[$ii] = $ii;
			$purchaseDT[$ii] = "";
			$itemName[$ii] = "";
			$spec[$ii] = "";
			$qty[$ii] = "";
			$unitCost[$ii] = "";
			$supplyCost[$ii] = "";
			$tax[$ii] = "";
			$remark[$ii] = "";

		}


		$Sql2 = "select 
						A.* 
				from TaxInvoiceItems A 
				where A.TaxInvoiceID=:TaxInvoiceID 
				order by A.serialNum asc";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':TaxInvoiceID', $TaxInvoiceID);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		while($Row2 = $Stmt2->fetch()) {

			$ii = $Row2["serialNum"];

			$serialNum[$ii] = $ii;
			$purchaseDT[$ii] = $Row2["purchaseDT"];
			$itemName[$ii] = $Row2["itemName"];
			$spec[$ii] = $Row2["spec"];
			$qty[$ii] = $Row2["qty"];
			$unitCost[$ii] = $Row2["unitCost"];
			$supplyCost[$ii] = $Row2["supplyCost"];
			$tax[$ii] = $Row2["tax"];
			$remark[$ii] = $Row2["remark"];

			$purchaseDT[$ii] = substr($purchaseDT[$ii], 0,4)."-".substr($purchaseDT[$ii], 4,2)."-".substr($purchaseDT[$ii], 6,2);

		}
		$Stmt2 = null;



	}else{//신규발행


		$Sql = "
				select 
					A.*
				from TaxMemberInfos A
				where A.OrganType=1 and A.OrganID=:OrganID
				";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':OrganID', $CenterID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$Cus_TaxMemberInfoID= $Row["TaxMemberInfoID"];
		$Cus_CorpName= $Row["CorpName"];
		$Cus_CorpNum = $Row["CorpNum"];
		$Cus_TaxRegID = $Row["TaxRegID"];
		$Cus_CEOName = $Row["CEOName"];
		$Cus_TEL1 = $Row["TEL1"];
		$Cus_HP1 = $Row["HP1"];
		$Cus_Addr = $Row["Addr"];
		$Cus_BizType = $Row["BizType"];
		$Cus_BizClass = $Row["BizClass"];
		$Cus_ContactName1 = $Row["ContactName1"];
		$Cus_Email1 = $Row["Email1"];
		$Cus_ContactName2 = $Row["ContactName2"];
		$Cus_Email2 = $Row["Email2"];

		if (!$Cus_TaxMemberInfoID){
			$ErrNum=1;
			$ErrMsg="기업의 세금계산서정보가 등록되어있지 않습니다.";
		}


		//=============== 표시 데이터
		$TaxInvoiceRegType = 1;
		$TaxInvoiceType = 1;
		//$TaxInvoicePayID = 1;//위에서 정의되어 있음.

		$memo = "";
		$emailSubject = "";
		$writeDate = date("Y-m-d");
		$issueType = "정발행";
		$chargeDirection = "정과금";
		$purposeType = "영수";
		$taxType = "면세";
		$issueTiming = "직접발행";

		
		$invoicerMgtKey = date("YmdHis")."-".substr("000000".$CenterID, -6);//문서번호
		$invoicerCorpName = $Com_CorpName;
		$invoicerCorpNum = $Com_CorpNum;
		$invoicerTaxRegID = $Com_TaxRegID;
		$invoicerCEOName = $Com_CEOName;
		$invoicerTEL = $Com_TEL1;
		$invoicerHP = $Com_HP1;
		$invoicerAddr = $Com_Addr ;
		$invoicerBizType = $Com_BizType;
		$invoicerBizClass = $Com_BizClass;
		$invoicerContactName1 = $Com_ContactName1;
		$invoicerEmail1 = $Com_Email1;
		$invoicerContactName2 = $Com_ContactName2;
		$invoicerEmail2 = $Com_Email2;

		
		$invoiceeMgtKey = "";//역발행시 문서번호
		$invoiceeType = "사업자";
		$invoiceeCorpName = $Cus_CorpName;
		$invoiceeCorpNum = $Cus_CorpNum;
		$invoiceeTaxRegID = $Cus_TaxRegID;
		$invoiceeCEOName = $Cus_CEOName;
		$invoiceeTEL = $Cus_TEL1;
		$invoiceeHP = $Cus_HP1;
		$invoiceeAddr = $Cus_Addr;
		$invoiceeBizType = $Cus_BizType;
		$invoiceeBizClass = $Cus_BizClass;
		$invoiceeContactName1 = $Cus_ContactName1;
		$invoiceeEmail1 = $Cus_Email1;
		$invoiceeContactName2 = $Cus_ContactName2;
		$invoiceeEmail2 = $Cus_Email2;

		$supplyCostTotal = $ClassOrderPayUseCashPrice;
		$taxTotal = 0;
		$totalAmount = $supplyCostTotal+$taxTotal;
		$serialNum_ = "1";
		$cash = "";
		$chkBill = "";
		$note = "";
		$credit = "";
		$remark1 = "";
		$remark2 = "";
		$remark3 = "";
		$kwon = "";
		$ho = "";

		$code = "";
		$message = "";
		$ntsConfirmNum = "";


		$serialNum[1] = 1;
		$purchaseDT[1] = date("Y-m-d");
		$itemName[1] = $PayGoods;
		$spec[1] = "";
		$qty[1] = "";
		$unitCost[1] = $ClassOrderPayUseCashPrice;
		$supplyCost[1] = $ClassOrderPayUseCashPrice;
		$tax[1] = "";
		$remark[1] = "";

		for ($ii=2;$ii<=5;$ii++){

			$serialNum[$ii] = $ii;
			$purchaseDT[$ii] = "";
			$itemName[$ii] = "";
			$spec[$ii] = "";
			$qty[$ii] = "";
			$unitCost[$ii] = "";
			$supplyCost[$ii] = "";
			$tax[$ii] = "";
			$remark[$ii] = "";

		}

	}



}else{

}

?>


<div id="mainWrap" style="margin:20px auto;">
<div id="contents">
	<div id="TaxFormWrap">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
	
		<input type="hidden" name="TaxInvoiceID" value="<?=$TaxInvoiceID?>">

		<input type="hidden" name="TaxInvoiceRegType" value="<?=$TaxInvoiceRegType?>">
		<input type="hidden" name="TaxInvoiceType" value="<?=$TaxInvoiceType?>">
		<input type="hidden" name="TaxInvoicePayID" value="<?=$TaxInvoicePayID?>">

		<input type="hidden" name="memo" value="<?=$memo?>">
		<input type="hidden" name="emailSubject" value="<?=$emailSubject?>">
		<input type="hidden" name="issueType" value="<?=$issueType?>">
		<input type="hidden" name="chargeDirection" value="<?=$chargeDirection?>">
		<input type="hidden" name="taxType" value="<?=$taxType?>">
		<input type="hidden" name="issueTiming" value="<?=$issueTiming?>">

		<input type="hidden" name="serialNum" value="<?=$serialNum_?>">
		<input type="hidden" name="cash" value="<?=$cash?>">
		<input type="hidden" name="chkBill" value="<?=$chkBill?>">
		<input type="hidden" name="note" value="<?=$note?>">
		<input type="hidden" name="credit" value="<?=$credit?>">
		
		<input type="hidden" name="invoiceeType" value="<?=$invoiceeType?>">
		<input type="hidden" name="invoiceeMgtKey" value="<?=$invoiceeMgtKey?>">
		
		<input type="hidden" name="invoicerTEL" value="<?=$invoicerTEL?>">
		<input type="hidden" name="invoicerHP" value="<?=$invoicerHP?>">
		<input type="hidden" name="invoicerContactName1" value="<?=$invoicerContactName1?>">
		<input type="hidden" name="invoicerContactName2" value="<?=$invoicerContactName2?>">

		<input type="hidden" name="invoiceeTEL" value="<?=$invoiceeTEL?>">
		<input type="hidden" name="invoiceeHP" value="<?=$invoiceeHP?>">
		<input type="hidden" name="invoiceeContactName1" value="<?=$invoiceeContactName1?>">
		<input type="hidden" name="invoiceeContactName2" value="<?=$invoiceeContactName2?>">

		

		<div id="tax_info_wrap">
			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td rowspan="2" class="tx_subject tx_bg" style="font-size:22px;">계산서</td>
				<td class="tx_no_label tx_bg">책번호</td>
				<td class="tx_no_field">
					<input type="text" name="kwon" value="<?=$kwon?>" style="width:35%;" class="allownumericwithoutdecimal"> 권 
					<input type="text" name="ho" value="<?=$ho?>" style="width:35%;" class="allownumericwithoutdecimal"> 호
				</td>
			</tr>
			<tr>
				<td class="tx_bg">일련번호</td>
				<td>
					<input type="text" name="invoicerMgtKey" value="<?=$invoicerMgtKey?>" size="20" readonly="readonly" style="background-color:#f1f1f1;"/>
				</td>
			</tr>
			</table>
			<table cellpadding="0" cellspacing="0" border="0" class="no_top_border">
			<tr>
				<td rowspan="5" class="tx_info_vertical tx_bg">공<br />급<br />자</td>
				<td class="tx_info_label1 tx_bg">등록번호</td>
				<td class="tx_info_field">
					<span id="Div_invoicerCorpNum"><?=substr($invoicerCorpNum, 0, 3)?>-<?=substr($invoicerCorpNum, 3, 2)?>-<?=substr($invoicerCorpNum, 5, 5)?></span>
					<input type="hidden" name="invoicerCorpNum" value="<?=$invoicerCorpNum?>">
					<!--<div onclick="GetTaxMemberInfo(9, 1)" style="width:100%;height:20px;margin-top:3px;font-size:13px;background-color:#cccccc;cursor:pointer;">기업정보가져오기</div>-->
				</td>
				<td class="tx_info_label1 tx_bg">종사업장<br />번호</td>
				<td class="tx_info_field">
					<span id="Div_invoicerTaxRegID"><?=$invoicerTaxRegID?></span>
					<input type="hidden" name="invoicerTaxRegID" value="<?=$invoicerTaxRegID?>">
				</td>
				<td rowspan="5" class="tx_info_vertical tx_bg">공<br />급<br />받<br />는<br />자</td>
				<td class="tx_info_label1 tx_bg">등록번호</td>
				<td class="tx_info_field">
					<input type="text" name="invoiceeCorpNum" value="<?=substr($invoiceeCorpNum, 0, 3)?>-<?=substr($invoiceeCorpNum, 3, 2)?>-<?=substr($invoiceeCorpNum, 5, 5)?>"/>
					<div onclick="GetTaxMemberInfo(<?=$OrganType?>, <?=$OrganID?>)" style="width:100%;height:20px;margin-top:3px;font-size:13px;background-color:#cccccc;cursor:pointer;">기업정보가져오기</div>
				</td>
				<td class="tx_info_label1 tx_bg">종사업장<br />번호</td>
				<td class="tx_info_field">
					<input type="text" name="invoiceeTaxRegID" value="<?=$invoiceeTaxRegID?>"  />
				</td>
			</tr>
			<tr>
				<td class="tx_info_label1 tx_bg">상호<br />(법인명)</td>
				<td class="tx_info_field">
					<span id="Div_invoicerCorpName"><?=$invoicerCorpName?></span>
					<input type="hidden" name="invoicerCorpName" value="<?=$invoicerCorpName?>">
				</td>
				<td class="tx_info_label1 tx_bg">성명<br />(대표자)</td>
				<td class="tx_info_field">
					<span id="Div_invoicerCEOName"><?=$invoicerCEOName?></span>
					<input type="hidden" name="invoicerCEOName" value="<?=$invoicerCEOName?>">
				</td>
				<td class="tx_info_label1 tx_bg">상호<br />(법인명)</td>
				<td class="tx_info_field">
					<input type="text" name="invoiceeCorpName" value="<?=$invoiceeCorpName?>"  />
				</td>
				<td class="tx_info_label1 tx_bg">성명<br />(대표자)</td>
				<td class="tx_info_field">
					<input type="text" name="invoiceeCEOName" value="<?=$invoiceeCEOName?>"  />
				</td>
			</tr>
			<tr>
				<td class="tx_info_label2 tx_bg">주소</td>
				<td colspan="3">
					<span id="Div_invoicerAddr"><?=$invoicerAddr?></span>
					<input type="hidden" name="invoicerAddr" value="<?=$invoicerAddr?>">
				</td>
				<td class="tx_info_label2 tx_bg">주소</td>
				<td colspan="3">
					<input type="text" name="invoiceeAddr" value="<?=$invoiceeAddr?>" size="45" />
				</td>
			</tr>
			<tr>
				<td class="tx_info_label2 tx_bg">업태</td>
				<td class="tx_info_field">
					<span id="Div_invoicerBizType"><?=$invoicerBizType?></span>
					<input type="hidden" name="invoicerBizType" value="<?=$invoicerBizType?>">
				</td>
				<td class="tx_info_label2 tx_bg">종목</td>
				<td class="tx_info_field">
					<span id="Div_invoicerBizClass"><?=$invoicerBizClass?></span>
					<input type="hidden" name="invoicerBizClass" value="<?=$invoicerBizClass?>">
				</td>
				<td class="tx_info_label2 tx_bg">업태</td>
				<td class="tx_info_field">
					<input type="text" name="invoiceeBizType" value="<?=$invoiceeBizType?>"/>
				</td>
				<td class="tx_info_label2 tx_bg">종목</td>
				<td class="tx_info_field">
					<input type="text" name="invoiceeBizClass" value="<?=$invoiceeBizClass?>"/>
				</td>
			</tr>
			<tr>
				<td class="tx_info_label2 tx_bg">이메일</td>
				<td class="tx_info_field" colspan="3">
					<input type="text" name="invoicerEmail1" value="<?=$invoicerEmail1?>">
				</td>
				<!--
				<td class="tx_info_label2 tx_bg">이메일(2)</td>
				<td class="tx_info_field">
					<input type="text" name="invoicerEmail2" value="<?=$invoicerEmail2?>">
				</td>
				-->
				<td class="tx_info_label2 tx_bg">이메일(1)</td>
				<td class="tx_info_field">
					<input type="text" name="invoiceeEmail1" value="<?=$invoiceeEmail1?>"/>
				</td>
				<td class="tx_info_label2 tx_bg">이메일(2)</td>
				<td class="tx_info_field">
					<input type="text" name="invoiceeEmail2" value="<?=$invoiceeEmail2?>"/>
				</td>
			</tr>
			</table>
			<table cellpadding="0" cellspacing="0" border="0" class="no_top_border">
			<tr>
				<td class="tx_sum_item1 tx_bg">작 성 일 자</td>
				<td class="tx_sum_item1 tx_bg">공 급 가 격</td>
				<td class="tx_sum_item1 tx_bg">세 액</td>
				<td class="tx_sum_item2 tx_bg">비 고</td>
			</tr>
			<tr>
				<td style="height:40px;padding-bottom:10px;">
					<input type="text" name="writeDate" id="writeDate" value="<?=$writeDate?>" style="border:0px;margin-top:2px;width:95%;height:22px;padding-right:0px;" onchange="ChWriteDate(this.value)"/>
					<script>
						$(document).ready(function() {
							$("#writeDate").kendoDatePicker({
								format: "yyyy-MM-dd"
							});
						});
					</script>
				</td>
				<td>
					<input type="text" name="supplyCostTotal" id="supplyCostTotal" class="money"  value="<?=$supplyCostTotal?>" readonly="readonly" style="background-color:#f1f1f1;"/>
					<input type="hidden" id="totalAmount" name="totalAmount" value="<?=$totalAmount?>">
				</td>
				<td>
					<input type="text" name="taxTotal" id="taxTotal" class="money"  value="<?=$taxTotal?>" readonly="readonly" style="background-color:#f1f1f1;"/>
				</td>
				<td>
					<input type="text" name="remark1" value="<?=$remark1?>" />
					<input type="hidden" name="remark2" value="<?=$remark2?>">
					<input type="hidden" name="remark3" value="<?=$remark3?>">
				</td>
			</tr>
			</table>


		</div><!-- /tax_info_wrap -->
		<div id="tax_item_wrap">
			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="tx_item_label1 tx_bg" style="width:15%;">거래일자</td>
				<td class="tx_item_label1 tx_bg">품 목<br>(공백이면 등록되지 않음)</td>
				<td class="tx_item_label1 tx_bg" style="width:8%;">규 격</td>
				<td class="tx_item_label1 tx_bg" style="width:5%;">수 량</td>
				<td class="tx_item_label1 tx_bg" style="width:10%;">단 가</td>
				<td class="tx_item_label1 tx_bg" style="width:10%;">공급가액<br>(필수)</td>
				<td class="tx_item_label1 tx_bg" style="width:8%;">세 액</td>
				<td class="tx_item_label2 tx_bg" style="width:18%;">비고</td>
			</tr>

			<?for ($ii=1;$ii<=5;$ii++){?>
			<input type="hidden" name="serialNum_<?=$ii?>"value="<?=$serialNum[$ii]?>" />
			<tr>
				<td style="height:40px;padding-bottom:10px;">
					<input type="text" name="purchaseDT_<?=$ii?>" id="purchaseDT_<?=$ii?>" value="<?=$purchaseDT[$ii]?>" style="border:0px;margin-top:2px;width:95%;height:22px;padding-right:0px;"/>
					<script>
						$(document).ready(function() {
							$("#purchaseDT_<?=$ii?>").kendoDatePicker({
								format: "yyyy-MM-dd"
							});
						});
					</script>
				</td>
				<td><input type="text" id="itemName_<?=$ii?>" name="itemName_<?=$ii?>" size="10" value="<?=$itemName[$ii]?>" onkeyup="CalCost()"/></td>
				<td><input type="text" id="spec_<?=$ii?>" name="spec_<?=$ii?>" size="10" value="<?=$spec[$ii]?>"/></td>
				<td><input type="text" id="qty_<?=$ii?>" name="qty_<?=$ii?>" class="money calculate allownumericwithoutdecimal" size="10" value="<?=$qty[$ii]?>" onfocus="this.select()" onkeyup="CalCost()" /></td>
				<td><input type="text" id="unitCost_<?=$ii?>" name="unitCost_<?=$ii?>" class="money calculate allownumericwithoutdecimal" size="10" value="<?=$unitCost[$ii]?>" onfocus="this.select()" onkeyup="CalCost()"/></td>
				<td><input type="text" id="supplyCost_<?=$ii?>" name="supplyCost_<?=$ii?>" class="money allownumericwithoutdecimal" size="10" value="<?=$supplyCost[$ii]?>" onfocus="this.select()" readonly="readonly" /></td>
				<td><input type="text" id="tax_<?=$ii?>" name="tax_<?=$ii?>" class="money allownumericwithoutdecimal" size="10" value="<?=$tax[$ii]?>" readonly="readonly" style="background-color:#f1f1f1;"/></td>
				<td><input type="text" id="remark_<?=$ii?>" name="remark_<?=$ii?>" class="money" size="10" value="<?=$remark[$ii]?>" readonly="readonly" /></td>
			</tr>
			<?}?>

			</table>
			<script>
			function ChWriteDate(d){
				for (ii=1;ii<=5;ii++){

					itemName = document.getElementById("itemName_"+ii).value;
					itemName = trim(itemName);
					if (itemName!="" && document.getElementById("purchaseDT_"+ii).value==""){
						document.getElementById("purchaseDT_"+ii).value = d;
					}
					
				}
			}

			function CalCost(){
				
				totalAmount = 0;
				for (ii=1;ii<=5;ii++){
					itemName = document.getElementById("itemName_"+ii).value;
					qty = document.getElementById("qty_"+ii).value;
					unitCost = document.getElementById("unitCost_"+ii).value;
				
					itemName = trim(itemName);
					if (isNaN(qty) || qty==""){
						qty = 1;
					}
					if (isNaN(unitCost) || unitCost==""){
						unitCost = 0;
					}

					supplyCost = qty * unitCost;
					if (qty >= 1 && unitCost>0){
						document.getElementById("supplyCost_"+ii).value = supplyCost;
					}

					if (itemName!=""){
						totalAmount = totalAmount + supplyCost;
					}
					
				}

				document.getElementById("supplyCostTotal").value = totalAmount;
				document.getElementById("totalAmount").value = totalAmount;
			}

			function trim(stringToTrim) {
				return stringToTrim.replace(/^\s+|\s+$/g,"");
			}
			</script>
		</div><!-- /tax_item_wrap -->
		<div id="tax_bottom_wrap">
			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="tx_btm_label1 tx_bg" style="width:70%;height:50px;"></td>
				<!--
				<td class="tx_btm_label1 tx_bg">합계금액</td>
				<td class="tx_btm_label1 tx_bg">현 금</td>
				<td class="tx_btm_label1 tx_bg">수 표</td>
				<td class="tx_btm_label1 tx_bg">어 음</td>
				<td class="tx_btm_label1 tx_bg">외상미수금</td>
				-->
				<td rowspan="2" class="tx_btm_label2" style="width:30%;">
					이 금액을
					<select name="purposeType" style="width:65px; height:24px; padding-left: 5px;">
						<option value="영수" <?if ($purposeType=="영수"){?>selected<?}?>>영수</option>
						<option value="청구" <?if ($purposeType=="청구"){?>selected<?}?>>청구</option>
					</select>
					함
				</td>
			</tr>
			<!--
			<tr>
				<td><input type="text" name="sumprice" class="money"  value="" readonly="readonly" readonly="readonly" style="background-color:#f1f1f1;"/></td>
				<td><input type="text" name="money" class="money" readonly="readonly" style="background-color:#f1f1f1;"/></td>
				<td><input type="text" name="moneycheck" class="money" readonly="readonly" style="background-color:#f1f1f1;"/></td>
				<td><input type="text" name="bill" class="money" readonly="readonly" style="background-color:#f1f1f1;"/></td>
				<td><input type="text" name="uncollect" readonly="readonly" style="background-color:#f1f1f1;"/></td>
			</tr>
			-->
			</table>
		</div><!-- /tax_bottom_wrap -->
		<div id="tax_button_wrap"><a href="javascript:FormSubmit();" class="tax_button">세금계산서 발행</a></div>
	</form>
	</div><!-- /TaxFormWrap -->
</div>
</div>

<?if ($ErrNum!=0){?>
<script>
alert("<?=$ErrMsg?>");
</script>
<?}?>


<script>
function FormSubmit(){

	for (ii=1;ii<=5;ii++){

		itemName = document.getElementById("itemName_"+ii).value;
		itemName = trim(itemName);
		if (itemName!="" && document.getElementById("purchaseDT_"+ii).value==""){
			alert("거래일자["+ii+"]를 입력해 주세요.");
			return;
		}

		supplyCost = document.getElementById("supplyCost_"+ii).value;
		if (itemName!="" && supplyCost==""){
			alert("공급가액["+ii+"]을 입력해 주세요.");
			return;
		}

	}

	if (confirm("발행 하시겠습니까?")){
	
		//alert("팝빌 실 서비스 코드 입력전!!! 발행 불가!!!");
		document.RegForm.action = "tax_reg_action.php";
		document.RegForm.submit();

	}
}



function GetTaxMemberInfo(OrganType, OrganID){

	url = "ajax_get_tax_member_info.php";

	//location.href = url + "?OrganType="+OrganType+"&OrganID="+OrganID;
	$.ajax(url, {
		data: {
			OrganType: OrganType,
			OrganID: OrganID
		},
		success: function (data) {

			TaxMemberInfoID = data.TaxMemberInfoID;
			CorpName = data.CorpName;
			CorpNum =  data.CorpNum;
			TaxRegID =  data.TaxRegID;
			CEOName =  data.CEOName;
			TEL1 =  data.TEL1;
			HP1 =  data.HP1;
			Addr =  data.Addr;
			BizType =  data.BizType;
			BizClass =  data.BizClass;
			ContactName1 = data.ContactName1;
			Email1 = data.Email1;
			ContactName2 = data.ContactName2;
			Email2 = data.Email2;

			if (TaxMemberInfoID==0){
				alert("기업의 세금계산서정보가 등록되어있지 않습니다.");
			}else{
				if (OrganType!=9){

					document.RegForm.invoiceeTEL.value = TEL1;
					document.RegForm.invoiceeHP.value = HP1;
					document.RegForm.invoiceeContactName1.value = ContactName1;
					document.RegForm.invoiceeContactName2.value = ContactName2;
					document.RegForm.invoiceeCorpNum.value = CorpNum;
					document.RegForm.invoiceeTaxRegID.value = TaxRegID;
					document.RegForm.invoiceeCorpName.value = CorpName;
					document.RegForm.invoiceeCEOName.value = CEOName;
					document.RegForm.invoiceeAddr.value = Addr;
					document.RegForm.invoiceeBizType.value = BizType;
					document.RegForm.invoiceeBizClass.value = BizClass;
					document.RegForm.invoiceeEmail1.value = Email1;
					document.RegForm.invoiceeEmail2.value = Email2;

				}else{

					document.RegForm.invoicerTEL.value = TEL1;
					document.RegForm.invoicerHP.value = HP1;
					document.RegForm.invoicerContactName1.value = ContactName1;
					document.RegForm.invoicerContactName2.value = ContactName2;
					document.RegForm.invoicerCorpNum.value = CorpNum;
					document.RegForm.invoicerTaxRegID.value = TaxRegID;
					document.RegForm.invoicerCorpName.value = CorpName;
					document.RegForm.invoicerCEOName.value = CEOName;
					document.RegForm.invoicerAddr.value = Addr;
					document.RegForm.invoicerBizType.value = BizType;
					document.RegForm.invoicerBizClass.value = BizClass;
					document.RegForm.invoicerEmail1.value = Email1;
					document.RegForm.invoicerEmail2.value = Email2;

					document.getElementById("Div_invoicerCorpNum").innerHTML = CorpNum;
					document.getElementById("Div_invoicerTaxRegID").innerHTML = TaxRegID;
					document.getElementById("Div_invoicerCorpName").innerHTML = CorpName;
					document.getElementById("Div_invoicerCEOName").innerHTML = CEOName;
					document.getElementById("Div_invoicerAddr").innerHTML = Addr;
					document.getElementById("Div_invoicerBizType").innerHTML = BizType;
					document.getElementById("Div_invoicerBizClass").innerHTML = BizClass;
				}
			}
		},
		error: function () {

		}
	});

}


//float
$(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
	//this.value = this.value.replace(/[^0-9\.]/g,'');
	$(this).val($(this).val().replace(/[^0-9\.]/g,''));
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});

//int
$(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {    
	$(this).val($(this).val().replace(/[^\d].+/, ""));
	if ((event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});

</script>

</body>
</html>
<?php
include_once('../includes/dbclose.php');
?>