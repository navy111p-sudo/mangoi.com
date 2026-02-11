<?php
// lms/account_income_statement.php (수정 완료 - 월별 그래프 데이터 로직 수정)

include "./inc_income_query.php"; // 원본 inc_income_query.php 포함

// 자바스크립트로 그래프를 그려주기 위해서 데이타를 배열로 저장한다.
$IncomeArray = array();
$ExpenseArray = array();

// --- 손익계산서 데이터 조회 ---
try {
    $Sql = makeIncomeStateSql(); // 현재 선택된 조건으로 SQL 생성
} catch (Exception $e) {
    error_log("makeIncomeStateSql 함수 실행 오류: " . $e->getMessage());
    $Sql = null;
}
// $FirstSearchDate 는 현재 사용되지 않는 것으로 보임. 필요 시 $SearchDate 값 사용
// $FirstSearchDate = $SearchDate ?? ($StartDate ?: date("Y-m-d"));


/**
 * 손익계산서 HTML 테이블 출력 함수
 * (이전과 동일)
 */
function printIncome($sqlDataQuery, $display="block", $printTitle=true, $width=95, $id='income'){
    global $DbConn, $_SERVER, $Search_sw, $SearchStartYear, $SearchStartMonth, $SelectedAccount, $direction, $direction2, $SelectedCompany, $StartDate, $EndDate;
    global $PrintState;
    global $IncomeArray, $ExpenseArray; // 그래프 데이터 저장을 위해 global 필요

    // 변수 기본값 설정
    $currentSearchSw = $Search_sw ?? '2';
    $currentYear = $SearchStartYear ?? date('Y');
    $currentMonth = $SearchStartMonth ?? '';
    $currentSelectedAccount = $SelectedAccount ?? '';
    $currentSelectedCompany = $SelectedCompany ?? '';
    $currentDirection = $direction ?? 'asc';
    $currentDirection2 = $direction2 ?? 'asc';
    $currentPrintState = $PrintState ?? '0';
    $currentStartDate = $StartDate ?? '';
    $currentEndDate = $EndDate ?? '';

    // 링크 파라미터 생성
    $baseLinkParams = http_build_query([
        'Search_sw' => $currentSearchSw, 'SearchStartYear' => $currentYear, 'SearchStartMonth' => $currentMonth,
        'StartDate' => $currentStartDate, 'EndDate' => $currentEndDate, 'SelectedAccount' => $currentSelectedAccount, 'SelectedCompany' => $currentSelectedCompany
    ]);
    $linkOrderByID = $_SERVER['PHP_SELF'] . "?" . $baseLinkParams . "&OrderBy=AccBookConfigID&direction=" . ($currentDirection == "asc" ? "desc" : "asc");
    $linkOrderByMoney = $_SERVER['PHP_SELF'] . "?" . $baseLinkParams . "&OrderBy=AccBookMoney&direction2=" . ($currentDirection2 == "asc" ? "desc" : "asc");

    // 기간 제목 생성
    $periodTitle = "";
    if ($currentSearchSw == '1') { $periodTitle = $currentYear . "년"; }
    elseif ($currentSearchSw == '2' && !empty($currentMonth)) { $periodTitle = $currentYear . "년 " . $currentMonth . "월"; }
    elseif ($currentSearchSw == '3' && !empty($currentStartDate) && !empty($currentEndDate)) { $periodTitle = $currentStartDate . " ~ " . $currentEndDate; }
    else { $periodTitle = $currentYear . "년"; }

    // 테이블 시작
    echo "<div id='" . htmlspecialchars($id) . "' class='uk-overflow-container' style='width:" . intval($width) . "%;display:" . htmlspecialchars($display) . ";float:left;'>
            <table id='excelTable_" . htmlspecialchars($id) . "' class='uk-table uk-table-align-vertical sticky-table' border='1' style='width:100%;'>
                <thead>
                    <tr>";
    if ($printTitle) {
        echo "    <th class='pin' nowrap style='width:15%;height:25px; text-align:center;'>계정구분</th>
                        <th nowrap style='width:40%;height:25px; text-align:center;'><a href='" . htmlspecialchars($linkOrderByID) . "'><div class='uk-flex uk-flex-middle uk-flex-center'><div>과목명</div><div class='sort-arrow uk-flex uk-flex-column' style='margin-left:5px'><div style='height:7px'><i class='uk-icon-hover uk-icon-sort-asc'></i></div><div style='height:22px'><i class='uk-icon-sort-desc'></i></div></div></div></a></th>";
    }
    // 금액 헤더 수정 (원본 스타일 유지)
    echo "      <th nowrap style='width:25%;height:25px; text-align:center;'><a href='" . htmlspecialchars($linkOrderByMoney) . "'><div class='uk-flex uk-flex-middle uk-flex-center '><div>금액(" . htmlspecialchars($currentYear."년 ".($currentMonth ? $currentMonth."월" : '')) . ")</div><div class='sort-arrow uk-flex uk-flex-column' style='margin-left:5px'><div style='height:7px'><i class='uk-icon-hover uk-icon-sort-asc'></i></div><div style='height:22px'><i class='uk-icon-sort-desc'></i></div></div></div></a></th>
                    </tr>
                </thead>
            <tbody>
        ";

    // --- 데이터 조회 및 처리 ---
    $ListCount    = 0;
    $Total_Money1 = 0; // 수익 합계
    $Total_Money2 = 0; // 비용 합계
    $subTypeCounts = [];
    $allRows = [];

    if ($sqlDataQuery) {
        try {
            // 메인 데이터 조회
            $Stmt = $DbConn->prepare($sqlDataQuery);
            $Stmt->execute();
            $allRows = $Stmt->fetchAll(PDO::FETCH_ASSOC);
            $Stmt = null;

            // 데이터 기반 SubType 카운트 계산
            if (!empty($allRows)) {
                foreach ($allRows as $row) {
                    $subType = $row['AccBookConfigSubType'] ?? null;
                    if ($subType !== null) {
                        if (!isset($subTypeCounts[$subType])) $subTypeCounts[$subType] = 0;
                        $subTypeCounts[$subType]++;
                    }
                }
            }

        } catch (PDOException $e) {
            echo "<tr><td colspan='" . ($printTitle ? 3 : 1) . "' style='text-align:center; padding: 20px; color:red;'>데이터 조회 중 오류 발생 (DB)</td></tr>";
            error_log("printIncome DB 오류: " . $e->getMessage());
            $allRows = [];
        }
    } else {
        echo "<tr><td colspan='" . ($printTitle ? 3 : 1) . "' style='text-align:center; padding: 20px; color:orange;'>조회 조건 오류 (SQL 생성 실패)</td></tr>";
    }

    // --- 데이터 출력 루프 ---
    $currentSubType = null;
    $Imsi_Cnt = 0; // 원본 변수 유지

    if (!empty($allRows)) {
        foreach ($allRows as $Row) {
            $ListCount++;
            $Imsi_Cnt++;

            $AccBookConfigID    = $Row["AccBookConfigID"];
            $AccBookConfigType  = $Row["AccBookConfigType"];
            $AccBookConfigSubType = $Row["AccBookConfigSubType"];
            $AccBookConfigName  = $Row["AccBookConfigName"];
            $SumOfMoney         = (float)($Row["SumOfMoney"] ?? 0);

            // 합계 계산 및 배열 저장
            if ($AccBookConfigType == 1) {
                $Total_Money1 += $SumOfMoney;
                // 그래프용 배열은 메인 호출($id === 'income1') 시에만 채움
                if ($id === 'income1' && $SumOfMoney != 0) {
                    $IncomeArray[$AccBookConfigName] = $SumOfMoney;
                }
            } else {
                $Total_Money2 += $SumOfMoney;
                if ($id === 'income1' && $SumOfMoney != 0) {
                    $ExpenseArray[$AccBookConfigName] = $SumOfMoney;
                }
            }

            // SubType 이름 설정
            $AccBookConfigSubTypeName = "";
            if ($AccBookConfigSubType == 1) { $AccBookConfigSubTypeName = "매출"; }
            else if ($AccBookConfigSubType == 2) { $AccBookConfigSubTypeName = "영업외 수익"; }
            else if ($AccBookConfigSubType == 3) { $AccBookConfigSubTypeName = "판매비와 관리비"; }
            else if ($AccBookConfigSubType == 4) { $AccBookConfigSubTypeName = "영업외 비용"; }

            // 행 스타일
            $Back_Color = ($Imsi_Cnt % 2 == 0) ? "#F4F4F4" : "#fff";
            $fontColor = ($AccBookConfigType == 1) ? "#006CD8" : "#E80000";

            echo "<tr style='background:" . $Back_Color . "' >";

            if ($printTitle) {
                // 계정구분 출력
                if ($currentSubType !== $AccBookConfigSubType) {
                    $currentSubType = $AccBookConfigSubType;
                    $rowspan = isset($subTypeCounts[$currentSubType]) ? $subTypeCounts[$currentSubType] : 1;
                    echo "<td class='gubun pin' ".($rowspan > 0 ? "rowspan='".$rowspan."'" : "") ." style='font-size:14px;text-align:center; vertical-align: middle; color:".$fontColor.";'>".htmlspecialchars($AccBookConfigSubTypeName)."</td>";
                }
                // 과목명 출력
                echo "<td style='text-align:center; color:".$fontColor.";'>";
                if (($currentPrintState ?? '0') != "1") {
                    echo "<a href='javascript:OpenAccountList(".intval($AccBookConfigID).");' style='color:".$fontColor.";'>".htmlspecialchars($AccBookConfigName ?? '-')."</a>";
                } else {
                    echo htmlspecialchars($AccBookConfigName ?? '-');
                }
                echo "</td>";
            }
            // 금액 출력
            echo "<td style='text-align:right; color:".($AccBookConfigType==1?"#0057ae":"#c40000")."; font-weight:bold;'>".number_format($SumOfMoney)."</td></tr>";
        } // end foreach
    } // end if (!empty($allRows))

    // --- 합계 행 출력 ---
    if ($ListCount > 0) {
        $Total_Money = $Total_Money1 - $Total_Money2;
        echo "<tr>";
        if ($printTitle){ echo "<td class='uk-text-wrap uk-table-td-center pin' colspan=2 style='background:#EAF4FF;'>수익(매출) 합계</td>"; }
        echo "	<td style='text-align:right; color:#006CD8; font-size:1.1em; font-weight:bold; background:#EAF4FF;'>".number_format($Total_Money1)."</td></tr>";
        echo "<tr>";
        if ($printTitle){ echo "<td class='uk-text-wrap uk-table-td-center pin' colspan=2 style='background:#FFF4FA;'>비용 합계</td>"; }
        echo "	<td style='text-align:right; color:#E80000; font-size:1.1em; font-weight:bold; background:#FFF4FA;'>".number_format($Total_Money2)."</td></tr>";
        echo "<tr>";
        if ($printTitle){ echo "<td class='uk-text-wrap uk-table-td-center pin' colspan=2 style='background:".($Total_Money >= 0?"#DFEFFF":"#FFE6F3").";'>당기 순이익</td>"; }
        echo "	<td style='text-align:right; color:".($Total_Money >= 0?"#0000ff":"#ff0000")."; font-size:16px; font-weight:bold; background:".($Total_Money >= 0?"#DFEFFF":"#FFE6F3").";'>".($Total_Money >= 0?"":"(").number_format(abs($Total_Money)).($Total_Money >= 0?"":")")."</td></tr>";
    } elseif (empty($allRows) && $sqlDataQuery) {
        echo "<tr><td colspan='" . ($printTitle ? 3 : 1) . "' style='text-align:center; padding: 20px;'>등록된 자료가 없습니다</td></tr>";
    }

    echo "  </tbody>
        </table>
        </div>";
} // end function printIncome

?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    /* 원본 스타일 유지 */
    .select2-selection { height: 44px !important; border-color: #ced4da !important; }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{ width:8px; }
    table { border-collapse: collapse; border: 1px solid #aaa; }
    th, td { border: 1px solid #aaa; background-clip: padding-box; scroll-snap-align: start; padding: 0.4rem; min-width: 4rem; text-align: left; margin: 0; vertical-align: middle;}
    tbody tr:last-child th, tbody tr:last-child td { border-bottom: 0; }
    thead { z-index: 1000; position: sticky; top: 0; }
    th { }
    thead th { background-color: #f8f8f8; border-top: 0; }
    thead th.pin { position: sticky; left: 0; z-index: 1001; background-color: #f8f8f8; border-left: 0; }
    tbody th { background-clip: padding-box; border-left: 0; }
    tbody { z-index: 10; position: relative; }
    tbody th.pin, tbody td.pin { position: sticky; left: 0; background-color: #f8f8f8; z-index: 11;}
    .uk-table-td-center { text-align: center; }
    .sort-arrow i { font-size: 0.8em; color: #999; }
    .sort-arrow .uk-icon-sort-asc { margin-bottom: -10px; }
</style>

<script>
    $(document).ready(function() {
        $("#AccountCombo").select2();
        if (typeof UIkit !== 'undefined' && UIkit.datepicker) {
            UIkit.datepicker('#StartDate', { format: 'YYYY-MM-DD', weekstart: 0 });
            UIkit.datepicker('#EndDate', { format: 'YYYY-MM-DD', weekstart: 0 });
        }
    });
    function SearchSubmit(searchType) {
        var form = document.SearchForm;
        var selectedAccounts = $('#AccountCombo').val();
        $('input[name="SelectedAccount"]').val(selectedAccounts ? selectedAccounts.join(',') : '');
        $('input[name="Search_sw"]').val(searchType); // Search_sw 값 설정 확인
        if (searchType == 3) {
            if ($('#StartDate').val() === '' || $('#EndDate').val() === '') { alert('기간을 선택해주세요.'); return false; }
            if ($('#StartDate').val() > $('#EndDate').val()) { alert('시작일이 종료일보다 늦을 수 없습니다.'); return false; }
        } else if (searchType == 2) {
            if ($('#SearchStartMonth').val() === '') { alert('조회할 월을 선택해주세요.'); return false; }
        } else if (searchType == 1) {
            if ($('#SearchStartYear').val() === '') { alert('조회할 연도를 선택해주세요.'); return false; }
        }
        form.submit();
    }
    function OpenAccountList(configId) { /* 이전 답변 내용 유지 */ alert("상세 내역 보기 기능 구현 필요 (Config ID: " + configId + ")"); }
    function printIt(printThis) { window.print(); }
    function OpenPrint() { alert("엑셀 다운로드 기능 구현 필요"); }
    function CommentWrite(){ /* 이전 답변 내용 유지 */
        var Comment = $('#IncomeStatementComment').val();
        var YearMonth = <?= json_encode((!empty($SearchStartYear) && !empty($SearchStartMonth)) ? $SearchStartYear . str_pad($SearchStartMonth, 2, '0', STR_PAD_LEFT) : '') ?>;
        var SelectedCompanyValue = <?= json_encode($SelectedCompany ?? "2") ?>;
        if (!YearMonth) { alert('코멘트를 저장할 년월 정보가 올바르지 않습니다.'); return; }
        $.ajax({ url: "ajax_set_income_statement_comment.php", method: "POST", data: { Comment: Comment, YearMonth: YearMonth, SelectedCompany: SelectedCompanyValue }, dataType: "json",
            success: function (data) { alert(data && data.success ? '코멘트를 저장했습니다.' : '코멘트 저장 실패: ' + (data && data.message ? data.message : '오류')); },
            error: function (jqXHR, textStatus, errorThrown) { alert('저장 중 오류: ' + textStatus); console.error("CommentWrite Error:", textStatus, errorThrown, jqXHR.responseText); }
        });
    }
</script>

<div id="app" >
    <div id="page_content">
        <div id="page_content_inner">

            <h3 class="heading_b uk-margin-bottom"><?= htmlspecialchars($손익계산서[$LangID] ?? '손익계산서') ?> <?= htmlspecialchars($TitleString ?? '') ?></h3>

            <?php if (($PrintState ?? '0') != "1") : ?>
                <form name="SearchForm" method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                    <input type="hidden" name="OrderBy" value="<?= htmlspecialchars($OrderBy ?? 'AccBookConfigID') ?>">
                    <input type="hidden" name="direction" value="<?= htmlspecialchars($direction ?? 'asc') ?>">
                    <input type="hidden" name="direction2" value="<?= htmlspecialchars($direction2 ?? 'asc') ?>">
                    <input type="hidden" name="SelectedCompany" value="<?= htmlspecialchars($SelectedCompany ?? '') ?>">
                    <input type="hidden" name="SelectedAccount" value="<?= htmlspecialchars($SelectedAccount ?? '') ?>">
                    <input type="hidden" name="Search_sw" value="<?= htmlspecialchars($Search_sw ?? '2') ?>">

                    <div class="md-card" style="margin-bottom:10px;">
                        <div class="md-card-content">
                            <div class="uk-grid" data-uk-grid-margin="">
                                <div class="uk-width-medium-1-10" style="padding-top:7px;"><select id="SearchStartYear" name="SearchStartYear" class="uk-width-1-1" style="height:40px;"><option value="">년도 선택</option><?php $cy = $SearchStartYear ?? date("Y"); for ($i = date("Y"); $i >= 2019; $i--) { echo "<option value=\"$i\"".($cy==$i?" selected":"").">$i 년</option>"; } ?></select></div>
                                <div class="uk-width-medium-1-10" style="padding-top:7px; vertical-align:middle;"><a href="javascript:SearchSubmit(1)" class="md-btn md-btn-primary" style="background-color:#8d73d4;">년도별 조회</a></div>
                                <div class="uk-width-medium-1-10" style="padding-top:7px;"><select id="SearchStartMonth" name="SearchStartMonth" class="uk-width-1-1" style="height:40px;"><option value="">월 선택</option><?php $cm = $SearchStartMonth ?? ''; for ($i = 1; $i <= 12; $i++) { echo "<option value=\"$i\"".($cm==$i?" selected":"").">$i ".($월월[$LangID]??"월")."</option>"; } ?></select></div>
                                <div class="uk-width-medium-1-10" style="padding-top:7px; vertical-align:middle;"><a href="javascript:SearchSubmit(2)" class="md-btn md-btn-primary" style="background-color:#68b7e2;">월별 조회</a></div>
                                <div class="uk-width-medium-2-10" style="padding-top:7px;"><input type="text" size=8 id="StartDate" name="StartDate" value="<?= htmlspecialchars($StartDate ?? '') ?>" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}" placeholder="시작일" style="width: calc(50% - 5px);">~<input type="text" size=8 id="EndDate" name="EndDate" value="<?= htmlspecialchars($EndDate ?? '') ?>" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}" placeholder="종료일" style="width: calc(50% - 5px);"></div>
                                <div class="uk-width-medium-1-10" style="padding-top:7px; vertical-align:middle;"><a href="javascript:SearchSubmit(3)" class="md-btn md-btn-primary" style="background-color:#39a879;">기간별 조회</a></div>
                                <div class="uk-width-medium-1-10" style="padding-top:7px; vertical-align:middle;"><button type="button" onclick="javascript:printIt(document.getElementById('printSection'))" class="md-btn md-btn-primary" style="background-color:#6ac7ac;"><?= htmlspecialchars($인쇄[$LangID] ?? '인쇄') ?></button></div>
                                <div class="uk-width-medium-1-10" style="padding-top:7px; vertical-align:middle;"><a href="javascript:OpenPrint();" class="md-btn md-btn-primary" style="background-color:#eccc5f; color:#fff;" download=""><?= htmlspecialchars($엑셀_다운로드[$LangID] ?? '엑셀') ?></a></div>
                            </div>
                            <div class="uk-grid uk-margin-top" data-uk-grid-margin>
                                <div class="uk-width-medium-6-10" style="padding-top:7px; vertical-align:middle;"><label for="AccountCombo" style="margin-right: 10px;">계좌/카드 선택</label><select id="AccountCombo" class="js-example-basic-multiple js-states form-control" name="states[]" multiple="multiple" style="width: 75%;"><?php /* 계좌 목록 로드 PHP - 이전 답변 참조 */ ?></select></div>
                                <div class="uk-width-medium-4-10 uk-text-right" style="padding-top:7px; vertical-align:middle;"><?php /* 회사별 버튼 PHP - 이전 답변 참조 */ ?></div>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; ?>

            <div class="md-card" style="margin-bottom:10px;">
                <div id="printSection" class="md-card-content">
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-1-1">
                            <?php /* 보고서 제목 PHP - 이전 답변 참조 */ ?>
<!--                            <div style="text-align:center;margin-top:15px;margin-bottom:15px;"><h3>--><?php //= $reportTitle /* 이전 답변에서 정의됨 */ ?><!--</h3></div>-->
                        </div>
                        <div class="uk-width-1-1">
                            <?php if (isset($Sql) && $Sql !== null) { printIncome($Sql, "block", true, 95, 'income1'); } else { /* 오류 메시지 */ } ?>
                        </div>
                    </div>
                </div>
                <div class="md-card-content">
                    <?php /* 코멘트 PHP - 이전 답변 참조 */ ?>
                </div>
            </div>

            <?php if (($PrintState ?? '0') != "1"): ?>
                <?php
                // ===============================================================
                // *** 월별/분기별 그래프 데이터 생성 로직 (수정됨) ***
                // ===============================================================
                $currentGraphYear = $SearchStartYear ?? date('Y'); // 그래프 표시 연도

                // 월별 데이터 저장 배열 초기화
                $incomeMonthMoney = array_fill_keys(array_map(function($m) use ($currentGraphYear) { return $currentGraphYear . '-' . str_pad($m, 2, '0', STR_PAD_LEFT); }, range(1, 12)), 0);
                $expenseMonthMoney = array_fill_keys(array_map(function($m) use ($currentGraphYear) { return $currentGraphYear . '-' . str_pad($m, 2, '0', STR_PAD_LEFT); }, range(1, 12)), 0);

                // 전역 변수 백업 (makeIncomeStateSql 함수가 변경할 수 있으므로)
                $originalSearchStartYear = $SearchStartYear;
                $originalSearchStartMonth = $SearchStartMonth;
                $originalSearch_sw = $Search_sw;
                $originalStartDate = $StartDate;
                $originalEndDate = $EndDate;

                // 1월부터 12월까지 반복하며 각 월의 손익 계산
                for ($month = 1; $month <= 12; $month++) {
                    // 해당 월로 검색 조건 임시 설정 (월별 조회 모드)
                    $SearchStartYear = $currentGraphYear;
                    $SearchStartMonth = $month;
                    $Search_sw = "2"; // 월별 조회 모드로 고정
                    $StartDate = "";  // 월별 조회 시 기간 초기화
                    $EndDate = "";

                    $monthIncomeTotal = 0;
                    $monthExpenseTotal = 0;

                    try {
                        $monthSql = makeIncomeStateSql(); // 해당 월의 SQL 생성

                        if ($monthSql) {
                            $stmtMonth = $DbConn->prepare($monthSql);
                            $stmtMonth->execute(); // 바인딩 필요 시 수정
                            $monthRows = $stmtMonth->fetchAll(PDO::FETCH_ASSOC);
                            $stmtMonth = null;

                            // 결과 분석하여 해당 월의 총 수익/비용 계산
                            foreach ($monthRows as $row) {
                                $sum = (float)($row['SumOfMoney'] ?? 0);
                                if (($row['AccBookConfigType'] ?? 2) == 1) { // 수익
                                    $monthIncomeTotal += $sum;
                                } else { // 비용
                                    $monthExpenseTotal += $sum;
                                }
                            }
                        }
                    } catch (Exception $e) {
                        error_log("월별 손익 계산 오류 ($currentGraphYear-$month): " . $e->getMessage());
                    }

                    // 계산된 값을 배열에 저장
                    $monthKey = $currentGraphYear . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
                    $incomeMonthMoney[$monthKey] = $monthIncomeTotal;
                    $expenseMonthMoney[$monthKey] = $monthExpenseTotal;
                } // end for month

                // 전역 변수 원상 복구
                $SearchStartYear = $originalSearchStartYear;
                $SearchStartMonth = $originalSearchStartMonth;
                $Search_sw = $originalSearch_sw;
                $StartDate = $originalStartDate;
                $EndDate = $originalEndDate;


                // 분기별 데이터 계산
                $IncomeQuarter = array(0.0, 0.0, 0.0, 0.0);
                $ExpenseQuarter = array(0.0, 0.0, 0.0, 0.0);
                if (!empty($incomeMonthMoney)) {
                    foreach ($incomeMonthMoney as $monthKey => $value) {
                        $monthNum = (int)substr($monthKey, 5, 2);
                        if ($monthNum >= 1 && $monthNum <= 12) {
                            $quarterIndex = floor(($monthNum - 1) / 3);
                            if (isset($IncomeQuarter[$quarterIndex])) $IncomeQuarter[$quarterIndex] += $value;
                        }
                    }
                }
                if (!empty($expenseMonthMoney)) {
                    foreach ($expenseMonthMoney as $monthKey => $value) {
                        $monthNum = (int)substr($monthKey, 5, 2);
                        if ($monthNum >= 1 && $monthNum <= 12) {
                            $quarterIndex = floor(($monthNum - 1) / 3);
                            if (isset($ExpenseQuarter[$quarterIndex])) $ExpenseQuarter[$quarterIndex] += $value;
                        }
                    }
                }
                // ===============================================================
                // *** 월별/분기별 그래프 데이터 생성 로직 종료 ***
                // ===============================================================


                // --- 수강생 현황 데이터 조회 ---
                // (이전 답변과 동일)
                $studentStatusData = array_fill(1, 12, ['newStudents' => 0, 'dropoutStudents' => 0, 'attendance' => 0, 'increaseRate' => 0.0, 'reductionRate' => 0.0, 'changeRate' => 0.0]);
                $studentStatusYear = $SearchStartYear ?? date('Y');
                $nowYear = date("Y");
                $nowMonth = ($studentStatusYear == $nowYear) ? date("n") : 12;
                try {
                    $attendanceSql = "SELECT DATE_FORMAT(EndDateTime, '%Y-%m') AS month_year, COUNT(DISTINCT MemberID) AS student_count FROM Classes WHERE YEAR(EndDateTime) = :year AND ClassState = 2 GROUP BY month_year";
                    $stmtAtt = $DbConn->prepare($attendanceSql); $stmtAtt->bindParam(':year', $studentStatusYear, PDO::PARAM_INT); $stmtAtt->execute();
                    $monthlyAttendance = $stmtAtt->fetchAll(PDO::FETCH_KEY_PAIR); $stmtAtt = null;

                    for ($i = 1; $i <= 12; $i++) {
                        if ($studentStatusYear > $nowYear || ($studentStatusYear == $nowYear && $i > $nowMonth)) continue;
                        $currentMonthStr = $studentStatusYear . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                        $prevMonthStr = date('Y-m', strtotime($currentMonthStr . ' -1 month'));
                        $currentAttendance = $monthlyAttendance[$currentMonthStr] ?? 0;
                        $prevAttendance = $monthlyAttendance[$prevMonthStr] ?? 0;
                        $newStudents = 0; $dropoutStudents = 0;
                        try {
                            $newSql = "SELECT COUNT(DISTINCT d.MemberID) as newStudents FROM Classes d WHERE d.MemberID IN (SELECT DISTINCT a.MemberID FROM Classes a WHERE DATE_FORMAT(a.EndDateTime,'%Y-%m') = :currentMonth AND a.ClassState = 2) AND d.MemberID NOT IN (SELECT DISTINCT a.MemberID FROM Classes a WHERE DATE_FORMAT(a.EndDateTime,'%Y-%m') = :prevMonth AND a.ClassState = 2)";
                            $stmtNew = $DbConn->prepare($newSql); $stmtNew->bindParam(':currentMonth', $currentMonthStr); $stmtNew->bindParam(':prevMonth', $prevMonthStr); $stmtNew->execute();
                            $newRow = $stmtNew->fetch(PDO::FETCH_ASSOC); $newStudents = $newRow ? (int)$newRow['newStudents'] : 0;
                            $dropSql = "SELECT COUNT(DISTINCT d.MemberID) as dropoutStudents FROM Classes d WHERE d.MemberID IN (SELECT DISTINCT a.MemberID FROM Classes a WHERE DATE_FORMAT(a.EndDateTime,'%Y-%m') = :prevMonth AND a.ClassState = 2) AND d.MemberID NOT IN (SELECT DISTINCT a.MemberID FROM Classes a WHERE DATE_FORMAT(a.EndDateTime,'%Y-%m') = :currentMonth AND a.ClassState = 2)";
                            $stmtDrop = $DbConn->prepare($dropSql); $stmtDrop->bindParam(':currentMonth', $currentMonthStr); $stmtDrop->bindParam(':prevMonth', $prevMonthStr); $stmtDrop->execute();
                            $dropRow = $stmtDrop->fetch(PDO::FETCH_ASSOC); $dropoutStudents = $dropRow ? (int)$dropRow['dropoutStudents'] : 0;
                        } catch (PDOException $e) { error_log("월별 신규/탈락생 조회 오류 ($currentMonthStr): " . $e->getMessage()); }
                        $increaseRate = ($prevAttendance > 0) ? round(($newStudents / $prevAttendance) * 100, 1) : 0.0;
                        $reductionRate = ($prevAttendance > 0) ? round(($dropoutStudents / $prevAttendance) * 100, 1) : 0.0;
                        $changeRate = ($prevAttendance > 0) ? round((($currentAttendance - $prevAttendance) / $prevAttendance) * 100, 1) : ($currentAttendance > 0 ? 100.0 : 0.0);
                        $studentStatusData[$i] = compact('newStudents', 'dropoutStudents', 'increaseRate', 'reductionRate', 'changeRate');
                        $studentStatusData[$i]['attendance'] = $currentAttendance;
                    }
                } catch (PDOException $e) { error_log("수강생 현황 데이터 조회 오류: " . $e->getMessage()); }
                ?>

                <div class="md-card" style="margin-bottom:10px;">
                    <div class="md-card-content"><div class="uk-grid" data-uk-grid-margin><div class="uk-width-medium-1-2 uk-width-small-1-1"><h3 style="text-align:center">수익 그래프</h3><div id="chartdiv" class="uk-width-1-1" style="height:550px"></div></div><div class="uk-width-medium-1-2 uk-width-small-1-1"><h3 style="text-align:center">비용 그래프</h3><div id="chartdiv2" class="uk-width-1-1" style="height:550px"></div></div></div></div>
                    <div class="md-card-content"><div class="uk-grid" data-uk-grid-margin><div class="uk-width-1-1"><h2 style="text-align:center">월별 손익 그래프 (<?= htmlspecialchars($currentGraphYear) ?>년)</h2><div id="chartdiv3" style="width: 100%; height: 700px;"></div></div></div></div>
                    <div class="md-card-content"><div class="uk-grid" data-uk-grid-margin><div class="uk-width-1-1"><h2 style="text-align:center">월별 손익 꺽은선 그래프 (<?= htmlspecialchars($currentGraphYear) ?>년)</h2><div id="chartdiv4" style="width: 100%; height: 700px;"></div></div></div></div>
                    <div class="md-card-content"><div class="uk-grid" data-uk-grid-margin><div class="uk-width-1-1"><h2 style="text-align:center">분기별 손익 꺽은선 그래프 (<?= htmlspecialchars($currentGraphYear) ?>년)</h2><div id="chartdiv5" style="width: 100%; height: 700px;"></div></div></div></div>
                    <div class="md-card-content"><div class="uk-grid" data-uk-grid-margin><div class="uk-width-1-1"><h2 style="text-align:center">수강생 현황 (<?= htmlspecialchars($studentStatusYear) ?>년)</h2><div class="uk-overflow-container"><table class="uk-table uk-table-condensed uk-table-striped uk-text-center"><thead><tr><th class="uk-text-center" style="width: 8%;">구분</th><?php for ($i = 1; $i <= 12; $i++) : ?><th class="uk-text-center" style="width: 7.6%;"><?= $i ?>월</th><?php endfor; ?></tr></thead><tbody><tr><td>신규생</td><?php for ($i = 1; $i <= 12; $i++) : ?><td style="background-color:#fde0e0;"><?= number_format($studentStatusData[$i]['newStudents'] ?? 0) ?></td><?php endfor; ?></tr><tr><td>탈락생</td><?php for ($i = 1; $i <= 12; $i++) : ?><td style="background-color:#e0f7fa;"><?= number_format($studentStatusData[$i]['dropoutStudents'] ?? 0) ?></td><?php endfor; ?></tr><tr><td>수강생</td><?php for ($i = 1; $i <= 12; $i++) : ?><td><?= number_format($studentStatusData[$i]['attendance'] ?? 0) ?></td><?php endfor; ?></tr><tr><td>증가비율(%)</td><?php for ($i = 1; $i <= 12; $i++) : ?><td style="background-color:#fde0e0;"><?= $studentStatusData[$i]['increaseRate'] ?? 0 ?>%</td><?php endfor; ?></tr><tr><td>감소비율(%)</td><?php for ($i = 1; $i <= 12; $i++) : ?><td style="background-color:#e0f7fa;"><?= $studentStatusData[$i]['reductionRate'] ?? 0 ?>%</td><?php endfor; ?></tr><tr><td>증감율(%)</td><?php for ($i = 1; $i <= 12; $i++) : $rate = $studentStatusData[$i]['changeRate'] ?? 0; ?><td style="<?= $rate >= 0 ? 'background-color:#e8f5e9;' : 'background-color:#ffebee;' ?>"><?= $rate > 0 ? '+' : '' ?><?= $rate ?>%</td><?php endfor; ?></tr></tbody></table></div></div></div></div>
                </div> <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
                <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
                <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

                <script>
                    am4core.ready(function() {

                        // 테마 적용
                        am4core.useTheme(am4themes_animated);

                        // --- 차트 1: 수익 파이 차트 ---
                        try {
                            var chart = am4core.create("chartdiv", am4charts.PieChart);
                            chart.responsive.enabled = true;
                            var incomeChartData = [];
                            var totalIncomeFromGraph = 0;
                            console.groupCollapsed("수익 파이 차트 데이터 (chartdiv)");
                            <?php
                            $i = 0;
                            $pieColor = ["#ffa500","#ff97c7","#ffe474","#ff5edd","#ff1f4d","#fff70d"];
                            if (isset($IncomeArray) && is_array($IncomeArray)) {
                                foreach($IncomeArray as $key => $value) {
                                    $currentValue = (float)$value;
                                    if ($currentValue > 0) {
                                        $colorIndex = $i % count($pieColor);
                                        echo "console.log('  - 항목:', " . json_encode($key) . ", '금액:', " . $currentValue . ");\n";
                                        echo "incomeChartData.push({ income: " . json_encode($key) . ", money: " . $currentValue . ", color: am4core.color('" . $pieColor[$colorIndex] . "') });\n";
                                        echo "totalIncomeFromGraph += " . $currentValue . ";\n";
                                        $i++;
                                    }
                                }
                            }
                            ?>
                            console.log("수익 그래프 데이터 총합:", totalIncomeFromGraph.toLocaleString()); // 총합도 콘솔에 출력
                            console.groupEnd();
                            chart.data = incomeChartData;

                            if (incomeChartData.length > 0) {
                                var series = chart.series.push(new am4charts.PieSeries());
                                series.dataFields.value = "money"; series.dataFields.category = "income";
                                series.slices.template.propertyFields.fill = "color"; series.slices.template.stroke = am4core.color("#fff"); series.slices.template.strokeOpacity = 1;
                                series.labels.template.disabled = true; series.ticks.template.disabled = true; series.slices.template.tooltipText = "{category}: {value.value.formatNumber('#,###')} ({value.percent.formatNumber('#.0')}%)";
                                chart.legend = new am4charts.Legend(); chart.legend.position = "right"; chart.innerRadius = am4core.percent(30);
                            } else { document.getElementById("chartdiv").innerHTML = "<div style='text-align:center; padding-top: 100px; color:#999;'>수익 데이터 없음</div>"; }
                        } catch(e) { console.error("수익 파이 차트 오류:", e); document.getElementById("chartdiv").innerHTML = "<div style='text-align:center; padding-top: 100px; color:red;'>차트 오류 발생</div>"; }

                        // --- 차트 2: 비용 파이 차트 ---
                        try {
                            var chart2 = am4core.create("chartdiv2", am4charts.PieChart);
                            chart2.responsive.enabled = true;
                            var expenseChartData = [];
                            var totalExpenseFromGraph = 0;
                            console.groupCollapsed("비용 파이 차트 데이터 (chartdiv2)");
                            <?php
                            if (isset($ExpenseArray) && is_array($ExpenseArray)) {
                                foreach($ExpenseArray as $key => $value) {
                                    $currentValue = (float)$value;
                                    if ($currentValue > 0 ){
                                        echo "console.log('  - 항목:', " . json_encode($key) . ", '금액:', " . $currentValue . ");\n";
                                        echo "expenseChartData.push({ expense: " . json_encode($key) . ", money: " . $currentValue . " });\n";
                                        echo "totalExpenseFromGraph += " . $currentValue . ";\n";
                                    }
                                }
                            }
                            ?>
                            console.log("비용 그래프 데이터 총합:", totalExpenseFromGraph.toLocaleString());
                            console.groupEnd();
                            chart2.data = expenseChartData;

                            if (expenseChartData.length > 0) {
                                var series2 = chart2.series.push(new am4charts.PieSeries());
                                series2.dataFields.value = "money"; series2.dataFields.category = "expense";
                                series2.slices.template.stroke = am4core.color("#fff"); series2.slices.template.strokeOpacity = 1;
                                series2.labels.template.disabled = true; series2.ticks.template.disabled = true; series2.slices.template.tooltipText = "{category}: {value.value.formatNumber('#,###')} ({value.percent.formatNumber('#.0')}%)";
                                chart2.legend = new am4charts.Legend(); chart2.legend.position = "right"; chart2.innerRadius = am4core.percent(30);
                            } else { document.getElementById("chartdiv2").innerHTML = "<div style='text-align:center; padding-top: 100px; color:#999;'>비용 데이터 없음</div>"; }
                        } catch(e) { console.error("비용 파이 차트 오류:", e); document.getElementById("chartdiv2").innerHTML = "<div style='text-align:center; padding-top: 100px; color:red;'>차트 오류 발생</div>"; }

                        // --- 차트 3: 월별 손익 막대 그래프 ---
                        try {
                            var chart3 = am4core.create("chartdiv3", am4charts.XYChart);
                            chart3.responsive.enabled = true;
                            var monthlyChartData = []; // JavaScript 에서 사용할 배열
                            var totalMonthlyIncome = 0;
                            var totalMonthlyExpense = 0;
                            console.groupCollapsed("월별 손익 막대/꺽은선 데이터 (chartdiv3, chartdiv4)");
                            <?php
                            // PHP 에서 생성한 월별 데이터를 JavaScript 배열로 변환
                            if (isset($incomeMonthMoney) && is_array($incomeMonthMoney)) {
                                $monthlyDataTemp = [];
                                foreach($incomeMonthMoney as $key => $value) {
                                    $monthNumStr = substr($key, 5, 2);
                                    $monthLabel = preg_replace('/^0/', '', $monthNumStr) . "월";
                                    $incomeVal = (float)$value;
                                    $expenseVal = (float)($expenseMonthMoney[$key] ?? 0);
                                    $monthlyDataTemp[$monthNumStr] = "{ month: " . json_encode($monthLabel) . ", income: " . $incomeVal . ", expense: " . $expenseVal . " }";
                                    echo "console.log('  - 월:', " . json_encode($monthLabel) . ", '수익:', " . $incomeVal . ", '비용:', " . $expenseVal . ");\n";
                                    echo "totalMonthlyIncome += " . $incomeVal . ";\n";
                                    echo "totalMonthlyExpense += " . $expenseVal . ";\n";
                                }
                                ksort($monthlyDataTemp);
                                foreach ($monthlyDataTemp as $dataStr) {
                                    echo "monthlyChartData.push(" . $dataStr . ");\n";
                                }
                            }
                            ?>
                            console.log("월별 그래프 수익 총합:", totalMonthlyIncome.toLocaleString());
                            console.log("월별 그래프 비용 총합:", totalMonthlyExpense.toLocaleString());
                            console.groupEnd();
                            chart3.data = monthlyChartData;

                            if (monthlyChartData.length > 0) {
                                var categoryAxis3 = chart3.xAxes.push(new am4charts.CategoryAxis());
                                categoryAxis3.dataFields.category = "month"; categoryAxis3.title.text = "월"; categoryAxis3.renderer.grid.template.location = 0; categoryAxis3.renderer.minGridDistance = 30;
                                var valueAxis3 = chart3.yAxes.push(new am4charts.ValueAxis()); valueAxis3.title.text = "금액"; valueAxis3.numberFormatter.numberFormat = "#,###";
                                var series3 = chart3.series.push(new am4charts.ColumnSeries()); series3.dataFields.valueY = "income"; series3.dataFields.categoryX = "month"; series3.name = "수익"; series3.columns.template.fill = am4core.color("#ffb74d"); series3.columns.template.stroke = series3.columns.template.fill; series3.columns.template.tooltipText = "{name} ({categoryX}): {valueY.formatNumber('#,###')}";
                                var series4 = chart3.series.push(new am4charts.ColumnSeries()); series4.dataFields.valueY = "expense"; series4.dataFields.categoryX = "month"; series4.name = "비용"; series4.columns.template.fill = am4core.color("#64b5f6"); series4.columns.template.stroke = series4.columns.template.fill; series4.columns.template.tooltipText = "{name} ({categoryX}): {valueY.formatNumber('#,###')}";
                                var labelBullet = series3.bullets.push(new am4charts.LabelBullet()); labelBullet.label.text = "{valueY.value.formatNumber('#,###')}"; labelBullet.strokeOpacity = 0; labelBullet.dy = -10; labelBullet.label.fontSize = 9;
                                var labelBullet2 = series4.bullets.push(new am4charts.LabelBullet()); labelBullet2.label.text = "{valueY.value.formatNumber('#,###')}"; labelBullet2.strokeOpacity = 0; labelBullet2.dy = -10; labelBullet2.label.fontSize = 9;
                                chart3.legend = new am4charts.Legend(); chart3.cursor = new am4charts.XYCursor();
                            } else { document.getElementById("chartdiv3").innerHTML = "<div style='text-align:center; padding-top: 100px; color:#999;'>월별 데이터 없음</div>"; }
                        } catch(e) { console.error("월별 막대 차트 오류:", e); document.getElementById("chartdiv3").innerHTML = "<div style='text-align:center; padding-top: 100px; color:red;'>차트 오류 발생</div>"; }

                        // --- 차트 4: 월별 손익 꺽은선 그래프 ---
                        try {
                            var chart4 = am4core.create("chartdiv4", am4charts.XYChart);
                            chart4.responsive.enabled = true;
                            chart4.data = monthlyChartData; // 차트 3의 데이터 재사용

                            if (monthlyChartData.length > 0) {
                                var categoryAxis4 = chart4.xAxes.push(new am4charts.CategoryAxis()); categoryAxis4.dataFields.category = "month"; categoryAxis4.title.text = "월"; categoryAxis4.renderer.grid.template.location = 0; categoryAxis4.renderer.minGridDistance = 30;
                                var valueAxis4 = chart4.yAxes.push(new am4charts.ValueAxis()); valueAxis4.title.text = "금액"; valueAxis4.numberFormatter.numberFormat = "#,###";
                                var series5 = chart4.series.push(new am4charts.LineSeries()); series5.dataFields.valueY = "income"; series5.dataFields.categoryX = "month"; series5.name = "수익"; series5.stroke = am4core.color("#ffb74d"); series5.strokeWidth = 3; series5.tooltipText = "{name} ({categoryX}): {valueY.formatNumber('#,###')}"; series5.tensionX = 0.8;
                                var series6 = chart4.series.push(new am4charts.LineSeries()); series6.dataFields.valueY = "expense"; series6.dataFields.categoryX = "month"; series6.name = "비용"; series6.stroke = am4core.color("#64b5f6"); series6.strokeWidth = 3; series6.tooltipText = "{name} ({categoryX}): {valueY.formatNumber('#,###')}"; series6.tensionX = 0.8;
                                var labelBullet3 = series5.bullets.push(new am4charts.LabelBullet()); labelBullet3.label.text = "{valueY.value.formatNumber('#,###')}"; labelBullet3.strokeOpacity = 0; labelBullet3.dy = -10; labelBullet3.label.fontSize = 9;
                                var labelBullet4 = series6.bullets.push(new am4charts.LabelBullet()); labelBullet4.label.text = "{valueY.value.formatNumber('#,###')}"; labelBullet4.strokeOpacity = 0; labelBullet4.dy = 10; labelBullet4.label.fontSize = 9;
                                var bullet5 = series5.bullets.push(new am4charts.CircleBullet()); bullet5.circle.radius = 4; bullet5.circle.fill=series5.stroke; bullet5.circle.strokeWidth=1; bullet5.circle.stroke=am4core.color("#fff");
                                var bullet6 = series6.bullets.push(new am4charts.CircleBullet()); bullet6.circle.radius = 4; bullet6.circle.fill=series6.stroke; bullet6.circle.strokeWidth=1; bullet6.circle.stroke=am4core.color("#fff");
                                chart4.legend = new am4charts.Legend(); chart4.cursor = new am4charts.XYCursor();
                            } else { document.getElementById("chartdiv4").innerHTML = "<div style='text-align:center; padding-top: 100px; color:#999;'>월별 데이터 없음</div>"; }
                        } catch(e) { console.error("월별 꺽은선 차트 오류:", e); document.getElementById("chartdiv4").innerHTML = "<div style='text-align:center; padding-top: 100px; color:red;'>차트 오류 발생</div>"; }

                        // --- 차트 5: 분기별 손익 꺽은선 그래프 ---
                        try {
                            var chart5 = am4core.create("chartdiv5", am4charts.XYChart);
                            chart5.responsive.enabled = true;
                            var quarterlyChartData = [];
                            var totalQuarterlyIncome = 0;
                            var totalQuarterlyExpense = 0;
                            console.groupCollapsed("분기별 손익 꺽은선 데이터 (chartdiv5)");
                            <?php
                            if (isset($IncomeQuarter) && is_array($IncomeQuarter)) {
                                foreach($IncomeQuarter as $key => $value) {
                                    $quarterLabel = ($key + 1) . "분기";
                                    $incomeVal = (float)$value;
                                    $expenseVal = (float)($ExpenseQuarter[$key] ?? 0);
                                    echo "console.log('  - 분기:', " . json_encode($quarterLabel) . ", '수익:', " . $incomeVal . ", '비용:', " . $expenseVal . ");\n";
                                    echo "quarterlyChartData.push({ quarter: " . json_encode($quarterLabel) . ", income: " . $incomeVal . ", expense: " . $expenseVal . " });\n";
                                    echo "totalQuarterlyIncome += " . $incomeVal . ";\n";
                                    echo "totalQuarterlyExpense += " . $expenseVal . ";\n";
                                }
                            }
                            ?>
                            console.log("분기별 그래프 수익 총합:", totalQuarterlyIncome.toLocaleString());
                            console.log("분기별 그래프 비용 총합:", totalQuarterlyExpense.toLocaleString());
                            console.groupEnd();
                            chart5.data = quarterlyChartData;

                            if (quarterlyChartData.length > 0) {
                                var categoryAxis5 = chart5.xAxes.push(new am4charts.CategoryAxis()); categoryAxis5.dataFields.category = "quarter"; categoryAxis5.title.text = "분기"; categoryAxis5.renderer.grid.template.location = 0; categoryAxis5.renderer.minGridDistance = 30;
                                var valueAxis5 = chart5.yAxes.push(new am4charts.ValueAxis()); valueAxis5.title.text = "금액"; valueAxis5.numberFormatter.numberFormat = "#,###";
                                var series7 = chart5.series.push(new am4charts.LineSeries()); series7.dataFields.valueY = "income"; series7.dataFields.categoryX = "quarter"; series7.name = "수익"; series7.stroke = am4core.color("#ffb74d"); series7.strokeWidth = 3; series7.tooltipText = "{name} ({categoryX}): {valueY.formatNumber('#,###')}"; series7.tensionX = 0.8;
                                var series8 = chart5.series.push(new am4charts.LineSeries()); series8.dataFields.valueY = "expense"; series8.dataFields.categoryX = "quarter"; series8.name = "비용"; series8.stroke = am4core.color("#64b5f6"); series8.strokeWidth = 3; series8.tooltipText = "{name} ({categoryX}): {valueY.formatNumber('#,###')}"; series8.tensionX = 0.8;
                                var labelBullet7 = series7.bullets.push(new am4charts.LabelBullet()); labelBullet7.label.text = "{valueY.value.formatNumber('#,###')}"; labelBullet7.strokeOpacity = 0; labelBullet7.dy = -10; labelBullet7.label.fontSize = 9;
                                var labelBullet8 = series8.bullets.push(new am4charts.LabelBullet()); labelBullet8.label.text = "{valueY.value.formatNumber('#,###')}"; labelBullet8.strokeOpacity = 0; labelBullet8.dy = 10; labelBullet8.label.fontSize = 9;
                                var bullet7 = series7.bullets.push(new am4charts.CircleBullet()); bullet7.circle.radius = 4; bullet7.circle.fill=series7.stroke; bullet7.circle.strokeWidth=1; bullet7.circle.stroke=am4core.color("#fff");
                                var bullet8 = series8.bullets.push(new am4charts.CircleBullet()); bullet8.circle.radius = 4; bullet8.circle.fill=series8.stroke; bullet8.circle.strokeWidth=1; bullet8.circle.stroke=am4core.color("#fff");
                                chart5.legend = new am4charts.Legend(); chart5.cursor = new am4charts.XYCursor();
                            } else { document.getElementById("chartdiv5").innerHTML = "<div style='text-align:center; padding-top: 100px; color:#999;'>분기별 데이터 없음</div>"; }
                        } catch(e) { console.error("분기별 꺽은선 차트 오류:", e); document.getElementById("chartdiv5").innerHTML = "<div style='text-align:center; padding-top: 100px; color:red;'>차트 오류 발생</div>"; }

                    }); // end am4core.ready()
                </script>

            <?php endif; // End of if ($PrintState != "1") ?>

            <div id="printSection2" style="display: none;">
                <?php
                if (($Search_sw ?? '2') == "2" && !empty($SearchStartMonth)) {
                    $loopYear = $SearchStartYear ?? date('Y');
                    $loopMonth = $SearchStartMonth;
                    $originalSearchStartYear = $SearchStartYear; // 루프 전 원래 값 저장
                    $originalSearchStartMonth = $SearchStartMonth;

                    for ($i = 1; $i <= 3; $i++) {
                        $prevTimestamp = strtotime($loopYear . "-" . str_pad($loopMonth, 2, '0', STR_PAD_LEFT) . "-01 -1 month");
                        $prevYear = date('Y', $prevTimestamp);
                        $prevMonth = date('n', $prevTimestamp);

                        // 전역 변수 임시 변경
                        $SearchStartYear = $prevYear;
                        $SearchStartMonth = $prevMonth;
                        try {
                            $prevSql = makeIncomeStateSql();
                            printIncome($prevSql, "none", false, 100, 'income_prev_' . $i);
                        } catch (Exception $e) { error_log("이전 달 손익계산서 생성 오류 ($prevYear-$prevMonth): " . $e->getMessage()); }

                        // 루프 변수 업데이트
                        $loopYear = $prevYear;
                        $loopMonth = $prevMonth;
                    }
                    // 루프 종료 후 원래 값 복원
                    $SearchStartYear = $originalSearchStartYear;
                    $SearchStartMonth = $originalSearchStartMonth;
                }
                ?>

