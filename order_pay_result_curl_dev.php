<?php
######################################################################################################################################
######################################################### cURL GET DATA ##############################################################
######################################################################################################################################
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');

$ClassOrderEndDateLogFileName = $_SERVER["PHP_SELF"];

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


/*
$test = json_encode($myObj);
// 페이지 호출 여부 테스트 - 200407 끝나고 삭제할 것
$Sql = "
	insert into ClassOrderPays_Logs
	(ClassOrderPayNumber, ClassOrderPayLogs, date)
	values
	(:test, 'all', now() )
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':test', $test);
$Stmt->execute();
$Stmt = null;
*/


// 페이지 호출 여부 테스트 - 200407 끝나고 삭제할 것
$Sql = "
	insert into ClassOrderPays_Logs
	(ClassOrderPayNumber, ClassOrderPayLogs, ClassOrderPayLogs2)
	values
	(:PayTradenum, 'test for call', 'order_pay_result_curl.php')
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':PayTradenum', $PayTradenum);
$Stmt->execute();
$Stmt = null;



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



$Sql = "select * from ClassOrderPays where ClassOrderPayNumber=:OrderNumPay and PayResultCd='0000' ";;
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
			(:PayTradenum, 'test for call 2', 'order_pay_result_curl.php')
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':PayTradenum', $PayTradenum);
		$Stmt->execute();
		$Stmt = null;


		// ===================================================
		$Sql_Log = "UPDATE ClassOrderPays SET ";

			//모바일은 카드와 실시간이체 밖에 없음. 따라서 0000 이면 무조건 결제완료.
			if ($PayResultCd == "0000"){
				  if ($Mobile_PayMethod=='CARD') {
						 $Sql_Log .= " ClassOrderPayUseCashPaymentType = 1, ";

						 $ClassOrderPayPgFeeRatio = $OnlineSitePgCardFeeRatio;
						 $ClassOrderPayPgFeePrice = 0;
				  } else {
						 $Sql_Log .= " ClassOrderPayUseCashPaymentType = 2, ";

						 if ($PayMny<=10000){//1만원 이하
							 $ClassOrderPayPgFeeRatio = 0;
							 $ClassOrderPayPgFeePrice = $OnlineSitePgDirectFeePrice;
						}else{
							 $ClassOrderPayPgFeeRatio = $OnlineSitePgDirectFeeRatio;
							 $ClassOrderPayPgFeePrice = 0;
						}
				  }

				  $ChClassOrderState = 1;//ClassOrderState 변경해준다.
				  $Sql_Log .= " ClassOrderPayProgress = 21, ";
				  $Sql_Log .= " ClassOrderPayPaymentDateTime = now(), ";

				  $Sql_Log .= " ClassOrderPayPgFeeRatio = '$ClassOrderPayPgFeeRatio', ";
				  $Sql_Log .= " ClassOrderPayPgFeePrice = '$ClassOrderPayPgFeePrice', ";	
			
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


			$Sql_Log .= " ClassOrderPayDateTime = now(), ";
			$Sql_Log .= " ClassOrderPayModiDateTime = now() ";
		$Sql_Log .= " WHERE ClassOrderPayNumber = '$PayTradenum' ";

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
		// ===================================================

		/* 실행 안하기 - 셀프페이 재전송
		$Sql = "update ClassOrderPays set ";

			//모바일은 카드와 실시간이체 밖에 없음. 따라서 0000 이면 무조건 결제완료.
			if ($PayResultCd == "0000"){
				  if ($Mobile_PayMethod=='CARD') {
						 $Sql .= " ClassOrderPayUseCashPaymentType = 1, ";

						 $ClassOrderPayPgFeeRatio = $OnlineSitePgCardFeeRatio;
						 $ClassOrderPayPgFeePrice = 0;
				  } else {
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

				  $Sql .= " ClassOrderPayPgFeeRatio = :ClassOrderPayPgFeeRatio, ";
				  $Sql .= " ClassOrderPayPgFeePrice = :ClassOrderPayPgFeePrice, ";	
			
			}

			$Sql .= " LastUpdateUrl = 'order_pay_result_curl.php', ";

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

			$Sql .= " ClassOrderPayLogs = :ClassOrderPayLogs, ";
			$Sql .= " ClassOrderPayDateTime = now(), ";
			$Sql .= " ClassOrderPayModiDateTime = now() ";
		$Sql .= " WHERE ClassOrderPayNumber = :OrderNumPay ";
		$Stmt = $DbConn->prepare($Sql);

		if ($PayResultCd == "0000"){
			$Stmt->bindParam(':ClassOrderPayPgFeeRatio', $ClassOrderPayPgFeeRatio);
			$Stmt->bindParam(':ClassOrderPayPgFeePrice', $ClassOrderPayPgFeePrice);
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
		$Stmt->bindParam(':ClassOrderPayLogs', $Sql_Log);
		$Stmt->execute();
		$Stmt = null;

		*/




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
				
				/* 실행 안하기 - 셀프페이 재전송
				$Sql = "update ClassOrders set ClassOrderState=1 where ClassOrderID in ( select ClassOrderID from ClassOrderPayDetails where ClassOrderPayID=:ClassOrderPayID )";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
				$Stmt->execute();
				$Stmt = null;
				*/
			}


			/* 실행 안하기 - 셀프페이 재전송
			$Sql = "update ClassOrders set ClassOrderPayID=:ClassOrderPayID where ClassOrderID in ( select ClassOrderID from ClassOrderPayDetails where ClassOrderPayID=:ClassOrderPayID )";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
			$Stmt->execute();
			$Stmt = null;
			*/


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

					/* 실행 안하기 - 셀프페이 재전송
					$Sql2 = "update ClassOrders set LastClassOrderEndDate=ClassOrderEndDate, LastClassOrderEndDateByPay=ClassOrderEndDate, ClassOrderStartDate=:ClassOrderStartDate, ClassOrderEndDate=:ClassOrderEndDate, ClassOrderModiDateTime=now() where ClassOrderID=$ClassOrderID";
					$Stmt2 = $DbConn->prepare($Sql2);
					$Stmt2->bindParam(':ClassOrderEndDate', $NewClassOrderEndDate);
					$Stmt2->bindParam(':ClassOrderStartDate', $NewClassOrderStartDate);
					$Stmt2->execute();
					$Stmt2 = null;
					*/

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
						$Sql_EndDateLog .= " '셀프페이재전송', ";
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


					/* 실행 안하기 - 셀프페이 재전송
					$Sql2 = "update ClassOrders set LastClassOrderEndDate=ClassOrderEndDate, LastClassOrderEndDateByPay=ClassOrderEndDate, ClassOrderEndDate=:ClassOrderEndDate, ClassOrderModiDateTime=now() where ClassOrderID=$ClassOrderID";
					$Stmt2 = $DbConn->prepare($Sql2);
					$Stmt2->bindParam(':ClassOrderEndDate', $NewClassOrderEndDate);
					$Stmt2->execute();
					$Stmt2 = null;
					*/

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
						$Sql_EndDateLog .= " '셀프페이재전송', ";
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


			/* 실행 안하기 - 셀프페이 재전송
			$Sql2 = "update ClassOrderPays set ClassOrderPayStartDate=:ClassOrderPayStartDate where ClassOrderPayNumber=:ClassOrderPayNumber";
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':ClassOrderPayStartDate', $ClassOrderPayStartDate);
			$Stmt2->bindParam(':ClassOrderPayNumber', $PayTradenum);
			$Stmt2->execute();
			$Stmt2 = null;	
			*/
			//======================= 학습 종료일 설정 ======================================================



		}

}
include_once('./includes/dbclose.php');
######################################################################################################################################
echo $ret_val;
?>  