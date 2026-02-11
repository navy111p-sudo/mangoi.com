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
			A.PushMessageState=1 and A.UseSendPush=1 and timestampdiff(second, A.SendMessageDateTime, now())>=0 
		order by A.SendMessageLogID asc";	
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);	

while($Row = $Stmt->fetch()) {

	$SendMessageLogID = $Row["SendMessageLogID"];
	$DeviceType = $Row["DeviceType"];
	$DeviceToken = $Row["DeviceToken"];
	$SendTitle = $Row["SendTitle"];
	$SendMessage = $Row["SendMessage"];

	if ($DeviceToken!="" && $DeviceType=="Android"){//안드로이드
		
		$PushMessageResult = send_notification ($DeviceToken, $SendTitle, $SendMessage);

		$Sql2 = "update SendMessageLogs set PushMessageState=2, PushMessageResult=:PushMessageResult, PushMessageSendDateTime=now() where SendMessageLogID=:SendMessageLogID";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':PushMessageResult', $PushMessageResult);
		$Stmt2->bindParam(':SendMessageLogID', $SendMessageLogID);
		$Stmt2->execute();
		$Stmt2 = null;
	}

}
$Stmt = null;
//====================================================== PUSH ======================================================



//====================================================== SMS ======================================================

function MunjaSend($OnlineSiteID, $SendTitle, $SendMessage, $SmsMessagePhoneNumber, $OnlineSiteSmsID, $OnlineSiteSmsPW, $OnlineSiteSendNumber){
	

			$host = "www.munja114.co.kr";
			$id = $OnlineSiteSmsID; // 문자114 아이디 입력
			$pass = $OnlineSiteSmsPW; // 문자114 비밀번호 입력
			$callback = str_replace("-","",$OnlineSiteSendNumber);


			$contents = "";
			$etc1 = "";
			$etc2 = "";
			$name = "";

			$SmsReserve=0;
			$SmsSendDateTime = date("Y-m-d H:i:s");
			$SmsReceiveNumber = str_replace("-","",$SmsMessagePhoneNumber);


			if (mb_strwidth ( $SendMessage,"UTF-8" )>90){
				$mtype = "lms";
			}else{
				$mtype = "";
			}	
			// 문자 메시지 검증 ========================================

			
			
			$param = "remote_id=".$id;
			$param .= "&remote_pass=".$pass;
			$param .= "&remote_reserve=".$SmsReserve;
			$param .= "&remote_reservetime=".$SmsSendDateTime;
			$param .= "&remote_name=".$name;
			$param .= "&remote_phone=".$SmsReceiveNumber;
			$param .= "&remote_callback=".$callback;
			$param .= "&remote_msg=".$SendMessage;
			$param .= "&remote_contents=".$contents;
			$param .= "&remote_etc1=".$etc1;
			$param .= "&remote_etc2=".$etc2;
			if ($mtype == "lms") {
				$path = "/Remote/RemoteMms.html";
			} else {
				$path = "/Remote/RemoteSms.html";
			}
			$fp = @fsockopen($host,80,$errno,$errstr,30);
			$return = "";
			if (!$fp) {
				echo $errstr."(".$errno.")";
			} else {
				fputs($fp, "POST ".$path." HTTP/1.1\r\n");
				fputs($fp, "Host: ".$host."\r\n");
				fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
				fputs($fp, "Content-length: ".strlen($param)."\r\n");
				fputs($fp, "Connection: close\r\n\r\n");
				fputs($fp, $param."\r\n\r\n");
				while(!feof($fp)) $return .= fgets($fp,4096);
			}
			fclose ($fp);
			$_temp_array = explode("\r\n\r\n", $return);
			$_temp_array2 = explode("\r\n", $_temp_array[1]);
			if (sizeof($_temp_array2) > 1) {
				$return_string = $_temp_array2[1];
			} else {
				$return_string = $_temp_array2[0];
			}
			return $return_string;

	

}


$OnlineSiteID = 1;
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

$OnlineSiteSmsID = $Row["OnlineSiteSmsID"];
$OnlineSiteSmsPW = $Row["OnlineSiteSmsPW"];
$OnlineSiteSendNumber = $Row["OnlineSiteSendNumber"];


$Sql = "select 
			A.*
		from SendMessageLogs A 
		where 
			A.SmsMessageState=1 and A.UseSendSms=1 and timestampdiff(second, A.SendMessageDateTime, now())>=0 
		order by A.SendMessageLogID asc";	
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);	

while($Row = $Stmt->fetch()) {

	/*
		$SendMessageLogID = $Row["SendMessageLogID"];
		$SmsMessagePhoneNumber = $Row["SmsMessagePhoneNumber"];
		$SendTitle = $Row["SendTitle"];
		$SendMessage = $Row["SendMessage"];

		if ($SmsMessagePhoneNumber!=""){
			
			$SmsMessageResult = MunjaSend(1, $SendTitle, $SendMessage, $SmsMessagePhoneNumber, $OnlineSiteSmsID, $OnlineSiteSmsPW, $OnlineSiteSendNumber);

			$Sql2 = "update SendMessageLogs set SmsMessageState=2, SmsMessageResult=:SmsMessageResult, SmsMessageSendDateTime=now() where SendMessageLogID=:SendMessageLogID";
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':SmsMessageResult', $SmsMessageResult);
			$Stmt2->bindParam(':SendMessageLogID', $SendMessageLogID);
			$Stmt2->execute();
			$Stmt2 = null;
		
		}
	*/

}
$Stmt = null;

//====================================================== SMS ======================================================
?>