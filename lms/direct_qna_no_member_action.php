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

$DirectQnaNoMemberID = isset($_REQUEST["DirectQnaNoMemberID"]) ? $_REQUEST["DirectQnaNoMemberID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
$DirectQnaNoMemberTitle = isset($_REQUEST["DirectQnaNoMemberTitle"]) ? $_REQUEST["DirectQnaNoMemberTitle"] : "";
$DirectQnaNoMemberContent = isset($_REQUEST["DirectQnaNoMemberContent"]) ? $_REQUEST["DirectQnaNoMemberContent"] : "";

$AnswerMemberID = isset($_REQUEST["AnswerMemberID"]) ? $_REQUEST["AnswerMemberID"] : "";
$AnswerMemberName = isset($_REQUEST["AnswerMemberName"]) ? $_REQUEST["AnswerMemberName"] : "";
$DirectQnaNoMemberAnswer = isset($_REQUEST["DirectQnaNoMemberAnswer"]) ? $_REQUEST["DirectQnaNoMemberAnswer"] : "";

$DirectQnaNoMemberState = isset($_REQUEST["DirectQnaNoMemberState"]) ? $_REQUEST["DirectQnaNoMemberState"] : "";


if ($DirectQnaNoMemberID==""){

	$Sql = " insert into DirectQnaNoMembers ( ";
		$Sql .= " MemberID, ";
		$Sql .= " MemberName, ";
		$Sql .= " DirectQnaNoMemberTitle, ";
		$Sql .= " DirectQnaNoMemberContent, ";
		$Sql .= " DirectQnaNoMemberRegDateTime, ";
		$Sql .= " DirectQnaNoMemberModiDateTime, ";
		$Sql .= " DirectQnaNoMemberState ";
	$Sql .= " ) values ( ";
		$Sql .= " :MemberID, ";
		$Sql .= " :MemberName, ";
		$Sql .= " :DirectQnaNoMemberTitle, ";
		$Sql .= " :DirectQnaNoMemberContent, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :DirectQnaNoMemberState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':MemberName', $MemberName);
	$Stmt->bindParam(':DirectQnaNoMemberTitle', $DirectQnaNoMemberTitle);
	$Stmt->bindParam(':DirectQnaNoMemberContent', $DirectQnaNoMemberContent);
	$Stmt->bindParam(':DirectQnaNoMemberState', $DirectQnaNoMemberState);
	$Stmt->execute();
	$Stmt = null;

}else{


	if ($_LINK_ADMIN_LEVEL_ID_<=4){//프랜차이즈 관리자가 쓴다면 답변


		$Sql = "
				select 
						A.*
				from DirectQnaNoMembers A 
				where A.DirectQnaNoMemberID=:DirectQnaNoMemberID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':DirectQnaNoMemberID', $DirectQnaNoMemberID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$DirectQnaNoMemberAnswerRegDateTime = $Row["DirectQnaNoMemberAnswerRegDateTime"];


		$Sql = " update DirectQnaNoMembers set ";
			$Sql .= " MemberName = :MemberName, ";
			$Sql .= " DirectQnaNoMemberTitle = :DirectQnaNoMemberTitle, ";
			$Sql .= " DirectQnaNoMemberContent = :DirectQnaNoMemberContent, ";

			$Sql .= " AnswerMemberID = :AnswerMemberID, ";
			$Sql .= " AnswerMemberName = :AnswerMemberName, ";
			$Sql .= " DirectQnaNoMemberAnswer = :DirectQnaNoMemberAnswer, ";

			
			if ($DirectQnaNoMemberAnswerRegDateTime==""){
				$Sql .= " DirectQnaNoMemberAnswerRegDateTime = now(), ";
			}
			$Sql .= " DirectQnaNoMemberAnswerModiDateTime = now(), ";
			$Sql .= " DirectQnaNoMemberState = :DirectQnaNoMemberState ";

		$Sql .= " where DirectQnaNoMemberID = :DirectQnaNoMemberID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberName', $MemberName);
		$Stmt->bindParam(':DirectQnaNoMemberTitle', $DirectQnaNoMemberTitle);
		$Stmt->bindParam(':DirectQnaNoMemberContent', $DirectQnaNoMemberContent);
		$Stmt->bindParam(':AnswerMemberID', $AnswerMemberID);
		$Stmt->bindParam(':AnswerMemberName', $AnswerMemberName);
		$Stmt->bindParam(':DirectQnaNoMemberAnswer', $DirectQnaNoMemberAnswer);
		$Stmt->bindParam(':DirectQnaNoMemberState', $DirectQnaNoMemberState);
		$Stmt->bindParam(':DirectQnaNoMemberID', $DirectQnaNoMemberID);
		$Stmt->execute();
		$Stmt = null;

	}else{
		$Sql = " update DirectQnaNoMembers set ";
			
			$Sql .= " MemberName = :MemberName, ";
			$Sql .= " DirectQnaNoMemberTitle = :DirectQnaNoMemberTitle, ";
			$Sql .= " DirectQnaNoMemberContent = :DirectQnaNoMemberContent, ";
			$Sql .= " DirectQnaNoMemberModiDateTime = now(), ";
	
		$Sql .= " where DirectQnaNoMemberID = :DirectQnaNoMemberID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberName', $MemberName);
		$Stmt->bindParam(':DirectQnaNoMemberTitle', $DirectQnaNoMemberTitle);
		$Stmt->bindParam(':DirectQnaNoMemberContent', $DirectQnaNoMemberContent);
		$Stmt->bindParam(':DirectQnaNoMemberID', $DirectQnaNoMemberID);
		$Stmt->execute();
		$Stmt = null;

	}
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

