<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

if ($_LINK_ADMIN_LEVEL_ID_==4){
	header("Location: ./staff_form.php?StaffID=".$_LINK_ADMIN_STAFF_ID_); 
	exit;
}


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
$MainMenuID = 11;
$SubMenuID = 1102;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

$StaffID = isset($_REQUEST["StaffID"]) ? $_REQUEST["StaffID"] : "";
$StaffName = isset($_REQUEST["StaffName"]) ? $_REQUEST["StaffName"] : "";
$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : date("Y");


$Sql = "SELECT  *
		from StaffHoliday
		where StaffID = :StaffID AND Year = :Year";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':StaffID', $StaffID);
$Stmt->bindParam(':Year', $SearchYear);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();

$StaffHolidayID = isset($Row["StaffHolidayID"])?$Row["StaffHolidayID"] : 0;
$MaxHoliday = isset($Row["MaxHoliday"]) ? $Row["MaxHoliday"] : 0;




$Sql = "SELECT  *
		from SpentHoliday A 
			LEFT JOIN StaffHoliday B on A.StaffHolidayID = B.StaffHolidayID 
		where B.StaffID = :StaffID AND B.Year = :Year";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':StaffID', $StaffID);
$Stmt->bindParam(':Year', $SearchYear);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$resultCount = $Stmt->rowCount();


?>


<div id="page_content">
	<form name="SearchForm" method="get">
		<input type="hidden" name="StaffID" value="<?=$StaffID?>">
		<input type="hidden" name="StaffName" value="<?=$StaffName?>">
		<input type="hidden" name="MaxHoliday" value="<?=$MaxHoliday?>">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"> <?=$StaffName?> 휴가 사용 내역
		
			<select id="SearchYear" name="SearchYear" class="uk-width-1-4" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$년도선택[$LangID]?>"/>
				<option value=""><?=$년도선택[$LangID]?></option>
				<?
				for ($yearCount=2018; $yearCount<=(int)date("Y"); $yearCount++) {
				?>
					<option value="<?=$yearCount?>" <?if ((int)$SearchYear==$yearCount){?>selected<?}?>><?=$yearCount?> 년</option>
				<?
				}
				?>
			</select>
		</h3>
		<div class="md-card">
			<div class="md-card-content">
				<h3>&nbsp;&nbsp;&nbsp;최대 휴가일 : <?=$MaxHoliday?> 일</h3>
			</div>
		</div>


		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap>휴가 종류</th>
										<th nowrap>휴가 사용일 수</th>
										<th nowrap>휴가 사유</th>
										<th nowrap>시작날짜</th>
										<th nowrap>끝나는 날짜</th>
										<th nowrap>등록날짜</th>
										<th nowrap>삭제</th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									if ($resultCount > 0) {
										$ListCount = 1;
										while($Row = $Stmt->fetch()) {
											$SpentDays = $Row["SpentDays"];
											$StartDate = $Row["StartDate"];
											$EndDate = $Row["EndDate"];
											$RegistDate = $Row["RegistDate"];
											$Reason = $Row["Reason"];
											$HolidayType = $Row["HolidayType"];
											$DocumentReportID = $Row["DocumentReportID"];
											switch ($HolidayType){
												case 0:
													$HolidayTypeTitle = $일반휴가[$LangID];
													break;
												case 1:
													$HolidayTypeTitle = $병가[$LangID];
													break;
												case 2:
													$HolidayTypeTitle = $직접입력[$LangID];
													break;
											}
											
										?>
										<tr>
											<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
											<td class="uk-text-nowrap uk-table-td-center"><?=$HolidayTypeTitle?></td>
											<td class="uk-text-nowrap uk-table-td-center"><?=$SpentDays?></td>
											<td class="uk-text-nowrap uk-table-td-center"><?=$Reason?></td>
											<td class="uk-text-nowrap uk-table-td-center"><?=$StartDate?></td>
											<td class="uk-text-nowrap uk-table-td-center"><?=$EndDate?></td>
											<td class="uk-text-nowrap uk-table-td-center"><?=$RegistDate?></td>
											
											<td class="uk-text-nowrap uk-table-td-center"><a type="button" href="javascript:HolidaySpentDelete(<?=$DocumentReportID?>);" class="md-btn md-btn-danger">삭제하기</a></td>
										</tr>
										<?php
											$ListCount ++;
										}

										$ListCount--; // 리스트 카운트 값이 마지막인 경우 1을 빼서 맞춘다.

									} else {
									?>	
										<tr>
											<td colspan="7" class="uk-text-nowrap uk-table-td-center">등록된 휴가 사용내역이 없습니다.
											</td>
										</tr>
									<?	
									}
									
									$Stmt = null;
									?>

								</tbody>
							</table>
						</div>
						<br>
						<br>

						<div class="uk-form-row" style="text-align:center;">
							<h3 class="heading_b uk-margin-bottom">휴가 사용 수동등록하기</h3>
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>휴가종류</th>
										<th nowrap>휴가 사용일 수</th>
										<th nowrap>휴가 사유</th>
										<th nowrap>시작날짜</th>
										<th nowrap>끝나는 날짜</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><select id="HolidayType" name="HolidayType"><option value=0>일반 휴가</option><option value=1>병가</option></select></td>
										<td class="uk-text-nowrap uk-table-td-center"><input type="text" id="SpentDays" name="SpentDays" value=""></td>
										<td class="uk-text-nowrap uk-table-td-center" width="30%"><input type="text" id="Reason" name="Reason" value="" style="width:80%"></td>
										<td class="uk-text-nowrap uk-table-td-center"><input type="text" id="StartDate" name="StartDate" value="<?=date("Y-m-d")?>" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}"></td>
										<td class="uk-text-nowrap uk-table-td-center"><input type="text" id="EndDate" name="EndDate" value="<?=date("Y-m-d")?>" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}"></td>
									</tr>

								</tbody>
							</table>
							<a type="button" href="javascript:HolidaySpentSubmit();" class="md-btn md-btn-primary">휴가 사용내역 수동 등록</a>
						</div>

					</div>
				</div>
			</div>
		</div>
		</form>
	</div>
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
function SearchSubmit(){
	document.SearchForm.action = "staff_personal_holiday_list.php";
	document.SearchForm.submit();
}

function HolidaySpentSubmit(){

	url = "ajax_set_spent_holiday.php";
	var maxHoliday = <?=$MaxHoliday?>;

	if (maxHoliday == 0) {
		alert("최대 휴가일부터 먼저 설정해 주세요.");
	} else {

		$.ajax(url, {
			data: {
				StaffHolidayID: <?=$StaffHolidayID?>,
				HolidayType: $('#HolidayType').val(),
				SpentDays: $('#SpentDays').val(),
				Reason: $('#Reason').val(),
				StartDate: $('#StartDate').val(),
				EndDate: $('#EndDate').val(),
			},
			success: function (data) {
				json_data = data;
				alert("수동으로 휴가 사용내역을 저장했습니다.");
				location.reload();
				
			},
			error: function () {
				alert("에러가 발생했습니다.");
			}
		});
	}
}

function HolidaySpentDelete(documentReportID){

	url = "ajax_del_spent_holiday.php";

	if (window.confirm("휴가 내역을 삭제하겠습니까?")) {
		$.ajax(url, {
				data: {
					DocumentReportID: documentReportID
				},
				success: function (data) {
					json_data = data;
					alert("휴가 사용내역을 삭제했습니다.");
					location.reload();
					
				},
				error: function () {
					alert("에러가 발생했습니다.");
				}
			})
	} 

}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>