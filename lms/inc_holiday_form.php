<?php
include('./inc_departments.php');
include_once('./inc_document_common.php');

if (!function_exists('getVacationYearByDate')) {
    // Vacation year starts on March 1st; Jan/Feb belong to the previous year.
    function getVacationYearByDate($dateValue) {
        $timestamp = strtotime($dateValue);
        if ($timestamp === false) {
            $timestamp = time();
        }
        $year = intval(date("Y", $timestamp));
        if (intval(date("n", $timestamp)) < 3) {
            $year -= 1;
        }
        return $year;
    }
}

// ----------------------------------------------
// 공통 변수/함수 로드
// ----------------------------------------------
$departments = getDepartments($LangID);
$Feedback = array();
$MemberName = array();
$DocumentReportMemberID = array();
$DocumentPermited = false; // 결재 승인자가 1명이라도 있으면 true

// 결재 상태 메시지
if (compareApprovalMemberCount($DocumentReportID)) {
    $ApporovalMessage = $승인[$LangID];
} else {
    $ApporovalMessage = $승인[$LangID];
}

// ----------------------------------------------
// (1) 휴가 연도($SearchYear) 설정 및 휴가기간 계산
// ----------------------------------------------
$SearchYear = isset($_REQUEST["SearchYear"]) ? intval($_REQUEST["SearchYear"]) : 0;
if ($SearchYear == 0) {
    if (isset($StartDate) && $StartDate != "") {
        $SearchYear = getVacationYearByDate($StartDate);
    } else if (isset($DocumentReportID) && $DocumentReportID != "0") {
        $TmpStartDate = "";
        $TmpSql = "SELECT StartDate
                     FROM SpentHoliday
                    WHERE DocumentReportID = :DocumentReportID";
        $TmpStmt = $DbConn->prepare($TmpSql);
        $TmpStmt->bindParam(':DocumentReportID', $DocumentReportID);
        $TmpStmt->execute();
        $TmpRow = $TmpStmt->fetch(PDO::FETCH_ASSOC);
        if ($TmpRow && isset($TmpRow["StartDate"])) {
            $TmpStartDate = $TmpRow["StartDate"];
        }
        if ($TmpStartDate != "") {
            $SearchYear = getVacationYearByDate($TmpStartDate);
        }
    }
}
if ($SearchYear == 0) {
    $SearchYear = getVacationYearByDate(date("Y-m-d"));
}

// “YYYY-03-01 ~ (YYYY+1)-02-28” 형태로 휴가기간 계산
$YearStartDate = $SearchYear . "-03-01";
$YearEndDate   = ($SearchYear + 1) . "-02-28";

// ----------------------------------------------
// (2) StaffHoliday 테이블에서 해당 연도의 MaxHoliday/MaxSickLeave만 가져옴
// ----------------------------------------------
$Sql2 = "SELECT StaffHolidayID, MaxHoliday, MaxSickLeave 
           FROM StaffHoliday 
          WHERE StaffID = :StaffID 
            AND Year = :SearchYear";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':StaffID', $StaffID);
$Stmt2->bindParam(':SearchYear', $SearchYear);
$Stmt2->execute();
$Row2 = $Stmt2->fetch(PDO::FETCH_ASSOC);

$StaffHolidayID = isset($Row2["StaffHolidayID"]) ? $Row2["StaffHolidayID"] : 0;
$MaxHoliday     = isset($Row2["MaxHoliday"]) ? floatval($Row2["MaxHoliday"]) : 0;
$MaxSickLeave   = isset($Row2["MaxSickLeave"]) ? floatval($Row2["MaxSickLeave"]) : 0;

// ----------------------------------------------
// (3) 로그인한 사용자 정보
// ----------------------------------------------
$MemberLoginID = isset($_COOKIE["LoginMemberID"]) ? $_COOKIE["LoginMemberID"] : "";
$Sql = "SELECT T.*, M.* 
          FROM Members as M
     LEFT JOIN Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
         WHERE M.MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
$Stmt->execute();
$Row = $Stmt->fetch(PDO::FETCH_ASSOC);

$My_MemberID     = $Row["MemberID"];
$My_MemberName   = $Row["MemberName"];
$My_StaffID      = $Row["StaffID"];
$My_TeacherID    = $Row["TeacherID"];
$My_OrganLevel   = $Row["Hr_OrganLevel"];
$My_OrganLevelID = $Row["Hr_OrganLevelID"];
$My_OrganTask2ID = $Row["Hr_OrganTask2ID"];

// 필리핀 강사 여부 확인
$IsPhTeacher = false;
if ($My_StaffID != 0 && $My_TeacherID != 0) {
    $IsPhTeacher = true;
    // 필리핀 강사면 환율 정보 가져오기(필요시)
    $Sql = "SELECT * 
              FROM Currency  
             WHERE CountryCode='PH'";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->execute();
    $Row = $Stmt->fetch(PDO::FETCH_ASSOC);
    $CountryName = $Row["CountryName"];
    $Currency    = $Row["Currency"];
}

// ----------------------------------------------
// (4) 고정 결재 라인 가져오기
// ----------------------------------------------
if($IsPhTeacher) {
    $DocumentType = 3;
} else {
    $DocumentType = 1;
}
$FixedApprovalLine = array();
$Sql = "SELECT A.*, B.MemberName, C.StaffManagement 
          FROM FixedApprovalLine A
     LEFT JOIN Members B ON A.MemberID = B.MemberID
     LEFT JOIN Staffs  C ON B.StaffID = C.StaffID
         WHERE DocumentType=:DocumentType
      ORDER BY ApprovalSequence";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':DocumentType', $DocumentType);
$Stmt->execute();
while($Row = $Stmt->fetch(PDO::FETCH_ASSOC)) {
    $ApprovalSequence = $Row["ApprovalSequence"];
    $FixedApprovalLine[] = [ $Row["MemberID"] => $Row["MemberName"] ];
}

// ----------------------------------------------
// (5) 이미 작성된 문서(수정/열람)인지 확인
// ----------------------------------------------
if ($DocumentReportID != "0") {
    // 이미 등록된 휴가정보 불러오기
    $Sql2 = "SELECT *
               FROM SpentHoliday 
              WHERE DocumentReportID = :DocumentReportID";
    $Stmt2 = $DbConn->prepare($Sql2);
    $Stmt2->bindParam(':DocumentReportID', $DocumentReportID);
    $Stmt2->execute();
    $Row2 = $Stmt2->fetch(PDO::FETCH_ASSOC);

    $SpentDays   = isset($Row2["SpentDays"]) ? floatval($Row2["SpentDays"]) : 0;
    $StartDate   = isset($Row2["StartDate"]) ? $Row2["StartDate"] : "";
    $EndDate     = isset($Row2["EndDate"])   ? $Row2["EndDate"]   : "";
    $RegistDate  = isset($Row2["RegistDate"])? $Row2["RegistDate"]: "";
    $HolidayType = isset($Row2["HolidayType"])?$Row2["HolidayType"]: 0;

} else {
    // 신규 작성
    $SpentDays  = 0;
    $StartDate  = "";
    $EndDate    = "";
    $RegistDate = date("Y-m-d H:i:s");
    $HolidayType= isset($_REQUEST["HolidayType"]) ? intval($_REQUEST["HolidayType"]) : 0;
}

// ----------------------------------------------
// (6) 휴가종류에 따라 MaxHoliday 조정 (ex: 병가이면 MaxSickLeave 적용)
// ----------------------------------------------
if ($HolidayType == 1) { // 병가
    $MaxHoliday = $MaxSickLeave;
}

// ----------------------------------------------
// (7) 이미 사용한 휴가 일수(SpentHoliday) 계산
// ----------------------------------------------
$SpentHoliday = 0; // default
if ($HolidayType != 2) { // 일반휴가/병가인 경우만
    // 제출일($RegistDate) 기준으로 같은 연도 내에서 이미 사용한 휴가 합산
    // (필요하다면 아래 로직을 수정하여 정확한 기간 필터를 적용 가능)
    $Sql2 = "SELECT SUM(SpentDays) AS SpentHoliday
               FROM SpentHoliday
              WHERE StaffHolidayID = :StaffHolidayID
                AND HolidayType    = :HolidayType
                AND unix_timestamp(RegistDate) < unix_timestamp(:RegistDate)
           GROUP BY StaffHolidayID";
    $Stmt2 = $DbConn->prepare($Sql2);
    $Stmt2->bindParam(':StaffHolidayID', $StaffHolidayID);
    $Stmt2->bindParam(':HolidayType', $HolidayType);
    $Stmt2->bindParam(':RegistDate', $RegistDate);
    $Stmt2->execute();
    $Row2 = $Stmt2->fetch(PDO::FETCH_ASSOC);
    $SpentHoliday = isset($Row2["SpentHoliday"]) ? floatval($Row2["SpentHoliday"]) : 0;

    // 문서를 수정 중일 경우 현재 문서의 휴가일수($SpentDays)를 중복으로 빼야 한다면 아래 로직 적용
    // if ($DocumentReportID != "0") {
    //     $SpentHoliday -= $SpentDays;
    // }

    // 만약 DB에 MaxHoliday=0이면 “연차 미입력” 경고
    if ($MaxHoliday == 0) {
        echo "<script>alert('{$SearchYear}{$총휴가_미입력[$LangID]}');</script>";
    }
} else {
    // 직접입력(휴가제목/일수)을 사용하는 경우
    $MaxHoliday = "-";
    $SpentHoliday = "-";
}

// ----------------------------------------------
// (8) 남은 휴가(잔여 일수) 계산
// ----------------------------------------------
$remainingVacationDays = 0;
if ($HolidayType != 2) {
    // 일반휴가/병가
    $remainingVacationDays = $MaxHoliday - $SpentHoliday - $SpentDays;
    if ($remainingVacationDays < 0) $remainingVacationDays = 0;
}

// 자바스크립트로 값 세팅
echo "<script>
    if (document.getElementById('MaxHoliday')) {
        document.getElementById('MaxHoliday').value = '{$MaxHoliday}';
    }
    if (document.getElementById('SpentHoliday')) {
        document.getElementById('SpentHoliday').value = '{$SpentHoliday}';
    }
    if (document.getElementById('RemainHoliday')) {
        document.getElementById('RemainHoliday').value = '{$remainingVacationDays}';
    }
</script>";

// ----------------------------------------------
// (9) HTML 출력부
// ----------------------------------------------
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
      crossorigin="anonymous">

<style>
    .box {
        width: 160px;
        min-height: 50px;
        border: 1px solid gray;
        display: block;
        background-color: bisque;
        transition: all 0.5s;
        transition-delay: 0.4s;
        padding: 10px;
    }
    .box:hover {
        width: 165px;
        min-height: 55px;
    }
    @media screen and (max-width:700px) {
        .user_content {
            padding: 10px !important;
        }
        .draft_approval {
            font-size:8px;
            width:320px;
        }
        .uk-grid > * {
            padding-left: 25px;
        }
        #page_content_inner {
            padding: 30px 10px 30px 10px;
        }
    }
</style>

<div class="draft_wrap">
    <div class="draft_top uk-grid-match uk-grid-large row" uk-grid>
        <div class="col-md-4 col-sm-12">
            <!-- 휴가 적용기간 (YYYY년 휴가) 드롭다운 -->
            <h3 class="uk-heading-small">
                <?=($HolidayType == 1) ? $병가원[$LangID] : $휴가원[$LangID]?>
                (<?=$SearchYear?>년 <?=$휴가[$LangID]?>)
                <select name="SearchYear" onchange="SearchSubmit();">
                    <!-- 필요 시, 연도를 여러 개 추가 -->
                    <option value="<?=date("Y")?>"     <?=($SearchYear == date("Y"))?"selected":""?>>
                        <?=date("Y")?><?=$년[$LangID]?> <?=$휴가[$LangID]?>
                    </option>
                    <option value="<?=(date("Y")-1)?>" <?=($SearchYear == (date("Y")-1))?"selected":""?>>
                        <?=(date("Y")-1)?><?=$년[$LangID]?> <?=$휴가[$LangID]?>
                    </option>
                    <option value="<?=(date("Y")+1)?>" <?=($SearchYear == (date("Y")+1))?"selected":""?>>
                        <?=(date("Y")+1)?><?=$년[$LangID]?> <?=$휴가[$LangID]?>
                    </option>
                </select>
            </h3>

            <!-- 계산된 휴가적용기간 표시 -->
            <?php if ($MaxHoliday !== 0 && $MaxHoliday !== "-") { ?>
                <div>
                    <span style='font-size:16px;color:blue'>
                        <?=$휴가적용기간[$LangID]?> : <?=$YearStartDate?> ~ <?=$YearEndDate?>
                    </span>
                </div>
            <?php } else { ?>
                <div>
                    <span style='color:red;font-size: 17px;font-weight: bold;'>
                        * <?=$휴가일수_없음[$LangID]?>
                    </span>
                </div>
            <?php } ?>
        </div>

        <!-- 결재라인 테이블 -->
        <div class="col-md-6 col-sm-12">
            <table class="draft_approval">
                <col width="5%">
                <colgroup span="6" width="15%"></colgroup>
                <tr style="height:60px;">
                    <th rowspan="2"><?=$결_재[$LangID]?></th>
                    <?php
                    $countOfApprovalLine = 0;
                    // 남은 칸(6칸 중)에 대해 동적으로 생성
                    for ($tdCount=0;$tdCount<(6-count($FixedApprovalLine));$tdCount++) { ?>
                        <td>
                            <?php
                            ${"StrDocumentReportMemberState".$tdCount} = "-";
                            if ($DocumentReportState==1) {
                                // 이미 제출된 문서의 결재자 정보 불러오기
                                $Sql3 = "SELECT A.*, B.MemberName 
                                           FROM DocumentReportMembers A
                                      INNER JOIN Members B on A.MemberID=B.MemberID 
                                          WHERE A.DocumentReportID = :DocumentReportID
                                            AND A.DocumentReportMemberOrder = :OrderNo";
                                $Stmt3 = $DbConn->prepare($Sql3);
                                $Stmt3->bindParam(':DocumentReportID', $DocumentReportID);
                                $Stmt3->bindParam(':OrderNo', $tdCount);
                                $Stmt3->execute();
                                $Row3 = $Stmt3->fetch(PDO::FETCH_ASSOC);

                                $MemberName[$tdCount] = $Row3["MemberName"];
                                $Feedback[$tdCount]   = $Row3["Feedback"];
                                $DocumentReportMemberState = $Row3["DocumentReportMemberState"];
                                $DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);

                                if ($DocumentReportMemberState==0){
                                    ${"StrDocumentReportMemberState".$tdCount} = "-";
                                } else if ($DocumentReportMemberState==1){
                                    $DocumentPermited = true;
                                    ${"StrDocumentReportMemberState".$tdCount} =
                                        $DocumentReportMemberModiDateTime . "<br>".$ApporovalMessage;
                                } else if ($DocumentReportMemberState==2){
                                    ${"StrDocumentReportMemberState".$tdCount} =
                                        $DocumentReportMemberModiDateTime . "<br>".$반려[$LangID];
                                }
                                echo ("<input type='hidden' id='DocumentReportMemberID{$tdCount}' 
                                             name='DocumentReportMemberID{$tdCount}' 
                                             value='".$Row3["MemberID"]."'>");
                                echo ($MemberName[$tdCount]);
                            } else {
                                // 아직 제출 전인 문서 => 부서/직원 선택
                                ?>
                                <select id="category<?=$tdCount?>"
                                        onchange="javascript:categoryChange(this,'DocumentReportMemberID<?=$tdCount?>',0)">
                                    <option><?=$부서선택[$LangID]?></option>
                                    <?php
                                    foreach($departments as $key => $value){
                                        echo "<option value='{$key}'>{$value}</option>";
                                    }
                                    ?>
                                </select>
                                <select id="DocumentReportMemberID<?=$tdCount?>" name="DocumentReportMemberID<?=$tdCount?>">
                                    <option value=""><?=$직원선택[$LangID]?></option>
                                </select>
                                <?php
                                if ($DocumentReportState==2) {
                                    $Sql3 = "SELECT A.MemberID, C.StaffManagement 
                                               FROM DocumentReportMembers A
                                          LEFT JOIN Members B ON A.MemberID = B.MemberID
                                          LEFT JOIN Staffs  C ON B.StaffID = C.StaffID
                                              WHERE A.DocumentReportID=:DocumentReportID
                                                AND A.DocumentReportMemberOrder=:OrderNo";
                                    $Stmt3 = $DbConn->prepare($Sql3);
                                    $Stmt3->bindParam(':DocumentReportID', $DocumentReportID);
                                    $Stmt3->bindParam(':OrderNo', $tdCount);
                                    $Stmt3->execute();
                                    $Row3 = $Stmt3->fetch(PDO::FETCH_ASSOC);
                                    $DocumentReportMemberID[] = [ $Row3["MemberID"] => $Row3["StaffManagement"] ];
                                } else {
                                    $DocumentReportMemberID[] = [0 => NULL];
                                }
                            }
                            ?>
                        </td>
                        <?php
                        $countOfApprovalLine++;
                    }

                    // 고정 결재 라인(나머지 칸)
                    for ($tdCount=$countOfApprovalLine;$tdCount<=5;$tdCount++) { ?>
                        <td>
                            <?php
                            ${"StrDocumentReportMemberState".$tdCount} = "-";
                            if ($DocumentReportState==1) {
                                $Sql3 = "SELECT A.*, B.MemberName 
                                           FROM DocumentReportMembers A
                                      INNER JOIN Members B on A.MemberID=B.MemberID 
                                          WHERE A.DocumentReportID=:DocumentReportID 
                                            AND A.DocumentReportMemberOrder=:OrderNo";
                                $Stmt3 = $DbConn->prepare($Sql3);
                                $Stmt3->bindParam(':DocumentReportID', $DocumentReportID);
                                $Stmt3->bindParam(':OrderNo', $tdCount);
                                $Stmt3->execute();
                                $Row3 = $Stmt3->fetch(PDO::FETCH_ASSOC);

                                $MemberName[$tdCount] = $Row3["MemberName"];
                                $Feedback[$tdCount]   = $Row3["Feedback"];
                                $DocumentReportMemberState = $Row3["DocumentReportMemberState"];
                                $DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);

                                if ($DocumentReportMemberState==0){
                                    ${"StrDocumentReportMemberState".$tdCount} = "-";
                                } else if ($DocumentReportMemberState==1){
                                    $DocumentPermited = true;
                                    ${"StrDocumentReportMemberState".$tdCount} =
                                        $DocumentReportMemberModiDateTime . "<br>".$ApporovalMessage;
                                } else if ($DocumentReportMemberState==2){
                                    ${"StrDocumentReportMemberState".$tdCount} =
                                        $DocumentReportMemberModiDateTime . "<br>".$반려[$LangID];
                                }
                                echo "<input type='hidden' id='DocumentReportMemberID{$tdCount}' 
                                             name='DocumentReportMemberID{$tdCount}' 
                                             value='".key($FixedApprovalLine[($tdCount-$countOfApprovalLine)])."'>";
                                echo ($MemberName[$tdCount]);
                            } else {
                                // 제출 전: 고정 결재자는 선택 불가
                                echo "<select id='DocumentReportMemberID{$tdCount}' name='DocumentReportMemberID{$tdCount}'>";
                                echo "<option value='".key($FixedApprovalLine[($tdCount-$countOfApprovalLine)]) ."'>".
                                    $FixedApprovalLine[($tdCount-$countOfApprovalLine)][key($FixedApprovalLine[($tdCount-$countOfApprovalLine)])].
                                    "</option>";
                                echo "</select>";
                            }
                            ?>
                        </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td><?=$StrDocumentReportMemberState0?></td>
                    <td><?=$StrDocumentReportMemberState1?></td>
                    <td><?=$StrDocumentReportMemberState2?></td>
                    <td><?=$StrDocumentReportMemberState3?></td>
                    <td><?=$StrDocumentReportMemberState4?></td>
                    <td><?=$StrDocumentReportMemberState5?></td>
                </tr>
            </table>
        </div>

        <!-- 반려 사유 박스 표시 -->
        <?php
        for ($i=0; $i<=5; $i++) {
            if (isset(${"StrDocumentReportMemberState".$i}) && strstr(${"StrDocumentReportMemberState".$i}, '반려')){
                ?>
                <div>
                    <div class="box">
                        <h6 style="text-align:center;color:darkslategrey">
                            <?=$MemberName[$i]?> 님의 반려 사유
                        </h6>
                        <?=$Feedback[$i]?>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>

    <!-- 입력폼(휴가정보) -->
    <input type="hidden" name="StaffHolidayID" value="<?=$StaffHolidayID?>">

    <table class="draft_table_1">
        <col width="13%">
        <col width="22%">
        <col width="">
        <col width="22%">
        <col width="13%">
        <col width="22%">
        <tr>
            <th class="draft_cell_green"><?=$제출일[$LangID]?></th>
            <td><?=$StrDocumentReportRegDateTime?></td>
            <td>인</td>
            <th><?=$증빙자료[$LangID]?></th>
            <td colspan=2 align=center>
                <? if (!$printMode) {?>
                    <div id="multiple">
                        <div type="button" class="btn btn-success fileup-btn"
                             style="display:block;margin-left:auto;margin-right:auto;margin-bottom:5px">
                            <?=$올릴파일[$LangID]?>
                            <? if (!$DocumentPermited) { ?>
                                <input type="file" id="upload-2" multiple
                                       accept=".jpg, .jpeg, .png, .gif, .doc, .docx, .xls, .xlsx, .hwp, .pdf, .psd, .txt, .ppt, .zip">
                            <? } ?>
                        </div>
                        <div id="upload-2-queue" class="queue" style="display:inline-block"></div>
                    </div>
                <? } else {
                    if ($FileName!=""){
                        $fileRealNameArr = explode(',',$FileRealName);
                        $fileNameArr     = explode(',',$FileName);
                        for($i=0; $i<count($fileRealNameArr); $i++){
                            echo "<a href=\"../uploads/document_files/{$fileNameArr[$i]}\" download>{$fileRealNameArr[$i]}</a><br>";
                        }
                    } else {
                        echo "-";
                    }
                } ?>
                <input type="hidden" id="FileRealName" name="FileRealName" value="<?=$FileRealName?>" class="draft_input">
                <input type="hidden" id="FileName"     name="FileName"     value="<?=$FileName?>"     class="draft_input">
            </td>
        </tr>
        <tr>
            <th class="draft_cell_green"><?=$소속[$LangID]?></th>
            <td colspan="5"><?=$Document_OrganName?></td>
        </tr>
        <tr>
            <th class="draft_cell_green"><?=$이름[$LangID]?></th>
            <td colspan="5"><?=$Document_MemberName?></td>
        </tr>
        <tr>
            <th class="draft_cell_green"><?=$휴가종류[$LangID]?></th>
            <td colspan="5">
                <? if (!$DocumentPermited) { ?>
                    <select id="HolidayType" name="HolidayType" onchange="SearchSubmit();">
                        <option value=0 <?=$HolidayType==0?"selected":""?>><?=$일반휴가[$LangID]?></option>
                        <option value=1 <?=$HolidayType==1?"selected":""?>><?=$병가[$LangID]?></option>
                        <option value=2 <?=$HolidayType==2?"selected":""?>><?=$직접입력[$LangID]?></option>
                    </select>
                <? } else { ?>
                    <input type="input" id="Holidaytype" name="Holidaytype"
                           value="<?=$HolidayTypeTitle?>" class="draft_input"
                        <?=$DocumentPermited?"readonly":""?>>
                <? } ?>
            </td>
        </tr>
        <tr>
            <th class="draft_cell_green"><?=$휴가제목[$LangID]?></th>
            <td colspan="5">
                <input type="input" id="DocumentReportName" name="DocumentReportName"
                       value="<?=$DocumentReportName?>" class="draft_input"
                    <?=$DocumentPermited?"readonly":""?>>
            </td>
        </tr>
        <tr>
            <th class="draft_cell_yellow"><?=$사유[$LangID]?></th>
            <td colspan="5" style="text-align:left;">
                <textarea id="DocumentReportContent" name="DocumentReportContent" class="draft_textarea"
                          <?=$DocumentPermited?"readonly":""?>>
                    <?=$DocumentReportContent?>
                </textarea>
            </td>
        </tr>
    </table>

    <table class="draft_table_3">
        <col width="25%">
        <col width="">
        <tr>
            <th rowspan="2"><?=$휴가_사용_일수[$LangID]?></th>
            <td align="center"><?=$총휴가일수[$LangID]?></td>
            <td align="center"><?=$사용한_휴가_일수[$LangID]?></td>
            <td align="center"><?=$금번_휴가일수[$LangID]?></td>
            <td align="center"><?=$잔여_휴가일수[$LangID]?></td>
        </tr>
        <tr>
            <td><input type="input" id="MaxHoliday"    name="MaxHoliday"    value="<?=$MaxHoliday?>"    class="draft_input" readonly></td>
            <td><input type="input" id="SpentHoliday"  name="SpentHoliday"  value="<?=$SpentHoliday?>"  class="draft_input" readonly></td>
            <td><input type="input" id="Holiday"       name="Holiday"       value="<?=$SpentDays?>"     class="draft_input" <?=$DocumentPermited?"readonly":""?>></td>
            <td><input type="input" id="RemainHoliday" name="RemainHoliday" value="" class="draft_input" readonly></td>
        </tr>
        <tr>
            <th><?=$휴가기간[$LangID]?></th>
            <td colspan="4">
                <!-- 화면에 “계산된 휴가적용기간” 안내 -->
                <div>
                    <span style='font-size:16px;color:red;'>
                        <?=$휴가적용기간[$LangID]?> : <?=$YearStartDate?> ~ <?=$YearEndDate?>
                    </span>
                </div>

                <input type="input" id="StartDate" name="StartDate" value="<?=$StartDate?>"
                       class="draft_input" style="width:40%;display:inline"
                    <?=$DocumentPermited?"readonly":"data-uk-datepicker=\"{format:'YYYY-MM-DD', weekstart:0}\" "?>>
                ~
                <input type="input" id="EndDate" name="EndDate" value="<?=$EndDate?>"
                       class="draft_input" style="width:40%;display:inline"
                    <?=$DocumentPermited?"readonly":"data-uk-datepicker=\"{format:'YYYY-MM-DD', weekstart:0}\" "?>>
            </td>
        </tr>
        <tr>
            <th><?=$본인_외_비상연락망[$LangID]?></th>
            <td colspan="4">
                <input type="input" id="PayMemo" name="PayMemo"
                       value="<?=$PayMemo?>" class="draft_input"
                    <?=$DocumentPermited?"readonly":""?>>
            </td>
        </tr>
    </table>

    <div class="draft_bottom">
        <?=$휴가계획서_제출[$LangID]?><br>
        <?=$StrDocumentReportRegDateTime2?>
        <div class="draft_sign_wrap">
            <?=$작성자[$LangID]?> : <?=$Document_MemberName?>
            <span class="draft_sign">(인)</span>
        </div>
    </div>
</div>

<?php include_once('./inc_category_change.php'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    // 휴가 연도 재선택 시 폼 재전송
    function SearchSubmit(){
        document.RegForm.DocumentID.value = 0;
        document.RegForm.action = "<?=basename($_SERVER['PHP_SELF'])?>";
        document.RegForm.submit();
    }

    $(document).ready(function(){

        // (A) 페이지 로드 시, 잔여 휴가 일수 세팅 (이미 PHP에서 1차로 세팅해놓았으므로 여기선 2차 보정)
        updateRemainHoliday();

        // (B) 휴가 일수 직접 수정 시, 잔여 휴가 일수 재계산
        $('#Holiday').on('keyup', function(e) {
            updateRemainHoliday();
        });

        // (C) 시작/종료일 변경 시, 자동으로 휴가일수 계산 + 잔여 휴가 재계산
        $('#StartDate, #EndDate').change(function(){
            calculateVacationDays();
            // 선택한 날짜가 휴가적용기간(3/1~2/28)을 벗어나면 경고
            if($(this).val() < '<?=$YearStartDate?>' || $(this).val() > '<?=$YearEndDate?>'){
                alert('입력하신 날짜가 선택된 연도의 휴가기간을 벗어났습니다.');
                $(this).val('');
            }
        });
    });

    // 금번 휴가일수 + 잔여 휴가 계산
    function calculateVacationDays() {
        var startDate = $('#StartDate').val();
        var endDate   = $('#EndDate').val();
        if (startDate && endDate) {
            var start = new Date(startDate);
            var end   = new Date(endDate);
            // 종료일 - 시작일 + 1
            var diff  = (end - start) / (1000*60*60*24);
            var days  = diff + 1;

            if (days < 0) {
                alert("종료일이 시작일보다 이전입니다.");
                $('#EndDate').val('');
                return;
            }
            // 금번 휴가일수
            $('#Holiday').val(days);
            updateRemainHoliday();
        }
    }

    function updateRemainHoliday() {
        var MaxHoliday    = parseFloat($('#MaxHoliday').val());
        var SpentHoliday  = parseFloat($('#SpentHoliday').val());
        var Holiday       = parseFloat($('#Holiday').val());
        if (isNaN(MaxHoliday) || isNaN(SpentHoliday) || isNaN(Holiday)) return;

        var RemainHoliday = MaxHoliday - SpentHoliday - Holiday;
        if (RemainHoliday < 0) {
            // 휴가일수가 잔여분보다 많으면 자동 보정
            $('#Holiday').val(0);
            alert("입력한 값이 잔여 휴가일을 초과합니다.");
            RemainHoliday = MaxHoliday - SpentHoliday;
        }
        $('#RemainHoliday').val(RemainHoliday.toFixed(1)); // 소수점 1자리
    }
</script>
