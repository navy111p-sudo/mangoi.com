<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./includes/board_config.php');
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
 <!-- dropify -->
<link rel="stylesheet" href="assets/skins/dropify/css/dropify.css">
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
if ($BoardCode=="notice"){
	$MainMenuID = 24;
	$SubMenuID = 2403;
}else if ($BoardCode=="exchange"){
	$MainMenuID = 24;
	$SubMenuID = 2404;
}else if ($BoardCode=="event"){
	$MainMenuID = 24;
	$SubMenuID = 2405;
}else if ($BoardCode=="qna"){
	$MainMenuID = 24;
	$SubMenuID = 2406;
}else if ($BoardCode=="faq"){
	$MainMenuID = 24;
	$SubMenuID = 2407;
}else if ($BoardCode=="reference"){
	$MainMenuID = 28;
	$SubMenuID = 2808;
}else if ($BoardCode=="branch"){
	$MainMenuID = 28;
	$SubMenuID = 28081;
}else if ($BoardCode=="center"){
	$MainMenuID = 28;
	$SubMenuID = 28082;
}else if ($BoardCode=="etc"){
	$MainMenuID = 28;
	$SubMenuID = 28083;
}else if ($BoardCode=="hrfile"){
	$MainMenuID = 88;
	$SubMenuID = 8841;
}


include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>


<?php

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BoardContentID = isset($_REQUEST["BoardContentID"]) ? $_REQUEST["BoardContentID"] : "";
$GetBoardContentReplyID = isset($_REQUEST["GetBoardContentReplyID"]) ? $_REQUEST["GetBoardContentReplyID"] : "";


$Sql = "select BoardID from Boards where BoardCode=:BoardCode ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardCode', $BoardCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BoardID = $Row["BoardID"];


if ($BoardContentID!=""){
	$Sql = "select *, DATE_FORMAT(EventStartDate,'%Y-%m-%d') as EventStartDate2 , DATE_FORMAT(EventEndDate,'%Y-%m-%d') as EventEndDate2 from BoardContents where BoardContentID=:BoardContentID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardContentID', $BoardContentID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BoardCategoryID = $Row["BoardCategoryID"];
	$BoardContentMemberID = $Row["BoardContentMemberID"];
	$BoardContentWriterName = $Row["BoardContentWriterName"];
	$BoardContentWriterPW = $Row["BoardContentWriterPW"];
	$BoardContentNotice = $Row["BoardContentNotice"];
	$BoardContentSubject = $Row["BoardContentSubject"];
	$BoardContent = $Row["BoardContent"];
	$BoardContentTag = $Row["BoardContentTag"];
	$BoardContentSecret = $Row["BoardContentSecret"];
	$BoardContentState = $Row["BoardContentState"];
	$BoardContentRegDateTime = $Row["BoardContentRegDateTime"];
	$BoardContentReplyID = $Row["BoardContentReplyID"];
	$BoardContentReplyOrder = $Row["BoardContentReplyOrder"];
	$BoardContentReplyDepth = $Row["BoardContentReplyDepth"];
	$BoardContentViewCount = $Row["BoardContentViewCount"];

	$BoardContentVideoCode = $Row["BoardContentVideoCode"];

	$NewData = "0";

	$BoardContentWriterName = str_replace('"', "&#34;", $BoardContentWriterName);
	$BoardContentSubject = str_replace('"', "&#34;", $BoardContentSubject);

	$EventStartDate = $Row["EventStartDate2"];
	$EventEndDate = $Row["EventEndDate2"];


}else{
	$BoardContentMemberID = $_LINK_ADMIN_ID_;
	$BoardContentWriterName = $_LINK_ADMIN_NAME_;
	$BoardContentReplyID = "";
	$BoardContentReplyOrder = "0";
	$BoardContentReplyDepth = "0";

	$BoardContentSubject = "";
	$BoardContentWriterPW = "";
	$BoardContent = "";
	$BoardContentNotice = false;
	$BoardContentSecret = false;

	$NewData = "1";

	$EventStartDate = date("Y-m-d");
	$EventEndDate = date("Y-m-d");
}

if ($GetBoardContentReplyID!=""){

	$BoardContentMemberID = $_MEMBER_ID_;
	$BoardContentWriterName = $_MEMBER_NAME_;

	$BoardContentID = "";
	$NewData = "1";
	$BoardContentSubject = "[답변]".$BoardContentSubject;
	$BoardContent = "<br><br><br>======================================================<br><br>".$BoardContent;
}

?>


<div id="page_content">
	<div id="page_content_inner">


		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="BoardCode" value="<?=$BoardCode?>">
		<input type="hidden" name="BoardContentID" value="<?=$BoardContentID?>">
		<input type="hidden" name="BoardContentMemberID" value="<?=$BoardContentMemberID?>">

		<input type="hidden" name="BoardContentReplyID" value="<?=$BoardContentReplyID?>">
		<input type="hidden" name="BoardContentReplyOrder" value="<?=$BoardContentReplyOrder?>">
		<input type="hidden" name="BoardContentReplyDepth" value="<?=$BoardContentReplyDepth?>">

		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="NewData" value="<?=$NewData?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<!--
						<div class="user_heading_avatar fileinput fileinput-new" data-provides="fileinput">
							<div class="fileinput-new thumbnail">
								<img src="assets/img/avatars/user.png" alt="user avatar"/>
							</div>
							<div class="fileinput-preview fileinput-exists thumbnail"></div>
							<div class="user_avatar_controls">
								<span class="btn-file">
									<span class="fileinput-new"><i class="material-icons">&#xE2C6;</i></span>
									<span class="fileinput-exists"><i class="material-icons">&#xE86A;</i></span>
									<input type="file" name="user_edit_avatar_control" id="user_edit_avatar_control">
								</span>
								<a href="#" class="btn-file fileinput-exists" data-dismiss="fileinput"><i class="material-icons">&#xE5CD;</i></a>
							</div>
						</div>
						-->
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$BoardTitle?></span><span class="sub-heading" id="user_edit_position"></span></h2>
						</div>
						<!--
						<div class="md-fab-wrapper">
							<div class="md-fab md-fab-toolbar md-fab-small md-fab-accent">
								<i class="material-icons">&#xE8BE;</i>
								<div class="md-fab-toolbar-actions">
									<button type="submit" id="user_edit_save" data-uk-tooltip="{cls:'uk-tooltip-small',pos:'bottom'}" title="Save"><i class="material-icons md-color-white">&#xE161;</i></button>
									<button type="submit" id="user_edit_print" data-uk-tooltip="{cls:'uk-tooltip-small',pos:'bottom'}" title="Print"><i class="material-icons md-color-white">&#xE555;</i></button>
									<button type="submit" id="user_edit_delete" data-uk-tooltip="{cls:'uk-tooltip-small',pos:'bottom'}" title="Delete"><i class="material-icons md-color-white">&#xE872;</i></button>
								</div>
							</div>
						</div>
						-->
					</div>
					<div class="user_content">

						<ul class="uk-margin" style="margin:0; padding:0;">
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c"> 
										<?=$제목[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<?php
										if ($BoardEnableCategory==1){
										?>
										<div class="uk-width-medium-2-10">

											<select id="BoardCategoryID" name="BoardCategoryID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="<?=$카테고리[$LangID]?>" style="width:100%;"/>
													<?
												$Sql2 = "select * from BoardCategories  where BoardID=:BoardID order by BoardCategoryOrder asc";
												$Stmt2 = $DbConn->prepare($Sql2);
												$Stmt2->bindParam(':BoardID', $BoardID);
												$Stmt2->execute();
												$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

												while($Row2 = $Stmt2->fetch()){
												?>
												<option value="<?=$Row2["BoardCategoryID"]?>" <?php if ($BoardCategoryID==$Row2["BoardCategoryID"]) { echo "selected"; }?>><?=$Row2["BoardCategoryName"]?></option>
												<?
												}
												$Stmt2 = null;
												?>
											</select>
										</div>
										<?
										}else{
										?>
										<input type="hidden" name="BoardCategoryID" value="0">
										<?
										}
										?>
										<div class="<?if ($BoardEnableCategory==1){?>uk-width-medium-8-10<?}else{?>uk-width-medium-10-10<?}?>">
											<label for="BoardContentSubject"><?=$제목[$LangID]?></label>
											<input type="text" id="BoardContentSubject" name="BoardContentSubject" value="<?=$BoardContentSubject?>" class="md-input label-fixed"/>
										</div>

									
									
									</div>


									<div class="uk-grid" data-uk-grid-margin>
										<div class="<?if ($BoardContentWriterPW!=""){?>uk-width-medium-4-10<?}else{?>uk-width-medium-7-10<?}?>">
											<label for="BoardContentWriterName"><?=$작성자[$LangID]?></label>
											<input type="text" id="BoardContentWriterName" name="BoardContentWriterName" value="<?=$BoardContentWriterName?>" class="md-input label-fixed"/>
										</div>
										<?if ($BoardContentWriterPW!=""){?>
										<div class="uk-width-medium-4-10">
											<label for="BoardContentWriterPW"><?=$비밀번호[$LangID]?></label>
											<input type="text" id="BoardContentWriterPW" name="BoardContentWriterPW" value="<?=$BoardContentWriterPW?>" class="md-input label-fixed"/>
										</div>
										<?}?>
										<div class="uk-width-medium-3-10 uk-input-group" style="padding-top:10px;">
											<span class="icheck-inline">
												<input type="checkbox" name="BoardContentNotice" id="BoardContentNotice" value="1" <?php if ($BoardContentNotice==1) {echo ("checked");}?> data-md-icheck/>
												<label for="BoardContentNotice" class="inline-label" style="margin-right:20px;"><?=$공지사항[$LangID]?></label>
											</span>
											<span class="icheck-inline">
												<input type="checkbox" name="BoardContentSecret" id="BoardContentSecret" value="1" <?php if ($BoardContentSecret==1) {echo ("checked");}?> data-md-icheck/>
												<label for="BoardContentSecret" class="inline-label" style="margin-right:20px;"><?=$비밀글[$LangID]?></label>
											</span>
										</div>
									</div>

									<?if ($BoardContentID!=""){?>
									<div class="uk-grid" data-uk-grid-margin>
										
										<div class="uk-width-medium-1-10">
											<label for="BoardContentViewCount"><?=$조회수_수정[$LangID]?></label>
											<input type="text" id="BoardContentViewCount" name="BoardContentViewCount" value="<?=$BoardContentViewCount?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-3-10">
											※ 입력한 숫자 부터 카운트 됩니다.
										</div>
										<div class="uk-width-medium-2-10">
											<label for="BoardContentRegDateTime"><?=$작성일_수정[$LangID]?><?=$공지사항[$LangID]?></label>
											<input type="text" id="BoardContentRegDateTime" name="BoardContentRegDateTime" value="<?=$BoardContentRegDateTime?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-4-10">
											※ 작성형식 : 2017-12-01 16:02:56 (반드시 지정한 형식으로 입력하세요.)
										</div>
										
									</div>
									<?}?>

									<?if ($BoardCode=="event"){?>
									<div class="uk-grid" data-uk-grid-margin>
										
										<div class="uk-width-medium-2-10">
											<label for="EventStartDate"><?=$이벤트_시작일[$LangID]?></label>
											<input type="text" id="EventStartDate" name="EventStartDate" value="<?=$EventStartDate?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-3-10">
											※ 이벤트 시작일을 입력하세요.
										</div>
										<div class="uk-width-medium-2-10">
											<label for="EventEndDate"><?=$이벤트_종료일[$LangID]?></label>
											<input type="text" id="EventEndDate" name="EventEndDate" value="<?=$EventEndDate?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-3-10">
											※ 이벤트 종료일을 입력하세요.
										</div>

										<script>
										$(document).ready(function() {
											$("#EventStartDate").kendoDatePicker({
												format: "yyyy-MM-dd"
											});
											$("#EventEndDate").kendoDatePicker({
												format: "yyyy-MM-dd"
											});
										});
										</script>
										
									</div>
									<?}else{?>
									<input type="hidden" id="EventStartDate" name="EventStartDate" value="<?=$EventStartDate?>"/>
									<input type="hidden" id="EventEndDate" name="EventEndDate" value="<?=$EventEndDate?>"/>
									<?}?>

									<h3 class="full_width_in_card heading_c"> 
										<?=$내용작성[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<textarea class="md-input" name="BoardContent" id="BoardContent" cols="30" rows="7"><?=$BoardContent?></textarea>
										</div>
									</div>

									<?php
									if ($BoardFileCount>0) {
									?>
									<h3 class="full_width_in_card heading_c"> 
										<?=$파일첨부[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										
										<?php
										for ($FileID=1;$FileID<=$BoardFileCount;$FileID++){
											
											$Sql = "select count(*) as BoardFileIDCount from BoardContentFiles where BoardContentID=:BoardContentID and BoardFileNumber=:FileID";
											$Stmt = $DbConn->prepare($Sql);
											$Stmt->bindParam(':BoardContentID', $BoardContentID);
											$Stmt->bindParam(':FileID', $FileID);
											$Stmt->execute();
											$Stmt->setFetchMode(PDO::FETCH_ASSOC);
											$Row = $Stmt->fetch();
											$Stmt = null;

											$BoardFileIDCount = $Row["BoardFileIDCount"];

											if ($BoardFileIDCount>0){					
												$Sql = "select BoardFileID from BoardContentFiles where BoardContentID=:BoardContentID and BoardFileNumber=:FileID";
												$Stmt = $DbConn->prepare($Sql);
												$Stmt->bindParam(':BoardContentID', $BoardContentID);
												$Stmt->bindParam(':FileID', $FileID);
												$Stmt->execute();
												$Stmt->setFetchMode(PDO::FETCH_ASSOC);
												$Row = $Stmt->fetch();
												$Stmt = null;

												$BoardFileID = $Row["BoardFileID"];
											}else{
												$BoardFileID = "";
											}
										?>
										<div class="uk-width-1-1">
											<label for="BoardContent">첨부파일 : </label>
											<input type="file" id="file" name="BoardFile<?=$FileID?>" style="width:300px;">
											<?if ($BoardFileID!=""){?>
											<input type="checkbox" name="DelBoardFile<?=$FileID?>" id="DelBoardFile<?=$FileID?>" value="1"> <label for="DelBoardFile<?=$FileID?>" data-md-icheck>삭제</label>
											<?}else{?>
											<input type="hidden" name="DelBoardFile<?=$FileID?>" id="DelBoardFile<?=$FileID?>" value="0">
											<?}?>
										</div>
										<?php
										}
										?>
									</div>
									<?
									}
									?>

								</div>



						</ul>
					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10">
				<div class="md-card">
					<div class="md-card-content">
						<!--
						<h3 class="heading_c uk-margin-medium-bottom">기타설정</h3>
						<div class="uk-form-row" style="display:none;">
							<input type="checkbox" id="BookView" name="BookView" value="1" <?php if ($BookView==1) { echo "checked";}?> data-switchery/>
							<label for="BookView" class="inline-label">활성화</label>
						</div>
						<hr class="md-hr" style="display:none;">

						<div class="uk-form-row">
							<input type="checkbox" id="BookState" name="BookState" value="1" <?php if ($BookState==1) { echo "checked";}?> data-switchery/>
							<label for="BookState" class="inline-label">사용중</label>
						</div>
						<hr class="md-hr">
						-->
						<div class="uk-form-row">
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?=$저장하기[$LangID]?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		</form>



	</div>
</div>




<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->
<!--  dropify -->
<script src="bower_components/dropify/dist/js/dropify.min.js"></script>
<!--  form file input functions -->
<script src="assets/js/pages/forms_file_input.min.js"></script>
<script>
$(function() {
	if(isHighDensity()) {
		$.getScript( "assets/js/custom/dense.min.js", function(data) {
			// enable hires images
			altair_helpers.retina_images();
		});
	}
	if(Modernizr.touch) {
		// fastClick (touch devices)
		FastClick.attach(document.body);
	}
});
$window.load(function() {
	// ie fixes
	altair_helpers.ie_fix();
});
</script>


<script language="javascript">
function FormSubmit(){

	obj = document.RegForm.BoardContentWriterName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$작성자를_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}
	
	obj = document.RegForm.BoardContentSubject;
	if (obj.value==""){
		UIkit.modal.alert("<?=$제목을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}
	
	

	document.RegForm.action = "board_action.php";
	document.RegForm.submit();
}

</script>
<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->








<!-- ===========================================   froala_editor   =========================================== -->
<style>
#BoardContent {
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
  const editorInstance = new FroalaEditor('#BoardContent', {
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


<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>







