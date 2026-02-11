<?php
//수강신청 생성하고 실제 슬랏에 입력하기(추가일경우 기존 OrderID 알아둘것)
//include_once('../includes/dbopen.php');
include_once('../includes/common.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<body>
<?
$Sql = "
		select 
			A.*
		from ClassOrderSlots_2 A 
			where A.MemberID<>0 and A.TeacherID=49 and DeleteTag<>1
		order by A.MemberID ASC, A.StudyTimeWeek ASC, A.StudyTimeHour ASC, A.StudyTimeMinute ASC
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListCount = 1;
$OldMemberID = 0;
$OldStudyTimeWeek = 0;
$OldTeacherID = 0;
$ClassOrderCount = 0;
while($Row = $Stmt->fetch()) {

	$ClassOrderSlotID = $Row["ClassOrderSlotID"];
	$ClassMemberType = $Row["ClassMemberType"];
	$StudyTimeWeek = $Row["StudyTimeWeek"];
	$StudyTimeHour = $Row["StudyTimeHour"];
	$StudyTimeMinute = $Row["StudyTimeMinute"];
	$MemberID = $Row["MemberID"];
	$TeacherID = $Row["TeacherID"];
	$OnlineSiteCode = $Row["OnlineSiteCode"];
	$ClassOrderMaster = $Row["ClassOrderMaster"];
	$ClassOrderSlotMaster = $Row["ClassOrderSlotMaster"];

	if ($ClassOrderMaster==1){

		$Sql2 = "
				select 
					count(*) as RowCount
				from ClassOrderSlots_2 A 
					where A.ClassMemberType=$ClassMemberType 
						and A.StudyTimeWeek=$StudyTimeWeek 
						and A.MemberID=$MemberID 
						and A.TeacherID=$TeacherID 
						and A.OnlineSiteCode='$OnlineSiteCode'
				";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		$Row2 = $Stmt2->fetch();	
		$Stmt2 = null;
		$ClassOrderTimeTypeID = $Row2["RowCount"];


		$Sql2 = "
				select 
					count(*) as RowCount
				from ClassOrderSlots_2 A 
					where A.ClassMemberType=$ClassMemberType 
						and A.ClassOrderSlotMaster=1
						and A.MemberID=$MemberID 
						and A.TeacherID=$TeacherID 
						and A.OnlineSiteCode='$OnlineSiteCode'
				";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		$Row2 = $Stmt2->fetch();	
		$Stmt2 = null;
		$ClassOrderWeekCountID = $Row2["RowCount"];


		$ClassProductID = 1;
		$ClassOrderLeveltestApplyTypeID = 1;
		$ClassOrderRequestText = "";
		$ClassOrderStartDate = "2019-10-28";
		$ClassOrderState = 1;
		$ClassProgress = 11;

		$Sql2 = " insert into ClassOrders ( ";
			$Sql2 .= " ClassProductID, ";
			$Sql2 .= " ClassOrderLeveltestApplyTypeID, ";
			$Sql2 .= " ClassOrderTimeTypeID, ";
			$Sql2 .= " ClassOrderWeekCountID, ";
			$Sql2 .= " MemberID, ";
			$Sql2 .= " ClassOrderRequestText, ";
			$Sql2 .= " ClassOrderStartDate, ";
			$Sql2 .= " ClassOrderState, ";
			$Sql2 .= " ClassMemberType, ";
			$Sql2 .= " ClassProgress, ";
			$Sql2 .= " ClassOrderRegDateTime, ";
			$Sql2 .= " ClassOrderModiDateTime ";
		$Sql2 .= " ) values ( ";
			$Sql2 .= " :ClassProductID, ";
			$Sql2 .= " :ClassOrderLeveltestApplyTypeID, ";
			$Sql2 .= " :ClassOrderTimeTypeID, ";
			$Sql2 .= " :ClassOrderWeekCountID, ";
			$Sql2 .= " :MemberID, ";
			$Sql2 .= " :ClassOrderRequestText, ";
			$Sql2 .= " :ClassOrderStartDate, ";
			$Sql2 .= " :ClassOrderState, ";
			$Sql2 .= " :ClassMemberType, ";
			$Sql2 .= " :ClassProgress, ";
			$Sql2 .= " now(), ";
			$Sql2 .= " now() ";
		$Sql2 .= " ) ";
		
		$ClassOrderCount++;


		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':ClassProductID', $ClassProductID);
		$Stmt2->bindParam(':ClassOrderLeveltestApplyTypeID', $ClassOrderLeveltestApplyTypeID);
		$Stmt2->bindParam(':ClassOrderTimeTypeID', $ClassOrderTimeTypeID);
		$Stmt2->bindParam(':ClassOrderWeekCountID', $ClassOrderWeekCountID);
		$Stmt2->bindParam(':MemberID', $MemberID);
		$Stmt2->bindParam(':ClassOrderRequestText', $ClassOrderRequestText);
		$Stmt2->bindParam(':ClassOrderStartDate', $ClassOrderStartDate);
		$Stmt2->bindParam(':ClassOrderState', $ClassOrderState);
		$Stmt2->bindParam(':ClassMemberType', $ClassMemberType);
		$Stmt2->bindParam(':ClassProgress', $ClassProgress);
		$Stmt2->execute();
		$ClassOrderID = $DbConn->lastInsertId();
		$Stmt2 = null;

	}


	$ClassOrderSlotType =1;

	$Sql2 = " insert into ClassOrderSlots ( ";
		$Sql2 .= " ClassMemberType, ";
		$Sql2 .= " ClassOrderSlotType, ";
		$Sql2 .= " TeacherID, ";
		$Sql2 .= " ClassOrderID, ";
		$Sql2 .= " ClassOrderSlotMaster, ";
		$Sql2 .= " StudyTimeWeek, ";
		$Sql2 .= " StudyTimeHour, ";
		$Sql2 .= " StudyTimeMinute, ";
		$Sql2 .= " ClassOrderSlotState ";
	$Sql2 .= " ) values ( ";
		$Sql2 .= " :ClassMemberType, ";
		$Sql2 .= " :ClassOrderSlotType, ";
		$Sql2 .= " :TeacherID, ";
		$Sql2 .= " :ClassOrderID, ";
		$Sql2 .= " :ClassOrderSlotMaster, ";
		$Sql2 .= " :StudyTimeWeek, ";
		$Sql2 .= " :StudyTimeHour, ";
		$Sql2 .= " :StudyTimeMinute, ";
		$Sql2 .= " 1 ";
	$Sql2 .= " ) ";

	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':ClassMemberType', $ClassMemberType);
	$Stmt2->bindParam(':ClassOrderSlotType', $ClassOrderSlotType);
	$Stmt2->bindParam(':TeacherID', $TeacherID);
	$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
	$Stmt2->bindParam(':ClassOrderSlotMaster', $ClassOrderSlotMaster);
	$Stmt2->bindParam(':StudyTimeWeek', $StudyTimeWeek);
	$Stmt2->bindParam(':StudyTimeHour', $StudyTimeHour);
	$Stmt2->bindParam(':StudyTimeMinute', $StudyTimeMinute);
	$Stmt2->execute();
	$Stmt2 = null;


}
$Stmt = null;
?>


수강신청 개수 : <?=$ClassOrderCount?>
</body>
</html>
<?
include_once('../includes/dbclose.php');
?>