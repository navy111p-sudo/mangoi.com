<?php // Use <?php instead of <? for better compatibility

// Keep original function definition and comments
function CheckStudyTime($EduCenterID, $StudyTimeDate, $StudyTimeHour, $StudyTimeMinute, $ClassOrderTimeTypeID, $ClassProductID, $LinkAdminLevelID){

    // ***** 디버깅 활성화 스위치 *****
    // 디버깅 메시지를 보려면 이 값을 true로 변경하세요.
    // 운영 환경에서는 반드시 false로 설정해야 합니다.
    $debugCheckStudyTime = false;
    // ***** 디버깅 활성화 스위치 끝 *****

    // ***** DEBUG POINT 1: 함수 시작 확인 *****
    if ($debugCheckStudyTime) {
        echo "<div style='background:yellow; color:black; padding: 2px; border: 1px solid black; font-size: 10px; position:fixed; top:0; left:0; z-index:9999;'>[DEBUG] CheckStudyTime Called: Date=$StudyTimeDate, Time=$StudyTimeHour:$StudyTimeMinute, ProdID=$ClassProductID</div>";
        @ob_flush(); @flush();
    }
    // ***** END DEBUG POINT 1 *****

    // --- Input Validation (Recommended, but keeping original structure) ---
    if (!isset($GLOBALS['DbConn'])) { return ""; } // Ensure DB connection


    $ClassOrderTimeSlotCount = $ClassOrderTimeTypeID;

    $SelectTimeWeek = date('w', strtotime($StudyTimeDate));
    $ArrWeekDayStr = explode(",","일요일,월요일,화요일,수요일,목요일,금요일,토요일"); // Ensure this is defined/available globally or passed in
    $WeekDayStr = $ArrWeekDayStr[$SelectTimeWeek] ?? 'N/A'; // Handle potential undefined index


    // Search time range optimization (Keep original)
    $SearchStartHour =   (int)date("H", strtotime("1970-01-01 ".substr("0".$StudyTimeHour,-2).":".substr("0".$StudyTimeMinute,-2).":00") - 60 * 100);//100분 빼기
    $SearchEndHour =     (int)date("H", strtotime("1970-01-01 ".substr("0".$StudyTimeHour,-2).":".substr("0".$StudyTimeMinute,-2).":00") + 60 * 100);//100분 더하기


    $SelectYear = date('Y', strtotime($StudyTimeDate));
    $SelectMonth = date('m', strtotime($StudyTimeDate));
    $SelectDay = date('d', strtotime($StudyTimeDate));


    // 에듀센터 휴무, 설날, 크리스마스 등등 (Keep original SQL - consider prepared statements later)
    $Sql = "
            select
                    A.EduCenterHolidayID
            from EduCenterHolidays A
            where
                A.EduCenterID=$EduCenterID
                and datediff(A.EduCenterHolidayDate, '".$StudyTimeDate."')=0
                and A.EduCenterHolidayState=1";
    $Stmt = $GLOBALS['DbConn']->prepare($Sql);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;
    $EduCenterHolidayID = $Row["EduCenterHolidayID"] ?? null; // Use null coalescing

    $TempEduCenterHoliday = 0;
    if ($EduCenterHolidayID){
        $TempEduCenterHoliday = 1;
    }
    // 에듀센터 휴무, 설날, 크리스마스 등등


    //교육센터 정기휴일 검색 (Keep original SQL)
    $Sql = "
            select
                    A.*
            from EduCenters A
            where A.EduCenterID=$EduCenterID";
    $Stmt = $GLOBALS['DbConn']->prepare($Sql);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;

    if (!$Row) { /* Handle case where center doesn't exist */ return ""; } // Added basic check

    $EduCenterStartHour = $Row["EduCenterStartHour"];
    $EduCenterEndHour = $Row["EduCenterEndHour"];


    $EduCenterHoliday[0] = $Row["EduCenterHoliday0"];
    $EduCenterHoliday[1] = $Row["EduCenterHoliday1"];
    $EduCenterHoliday[2] = $Row["EduCenterHoliday2"];
    $EduCenterHoliday[3] = $Row["EduCenterHoliday3"];
    $EduCenterHoliday[4] = $Row["EduCenterHoliday4"];
    $EduCenterHoliday[5] = $Row["EduCenterHoliday5"];
    $EduCenterHoliday[6] = $Row["EduCenterHoliday6"];

    //$WorkDayCount = 7; // This variable doesn't seem used later

    $WeekDayNum = $SelectTimeWeek;//지정한 날짜만 나온다
    $isRegularCenterHoliday = (isset($EduCenterHoliday[$WeekDayNum]) && $EduCenterHoliday[$WeekDayNum] == 1); // Added for clarity later
    //교육센터 정기휴일 검색


    //교육센터 브레이크 타임 검색 (Keep original)
    $EduCenterBreak = []; // Initialize
    for ($HourNum=0;$HourNum<=23;$HourNum++){ // Correct loop end
        for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){
            if (isset($EduCenterHoliday[$SelectTimeWeek])) { // Check if index exists
                if ($EduCenterHoliday[$SelectTimeWeek]==0) {
                    $EduCenterBreak[$SelectTimeWeek][$HourNum][$MinuteNum] = 1;//[요일/시/분] 1은 수업가능
                } else {
                    $EduCenterBreak[$SelectTimeWeek][$HourNum][$MinuteNum] = 0; // Indicate unavailable due to holiday
                }
            } else {
                // Handle cases where $EduCenterHoliday[$SelectTimeWeek] might not be set (though unlikely with current logic)
                $EduCenterBreak[$SelectTimeWeek][$HourNum][$MinuteNum] = 0; // Default to unavailable if week data missing
            }
        }
    }

    $Sql2 = "select
                    A.*
            from EduCenterBreakTimes A
            where A.EduCenterID=$EduCenterID and A.EduCenterBreakTimeState=1
            order by A.EduCenterBreakTimeWeek asc, A.EduCenterBreakTimeHour asc, A.EduCenterBreakTimeMinute asc";
    $Stmt2 = $GLOBALS['DbConn']->prepare($Sql2);
    $Stmt2->execute();
    $Stmt2->setFetchMode(PDO::FETCH_ASSOC);

    while($Row2 = $Stmt2->fetch()) {
        $EduCenterBreakTimeWeek = $Row2["EduCenterBreakTimeWeek"];
        $EduCenterBreakTimeHour = $Row2["EduCenterBreakTimeHour"];
        $EduCenterBreakTimeMinute = $Row2["EduCenterBreakTimeMinute"];
        $EduCenterBreakTimeType = $Row2["EduCenterBreakTimeType"]; // Usually 2 for break

        if ($EduCenterBreakTimeWeek == $SelectTimeWeek) {
            $breakMinuteRounded = floor($EduCenterBreakTimeMinute / 10) * 10;
            if (isset($EduCenterBreak[$EduCenterBreakTimeWeek][$EduCenterBreakTimeHour][$breakMinuteRounded])) {
                $EduCenterBreak[$EduCenterBreakTimeWeek][$EduCenterBreakTimeHour][$breakMinuteRounded] = $EduCenterBreakTimeType;
            }
        }
    }
    $Stmt2 = null;
    //교육센터 브레이크 타임 검색


    // --- Teacher Query Setup (Keep original) ---
    $AddSqlWhere = "1=1";
    $AddSqlWhere = $AddSqlWhere . " and A.TeacherState=1 ";
    $AddSqlWhere = $AddSqlWhere . " and A.TeacherView=1 ";
    $AddSqlWhere = $AddSqlWhere . " and B.EduCenterID=$EduCenterID "; // Direct variable injection - risk!
    $AddSqlWhere = $AddSqlWhere . " and B.TeacherGroupState=1 ";
    if($LinkAdminLevelID > 4) { // Direct variable injection - risk!
        $AddSqlWhere = $AddSqlWhere . " and ( A.TeacherGroupID=4 or A.TeacherGroupID=9 ) ";
    }

    //출퇴근 시간=== (Keep original SQL - consider prepared statements later)
    $Sql = "select
                    A.*
            from Teachers A
                inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID
            where ".$AddSqlWhere."
            order by B.TeacherGroupOrder asc, A.TeacherOrder asc";
    $Stmt = $GLOBALS['DbConn']->prepare($Sql);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);

    $ArrTeacherStartHour = [];
    $ArrTeacherEndHour = [];
    // $TeacherNum=1; // This counter is not used later
    while($Row = $Stmt->fetch()) {
        $ArrTeacherStartHour[$Row["TeacherID"]] = $Row["TeacherStartHour"];
        $ArrTeacherEndHour[$Row["TeacherID"]] = $Row["TeacherEndHour"];
    }
    $Stmt = null;
    //출퇴근 시간===


    // --- Main Teacher Loop (Keep original SQL) ---
    $Sql = "select
                    A.*
            from Teachers A
                inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID
                -- inner join Members C on A.TeacherID=C.TeacherID -- Removed potentially problematic join
            where ".$AddSqlWhere."
            order by A.TeacherOrder asc";
    $Stmt = $GLOBALS['DbConn']->prepare($Sql);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);


    // ***** DEBUG POINT 2: 강사 루프 시작 전 확인 *****
    if ($debugCheckStudyTime) {
        echo "<div style='background:lightblue; color:black; padding: 2px; border: 1px solid black; font-size: 10px;'>[DEBUG] Starting Teacher Loop for Date=$StudyTimeDate, Time=$StudyTimeHour:$StudyTimeMinute</div>";
        @ob_flush(); @flush();
    }
    // ***** END DEBUG POINT 2 *****


    //$TeacherNum = 1; // Reset counter (used for ??? - seems unused inside loop)
    $TeacherListHTML = "";
    $SlotStatus = []; // Define outside teacher loop if needed across teachers? No, seems per-teacher.

    while($Row = $Stmt->fetch()) { // Start Teacher Loop

        // Get Teacher details
        $TeacherID = $Row["TeacherID"];
        $TeacherName = $Row["TeacherName"];
        $TeacherStartHour = $Row["TeacherStartHour"];
        $TeacherEndHour = $Row["TeacherEndHour"];
        $TeacherBlock80Min = $Row["TeacherBlock80Min"];

        // ***** DEBUG POINT 3: 개별 강사 확인 시작 *****
        // if ($debugCheckStudyTime) {
        //    echo "<div style='background:lightgray; color:black; padding: 1px; border: 1px solid gray; font-size: 9px;'>[DEBUG] Checking Teacher $TeacherID ($TeacherName)...</div>"; @ob_flush(); @flush();
        // }
        // ***** END DEBUG POINT 3 *****


        //강사 브레이크 타임 검색 (Keep original)
        $TeacherBreak = []; // Initialize per teacher for the specific day
        $SlotBreakEvent = []; // Initialize per teacher
        //$WeekDayNum = $SelectTimeWeek;//지정한 날짜만 나온다 - Redundant, already defined

        // Initialize TeacherBreak for the relevant day
        for ($HourNum=0;$HourNum<=23;$HourNum++){ // Loop up to 23
            for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){
                if (!$isRegularCenterHoliday) {
                    $TeacherBreak[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 1;//[요일/시/분] 1은 수업가능
                    $SlotBreakEvent[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 0;// Initialize event flag
                } else {
                    $TeacherBreak[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 1; // Initialize even if holiday, check later
                    $SlotBreakEvent[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] = 0;
                }
            }
        }

        // Load Teacher Breaks (Keep original SQL)
        $Sql2 = "select
                        A.*
                from TeacherBreakTimes A
                where A.TeacherID=$TeacherID and A.TeacherBreakTimeState=1
                and A.TeacherBreakTimeWeek = $WeekDayNum -- Only load for the relevant day
                order by A.TeacherBreakTimeHour asc, A.TeacherBreakTimeMinute asc"; // Removed week from order by
        $Stmt2 = $GLOBALS['DbConn']->prepare($Sql2);
        $Stmt2->execute();
        $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
        while($Row2 = $Stmt2->fetch()) {
            $TeacherBreakTimeWeek = $Row2["TeacherBreakTimeWeek"];
            $TeacherBreakTimeHour = $Row2["TeacherBreakTimeHour"];
            $TeacherBreakTimeMinute = $Row2["TeacherBreakTimeMinute"];
            $TeacherBreakTimeType = $Row2["TeacherBreakTimeType"];

            $breakMinuteRounded = floor($TeacherBreakTimeMinute / 10) * 10;
            if (isset($TeacherBreak[$TeacherID][$TeacherBreakTimeWeek][$TeacherBreakTimeHour][$breakMinuteRounded])) {
                $TeacherBreak[$TeacherID][$TeacherBreakTimeWeek][$TeacherBreakTimeHour][$breakMinuteRounded] = $TeacherBreakTimeType;
            }
        }
        $Stmt2 = null;
        //강사 브레이크 타임 검색


        // 강사 자체 휴무 (Keep original SQL)
        $TeacherHolidayID = null; // Reset for current teacher
        $Sql5 = "
                select
                        A.TeacherHolidayID
                from TeacherHolidays A
                where
                    A.TeacherID=$TeacherID
                    and datediff(A.TeacherHolidayDate, '".$StudyTimeDate."')=0
                    and A.TeacherHolidayState=1";
        $Stmt5 = $GLOBALS['DbConn']->prepare($Sql5);
        $Stmt5->execute();
        $Stmt5->setFetchMode(PDO::FETCH_ASSOC);
        $Row5 = $Stmt5->fetch();
        $Stmt5 = null;
        $TeacherHolidayID = $Row5["TeacherHolidayID"] ?? null;

        $isTeacherOrCenterHolidayToday = $TempEduCenterHoliday || $TeacherHolidayID;
        // 강사 자체 휴무


        // --- Main Availability Check ---
        if ($isRegularCenterHoliday || $isTeacherOrCenterHolidayToday) {
            // ***** DEBUG POINT 4: 휴일로 인한 강사 건너뛰기 확인 *****
            if ($debugCheckStudyTime && $StudyTimeDate == '2025-04-17') { // 문제의 날짜 조건 추가
                echo "<div style='background:orange; color:black; padding: 1px; border: 1px solid gray; font-size: 9px;'>[DEBUG] Skipping Teacher $TeacherID ($TeacherName) due to holiday. CenterReg: $isRegularCenterHoliday, CenterSpecific: $TempEduCenterHoliday, TeacherSpecific: ".($TeacherHolidayID ? 'YES':'NO')."</div>"; @ob_flush(); @flush();
            }
            // ***** END DEBUG POINT 4 *****
            continue; // Skip this teacher
        }

        // Initialize SlotStatus array for this teacher/day
        $SlotStatus = []; // Initialize SlotStatus for this teacher/day
        $SelectedHourNum = $StudyTimeHour;
        $SelectedMinuteNum = $StudyTimeMinute;
        $CheckMinuteListNum = 0;
        $MinuteListNum = 1;

        // --- Build SlotStatus array (Keep original logic inside loops) ---
        for ($HourNum = $TeacherStartHour; $HourNum < $TeacherEndHour; $HourNum++){
            for ($MinuteNum = 0; $MinuteNum <= 50; $MinuteNum=$MinuteNum+10){

                $SlotBreakEventCode = 0;
                $isBlocked = false;

                // Check Center Break
                if (isset($EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum]) && $EduCenterBreak[$WeekDayNum][$HourNum][$MinuteNum] != 1){ $isBlocked = true; $SlotBreakEventCode = 51; }
                // Check Teacher Regular Break
                if (!$isBlocked && isset($TeacherBreak[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum]) && $TeacherBreak[$TeacherID][$WeekDayNum][$HourNum][$MinuteNum] != 1){ $isBlocked = true; $SlotBreakEventCode = 61; }
                // Check Teacher Temp Break (Keep original SQL)
                if (!$isBlocked) {
                    $TargetDate = $StudyTimeDate; $TempStartHourNum = $HourNum; $TempStartMinuteNum = $MinuteNum; $TempEndHourNum = $HourNum; $TempEndMinuteNum = $MinuteNum+10; if ($TempEndMinuteNum==60){ $TempEndHourNum = $TempEndHourNum + 1; $TempEndMinuteNum = 0; }
                    $Sql3 = "select TeacherBreakTimeTempID, TeacherBreakTimeTempType from TeacherBreakTimeTemps A where A.TeacherBreakTimeTempWeek=$WeekDayNum and A.TeacherID=".$TeacherID." and A.TeacherBreakTimeTempState=1 and datediff(A.TeacherBreakTimeTempStartDate, '".$TargetDate."')<=0 and datediff(A.TeacherBreakTimeTempEndDate, '".$TargetDate."')>=0 and time_to_sec(timediff(A.TeacherBreakTimeTempStartTime, '".$TempStartHourNum.":".$TempStartMinuteNum."'))<=0 and time_to_sec(timediff(A.TeacherBreakTimeTempEndTime, '".$TempEndHourNum.":".$TempEndMinuteNum."'))>=0";
                    $Stmt3 = $GLOBALS['DbConn']->prepare($Sql3); $Stmt3->execute(); $Row3 = $Stmt3->fetch(PDO::FETCH_ASSOC); $Stmt3 = null;
                    if ($Row3){ $isBlocked = true; $tempBreakType = $Row3["TeacherBreakTimeTempType"]; if ($tempBreakType==2) { $SlotBreakEventCode = 61; } else if ($tempBreakType==3) { $SlotBreakEventCode = 71; } else if ($tempBreakType==4) { $SlotBreakEventCode = 81; } else { $SlotBreakEventCode = 71; } }
                }
                // Check Existing REGULAR Slots (Type 1) (Keep original SQL)
                if (!$isBlocked) {
                    $TargetDate = $StudyTimeDate; $TempYear = date('Y', strtotime($TargetDate)); $TempMonth = date('m', strtotime($TargetDate)); $TempDay = date('d', strtotime($TargetDate));
                    if ($ClassProductID==1){ $SqlWhere3 = "  "; $SqlWhere3_1 = "  "; } else{ $SqlWhere3 = " and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0  "; $SqlWhere3 .= " and ( CLS.ClassAttendState is NULL or CLS.ClassAttendState<4 ) "; $SqlWhere3_1 = " and datediff(CO.ClassOrderStartDate, '".$TargetDate."')<=0  "; }
                    $Sql3 = "select count(*) as ClassOrderSlotCount from ClassOrderSlots COS inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$TempYear." and CLS.StartMonth=".$TempMonth." and CLS.StartDay=".$TempDay." and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID and CLS.ClassAttendState<>99 where COS.StudyTimeWeek=".$WeekDayNum." and COS.StudyTimeHour=".$HourNum." and COS.StudyTimeMinute=".$MinuteNum." and COS.TeacherID=".$TeacherID." and COS.ClassOrderSlotType=1 and ( COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL or datediff(COS.ClassOrderSlotStartDate, '".$TargetDate."')<=0 and COS.ClassOrderSlotEndDate is NULL or COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$TargetDate."')>=0 or datediff(COS.ClassOrderSlotStartDate, '".$TargetDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$TargetDate."')>=0 ) ".$SqlWhere3." and COS.ClassOrderSlotState=1 and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$TargetDate."')>=0) )";
                    $Stmt3 = $GLOBALS['DbConn']->prepare($Sql3); $Stmt3->execute(); $Row3 = $Stmt3->fetch(PDO::FETCH_ASSOC); $ClassOrderSlotCount = $Row3["ClassOrderSlotCount"];
                    if ($ClassOrderSlotCount>0) { $isBlocked = true; $SlotBreakEventCode = 11; }
                }
                // Check Existing TEMP Slots (Type 2) (Keep original SQL)
                if (!$isBlocked) {
                    $TargetDate = $StudyTimeDate; $TempYear = date('Y', strtotime($TargetDate)); $TempMonth = date('m', strtotime($TargetDate)); $TempDay = date('d', strtotime($TargetDate));
                    if ($ClassProductID==1){ $SqlWhere3 = " and datediff(COS.ClassOrderSlotDate,'".$TargetDate."')>=0 "; $SqlFrom3 = " and datediff(CLS.StartDateTime,'".$TargetDate."')>=0 "; } else{ $SqlWhere3 = " and datediff(COS.ClassOrderSlotDate,'".$TargetDate."')=0 "; $SqlFrom3 = " and CLS.StartYear=".$TempYear." and CLS.StartMonth=".$TempMonth." and CLS.StartDay=".$TempDay." "; }
                    $Sql3 = "select count(*) as ClassOrderSlotCount from ClassOrderSlots COS inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID inner join ClassOrderSlots COS_2 on COS.ClassOrderSlotGroupID=COS_2.ClassOrderSlotGroupID and COS_2.ClassOrderSlotMaster=1 inner join Members MB on CO.MemberID=MB.MemberID left outer join Classes CLS on COS_2.ClassOrderID=CLS.ClassOrderID ".$SqlFrom3." and CLS.StartHour=COS_2.StudyTimeHour and CLS.StartMinute=COS_2.StudyTimeMinute and CLS.TeacherID=COS_2.TeacherID where COS.StudyTimeWeek=".$WeekDayNum." and COS.StudyTimeHour=".$HourNum." and COS.StudyTimeMinute=".$MinuteNum." and COS.TeacherID=".$TeacherID." ".$SqlWhere3." and COS.ClassOrderSlotType=2 and ( CLS.ClassAttendState is NULL or CLS.ClassAttendState<4 ) and COS.ClassOrderSlotState=1 and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4) ";
                    $Stmt3 = $GLOBALS['DbConn']->prepare($Sql3); $Stmt3->execute(); $Row3 = $Stmt3->fetch(PDO::FETCH_ASSOC); $Stmt3 = null; $ClassOrderSlotCount = $Row3["ClassOrderSlotCount"];
                    if ($ClassOrderSlotCount>0){ $isBlocked = true; $SlotBreakEventCode = 12; }
                }

                // Assign SlotStatus
                $SlotStatus[$MinuteListNum] = $isBlocked ? $SlotBreakEventCode : 100;
                if ($SelectedHourNum==$HourNum && $SelectedMinuteNum==$MinuteNum){ $CheckMinuteListNum = $MinuteListNum; }
                $MinuteListNum++;

            } // End Minute Loop
        } // End Hour Loop
        // --- End Build SlotStatus array ---


        // --- Verification Step (Keep original Logic) ---
        $DenySelect = 0;
        if ($CheckMinuteListNum > 0) {
            for ($iiii=0; $iiii < $ClassOrderTimeSlotCount; $iiii++){
                $slotIndexToCheck = $CheckMinuteListNum + $iiii;
                if (!isset($SlotStatus[$slotIndexToCheck]) || $SlotStatus[$slotIndexToCheck] != 100){ $DenySelect = 1; break; }
            }
        } else { $DenySelect = 1; }

        if ($DenySelect == 0 && $TeacherBlock80Min == 1){
            // Original 80-Min Check Logic (Kept as is)
            $EmptyNum = 100;
            for ($iiii=-1;$iiii>=($ClassOrderTimeSlotCount-9) ;$iiii--){
                if ($EmptyNum==100){
                    $ii = $CheckMinuteListNum + $iiii;
                    if ($ii>=0){ if (isset($SlotStatus[$ii]) && $SlotStatus[$ii]!=11) { $EmptyNum = $ii+1; } elseif (!isset($SlotStatus[$ii])) { $EmptyNum = $ii+1; } }
                    else { $EmptyNum = 1; }
                }
                if ($EmptyNum != 100) break;
            }
            if ($EmptyNum == 100) $EmptyNum = 1;

            $ActiveSlotCount = 0;
            for ($iiii=$EmptyNum; $iiii<=(8+$EmptyNum); $iiii++) {
                if ( ($iiii-$CheckMinuteListNum)>=0 && ($iiii-$CheckMinuteListNum)<=($ClassOrderTimeSlotCount-1) ){ $ActiveSlotCount++; }
                else{ if (isset($SlotStatus[$iiii]) && $SlotStatus[$iiii]==11){ $ActiveSlotCount++; } }
            }
            if ($ActiveSlotCount>8){ $DenySelect = 2; }
        }
        // --- End Verification Step ---


        // ***** START DEBUGGING CODE (Final Check - Conditional) *****
        if ($debugCheckStudyTime) {
            // 조건 단순화: 특정 날짜와 Product ID만 확인 (시간 무관) -> 원래 조건으로 되돌리거나 필요에 맞게 수정 가능
            $isDebugTarget = ($StudyTimeDate == '2025-04-17' && (int)$ClassProductID == 3);

            if ($isDebugTarget) {
                echo "<pre class='debug-output' style='background: #fdd; border: 1px solid red; padding: 5px; margin: 5px; text-align: left; font-size: 10px; clear:both;'>";
                echo "DEBUG CHECK RESULT: TeacherID: $TeacherID (" . htmlspecialchars($TeacherName) . ") for Date: $StudyTimeDate, ReqTime: $StudyTimeHour:$StudyTimeMinute (Product: $ClassProductID)\n";
                echo "Work Hours: $TeacherStartHour - $TeacherEndHour | Req Slot Index: $CheckMinuteListNum | Slots Needed: $ClassOrderTimeSlotCount\n";
                echo "==> DenySelect Code: $DenySelect (0=OK, 1=Conflict/OutsideHour, 2=80min)\n";
                echo "Slot Status around check (Idx => Code [100=Free, 11/12=Class, 51=CBreak, 61=TBreak, 71/81=TTemp/Block]):\n";
                if ($CheckMinuteListNum > 0) {
                    $startDebugIdx = max(1, $CheckMinuteListNum - 4);
                    $endDebugIdx = $CheckMinuteListNum + $ClassOrderTimeSlotCount + 4;
                    for ($debug_idx = $startDebugIdx; $debug_idx < $endDebugIdx; $debug_idx++) {
                        if (isset($SlotStatus[$debug_idx])) {
                            $marker = ($debug_idx >= $CheckMinuteListNum && $debug_idx < $CheckMinuteListNum + $ClassOrderTimeSlotCount) ? '*' : ' ';
                            echo sprintf("  [%3d]%s: %-3s ", $debug_idx, $marker, $SlotStatus[$debug_idx]);
                            if (($debug_idx - $startDebugIdx + 1) % 5 == 0) echo "\n"; // Newline every 5 entries
                        }
                    }
                    if (($endDebugIdx - $startDebugIdx) % 5 != 0) echo "\n"; // Final newline if needed
                } else {
                    echo "  Req start time ($SelectedHourNum:$SelectedMinuteNum) not found in schedule (CheckMinuteListNum=0).\n";
                }
                echo "--- End Debug for Teacher $TeacherID ---";
                echo "</pre>";
                @ob_flush(); @flush(); // Try to force output
            }
        }
        // ***** END DEBUGGING CODE (Final Check - Conditional) *****


        // Add teacher to HTML options if DenySelect is 0
        if ($DenySelect==0){//해당요일이 조건에 맞음
            $TeacherListHTML .= "<option value=\"".$TeacherID."\">".htmlspecialchars($TeacherName)."</option>"; // Use htmlspecialchars
        }

    } // End Teacher Loop
    $Stmt = null;

    // ***** DEBUG POINT 5: 함수 종료 확인 *****
    if ($debugCheckStudyTime) {
        echo "<div style='background:lightgreen; color:black; padding: 2px; border: 1px solid black; font-size: 10px;'>[DEBUG] CheckStudyTime End: Date=$StudyTimeDate, Time=$StudyTimeHour:$StudyTimeMinute. Returning options: " . (!empty($TeacherListHTML) ? 'Yes' : 'No') . "</div>";
        @ob_flush(); @flush();
    }
    // ***** END DEBUG POINT 5 *****


    return $TeacherListHTML;

} // End function CheckStudyTime
?>