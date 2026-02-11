<?php
######################################################################################################################################
######################################################### cURL GET DATA ##############################################################
######################################################################################################################################
include_once('../includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('../includes/common.php');


$ClassOrderPayID = "";
$CenterID = "";
$ClassOrderPayYear = "";
$ClassOrderPayMonth = "";
$ClassOrderPayUseCashPaymentType = 1;


#------------------------------------------------------------------------------------------------------------------------------------#
$ret_val = 9;
#====================================================================================================================================#
$myObj = new stdObject();
$myObj = new stdClass;
$myObj = json_decode(file_get_contents('php://input'));
#------------------------------------------------------------------------------------------------------------------------------------#
$Mobile_PayMethod = $myObj->Mobile_PayMethod; 
$PayCustName      = $myObj->PayCustName;      
$PayGoods         = $myObj->PayGoods;         
$PayMny           = $myObj->PayMny;           
$PayTradenum      = $myObj->PayTradenum;      
$PayResultCd      = $myObj->PayResultCd;     
$PayResultMsg     = $myObj->PayResultMsg;    
$PayReTrno        = $myObj->PayReTrno;       
$PayReNum         = $myObj->PayReNum;        
$PayReTime        = $myObj->PayReTime;       
if ($Mobile_PayMethod=='CARD') {
	  $PayCardCD  = $myObj->PayCardCD;       
	  $PayCard    = $myObj->PayCard;         
	  $PayDivMon  = $myObj->PayDivMon;       
} else {
      $PayBankCD  = $myObj->PayBankCD;
	  $PayBank    = $myObj->PayBank;
	  $PayCashYN  = $myObj->PayCashYN;
}
$PayReqURL        = $myObj->PayReqURL;       
#------------------------------------------------------------------------------------------------------------------------------------#
$err_num = 0;
$err_msg = "";
$ChClassOrderState = 0;


$Sql = "select * from ClassOrderPays where OrderNumPay=:OrderNumPay";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':OrderNumPay', $PayTradenum);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$PrePayClassOrderPayID = $Row["ClassOrderPayID"];


if (!$PrePayClassOrderPayID){
		$Sql = "update ClassOrderPays set ";

			//모바일은 카드와 실시간이체 밖에 없음. 따라서 0000 이면 무조건 결제완료.
			if ($PayResultCd == "0000"){
				  if ($Mobile_PayMethod=='CARD') {
						 $ClassOrderPayUseCashPaymentType = 1;

						 $Sql .= " ClassOrderPayUseCashPaymentType = 1, ";

						 $ClassOrderPayPgFeeRatio = $OnlineSitePgCardFeeRatio;
						 $ClassOrderPayPgFeePrice = 0;
				  } else {
						 $ClassOrderPayUseCashPaymentType = 2;

						 $Sql .= " ClassOrderPayUseCashPaymentType = 2, ";

						 if ($PayMny<=10000){//1만원 이하
							 $ClassOrderPayPgFeeRatio = 0;
							 $ClassOrderPayPgFeePrice = $OnlineSitePgDirectFeePrice;
						}else{
							 $ClassOrderPayPgFeeRatio = $OnlineSitePgDirectFeeRatio;
							 $ClassOrderPayPgFeePrice = 0;
						}
				  }

				  $ChClassOrderState = 1;//ClassOrderState 변경해준다.
				  $Sql .= " ClassOrderPayProgress = 21, ";
				  $Sql .= " ClassOrderPayPaymentDateTime = now(), ";
			}

			$Sql .= " ClassOrderPayPgFeeRatio = :ClassOrderPayPgFeeRatio, ";
			$Sql .= " ClassOrderPayPgFeePrice = :ClassOrderPayPgFeePrice, ";

			$Sql .= " LastUpdateUrl = 'class_order_renew_center_result_curl.php', ";

			//kcp ==============
			$Sql .= " PayCustName  = :PayCustName,  ";
			$Sql .= " PayGoods     = :PayGoods,     ";
			$Sql .= " PayMny       = :PayMny,       ";
			$Sql .= " PayResultCd  = :PayResultCd,  ";
			$Sql .= " PayResultMsg = :PayResultMsg, ";
			$Sql .= " PayReTrno    = :PayReTrno,    ";
			$Sql .= " PayReNum     = :PayReNum,     ";
			$Sql .= " PayReTime    = :PayReTime,    ";
			if ($Mobile_PayMethod=='CARD') {
				 $Sql .= " PayCardCD = :PayCardCD, ";
				 $Sql .= " PayCard   = :PayCard,   ";
				 $Sql .= " PayDivMon = :PayDivMon, ";
			} else { 
				 $Sql .= " PayBankCD = :PayBankCD, ";
				 $Sql .= " PayBank   = :PayBank,   ";
				 $Sql .= " PayCashYN = :PayCashYN, ";
			}
			$Sql .= " PayReqURL    = :PayReqURL,    ";	
			//kcp ==============


			$Sql .= " ClassOrderPayDateTime = now(), ";
			$Sql .= " ClassOrderPayModiDateTime = now() ";
		$Sql .= " WHERE ClassOrderPayNumber = :OrderNumPay ";
		$Stmt = $DbConn->prepare($Sql);

		$Stmt->bindParam(':ClassOrderPayPgFeeRatio', $ClassOrderPayPgFeeRatio);
		$Stmt->bindParam(':ClassOrderPayPgFeePrice', $ClassOrderPayPgFeePrice);

		//kcp ==============
		$Stmt->bindParam(':PayCustName',  $PayCustName);
		$Stmt->bindParam(':PayGoods',     $PayGoods);
		$Stmt->bindParam(':PayMny',       $PayMny);
		$Stmt->bindParam(':OrderNumPay',  $PayTradenum);
		$Stmt->bindParam(':PayResultCd',  $PayResultCd);
		$Stmt->bindParam(':PayResultMsg', $PayResultMsg);
		$Stmt->bindParam(':PayReTrno',    $PayReTrno);
		$Stmt->bindParam(':PayReNum',     $PayReNum);
		$Stmt->bindParam(':PayReTime',    $PayReTime);
		if ($Mobile_PayMethod=='CARD') {
			 $Stmt->bindParam(':PayCardCD', $PayCardCD);
			 $Stmt->bindParam(':PayCard',   $PayCard);
			 $Stmt->bindParam(':PayDivMon', $PayDivMon);
		} else {
			 $Stmt->bindParam(':PayBankCD', $PayBankCD);
			 $Stmt->bindParam(':PayBank',   $PayBank);
			 $Stmt->bindParam(':PayCashYN', $PayCashYN);
		}
		$Stmt->bindParam(':PayReqURL',    $PayReqURL);
		//kcp ==============
		$Stmt->execute();
		$Stmt = null;

		if ($err_msg != 0) {
			  $ret_val = 1;
		}


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

				//======================= 학습 종료일 설정 ======================================================
				$Sql = "select * from ClassOrderPayB2bs where ClassOrderPayID=:ClassOrderPayID";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$ClassOrderPayYear = $Row["ClassOrderPayYear"];
				$ClassOrderPayMonth = $Row["ClassOrderPayMonth"];
				$CenterID = $Row["CenterID"];

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


			$Sql = "update 
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

			$Today = date("d");
			$ConditionDate = date("25");

			// 결제일이 25일 이후라면
			if($Today<=$ConditionDate) {
				InsertNewTypePoint(12, 0, $ClassOrderPayPaymentMemberID, $ClassOrderPayID);
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


######################################################################################################################################

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (1, now())";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

//계좌이체 ==== 세금계산서 발행 =============================================================================================================
if ($ClassOrderPayUseCashPaymentType==2 && $PayResultCd == "0000"){
	
$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (2, now())";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;	
	
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

	
	if ($Cus_TaxMemberInfoID){

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (3, now())";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

		//데이터 넣기 ========================= tax_reg_action_auto.php 와 동일

		$TaxInvoiceID = "";
		$TaxInvoiceRegType = 1;
		$TaxInvoiceType = 1;
		$TaxInvoicePayID = $ClassOrderPayID; 


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

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (4, '$CenterID / $TaxInvoicePayID / $PayGoods / $ClassOrderPayUseCashPrice')";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

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

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (5, now())";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

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


$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (6, '$CenterID')";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

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


		$invoicerMgtKey = date("Ymdhis")."-".substr("000000".$CenterID, -6);//문서번호
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

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (7, '$Cus_CorpNum')";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

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

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (8, now())";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

		$invoicerCorpNum = str_replace("-","",$invoicerCorpNum);
		$invoiceeCorpNum = str_replace("-","",$invoiceeCorpNum);
		$writeDate = str_replace("-","",$writeDate);

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (33, '$invoicerCorpNum / $invoiceeCorpNum / $writeDate')";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

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

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (9, now())";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

		$serialNum = 1;
		for ($ii=1;$ii<=5;$ii++){

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (10, '$Sql')";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

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

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (11, now())";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;
			
			}

		}
		//데이터 넣기 ========================= tax_reg_action_auto.php 와 동일

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (12, now())";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

		//계산서 발행 ========================= RegistIssue_mangoi.php 와 동일
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

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (13, now())";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

		$supplyCostTotal = SpaceToZero($supplyCostTotal);
		$taxTotal = SpaceToZero($taxTotal);
		$totalAmount = SpaceToZero($totalAmount);

		//==============================================================================================
		/**
		 * 1건의 세금계산서를 즉시발행 처리합니다.
		 * - https://docs.popbill.com/taxinvoice/php/api#RegistIssue
		 */

		include '../popbill/TaxinvoiceExample/common_mangoi.php';

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (14, now())";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

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

		$Taxinvoice->addContactList = array();

		$Taxinvoice->addContactList[] = new TaxinvoiceAddContact();
		$Taxinvoice->addContactList[0]->serialNum = 1;				        // 일련번호 1부터 순차기재
		$Taxinvoice->addContactList[0]->email = $invoiceeEmail2;	    // 이메일주소
		$Taxinvoice->addContactList[0]->contactName	= $invoiceeContactName2;		// 담당자명

		//$Taxinvoice->addContactList[] = new TaxinvoiceAddContact();
		//$Taxinvoice->addContactList[1]->serialNum = 2;			        	// 일련번호 1부터 순차기재
		//$Taxinvoice->addContactList[1]->email = 'goonglee@naver.com';	    // 이메일주소
		//$Taxinvoice->addContactList[1]->contactName	= '홍길동';		  // 담당자명


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

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (15, now())";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

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

		//계산서 발행 ========================= RegistIssue_mangoi.php 와 동일

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (16, now())";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;
	
	}
}

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

//계좌이체 ==== 세금계산서 발행 =============================================================================================================

$Sql33 = "insert into TestTable (TestTableText, TestTableLog) values (17, now())";
$Stmt33 = $DbConn->prepare($Sql33);
$Stmt33->execute();
$Stmt33 = null;

echo $ret_val;
include_once('../includes/dbclose.php');
?> 