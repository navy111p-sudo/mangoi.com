<!doctype html>
<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>

<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
include_once('./inc_document_common.php');
?>
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

<style>
	.box {
		width: 160px;
		min-height: 50px;
		border: 1px solid gray;
		display: block;
		background-color:bisque;
		/*box-shadow: 5px 5px 20px;*/
		transition: all 0.5s;
		transition-delay: 0.4s;
		padding: 10px;
	}
	.box:hover {
		width: 165px;
		min-height: 55px;
	}
</style>

<style>
     @media screen and (max-width:700px) {
           .user_content {
               padding: 10px !important;
           }
		   .draft_approval {
			   font-size:8px;
			   width:320px;
		   }
		   .uk-grid > * {
				padding-left: 25px;
			}
			#page_content_inner {
				padding: 30px 10px 30px 10px;
			}
</style>


</head>
<?
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
$My_StaffID   = $Row["StaffID"];
$My_TeacherID   = $Row["TeacherID"];
$My_OrganLevel   = $Row["Hr_OrganLevel"];    
$My_OrganLevelID = $Row["Hr_OrganLevelID"];
$My_OrganTask2ID = $Row["Hr_OrganTask2ID"];
#-----------------------------------------------------------------------------------------------------------------------------------------#
// 필리핀강사인지 아니면 일반 강사나 직원인지를 확인한다.
if ($My_StaffID !=0 && $My_TeacherID !=0) {
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

?>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 29;
$SubMenuID = 2923; 
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
include('./inc_departments.php');
$departments = getDepartments($LangID);

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$DocumentReportID = isset($_REQUEST["DocumentReportID"]) ? $_REQUEST["DocumentReportID"] : "";
$DocumentID = isset($_REQUEST["DocumentID"]) ? $_REQUEST["DocumentID"] : "";
$CopyMode = isset($_REQUEST["CopyMode"]) ? $_REQUEST["CopyMode"] : false;

$PageTabID = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";
if ($PageTabID==""){
	$PageTabID = "1";
}


if ($DocumentReportID!=""){  // 이전 문서를 보여주거나 수정하는 경우 (복사해서 신규 작성하는 경우 포함)

	$Sql = "SELECT 
					A.*,
					date_format(A.DocumentReportRegDateTime, '%Y-%m-%d')as StrDocumentReportRegDateTime,
					date_format(A.DocumentReportRegDateTime, '%Y년 %m월 %d일')as StrDocumentReportRegDateTime2,
					B.MemberName
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


	$DocumentReportState = $Row["DocumentReportState"];

	$StrDocumentReportRegDateTime = $Row["StrDocumentReportRegDateTime"];
	$StrDocumentReportRegDateTime2 = $Row["StrDocumentReportRegDateTime2"];


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

	// 만약 예전 문서 복사해서 신규로 등록하는 거라면
	if($CopyMode) {
		$CopyDocumentReportID = $DocumentReportID;  // 원래 문서의 ID를 카피 ID로 복사해 놓는다.
		$DocumentReportID = 0;

		$DocumentReportState = 0;
		$FileName = "";
		$FileRealName = "";
		$StrDocumentReportRegDateTime = date("Y-m-d");
		$RequestPayDate = date("Y-m-d");
		$StrDocumentReportRegDateTime2 = date("Y년 m월 d일");

		$StrDocumentReportMemberRegDateTime = "-";
		$DocumentID = 99;
	}


} else {  // 신규 문서 작성인 경우
	$DocumentReportID = 0;

	$DocumentReportMemberID = $_LINK_ADMIN_ID_;
	$DocumentReportMemberName = $_LINK_ADMIN_NAME_;
	$DocumentReportName = "";
	$DocumentReportContent = "";

	$PayDate = date("Y-m-d");
	$AccCode = "";
	$FileName = "";
	$FileRealName = "";
	$OrganName = "";
	$OrganPhone = "";
	$OrganManagerName = "";
	$PayMethod = "";
	$RequestPayDate = date("Y-m-d");
	$PayMemo = "";

	$DocumentReportState = 0;

	$StrDocumentReportRegDateTime = date("Y-m-d");
	$StrDocumentReportRegDateTime2 = date("Y년 m월 d일");

	$StrDocumentReportMemberRegDateTime = "-";


	$DocumentID = 99;

}


$TotalItemCount = 20;

//고정 결재 라인을 가져온다.
if($IsPhTeacher) $DocumentType=2;
else $DocumentType=0;
$FixedApprovalLine = array();
$Sql = "SELECT  A.*, B.MemberName, C.StaffManagement 
			from FixedApprovalLine A
			LEFT JOIN Members B ON A.MemberID = B.MemberID
			LEFT JOIN Staffs C ON B.StaffID = C.StaffID
			where DocumentType=:DocumentType order by ApprovalSequence";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':DocumentType', $DocumentType);
$Stmt->execute();

$Stmt->setFetchMode(PDO::FETCH_ASSOC);
while($Row = $Stmt->fetch()) {
	$ApprovalSequence = $Row["ApprovalSequence"];
	array_push($FixedApprovalLine,[$Row["MemberID"] => $Row["MemberName"]]);
}

?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="DocumentReportID" value="<?=$DocumentReportID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="DocumentID" value="<?=$DocumentID?>">
		<input type="hidden" name="TotalItemCount" value="<?=$TotalItemCount?>">
		
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$기안_및_지출서[$LangID]?></span><span class="sub-heading" id="user_edit_position"></span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:none;">
							<li <?if ($PageTabID=="1"){?>class="uk-active"<?}?>><a href="#"><?=$직원정보[$LangID]?></a></li>
							<li <?if ($PageTabID=="2"){?>class="uk-active"<?}?>><a href="#"><?=$권한설정[$LangID]?></a></li>
						</ul>

						<?if ($DocumentReportState==1) {?>
						<div style="text-align:right;">
							<div style="width:150px;height:50px;line-height:50px;text-align:center;margin:3px 0px;background-color:#f1f1f1;border:1px solid #cccccc;border-radius:5px;cursor:pointer;" onclick="javascript:DocPrint(<?=$DocumentReportID?>);"><?=$인쇄[$LangID]?></div>
						</div>
						<?}
						
						$Feedback = array();
						$MemberName = array();
						$DocumentReportMemberID = array();
						$DocumentPermited = false;    // 품의서를 승인한 사람이 있는지 체크해서 있으면 true를 넣어준다.

						//결재해야 할 사람 수와 이미 결재완료한 사람 수가 일치하면 메시지를 '승인'으로 다르면 '진행중'으로 표시
						if (compareApprovalMemberCount($DocumentReportID)) $ApporovalMessage = $승인[$LangID];
						else $ApporovalMessage = $승인[$LangID];

						?>

						<div class="draft_wrap">
							<div class="draft_top uk-grid-match uk-grid-large row" uk-grid>
								<div class="col-md-4 col-sm-12">
									<h3 class="uk-heading-small"><?=$에듀비전_기안및지출서[$LangID]?></h3>
								</div>
								<div class="col-md-6 col-sm-12">
									<table class="draft_approval">
										<col width="5%">
										<colgroup span="6" width="15%"></colgroup>
										<tr style="height:60px;">
											<th rowspan="2"><?=$결_재[$LangID]?></th>
											<? 
											$countOfApprovalLine = 0;
											for ($tdCount=0;$tdCount<(6-count($FixedApprovalLine));$tdCount++) { ?>

												<td>
													<?
													${"StrDocumentReportMemberState".$tdCount} = "-";
													if ($DocumentReportState==1) {
														
														$Sql3 = "SELECT A.*, B.MemberName from DocumentReportMembers A 
																	inner join Members B on A.MemberID=B.MemberID 
																	where A.DocumentReportID = $DocumentReportID 
																	and A.DocumentReportMemberOrder = $tdCount";
														$Stmt3 = $DbConn->prepare($Sql3);
														$Stmt3->execute();
														$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
														$Row3 = $Stmt3->fetch();
														$Stmt3 = null;

														$MemberName[$tdCount] = $Row3["MemberName"];
														$Feedback[$tdCount] = $Row3["Feedback"];
														$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
														$DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);
														if ($DocumentReportMemberState==0){
															${"StrDocumentReportMemberState".$tdCount} = "-";
														}else if ($DocumentReportMemberState==1){
															$DocumentPermited = true;
															${"StrDocumentReportMemberState".$tdCount} = $DocumentReportMemberModiDateTime . "<br>".$ApporovalMessage;
														}else if ($DocumentReportMemberState==2){
															${"StrDocumentReportMemberState".$tdCount} = $DocumentReportMemberModiDateTime . "<br>".$반려[$LangID];
														}
														echo ("<input type='hidden' id='DocumentReportMemberID".$tdCount."' name='DocumentReportMemberID".$tdCount."' value='".$Row3["MemberID"]."'>");
														echo ($MemberName[$tdCount]); 
													
													} else {
													?>
														<select class="uk-select" id="category<?=$tdCount?>" onchange="javascript:categoryChange(this,'DocumentReportMemberID<?=$tdCount?>',0)">
															<option><?=$부서선택[$LangID]?></option>
															<?		
																foreach($departments as $key => $value){
																	echo "<option value='{$key}'>{$value}</option>";
																}
															?>
														</select>
														<select class="uk-select" id="DocumentReportMemberID<?=$tdCount?>" name="DocumentReportMemberID<?=$tdCount?>">
															<option value=""><?=$직원선택[$LangID]?></option>
														</select>
														<?
															if ($DocumentReportState==2){
																$Sql3 = "SELECT A.MemberID, C.StaffManagement from DocumentReportMembers A 
																			LEFT JOIN Members B ON A.MemberID = B.MemberID
																			LEFT JOIN Staffs C ON B.StaffID = C.StaffID
																			where A.DocumentReportID=$DocumentReportID and DocumentReportMemberOrder= $tdCount ";
																$Stmt3 = $DbConn->prepare($Sql3);
																$Stmt3->execute();
																$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
																$Row3 = $Stmt3->fetch();
																$Stmt3 = null;
																array_push($DocumentReportMemberID,[$Row3["MemberID"] => $Row3["StaffManagement"]]);
															}else{
																array_push($DocumentReportMemberID,[0 => NULL]);
															}

														?>
													<?
													}
													?>
												</td>
												
											<? 
												$countOfApprovalLine++;
											} 
											
											for ($tdCount=$countOfApprovalLine;$tdCount<6;$tdCount++) { ?>	
											<td>
												<?
												${"StrDocumentReportMemberState".$tdCount} = "-";
												if ($DocumentReportState==1) {
													$Sql3 = "SELECT A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID 
																where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=$tdCount";
													$Stmt3 = $DbConn->prepare($Sql3);
													$Stmt3->execute();
													$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
													$Row3 = $Stmt3->fetch();
													$Stmt3 = null;

													$MemberName[$tdCount] = $Row3["MemberName"];
													$Feedback[$tdCount] = $Row3["Feedback"];
													$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
													$DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);
													if ($DocumentReportMemberState==0){
														${"StrDocumentReportMemberState".$tdCount} = "-";
													}else if ($DocumentReportMemberState==1){
														$DocumentPermited = true;
														${"StrDocumentReportMemberState".$tdCount} = $DocumentReportMemberModiDateTime . "<br>".$ApporovalMessage;
													}else if ($DocumentReportMemberState==2){
														${"StrDocumentReportMemberState".$tdCount} = $DocumentReportMemberModiDateTime . "<br>".$반려[$LangID];
													}
													?>
													<input type='hidden' id='DocumentReportMemberID4' name='DocumentReportMemberID<?=${"StrDocumentReportMemberState".$tdCount}?>' value='<?=key($FixedApprovalLine[$tdCount-$countOfApprovalLine])?>'>
													<?
													echo ($MemberName[$tdCount]); 
												}else{
												?>
													<select class="uk-select" id="DocumentReportMemberID<?=$tdCount?>" name="DocumentReportMemberID<?=$tdCount?>">
														<option value="<?=key($FixedApprovalLine[$tdCount-$countOfApprovalLine])?>"><?=$FixedApprovalLine[$tdCount-$countOfApprovalLine][key($FixedApprovalLine[$tdCount-$countOfApprovalLine])]?></option>
													</select>
												<?
												}
												?>
											</td>
											<?php } ?>

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
								<?php 
									for ($i=0; $i<=5; $i++) {
										if (strstr(${"StrDocumentReportMemberState".$i},'반려')){
											
								?>			
								<div class="col-md-2 col-sm-12">
									<div class="box">
										<h6 style="text-align:center;color:darkslategrey"><?=$MemberName[$i]?> 님의 반려 사유</h6>
										<?=$Feedback[$i]?>
									</div>
								</div>	
								<?			
										}
									}
								?>
							</div>
							<table class="draft_table_1">
								<col width="13%">
								<col width="22%">
								<col width="">
								<col width="22%">
								<col width="13%">
								<col width="22%">
								<tr>
									<th class="draft_cell_green"><?=$발의[$LangID]?></th>
									<td><?=$StrDocumentReportRegDateTime?></td>
									<td>인</td>
									<th><?=$증빙자료[$LangID]?></th>
									<td  colspan=2 align=center>
										<div id="multiple">
											<div type="button" class="btn btn-success fileup-btn" style="display:block;margin-left:auto;margin-right:auto;margin-bottom:5px">
												<?=$올릴파일[$LangID]?>
												
												<input type="file" id="upload-2" multiple accept=".jpg, .jpeg, .png, .gif, .doc, .docx, .xls, .xlsx, .hwp, .pdf, .psd, .txt, .ppt, .zip">
												
											</div>
											<!--
											<a class="control-button btn btn-link" style="display: none" href="javascript:$.fileup('upload-2', 'upload', '*')">Upload all</a>
											<a class="control-button btn btn-link" style="display: none" href="javascript:$.fileup('upload-2', 'remove', '*')">Remove all</a>
											-->
											<div id="upload-2-queue" class="queue" style="display:inline-block"></div>
										</div>
										<input type="hidden" id="FileRealName" name="FileRealName" value="<?=$FileRealName?>" class="draft_input">
										<input type="hidden" id="FileName" name="FileName" value="<?=$FileName?>" class="draft_input">
										<!--	
										<input type="input" id="FileName_" name="FileName_" value="<?=$FileRealName?>" class="draft_input" readonly>
										<div style="text-align:center;margin:3px 0px;background-color:#f1f1f1;border:1px solid #cccccc;border-radius:5px;cursor:pointer;" onclick="javascript:PopupFileUpForm('RegForm.FileName','RegForm.FileRealName','RegForm.FileName_','../uploads/document_files');">업로드</div>
										<input type="hidden" id="FileName" name="FileName" value="<?=$FileName?>" class="draft_input">
										<input type="hidden" id="FileRealName" name="FileRealName" value="<?=$FileRealName?>" class="draft_input">
										-->
									</td>
								</tr>
								<tr>
									<th><?=$결재[$LangID]?></th>
									<td><?=$StrDocumentReportMemberRegDateTime?></td>
									<td>인</td>
									<th><?=$계정과목[$LangID]?></th>
									<td colspan=2><input type="input" id="AccCode" name="AccCode" value="<?=$AccCode?>" class="draft_input" <?=$DocumentPermited?"readonly":""?>></td>
								</tr>
								<tr id="RealPayDate" <?=($My_OrganLevelID == 18 || $My_OrganLevelID == 19)?"":"style=\"display:none;\""?>>
									<th>실 지출일</th>
									<td><input type="input" id="PayDate" name="PayDate" value="<?=$PayDate?>" style="text-align:center;" class="draft_input" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}"></td>
									<td>인</td>
									<td></td>
									<td colspan="2"></td>
								</tr>
								<tr>
									<th class="draft_cell_green"><?=$제목[$LangID]?></th>
									<td colspan="5"><input type="input" id="DocumentReportName" name="DocumentReportName" value="<?=$DocumentReportName?>" class="draft_input"  <?=$DocumentPermited?"readonly":""?>></td>
								</tr>
								<tr>
									<th class="draft_cell_yellow"><?=$기안내용[$LangID]?></th>
									<td colspan="5" style="text-align:left;"><textarea id="DocumentReportContent" name="DocumentReportContent" class="draft_textarea"  <?=$DocumentPermited?"readonly":""?>><?=$DocumentReportContent?></textarea></td>
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
									<th colspan="<?=$IsPhTeacher?"7":"6"?>" class="draft_table_caption"><?=$지출내역[$LangID]?></th>
								</tr>
								<tr>
									<th><?=$적요[$LangID]?></th>
									<th><?=$수량[$LangID]?></th>
									<th><?=$단가[$LangID]?></th>
									<th><?=$공급가[$LangID]?></th>
									<th><?=$부가세[$LangID]?></th>
									<?=$IsPhTeacher?"<th>Korean Money<br>(Won)</th>":""?>	
									<th><?=$비고[$LangID]?></th>
								</tr>

								<?
								
								$Sql3 = "SELECT A.* 
										FROM DocumentReportDetails A 
										where 
											A.DocumentReportDetailState=1 
											and A.DocumentReportID = :DocumentReportID 
										order by A.DocumentReportDetailOrder asc";
								$Stmt3 = $DbConn->prepare($Sql3);
								// 다른 문서를 복사해서 새로 작성하는 거라면 이전 문서의 아이디로 쿼리한다.
								if (!$CopyMode)	$Stmt3->bindParam(':DocumentReportID', $DocumentReportID);
									else $Stmt3->bindParam(':DocumentReportID', $CopyDocumentReportID);
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
								?>
								<tr>
									<td><input type="input" id="DocumentReportDetailName_<?=$ItemCount?>" name="DocumentReportDetailName_<?=$ItemCount?>" value="<?=$DocumentReportDetailName?>" class="draft_input"  <?=$DocumentPermited?"readonly":""?>></td>
									<td><input type="input" id="DocumentReportDetailCount_<?=$ItemCount?>" name="DocumentReportDetailCount_<?=$ItemCount?>" value="<?=number_format($DocumentReportDetailCount)?>" onkeyup="ChItem(2, <?=$ItemCount?>)" onfocus="this.select();" class="draft_input allownumericwithoutdecimal"  <?=$DocumentPermited?"readonly":""?>></td>
									<td><input type="input" id="DocumentReportDetailUnitPrice_<?=$ItemCount?>" name="DocumentReportDetailUnitPrice_<?=$ItemCount?>" value="<?=number_format($DocumentReportDetailUnitPrice)?>" onkeyup="ChItem(2, <?=$ItemCount?>)" onfocus="this.select();" class="draft_input"  <?=$DocumentPermited?"readonly":""?>></td>
									<td><input type="input" id="DocumentReportDetailPrice_<?=$ItemCount?>" name="DocumentReportDetailPrice_<?=$ItemCount?>" value="<?=number_format($DocumentReportDetailPrice)?>" onkeyup="ChItem(1, <?=$ItemCount?>)" onfocus="this.select();" class="draft_input allownumericwithoutdecimal"  <?=$DocumentPermited?"readonly":""?>></td>
									<td><input type="input" id="DocumentReportDetailVat_<?=$ItemCount?>" name="DocumentReportDetailVat_<?=$ItemCount?>" value="<?=number_format($DocumentReportDetailVat)?>" onkeyup="ChItem(1, <?=$ItemCount?>)" onfocus="this.select();" class="draft_input allownumericwithoutdecimal"  <?=$DocumentPermited?"readonly":""?>></td>
									<?=$IsPhTeacher?"<td><input type='input' id='DocumentReportDetailWon_".$ItemCount."' name='DocumentReportDetailWon_".$ItemCount."' value='".number_format(($DocumentReportDetailPrice+$DocumentReportDetailVat)*$Currency)."' onkeyup='ChItem(1, <?=$ItemCount?>)' onfocus='this.select();' class='draft_input allownumericwithoutdecimal' readonly></td>":""?>	
									<td><input type="input" id="DocumentReportDetailMemo_<?=$ItemCount?>" name="DocumentReportDetailMemo_<?=$ItemCount?>" value="<?=$DocumentReportDetailMemo?>" class="draft_input"  <?=$DocumentPermited?"readonly":""?>></td>
								</tr>
								<?
									$ItemCount ++;
								}
								$Stmt3 = null;

								$StartItemCount = $ItemCount;
								for ($ItemCount=$StartItemCount; $ItemCount<=$TotalItemCount; $ItemCount++){
								?>
								<tr>
									<td><input type="input" id="DocumentReportDetailName_<?=$ItemCount?>" name="DocumentReportDetailName_<?=$ItemCount?>" value="" class="draft_input" <?=$DocumentPermited?"readonly":""?>></td>
									<td><input type="input" id="DocumentReportDetailCount_<?=$ItemCount?>" name="DocumentReportDetailCount_<?=$ItemCount?>" value="0" onkeyup="ChItem(2, <?=$ItemCount?>)" onfocus="this.select();" class="draft_input allownumericwithoutdecimal" <?=$DocumentPermited?"readonly":""?>></td>
									<td><input type="input" id="DocumentReportDetailUnitPrice_<?=$ItemCount?>" name="DocumentReportDetailUnitPrice_<?=$ItemCount?>" value="0" onkeyup="ChItem(2, <?=$ItemCount?>)" onfocus="this.select();" class="draft_input" <?=$DocumentPermited?"readonly":""?>></td>
									<td><input type="input" id="DocumentReportDetailPrice_<?=$ItemCount?>" name="DocumentReportDetailPrice_<?=$ItemCount?>" value="0" onkeyup="ChItem(1, <?=$ItemCount?>)" onfocus="this.select();" class="draft_input allownumericwithoutdecimal" <?=$DocumentPermited?"readonly":""?>></td>
									<td><input type="input" id="DocumentReportDetailVat_<?=$ItemCount?>" name="DocumentReportDetailVat_<?=$ItemCount?>" value="0" onkeyup="ChItem(1, <?=$ItemCount?>)" onfocus="this.select();" class="draft_input allownumericwithoutdecimal" <?=$DocumentPermited?"readonly":""?>></td>
									<?=$IsPhTeacher?"<td><input type='input' id='DocumentReportDetailWon_".$ItemCount."' name='DocumentReportDetailWon_".$ItemCount."' value='0' onkeyup='ChItem(1, <?=$ItemCount?>)' onfocus='this.select();' class='draft_input allownumericwithoutdecimal' readonly></td>":""?>	
									<td><input type="input" id="DocumentReportDetailMemo_<?=$ItemCount?>" name="DocumentReportDetailMemo_<?=$ItemCount?>" value="" class="draft_input" <?=$DocumentPermited?"readonly":""?>></td>
								</tr>
								<?
								}
								?>



								<tr>
									<th><?=$합계[$LangID]?></th>
									<td></td>
									<td></td>
									<td id="DocumentReportDetailPriceSum"><?=number_format($DocumentReportDetailPriceSum,0)?></td>
									<td></td>
									<?=$IsPhTeacher?"<td></td>":""?>
									<td></td>
								</tr>  
								<tr>
									<th class="draft_cell_green total"><?=$총결제금액[$LangID]?></th>
									<td colspan="<?=$IsPhTeacher?"6":"5"?>"><b id="DocumentReportDetailPriceTotSum"><?=number_format($DocumentReportDetailPriceTotSum,0)?></b></td>
								</tr>          
							</table>

							<script>
							<?
							//필리핀 강사인 경우 부가세를 12%로 적용한다.
							if ($IsPhTeacher) $vat = 0.12;
								else $vat = 0.1;
							
							?>	
							var TotalItemCount = <?=$TotalItemCount?>;

							function comma(str) {
								str = String(str);
								return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
							}

							function uncomma(str) {
								str = String(str);
								return str.replace(/[^\d]+/g, '');
							}

							function ChItem(CalType, ItemNum){

								if (CalType==2){
																	
									DocumentReportDetailCount = parseInt(uncomma(document.getElementById("DocumentReportDetailCount_"+ItemNum).value));
									DocumentReportDetailUnitPrice = parseInt(uncomma(document.getElementById("DocumentReportDetailUnitPrice_"+ItemNum).value));

									if (isNaN(DocumentReportDetailCount) || DocumentReportDetailCount==''){
										DocumentReportDetailCount = 0;
										document.getElementById("DocumentReportDetailCount_"+ItemNum).value = 0;
									}

									if (isNaN(DocumentReportDetailUnitPrice) || DocumentReportDetailUnitPrice==''){
										DocumentReportDetailUnitPrice = 0;
										document.getElementById("DocumentReportDetailUnitPrice_"+ItemNum).value = 0;
									}

									DocumentReportDetailPrice = DocumentReportDetailCount * DocumentReportDetailUnitPrice;
									DocumentReportDetailVat = DocumentReportDetailPrice * <?=$vat?>;
									DocumentReportDetailVat = Math.round(DocumentReportDetailVat);

									//console.log(DocumentReportDetailUnitPrice.format());

									document.getElementById("DocumentReportDetailUnitPrice_"+ItemNum).value = comma(DocumentReportDetailUnitPrice);
									document.getElementById("DocumentReportDetailPrice_"+ItemNum).value = comma(DocumentReportDetailPrice);
									document.getElementById("DocumentReportDetailVat_"+ItemNum).value = comma(DocumentReportDetailVat);

									<? if ($IsPhTeacher) { ?>
									DocumentReportDetailWon = Math.round((DocumentReportDetailPrice + DocumentReportDetailVat) * <?=$Currency?>);
									document.getElementById("DocumentReportDetailWon_"+ItemNum).value = comma(DocumentReportDetailWon);
									<? } ?>
									
								}
								
								DocumentReportDetailPriceSum = 0;
								DocumentReportDetailPriceTotSum = 0;
								for (ii=1; ii<=TotalItemCount; ii++){


									DocumentReportDetailPrice = parseInt(uncomma(document.getElementById("DocumentReportDetailPrice_"+ii).value));
									DocumentReportDetailVat = parseInt(uncomma(document.getElementById("DocumentReportDetailVat_"+ii).value));


									if (isNaN(DocumentReportDetailPrice) || DocumentReportDetailPrice==''){
										DocumentReportDetailPrice = 0;
										document.getElementById("DocumentReportDetailPrice_"+ii).value = 0;
									}

									if (isNaN(DocumentReportDetailVat) || DocumentReportDetailVat==''){
										DocumentReportDetailVat = 0;
										document.getElementById("DocumentReportDetailVat_"+ii).value = 0;
									}
									
									//document.getElementById("DocumentReportDetailPrice_"+ii).value = DocumentReportDetailPrice.format();
									//document.getElementById("DocumentReportDetailVat_"+ii).value = DocumentReportDetailVat.format();

									DocumentReportDetailPriceSum = parseInt(DocumentReportDetailPriceSum) + parseInt(DocumentReportDetailPrice);
									DocumentReportDetailPriceTotSum = parseInt(DocumentReportDetailPriceTotSum) + parseInt(DocumentReportDetailPrice) + parseInt(DocumentReportDetailVat);
								}

								DocumentReportDetailPriceSum = Math.round(DocumentReportDetailPriceSum);
								DocumentReportDetailPriceTotSum = Math.round(DocumentReportDetailPriceTotSum);

								document.getElementById("DocumentReportDetailPriceSum").innerHTML = comma(DocumentReportDetailPriceSum);
								document.getElementById("DocumentReportDetailPriceTotSum").innerHTML = comma(DocumentReportDetailPriceTotSum);
							}


							// 숫자 타입에서 쓸 수 있도록 format() 함수 추가
							Number.prototype.format = function(){
								if(this==0) return 0;
							 
								var reg = /(^[+-]?\d+)(\d{3})/;
								var n = (this + '');
							 
								while (reg.test(n)) n = n.replace(reg, '$1' + ',' + '$2');
							 
								return n;
							};
							 
							// 문자열 타입에서 쓸 수 있도록 format() 함수 추가
							String.prototype.format = function(){
								var num = parseFloat(this);
								if( isNaN(num) ) return "0";
							 
								return num.format();
							};

							</script>

							<table class="draft_table_3">
								<col width="25%">
								<col width="">
								<tr>
									<th>1. <?=$거래처명[$LangID]?></th>
									<td><input type="input" id="OrganName" name="OrganName" value="<?=$OrganName?>" class="draft_input"  <?=$DocumentPermited?"readonly":""?>></td>
								</tr>
								<tr>
									<th>2. <?=$거래처_연락처[$LangID]?></th>
									<td><input type="input" id="OrganPhone" name="OrganPhone" value="<?=$OrganPhone?>" class="draft_input" <?=$DocumentPermited?"readonly":""?>></td>
								</tr>
								<tr>
									<th>3. <?=$거래처_담당자[$LangID]?></th>
									<td><input type="input" id="OrganManagerName" name="OrganManagerName" value="<?=$OrganManagerName?>" class="draft_input" <?=$DocumentPermited?"readonly":""?>></td>
								</tr>
								<tr>
									<th>4. <?=$결제방법[$LangID]?></th>
									<td><input type="input" id="PayMethod" name="PayMethod" value="<?=$PayMethod?>" class="draft_input" <?=$DocumentPermited?"readonly":""?>></td>
								</tr>
								<tr>
									<th>5. <?=$결제_요청일[$LangID]?></th>
									<td><input type="input" id="RequestPayDate" name="RequestPayDate" value="<?=$RequestPayDate?>" class="draft_input"  <?=$DocumentPermited?"readonly":"data-uk-datepicker=\"{format:'YYYY-MM-DD', weekstart:0}\" "?>></td>
								</tr>
								<tr>
									<th>6. <?=$비고[$LangID]?></th>
									<td><input type="input" id="PayMemo" name="PayMemo" value="<?=$PayMemo?>" class="draft_input"  <?=$DocumentPermited?"readonly":""?>></td>
								</tr>
							</table>
							<div class="draft_bottom">
								<?=$영수요청[$LangID]?><br>
								<?=$StrDocumentReportRegDateTime2?>
								<div class="draft_sign_wrap"><?=$작성자[$LangID]?> : <?=$_LINK_ADMIN_NAME_?> <span class="draft_sign">(인)</span></div>
							</div>
						</div>
                        
                        
					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10" >
				<div class="md-card">
					<div class="md-card-content">
						<h3 class="heading_c uk-margin-medium-bottom"><?=$저장설정[$LangID]?></h3>

						<div class="uk-form-row">
							<input type="hidden" name="DocumentReportState" id="DocumentReportState" value="<?=$DocumentReportState?>">
							<? if ($DocumentReportState!=1) {?>
							<a type="button" href="javascript:FormSubmit(2);" class="md-btn md-btn-worning"><?=$저장하기[$LangID]?></a>
							<?}?>
							<? if ($DocumentReportState==1) {?>
							<a type="button" href="javascript:FormSubmit(1);" class="md-btn md-btn-worning"><?=$수정하기[$LangID]?></a>
							<?}?>
							<? if ($DocumentReportState!=1) {?>
							<a type="button" href="javascript:FormSubmit(1);" class="md-btn md-btn-primary"><?=$제출하기[$LangID]?></a>
							<?}?>
							<? if ($DocumentReportState==1 || $DocumentReportState==2) {?>
							<a type="button" href="javascript:FormSubmit(0);" class="md-btn md-btn-danger"><?=$삭제하기[$LangID]?></a>
							<?}?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="DocumentPermited" value="<?=$DocumentPermited?"true":"false"?>">
		</form>

	</div>
</div>

				


<?
include_once('./inc_category_change.php');

include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->

<!-- 멀티 파일업로드 JQuery -->
<link href="/js/fileuploadjs/fileup.min.css" rel="stylesheet">
<script src="/js/fileuploadjs/fileup.min.js"></script>


<script>
	var fileIndex=0;
	var fileNumberArr = [];   // 서버에 업로드된 파일 순서와 큐에 들어가 있는 파일 순서가 달라서 맞추기 위함
	var fileNameArr = [];     // 서버에 업로드된 파일 이름의 배열
	var originNameArr = [];   // 원래 파일 이름의 배열
	var jsonData;

	// 이전에 저장되어 있는 파일들의 정보를 가져와서 JSON 형식으로 변환하여 파일 업로더에 넘겨준다.
	if (document.RegForm.FileName.value != '') {
		fileNameArr = document.RegForm.FileName.value.split(',');
		originNameArr = document.RegForm.FileRealName.value.split(',');
		for (var i=0; i<fileNameArr.length; i++) {
			fileNumberArr.push(i);
		}

		// 리스트 생성
        var objectList = new Array() ;
         
        for(var i=0; i<fileNameArr.length; i++){
            // 객체 생성
            var data = new Object() ;
            data.id = i; 
            data.name = originNameArr[i] ;
            data.size = 1024 ;
			data.downloadUrl = "../uploads/document_files/" + fileNameArr[i] ;
             
             
            // 리스트에 생성된 객체 삽입
            objectList.push(data) ;
        }
         
        // String 형태로 변환
        jsonData = JSON.stringify(objectList) ;
         
        //alert(jsonData) ;
	}
	
	

	$.fileup({
		url: 'jquery_file_upload.php',
		inputID: 'upload-2',
		dropzoneID: 'upload-2-dropzone',
		queueID: 'upload-2-queue',
		autostart: true,
		files: objectList,
		onSelect: function(file) {
			$('#multiple .control-button').show();
		},
		onRemove: function(file, total) {
			// 사진 삭제버튼 누를 시 ajax로 서버에 있는 파일도 삭제하고 배열에서 해당 파일들 제거
			$.ajax({
				type: 'POST',
				url: 'jquery_file_delete.php',
				data: { filename : fileNameArr[fileNumberArr.indexOf(file.file_number)] },
				//success: (log) => {alert('실패'+file.file_number)},
				//error: (log) => {alert('실패'+log)}
			});
			fileNameArr.splice(fileNumberArr.indexOf(file.file_number),1);
			originNameArr.splice(fileNumberArr.indexOf(file.file_number),1);
			fileNumberArr.splice(fileNumberArr.indexOf(file.file_number),1);
			if (file === '*' || total === 1) {
				$('#multiple .control-button').hide();
			}
		},
		onSuccess: function(response, file_number, file) {
			originNameArr.push(file.name);
			var imsi = response.split(',');
			fileNameArr.push(imsi[1]);
			fileNumberArr.push(file_number);
			//$.growl.notice({ title: "Upload success!", message: response+file.name });
		},
		onError: function(event, file, file_number) {
			//$.growl.error({ message: "Upload error!" });
		},
		templateFile: "<div id='fileup-[INPUT_ID]-[FILE_NUM]' class='fileup-file [TYPE]'>\
			<div class='fileup-preview'>\
				<img src='[PREVIEW_SRC]' alt='[NAME]'/>\
			</div>\
			<div class='fileup-container'>\
				<div class='fileup-description'>\
					<span class='fileup-name'>[NAME]</span>\
				</div>\
				<div class='fileup-controls'>\
					<span class='fileup-remove' onclick=\"$.fileup('[INPUT_ID]', 'remove', '[FILE_NUM]');\" title='[REMOVE]'></span>\
				</div>\
				<div class='fileup-result'></div>\
				<div class='fileup-progress'>\
					<div class='fileup-progress-bar'></div>\
				</div>\
			</div>\
			<div class='fileup-clear'></div>\
		</div>"
	});
</script>


<script language="javascript">
function DocPrint(DocumentReportID){
	window.open("./my_document_draft_print.php?DocumentReportID="+DocumentReportID, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=100,width=1000,height=900");
}

function PopupFileUpForm(FormName1,FormName2,FormName3,UpPath){
	openurl = "./popup_doc_file_upload_form.php?FormName1="+FormName1+"&FormName2="+FormName2+"&FormName3="+FormName3+"&UpPath="+UpPath;
	$.colorbox({	
		href:openurl
		,width:"500" 
		,height:"300"
		,title:""
		,iframe:true 
		,scrolling:false
		//,onClosed:function(){location.reload(true);}   
	}); 
}


function FormSubmit(DocumentReportState){
	
	document.RegForm.DocumentReportState.value = DocumentReportState;

	obj = document.RegForm.DocumentReportName;
	if (obj.value==""){
		UIkit.modal.alert("제목을 입력하세요.");
		obj.focus();
		return;
	}
	// 자바스크립트 변수를 문자열로 조인하고 그걸 Input 태그에 입력해서 전송한다.
	var fileNameJoin = fileNameArr.join();
	var originNameJoin = originNameArr.join();

	document.RegForm.FileName.value = fileNameJoin;       // 서버에 올라간 파일 이름
	document.RegForm.FileRealName.value = originNameJoin; // 원래 파일 이름

	var submitMsg;
	if (DocumentReportState==1) {
		submitMsg = "제출하시겠습니까?";
	} else if (DocumentReportState==0) {
		submitMsg = "삭제하시겠습니까?";
	}

	// 결재라인 중복확인 루틴 결재라인을 배열과 SET으로 만들어서 길이를 비교하여 확인한다.
	const memberArr = [$('#DocumentReportMemberID0 option:selected').val(),$('#DocumentReportMemberID1 option:selected').val(),$('#DocumentReportMemberID2 option:selected').val(),$('#DocumentReportMemberID3 option:selected').val(),$('#DocumentReportMemberID4 option:selected').val(),$('#DocumentReportMemberID5 option:selected').val()];

	var filterArr = memberArr.filter(function(data) {
		return data != '';
	});


	const memberSet = new Set(filterArr);
	const isDuplicate = memberSet.size < filterArr.length;

	<? if ($DocumentReportState!=1) {?>

	if (isDuplicate && (DocumentReportState==1 || DocumentReportState==2)) {
		UIkit.modal.alert("결재 라인이 중복됩니다. 결재 라인을 수정해 주세요.");
		$('#DocumentReportMemberID0').focus();
		return;
	}

	<?}?>


	if (DocumentReportState==2){//저장
		document.RegForm.action = "my_document_draft_action.php";
		document.RegForm.submit();
	} else {//제출

		UIkit.modal.confirm(
			submitMsg, 
			function(){ 
				document.RegForm.action = "my_document_draft_action.php";
				document.RegForm.submit();
			}
		);
	}
}

</script>


<!-- ===========================================   froala_editor   =========================================== -->
<style>
#_____DocumentReportContent {
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

<script>
(function () {
  const editorInstance = new FroalaEditor('#_____DocumentReportContent', {
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
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>