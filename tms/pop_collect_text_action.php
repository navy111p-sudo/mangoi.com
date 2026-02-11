 <?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$TrnCollectUrlID = isset($_REQUEST["TrnCollectUrlID"]) ? $_REQUEST["TrnCollectUrlID"] : "";
$TrnCollectTextID = isset($_REQUEST["TrnCollectTextID"]) ? $_REQUEST["TrnCollectTextID"] : "";
$TrnCollectUrlDviceType = isset($_REQUEST["TrnCollectUrlDviceType"]) ? $_REQUEST["TrnCollectUrlDviceType"] : "";
$TrnCollectText = isset($_REQUEST["TrnCollectText"]) ? $_REQUEST["TrnCollectText"] : "";
$TrnCollectTextState = isset($_REQUEST["TrnCollectTextState"]) ? $_REQUEST["TrnCollectTextState"] : "";
$ReqTrnCollectTextID = $TrnCollectTextID;

$TrnCollectText = trim($TrnCollectText);

if ($TrnCollectTextID=="0"){

	$Sql = "select ifnull(Max(TrnCollectTextOrder),0) as TrnCollectTextOrder from TrnCollectTexts";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TrnCollectTextOrder = $Row["TrnCollectTextOrder"]+1;

	$Sql = " insert into TrnCollectTexts ( ";
		$Sql .= " TrnCollectTextType, ";
		$Sql .= " TrnCollectUrlID, ";
		$Sql .= " TrnCollectText, ";
		$Sql .= " TrnCollectTextRegDateTime, ";
		$Sql .= " TrnCollectTextRegModiDateTime, ";
		$Sql .= " TrnCollectTextOrder, ";
		$Sql .= " TrnCollectTextState ";
	$Sql .= " ) values ( ";
		$Sql .= " 1, ";
		$Sql .= " :TrnCollectUrlID, ";
		$Sql .= " :TrnCollectText, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :TrnCollectTextOrder, ";
		$Sql .= " 1 ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TrnCollectUrlID', $TrnCollectUrlID);
	$Stmt->bindParam(':TrnCollectText', $TrnCollectText);
	$Stmt->bindParam(':TrnCollectTextOrder', $TrnCollectTextOrder);

	$Stmt->execute();
	$TrnCollectTextID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	if ($TrnCollectUrlID=="0" || $TrnCollectUrlID=="1"){//경고문구 일때만 수정한다.

		$Sql = " update TrnCollectTexts set ";
			$Sql .= " TrnCollectText = :TrnCollectText, ";
			$Sql .= " TrnCollectTextState = :TrnCollectTextState, ";
			$Sql .= " TrnCollectTextRegModiDateTime = now() ";
		$Sql .= " where TrnCollectTextID = :TrnCollectTextID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':TrnCollectText', $TrnCollectText);
		$Stmt->bindParam(':TrnCollectTextState', $TrnCollectTextState);
		$Stmt->bindParam(':TrnCollectTextID', $TrnCollectTextID);
		$Stmt->execute();
		$Stmt = null;

	}
}



$Sql = "select 
				A.*
		from TrnLanguages A 
		where A.TrnLanguageState=1 order by A.TrnLanguageOrder desc";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
while($Row = $Stmt->fetch()) {
	$TrnLanguageID = $Row["TrnLanguageID"];
	$TrnLanguageName = $Row["TrnLanguageName"];

	$Sql2 = "select 
		A.*
		from TrnTranslationTexts A 
		where A.TrnLanguageID=:TrnLanguageID and A.TrnCollectTextID=:TrnCollectTextID";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':TrnLanguageID', $TrnLanguageID);
	$Stmt2->bindParam(':TrnCollectTextID', $TrnCollectTextID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	$Stmt2 = null;
	$TrnTranslationTextID = $Row2["TrnTranslationTextID"];

	$TrnTranslationText = isset($_REQUEST["TrnTranslationText_".$ReqTrnCollectTextID."_".$TrnLanguageID]) ? $_REQUEST["TrnTranslationText_".$ReqTrnCollectTextID."_".$TrnLanguageID] : "";


	if (!$TrnTranslationTextID){

		$Sql3 = "select ifnull(Max(TrnTranslationTextOrder),0) as TrnTranslationTextOrder from TrnTranslationTexts";
		$Stmt3 = $DbConn->prepare($Sql3);
		$Stmt3->execute();
		$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
		$Row3 = $Stmt3->fetch();
		$Stmt3 = null;
		$TrnTranslationTextOrder = $Row3["TrnTranslationTextOrder"]+1;

		$Sql3 = " insert into TrnTranslationTexts ( ";
			$Sql3 .= " TrnLanguageID, ";
			$Sql3 .= " TrnCollectTextID, ";
			$Sql3 .= " TrnTranslationText, ";
			$Sql3 .= " TrnTranslationTextRegDateTime, ";
			$Sql3 .= " TrnTranslationTextModiDateTime, ";
			$Sql3 .= " TrnTranslationTextOrder, ";
			$Sql3 .= " TrnTranslationTextState ";
		$Sql3 .= " ) values ( ";
			$Sql3 .= " :TrnLanguageID, ";
			$Sql3 .= " :TrnCollectTextID, ";
			$Sql3 .= " :TrnTranslationText, ";
			$Sql3 .= " now(), ";
			$Sql3 .= " now(), ";
			$Sql3 .= " :TrnTranslationTextOrder, ";
			$Sql3 .= " 1 ";
		$Sql3 .= " ) ";

		$Stmt3 = $DbConn->prepare($Sql3);
		$Stmt3->bindParam(':TrnLanguageID', $TrnLanguageID);
		$Stmt3->bindParam(':TrnCollectTextID', $TrnCollectTextID);
		$Stmt3->bindParam(':TrnTranslationText', $TrnTranslationText);
		$Stmt3->bindParam(':TrnTranslationTextOrder', $TrnTranslationTextOrder);

		$Stmt3->execute();
		$TrnTranslationTextID = $DbConn->lastInsertId();
		$Stmt3 = null;


	}else{

		$Sql3 = " update TrnTranslationTexts set ";
			$Sql3 .= " TrnTranslationText = :TrnTranslationText, ";
			$Sql3 .= " TrnTranslationTextModiDateTime = now() ";
		$Sql3 .= " where TrnTranslationTextID = :TrnTranslationTextID ";

		$Stmt3 = $DbConn->prepare($Sql3);
		$Stmt3->bindParam(':TrnTranslationText', $TrnTranslationText);
		$Stmt3->bindParam(':TrnTranslationTextID', $TrnTranslationTextID);
		$Stmt3->execute();
		$Stmt3 = null;

	}



}
$Stmt = null;




include_once('./inc_header.php');
?>
</head>
<body>
<script>
//parent.$.fn.colorbox.close();
parent.location.reload();
</script>
<?php
include_once('./inc_footer.php'); 
?>
</body>
</html>
<?
include_once('../includes/dbclose.php');
?>


