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
<?
#-----------------------------------------------------------------------------------------------------------------------------------------#
$MemberLoginID     = isset($_COOKIE["LoginMemberID"   ]) ? $_COOKIE["LoginMemberID"   ] : "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 회원 고유아이디(번호, 조직아이디) 찾기
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "SELECT T.*,M.*, O.* from Members as M 
			  left join Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
			  LEFT JOIN Hr_OrganLevels O on T.Hr_OrganLevelID = O.Hr_OrganLevelID
			      where M.MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$My_MemberID     = $Row["MemberID"];
$My_MemberName   = $Row["MemberName"];
$My_OrganLevel   = $Row["Hr_OrganLevel"];    
$My_OrganName    = $Row["Hr_OrganLevelName"];
$My_OrganLevelID = $Row["Hr_OrganLevelID"];
$My_OrganTask2ID = $Row["Hr_OrganTask2ID"]; 
#-----------------------------------------------------------------------------------------------------------------------------------------#

// 로그인한 회원이 master 이거나 이지애일 경우 아래 문서를 수정할 수 있게 한다. (22.10.21)
$updatable = false;
//if ($MemberLoginID == "이지애1" || $MemberLoginID == "master"){
if ($MemberLoginID == "이지애1" || $MemberLoginID == "master" || $MemberLoginID == "김경숙") {
	$updatable = true;
}

?>


<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 29;
$SubMenuID = 2922; 
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
include_once('./inc_document_common.php');
?>

 

<?php
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
$DocumentReportStaffID = $Row["StaffID"];
$DocumentReportMemberName = $Row["MemberName"];
$DocumentID = $Row["DocumentID"];
$DocumentReportID = $Row["DocumentReportID"];
$DocumentReportName = $Row["DocumentReportName"];
$DocumentReportContent = $Row["DocumentReportContent"];
$DocumentReportState = $Row["DocumentReportState"];

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

$SpentDays = 0;
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


$Sql3 = "SELECT A.* from DocumentReportMembers A  
			where A.DocumentReportID=".$DocumentReportID." and A.MemberID=".$_LINK_ADMIN_ID_." ";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
$Row3 = $Stmt3->fetch();
$DocumentReportMemberState = $Row3["DocumentReportMemberState"];

$TotalItemCount = 20;
?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="DocumentReportID" value="<?=$DocumentReportID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="DocumentReportState" value="<?=$DocumentReportState?>">
		<input type="hidden" name="TotalItemCount" value="<?=$TotalItemCount?>">
		
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname">보고서정보</span><span class="sub-heading" id="user_edit_position"><!--보고서정보--></span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:none;">
							<li <?if ($PageTabID=="1"){?>class="uk-active"<?}?>><a href="#"><?=$직원정보[$LangID]?></a></li>
							<li <?if ($PageTabID=="2"){?>class="uk-active"<?}?>><a href="#"><?=$권한설정[$LangID]?></a></li>
						</ul>


						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-10-10">
											<label for="DocumentReportName" style="margin-right:30px;"><?=$보고서명[$LangID]?></label>
											<?=$DocumentReportName?>
										</div>
									</div>
									<?
									
									$Sql3 = "SELECT MemberID as DocumentReportMemberID from DocumentReportMembers where DocumentReportID=$DocumentReportID and MemberID=$_LINK_ADMIN_ID_";
									$Stmt3 = $DbConn->prepare($Sql3);
									$Stmt3->execute();
									$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
									$Row3 = $Stmt3->fetch();
									$Stmt3 = null;

									$DocumentReportMemberID = $Row3["DocumentReportMemberID"];
									
									// 나 이전에 승인해야 할 단계가 남았으면 [이전단계 승인대기중] 표시 아니면 [결재 대기중]
									if (isPrevApproval($DocumentReportID,$My_MemberID)) {
										$WaitMessage = "이전단계 승인대기중";
									} 
									?>

									<div class="uk-grid" data-uk-grid-margin style="display:<?=(!$DocumentReportMemberID || isPrevApproval($DocumentReportID,$My_MemberID))?"none":""?>">
										<div class="uk-width-medium-10-10">
											<label for="DocumentReportMemberState" style="margin-right:30px;"><?=$상태선택[$LangID]?></label>
											<span class="icheck-inline">
												<input type="radio" class="radio_input" id="DocumentReportMemberState0" name="DocumentReportMemberState" <?php if ($DocumentReportMemberState==0) { echo "checked";}?> value="0" onclick="ChDocumentReportMemberState(0);"/>
												<label for="DocumentReportMemberState0" class="radio_label"><span class="radio_bullet"></span><?=$미설정[$LangID]?></label>
											</span>

											<span class="icheck-inline">
												<input type="radio" class="radio_input" id="DocumentReportMemberState1" name="DocumentReportMemberState" <?php if ($DocumentReportMemberState==1) { echo "checked";}?> value="1" onclick="ChDocumentReportMemberState(1);"/>
												<label for="DocumentReportMemberState1" class="radio_label"><span class="radio_bullet"></span><?=$승인[$LangID]?></label>
											</span>

											<span class="icheck-inline">
												<input type="radio" class="radio_input" id="DocumentReportMemberState2" name="DocumentReportMemberState" <?php if ($DocumentReportMemberState==2) { echo "checked";}?> value="2" onclick="ChDocumentReportMemberState(2);"/>
												<label for="DocumentReportMemberState2" class="radio_label"><span class="radio_bullet"></span><?=$반려[$LangID]?></label>
											</span>
										
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin style="display:<?if (!isPrevApproval($DocumentReportID,$My_MemberID)){?>none<?}?>;">
										<font color='red'><?=$WaitMessage?></font>
										<br>&nbsp;
									</div>

									<?if (!$DocumentReportMemberID){?>
									<br>
									<?}?>
									<div style="text-align:right;">
										<div style="width:150px;height:50px;line-height:50px;text-align:center;margin:3px 0px;background-color:#f1f1f1;border:1px solid #cccccc;border-radius:5px;cursor:pointer;" onclick="javascript:DocPrint(<?=$DocumentReportID?>);"><?=$인쇄[$LangID]?></div>
									</div>
									<?if ($DocumentID!=99 && $SpentDays ==0) {
										// 휴가 문건 예전방식
									?>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<?=$DocumentReportContent?>
											<textarea class="md-input" name="DocumentReportContent" id="DocumentReportContent" cols="30" rows="4" style="display:none;"><?=$DocumentReportContent?></textarea>
										</div>
									</div>
									<?} else if ($DocumentID==2 && $SpentDays >0) {
										// 휴가 문건(신규) SpentHoliday 테이블에 레코드가 있으면 신규 방식이다.
										$printMode = true;
										include("./inc_holiday_form.php"); 

									  } else {
										// 지출 품의서일 경우
									?>
										

									
									<div class="draft_wrap">
										<div class="draft_top">
											<h3 class="draft_title">(주) 에듀비전 기안 및 지출서</h3>
											<table class="draft_approval">
												<col width="">
												<colgroup span="6" width="15%"></colgroup>
												
												<tr style="height:60px;">
													<th rowspan="2">결<br><br>재</th>
													<td>
														<?
														$StrDocumentReportMemberState0 = "-";
														$Sql3 = "SELECT A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID 
																	where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=0";
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
														$Sql3 = "SELECT A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID 
																	where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=1";
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
														$Sql3 = "SELECT A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID 
																	where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=2";
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
														$Sql3 = "SELECT A.*, B.MemberName 
																	from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID 
																	where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=3";
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
														$Sql3 = "SELECT A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID 
																	where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=4";
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
														$Sql3 = "SELECT A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID 
																	where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=5";
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
													<?if ($FileName!=""){
														$fileRealNameArr = explode(',',$FileRealName);
														$fileNameArr = explode(',',$FileName);
														for($i=0; $i<count($fileRealNameArr); $i++){
													?>
														<a href="../uploads/document_files/<?=$fileNameArr[$i]?>" download><?=$fileRealNameArr[$i]?></a><br>
													<?		
														}


													} else {?>
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
												<td><input type="input" id="AccCode" name="AccCode" value="<?=$AccCode?>" class="draft_input" <?=$updatable?"":"readonly"?>></td>
											</tr>
											<tr id="RealPayDate" <?=($My_OrganLevelID == 18 || $My_OrganLevelID == 19)?"":"style=\"display:none;\""?>>
												<th>실 지출일</th>
												<td><input type="input" id="PayDate" name="PayDate" value="<?=$PayDate?>"  data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}" style="text-align:center;" class="draft_input" ></td>
												<td>인</td>
												<td><a type="button" href="javascript:ChangePayDate();" class="md-btn md-btn-primary">실지출일 수정</a></td>
												<td colspan="2"></td>
											</tr>
											<tr>
												<th class="draft_cell_green">제목</th>
												<td colspan="5"><input type="input" id="DocumentReportName" name="DocumentReportName" value="<?=$DocumentReportName?>" class="draft_input"  <?=$updatable?"":"readonly"?>></td>
											</tr>
											<tr>
												<th class="draft_cell_yellow">기안내용</th>
												<td colspan="5" style="text-align:left;"><textarea id="DocumentReportContent" name="DocumentReportContent" class="draft_textarea"  <?=$updatable?"":"readonly"?>><?=$DocumentReportContent?></textarea></td>
											</tr>
										</table>

										<table class="draft_table_2">
											<col width="">
											<col width="11%">
											<col width="15%">
											<col width="15%">
											<col width="15%">
											<?=$IsPhTeacher?"<col width='15%'>":""?>
											<col width="11%">
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
											
											$Sql3 = "SELECT  
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
											$DocumentReportDetailVatSum = 0;
											$KoreandWonSum = 0;
											while($Row3 = $Stmt3->fetch()) {
												
												$DocumentReportDetailName = $Row3["DocumentReportDetailName"];
												$DocumentReportDetailCount = $Row3["DocumentReportDetailCount"];
												$DocumentReportDetailUnitPrice = $Row3["DocumentReportDetailUnitPrice"];
												$DocumentReportDetailPrice = $Row3["DocumentReportDetailPrice"];
												$DocumentReportDetailVat = $Row3["DocumentReportDetailVat"];
												$DocumentReportDetailMemo = $Row3["DocumentReportDetailMemo"];

												$DocumentReportDetailPriceSum = $DocumentReportDetailPriceSum + $DocumentReportDetailPrice;
												$DocumentReportDetailVatSum = $DocumentReportDetailVatSum + $DocumentReportDetailVat;
												$DocumentReportDetailPriceTotSum = $DocumentReportDetailPriceTotSum + $DocumentReportDetailPrice + $DocumentReportDetailVat;
												if ($IsPhTeacher) {
													$KoreandWonSum += ($DocumentReportDetailPrice+$DocumentReportDetailVat)*$Currency;
												}
											?>
											<tr>
												<td><input type="input" id="DocumentReportDetailName_<?=$ItemCount?>" name="DocumentReportDetailName_<?=$ItemCount?>" value="<?=$DocumentReportDetailName?>" class="draft_input"  <?=$updatable?"":"readonly"?>></td>
												<td><input type="input" id="DocumentReportDetailCount_<?=$ItemCount?>" name="DocumentReportDetailCount_<?=$ItemCount?>" value="<?=$DocumentReportDetailCount?>" class="draft_input allownumericwithoutdecimal"  <?=$updatable?"":"readonly"?>></td>
												<td><input type="input" id="DocumentReportDetailUnitPrice_<?=$ItemCount?>" name="DocumentReportDetailUnitPrice_<?=$ItemCount?>" value="<?=number_format($DocumentReportDetailUnitPrice)?>" class="draft_input allownumericwithoutdecimal"  <?=$updatable?"":"readonly"?>></td>
												<td><input type="input" id="DocumentReportDetailPrice_<?=$ItemCount?>" name="DocumentReportDetailPrice_<?=$ItemCount?>" value="<?=number_format($DocumentReportDetailPrice)?>" class="draft_input allownumericwithoutdecimal"  <?=$updatable?"":"readonly"?>></td>
												<td><input type="input" id="DocumentReportDetailVat_<?=$ItemCount?>" name="DocumentReportDetailVat_<?=$ItemCount?>" value="<?=number_format($DocumentReportDetailVat)?>" class="draft_input allownumericwithoutdecimal"  <?=$updatable?"":"readonly"?>></td>
												<?=$IsPhTeacher?"<td><input type='input' id='DocumentReportDetailWon_".$ItemCount."' name='DocumentReportDetailWon_".$ItemCount."'  value='".number_format(($DocumentReportDetailPrice+$DocumentReportDetailVat)*$Currency)."' class='draft_input allownumericwithoutdecimal'  ".($updatable?"":"readonly")."></td>":""?>	
												<td><input type="input" id="DocumentReportDetailMemo_<?=$ItemCount?>" name="DocumentReportDetailMemo_<?=$ItemCount?>" value="<?=$DocumentReportDetailMemo?>" class="draft_input"  <?=$updatable?"":"readonly"?>></td>
											</tr>
											<?
												$ItemCount ++;
											}
											$Stmt3 = null;

											$StartItemCount = $ItemCount;
											for ($ItemCount=$StartItemCount; $ItemCount<=$TotalItemCount; $ItemCount++){
											?>
											<tr>
												<td><input type="input" id="DocumentReportDetailName_<?=$ItemCount?>" name="DocumentReportDetailName_<?=$ItemCount?>" value="" class="draft_input"></td>
												<td><input type="input" id="DocumentReportDetailCount_<?=$ItemCount?>" name="DocumentReportDetailCount_<?=$ItemCount?>" value="0" class="draft_input allownumericwithoutdecimal"></td>
												<td><input type="input" id="DocumentReportDetailUnitPrice_<?=$ItemCount?>" name="DocumentReportDetailUnitPrice_<?=$ItemCount?>" value="0" onfocus="this.select();" class="draft_input allownumericwithoutdecimal"></td>
												<td><input type="input" id="DocumentReportDetailPrice_<?=$ItemCount?>" name="DocumentReportDetailPrice_<?=$ItemCount?>" value="0" class="draft_input allownumericwithoutdecimal"></td>
												<td><input type="input" id="DocumentReportDetailVat_<?=$ItemCount?>" name="DocumentReportDetailVat_<?=$ItemCount?>" value="0" class="draft_input allownumericwithoutdecimal"></td>
												<?=$IsPhTeacher?"<td><input type=input id='DocumentReportDetailVat_".$ItemCount."' name='DocumentReportDetailVat_".$ItemCount."' value='0' class='draft_input allownumericwithoutdecimal'></td>":""?>
												<td><input type="input" id="DocumentReportDetailMemo_<?=$ItemCount?>" name="DocumentReportDetailMemo_<?=$ItemCount?>" value="" class="draft_input"></td>
											</tr>
											<?
											}
											?>



											<tr>
												<th>합계</th>
												<td></td>
												<td></td>
												<td id="DocumentReportDetailPriceSum"><?=number_format($DocumentReportDetailPriceSum,0)?></td>
												<td><?=number_format($DocumentReportDetailVatSum,0)?></td>
												<?=$IsPhTeacher?"<td>".number_format($KoreandWonSum,0)."</td>":""?>
												<td></td>
											</tr>  
											<tr>
												<th class="draft_cell_green total">총합계</th>
												<td colspan="<?=$IsPhTeacher?"6":"5"?>"><b id="DocumentReportDetailPriceTotSum" style="color:#ff0000;"><?=number_format($DocumentReportDetailPriceTotSum,0)?></b></td>
											</tr>          
										</table>

										
										<table class="draft_table_3">
											<col width="25%">
											<col width="">
											<tr>
												<th>1. 거래처명</th>
												<td><input type="input" id="OrganName" name="OrganName" value="<?=$OrganName?>" class="draft_input"  <?=$updatable?"":"readonly"?>></td>
											</tr>
											<tr>
												<th>2. 거래처 연락처</th>
												<td><input type="input" id="OrganPhone" name="OrganPhone" value="<?=$OrganPhone?>" class="draft_input"  <?=$updatable?"":"readonly"?>></td>
											</tr>
											<tr>
												<th>3. 거래처 담당자</th>
												<td><input type="input" id="OrganManagerName" name="OrganManagerName" value="<?=$OrganManagerName?>" class="draft_input"  <?=$updatable?"":"readonly"?>></td>
											</tr>
											<tr>
												<th>4. 결제방법</th>
												<td><input type="input" id="PayMethod" name="PayMethod" value="<?=$PayMethod?>" class="draft_input"  <?=$updatable?"":"readonly"?>></td>
											</tr>
											<tr>
												<th>5. 요청결제일</th>
												<td><input type="input" id="RequestPayDate" name="RequestPayDate" value="<?=$RequestPayDate?>" class="draft_input"  <?=$updatable?"":"readonly"?>></td>
											</tr>
											<tr>
												<th>6. 비고</th>
												<td><input type="input" id="PayMemo" name="PayMemo" value="<?=$PayMemo?>" class="draft_input"  <?=$updatable?"":"readonly"?>></td>
											</tr>
										</table>
										<div class="draft_bottom">
											위 금액을 영수(청구)합니다.<br>
											<?=$StrDocumentReportRegDateTime2?>
<!--											<div class="draft_sign_wrap">영 수 자 : 정 우 영 <span class="draft_sign">(인)</span></div>-->
											<div class="draft_sign_wrap">영 수 자 : <?=$MemberName?> <span class="draft_sign">(인)</span></div>
										</div>
									</div>


									<?}?>

								</div>
							</li>


						</ul>
					</div>
				</div>
			</div>

		</div>
		<? if ($updatable && $DocumentID != 2) {?>
			<div style="padding-left:30%">
			<br>
				<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-warning"><?=$수정하기[$LangID]?></a>
			</div>	
		<? } ?>
		</form>
		

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
var userinput = "";
function DocFileDownload(DocumentReportID){
	location.href = "my_document_file_download.php?DocumentReportID="+DocumentReportID;
}

function DocPrint(DocumentReportID){
	window.open("./my_document_draft_print.php?DocumentReportID="+DocumentReportID, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=100,width=1000,height=900");
}

function ChDocumentReportMemberState(DocumentReportMemberState){

			url = "ajax_set_documet_confirm.php";
			

			// 반려를 선택할 시 프롬프트 창을 띄워서 반려 사유를 입력할 수 있게 한다.
			if (DocumentReportMemberState == 2) {
				userinput = prompt("반려하시는 사유는 무엇인가요?"+"");
			}

			//location.href = url + "?DocumentReportID=<?=$DocumentReportID?>&MemberID=<?=$_LINK_ADMIN_ID_?>&DocumentReportMemberState="+DocumentReportMemberState;
			
			$.ajax(url, {
				data: {
					DocumentReportID: <?=$DocumentReportID?>,
					MemberID: <?=$_LINK_ADMIN_ID_?>,
					DocumentReportMemberState: DocumentReportMemberState,
					Feedback: userinput
				},
				success: function (data) {
					json_data = data;
					alert("상태를 변경했습니다.");
					//location.reload();
					location.href = "my_document_comfirm_list.php?<?=str_replace("^^", "&", $ListParam)?>";
				},
				error: function () {
					alert("에러가 발생했습니다.");
				}
			});


}

//실 지출일을 변경후 저장할 때 ajax 로 php 파일 호출해서 저장한다.
function ChangePayDate(){
	url = "ajax_set_documet_paydate.php";
	var PayDate = $("#PayDate").val();

	$.ajax(url, {
		data: {
			DocumentReportID: <?=$DocumentReportID?>,
			MemberID: <?=$_LINK_ADMIN_ID_?>,
			PayDate: PayDate
		},
		success: function (data) {
			json_data = data;
			alert("실 지출일을 변경했습니다.");
			//location.reload();
			//location.href = "my_document_comfirm_list.php?<?=str_replace("^^", "&", $ListParam)?>";
		},
		error: function () {
			alert("에러가 발생했습니다.");
		}
	});

}


// 남은 휴가일수를 자동으로 계산
var RemainHoliday = $('#MaxHoliday').val() - $('#SpentHoliday').val() - $('#Holiday').val();
$('#RemainHoliday').attr('value',RemainHoliday);



function FormSubmit(){
	
	obj = document.RegForm.DocumentReportName;
	if (obj.value==""){
		UIkit.modal.alert("제목을 입력하세요.");
		obj.focus();
		return;
	}
	

	UIkit.modal.confirm(
		"수정하시겠습니까?", 
		function(){ 
			document.RegForm.action = "my_document_comfirm_action.php";
			document.RegForm.submit();
		}
	);
	
}



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

// DataCredit 수정 - 2024-02-18
function SearchSubmit(){
    var form = document.getElementById('RegForm'); // 'RegForm'은 폼의 ID입니다.
    form.action = "<?=basename($_SERVER['PHP_SELF'])?>"; // 현재 페이지에 대한 액션 설정
    form.submit(); // 폼 제출
}

</script>
<!-- ===========================================   froala_editor   =========================================== -->




<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>