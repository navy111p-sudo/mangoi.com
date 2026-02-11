<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->


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


</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$DirectQnaMemberID = isset($_REQUEST["DirectQnaMemberID"]) ? $_REQUEST["DirectQnaMemberID"] : "";


if ($DirectQnaMemberID!=""){

	$Sql = "
			select 
					A.*
			from DirectQnaMembers A 
				left outer join Members B on A.MemberID=B.MemberID 
				left outer join Members C on A.AnswerMemberID=C.MemberID 
			where A.DirectQnaMemberID=:DirectQnaMemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DirectQnaMemberID', $DirectQnaMemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$ContentType = $Row["ContentType"];
	$AnswerType = $Row["AnswerType"];

	$MemberID = $Row["MemberID"];
	$MemberName = $Row["MemberName"];
	$AnswerMemberID = $Row["AnswerMemberID"];
	$AnswerMemberName = $Row["AnswerMemberName"];
	$DirectQnaMemberTitle = $Row["DirectQnaMemberTitle"];
	$DirectQnaMemberContent = $Row["DirectQnaMemberContent"];
	$DirectQnaMemberFileName = $Row["DirectQnaMemberFileName"];
	$DirectQnaMemberFileRealName = $Row["DirectQnaMemberFileRealName"];
	$DirectQnaMemberAnswer = $Row["DirectQnaMemberAnswer"];
	$DirectQnaMemberRegDateTime = $Row["DirectQnaMemberRegDateTime"];
	$DirectQnaMemberAnswerRegDateTime = $Row["DirectQnaMemberAnswerRegDateTime"];
	$DirectQnaMemberState = $Row["DirectQnaMemberState"];


	if ($ContentType==0){
		$DirectQnaMemberContent = str_replace("\n","<br>",$DirectQnaMemberContent);
	}


	if ($AnswerMemberID==""){
		$AnswerMemberID = $_LINK_ADMIN_ID_;
	}
	if ($AnswerMemberName==""){
		$AnswerMemberName = $_LINK_ADMIN_NAME_;
	}

}else{

	$MemberID = $_LINK_ADMIN_ID_;
	$MemberName = $_LINK_ADMIN_NAME_;
	$DirectQnaMemberTitle = "";
	$DirectQnaMemberContent = "";
	$DirectQnaMemberState = 1;
	$DirectQnaMemberFileName = "";
	$DirectQnaMemberFileRealName = "";
}

?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="DirectQnaMemberID" value="<?=$DirectQnaMemberID?>">
		<input type="hidden" name="AnswerType" value="<?=$AnswerType?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname">1:1 문의</span><span class="sub-heading" id="user_edit_position"><!--질문관리--></span></h2>
						</div>
					</div>
					<div class="user_content">
						<h3 class="full_width_in_card heading_c"> 
							<?=$질문[$LangID]?>
						</h3>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="MemberName"><?=$이름[$LangID]?></label>
									<input type="hidden" id="MemberID" name="MemberID" value="<?=$MemberID?>"/>
									<input type="text" id="MemberName" name="MemberName" value="<?=$MemberName?>" class="md-input label-fixed" readonly/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="DirectQnaMemberTitle"><?=$제목[$LangID]?></label>
									<input type="text" id="DirectQnaMemberTitle" name="DirectQnaMemberTitle" value="<?=$DirectQnaMemberTitle?>" class="md-input label-fixed" readonly/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="DirectQnaMemberContent"><?=$질문내용[$LangID]?></label><br/>
									<? if($DirectQnaMemberFileName!=null) { ?>
									<a style="float: right;" href="../uploads/app_upload_direct_qna/<?=$DirectQnaMemberFileName?>" download="<?=$DirectQnaMemberFileRealName?>" ><?=$첨부파일다운[$LangID]?></a>
									<? } ?>
									<div style="margin-top:20px;border:1px solid #cccccc;padding:20px;"><?=$DirectQnaMemberContent?></div>
								</div>
							</div>
						</div>
					
						<?if ($DirectQnaMemberID!=""){?>
						<h3 class="full_width_in_card heading_c"> 
							<?=$답변[$LangID]?>
						</h3>
						<input type="hidden" id="AnswerMemberID" name="AnswerMemberID" value="<?=$AnswerMemberID?>" class="md-input label-fixed"/>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="AnswerMemberName"><?=$답변자[$LangID]?></label>
									<input type="text" id="AnswerMemberName" name="AnswerMemberName" value="<?=$AnswerMemberName?>" class="md-input label-fixed"/>
								</div>
								<div class="uk-width-medium-1-2">
									<span class="icheck-inline">
										<input type="radio" name="DirectQnaMemberState" id="DirectQnaMemberState1" value="1" <?if ($DirectQnaMemberState==1){?>checked<?}?> data-md-icheck />
										<label for="DirectQnaMemberState1" class="inline-label"><?=$답변전[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" name="DirectQnaMemberState" id="DirectQnaMemberState2" value="2" <?if ($DirectQnaMemberState==2){?>checked<?}?> data-md-icheck />
										<label for="DirectQnaMemberState2" class="inline-label"><?=$답변완료[$LangID]?></label>
									</span>
								</div>
							</div>
						</div>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<textarea class="md-input" name="DirectQnaMemberAnswer" id="DirectQnaMemberAnswer" cols="30" rows="4"><?=$DirectQnaMemberAnswer?></textarea>
								</div>
							</div>
						</div>
						<?}else{?>
						<input type="hidden" name="DirectQnaMemberState" id="DirectQnaMemberState" value="1"/>
						<?}?>


						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
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

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<script language="javascript">

function FormSubmit(){




	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "direct_qna_member_action.php";
			document.RegForm.submit();
		}
	);

}

</script>



<!-- ===========================================   froala_editor   =========================================== -->
<?if ($AnswerType==1){?>
<style>
#DirectQnaMemberAnswer {
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
  const editorInstance = new FroalaEditor('#DirectQnaMemberAnswer', {
	key: "xGE6oB4B3C3A6D6E5fLUQZf1ASFb1EFRNh1Hb1BCCQDUHnA8B6E5B4B1C3I3A1B8A6==",
	enter: FroalaEditor.ENTER_BR,
	heightMin: 200,
	fileUploadURL: '../froala_editor_file_upload.php',
	imageUploadURL: '../froala_editor_image_upload.php',
	placeholderText: null,
	toolbarButtons: [ ['undo', 'redo', 'bold', 'italic', 'underline', 'strikeThrough', 'textColor', 'upload', 'insertImage'] ],
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
<?}?>
<!-- ===========================================   froala_editor   =========================================== -->


<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>