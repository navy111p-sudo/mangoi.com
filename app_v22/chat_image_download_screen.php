<?
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Mangoi</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
</head>
<body style="padding:20px;"> 
<?
$MangoTalkMsgID = isset($_REQUEST["MangoTalkMsgID"]) ? $_REQUEST["MangoTalkMsgID"] : "";


$Sql = "select * from MangoTalkMsgs where MangoTalkMsgID=:MangoTalkMsgID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MangoTalkMsgID', $MangoTalkMsgID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$MangoTalkImageName = $Row["MangoTalkImageName"];
$MangoTalkImageSaveName = $Row["MangoTalkImageSaveName"];

?>
<img src="../uploads/chat_images/<?=$MangoTalkImageSaveName?>" style="width:100%;border-radius:10px;" onclick="FileDownload()">

<script>
function FileDownload(){
	location.href = "chat_image_download.php?MangoTalkMsgID=<?=$MangoTalkMsgID?>";
}

window.onload = function(){
	FileDownload();
}
</script>

</body>
</html>
<?
include_once('../includes/dbclose.php');
?>