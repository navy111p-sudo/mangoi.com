<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../../includes/dbopen.php');
include_once('../../includes/common.php');


$ComputerID = isset($_REQUEST["ComputerID"]) ? (int)$_REQUEST["ComputerID"] : 0;
$MemberSocketName = isset($_REQUEST["MemberSocketName"]) ? $_REQUEST["MemberSocketName"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? (int)$_REQUEST["MemberID"] : 0;
$LinkMemberID = isset($_REQUEST["LinkMemberID"]) ? (int)$_REQUEST["LinkMemberID"] : 0;
 
setcookie('ComputerID', $ComputerID, time() + 3600);
setcookie('MemberSocketName', $MemberSocketName, time() + 3600);
setcookie('MemberID', $MemberID, time() + 3600);
setcookie('LinkMemberID', $LinkMemberID, time() + 3600);


$Sql = " insert into MemberSockets_test ( ";
    $Sql .= " MemberSocketName, ";
    $Sql .= " ComputerID, ";
    $Sql .= " MemberID, ";
    $Sql .= " LinkMemberID, ";
    $Sql .= " MemberSocketRegDateTime, ";
    $Sql .= " MemberSocketModiDateTime, ";
    $Sql .= " MemberSocketStatus ";
$Sql .= " ) values ( ";
    $Sql .= " :MemberSocketName, ";
    $Sql .= " :ComputerID, ";
    $Sql .= " :MemberID, ";
    $Sql .= " :LinkMemberID, ";
    $Sql .= " now(), ";
    $Sql .= " now(), ";
    $Sql .= " 1 ";
$Sql .= " ) ";


$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberSocketName', $MemberSocketName);
$Stmt->bindParam(':ComputerID', $ComputerID);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':LinkMemberID', $LinkMemberID);
$Result = $Stmt->execute();
$Stmt = null;

if($Result) {
    $ArrValue["Result"] = 1;
} else {
    $ArrValue["Result"] = 0;
}

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 


function my_json_encode($arr) {
    //convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
    array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
    return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../../includes/dbclose.php');

?>