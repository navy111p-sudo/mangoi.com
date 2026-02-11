 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$DocumentReportID = isset($_REQUEST["DocumentReportID"]) ? $_REQUEST["DocumentReportID"] : "";
$Feedback = isset($_REQUEST["Feedback"]) ? $_REQUEST["Feedback"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$DocumentReportMemberState = isset($_REQUEST["DocumentReportMemberState"]) ? $_REQUEST["DocumentReportMemberState"] : "";


$Sql3 = "select MemberID as DocumentReportMemberID from DocumentReportMembers where DocumentReportID=$DocumentReportID and MemberID=$MemberID";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
$Row3 = $Stmt3->fetch();
$Stmt3 = null;
$DocumentReportMemberID = $Row3["DocumentReportMemberID"];


if ($DocumentReportMemberID){
	$Sql = "UPDATE DocumentReportMembers set DocumentReportMemberState=$DocumentReportMemberState, Feedback='$Feedback',
					DocumentReportMemberModiDateTime = now()
				where DocumentReportID=$DocumentReportID and MemberID=$MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
}else{


	$Sql3 = "select ifnull(max(DocumentReportMemberOrder),0) as DocumentReportMemberOrder from DocumentReportMembers where DocumentReportID=$DocumentReportID";
	$Stmt3 = $DbConn->prepare($Sql3);
	$Stmt3->execute();
	$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
	$Row3 = $Stmt3->fetch();
	$Stmt3 = null;
	$DocumentReportMemberOrder = $Row3["DocumentReportMemberOrder"]+1;

	$Sql = " insert into DocumentReportMembers ( ";
		$Sql .= " DocumentReportID, ";
		$Sql .= " MemberID, ";
		$Sql .= " DocumentReportMemberOrder, ";
		$Sql .= " DocumentReportMemberRegDateTime, ";
		$Sql .= " DocumentReportMemberModiDateTime, ";
		$Sql .= " DocumentReportMemberState, ";
		$Sql .= " Feedback ";
	$Sql .= " ) values ( ";
		$Sql .= " :DocumentReportID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " :DocumentReportMemberOrder, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :DocumentReportMemberState, ";
		$Sql .= " :Feedback ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':DocumentReportMemberOrder', $DocumentReportMemberOrder);
	$Stmt->bindParam(':DocumentReportMemberState', $DocumentReportMemberState);
	$Stmt->bindParam(':Feedback', $Feedback);
	$Stmt->execute();
	$Stmt = null;
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