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
$SubMenuID = 2501;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";
$PageTabID = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";
if ($PageTabID==""){
	$PageTabID = "1";
}


if ($BookID!=""){

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

	$BookGroupID = $Row["BookGroupID"];
	$BookName = $Row["BookName"];
	$BookMemo = $Row["BookMemo"];
	$BookImageFileName = $Row["BookImageFileName"];
	$BookState = $Row["BookState"];
	$BookView = $Row["BookView"];
	$BookTeacherView = $Row["BookTeacherView"];
	$BookViewList = $Row["BookViewList"];

}else{
	$BookGroupID = "";
	$BookName = "";
	$BookMemo = "";
	$BookImageFileName = "";
	$BookState = 1;
	$BookView = 1;
	$BookTeacherView = 1;
	$BookViewList = 1;
}


if ($BookImageFileName==""){
	$StrBookImageFileName = "images/logo_mangoi.png";
}else{
	$StrBookImageFileName = "../uploads/book_images/".$BookImageFileName;
}
?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="BookID" value="<?=$BookID?>">
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
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$BookName?></span><span class="sub-heading" id="user_edit_position"><?=$교재정보[$LangID]?></span></h2>
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
						
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:<?if ($BookID==""){?>none<?}?>;">
							<li <?if ($PageTabID=="1"){?>class="uk-active"<?}?>><a href="#"><?=$교재정보[$LangID]?></a></li>
							<li <?if ($PageTabID=="2"){?>class="uk-active"<?}?>><a href="#"><?=$동영상목록[$LangID]?></a></li>
							<li <?if ($PageTabID=="3"){?>class="uk-active"<?}?>><a href="#"><?=$퀴즈목록[$LangID]?></a></li>
							<li <?if ($PageTabID=="4"){?>class="uk-active"<?}?>><a href="#"><?=$교재PDF자료[$LangID]?></a></li>
						</ul>
						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c"> 
										<?=$교재명[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10">
											<select id="BookGroupID" name="BookGroupID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="<?=$교재그룹선택[$LangID]?>" style="width:100%;"/>
												<option value=""></option>
												<?
												$Sql2 = "select 
																A.*
														from BookGroups A 
														where A.BookGroupState<>0 
														order by A.BookGroupState asc, A.BookGroupName asc";
												
												$Stmt2 = $DbConn->prepare($Sql2);
												$Stmt2->execute();
												$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
												
												$OldSelectBookGroupState = -1;
												while($Row2 = $Stmt2->fetch()) {
													$SelectBookGroupID = $Row2["BookGroupID"];
													$SelectBookGroupName = $Row2["BookGroupName"];
													$SelectBookGroupState = $Row2["BookGroupState"];
												
													if ($OldSelectBookGroupState!=$SelectBookGroupState){
														if ($OldSelectBookGroupState!=-1){
															echo "</optgroup>";
														}
														
														if ($SelectBookGroupState==1){
															echo "<optgroup label=\"교재그룹(사용중)\">";
														}else if ($SelectBookGroupState==2){
															echo "<optgroup label=\"교재그룹(미사용)\">";
														}
													}
													$OldSelectBookGroupState = $SelectBookGroupState;
												?>

												<option value="<?=$SelectBookGroupID?>" <?if ($BookGroupID==$SelectBookGroupID){?>selected<?}?>><?=$SelectBookGroupName?></option>
												<?
												}
												$Stmt2 = null;
												?>
											</select>
										</div>
										<div class="uk-width-medium-6-10">
											<label for="BookName"><?=$교재명[$LangID]?></label>
											<input type="text" id="BookName" name="BookName" value="<?=$BookName?>" class="md-input label-fixed"/>
										</div>
									</div>


									<h3 class="full_width_in_card heading_c"> 
										<?=$교재_이미지_및_소개[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-3-10">
											<input type="file" name="UpFile" id="UpFile" class="dropify" data-default-file="<?=$StrBookImageFileName?>"/>
										</div>
										<div class="uk-width-7-10">
											<label for="BookMemo"><?=$교재소개[$LangID]?></label>
											<textarea class="md-input" name="BookMemo" id="BookMemo" cols="30" rows="7"><?=$BookMemo?></textarea>
										</div>
									</div>




								</div>
							</li>
							<?
							if ($BookID!=""){
							?>
							<li id="DivBookVideoList">
								<!--동영상 목록-->


								<!--동영상 목록-->
							</li>
							<li id="DivBookQuizList">
								<!--퀴즈 목록-->

								<!--퀴즈 목록-->
							</li>
							<li id="DivBookScanList">
								<!--교재 스캔 자료-->


								<!--교재 스캔 자료-->
							</li>
							<?
							}
							?>
						</ul>
					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10">
				<div class="md-card">
					<div class="md-card-content">
						<h3 class="heading_c uk-margin-medium-bottom"><?=$기타설정[$LangID]?></h3>
						<div class="uk-form-row" style="display:none;">
							<input type="checkbox" id="BookView" name="BookView" value="1" <?php if ($BookView==1) { echo "checked";}?> data-switchery/>
							<label for="BookView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">

						<div class="uk-form-row">
							<input type="checkbox" id="BookState" name="BookState" value="1" <?php if ($BookState==1) { echo "checked";}?> data-switchery/>
							<label for="BookState" class="inline-label"><?=$사용중[$LangID]?></label>
						</div>
						<hr class="md-hr">

						<div class="uk-form-row">
							<input type="checkbox" id="BookTeacherView" name="BookTeacherView" value="1" <?php if ($BookTeacherView==1) { echo "checked";}?> data-switchery/>
							<label for="BookTeacherView" class="inline-label"><?=$강사에게노출[$LangID]?></label>
						</div>
						<hr class="md-hr">

						<div class="uk-form-row">
							<input type="checkbox" id="BookViewList" name="BookViewList" value="1" <?php if ($BookViewList==1) { echo "checked";}?> data-switchery/>
							<label for="BookViewList" class="inline-label"><?=$목록에노출[$LangID]?></label>
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

	obj = document.RegForm.BookGroupID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$교재그룹를_선택하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.BookName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$교재명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'<?=$저장하시겠습니까[$LangID]?>?', 
		function(){ 
				document.RegForm.action = "book_action.php";
				document.RegForm.submit();
		}
	);

}

function OpenContentType(PdfFileName){
	if(PdfFileName==""){
		alert("<?=$PDF파일이_없습니다[$LangID]?>");
	}else{
		var iframe = "<html><head><style>body, html {width: 100%; height: 100%; margin: 0; padding: 0}</style></head><body><iframe src='../ViewerJS/?zoom=page-width#../uploads/book_pdf_uploads/"+PdfFileName+"' frameborder='0' style='height:calc(100% - 4px);width:calc(100% - 4px)'></iframe><input type='hidden' id='filename' value='"+PdfFileName+"'></div></body></html>";

		var win = window.open("","coursebookcontent","frameborder=0,width="+screen.width+",height="+screen.height+",top=0,left=0,directories=no,location=no,channelmode=yes,fullscreen=yes,menubar=no, resizable=no,status=no,toolbar=no,history=no,scrollbars=yes");

		win.document.write(iframe);
	}

}

function SetBookScanListOrder(BookScanID, OrderType) {
	url = "ajax_set_book_scan_list_order.php";

	
	//location.href = url + "?TeacherCharacterItemID="+TeacherCharacterItemID+"&OrderType="+OrderType;

    $.ajax(url, {
        data: {
			BookScanID: BookScanID,
			OrderType: OrderType
        },
        success: function () {
			GetBookScanList();
			//location.reload();
        },
        error: function () {
            alert('Error while contacting server, please try again');
        }

    });
}


function SetBookVideoListOrder(BookVideoID, OrderType) {
	url = "ajax_set_book_video_list_order.php";

	
	//location.href = url + "?TeacherCharacterItemID="+TeacherCharacterItemID+"&OrderType="+OrderType;

    $.ajax(url, {
        data: {
			BookVideoID: BookVideoID,
			OrderType: OrderType
        },
        success: function () {
			GetBookVideoList();
			//location.reload();
        },
        error: function () {
            alert('Error while contacting server, please try again');
        }

    });
}


function SetBookQuizListOrder(BookQuizID, OrderType, BookID) {
	url = "ajax_set_book_quiz_list_order.php";

	
	//location.href = url + "?TeacherCharacterItemID="+TeacherCharacterItemID+"&OrderType="+OrderType;

    $.ajax(url, {
        data: {
			BookQuizID: BookQuizID,
			OrderType: OrderType,
			BookID: BookID
        },
        success: function () {
			GetBookQuizList();
			//location.reload();
        },
        error: function () {
            alert('Error while contacting server, please try again');
        }

    });
}


function SetBookQuizDetailListOrder(BookQuizDetailID, OrderType, BookQuizID){
	url = "ajax_set_book_quiz_detail_list_order.php";

	
	//location.href = url + "?BookQuizDetailID="+BookQuizDetailID+"&OrderType="+OrderType+"&BookQuizID="+BookQuizID;

    $.ajax(url, {
        data: {
			BookQuizDetailID: BookQuizDetailID,
			OrderType: OrderType,
			BookQuizID: BookQuizID
        },
        success: function () {
			GetBookQuizDetailList(BookQuizID);
			//location.reload();
        },
        error: function () {
            alert('Error while contacting server, please try again');
        }

    });

}


function OpenVideoPlayer(BookVideoType, BookVideoCode) {
	
	openurl = "video_player.php?VideoCode="+BookVideoCode+"&VideoType="+BookVideoType;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:false
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 

}


function OpenBookVideoForm(BookVideoID){
	openurl = "book_video_form.php?BookID=<?=$BookID?>&ListParam=<?=$ListParam?>&BookVideoID="+BookVideoID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenBookViewer(BookScanID){

}

function OpenBookScanForm(BookScanID){
	openurl = "book_scan_form.php?BookID=<?=$BookID?>&ListParam=<?=$ListParam?>&BookScanID="+BookScanID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function OpenBookQuizForm(BookQuizID){
	openurl = "book_quiz_form.php?BookID=<?=$BookID?>&ListParam=<?=$ListParam?>&BookQuizID="+BookQuizID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function OpenBookQuizDetailForm(BookQuizID, BookQuizDetailID){

	openurl = "book_quiz_detail_form.php?BookID=<?=$BookID?>&ListParam=<?=$ListParam?>&BookQuizID="+BookQuizID+"&BookQuizDetailID="+BookQuizDetailID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){
			GetBookQuizDetailList(BookQuizID);
		}
		//,onComplete:function(){alert(1);}
	}); 

}

function GetBookQuizDetailList(BookQuizID){
	url = "ajax_get_book_quiz_detail_list.php";

	//location.href = url + "?NewID="+NewID;
	$.ajax(url, {
		data: {
			BookQuizID: BookQuizID
		},
		success: function (data) {
			json_data = data;
			document.getElementById("BookQuizDetailList_"+BookQuizID).innerHTML = data.BookQuizDetailList;
		},
		error: function () {

		}
	});
}


function GetBookVideoList(){
	url = "ajax_get_book_video_list.php";

	//location.href = url + "?NewID="+NewID;
	$.ajax(url, {
		data: {
			BookID: '<?=$BookID?>'
		},
		success: function (data) {
			json_data = data;
			document.getElementById("DivBookVideoList").innerHTML = data.BookVideoList;
		},
		error: function () {

		}
	});
}


function GetBookQuizList(){
	url = "ajax_get_book_quiz_list.php";

	//location.href = url + "?NewID="+NewID;
	$.ajax(url, {
		data: {
			BookID: '<?=$BookID?>'
		},
		success: function (data) {
			json_data = data;
			document.getElementById("DivBookQuizList").innerHTML = data.BookQuizList;
		},
		error: function () {

		}
	});
}


function GetBookScanList(){
	url = "ajax_get_book_scan_list.php";

	//location.href = url + "?BookID=<?=$BookID?>";
	$.ajax(url, {
		data: {
			BookID: '<?=$BookID?>'
		},
		success: function (data) {
			json_data = data;
			document.getElementById("DivBookScanList").innerHTML = data.BookScanList;
		},
		error: function () {

		}
	});
}

window.onload = function(){
<?if ($BookID!="") {?>
	GetBookVideoList();
	GetBookQuizList();
	GetBookScanList();
<?}?>
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