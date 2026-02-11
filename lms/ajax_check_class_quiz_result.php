<?php

header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";

$Sql = "
	select 
			A.BookQuizResultID
		from BookQuizResults A 
		where A.ClassID=:ClassID and A.BookQuizResultState=2 and A.QuizStudyNumber=1 
";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookQuizResultID = $Row["BookQuizResultID"];

if (!$BookQuizResultID){
	$BookQuizResultID = 0;
}

$ArrValue["BookQuizResultID"] = $BookQuizResultID;


$QueryResult = my_json_encode($ArrValue);
echo $QueryResult;


function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');

?>