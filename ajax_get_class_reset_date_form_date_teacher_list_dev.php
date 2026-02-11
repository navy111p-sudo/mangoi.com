<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$ErrNum = 0;
$ErrMsg = "";

$EduCenterID = isset($_REQUEST["EduCenterID"]) ? $_REQUEST["EduCenterID"] : "";
$StudyTimeDate = isset($_REQUEST["StudyTimeDate"]) ? $_REQUEST["StudyTimeDate"] : "";//지정날짜
$StudyTimeHour = isset($_REQUEST["StudyTimeHour"]) ? $_REQUEST["StudyTimeHour"] : "";//지정시
$StudyTimeMinute = isset($_REQUEST["StudyTimeMinute"]) ? $_REQUEST["StudyTimeMinute"] : "";//지정분
$ClassOrderTimeTypeID = isset($_REQUEST["ClassOrderTimeTypeID"]) ? $_REQUEST["ClassOrderTimeTypeID"] : "";//사용안함
$ResetType = isset($_REQUEST["ResetType"]) ? $_REQUEST["ResetType"] : "";
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";


//================================================================
// 7일 이하동안 유지되는 슬랏 중에 그 기간내에 수업이 없는 슬랏을 삭제 처리
$Sql = "SELECT 
					distinct ClassOrderSlotID 
				FROM View_ClassOrderSlotDelTargets 
				WHERE 
					ClassOrderSlotID NOT in (SELECT ClassOrderSlotID FROM View_ClassOrderSlotDelTargets WHERE ClassOrderSlotWeek=StudyWeek)";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

while($Row = $Stmt->fetch()) {

	$DelClassOrderSlotID = $Row["ClassOrderSlotID"];

	$Sql2 = "
		update ClassOrderSlots set 
			ClassOrderSlotState=0,
			DelAdminUnder7Day=1,
			DelAdminUnder7DayDateTime=now(),
			ClassOrderSlotDateModiDateTime=now()
		where ClassOrderSlotID=$DelClassOrderSlotID
	";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2 = null;

}
$Stmt = null;				

//================================================================



$ClassOrderTimeSlotCount = $ClassOrderTimeTypeID;

$ResetTimeWeek = date('w', strtotime($StudyTimeDate));
$ArrWeekDayStr = explode(",","일요일,월요일,화요일,수요일,목요일,금요일,토요일");
$WeekDayStr = $ArrWeekDayStr[$ResetTimeWeek];


$SearchStartHour =   (int)date("H", strtotime("1970-01-01 ".substr("0".$StudyTimeHour,-2).":".substr("0".$StudyTimeMinute,-2).":00") - 60 * 100);//100분 빼기
$SearchEndHour =     (int)date("H", strtotime("1970-01-01 ".substr("0".$StudyTimeHour,-2).":".substr("0".$StudyTimeMinute,-2).":00") + 60 * 100);//100분 더하기


if ($ResetType=="ChTeacher"){
	$StrResetType = "강사변경";
}else if ($ResetType=="PlusClass"){
	$StrResetType = "보강등록";
}else if ($ResetType=="EverChange"){
	$StrResetType = "스케줄변경";
}else{
	$StrResetType = "연기";
}
 

$ResetYear = date('Y', strtotime($StudyTimeDate));
$ResetMonth = date('m', strtotime($StudyTimeDate));
$ResetDay = date('d', strtotime($StudyTimeDate));


// 에듀센터 휴무, 설날, 크리스마스 등등
$Sql = "
		select 
				A.EduCenterHolidayID
		from EduCenterHolidays A 
		where 
			A.EduCenterID=$EduCenterID 
			and datediff(A.EduCenterHolidayDate, '".$StudyTimeDate."')=0 
			and A.EduCenterHolidayState=1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null; 
$EduCenterHolidayID = $Row["EduCenterHolidayID"];

$TempEduCenterHoliday = 0;
if ($EduCenterHolidayID){
	$TempEduCenterHoliday = 1;
}
// 에듀센터 휴무, 설날, 크리스마스 등등




//교육센터 정기휴일 검색
$Sql = "
		select 
				A.*
		from EduCenters A 
		where A.EduCenterID=$EduCenterID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;


$EduCenterStartHour = $Row["EduCenterStartHour"];
$EduCenterEndHour = $Row["EduCenterEndHour"];


$EduCenterHoliday[0] = $Row["EduCenterHoliday0"];
$EduCenterHoliday[1] = $Row["EduCenterHoliday1"];
$EduCenterHoliday[2] = $Row["EduCenterHoliday2"];
$EduCenterHoliday[3] = $Row["EduCenterHoliday3"];
$EduCenterHoliday[4] = $Row["EduCenterHoliday4"];
$EduCenterHoliday[5] = $Row["EduCenterHoliday5"];
$EduCenterHoliday[6] = $Row["EduCenterHoliday6"];

$WorkDayCount = 7;

$WeekDayNum = $ResetTimeWeek;//지정한 날짜만 나온다
if ($EduCenterHoliday[$WeekDayNum]==1){
	$WorkDayCount--;
}
//교육센터 정기휴일 검색


//교육센터 브레이크 타임 검색
for ($HourNum=0;$HourNum<=24-1;$HourNum++){
	for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){
		
		$WeekDayNum = $ResetTimeWeek;//지정한 날짜만 나온다
		if ($EduCenterHoliday[$WeekDayNum]==0) {
			$EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum] = 1;//[요일/시/분] 1은 수업가능
		}

	}
}

$Sql2 = "select 
				A.* 
		from EduCenterBreakTimes A 
		where A.EduCenterID=$EduCenterID and A.EduCenterBreakTimeState=1 
		order by A.EduCenterBreakTimeWeek asc, A.EduCenterBreakTimeHour asc, A.EduCenterBreakTimeMinute asc";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

while($Row2 = $Stmt2->fetch()) {
	$EduCenterBreakTimeWeek = $Row2["EduCenterBreakTimeWeek"];
	$EduCenterBreakTimeHour = $Row2["EduCenterBreakTimeHour"];
	$EduCenterBreakTimeMinute = $Row2["EduCenterBreakTimeMinute"];
	$EduCenterBreakTimeType = $Row2["EduCenterBreakTimeType"];
	
	$EduCenterBreak[$EduCenterBreakTimeWeek][$EduCenterBreakTimeHour][$EduCenterBreakTimeMinute] = $EduCenterBreakTimeType;
}
$Stmt2 = null;

//교육센터 브레이크 타임 검색




$AddSqlWhere = "1=1";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherState=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherView=1 ";
$AddSqlWhere = $AddSqlWhere . " and B.EduCenterID=$EduCenterID ";
$AddSqlWhere = $AddSqlWhere . " and B.TeacherGroupState=1 ";
if($_LINK_MEMBER_LEVEL_ID_> 4) { // 3 : 프랜차이즈장, 4 : 프랜차이즈 직원, 5 : 영업
	$AddSqlWhere = $AddSqlWhere . " and ( A.TeacherGroupID=4 or A.TeacherGroupID=9 ) ";
}


//출퇴근 시간=== 반드시 필요함, 아래쪽 강사별로 가져오는 시작, 종료 시간과는 별개임 
$Sql = "select 
				A.*
		from Teachers A 
			inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
		where ".$AddSqlWhere."
		order by B.TeacherGroupOrder asc, A.TeacherOrder asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ArrTeacherStartHour = [];
$ArrTeacherEndHour = [];

$TeacherNum=1;
while($Row = $Stmt->fetch()) {
	$ArrTeacherStartHour[$Row["TeacherID"]] = $Row["TeacherStartHour"];
	$ArrTeacherEndHour[$Row["TeacherID"]] = $Row["TeacherEndHour"];
	$TeacherNum++;
}
$Stmt = null;
//출퇴근 시간=== 반드시 필요함, 아래쪽 강사별로 가져오는 시작, 종료 시간과는 별개임 


$Sql = "select 
				A.* 
		from Teachers A 
			inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
			inner join Members C on A.TeacherID=C.TeacherID 
		where ".$AddSqlWhere."  
		order by A.TeacherOrder asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


$TeacherNum = 1;
$TeacherListHTML = "";
$TeacherSlotListHTML = "";
$ForceBlockSlotIDs = "|";

while($Row = $Stmt->fetch()) {
	
	//$SelectFranchiseID = $Row2["FranchiseID"];

	$TeacherID = $Row["TeacherID"];
	$TeacherGroupID = $Row["TeacherGroupID"];
	$TeacherPayTypeItemID = $Row["TeacherPayTypeItemID"];
	$TeacherName = $Row["TeacherName"];
	$TeacherNickName = $Row["TeacherNickName"];
	$TeacherImageFileName = $Row["TeacherImageFileName"];
	$TeacherZip = $Row["TeacherZip"];
	$TeacherAddr1 = $Row["TeacherAddr1"];
	$TeacherAddr2 = $Row["TeacherAddr2"];
	$TeacherVideoType = $Row["TeacherVideoType"];
	$TeacherVideoCode = $Row["TeacherVideoCode"];
	$TeacherIntroText = $Row["TeacherIntroText"];
	$TeacherPayPerTime = $Row["TeacherPayPerTime"];
	$TeacherState = $Row["TeacherState"];
	$TeacherView = $Row["TeacherView"];
	$TeacherIntroEdu = $Row["TeacherIntroEdu"];
	$TeacherIntroSpec = $Row["TeacherIntroSpec"];
	$TeacherStartHour = $Row["TeacherStartHour"];
	$TeacherEndHour = $Row["TeacherEndHour"];
	$TeacherBlock80Min = $Row["TeacherBlock80Min"];


	//강사 브레이크 타임 검색
	for ($HourNum=0;$HourNum<=24-1;$HourNum++){
		for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){
			
			//for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){
			$WeekDayNum = $ResetTimeWeek;//지정한 날짜만 나온다
			if ($EduCenterHoliday[$WeekDayNum]==0) {
				$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 0;//테이블구성 === KKK
				$TeacherBreak[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;//[요일/시/분] 1은 수업가능
				$ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 0;//[강사순번/요일/시/분] 1은 수업가능, 80분 연속수업 검색용
			}
			//}

		}
	}



	$Sql2 = "select 
					A.* 
			from TeacherBreakTimes A 
			where A.TeacherID=$TeacherID and A.TeacherBreakTimeState=1 
			order by A.TeacherBreakTimeWeek asc, A.TeacherBreakTimeHour asc, A.TeacherBreakTimeMinute asc";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

	while($Row2 = $Stmt2->fetch()) {
		$TeacherBreakTimeWeek = $Row2["TeacherBreakTimeWeek"];
		$TeacherBreakTimeHour = $Row2["TeacherBreakTimeHour"];
		$TeacherBreakTimeMinute = $Row2["TeacherBreakTimeMinute"];
		$TeacherBreakTimeType = $Row2["TeacherBreakTimeType"];
		
		$TeacherBreak[$TeacherNum][$TeacherBreakTimeWeek][$TeacherBreakTimeHour][$TeacherBreakTimeMinute] = $TeacherBreakTimeType;//[강사순번/요일/시/분] 1은 수업가능
	
	}
	$Stmt2 = null;
	//강사 브레이크 타임 검색
	
	
	$WeekDayNum = $ResetTimeWeek;//지정한 날짜만 나온다
	
	
	// 강사 자체 휴무
	$Sql5 = "
			select 
					A.TeacherHolidayID
			from TeacherHolidays A 
			where 
				A.TeacherID=$TeacherID 
				and datediff(A.TeacherHolidayDate, '".$StudyTimeDate."')=0 
				and A.TeacherHolidayState=1";
	$Stmt5 = $DbConn->prepare($Sql5);
	$Stmt5->execute();
	$Stmt5->setFetchMode(PDO::FETCH_ASSOC);
	$Row5 = $Stmt5->fetch();
	$Stmt5 = null; 
	$TeacherHolidayID = $Row5["TeacherHolidayID"];

	if ($TeacherHolidayID){
		$TempEduCenterHoliday = 1;
	}
	// 강사 자체 휴무


	if ($TempEduCenterHoliday==1) {
		$EduCenterHoliday[$WeekDayNum] = 1;
	}

	if ($EduCenterHoliday[$WeekDayNum]==0){
	

		$SelectedHourNum = $StudyTimeHour;
		$SelectedMinuteNum = $StudyTimeMinute;		

		$MinuteListNum=1;
		$OldMinuteListNum = 0;
		
		$CheckMinuteListNum = 0;//기준 MinuteListNum (해당 요일의 시작 시점 MinuteListNum)

		
		//==== 검색범위 좁히기 
		//if ($TeacherEndHour>$SearchEndHour+1){//100분을 더해준 값이 10:40분 이라면 11시 비교
		//	$TeacherEndHour = $SearchEndHour+2;//11시에서 1시간 더 설정하여 슬랏을 채움
		//}
		//$TeacherEndHour = $TeacherEndHour + 1;
		//if ($TeacherEndHour>23){
		//	$TeacherEndHour = 23;
		//}

		//if ($TeacherStartHour<$SearchStartHour){
		//	$TeacherStartHour = $SearchStartHour;
		//}
		//==== 검색범위 좁히기 

		//if ($TeacherStartHour>$SearchEndHour+1){//=====================kkkkkkk
			//강사 출근시간이 검색범위 밖이다.
		//}else {//=====================kkkkkkk



				//echo $TeacherStartHour ."~~~" . $TeacherEndHour;
				
				//==== 검색범위 좁히기 

				for ($HourNum=$TeacherStartHour;$HourNum<$TeacherEndHour;$HourNum++){

					for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){


						$SlotBreakEventCode = 0;
						$ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 0;//수업아님, 80분 연속수업 검색용

						$SlotBreakEventType = 0;//1 이면 ClassOrderSlots 기반 === KKK
						$TempClassOrderSlotMaster = 0;//1 이면 ClassOrderSlots 기반 === KKK

						$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 0;

						$TargetDate = $StudyTimeDate;
						$TempYear = date('Y', strtotime($TargetDate));
						$TempMonth = date('m', strtotime($TargetDate));
						$TempDay = date('d', strtotime($TargetDate));

						//내 강좌 중에 같은 시간 강좌가 있으면 패스
						$Sql3 = "select 
										count(*) as ClassOrderSlotCount  
								from ClassOrderSlots COS 
									inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
								where COS.ClassOrderID=$ClassOrderID
									and COS.ClassOrderSlotMaster=1 
									and COS.ClassOrderSlotState=1
									and COS.ClassOrderSlotType=1 
									and COS.TeacherID=$TeacherID 
									and COS.StudyTimeWeek=$WeekDayNum 
									and COS.StudyTimeHour=$HourNum
									and COS.StudyTimeMinute=$MinuteNum 
									and ( 
												( COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 ) 

												or 
												( datediff(COS.ClassOrderSlotStartDate, '".$TargetDate."')<=0 and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 ) 

												or 
												( COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$TargetDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 ) 

												or 
												( datediff(COS.ClassOrderSlotStartDate, '".$TargetDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$TargetDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 ) 
												
										)  
									
						";


						$Stmt3 = $DbConn->prepare($Sql3);
						$Stmt3->execute();
						$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
						$Row3 = $Stmt3->fetch();
						$ClassOrderSlotCount = $Row3["ClassOrderSlotCount"];

						if ($ClassOrderSlotCount>0){
							$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] =1;
						}


						
						if ($SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] ==0 ){//해당날짜 정규 수업 검색
							
							if ($ResetType=="EverChange"){
								$SqlWhere3 = "  ";
								$SqlWhere3_1 = "  ";
							}else{
								$SqlWhere3 = " and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0  "; 
								$SqlWhere3 .= " and ( CLS.ClassAttendState is NULL or CLS.ClassAttendState<4 ) ";

								$SqlWhere3_1 = " and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0  "; 
								
							}
							//$SqlWhere3 .= " and (datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0 or CO.ClassOrderEndDate is null) "; 제거 2019-12-22
							//$SqlWhere3_1 .= " and (datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0 or CO.ClassOrderEndDate is null) "; 제거 2019-12-22

							$Sql3 = "select 
											count(*) as ClassOrderSlotCount
									from ClassOrderSlots COS 
										inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
										inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 

										left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$TempYear." and CLS.StartMonth=".$TempMonth." and CLS.StartDay=".$TempDay." and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID and CLS.ClassAttendState<>99 
									where 
											COS.StudyTimeWeek=".$WeekDayNum." 
											and COS.StudyTimeHour=".$HourNum." 
											and COS.StudyTimeMinute=".$MinuteNum." 
											and COS.TeacherID=".$TeacherID." 
											and COS.ClassOrderSlotType=1 
											and (

												COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL 
												or
												datediff(COS.ClassOrderSlotStartDate, '".$TargetDate."')<=0 and COS.ClassOrderSlotEndDate is NULL 
												or
												COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$TargetDate."')>=0 
												or
												datediff(COS.ClassOrderSlotStartDate, '".$TargetDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$TargetDate."')>=0  
											)
											
											".$SqlWhere3."

											

											and COS.ClassOrderSlotState=1 
											and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0) )
							";


							$Stmt3 = $DbConn->prepare($Sql3);
							$Stmt3->execute();
							$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
							$Row3 = $Stmt3->fetch();
							$ClassOrderSlotCount = $Row3["ClassOrderSlotCount"];

							
							if ($ClassOrderSlotCount>0) {
								$SlotBreakEventType = 1; // === KKK
								$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;
								$SlotBreakEventCode = 11;
								$ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;//수업, 80분 연속수업 검색용



								$Sql3 = "select 
												COS.ClassOrderSlotMaster,
												CO.ClassOrderID,
												CO.ClassOrderTimeTypeID,
												ifnull(CLS.ClassAttendState, 0) as ClassAttendState
										from ClassOrderSlots COS 
											inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
											inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 

											left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$TempYear." and CLS.StartMonth=".$TempMonth." and CLS.StartDay=".$TempDay." and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID and CLS.ClassAttendState<>99 
										where 
												COS.StudyTimeWeek=".$WeekDayNum." 
												and COS.StudyTimeHour=".$HourNum." 
												and COS.StudyTimeMinute=".$MinuteNum." 
												and COS.TeacherID=".$TeacherID." 
												and COS.ClassOrderSlotType=1 
												and (

													COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL 
													or
													datediff(COS.ClassOrderSlotStartDate, '".$TargetDate."')<=0 and COS.ClassOrderSlotEndDate is NULL 
													or
													COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$TargetDate."')>=0 
													or
													datediff(COS.ClassOrderSlotStartDate, '".$TargetDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$TargetDate."')>=0  
												)
												
												".$SqlWhere3."

												and COS.ClassOrderSlotState=1 
												and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0) )
								";


								$Stmt3 = $DbConn->prepare($Sql3);
								$Stmt3->execute();
								$Stmt3->setFetchMode(PDO::FETCH_ASSOC);

								$jjjj = 0;
								$kkkk = 0;
								while($Row3 = $Stmt3->fetch()) {
									$TempClassOrderSlotMaster = $Row3["ClassOrderSlotMaster"]; // === KKK
									$TempClassOrderID = $Row3["ClassOrderID"];
									$TempClassOrderTimeTypeID = $Row3["ClassOrderTimeTypeID"];

									if ($ClassOrderID!=$TempClassOrderID){
										$jjjj++;
									}else{
										
										$TempClassAttendState = 0;

										//echo $TempClassOrderTimeTypeID."%%";

										for ($cccc=0;$cccc<=$TempClassOrderTimeTypeID-1;$cccc++){

											$SubHourNum = $HourNum; 
											$SubMinuteNum = $MinuteNum - ($cccc * 10);
											
											if ($SubMinuteNum<0){
												$SubMinuteNum = 60 + $SubMinuteNum;
												$SubHourNum = $SubHourNum - 1;
											}

											
											
											$Sql4 = "select 
															COS.ClassOrderSlotMaster,
															CO.ClassOrderID,
															ifnull(CLS.ClassAttendState, 0) as ClassAttendState
													from ClassOrderSlots COS 
														inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
														inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 

														left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$TempYear." and CLS.StartMonth=".$TempMonth." and CLS.StartDay=".$TempDay." and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID 
													where 
															CO.ClassOrderID=".$TempClassOrderID." 
															and COS.StudyTimeWeek=".$WeekDayNum." 
															and COS.StudyTimeHour=".$SubHourNum." 
															and COS.StudyTimeMinute=".$SubMinuteNum." 
															and COS.TeacherID=".$TeacherID." 
															and COS.ClassOrderSlotType=1 
															and (

																COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL 
																or
																datediff(COS.ClassOrderSlotStartDate, '".$TargetDate."')<=0 and COS.ClassOrderSlotEndDate is NULL 
																or
																COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$TargetDate."')>=0 
																or
																datediff(COS.ClassOrderSlotStartDate, '".$TargetDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$TargetDate."')>=0  
															)
															
															".$SqlWhere3_1."

															and COS.ClassOrderSlotState=1 
															and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0) )
											";

											//if ($TargetDate=="2019-12-19" && $TeacherID==47 && $SubHourNum==17){
											//	echo $Sql4 . "\n\n";
											//}

											$Stmt4 = $DbConn->prepare($Sql4);
											$Stmt4->execute();
											$Stmt4->setFetchMode(PDO::FETCH_ASSOC);
											$Row4 = $Stmt4->fetch();
											$Stmt4 = null;
											

											if ($TempClassAttendState!=99 && $Row4["ClassAttendState"]>=4 && $Row4["ClassAttendState"]<=8 && $Row4["ClassOrderSlotMaster"]==1){
												$TempClassAttendState = 99;
											}
										
										}
										
										
										if ($TempClassOrderSlotMaster==0 && $TempClassAttendState==99){
											$kkkk++;
										}
									}

								}
								$Stmt3 = null;

								if ($kkkk>0){
									$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 0;
								}
								
								if ( $jjjj>0 ){
									$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;
								}

							}

						}



						if ($SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] ==0 ){//해당날짜 임시 수업 검색

							$TargetDate = $StudyTimeDate;
							$TempYear = date('Y', strtotime($TargetDate));
							$TempMonth = date('m', strtotime($TargetDate));
							$TempDay = date('d', strtotime($TargetDate));

							if ($ResetType=="EverChange"){
								$SqlWhere3 = " and datediff(COS.ClassOrderSlotDate,'".$TargetDate."')>=0 ";
								$SqlFrom3 = " and datediff(CLS.StartDateTime,'".$TargetDate."')>=0 ";
							}else{
								$SqlWhere3 = " and datediff(COS.ClassOrderSlotDate,'".$TargetDate."')=0 ";
								$SqlFrom3 = " and CLS.StartYear=".$TempYear." and CLS.StartMonth=".$TempMonth." and CLS.StartDay=".$TempDay." ";
							}
							
							$Sql3 = "select 
											count(*) as ClassOrderSlotCount
									from ClassOrderSlots COS 
										inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
										inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 
										inner join Members MB on CO.MemberID=MB.MemberID 

										left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID ".$SqlFrom3." and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID 
									where 
											COS.StudyTimeWeek=".$WeekDayNum." 
											and COS.StudyTimeHour=".$HourNum." 
											and COS.StudyTimeMinute=".$MinuteNum." 
											and COS.TeacherID=".$TeacherID." 
											".$SqlWhere3."
											and COS.ClassOrderSlotType=2 
											and ( CLS.ClassAttendState is NULL or CLS.ClassAttendState<4 )
											and COS.ClassOrderSlotState=1 
											and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4) 
											
							"; 

							$Stmt3 = $DbConn->prepare($Sql3);
							$Stmt3->execute();
							$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
							$Row3 = $Stmt3->fetch();
							$Stmt3 = null;
							$ClassOrderSlotCount = $Row3["ClassOrderSlotCount"];

							if ($ClassOrderSlotCount>0){
								$SlotBreakEventType = 1; // === KKK
								$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;
								$BgColor = "#CC99FF";
								$FontColor = "#FFFFFF";
								$SlotBreakEventName = "수업";
								$SlotBreakEventCode = 11;
								$ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;//레벨테스트, 80분 연속수업 검색용


								$Sql3 = "select 
												COS.ClassOrderSlotMaster
										from ClassOrderSlots COS 
											inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
											inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 
											inner join Members MB on CO.MemberID=MB.MemberID 

											left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID ".$SqlFrom3." and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID 
										where 
												COS.StudyTimeWeek=".$WeekDayNum." 
												and COS.StudyTimeHour=".$HourNum." 
												and COS.StudyTimeMinute=".$MinuteNum." 
												and COS.TeacherID=".$TeacherID." 
												".$SqlWhere3."
												and COS.ClassOrderSlotType=2 
												and ( CLS.ClassAttendState is NULL or CLS.ClassAttendState<4 )
												and COS.ClassOrderSlotState=1 
												and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4) 
												
								"; 

								$Stmt3 = $DbConn->prepare($Sql3);
								$Stmt3->execute();
								$Stmt3->setFetchMode(PDO::FETCH_ASSOC);


								while($Row3 = $Stmt3->fetch()) {
									$TempClassOrderSlotMaster = $Row3["ClassOrderSlotMaster"]; // === KKK
								}
								$Stmt3 = null;

							}

						}
						

						if ($SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] ==0 ){//식사,휴식,블락(임시) 검색

							$TargetDate = $StudyTimeDate;
							$TempStartHourNum = $HourNum;
							$TempStartMinuteNum = $MinuteNum;
							$TempEndHourNum = $HourNum;
							$TempEndMinuteNum = $MinuteNum+10;
							
							if ($TempEndMinuteNum==60){
								$TempEndHourNum = $TempEndHourNum + 1;
								$TempEndMinuteNum = 0;
							}
							
							$Sql3 = "select 
											A.TeacherBreakTimeTempID,
											A.TeacherBreakTimeTempType
									from TeacherBreakTimeTemps A 

									where 
											A.TeacherBreakTimeTempWeek=$WeekDayNum 
											and A.TeacherID=".$TeacherID." 
											and A.TeacherBreakTimeTempState=1 
											and datediff(A.TeacherBreakTimeTempStartDate, '".$TargetDate."')<=0 
											and datediff(A.TeacherBreakTimeTempEndDate, '".$TargetDate."')>=0 
											and time_to_sec(timediff(A.TeacherBreakTimeTempStartTime, '".$TempStartHourNum.":".$TempStartMinuteNum."'))<=0 
											and time_to_sec(timediff(A.TeacherBreakTimeTempEndTime, '".$TempEndHourNum.":".$TempEndMinuteNum."'))>=0 

							";

							//echo $Sql;

							$Stmt3 = $DbConn->prepare($Sql3);
							$Stmt3->execute();
							$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
							$Row3 = $Stmt3->fetch();
							$Stmt3 = null;
							$TeacherBreakTimeTempID = $Row3["TeacherBreakTimeTempID"];
							$TeacherBreakTimeTempType = $Row3["TeacherBreakTimeTempType"];

							if ($TeacherBreakTimeTempID){
								if ($TeacherBreakTimeTempType==2) {
									$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;
									$BgColor = "#FFCC00";
									$FontColor = "#FFFFFF";
									$SlotBreakEventName = "식사";
									$SlotBreakEventCode = 61;
								}else if ($TeacherBreakTimeTempType==3) {
									$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;
									$BgColor = "#CC9933";
									$FontColor = "#FFFFFF";
									$SlotBreakEventName = "휴식";
									$SlotBreakEventCode = 71;
								}else if ($TeacherBreakTimeTempType==4) {
									$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;
									$BgColor = "#CC6666";
									$FontColor = "#FFFFFF";
									$SlotBreakEventName = "블락";
									$SlotBreakEventCode = 81;
								}
							}

						}


						//출퇴근 시간
						if ($HourNum < $ArrTeacherStartHour[$TeacherID] || $HourNum >= $ArrTeacherEndHour[$TeacherID] ){
							$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;
							$SlotBreakEventCode = 81;
						}
						//출퇴근 시간

						$SlotStatus[$TeacherID][$MinuteListNum] = 100;//수업가능(빈슬랏)

						if ($EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum]==1 && $TeacherBreak[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum]==1 && $SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum]==0){
							
							$TeacherSlotListHTML .= "<span id=\"Div_Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" class=\"teacher_time\" onclick=\"SelectSlot(".$TeacherID.",".$HourNum.",".$MinuteNum.", ".$WeekDayNum.", ".$MinuteListNum.", ".$TeacherBlock80Min.");\">".substr("0".$HourNum,-2).":".substr("0".$MinuteNum,-2)."";

								$TeacherSlotListHTML .= "<input type=\"hidden\" name=\"Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"|".$TeacherID."_".$WeekDayNum."_".$HourNum."_".$MinuteNum."\">";
								$TeacherSlotListHTML .= "<input type=\"hidden\" name=\"Able_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Able_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"1\" style=\"background-color:#cccccc;\">";
								$TeacherSlotListHTML .= "<input type=\"hidden\" name=\"Break_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Break_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"".$SlotBreakEventCode."\">";
							
							$TeacherSlotListHTML .= "</span>";

							$SlotStatus[$TeacherID][$MinuteListNum] = 100;//수업가능(빈슬랏)

						}else{

							//$SlotBreakEventCode = 100;//이곳에서는 큰 의미가 없어 100으로 통일
							
							$TeacherSlotListHTML .= "<span id=\"Div_Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" class=\"teacher_time\" style=\"background-color:#bbbbbb;cursor:default;display:none;\">".substr("0".$HourNum,-2).":".substr("0".$MinuteNum,-2)."";

								$TeacherSlotListHTML .= "<input type=\"hidden\" name=\"Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"0\">";
								$TeacherSlotListHTML .= "<input type=\"hidden\" name=\"Able_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Able_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"0\" style=\"background-color:#ff0000;\">";
								$TeacherSlotListHTML .= "<input type=\"hidden\" name=\"Break_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Break_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"".$SlotBreakEventCode."\">";

							$TeacherSlotListHTML .= "</span>";
							
							if ($MinuteListNum-$OldMinuteListNum!=1){//연속될 경우 보여주지 않는다.
								//$TeacherSlotListHTML .= "<span id=\"Div_Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" class=\"teacher_time\" style=\"cursor:default;\">-</span>";
							}
							$OldMinuteListNum = $MinuteListNum;

							$SlotStatus[$TeacherID][$MinuteListNum] = $SlotBreakEventCode;//이벤트 번호 11은 수업

						}


						if ($SelectedHourNum==$HourNum && $SelectedMinuteNum==$MinuteNum){ // 선택한 시간과 동일하고 20분, 40분 수업 연속 체크
							$CheckMinuteListNum = $MinuteListNum;
						}

						//echo $MinuteListNum."/";
						$MinuteListNum++;
					
					}
				
					
					
				}


				//여기서 검증

				$SlotStatus[$TeacherID][0] = 0;//맨위 슬랏 위의 가상 슬랏 초기화
				$DenySelect = 0;//선택가능 기본값

				for ($iiii=1;$iiii<=$ClassOrderTimeSlotCount;$iiii++){
					//echo $iiii . ":" . "~". ($CheckMinuteListNum + ($iiii-1)) . "/";
					if (isset($SlotStatus[$TeacherID][$CheckMinuteListNum + ($iiii-1)]) && $SlotStatus[$TeacherID][$CheckMinuteListNum + ($iiii-1)]!=100){
						$DenySelect = 1;//시작하는 시점부터 20,40분 수업으로 진행중 다른 수업이 있음.
					}
				}


				if ($DenySelect == 0 && $TeacherBlock80Min==1){//원하는 시간 모두 선택가능할때 - 이렇게 추가할경우 80분 연속강의에 위배되지 않는지 검사(풀스캔)
					
					//위쪽으로 현재 선택을 포함하여 최대 9단계 올라가본다.
					//올라가면서 빈슬랏이 나오면 빈슬랏의 번호를 딴다.
					$EmptyNum = 100;//일단 100으로 한다. 100은 아직 빈슬랏을 못찾은 경우이다.
					for ($iiii=-1;$iiii>=($ClassOrderTimeSlotCount-9) ;$iiii--){

						if ($EmptyNum==100){
						
							$ii = $CheckMinuteListNum + $iiii;

							if ($ii>=0){
								if (isset($SlotStatus[$TeacherID][$ii]) && $SlotStatus[$TeacherID][$ii]!=11){
									$EmptyNum = $ii+1;//수업이 아닌 슬랏 바로 아래 슬랏
								}
							}

						}

					}

					$ActiveSlotCount = 0;


					if ($EmptyNum==100){//수업이 아닌 슬랏을 못찾았다면 80분 위배이다.
						$DenySelect = 2;
					}else{
						
						//위에서 찾은 빈슬랏 바로 아래 부터 9개를 살펴본다.
						for ($iiii=$EmptyNum;$iiii<=(8+$EmptyNum);$iiii++)	{
							
							//현재 선택한 슬랏은 수업이 아닌 슬랏이 아님으로 취급한다.
							if ( ($iiii-$CheckMinuteListNum)>=0 && ($iiii-$CheckMinuteListNum)<=($ClassOrderTimeSlotCount-1) ){
								$ActiveSlotCount++;
							}else{
								
								//if ($SlotStatus[$TeacherID][$iiii]==11){//수업이면
								if ( (isset($SlotStatus[$TeacherID][$iiii]) && $SlotStatus[$TeacherID][$iiii]==11) || isset($SlotStatus[$TeacherID][$iiii])==false ){//수업이면
									$ActiveSlotCount++;
								}
							}
						}

						//채워진 슬랏이 8개 초과이면 80분 위배이다
						if ($ActiveSlotCount>8){
							$DenySelect = 2;
						}

					}

				}


				if ($DenySelect==0){//해당요일이 조건에 맞음

					if ($TeacherImageFileName==""){
						$StrTeacherImageFileName = "/images/no_photo_2.png";
					}else{
						$StrTeacherImageFileName = "/uploads/teacher_images/".$TeacherImageFileName;
					}


					$TeacherListHTML .= "<li>";
					$TeacherListHTML .= "	<div class=\"lms_teacher_photo_wrap\">";
					$TeacherListHTML .= "		<img src=\"".$StrTeacherImageFileName."\" alt=\"jed\" class=\"lms_teacher_photo\">";
					$TeacherListHTML .= "		<div class=\"lms_teacher_photo_name\">".$TeacherName."</div>";  
					$TeacherListHTML .= "	</div>";
					$TeacherListHTML .= "	<a href=\"javascript:SelTeacherID(".$TeacherID.")\" class=\"lms_teacher_select_btn active\">강사선택</a>";
					$TeacherListHTML .= "</li>";   


				}


				//여기서 검증

		//}//=====================kkkkkkk

	}
	

	$TeacherNum++;
}
$Stmt = null;




$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["ForceBlockSlotIDs"] = $ForceBlockSlotIDs;
$ArrValue["TeacherListHTML"] = $TeacherListHTML;
$ArrValue["TeacherSlotListHTML"] = $TeacherSlotListHTML;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>