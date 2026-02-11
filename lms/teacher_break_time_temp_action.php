<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$TeacherBreakTimeTempID = isset($_REQUEST["TeacherBreakTimeTempID"]) ? $_REQUEST["TeacherBreakTimeTempID"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$TeacherBreakTimeTempStartDate = isset($_REQUEST["TeacherBreakTimeTempStartDate"]) ? $_REQUEST["TeacherBreakTimeTempStartDate"] : "";
$TeacherBreakTimeTempEndDate = isset($_REQUEST["TeacherBreakTimeTempEndDate"]) ? $_REQUEST["TeacherBreakTimeTempEndDate"] : "";
$TeacherBreakTimeTempWeek = isset($_REQUEST["TeacherBreakTimeTempWeek"]) ? $_REQUEST["TeacherBreakTimeTempWeek"] : "";
$TeacherBreakTimeTempStartHour = isset($_REQUEST["TeacherBreakTimeTempStartHour"]) ? $_REQUEST["TeacherBreakTimeTempStartHour"] : "";
$TeacherBreakTimeTempStartMinute = isset($_REQUEST["TeacherBreakTimeTempStartMinute"]) ? $_REQUEST["TeacherBreakTimeTempStartMinute"] : "";
$TeacherBreakTimeTempEndHour = isset($_REQUEST["TeacherBreakTimeTempEndHour"]) ? $_REQUEST["TeacherBreakTimeTempEndHour"] : "";
$TeacherBreakTimeTempEndMinute = isset($_REQUEST["TeacherBreakTimeTempEndMinute"]) ? $_REQUEST["TeacherBreakTimeTempEndMinute"] : "";
$TeacherBreakTimeTempType = isset($_REQUEST["TeacherBreakTimeTempType"]) ? $_REQUEST["TeacherBreakTimeTempType"] : "";
$DelTeacherBreakTimeTemp = isset($_REQUEST["DelTeacherBreakTimeTemp"]) ? $_REQUEST["DelTeacherBreakTimeTemp"] : "";

$ListParam = str_replace("^^", "&", $ListParam);

if ($DelTeacherBreakTimeTemp=="1"){
	$TeacherBreakTimeTempState = 0;
}else{
	$TeacherBreakTimeTempState = 1;
}

$TeacherBreakTimeTempStartTime = $TeacherBreakTimeTempStartHour . ":" . $TeacherBreakTimeTempStartMinute;
$TeacherBreakTimeTempEndTime = $TeacherBreakTimeTempEndHour . ":" . $TeacherBreakTimeTempEndMinute;

if ($TeacherBreakTimeTempID==""){

	$Sql = " insert into TeacherBreakTimeTemps ( ";
		$Sql .= " TeacherID, ";
		$Sql .= " TeacherBreakTimeTempStartDate, ";
		$Sql .= " TeacherBreakTimeTempEndDate, ";
		$Sql .= " TeacherBreakTimeTempWeek, ";
		$Sql .= " TeacherBreakTimeTempStartTime, ";
		$Sql .= " TeacherBreakTimeTempEndTime, ";
		$Sql .= " TeacherBreakTimeTempStartHour, ";
		$Sql .= " TeacherBreakTimeTempStartMinute, ";
		$Sql .= " TeacherBreakTimeTempEndHour, ";
		$Sql .= " TeacherBreakTimeTempEndMinute, ";
		$Sql .= " TeacherBreakTimeTempType, ";
		$Sql .= " TeacherBreakTimeTempRegDateTime, ";
		$Sql .= " TeacherBreakTimeTempModiDateTime, ";
		$Sql .= " TeacherBreakTimeTempState ";
	$Sql .= " ) values ( ";
		$Sql .= " :TeacherID, ";
		$Sql .= " :TeacherBreakTimeTempStartDate, ";
		$Sql .= " :TeacherBreakTimeTempEndDate, ";
		$Sql .= " :TeacherBreakTimeTempWeek, ";
		$Sql .= " :TeacherBreakTimeTempStartTime, ";
		$Sql .= " :TeacherBreakTimeTempEndTime, ";
		$Sql .= " :TeacherBreakTimeTempStartHour, ";
		$Sql .= " :TeacherBreakTimeTempStartMinute, ";
		$Sql .= " :TeacherBreakTimeTempEndHour, ";
		$Sql .= " :TeacherBreakTimeTempEndMinute, ";
		$Sql .= " :TeacherBreakTimeTempType, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " 1 ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherID', $TeacherID);
	$Stmt->bindParam(':TeacherBreakTimeTempStartDate', $TeacherBreakTimeTempStartDate);
	$Stmt->bindParam(':TeacherBreakTimeTempEndDate', $TeacherBreakTimeTempEndDate);
	$Stmt->bindParam(':TeacherBreakTimeTempWeek', $TeacherBreakTimeTempWeek);
	$Stmt->bindParam(':TeacherBreakTimeTempStartTime', $TeacherBreakTimeTempStartTime);
	$Stmt->bindParam(':TeacherBreakTimeTempEndTime', $TeacherBreakTimeTempEndTime);
	$Stmt->bindParam(':TeacherBreakTimeTempStartHour', $TeacherBreakTimeTempStartHour);
	$Stmt->bindParam(':TeacherBreakTimeTempStartMinute', $TeacherBreakTimeTempStartMinute);
	$Stmt->bindParam(':TeacherBreakTimeTempEndHour', $TeacherBreakTimeTempEndHour);
	$Stmt->bindParam(':TeacherBreakTimeTempEndMinute', $TeacherBreakTimeTempEndMinute);
	$Stmt->bindParam(':TeacherBreakTimeTempType', $TeacherBreakTimeTempType);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update TeacherBreakTimeTemps set ";
		$Sql .= " TeacherBreakTimeTempStartDate = :TeacherBreakTimeTempStartDate, ";
		$Sql .= " TeacherBreakTimeTempEndDate = :TeacherBreakTimeTempEndDate, ";
		$Sql .= " TeacherBreakTimeTempWeek = :TeacherBreakTimeTempWeek, ";
		$Sql .= " TeacherBreakTimeTempStartTime = :TeacherBreakTimeTempStartTime, ";
		$Sql .= " TeacherBreakTimeTempEndTime = :TeacherBreakTimeTempEndTime, ";
		$Sql .= " TeacherBreakTimeTempStartHour = :TeacherBreakTimeTempStartHour, ";
		$Sql .= " TeacherBreakTimeTempStartMinute = :TeacherBreakTimeTempStartMinute, ";
		$Sql .= " TeacherBreakTimeTempEndHour = :TeacherBreakTimeTempEndHour, ";
		$Sql .= " TeacherBreakTimeTempEndMinute = :TeacherBreakTimeTempEndMinute, ";
		$Sql .= " TeacherBreakTimeTempType = :TeacherBreakTimeTempType, ";
		$Sql .= " TeacherBreakTimeTempState = :TeacherBreakTimeTempState, ";
		$Sql .= " TeacherBreakTimeTempModiDateTime = now() ";
	$Sql .= " where TeacherBreakTimeTempID = :TeacherBreakTimeTempID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherBreakTimeTempStartDate', $TeacherBreakTimeTempStartDate);
	$Stmt->bindParam(':TeacherBreakTimeTempEndDate', $TeacherBreakTimeTempEndDate);
	$Stmt->bindParam(':TeacherBreakTimeTempWeek', $TeacherBreakTimeTempWeek);
	$Stmt->bindParam(':TeacherBreakTimeTempStartTime', $TeacherBreakTimeTempStartTime);
	$Stmt->bindParam(':TeacherBreakTimeTempEndTime', $TeacherBreakTimeTempEndTime);
	$Stmt->bindParam(':TeacherBreakTimeTempStartHour', $TeacherBreakTimeTempStartHour);
	$Stmt->bindParam(':TeacherBreakTimeTempStartMinute', $TeacherBreakTimeTempStartMinute);
	$Stmt->bindParam(':TeacherBreakTimeTempEndHour', $TeacherBreakTimeTempEndHour);
	$Stmt->bindParam(':TeacherBreakTimeTempEndMinute', $TeacherBreakTimeTempEndMinute);
	$Stmt->bindParam(':TeacherBreakTimeTempType', $TeacherBreakTimeTempType);
	$Stmt->bindParam(':TeacherBreakTimeTempState', $TeacherBreakTimeTempState);
	$Stmt->bindParam(':TeacherBreakTimeTempID', $TeacherBreakTimeTempID);
	
	$Stmt->execute();
	$Stmt = null;

}


include_once('../includes/dbclose.php');
?>
<script>
//parent.$.fn.colorbox.close();
parent.location.href = "teacher_form.php?<?=$ListParam?>&TeacherID=<?=$TeacherID?>&PageTabID=6";
</script>

