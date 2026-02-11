<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$TeacherHolidayID = isset($_REQUEST["TeacherHolidayID"]) ? $_REQUEST["TeacherHolidayID"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$TeacherHolidayDate = isset($_REQUEST["TeacherHolidayDate"]) ? $_REQUEST["TeacherHolidayDate"] : "";
$TeacherHolidayName = isset($_REQUEST["TeacherHolidayName"]) ? $_REQUEST["TeacherHolidayName"] : "";
$DelTeacherHoliday = isset($_REQUEST["DelTeacherHoliday"]) ? $_REQUEST["DelTeacherHoliday"] : "";

$ListParam = str_replace("^^", "&", $ListParam);

if ($DelTeacherHoliday=="1"){
	$TeacherHolidayState = 0;
}else{
	$TeacherHolidayState = 1;
}


if ($TeacherHolidayID==""){
	$Sql = "
			select 
					A.TeacherHolidayID
			from TeacherHolidays A 
			where A.TeacherID=:TeacherID and TeacherHolidayDate=:TeacherHolidayDate";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherID', $TeacherID);
	$Stmt->bindParam(':TeacherHolidayDate', $TeacherHolidayDate);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TeacherHolidayID = $Row["TeacherHolidayID"];
}


if ($TeacherHolidayID==""){

	$Sql = " insert into TeacherHolidays ( ";
		$Sql .= " TeacherID, ";
		$Sql .= " TeacherHolidayDate, ";
		$Sql .= " TeacherHolidayName, ";
		$Sql .= " TeacherHolidayRegDateTime, ";
		$Sql .= " TeacherHolidayModiDateTime, ";
		$Sql .= " TeacherHolidayState ";
	$Sql .= " ) values ( ";
		$Sql .= " :TeacherID, ";
		$Sql .= " :TeacherHolidayDate, ";
		$Sql .= " :TeacherHolidayName, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " 1 ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherID', $TeacherID);
	$Stmt->bindParam(':TeacherHolidayDate', $TeacherHolidayDate);
	$Stmt->bindParam(':TeacherHolidayName', $TeacherHolidayName);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update TeacherHolidays set ";
		$Sql .= " TeacherHolidayName = :TeacherHolidayName, ";
		$Sql .= " TeacherHolidayState = :TeacherHolidayState, ";
		$Sql .= " TeacherHolidayModiDateTime = now() ";
	$Sql .= " where TeacherHolidayID = :TeacherHolidayID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherHolidayName', $TeacherHolidayName);
	$Stmt->bindParam(':TeacherHolidayState', $TeacherHolidayState);
	$Stmt->bindParam(':TeacherHolidayID', $TeacherHolidayID);
	$Stmt->execute();
	$Stmt = null;

}


include_once('../includes/dbclose.php');
?>
<script>
//parent.$.fn.colorbox.close();
parent.location.href = "teacher_form.php?<?=$ListParam?>&TeacherID=<?=$TeacherID?>&PageTabID=3";
</script>

