<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$EduCenterHolidayID = isset($_REQUEST["EduCenterHolidayID"]) ? $_REQUEST["EduCenterHolidayID"] : "";
$EduCenterID = isset($_REQUEST["EduCenterID"]) ? $_REQUEST["EduCenterID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$EduCenterHolidayDate = isset($_REQUEST["EduCenterHolidayDate"]) ? $_REQUEST["EduCenterHolidayDate"] : "";
$EduCenterHolidayName = isset($_REQUEST["EduCenterHolidayName"]) ? $_REQUEST["EduCenterHolidayName"] : "";
$DelEduCenterHoliday = isset($_REQUEST["DelEduCenterHoliday"]) ? $_REQUEST["DelEduCenterHoliday"] : "";

if ($DelEduCenterHoliday=="1"){
	$EduCenterHolidayState = 0;
}else{
	$EduCenterHolidayState = 1;
}


if ($EduCenterHolidayID==""){
	$Sql = "
			select 
					A.EduCenterHolidayID
			from EduCenterHolidays A 
			where A.EduCenterID=:EduCenterID and EduCenterHolidayDate=:EduCenterHolidayDate";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EduCenterID', $EduCenterID);
	$Stmt->bindParam(':EduCenterHolidayDate', $EduCenterHolidayDate);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$EduCenterHolidayID = $Row["EduCenterHolidayID"];
}


if ($EduCenterHolidayID==""){

	$Sql = " insert into EduCenterHolidays ( ";
		$Sql .= " EduCenterID, ";
		$Sql .= " EduCenterHolidayDate, ";
		$Sql .= " EduCenterHolidayName, ";
		$Sql .= " EduCenterHolidayRegDateTime, ";
		$Sql .= " EduCenterHolidayModiDateTime, ";
		$Sql .= " EduCenterHolidayState ";
	$Sql .= " ) values ( ";
		$Sql .= " :EduCenterID, ";
		$Sql .= " :EduCenterHolidayDate, ";
		$Sql .= " :EduCenterHolidayName, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " 1 ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EduCenterID', $EduCenterID);
	$Stmt->bindParam(':EduCenterHolidayDate', $EduCenterHolidayDate);
	$Stmt->bindParam(':EduCenterHolidayName', $EduCenterHolidayName);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update EduCenterHolidays set ";
		$Sql .= " EduCenterHolidayName = :EduCenterHolidayName, ";
		$Sql .= " EduCenterHolidayState = :EduCenterHolidayState, ";
		$Sql .= " EduCenterHolidayModiDateTime = now() ";
	$Sql .= " where EduCenterHolidayID = :EduCenterHolidayID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EduCenterHolidayName', $EduCenterHolidayName);
	$Stmt->bindParam(':EduCenterHolidayState', $EduCenterHolidayState);
	$Stmt->bindParam(':EduCenterHolidayID', $EduCenterHolidayID);
	$Stmt->execute();
	$Stmt = null;

}


include_once('../includes/dbclose.php');
?>
<script>
//parent.$.fn.colorbox.close();
parent.location.href = "edu_center_form.php?<?=$ListParam?>&EduCenterID=<?=$EduCenterID?>&PageTabID=3";
</script>

