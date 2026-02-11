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
$Sql = "select T.*,M.* from Members as M 
			  left join Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
			      where M.MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$My_MemberID     = $Row["MemberID"];
$My_MemberName   = $Row["MemberName"];
$My_OrganLevel   = $Row["Hr_OrganLevel"];    
$My_OrganLevelID = $Row["Hr_OrganLevelID"];
$My_OrganTask2ID = $Row["Hr_OrganTask2ID"]; 
#-----------------------------------------------------------------------------------------------------------------------------------------#


// 현재 로그인한 사용자의 급여 열람 권한을 가져온다.
$Sql4 = "SELECT *  FROM PayAuth 
			WHERE MemberID = :MemberID";
$Stmt4 = $DbConn->prepare($Sql4);
$Stmt4->bindParam(':MemberID', $My_MemberID);
$Stmt4->execute();
$Stmt4->setFetchMode(PDO::FETCH_ASSOC);
$Row4 = $Stmt4->fetch();

$BasePayAuth = $Row4["BasePayAuth"];
$SpecialDutyPayAuth = $Row4["SpecialDutyPayAuth"];
$PositionPayAuth = $Row4["PositionPayAuth"];
$OverTimePayAuth = $Row4["OverTimePayAuth"];
$ReplacePayAuth = $Row4["ReplacePayAuth"];
$IncentivePayAuth = $Row4["IncentivePayAuth"];
$Special1Auth = $Row4["Special1Auth"];
$Special2Auth = $Row4["Special2Auth"];


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
$opt["caption"] = "급여 열람"; // caption of grid
$opt["autowidth"] = true; // expand grid to screen width

$opt["height"] = "100%";
$opt["multiselect"] = true; // allow you to multi-select through checkboxes
$opt["altRows"] = true; 
$opt["altclass"] = "myAltRowClass"; 

$opt["rowactions"] = true; // allow you to multi-select through checkboxes

// export XLS file
// export to excel parameters
$opt["export"] = array("format"=>"xls", "filename"=>"급여열람", "sheetname"=>"급여열람");

$g->set_options($opt);


$g->set_actions(array(	
						"add"=>false, // allow/disallow add
						"edit"=>false, // allow/disallow edit
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
						E.*
						from Staffs A 
						inner join Franchises B on A.FranchiseID=B.FranchiseID and A.StaffState=1
						inner join Members C on A.StaffID=C.StaffID and C.MemberLevelID=4 
						left outer join Hr_OrganLevelTaskMembers D on C.MemberID=D.MemberID
						left outer join Pay E on C.MemberID=E.MemberID and E.PayMonth = '$PayMonth'";

// this db table will be used for add,edit,delete
$g->table = "Pay";

// you can customize your own columns ...
$col = array();
$col["title"] = "구분ID"; // caption of column
$col["name"] = "PayID"; // grid column name, must be exactly same as returned column-name from sql (tablefield or field-alias)
$col["width"] = "15";
$cols[] = $col;		

$col = array();
$col["title"] = $교사_및_직원명[$LangID];
$col["name"] = "StaffName";
$col["width"] = "50";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = $직무[$LangID];
$col["name"] = "Hr_OrganPositionName";
$col["width"] = "50";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = $프랜차이즈명[$LangID];
$col["name"] = "FranchiseName";
$col["width"] = "50";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

if ($BasePayAuth == 1 || $_LINK_ADMIN_LEVEL_ID_ == 0) {
	$col = array();
	$col["title"] = $기본급[$LangID];
	$col["name"] = "BasePay";
	$col["width"] = "50";
	$col["editable"] = true;
	$col["formatter"] = "number";
	$col["formatoptions"] = array("thousandsSeparator" => ",",
									"decimalSeparator" => ".",
									"decimalPlaces" => 0);
	$cols[] = $col;
}

if ($SpecialDutyPayAuth == 1 || $_LINK_ADMIN_LEVEL_ID_ == 0) {
	$col = array();
	$col["title"] = $특무수당[$LangID];
	$col["name"] = "SpecialDutyPay";
	$col["width"] = "50";
	$col["editable"] = true;
	$col["formatter"] = "number";
	$col["formatoptions"] = array("thousandsSeparator" => ",",
									"decimalSeparator" => ".",
									"decimalPlaces" => 0);
	$cols[] = $col;
}

if ($PositionPayAuth == 1 || $_LINK_ADMIN_LEVEL_ID_ == 0) {
	$col = array();
	$col["title"] = $직책수당[$LangID];
	$col["name"] = "PositionPay";
	$col["width"] = "50";
	$col["editable"] = true;
	$col["formatter"] = "number";
	$col["formatoptions"] = array("thousandsSeparator" => ",",
									"decimalSeparator" => ".",
									"decimalPlaces" => 0);
	$cols[] = $col;
}	

if ($OverTimePayAuth == 1 || $_LINK_ADMIN_LEVEL_ID_ == 0) {
	$col = array();
	$col["title"] = $초과근무수당[$LangID];
	$col["name"] = "OverTimePay";
	$col["width"] = "50";
	$col["editable"] = true;
	$col["formatter"] = "number";
	$col["formatoptions"] = array("thousandsSeparator" => ",",
									"decimalSeparator" => ".",
									"decimalPlaces" => 0);
	$cols[] = $col;
}

if ($ReplacePayAuth == 1 || $_LINK_ADMIN_LEVEL_ID_ == 0) {
	$col = array();
	$col["title"] = $대체수당[$LangID];
	$col["name"] = "ReplacePay";
	$col["width"] = "50";
	$col["editable"] = true;
	$col["formatter"] = "number";
	$col["formatoptions"] = array("thousandsSeparator" => ",",
									"decimalSeparator" => ".",
									"decimalPlaces" => 0);
	$cols[] = $col;
}	

if ($IncentivePayAuth == 1 || $_LINK_ADMIN_LEVEL_ID_ == 0) {
	$col = array();
	$col["title"] = $인센티브[$LangID];
	$col["name"] = "IncentivePay";
	$col["width"] = "50";
	$col["editable"] = true;
	$col["formatter"] = "number";
	$col["formatoptions"] = array("thousandsSeparator" => ",",
									"decimalSeparator" => ".",
									"decimalPlaces" => 0);
	$cols[] = $col;
}	

if ($Special1Auth == 1 || $_LINK_ADMIN_LEVEL_ID_ == 0) {
	$col = array();
	$col["title"] = $상여금명1[$LangID];
	$col["name"] = "SpecialName1";
	$col["width"] = "50";
	$col["editable"] = true;

	$cols[] = $col;
}	

if ($Special1Auth == 1 || $_LINK_ADMIN_LEVEL_ID_ == 0) {
	$col = array();
	$col["title"] = $상여금1[$LangID];
	$col["name"] = "Special1";
	$col["width"] = "50";
	$col["editable"] = true;
	$col["formatter"] = "number";
	$col["formatoptions"] = array("thousandsSeparator" => ",",
									"decimalSeparator" => ".",
									"decimalPlaces" => 0);
	$cols[] = $col;
}	

if ($Special2Auth == 1 || $_LINK_ADMIN_LEVEL_ID_ == 0) {
	$col = array();
	$col["title"] = $상여금명2[$LangID];
	$col["name"] = "SpecialName2";
	$col["width"] = "50";
	$col["editable"] = true;

	$cols[] = $col;
}	

if ($Special2Auth == 1 || $_LINK_ADMIN_LEVEL_ID_ == 0) {
	$col = array();
	$col["title"] = $상여금2[$LangID];
	$col["name"] = "Special2";
	$col["width"] = "50";
	$col["editable"] = true;
	$col["formatter"] = "number";
	$col["formatoptions"] = array("thousandsSeparator" => ",",
									"decimalSeparator" => ".",
									"decimalPlaces" => 0);
	$cols[] = $col;
}	


// pass the cooked columns to grid
$g->set_columns($cols);

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
$SubMenuID = 7707;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

?>


<div id="page_content">
	<div id="page_content_inner">
	<div class="md-card" style="padding:25px;margin:15px;position:relative">
		<form name="SearchForm" method="get">
			<input type="hidden" name="PayInputMode" value="false">
			<h3 class="heading_b uk-margin-bottom"><?=$급여열람[$LangID]?></h3>
			<h3 class="heading_b uk-margin-bottom"><?=$귀속년월[$LangID]?> : 
				<select id="PayMonth" name="PayMonth" class="uk-width-1-4" onchange="SearchSubmit(false)" data-md-select2 data-allow-clear="true" data-placeholder="귀속년월 선택"/>
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
		</form>
		<? if ($PayState != 99)	{ ?>
				<form name="approvalForm" method="get">
					<input type="hidden" name="PayMonthStateID" value="<?=$PayMonthStateID?>">
					<input type="hidden" name="PayMonth" value="<?=$PayMonth?>">
				</form>			
		</div>
		
		<div style="margin:10px">

			<!-- display grid here -->
			<?php echo $out?>
			<!-- display grid here -->
		</div>
		<? } else if ($PayState == 99) {?>
		<div style="margin:10px">
			<br><br><br><br><br><br>
			<center>
			<h4 class="heading_b uk-margin-bottom">
			급여 정보가 생성되어 있지 않았습니다.<br>
			먼저 급여 정보 생성을 하고 급여 정보를 입력해 주세요.
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
function OpenSpecialForm(){
	openurl = "special_form.php";
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"70%"
		,maxWidth: "850"
		,maxHeight: "550"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function CompleteSubmit(){
	document.approvalForm.action = "pay_complete.php";
	document.approvalForm.submit();
}

function EmailSubmit(){
	document.approvalForm.action = "pay_email.php";
	document.approvalForm.submit();
}


function ApprovalSubmit(){
	document.approvalForm.action = "pay_action.php";
	document.approvalForm.submit();
}

function SearchSubmit(PayInputMode){
	document.SearchForm.PayInputMode.value = PayInputMode;
	document.SearchForm.action = "pay_view.php";
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