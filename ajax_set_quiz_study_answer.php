<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');

$ErrNum = 1;
$ErrMsg = "";

$BookQuizResultDetailID = isset($_REQUEST["BookQuizResultDetailID"]) ? $_REQUEST["BookQuizResultDetailID"] : "";
$MyAnswer = isset($_REQUEST["MyAnswer"]) ? $_REQUEST["MyAnswer"] : "";

$Sql = "select 
	A.BookQuizResultID,
	A.BookQuizDetailCorrectAnswer,
	A.BookQuizDetailOrder
from BookQuizResultDetails A 
where A.BookQuizResultDetailID=".$BookQuizResultDetailID." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookQuizResultID = $Row["BookQuizResultID"];
$BookQuizDetailCorrectAnswer = $Row["BookQuizDetailCorrectAnswer"];
$BookQuizDetailOrder = $Row["BookQuizDetailOrder"];

if ($MyAnswer==$BookQuizDetailCorrectAnswer){
	$MyScore = 100;
}else{
	$MyScore = 0;
}	

$Sql = "update BookQuizResultDetails set 
			MyAnswer=$MyAnswer, 
			MyScore=$MyScore
		where BookQuizResultDetailID=$BookQuizResultDetailID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt = null;

$Sql = "update BookQuizResults set 
			BookQuizCurrentPage=$BookQuizDetailOrder
		where BookQuizResultID=$BookQuizResultID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt = null;


$CheckResult = 1;

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["CheckResult"] = $CheckResult;


 
$ResultValue = my_json_encode($ArrValue);
echo $ResultValue; 

function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('./includes/dbclose.php');
?>