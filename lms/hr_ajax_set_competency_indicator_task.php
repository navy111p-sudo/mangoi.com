 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ErrNum = 0;
$ErrMsg = "<?=$성공[$LangID]?>";

$Hr_CompetencyIndicatorCate2ID = isset($_REQUEST["Hr_CompetencyIndicatorCate2ID"]) ? $_REQUEST["Hr_CompetencyIndicatorCate2ID"] : "";
$Hr_OrganTask2ID = isset($_REQUEST["Hr_OrganTask2ID"]) ? $_REQUEST["Hr_OrganTask2ID"] : "";

$Sql3 = "
		select 
			count(*) as Hr_CompetencyIndicatorTaskCount
		from Hr_CompetencyIndicatorTasks 
		where Hr_OrganTask2ID=$Hr_OrganTask2ID and Hr_CompetencyIndicatorCate2ID=$Hr_CompetencyIndicatorCate2ID ";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
$Row3 = $Stmt3->fetch();
$Stmt3 = null;
$Hr_CompetencyIndicatorTaskCount = $Row3["Hr_CompetencyIndicatorTaskCount"];

if ($Hr_CompetencyIndicatorTaskCount>0){
	$SetType = 0;

	$Sql = "delete from Hr_CompetencyIndicatorTasks where Hr_OrganTask2ID=:Hr_OrganTask2ID and Hr_CompetencyIndicatorCate2ID=:Hr_CompetencyIndicatorCate2ID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_OrganTask2ID', $Hr_OrganTask2ID);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
	$Stmt->execute();
	$Stmt = null;

}else{
	$SetType = 1;

	$Sql = " insert into Hr_CompetencyIndicatorTasks ( ";
		$Sql .= " Hr_OrganTask2ID, ";
		$Sql .= " Hr_CompetencyIndicatorCate2ID, ";
		$Sql .= " Hr_CompetencyIndicatorTaskRegDateTime, ";
		$Sql .= " Hr_CompetencyIndicatorTaskModiDateTime, ";
		$Sql .= " Hr_CompetencyIndicatorTaskState ";
	$Sql .= " ) values ( ";
		$Sql .= " :Hr_OrganTask2ID, ";
		$Sql .= " :Hr_CompetencyIndicatorCate2ID, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " 1 ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_OrganTask2ID', $Hr_OrganTask2ID);
	$Stmt->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
	$Stmt->execute();
	$Hr_OrganTask1ID = $DbConn->lastInsertId();
	$Stmt = null;
}


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;

$ArrValue["SetType"] = $SetType;
$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}


include_once('../includes/dbclose.php');
?>