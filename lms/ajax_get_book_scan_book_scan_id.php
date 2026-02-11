  <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ErrNum = 0;
$ErrMsg = "성공";

$BookScanBookID = isset($_REQUEST["BookScanBookID"]) ? $_REQUEST["BookScanBookID"] : "";
$SelectedBookScanID = isset($_REQUEST["SelectedBookScanID"]) ? $_REQUEST["SelectedBookScanID"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";

$AddSqlWhere2 = "";
if ($TeacherID!=""){
	//$AddSqlWhere2 = " and BookID in (select BookID from TeacherUseBooks where TeacherID=$TeacherID) ";
}


$Sql2 = "
		select 
				* 
		from BookScans 
		where 
			BookID=$BookScanBookID 
			and BookScanView=1 
			and BookScanState=1 
			".$AddSqlWhere2."
		order by BookScanOrder asc
	";


$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

$BookScanBookScanIDs = "{{|}}";
while($Row2 = $Stmt2->fetch()) {
	if ($SelectedBookScanID==$Row2["BookScanID"]){
		$StrSelected = "selected";
	}else{
		$StrSelected = "";
	}
	$BookScanBookScanIDs .= "{{|}} ".$Row2["BookScanName"]."{|}".$Row2["BookScanID"]."{|}".$StrSelected;
}
$Stmt2 = null;

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;

$ArrValue["BookScanBookScanIDs"] = $BookScanBookScanIDs;
$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}


include_once('../includes/dbclose.php');
?>