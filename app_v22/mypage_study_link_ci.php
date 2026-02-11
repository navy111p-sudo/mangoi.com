<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Mangoi</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
</head>
<body> 


<?php

$ClassRoomUrl = isset($_REQUEST["ClassRoomUrl"]) ? $_REQUEST["ClassRoomUrl"] : "";

$ClassRoomUrl = str_replace("^^","&",$ClassRoomUrl);
$ClassRoomUrl = str_replace("*****","?",$ClassRoomUrl);

//echo $ClassRoomUrl;
header("Location: $ClassRoomUrl"); 
exit;
?>

<!--
<div style="display:none;">
    <form name="CiClassForm" id="CiClassForm" action="<?=$ClassRoomUrl?>" method="POST">
        <input type="text" name="ClassID" id="ClassID" value="<?=$ClassID?>">
        <input type="text" name="ClassName" id="ClassName" value="<?=$ClassName?>">
        <input type="text" name="MemberType" id="MemberType" value="<?=$MemberType?>">
    </form>
</div>

<script>
window.onload = function(){
	document.CiClassForm.submit();
}
</script>
-->


</body>
</html>
