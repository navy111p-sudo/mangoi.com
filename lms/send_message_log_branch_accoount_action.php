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

$SendMemberID = isset($_REQUEST["SendMemberID"]) ? $_REQUEST["SendMemberID"] : "";
$MemberIDs = isset($_REQUEST["MemberIDs"]) ? $_REQUEST["MemberIDs"] : "";

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

$TempSendMessage = $SendMessage;

$ArrMemberID = explode("|",$MemberIDs);
for ($ii=1;$ii<=count($ArrMemberID)-2;$ii++){

	$MemberID = $ArrMemberID[$ii];


	$Sql2 = "select A.BranchID, B.BranchName from Members A inner join Branches B on A.BranchID=B.BranchID where A.MemberID=:MemberID ";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':MemberID', $MemberID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	$Stmt2 = null;
	$AccBranchID = $Row2["BranchID"];
	$AccBranchName = $Row2["BranchName"];
	
	
	$Sql2 = "select sum(BranchAccountPrice) as BranchAccountPrice from BranchAccounts where BranchID=:BranchID ";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':BranchID', $AccBranchID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	$Stmt2 = null;
	$BranchAccountPrice = $Row2["BranchAccountPrice"];


	$SendMessage = str_replace("{{지사명}}", $AccBranchName, $TempSendMessage);
	$SendMessage = str_replace("{{미수금금액}}", $BranchAccountPrice, $SendMessage);

	$Sql = "
			select 
				A.*,
				AES_DECRYPT(UNHEX(B.BranchPhone2),:EncryptionKey) as DecMemberPhone1,
				ifnull((select DeviceToken from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
				ifnull((select DeviceType from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType
			from Members A 
				inner join Branches B on A.BranchID=B.BranchID
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

	$SmsMessagePhoneNumber = $MemberPhone1;
	$KakaoMessagePhoneNumber = $MemberPhone1;

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

}

?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>
$.confirm({
	title: '',
	content: "메시지전송 내용을 저장했습니다.<br>메시지전송내역에서 확인하실 수 있습니다.",
	buttons: {
		닫기: function () {
			parent.$.fn.colorbox.close();
		}
	}
});
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

