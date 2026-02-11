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
