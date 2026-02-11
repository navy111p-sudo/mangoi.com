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

$TeacherListBestID = isset($_REQUEST["TeacherListBestID"]) ? $_REQUEST["TeacherListBestID"] : "";
$TeacherListBestName = isset($_REQUEST["TeacherListBestName"]) ? $_REQUEST["TeacherListBestName"] : "";
$TeacherListBestTeacherID1 = isset($_REQUEST["TeacherListBestTeacherID1"]) ? $_REQUEST["TeacherListBestTeacherID1"] : "";
$TeacherListBestTeacherID2 = isset($_REQUEST["TeacherListBestTeacherID2"]) ? $_REQUEST["TeacherListBestTeacherID2"] : "";
$TeacherListBestTeacherID3 = isset($_REQUEST["TeacherListBestTeacherID3"]) ? $_REQUEST["TeacherListBestTeacherID3"] : "";
$TeacherListBestTeacherID4 = isset($_REQUEST["TeacherListBestTeacherID4"]) ? $_REQUEST["TeacherListBestTeacherID4"] : "";
$TeacherListBestTeacherID5 = isset($_REQUEST["TeacherListBestTeacherID5"]) ? $_REQUEST["TeacherListBestTeacherID5"] : "";
$TeacherListBestTeacherID6 = isset($_REQUEST["TeacherListBestTeacherID6"]) ? $_REQUEST["TeacherListBestTeacherID6"] : "";
$TeacherListBestTeacherID7 = isset($_REQUEST["TeacherListBestTeacherID7"]) ? $_REQUEST["TeacherListBestTeacherID7"] : "";
$TeacherListBestTeacherID8 = isset($_REQUEST["TeacherListBestTeacherID8"]) ? $_REQUEST["TeacherListBestTeacherID8"] : "";
$TeacherListBestTeacherID9 = isset($_REQUEST["TeacherListBestTeacherID9"]) ? $_REQUEST["TeacherListBestTeacherID9"] : "";
$TeacherListBestTeacherID10 = isset($_REQUEST["TeacherListBestTeacherID10"]) ? $_REQUEST["TeacherListBestTeacherID10"] : "";

$TeacherListBestState = isset($_REQUEST["TeacherListBestState"]) ? $_REQUEST["TeacherListBestState"] : "";

if ($TeacherListBestState!="1"){
	$TeacherListBestState = 2;
}


if ($TeacherListBestID==""){

	$Sql = " insert into TeacherListBests ( ";
		$Sql .= " TeacherListBestName, ";
		$Sql .= " TeacherListBestTeacherID1, ";
		$Sql .= " TeacherListBestTeacherID2, ";
		$Sql .= " TeacherListBestTeacherID3, ";
		$Sql .= " TeacherListBestTeacherID4, ";
		$Sql .= " TeacherListBestTeacherID5, ";
		$Sql .= " TeacherListBestTeacherID6, ";
		$Sql .= " TeacherListBestTeacherID7, ";
		$Sql .= " TeacherListBestTeacherID8, ";
		$Sql .= " TeacherListBestTeacherID9, ";
		$Sql .= " TeacherListBestTeacherID10, ";
		$Sql .= " TeacherListBestRegDateTime, ";
		$Sql .= " TeacherListBestModiDateTime, ";
		$Sql .= " TeacherListBestState ";
	$Sql .= " ) values ( ";
		$Sql .= " :TeacherListBestName, ";
		$Sql .= " :TeacherListBestTeacherID1, ";
		$Sql .= " :TeacherListBestTeacherID2, ";
		$Sql .= " :TeacherListBestTeacherID3, ";
		$Sql .= " :TeacherListBestTeacherID4, ";
		$Sql .= " :TeacherListBestTeacherID5, ";
		$Sql .= " :TeacherListBestTeacherID6, ";
		$Sql .= " :TeacherListBestTeacherID7, ";
		$Sql .= " :TeacherListBestTeacherID8, ";
		$Sql .= " :TeacherListBestTeacherID9, ";
		$Sql .= " :TeacherListBestTeacherID10, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :TeacherListBestState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherListBestName', $TeacherListBestName);
	$Stmt->bindParam(':TeacherListBestTeacherID1', $TeacherListBestTeacherID1);
	$Stmt->bindParam(':TeacherListBestTeacherID2', $TeacherListBestTeacherID2);
	$Stmt->bindParam(':TeacherListBestTeacherID3', $TeacherListBestTeacherID3);
	$Stmt->bindParam(':TeacherListBestTeacherID4', $TeacherListBestTeacherID4);
	$Stmt->bindParam(':TeacherListBestTeacherID5', $TeacherListBestTeacherID5);
	$Stmt->bindParam(':TeacherListBestTeacherID6', $TeacherListBestTeacherID6);
	$Stmt->bindParam(':TeacherListBestTeacherID7', $TeacherListBestTeacherID7);
	$Stmt->bindParam(':TeacherListBestTeacherID8', $TeacherListBestTeacherID8);
	$Stmt->bindParam(':TeacherListBestTeacherID9', $TeacherListBestTeacherID9);
	$Stmt->bindParam(':TeacherListBestTeacherID10', $TeacherListBestTeacherID10);
	$Stmt->bindParam(':TeacherListBestState', $TeacherListBestState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update TeacherListBests set ";
		$Sql .= " TeacherListBestName = :TeacherListBestName, ";
		$Sql .= " TeacherListBestTeacherID1 = :TeacherListBestTeacherID1, ";
		$Sql .= " TeacherListBestTeacherID2 = :TeacherListBestTeacherID2, ";
		$Sql .= " TeacherListBestTeacherID3 = :TeacherListBestTeacherID3, ";
		$Sql .= " TeacherListBestTeacherID4 = :TeacherListBestTeacherID4, ";
		$Sql .= " TeacherListBestTeacherID5 = :TeacherListBestTeacherID5, ";
		$Sql .= " TeacherListBestTeacherID6 = :TeacherListBestTeacherID6, ";
		$Sql .= " TeacherListBestTeacherID7 = :TeacherListBestTeacherID7, ";
		$Sql .= " TeacherListBestTeacherID8 = :TeacherListBestTeacherID8, ";
		$Sql .= " TeacherListBestTeacherID9 = :TeacherListBestTeacherID9, ";
		$Sql .= " TeacherListBestTeacherID10 = :TeacherListBestTeacherID10, ";
		$Sql .= " TeacherListBestModiDateTime = now(), ";
		$Sql .= " TeacherListBestState = :TeacherListBestState ";
	$Sql .= " where TeacherListBestID = :TeacherListBestID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherListBestName', $TeacherListBestName);
	$Stmt->bindParam(':TeacherListBestTeacherID1', $TeacherListBestTeacherID1);
	$Stmt->bindParam(':TeacherListBestTeacherID2', $TeacherListBestTeacherID2);
	$Stmt->bindParam(':TeacherListBestTeacherID3', $TeacherListBestTeacherID3);
	$Stmt->bindParam(':TeacherListBestTeacherID4', $TeacherListBestTeacherID4);
	$Stmt->bindParam(':TeacherListBestTeacherID5', $TeacherListBestTeacherID5);
	$Stmt->bindParam(':TeacherListBestTeacherID6', $TeacherListBestTeacherID6);
	$Stmt->bindParam(':TeacherListBestTeacherID7', $TeacherListBestTeacherID7);
	$Stmt->bindParam(':TeacherListBestTeacherID8', $TeacherListBestTeacherID8);
	$Stmt->bindParam(':TeacherListBestTeacherID9', $TeacherListBestTeacherID9);
	$Stmt->bindParam(':TeacherListBestTeacherID10', $TeacherListBestTeacherID10);
	$Stmt->bindParam(':TeacherListBestState', $TeacherListBestState);
	$Stmt->bindParam(':TeacherListBestID', $TeacherListBestID);
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
parent.$.fn.colorbox.close();
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

