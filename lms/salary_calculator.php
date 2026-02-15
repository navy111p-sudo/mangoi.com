<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
?>
<?
include_once('./inc_common_list_css.php');
?>
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->

<!-- ============== salary calculator css ============== -->
<link rel="stylesheet" type="text/css" href="../css/salary-calculator.css" />
<!-- ============== salary calculator css ============== -->

<!-- Tailwind CSS CDN (salary calculator에서 사용) -->
<script src="https://cdn.tailwindcss.com"></script>

<style>
/* salary calculator가 LMS 프레임 안에서 잘 보이도록 추가 스타일 */
.salary-calculator-wrapper {
    max-width: 900px;
    margin: 0 auto;
    padding: 10px;
}
/* LMS의 기본 스타일과 충돌 방지 */
.salary-calculator-wrapper .calculator-card {
    margin-top: 0;
}
</style>

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 13;  // 교육센터 메뉴
$SubMenuID = 1308; // 수업료 계산기
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>

<div id="page_content">
    <div id="page_content_inner">

        <div class="salary-calculator-wrapper">
            <!-- ====== salary calculator 본문 시작 ====== -->

            <div class="calculator-card">
                <div class="calculator-card__header">
                    <h1 class="calculator-card__title">Salary Calculator</h1>
                    <p class="calculator-card__subtitle">수업료 계산기</p>
                </div>

                <div class="calculator-card__body">
                    <!-- 강사 이름 선택/입력 영역 -->
                    <div class="teacher-section">
                        <div class="teacher-section__select-group">
                            <label class="form-label" for="teacher-select">강사 이름 선택</label>
                            <div class="teacher-section__dropdown-wrapper">
                                <select id="teacher-select" class="form-select">
                                    <option value="">-- 선택하세요 --</option>
                                </select>
                            </div>
                        </div>

                        <div class="teacher-section__input-group">
                            <label class="form-label" for="teacher-name">새 강사 이름</label>
                            <div class="teacher-section__input-wrapper">
                                <input
                                    type="text"
                                    id="teacher-name"
                                    class="form-input"
                                    placeholder="이름 입력 후 저장"
                                />
                                <button id="save-teacher-btn" class="btn btn--save">
                                    저장
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 점수 입력 영역 -->
                    <div class="score-section">
                        <div class="score-section__grid">
                            <div class="score-item">
                                <label class="form-label" for="attendance">출석 점수</label>
                                <input
                                    type="number"
                                    id="attendance"
                                    class="form-input"
                                    min="0"
                                    max="5"
                                    step="0.1"
                                    placeholder="0 ~ 5"
                                />
                            </div>
                            <div class="score-item">
                                <label class="form-label" for="student-eval">학생 평가</label>
                                <input
                                    type="number"
                                    id="student-eval"
                                    class="form-input"
                                    min="0"
                                    max="5"
                                    step="0.1"
                                    placeholder="0 ~ 5"
                                />
                            </div>
                            <div class="score-item">
                                <label class="form-label" for="boss-eval">관리자 평가</label>
                                <input
                                    type="number"
                                    id="boss-eval"
                                    class="form-input"
                                    min="0"
                                    max="5"
                                    step="0.1"
                                    placeholder="0 ~ 5"
                                />
                            </div>
                            <div class="score-item">
                                <label class="form-label" for="yearly-rate">연간 비율</label>
                                <input
                                    type="number"
                                    id="yearly-rate"
                                    class="form-input"
                                    min="0"
                                    max="5"
                                    step="0.1"
                                    placeholder="0 ~ 5"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- 계산 버튼 -->
                    <button id="calculate-btn" class="btn btn--calculate">
                        계산하기
                    </button>

                    <!-- 결과 표시 영역 -->
                    <div id="result-area" class="result-area" style="display: none">
                        <div class="result-area__header">
                            <h2 class="result-area__title">계산 결과</h2>
                        </div>
                        <div class="result-area__body">
                            <div class="result-item">
                                <span class="result-item__label">강사 이름</span>
                                <span id="result-name" class="result-item__value">-</span>
                            </div>
                            <div class="result-item">
                                <span class="result-item__label">평균 점수</span>
                                <span id="result-avg" class="result-item__value">-</span>
                            </div>
                            <div class="result-item">
                                <span class="result-item__label">등급</span>
                                <span id="result-grade" class="result-item__value">-</span>
                            </div>
                            <div class="result-item">
                                <span class="result-item__label">10분당 단가</span>
                                <span id="result-rate" class="result-item__value">-</span>
                            </div>
                            <div class="result-item">
                                <span class="result-item__label">순위</span>
                                <span id="result-rank" class="result-item__value">-</span>
                            </div>
                            <div id="result-feedback" class="result-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 단가 기준표 -->
            <div class="rate-table-card">
                <div class="rate-table-card__header">
                    <h2 class="rate-table-card__title">단가 기준표 (10분 기준)</h2>
                </div>
                <div class="rate-table-card__body">
                    <table class="rate-table">
                        <thead>
                            <tr>
                                <th>등급</th>
                                <th>평균 점수 범위</th>
                                <th>10분당 단가 (페소)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge badge--best">Best (최우수)</span></td>
                                <td>4.7 ~ 5.0</td>
                                <td class="rate-table__rate">160</td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="badge badge--excellent">Excellent (우수)</span>
                                </td>
                                <td>4.0 ~ 4.69</td>
                                <td class="rate-table__rate">150</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge--good">Good (양호)</span></td>
                                <td>3.5 ~ 3.99</td>
                                <td class="rate-table__rate">140</td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="badge badge--average">Average (보통)</span>
                                </td>
                                <td>3.0 ~ 3.49</td>
                                <td class="rate-table__rate">130</td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="badge badge--below-average"
                                        >Below Average (미흡)</span
                                    >
                                </td>
                                <td>2.5 ~ 2.99</td>
                                <td class="rate-table__rate">120</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge--poor">Poor (부족)</span></td>
                                <td>2.0 ~ 2.49</td>
                                <td class="rate-table__rate">110</td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="badge badge--very-poor"
                                        >Very Poor (매우 부족)</span
                                    >
                                </td>
                                <td>0 ~ 1.99</td>
                                <td class="rate-table__rate">100</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ====== salary calculator 본문 끝 ====== -->
        </div>

    </div>
</div>

<!-- common functions -->
<script src="assets/js/common_no_jquery.js"></script>
<!-- uikit functions -->
<script src="assets/js/uikit_custom.js"></script>
<!-- altair common functions/helpers -->
<script src="assets/js/altair_admin_common.js"></script>

<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->

<!-- ============== salary calculator js ============== -->
<script type="text/javascript" src="../js/salary-calculator.js"></script>
<!-- ============== salary calculator js ============== -->

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>

</body>
</html>
