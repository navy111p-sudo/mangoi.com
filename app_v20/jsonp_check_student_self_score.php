<?
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$ErrNum = 0;
$ErrMsg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


$Sql = "select 
			AssmtStudentSelfScoreID
		from AssmtStudentSelfScores A 
		where 
			A.ClassID=:ClassID
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$AssmtStudentSelfScoreID = $Row["AssmtStudentSelfScoreID"];

if (!$AssmtStudentSelfScoreID){
	$AssmtStudentSelfScoreID=0;
}


// 위 코드는 점수를 내기전에는 쌓이지않음
// 처음 눌렀을 때, 멤버계정을 가져오려면 별개의 SQL 필요
$Sql = "select 
			A.MemberID,
			B.MemberInviteID
		from Classes A 
			inner join Members B on A.MemberID=B.MemberID 
		where 
			A.ClassID=:ClassID
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberID = $Row["MemberID"];
$MemberInviteID = $Row["MemberInviteID"];

InsertNewTypePoint(1, 0, $MemberID, $ClassID);


// 친구 초대로 유입된 학생이라면
if($MemberInviteID) {
	$InviteInfo = $MemberID."|".$MemberInviteID;
	InsertNewTypePoint(6, 0, $MemberInviteID, $ClassID);
}


$ArrValue["AssmtStudentSelfScoreID"] = $AssmtStudentSelfScoreID;
$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;

$ResultValue = my_json_encode($ArrValue);


echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');


?>