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

$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";
$BookVideoID = isset($_REQUEST["BookVideoID"]) ? $_REQUEST["BookVideoID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BookVideoName = isset($_REQUEST["BookVideoName"]) ? $_REQUEST["BookVideoName"] : "";
$BookVideoType = isset($_REQUEST["BookVideoType"]) ? $_REQUEST["BookVideoType"] : "";
$BookVideoType2 = isset($_REQUEST["BookVideoType2"]) ? $_REQUEST["BookVideoType2"] : "";
$BookVideoCode = isset($_REQUEST["BookVideoCode"]) ? $_REQUEST["BookVideoCode"] : "";
$BookVideoCode2 = isset($_REQUEST["BookVideoCode2"]) ? $_REQUEST["BookVideoCode2"] : "";
$BookVideoMemo = isset($_REQUEST["BookVideoMemo"]) ? $_REQUEST["BookVideoMemo"] : "";
$BookVideoState = isset($_REQUEST["BookVideoState"]) ? $_REQUEST["BookVideoState"] : "";


if ($BookVideoState!="1"){
	$BookVideoState = 2;
}

$BookVideoView = 1;



if ($BookVideoID==""){

	$Sql = "select ifnull(Max(BookVideoOrder),0) as BookVideoOrder from BookVideos";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	
	$BookVideoOrder = $Row["BookVideoOrder"]+1;


	$Sql = " insert into BookVideos ( ";
		$Sql .= " BookID, ";
		$Sql .= " BookVideoName, ";
		$Sql .= " BookVideoMemo, ";
		$Sql .= " BookVideoType, ";
		$Sql .= " BookVideoType2, ";
		$Sql .= " BookVideoCode, ";
		$Sql .= " BookVideoCode2, ";
		$Sql .= " BookVideoRegDateTime, ";
		$Sql .= " BookVideoModiDateTime, ";
		$Sql .= " BookVideoOrder, ";
		$Sql .= " BookVideoView, ";
		$Sql .= " BookVideoState ";
	$Sql .= " ) values ( ";
		$Sql .= " :BookID, ";
		$Sql .= " :BookVideoName, ";
		$Sql .= " :BookVideoMemo, ";
		$Sql .= " :BookVideoType, ";
		$Sql .= " :BookVideoType2, ";
		$Sql .= " :BookVideoCode, ";
		$Sql .= " :BookVideoCode2, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :BookVideoOrder, ";
		$Sql .= " :BookVideoView, ";
		$Sql .= " :BookVideoState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookID', $BookID);
	$Stmt->bindParam(':BookVideoName', $BookVideoName);
	$Stmt->bindParam(':BookVideoMemo', $BookVideoMemo);
	$Stmt->bindParam(':BookVideoType', $BookVideoType);
	$Stmt->bindParam(':BookVideoType2', $BookVideoType2);
	$Stmt->bindParam(':BookVideoCode', $BookVideoCode);
	$Stmt->bindParam(':BookVideoCode2', $BookVideoCode2);
	$Stmt->bindParam(':BookVideoOrder', $BookVideoOrder);
	$Stmt->bindParam(':BookVideoView', $BookVideoView);
	$Stmt->bindParam(':BookVideoState', $BookVideoState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update BookVideos set ";
		$Sql .= " BookVideoName = :BookVideoName, ";
		$Sql .= " BookVideoMemo = :BookVideoMemo, ";
		$Sql .= " BookVideoType = :BookVideoType, ";
		$Sql .= " BookVideoType2 = :BookVideoType2, ";
		$Sql .= " BookVideoCode = :BookVideoCode, ";
		$Sql .= " BookVideoCode2 = :BookVideoCode2, ";
		$Sql .= " BookVideoView = :BookVideoView, ";
		$Sql .= " BookVideoState = :BookVideoState, ";
		$Sql .= " BookVideoModiDateTime = now() ";
	$Sql .= " where BookVideoID = :BookVideoID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookVideoName', $BookVideoName);
	$Stmt->bindParam(':BookVideoMemo', $BookVideoMemo);
	$Stmt->bindParam(':BookVideoType', $BookVideoType);
	$Stmt->bindParam(':BookVideoType2', $BookVideoType2);
	$Stmt->bindParam(':BookVideoCode', $BookVideoCode);
	$Stmt->bindParam(':BookVideoCode2', $BookVideoCode2);
	$Stmt->bindParam(':BookVideoView', $BookVideoView);
	$Stmt->bindParam(':BookVideoState', $BookVideoState);
	$Stmt->bindParam(':BookVideoID', $BookVideoID);
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
parent.location.href = "book_form.php?<?=$ListParam?>&BookID=<?=$BookID?>&PageTabID=2";
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

