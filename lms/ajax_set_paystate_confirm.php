 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$PayMonthStateID = isset($_REQUEST["PayMonthStateID"]) ? $_REQUEST["PayMonthStateID"] : "";
$Feedback = isset($_REQUEST["Feedback"]) ? $_REQUEST["Feedback"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$State = isset($_REQUEST["State"]) ? $_REQUEST["State"] : "";


// $Sql3 = "SELECT MemberID as PayApprovalMemberID from PayApprovalMembers where PayMonthStateID=$PayMonthStateID and MemberID=$MemberID";
// $Stmt3 = $DbConn->prepare($Sql3);
// $Stmt3->execute();
// $Stmt3->setFetchMode(PDO::FETCH_ASSOC);
// $Row3 = $Stmt3->fetch();
// $Stmt3 = null;
// $PayApprovalMemberID = $Row3["PayApprovalMemberID"];


if ($PayMonthStateID){
	$Sql = "UPDATE PayApprovalMembers set ApprovalState=$State, Feedback='$Feedback',
					ApprovalModiDateTime = now()
				where PayMonthStateID=$PayMonthStateID and MemberID=$MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
}

// 만약 모든 사람이 다 결재 승인을 했다면 PayMonthState 를 결재완료로(2) 상태 변경한다.
$Sql3 = "SELECT count(*) AS MemberCount from PayApprovalMembers where PayMonthStateID=$PayMonthStateID";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
$Row3 = $Stmt3->fetch();

$MemberCount = $Row3["MemberCount"];

$Sql3 = "SELECT count(*) AS MemberOkCount from PayApprovalMembers where PayMonthStateID=$PayMonthStateID AND ApprovalState = 1";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
$Row3 = $Stmt3->fetch();

$MemberOkCount = $Row3["MemberOkCount"];

if ($MemberOkCount == $MemberCount) {
	$Sql = "UPDATE PayMonthState set PayState=2
				where PayMonthStateID=$PayMonthStateID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
}




$ArrValue["ResultValue"] = 1;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>