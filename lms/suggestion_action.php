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

$SuggestionID = isset($_REQUEST["SuggestionID"]) ? $_REQUEST["SuggestionID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
$SuggestionTitle = isset($_REQUEST["SuggestionTitle"]) ? $_REQUEST["SuggestionTitle"] : "";
$SuggestionContent = isset($_REQUEST["SuggestionContent"]) ? $_REQUEST["SuggestionContent"] : "";

$AnswerMemberID = isset($_REQUEST["AnswerMemberID"]) ? $_REQUEST["AnswerMemberID"] : "";
$AnswerMemberName = isset($_REQUEST["AnswerMemberName"]) ? $_REQUEST["AnswerMemberName"] : "";
$SuggestionAnswer = isset($_REQUEST["SuggestionAnswer"]) ? $_REQUEST["SuggestionAnswer"] : "";

$SuggestionState = isset($_REQUEST["SuggestionState"]) ? $_REQUEST["SuggestionState"] : "";


if ($SuggestionID==""){

	$Sql = " insert into Suggestions ( ";
		$Sql .= " MemberID, ";
		$Sql .= " MemberName, ";
		$Sql .= " SuggestionTitle, ";
		$Sql .= " SuggestionContent, ";
		$Sql .= " SuggestionRegDateTime, ";
		$Sql .= " SuggestionModiDateTime, ";
		$Sql .= " SuggestionState ";
	$Sql .= " ) values ( ";
		$Sql .= " :MemberID, ";
		$Sql .= " :MemberName, ";
		$Sql .= " :SuggestionTitle, ";
		$Sql .= " :SuggestionContent, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :SuggestionState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':MemberName', $MemberName);
	$Stmt->bindParam(':SuggestionTitle', $SuggestionTitle);
	$Stmt->bindParam(':SuggestionContent', $SuggestionContent);
	$Stmt->bindParam(':SuggestionState', $SuggestionState);
	$Stmt->execute();
	$Stmt = null;

}else{

	

	if ($_LINK_ADMIN_LEVEL_ID_<=4){//프랜차이즈 관리자가 쓴다면 답변

		
		$Sql = "
				select 
						A.*
				from Suggestions A 
				where A.SuggestionID=:SuggestionID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':SuggestionID', $SuggestionID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$SuggestionAnswerRegDateTime = $Row["SuggestionAnswerRegDateTime"];		
		$QuestionMemberID = $Row["MemberID"];	
		
		$Sql = " update Suggestions set ";
			$Sql .= " MemberName = :MemberName, ";
			$Sql .= " SuggestionTitle = :SuggestionTitle, ";
			$Sql .= " SuggestionContent = :SuggestionContent, ";

			$Sql .= " AnswerMemberID = :AnswerMemberID, ";
			$Sql .= " AnswerMemberName = :AnswerMemberName, ";
			$Sql .= " SuggestionAnswer = :SuggestionAnswer, ";

			if ($SuggestionAnswerRegDateTime==""){
				$Sql .= " SuggestionAnswerRegDateTime = now(), ";
			}
			$Sql .= " SuggestionAnswerModiDateTime = now(), ";
			$Sql .= " SuggestionState = :SuggestionState ";
		$Sql .= " where SuggestionID = :SuggestionID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberName', $MemberName);
		$Stmt->bindParam(':SuggestionTitle', $SuggestionTitle);
		$Stmt->bindParam(':SuggestionContent', $SuggestionContent);
		$Stmt->bindParam(':AnswerMemberID', $AnswerMemberID);
		$Stmt->bindParam(':AnswerMemberName', $AnswerMemberName);
		$Stmt->bindParam(':SuggestionAnswer', $SuggestionAnswer);
		$Stmt->bindParam(':SuggestionState', $SuggestionState);
		$Stmt->bindParam(':SuggestionID', $SuggestionID);
		$Stmt->execute();
		$Stmt = null;

		/*
		if ($SuggestionState=="2"){
			

			$Sql = "select DeviceToken, DeviceType from DeviceTokens where MemberID=:MemberID order by ModiDateTime desc limit 0,1";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':MemberID', $QuestionMemberID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;
			$DeviceToken = $Row["DeviceToken"];
			$DeviceType = $Row["DeviceType"];
			
			if ($DeviceToken){
				
				$SendTitle = "건의사항에 대한 답변이 등록되었습니다.";
				$SendMessage = "건의사항에 대한 답변이 등록되었습니다.";
				$SendMemo = "";

				//send_notification ($DeviceToken, $SendTitle, $SendMessage);

				$Sql = " insert into SendMessageLogs ( ";
					$Sql .= " SendMemberID, ";
					$Sql .= " MemberID, ";
					$Sql .= " DeviceType, ";
					$Sql .= " DeviceToken, ";
					$Sql .= " SendTitle, ";
					$Sql .= " SendMessage, ";
					$Sql .= " SendMemo, ";
					$Sql .= " SendMessageLogRegDateTime ";
				$Sql .= " ) values ( ";
					$Sql .= " :SendMemberID, ";
					$Sql .= " :MemberID, ";
					$Sql .= " :DeviceType, ";
					$Sql .= " :DeviceToken, ";
					$Sql .= " :SendTitle, ";
					$Sql .= " :SendMessage, ";
					$Sql .= " :SendMemo, ";
					$Sql .= " now() ";
				$Sql .= " ) ";

				$Stmt = $DbConn->prepare($Sql);
				$Stmt->bindParam(':SendMemberID', $AnswerMemberID);
				$Stmt->bindParam(':MemberID', $QuestionMemberID);
				$Stmt->bindParam(':DeviceType', $DeviceType);
				$Stmt->bindParam(':DeviceToken', $DeviceToken);
				$Stmt->bindParam(':SendTitle', $SendTitle);
				$Stmt->bindParam(':SendMessage', $SendMessage);
				$Stmt->bindParam(':SendMemo', $SendMemo);
				$Stmt->execute();
				$Stmt = null;

				
			}
		}
		*/



	}else{
		$Sql = " update Suggestions set ";
			
			$Sql .= " MemberName = :MemberName, ";
			$Sql .= " SuggestionTitle = :SuggestionTitle, ";
			$Sql .= " SuggestionContent = :SuggestionContent, ";
			$Sql .= " SuggestionModiDateTime = now(), ";
	
		$Sql .= " where SuggestionID = :SuggestionID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberName', $MemberName);
		$Stmt->bindParam(':SuggestionTitle', $SuggestionTitle);
		$Stmt->bindParam(':SuggestionContent', $SuggestionContent);
		$Stmt->bindParam(':SuggestionID', $SuggestionID);
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

