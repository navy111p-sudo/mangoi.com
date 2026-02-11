<?php
include_once('../includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('../includes/common.php');

$ClassOrderEndDateLogFileName = $_SERVER["PHP_SELF"];

$ClassOrderPayYear = isset($_REQUEST["ClassOrderPayYear"]) ? $_REQUEST["ClassOrderPayYear"] : "";
$ClassOrderPayMonth = isset($_REQUEST["ClassOrderPayMonth"]) ? $_REQUEST["ClassOrderPayMonth"] : "";
$ClassOrderPayNumber = isset($_REQUEST["ClassOrderPayNumber"]) ? $_REQUEST["ClassOrderPayNumber"] : "";
$ClassOrderPayID = isset($_REQUEST["ClassOrderPayID"]) ? $_REQUEST["ClassOrderPayID"] : "";
$UsedSavedMoney = isset($_REQUEST["UsedSavedMoney"]) ? $_REQUEST["UsedSavedMoney"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";

// 결제상태를 변경해 준다.
        $Sql = " UPDATE ClassOrderPays set ";
		$Sql .= " ClassOrderPayProgress = 21, ";
		$Sql .= " ClassOrderPayPaymentDateTime = now(), ";
		$Sql .= " LastUpdateUrl = 'class_order_renew_center_result.php', ";
		$Sql .= " ClassOrderPayDateTime = now(), ";
		$Sql .= " ClassOrderPayModiDateTime = now() ";
    	$Sql .= " where ClassOrderPayNumber = :ClassOrderPayNumber ";
        
        $Stmt = $DbConn->prepare($Sql);
        $Stmt->bindParam(':ClassOrderPayNumber', $ClassOrderPayNumber);
	    
        $Stmt->execute();
        $Stmt = null;

// 충전금에서 사용한 만큼 차감해 준다.
$Sql = " INSERT INTO SavedMoney (SavedMoneyType, CenterID, SavedMoney, SavedMoneyRegDateTime, SavedMoneyState) 
            VALUES (2, :CenterID, :SavedMoney, now(), 1)";

// 음수로 변경해서 차감금액으로 변경한다.
$UsedSavedMoney = (int)("-".$UsedSavedMoney);
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':CenterID', $CenterID);
$Stmt->bindParam(':SavedMoney', $UsedSavedMoney);
$Stmt->execute();
$Stmt = null;



            //======================= 학습 종료일 설정 ======================================================


			$NextSearchYear = $ClassOrderPayYear;
			$NextSearchMonth = (int)$ClassOrderPayMonth + 1;
			if ($NextSearchMonth>12){
				$NextSearchMonth = 1;
				$NextSearchYear = (int)$NextSearchYear + 1;
			}

			$CenterStudyStartDate = $NextSearchYear . "-" . substr("0".$NextSearchMonth,-2) . "-01";
			$CenterStudyEndDate = $NextSearchYear."-".substr("0".$NextSearchMonth,-2)."-".date("t", strtotime($CenterStudyStartDate));
			
			
			$Sql = "UPDATE ClassOrderPayB2bs set ClassOrderPayB2bState=1 where ClassOrderPayID=:ClassOrderPayID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
			$Stmt->execute();
			$Stmt = null;
			
			$Sql = "UPDATE Centers set CenterStudyEndDate=:CenterStudyEndDate where CenterID=:CenterID and datediff(CenterStudyEndDate, '".$CenterStudyEndDate."')<0";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':CenterStudyEndDate', $CenterStudyEndDate);
			$Stmt->bindParam(':CenterID', $CenterID);
			$Stmt->execute();
			$Stmt = null;


			$Sql = "UPDATE 
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
			$ClassOrderEndDateLogText = "UPDATE 
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
			$Sql_EndDateLog = " INSERT into ClassOrderEndDateLogs ( ";
				$Sql_EndDateLog .= " ClassOrderID, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogType, ";
				$Sql_EndDateLog .= " ClassOrderEndDate, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogFileName, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogFileQueryNum, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogText, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogRegDateTime ";
			$Sql_EndDateLog .= " ) values ( ";
				$Sql_EndDateLog .= " :ClassOrderID, ";
				$Sql_EndDateLog .= " '충전금 결제에 의한 종료일 변경', ";
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


			$Sql = "UPDATE 
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

        
    //header("Location: class_order_renew_center_form.php?SearchCenterID=$CenterID&SearchYear=$ClassOrderPayYear&SearchMonth=$ClassOrderPayMonth"); 
    //exit;
        
        
    include_once('../includes/dbclose.php');    
?>            