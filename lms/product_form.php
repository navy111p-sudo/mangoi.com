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
$MainMenuID = 25;
$SubMenuID = 2511;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ProductID = isset($_REQUEST["ProductID"]) ? $_REQUEST["ProductID"] : "";
$PageTabID = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";
if ($PageTabID==""){
	$PageTabID = "1";
}


if ($ProductID!=""){

	$Sql = "
			select 
					A.*,
					B.ProductSellerID
			from Products A 
				inner join ProductCategories B on A.ProductCategoryID=B.ProductCategoryID 
			where A.ProductID=:ProductID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductID', $ProductID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$ProductSellerID = $Row["ProductSellerID"];
	$ProductSellerBookID = $Row["ProductSellerBookID"];
	$ProductISBN = $Row["ProductISBN"];
	$ProductCategoryID = $Row["ProductCategoryID"];
	$ProductName = $Row["ProductName"];
	$ProductCostPrice = $Row["ProductCostPrice"];
	$ProductPrice = $Row["ProductPrice"];
	$ProductMemo = $Row["ProductMemo"];
	$ProductImageFileName = $Row["ProductImageFileName"];
	$ProductState = $Row["ProductState"];
	$ProductView = $Row["ProductView"];

}else{
	$ProductSellerID = 0;
	$ProductSellerBookID = "";
	$ProductISBN = "";
	$ProductCategoryID = "";
	$ProductName = "";
	$ProductCostPrice = 0;
	$ProductPrice = 0;
	$ProductMemo = "";
	$ProductImageFileName = "";
	$ProductState = 1;
	$ProductView = 1;
}


if ($ProductImageFileName==""){
	$StrProductImageFileName = "images/logo_mangoi.png";
}else{
	$StrProductImageFileName = "../uploads/product_images/".$ProductImageFileName;
}
?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="ProductID" value="<?=$ProductID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="PageTabID" value="<?=$PageTabID?>">
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
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$ProductName?></span><span class="sub-heading" id="user_edit_position"><?=$교재정보[$LangID]?></span></h2>
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
							<li <?if ($PageTabID=="1"){?>class="uk-active"<?}?>><a href="#"><?=$교재정보[$LangID]?></a></li>
							<li <?if ($PageTabID=="2"){?>class="uk-active"<?}?>><a href="#"><?=$동영상목록[$LangID]?></a></li>
							<li <?if ($PageTabID=="3"){?>class="uk-active"<?}?>><a href="#"><?=$퀴즈목록[$LangID]?></a></li>
							<li <?if ($PageTabID=="4"){?>class="uk-active"<?}?>><a href="#"><?=$교재PDF자료[$LangID]?></a></li>
						</ul>
						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c"> 
										<?=$교재명[$LangID]?> (올북스 교재는 반드시 고유코드를 입력하셔야 올북스측에 주문이 접수됩니다.)
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10">
											<select id="ProductCategoryID" name="ProductCategoryID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="교재그룹선택" style="width:100%;"/>
												<option value=""></option>
												<?
												$AddSqlWhere2 = "";
												if ($ProductID==""){
													$AddSqlWhere2 = $AddSqlWhere2 . " and A.ProductSellerID<>2 ";//올북스는 자동으로 추가 되기 때문에 추가할 수 없다.
												}

												if ($ProductSellerID==2){
													$AddSqlWhere2 = $AddSqlWhere2 . " and A.ProductSellerID=2 ";//올북스교재만 추린다.
												}

												$Sql2 = "select 
																A.*,
																B.ProductSellerName
														from ProductCategories A 
															inner join ProductSellers B on A.ProductSellerID=B.ProductSellerID 
														where A.ProductCategoryState<>0 and B.ProductSellerState=1 ".$AddSqlWhere2."
														order by B.ProductSellerOrder asc, A.ProductCategoryState asc, A.ProductCategoryName asc";
												
												$Stmt2 = $DbConn->prepare($Sql2);
												$Stmt2->execute();
												$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
												
												$OldSelectProductCategoryState = -1;
												while($Row2 = $Stmt2->fetch()) {
													$SelectProductCategoryID = $Row2["ProductCategoryID"];
													$SelectProductCategoryName = $Row2["ProductCategoryName"];
													$SelectProductCategoryState = $Row2["ProductCategoryState"];

													$ProductSellerName = $Row2["ProductSellerName"];
												
													if ($OldSelectProductCategoryState!=$SelectProductCategoryState){
														if ($OldSelectProductCategoryState!=-1){
															echo "</optgroup>";
														}
														
														if ($SelectProductCategoryState==1){
															echo "<optgroup label=\"교재그룹(사용중)\">";
														}else if ($SelectProductCategoryState==2){
															echo "<optgroup label=\"교재그룹(미사용)\">";
														}
													}
													$OldSelectProductCategoryState = $SelectProductCategoryState;
												?>

												<option value="<?=$SelectProductCategoryID?>" <?if ($ProductCategoryID==$SelectProductCategoryID){?>selected<?}?>>[<?=$ProductSellerName?>] <?=$SelectProductCategoryName?></option>
												<?
												}
												$Stmt2 = null;
												?>
											</select>
										</div>
										<div class="uk-width-medium-6-10">
											<label for="ProductName"><?=$교재명[$LangID]?><?if ($ProductSellerID==2){?>(<?=$수정불가[$LangID]?>)<?}?></label>
											<input type="text" id="ProductName" name="ProductName" value="<?=$ProductName?>" class="md-input label-fixed" <?if ($ProductSellerID==2){?>readonly<?}?>/>
										</div>
									</div>
									
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-2-10">
											<label for="ProductSellerBookID"><?=$고유코드[$LangID]?><?if ($ProductSellerID==2){?>(<?=$수정불가[$LangID]?>)<?}?></label>
											<input type="text" id="ProductSellerBookID" name="ProductSellerBookID" value="<?=$ProductSellerBookID?>" class="md-input label-fixed" <?if ($ProductSellerID==2){?>readonly<?}?>/>
										</div>
										<div class="uk-width-medium-2-10">
											<label for="ProductISBN">ISBN<?if ($ProductSellerID==2){?>(<?=$수정불가[$LangID]?>)<?}?></label>
											<input type="text" id="ProductISBN" name="ProductISBN" value="<?=$ProductISBN?>" class="md-input label-fixed" <?if ($ProductSellerID==2){?>readonly<?}?>/>
										</div>
										<?if ($ProductSellerID==2){?>
										<div class="uk-width-medium-2-10">
											<label for="ProductCostPrice"><?=$올북스원가[$LangID]?><?if ($ProductSellerID==2){?>(<?=$수정불가[$LangID]?>)<?}?></label>
											<input type="text" id="ProductCostPrice" name="ProductCostPrice" value="<?=$ProductCostPrice?>" class="md-input label-fixed" <?if ($ProductSellerID==2){?>readonly<?}?>/>
										</div>
										<?}?>
										<div class="uk-width-medium-2-10">
											<label for="ProductPrice"><?=$판매가[$LangID]?></label>
											<input type="text" id="ProductPrice" name="ProductPrice" value="<?=$ProductPrice?>" class="md-input label-fixed"/>
										</div>
									</div>


									<h3 class="full_width_in_card heading_c"> 
										<?=$교재_이미지_및_메모[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-3-10">
											<input type="file" name="UpFile" id="UpFile" class="dropify" data-default-file="<?=$StrProductImageFileName?>"/>
										</div>
										<div class="uk-width-7-10">
											<label for="ProductMemo"><?=$메모[$LangID]?></label>
											<textarea class="md-input" name="ProductMemo" id="ProductMemo" cols="30" rows="7"><?=$ProductMemo?></textarea>
										</div>
									</div>




								</div>
							</li>

						</ul>
					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10">
				<div class="md-card">
					<div class="md-card-content">
						<h3 class="heading_c uk-margin-medium-bottom"><?=$기타설정[$LangID]?></h3>
						<div class="uk-form-row" style="display:none;">
							<input type="checkbox" id="ProductView" name="ProductView" value="1" <?php if ($ProductView==1) { echo "checked";}?> data-switchery/>
							<label for="ProductView" class="inline-label"><?=$실시간_올북스_보유[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">

						<div class="uk-form-row">
							<input type="checkbox" id="ProductState" name="ProductState" value="1" <?php if ($ProductState==1) { echo "checked";}?> data-switchery/>
							<label for="ProductState" class="inline-label"><?=$판매[$LangID]?></label>
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


<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->

<script language="javascript">

function FormSubmit(){

	obj = document.RegForm.ProductCategoryID;
	if (obj.value==""){
		UIkit.modal.alert("교재그룹를 선택하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.ProductName;
	if (obj.value==""){
		UIkit.modal.alert("교재명을 입력하세요.");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
				document.RegForm.action = "product_action.php";
				document.RegForm.submit();
		}
	);

}


window.onload = function(){

}
</script>



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