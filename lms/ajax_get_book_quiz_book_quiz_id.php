  <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ErrNum = 0;
$ErrMsg = "성공";

$BookQuizBookID = isset($_REQUEST["BookQuizBookID"]) ? $_REQUEST["BookQuizBookID"] : "";
$SelectedBookQuizID = isset($_REQUEST["SelectedBookQuizID"]) ? $_REQUEST["SelectedBookQuizID"] : "";

$Sql2 = "select * from BookQuizs where BookID=$BookQuizBookID and BookQuizView=1 and BookQuizState=1 order by BookQuizOrder asc";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

$BookQuizBookQuizIDs = "{{|}}";
while($Row2 = $Stmt2->fetch()) {
	if ($SelectedBookQuizID==$Row2["BookQuizID"]){
		$StrSelected = "selected";
	}else{
		$StrSelected = "";
	}
	$BookQuizBookQuizIDs .= "{{|}} ".$Row2["BookQuizName"]."{|}".$Row2["BookQuizID"]."{|}".$StrSelected;
}
$Stmt2 = null;


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;

$ArrValue["BookQuizBookQuizIDs"] = $BookQuizBookQuizIDs;
$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}


include_once('../includes/dbclose.php');
?>