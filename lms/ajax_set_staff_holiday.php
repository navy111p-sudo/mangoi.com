 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');


$ListCount = isset($_REQUEST["ListCount"]) ? $_REQUEST["ListCount"] : 0;
$Year = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$i=1;

// 데이터의 갯수만큼 반복한다.
while ($i<=$ListCount) {

	$StaffID = isset($_REQUEST["StaffID_$i"]) ? $_REQUEST["StaffID_$i"] : "";
	$StartDate = isset($_REQUEST["StartDate_$i"]) ? $_REQUEST["StartDate_$i"] : NULL;
	$StaffHoliday = isset($_REQUEST["StaffHoliday_$i"]) ? $_REQUEST["StaffHoliday_$i"] : "";
	$StaffSickLeave = isset($_REQUEST["StaffSickLeave_$i"]) ? $_REQUEST["StaffSickLeave_$i"] : "";
	
	if ($StartDate == '') $StartDate = NULL;

	// StaffHoliday 테이블에 같은 년도의 같은 스태프id를 가진 레코드가 있는지 확인한다.
	$Sql = "SELECT COUNT(*) AS countIs 
				from StaffHoliday 
				where StaffID = $StaffID and Year = $Year";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	
	$countIs = $Row["countIs"];
	

	if ($countIs > 0){
		$Sql = "UPDATE StaffHoliday  
					SET 
					    StartDate = :StartDate,
					    MaxHoliday = :MaxHoliday,
					    MaxSickLeave = :MaxSickLeave   
					where StaffID = :StaffID and Year = :Year";
	
	} else {

		$Sql = "INSERT into StaffHoliday ( 
					StaffID,
					Year,
					MaxHoliday,
					MaxSickLeave,
					StartDate)
					VALUES (:StaffID, :Year, :MaxHoliday, :MaxSickLeave, :StartDate)";

		
	}
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':StaffID', $StaffID);
	$Stmt->bindParam(':Year', $Year);
	$Stmt->bindParam(':MaxHoliday', $StaffHoliday);
	$Stmt->bindParam(':MaxSickLeave', $StaffSickLeave);
	$Stmt->bindParam(':StartDate', $StartDate);
	$Stmt->execute();
	$Stmt = null;
	$i++;
}
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