<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
?>
<link rel="stylesheet" type="text/css" href="css/common.css" />
</head>
</body>
<?

$err_num = 0;
$err_msg = "";

$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$Hr_OrganLevelID = isset($_REQUEST["Hr_OrganLevelID"]) ? $_REQUEST["Hr_OrganLevelID"] : "";
$Hr_OrganLevelName = isset($_REQUEST["Hr_OrganLevelName"]) ? $_REQUEST["Hr_OrganLevelName"] : "";
$Hr_OrganLevel = isset($_REQUEST["Hr_OrganLevel"]) ? $_REQUEST["Hr_OrganLevel"] : "";
$Hr_OrganLevel1ID = isset($_REQUEST["Hr_OrganLevel1ID"]) ? $_REQUEST["Hr_OrganLevel1ID"] : "";
$Hr_OrganLevel2ID = isset($_REQUEST["Hr_OrganLevel2ID"]) ? $_REQUEST["Hr_OrganLevel2ID"] : "";
$Hr_OrganLevel3ID = isset($_REQUEST["Hr_OrganLevel3ID"]) ? $_REQUEST["Hr_OrganLevel3ID"] : "";
$Hr_OrganLevel4ID = isset($_REQUEST["Hr_OrganLevel4ID"]) ? $_REQUEST["Hr_OrganLevel4ID"] : "";

$Hr_Incentive1 = isset($_REQUEST["Hr_Incentive1"]) ? $_REQUEST["Hr_Incentive1"] : "";
$Hr_Incentive2 = isset($_REQUEST["Hr_Incentive2"]) ? $_REQUEST["Hr_Incentive2"] : "";
$Hr_Incentive3 = isset($_REQUEST["Hr_Incentive3"]) ? $_REQUEST["Hr_Incentive3"] : "";
$Hr_Incentive4 = isset($_REQUEST["Hr_Incentive4"]) ? $_REQUEST["Hr_Incentive4"] : "";
$Hr_Incentive5 = isset($_REQUEST["Hr_Incentive5"]) ? $_REQUEST["Hr_Incentive5"] : "";

$Hr_OrganLevelState = isset($_REQUEST["Hr_OrganLevelState"]) ? $_REQUEST["Hr_OrganLevelState"] : "";

if ($Hr_OrganLevel1ID==""){
	$Hr_OrganLevel1ID = 0;
}
if ($Hr_OrganLevel2ID==""){
	$Hr_OrganLevel2ID = 0;
}
if ($Hr_OrganLevel3ID==""){
	$Hr_OrganLevel3ID = 0;
}
if ($Hr_OrganLevel4ID==""){
	$Hr_OrganLevel4ID = 0;
}

if ($Hr_OrganLevel=="1"){
	$Hr_OrganLevel2ID = 0;
	$Hr_OrganLevel3ID = 0;
	$Hr_OrganLevel4ID = 0;
}else if ($Hr_OrganLevel=="2"){
	$Hr_OrganLevel3ID = 0;
	$Hr_OrganLevel4ID = 0;
}else if ($Hr_OrganLevel=="3"){
	$Hr_OrganLevel4ID = 0;
}else if ($Hr_OrganLevel=="4"){

}


if ($Hr_OrganLevelID==""){

	if ($Hr_OrganLevel=="1"){
		$Hr_OrganLevel1ID = 0;
	}else if ($Hr_OrganLevel=="2"){
		$Hr_OrganLevel2ID = 0;
	}else if ($Hr_OrganLevel=="3"){
		$Hr_OrganLevel3ID = 0;
	}else if ($Hr_OrganLevel=="4"){
		$Hr_OrganLevel4ID = 0;
	}


	$Sql = " insert into Hr_OrganLevels ( ";
		$Sql .= " CenterID, ";
		$Sql .= " Hr_OrganLevel1ID, ";
		$Sql .= " Hr_OrganLevel2ID, ";
		$Sql .= " Hr_OrganLevel3ID, ";
		$Sql .= " Hr_OrganLevel4ID, ";
		$Sql .= " Hr_OrganLevel, ";
		$Sql .= " Hr_Incentive1, ";
		$Sql .= " Hr_Incentive2, ";
		$Sql .= " Hr_Incentive3, ";
		$Sql .= " Hr_Incentive4, ";
		$Sql .= " Hr_Incentive5, ";
		$Sql .= " Hr_OrganLevelName, ";
		$Sql .= " Hr_OrganLevelRegDateTime, ";
		$Sql .= " Hr_OrganLevelModiDateTime, ";
		$Sql .= " Hr_OrganLevelState ";
	$Sql .= " ) values ( ";
		$Sql .= " :CenterID, ";
		$Sql .= " :Hr_OrganLevel1ID, ";
		$Sql .= " :Hr_OrganLevel2ID, ";
		$Sql .= " :Hr_OrganLevel3ID, ";
		$Sql .= " :Hr_OrganLevel4ID, ";
		$Sql .= " :Hr_OrganLevel, ";
		$Sql .= " :Hr_Incentive1, ";
		$Sql .= " :Hr_Incentive2, ";
		$Sql .= " :Hr_Incentive3, ";
		$Sql .= " :Hr_Incentive4, ";
		$Sql .= " :Hr_Incentive5, ";
		$Sql .= " :Hr_OrganLevelName, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :Hr_OrganLevelState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':Hr_OrganLevel1ID', $Hr_OrganLevel1ID);
	$Stmt->bindParam(':Hr_OrganLevel2ID', $Hr_OrganLevel2ID);
	$Stmt->bindParam(':Hr_OrganLevel3ID', $Hr_OrganLevel3ID);
	$Stmt->bindParam(':Hr_OrganLevel4ID', $Hr_OrganLevel4ID);
	$Stmt->bindParam(':Hr_OrganLevel', $Hr_OrganLevel);
	$Stmt->bindParam(':Hr_Incentive1', $Hr_Incentive1);
	$Stmt->bindParam(':Hr_Incentive2', $Hr_Incentive2);
	$Stmt->bindParam(':Hr_Incentive3', $Hr_Incentive3);
	$Stmt->bindParam(':Hr_Incentive4', $Hr_Incentive4);
	$Stmt->bindParam(':Hr_Incentive5', $Hr_Incentive5);
	$Stmt->bindParam(':Hr_OrganLevelName', $Hr_OrganLevelName);
	$Stmt->bindParam(':Hr_OrganLevelState', $Hr_OrganLevelState);
	$Stmt->execute();
	$In_Hr_OrganLevelID = $DbConn->lastInsertId();
	$Stmt = null;


	$Sql = " update Hr_OrganLevels set ";
		$Sql .= " Hr_OrganLevel".$Hr_OrganLevel."ID = :In_Hr_OrganLevelID, ";
		$Sql .= " Hr_OrganLevelModiDateTime = now() ";
	$Sql .= " where Hr_OrganLevelID = :In_Hr_OrganLevelID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':In_Hr_OrganLevelID', $In_Hr_OrganLevelID);
	$Stmt->execute();
	$Stmt = null;


}else{

	$Sql = " update Hr_OrganLevels set ";
		$Sql .= " Hr_OrganLevel1ID = :Hr_OrganLevel1ID, ";
		$Sql .= " Hr_OrganLevel2ID = :Hr_OrganLevel2ID, ";
		$Sql .= " Hr_OrganLevel3ID = :Hr_OrganLevel3ID, ";
		$Sql .= " Hr_OrganLevel4ID = :Hr_OrganLevel4ID, ";
		$Sql .= " Hr_OrganLevel = :Hr_OrganLevel, ";
		$Sql .= " Hr_Incentive1 = :Hr_Incentive1, ";
		$Sql .= " Hr_Incentive2 = :Hr_Incentive2, ";
		$Sql .= " Hr_Incentive3 = :Hr_Incentive3, ";
		$Sql .= " Hr_Incentive4 = :Hr_Incentive4, ";
		$Sql .= " Hr_Incentive5 = :Hr_Incentive5, ";
		$Sql .= " Hr_OrganLevelName = :Hr_OrganLevelName, ";
		$Sql .= " Hr_OrganLevelModiDateTime = now(), ";
		$Sql .= " Hr_OrganLevelState = :Hr_OrganLevelState ";
	$Sql .= " where Hr_OrganLevelID = :Hr_OrganLevelID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_OrganLevel1ID', $Hr_OrganLevel1ID);
	$Stmt->bindParam(':Hr_OrganLevel2ID', $Hr_OrganLevel2ID);
	$Stmt->bindParam(':Hr_OrganLevel3ID', $Hr_OrganLevel3ID);
	$Stmt->bindParam(':Hr_OrganLevel4ID', $Hr_OrganLevel4ID);
	$Stmt->bindParam(':Hr_OrganLevel', $Hr_OrganLevel);
	$Stmt->bindParam(':Hr_Incentive1', $Hr_Incentive1);
	$Stmt->bindParam(':Hr_Incentive2', $Hr_Incentive2);
	$Stmt->bindParam(':Hr_Incentive3', $Hr_Incentive3);
	$Stmt->bindParam(':Hr_Incentive4', $Hr_Incentive4);
	$Stmt->bindParam(':Hr_Incentive5', $Hr_Incentive5);
	$Stmt->bindParam(':Hr_OrganLevelName', $Hr_OrganLevelName);
	$Stmt->bindParam(':Hr_OrganLevelState', $Hr_OrganLevelState);
	$Stmt->bindParam(':Hr_OrganLevelID', $Hr_OrganLevelID);
	$Stmt->execute();
	$Stmt = null;

}
?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>
//parent.$.fn.colorbox.close();
parent.location.reload();
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

