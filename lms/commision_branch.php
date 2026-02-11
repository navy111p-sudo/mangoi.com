<?php
error_reporting( E_ALL );
ini_set( "display_errors", 1 );
?>
<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
    <?
    include_once('./includes/common_meta_tag.php');
    include_once('./inc_header.php');
    ?>

    <?
    include_once('./inc_common_list_css.php');
    ?>
    <!-- ============== only this page css ============== -->

    <!-- ============== only this page css ============== -->
    <!-- ============== common.css ============== -->
    <link rel="stylesheet" type="text/css" href="./css/common.css" />
    <!-- ============== common.css ============== -->

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
#--------------------------------------------------------------------------------------------------------------------#
#--------------------------------------------------------- 학생수  --------------------------------------------------#
#--------------------------------------------------------------------------------------------------------------------#
$MainMenuID = 21;
$SubMenuID  = 21042;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
#--------------------------------------------------------------------------------------------------------------------#
$AddSqlWhere             = "1=1";
$AddSqlWhere2            = "1=1";
$ListParam               = "1=1";
$TotalRowCount           = "";
#--------------------------------------------------------------------------------------------------------------------#
$SearchStandard          = 2;
$SearchStandardDetail    = isset($_REQUEST["SearchStandardDetail"   ]) ? $_REQUEST["SearchStandardDetail"   ] : "";
$SearchStandardDetailSub = $_LINK_ADMIN_BRANCH_ID_;
$SearchDate              = 3;
#--------------------------------------------------------------------------------------------------------------------#
$SearchStartYear         = isset($_REQUEST["SearchStartYear"        ]) ? $_REQUEST["SearchStartYear"        ] : "";
$SearchStartMonth        = isset($_REQUEST["SearchStartMonth"       ]) ? $_REQUEST["SearchStartMonth"       ] : "";
$SearchStartDay          = isset($_REQUEST["SearchStartDay"         ]) ? $_REQUEST["SearchStartDay"         ] : "";
#--------------------------------------------------------------------------------------------------------------------#
$SearchEndYear           = isset($_REQUEST["SearchEndYear"          ]) ? $_REQUEST["SearchEndYear"          ] : "";
$SearchEndMonth          = isset($_REQUEST["SearchEndMonth"         ]) ? $_REQUEST["SearchEndMonth"         ] : "";
$SearchEndDay            = isset($_REQUEST["SearchEndDay"           ]) ? $_REQUEST["SearchEndDay"           ] : "";
#--------------------------------------------------------------------------------------------------------------------#
if ($SearchStandardDetail=="") {
    $SearchStandardDetail = 1;
}
if($SearchDate=="") {
    $SearchDate = 1;
}
#--------------------------------------------------------------------------------------------------------------------#
if ($SearchStartYear==""){
    $SearchStartYear  = date("Y");
}
if ($SearchStartMonth==""){
    $SearchStartMonth = date("m");
}
if ($SearchStartDay==""){
    $SearchStartDay  = 1; //date("d");
}
#--------------------------------------------------------------------------------------------------------------------#
if ($SearchDate==3) {    // 월별
    //$SearchStartMonth = 1;
    $SearchStartDay   = 1;
}
#--------------------------------------------------------------------------------------------------------------------#
if ($SearchEndYear==""){
    $SearchEndYear  = date("Y");
}
if ($SearchEndMonth==""){
    $SearchEndMonth = date("m");
}
if ($SearchEndDay==""){
    $SearchEndDay   = date("d");
}
#--------------------------------------------------------------------------------------------------------------------#
// 서치폼 감추기
$HideSearchForm = 0;
$HideStartMonth = 0;
$HideStartDay   = 0;
$HideEndYear    = 0;
$HideEndMonth   = 0;
$HideEndDay     = 0;
#--------------------------------------------------------------------------------------------------------------------#
if ($SearchDate==3) {    // 월별
    $SearchEndYear  = $SearchStartYear;
    if (!$SearchEndMonth) {
        $SearchEndMonth = date("n");
    }
    if (!$SearchEndDay) {
        $SearchEndDay   = 31;
    }
    if ($SearchStandardDetail=="5") {
        $HideStartMonth = 1;
    }
    $HideStartDay   = 1;
    $HideEndYear    = 1;
    $HideEndDay     = 1;
}
#--------------------------------------------------------------------------------------------------------------------#
$SearchStartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth,-2) . "-" . substr("0".$SearchStartDay,-2);
$SearchEndDate   = $SearchEndYear . "-" . substr("0".$SearchEndMonth,-2) . "-" . substr("0".$SearchEndDay,-2);
if ($SearchDate==3) {    // 월별
    $SearchStartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth,-2);
    $SearchEndDate   = $SearchEndYear . "-" . substr("0".$SearchEndMonth,-2);
}
#--------------------------------------------------------------------------------------------------------------------#
$Search_Parameter = "";
#--------------------------------------------------------------------------------------------------------------------#
$ArrSearchStandard   = "";
$idx = 0;
#--------------------------------------------------------------------------------------------------------------------#
$ArrTotalResultCount   = [];
$ArrTotalSubResultCont = [];
$CurrYear = date('Y');

//배열선언
$CenterName = [];
$TotalStudents = [];
$TotalClassOrderPayUseCashPrice = [];
$TotalBranchFee = [];
#====================================================================================================================#
#----------------------------------------------------- 지사 ----------------------------------------------------------#
#====================================================================================================================#
if($_LINK_ADMIN_LEVEL_ID_==9 || $_LINK_ADMIN_LEVEL_ID_==10) {  // 지사
#====================================================================================================================#

    // --- 권한별 필터 시작 (내용 동일) ---------------------------------------------------
    if (!empty($_REQUEST['SearchStandardDetail'])) {
        $SearchStandardDetailSub = (int)$_REQUEST['SearchStandardDetail'];
    }

    switch ($_LINK_ADMIN_LEVEL_ID_) {
        case 3:
        case 4:
            $AddSqlWhere = "A.BranchID IN (
                            SELECT AA.BranchID
                              FROM Branches AA
                              JOIN BranchGroups BB ON AA.BranchGroupID = BB.BranchGroupID
                              JOIN Companies   CC ON BB.CompanyID     = CC.CompanyID
                             WHERE CC.FranchiseID = {$_LINK_ADMIN_FRANCHISE_ID_}
                           )
                        AND A.CenterState<>0  AND B.BranchState<>0  AND C.BranchGroupState<>0";
            break;
        case 6:
        case 7:
            $AddSqlWhere = "A.BranchID IN (
                            SELECT BranchID
                              FROM Branches
                             WHERE BranchGroupID = {$_LINK_ADMIN_BRANCH_GROUP_ID_}
                           )
                        AND A.CenterState<>0 AND B.BranchState<>0 AND C.BranchGroupState<>0";
            break;
        case 9:
        case 10:
            $AddSqlWhere = "A.BranchID = {$_LINK_ADMIN_BRANCH_ID_}
                        AND A.CenterState<>0 AND B.BranchState<>0 AND C.BranchGroupState<>0";
            break;
        default:
            if (empty($SearchStandardDetailSub)) {
                $AddSqlWhere = "A.CenterState<>0 AND B.BranchState<>0 AND C.BranchGroupState<>0";
            } else {
                $AddSqlWhere = "A.BranchID IN (
                                SELECT BranchID
                                  FROM Branches
                                 WHERE BranchID      = {$SearchStandardDetailSub}
                                    OR BranchGroupID = {$SearchStandardDetailSub}
                             )
                            AND A.CenterState<>0 AND B.BranchState<>0 AND C.BranchGroupState<>0";
            }
    }
// --- 권한별 필터 끝 -----------------------------------------------------------------


    $AddSqlWhere2 = "date_format(AAA.ClassOrderPayPaymentDateTime, '%Y-%m') BETWEEN '".$SearchStartDate."' AND '".$SearchEndDate."' ";
    $AddSqlWhere2 = $AddSqlWhere2 . " and (AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41) ";

    //원천세 징수를 하고 싶으면 커미션에 0.967을 곱하면 된다.
    $ViewTable = "SELECT A.CenterName, 

                                (select count(*) from Members where  CenterID=A.CenterID  and MemberLevelID=19 and MemberState=1 and MemberID in
                                    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  BETWEEN  '$SearchStartDate' AND '$SearchEndDate')) AS TotalStudents,

								(SELECT sum(round(AAA.ClassOrderPayUseCashPrice)) 
                                from ClassOrderPays AAA inner join Members BBB on AAA.ClassOrderPayPaymentMemberID=BBB.MemberID inner join Centers CCC on AAA.CenterID=CCC.CenterID 
                                where ".$AddSqlWhere2." and CCC.CenterID=A.CenterID and AAA.PayResultCd = '0000') as TotalClassOrderPayUseCashPrice,

								(SELECT ROUND(SUM( AAA.ClassOrderPayUseCashPrice * 0.40 ))  -- 40% 커미션 으로 고정 설정
                                from ClassOrderPays AAA inner join Members BBB on AAA.ClassOrderPayPaymentMemberID=BBB.MemberID 
                                inner join Centers CCC on AAA.CenterID=CCC.CenterID 
                                left join Branches KKK on KKK.BranchID = CCC.BranchID 
                                left join BranchGroups LLL on LLL.BranchGroupID = KKK.BranchGroupID
                                left join Companies DDD on DDD.CompanyID = LLL.CompanyID
                                where ".$AddSqlWhere2." and CCC.CenterID=A.CenterID and AAA.PayResultCd = '0000') as TotalBranchFee

							from Centers A 
								inner join Branches B on A.BranchID=B.BranchID 
								inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
							where ".$AddSqlWhere ." and A.CenterRegDateTime <= '".$SearchStartDate."-01"."' ";

    $Sql = "select * from ($ViewTable) V order by V.TotalClassOrderPayUseCashPrice desc";

    //echo $ViewTable;
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    while($Row = $Stmt->fetch()) {
        array_push($CenterName, $Row["CenterName"]);
        array_push($TotalStudents, $Row["TotalStudents"]);
        array_push($TotalClassOrderPayUseCashPrice,$Row["TotalClassOrderPayUseCashPrice"]);
        array_push($TotalBranchFee,$Row["TotalBranchFee"]) ;
    }

#====================================================================================================================#
}
#====================================================================================================================#
$Search_Parameter = $SearchStandard . "-" . $SearchStandardDetail;

?>
<div id="page_content">
    <div id="page_content_inner">
        <h3 class="heading_b uk-margin-bottom">커미션 데이터</h3>

        <form name="SearchForm" method="get">
            <div class="md-card" style="margin-bottom:10px;">
                <div class="md-card-content">
                    <div class="uk-grid" data-uk-grid-margin="">

                        <div class="uk-width-medium-2-10" style="padding-top:7px;">
                            <select id="SearchStartYear" name="SearchStartYear" class="uk-width-1-1" data-placeholder="년도선택" style="width:100%;height:40px;"/>
                            <option value=""><?=$년도선택[$LangID]?></option>
                            <?
                            for ($iiii=2019;$iiii<=$CurrYear;$iiii++) {
                                ?>
                                <option value="<?=$iiii?>" <?if ($SearchStartYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
                                <?
                            }
                            ?>
                            </select>
                        </div>
                        <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideStartMonth==1,"none","")?>;">
                            <select id="SearchStartMonth" name="SearchStartMonth" class="uk-width-1-1" onchange="ChSearchStartMonth(1, this.value);" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;height:40px;"/>
                            <option value=""><?=$월선택[$LangID]?></option>
                            <?
                            for ($iiii=1;$iiii<=12;$iiii++) {
                                ?>
                                <option value="<?=$iiii?>" <?if ($SearchStartMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
                                <?
                            }
                            ?>
                            </select>
                        </div>
                        <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideStartDay==1,"none","")?>;">
                            <select id="SearchStartDay" name="SearchStartDay" onchange="SearchSubmit()" class="uk-width-1-1" data-placeholder="<?=$일선택[$LangID]?>" style="width:100%;height:40px;"/>
                            <option value=""><?=$일선택[$LangID]?></option>
                            </select>
                        </div>

                        <span style="padding-top: 15px; ">~</span>

                        <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideEndYear==1,"none","")?>;">
                            <select id="SearchEndYear" name="SearchEndYear" class="uk-width-1-1" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%;height:40px;"/>
                            <option value=""><?=$년도선택[$LangID]?></option>
                            <?
                            for ($iiii=2019;$iiii<=$CurrYear;$iiii++) {
                                ?>
                                <option value="<?=$iiii?>" <?if ($SearchEndYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
                                <?
                            }
                            ?>
                            </select>
                        </div>
                        <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideEndMonth==1,"none","")?>;">
                            <select id="SearchEndMonth" name="SearchEndMonth" class="uk-width-1-1" onchange="ChSearchStartMonth(2, this.value);" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;height:40px;"/>
                            <option value=""><?=$월선택[$LangID]?></option>
                            <?
                            for ($iiii=1;$iiii<=12;$iiii++) {
                                ?>
                                <option value="<?=$iiii?>" <?if ($SearchEndMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
                                <?
                            }
                            ?>
                            </select>
                        </div>
                        <div class="uk-width-medium-1-10" style="padding-top:7px; display:<?=iif($HideEndDay==1,"none","")?>;">
                            <select id="SearchEndDay" name="SearchEndDay" onchange="SearchSubmit()" class="uk-width-1-1" data-placeholder="<?=$일선택[$LangID]?>" style="width:100%;height:40px;"/>
                            <option value=""><?=$일선택[$LangID]?></option>
                            </select>
                        </div>

                        <?
                        $categorydate_find = "";
                        if ($SearchStartYear) {

                            $categorydate_find .= $SearchStartYear;

                        }
                        if ($SearchStartMonth and $SearchStandardDetail < 5) {

                            $categorydate_find .= "-" . $SearchStartMonth;

                        }

                        if ($SearchDate==3) {    // 월별

                            $categorydate_find .= " ~ " . $SearchEndMonth;

                        } else {
                            if ($SearchStartDay) {
                                $categorydate_find .= "-" . $SearchStartDay;
                            }
                            if ($SearchEndYear) {
                                $categorydate_find .= " ~ " . $SearchEndYear;
                            }
                            if ($SearchEndMonth) {
                                $categorydate_find .= "-" . $SearchEndMonth;
                            }
                            if ($SearchEndDay) {
                                $categorydate_find .= "-" . $SearchEndDay;
                            }

                        }
                        ?>
                        <div class="uk-width-medium-1-10" style="padding-top:7px;">
                            <a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div class="uk-overflow-container">

                            <h3></h3>
                            <table class="uk-table uk-table-align-vertical">
                                <thead>
                                <tr>
                                    <th nowrap>순번</th>
                                    <th nowrap>학원명(대리점명)</th>
                                    <th nowrap>재원생 수</th>
                                    <th nowrap>입금액</th>
                                    <th nowrap>커미션 (40%)</th>
                                    <th nowrap>세금 (3.3%)</th>
                                    <th nowrap>최종 수수료</th>
                                </tr>
                                </thead>
                                <?php for ($i=0;$i<count($CenterName);$i++){?>
                                    <tr>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$i+1?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$CenterName[$i]?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalStudents[$i])?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalClassOrderPayUseCashPrice[$i])?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalBranchFee[$i])?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=number_format(round($TotalBranchFee[$i]*0.033))?></td>
                                        <td class="uk-text-nowrap uk-table-td-center" style="font-weight:1000"><?=number_format($TotalBranchFee[$i] - round($TotalBranchFee[$i]*0.033))?></td>
                                    </tr>
                                <?php } ?>

                                <tr style="color:red;font-weight:1000">
                                    <td colspan="2" class="uk-text-nowrap uk-table-td-center">합계</td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=number_format(array_sum($TotalStudents))?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=number_format(array_sum($TotalClassOrderPayUseCashPrice))?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=number_format(array_sum($TotalBranchFee))?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=number_format(round(array_sum($TotalBranchFee)*0.033))?></td>
                                    <td class="uk-text-nowrap uk-table-td-center" style="font-weight:1000"><?=number_format(array_sum($TotalBranchFee) - round(array_sum($TotalBranchFee)*0.033))?></td>
                                </tr>
                            </table>


                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



<?
include_once('./inc_common_list_js.php');
?>
<!-- ============== only this page js ============== -->


<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
    var ListCount = <?=$ListCount-1?>;

    function ChSearchStartMonth(MonthType, MonthNumber){

        if (MonthType==1){
            YearNumber = document.SearchForm.SearchStartYear.value;
        }else{
            YearNumber = document.SearchForm.SearchEndYear.value;
        }
        url = "ajax_get_month_last_day.php";

        //location.href = url + "?YearNumber="+YearNumber+"&MonthNumber"+MonthNumber;
        $.ajax(url, {
            data: {
                YearNumber: YearNumber,
                MonthNumber: MonthNumber
            },
            success: function (data) {

                if (MonthType==1){

                    SelBoxInitOption('SearchStartDay');

                    SelBoxAddOption( 'SearchStartDay', '<?=$일선택[$LangID]?>', "", "");
                    for (ii=1 ; ii<=data.LastDay ; ii++ ){
                        ArrOptionText     = ii + "<?=$일일[$LangID]?>";
                        ArrOptionValue    = ii;

                        ArrOptionSelected = "";
                        if (ii==<?=(int)$SearchStartDay?>){
                            ArrOptionSelected = "selected";
                        }

                        SelBoxAddOption( 'SearchStartDay', ArrOptionText, ArrOptionValue, ArrOptionSelected );
                    }

                }else{

                    SelBoxInitOption('SearchEndDay');

                    SelBoxAddOption( 'SearchEndDay', '<?=$일선택[$LangID]?>', "", "");
                    for (ii=1 ; ii<=data.LastDay ; ii++ ){
                        ArrOptionText     = ii + "<?=$일일[$LangID]?>";
                        ArrOptionValue    = ii;

                        ArrOptionSelected = "";
                        if (ii==<?=(int)$SearchEndDay?>){
                            ArrOptionSelected = "selected";
                        }

                        SelBoxAddOption( 'SearchEndDay', ArrOptionText, ArrOptionValue, ArrOptionSelected );
                    }

                }

            },
            error: function () {

            }
        });
    }

    /** ===================================== 기본함수 ===================================== **/
// Option객체를 생성해서 Return
    function SelBoxCreateOption( text, value, selected )
    {
        var oOption = document.createElement("OPTION"); // Option 객체를 생성
        oOption.text = text; // Text(Keyword)를 입력
        oOption.value = value; // Value를 입력
        if (selected=="selected"){
            oOption.selected = true;
        }
        return oOption;
    }

    // SelectBox의 Option을 초기화
    function SelBoxInitOption( ObjId ){
        var SelectObj = document.getElementById( ObjId );
        if ( SelectObj == null ) return; // 객체가 존재하지 않으면 취소

        SelectObj.options.length = 0; // 길이를 0으로 하면 초기화
    }

    // Option을 추가
    function SelBoxAddOption( ObjId, text, value, selected ){
        var SelectObj = document.getElementById( ObjId );

        SelectObj.add( SelBoxCreateOption( text , value, selected ) );
        text     = "";
        value    = "";
        selected = "";
    }
    /** ===================================== 기본함수 ===================================== **/

</script>




<script>
    function SearchSubmit(){
        document.SearchForm.action = "commision_branch.php";
        document.SearchForm.submit();
    }

    window.onload = function(){
        ChSearchStartMonth(1, <?=(int)$SearchStartMonth?>);
        ChSearchStartMonth(2, <?=(int)$SearchEndMonth?>);
    }

</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>