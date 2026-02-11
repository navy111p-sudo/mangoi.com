<?php
include_once('../includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('../includes/common.php');

$ClassOrderEndDateLogFileName = $_SERVER["PHP_SELF"];

$SavedMoneyID = "";
$CenterID = "";


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

/*
if ( substr($card_name,0,2)=="BC" ){
	$card_name = "BC";//이도윤 오류 - 카드명이 깨져서 들어옴
}else{
	$card_name = iconv('EUC-KR', 'UTF-8', $card_name);
}
*/
$card_name = "";

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
	$PayUseCashPaymentType = 3;
}else if ($use_pay_method=="100000000000"){//신용카드
	$PayUseCashPaymentType = 1;
}else if ($use_pay_method=="010000000000"){//계좌이체
	$PayUseCashPaymentType = 2;
}else if ($use_pay_method=="000100000000"){//포인트 - 사용안함
	$PayUseCashPaymentType = 11;
}else if ($use_pay_method=="000010000000"){//휴대폰 - 사용안함
	$PayUseCashPaymentType = 12;
}else if ($use_pay_method=="000000001000"){//상품권 - 사용안함
	$PayUseCashPaymentType = 13;
}

$ChClassOrderState = 0;


// 비정상적인 결과를 걸러내기 위해 주문번호가 제대로 들어오는지 확인한다. 주문번호가 없으면 진행하지 않는다.
if ($ordr_idxx != ""){

	$Sql = "UPDATE SavedMoney set ";

	//결제가 정상적으로 이루어졌을때
	if ($PayResultCd == "0000"){
		if ($use_pay_method=="001000000000"){//가상계좌
			$Sql .= " PayProgress = 11,";
		}else{
			$ChClassOrderState = 1;//ClassOrderState 변경해준다.
			$Sql .= " PayProgress = 21,  
					SavedMoneyState = 1, ";
		}
		$Sql .= " PayUseCashPaymentType = $PayUseCashPaymentType,";
	}

	//kcp ==============
			
	$Sql_kcp = " site_cd = :site_cd, 
			req_tx = :req_tx, 
			use_pay_method = :use_pay_method, 
			bSucc = :bSucc, 
			bSucces_cd = :bSucces_cd, 
			res_cd = :res_cd, 
			res_msg = :res_msg, 
			res_msg_bsucc = :res_msg_bsucc, 
			amount = :amount, 
			ordr_idxx = :ordr_idxx, 
			tno = :tno, 
			good_name = :good_name, 
			buyr_name = :buyr_name, 
			buyr_tel1 = :buyr_tel1, 
			buyr_tel2 = :buyr_tel2,
			buyr_mail = :buyr_mail,
			pnt_issue = :pnt_issue,
			app_time = :app_time, 
			card_cd = :card_cd, 
			card_name = :card_name, 
			noinf = :noinf, 
			quota = :quota, 
			app_no = :app_no,
			bank_name = :bank_name,
			bank_code = :bank_code, 
			bankname = :bankname,
			depositor = :depositor,
			account = :account, 
			va_date = :va_date, 
			cash_yn = :cash_yn, 
			cash_authno = :cash_authno, 
			cash_tr_code = :cash_tr_code, 
			cash_id_info = :cash_id_info, 
			cash_no = :cash_no, 
			pay_homeurl = :pay_homeurl, 
			PayMethod = :PayMethod, 
			PayCustName = :PayCustName, 
			PayGoods = :PayGoods, 
			PayMny = :PayMny, 
			OrderNumPay = :OrderNumPay, 
			PayResultCd = :PayResultCd, 
			PayResultMsg = :PayResultMsg, 
			PayReTrno = :PayReTrno, 
			PayReNum = :PayReNum, 
			PayReTime = :PayReTime, 
			PayCardCD = :PayCardCD,
			PayCard = :PayCard, 
			PayDivMon = :PayDivMon, 
			PayBankCD = :PayBankCD, 
			PayBank = :PayBank, 
			PayCashYN = :PayCashYN, 
			PayReqURL = :PayReqURL, ";
			//kcp ==============
			
		$Sql .= $Sql_kcp;
		$Sql .= " SavedMoneyRegDateTime = now(), ";
		$Sql .= " SavedMoneyModiDateTime = now() ";
		$Sql .= " WHERE SavedMoneyPayNumber = :SavedMoneyPayNumber ";

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

		$Stmt->bindParam(':SavedMoneyPayNumber', $ordr_idxx);
		$Stmt->execute();
		$Stmt = null;


		//CenterID와 SavedMoney, RegMemberID를 가져온다.
		$Sql = "SELECT * from SavedMoney WHERE SavedMoneyPayNumber = :SavedMoneyPayNumber";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':SavedMoneyPayNumber', $ordr_idxx);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$CenterID = $Row["CenterID"];
		$SavedMoneyID = $Row["SavedMoneyID"];
		$RegMemberID = $Row["RegMemberID"];


		// 결제가 정상적으로 이루어졌을때는 ClassOrderPays 에도 
		// 결제내역을 추가하여 B2B결제내역에서 확인 가능하게 만들어 준다.
		if ($PayResultCd == "0000"){
			try {
				$Sql = "INSERT INTO ClassOrderPays 
							(ClassOrderPayNumber,
							ClassOrderPayType,
							CenterID,
							ClassOrderPayPaymentMemberID,
							ClassOrderPaySellingPrice,
							ClassOrderPayDiscountPrice,
							ClassOrderPayFreeTrialDiscountPrice,
							ClassOrderPayPaymentPrice,
							ClassOrderPayUseSavedMoneyPrice,
							ClassOrderPayUseCashPrice,
							ClassOrderPayUseCashPaymentType,
							ClassOrderPayB2bDifferencePrice,
							ClassOrderPayPgFeeRatio,
							ClassOrderPayProgress,
							ClassOrderPayDateTime,
							ClassOrderPayPaymentDateTime,
							ClassOrderPayRegDateTime,
							ClassOrderPayModiDateTime,
							ClassOrderPayPgFeePrice,
							site_cd, 
							req_tx, 
							use_pay_method , 
							bSucc, 
							bSucces_cd, 
							res_cd, 
							res_msg, 
							res_msg_bsucc, 
							amount, 
							ordr_idxx, 
							tno, 
							good_name, 
							buyr_name , 
							buyr_tel1 , 
							buyr_tel2 ,
							buyr_mail,
							pnt_issue,
							app_time , 
							card_cd, 
							card_name, 
							noinf, 
							quota, 
							app_no,
							bank_name,
							bank_code, 
							bankname,
							depositor,
							account, 
							va_date, 
							cash_yn, 
							cash_authno, 
							cash_tr_code, 
							cash_id_info, 
							cash_no, 
							pay_homeurl, 
							PayMethod, 
							PayCustName, 
							PayGoods, 
							PayMny, 
							OrderNumPay, 
							PayResultCd, 
							PayResultMsg, 
							PayReTrno, 
							PayReNum, 
							PayReTime, 
							PayCardCD,
							PayCard, 
							PayDivMon, 
							PayBankCD, 
							PayBank, 
							PayCashYN, 
							PayReqURL 
							)
							VALUES 
							( 
							:ClassOrderPayNumber,
							1,
							:CenterID,
							:ClassOrderPayPaymentMemberID,
							:ClassOrderPaySellingPrice,
							0,
							0,
							:ClassOrderPayPaymentPrice,
							0,
							:ClassOrderPayUseCashPrice,
							:ClassOrderPayUseCashPaymentType,
							0,
							:ClassOrderPayPgFeeRatio,
							21,
							now(),
							now(),
							now(),
							now(),
							0,
							:site_cd, 
							:req_tx, 
							:use_pay_method, 
							:bSucc, 
							:bSucces_cd, 
							:res_cd, 
							:res_msg, 
							:res_msg_bsucc, 
							:amount, 
							:ordr_idxx, 
							:tno, 
							:good_name, 
							:buyr_name, 
							:buyr_tel1, 
							:buyr_tel2,
							:buyr_mail,
							:pnt_issue,
							:app_time, 
							:card_cd, 
							:card_name, 
							:noinf, 
							:quota, 
							:app_no,
							:bank_name,
							:bank_code, 
							:bankname,
							:depositor,
							:account, 
							:va_date, 
							:cash_yn, 
							:cash_authno, 
							:cash_tr_code, 
							:cash_id_info, 
							:cash_no, 
							:pay_homeurl, 
							:PayMethod, 
							:PayCustName, 
							:PayGoods, 
							:PayMny, 
							:OrderNumPay, 
							:PayResultCd, 
							:PayResultMsg, 
							:PayReTrno, 
							:PayReNum, 
							:PayReTime, 
							:PayCardCD,
							:PayCard, 
							:PayDivMon, 
							:PayBankCD, 
							:PayBank, 
							:PayCashYN, 
							:PayReqURL	)	
							";
				$Stmt = $DbConn->prepare($Sql);

				$Stmt->bindParam(':ClassOrderPayNumber', $ordr_idxx);
				$Stmt->bindParam(':CenterID', $CenterID);
				$Stmt->bindParam(':ClassOrderPayPaymentMemberID', $RegMemberID);
				$Stmt->bindParam(':ClassOrderPayPaymentPrice', $PayMny);
				$Stmt->bindParam(':ClassOrderPaySellingPrice', $PayMny);
				$Stmt->bindParam(':ClassOrderPayUseCashPaymentType', $PayUseCashPaymentType);
				$Stmt->bindParam(':ClassOrderPayPgFeeRatio', $OnlineSitePgCardFeeRatio);
				$Stmt->bindParam(':ClassOrderPayUseCashPrice', $PayMny);
				
				//$Stmt->bindParam(':ClassOrderPayPgFeePrice', round($PayMny * $OnlineSitePgCardFeeRatio/100));

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
			} catch (PDOException $e) {
 			   var_dump($e->errorInfo);
			}
			
		}	

	$TaxOpen = 1;

	if ($PayUseCashPaymentType==2 && $PayResultCd == "0000" && $TaxOpen==1){//계좌이체 - 세금계산서 발행
		$Sql = "SELECT 
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

		if ($Cus_TaxMemberInfoID) {
			header("Location: tax_reg_action_auto.php?SearchCenterID=$CenterID&GoodType=SavedMoney&GoodName=수업료충전&GoodMoney=$amount&SavedMoneyID=$SavedMoneyID"); 
			exit;
		}else{
			header("Location: class_order_renew_center_form.php?SearchCenterID=$CenterID&GoodType=SavedMoney&GoodName=수업료충전&GoodMoney=$amount&SavedMoneyID=$SavedMoneyID"); 
			exit;
		}

	}else{
		header("Location: class_order_renew_center_form.php?SearchCenterID=$CenterID&GoodType=SavedMoney&GoodName=수업료충전&GoodMoney=$amount&SavedMoneyID=$SavedMoneyID");
		exit;
	}
	

}




include_once('../includes/dbclose.php');

?> 