<?php
// lms/account_income_statement.php (수정 완료 - 콘솔 로그 추가)

include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./inc_header.php');
include_once('./inc_common_list_css.php');
include "./inc_income_query.php"; // 수정된 inc_income_query.php 포함

// 자바스크립트로 그래프를 그려주기 위해서 데이타를 배열로 저장한다.
$IncomeArray = array();
$ExpenseArray = array();

// --- 손익계산서 데이터 조회 ---
try {
    $Sql = makeIncomeStateSql();
} catch (Exception $e) {
    error_log("makeIncomeStateSql 함수 실행 오류: " . $e->getMessage());
    $Sql = null; // 오류 시 $Sql 을 null 로 설정
}
$FirstSearchDate = $SearchDate ?? ($StartDate ?: date("Y-m-d")); // 초기 검색 날짜 저장

// --- 필터링 SQL 변수 (inc_income_query.php 에서 설정된 값 사용 시 필요) ---
// $companyFilterSql = $GLOBALS['companyFilterSql'] ?? ''; // 예시
// $accountFilterSql = $GLOBALS['accountFilterSql'] ?? ''; // 예시
// *** 임시 조치: 아래 $monthlySql 에서 필터링 제거 ***
$companyFilterSql = '';
$accountFilterSql = '';


/**
 * 손익계산서 HTML 테이블 출력 함수
 * (이전 답변의 수정 내용 유지)
 */
function printIncome($sqlDataQuery, $display="block", $printTitle=true, $width=95, $id='income'){
    global $DbConn, $_SERVER, $Search_sw, $SearchStartYear, $SearchStartMonth, $SelectedAccount, $direction, $direction2, $SelectedCompany, $StartDate, $EndDate;
    global $PrintState;
    global $IncomeArray, $ExpenseArray;

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
        'Search_sw' => $currentSearchSw,
        'SearchStartYear' => $currentYear,
        'SearchStartMonth' => $currentMonth,
        'StartDate' => $currentStartDate,
        'EndDate' => $currentEndDate,
        'SelectedAccount' => $currentSelectedAccount,
        'SelectedCompany' => $currentSelectedCompany
    ]);
    $linkOrderByID = $_SERVER['PHP_SELF'] . "?" . $baseLinkParams . "&OrderBy=AccBookConfigID&direction=" . ($currentDirection == "asc" ? "desc" : "asc");
    $linkOrderByMoney = $_SERVER['PHP_SELF'] . "?" . $baseLinkParams . "&OrderBy=AccBookMoney&direction2=" . ($currentDirection2 == "asc" ? "desc" : "asc");

    // 기간 제목 생성
    $periodTitle = "";
    if ($currentSearchSw == '1') {
        $periodTitle = $currentYear . "년";
    } elseif ($currentSearchSw == '2' && !empty($currentMonth)) {
        $periodTitle = $currentYear . "년 " . $currentMonth . "월";
    } elseif ($currentSearchSw == '3' && !empty($currentStartDate) && !empty($currentEndDate)) {
        $periodTitle = $currentStartDate . " ~ " . $currentEndDate;
    } else {
        $periodTitle = $currentYear . "년"; // 기본
    }

    // 테이블 시작
    echo "<div id='" . htmlspecialchars($id) . "' class='uk-overflow-container' style='width:" . intval($width) . "%;display:" . htmlspecialchars($display) . ";float:left;'>
            <table id='excelTable_" . htmlspecialchars($id) . "' class='uk-table uk-table-align-vertical sticky-table' border='1' style='width:100%;'>
                <thead>
                    <tr>";
    if ($printTitle) {
        echo "    <th class='pin' nowrap style='width:15%;height:25px; text-align:center;'>계정구분</th>
                        <th nowrap style='width:40%;height:25px; text-align:center;'><a href='" . htmlspecialchars($linkOrderByID) . "'><div class='uk-flex uk-flex-middle uk-flex-center'><div>과목명</div><div class='sort-arrow uk-flex uk-flex-column' style='margin-left:5px'><div style='height:7px'><i class='uk-icon-hover uk-icon-sort-asc'></i></div><div style='height:22px'><i class='uk-icon-sort-desc'></i></div></div></div></a></th>";
    }
    // 금액 헤더 (원본 스타일 참조하여 수정)
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
            $Stmt->execute(); // 원본 방식 유지 (바인딩 필요 시 수정)
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
    $Imsi_Cnt = 0;

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
                if ($id === 'income1' && $SumOfMoney != 0) {
                    // 배열 키 존재 여부 확인 후 할당 (중복 방지 - 필요 시)
                    // if (!isset($IncomeArray[$AccBookConfigName])) {
                    $IncomeArray[$AccBookConfigName] = $SumOfMoney;
                    // } else {
                    //     $IncomeArray[$AccBookConfigName] += $SumOfMoney; // 중복 시 합산?
                    // }
                }
            } else {
                $Total_Money2 += $SumOfMoney;
                if ($id === 'income1' && $SumOfMoney != 0) {
                    // if (!isset($ExpenseArray[$AccBookConfigName])) {
                    $ExpenseArray[$AccBookConfigName] = $SumOfMoney;
                    // } else {
                    //     $ExpenseArray[$AccBookConfigName] += $SumOfMoney;
                    // }
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
                // 계정구분 출력 (rowspan 적용)
                if ($currentSubType !== $AccBookConfigSubType) {
                    $currentSubType = $AccBookConfigSubType;
                    $rowspan = isset($subTypeCounts[$currentSubType]) ? $subTypeCounts[$currentSubType] : 1;
                    echo "<td class='gubun pin' ".($rowspan > 0 ? "rowspan='".$rowspan."'" : "") ." style='font-size:14px;text-align:center; vertical-align: middle; color:".$fontColor.";'>".htmlspecialchars($AccBookConfigSubTypeName)."</td>";
                }

                // 과목명 출력
                echo "<td style='text-align:center; color:".$fontColor.";'>"; // 원본: text-align:center
                if (($currentPrintState ?? '0') != "1") {
                    echo "<a href='javascript:OpenAccountList(".intval($AccBookConfigID).");' style='color:".$fontColor.";'>".htmlspecialchars($AccBookConfigName ?? '-')."</a>";
                } else {
                    echo htmlspecialchars($AccBookConfigName ?? '-');
                }
                echo "</td>";
            }

            // 금액 출력
            echo "<td style='text-align:right; color:".($AccBookConfigType==1?"#0057ae":"#c40000")."; font-weight:bold;'>".number_format($SumOfMoney)."</td>
                </tr>";
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
        $('input[name="Search_sw"]').val(searchType);

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

    function OpenAccountList(configId) {
        var selectedAccountsVal = $('#AccountCombo').val();
        var selectedAccountParam = selectedAccountsVal ? encodeURIComponent(selectedAccountsVal.join(',')) : '';
        var searchSw = <?= json_encode($Search_sw ?? '2') ?>;
        var searchYear = <?= json_encode($SearchStartYear ?? '') ?>;
        var searchMonth = <?= json_encode($SearchStartMonth ?? '') ?>;
        var startDate = <?= json_encode($StartDate ?? '') ?>;
        var endDate = <?= json_encode($EndDate ?? '') ?>;
        var selectedCompany = <?= json_encode($SelectedCompany ?? '') ?>;
        var url = "account_details.php?AccBookConfigID=" + configId + "&Search_sw=" + searchSw + "&SearchStartYear=" + searchYear + "&SearchStartMonth=" + searchMonth + "&StartDate=" + startDate + "&EndDate=" + endDate + "&SelectedCompany=" + selectedCompany + "&SelectedAccount=" + selectedAccountParam;
        alert("상세 내역 보기 기능 구현 필요 (Config ID: " + configId + ")");
        // window.open(url, "AccountDetails", "width=800,height=600,scrollbars=yes,resizable=yes");
    }

    function printIt(printThis) { window.print(); }
    function OpenPrint() { alert("엑셀 다운로드 기능 구현 필요"); }

    function CommentWrite(){
        var Comment = $('#IncomeStatementComment').val();
        var YearMonth = <?= json_encode((!empty($SearchStartYear) && !empty($SearchStartMonth)) ? $SearchStartYear . str_pad($SearchStartMonth, 2, '0', STR_PAD_LEFT) : '') ?>;
        var SelectedCompanyValue = <?= json_encode($SelectedCompany ?? "2") ?>;
        if (!YearMonth) { alert('코멘트를 저장할 년월 정보가 올바르지 않습니다.'); return; }
        $.ajax({
            url: "ajax_set_income_statement_comment.php", method: "POST", data: { Comment: Comment, YearMonth: YearMonth, SelectedCompany: SelectedCompanyValue }, dataType: "json",
            success: function (data) { alert(data && data.success ? '코멘트를 저장했습니다.' : '코멘트 저장에 실패했습니다: ' + (data && data.message ? data.message : '서버 응답 오류')); },
            error: function (jqXHR, textStatus, errorThrown) { alert('코멘트 저장 중 오류가 발생했습니다: ' + textStatus); console.error("CommentWrite AJAX Error:", textStatus, errorThrown, jqXHR.responseText); }
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
                                <div class="uk-width-medium-1-10" style="padding-top:7px;">
                                    <select id="SearchStartYear" name="SearchStartYear" class="uk-width-1-1" data-placeholder="<?= htmlspecialchars($년도선택[$LangID] ?? '년도 선택') ?>" style="height:40px;">
                                        <option value=""><?= htmlspecialchars($년도선택[$LangID] ?? '년도 선택') ?></option>
                                        <?php
                                        $currentSelectedYear = $SearchStartYear ?? date("Y");
                                        for ($iiii = date("Y"); $iiii >= 2019; $iiii--) { echo "<option value=\"$iiii\"" . ($currentSelectedYear == $iiii ? " selected" : "") . ">" . $iiii . " 년</option>"; }
                                        ?>
                                    </select>
                                </div>
                                <div class="uk-width-medium-1-10" style="padding-top:7px; vertical-align:middle;">
                                    <a href="javascript:SearchSubmit(1)" class="md-btn md-btn-primary" style="background-color:#8d73d4;">년도별 조회</a>
                                </div>
                                <div class="uk-width-medium-1-10" style="padding-top:7px;">
                                    <select id="SearchStartMonth" name="SearchStartMonth" class="uk-width-1-1" data-placeholder="<?= htmlspecialchars($월선택[$LangID] ?? '월 선택') ?>" style="height:40px;">
                                        <option value=""><?= htmlspecialchars($월선택[$LangID] ?? '월 선택') ?></option>
                                        <?php
                                        $currentSelectedMonth = $SearchStartMonth ?? '';
                                        for ($iiii = 1; $iiii <= 12; $iiii++) { echo "<option value=\"$iiii\"" . ($currentSelectedMonth == $iiii ? " selected" : "") . ">" . $iiii . " " . ($월월[$LangID] ?? '월') . "</option>"; }
                                        ?>
                                    </select>
                                </div>
                                <div class="uk-width-medium-1-10" style="padding-top:7px; vertical-align:middle;">
                                    <a href="javascript:SearchSubmit(2)" class="md-btn md-btn-primary" style="background-color:#68b7e2;">월별 조회</a>
                                </div>
                                <div class="uk-width-medium-2-10" style="padding-top:7px;">
                                    <input type="text" size=8 id="StartDate" name="StartDate" value="<?= htmlspecialchars($StartDate ?? '') ?>" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}" placeholder="시작일" style="width: calc(50% - 5px);">~
                                    <input type="text" size=8 id="EndDate" name="EndDate" value="<?= htmlspecialchars($EndDate ?? '') ?>" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}" placeholder="종료일" style="width: calc(50% - 5px);">
                                </div>
                                <div class="uk-width-medium-1-10" style="padding-top:7px; vertical-align:middle;">
                                    <a href="javascript:SearchSubmit(3)" class="md-btn md-btn-primary" style="background-color:#39a879;">기간별 조회</a>
                                </div>
                                <div class="uk-width-medium-1-10" style="padding-top:7px; vertical-align:middle;">
                                    <button type="button" onclick="javascript:printIt(document.getElementById('printSection'))" class="md-btn md-btn-primary" style="background-color:#6ac7ac;"><?= htmlspecialchars($인쇄[$LangID] ?? '인쇄') ?></button>
                                </div>
                                <div class="uk-width-medium-1-10" style="padding-top:7px; vertical-align:middle;">
                                    <a href="javascript:OpenPrint();" class="md-btn md-btn-primary" style="background-color:#eccc5f; color:#fff;" download=""><?= htmlspecialchars($엑셀_다운로드[$LangID] ?? '엑셀') ?></a>
                                </div>
                            </div>
                            <div class="uk-grid uk-margin-top" data-uk-grid-margin>
                                <div class="uk-width-medium-6-10" style="padding-top:7px; vertical-align:middle;">
                                    <label for="AccountCombo" style="margin-right: 10px;">계좌/카드 선택</label>
                                    <select id="AccountCombo" class="js-example-basic-multiple js-states form-control" name="states[]" multiple="multiple" style="width: 75%;">
                                        <?php
                                        $accountListSql = "SELECT AccountNumber, AccountName FROM AccountState ";
                                        $accParams = [];
                                        if (($SelectedCompany ?? '') !== "") {
                                            $accName1 = ($SelectedCompany == "0") ? "신한은행" : "국민은행";
                                            $accName2 = ($SelectedCompany == "0") ? "신한카드" : "KB카드";
                                            $accountListSql .= " WHERE AccountName = :name1 OR AccountName = :name2 ";
                                            $accParams[':name1'] = $accName1;
                                            $accParams[':name2'] = $accName2;
                                        }
                                        $accountListSql .= " ORDER BY AccountName, AccountNumber";
                                        try {
                                            $stmtAcc = $DbConn->prepare($accountListSql);
                                            $stmtAcc->execute($accParams);
                                            $accountRows = $stmtAcc->fetchAll(PDO::FETCH_ASSOC);
                                            $currentSelectedAccounts = !empty($SelectedAccount) ? explode(",", $SelectedAccount) : [];
                                            foreach($accountRows as $accRow) {
                                                $accNum = $accRow["AccountNumber"];
                                                $accDesc = htmlspecialchars($accRow["AccountName"] . ' - ' . $accNum);
                                                $isSelected = in_array($accNum, $currentSelectedAccounts);
                                                echo "<option value=\"" . htmlspecialchars($accNum) . "\"" . ($isSelected ? " selected" : "") . ">" . $accDesc . "</option>";
                                            }
                                        } catch (PDOException $e) { error_log("계좌/카드 목록 조회 오류: " . $e->getMessage()); echo "<option value=''>오류</option>"; }
                                        ?>
                                    </select>
                                </div>
                                <div class="uk-width-medium-4-10 uk-text-right" style="padding-top:7px; vertical-align:middle;">
                                    <?php
                                    $linkParamsCompany = $_GET; unset($linkParamsCompany['SelectedCompany'], $linkParamsCompany['states']);
                                    $linkBaseCompany = htmlspecialchars($_SERVER['PHP_SELF'] . '?' . http_build_query($linkParamsCompany));
                                    $currentCompany = $SelectedCompany ?? '';
                                    ?>
                                    <a href="<?= $linkBaseCompany ?>" class="md-btn <?= ($currentCompany == '') ? 'md-btn-primary' : 'md-btn-default' ?>" style="<?= ($currentCompany == '') ? 'background-color:#e27450; color:#fff;' : '' ?>">전체 <?= htmlspecialchars($손익계산서[$LangID] ?? '') ?></a>
                                    <a href="<?= $linkBaseCompany . '&SelectedCompany=0' ?>" class="md-btn <?= ($currentCompany === '0') ? 'md-btn-primary' : 'md-btn-default' ?>" style="<?= ($currentCompany === '0') ? 'background-color:#e27450; color:#fff;' : '' ?>">MangoI <?= htmlspecialchars($손익계산서[$LangID] ?? '') ?></a>
                                    <a href="<?= $linkBaseCompany . '&SelectedCompany=1' ?>" class="md-btn <?= ($currentCompany === '1') ? 'md-btn-primary' : 'md-btn-default' ?>" style="<?= ($currentCompany === '1') ? 'background-color:#e27450; color:#fff;' : '' ?>">SLP <?= htmlspecialchars($손익계산서[$LangID] ?? '') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; // End of if ($PrintState != "1") ?>

            <div class="md-card" style="margin-bottom:10px;">
                <div id="printSection" class="md-card-content">
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-1-1">
                            <?php
                            // 보고서 제목 설정
                            $reportTitle = "";
                            $reportYear = $SearchStartYear ?? date('Y');
                            $reportMonth = $SearchStartMonth ?? '';
                            $reportStartDate = $StartDate ?? '';
                            $reportEndDate = $EndDate ?? '';
                            $reportTitleString = $TitleString ?? '';
                            $reportSearchSw = $Search_sw ?? '2';

                            if ($reportSearchSw == '1') { $reportTitle = $reportYear . "년 손익계산서"; }
                            elseif ($reportSearchSw == '2' && !empty($reportMonth)) { $reportTitle = $reportYear . "년 " . $reportMonth . "월 손익계산서"; }
                            elseif ($reportSearchSw == '3' && !empty($reportStartDate) && !empty($reportEndDate)) { $reportTitle = $reportStartDate . " ~ " . $reportEndDate . " 기간 손익계산서"; }
                            else { $reportTitle = $reportYear . "년 손익계산서"; }
                            $reportTitle .= " " . htmlspecialchars($reportTitleString);
                            ?>
                            <div style="text-align:center;margin-top:15px;margin-bottom:15px;"><h3><?= $reportTitle ?></h3></div>
                        </div>

                        <div class="uk-width-1-1">
                            <?php
                            // 메인 손익계산서 출력
                            if (isset($Sql) && $Sql !== null) {
                                printIncome($Sql, "block", true, 95, 'income1'); // 너비 원본 95% 유지
                            } else {
                                echo "<p style='text-align:center; color:red;'>손익계산서 데이터를 표시할 수 없습니다 (SQL 생성 오류). 관리자에게 문의하세요.</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="md-card-content">
                    <?php
                    if (($Search_sw ?? '') == "2" && !empty($SearchStartMonth) && !empty($SearchStartYear)) {
                        $commentSelectedCompany = $SelectedCompany ?? "2";
                        $commentYearMonth = $SearchStartYear . str_pad($SearchStartMonth, 2, '0', STR_PAD_LEFT);
                        $comment = "";
                        try {
                            $CommentSql = "SELECT Comment FROM IncomeStatementComment WHERE YearMonth = :yearMonth AND Company = :company";
                            $StmtComment = $DbConn->prepare($CommentSql);
                            $StmtComment->bindParam(':yearMonth', $commentYearMonth, PDO::PARAM_STR);
                            $StmtComment->bindParam(':company', $commentSelectedCompany, PDO::PARAM_STR);
                            $StmtComment->execute();
                            $RowComment = $StmtComment->fetch(PDO::FETCH_ASSOC);
                            if ($RowComment) $comment = $RowComment["Comment"];
                        } catch (PDOException $e) { error_log("코멘트 조회 오류 ($commentYearMonth): " . $e->getMessage()); }
                        ?>
                        <form name='CommentForm' style="width:96%">
                            <div class="uk-form-row"><label for="IncomeStatementComment">코멘트</label><textarea id="IncomeStatementComment" name="IncomeStatementComment" class="md-input" cols="30" rows="4"><?= htmlspecialchars($comment) ?></textarea></div>
                            <div class="uk-form-row uk-text-right"><a href="javascript:CommentWrite()" class="md-btn md-btn-primary" style="background-color:#68b7e2;">저장</a></div>
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </div>


            <?php if (($PrintState ?? '0') != "1"): ?>
                <?php
                // --- 월별/분기별 데이터 조회 (그래프용) ---
                $currentGraphYear = $SearchStartYear ?? date('Y');
                $incomeMonthMoney = array_fill_keys(array_map(function($m) use ($currentGraphYear) { return $currentGraphYear . '-' . str_pad($m, 2, '0', STR_PAD_LEFT); }, range(1, 12)), 0);
                $expenseMonthMoney = array_fill_keys(array_map(function($m) use ($currentGraphYear) { return $currentGraphYear . '-' . str_pad($m, 2, '0', STR_PAD_LEFT); }, range(1, 12)), 0);

                try {
                    $sqlIncomeMonthly = "SELECT sum(AccBookMoney) AS SumOfMoney, DATE_FORMAT(AccBookDate,'%Y-%m') m FROM account_book WHERE YEAR(AccBookDate) = :year AND AccBookType = 1 GROUP BY m";
                    $stmtIncomeMonthly = $DbConn->prepare($sqlIncomeMonthly);
                    $stmtIncomeMonthly->bindParam(':year', $currentGraphYear, PDO::PARAM_INT);
                    $stmtIncomeMonthly->execute();
                    while($Row = $stmtIncomeMonthly->fetch(PDO::FETCH_ASSOC)){ if(isset($incomeMonthMoney[$Row["m"]])) { $incomeMonthMoney[$Row["m"]] = (float)$Row["SumOfMoney"]; } }
                    $stmtIncomeMonthly = null;

                    $sqlExpenseMonthly = "SELECT sum(AccBookMoney) AS SumOfMoney, DATE_FORMAT(AccBookDate,'%Y-%m') m FROM account_book WHERE YEAR(AccBookDate) = :year AND AccBookType = 2 GROUP BY m";
                    $stmtExpenseMonthly = $DbConn->prepare($sqlExpenseMonthly);
                    $stmtExpenseMonthly->bindParam(':year', $currentGraphYear, PDO::PARAM_INT);
                    $stmtExpenseMonthly->execute();
                    while($Row = $stmtExpenseMonthly->fetch(PDO::FETCH_ASSOC)){ if(isset($expenseMonthMoney[$Row["m"]])) { $expenseMonthMoney[$Row["m"]] = (float)$Row["SumOfMoney"]; } }
                    $stmtExpenseMonthly = null;
                } catch (PDOException $e) { error_log("월별 그래프 데이터 조회 오류: " . $e->getMessage()); }

                // 분기별 데이터 계산
                $IncomeQuarter = array(0.0, 0.0, 0.0, 0.0);
                $ExpenseQuarter = array(0.0, 0.0, 0.0, 0.0);
                for($q_m = 1; $q_m <= 12; $q_m++) {
                    $q_key = $currentGraphYear . '-' . str_pad($q_m, 2, '0', STR_PAD_LEFT);
                    $q_index = floor(($q_m - 1) / 3);
                    if (isset($incomeMonthMoney[$q_key])) $IncomeQuarter[$q_index] += $incomeMonthMoney[$q_key];
                    if (isset($expenseMonthMoney[$q_key])) $ExpenseQuarter[$q_index] += $expenseMonthMoney[$q_key];
                }

                // --- 수강생 현황 데이터 조회 ---
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
                        $studentStatusData[$i]['attendance'] = $currentAttendance; // attendance 값 추가
                    }
                } catch (PDOException $e) { error_log("수강생 현황 데이터 조회 오류: " . $e->getMessage()); }
                ?>

                <div class="md-card" style="margin-bottom:10px;">
                    <div class="md-card-content">
                        <div class="uk-grid" data-uk-grid-margin>
                            <div class="uk-width-medium-1-2 uk-width-small-1-1">
                                <h3 style="text-align:center">수익 그래프</h3>
                                <div id="chartdiv" class="uk-width-1-1" style="height:550px"></div>
                            </div>
                            <div class="uk-width-medium-1-2 uk-width-small-1-1">
                                <h3 style="text-align:center">비용 그래프</h3>
                                <div id="chartdiv2" class="uk-width-1-1" style="height:550px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="md-card-content">
                        <div class="uk-grid" data-uk-grid-margin>
                            <div class="uk-width-1-1">
                                <h2 style="text-align:center">월별 손익 그래프 (<?= htmlspecialchars($currentGraphYear) ?>년)</h2>
                                <div id="chartdiv3" style="width: 100%; height: 700px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="md-card-content">
                        <div class="uk-grid" data-uk-grid-margin>
                            <div class="uk-width-1-1">
                                <h2 style="text-align:center">월별 손익 꺽은선 그래프 (<?= htmlspecialchars($currentGraphYear) ?>년)</h2>
                                <div id="chartdiv4" style="width: 100%; height: 700px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="md-card-content">
                        <div class="uk-grid" data-uk-grid-margin>
                            <div class="uk-width-1-1">
                                <h2 style="text-align:center">분기별 손익 꺽은선 그래프 (<?= htmlspecialchars($currentGraphYear) ?>년)</h2>
                                <div id="chartdiv5" style="width: 100%; height: 700px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="md-card-content">
                        <div class="uk-grid" data-uk-grid-margin>
                            <div class="uk-width-1-1">
                                <h2 style="text-align:center">수강생 현황 (<?= htmlspecialchars($studentStatusYear) ?>년)</h2>
                                <div class="uk-overflow-container">
                                    <table class="uk-table uk-table-condensed uk-table-striped uk-text-center">
                                        <thead>
                                        <tr>
                                            <th class="uk-text-center" style="width: 8%;">구분</th>
                                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                                <th class="uk-text-center" style="width: 7.6%;"><?= $i ?>월</th>
                                            <?php endfor; ?>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>신규생</td>
                                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                                <td style="background-color:#fde0e0;"><?= number_format($studentStatusData[$i]['newStudents'] ?? 0) ?></td>
                                            <?php endfor; ?>
                                        </tr>
                                        <tr>
                                            <td>탈락생</td>
                                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                                <td style="background-color:#e0f7fa;"><?= number_format($studentStatusData[$i]['dropoutStudents'] ?? 0) ?></td>
                                            <?php endfor; ?>
                                        </tr>
                                        <tr>
                                            <td>수강생</td>
                                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                                <td><?= number_format($studentStatusData[$i]['attendance'] ?? 0) ?></td>
                                            <?php endfor; ?>
                                        </tr>
                                        <tr>
                                            <td>증가비율(%)</td>
                                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                                <td style="background-color:#fde0e0;"><?= $studentStatusData[$i]['increaseRate'] ?? 0 ?>%</td>
                                            <?php endfor; ?>
                                        </tr>
                                        <tr>
                                            <td>감소비율(%)</td>
                                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                                <td style="background-color:#e0f7fa;"><?= $studentStatusData[$i]['reductionRate'] ?? 0 ?>%</td>
                                            <?php endfor; ?>
                                        </tr>
                                        <tr>
                                            <td>증감율(%)</td>
                                            <?php for ($i = 1; $i <= 12; $i++) :
                                                $rate = $studentStatusData[$i]['changeRate'] ?? 0; ?>
                                                <td style="<?= $rate >= 0 ? 'background-color:#e8f5e9;' : 'background-color:#ffebee;' ?>">
                                                    <?= $rate > 0 ? '+' : '' ?><?= $rate ?>%
                                                </td>
                                            <?php endfor; ?>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
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
                            var totalIncomeFromGraph = 0; // 그래프 데이터 총합 계산용
                            console.groupCollapsed("수익 파이 차트 데이터 (chartdiv)"); // 콘솔 그룹 시작 (펼쳐보기 가능)
                            <?php
                            $i = 0;
                            $pieColor = ["#ffa500","#ff97c7","#ffe474","#ff5edd","#ff1f4d","#fff70d"]; // 원본 색상
                            if (isset($IncomeArray) && is_array($IncomeArray)) {
                                foreach($IncomeArray as $key => $value) {
                                    $currentValue = (float)$value; // 숫자형 변환
                                    if ($currentValue > 0) {
                                        $colorIndex = $i % count($pieColor);
                                        // PHP에서 바로 JavaScript 배열 데이터 생성 및 콘솔 로그
                                        echo "console.log('  - 항목:', " . json_encode($key) . ", '금액:', " . $currentValue . ");\n";
                                        echo "incomeChartData.push({ income: " . json_encode($key) . ", money: " . $currentValue . ", color: am4core.color('" . $pieColor[$colorIndex] . "') });\n";
                                        echo "totalIncomeFromGraph += " . $currentValue . ";\n"; // 총합 계산
                                        $i++;
                                    }
                                }
                            }
                            ?>
                            console.log("수익 그래프 데이터 총합:", totalIncomeFromGraph);
                            console.groupEnd(); // 콘솔 그룹 끝
                            chart.data = incomeChartData;

                            if (incomeChartData.length > 0) {
                                var series = chart.series.push(new am4charts.PieSeries());
                                series.dataFields.value = "money";
                                series.dataFields.category = "income";
                                series.slices.template.propertyFields.fill = "color";
                                series.slices.template.stroke = am4core.color("#fff");
                                series.slices.template.strokeOpacity = 1;
                                series.labels.template.disabled = true;
                                series.ticks.template.disabled = true;
                                series.slices.template.tooltipText = "{category}: {value.value.formatNumber('#,###')} ({value.percent.formatNumber('#.0')}%)";
                                chart.legend = new am4charts.Legend();
                                chart.legend.position = "right";
                                chart.innerRadius = am4core.percent(30);
                            } else {
                                document.getElementById("chartdiv").innerHTML = "<div style='text-align:center; padding-top: 100px; color:#999;'>수익 데이터가 없습니다.</div>";
                            }
                        } catch(e) {
                            console.error("수익 파이 차트 오류:", e);
                            document.getElementById("chartdiv").innerHTML = "<div style='text-align:center; padding-top: 100px; color:red;'>차트 오류 발생</div>";
                        }

                        // --- 차트 2: 비용 파이 차트 ---
                        try {
                            var chart2 = am4core.create("chartdiv2", am4charts.PieChart);
                            chart2.responsive.enabled = true;
                            var expenseChartData = [];
                            var totalExpenseFromGraph = 0; // 그래프 데이터 총합 계산용
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
                            console.log("비용 그래프 데이터 총합:", totalExpenseFromGraph);
                            console.groupEnd();
                            chart2.data = expenseChartData;

                            if (expenseChartData.length > 0) {
                                var series2 = chart2.series.push(new am4charts.PieSeries());
                                series2.dataFields.value = "money";
                                series2.dataFields.category = "expense";
                                series2.slices.template.stroke = am4core.color("#fff");
                                series2.slices.template.strokeOpacity = 1;
                                series2.labels.template.disabled = true;
                                series2.ticks.template.disabled = true;
                                series2.slices.template.tooltipText = "{category}: {value.value.formatNumber('#,###')} ({value.percent.formatNumber('#.0')}%)";
                                chart2.legend = new am4charts.Legend();
                                chart2.legend.position = "right";
                                chart2.innerRadius = am4core.percent(30);
                            } else {
                                document.getElementById("chartdiv2").innerHTML = "<div style='text-align:center; padding-top: 100px; color:#999;'>비용 데이터가 없습니다.</div>";
                            }
                        } catch(e) {
                            console.error("비용 파이 차트 오류:", e);
                            document.getElementById("chartdiv2").innerHTML = "<div style='text-align:center; padding-top: 100px; color:red;'>차트 오류 발생</div>";
                        }

                        // --- 차트 3: 월별 손익 막대 그래프 ---
                        try {
                            var chart3 = am4core.create("chartdiv3", am4charts.XYChart);
                            chart3.responsive.enabled = true;
                            var monthlyChartData = [];
                            var totalMonthlyIncome = 0;
                            var totalMonthlyExpense = 0;
                            console.groupCollapsed("월별 손익 막대/꺽은선 데이터 (chartdiv3, chartdiv4)");
                            <?php
                            if (isset($incomeMonthMoney) && is_array($incomeMonthMoney)) {
                                $monthlyDataTemp = []; // 임시 배열
                                foreach($incomeMonthMoney as $key => $value) {
                                    $monthNumStr = substr($key, 5, 2);
                                    $monthLabel = preg_replace('/^0/', '', $monthNumStr) . "월";
                                    $incomeVal = (float)$value;
                                    $expenseVal = (float)($expenseMonthMoney[$key] ?? 0);
                                    $monthlyDataTemp[$monthNumStr] = "{ month: " . json_encode($monthLabel) . ", income: " . $incomeVal . ", expense: " . $expenseVal . " }"; // 월별 데이터 저장
                                    echo "console.log('  - 월:', " . json_encode($monthLabel) . ", '수익:', " . $incomeVal . ", '비용:', " . $expenseVal . ");\n";
                                    echo "totalMonthlyIncome += " . $incomeVal . ";\n";
                                    echo "totalMonthlyExpense += " . $expenseVal . ";\n";
                                }
                                ksort($monthlyDataTemp); // 월 순서대로 정렬 (키 기준)
                                foreach ($monthlyDataTemp as $dataStr) {
                                    echo "monthlyChartData.push(" . $dataStr . ");\n"; // 정렬된 순서로 push
                                }
                            }
                            ?>
                            console.log("월별 그래프 수익 총합:", totalMonthlyIncome);
                            console.log("월별 그래프 비용 총합:", totalMonthlyExpense);
                            console.groupEnd();
                            chart3.data = monthlyChartData; // 정렬된 데이터 사용

                            if (monthlyChartData.length > 0) {
                                var categoryAxis3 = chart3.xAxes.push(new am4charts.CategoryAxis());
                                categoryAxis3.dataFields.category = "month";
                                categoryAxis3.title.text = "월";
                                categoryAxis3.renderer.grid.template.location = 0;
                                categoryAxis3.renderer.minGridDistance = 30;

                                var valueAxis3 = chart3.yAxes.push(new am4charts.ValueAxis());
                                valueAxis3.title.text = "금액";
                                valueAxis3.numberFormatter.numberFormat = "#,###";

                                var series3 = chart3.series.push(new am4charts.ColumnSeries());
                                series3.dataFields.valueY = "income"; // 'money' -> 'income'
                                series3.dataFields.categoryX = "month";
                                series3.name = "수익";
                                series3.columns.template.fill = am4core.color("#ffb74d"); // 원본 색상과 유사하게
                                series3.columns.template.stroke = am4core.color("#ffb74d");
                                series3.columns.template.tooltipText = "{name} ({categoryX}): {valueY.formatNumber('#,###')}";

                                var series4 = chart3.series.push(new am4charts.ColumnSeries());
                                series4.dataFields.valueY = "expense"; // 'money2' -> 'expense'
                                series4.dataFields.categoryX = "month";
                                series4.name = "비용";
                                series4.columns.template.fill = am4core.color("#64b5f6"); // 파란 계열
                                series4.columns.template.stroke = am4core.color("#64b5f6");
                                series4.columns.template.tooltipText = "{name} ({categoryX}): {valueY.formatNumber('#,###')}";

                                // 원본 라벨 블릿 코드 복원
                                var labelBullet = series3.bullets.push(new am4charts.LabelBullet());
                                labelBullet.label.text = "{valueY.value.formatNumber('#,###')}";
                                labelBullet.strokeOpacity = 0;
                                labelBullet.dy = -10;
                                labelBullet.label.fontSize = 9;

                                var labelBullet2 = series4.bullets.push(new am4charts.LabelBullet());
                                labelBullet2.label.text = "{valueY.value.formatNumber('#,###')}";
                                labelBullet2.strokeOpacity = 0;
                                labelBullet2.dy = -10;
                                labelBullet2.label.fontSize = 9;

                                chart3.legend = new am4charts.Legend();
                                chart3.cursor = new am4charts.XYCursor();
                            } else {
                                document.getElementById("chartdiv3").innerHTML = "<div style='text-align:center; padding-top: 100px; color:#999;'>월별 데이터가 없습니다.</div>";
                            }
                        } catch(e) {
                            console.error("월별 막대 차트 오류:", e);
                            document.getElementById("chartdiv3").innerHTML = "<div style='text-align:center; padding-top: 100px; color:red;'>차트 오류 발생</div>";
                        }

                        // --- 차트 4: 월별 손익 꺽은선 그래프 ---
                        try {
                            var chart4 = am4core.create("chartdiv4", am4charts.XYChart);
                            chart4.responsive.enabled = true;
                            chart4.data = monthlyChartData; // 차트 3에서 생성/정렬한 데이터 사용

                            if (monthlyChartData.length > 0) {
                                var categoryAxis4 = chart4.xAxes.push(new am4charts.CategoryAxis());
                                categoryAxis4.dataFields.category = "month";
                                categoryAxis4.title.text = "월";
                                categoryAxis4.renderer.grid.template.location = 0;
                                categoryAxis4.renderer.minGridDistance = 30;

                                var valueAxis4 = chart4.yAxes.push(new am4charts.ValueAxis());
                                valueAxis4.title.text = "금액";
                                valueAxis4.numberFormatter.numberFormat = "#,###";

                                var series5 = chart4.series.push(new am4charts.LineSeries());
                                series5.dataFields.valueY = "income"; // 'money' -> 'income'
                                series5.dataFields.categoryX = "month";
                                series5.name = "수익";
                                series5.stroke = am4core.color("#ffb74d"); // 색상 통일
                                series5.strokeWidth = 3; // 두께 원본과 유사하게
                                series5.tooltipText = "{name} ({categoryX}): {valueY.formatNumber('#,###')}";
                                series5.tensionX = 0.8;

                                var series6 = chart4.series.push(new am4charts.LineSeries());
                                series6.dataFields.valueY = "expense"; // 'money2' -> 'expense'
                                series6.dataFields.categoryX = "month";
                                series6.name = "비용";
                                series6.stroke = am4core.color("#64b5f6"); // 색상 통일
                                series6.strokeWidth = 3; // 두께 원본과 유사하게
                                series6.tooltipText = "{name} ({categoryX}): {valueY.formatNumber('#,###')}";
                                series6.tensionX = 0.8;

                                // 원본 라벨 블릿 복원
                                var labelBullet3 = series5.bullets.push(new am4charts.LabelBullet());
                                labelBullet3.label.text = "{valueY.value.formatNumber('#,###')}";
                                labelBullet3.strokeOpacity = 0;
                                labelBullet3.dy = -10; // 위치 조정 (원본과 다를 수 있음)
                                labelBullet3.label.fontSize = 9;

                                var labelBullet4 = series6.bullets.push(new am4charts.LabelBullet());
                                labelBullet4.label.text = "{valueY.value.formatNumber('#,###')}";
                                labelBullet4.strokeOpacity = 0;
                                labelBullet4.dy = 10; // 위치 조정
                                labelBullet4.label.fontSize = 9;

                                // 원형 블릿 추가
                                var bullet5 = series5.bullets.push(new am4charts.CircleBullet()); bullet5.circle.radius = 4; bullet5.circle.fill=series5.stroke; bullet5.circle.strokeWidth=1; bullet5.circle.stroke=am4core.color("#fff");
                                var bullet6 = series6.bullets.push(new am4charts.CircleBullet()); bullet6.circle.radius = 4; bullet6.circle.fill=series6.stroke; bullet6.circle.strokeWidth=1; bullet6.circle.stroke=am4core.color("#fff");

                                chart4.legend = new am4charts.Legend();
                                chart4.cursor = new am4charts.XYCursor();
                            } else {
                                document.getElementById("chartdiv4").innerHTML = "<div style='text-align:center; padding-top: 100px; color:#999;'>월별 데이터가 없습니다.</div>";
                            }
                        } catch(e) {
                            console.error("월별 꺽은선 차트 오류:", e);
                            document.getElementById("chartdiv4").innerHTML = "<div style='text-align:center; padding-top: 100px; color:red;'>차트 오류 발생</div>";
                        }

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
                            console.log("분기별 그래프 수익 총합:", totalQuarterlyIncome);
                            console.log("분기별 그래프 비용 총합:", totalQuarterlyExpense);
                            console.groupEnd();
                            chart5.data = quarterlyChartData;

                            if (quarterlyChartData.length > 0) {
                                var categoryAxis5 = chart5.xAxes.push(new am4charts.CategoryAxis());
                                categoryAxis5.dataFields.category = "quarter";
                                categoryAxis5.title.text = "분기";
                                categoryAxis5.renderer.grid.template.location = 0;
                                categoryAxis5.renderer.minGridDistance = 30;

                                var valueAxis5 = chart5.yAxes.push(new am4charts.ValueAxis());
                                valueAxis5.title.text = "금액";
                                valueAxis5.numberFormatter.numberFormat = "#,###";

                                var series7 = chart5.series.push(new am4charts.LineSeries());
                                series7.dataFields.valueY = "income"; // 'money' -> 'income'
                                series7.dataFields.categoryX = "quarter";
                                series7.name = "수익";
                                series7.stroke = am4core.color("#ffb74d");
                                series7.strokeWidth = 3;
                                series7.tooltipText = "{name} ({categoryX}): {valueY.formatNumber('#,###')}";
                                series7.tensionX = 0.8;

                                var series8 = chart5.series.push(new am4charts.LineSeries());
                                series8.dataFields.valueY = "expense"; // 'money2' -> 'expense'
                                series8.dataFields.categoryX = "quarter";
                                series8.name = "비용";
                                series8.stroke = am4core.color("#64b5f6");
                                series8.strokeWidth = 3;
                                series8.tooltipText = "{name} ({categoryX}): {valueY.formatNumber('#,###')}";
                                series8.tensionX = 0.8;

                                // 원본 라벨 블릿 복원
                                var labelBullet7 = series7.bullets.push(new am4charts.LabelBullet());
                                labelBullet7.label.text = "{valueY.value.formatNumber('#,###')}";
                                labelBullet7.strokeOpacity = 0;
                                labelBullet7.dy = -10; // 원본과 다를 수 있음
                                labelBullet7.label.fontSize = 9;

                                var labelBullet8 = series8.bullets.push(new am4charts.LabelBullet());
                                labelBullet8.label.text = "{valueY.value.formatNumber('#,###')}";
                                labelBullet8.strokeOpacity = 0;
                                labelBullet8.dy = 10;
                                labelBullet8.label.fontSize = 9;

                                // 원형 블릿 추가
                                var bullet7 = series7.bullets.push(new am4charts.CircleBullet()); bullet7.circle.radius = 4; bullet7.circle.fill=series7.stroke; bullet7.circle.strokeWidth=1; bullet7.circle.stroke=am4core.color("#fff");
                                var bullet8 = series8.bullets.push(new am4charts.CircleBullet()); bullet8.circle.radius = 4; bullet8.circle.fill=series8.stroke; bullet8.circle.strokeWidth=1; bullet8.circle.stroke=am4core.color("#fff");

                                chart5.legend = new am4charts.Legend();
                                chart5.cursor = new am4charts.XYCursor();
                            } else {
                                document.getElementById("chartdiv5").innerHTML = "<div style='text-align:center; padding-top: 100px; color:#999;'>분기별 데이터가 없습니다.</div>";
                            }
                        } catch(e) {
                            console.error("분기별 꺽은선 차트 오류:", e);
                            document.getElementById("chartdiv5").innerHTML = "<div style='text-align:center; padding-top: 100px; color:red;'>차트 오류 발생</div>";
                        }

                    }); // end am4core.ready()
                </script>

            <?php endif; // End of if ($PrintState != "1") ?>

            <div id="printSection2" style="display: none;">
                <?php
                if (($Search_sw ?? '2') == "2" && !empty($SearchStartMonth)) {
                    $loopYear = $SearchStartYear ?? date('Y'); // 루프용 변수
                    $loopMonth = $SearchStartMonth;

                    for ($i = 1; $i <= 3; $i++) { // 3번 반복하여 이전 3개월 데이터 로드
                        $prevTimestamp = strtotime($loopYear . "-" . str_pad($loopMonth, 2, '0', STR_PAD_LEFT) . "-01 -1 month");
                        $prevYear = date('Y', $prevTimestamp);
                        $prevMonth = date('n', $prevTimestamp);

                        // 임시로 전역 변수 변경하여 makeIncomeStateSql 호출 (원본 방식)
                        $originalSearchStartYear = $SearchStartYear;
                        $originalSearchStartMonth = $SearchStartMonth;
                        $SearchStartYear = $prevYear;
                        $SearchStartMonth = $prevMonth;

                        try {
                            $prevSql = makeIncomeStateSql();
                            printIncome($prevSql, "none", false, 100, 'income_prev_' . $i);
                        } catch (Exception $e) {
                            error_log("이전 달 손익계산서 생성 오류 ($prevYear-$prevMonth): " . $e->getMessage());
                        }

                        // 전역 변수 원상 복구
                        $SearchStartYear = $originalSearchStartYear;
                        $SearchStartMonth = $originalSearchStartMonth;

                        // 다음 루프를 위해 루프 변수 업데이트
                        $loopYear = $prevYear;
                        $loopMonth = $prevMonth;
                    }
                    // 루프 종료 후 최종적으로 원본 값 복원 (중요)
                    $SearchStartYear = $originalSearchStartYear;
                    $SearchStartMonth = $originalSearchStartMonth;
                }
                ?>