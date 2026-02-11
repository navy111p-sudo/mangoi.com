<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');



$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$ClassBookType = isset($_REQUEST["ClassBookType"]) ? $_REQUEST["ClassBookType"] : "";

$Sql2 = "insert into ClassBookScanViewLogs (
                ClassID,
				ClassBookType,
                ClassBookScanViewLogDateTime

    ) values (
                :ClassID,
				:ClassBookType,
                now()
    )";

$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':ClassID', $ClassID);
$Stmt2->bindParam(':ClassBookType', $ClassBookType);
$Stmt2->execute();
$Stmt2 = null;



$ArrValue["CheckResult"] = 1;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
    //convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
    array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
    return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>