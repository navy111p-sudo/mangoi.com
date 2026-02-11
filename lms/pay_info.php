<?php
  error_reporting( E_ALL );
  ini_set( "display_errors", 1 );
?>
<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');


include_once('./includes/common.php');
include_once('./gridphp/config_lms.php');

?>

<?php
// 실제 db에 데이타를 업데이트해 주는 함수
function update_data($data)
{
	// you can also use grid object to execute sql, useful in non-mysql driver
	// global $grid;
	// $grid->execute_query("MY-SQL");
	/*
		These comments are just to show the input param format
		$data => Array
		(
			[client_id] => 2
			[params] => Array
				(
					[client_id] => 2
					[name] => Client 2
					[gender] => male
					[company] => Client 2 Company
				)
		)
	*/
	
	global $g;

	//해당 멤버의 4대보험 가입유무를 업데이트한다.

	$Sql3 = "UPDATE PayInfo SET 
		EmploymentInsurance = ".$data["params"]['EmploymentInsurance'].",
		HealthInsurance = ".$data["params"]['HealthInsurance'].",
		CareInsurance = ".$data["params"]['CareInsurance'].",
		NationalPension = ".$data["params"]['NationalPension']."
		WHERE PayInfoID = ".$data['PayInfoID']." 
	";
	
	$g->execute_query($Sql3);
	
}

// 먼저 급여 기본정보 테이블(payInfo)에 Staffs 테이블에 있는 직원들이 다 있는지 확인해 보고
// 없으면 새로 INSERT 해 준다.
/*
$Sql = "SELECT
			A.*,
			B.MemberID 
		from Staffs A 
			inner join Members B on A.StaffID=B.StaffID and B.MemberLevelID=4
		where A.StaffState = 1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

while($Row = $Stmt->fetch()) {
	$Sql2 = "SELECT count(*) AS CountMember FROM PayInfo 
				WHERE MemberID = '".$Row['MemberID']."'";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();

	if ($Row2['CountMember'] == 0) {
		$Sql3 = "INSERT INTO PayInfo (MemberID, regDate) 
					VALUES ('".$Row['MemberID']."',now());";
		$Stmt3 = $DbConn->prepare($Sql3);
		$Stmt3->execute();
	}

}
*/

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
$opt["rowNum"] = 30; // by default 20
$opt["sortname"] = 'StaffID'; // by default sort grid by this field
$opt["sortorder"] = "desc"; // ASC or DESC
$opt["caption"] = $급여기본정보관리[$LangID]; // caption of grid
$opt["autowidth"] = true; // expand grid to screen width
$opt["cellEdit"] = true; //엑셀모드


$opt["height"] = "100%";
$opt["multiselect"] = true; // allow you to multi-select through checkboxes
$opt["altRows"] = true; 
$opt["altclass"] = "myAltRowClass"; 

$opt["rowactions"] = true; // allow you to multi-select through checkboxes

// export XLS file
// export to excel parameters
$opt["export"] = array("format"=>"xls", "filename"=>"급여기본정보", "sheetname"=>"급여정보");

//$opt["afterSaveCell"] = "function(){update_insurance();}";

$g->set_options($opt);

//$e["on_update"] = array("update_data", null, true);

//$g->set_events($e);


$g->set_actions(array(	
						"add"=>false, // allow/disallow add
						"edit"=>true, // allow/disallow edit
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
						AES_DECRYPT(UNHEX(A.StaffPhone1),'$EncryptionKey') as DecStaffPhone1,
						AES_DECRYPT(UNHEX(A.StaffPhone2),'$EncryptionKey') as DecStaffPhone2,
						B.FranchiseName,
						C.MemberLoginID,
						ifnull(D.Hr_OrganLevel,0) as Hr_OrganLevel,
						Hr_OrganPositionName,
						E.*,
						F.Hr_OrganLevelName 
						from Staffs A 
						inner join Franchises B on A.FranchiseID=B.FranchiseID and A.StaffState=1
						inner join Members C on A.StaffID=C.StaffID and C.MemberLevelID=4 
						left outer join Hr_OrganLevelTaskMembers D on C.MemberID=D.MemberID
						left outer join PayInfo E on C.MemberID=E.MemberID
						left outer join Hr_OrganLevels F on D.Hr_OrganLevelID = F.Hr_OrganLevelID
						";

// this db table will be used for add,edit,delete
$g->table = "PayInfo";

// you can customize your own columns ...
$col = array();
$col["title"] = "구분ID"; // caption of column
$col["name"] = "PayInfoID"; // grid column name, must be exactly same as returned column-name from sql (tablefield or field-alias)
$col["width"] = "15";
$cols[] = $col;		

$col = array();
$col["title"] = $교사_및_직원명[$LangID];
$col["name"] = "StaffName";
$col["width"] = "35";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = $부서[$LangID];
$col["name"] = "Hr_OrganLevelName";
$col["width"] = "40";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = $직무[$LangID];
$col["name"] = "Hr_OrganPositionName";
$col["width"] = "30";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = $프랜차이즈명[$LangID];
$col["name"] = "FranchiseName";
$col["width"] = "30";
$col["editable"] = false;
$cols[] = $col;
	
/*
$col = array();
$col["title"] = $전화번호[$LangID];
$col["name"] = "DecStaffPhone1";
$col["width"] = "50";
$col["editable"] = false;
$cols[] = $col;
*/

$col = array();
$col["title"] = $휴대폰번호[$LangID];
$col["name"] = "DecStaffPhone2";
$col["width"] = "45";
$col["editable"] = false;
$cols[] = $col;



$col = array();
$col["title"] = $기본급[$LangID];
$col["name"] = "BasePay";
$col["width"] = "30";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);

$cols[] = $col;

$col = array();
$col["title"] = $특무수당[$LangID];
$col["name"] = "SpecialDutyPay";
$col["width"] = "30";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $직책수당[$LangID];
$col["name"] = "PositionPay";
$col["width"] = "30";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = "고용보험여부";
$col["name"] = "EmploymentInsurance";
$col["width"] = "40";
$col["formatter"] = "select";
$col["edittype"] = "select";
$col["editoptions"] = array("value"=>"1:가입;0:미가입");
$col["editable"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "산재보험여부";
$col["name"] = "IndustrialInsurance";
$col["width"] = "40";
$col["formatter"] = "select";
$col["edittype"] = "select";
$col["editoptions"] = array("value"=>"1:가입;0:미가입");
$col["editable"] = true;
$cols[] = $col;


$col = array();
$col["title"] = "건강보험여부";
$col["name"] = "HealthInsurance";
$col["width"] = "40";
$col["formatter"] = "select";
$col["edittype"] = "select";
$col["editoptions"] = array("value"=>"1:가입;0:미가입");
$col["editable"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "국민연금여부";
$col["name"] = "NationalPension";
$col["width"] = "40";
$col["formatter"] = "select";
$col["edittype"] = "select";
$col["editoptions"] = array("value"=>"1:가입;0:미가입");
$col["editable"] = true;
$cols[] = $col;


$col = array();
$col["title"] = "국민연금보수총액";
$col["name"] = "NationalPay";
$col["width"] = "30";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = "근로소득:0/사업소득:1";
$col["name"] = "WorkType";
$col["width"] = "50";
$col["formatter"] = "select";
$col["edittype"] = "select";
$col["editoptions"] = array("value"=>"1:사업소득;0:근로소득", 
							"onKeyup" =>  "update_insurance();",
							"onchange" =>  "update_insurance();" );
$col["editable"] = true;
$cols[] = $col;



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
$SubMenuID = 7701;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$급여기본정보관리[$LangID]?></h3>
		<p>* 아래 표에서 고용보험여부/산재보험여부/국민연금여부 등은 해당 칸 위에서 0 을 누르면 <미가입> 1을 누르면 <가입>으로 변경됩니다.</p>
		<div style="margin:10px">

			<!-- display grid here -->
			<?php echo $out?>
			<!-- display grid here -->
		</div>
	</div>
</div>		
<!-- common functions -->
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
	// 사업자인지 근로소득자인지에 따라서 4대보험 가입여부를 자동으로 바꿔준다. 
	// 사업자일때는 모두 미가입
	function update_insurance(){
		var setInsurance;
		var selectedType = $("select[name=WorkType]").val();

		var rowId = jQuery('#list1').jqGrid('getGridParam', 'selrow');
		
		// 선택된 줄의 데이타를 모두 가져온다.
		var obj = jQuery('#list1').getRowData(rowId);

		//alert(selectedType);

		// 사업소득자일때는 0으로 미가입 설정 
		if ( selectedType == 1){
			setInsurance = "0";
		} else {
			setInsurance = "1";
			
		}

		/*
		obj.EmploymentInsurance = setInsurance;
		obj.IndustrialInsurance = setInsurance;
		obj.HealthInsurance = setInsurance;
		obj.NationalPension = setInsurance;
		obj.WorkType = selectedType;

		jQuery('#list1').setRowData(rowId,obj);
		*/

		myData = {};
		myData.oper = 'edit';
		myData.PayInfoID = obj.PayInfoID;
        myData.EmploymentInsurance = setInsurance;
		myData.IndustrialInsurance = setInsurance;
		myData.HealthInsurance = setInsurance;
		myData.NationalPension = setInsurance;
		myData.WorkType = selectedType;
        
        console.log(myData);

		
        jQuery.ajax({
            url: "?grid_id=list1",
            dataType: "json",
            data: myData,
            type: "POST",
            error: function(res, status) {
                alert(res.status+" : "+res.statusText+". Status: "+status);
            },
            success: function( data ) {
				jQuery("#list1").jqGrid().trigger('reloadGrid',[{page:1}]);
            }
        });
		
        

		console.log(obj);

	}
	
	
</script>



<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>





</body>
</html>