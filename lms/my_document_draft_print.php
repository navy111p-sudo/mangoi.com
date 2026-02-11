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
include_once('./inc_common_form_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->

<!-- ===========================================   froala_editor   =========================================== -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/froala_editor.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/froala_style.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/code_view.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/draggable.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/colors.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/emoticons.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/image_manager.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/image.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/line_breaker.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/table.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/char_counter.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/video.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/fullscreen.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/file.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/quick_insert.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/help.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/third_party/spell_checker.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/special_characters.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.css">
<!-- ===========================================   froala_editor   =========================================== -->
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 29;
$SubMenuID = 2922; 


$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$DocumentReportID = isset($_REQUEST["DocumentReportID"]) ? $_REQUEST["DocumentReportID"] : "";


$PageTabID = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";
if ($PageTabID==""){
	$PageTabID = "1";
}



$Sql = "SELECT  
				A.*,
				date_format(A.DocumentReportRegDateTime, '%Y-%m-%d')as StrDocumentReportRegDateTime,
				date_format(A.DocumentReportRegDateTime, '%Y년 %m월 %d일')as StrDocumentReportRegDateTime2,
				B.MemberName,
				B.StaffID
		from DocumentReports A 
			inner join Members B on A.MemberID=B.MemberID 
		where A.DocumentReportID=:DocumentReportID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$DocumentReportMemberID = $Row["MemberID"];
$DocumentReportMemberName = $Row["MemberName"];
$DocumentID = $Row["DocumentID"];
$DocumentReportID = $Row["DocumentReportID"];
$DocumentReportName = $Row["DocumentReportName"];
$DocumentReportContent = $Row["DocumentReportContent"];
$DocumentReportState = $Row["DocumentReportState"];
$DocumentReportStaffID = $Row["StaffID"];

$PayDate = $Row["PayDate"];
$AccCode = $Row["AccCode"];
$FileName = $Row["FileName"];
$FileRealName = $Row["FileRealName"];
$OrganName = $Row["OrganName"];
$OrganPhone = $Row["OrganPhone"];
$OrganManagerName = $Row["OrganManagerName"];
$PayMethod = $Row["PayMethod"];
$RequestPayDate = $Row["RequestPayDate"];
$PayMemo = $Row["PayMemo"];

$StrDocumentReportRegDateTime = $Row["StrDocumentReportRegDateTime"];
$StrDocumentReportRegDateTime2 = $Row["StrDocumentReportRegDateTime2"];

// 휴가 문건일 경우 StaffHoliday 와 SpentHoliday 테이블에서 세부 휴가내역을 가지고 온다.
if ($DocumentID == 2) {
	include('./inc_holiday_form_data.php');
}


// 필리핀 강사인지 확인하다
$Sql = "SELECT  
				*
		from Members 
		where MemberID=:MemberID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $DocumentReportMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$StaffID = $Row["StaffID"];
$TeacherID = $Row["TeacherID"];

if ( $StaffID !='0' && $TeacherID !='0') {
	$IsPhTeacher = true;
	// 필리핀 강사인 경우 환율을 가져온다.
	$Sql = "SELECT  
					*
			from Currency  
			where CountryCode='PH'";
	$Stmt = $DbConn->prepare($Sql);
	
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$CountryName = $Row["CountryName"];
	$Currency = $Row["Currency"];

} else {
	$IsPhTeacher = false;
}


$Sql = "SELECT  
				A.*,
				date_format(A.DocumentReportMemberRegDateTime, '%Y-%m-%d')as StrDocumentReportMemberRegDateTime
		from DocumentReportMembers A 
		where A.DocumentReportID=:DocumentReportID
		order by A.DocumentReportMemberOrder desc limit 0,1
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$DocumentReportMemberState = $Row["DocumentReportMemberState"];
$StrDocumentReportMemberRegDateTime = $Row["StrDocumentReportMemberRegDateTime"];

if ($DocumentReportMemberState!=1){
	$StrDocumentReportMemberRegDateTime = "-";
}



$Sql3 = "select A.* from DocumentReportMembers A  where A.DocumentReportID=".$DocumentReportID." and A.MemberID=".$_LINK_ADMIN_ID_." ";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
$Row3 = $Stmt3->fetch();
$DocumentReportMemberState = $Row3["DocumentReportMemberState"];

$TotalItemCount = 20;

?>



<div class="draft_wrap" style="padding:30px;">
	<div class="draft_top">


	<?if ($DocumentID==99) {?>
		<h3 class="draft_title">(주) 에듀비전 기안 및 지출서</h3>
		<table class="draft_approval">
			<col width="">
			<colgroup span="6" width="15%"></colgroup>
	
			<tr style="height:60px;">
				<th rowspan="2">결<br><br>재</th>
				<td>
					<?
					$StrDocumentReportMemberState0 = "-";
					$Sql3 = "select A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=0";
					$Stmt3 = $DbConn->prepare($Sql3);
					$Stmt3->execute();
					$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
					$Row3 = $Stmt3->fetch();
					$Stmt3 = null;

					$MemberName = $Row3["MemberName"];
					$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
					$DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);
					if ($DocumentReportMemberState==0){
						$StrDocumentReportMemberState0 = "-";
					}else if ($DocumentReportMemberState==1){
						$StrDocumentReportMemberState0 = $DocumentReportMemberModiDateTime . "<br>승인";
					}else if ($DocumentReportMemberState==2){
						$StrDocumentReportMemberState0 = $DocumentReportMemberModiDateTime . "<br>반려";
					}
					?>
					<?=$MemberName?>
				</td>
				<td>
					<?
					$StrDocumentReportMemberState1 = "-";
					$Sql3 = "select A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=1";
					$Stmt3 = $DbConn->prepare($Sql3);
					$Stmt3->execute();
					$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
					$Row3 = $Stmt3->fetch();
					$Stmt3 = null;

					$MemberName = $Row3["MemberName"];
					$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
					$DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);
					if ($DocumentReportMemberState==0){
						$StrDocumentReportMemberState1 = "-";
					}else if ($DocumentReportMemberState==1){
						$StrDocumentReportMemberState1 = $DocumentReportMemberModiDateTime . "<br>승인";
					}else if ($DocumentReportMemberState==2){
						$StrDocumentReportMemberState1 = $DocumentReportMemberModiDateTime . "<br>반려";
					}
					?>
					<?=$MemberName?>
				</td>
				<td>
					<?
					$StrDocumentReportMemberState2 = "-";
					$Sql3 = "select A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=2";
					$Stmt3 = $DbConn->prepare($Sql3);
					$Stmt3->execute();
					$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
					$Row3 = $Stmt3->fetch();
					$Stmt3 = null;

					$MemberName = $Row3["MemberName"];
					$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
					$DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);
					if ($DocumentReportMemberState==0){
						$StrDocumentReportMemberState2 = "-";
					}else if ($DocumentReportMemberState==1){
						$StrDocumentReportMemberState2 = $DocumentReportMemberModiDateTime . "<br>승인";
					}else if ($DocumentReportMemberState==2){
						$StrDocumentReportMemberState2 = $DocumentReportMemberModiDateTime . "<br>반려";
					}
					?>
					<?=$MemberName?>
				</td>
				<td>
					<?
					$StrDocumentReportMemberState3 = "-";
					$Sql3 = "select A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=3";
					$Stmt3 = $DbConn->prepare($Sql3);
					$Stmt3->execute();
					$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
					$Row3 = $Stmt3->fetch();
					$Stmt3 = null;

					$MemberName = $Row3["MemberName"];
					$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
					$DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);
					if ($DocumentReportMemberState==0){
						$StrDocumentReportMemberState3 = "-";
					}else if ($DocumentReportMemberState==1){
						$StrDocumentReportMemberState3 = $DocumentReportMemberModiDateTime . "<br>승인";
					}else if ($DocumentReportMemberState==2){
						$StrDocumentReportMemberState3 = $DocumentReportMemberModiDateTime . "<br>반려";
					}
					?>
					<?=$MemberName?>
				</td>
				<td>
					<?
					$StrDocumentReportMemberState4 = "-";
					$Sql3 = "select A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=4";
					$Stmt3 = $DbConn->prepare($Sql3);
					$Stmt3->execute();
					$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
					$Row3 = $Stmt3->fetch();
					$Stmt3 = null;

					$MemberName = $Row3["MemberName"];
					$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
					$DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);
					if ($DocumentReportMemberState==0){
						$StrDocumentReportMemberState4 = "-";
					}else if ($DocumentReportMemberState==1){
						$StrDocumentReportMemberState4 = $DocumentReportMemberModiDateTime . "<br>승인";
					}else if ($DocumentReportMemberState==2){
						$StrDocumentReportMemberState4 = $DocumentReportMemberModiDateTime . "<br>반려";
					}
					?>
					<?=$MemberName?>
				</td>
				<td>
					<?
					$StrDocumentReportMemberState5 = "-";
					$Sql3 = "select A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=5";
					$Stmt3 = $DbConn->prepare($Sql3);
					$Stmt3->execute();
					$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
					$Row3 = $Stmt3->fetch();
					$Stmt3 = null;

					$MemberName = $Row3["MemberName"];
					$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
					$DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);
					if ($DocumentReportMemberState==0){
						$StrDocumentReportMemberState5 = "-";
					}else if ($DocumentReportMemberState==1){
						$StrDocumentReportMemberState5 = $DocumentReportMemberModiDateTime . "<br>승인";
					}else if ($DocumentReportMemberState==2){
						$StrDocumentReportMemberState5 = $DocumentReportMemberModiDateTime . "<br>반려";
					}
					?>
					<?=$MemberName?>
				</td>
			</tr>
			<tr>
				<td><?=$StrDocumentReportMemberState0?></td>
				<td><?=$StrDocumentReportMemberState1?></td>
				<td><?=$StrDocumentReportMemberState2?></td>
				<td><?=$StrDocumentReportMemberState3?></td>
				<td><?=$StrDocumentReportMemberState4?></td>
				<td><?=$StrDocumentReportMemberState5?></td>
			</tr>
		</table>
	</div>
	<table class="draft_table_1">
		<col width="13%">
		<col width="22%">
		<col width="">
		<col width="22%">
		<col width="13%">
		<col width="22%">
		<tr>
			<th class="draft_cell_green">발의</th>
			<td><?=$StrDocumentReportRegDateTime?></td>
			<td>인</td>
			<td></td>
			<th>증빙자료</th>
			<td>
				<?if ($FileName!=""){?>
					<input type="input" id="FileName_" name="FileName_" value="<?=$FileRealName?>" class="draft_input" readonly>
				<?}else{?>
				-
				<?}?>
			</td>
		</tr>
		<tr>
			<th>결재</th>
			<td><?=$StrDocumentReportMemberRegDateTime?></td>
			<td>인</td>
			<td></td>
			<th>계정과목</th>
			<td><input type="input" id="AccCode" name="AccCode" value="<?=$AccCode?>" class="draft_input" readonly></td>
		</tr>
		<tr >
			<th>실 지출일</th>
			<td><input type="input" id="PayDate" name="PayDate" value="<?=$PayDate?>" style="text-align:center;" class="draft_input" readonly></td>
			<td>인</td>
			<td></td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th class="draft_cell_green">제목</th>
			<td colspan="5"><input type="input" id="DocumentReportName" name="DocumentReportName" value="<?=$DocumentReportName?>" class="draft_input" readonly></td>
		</tr>
		<tr>
			<th class="draft_cell_yellow">기안내용</th>
			<td colspan="5" style="text-align:left;"><textarea id="DocumentReportContent" name="DocumentReportContent" class="draft_textarea" readonly><?=$DocumentReportContent?></textarea></td>
		</tr>
	</table>

	<table class="draft_table_2">
		<col width="">
		<col width="11%">
		<col width="15%">
		<col width="15%">
		<col width="15%">
		<?=$IsPhTeacher?"<col width='15%'>":""?>
		<col width="12%">
		<tr>
			<th colspan="<?=$IsPhTeacher?"7":"6"?>" class="draft_table_caption">지출내역</th>
		</tr>
		<tr>
			<th>적요</th>
			<th>수량</th>
			<th>단가</th>
			<th>공급가액</th>
			<th>부가세</th>
			<?=$IsPhTeacher?"<th>한화</th>":""?>
			<th>비고</th>
		</tr>

		<?
		
		$Sql3 = "
				select 
					A.*
				from DocumentReportDetails A 
				where 
					A.DocumentReportDetailState=1 
					and A.DocumentReportID=$DocumentReportID 
				order by A.DocumentReportDetailOrder asc";
		$Stmt3 = $DbConn->prepare($Sql3);
		$Stmt3->execute();
		$Stmt3->setFetchMode(PDO::FETCH_ASSOC);

		$ItemCount = 1;
		$DocumentReportDetailPriceSum = 0;
		$DocumentReportDetailPriceTotSum = 0;
		while($Row3 = $Stmt3->fetch()) {
			
			$DocumentReportDetailName = $Row3["DocumentReportDetailName"];
			$DocumentReportDetailCount = $Row3["DocumentReportDetailCount"];
			$DocumentReportDetailUnitPrice = $Row3["DocumentReportDetailUnitPrice"];
			$DocumentReportDetailPrice = $Row3["DocumentReportDetailPrice"];
			$DocumentReportDetailVat = $Row3["DocumentReportDetailVat"];
			$DocumentReportDetailMemo = $Row3["DocumentReportDetailMemo"];

			$DocumentReportDetailPriceSum = $DocumentReportDetailPriceSum + $DocumentReportDetailPrice;
			$DocumentReportDetailPriceTotSum = $DocumentReportDetailPriceTotSum + $DocumentReportDetailPrice + $DocumentReportDetailVat;
		
		
			if ($DocumentReportDetailName!="" || $DocumentReportDetailCount>0 || $DocumentReportDetailUnitPrice>0 || $DocumentReportDetailMemo!=""){
		?>
		<tr>
			<td><input type="input" id="DocumentReportDetailName_<?=$ItemCount?>" name="DocumentReportDetailName_<?=$ItemCount?>" value="<?=$DocumentReportDetailName?>" class="draft_input" readonly></td>
			<td><input type="input" id="DocumentReportDetailCount_<?=$ItemCount?>" name="DocumentReportDetailCount_<?=$ItemCount?>" value="<?=$DocumentReportDetailCount?>" class="draft_input allownumericwithoutdecimal" readonly></td>
			<td><input type="input" id="DocumentReportDetailUnitPrice_<?=$ItemCount?>" name="DocumentReportDetailUnitPrice_<?=$ItemCount?>" value="<?=number_format($DocumentReportDetailUnitPrice)?>" class="draft_input allownumericwithoutdecimal" readonly></td>
			<td><input type="input" id="DocumentReportDetailPrice_<?=$ItemCount?>" name="DocumentReportDetailPrice_<?=$ItemCount?>" value="<?=number_format($DocumentReportDetailPrice)?>" class="draft_input allownumericwithoutdecimal" readonly></td>
			<td><input type="input" id="DocumentReportDetailVat_<?=$ItemCount?>" name="DocumentReportDetailVat_<?=$ItemCount?>" value="<?=number_format($DocumentReportDetailVat)?>" class="draft_input allownumericwithoutdecimal" readonly></td>
			<?=$IsPhTeacher?"<td><input type='input' id='DocumentReportDetailWon_".$ItemCount."' name='DocumentReportDetailWon_".$ItemCount."'  value='".number_format(($DocumentReportDetailPrice+$DocumentReportDetailVat)*$Currency)."' class='draft_input allownumericwithoutdecimal' readonly></td>":""?>	
			<td><input type="input" id="DocumentReportDetailMemo_<?=$ItemCount?>" name="DocumentReportDetailMemo_<?=$ItemCount?>" value="<?=$DocumentReportDetailMemo?>" class="draft_input" readonly></td>
		</tr>
		<?
				$ItemCount ++;
			}
		}
		$Stmt3 = null;
		?>



		<tr>
			<th>합계</th>
			<td></td>
			<td id="DocumentReportDetailPriceSum"><?=number_format($DocumentReportDetailPriceSum,0)?></td>
			<td></td>
			<td></td>
			<?=$IsPhTeacher?"<td></td>":""?>
			<td></td>
		</tr>  
		<tr>
			<th class="draft_cell_green total">총합계</th>
			<td colspan="<?=$IsPhTeacher?"6":"5"?>"><b id="DocumentReportDetailPriceTotSum"><?=number_format($DocumentReportDetailPriceTotSum,0)?></b></td>
		</tr>          
	</table>

	
	<table class="draft_table_3">
		<col width="25%">
		<col width="">
		<tr>
			<th>1. 거래처명</th>
			<td><input type="input" id="OrganName" name="OrganName" value="<?=$OrganName?>" class="draft_input" readonly></td>
		</tr>
		<tr>
			<th>2. 거래처 연락처</th>
			<td><input type="input" id="OrganPhone" name="OrganPhone" value="<?=$OrganPhone?>" class="draft_input" readonly></td>
		</tr>
		<tr>
			<th>3. 거래처 담당자</th>
			<td><input type="input" id="OrganManagerName" name="OrganManagerName" value="<?=$OrganManagerName?>" class="draft_input" readonly></td>
		</tr>
		<tr>
			<th>4. 결제방법</th>
			<td><input type="input" id="PayMethod" name="PayMethod" value="<?=$PayMethod?>" class="draft_input" readonly></td>
		</tr>
		<tr>
			<th>5. 요청결제일</th>
			<td><input type="input" id="RequestPayDate" name="RequestPayDate" value="<?=$RequestPayDate?>" class="draft_input" readonly></td>
		</tr>
		<tr>
			<th>6. 비고</th>
			<td><input type="input" id="PayMemo" name="PayMemo" value="<?=$PayMemo?>" class="draft_input" readonly></td>
		</tr>
	</table>
	<div class="draft_bottom">
		위 금액을 영수(청구)합니다.<br>
		<?=$StrDocumentReportRegDateTime2?>
		<div class="draft_sign_wrap">작 성 자 : <?=$_LINK_ADMIN_NAME_?> <span class="draft_sign">(인)</span></div>
	</div>
	<?} else if ($DocumentID==2 && $SpentDays >0){
		// 휴가 문건(신규) SpentHoliday 테이블에 레코드가 있으면 신규 방식이다.
			$printMode = true;
			include("./inc_holiday_form.php");
	  } else {
		// 구 방식의 문서를 읽어와서 뿌려준다.
	?>
	  	
	<?=$DocumentReportContent?>


	<?}?>
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
function DocFileDownload(DocumentReportID){
	location.href = "my_document_file_download.php?DocumentReportID="+DocumentReportID;
}


function ChDocumentReportMemberState(DocumentReportMemberState){

	UIkit.modal.confirm(
		'상태를 변경하시겠습니까?', 
		function(){ 

			url = "ajax_set_documet_confirm.php";

			//location.href = url + "?NewID="+NewID;
			$.ajax(url, {
				data: {
					DocumentReportID: <?=$DocumentReportID?>,
					MemberID: <?=$_LINK_ADMIN_ID_?>,
					DocumentReportMemberState: DocumentReportMemberState
				},
				success: function (data) {
					json_data = data;
					
				},
				error: function () {

				}
			});

		}
	);	


}


// 남은 휴가일수를 자동으로 계산
var RemainHoliday = $('#MaxHoliday').val() - $('#SpentHoliday').val() - $('#Holiday').val();
$('#RemainHoliday').attr('value',RemainHoliday);



</script>


<!-- ===========================================   froala_editor   =========================================== -->
<style>
#DocumentReportContent_____ {
  width: 81%;
  margin: auto;
  text-align: left;
}
</style>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/mode/xml/xml.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/froala_editor.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/align.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/char_counter.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/code_beautifier.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/code_view.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/colors.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/draggable.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/emoticons.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/entities.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/file.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/font_size.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/font_family.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/fullscreen.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/image.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/image_manager.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/line_breaker.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/inline_style.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/link.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/lists.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/paragraph_format.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/paragraph_style.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/quick_insert.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/quote.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/table.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/save.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/url.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/video.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/help.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/print.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/third_party/spell_checker.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/special_characters.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/word_paste.min.js"></script>

<script type="text/javascript" src="https://raw.githack.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js"></script>
<script>
(function () {
  const editorInstance = new FroalaEditor('#DocumentReportContent_____', {
	toolbarButtons: ['print'],
	key: "xGE6oB4B3C3A6D6E5fLUQZf1ASFb1EFRNh1Hb1BCCQDUHnA8B6E5B4B1C3I3A1B8A6==",
	enter: FroalaEditor.ENTER_BR,
	heightMin: 300,
	fileUploadURL: '../froala_editor_file_upload.php',
	imageUploadURL: '../froala_editor_image_upload.php',
	placeholderText: null,
	events: {
	  initialized: function () {
		const editor = this
		this.el.closest('form').addEventListener('submit', function (e) {
		  console.log(editor.$oel.val())
		  e.preventDefault()
		})
	  }
	}
  })
})()


</script>
<!-- ===========================================   froala_editor   =========================================== -->




<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>


<script>
print();
</script>
</body>
</html>