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
$BookQuizID = isset($_REQUEST["BookQuizID"]) ? $_REQUEST["BookQuizID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BookQuizName = isset($_REQUEST["BookQuizName"]) ? $_REQUEST["BookQuizName"] : "";
$BookQuizMemo = isset($_REQUEST["BookQuizMemo"]) ? $_REQUEST["BookQuizMemo"] : "";
$BookQuizState = isset($_REQUEST["BookQuizState"]) ? $_REQUEST["BookQuizState"] : "";


if ($BookQuizState!="1"){
	$BookQuizState = 2;
}

$BookQuizView = 1;



if ($BookQuizID==""){

	$Sql = "select ifnull(Max(BookQuizOrder),0) as BookQuizOrder from BookQuizs";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	
	$BookQuizOrder = $Row["BookQuizOrder"]+1;


	$Sql = " insert into BookQuizs ( ";
		$Sql .= " BookID, ";
		$Sql .= " BookQuizName, ";
		$Sql .= " BookQuizMemo, ";
		$Sql .= " BookQuizRegDateTime, ";
		$Sql .= " BookQuizModiDateTime, ";
		$Sql .= " BookQuizOrder, ";
		$Sql .= " BookQuizView, ";
		$Sql .= " BookQuizState ";
	$Sql .= " ) values ( ";
		$Sql .= " :BookID, ";
		$Sql .= " :BookQuizName, ";
		$Sql .= " :BookQuizMemo, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :BookQuizOrder, ";
		$Sql .= " :BookQuizView, ";
		$Sql .= " :BookQuizState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookID', $BookID);
	$Stmt->bindParam(':BookQuizName', $BookQuizName);
	$Stmt->bindParam(':BookQuizMemo', $BookQuizMemo);
	$Stmt->bindParam(':BookQuizOrder', $BookQuizOrder);
	$Stmt->bindParam(':BookQuizView', $BookQuizView);
	$Stmt->bindParam(':BookQuizState', $BookQuizState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update BookQuizs set ";
		$Sql .= " BookQuizName = :BookQuizName, ";
		$Sql .= " BookQuizMemo = :BookQuizMemo, ";
		$Sql .= " BookQuizView = :BookQuizView, ";
		$Sql .= " BookQuizState = :BookQuizState, ";
		$Sql .= " BookQuizModiDateTime = now() ";
	$Sql .= " where BookQuizID = :BookQuizID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookQuizName', $BookQuizName);
	$Stmt->bindParam(':BookQuizMemo', $BookQuizMemo);
	$Stmt->bindParam(':BookQuizView', $BookQuizView);
	$Stmt->bindParam(':BookQuizState', $BookQuizState);
	$Stmt->bindParam(':BookQuizID', $BookQuizID);
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
parent.location.href = "book_form.php?<?=$ListParam?>&BookID=<?=$BookID?>&PageTabID=3";
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

