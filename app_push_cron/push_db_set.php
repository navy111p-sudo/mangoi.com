<?
//====================================================== DB ======================================================
$DbHost = "localhost";
$DbName = "mangoi";
$DbUser = "mangoi";
$DbPass = "mi!@#2019";

try {
	$DbConn = new PDO("mysql:host=$DbHost;dbname=$DbName;charset=utf8", $DbUser, $DbPass);
	$DbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
	echo "Connection failed: " . $e->getMessage();
}
//====================================================== DB ======================================================
//=======================================================================================================
$EncryptionKey = md5("kr.ahsol");//절대 변경 불가(변경되면 회원정보 복구 불가)
//=======================================================================================================



// test ======================================================
$Sql2 = "update TestTable set TestTableText=now() where TestTableID=1";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->execute();
$Stmt2 = null;
// test ======================================================

$CheckReSend = "|";

$Sql2 = "select 
				A.*, 
				AES_DECRYPT(UNHEX(B.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
				ifnull((select DeviceToken from DeviceTokens where MemberID=B.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
				ifnull((select DeviceType from DeviceTokens where MemberID=B.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType,
				C.CenterID,
				E.BranchGroupID
		from Classes A 
			inner join Members B on A.MemberID=B.MemberID 
			inner join Centers C on B.CenterID=C.CenterID 
			inner join Branches D on D.BranchID=C.BranchID 
			inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
		where 1=1 and timestampdiff(second, A.StartDateTime, now())>=300 and timestampdiff(second, A.StartDateTime, now())<310
";//A.MemberID=87 and

$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);


while($Row2 = $Stmt2->fetch()) {

		$DeviceToken=$Row2["DeviceToken"];
		$DeviceType=$Row2["DeviceType"];
		$SmsMessagePhoneNumber=$Row2["DecMemberPhone1"];
		$KakaoMessagePhoneNumber=$Row2["DecMemberPhone1"];
		$CenterID=$Row2["CenterID"];
		$BranchGroupID=$Row2["BranchGroupID"];

		if($CenterID == 121) { // 잉글리시텔 대리점의 경우
			$StrDomain = "[EnglishTell 화상영어]";
		} else if($BranchGroupID==18) { // SLP 대표지사 ( SLP 가 아닌 "김재익 지사, 망고아이 기본지사 뺄것 )
			$StrDomain = "[SLP 망고아이]";
		} else {
			$StrDomain = "[망고아이]";
		}

		$SendMemberID = 0;
		$MemberID=$Row2["MemberID"];
		$SendTitle="수업알림";
		$SendMessage="$DomainSiteID 수업이 약 5분 후 시작됩니다.";
		$SendMemo="";
		$SendMessageDateTime=date("Y-m-d H:i:s");
		$UseSendPush=1;
		$UseSendSms=1;
		$UseSendKakao=1;

		$MemberPhone1Check = preg_replace("/[^0-9]/", "", $SmsMessagePhoneNumber); // - 대쉬 제거
		if(preg_match("/^01[0-9]{8,9}$/", $MemberPhone1Check) && $UseSendSms == 1) { // 발송 대상이면서 유효성검사에 통과한 값들만
			if( strpos($CheckReSend, $SmsMessagePhoneNumber)!==false ) { // 보낸폰번호 이력에 해당 번호가 있다면 ( 기존에 보냈었다면 )
				$SmsCheckResult = 0;
			} else { // 보낸폰번호 이력에 없다면... 
				$CheckReSend = $CheckReSend . $SmsMessagePhoneNumber . "|";
				$SmsCheckResult = 1;
			}
		} else {
			$SmsCheckResult = 0;
		}

		$Sql_Push = " insert into SendMessageLogs ( ";
			$Sql_Push .= " MemberID, ";
			$Sql_Push .= " SendMemberID, ";
			$Sql_Push .= " SendTitle, ";
			$Sql_Push .= " SendMessage, ";
			$Sql_Push .= " SendMemo, ";
			$Sql_Push .= " SendMessageDateTime, ";
			$Sql_Push .= " SendMessageLogRegDateTime, ";
			$Sql_Push .= " SendMessageLogModiDateTime, ";
			$Sql_Push .= " UseSendPush, ";
			$Sql_Push .= " UseSendSms, ";
			$Sql_Push .= " UseSendKakao, ";
			$Sql_Push .= " DeviceToken, ";
			$Sql_Push .= " DeviceType, ";
			$Sql_Push .= " PushMessageState, ";
			$Sql_Push .= " SmsMessagePhoneNumber, ";
			$Sql_Push .= " SmsMessageState, ";
			$Sql_Push .= " KakaoMessagePhoneNumber, ";
			$Sql_Push .= " KakaoMessageState ";
		$Sql_Push .= " ) values ( ";
			$Sql_Push .= " :MemberID, ";
			$Sql_Push .= " :SendMemberID, ";
			$Sql_Push .= " :SendTitle, ";
			$Sql_Push .= " :SendMessage, ";
			$Sql_Push .= " :SendMemo, ";
			$Sql_Push .= " :SendMessageDateTime, ";
			$Sql_Push .= " now(), ";
			$Sql_Push .= " now(), ";
			$Sql_Push .= " :UseSendPush, ";
			$Sql_Push .= " :UseSendSms, ";
			$Sql_Push .= " :UseSendKakao, ";
			$Sql_Push .= " :DeviceToken, ";
			$Sql_Push .= " :DeviceType, ";
			$Sql_Push .= " 1, ";
			$Sql_Push .= " :SmsMessagePhoneNumber, ";
			$Sql_Push .= " 1, ";
			$Sql_Push .= " :KakaoMessagePhoneNumber, ";
			$Sql_Push .= " 1 ";
		$Sql_Push .= " ) ";

		$Stmt_Push = $DbConn->prepare($Sql_Push);
		$Stmt_Push->bindParam(':MemberID', $MemberID);
		$Stmt_Push->bindParam(':SendMemberID', $SendMemberID);
		$Stmt_Push->bindParam(':SendTitle', $SendTitle);
		$Stmt_Push->bindParam(':SendMessage', $SendMessage);
		$Stmt_Push->bindParam(':SendMemo', $SendMemo);
		$Stmt_Push->bindParam(':SendMessageDateTime', $SendMessageDateTime);
		$Stmt_Push->bindParam(':UseSendPush', $UseSendPush);
		$Stmt_Push->bindParam(':UseSendSms', $SmsCheckResult);
		$Stmt_Push->bindParam(':UseSendKakao', $UseSendKakao);
		$Stmt_Push->bindParam(':DeviceToken', $DeviceToken);
		$Stmt_Push->bindParam(':DeviceType', $DeviceType);
		$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
		$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
		$Stmt_Push->execute();
		$Stmt_Push = null;

}

?>
