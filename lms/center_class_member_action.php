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
$CenterClassMemberID = (int)isset($_REQUEST["CenterClassMemberID"]) ? $_REQUEST["CenterClassMemberID"] : "";
$CenterID = (int)isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : 0;
$CenterClassMemberWeekNum = (int)isset($_REQUEST["CenterClassMemberWeekNum"]) ? $_REQUEST["CenterClassMemberWeekNum"] : 0;
$CenterClassMemberStartTime = isset($_REQUEST["CenterClassMemberStartTime"]) ? $_REQUEST["CenterClassMemberStartTime"] : "";
$CenterClassMemberEndTime = isset($_REQUEST["CenterClassMemberEndTime"]) ? $_REQUEST["CenterClassMemberEndTime"] : "";
$CenterClassMemberName = isset($_REQUEST["CenterClassMemberName"]) ? $_REQUEST["CenterClassMemberName"] : "";
$CenterClassMemberRegDateTime = isset($_REQUEST["CenterClassMemberRegDateTime"]) ? $_REQUEST["CenterClassMemberRegDateTime"] : "";
$CenterClassMemberModiDateTime = isset($_REQUEST["CenterClassMemberModiDateTime"]) ? $_REQUEST["CenterClassMemberModiDateTime"] : "";
$CenterClassMemberState = (int)isset($_REQUEST["CenterClassMemberState"]) ? $_REQUEST["CenterClassMemberState"] : "";
$CenterClassMemberView = (int)isset($_REQUEST["CenterClassMemberView"]) ? $_REQUEST["CenterClassMemberView"] : "";
*/

$CenterClassMemberID = isset($_REQUEST["CenterClassMemberID"]) ? $_REQUEST["CenterClassMemberID"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : 0;
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$CenterDeviceID = isset($_REQUEST["CenterDeviceID"]) ? $_REQUEST["CenterDeviceID"] : 0;
$CenterClassID = isset($_REQUEST["CenterClassID"]) ? $_REQUEST["CenterClassID"] : "";
$CenterClassMemberSeatNum = isset($_REQUEST["CenterClassMemberSeatNum"]) ? $_REQUEST["CenterClassMemberSeatNum"] : 0;
$CenterClassMemberLoginStatus = isset($_REQUEST["CenterClassMemberLoginStatus"]) ? $_REQUEST["CenterClassMemberLoginStatus"] : 0;
$CenterClassMemberRegDateTime = isset($_REQUEST["CenterClassMemberRegDateTime"]) ? $_REQUEST["CenterClassMemberRegDateTime"] : "";
$CenterClassMemberModiDateTime = isset($_REQUEST["CenterClassMemberModiDateTime"]) ? $_REQUEST["CenterClassMemberModiDateTime"] : "";
$CenterClassMemberState = isset($_REQUEST["CenterClassMemberState"]) ? $_REQUEST["CenterClassMemberState"] : 2;
$CenterClassMemberView = isset($_REQUEST["CenterClassMemberView"]) ? $_REQUEST["CenterClassMemberView"] : 2;

if ($CenterClassMemberView!="1"){
	$CenterClassMemberView = 0;
}


// $CenterClassMemberLevelID = 19;//학생


if ($CenterClassMemberID==""){

	$Sql = " insert into CenterClassMembers ( ";
		$Sql .= " CenterClassID, ";
		$Sql .= " MemberID, ";
		$Sql .= " CenterDeviceID, ";
		$Sql .= " CenterClassMemberSeatNum, ";
		$Sql .= " CenterClassMemberLoginStatus, ";
		$Sql .= " CenterClassMemberRegDateTime, ";
		$Sql .= " CenterClassMemberModiDateTime, ";
		$Sql .= " CenterClassMemberState, ";
		$Sql .= " CenterClassMemberView ";

	$Sql .= " ) values ( ";

		$Sql .= " :CenterClassID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " :CenterDeviceID, ";
		$Sql .= " :CenterClassMemberSeatNum, ";
		$Sql .= " :CenterClassMemberLoginStatus, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :CenterClassMemberState, ";
		$Sql .= " :CenterClassMemberView ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterClassID', $CenterClassID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':CenterDeviceID', $CenterDeviceID);
	$Stmt->bindParam(':CenterClassMemberSeatNum', $CenterClassMemberSeatNum);
	$Stmt->bindParam(':CenterClassMemberLoginStatus', $CenterClassMemberLoginStatus);
	$Stmt->bindParam(':CenterClassMemberState', $CenterClassMemberState);
	$Stmt->bindParam(':CenterClassMemberView', $CenterClassMemberView);
	$Stmt->execute();
	$Stmt = null;
	
}else{

	$Sql = " update CenterClassMembers set ";
		$Sql .= " CenterClassID = :CenterClassID, ";
		$Sql .= " MemberID = :MemberID, ";
		$Sql .= " CenterDeviceID = :CenterDeviceID, ";
		$Sql .= " CenterClassMemberSeatNum = :CenterClassMemberSeatNum, ";
		$Sql .= " CenterClassMemberLoginStatus = :CenterClassMemberLoginStatus, ";
		$Sql .= " CenterClassMemberModiDateTime = now(), ";
		$Sql .= " CenterClassMemberState = :CenterClassMemberState, ";
		$Sql .= " CenterClassMemberView = :CenterClassMemberView ";
	$Sql .= " where CenterClassMemberID = :CenterClassMemberID ";


	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterClassID', $CenterClassID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':CenterDeviceID', $CenterDeviceID);
	$Stmt->bindParam(':CenterClassMemberSeatNum', $CenterClassMemberSeatNum);
	$Stmt->bindParam(':CenterClassMemberLoginStatus', $CenterClassMemberLoginStatus);
	$Stmt->bindParam(':CenterClassMemberState', $CenterClassMemberState);
	$Stmt->bindParam(':CenterClassMemberView', $CenterClassMemberView);
	$Stmt->bindParam(':CenterClassMemberID', $CenterClassMemberID);
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
parent.location.href = "center_form.php?<?=$ListParam?>&CenterID=<?=$CenterID?>&PageTabID=4";

</script>
<?
}
?>


