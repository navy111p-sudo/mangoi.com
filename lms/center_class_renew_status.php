<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

if ($_LINK_ADMIN_LEVEL_ID_>10){
    header("Location: center_form.php?CenterID=".$_LINK_ADMIN_CENTER_ID_);
    exit;
}
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
    <?
    include_once('./includes/common_meta_tag.php');
    include_once('./inc_header.php');
    include_once('./inc_common_list_css.php');
    ?>
    <!-- ============== only this page css ============== -->
    <link rel="stylesheet" type="text/css" href="./css/common.css" />
    <!-- ============== only this page css ============== -->
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 11;
$SubMenuID = 1133;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

// ================= 검색 파라미터 세팅 =================
$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";

$CurrentPage         = isset($_REQUEST["CurrentPage"])         ? $_REQUEST["CurrentPage"] : "";
$PageListNum         = isset($_REQUEST["PageListNum"])         ? $_REQUEST["PageListNum"] : "";
$SearchText          = isset($_REQUEST["SearchText"])          ? $_REQUEST["SearchText"] : "";
$SearchState         = isset($_REQUEST["SearchState"])         ? $_REQUEST["SearchState"] : "";
$SearchFranchiseID   = isset($_REQUEST["SearchFranchiseID"])   ? $_REQUEST["SearchFranchiseID"] : "";
$SearchCompanyID     = isset($_REQUEST["SearchCompanyID"])     ? $_REQUEST["SearchCompanyID"] : "";
$SearchBranchGroupID = isset($_REQUEST["SearchBranchGroupID"]) ? $_REQUEST["SearchBranchGroupID"] : "";
$SearchBranchID      = isset($_REQUEST["SearchBranchID"])      ? $_REQUEST["SearchBranchID"] : "";
$SearchOnlineSiteID  = isset($_REQUEST["SearchOnlineSiteID"])  ? $_REQUEST["SearchOnlineSiteID"] : "";
$SearchManagerID     = isset($_REQUEST["SearchManagerID"])     ? $_REQUEST["SearchManagerID"] : "";

$SearchYear  = isset($_REQUEST["SearchYear"])  ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

// ================== 서치폼 감추기 로직 (원본 유지) =================
$HideSearchCenterID = 0;
$HideSearchBranchID = 0;
$HideSearchBranchGroupID = 0;
$HideSearchCompanyID = 0;
$HideSearchFranchiseID = 0;
$HideSearchOnlineSiteID = 0;
$HideSearchManagerID = 0;

if ($_LINK_ADMIN_LEVEL_ID_==0 or $_LINK_ADMIN_LEVEL_ID_==1){//마스터
    //모두허용
}else if ($_LINK_ADMIN_LEVEL_ID_==3 or $_LINK_ADMIN_LEVEL_ID_==4){//프랜차이즈 관리자
    $SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
    $HideSearchFranchiseID = 1;
    $HideSearchOnlineSiteID = 1;
    $HideSearchManagerID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==6 or $_LINK_ADMIN_LEVEL_ID_==7){//대표지사 관리자
    $SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
    $SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
    $SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;

    $HideSearchBranchGroupID = 1;
    $HideSearchCompanyID = 1;
    $HideSearchFranchiseID = 1;
    $HideSearchOnlineSiteID = 1;
    $HideSearchManagerID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==9 or $_LINK_ADMIN_LEVEL_ID_==10){//지사 관리자
    $SearchBranchID = $_LINK_ADMIN_BRANCH_ID_;
    $SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
    $SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
    $SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;

    $HideSearchBranchID = 1;
    $HideSearchBranchGroupID = 1;
    $HideSearchCompanyID = 1;
    $HideSearchFranchiseID = 1;
    $HideSearchOnlineSiteID = 1;
    $HideSearchManagerID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==12 or $_LINK_ADMIN_LEVEL_ID_==13){//대리점 관리자
    //폼으로 넘김
}else if ($_LINK_ADMIN_LEVEL_ID_==15){//강사
    //접속불가
}
// ================== 서치폼 감추기 끝 =================

if (!$CurrentPage){
    $CurrentPage = 1;
}
if (!$PageListNum){
    $PageListNum = 30;
}

if ($SearchYear==""){
    $SearchYear = date("Y");
}
if ($SearchMonth==""){
    $SearchMonth = date("m");
}

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

$SearchMonthFirstDay = $SearchYear."-".substr("0".$SearchMonth,-2)."-01";
$PrevPrevSearchMonthFirstDay = $PrevPrevSearchYear."-".substr("0".$PrevPrevSearchMonth,-2)."-01";
$SearchMonthLastDay = $SearchYear."-".substr("0".$SearchMonth,-2)."-".date("t", strtotime($SearchMonthFirstDay));

if ($PageListNum!=""){
    $ListParam .= "&PageListNum=".$PageListNum;
}

if ($SearchState==""){
    $SearchState = "1";
}
if ($SearchState!="100"){
    $ListParam .= "&SearchState=".$SearchState;
    $AddSqlWhere .= " AND A.CenterState = $SearchState ";
}

// 공통 where 조건
$AddSqlWhere .= " AND A.CenterState <> 0 
                  AND B.BranchState <> 0 
                  AND C.BranchGroupState <> 0 
                  AND D.CompanyState <> 0 
                  AND H.FranchiseState <> 0 
                  AND (E.OnlineSiteState <> 0 OR E.OnlineSiteState IS NULL)
                  AND (F.ManagerState <> 0 OR F.ManagerState IS NULL)";

if ($SearchText!=""){
    $ListParam .= "&SearchText=".$SearchText;
    $AddSqlWhere .= " AND (A.CenterName LIKE '%$SearchText%' 
                      OR A.CenterManagerName LIKE '%$SearchText%' 
                      OR G.MemberLoginID LIKE '%$SearchText%') ";
}
if ($SearchFranchiseID!=""){
    $ListParam .= "&SearchFranchiseID=".$SearchFranchiseID;
    $AddSqlWhere .= " AND D.FranchiseID = $SearchFranchiseID ";
}
if ($SearchCompanyID!=""){
    $ListParam .= "&SearchCompanyID=".$SearchCompanyID;
    $AddSqlWhere .= " AND C.CompanyID = $SearchCompanyID ";
}
if ($SearchBranchGroupID!=""){
    $ListParam .= "&SearchBranchGroupID=".$SearchBranchGroupID;
    $AddSqlWhere .= " AND B.BranchGroupID = $SearchBranchGroupID ";
}
if ($SearchBranchID!=""){
    $ListParam .= "&SearchBranchID=".$SearchBranchID;
    $AddSqlWhere .= " AND A.BranchID = $SearchBranchID ";
}
if ($SearchOnlineSiteID!=""){
    $ListParam .= "&SearchOnlineSiteID=".$SearchOnlineSiteID;
    $AddSqlWhere .= " AND A.OnlineSiteID = $SearchOnlineSiteID ";
}
if ($SearchManagerID!=""){
    $ListParam .= "&SearchManagerID=".$SearchManagerID;
    $AddSqlWhere .= " AND A.ManagerID = $SearchManagerID ";
}

$AddSqlWhere .= " AND A.CenterPayType = 1 "; // B2B 결제만

// 2개월 전부터 현재월 말일까지 수업 있는지 확인
$AddSqlWhere .= "
    AND (
        A.CenterID IN (
            SELECT B.CenterID
            FROM Classes A
            INNER JOIN Members B ON A.MemberID = B.MemberID
            WHERE DATEDIFF(A.StartDateTime, '$PrevPrevSearchMonthFirstDay') >= 0
              AND DATEDIFF(A.StartDateTime, '$SearchMonthLastDay') <= 0
        )
        OR
        A.CenterID IN (
            SELECT B.CenterID
            FROM ClassOrders A
            INNER JOIN Members B ON A.MemberID = B.MemberID
            WHERE A.ClassProductID = 1
              AND (DATEDIFF(A.ClassOrderEndDate, '$SearchMonthLastDay') >= 0 OR A.ClassOrderEndDate IS NULL)
              AND (A.ClassOrderState = 1 OR A.ClassOrderState = 2 OR A.ClassOrderState = 4)
        )
    )
";

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
    $ListParam .= "&CurrentPage=".$CurrentPage;
}
$ListParam = str_replace("&", "^^", $ListParam);

// === 전체 개수 조회 ===
$Sql = "SELECT COUNT(*) AS TotalRowCount
        FROM Centers A
            INNER JOIN Branches B ON A.BranchID = B.BranchID
            INNER JOIN BranchGroups C ON B.BranchGroupID = C.BranchGroupID
            INNER JOIN Companies D ON C.CompanyID = D.CompanyID
            LEFT OUTER JOIN OnlineSites E ON A.OnlineSiteID = E.OnlineSiteID
            LEFT OUTER JOIN Managers F ON A.ManagerID = F.ManagerID
            INNER JOIN Franchises H ON D.FranchiseID = H.FranchiseID
            INNER JOIN Members G ON A.CenterID = G.CenterID AND G.MemberLevelID = 12
        WHERE $AddSqlWhere";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];
$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );

// === 실제 목록 조회 ===
$Sql = "
    SELECT
        A.*,
        AES_DECRYPT(UNHEX(A.CenterPhone1), :EncryptionKey) AS DecCenterPhone1,
        AES_DECRYPT(UNHEX(A.CenterPhone2), :EncryptionKey) AS DecCenterPhone2,
        
        /* 원래 지사명(대표지사명) 대신 아래 주석 처리:
           C.BranchGroupName AS RepresentativeBranchName,
        */
        
        -- 실제 지사 이름 (Branches)
        B.BranchName AS ActualBranchName,
        -- 실제 지사 전화번호 (Branches.BranchPhone1 복호화)
        AES_DECRYPT(UNHEX(B.BranchPhone1), :EncryptionKey) AS DecBranchPhone1,
        
        D.CompanyName,
        D.CompanyPricePerTime,
        IFNULL(E.OnlineSiteName, '미지정') AS OnlineSiteName,
        IFNULL(F.ManagerName, '미지정')   AS ManagerName,
        G.MemberLoginID,
        G.MemberID,
        H.FranchiseName,
        
        IFNULL((
            SELECT COUNT(*)
            FROM ClassOrderPayB2bs AAA
            INNER JOIN ClassOrderPays BBB ON AAA.ClassOrderPayID = BBB.ClassOrderPayID
            WHERE AAA.ClassOrderPayYear  = $SearchYear
              AND AAA.ClassOrderPayMonth = $SearchMonth
              AND AAA.CenterID           = A.CenterID
              AND AAA.ClassOrderPayB2bState = 1
              AND (BBB.ClassOrderPayProgress=21 OR BBB.ClassOrderPayProgress=31 OR BBB.ClassOrderPayProgress=41)
        ),0) AS ClassOrderOrderCount,
        
        (
            SELECT SUM(ClassOrderPayUseCashPrice)
            FROM ClassOrderPays
            WHERE ClassOrderPayID IN (
                SELECT DISTINCT AAA.ClassOrderPayID
                FROM ClassOrderPayB2bs AAA
                INNER JOIN ClassOrderPays BBB ON AAA.ClassOrderPayID = BBB.ClassOrderPayID
                WHERE AAA.ClassOrderPayYear  = $SearchYear
                  AND AAA.ClassOrderPayMonth = $SearchMonth
                  AND AAA.CenterID           = A.CenterID
                  AND AAA.ClassOrderPayB2bState = 1
                  AND (BBB.ClassOrderPayProgress=21 OR BBB.ClassOrderPayProgress=31 OR BBB.ClassOrderPayProgress=41)
            )
        ) AS ClassOrderOrderPrice,
        
        (
            SELECT COUNT(*)
            FROM ClassOrderPays
            WHERE ClassOrderPayID IN (
                SELECT DISTINCT AAA.ClassOrderPayID
                FROM ClassOrderPayB2bs AAA
                INNER JOIN ClassOrderPays BBB ON AAA.ClassOrderPayID = BBB.ClassOrderPayID
                WHERE AAA.ClassOrderPayYear  = $SearchYear
                  AND AAA.ClassOrderPayMonth = $SearchMonth
                  AND AAA.CenterID           = A.CenterID
                  AND AAA.ClassOrderPayB2bState = 1
                  AND (BBB.ClassOrderPayProgress=21 OR BBB.ClassOrderPayProgress=31 OR BBB.ClassOrderPayProgress=41)
            )
        ) AS ClassOrderPayCount
        
    FROM Centers A
        INNER JOIN Branches B ON A.BranchID = B.BranchID
        INNER JOIN BranchGroups C ON B.BranchGroupID = C.BranchGroupID
        INNER JOIN Companies D ON C.CompanyID = D.CompanyID
        LEFT OUTER JOIN OnlineSites E ON A.OnlineSiteID = E.OnlineSiteID
        LEFT OUTER JOIN Managers F ON A.ManagerID = F.ManagerID
        INNER JOIN Franchises H ON D.FranchiseID = H.FranchiseID
        INNER JOIN Members G ON A.CenterID = G.CenterID AND G.MemberLevelID = 12
    WHERE $AddSqlWhere
    ORDER BY A.CenterOrder DESC
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>

<div id="page_content">
    <div id="page_content_inner">
        <!-- 제목 -->
        <h3 class="heading_b uk-margin-bottom"><?=$대리점_수강연장_현황[$LangID]?></h3>

        <!-- 상단 검색 폼 (원본 그대로 유지) -->
        <form name="SearchForm" method="get">
            <div class="md-card" style="margin-bottom:10px;">
                <div class="md-card-content">
                    <div class="uk-grid" data-uk-grid-margin="">

                        <!-- ================== 프랜차이즈 선택 ================== -->
                        <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchFranchiseID==1){?>none<?}?>;">
                            <select id="SearchFranchiseID" name="SearchFranchiseID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$프랜차이즈선택[$LangID]?>" style="width:100%;">
                                <option value=""></option>
                                <?
                                $Sql2 = "SELECT A.* 
                                     FROM Franchises A 
                                     WHERE A.FranchiseState<>0 
                                     ORDER BY A.FranchiseState ASC, A.FranchiseName ASC";
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
                                            echo "<optgroup label=\"".$프랜차이즈_운영중[$LangID]."\">";
                                        }else if ($SelectFranchiseState==2){
                                            echo "<optgroup label=\"".$프랜차이즈_미운영[$LangID]."\">";
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

                        <!-- ================== 사이트 선택 ================== -->
                        <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchOnlineSiteID==1){?>none<?}?>;">
                            <select id="SearchOnlineSiteID" name="SearchOnlineSiteID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$사이트선택[$LangID]?>" style="width:100%;">
                                <option value=""></option>
                                <?
                                $AddWhere2 = "";
                                if ($SearchFranchiseID!=""){
                                    $AddWhere2 = "AND A.FranchiseID=".$SearchFranchiseID." ";
                                }else{
                                    $AddWhere2 = " ";
                                }
                                $Sql2 = "SELECT A.* 
                                     FROM OnlineSites A 
                                     INNER JOIN Franchises B ON A.FranchiseID=B.FranchiseID 
                                     WHERE A.OnlineSiteState<>0 AND B.FranchiseState<>0 $AddWhere2
                                     ORDER BY A.OnlineSiteState ASC, A.OnlineSiteName ASC";
                                $Stmt2 = $DbConn->prepare($Sql2);
                                $Stmt2->execute();
                                $Stmt2->setFetchMode(PDO::FETCH_ASSOC);

                                $OldSelectOnlineSiteState = -1;
                                while($Row2 = $Stmt2->fetch()) {
                                    $SelectOnlineSiteID = $Row2["OnlineSiteID"];
                                    $SelectOnlineSiteName = $Row2["OnlineSiteName"];
                                    $SelectOnlineSiteState = $Row2["OnlineSiteState"];

                                    if ($OldSelectOnlineSiteState!=$SelectOnlineSiteState){
                                        if ($OldSelectOnlineSiteState!=-1){
                                            echo "</optgroup>";
                                        }

                                        if ($SelectOnlineSiteState==1){
                                            echo "<optgroup label=\"".$사이트_운영중[$LangID]."\">";
                                        }else if ($SelectOnlineSiteState==2){
                                            echo "<optgroup label=\"".$사이트_미운영[$LangID]."\">";
                                        }
                                    }
                                    $OldSelectOnlineSiteState = $SelectOnlineSiteState;
                                    ?>
                                    <option value="<?=$SelectOnlineSiteID?>" <?if ($SearchOnlineSiteID==$SelectOnlineSiteID){?>selected<?}?>><?=$SelectOnlineSiteName?></option>
                                    <?
                                }
                                $Stmt2 = null;
                                ?>
                            </select>
                        </div>

                        <!-- ================== 영업본부 선택 ================== -->
                        <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchManagerID==1){?>none<?}?>;">
                            <select id="SearchManagerID" name="SearchManagerID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$영업본부선택[$LangID]?>" style="width:100%;">
                                <option value=""></option>
                                <?
                                $AddWhere2 = "";
                                if ($SearchFranchiseID!=""){
                                    $AddWhere2 = "AND A.FranchiseID=".$SearchFranchiseID." ";
                                }else{
                                    $AddWhere2 = " ";
                                }
                                $Sql2 = "SELECT A.* 
                                     FROM Managers A 
                                     INNER JOIN Franchises B ON A.FranchiseID=B.FranchiseID 
                                     WHERE A.ManagerState<>0 AND B.FranchiseState<>0 $AddWhere2
                                     ORDER BY A.ManagerState ASC, A.ManagerName ASC";
                                $Stmt2 = $DbConn->prepare($Sql2);
                                $Stmt2->execute();
                                $Stmt2->setFetchMode(PDO::FETCH_ASSOC);

                                $OldSelectManagerState = -1;
                                while($Row2 = $Stmt2->fetch()) {
                                    $SelectManagerID = $Row2["ManagerID"];
                                    $SelectManagerName = $Row2["ManagerName"];
                                    $SelectManagerState = $Row2["ManagerState"];

                                    if ($OldSelectManagerState!=$SelectManagerState){
                                        if ($OldSelectManagerState!=-1){
                                            echo "</optgroup>";
                                        }

                                        if ($SelectManagerState==1){
                                            echo "<optgroup label=\"".$영업본부_운영중[$LangID]."\">";
                                        }else if ($SelectManagerState==2){
                                            echo "<optgroup label=\"".$영업본부_미운영[$LangID]."\">";
                                        }
                                    }
                                    $OldSelectManagerState = $SelectManagerState;
                                    ?>
                                    <option value="<?=$SelectManagerID?>" <?if ($SearchManagerID==$SelectManagerID){?>selected<?}?>><?=$SelectManagerName?></option>
                                    <?
                                }
                                $Stmt2 = null;
                                ?>
                            </select>
                        </div>

                        <!-- ================== 본사 선택 ================== -->
                        <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchCompanyID==1){?>none<?}?>;">
                            <select id="SearchCompanyID" name="SearchCompanyID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$본사선택[$LangID]?>" style="width:100%;">
                                <option value=""></option>
                                <?
                                $AddWhere2 = "";
                                if ($SearchFranchiseID!=""){
                                    $AddWhere2 = "AND A.FranchiseID=".$SearchFranchiseID." ";
                                }
                                $Sql2 = "SELECT A.* 
                                     FROM Companies A 
                                     INNER JOIN Franchises B ON A.FranchiseID=B.FranchiseID 
                                     WHERE A.CompanyState<>0 AND B.FranchiseState<>0 $AddWhere2
                                     ORDER BY A.CompanyState ASC, A.CompanyName ASC";
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
                                            echo "<optgroup label=\"".$본사_운영중[$LangID]."\">";
                                        }else if ($SelectCompanyState==2){
                                            echo "<optgroup label=\"".$본사_미운영[$LangID]."\">";
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

                        <!-- ================== 대표지사 선택 ================== -->
                        <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
                            <select id="SearchBranchGroupID" name="SearchBranchGroupID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$대표지사선택[$LangID]?>" style="width:100%;">
                                <option value=""></option>
                                <?
                                $AddWhere2 = "";
                                if ($SearchCompanyID!=""){
                                    $AddWhere2 = "AND A.CompanyID=".$SearchCompanyID." ";
                                }else{
                                    if ($SearchFranchiseID!=""){
                                        $AddWhere2 = "AND B.FranchiseID=".$SearchFranchiseID." ";
                                    }
                                }
                                $Sql2 = "SELECT A.* 
                                     FROM BranchGroups A 
                                     INNER JOIN Companies B ON A.CompanyID=B.CompanyID
                                     INNER JOIN Franchises C ON B.FranchiseID=C.FranchiseID
                                     WHERE A.BranchGroupState<>0 AND B.CompanyState<>0 AND C.FranchiseState<>0 $AddWhere2
                                     ORDER BY A.BranchGroupState ASC, A.BranchGroupName ASC";

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
                                            echo "<optgroup label=\"$대표지사[$LangID]($운영중[$LangID])\">";
                                        }else if ($SelectBranchGroupState==2){
                                            echo "<optgroup label=\"$대표지사[$LangID]($미운영[$LangID])\">";
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

                        <!-- ================== 지사 선택 ================== -->
                        <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchID==1){?>none<?}?>;">
                            <select id="SearchBranchID" name="SearchBranchID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$지사선택[$LangID]?>" style="width:100%;">
                                <option value=""></option>
                                <?
                                $AddWhere2 = "";
                                if ($SearchBranchGroupID!=""){
                                    $AddWhere2 = "AND A.BranchGroupID=".$SearchBranchGroupID." ";
                                }else{
                                    if ($SearchCompanyID!=""){
                                        $AddWhere2 = "AND B.CompanyID=".$SearchCompanyID." ";
                                    }else{
                                        if ($SearchFranchiseID!=""){
                                            $AddWhere2 = "AND C.FranchiseID=".$SearchFranchiseID." ";
                                        }
                                    }
                                }
                                $Sql2 = "SELECT A.*
                                     FROM Branches A
                                     INNER JOIN BranchGroups B ON A.BranchGroupID=B.BranchGroupID
                                     INNER JOIN Companies C ON B.CompanyID=C.CompanyID
                                     INNER JOIN Franchises D ON C.FranchiseID=D.FranchiseID
                                     WHERE A.BranchState<>0 AND B.BranchGroupState<>0 AND C.CompanyState<>0 AND D.FranchiseState<>0 $AddWhere2
                                     ORDER BY A.BranchState ASC, A.BranchName ASC";
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
                                            echo "<optgroup label=\"".$대표지사_운영중[$LangID]."\">";
                                        }else if ($SelectBranchState==2){
                                            echo "<optgroup label=\"".$대표지사_미운영[$LangID]."\">";
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

                        <!-- ================== 대리점명/관리자명 검색 ================== -->
                        <div class="uk-width-medium-2-10">
                            <label for="SearchText"><?=$대리점명_또는_관리자명[$LangID]?></label>
                            <input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
                        </div>

                        <!-- ================== 연도 선택 ================== -->
                        <div class="uk-width-medium-2-10" style="padding-top:7px;">
                            <select id="SearchYear" name="SearchYear" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%;">
                                <option value="">년도선택<?=$년도선택[$LangID]?></option>
                                <?php
                                for ($iiii=$SearchYear-1; $iiii<=$SearchYear+1; $iiii++) {
                                    ?>
                                    <option value="<?=$iiii?>" <?if ($SearchYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>

                        <!-- ================== 월 선택 ================== -->
                        <div class="uk-width-medium-2-10" style="padding-top:7px;">
                            <select id="SearchMonth" name="SearchMonth" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;">
                                <option value=""><?=$월선택[$LangID]?></option>
                                <?php
                                for ($iiii=1; $iiii<=12; $iiii++) {
                                    ?>
                                    <option value="<?=$iiii?>" <?if ($SearchMonth==$iiii){?>selected<?}?>><?=$iiii?><?=$월월[$LangID]?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>

                        <!-- ================== 상태 ================== -->
                        <div class="uk-width-medium-1-10">
                            <div class="uk-margin-small-top">
                                <select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize data-md-selectize-bottom>
                                    <option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
                                    <option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$운영[$LangID]?></option>
                                    <option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$휴원[$LangID]?></option>
                                    <option value="3" <?if ($SearchState=="3"){?>selected<?}?>><?=$미운영[$LangID]?></option>
                                </select>
                            </div>
                        </div>

                        <!-- 검색 버튼 -->
                        <div class="uk-width-medium-1-10 uk-text-center">
                            <a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- // 검색 폼 끝 -->

        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div class="uk-overflow-container">
                            <table class="uk-table uk-table-align-vertical">
                                <thead>
                                <tr>
                                    <th nowrap><input name="CheckBoxAll" type="checkbox" onclick="CheckListAll(this)"></th>
                                    <th nowrap>No</th>
                                    <!-- 지사명(대표지사명)은 주석 처리 -->
                                    <!-- <th nowrap>지사명</th> -->
                                    <th nowrap><?=$대리점명[$LangID]?></th>
                                    <th nowrap>지사 이름</th>
                                    <th nowrap><?=$결제_수업수[$LangID]?></th>
                                    <th nowrap><?=$결제_횟수[$LangID]?></th>
                                    <th nowrap><?=$총결제금액[$LangID]?></th>
                                    <th nowrap><?=$전화번호_1[$LangID]?></th>
                                    <th nowrap>지사 전화번호</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $ListCount = 1;

                                $SumClassOrderOrderCount = 0;
                                $SumClassOrderOrderPrice = 0;
                                $SumClassOrderPayCount   = 0;

                                while($Row = $Stmt->fetch()) {
                                    $ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

                                    // 대리점명
                                    $centerName = $Row["CenterName"] ?: "";
                                    // 실제 지사 이름 (Branches)
                                    $actualBranchName = $Row["ActualBranchName"] ?: "";
                                    // 대리점 전화번호(1)
                                    $centerPhone1 = $Row["DecCenterPhone1"] ?: "";
                                    // 실제 지사 전화번호
                                    $branchPhone = $Row["DecBranchPhone1"] ?: "";

                                    $ClassOrderOrderCount = $Row["ClassOrderOrderCount"];
                                    $ClassOrderOrderPrice = $Row["ClassOrderOrderPrice"];
                                    $ClassOrderPayCount   = $Row["ClassOrderPayCount"];

                                    $SumClassOrderOrderCount += $ClassOrderOrderCount;
                                    $SumClassOrderOrderPrice += $ClassOrderOrderPrice;
                                    $SumClassOrderPayCount   += $ClassOrderPayCount;
                                    ?>
                                    <tr>
                                        <td class="uk-text-nowrap uk-table-td-center">
                                            <input name="CheckBox_<?=$ListCount?>" id="CheckBox_<?=$ListCount?>" type="checkbox" value="<?=$Row["MemberID"]?>">
                                        </td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>

                                        <!-- 대리점명 -->
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$centerName?></td>

                                        <!-- 지사 이름 (Branches) -->
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$actualBranchName?></td>

                                        <!-- 결제 수업 수 -->
                                        <td class="uk-text-nowrap uk-table-td-center">
                                            <?=($ClassOrderOrderCount > 0) ? number_format($ClassOrderOrderCount,0) : ""?>
                                        </td>

                                        <!-- 결제 횟수 -->
                                        <td class="uk-text-nowrap uk-table-td-center">
                                            <?=($ClassOrderPayCount > 0) ? number_format($ClassOrderPayCount,0) : ""?>
                                        </td>

                                        <!-- 총결제금액 -->
                                        <td class="uk-text-nowrap uk-table-td-center">
                                            <?=($ClassOrderOrderPrice > 0) ? number_format($ClassOrderOrderPrice,0) : ""?>
                                        </td>

                                        <!-- 대리점 전화번호(1) -->
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$centerPhone1?></td>

                                        <!-- 지사 전화번호 -->
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$branchPhone?></td>
                                    </tr>
                                    <?php
                                    $ListCount++;
                                }
                                $Stmt = null;
                                ?>
                                <tr>
                                    <!-- 체크박스, No, 대리점명, 지사 이름 => 4개 컬럼 병합 -->
                                    <th nowrap colspan="4"><?=$합계[$LangID]?></th>
                                    <th nowrap><?=number_format($SumClassOrderOrderCount,0)?></th>
                                    <th nowrap><?=number_format($SumClassOrderPayCount,0)?></th>
                                    <th nowrap><?=number_format($SumClassOrderOrderPrice,0)?></th>
                                    <th nowrap colspan="2"></th>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- 필요 시 메시지 전송 버튼 (주석) -->
                        <!--
                        <div class="uk-form-row" style="text-align:left;margin-top:20px;">
                            <a type="button" href="javascript:SendMessageForm()" class="md-btn md-btn-primary">메시지 전송</a>
                        </div>
                        -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var ListCount = <?=$ListCount-1?>;
    function CheckListAll(obj){
        for (var ii=1; ii<=ListCount; ii++){
            document.getElementById("CheckBox_"+ii).checked = obj.checked;
        }
    }

    function SendMessageForm(){
        if (ListCount==0){
            alert("<?=$선택한_목록이_없습니다[$LangID]?>");
        }else{
            var MemberIDs = "|";
            for (var ii=1; ii<=ListCount; ii++){
                if (document.getElementById("CheckBox_"+ii).checked){
                    MemberIDs += document.getElementById("CheckBox_"+ii).value + "|";
                }
            }
            if (MemberIDs=="|"){
                alert("<?=$선택한_목록이_없습니다[$LangID]?>");
            }else{
                var openurl = "send_message_log_multi_form.php?MemberIDs="+MemberIDs;
                $.colorbox({
                    href:openurl,
                    width:"95%",
                    height:"95%",
                    maxWidth: "850",
                    maxHeight: "750",
                    title:"",
                    iframe:true,
                    scrolling:true
                });
            }
        }
    }

    function SearchSubmit(){
        document.SearchForm.action = "center_class_renew_status.php";
        document.SearchForm.submit();
    }
</script>

<?
include_once('./inc_common_list_js.php');
?>
<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>
