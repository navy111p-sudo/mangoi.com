<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$TaxInvoiceID = isset($_REQUEST["TaxInvoiceID"]) ? $_REQUEST["TaxInvoiceID"] : "";

$TaxInvoiceRegType = isset($_REQUEST["TaxInvoiceRegType"]) ? $_REQUEST["TaxInvoiceRegType"] : "";
$TaxInvoiceType = isset($_REQUEST["TaxInvoiceType"]) ? $_REQUEST["TaxInvoiceType"] : "";
$TaxInvoicePayID = isset($_REQUEST["TaxInvoicePayID"]) ? $_REQUEST["TaxInvoicePayID"] : ""; 


$memo = isset($_REQUEST["memo"]) ? $_REQUEST["memo"] : "";
$emailSubject = isset($_REQUEST["emailSubject"]) ? $_REQUEST["emailSubject"] : "";
$writeDate = isset($_REQUEST["writeDate"]) ? $_REQUEST["writeDate"] : "";
$issueType = isset($_REQUEST["issueType"]) ? $_REQUEST["issueType"] : "";
$chargeDirection = isset($_REQUEST["chargeDirection"]) ? $_REQUEST["chargeDirection"] : "";
$purposeType = isset($_REQUEST["purposeType"]) ? $_REQUEST["purposeType"] : "";
$taxType = isset($_REQUEST["taxType"]) ? $_REQUEST["taxType"] : "";
$issueTiming = isset($_REQUEST["issueTiming"]) ? $_REQUEST["issueTiming"] : "";

$invoicerMgtKey = isset($_REQUEST["invoicerMgtKey"]) ? $_REQUEST["invoicerMgtKey"] : "";
$invoicerCorpName = isset($_REQUEST["invoicerCorpName"]) ? $_REQUEST["invoicerCorpName"] : "";
$invoicerCorpNum = isset($_REQUEST["invoicerCorpNum"]) ? $_REQUEST["invoicerCorpNum"] : "";
$invoicerTaxRegID = isset($_REQUEST["invoicerTaxRegID"]) ? $_REQUEST["invoicerTaxRegID"] : "";
$invoicerCEOName = isset($_REQUEST["invoicerCEOName"]) ? $_REQUEST["invoicerCEOName"] : "";
$invoicerTEL = isset($_REQUEST["invoicerTEL"]) ? $_REQUEST["invoicerTEL"] : "";
$invoicerHP = isset($_REQUEST["invoicerHP"]) ? $_REQUEST["invoicerHP"] : "";
$invoicerAddr = isset($_REQUEST["invoicerAddr"]) ? $_REQUEST["invoicerAddr"] : "";
$invoicerBizType = isset($_REQUEST["invoicerBizType"]) ? $_REQUEST["invoicerBizType"] : "";
$invoicerBizClass = isset($_REQUEST["invoicerBizClass"]) ? $_REQUEST["invoicerBizClass"] : "";
$invoicerContactName1 = isset($_REQUEST["invoicerContactName1"]) ? $_REQUEST["invoicerContactName1"] : "";
$invoicerEmail1 = isset($_REQUEST["invoicerEmail1"]) ? $_REQUEST["invoicerEmail1"] : "";
$invoicerContactName2 = isset($_REQUEST["invoicerContactName2"]) ? $_REQUEST["invoicerContactName2"] : "";
$invoicerEmail2 = isset($_REQUEST["invoicerEmail2"]) ? $_REQUEST["invoicerEmail2"] : "";

$invoiceeType = isset($_REQUEST["invoiceeType"]) ? $_REQUEST["invoiceeType"] : "";
$invoiceeMgtKey = isset($_REQUEST["invoiceeMgtKey"]) ? $_REQUEST["invoiceeMgtKey"] : "";
$invoiceeCorpName = isset($_REQUEST["invoiceeCorpName"]) ? $_REQUEST["invoiceeCorpName"] : "";
$invoiceeCorpNum = isset($_REQUEST["invoiceeCorpNum"]) ? $_REQUEST["invoiceeCorpNum"] : "";
$invoiceeTaxRegID = isset($_REQUEST["invoiceeTaxRegID"]) ? $_REQUEST["invoiceeTaxRegID"] : "";
$invoiceeCEOName = isset($_REQUEST["invoiceeCEOName"]) ? $_REQUEST["invoiceeCEOName"] : "";
$invoiceeTEL = isset($_REQUEST["invoiceeTEL"]) ? $_REQUEST["invoiceeTEL"] : "";
$invoiceeHP = isset($_REQUEST["invoiceeHP"]) ? $_REQUEST["invoiceeHP"] : "";
$invoiceeAddr = isset($_REQUEST["invoiceeAddr"]) ? $_REQUEST["invoiceeAddr"] : "";
$invoiceeBizType = isset($_REQUEST["invoiceeBizType"]) ? $_REQUEST["invoiceeBizType"] : "";
$invoiceeBizClass = isset($_REQUEST["invoiceeBizClass"]) ? $_REQUEST["invoiceeBizClass"] : "";
$invoiceeContactName1 = isset($_REQUEST["invoiceeContactName1"]) ? $_REQUEST["invoiceeContactName1"] : "";
$invoiceeEmail1 = isset($_REQUEST["invoiceeEmail1"]) ? $_REQUEST["invoiceeEmail1"] : "";
$invoiceeContactName2 = isset($_REQUEST["invoiceeContactName2"]) ? $_REQUEST["invoiceeContactName2"] : "";
$invoiceeEmail2 = isset($_REQUEST["invoiceeEmail2"]) ? $_REQUEST["invoiceeEmail2"] : "";

$supplyCostTotal = isset($_REQUEST["supplyCostTotal"]) ? $_REQUEST["supplyCostTotal"] : "";
$taxTotal = isset($_REQUEST["taxTotal"]) ? $_REQUEST["taxTotal"] : "";
$totalAmount = isset($_REQUEST["totalAmount"]) ? $_REQUEST["totalAmount"] : "";
$serialNum = isset($_REQUEST["serialNum"]) ? $_REQUEST["serialNum"] : "";
$cash = isset($_REQUEST["cash"]) ? $_REQUEST["cash"] : "";
$chkBill = isset($_REQUEST["chkBill"]) ? $_REQUEST["chkBill"] : "";
$note = isset($_REQUEST["note"]) ? $_REQUEST["note"] : "";
$credit = isset($_REQUEST["credit"]) ? $_REQUEST["credit"] : "";
$remark1 = isset($_REQUEST["remark1"]) ? $_REQUEST["remark1"] : "";
$remark2 = isset($_REQUEST["remark2"]) ? $_REQUEST["remark2"] : "";
$remark3 = isset($_REQUEST["remark3"]) ? $_REQUEST["remark3"] : "";
$kwon = isset($_REQUEST["kwon"]) ? $_REQUEST["kwon"] : "";
$ho = isset($_REQUEST["ho"]) ? $_REQUEST["ho"] : "";

$invoicerCorpNum = str_replace("-","",$invoicerCorpNum);
$invoiceeCorpNum = str_replace("-","",$invoiceeCorpNum);
$writeDate = str_replace("-","",$writeDate);

if ($TaxInvoiceID!=""){//기존 실패한 계산서 삭제

	$Sql = " update TaxInvoices set ";
		$Sql .= " TaxInvoiceState = 0, ";
		$Sql .= " TaxInvoiceModiDateTime = now() ";
	$Sql .= " where TaxInvoiceID = :TaxInvoiceID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TaxInvoiceID', $TaxInvoiceID);
	$Stmt->execute();
	$Stmt = null;

}



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

	//$serialNum = isset($_REQUEST["serialNum_".$ii]) ? $_REQUEST["serialNum_".$ii] : "";//사용안하고 계산해서 상용
	$purchaseDT = isset($_REQUEST["purchaseDT_".$ii]) ? $_REQUEST["purchaseDT_".$ii] : "";
	$itemName = isset($_REQUEST["itemName_".$ii]) ? $_REQUEST["itemName_".$ii] : "";
	$spec = isset($_REQUEST["spec_".$ii]) ? $_REQUEST["spec_".$ii] : "";
	$qty = isset($_REQUEST["qty_".$ii]) ? $_REQUEST["qty_".$ii] : "";
	$unitCost = isset($_REQUEST["unitCost_".$ii]) ? $_REQUEST["unitCost_".$ii] : "";
	$supplyCost = isset($_REQUEST["supplyCost_".$ii]) ? $_REQUEST["supplyCost_".$ii] : "";
	$tax = isset($_REQUEST["tax_".$ii]) ? $_REQUEST["tax_".$ii] : "";
	$remark = isset($_REQUEST["remark_".$ii]) ? $_REQUEST["remark_".$ii] : "";

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




if ($err_num != 0){
	include_once('./inc_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
//history.go(-1);
</script>
<?php
	include_once('./inc_footer.php'); 
?>
</body>
</html>
<?
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
?>
<script>
//parent.location.reload();
location.href = "../popbill/TaxinvoiceExample/RegistIssue_mangoi.php?TaxInvoiceID=<?=$TaxInvoiceID?>&FromPage=lmsb2b_manual"
</script>
<?
}
?>


