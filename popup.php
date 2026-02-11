<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');

$PopupID = isset($_REQUEST["PopupID"]) ? $_REQUEST["PopupID"] : "";

$Sql = "select * from Popups where PopupID=:PopupID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':PopupID', $PopupID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$PopupTitle = $Row["PopupTitle"];
$PopupType = $Row["PopupType"];
$PopupContent = $Row["PopupContent"];
$PopupImage = $Row["PopupImage"];
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $PopupTitle;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />

<SCRIPT LANGUAGE="JavaScript">
	function setCookie( name, value, expiredays ){ 

		var todayDate = new Date(); 
		todayDate.setDate( todayDate.getDate() + expiredays ); 
		document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";" 
	} 

   
</script>


</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<?php
if ($PopupType==1){
?>
	<img src="uploads/popup_images/<?=$PopupImage?>">
<?php
}else{
	echo $PopupContent;
}
?>
<div style="font-size:11px;">
<br>
<input type="checkbox" onclick="setCookie( 'Pop<?=$PopupID?>', 'Pop<?=$PopupID?>' , 1); window.close();"> 오늘 하루 이 창을 열지 않습니다.
</div>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>


