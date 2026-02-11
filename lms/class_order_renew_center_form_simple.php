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
$MainMenuID = 14;
$SubMenuID = 1416;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
#===== 모바일 결제창에서 결제하지 않고 다시 돌아올경우 셀프페이에 남겨진 고유코드를 다시 재사용하기위한 변수 입니다. =====#

$ReqUrl = isset($_REQUEST["ReqUrl"]) ? $_REQUEST["ReqUrl"] : ""; //' 결제창에서 결제실행전 돌아올때
if ($ReqUrl!=""){
    ?>
    <script>
        // location.href = "class_order_renew_center_form.php";
        location.href = "class_order_renew_center_form_simple.php";
    </script>
    <?
}
?>


<?php
$ArrWeekDayStr = explode(",","일,월,화,수,목,금,토");

function getWeekCnt($s_date, $e_date, $week) { // $week 0:일 ~ 6:토
    $s = strtotime($s_date); // 타임스탬프로 변환
    $e = strtotime($e_date);
    $d = ceil(($e - $s) / 86400); // 두 날짜 사이의 일수 계산

    for($i=0, $cnt=0; $i<=$d; $i++) {
        $w = date("w",strtotime("+$i day",$s));
        if($w==$week) $cnt++;
    }
    return $cnt;
}


$FromDevice = isset($_COOKIE["FromDevice"]) ? $_COOKIE["FromDevice"] : "";



$AddSqlWhere = "1=1";

$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
$SearchFranchiseID = isset($_REQUEST["SearchFranchiseID"]) ? $_REQUEST["SearchFranchiseID"] : "";
$SearchCompanyID = isset($_REQUEST["SearchCompanyID"]) ? $_REQUEST["SearchCompanyID"] : "";
$SearchBranchGroupID = isset($_REQUEST["SearchBranchGroupID"]) ? $_REQUEST["SearchBranchGroupID"] : "";
$SearchBranchID = isset($_REQUEST["SearchBranchID"]) ? $_REQUEST["SearchBranchID"] : "";
$SearchCenterID = isset($_REQUEST["SearchCenterID"]) ? $_REQUEST["SearchCenterID"] : "";

$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

if ($SearchYear==""){
    $SearchYear = date("Y");
}
if ($SearchMonth==""){
    $SearchMonth = date("m");
}

$ThisYearMonthNum = $SearchYear . substr("0".$SearchMonth,-2);


$PrevSearchYear = $SearchYear;
$PrevSearchMonth = (int)$SearchMonth - 1;
if ($PrevSearchMonth<1){
    $PrevSearchMonth = 12;
    $PrevSearchYear = (int)$PrevSearchYear - 1;
}

$PrevPrevSearchYear = $PrevSearchYear;
$PrevPrevSearchMonth = (int)$PrevSearchMonth - 1;
if ($PrevPrevSearchMonth<1){
    $PrevPrevSearchMonth = 12;
    $PrevPrevSearchYear = (int)$PrevPrevSearchYear - 1;
}


$NextSearchYear = $SearchYear;
$NextSearchMonth = (int)$SearchMonth + 1;
if ($NextSearchMonth>12){
    $NextSearchMonth = 1;
    $NextSearchYear = (int)$NextSearchYear + 1;
}


$NextSearchMonthFirstDay = $NextSearchYear."-".substr("0".$NextSearchMonth,-2)."-01";
$NextSearchMonthLastDay = $NextSearchYear."-".substr("0".$NextSearchMonth,-2)."-".date("t", strtotime($NextSearchMonthFirstDay));


$SearchOrder = isset($_REQUEST["SearchOrder"]) ? $_REQUEST["SearchOrder"] : "";
//================== 서치폼 감추기 =================
$HideSearchCenterID = 0;
$HideSearchBranchID = 0;
$HideSearchBranchGroupID = 0;
$HideSearchCompanyID = 0;
$HideSearchFranchiseID = 0;

if ($_LINK_ADMIN_LEVEL_ID_==0 or $_LINK_ADMIN_LEVEL_ID_==1){//마스터
    //모두허용
}else if ($_LINK_ADMIN_LEVEL_ID_==3 or $_LINK_ADMIN_LEVEL_ID_==4){//프랜차이즈 관리자
    $SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;

    $HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==6 or $_LINK_ADMIN_LEVEL_ID_==7){//대표지사 관리자
    $SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
    $SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
    $SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;

    $HideSearchBranchGroupID = 1;
    $HideSearchCompanyID = 1;
    $HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==9 or $_LINK_ADMIN_LEVEL_ID_==10){//지사 관리자
    $SearchBranchID = $_LINK_ADMIN_BRANCH_ID_;
    $SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
    $SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
    $SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;

    $HideSearchBranchID = 1;
    $HideSearchBranchGroupID = 1;
    $HideSearchCompanyID = 1;
    $HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==12 or $_LINK_ADMIN_LEVEL_ID_==13){//대리점 관리자
    $SearchCenterID = $_LINK_ADMIN_CENTER_ID_;
    $SearchBranchID = $_LINK_ADMIN_BRANCH_ID_;
    $SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
    $SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
    $SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;

    $HideSearchCenterID = 1;
    $HideSearchBranchID = 1;
    $HideSearchBranchGroupID = 1;
    $HideSearchCompanyID = 1;
    $HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==15){//강사
    //접속불가
}
//================== 서치폼 감추기 =================

$CenterFreeTrialCount = 0;

$CenterPricePerGroup = 0;
$CenterPricePerTimeGroup = 0;
$CompanyPricePerTimeGroup = 0;

$DefaultCenterPricePerTime = 0;
$DefaultCompanyPricePerTime = 0;

$PrevSumClassOrderPayUseCashPrice = 0;
$PrevSumClassOrderPayB2bDifferencePrice = 0;
$PrePayClassOrderPayCount = 0;


$CenterRenewStartYear = 2100;
$CenterRenewStartMonth = 12;


if ($SearchCenterID!=""){

    $Sql = "SELECT 
				A.CenterFreeTrialCount,
				A.CenterPricePerGroup,
				A.CenterPricePerTime as CenterPricePerTimeGroup,
				A.CenterRenewStartYearMonthNum,
				D.CompanyPricePerTime as CompanyPricePerTimeGroup,
				A.CenterPricePerTime as DefaultCenterPricePerTime,
				D.CompanyPricePerTime as DefaultCompanyPricePerTime
			from Centers A 
				inner join Branches B on A.BranchID=B.BranchID 
				inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
				inner join Companies D on C.CompanyID=D.CompanyID 
			where A.CenterID=:CenterID 
	";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':CenterID', $SearchCenterID);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;

    $CenterFreeTrialCount = $Row["CenterFreeTrialCount"];
    $CenterPricePerGroup = $Row["CenterPricePerGroup"];
    $CenterPricePerTimeGroup = $Row["CenterPricePerTimeGroup"];
    $CenterRenewStartYearMonthNum = $Row["CenterRenewStartYearMonthNum"];
    $CompanyPricePerTimeGroup = $Row["CompanyPricePerTimeGroup"];

    $DefaultCenterPricePerTime = $Row["DefaultCenterPricePerTime"];
    $DefaultCompanyPricePerTime = $Row["DefaultCompanyPricePerTime"];

    $CenterRenewStartYear = substr($CenterRenewStartYearMonthNum, 0,4);
    $CenterRenewStartMonth = substr($CenterRenewStartYearMonthNum, -2);

    $CenterRenewStartYear = (int)$CenterRenewStartYear;
    $CenterRenewStartMonth = (int)$CenterRenewStartMonth;

    $Sql = "SELECT
                                count(*) as PrePayClassOrderPayCount
                        from ClassOrderPayB2bs A
                                inner join ClassOrderPays B on A.ClassOrderPayID=B.ClassOrderPayID
                        where
                                A.CenterID=:CenterID
                                and A.ClassOrderPayYear=:ClassOrderPayYear
                                and A.ClassOrderPayMonth=:ClassOrderPayMonth
                                and A.ClassOrderPayB2bState=1
                                and (B.ClassOrderPayProgress=21 or B.ClassOrderPayProgress=31 or B.ClassOrderPayProgress=41)
        ";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':CenterID', $SearchCenterID);
    $Stmt->bindParam(':ClassOrderPayYear', $SearchYear);
    $Stmt->bindParam(':ClassOrderPayMonth', $SearchMonth);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;
    $PrePayClassOrderPayCount = $Row["PrePayClassOrderPayCount"];//이번달 기 결제한 목록이 있는가?


    //선불결제 최초 지정한달 에는 차액결제를 하지 않는다. ========================================================
    if ($SearchYear==$CenterRenewStartYear && $SearchMonth==$CenterRenewStartMonth) {
        $PrePayClassOrderPayCount = 100;
    }
    //선불결제 최초 지정한달 에는 차액결제를 하지 않는다. ========================================================



    $Sql = "SELECT 
				ifnull(sum(A.ClassOrderPayUseCashPrice),0) as PrevSumClassOrderPayUseCashPrice,
				ifnull(sum(A.ClassOrderPayB2bDifferencePrice),0) as PrevSumClassOrderPayB2bDifferencePrice
			from ClassOrderPays A 
			where
				A.ClassOrderPayID in 
						(
							select 
								ClassOrderPayID 
							from ClassOrderPayB2bs where CenterID=:CenterID 
								and ClassOrderPayYear=:ClassOrderPayYear 
								and ClassOrderPayMonth=:ClassOrderPayMonth 
								and ClassOrderPayB2bState=1 
						)
	";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':CenterID', $SearchCenterID);
    $Stmt->bindParam(':ClassOrderPayYear', $SearchYear);
    $Stmt->bindParam(':ClassOrderPayMonth', $SearchMonth);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;
    $PrevSumClassOrderPayUseCashPrice = $Row["PrevSumClassOrderPayUseCashPrice"];
    $PrevSumClassOrderPayB2bDifferencePrice = $Row["PrevSumClassOrderPayB2bDifferencePrice"];


    //echo "<!-- $Sql -->";

    // SavedMoney(충전금 테이블)에서 현재 CenterID의 사용가능한 충전금 잔액을 가지고 온다. SavedMoneyState = 1 정상 충전금
    $Sql = "SELECT SUM(SavedMoney) AS SumOfSavedMoney FROM SavedMoney WHERE SavedMoneyState = 1 AND CenterID = :CenterID";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':CenterID', $SearchCenterID);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;
    $SumOfSavedMoney = $Row["SumOfSavedMoney"];

    if ($SumOfSavedMoney == "") $SumOfSavedMoney =0;


}



if (!$CurrentPage){
    $CurrentPage = 1;
}
if (!$PageListNum){
    $PageListNum = 30;
}


if ($SearchState==""){
    $SearchState = "1";
}


if ($SearchOrder==""){
    $SearchOrder = "1";
}

if ($SearchState!="100"){
    //$AddSqlWhere = $AddSqlWhere . " and A.MemberState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and (A.MemberState<>0 or A.MemberState is null) ";
$AddSqlWhere = $AddSqlWhere . " and (B.CenterState<>0 or B.CenterState is null)";
$AddSqlWhere = $AddSqlWhere . " and (C.BranchState<>0 or C.BranchState is null)";
$AddSqlWhere = $AddSqlWhere . " and (D.BranchGroupState<>0 or D.BranchGroupState is null)";
$AddSqlWhere = $AddSqlWhere . " and (E.CompanyState<>0 or E.CompanyState is null)";
$AddSqlWhere = $AddSqlWhere . " and (F.FranchiseState<>0 or F.FranchiseState is null)";

$AddSqlWhere = $AddSqlWhere . " and A.MemberLevelID=19 ";
$AddSqlWhere = $AddSqlWhere . " and A.MemberPayType=0  ";

if ($SearchText!=""){
    $AddSqlWhere = $AddSqlWhere . " and (A.MemberName like '%".$SearchText."%' or A.MemberLoginID like '%".$SearchText."%' or A.MemberNickName like '%".$SearchText."%') ";
}

if ($SearchFranchiseID!=""){
    $AddSqlWhere = $AddSqlWhere . " and E.FranchiseID=$SearchFranchiseID ";
}

if ($SearchCompanyID!=""){
    $AddSqlWhere = $AddSqlWhere . " and D.CompanyID=$SearchCompanyID ";
}

if ($SearchBranchGroupID!=""){
    $AddSqlWhere = $AddSqlWhere . " and C.BranchGroupID=$SearchBranchGroupID ";
}

if ($SearchBranchID!=""){
    $AddSqlWhere = $AddSqlWhere . " and B.BranchID=$SearchBranchID ";
}


if ($SearchCenterID!=""){
    $AddSqlWhere = $AddSqlWhere . " and A.CenterID=$SearchCenterID ";
}
$AddSqlWhere = $AddSqlWhere . " and AA.ClassProductID=1  ";
$AddSqlWhere = $AddSqlWhere . " and AA.ClassProgress=11  ";


$AddSqlWhere = $AddSqlWhere . " and (
										(AA.ClassMemberType=3 and AA.ClassMemberTypeGroupID>0) 
										or 
										(AA.ClassMemberType<>3) 
									)";

// ClassOrderState - 0:완전삭제 1:정상 2:종료대상 3:종료완료 4:장기홀드
//현재 정상, 종료대상 이거나
//지지난달 결제한 내역이 있거나
//지난달 수업을 진행한 내역이 있거나
$AddSqlWhere = $AddSqlWhere .  "and 
								(
									AA.ClassOrderState=1 
									or 
									AA.ClassOrderState=2 

									or 
									(AA.ClassOrderID in (select ClassOrderID from ClassOrderPayB2bs where ClassOrderPayB2bState=1 and ClassOrderPayLogDifferenceNextMonthPayMoney>0 and concat(ClassOrderPayYear , lpad(ClassOrderPayMonth,2,0))=".$PrevPrevSearchYear.substr("0".$PrevPrevSearchMonth,-2)." and ClassOrderID>0))
									or 
									(AA.ClassMemberTypeGroupID in (select ClassMemberTypeGroupID from ClassOrderPayB2bs where ClassOrderPayB2bState=1 and ClassOrderPayLogDifferenceNextMonthPayMoney>0 and concat(ClassOrderPayYear , lpad(ClassOrderPayMonth,2,0))=".$PrevPrevSearchYear.substr("0".$PrevPrevSearchMonth,-2)." and ClassMemberTypeGroupID>0))

									or 
									(AA.ClassOrderID in (select CLS.ClassOrderID from Classes CLS where CLS.ClassState=2 and CLS.ClassAttendState<>99 and concat(CLS.StartYear , lpad(CLS.StartMonth,2,0))=".$PrevSearchYear.substr("0".$PrevSearchMonth,-2)."))
									or 
									(AA.ClassMemberTypeGroupID in (select CO.ClassMemberTypeGroupID from Classes CLS inner join ClassOrders CO on CLS.ClassOrderID=CO.ClassOrderID where CLS.ClassState=2 and CLS.ClassAttendState<>99 and concat(CLS.StartYear , lpad(CLS.StartMonth,2,0))=".$PrevSearchYear.substr("0".$PrevSearchMonth,-2)." and CO.ClassMemberTypeGroupID>0))
								)
								";

//$AddSqlWhere = $AddSqlWhere . " and date_format(AA.ClassOrderStartDate, '%Y%m')>=".$PrevPrevSearchYear.substr("0".$PrevPrevSearchMonth,-2)." ";


$ClassOrderPayB2bsViewTable = "SELECT
		COPB.* 
	from ClassOrderPayB2bs COPB
		inner join ClassOrderPays COP on COPB.ClassOrderPayID=COP.ClassOrderPayID 
	where 
		COP.ClassOrderPayProgress=21 
		or COP.ClassOrderPayProgress=31 
		or COP.ClassOrderPayProgress=41
";

$ViewTable = "SELECT
			AA.ClassOrderID,
			AA.ClassMemberType,
			AA.ClassMemberTypeGroupID,
			AA.ClassOrderTimeTypeID,
			AA.ClassOrderWeekCountID,
			AA.ClassOrderStartDate,
			AA.ClassOrderEndDate,
			AA.ClassOrderState,

			date_format(AA.ClassOrderStartDate, '%Y%m') as ClassOrderStartMonthNum,

			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayB2bID, 0) 
				else 
					ifnull(BB.ClassOrderPayB2bID, 0) 
			end as ClassOrderPayB2bID,

			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogStartDate, 0) 
				else 
					ifnull(BB.ClassOrderPayLogStartDate, 0) 
			end as DbClassOrderPayLogStartDate,
			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogEndDate, 0) 
				else 
					ifnull(BB.ClassOrderPayLogEndDate, 0) 
			end as DbClassOrderPayLogEndDate,
			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogState, 0) 
				else 
					ifnull(BB.ClassOrderPayLogState, 0) 
			end as DbClassOrderPayLogState,
			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogWeekCount, 0) 
				else 
					ifnull(BB.ClassOrderPayLogWeekCount, 0) 
			end as DbClassOrderPayLogWeekCount,
			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogTeacherListInfo, 0) 
				else 
					ifnull(BB.ClassOrderPayLogTeacherListInfo, 0) 
			end as DbClassOrderPayLogTeacherListInfo,
			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogClassMemberType, 0) 
				else 
					ifnull(BB.ClassOrderPayLogClassMemberType, 0) 
			end as DbClassOrderPayLogClassMemberType,
			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogPrevPrevMonthPaidClassCount, 0) 
				else 
					ifnull(BB.ClassOrderPayLogPrevPrevMonthPaidClassCount, 0) 
			end as DbClassOrderPayLogPrevPrevMonthPaidClassCount,
			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogPrevPrevMonthPaidMoney, 0) 
				else 
					ifnull(BB.ClassOrderPayLogPrevPrevMonthPaidMoney, 0) 
			end as DbClassOrderPayLogPrevPrevMonthPaidMoney,
			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogPrevMonthEndClassCount, 0) 
				else 
					ifnull(BB.ClassOrderPayLogPrevMonthEndClassCount, 0) 
			end as DbClassOrderPayLogPrevMonthEndClassCount,
			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogPrevMonthUsedMoney, 0) 
				else 
					ifnull(BB.ClassOrderPayLogPrevMonthUsedMoney, 0) 
			end as DbClassOrderPayLogPrevMonthUsedMoney,
			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogDifferencePrevPrevMonthPaidMoney, 0) 
				else 
					ifnull(BB.ClassOrderPayLogDifferencePrevPrevMonthPaidMoney, 0) 
			end as DbClassOrderPayLogDifferencePrevPrevMonthPaidMoney,
			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogNextMonthClassCountInfo, 0) 
				else 
					ifnull(BB.ClassOrderPayLogNextMonthClassCountInfo, 0) 
			end as DbClassOrderPayLogNextMonthClassCountInfo,
			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogNextMonthPayMoney, 0) 
				else 
					ifnull(BB.ClassOrderPayLogNextMonthPayMoney, 0) 
			end as DbClassOrderPayLogNextMonthPayMoney,
			case
				when AA.ClassMemberType=3 then 
					ifnull(CC.ClassOrderPayLogDifferenceNextMonthPayMoney, 0) 
				else 
					ifnull(BB.ClassOrderPayLogDifferenceNextMonthPayMoney, 0) 
			end as DbClassOrderPayLogDifferenceNextMonthPayMoney,

			A.MemberName,
			A.MemberLoginID,
			B.CenterStudyEndDate,

			(
				select 
					count(*) 
				from ClassOrders AAAAA 
				where AAAAA.MemberID=AA.MemberID
					and (AAAAA.ClassOrderState=1 or AAAAA.ClassOrderState=2 or AAAAA.ClassOrderState=3 or AAAAA.ClassOrderState=4)
					and AAAAA.ClassProgress=11 
					and AAAAA.ClassProductID=1 
					and AAAAA.ClassOrderID<>AA.ClassOrderID 
			) as PreClassOrderCount

		from ClassOrders AA 
			left outer join ($ClassOrderPayB2bsViewTable) BB on AA.ClassOrderID=BB.ClassOrderID and BB.ClassOrderPayYear=".$SearchYear." and BB.ClassOrderPayMonth=".$SearchMonth." and BB.ClassOrderPayB2bState=1 and BB.ClassMemberType=1 
			left outer join ($ClassOrderPayB2bsViewTable) CC on AA.ClassMemberTypeGroupID=CC.ClassMemberTypeGroupID and CC.ClassOrderPayYear=".$SearchYear." and CC.ClassOrderPayMonth=".$SearchMonth." and CC.ClassOrderPayB2bState=1 and CC.ClassMemberType=3 

			inner join Members A on AA.MemberID=A.MemberID 
			inner join Centers B on A.CenterID=B.CenterID 
			inner join Branches C on B.BranchID=C.BranchID 
			inner join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
			inner join Companies E on D.CompanyID=E.CompanyID 
			inner join Franchises F on E.FranchiseID=F.FranchiseID 
		where ".$AddSqlWhere." 
";
//ClassMemberType : 2는 개별결제임으로 1로 취급 =========================


//echo "***".$ViewTable;

$Sql = "SELECT  
				count(*) TotalRowCount 
		from ($ViewTable) V 
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$Sql = "select V.* from ($ViewTable) V order by V.ClassMemberType, V.ClassMemberTypeGroupID, V.MemberName desc";



$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>

<!-- <?=$Sql?> -->
<div id="page_content">
    <div id="page_content_inner">

        <h3 class="heading_b uk-margin-bottom"><?=$수강연장_요약[$LangID]?></h3>

        <form name="SearchForm" method="get">
            <div class="md-card" style="margin-bottom:10px;">
                <div class="md-card-content">
                    <div class="uk-grid" data-uk-grid-margin="">
                        <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchFranchiseID==1){?>none<?}?>;">
                            <select id="SearchFranchiseID" name="SearchFranchiseID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="프랜차이즈선택" style="width:100%;"/>
                            <option value=""></option>
                            <?
                            $Sql2 = "select 
											A.* 
									from Franchises A 
									where A.FranchiseState<>0 
									order by A.FranchiseState asc, A.FranchiseName asc";
                            $Stmt2 = $DbConn->prepare($Sql2);
                            $Stmt2->execute();
                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);

                            $OldSelectFranchiseState = -1;
                            while($Row2 = $Stmt2->fetch()) {
                                $SelectFranchiseID = $Row2["FranchiseID"];
                                $SelectFranchiseName = $Row2["FranchiseName"];
                                $SelectFranchiseState = $Row2["FranchiseState"];

                                if ($OldSelectFranchiseState!=$SelectFranchiseState){
                                    if ($OldSelectFranchiseState!=-1){
                                        echo "</optgroup>";
                                    }

                                    if ($SelectFranchiseState==1){
                                        echo "<optgroup label=\"프랜차이즈(운영중)\">";
                                    }else if ($SelectFranchiseState==2){
                                        echo "<optgroup label=\"프랜차이즈(미운영)\">";
                                    }
                                }
                                $OldSelectFranchiseState = $SelectFranchiseState;
                                ?>

                                <option value="<?=$SelectFranchiseID?>" <?if ($SearchFranchiseID==$SelectFranchiseID){?>selected<?}?>><?=$SelectFranchiseName?></option>
                                <?
                            }
                            $Stmt2 = null;
                            ?>
                            </select>
                        </div>

                        <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchCompanyID==1){?>none<?}?>;">
                            <select id="SearchCompanyID" name="SearchCompanyID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="본사선택" style="width:100%;"/>
                            <option value=""></option>
                            <?
                            $AddWhere2 = "";
                            if ($SearchFranchiseID!=""){
                                $AddWhere2 = "and A.FranchiseID=".$SearchFranchiseID." ";
                            }else{
                                $AddWhere2 = " ";
                            }
                            $Sql2 = "SELECT  
											A.* 
									from Companies A 
										inner join Franchises B on A.FranchiseID=B.FranchiseID 
									where A.CompanyState<>0 and B.FranchiseState<>0 ".$AddWhere2." 
									order by A.CompanyState asc, A.CompanyName asc";
                            $Stmt2 = $DbConn->prepare($Sql2);
                            $Stmt2->execute();
                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);

                            $OldSelectCompanyState = -1;
                            while($Row2 = $Stmt2->fetch()) {
                                $SelectCompanyID = $Row2["CompanyID"];
                                $SelectCompanyName = $Row2["CompanyName"];
                                $SelectCompanyState = $Row2["CompanyState"];

                                if ($OldSelectCompanyState!=$SelectCompanyState){
                                    if ($OldSelectCompanyState!=-1){
                                        echo "</optgroup>";
                                    }

                                    if ($SelectCompanyState==1){
                                        echo "<optgroup label=\"본사(운영중)\">";
                                    }else if ($SelectCompanyState==2){
                                        echo "<optgroup label=\"본사(미운영)\">";
                                    }
                                }
                                $OldSelectCompanyState = $SelectCompanyState;
                                ?>

                                <option value="<?=$SelectCompanyID?>" <?if ($SearchCompanyID==$SelectCompanyID){?>selected<?}?>><?=$SelectCompanyName?></option>
                                <?
                            }
                            $Stmt2 = null;
                            ?>
                            </select>
                        </div>


                        <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
                            <select id="SearchBranchGroupID" name="SearchBranchGroupID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$대표지사선택[$LangID]?>" style="width:100%;"/>
                            <option value=""></option>
                            <?
                            $AddWhere2 = "";
                            if ($SearchCompanyID!=""){
                                $AddWhere2 = "and A.CompanyID=".$SearchCompanyID." ";
                            }else{
                                if ($SearchFranchiseID!=""){
                                    $AddWhere2 = "and B.FranchiseID=".$SearchFranchiseID." ";
                                }else{
                                    $AddWhere2 = " ";
                                }
                            }
                            $Sql2 = "SELECT  
											A.* 
										from BranchGroups A 
											inner join Companies B on A.CompanyID=B.CompanyID 
											inner join Franchises C on B.FranchiseID=C.FranchiseID 
										where A.BranchGroupState<>0 and B.CompanyState<>0 and C.FranchiseState<>0 ".$AddWhere2." 
										order by A.BranchGroupState asc, A.BranchGroupName asc";

                            $Stmt2 = $DbConn->prepare($Sql2);
                            $Stmt2->execute();
                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);

                            $OldSelectBranchGroupState = -1;
                            while($Row2 = $Stmt2->fetch()) {
                                $SelectBranchGroupID = $Row2["BranchGroupID"];
                                $SelectBranchGroupName = $Row2["BranchGroupName"];
                                $SelectBranchGroupState = $Row2["BranchGroupState"];

                                if ($OldSelectBranchGroupState!=$SelectBranchGroupState){
                                    if ($OldSelectBranchGroupState!=-1){
                                        echo "</optgroup>";
                                    }

                                    if ($SelectBranchGroupState==1){
                                        echo "<optgroup label=\"대표지사(운영중)\">";
                                    }else if ($SelectBranchGroupState==2){
                                        echo "<optgroup label=\"대표지사(미운영)\">";
                                    }
                                }
                                $OldSelectBranchGroupState = $SelectBranchGroupState;
                                ?>

                                <option value="<?=$SelectBranchGroupID?>" <?if ($SearchBranchGroupID==$SelectBranchGroupID){?>selected<?}?>><?=$SelectBranchGroupName?></option>
                                <?
                            }
                            $Stmt2 = null;
                            ?>
                            </select>
                        </div>
                        <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchID==1){?>none<?}?>;">
                            <select id="SearchBranchID" name="SearchBranchID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$지사선택[$LangID]?>" style="width:100%;"/>
                            <option value=""></option>
                            <?
                            $AddWhere2 = "";
                            if ($SearchBranchGroupID!=""){
                                $AddWhere2 = "and A.BranchGroupID=".$SearchBranchGroupID." ";
                            }else{
                                if ($SearchCompanyID!=""){
                                    $AddWhere2 = "and B.CompanyID=".$SearchCompanyID." ";
                                }else{
                                    if ($SearchFranchiseID!=""){
                                        $AddWhere2 = "and C.FranchiseID=".$SearchFranchiseID." ";
                                    }else{
                                        $AddWhere2 = " ";
                                    }
                                }
                            }

                            $Sql2 = "select 
											A.* 
									from Branches A 
										inner join BranchGroups B on A.BranchGroupID=B.BranchGroupID 
										inner join Companies C on B.CompanyID=C.CompanyID 
										inner join Franchises D on C.FranchiseID=D.FranchiseID 
									where A.BranchState<>0 and B.BranchGroupState<>0 and C.CompanyState<>0 and D.FranchiseState<>0 ".$AddWhere2." 
									order by A.BranchState asc, A.BranchName asc";

                            $Stmt2 = $DbConn->prepare($Sql2);
                            $Stmt2->execute();
                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);

                            $OldSelectBranchState = -1;
                            while($Row2 = $Stmt2->fetch()) {
                                $SelectBranchID = $Row2["BranchID"];
                                $SelectBranchName = $Row2["BranchName"];
                                $SelectBranchState = $Row2["BranchState"];

                                if ($OldSelectBranchState!=$SelectBranchState){
                                    if ($OldSelectBranchState!=-1){
                                        echo "</optgroup>";
                                    }

                                    if ($SelectBranchState==1){
                                        echo "<optgroup label=\"지사(운영중)\">";
                                    }else if ($SelectBranchState==2){
                                        echo "<optgroup label=\"지사(미운영)\">";
                                    }
                                }
                                $OldSelectBranchState = $SelectBranchState;
                                ?>

                                <option value="<?=$SelectBranchID?>" <?if ($SearchBranchID==$SelectBranchID){?>selected<?}?>><?=$SelectBranchName?></option>
                                <?
                            }
                            $Stmt2 = null;
                            ?>
                            </select>
                        </div>
                        <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchCenterID==1){?>none<?}?>;">
                            <select id="SearchCenterID" name="SearchCenterID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$대리점선택[$LangID]?>" style="width:100%;"/>
                            <option value=""></option>
                            <?

                            $AddWhere2 = "";
                            if ($SearchBranchID!=""){
                                $AddWhere2 = "and A.BranchID=".$SearchBranchID." ";
                            }else{
                                if ($SearchBranchGroupID!=""){
                                    $AddWhere2 = "and B.BranchGroupID=".$SearchBranchGroupID." ";
                                }else{
                                    if ($SearchCompanyID!=""){
                                        $AddWhere2 = "and C.CompanyID=".$SearchCompanyID." ";
                                    }else{
                                        if ($SearchFranchiseID!=""){
                                            $AddWhere2 = "and D.FranchiseID=".$SearchFranchiseID." ";
                                        }else{
                                            $AddWhere2 = " ";
                                        }
                                    }
                                }
                            }

                            $AddWhere2 = $AddWhere2." and A.CenterPayType=1 ";
                            $AddWhere2 = $AddWhere2." and A.CenterRenewType=1 ";//무결제가 아닌 학원

                            $Sql2 = "select 
											A.* 
									from Centers A 
										inner join Branches B on A.BranchID=B.BranchID 
										inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
										inner join Companies D on C.CompanyID=D.CompanyID 
										inner join Franchises E on D.FranchiseID=E.FranchiseID 
									where A.CenterState<>0 and B.BranchState<>0 and C.BranchGroupState<>0 and D.CompanyState<>0 and E.FranchiseState<>0 ".$AddWhere2." 
									order by A.CenterState asc, A.CenterName asc";
                            $Stmt2 = $DbConn->prepare($Sql2);
                            $Stmt2->execute();
                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);

                            $OldSelectBranchState = -1;
                            while($Row2 = $Stmt2->fetch()) {
                                $SelectCenterID = $Row2["CenterID"];
                                $SelectCenterName = $Row2["CenterName"];
                                $SelectCenterState = $Row2["CenterState"];

                                if ($OldSelectBranchState!=$SelectCenterState){
                                    if ($OldSelectBranchState!=-1){
                                        echo "</optgroup>";
                                    }

                                    if ($SelectCenterState==1){
                                        echo "<optgroup label=\"대리점(운영중)\">";
                                    }else if ($SelectCenterState==2){
                                        echo "<optgroup label=\"대리점(미운영)\">";
                                    }
                                }
                                $OldSelectBranchState = $SelectCenterState;
                                ?>

                                <option value="<?=$SelectCenterID?>" <?if ($SearchCenterID==$SelectCenterID){?>selected<?}?>><?=$SelectCenterName?></option>
                                <?
                            }
                            $Stmt2 = null;
                            ?>
                            </select>
                        </div>

                        <div class="uk-width-medium-2-10" style="padding-top:7px;">
                            <select id="SearchYear" name="SearchYear" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%;"/>
                            <option value=""><?=$년도선택[$LangID]?></option>
                            <?
                            for ($iiii=$SearchYear-1;$iiii<=$SearchYear+1;$iiii++) {
                                ?>
                                <option value="<?=$iiii?>" <?if ($SearchYear==$iiii){?>selected<?}?>><?=$iiii?><?=$년[$LangID]?></option>
                                <?
                            }
                            ?>
                            </select>
                        </div>
                        <div class="uk-width-medium-2-10" style="padding-top:7px;">
                            <select id="SearchMonth" name="SearchMonth" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;"/>
                            <option value=""><?=$월선택[$LangID]?></option>
                            <?
                            for ($iiii=1;$iiii<=12;$iiii++) {
                                ?>
                                <option value="<?=$iiii?>" <?if ($SearchMonth==$iiii){?>selected<?}?>><?=$iiii?><?=$월월[$LangID]?></option>
                                <?
                            }
                            ?>
                            </select>
                        </div>


                        <div class="uk-width-medium-2-10" style="display:none;">
                            <label for="SearchText"><?=$학생명_또는_아이디[$LangID]?></label>
                            <input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
                        </div>

                        <!--
                        <div class="uk-width-medium-1-10">
                            <label for="product_search_price">Price</label>
                            <input type="text" class="md-input" id="product_search_price">
                        </div>
                        -->

                        <div class="uk-width-medium-2-10" style="display:none;">
                            <div class="uk-margin-small-top">
                                <select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
                                    <option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
                                    <option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$정상[$LangID]?></option>
                                    <option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$휴면[$LangID]?></option>
                                    <option value="3" <?if ($SearchState=="3"){?>selected<?}?>><?=$탈퇴[$LangID]?></option>
                                </select>
                            </div>
                        </div>

                        <!--
                        <div class="uk-width-medium-1-10">
                            <div class="uk-margin-top uk-text-nowrap">
                                <input type="checkbox" name="product_search_active" id="product_search_active" data-md-icheck/>
                                <label for="product_search_active" class="inline-label">Active</label>
                            </div>
                        </div>
                        -->

                        <div class="uk-width-medium-1-10" style="display:none;">
                            <div class="uk-margin-small-top">
                                <select id="SearchOrder" name="SearchOrder" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
                                    <option value="1" <?if ($SearchOrder=="1"){?>selected<?}?>><?=$이름으로_정렬[$LangID]?></option>
                                    <option value="2" <?if ($SearchOrder=="2"){?>selected<?}?>><?=$결제금액으로_정렬[$LangID]?></option>
                                </select>
                            </div>
                        </div>

                        <div class="uk-width-medium-1-10 uk-text-center">
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
                            <div style="margin:5px 0px;">

                                ※ 망고아이 수강연장은 선불결제로서 익월 예정 수강료를 결제합니다. 선불결제는 결제기간내 여러번 나누어 결제할 수 있습니다.
                                <br>
                                ※ 지난달 실수강료 차액(c)은 지지난달 선불 결제한 금액과 지난달 실제 학습한 수강료의 차액 입니다.
                                <br>
                                &nbsp; &nbsp; 지난달 실수강료 차액(c)이 마이너스(-) 이면 결제한 금액보다 실제 수강료가 초과한 상태로 이번달 결제시 추가 결제해야 할 금액 입니다.
                                <br>
                                &nbsp; &nbsp; 반대로 지난달 실수강료 차액(c)이 플러스(+) 이면 결제한 금액보다 실제 수강료가 미달된 상태로 이번달 결제시 차감되어 결제하게 됩니다.
                                <br>
                                ※ 지난달 실수강료 차액(c)은 이번달의 첫번째 결제시 모두 결제처리 됩니다. 다음 결제 부터는 지난달 진행한 수강료와 차액이 0으로 표시 됩니다.
                                <br>
                                <span style="color:red; font-weight:bold;">※ 본 페이지는 요약 보기 페이지입니다.</span> 상세 계산을 확인하시려면, <a href="/lms/class_order_renew_center_form.php" style="color:blue; font-weight:bold;">여기를 누르시거나</a> 좌측 메뉴에서 <span style="color:blue; font-weight:bold;">'학생관리 - 수강 연장(상세보기)'</span> 를 클릭하시기 바랍니다.
                            </div>

                            <form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
                                <input type="hidden" name="ClassOrderPayUseSavedMoneyPrice" id="ClassOrderPayUseSavedMoneyPrice" value=""><!--충전금 사용액-->
                                <input type="hidden" name="CheckBoxNums" id="CheckBoxNums" value="">
                                <input type="hidden" name="ClassOrderPayYear" id="ClassOrderPayYear" value="<?=$SearchYear?>">
                                <input type="hidden" name="ClassOrderPayMonth" id="ClassOrderPayMonth" value="<?=$SearchMonth?>">
                                <input type="hidden" name="CenterID" id="CenterID" value="<?=$SearchCenterID?>">

                                <input type="hidden" name="DefaultCenterPricePerTime" id="DefaultCenterPricePerTime" value="<?=$DefaultCenterPricePerTime?>">
                                <input type="hidden" name="DefaultCompanyPricePerTime" id="DefaultCompanyPricePerTime" value="<?=$DefaultCompanyPricePerTime?>">

                                <table class="uk-table uk-table-align-vertical" style="width:100%;">
                                    <thead>
                                    <tr>
                                        <th nowrap><input name="CheckAll_1" id="CheckAll_1" type="checkbox" onclick="CheckListAll(this)"></th>
                                        <th nowrap>No</th>
                                        <th nowrap><?=$학생명[$LangID]?><br><?=$_아이디[$LangID]?></th>
                                        <th nowrap style="display:none;"><?=$수강[$LangID]?><br><?=$시작일[$LangID]?><br><?=$종료일[$LangID]?><br><?=$상태[$LangID]?></th>
                                        <th nowrap style="display:none;"><?=$수강[$LangID]?><br><?=$회수[$LangID]?></th>
                                        <th nowrap style="display:none;"><?=$수업[$LangID]?><br><?=$스케줄[$LangID]?></th>
                                        <th nowrap style="display:none;"><?=$수업[$LangID]?><br><?=$타입[$LangID]?></th>
                                        <th nowrap style="display:none;"><?=$지지난달[$LangID]?><br><?=$결제한[$LangID]?><br><?=$수업수[$LangID]?></th>
                                        <th nowrap style="display:none;"><?=$지지난달[$LangID]?><br><?=$결제한[$LangID]?><br><?=$수강료[$LangID]?><br>(a)</th>
                                        <th nowrap style="display:none;"><?=$지난달[$LangID]?><br><?=$진행한[$LangID]?><br><?=$수업수[$LangID]?></th>
                                        <th nowrap style="display:none;"><?=$지난달[$LangID]?><br><?=$진행한[$LangID]?><br><?=$수강료[$LangID]?><br>(b)</th>
                                        <th nowrap style="display:none;"><?=$지난달[$LangID]?><br><?=$실수강료[$LangID]?><br><?=$차액[$LangID]?><br>(c=a-b)</th>
                                        <th nowrap style="display:none;"><?=$다음달[$LangID]?><br><?=$예상[$LangID]?><br><?=$수업수[$LangID]?></th>
                                        <th nowrap style="display:none;"><?=$다음달[$LangID]?><br><?=$예상[$LangID]?><br><?=$수강료[$LangID]?><br>(d)</th>

                                        <th nowrap style="display:none;"><?=$첫수강[$LangID]?><br><?=$무료체험[$LangID]?><br><?=$수업수[$LangID]?></th>
                                        <th nowrap style="display:none;"><?=$첫수강[$LangID]?><br><?=$무료체험[$LangID]?><br><?=$할인[$LangID]?><br>(e)</th>

                                        <th nowrap colspan="5"><b><?=$이번달[$LangID]?> <?=$결제할[$LangID]?> <?=$수강료[$LangID]?></b><br></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    $ListCount = 1;
                                    $ListPayCount = 0;

                                    $SumPrevPrevMonthPaidClassCount = 0;
                                    $SumPrevPrevMonthPaidMoney = 0;
                                    $SumPrevMonthEndClassCount = 0;
                                    $SumPrevMonthUsedMoney = 0;
                                    $SumDifferencePrevPrevMonthPaidMoney = 0;
                                    $SumNextMonthClassCount = 0;
                                    $SumNextMonthPayMoney = 0;
                                    $SumDifferenceNextMonthPayMoney = 0;

                                    $SumClassOrderPayLogCalCenterFreeTrialCount = 0;
                                    $SumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice = 0;

                                    if ($SearchCenterID!=""){


                                        $OldClassMemberTypeGroupID = 0;

                                        while($Row = $Stmt->fetch()) {

                                            $ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

                                            $ClassOrderID = $Row["ClassOrderID"];
                                            $ClassMemberTypeGroupID = $Row["ClassMemberTypeGroupID"];
                                            $ClassMemberType = $Row["ClassMemberType"];
                                            $ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
                                            $ClassOrderWeekCountID = $Row["ClassOrderWeekCountID"];
                                            $ClassOrderStartDate = $Row["ClassOrderStartDate"];
                                            $ClassOrderEndDate = $Row["ClassOrderEndDate"];//수강신청별 종료일
                                            $CenterStudyEndDate = $Row["CenterStudyEndDate"];//대리점 종료일
                                            $ClassOrderState = $Row["ClassOrderState"];
                                            $ClassOrderStartMonthNum = $Row["ClassOrderStartMonthNum"];

                                            $ClassOrderPayB2bID = $Row["ClassOrderPayB2bID"];

                                            $DbClassOrderPayLogStartDate = $Row["DbClassOrderPayLogStartDate"];
                                            $DbClassOrderPayLogEndDate = $Row["DbClassOrderPayLogEndDate"];
                                            $DbClassOrderPayLogState = $Row["DbClassOrderPayLogState"];
                                            $DbClassOrderPayLogWeekCount = $Row["DbClassOrderPayLogWeekCount"];
                                            $DbClassOrderPayLogTeacherListInfo = $Row["DbClassOrderPayLogTeacherListInfo"];
                                            $DbClassOrderPayLogClassMemberType = $Row["DbClassOrderPayLogClassMemberType"];
                                            $DbClassOrderPayLogPrevPrevMonthPaidClassCount = $Row["DbClassOrderPayLogPrevPrevMonthPaidClassCount"];
                                            $DbClassOrderPayLogPrevPrevMonthPaidMoney = $Row["DbClassOrderPayLogPrevPrevMonthPaidMoney"];
                                            $DbClassOrderPayLogPrevMonthEndClassCount = $Row["DbClassOrderPayLogPrevMonthEndClassCount"];
                                            $DbClassOrderPayLogPrevMonthUsedMoney = $Row["DbClassOrderPayLogPrevMonthUsedMoney"];
                                            $DbClassOrderPayLogDifferencePrevPrevMonthPaidMoney = $Row["DbClassOrderPayLogDifferencePrevPrevMonthPaidMoney"];
                                            $DbClassOrderPayLogNextMonthClassCountInfo = $Row["DbClassOrderPayLogNextMonthClassCountInfo"];
                                            $DbClassOrderPayLogNextMonthPayMoney = $Row["DbClassOrderPayLogNextMonthPayMoney"];
                                            $DbClassOrderPayLogDifferenceNextMonthPayMoney = $Row["DbClassOrderPayLogDifferenceNextMonthPayMoney"];

                                            $PreClassOrderCount = $Row["PreClassOrderCount"];

                                            $MemberName = $Row["MemberName"];
                                            $MemberLoginID = $Row["MemberLoginID"];


                                            //======================= 1:1 수업일때 무료수업 수강료 빼주기 ===============================
                                            if ($ClassOrderStartMonthNum!=$ThisYearMonthNum || $PreClassOrderCount>0){//수업 시작한 달이 이번달이 아니면 무료수업 없다. 기존 수강신청 이력이 있다면 무료수업 없다.
                                                $ClassOrderPayLogCalCenterFreeTrialCount = 0;
                                            }else{
                                                $ClassOrderPayLogCalCenterFreeTrialCount = $CenterFreeTrialCount;
                                            }
                                            $ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice = $ClassOrderPayLogCalCenterFreeTrialCount * ($DefaultCenterPricePerTime * $ClassOrderTimeTypeID);// 무료수업수 * 10분당기본가 * 수업시간(2 : 20분, 4 : 40분)
                                            //======================= 1:1 수업일때 무료수업 수강료 빼주기 ===============================


                                            //======================= 1:2 수업일때 무료수업 수강료 빼주기 ===============================
                                            if ($ClassMemberType==2){
                                                $ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice = $ClassOrderPayLogCalCenterFreeTrialCount * (round($DefaultCenterPricePerTime / 3 * 2, 0) * $ClassOrderTimeTypeID);// 무료수업수 * 10분당기본가 * 수업시간(2 : 20분, 4 : 40분)
                                            }
                                            //======================= 1:2 수업일때 무료수업 수강료 빼주기 ===============================


                                            //======================= G 수업일때 무료수업 수강료 빼주기 ===============================
                                            if ($ClassMemberType==3){
                                                $ClassOrderPayLogCalCenterFreeTrialCount = 0;
                                                $ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice = 0;
                                            }
                                            //======================= G 수업일때 무료수업 수강료 빼주기 ===============================



                                            if ($ClassMemberType==1){
                                                $StrClassMemberType = "(1:1)";
                                            }else if ($ClassMemberType==2){
                                                $StrClassMemberType = "(1:2)";
                                            }else if ($ClassMemberType==3){
                                                $StrClassMemberType = "(G)";
                                            }

                                            if ($ClassOrderState==0){
                                                $StrClassOrderState = "삭제";
                                            }else if ($ClassOrderState==1) {
                                                $StrClassOrderState = "정상";
                                            }else if ($ClassOrderState==2) {
                                                $StrClassOrderState = "종료대상";
                                            }else if ($ClassOrderState==3) {
                                                $StrClassOrderState = "종료완료";
                                            }else if ($ClassOrderState==4) {
                                                $StrClassOrderState = "장기홀드";
                                            }


                                            if ($ClassMemberType==3){

                                                if ($OldClassMemberTypeGroupID!=$ClassMemberTypeGroupID){

                                                    $AddSqlWhere = " 1=1 ";
                                                    $AddSqlWhere = $AddSqlWhere .  "and A.CenterID=".$SearchCenterID." ";
                                                    $AddSqlWhere = $AddSqlWhere .  "and 
													(
														(AA.ClassOrderState=1 or AA.ClassOrderState=2) 
														
														or 
														(AA.ClassOrderID in (select ClassOrderID from ClassOrderPayB2bs where ClassOrderPayB2bState=1 and concat(ClassOrderPayYear , lpad(ClassOrderPayMonth,2,0))=".$PrevPrevSearchYear.substr("0".$PrevPrevSearchMonth,-2)." ))
														or 
														(AA.ClassMemberTypeGroupID in (select ClassMemberTypeGroupID from ClassOrderPayB2bs where ClassOrderPayB2bState=1 and concat(ClassOrderPayYear , lpad(ClassOrderPayMonth,2,0))=".$PrevPrevSearchYear.substr("0".$PrevPrevSearchMonth,-2)." and ClassMemberTypeGroupID>0))

														or 
														(AA.ClassOrderID in (select CLS.ClassOrderID from Classes CLS where CLS.ClassState=2 and CLS.ClassAttendState<>99 and concat(CLS.StartYear , lpad(CLS.StartMonth,2,0))=".$PrevSearchYear.substr("0".$PrevSearchMonth,-2)."))
														or 
														(AA.ClassMemberTypeGroupID in (select CO.ClassMemberTypeGroupID from Classes CLS inner join ClassOrders CO on CLS.ClassOrderID=CO.ClassOrderID where CLS.ClassState=2 and CLS.ClassAttendState<>99 and concat(CLS.StartYear , lpad(CLS.StartMonth,2,0))=".$PrevSearchYear.substr("0".$PrevSearchMonth,-2)." and CO.ClassMemberTypeGroupID>0))
													)
													";

                                                    $Sql_Group_ViewTable = "
													select 
														AA.ClassOrderID,
														AA.ClassMemberType,
														AA.ClassMemberTypeGroupID,
														AA.ClassOrderTimeTypeID,
														AA.ClassOrderWeekCountID,
														AA.ClassOrderStartDate,
														AA.ClassOrderEndDate,
														AA.ClassOrderState,

														ifnull(BB.ClassOrderPayB2bID, 0) as ClassOrderPayB2bID, 

														A.MemberName,
														A.MemberLoginID,
														B.CenterStudyEndDate
													from ClassOrders AA 
														left outer join ClassOrderPayB2bs BB on AA.ClassOrderID=BB.ClassOrderID and BB.ClassOrderPayYear=".$SearchYear." and BB.ClassOrderPayMonth=".$SearchMonth." and BB.ClassOrderPayB2bState=1  
														inner join Members A on AA.MemberID=A.MemberID 
														inner join Centers B on A.CenterID=B.CenterID 
														inner join Branches C on B.BranchID=C.BranchID 
														inner join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
														inner join Companies E on D.CompanyID=E.CompanyID 
														inner join Franchises F on E.FranchiseID=F.FranchiseID 
													where
														".$AddSqlWhere." 
														and AA.ClassMemberTypeGroupID=".$ClassMemberTypeGroupID."
														
												";


                                                    $Sql_G = "select 
																count(*) GroupRowCount 
														from ($Sql_Group_ViewTable) V 
												";
                                                    $Stmt_G = $DbConn->prepare($Sql_G);
                                                    $Stmt_G->execute();
                                                    $Stmt_G->setFetchMode(PDO::FETCH_ASSOC);
                                                    $Row_G = $Stmt_G->fetch();
                                                    $Stmt_G = null;
                                                    $GroupRowCount = $Row_G["GroupRowCount"];

                                                    $Sql_G = "select 
																count(*) GroupRenewCount 
														from ($Sql_Group_ViewTable) V 
														where V.ClassOrderState=1 
												";
                                                    $Stmt_G = $DbConn->prepare($Sql_G);
                                                    $Stmt_G->execute();
                                                    $Stmt_G->setFetchMode(PDO::FETCH_ASSOC);
                                                    $Row_G = $Stmt_G->fetch();
                                                    $Stmt_G = null;
                                                    $GroupRenewCount = $Row_G["GroupRenewCount"];//정상 수업인 ClassOrders



                                                }

                                                $PrintListPayCount = 0;
                                                if ($ClassOrderPayB2bID==0 && $OldClassMemberTypeGroupID!=$ClassMemberTypeGroupID){
                                                    $ListPayCount++;

                                                    $PrintListPayCount = $ListPayCount;
                                                }


                                            }else{

                                                $PrintListPayCount = 0;
                                                if ($ClassOrderPayB2bID==0){
                                                    $ListPayCount++;

                                                    $PrintListPayCount = $ListPayCount;
                                                }

                                            }
                                            ?>
                                            <tr style="background-color:<?if ($ClassOrderPayB2bID!=0){?>#F9FCFF<?}?>;">
                                                <?
                                                if ($ClassMemberType==3){
                                                    if ($OldClassMemberTypeGroupID!=$ClassMemberTypeGroupID){
                                                        ?>
                                                        <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>">
                                                            <?if ($ClassOrderPayB2bID==0){?>
                                                                <input name="CheckBox_<?=$PrintListPayCount?>" id="CheckBox_<?=$PrintListPayCount?>" type="checkbox" value="<?=$ClassMemberType?>|<?=$ClassMemberTypeGroupID?>" onclick="CalcSumList()">
                                                            <?}else{?>
                                                                결제완료
                                                            <?}?>
                                                        </td>
                                                        <?
                                                    }
                                                }else{
                                                    ?>
                                                    <td class="uk-text-nowrap uk-table-td-center">
                                                        <?if ($ClassOrderPayB2bID==0){?>
                                                            <input name="CheckBox_<?=$PrintListPayCount?>" id="CheckBox_<?=$PrintListPayCount?>" type="checkbox" value="<?=$ClassMemberType?>|<?=$ClassOrderID?>" onclick="CalcSumList()">
                                                        <?}else{?>
                                                            결제완료
                                                        <?}?>
                                                    </td>
                                                    <?
                                                }
                                                ?>

                                                <td class="uk-text-nowrap uk-table-td-center">
                                                    <?=$ListCount?>
                                                    <br>
                                                    (
                                                    <?=$ClassOrderID?><?if ($ClassMemberType!=1){?>-<?=$ClassMemberTypeGroupID?><?}?>
                                                    )
                                                </td>
                                                <td class="uk-text-nowrap uk-table-td-center" style="line-height:1.5;">

                                                    <!-- 학생명(아이디) -->
                                                    <?=$MemberName?><br>(<?=$MemberLoginID?>)
                                                </td>
                                                <td class="uk-text-nowrap uk-table-td-center" style="display:none;">
                                                    <!-- 수강시작일 -->
                                                    <!--
											<?if ($ClassOrderPayB2bID==0){?>
												<?=$ClassOrderStartDate?>
											<?}else{?>
												<?=$DbClassOrderPayLogStartDate?>
											<?}?>
											-->

                                                    <?=$ClassOrderStartDate?>
                                                    <input type="hidden" name="ClassOrderPayLogStartDate_<?=$PrintListPayCount?>" id="ClassOrderPayLogStartDate_<?=$PrintListPayCount?>" value="<?=$ClassOrderStartDate?>">


                                                    <br>
                                                    <!-- 수강종료일 -->
                                                    <!--
											<?if ($ClassOrderPayB2bID==0){?>
												<?=$CenterStudyEndDate?>
											<?}else{?>
												<?=$DbClassOrderPayLogEndDate?>
											<?}?>
											-->
                                                    <?if ($ClassOrderEndDate==""){?>
                                                        -
                                                    <?}else{?>
                                                        <?=$ClassOrderEndDate?>
                                                    <?}?>
                                                    <input type="hidden" name="ClassOrderPayLogEndDate_<?=$PrintListPayCount?>" id="ClassOrderPayLogEndDate_<?=$PrintListPayCount?>" value="<?=$CenterStudyEndDate?>">


                                                    <br>
                                                    <!-- 수강상태 -->
                                                    <?
                                                    if ($ClassOrderPayB2bID==0){
                                                        ?>
                                                        <?=$StrClassOrderState?>
                                                        <?
                                                    }else{
                                                        if ($DbClassOrderPayLogState==0){
                                                            $StrDbClassOrderState = "<br><?=$삭제[$LangID]?>";
                                                        }else if ($DbClassOrderPayLogState==1) {
                                                            $StrDbClassOrderState = "<br><?=$정상[$LangID]?>";
                                                        }else if ($DbClassOrderPayLogState==2) {
                                                            $StrDbClassOrderState = "<br><?=$종료대상[$LangID]?>";
                                                        }else if ($DbClassOrderPayLogState==3) {
                                                            $StrDbClassOrderState = "<br><?=$종료완료[$LangID]?>";
                                                        }else if ($DbClassOrderPayLogState==4) {
                                                            $StrDbClassOrderState = "<br><?=$장기홀드[$LangID]?>";
                                                        }
                                                        ?>
                                                        <?=$StrDbClassOrderState?>
                                                        <?
                                                    }
                                                    ?>
                                                    <input type="hidden" name="ClassOrderPayLogState_<?=$PrintListPayCount?>" id="ClassOrderPayLogState_<?=$PrintListPayCount?>" value="<?=$ClassOrderState?>">
                                                </td>

                                                <td class="uk-text-nowrap uk-table-td-center" style="display:none;">
                                                    <!-- 수강회수 -->
                                                    <?if ($ClassOrderPayB2bID==0){?>
                                                        <?=$ClassOrderWeekCountID?>회/주 (<?=$ClassOrderTimeTypeID*10?>분)
                                                    <?}else{?>
                                                        <?=$DbClassOrderPayLogWeekCount?>
                                                    <?}?>
                                                    <input type="hidden" name="ClassOrderPayLogWeekCount_<?=$PrintListPayCount?>" id="ClassOrderPayLogWeekCount_<?=$PrintListPayCount?>" value="<?=$ClassOrderWeekCountID?>회/주 (<?=$ClassOrderTimeTypeID*10?>분)">
                                                </td>
                                                <td class="uk-text-nowrap uk-table-td-center" style="display:none;">
                                                    <!--수업스케줄-->
                                                    <?

                                                    $TargetClassOrderID = $ClassOrderID;
                                                    if ($ClassMemberType==3){//그룹일경우 정상적인 신청건을 가져온다.

                                                        $Sql_Target = "
													select 
														A.ClassOrderID 
													from 
														ClassOrders A
													where 
														A.ClassMemberTypeGroupID=".$ClassMemberTypeGroupID."
														and ( A.ClassOrderState=1 or A.ClassOrderState=2 )
														and A.ClassProgress=11 
													order by A.ClassOrderState asc 
													limit 0, 1
												";
                                                        $Stmt_Target = $DbConn->prepare($Sql_Target);
                                                        $Stmt_Target->execute();
                                                        $Stmt_Target->setFetchMode(PDO::FETCH_ASSOC);
                                                        $Row_Target = $Stmt_Target->fetch();
                                                        $Stmt_Target = null;

                                                        $TargetClassOrderID = $Row_Target["ClassOrderID"];
                                                        if (!$TargetClassOrderID) {
                                                            $TargetClassOrderID = $ClassOrderID;
                                                        }

                                                    }


                                                    $Sql_Slot = "SELECT
														AAA.TeacherID, 
														AAA.StudyTimeHour,
														AAA.StudyTimeMinute,
														AAA.StudyTimeWeek,
														AAA.ClassOrderSlotStartDate,
														AAA.ClassOrderSlotEndDate,
														BBB.TeacherName,
														CCC.ClassMemberTypeGroupID,
														CCC.ClassOrderStartDate,

														B.MemberPricePerTime,
														C.CenterPricePerTime,
														F.CompanyPricePerTime,
														H.TeacherPayTypeItemCenterPriceX,

														(date_format(AAA.ClassOrderSlotEndDate, '%Y%m')-".$NextSearchYear.substr("0".$NextSearchMonth,-2).") as CheckSlotEndDate
													from ClassOrderSlots AAA 
														inner join Teachers BBB on AAA.TeacherID=BBB.TeacherID 
														inner join ClassOrders CCC on AAA.ClassOrderID=CCC.ClassOrderID 

														inner join Members B on CCC.MemberID=B.MemberID 
														inner join Centers C on B.CenterID=C.CenterID 
														inner join Branches D on C.BranchID=D.BranchID 
														inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
														inner join Companies F on E.CompanyID=F.CompanyID 

														inner join Teachers G on AAA.TeacherID=G.TeacherID 
														inner join TeacherPayTypeItems H on G.TeacherPayTypeItemID=H.TeacherPayTypeItemID 

													where 
														AAA.ClassOrderID=".$TargetClassOrderID." and AAA.ClassOrderSlotState=1 and AAA.ClassOrderSlotType=1 and AAA.ClassOrderSlotMaster=1 
														and date_format(CCC.ClassOrderStartDate, '%Y%m')<=".$SearchYear.substr("0".$SearchMonth,-2)."
														and ( 
																(AAA.ClassOrderSlotStartDate is NULL and AAA.ClassOrderSlotEndDate is NULL ) 
																or 
																(date_format(AAA.ClassOrderSlotStartDate, '%Y%m')<=".$NextSearchYear.substr("0".$NextSearchMonth,-2)." and AAA.ClassOrderSlotEndDate is NULL ) 
																or 
																(AAA.ClassOrderSlotStartDate is NULL and date_format(AAA.ClassOrderSlotEndDate, '%Y%m')>=".$SearchYear.substr("0".$SearchMonth,-2)." ) 
																or 
																(date_format(AAA.ClassOrderSlotStartDate, '%Y%m')<=".$NextSearchYear.substr("0".$NextSearchMonth,-2)." and date_format(AAA.ClassOrderSlotEndDate, '%Y%m')>=".$SearchYear.substr("0".$SearchMonth,-2)." and datediff(AAA.ClassOrderSlotStartDate, AAA.ClassOrderSlotEndDate)<=0 ) 
															) 
														and (date_format(AAA.ClassOrderSlotEndDate, '%Y%m')>=".$SearchYear.substr("0".$SearchMonth,-2)." or AAA.ClassOrderSlotEndDate is NULL)
													order by AAA.StudyTimeWeek, AAA.StudyTimeHour asc, AAA.StudyTimeMinute asc  
											";


                                                    if ($ClassOrderPayB2bID==0){

                                                        $Stmt_Slot = $DbConn->prepare($Sql_Slot);
                                                        $Stmt_Slot->execute();
                                                        $Stmt_Slot->setFetchMode(PDO::FETCH_ASSOC);


                                                        $ClassOrderPayLogTeacherListInfo = "||";

                                                        while($Row_Slot = $Stmt_Slot->fetch()) {
                                                            $TeacherID = $Row_Slot["TeacherID"];
                                                            $StudyTimeHour = $Row_Slot["StudyTimeHour"];
                                                            $StudyTimeMinute = $Row_Slot["StudyTimeMinute"];
                                                            $StudyTimeWeek = $Row_Slot["StudyTimeWeek"];
                                                            $CheckSlotEndDate = $Row_Slot["CheckSlotEndDate"];
                                                            $TeacherName = $Row_Slot["TeacherName"];
                                                            $ClassOrderSlotStartDate = $Row_Slot["ClassOrderSlotStartDate"];
                                                            $ClassOrderSlotEndDate = $Row_Slot["ClassOrderSlotEndDate"];

                                                            $ClassMemberTypeGroupID = $Row_Slot["ClassMemberTypeGroupID"];
                                                            $ClassOrderStartDate = $Row_Slot["ClassOrderStartDate"];

                                                            $TeacherPayTypeItemCenterPriceX = $Row_Slot["TeacherPayTypeItemCenterPriceX"];

                                                            $StrStartEnd = "";
                                                            if ($ClassOrderSlotStartDate!=""){
                                                                $StrStartEnd = $ClassOrderSlotStartDate . "~";
                                                            }else{
                                                                $StrStartEnd = $ClassOrderStartDate . "~";
                                                            }
                                                            if ($ClassOrderSlotEndDate!=""){
                                                                if ($StrStartEnd==""){
                                                                    $StrStartEnd = $StrStartEnd . "~";
                                                                }
                                                                $StrStartEnd = $StrStartEnd . $ClassOrderSlotEndDate;
                                                            }

                                                            if ($StrStartEnd!=""){
                                                                $StrStartEnd = "[".$StrStartEnd."]";
                                                            }


                                                            $ClassOrderPayLogTeacherListInfo = $ClassOrderPayLogTeacherListInfo . $ArrWeekDayStr[$StudyTimeWeek]."(".$StudyTimeHour."시 ".$StudyTimeMinute."분) / ".$TeacherName. "|".$StrStartEnd."||";

                                                            ?>
                                                            <div style="display:inline-block;width:190px;"><?=$ArrWeekDayStr[$StudyTimeWeek]?>(<?=$StudyTimeHour?>시 <?=$StudyTimeMinute?>분) / <?=$TeacherName?></div>
                                                            <div style="display:inline-block;width:170px;"><?=$StrStartEnd?></div>

                                                            <br>
                                                            <?

                                                        }
                                                        $Stmt_Slot = null;

                                                    }else{
                                                        $ClassOrderPayLogTeacherListInfo = $DbClassOrderPayLogTeacherListInfo;

                                                        $ArrDbClassOrderPayLogTeacherListInfo = explode("||", $DbClassOrderPayLogTeacherListInfo);
                                                        for ($jj=1;$jj<=count($ArrDbClassOrderPayLogTeacherListInfo)-2;$jj++){
                                                            $ArrArrDbClassOrderPayLogTeacherListInfo = explode("|", $ArrDbClassOrderPayLogTeacherListInfo[$jj]);
                                                            ?>
                                                            <div style="display:inline-block;width:190px;"><?=$ArrArrDbClassOrderPayLogTeacherListInfo[0]?></div>
                                                            <div style="display:inline-block;width:170px;"><?=$ArrArrDbClassOrderPayLogTeacherListInfo[1]?></div>

                                                            <br>
                                                            <?
                                                        }
                                                    }
                                                    ?>
                                                    <input type="hidden" name="ClassOrderPayLogTeacherListInfo_<?=$PrintListPayCount?>" id="ClassOrderPayLogTeacherListInfo_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogTeacherListInfo?>">
                                                </td>
                                                <td class="uk-text-nowrap uk-table-td-center" style="display:none;">
                                                    <!--수업 타입-->

                                                    <?
                                                    if ($ClassOrderPayB2bID==0){
                                                        ?>
                                                        <?=$StrClassMemberType?>
                                                        <?
                                                    }else{

                                                        if ($DbClassOrderPayLogClassMemberType==1){
                                                            $StrDbClassMemberType = "(1:1)";
                                                        }else if ($ClassMemberType==2){
                                                            $StrDbClassMemberType = "(1:2)";
                                                        }else if ($ClassMemberType==3){
                                                            $StrDbClassMemberType = "(G)";
                                                        }
                                                        ?>
                                                        <?=$StrDbClassMemberType?>
                                                        <?
                                                    }
                                                    ?>
                                                    <input type="hidden" name="ClassOrderPayLogClassMemberType_<?=$PrintListPayCount?>" id="ClassOrderPayLogClassMemberType_<?=$PrintListPayCount?>" value="<?=$ClassMemberType?>">
                                                </td>

                                                <?
                                                //============================================================================================================================ AAA : 그룹수업
                                                if ($ClassMemberType==3){
                                                    ?>

                                                    <?
                                                    if ($OldClassMemberTypeGroupID!=$ClassMemberTypeGroupID){
                                                        ?>
                                                        <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>" style="display:none;">
                                                            <!--지지난달 결제한 수업수-->
                                                            <?
                                                            $Sql_PrevPrev = "
													select
															ifnull(sum(A.ClassOrderPayTotalClassCount),0) as PrevPrevMonthPaidClassCount,
															ifnull(sum(A.ClassOrderPayDetailPaymentPrice),0) as PrevPrevMonthPaidMoney
													from ClassOrderPayB2bDetails A 
														inner join ClassOrderPayB2bs B on A.ClassorderPayB2bID=B.ClassorderPayB2bID and B.ClassOrderPayB2bState=1 
													where 
														B.ClassOrderPayYear=".$PrevPrevSearchYear."
														and B.ClassOrderPayMonth=".$PrevPrevSearchMonth."
														and B.ClassMemberTypeGroupID=".$ClassMemberTypeGroupID." 
												";
                                                            $Stmt_PrevPrev = $DbConn->prepare($Sql_PrevPrev);
                                                            $Stmt_PrevPrev->execute();
                                                            $Stmt_PrevPrev->setFetchMode(PDO::FETCH_ASSOC);
                                                            $Row_PrevPrev = $Stmt_PrevPrev->fetch();
                                                            $Stmt_PrevPrev = null;
                                                            $PrevPrevMonthPaidClassCount = $Row_PrevPrev["PrevPrevMonthPaidClassCount"];
                                                            $PrevPrevMonthPaidMoney = $Row_PrevPrev["PrevPrevMonthPaidMoney"];


                                                            $SumPrevPrevMonthPaidClassCount = $SumPrevPrevMonthPaidClassCount + $PrevPrevMonthPaidClassCount;
                                                            ?>
                                                            <?=number_format($PrevPrevMonthPaidClassCount,0)?>
                                                            <input type="hidden" name="ClassOrderPayLogPrevPrevMonthPaidClassCount_<?=$PrintListPayCount?>" id="ClassOrderPayLogPrevPrevMonthPaidClassCount_<?=$PrintListPayCount?>" value="<?=$PrevPrevMonthPaidClassCount?>">
                                                        </td>
                                                        <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>" style="display:none;">
                                                            <!--지지난달 결제한 수강료(a)-->
                                                            <?

                                                            $SumPrevPrevMonthPaidMoney = $SumPrevPrevMonthPaidMoney + $PrevPrevMonthPaidMoney;
                                                            ?>
                                                            <?=number_format($PrevPrevMonthPaidMoney,0)?>
                                                            <input type="hidden" name="ClassOrderPayLogPrevPrevMonthPaidMoney_<?=$PrintListPayCount?>" id="ClassOrderPayLogPrevPrevMonthPaidMoney_<?=$PrintListPayCount?>" value="<?=$PrevPrevMonthPaidMoney?>">
                                                        </td>
                                                        <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>" style="display:none;">
                                                            <!--지난달 진행한 수업수-->
                                                            <?
                                                            $Sql_Class_ViewTable = "
													select 
														AAA.* 
													from Classes AAA 
														inner join ClassOrders BBB on AAA.ClassOrderID=BBB.ClassOrderID 
													where 
														BBB.ClassMemberTypeGroupID=".$ClassMemberTypeGroupID." 
														and AAA.ClassState=2 
														and AAA.ClassAttendState<>99 
														and AAA.StartYear=".$PrevSearchYear." 
														and AAA.StartMonth=".$PrevSearchMonth." 
													group by AAA.StartDateTimeStamp, AAA.TeacherID 
											
												";


                                                            $Sql_Class = "
													select 
														count(*) as PrevMonthEndClassCount 
													from ($Sql_Class_ViewTable) A 
												";
                                                            $Stmt_Class = $DbConn->prepare($Sql_Class);
                                                            $Stmt_Class->execute();
                                                            $Stmt_Class->setFetchMode(PDO::FETCH_ASSOC);
                                                            $Row_Class = $Stmt_Class->fetch();
                                                            $Stmt_Class = null;
                                                            $PrevMonthEndClassCount = $Row_Class["PrevMonthEndClassCount"];

                                                            $SumPrevMonthEndClassCount = $SumPrevMonthEndClassCount + $PrevMonthEndClassCount;
                                                            ?>
                                                            <?=$PrevMonthEndClassCount?>
                                                            <input type="hidden" name="ClassOrderPayLogPrevMonthEndClassCount_<?=$PrintListPayCount?>" id="ClassOrderPayLogPrevMonthEndClassCount_<?=$PrintListPayCount?>" value="<?=$PrevMonthEndClassCount?>">
                                                        </td>
                                                        <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>" style="display:none;">
                                                            <!--지난달 진행한 수강료(b)-->
                                                            <?
                                                            $Sql_Class = "
													select 
														A.*,
														H.TeacherPayTypeItemCenterPriceX
													from ($Sql_Class_ViewTable) A 
														inner join Teachers G on A.TeacherID=G.TeacherID 
														inner join TeacherPayTypeItems H on G.TeacherPayTypeItemID=H.TeacherPayTypeItemID 
												";
                                                            $Stmt_Class = $DbConn->prepare($Sql_Class);
                                                            $Stmt_Class->execute();
                                                            $Stmt_Class->setFetchMode(PDO::FETCH_ASSOC);

                                                            $PrevMonthUsedMoney = 0;
                                                            while($Row_Class = $Stmt_Class->fetch()) {

                                                                $TeacherID = $Row_Class["TeacherID"];
                                                                $TeacherPayTypeItemCenterPriceX = $Row_Class["TeacherPayTypeItemCenterPriceX"];

                                                                $CenterPricePerTime = $CenterPricePerGroup / 8;//20분 수업 4회 가격이 $CenterPricePerGroup 임. 따라서 8로 나누면 10분당 가격이 됨.
                                                                $CompanyPricePerTime = $CenterPricePerTime * ($CompanyPricePerTimeGroup/$CenterPricePerTimeGroup); //본사 판매가를 계산해 준다.

                                                                $PrevMonthUsedMoney = $PrevMonthUsedMoney + ($CenterPricePerTime * $TeacherPayTypeItemCenterPriceX * $ClassOrderTimeTypeID);//판매가 = 센터 단가 * 교사배수 * 슬랏(20분, 30분 수업)
                                                            }
                                                            $Stmt_Class = null;

                                                            if ($ClassOrderPayB2bID != 0){//같은달 두번째 결제 부터는 지난달 진행한 수강료 0으로
                                                                $PrevMonthUsedMoney = 0;   // 결제완료된 그룹행
                                                            }

                                                            $SumPrevMonthUsedMoney = $SumPrevMonthUsedMoney + $PrevMonthUsedMoney;
                                                            ?>
                                                            <?=number_format($PrevMonthUsedMoney,0)?>
                                                            <input type="hidden" name="ClassOrderPayLogPrevMonthUsedMoney_<?=$PrintListPayCount?>" id="ClassOrderPayLogPrevMonthUsedMoney_<?=$PrintListPayCount?>" value="<?=$PrevMonthUsedMoney?>">
                                                        </td>
                                                        <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>" style="display:none;">
                                                            <!--지지난달 실수강료 차액(c=a-b)-->
                                                            <?
                                                            $DifferencePrevPrevMonthPaidMoney = $PrevPrevMonthPaidMoney-$PrevMonthUsedMoney;
                                                            if ($ClassOrderPayB2bID != 0){//같은달 두번째 결제 부터는 차액을 0으로
                                                                $DifferencePrevPrevMonthPaidMoney = 0;
                                                            }

                                                            $SumDifferencePrevPrevMonthPaidMoney = $SumDifferencePrevPrevMonthPaidMoney + $DifferencePrevPrevMonthPaidMoney;


                                                            ?>
                                                            <?=number_format($DifferencePrevPrevMonthPaidMoney,0)?>
                                                            <input type="hidden" name="ClassOrderPayLogDifferencePrevPrevMonthPaidMoney_<?=$PrintListPayCount?>" id="ClassOrderPayLogDifferencePrevPrevMonthPaidMoney_<?=$PrintListPayCount?>" value="<?=$DifferencePrevPrevMonthPaidMoney?>">
                                                        </td>
                                                        <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>" style="display:none;">
                                                            <!--다음달 예상 수업수-->
                                                            <?
                                                            $Stmt_Slot = $DbConn->prepare($Sql_Slot);
                                                            $Stmt_Slot->execute();
                                                            $Stmt_Slot->setFetchMode(PDO::FETCH_ASSOC);

                                                            $ClassOrderPayLogNextMonthClassCountInfo = "||";
                                                            while($Row_Slot = $Stmt_Slot->fetch()) {
                                                                $StudyTimeWeek = $Row_Slot["StudyTimeWeek"];
                                                                $CheckSlotEndDate = $Row_Slot["CheckSlotEndDate"];
                                                                $ClassOrderSlotStartDate = $Row_Slot["ClassOrderSlotStartDate"];
                                                                $ClassOrderSlotEndDate = $Row_Slot["ClassOrderSlotEndDate"];

                                                                if ($CheckSlotEndDate<0 || ($ClassOrderState!=1 && $ClassOrderState!=2)){
                                                                    $NextMonthClassCount = 0;
                                                                }else{

                                                                    $ClassOrderSlotStartDateNum = str_replace("-","",$ClassOrderSlotStartDate);
                                                                    $ClassOrderSlotEndDateNum = str_replace("-","",$ClassOrderSlotEndDate);

                                                                    $NextSearchMonthFirstDayNum = str_replace("-","",$NextSearchMonthFirstDay);
                                                                    $NextSearchMonthLastDayNum = str_replace("-","",$NextSearchMonthLastDay);

                                                                    if ($ClassOrderSlotStartDate==""){
                                                                        $CheckSearchMonthFirstDay = $NextSearchMonthFirstDay;
                                                                    }else if ($ClassOrderSlotStartDateNum-$NextSearchMonthFirstDayNum>=0){
                                                                        $CheckSearchMonthFirstDay = $ClassOrderSlotStartDate;
                                                                    }else if ($ClassOrderSlotStartDateNum-$NextSearchMonthFirstDayNum<0){
                                                                        $CheckSearchMonthFirstDay = $NextSearchMonthFirstDay;
                                                                    }

                                                                    if ($ClassOrderSlotEndDate==""){
                                                                        $CheckSearchMonthLastDay = $NextSearchMonthLastDay;
                                                                    }else if ($ClassOrderSlotEndDateNum-$NextSearchMonthLastDayNum>=0){
                                                                        $CheckSearchMonthLastDay = $NextSearchMonthLastDay;
                                                                    }else if ($ClassOrderSlotEndDateNum-$NextSearchMonthLastDayNum<0){
                                                                        $CheckSearchMonthLastDay = $ClassOrderSlotEndDate;
                                                                    }

                                                                    $NextMonthClassCount = getWeekCnt($CheckSearchMonthFirstDay, $CheckSearchMonthLastDay, $StudyTimeWeek);

                                                                }

                                                                $SumNextMonthClassCount = $SumNextMonthClassCount + $NextMonthClassCount;

                                                                $ClassOrderPayLogNextMonthClassCountInfo = $ClassOrderPayLogNextMonthClassCountInfo . $NextMonthClassCount . "||";


                                                                ?>
                                                                <div style="display:inline-block;"><?=$NextMonthClassCount?></div>
                                                                <br>
                                                                <?

                                                            }
                                                            $Stmt_Slot = null;
                                                            ?>
                                                            <input type="hidden" name="ClassOrderPayLogNextMonthClassCountInfo_<?=$PrintListPayCount?>" id="ClassOrderPayLogNextMonthClassCountInfo_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogNextMonthClassCountInfo?>">
                                                        </td>
                                                        <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>" style="display:none;">
                                                            <!--다음달 예상 수강료(d)-->
                                                            <?
                                                            $Stmt_Slot = $DbConn->prepare($Sql_Slot);
                                                            $Stmt_Slot->execute();
                                                            $Stmt_Slot->setFetchMode(PDO::FETCH_ASSOC);

                                                            $NextMonthPayMoney = 0;

                                                            $ClassOrderPayLogTeacherIDs = "||";
                                                            $ClassOrderPayLogTeacherPayTypeItemCenterPriceXs = "||";
                                                            $ClassOrderPayLogCenterPricePerTimes = "||";
                                                            $ClassOrderPayLogCompanyPricePerTimes = "||";
                                                            $ClassOrderPayLogTotalClassCounts = "||";
                                                            $ClassOrderPayLogClassSlotCounts = "||";
                                                            $ClassOrderPayLogDetailPaymentPrices = "||";
                                                            while($Row_Slot = $Stmt_Slot->fetch()) {

                                                                $StudyTimeWeek = $Row_Slot["StudyTimeWeek"];
                                                                $CheckSlotEndDate = $Row_Slot["CheckSlotEndDate"];
                                                                $ClassOrderSlotStartDate = $Row_Slot["ClassOrderSlotStartDate"];
                                                                $ClassOrderSlotEndDate = $Row_Slot["ClassOrderSlotEndDate"];

                                                                $TeacherID = $Row_Slot["TeacherID"];

                                                                $CenterPricePerTime = $CenterPricePerGroup / 8;//20분 수업 4회 가격이 $CenterPricePerGroup 임. 따라서 8로 나누면 10분당 가격이 됨.
                                                                $CompanyPricePerTime = $CenterPricePerTime * ($CompanyPricePerTimeGroup/$CenterPricePerTimeGroup); //본사 판매가를 계산해 준다.

                                                                $TeacherPayTypeItemCenterPriceX = $Row_Slot["TeacherPayTypeItemCenterPriceX"];



                                                                if ($CheckSlotEndDate<0 || ($ClassOrderState!=1 && $ClassOrderState!=2)){
                                                                    $NextMonthClassCount = 0;
                                                                }else{

                                                                    $ClassOrderSlotStartDateNum = str_replace("-","",$ClassOrderSlotStartDate);
                                                                    $ClassOrderSlotEndDateNum = str_replace("-","",$ClassOrderSlotEndDate);

                                                                    $NextSearchMonthFirstDayNum = str_replace("-","",$NextSearchMonthFirstDay);
                                                                    $NextSearchMonthLastDayNum = str_replace("-","",$NextSearchMonthLastDay);

                                                                    if ($ClassOrderSlotStartDate==""){
                                                                        $CheckSearchMonthFirstDay = $NextSearchMonthFirstDay;
                                                                    }else if ($ClassOrderSlotStartDateNum-$NextSearchMonthFirstDayNum>=0){
                                                                        $CheckSearchMonthFirstDay = $ClassOrderSlotStartDate;
                                                                    }else if ($ClassOrderSlotStartDateNum-$NextSearchMonthFirstDayNum<0){
                                                                        $CheckSearchMonthFirstDay = $NextSearchMonthFirstDay;
                                                                    }

                                                                    if ($ClassOrderSlotEndDate==""){
                                                                        $CheckSearchMonthLastDay = $NextSearchMonthLastDay;
                                                                    }else if ($ClassOrderSlotEndDateNum-$NextSearchMonthLastDayNum>=0){
                                                                        $CheckSearchMonthLastDay = $NextSearchMonthLastDay;
                                                                    }else if ($ClassOrderSlotEndDateNum-$NextSearchMonthLastDayNum<0){
                                                                        $CheckSearchMonthLastDay = $ClassOrderSlotEndDate;
                                                                    }

                                                                    $NextMonthClassCount = getWeekCnt($CheckSearchMonthFirstDay, $CheckSearchMonthLastDay, $StudyTimeWeek);

                                                                }

                                                                $NextMonthPayMoney = $NextMonthPayMoney + ($CenterPricePerTime * $TeacherPayTypeItemCenterPriceX * $ClassOrderTimeTypeID * $NextMonthClassCount);//판매가 = 센터 단가 * 교사배수 * 슬랏(20분, 30분 수업 * 수업수)

                                                                $ClassOrderPayLogTeacherIDs = $ClassOrderPayLogTeacherIDs . $TeacherID ."||";
                                                                $ClassOrderPayLogTeacherPayTypeItemCenterPriceXs = $ClassOrderPayLogTeacherPayTypeItemCenterPriceXs . $TeacherPayTypeItemCenterPriceX ."||";
                                                                $ClassOrderPayLogCenterPricePerTimes = $ClassOrderPayLogCenterPricePerTimes . $CenterPricePerTime . "||";
                                                                $ClassOrderPayLogCompanyPricePerTimes = $ClassOrderPayLogCompanyPricePerTimes . $CompanyPricePerTime . "||";
                                                                $ClassOrderPayLogClassSlotCounts = $ClassOrderPayLogClassSlotCounts . $ClassOrderTimeTypeID . "||";
                                                                $ClassOrderPayLogTotalClassCounts = $ClassOrderPayLogTotalClassCounts . $NextMonthClassCount . "||";
                                                                $ClassOrderPayLogDetailPaymentPrices = $ClassOrderPayLogDetailPaymentPrices . ($CenterPricePerTime * $TeacherPayTypeItemCenterPriceX * $ClassOrderTimeTypeID * $NextMonthClassCount) . "||";


                                                            }
                                                            $Stmt_Slot = null;

                                                            $SumNextMonthPayMoney = $SumNextMonthPayMoney + $NextMonthPayMoney;
                                                            ?>
                                                            <?=number_format($NextMonthPayMoney,0)?>
                                                            <input type="hidden" name="ClassOrderPayLogNextMonthPayMoney_<?=$PrintListPayCount?>" id="ClassOrderPayLogNextMonthPayMoney_<?=$PrintListPayCount?>" value="<?=$NextMonthPayMoney?>">

                                                            <input type="hidden" name="ClassOrderPayLogTeacherIDs_<?=$PrintListPayCount?>" id="ClassOrderPayLogTeacherIDs_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogTeacherIDs?>">
                                                            <input type="hidden" name="ClassOrderPayLogTeacherPayTypeItemCenterPriceXs_<?=$PrintListPayCount?>" id="ClassOrderPayLogTeacherPayTypeItemCenterPriceXs_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogTeacherPayTypeItemCenterPriceXs?>">
                                                            <input type="hidden" name="ClassOrderPayLogCenterPricePerTimes_<?=$PrintListPayCount?>" id="ClassOrderPayLogCenterPricePerTimes_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogCenterPricePerTimes?>">
                                                            <input type="hidden" name="ClassOrderPayLogCompanyPricePerTimes_<?=$PrintListPayCount?>" id="ClassOrderPayLogCompanyPricePerTimes_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogCompanyPricePerTimes?>">
                                                            <input type="hidden" name="ClassOrderPayLogTotalClassCounts_<?=$PrintListPayCount?>" id="ClassOrderPayLogTotalClassCounts_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogTotalClassCounts?>">
                                                            <input type="hidden" name="ClassOrderPayLogClassSlotCounts_<?=$PrintListPayCount?>" id="ClassOrderPayLogClassSlotCounts_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogClassSlotCounts?>">
                                                            <input type="hidden" name="ClassOrderPayLogDetailPaymentPrices_<?=$PrintListPayCount?>" id="ClassOrderPayLogDetailPaymentPrices_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogDetailPaymentPrices?>">
                                                        </td>


                                                        <!-- 무료수업 관련 -->
                                                        <?
                                                        $SumClassOrderPayLogCalCenterFreeTrialCount = $SumClassOrderPayLogCalCenterFreeTrialCount + $ClassOrderPayLogCalCenterFreeTrialCount;
                                                        $SumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice = $SumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice + $ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice;

                                                        //단체수업은 무조건 0 이다.
                                                        ?>
                                                        <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>" style="display:none;">
                                                            <?=number_format($ClassOrderPayLogCalCenterFreeTrialCount,0)?>
                                                            <input type="hidden" name="ClassOrderPayLogCalCenterFreeTrialCount_<?=$PrintListPayCount?>" id="ClassOrderPayLogCalCenterFreeTrialCount_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogCalCenterFreeTrialCount?>">
                                                        </td>
                                                        <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>" style="display:none;">
                                                            <?=number_format($ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice,0)?>
                                                            <input type="hidden" name="ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice_<?=$PrintListPayCount?>" id="ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice?>">
                                                        </td>
                                                        <!-- 무료수업 관련 -->


                                                        <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>" colspan="5">
                                                            <?
                                                            $DifferenceNextMonthPayMoney = $NextMonthPayMoney-($PrevPrevMonthPaidMoney-$PrevMonthUsedMoney);

                                                            $SumDifferenceNextMonthPayMoney = $SumDifferenceNextMonthPayMoney + $DifferenceNextMonthPayMoney;
                                                            ?>
                                                            <?=number_format($DifferenceNextMonthPayMoney,0)?>
                                                            <input type="hidden" name="ClassOrderPayLogDifferenceNextMonthPayMoney_<?=$PrintListPayCount?>" id="ClassOrderPayLogDifferenceNextMonthPayMoney_<?=$PrintListPayCount?>" value="<?=$DifferenceNextMonthPayMoney?>">
                                                        </td>
                                                        <?
                                                        $OldClassMemberTypeGroupID=$ClassMemberTypeGroupID;
                                                    }
                                                    ?>

                                                    <?
                                                    //============================================================================================================================ AAA : 1:1 ~ 1:2 수업
                                                }else{
                                                    ?>

                                                    <td class="uk-text-nowrap uk-table-td-center" style="display:none;">
                                                        <!--지지난달 결제한 수업수-->
                                                        <?
                                                        $Sql_PrevPrev = "SELECT 
															ifnull(sum(A.ClassOrderPayTotalClassCount),0) as PrevPrevMonthPaidClassCount,
															ifnull(sum(A.ClassOrderPayDetailPaymentPrice),0) as PrevPrevMonthPaidMoney
													from ClassOrderPayB2bDetails A 
														inner join ClassOrderPayB2bs B on A.ClassorderPayB2bID=B.ClassorderPayB2bID and B.ClassOrderPayB2bState=1 
													where 
														B.ClassOrderPayYear=".$PrevPrevSearchYear."
														and B.ClassOrderPayMonth=".$PrevPrevSearchMonth."
														and B.ClassOrderID=".$ClassOrderID." 
												";
                                                        $Stmt_PrevPrev = $DbConn->prepare($Sql_PrevPrev);
                                                        $Stmt_PrevPrev->execute();
                                                        $Stmt_PrevPrev->setFetchMode(PDO::FETCH_ASSOC);
                                                        $Row_PrevPrev = $Stmt_PrevPrev->fetch();
                                                        $Stmt_PrevPrev = null;
                                                        $PrevPrevMonthPaidClassCount = $Row_PrevPrev["PrevPrevMonthPaidClassCount"];
                                                        $PrevPrevMonthPaidMoney = $Row_PrevPrev["PrevPrevMonthPaidMoney"];


                                                        $SumPrevPrevMonthPaidClassCount = $SumPrevPrevMonthPaidClassCount + $PrevPrevMonthPaidClassCount;
                                                        ?>
                                                        <?=number_format($PrevPrevMonthPaidClassCount,0)?>
                                                        <input type="hidden" name="ClassOrderPayLogPrevPrevMonthPaidClassCount_<?=$PrintListPayCount?>" id="ClassOrderPayLogPrevPrevMonthPaidClassCount_<?=$PrintListPayCount?>" value="<?=$PrevPrevMonthPaidClassCount?>">
                                                    </td>
                                                    <td class="uk-text-nowrap uk-table-td-center" style="display:none;">
                                                        <!--지지난달 결제한 수강료(a)-->
                                                        <?
                                                        $SumPrevPrevMonthPaidMoney = $SumPrevPrevMonthPaidMoney + $PrevPrevMonthPaidMoney;
                                                        ?>
                                                        <?=number_format($PrevPrevMonthPaidMoney,0)?>
                                                        <input type="hidden" name="ClassOrderPayLogPrevPrevMonthPaidMoney_<?=$PrintListPayCount?>" id="ClassOrderPayLogPrevPrevMonthPaidMoney_<?=$PrintListPayCount?>" value="<?=$PrevPrevMonthPaidMoney?>">
                                                    </td>
                                                    <td class="uk-text-nowrap uk-table-td-center" style="display:none;">
                                                        <!--지난달 진행한 수업수-->
                                                        <?
                                                        $Sql_Class_ViewTable = "
													select 
														AAA.* 
													from Classes AAA 
													where 
														AAA.ClassOrderID=".$ClassOrderID." 
														and AAA.ClassState=2 
														and AAA.ClassAttendState<>99 
														and AAA.StartYear=".$PrevSearchYear." 
														and AAA.StartMonth=".$PrevSearchMonth." 	
												";


                                                        $Sql_Class = "
													select 
														count(*) as PrevMonthEndClassCount 
													from ($Sql_Class_ViewTable) A 
												";
                                                        $Stmt_Class = $DbConn->prepare($Sql_Class);
                                                        $Stmt_Class->execute();
                                                        $Stmt_Class->setFetchMode(PDO::FETCH_ASSOC);
                                                        $Row_Class = $Stmt_Class->fetch();
                                                        $Stmt_Class = null;
                                                        $PrevMonthEndClassCount = $Row_Class["PrevMonthEndClassCount"];

                                                        $SumPrevMonthEndClassCount = $SumPrevMonthEndClassCount + $PrevMonthEndClassCount;
                                                        ?>
                                                        <?=$PrevMonthEndClassCount?>
                                                        <input type="hidden" name="ClassOrderPayLogPrevMonthEndClassCount_<?=$PrintListPayCount?>" id="ClassOrderPayLogPrevMonthEndClassCount_<?=$PrintListPayCount?>" value="<?=$PrevMonthEndClassCount?>">
                                                    </td>
                                                    <td class="uk-text-nowrap uk-table-td-center" style="display:none;">
                                                        <!--지난달 진행한 수강료(b)-->
                                                        <?
                                                        $Sql_Class = "
													select 
														A.*,
														B.MemberPricePerTime,
														C.CenterPricePerTime,
														F.CompanyPricePerTime,
														H.TeacherPayTypeItemCenterPriceX
													from ($Sql_Class_ViewTable) A 
														inner join Members B on A.MemberID=B.MemberID 
														inner join Centers C on B.CenterID=C.CenterID 
														inner join Branches D on C.BranchID=D.BranchID 
														inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
														inner join Companies F on E.CompanyID=F.CompanyID 

														inner join Teachers G on A.TeacherID=G.TeacherID 
														inner join TeacherPayTypeItems H on G.TeacherPayTypeItemID=H.TeacherPayTypeItemID 
												";
                                                        $Stmt_Class = $DbConn->prepare($Sql_Class);
                                                        $Stmt_Class->execute();
                                                        $Stmt_Class->setFetchMode(PDO::FETCH_ASSOC);

                                                        $PrevMonthUsedMoney = 0;
                                                        while($Row_Class = $Stmt_Class->fetch()) {

                                                            $TeacherID = $Row_Class["TeacherID"];
                                                            $MemberPricePerTime = $Row_Class["MemberPricePerTime"];
                                                            $CenterPricePerTime = $Row_Class["CenterPricePerTime"];
                                                            $CompanyPricePerTime = $Row_Class["CompanyPricePerTime"];
                                                            $TeacherPayTypeItemCenterPriceX = $Row_Class["TeacherPayTypeItemCenterPriceX"];


                                                            if ($MemberPricePerTime>0){
                                                                $CenterPricePerTime = $MemberPricePerTime;
                                                            }


                                                            if ($ClassMemberType==2){
                                                                $CenterPricePerTime = round($CenterPricePerTime / 3 * 2, 0);
                                                                $CompanyPricePerTime = round($CompanyPricePerTime / 3 * 2, 0);
                                                            }

                                                            $PrevMonthUsedMoney = $PrevMonthUsedMoney + ($CenterPricePerTime * $TeacherPayTypeItemCenterPriceX * $ClassOrderTimeTypeID);//판매가 = 센터 단가 * 교사배수 * 슬랏(20분, 30분 수업)

                                                        }
                                                        $Stmt_Class = null;

                                                        if ($ClassOrderPayB2bID != 0){//같은달 두번째 결제 부터는 지난달 진행한 수강료 0으로
                                                            $PrevMonthUsedMoney = 0;
                                                        }

                                                        $SumPrevMonthUsedMoney = $SumPrevMonthUsedMoney + $PrevMonthUsedMoney;
                                                        ?>
                                                        <?=number_format($PrevMonthUsedMoney,0)?>
                                                        <input type="hidden" name="ClassOrderPayLogPrevMonthUsedMoney_<?=$PrintListPayCount?>" id="ClassOrderPayLogPrevMonthUsedMoney_<?=$PrintListPayCount?>" value="<?=$PrevMonthUsedMoney?>">
                                                    </td>
                                                    <td class="uk-text-nowrap uk-table-td-center" style="display:none;">
                                                        <!--지지난달 실수강료 차액(c=a-b)-->
                                                        <?
                                                        $DifferencePrevPrevMonthPaidMoney = $PrevPrevMonthPaidMoney-$PrevMonthUsedMoney;
                                                        if ($ClassOrderPayB2bID != 0){//같은달 두번째 결제 부터는 차액을 0으로
                                                            $DifferencePrevPrevMonthPaidMoney = 0;
                                                        }

                                                        $SumDifferencePrevPrevMonthPaidMoney = $SumDifferencePrevPrevMonthPaidMoney + $DifferencePrevPrevMonthPaidMoney;
                                                        ?>
                                                        <?=number_format($DifferencePrevPrevMonthPaidMoney,0)?>
                                                        <input type="hidden" name="ClassOrderPayLogDifferencePrevPrevMonthPaidMoney_<?=$PrintListPayCount?>" id="ClassOrderPayLogDifferencePrevPrevMonthPaidMoney_<?=$PrintListPayCount?>" value="<?=$DifferencePrevPrevMonthPaidMoney?>">
                                                    </td>
                                                    <td class="uk-text-nowrap uk-table-td-center" style="display:none;">
                                                        <!--다음달 예상 수업수-->
                                                        <?
                                                        $Stmt_Slot = $DbConn->prepare($Sql_Slot);
                                                        $Stmt_Slot->execute();
                                                        $Stmt_Slot->setFetchMode(PDO::FETCH_ASSOC);

                                                        $ClassOrderPayLogNextMonthClassCountInfo = "||";
                                                        while($Row_Slot = $Stmt_Slot->fetch()) {
                                                            $StudyTimeWeek = $Row_Slot["StudyTimeWeek"];
                                                            $CheckSlotEndDate = $Row_Slot["CheckSlotEndDate"];
                                                            $ClassOrderSlotStartDate = $Row_Slot["ClassOrderSlotStartDate"];
                                                            $ClassOrderSlotEndDate = $Row_Slot["ClassOrderSlotEndDate"];

                                                            if ($CheckSlotEndDate<0 || ($ClassOrderState!=1 && $ClassOrderState!=2)){
                                                                $NextMonthClassCount = 0;
                                                            }else{

                                                                $ClassOrderSlotStartDateNum = str_replace("-","",$ClassOrderSlotStartDate);
                                                                $ClassOrderSlotEndDateNum = str_replace("-","",$ClassOrderSlotEndDate);

                                                                $NextSearchMonthFirstDayNum = str_replace("-","",$NextSearchMonthFirstDay);
                                                                $NextSearchMonthLastDayNum = str_replace("-","",$NextSearchMonthLastDay);

                                                                if ($ClassOrderSlotStartDate==""){
                                                                    $CheckSearchMonthFirstDay = $NextSearchMonthFirstDay;
                                                                }else if ($ClassOrderSlotStartDateNum-$NextSearchMonthFirstDayNum>=0){
                                                                    $CheckSearchMonthFirstDay = $ClassOrderSlotStartDate;
                                                                }else if ($ClassOrderSlotStartDateNum-$NextSearchMonthFirstDayNum<0){
                                                                    $CheckSearchMonthFirstDay = $NextSearchMonthFirstDay;
                                                                }

                                                                if ($ClassOrderSlotEndDate==""){
                                                                    $CheckSearchMonthLastDay = $NextSearchMonthLastDay;
                                                                }else if ($ClassOrderSlotEndDateNum-$NextSearchMonthLastDayNum>=0){
                                                                    $CheckSearchMonthLastDay = $NextSearchMonthLastDay;
                                                                }else if ($ClassOrderSlotEndDateNum-$NextSearchMonthLastDayNum<0){
                                                                    $CheckSearchMonthLastDay = $ClassOrderSlotEndDate;
                                                                }

                                                                $NextMonthClassCount = getWeekCnt($CheckSearchMonthFirstDay, $CheckSearchMonthLastDay, $StudyTimeWeek);

                                                            }

                                                            $SumNextMonthClassCount = $SumNextMonthClassCount + $NextMonthClassCount;

                                                            $ClassOrderPayLogNextMonthClassCountInfo = $ClassOrderPayLogNextMonthClassCountInfo . $NextMonthClassCount . "||";

                                                            ?>
                                                            <div style="display:inline-block;"><?=$NextMonthClassCount?></div>
                                                            <br>
                                                            <?

                                                        }
                                                        $Stmt_Slot = null;
                                                        ?>
                                                        <input type="hidden" name="ClassOrderPayLogNextMonthClassCountInfo_<?=$PrintListPayCount?>" id="ClassOrderPayLogNextMonthClassCountInfo_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogNextMonthClassCountInfo?>">
                                                        <input type="hidden" name="ClassOrderPayLogTotalClassCounts_<?=$PrintListPayCount?>" id="ClassOrderPayLogTotalClassCounts_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogTotalClassCounts?>">
                                                    </td>
                                                    <td class="uk-text-nowrap uk-table-td-center" style="display:none;">
                                                        <!--다음달 예상 수강료(d)-->
                                                        <?
                                                        $Stmt_Slot = $DbConn->prepare($Sql_Slot);
                                                        $Stmt_Slot->execute();
                                                        $Stmt_Slot->setFetchMode(PDO::FETCH_ASSOC);

                                                        $NextMonthPayMoney = 0;

                                                        $ClassOrderPayLogTeacherIDs = "||";
                                                        $ClassOrderPayLogTeacherPayTypeItemCenterPriceXs = "||";
                                                        $ClassOrderPayLogCenterPricePerTimes = "||";
                                                        $ClassOrderPayLogCompanyPricePerTimes = "||";
                                                        $ClassOrderPayLogTotalClassCounts = "||";
                                                        $ClassOrderPayLogClassSlotCounts = "||";
                                                        $ClassOrderPayLogDetailPaymentPrices = "||";
                                                        while($Row_Slot = $Stmt_Slot->fetch()) {

                                                            $StudyTimeWeek = $Row_Slot["StudyTimeWeek"];
                                                            $CheckSlotEndDate = $Row_Slot["CheckSlotEndDate"];
                                                            $ClassOrderSlotStartDate = $Row_Slot["ClassOrderSlotStartDate"];
                                                            $ClassOrderSlotEndDate = $Row_Slot["ClassOrderSlotEndDate"];

                                                            $TeacherID = $Row_Slot["TeacherID"];
                                                            $MemberPricePerTime = $Row_Slot["MemberPricePerTime"];
                                                            $CenterPricePerTime = $Row_Slot["CenterPricePerTime"];
                                                            $CompanyPricePerTime = $Row_Slot["CompanyPricePerTime"];
                                                            $TeacherPayTypeItemCenterPriceX = $Row_Slot["TeacherPayTypeItemCenterPriceX"];


                                                            if ($MemberPricePerTime>0){
                                                                $CenterPricePerTime = $MemberPricePerTime;
                                                            }


                                                            if ($CheckSlotEndDate<0 || ($ClassOrderState!=1 && $ClassOrderState!=2)){
                                                                $NextMonthClassCount = 0;
                                                            }else{

                                                                $ClassOrderSlotStartDateNum = str_replace("-","",$ClassOrderSlotStartDate);
                                                                $ClassOrderSlotEndDateNum = str_replace("-","",$ClassOrderSlotEndDate);

                                                                $NextSearchMonthFirstDayNum = str_replace("-","",$NextSearchMonthFirstDay);
                                                                $NextSearchMonthLastDayNum = str_replace("-","",$NextSearchMonthLastDay);

                                                                if ($ClassOrderSlotStartDate==""){
                                                                    $CheckSearchMonthFirstDay = $NextSearchMonthFirstDay;
                                                                }else if ($ClassOrderSlotStartDateNum-$NextSearchMonthFirstDayNum>=0){
                                                                    $CheckSearchMonthFirstDay = $ClassOrderSlotStartDate;
                                                                }else if ($ClassOrderSlotStartDateNum-$NextSearchMonthFirstDayNum<0){
                                                                    $CheckSearchMonthFirstDay = $NextSearchMonthFirstDay;
                                                                }

                                                                if ($ClassOrderSlotEndDate==""){
                                                                    $CheckSearchMonthLastDay = $NextSearchMonthLastDay;
                                                                }else if ($ClassOrderSlotEndDateNum-$NextSearchMonthLastDayNum>=0){
                                                                    $CheckSearchMonthLastDay = $NextSearchMonthLastDay;
                                                                }else if ($ClassOrderSlotEndDateNum-$NextSearchMonthLastDayNum<0){
                                                                    $CheckSearchMonthLastDay = $ClassOrderSlotEndDate;
                                                                }

                                                                $NextMonthClassCount = getWeekCnt($CheckSearchMonthFirstDay, $CheckSearchMonthLastDay, $StudyTimeWeek);

                                                            }

                                                            if ($ClassMemberType==2){
                                                                $CenterPricePerTime = round($CenterPricePerTime / 3 * 2, 0);
                                                                $CompanyPricePerTime = round($CompanyPricePerTime / 3 * 2, 0);
                                                            }

                                                            $NextMonthPayMoney = $NextMonthPayMoney + ($CenterPricePerTime * $TeacherPayTypeItemCenterPriceX * $ClassOrderTimeTypeID * $NextMonthClassCount);//판매가 = 센터 단가 * 교사배수 * 슬랏(20분, 30분 수업 * 수업수)

                                                            $ClassOrderPayLogTeacherIDs = $ClassOrderPayLogTeacherIDs . $TeacherID ."||";
                                                            $ClassOrderPayLogTeacherPayTypeItemCenterPriceXs = $ClassOrderPayLogTeacherPayTypeItemCenterPriceXs . $TeacherPayTypeItemCenterPriceX ."||";
                                                            $ClassOrderPayLogCenterPricePerTimes = $ClassOrderPayLogCenterPricePerTimes . $CenterPricePerTime . "||";
                                                            $ClassOrderPayLogCompanyPricePerTimes = $ClassOrderPayLogCompanyPricePerTimes . $CompanyPricePerTime . "||";
                                                            $ClassOrderPayLogClassSlotCounts = $ClassOrderPayLogClassSlotCounts . $ClassOrderTimeTypeID . "||";
                                                            $ClassOrderPayLogTotalClassCounts = $ClassOrderPayLogTotalClassCounts . $NextMonthClassCount . "||";
                                                            $ClassOrderPayLogDetailPaymentPrices = $ClassOrderPayLogDetailPaymentPrices . ($CenterPricePerTime * $TeacherPayTypeItemCenterPriceX * $ClassOrderTimeTypeID * $NextMonthClassCount) . "||";

                                                        }
                                                        $Stmt_Slot = null;

                                                        $SumNextMonthPayMoney = $SumNextMonthPayMoney + $NextMonthPayMoney;
                                                        ?>
                                                        <?=number_format($NextMonthPayMoney,0)?>
                                                        <input type="hidden" name="ClassOrderPayLogNextMonthPayMoney_<?=$PrintListPayCount?>" id="ClassOrderPayLogNextMonthPayMoney_<?=$PrintListPayCount?>" value="<?=$NextMonthPayMoney?>">

                                                        <input type="hidden" name="ClassOrderPayLogTeacherIDs_<?=$PrintListPayCount?>" id="ClassOrderPayLogTeacherIDs_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogTeacherIDs?>">
                                                        <input type="hidden" name="ClassOrderPayLogTeacherPayTypeItemCenterPriceXs_<?=$PrintListPayCount?>" id="ClassOrderPayLogTeacherPayTypeItemCenterPriceXs_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogTeacherPayTypeItemCenterPriceXs?>">
                                                        <input type="hidden" name="ClassOrderPayLogCenterPricePerTimes_<?=$PrintListPayCount?>" id="ClassOrderPayLogCenterPricePerTimes_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogCenterPricePerTimes?>">
                                                        <input type="hidden" name="ClassOrderPayLogCompanyPricePerTimes_<?=$PrintListPayCount?>" id="ClassOrderPayLogCompanyPricePerTimes_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogCompanyPricePerTimes?>">
                                                        <input type="hidden" name="ClassOrderPayLogTotalClassCounts_<?=$PrintListPayCount?>" id="ClassOrderPayLogTotalClassCounts_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogTotalClassCounts?>">
                                                        <input type="hidden" name="ClassOrderPayLogClassSlotCounts_<?=$PrintListPayCount?>" id="ClassOrderPayLogClassSlotCounts_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogClassSlotCounts?>">
                                                        <input type="hidden" name="ClassOrderPayLogDetailPaymentPrices_<?=$PrintListPayCount?>" id="ClassOrderPayLogDetailPaymentPrices_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogDetailPaymentPrices?>">
                                                    </td>

                                                    <!-- 무료수업 관련 -->
                                                    <?
                                                    $SumClassOrderPayLogCalCenterFreeTrialCount = $SumClassOrderPayLogCalCenterFreeTrialCount + $ClassOrderPayLogCalCenterFreeTrialCount;
                                                    $SumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice = $SumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice + $ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice;
                                                    ?>
                                                    <td class="uk-text-nowrap uk-table-td-center" style="display:none;">
                                                        <?=number_format($ClassOrderPayLogCalCenterFreeTrialCount,0)?>
                                                        <input type="hidden" name="ClassOrderPayLogCalCenterFreeTrialCount_<?=$PrintListPayCount?>" id="ClassOrderPayLogCalCenterFreeTrialCount_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogCalCenterFreeTrialCount?>">
                                                    </td>
                                                    <td class="uk-text-nowrap uk-table-td-center" style="display:none;">
                                                        <?=number_format($ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice,0)?>
                                                        <input type="hidden" name="ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice_<?=$PrintListPayCount?>" id="ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice_<?=$PrintListPayCount?>" value="<?=$ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice?>">
                                                    </td>
                                                    <!-- 무료수업 관련 -->

                                                    <td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>" colspan="5">
                                                        <?
                                                        $DifferenceNextMonthPayMoney = $NextMonthPayMoney-($PrevPrevMonthPaidMoney-$PrevMonthUsedMoney);

                                                        $SumDifferenceNextMonthPayMoney = $SumDifferenceNextMonthPayMoney + $DifferenceNextMonthPayMoney;
                                                        ?>
                                                        <?=number_format($DifferenceNextMonthPayMoney,0)?>
                                                        <input type="hidden" name="ClassOrderPayLogDifferenceNextMonthPayMoney_<?=$PrintListPayCount?>" id="ClassOrderPayLogDifferenceNextMonthPayMoney_<?=$PrintListPayCount?>" value="<?=$DifferenceNextMonthPayMoney?>">
                                                    </td>

                                                    <?
                                                }
                                                //============================================================================================================================ AAA : 1:1 ~ 1:2 수업
                                                ?>

                                            </tr>
                                            <?php
                                            $ListCount ++;

                                        }
                                    }
                                    $Stmt = null;
                                    ?>


                                    <tr style="display:<?if ($PrePayClassOrderPayCount>0){?>none<?}?>">
                                        <th nowrap><input name="CheckAll_2" id="CheckAll_2" type="checkbox" onclick="CheckListAll(this)"></th>
                                        <th nowrap>-</th>
                                        <th nowrap colspan="5">전체 합계</th>
<!--                                        <th nowrap>--><?php //=number_format($SumPrevPrevMonthPaidClassCount,0)?><!--</th>-->
<!--                                        <th nowrap>--><?php //=number_format($SumPrevPrevMonthPaidMoney,0)?><!--</th>-->
<!--                                        <th nowrap>--><?php //=number_format($SumPrevMonthEndClassCount,0)?><!--</th>-->
<!--                                        <th nowrap>--><?php //=number_format($SumPrevMonthUsedMoney,0)?><!--</th>-->
<!--                                        <th nowrap>--><?php //=number_format($SumDifferencePrevPrevMonthPaidMoney,0)?><!--</th>-->
<!--                                        <th nowrap>--><?php //=number_format($SumNextMonthClassCount,0)?><!--</th>-->
<!--                                        <th nowrap>--><?php //=number_format($SumNextMonthPayMoney,0)?><!--</th>-->
<!--                                        <th nowrap>--><?php //=number_format($SumClassOrderPayLogCalCenterFreeTrialCount,0)?><!--</th>-->
<!--                                        <th nowrap>--><?php //=number_format($SumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice,0)?><!--</th>-->
                                        <th nowrap><?=number_format($SumDifferenceNextMonthPayMoney,0)?></th>
                                    </tr>

                                    <tr style="display:<?if ($PrePayClassOrderPayCount==0){?>none<?}?>">
                                        <th nowrap style="background-color:#f1f1f1;color:#000000;"><input name="CheckAll_3" id="CheckAll_3" type="checkbox" onclick="CheckListAll(this)"></th>
                                        <th nowrap style="background-color:#f1f1f1;color:#000000;"></th>
                                        <th nowrap style="background-color:#f1f1f1;color:#000000;line-height:1.5;" colspan="5">
                                            이전 결제한 금액 합계 (연장한 수강료 합계 + 차액합계 )
                                            <br>
                                            (차액 : <?=number_format($PrevSumClassOrderPayB2bDifferencePrice,0)?>원 , 지지난달 선불 결제한 금액과 지난달 실제 수강한 금액의 차액, '-' 이면 이번달 첫번째 결제시 추가 결제됨 )
                                        </th>
                                        <th nowrap style="background-color:#f1f1f1;color:#000000;" colspan="9"></th>
                                        <th nowrap style="background-color:#f1f1f1;color:#000000;">
                                            <?=number_format($PrevSumClassOrderPayUseCashPrice,0)?>
                                        </th>
                                    </tr>


                                    <tr>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000;"></th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000;"></th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000;" colspan="5"><?=$선택한_목록_합계[$LangID]?></th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000; display:none;" id="CheckPaySumPrevPrevMonthPaidClassCount">0</th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000; display:none;" id="CheckPaySumPrevPrevMonthPaidMoney">0</th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000; display:none;" id="CheckPaySumPrevMonthEndClassCount">0</th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000; display:none;" id="CheckPaySumPrevMonthUsedMoney">0</th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000; display:none;" id="CheckPaySumDifferencePrevPrevMonthPaidMoney">0</th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000; display:none;" id="CheckPaySumNextMonthClassCount">0</th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000; display:none;" id="CheckPaySumNextMonthPayMoney">0</th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000; display:none;" id="CheckPaySumClassOrderPayLogCalCenterFreeTrialCount">0</th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000; display:none;" id="CheckPaySumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice">0</th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000;" id="CheckPaySumDifferenceNextMonthPayMoney">0</th>
                                    </tr>


                                    <tr>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000;"></th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000;"></th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000;" colspan="5"><?=$미선택_목록_차액_합계[$LangID]?></th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000; display:none;" colspan="4"></th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000; display:none;" id="NoCheckPaySumDifferencePrevPrevMonthPaidMoney">
                                            0
                                        </th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000; display:none;" colspan="4"></th>
                                        <th nowrap style="background-color:#c1c1c1;color:#000000;" id="NoCheckPaySumDifferencePrevPrevMonthPaidMoney2">
                                            0
                                        </th>
                                        <input type="hidden" name="ClassOrderPayB2bDifferencePrice" id="ClassOrderPayB2bDifferencePrice" value="0">
                                    </tr>

                                    <tr>
                                        <th nowrap style="background-color:#888888;color:#ffffff;"></th>
                                        <th nowrap style="background-color:#888888;color:#ffffff;"></th>
                                        <th nowrap style="background-color:#888888;color:#ffffff;" colspan="5"><?=$결제할_금액_합계[$LangID]?></th>
                                        <th nowrap style="background-color:#888888;color:#ffffff; display:none;" colspan="4"></th>
                                        <th nowrap style="background-color:#888888;color:#ffffff; display:none;" id="PaySumDifferencePrevPrevMonthPaidMoney">0</th>
                                        <th nowrap style="background-color:#888888;color:#ffffff; display:none;" colspan="4"></th>
                                        <th nowrap style="background-color:#888888;color:#ffffff;font-size:20px;" id="PaySumDifferenceNextMonthPayMoney">0</th>
                                    </tr>
                                    <tr>
                                        <th nowrap style="background-color:#FFE5E5;color:#000000;"></th>
                                        <th nowrap style="background-color:#FFE5E5;color:#000000;"></th>
                                        <th nowrap style="background-color:#FFE5E5;color:#000000;" colspan="5">수업료 충전금 잔액</th>
                                        <th nowrap style="background-color:#FFE5E5;color:#000000;" colspan="11" id="SumOfSavedMoney"><?=number_format($SumOfSavedMoney)?></th>
                                    </tr>
                                    <tr>
                                        <th nowrap style="background-color:#BFCFFF;color:#000000;"></th>
                                        <th nowrap style="background-color:#BFCFFF;color:#000000;"></th>
                                        <th nowrap style="background-color:#BFCFFF;color:#000000;" colspan="5">충전금 차감후 결제할 금액</th>
                                        <th nowrap style="background-color:#BFCFFF;color:#000000;font-size:20px;" colspan="11" >차감할 충전금 : <input  id="Deduction" type=text valeu='0' readonly>, 최종 결제금액 : <input  id="DeterminedMoney" type=text valeu='0' readonly></th>
                                    </tr>

                                    <?

                                    if ($SearchCenterID==""){
                                        ?>
                                        <tr>
                                            <td class="uk-text-nowrap uk-table-td-center" colspan="20" style="height:100px;"><?=$가맹점을_검색하시기_바랍니다[$LangID]?></td>
                                        </tr>
                                        <?
                                    }
                                    ?>

                                    </tbody>
                                </table>
                            </form>
                        </div>

                        <?if ($SearchCenterID!=""){?>
                            <div class="uk-width-1-1" >
                                <a type="button" id="savedMoneyButton" class="uk-align-right uk-button-primary uk-button-large uk-margin-medium-top"><?=$수업료충전[$LangID]?></a>
                                <?if ( $SearchYear>$CenterRenewStartYear || ($SearchYear==$CenterRenewStartYear && $SearchMonth>=$CenterRenewStartMonth)){?>
                                    <?if ($ThisYearMonthNum<=date("Ym")){?>
                                        <a type="button" href="javascript:PrePayOrder()" class="uk-align-right uk-button-primary uk-button-large uk-margin-medium-top"><?=$수강연장_요약[$LangID]?></a>
                                    <?}else{?>
                                        <?=$선택하신_월의_결제기간이_아닙니다[$LangID]?>
                                    <?}?>
                                <?}else{?>
                                    <?=$선불결제_수강연장은[$LangID]?><?=$CenterRenewStartYear?>년 <?=$CenterRenewStartMonth?><?=$월부터_가능합니다[$LangID]?>
                                <?}?>
                            </div>
                        <?}?>


                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



<!----------------------- KCP PC결제창을 띄우기위한 팝업(iframe 포함) ------------------------->
<div id='paylayer' class="wrap-loading" style="display:none; z-index:100000000;">
    <iframe id='kcppay' name='kcppay' width='100%' height='100%'></iframe>
</div>
<!------------------------------------------------------------------------------------>

<style type="text/css">
    /* 결제창을 위한 가상창 */
    .wrap-loading {
        z-index:+1;
        position: fixed;
        width:100%;
        height:100%;
        left:0;
        right:0;
        top:0;
        bottom:0;
        background: rgba(255,255,255,0.4); /*not in ie */
        filter: progid:DXImageTransform.Microsoft.Gradient(startColorstr='#808080', endColorstr='#eeeeee');    /* ie */
    }
</style>
<?
if ($SearchCenterID!=""){
    $Sql = "
			select 
					A.*,
					AES_DECRYPT(UNHEX(A.CenterPhone1),:EncryptionKey) as DecCenterPhone1,
					AES_DECRYPT(UNHEX(A.CenterPhone2),:EncryptionKey) as DecCenterPhone2,
					AES_DECRYPT(UNHEX(A.CenterPhone3),:EncryptionKey) as DecCenterPhone3,
					AES_DECRYPT(UNHEX(A.CenterEmail),:EncryptionKey) as DecCenterEmail

			from Centers A 
			where A.CenterID=:CenterID";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':CenterID', $SearchCenterID);
    $Stmt->bindParam(':EncryptionKey', $EncryptionKey);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;

    $CenterName = $Row["CenterName"];
    $CenterManagerName = $Row["CenterManagerName"];
    $CenterPhone1 = $Row["DecCenterPhone1"];
    $CenterPhone2 = $Row["DecCenterPhone2"];
    $CenterPhone3 = $Row["DecCenterPhone3"];
    $CenterEmail = $Row["DecCenterEmail"];

}
?>


<!------------------------------------------------------------------------------------>
<?
//$DefaultDomain2 = "localhost";   //테스트용
//$FrchBsUqCode    = "48091699472481";            // 판매사고유코드(망고아이 - 테스트)
$FrchBsUqCode    = "36049230468271";            // 판매사고유코드(망고아이)
$FrchBrUqCode    = "";							// 지점고유코드
$test_paysw      = "N";                         // 테스트결제시-Y, 실결제시-N
$pay_repaybutyn = "N";                         // 동일 주문번호 재결제 가능-Y, 재결제 불가-N
$conf_site_name  = "MANGOI";					// PC결제 - 상호[반드시 영문으로만지정] 
$domain_name     = "https://".$DefaultDomain2;   // 도메인
$url_close       = "https://".$DefaultDomain2 . "/lms/class_order_renew_center_close.php";        // PC 결제일 경우에 KCP결제창 닫기 페이지(사용자지정)

if ($FromDevice=="app"){
    $url_payreqhome  = "SelfPayExit";          // 결제요청페이지(사용자지정)
    $url_returnhome  = "SelfPayExit";      // 결제후 최종돌아갈 홈페이지(사용자지정)
    $pay_homekey   = "mangoi://kr.ahsol.mangoi";     // 앱-홈(사용자지정)
    $pay_replaceurl   = "http://localhost"; //앱에서 결제 종료후 시스템 브라우져 이동할 페이지
}else{
    $url_payreqhome  = "https://".$DefaultDomain2 . "/lms/class_order_renew_center_form.php";          // 결제요청페이지(사용자지정)
    $url_returnhome  = "https://".$DefaultDomain2 . "/lms/class_order_renew_center_form.php";      // 결제후 최종돌아갈 홈페이지(사용자지정)
    $pay_homekey   = "";     // 앱-홈(사용자지정)
    $pay_replaceurl   = ""; //앱에서 결제 종료후 시스템 브라우져 이동할 페이지
}
$url_result      = "https://".$DefaultDomain2 . "/lms/class_order_renew_center_result.php";       // 결제결과처리 페이지(사용자지정)
$url_result_json = "https://".$DefaultDomain2 . "/lms/class_order_renew_center_result_json.php";  // 결제결과처리 JSON 페이지(사용자지정) 
$url_result_curl = "https://".$DefaultDomain2 . "/lms/class_order_renew_center_result_curl.php";  // 결제결과처리 JSON 페이지(사용자지정)
$url_vbnotice    = "https://".$DefaultDomain2 . "/lms/class_order_renew_center_result_vbank.php";        // 가상계좌 결제결과 통보처리 페이지(사용자지정)
$url_retmethod   = "curl";                                                       // 결과값 처리방법 (curl, iframe)

$ReqUrl  = isset($_REQUEST["ReqUrl"])  ? $_REQUEST["ReqUrl"]  : "";               // 결제창에서 결제실행전 돌아올때
$TradeNo = isset($_REQUEST["TradeNo"]) ? $_REQUEST["TradeNo"] : "";               // 결제완료 후 홈으로 리턴시 거래번호를 가져옴


?>
<!------------------------------------------------------------------------------------>

<div style="display:none;">
    <form id="SendPayForm" name="SendPayForm" method="POST">
        <input type="hidden" name="Frch_BsUqCode"   value="<?=$FrchBsUqCode ?>">
        <input type="hidden" name="Frch_BrUqCode"   value="<?=$FrchBrUqCode ?>">
        <input type="hidden" name="TestPay"         value="<?=$test_paysw ?>">
        <input type="hidden" name="pay_repaybutyn" value="<?=$pay_repaybutyn?>">
        <input type="hidden" name="pay_closeurl"    value="<?=$url_close ?>">
        <input type="hidden" name="pay_requrl"	    value="<?=$url_payreqhome ?>">
        <input type="hidden" name="pay_homeurl"     value="<?=$url_returnhome ?>">
        <input type="hidden" name="pay_returl"      value="<?=$url_result ?>">
        <input type="hidden" name="pay_returl_json" value="<?=$url_result_json?>">
        <input type="hidden" name="pay_returl_curl" value="<?=$url_result_curl?>">
        <input type="hidden" name="pay_vbnturl"     value="<?=$url_vbnotice ?>">
        <input type="hidden" name="pay_retmethod"   value="<?=$url_retmethod ?>">
        <input type="hidden" name="ReqUrl"          value="<?=$ReqUrl ?>">
        <input type="hidden" name="conf_site_name"  value="<?=$conf_site_name ?>">
        <input type="hidden" name="pay_homekey"  value="<?=$pay_homekey?>"  />
        <input type="hidden" name="pay_replaceurl"  value="<?=$pay_replaceurl?>"  />
        <!----------------------- 쇼핑몰운영시 분할승인을 사용여부 파라메터(필수) ------------------------------>
        <input type="hidden" name="conf_divpay_use"   value="N">
        <input type="hidden" name="DivPayReq_UqCode"  value="">
        <!--------------- 쇼핑몰구매상품 분할승인 구매내역 파라메터(필수아님-테스트용임) ---------------------------->
        <input type="hidden" name="shop_buy_goods" value="">
        <!-- goods_key[] => [셀프페이고유코드/상품코드/상품명/상품가격/구매수량] --->

        <input type="text" name="ordr_idxx" value=""><!-- 주문번호 -->
        <input type="text" name="buyr_name" value="<?=$CenterName?>(<?=$CenterManagerName?>)"><!-- 고객성명 -->
        <input type="text" name="buyr_tel1" value="<?=str_replace("-","",$CenterPhone1)?>"><!-- 전화번호 -->
        <input type="text" name="buyr_tel2" value="<?=str_replace("-","",$CenterPhone2)?>"><!-- 휴대폰 -->
        <input type="text" name="buyr_mail" value="<?=$CenterEmail?>"><!-- 이메일 -->
        <input type="text" name="good_name" value="<?=$SearchYear?>년 <?=$SearchMonth?>월 망고아이 연장 수강신청"><!-- 상품명 -->
        <input type="text" name="good_mny" value=""><!-- 결제금액 -->
    </form>
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
    var ListCount = <?=$ListPayCount?>;
    var CheckedListCount = 0;
    function CheckListAll(obj){

        for (ii=1;ii<=ListCount;ii++){
            if (obj.checked){
                document.getElementById("CheckBox_"+ii).checked = true;
            }else{
                document.getElementById("CheckBox_"+ii).checked = false;
            }
        }

        CalcSumList();
    }


    function CalcSumList(){
        var SumOfSavedMoney = <?=$SumOfSavedMoney?>; //충전금
        var DeterminedMoney = 0;					//최종 결제할 금액
        var Deduction = 0; 							//충전금 중 차감할 금액

        CheckPaySumPrevPrevMonthPaidClassCount = 0;
        CheckPaySumPrevPrevMonthPaidMoney = 0;
        CheckPaySumPrevMonthEndClassCount = 0;
        CheckPaySumPrevMonthUsedMoney = 0;
        CheckPaySumDifferencePrevPrevMonthPaidMoney = 0;
        CheckPaySumNextMonthClassCount = 0;
        CheckPaySumNextMonthPayMoney = 0;
        CheckPaySumClassOrderPayLogCalCenterFreeTrialCount = 0;
        CheckPaySumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice = 0;
        CheckPaySumDifferenceNextMonthPayMoney = 0;

        NoCheckPaySumDifferencePrevPrevMonthPaidMoney = 0;
        ClassOrderPayB2bDifferencePrice = 0;

        CheckedListCount = 0;


        for (ii=1;ii<=ListCount;ii++){
            if (document.getElementById("CheckBox_"+ii).checked){

                CheckedListCount ++;

                ClassOrderPayLogPrevPrevMonthPaidClassCount = document.getElementById("ClassOrderPayLogPrevPrevMonthPaidClassCount_"+ii).value;
                ClassOrderPayLogPrevPrevMonthPaidMoney = document.getElementById("ClassOrderPayLogPrevPrevMonthPaidMoney_"+ii).value;
                ClassOrderPayLogPrevMonthEndClassCount = document.getElementById("ClassOrderPayLogPrevMonthEndClassCount_"+ii).value;
                ClassOrderPayLogPrevMonthUsedMoney = document.getElementById("ClassOrderPayLogPrevMonthUsedMoney_"+ii).value;
                ClassOrderPayLogDifferencePrevPrevMonthPaidMoney = document.getElementById("ClassOrderPayLogDifferencePrevPrevMonthPaidMoney_"+ii).value;
                ClassOrderPayLogNextMonthClassCountInfo = document.getElementById("ClassOrderPayLogNextMonthClassCountInfo_"+ii).value;
                ClassOrderPayLogNextMonthPayMoney = document.getElementById("ClassOrderPayLogNextMonthPayMoney_"+ii).value;
                ClassOrderPayLogCalCenterFreeTrialCount = document.getElementById("ClassOrderPayLogCalCenterFreeTrialCount_"+ii).value;
                ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice = document.getElementById("ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice_"+ii).value;
                ClassOrderPayLogDifferenceNextMonthPayMoney = document.getElementById("ClassOrderPayLogDifferenceNextMonthPayMoney_"+ii).value;

                CheckPaySumPrevPrevMonthPaidClassCount = CheckPaySumPrevPrevMonthPaidClassCount + parseInt(ClassOrderPayLogPrevPrevMonthPaidClassCount);
                CheckPaySumPrevPrevMonthPaidMoney = CheckPaySumPrevPrevMonthPaidMoney + parseInt(ClassOrderPayLogPrevPrevMonthPaidMoney);
                CheckPaySumPrevMonthEndClassCount = CheckPaySumPrevMonthEndClassCount + parseInt(ClassOrderPayLogPrevMonthEndClassCount);
                CheckPaySumPrevMonthUsedMoney = CheckPaySumPrevMonthUsedMoney + parseInt(ClassOrderPayLogPrevMonthUsedMoney);
                CheckPaySumDifferencePrevPrevMonthPaidMoney = CheckPaySumDifferencePrevPrevMonthPaidMoney + parseInt(ClassOrderPayLogDifferencePrevPrevMonthPaidMoney);


                ArrClassOrderPayLogNextMonthClassCountInfo = ClassOrderPayLogNextMonthClassCountInfo.split("||");
                for (jj=1 ; jj<=ArrClassOrderPayLogNextMonthClassCountInfo.length-2 ; jj++ ){
                    CheckPaySumNextMonthClassCount = CheckPaySumNextMonthClassCount + parseInt(ArrClassOrderPayLogNextMonthClassCountInfo[jj]);
                }

                CheckPaySumNextMonthPayMoney = CheckPaySumNextMonthPayMoney + parseInt(ClassOrderPayLogNextMonthPayMoney);
                CheckPaySumClassOrderPayLogCalCenterFreeTrialCount = CheckPaySumClassOrderPayLogCalCenterFreeTrialCount + parseInt(ClassOrderPayLogCalCenterFreeTrialCount);
                CheckPaySumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice = CheckPaySumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice + parseInt(ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice);

                CheckPaySumDifferenceNextMonthPayMoney = CheckPaySumDifferenceNextMonthPayMoney + parseInt(ClassOrderPayLogDifferenceNextMonthPayMoney);
            }else{

                ClassOrderPayLogDifferencePrevPrevMonthPaidMoney = document.getElementById("ClassOrderPayLogDifferencePrevPrevMonthPaidMoney_"+ii).value;
                NoCheckPaySumDifferencePrevPrevMonthPaidMoney = NoCheckPaySumDifferencePrevPrevMonthPaidMoney + parseInt(ClassOrderPayLogDifferencePrevPrevMonthPaidMoney);
                ClassOrderPayB2bDifferencePrice = NoCheckPaySumDifferencePrevPrevMonthPaidMoney;
            }
        }



        document.getElementById("CheckPaySumPrevPrevMonthPaidClassCount").innerHTML = numberWithCommas(CheckPaySumPrevPrevMonthPaidClassCount);
        document.getElementById("CheckPaySumPrevPrevMonthPaidMoney").innerHTML = numberWithCommas(CheckPaySumPrevPrevMonthPaidMoney);
        document.getElementById("CheckPaySumPrevMonthEndClassCount").innerHTML = numberWithCommas(CheckPaySumPrevMonthEndClassCount);
        document.getElementById("CheckPaySumPrevMonthUsedMoney").innerHTML = numberWithCommas(CheckPaySumPrevMonthUsedMoney);
        document.getElementById("CheckPaySumDifferencePrevPrevMonthPaidMoney").innerHTML = numberWithCommas(CheckPaySumDifferencePrevPrevMonthPaidMoney);
        document.getElementById("CheckPaySumNextMonthClassCount").innerHTML = numberWithCommas(CheckPaySumNextMonthClassCount);
        document.getElementById("CheckPaySumNextMonthPayMoney").innerHTML = numberWithCommas(CheckPaySumNextMonthPayMoney);
        document.getElementById("CheckPaySumClassOrderPayLogCalCenterFreeTrialCount").innerHTML = numberWithCommas(CheckPaySumClassOrderPayLogCalCenterFreeTrialCount);
        document.getElementById("CheckPaySumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice").innerHTML = numberWithCommas(CheckPaySumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice);
        document.getElementById("CheckPaySumDifferenceNextMonthPayMoney").innerHTML = numberWithCommas(CheckPaySumDifferenceNextMonthPayMoney);

        document.getElementById("NoCheckPaySumDifferencePrevPrevMonthPaidMoney").innerHTML = numberWithCommas(NoCheckPaySumDifferencePrevPrevMonthPaidMoney);
        document.getElementById("NoCheckPaySumDifferencePrevPrevMonthPaidMoney2").innerHTML = numberWithCommas(NoCheckPaySumDifferencePrevPrevMonthPaidMoney);
        document.getElementById("ClassOrderPayB2bDifferencePrice").value = NoCheckPaySumDifferencePrevPrevMonthPaidMoney;
        document.getElementById("PaySumDifferencePrevPrevMonthPaidMoney").innerHTML = numberWithCommas(CheckPaySumDifferencePrevPrevMonthPaidMoney+NoCheckPaySumDifferencePrevPrevMonthPaidMoney);
        document.getElementById("PaySumDifferenceNextMonthPayMoney").innerHTML = numberWithCommas(CheckPaySumDifferenceNextMonthPayMoney - NoCheckPaySumDifferencePrevPrevMonthPaidMoney);

        //최종 결제할 금액 계산하기 만약 충전금이 결제해야 할 금액보다 적으면 충전금을 뺀 돈이 최종 결제 금액
        if (((CheckPaySumDifferenceNextMonthPayMoney - NoCheckPaySumDifferencePrevPrevMonthPaidMoney)-SumOfSavedMoney) >= 0){
            DeterminedMoney = (CheckPaySumDifferenceNextMonthPayMoney - NoCheckPaySumDifferencePrevPrevMonthPaidMoney)-SumOfSavedMoney;
            Deduction = SumOfSavedMoney;
        } else {
            // 그렇지 않으면 최종 결제 금액은 0 이고 충전금액에서 결제해야 할 금액만큼 차감한다.
            DeterminedMoney = 0;
            Deduction = CheckPaySumDifferenceNextMonthPayMoney - NoCheckPaySumDifferencePrevPrevMonthPaidMoney;
        }
        document.getElementById("DeterminedMoney").value = numberWithCommas(DeterminedMoney);
        document.getElementById("Deduction").value = numberWithCommas(Deduction);


        document.SendPayForm.good_mny.value = DeterminedMoney;
        // document.SendPayForm.good_mny.value = CheckPaySumDifferenceNextMonthPayMoney - NoCheckPaySumDifferencePrevPrevMonthPaidMoney;//결제폼에 넣어줌.

        if (CheckedListCount==ListCount){
            document.getElementById("CheckAll_1").checked = true;
            document.getElementById("CheckAll_2").checked = true;
            document.getElementById("CheckAll_3").checked = true;
        }else{
            document.getElementById("CheckAll_1").checked = false;
            document.getElementById("CheckAll_2").checked = false;
            document.getElementById("CheckAll_3").checked = false;
        }

    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }



    function DelPrePayOrder(){
        if (confirm('<?=$테스트한_DB를_삭제_하시겠습니까[$LangID]?>?')){

            url = "ajax_set_class_order_renew_center_order_delete.php";

            $.ajax(url, {
                data: {

                },
                success: function (data) {
                    location.reload();

                },
                error: function () {

                }
            });

        }
    }

    function PrePayOrder(){

        var UsedSavedMoney = parseInt(document.getElementById("Deduction").value.replace(",", ""));

        if (ListCount==0){
            alert("<?=$선택한_목록이_없습니다[$LangID]?>");
        }else{

            CheckBoxNums = "||";
            for (ii=1;ii<=ListCount;ii++){
                if (document.getElementById("CheckBox_"+ii).checked){

                    CheckBoxNums = CheckBoxNums + ii + "||";

                }
            }
            document.RegForm.CheckBoxNums.value = CheckBoxNums;

            if (CheckBoxNums=="||"){
                alert("선택한 목록이 없습니다.");
            }else{

                Test = 1;

                url = "ajax_set_class_order_renew_center_order.php";

                if (Test==0){

                    document.RegForm.target = "_blank";
                    document.RegForm.action = url;
                    document.RegForm.submit();

                }else{
                    $("#ClassOrderPayUseSavedMoneyPrice").val(UsedSavedMoney);

                    var param = $("form[name=RegForm]").serialize();
                    $.ajax({
                        type: "POST"
                        ,url: url
                        ,data: param
                        ,contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
                        ,success:function(data){
                            if (data && data.ErrNum && parseInt(data.ErrNum, 10) > 0) {
                                alert(data.ErrMsg ? data.ErrMsg : "Payment blocked.");
                                return;
                            }
                            json = data;
                            document.SendPayForm.ordr_idxx.value = json.ClassOrderPayNumber;

                            var good_mny = document.SendPayForm.good_mny.value;  //결제할 금액


                            //만약 결제할 금액이 0이라면 결제창을 열지 않고 바로 결제완료 후 처리를 실행한다.
                            if (good_mny > 0){
                                //결제창 열기
                                PayAction();
                            } else {
                                url = "savedmoney_class_order_result.php";
                                alert(UsedSavedMoney);
                                $.ajax({
                                    type: "POST"
                                    ,url: url
                                    ,data: {
                                        CenterID: "<?=$SearchCenterID?>",
                                        ClassOrderPayYear:"<?=$SearchYear?>",
                                        ClassOrderPayMonth:"<?=$SearchMonth?>",
                                        ClassOrderPayNumber: data.ClassOrderPayNumber,
                                        ClassOrderPayID: data.ClassOrderPayID,
                                        UsedSavedMoney: UsedSavedMoney
                                    }
                                    ,contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
                                    ,success:function(data){

                                        location.reload();
                                    }
                                    ,error:function(data){
                                        alert("오류가 발생했습니다. 다시 시도해 주시기 바랍니다.");
                                    }
                                });
                            }


                        }
                        ,error:function(data){
                            alert("결제 요청중 오류가 발생했습니다. 다시 시도해 주시기 바랍니다.");
                        }
                    });
                }

            }
        }

    }

    $(document).ready(function(){

        $('#savedMoneyButton').click(function (e) {
            e.preventDefault();
            e.target.blur();
            UIkit.modal.prompt('수업료 충전할 액수:', '300,000원 이상부터 입력가능', function(savedMoney) {

                if (!isNaN(savedMoney)){
                    if (parseInt(savedMoney) < 300000){
                        UIkit.modal.alert('300,000원 이상을 입력해 주세요.');
                    } else {
                        SavedMoneyOrder(savedMoney);
                    }
                } else {
                    UIkit.modal.alert('숫자로만 입력해 주세요.');
                }

            });
        });
    });



    //충전금 충전을 위해 결제시스템을 작동시킨다.
    function SavedMoneyOrder(savedMoney){

        Test = 1;

        url = "ajax_set_class_order_saved_money.php?SavedMoney="+savedMoney;

        if (Test==0){

            document.RegForm.target = "_blank";
            document.RegForm.action = url;
            document.RegForm.submit();

        }else{

            var param = $("form[name=RegForm]").serialize();
            $.ajax({
                type: "POST"
                ,url: url
                ,data: param
                ,contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
                ,success:function(data){
                    json = data;
                    document.SendPayForm.good_mny.value = savedMoney;
                    document.SendPayForm.ordr_idxx.value = json.SavedMoneyPayNumber;
                    document.SendPayForm.good_name.value = '수업료충전';
                    document.SendPayForm.pay_returl.value = "https://www.mangoi.co.kr/lms/saved_money_pay_result.php";  //테스트용 꼭 변경해야 함
                    //console.log(data.Sql);
                    //결제창 열기
                    PayAction();

                }
                ,error:function(data){
                    alert("결제 요청중 오류가 발생했습니다. 다시 시도해 주시기 바랍니다.");

                }
            });
        }

    }
</script>



<script type="text/javascript">
    //---------------------------------------------------------------------------------------------//
    // 브라우저에서 뒤로가기 기능막기
    //---------------------------------------------------------------------------------------------//
    history.pushState(null, null, location.href);
    window.onpopstate = function(event) {

        history.go(1);

        alert("<?=$뒤로가기_버튼은_사용할_수_없습니다[$LangID]?>!");
    };
    //---------------------------------------------------------------------------------------------//
    // PC | MOBILE 구분
    //---------------------------------------------------------------------------------------------//
    function device_check() {
        // 디바이스 종류 설정
        var pc_device = "win16|win32|win64|mac|macintel";

        // 접속한 디바이스 환경
        var this_device = navigator.platform;

        if ( this_device ) {

            if ( pc_device.indexOf(navigator.platform.toLowerCase()) < 0 ) {
                return 'MOBILE';
            } else {
                return 'PC';
            }

        }
    }


    //--------------------------------------------------------------------------------------------//
    // 화폐단위
    //--------------------------------------------------------------------------------------------//
    function numberWithCommas(x) {

        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

    }
    //--------------------------------------------------------------------------------------------//
    // 영수증보기
    //--------------------------------------------------------------------------------------------//
    function PayReceipt() {

        var device_name = device_check();

        if (device_name == 'MOBILE') {
            document.SendPayForm.action = "https://www.selfpay.kr/" + document.SendPayForm.ReqUrl.value;
            document.SendPayForm.submit();
        }

    }


</script>

<script type="text/javascript">
    //-------------------------------------------------------------------------------------------------------------------------//
    // 단문비교함수
    //-------------------------------------------------------------------------------------------------------------------------//
    function jviif( sw, a, b ) {

        if (sw) {
            return a;
        } else {
            return b;
        }

    }

    //-----------------------------------------------------------------------------------//
    // 결제요청실행
    //-----------------------------------------------------------------------------------//
    function PayAction() {
//-----------------------------------------------------------------------------------//

        var device_name = device_check();

        var divpay_use = document.SendPayForm.conf_divpay_use.value;

        rabbit=confirm("<?=$결제_하시겠습니까[$LangID]?>?");
        if(!rabbit) {
            return;
        }


        //------------------------------------------------------------------------------//
        // PC 결제시
        //------------------------------------------------------------------------------//
        if (device_name == 'PC') {
            //------------------------------------------------------------------------------//

            var pay_layer = document.getElementById('paylayer');
            pay_layer.style.display = 'block';

            var pay_url = "https://www.selfpay.kr/KCPPAY/pcpay/from_order.php";

            document.SendPayForm.target = 'kcppay';
            document.SendPayForm.action = pay_url;
            document.SendPayForm.submit();

            //-------------------------------------------------------------------------------//
            // MOBIL 결제시
            //-------------------------------------------------------------------------------//
        } else {
            //-------------------------------------------------------------------------------//

            <?if ($FromDevice=="app"){?>

            Frch_BsUqCode = document.SendPayForm.Frch_BsUqCode.value;
            Frch_BrUqCode = document.SendPayForm.Frch_BrUqCode.value;
            TestPay = document.SendPayForm.TestPay.value;
            pay_closeurl = document.SendPayForm.pay_closeurl.value;
            pay_requrl = document.SendPayForm.pay_requrl.value;
            pay_homeurl = document.SendPayForm.pay_homeurl.value;
            pay_returl = document.SendPayForm.pay_returl.value;
            pay_returl_json = document.SendPayForm.pay_returl_json.value;
            pay_returl_curl = document.SendPayForm.pay_returl_curl.value;
            pay_vbnturl = document.SendPayForm.pay_vbnturl.value;
            pay_retmethod = document.SendPayForm.pay_retmethod.value;
            ReqUrl = document.SendPayForm.ReqUrl.value;
            conf_site_name = document.SendPayForm.conf_site_name.value;
            conf_divpay_use = document.SendPayForm.conf_divpay_use.value;
            DivPayReq_UqCode = document.SendPayForm.DivPayReq_UqCode.value;
            shop_buy_goods = document.SendPayForm.shop_buy_goods.value;
            ordr_idxx = document.SendPayForm.ordr_idxx.value;
            buyr_name = document.SendPayForm.buyr_name.value;
            buyr_tel1 = document.SendPayForm.buyr_tel1.value;
            buyr_tel2 = document.SendPayForm.buyr_tel2.value;
            buyr_mail = document.SendPayForm.buyr_mail.value;
            good_name = document.SendPayForm.good_name.value;
            good_mny = document.SendPayForm.good_mny.value;
            pay_homekey = document.SendPayForm.pay_homekey.value;
            pay_replaceurl = document.SendPayForm.pay_replaceurl.value;

            var pay_url = "https://www.selfpay.kr/mselfpay_sms_order.php";

            pay_url = pay_url + "?1=1&Frch_BsUqCode="+Frch_BsUqCode+"&Frch_BrUqCode="+Frch_BrUqCode+"&TestPay="+TestPay+"&pay_closeurl="+pay_closeurl+"&pay_requrl="+pay_requrl+"&pay_homeurl="+pay_homeurl+"&pay_returl="+pay_returl+"&pay_returl_json="+pay_returl_json+"&pay_returl_curl="+pay_returl_curl+"&pay_vbnturl="+pay_vbnturl+"&pay_retmethod="+pay_retmethod+"&ReqUrl="+ReqUrl+"&conf_site_name="+conf_site_name+"&conf_divpay_use="+conf_divpay_use+"&DivPayReq_UqCode="+DivPayReq_UqCode+"&shop_buy_goods="+shop_buy_goods+"&ordr_idxx="+ordr_idxx+"&buyr_name="+buyr_name+"&buyr_tel1="+buyr_tel1+"&buyr_tel2="+buyr_tel2+"&buyr_mail="+buyr_mail+"&good_name="+good_name+"&good_mny="+good_mny+"&pay_homekey="+pay_homekey+"&pay_replaceurl="+pay_replaceurl;

            cordova_iab.InAppOpenBrowser(pay_url);
            setTimeout(InAppBrowserClose, 3000);


            <?}else{?>

            var pay_url = "https://www.selfpay.kr/mselfpay_sms_order.php";
            document.SendPayForm.action = pay_url;
            document.SendPayForm.submit();

            <?}?>


            //-------------------------------------------------------------------------------//
        }
        //-------------------------------------------------------------------------------//

//-----------------------------------------------------------------------------------//
    }
    //-----------------------------------------------------------------------------------//
    // PC 결제창 닫기
    //-----------------------------------------------------------------------------------//
    function PayWindow_Close() {

        var pay_layer = document.getElementById('paylayer');
        var kcppay    = document.getElementById('kcppay');

        pay_layer.style.display = 'none';
        kcppay.src = '';

    }

    //-----------------------------------------------------------------------------------//
</script>


<script>
    function SearchSubmit(){
        // document.SearchForm.action = "class_order_renew_center_form.php";
        document.SearchForm.action = "class_order_renew_center_form_simple.php";
        document.SearchForm.submit();
    }

    CalcSumList();
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>
