 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$MemberID = $_LINK_MEMBER_ID_;
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$AssmtStudentSelfScore = isset($_REQUEST["AssmtStudentSelfScore"]) ? $_REQUEST["AssmtStudentSelfScore"] : "";

$DeviceType = 1;//1:PC , 11:안드로이드 12:IOS

$Sql = " insert into AssmtStudentSelfScores ( ";
	$Sql .= " ClassID, ";
	$Sql .= " MemberID, ";
	$Sql .= " DeviceType, ";
	$Sql .= " AssmtStudentSelfScore, ";
	$Sql .= " AssmtStudentSelfScoreRegDateTime ";
$Sql .= " ) values ( ";
	$Sql .= " :ClassID, ";
	$Sql .= " :MemberID, ";
	$Sql .= " :DeviceType, ";
	$Sql .= " :AssmtStudentSelfScore, ";
	$Sql .= " now() ";
$Sql .= " ) ";


$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':DeviceType', $DeviceType);
$Stmt->bindParam(':AssmtStudentSelfScore', $AssmtStudentSelfScore);
$Stmt->execute();
$Stmt = null;


$ArrValue["ResultValue"] = 1;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>