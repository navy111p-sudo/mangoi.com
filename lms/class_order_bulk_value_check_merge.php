<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

// --- HELPER FUNCTION ---
/**
 * Calculates the start date for the next session based on the previous session's date and weekly schedule.
 *
 * @param string $prevSessionDate 'Y-m-d' format date of the previous session.
 * @param array $weekdayTimes An array mapping weekday number (1=Mon, 5=Fri) to time string ('H:i') or null.
 * @param int $currentSequence The sequence number we are calculating the date for (e.g., 2 for Trial, 3 for Regular).
 * @param string $initialLevelStartDate 'Y-m-d' format date of the very first session (Level Test). Used for sequence 2 calculation.
 * @return string|false Returns the calculated date string 'Y-m-d' or false on failure.
 */
if (!function_exists('calculateNextSessionDate')) {
    function calculateNextSessionDate($prevSessionDate, $weekdayTimes, $currentSequence, $initialLevelStartDate = null) {
        // Input validation
        if (empty($weekdayTimes) || !strtotime($prevSessionDate) || ($currentSequence == 2 && !strtotime($initialLevelStartDate))) {
            return false; // Invalid input
        }

        // Base date depends on the sequence we're calculating
        $baseDateStr = ($currentSequence == 2) ? $initialLevelStartDate : $prevSessionDate;
        if (!$baseDateStr) return false;

        $baseDate = new DateTime($baseDateStr);
        $baseDayOfWeek = (int)$baseDate->format('N'); // 1 (for Monday) through 7 (for Sunday)

        // Create a sorted list of scheduled weekdays
        $scheduledDays = [];
        for ($day = 1; $day <= 5; $day++) { // Mon to Fri
            if (!empty($weekdayTimes[$day])) {
                $scheduledDays[] = $day;
            }
        }
        if (empty($scheduledDays)) {
            return false; // No scheduled days
        }
        sort($scheduledDays); // Ensure it's sorted [1, 3, 5] etc.

        // Find the index of the baseDayOfWeek in the scheduled days
        $baseIndex = -1;
        foreach ($scheduledDays as $index => $day) {
            // If the base date itself is a scheduled day, use it as the starting point
            if ($day == $baseDayOfWeek) {
                $baseIndex = $index;
                break;
            }
            // If the base date is *not* a scheduled day, find the *next* scheduled day *after* it
            if ($day > $baseDayOfWeek && $baseIndex == -1) {
                // We need the day *before* this one in the schedule to calculate offset correctly
                // But for finding the *next* occurrence, we actually start checking from the base date itself.
                // Let's simplify: find the next scheduled day *on or after* the base date.
                // This requires adjustment below.

                // Simpler approach: Start searching from the day *after* the base date.
                // Let's stick to finding the *next* scheduled day in the sequence.
            }
        }

        // Determine the target day index in the schedule
        $targetIndex = -1;
        if ($baseIndex !== -1) {
            // Base date was a scheduled day, find the next one in the list (wrapping around)
            $targetIndex = ($baseIndex + 1) % count($scheduledDays);
        } else {
            // Base date was NOT a scheduled day. Find the first scheduled day *after* baseDayOfWeek.
            $found = false;
            for($i=0; $i < count($scheduledDays); $i++) {
                if ($scheduledDays[$i] > $baseDayOfWeek) {
                    $targetIndex = $i;
                    $found = true;
                    break;
                }
            }
            // If no scheduled day found after baseDayOfWeek in the same week, take the first scheduled day of the *next* week.
            if (!$found) {
                $targetIndex = 0; // First element in the sorted list
            }
        }

        $targetDayOfWeek = $scheduledDays[$targetIndex];

        // Calculate days difference
        $daysToAdd = 0;
        if ($targetDayOfWeek > $baseDayOfWeek) {
            // Target day is later in the same week
            $daysToAdd = $targetDayOfWeek - $baseDayOfWeek;
        } else {
            // Target day is earlier in the week (meaning next week) or the same day (meaning next week)
            // Or, base date was not a scheduled day and target is the first one next week
            $daysToAdd = (7 - $baseDayOfWeek) + $targetDayOfWeek;
        }

        // Add the calculated days to the base date
        $nextDate = clone $baseDate;
        $nextDate->modify("+$daysToAdd days");

        // Basic check: if the calculated date is still a weekend, something is wrong (shouldn't happen with Mon-Fri schedule)
        $finalDayOfWeek = (int)$nextDate->format('N');
        if ($finalDayOfWeek >= 6) {
            // This indicates an issue in logic, maybe try adding another day or review calculation
            // For now, return false or log an error
            error_log("Calculated next session date is a weekend: " . $nextDate->format('Y-m-d'));
            // Attempt basic correction: move to next Monday if weekend
            if ($finalDayOfWeek == 6) $nextDate->modify('+2 days'); // Sat -> Mon
            if ($finalDayOfWeek == 7) $nextDate->modify('+1 day');  // Sun -> Mon
        }


        return $nextDate->format('Y-m-d');
    }
}
// --- END HELPER FUNCTION ---


try {
    // DB 트랜잭션 시작 (Only for slot deletion part in this script)
    $DbConn->beginTransaction();

    // 1. 불필요한 슬롯 삭제 (기존 로직 유지)
    // echo "1. 불필요한 슬롯 삭제 처리 중...<br>"; // User doesn't need to see this intermediate step usually
    // flush(); ob_flush();

    $Sql = "SELECT DISTINCT ClassOrderSlotID FROM View_ClassOrderSlotDelTargets
            WHERE ClassOrderSlotID NOT IN (
                SELECT ClassOrderSlotID FROM View_ClassOrderSlotDelTargets WHERE ClassOrderSlotWeek=StudyWeek
            )";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    while ($Row = $Stmt->fetch()) {
        $DelClassOrderSlotID = $Row["ClassOrderSlotID"];
        $Sql2 = "UPDATE ClassOrderSlots SET
                    ClassOrderSlotState=0,
                    DelAdminUnder7Day=1,
                    DelAdminUnder7DayDateTime=NOW(),
                    ClassOrderSlotDateModiDateTime=NOW()
                 WHERE ClassOrderSlotID=:delSlotId"; // Use binding
        $Stmt2 = $DbConn->prepare($Sql2);
        $Stmt2->bindParam(':delSlotId', $DelClassOrderSlotID, PDO::PARAM_INT);
        $Stmt2->execute();
        $Stmt2 = null;
    }
    $Stmt = null;

    // Commit the transaction for the slot deletion part
    $DbConn->commit();
    // Start a new transaction if needed for subsequent DB operations in this script,
    // but currently, there are none before the next page load.

    // 2. 업로드 파일 처리
    // echo "2. 파일 업로드 처리 중...<br>"; // User feedback handled later
    // flush(); ob_flush();

    $ErrNum = 0;
    $ErrMsg = "";
    $UpPath = "../uploads/excel_add_student/"; // Ensure this directory exists and is writable

    if (!isset($_FILES['UpFile']) || $_FILES['UpFile']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("파일 업로드 오류 발생. Error Code: " . ($_FILES['UpFile']['error'] ?? 'N/A'));
    }

    $TempFile = $_FILES['UpFile']['tmp_name'];
    $MyFile = $_FILES['UpFile']['name'];
    $MyFileSize = $_FILES['UpFile']['size'];
    $MyFileMimeType = $_FILES['UpFile']['type'];

    // Basic security check for file extension
    $pathinfo = pathinfo($MyFile);
    $FileType = strtolower($pathinfo['extension'] ?? '');
    if (!in_array($FileType, ['xls', 'xlsx'])) {
        throw new Exception("엑셀 파일(xls, xlsx)만 업로드 가능합니다.");
    }

    // Generate a unique filename to prevent collisions and handle encoding
    $MyFileName = 'upload_' . md5(time() . rand()) . '.' . $FileType;
    $UploadFileName = $UpPath . $MyFileName; // Full path for the final uploaded file

    if (!move_uploaded_file($TempFile, $UploadFileName)) {
        throw new Exception("파일 이동에 실패하였습니다. 서버 권한을 확인하세요.");
    }

    // File is now uploaded as $UploadFileName

    // 3. 엑셀 파일 파싱 및 데이터 처리 시작
    // echo "3. 엑셀 파일 파싱 중...<br>";
    // flush(); ob_flush();

    $mode = isset($_REQUEST["mode"]) ? $_REQUEST["mode"] : ""; // Expecting 'merge'
    $processed_data = []; // Array to hold data for the next step
    $excel_feedback_rows = []; // Array to hold data for HTML feedback table

    $ArrWeekName = explode("|", "일요일|월요일|화요일|수요일|목요일|금요일|토요일"); // Make sure this is defined

    include_once("../PHPExcel-1.8/Classes/PHPExcel.php"); // Adjust path if needed
    libxml_use_internal_errors(true);
    $objPHPExcel = new PHPExcel();
    $filename = $UploadFileName;

    try {
        $objReader = PHPExcel_IOFactory::createReaderForFile($filename);
        $objReader->setReadDataOnly(true); // Faster reading
        $objExcel = $objReader->load($filename);
        $objExcel->setActiveSheetIndex(0);
        $objWorksheet = $objExcel->getActiveSheet();
        $maxRow = $objWorksheet->getHighestRow();

        $TotalExcelListNum = 0;
        $AbleExcelListNum = 0;

        // 11행부터 데이터라고 가정
        for ($i = 11; $i <= $maxRow; $i++) {
            // Reset variables for each row
            $DataOk = []; // Reset DataOk array
            $StrMemberLoginID = ''; $StrClassType = ''; $StrClassOrderLeveltestApplyLevel = ''; $StrClassOrderTimeTypeID = '';
            $StrClassStartDate = ''; $StrStartTimeLevel = ''; $StrStartTimeWeek1 = ''; $StrStartTimeWeek2 = '';
            $StrStartTimeWeek3 = ''; $StrStartTimeWeek4 = ''; $StrStartTimeWeek5 = '';
            $InputMemberID = ''; $InputClassMemberType = 0; $InputClassOrderTimeTypeID = 0; $InputClassOrderWeekCountID = 0;
            $TempClassType = ''; $TempClassOrderLeveltestApplyLevel = ''; $TempClassOrderTimeTypeID = ''; $TempClassStartDate = '';
            $TempStartTimeLevel = ''; $TempStartTimeWeek1 = ''; $TempStartTimeWeek2 = ''; $TempStartTimeWeek3 = ''; $TempStartTimeWeek4 = ''; $TempStartTimeWeek5 = '';

            // Include the validation logic (which now handles '통합' correctly)
            include("./class_order_bulk_excel_check_inc.php");

            $TotalExcelListNum++;
            $AllDataOk = (
                ($DataOk[1] ?? 0) == 1 && ($DataOk[2] ?? 0) == 1 && ($DataOk[3] ?? 0) == 1 &&
                ($DataOk[4] ?? 0) == 1 && ($DataOk[5] ?? 0) == 1 && ($DataOk[6] ?? 0) == 1 &&
                ($DataOk[7] ?? 0) == 1 && ($DataOk[8] ?? 0) == 1 && ($DataOk[9] ?? 0) == 1 &&
                ($DataOk[10] ?? 0) == 1
            );

            // Store data for HTML feedback table regardless of validity
            $excel_feedback_rows[] = [
                'rowNum' => $i,
                'valid' => $AllDataOk,
                'StrMemberLoginID' => $StrMemberLoginID,
                'StrClassType' => $StrClassType,
                'TempClassType' => $TempClassType, // Keep original type for feedback logic
                'StrClassOrderLeveltestApplyLevel' => ($TempClassType == '레벨' || $TempClassType == '통합') ? "LEVEL " . $TempClassOrderLeveltestApplyLevel : '',
                'StrClassOrderTimeTypeID' => $StrClassOrderTimeTypeID,
                'StrClassStartDate' => $StrClassStartDate,
                'StrStartTimeLevel' => $StrStartTimeLevel,
                'StrStartTimeWeek1' => $StrStartTimeWeek1,
                'StrStartTimeWeek2' => $StrStartTimeWeek2,
                'StrStartTimeWeek3' => $StrStartTimeWeek3,
                'StrStartTimeWeek4' => $StrStartTimeWeek4,
                'StrStartTimeWeek5' => $StrStartTimeWeek5
            ];

            if ($AllDataOk) {
                $AbleExcelListNum++;

                $weekdayTimes = [
                    1 => $TempStartTimeWeek1 ?: null,
                    2 => $TempStartTimeWeek2 ?: null,
                    3 => $TempStartTimeWeek3 ?: null,
                    4 => $TempStartTimeWeek4 ?: null,
                    5 => $TempStartTimeWeek5 ?: null,
                ];

                //─────────────────────────────────────────────
                //  ▶ 통합 / 레벨체험 / 일반 분기 ◀
                //─────────────────────────────────────────────
                if (trim($TempClassType) == '통합') {
                    // --- Integrated Type: Generate 3 data sets ---

                    // 1. Calculate Dates
                    // Need valid base dates and times from validation ($TempClassStartDate, $TempStartTimeLevel, $weekdayTimes)
                    $levelStartDate = $TempClassStartDate;
                    $trialStartDate = calculateNextSessionDate($levelStartDate, $weekdayTimes, 2, $levelStartDate);
                    $regularStartDate = $trialStartDate ? calculateNextSessionDate($trialStartDate, $weekdayTimes, 3) : false;

                    if (!$trialStartDate || !$regularStartDate) {
                        // Mark the original feedback row as invalid if date calculation fails
                        $excel_feedback_rows[count($excel_feedback_rows)-1]['valid'] = false;
                        $excel_feedback_rows[count($excel_feedback_rows)-1]['StrClassType'] .= ' (날짜 계산 오류)';
                        $AbleExcelListNum--; // Decrement counter
                        error_log("Date calculation failed for Excel row: $i");
                        continue; // Skip adding data for this row
                    }

                    // 2. Prepare Level Data
                    $levelData = [
                        'origin_row' => $i,
                        'MemberID' => $InputMemberID, // From check_inc
                        'ClassProductID' => 2, // 레벨
                        'ClassMemberType' => $InputClassMemberType, // From check_inc
                        'ClassOrderTimeTypeID' => 2, // 20분 고정
                        'ClassOrderWeekCountID' => 1, // 레벨/체험은 1회
                        'ClassOrderStartDate' => $levelStartDate,
                        'ClassOrderLeveltestApplyLevel' => $TempClassOrderLeveltestApplyLevel, // From check_inc
                        'StartTimeLevel' => $TempStartTimeLevel, // From check_inc
                        'StartTimeWeek1' => null, 'StartTimeWeek2' => null, 'StartTimeWeek3' => null, 'StartTimeWeek4' => null, 'StartTimeWeek5' => null,
                        'DisplayClassType' => '레벨 (통합)', // For display in next step
                        'DisplayLevel' => "LEVEL " . $TempClassOrderLeveltestApplyLevel
                    ];
                    $processed_data[] = $levelData;

                    // 3. Prepare Trial Data
                    $trialData = [
                        'origin_row' => $i,
                        'MemberID' => $InputMemberID,
                        'ClassProductID' => 3, // 체험
                        'ClassMemberType' => $InputClassMemberType,
                        'ClassOrderTimeTypeID' => 2, // 20분 고정
                        'ClassOrderWeekCountID' => 1, // 레벨/체험은 1회
                        'ClassOrderStartDate' => $trialStartDate, // Calculated date
                        'ClassOrderLeveltestApplyLevel' => 1, // 체험은 레벨 1 기본값
                        'StartTimeLevel' => $TempStartTimeLevel, // 체험도 레벨 시간 사용
                        'StartTimeWeek1' => null, 'StartTimeWeek2' => null, 'StartTimeWeek3' => null, 'StartTimeWeek4' => null, 'StartTimeWeek5' => null,
                        'DisplayClassType' => '체험 (통합)',
                        'DisplayLevel' => ''
                    ];
                    $processed_data[] = $trialData;

                    // 4. Prepare Regular Data
                    $regularData = [
                        'origin_row' => $i,
                        'MemberID' => $InputMemberID,
                        'ClassProductID' => 1, // 정규
                        'ClassMemberType' => $InputClassMemberType,
                        'ClassOrderTimeTypeID' => $InputClassOrderTimeTypeID, // 원본 엑셀값 (20 or 40)
                        'ClassOrderWeekCountID' => $InputClassOrderWeekCountID, // From check_inc
                        'ClassOrderStartDate' => $regularStartDate, // Calculated date
                        'ClassOrderLeveltestApplyLevel' => 1, // 정규는 레벨 1 기본값
                        'StartTimeLevel' => null, // 정규는 레벨 시간 사용 안함
                        'StartTimeWeek1' => $TempStartTimeWeek1, 'StartTimeWeek2' => $TempStartTimeWeek2, 'StartTimeWeek3' => $TempStartTimeWeek3, 'StartTimeWeek4' => $TempStartTimeWeek4, 'StartTimeWeek5' => $TempStartTimeWeek5,
                        'DisplayClassType' => '정규 (통합)',
                        'DisplayLevel' => ''
                    ];
                    $processed_data[] = $regularData;

                }
                // ▼▼▼ [레벨체험] 새 분기 ▼▼▼
                else if (trim($TempClassType) == '레벨체험') {           // [레벨체험]

                    /* 규칙 :
                       1회차  ▶ 레벨  (상품ID 2)
                       2회차  ▶ 체험  (상품ID 3)
                       정규는 생성하지 않음
                    */

                    // -- 날짜 계산 --
                    $levelStartDate  = $TempClassStartDate;                                  // 레벨
                    /* ───── 레벨→체험 날짜 계산 ───── */
                    $weekdayEmpty = true;                       // 요일시간 하나라도 있나?
                    foreach ($weekdayTimes as $t) {
                        if ($t) { $weekdayEmpty = false; break; }
                    }

                    if ($weekdayEmpty) {                       // ▶ 요일 시간이 전혀 없으면 단순 7일 뒤
                        $trialStartDate = date('Y-m-d', strtotime($levelStartDate.' +7 days'));
                    } else {                                   // ▶ 요일 시간이 있으면 기존 계산 함수 사용
                        $trialStartDate = calculateNextSessionDate(
                            $levelStartDate, $weekdayTimes, 2, $levelStartDate);
                    }

                    if (!$trialStartDate){
                        // 날짜 계산 실패 → 피드백 row invalid 처리
                        $excel_feedback_rows[count($excel_feedback_rows)-1]['valid'] = false;
                        $excel_feedback_rows[count($excel_feedback_rows)-1]['StrClassType'] .= ' (날짜 계산 오류)';
                        $AbleExcelListNum--;
                        continue;
                    }

                    // -- 레벨 데이터 --
                    $processed_data[] = [
                        'origin_row'                    => $i,
                        'MemberID'                      => $InputMemberID,
                        'ClassProductID'                => 2,           // 레벨
                        'ClassMemberType'               => $InputClassMemberType,
                        'ClassOrderTimeTypeID'          => 2,           // 20분 고정
                        'ClassOrderWeekCountID'         => 1,
                        'ClassOrderStartDate'           => $levelStartDate,
                        'ClassOrderLeveltestApplyLevel' => $TempClassOrderLeveltestApplyLevel,
                        'StartTimeLevel'                => $TempStartTimeLevel,
                        'StartTimeWeek1' => null,'StartTimeWeek2' => null,
                        'StartTimeWeek3' => null,'StartTimeWeek4' => null,'StartTimeWeek5' => null,
                        'DisplayClassType'              => '레벨 (레벨체험)',
                        'DisplayLevel'                  => "LEVEL ".$TempClassOrderLeveltestApplyLevel
                    ];

                    // -- 체험 데이터 --
                    $processed_data[] = [
                        'origin_row'                    => $i,
                        'MemberID'                      => $InputMemberID,
                        'ClassProductID'                => 3,           // 체험
                        'ClassMemberType'               => $InputClassMemberType,
                        'ClassOrderTimeTypeID'          => 2,           // 20분
                        'ClassOrderWeekCountID'         => 1,
                        'ClassOrderStartDate'           => $trialStartDate,
                        'ClassOrderLeveltestApplyLevel' => 1,
                        'StartTimeLevel'                => $TempStartTimeLevel,
                        'StartTimeWeek1' => null,'StartTimeWeek2' => null,
                        'StartTimeWeek3' => null,'StartTimeWeek4' => null,'StartTimeWeek5' => null,
                        'DisplayClassType'              => '체험 (레벨체험)',
                        'DisplayLevel'                  => ''
                    ];
                }
                // ▲▲▲ [레벨체험] 끝 ▲▲▲
                else {
                    // --- Normal Type: Generate 1 data set ---
                    $singleData = [
                        'origin_row' => $i,
                        'MemberID' => $InputMemberID,
                        'ClassProductID' => ($TempClassType == '정규' ? 1 : ($TempClassType == '레벨' ? 2 : 3)),
                        'ClassMemberType' => $InputClassMemberType,
                        'ClassOrderTimeTypeID' => $InputClassOrderTimeTypeID, // Note: check_inc already forced 20min for 레벨/체험
                        'ClassOrderWeekCountID' => $InputClassOrderWeekCountID, // Note: check_inc forced 1 for 레벨/체험 if 정규 times were blank
                        'ClassOrderStartDate' => $TempClassStartDate,
                        'ClassOrderLeveltestApplyLevel' => $TempClassOrderLeveltestApplyLevel,
                        'StartTimeLevel' => $TempStartTimeLevel, // check_inc cleared this if 정규
                        'StartTimeWeek1' => $TempStartTimeWeek1, // check_inc cleared these if 레벨/체험
                        'StartTimeWeek2' => $TempStartTimeWeek2,
                        'StartTimeWeek3' => $TempStartTimeWeek3,
                        'StartTimeWeek4' => $TempStartTimeWeek4,
                        'StartTimeWeek5' => $TempStartTimeWeek5,
                        'DisplayClassType' => $TempClassType,
                        'DisplayLevel' => ($TempClassType == '레벨') ? "LEVEL " . $TempClassOrderLeveltestApplyLevel : ''
                    ];
                    // Ensure WeekCountID is 1 for level/trial even if weekday times were present initially
                    if ($singleData['ClassProductID'] == 2 || $singleData['ClassProductID'] == 3) {
                        $singleData['ClassOrderWeekCountID'] = 1;
                    }
                    $processed_data[] = $singleData;
                }
                // ***** END PROCESSING LOGIC *****
            }
        } // End for loop ($i)

        // --- Save processed data to temporary file ---
        $tempDataFileName = null;
        if (!empty($processed_data)) {
            $tempDataFileName = $UpPath . 'processed_' . md5(time() . rand()) . '.tmp';
            if (file_put_contents($tempDataFileName, serialize($processed_data)) === false) {
                throw new Exception("처리된 데이터 임시 파일 저장 실패.");
                $tempDataFileName = null; // Ensure it's null if saving failed
            }
            // Set permissions if needed, though usually handled by web server umask
            // chmod($tempDataFileName, 0664);
        }
        // --- End save processed data ---

    } catch (Exception $e) {
        // Handle PHPExcel loading/reading errors
        echo '엑셀 파일을 읽는 중 오류 발생: ' . htmlspecialchars($e->getMessage()) . '<br>';
        // Optionally re-throw or log $e
        // Invalidate further processing
        $AbleExcelListNum = 0;
        $tempDataFileName = null; // Ensure no temp file is passed if reading failed
    }

    // --- Start HTML Output ---
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $_SITE_TITLE_; ?></title>
        <link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_; ?>">
        <link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_; ?>">
        <?php
        // Assuming these includes handle CSS correctly
        include_once('./includes/common_meta_tag.php');
        include_once('./inc_header.php');
        include_once('./inc_common_list_css.php');
        ?>
        <link rel="stylesheet" type="text/css" href="./css/common.css" />
        <style>
            .invalid-row td { color: red !important; }
            .integrated-row td { background-color: #f0f8ff; } /* Light blue background for integrated rows */
        </style>
    </head>
    <body>

    <div id="page_content">
        <div id="page_content_inner">
            <h3 class="heading_b uk-margin-bottom" style="text-align:center;margin-top:-30px;">
                단체 수강 신청 (통합) - 업로드 결과
            </h3>
            <form id="RegForm" name="RegForm" method="post" action="class_order_bulk_time_check_processed.php" enctype="multipart/form-data"
                  class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
                <?php if ($tempDataFileName): // Only pass the temp file name if it was created successfully ?>
                    <input type="hidden" name="ProcessedDataFile" value="<?= htmlspecialchars($tempDataFileName) ?>">
                <?php endif; ?>
                <input type="hidden" name="OriginalUploadFileName" value="<?= htmlspecialchars($UploadFileName) ?>">

                <div class="md-card">
                    <div class="md-card-content">
                        <div class="uk-grid" data-uk-grid-margin>
                            <div class="uk-width-1-1">
                                <div class="uk-overflow-container">
                                    <span style="float:right; font-size:12px;">
                                        <span style="color:red;">■</span> : 잘못된 데이터 또는 처리 불가 /
                                        <span style="background-color:#f0f8ff;">■</span> : 통합 처리 대상
                                    </span>
                                    <table class="uk-table uk-table-align-vertical uk-table-condensed" style="width:100%;margin-top:20px;"> <thead>
                                        <tr style="background-color:gray; color: white;">
                                            <th style="border: 1px solid #ccc; padding: 5px;">아이디</th>
                                            <th style="border: 1px solid #ccc; padding: 5px;">수업구분</th>
                                            <th style="border: 1px solid #ccc; padding: 5px;">테스트레벨</th>
                                            <th style="border: 1px solid #ccc; padding: 5px;">수업시간</th>
                                            <th style="border: 1px solid #ccc; padding: 5px;">시작일</th>
                                            <th style="border: 1px solid #ccc; padding: 5px;">체험/레벨시간</th>
                                            <th style="border: 1px solid #ccc; padding: 5px;">월</th>
                                            <th style="border: 1px solid #ccc; padding: 5px;">화</th>
                                            <th style="border: 1px solid #ccc; padding: 5px;">수</th>
                                            <th style="border: 1px solid #ccc; padding: 5px;">목</th>
                                            <th style="border: 1px solid #ccc; padding: 5px;">금</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if (empty($excel_feedback_rows)) {
                                            echo '<tr><td colspan="11" style="text-align:center; border: 1px solid #ccc; padding: 5px;">엑셀 파일을 읽을 수 없거나 데이터가 없습니다.</td></tr>';
                                        } else {
                                            foreach ($excel_feedback_rows as $row) {
                                                $rowClass = '';
                                                if (!$row['valid']) {
                                                    $rowClass = 'invalid-row';
                                                } elseif ($row['TempClassType'] == '통합') {
                                                    $rowClass = 'integrated-row';
                                                }
                                                echo "<tr class='$rowClass'>";
                                                echo "<td style='border:1px solid #ccc;padding: 5px;padding-left:10px;'>" . ($row['StrMemberLoginID'] ?: '&nbsp;') . "</td>";
                                                echo "<td style='border:1px solid #ccc;padding: 5px;text-align:center;'>" . ($row['StrClassType'] ?: '&nbsp;') . "</td>";
                                                echo "<td style='border:1px solid #ccc;padding: 5px;text-align:center;'>" . ($row['StrClassOrderLeveltestApplyLevel'] ?: '&nbsp;') . "</td>";
                                                echo "<td style='border:1px solid #ccc;padding: 5px;text-align:center;'>" . ($row['StrClassOrderTimeTypeID'] ?: '&nbsp;') . "</td>";
                                                echo "<td style='border:1px solid #ccc;padding: 5px;text-align:center;'>" . ($row['StrClassStartDate'] ?: '&nbsp;') . "</td>";
                                                echo "<td style='border:1px solid #ccc;padding: 5px;text-align:center;'>" . ($row['StrStartTimeLevel'] ?: '&nbsp;') . "</td>";
                                                echo "<td style='border:1px solid #ccc;padding: 5px;text-align:center;'>" . ($row['StrStartTimeWeek1'] ?: '&nbsp;') . "</td>";
                                                echo "<td style='border:1px solid #ccc;padding: 5px;text-align:center;'>" . ($row['StrStartTimeWeek2'] ?: '&nbsp;') . "</td>";
                                                echo "<td style='border:1px solid #ccc;padding: 5px;text-align:center;'>" . ($row['StrStartTimeWeek3'] ?: '&nbsp;') . "</td>";
                                                echo "<td style='border:1px solid #ccc;padding: 5px;text-align:center;'>" . ($row['StrStartTimeWeek4'] ?: '&nbsp;') . "</td>";
                                                echo "<td style='border:1px solid #ccc;padding: 5px;text-align:center;'>" . ($row['StrStartTimeWeek5'] ?: '&nbsp;') . "</td>";
                                                echo "</tr>";
                                            }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                // Show proceed button only if there are valid rows and temp file was created
                $proceedAllowed = ($AbleExcelListNum > 0 && $tempDataFileName !== null);
                ?>

                <?php if ($proceedAllowed){ ?>
                    <div style="margin-top:20px;text-align:center;color:#990000;">
                        ※ 총 <?= $TotalExcelListNum ?>건 중 <?= $AbleExcelListNum ?>건의 유효한 데이터가 확인되었습니다.<br>
                        ※ 아래 진행하기를 클릭하시면 가능한 강사를 분석하여 보여드립니다.<br>
                        강사가 없으면 신청서를 수정 후 다시 업로드해 주세요.
                    </div>
                <?php } else if ($TotalExcelListNum > 0) { ?>
                    <div style="margin-top:20px;text-align:center;color:red;">
                        ※ 총 <?= $TotalExcelListNum ?>건 중 유효한 데이터가 없습니다. 엑셀 파일을 수정 후 다시 업로드 해주세요.
                    </div>
                <?php } else { /* Case where file was empty or unreadable handled above */ } ?>

                <div style="margin-top:20px; text-align:center;" id="BtnAction">
                    <a style="margin:0 auto;display:inline-block; background-color:#888888; color:#ffffff; text-align:center; width:110px; line-height:32px; font-size:14px;" href="javascript:GoPrev();">이전으로</a>

                    <?php if ($proceedAllowed){ ?>
                        <?php $buttonText = ($AbleExcelListNum == $TotalExcelListNum) ? "수강신청 진행하기" : "유효한 학생만 진행하기"; ?>
                        <a style="margin:0 auto;display:inline-block; background-color:#556BAC; color:#ffffff; text-align:center; width:220px; line-height:32px; font-size:14px;" href="javascript:FormSubmit();"><?= $buttonText ?></a>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>

    <script>
        function GoPrev(){
            // Go back to the upload form
            location.href = "class_order_bulk_form_merge.php";
        }

        function FormSubmit() {
            // Check if the form and necessary hidden input exist
            const form = document.getElementById('RegForm');
            const processedFileField = form.elements['ProcessedDataFile'];

            if (!form || !processedFileField || !processedFileField.value) {
                alert("처리할 데이터가 없거나 오류가 발생했습니다. 이전으로 돌아가 다시 시도해주세요.");
                return;
            }

            if (confirm("진행하시겠습니까?")){
                // Show loading indicator
                document.getElementById("BtnAction").innerHTML = "<img src='images/uploading_ing.gif'><br><br>강사 가능 시간을 분석 중입니다.<br><br>데이터 양에 따라 시간이 소요될 수 있으니 기다려주세요.";
                // Submit the form to the next step
                form.submit();
            }
        }

        // Adjust colorbox size for potentially wider table
        if (parent && parent.$ && parent.$.colorbox) {
            parent.$.colorbox.resize({width:"95%", height:"95%", maxWidth: "1500", maxHeight: "1000"});
        } else {
            console.warn("Parent colorbox not detected for resize.");
        }

    </script>

    </body>
    </html>

    <?php
    // End HTML Output

} catch (Exception $e) {
    // Catch potential exceptions from file upload, DB operations (slot deletion)
    // Rollback transaction if still active (only relevant for slot deletion part now)
    if ($DbConn->inTransaction()) {
        $DbConn->rollBack();
    }
    // Sentry 에 오류 기록 (Sentry PHP SDK 함수 사용)
    if (function_exists('Sentry\captureException')) {
        Sentry\captureException($e);
    }
    // Display error message to user
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>오류</title></head><body>";
    echo "<h1>처리 중 오류 발생</h1>";
    echo "<p>오류가 발생하여 작업을 완료할 수 없습니다. 관리자에게 문의해주세요.</p>";
    echo "<p><strong>오류 메시지:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    // Provide a way back
    echo '<p><a href="class_order_bulk_form_merge.php">돌아가기</a></p>';
    echo "</body></html>";
    // Log the full error for debugging
    error_log("Error in class_order_bulk_value_check_merge.php: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    flush(); ob_flush();
} finally {
    // Close DB connection if it's still open
    // Ensure dbclose.php is included appropriately outside the try/catch or here
}
?>
<?php
// Make sure this is the final part of the script if DB connection needs closing
include_once('../includes/dbclose.php');
?>