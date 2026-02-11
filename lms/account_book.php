<?php

error_reporting( E_ALL );
ini_set( "display_errors", 1 );

include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./gridphp/config_lms.php');


$MainMenuID = 77;
$SubMenuID = 7710;

$SearchStartYear  = isset($_REQUEST["SearchStartYear" ]) ? $_REQUEST["SearchStartYear" ] : "";
$SearchStartMonth = isset($_REQUEST["SearchStartMonth"]) ? $_REQUEST["SearchStartMonth"] : "";
$SearchStartDay   = isset($_REQUEST["SearchStartDay"  ]) ? $_REQUEST["SearchStartDay"  ] : "";
$SearchQuarter    = isset($_REQUEST["SearchQuarter"  ])  ? $_REQUEST["SearchQuarter"  ] : "";
$PeriodStartDay   = isset($_REQUEST["PeriodStartDay"  ]) ? $_REQUEST["PeriodStartDay"  ] : "";
$PeriodEndDay     = isset($_REQUEST["PeriodEndDay"  ]) ? $_REQUEST["PeriodEndDay"  ] : "";
$Search_sw        = isset($_REQUEST["Search_sw"       ]) ? $_REQUEST["Search_sw"       ] : "";
$ReloadMonth 	  = isset($_REQUEST["ReloadMonth"]) ? $_REQUEST["ReloadMonth"] : "";


//계좌이체/카드거래 내역을 자동으로 가지고 온다. 
include_once('./card_insert_db.php');
include_once('./account_insert_db.php');


if ($SearchStartYear==""){
	$SearchStartYear = date("Y");
}
if ($SearchStartMonth==""){
	$SearchStartMonth = date("m");
} else {
	if ($SearchStartMonth < 10) $SearchStartMonth = "0".$SearchStartMonth;
}
if ($SearchStartDay=="" && $Search_sw != "4" && $Search_sw != "5"){
	//$SearchStartDay = date("d");	
	$Search_sw = "2";
} else {
	if ($SearchStartDay < 10) $SearchStartDay = "0".$SearchStartDay;
}

$SearchDate = $SearchStartYear . "-" . $SearchStartMonth . "-" .  $SearchStartDay;

$SearchMark = "=";

//월별 검색일 때 월만 나오도록 변수 변경
if ($Search_sw == "2"){
	$SearchStartDay="";
	$SearchDate = $SearchStartYear . "-" . $SearchStartMonth  . "%";
	$SearchMark = "LIKE";
} 

// 기간별 검색일 때 검색 옵션을 설정해 준다.
if ($Search_sw == "4"){
	$SearchQuery = " AccBookDate BETWEEN '".$PeriodStartDay."' AND '".$PeriodEndDay."'  ";
} else if ($Search_sw == "5"){
	
	if ($SearchQuarter == "1"){
		$QuarterStartDay = $SearchStartYear . "-03-01";
		$QuarterEndDay = $SearchStartYear . "-05-31";
	} else if ($SearchQuarter == "2"){
		$QuarterStartDay = $SearchStartYear . "-06-01";
		$QuarterEndDay = $SearchStartYear . "-08-31";
	} else if ($SearchQuarter == "3"){
		$QuarterStartDay = $SearchStartYear . "-09-01";
		$QuarterEndDay = $SearchStartYear . "-11-30";
	} else if ($SearchQuarter == "4"){
		$QuarterStartDay = $SearchStartYear . "-12-01";
		$QuarterEndDay = $SearchStartYear . "-02-29";
	}

	$SearchQuery = " AccBookDate BETWEEN '".$QuarterStartDay."' AND '".$QuarterEndDay."'  ";
} else {
	$SearchQuery = "AccBookDate ".$SearchMark." '".$SearchDate."' ";
}


// ==========================  GridPHP 이용한 각 항목을 보여주는 그리드 세팅 ================ 
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
$opt["sortname"] = 'AccBookType, TransactDate'; // by default sort grid by this field
$opt["sortorder"] = "desc"; // ASC or DESC

$opt["caption"] = $회계관리[$LangID]; // caption of grid
$opt["autowidth"] = true; // expand grid to screen width
// celledit double click (master-detail) – list1 is grid id

$opt["cellEdit"] = true;
$opt["beforeSelectRow"] = "function(rowid) { if (jQuery('#list1').jqGrid('getGridParam','selrow') != rowid) { jQuery('#list1').jqGrid('resetSelection'); jQuery('#list1').setSelection(rowid); } return false; }";
$opt["ondblClickRow"] = "function (rowid, iRow,iCol) { jQuery('#list1').editCell(iRow, iCol, true); }";


$opt["footerrow"] = true;
$opt["reloadedit"] = true;


$opt["height"] = "100%";
$opt["multiselect"] = FALSE; // allow you to multi-select through checkboxes
$opt["altRows"] = true; 
$opt["altclass"] = "myAltRowClass"; 

$opt["rowactions"] = false; 

// export XLS file
// export to excel parameters
$opt["export"] = array("format"=>"xls", "filename"=>$회계관리[$LangID], "sheetname"=>$회계관리[$LangID]);

$opt["cmTemplate"]["visible"] = "xs+"; // show all column on small screens
$opt["shrinkToFit"] = false; // enable horizontal scrollbar


$opt["detail_grid_id"] = "list2";
$opt["subgridparams"] = "AccBookID";
$opt["hidefirst"] = true;


$opt["onSelectRow"] = "function(rid){
    var rowdata = $('#list1').getRowData(rid);
    jQuery('#list2_pager #import_list2, #list2_toppager #import_list2').removeClass('ui-state-disabled');
}";


$g->set_options($opt);



//현재는 항상 수정할 수 있게 세팅	
$editable = true;

$g->set_actions(array(	
						"add"=>false, // allow/disallow add
						"edit"=>$editable, // allow/disallow edit
						"delete"=>true, // allow/disallow delete
						"rowactions"=>true, // show/hide row wise edit/del/save option
						"showhidecolumns"=>false, // show/hide row wise edit/del/save option
						"export"=>true, // show/hide export to excel option
						"autofilter" => true, // show/hide autofilter for search
						"search" => "simple" // show single/multi field search condition (e.g. simple or advance)
					) 
				);

// you can provide custom SQL query to display data
$g->select_command = "SELECT A.*, B.AccountName as AccountName FROM account_book A
						LEFT JOIN AccountState B ON A.AccNumber = B.AccountNumber 
						WHERE ".$SearchQuery;


// this db table will be used for add,edit,delete
$g->table = "account_book";

//echo $g->select_command;

// you can customize your own columns ...
$col = array();
$col["title"] = "구분ID"; // caption of column
$col["name"] = "AccBookID"; // grid column name, must be exactly same as returned column-name from sql (tablefield or field-alias)
$col["width"] = "40";
$cols[] = $col;		

$col = array();
$col["title"] = "거래종류";
$col["name"] = "AccType";
$col["width"] = "60";
$col["formatter"] = "select";
$col["edittype"] = "select";
$col["editoptions"] = array("value"=>"0:계좌이체;1:카드거래;3:현금/기타");
$col["editable"] = false;
$str = "0:계좌이체;1:카드거래;3:현금/기타";
$col["stype"] = "select";
$col["searchoptions"] = array("value" => $str, "separator" => ":", "delimiter" => ";");
$cols[] = $col;

$col = array();
$col["title"] = "카드사/은행";
$col["name"] = "AccountName";
$col["width"] = "70";
$col["editable"] = false; // this column is not editable
$col["align"] = "left"; 
$str = $g->get_dropdown_values("SELECT distinct AccountName as k, AccountName as v FROM AccountState");
$col["stype"] = "select";
$col["searchoptions"] = array("value" => $str, "separator" => ":", "delimiter" => ";");
$cols[] = $col;


$col = array();
$col["title"] = "카드번호/계좌번호";
$col["name"] = "AccNumber";
$col["width"] = "120";
$col["editable"] = false; // this column is not editable
$col["align"] = "left"; 
$str = $g->get_dropdown_values("SELECT distinct AccountNumber as k, AccountNumber as v FROM AccountState");
$col["stype"] = "select";
$col["searchoptions"] = array("value" => $str, "separator" => ":", "delimiter" => ";");
$cols[] = $col;


$col = array();
$col["title"] = $거래일시[$LangID];
$col["name"] = "TransactDate";
$col["width"] = "90";
$col["editable"] = true; // this column is not editable
$col["align"] = "left"; 
$str = $g->get_dropdown_values("SELECT distinct AccBookDate as k, AccBookDate as v FROM account_book WHERE AccBookDate ".$SearchMark." '".$SearchDate."' ");
$col["stype"] = "select";
$col["searchoptions"] = array("value" => $str, "separator" => ":", "delimiter" => ";");
$col["searchoptions"]["sopt"] = array("cn");
$cols[] = $col;

$col = array();
$col["title"] = $수취인의뢰인[$LangID];
$col["name"] = "AccUser";
$col["width"] = "70";
$col["editable"] = true; // this column is not editable
$col["align"] = "left"; 
$str = $g->get_dropdown_values("SELECT distinct AccUser as k, AccUser as v FROM account_book");
$col["stype"] = "select";
$col["searchoptions"] = array("value" => $str, "separator" => ":", "delimiter" => ";");

$cols[] = $col;

$col = array();
$col["title"] = "금액";
$col["name"] = "AccBookMoney";
$col["width"] = "90";
$col["editable"] = false;
$col["hidden"] = TRUE;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = "수익";
$col["name"] = "AccBookMoney1";
$col["dbname"] = "account_book.AccBookMoney";
$col["width"] = "80";
$col["editable"] = false;
$col["search"] = false;
$col["condition"] = array('{AccBookType} == 1', '{AccBookMoney}',null);
$col["formatter"] = 'function(cellval,options,rowdata){ return cellval==null?"":cellval.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","); }';
$cols[] = $col;

$col = array();
$col["title"] = "비용";
$col["name"] = "AccBookMoney2";
$col["dbname"] = "account_book.AccBookMoney";
$col["width"] = "80";
$col["search"] = false;
$col["editable"] = false;
$col["condition"] = array('{AccBookType} == 2', '{AccBookMoney}',null);
$col["formatter"] = 'function(cellval,options,rowdata){ return cellval==null?"":cellval.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","); }';
$cols[] = $col;


$col = array();
$col["title"] = $적요[$LangID];
$col["name"] = "AccBookSubject";
$col["width"] = "120";
$col["editable"] = true; // this column is not editable
$col["align"] = "left"; 

$str = $g->get_dropdown_values("SELECT distinct AccBookSubject as k, AccBookSubject as v FROM account_book");

$col["stype"] = "select";
$col["searchoptions"] = array("value" => $str, "separator" => ":", "delimiter" => ";");
$cols[] = $col;

$col = array();
$col["title"] = $메모[$LangID];
$col["name"] = "TransactMemo";
$col["width"] = "130";
$col["editable"] = true; // this column is not editable
$col["align"] = "left"; 
$str = $g->get_dropdown_values("SELECT distinct TransactMemo as k, TransactMemo as v FROM account_book");

$col["stype"] = "select";
$col["searchoptions"] = array("value" => $str, "separator" => ":", "delimiter" => ";");

$cols[] = $col;

$col = array();
$col["title"] = $점포명[$LangID];
$col["name"] = "StoreName";
$col["width"] = "100";
$col["editable"] = true; // this column is not editable
$col["align"] = "left"; 
$str = $g->get_dropdown_values("SELECT distinct StoreName as k, StoreName as v FROM account_book");
$col["stype"] = "select";
$col["searchoptions"] = array("value" => $str, "separator" => ":", "delimiter" => ";");
$cols[] = $col;


$col = array();
$col["title"] = "카드승인번호";
$col["name"] = "CardApprovalNum";
$col["width"] = "100";
$col["editable"] = true; // this column is not editable
$col["align"] = "left"; 
$cols[] = $col;


$col = array();
$col["title"] = "계정";
$col["name"] = "AccBookType";
$col["width"] = "60";
$col["formatter"] = "select";
$col["edittype"] = "select";
$col["editoptions"] = array("value"=>"1:수익;2:비용");
$col["editable"] = true;
$str = "1:수익;2:비용";
$col["stype"] = "select";
$col["searchoptions"] = array("value" => $str, "separator" => ":", "delimiter" => ";");
$cols[] = $col;

$col = array();
$col["title"] = "계정과목";
$col["name"] = "AccBookConfigID";
$col["dbname"] = "A.AccBookConfigID"; // this is required as we need to search in name field, not id
$col["width"] = "90";
$col["align"] = "left";
$col["search"] = true;
$col["editable"] = true;
$col["formatter"] = "select";
$col["edittype"] = "textarea"; // render as select
# fetch data from database, with alias k for key, v for value
$str = $g->get_dropdown_values("SELECT distinct AccBookConfigID as k, concat(AccBookConfigName,'-' ,AccBookConfigID) as v FROM account_bookconfig ORDER BY AccBookConfigName");
$col["editoptions"] = array(
							"value"=>":Select...;".$str, 
							"onchange" => array( "update_field" => "AccBookSubConfigID",
												"sql" => "SELECT distinct AccBookSubConfigID as k, AccBookSubConfigName as v FROM account_booksubconfig WHERE AccBookConfigID = {AccBookConfigID}"
												)
							);

// reloading dropdown sql
$col["editoptions"]["onload"]["sql"] = "SELECT distinct AccBookConfigID as k, AccBookConfigName as v FROM account_bookconfig "; 

$col["editrules"] = array("required"=>true);
//$col["show"] = array("list"=>true, "add"=>true, "edit"=>true, "view"=>true, "bulkedit"=>false);
$col["stype"] = "select"; // enable dropdown search
$col["searchoptions"] = array("value" => ":;".$str);
$col["formatter"] = "select"; // display label, not value
$cols[] = $col;



$col = array();
$col["title"] = "계정세목";
$col["name"] = "AccBookSubConfigID";
$col["width"] = "90";
$col["search"] = true;
$col["editable"] = true;
$col["edittype"] = "select"; // render as select
$str = $g->get_dropdown_values("SELECT distinct AccBookSubConfigID as k, AccBookSubConfigName as v FROM account_booksubconfig ");
$col["editoptions"] = array("value"=>$str); 
$col["formatter"] = "select"; // display label, not value
$col["stype"] = "select"; // enable dropdown search
$col["searchoptions"] = array("value" => ":;".$str);

$cols[] = $col;

// virtual column for grand total
$col = array();
$col["title"] = "table_total";
$col["name"] = "table_total";
$col["width"] = "100";
$col["hidden"] = true;
$col["search"] = false;
$cols[] = $col;

$col = array();
$col["title"] = "월분류<br>0은 불포함,<br>귀속월 202209 형식";
$col["name"] = "AttributionMonth";
$col["width"] = "120";
$col["editable"] = true; // this column is not editable
$col["align"] = "left"; 
$cols[] = $col;




// pass the cooked columns to grid
$g->set_columns($cols);

//$e["on_data_display"] = array("filter_display", null, true);
// customize phpexcel settings
$e["on_render_excel"] = array("custom_export", null);
$e["on_data_display"] = array("pre_render","",true);
//$e["on_update"] = array("update_rec",null,true);

$e["js_on_select_row"] = "grid_onselect";
$e["js_on_load_complete"] = "grid_onload";

$g->set_events($e);

function update_rec(&$data){
	$money = intval($_GET["AccBookMoney"]);

	unset($data["params"]["AccBookMoney1"]);
	unset($data["params"]["AccBookMoney2"]);

	$data["params"]["AccBookMoney"] = $money;

}

function filter_display($data)
{
	foreach($data["params"] as &$d)
	{
		$d["AccNumber"] = $d["AccNumber"]." ";
	}
}

function pre_render($data)
{
	$rows = $_GET["jqgrid_page"] * $_GET["rows"];
	$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
	$sord = $_GET['sord']; // get the direction
	
	// to where filtered data
	$swhere = "WHERE 1=1 ".$_SESSION["jqgrid_list1_filter"];

	global $g;
	
	// table total (with filter)
	$result = $g->execute_query("SELECT SUM(AccBookMoney) as s FROM (SELECT AccBookMoney  FROM account_book A LEFT JOIN AccountState B ON A.AccNumber = B.AccountNumber  $swhere) AS tmp");
	$rs = $result->GetRows();
	$rs = $rs[0];
	foreach($data["params"] as &$d)
	{
		$d["table_total"] = $rs["s"];
	}	
}


// custom on_export callback function
function custom_export($param)
{
	$objPHPExcel = $param["phpexcel"];
	$arr = $param["data"];

	// column formatting using phpexcel 
	for($r=1;$r<count($arr)+2;$r++)
	{
		// format column D as decimal 0.00
		$objPHPExcel->setActiveSheetIndex(0)->getStyle("D$r")->getNumberFormat()->setFormatCode('0'); 
					
	}
}



$f = array();
$f["column"] = "AccBookType";
$f["op"] = "eq";
$f["value"] = "1";
$f["class"] = "focus-row";
$f_conditions[] = $f;


$g->set_conditional_css($f_conditions);


// generate grid output, with unique grid name as 'list1'
$out = $g->render("list1");



// ==========================  GridPHP 이용한 각 항목을 보여주는 그리드 세팅 끝================ 




//=================================디테일 그리드 시작============================================//


// detail grid
$grid = new jqgrid($db_conf);

// receive id, selected row of parent grid
// check if comma sep numeric ids
$re = '/^([0-9]+[,]?)+$/';
preg_match_all($re, $_GET["rowid"], $matches);
if (count($matches[0]))
    $id = $_GET["rowid"];
else
    $id = intval($_GET["rowid"]);

		

$AccBookID = utf8_encode($_GET["AccBookID"]); // if passed param contains utf8

$opt = array();

$opt["cellEdit"] = false; //엑셀모드
$opt["beforeGrid"] = "function(){ $.jgrid.nav.addtext = '세부내역 추가'; }";
$opt["datatype"] = "local"; // stop loading detail grid at start
$opt["height"] = ""; // autofit height of subgrid
$opt["caption"] = "세부 내역"; // caption of grid
//$opt["multiselect"] = true; // allow you to multi-select through checkboxes
$opt["reloadedit"] = true; // reload after inline edit
$opt["hidefirst"] = true;

// fill detail grid add dialog with master grid id
$opt["add_options"]["afterShowForm"] = 'function() { var selr = jQuery("#list1").jqGrid("getGridParam","selrow");  var n = jQuery("#list1").jqGrid("getCell",selr,"AccBookSubject");  jQuery("#AccBookID").val( selr ); jQuery("#editmodlist2").css("position","relative" ) }';

// reload master after detail update
$opt["onAfterSave"] = "function(){ jQuery('#list1').trigger('reloadGrid',[{current:true}]); }";

$opt["delete_options"]["afterSubmit"] = 'function(response) { if(response.status == 200)
                                                                                {
                                                                                    jQuery("#list1").trigger("reloadGrid",[{current:true}]);
                                                                                    return [true,""];
                                                                                }
                                                                            }';


$grid->set_options($opt);

// and use in sql for filteration
$grid->select_command = "SELECT * FROM account_book_detail 
                            WHERE AccBookID IN ($id)";

$grid->table = "account_book_detail";

//echo $grid->select_command;

$cols = array();

$col = array();
$col["title"] = "세부 내역"; // caption of column
$col["name"] = "AccBookDetailTitle"; // field name, must be exactly same as with SQL prefix or db field
$col["width"] = "150";
$col["editable"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "금액";
$col["name"] = "AccBookDetailMoney";
$col["width"] = "100";
$col["align"] = "left";
$col["search"] = true;
$col["editable"] = true;

$cols[] = $col;


$grid->set_columns($cols,true);

$e["on_insert"] = array("add_detail", null, true);
$e["on_update"] = array("update_detail", null, true);
$grid->set_events($e);

function add_detail(&$data)
{
    $id = intval($_GET["rowid"]);
    $data["params"]["AccBookID"] = $id;
}

function update_detail(&$data)
{
    $id = intval($_GET["rowid"]);
    //$g = $_GET["gender"] . ' client note';
    //$data["params"]["note"] = $g;
    $data["params"]["AccBookID"] = $id;
    
}
// generate grid output, with unique grid name as 'list1'
$out_detail = $grid->render("list2");




//=================================디테일 그리드 끝============================================//



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
<script src="./gridphp/lib/js/jquery.min.js" type="text/javascript"></script>

<script src="./gridphp/lib/js/jqgrid/js/i18n/grid.locale-kr.js" type="text/javascript"></script>
<script src="./gridphp/lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script src="./gridphp/lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>

<!-- ---------  GRID4PHP 용 스크립트와 CSS ----------------- -->

<style>
	.focus-row {
		background: #FFF0F5;
		color: black;
	}
</style>

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
include_once('./inc_top.php'); 
include_once('./inc_menu_left.php');
?>

<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$회계관리[$LangID]?></h3>

		<form name="SearchForm" class="uk-form"  method="post" ENCTYPE="multipart/form-data">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">

					<div class="uk-width-medium-2-10 uk-width-small-1-1" style="padding-top:2px;vertical-align:middle;">
						<select id="SearchStartYear" name="SearchStartYear" class="uk-width-1-3" data-placeholder="<?=$년도선택[$LangID]?>" style="height:40px;"/>
							<option value=""><?=$년도선택[$LangID]?></option>
							<?
							for ($iiii=2019;$iiii<=(int)date("Y");$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchStartYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
						<select id="SearchStartMonth" name="SearchStartMonth" class="uk-width-1-4" onchange="ChSearchStartMonth(1, this.value);" data-placeholder="<?=$월선택[$LangID]?>" style="height:40px"/>
							<option value=""><?=$월선택[$LangID]?></option>
							<?
							for ($iiii=1;$iiii<=12;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchStartMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
							<?
							}
							?>
						</select>
						<button type="button" onclick="javascript:SearchSubmit(2)" class="md-btn md-btn-primary  " style="background-color:#8d73d4;margin-right:15px;">월별 조회</button>
					</div>
					<div class="uk-width-medium-2-10 uk-width-small-1-1" style="padding-top:2px;vertical-align:middle;">	
                        
						<select id="SearchStartDay" name="SearchStartDay" class="uk-width-1-3" data-placeholder="일선택" style="height:40px;"/>
							<option value=""><?=$일선택[$LangID]?></option>
						</select>
						<button type="button" onclick="javascript:SearchSubmit(3)" class="md-btn md-btn-primary" style="background-color:#68b7e2;margin-right:15px;">일별 조회</button>
					</div>
					<div class="uk-width-medium-2-10 uk-width-small-1-1" style="padding-top:2px;vertical-align:middle;">		
						<select id="SearchQuarter" name="SearchQuarter" class="uk-width-1-3" data-placeholder="분기선택" style="height:40px;"/>
							<option value="">분기</option>
							<option value="1" <?=($SearchQuarter ==1)?"selected":""?>>1분기</option>
							<option value="2" <?=($SearchQuarter ==2)?"selected":""?>>2분기</option>
							<option value="3" <?=($SearchQuarter ==3)?"selected":""?>>3분기</option>
							<option value="4" <?=($SearchQuarter ==4)?"selected":""?>>4분기</option>
						</select>
						<button type="button" onclick="javascript:SearchSubmit(5)" class="md-btn md-btn-primary" style="background-color:#6ac7ac;">분기별 조회</button>
                    </div>
					<div class="uk-width-medium-4-10 uk-width-small-1-1" style="padding-top:1px; vertical-align:middle; ">
                        <a href="javascript:OpenAccountConfigForm()" class="md-btn md-btn-primary" style="background-color:#eccc5f; color:#fff;"><?=$계정과목설정[$LangID]?></a>
						<a href="javascript:ReloadSubmit()" class="md-btn md-btn-primary" style="background-color:#eccc5f; color:#fff;font-size:9px">거래내역 다시가져오기</a>
                    </div>
					
					<div class="uk-width-medium-3-10 uk-width-small-1-1" style="padding-top:2px;">
						<input type="text" size=9 id="PeriodStartDay" name="PeriodStartDay"  value="<?=$PeriodStartDay?>" data-uk-datepicker="{format:'YYYY-MM-DD'}"> ~
						<input type="text" size=9 id="PeriodEndDay" name="PeriodEndDay"  value="<?=$PeriodEndDay?>" data-uk-datepicker="{format:'YYYY-MM-DD'}">
						<button type="button" onclick="javascript:SearchSubmit(4)" class="md-btn md-btn-primary" style="background-color:#e45d64;">기간별 조회</a>
					</div>
						
                    
					<div class="uk-width-medium-3-10 uk-width-small-1-1" style="padding-top:2px; vertical-align:middle; ">
                        <a href="./account_income_statement.php?SearchStartYear=<?=$SearchStartYear?>&SearchStartMonth=<?=$SearchStartMonth?>" class="md-btn md-btn-primary" style="background-color:#e27450; color:#fff;">전체<?=$손익계산서[$LangID]?></a>
						<a href="./account_income_statement.php?SearchStartYear=<?=$SearchStartYear?>&SearchStartMonth=<?=$SearchStartMonth?>&SelectedCompany=0" class="md-btn md-btn-primary" style="background-color:#e27450; color:#fff;">MangoI 손익계산서</a>
						<a href="./account_income_statement.php?SearchStartYear=<?=$SearchStartYear?>&SearchStartMonth=<?=$SearchStartMonth?>&SelectedCompany=1" class="md-btn md-btn-primary" style="background-color:#e27450; color:#fff;">SLP 손익계산서</a>
                    </div>

					<div class="uk-width-medium-4-10 uk-width-small-1-1">
						<div class="uk-margin-small-top">
                           <input type="file" id="AccountData" name="AccountData" style="width:40%;">   
						</div>
						<a href="javascript:AccountTable_Upload();" class="md-btn md-btn-primary uk-margin-small-top"><?=$일괄_엑셀자료_올리기[$LangID]?></a>
						<a href="AccountTable.xlsx" class="md-btn md-btn-primary uk-margin-small-top" style="background:#408080;">회계 엑셀자료구조 다운로드</a>
					</div>
				</div>
			</div>
		</div>
		
		</form>

<!-- ==========================  GridPHP 이용한 각 항목을 보여주는 그리드 출력 ================ -->		
		<div class="md-card" style="margin-bottom:10px;">
			<?php //echo $g->select_command;?>
			<div class="md-card-content">
				<!-- display grid here -->
				<?php echo $out?>
				<!-- display grid here -->
			</div>
			<div class="md-card-content">
				<?php echo $out_detail; ?>
			</div>
		</div>

<!-- ==========================  GridPHP 이용한 각 항목을 보여주는 그리드 출력 끝 ================ -->		
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

function fillZero(width, str){
    return str.length >= width ? str:new Array(width-str.length+1).join('0')+str;//남는 길이만큼 0으로 채움
}

function ReloadSubmit(){
	var SearchStartYear, SearchStartMonth, ReloadMonth;

	SearchStartYear = document.SearchForm.SearchStartYear.value
	SearchStartMonth = document.SearchForm.SearchStartMonth.value

	if (SearchStartYear == ""){
		alert('거래내역을 가지고 올 년도를 선택하세요.');
		return;
	}


	if (SearchStartMonth == ""){
		alert('거래내역을 가지고 올 월을 선택하세요.');
		return;
	}

	ReloadMonth = SearchStartYear + fillZero(2, SearchStartMonth);

	document.SearchForm.action = "account_book.php?Search_sw=2&SearchStartYear="+SearchStartYear+"&SearchStartMonth="+SearchStartMonth+"&ReloadMonth="+ReloadMonth;
	document.SearchForm.submit();
}


function SearchSubmit(s){
	var PeriodStartDay, PeriodEndDay;
	var SearchQuarter;
	var SearchStartYear, SearchStartMonth, SearchStartDay;

	if (s==4){
		PeriodStartDay = document.SearchForm.PeriodStartDay.value
		PeriodEndDay = document.SearchForm.PeriodEndDay.value
		document.SearchForm.SearchStartYear.value = PeriodStartDay.substr(0,4);
		document.SearchForm.SearchStartMonth.value = PeriodStartDay.substr(5,2);
		document.SearchForm.SearchStartDay.value= "";
		document.SearchForm.SearchQuarter.value= "";
	} else if (s==5) {
		SearchStartYear = document.SearchForm.SearchStartYear.value
		SearchQuarter = document.SearchForm.SearchQuarter.value
		document.SearchForm.SearchStartMonth.value = "";
		document.SearchForm.SearchStartDay.value= "";
	} else {
		SearchStartYear = document.SearchForm.SearchStartYear.value
		SearchStartMonth = document.SearchForm.SearchStartMonth.value
		SearchStartDay = document.SearchForm.SearchStartDay.value
		document.SearchForm.PeriodStartDay.value = "";
		document.SearchForm.PeriodEndDay.value = "";
		document.SearchForm.SearchQuarter.value= "";
	}
	
	document.SearchForm.action = "account_book.php?Search_sw="+s+"&SearchStartYear="+SearchStartYear+"&SearchStartMonth="+SearchStartMonth+"&SearchStartDay="+SearchStartDay+"&PeriodStartDay="+PeriodStartDay+"&PeriodEndDay="+PeriodEndDay+"&SearchQuarter="+SearchQuarter;
	document.SearchForm.submit();
}

//-------------------------------------------------------------------------------------------------------------------------//
// 계정과목 설정창 오픈
//-------------------------------------------------------------------------------------------------------------------------//
function OpenAccountBookForm(s) {
    
	var YearNumber  = document.SearchForm.SearchStartYear.value;
	var MonthNumber = document.SearchForm.SearchStartMonth.value;
	var DayNumber   = document.SearchForm.SearchStartDay.value;

	openurl = "account_book_form.php?WorkSW="+s+"&YearNumber="+YearNumber+"&MonthNumber="+MonthNumber+"&DayNumber="+DayNumber;

    $.colorbox({    
        href:openurl
        ,width:"98%" 
        ,height:"95%"
        ,maxWidth: "850"
        ,maxHeight: "750"
        ,title:""
        ,iframe:true 
        ,scrolling:true
    }); 

}
//-------------------------------------------------------------------------------------------------------------------------//
// 계정과목 설정창 오픈
//-------------------------------------------------------------------------------------------------------------------------//
function OpenAccountConfigForm() {
    
	openurl = "account_book_config.php";

    $.colorbox({    
        href:openurl
        ,width:"98%" 
        ,height:"95%"
        ,maxWidth: "850"
        ,maxHeight: "750"
        ,title:""
        ,iframe:true 
        ,scrolling:true
    }); 

}

function ChSearchStartMonth(MonthType, MonthNumber){
	
	YearNumber = document.SearchForm.SearchStartYear.value;

	SelBoxInitOption('SearchStartDay');

	SelBoxAddOption( 'SearchStartDay', '일선택', "", "");

	var LastDay = new Date(YearNumber, MonthNumber, 0).getDate();

	for (ii=1 ; ii<=LastDay ; ii++ ){
		ArrOptionText     = ii + "일";
		ArrOptionValue    = ii;

		ArrOptionSelected = "";
		if (ii==<?=(int)$SearchStartDay?>){
			ArrOptionSelected = "selected";
		}

		SelBoxAddOption( 'SearchStartDay', ArrOptionText, ArrOptionValue, ArrOptionSelected );
	}

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
window.onload = function(){
	ChSearchStartMonth(1, <?=(int)$SearchStartMonth?>);
}

jQuery("document").ready(function(){
   	setTimeout(()=>{
			
		jQuery('#list1').jqGrid('navButtonAdd', '#list1_pager',
		{
			'id'      		: 'historyBack',
			'caption'      	: '뒤로가기',
			'buttonicon'   	: 'ui-icon-circle-arrow-w',
			'onClickButton'	: function()
			{
				location.href="account_book.php";
			},
			'position': 'last'
		});


	},10);
});

// 하단에 합계 줄을 보이게 한다.
function grid_onload() 
{

	//아래에 있는 50000원 이상 글자 빨갛게 하는 코드
	do_onload();

	var grid = $("#list1");

	// sum of displayed result
	sum = grid.jqGrid('getCol', 'AccBookMoney', false, 'sum'); // 'sum, 'avg', 'count' (use count-1 as it count footer row).


	// sum of total records
	sum_table = grid.jqGrid('getCol', 'table_total')[0];

	// record count
	c = grid.jqGrid('getCol', 'AccBookID', false, 'count');

	sum = Number(sum).toLocaleString('ko-KR', { style: 'currency', currency: 'KRW' });
	sum_table = Number(sum_table).toLocaleString('ko-KR', { style: 'currency', currency: 'KRW' });

	// 4th arg value of false will disable the using of formatter
	//grid.jqGrid('footerData','set', {AccBookMoney: '합계: ' + sum, TransactMemo : '전체 합계: '+sum_table}, false);
	grid.jqGrid('footerData','set', {AccBookMoney: '합계: ' + sum}, false);
};
	
	// e.g. to update footer summary on selection
function grid_onselect() 
{

	var grid = $("#list1");
	var t = 0;
	var selr = grid.jqGrid('getGridParam','selarrrow'); // array of id's of the selected rows when multiselect options is true. Empty array if not selection 
	for (var x=0;x<selr.length;x++)
	{
		t += parseFloat(grid.jqGrid('getCell', selr[x], 'total'));
	}

	t = Number(t).toLocaleString('ko-KR', { style: 'currency', currency: 'KRW' });

	grid.jqGrid('footerData','set', {invdate: 'Selected Total: '+ t }, false);
};
	

//그리드가 실행되고 나서 50000원 이상의 금액은 색상을 빨간색으로 바꿔준다.
function do_onload()
{
    var grid = $("#list1");
    var ids = grid.jqGrid('getDataIDs');
    for (var i=0;i<ids.length;i++)
    {
        var id=ids[i];
        if (grid.jqGrid('getCell',id,'AccBookMoney') >= 50000)
        {
            grid.jqGrid('setCell',id,'AccBookMoney','',{'color':'red'}); // 글자 색상을 빨강으로
        }
    }
}



</script>
<script>
// 엑셀자료 등록
function AccountTable_Upload() {

	obj = document.SearchForm.AccountData;
	if (obj.value==""){
		UIkit.modal.alert("회계 자료엑셀파일을 선택해 주세요");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'회계 추가 업로드 하시겠습니까?', 
		function(){ 
			document.SearchForm.action = "account_excell_upload.php";
			document.SearchForm.submit();
		}
	);

}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>