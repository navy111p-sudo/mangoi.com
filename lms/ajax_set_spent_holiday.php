 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');


$StaffHolidayID = isset($_REQUEST["StaffHolidayID"]) ? $_REQUEST["StaffHolidayID"] : "";
$SpentDays = isset($_REQUEST["SpentDays"]) ? $_REQUEST["SpentDays"] : 0;
$Reason = isset($_REQUEST["Reason"]) ? $_REQUEST["Reason"] : "";
$StartDate = isset($_REQUEST["StartDate"]) ? $_REQUEST["StartDate"] : "";
$EndDate = isset($_REQUEST["EndDate"]) ? $_REQUEST["EndDate"] : "";
$HolidayType = isset($_REQUEST["HolidayType"]) ? $_REQUEST["HolidayType"] : "";
$DocumentReportID = mt_rand(100000,1000000);


$Sql = "INSERT into SpentHoliday ( 
			StaffHolidayID,
			SpentDays,
			Reason,
			RegistDate,
			StartDate,
			EndDate,
			HolidayType,
			DocumentReportID)
			VALUES (
			:StaffHolidayID, 
			:SpentDays, 
			:Reason, 
			NOW(), 
			:StartDate, 
			:EndDate,
			:HolidayType,
			:DocumentReportID)";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':StaffHolidayID', $StaffHolidayID);
$Stmt->bindParam(':SpentDays', $SpentDays);
$Stmt->bindParam(':Reason', $Reason);
$Stmt->bindParam(':StartDate', $StartDate);
$Stmt->bindParam(':EndDate', $EndDate);
$Stmt->bindParam(':HolidayType', $HolidayType);
$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
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