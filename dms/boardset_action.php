<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);
$NewData = isset($_REQUEST["NewData"]) ? $_REQUEST["NewData"] : "";

$UseMain = isset($_REQUEST["UseMain"]) ? $_REQUEST["UseMain"] : "";
$UseSub = isset($_REQUEST["UseSub"]) ? $_REQUEST["UseSub"] : "";
$SubID = isset($_REQUEST["SubID"]) ? $_REQUEST["SubID"] : "";
$BoardID = isset($_REQUEST["BoardID"]) ? $_REQUEST["BoardID"] : "";
$BoardCode = isset($_REQUEST["BoardCode"]) ? $_REQUEST["BoardCode"] : "";
$BoardLayout = isset($_REQUEST["BoardLayout"]) ? $_REQUEST["BoardLayout"] : "";
$BoardCss = isset($_REQUEST["BoardCss"]) ? $_REQUEST["BoardCss"] : "";
$BoardJavascript = isset($_REQUEST["BoardJavascript"]) ? $_REQUEST["BoardJavascript"] : "";
$BoardName = isset($_REQUEST["BoardName"]) ? $_REQUEST["BoardName"] : "";
$BoardTitle = isset($_REQUEST["BoardTitle"]) ? $_REQUEST["BoardTitle"] : "";
$BoardListRowNum = isset($_REQUEST["BoardListRowNum"]) ? $_REQUEST["BoardListRowNum"] : "";
$BoardEnableReplay = isset($_REQUEST["BoardEnableReplay"]) ? $_REQUEST["BoardEnableReplay"] : "";
$BoardEnableCategory = isset($_REQUEST["BoardEnableCategory"]) ? $_REQUEST["BoardEnableCategory"] : "";
$BoardEnableComment = isset($_REQUEST["BoardEnableComment"]) ? $_REQUEST["BoardEnableComment"] : "";
$BoardEnableSecret = isset($_REQUEST["BoardEnableSecret"]) ? $_REQUEST["BoardEnableSecret"] : "";
$BoardFileCount = isset($_REQUEST["BoardFileCount"]) ? $_REQUEST["BoardFileCount"] : "";
$BoardListLevel = isset($_REQUEST["BoardListLevel"]) ? $_REQUEST["BoardListLevel"] : "";
$BoardReadLevel = isset($_REQUEST["BoardReadLevel"]) ? $_REQUEST["BoardReadLevel"] : "";
$BoardWriteLevel = isset($_REQUEST["BoardWriteLevel"]) ? $_REQUEST["BoardWriteLevel"] : "";
$BoardReplyLevel = isset($_REQUEST["BoardReplyLevel"]) ? $_REQUEST["BoardReplyLevel"] : "";
$BoardNoticeLevel = isset($_REQUEST["BoardNoticeLevel"]) ? $_REQUEST["BoardNoticeLevel"] : "";
$BoardCommentLevel = isset($_REQUEST["BoardCommentLevel"]) ? $_REQUEST["BoardCommentLevel"] : "";
$BoardSecretReadLevel = isset($_REQUEST["BoardSecretReadLevel"]) ? $_REQUEST["BoardSecretReadLevel"] : "";
$BoardModifyLevel = isset($_REQUEST["BoardModifyLevel"]) ? $_REQUEST["BoardModifyLevel"] : "";
$BoardState = isset($_REQUEST["BoardState"]) ? $_REQUEST["BoardState"] : "";

$BoardCode = trim($BoardCode);

if ($NewData=="1"){

	$Sql = " insert into Boards ( ";
		$Sql .= " UseMain, ";
		$Sql .= " UseSub, ";
		$Sql .= " SubID, ";
		$Sql .= " BoardCode, ";
		$Sql .= " BoardLayout, ";
		$Sql .= " BoardCss, ";
		$Sql .= " BoardJavascript, ";
		$Sql .= " BoardName, ";
		$Sql .= " BoardTitle, ";
		$Sql .= " BoardListRowNum, ";
		$Sql .= " BoardEnableCategory, ";
		$Sql .= " BoardEnableReplay, ";
		$Sql .= " BoardEnableComment, ";
		$Sql .= " BoardEnableSecret, ";
		$Sql .= " BoardFileCount, ";
		$Sql .= " BoardListLevel, ";
		$Sql .= " BoardReadLevel, ";
		$Sql .= " BoardWriteLevel, ";
		$Sql .= " BoardReplyLevel, ";
		$Sql .= " BoardNoticeLevel, ";
		$Sql .= " BoardCommentLevel, ";
		$Sql .= " BoardSecretReadLevel, ";
		$Sql .= " BoardModifyLevel, ";
		$Sql .= " BoardRegDateTime, ";
		$Sql .= " BoardState ";
	$Sql .= " ) values ( ";
		$Sql .= " :UseMain, ";
		$Sql .= " :UseSub, ";
		$Sql .= " :SubID, ";
		$Sql .= " :BoardCode, ";
		$Sql .= " :BoardLayout, ";
		$Sql .= " :BoardCss, ";
		$Sql .= " :BoardJavascript, ";
		$Sql .= " :BoardName, ";
		$Sql .= " :BoardTitle, ";
		$Sql .= " :BoardListRowNum, ";
		$Sql .= " :BoardEnableCategory, ";
		$Sql .= " :BoardEnableReplay, ";
		$Sql .= " :BoardEnableComment, ";
		$Sql .= " :BoardEnableSecret, ";
		$Sql .= " :BoardFileCount, ";
		$Sql .= " :BoardListLevel, ";
		$Sql .= " :BoardReadLevel, ";
		$Sql .= " :BoardWriteLevel, ";
		$Sql .= " :BoardReplyLevel, ";
		$Sql .= " :BoardNoticeLevel, ";
		$Sql .= " :BoardCommentLevel, ";
		$Sql .= " :BoardSecretReadLevel, ";
		$Sql .= " :BoardModifyLevel, ";
		$Sql .= " now(), ";
		$Sql .= " :BoardState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':UseMain', $UseMain);
	$Stmt->bindParam(':UseSub', $UseSub);
	$Stmt->bindParam(':SubID', $SubID);
	$Stmt->bindParam(':BoardCode', $BoardCode);
	$Stmt->bindParam(':BoardLayout', $BoardLayout);
	$Stmt->bindParam(':BoardCss', $BoardCss);
	$Stmt->bindParam(':BoardJavascript', $BoardJavascript);
	$Stmt->bindParam(':BoardName', $BoardName);
	$Stmt->bindParam(':BoardTitle', $BoardTitle);
	$Stmt->bindParam(':BoardListRowNum', $BoardListRowNum);
	$Stmt->bindParam(':BoardEnableCategory', $BoardEnableCategory);
	$Stmt->bindParam(':BoardEnableReplay', $BoardEnableReplay);
	$Stmt->bindParam(':BoardEnableComment', $BoardEnableComment);
	$Stmt->bindParam(':BoardEnableSecret', $BoardEnableSecret);
	$Stmt->bindParam(':BoardFileCount', $BoardFileCount);
	$Stmt->bindParam(':BoardListLevel', $BoardListLevel);
	$Stmt->bindParam(':BoardReadLevel', $BoardReadLevel);
	$Stmt->bindParam(':BoardWriteLevel', $BoardWriteLevel);
	$Stmt->bindParam(':BoardReplyLevel', $BoardReplyLevel);
	$Stmt->bindParam(':BoardNoticeLevel', $BoardNoticeLevel);
	$Stmt->bindParam(':BoardCommentLevel', $BoardCommentLevel);
	$Stmt->bindParam(':BoardSecretReadLevel', $BoardSecretReadLevel);
	$Stmt->bindParam(':BoardModifyLevel', $BoardModifyLevel);
	$Stmt->bindParam(':BoardState', $BoardState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update Boards set ";
		$Sql .= " UseMain = :UseMain, ";
		$Sql .= " UseSub = :UseSub, ";
		$Sql .= " SubID = :SubID, ";
		$Sql .= " BoardLayout = :BoardLayout, ";
		$Sql .= " BoardCss = :BoardCss, ";
		$Sql .= " BoardJavascript = :BoardJavascript, ";
		$Sql .= " BoardName = :BoardName, ";
		$Sql .= " BoardTitle = :BoardTitle, ";
		$Sql .= " BoardListRowNum = :BoardListRowNum, ";
		$Sql .= " BoardEnableCategory = :BoardEnableCategory, ";
		$Sql .= " BoardEnableReplay = :BoardEnableReplay, ";
		$Sql .= " BoardEnableComment = :BoardEnableComment, ";
		$Sql .= " BoardEnableSecret = :BoardEnableSecret, ";
		$Sql .= " BoardFileCount = :BoardFileCount, ";
		$Sql .= " BoardListLevel = :BoardListLevel, ";
		$Sql .= " BoardReadLevel = :BoardReadLevel, ";
		$Sql .= " BoardWriteLevel = :BoardWriteLevel, ";
		$Sql .= " BoardReplyLevel = :BoardReplyLevel, ";
		$Sql .= " BoardNoticeLevel = :BoardNoticeLevel, ";
		$Sql .= " BoardCommentLevel = :BoardCommentLevel, ";
		$Sql .= " BoardSecretReadLevel = :BoardSecretReadLevel, ";
		$Sql .= " BoardModifyLevel = :BoardModifyLevel, ";
		$Sql .= " BoardState = :BoardState ";
	$Sql .= " where BoardID = :BoardID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':UseMain', $UseMain);
	$Stmt->bindParam(':UseSub', $UseSub);
	$Stmt->bindParam(':SubID', $SubID);
	$Stmt->bindParam(':BoardLayout', $BoardLayout);
	$Stmt->bindParam(':BoardCss', $BoardCss);
	$Stmt->bindParam(':BoardJavascript', $BoardJavascript);
	$Stmt->bindParam(':BoardName', $BoardName);
	$Stmt->bindParam(':BoardTitle', $BoardTitle);
	$Stmt->bindParam(':BoardListRowNum', $BoardListRowNum);
	$Stmt->bindParam(':BoardEnableCategory', $BoardEnableCategory);
	$Stmt->bindParam(':BoardEnableReplay', $BoardEnableReplay);
	$Stmt->bindParam(':BoardEnableComment', $BoardEnableComment);
	$Stmt->bindParam(':BoardEnableSecret', $BoardEnableSecret);
	$Stmt->bindParam(':BoardFileCount', $BoardFileCount);
	$Stmt->bindParam(':BoardListLevel', $BoardListLevel);
	$Stmt->bindParam(':BoardReadLevel', $BoardReadLevel);
	$Stmt->bindParam(':BoardWriteLevel', $BoardWriteLevel);
	$Stmt->bindParam(':BoardReplyLevel', $BoardReplyLevel);
	$Stmt->bindParam(':BoardNoticeLevel', $BoardNoticeLevel);
	$Stmt->bindParam(':BoardCommentLevel', $BoardCommentLevel);
	$Stmt->bindParam(':BoardSecretReadLevel', $BoardSecretReadLevel);
	$Stmt->bindParam(':BoardModifyLevel', $BoardModifyLevel);
	$Stmt->bindParam(':BoardState', $BoardState);
	$Stmt->bindParam(':BoardID', $BoardID);
	$Stmt->execute();
	$Stmt = null;
}


if ($err_num != 0){
	include_once('./_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
</body>
<?php
	include_once('./_footer.php');
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: board.php?$ListParam"); 
	exit;
}
?>


