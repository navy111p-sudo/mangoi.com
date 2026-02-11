<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$SearchTeacherGroupID = isset($_REQUEST["SearchTeacherGroupID"]) ? $_REQUEST["SearchTeacherGroupID"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
$SearchDate = isset($_REQUEST["SearchDate"]) ? $_REQUEST["SearchDate"] : "";
$EduCenterID = isset($_REQUEST["EduCenterID"]) ? $_REQUEST["EduCenterID"] : "";
$StartHour = isset($_REQUEST["StartHour"]) ? $_REQUEST["StartHour"] : "";
$EndHour = isset($_REQUEST["EndHour"]) ? $_REQUEST["EndHour"] : "";
$MinuteListNum = isset($_REQUEST["MinuteListNum"]) ? $_REQUEST["MinuteListNum"] : "";
$SearchType = isset($_REQUEST["SearchType"]) ? $_REQUEST["SearchType"] : "";//1:정규수업배정기준 2:임시수업배정기준


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


$WeekDayNum = date('w', strtotime($SearchDate));//선택한 날짜의 요일//지정날짜


$ArrWeekDayStr = explode(",","일,월,화,수,목,금,토");

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

$WorkDayCount = 1;
//교육센터 정기휴일 검색 


//현재 페이지 강사 수 구하기, 강사들의 최소 시작, 최대 종료 시간 구하기
$AddSqlWhere = "1=1";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherState=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherView=1 ";
if ($SearchTeacherGroupID!=""){
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherGroupID=$SearchTeacherGroupID ";
}
if ($SearchText!=""){
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherName like '%".$SearchText."%' ";
}
if ($SearchState!="100"){
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and B.EduCenterID=$EduCenterID ";
$AddSqlWhere = $AddSqlWhere . " and B.TeacherGroupState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherState<>0 ";
if ($_LINK_ADMIN_LEVEL_ID_>4){// 관리자가 아니면 General / Homebase 만 나오게 한다.
	$AddSqlWhere = $AddSqlWhere . " and (A.TeacherGroupID=4 or A.TeacherGroupID=9) ";
}

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

//처음에는 9시간 그 다음부터는 3시간씩 가져온다
if ($StartHour=="" && $EndHour==""){
	$StartHour = $MinTeacherHour;
	//$EndHour = $StartHour + 8;
	$EndHour = $StartHour + 24;//전체 가져오기
}

$AjaxLoading = 1;
if ($EndHour > $MaxTeacherHour-1){
	$EndHour = $MaxTeacherHour-1;
	$AjaxLoading = 0;
}

$NextStartHour = $EndHour + 1;
$NextEndHour = $NextStartHour + 2;
//처음에는 9시간 그 다음부터는 3시간씩 가져온다

$StartHour = (int)$StartHour;
$EndHour = (int)$EndHour;
$MinuteListNum = (int)$MinuteListNum;

if ($EndHour<23){
	$EndHour = 23;
}


//교육센터 브레이크 타임 검색
$TeacherHourMinuteCount = 1;
for ($HourNum=$MinTeacherHour;$HourNum<=$MaxTeacherHour;$HourNum++){//전체를 초기화 해준다.
	for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){
		//for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){
			//if ($EduCenterHoliday[$WeekDayNum]==0) {
				$EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum] = 1;//[요일/시/분] 1은 수업가능
				
				for ($TeacherNum=1;$TeacherNum<=$TeacherCount;$TeacherNum++){
					$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = "";//테이블구성 === KKK
					
					$TeacherBreak[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;//[강사순번/요일/시/분] 1은 수업가능
					$ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 0;//[강사순번/요일/시/분] 1은 수업가능, 80분 연속수업 검색용
				}
			//}
		//}

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
if ($SearchTeacherGroupID!=""){
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherGroupID=$SearchTeacherGroupID ";
}
if ($SearchText!=""){
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherName like '%".$SearchText."%' ";
}
if ($SearchState!="100"){
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherState=$SearchState ";
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
$ArrTeacherClassCount = [];
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



	$ViewTable = "

		select 
			
			ClassOrderTimeTypeID

		from ClassOrderSlots COS 

				left outer join Classes CLS on COS.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".date('Y', strtotime($SearchDate))." and CLS.StartMonth=".date('m', strtotime($SearchDate))." and CLS.StartDay=".date('d', strtotime($SearchDate))." and CLS.StartHour=COS.StudyTimeHour and CLS.StartMinute=COS.StudyTimeMinute and CLS.ClassAttendState<>99 

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

		where TEA.TeacherState=1 
				and COS.TeacherID=".$ArrTeacherID[$TeacherNum]." 
				and COS.ClassOrderSlotMaster=1 
				and ( 
						(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$WeekDayNum." and COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SearchDate."')<=0 ) 

						or 
						(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$WeekDayNum." and datediff(COS.ClassOrderSlotStartDate, '".$SearchDate."')<=0 and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SearchDate."')<=0 ) 

						or 
						(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$WeekDayNum." and COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$SearchDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SearchDate."')<=0 ) 

						or 
						(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$WeekDayNum." and datediff(COS.ClassOrderSlotStartDate, '".$SearchDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$SearchDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SearchDate."')<=0 ) 

						or 
						(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SearchDate."')=0 )   
					)  
				and COS.ClassOrderSlotState=1 
				and CO.ClassProgress=11 
				and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4  or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$SearchDate."')>=0) or CO.ClassOrderState=5 or CO.ClassOrderState=6)

				and (
						(CT.CenterPayType=1 and MB.MemberPayType=0 and ((CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=5 or CO.ClassOrderState=6) or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$SearchDate."')>=0) )) 
						or 
						( 
							( CT.CenterPayType=2 and datediff(CO.ClassOrderEndDate, '".$SearchDate."')>=0 ) 
							or 
							( CT.CenterPayType=1 and MB.MemberPayType=1 and datediff(CO.ClassOrderEndDate, '".$SearchDate."')>=0 ) 
						)
						or
						CO.ClassProductID=2 
						or 
						CO.ClassProductID=3 
						or 
						(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SearchDate."')=0) 
					)
	
			GROUP BY COS.StudyTimeHour, COS.StudyTimeMinute
	";

	
	$Sql2 = "select 
					sum(ClassOrderTimeTypeID/ClassOrderTimeTypeID) as ClassCount,
					sum(ClassOrderTimeTypeID) as MinuteCount 
			from ($ViewTable) V 
	";

	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	$Stmt2 = null;
	$MinuteCount = $Row2["MinuteCount"];
	$ClassCount = round($Row2["ClassCount"],0);

	if ($MinuteCount==""){
		$MinuteCount = 0;
	}
	if ($ClassCount==""){
		$ClassCount = 0;
	}

	$ArrTeacherClassCount[$TeacherNum] = "$ClassCount/$MinuteCount";

}
//강사 브레이크 타입 검색


$ForceBlockSlotIDs = "|";
$ScheduleTable = "";


//=====================================================  AAAA
$HourListCount = 0;

//for ($HourNum=$MinTeacherHour;$HourNum<=$MaxTeacherHour-1;$HourNum++){
for ($HourNum=$StartHour;$HourNum<$EndHour;$HourNum++){//해당되는 리스트만 가져온다.

	if ($HourListCount % 3 == 0){
		$ScheduleTable .= "<thead>";
		$ScheduleTable .= "<tr>";

		for ($TeacherNum=1;$TeacherNum<=$TeacherCount;$TeacherNum++){
			if (($TeacherNum-1) % 8 == 0){
				$ScheduleTable .= "<th nowrap rowspan=\"2\">시</th>";
				$ScheduleTable .= "<th nowrap rowspan=\"2\">분</th>";
			}


			$StrBlock80Min = "";
			if ($ArrTeacherBlock80Min[$TeacherNum]==0){
				$StrBlock80Min = " (Over 80min)";
			}

			$ScheduleTable .= "<th nowrap colspan=\"".$WorkDayCount."\" class=\"TdTeacherName_".$TeacherNum."\" style=\"width:70px;\">".$ArrTeacherName[$TeacherNum]." ".$StrBlock80Min." - ".$ArrTeacherClassCount[$TeacherNum]."</th>";
		}


		$ScheduleTable .= "</tr>";
		$ScheduleTable .= "<tr>";

		for ($TeacherNum=1;$TeacherNum<=$TeacherCount;$TeacherNum++){
			//for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){
				//if ($EduCenterHoliday[$WeekDayNum]==0) {
					$ScheduleTable .= "<th nowrap class=\"TdWeekName_".$TeacherNum."_".$WeekDayNum."\">".$ArrWeekDayStr[$WeekDayNum]."</th>";
				
				//}
			//}
				
		}
	
		$ScheduleTable .= "</tr>";
		$ScheduleTable .= "</thead>";
		$ScheduleTable .= "<tbody>";
	}	


	$ScheduleTable .= "<tr>";
	for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){
		
		for ($TeacherNum=1;$TeacherNum<=$TeacherCount;$TeacherNum++){
//=====================================================  AAAA
	

			if (($TeacherNum-1) % 8 == 0){
				if ($MinuteNum == 0){
					if($HourNum > 12) {
						$StrHourNum = "PM ".((int)$HourNum-12);
					} else {
						$StrHourNum = "AM ".$HourNum;
					}
					$ScheduleTable .= "<th rowspan=\"6\">".$StrHourNum."</th>";

				}

				$ScheduleTable .= "<th class=\"TdMinuteNum_".$HourNum."_".$MinuteNum."\">".$MinuteNum."</th>";
			}



			$TeacherID = $ArrTeacherID[$TeacherNum];//현재 슬랏 교사 아이디 


			//=====================================================  BBBB
			//for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){
				//if ($EduCenterHoliday[$WeekDayNum]==0) {
			//=====================================================  BBBB

					$SlotBreakEvent = 0;
					$SlotBreakEventName = "";//"선택";
					$SlotBreakEventName2 = "";//"선택";
					$SlotBreakEventCode = 0;
					$SlotBreakEventType = 0;//1 이면 ClassOrderSlots 기반 === KKK
					$TempClassOrderSlotMaster = 0;//1 이면 ClassOrderSlots 기반 === KKK

					if ($SlotBreakEvent ==0 ){//교사 수업가능 시간 검색
						if ($HourNum < $ArrTeacherStartHour[$TeacherNum] || $HourNum >= $ArrTeacherEndHour[$TeacherNum] ){
							$SlotBreakEvent = 1;
							$BgColor = "#888888";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "궐석";
							$SlotBreakEventName2 = "궐석";
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
							$SlotBreakEventName = "(개)식사";
							$SlotBreakEventName2 = "(개)식사";
							$SlotBreakEventCode = 31;
						}else if ($EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum]==3) {
							$SlotBreakEvent = 1;
							$BgColor = "#CC9933";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "(개)휴식";
							$SlotBreakEventName2 = "(개)휴식";
							$SlotBreakEventCode = 41;
						}else if ($EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum]==4) {
							$SlotBreakEvent = 1;
							$BgColor = "#CC6666";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "(개)블락";
							$SlotBreakEventName2 = "(개)블락";
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
							$SlotBreakEventName2 = "식사";
							$SlotBreakEventCode = 61;
						}else if ($TeacherBreak[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum]==3) {
							$SlotBreakEvent = 1;
							$BgColor = "#CC9933";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "휴식";
							$SlotBreakEventName2 = "휴식";
							$SlotBreakEventCode = 71;
						}else if ($TeacherBreak[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum]==4) {
							$SlotBreakEvent = 1;
							$BgColor = "#CC6666";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "블락";
							$SlotBreakEventName2 = "블락";
							$SlotBreakEventCode = 81;
						}else if ($TeacherBreak[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum]==5) {
							$SlotBreakEvent = 1;
							$BgColor = "#CC6666";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "";
							$SlotBreakEventName2 = "";
							$SlotBreakEventCode = 81;
						}

					}


					
					$ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 0;//수업아님, 80분 연속수업 검색용


					if ($SlotBreakEvent ==0 ){//해당날짜 임시 수업 검색

						$TargetDate = $SearchDate;
						$TempYear = date('Y', strtotime($TargetDate));
						$TempMonth = date('m', strtotime($TargetDate));
						$TempDay = date('d', strtotime($TargetDate));

						if ($SearchType=="1"){//정규수업 배정기준
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

									left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$TempYear." and CLS.StartMonth=".$TempMonth." and CLS.StartDay=".$TempDay." and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID and CLS.ClassAttendState<>99 ".$Sql3Where2."
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

						$Stmt3 = $DbConn->prepare($Sql3);
						$Stmt3->execute();
						$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
						$Row3 = $Stmt3->fetch();
						$Stmt3 = null;
						$ClassOrderSlotCount = $Row3["ClassOrderSlotCount"];

						if ($ClassOrderSlotCount>0){

							$SlotBreakEvent = 1;
							$SlotBreakEventCode = 11;
							$SlotBreakEventType = 1; // === KKK
							$ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;//수업, 80분 연속수업 검색용


							$TargetDate = $SearchDate;
							$TempYear = date('Y', strtotime($TargetDate));
							$TempMonth = date('m', strtotime($TargetDate));
							$TempDay = date('d', strtotime($TargetDate));

							$Sql3 = "select 
											COS.ClassOrderID,
											COS.ClassOrderSlotMaster,
											CO.ClassMemberType,
											CO.ClassProductID,
											MB.MemberID,
											MB.MemberName,
											MB.MemberLoginID,
											MB.MemberNickName,
											COT.ClassOrderTimeSlotCount
									from ClassOrderSlots COS 
										inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
										inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 
										inner join Members MB on CO.MemberID=MB.MemberID 
										inner join ClassOrderTimeTypes COT on CO.ClassOrderTimeTypeID=COT.ClassOrderTimeTypeID 

										left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$TempYear." and CLS.StartMonth=".$TempMonth." and CLS.StartDay=".$TempDay." and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID and CLS.ClassAttendState<>99 ".$Sql3Where2."
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

							$Stmt3 = $DbConn->prepare($Sql3);
							$Stmt3->execute();
							$Stmt3->setFetchMode(PDO::FETCH_ASSOC);


							//if ($ClassOrderID){
							$MemberListNum=1;
							$OldTempClassOrderID = 0;
							while($Row3 = $Stmt3->fetch()) {

								$TempClassOrderID = $Row3["ClassOrderID"];
								$TempClassOrderSlotMaster = $Row3["ClassOrderSlotMaster"]; // === KKK
								$TempClassMemberType = $Row3["ClassMemberType"];
								$TempClassOrderTimeSlotCount = $Row3["ClassOrderTimeSlotCount"];

								$ClassProductID = $Row3["ClassProductID"];
								
								if ($TempClassMemberType==1 && $MemberListNum==1){

									$BgColor = "#69B7DA";
									$FontColor = "#FFFFFF";
									
									if ($ClassProductID==1){
										$SlotBreakEventName = "(임)수업 ";
										$SlotBreakEventName2 = "(임)수업 ";
									}else if ($ClassProductID==2){
										$SlotBreakEventName = "(임)레벨 ";
										$SlotBreakEventName2 = "(임)레벨 ";
									}else if ($ClassProductID==3){
										$SlotBreakEventName = "(임)체험 ";
										$SlotBreakEventName2 = "(임)체험 ";
									}

								}else if (($TempClassMemberType==2 || $TempClassMemberType==3) && $MemberListNum==1){//그룹수업이면
										
									$BgColor = "#A6CAEA";
									$FontColor = "#FFFFFF";

									if ($ClassProductID==1){
										if ($TempClassMemberType==2){
											$SlotBreakEventName = "(임)1:2수업(".($TempClassOrderTimeSlotCount*10)."분) ";
											$SlotBreakEventName2 = "(임)1:2수업(".($TempClassOrderTimeSlotCount*10)."분) ";
										}else{
											$SlotBreakEventName = "(임)그룹수업(".($TempClassOrderTimeSlotCount*10)."분) ";
											$SlotBreakEventName2 = "(임)그룹수업(".($TempClassOrderTimeSlotCount*10)."분) ";
										}
									}else if ($ClassProductID==2){
										if ($TempClassMemberType==2){
											$SlotBreakEventName = "(임)1:2레벨 ";//이런경우는 없음
											$SlotBreakEventName2 = "(임)1:2레벨 ";//이런경우는 없음
										}else{
											$SlotBreakEventName = "(임)그룹레벨 ";//이런경우는 없음
											$SlotBreakEventName2 = "(임)그룹레벨 ";//이런경우는 없음
										}
									}else if ($ClassProductID==3){
										if ($TempClassMemberType==2){
											$SlotBreakEventName = "(임)1:2체험 ";
											$SlotBreakEventName2 = "(임)1:2체험 ";
										}else{
											$SlotBreakEventName = "(임)그룹체험 ";
											$SlotBreakEventName2 = "(임)그룹체험 ";
										}
									}

									//신청중인 수업이 그룹, 기존 수업이 그룹, 20/40분 수업이 동일, 수업의 시작 슬롯, 선택가능으로...
									//if ( ($TempClassMemberType==2 || $TempClassMemberType==3) && $ClassOrderTimeSlotCount==$TempClassOrderTimeSlotCount ){
									//	$SlotBreakEvent = 0;
									//	$SlotBreakEventCode = 0;
									//}
								}

								if ($TempClassMemberType==2 && $MemberListNum>1){
									$SlotBreakEvent = 1;//1:2 수업이고 2명 이상일때
									$SlotBreakEventCode = 11;
								}

								$MemberID = $Row3["MemberID"];
								$MemberName = $Row3["MemberName"];
								$MemberLoginID = $Row3["MemberLoginID"];
								$MemberNickName = $Row3["MemberNickName"];

								if ($MemberListNum>1){
									$SlotBreakEventName .= " , ".$MemberName."(".$MemberLoginID.")";
									$SlotBreakEventName2 .= " , ".$MemberName."(".$MemberLoginID.") <i onclick=\"OpenStudentCalendar(".$MemberID.")\" class=\"material-icons\" style=\"cursor:pointer;\">date_range</i>";
								}else{
									$SlotBreakEventName .= " / ".$MemberName."(".$MemberLoginID.")";
									$SlotBreakEventName2 .= " / ".$MemberName."(".$MemberLoginID.") <i onclick=\"OpenStudentCalendar(".$MemberID.")\" class=\"material-icons\" style=\"cursor:pointer;\">date_range</i>";
								}


								$MemberListNum++;
							
							}
							$Stmt3 = null;
						
						}

					}


					if ($SlotBreakEvent ==0 ){//교사 기존 수업 검색


						$TargetDate = $SearchDate;
						$TempYear = date('Y', strtotime($TargetDate));
						$TempMonth = date('m', strtotime($TargetDate));
						$TempDay = date('d', strtotime($TargetDate));

						if ($SearchType=="1"){//정규수업 배정기준
							$Sql3Where = " ";
						}else{
							$Sql3Where = " and ( CLS.ClassAttendState is NULL or CLS.ClassAttendState<4 )  ";
							$Sql3Where = $Sql3Where . " and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 ";
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
										 

										and COS.ClassOrderSlotState=1 
										and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0) ) 
										".$Sql3Where." 

						";
						//and (datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0 or CO.ClassOrderEndDate is null) 제거 2019-12-22


						$Stmt = $DbConn->prepare($Sql);
						$Stmt->execute();
						$Stmt->setFetchMode(PDO::FETCH_ASSOC);
						$Row = $Stmt->fetch();
						$Stmt = null;
						$ClassOrderSlotCount = $Row["ClassOrderSlotCount"];

						if ($ClassOrderSlotCount>0) {

							$SlotBreakEvent = 1;
							$SlotBreakEventType = 1; // === KKK

							$BgColor = "#CC99FF";
							$FontColor = "#FFFFFF";
							$SlotBreakEventName = "수업";//"수업";
							$SlotBreakEventName2 = "수업";//"수업";
							$SlotBreakEventCode = 11;
							$ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = 1;//수업, 80분 연속수업 검색용
						
							$TargetDate = $SearchDate;
							$TempYear = date('Y', strtotime($TargetDate));
							$TempMonth = date('m', strtotime($TargetDate));
							$TempDay = date('d', strtotime($TargetDate));

							$Sql = "select 
										COS.ClassOrderID,
										COS.ClassOrderSlotMaster,
										CO.ClassMemberType,
										CO.ClassProductID,
										CO.ClassOrderEndDate,
										datediff(CO.ClassOrderEndDate, '".$TargetDate."') as EndDateDiff,
										MB.MemberID,
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
										 

										and COS.ClassOrderSlotState=1 
										and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0) ) 
										".$Sql3Where." 
								order by CO.ClassOrderID asc
								";
								//and (datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0 or CO.ClassOrderEndDate is null) 제거 2019-12-22

							

							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC); 
							
							$MemberListNum = 1;
							while($Row = $Stmt->fetch()) {

								$TempClassOrderID = $Row["ClassOrderID"];
								$TempClassOrderSlotMaster = $Row["ClassOrderSlotMaster"]; // === KKK
								$TempClassMemberType = $Row["ClassMemberType"];
								$TempClassOrderTimeSlotCount = $Row["ClassOrderTimeSlotCount"];
								$TempClassOrderEndDate = $Row["ClassOrderEndDate"];
								$TempEndDateDiff = $Row["EndDateDiff"];

								$StrEndDateDiff = "";
								if ($TempEndDateDiff<0 && $TempClassOrderEndDate!=""){
									$StrEndDateDiff = "(★)";
								}

								if (($TempClassMemberType==2 || $TempClassMemberType==3) && $MemberListNum==1){//그룹수업이면
										
									$BgColor = "#FFCCFF";
									$FontColor = "#FFFFFF";
									if ($TempClassMemberType==2){
										$SlotBreakEventName = "1:2수업(".($TempClassOrderTimeSlotCount*10)."분)";
										$SlotBreakEventName2 = "1:2수업(".($TempClassOrderTimeSlotCount*10)."분)";
									}else{
										$SlotBreakEventName = "그룹수업(".($TempClassOrderTimeSlotCount*10)."분)";
										$SlotBreakEventName2 = "그룹수업(".($TempClassOrderTimeSlotCount*10)."분)";
									}

								}

								if ($TempClassMemberType==2 && $MemberListNum>1){
									$SlotBreakEvent = 1;//1:2 수업이고 2명 이상일때
									$SlotBreakEventCode = 11;
								}

								$MemberID = $Row["MemberID"];
								$MemberName = $Row["MemberName"];
								$MemberLoginID = $Row["MemberLoginID"];
								$MemberNickName = $Row["MemberNickName"];

								if ($MemberListNum>1){
									$SlotBreakEventName .= " , ".$StrEndDateDiff.$MemberName."(".$MemberLoginID.")";
									$SlotBreakEventName2 .= " , ".$StrEndDateDiff.$MemberName."(".$MemberLoginID.") <i onclick=\"OpenStudentCalendar(".$MemberID.")\" class=\"material-icons\" style=\"cursor:pointer;\">date_range</i>";
								}else{
									$SlotBreakEventName .= " / ".$StrEndDateDiff.$MemberName."(".$MemberLoginID.")";
									$SlotBreakEventName2 .= " / ".$StrEndDateDiff.$MemberName."(".$MemberLoginID.") <i onclick=\"OpenStudentCalendar(".$MemberID.")\" class=\"material-icons\" style=\"cursor:pointer;\">date_range</i>";
								}

								$MemberListNum++;
							}
							$Stmt = null;
						}

					}





					if ($SlotBreakEvent ==0 ){//식사,휴식,블락(임시) 검색

						$TargetDate = $SearchDate;
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
								$SlotBreakEventName2 = "식사";
								$SlotBreakEventCode = 61;
							}else if ($TeacherBreakTimeTempType==3) {
								$SlotBreakEvent = 1;
								$BgColor = "#CC9933";
								$FontColor = "#FFFFFF";
								$SlotBreakEventName = "휴식";
								$SlotBreakEventName2 = "휴식";
								$SlotBreakEventCode = 71;
							}else if ($TeacherBreakTimeTempType==4) {
								$SlotBreakEvent = 1;
								$BgColor = "#CC6666";
								$FontColor = "#FFFFFF";
								$SlotBreakEventName = "블락";
								$SlotBreakEventName2 = "블락";
								$SlotBreakEventCode = 81;
							}
						}

					}


					//$ForceBlock = 0;
					if ($SlotBreakEvent ==0  && $ArrTeacherBlock80Min[$TeacherNum]==1){//80분 연속수업인지 검색
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
								
							if ($TempHourNum>=$MinTeacherHour){
								
								if ($ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$TempHourNum][$TempMinuteNum] && $ForceBlockSlotCheck[$TeacherNum][$WeekDayNum][$TempHourNum][$TempMinuteNum] == 1){
									$AccrueNumCount++;
								}
								
								$aaaa = "";
								if ($AccrueNumCount==8){
									/*
									$ForceBlock = 1;
									$SlotBreakEvent = 1;
									$BgColor = "#FF0000";
									$FontColor = "#FFFFFF";
									$SlotBreakEventName = "강휴";
									$SlotBreakEventName2 = "강휴";
									*/
									
									//$SlotBreakEventCode = 101 - 사용자단에서 js 로 변경
									$ForceBlockSlotIDs .= "Div_Slot_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."|";
									
									//이전 8슬랏 위로 올라가서 강제 휴식 처리
									if ($MinuteListNum-9>0){
										$ForceBlockSlotIDs .= "Div_Slot_".$TeacherNum."_".$WeekDayNum."_".($MinuteListNum-9)."|";
									}
								}

							}

						}
					}


					$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = "<td nowrap id=\"Div_Slot_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" style=\"background-color:".$BgColor.";color:".$FontColor.";text-align:center;font-size:11px;\" title=\"".$SlotBreakEventName."\" class=\"TdSlot_".$TeacherNum."_".$WeekDayNum." TdSlot_".$HourNum."_".$MinuteNum."\" onclick=\"EventMouseOver(".$TeacherNum.",".$WeekDayNum.",".$HourNum.",".$MinuteNum.")\">".$SlotBreakEventName2."";
					$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Slot_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Slot_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" value=\"0\">";
					$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Able_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Able_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" value=\"0\" style=\"background-color:#ff0000;\">";
					$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Break_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Break_".$TeacherNum."_".$WeekDayNum."_".$MinuteListNum."\" value=\"".$SlotBreakEventCode."\">";
					$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] .= "</td>";


					/*
					if ($SlotBreakEventType==1 && $TempClassOrderSlotMaster==0){// === KKK


						$TempSlotWeekDayNum = $WeekDayNum;
						$TempSlotHourNum = $HourNum;
						$TempSlotMinuteNumNum = $MinuteNum - 10;
						$TempSlotMinuteListNum = $MinuteListNum - 1;
						
						if ($TempSlotMinuteNumNum<0){
							$TempSlotHourNum = $TempSlotHourNum - 1;
							$TempSlotMinuteNumNum = 60 + $TempSlotMinuteNumNum;
						}

						$TempConvTableHTML = $TableHTML[$TeacherNum][$TempSlotWeekDayNum][$TempSlotHourNum][$TempSlotMinuteNumNum];
						
						$TempConvTableHTML = str_replace($TeacherNum."_".$TempSlotWeekDayNum."_".$TempSlotMinuteListNum, $TeacherNum."_".$WeekDayNum."_".$MinuteListNum, $TempConvTableHTML);

						$TempConvTableHTML = str_replace($TeacherNum."_".$TempSlotWeekDayNum, $TeacherNum."_".$WeekDayNum, $TempConvTableHTML);

						$TempConvTableHTML = str_replace($TempSlotHourNum."_".$TempSlotMinuteNumNum, $HourNum."_".$MinuteNum, $TempConvTableHTML);

						$TempConvTableHTML = str_replace($TeacherNum.",".$TempSlotWeekDayNum.",".$TempSlotHourNum.",".$TempSlotMinuteNumNum, $TeacherNum.",".$WeekDayNum.",".$HourNum.",".$MinuteNum, $TempConvTableHTML);

						$TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum] = $TempConvTableHTML;

					}
					*/


					$ScheduleTable .= $TableHTML[$TeacherNum][$WeekDayNum][$HourNum][$MinuteNum];// === KKK

			//=====================================================  BBBB
				//}
			//}
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
$ArrValue["ForceBlockSlotIDs"] = $ForceBlockSlotIDs;
$ArrValue["ScheduleTable"] = $ScheduleTable;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>