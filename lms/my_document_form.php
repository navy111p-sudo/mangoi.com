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
$SubMenuID = 2921; 
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>

 

<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$DocumentReportID = isset($_REQUEST["DocumentReportID"]) ? $_REQUEST["DocumentReportID"] : "";
$DocumentID = isset($_REQUEST["DocumentID"]) ? $_REQUEST["DocumentID"] : "";

$PageTabID = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";
if ($PageTabID==""){
	$PageTabID = "1";
}



if ($DocumentReportID!=""){

	$Sql = "
			select 
					A.*
			from DocumentReports A 
			where A.DocumentReportID=:DocumentReportID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$DocumentID = $Row["DocumentID"];
	$DocumentReportID = $Row["DocumentReportID"];
	$DocumentReportName = $Row["DocumentReportName"];
	$DocumentReportContent = $Row["DocumentReportContent"];
	$DocumentReportState = $Row["DocumentReportState"];


}else{

	$DocumentReportID = 0;
	$DocumentReportName = "";
	$DocumentReportContent = "";
	$DocumentReportState = 0;

	if ($DocumentID==""){
		$DocumentID = 2;
	}

	$Sql = "
			select 
				A.*
			from Documents A
			where A.DocumentID=:DocumentID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentID', $DocumentID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$DocumentReportContent = $Row["DocumentContent"];

	

}




?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="DocumentReportID" value="<?=$DocumentReportID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
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
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$휴가_및_병가원[$LangID]?></span><span class="sub-heading" id="user_edit_position"><!--문서정보--></span></h2>
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
						
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:none;">
							<li <?if ($PageTabID=="1"){?>class="uk-active"<?}?>><a href="#"><?=$직원정보[$LangID]?></a></li>
							<li <?if ($PageTabID=="2"){?>class="uk-active"<?}?>><a href="#"><?=$권한설정[$LangID]?></a></li>
						</ul>


						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-10-10">
											<label for="DocumentReportName"><?=$문서명[$LangID]?></label>
											<input type="text" id="DocumentReportName" name="DocumentReportName" value="<?=$DocumentReportName?>" class="md-input label-fixed"/>
										</div>
									</div>

									<div class="uk-grid" data-uk-grid-margin style="display:<?if ($DocumentReportID!=""){?>none<?}?>;">
										<div class="uk-width-medium-10-10">
											<label for="DocumentID" style="margin-right:30px;"><?=$양식선택[$LangID]?></label>

											<?
											$Sql3 = "select A.* from Documents A where A.DocumentID<>1 and A.DocumentState=1 and A.DocumentView=1 order by A.DocumentOrder asc";
											$Stmt3 = $DbConn->prepare($Sql3);
											$Stmt3->execute();
											$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
											while($Row3 = $Stmt3->fetch()) {
												
											?>
												<span class="icheck-inline">
													<input type="radio" class="radio_input" id="DocumentID<?=$Row3["DocumentID"]?>" name="DocumentID" <?php if ($DocumentID==$Row3["DocumentID"]) { echo "checked";}?> value="<?=$Row3["DocumentID"]?>" onclick="ChDocumentID(<?=$Row3["DocumentID"]?>);"/>
													<label for="DocumentID<?=$Row3["DocumentID"]?>" class="radio_label"><span class="radio_bullet"></span><?=$Row3["DocumentName"]?></label>
												</span>
											<?
											}
											$Stmt3 = null;
											?>

											<span class="icheck-inline">
												<input type="radio" class="radio_input" id="DocumentID0" name="DocumentID" <?php if ($DocumentID==0) { echo "checked";}?> value="1" onclick="ChDocumentID(0);"/>
												<label for="DocumentID0" class="radio_label"><span class="radio_bullet"></span><?=$양식_미사용[$LangID]?></label>
											</span>

										
										</div>
									</div>

									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-10-10">
											<label for="DocumentID" style="margin-right:30px;"><?=$회람인원[$LangID]?></label>
											<?
											if ($DocumentReportState==0 || $DocumentReportState==2){
												
												$Sql3 = "select A.*, B.MemberID as DocumentReportMemberID from Members A left outer join DocumentReportMembers B on A.MemberID=B.MemberID and B.DocumentReportID=$DocumentReportID where A.MemberLevelID<=4 and A.MemberID<>".$_LINK_ADMIN_ID_." order by A.MemberName asc";

												$Stmt3 = $DbConn->prepare($Sql3);
												$Stmt3->execute();
												$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
												while($Row3 = $Stmt3->fetch()) {

													$DocumentReportMemberID = $Row3["DocumentReportMemberID"];
											
											?>
												<span class="icheck-inline">
													<input type="checkbox" name="CheckMemberID_<?=$Row3["MemberID"]?>" id="CheckMemberID_<?=$Row3["MemberID"]?>" value="1" <?if ($DocumentReportMemberID){?>checked<?}?> data-md-icheck />
													<label for="CheckMemberID_<?=$Row3["MemberID"]?>" class="inline-label"><?=$Row3["MemberName"]?></label>
												</span> 
											<?
												}
												$Stmt3 = null;
											
											}else{


												$Sql3 = "select A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID where A.DocumentReportID=".$DocumentReportID." order by B.MemberName asc";
												$Stmt3 = $DbConn->prepare($Sql3);
												$Stmt3->execute();
												$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
												
												$ii=1;
												while($Row3 = $Stmt3->fetch()) {
											
													$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
													if ($DocumentReportMemberState==0){
														$StrDocumentReportMemberState = "-";
													}else if ($DocumentReportMemberState==1){
														$StrDocumentReportMemberState = "승인";
													}else if ($DocumentReportMemberState==2){
														$StrDocumentReportMemberState = "반려";
													}

													if ($ii>1){
														echo ", ";
													}
											?>
												<b><?=$Row3["MemberName"]?> (<?=$StrDocumentReportMemberState?>) </b>
											<?
													$ii++;
												}
												$Stmt3 = null;
											}
											?>
										
										</div>
									</div>

									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<textarea class="md-input" name="DocumentReportContent" id="DocumentReportContent" cols="30" rows="4"><?=$DocumentReportContent?></textarea>
										</div>
									</div>


								</div>
							</li>


						</ul>
                        

                        
                        
					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10" style="display:<? if ($DocumentReportState==1) {?>none<?}?>;">
				<div class="md-card">
					<div class="md-card-content">
						<h3 class="heading_c uk-margin-medium-bottom">저장설정</h3>


						<div class="uk-form-row">
							<input type="hidden" name="DocumentReportState" id="DocumentReportState" value="<?=$DocumentReportState?>">
							<a type="button" href="javascript:FormSubmit(2);" class="md-btn md-btn-worning">저장하기</a>
							<a type="button" href="javascript:FormSubmit(1);" class="md-btn md-btn-primary">제출하기</a>
							<? if ($DocumentReportState==2) {?>
							<a type="button" href="javascript:FormSubmit(0);" class="md-btn md-btn-danger">삭제하기</a>
							<?}?>
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

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->



<script language="javascript">
function ChDocumentID(DocumentID){

	UIkit.modal.confirm(
		'양식을 변경하면 작성중인 내용은 삭제됩니다. 변경 하시겠습니까?', 
		function(){ 
			location.href = "my_document_form.php?ListParam=<?=$ListParam?>&DocumentID="+DocumentID;
		}
	);	


}


function FormSubmit(DocumentReportState){
	
	document.RegForm.DocumentReportState.value = DocumentReportState;
	
	obj = document.RegForm.DocumentReportName;
	if (obj.value==""){
		UIkit.modal.alert("문서명을 입력하세요.");
		obj.focus();
		return;
	}



	if (DocumentReportState==2){//저장
		document.RegForm.action = "my_document_action.php";
		document.RegForm.submit();
	}else if (DocumentReportState==0){//삭제
		UIkit.modal.confirm(
			'삭제 하시겠습니까?', 
			function(){ 
				document.RegForm.action = "my_document_action.php";
				document.RegForm.submit();
			}
		);
	}else{//제출
		UIkit.modal.confirm(
			'저장 하시겠습니까?', 
			function(){ 
				document.RegForm.action = "my_document_action.php";
				document.RegForm.submit();
			}
		);
	}



}

</script>


<!-- ===========================================   froala_editor   =========================================== -->
<style>
#DocumentReportContent {
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
  const editorInstance = new FroalaEditor('#DocumentReportContent', {
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