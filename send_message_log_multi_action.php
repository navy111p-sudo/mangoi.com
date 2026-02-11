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
/*
배열로 받은 id 들을 
*/
$err_num = 0;
$err_msg = "";

$SendMemberID = isset($_REQUEST["SendMemberID"]) ? $_REQUEST["SendMemberID"] : ""; // 발신자
$MemberIDs = isset($_REQUEST["MemberIDs"]) ? $_REQUEST["MemberIDs"] : ""; // 수신자

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

$SendFailedMember = "";
$CheckReSend = "|";

// MemberIDs 를 나눠 각각의 MemberID 를 가져온다
$ArrMemberID = explode("|",$MemberIDs);
for ($ii=1;$ii<=count($ArrMemberID)-2;$ii++){

	$MemberID = $ArrMemberID[$ii];

	// level 을 구하기 위한 사전 쿼리
	$Sql = "
			select 
				A.MemberLevelID 
			from Members A 
			where A.MemberID=:MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$MemberLevelID = $Row["MemberLevelID"];

	if($MemberLevelID==12 or $MemberLevelID==13) {
		$ChoiceCenter = " Members A inner join Centers B on A.CenterID=B.CenterID ";
		$ChoiceField1 = "B.CenterPhone1 ";
		$ChoiceField2 = "B.CenterPhone2 ";
	} else if($MemberLevelID==10 or $MemberLevelID==9) {
		$ChoiceCenter = " Members A inner join Branches B on A.BranchID=B.BranchID ";
		$ChoiceField1 = "B.BranchPhone1 ";
		$ChoiceField2 = "B.BranchPhone2 ";
	} else if($MemberLevelID==6 or $MemberLevelID==7) {
		$ChoiceCenter = " Members A inner join BranchGroups B on A.BranchGroupID=B.BranchGroupID ";
		$ChoiceField1 = "B.BranchGroupPhone1 ";
		$ChoiceField2 = "B.BranchGroupPhone2 ";
	} else if($MemberLevelID==5) {
		$ChoiceCenter = " Members A inner join Manager B on A.ManagerID=B.ManagerID ";
		$ChoiceField1 = "B.ManagerPhone1 ";
		$ChoiceField2 = "B.ManagerPhone2 ";
	} else if($MemberLevelID==19) {
		$ChoiceCenter = " Members A ";
		$ChoiceField1 = "A.MemberPhone1 ";
		$ChoiceField2 = "A.MemberPhone2 ";
	}

	$Sql = "select 
				A.*,
				AES_DECRYPT(UNHEX(".$ChoiceField1."),:EncryptionKey) as DecMemberPhone1,
				AES_DECRYPT(UNHEX(".$ChoiceField2."),:EncryptionKey) as DecMemberPhone2,
				ifnull((select DeviceToken from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
				ifnull((select DeviceType from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType
			from ".$ChoiceCenter." 
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
	$MemberPhone2 = $Row["DecMemberPhone2"];
	$DeviceToken = $Row["DeviceToken"];
	$DeviceType = $Row["DeviceType"];
	$MemberLevelID = $Row["MemberLevelID"];


	$SmsMessagePhoneNumber1 = $MemberPhone1;
	$SmsMessagePhoneNumber2 = $MemberPhone2;
	$KakaoMessagePhoneNumber1 = $MemberPhone1;
	$KakaoMessagePhoneNumber2 = $MemberPhone2;



	// 휴대전화 유효성 체크 ( 본인 휴대전화 )
	$MemberPhone1Check = preg_replace("/[^0-9]/", "", $MemberPhone1); // - 대쉬 제거

	if(preg_match("/^01[0-9]{8,9}$/", $MemberPhone1Check) && $UseSendSms == 1) { // 발송 대상이면서 유효성검사에 통과한 값들만
		if( strpos($CheckReSend, $MemberPhone1Check)!==false ) { // 보낸폰번호 이력에 해당 번호가 있다면 ( 기존에 보냈었다면 )
			$SmsCheckResult = 0;
		} else { // 보낸폰번호 이력에 없다면... 
			$CheckReSend = $CheckReSend . $MemberPhone1Check . "|";
			$SmsCheckResult = 1;
		}
		// $SendFailedMember = $SendFailedMember . $MemberID . ", "; 
		// 휴대폰 유효성을 통과하지 못하면 SMS 발송 대상에서 제외되는데 그 대상의 이름을 출력하기 위한 변수
	} else {
		if($SendFailedMember=="") {
			$SendFailedMember = $SendFailedMember . $MemberName; 
		} else {
			$SendFailedMember = $SendFailedMember . ", " . $MemberName;
		}
		$SmsCheckResult = 0;
	}

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
	$Stmt->bindParam(':UseSendSms', $SmsCheckResult);
	$Stmt->bindParam(':UseSendKakao', $UseSendKakao);
	$Stmt->bindParam(':DeviceToken', $DeviceToken);
	$Stmt->bindParam(':DeviceType', $DeviceType);
	$Stmt->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber1);
	$Stmt->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber1);
	$Stmt->execute();
	$Stmt = null;

	if($MemberLevelID==19) { // 학생이라면 부모님의 번호 확인 및 부모님한테도 문자 발송


		// 휴대전화 유효성 체크 ( 학부모 휴대전화 )
		$MemberPhone2Check = preg_replace("/[^0-9]/", "", $MemberPhone2); // - 대쉬 제거

		if(preg_match("/^0[0-9]{9,10}$/", $MemberPhone2Check) && $UseSendSms == 1) { // 발송 대상이면서 유효성검사에 통과한 값들만
		if( strpos($CheckReSend, $MemberPhone2Check)!==false ) { // 보낸폰번호 이력에 해당 번호가 있다면 ( 기존에 보냈었다면 )
			$SmsCheckResult = 0;
		} else { // 보낸폰번호 이력에 없다면... 
			$CheckReSend = $CheckReSend . $MemberPhone2Check . "|";
			$SmsCheckResult = 1;
		}
			// $SmsMessagePhoneNumber = $SmsMessagePhoneNumber. "|" . $MemberPhone2; // 부모님의 번호도 발송번호에 추가 ( 학생번호도 같이 넘길때 이 구문 사용 )
			// 010-****-****|010-****-**** 이런 식으로 저장 ( 뿌리오 에선 다중 문자를 이와 같이 보내게 됨 ) 
		} else {
			if($SendFailedMember=="") {
				$SendFailedMember = $SendFailedMember . $MemberName."(부)"; 
			} else {
				$SendFailedMember = $SendFailedMember . ", " . $MemberName."(부)";
			}
			$SmsCheckResult = 0;
		}

		$Sql = " insert into SendMessageLogs ( ";
			$Sql .= " MemberID, ";
			$Sql .= " SendMemberParentCheck, ";
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
			$Sql .= " 2, ";
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
		$Stmt->bindParam(':UseSendSms', $SmsCheckResult);
		$Stmt->bindParam(':UseSendKakao', $UseSendKakao);
		$Stmt->bindParam(':DeviceToken', $DeviceToken);
		$Stmt->bindParam(':DeviceType', $DeviceType);
		$Stmt->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber2);
		$Stmt->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber2);
		$Stmt->execute();
		$Stmt = null;
	}
}

?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>

var Str = "연락처가 잘못 기재되어 아래 대상들은<br>문자발송에서 제외되었습니다.";

<?if($SendFailedMember!="") { ?>
	// 번호 유효성에 걸린 애들이 있다면
	$.confirm({
		title: '',
		content: Str + "<br><br><?=$SendFailedMember?>",
		buttons: {
			닫기: function () {
				//parent.$.fn.colorbox.close();
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
			}
		}
	});
<? } else { ?>
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
<? } ?>

</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

