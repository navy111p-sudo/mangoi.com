<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";


$Sql = "UPDATE Classes set ";
	$Sql .= " ClassAttendState = 0, ";
	$Sql .= " ClassAttendStateReturnDateTime = now(), ";
	$Sql .= " ClassModiDateTime = now() ";
$Sql .= " where ClassID = :ClassID ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt = null;


// 수강신청 테이블에서 연기하면서 늘렸던 수강기강은 원래대로 복구한다

// ClassOrderID 얻어오기
$Sql = "SELECT 
			A.*,
			B.ClassProductID,
			C.MemberPayType, 
			D.CenterPayType 
		from Classes A 
			inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID 
			inner join Members C on A.MemberID=C.MemberID 
			inner join Centers D on C.CenterID=D.CenterID 
		where A.ClassID=$ClassID 
	";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;


$ClassOrderID = $Row["ClassOrderID"];


//ClassOrderEndDate 얻어오기
$Sql = "SELECT 
					A.ClassOrderEndDate
				from ClassOrders A 
				where ClassOrderID=$ClassOrderID 
			";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ClassOrderEndDate = $Row["ClassOrderEndDate"];

			
if ($ClassOrderEndDate!="" && $ClassOrderEndDate!="0000-00-00"){

	$ClassOrderEndDateWeek = date("w", strtotime($ClassOrderEndDate));
				
				
	$ExistStudyWeek[0] = 0;
	$ExistStudyWeek[1] = 0;
	$ExistStudyWeek[2] = 0;
	$ExistStudyWeek[3] = 0;
	$ExistStudyWeek[4] = 0;
	$ExistStudyWeek[5] = 0;
	$ExistStudyWeek[6] = 0;
	// 1주일 중 어느 요일에 수업이 있는지 체크한다.			
	$Sql = "SELECT  
				A.*
			from ClassOrderSlots A 
			where A.ClassOrderID=$ClassOrderID and A.ClassOrderSlotType=1 and A.ClassOrderSlotMaster=1 and A.ClassOrderSlotEndDate is null 
			order by A.StudyTimeWeek asc
		";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	while($Row = $Stmt->fetch()) {
					
		$StudyTimeWeek = $Row["StudyTimeWeek"];
		$ExistStudyWeek[$StudyTimeWeek] = 1;

	}
	$Stmt = null;
	$ResetClassOrderEndDate = $ClassOrderEndDate;
	$SetLastDate = 0;
	$ListWeekNum = $ClassOrderEndDateWeek-1;
	$ListNum = 1;

	// 요일을 줄여가면서 수업이 있는 요일로 수업일자를 복원한다.			
	while($SetLastDate==0){
			
		if ($ListWeekNum<0){
			$ListWeekNum2 = 6;
		}else{
			$ListWeekNum2 = $ListWeekNum;
		}

		if ($ExistStudyWeek[$ListWeekNum2]==1){
			$ResetClassOrderEndDate = date("Y-m-d", strtotime($ClassOrderEndDate. " - ".$ListNum." days"));
			$SetLastDate=1;
		}

		$ListWeekNum--;
		$ListNum++;
	}
}

$Sql2 = "UPDATE ClassOrders set 
			LastClassOrderEndDate=ClassOrderEndDate,
			ClassOrderEndDate=:ClassOrderEndDate, 
			ClassOrderModiDateTime=now() 
		where ClassOrderID=:ClassOrderID";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':ClassOrderEndDate', $ResetClassOrderEndDate);
$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
$Stmt2->execute();
$Stmt2 = null;

//종료일 로그 남기기 =======================================
$ClassOrderEndDateLogFileQueryNum = 1;
$Sql_EndDateLog = " INSERT into ClassOrderEndDateLogs ( ";
	$Sql_EndDateLog .= " ClassOrderID, ";
	$Sql_EndDateLog .= " ClassOrderEndDateLogType, ";
	$Sql_EndDateLog .= " ClassOrderEndDate, ";
	$Sql_EndDateLog .= " ClassOrderEndDateLogFileName, ";
	$Sql_EndDateLog .= " ClassOrderEndDateLogFileQueryNum, ";
	$Sql_EndDateLog .= " ClassOrderEndDateLogRegDateTime ";
$Sql_EndDateLog .= " ) values ( ";
	$Sql_EndDateLog .= " :ClassOrderID, ";
	$Sql_EndDateLog .= " '강의 연기복원에 의한 종료일 변경', ";
	$Sql_EndDateLog .= " :ClassOrderEndDate, ";
	$Sql_EndDateLog .= " :ClassOrderEndDateLogFileName, ";
	$Sql_EndDateLog .= " :ClassOrderEndDateLogFileQueryNum, ";
	$Sql_EndDateLog .= " now() ";
$Sql_EndDateLog .= " ) ";
$Stmt_EndDateLog = $DbConn->prepare($Sql_EndDateLog);
$Stmt_EndDateLog->bindParam(':ClassOrderID', $ClassOrderID);
$Stmt_EndDateLog->bindParam(':ClassOrderEndDate', $ResetClassOrderEndDate);
$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileName', $ClassOrderEndDateLogFileName);
$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileQueryNum', $ClassOrderEndDateLogFileQueryNum);
$Stmt_EndDateLog->execute();
$Stmt_EndDateLog = null;
//종료일 로그 남기기 =======================================



$ArrValue["ResultValue"] = 1;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>