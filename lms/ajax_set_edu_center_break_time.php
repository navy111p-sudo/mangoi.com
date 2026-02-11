 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$EduCenterBreakTimeWeek = isset($_REQUEST["EduCenterBreakTimeWeek"]) ? $_REQUEST["EduCenterBreakTimeWeek"] : "";
$EduCenterBreakTimeHour = isset($_REQUEST["EduCenterBreakTimeHour"]) ? $_REQUEST["EduCenterBreakTimeHour"] : "";
$EduCenterBreakTimeMinute = isset($_REQUEST["EduCenterBreakTimeMinute"]) ? $_REQUEST["EduCenterBreakTimeMinute"] : "";
$EduCenterBreakTimeType = isset($_REQUEST["EduCenterBreakTimeType"]) ? $_REQUEST["EduCenterBreakTimeType"] : "";
$EduCenterID = isset($_REQUEST["EduCenterID"]) ? $_REQUEST["EduCenterID"] : "";

$Sql = "select count(*) as ExistCount from EduCenterBreakTimes where EduCenterID=:EduCenterID and EduCenterBreakTimeWeek=:EduCenterBreakTimeWeek and EduCenterBreakTimeHour=:EduCenterBreakTimeHour and EduCenterBreakTimeMinute=:EduCenterBreakTimeMinute";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EduCenterID', $EduCenterID);
$Stmt->bindParam(':EduCenterBreakTimeWeek', $EduCenterBreakTimeWeek);
$Stmt->bindParam(':EduCenterBreakTimeHour', $EduCenterBreakTimeHour);
$Stmt->bindParam(':EduCenterBreakTimeMinute', $EduCenterBreakTimeMinute);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ExistCount = $Row["ExistCount"];

if ($ExistCount==0){
	
	$Sql = " insert into EduCenterBreakTimes ( ";
		$Sql .= " EduCenterID, ";
		$Sql .= " EduCenterBreakTimeWeek, ";
		$Sql .= " EduCenterBreakTimeHour, ";
		$Sql .= " EduCenterBreakTimeMinute, ";
		$Sql .= " EduCenterBreakTimeType, ";
		$Sql .= " EduCenterBreakTimeRegDateTime, ";
		$Sql .= " EduCenterBreakTimeModiDateTime, ";
		$Sql .= " EduCenterBreakTimeState ";
	$Sql .= " ) values ( ";
		$Sql .= " :EduCenterID, ";
		$Sql .= " :EduCenterBreakTimeWeek, ";
		$Sql .= " :EduCenterBreakTimeHour, ";
		$Sql .= " :EduCenterBreakTimeMinute, ";
		$Sql .= " :EduCenterBreakTimeType, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " 1 ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EduCenterID', $EduCenterID);
	$Stmt->bindParam(':EduCenterBreakTimeWeek', $EduCenterBreakTimeWeek);
	$Stmt->bindParam(':EduCenterBreakTimeHour', $EduCenterBreakTimeHour);
	$Stmt->bindParam(':EduCenterBreakTimeMinute', $EduCenterBreakTimeMinute);
	$Stmt->bindParam(':EduCenterBreakTimeType', $EduCenterBreakTimeType);
	$Stmt->execute();
	$Stmt = null;

}else{
	$Sql = " update EduCenterBreakTimes set EduCenterBreakTimeType=:EduCenterBreakTimeType where EduCenterID=:EduCenterID and EduCenterBreakTimeWeek=:EduCenterBreakTimeWeek and EduCenterBreakTimeHour=:EduCenterBreakTimeHour and EduCenterBreakTimeMinute=:EduCenterBreakTimeMinute ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EduCenterID', $EduCenterID);
	$Stmt->bindParam(':EduCenterBreakTimeWeek', $EduCenterBreakTimeWeek);
	$Stmt->bindParam(':EduCenterBreakTimeHour', $EduCenterBreakTimeHour);
	$Stmt->bindParam(':EduCenterBreakTimeMinute', $EduCenterBreakTimeMinute);
	$Stmt->bindParam(':EduCenterBreakTimeType', $EduCenterBreakTimeType);
	$Stmt->execute();
	$Stmt = null;
}

$ArrValue["CheckResult"] = "";

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>