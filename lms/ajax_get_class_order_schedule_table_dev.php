<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$EduCenterID = isset($_REQUEST["EduCenterID"]) ? $_REQUEST["EduCenterID"] : "";
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$SearchTeacherGroupID = isset($_REQUEST["SearchTeacherGroupID"]) ? $_REQUEST["SearchTeacherGroupID"] : "";
$StartHour = isset($_REQUEST["StartHour"]) ? $_REQUEST["StartHour"] : "";
$EndHour = isset($_REQUEST["EndHour"]) ? $_REQUEST["EndHour"] : "";
$MinuteListNum = isset($_REQUEST["MinuteListNum"]) ? $_REQUEST["MinuteListNum"] : "";
$ClassOrderStartDate = isset($_REQUEST["ClassOrderStartDate"]) ? $_REQUEST["ClassOrderStartDate"] : "";//지정날짜

$ClassOrderStartDateWeek = date('w', strtotime($ClassOrderStartDate));
$ArrClassOrderStartDate[0] = date("Y-m-d", strtotime("-".$ClassOrderStartDateWeek." day", strtotime($ClassOrderStartDate)));
$ArrClassOrderStartDateShot[0] = date("m.d", strtotime("-".$ClassOrderStartDateWeek." day", strtotime($ClassOrderStartDate)));
for ($iiii=1;$iiii<=6;$iiii++){
	$ArrClassOrderStartDate[$iiii] = date("Y-m-d", strtotime($iiii." day", strtotime($ArrClassOrderStartDate[0])));
	$ArrClassOrderStartDateShot[$iiii] = date("m.d", strtotime($iiii." day", strtotime($ArrClassOrderStartDate[0])));
}

// 에듀센터 휴무, 설날, 크리스마스 등등
for ($iiii=0;$iiii<=6;$iiii++){

	$Sql = "
			select 
					A.EduCenterHolidayID
			from EduCenterHolidays A 
			where 
				A.EduCenterID=$EduCenterID 
				and datediff(A.EduCenterHolidayDate, '".$ArrClassOrderStartDate[$iiii]."')=0 
				and A.EduCenterHolidayState=1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null; 
	$EduCenterHolidayID = $Row["EduCenterHolidayID"];

	$TempEduCenterHoliday[$iiii] = 0;
	if ($EduCenterHolidayID){
		$TempEduCenterHoliday[$iiii] = 1;
	}

}
// 에듀센터 휴무, 설날, 크리스마스 등등




$Sql = "
		select 
				A.*,
				B.MemberName,
				B.MemberLoginID,
				B.MemberNickName,
				C.ClassOrderWeekCount,
				D.ClassOrderTimeSlotCount,
				D.ClassOrderTimeTypeName
		from ClassOrders A 
			inner join Members B on A.MemberID=B.MemberID 
			inner join ClassOrderWeekCounts C on A.ClassOrderWeekCountID=C.ClassOrderWeekCountID 
			inner join ClassOrderTimeTypes D on A.ClassOrderTimeTypeID=D.ClassOrderTimeTypeID 
		where A.ClassOrderID=:ClassOrderID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null; 

$ClassProductID = $Row["ClassProductID"];
$ClassMemberType = $Row["ClassMemberType"];
$ClassOrderID = $Row["ClassOrderID"];
$ClassProgress = $Row["ClassProgress"];
$ClassOrderTimeSlotCount = $Row["ClassOrderTimeSlotCount"];

if (!$ClassOrderID){
	$ClassOrderID = 0;
}
if (!$ClassProgress){
	$ClassProgress = 0;
}


if ($ClassProductID==1){
	$SearchType = 1;//1:정규수업배정기준 2:임시수업배정기준
}else if ($ClassProductID==2 || $ClassProductID==3){
	$SearchType = 2;//1:정규수업배정기준 2:임시수업배정기준
}else{
	$SearchType = 1;//1:정규수업배정기준 2:임시수업배정기준
}





$ArrWeekDayStr = explode(",","일<br>$ArrClassOrderStartDateShot[0],월<br>$ArrClassOrderStartDateShot[1],화<br>$ArrClassOrderStartDateShot[2],수<br>$ArrClassOrderStartDateShot[3],목<br>$ArrClassOrderStartDateShot[4],금<br>$ArrClassOrderStartDateShot[5],토<br>$ArrClassOrderStartDateShot[6]");

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
for ($WorkDayNum=0;$WorkDayNum<=6;$WorkDayNum++){
	if ($EduCenterHoliday[$WorkDayNum]==1){
		$WorkDayCount--;
	}
}
//교육센터 정기휴일 검색


//현재 페이지 강사 수 구하기, 강사들의 최소 시작, 최대 종료 시간 구하기
$AddSqlWhere = "1=1";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherState=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherView=1 ";
if ($SearchTeacherGroupID!="" && $SearchTeacherGroupID!="0"){
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherGroupID=$SearchTeacherGroupID ";
}

/*
if ($SearchTeacherGroupID=="0"){
	if ($_LINK_ADMIN_LEVEL_ID_>4){// 관리자가 아니면 General / Homebase 만 나오게 한다.
		$AddSqlWhere = $AddSqlWhere . " and (A.TeacherGroupID=4 or A.TeacherGroupID=9) ";
	}
}
*/

if ($_LINK_ADMIN_LEVEL_ID_>4){// 관리자가 아니면 General / Homebase 만 나오게 한다.
	$AddSqlWhere = $AddSqlWhere . " and (A.TeacherGroupID=4 or A.TeacherGroupID=9) ";
}

$AddSqlWhere = $AddSqlWhere . " and B.EduCenterID=$EduCenterID ";
$AddSqlWhere = $AddSqlWhere . " and B.TeacherGroupState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherState<>0 ";

$Sql = "select 
				count(*) as RowCount,
				min(TeacherStartHour) as MinTeacherHour,
				max(TeacherEndHour) as MaxTeacherHour
		from Teachers A 
			inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TeacherCount = $Row["RowCount"];
$MinTeacherHour = $Row["MinTeacherHour"];
$MaxTeacherHour = $Row["MaxTeacherHour"];

//현재 페이지 강사 수 구하기, 강사들의 최소 시작, 최대 종료 시간 구하기

//처음에는 3시간 그 다음부터는 1시간씩 가져온다
$FirstGetHour = 4;//처음에는 3시간
$NextGetHour = 1;//다음부터는 1시간씩 가져온다

if ($StartHour=="" && $EndHour==""){
	$StartHour = $MinTeacherHour;
	$EndHour = $StartHour + ($FirstGetHour-1);
	//$EndHour = $StartHour + 24;//전체 가져오기
}

$AjaxLoading = 1;
if ($EndHour > $MaxTeacherHour-1){
	$EndHour = $MaxTeacherHour-1;
	$AjaxLoading = 0;
}

$NextStartHour = $EndHour + 1;
$NextEndHour = $NextStartHour + ($NextGetHour-1);
//처음에는 3시간 그 다음부터는 1시간씩 가져온다

$StartHour = (int)$StartHour;
$EndHour = (int)$EndHour;
$MinuteListNum = (int)$MinuteListNum;

if ($EndHour>23){
	$EndHour = 23;
}


//교육센터 브레이크 타임 검색
$TeacherHourMinuteCount = 1;
for ($HourNum=$MinTeacherHour;$HourNum<=$MaxTeacherHour;$HourNum++){//전체를 초기화 해준다.
	for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){
		for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){
			if ($EduCenterHoliday[$WeekDayNum]==0) {
				$EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum] = 1;//[요일/시/분] 1은 수업가능
				
				for ($TeacherNum=1;$TeacherNum<=$TeacherCount;$TeacherNum++){
					
					$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = "";//테이블구성 === KKK

					$TeacherBreak[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;//[강사순번/요일/시/분] 1은 수업가능
				}
			}
		}

		$ArrListHourMinute[$TeacherHourMinuteCount] = $HourNum."|".$MinuteNum;
		$TeacherHourMinuteCount++;
	}
}

$Sql = "select 
				A.* 
		from EduCenterBreakTimes A 
		where A.EduCenterID=$EduCenterID and A.EduCenterBreakTimeState=1 
		order by A.EduCenterBreakTimeWeek asc, A.EduCenterBreakTimeHour asc, A.EduCenterBreakTimeMinute asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

while($Row = $Stmt->fetch()) {
	$EduCenterBreakTimeWeek = $Row["EduCenterBreakTimeWeek"];
	$EduCenterBreakTimeHour = $Row["EduCenterBreakTimeHour"];
	$EduCenterBreakTimeMinute = $Row["EduCenterBreakTimeMinute"];
	$EduCenterBreakTimeType = $Row["EduCenterBreakTimeType"];
	
	$EduCenterBreak[$EduCenterBreakTimeWeek][$EduCenterBreakTimeHour][$EduCenterBreakTimeMinute] = $EduCenterBreakTimeType;
}
$Stmt = null;
//교육센터 브레이크 타임 검색


//강사목록 검색
$AddSqlWhere = "1=1";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherState=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherView=1 ";
if ($SearchTeacherGroupID!="" && $SearchTeacherGroupID!="0"){
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherGroupID=$SearchTeacherGroupID ";
}
$AddSqlWhere = $AddSqlWhere . " and B.EduCenterID=$EduCenterID ";
$AddSqlWhere = $AddSqlWhere . " and B.TeacherGroupState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherState<>0 ";
if ($_LINK_ADMIN_LEVEL_ID_>4){// 관리자가 아니면 General / Homebase 만 나오게 한다.
	$AddSqlWhere = $AddSqlWhere . " and (A.TeacherGroupID=4 or A.TeacherGroupID=9) ";
}

$Sql = "select 
				A.*
		from Teachers A 
			inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
		where ".$AddSqlWhere."
		order by B.TeacherGroupOrder asc, A.TeacherOrder asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


$ArrTeacherID = [];
$ArrTeacherName = [];
$ArrTeacherStartHour = [];
$ArrTeacherEndHour = [];
$ArrTeacherBlock80Min = [];


$TeacherNum=1;
while($Row = $Stmt->fetch()) {
	$ArrTeacherID[$TeacherNum] = $Row["TeacherID"];
	$ArrTeacherName[$TeacherNum] = $Row["TeacherName"];
	$ArrTeacherStartHour[$TeacherNum] = $Row["TeacherStartHour"];
	$ArrTeacherEndHour[$TeacherNum] = $Row["TeacherEndHour"];
	$ArrTeacherBlock80Min[$TeacherNum] = $Row["TeacherBlock80Min"];

	$TeacherNum++;
}
$Stmt = null;
//강사목록 검색



//강사 브레이크 타임 검색
for ($TeacherNum=1;$TeacherNum<=$TeacherCount;$TeacherNum++){
	

	$Sql = "select 
					A.* 
			from TeacherBreakTimes A 
			where A.TeacherID=$ArrTeacherID[$TeacherNum] and A.TeacherBreakTimeState=1 
			order by A.TeacherBreakTimeWeek asc, A.TeacherBreakTimeHour asc, A.TeacherBreakTimeMinute asc";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	while($Row = $Stmt->fetch()) {
		$TeacherBreakTimeWeek = $Row["TeacherBreakTimeWeek"];
		$TeacherBreakTimeHour = $Row["TeacherBreakTimeHour"];
		$TeacherBreakTimeMinute = $Row["TeacherBreakTimeMinute"];
		$TeacherBreakTimeType = $Row["TeacherBreakTimeType"];
		
		$TeacherBreak[$TeacherNum][$TeacherBreakTimeWeek][$TeacherBreakTimeHour][$TeacherBreakTimeMinute] = $TeacherBreakTimeType;//[강사순번/요일/시/분] 1은 수업가능
	}
	$Stmt = null;

}
//강사 브레이크 타입 검색


$ScheduleTable = "";


//=====================================================  AAAA
$HourListCount = 0;

//for ($HourNum=$MinTeacherHour;$HourNum<=$MaxTeacherHour-1;$HourNum++){
for ($HourNum=$StartHour;$HourNum<=$EndHour;$HourNum++){//해당되는 리스트만 가져온다.

	//if ($HourListCount % 3 == 0){
	if ((($HourNum - $MinTeacherHour) % 3) == 0) {
		$ScheduleTable .= "<thead>";
		$ScheduleTable .= "<tr>";

		for ($TeacherNum=1;$TeacherNum<=$TeacherCount;$TeacherNum++){
			if (($TeacherNum-1) % 5 == 0){
				$ScheduleTable .= "<th nowrap rowspan=\"2\">시</th>";
				$ScheduleTable .= "<th nowrap rowspan=\"2\">분</th>";
			}

			$StrBlock80Min = "";
			if ($ArrTeacherBlock80Min[$TeacherNum]==0){
				$StrBlock80Min = " (Over 80min)";
			}
			$ScheduleTable .= "<th nowrap colspan=\"".$WorkDayCount."\" class=\"TdTeacherName_".$TeacherNum."\">".$ArrTeacherName[$TeacherNum].$StrBlock80Min."</th>";
		}


		$ScheduleTable .= "</tr>";
		$ScheduleTable .= "<tr>";

		for ($TeacherNum=1;$TeacherNum<=$TeacherCount;$TeacherNum++){
			for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){
				if ($EduCenterHoliday[$WeekDayNum]==0) {
					$ScheduleTable .= "<th nowrap class=\"TdWeekName_".$TeacherNum."_".$WeekDayNum."\">".$ArrWeekDayStr[$WeekDayNum]."</th>";
				
				}
			}
				
		}
	
		$ScheduleTable .= "</tr>";
		$ScheduleTable .= "</thead>";
		
	}	
	$ScheduleTable .= "<tbody>";


	$ScheduleTable .= "<tr>";
	for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){
		
		for ($TeacherNum=1;$TeacherNum<=$TeacherCount;$TeacherNum++){
//=====================================================  AAAA
	

			if (($TeacherNum-1) % 5 == 0){
				if ($MinuteNum == 0){

					$ScheduleTable .= "<th rowspan=\"6\">".$HourNum."</th>";

				}

				$ScheduleTable .= "<th class=\"TdMinuteNum_".$HourNum."_".$MinuteNum."\">".$MinuteNum."</th>";

			}



			$TeacherID = $ArrTeacherID[$TeacherNum];//현재 슬랏 교사 아이디 


			//=====================================================  BBBB
			for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){
				if ($EduCenterHoliday[$WeekDayNum]==0) {
			//=====================================================  BBBB

					$TargetDate = $ArrClassOrderStartDate[$WeekDayNum];

					$SlotBreakEvent = 0;
					$SlotBreakEventName = "선택";
					$SlotBreakEventCode = 0;

					$SlotBreakEventType = 0;//1 이면 ClassOrderSlots 기반 === KKK
					$TempClassOrderSlotMaster = 0;//1 이면 ClassOrderSlots 기반 === KKK

					// 에듀센터 휴무, 설날, 크리스마스 등등
					if ($TempEduCenterHoliday[$WeekDayNum]==1 && $SearchType == 2) {
						$SlotBreakEvent = 1;
						$BgColor = "#96C265";
						$FontColor = "#FFFFFF";
						$SlotBreakEventName = "센터휴무";
						$SlotBreakEventCode = 21;
					}
					// 에듀센터 휴무, 설날, 크리스마스 등등



					// 강사 자체 휴무
					$Sql5 = "
							select 
									A.TeacherHolidayID
							from TeacherHolidays A 
							where 
								A.TeacherID=$TeacherID 
								and datediff(A.TeacherHolidayDate, '".$TargetDate."')=0 
								and A.TeacherHolidayState=1";
					$Stmt5 = $DbConn->prepare($Sql5);
					$Stmt5->execute();
					$Stmt5->setFetchMode(PDO::FETCH_ASSOC);
					$Row5 = $Stmt5->fetch();
					$Stmt5 = null; 
					$TeacherHolidayID = $Row5["TeacherHolidayID"];

					if ($TeacherHolidayID && $SearchType == 2){
						$SlotBreakEvent = 1;
						$BgColor = "#AFD08A";
						$FontColor = "#FFFFFF";
						$SlotBreakEventName = "강사휴무";
						$SlotBreakEventCode = 21;
					}
					// 강사 자체 휴무



					if ($SlotBreakEvent ==0 ){//교사 수업가능 시간 검색
						if ($HourNum < $ArrTeacherStartHour[$TeacherNum] || $HourNum >= $ArrTeacherEndHour[$TeacherNum] ){
							$SlotBreakEvent = 1;
							$BgColor = "#888888";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "궐석";
							$SlotBreakEventCode = 21;
						}
					}
							
					if ($SlotBreakEvent ==0 ){//교육센터 브레이크타임 검색
						$BgColor = "#FBFBFB";
						$FontColor = "#888888";
						if ($EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum]==1) {
							$BgColor = "#FBFBFB";
							$FontColor = "#888888";
						}else if ($EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum]==2) {
							$SlotBreakEvent = 1;
							$BgColor = "#FFCC00";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "식사(개)";
							$SlotBreakEventCode = 31;
						}else if ($EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum]==3) {
							$SlotBreakEvent = 1;
							$BgColor = "#CC9933";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "휴식(개)";
							$SlotBreakEventCode = 41;
						}else if ($EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum]==4) {
							$SlotBreakEvent = 1;
							$BgColor = "#CC6666";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "블락(개)";
							$SlotBreakEventCode = 51;
						}
					}


					if ($SlotBreakEvent ==0 ){//교육센터가 수업가능일경우 교사 브레이크타임 검색

						$BgColor = "#FBFBFB";
						$FontColor = "#888888";
						if ($TeacherBreak[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum]==1) {
							$BgColor = "#FBFBFB";
							$FontColor = "#888888";
						}else if ($TeacherBreak[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum]==2) {
							$SlotBreakEvent = 1;
							$BgColor = "#FFCC00";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "식사";
							$SlotBreakEventCode = 61;
						}else if ($TeacherBreak[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum]==3) {
							$SlotBreakEvent = 1;
							$BgColor = "#CC9933";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "휴식";
							$SlotBreakEventCode = 71;
						}else if ($TeacherBreak[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum]==4) {
							$SlotBreakEvent = 1;
							$BgColor = "#CC6666";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "블락";
							$SlotBreakEventCode = 81;
						}else if ($TeacherBreak[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum]==5) {
							$SlotBreakEvent = 1;
							$BgColor = "#CC6666";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "";
							$SlotBreakEventCode = 81;
						}

					}


					
					if ($SlotBreakEvent ==0 ){//교사 기존 수업 검색

						$TempYear = date('Y', strtotime($TargetDate));
						$TempMonth = date('m', strtotime($TargetDate));
						$TempDay = date('d', strtotime($TargetDate));

						if ($SearchType==1){//정규수업 배정기준
							$Sql3Where = " ";
						}else{
							$Sql3Where = " and ( CLS.ClassAttendState is NULL or CLS.ClassAttendState<4 )  ";
						}



						$Sql = "select 
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
										and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 
										and COS.ClassOrderSlotState=1 
										and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0) ) 
										".$Sql3Where." 
						";
										//수강신청에는 다른 수강신청의 시작날짜를 검토하지 않는다.
										//and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 
										//and (datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0 or CO.ClassOrderEndDate is null) 제거 2019-12-22
						
						$Stmt = $DbConn->prepare($Sql);
						$Stmt->execute();
						$Stmt->setFetchMode(PDO::FETCH_ASSOC); 
						$Row = $Stmt->fetch();
						$Stmt = null;
						$ClassOrderSlotCount = $Row["ClassOrderSlotCount"];

						//echo "^^^".$Sql."^^^";
						
						if ($ClassOrderSlotCount>0) {

							

							$SlotBreakEvent = 1;
							$BgColor = "#CC99FF";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "수업";
							$SlotBreakEventCode = 11;
							$SlotBreakEventType = 1; // === KKK
						
							$TempYear = date('Y', strtotime($TargetDate));
							$TempMonth = date('m', strtotime($TargetDate));
							$TempDay = date('d', strtotime($TargetDate));

						
							$Sql = "select 
										COS.ClassOrderID,
										COS.ClassOrderSlotMaster,
										CO.ClassMemberType,
										MB.MemberName,
										MB.MemberLoginID,
										MB.MemberNickName,
										COT.ClassOrderTimeSlotCount
								from ClassOrderSlots COS 
									inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
									inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 
									inner join Members MB on CO.MemberID=MB.MemberID 
									inner join ClassOrderTimeTypes COT on CO.ClassOrderTimeTypeID=COT.ClassOrderTimeTypeID 

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
										and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 

										and COS.ClassOrderSlotState=1 
										and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0) ) 
										".$Sql3Where." 
								order by CO.ClassOrderID asc
								";
										//수강신청에는 다른 수강신청의 시작날짜를 검토하지 않는다.
										//and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 
										//and (datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0 or CO.ClassOrderEndDate is null) 제거 2019-12-22
							

							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
							
							$MemberListNum = 1;
							$OldTempClassOrderID = 0;
							while($Row = $Stmt->fetch()) {

								$TempClassOrderID = $Row["ClassOrderID"];
								$TempClassOrderSlotMaster = $Row["ClassOrderSlotMaster"]; // === KKK
								$TempClassMemberType = $Row["ClassMemberType"];
								$TempClassOrderTimeSlotCount = $Row["ClassOrderTimeSlotCount"];

								if (($TempClassMemberType==2 || $TempClassMemberType==3) && $MemberListNum==1){//그룹수업이면
										
									$BgColor = "#FFCCFF";
									$FontColor = "#FFFFFF";
									if ($TempClassMemberType==2){
										$SlotBreakEventName = "1:2수업(".($TempClassOrderTimeSlotCount*10)."분)";
									}else{
										$SlotBreakEventName = "그룹수업(".($TempClassOrderTimeSlotCount*10)."분)";
									}

									//신청중인 수업이 그룹, 기존 수업이 그룹, 20/40분 수업이 동일, 수업의 시작 슬롯, 선택가능으로...
									if ( $ClassMemberType==$TempClassMemberType && $ClassOrderTimeSlotCount==$TempClassOrderTimeSlotCount ){
										$SlotBreakEvent = 0;
										$SlotBreakEventCode = 0;
									}

								}

								if ($ClassMemberType==2 && $MemberListNum>1){
									$SlotBreakEvent = 1;//1:2 수업이고 2명 이상일때
									$SlotBreakEventCode = 11;
								}

								$MemberName = $Row["MemberName"];
								$MemberLoginID = $Row["MemberLoginID"];
								$MemberNickName = $Row["MemberNickName"];

								if ($MemberListNum>1){
									$SlotBreakEventName .= " , ".$MemberName."(".$MemberLoginID.") ";
								}else{
									$SlotBreakEventName .= " / ".$MemberName."(".$MemberLoginID.") ";
								}

								$OldTempClassOrderID = $TempClassOrderID;

								$MemberListNum++;
							}
							$Stmt = null;
						}

					}



					
					if ($SlotBreakEvent ==0 ){//해당날짜 임시 수업 검색

						$TempYear = date('Y', strtotime($TargetDate));
						$TempMonth = date('m', strtotime($TargetDate));
						$TempDay = date('d', strtotime($TargetDate));	
						
						if ($SearchType==1){//정규수업 배정기준
							$Sql3Where = " and datediff(COS.ClassOrderSlotDate,'".$TargetDate."')>=0 ";
							$Sql3Where2 = " and datediff(CLS.StartDateTime,'".$TargetDate."')>=0";
						}else{
							$Sql3Where = " and datediff(COS.ClassOrderSlotDate,'".$TargetDate."')=0  ";
							$Sql3Where2 = " and datediff(CLS.StartDateTime,'".$TargetDate."')=0 ";
						}




						$Sql3 = "select 
										count(*) as ClassOrderSlotCount
								from ClassOrderSlots COS 
									inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
									inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 
									inner join Members MB on CO.MemberID=MB.MemberID 
									inner join ClassOrderTimeTypes COT on CO.ClassOrderTimeTypeID=COT.ClassOrderTimeTypeID 

									left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID ".$Sql3Where2."
								where 
										COS.StudyTimeWeek=".$WeekDayNum." 
										and COS.StudyTimeHour=".$HourNum." 
										and COS.StudyTimeMinute=".$MinuteNum." 
										and COS.TeacherID=".$TeacherID." 
										".$Sql3Where."
										and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 
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
							$SlotBreakEvent = 1;
							$SlotBreakEventCode = 11;

							$TempYear = date('Y', strtotime($TargetDate));
							$TempMonth = date('m', strtotime($TargetDate));
							$TempDay = date('d', strtotime($TargetDate));

							$Sql3 = "select 
											COS.ClassOrderID,
											COS.ClassOrderSlotMaster,
											CO.ClassMemberType,
											CO.ClassProductID,
											MB.MemberName,
											MB.MemberLoginID,
											MB.MemberNickName,
											COT.ClassOrderTimeSlotCount,
											ifnull(CLS.ClassID, 0) as ClassID
									from ClassOrderSlots COS 
										inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
										inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 
										inner join Members MB on CO.MemberID=MB.MemberID 
										inner join ClassOrderTimeTypes COT on CO.ClassOrderTimeTypeID=COT.ClassOrderTimeTypeID 

										left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID ".$Sql3Where2."

									where 
											COS.StudyTimeWeek=".$WeekDayNum." 
											and COS.StudyTimeHour=".$HourNum." 
											and COS.StudyTimeMinute=".$MinuteNum." 
											and COS.TeacherID=".$TeacherID." 
											".$Sql3Where."
											
											and COS.ClassOrderSlotType=2 
											and ( CLS.ClassAttendState is NULL or CLS.ClassAttendState<4 )
											and COS.ClassOrderSlotState=1 
											and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4) 
											
							";

							//echo $Sql3;

							$Stmt3 = $DbConn->prepare($Sql3);
							$Stmt3->execute();
							$Stmt3->setFetchMode(PDO::FETCH_ASSOC);


							//if ($ClassOrderID){
							$MemberListNum=1;
							$OldTempClassOrderID = 0;
							while($Row3 = $Stmt3->fetch()) {

								$TempClassOrderID = $Row3["ClassOrderID"];
								$TempClassMemberType = $Row3["ClassMemberType"];
								$TempClassOrderTimeSlotCount = $Row3["ClassOrderTimeSlotCount"];
								$TempClassOrderSlotMaster = $Row3["ClassOrderSlotMaster"]; // === KKK
								$TempClassID = $Row3["ClassID"];

								$ClassProductID = $Row3["ClassProductID"];
								
								if ($TempClassMemberType==1 && $MemberListNum==1){

									$BgColor = "#69B7DA";
									$FontColor = "#FFFFFF";
									
									if ($ClassProductID==1){
										$SlotBreakEventName = "(임)수업 ";
									}else if ($ClassProductID==2){
										$SlotBreakEventName = "(임)레벨 ";
									}else if ($ClassProductID==3){
										$SlotBreakEventName = "(임)체험 ";
									}

								}else if (($TempClassMemberType==2 || $TempClassMemberType==3) && $MemberListNum==1){//그룹수업이면
										
									$BgColor = "#A6CAEA";
									$FontColor = "#FFFFFF";

									if ($ClassProductID==1){
										if ($TempClassMemberType==2){
											$SlotBreakEventName = "(임)1:2수업(".($TempClassOrderTimeSlotCount*10)."분) ";
										}else{
											$SlotBreakEventName = "(임)그룹수업(".($TempClassOrderTimeSlotCount*10)."분) ";
										}
									}else if ($ClassProductID==2){
										if ($TempClassMemberType==2){
											$SlotBreakEventName = "(임)1:2레벨 ";//이런경우는 없음
										}else{
											$SlotBreakEventName = "(임)그룹레벨 ";//이런경우는 없음
										}
									}else if ($ClassProductID==3){
										if ($TempClassMemberType==2){
											$SlotBreakEventName = "(임)1:2체험 ";
										}else{
											$SlotBreakEventName = "(임)그룹체험 ";
										}
									}

									//신청중인 수업이 그룹, 기존 수업이 그룹, 20/40분 수업이 동일, 수업의 시작 슬롯, 선택가능으로...
									if ( ($ClassMemberType==2 || $ClassMemberType==3) && $ClassOrderTimeSlotCount==$TempClassOrderTimeSlotCount ){
										$SlotBreakEvent = 0;
										$SlotBreakEventCode = 0;
									}
								}

								if ($ClassMemberType==2 && $MemberListNum>1){
									$SlotBreakEvent = 1;//1:2 수업이고 2명 이상일때
									$SlotBreakEventCode = 11;
								}

								$MemberName = $Row3["MemberName"];
								$MemberLoginID = $Row3["MemberLoginID"];
								$MemberNickName = $Row3["MemberNickName"];

								if ($MemberListNum>1){
									$SlotBreakEventName .= " , ".$MemberName."(".$MemberLoginID.", ".$TempClassID.") ";
								}else{
									$SlotBreakEventName .= " / ".$MemberName."(".$MemberLoginID.", ".$TempClassID.") ";
								}


								$MemberListNum++;
							
							}
							$Stmt3 = null;
						
						}
					}
					

					if ($SlotBreakEvent ==0 ){//식사,휴식,블락(임시) 검색

						$TargetDate = $ArrClassOrderStartDate[$WeekDayNum]; 
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
								$SlotBreakEvent = 1;
								$BgColor = "#FFCC00";
								$FontColor = "#FFFFFF";
								$SlotBreakEventName = "식사";
								$SlotBreakEventCode = 61;
							}else if ($TeacherBreakTimeTempType==3) {
								$SlotBreakEvent = 1;
								$BgColor = "#CC9933";
								$FontColor = "#FFFFFF";
								$SlotBreakEventName = "휴식";
								$SlotBreakEventCode = 71;
							}else if ($TeacherBreakTimeTempType==4) {
								$SlotBreakEvent = 1;
								$BgColor = "#CC6666";
								$FontColor = "#FFFFFF";
								$SlotBreakEventName = "블락";
								$SlotBreakEventCode = 81;
							}
						}

					} 



					
					if ($SlotBreakEvent==0 && $ClassProgress==1){//수업가능

						 $TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = "<td nowrap id=\"Div_Slot_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" style=\"background-color:".$BgColor.";color:".$FontColor.";text-align:center;cursor:pointer;\" onclick=\"SelectSlot(".$TeacherNum.",".$WeekDayNum.",".$MinuteListNum.", ".$ArrTeacherBlock80Min[$TeacherNum].");EventMouseOver(".$TeacherNum.",".$WeekDayNum.",".$HourNum.",".$MinuteNum.");\" title=\"".$SlotBreakEventName."\" class=\"TdSlot_".$TeacherNum."_".$WeekDayNum." TdSlot_".$HourNum."_".$MinuteNum."\">";
						$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Slot_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Slot_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" value=\"|".$TeacherID."_".$WeekDayNum."_".$HourNum."_".$MinuteNum."\">";
						$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Able_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Able_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" value=\"1\" style=\"background-color:#cccccc;\">";
						$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Break_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Break_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" value=\"".$SlotBreakEventCode."\">";
						$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] .= "</td>";

					}else{

						$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = "<td nowrap id=\"Div_Slot_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" style=\"background-color:".$BgColor.";color:".$FontColor.";text-align:center;\" title=\"".$SlotBreakEventName."\" class=\"TdSlot_".$TeacherNum."_".$WeekDayNum." TdSlot_".$HourNum."_".$MinuteNum."\" onclick=\"EventMouseOver(".$TeacherNum.",".$WeekDayNum.",".$HourNum.",".$MinuteNum.")\">";
						$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Slot_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Slot_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" value=\"0\">";
						$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Able_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Able_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" value=\"0\" style=\"background-color:#ff0000;\">";
						$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Break_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Break_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" value=\"".$SlotBreakEventCode."\">";
						$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] .= "</td>";

					}



					$ScheduleTable .= $TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum];// === KKK



			//=====================================================  BBBB
				}
			}
			//=====================================================  BBBB
 

//=====================================================  AAAA
		}

		$ScheduleTable .= "</tr>";

		$MinuteListNum++;
	}

	$HourListCount++;
}

//=====================================================  AAAA

$ScheduleTable .= "</tbody>";


$ArrValue["AjaxLoading"] = $AjaxLoading;
$ArrValue["NextStartHour"] = $NextStartHour;
$ArrValue["NextEndHour"] = $NextEndHour;
$ArrValue["NextMinuteListNum"] = $MinuteListNum;
$ArrValue["ScheduleTable"] = $ScheduleTable;
$ArrValue["WeekStartDate"] = str_replace("-",".",$ArrClassOrderStartDate[0]);
$ArrValue["WeekEndDate"] = str_replace("-",".",$ArrClassOrderStartDate[6]);

 

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>