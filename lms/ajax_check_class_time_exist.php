<?php

header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";

$CheckResult = 0;
// 되살리고자 하는 수업의 정보
$Sql = "
		select 
			A.ClassOrderStartDate,
			A.ClassOrderState,
			A.ClassProgress,
			A.ClassMemberTypeGroupID,
			B.TeacherID,
			B.StudyTimeWeek,
			B.StudyTimeHour,
			B.StudyTimeMinute,
			B.ClassOrderSlotState
		from ClassOrders A 
			inner join ClassOrderSlots B on A.ClassOrderID=B.ClassOrderID 
		where
			A.ClassOrderID=:ClassOrderID
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

while($Row = $Stmt->fetch() ) {
	$ClassOrderStartDate = $Row["ClassOrderStartDate"];
	$ClassOrderState = $Row["ClassOrderState"];
	$ClassProgress = $Row["ClassProgress"];
	$TeacherID = $Row["TeacherID"];
	$StudyTimeWeek = $Row["StudyTimeWeek"];
	$StudyTimeHour = $Row["StudyTimeHour"];
	$StudyTimeMinute = $Row["StudyTimeMinute"];
	$ClassOrderSlotState = $Row["ClassOrderSlotState"];
	$ClassMemberTypeGroupID = $Row["ClassMemberTypeGroupID"];


	$SelectYear = date("Y");
	$SelectMonth = date("m");
	$SelectDay = date("d");

	$SelectDate = $SelectYear."-".$SelectMonth."-".$SelectDay;

	$Sql2 = "
			select 
				count(*) as TotalRowCount,
				COS.ClassOrderSlotType,
				COS.ClassOrderSlotDate,
				MB.MemberName,
				CO.ClassOrderStartDate
			from ClassOrders CO 
				inner join ClassOrderSlots COS on CO.ClassOrderID=COS.ClassOrderID 
				inner join Members MB on CO.MemberID=MB.MemberID 
			where
				COS.ClassOrderSlotState = 1
				and COS.TeacherID=".$TeacherID." 
				and COS.StudyTimeWeek=".$StudyTimeWeek."
				and COS.StudyTimeHour=".$StudyTimeHour."
				and COS.StudyTimeMinute=".$StudyTimeMinute."
				and COS.ClassOrderSlotState=1
				and CO.ClassOrderState=1
				and CO.ClassProgress=11
				and 
					(
						(COS.ClassOrderSlotType=1 and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 ) 

						or 
						(COS.ClassOrderSlotType=1 and COS.ClassOrderSlotEndDate is null)
					)
				and
				(
					(COS.ClassMemberType=2 or COS.ClassMemberType=3)
					and datediff(COS.ClassOrderSlotDate, '".$SelectDate."') >=0 
					and CO.ClassMemberTypeGroupID<>".$ClassMemberTypeGroupID."
				)";
/*
					or
					(
						(COS.ClassOrderSlotType=2 or COS.ClassOrderSlotType=3)
						and datediff(COS.ClassOrderSlotDate, '".$SelectDate."') >=0 
						and CO.ClassMemberTypeGroupID=".$ClassMemberTypeGroupID."
					)
				) 
				*/
	$Stmt2 = $DbConn->prepare($Sql2);
	//$Stmt2->bindParam(':SelectDate', $SelectDate);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	$TotalRowCount = $Row2["TotalRowCount"];

	if($TotalRowCount > 0 ) {
		// 
		$CheckResult = 1;
		$ClassOrderSlotType = $Row2["ClassOrderSlotType"];
		$ClassOrderSlotDate = $Row2["ClassOrderSlotDate"];
		$MemberName = $Row2["MemberName"];
		$ClassOrderStartDate = $Row2["ClassOrderStartDate"];

		$ArrValue["ClassOrderSlotType"] = $ClassOrderSlotType;
		$ArrValue["ClassOrderSlotDate"] = $ClassOrderSlotDate;
		$ArrValue["MemberName"] = $MemberName;
		$ArrValue["ClassOrderStartDate"] = $ClassOrderStartDate;

	}
	$Stmt2 = null;
}
$Stmt = null;

$ArrValue["CheckResult"] = $CheckResult;


$QueryResult = my_json_encode($ArrValue);
echo $QueryResult;


function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');

?>

