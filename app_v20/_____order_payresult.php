<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

//'==================================================================================='
//' PC 결제결과
//'==================================================================================='
$site_cd          = isset($_REQUEST["site_cd"]) ? $_REQUEST["site_cd"] : "";      //' 사이트코드
$req_tx           = isset($_REQUEST["req_tx"]) ? $_REQUEST["req_tx"] : "";      //' 요청 구분(승인/취소)
$use_pay_method   = isset($_REQUEST["use_pay_method"]) ? $_REQUEST["use_pay_method"] : "";      //' 사용 결제 수단
$bSucc            = isset($_REQUEST["bSucc"]) ? $_REQUEST["bSucc"] : "";      //' 업체 DB 정상처리 완료 여부
$bSucces_cd       = isset($_REQUEST["bSucces_cd"]) ? $_REQUEST["bSucces_cd"] : "";      //' 결과코드
$res_cd           = isset($_REQUEST["res_cd"]) ? $_REQUEST["res_cd"] : ""; //결과코드
$res_msg          = isset($_REQUEST["CenterPricePerTime"]) ? $_REQUEST["CenterPricePerTime"] : "";      //' 결과메시지
$res_msg_bsucc    = "";
$amount           = isset($_REQUEST["amount"]) ? $_REQUEST["amount"] : "";      //' KCP 실제 거래 금액
$ordr_idxx        = isset($_REQUEST["ordr_idxx"]) ? $_REQUEST["ordr_idxx"] : "";      //' 주문번호
$tno              = isset($_REQUEST["tno"]) ? $_REQUEST["tno"] : "";      //' KCP 거래번호
$good_name        = isset($_REQUEST["good_name"]) ? $_REQUEST["good_name"] : "";      //' 상품명
$buyr_name        = isset($_REQUEST["buyr_name"]) ? $_REQUEST["buyr_name"] : "";      //' 구매자명
$buyr_tel1        = isset($_REQUEST["buyr_tel1"]) ? $_REQUEST["buyr_tel1"] : "";      //' 구매자 전화번호
$buyr_tel2        = isset($_REQUEST["buyr_tel2"]) ? $_REQUEST["buyr_tel2"] : "";      //' 구매자 휴대폰번호
$buyr_mail        = isset($_REQUEST["buyr_mail"]) ? $_REQUEST["buyr_mail"] : "";      //' 구매자 E-Mail
$pnt_issue        = isset($_REQUEST["pnt_issue"]) ? $_REQUEST["pnt_issue"] : "";      //' 포인트 서비스사
$app_time         = isset($_REQUEST["app_time"]) ? $_REQUEST["app_time"] : "";      //' 승인시간 (공통)
$card_cd          = isset($_REQUEST["card_cd"]) ? $_REQUEST["card_cd"] : "";      //' 카드코드
$card_name        = isset($_REQUEST["card_name"]) ? $_REQUEST["card_name"] : "";      //' 카드명
$noinf            = isset($_REQUEST["noinf"]) ? $_REQUEST["noinf"] : "";      //' 무이자 여부
$quota            = isset($_REQUEST["quota"]) ? $_REQUEST["quota"] : "";      //' 할부개월
$app_no           = isset($_REQUEST["app_no"]) ? $_REQUEST["app_no"] : "";      //' 승인번호
$bank_name        = isset($_REQUEST["bank_name"]) ? $_REQUEST["bank_name"] : "";      //' 은행명
$bank_code        = isset($_REQUEST["bank_code"]) ? $_REQUEST["bank_code"] : "";      //' 은행코드
$bankname         = isset($_REQUEST["bankname"]) ? $_REQUEST["bankname"] : "";      //' 입금할 은행
$depositor        = isset($_REQUEST["depositor"]) ? $_REQUEST["depositor"] : "";      //' 입금할 계좌 예금주
$account          = isset($_REQUEST["account"]) ? $_REQUEST["account"] : "";      //' 입금할 계좌 번호
$va_date          = isset($_REQUEST["va_date"]) ? $_REQUEST["va_date"] : "";      //' 가상계좌 입금마감시간
$cash_yn          = isset($_REQUEST["cash_yn"]) ? $_REQUEST["cash_yn"] : "";      //' 현금영수증 등록 여부
$cash_authno      = isset($_REQUEST["cash_authno"]) ? $_REQUEST["cash_authno"] : "";      //' 현금영수증 승인 번호
$cash_tr_code     = isset($_REQUEST["cash_tr_code"]) ? $_REQUEST["cash_tr_code"] : "";      //' 현금영수증 발행 구분
$cash_id_info     = isset($_REQUEST["cash_id_info"]) ? $_REQUEST["cash_id_info"] : "";      //' 현금영수증 등록 번호
$cash_no          = isset($_REQUEST["cash_no"]) ? $_REQUEST["cash_no"] : "";      //' 현금영수증 거래 번호    
$pay_homeurl      = isset($_REQUEST["pay_homeurl"]) ? $_REQUEST["pay_homeurl"] : "";      //' 홈페이지
//'-----------------------------------------------------------------------------------'

$res_msg   = iconv('EUC-KR', 'UTF-8', $res_msg);
$card_name = iconv('EUC-KR', 'UTF-8', $card_name);
$good_name = iconv('EUC-KR', 'UTF-8', $good_name);
$buyr_name = iconv('EUC-KR', 'UTF-8', $buyr_name);
$bank_name = iconv('EUC-KR', 'UTF-8', $bank_name);
$bankname  = iconv('EUC-KR', 'UTF-8', $bankname);
$depositor = iconv('EUC-KR', 'UTF-8', $depositor);

//'==================================================================================='
//' MOBILE 결제결과
//'==================================================================================='
$PayMethod        = isset($_REQUEST["PayMethod"]) ? $_REQUEST["PayMethod"] : "";	   //' 모방일 결제타입 : CARD, BANK
$PayCustName      = isset($_REQUEST["PayCustName"]) ? $_REQUEST["PayCustName"] : "";      //' 구매자명
$PayGoods         = isset($_REQUEST["PayGoods"]) ? $_REQUEST["PayGoods"] : "";      //' 구매상품명
$PayMny           = isset($_REQUEST["PayMny"]) ? $_REQUEST["PayMny"] : "";      //' 구매결제금액
//$OrderNumPay      = isset($_REQUEST["OrderNumPay"]) ? $_REQUEST["OrderNumPay"] : "";      //' 주문번호
$PayTradenum      = isset($_REQUEST["PayTradenum"]) ? $_REQUEST["PayTradenum"] : "";      //' 주문번호
$PayResultCd      = isset($_REQUEST["PayResultCd"]) ? $_REQUEST["PayResultCd"] : "";      //' 결과코드(성공 : 0000)
$PayResultMsg     = isset($_REQUEST["PayResultMsg"]) ? $_REQUEST["PayResultMsg"] : "";      //' 결제결과메시지
$PayReTrno        = isset($_REQUEST["PayReTrno"]) ? $_REQUEST["PayReTrno"] : "";      //' PG사 거래번호(취소시 필요한번호)
$PayReNum         = isset($_REQUEST["PayReNum"]) ? $_REQUEST["PayReNum"] : "";      //' PG사 거래승인번호
$PayReTime        = isset($_REQUEST["PayReTime"]) ? $_REQUEST["PayReTime"] : "";      //' PG사 거래일시
$PayCardCD        = isset($_REQUEST["PayCardCD"]) ? $_REQUEST["PayCardCD"] : "";      //' 카드사 코드
$PayCard          = isset($_REQUEST["PayCard"]) ? $_REQUEST["PayCard"] : "";      //' 카드사명
$PayDivMon        = isset($_REQUEST["PayDivMon"]) ? $_REQUEST["PayDivMon"] : "";      //' 할부 개월수
$PayBankCD        = isset($_REQUEST["PayBankCD"]) ? $_REQUEST["PayBankCD"] : "";      //' 결제은행코드
$PayBank          = isset($_REQUEST["PayBank"]) ? $_REQUEST["PayBank"] : "";      //' 결제은행
$PayCashYN        = isset($_REQUEST["PayCashYN"]) ? $_REQUEST["PayCashYN"] : "";      //' 현금영수증사용여부
$PayReqURL        = isset($_REQUEST["PayReqURL"]) ? $_REQUEST["PayReqURL"] : "";      //' ReqURL
//'-----------------------------------------------------------------------------------'
if ($PayTradenum == ""){ 
      $PayTradenum = $ordr_idxx;
} 
//' 결제결과 코드
If ($PayResultCd == ""){ 
      $PayResultCd = $res_cd;
}
//' 결제결과 메시지
If ($PayResultMsg == ""){ 
      $PayResultMsg = $res_msg;
}
//' 구매자명
If ($PayCustName == ""){ 
      $PayCustName = $buyr_name;
}
//' 상품명
If ($PayGoods == ""){ 
      $PayGoods = $good_name;
}
//' 결제금액
If ($PayMny == ""){ 
      $PayMny = $amount;
} 

if ($use_pay_method=="001000000000"){//가상계좌
	$UseCashPaymentType = 3;
}else if ($use_pay_method=="100000000000"){//신용카드
	$UseCashPaymentType = 1;
}else if ($use_pay_method=="010000000000"){//계좌이체
	$UseCashPaymentType = 2;
}else if ($use_pay_method=="000100000000"){//포인트
	$UseCashPaymentType = 9;
}else if ($use_pay_method=="000010000000"){//휴대폰
	$UseCashPaymentType = 9;
}else if ($use_pay_method=="000000001000"){//상품권
	$UseCashPaymentType = 9;
}


$Sql = " update ClassOrders set ";
	

	if ($PayResultCd == "0000"){
		if ($use_pay_method=="001000000000"){//가상계좌
			$Sql .= " OrderProgress = 11, ";
		}else{
			$Sql .= " OrderProgress = 21, ";
			$Sql .= " ClassOrderPaymentDateTime = now(), ";
		}
		$Sql .= " UseCashPaymentType = $UseCashPaymentType, ";
	}
	
	//kcp ==============
	$Sql .= " site_cd = :site_cd, ";
	$Sql .= " req_tx = :req_tx, ";
	$Sql .= " use_pay_method = :use_pay_method, ";
	$Sql .= " bSucc = :bSucc, ";
	$Sql .= " bSucces_cd = :bSucces_cd, ";
	$Sql .= " res_cd = :res_cd, ";
	$Sql .= " res_msg = :res_msg, ";
	$Sql .= " res_msg_bsucc = :res_msg_bsucc, ";
	$Sql .= " amount = :amount, ";
	$Sql .= " ordr_idxx = :ordr_idxx, ";
	$Sql .= " tno = :tno, ";
	$Sql .= " good_name = :good_name, ";
	$Sql .= " buyr_name = :buyr_name, ";
	$Sql .= " buyr_tel1 = :buyr_tel1, ";
	$Sql .= " buyr_tel2 = :buyr_tel2, ";
	$Sql .= " buyr_mail = :buyr_mail, ";
	$Sql .= " pnt_issue = :pnt_issue, ";
	$Sql .= " app_time = :app_time, ";
	$Sql .= " card_cd = :card_cd, ";
	$Sql .= " card_name = :card_name, ";
	$Sql .= " noinf = :noinf, ";
	$Sql .= " quota = :quota, ";
	$Sql .= " app_no = :app_no, ";
	$Sql .= " bank_name = :bank_name, ";
	$Sql .= " bank_code = :bank_code, ";
	$Sql .= " bankname = :bankname, ";
	$Sql .= " depositor = :depositor, ";
	$Sql .= " account = :account, ";
	$Sql .= " va_date = :va_date, ";
	$Sql .= " cash_yn = :cash_yn, ";
	$Sql .= " cash_authno = :cash_authno, ";
	$Sql .= " cash_tr_code = :cash_tr_code, ";
	$Sql .= " cash_id_info = :cash_id_info, ";
	$Sql .= " cash_no = :cash_no, ";
	$Sql .= " pay_homeurl = :pay_homeurl, ";
	$Sql .= " PayMethod = :PayMethod, ";
	$Sql .= " PayCustName = :PayCustName, ";
	$Sql .= " PayGoods = :PayGoods, ";
	$Sql .= " PayMny = :PayMny, ";
	$Sql .= " OrderNumPay = :OrderNumPay, ";
	$Sql .= " PayResultCd = :PayResultCd, ";
	$Sql .= " PayResultMsg = :PayResultMsg, ";
	$Sql .= " PayReTrno = :PayReTrno, ";
	$Sql .= " PayReNum = :PayReNum, ";
	$Sql .= " PayReTime = :PayReTime, ";
	$Sql .= " PayCardCD = :PayCardCD, ";
	$Sql .= " PayCard = :PayCard, ";
	$Sql .= " PayDivMon = :PayDivMon, ";
	$Sql .= " PayBankCD = :PayBankCD, ";
	$Sql .= " PayBank = :PayBank, ";
	$Sql .= " PayCashYN = :PayCashYN, ";
	$Sql .= " PayReqURL = :PayReqURL, ";	
	//kcp ==============
	
	
	
	$Sql .= " ClassOrderModiDateTime = now() ";
$Sql .= " where ClassOrderNumber = :OrderNumPay ";

$Stmt = $DbConn->prepare($Sql);

//kcp ==============
$Stmt->bindParam(':site_cd', $site_cd);
$Stmt->bindParam(':req_tx', $req_tx);
$Stmt->bindParam(':use_pay_method', $use_pay_method);
$Stmt->bindParam(':bSucc', $bSucc);
$Stmt->bindParam(':bSucces_cd', $bSucces_cd);
$Stmt->bindParam(':res_cd', $res_cd);
$Stmt->bindParam(':res_msg', $res_msg);
$Stmt->bindParam(':res_msg_bsucc', $res_msg_bsucc);
$Stmt->bindParam(':amount', $amount);
$Stmt->bindParam(':ordr_idxx', $ordr_idxx);
$Stmt->bindParam(':tno', $tno);
$Stmt->bindParam(':good_name', $good_name);
$Stmt->bindParam(':buyr_name', $buyr_name);
$Stmt->bindParam(':buyr_tel1', $buyr_tel1);
$Stmt->bindParam(':buyr_tel2', $buyr_tel2);
$Stmt->bindParam(':buyr_mail', $buyr_mail);
$Stmt->bindParam(':pnt_issue', $pnt_issue);
$Stmt->bindParam(':app_time', $app_time);
$Stmt->bindParam(':card_cd', $card_cd);
$Stmt->bindParam(':card_name', $card_name);
$Stmt->bindParam(':noinf', $noinf);
$Stmt->bindParam(':quota', $quota);
$Stmt->bindParam(':app_no', $app_no);
$Stmt->bindParam(':bank_name', $bank_name);
$Stmt->bindParam(':bank_code', $bank_code);
$Stmt->bindParam(':bankname', $bankname);
$Stmt->bindParam(':depositor', $depositor);
$Stmt->bindParam(':account', $account);
$Stmt->bindParam(':va_date', $va_date);
$Stmt->bindParam(':cash_yn', $cash_yn);
$Stmt->bindParam(':cash_authno', $cash_authno);
$Stmt->bindParam(':cash_tr_code', $cash_tr_code);
$Stmt->bindParam(':cash_id_info', $cash_id_info);
$Stmt->bindParam(':cash_no', $cash_no);
$Stmt->bindParam(':pay_homeurl', $pay_homeurl);
$Stmt->bindParam(':PayMethod', $PayMethod);
$Stmt->bindParam(':PayCustName', $PayCustName);
$Stmt->bindParam(':PayGoods', $PayGoods);
$Stmt->bindParam(':PayMny', $PayMny);
$Stmt->bindParam(':OrderNumPay', $PayTradenum);
$Stmt->bindParam(':PayResultCd', $PayResultCd);
$Stmt->bindParam(':PayResultMsg', $PayResultMsg);
$Stmt->bindParam(':PayReTrno', $PayReTrno);
$Stmt->bindParam(':PayReNum', $PayReNum);
$Stmt->bindParam(':PayReTime', $PayReTime);
$Stmt->bindParam(':PayCardCD', $PayCardCD);
$Stmt->bindParam(':PayCard', $PayCard);
$Stmt->bindParam(':PayDivMon', $PayDivMon);
$Stmt->bindParam(':PayBankCD', $PayBankCD);
$Stmt->bindParam(':PayBank', $PayBank);
$Stmt->bindParam(':PayCashYN', $PayCashYN);
$Stmt->bindParam(':PayReqURL', $PayReqURL);
//kcp ==============
$Stmt->execute();
$Stmt = null;

include_once('../includes/dbclose.php');

header("Location: student_list.php"); 
exit;
?>