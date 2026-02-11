<?php
error_reporting( E_ALL );
ini_set( "display_errors", 1 );
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!-->
<html lang="ko">
<!--<![endif]-->
<head>
    <?php
    include_once('./includes/common_meta_tag.php');
    include_once('./inc_header.php');
    include_once('./inc_common_list_css.php');
    ?>
    <!-- ============== only this page css ============== -->
    <link rel="stylesheet" type="text/css" href="./css/common.css" />
    <!-- 처리중 오버레이 스타일 (페이지 로딩 시 기본 표시) -->
    <style>
        #processingOverlay {
            display: block; /* 페이지 로딩 시 기본으로 노출 */
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            color: #fff;
            font-size: 18px;
            text-align: center;
            padding-top: 20%;
        }
        /* 오버레이 내부 버튼 스타일 */
        #processingOverlay button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<!-- 처리중 메시지 오버레이 (수동 닫기 버튼 포함) -->
<div id="processingOverlay">
    요청하신 작업을 처리 중입니다.<br>
    페이지를 새로고침 하지 마시고 잠시만 기다려 주세요...<br>
    <button type="button" onclick="hideProcessingOverlay();">안내창 닫기</button>
</div>

<?php
//--------------------------------------------------------------------------------------------------------------------//
//--------------------------------------------------------- 학생수 및 커미션  --------------------------------------------------//
//--------------------------------------------------------------------------------------------------------------------//
$MainMenuID = 21;
$SubMenuID  = 21044;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

//----------------------------- 검색 관련 변수 설정 -----------------------------//
$AddSqlWhere  = "1=1";
$AddSqlWhere2 = "1=1";
$ListParam    = "1=1";
$TotalRowCount= "";
$SearchStandard          = 2;
$SearchStandardDetail    = isset($_REQUEST["SearchStandardDetail"]) ? $_REQUEST["SearchStandardDetail"] : "";
$SearchStandardDetailSub = $_LINK_ADMIN_BRANCH_ID_;
$SearchDate              = 3;  // 3: 월별 검색, 그 외는 일별
$SearchStartYear  = isset($_REQUEST["SearchStartYear"])  ? $_REQUEST["SearchStartYear"]  : "";
$SearchStartMonth = isset($_REQUEST["SearchStartMonth"]) ? $_REQUEST["SearchStartMonth"] : "";
$SearchStartDay   = isset($_REQUEST["SearchStartDay"])   ? $_REQUEST["SearchStartDay"]   : "";
$SearchEndYear    = isset($_REQUEST["SearchEndYear"])    ? $_REQUEST["SearchEndYear"]    : "";
$SearchEndMonth   = isset($_REQUEST["SearchEndMonth"])   ? $_REQUEST["SearchEndMonth"]   : "";
$SearchEndDay     = isset($_REQUEST["SearchEndDay"])     ? $_REQUEST["SearchEndDay"]     : "";

if ($SearchStandardDetail=="") { $SearchStandardDetail = 1; }
if($SearchDate=="") { $SearchDate = 1; }
if ($SearchStartYear==""){ $SearchStartYear  = date("Y"); }
if ($SearchStartMonth==""){ $SearchStartMonth = date("m"); }
if ($SearchStartDay==""){ $SearchStartDay  = 1; }
if ($SearchDate==3) { $SearchStartDay = 1; }
if ($SearchEndYear==""){ $SearchEndYear  = date("Y"); }
if ($SearchEndMonth==""){ $SearchEndMonth = date("m"); }
if ($SearchEndDay==""){ $SearchEndDay   = date("d"); }
if ($SearchDate==3) {
    $SearchEndYear  = $SearchStartYear;
    if (!$SearchEndMonth) { $SearchEndMonth = date("n"); }
    if (!$SearchEndDay) { $SearchEndDay   = 31; }
    $HideStartDay   = 1;
    $HideEndYear    = 1;
    $HideEndDay     = 1;
} else {
    $HideStartDay = 0;
    $HideEndYear  = 0;
    $HideEndDay   = 0;
}
if ($SearchDate==3) {
    $SearchStartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth, -2);
    $SearchEndDate   = $SearchEndYear   . "-" . substr("0".$SearchEndMonth, -2);
} else {
    $SearchStartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth, -2) . "-" . substr("0".$SearchStartDay, -2);
    $SearchEndDate   = $SearchEndYear   . "-" . substr("0".$SearchEndMonth, -2) . "-" . substr("0".$SearchEndDay, -2);
}

// 대표지사 및 대리점 조회 조건
// (기존 $AddSqlWhere = "..."; 블록을 전부 삭제하고 아래로 교체)
// --- 권한별 필터 시작 --------------------------------------------------------------
if (!empty($_REQUEST['SearchStandardDetail'])) {
    // 사용자가 드롭다운으로 고른 값(지사ID or 지사그룹ID)이 있으면 우선
    $SearchStandardDetailSub = (int)$_REQUEST['SearchStandardDetail'];
}

/**
 *  권한 레벨별 기본 필터
 *  0,1  : 마스터 (전체)
 *  3,4  : 프랜차이즈 관리자     → 같은 Franchise 소속 지사만
 *  6,7  : 대표지사 관리자      → 자기 BranchGroup(대표지사) 소속 지사만
 *  9,10 : 지사 관리자          → 자기 지사만
 */
switch ($_LINK_ADMIN_LEVEL_ID_) {
    case 3:
    case 4: // 프랜차이즈
        $AddSqlWhere = "A.BranchID IN (
                            SELECT AA.BranchID
                              FROM Branches AA
                              JOIN BranchGroups BB ON AA.BranchGroupID = BB.BranchGroupID
                              JOIN Companies   CC ON BB.CompanyID     = CC.CompanyID
                             WHERE CC.FranchiseID = {$_LINK_ADMIN_FRANCHISE_ID_}
                           )
                        AND A.CenterState<>0 AND B.BranchState<>0 AND C.BranchGroupState<>0";
        break;

    case 6:
    case 7: // 대표지사
        $AddSqlWhere = "A.BranchID IN (
                            SELECT BranchID
                              FROM Branches
                             WHERE BranchGroupID = {$_LINK_ADMIN_BRANCH_GROUP_ID_}
                           )
                        AND A.CenterState<>0 AND B.BranchState<>0 AND C.BranchGroupState<>0";
        break;

    case 9:
    case 10: // 지사
        $AddSqlWhere = "A.BranchID = {$_LINK_ADMIN_BRANCH_ID_}
                        AND A.CenterState<>0 AND B.BranchState<>0 AND C.BranchGroupState<>0";
        break;

    default: // 마스터 등
        if (empty($SearchStandardDetailSub)) {
            // 전체
            $AddSqlWhere = "A.CenterState<>0 AND B.BranchState<>0 AND C.BranchGroupState<>0";
        } else {
            // 마스터가 특정 지사·대표지사를 선택했을 때
            $AddSqlWhere = "A.BranchID IN (
                                SELECT BranchID
                                  FROM Branches
                                 WHERE BranchID      = {$SearchStandardDetailSub}
                                    OR BranchGroupID = {$SearchStandardDetailSub}
                             )
                            AND A.CenterState<>0 AND B.BranchState<>0 AND C.BranchGroupState<>0";
        }
}
// --- 권한별 필터 끝 ----------------------------------------------------------------

$AddSqlWhere2 = "date_format(AAA.ClassOrderPayPaymentDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."' ";
$AddSqlWhere2 .= " and (AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41) ";

// 데이터 조회 쿼리 (Branches의 BranchName을 '지사구분'으로 출력)
$ViewTable = "
SELECT 
    B.BranchName AS BranchDivision, 
    A.CenterName, 
    (SELECT count(*) 
     FROM Members 
     WHERE CenterID=A.CenterID AND MemberLevelID=19 AND MemberState=1 
           AND MemberID IN (SELECT MemberID FROM Classes 
                            WHERE date_format(StartDateTime, '%Y-%m') BETWEEN '$SearchStartDate' AND '$SearchEndDate')
    ) AS TotalStudents,
    (SELECT sum(round(AAA.ClassOrderPayUseCashPrice)) 
     FROM ClassOrderPays AAA 
          INNER JOIN Members BBB ON AAA.ClassOrderPayPaymentMemberID=BBB.MemberID 
          INNER JOIN Centers CCC ON AAA.CenterID=CCC.CenterID 
     WHERE $AddSqlWhere2 AND CCC.CenterID=A.CenterID AND AAA.PayResultCd = '0000'
    ) AS TotalClassOrderPayUseCashPrice,
    (SELECT ROUND(sum((AAA.ClassOrderPayUseCashPrice) * ((CCC.CenterPricePerTime - DDD.CompanyPricePerTime) / CCC.CenterPricePerTime)))  
     FROM ClassOrderPays AAA 
          INNER JOIN Members BBB ON AAA.ClassOrderPayPaymentMemberID=BBB.MemberID 
          INNER JOIN Centers CCC ON AAA.CenterID=CCC.CenterID 
          LEFT JOIN Branches KKK ON KKK.BranchID = CCC.BranchID 
          LEFT JOIN BranchGroups LLL ON LLL.BranchGroupID = KKK.BranchGroupID
          LEFT JOIN Companies DDD ON DDD.CompanyID = LLL.CompanyID
     WHERE $AddSqlWhere2 AND CCC.CenterID=A.CenterID AND AAA.PayResultCd = '0000'
    ) AS TotalBranchFee
FROM Centers A 
     INNER JOIN Branches B ON A.BranchID = B.BranchID 
     INNER JOIN BranchGroups C ON B.BranchGroupID = C.BranchGroupID 
WHERE $AddSqlWhere 
  AND A.CenterRegDateTime <= '".$SearchStartDate."-01'
";

$Sql = "SELECT * FROM ($ViewTable) V ORDER BY V.BranchDivision ASC, V.TotalClassOrderPayUseCashPrice DESC";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$resultData = array();
while($Row = $Stmt->fetch()) {
    $resultData[] = $Row;
}
?>
<div id="page_content">
    <div id="page_content_inner">
        <h3 class="heading_b uk-margin-bottom">커미션 데이터 (상세보기)</h3>

        <!-- 검색폼 (기간 선택 드롭다운) -->
        <form name="SearchForm" method="get" onsubmit="showProcessingOverlay();">
            <div class="md-card" style="margin-bottom:10px;">
                <div class="md-card-content">
                    <div class="uk-grid" data-uk-grid-margin="">
                        <!-- 시작년도 선택 -->
                        <div class="uk-width-medium-2-10" style="padding-top:7px;">
                            <select id="SearchStartYear" name="SearchStartYear" class="uk-width-1-1" style="height:40px;">
                                <option value=""><?=$년도선택[$LangID]?></option>
                                <?php for($y=2019; $y<=date('Y'); $y++){ ?>
                                    <option value="<?=$y?>" <?=($SearchStartYear==$y ? 'selected' : '')?>><?=$y?>년</option>
                                <?php } ?>
                            </select>
                        </div>
                        <!-- 시작월 선택 -->
                        <div class="uk-width-medium-1-10" style="padding-top:7px;">
                            <select id="SearchStartMonth" name="SearchStartMonth" class="uk-width-1-1" style="height:40px;" onchange="ChSearchStartMonth(1, this.value);">
                                <option value=""><?=$월선택[$LangID]?></option>
                                <?php for($m=1; $m<=12; $m++){ ?>
                                    <option value="<?=$m?>" <?=($SearchStartMonth==$m ? 'selected' : '')?>><?=$m?>월</option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php if(!$HideStartDay){ ?>
                            <!-- 시작일 선택 (일별 검색 시) -->
                            <div class="uk-width-medium-1-10" style="padding-top:7px;">
                                <select id="SearchStartDay" name="SearchStartDay" class="uk-width-1-1" style="height:40px;">
                                    <option value=""><?=$일선택[$LangID]?></option>
                                </select>
                            </div>
                        <?php } ?>

                        <span style="padding-top:15px;"> ~ </span>

                        <?php if(!$HideEndYear){ ?>
                            <!-- 끝년도 선택 (일별 검색 시) -->
                            <div class="uk-width-medium-1-10" style="padding-top:7px;">
                                <select id="SearchEndYear" name="SearchEndYear" class="uk-width-1-1" style="height:40px;">
                                    <option value=""><?=$년도선택[$LangID]?></option>
                                    <?php for($y=2019; $y<=date('Y'); $y++){ ?>
                                        <option value="<?=$y?>" <?=($SearchEndYear==$y ? 'selected' : '')?>><?=$y?>년</option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                        <!-- 끝월 선택 -->
                        <div class="uk-width-medium-1-10" style="padding-top:7px;">
                            <select id="SearchEndMonth" name="SearchEndMonth" class="uk-width-1-1" style="height:40px;" onchange="ChSearchStartMonth(2, this.value);">
                                <option value=""><?=$월선택[$LangID]?></option>
                                <?php for($m=1; $m<=12; $m++){ ?>
                                    <option value="<?=$m?>" <?=($SearchEndMonth==$m ? 'selected' : '')?>><?=$m?>월</option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php if(!$HideEndDay){ ?>
                            <!-- 끝일 선택 (일별 검색 시) -->
                            <div class="uk-width-medium-1-10" style="padding-top:7px;">
                                <select id="SearchEndDay" name="SearchEndDay" class="uk-width-1-1" style="height:40px;">
                                    <option value=""><?=$일선택[$LangID]?></option>
                                </select>
                            </div>
                        <?php } ?>
                        <!-- 검색 버튼 -->
                        <div class="uk-width-medium-1-10" style="padding-top:7px;">
                            <a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
                        </div>
                        <!-- 합계 확인 버튼 -->
                        <div class="uk-width-medium" style="padding-top:7px;">
                            <a href="javascript:void(0);" onclick="scrollToBottom();" class="md-btn md-btn-primary uk-margin-small-top">합계 확인 (페이지 하단으로 이동)</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- 데이터 출력 영역 -->
        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div class="uk-overflow-container">
                            <table class="uk-table uk-table-align-vertical">
                                <thead>
                                <tr>
                                    <th nowrap>순번</th>
                                    <th nowrap>지사구분</th>
                                    <th nowrap>학원명(대리점명)</th>
                                    <th nowrap>재학생 수</th>
                                    <th nowrap>입금액</th>
                                    <th nowrap>커미션</th>
                                    <th nowrap>세금 (3.3%)</th>
                                    <th nowrap>최종 수수료</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $totalStudentsSum = 0;
                                $totalPaySum      = 0;
                                $totalCommissionSum = 0;
                                foreach($resultData as $i => $row){
                                    $totalStudentsSum += $row["TotalStudents"];
                                    $totalPaySum      += $row["TotalClassOrderPayUseCashPrice"];
                                    $totalCommissionSum += $row["TotalBranchFee"];
                                    ?>
                                    <tr>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=($i+1)?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$row["BranchDivision"]?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$row["CenterName"]?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=number_format($row["TotalStudents"])?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=number_format($row["TotalClassOrderPayUseCashPrice"])?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=number_format($row["TotalBranchFee"])?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=number_format(round($row["TotalBranchFee"]*0.033))?></td>
                                        <td class="uk-text-nowrap uk-table-td-center" style="font-weight:1000">
                                            <?=number_format($row["TotalBranchFee"] - round($row["TotalBranchFee"]*0.033))?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr style="color:red;font-weight:1000">
                                    <td colspan="3" class="uk-text-nowrap uk-table-td-center">합계</td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=number_format($totalStudentsSum)?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=number_format($totalPaySum)?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=number_format($totalCommissionSum)?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=number_format(round($totalCommissionSum*0.033))?></td>
                                    <td class="uk-text-nowrap uk-table-td-center" style="font-weight:1000">
                                        <?=number_format($totalCommissionSum - round($totalCommissionSum*0.033))?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
include_once('./inc_common_list_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<script>
    // 페이지 하단으로 부드럽게 스크롤하는 함수
    function scrollToBottom(){
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    }

    // 오버레이 표시 함수
    function showProcessingOverlay(){
        var overlay = document.getElementById('processingOverlay');
        if(overlay){
            overlay.style.display = 'block';
        }
    }

    // 오버레이 숨김 함수
    function hideProcessingOverlay(){
        var overlay = document.getElementById('processingOverlay');
        if(overlay){
            overlay.style.display = 'none';
        }
    }

    // 날짜 선택 드롭다운 AJAX 옵션 채우기 (해당 셀렉트가 있으면 실행)
    function ChSearchStartMonth(MonthType, MonthNumber){
        var YearNumber;
        if (MonthType == 1) {
            if(document.SearchForm.SearchStartYear){
                YearNumber = document.SearchForm.SearchStartYear.value;
            } else {
                return;
            }
        } else {
            if(document.SearchForm.SearchEndYear){
                YearNumber = document.SearchForm.SearchEndYear.value;
            } else {
                return;
            }
        }
        var daySelectId = (MonthType == 1) ? 'SearchStartDay' : 'SearchEndDay';
        if(!document.getElementById(daySelectId)) {
            return;
        }
        var url = "ajax_get_month_last_day.php";
        $.ajax(url, {
            data: { YearNumber: YearNumber, MonthNumber: MonthNumber },
            success: function (data) {
                if (MonthType == 1){
                    if(document.getElementById('SearchStartDay')){
                        SelBoxInitOption('SearchStartDay');
                        SelBoxAddOption('SearchStartDay', '<?=$일선택[$LangID]?>', "", "");
                        for (var ii = 1; ii <= data.LastDay; ii++){
                            var ArrOptionText = ii + "<?=$일일[$LangID]?>";
                            var ArrOptionValue = ii;
                            var ArrOptionSelected = (ii == <?=(int)$SearchStartDay?>) ? "selected" : "";
                            SelBoxAddOption('SearchStartDay', ArrOptionText, ArrOptionValue, ArrOptionSelected);
                        }
                    }
                } else {
                    if(document.getElementById('SearchEndDay')){
                        SelBoxInitOption('SearchEndDay');
                        SelBoxAddOption('SearchEndDay', '<?=$일선택[$LangID]?>', "", "");
                        for (var ii = 1; ii <= data.LastDay; ii++){
                            var ArrOptionText = ii + "<?=$일일[$LangID]?>";
                            var ArrOptionValue = ii;
                            var ArrOptionSelected = (ii == <?=(int)$SearchEndDay?>) ? "selected" : "";
                            SelBoxAddOption('SearchEndDay', ArrOptionText, ArrOptionValue, ArrOptionSelected);
                        }
                    }
                }
            },
            error: function () { }
        });
    }

    function SelBoxCreateOption(text, value, selected){
        var oOption = document.createElement("OPTION");
        oOption.text = text;
        oOption.value = value;
        if (selected=="selected"){ oOption.selected = true; }
        return oOption;
    }
    function SelBoxInitOption(ObjId){
        var SelectObj = document.getElementById(ObjId);
        if (SelectObj == null) return;
        SelectObj.options.length = 0;
    }
    function SelBoxAddOption(ObjId, text, value, selected){
        var SelectObj = document.getElementById(ObjId);
        if(SelectObj){
            SelectObj.add(SelBoxCreateOption(text, value, selected));
        }
    }
    function SearchSubmit(){
        showProcessingOverlay();
        document.SearchForm.action = "commision_branch_detail.php"; // ← 자기 자신으로
        document.SearchForm.submit();
    }

    // 페이지 로딩 완료 후 (모든 리소스 포함) 1초마다 document.readyState를 체크해서 오버레이 숨김
    $(window).on('load', function(){
        // 필요한 날짜 셀렉트가 있다면 옵션 채우기
        if(document.getElementById('SearchStartDay')){
            ChSearchStartMonth(1, <?=(int)$SearchStartMonth?>);
        }
        if(document.getElementById('SearchEndDay')){
            ChSearchStartMonth(2, <?=(int)$SearchEndMonth?>);
        }
        // 1초마다 document.readyState를 체크
        var checkInterval = setInterval(function(){
            if(document.readyState === "complete"){
                hideProcessingOverlay();
                clearInterval(checkInterval);
            }
        }, 500);
    });
</script>
<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>
