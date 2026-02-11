<?php
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


//====================================================== PUSH ======================================================
function send_notification ($token, $title, $message){

	$server_key = 'AAAAfmHgpkE:APA91bFSkzP7dQCajGIoVuPYG0SbyFPKIWlNIVoSXDkvT08fX_HKgMTYuLJIPRWieilNuJfnhw7Pkk3XBZUURRAp1NhNerqKQ2xQ-GEJKAnY1rfKDvPvphCQ0R8Rp-0pLwSiUKh3HSNw';
	$str_result = "";

	//등록 하기 ==============================================
	$url = 'https://iid.googleapis.com/iid/v1:batchAdd';
	$fields['registration_tokens'] = array($token);
	$fields['to'] = '/topics/my-app';
	$headers = array(
		'Content-Type:application/json',
		'Authorization:key='.$server_key
	);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);
	curl_close($ch);
	$str_result .= "//" . $result;
	//var_dump($result);
	//등록 하기 ==============================================


	//발송 하기 ==============================================
	$payload = array(
		'to'=>'/topics/my-app',
		'priority'=>'high',
		"mutable_content"=>true,
		"notification"=>array(
			"title"=> $title,
			"body"=> $message
		)
	);

	$headers = array(
		'Authorization:key ='.$server_key,
		'Content-Type: application/json'
	);

    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );//'https://android.googleapis.com/gcm/send' 2019_08_19_sun
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $payload ) );
    $result = curl_exec($ch );
    curl_close( $ch );
	$str_result .= "//" . $result;
    //var_dump($result);
	//발송 하기 ==============================================

	//삭제 하기 ==============================================
	 //$url = "https://fcm.googleapis.com/fcm/send"; // 2019_08_19_sun
	 $url = "https://iid.googleapis.com/iid/v1:batchRemove";
	$fields['registration_tokens'] = array($token);
	$fields['to'] = '/topics/my-app';
	$headers = array(
		'Content-Type:application/json',
		'Authorization:key='.$server_key
	);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);
	curl_close($ch);
	$str_result .= "//" . $result;
	//var_dump($result);
	//삭제 하기 ==============================================

	return $str_result;
}


$Sql = "select 
			A.*
		from SendMessageLogs A 
		where 
			A.PushMessageState=1 and A.UseSendPush=1 and timestampdiff(second, A.SendMessageDateTime, now())>=0 and datediff(A.SendMessageDateTime, now())=0 and A.DeviceToken<>''
		order by A.SendMessageLogID asc";	
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);	

$DeviceTokenAndMsgs = "||";
while($Row = $Stmt->fetch()) {

	$SendMessageLogID = $Row["SendMessageLogID"];
	$DeviceType = $Row["DeviceType"];
	$DeviceToken = $Row["DeviceToken"];
	$SendTitle = $Row["SendTitle"];
	$SendMessage = $Row["SendMessage"];

	//if ($DeviceToken!="" && $DeviceType=="Android"){//안드로이드
	if ($DeviceToken!=""){//안드로이드, IOS
		
		$DeviceTokenAndMsg = $DeviceToken . "|" . $SendMessage;

		if (strpos($DeviceTokenAndMsgs, $DeviceTokenAndMsg) === false){
			$PushMessageResult = send_notification ($DeviceToken, $SendTitle, $SendMessage);
		}else{
			$PushMessageResult = "중복";
		}

		$Sql2 = "update SendMessageLogs set PushMessageState=2, PushMessageResult=:PushMessageResult, PushMessageSendDateTime=now() where SendMessageLogID=:SendMessageLogID";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':PushMessageResult', $PushMessageResult);
		$Stmt2->bindParam(':SendMessageLogID', $SendMessageLogID);
		$Stmt2->execute();
		$Stmt2 = null;

		$DeviceTokenAndMsgs = $DeviceTokenAndMsgs . $DeviceTokenAndMsg . "||";
	}

}
$Stmt = null;
//====================================================== PUSH ======================================================



//====================================================== SMS ======================================================

/*
사용자들이 문자 정보를 입력 후 함수 실행
각 정보를 파라미터로 받고, 
데이터베이스  안에 정보를 저장
OnlineSiteID : int, SendTitle : String, SendMessage : String, SmsMessagePhoneNumber : array
*/

function MunjaSend($OnlineSiteID, $SendTitle, $SendMessage, $SmsMessagePhoneNumber){

	// 필요 시, 파라미터 수정

	// curl 입력부분
	
	// 고정값
	$_api_url = 'https://message.ppurio.com/api/send_utf8_json.php';     // UTF-8 인코딩과 JSON 응답용 호출 페이지
	$SmsMessagePhoneNumber = str_replace("-","",$SmsMessagePhoneNumber); // 하이픈 허용하지 않음으로 공백으로 치환
	$userid = "mangoi0505";
	$callback = "1644-0561"; // 발신자 번호 ( 다른 번호로 할 경우, 발신자 관리에 추가해야함 )
	$callback = str_replace("-","",$callback); // 하이픈 허용하지 않음으로 공백으로 치환

	// 파라미터 정의
	$_param['userid'] = $userid;   // [필수] 뿌리오 아이
	$_param['callback'] = $callback;    // [필수] 발신번호 - 숫자만 ( 고정값으로 둬도 될듯 )
	$_param['phone'] = $SmsMessagePhoneNumber; 
	$_param['msg'] = $SendTitle."\n".$SendMessage;   // [필수] 문자내용 - 이름(names)값이 있다면 [*이름*]가 치환되서 발송됨
	$_param['subject'] = $SendTitle;          // [선택] 제목 (30byte)
	// [필수] 수신번호 - 여러명일 경우 |로 구분 '010********|010********|010********'
	//$_param['msgid'] = '1234567890'; // [필수] 발송 msgid
	//$_param['names'] = 'hong';            // [선택] 이름 - 여러명일 경우 |로 구분 '홍길동|이순신|김철수'


	$_curl = curl_init();
	curl_setopt($_curl,CURLOPT_URL,$_api_url);
	curl_setopt($_curl,CURLOPT_POST,true);
	curl_setopt($_curl,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($_curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($_curl,CURLOPT_POSTFIELDS,$_param);
	$_result = curl_exec($_curl);
	curl_close($_curl);

	//$_result = json_decode($_result);
	return $_result;
}


$Sql = "
	select 
			A.*
	from OnlineSites A 
	where A.OnlineSiteID=:OnlineSiteID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':OnlineSiteID', $OnlineSiteID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$OnlineSiteSendNumber = $Row["OnlineSiteSendNumber"];

// ( 90 byte ) - 장문이 되는 기준 byte
/*
데이터를 가져오고 
각 데이터를 뿌리오 방식에 맞게 수정
뿌리오 SMS 전송
에러 내용 체크
SmsSends 에 전송 로그를 찍을 것.
*/
$Sql = "select 
			A.*
		from SendMessageLogs A 
		where 
			A.SmsMessageState=1 and A.UseSendSms=1 and timestampdiff(second, A.SendMessageDateTime, now())>=0 and datediff(A.SendMessageDateTime, now())=0 
		order by A.SendMessageLogID asc";	
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);	


$PhoneNumberAndMsgs = "||";
while($Row = $Stmt->fetch()) {

		$SendMessageLogID = $Row["SendMessageLogID"];
		$SmsMessagePhoneNumber = $Row["SmsMessagePhoneNumber"];
		$SendTitle = $Row["SendTitle"];
		$SendMessage = $Row["SendMessage"];

		//결과값과 상관없이 먼저 보냄 처리 한다. 결과값 지연으로 중복 발송이 되는것 같음
		$Sql2 = "update SendMessageLogs set SmsMessageState=2, SmsMessageSendDateTime=now() where SendMessageLogID=:SendMessageLogID";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':SendMessageLogID', $SendMessageLogID);
		$Stmt2->execute();
		$Stmt2 = null;
		//결과값과 상관없이 먼저 보냄 처리 한다. 결과값 지연으로 중복 발송이 되는것 같음


		if ($SmsMessagePhoneNumber!=""){

			$PhoneNumberAndMsg = $SmsMessagePhoneNumber . "|" . $SendMessage;
			
			if (strpos($PhoneNumberAndMsgs, $PhoneNumberAndMsg) === false){
				$SmsMessageResult = MunjaSend(1, $SendTitle, $SendMessage, $SmsMessagePhoneNumber);
			}else{
				$SmsMessageResult = "중복";
			}

			$Sql2 = "update SendMessageLogs set SmsMessageState=2, SmsMessageResult=:SmsMessageResult, SmsMessageSendDateTime=now() where SendMessageLogID=:SendMessageLogID";
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':SmsMessageResult', $SmsMessageResult);
			$Stmt2->bindParam(':SendMessageLogID', $SendMessageLogID);
			$Stmt2->execute();
			$Stmt2 = null;

			$PhoneNumberAndMsgs = $PhoneNumberAndMsgs . $PhoneNumberAndMsg . "||";
		
		}

		
}
$Stmt = null;

//====================================================== SMS ======================================================

function MunjaSend2($OnlineSiteID, $SendTitle, $SendMessage, $SmsMessagePhoneNumber){

	// 테스트 용도
	$_api_url = 'https://message.ppurio.com/api/send_utf8_json.php';     // UTF-8 인코딩과 JSON 응답용 호출 페이지
	//$SmsMessagePhoneNumber = str_replace("-","",$OnlineSiteSendNumber); // 하이픈 허용하지 않음으로 공백으로 치환
	$callback = "01022712396"; // 발신자 번호 ( 고정값 )

	//$callback = str_replace("-","",$OnlineSiteSendNumber); // 하이픈 허용하지 않음으로 공백으로 치환

	$_param['userid'] = "mangoi0505";   // [필수] 뿌리오 아이
	$_param['callback'] = "01051804402";    // [필수] 발신번호 - 숫자만 ( 고정값으로 둬도 될듯 )
	$_param['phone'] = '01022712396'; 
	//$_param['msgid'] = '1234567890'; // [필수] 발송 msgid
	// [필수] 수신번호 - 여러명일 경우 |로 구분 '010********|010********|010********'
	$_param['msg'] = '1231ㅁㄴㅇㅁㄴㅇㅁㅁㄴㅇㅁㄴㅇ23';   // [필수] 문자내용 - 이름(names)값이 있다면 [*이름*]가 치환되서 발송됨
	//$_param['names'] = 'hong';            // [선택] 이름 - 여러명일 경우 |로 구분 '홍길동|이순신|김철수'
	$_param['subject'] = "test123";          // [선택] 제목 (30byte)


	$_curl = curl_init();
	curl_setopt($_curl,CURLOPT_URL,$_api_url);
	curl_setopt($_curl,CURLOPT_POST,true);
	curl_setopt($_curl,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($_curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($_curl,CURLOPT_POSTFIELDS,$_param);
	$_result = curl_exec($_curl);
	curl_close($_curl);

	$_result = json_decode($_result);

	print_r($_result);
	// 테스트 용도 //

}


/*
$Sql_Push = " insert into SendMessageLogs_CronLogs ( ";
	$Sql_Push .= " SendMessageLogs_CronLog_Time ";
$Sql_Push .= " ) values ( ";
	$Sql_Push .= " now() ";
$Sql_Push .= " ) ";

$Stmt_Push = $DbConn->prepare($Sql_Push);
$Stmt_Push->execute();
$Stmt_Push = null;
*/

?>