<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');


$SearchDate       = isset($_REQUEST["SearchDate"      ]) ? $_REQUEST["SearchDate"      ] : Date("Y-m-d");
$AccBookConfigID  = isset($_REQUEST["AccBookConfigID" ]) ? $_REQUEST["AccBookConfigID" ] : 14;


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
	if ($SearchDate != ""){
		$SearchStartYear = substr($SearchDate,0,4);
	} else {
		$SearchStartYear = date("Y");
	}
	  
}
if ($SearchStartMonth==""){
	if ($SearchDate != ""){
		$SearchStartMonth = substr($SearchDate,5,2);
	} else {
		$SearchStartMonth = date("m");
	}
	  
}

// 카드비용 적용을 위한 시작 날짜와 끝 날짜를 가져온다.
$Sql = "SELECT * FROM CardMoneyDate ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$StartDay = $Row["StartDay"];
$EndDay = $Row["EndDay"];


// 귀속월이 입력되어 있는 필드는 해당귀속월도 체크해 준다. 0은 무조건 불포함.
$SearchSql = " WHERE (AttributionMonth <> '0' or AttributionMonth IS NULL) ";

$SearchSql2 = $SearchSql;

// 카드 비용 (AccBookConfigID 14번) 은 union 으로 추가로 쿼리해 온다. 카드비용은 1일~ 말일까지 기준이 아닌 11일~10일까지 기준으로 적용해야 하므로
$SearchSql .= " AND A.AccBookConfigID = $AccBookConfigID ";

$SearchSql2 .= " AND A.AccBookConfigID = 14 ";

$TitleString = "";
$Accounts = array();
$companyAccounts = "";
$SqlCompany = "SELECT * FROM AccountState ";
$CompanySql = "";
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
	
	$SearchSql .= " AND  (YEAR(AccBookDate) = YEAR('".$SearchDate."')  AND AttributionMonth IS NULL) ";
	$SearchSql .= " OR  ( substr(AttributionMonth,1,4) = YEAR('".$SearchDate."')   ".$CompanySql." ".$SelAccSql." )";
	$SearchSql2 = $SearchSql;
	$StudentStatusMonth = date("Y-m"); //학생현황 기준월
	$SearchStartMonth ="";
	
} else if ($Search_sw == "2"){
	
	$SearchSql .= " AND  (YEAR(AccBookDate) = YEAR('".$SearchDate."') AND MONTH(AccBookDate) = MONTH('".$SearchDate."') AND AttributionMonth IS NULL) ";
	$SearchSql .= " OR  (substr(AttributionMonth,1,4) = YEAR('".$SearchDate."') AND substr(AttributionMonth,5,2) = MONTH('".$SearchDate."') AND  A.AccBookConfigID = $AccBookConfigID  ".$CompanySql." ".$SelAccSql."   ) ";
	
	// 카드 비용인 경우 11일~10일(db에 저장한 날짜로 바꿈)까지의 비용을 가지고 온다. 
	$SearchEndMonth = $SearchStartMonth + 1; 
	if ($SearchEndMonth== 13) $SearchEndMonth = 1;
	$SearchDate1 = $SearchStartYear . "-" . iif(strlen($SearchStartMonth) == 1,"0","") . $SearchStartMonth ."-".$StartDay ;
	$SearchDate2 = $SearchStartYear . "-" . iif(strlen($SearchEndMonth) == 1,"0","") . $SearchEndMonth ."-".$EndDay ;
	
	$SearchSql2 .= " AND  (DATE(AccBookDate) >= '".$SearchDate1."'  AND DATE(AccBookDate) <= '".$SearchDate2."'  AND AttributionMonth IS NULL ) ";
	$SearchSql2 .= " OR  (substr(AttributionMonth,1,4) = YEAR('".$SearchDate."') AND substr(AttributionMonth,5,2) = MONTH('".$SearchDate."') AND A.AccBookConfigID = 14  ".$CompanySql." ".$SelAccSql."  ) ";
	$StudentStatusMonth = $SearchStartYear . "-" .$SearchStartMonth;
} else if ($Search_sw == "3"){
	$SearchDate = $StartDate;
	$SearchSql .= " AND  (AccBookDate >= '".$StartDate."' AND AccBookDate <= '".$EndDate."' AND AttributionMonth IS NULL) ";
	$SearchSql .= " OR  (concat(AttributionMonth,'01') >= '".str_replace('-','',$StartDate)."' AND concat(AttributionMonth,'01') <= '".str_replace('-','',$EndDate)."'  AND A.AccBookConfigID = $AccBookConfigID  ".$CompanySql." ".$SelAccSql." )";
	$SearchSql2 = $SearchSql;
	$StudentStatusMonth = date("Y-m",strtotime($StartDate));
}




$OrderBySql = " ORDER BY  B.AccBookConfigType asc, B.AccBookConfigSubType asc, B.AccBookConfigName asc,   A.AccBookSubConfigID ASC, A.AccBookDate asc ";

// 년도별 또는 월별로 손익계산서를 계산해서 보여준다. 
#------------------------------------------------------------------------------------------------------#



if ($AccBookConfigID == 14){

	$Sql = "SELECT A.*, B.AccBookConfigType,
            B.AccBookConfigName, B.AccBookConfigSubType, C.AccBookSubConfigName 
        from account_book A 
            left join account_bookconfig B on A.AccBookConfigID=B.AccBookConfigID 
            left join account_booksubconfig C on A.AccBookSubConfigID=C.AccBookSubConfigID ".
            $SearchSql2 
            .$OrderBySql;
} else {

	$Sql = "SELECT A.*, B.AccBookConfigType,
                B.AccBookConfigName, B.AccBookConfigSubType, C.AccBookSubConfigName 
            from account_book A 
                left join account_bookconfig B on A.AccBookConfigID=B.AccBookConfigID 
                left join account_booksubconfig C on A.AccBookSubConfigID=C.AccBookSubConfigID ".
                $SearchSql 	
                .$OrderBySql;

}







// 귀속월이 입력되어 있는 필드는 해당귀속월도 체크해 준다. 0은 무조건 불포함.
/*
$SearchSql = " WHERE  A.AccBookConfigID = ".$AccBookConfigID."  AND ( (AttributionMonth <> '0' or AttributionMonth IS NULL) ";

if ($Search_sw == "1") {
	$SearchSql .= " AND  (YEAR(AccBookDate) = YEAR('".$SearchDate."')  AND AttributionMonth IS NULL) ";
	$SearchSql .= " OR   substr(AttributionMonth,1,4) = YEAR('".$SearchDate."') )";

} else if ($Search_sw == "2"){
	$SearchSql .= " AND  (YEAR(AccBookDate) = YEAR('".$SearchDate."') AND MONTH(AccBookDate) = MONTH('".$SearchDate."') AND AttributionMonth IS NULL) ";
	$SearchSql .= " OR  (substr(AttributionMonth,1,4) = YEAR('".$SearchDate."') AND substr(AttributionMonth,5,2) = MONTH('".$SearchDate."') ) )";
} else if ($Search_sw == "3"){
	$SearchSql .= " AND  (AccBookDate >= '".$StartDate."' AND AccBookDate <= '".$EndDate."' AND AttributionMonth IS NULL) ";
	$SearchSql .= " OR  (concat(AttributionMonth,'01') >= '".$StartDate."' AND concat(AttributionMonth,'01') <= '".$EndDate."' ) )";
}

	
$Sql = "SELECT A.*, B.*, C.AccBookSubConfigName 
	        from account_book A 
			left join account_bookconfig B on A.AccBookConfigID=B.AccBookConfigID 
			left join account_booksubconfig C on A.AccBookSubConfigID = C.AccBookSubConfigID ".
            $SearchSql    
		   ." order by B.AccBookConfigType asc, B.AccBookConfigID asc, A.AccBookSubConfigID asc"; 
*/

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

//echo $Sql;

?>

<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<div id="page_content">

		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical" style="width:100%;">
								<thead>
									<tr>
										<th nowrap style="width:8%;">No</th>
										<th nowrap>과목명</th>
										<th nowrap style="width:30%;"><?=$세목[$LangID]?></th>
										<th nowrap style="width:22%;">금액</th>
										<th nowrap style="width:22%;">거래일시</th>
									</tr>
								</thead>
								<tbody>
                  
							<?php
                            $ListCount = 0;
							while($Row = $Stmt->fetch()) {

								 $ListCount ++;
								 $AccBookID   = $Row["AccBookID"];
								 $AccBookConfigID   = $Row["AccBookConfigID"];
								 $AccBookConfigType = $Row["AccBookConfigType"];
								 $AccBookConfigName = $Row["AccBookConfigName"];
								 $AccBookSubConfigName = $Row["AccBookSubConfigName"];
								 
								 $AccBookMoney = $Row["AccBookMoney"];
								 $AccBookDate = $Row["AccBookDate"];
                                 ?> 
							<tr>
								<td ><?=$ListCount?></td>
								<td ><?=$AccBookConfigName?></td>
								<td ><?=$AccBookSubConfigName?></td>
								<td style="text-align:center;"><?=number_format($AccBookMoney)?></td>
								<td style="text-align:center;">	<?=$AccBookDate?></td>
							</tr>
                            <?php
								// 만약 세부내역이 존재하면 세부내역 줄도 출력한다.
								$Sql2 = "SELECT * 
											from account_book_detail 
											where AccBookID = ".$AccBookID;
								$Stmt2 = $DbConn->prepare($Sql2);
								$Stmt2->execute();
								if ($Stmt2->rowCount()>0){
								?>
								<tr>
									<td colspan=5 style="padding-left:100px"> 세부 내역 <br> 
								<?
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
									while($Row2 = $Stmt2->fetch()) {
										$AccBookDetailTitle = $Row2["AccBookDetailTitle"];
										$AccBookDetailMoney = $Row2["AccBookDetailMoney"];
									?>
								
									- <?=$AccBookDetailTitle?> : <?=number_format($AccBookDetailMoney)?> <br>
									<?php
									}	
								?>
									</td>
								</tr>

								<?	
								}

							}
							$Stmt = null;
							if ($ListCount==0) {
							?>
								<tr>
									<td class="uk-text-wrap uk-table-td-center" colspan=5><?=$등록된_자료가_없습니다[$LangID]?></td>
								</tr>
							<?php

							}

							?>
								</tbody>
							</table>
						</div>

					</div>
				</div>
			</div>
		</div>

	</div>
</div>


<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<script language="javascript">
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

function FormSubmit(s){

	obj = document.RegForm.account_name;
	if (obj.value==""){
		UIkit.modal.alert("<?=$과목명을_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}
    var modal_msg = jviif(s > 0,"<?=$수정_하시겠습니까[$LangID]?>?","<?=$저장_하시겠습니까[$LangID]?>?")

	UIkit.modal.confirm(
		modal_msg, 
		function(){ 
			document.RegForm.action = "account_book_config_action.php?edit_mode=" + s;
			document.RegForm.submit();
		}
	);

}

function CancleSubmit() {
     
	 document.RegForm.account_name.value  = "";
     document.RegForm.account_id.value    = "";
     document.RegForm.account_subid.value = "";
	 document.RegForm.edit_mode.value     = "";
	 document.RegForm.acc_type.value      = "";
     document.RegForm.submit();

}

function FormEdit(s,edit_id, acc_type) {

    document.RegForm.account_id.value = edit_id;
	document.RegForm.edit_mode.value  = s;
	document.RegForm.acc_type.value   = acc_type;

    if (s == 1 || s == 3) {
	      
          document.RegForm.submit();

	} else {
	     
		  var edit_msg = "";
		  UIkit.modal.confirm(
				'<?=$삭제_하시겠습니까[$LangID]?>?', 
				function(){ 
					document.RegForm.action = "account_book_config_action.php";
					document.RegForm.submit();
				}
		 );

	}


}


function SubFormSubmit(s){

	obj = document.RegForm.account_subname;
	if (obj.value==""){
		UIkit.modal.alert("<?=$세목명을_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}
    var modal_msg = jviif(s==31,"<?=$세목을_수정_하시겠습니까[$LangID]?>?","<?=$세목을_저장_하시겠습니까[$LangID]?>?");

	document.RegForm.edit_mode.value = s;

	UIkit.modal.confirm(
		modal_msg, 
		function(){ 
			document.RegForm.action = "account_book_config_action.php";
			document.RegForm.submit();
		}
	);

}
function SubCancleSubmit() {

     document.RegForm.account_id.value = document.RegForm.Oldaccount_id.value;
	 document.RegForm.acc_type.value   = document.RegForm.Oldacc_type.value;

	 document.RegForm.account_subname.value = "";
     document.RegForm.account_subid.value   = "";
	 document.RegForm.edit_mode.value       = 3;
     document.RegForm.submit();

}

function SubFormEdit(s,edit_id, edit_subid, acc_type) {

    document.RegForm.account_id.value    = edit_id;
    document.RegForm.account_subid.value = edit_subid;
	document.RegForm.edit_mode.value     = s;
	document.RegForm.acc_type.value      = acc_type;
    if (s == 31) {
	      
		  document.RegForm.edit_mode.value = 3;
          document.RegForm.submit();

	} else {
	     
		  var edit_msg = "";
		  UIkit.modal.confirm(
				'<?=$세목을_삭제_하시겠습니까[$LangID]?>?', 
				function(){ 
					document.RegForm.action = "account_book_config_action.php";
					document.RegForm.submit();
				}
		 );

	}


}

</script>




<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>