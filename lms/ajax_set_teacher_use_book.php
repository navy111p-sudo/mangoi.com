 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";

$Sql = "select count(*) as ExistCount from TeacherUseBooks where BookID=:BookID and TeacherID=:TeacherID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookID', $BookID);
$Stmt->bindParam(':TeacherID', $TeacherID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ExistCount = $Row["ExistCount"];

if ($ExistCount==0){
	
	$Sql = " insert into TeacherUseBooks ( ";
		$Sql .= " BookID, ";
		$Sql .= " TeacherID ";
	$Sql .= " ) values ( ";
		$Sql .= " :BookID, ";
		$Sql .= " :TeacherID ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookID', $BookID);
	$Stmt->bindParam(':TeacherID', $TeacherID);
	$Stmt->execute();
	$Stmt = null;

}else{
	$Sql = " delete from TeacherUseBooks where BookID=:BookID and TeacherID=:TeacherID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookID', $BookID);
	$Stmt->bindParam(':TeacherID', $TeacherID);
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