 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ErrNum = 0;
$ErrMsg = "<?=$성공[$LangID]?>";

$Hr_CompetencyIndicatorID = isset($_REQUEST["Hr_CompetencyIndicatorID"]) ? $_REQUEST["Hr_CompetencyIndicatorID"] : "";
$memberType = isset($_REQUEST["memberType"]) ? $_REQUEST["memberType"] : 1;
$SetType = isset($_REQUEST["SetType"]) ? $_REQUEST["SetType"] : 1;


$Sql = "UPDATE Hr_CompetencyIndicators SET 
			Hr_MemberType".$memberType." = ".$SetType."
			WHERE Hr_CompetencyIndicatorID = :Hr_CompetencyIndicatorID
		";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':Hr_CompetencyIndicatorID', $Hr_CompetencyIndicatorID);
$Stmt->execute();
$Stmt = null;


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