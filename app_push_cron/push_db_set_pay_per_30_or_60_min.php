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



// test ======================================================
$Sql2 = "update TestTable set TestTableText=now() where TestTableID=1";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->execute();
$Stmt2 = null;
// test ======================================================


$SelectYear = date("Y");
$SelectMonth = date("m");
$SelectDay = date("d");

$SelectDate = $SelectYear."-".$SelectMonth."-".$SelectDay;
$SelectDateWeek = date('w', strtotime($SelectDate));

// 아래의 값은 오늘수업 페이지에서 사용하는것.. 
// 필요 시, 구분자를 넣어 다른 값을 넣어야할 수도 있음
$type = "1";
$SearchState = $type;
$EduCenterID = 1;
$DeviceToken1 = "";
$DeviceType1 = "";
$DeviceToken2 = "";
$DeviceType2 = "";

$Sql_edu = "select 
					A.EduCenterHolidayName  
				from EduCenterHolidays A
				where A.EduCenterHolidayDate=:EduCenterHolidayDate
					and A.EduCenterID=:EduCenterID
					and A.EduCenterHolidayState=1 
				";
$Stmt_edu = $DbConn->prepare($Sql_edu);
$Stmt_edu->bindParam(':EduCenterHolidayDate', $SelectDate);
$Stmt_edu->bindParam(':EduCenterID', $EduCenterID);
$Stmt_edu->execute();
$Stmt_edu->setFetchMode(PDO::FETCH_ASSOC);
$Row_edu = $Stmt_edu->fetch();
$Stmt_edu = null;

$EduCenterHolidayName = $Row_edu["EduCenterHolidayName"];

$Sql = " select 
				A.EduCenterHoliday0,
				A.EduCenterHoliday1,
				A.EduCenterHoliday2,
				A.EduCenterHoliday3,
				A.EduCenterHoliday4,
				A.EduCenterHoliday5,
				A.EduCenterHoliday6 
			from EduCenters A
			where 
				A.EduCenterID=:EduCenterID
				and A.EduCenterState=1 
				";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EduCenterID', $EduCenterID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();

$EduCenterHoliday[0] = $Row["EduCenterHoliday0"];
$EduCenterHoliday[1] = $Row["EduCenterHoliday1"];
$EduCenterHoliday[2] = $Row["EduCenterHoliday2"];
$EduCenterHoliday[3] = $Row["EduCenterHoliday3"];
$EduCenterHoliday[4] = $Row["EduCenterHoliday4"];
$EduCenterHoliday[5] = $Row["EduCenterHoliday5"];
$EduCenterHoliday[6] = $Row["EduCenterHoliday6"];

if($EduCenterHoliday[$SelectDateWeek]==1) {
	$IsRegHoliday = 1;
} else {
	$IsRegHoliday = 0;
}

if($EduCenterHolidayName==null && $IsRegHoliday==0) {

	$AddSqlWhere = " 1=1 ";
	$CheckReSend = "|";

	$AddSqlWhere = $AddSqlWhere . " and MB.MemberState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and CT.CenterState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and BR.BranchState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and BRG.BranchGroupState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and COM.CompanyState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and FR.FranchiseState<>0 ";

	/*
	$AddSqlWhere = $AddSqlWhere . " and 
							( COS.StudyTimeWeek=".$SelectDateWeek." and MB.MemberStudyAlarmType=1 and MB.MemberStudyAlarmTime=10 and CT.CenterAcceptSms=1 and 
							(
							  timestampdiff( minute, NOW(), str_to_date(concat(".$SelectYear.",'-', ".$SelectMonth.",'-', ".$SelectDay.",' ', lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0  ), ':00'), '%Y-%m-%d %H:%i:%s')) >0 and 
							  timestampdiff( minute, NOW(), str_to_date(concat(".$SelectYear.",'-', ".$SelectMonth.",'-', ".$SelectDay.",' ', lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0 ), ':00'), '%Y-%m-%d %H:%i:%s')) <=10)  
							)

							or
							( COS.StudyTimeWeek=".$SelectDateWeek." and MB.MemberStudyAlarmType=1 and MB.MemberStudyAlarmTime=30 and CT.CenterAcceptSms=1 and 
							(
							  timestampdiff( minute, NOW(), str_to_date(concat(".$SelectYear.",'-', ".$SelectMonth.",'-', ".$SelectDay.",' ', lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0), ':00'), '%Y-%m-%d %H:%i:%s')) >20 and timestampdiff( minute, NOW(), str_to_date(concat(".$SelectYear.",'-', ".$SelectMonth.",'-', ".$SelectDay.",' ', lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0 ), ':00'), '%Y-%m-%d %H:%i:%s')) <=30) 
							)

							or
							( COS.StudyTimeWeek=".$SelectDateWeek." and MB.MemberStudyAlarmType=1 and MB.MemberStudyAlarmTime=60 and CT.CenterAcceptSms=1 and 
							(
							  timestampdiff( minute, NOW(), str_to_date(concat(".$SelectYear.",'-', ".$SelectMonth.",'-', ".$SelectDay.",' ', lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0), ':00'), '%Y-%m-%d %H:%i:%s')) >50 and timestampdiff( minute, NOW(), str_to_date(concat(".$SelectYear.",'-', ".$SelectMonth.",'-', ".$SelectDay.",' ', lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0 ), ':00'), '%Y-%m-%d %H:%i:%s')) <=60) 
							)
							 ";
	*/

	//============================

	$SearchAfter10min = date('H:i');
	$SearchBefore10min = date('H:i', strtotime("+10 minutes") );
	//2023년6월27일 30분전에 알람이 가면 수업변경시 너무 빠듯하다고 해서 수업 40분전으로 변경함. 
	$SearchAfter30min = date('H:i', strtotime("+30 minutes") );
	$SearchBefore30min = date('H:i', strtotime("+40 minutes") );

	$SearchAfter60min = date('H:i', strtotime("+50 minutes") );
	$SearchBefore60min = date('H:i', strtotime("+60 minutes") );

	$AddSqlWhere = $AddSqlWhere . " and 
							( 
								COS.StudyTimeWeek=".$SelectDateWeek." and MB.MemberStudyAlarmType=1 and MB.MemberStudyAlarmTime=10 and CT.CenterAcceptSms=1 and 
								(
									concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0)) > '".$SearchAfter10min."' and
									concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0)) <= '".$SearchBefore10min."'
								)

								or
								COS.StudyTimeWeek=".$SelectDateWeek." and MB.MemberStudyAlarmType=1 and MB.MemberStudyAlarmTime=30 and CT.CenterAcceptSms=1 and 
								(
									concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0)) > '".$SearchAfter30min."' and
									concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0)) <= '".$SearchBefore30min."'
								)

								or
								COS.StudyTimeWeek=".$SelectDateWeek." and MB.MemberStudyAlarmType=1 and MB.MemberStudyAlarmTime=60 and CT.CenterAcceptSms=1 and 
								(
									concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0)) > '".$SearchAfter60min."' and
									concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0)) <= '".$SearchBefore60min."'
								)
							)
							 ";

	/*

	$SearchStartHourMinute = substr("0".$SearchStartHour,-2) . substr("0".$SearchStartMinute,-2);
	$SearchEndHourMinute = substr("0".$SearchEndHour,-2) . substr("0".$SearchEndMinute,-2);

	$AddSqlWhere = $AddSqlWhere . " and concat(lpad(COS.StudyTimeHour,2,0) , lpad(COS.StudyTimeMinute,2,0))>=$SearchStartHourMinute ";
	$AddSqlWhere = $AddSqlWhere . " and concat(lpad(COS.StudyTimeHour,2,0) , lpad(COS.StudyTimeMinute,2,0))<=$SearchEndHourMinute ";
	*/
	//============================


	if ($SearchState!="1"){//2:미등록 3:강사취소 4:학생취소 //0:준비 1:출석 2:지각 3:결석 4:학생연기 5:교사연기
		if ($SearchState=="2"){
			$AddSqlWhere = $AddSqlWhere . " and CLS.ClassAttendState is NULL ";
		}else if ($SearchState=="9"){
			$AddSqlWhere = $AddSqlWhere . " and CLS.ClassAttendState is not NULL and ( CLS.ClassAttendState=4 or CLS.ClassAttendState=5 or CLS.ClassAttendState=6 or CLS.ClassAttendState=7 or CLS.ClassAttendState=8 ) ";
		}
	}

	$AddSqlWhere = $AddSqlWhere . " and TEA.TeacherState=1 ";

	$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotMaster=1 ";
	$AddSqlWhere = $AddSqlWhere . " and 
										( 
											(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

											or 
											(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

											or 
											(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

											or 
											(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 
											
											or 
											(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SelectDate."')=0 )   
										)   
									";
	$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotState=1 ";

	$AddSqlWhere = $AddSqlWhere . " and CO.ClassProgress=11 ";
	$AddSqlWhere = $AddSqlWhere . " and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=3 or CO.ClassOrderState=5 or CO.ClassOrderState=6) ";

	$AddSqlWhere = $AddSqlWhere . " and ( CLS.ClassAttendState is NULL or CLS.ClassAttendState<4 ) ";

	/*
			$SmsMessagePhoneNumber=$Row["DecMemberPhone1"];
			$SmsMessagePhoneNumber2=$Row["DecMemberPhone2"];
			$KakaoMessagePhoneNumber=$Row["DecMemberPhone1"];
			$KakaoMessagePhoneNumber2=$Row["DecMemberPhone2"];
			$MemberName = $Row["MemberName"];
			$CenterID = $Row["CenterID"];
			$ClassID = $Row["ClassID"];
			$BranchGroupID = $Row["BranchGroupID"];
			$StudyTimeHour = $Row["StudyTimeHour"];
			$StudyTimeMinute = $Row["StudyTimeMinute"];
			$MemberStudyAlarmTime = $Row["MemberStudyAlarmTime"];
	*/

	$ViewTable = "

		select 
			COS.ClassMemberType,
			COS.ClassOrderSlotType,
			COS.ClassOrderSlotType2, 
			COS.ClassOrderSlotDate,
			COS.TeacherID,
			COS.ClassOrderSlotMaster,
			COS.StudyTimeWeek,
			COS.StudyTimeHour,
			COS.StudyTimeMinute,
			COS.ClassOrderSlotState,
			concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0)) as ClassStartTime, 

			CO.ClassOrderID,
			CO.ClassProductID,
			CO.ClassOrderTimeTypeID,
			CO.MemberID,
			CO.ClassOrderStartDate,
			CO.ClassOrderEndDate,
			CO.ClassOrderState,

			ifnull(CLS.ClassID,0) as ClassID,
			CLS.TeacherInDateTime, 
			CLS.TeacherID as ClassTeacherID,
			CLS.ClassLinkType,
			CLS.StartDateTime,
			CLS.StartDateTimeStamp,
			CLS.StartYear,
			CLS.StartMonth,
			CLS.StartDay,
			CLS.StartHour,
			CLS.StartMinute,
			CLS.EndDateTime,
			CLS.EndDateTimeStamp,
			CLS.EndYear,
			CLS.EndMonth,
			CLS.EndDay,
			CLS.EndHour,
			CLS.EndMinute,
			CLS.CommonUseClassIn,
			CLS.CommonShClassCode,
			CLS.CommonCiCourseID,
			CLS.CommonCiClassID,
			CLS.CommonCiTelephoneTeacher,
			CLS.CommonCiTelephoneStudent,
			ifnull(CLS.ClassAttendState,-1) as ClassAttendState,
			CLS.ClassAttendStateMemberID,
			CLS.ClassAttendStateMsg,
			ifnull(CLS.ClassState, 0) as ClassState,
			CLS.BookVideoID,
			CLS.BookQuizID,
			CLS.BookScanID,
			CLS.BookWebookUnitID, 
			CLS.BookSystemType, 
			CLS.ClassRegDateTime,
			CLS.ClassModiDateTime,

			MB.MemberName,
			MB.MemberPayType,
			MB.MemberChangeTeacher,
			MB.MemberNickName,
			MB.MemberLoginID, 
			MB.MemberLevelID,

			MB.MemberStudyAlarmTime, 
			AES_DECRYPT(UNHEX(MB.MemberPhone1),'$EncryptionKey') as DecMemberPhone1,
			AES_DECRYPT(UNHEX(MB.MemberPhone2),'$EncryptionKey') as DecMemberPhone2,

			TEA.TeacherName,
			MB2.MemberLoginID as TeacherLoginID, 
			CT.CenterID, 
			CT.CenterName,
			CT.CenterPayType,
			CT.CenterRenewType,
			CT.CenterStudyEndDate,

			BR.BranchID,
			BR.BranchName, 
			BRG.BranchGroupID,
			BRG.BranchGroupName,
			FR.FranchiseName,
			MB3.MemberLoginID as CenterLoginID,
			TEA2.TeacherName as ClassTeacherName

		from ClassOrderSlots COS 

				left outer join Classes CLS on COS.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$SelectYear." and CLS.StartMonth=".$SelectMonth." and CLS.StartDay=".$SelectDay." and CLS.StartHour=COS.StudyTimeHour and CLS.StartMinute=COS.StudyTimeMinute and CLS.TeacherID=COS.TeacherID and CLS.ClassAttendState<>99 

				inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 



				inner join Members MB on CO.MemberID=MB.MemberID 
				inner join Centers CT on MB.CenterID=CT.CenterID 
				inner join Branches BR on CT.BranchID=BR.BranchID 
				inner join BranchGroups BRG on BR.BranchGroupID=BRG.BranchGroupID 
				inner join Companies COM on BRG.CompanyID=COM.CompanyID 
				inner join Franchises FR on COM.FranchiseID=FR.FranchiseID 
				inner join Teachers TEA on COS.TeacherID=TEA.TeacherID 
				left outer join Teachers TEA2 on CLS.TeacherID=TEA2.TeacherID 
				inner join Members MB2 on TEA.TeacherID=MB2.TeacherID 
				left outer join Members MB3 on CT.CenterID=MB3.CenterID and MB3.MemberLevelID=12 

		where ".$AddSqlWhere." ";


				//결제 안해도 관리자가 등록한 수업 진행가능하도록 주석 처리
				//inner join ClassOrderPayDetails COPD on COS.ClassOrderID=COPD.ClassOrderID 
				//inner join ClassOrderPays COP on COPD.ClassOrderPayID=COP.ClassOrderPayID and (COP.ClassOrderPayProgress=21 or COP.ClassOrderPayProgress=31 or COP.ClassOrderPayProgress=41)

	
	$SqlWhereCenterRenew = "";
	if ($NoIgnoreCenterRenew==1){
		$SqlWhereCenterRenew = " and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ";
	}
	
	$Sql = "
			select 
				V.*
			from ($ViewTable) V 

			where 
				(
					V.CenterPayType=1 and V.CenterRenewType=1 
					".$SqlWhereCenterRenew." 
					and V.MemberPayType=0 
					and (
							(V.ClassOrderState=1 or V.ClassOrderState=2 or V.ClassOrderState=5 or V.ClassOrderState=6) 
							or 
							(V.ClassOrderState=3 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0) 
						)
				) 
				or 
				(
					V.CenterPayType=1 and V.CenterRenewType=2 
					and V.MemberPayType=0 
					and (
							(V.ClassOrderState=1 or V.ClassOrderState=2 or V.ClassOrderState=5 or V.ClassOrderState=6) 
							or 
							(V.ClassOrderState=3 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0) 
						)
				)
				or 
				( 
					( V.CenterPayType=2 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
					or 
					( V.CenterPayType=1 and V.MemberPayType=1 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
				)
				or
				V.ClassProductID=2 
				or 
				V.ClassProductID=3 
				or 
				(V.ClassOrderSlotType=2 and datediff(V.ClassOrderSlotDate, '".$SelectDate."')=0) 
			group by V.ClassMemberType, V.ClassOrderSlotType, V.ClassOrderSlotDate, V.TeacherID, V.StudyTimeWeek, V.StudyTimeHour, V.StudyTimeMinute
			order by V.StudyTimeHour asc, V.StudyTimeMinute, V.TeacherID asc
		";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute(); 
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	while($Row = $Stmt->fetch()) {

			$SmsMessagePhoneNumber=$Row["DecMemberPhone1"];
			$SmsMessagePhoneNumber2=$Row["DecMemberPhone2"];
			$KakaoMessagePhoneNumber=$Row["DecMemberPhone1"];
			$KakaoMessagePhoneNumber2=$Row["DecMemberPhone2"];

			$MemberName = $Row["MemberName"];
			$CenterID = $Row["CenterID"];
			$ClassID = $Row["ClassID"];
			$ClassOrderID = $Row["ClassOrderID"];
			$BranchGroupID = $Row["BranchGroupID"];
			$StudyTimeHour = $Row["StudyTimeHour"];
			$StudyTimeMinute = $Row["StudyTimeMinute"];
			$MemberStudyAlarmTime = $Row["MemberStudyAlarmTime"];

			if($CenterID == 121) { // 잉글리시텔 대리점의 경우
				$StrDomain = "[EnglishTell 화상영어]";
			} else if($BranchGroupID==18) { // SLP 대표지사 ( SLP 가 아닌 "김재익 지사, 망고아이 기본지사 뺄것 )
				$StrDomain = "[SLP 망고아이]";
			} else {
				$StrDomain = "[망고아이]";
			}

			if($MemberStudyAlarmTime==10) {
				$StrMessage = "10분";
			} else if($MemberStudyAlarmTime==30) {
				$StrMessage = "40분";
			} else if($MemberStudyAlarmTime==60) {
				$StrMessage = "1시간";
			}

			$SendMemberID = 0;
			$MemberID=$Row["MemberID"];
			$SendTitle="수업알림";
			$SendMessage="$StrDomain $MemberName 님의 수업이 약 $StrMessage 후에 시작됩니다. ";
			$SendMemo="";
			$SendMessageDateTime=date("Y-m-d H:i:s");
			$UseSendPush=1;
			$UseSendSms=1;
			$UseSendKakao=1;

			$MemberPhone1Check = preg_replace("/[^0-9]/", "", $SmsMessagePhoneNumber); // - 대쉬 제거
			if(preg_match("/^01[0-9]{8,9}$/", $MemberPhone1Check) && $UseSendSms == 1) { // 발송 대상이면서 유효성검사에 통과한 값들만
				if( strpos($CheckReSend, "|".$SmsMessagePhoneNumber."|")!==false ) { // 보낸폰번호 이력에 해당 번호가 있다면 ( 기존에 보냈었다면 )
					$SmsCheckResult = 0;
				} else { // 보낸폰번호 이력에 없다면... 
					$CheckReSend = $CheckReSend . $SmsMessagePhoneNumber . "|";
					$SmsCheckResult = 1;
				}
			} else {
				$SmsCheckResult = 0;
			}

			$Sql_Push = " insert into SendMessageLogs ( ";
				$Sql_Push .= " ClassID, ";
				$Sql_Push .= " ClassOrderID, ";
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
				$Sql_Push .= " :ClassID, ";
				$Sql_Push .= " :ClassOrderID, ";
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
				$Sql_Push .= " :DeviceToken1, ";
				$Sql_Push .= " :DeviceType1, ";
				$Sql_Push .= " 1, ";
				$Sql_Push .= " :SmsMessagePhoneNumber, ";
				$Sql_Push .= " 1, ";
				$Sql_Push .= " :KakaoMessagePhoneNumber, ";
				$Sql_Push .= " 1 ";
			$Sql_Push .= " ) ";

			$Stmt_Push = $DbConn->prepare($Sql_Push);
			$Stmt_Push->bindParam(':ClassID', $ClassID);
			$Stmt_Push->bindParam(':ClassOrderID', $ClassOrderID);
			$Stmt_Push->bindParam(':MemberID', $MemberID);
			$Stmt_Push->bindParam(':SendMemberID', $SendMemberID);
			$Stmt_Push->bindParam(':SendTitle', $SendTitle);
			$Stmt_Push->bindParam(':SendMessage', $SendMessage);
			$Stmt_Push->bindParam(':SendMemo', $SendMemo);
			$Stmt_Push->bindParam(':SendMessageDateTime', $SendMessageDateTime);
			$Stmt_Push->bindParam(':UseSendPush', $UseSendPush);
			$Stmt_Push->bindParam(':UseSendSms', $SmsCheckResult);
			$Stmt_Push->bindParam(':UseSendKakao', $UseSendKakao);
			$Stmt_Push->bindParam(':DeviceToken1', $DeviceToken1);
			$Stmt_Push->bindParam(':DeviceType1', $DeviceType1);
			$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
			$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
			$Stmt_Push->execute();
			$Stmt_Push = null;

			$MemberPhone2Check = preg_replace("/[^0-9]/", "", $SmsMessagePhoneNumber2); // - 대쉬 제거
			if(preg_match("/^01[0-9]{8,9}$/", $MemberPhone2Check) && $UseSendSms == 1) { // 발송 대상이면서 유효성검사에 통과한 값들만
				if( strpos($CheckReSend, "|".$SmsMessagePhoneNumber2."|")!==false ) { // 보낸폰번호 이력에 해당 번호가 있다면 ( 기존에 보냈었다면 )
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
				$Sql_Push .= " ClassID, ";
				$Sql_Push .= " ClassOrderID, ";
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
				$Sql_Push .= " :ClassID, ";
				$Sql_Push .= " :ClassOrderID, ";
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
				$Sql_Push .= " :DeviceToken2, ";
				$Sql_Push .= " :DeviceType2, ";
				$Sql_Push .= " 1, ";
				$Sql_Push .= " :SmsMessagePhoneNumber, ";
				$Sql_Push .= " 1, ";
				$Sql_Push .= " :KakaoMessagePhoneNumber, ";
				$Sql_Push .= " 1 ";
			$Sql_Push .= " ) ";

			$Stmt_Push = $DbConn->prepare($Sql_Push);
			$Stmt_Push->bindParam(':ClassID', $ClassID);
			$Stmt_Push->bindParam(':ClassOrderID', $ClassOrderID);
			$Stmt_Push->bindParam(':MemberID', $MemberID);
			$Stmt_Push->bindParam(':SendMemberID', $SendMemberID);
			$Stmt_Push->bindParam(':SendTitle', $SendTitle);
			$Stmt_Push->bindParam(':SendMessage', $SendMessage);
			$Stmt_Push->bindParam(':SendMemo', $SendMemo);
			$Stmt_Push->bindParam(':SendMessageDateTime', $SendMessageDateTime);
			$Stmt_Push->bindParam(':UseSendPush', $UseSendPush);
			$Stmt_Push->bindParam(':UseSendSms', $SmsCheckResult);
			$Stmt_Push->bindParam(':UseSendKakao', $UseSendKakao);
			$Stmt_Push->bindParam(':DeviceToken2', $DeviceToken2);
			$Stmt_Push->bindParam(':DeviceType2', $DeviceType2);
			$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber2);
			$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber2);
			$Stmt_Push->execute();
			$Stmt_Push = null;
	}
}

?>
