<?
include_once('../../includes/dbopen.php');
include_once('../../includes/common.php');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>팝빌</title>
</head>
<?php
$TaxInvoiceID = isset($_REQUEST["TaxInvoiceID"]) ? $_REQUEST["TaxInvoiceID"] : "";
$FromPage = isset($_REQUEST["FromPage"]) ? $_REQUEST["FromPage"] : "";

	//FromPage : lms b2b 자동발행
	$SearchCenterID = isset($_REQUEST["SearchCenterID"]) ? $_REQUEST["SearchCenterID"] : "";
	$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
	$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";



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
$invoicerContactName2 = $Row["invoicerContactName2"];//사용안됨
$invoicerEmail2 = $Row["invoicerEmail2"];//사용안됨

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
$serialNum = $Row["serialNum"];
$cash = $Row["cash"];
$chkBill = $Row["chkBill"];
$note = $Row["note"];
$credit = $Row["credit"];
$remark1 = $Row["remark1"];
$remark2 = $Row["remark2"];
$remark3 = $Row["remark3"];
$kwon = $Row["kwon"];
$ho = $Row["ho"];

$kwon = SpaceToNull($kwon);
$ho = SpaceToNull($ho);



$supplyCostTotal = SpaceToZero($supplyCostTotal);
$taxTotal = SpaceToZero($taxTotal);
$totalAmount = SpaceToZero($totalAmount);

//==============================================================================================

// 모듈 인클루드 =================================
require_once '../Popbill/PopbillTaxinvoice.php';

$LinkID = $Popbill_LinkID;//common.php
$SecretKey = $Popbill_SecretKey;//common.php
define('LINKHUB_COMM_MODE','CURL');
$TaxinvoiceService = new TaxinvoiceService($LinkID, $SecretKey);
$TaxinvoiceService->IsTest(false);
$TaxinvoiceService->IPRestrictOnOff(true);
// 모듈 인클루드 =================================

$invoicerCorpNum = $invoicerCorpNum;// 팝빌회원 사업자번호, '-' 제외 10자리
$invoicerUserID = 'mangoi';// 팝빌회원 아이디
$invoicerMgtKey = $invoicerMgtKey;// 세금계산서 문서번호 // - 최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성
$forceIssue = false;// 지연발행 강제여부
$memo = $memo;// 즉시발행 메모
$emailSubject = '';// 안내메일 제목, 미기재시 기본제목으로 전송
$writeSpecification = false;// 거래명세서 동시작성 여부
$dealInvoiceMgtKey = '';// 거래명세서 동시작성시 명세서 관리번호 // - 최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성



/************************************************************
 *                        세금계산서 정보
 ************************************************************/
$Taxinvoice = new Taxinvoice();// 세금계산서 객체 생성
$Taxinvoice->writeDate = $writeDate;// [필수] 작성일자, 형식(yyyyMMdd) 예)20150101
$Taxinvoice->issueType = $issueType;// [필수] 발행형태, '정발행', '역발행', '위수탁' 중 기재
$Taxinvoice->chargeDirection = $chargeDirection;// [필수] 과금방향, // - '정과금'(공급자 과금), '역과금'(공급받는자 과금) 중 기재, 역과금은 역발행시에만 가능.
$Taxinvoice->purposeType = $purposeType;// [필수] '영수', '청구' 중 기재
$Taxinvoice->taxType = $taxType;// [필수] 과세형태, '과세', '영세', '면세' 중 기재
$Taxinvoice->issueTiming = $issueTiming;// [필수] 발행시점


/************************************************************
 *                         공급자 정보
 ************************************************************/
$Taxinvoice->invoicerCorpNum = $invoicerCorpNum;// [필수] 공급자 사업자번호
$Taxinvoice->invoicerTaxRegID = $invoicerTaxRegID;// 공급자 종사업장 식별번호, 4자리 숫자 문자열
$Taxinvoice->invoicerCorpName = $invoicerCorpName;// [필수] 공급자 상호
$Taxinvoice->invoicerMgtKey = $invoicerMgtKey;// [필수] 공급자 문서번호, 최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성
$Taxinvoice->invoicerCEOName = $invoicerCEOName;// [필수] 공급자 대표자성명
$Taxinvoice->invoicerAddr = $invoicerAddr;// 공급자 주소
$Taxinvoice->invoicerBizType = $invoicerBizType;// 공급자 업태
$Taxinvoice->invoicerBizClass = $invoicerBizClass;// 공급자 종목
$Taxinvoice->invoicerContactName = $invoicerContactName1;// 공급자 담당자 성명
$Taxinvoice->invoicerEmail = $invoicerEmail1;// 공급자 담당자 메일주소
$Taxinvoice->invoicerTEL = $invoicerTEL;// 공급자 담당자 연락처
$Taxinvoice->invoicerHP = $invoicerHP;// 공급자 휴대폰 번호
$Taxinvoice->invoicerSMSSendYN = false;// 발행시 알림문자 전송여부 (정발행에서만 사용가능)// - 공급받는자 주)담당자 휴대폰번호(invoiceeHP1)로 전송// - 전송시 포인트가 차감되며 전송실패하는 경우 포인트 환불처리


/************************************************************
 *                      공급받는자 정보
 ************************************************************/
$Taxinvoice->invoiceeType = $invoiceeType;// [필수] 공급받는자 구분, '사업자', '개인', '외국인' 중 기재
$Taxinvoice->invoiceeCorpNum = $invoiceeCorpNum;// [필수] 공급받는자 사업자번호
$Taxinvoice->invoiceeTaxRegID = $invoiceeTaxRegID;// 공급받는자 종사업장 식별번호, 4자리 숫자 문자열
$Taxinvoice->invoiceeCorpName = $invoiceeCorpName;// [필수] 공급자 상호
$Taxinvoice->invoiceeMgtKey = $invoiceeMgtKey;// [역발행시 필수] 공급받는자 문서번호, 최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성
$Taxinvoice->invoiceeCEOName = $invoiceeCEOName;// [필수] 공급받는자 대표자성명
$Taxinvoice->invoiceeAddr = $invoiceeAddr;// 공급받는자 주소
$Taxinvoice->invoiceeBizType = $invoiceeBizType;// 공급받는자 업태
$Taxinvoice->invoiceeBizClass = $invoiceeBizClass;// 공급받는자 종목
$Taxinvoice->invoiceeContactName1 = $invoiceeContactName1;// 공급받는자 담당자 성명
$Taxinvoice->invoiceeEmail1 = $invoiceeEmail1;// 공급받는자 담당자 메일주소// 팝빌 개발환경에서 테스트하는 경우에도 안내 메일이 전송되므로,// 실제 거래처의 메일주소가 기재되지 않도록 주의
$Taxinvoice->invoiceeTEL1 = $invoiceeTEL;// 공급받는자 담당자 연락처
$Taxinvoice->invoiceeHP1 = $invoiceeHP;// 공급받는자 담당자 휴대폰 번호



/************************************************************
 *                       세금계산서 기재정보
 ************************************************************/
$Taxinvoice->supplyCostTotal = $supplyCostTotal;// [필수] 공급가액 합계
$Taxinvoice->taxTotal = $taxTotal;// [필수] 세액 합계
$Taxinvoice->totalAmount = $totalAmount;// [필수] 합계금액, (공급가액 합계 + 세액 합계)
$Taxinvoice->serialNum = $serialNum;// 기재상 '일련번호'항목
$Taxinvoice->cash = $cash ;// 기재상 '현금'항목
$Taxinvoice->chkBill = $chkBill;// 기재상 '수표'항목
$Taxinvoice->note = $note;// 기재상 '어음'항목
$Taxinvoice->credit = $credit;// 기재상 '외상'항목
$Taxinvoice->remark1 = $remark1;// 기재상 '비고' 항목// 기재상 '비고' 항목
$Taxinvoice->remark2 = $remark2;
$Taxinvoice->remark3 = $remark3;
$Taxinvoice->kwon = $kwon;// 기재상 '권' 항목, 최대값 32767// 미기재시 $Taxinvoice->kwon = 'null';
$Taxinvoice->ho = $ho;// 기재상 '호' 항목, 최대값 32767// 미기재시 $Taxinvoice->ho = 'null';
$Taxinvoice->businessLicenseYN = false;// 사업자등록증 이미지파일 첨부여부
$Taxinvoice->bankBookYN = false;// 통장사본 이미지파일 첨부여부



/************************************************************
 *                     수정 세금계산서 기재정보
 * - 수정세금계산서 관련 정보는 연동매뉴얼 또는 개발가이드 링크 참조
 * - [참고] 수정세금계산서 작성방법 안내 - https://docs.popbill.com/taxinvoice/modify?lang=php
 ************************************************************/

// 수정사유코드, 수정사유에 따라 1~6중 선택기재
// $Taxinvoice->modifyCode = '2';

// 원본세금계산서의 국세청 승인번호 기재
// $Taxinvoice->orgNTSConfirmNum = '';


/************************************************************
 *                       상세항목(품목) 정보
 ************************************************************/

$Taxinvoice->detailList = array();


$Sql2 = "
		select 
			A.*
		from TaxInvoiceItems A
		where TaxInvoiceID=:TaxInvoiceID 
		order by A.serialNum asc";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':TaxInvoiceID', $TaxInvoiceID);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

$ii=0;
while($Row2 = $Stmt2->fetch()) {

	$serialNum = $Row2["serialNum"];
	$purchaseDT = $Row2["purchaseDT"];
	$itemName = $Row2["itemName"];
	$spec = $Row2["spec"];
	$qty = $Row2["qty"];
	$unitCost = $Row2["unitCost"];
	$supplyCost = $Row2["supplyCost"];
	$tax = $Row2["tax"];
	$remark = $Row2["remark"];

	$unitCost = SpaceToZero($unitCost);
	$supplyCost = SpaceToZero($supplyCost);
	$tax = SpaceToZero($tax);


	$Taxinvoice->detailList[] = new TaxinvoiceDetail();
	$Taxinvoice->detailList[$ii]->serialNum = $serialNum;				      // [상세항목 배열이 있는 경우 필수] 일련번호 1~99까지 순차기재,
	$Taxinvoice->detailList[$ii]->purchaseDT = $purchaseDT;	  // 거래일자
	$Taxinvoice->detailList[$ii]->itemName = $itemName;	  	// 품명
	$Taxinvoice->detailList[$ii]->spec = $spec;				      // 규격
	$Taxinvoice->detailList[$ii]->qty = $qty;					        // 수량
	$Taxinvoice->detailList[$ii]->unitCost = $unitCost;		    // 단가
	$Taxinvoice->detailList[$ii]->supplyCost = $supplyCost;		  // 공급가액
	$Taxinvoice->detailList[$ii]->tax = $tax;				      // 세액
	$Taxinvoice->detailList[$ii]->remark = $remark;		    // 비고

	$ii++;
}
$Stmt2 = null;



/************************************************************
 *                      추가담당자 정보
 * - 세금계산서 발행안내 메일을 수신받을 공급받는자 담당자가 다수인 경우
 * 추가 담당자 정보를 등록하여 발행안내메일을 다수에게 전송할 수 있습니다. (최대 5명)
 ************************************************************/

if (trim($invoiceeEmail2)=="" || trim($invoiceeContactName2)==""){
	//추가담당자가 없으면 추가하지 않는다.
}else{

	$Taxinvoice->addContactList = array();

	$Taxinvoice->addContactList[] = new TaxinvoiceAddContact();
	$Taxinvoice->addContactList[0]->serialNum = 1;				        // 일련번호 1부터 순차기재
	$Taxinvoice->addContactList[0]->email = $invoiceeEmail2;	    // 이메일주소
	$Taxinvoice->addContactList[0]->contactName	= $invoiceeContactName2;		// 담당자명

	//$Taxinvoice->addContactList[] = new TaxinvoiceAddContact();
	//$Taxinvoice->addContactList[1]->serialNum = 2;			        	// 일련번호 1부터 순차기재
	//$Taxinvoice->addContactList[1]->email = 'goonglee@naver.com';	    // 이메일주소
	//$Taxinvoice->addContactList[1]->contactName	= '홍길동';		  // 담당자명

}

$ntsConfirmNum = "";
try {
	$result = $TaxinvoiceService->RegistIssue($invoicerCorpNum, $Taxinvoice, $invoicerUserID,
		$writeSpecification, $forceIssue, $memo, $emailSubject, $dealInvoiceMgtKey);
	$code = $result->code;
	$message = $result->message;
	$ntsConfirmNum = $result->ntsConfirmNum;
}
catch(PopbillException $pe) {
	$code = $pe->getCode();
	$message = $pe->getMessage();
}


$Sql2 = " update TaxInvoices set ";
	$Sql2 .= " code = :code, ";
	$Sql2 .= " message = :message, ";
	$Sql2 .= " ntsConfirmNum = :ntsConfirmNum, ";
	$Sql2 .= " TaxInvoiceModiDateTime = now() ";
$Sql2 .= " where TaxInvoiceID = :TaxInvoiceID ";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':code', $code);
$Stmt2->bindParam(':message', $message);
$Stmt2->bindParam(':ntsConfirmNum', $ntsConfirmNum);
$Stmt2->bindParam(':TaxInvoiceID', $TaxInvoiceID);
$Stmt2->execute();
$Stmt2 = null;






//==============================================================================================

function SpaceToNull($n){
	
	$n=trim($n);
	if ($n==""){
		$n=null;
	}

	return $n;

}

function SpaceToZero($n){
	
	$n=trim($n);
	if ($n=="" || $n==null){
		$n=0;
	}

	return $n;

}

//==============================================================================================
?>
<body>
<div id="content">
	<p class="heading1">Response</p>
	<br/>
	<fieldset class="fieldset1">
		<legend>전자세금계산서 즉시발행</legend>
		<ul>
			<li>응답코드 (code) : <?php echo $code ?></li>
			<li>응답메시지 (message) : <?php echo $message ?></li>
  <?php
	if ( isset($ntsConfirmNum) ) {
  ?>
	<li>국세청승인번호 (ntsConfirmNum) : <?php echo $ntsConfirmNum ?></li>
  <?php
	}
  ?>
		</ul>
	</fieldset>
 </div>

<?if ($FromPage=="lmsb2b_manual"){//lms b2b 수동발행?>
	<script>
	parent.location.reload();
	</script>
<?}else if ($FromPage=="lmsb2b_auto"){//lms b2b 자동발행?>
	<script>
	location.href = "../../lms/class_order_renew_center_form.php?SearchCenterID=<?=$SearchCenterID?>&SearchYear=<?=$SearchYear?>&SearchMonth=<?=$SearchMonth?>"
	</script>
<?}else{?>

<?}?>
</body>
</html>
<?
include_once('../../includes/dbclose.php');
?>
