<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$ErrNum = 0;
$ErrMsg = "";

$EduCenterID = isset($_REQUEST["EduCenterID"]) ? $_REQUEST["EduCenterID"] : "";
$SearchSex = isset($_REQUEST["SearchSex"]) ? $_REQUEST["SearchSex"] : "";
$SearchChar = isset($_REQUEST["SearchChar"]) ? $_REQUEST["SearchChar"] : "";
$SearchTeacherPayTypeItemID = isset($_REQUEST["SearchTeacherPayTypeItemID"]) ? $_REQUEST["SearchTeacherPayTypeItemID"] : "";
$SearchTeacherID = isset($_REQUEST["SearchTeacherID"]) ? $_REQUEST["SearchTeacherID"] : "";

$ClassOrderStartDate = isset($_REQUEST["ClassOrderStartDate"]) ? $_REQUEST["ClassOrderStartDate"] : "";//지정날짜

//개인결제를 할 수 있는지 못 하는지를 체크하기 위해 개인 정보를 가져온다.
$MemberID = $_LINK_MEMBER_ID_;

$MemberPayType = "";
$CenterPayType = "";
$CenterRenewType = "";
$CenterStudyEndDate = "";

if (!empty($MemberID)) {
    $Sql = "SELECT
            A.MemberPayType,
            B.CenterPayType,
            B.CenterRenewType,
            B.CenterStudyEndDate
        from Members A
        inner join Centers B on A.CenterID=B.CenterID
        where A.MemberID=:MemberID and MemberLevelID=19";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':MemberID', $MemberID, PDO::PARAM_INT);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;
    if ($Row) {
        $MemberPayType = $Row["MemberPayType"];
        $CenterPayType = $Row["CenterPayType"];
        $CenterRenewType = $Row["CenterRenewType"];
        $CenterStudyEndDate = $Row["CenterStudyEndDate"];
    }
}



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





$ClassOrderStartDateWeek = date('w', strtotime($ClassOrderStartDate));
$ArrClassOrderStartDate[0] = date("Y-m-d", strtotime("-".$ClassOrderStartDateWeek." day", strtotime($ClassOrderStartDate)));
for ($iiii=1;$iiii<=6;$iiii++){
	$ArrClassOrderStartDate[$iiii] = date("Y-m-d", strtotime($iiii." day", strtotime($ArrClassOrderStartDate[0])));
}


$ClassMemberType = 1;
$ClassOrderID = 0;
$ClassProgress = 0;


$ArrWeekDay = explode(",","일요일,월요일,화요일,수요일,목요일,금요일,토요일");



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
for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){
	if ($EduCenterHoliday[$WeekDayNum]==1){
		$WorkDayCount--;
	}
}
//교육센터 정기휴일 검색


//교육센터 브레이크 타임 검색
for ($HourNum=0;$HourNum<=24-1;$HourNum++){
	for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){
		for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){
			if ($EduCenterHoliday[$WeekDayNum]==0) {
				$EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum] = 1;//[요일/시/분] 1은 수업가능
			}
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


if ($SearchSex!="0"){
	$AddSqlWhere .= " and C.MemberSex=".$SearchSex;
}

if ($SearchChar!=""){
	$ArrSearchChar = explode("|",$SearchChar);

	for ($ItemNum=1;$ItemNum<=count($ArrSearchChar)-1;$ItemNum++){
		$AddSqlWhere .= " and A.TeacherID in ( select TeacherID from TeacherCharacters where TeacherCharacterItemID=".$ArrSearchChar[$ItemNum]." ) ";
	}

}

if ($SearchTeacherID!=""){
	$AddSqlWhere .= " and A.TeacherID=".$SearchTeacherID;
}

$AddSqlWhere = $AddSqlWhere . " and A.TeacherState=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherView=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherPayTypeItemID=$SearchTeacherPayTypeItemID ";
$AddSqlWhere = $AddSqlWhere . " and B.EduCenterID=$EduCenterID ";
$AddSqlWhere = $AddSqlWhere . " and B.TeacherGroupState=1 ";
if($_LINK_MEMBER_LEVEL_ID_> 4) { // 3 : 프랜차이즈장, 4 : 프랜차이즈 직원, 5 : 영업
	$AddSqlWhere = $AddSqlWhere . " and ( A.TeacherGroupID=4 or A.TeacherGroupID=9 or A.TeacherGroupID=6 ) ";//Home-based, General Teacher, 미국오후반
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
$TeacherBlock80Min = "";

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
			for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){
				if ($EduCenterHoliday[$WeekDayNum]==0) {
					$TableHTML[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = "";//테이블구성 === KKK
					$SlotBreakEvent[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 0;//테이블구성 === KKK
					$TeacherBreak[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 1;//[요일/시/분] 1은 수업가능
					$ForceBlockSlotCheck[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 0;//[강사순번/요일/시/분] 1은 수업가능, 80분 연속수업 검색용
				}
			}
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
		
		$TeacherBreak[$TeacherID][$TeacherBreakTimeWeek][$TeacherBreakTimeHour][$TeacherBreakTimeMinute] = $TeacherBreakTimeType;//[강사순번/요일/시/분] 1은 수업가능
	
	}
	$Stmt2 = null;
	//강사 브레이크 타임 검색
	
	
	if ($TeacherImageFileName==""){
		$StrTeacherImageFileName = "/images/no_photo_2.png";
	}else{
		$StrTeacherImageFileName = "/uploads/teacher_images/".$TeacherImageFileName;
	}


	if ($SearchTeacherID==""){
		$TeacherListHTML .= "<li>";
		$TeacherListHTML .= "	<div class=\"teacher_photo_wrap\">";
		$TeacherListHTML .= "		<img src=\"".$StrTeacherImageFileName."\" alt=\"".$TeacherName."\" class=\"teacher_photo\">";
		$TeacherListHTML .= "		<a href=\"#\" class=\"teacher_select_btn\" onclick=\"GetTeacherList(1,".$TeacherID.")\">강사선택 <span class=\"teacher_select_arrow\"></span></a>";
		$TeacherListHTML .= "	</div>";
		$TeacherListHTML .= "	<div class=\"teacher_time_wrap\" id=\"DivTeacherTimeBox_".$TeacherID."\">";
	}

	if ($SearchTeacherID!=""){

			$TeacherListHTML .= "		<div class=\"teacher_time_caption\"><img src=\"images/icon_time.png\">수강가능시간</div>";
			$TeacherListHTML .= "		<span class=\"teacher_time_line\"></span>";
			$TeacherListHTML .= "		<table class=\"teacher_time_table\">";

			for ($WeekDayNum=0;$WeekDayNum<=6;$WeekDayNum++){

				$TargetDate = $ArrClassOrderStartDate[$WeekDayNum];

				if ($EduCenterHoliday[$WeekDayNum]==0){

				
					$TeacherListHTML .= "			<tr>";
					$TeacherListHTML .= "				<th valign=\"top\" style=\"padding-top:10px;line-height:1.5;\">".$ArrWeekDay[$WeekDayNum]."<!--<br>".str_replace("-","/",substr($ArrClassOrderStartDate[$WeekDayNum],-5))."--></th>";
					$TeacherListHTML .= "				<td>";

					
					$MinuteListNum=1;
					$OldMinuteListNum = 0;
					for ($HourNum=$TeacherStartHour;$HourNum<$TeacherEndHour;$HourNum++){

						for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){


							$SlotBreakEventCode = 0;
							$ForceBlockSlotCheck[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 0;//수업아님, 80분 연속수업 검색용

							$SlotBreakEventType = 0;//1 이면 ClassOrderSlots 기반 === KKK
							$TempClassOrderSlotMaster = 0;//1 이면 ClassOrderSlots 기반 === KKK

							$TempYear = date('Y', strtotime($TargetDate));
							$TempMonth = date('m', strtotime($TargetDate));
							$TempDay = date('d', strtotime($TargetDate));

							
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


											and COS.ClassOrderSlotState=1 
											and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0) )
							";
											//수강신청에는 다른 수강신청의 시작날짜를 검토하지 않는다.
											//and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 
											//and (datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0 or CO.ClassOrderEndDate is null) 제거 2019-12-22

							$Stmt3 = $DbConn->prepare($Sql3);
							$Stmt3->execute();
							$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
							$Row3 = $Stmt3->fetch();
							$ClassOrderSlotCount = $Row3["ClassOrderSlotCount"];

							$SlotBreakEvent[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 0;
							if ($ClassOrderSlotCount>0) {
								$SlotBreakEventType = 1; // === KKK
								$SlotBreakEvent[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 1;
								$SlotBreakEventCode = 11;
								$ForceBlockSlotCheck[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 1;//수업, 80분 연속수업 검색용 


								$Sql3 = "select 
												COS.ClassOrderSlotMaster
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
								";
												//수강신청에는 다른 수강신청의 시작날짜를 검토하지 않는다.
												//and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0 
												//and (datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0 or CO.ClassOrderEndDate is null) 제거 2019-12-22


								$Stmt3 = $DbConn->prepare($Sql3);
								$Stmt3->execute();
								$Stmt3->setFetchMode(PDO::FETCH_ASSOC);


								while($Row3 = $Stmt3->fetch()) {
									$TempClassOrderSlotMaster = $Row3["ClassOrderSlotMaster"]; // === KKK
								}
								$Stmt3 = null;

							}


							if ($SlotBreakEvent[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] ==0 ){//해당날짜 임시 수업 검색

								$TempYear = date('Y', strtotime($TargetDate));
								$TempMonth = date('m', strtotime($TargetDate));
								$TempDay = date('d', strtotime($TargetDate));
								
								$Sql3 = "select 
												count(*) as ClassOrderSlotCount
										from ClassOrderSlots COS 
											inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
											inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 
											inner join Members MB on CO.MemberID=MB.MemberID 

											left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID and datediff(CLS.StartDateTime,'".$TargetDate."')>=0 
										where 
												COS.StudyTimeWeek=".$WeekDayNum." 
												and COS.StudyTimeHour=".$HourNum." 
												and COS.StudyTimeMinute=".$MinuteNum." 
												and COS.TeacherID=".$TeacherID." 
												and datediff(COS.ClassOrderSlotDate,'".$TargetDate."')>=0 
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
									$SlotBreakEvent[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 1;
									$BgColor = "#CC99FF";
									$FontColor = "#FFFFFF";
									$SlotBreakEventName = "수업";
									$SlotBreakEventCode = 11;
									$ForceBlockSlotCheck[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 1;//레벨테스트, 80분 연속수업 검색용


									$Sql3 = "select 
													COS.ClassOrderSlotMaster
											from ClassOrderSlots COS 
												inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 
												inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 
												inner join Members MB on CO.MemberID=MB.MemberID 

												left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID and datediff(CLS.StartDateTime,'".$TargetDate."')>=0
											where 
													COS.StudyTimeWeek=".$WeekDayNum." 
													and COS.StudyTimeHour=".$HourNum." 
													and COS.StudyTimeMinute=".$MinuteNum." 
													and COS.TeacherID=".$TeacherID." 
													and datediff(COS.ClassOrderSlotDate,'".$TargetDate."')>=0 
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
							



							if ($SlotBreakEvent[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] ==0 ){//식사,휴식,블락(임시) 검색

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
										$SlotBreakEvent[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 1;
										$BgColor = "#FFCC00";
										$FontColor = "#FFFFFF";
										$SlotBreakEventName = "식사";
										$SlotBreakEventCode = 61;
									}else if ($TeacherBreakTimeTempType==3) {
										$SlotBreakEvent[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 1;
										$BgColor = "#CC9933";
										$FontColor = "#FFFFFF";
										$SlotBreakEventName = "휴식";
										$SlotBreakEventCode = 71;
									}else if ($TeacherBreakTimeTempType==4) {
										$SlotBreakEvent[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 1;
										$BgColor = "#CC6666";
										$FontColor = "#FFFFFF";
										$SlotBreakEventName = "블락";
										$SlotBreakEventCode = 81;
									}
								}


							}


							if ($SlotBreakEvent[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] ==0  && $TeacherBlock80Min==1){//80분 연속수업인지 검색
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
										
										if ($ForceBlockSlotCheck[$TeacherID][$WeekDayNum][$TempHourNum][$TempMinuteNum] && $ForceBlockSlotCheck[$TeacherID][$WeekDayNum][$TempHourNum][$TempMinuteNum] == 1){
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


							

							if ($EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum]==1 && $TeacherBreak[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum]==1 && $SlotBreakEvent[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum]==0){
								
								$TableHTML[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] =  "<span id=\"Div_Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" class=\"teacher_time\" onclick=\"SelectSlot(".$TeacherID.",".$WeekDayNum.",".$MinuteListNum.",".$TeacherBlock80Min.");\">".substr("0".$HourNum,-2).":".substr("0".$MinuteNum,-2)."";

									$TableHTML[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"|".$TeacherID."_".$WeekDayNum."_".$HourNum."_".$MinuteNum."\">";
									$TableHTML[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Able_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Able_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"1\" style=\"background-color:#cccccc;\">";
									$TableHTML[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Break_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Break_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"".$SlotBreakEventCode."\">";
								
								$TableHTML[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] .= "</span>";


							}else{ 

								//$SlotBreakEventCode = 100;//이곳에서는 큰 의미가 없어 100으로 통일
								
								$TableHTML[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = "<span id=\"Div_Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" class=\"teacher_time\" style=\"background-color:#bbbbbb;cursor:default;display:none;\">".substr("0".$HourNum,-2).":".substr("0".$MinuteNum,-2)."";

									$TableHTML[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"0\">";
									$TableHTML[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Able_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Able_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"0\" style=\"background-color:#ff0000;\">";
									$TableHTML[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] .= "<input type=\"hidden\" name=\"Break_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" id=\"Break_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" value=\"".$SlotBreakEventCode."\">";

								$TableHTML[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] .= "</span>";
								
								if ($MinuteListNum-$OldMinuteListNum!=1){//연속될 경우 보여주지 않는다.( '-' 로 보여줌)
									$TableHTML[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] .= "<span id=\"Div_Slot_".$TeacherID."_".$WeekDayNum."_".$MinuteListNum."\" class=\"teacher_time\" style=\"cursor:default;\">-</span>";
								}
								$OldMinuteListNum = $MinuteListNum;
			
							}


							$TeacherListHTML .= $TableHTML[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum];// === KKK



							$MinuteListNum++;
						}
					
						
						
					}
					
					$TeacherListHTML .= "				</td>";
					$TeacherListHTML .= "			</tr>";
				}
			
			}


	

			$TeacherListHTML .= "		</table>";

			
			if ($MemberPayType!=1 && $CenterPayType != 2 ){
				$TeacherListHTML .= "		<a href=\"javascript:alert('회원님은 개인결제를 이용하지 않습니다. 관리자에게 문의해 주세요.');\" class=\"teacher_select_submit\">수강신청하기</a>";
			} else {
				$TeacherListHTML .= "		<a href=\"javascript:ClassOrderSubmit();\" class=\"teacher_select_submit\">수강신청하기</a>";
			}

	}
	
	if ($SearchTeacherID==""){

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
$Stmt2 = null;


$Sql = "
		select 
				A.TeacherPayTypeItemCenterPriceX
		from TeacherPayTypeItems A 
		where A.TeacherPayTypeItemID=$SearchTeacherPayTypeItemID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TeacherPayTypeItemCenterPriceX = $Row["TeacherPayTypeItemCenterPriceX"];


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["TeacherNum"] = $TeacherNum-1;//출력할 강사 숫자
$ArrValue["ForceBlockSlotIDs"] = $ForceBlockSlotIDs;
$ArrValue["TeacherPayTypeItemCenterPriceX"] = $TeacherPayTypeItemCenterPriceX;
$ArrValue["TeacherListHTML"] = $TeacherListHTML;
$ArrValue["WeekStartDate"] = str_replace("-",".",$ArrClassOrderStartDate[0]);
$ArrValue["WeekEndDate"] = str_replace("-",".",$ArrClassOrderStartDate[6]);
$ArrValue["TeacherBlock80Min"] = $TeacherBlock80Min;//$SearchTeacherID 가 있을때만 사용됨

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>