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


if ($ClassOrderPayUseCashPaymentType==2 && $PayResultCd == "0000"){//계좌이체
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
		echo $ret_val;
		header("Location: tax_reg_action_auto.php?ClassOrderPayID=$ClassOrderPayID&SearchCenterID=$CenterID&SearchYear=$ClassOrderPayYear&SearchMonth=$ClassOrderPayMonth"); 
		exit;
	}else{
		echo $ret_val;
	}
}else{
	echo $ret_val;
}
include_once('../includes/dbclose.php');
?> 