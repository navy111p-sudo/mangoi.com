 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ErrNum = 0;
$ErrMsg = "성공";

$BookQuizBookGroupID = isset($_REQUEST["BookQuizBookGroupID"]) ? $_REQUEST["BookQuizBookGroupID"] : "";
$SelectedBookID = isset($_REQUEST["SelectedBookID"]) ? $_REQUEST["SelectedBookID"] : "";

$Sql2 = "select * from Books where BookGroupID=$BookQuizBookGroupID and BookView=1 and BookState=1 and BookTeacherView=1 order by BookOrder asc";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);


$BookQuizBookIDs = "{{|}}";
while($Row2 = $Stmt2->fetch()) {
	if ($SelectedBookID==$Row2["BookID"]){
		$StrSelected = "selected";
	}else{
		$StrSelected = "";
	}
	$BookQuizBookIDs .= "{{|}} ".$Row2["BookName"]."{|}".$Row2["BookID"]."{|}".$StrSelected;
}
$Stmt2 = null;


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;

$ArrValue["BookQuizBookIDs"] = $BookQuizBookIDs;
$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}


include_once('../includes/dbclose.php');
?>