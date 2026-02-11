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
<link rel="stylesheet" type="text/css" href="css/common.css" />
</head>
</body>
<?

$err_num = 0;
$err_msg = "";

$SendMessageLogID = isset($_REQUEST["SendMessageLogID"]) ? $_REQUEST["SendMessageLogID"] : "";
$SendMemberID = isset($_REQUEST["SendMemberID"]) ? $_REQUEST["SendMemberID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$DeviceToken = isset($_REQUEST["DeviceToken"]) ? $_REQUEST["DeviceToken"] : "";
$DeviceType = isset($_REQUEST["DeviceType"]) ? $_REQUEST["DeviceType"] : "";
$SmsMessagePhoneNumber = isset($_REQUEST["SmsMessagePhoneNumber"]) ? $_REQUEST["SmsMessagePhoneNumber"] : "";
$KakaoMessagePhoneNumber = isset($_REQUEST["KakaoMessagePhoneNumber"]) ? $_REQUEST["KakaoMessagePhoneNumber"] : "";

$SendTitle = isset($_REQUEST["SendTitle"]) ? $_REQUEST["SendTitle"] : "";
$SendMessage = isset($_REQUEST["SendMessage"]) ? $_REQUEST["SendMessage"] : "";
$SendMemo = isset($_REQUEST["SendMemo"]) ? $_REQUEST["SendMemo"] : "";
$SendMessageDateTime = isset($_REQUEST["SendMessageDateTime"]) ? $_REQUEST["SendMessageDateTime"] : "";

$UseSendPush = isset($_REQUEST["UseSendPush"]) ? $_REQUEST["UseSendPush"] : "";
$UseSendSms = isset($_REQUEST["UseSendSms"]) ? $_REQUEST["UseSendSms"] : "";
$UseSendKakao = isset($_REQUEST["UseSendKakao"]) ? $_REQUEST["UseSendKakao"] : "";

if ($UseSendPush!="1"){
	$UseSendPush = "0";
}
if ($UseSendSms!="1"){
	$UseSendSms = "0";
}
if ($UseSendKakao!="1"){
	$UseSendKakao = "0";
}


if ($SendMessageLogID==""){

	$Sql = " insert into SendMessageLogs ( ";
		$Sql .= " MemberID, ";
		$Sql .= " SendMemberID, ";
		$Sql .= " SendTitle, ";
		$Sql .= " SendMessage, ";
		$Sql .= " SendMemo, ";
		$Sql .= " SendMessageDateTime, ";
		$Sql .= " SendMessageLogRegDateTime, ";
		$Sql .= " SendMessageLogModiDateTime, ";
		$Sql .= " UseSendPush, ";
		$Sql .= " UseSendSms, ";
		$Sql .= " UseSendKakao, ";
		$Sql .= " DeviceToken, ";
		$Sql .= " DeviceType, ";
		$Sql .= " PushMessageState, ";
		$Sql .= " SmsMessagePhoneNumber, ";
		$Sql .= " SmsMessageState, ";
		$Sql .= " KakaoMessagePhoneNumber, ";
		$Sql .= " KakaoMessageState ";
	$Sql .= " ) values ( ";
		$Sql .= " :MemberID, ";
		$Sql .= " :SendMemberID, ";
		$Sql .= " :SendTitle, ";
		$Sql .= " :SendMessage, ";
		$Sql .= " :SendMemo, ";
		$Sql .= " :SendMessageDateTime, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :UseSendPush, ";
		$Sql .= " :UseSendSms, ";
		$Sql .= " :UseSendKakao, ";
		$Sql .= " :DeviceToken, ";
		$Sql .= " :DeviceType, ";
		$Sql .= " 1, ";
		$Sql .= " :SmsMessagePhoneNumber, ";
		$Sql .= " 1, ";
		$Sql .= " :KakaoMessagePhoneNumber, ";
		$Sql .= " 1 ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':SendMemberID', $SendMemberID);
	$Stmt->bindParam(':SendTitle', $SendTitle);
	$Stmt->bindParam(':SendMessage', $SendMessage);
	$Stmt->bindParam(':SendMemo', $SendMemo);
	$Stmt->bindParam(':SendMessageDateTime', $SendMessageDateTime);
	$Stmt->bindParam(':UseSendPush', $UseSendPush);
	$Stmt->bindParam(':UseSendSms', $UseSendSms);
	$Stmt->bindParam(':UseSendKakao', $UseSendKakao);
	$Stmt->bindParam(':DeviceToken', $DeviceToken);
	$Stmt->bindParam(':DeviceType', $DeviceType);
	$Stmt->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
	$Stmt->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update SendMessageLogs set ";
		$Sql .= " SendTitle = :SendTitle, ";
		$Sql .= " SendMessage = :SendMessage, ";
		$Sql .= " SendMemo = :SendMemo, ";
		$Sql .= " SendMessageDateTime = :SendMessageDateTime, ";
		$Sql .= " UseSendPush = :UseSendPush, ";
		$Sql .= " UseSendSms = :UseSendSms, ";
		$Sql .= " UseSendKakao = :UseSendKakao, ";
		$Sql .= " DeviceToken = :DeviceToken, ";
		$Sql .= " DeviceType = :DeviceType, ";
		$Sql .= " SmsMessagePhoneNumber = :SmsMessagePhoneNumber, ";
		$Sql .= " KakaoMessagePhoneNumber = :KakaoMessagePhoneNumber ";
	$Sql .= " where SendMessageLogID = :SendMessageLogID and timestampdiff(second, SendMessageDateTime, now())<=-20 ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SendTitle', $SendTitle);
	$Stmt->bindParam(':SendMessage', $SendMessage);
	$Stmt->bindParam(':SendMemo', $SendMemo);
	$Stmt->bindParam(':SendMessageDateTime', $SendMessageDateTime);
	$Stmt->bindParam(':UseSendPush', $UseSendPush);
	$Stmt->bindParam(':UseSendSms', $UseSendSms);
	$Stmt->bindParam(':UseSendKakao', $UseSendKakao);
	$Stmt->bindParam(':DeviceToken', $DeviceToken);
	$Stmt->bindParam(':DeviceType', $DeviceType);
	$Stmt->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
	$Stmt->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
	$Stmt->bindParam(':SendMessageLogID', $SendMessageLogID);
	$Stmt->execute();
	$Stmt = null;

}

?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>
<?if ($SendMessageLogID==""){?>
$.confirm({
	title: '',
	content: "메시지전송 내용을 저장했습니다.<br>메시지전송내역에서 확인하실 수 있습니다.",
	buttons: {
		닫기: function () {
			parent.$.fn.colorbox.close();
		},
		메시지전송내역이동: function () {
			parent.location.href = "send_message_log_list.php";
		}
	}
});
<?}else{?>
	parent.$.fn.colorbox.close();
<?}?>
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

