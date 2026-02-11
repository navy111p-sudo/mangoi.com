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



$type = 1;
$SearchState = $type;
$TeacherIDIsHoliday = "|"; // 휴일인 강사들을 저장하는 변수
$TeacherManagerList = "|";

$SelectYear = date("Y");
$SelectMonth = date("m");
$SelectDay = date("d");
$SearchCurrentHour = date('H');

 /* 테스트용 시간 설정
$SearchCurrentHour = date('14');
$SearchCurrentMinute = date('00');
$SelectDate = "2019-12-26";
 */

$SelectDate = $SelectYear."-".$SelectMonth."-".$SelectDay;
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

if($EduCenterHolidayDate) {
	// 휴무일
	$IsHoliday = 1;
} else {
	$IsHoliday = 0;
}

if($IsHoliday==0 && $IsRegHoliday==0) {

	// 강사 휴일 검색
	$EduCenterID = 1;

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

	$Sql = "
			select 
				A.*
			from Teachers A 
			where 
				A.TeacherStartHour=:SearchCurrentHour and A.TeacherState=1
		";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SearchCurrentHour', $SearchCurrentHour);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	while($Row = $Stmt->fetch() ) {

		$TeacherID = $Row["TeacherID"];
		$TeacherName = $Row["TeacherName"];
		$TeacherStartHour = $Row["TeacherStartHour"];
		
		if(strpos($TeacherIDIsHoliday, "|".$TeacherID."|" )!==false) {
			// 강사가 휴일 이라면
			return false;

		} else {

			// 오늘 날짜와 강사를 검색
			$Sql = " select count(*) as TotalRowCount from TeacherAttendances A where A.TeacherID=:TeacherID and A.CheckDate=:CheckDate ";
			$Stmt2 = $DbConn->prepare($Sql);
			$Stmt2->bindParam(':TeacherID', $TeacherID);
			$Stmt2->bindParam(':CheckDate', $SelectDate);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
			$Row2 = $Stmt2->fetch();
			$Stmt2 = null;
			$TotalRowCount = $Row2["TotalRowCount"];
			$TeacherMessageText = $TeacherName . " 강사(".$TeacherStartHour."시) 가 출근 하지 않았습니다.";


			if($TotalRowCount == 0 ) {


					
				$TeacherAttendanceHour = $SelectDate ." ".  substr("0".$TeacherStartHour, -2).":00:00";
				
				$Sql2 = " insert into TeacherAttendances ( ";
					$Sql2 .= " TeacherID, ";
					$Sql2 .= " CheckDate, ";
					$Sql2 .= " TeacherAttendanceHour ";
				$Sql2 .= " ) values ( ";
					$Sql2 .= " :TeacherID, ";
					$Sql2 .= " :CheckDate, ";
					$Sql2 .= " :TeacherAttendanceHour ";
				$Sql2 .= " ) ";
				$Stmt2 = $DbConn->prepare($Sql2);
				$Stmt2->bindParam(':TeacherID', $TeacherID);
				$Stmt2->bindParam(':CheckDate', $SelectDate);
				$Stmt2->bindParam(':TeacherAttendanceHour', $TeacherAttendanceHour);
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
					$Sql .= " 2, ";
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
