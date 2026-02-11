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
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$SendMessageLogID = isset($_REQUEST["SendMessageLogID"]) ? $_REQUEST["SendMessageLogID"] : "";


if ($SendMessageLogID!=""){

	$Sql = "
			select 
					A.*,
					B.MemberName,
					timestampdiff(second, A.SendMessageDateTime, now()) as SendTimeDiff
			from SendMessageLogs A 
				inner join Members B on A.MemberID=B.MemberID 
			where A.SendMessageLogID=:SendMessageLogID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SendMessageLogID', $SendMessageLogID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberID = $Row["MemberID"];
	$SendMemberID = $Row["SendMemberID"];
	$SendTitle = $Row["SendTitle"];
	$SendMessage = $Row["SendMessage"];
	$SendMemo = $Row["SendMemo"];
	$SendMessageDateTime = $Row["SendMessageDateTime"];
	$SendMessageLogRegDateTime = $Row["SendMessageLogRegDateTime"];
	$SendMessageLogModiDateTime = $Row["SendMessageLogModiDateTime"];

	$UseSendPush = $Row["UseSendPush"];
	$UseSendSms = $Row["UseSendSms"];
	$UseSendKakao = $Row["UseSendKakao"];

	$DeviceToken = $Row["DeviceToken"];
	$DeviceType = $Row["DeviceType"];
	$PushMessageResult = $Row["PushMessageResult"];
	$PushMessageState = $Row["PushMessageState"];
	$PushMessageSendDateTime = $Row["PushMessageSendDateTime"];

	$SmsMessagePhoneNumber = $Row["SmsMessagePhoneNumber"];
	$SmsMessageResult = $Row["SmsMessageResult"];
	$SmsMessageState = $Row["SmsMessageState"];
	$SmsMessageSendDateTime = $Row["SmsMessageSendDateTime"];

	$KakaoMessagePhoneNumber = $Row["KakaoMessagePhoneNumber"];
	$KakaoMessageResult = $Row["KakaoMessageResult"];
	$KakaoMessageState = $Row["KakaoMessageState"];
	$KakaoMessageSendDateTime = $Row["KakaoMessageSendDateTime"];

	$MemberName = $Row["MemberName"];
	$SendTimeDiff = $Row["SendTimeDiff"];

}else{

	$Sql = "
			select 
				A.*,
				AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
				ifnull((select DeviceToken from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
				ifnull((select DeviceType from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType
			from Members A 
			where A.MemberID=:MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$MemberName = $Row["MemberName"];
	$MemberPhone1 = $Row["DecMemberPhone1"];	
	$DeviceToken = $Row["DeviceToken"];
	$DeviceType = $Row["DeviceType"];	
	
	$SendMemberID = $_LINK_ADMIN_ID_;
	$SendTitle = "";
	$SendMessage = "";
	$SendMemo = "";
	$SendMessageDateTime = date("Y-m-d H:i:s");

	$UseSendPush = 1;
	$UseSendSms = 1;
	$UseSendKakao = 1;

	$SmsMessagePhoneNumber = $MemberPhone1;
	$KakaoMessagePhoneNumber = $MemberPhone1;
}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="SendMessageLogID" value="<?=$SendMessageLogID?>">
		<input type="hidden" name="SendMemberID" value="<?=$SendMemberID?>">
		<input type="hidden" name="MemberID" value="<?=$MemberID?>">
		<input type="hidden" name="DeviceToken" value="<?=$DeviceToken?>">
		<input type="hidden" name="DeviceType" value="<?=$DeviceType?>">
		<input type="hidden" name="SmsMessagePhoneNumber" value="<?=$SmsMessagePhoneNumber?>">
		<input type="hidden" name="KakaoMessagePhoneNumber" value="<?=$KakaoMessagePhoneNumber?>">

		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$MemberName?></span><span class="sub-heading" id="user_edit_position"><?=$메시지_전송_관리[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						

					
						<div class="uk-margin-top" style="margin-top:20px;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-6-10">
									<label for="SendTitle"><?=$메시지_제목[$LangID]?></label>
									<input type="text" id="SendTitle" name="SendTitle" value="<?=$SendTitle?>" class="md-input label-fixed"/>
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
									<label for="UseSendPush" class="inline-label" style="margin-right:20px;"><?=$PUSH전송[$LangID]?> (<?=$DeviceType?>)</label> 
								</div>
								<div class="uk-width-medium-1-1">
									<input type="checkbox" name="UseSendSms" id="UseSendSms" value="1" <?if ($UseSendSms==1){?>checked<?}?> data-md-icheck/>
									<label for="UseSendSms" class="inline-label" style="margin-right:20px;"><?=$SMS전송[$LangID]?> (<?=$SmsMessagePhoneNumber?>)</label> 
								</div>
								<div class="uk-width-medium-1-1" style="display:none;">
									<input type="checkbox" name="UseSendKakao" id="UseSendKakao" value="1" <?if ($UseSendKakao==1){?>checked<?}?> data-md-icheck/>
									<label for="UseSendKakao" class="inline-label" style="margin-right:20px;"><?=$카카오전송[$LangID]?> (<?=$KakaoMessagePhoneNumber?>)</label> 
								</div>
							</div>
						</div>

						<hr>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="SendMessage"><?=$메시지_내용[$LangID]?></label>
									<textarea class="md-input" name="SendMessage" id="SendMessage" cols="30" rows="4"><?=$SendMessage?></textarea>
								</div>
							</div>
						</div>

						

						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
							<?if ($SendMessageLogID=="" || $SendTimeDiff<=-20){?>
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?if ($SendMessageLogID!=""){?><?=$수정하기[$LangID]?><?}else{?><?=$전송하기[$LangID]?><?}?></a>
							<?}else{?>
							<a type="button" href="javascript:parent.$.fn.colorbox.close();" class="md-btn md-btn-primary">닫기</a>
							<?}?>
						</div>

						<?if ($SendMessageLogID!=""){?>
						<div style="text-align:center;padding-top:30px;">수정완료 기준 발송예약 20초 전까지만 수정 가능합니다.</div>
						<?}?>

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
			document.RegForm.action = "send_message_log_action.php";
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