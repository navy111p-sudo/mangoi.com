<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$ClassOrderEndDateLogFileName = $_SERVER["PHP_SELF"];

$err_num = 0;
$err_msg = "";
$NewTeacherMemberID = "";
$OldTeacherMemberID = "";
$ClassOrderResetApplyDate = "";
$SetStudyTimeDate = "";

$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$ClassProductID = isset($_REQUEST["ClassProductID"]) ? $_REQUEST["ClassProductID"] : "";
$CenterPayType = isset($_REQUEST["CenterPayType"]) ? $_REQUEST["CenterPayType"] : "";
$GroupRowCount = isset($_REQUEST["GroupRowCount"]) ? $_REQUEST["GroupRowCount"] : "";
$ClassMemberType = isset($_REQUEST["ClassMemberType"]) ? $_REQUEST["ClassMemberType"] : "";

$FromPage = isset($_REQUEST["FromPage"]) ? $_REQUEST["FromPage"] : "";

$ClassAttendStateMemberID = $_LINK_MEMBER_ID_;
if ($_LINK_MEMBER_LEVEL_ID_==19){
	$ClassAttendState = 4;//학생연기
}else{
	$ClassAttendState = 5;//강사연기
}



//=================== 로그 남기기 ==========================
$ClassOrderSlotLogMemberID = $_LINK_MEMBER_ID_;
$ClassOrderSlotLogMemo = "마지막수업 뒤로 연기";
$Sql = " insert into ClassOrderSlotLogs ( ";
	$Sql .= " ClassOrderID, ";
	$Sql .= " MemberID, ";
	$Sql .= " ClassOrderSlotLogMemo, ";
	$Sql .= " ClassOrderSlotLogRegDateTime ";
$Sql .= " ) values ( ";
	$Sql .= " :ClassOrderID, ";
	$Sql .= " :MemberID, ";
	$Sql .= " :ClassOrderSlotLogMemo, ";
	$Sql .= " now() ";
$Sql .= " ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
$Stmt->bindParam(':MemberID', $ClassOrderSlotLogMemberID);
$Stmt->bindParam(':ClassOrderSlotLogMemo', $ClassOrderSlotLogMemo);
$Stmt->execute();
$Stmt = null;
//=================== 로그 남기기 ==========================




if ($ClassMemberType!="1"){

	$Sql = "
			select 
				* 
			from Classes A 
			where ClassID=$ClassID 
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TeacherID=$Row["TeacherID"];
	$StartDateTimeStamp=$Row["StartDateTimeStamp"];
	$EndDateTimeStamp=$Row["EndDateTimeStamp"];


	$Sql = "
			select 
				* 
			from Classes A 
			where A.TeacherID=$TeacherID 
				and A.StartDateTimeStamp=$StartDateTimeStamp 
				and A.EndDateTimeStamp=$EndDateTimeStamp 
				and A.ClassAttendState<=3 
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	
	$ClassIDs = "|";
	while($Row = $Stmt->fetch()) {
		$ClassID = $Row["ClassID"];
		$ClassIDs = $ClassIDs . $ClassID . "|";
	}
	$Stmt = null;



}else{
	$ClassIDs = "|".$ClassID."|";
}

//echo $ClassIDs;

$ArrClassID = explode("|", $ClassIDs);

//echo $ClassIDs;

for ($ii=1;$ii<=count($ArrClassID)-2;$ii++){

	$ClassID = $ArrClassID[$ii];

	$Sql = "
		select 
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

	$ClassAttendState = $Row["ClassAttendState"];
	$ClassOrderID = $Row["ClassOrderID"];
	$MemberID = $Row["MemberID"];
	$TeacherID = $Row["TeacherID"];
	$ClassProductID = $Row["ClassProductID"];
	$CenterPayType = $Row["CenterPayType"];
	$MemberPayType = $Row["MemberPayType"];
 
	//0:미등록 1:출석 2:지각 3:결석 4:학생연기 5:강사연기 6:학생취소 7:강사취소 8:교사변경
	if ($ClassAttendState!=4 && $ClassAttendState!=5 && $ClassAttendState!=6 && $ClassAttendState!=7 && $ClassProductID==1){

		$ClassAttendStateMemberID = $_LINK_MEMBER_ID_;
		if ($_LINK_MEMBER_LEVEL_ID_==19){
			$ClassAttendState = 4;//학생연기
		}else{
			$ClassAttendState = 5;//강사연기
		}


		$Sql = " update Classes set ";
			$Sql .= " ClassAttendState = :ClassAttendState, ";
			$Sql .= " ClassAttendStateMemberID = :ClassAttendStateMemberID, ";
			$Sql .= " ClassAttendStateMsg='마지막수업 뒤로연기', ";
			$Sql .= " ClassModiDateTime = now() ";
		$Sql .= " where ClassID = :ClassID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ClassAttendState', $ClassAttendState);
		$Stmt->bindParam(':ClassAttendStateMemberID', $ClassAttendStateMemberID);
		$Stmt->bindParam(':ClassID', $ClassID);
		$Stmt->execute();
		$Stmt = null;

		if ($ii==1){
			$Sql = "
					select 
						A.* 
					from Classes A 
					where A.ClassID=".$ClassID." 
						and A.StartYear=".date("Y")." 
						and A.StartMonth=".date("n")." 
						and A.StartDay=".date("j")."
			";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;
			$OldTeacherID=$Row["TeacherID"];
			
			if ($OldTeacherID){
				$Sql = "select A.MemberID from Members A where A.TeacherID=$OldTeacherID and A.MemberLevelID=15";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$OldTeacherMemberID=$Row["MemberID"];
				
				InsertTeacherMessage($OldTeacherMemberID, 2, "Today's class schedule is changing. Please refresh the screen.");
			}
		}

		if ($CenterPayType==2 || ($CenterPayType==1 && $MemberPayType==1)){

			$Sql = "
				select
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
				
				$Sql = "
						select 
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
				$ListWeekNum = $ClassOrderEndDateWeek+1;
				$ListNum = 1;

				
				while($SetLastDate==0){
					
					if ($ListWeekNum>6){
						$ListWeekNum2 = $ListWeekNum - 7;
					}else{
						$ListWeekNum2 = $ListWeekNum;
					}

					//echo "{".$ListWeekNum2."/".$ExistStudyWeek[$ListWeekNum2]."}<br><br>";

					if ($ExistStudyWeek[$ListWeekNum2]==1){
						$ResetClassOrderEndDate = date("Y-m-d", strtotime($ClassOrderEndDate. " + ".$ListNum." days"));
						$SetLastDate=1;
					}

					$ListWeekNum++;
					$ListNum++;
				}

			}

			$Sql2 = "update ClassOrders set 
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
			$Sql_EndDateLog = " insert into ClassOrderEndDateLogs ( ";
				$Sql_EndDateLog .= " ClassOrderID, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogType, ";
				$Sql_EndDateLog .= " ClassOrderEndDate, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogFileName, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogFileQueryNum, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogRegDateTime ";
			$Sql_EndDateLog .= " ) values ( ";
				$Sql_EndDateLog .= " :ClassOrderID, ";
				$Sql_EndDateLog .= " '강의 연기에 따른 종료일 변경', ";
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

		}

	}
}


if ( $OldTeacherMemberID!=$NewTeacherMemberID && ( $ClassOrderResetApplyDate==date("Y-m-d") || $SetStudyTimeDate==date("Y-m-d") ) ){//보강, 연기, 변경 이고 그 날짜가 오늘일때
	InsertTeacherMessage($NewTeacherMemberID, 2, "Today's class schedule is changing. Please refresh the screen.");
}

if ($err_num != 0){
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
</body>
</html>
<?php
}

include_once('./includes/dbclose.php');


if ($err_num == 0){
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="./js/jquery-confirm.min.js"></script>
<link href="./css/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<script>
 	 
<?if ($FromPage=="Calendar"){?>
	top.location.reload();
<?}else{?>
	<?if ($FromDevice==""){?>
		parent.location.reload();
	<?}else{?>
		window.Exit=true;
		parent.$.fn.colorbox.close();
	<?}?>
<?}?>
// window.top.location.reload();
// window.open('','_self').close();  

if (top.location != window.location) top.location.reload();
if (window.opener) {window.opener.top.location.reload(); self.close();}
</script>
</body>
</html>
<?
}
?>