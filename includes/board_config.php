<?php
$BoardCode = isset($_REQUEST["BoardCode"]) ? $_REQUEST["BoardCode"] : "";

$Sql = "select * from Boards where BoardCode=:BoardCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardCode', $BoardCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$UseMain = $Row["UseMain"];
$UseSub = $Row["UseSub"];
$SubID = $Row["SubID"];
$BoardID = $Row["BoardID"];
$BoardLayout = $Row["BoardLayout"];
$BoardCss = $Row["BoardCss"];
$BoardJavascript = $Row["BoardJavascript"];
$BoardName = $Row["BoardName"];
$BoardTitle = $Row["BoardTitle"];
$BoardListRowNum = $Row["BoardListRowNum"];
$BoardEnableCategory = $Row["BoardEnableCategory"];
$BoardEnableReplay = $Row["BoardEnableReplay"];
$BoardEnableComment = $Row["BoardEnableComment"];
$BoardEnableSecret = $Row["BoardEnableSecret"];
$BoardFileCount = $Row["BoardFileCount"];
$BoardListLevel = $Row["BoardListLevel"];
$BoardReadLevel = $Row["BoardReadLevel"];
$BoardWriteLevel = $Row["BoardWriteLevel"];
$BoardReplyLevel = $Row["BoardReplyLevel"];
$BoardNoticeLevel = $Row["BoardNoticeLevel"];
$BoardCommentLevel = $Row["BoardCommentLevel"];
$BoardSecretReadLevel = $Row["BoardSecretReadLevel"];
$BoardModifyLevel = $Row["BoardModifyLevel"];
$BoardRegDateTime = $Row["BoardRegDateTime"];
$BoardState = $Row["BoardState"];
$BoardDateHide = $Row["BoardDateHide"];

$EnableReplay = false;
$EnableComment = false;
$EnableSecret = false;

$AuthList = false;
$AuthRead = false;
$AuthWrite = false;
$AuthReply = false;
$AuthNotice = false;
$AuthComment = false;
$AuthSecretRead = false;
$AuthModify = false;
$AuthDelete = false;

if ($BoardEnableReplay==1){
	$EnableReplay = true;
}
if ($BoardEnableComment==1){
	$EnableComment = true;
}
if ($BoardEnableSecret==1){
	$EnableSecret = true;
}

if (isset($_LINK_MEMBER_LEVEL_ID_)){
	if ($_LINK_MEMBER_LEVEL_ID_ <= $BoardListLevel){
		$AuthList = true;
	}
	if ($_LINK_MEMBER_LEVEL_ID_ <= $BoardReadLevel){
		$AuthRead = true;
	}
	if ($_LINK_MEMBER_LEVEL_ID_ <= $BoardWriteLevel){
		$AuthWrite = true;
	}
	if ($_LINK_MEMBER_LEVEL_ID_ <= $BoardReplyLevel){
		$AuthReply = true;
	}
	if ($_LINK_MEMBER_LEVEL_ID_ <= $BoardNoticeLevel){
		$AuthNotice = true;
	}
	if ($_LINK_MEMBER_LEVEL_ID_ <= $BoardCommentLevel){
		$AuthComment = true;
	}
	if ($_LINK_MEMBER_LEVEL_ID_ <= $BoardSecretReadLevel){
		$AuthSecretRead = true;
	}
	if ($_LINK_MEMBER_LEVEL_ID_ <= $BoardModifyLevel){
		$AuthModify = true;
	}
}else{
	$AuthList = false;
	$AuthRead = false;
	$AuthWrite = false;
	$AuthReply = false;
	$AuthNotice = false;
	$AuthComment = false;
	$AuthSecretRead = false;
	$AuthModify = false;
}
?>