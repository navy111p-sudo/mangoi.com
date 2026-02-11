<?php
//먼저 실행
//UPDATE ClassOrderSlots_2 SET MemberID=IFNULL((SELECT MemberID FROM Members WHERE MemberLoginID=ClassOrderSlots_2.MemberLoginID AND OnlineSiteCode=ClassOrderSlots_2.OnlineSiteCode AND MemberState=1),0);
//UPDATE ClassOrderSlots_2 SET TeacherID=IFNULL((SELECT TeacherID FROM Teachers WHERE TeacherCode=ClassOrderSlots_2.TeacherCode AND TeacherState=1),0);

//10분짜리 수업 지우기
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
			where A.MemberID<>0 and A.TeacherID=49 
		order by A.ClassOrderSlotID asc 
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListCount = 1;
while($Row = $Stmt->fetch()) {

	$ClassOrderSlotID = $Row["ClassOrderSlotID"];
	$ClassMemberType = $Row["ClassMemberType"];
	$StudyTimeWeek = $Row["StudyTimeWeek"];
	$StudyTimeHour = $Row["StudyTimeHour"];
	$StudyTimeMinute = $Row["StudyTimeMinute"];
	$MemberID = $Row["MemberID"];
	$TeacherID = $Row["TeacherID"];
	$OnlineSiteCode = $Row["OnlineSiteCode"];

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
	$RowCount = $Row2["RowCount"];

	if ($RowCount==1){

		$Sql2 = " update ClassOrderSlots_2 set DeleteTag=1 where ClassOrderSlotID=$ClassOrderSlotID ";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->execute();
		$Stmt2 = null;
		
	}

}
$Stmt = null;
?>

</body>
</html>
<?
include_once('../includes/dbclose.php');
?>