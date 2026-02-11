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

$CounselID = isset($_REQUEST["CounselID"]) ? $_REQUEST["CounselID"] : "";
$RegMemberID = isset($_REQUEST["RegMemberID"]) ? $_REQUEST["RegMemberID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$TeacherWriteName = isset($_REQUEST["TeacherWriteName"]) ? $_REQUEST["TeacherWriteName"] : "";
$MemberWriteName = isset($_REQUEST["MemberWriteName"]) ? $_REQUEST["MemberWriteName"] : "";
$CounselDate = isset($_REQUEST["CounselDate"]) ? $_REQUEST["CounselDate"] : "";
$CounselTime = isset($_REQUEST["CounselTime"]) ? $_REQUEST["CounselTime"] : "";
$CounselTitle = isset($_REQUEST["CounselTitle"]) ? $_REQUEST["CounselTitle"] : "";
$CounselContent = isset($_REQUEST["CounselContent"]) ? $_REQUEST["CounselContent"] : "";
$CounselAnswerContent = isset($_REQUEST["CounselAnswerContent"]) ? $_REQUEST["CounselAnswerContent"] : "";
$CounselSms = isset($_REQUEST["CounselSms"]) ? $_REQUEST["CounselSms"] : "";
$CounselState = isset($_REQUEST["CounselState"]) ? $_REQUEST["CounselState"] : "";


if ($CounselState!="1"){
	$CounselState = 0;
}

if ($CounselSms=="1"){
	//문자전송
}


if ($CounselID==""){

	$Sql = " insert into Counsels ( ";
		$Sql .= " RegMemberID, ";
		$Sql .= " MemberID, ";
		$Sql .= " TeacherWriteName, ";
		$Sql .= " MemberWriteName, ";
		$Sql .= " CounselDate, ";
		$Sql .= " CounselTime, ";
		$Sql .= " CounselTitle, ";
		$Sql .= " CounselContent, ";
		$Sql .= " CounselAnswerContent, ";
		if ($CounselSms=="1"){
			$Sql .= " CounselSms, ";
			$Sql .= " CounselSmsDateTime, ";
		}
		$Sql .= " CounselRegDateTime, ";
		$Sql .= " CounselModiDateTime, ";
		$Sql .= " CounselView, ";
		$Sql .= " CounselState ";
	$Sql .= " ) values ( ";
		$Sql .= " :RegMemberID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " :TeacherWriteName, ";
		$Sql .= " :MemberWriteName, ";
		$Sql .= " :CounselDate, ";
		$Sql .= " :CounselTime, ";
		$Sql .= " :CounselTitle, ";
		$Sql .= " :CounselContent, ";
		$Sql .= " :CounselAnswerContent, ";
		if ($CounselSms=="1"){
			$Sql .= " 1, ";
			$Sql .= " now(), ";
		}
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " 1, ";
		$Sql .= " :CounselState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':RegMemberID', $RegMemberID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':TeacherWriteName', $TeacherWriteName);
	$Stmt->bindParam(':MemberWriteName', $MemberWriteName);
	$Stmt->bindParam(':CounselDate', $CounselDate);
	$Stmt->bindParam(':CounselTime', $CounselTime);
	$Stmt->bindParam(':CounselTitle', $CounselTitle);
	$Stmt->bindParam(':CounselContent', $CounselContent);
	$Stmt->bindParam(':CounselAnswerContent', $CounselAnswerContent);
	$Stmt->bindParam(':CounselState', $CounselState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update Counsels set ";
		//$Sql .= " TeacherWriteName = :TeacherWriteName, ";
		//$Sql .= " MemberWriteName = :MemberWriteName, ";
		//$Sql .= " CounselDate = :CounselDate, ";
		//$Sql .= " CounselTime = :CounselTime, ";
		//$Sql .= " CounselTitle = :CounselTitle, ";
		//$Sql .= " CounselContent = :CounselContent, ";
		$Sql .= " CounselAnswerContent = :CounselAnswerContent, ";
		if ($CounselSms=="1"){
			$Sql .= " CounselSms = 1, ";
			$Sql .= " CounselSmsDateTime = now(), ";
		}
		$Sql .= " CounselModiDateTime = now(), ";
		$Sql .= " CounselState = :CounselState ";
	$Sql .= " where CounselID = :CounselID ";

	$Stmt = $DbConn->prepare($Sql);
	//$Stmt->bindParam(':TeacherWriteName', $TeacherWriteName);
	//$Stmt->bindParam(':MemberWriteName', $MemberWriteName);
	//$Stmt->bindParam(':CounselDate', $CounselDate);
	//$Stmt->bindParam(':CounselTime', $CounselTime);
	//$Stmt->bindParam(':CounselTitle', $CounselTitle);
	//$Stmt->bindParam(':CounselContent', $CounselContent);
	$Stmt->bindParam(':CounselAnswerContent', $CounselAnswerContent);
	$Stmt->bindParam(':CounselState', $CounselState);
	$Stmt->bindParam(':CounselID', $CounselID);
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
<?if ($CounselID==""){?>
$.confirm({
	title: '',
	content: "상담 내용을 저장했습니다.<br>상담내역에서 확인하실 수 있습니다.",
	buttons: {
		닫기: function () {
			parent.$.fn.colorbox.close();
		},
		상담내역이동: function () {
			parent.location.href = "counsel_list.php";
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

