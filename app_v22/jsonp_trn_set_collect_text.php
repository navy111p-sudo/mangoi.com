<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$TrnCollectUrlDviceType = isset($_REQUEST["TrnCollectUrlDviceType"]) ? $_REQUEST["TrnCollectUrlDviceType"] : "";
$TrnCollectUrl = isset($_REQUEST["TrnCollectUrl"]) ? $_REQUEST["TrnCollectUrl"] : "";
$TrnCollectTextExplodeIndex = isset($_REQUEST["TrnCollectTextExplodeIndex"]) ? $_REQUEST["TrnCollectTextExplodeIndex"] : "";
$TrnCollectTextType = isset($_REQUEST["TrnCollectTextType"]) ? $_REQUEST["TrnCollectTextType"] : "";
$TrnCollectTexts = isset($_REQUEST["TrnCollectTexts"]) ? $_REQUEST["TrnCollectTexts"] : "";

$TrnCollectUrl = trim($TrnCollectUrl);
$TrnCollectTextExplodeIndex = trim($TrnCollectTextExplodeIndex);

$Sql = "select 
			A.TrnCollectUrlID
		from TrnCollectUrls A
		where 
			A.TrnCollectUrlDviceType=:TrnCollectUrlDviceType
			and A.TrnCollectUrl=:TrnCollectUrl
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TrnCollectUrlDviceType', $TrnCollectUrlDviceType);
$Stmt->bindParam(':TrnCollectUrl', $TrnCollectUrl);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TrnCollectUrlID = $Row["TrnCollectUrlID"];

if (!$TrnCollectUrlID){

	$Sql = "select ifnull(Max(TrnCollectUrlOrder),0) as TrnCollectUrlOrder from TrnCollectUrls";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TrnCollectUrlOrder = $Row["TrnCollectUrlOrder"]+1;

	$Sql = " insert into TrnCollectUrls ( ";
		$Sql .= " TrnCollectUrlDviceType, ";
		$Sql .= " TrnCollectUrl, ";
		$Sql .= " TrnCollectUrlName, ";
		$Sql .= " TrnCollectUrlRegDateTime, ";
		$Sql .= " TrnCollectUrlModiDateTime, ";
		$Sql .= " TrnCollectUrlState, ";
		$Sql .= " TrnCollectUrlOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :TrnCollectUrlDviceType, ";
		$Sql .= " :TrnCollectUrl, ";
		$Sql .= " :TrnCollectUrlName, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " 1, ";
		$Sql .= " :TrnCollectUrlOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TrnCollectUrlDviceType', $TrnCollectUrlDviceType);
	$Stmt->bindParam(':TrnCollectUrl', $TrnCollectUrl);
	$Stmt->bindParam(':TrnCollectUrlName', $TrnCollectUrl);
	$Stmt->bindParam(':TrnCollectUrlOrder', $TrnCollectUrlOrder);
	$Stmt->execute();
	$TrnCollectUrlID = $DbConn->lastInsertId();
	$Stmt = null;

}


/*
$Sql = "update TrnCollectTexts set TrnCollectTextState=0, TrnCollectTextRegModiDateTime=now() where TrnCollectTextType=:TrnCollectTextType and TrnCollectUrlID=:TrnCollectUrlID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TrnCollectTextType', $TrnCollectTextType);
$Stmt->bindParam(':TrnCollectUrlID', $TrnCollectUrlID);
$Stmt->execute();
*/

$ArrTrnCollectText = explode($TrnCollectTextExplodeIndex, $TrnCollectTexts);
for ($ii=1;$ii<=count($ArrTrnCollectText)-2;$ii++){
	$TrnCollectText = $ArrTrnCollectText[$ii];

	$Sql = "select 
				A.TrnCollectTextID
			from TrnCollectTexts A
			where 
				A.TrnCollectTextType=:TrnCollectTextType 
				and A.TrnCollectUrlID=:TrnCollectUrlID 
				and A.TrnCollectText=:TrnCollectText 
			";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TrnCollectTextType', $TrnCollectTextType);
	$Stmt->bindParam(':TrnCollectUrlID', $TrnCollectUrlID);
	$Stmt->bindParam(':TrnCollectText', $TrnCollectText);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TrnCollectTextID = $Row["TrnCollectTextID"];

	if ($TrnCollectTextID){

		$Sql = "update TrnCollectTexts set TrnCollectTextState=1, TrnCollectTextRegModiDateTime=now() where TrnCollectTextID=:TrnCollectTextID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':TrnCollectTextID', $TrnCollectTextID);
		$Stmt->execute();

	}else{
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
			$Sql .= " TrnCollectTextState, ";
			$Sql .= " TrnCollectTextOrder ";
		$Sql .= " ) values ( ";
			$Sql .= " :TrnCollectTextType, ";
			$Sql .= " :TrnCollectUrlID, ";
			$Sql .= " :TrnCollectText, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " 1, ";
			$Sql .= " :TrnCollectTextOrder ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':TrnCollectTextType', $TrnCollectTextType);
		$Stmt->bindParam(':TrnCollectUrlID', $TrnCollectUrlID);
		$Stmt->bindParam(':TrnCollectText', $TrnCollectText);
		$Stmt->bindParam(':TrnCollectTextOrder', $TrnCollectTextOrder);
		$Stmt->execute();
		$TrnCollectTextID = $DbConn->lastInsertId();
		$Stmt = null;

	}

}

include_once('../includes/dbclose.php');
?>