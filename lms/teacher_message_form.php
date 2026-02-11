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
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$TeacherMessageType = 1;


$Sql = "
		select 
				A.*,
				B.MemberID
		from Teachers A 
			inner join Members B on A.TeacherID=B.TeacherID and B.MemberLevelID=15 
		where A.TeacherID=:TeacherID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherID', $TeacherID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TeacherName = $Row["TeacherName"];
$MemberID = $Row["MemberID"];


$TeacherMessageText = "";
?>


<div id="page_content">
	<div id="page_content_inner">
		
		<ul id="user_edit_tabs" class="uk-tab">
			<li style="background-color:#1D76CE;border-radius:5px 5px 0px 0px;"><a href="teacher_message_form.php?TeacherID=<?=$TeacherID?>" style="color:#ffffff;"><?=$메시지전송[$LangID]?></a></li>
			<li style="background-color:#ffffff;border-radius:5px 5px 0px 0px;"><a href="teacher_message_list_mini.php?TeacherID=<?=$TeacherID?>" style="color:#1D76CE;"><?=$전송목록[$LangID]?></a></li>
		</ul>

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="MemberID" value="<?=$MemberID?>">
		<input type="hidden" name="TeacherMessageType" value="<?=$TeacherMessageType?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$TeacherName?></span><span class="sub-heading" id="user_edit_position"><?=$메시지_전달[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="TeacherMessageText"><?=$요청내용[$LangID]?></label>
									<textarea class="md-input" name="TeacherMessageText" id="TeacherMessageText" cols="30" rows="8"><?=$TeacherMessageText?></textarea>
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


	obj = document.RegForm.TeacherMessageText;
	if (obj.value==""){
		UIkit.modal.alert("<?=$내용을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}
	

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "teacher_message_action.php";
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