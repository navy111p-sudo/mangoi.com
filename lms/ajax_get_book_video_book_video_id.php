  <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ErrNum = 0;
$ErrMsg = "성공";

$BookVideoBookID = isset($_REQUEST["BookVideoBookID"]) ? $_REQUEST["BookVideoBookID"] : "";
$SelectedBookVideoID = isset($_REQUEST["SelectedBookVideoID"]) ? $_REQUEST["SelectedBookVideoID"] : "";

$Sql2 = "select * from BookVideos where BookID=$BookVideoBookID and BookVideoView=1 and BookVideoState=1 order by BookVideoOrder asc";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

$BookVideoBookVideoIDs = "{{|}}";
while($Row2 = $Stmt2->fetch()) {
	if ($SelectedBookVideoID==$Row2["BookVideoID"]){
		$StrSelected = "selected";
	}else{
		$StrSelected = "";
	}
	$BookVideoBookVideoIDs .= "{{|}} ".$Row2["BookVideoName"]."{|}".$Row2["BookVideoID"]."{|}".$StrSelected;
}
$Stmt2 = null;


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;

$ArrValue["BookVideoBookVideoIDs"] = $BookVideoBookVideoIDs;
$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}


include_once('../includes/dbclose.php');
?>