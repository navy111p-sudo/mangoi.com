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

$RemoteSupportMemberID = isset($_REQUEST["RemoteSupportMemberID"]) ? $_REQUEST["RemoteSupportMemberID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
$RemoteSupportMemberTitle = isset($_REQUEST["RemoteSupportMemberTitle"]) ? $_REQUEST["RemoteSupportMemberTitle"] : "";
$RemoteSupportMemberContent = isset($_REQUEST["RemoteSupportMemberContent"]) ? $_REQUEST["RemoteSupportMemberContent"] : "";

$AnswerMemberID = isset($_REQUEST["AnswerMemberID"]) ? $_REQUEST["AnswerMemberID"] : "";
$AnswerMemberName = isset($_REQUEST["AnswerMemberName"]) ? $_REQUEST["AnswerMemberName"] : "";
$RemoteSupportMemberAnswer = isset($_REQUEST["RemoteSupportMemberAnswer"]) ? $_REQUEST["RemoteSupportMemberAnswer"] : "";

$RemoteSupportMemberState = isset($_REQUEST["RemoteSupportMemberState"]) ? $_REQUEST["RemoteSupportMemberState"] : "";


if ($RemoteSupportMemberID==""){

	$Sql = " insert into RemoteSupportMembers ( ";
		$Sql .= " MemberID, ";
		$Sql .= " MemberName, ";
		$Sql .= " RemoteSupportMemberTitle, ";
		$Sql .= " RemoteSupportMemberContent, ";
		$Sql .= " RemoteSupportMemberRegDateTime, ";
		$Sql .= " RemoteSupportMemberModiDateTime, ";
		$Sql .= " RemoteSupportMemberState ";
	$Sql .= " ) values ( ";
		$Sql .= " :MemberID, ";
		$Sql .= " :MemberName, ";
		$Sql .= " :RemoteSupportMemberTitle, ";
		$Sql .= " :RemoteSupportMemberContent, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :RemoteSupportMemberState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':MemberName', $MemberName);
	$Stmt->bindParam(':RemoteSupportMemberTitle', $RemoteSupportMemberTitle);
	$Stmt->bindParam(':RemoteSupportMemberContent', $RemoteSupportMemberContent);
	$Stmt->bindParam(':RemoteSupportMemberState', $RemoteSupportMemberState);
	$Stmt->execute();
	$Stmt = null;

}else{

	

	if ($_LINK_ADMIN_LEVEL_ID_<=4){//프랜차이즈 관리자가 쓴다면 답변

		
		$Sql = "
				select 
						A.*
				from RemoteSupportMembers A 
				where A.RemoteSupportMemberID=:RemoteSupportMemberID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':RemoteSupportMemberID', $RemoteSupportMemberID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		
		
		$Sql = " update RemoteSupportMembers set ";
			$Sql .= " MemberName = :MemberName, ";
			$Sql .= " RemoteSupportMemberTitle = :RemoteSupportMemberTitle, ";
			$Sql .= " RemoteSupportMemberContent = :RemoteSupportMemberContent, ";

			$Sql .= " RemoteSupportMemberState = :RemoteSupportMemberState ";
		$Sql .= " where RemoteSupportMemberID = :RemoteSupportMemberID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberName', $MemberName);
		$Stmt->bindParam(':RemoteSupportMemberTitle', $RemoteSupportMemberTitle);
		$Stmt->bindParam(':RemoteSupportMemberContent', $RemoteSupportMemberContent);
		$Stmt->bindParam(':RemoteSupportMemberState', $RemoteSupportMemberState);
		$Stmt->bindParam(':RemoteSupportMemberID', $RemoteSupportMemberID);
		$Stmt->execute();
		$Stmt = null;

	}else{
		$Sql = " update RemoteSupportMembers set ";
			
			$Sql .= " MemberName = :MemberName, ";
			$Sql .= " RemoteSupportMemberTitle = :RemoteSupportMemberTitle, ";
			$Sql .= " RemoteSupportMemberContent = :RemoteSupportMemberContent, ";
			$Sql .= " RemoteSupportMemberModiDateTime = now(), ";
	
		$Sql .= " where RemoteSupportMemberID = :RemoteSupportMemberID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberName', $MemberName);
		$Stmt->bindParam(':RemoteSupportMemberTitle', $RemoteSupportMemberTitle);
		$Stmt->bindParam(':RemoteSupportMemberContent', $RemoteSupportMemberContent);
		$Stmt->bindParam(':RemoteSupportMemberID', $RemoteSupportMemberID);
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

