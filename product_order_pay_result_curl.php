<?php
######################################################################################################################################
######################################################### cURL GET DATA ##############################################################
######################################################################################################################################
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
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



$Sql = "select * from ProductOrders where OrderNumPay=:OrderNumPay and PayResultCd='0000' ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':OrderNumPay', $PayTradenum);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$PreProductOrderID = $Row["ProductOrderID"];


if (!$PreProductOrderID){


		// ===================================================
		$Sql_Log = "UPDATE ProductOrders SET ";

			//모바일은 카드와 실시간이체 밖에 없음. 따라서 0000 이면 무조건 결제완료.
			if ($PayResultCd == "0000"){
				  if ($Mobile_PayMethod=='CARD') {
						 $Sql_Log .= " UseCashPaymentType = 1, ";

						 $OrderPayPgFeeRatio = $OnlineSitePgCardFeeRatio;
						 $OrderPayPgFeePrice = 0;
				  } else {
						 $Sql_Log .= " UseCashPaymentType = 2, ";

						 if ($PayMny<=10000){//1만원 이하
							 $OrderPayPgFeeRatio = 0;
							 $OrderPayPgFeePrice = $OnlineSitePgDirectFeePrice;
						}else{
							 $OrderPayPgFeeRatio = $OnlineSitePgDirectFeeRatio;
							 $OrderPayPgFeePrice = 0;
						}
				  }

				  $Sql_Log .= " ProductOrderState = 21, ";
				  $Sql_Log .= " PaymentDateTime = now(), ";

				  $Sql_Log .= " OrderPayPgFeeRatio = '$OrderPayPgFeeRatio', ";
				  $Sql_Log .= " OrderPayPgFeePrice = '$OrderPayPgFeePrice', ";	
			
			}


			//kcp ==============
			$Sql_Log .= " PayCustName  = '$PayCustName',  ";
			$Sql_Log .= " PayGoods     = '$PayGoods',     ";
			$Sql_Log .= " PayMny       = '$PayMny',       ";
			$Sql_Log .= " PayResultCd  = '$PayResultCd',  ";
			$Sql_Log .= " PayResultMsg = '$PayResultMsg', ";
			$Sql_Log .= " PayReTrno    = '$PayReTrno',    ";
			$Sql_Log .= " PayReNum     = '$PayReNum',     ";
			$Sql_Log .= " PayReTime    = '$PayReTime',    ";
			if ($Mobile_PayMethod=='CARD') {
				 $Sql_Log .= " PayCardCD = '$PayCardCD', ";
				 $Sql_Log .= " PayCard   = '$PayCard',   ";
				 $Sql_Log .= " PayDivMon = '$PayDivMon', ";
			} else { 
				 $Sql_Log .= " PayBankCD = '$PayBankCD', ";
				 $Sql_Log .= " PayBank   = '$PayBank',   ";
				 $Sql_Log .= " PayCashYN = '$PayCashYN', ";
			}
			$Sql_Log .= " PayReqURL    = '$PayReqURL',    ";
			//kcp ==============


			$Sql_Log .= " ProductOrderModiDateTime = now() ";
		$Sql_Log .= " WHERE ProductOrderNumber = '$PayTradenum' ";

		$Sql = "
			insert into ProductOrders_Logs
			(ProductOrderNumber, OrderPayLogs)
			values
			(:PayTradenum, :Sql_Log)
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':PayTradenum', $PayTradenum);
		$Stmt->bindParam(':Sql_Log', $Sql_Log);
		$Stmt->execute();
		$Stmt = null;

		// ===================================================


		$Sql = "update ProductOrders set ";

			//모바일은 카드와 실시간이체 밖에 없음. 따라서 0000 이면 무조건 결제완료.
			if ($PayResultCd == "0000"){
				  if ($Mobile_PayMethod=='CARD') {
						 $Sql .= " UseCashPaymentType = 1, ";

						 $OrderPayPgFeeRatio = $OnlineSitePgCardFeeRatio;
						 $OrderPayPgFeePrice = 0;
				  } else {
						 $Sql .= " UseCashPaymentType = 2, ";

						 if ($PayMny<=10000){//1만원 이하
							 $OrderPayPgFeeRatio = 0;
							 $OrderPayPgFeePrice = $OnlineSitePgDirectFeePrice;
						}else{
							 $OrderPayPgFeeRatio = $OnlineSitePgDirectFeeRatio;
							 $OrderPayPgFeePrice = 0;
						}
				  }

				  $Sql .= " ProductOrderState = 21, ";
				  $Sql .= " PaymentDateTime = now(), ";

				  $Sql .= " OrderPayPgFeeRatio = :OrderPayPgFeeRatio, ";
				  $Sql .= " OrderPayPgFeePrice = :OrderPayPgFeePrice, ";	
			
			}

			$Sql .= " LastUpdateUrl = 'product_order_pay_result_curl.php', ";

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

			$Sql .= " OrderDateTime = now(), ";
			$Sql .= " OrderPayLogs = :OrderPayLogs, ";
			$Sql .= " ProductOrderModiDateTime = now() ";
		$Sql .= " WHERE ProductOrderNumber = :OrderNumPay ";
		$Stmt = $DbConn->prepare($Sql);

		if ($PayResultCd == "0000"){
			$Stmt->bindParam(':OrderPayPgFeeRatio', $OrderPayPgFeeRatio);
			$Stmt->bindParam(':OrderPayPgFeePrice', $OrderPayPgFeePrice);
		}

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
		$Stmt->bindParam(':OrderPayLogs', $Sql_Log);
		$Stmt->execute();
		$Stmt = null;




		if ($err_msg != 0) {
			  $ret_val = 1;
		}


		if ($PayResultCd == "0000"){


			$Sql = "select 
				A.ProductOrderCartID
				from ProductOrders A where A.ProductOrderNumber=:PayTradenum";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':PayTradenum', $PayTradenum);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;
			$ProductOrderCartID = $Row["ProductOrderCartID"];

			$Sql = "delete from ProductOrderCartDetails where ProductOrderCartID=$ProductOrderCartID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt = null;

			$Sql = "delete from ProductOrderCarts where ProductOrderCartID=$ProductOrderCartID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt = null;


			//================= 포인트 ======================
			//$OnlineSitePaymentPoint = round($ClassOrderPayUseCashPrice * ($OnlineSitePaymentPointRatio / 100));

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
######################################################################################################################################
echo $ret_val;
?> 