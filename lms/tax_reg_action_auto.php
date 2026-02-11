<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$ErrNum = 0;
$ErrMsg = "";


$ClassOrderPayID = isset($_REQUEST["ClassOrderPayID"]) ? $_REQUEST["ClassOrderPayID"] : "";
$SearchCenterID = isset($_REQUEST["SearchCenterID"]) ? $_REQUEST["SearchCenterID"] : "";
$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

// 수업료 충전시 수업료에 대한 정보를 가져오기 위해 추가
$GoodType = isset($_REQUEST["GoodType"]) ? $_REQUEST["GoodType"] : "";
$GoodName = isset($_REQUEST["GoodName"]) ? $_REQUEST["GoodName"] : "";
$GoodMoney = isset($_REQUEST["GoodMoney"]) ? $_REQUEST["GoodMoney"] : "";
$SavedMoneyID = isset($_REQUEST["SavedMoneyID"]) ? $_REQUEST["SavedMoneyID"] : "";


$CenterID = $SearchCenterID;

$TaxInvoiceID = "";
$TaxInvoiceRegType = 1;
$TaxInvoiceType = 1;
$TaxInvoicePayID = $ClassOrderPayID; 


//만약 충전금일 경우
if ($GoodType == "SavedMoney"){
	$PayGoods = $GoodName;
	$ClassOrderPayUseCashPrice = $GoodMoney;
	$TaxInvoicePayID = $SavedMoneyID; 
} else { // 충전금이 아닌 일반 결제인 경우
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
}




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


$Sql = "
		select 
			A.*
		from TaxMemberInfos A
		where A.OrganType=1 and A.OrganID=:OrganID
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':OrganID', $SearchCenterID);
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

}else{


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
	$serialNum = "1";
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


	$serialNum_[1] = 1;
	$purchaseDT_[1] = date("Y-m-d");
	$itemName_[1] = $PayGoods;
	$spec_[1] = "";
	$qty_[1] = "";
	$unitCost_[1] = $ClassOrderPayUseCashPrice;
	$supplyCost_[1] = $ClassOrderPayUseCashPrice;
	$tax_[1] = "";
	$remark_[1] = "";

	for ($ii=2;$ii<=5;$ii++){

		$serialNum_[$ii] = $ii;
		$purchaseDT_[$ii] = "";
		$itemName_[$ii] = "";
		$spec_[$ii] = "";
		$qty_[$ii] = "";
		$unitCost_[$ii] = "";
		$supplyCost_[$ii] = "";
		$tax_[$ii] = "";
		$remark_[$ii] = "";

	}


	$invoicerCorpNum = str_replace("-","",$invoicerCorpNum);
	$invoiceeCorpNum = str_replace("-","",$invoiceeCorpNum);
	$writeDate = str_replace("-","",$writeDate);



	$Sql = " insert into TaxInvoices ( ";

		$Sql .= " TaxInvoiceRegType, ";
		$Sql .= " TaxInvoiceType, ";
		$Sql .= " TaxInvoicePayID, ";
		$Sql .= " memo, ";
		$Sql .= " emailSubject, ";
		$Sql .= " writeDate, ";
		$Sql .= " issueType, ";
		$Sql .= " chargeDirection, ";
		$Sql .= " purposeType, ";
		$Sql .= " taxType, ";
		$Sql .= " issueTiming, ";

		$Sql .= " invoicerMgtKey, ";
		$Sql .= " invoicerCorpName, ";
		$Sql .= " invoicerCorpNum, ";
		$Sql .= " invoicerTaxRegID, ";
		$Sql .= " invoicerCEOName, ";
		$Sql .= " invoicerTEL, ";
		$Sql .= " invoicerHP, ";
		$Sql .= " invoicerAddr, ";
		$Sql .= " invoicerBizClass, ";
		$Sql .= " invoicerBizType, ";
		$Sql .= " invoicerContactName1, ";
		$Sql .= " invoicerEmail1, ";
		$Sql .= " invoicerContactName2, ";
		$Sql .= " invoicerEmail2, ";

		$Sql .= " invoiceeType, ";
		$Sql .= " invoiceeMgtKey, ";
		$Sql .= " invoiceeCorpName, ";
		$Sql .= " invoiceeCorpNum, ";
		$Sql .= " invoiceeTaxRegID, ";
		$Sql .= " invoiceeCEOName, ";
		$Sql .= " invoiceeTEL, ";
		$Sql .= " invoiceeHP, ";
		$Sql .= " invoiceeAddr, ";
		$Sql .= " invoiceeBizType, ";
		$Sql .= " invoiceeBizClass, ";
		$Sql .= " invoiceeContactName1, ";
		$Sql .= " invoiceeEmail1, ";
		$Sql .= " invoiceeContactName2, ";
		$Sql .= " invoiceeEmail2, ";

		$Sql .= " supplyCostTotal, ";
		$Sql .= " taxTotal, ";
		$Sql .= " totalAmount, ";
		$Sql .= " serialNum, ";
		$Sql .= " cash, ";
		$Sql .= " chkBill, ";
		$Sql .= " note, ";
		$Sql .= " credit, ";
		$Sql .= " remark1, ";
		$Sql .= " remark2, ";
		$Sql .= " remark3, ";
		$Sql .= " kwon, ";
		$Sql .= " ho, ";

		$Sql .= " TaxInvoiceState, ";
		$Sql .= " TaxInvoiceRegDateTime, ";
		$Sql .= " TaxInvoiceModiDateTime ";

	$Sql .= " ) values ( ";

		$Sql .= " :TaxInvoiceRegType, ";
		$Sql .= " :TaxInvoiceType, ";
		$Sql .= " :TaxInvoicePayID, ";
		$Sql .= " :memo, ";
		$Sql .= " :emailSubject, ";
		$Sql .= " :writeDate, ";
		$Sql .= " :issueType, ";
		$Sql .= " :chargeDirection, ";
		$Sql .= " :purposeType, ";
		$Sql .= " :taxType, ";
		$Sql .= " :issueTiming, ";

		$Sql .= " :invoicerMgtKey, ";
		$Sql .= " :invoicerCorpName, ";
		$Sql .= " :invoicerCorpNum, ";
		$Sql .= " :invoicerTaxRegID, ";
		$Sql .= " :invoicerCEOName, ";
		$Sql .= " :invoicerTEL, ";
		$Sql .= " :invoicerHP, ";
		$Sql .= " :invoicerAddr, ";
		$Sql .= " :invoicerBizClass, ";
		$Sql .= " :invoicerBizType, ";
		$Sql .= " :invoicerContactName1, ";
		$Sql .= " :invoicerEmail1, ";
		$Sql .= " :invoicerContactName2, ";
		$Sql .= " :invoicerEmail2, ";

		$Sql .= " :invoiceeType, ";
		$Sql .= " :invoiceeMgtKey, ";
		$Sql .= " :invoiceeCorpName, ";
		$Sql .= " :invoiceeCorpNum, ";
		$Sql .= " :invoiceeTaxRegID, ";
		$Sql .= " :invoiceeCEOName, ";
		$Sql .= " :invoiceeTEL, ";
		$Sql .= " :invoiceeHP, ";
		$Sql .= " :invoiceeAddr, ";
		$Sql .= " :invoiceeBizType, ";
		$Sql .= " :invoiceeBizClass, ";
		$Sql .= " :invoiceeContactName1, ";
		$Sql .= " :invoiceeEmail1, ";
		$Sql .= " :invoiceeContactName2, ";
		$Sql .= " :invoiceeEmail2, ";

		$Sql .= " :supplyCostTotal, ";
		$Sql .= " :taxTotal, ";
		$Sql .= " :totalAmount, ";
		$Sql .= " :serialNum, ";
		$Sql .= " :cash, ";
		$Sql .= " :chkBill, ";
		$Sql .= " :note, ";
		$Sql .= " :credit, ";
		$Sql .= " :remark1, ";
		$Sql .= " :remark2, ";
		$Sql .= " :remark3, ";
		$Sql .= " :kwon, ";
		$Sql .= " :ho, ";

		$Sql .= " 1, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TaxInvoiceRegType', $TaxInvoiceRegType);
	$Stmt->bindParam(':TaxInvoiceType', $TaxInvoiceType);
	$Stmt->bindParam(':TaxInvoicePayID', $TaxInvoicePayID);
	$Stmt->bindParam(':memo', $memo);
	$Stmt->bindParam(':emailSubject', $emailSubject);
	$Stmt->bindParam(':writeDate', $writeDate);
	$Stmt->bindParam(':issueType', $issueType);
	$Stmt->bindParam(':chargeDirection', $chargeDirection);
	$Stmt->bindParam(':purposeType', $purposeType);
	$Stmt->bindParam(':taxType', $taxType);
	$Stmt->bindParam(':issueTiming', $issueTiming);

	$Stmt->bindParam(':invoicerMgtKey', $invoicerMgtKey);
	$Stmt->bindParam(':invoicerCorpName', $invoicerCorpName);
	$Stmt->bindParam(':invoicerCorpNum', $invoicerCorpNum);
	$Stmt->bindParam(':invoicerTaxRegID', $invoicerTaxRegID);
	$Stmt->bindParam(':invoicerCEOName', $invoicerCEOName);
	$Stmt->bindParam(':invoicerTEL', $invoicerTEL);
	$Stmt->bindParam(':invoicerHP', $invoicerHP);
	$Stmt->bindParam(':invoicerAddr', $invoicerAddr);
	$Stmt->bindParam(':invoicerBizClass', $invoicerBizClass);
	$Stmt->bindParam(':invoicerBizType', $invoicerBizType);
	$Stmt->bindParam(':invoicerContactName1', $invoicerContactName1);
	$Stmt->bindParam(':invoicerEmail1', $invoicerEmail1);
	$Stmt->bindParam(':invoicerContactName2', $invoicerContactName2);
	$Stmt->bindParam(':invoicerEmail2', $invoicerEmail2);

	$Stmt->bindParam(':invoiceeType', $invoiceeType);
	$Stmt->bindParam(':invoiceeMgtKey', $invoiceeMgtKey);
	$Stmt->bindParam(':invoiceeCorpName', $invoiceeCorpName);
	$Stmt->bindParam(':invoiceeCorpNum', $invoiceeCorpNum);
	$Stmt->bindParam(':invoiceeTaxRegID', $invoiceeTaxRegID);
	$Stmt->bindParam(':invoiceeCEOName', $invoiceeCEOName);
	$Stmt->bindParam(':invoiceeTEL', $invoiceeTEL);
	$Stmt->bindParam(':invoiceeHP', $invoiceeHP);
	$Stmt->bindParam(':invoiceeAddr', $invoiceeAddr);
	$Stmt->bindParam(':invoiceeBizType', $invoiceeBizType);
	$Stmt->bindParam(':invoiceeBizClass', $invoiceeBizClass);
	$Stmt->bindParam(':invoiceeContactName1', $invoiceeContactName1);
	$Stmt->bindParam(':invoiceeEmail1', $invoiceeEmail1);
	$Stmt->bindParam(':invoiceeContactName2', $invoiceeContactName2);
	$Stmt->bindParam(':invoiceeEmail2', $invoiceeEmail2);

	$Stmt->bindParam(':supplyCostTotal', $supplyCostTotal);
	$Stmt->bindParam(':taxTotal', $taxTotal);
	$Stmt->bindParam(':totalAmount', $totalAmount);
	$Stmt->bindParam(':serialNum', $serialNum);
	$Stmt->bindParam(':cash', $cash);
	$Stmt->bindParam(':chkBill', $chkBill);
	$Stmt->bindParam(':note', $note);
	$Stmt->bindParam(':credit', $credit);
	$Stmt->bindParam(':remark1', $remark1);
	$Stmt->bindParam(':remark2', $remark2);
	$Stmt->bindParam(':remark3', $remark3);
	$Stmt->bindParam(':kwon', $kwon);
	$Stmt->bindParam(':ho', $ho);
	$Stmt->execute();
	$TaxInvoiceID = $DbConn->lastInsertId();
	$Stmt = null;


	$serialNum = 1;
	for ($ii=1;$ii<=5;$ii++){

		//$serialNum = $serialNum_[$ii];//사용안하고 계산해서 상용
		$purchaseDT = $purchaseDT_[$ii];
		$itemName = $itemName_[$ii];
		$spec = $spec_[$ii];
		$qty = $qty_[$ii];
		$unitCost = $unitCost_[$ii];
		$supplyCost = $supplyCost_[$ii];
		$tax = $tax_[$ii];
		$remark = $remark_[$ii];

		$purchaseDT = str_replace("-","",$purchaseDT)."000000";

		if (trim($itemName)!=""){
			
			$Sql = " insert into TaxInvoiceItems ( ";
				$Sql .= " TaxInvoiceID, ";
				$Sql .= " serialNum, ";
				$Sql .= " purchaseDT, ";
				$Sql .= " itemName, ";
				$Sql .= " spec, ";
				$Sql .= " qty, ";
				$Sql .= " unitCost, ";
				$Sql .= " supplyCost, ";
				$Sql .= " tax, ";
				$Sql .= " remark ";

			$Sql .= " ) values ( ";

				$Sql .= " :TaxInvoiceID, ";
				$Sql .= " :serialNum, ";
				$Sql .= " :purchaseDT, ";
				$Sql .= " :itemName, ";
				$Sql .= " :spec, ";
				$Sql .= " :qty, ";
				$Sql .= " :unitCost, ";
				$Sql .= " :supplyCost, ";
				$Sql .= " :tax, ";
				$Sql .= " :remark ";
			$Sql .= " ) ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':TaxInvoiceID', $TaxInvoiceID);
			$Stmt->bindParam(':serialNum', $serialNum);
			$Stmt->bindParam(':purchaseDT', $purchaseDT);
			$Stmt->bindParam(':itemName', $itemName);
			$Stmt->bindParam(':spec', $spec);
			$Stmt->bindParam(':qty', $qty);
			$Stmt->bindParam(':unitCost', $unitCost);
			$Stmt->bindParam(':supplyCost', $supplyCost);
			$Stmt->bindParam(':tax', $tax);
			$Stmt->bindParam(':remark', $remark);
			$Stmt->execute();
			$Stmt = null;

			$serialNum++;
		
		}

	}

}

include_once('../includes/dbclose.php');


if ($ErrNum == 0){
?>
<script>
location.href = "../popbill/TaxinvoiceExample/RegistIssue_mangoi.php?TaxInvoiceID=<?=$TaxInvoiceID?>&FromPage=lmsb2b_auto&SearchCenterID=<?=$SearchCenterID?>&SearchYear=<?=$SearchYear?>&SearchMonth=<?=$SearchMonth?>";
</script>
<?
}else{
?>
<script>
location.href = "class_order_renew_center_form.php?SearchCenterID=<?=$SearchCenterID?>&SearchYear=<?=$SearchYear?>&SearchMonth=<?=$SearchMonth?>";
</script>
<?
}
?>


