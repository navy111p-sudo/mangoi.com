<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');

$ClassOrderEndDateLogFileName = $_SERVER["PHP_SELF"];

//'==================================================================================='
//' PC 결제결과
//'==================================================================================='
$site_cd          = isset($_REQUEST["site_cd"]) ? $_REQUEST["site_cd"] : "";      //' 사이트코드
$req_tx           = isset($_REQUEST["req_tx"]) ? $_REQUEST["req_tx"] : "";      //' 요청 구분(승인/취소)
$use_pay_method   = isset($_REQUEST["use_pay_method"]) ? $_REQUEST["use_pay_method"] : "";      //' 사용 결제 수단
$bSucc            = isset($_REQUEST["bSucc"]) ? $_REQUEST["bSucc"] : "";      //' 업체 DB 정상처리 완료 여부
$bSucces_cd       = isset($_REQUEST["bSucces_cd"]) ? $_REQUEST["bSucces_cd"] : "";      //' 결과코드
$res_cd           = isset($_REQUEST["res_cd"]) ? $_REQUEST["res_cd"] : ""; //결과코드
$res_msg          = isset($_REQUEST["res_msg"]) ? $_REQUEST["res_msg"] : "";      //' 결과메시지
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
if ( substr($card_name,0,2)=="BC" ){
	$card_name = "BC";//이도윤 오류 - 카드명이 깨져서 들어옴
}else{
	$card_name = iconv('EUC-KR', 'UTF-8', $card_name);
}
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

// 페이지 호출 여부 테스트 - 200407 끝나고 삭제할 것
$Sql = "
	insert into ClassOrderPays_Logs
	(ClassOrderPayNumber, ClassOrderPayLogs, ClassOrderPayLogs2)
	values
	(:PayTradenum, 'test for call', 'order_pay_result.php')
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':PayTradenum', $PayTradenum);
$Stmt->execute();
$Stmt = null;

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
	
	$ClassOrderPayUseCashPaymentType = 3;

	$ClassOrderPayPgFeeRatio = 0;
	$ClassOrderPayPgFeePrice = $OnlineSitePgVBankFeePrice;

}else if ($use_pay_method=="100000000000"){//신용카드

	$ClassOrderPayUseCashPaymentType = 1;

	$ClassOrderPayPgFeeRatio = $OnlineSitePgCardFeeRatio;
	$ClassOrderPayPgFeePrice = 0;

}else if ($use_pay_method=="010000000000"){//계좌이체
	
	$ClassOrderPayUseCashPaymentType = 2;
	
	if ($PayMny<=10000){//1만원 이하
		 $ClassOrderPayPgFeeRatio = 0;
		 $ClassOrderPayPgFeePrice = $OnlineSitePgDirectFeePrice;
	}else{
		 $ClassOrderPayPgFeeRatio = $OnlineSitePgDirectFeeRatio;
		 $ClassOrderPayPgFeePrice = 0;
	}


}else if ($use_pay_method=="000100000000"){//포인트 - 사용안함
	$ClassOrderPayUseCashPaymentType = 11;
}else if ($use_pay_method=="000010000000"){//휴대폰 - 사용안함
	$ClassOrderPayUseCashPaymentType = 12;
}else if ($use_pay_method=="000000001000"){//상품권 - 사용안함
	$ClassOrderPayUseCashPaymentType = 13;
}

$ChClassOrderState = 0;

$Sql = "select * from ClassOrderPays where ClassOrderPayNumber=:OrderNumPay and PayResultCd='0000' ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':OrderNumPay', $PayTradenum);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$PrePayClassOrderPayID = $Row["ClassOrderPayID"];


if (!$PrePayClassOrderPayID){

	$Sql = "
		insert into ClassOrderPays_Logs
		(ClassOrderPayNumber, ClassOrderPayLogs, ClassOrderPayLogs2)
		values
		(:PayTradenum, 'test for call 2', 'order_pay_result.php')
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':PayTradenum', $PayTradenum);
	$Stmt->execute();
	$Stmt = null;


	// 로그 남기기 =======================
	$Sql_Log = " update ClassOrderPays set ";
		
		
		if ($PayResultCd == "0000"){
			if ($use_pay_method=="001000000000"){//가상계좌
				$Sql_Log .= " ClassOrderPayProgress = 11, ";
			}else{
				$ChClassOrderState = 1;//ClassOrderState 변경해준다.
				$Sql_Log .= " ClassOrderPayProgress = 21, ";
				$Sql_Log .= " ClassOrderPayPaymentDateTime = now(), ";
			}
			$Sql_Log .= " ClassOrderPayUseCashPaymentType = '$ClassOrderPayUseCashPaymentType', ";

			$Sql_Log .= " ClassOrderPayPgFeeRatio = '$ClassOrderPayPgFeeRatio', ";
			$Sql_Log .= " ClassOrderPayPgFeePrice = '$ClassOrderPayPgFeePrice', ";
		
		}
		


		//kcp ==============
		$Sql_Log .= " site_cd = '$site_cd', ";
		$Sql_Log .= " req_tx = '$req_tx', ";
		$Sql_Log .= " use_pay_method = '$use_pay_method', ";
		$Sql_Log .= " bSucc = '$bSucc', ";
		$Sql_Log .= " bSucces_cd = '$bSucces_cd', ";
		$Sql_Log .= " res_cd = '$res_cd', ";
		$Sql_Log .= " res_msg = '$res_msg', ";
		$Sql_Log .= " res_msg_bsucc = '$res_msg_bsucc', ";
		$Sql_Log .= " amount = '$amount', ";
		$Sql_Log .= " ordr_idxx = '$ordr_idxx', ";
		$Sql_Log .= " tno = '$tno', ";
		$Sql_Log .= " good_name = '$good_name', ";
		$Sql_Log .= " buyr_name = '$buyr_name', ";
		$Sql_Log .= " buyr_tel1 = '$buyr_tel1', ";
		$Sql_Log .= " buyr_tel2 = '$buyr_tel2', ";
		$Sql_Log .= " buyr_mail = '$buyr_mail', ";
		$Sql_Log .= " pnt_issue = '$pnt_issue', ";
		$Sql_Log .= " app_time = '$app_time', ";
		$Sql_Log .= " card_cd = '$card_cd', ";
		$Sql_Log .= " card_name = '$card_name', ";
		$Sql_Log .= " noinf = '$noinf', ";
		$Sql_Log .= " quota = '$quota', ";
		$Sql_Log .= " app_no = '$app_no', ";
		$Sql_Log .= " bank_name = '$bank_name', ";
		$Sql_Log .= " bank_code = '$bank_code', ";
		$Sql_Log .= " bankname = '$bankname', ";
		$Sql_Log .= " depositor = '$depositor', ";
		$Sql_Log .= " account = '$account', ";
		$Sql_Log .= " va_date = '$va_date', ";
		$Sql_Log .= " cash_yn = '$cash_yn', ";
		$Sql_Log .= " cash_authno = '$cash_authno', ";
		$Sql_Log .= " cash_tr_code = '$cash_tr_code', ";
		$Sql_Log .= " cash_id_info = '$cash_id_info', ";
		$Sql_Log .= " cash_no = '$cash_no', ";
		$Sql_Log .= " pay_homeurl = '$pay_homeurl', ";
		$Sql_Log .= " PayMethod = '$PayMethod', ";
		$Sql_Log .= " PayCustName = '$PayCustName', ";
		$Sql_Log .= " PayGoods = '$PayGoods', ";
		$Sql_Log .= " PayMny = '$PayMny', ";
		$Sql_Log .= " OrderNumPay = '$PayTradenum', ";
		$Sql_Log .= " PayResultCd = '$PayResultCd', ";
		$Sql_Log .= " PayResultMsg = '$PayResultMsg', ";
		$Sql_Log .= " PayReTrno = '$PayReTrno', ";
		$Sql_Log .= " PayReNum = '$PayReNum', ";
		$Sql_Log .= " PayReTime = '$PayReTime', ";
		$Sql_Log .= " PayCardCD = '$PayCardCD', ";
		$Sql_Log .= " PayCard = '$PayCard', ";
		$Sql_Log .= " PayDivMon = '$PayDivMon', ";
		$Sql_Log .= " PayBankCD = '$PayBankCD', ";
		$Sql_Log .= " PayBank = '$PayBank', ";
		$Sql_Log .= " PayCashYN = '$PayCashYN', ";
		$Sql_Log .= " PayReqURL = '$PayReqURL', ";	
		//kcp ==============
		
		
		$Sql_Log .= " ClassOrderPayDateTime = now(), ";
		$Sql_Log .= " ClassOrderPayModiDateTime = now() ";
	$Sql_Log .= " where ClassOrderPayNumber = '$PayTradenum' ";

	$Sql = "
		insert into ClassOrderPays_Logs 
		(ClassOrderPayNumber, ClassOrderPayLogs)
		values
		(:PayTradenum, :Sql_Log)
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':PayTradenum', $PayTradenum);
	$Stmt->bindParam(':Sql_Log', $Sql_Log);
	$Stmt->execute();
	$Stmt = null;
	// =======================

	$Sql = " update ClassOrderPays set ";
		
		
		if ($PayResultCd == "0000"){
			if ($use_pay_method=="001000000000"){//가상계좌
				$Sql .= " ClassOrderPayProgress = 11, ";
			}else{
				$ChClassOrderState = 1;//ClassOrderState 변경해준다.
				$Sql .= " ClassOrderPayProgress = 21, ";
				$Sql .= " ClassOrderPayPaymentDateTime = now(), ";
			}
			$Sql .= " ClassOrderPayUseCashPaymentType = $ClassOrderPayUseCashPaymentType, ";

			$Sql .= " ClassOrderPayPgFeeRatio = :ClassOrderPayPgFeeRatio, ";
			$Sql .= " ClassOrderPayPgFeePrice = :ClassOrderPayPgFeePrice, ";
		
		}
		

		$Sql .= " LastUpdateUrl = 'order_pay_result.php', ";

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
		$Sql .= " ClassOrderPayLogs = :ClassOrderPayLogs, ";
		
		
		$Sql .= " ClassOrderPayDateTime = now(), ";
		$Sql .= " ClassOrderPayModiDateTime = now() ";
	$Sql .= " where ClassOrderPayNumber = :OrderNumPay ";

	$Stmt = $DbConn->prepare($Sql);

	if ($PayResultCd == "0000"){
		$Stmt->bindParam(':ClassOrderPayPgFeeRatio', $ClassOrderPayPgFeeRatio);
		$Stmt->bindParam(':ClassOrderPayPgFeePrice', $ClassOrderPayPgFeePrice);
	}


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
	$Stmt->bindParam(':ClassOrderPayLogs', $Sql_Log);
	$Stmt->execute();
	$Stmt = null;


	if ($PayResultCd == "0000"){

		$Sql = "select * from ClassOrderPays where ClassOrderPayNumber=:ClassOrderPayNumber";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ClassOrderPayNumber', $PayTradenum);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$ClassOrderPayID = $Row["ClassOrderPayID"];
		$ClassOrderPayUseCashPrice = $Row["ClassOrderPayUseCashPrice"];
		$ClassOrderPayPaymentMemberID = $Row["ClassOrderPayPaymentMemberID"];

		if ($ChClassOrderState==1){
			$Sql = "update ClassOrders set ClassOrderState=1 where ClassOrderID in ( select ClassOrderID from ClassOrderPayDetails where ClassOrderPayID=:ClassOrderPayID )";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
			$Stmt->execute();
			$Stmt = null;
		}

		$Sql = "update ClassOrders set ClassOrderPayID=:ClassOrderPayID where ClassOrderID in ( select ClassOrderID from ClassOrderPayDetails where ClassOrderPayID=:ClassOrderPayID )";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
		$Stmt->execute();
		$Stmt = null;


		
		//======================= 학습 종료일 설정 ======================================================
		$Sql = "select 
					A.ClassOrderID,
					B.ClassOrderPayTotalWeekCount,
					C.MemberID, 
					C.ClassOrderStartDate,
					C.ClassOrderEndDate,
					
					D.MemberPayType,
					E.CenterPayType,

					datediff(C.ClassOrderEndDate, now()) as DiffClassOrderEndDate

				from ClassOrderPayDetails A 
					inner join ClassOrderPayMonthNumbers B on A.ClassOrderPayMonthNumberID=B.ClassOrderPayMonthNumberID 
					inner join ClassOrders C on A.ClassOrderID=C.ClassOrderID 
					inner join Members D on C.MemberID=D.MemberID 
					inner join Centers E on D.CenterID=E.CenterID 
				where A.ClassOrderPayID=:ClassOrderPayID
				order by A.ClassOrderPayDetailID asc 
				";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($Row = $Stmt->fetch()) {

			$ClassOrderID = $Row["ClassOrderID"];
			$ClassOrderPayTotalWeekCount = $Row["ClassOrderPayTotalWeekCount"];
			$MemberID = $Row["MemberID"];
			$ClassOrderStartDate = $Row["ClassOrderStartDate"];
			$ClassOrderEndDate = $Row["ClassOrderEndDate"];
			$MemberPayType = $Row["MemberPayType"];
			$CenterPayType = $Row["CenterPayType"];
			$DiffClassOrderEndDate = $Row["DiffClassOrderEndDate"];


			if ($ClassOrderEndDate=="" || $ClassOrderEndDate=="0000-00-00"){// 첫 결제시====================================================================================
				
				$ExistStudyWeek[0] = 0;
				$ExistStudyWeek[1] = 0;
				$ExistStudyWeek[2] = 0;
				$ExistStudyWeek[3] = 0;
				$ExistStudyWeek[4] = 0;
				$ExistStudyWeek[5] = 0;
				$ExistStudyWeek[6] = 0;

				$Sql2 = " 
						select 
							A.*
						from ClassOrderSlots A 
						where A.ClassOrderID=$ClassOrderID and A.ClassOrderSlotType=1 and A.ClassOrderSlotMaster=1 and A.ClassOrderSlotEndDate is null 
						order by A.StudyTimeWeek asc
				";
				$Stmt2 = $DbConn->prepare($Sql2);
				$Stmt2->execute();
				$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
				while($Row2 = $Stmt2->fetch()) {
					
					$StudyTimeWeek = $Row2["StudyTimeWeek"];
					$ExistStudyWeek[$StudyTimeWeek] = 1;

				}
				$Stmt2 = null;

				// 시작일 계산 하기 : 오늘 이후의 수업일을 찾기
				//$ClassOrderStartDateWeek = date("w", strtotime($ClassOrderStartDate));//지금 현재 ClassOrderStartDate 는 일요일임 
				$ClassOrderStartDateWeek = 6;//거꾸로 계산
				$SetLastDate = 0;

				$ListNum = 6;
				$NewClassOrderStartDate = "";
				while($SetLastDate==0){//다음 일요일 부터 거꾸로 찾아온다.
					
					if ( $ExistStudyWeek[$ClassOrderStartDateWeek]==1 && date('Ymd', strtotime($ClassOrderStartDate. ' +'.$ListNum.' day')) > date("Ymd") ){//오늘 이후인 요일이 있으면 세팅
						$NewClassOrderStartDate = date("Y-m-d", strtotime($ClassOrderStartDate. " + ".$ListNum." days"));
					}

					if ($ListNum==0){
						$SetLastDate=1;
					}
					
					$ListNum--;
					$ClassOrderStartDateWeek--;
				}

				if ($NewClassOrderStartDate==""){//선택한 요일이 모두 오늘보다 앞일경우

					$ClassOrderStartDateWeek = date("w", strtotime($ClassOrderStartDate));//지금 현재 ClassOrderStartDate 는 일요일임 
					$SetLastDate = 0;

					$ListNum = 0;
					while($SetLastDate==0){
						
						if ($ExistStudyWeek[$ClassOrderStartDateWeek]==1){
							$NewClassOrderStartDate = date("Y-m-d", strtotime($ClassOrderStartDate. " + ".($ListNum + 7)." days"));//첫번째 나오는 요일의 일주일 후
							$SetLastDate=1;
						}
						
						$ListNum++;
						$ClassOrderStartDateWeek++;
					}

				}
				// 시작일 계산 하기 : 오늘 이후의 수업일을 찾기


				$ClassOrderPayTotalDayCount = ( $ClassOrderPayTotalWeekCount * 7 ) - 1;//시작일 기준일때는 1을 빼줌
			
				$NewClassOrderEndDate = date("Y-m-d", strtotime(substr($NewClassOrderStartDate,0,10). " + ".$ClassOrderPayTotalDayCount." days"));


				$Sql2 = "update ClassOrders set LastClassOrderEndDate=ClassOrderEndDate, LastClassOrderEndDateByPay=ClassOrderEndDate, ClassOrderStartDate=:ClassOrderStartDate, ClassOrderEndDate=:ClassOrderEndDate, ClassOrderModiDateTime=now() where ClassOrderID=$ClassOrderID";
				$Stmt2 = $DbConn->prepare($Sql2);
				$Stmt2->bindParam(':ClassOrderEndDate', $NewClassOrderEndDate);
				$Stmt2->bindParam(':ClassOrderStartDate', $NewClassOrderStartDate);
				$Stmt2->execute();
				$Stmt2 = null;

				//종료일 로그 남기기 =======================================
				$ClassOrderEndDateLogFileQueryNum = 1;
				$Sql_EndDateLog = " insert into ClassOrderEndDateLogs ( ";
					$Sql_EndDateLog .= " ClassOrderID, ";
					$Sql_EndDateLog .= " ClassOrderEndDateLogType, ";
					$Sql_EndDateLog .= " ClassOrderEndDate, ";
					$Sql_EndDateLog .= " ClassOrderEndDateLogFileName, ";
					$Sql_EndDateLog .= " ClassOrderEndDateLogFileQueryNum, ";
					$Sql_EndDateLog .= " ClassOrderEndDateLogRegDateTime ";
				$Sql_EndDateLog .= " ) values ( ";
					$Sql_EndDateLog .= " :ClassOrderID, ";
					$Sql_EndDateLog .= " '결제에 의한 종료일 변경', ";
					$Sql_EndDateLog .= " :ClassOrderEndDate, ";
					$Sql_EndDateLog .= " :ClassOrderEndDateLogFileName, ";
					$Sql_EndDateLog .= " :ClassOrderEndDateLogFileQueryNum, ";
					$Sql_EndDateLog .= " now() ";
				$Sql_EndDateLog .= " ) ";
				$Stmt_EndDateLog = $DbConn->prepare($Sql_EndDateLog);
				$Stmt_EndDateLog->bindParam(':ClassOrderID', $ClassOrderID);
				$Stmt_EndDateLog->bindParam(':ClassOrderEndDate', $NewClassOrderEndDate);
				$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileName', $ClassOrderEndDateLogFileName);
				$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileQueryNum', $ClassOrderEndDateLogFileQueryNum);
				$Stmt_EndDateLog->execute();
				$Stmt_EndDateLog = null;
				//종료일 로그 남기기 =======================================


				$ClassOrderPayStartDate = $NewClassOrderStartDate;//이변결제의 수업 시작날짜


			}else{//연장 결제시 ==============================================================================================================================

				
				
				if ($DiffClassOrderEndDate>=0){//종료일 이전 결제====================================================================================

					$ClassOrderPayTotalDayCount = $ClassOrderPayTotalWeekCount * 7;//종료일 기준일때는 1을 빼주지 않음

					$NewClassOrderEndDate = date("Y-m-d", strtotime(substr($ClassOrderEndDate,0,10). " + ".$ClassOrderPayTotalDayCount." days"));

					$ClassOrderPayStartDate = date("Y-m-d", strtotime(substr($ClassOrderEndDate,0,10). " + 1 days"));//이변결제의 수업 시작날짜
				
				}else{//종료일 이후 결제====================================================================================
					
					
					$ExistStudyWeek[0] = 0;
					$ExistStudyWeek[1] = 0;
					$ExistStudyWeek[2] = 0;
					$ExistStudyWeek[3] = 0;
					$ExistStudyWeek[4] = 0;
					$ExistStudyWeek[5] = 0;
					$ExistStudyWeek[6] = 0;

					$Sql2 = " 
							select 
								A.*
							from ClassOrderSlots A 
							where A.ClassOrderID=$ClassOrderID and A.ClassOrderSlotType=1 and A.ClassOrderSlotMaster=1 and A.ClassOrderSlotEndDate is null 
							order by A.StudyTimeWeek asc
					";
					$Stmt2 = $DbConn->prepare($Sql2);
					$Stmt2->execute();
					$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
					while($Row2 = $Stmt2->fetch()) {
						
						$StudyTimeWeek = $Row2["StudyTimeWeek"];
						$ExistStudyWeek[$StudyTimeWeek] = 1;

					}
					$Stmt2 = null;


					$ClassOrderStartDateWeek = date("w", strtotime(date("Y-m-d")));//오늘의 요일
					$SetLastDate = 0;

					$ListNum = 0;
					while($SetLastDate==0){
						
						if ($ClassOrderStartDateWeek==7){//일요일이면 0으로 초기화
							$ClassOrderStartDateWeek = 0;
						}
						
						if ($ExistStudyWeek[$ClassOrderStartDateWeek]==1){
							$NewClassOrderStartDate = date("Y-m-d", strtotime(date("Y-m-d"). " + ".$ListNum." days"));
							$SetLastDate=1;
						}
						
						$ListNum++;
						$ClassOrderStartDateWeek++;
					}

					$ClassOrderPayTotalDayCount = ( $ClassOrderPayTotalWeekCount * 7 ) - 1;//시작일 기준일때는 1을 빼줌
				
					$NewClassOrderEndDate = date("Y-m-d", strtotime(substr($NewClassOrderStartDate,0,10). " + ".$ClassOrderPayTotalDayCount." days"));	
					
					$ClassOrderPayStartDate = $NewClassOrderStartDate;//이변결제의 수업 시작날짜
					
				}


				$Sql2 = "update ClassOrders set LastClassOrderEndDate=ClassOrderEndDate, LastClassOrderEndDateByPay=ClassOrderEndDate, ClassOrderEndDate=:ClassOrderEndDate, ClassOrderModiDateTime=now() where ClassOrderID=$ClassOrderID";
				$Stmt2 = $DbConn->prepare($Sql2);
				$Stmt2->bindParam(':ClassOrderEndDate', $NewClassOrderEndDate);
				$Stmt2->execute();
				$Stmt2 = null;

				//종료일 로그 남기기 =======================================
				$ClassOrderEndDateLogFileQueryNum = 2;
				$Sql_EndDateLog = " insert into ClassOrderEndDateLogs ( ";
					$Sql_EndDateLog .= " ClassOrderID, ";
					$Sql_EndDateLog .= " ClassOrderEndDateLogType, ";
					$Sql_EndDateLog .= " ClassOrderEndDate, ";
					$Sql_EndDateLog .= " ClassOrderEndDateLogFileName, ";
					$Sql_EndDateLog .= " ClassOrderEndDateLogFileQueryNum, ";
					$Sql_EndDateLog .= " ClassOrderEndDateLogRegDateTime ";
				$Sql_EndDateLog .= " ) values ( ";
					$Sql_EndDateLog .= " :ClassOrderID, ";
					$Sql_EndDateLog .= " '결제에 의한 종료일 변경', ";
					$Sql_EndDateLog .= " :ClassOrderEndDate, ";
					$Sql_EndDateLog .= " :ClassOrderEndDateLogFileName, ";
					$Sql_EndDateLog .= " :ClassOrderEndDateLogFileQueryNum, ";
					$Sql_EndDateLog .= " now() ";
				$Sql_EndDateLog .= " ) ";
				$Stmt_EndDateLog = $DbConn->prepare($Sql_EndDateLog);
				$Stmt_EndDateLog->bindParam(':ClassOrderID', $ClassOrderID);
				$Stmt_EndDateLog->bindParam(':ClassOrderEndDate', $NewClassOrderEndDate);
				$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileName', $ClassOrderEndDateLogFileName);
				$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileQueryNum', $ClassOrderEndDateLogFileQueryNum);
				$Stmt_EndDateLog->execute();
				$Stmt_EndDateLog = null;
				//종료일 로그 남기기 =======================================

			}
			

		}
		$Stmt = null;

		//var_dump($ClassOrderPayStartDate);
		//var_dump($PayTradenum);
		$Sql2 = "update ClassOrderPays set ClassOrderPayStartDate=:ClassOrderPayStartDate where ClassOrderPayNumber=:ClassOrderPayNumber";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':ClassOrderPayStartDate', $ClassOrderPayStartDate);
		$Stmt2->bindParam(':ClassOrderPayNumber', $PayTradenum);
		$Stmt2->execute();
		$Stmt2 = null;	
		//======================= 학습 종료일 설정 ======================================================


		//================= 포인트 ======================
		$OnlineSitePaymentPoint = round($ClassOrderPayUseCashPrice * ($OnlineSitePaymentPointRatio / 100));

		/*
		$Sql = "select A.MemberPointID from MemberPoints A where A.MemberPointTypeID=3 and A.MemberID=:MemberID and A.MemberPointState=1 and A.RootOrderID=:RootOrderID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $PointMemberID);
		$Stmt->bindParam(':RootOrderID', $ClassOrderPayID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$MemberPointID = $Row["MemberPointID"];

		if (!$MemberPointID){
			InsertPointWithRootOrderID(3, 0, $PointMemberID, "수강신청(웹)", "수강신청(웹)" ,$OnlineSitePaymentPoint, $ClassOrderPayID);
		}
		*/

		//================= 포인트 ======================


	}
}

include_once('./includes/dbclose.php');

if (substr($PayTradenum,0,2)=="ML"){
	header("Location: /lms/class_order_list.php?type=21"); 
	exit;
}else{
	header("Location: mypage_payment_list.php"); 
	exit;
}
?> 