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


</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<?php
$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BookScanID = isset($_REQUEST["BookScanID"]) ? $_REQUEST["BookScanID"] : "";


if ($BookScanID!=""){

	$Sql = "
			select 
					A.*,
					B.BookName 
			from BookScans A 
				inner join Books B on A.BookID=B.BookID 
			where A.BookScanID=:BookScanID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookScanID', $BookScanID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BookScanName = $Row["BookScanName"];
	$BookScanImageFileName = $Row["BookScanImageFileName"];
	$BookScanView = $Row["BookScanView"];
	$BookScanState = $Row["BookScanState"];

	$BookName = $Row["BookName"];

}else{
	$BookScanName = "";
	$BookScanImageFileName = "";
	$BookScanView = 1;
	$BookScanState = 1;

	$Sql = "
			select 
					A.*
			from Books A 
			where A.BookID=:BookID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookID', $BookID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	
	$BookName = $Row["BookName"];
}


// if ($BookScanImageFileName==""){
// 	$StrBookScanImageFileName = "images/logo_mangoi.png";
// }else{
// 	$StrBookScanImageFileName = "../uploads/book_scan_images/".$BookScanImageFileName;
// }
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="BookID" value="<?=$BookID?>">
		<input type="hidden" name="BookScanID" value="<?=$BookScanID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="UpFile" id="UpFile" value="<?=$BookScanImageFileName?>"/>
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$BookName?></span><span class="sub-heading" id="user_edit_position"><?=$교재PDF_자료관리[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<h3 class="full_width_in_card heading_c"> 
							<?=$교재_PDF이미지_및_제목[$LangID]?>
						</h3>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="BookScanName"><?=$PDF자료_제목[$LangID]?></label>
									<input type="text" id="BookScanName" name="BookScanName" value="<?=$BookScanName?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>
						<div class="uk-margin-top">						
								<div class="uk-width-medium-1-1">									
									<a href="javascript:PopupUploadPPT('RegForm.UpFile','../uploads/book_pdf_uploads');" class="md-btn md-btn-primary" style="width:200px;"><?=$PDF_파일_업로드[$LangID]?></a>
									<a href="javascript:OpenPreview();" class="md-btn md-btn-primary" style="width:200px;" ><?=$미리보기[$LangID]?></a>
								</div>
					  	</div>
						<!-- 
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<input type="file" name="UpFile" id="UpFile" class="dropify" data-default-file="<?=$StrBookScanImageFileName?>"/>
								</div>
							</div>
						</div> -->
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2 uk-input-group">
									<input type="checkbox" id="BookScanState" name="BookScanState" value="1" <?php if ($BookScanState==1) { echo "checked";}?> data-switchery/>
									<label for="BookScanState" class="inline-label"><?=$사용[$LangID]?></label>
								</div>
							</div>
						</div>

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
function PopupUploadPPT(FormNameFile, UpPath){
	openurl = "./popup_pdf_upload_form.php?FormNameFile="+FormNameFile+"&UpPath="+UpPath;
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

function OpenPreview(){		
	PdfFileName = document.RegForm.UpFile.value;
	if (PdfFileName==""){
		UIkit.modal.alert("<?=$PDF파일을_업로드_하세요[$LangID]?>");
	}else{
		OpenContentType(PdfFileName);
	}
}

function OpenContentType(PdfFileName){
		var iframe = "<html><head><style>body, html {width: 100%; height: 100%; margin: 0; padding: 0}</style></head><body><iframe src='../ViewerJS/?zoom=page-width#../uploads/book_pdf_uploads/"+PdfFileName+"' frameborder='0' style='height:calc(100% - 4px);width:calc(100% - 4px)'></iframe><input type='hidden' id='filename' value='"+PdfFileName+"'></div></body></html>";
		var win = window.open("","coursebookcontent","frameborder=0,width="+screen.width+",height="+screen.height+",top=0,left=0,directories=no,location=no,channelmode=yes,fullscreen=yes,menubar=no, resizable=no,status=no,toolbar=no,history=no,scrollbars=yes");

		win.document.write(iframe);

}

function FormSubmit(){
	obj = document.RegForm.BookScanName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$PDF자료_제목을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	<?if ($BookScanID==""){?>
		obj = document.RegForm.UpFile;
		if (obj.value==""){
			UIkit.modal.alert("<?=$PDF_이미지를_업로드하세요[$LangID]?>");
			obj.focus();
			return;
		}
	<?}?>

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "book_scan_action.php";
			document.RegForm.submit();
		}
	);
}
</script>



<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>