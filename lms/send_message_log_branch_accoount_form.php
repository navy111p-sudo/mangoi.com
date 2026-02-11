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
<link rel="stylesheet" href="bower_components/kendo-ui/styles/kendo.common-material.min.css"/>
<link rel="stylesheet" href="bower_components/kendo-ui/styles/kendo.material.min.css" id="kendoCSS"/>
<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->


</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<?php
$MemberIDs = isset($_REQUEST["MemberIDs"]) ? $_REQUEST["MemberIDs"] : "";


$SendMemberID = $_LINK_ADMIN_ID_;
$SendTitle = "";
$SendMessage = "";
$SendMemo = "";
$SendMessageDateTime = date("Y-m-d H:i:s");

$UseSendPush = 1;
$UseSendSms = 1;
$UseSendKakao = 1;


?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="SendMessageLogID" value="<?=$SendMessageLogID?>">
		<input type="hidden" name="SendMemberID" value="<?=$SendMemberID?>">
		<input type="hidden" name="MemberIDs" value="<?=$MemberIDs?>">

		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"></span><span class="sub-heading" id="user_edit_position"><?=$메시지_전송_관리[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						

					
						<div class="uk-margin-top" style="margin-top:20px;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-6-10">
									<label for="SendTitle"><?=$메시지_제목[$LangID]?></label>
									<input type="text" id="SendTitle" name="SendTitle" value="망고아이 미수금 안내" class="md-input label-fixed" readonly/>
								</div>
								<div class="uk-width-medium-4-10">
									<input type="text" id="SendMessageDateTime" name="SendMessageDateTime" value="<?=$SendMessageDateTime?>"/> 
								</div>
							</div>
						</div>

						<div class="uk-margin-top" style="margin-top:20px;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<input type="checkbox" name="UseSendPush" id="UseSendPush" value="1" <?if ($UseSendPush==1){?>checked<?}?> data-md-icheck/>
									<label for="UseSendPush" class="inline-label" style="margin-right:20px;">PUSH</label> 
								</div>
								<div class="uk-width-medium-1-1">
									<input type="checkbox" name="UseSendSms" id="UseSendSms" value="1" <?if ($UseSendSms==1){?>checked<?}?> data-md-icheck/>
									<label for="UseSendSms" class="inline-label" style="margin-right:20px;">SMS</label> 
								</div>
								<div class="uk-width-medium-1-1" style="display:none;">
									<input type="checkbox" name="UseSendKakao" id="UseSendKakao" value="1" <?if ($UseSendKakao==1){?>checked<?}?> data-md-icheck/>
									<label for="UseSendKakao" class="inline-label" style="margin-right:20px;">카카오</label> 
								</div>
							</div>
						</div>

						<hr>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="SendMessage"><?=$메시지_내용[$LangID]?></label>
									<textarea class="md-input" name="SendMessage" id="SendMessage" cols="30" rows="4" readonly>{{지사명}} 는 현재 {{미수금금액}}의 미수금이 있습니다. 확인 바랍니다.</textarea>
								</div>
							</div>
						</div>

						

						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?=$전송하기[$LangID]?></a>
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
<script src="assets/js/kendoui_custom.min.js"></script>
<script src="assets/js/pages/kendoui.min.js"></script>

<script>
$("#SendMessageDateTime").kendoDateTimePicker({
	format: "yyyy-MM-dd HH:mm:ss"
});
</script>

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<script language="javascript">

function FormSubmit(){


	obj = document.RegForm.SendTitle;
	if (obj.value==""){
		UIkit.modal.alert("메시지 제목을 입력하세요.");
		obj.focus();
		return;
	}


	obj = document.RegForm.SendMessage;
	if (obj.value==""){
		UIkit.modal.alert("메시지 내용을 입력하세요.");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "send_message_log_branch_accoount_action.php";
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