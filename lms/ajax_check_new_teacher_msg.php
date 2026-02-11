<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');


$Sql = "select 
				A.* 
		from TeacherMessages A 
		where 
			A.TeacherMessageID not in (select TeacherMessageID from TeacherMessageReads where MemberID=".$_LINK_ADMIN_ID_.") 
			and A.MemberID=".$_LINK_ADMIN_ID_." 
			and A.TeacherMessageType=1 
			and datediff(A.TeacherMessageRegDateTime, now())=0 
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$MemberID = $_LINK_ADMIN_ID_;
$MsgCount = 0;
$Msg = "";
while($Row = $Stmt->fetch()) {

	$TeacherMessageID = $Row["TeacherMessageID"];
	$TeacherMessageText = $Row["TeacherMessageText"];

	
	if ($Msg!=""){
		$Msg = $Msg . "\n\n";
	}
	$Msg = $Msg . ($MsgCount+1) . ") ". $TeacherMessageText;
		
	$Sql2 = " insert into TeacherMessageReads ( ";
		$Sql2 .= " TeacherMessageID, ";
		$Sql2 .= " MemberID, ";
		$Sql2 .= " TeacherMessageReadDateTime ";
	$Sql2 .= " ) values ( ";
		$Sql2 .= " :TeacherMessageID, ";
		$Sql2 .= " :MemberID, ";
		$Sql2 .= " now() ";
	$Sql2 .= " ) ";

	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':TeacherMessageID', $TeacherMessageID);
	$Stmt2->bindParam(':MemberID', $MemberID);
	$Stmt2->execute();
	$Stmt2 = null;

	$MsgCount++;

}
$Stmt = null;


$ArrValue["MsgCount"] = $MsgCount;
$ArrValue["Msg"] = $Msg;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>