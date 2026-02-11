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

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->
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


$Sql = "select *, DATE_FORMAT(EventStartDate,'%Y-%m-%d') as EventStartDate2 , DATE_FORMAT(EventEndDate,'%Y-%m-%d') as EventEndDate2 from BoardContents where BoardContentID=:BoardContentID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardContentID', $BoardContentID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

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

$BoardContent = str_replace('&amp;', "&", $BoardContent);
$BoardContent = str_replace('&quot;', '"', $BoardContent);
$BoardContent = str_replace('&#039;', "'", $BoardContent);
$BoardContent = str_replace('&lt;', "<", $BoardContent);
$BoardContent = str_replace('&gt;', ">", $BoardContent);
$BoardContent = str_replace('<p></p>', "<br>", $BoardContent);

$BoardContent = str_replace('https:////', "https://", $BoardContent);

$EventStartDate = $Row["EventStartDate2"];
$EventEndDate = $Row["EventEndDate2"];


?>



<div id="page_content">
	<div id="page_content_inner">

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
							<li style="list-style:none;">
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c"> 
										<?=$제목[$LangID]?>
									</h3>


									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-10-10 uk-input-group">
											<label for="BoardContentSubject"><?=$제목[$LangID]?></label>
											<div class="underline_box"><?=$BoardContentSubject?></div>
										</div>
									</div>


									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-10-10">
											<label for="BoardContentWriterName"><?=$작성자[$LangID]?></label>
											<div class="underline_box"><?=$BoardContentWriterName?></div>
										</div>
									</div>


									<?if ($BoardCode=="event"){?>
									<div class="uk-grid" data-uk-grid-margin>
										
										<div class="uk-width-medium-2-10">
											<label for="EventStartDate"><?=$이벤트_시작일[$LangID]?></label>
											<?=$EventStartDate?>
										</div>
										<div class="uk-width-medium-2-10">
											<label for="EventEndDate"><?=$이벤트_종료일[$LangID]?></label>
											<?=$EventEndDate?>
										</div>
										<div class="uk-width-medium-6-10">
											
										</div>
									
									</div>
									<?}?>

									<h3 class="full_width_in_card heading_c"> 
										<?=$작성내용[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-10-10">
											<div class="border_box"><?=$BoardContent?></div>
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
										$Sql = "select * from BoardContentFiles  where BoardContentID=:BoardContentID order by BoardFileNumber asc";
										$Stmt = $DbConn->prepare($Sql);
										$Stmt->bindParam(':BoardContentID', $BoardContentID);
										$Stmt->execute();
										$Stmt->setFetchMode(PDO::FETCH_ASSOC);

										while($Row = $Stmt->fetch()) {
										?>
										<div class="uk-width-1-1">
											<label for="BoardContent"><?=$첨부파일[$LangID]?></label>
											<div class="underline_box"><?=$Row["BoardFileRealName"]?> <a href="board_file_down.php?BoardFileID=<?=$Row["BoardFileID"]?>"> &nbsp;<img src="images/filedown.png" style="vertical-align:middle;"></a></div>
										</div>
										<?php
										}
										?>
									</div>
									<?
									}
									?>

								</div>

								<div style="display:<?if ($BoardEnableComment==0){?>none<?}?>;">
									<?php
									$Sql = "select * from BoardComments where BoardContentID=:BoardContentID order by BoardCommentRegDateTime asc";
									$Stmt = $DbConn->prepare($Sql);
									$Stmt->bindParam(':BoardContentID', $BoardContentID);
									$Stmt->execute();
									$Stmt->setFetchMode(PDO::FETCH_ASSOC);

									while($Row = $Stmt->fetch()) {
									?>
									<table class="table_bbs_1">
									  <tr class="table_underline">
										<th><?=$작성자[$LangID]?></th>
										<td><?=$Row["BoardCommentWriterName"]?></td>
										<th><?=$작성일[$LangID]?></th>
										<td>
											<?=$Row["BoardCommentRegDateTime"]?> 
											&nbsp;
											<input type="button" value="삭제" onclick="DeleteComment(<?=$Row["BoardCommentID"]?>)" class="button_gray_white_small">
										</td>
									  </tr>
									  <tr class="table_underline">
										<td colspan="4" class="table_bg_gray text_left">
											<?=str_replace("\n","<br>",$Row["BoardComment"])?>
										</td>
									  </tr>
									</table>
									<?php
									}
									$Stmt = null;
									?>

								

									<?php
									$BoardCommentMemberID = $_LINK_ADMIN_ID_;
									$BoardCommentWriterName = $_ADMIN_NAME_;
									?>

									<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8" autocomplete="off">
									<input type="hidden" name="BoardCode" value="<?=$BoardCode?>">
									<input type="hidden" name="BoardContentID" value="<?=$BoardContentID?>">
									<input type="hidden" name="ListParam" value="<?=$ListParam?>">			
									<input type="hidden" name="BoardCommentMemberID" value="<?=$BoardCommentMemberID?>">
									<table class="table_bbs_1">
									<col width="20%">
									<col width="">
									<col width="20%">
									  <tr class="table_underline">
										<th><?=$작성자[$LangID]?></th>
										<td colspan="2"><input type="text" class="input_text" id="BoardCommentWriterName" name="BoardCommentWriterName"  value="<?=$BoardCommentWriterName?>"/></td>
									  </tr>
									  <tr class="table_underline">
										<th><?=$댓글[$LangID]?><span></span></th>
										<td><textarea name="BoardComment" class="textarea"></textarea></td>
										<td><input type="button" value="<?=$전송[$LangID]?>" onclick="FormSubmit()" class="button_gray_white_big reply"></td>
									  </tr>
									</table>
									</form>

								</div>					
							</li>

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
						<div class="uk-form-row button_3">
							<?if ($_LINK_ADMIN_LEVEL_ID_<=4){?>
							<a type="button" href="board_form.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>" class="md-btn md-btn-primary"><?=$수정하기[$LangID]?></a>
							<a type="button" href="javascript:DeleteContent();" class="md-btn md-btn-primary"><?=$삭제하기[$LangID]?></a>
							<?}?>
							<a href="board_list.php?<?=str_replace("^^", "&", $ListParam)?>" class="md-btn md-btn-primary"><?=$목록으로[$LangID]?></a>
						</div>
					</div>
				</div>
			</div>
		</div>




	</div>
</div>






<script language="javascript">
function DeleteContent(){
	if (confirm('<?=$삭제하시겠습니까[$LangID]?>?')){
		location.href = "board_delete.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>";
	}

}

function DeleteComment(BoardCommentID){
	if (confirm('<?=$댓글을_삭제하시겠습니까[$LangID]?>?')){
		location.href = "board_comment_delete.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>&BoardCommentID="+BoardCommentID;
	}

}


function FormSubmit(){


	obj = document.RegForm.BoardCommentWriterName;
	if (obj.value==""){
		alert('<?=$작성자를_입력하세요[$LangID]?>');
		obj.focus();
		return;
	}

	obj = document.RegForm.BoardComment;
	if (obj.value==""){
		alert('<?=$댓글을_작성하세요[$LangID]?>');
		obj.focus();
		return;
	}

	document.RegForm.action = "board_comment_action.php";
	document.RegForm.submit();

}



</script>



<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->


<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->





<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>







