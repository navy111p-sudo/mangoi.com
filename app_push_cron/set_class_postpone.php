<?
$NoIgnoreCenterRenew = 0;//0 이면 단체 연장을 하지 않아도 수업을 계속한다.(과도기) 최종적으로는 1으로 해야한다.

//====================================================== DB ======================================================
$DbHost = "localhost";
$DbName = "mangoi";
$DbUser = "mangoi";
$DbPass = "mi!@#2019";

$ClassOrderEndDateLogFileName = $_SERVER["PHP_SELF"];

try {
	$DbConn = new PDO("mysql:host=$DbHost;dbname=$DbName;charset=utf8", $DbUser, $DbPass);
	$DbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
	echo "Connection failed: " . $e->getMessage();
}

//====================================================== DB ======================================================
//=======================================================================================================
$EncryptionKey = md5("kr.ahsol");//절대 변경 불가(변경되면 회원정보 복구 불가)
//=======================================================================================================

function DateToTimestamp($Date, $TimeZone) {
	// ex, GetTimeStamp(16050230, 9)
	$NewTimeStamp = new DateTime($Date, new DateTimeZone($TimeZone));

	return $NewTimeStamp->getTimestamp(); // 1457690400
}



$EduCenterID = 1;
$AddSqlWhere = "1=1";

$SelectYear = date("Y");
$SelectMonth = date("m");
$SelectDay = date("d");

//$SelectYear = "2020";
//$SelectMonth = "01";
//$SelectDay = "15";

$SearchStartHour=0;
$SearchStartMinute=0;
$SearchEndHour=23;
$SearchEndMinute=50;

$SelectDate = $SelectYear."-".$SelectMonth."-".$SelectDay;
$SelectDateWeek = date('w', strtotime($SelectDate));


$Sql = " select 
				A.EduCenterHolidayDate  
			from EduCenterHolidays A
			where A.EduCenterHolidayDate=:EduCenterHolidayDate
				and A.EduCenterID=:EduCenterID
				and A.EduCenterHolidayState=1 
				";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EduCenterHolidayDate', $SelectDate);
$Stmt->bindParam(':EduCenterID', $EduCenterID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$EduCenterHolidayDate = $Row["EduCenterHolidayDate"];


if ( $EduCenterHolidayDate ){


	
	// 클래스 가져오기 및 등록 ========================================
	$AddSqlWhere = $AddSqlWhere . " and MB.MemberState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and CT.CenterState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and BR.BranchState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and BRG.BranchGroupState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and COM.CompanyState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and FR.FranchiseState<>0 ";


	//============================
	$SearchStartHourMinute = substr("0".$SearchStartHour,-2) . substr("0".$SearchStartMinute,-2);
	$AddSqlWhere = $AddSqlWhere . " and concat(lpad(COS.StudyTimeHour,2,0) , lpad(COS.StudyTimeMinute,2,0))>=$SearchStartHourMinute ";

	$SearchEndHourMinute = substr("0".$SearchEndHour,-2) . substr("0".$SearchEndMinute,-2);
	$AddSqlWhere = $AddSqlWhere . " and concat(lpad(COS.StudyTimeHour,2,0) , lpad(COS.StudyTimeMinute,2,0))<=$SearchEndHourMinute ";
	//============================


	$AddSqlWhere = $AddSqlWhere . " and TEA.TeacherState=1 ";
	$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotMaster=1 ";
	$AddSqlWhere = $AddSqlWhere . " and ( 
											(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

											or 
											(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

											or 
											(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

											or 
											(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 
											
											or 
											(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SelectDate."')=0 )   
										)   
									";

	$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotState=1 ";

	$AddSqlWhere = $AddSqlWhere . " and CO.ClassProgress=11 ";
	$AddSqlWhere = $AddSqlWhere . " and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=3 or CO.ClassOrderState=5 or CO.ClassOrderState=6) ";



	$ViewTable = "

		select 
			COS.ClassMemberType,
			COS.ClassOrderSlotType,
			COS.ClassOrderSlotType2, 
			COS.ClassOrderSlotDate,
			COS.TeacherID,
			COS.ClassOrderSlotMaster,
			COS.StudyTimeWeek,
			COS.StudyTimeHour,
			COS.StudyTimeMinute,
			COS.ClassOrderSlotState,
			concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0)) as ClassStartTime, 

			CO.ClassOrderID,
			CO.ClassProductID,
			CO.ClassOrderTimeTypeID,
			CO.MemberID,
			CO.ClassOrderStartDate,
			CO.ClassOrderEndDate,
			CO.ClassOrderState,

			ifnull(CLS.ClassID,0) as ClassID,
			CLS.TeacherInDateTime, 
			CLS.TeacherID as ClassTeacherID,
			CLS.ClassLinkType,
			CLS.StartDateTime,
			CLS.StartDateTimeStamp,
			CLS.StartYear,
			CLS.StartMonth,
			CLS.StartDay,
			CLS.StartHour,
			CLS.StartMinute,
			CLS.EndDateTime,
			CLS.EndDateTimeStamp,
			CLS.EndYear,
			CLS.EndMonth,
			CLS.EndDay,
			CLS.EndHour,
			CLS.EndMinute,
			CLS.CommonUseClassIn,
			CLS.CommonShClassCode,
			CLS.CommonCiCourseID,
			CLS.CommonCiClassID,
			CLS.CommonCiTelephoneTeacher,
			CLS.CommonCiTelephoneStudent,
			ifnull(CLS.ClassAttendState,-1) as ClassAttendState,
			CLS.ClassAttendStateMemberID,
			CLS.ClassAttendStateMsg,
			ifnull(CLS.ClassState, 0) as ClassState,
			CLS.BookVideoID,
			CLS.BookQuizID,
			CLS.BookScanID,
			CLS.BookWebookUnitID, 
			CLS.BookSystemType, 
			CLS.ClassRegDateTime,
			CLS.ClassModiDateTime,

			MB.MemberName,
			MB.MemberPayType,
			MB.MemberChangeTeacher,
			MB.MemberNickName,
			MB.MemberLoginID, 
			MB.MemberLevelID,
			MB.MemberCiTelephone,

			AES_DECRYPT(UNHEX(MB.MemberPhone1),'$EncryptionKey') as DecMemberPhone1,

			TEA.TeacherName,
			MB2.MemberLoginID as TeacherLoginID, 
			MB2.MemberCiTelephone as TeacherCiTelephone,
			CT.CenterID as JoinCenterID,
			CT.CenterName as JoinCenterName,
			CT.CenterPayType,
			CT.CenterRenewType,
			CT.CenterStudyEndDate,

			BR.BranchID as JoinBranchID,
			BR.BranchName as JoinBranchName, 
			BRG.BranchGroupID as JoinBranchGroupID,
			BRG.BranchGroupName as JoinBranchGroupName,
			COM.CompanyID as JoinCompanyID,
			COM.CompanyName as JoinCompanyName,
			FR.FranchiseName,
			MB3.MemberLoginID as CenterLoginID,
			TEA2.TeacherName as ClassTeacherName

		from ClassOrderSlots COS 

				left outer join Classes CLS on COS.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$SelectYear." and CLS.StartMonth=".$SelectMonth." and CLS.StartDay=".$SelectDay." and CLS.StartHour=COS.StudyTimeHour and CLS.StartMinute=COS.StudyTimeMinute and CLS.TeacherID=COS.TeacherID and CLS.ClassAttendState<>99 

				inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 



				inner join Members MB on CO.MemberID=MB.MemberID 
				inner join Centers CT on MB.CenterID=CT.CenterID 
				inner join Branches BR on CT.BranchID=BR.BranchID 
				inner join BranchGroups BRG on BR.BranchGroupID=BRG.BranchGroupID 
				inner join Companies COM on BRG.CompanyID=COM.CompanyID 
				inner join Franchises FR on COM.FranchiseID=FR.FranchiseID 
				inner join Teachers TEA on COS.TeacherID=TEA.TeacherID 
				left outer join Teachers TEA2 on CLS.TeacherID=TEA2.TeacherID 
				inner join Members MB2 on TEA.TeacherID=MB2.TeacherID 
				left outer join Members MB3 on CT.CenterID=MB3.CenterID and MB3.MemberLevelID=12 

	where ".$AddSqlWhere." ";


	$SqlWhereCenterRenew = "";
	if ($NoIgnoreCenterRenew==1){
		$SqlWhereCenterRenew = " and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ";
	}

	$Sql3 = "
			select 
				V.*
			from ($ViewTable) V 

			where 
				(
					V.CenterPayType=1 and V.CenterRenewType=1 
					".$SqlWhereCenterRenew." 
					and V.MemberPayType=0 
					and (
							(V.ClassOrderState=1 or V.ClassOrderState=2 or V.ClassOrderState=5 or V.ClassOrderState=6) 
							or 
							(V.ClassOrderState=3 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0) 
						)
				) 
				or 
				(
					V.CenterPayType=1 and V.CenterRenewType=2 
					and V.MemberPayType=0 
					and (
							(V.ClassOrderState=1 or V.ClassOrderState=2 or V.ClassOrderState=5 or V.ClassOrderState=6) 
							or 
							(V.ClassOrderState=3 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0) 
						)
				)
				or 
				( 
					( V.CenterPayType=2 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
					or 
					( V.CenterPayType=1 and V.MemberPayType=1 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
				)
				or
				V.ClassProductID=2 
				or 
				V.ClassProductID=3 
				or 
				(V.ClassOrderSlotType=2 and datediff(V.ClassOrderSlotDate, '".$SelectDate."')=0) 
			order by V.StudyTimeHour asc, V.StudyTimeMinute, V.TeacherID asc
	";

	$Stmt3 = $DbConn->prepare($Sql3);
	$Stmt3->execute();
	$Stmt3->setFetchMode(PDO::FETCH_ASSOC);


	$ClassIDs = "|";
	while($Row3 = $Stmt3->fetch()) {
		
		$ClassID = $Row3["ClassID"];
		$ClassState = $Row3["ClassState"];
		$ClassOrderID = $Row3["ClassOrderID"];
		$StartHour = $Row3["StudyTimeHour"];
		$StartMinute = $Row3["StudyTimeMinute"];
		$ClassOrderTimeTypeID = $Row3["ClassOrderTimeTypeID"];
		$TeacherID = $Row3["TeacherID"];
		$MemberID = $Row3["MemberID"];

		if ($ClassState==0){

			$StartYear = $SelectYear;
			$StartMonth = $SelectMonth;
			$StartDay = $SelectDay;

			$Sql2 = "select 
					A.TeacherPayPerTime,
					A.TeacherName,
					B.MemberLoginID
				from 
					Teachers A 
						inner join Members B on A.TeacherID=B.TeacherID and B.MemberLevelID=15 
				where A.TeacherID=:TeacherID";
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':TeacherID', $TeacherID);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
			$Row2 = $Stmt2->fetch();
			$Stmt = null;
			$TeacherPayPerTime = $Row2["TeacherPayPerTime"];
			$TeacherName = $Row2["TeacherName"];
			$TeacherLoginID = $Row2["MemberLoginID"];


			if ($ClassOrderTimeTypeID==2){
				$PlusMinute = 20;
			}else if ($ClassOrderTimeTypeID==3){
				$PlusMinute = 30;
			}else if ($ClassOrderTimeTypeID==4){
				$PlusMinute = 40;
			}

			$EndMinute = $StartMinute + $PlusMinute;
			if ($EndMinute>=60){
				$EndMinute = $EndMinute - 60;
				$EndHour = $StartHour + 1;
			}else{
				$EndHour = $StartHour;
			}

			//종로시간이 24를 넘어가면 23시 59분으로 맞춘다.
			if ($EndHour>=24){
				$EndHour = 23;
				$EndMinute = 59;
			}
			//종로시간이 24를 넘어가면 23시 59분으로 맞춘다.

			$EndYear = $StartYear;
			$EndMonth = $StartMonth;
			$EndDay = $StartDay;



			$Sql2 = "select 
						ClassID, 
						CommonShClassCode 
					from Classes 
					where 
						ClassOrderID=".$ClassOrderID." 
						and MemberID=".$MemberID."
						and TeacherID=".$TeacherID."
						and StartYear=".$StartYear."
						and StartMonth=".$StartMonth."
						and StartDay=".$StartDay."
						and StartHour=".$StartHour."
						and StartMinute=".$StartMinute."

						and EndYear=".$EndYear."
						and EndMonth=".$EndMonth."
						and EndDay=".$EndDay."
						and EndHour=".$EndHour."
						and EndMinute=".$EndMinute." 

						and ClassAttendState<>99 

				";

			//echo $Sql2;
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':TeacherID', $TeacherID);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
			$Row2 = $Stmt2->fetch();
			$Stmt2 = null;
			$ClassID = $Row2["ClassID"];
			$CommonShClassCode = $Row2["CommonShClassCode"];



			if (!$ClassID){

				$StartDate = $StartYear."-".substr("0".$StartMonth,-2)."-".substr("0".$StartDay,-2);
				$CommonShClassCode = $TeacherID."_". str_replace("-","",$StartDate) ."_".$StartHour."_".$StartMinute;

				$StartDateTime = $StartYear."-".substr("0".$StartMonth,-2)."-".substr("0".$StartDay,-2)." ".substr("0".$StartHour,-2).":".substr("0".$StartMinute,-2).":00";
				$EndDateTime   = $EndYear.  "-".substr("0".$EndMonth,  -2)."-".substr("0".$EndDay,-2  )." ".substr("0".$EndHour,-2  ).":".substr("0".$EndMinute,-2  ).":00";

				$StartDateTimeStamp = DateToTimestamp($StartDateTime, "Asia/Seoul");
				$EndDateTimeStamp =   DateToTimestamp($EndDateTime  , "Asia/Seoul");

				
				$Sql2 = " insert into Classes ( ";
					$Sql2 .= " ClassOrderID, ";
					$Sql2 .= " MemberID, ";
					$Sql2 .= " TeacherID, ";
					$Sql2 .= " TeacherPayPerTime, ";
					$Sql2 .= " StartDateTime, ";
					$Sql2 .= " StartDateTimeStamp, ";
					$Sql2 .= " StartYear, ";
					$Sql2 .= " StartMonth, ";
					$Sql2 .= " StartDay, ";
					$Sql2 .= " StartHour, ";
					$Sql2 .= " StartMinute, ";
					$Sql2 .= " EndDateTime, ";
					$Sql2 .= " EndDateTimeStamp, ";
					$Sql2 .= " EndYear, ";
					$Sql2 .= " EndMonth, ";
					$Sql2 .= " EndDay, ";
					$Sql2 .= " EndHour, ";
					$Sql2 .= " EndMinute, ";
					$Sql2 .= " CommonUseClassIn, ";
					$Sql2 .= " CommonShClassCode, ";
					$Sql2 .= " ClassRegDateTime, ";
					$Sql2 .= " ClassModiDateTime ";
				$Sql2 .= " ) values ( ";
					$Sql2 .= " :ClassOrderID, ";
					$Sql2 .= " :MemberID, ";
					$Sql2 .= " :TeacherID, ";
					$Sql2 .= " :TeacherPayPerTime, ";
					$Sql2 .= " :StartDateTime, ";
					$Sql2 .= " :StartDateTimeStamp, ";
					$Sql2 .= " :StartYear, ";
					$Sql2 .= " :StartMonth, ";
					$Sql2 .= " :StartDay, ";
					$Sql2 .= " :StartHour, ";
					$Sql2 .= " :StartMinute, ";
					$Sql2 .= " :EndDateTime, ";
					$Sql2 .= " :EndDateTimeStamp, ";
					$Sql2 .= " :EndYear, ";
					$Sql2 .= " :EndMonth, ";
					$Sql2 .= " :EndDay, ";
					$Sql2 .= " :EndHour, ";
					$Sql2 .= " :EndMinute, ";
					$Sql2 .= " 0, ";
					$Sql2 .= " :CommonShClassCode, ";
					$Sql2 .= " now(), ";
					$Sql2 .= " now() ";
				$Sql2 .= " ) ";

				$Stmt2 = $DbConn->prepare($Sql2);
				$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
				$Stmt2->bindParam(':MemberID', $MemberID);
				$Stmt2->bindParam(':TeacherID', $TeacherID);
				$Stmt2->bindParam(':TeacherPayPerTime', $TeacherPayPerTime);
				$Stmt2->bindParam(':StartDateTime', $StartDateTime);
				$Stmt2->bindParam(':StartDateTimeStamp', $StartDateTimeStamp);
				$Stmt2->bindParam(':StartYear', $StartYear);
				$Stmt2->bindParam(':StartMonth', $StartMonth);
				$Stmt2->bindParam(':StartDay', $StartDay);
				$Stmt2->bindParam(':StartHour', $StartHour);
				$Stmt2->bindParam(':StartMinute', $StartMinute);
				$Stmt2->bindParam(':EndDateTime', $EndDateTime);
				$Stmt2->bindParam(':EndDateTimeStamp', $EndDateTimeStamp);
				$Stmt2->bindParam(':EndYear', $EndYear);
				$Stmt2->bindParam(':EndMonth', $EndMonth);
				$Stmt2->bindParam(':EndDay', $EndDay);
				$Stmt2->bindParam(':EndHour', $EndHour);
				$Stmt2->bindParam(':EndMinute', $EndMinute);
				$Stmt2->bindParam(':CommonShClassCode', $CommonShClassCode);
				$Stmt2->execute();
				$ClassID = $DbConn->lastInsertId();
				$Stmt2 = null;
				
				//echo $ClassID."/";

				$ClassIDs = $ClassIDs . $ClassID . "|";
			
			}



		}else{

			if ($ClassID!=0){
				$ClassIDs = $ClassIDs . $ClassID . "|";
			}

		}

	}
	$Stmt3 = null;
	// 클래스 가져오기 및 등록 ========================================

						

	// 연기 하기 실행 ========================================
	$ArrClassID = explode("|", $ClassIDs);

	for ($ii=1;$ii<=count($ArrClassID)-2;$ii++){

		$ClassID = $ArrClassID[$ii];

		$Sql = "
			select 
				A.*,
				B.ClassProductID,
				C.MemberPayType, 
				D.CenterPayType  
			from Classes A 
				inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID 
				inner join Members C on A.MemberID=C.MemberID 
				inner join Centers D on C.CenterID=D.CenterID 
			where A.ClassID=$ClassID 
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$ClassAttendState = $Row["ClassAttendState"];
		$ClassOrderID = $Row["ClassOrderID"];
		$MemberID = $Row["MemberID"];
		$TeacherID = $Row["TeacherID"];
		$ClassProductID = $Row["ClassProductID"];
		$CenterPayType = $Row["CenterPayType"]; 
		$MemberPayType = $Row["MemberPayType"];

		//0:미등록 1:출석 2:지각 3:결석 4:학생연기 5:강사연기 6:학생취소 7:강사취소 8:교사변경
		if ($ClassAttendState!=4 && $ClassAttendState!=5 && $ClassAttendState!=6 && $ClassAttendState!=7 && $ClassProductID==1){

			$ClassAttendStateMemberID = 1;
			$ClassAttendState = 5;//강사연기


			$Sql = " update Classes set ";
				$Sql .= " ClassAttendState = :ClassAttendState, ";
				$Sql .= " ClassAttendStateMemberID = :ClassAttendStateMemberID, ";
				$Sql .= " ClassAttendStateMsg='마지막수업 뒤로연기', ";
				$Sql .= " ClassModiDateTime = now() ";
			$Sql .= " where ClassID = :ClassID ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ClassAttendState', $ClassAttendState);
			$Stmt->bindParam(':ClassAttendStateMemberID', $ClassAttendStateMemberID);
			$Stmt->bindParam(':ClassID', $ClassID);
			$Stmt->execute();
			$Stmt = null;


			//=================== 로그 남기기 ==========================
			$ClassOrderSlotLogMemberID = 1;//마스터가 연기한 것으로 처리
			$ClassOrderSlotLogMemo = "마지막수업 뒤로 연기(자동)";
			$Sql = " insert into ClassOrderSlotLogs ( ";
				$Sql .= " ClassOrderID, ";
				$Sql .= " MemberID, ";
				$Sql .= " ClassOrderSlotLogMemo, ";
				$Sql .= " ClassOrderSlotLogRegDateTime ";
			$Sql .= " ) values ( ";
				$Sql .= " :ClassOrderID, ";
				$Sql .= " :MemberID, ";
				$Sql .= " :ClassOrderSlotLogMemo, ";
				$Sql .= " now() ";
			$Sql .= " ) ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
			$Stmt->bindParam(':MemberID', $ClassOrderSlotLogMemberID);
			$Stmt->bindParam(':ClassOrderSlotLogMemo', $ClassOrderSlotLogMemo);
			$Stmt->execute();
			$Stmt = null;
			//=================== 로그 남기기 ==========================


			if ($CenterPayType==2 || ($CenterPayType==1 && $MemberPayType==1)){

				$Sql = "
					select
						A.ClassOrderEndDate
					from ClassOrders A 
					where ClassOrderID=$ClassOrderID 
				";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$ClassOrderEndDate = $Row["ClassOrderEndDate"];

				
				if ($ClassOrderEndDate!="" && $ClassOrderEndDate!="0000-00-00"){

					$ClassOrderEndDateWeek = date("w", strtotime($ClassOrderEndDate));
					
					
					$ExistStudyWeek[0] = 0;
					$ExistStudyWeek[1] = 0;
					$ExistStudyWeek[2] = 0;
					$ExistStudyWeek[3] = 0;
					$ExistStudyWeek[4] = 0;
					$ExistStudyWeek[5] = 0;
					$ExistStudyWeek[6] = 0;
					
					$Sql = "
							select 
								A.*
							from ClassOrderSlots A 
							where A.ClassOrderID=$ClassOrderID and A.ClassOrderSlotType=1 and A.ClassOrderSlotMaster=1 and A.ClassOrderSlotEndDate is null 
							order by A.StudyTimeWeek asc
					";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					while($Row = $Stmt->fetch()) {
						
						$StudyTimeWeek = $Row["StudyTimeWeek"];
						$ExistStudyWeek[$StudyTimeWeek] = 1;

					}
					$Stmt = null;

					$ResetClassOrderEndDate = $ClassOrderEndDate;
					$SetLastDate = 0;
					$ListWeekNum = $ClassOrderEndDateWeek+1;
					$ListNum = 1;

					
					while($SetLastDate==0){
						
						if ($ListWeekNum>6){
							$ListWeekNum2 = $ListWeekNum - 7;
						}else{
							$ListWeekNum2 = $ListWeekNum;
						}

						//echo "{".$ListWeekNum2."/".$ExistStudyWeek[$ListWeekNum2]."}<br><br>";

						if ($ExistStudyWeek[$ListWeekNum2]==1){
							$ResetClassOrderEndDate = date("Y-m-d", strtotime($ClassOrderEndDate. " + ".$ListNum." days"));
							$SetLastDate=1;
						}

						$ListWeekNum++;
						$ListNum++;
					}

				}


				$Sql2 = "update ClassOrders set 
							LastClassOrderEndDate=ClassOrderEndDate,
							ClassOrderEndDate=:ClassOrderEndDate, 
							ClassOrderModiDateTime=now() 
						where ClassOrderID=:ClassOrderID";
				$Stmt2 = $DbConn->prepare($Sql2);
				$Stmt2->bindParam(':ClassOrderEndDate', $ResetClassOrderEndDate);
				$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
				$Stmt2->execute();
				$Stmt2 = null;

				//종료일 로그 남기기 =======================================
				$ClassOrderEndDateLogFileQueryNum = 1;
				$Sql_EndDateLog = " insert into ClassOrderEndDateLogs ( ";
					$Sql_EndDateLog .= " ClassOrderID, ";
					$Sql_EndDateLog .= " ClassOrderEndDate, ";
					$Sql_EndDateLog .= " ClassOrderEndDateLogFileName, ";
					$Sql_EndDateLog .= " ClassOrderEndDateLogFileQueryNum, ";
					$Sql_EndDateLog .= " ClassOrderEndDateLogRegDateTime ";
				$Sql_EndDateLog .= " ) values ( ";
					$Sql_EndDateLog .= " :ClassOrderID, ";
					$Sql_EndDateLog .= " :ClassOrderEndDate, ";
					$Sql_EndDateLog .= " :ClassOrderEndDateLogFileName, ";
					$Sql_EndDateLog .= " :ClassOrderEndDateLogFileQueryNum, ";
					$Sql_EndDateLog .= " now() ";
				$Sql_EndDateLog .= " ) ";
				$Stmt_EndDateLog = $DbConn->prepare($Sql_EndDateLog);
				$Stmt_EndDateLog->bindParam(':ClassOrderID', $ClassOrderID);
				$Stmt_EndDateLog->bindParam(':ClassOrderEndDate', $ResetClassOrderEndDate);
				$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileName', $ClassOrderEndDateLogFileName);
				$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileQueryNum', $ClassOrderEndDateLogFileQueryNum);
				$Stmt_EndDateLog->execute();
				$Stmt_EndDateLog = null;
				//종료일 로그 남기기 =======================================


			}

		}

	}
	// 연기 하기 실행 ========================================


}









?>
