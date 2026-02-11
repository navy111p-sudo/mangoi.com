 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$TeacherBreakTimeWeek = isset($_REQUEST["TeacherBreakTimeWeek"]) ? $_REQUEST["TeacherBreakTimeWeek"] : "";
$TeacherBreakTimeHour = isset($_REQUEST["TeacherBreakTimeHour"]) ? $_REQUEST["TeacherBreakTimeHour"] : "";
$TeacherBreakTimeMinute = isset($_REQUEST["TeacherBreakTimeMinute"]) ? $_REQUEST["TeacherBreakTimeMinute"] : "";
$TeacherBreakTimeType = isset($_REQUEST["TeacherBreakTimeType"]) ? $_REQUEST["TeacherBreakTimeType"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";

$Sql = "select count(*) as ExistCount from TeacherBreakTimes where TeacherID=:TeacherID and TeacherBreakTimeWeek=:TeacherBreakTimeWeek and TeacherBreakTimeHour=:TeacherBreakTimeHour and TeacherBreakTimeMinute=:TeacherBreakTimeMinute";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherID', $TeacherID);
$Stmt->bindParam(':TeacherBreakTimeWeek', $TeacherBreakTimeWeek);
$Stmt->bindParam(':TeacherBreakTimeHour', $TeacherBreakTimeHour);
$Stmt->bindParam(':TeacherBreakTimeMinute', $TeacherBreakTimeMinute);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ExistCount = $Row["ExistCount"];

if ($ExistCount==0){
	
	$Sql = " insert into TeacherBreakTimes ( ";
		$Sql .= " TeacherID, ";
		$Sql .= " TeacherBreakTimeWeek, ";
		$Sql .= " TeacherBreakTimeHour, ";
		$Sql .= " TeacherBreakTimeMinute, ";
		$Sql .= " TeacherBreakTimeType, ";
		$Sql .= " TeacherBreakTimeRegDateTime, ";
		$Sql .= " TeacherBreakTimeModiDateTime, ";
		$Sql .= " TeacherBreakTimeState ";
	$Sql .= " ) values ( ";
		$Sql .= " :TeacherID, ";
		$Sql .= " :TeacherBreakTimeWeek, ";
		$Sql .= " :TeacherBreakTimeHour, ";
		$Sql .= " :TeacherBreakTimeMinute, ";
		$Sql .= " :TeacherBreakTimeType, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " 1 ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherID', $TeacherID);
	$Stmt->bindParam(':TeacherBreakTimeWeek', $TeacherBreakTimeWeek);
	$Stmt->bindParam(':TeacherBreakTimeHour', $TeacherBreakTimeHour);
	$Stmt->bindParam(':TeacherBreakTimeMinute', $TeacherBreakTimeMinute);
	$Stmt->bindParam(':TeacherBreakTimeType', $TeacherBreakTimeType);
	$Stmt->execute();
	$Stmt = null;

}else{
	$Sql = " update TeacherBreakTimes set TeacherBreakTimeType=:TeacherBreakTimeType where TeacherID=:TeacherID and TeacherBreakTimeWeek=:TeacherBreakTimeWeek and TeacherBreakTimeHour=:TeacherBreakTimeHour and TeacherBreakTimeMinute=:TeacherBreakTimeMinute ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherID', $TeacherID);
	$Stmt->bindParam(':TeacherBreakTimeWeek', $TeacherBreakTimeWeek);
	$Stmt->bindParam(':TeacherBreakTimeHour', $TeacherBreakTimeHour);
	$Stmt->bindParam(':TeacherBreakTimeMinute', $TeacherBreakTimeMinute);
	$Stmt->bindParam(':TeacherBreakTimeType', $TeacherBreakTimeType);
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