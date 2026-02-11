<?php
$SearchStartYear  = isset($_REQUEST["SearchStartYear" ]) ? $_REQUEST["SearchStartYear" ] : "";
$SearchStartMonth = isset($_REQUEST["SearchStartMonth"]) ? $_REQUEST["SearchStartMonth"] : "";
$StartDate = isset($_REQUEST["StartDate"]) ? $_REQUEST["StartDate"] : "";
$EndDate = isset($_REQUEST["EndDate"]) ? $_REQUEST["EndDate"] : "";
$Search_sw        = isset($_REQUEST["Search_sw"       ]) ? $_REQUEST["Search_sw"       ] : "2";
$OrderBy          = isset($_REQUEST["OrderBy"         ]) ? $_REQUEST["OrderBy"         ] : "AccBookConfigID";
$direction        = isset($_REQUEST["direction"       ]) ? $_REQUEST["direction"       ] : "asc";
$direction2       = isset($_REQUEST["direction2"      ]) ? $_REQUEST["direction2"      ] : "asc";
$PrintState       = isset($_REQUEST["PrintState"      ]) ? $_REQUEST["PrintState"      ] : "0";
$SelectedAccount  = isset($_REQUEST["SelectedAccount" ]) ? $_REQUEST["SelectedAccount" ] : "";
$SelectedCompany  = isset($_REQUEST["SelectedCompany" ]) ? $_REQUEST["SelectedCompany" ] : "";


if ($SelectedAccount == null || $SelectedAccount == Null || $SelectedAccount == "null") $selectedAccount = "";

if ($SearchStartYear==""){
    $SearchStartYear = date("Y");
}
if ($SearchStartMonth==""){
    $SearchStartMonth = date("m");
}



// 카드비용 적용을 위한 시작 날짜와 끝 날짜를 가져온다.
$Sql = "SELECT * FROM CardMoneyDate ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$StartDay = $Row["StartDay"];
$EndDay = $Row["EndDay"];

$TitleString = "";
$SqlCompany = "";
$Accounts = array();
$SearchDate = "";

function makeIncomeStateSql(){

    global $DbConn;
    global $SearchStartYear, $SearchStartMonth, $StartDate, $EndDate, $StartDay, $EndDay,
           $Search_sw, $OrderBy, $direction, $direction2, $PrintState, $SelectedAccount, $SelectedCompany;

    global $TitleString,$SqlCompany;
    global $SearchDate;
    global $Accounts;

    // TitleString을 함수 호출 시마다 초기화
    $TitleString = "";

    // SelectedCompany 기본값 처리
    if (!isset($SelectedCompany) || $SelectedCompany === null) {
        $SelectedCompany = '';
    }

    // 귀속월이 입력되어 있는 필드는 해당귀속월도 체크해 준다. 0은 무조건 불포함.
    $SearchSql = " WHERE (AttributionMonth <> '0' or AttributionMonth IS NULL) ";

    $SearchSql2 = $SearchSql;

    // 카드 비용 (AccBookConfigID 14번) 은 union 으로 추가로 쿼리해 온다. 카드비용은 1일~ 말일까지 기준이 아닌 11일~10일까지 기준으로 적용해야 하므로
    $SearchSql .= " AND A.AccBookConfigID <> 14 ";

    $SearchSql2 .= " AND A.AccBookConfigID = 14 ";




    $companyAccounts = "";
    $SqlCompany = "SELECT * FROM AccountState ";
    $CompanySql = "";
    $SelectName1 = "";
    $SelectName2 = "";
    // 선택된 회사가 있으면 해당 회사에 맞는 계좌 가져오기
    if ($SelectedCompany != ""){
        // 망고아이 선택했을 경우 신한은행과 신한카드
        if ($SelectedCompany== "0"){
            $SelectName1 = "신한은행";
            $SelectName2 = "신한카드";
            $TitleString = "(망고아이)";
        } else if ($SelectedCompany== "1"){  //SLP선택했을 경우 국민은행과 KB카드
            $SelectName1 = "국민은행";
            $SelectName2 = "KB카드";
            $TitleString = "(SLP)";
        }

        $SqlCompany .= " WHERE AccountName = '$SelectName1' OR AccountName = '$SelectName2' ";

        $Stmt7 = $DbConn->prepare($SqlCompany);
        $Stmt7->execute();
        $Stmt7->setFetchMode(PDO::FETCH_ASSOC);



        while($Row7 = $Stmt7->fetch()) {
            $companyAccounts .= ",'" . $Row7['AccountNumber'] . "'";
        }

        if ($companyAccounts != ""){
            $companyAccounts = substr($companyAccounts,1);

            $CompanySql = " AND AccNumber IN (".$companyAccounts.")";
        } else if($companyAccounts == ""  ) {
            $CompanySql .= " AND AccNumber IN ('null')";
        }

        $SearchSql .= $CompanySql;
        $SearchSql2 .= $CompanySql;

    }

    $SelAccSql = "";

    // 선택된 계좌가 있을 때는 해당 계좌만 검색해서 보여줌
    if ($SelectedAccount != ""){

        // 앞의 sql 문과 연결하는 연결 쿼리
        $SelAccSql = " AND (";
        $Accounts = explode(",", $SelectedAccount);

        $AccountsLength = count($Accounts);
        $i = 1;

        foreach($Accounts as $Account){
            $SelAccSql .= "  AccNumber = '".$Account. "' ";
            if ($i < $AccountsLength) {
                $SelAccSql .= " OR ";
                $i++;
            }
        }
        $SelAccSql .= " ) ";

        $SearchSql .= $SelAccSql;

        $SearchSql2 .= $SelAccSql;

    }



    if ($Search_sw == "1") {
        $SearchDate = $SearchStartYear . "-01-01" ;
        $SearchSql .= " AND  (YEAR(AccBookDate) = YEAR('".$SearchDate."')  AND AttributionMonth IS NULL) ";
        $SearchSql .= " OR   (substr(AttributionMonth,1,4) = YEAR('".$SearchDate."') ".$CompanySql." ".$SelAccSql." )";
        $SearchSql2 = $SearchSql;
        $StudentStatusMonth = date("Y-m"); //학생현황 기준월
        $SearchStartMonth ="";

    } else if ($Search_sw == "2"){
        $SearchDate = $SearchStartYear . "-" . (strlen($SearchStartMonth) == 1 ? "0" : "") . $SearchStartMonth ."-01";
        $SearchSql .= " AND  (YEAR(AccBookDate) = YEAR('".$SearchDate."') AND MONTH(AccBookDate) = MONTH('".$SearchDate."') AND AttributionMonth IS NULL) ";
        $SearchSql .= " OR  (substr(AttributionMonth,1,4) = YEAR('".$SearchDate."') AND substr(AttributionMonth,5,2) = MONTH('".$SearchDate."') AND A.AccBookConfigID <> 14  ".$CompanySql." ".$SelAccSql."  ) ";

        // 카드 비용인 경우 11일~10일(db에 저장한 날짜로 바꿈)까지의 비용을 가지고 온다.
        $SearchEndMonth = $SearchStartMonth + 1;
        if ($SearchEndMonth== 13) $SearchEndMonth = 1;
        $SearchDate1 = $SearchStartYear . "-" . (strlen($SearchStartMonth) == 1 ? "0" : "") . $SearchStartMonth ."-".$StartDay ;
        $SearchDate2 = $SearchStartYear . "-" . (strlen($SearchEndMonth) == 1 ? "0" : "") . $SearchEndMonth ."-".$EndDay ;

        $SearchSql2 .= " AND  (DATE(AccBookDate) >= '".$SearchDate1."'  AND DATE(AccBookDate) <= '".$SearchDate2."'  AND AttributionMonth IS NULL ) ";
        $SearchSql2 .= " OR  (substr(AttributionMonth,1,4) = YEAR('".$SearchDate."') AND substr(AttributionMonth,5,2) = MONTH('".$SearchDate."') AND A.AccBookConfigID = 14   ".$CompanySql." ".$SelAccSql." ) ";

        // 혹시 이전에 기간별로 검색했으면 기간별 검색 내용을 초기화한다.
        $StartDate = "";
        $EndDate = "";
        $StudentStatusMonth = $SearchStartYear . "-" .$SearchStartMonth;
    } else if ($Search_sw == "3"){
        $SearchDate = $StartDate;
        $SearchSql .= " AND  (AccBookDate >= '".$StartDate."' AND AccBookDate <= '".$EndDate."' AND AttributionMonth IS NULL) ";
        $SearchSql .= " OR  (concat(AttributionMonth,'01') >= '".str_replace('-','',$StartDate)."' AND concat(AttributionMonth,'01') <= '".str_replace('-','',$EndDate)."'  ".$CompanySql." ".$SelAccSql." )";
        $SearchSql2 = $SearchSql;
        $StudentStatusMonth = date("Y-m",strtotime($StartDate));
    }



    if ($OrderBy == "AccBookConfigID"){
        $OrderBySql = " ORDER BY B.AccBookConfigType asc, B.AccBookConfigSubType asc, B.AccBookConfigName ".$direction.", SumOfMoney asc, A.AccBookSubConfigID ASC, A.AccBookDate asc ";
    } else if ($OrderBy == "AccBookMoney") {
        $OrderBySql = " ORDER BY  B.AccBookConfigType asc, B.AccBookConfigSubType asc, SumOfMoney ".$direction2.",B.AccBookConfigName asc,   A.AccBookSubConfigID ASC, A.AccBookDate asc ";
    }
    // 년도별 또는 월별로 손익계산서를 계산해서 보여준다.
    #------------------------------------------------------------------------------------------------------#



    $Sql = "SELECT  AB.*, AC.AccBookMoney, AC.AccBookDate, AC.SumOfMoney,AC.AccBookSubConfigID  FROM account_bookconfig AB  

			LEFT JOIN	(SELECT * 
				FROM (
					SELECT A.*, sum(AccBookMoney) AS SumOfMoney, B.AccBookConfigType,
						B.AccBookConfigName, B.AccBookConfigSubType, C.AccBookSubConfigName 
					from account_book A 
						left join account_bookconfig B on A.AccBookConfigID=B.AccBookConfigID 
						left join account_booksubconfig C on A.AccBookSubConfigID=C.AccBookSubConfigID ".
        $SearchSql
        ."GROUP BY A.AccBookConfigID "
        .
        ") Agroup
				UNION 
					SELECT * 
				FROM (
					SELECT A.*, sum(AccBookMoney) AS SumOfMoney, B.AccBookConfigType,
						B.AccBookConfigName, B.AccBookConfigSubType, C.AccBookSubConfigName 
					from account_book A 
						left join account_bookconfig B on A.AccBookConfigID=B.AccBookConfigID 
						left join account_booksubconfig C on A.AccBookSubConfigID=C.AccBookSubConfigID ".
        $SearchSql2
        ."GROUP BY A.AccBookConfigID "
        .
        ") Bgroup
				) AC ON AB.AccBookConfigID = AC.AccBookConfigID
			ORDER BY AB.AccBookConfigSubType, AB.AccBookConfigID" ;



    return $Sql;

}


?>