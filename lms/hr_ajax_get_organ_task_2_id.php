 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ErrNum = 0;
$ErrMsg = "성공";

$Hr_OrganLevelID = isset($_REQUEST["Hr_OrganLevelID"]) ? $_REQUEST["Hr_OrganLevelID"] : "";
$Hr_OrganTask1ID = isset($_REQUEST["Hr_OrganTask1ID"]) ? $_REQUEST["Hr_OrganTask1ID"] : "";
$Hr_OrganTask2ID = isset($_REQUEST["Hr_OrganTask2ID"]) ? $_REQUEST["Hr_OrganTask2ID"] : "";

$Sql = "
		select 
			A.*
		from Hr_OrganLevels A 
		where A.Hr_OrganLevelID=:Hr_OrganLevelID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':Hr_OrganLevelID', $Hr_OrganLevelID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Hr_OrganLevel = $Row["Hr_OrganLevel"];


$Sql2 = "select 
			* 
		from Hr_OrganTask2 
		where 
			Hr_OrganLevel=:Hr_OrganLevel 
			and Hr_OrganTask1ID=:Hr_OrganTask1ID 
			and (Hr_OrganTask2State=1 or Hr_OrganTask2ID=:Hr_OrganTask2ID) 
		order by Hr_OrganTask2Name asc";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':Hr_OrganLevel', $Hr_OrganLevel);
$Stmt2->bindParam(':Hr_OrganTask1ID', $Hr_OrganTask1ID);
$Stmt2->bindParam(':Hr_OrganTask2ID', $Hr_OrganTask2ID);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);


$Hr_OrganTask2IDs = "{{|}}";
while($Row2 = $Stmt2->fetch()) {
	if ($Hr_OrganTask2ID==$Row2["Hr_OrganTask2ID"]){
		$StrSelected = "selected";
	}else{
		$StrSelected = "";
	}
	$Hr_OrganTask2IDs .= "{{|}} ".$Row2["Hr_OrganTask2Name"]."{|}".$Row2["Hr_OrganTask2ID"]."{|}".$StrSelected;
}
$Stmt2 = null;


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;

$ArrValue["Hr_OrganLevel"] = $Hr_OrganLevel;
$ArrValue["Hr_OrganTask2IDs"] = $Hr_OrganTask2IDs;
$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}


include_once('../includes/dbclose.php');
?>