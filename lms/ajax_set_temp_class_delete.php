<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');


$SelectDate = isset($_REQUEST["SelectDate"]) ? $_REQUEST["SelectDate"] : "";
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$ClassOrderTimeTypeID = isset($_REQUEST["ClassOrderTimeTypeID"]) ? $_REQUEST["ClassOrderTimeTypeID"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$StudyTimeHour = isset($_REQUEST["StudyTimeHour"]) ? $_REQUEST["StudyTimeHour"] : "";
$StudyTimeMinute = isset($_REQUEST["StudyTimeMinute"]) ? $_REQUEST["StudyTimeMinute"] : "";


$ClassOrderSlotDateModiIP = $_SERVER["REMOTE_ADDR"];
$ClassOrderSlotDateModiMemberID = $_LINK_ADMIN_ID_;

//슬랏을 삭제
for ($ii=0;$ii<=$ClassOrderTimeTypeID-1;$ii++){

	$TempStudyTimeHour = $StudyTimeHour;
	$TempStudyTimeMinute = $StudyTimeMinute + ($ii*10);

	if ($TempStudyTimeMinute>=60){
		$TempStudyTimeMinute = $TempStudyTimeMinute - 60;
		$TempStudyTimeHour = $TempStudyTimeHour + 1;
	}

	$Sql = " update ClassOrderSlots set ";

		$Sql .= " ClassOrderSlotState = 0, ";
		$Sql .= " ClassOrderSlotDateModiMemberID = $ClassOrderSlotDateModiMemberID, ";
		$Sql .= " ClassOrderSlotDateModiDateTime = now(), ";
		$Sql .= " ClassOrderSlotDateModiIP = '".$ClassOrderSlotDateModiIP."' ";
	$Sql .= " where 
				ClassOrderSlotState=1
				and ClassOrderSlotType=2 
				and ClassOrderID=:ClassOrderID 
				and ClassOrderSlotDate=:ClassOrderSlotDate
				and TeacherID = :TeacherID 
				and StudyTimeHour = :StudyTimeHour 
				and StudyTimeMinute = :StudyTimeMinute 
			";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
	$Stmt->bindParam(':ClassOrderSlotDate', $SelectDate);
	$Stmt->bindParam(':TeacherID', $TeacherID);
	$Stmt->bindParam(':StudyTimeHour', $TempStudyTimeHour);
	$Stmt->bindParam(':StudyTimeMinute', $TempStudyTimeMinute);
	$Stmt->execute();
	$Stmt = null;
}

//클래스를 삭제
$Sql = " update Classes set ClassAttendState=99 where ClassID=:ClassID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt = null;



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