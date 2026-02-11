<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);
$PageType = isset($_REQUEST["PageType"]) ? $_REQUEST["PageType"] : "";
$PageTabID = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";

/*
$CenterDeviceID = (int)isset($_REQUEST["CenterDeviceID"]) ? $_REQUEST["CenterDeviceID"] : "";
$CenterID = (int)isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : 0;
$CenterDeviceWeekNum = (int)isset($_REQUEST["CenterDeviceWeekNum"]) ? $_REQUEST["CenterDeviceWeekNum"] : 0;
$CenterDeviceStartTime = isset($_REQUEST["CenterDeviceStartTime"]) ? $_REQUEST["CenterDeviceStartTime"] : "";
$CenterDeviceEndTime = isset($_REQUEST["CenterDeviceEndTime"]) ? $_REQUEST["CenterDeviceEndTime"] : "";
$CenterDeviceName = isset($_REQUEST["CenterDeviceName"]) ? $_REQUEST["CenterDeviceName"] : "";
$CenterDeviceRegDateTime = isset($_REQUEST["CenterDeviceRegDateTime"]) ? $_REQUEST["CenterDeviceRegDateTime"] : "";
$CenterDeviceModiDateTime = isset($_REQUEST["CenterDeviceModiDateTime"]) ? $_REQUEST["CenterDeviceModiDateTime"] : "";
$CenterDeviceState = (int)isset($_REQUEST["CenterDeviceState"]) ? $_REQUEST["CenterDeviceState"] : "";
$CenterDeviceView = (int)isset($_REQUEST["CenterDeviceView"]) ? $_REQUEST["CenterDeviceView"] : "";
*/
$CenterDeviceID = isset($_REQUEST["CenterDeviceID"]) ? $_REQUEST["CenterDeviceID"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : 0;
$CenterDeviceType = isset($_REQUEST["CenterDeviceType"]) ? $_REQUEST["CenterDeviceType"] : "";
$CenterDeviceName = isset($_REQUEST["CenterDeviceName"]) ? $_REQUEST["CenterDeviceName"] : "";
$CenterDeviceRegDateTime = isset($_REQUEST["CenterDeviceRegDateTime"]) ? $_REQUEST["CenterDeviceRegDateTime"] : "";
$CenterDeviceModiDateTime = isset($_REQUEST["CenterDeviceModiDateTime"]) ? $_REQUEST["CenterDeviceModiDateTime"] : "";
$CenterDeviceState = isset($_REQUEST["CenterDeviceState"]) ? $_REQUEST["CenterDeviceState"] : 2;
$CenterDeviceView = isset($_REQUEST["CenterDeviceView"]) ? $_REQUEST["CenterDeviceView"] : 2;


if ($CenterDeviceView!="1"){
	$CenterDeviceView = 0;
}


// $CenterDeviceLevelID = 19;//학생


if ($CenterDeviceID==""){

	$Sql = " insert into CenterDevices ( ";
		$Sql .= " CenterID, ";
		$Sql .= " CenterDeviceName, ";
		$Sql .= " CenterDeviceType, ";
		$Sql .= " CenterDeviceRegDateTime, ";
		$Sql .= " CenterDeviceModiDateTime, ";
		$Sql .= " CenterDeviceState, ";
		$Sql .= " CenterDeviceView ";

	$Sql .= " ) values ( ";

		$Sql .= " :CenterID, ";
		$Sql .= " :CenterDeviceName, ";
		$Sql .= " :CenterDeviceType, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :CenterDeviceState, ";
		$Sql .= " :CenterDeviceView ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':CenterDeviceName', $CenterDeviceName);
	$Stmt->bindParam(':CenterDeviceType', $CenterDeviceType);
	$Stmt->bindParam(':CenterDeviceState', $CenterDeviceState);
	$Stmt->bindParam(':CenterDeviceView', $CenterDeviceView);
	$Stmt->execute();
	$Stmt = null;
	
}else{

	$Sql = " update CenterDevices set ";
		$Sql .= " CenterID = :CenterID, ";
		$Sql .= " CenterDeviceName = :CenterDeviceName, ";
		$Sql .= " CenterDeviceType = :CenterDeviceType, ";
		$Sql .= " CenterDeviceModiDateTime = now(), ";
		$Sql .= " CenterDeviceState = :CenterDeviceState, ";
		$Sql .= " CenterDeviceView = :CenterDeviceView ";
	$Sql .= " where CenterDeviceID = :CenterDeviceID ";


	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterDeviceID', $CenterDeviceID);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':CenterDeviceName', $CenterDeviceName);
	$Stmt->bindParam(':CenterDeviceType', $CenterDeviceType);
	$Stmt->bindParam(':CenterDeviceState', $CenterDeviceState);
	$Stmt->bindParam(':CenterDeviceView', $CenterDeviceView);
	$Stmt->execute();
	$Stmt = null;


}


if ($err_num != 0){
	include_once('./inc_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
//history.go(-1);
</script>
<?php
	include_once('./inc_footer.php'); 
?>
</body>
</html>
<?
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
?>
<script>
//parent.$.fn.colorbox.close();
parent.location.href = "center_form.php?<?=$ListParam?>&CenterID=<?=$CenterID?>&PageTabID=3";

</script>
<?
}
?>


