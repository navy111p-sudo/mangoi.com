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



$type = 1;
$SearchState = $type;
$TeacherIDIsHoliday = "|"; // 휴일인 강사들을 저장하는 변수
$TeacherManagerList = "|";

$SelectYear = date("Y");
$SelectMonth = date("m");
$SelectDay = date("d");
$SearchCurrentHour = date('H');
$SearchCurrentMinute = date('i');
$SelectDate = $SelectYear."-".$SelectMonth."-".$SelectDay;

// 테스트용 시간 설정
//$SearchCurrentHour = "14";
//$SearchCurrentMinute = "00";
//$SelectDate = "2019-12-19";
// 테스트용 시간 설정


$SearchCurrentMinute = substr("0". floor($SearchCurrentMinute / 10) *10 , -2); // 10분 단위로 하기 위함 31분의 경우 나누면 3.1 소수점 버리고 * 10 하면 30
$SearchCurrentTime = $SearchCurrentHour.':'.$SearchCurrentMinute;




$SelectDateWeek = date('w', strtotime($SelectDate));

$EduCenterID = 1;

$Sql = " select 
				A.EduCenterHolidayDate  
			from EduCenterHolidays A
			where A.EduCenterHolidayDate=:EduCenterHolidayDate
				and A.EduCenterID=:EduCenterID
				and A.EduCenterHolidayState=1 
				";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EduCenterHolidayDate', $SelectDate);
$Stmt->bindParam(':EduCenterID', $EduCenterID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$EduCenterHolidayDate = $Row["EduCenterHolidayDate"];
$Stmt = null;
if($EduCenterHolidayDate) {
	// 휴무일
	$IsHoliday = 1;
} else {
	$IsHoliday = 0;
}

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

if($IsHoliday==0 && $IsRegHoliday==0) {

	$Sql = " select 
					A.TeacherID 
				from TeacherHolidays A 
					inner join Teachers B on A.TeacherID=B.TeacherID 
					inner join TeacherGroups C on B.TeacherGroupID=C.TeacherGroupID 
				where A.TeacherHolidayDate=:TeacherHolidayDate
					and C.EduCenterID=:EduCenterID
					and A.TeacherHolidayState=1   
				";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherHolidayDate', $SelectDate);
	$Stmt->bindParam(':EduCenterID', $EduCenterID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	while($Row = $Stmt->fetch() ) {
		$TeacherID = $Row["TeacherID"];
		$TeacherIDIsHoliday = $TeacherIDIsHoliday . $TeacherID . "|";
	}
	$Stmt = null;

	// 매니저 강사 검색
	$Sql = " select 
					A.MemberID 
				from Members A
					inner join Teachers B on A.TeacherID=B.TeacherID 
				where 
				A.MemberState=1
				and A.MemberLevelID=15
				and B.TeacherIsManager=2 
				and B.TeacherState=1
				and B.TeacherView=1
				";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	while($Row = $Stmt->fetch() ) {
		$MemberID = $Row["MemberID"];
		$TeacherManagerList = $TeacherManagerList . $MemberID . "|";
	}
	$TeacherManagerList = explode("|", $TeacherManagerList);
	$Stmt = null;

	$AddSqlWhere = " 1=1 ";
	$CheckReSend = "|";

	$AddSqlWhere = $AddSqlWhere . " and MB.MemberState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and CT.CenterState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and BR.BranchState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and BRG.BranchGroupState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and COM.CompanyState<>0 ";
	$AddSqlWhere = $AddSqlWhere . " and FR.FranchiseState<>0 ";

	$AddSqlWhere = $AddSqlWhere . " and concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0))='".$SearchCurrentTime."' ";

	if ($SearchState!="1"){//2:미등록 3:강사취소 4:학생취소 //0:준비 1:출석 2:지각 3:결석 4:학생연기 5:교사연기
		if ($SearchState=="2"){
			$AddSqlWhere = $AddSqlWhere . " and CLS.ClassAttendState is NULL ";
		}else if ($SearchState=="9"){
			$AddSqlWhere = $AddSqlWhere . " and CLS.ClassAttendState is not NULL and ( CLS.ClassAttendState=4 or CLS.ClassAttendState=5 or CLS.ClassAttendState=6 or CLS.ClassAttendState=7 or CLS.ClassAttendState=8 ) ";
		}
	}

	$AddSqlWhere = $AddSqlWhere . " and TEA.TeacherState=1 ";
	$AddSqlWhere = $AddSqlWhere . " and TEA.TeacherIsManager=1 "; // 일반 강사들만 추려냄

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

  $AddSqlWhere = $AddSqlWhere . " and ( CLS.ClassAttendState is NULL or  ( CLS.ClassAttendState<4 and CLS.ClassAttendState >=0 ) ) ";

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
	  MB2.MemberID as TeacherMemberID,
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

	while($Row = $Stmt->fetch() ) {

		
		$ClassOrderTimeTypeID=$Row["ClassOrderTimeTypeID"];
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
		$TeacherID = $Row["TeacherID"];
		$MemberID = $Row["MemberID"];
		$TeacherMemberID = $Row["TeacherMemberID"];
		$TeacherInDateTime = $Row["TeacherInDateTime"];
		$ClassStartTime = $Row["ClassStartTime"];
		$TeacherName = $Row["TeacherName"];
		
		if(strpos($TeacherIDIsHoliday, "|".$TeacherID."|" )!==false) {
			// 강사가 휴일 이라면
			return false;

		} else {

			// 강사가 휴일이 아니면
			$TeacherMessageText = $TeacherName . " 강사가 ".$ClassStartTime."(".$MemberName.") 수업에 입장하지 않았습니다.";

			
			// 강사 휴일이 아니고 수업입장도 시간이 비어있다면 
			if ($TeacherInDateTime=="" || $TeacherInDateTime==null){



				$ClassStartDateTime = $SelectDate . " ". $ClassStartTime . ":00"; 
				$ClassRunMinute = $ClassOrderTimeTypeID*10;

				$Sql = " insert into ClassTeacherEnters ( ";
					$Sql .= " TeacherID, ";
					$Sql .= " ClassDate, ";
					$Sql .= " ClassStartDateTime, ";
					$Sql .= " ClassRunMinute ";
				$Sql .= " ) values ( ";
					$Sql .= " :TeacherID, ";
					$Sql .= " :ClassDate, ";
					$Sql .= " :ClassStartDateTime, ";
					$Sql .= " :ClassRunMinute ";
				$Sql .= " ) ";
				//echo $Sql."<br>";
				$Stmt2 = $DbConn->prepare($Sql);
				$Stmt2->bindParam(':TeacherID', $TeacherID);
				$Stmt2->bindParam(':ClassDate', $SelectDate);
				$Stmt2->bindParam(':ClassStartDateTime', $ClassStartDateTime);
				$Stmt2->bindParam(':ClassRunMinute', $ClassRunMinute);
				$Stmt2->execute();
				$Stmt2 = null;



				for($ii=1; $ii < count($TeacherManagerList)-1; $ii++) {

					$RequestMemberID = 0; 

					$Sql = " insert into TeacherMessages ( ";
						$Sql .= " MemberID, ";
						$Sql .= " RequestMemberID, ";
						$Sql .= " TeacherMessageType, ";
						$Sql .= " TeacherMessageText, ";
						$Sql .= " TeacherMessageRegDateTime, ";
						$Sql .= " TeacherMessageModiDateTime ";
					$Sql .= " ) values ( ";
						$Sql .= " :MemberID, ";
						$Sql .= " :RequestMemberID, ";
						$Sql .= " 1, ";
						$Sql .= " :TeacherMessageText, ";
						$Sql .= " now(), ";
						$Sql .= " now() ";
					$Sql .= " ) ";

					$Stmt2 = $DbConn->prepare($Sql);
					$Stmt2->bindParam(':MemberID', $TeacherManagerList[$ii]);
					$Stmt2->bindParam(':RequestMemberID', $RequestMemberID);
					$Stmt2->bindParam(':TeacherMessageText', $TeacherMessageText);
					$Stmt2->execute();
					$Stmt2 = null;
				}

				$Sql = " insert into MasterMessages ( ";
					
					$Sql .= " MasterMessageType, ";
					$Sql .= " MasterMessageText, ";
					$Sql .= " MasterMessageAlarmType, ";
					$Sql .= " MasterMessageRegDateTime, ";
					$Sql .= " MasterMessageModiDateTime ";
				$Sql .= " ) values ( ";
					$Sql .= " 1, ";
					$Sql .= " :TeacherMessageText, ";
					$Sql .= " 1, ";
					$Sql .= " now(), ";
					$Sql .= " now() ";
				$Sql .= " ) ";

				$Stmt2 = $DbConn->prepare($Sql);
				$Stmt2->bindParam(':TeacherMessageText', $TeacherMessageText);
				$Stmt2->execute();
				$Stmt2 = null;

			}
		}
	}
}

?>
