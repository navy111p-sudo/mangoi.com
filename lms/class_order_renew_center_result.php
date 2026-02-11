<?php
include_once('../includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('../includes/common.php');

$ClassOrderEndDateLogFileName = $_SERVER["PHP_SELF"];

$ClassOrderPayID = "";
$CenterID = "";
$ClassOrderPayYear = "";
$ClassOrderPayMonth = "";
$ClassOrderPayUseCashPaymentType = 1;


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
$PayMethod        = isset($_REQUEST["PayMethod"]) ? $_REQUEST["PayMethod"] : "";	   	//' 모방일 결제타입 : CARD, BANK
$PayCustName      = isset($_REQUEST["PayCustName"]) ? $_REQUEST["PayCustName"] : "";   	//' 구매자명
$PayGoods         = isset($_REQUEST["PayGoods"]) ? $_REQUEST["PayGoods"] : "";         	//' 구매상품명
$PayMny           = isset($_REQUEST["PayMny"]) ? $_REQUEST["PayMny"] : "";           	//' 구매결제금액
//$OrderNumPay      = isset($_REQUEST["OrderNumPay"]) ? $_REQUEST["OrderNumPay"] : "";  //' 주문번호
$PayTradenum      = isset($_REQUEST["PayTradenum"]) ? $_REQUEST["PayTradenum"] : "";    //' 주문번호
$PayResultCd      = isset($_REQUEST["PayResultCd"]) ? $_REQUEST["PayResultCd"] : "";    //' 결과코드(성공 : 0000)
$PayResultMsg     = isset($_REQUEST["PayResultMsg"]) ? $_REQUEST["PayResultMsg"] : "";  //' 결제결과메시지
$PayReTrno        = isset($_REQUEST["PayReTrno"]) ? $_REQUEST["PayReTrno"] : "";      	//' PG사 거래번호(취소시 필요한번호)
$PayReNum         = isset($_REQUEST["PayReNum"]) ? $_REQUEST["PayReNum"] : "";      	//' PG사 거래승인번호
$PayReTime        = isset($_REQUEST["PayReTime"]) ? $_REQUEST["PayReTime"] : "";      	//' PG사 거래일시
$PayCardCD        = isset($_REQUEST["PayCardCD"]) ? $_REQUEST["PayCardCD"] : "";      	//' 카드사 코드
$PayCard          = isset($_REQUEST["PayCard"]) ? $_REQUEST["PayCard"] : "";      		//' 카드사명
$PayDivMon        = isset($_REQUEST["PayDivMon"]) ? $_REQUEST["PayDivMon"] : "";      	//' 할부 개월수
$PayBankCD        = isset($_REQUEST["PayBankCD"]) ? $_REQUEST["PayBankCD"] : "";     	//' 결제은행코드
$PayBank          = isset($_REQUEST["PayBank"]) ? $_REQUEST["PayBank"] : "";      		//' 결제은행
$PayCashYN        = isset($_REQUEST["PayCashYN"]) ? $_REQUEST["PayCashYN"] : "";      	//' 현금영수증사용여부
$PayReqURL        = isset($_REQUEST["PayReqURL"]) ? $_REQUEST["PayReqURL"] : "";      	//' ReqURL
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


$Sql = "SELECT * from ClassOrderPays where OrderNumPay=:OrderNumPay and PayResultCd='0000' ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':OrderNumPay', $PayTradenum);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$PrePayClassOrderPayID = $Row["ClassOrderPayID"];


if (!$PrePayClassOrderPayID){

	$Sql = "UPDATE ClassOrderPays set ";
		if ($PayResultCd == "0000"){
			if ($use_pay_method=="001000000000"){//가상계좌
				$Sql .= " ClassOrderPayProgress = 11, ";
			}else{
				$ChClassOrderState = 1;//ClassOrderState 변경해준다.
				$Sql .= " ClassOrderPayProgress = 21, ";
				$Sql .= " ClassOrderPayPaymentDateTime = now(), ";
			}
			$Sql .= " ClassOrderPayUseCashPaymentType = $ClassOrderPayUseCashPaymentType, ";
		}
		
		$Sql .= " ClassOrderPayPgFeeRatio = :ClassOrderPayPgFeeRatio, ";
		$Sql .= " ClassOrderPayPgFeePrice = :ClassOrderPayPgFeePrice, ";

		$Sql .= " LastUpdateUrl = 'class_order_renew_center_result.php', ";

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
		
		
		$Sql .= " ClassOrderPayDateTime = now(), ";
		$Sql .= " ClassOrderPayModiDateTime = now() ";
	$Sql .= " where ClassOrderPayNumber = :OrderNumPay ";

	$Stmt = $DbConn->prepare($Sql);

	$Stmt->bindParam(':ClassOrderPayPgFeeRatio', $ClassOrderPayPgFeeRatio);
	$Stmt->bindParam(':ClassOrderPayPgFeePrice', $ClassOrderPayPgFeePrice);

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
		$ClassOrderPayUseSavedMoneyPrice = $Row["ClassOrderPayUseSavedMoneyPrice"];
		$CenterID = $Row["CenterID"];


		$Sql = "select * from ClassOrderPayB2bs where ClassOrderPayID=:ClassOrderPayID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$ClassOrderPayYear = $Row["ClassOrderPayYear"];
		$ClassOrderPayMonth = $Row["ClassOrderPayMonth"];
		


		// ClassOrderPays 에서 검색해 보고 충전금을 사용했으면 충전금에서 사용한 만큼 차감해 준다.
		if ($ClassOrderPayUseSavedMoneyPrice > 0){

			$Sql = " INSERT INTO SavedMoney (SavedMoneyType, CenterID, SavedMoney, SavedMoneyRegDateTime, SavedMoneyState) 
			VALUES (2, :CenterID, :SavedMoney, now(), 1)";

			// 음수로 변경해서 차감금액으로 변경한다.
			$ClassOrderPayUseSavedMoneyPrice = (int)("-".$ClassOrderPayUseSavedMoneyPrice);
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':CenterID', $CenterID);
			$Stmt->bindParam(':SavedMoney', $ClassOrderPayUseSavedMoneyPrice);
			$Stmt->execute();
			$Stmt = null;
		}
		

		

		if ($ChClassOrderState==1){

			//======================= 학습 종료일 설정 ======================================================


			$NextSearchYear = $ClassOrderPayYear;
			$NextSearchMonth = (int)$ClassOrderPayMonth + 1;
			if ($NextSearchMonth>12){
				$NextSearchMonth = 1;
				$NextSearchYear = (int)$NextSearchYear + 1;
			}

			$CenterStudyStartDate = $NextSearchYear . "-" . substr("0".$NextSearchMonth,-2) . "-01";
			$CenterStudyEndDate = $NextSearchYear."-".substr("0".$NextSearchMonth,-2)."-".date("t", strtotime($CenterStudyStartDate));
			
			
			$Sql = "update ClassOrderPayB2bs set ClassOrderPayB2bState=1 where ClassOrderPayID=:ClassOrderPayID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
			$Stmt->execute();
			$Stmt = null;
			
			$Sql = "update Centers set CenterStudyEndDate=:CenterStudyEndDate where CenterID=:CenterID and datediff(CenterStudyEndDate, '".$CenterStudyEndDate."')<0";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':CenterStudyEndDate', $CenterStudyEndDate);
			$Stmt->bindParam(':CenterID', $CenterID);
			$Stmt->execute();
			$Stmt = null;


			$Sql = "update 
						ClassOrders 
					set 
						LastClassOrderEndDate=ClassOrderEndDate, 
						ClassOrderEndDate=:ClassOrderEndDate 
					where 
						(
							ClassOrderID in ( select ClassOrderID from ClassOrderPayB2bs where ClassOrderPayID=:ClassOrderPayID and ClassOrderID>0 ) 
							or 
							ClassMemberTypeGroupID in ( select ClassMemberTypeGroupID from ClassOrderPayB2bs where ClassOrderPayID=:ClassOrderPayID and ClassMemberTypeGroupID>0)
						) 
						and ClassOrderState=1 
						and datediff(ClassOrderEndDate, '".$CenterStudyEndDate."')<0";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ClassOrderEndDate', $CenterStudyEndDate);
			$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
			$Stmt->execute();
			$Stmt = null;

			//종료일 로그 남기기 =======================================
			$ClassOrderEndDateLogText = "update 
						ClassOrders 
					set 
						LastClassOrderEndDate=ClassOrderEndDate, 
						ClassOrderEndDate='$CenterStudyEndDate' 
					where 
						(
							ClassOrderID in ( select ClassOrderID from ClassOrderPayB2bs where ClassOrderPayID=$ClassOrderPayID and ClassOrderID>0 ) 
							or 
							ClassMemberTypeGroupID in ( select ClassMemberTypeGroupID from ClassOrderPayB2bs where ClassOrderPayID=$ClassOrderPayID and ClassMemberTypeGroupID>0)
						) 
						and ClassOrderState=1 
						and datediff(ClassOrderEndDate, '".$CenterStudyEndDate."')<0";

			$ClassOrderEndDateLogFileQueryNum = 1;
			$ClassOrderID = 0;
			$Sql_EndDateLog = " insert into ClassOrderEndDateLogs ( ";
				$Sql_EndDateLog .= " ClassOrderID, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogType, ";
				$Sql_EndDateLog .= " ClassOrderEndDate, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogFileName, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogFileQueryNum, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogText, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogRegDateTime ";
			$Sql_EndDateLog .= " ) values ( ";
				$Sql_EndDateLog .= " :ClassOrderID, ";
				$Sql_EndDateLog .= " '결제에 의한 종료일 변경', ";
				$Sql_EndDateLog .= " :ClassOrderEndDate, ";
				$Sql_EndDateLog .= " :ClassOrderEndDateLogFileName, ";
				$Sql_EndDateLog .= " :ClassOrderEndDateLogFileQueryNum, ";
				$Sql_EndDateLog .= " :ClassOrderEndDateLogText, ";
				$Sql_EndDateLog .= " now() ";
			$Sql_EndDateLog .= " ) ";
			$Stmt_EndDateLog = $DbConn->prepare($Sql_EndDateLog);
			$Stmt_EndDateLog->bindParam(':ClassOrderID', $ClassOrderID);
			$Stmt_EndDateLog->bindParam(':ClassOrderEndDate', $CenterStudyEndDate);
			$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileName', $ClassOrderEndDateLogFileName);
			$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileQueryNum', $ClassOrderEndDateLogFileQueryNum);
			$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogText', $ClassOrderEndDateLogText);
			$Stmt_EndDateLog->execute();
			$Stmt_EndDateLog = null;
			//종료일 로그 남기기 =======================================


			$Sql = "update 
						ClassOrders 
					set 
						LastClassOrderEndDateByPay=ClassOrderEndDate 
					where 
						(
							ClassOrderID in ( select ClassOrderID from ClassOrderPayB2bs where ClassOrderPayID=:ClassOrderPayID and ClassOrderID>0 ) 
							or 
							ClassMemberTypeGroupID in ( select ClassMemberTypeGroupID from ClassOrderPayB2bs where ClassOrderPayID=:ClassOrderPayID and ClassMemberTypeGroupID>0)
						) 
						and ClassOrderState=1 
						";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
			$Stmt->execute();
			$Stmt = null;

			//======================= 학습 종료일 설정 ======================================================
		}



		$Sql = "UPDATE 
					ClassOrders 
				set 
					ClassOrderPayID=:ClassOrderPayID 
				where 
					(
						ClassOrderID in ( select ClassOrderID from ClassOrderPayB2bs where ClassOrderPayID=:ClassOrderPayID and ClassOrderID>0 ) 
						or 
						ClassMemberTypeGroupID in ( select ClassMemberTypeGroupID from ClassOrderPayB2bs where ClassOrderPayID=:ClassOrderPayID and ClassMemberTypeGroupID>0)
					)
					and ClassOrderState=1 
				";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
		$Stmt->execute();
		$Stmt = null;


		
		

		


		//================= 포인트 ======================
		$OnlineSitePaymentPoint = round($ClassOrderPayUseCashPrice * ($OnlineSitePaymentPointRatio / 100));

		$Sql = "
			select 
				B.OnlineSiteSincerityPayStartDate,
				B.OnlineSiteSincerityPayEndDate
			from Centers A 
				inner join OnlineSites B on A.OnlineSiteID=B.OnlineSiteID
			where 
				A.CenterID=:CenterID
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':CenterID', $CenterID);
		$Stmt->execute();
		$Row = $Stmt->fetch();
		$OnlineSiteSincerityPayStartDate = $Row["OnlineSiteSincerityPayStartDate"];
		$OnlineSiteSincerityPayEndDate = $Row["OnlineSiteSincerityPayEndDate"];
		$Stmt = null;
		
		if($OnlineSiteSincerityPayStartDate) {
			$StartDay = $OnlineSiteSincerityPayStartDate;
		} else {
			$StartDay = date("25");
		}

		if($OnlineSiteSincerityPayEndDate) {
			$EndDay = $OnlineSiteSincerityPayEndDate;
		} else {
			$EndDay = date("t");
		}

		$Today = date("d");

		// 결제일이 25일 이후라면
		if($Today>=$StartDay && $Today<=$OnlineSiteSincerityPayEndDate) {
			$ValidateInfo = $ClassOrderPayYear ."|".$ClassOrderPayMonth;
			InsertNewTypePoint(12, 0, $ClassOrderPayPaymentMemberID, $ValidateInfo);
		}

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


$TaxOpen = 1;

if ($ClassOrderPayUseCashPaymentType==2 && $PayResultCd == "0000" && $TaxOpen==1){//계좌이체 - 세금계산서 발행

	$Sql = "SELECT 
				A.*
			from TaxMemberInfos A
			where A.OrganType=1 and A.OrganID=:OrganID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':OrganID', $CenterID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$Cus_TaxMemberInfoID= $Row["TaxMemberInfoID"];

	if ($Cus_TaxMemberInfoID) {
		header("Location: tax_reg_action_auto.php?ClassOrderPayID=$ClassOrderPayID&SearchCenterID=$CenterID&SearchYear=$ClassOrderPayYear&SearchMonth=$ClassOrderPayMonth"); 
		exit;
	}else{
		header("Location: class_order_renew_center_form.php?SearchCenterID=$CenterID&SearchYear=$ClassOrderPayYear&SearchMonth=$ClassOrderPayMonth"); 
		exit;
	}

}else{
	header("Location: class_order_renew_center_form.php?SearchCenterID=$CenterID&SearchYear=$ClassOrderPayYear&SearchMonth=$ClassOrderPayMonth"); 
	exit;
}

include_once('../includes/dbclose.php');

?> 