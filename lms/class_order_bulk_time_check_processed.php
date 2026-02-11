<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./class_order_bulk_time_check_fnc.php'); // Function CheckStudyTime is needed

//버퍼 켜기 ===============================
ob_start();
//버퍼 켜기 ===============================

$ProcessedDataFile = isset($_REQUEST["ProcessedDataFile"]) ? $_REQUEST["ProcessedDataFile"] : "";
$OriginalUploadFileName = isset($_REQUEST["OriginalUploadFileName"]) ? $_REQUEST["OriginalUploadFileName"] : ""; // May be needed for context or cleanup later

$processed_data = [];
$ErrNum = 0;
$ErrMsg = "";

// --- Load Processed Data ---
if (empty($ProcessedDataFile) || !file_exists($ProcessedDataFile)) {
    $ErrNum = 1;
    $ErrMsg = "처리할 데이터 파일을 찾을 수 없습니다.";
} else {
    $serializedData = file_get_contents($ProcessedDataFile);
    if ($serializedData === false) {
        $ErrNum = 1;
        $ErrMsg = "처리된 데이터 파일을 읽는데 실패했습니다.";
    } else {
        // Delete the temp file immediately after reading
        @unlink($ProcessedDataFile);

        $unserializedResult = @unserialize($serializedData); // Use @ to suppress notice on failure
        if ($unserializedResult === false && $serializedData !== 'b:0;') { // Check if unserialize failed
            $ErrNum = 1;
            $ErrMsg = "처리된 데이터의 형식이 잘못되었습니다.";
        } else {
            $processed_data = $unserializedResult;
            if (!is_array($processed_data)) { // Ensure it's an array
                $ErrNum = 1;
                $ErrMsg = "처리된 데이터가 유효한 배열이 아닙니다.";
                $processed_data = []; // Reset to empty array
            }
        }
    }
}
// --- End Load Processed Data ---


//================================================================
// 7일 이하 슬롯 삭제 (value_check에서도 했지만, 안전을 위해 유지 또는 제거 고려)
// 이 로직은 DB 상태에 따라 시간이 걸릴 수 있으므로, value_check에서만 수행하는 것이 더 효율적일 수 있음.
// 여기서는 일단 유지.
try {
    $DbConn->beginTransaction();
    $Sql = "SELECT
                    distinct ClassOrderSlotID
                FROM View_ClassOrderSlotDelTargets
                WHERE
                    ClassOrderSlotID NOT in (SELECT ClassOrderSlotID FROM View_ClassOrderSlotDelTargets WHERE ClassOrderSlotWeek=StudyWeek)";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);

    while ($Row = $Stmt->fetch()) {
        $DelClassOrderSlotID = $Row["ClassOrderSlotID"];
        $Sql2 = "
            update ClassOrderSlots set
                ClassOrderSlotState=0,
                DelAdminUnder7Day=1,
                DelAdminUnder7DayDateTime=now(),
                ClassOrderSlotDateModiDateTime=now()
            where ClassOrderSlotID=:delSlotId
        ";
        $Stmt2 = $DbConn->prepare($Sql2);
        $Stmt2->bindParam(':delSlotId', $DelClassOrderSlotID, PDO::PARAM_INT);
        $Stmt2->execute();
        $Stmt2 = null;
    }
    $Stmt = null;
    $DbConn->commit();
} catch (Exception $e) {
    if ($DbConn->inTransaction()) {
        $DbConn->rollBack();
    }
    error_log("Error during slot deletion in time_check_processed: " . $e->getMessage());
    // Notify user or handle error appropriately, maybe set $ErrMsg
    $ErrMsg .= " [슬롯 정리 중 오류 발생]";
}
//================================================================

?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $_SITE_TITLE_; ?></title>
        <link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_; ?>">
        <link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_; ?>"/>
        <?
        include_once('./includes/common_meta_tag.php');
        // inc_header.php might output content, ensure it's compatible with ob_start
        include_once('./inc_header.php');
        ?>

        <?
        include_once('./inc_common_list_css.php');
        ?>
        <style>
            /* Style to visually group rows from the same origin */
            .origin-group-even td {
                background-color: #ffffff;
            }

            /* White */
            .origin-group-odd td {
                background-color: #f8f8f8;
            }

            /* Light Gray */
            .origin-separator {
                border-top: 2px solid #cccccc !important;
            }

            /* Separator line */
        </style>
        <link rel="stylesheet" type="text/css" href="./css/common.css"/>
    </head>
    <body>

    <div id="page_content">
        <div id="page_content_inner">
            <h3 class="heading_b uk-margin-bottom" style="text-align:center;margin-top:-30px;">강사 선택 (통합 처리)</h3>

            <?php if ($ErrNum > 0): ?>
                <div class="uk-alert uk-alert-danger" data-uk-alert>
                    <a href="#" class="uk-alert-close uk-close"></a>
                    <?= htmlspecialchars($ErrMsg) ?>
                    <br>
                    <a href="class_order_bulk_form_merge.php">이전으로 돌아가기</a>
                </div>
            <?php else: ?>
            <form id="RegForm" name="RegForm" method="post" action="class_order_bulk_action.php"
                  enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
                <input type="hidden" name="OriginalUploadFileName"
                       value="<?= htmlspecialchars($OriginalUploadFileName) ?>">

                <div style="text-align:right;">
                </div>
                <div class="md-card">
                    <div class="md-card-content">
                        <div class="uk-grid" data-uk-grid-margin>
                            <div class="uk-width-1-1">
                                <div class="uk-overflow-container">
                                    <span style="float:right; font-size:12px;">※ 각 항목에 대한 강사를 선택해 주세요. (동일 원본 행은 같은 배경색으로 표시됩니다)</span>

                                    <table class="uk-table uk-table-align-vertical uk-table-condensed"
                                           style="width:100%;margin-top:20px;">
                                        <thead>
                                        <tr style="background-color:gray; color: white;">
                                            <th style="border: 1px solid #ccc; padding: 5px;"><?= $대리점[$LangID] ?>
                                                /<?= $학생명[$LangID] ?>/<?= $아이디[$LangID] ?></th>
                                            <th style="border: 1px solid #ccc; padding: 5px;"><?= $수업구분[$LangID] ?></th>
                                            <th style="border: 1px solid #ccc; padding: 5px;"><?= $테스트레벨[$LangID] ?></th>
                                            <th style="border: 1px solid #ccc; padding: 5px;"><?= $수업시간[$LangID] ?></th>
                                            <th style="border: 1px solid #ccc; padding: 5px;"><?= $시작일[$LangID] ?></th>
                                            <th style="border: 1px solid #ccc; padding: 5px;"><?= $체험[$LangID] ?>
                                                /<?= $레벨시간[$LangID] ?></th>
                                            <th style="border: 1px solid #ccc; padding: 5px;"><?= $월[$LangID] ?></th>
                                            <th style="border: 1px solid #ccc; padding: 5px;"><?= $화[$LangID] ?></th>
                                            <th style="border: 1px solid #ccc; padding: 5px;"><?= $수[$LangID] ?></th>
                                            <th style="border: 1px solid #ccc; padding: 5px;"><?= $목[$LangID] ?></th>
                                            <th style="border: 1px solid #ccc; padding: 5px;"><?= $금[$LangID] ?></th>
                                            <th style="border: 1px solid #ccc; padding: 5px;"><?= $등록여부[$LangID] ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <?php
                                        $LinkAdminLevelID = $_LINK_ADMIN_LEVEL_ID_; // Assume this is defined globally
                                        $EduCenterID = 1; // Assuming default EduCenterID
                                        $ArrWeekName = explode("|", "일요일|월요일|화요일|수요일|목요일|금요일|토요일"); // Make sure defined

                                        $ListNum = 1; // Counter for form elements
                                        $RegOk = 1;   // Flag to check if all required slots have teachers
                                        $SelectForms = ","; // String to track select elements
                                        $currentOriginRow = null; // Track origin row for styling
                                        $originRowCounter = 0; // Counter for alternating styles

                                        if (empty($processed_data)) {
                                            echo '<tr><td colspan="12" style="text-align:center; border: 1px solid #ccc; padding: 5px;">처리할 데이터가 없습니다.</td></tr>';
                                        } else {
                                            foreach ($processed_data as $item) {
                                                $LineRegOk = 1; // Assume this line can be registered initially

                                                // --- Extract data from $item ---
                                                $origin_row = $item['origin_row'];
                                                $InputMemberID = $item['MemberID'];
                                                $InputClassProductID = $item['ClassProductID'];
                                                $InputClassMemberType = $item['ClassMemberType'];
                                                $InputClassOrderTimeTypeID = $item['ClassOrderTimeTypeID']; // This is now the calculated value (e.g., 2 or 4)
                                                $InputClassOrderWeekCountID = $item['ClassOrderWeekCountID']; // This is now the calculated value
                                                $InputClassOrderStartDate = $item['ClassOrderStartDate'];
                                                $InputClassOrderLeveltestApplyLevel = $item['ClassOrderLeveltestApplyLevel'];
                                                $StrClassType = $item['DisplayClassType']; // e.g., "레벨 (통합)"
                                                $StrClassOrderLeveltestApplyLevel = $item['DisplayLevel']; // e.g., "LEVEL 3"
                                                $StrClassOrderTimeTypeID = $InputClassOrderTimeTypeID * 10; // Display 20 or 40
                                                $StrClassStartDate = $InputClassOrderStartDate . "<br>" . ($ArrWeekName[date('w', strtotime($InputClassOrderStartDate))] ?? '');

                                                // Times
                                                $StrStartTimeLevel = $item['StartTimeLevel'] ?? '';
                                                $StrStartTimeWeek1 = $item['StartTimeWeek1'] ?? '';
                                                $StrStartTimeWeek2 = $item['StartTimeWeek2'] ?? '';
                                                $StrStartTimeWeek3 = $item['StartTimeWeek3'] ?? '';
                                                $StrStartTimeWeek4 = $item['StartTimeWeek4'] ?? '';
                                                $StrStartTimeWeek5 = $item['StartTimeWeek5'] ?? '';

                                                // --- Get Member/Center Info for Display ---
                                                // Need to query DB based on $InputMemberID (assuming it contains valid MemberIDs like ",123,456,")
                                                $StrMemberLoginID_Display = '';
                                                $ArrMemberIDs = array_filter(explode(",", $InputMemberID)); // Get valid IDs
                                                $memberInfoCount = 0;
                                                foreach ($ArrMemberIDs as $memID) {
                                                    if (!is_numeric($memID)) continue;
                                                    $SqlMem = "select A.MemberLoginID, A.MemberName, B.CenterName
                            from Members A
                            left join Centers B on A.CenterID=B.CenterID
                            where A.MemberID = :memberId";
                                                    $StmtMem = $DbConn->prepare($SqlMem);
                                                    $StmtMem->bindParam(':memberId', $memID, PDO::PARAM_INT);
                                                    $StmtMem->execute();
                                                    $RowMem = $StmtMem->fetch(PDO::FETCH_ASSOC);
                                                    if ($RowMem) {
                                                        if ($memberInfoCount > 0) $StrMemberLoginID_Display .= "<br>";
                                                        $StrMemberLoginID_Display .= htmlspecialchars($RowMem['CenterName'] ?? 'N/A') . " / " . htmlspecialchars($RowMem['MemberName'] ?? 'N/A') . " / " . htmlspecialchars($RowMem['MemberLoginID'] ?? 'N/A');
                                                        $memberInfoCount++;
                                                    }
                                                    $StmtMem = null;
                                                }
                                                if (empty($StrMemberLoginID_Display)) $StrMemberLoginID_Display = "학생정보 없음";


                                                // --- Determine Styling for Grouping ---
                                                $rowStyleClass = '';
                                                $separatorClass = '';
                                                if ($currentOriginRow !== $origin_row) {
                                                    if ($currentOriginRow !== null) { // Add separator if not the first row
                                                        $separatorClass = 'origin-separator';
                                                    }
                                                    $currentOriginRow = $origin_row;
                                                    $originRowCounter++;
                                                }
                                                $rowStyleClass = ($originRowCounter % 2 == 0) ? 'origin-group-even' : 'origin-group-odd';


                                                ?>
                                                <tr class="<?= $rowStyleClass ?> <?= $separatorClass ?>"
                                                    data-origin-row="<?= $origin_row ?>">
                                                    <td style="border: 1px solid #ccc;text-align:left;padding: 5px 5px 5px 10px;line-height:1.5;width:300px;">
                        <span style="display:none;">
                        아이디 : <input type="text" name="MemberID_<?= $ListNum ?>" id="MemberID_<?= $ListNum ?>"
                                     value="<?= $InputMemberID ?>"><br>
                        상품 : <input type="text" name="ClassProductID_<?= $ListNum ?>"
                                    id="ClassProductID_<?= $ListNum ?>" value="<?= $InputClassProductID ?>"><br>
                        인원 : <input type="text" name="ClassMemberType_<?= $ListNum ?>"
                                    id="ClassMemberType_<?= $ListNum ?>" value="<?= $InputClassMemberType ?>"><br>
                        시수 : <input type="text" name="ClassOrderTimeTypeID_<?= $ListNum ?>"
                                    id="ClassOrderTimeTypeID_<?= $ListNum ?>" value="<?= $InputClassOrderTimeTypeID ?>"><br>
                        회수 : <input type="text" name="ClassOrderWeekCountID_<?= $ListNum ?>"
                                    id="ClassOrderWeekCountID_<?= $ListNum ?>"
                                    value="<?= $InputClassOrderWeekCountID ?>"><br>
                        시작 : <input type="text" name="ClassOrderStartDate_<?= $ListNum ?>"
                                    id="ClassOrderStartDate_<?= $ListNum ?>"
                                    value="<?= $InputClassOrderStartDate ?>"><br>
                        레벨 : <input type="text" name="ClassOrderLeveltestApplyLevel_<?= $ListNum ?>"
                                    id="ClassOrderLeveltestApplyLevel_<?= $ListNum ?>"
                                    value="<?= $InputClassOrderLeveltestApplyLevel ?>"><br>
                        선택 : <input type="text" name="SelectMasterSlotCode_<?= $ListNum ?>"
                                    id="SelectMasterSlotCode_<?= $ListNum ?>" value="|"><br> </span>
                                                        <?= $StrMemberLoginID_Display ?>
                                                    </td>
                                                    <td style="border: 1px solid #ccc;text-align:center; padding: 5px;"><?= htmlspecialchars($StrClassType) ?></td>
                                                    <td style="border: 1px solid #ccc;text-align:center; padding: 5px;"><?= htmlspecialchars($StrClassOrderLeveltestApplyLevel) ?></td>
                                                    <td style="border: 1px solid #ccc;text-align:center; padding: 5px;"><?= htmlspecialchars($StrClassOrderTimeTypeID) ?></td>
                                                    <td style="border: 1px solid #ccc;text-align:center; padding: 5px;"><?= $StrClassStartDate ?></td>
                                                    <td style="border: 1px solid #ccc;text-align:center; padding: 5px; width:120px;<? if ($StrStartTimeLevel != "") { ?>background-color:#E1E6F0;<? } ?>">
                                                        <?= htmlspecialchars($StrStartTimeLevel) ?>
                                                        <?php
                                                        // --- Call CheckStudyTime for Level/Trial Time ---
                                                        if (($InputClassProductID == 2 || $InputClassProductID == 3) && $StrStartTimeLevel != "" && $InputClassOrderStartDate != "") {
                                                            $WeekNum = date("w", strtotime($InputClassOrderStartDate)); // Day of the week for this specific class
                                                            $ClassOrderTimeTypeID_Check = 2; // Level/Trial always 20 min (represented by 2)

                                                            $ArrStrStartTimeLevel = explode(":", $StrStartTimeLevel);
                                                            $StudyTimeHour = (int)($ArrStrStartTimeLevel[0] ?? 0);
                                                            $StudyTimeMinute = (int)($ArrStrStartTimeLevel[1] ?? 0);

                                                            // Call the check function
                                                            $SelectOptions = CheckStudyTime($EduCenterID, $InputClassOrderStartDate, $StudyTimeHour, $StudyTimeMinute, $ClassOrderTimeTypeID_Check, $InputClassProductID, $LinkAdminLevelID);

                                                            if ($SelectOptions == "") {
                                                                $RegOk = 0;
                                                                $LineRegOk = 0;
                                                                echo '<div style="color:#ff0000;margin-top:5px;">강사없음</div>';
                                                            } else {
                                                                ?>
                                                                <div style="margin-top:5px;">
                                                                    <select name="LevelTeacherID_<?= $ListNum ?>"
                                                                            id="LevelTeacherID_<?= $ListNum ?>"
                                                                            style="width:100px;height:25px;"
                                                                            onchange="ChTeacher('LevelTeacherID_<?= $ListNum ?>', 'OldLevelTeacherID_<?= $ListNum ?>', 'OldLevelSlotTeacherID_<?= $ListNum ?>', 'OldLevelSlotAllTime_<?= $ListNum ?>', <?= $ListNum ?>, <?= $WeekNum ?>, <?= $StudyTimeHour ?>, <?= $StudyTimeMinute ?>, <?= $ClassOrderTimeTypeID_Check ?>, this.value, '<?= $InputClassOrderStartDate ?>')">
                                                                        <option value=""><?= $강사선택[$LangID] ?></option>
                                                                        <?= $SelectOptions ?>
                                                                    </select>
                                                                    <span style="display:none;">
                                        <input type="text" name="OldLevelTeacherID_<?= $ListNum ?>"
                                               id="OldLevelTeacherID_<?= $ListNum ?>" value="">
                                        <input type="text" name="OldLevelSlotTeacherID_<?= $ListNum ?>"
                                               id="OldLevelSlotTeacherID_<?= $ListNum ?>" value="">
                                        <input type="text" name="OldLevelSlotAllTime_<?= $ListNum ?>"
                                               id="OldLevelSlotAllTime_<?= $ListNum ?>" value="|"
                                               style="background-color:#cccccc;">
                                    </span>
                                                                </div>
                                                                <?php
                                                                $SelectForms = $SelectForms . "LevelTeacherID_" . $ListNum . ",";
                                                            }
                                                        }
                                                        ?>
                                                    </td>

                                                    <?php
                                                    // --- Call CheckStudyTime for Regular Weekday Times ---
                                                    $weekdaySlots = [
                                                        1 => $StrStartTimeWeek1, 2 => $StrStartTimeWeek2, 3 => $StrStartTimeWeek3,
                                                        4 => $StrStartTimeWeek4, 5 => $StrStartTimeWeek5
                                                    ];
                                                    for ($WeekNum = 1; $WeekNum <= 5; $WeekNum++) {
                                                        $currentWeekTime = $weekdaySlots[$WeekNum];
                                                        $tdStyle = ($currentWeekTime != "") ? 'background-color:#E1E6F0;' : '';
                                                        echo "<td style='border: 1px solid #ccc;text-align:center; padding: 5px; width:120px; $tdStyle'>";
                                                        echo htmlspecialchars($currentWeekTime);

                                                        if ($InputClassProductID == 1 && $currentWeekTime != "" && $InputClassOrderStartDate != "") { // Only for 정규 type
                                                            $ClassOrderTimeTypeID_Check = $InputClassOrderTimeTypeID; // Use the actual duration (2 or 4)

                                                            $ArrStrStartTimeWeek = explode(":", $currentWeekTime);
                                                            $StudyTimeHour = (int)($ArrStrStartTimeWeek[0] ?? 0);
                                                            $StudyTimeMinute = (int)($ArrStrStartTimeWeek[1] ?? 0);

                                                            // Calculate the actual date for this weekday based on the REGULAR start date
                                                            $startDateObj = new DateTime($InputClassOrderStartDate);
                                                            $startDayOfWeek = (int)$startDateObj->format('N'); // 1-7
                                                            $daysToAdd = ($WeekNum >= $startDayOfWeek) ? ($WeekNum - $startDayOfWeek) : (7 - $startDayOfWeek + $WeekNum);
                                                            $actualStudyDateObj = clone $startDateObj;
                                                            if ($daysToAdd > 0) {
                                                                $actualStudyDateObj->modify("+$daysToAdd days");
                                                            }
                                                            $actualStudyDate = $actualStudyDateObj->format('Y-m-d');


                                                            $SelectOptions = CheckStudyTime($EduCenterID, $actualStudyDate, $StudyTimeHour, $StudyTimeMinute, $ClassOrderTimeTypeID_Check, $InputClassProductID, $LinkAdminLevelID);

                                                            if ($SelectOptions == "") {
                                                                $RegOk = 0;
                                                                $LineRegOk = 0;
                                                                echo '<div style="color:#ff0000;margin-top:5px;">' . $강사없음[$LangID] . '</div>';
                                                            } else {
                                                                ?>
                                                                <div style="margin-top:5px;">
                                                                    <select name="Week<?= $WeekNum ?>TeacherID_<?= $ListNum ?>"
                                                                            id="Week<?= $WeekNum ?>TeacherID_<?= $ListNum ?>"
                                                                            style="width:100px;height:25px;"
                                                                            onchange="ChTeacher('Week<?= $WeekNum ?>TeacherID_<?= $ListNum ?>', 'OldWeek<?= $WeekNum ?>TeacherID_<?= $ListNum ?>', 'OldWeek<?= $WeekNum ?>SlotTeacherID_<?= $ListNum ?>', 'OldWeek<?= $WeekNum ?>SlotAllTime_<?= $ListNum ?>', <?= $ListNum ?>, <?= $WeekNum ?>, <?= $StudyTimeHour ?>, <?= $StudyTimeMinute ?>, <?= $ClassOrderTimeTypeID_Check ?>, this.value, '<?= $actualStudyDate ?>')">
                                                                        <option value=""><?= $강사선택[$LangID] ?></option>
                                                                        <?= $SelectOptions ?>
                                                                    </select>
                                                                    <span style="display:none;">
                                        <input type="text" name="OldWeek<?= $WeekNum ?>TeacherID_<?= $ListNum ?>"
                                               id="OldWeek<?= $WeekNum ?>TeacherID_<?= $ListNum ?>" value="">
                                        <input type="text" name="OldWeek<?= $WeekNum ?>SlotTeacherID_<?= $ListNum ?>"
                                               id="OldWeek<?= $WeekNum ?>SlotTeacherID_<?= $ListNum ?>" value="">
                                        <input type="text" name="OldWeek<?= $WeekNum ?>SlotAllTime_<?= $ListNum ?>"
                                               id="OldWeek<?= $WeekNum ?>SlotAllTime_<?= $ListNum ?>" value="|"
                                               style="background-color:#cccccc;">
                                    </span>
                                                                </div>
                                                                <?php
                                                                $SelectForms = $SelectForms . "Week{$WeekNum}TeacherID_" . $ListNum . ",";
                                                            }
                                                        }
                                                        echo "</td>";
                                                    } // End weekday loop
                                                    ?>

                                                    <td style="border: 1px solid #ccc;text-align:center; padding: 5px; width:100px;">
                                                        <? if ($LineRegOk == 0) { ?>
                                                            <div style="color:#ff0000;margin-top:5px; font-weight: bold;"><?= $등록불가[$LangID] ?></div>
                                                            <span style="display:none;">
                        <input type="text" name="LineRegOk_<?= $ListNum ?>" id="LineRegOk_<?= $ListNum ?>"
                               value="0"><br>
                        </span>
                                                        <? } else { ?>
                                                            <input type="checkbox" name="LineRegOk_<?= $ListNum ?>"
                                                                   id="LineRegOk_<?= $ListNum ?>"
                                                                   value="0"><?= $등록안함[$LangID] ?><? } ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                $ListNum++; // Increment for the next row in the processed data
                                            } // End foreach processed_data
                                        } // End else (if data exists)
                                        ?>
                                        </tbody>
                                    </table>
                                    <span style="display:none;">
    <input type="text" name="ListNum" id="ListNum" value="<?= max(0, $ListNum - 1) ?>" style="width:100%">
    <input type="text" name="SelectForms" id="SelectForms" value="<?= htmlspecialchars($SelectForms) ?>"
           style="width:100%">
    <input type="text" name="ClassOrderSlotAllTime" id="ClassOrderSlotAllTime" value="|" style="width:100%"> </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <? if ($RegOk == 0) { ?>
                    <div style="width:100%;color:#880000;margin-bottom:20px;margin-top:20px;text-align:center;">선택하신
                        시간중에 수업 가능한 강사가 없는 시간이 있습니다. 신청서를 재작성 또는 강사 선택이 가능한 학생만 등록 할 수 있습니다.
                    </div>
                    <script>alert("선택하신 시간중에 수업 가능한 강사가 없는 시간이 있습니다. 신청서를 재작성 또는 강사 선택이 가능한 학생만 등록 할 수 있습니다.");</script>
                <? } ?>

                <div style="margin-top: 20px; text-align:center;" id="BtnAction">
                    <a style="margin:0 auto;display:inline-block; background-color:#888888; color:#ffffff; text-align:center; width:110px; line-height:32px; font-size:14px;"
                       href="javascript:GoPrev();"><?= $이전으로[$LangID] ?></a>
                    <?php if ($ListNum > 1) : // Only show submit if there are rows ?>
                        <? if ($RegOk == 0) { ?>
                            <a style="margin:0 auto;display:inline-block; background-color:#556BAC; color:#ffffff; text-align:center; width:240px; line-height:32px; font-size:14px;"
                               href="javascript:FormSubmit();"><?= $강사_선택이_가능한_학생만_등록[$LangID] ?></a>
                        <? } else { ?>
                            <a style="margin:0 auto;display:inline-block; background-color:#556BAC; color:#ffffff; text-align:center; width:110px;  line-height:32px; font-size:14px;"
                               href="javascript:FormSubmit();"><?= $등록하기[$LangID] ?></a>
                        <? } ?>
                    <?php endif; ?>
                </div>
        </div>
        </form>
        <?php endif; // End else for $ErrNum check ?>
    </div>
    </div>

    <script>
        let selectTeachers = [];

        function ChTeacher(SelectFormID, OldSelectFormID, OldSlotTeacherIDFormID, OldSlotAllTimeFormID, ListNum, WeekNum, StudyTimeHour, StudyTimeMinute, ClassOrderTimeTypeID, TeacherID, StudyStartDate) {

            var ExistSlot = 0;
            // Ensure ClassOrderSlotAllTime exists before accessing
            var ClassOrderSlotAllTime_Element = document.getElementById("ClassOrderSlotAllTime");
            var ClassOrderSlotAllTime_ = ClassOrderSlotAllTime_Element ? ClassOrderSlotAllTime_Element.value : "|";

            // Ensure OldSlotTeacherIDFormID element exists
            var OldSlotTeacherIDFormID_Element = document.getElementById(OldSlotTeacherIDFormID);
            var OldSlotTeacherIDFormID_ = OldSlotTeacherIDFormID_Element ? OldSlotTeacherIDFormID_Element.value : "";

            // Ensure ClassOrderTimeTypeID is a number
            var classOrderTimeSlots = parseInt(ClassOrderTimeTypeID, 10);
            if (isNaN(classOrderTimeSlots) || classOrderTimeSlots <= 0) {
                console.error("Invalid ClassOrderTimeTypeID:", ClassOrderTimeTypeID);
                return; // Prevent further execution if invalid
            }

            // If a teacher is selected, check for conflicts
            if (TeacherID != "") {
                for (var jj = 0; jj < classOrderTimeSlots; jj++) {
                    var TempStudyTimeHour = parseInt(StudyTimeHour, 10);
                    var TempStudyTimeMinute = parseInt(StudyTimeMinute, 10) + (jj * 10);

                    if (TempStudyTimeMinute >= 60) {
                        TempStudyTimeHour += 1;
                        TempStudyTimeMinute -= 60;
                    }
                    TempStudyTimeHour = TempStudyTimeHour % 24;

                    // Use the actual date (StudyStartDate) instead of the day of the week (WeekNum) for a unique check
                    var CheckSlot_ = "|" + TeacherID + "_" + StudyStartDate + "_" + TempStudyTimeHour + "_" + TempStudyTimeMinute + "|";

                    if (ClassOrderSlotAllTime_.indexOf(CheckSlot_) !== -1) {
                        ExistSlot = 1;
                        break;
                    }
                }
            }

            if (ExistSlot == 1) {
                alert("<?=$먼저_선택한_수업_중에_동일한_강사와_시간이_중복되는_신청이_있습니다[$LangID]?>");
                // Revert selection only if element exists
                var SelectFormElement = document.getElementById(SelectFormID);
                if (SelectFormElement) {
                    SelectFormElement.value = OldSlotTeacherIDFormID_;
                }
            } else {
                //============================ 전체 관련 ======================================
                var OldSlotAllTimeFormElement = document.getElementById(OldSlotAllTimeFormID);
                var ClassOrderSlotAllTimeElement = document.getElementById("ClassOrderSlotAllTime");

                if (OldSlotAllTimeFormElement && ClassOrderSlotAllTimeElement) {
                    var OldSlotAllTimeFormID_ = OldSlotAllTimeFormElement.value;
                    var ClassOrderSlotAllTime_ = ClassOrderSlotAllTimeElement.value;

                    if (OldSlotAllTimeFormID_ !== "|") {
                        ClassOrderSlotAllTime_ = ClassOrderSlotAllTime_.replace(OldSlotAllTimeFormID_, "|");
                    }

                    if (TeacherID != "") {
                        var NewSlotAllTime = "";
                        for (var jj = 0; jj < classOrderTimeSlots; jj++) {
                            var TempStudyTimeHour = parseInt(StudyTimeHour, 10);
                            var TempStudyTimeMinute = parseInt(StudyTimeMinute, 10) + (jj * 10);

                            if (TempStudyTimeMinute >= 60) {
                                TempStudyTimeHour += 1;
                                TempStudyTimeMinute -= 60;
                            }
                            TempStudyTimeHour = TempStudyTimeHour % 24;
                            
                            // Use the actual date (StudyStartDate) for building the slot string
                            NewSlotAllTime += TeacherID + "_" + StudyStartDate + "_" + TempStudyTimeHour + "_" + TempStudyTimeMinute + "|";
                        }
                        OldSlotAllTimeFormElement.value = "|" + NewSlotAllTime;
                        ClassOrderSlotAllTime_ += NewSlotAllTime;
                    } else {
                        OldSlotAllTimeFormElement.value = "|";
                    }
                    ClassOrderSlotAllTimeElement.value = ClassOrderSlotAllTime_.replace("||", "|");
                }
                //============================ 전체 관련 ======================================


                //============================ 라인 관련 ======================================
                var SelectMasterSlotCodeElement = document.getElementById("SelectMasterSlotCode_" + ListNum);
                var OldSelectFormElement = document.getElementById(OldSelectFormID);
                var OldSlotTeacherIDFormElement = document.getElementById(OldSlotTeacherIDFormID);

                if (SelectMasterSlotCodeElement && OldSelectFormElement && OldSlotTeacherIDFormElement) {
                    var SelectMasterSlotCode_ = SelectMasterSlotCodeElement.value;
                    var OldSelectMasterSlotCode_ = OldSelectFormElement.value;

                    if (OldSelectMasterSlotCode_ != "") {
                        SelectMasterSlotCode_ = SelectMasterSlotCode_.replace(OldSelectMasterSlotCode_, "|");
                    }

                    if (TeacherID != "") {
                        // Use the actual date (StudyStartDate) for the master slot code
                        var newMasterSlot = "|" + TeacherID + "_" + StudyStartDate + "_" + StudyTimeHour + "_" + StudyTimeMinute + "|";
                        OldSelectFormElement.value = newMasterSlot;
                        SelectMasterSlotCode_ += TeacherID + "_" + StudyStartDate + "_" + StudyTimeHour + "_" + StudyTimeMinute + "|";
                    } else {
                        OldSelectFormElement.value = "";
                    }
                    SelectMasterSlotCodeElement.value = SelectMasterSlotCode_.replace("||", "|");
                    OldSlotTeacherIDFormElement.value = TeacherID;
                }
                //============================ 라인 관련 ======================================
            }
        }


        function FormSubmit() {

            var SelectFormsElement = document.getElementById("SelectForms");
            if (!SelectFormsElement) return;

            var SelectForms = SelectFormsElement.value;
            var ArrSelectForm = SelectForms.split(',');
            var AllSelect = 1; // Assume all selections are made or skippable

            for (var ii = 1; ii < ArrSelectForm.length - 1; ii++) { // Adjusted loop condition
                var SelectFormName = ArrSelectForm[ii];
                if (!SelectFormName) continue; // Skip empty elements

                var SelectElement = document.getElementById(SelectFormName);
                if (!SelectElement) continue; // Skip if element not found

                if (SelectElement.value == "") {
                    // Check if this line is supposed to be registered
                    var ArrLineNum = SelectFormName.split("_");
                    var LineNum = ArrLineNum[ArrLineNum.length - 1]; // Get the last part as LineNum
                    var CheckboxElement = document.getElementById("LineRegOk_" + LineNum);

                    // If checkbox exists and is NOT checked (meaning user wants to register this line)
                    if (CheckboxElement && CheckboxElement.checked == false) {
                        AllSelect = 0; // Found an unselected teacher for a line intended for registration
                        break; // No need to check further
                    }
                }
            }

            if (AllSelect == 0) {
                alert("<?=$모든_시간의_강사를_선택해_주세요[$LangID]?> (등록 안함으로 체크된 항목 제외)");
                return;
            }

            if (confirm("<?=$등록_하시겠습니까[$LangID]?>?")) {
                // Show loading indicator
                var btnActionElement = document.getElementById('BtnAction');
                if (btnActionElement) {
                    btnActionElement.innerHTML = "<img src='images/uploading_ing.gif'><br><br>수업을 등록 중입니다...";
                }
                // Submit the form
                document.getElementById('RegForm').submit();
            }

        }

        function FormErr() { // This function doesn't seem to be used, but kept it.
            alert("등록할 수 없습니다. 안내를 참고하세요.");
        }

        function CloseThisWinodw() { // This function doesn't seem to be used, but kept it.
            // Assuming this is inside a colorbox iframe
            if (parent && parent.$ && parent.$.fn && parent.$.fn.colorbox) {
                parent.$.fn.colorbox.close();
            }
        }

        function GoPrev() {
            // Go back to the upload form
            location.href = "class_order_bulk_form_merge.php";
        }

        // Adjust colorbox size for potentially taller table
        if (parent && parent.$ && parent.$.colorbox) {
            parent.$.colorbox.resize({width: "95%", height: "95%", maxWidth: "1500", maxHeight: "1000"});
        } else {
            console.warn("Parent colorbox not detected for resize.");
        }
    </script>


    <?php
    /*
    // validateDate function might be needed by included files, ensure it's defined somewhere accessible
    // If not used elsewhere, it can be removed from here.
    function validateDate($date, $format = 'Y-m-d H:i:s'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    */
    ?>

    </body>
    </html>

<?php

//버퍼 끄고 출력 ===============================
ob_end_flush();
//버퍼 끄고 출력 ===============================


include_once('../includes/dbclose.php');
?>