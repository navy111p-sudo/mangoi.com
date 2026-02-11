<?
$NoIgnoreCenterRenew = 0;//0 이면 단체 연장을 하지 않아도 수업을 계속한다.(과도기) 최종적으로는 1으로 해야한다.

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

function DateToTimestamp($Date, $TimeZone) {
	// ex, GetTimeStamp(16050230, 9)
	$NewTimeStamp = new DateTime($Date, new DateTimeZone($TimeZone));

	return $NewTimeStamp->getTimestamp(); // 1457690400
}

function InsertNewTypePoint($MemberPointNewTypeID, $RegMemberID, $PointMemberID, $MemberPointVaridate){
	if ($PointMemberID>0){
		$Sql = "select 
					A.MemberLevelID,
					A.MemberName,
					AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
					ifnull((select DeviceToken from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
					ifnull((select DeviceType from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType 
				from Members A 
				where A.MemberID=:MemberID";
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		//$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $PointMemberID);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$Row = $Stmt->fetch();
		$Stmt = null;

		$MemberLevelID = $Row["MemberLevelID"];
		$DeviceToken = $Row["DeviceToken"];
		$DeviceType = $Row["DeviceType"];
		$SmsMessagePhoneNumber = $Row["DecMemberPhone1"];
		$KakaoMessagePhoneNumber = $Row["DecMemberPhone1"];
		$MemberName = $Row["MemberName"];

		/*
		if($MemberLevelID==19) {
		} else if($MemberLevelID==18) {
		} else if($MemberLevelID==12 or $MemberLevelID==13) {
		}
		*/

		$Sql = "select 
					A.MemberPoint, 
					A.MemberPointTypeName, 
					A.MemberPointTypeText
				from MemberPointNewTypes A 
				where A.MemberPointTypeID=:MemberPointNewTypeID";
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		//$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberPointNewTypeID', $MemberPointNewTypeID);
		$Stmt->execute();
		$Row = $Stmt->fetch();
		$Stmt = null;
		
		$MemberPoint = $Row["MemberPoint"];
		$MemberPointTypeName = $Row["MemberPointTypeName"];
		$MemberPointTypeText = $Row["MemberPointTypeText"];


		$MemberPointTypeText = str_replace("{{이름}}", $MemberName, $MemberPointTypeText);
		$MemberPointTypeText = str_replace("{{포인트}}", number_format($MemberPoint,0), $MemberPointTypeText);

		$Sql = "
			select
				A.MemberPointID
			from MemberPoints A
			where 
				A.MemberPointTypeID=:MemberPointNewTypeID 
				and 
				A.MemberID=:MemberID 
				and 
				A.MemberPointState=1 
				and 
				datediff(A.MemberPointRegDateTime, now())=0
				and
				A.MemberPointVaridate=:MemberPointVaridate
		";
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		//$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $PointMemberID);
		$Stmt->bindParam(':MemberPointNewTypeID', $MemberPointNewTypeID);
		$Stmt->bindParam(':MemberPointVaridate', $MemberPointVaridate);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$MemberPointID = $Row["MemberPointID"];

		if (!$MemberPointID) {

			$Sql = " insert into MemberPoints ( ";
				$Sql .= " MemberPointTypeID, ";
				$Sql .= " RegMemberID, ";
				$Sql .= " MemberID, ";
				$Sql .= " MemberPointName, ";
				$Sql .= " MemberPointText, ";
				$Sql .= " MemberPoint, ";
				$Sql .= " MemberPointRegDateTime, ";
				$Sql .= " MemberPointModiDateTime, ";
				$Sql .= " MemberPointState, ";
				$Sql .= " MemberPointVaridate ";
				
			$Sql .= " ) values ( ";
				$Sql .= " :MemberPointTypeID, ";
				$Sql .= " :RegMemberID, ";
				$Sql .= " :MemberID, ";
				$Sql .= " :MemberPointName, ";
				$Sql .= " :MemberPointText, ";
				$Sql .= " :MemberPoint, ";
				$Sql .= " now(), ";
				$Sql .= " now(), ";
				$Sql .= " 1, ";
				$Sql .= " :MemberPointVaridate ";
			$Sql .= " ) ";
			
			$Stmt = $GLOBALS['DbConn']->prepare($Sql);
			//$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':MemberPointTypeID', $MemberPointNewTypeID);
			$Stmt->bindParam(':RegMemberID', $RegMemberID);
			$Stmt->bindParam(':MemberID', $PointMemberID);
			$Stmt->bindParam(':MemberPointName', $MemberPointTypeName);
			$Stmt->bindParam(':MemberPointText', $MemberPointTypeText);
			$Stmt->bindParam(':MemberPoint', $MemberPoint);
			$Stmt->bindParam(':MemberPointVaridate', $MemberPointVaridate);
			$Stmt->execute();
			$Stmt = null;


			$SendMemberID = 0;
			$SendTitle="적립알림";
			//$SendMessage=$MemberName." 님께서 ".$MemberPoint." 포인트(".$MemberPointTypeName.")를 적립하였습니다.";
			//$SendMessage=$MemberName." 학생에게 ".$MemberPoint." 포인트를 적립되었습니다.".$StrMemberPointTypeName;
			$SendMemo="";
			$SendMessageDateTime=date("Y-m-d H:i:s");
			$UseSendPush=1;
			$UseSendSms=0;
			$UseSendKakao=0;

			if($MemberLevelID==19) {//학생일때만 푸시 보냄

				//학생에게 푸시
				if ($DeviceToken!=""){
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

					$Stmt_Push = $GLOBALS['DbConn']->prepare($Sql_Push);
					$Stmt_Push->bindParam(':MemberID', $PointMemberID);
					$Stmt_Push->bindParam(':SendMemberID', $SendMemberID);
					$Stmt_Push->bindParam(':SendTitle', $SendTitle);
					$Stmt_Push->bindParam(':SendMessage', $MemberPointTypeText);
					$Stmt_Push->bindParam(':SendMemo', $SendMemo);
					$Stmt_Push->bindParam(':SendMessageDateTime', $SendMessageDateTime);
					$Stmt_Push->bindParam(':UseSendPush', $UseSendPush);
					$Stmt_Push->bindParam(':UseSendSms', $UseSendSms);
					$Stmt_Push->bindParam(':UseSendKakao', $UseSendKakao);
					$Stmt_Push->bindParam(':DeviceToken', $DeviceToken);
					$Stmt_Push->bindParam(':DeviceType', $DeviceType);
					$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
					$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
					$Stmt_Push->execute();
					$Stmt_Push = null;
				}
				//부모에게 푸시
				$Sql2 = "select
							ifnull((select DeviceToken from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
							ifnull((select DeviceType from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType 
						from MemberChilds A 
						where A.MemberChildID=:MemberID";

				$Stmt2 = $GLOBALS['DbConn']->prepare($Sql2);
				$Stmt2->bindParam(':MemberID', $PointMemberID);
				$Stmt2->execute();
				
				while($Row2 = $Stmt2->fetch()) {
					$DeviceToken = $Row2["DeviceToken"];
					$DeviceType = $Row2["DeviceType"];


					if ($DeviceToken!=""){
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

						$Stmt_Push = $GLOBALS['DbConn']->prepare($Sql_Push);
						$Stmt_Push->bindParam(':MemberID', $PointMemberID);
						$Stmt_Push->bindParam(':SendMemberID', $SendMemberID);
						$Stmt_Push->bindParam(':SendTitle', $SendTitle);
						$Stmt_Push->bindParam(':SendMessage', $SendMessage);
						$Stmt_Push->bindParam(':SendMemo', $SendMemo);
						$Stmt_Push->bindParam(':SendMessageDateTime', $SendMessageDateTime);
						$Stmt_Push->bindParam(':UseSendPush', $UseSendPush);
						$Stmt_Push->bindParam(':UseSendSms', $UseSendSms);
						$Stmt_Push->bindParam(':UseSendKakao', $UseSendKakao);
						$Stmt_Push->bindParam(':DeviceToken', $DeviceToken);
						$Stmt_Push->bindParam(':DeviceType', $DeviceType);
						$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
						$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
						$Stmt_Push->execute();
						$Stmt_Push = null;
					}
				}
				$Stmt2 = null;
			}
		}
	}
}

$AddSqlWhere = "1=1";

$Today = date("Y-m-d");
$BeforeDate = date("Y-m-d", strtotime("-1 week"));

$Sql = "
	select 
		A.CenterID,
		B.MemberID
	from Centers A
		inner join Members B on A.CenterID=B.CenterID and ( B.MemberLevelID=12 ) 
	where 
		A.CenterState=1
		and
		A.CenterView=1
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

while($Row = $Stmt->fetch() ) {
	$CenterID = $Row["CenterID"];
	$MemberID = $Row["MemberID"];

			//A.MemberID,
			//A.MemberName,
			//A.MemberRegDateTime
	$Sql2 = "
		select
			count(*) as BeforeRowCount 
		from Members A
		where 
			A.MemberState=1
			and
			A.CenterID=:CenterID
			and
			datediff(DATE_FORMAT(A.MemberRegDateTime, '%Y-%m-%d'), :BeforeDate) <=0
	";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':BeforeDate', $BeforeDate);
	$Stmt2->bindParam(':CenterID', $CenterID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	$BeforeRowCount = $Row2["BeforeRowCount"];
	$Stmt2 = null;

	$Sql3 = "
		select
			count(*) as TodayRowCount 
		from Members A
		where 
			A.MemberState=1
			and
			A.CenterID=:CenterID
			and
			datediff(DATE_FORMAT(A.MemberRegDateTime, '%Y-%m-%d'), :Today) <=0
	";
	$Stmt3 = $DbConn->prepare($Sql3);
	$Stmt3->bindParam(':Today', $Today);
	$Stmt3->bindParam(':CenterID', $CenterID);
	$Stmt3->execute();
	$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
	$Row3 = $Stmt3->fetch();
	$TodayRowCount = $Row3["TodayRowCount"];
	$Stmt3 = null;

	if($BeforeRowCount < $TodayRowCount) {
		InsertNewTypePoint(11, 0, $MemberID, $Today);
	}

}
$Stmt = null;


?>
