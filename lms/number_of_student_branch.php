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
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->

<style>
#chartdiv1 {
  width: 100%;
  height: 400px;
}
#chartdiv2 {
  width: 100%;
  height: 400px;
}
#chartdiv3 {
  width: 100%;
  height: 400px;
}

</style>
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
#--------------------------------------------------------------------------------------------------------------------#
#--------------------------------------------------------- 학생수  --------------------------------------------------#
#--------------------------------------------------------------------------------------------------------------------#
$MainMenuID = 21;
$SubMenuID  = 21041;
include_once('./inc_top.php'); 
include_once('./inc_menu_left.php');
#--------------------------------------------------------------------------------------------------------------------#
$AddSqlWhere             = "1=1";
$AddSqlWhere2            = "1=1";
$ListParam               = "1=1";
$TotalRowCount           = "";
#--------------------------------------------------------------------------------------------------------------------#

$SearchStandardDetailSub = $_LINK_ADMIN_BRANCH_ID_;

#--------------------------------------------------------------------------------------------------------------------#
$SearchStartYear         = isset($_REQUEST["SearchStartYear"        ]) ? $_REQUEST["SearchStartYear"        ] : "";

if ($SearchStartYear==""){
    $SearchStartYear  = date("Y");
}

$PreYear = $SearchStartYear - 1;

$CurrYear = date("Y");

//배열 선언
$MonthArr = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

$CenterName = [];
$CenterRegDate = [];
$Jan = [];
$Feb = [];
$Mar = [];
$Apr = [];
$May = [];
$Jun = [];
$Jul = [];
$Aug = [];
$Sep = [];
$Oct = [];
$Nov = [];
$Dec = [];

$CenterName2 = [];
$CenterRegDate2 = [];
$Jan2 = [];
$Feb2 = [];
$Mar2 = [];
$Apr2 = [];
$May2 = [];
$Jun2 = [];
$Jul2 = [];
$Aug2 = [];
$Sep2 = [];
$Oct2 = [];
$Nov2 = [];
$Dec2 = [];


$CenterName3 = [];
$CenterRegDate3 = [];
$Jan3 = [];
$Feb3 = [];
$Mar3 = [];
$Apr3 = [];
$May3 = [];
$Jun3 = [];
$Jul3 = [];
$Aug3 = [];
$Sep3 = [];
$Oct3 = [];
$Nov3 = [];
$Dec3 = [];

#====================================================================================================================#
#----------------------------------------------------- 지사 ----------------------------------------------------------#
#====================================================================================================================#
if($_LINK_ADMIN_LEVEL_ID_==9 || $_LINK_ADMIN_LEVEL_ID_==10) {  // 지사
#====================================================================================================================#
    // 재원생
    $Sql = "SELECT  A.CenterName,  count(B.memberID), DATE_FORMAT(A.CenterRegDateTime,'%Y-%m-%d') as regDate,
    (select count(*) from Members where  CenterID=A.CenterID  and MemberLevelID=19 and MemberState=1 and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$PreYear-12' and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-01'))) as '1월',
    (select count(*) from Members where  CenterID=A.CenterID  and MemberLevelID=19 and MemberState=1 and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-01' and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-02'))) as '2월',
    (select count(*) from Members where  CenterID=A.CenterID  and MemberLevelID=19 and MemberState=1 and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-02' and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-03'))) as '3월',
    (select count(*) from Members where  CenterID=A.CenterID  and MemberLevelID=19 and MemberState=1 and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-03' and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-04'))) as '4월',
    (select count(*) from Members where  CenterID=A.CenterID  and MemberLevelID=19 and MemberState=1 and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-04' and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-05'))) as '5월',
    (select count(*) from Members where  CenterID=A.CenterID  and MemberLevelID=19 and MemberState=1 and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-05' and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-06'))) as '6월',
    (select count(*) from Members where  CenterID=A.CenterID  and MemberLevelID=19 and MemberState=1 and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-06' and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-07'))) as '7월',
    (select count(*) from Members where  CenterID=A.CenterID  and MemberLevelID=19 and MemberState=1 and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-07' and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-08'))) as '8월',
    (select count(*) from Members where  CenterID=A.CenterID  and MemberLevelID=19 and MemberState=1 and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-08' and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-09'))) as '9월',
    (select count(*) from Members where  CenterID=A.CenterID  and MemberLevelID=19 and MemberState=1 and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-09' and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-10'))) as '10월',
    (select count(*) from Members where  CenterID=A.CenterID  and MemberLevelID=19 and MemberState=1 and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-10' and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-11'))) as '11월',
    (select count(*) from Members where  CenterID=A.CenterID  and MemberLevelID=19 and MemberState=1 and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-11' and MemberID in
    (SELECT MemberID from Classes where date_format(StartDateTime, '%Y-%m')  = '$SearchStartYear-12'))) as '12월'  
    from Centers A 
    left join Members B ON A.CenterID = B.CenterID
    WHERE B.MemberLevelID=19 and B.MemberState=1 and A.BranchID=$_LINK_ADMIN_BRANCH_ID_ and DATE_FORMAT(centerRegDateTime,'%Y') <= $SearchStartYear 
    GROUP by A.CenterID ";
    
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $i=0;
    while($Row = $Stmt->fetch()) {
        $CenterName[$i] = $Row["CenterName"];
        $CenterRegDate[$i] = $Row["regDate"];
        $Jan[$i] = $Row["1월"];
        $Feb[$i] = $Row["2월"];
        $Mar[$i] = $Row["3월"];
        $Apr[$i] = $Row["4월"];
        $May[$i] = $Row["5월"];
        $Jun[$i] = $Row["6월"];
        $Jul[$i] = $Row["7월"];
        $Aug[$i] = $Row["8월"];
        $Sep[$i] = $Row["9월"];
        $Oct[$i] = $Row["10월"];
        $Nov[$i] = $Row["11월"];
        $Dec[$i] = $Row["12월"];
        $i++;
    }

    // 신입생
    $Sql = "SELECT  A.CenterName, count(*), DATE_FORMAT(A.CenterRegDateTime,'%Y-%m-%d') as regDate,
                count(C.MemberID) as '1월',
                count(C2.MemberID) as '2월',
                count(C3.MemberID) as '3월',
                count(C4.MemberID) as '4월',
                count(C5.MemberID) as '5월',
                count(C6.MemberID) as '6월',
                count(C7.MemberID) as '7월',
                count(C8.MemberID) as '8월',
                count(C9.MemberID) as '9월',
                count(C10.MemberID) as '10월',
                count(C11.MemberID) as '11월',
                count(C12.MemberID) as '12월'
            from Centers A 
            left join Members B ON A.CenterID = B.CenterID 
            left join ClassOrders C on B.MemberID=C.MemberID and date_format(C.ClassOrderRegDateTime, '%Y-%m')  = '$SearchStartYear-01' and C.ClassOrderState=1 and C.ClassProgress=11
            left join ClassOrders C2 on B.MemberID=C2.MemberID and date_format(C2.ClassOrderRegDateTime, '%Y-%m')  = '$SearchStartYear-02' and C2.ClassOrderState=1 and C2.ClassProgress=11
            left join ClassOrders C3 on B.MemberID=C3.MemberID and date_format(C3.ClassOrderRegDateTime, '%Y-%m')  = '$SearchStartYear-03' and C3.ClassOrderState=1 and C3.ClassProgress=11
            left join ClassOrders C4 on B.MemberID=C4.MemberID and date_format(C4.ClassOrderRegDateTime, '%Y-%m')  = '$SearchStartYear-04' and C4.ClassOrderState=1 and C4.ClassProgress=11
            left join ClassOrders C5 on B.MemberID=C5.MemberID and date_format(C5.ClassOrderRegDateTime, '%Y-%m')  = '$SearchStartYear-05' and C5.ClassOrderState=1 and C5.ClassProgress=11
            left join ClassOrders C6 on B.MemberID=C6.MemberID and date_format(C6.ClassOrderRegDateTime, '%Y-%m')  = '$SearchStartYear-06' and C6.ClassOrderState=1 and C6.ClassProgress=11
            left join ClassOrders C7 on B.MemberID=C7.MemberID and date_format(C7.ClassOrderRegDateTime, '%Y-%m')  = '$SearchStartYear-07' and C7.ClassOrderState=1 and C7.ClassProgress=11
            left join ClassOrders C8 on B.MemberID=C8.MemberID and date_format(C8.ClassOrderRegDateTime, '%Y-%m')  = '$SearchStartYear-08' and C8.ClassOrderState=1 and C8.ClassProgress=11
            left join ClassOrders C9 on B.MemberID=C9.MemberID and date_format(C9.ClassOrderRegDateTime, '%Y-%m')  = '$SearchStartYear-09' and C9.ClassOrderState=1 and C9.ClassProgress=11
            left join ClassOrders C10 on B.MemberID=C10.MemberID and date_format(C10.ClassOrderRegDateTime, '%Y-%m')  = '$SearchStartYear-10' and C10.ClassOrderState=1 and C10.ClassProgress=11
            left join ClassOrders C11 on B.MemberID=C11.MemberID and date_format(C11.ClassOrderRegDateTime, '%Y-%m')  = '$SearchStartYear-11' and C11.ClassOrderState=1 and C11.ClassProgress=11
            left join ClassOrders C12 on B.MemberID=C12.MemberID and date_format(C12.ClassOrderRegDateTime, '%Y-%m')  = '$SearchStartYear-12' and C12.ClassOrderState=1 and C12.ClassProgress=11
            WHERE B.MemberLevelID=19 and B.MemberState=1 and A.BranchID=$_LINK_ADMIN_BRANCH_ID_ and DATE_FORMAT(centerRegDateTime,'%Y') <= $SearchStartYear 
            GROUP by A.CenterID";
    
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $i=0;
    while($Row = $Stmt->fetch()) {
        $CenterName2[$i] = $Row["CenterName"];
        $CenterRegDate2[$i] = $Row["regDate"];
        $Jan2[$i] = $Row["1월"];
        $Feb2[$i] = $Row["2월"];
        $Mar2[$i] = $Row["3월"];
        $Apr2[$i] = $Row["4월"];
        $May2[$i] = $Row["5월"];
        $Jun2[$i] = $Row["6월"];
        $Jul2[$i] = $Row["7월"];
        $Aug2[$i] = $Row["8월"];
        $Sep2[$i] = $Row["9월"];
        $Oct2[$i] = $Row["10월"];
        $Nov2[$i] = $Row["11월"];
        $Dec2[$i] = $Row["12월"];
        $i++;
    }
      

    // 탈락생
    $Sql = "SELECT  A.CenterName, count(*), DATE_FORMAT(A.CenterRegDateTime,'%Y-%m-%d') as regDate,
                count(C.MemberID) as '1월',
                count(C2.MemberID) as '2월',
                count(C3.MemberID) as '3월',
                count(C4.MemberID) as '4월',
                count(C5.MemberID) as '5월',
                count(C6.MemberID) as '6월',
                count(C7.MemberID) as '7월',
                count(C8.MemberID) as '8월',
                count(C9.MemberID) as '9월',
                count(C10.MemberID) as '10월',
                count(C11.MemberID) as '11월',
                count(C12.MemberID) as '12월'
            from Centers A 
            left join Members B ON A.CenterID = B.CenterID 
            left join ClassOrders C on B.MemberID=C.MemberID and date_format(C.ClassOrderModiDateTime, '%Y-%m')  = '$SearchStartYear-01' and C.ClassOrderState=3 
            left join ClassOrders C2 on B.MemberID=C2.MemberID and date_format(C2.ClassOrderModiDateTime, '%Y-%m')  = '$SearchStartYear-02' and C2.ClassOrderState=3 
            left join ClassOrders C3 on B.MemberID=C3.MemberID and date_format(C3.ClassOrderModiDateTime, '%Y-%m')  = '$SearchStartYear-03' and C3.ClassOrderState=3 
            left join ClassOrders C4 on B.MemberID=C4.MemberID and date_format(C4.ClassOrderModiDateTime, '%Y-%m')  = '$SearchStartYear-04' and C4.ClassOrderState=3 
            left join ClassOrders C5 on B.MemberID=C5.MemberID and date_format(C5.ClassOrderModiDateTime, '%Y-%m')  = '$SearchStartYear-05' and C5.ClassOrderState=3 
            left join ClassOrders C6 on B.MemberID=C6.MemberID and date_format(C6.ClassOrderModiDateTime, '%Y-%m')  = '$SearchStartYear-06' and C6.ClassOrderState=3 
            left join ClassOrders C7 on B.MemberID=C7.MemberID and date_format(C7.ClassOrderModiDateTime, '%Y-%m')  = '$SearchStartYear-07' and C7.ClassOrderState=3 
            left join ClassOrders C8 on B.MemberID=C8.MemberID and date_format(C8.ClassOrderModiDateTime, '%Y-%m')  = '$SearchStartYear-08' and C8.ClassOrderState=3 
            left join ClassOrders C9 on B.MemberID=C9.MemberID and date_format(C9.ClassOrderModiDateTime, '%Y-%m')  = '$SearchStartYear-09' and C9.ClassOrderState=3 
            left join ClassOrders C10 on B.MemberID=C10.MemberID and date_format(C10.ClassOrderModiDateTime, '%Y-%m')  = '$SearchStartYear-10' and C10.ClassOrderState=3 
            left join ClassOrders C11 on B.MemberID=C11.MemberID and date_format(C11.ClassOrderModiDateTime, '%Y-%m')  = '$SearchStartYear-11' and C11.ClassOrderState=3 
            left join ClassOrders C12 on B.MemberID=C12.MemberID and date_format(C12.ClassOrderModiDateTime, '%Y-%m')  = '$SearchStartYear-12' and C12.ClassOrderState=3 
            WHERE B.MemberLevelID=19 and B.MemberState=1 and A.BranchID=$_LINK_ADMIN_BRANCH_ID_ and DATE_FORMAT(centerRegDateTime,'%Y') <= $SearchStartYear 
            GROUP by A.CenterID                        
                                ";
    
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $i=0;
    while($Row = $Stmt->fetch()) {
        $CenterName3[$i] = $Row["CenterName"];
        $CenterRegDate3[$i] = $Row["regDate"];
        $Jan3[$i] = $Row["1월"];
        $Feb3[$i] = $Row["2월"];
        $Mar3[$i] = $Row["3월"];
        $Apr3[$i] = $Row["4월"];
        $May3[$i] = $Row["5월"];
        $Jun3[$i] = $Row["6월"];
        $Jul3[$i] = $Row["7월"];
        $Aug3[$i] = $Row["8월"];
        $Sep3[$i] = $Row["9월"];
        $Oct3[$i] = $Row["10월"];
        $Nov3[$i] = $Row["11월"];
        $Dec3[$i] = $Row["12월"];
        $i++;
    }

#====================================================================================================================#
}

//현재 날짜 세팅
$nowDate = date("Y-m"."-01");

?>
<div id="page_content">
    <div id="page_content_inner">
        <h3 class="heading_b uk-margin-bottom">학생수 데이터
        <?
        if($SearchStandard=="1" and $SearchStandardDetail=="7") {
             //echo $ArrTotalSubResultCont["Lesson"] ." / ". $ArrTotalSubResultCont["Quiz"];
        }
        ?> 
        </h3>

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

                            <!-- 재원생(지난달에도 수업했고 이번달에도 수업) -->
                            <h3>재원생(지난달에도 수업했고 이번달에도 수업)</h3>
                            <table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>순번</th>
										<th nowrap>학원명(대리점명)</th>
										<th nowrap>1월</th>
										<th nowrap>2월</th>
                                        <th nowrap>3월</th>
                                        <th nowrap>4월</th>
                                        <th nowrap>5월</th>
                                        <th nowrap>6월</th>
                                        <th nowrap>7월</th>
                                        <th nowrap>8월</th>
                                        <th nowrap>9월</th>
                                        <th nowrap>10월</th>
                                        <th nowrap>11월</th>
                                        <th nowrap>12월</th>
									</tr>
								</thead>
                                <?php for ($i=0;$i<count($CenterName);$i++){?>
                                <tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$i+1?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$CenterName[$i]?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-01-01"<=$nowDate && $SearchStartYear."-01-01">=$CenterRegDate[$i]?$Jan[$i]:"")?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-02-01"<=$nowDate && $SearchStartYear."-02-01">=$CenterRegDate[$i]?$Feb[$i]:"")?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-03-01"<=$nowDate && $SearchStartYear."-03-01">=$CenterRegDate[$i]?$Mar[$i]:"")?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-04-01"<=$nowDate && $SearchStartYear."-04-01">=$CenterRegDate[$i]?$Apr[$i]:"")?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-05-01"<=$nowDate && $SearchStartYear."-05-01">=$CenterRegDate[$i]?$May[$i]:"")?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-06-01"<=$nowDate && $SearchStartYear."-06-01">=$CenterRegDate[$i]?$Jun[$i]:"")?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-07-01"<=$nowDate && $SearchStartYear."-07-01">=$CenterRegDate[$i]?$Jul[$i]:"")?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-08-01"<=$nowDate && $SearchStartYear."-08-01">=$CenterRegDate[$i]?$Aug[$i]:"")?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-09-01"<=$nowDate && $SearchStartYear."-09-01">=$CenterRegDate[$i]?$Sep[$i]:"")?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-10-01"<=$nowDate && $SearchStartYear."-10-01">=$CenterRegDate[$i]?$Oct[$i]:"")?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-11-01"<=$nowDate && $SearchStartYear."-11-01">=$CenterRegDate[$i]?$Nov[$i]:"")?></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-12-01"<=$nowDate && $SearchStartYear."-12-01">=$CenterRegDate[$i]?$Dec[$i]:"")?></td>
                                </tr>

                                <?}?>
                                <tr>
                                <td class="uk-text-nowrap uk-table-td-center" colspan=2 style="font-weight:1000">합 계</td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-01-01"<=$nowDate && $SearchStartYear."-01-01">=$CenterRegDate[$i]?array_sum($Jan):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-02-01"<=$nowDate && $SearchStartYear."-02-01">=$CenterRegDate[$i]?array_sum($Feb):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-03-01"<=$nowDate && $SearchStartYear."-03-01">=$CenterRegDate[$i]?array_sum($Mar):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-04-01"<=$nowDate && $SearchStartYear."-04-01">=$CenterRegDate[$i]?array_sum($Apr):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-05-01"<=$nowDate && $SearchStartYear."-05-01">=$CenterRegDate[$i]?array_sum($May):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-06-01"<=$nowDate && $SearchStartYear."-06-01">=$CenterRegDate[$i]?array_sum($Jun):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-07-01"<=$nowDate && $SearchStartYear."-07-01">=$CenterRegDate[$i]?array_sum($Jul):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-08-01"<=$nowDate && $SearchStartYear."-08-01">=$CenterRegDate[$i]?array_sum($Aug):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-09-01"<=$nowDate && $SearchStartYear."-09-01">=$CenterRegDate[$i]?array_sum($Sep):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-10-01"<=$nowDate && $SearchStartYear."-10-01">=$CenterRegDate[$i]?array_sum($Oct):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-11-01"<=$nowDate && $SearchStartYear."-11-01">=$CenterRegDate[$i]?array_sum($Nov):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-12-01"<=$nowDate && $SearchStartYear."-12-01">=$CenterRegDate[$i]?array_sum($Dec):"")?></td>
                                </tr>

                            </table>            

                            <!------ GRAPH DIV ----->
                            <div id="chartdiv1"></div>

                            <!-- 신규생(지난달에 수업이 없었으나 이번달 새로 생긴 계정) -->
                            <h3>신규생(지난달에 수업이 없었으나 이번달 새로 생긴 계정)</h3>
                            <table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>순번</th>
										<th nowrap>학원명(대리점명)</th>
										<th nowrap>1월</th>
										<th nowrap>2월</th>
                                        <th nowrap>3월</th>
                                        <th nowrap>4월</th>
                                        <th nowrap>5월</th>
                                        <th nowrap>6월</th>
                                        <th nowrap>7월</th>
                                        <th nowrap>8월</th>
                                        <th nowrap>9월</th>
                                        <th nowrap>10월</th>
                                        <th nowrap>11월</th>
                                        <th nowrap>12월</th>
									</tr>
								</thead>
                                <?php for ($i=0;$i<count($CenterName2);$i++){?>
                                <tr>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=$i+1?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=$CenterName2[$i]?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-01-01"<=$nowDate && $SearchStartYear."-01-01">=$CenterRegDate2[$i]?$Jan2[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-02-01"<=$nowDate && $SearchStartYear."-02-01">=$CenterRegDate2[$i]?$Feb2[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-03-01"<=$nowDate && $SearchStartYear."-03-01">=$CenterRegDate2[$i]?$Mar2[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-04-01"<=$nowDate && $SearchStartYear."-04-01">=$CenterRegDate2[$i]?$Apr2[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-05-01"<=$nowDate && $SearchStartYear."-05-01">=$CenterRegDate2[$i]?$May2[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-06-01"<=$nowDate && $SearchStartYear."-06-01">=$CenterRegDate2[$i]?$Jun2[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-07-01"<=$nowDate && $SearchStartYear."-07-01">=$CenterRegDate2[$i]?$Jul2[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-08-01"<=$nowDate && $SearchStartYear."-08-01">=$CenterRegDate2[$i]?$Aug2[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-09-01"<=$nowDate && $SearchStartYear."-09-01">=$CenterRegDate2[$i]?$Sep2[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-10-01"<=$nowDate && $SearchStartYear."-10-01">=$CenterRegDate2[$i]?$Oct2[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-11-01"<=$nowDate && $SearchStartYear."-11-01">=$CenterRegDate2[$i]?$Nov2[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-12-01"<=$nowDate && $SearchStartYear."-12-01">=$CenterRegDate2[$i]?$Dec2[$i]:"")?></td>

                                </tr>
                                <? } ?>
                                <tr>
                                    <td class="uk-text-nowrap uk-table-td-center" colspan=2 style="font-weight:1000">합 계</td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-01-01"<=$nowDate && $SearchStartYear."-01-01">=$CenterRegDate2[$i]?array_sum($Jan2):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-02-01"<=$nowDate && $SearchStartYear."-02-01">=$CenterRegDate2[$i]?array_sum($Feb2):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-03-01"<=$nowDate && $SearchStartYear."-03-01">=$CenterRegDate2[$i]?array_sum($Mar2):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-04-01"<=$nowDate && $SearchStartYear."-04-01">=$CenterRegDate2[$i]?array_sum($Apr2):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-05-01"<=$nowDate && $SearchStartYear."-05-01">=$CenterRegDate2[$i]?array_sum($May2):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-06-01"<=$nowDate && $SearchStartYear."-06-01">=$CenterRegDate2[$i]?array_sum($Jun2):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-07-01"<=$nowDate && $SearchStartYear."-07-01">=$CenterRegDate2[$i]?array_sum($Jul2):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-08-01"<=$nowDate && $SearchStartYear."-08-01">=$CenterRegDate2[$i]?array_sum($Aug2):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-09-01"<=$nowDate && $SearchStartYear."-09-01">=$CenterRegDate2[$i]?array_sum($Sep2):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-10-01"<=$nowDate && $SearchStartYear."-10-01">=$CenterRegDate2[$i]?array_sum($Oct2):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-11-01"<=$nowDate && $SearchStartYear."-11-01">=$CenterRegDate2[$i]?array_sum($Nov2):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-12-01"<=$nowDate && $SearchStartYear."-12-01">=$CenterRegDate2[$i]?array_sum($Dec2):"")?></td>

                                </tr>

                            </table>  
                            <!------ GRAPH DIV ----->
                            <div id="chartdiv2"></div>

                            <!-- 탈락생(지난달까지 수업이 있었으나 이번달에 수업 없는 계정) -->
                            <h3>탈락생(지난달까지 수업이 있었으나 이번달에 수업 없는 계정)</h3>
                            <table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>순번</th>
										<th nowrap>학원명(대리점명)</th>
										<th nowrap>1월</th>
										<th nowrap>2월</th>
                                        <th nowrap>3월</th>
                                        <th nowrap>4월</th>
                                        <th nowrap>5월</th>
                                        <th nowrap>6월</th>
                                        <th nowrap>7월</th>
                                        <th nowrap>8월</th>
                                        <th nowrap>9월</th>
                                        <th nowrap>10월</th>
                                        <th nowrap>11월</th>
                                        <th nowrap>12월</th>
									</tr>
								</thead>
                                <?php for ($i=0;$i<count($CenterName2);$i++){?>
                                <tr>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=$i+1?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=$CenterName3[$i]?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-01-01"<=$nowDate && $SearchStartYear."-01-01">=$CenterRegDate3[$i]?$Jan3[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-02-01"<=$nowDate && $SearchStartYear."-02-01">=$CenterRegDate3[$i]?$Feb3[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-03-01"<=$nowDate && $SearchStartYear."-03-01">=$CenterRegDate3[$i]?$Mar3[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-04-01"<=$nowDate && $SearchStartYear."-04-01">=$CenterRegDate3[$i]?$Apr3[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-05-01"<=$nowDate && $SearchStartYear."-05-01">=$CenterRegDate3[$i]?$May3[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-06-01"<=$nowDate && $SearchStartYear."-06-01">=$CenterRegDate3[$i]?$Jun3[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-07-01"<=$nowDate && $SearchStartYear."-07-01">=$CenterRegDate3[$i]?$Jul3[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-08-01"<=$nowDate && $SearchStartYear."-08-01">=$CenterRegDate3[$i]?$Aug3[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-09-01"<=$nowDate && $SearchStartYear."-09-01">=$CenterRegDate3[$i]?$Sep3[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-10-01"<=$nowDate && $SearchStartYear."-10-01">=$CenterRegDate3[$i]?$Oct3[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-11-01"<=$nowDate && $SearchStartYear."-11-01">=$CenterRegDate3[$i]?$Nov3[$i]:"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"><?=($SearchStartYear."-12-01"<=$nowDate && $SearchStartYear."-12-01">=$CenterRegDate3[$i]?$Dec3[$i]:"")?></td>
                                </tr>
                                <? } ?>
                                <tr>
                                    <td class="uk-text-nowrap uk-table-td-center" colspan=2 style="font-weight:1000">합 계</td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-01-01"<=$nowDate && $SearchStartYear."-01-01">=$CenterRegDate3[$i]?array_sum($Jan3):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-02-01"<=$nowDate && $SearchStartYear."-02-01">=$CenterRegDate3[$i]?array_sum($Feb3):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-03-01"<=$nowDate && $SearchStartYear."-03-01">=$CenterRegDate3[$i]?array_sum($Mar3):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-04-01"<=$nowDate && $SearchStartYear."-04-01">=$CenterRegDate3[$i]?array_sum($Apr3):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-05-01"<=$nowDate && $SearchStartYear."-05-01">=$CenterRegDate3[$i]?array_sum($May3):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-06-01"<=$nowDate && $SearchStartYear."-06-01">=$CenterRegDate3[$i]?array_sum($Jun3):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-07-01"<=$nowDate && $SearchStartYear."-07-01">=$CenterRegDate3[$i]?array_sum($Jul3):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-08-01"<=$nowDate && $SearchStartYear."-08-01">=$CenterRegDate3[$i]?array_sum($Aug3):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-09-01"<=$nowDate && $SearchStartYear."-09-01">=$CenterRegDate3[$i]?array_sum($Sep3):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-10-01"<=$nowDate && $SearchStartYear."-10-01">=$CenterRegDate3[$i]?array_sum($Oct3):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-11-01"<=$nowDate && $SearchStartYear."-11-01">=$CenterRegDate3[$i]?array_sum($Nov3):"")?></td>
                                    <td class="uk-text-nowrap uk-table-td-center"  style="font-weight:1000;color:red"><?=($SearchStartYear."-12-01"<=$nowDate && $SearchStartYear."-12-01">=$CenterRegDate3[$i]?array_sum($Dec3):"")?></td>
                                </tr>
                                
                            </table>  
                            <!------ GRAPH DIV ----->
                            <div id="chartdiv3"></div>
                            
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
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->
egoryAxis5
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>


<script type="text/javascript">


function SearchSubmit(){
    document.SearchForm.action = "number_of_student_branch.php";
    document.SearchForm.submit();
}



    // Create chart instance
	var chart1 = am4core.create("chartdiv1", am4charts.XYChart);

	chart1.responsive.enabled = true;

	//월별 꺽은선 그래프용
	// Create xychart series
	var categoryAxis1 = chart1.xAxes.push(new am4charts.CategoryAxis());
	categoryAxis1.dataFields.category = "month";
	categoryAxis1.title.text = "월";
	categoryAxis1.renderer.grid.template.location = 0;
	categoryAxis1.renderer.minGridDistance = 30;


	var valueAxis1 = chart1.yAxes.push(new am4charts.ValueAxis());
	valueAxis1.title.text = "재원생 수";

	// 시리즈 생성. 대리점 수만큼 반복 생성
    <? for ($i=0;$i<count($CenterName);$i++)	{?>
	
    var series<?=$i?> = chart1.series.push(new am4charts.LineSeries());
	series<?=$i?>.dataFields.valueY = "studentNumber<?=$i?>";
	series<?=$i?>.dataFields.categoryX = "month";
	series<?=$i?>.name = "<?=$CenterName[$i]?>";
	series<?=$i?>.strokeWidth = 5;
    
    <?}?>

	
	// label bullet
	// var labelBullet1 = new am4charts.LabelBullet();
	// series1.bullets.push(labelBullet1);
	// labelBullet1.label.text = "{valueY.value.formatNumber('###,###,###,###')}";
	// labelBullet1.strokeOpacity = 0;
	// labelBullet1.stroke = am4core.color("#dadada");
	// labelBullet1.dy = 10;
	// labelBullet1.label.fontSize = 10;

	// php에서 만들어진 배열을 자바스크립트에 대입해 줌
	chart1.data = [
	<? for ($i=0;$i<=11;$i++)	{?>
	{
  		"month": "<?=$i+1?>월",
        <? for ($j=0;$j<count($CenterName);$j++)	{?>
  		    "studentNumber<?=$j?>": <?=${$MonthArr[$i]}[$j]?>,
        <?}?>
	},
	<?}?>
	];
	// And, for a good measure, let's add a legend
	chart1.legend = new am4charts.Legend();




    // Create chart instance
	var chart2 = am4core.create("chartdiv2", am4charts.XYChart);

    chart2.responsive.enabled = true;

    //월별 꺽은선 그래프용
    // Create xychart series
    var categoryAxis2 = chart2.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis2.dataFields.category = "month";
    categoryAxis2.title.text = "월";
    categoryAxis2.renderer.grid.template.location = 0;
    categoryAxis2.renderer.minGridDistance = 30;


    var valueAxis2 = chart2.yAxes.push(new am4charts.ValueAxis());
    valueAxis2.title.text = "신규생 수";

    // 시리즈 생성. 대리점 수만큼 반복 생성
    <? for ($i=0;$i<count($CenterName2);$i++)	{?>

    var series2<?=$i?> = chart2.series.push(new am4charts.LineSeries());
    series2<?=$i?>.dataFields.valueY = "studentNumber<?=$i?>";
    series2<?=$i?>.dataFields.categoryX = "month";
    series2<?=$i?>.name = "<?=$CenterName2[$i]?>";
    series2<?=$i?>.strokeWidth = 5;

    <?}?>


    // label bullet
    // var labelBullet1 = new am4charts.LabelBullet();
    // series1.bullets.push(labelBullet1);
    // labelBullet1.label.text = "{valueY.value.formatNumber('###,###,###,###')}";
    // labelBullet1.strokeOpacity = 0;
    // labelBullet1.stroke = am4core.color("#dadada");
    // labelBullet1.dy = 10;
    // labelBullet1.label.fontSize = 10;

    // php에서 만들어진 배열을 자바스크립트에 대입해 줌
    chart2.data = [
    <? for ($i=0;$i<=11;$i++)	{?>
    {
        "month": "<?=$i+1?>월",
        <? for ($j=0;$j<count($CenterName2);$j++)	{?>
            "studentNumber<?=$j?>": <?=${$MonthArr[$i]."2"}[$j]?>,
        <?}?>
    },
    <?}?>
    ];
    // And, for a good measure, let's add a legend
    chart2.legend = new am4charts.Legend();






    // Create chart instance
    var chart3 = am4core.create("chartdiv3", am4charts.XYChart);

    chart3.responsive.enabled = true;

    //월별 꺽은선 그래프용
    // Create xychart series
    var categoryAxis3 = chart3.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis3.dataFields.category = "month";
    categoryAxis3.title.text = "월";
    categoryAxis3.renderer.grid.template.location = 0;
    categoryAxis3.renderer.minGridDistance = 30;


    var valueAxis3 = chart3.yAxes.push(new am4charts.ValueAxis());
    valueAxis3.title.text = "탈락생 수";

    // 시리즈 생성. 대리점 수만큼 반복 생성
    <? for ($i=0;$i<count($CenterName3);$i++)	{?>

    var series3<?=$i?> = chart3.series.push(new am4charts.LineSeries());
    series3<?=$i?>.dataFields.valueY = "studentNumber<?=$i?>";
    series3<?=$i?>.dataFields.categoryX = "month";
    series3<?=$i?>.name = "<?=$CenterName3[$i]?>";
    series3<?=$i?>.strokeWidth = 5;

<?}?>


// label bullet
// var labelBullet1 = new am4charts.LabelBullet();
// series1.bullets.push(labelBullet1);
// labelBullet1.label.text = "{valueY.value.formatNumber('###,###,###,###')}";
// labelBullet1.strokeOpacity = 0;
// labelBullet1.stroke = am4core.color("#dadada");
// labelBullet1.dy = 10;
// labelBullet1.label.fontSize = 10;

// php에서 만들어진 배열을 자바스크립트에 대입해 줌
chart3.data = [
<? for ($i=0;$i<=11;$i++)	{?>
{
      "month": "<?=$i+1?>월",
    <? for ($j=0;$j<count($CenterName3);$j++)	{?>
          "studentNumber<?=$j?>": <?=${$MonthArr[$i]."3"}[$j]?>,
    <?}?>
},
<?}?>
];
// And, for a good measure, let's add a legend
chart3.legend = new am4charts.Legend();

</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>