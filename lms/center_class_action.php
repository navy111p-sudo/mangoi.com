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
$CenterClassID = (int)isset($_REQUEST["CenterClassID"]) ? $_REQUEST["CenterClassID"] : "";
$CenterID = (int)isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : 0;
$CenterClassWeekNum = (int)isset($_REQUEST["CenterClassWeekNum"]) ? $_REQUEST["CenterClassWeekNum"] : 0;
$CenterClassStartTime = isset($_REQUEST["CenterClassStartTime"]) ? $_REQUEST["CenterClassStartTime"] : "";
$CenterClassEndTime = isset($_REQUEST["CenterClassEndTime"]) ? $_REQUEST["CenterClassEndTime"] : "";
$CenterClassName = isset($_REQUEST["CenterClassName"]) ? $_REQUEST["CenterClassName"] : "";
$CenterClassRegDateTime = isset($_REQUEST["CenterClassRegDateTime"]) ? $_REQUEST["CenterClassRegDateTime"] : "";
$CenterClassModiDateTime = isset($_REQUEST["CenterClassModiDateTime"]) ? $_REQUEST["CenterClassModiDateTime"] : "";
$CenterClassState = (int)isset($_REQUEST["CenterClassState"]) ? $_REQUEST["CenterClassState"] : "";
$CenterClassView = (int)isset($_REQUEST["CenterClassView"]) ? $_REQUEST["CenterClassView"] : "";
*/
$CenterClassID = isset($_REQUEST["CenterClassID"]) ? $_REQUEST["CenterClassID"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : 0;
$CenterClassWeekNum = isset($_REQUEST["CenterClassWeekNum"]) ? $_REQUEST["CenterClassWeekNum"] : 0;
$CenterClassStartTime = isset($_REQUEST["CenterClassStartTime"]) ? $_REQUEST["CenterClassStartTime"] : "";
$CenterClassEndTime = isset($_REQUEST["CenterClassEndTime"]) ? $_REQUEST["CenterClassEndTime"] : "";
$CenterClassName = isset($_REQUEST["CenterClassName"]) ? $_REQUEST["CenterClassName"] : "";
$CenterClassRegDateTime = isset($_REQUEST["CenterClassRegDateTime"]) ? $_REQUEST["CenterClassRegDateTime"] : "";
$CenterClassModiDateTime = isset($_REQUEST["CenterClassModiDateTime"]) ? $_REQUEST["CenterClassModiDateTime"] : "";
$CenterClassState = isset($_REQUEST["CenterClassState"]) ? $_REQUEST["CenterClassState"] : 2;
$CenterClassView = isset($_REQUEST["CenterClassView"]) ? $_REQUEST["CenterClassView"] : 2;


if ($CenterClassView!="1"){
	$CenterClassView = 0;
}


// $CenterClassLevelID = 19;//학생


if ($CenterClassID==""){

	$Sql = " insert into CenterClasses ( ";
		$Sql .= " CenterID, ";
		$Sql .= " CenterClassWeekNum, ";
		$Sql .= " CenterClassStartTime, ";
		$Sql .= " CenterClassEndTime, ";
		$Sql .= " CenterClassName, ";
		$Sql .= " CenterClassRegDateTime, ";
		$Sql .= " CenterClassModiDateTime, ";
		$Sql .= " CenterClassState, ";
		$Sql .= " CenterClassView ";

	$Sql .= " ) values ( ";

		$Sql .= " :CenterID, ";
		$Sql .= " :CenterClassWeekNum, ";
		$Sql .= " :CenterClassStartTime, ";
		$Sql .= " :CenterClassEndTime, ";
		$Sql .= " :CenterClassName, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :CenterClassState, ";
		$Sql .= " :CenterClassView ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':CenterClassWeekNum', $CenterClassWeekNum);
	$Stmt->bindParam(':CenterClassStartTime', $CenterClassStartTime);
	$Stmt->bindParam(':CenterClassEndTime', $CenterClassEndTime);
	$Stmt->bindParam(':CenterClassName', $CenterClassName);
	$Stmt->bindParam(':CenterClassState', $CenterClassState);
	$Stmt->bindParam(':CenterClassView', $CenterClassView);
	$Stmt->execute();
	$Stmt = null;
	
}else{

	$Sql = " update CenterClasses set ";
		$Sql .= " CenterID = :CenterID, ";
		$Sql .= " CenterClassWeekNum = :CenterClassWeekNum, ";
		$Sql .= " CenterClassStartTime = :CenterClassStartTime, ";
		$Sql .= " CenterClassEndTime = :CenterClassEndTime, ";
		$Sql .= " CenterClassName = :CenterClassName, ";
		$Sql .= " CenterClassModiDateTime = now(), ";
		$Sql .= " CenterClassState = :CenterClassState, ";
		$Sql .= " CenterClassView = :CenterClassView ";
	$Sql .= " where CenterClassID = :CenterClassID ";


	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterClassID', $CenterClassID);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':CenterClassWeekNum', $CenterClassWeekNum);
	$Stmt->bindParam(':CenterClassStartTime', $CenterClassStartTime);
	$Stmt->bindParam(':CenterClassEndTime', $CenterClassEndTime);
	$Stmt->bindParam(':CenterClassName', $CenterClassName);
	$Stmt->bindParam(':CenterClassState', $CenterClassState);
	$Stmt->bindParam(':CenterClassView', $CenterClassView);
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
parent.location.href = "center_form.php?<?=$ListParam?>&CenterID=<?=$CenterID?>&PageTabID=2";

</script>
<?
}
?>


