<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');

$UpDown = isset($_REQUEST["UpDown"]) ? $_REQUEST["UpDown"] : "";
$BoardID = isset($_REQUEST["BoardID"]) ? $_REQUEST["BoardID"] : "";
$MyBoardContentID = isset($_REQUEST["MyBoardContentID"]) ? $_REQUEST["MyBoardContentID"] : "";

$Sql = "select  * from BoardContents where BoardContentID=:MyBoardContentID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MyBoardContentID', $MyBoardContentID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$MyBoardContentReplyID = $Row["BoardContentReplyID"];


if ($UpDown=="1"){ // 올리기
	$Sql = "select BoardContentReplyID, BoardContentID from BoardContents where BoardID=:BoardID and BoardContentState=1 and BoardContentReplyID>:MyBoardContentReplyID order by BoardContentReplyID asc limit 0,1";
}else{//내리기
	$Sql = "select BoardContentReplyID, BoardContentID from BoardContents where BoardID=:BoardID and BoardContentState=1 and BoardContentReplyID<:MyBoardContentReplyID order by BoardContentReplyID desc limit 0,1";
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardID', $BoardID);
$Stmt->bindParam(':MyBoardContentReplyID', $MyBoardContentReplyID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TargetBoardContentID = $Row["BoardContentID"];
$TargetBoardContentReplyID = $Row["BoardContentReplyID"];

if ($TargetBoardContentID!=""){
	$TempBoardContentReplyID = $MyBoardContentReplyID;
	$MyBoardContentReplyID = $TargetBoardContentReplyID;
	$TargetBoardContentReplyID = $TempBoardContentReplyID;

	$Sql = "update BoardContents set BoardContentReplyID=:MyBoardContentReplyID where BoardContentID=:MyBoardContentID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MyBoardContentReplyID', $MyBoardContentReplyID);
	$Stmt->bindParam(':MyBoardContentID', $MyBoardContentID);
	$Stmt->execute();
	$Stmt = null;

	$Sql = "update BoardContents set BoardContentReplyID=:TargetBoardContentReplyID where BoardContentID=:TargetBoardContentID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TargetBoardContentReplyID', $TargetBoardContentReplyID);
	$Stmt->bindParam(':TargetBoardContentID', $TargetBoardContentID);
	$Stmt->execute();
	$Stmt = null;
}

include_once('./includes/dbclose.php');
?>