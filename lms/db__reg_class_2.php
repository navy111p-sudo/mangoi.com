<?php
// 슬랏 마스터 설정하기 (수업의 첫번째 슬랏)
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

$ListCount = 0;
$OldMemberID = 0;
$OldStudyTimeWeek = 0;
$OldTeacherID = 0;
while($Row = $Stmt->fetch()) {

	$ClassOrderSlotID = $Row["ClassOrderSlotID"];
	$ClassMemberType = $Row["ClassMemberType"];
	$StudyTimeWeek = $Row["StudyTimeWeek"];
	$StudyTimeHour = $Row["StudyTimeHour"];
	$StudyTimeMinute = $Row["StudyTimeMinute"];
	$MemberID = $Row["MemberID"];
	$TeacherID = $Row["TeacherID"];
	$MemberLoginID = $Row["MemberLoginID"];
	$OnlineSiteCode = $Row["OnlineSiteCode"];


	if ($OldMemberID != $MemberID){

		

		$Sql2 = " update ClassOrderSlots_2 set ClassOrderMaster=1 where ClassOrderSlotID=$ClassOrderSlotID ";

		echo $OldMemberID ." - ".$MemberID. " - ". $MemberLoginID. " : ".$Sql2."<br>";

		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->execute();
		$Stmt2 = null;

		$ListCount++;
		
	}

	if ($OldMemberID != $MemberID || $OldStudyTimeWeek != $StudyTimeWeek || $OldTeacherID != $TeacherID){

		$Sql2 = " update ClassOrderSlots_2 set ClassOrderSlotMaster=1 where ClassOrderSlotID=$ClassOrderSlotID ";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->execute();
		$Stmt2 = null;
		
	}

	$OldMemberID = $MemberID;
	$OldStudyTimeWeek = $StudyTimeWeek;
	$OldTeacherID = $TeacherID;
}
$Stmt = null;
?>

<?=$ListCount?>
</body>
</html>
<?
include_once('../includes/dbclose.php');
?>