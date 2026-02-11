<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./gridphp/config_lms.php');

// $PayMonth 급여 귀속년월 기본적으로 이번달을 귀속년월로 잡아주고
// 조건을 바꾸면 거기에 맞게 귀속년월이 바뀐다. 매월 1일로 설정한다.(혼선 방지)
$Month = date("Y-m-01");
$PayMonth = isset($_REQUEST["PayMonth"]) ? $_REQUEST["PayMonth"] : $Month;
$PayInputMode = isset($_REQUEST["PayInputMode"]) ? $_REQUEST["PayInputMode"] : "false";   // 새로운 급여정보 생성인지를 표시
$RecalcurationMode = isset($_REQUEST["RecalcurationMode"]) ? $_REQUEST["RecalcurationMode"] : "false";   // 4대보험 재계산모드인지 표시

// 귀속년월의 급여 지급 상태($PayState)를 가져온다. 0:입력, 1:결재요청, 2:결재완료, 3:지급완료
$Sql3 = "SELECT *  FROM PayMonthState 
			WHERE PayMonth = :PayMonth";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->bindParam(':PayMonth', $PayMonth);
$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
$Row3 = $Stmt3->fetch();
$PayState = isset($Row3["PayState"])?$Row3["PayState"]:99; //귀속년월 급여 상태 (0:입력,1:결재요청,2:결재완료,3:지급완료, 99:상태없음)
$PayMonthStateID = isset($Row3["PayMonthStateID"])?$Row3["PayMonthStateID"]:""; 


#-----------------------------------------------------------------------------------------------------------------------------------------#
$MemberLoginID     = isset($_COOKIE["LoginMemberID"   ]) ? $_COOKIE["LoginMemberID"   ] : "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 회원 고유아이디(번호, 조직아이디) 찾기
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "SELECT M.* from Members as M 
			      where M.MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$My_StaffID      = $Row["StaffID"];
#-----------------------------------------------------------------------------------------------------------------------------------------#


// 먼저 PayInsuranceRate 테이블에서 귀속 연도의 보험요율을 가지고 온다.
// 귀속 연도의 보험요율이 있으면 그걸 사용하고 없으면 이전 연도의 보험요율을 가지고 온다.
$Year = substr($PayMonth,0,4);
$PreYear = $Year - 1; 

$Sql3 = "SELECT * FROM PayInsuranceRate  
			WHERE Year = COALESCE((SELECT Year FROM PayInsuranceRate WHERE Year  = :Year),:PreYear)";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->bindParam(':Year', $Year);
$Stmt3->bindParam(':PreYear', $PreYear);

$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
$Row3 = $Stmt3->fetch();

$EmploymentRate = $Row3["EmploymentInsurance"];  	//고용보험 요율
$HealthRate = $Row3["HealthInsurance"];				//건강보험 요율
$CareRate = $Row3["CareInsurance"];					//장기요양보험 요율
$NationalRate = $Row3["NationalPension"];			//국민연금 요율


// 귀속년월의 과세 여부 정보를 가지고 온다. 만약 해당 정보가 없으면 가장 최근의 정보를 가지고 온다.
$TaxInfoPayMonth = substr(str_replace("-","",$PayMonth),0,6);;
$Sql = "SELECT  
				*
		from PayTaxInfo 
		where PayMonth=:TaxInfoPayMonth";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TaxInfoPayMonth', $TaxInfoPayMonth);
$Stmt->execute();
if ($Stmt->rowCount()<=0) {
	$Sql = "SELECT  
		*
		from PayTaxInfo 
		order by PayMonth desc limit 1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
}
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TaxBasePay = $Row["BasePay"];
$TaxSpecialDutyPay = $Row["SpecialDutyPay"];
$TaxPositionPay = $Row["PositionPay"];
$TaxOverTimePay = $Row["OverTimePay"];
$TaxReplacePay = $Row["ReplacePay"];
$TaxIncentivePay = $Row["IncentivePay"];
$TaxSpecial1 = $Row["Special1"];
$TaxSpecial2 = $Row["Special2"];


// include and create object
include(PHPGRID_LIBPATH."inc/jqgrid_dist.php");

$db_conf = array(
                    "type"      => PHPGRID_DBTYPE,
                    "server"    => PHPGRID_DBHOST,
                    "user"      => PHPGRID_DBUSER,
                    "password"  => PHPGRID_DBPASS,
                    "database"  => PHPGRID_DBNAME
                );

$g = new jqgrid($db_conf);

$opt = array();
$opt["rowNum"] = 20; // by default 20
$opt["sortname"] = 'StaffID'; // by default sort grid by this field
$opt["sortorder"] = "desc"; // ASC or DESC
$opt["caption"] = $급여[$LangID]; // caption of grid
$opt["autowidth"] = true; // expand grid to screen width
$opt["cellEdit"] = false;  //엑셀형태로 바로 수정가능하게 만들어준다.

$opt["height"] = "100%";
$opt["multiselect"] = true; // allow you to multi-select through checkboxes
$opt["altRows"] = true; 
$opt["altclass"] = "myAltRowClass"; 

$opt["rowactions"] = true; // allow you to multi-select through checkboxes

// export XLS file
// export to excel parameters
$opt["export"] = array("format"=>"xls", "filename"=>$급여[$LangID], "sheetname"=>$급여[$LangID]);

$opt["cmTemplate"]["visible"] = "xs+"; // show all column on small screens
$opt["shrinkToFit"] = false; // enable horizontal scrollbar

                                                 
$g->set_options($opt);

//급여정보를 수정할 수 없게 세팅
$editable = false;

$g->set_actions(array(	
						"add"=>false, // allow/disallow add
						"edit"=>$editable, // allow/disallow edit
						"delete"=>false, // allow/disallow delete
						"rowactions"=>true, // show/hide row wise edit/del/save option
						"showhidecolumns"=>false, // show/hide row wise edit/del/save option
						"export"=>true, // show/hide export to excel option
						"autofilter" => true, // show/hide autofilter for search
						"search" => "simple" // show single/multi field search condition (e.g. simple or advance)
					) 
				);

// you can provide custom SQL query to display data
$g->select_command = "SELECT  
						A.StaffID,
						A.StaffName,
						B.FranchiseName,
						C.MemberLoginID,
						Hr_OrganPositionName,
						E.*,
						F.WorkType as WorkType1,
						F.EmploymentInsurance AS IsEmployment,
						F.HealthInsurance AS IsHealth,
						F.NationalPension AS IsNational,
						G.DepartmentName 
						from Staffs A 
						inner join Franchises B on A.FranchiseID=B.FranchiseID and A.StaffState=1
						inner join Members C on A.StaffID=C.StaffID and C.MemberLevelID=4 
						left outer join Departments G on G.DepartmentID=A.StaffManageMent 
						left outer join Hr_OrganLevelTaskMembers D on C.MemberID=D.MemberID
						left outer join PayInfo F on C.MemberID=F.MemberID 
						left outer join Pay E on C.MemberID=E.MemberID and E.PayMonth = '$PayMonth'
						WHERE A.StaffID = '$My_StaffID'";

// this db table will be used for add,edit,delete
$g->table = "Pay";


$col = array();
$col["title"] = "구분ID"; // caption of column
$col["name"] = "PayID"; // grid column name, must be exactly same as returned column-name from sql (tablefield or field-alias)
$col["width"] = "40";
$col["hidden"] = true;
$cols[] = $col;		

$col = array();
$col["title"] = $교사_및_직원명[$LangID];
$col["name"] = "StaffName";
$col["width"] = "62";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = $직무[$LangID];
$col["name"] = "Hr_OrganPositionName";
$col["width"] = "62";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = "스태프아이디";
$col["name"] = "StaffID";
$col["editable"] = false; // this column is not editable
$col["hidden"] = true;
$cols[] = $col;


$col = array();
$col["title"] = "부서";
$col["name"] = "DepartmentName";
$col["editable"] = false; // this column is not editable
$col["hidden"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "주민번호1";
$col["name"] = "Jumin1";
$col["editable"] = false; // this column is not editable
$col["hidden"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "급여 지급일";
$col["name"] = "GivePayDate";
$col["editable"] = false; // this column is not editable
$col["hidden"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "근로소득/사업소득";
$col["name"] = "WorkType1";
$col["editable"] = false; // this column is not editable
$col["hidden"] = true;
$cols[] = $col;


$col = array();
$col["title"] = $프랜차이즈명[$LangID];
$col["name"] = "FranchiseName";
$col["width"] = "62";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = $기본급[$LangID];
$col["name"] = "BasePay";
$col["width"] = "66";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);

$cols[] = $col;

$col = array();
$col["title"] = $특무수당[$LangID];
$col["name"] = "SpecialDutyPay";
$col["width"] = "62";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $직책수당[$LangID];
$col["name"] = "PositionPay";
$col["width"] = "62";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $초과근무수당[$LangID];
$col["name"] = "OverTimePay";
$col["width"] = "62";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $대체수당[$LangID];
$col["name"] = "ReplacePay";
$col["width"] = "62";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $인센티브[$LangID];
$col["name"] = "IncentivePay";
$col["width"] = "62";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $상여금명1[$LangID];
$col["name"] = "SpecialName1";
$col["width"] = "62";
$col["editable"] = false;

$cols[] = $col;

$col = array();
$col["title"] = $상여금1[$LangID];
$col["name"] = "Special1";
$col["width"] = "62";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $상여금명2[$LangID];
$col["name"] = "SpecialName2";
$col["width"] = "62";
$col["editable"] = false;

$cols[] = $col;

$col = array();
$col["title"] = $상여금2[$LangID];
$col["name"] = "Special2";
$col["width"] = "62";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $지급합계[$LangID];
$col["name"] = "TotalPay";
$col["width"] = "66";
$col["cellattr"] = "' style=\"color:red;\"' ";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = $고용보험[$LangID];
$col["name"] = "IsEmployment";
$col["hidden"] = true;
$cols[] = $col;

$col = array();
$col["title"] = $건강보험[$LangID];
$col["name"] = "IsHealth";
$col["hidden"] = true;
$cols[] = $col;


$col = array();
$col["title"] = $국민연금[$LangID];
$col["name"] = "IsNational";
$col["hidden"] = true;
$cols[] = $col;


$col = array();
$col["title"] = $고용보험[$LangID];
$col["name"] = "EmploymentInsurance";
$col["width"] = "60";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = $건강보험[$LangID];
$col["name"] = "HealthInsurance";
$col["width"] = "60";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $장기요양보험[$LangID];
$col["name"] = "CareInsurance";
$col["width"] = "60";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = "국민연금<br>보수총액";
$col["name"] = "NationalPay";
$col["width"] = "63";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = $국민연금[$LangID];
$col["name"] = "NationalPension";
$col["width"] = "60";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = $소득세[$LangID];
$col["name"] = "IncomeTax";
$col["width"] = "62";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = $주민세[$LangID];
$col["name"] = "ResidenceTax";
$col["width"] = "62";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = $공제합계[$LangID];
$col["name"] = "SumOfDeductions";
$col["width"] = "66";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = $차인지급액[$LangID];
$col["name"] = "ActualPay";
$col["width"] = "66";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $과세급여[$LangID];
$col["name"] = "TaxationPay";
$col["width"] = "66";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $비과세급여[$LangID];
$col["name"] = "TaxFreePay";
$col["width"] = "66";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = "근로일수";
$col["name"] = "TotalWorkDay";
$col["width"] = "62";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = "총근로시간";
$col["name"] = "TotalWorkTime";
$col["width"] = "62";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 2);
$cols[] = $col;



// pass the cooked columns to grid
$g->set_columns($cols);

$f = array();
$f["column"] = "PayID";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "StaffName";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "Hr_OrganPositionName";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "FranchiseName";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "EmploymentInsurance";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "HealthInsurance";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;


$f = array();
$f["column"] = "CareInsurance";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;


$f = array();
$f["column"] = "NationalPension";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "TaxationPay";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "TaxFreePay";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;


$f = array();
$f["column"] = "TotalPay";
$f["css"] = "'background-color':'#FBEC88', 'color':'green'"; // must use (single quote ') with css attr and value
$f_conditions[] = $f;

$f = array();
$f["column"] = "SumOfDeductions";
$f["css"] = "'background-color':'#FBEC88', 'color':'red'"; // must use (single quote ') with css attr and value
$f_conditions[] = $f;

$f = array();
$f["column"] = "ActualPay";
$f["css"] = "'background-color':'#cef7f0', 'color':'black'"; // must use (single quote ') with css attr and value
$f_conditions[] = $f;


$g->set_conditional_css($f_conditions);


// generate grid output, with unique grid name as 'list1'
$out = $g->render("list1");



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
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
<!-- ---------  GRID4PHP 용 스크립트와 CSS ----------------- -->
<link rel="stylesheet" type="text/css" media="screen" href="./gridphp/lib/js/themes/material/jquery-ui.custom.css"></link>
<link rel="stylesheet" type="text/css" media="screen" href="./gridphp/lib/js/jqgrid/css/ui.jqgrid.css"></link>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.0.1/jquery-migrate.min.js"></script>
<script src="./gridphp/lib/js/jqgrid/js/i18n/grid.locale-kr.js" type="text/javascript"></script>
<script src="./gridphp/lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script src="./gridphp/lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>

<!-- ---------  GRID4PHP 용 스크립트와 CSS ----------------- -->

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 77;
$SubMenuID = 7712;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
include('./inc_departments.php');
$departments = getDepartments($LangID);

?>


<div id="page_content">
	<div id="page_content_inner">
	<div class="md-card" style="min-height:150px;padding:25px;margin:15px;position:relative;min-width:250px">
		<div style="width:40%;float:left;left:10px">
			<form name="SearchForm" method="get">
				<input type="hidden" name="PayInputMode" value="false">
				<input type="hidden" name="RecalcurationMode" value="false">
				<h3 class="heading_b uk-margin-bottom"><?=$급여[$LangID]?></h3>
				<h3 class="heading_b uk-margin-bottom"><?=$귀속년월[$LangID]?> : 
					<select id="PayMonth" name="PayMonth" class="uk-width-2-4" style="min-width:159px" onchange="SearchSubmit(false)" data-md-select2 data-allow-clear="true" data-placeholder="귀속년월 선택"/>
						<option value=""><?=$귀속년월선택[$LangID]?></option>
						<?
						for ($yearCount=2021; $yearCount<=((int)date("Y")); $yearCount++) {
							if ($yearCount == date("Y")) $maxMonth = date("m");
								else $maxMonth =12;
							for ($monthCount=1; $monthCount<=$maxMonth; $monthCount++) {
								$optMonth = $yearCount."-".sprintf('%02d',$monthCount)."-01";
						?>
							<option value="<?=$yearCount?>-<?=sprintf('%02d',$monthCount)?>-01" <?if ($PayMonth==$optMonth){?>selected<?}?>><?=$yearCount?> 년 <?=$monthCount?> 월</option>
						<?
							}
						}
						?>
					</select>
				</h3>
				<p>
					 4대보험요율 : 고용보험 <?=$EmploymentRate?>%, 건강보험 <?=$HealthRate?>%, 장기요양보험 <?=$CareRate?>%, 국민연금 <?=$NationalRate?>% 

				</p>
			</form>
		</div>

		<?
			$Feedback = array();
			$MemberName = array();
			$DocumentReportMemberID = array();
			$DocumentPermited = false;    // 품의서를 승인한 사람이 있는지 체크해서 있으면 true를 넣어준다.
		?>
		<div style="float:right;width:30%;margin-right:5px;min-width:230px">
		<form name="approvalForm" method="get">
		<input type="hidden" name="PayMonthStateID" value="<?=$PayMonthStateID?>">
		<input type="hidden" name="PayMonth" value="<?=$PayMonth?>">
		
			</form>
			</div>
		
		<? include('./inc_category_change.php');?>
		
		
		<? if ($PayState == 0)	{ ?>
		<div style="text-align:center;width:100%;display:inline-block;margin-top:10px">
		<?} else if ($PayState == 1)	{ ?>
		<div class="uk-form-row" style="text-align:center;">
			<h3><?=$현재상태결재요청[$LangID]?></h3>
		<?} else if ($PayState == 2)	{ ?>
		<div class="uk-form-row" style="text-align:center;">
			<h3><?=$현재상태결재완료[$LangID]?></h3>
		<?} else if ($PayState == 3)	{ ?>
		<div class="uk-form-row" style="text-align:center;">
			<h3><?=$현재상태지급완료[$LangID]?></h3>
		<?} ?>
			<a type="button" href="javascript:PayStub()" class="md-btn md-btn-primary"  style="margin-top:10px">급여명세서</a>
		</div>
	</div>					
		<? if ($PayState != 99)	{ ?>				
			<div style="margin:10px">

				<!-- display grid here -->
				<?php echo $out?>
				<!-- display grid here -->
			</div>
		<? } else if ($PayState == 99) {?>

		<div style="margin:10px">
			<br><br><br>
			<center>
			<h4 class="heading_b uk-margin-bottom">
			급여 정보가 생성되어 있지 않았습니다.<br>
			</h4>
		</div>
		<? } ?>
	</div>
</div>		
<!-- common functions grid4php 와 충돌을 막기 위해 jquery를 제거 -->
<script src="assets/js/common_no_jquery.js"></script>
<!-- uikit functions -->
<script src="assets/js/uikit_custom.js"></script>
<!-- altair common functions/helpers -->
<script src="assets/js/altair_admin_common.js"></script>
<!-- select2 -->
<script src="bower_components/select2/dist/js/select2.min.js"></script>

<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->
<script>


//급여명세서를 보여주는 창을 열어준다. 
function PayStub(selectedRows){

	myWindow = window.open("pay_stub.php?selectedRows="+selectedRows, "PayStub", "scrollbars=yes,resizable=yes,top=150,left=300,width=780,height=700");
	myWindow.focus();
}


function SearchSubmit(PayInputMode){
	document.SearchForm.PayInputMode.value = PayInputMode;
	document.SearchForm.action = "pay_self_view.php";
	document.SearchForm.submit();
}

</script>



<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>





</body>
</html>