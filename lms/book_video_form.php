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

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<?php
$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BookVideoID = isset($_REQUEST["BookVideoID"]) ? $_REQUEST["BookVideoID"] : "";


if ($BookVideoID!=""){

	$Sql = "
			select 
					A.*,
					B.BookName 
			from BookVideos A 
				inner join Books B on A.BookID=B.BookID 
			where A.BookVideoID=:BookVideoID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookVideoID', $BookVideoID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BookVideoName = $Row["BookVideoName"];
	$BookVideoMemo = $Row["BookVideoMemo"];
	$BookVideoType = $Row["BookVideoType"];
	$BookVideoType2 = $Row["BookVideoType2"];
	$BookVideoCode = $Row["BookVideoCode"];
	$BookVideoCode2 = $Row["BookVideoCode2"];
	$BookVideoView = $Row["BookVideoView"];
	$BookVideoState = $Row["BookVideoState"];

	$BookName = $Row["BookName"];

}else{
	$BookVideoName = "";
	$BookVideoMemo = "";
	$BookVideoType = 1;
	$BookVideoType2 = 1;
	$BookVideoCode = "";
	$BookVideoCode2 = "";
	$BookVideoView = 1;
	$BookVideoState = 1;

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
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="BookID" value="<?=$BookID?>">
		<input type="hidden" name="BookVideoID" value="<?=$BookVideoID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$BookName?></span><span class="sub-heading" id="user_edit_position"><?=$동영상관리[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="BookVideoName"><?=$동영상제목[$LangID]?></label>
									<input type="text" id="BookVideoName" name="BookVideoName" value="<?=$BookVideoName?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1 uk-input-group">
									※ Youtube 또는 Vimeo 코드를 입력하세요.(아래 예제의 <span style='color:#ff0000;'>빨간색</span> 코드 부분)<br>
									※ 예) https://www.youtube.com/watch?v=<span style='color:#ff0000;'>LDPt7XLrbks</span> , https://vimeo.com/<span style='color:#ff0000;'>159328419</span> 
								</div>
							</div>
						</div>

						<hr>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-5-10 uk-form-row">
									<span class="icheck-inline">
										<input type="radio" id="BookVideoType1_1" name="BookVideoType" value="1" <?php if ($BookVideoType==1) { echo "checked";}?> data-md-icheck/>
										<label for="BookVideoType1_1" class="inline-label">Youtube</label>
									</span>
									<span class="icheck-inline">
										<input type="radio" id="BookVideoType1_2" name="BookVideoType" value="2" <?php if ($BookVideoType==2) { echo "checked";}?> data-md-icheck/>
										<label for="BookVideoType1_2" class="inline-label">Vimeo</label>
									</span>
								</div>
								<div class="uk-width-medium-5-10 uk-input-group">
									<label for="BookVideoCode">A 타입 <?=$영상코드[$LangID]?></label>
									<input type="text" id="BookVideoCode" name="BookVideoCode" value="<?=$BookVideoCode?>" class="md-input label-fixed" />
									<span class="uk-input-group-addon"><a class="md-btn" href="javascript:OpenVideoPlayer(1);"><?=$영상확인[$LangID]?></a></span>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-5-10 uk-form-row">
									<span class="icheck-inline">
										<input type="radio" id="BookVideoType2_1" name="BookVideoType2" value="1" <?php if ($BookVideoType2==1) { echo "checked";}?> data-md-icheck/>
										<label for="BookVideoType2_1" class="inline-label">Youtube</label>
									</span>
									<span class="icheck-inline">
										<input type="radio" id="BookVideoType2_2" name="BookVideoType2" value="2" <?php if ($BookVideoType2==2) { echo "checked";}?> data-md-icheck/>
										<label for="BookVideoType2_2" class="inline-label">Vimeo</label>
									</span>
								</div>
								<div class="uk-width-medium-5-10 uk-input-group">
									<label for="BookVideoCode">B타입 <?=$영상코드[$LangID]?></label>
									<input type="text" id="BookVideoCode2" name="BookVideoCode2" value="<?=$BookVideoCode2?>" class="md-input label-fixed" />
									<span class="uk-input-group-addon"><a class="md-btn" href="javascript:OpenVideoPlayer(2);"><?=$영상확인[$LangID]?></a></span>
								</div>
							</div>
						</div>

						<hr>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="BookVideoMemo"><?=$동영상내용[$LangID]?></label>
									<textarea class="md-input" name="BookVideoMemo" id="BookVideoMemo" cols="30" rows="4"><?=$BookVideoMemo?></textarea>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2 uk-input-group">
									<input type="checkbox" id="BookVideoState" name="BookVideoState" value="1" <?php if ($BookVideoState==1) { echo "checked";}?> data-switchery/>
									<label for="BookVideoState" class="inline-label">사용</label>
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

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<script language="javascript">
function FormSubmit(){
	obj = document.RegForm.BookVideoName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$동영상_제목을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.BookVideoCode;
	if (obj.value==""){
		UIkit.modal.alert("<?=$동영상_코드를_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "book_video_action.php";
			document.RegForm.submit();
		}
	);
}

function OpenVideoPlayer(VT) {
	if (VT==1){
		var BookVideoCode = document.RegForm.BookVideoCode.value;
		var BookVideoTypeForm = document.RegForm.BookVideoType;
	}else{
		var BookVideoCode = document.RegForm.BookVideoCode2.value;
		var BookVideoTypeForm = document.RegForm.BookVideoType2;
	}

	if (BookVideoTypeForm[0].checked){
		BookVideoType = 1;
	} else {
		BookVideoType = 2;
	}

	if (BookVideoCode==""){
		UIkit.modal.alert("<?=$동영상_코드를_입력하세요[$LangID]?>");
	}else{
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

}
</script>

<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>