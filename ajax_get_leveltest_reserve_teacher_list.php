<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$ErrNum = 0;
$ErrMsg = "";

$EduCenterID = isset($_REQUEST["EduCenterID"]) ? $_REQUEST["EduCenterID"] : "";
$LeveltestApplyDate = isset($_REQUEST["LeveltestApplyDate"]) ? $_REQUEST["LeveltestApplyDate"] : "";//지정날짜
$SearchTeacherID = isset($_REQUEST["SearchTeacherID"]) ? $_REQUEST["SearchTeacherID"] : "";


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

$ClassOrderTimeTypeID = 2;//20분 수업, 사용안함

$LeveltestTimeWeek = date('w', strtotime($LeveltestApplyDate));
$ArrWeekDayStr = explode(",","일요일,월요일,화요일,수요일,목요일,금요일,토요일");
$WeekDayStr = $ArrWeekDayStr[$LeveltestTimeWeek];


$LeveltestYear = date('Y', strtotime($LeveltestApplyDate));
$LeveltestMonth = date('m', strtotime($LeveltestApplyDate));
$LeveltestDay = date('d', strtotime($LeveltestApplyDate));

$LeveltestMonth_2 = date('n', strtotime($LeveltestApplyDate));
if (($LeveltestMonth_2-1)==0){
	$PrevLinkYear = $LeveltestYear - 1;
	$PrevLinkMonth = 12;
}else{
	$PrevLinkYear = $LeveltestYear;
	$PrevLinkMonth = $LeveltestMonth_2-1;
}
if (($LeveltestMonth_2+1)==13){
	$NextLinkYear = $LeveltestYear + 1;
	$NextLinkMonth = 1;
}else{
	$NextLinkYear = $LeveltestYear;
	$NextLinkMonth = $LeveltestMonth_2+1;
} 


$PrevLinkDate = $PrevLinkYear."-".substr("0".$PrevLinkMonth,-2)."-".date('t', strtotime($PrevLinkYear."-".$PrevLinkMonth."-01"));
$NextLinkDate = $NextLinkYear."-".substr("0".$NextLinkMonth,-2)."-01";



// 에듀센터 휴무, 설날, 크리스마스 등등
$Sql = "
		select 
				A.EduCenterHolidayID
		from EduCenterHolidays A 
		where datediff(A.EduCenterHolidayDate, '".$LeveltestApplyDate."')=0 
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

//for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){
$WeekDayNum = $LeveltestTimeWeek;//지정한 날짜만 나온다
if ($EduCenterHoliday[$WeekDayNum]==1){
	$WorkDayCount--;
}
//}

//교육센터 정기휴일 검색


//교육센터 브레이크 타임 검색
for ($HourNum=0;$HourNum<=24-1;$HourNum++){
	for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){
		
		//for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){
		$WeekDayNum = $LeveltestTimeWeek;//지정한 날짜만 나온다
		if ($EduCenterHoliday[$WeekDayNum]==0) {
			$EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum] = 1;//[요일/시/분] 1은 수업가능
		}
		//}
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
//$AddSqlWhere = $AddSqlWhere . " and A.TeacherPayTypeItemID=$SearchTeacherPayTypeItemID ";
$AddSqlWhere = $AddSqlWhere . " and B.EduCenterID=$EduCenterID ";
$AddSqlWhere = $AddSqlWhere . " and B.TeacherGroupState=1 ";
if ($SearchTeacherID!=""){
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherID=$SearchTeacherID ";
}
if($_LINK_MEMBER_LEVEL_ID_> 4) { // 3 : 프랜차이즈장, 4 : 프랜차이즈 직원, 5 : 영업
	$AddSqlWhere = $AddSqlWhere . " and ( A.TeacherGroupID=4 or A.TeacherGroupID=9 ) ";
}

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

//echo $Sql;

$TeacherNum = 1;
$TeacherListHTML = "";
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
			$WeekDayNum = $LeveltestTimeWeek;//지정한 날짜만 나온다
			if ($EduCenterHoliday[$WeekDayNum]==0) {
				$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 0;//테이블구성 === KKK
				$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = "";//테이블구성 === KKK
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
	
	
	if ($TeacherImageFileName==""){
		$StrTeacherImageFileName = "/images/no_photo_2.png";
	}else{
		$StrTeacherImageFileName = "/uploads/teacher_images/".$TeacherImageFileName;
	}


	if ($SearchTeacherID==""){//강사 아이디가 있을때는 내용만 가져다 뿌림 ===================================================
		
		$TeacherListHTML .= "<li>";
		$TeacherListHTML .= "	<div class=\"level_teacher_photo_wrap\">";
		$TeacherListHTML .= "		<img src=\"".$StrTeacherImageFileName."\" alt=\"".$TeacherName."\" class=\"level_teacher_photo\">";
		$TeacherListHTML .= "		<a href=\"#\" class=\"level_teacher_select_btn\" onclick=\"TeacherChangeDate('', ".$TeacherID.", ".$TeacherBlock80Min.")\">강사선택 <span class=\"level_teacher_select_arrow\"></span></a>";
		$TeacherListHTML .= "	</div>";

		$TeacherListHTML .= "	<div class=\"level_teacher_time_wrap\" id=\"DivTeacherTimeHTML_".$TeacherID."\">";
	}
	
	if ($SearchTeacherID!=""){

			$TeacherListHTML .= "		<div class=\"level_teacher_time_caption\">";
			$TeacherListHTML .= "			<h3 class=\"level_month_caption\">";
			$TeacherListHTML .= "				<a href=\"javascript:TeacherChangeDate('".$PrevLinkDate."', ".$TeacherID.", ".$TeacherBlock80Min.");\"><img src=\"images/btn_prev_black.png\" class=\"level_month_prev\"></a>";
			$TeacherListHTML .= "				".$LeveltestYear.".".$LeveltestMonth." ";
			$TeacherListHTML .= "				<a href=\"javascript:TeacherChangeDate('".$NextLinkDate."', ".$TeacherID.", ".$TeacherBlock80Min.");\"><img src=\"images/btn_next_black.png\" class=\"level_month_next\"></a>";
			$TeacherListHTML .= "			</h3>";



			// 달력 ==================================
			$today = strtotime($LeveltestApplyDate);
			$year = $LeveltestYear;
			$month = $LeveltestMonth_2;


			$p_month = $month - 1;
			$n_month = $month + 1;

			if ($p_month==0){
				$p_month = 12;
				$p_year = $year-1;
			}else{
				$p_year = $year;
			}

			if ($n_month==13){
				$n_month = 1;
				$n_year = $year+1;
			}else{
				$n_year = $year;
			}

			$time = strtotime($year.'-'.$month.'-01'); 
			list($tday, $sweek) = explode('-', date('t-w', $time));  // 총 일수, 시작요일 
			$tweek = ceil(($tday + $sweek) / 7);  // 총 주차 
			$lweek = date('w', strtotime($year.'-'.$month.'-'.$tday));  // 마지막요일 

			$TeacherListHTML .= "				<table class=\"level_month_table\">
													<tr>
														<th>SUN</th>
														<th>MON</th>
														<th>TUE</th>
														<th>WED</th>
														<th>THU</th>
														<th>FRI</th>
														<th>SAT</th>
													</tr>";
			for ($nn=1,$ii=0; $ii<$tweek; $ii++){
				
				$TeacherListHTML .= "<tr>";

				for ($kk=0; $kk<7; $kk++){
					

					$day2 = $nn;
					$NowDate2 = strtotime($year . "-" . substr("0".$month,-2) . "-" . substr("0".$day2,-2));
					$SelectDate2 = $year . "-" . substr("0".$month,-2) . "-" . substr("0".$day2,-2);

					if ($today-$NowDate2==0 && $LeveltestTimeWeek==$kk){
						$UseLink = 0;
						$TeacherListHTML .= "<td class=\"active\">";
					}else{
						$UseLink = 1;
						$TeacherListHTML .= "<td>";
					}

					if (($ii == 0 && $kk < $sweek) || ($ii == $tweek-1 && $kk > $lweek)) {
						$TeacherListHTML .=  "</td>\n";
						continue;
					}

					$day = $nn++;
					$NowDate = strtotime($year . "-" . substr("0".$month,-2) . "-" . substr("0".$day,-2));
					$SelectDate = $year . "-" . substr("0".$month,-2) . "-" . substr("0".$day,-2);

					$CheckNowDate = strtotime(date("Y-m-d"));
					$CheckSelectDate = strtotime($SelectDate);
					
					if ($UseLink==1){
						if ($CheckSelectDate<=$CheckNowDate){
							$TeacherListHTML .=  "<a style=\"color:#CCCCCC;\">".$day."</a>";
						}else{
							$TeacherListHTML .=  "<a href=\"javascript:TeacherChangeDate('".$SelectDate."', ".$TeacherID.", ".$TeacherBlock80Min.");\" style=\"color:#000000;\">".$day."</a>";
						}
					}else{
						$TeacherListHTML .= $day ;
					}
					$TeacherListHTML .=  "</td>";

				}
				
				$TeacherListHTML .=  "</tr>";
			}

			$TeacherListHTML .=  "</table>";


			// 달력 ==================================

			$TeacherListHTML .= "		</div>";
			$TeacherListHTML .= "		<span class=\"level_teacher_time_line\"></span>";
			$TeacherListHTML .= "		<table class=\"level_teacher_time_table\">";
			$TeacherListHTML .= "			<tr>";
			$TeacherListHTML .= "				<th>".str_replace("-",".",$LeveltestApplyDate)." (".$WeekDayStr.")</th>";
			$TeacherListHTML .= "			</tr>";


			//for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){
			$WeekDayNum = $LeveltestTimeWeek;//지정한 날짜만 나온다


			// 강사 자체 휴무
			$Sql5 = "
					select 
							A.TeacherHolidayID
					from TeacherHolidays A 
					where 
						A.TeacherID=$TeacherID 
						and datediff(A.TeacherHolidayDate, '".$LeveltestApplyDate."')=0 
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
			
				$TeacherListHTML .= "			<tr>";
				$TeacherListHTML .= "				<td>";

				
				$MinuteListNum=1;
				$OldMinuteListNum = 0;
				for ($HourNum=$TeacherStartHour;$HourNum<$TeacherEndHour;$HourNum++){

					for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){


						$SlotBreakEventCode = 0;
						$ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 0;//수업아님, 80분 연속수업 검색용

						$SlotBreakEventType = 0;//1 이면 ClassOrderSlots 기반 === KKK
						$TempClassOrderSlotMaster = 0;//1 이면 ClassOrderSlots 기반 === KKK


						$TargetDate = $LeveltestApplyDate;
						$TempYear = date('Y', strtotime($TargetDate));
						$TempMonth = date('m', strtotime($TargetDate));
						$TempDay = date('d', strtotime($TargetDate));


						$Sql3 = "select 
										count(*) as ClassOrderSlotCount
								from ClassOrderSlots COS 
									inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
									inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 

									left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$TempYear." and CLS.StartMonth=".$TempMonth." and CLS.StartDay=".$TempDay." and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID 
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
										and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 

										and COS.ClassOrderSlotState=1 
										and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0) ) 
										and ( CLS.ClassAttendState is NULL or CLS.ClassAttendState<4 )
						";
						//and (datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0 or CO.ClassOrderEndDate is null) 제거 2019-12-22


						$Stmt3 = $DbConn->prepare($Sql3);
						$Stmt3->execute(); 
						$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
						$Row3 = $Stmt3->fetch();
						$ClassOrderSlotCount = $Row3["ClassOrderSlotCount"];

						$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 0;
						if ($ClassOrderSlotCount>0){
							$SlotBreakEventType = 1; // === KKK
							$SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;
							$SlotBreakEventCode = 11;
							$ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;//수업, 80분 연속수업 검색용



							$Sql3 = "select 
											COS.ClassOrderSlotMaster
									from ClassOrderSlots COS 
										inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
										inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 

										left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$TempYear." and CLS.StartMonth=".$TempMonth." and CLS.StartDay=".$TempDay." and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID 
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
											and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 

											and COS.ClassOrderSlotState=1 
											and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0) ) 
											and ( CLS.ClassAttendState is NULL or CLS.ClassAttendState<4 )
							";
							//and (datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0 or CO.ClassOrderEndDate is null) 제거 2019-12-22

							$Stmt3 = $DbConn->prepare($Sql3);
							$Stmt3->execute();
							$Stmt3->setFetchMode(PDO::FETCH_ASSOC);


							while($Row3 = $Stmt3->fetch()) {
								$TempClassOrderSlotMaster = $Row3["ClassOrderSlotMaster"]; // === KKK
							}
							$Stmt3 = null;

						}


						
						if ($SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] ==0 ){//해당날짜 임시 수업 검색

							$TargetDate = $LeveltestApplyDate;
							$TempYear = date('Y', strtotime($TargetDate));
							$TempMonth = date('m', strtotime($TargetDate));
							$TempDay = date('d', strtotime($TargetDate));

							$Sql3 = "select 
											count(*) as ClassOrderSlotCount
									from ClassOrderSlots COS 
										inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
										inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 
										inner join Members MB on CO.MemberID=MB.MemberID 

										left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$TempYear." and CLS.StartMonth=".$TempMonth." and CLS.StartDay=".$TempDay." and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID 
									where 
											COS.StudyTimeWeek=".$WeekDayNum." 
											and COS.StudyTimeHour=".$HourNum." 
											and COS.StudyTimeMinute=".$MinuteNum." 
											and COS.TeacherID=".$TeacherID." 
											and datediff(COS.ClassOrderSlotDate,'".$TargetDate."')=0 
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

											left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$TempYear." and CLS.StartMonth=".$TempMonth." and CLS.StartDay=".$TempDay." and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID 
										where 
												COS.StudyTimeWeek=".$WeekDayNum." 
												and COS.StudyTimeHour=".$HourNum." 
												and COS.StudyTimeMinute=".$MinuteNum." 
												and COS.TeacherID=".$TeacherID." 
												and datediff(COS.ClassOrderSlotDate,'".$TargetDate."')=0 
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

							$TargetDate = $LeveltestApplyDate;
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


						if ($SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] ==0  && $TeacherBlock80Min==1){//80분 연속수업인지 검색
							$TempMinuteNum = $MinuteNum;
							$TempHourNum = $HourNum;
							$AccrueNumCount = 0;
							for ($AccrueNum=8;$AccrueNum>=1;$AccrueNum--){
								
								if ($TempMinuteNum==0){
									$TempMinuteNum=50;
									$TempHourNum=$TempHourNum-1;
								}else{
									$TempMinuteNum=$TempMinuteNum-10;
								}
									
								if ($TempHourNum>=$TeacherStartHour){
									
									if ($ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$TempHourNum][$TempMinuteNum] && $ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$TempHourNum][$TempMinuteNum] == 1){
										$AccrueNumCount++;
									}
									

									if ($AccrueNumCount==8){

										//$SlotBreakEventCode = 101 - 사용자단에서 js 로 변경
										$ForceBlockSlotIDs .= "Div_Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."|";
										
										//이전 8슬랏 위로 올라가서 강제 휴식 처리
										if ($MinuteListNum-9>0){
											$ForceBlockSlotIDs .= "Div_Slot_".$TeacherID."_".$WeekDayNum."_".($MinuteListNum-9)."|";
										}
									}

								}

							}
						}


					

						if ($EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum]==1 && $TeacherBreak[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum]==1 && $SlotBreakEvent[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum]==0){
							
							$TeacherListHTML .= "<span id=\"Div_Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" class=\"teacher_time\" onclick=\"SelectSlot(".$TeacherID.",".$HourNum.",".$MinuteNum.", ".$WeekDayNum.", ".$MinuteListNum.",".$TeacherBlock80Min.");\">".substr("0".$HourNum,-2).":".substr("0".$MinuteNum,-2)."";

								$TeacherListHTML .= "<input type=\"hidden\" name=\"Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"|".$TeacherID."_".$WeekDayNum."_".$HourNum."_".$MinuteNum."\">";
								$TeacherListHTML .= "<input type=\"hidden\" name=\"Able_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Able_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"1\" style=\"background-color:#cccccc;\">";
								$TeacherListHTML .= "<input type=\"hidden\" name=\"Break_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Break_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"".$SlotBreakEventCode."\">";
							
							$TeacherListHTML .= "</span>";


						}else{

							//$SlotBreakEventCode = 100;//이곳에서는 큰 의미가 없어 100으로 통일
							
							$TeacherListHTML .= "<span id=\"Div_Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" class=\"teacher_time\" style=\"background-color:#bbbbbb;cursor:default;display:none;\">".substr("0".$HourNum,-2).":".substr("0".$MinuteNum,-2)."";

								$TeacherListHTML .= "<input type=\"hidden\" name=\"Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"0\">";
								$TeacherListHTML .= "<input type=\"hidden\" name=\"Able_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Able_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"0\" style=\"background-color:#ff0000;\">";
								$TeacherListHTML .= "<input type=\"hidden\" name=\"Break_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Break_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"".$SlotBreakEventCode."\">";

							$TeacherListHTML .= "</span>";
							
							if ($MinuteListNum-$OldMinuteListNum!=1){//연속될 경우 보여주지 않는다.
								//$TeacherListHTML .= "<span id=\"Div_Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" class=\"teacher_time\" style=\"cursor:default;\">-</span>";
							}
							$OldMinuteListNum = $MinuteListNum;

						}

						$MinuteListNum++;
					}
				
					
					
				}
				
				$TeacherListHTML .= "				</td>";
				$TeacherListHTML .= "			</tr>";
			}
			
			//}

			$TeacherListHTML .= "		</table>";
			$TeacherListHTML .= "		<a href=\"javascript:OpenLeveltestReserveForm();\" class=\"teacher_select_submit\">레벨테스트 예약</a>";
	
	}


	if ($SearchTeacherID==""){//강사 아이디가 있을때는 내용만 가져다 뿌림 ===================================================

		$TeacherListHTML .= "	</div>";
		$TeacherListHTML .= "	<div class=\"teacher_profile_wrap\">";
		$TeacherListHTML .= "		<a href=\"javascript:OpenTeacherVideo(".$TeacherID.", ".$TeacherVideoType.", '".$TeacherVideoCode."')\" class=\"teacher_greeting_btn\">인사영상 <img src=\"images/arrow_big_right.png\"></a>";
		$TeacherListHTML .= "		<table class=\"teacher_profile_table\">";
		$TeacherListHTML .= "			<tr>";
		$TeacherListHTML .= "				<th>Name</th>";
		$TeacherListHTML .= "				<td><b>".$TeacherName."</b></td>";
		$TeacherListHTML .= "			</tr>";
		$TeacherListHTML .= "			<tr>";
		$TeacherListHTML .= "				<th>Education</th>";
		$TeacherListHTML .= "				<td>".$TeacherIntroSpec."</td>";
		$TeacherListHTML .= "			</tr>";
		$TeacherListHTML .= "			<tr>";
		$TeacherListHTML .= "				<th>Comment</th>";
		$TeacherListHTML .= "				<td>".str_replace("\n","<br>",$TeacherIntroText)."</td>";
		$TeacherListHTML .= "			</tr>";
		$TeacherListHTML .= "		</table>";
		$TeacherListHTML .= "		<div class=\"teacher_select_chart\"><img src=\"images/sample_teacher_chart_1.png\" style='display:none;'></div>";
		$TeacherListHTML .= "	</div>";
		$TeacherListHTML .= "</li>";
	}

	$TeacherNum++;
}
$Stmt = null;




$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;

$ArrValue["ForceBlockSlotIDs"] = $ForceBlockSlotIDs;
$ArrValue["TeacherListHTML"] = $TeacherListHTML;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>