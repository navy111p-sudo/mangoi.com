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
$opt["caption"] = "급여"; // caption of grid
$opt["autowidth"] = true; // expand grid to screen width

$opt["height"] = "100%";
$opt["multiselect"] = true; // allow you to multi-select through checkboxes
$opt["altRows"] = true; 
$opt["altclass"] = "myAltRowClass"; 

$opt["rowactions"] = true; // allow you to multi-select through checkboxes

// export XLS file
// export to excel parameters
$opt["export"] = array("format"=>"xls", "filename"=>"급여관리", "sheetname"=>"급여관리");

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
$col["title"] = "교사 및 직원명";
$col["name"] = "StaffName";
$col["width"] = "50";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = "직무";
$col["name"] = "Hr_OrganPositionName";
$col["width"] = "50";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = "프랜차이즈명";
$col["name"] = "FranchiseName";
$col["width"] = "50";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = "기본급";
$col["name"] = "BasePay";
$col["width"] = "50";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);

$cols[] = $col;

$col = array();
$col["title"] = "특무수당";
$col["name"] = "SpecialDutyPay";
$col["width"] = "50";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = "직책수당";
$col["name"] = "PositionPay";
$col["width"] = "50";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = "초과근무수당";
$col["name"] = "OverTimePay";
$col["width"] = "50";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = "대체수당";
$col["name"] = "ReplacePay";
$col["width"] = "50";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = "인센티브";
$col["name"] = "IncentivePay";
$col["width"] = "50";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = "상여금명1";
$col["name"] = "SpecialName1";
$col["width"] = "50";
$col["editable"] = true;

$cols[] = $col;

$col = array();
$col["title"] = "상여금1";
$col["name"] = "Special1";
$col["width"] = "50";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = "상여금명2";
$col["name"] = "SpecialName2";
$col["width"] = "50";
$col["editable"] = true;

$cols[] = $col;

$col = array();
$col["title"] = "상여금2";
$col["name"] = "Special2";
$col["width"] = "50";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

/*
$col = array();
$col["title"] = "상여금명3";
$col["name"] = "SpecialName3";
$col["width"] = "50";
$col["editable"] = true;

$cols[] = $col;

$col = array();
$col["title"] = "상여금3";
$col["name"] = "Special3";
$col["width"] = "50";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;
*/

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
$SubMenuID = 7703;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

?>


<div id="page_content">
	<div id="page_content_inner">
	<div class="md-card" style="padding:25px;margin:15px;position:relative">
		<form name="SearchForm" method="get">
			<input type="hidden" name="PayInputMode" value="false">
			<h3 class="heading_b uk-margin-bottom">급여 결재 관리</h3>
			<h3 class="heading_b uk-margin-bottom">귀속년월 : 
			<select id="PayMonth" name="PayMonth" class="uk-width-1-4" onchange="SearchSubmit(false)" data-md-select2 data-allow-clear="true" data-placeholder="귀속년월 선택"/>
					<option value="">귀속년월 선택</option>
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
			<?

				$Sql3 = "SELECT * 
							from PayMonthState  
							where PayMonth = '$PayMonth'";
				$Stmt3 = $DbConn->prepare($Sql3);
				$Stmt3->execute();
				$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
				$Row3 = $Stmt3->fetch();
				$rowCount = $Stmt3->rowCount();
				if ($rowCount>0){

					$PayMonthStateID = $Row3["PayMonthStateID"];
									
					$Sql3 = "SELECT MemberID as PayApprovalMemberID, ApprovalState 
								from PayApprovalMembers 
								where PayMonthStateID=$PayMonthStateID and MemberID=$_LINK_ADMIN_ID_";
					$Stmt3 = $DbConn->prepare($Sql3);
					$Stmt3->execute();
					$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
					$Row3 = $Stmt3->fetch();
					$rowCount = $Stmt3->rowCount();
					$Stmt3 = null;
					if ($rowCount > 0) {
						$PayApprovalMemberID = $Row3["PayApprovalMemberID"];
						$ApprovalState = $Row3["ApprovalState"];
					}
				}		
				

			?>
		<? if ($rowCount > 0)	{ ?>			
			<div class="uk-grid" data-uk-grid-margin style="display:<?if (!$PayApprovalMemberID){?>none<?}?>;">
				<div class="uk-width-medium-10-10">
					<label for="DocumentReportMemberState" style="margin-right:30px;"><?=$상태선택[$LangID]?></label>
					<span class="icheck-inline">
						<input type="radio" class="radio_input" id="DocumentReportMemberState0" name="DocumentReportMemberState" <?php if ($ApprovalState==0) { echo "checked";}?> value="0" onclick="ChDocumentReportMemberState(0);"/>
						<label for="DocumentReportMemberState0" class="radio_label"><span class="radio_bullet"></span><?=$미설정[$LangID]?></label>
					</span>

					<span class="icheck-inline">
						<input type="radio" class="radio_input" id="DocumentReportMemberState1" name="DocumentReportMemberState" <?php if ($ApprovalState==1) { echo "checked";}?> value="1" onclick="ChDocumentReportMemberState(1);"/>
						<label for="DocumentReportMemberState1" class="radio_label"><span class="radio_bullet"></span><?=$승인[$LangID]?></label>
					</span>

					<span class="icheck-inline">
						<input type="radio" class="radio_input" id="DocumentReportMemberState2" name="DocumentReportMemberState" <?php if ($ApprovalState==2) { echo "checked";}?> value="2" onclick="ChDocumentReportMemberState(2);"/>
						<label for="DocumentReportMemberState2" class="radio_label"><span class="radio_bullet"></span><?=$반려[$LangID]?></label>
					</span>
							
				</div>
			</div>
		</form>
		
		
		<?
			$Feedback = array();
			$MemberName = array();
			$DocumentReportMemberID = array();
			$DocumentPermited = false;    // 품의서를 승인한 사람이 있는지 체크해서 있으면 true를 넣어준다.
		?>
	<form name="approvalForm" method="get">
		<input type="hidden" name="PayMonthStateID" value="<?=$PayMonthStateID?>">
		<table class="draft_approval" style="margin-top:10px;position:absolute;right:20px;bottom:10px">
			<col width="">
			<colgroup span="4" width="22.5%"></colgroup>
			
			<tr style="height:60px;">
				<th rowspan="2">결<br><br>재</th>
			<? for ($tdCount=1;$tdCount<4;$tdCount++) { ?>

				<td>
					<?
					${"StrDocumentReportMemberState".$tdCount} = "-";
					
						
						$Sql3 = "SELECT A.*, B.MemberName from PayApprovalMembers A 
									inner join Members B on A.MemberID=B.MemberID 
									where A.PayMonthStateID = $PayMonthStateID 
									and A.ApprovalMemberOrder = $tdCount";
						$Stmt3 = $DbConn->prepare($Sql3);
						$Stmt3->execute();
						$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
						$Row3 = $Stmt3->fetch();
						$Stmt3 = null;
						$MemberName[$tdCount] = $Row3["MemberName"];
						$Feedback[$tdCount] = $Row3["Feedback"];
						$ApprovalState = $Row3["ApprovalState"];
						$ApprovalModiDateTime = substr($Row3["ApprovalModiDateTime"],0,10);
						if ($ApprovalState==0){
							${"StrDocumentReportMemberState".$tdCount} = "-";
						}else if ($ApprovalState==1){
							$DocumentPermited = true;
							${"StrDocumentReportMemberState".$tdCount} = $ApprovalModiDateTime . "<br>승인";
						}else if ($ApprovalState==2){
							${"StrDocumentReportMemberState".$tdCount} = $ApprovalModiDateTime . "<br>반려";
						}
						echo ("<input type='hidden' id='DocumentReportMemberID".$tdCount."' name='DocumentReportMemberID".$tdCount."' value='".$Row3["MemberID"]."'>");
						echo ($MemberName[$tdCount]); 
						
						?>
						</td>
					<? } ?>
					<td>
					<?
					$StrDocumentReportMemberState4 = "-";
				
					$Sql3 = "SELECT A.*, B.MemberName from PayApprovalMembers A 
									inner join Members B on A.MemberID=B.MemberID 
									where A.PayMonthStateID = $PayMonthStateID 
									and A.ApprovalMemberOrder = 4";
					$Stmt3 = $DbConn->prepare($Sql3);
					$Stmt3->execute();
					$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
					$Row3 = $Stmt3->fetch();
					$Stmt3 = null;
					$MemberName[4] = $Row3["MemberName"];
					$Feedback[4] = $Row3["Feedback"];
					$ApprovalState = $Row3["ApprovalState"];
					$ApprovalModiDateTime = substr($Row3["ApprovalModiDateTime"],0,10);
					if ($ApprovalState==0){
						$StrDocumentReportMemberState4 = "-";
					}else if ($ApprovalState==1){
						$DocumentPermited = true;
						$StrDocumentReportMemberState4 = $ApprovalModiDateTime . "<br>승인";
					}else if ($ApprovalState==2){
						$StrDocumentReportMemberState4 = $ApprovalModiDateTime . "<br>반려";
					}
					echo ("<input type='hidden' id='DocumentReportMemberID4' name='DocumentReportMemberID4' value='22050'>");
					echo ($MemberName[4]); 
				
				?>
				</td>
				</tr>
				<tr>
					<td><?=$StrDocumentReportMemberState1?></td>
					<td><?=$StrDocumentReportMemberState2?></td>
					<td><?=$StrDocumentReportMemberState3?></td>
					<td><?=$StrDocumentReportMemberState4?></td>
				</tr>
			</table>
			<?php 
				for ($i=1; $i<=4; $i++) {
					if (strstr(${"StrDocumentReportMemberState".$i},'반려')){
					$j = $i;
				?>			
				<style>
				.box {
				width: 250px;
				min-height: 50px;
				border: 1px solid gray;
				display: block;
				background-color:bisque;
				/*box-shadow: 5px 5px 20px;*/
				margin: auto;
				margin-bottom: 10px;
				transition: all 0.5s;
				transition-delay: 0.4s;
				padding: 10px;
				}
				.box:hover {
				width: 255px;
				min-height: 55px;
				}
			</style>
			<div>
					<div class="box">
						<h6 style="text-align:center;color:darkslategrey"><?=$MemberName[$j]?> 님의 반려 사유</h6>
						<?=$Feedback[$j]?>
					</div>
			</div>
			</form>			
			<?			
					}
				}
			?>
		</div>
		<?
						$part0 = array();
						$part1 = array();
						$part2 = array();
						$part3 = array();
						$part4 = array();


						// 각 부서별로 직원들 데이터를 가지고 온다.
						for ($i=0; $i<5; $i++) {
							$Sql2 = "SELECT A.StaffID, A.StaffName, B.MemberID, B.MemberName 
										FROM Staffs A 
										LEFT JOIN Members B ON A.StaffID = B.StaffID
										WHERE A.StaffState = 1 AND A.StaffManagement = $i AND B.MemberID <> '' 
										ORDER BY B.MemberName";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							while($Row2 = $Stmt2->fetch()){
								//array_push(${"part".$i},[$Row2["MemberID"] => $Row2["MemberName"]]);
								${"part".$i}[$Row2["MemberID"]] = $Row2["MemberName"];
							}
							//asort(${"part".$i});
						}
						
						?>

						<script>
							// 부서 카테고리별로 직원들 선택할수 있는 내용 변경
							function categoryChange(e, selectName, selectedID) {
								//배열 생성
								for (i=0;i<5;i++) {
									eval("var part"+i+" = [];");
									eval("var part"+i+"Value = [];");
								}
								
									<?
									//배열에 값 넣어주기
									for ($i=0;$i<5;$i++){
										foreach(${"part".$i} as $key => $value){
											if ($key != null)
											echo "part".$i.".push('".$key."');";
											echo "part".$i."Value.push('".$value."');";
										} 
									}
									?>

								
								var target = document.getElementById(selectName);

								if(e.value == "1") var partArray = part1, partArrayValue= part1Value;
								else if(e.value == "2") var partArray = part2, partArrayValue= part2Value;
								else if(e.value == "3") var partArray = part3, partArrayValue= part3Value;
								else if(e.value == "4") var partArray = part4, partArrayValue= part4Value;
								else if(e.value == "0") var partArray = part0, partArrayValue= part0Value;

								target.options.length = 0;

								for (i=0;i<partArray.length;i++) {
									var opt = document.createElement("option");
									opt.value = partArray[i];
									if (partArray[i] == selectedID) opt.selected = true;
									opt.innerHTML = partArrayValue[i];
									target.appendChild(opt);
								}

							}
							<?
							// 저장되어 있던  결재 라인의 부서부분과 결제자의 이름을 세팅한다.
							for ($i=1;$i<4;$i++){
								if ((key($DocumentReportMemberID[($i-1)]) != 0)) {
									$key = key($DocumentReportMemberID[($i-1)]);
									echo "var category = document.getElementById('category".$i."');";
									echo "category.value = ".$DocumentReportMemberID[($i-1)][$key].";";
									echo "categoryChange(category, 'DocumentReportMemberID".$i."', ".$key." );";
								}
							}
							?>
						</script>
		<div style="margin:10px">

			<!-- display grid here -->
			<?php echo $out?>
			<!-- display grid here -->
		</div>
		<? } else {?>
		<div style="margin:10px">
			<br><br><br><br><br><br>
			<center>
			<h4 class="heading_b uk-margin-bottom">
			결재할 급여 정보가 없습니다.
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
function ChDocumentReportMemberState(State){

	url = "ajax_set_paystate_confirm.php";

	// 반려를 선택할 시 프롬프트 창을 띄워서 반려 사유를 입력할 수 있게 한다.
	if (State == 2) {
		userinput = prompt("반려하시는 사유는 무엇인가요?"+"");
	} else {
		userinput = "";
	}

	$.ajax(url, {
		data: {
		<?	if ($PayMonthStateID!="") { ?>
			PayMonthStateID: <?=$PayMonthStateID?>,
		<?}?>	
			MemberID: <?=$_LINK_ADMIN_ID_?>,
			State: State,
			Feedback: userinput
		},
		success: function (data) {
			json_data = data;
			alert("상태를 변경했습니다.");
			location.reload();
		},
		error: function () {
			alert("에러가 발생했습니다.");
		}
	});


}
function SearchSubmit(PayInputMode){
	document.SearchForm.PayInputMode.value = PayInputMode;
	document.SearchForm.action = "pay_confirm_form.php";
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