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
$DeviceToken = "";
$DeviceType = "";


$Sql3 = "
      SELECT 
        AES_DECRYPT(UNHEX(A.MemberPhone1), :EncryptionKey) as DecMemberPhone1, 
        AES_DECRYPT(UNHEX(A.MemberPhone2), :EncryptionKey) as DecMemberPhone2, 
        A.*, 
        B.CenterID, 
        D.BranchGroupID 
      FROM Members A 
        inner join Centers B on A.CenterID=B.CenterID 
        inner join Branches C on B.BranchID=C.BranchID  
        inner join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
       WHERE 
        (
         B.CenterPayType=2 
         OR 
         ( B.CenterPayType=1 AND A.MemberPayType=1 ) 
        ) 
        AND 
        B.CenterState=1 
        AND 
        A.MemberState=1 
        AND 
        A.MemberLevelID=19 
        AND 
        A.MemberID IN ( SELECT MemberID from ClassOrders where datediff(ClassOrderEndDate, now())=1 and ClassOrderState=1 and ClassProgress=11 AND ClassProductID=1 ) 

";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);

while($Row3 = $Stmt3->fetch()) {

		$SmsMessagePhoneNumber=$Row3["DecMemberPhone1"];
		$SmsMessagePhoneNumber2=$Row3["DecMemberPhone2"];
		$KakaoMessagePhoneNumber=$Row3["DecMemberPhone1"];
		$KakaoMessagePhoneNumber2=$Row3["DecMemberPhone2"];

		$MemberName = $Row3["MemberName"];
		$CenterID = $Row3["CenterID"];
		$BranchGroupID = $Row3["BranchGroupID"];

		if($CenterID == 121) { // 잉글리시텔 대리점의 경우
			$StrDomain = "[EnglishTell 화상영어]";
		} else if($BranchGroupID==18) { // SLP 대표지사 ( SLP 가 아닌 "김재익 지사, 망고아이 기본지사 뺄것 )
			$StrDomain = "[SLP 망고아이]";
		} else {
			$StrDomain = "[망고아이]";
		}


		$NowDate = date("Y-m-d");
		$MsgDate =  date('Y-m-d', strtotime($NowDate. ' +1 day'));

		$SendMemberID = 0;
		$MemberID=$Row3["MemberID"];
		$SendTitle="종료알림";
		$SendMessage="$StrDomain $MemberName 님의 수업이 $MsgDate 일자로 종료됩니다.\n수업 연장을 원하시는 경우, 수업 연장결제를 해주세요. ";
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

		$MemberPhone2Check = preg_replace("/[^0-9]/", "", $SmsMessagePhoneNumber2); // - 대쉬 제거
		if(preg_match("/^01[0-9]{8,9}$/", $MemberPhone2Check) && $UseSendSms == 1) { // 발송 대상이면서 유효성검사에 통과한 값들만
			if( strpos($CheckReSend, $SmsMessagePhoneNumber2)!==false ) { // 보낸폰번호 이력에 해당 번호가 있다면 ( 기존에 보냈었다면 )
				$SmsCheckResult = 0;
			} else { // 보낸폰번호 이력에 없다면... 
				$CheckReSend = $CheckReSend . $SmsMessagePhoneNumber2 . "|";
				$SmsCheckResult = 1;
			}
		} else {
			$SmsCheckResult = 0;
		}

		// 학부모 휴대전화로 발송
		$Sql_Push = " insert into SendMessageLogs ( ";
			$Sql_Push .= " MemberID, ";
			$Sql_Push .= " SendMemberParentCheck, ";
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
			$Sql_Push .= " 2, ";
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
		$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber2);
		$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber2);
		$Stmt_Push->execute();
		$Stmt_Push = null;
}

?>
