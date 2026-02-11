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
$MainMenuID = 11;
$SubMenuID = 1103;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$PopupID = isset($_REQUEST["PopupID"]) ? $_REQUEST["PopupID"] : "";

if ($PopupID!=""){

	$Sql = "select * from Popups where PopupID=:PopupID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':PopupID', $PopupID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$PopupName = $Row["PopupName"];
	$PopupTitle = $Row["PopupTitle"];
	$PopupStartDateNum = $Row["PopupStartDateNum"];
	$PopupEndDateNum = $Row["PopupEndDateNum"];
	$PopupWidth = $Row["PopupWidth"];
	$PopupHeight = $Row["PopupHeight"];
	$PopupTop = $Row["PopupTop"];
	$PopupLeft = $Row["PopupLeft"];
	$PopupType = $Row["PopupType"];
	$WebPopup = $Row["WebPopup"];
	$MobilePopup = $Row["MobilePopup"];
	$AppPopup = $Row["AppPopup"];
	$PopupContent = $Row["PopupContent"];
	$PopupImage = $Row["PopupImage"];
	$PopupState = $Row["PopupState"];
	$NewData = "0";

	$PopupImageLink = $Row["PopupImageLink"];
	$PopupImageLinkType = $Row["PopupImageLinkType"];

	$ArrDomainSiteID[0] = $Row["DomainSiteID_0"];
	$ArrDomainSiteID[1] = $Row["DomainSiteID_1"];
	$ArrDomainSiteID[2] = $Row["DomainSiteID_2"];
	$ArrDomainSiteID[3] = $Row["DomainSiteID_3"];
	$ArrDomainSiteID[4] = $Row["DomainSiteID_4"];
	$ArrDomainSiteID[5] = $Row["DomainSiteID_5"];

	$PopupName = str_replace('"', "&#34;", $PopupName);
	$PopupTitle = str_replace('"', "&#34;", $PopupTitle);



}else{
	$PopupName = "";
	$PopupTitle = "";
	$PopupStartDateNum = date("Y-m-d");
	$PopupEndDateNum = date("Y-m-d");
	$PopupWidth = 500;
	$PopupHeight = 500;
	$PopupTop = 0;
	$PopupLeft = 0;
	$PopupType = 1;
	$WebPopup = 1;
	$MobilePopup = 1;
	$AppPopup = 1;
	$PopupContent = "";
	$PopupImage = "";
	$PopupState = 0;
	$NewData = "1";
	$PopupImageLink = "";
	$PopupImageLinkType=1;


	$ArrDomainSiteID[0] = 1;
	$ArrDomainSiteID[1] = 1;
	$ArrDomainSiteID[2] = 1;
	$ArrDomainSiteID[3] = 1;
	$ArrDomainSiteID[4] = 1;
	$ArrDomainSiteID[5] = 1;

}

?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="PopupID" value="<?=$PopupID?>">
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
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$PopupName?></span><span class="sub-heading" id="user_edit_position">팝업정보</span></h2>
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
							<li class="uk-active"><a href="#">Basic</a></li>
							<li><a href="#">Groups</a></li>
							<li><a href="#">Todo</a></li>
						</ul>
						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c">
										<?=$팝업명_또는_타이틀[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2">
											<label for="PopupName"><?=$팝업명[$LangID]?></label>
											<input type="text" id="PopupName" name="PopupName" value="<?=$PopupName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-1-2">
											<label for="PopupTitle"><?=$제목[$LangID]?></label>
											<input type="text" id="PopupTitle" name="PopupTitle" value="<?=$PopupTitle?>" class="md-input label-fixed"/>
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$운영기간[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="uk_dp_start"><?=$시작일[$LangID]?></label>
											<input type="text" id="PopupStartDateNum" name="PopupStartDateNum" value="<?=$PopupStartDateNum?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}">
											<span class="uk-input-group-addon"><i class="uk-input-group-icon uk-icon-calendar"></i></span>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="uk_dp_end"><?=$종료일[$LangID]?></label>
											<input type="text" id="PopupEndDateNum" name="PopupEndDateNum" value="<?=$PopupEndDateNum?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}">
											<span class="uk-input-group-addon"><i class="uk-input-group-icon uk-icon-calendar"></i></span>
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$크기_및_위치[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="PopupWidth"><?=$가로[$LangID]?></label>
											<input type="number" id="PopupWidth" name="PopupWidth" value="<?=$PopupWidth?>" class="md-input label-fixed allownumericwithoutdecimal" />
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="PopupHeight"><?=$세로[$LangID]?></label>
											<input type="number" id="PopupHeight" name="PopupHeight" value="<?=$PopupHeight?>" class="md-input label-fixed allownumericwithoutdecimal" />
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="PopupTop"><?=$상단[$LangID]?></label>
											<input type="number" id="PopupTop" name="PopupTop" value="<?=$PopupTop?>" class="md-input label-fixed allownumericwithoutdecimal" />
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="PopupLeft"><?=$좌측[$LangID]?></label>
											<input type="number" id="PopupLeft" name="PopupLeft" value="<?=$PopupLeft?>" class="md-input label-fixed allownumericwithoutdecimal" />
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$내용_및_링크[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<span class="icheck-inline">
												<input type="radio" class="radio_input" id="PopupType1" name="PopupType" <?php if ($PopupType==1) { echo "checked";}?> value="1" onclick="CheckPopupType(1);"/>
												<label for="PopupType1" class="radio_label"><span class="radio_bullet"></span><?=$이미지[$LangID]?></label>
											</span>
											<span class="icheck-inline">
												<input type="radio" class="radio_input" id="PopupType2" name="PopupType" <?php if ($PopupType==2) { echo "checked";}?> value="2" onclick="CheckPopupType(2);"/>
												<label for="PopupType2" class="radio_label"><span class="radio_bullet"></span><?=$텍스트[$LangID]?></label>
											</span>
										</div>
									</div>

									<div class="uk-grid" id="DivPopupType1_1" style="display:<?php if ($PopupType==1) { } else{ echo "none";}?>;" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<input type="file" id="PopupImage" name="PopupImage" value="<?=$PopupTitle?>" class="label-fixed"/>
										</div>
									</div>

									<div class="uk-grid" id="DivPopupType2" style="display:<?php if ($PopupType==2) { } else{ echo "none;";}?>" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<textarea name="PopupContent" id="PopupContent" cols="100" rows="12" style="margin-top:5px;margin-bottom:5px;"><?=$PopupContent?></textarea>
										</div>
									</div>

									<div class="uk-grid" id="DivPopupType1_2" style="display:<?php if ($PopupType==1) { } else{ echo "none";}?>;" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<input type="text" id="PopupImageLink" name="PopupImageLink" value="<?=$PopupImageLink?>" placeholder="http:// 포함 전체 주소 입력 (공백일경우 링크무시)" class="md-input label-fixed"/>
										</div>
									</div>

									<div class="uk-grid" id="DivPopupType1_3" style="display:<?php if ($PopupType==1) { } else{ echo "none";}?>;" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<span class="icheck-inline">
												<input type="radio" id="PopupImageLinkType1" name="PopupImageLinkType" <?php if ($PopupImageLinkType==1) { echo "checked";}?> value="1" data-md-icheck/>
												<label for="PopupImageLinkType1" class="inline-label"><?=$현재창[$LangID]?></label>
											</span>
											<span class="icheck-inline">
												<input type="radio" id="PopupImageLinkType2" name="PopupImageLinkType" <?php if ($PopupImageLinkType==2) { echo "checked";}?> value="2" data-md-icheck/>
												<label for="PopupImageLinkType2" class="inline-label"><?=$새창[$LangID]?></label>
											</span>
										</div>
									</div>



								</div>
							</li>
							<li>

							</li>
							<li>

							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10">
				<div class="md-card">
					<div class="md-card-content">
						<h3 class="heading_c uk-margin-medium-bottom"><?=$적용사이트[$LangID]?></h3>
						
						
						<?
						$ArrDomainSiteName = explode("|", "본사|SLP|EIE|DREAM|THOMAS|ENG_TELL");
						for ($ii=0;$ii<=5;$ii++){
						?>
							<div class="uk-form-row">
								<input type="checkbox" id="DomainSiteID_<?=$ii?>" name="DomainSiteID_<?=$ii?>" value="1" <?php if ($ArrDomainSiteID[$ii]==1) { echo "checked";}?> data-switchery/>
								<label for="DomainSiteID_<?=$ii?>" class="inline-label"><?=$ArrDomainSiteName[$ii]?></label>
							</div>
							<hr class="md-hr">
						<?
						}
						?>


						
						<h3 class="heading_c uk-margin-medium-bottom"><?=$디바이스[$LangID]?></h3>
						<div class="uk-form-row">
							<input type="checkbox" id="WebPopup" name="WebPopup" value="1" <?php if ($WebPopup==1) { echo "checked";}?> data-switchery/>
							<label for="WebPopup" class="inline-label">WEB</label>
						</div>
						<hr class="md-hr">
						<div class="uk-form-row">
							<input type="checkbox" id="MobilePopup" name="MobilePopup" value="1" <?php if ($MobilePopup==1) { echo "checked";}?> data-switchery/>
							<label for="MobilePopup" class="inline-label">MOBILE (타입이 이미지일 경우만 가능)</label>
						</div>
						<hr class="md-hr">
						<div class="uk-form-row">
							<input type="checkbox" id="AppPopup" name="AppPopup" value="1" <?php if ($AppPopup==1) { echo "checked";}?> data-switchery/>
							<label for="AppPopup" class="inline-label">APP (타입이 이미지일 경우만 가능)</label>
						</div>
						<hr class="md-hr">

						<h3 class="heading_c uk-margin-medium-bottom"><?=$기타설정[$LangID]?></h3>
						<div class="uk-form-row">
							<input type="checkbox" id="PopupState" name="PopupState" value="1" <?php if ($PopupState==1) { echo "checked";}?> data-switchery/>
							<label for="PopupState" class="inline-label"><?=$팝업_활성화[$LangID]?></label>
						</div>

						<hr class="md-hr">
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

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<script>
function CheckPopupType(type){
	if (type==1){
		document.getElementById('DivPopupType2').style.display = "none";
		document.getElementById('DivPopupType1_1').style.display = "";
		document.getElementById('DivPopupType1_2').style.display = "";
		document.getElementById('DivPopupType1_3').style.display = "";
	}else{
		document.getElementById('DivPopupType1_1').style.display = "none";
		document.getElementById('DivPopupType1_2').style.display = "none";
		document.getElementById('DivPopupType1_3').style.display = "none";
		document.getElementById('DivPopupType2').style.display = "";
	}
}

</script>

<script language="javascript">

function FormSubmit(){
	obj = document.RegForm.PopupName;
	if (obj.value==""){
		UIkit.modal.alert("팝업명을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.PopupTitle;
	if (obj.value==""){
		UIkit.modal.alert("팝업 타이틀을 입력하세요.");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "popup_action.php";
			document.RegForm.submit();
		}
	);


}

</script>



<!-- ===========================================   froala_editor   =========================================== -->
<style>
#PopupContent {
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
  const editorInstance = new FroalaEditor('#PopupContent', {
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