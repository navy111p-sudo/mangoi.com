<?php
// cron에서 실행하므로 include 파일들의 절대경로가 필요하다. 절대경로 확인하기
//$server_root_path = "/xampp/htdocs";

//=======================================================================================================
$EncryptionKey = "f2ffe2af6c94ba5c3b56b69658f5e471";//절대 변경 불가(변경되면 회원정보 복구 불가)
//=======================================================================================================

//include_once($server_root_path.'/includes/dbopen.php');
//include_once($server_root_path.'/includes/common.php');
//include_once('./includes/admin_check.php');
//include_once('./includes/common.php');

$type = "1";

$SearchState = $type;


$ArrWeekDayStr = explode(",","Sun.,Mon.,Tue.,Wed.,Thu.,Fri.,Sat.");

$AddSqlWhere = "1=1 ";

$SelectYear = date("Y");
$SelectMonth = date("m");
$SelectDay = date("d");


$SelectDate = $SelectYear."-".$SelectMonth."-".$SelectDay;

$SelectDateWeek = date('w', strtotime($SelectDate));
$WeekDayStr = $ArrWeekDayStr[$SelectDateWeek];

$PrevYear = date("Y", strtotime("-1 day", strtotime($SelectDate)));
$PrevMonth = date("m", strtotime("-1 day", strtotime($SelectDate)));
$PrevDay = date("d", strtotime("-1 day", strtotime($SelectDate)));

$NextYear = date("Y", strtotime("1 day", strtotime($SelectDate)));
$NextMonth = date("m", strtotime("1 day", strtotime($SelectDate)));
$NextDay = date("d", strtotime("1 day", strtotime($SelectDate)));


$SearchStartHour=0;
$SearchStartMinute=0;
$SearchEndHour=23;
$SearchEndMinute=50;
$ListParam = "";


$AddSqlWhere = $AddSqlWhere . " and MB.MemberState<>0 ";


//============================
$SearchStartHourMinute = substr("0".$SearchStartHour,-2) . substr("0".$SearchStartMinute,-2);
$ListParam = $ListParam . "&SearchStartHour=" . $SearchStartHour;
$ListParam = $ListParam . "&SearchStartMinute=" . $SearchStartMinute;
$AddSqlWhere = $AddSqlWhere . " and concat(lpad(COS.StudyTimeHour,2,0) , lpad(COS.StudyTimeMinute,2,0))>=$SearchStartHourMinute ";

$SearchEndHourMinute = substr("0".$SearchEndHour,-2) . substr("0".$SearchEndMinute,-2);
$ListParam = $ListParam . "&SearchEndHour=" . $SearchEndHour;
$ListParam = $ListParam . "&SearchEndMinute=" . $SearchEndMinute;
$AddSqlWhere = $AddSqlWhere . " and concat(lpad(COS.StudyTimeHour,2,0) , lpad(COS.StudyTimeMinute,2,0))<=$SearchEndHourMinute ";
//============================


$ListParam = $ListParam . "&SelectYear=" . $SelectYear;
$ListParam = $ListParam . "&SelectMonth=" . $SelectMonth;
$ListParam = $ListParam . "&SelectDay=" . $SelectDay;


$ListParam = $ListParam . "&type=" . $SearchState;


$AddSqlWhere = $AddSqlWhere . " and TEA.TeacherState=1 ";


$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotMaster=1 ";


$AddSqlWhere = $AddSqlWhere . " and ( 
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



$ViewTable = "SELECT  
		COS.ClassOrderSlotID,
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
		COS.ClassOrderSlotEndDate, 
		ifnull(COS.ClassOrderPayID,0) as ClassOrderPayID, 
		concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0)) as ClassStartTime, 

		CO.ClassMemberTypeGroupID,
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
		CLS.CommonShNewClassCode,

		MB.MemberName,
		MB.MemberPayType,
		MB.MemberChangeTeacher,
		MB.MemberNickName,
		MB.MemberLoginID, 
        MB.MemberLevelID,
		MB.MemberCiTelephone,

		AES_DECRYPT(UNHEX(MB.MemberPhone1),'$EncryptionKey') as DecMemberPhone1,

		TEA.TeacherName,
		MB2.MemberLoginID as TeacherLoginID, 
		MB2.MemberCiTelephone as TeacherCiTelephone,
		CT.CenterID as JoinCenterID,
		CT.CenterName as JoinCenterName,
		CT.MemberAcceptCallByTeacher,
		CT.CenterPayType,
		CT.CenterRenewType,
		CT.CenterStudyEndDate,

		BR.BranchID as JoinBranchID,
		BR.BranchName as JoinBranchName, 
		BRG.BranchGroupID as JoinBranchGroupID,
		BRG.BranchGroupName as JoinBranchGroupName,
		COM.CompanyID as JoinCompanyID,
		COM.CompanyName as JoinCompanyName,
		FR.FranchiseName,
		MB3.MemberLoginID as CenterLoginID,
		TEA2.TeacherName as ClassTeacherName,
		
		(select count(*) from Classes where ClassOrderID=COS.ClassOrderID and ClassState=2 and TIMESTAMPDIFF(minute, StartDateTime, CLS.StartDateTime)>0 and CLS.ClassState=2) as LastStudyClassCount

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



//오늘 수업 또는 선택하여 연기하기 일때 수업을 등록해 준다.
if (date("Y-m-d")==$SelectYear."-".substr("0".$SelectMonth,-2)."-".substr("0".$SelectDay,-2)  ||  $ListSelectResetDate=="1"){

	$SqlWhereCenterRenew = "";

	$Sql3 = "
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
			order by V.ClassMemberType asc, V.StudyTimeHour asc, V.StudyTimeMinute asc, V.TeacherID asc, V.ClassMemberTypeGroupID desc
	";


	$Stmt3 = $DbConn->prepare($Sql3);
	$Stmt3->execute();
	$Stmt3->setFetchMode(PDO::FETCH_ASSOC);

	$OldGroupStudyTimeHour = 0;
	$OldGroupStudyTimeMinute = 0;
	$OldGroupTeacherID = 0;
	$OldClassMemberTypeGroupID = 0;
	while($Row3 = $Stmt3->fetch()) {
		$ClassMemberType = $Row3["ClassMemberType"];
		$ClassMemberTypeGroupID = $Row3["ClassMemberTypeGroupID"];
		$ClassState = $Row3["ClassState"];
		$ClassOrderID = $Row3["ClassOrderID"];
		$StartHour = $Row3["StudyTimeHour"];
		$StartMinute = $Row3["StudyTimeMinute"];
		$ClassOrderTimeTypeID = $Row3["ClassOrderTimeTypeID"];
		$TeacherID = $Row3["TeacherID"];
		$MemberID = $Row3["MemberID"];
		$ClassOrderPayID = $Row3["ClassOrderPayID"];
		

		//================ 그룹 아이디 생성 ===================
		if ($ClassMemberType!=1) {

			if ($OldGroupStudyTimeHour!=$StartHour || $OldGroupStudyTimeMinute!=$StartMinute || $OldGroupTeacherID!=$TeacherID){
				$OldClassMemberTypeGroupID = 0;
			}


			if ($ClassMemberTypeGroupID==0){//아직 그룹 아이디가 등록 안되어 있으면..
				
				if ($OldClassMemberTypeGroupID==0){//첫번째 리스트 - 그룹아이디 새로생성

					$Sql_OG = "select max(ClassMemberTypeGroupID) as NewClassMemberTypeGroupID from ClassOrders";
					$Stmt_OG = $DbConn->prepare($Sql_OG);
					$Stmt_OG->execute();
					$Stmt_OG->setFetchMode(PDO::FETCH_ASSOC);
					$Row_OG = $Stmt_OG->fetch();
					$Stmt_OG = null;
					
					$NewClassMemberTypeGroupID = $Row_OG["NewClassMemberTypeGroupID"] + 1;

				}else{//기존것 넣기

					$NewClassMemberTypeGroupID = $OldClassMemberTypeGroupID;
				
				}

				$Sql_OG = "update ClassOrders set ClassMemberTypeGroupID=:ClassMemberTypeGroupID where ClassOrderID=:ClassOrderID";
				$Stmt_OG = $DbConn->prepare($Sql_OG);
				$Stmt_OG->bindParam(':ClassMemberTypeGroupID', $NewClassMemberTypeGroupID);
				$Stmt_OG->bindParam(':ClassOrderID', $ClassOrderID);
				$Stmt_OG->execute();
				$Stmt_OG = null;

				$ClassMemberTypeGroupID = $NewClassMemberTypeGroupID;
			}

			
			$OldClassMemberTypeGroupID = $ClassMemberTypeGroupID;
			$OldGroupStudyTimeHour = $StartHour;
			$OldGroupStudyTimeMinute = $StartMinute;
			$OldGroupTeacherID = $TeacherID;
		}
		//================ 그룹 아이디 생성 ===================

		if ($ClassState==0){

			$StartYear = $SelectYear;
			$StartMonth = $SelectMonth;
			$StartDay = $SelectDay;

			$Sql2 = "select 
					A.TeacherPayPerTime,
					A.TeacherName,
					B.MemberLoginID
				from 
					Teachers A 
						inner join Members B on A.TeacherID=B.TeacherID and B.MemberLevelID=15 
				where A.TeacherID=:TeacherID";
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':TeacherID', $TeacherID);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
			$Row2 = $Stmt2->fetch();
			$Stmt = null;
			$TeacherPayPerTime = $Row2["TeacherPayPerTime"];
			$TeacherName = $Row2["TeacherName"];
			$TeacherLoginID = $Row2["MemberLoginID"];


			if ($ClassOrderTimeTypeID==2){
				$PlusMinute = 20;
			}else if ($ClassOrderTimeTypeID==3){
				$PlusMinute = 30;
			}else if ($ClassOrderTimeTypeID==4){
				$PlusMinute = 40;
			}

			$EndMinute = $StartMinute + $PlusMinute;
			if ($EndMinute>=60){
				$EndMinute = $EndMinute - 60;
				$EndHour = $StartHour + 1;
			}else{
				$EndHour = $StartHour;
			}

			//종로시간이 24를 넘어가면 23시 59분으로 맞춘다.
			if ($EndHour>=24){
				$EndHour = 23;
				$EndMinute = 59;
			}
			//종로시간이 24를 넘어가면 23시 59분으로 맞춘다.


			$EndYear = $StartYear;
			$EndMonth = $StartMonth;
			$EndDay = $StartDay;



			$Sql2 = "select 
						ClassID, 
						CommonShClassCode 
					from Classes 
					where 
						ClassOrderID=".$ClassOrderID." 
						and MemberID=".$MemberID."
						and TeacherID=".$TeacherID."
						and StartYear=".$StartYear."
						and StartMonth=".$StartMonth."
						and StartDay=".$StartDay."
						and StartHour=".$StartHour."
						and StartMinute=".$StartMinute."

						and EndYear=".$EndYear."
						and EndMonth=".$EndMonth."
						and EndDay=".$EndDay."
						and EndHour=".$EndHour."
						and EndMinute=".$EndMinute." 

						and ClassAttendState<>99 

				";

			//echo $Sql2;
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':TeacherID', $TeacherID);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
			$Row2 = $Stmt2->fetch();
			$Stmt2 = null;
			$ClassID = $Row2["ClassID"];
			$CommonShClassCode = $Row2["CommonShClassCode"];

			if (!$ClassID){
				$StartDate = $StartYear."-".substr("0".$StartMonth,-2)."-".substr("0".$StartDay,-2);
				$CommonShClassCode = $TeacherID."_". str_replace("-","",$StartDate) ."_".$StartHour."_".$StartMinute;

				$StartDateTime = $StartYear."-".substr("0".$StartMonth,-2)."-".substr("0".$StartDay,-2)." ".substr("0".$StartHour,-2).":".substr("0".$StartMinute,-2).":00";
				$EndDateTime   = $EndYear.  "-".substr("0".$EndMonth,  -2)."-".substr("0".$EndDay,-2  )." ".substr("0".$EndHour,-2  ).":".substr("0".$EndMinute,-2  ).":00";

				$StartDateTimeStamp = strtotime($StartDateTime);
				$EndDateTimeStamp =   strtotime($EndDateTime);

				if($ClassOrderPayID==0) {
					$Sql2 = "SELECT 
							A.ClassOrderPayID 
						from ClassOrderPays A 
							inner join ClassOrderPayDetails B on A.ClassOrderPayID=B.ClassOrderPayID 
						where 
							B.ClassOrderID=:ClassOrderID 
							and 
							B.TeacherID=:TeacherID 
							and
							datediff(A.ClassOrderPayStartDate, now() ) < 0
						order by 
							A.ClassOrderPayID desc;
						limit
							0,1
					";

					$Stmt2 = $DbConn->prepare($Sql2);
					$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
					$Stmt2->bindParam(':TeacherID', $TeacherID);
					$Stmt2->execute();
					$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
					$Row2 = $Stmt2->fetch();
					$ClassOrderPayID = $Row2["ClassOrderPayID"];
					if($ClassOrderPayID==null) {
						$ClassOrderPayID = 0;
					}
					$Stmt2 = null;
				}

				$Sql2 = " INSERT into Classes ( ";
					$Sql2 .= " ClassOrderPayID, ";
					$Sql2 .= " ClassOrderID, ";
					$Sql2 .= " MemberID, ";
					$Sql2 .= " TeacherID, ";
					$Sql2 .= " TeacherPayPerTime, ";
					$Sql2 .= " StartDateTime, ";
					$Sql2 .= " StartDateTimeStamp, ";
					$Sql2 .= " StartYear, ";
					$Sql2 .= " StartMonth, ";
					$Sql2 .= " StartDay, ";
					$Sql2 .= " StartHour, ";
					$Sql2 .= " StartMinute, ";
					$Sql2 .= " EndDateTime, ";
					$Sql2 .= " EndDateTimeStamp, ";
					$Sql2 .= " EndYear, ";
					$Sql2 .= " EndMonth, ";
					$Sql2 .= " EndDay, ";
					$Sql2 .= " EndHour, ";
					$Sql2 .= " EndMinute, ";
					$Sql2 .= " CommonUseClassIn, ";
					$Sql2 .= " CommonShClassCode, ";
					$Sql2 .= " ClassRegDateTime, ";
					$Sql2 .= " ClassModiDateTime ";
				$Sql2 .= " ) values ( ";
					$Sql2 .= " :ClassOrderPayID, ";
					$Sql2 .= " :ClassOrderID, ";
					$Sql2 .= " :MemberID, ";
					$Sql2 .= " :TeacherID, ";
					$Sql2 .= " :TeacherPayPerTime, ";
					$Sql2 .= " :StartDateTime, ";
					$Sql2 .= " :StartDateTimeStamp, ";
					$Sql2 .= " :StartYear, ";
					$Sql2 .= " :StartMonth, ";
					$Sql2 .= " :StartDay, ";
					$Sql2 .= " :StartHour, ";
					$Sql2 .= " :StartMinute, ";
					$Sql2 .= " :EndDateTime, ";
					$Sql2 .= " :EndDateTimeStamp, ";
					$Sql2 .= " :EndYear, ";
					$Sql2 .= " :EndMonth, ";
					$Sql2 .= " :EndDay, ";
					$Sql2 .= " :EndHour, ";
					$Sql2 .= " :EndMinute, ";
					$Sql2 .= " 0, ";
					$Sql2 .= " :CommonShClassCode, ";
					$Sql2 .= " now(), ";
					$Sql2 .= " now() ";
				$Sql2 .= " ) ";

				$Stmt2 = $DbConn->prepare($Sql2);
				$Stmt2->bindParam(':ClassOrderPayID', $ClassOrderPayID);
				$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
				$Stmt2->bindParam(':MemberID', $MemberID);
				$Stmt2->bindParam(':TeacherID', $TeacherID);
				$Stmt2->bindParam(':TeacherPayPerTime', $TeacherPayPerTime);
				$Stmt2->bindParam(':StartDateTime', $StartDateTime);
				$Stmt2->bindParam(':StartDateTimeStamp', $StartDateTimeStamp);
				$Stmt2->bindParam(':StartYear', $StartYear);
				$Stmt2->bindParam(':StartMonth', $StartMonth);
				$Stmt2->bindParam(':StartDay', $StartDay);
				$Stmt2->bindParam(':StartHour', $StartHour);
				$Stmt2->bindParam(':StartMinute', $StartMinute);
				$Stmt2->bindParam(':EndDateTime', $EndDateTime);
				$Stmt2->bindParam(':EndDateTimeStamp', $EndDateTimeStamp);
				$Stmt2->bindParam(':EndYear', $EndYear);
				$Stmt2->bindParam(':EndMonth', $EndMonth);
				$Stmt2->bindParam(':EndDay', $EndDay);
				$Stmt2->bindParam(':EndHour', $EndHour);
				$Stmt2->bindParam(':EndMinute', $EndMinute);
				$Stmt2->bindParam(':CommonShClassCode', $CommonShClassCode);
				$Stmt2->execute();
				$ClassID = $DbConn->lastInsertId();
				$Stmt2 = null;
			}

		}

	}
	$Stmt3 = null;
						

}
//오늘 수업 또는 선택하여 연기하기 일때 수업을 등록해 준다.


?>