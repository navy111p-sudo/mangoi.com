<?php // Ensure PHP opening tag is present if this is the very start of the file.

// This line assumes $objWorksheet and $i are already defined in the calling script (e.g., class_order_bulk_value_check_merge.php)
// Also assumes $DbConn and $ArrWeekName are available.

$DataOk[1] = 1;//아이디
$DataOk[2] = 1;//정규, 체험, 레벨, 통합, 레벨체험
$DataOk[3] = 1;//20, 40
$DataOk[4] = 1;//시작일(레벨, 체험일)
$DataOk[5] = 1;//체험, 레벨 시작시간
$DataOk[6] = 1;//월요일
$DataOk[7] = 1;//화요일
$DataOk[8] = 1;//수요일
$DataOk[9] = 1;//목요일
$DataOk[10] = 1;//금요일

if (!function_exists('validateDate')) {
    // It's generally better to define functions outside loops or use includes/requires.
    // Defining it here might cause "Cannot redeclare function" errors if included multiple times without the check.
    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The H:i:s part might be wrong for 'H:i' format checks later. Let's adjust the usage later or create a specific time validator.
        // For 'Y-m-d', it's fine. For 'H:i', let's refine the check where it's used.
        return $d && $d->format($format) == $date;
    }
}

// Function to specifically validate H:i format and 10-minute intervals
if (!function_exists('validateTimeHM')) {
    function validateTimeHM($time) {
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
            return false; // Basic H:i format check
        }
        $d = DateTime::createFromFormat('H:i', $time);
        if (!$d || $d->format('H:i') !== $time) {
            return false; // Stricter validation
        }
        // Check for 10-minute interval
        list($hour, $minute) = explode(':', $time);
        if ((int)$minute % 10 != 0) {
            return false;
        }
        return true;
    }
}


// 중복 아이디가 있는지 체크
$MemberLoginID = $objWorksheet->getCell('A' . $i)->getValue();//아이디
$TempClassType = $objWorksheet->getCell('B' . $i)->getValue();//정규, 체험, 레벨, 통합
$TempClassOrderLeveltestApplyLevel = $objWorksheet->getCell('C' . $i)->getValue();//레벨테스트 레벨
$TempClassOrderTimeTypeID = $objWorksheet->getCell('D' . $i)->getValue();//20, 40
$TempClassStartDate = $objWorksheet->getCell('E' . $i)->getValue();//시작일(레벨, 체험일)
$TempStartTimeLevel = $objWorksheet->getCell('F' . $i)->getValue();//체험, 레벨 시작시간
$TempStartTimeWeek1 = $objWorksheet->getCell('G' . $i)->getValue();//월요일
$TempStartTimeWeek2 = $objWorksheet->getCell('H' . $i)->getValue();//화요일
$TempStartTimeWeek3 = $objWorksheet->getCell('I' . $i)->getValue();//수요일
$TempStartTimeWeek4 = $objWorksheet->getCell('J' . $i)->getValue();//목요일
$TempStartTimeWeek5 = $objWorksheet->getCell('K' . $i)->getValue();//금요일

// --- Format dates and times ---
// Check if date/time values are numeric (Excel timestamp) or string
if (is_numeric($TempClassStartDate)) {
    $TempClassStartDate = PHPExcel_Style_NumberFormat::toFormattedString($TempClassStartDate, 'YYYY-MM-DD');
} else {
    $TempClassStartDate = trim((string)$TempClassStartDate); // Handle as string if not numeric
}

if (is_numeric($TempStartTimeLevel)) {
    $TempStartTimeLevel = PHPExcel_Style_NumberFormat::toFormattedString($TempStartTimeLevel, 'hh:mm');
} else {
    $TempStartTimeLevel = trim((string)$TempStartTimeLevel);
}

if (is_numeric($TempStartTimeWeek1)) {
    $TempStartTimeWeek1 = PHPExcel_Style_NumberFormat::toFormattedString($TempStartTimeWeek1, 'hh:mm');
} else {
    $TempStartTimeWeek1 = trim((string)$TempStartTimeWeek1);
}
if (is_numeric($TempStartTimeWeek2)) {
    $TempStartTimeWeek2 = PHPExcel_Style_NumberFormat::toFormattedString($TempStartTimeWeek2, 'hh:mm');
} else {
    $TempStartTimeWeek2 = trim((string)$TempStartTimeWeek2);
}
if (is_numeric($TempStartTimeWeek3)) {
    $TempStartTimeWeek3 = PHPExcel_Style_NumberFormat::toFormattedString($TempStartTimeWeek3, 'hh:mm');
} else {
    $TempStartTimeWeek3 = trim((string)$TempStartTimeWeek3);
}
if (is_numeric($TempStartTimeWeek4)) {
    $TempStartTimeWeek4 = PHPExcel_Style_NumberFormat::toFormattedString($TempStartTimeWeek4, 'hh:mm');
} else {
    $TempStartTimeWeek4 = trim((string)$TempStartTimeWeek4);
}
if (is_numeric($TempStartTimeWeek5)) {
    $TempStartTimeWeek5 = PHPExcel_Style_NumberFormat::toFormattedString($TempStartTimeWeek5, 'hh:mm');
} else {
    $TempStartTimeWeek5 = trim((string)$TempStartTimeWeek5);
}
// --- End Format ---


$MemberLoginID = trim($MemberLoginID);
$MemberLoginID = str_replace(" ","",$MemberLoginID);
$MemberLoginID = ",".$MemberLoginID; // Add comma prefix
$TempClassStartDate = trim($TempClassStartDate);
$TempClassOrderLeveltestApplyLevel = trim($TempClassOrderLeveltestApplyLevel);
$TempClassOrderTimeTypeID = trim($TempClassOrderTimeTypeID);
$TempStartTimeLevel = trim($TempStartTimeLevel); // Trim again after potential formatting
$TempStartTimeWeek1 = trim($TempStartTimeWeek1);
$TempStartTimeWeek2 = trim($TempStartTimeWeek2);
$TempStartTimeWeek3 = trim($TempStartTimeWeek3);
$TempStartTimeWeek4 = trim($TempStartTimeWeek4);
$TempStartTimeWeek5 = trim($TempStartTimeWeek5);
$TempClassType = trim($TempClassType);


// --- Validate Level Test Level ---
if (!preg_match("/^[1-8]$/", $TempClassOrderLeveltestApplyLevel)) { // More specific regex
    $TempClassOrderLeveltestApplyLevel = 1;
}
// --- End Validate Level Test Level ---


// --- Validate Member ID ---
$ArrMemberLoginID = explode(",",$MemberLoginID);
$StrMemberLoginID = "";
$MemberCount = 0;
$InputMemberID = ",";
$DataOk[1] = 1; // Assume OK initially
for ($ii=1; $ii<count($ArrMemberLoginID); $ii++){ // Fixed loop condition
    $CheckMemberLoginID = trim($ArrMemberLoginID[$ii]);
    if ($CheckMemberLoginID!=""){
        // Use prepared statements to prevent SQL injection
        $Sql = "select
                    A.*,
                    B.CenterName
                from Members A
                    inner join Centers B on A.CenterID=B.CenterID
                where
                    A.MemberLevelID=19
                    and A.MemberLoginID=:memberLoginID";
        $Stmt = $DbConn->prepare($Sql);
        $Stmt->bindParam(':memberLoginID', $CheckMemberLoginID);
        $Stmt->execute();
        $Row = $Stmt->fetch(PDO::FETCH_ASSOC); // Fetch associative array
        $Stmt = null;
        $MemberID = $Row ? $Row["MemberID"] : null; // Check if Row exists

        if ($MemberCount>0){
            $StrMemberLoginID = $StrMemberLoginID . "<br>";
        }

        if (!$MemberID){
            $DataOk[1] = 0; // Set error flag
            $StrMemberLoginID = $StrMemberLoginID . htmlspecialchars($ArrMemberLoginID[$ii]) . " (x)"; // htmlspecialchars for safety
        }else{
            $CenterName = $Row["CenterName"];
            $MemberName = $Row["MemberName"];
            $MemberNickName = $Row["MemberNickName"]; // Not used in StrMemberLoginID currently

            $InputMemberID = $InputMemberID . $MemberID . ",";
            // Use htmlspecialchars for safety
            $StrMemberLoginID = $StrMemberLoginID . htmlspecialchars($CenterName) . " / " . htmlspecialchars($MemberName) . " / " . htmlspecialchars($ArrMemberLoginID[$ii]);
        }
        $MemberCount++;
    }
}

if ($MemberCount==0) { // Handle case where no valid ID was entered
    $DataOk[1] = 0;
    $StrMemberLoginID = "아이디 미입력 (x)";
}

$InputClassMemberType = 0; // Default
if ($MemberCount==1){
    $InputClassMemberType = 1;
}else if ($MemberCount==2){
    $InputClassMemberType = 2;
}else if ($MemberCount>2){
    $InputClassMemberType = 3;
}
// --- End Validate Member ID ---


// --- 수업구분 체크 ---
if (
    $TempClassType=="정규" ||
    $TempClassType=="체험" ||
    $TempClassType=="레벨" ||
    $TempClassType=='통합' ||
    $TempClassType=='레벨체험'        // [레벨체험] 추가
){
    $DataOk[2] = 1;
    $StrClassType = $TempClassType;

    // 수업 구분별 불필요 시간 제거
    if ($TempClassType=="체험" || $TempClassType=="레벨" || $TempClassType=="레벨체험"){ // [레벨체험] 포함
        // 체험·레벨·레벨체험 ⇒ 요일별 정규시간은 불필요
        $TempStartTimeWeek1 = $TempStartTimeWeek2 =
        $TempStartTimeWeek3 = $TempStartTimeWeek4 =
        $TempStartTimeWeek5 = "";
    } else if ($TempClassType=="정규"){
        // 정규 ⇒ 레벨 시작시간 불필요
        $TempStartTimeLevel = "";
    }
} else {
    $DataOk[2] = 0;
    $StrClassType = htmlspecialchars($TempClassType) . " (x)";
}
// --- End 수업구분 체크 ---


//==================== 수업시간구분 체크
$InputClassOrderTimeTypeID = 0; // Initialize
if (!preg_match("/^[0-9]+$/", $TempClassOrderTimeTypeID)) { // Check if it's numeric
    $TempClassOrderTimeTypeID = 0; // Set to 0 if not numeric
}
$TempClassOrderTimeTypeID = (int)$TempClassOrderTimeTypeID; // Cast to integer

if ($TempClassOrderTimeTypeID==20 || $TempClassOrderTimeTypeID==40){
    $DataOk[3] = 1;
    $StrClassOrderTimeTypeID = $TempClassOrderTimeTypeID;
    $InputClassOrderTimeTypeID = $TempClassOrderTimeTypeID / 10;

    // '통합'의 경우에도 체험/레벨 처럼 기본 20분으로 설정할지는 다음 단계에서 결정. 여기서는 입력값 기반으로 설정.
    // 단, '체험' 또는 '레벨' 명시 시에는 20분 고정
    if ($TempClassType=="체험" || $TempClassType=="레벨"){
        $StrClassOrderTimeTypeID = 20;
        $InputClassOrderTimeTypeID = 2;
    }
} else {
    // 통합이 아닌데 20, 40이 아니거나 || 체험/레벨인데 20이 아닌 경우 -> 오류
    // (위 if에서 체험/레벨이면 20/40 입력시 20으로 강제 변환되므로, 이 else는 20/40이 아닌 값이 들어온 경우)
    if ($TempClassType=="체험" || $TempClassType=="레벨") {
        // 체험/레벨은 무조건 20이어야 하지만, 혹시 다른값이 들어오면 일단 20으로 처리하고 다음 단계에서 사용
        $DataOk[3] = 1; // 데이터 자체는 유효하게 만듬 (20으로 처리)
        $StrClassOrderTimeTypeID = 20;
        $InputClassOrderTimeTypeID = 2;
    } else {
        // 정규 또는 통합인데 20, 40이 아닌 경우
        $DataOk[3] = 0;
        $StrClassOrderTimeTypeID = htmlspecialchars($TempClassOrderTimeTypeID) . " (x)";
        $InputClassOrderTimeTypeID = 0; // Ensure it's invalid
    }
}
//==================== End 수업시간구분 체크


//==================== 시작날짜 체크
$CheckDateTime = validateDate($TempClassStartDate, 'Y-m-d');
if ($CheckDateTime){
    $DataOk[4] = 1;
    $w = date("w", strtotime($TempClassStartDate));
    if ($w==0 || $w==6){ // 주말 체크
        $DataOk[4] = 0;
        $StrClassStartDate = $TempClassStartDate . "(".$ArrWeekName[$w].")" . " (주말X)";
    }else{
        $StrClassStartDate = $TempClassStartDate . "<br>". $ArrWeekName[$w];
    }
}else{
    $DataOk[4] = 0;
    $StrClassStartDate = htmlspecialchars($TempClassStartDate) . " (형식오류)";
}
//==================== End 시작날짜 체크


//==================== 체험,레벨 시간 체크
$StrStartTimeLevel = "";
if ($TempStartTimeLevel!=""){
    $CheckTime = validateTimeHM($TempStartTimeLevel); // Use specific time validator
    if ($CheckTime){
        $DataOk[5] = 1;
        $StrStartTimeLevel = $TempStartTimeLevel;
        // 10분 단위 체크는 validateTimeHM 에서 수행
    }else{
        $DataOk[5] = 0;
        $StrStartTimeLevel = htmlspecialchars($TempStartTimeLevel) . " (형식/시간오류)";
    }
} else {
    // 시간이 비어있는 경우 - 나중에 수업구분에 따라 필수 여부 체크
    $DataOk[5] = 1; // 일단 형식은 OK (비어있음)
    $StrStartTimeLevel = "";
}
//==================== End 체험,레벨 시간 체크


$InputClassOrderWeekCountID = 0;

//==================== 월요일 시간 체크
$StrStartTimeWeek1 = "";
if ($TempStartTimeWeek1!=""){
    $CheckTime = validateTimeHM($TempStartTimeWeek1);
    if ($CheckTime){
        $DataOk[6] = 1;
        $StrStartTimeWeek1 = $TempStartTimeWeek1;
        $InputClassOrderWeekCountID++; //주 수업회수 계산
    }else{
        $DataOk[6] = 0;
        $StrStartTimeWeek1 = htmlspecialchars($TempStartTimeWeek1) . " (형식/시간오류)";
    }
} else {
    $DataOk[6] = 1; // 비어있음 OK
    $StrStartTimeWeek1 = "";
}
//==================== End 월요일 시간 체크

//==================== 화요일 시간 체크
$StrStartTimeWeek2 = "";
if ($TempStartTimeWeek2!=""){
    $CheckTime = validateTimeHM($TempStartTimeWeek2);
    if ($CheckTime){
        $DataOk[7] = 1;
        $StrStartTimeWeek2 = $TempStartTimeWeek2;
        $InputClassOrderWeekCountID++; //주 수업회수 계산
    }else{
        $DataOk[7] = 0;
        $StrStartTimeWeek2 = htmlspecialchars($TempStartTimeWeek2) . " (형식/시간오류)";
    }
} else {
    $DataOk[7] = 1; // 비어있음 OK
    $StrStartTimeWeek2 = "";
}
//==================== End 화요일 시간 체크

//==================== 수요일 시간 체크
$StrStartTimeWeek3 = "";
if ($TempStartTimeWeek3!=""){
    $CheckTime = validateTimeHM($TempStartTimeWeek3);
    if ($CheckTime){
        $DataOk[8] = 1;
        $StrStartTimeWeek3 = $TempStartTimeWeek3;
        $InputClassOrderWeekCountID++; //주 수업회수 계산
    }else{
        $DataOk[8] = 0;
        $StrStartTimeWeek3 = htmlspecialchars($TempStartTimeWeek3) . " (형식/시간오류)";
    }
} else {
    $DataOk[8] = 1; // 비어있음 OK
    $StrStartTimeWeek3 = "";
}
//==================== End 수요일 시간 체크

//==================== 목요일 시간 체크
$StrStartTimeWeek4 = "";
if ($TempStartTimeWeek4!=""){
    $CheckTime = validateTimeHM($TempStartTimeWeek4);
    if ($CheckTime){
        $DataOk[9] = 1;
        $StrStartTimeWeek4 = $TempStartTimeWeek4;
        $InputClassOrderWeekCountID++; //주 수업회수 계산
    }else{
        $DataOk[9] = 0;
        $StrStartTimeWeek4 = htmlspecialchars($TempStartTimeWeek4) . " (형식/시간오류)";
    }
} else {
    $DataOk[9] = 1; // 비어있음 OK
    $StrStartTimeWeek4 = "";
}
//==================== End 목요일 시간 체크

//==================== 금요일 시간 체크
$StrStartTimeWeek5 = "";
if ($TempStartTimeWeek5!=""){
    $CheckTime = validateTimeHM($TempStartTimeWeek5);
    if ($CheckTime){
        $DataOk[10] = 1;
        $StrStartTimeWeek5 = $TempStartTimeWeek5;
        $InputClassOrderWeekCountID++; //주 수업회수 계산
    }else{
        $DataOk[10] = 0;
        $StrStartTimeWeek5 = htmlspecialchars($TempStartTimeWeek5) . " (형식/시간오류)";
    }
} else {
    $DataOk[10] = 1; // 비어있음 OK
    $StrStartTimeWeek5 = "";
}
//==================== End 금요일 시간 체크



//==================== 시간 미입력 필수 체크 ====================
$isRegularTimeMissing = (
    $TempStartTimeWeek1=="" && $TempStartTimeWeek2=="" &&
    $TempStartTimeWeek3=="" && $TempStartTimeWeek4=="" && $TempStartTimeWeek5==""
);
$isLevelTimeMissing = ($TempStartTimeLevel=="");

if ($TempClassType=="체험" || $TempClassType=="레벨"){
    if ($isLevelTimeMissing){
        $DataOk[5] = 0;
        $StrStartTimeLevel = "시간미입력";
    }
} else if ($TempClassType=="정규"){
    if ($isRegularTimeMissing){
        $DataOk[6] = 0;
        $StrStartTimeWeek1 = "시간미입력";
    }
// ▼ [레벨체험] 전용 규칙 ---------------------------------
} else if ($TempClassType=="레벨체험"){                    // [레벨체험] 분기
    /* 조건
       - 레벨/체험 시간 필수
       - 정규 요일 시간은 없어도 됨
    */
    if ($isLevelTimeMissing){
        $DataOk[5] = 0;
        $StrStartTimeLevel = "시간미입력";
    }
// ▲ [레벨체험] -------------------------------------------
// 기존 통합 규칙
} else if ($TempClassType=="통합"){
    if ($isLevelTimeMissing){
        $DataOk[5] = 0;
        $StrStartTimeLevel = "시간미입력";
    }
    if ($isRegularTimeMissing){
        $DataOk[6] = 0;
        $StrStartTimeWeek1 = "시간미입력";
    }
}
//==================== End 시간 미입력 필수 체크 ====================


?>