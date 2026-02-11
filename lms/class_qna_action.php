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

$ClassQnaID = isset($_REQUEST["ClassQnaID"]) ? $_REQUEST["ClassQnaID"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$ClassQnaTitle = isset($_REQUEST["ClassQnaTitle"]) ? $_REQUEST["ClassQnaTitle"] : "";
$ClassQnaContent = isset($_REQUEST["ClassQnaContent"]) ? $_REQUEST["ClassQnaContent"] : "";
$ClassQnaState = isset($_REQUEST["ClassQnaState"]) ? $_REQUEST["ClassQnaState"] : "";
$ClassQnaAnswer = isset($_REQUEST["ClassQnaAnswer"]) ? $_REQUEST["ClassQnaAnswer"] : "";


if ($ClassQnaID==""){

	$Sql = " insert into ClassQnas ( ";
		$Sql .= " ClassID, ";
		$Sql .= " ClassQnaTitle, ";
		$Sql .= " ClassQnaContent, ";
		$Sql .= " ClassQnaRegDateTime, ";
		$Sql .= " ClassQnaModiDateTime, ";
		$Sql .= " ClassQnaState ";
	$Sql .= " ) values ( ";
		$Sql .= " :ClassID, ";
		$Sql .= " :ClassQnaTitle, ";
		$Sql .= " :ClassQnaContent, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :ClassQnaState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassID', $ClassID);
	$Stmt->bindParam(':ClassQnaTitle', $ClassQnaTitle);
	$Stmt->bindParam(':ClassQnaContent', $ClassQnaContent);
	$Stmt->bindParam(':ClassQnaState', $ClassQnaState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update ClassQnas set ";
		$Sql .= " ClassQnaTitle = :ClassQnaTitle, ";
		$Sql .= " ClassQnaContent = :ClassQnaContent, ";
		$Sql .= " ClassQnaAnswer = :ClassQnaAnswer, ";
		$Sql .= " ClassQnaAnswerRegDateTime = now(), ";//한번만 입력하도록 수정
		$Sql .= " ClassQnaAnswerModiDateTime = now(), ";
		$Sql .= " ClassQnaState = :ClassQnaState ";
	$Sql .= " where ClassQnaID = :ClassQnaID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassQnaTitle', $ClassQnaTitle);
	$Stmt->bindParam(':ClassQnaContent', $ClassQnaContent);
	$Stmt->bindParam(':ClassQnaAnswer', $ClassQnaAnswer);
	$Stmt->bindParam(':ClassQnaState', $ClassQnaState);
	$Stmt->bindParam(':ClassQnaID', $ClassQnaID);
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
<?if ($ClassQnaID==""){?>
$.confirm({
	title: '',
	content: "요청 내용을 저장했습니다.<br>요청내역에서 확인하실 수 있습니다.",
	buttons: {
		닫기: function () {
			parent.$.fn.colorbox.close();
		},
		요청내역이동: function () {
			parent.location.href = "class_qna_list.php";
		}
	}
});
<?}else{?>
	parent.$.fn.colorbox.close();
<?}?>
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

