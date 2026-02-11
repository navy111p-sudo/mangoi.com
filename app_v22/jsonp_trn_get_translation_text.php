<?php
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ErrNum = 0;
$ErrMsg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";



$TrnCollectTextType = isset($_REQUEST["TrnCollectTextType"]) ? $_REQUEST["TrnCollectTextType"] : "";
$TrnCollectUrlDviceType = isset($_REQUEST["TrnCollectUrlDviceType"]) ? $_REQUEST["TrnCollectUrlDviceType"] : "";
$TrnBrowserLocCode = isset($_REQUEST["TrnBrowserLocCode"]) ? $_REQUEST["TrnBrowserLocCode"] : "";
$TrnCollectUrl = isset($_REQUEST["TrnCollectUrl"]) ? $_REQUEST["TrnCollectUrl"] : "";
$ObjLang = null;

$TrnBrowserLocCode = trim($TrnBrowserLocCode);
$TrnCollectUrl = trim($TrnCollectUrl);

$Sql = "select 
			A.*
		from TrnSetup A where A.TrnSetupID=1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TrnCollectDomain = $Row["TrnCollectDomain"];
$TrnCollectTextMode = $Row["TrnCollectTextMode"];
$TrnCollectTextExplodeIndex = $Row["TrnCollectTextExplodeIndex"];
$TrnSiteLocCode = $Row["TrnSiteLocCode"];
$TrnIndexCode = $Row["TrnIndexCode"];
$TrnIndexCodeCommon = $Row["TrnIndexCodeCommon"];
$TrnIndexCodeCommonUrl = $Row["TrnIndexCodeCommonUrl"];
$TrnTranslationMode = $Row["TrnTranslationMode"];
$TrnRunType = $Row["TrnRunType"];
$TrnDefaultLanguageID = $Row["TrnDefaultLanguageID"];

$TrnCollectTextExplodeIndex = trim($TrnCollectTextExplodeIndex);
$TrnSiteLocCode = trim($TrnSiteLocCode);
$TrnIndexCode = trim($TrnIndexCode);
$TrnIndexCodeCommon = trim($TrnIndexCodeCommon);
$TrnIndexCodeCommonUrl = trim($TrnIndexCodeCommonUrl);


if ($TrnRunType==1){

	$Sql = "select 
					A.*
			from TrnBrowserLocCodes A 
			where A.TrnBrowserLocCodeState=1 order by A.TrnBrowserLocCodeOrder asc ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	$TrnBrowserLocCodeID = 0;
	$TrnLanguageID = 0;
	while($Row = $Stmt->fetch()) {
		
		$Db_TrnLanguageID = $Row["TrnLanguageID"];
		$Db_TrnBrowserLocCode = trim($Row["TrnBrowserLocCode"]);
		$IncDb_TrnBrowserLocCode = str_replace("%", "", $Db_TrnBrowserLocCode);
		$IncDbLen_TrnBrowserLocCode = strlen($IncDb_TrnBrowserLocCode);

		
		if ($TrnLanguageID==0){

			if (substr($Db_TrnBrowserLocCode,0,1)=="%" && substr($Db_TrnBrowserLocCode,-1)=="%"){
				if (strpos($TrnBrowserLocCode, $IncDb_TrnBrowserLocCode) !== false) {
					$TrnLanguageID = $Db_TrnLanguageID;
				}
			}else if (substr($Db_TrnBrowserLocCode,0,1)=="%"){
				if (substr($TrnBrowserLocCode,($IncDbLen_TrnBrowserLocCode*-1)) == $IncDb_TrnBrowserLocCode) {
					$TrnLanguageID = $Db_TrnLanguageID;
				}
			}else if (substr($Db_TrnBrowserLocCode,-1)=="%"){
				if (substr($TrnBrowserLocCode,0,$IncDbLen_TrnBrowserLocCode) == $IncDb_TrnBrowserLocCode) {
					$TrnLanguageID = $Db_TrnLanguageID;
				}
			}else{
				if ($TrnBrowserLocCode == $Db_TrnBrowserLocCode) {
					$TrnLanguageID = $Db_TrnLanguageID;
				}
			}

		}

	}
	$Stmt = null;

}else{
	$TrnLanguageID = $TrnBrowserLocCode;
}

if ($TrnLanguageID==0 && $TrnDefaultLanguageID!=0){
	$TrnLanguageID = $TrnDefaultLanguageID;
}

if ($TrnLanguageID!=0){

	if ($TrnCollectTextType=="1"){//페이지 부분 =====================================================

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


		$Sql = "select 
						A.*
				from TrnCollectTexts A 
				where A.TrnCollectUrlID=:TrnCollectUrlID and A.TrnCollectTextState=1 and A.TrnCollectTextType=1 order by A.TrnCollectTextOrder asc ";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':TrnCollectUrlID', $TrnCollectUrlID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	}else{//공통부분 =====================================================

		$Sql = "select 
						A.*
				from TrnCollectTexts A 
					inner join TrnCollectUrls B on A.TrnCollectUrlID=B.TrnCollectUrlID 
				where B.TrnCollectUrlDviceType=:TrnCollectUrlDviceType and A.TrnCollectTextState=1 and A.TrnCollectTextType=2 order by A.TrnCollectTextOrder asc ";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':TrnCollectUrlDviceType', $TrnCollectUrlDviceType);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	}


	while($Row = $Stmt->fetch()) {

		$TrnCollectTextID = $Row["TrnCollectTextID"];
		$TrnCollectText = $Row["TrnCollectText"];
		
		$Sql2 = "select 
					A.TrnTranslationTextID,
					A.TrnTranslationText
				from TrnTranslationTexts A
				where 
					A.TrnLanguageID=:TrnLanguageID
					and A.TrnCollectTextID=:TrnCollectTextID
				";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':TrnLanguageID', $TrnLanguageID);
		$Stmt2->bindParam(':TrnCollectTextID', $TrnCollectTextID);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		$Row2 = $Stmt2->fetch();
		$Stmt2 = null;
		$TrnTranslationTextID = $Row2["TrnTranslationTextID"];
		$TrnTranslationText = $Row2["TrnTranslationText"];
		if ($TrnTranslationText!=""){
			$ObjLang[$TrnCollectText] = $TrnTranslationText;
		}else{

			if ($TrnDefaultLanguageID!=0){

				$Sql3 = "select 
							A.TrnTranslationTextID,
							A.TrnTranslationText
						from TrnTranslationTexts A
						where 
							A.TrnLanguageID=:TrnDefaultLanguageID
							and A.TrnCollectTextID=:TrnCollectTextID
						";
				$Stmt3 = $DbConn->prepare($Sql3);
				$Stmt3->bindParam(':TrnDefaultLanguageID', $TrnDefaultLanguageID);
				$Stmt3->bindParam(':TrnCollectTextID', $TrnCollectTextID);
				$Stmt3->execute();
				$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
				$Row3 = $Stmt3->fetch();
				$Stmt3 = null;
				$TrnTranslationTextID = $Row3["TrnTranslationTextID"];
				$TrnTranslationText = $Row3["TrnTranslationText"];

				if ($TrnTranslationText!=""){
					$ObjLang[$TrnCollectText] = $TrnTranslationText;
				}

			}
		}

	}
	$Stmt = null;


}


$ArrValue["obj_lang"] = $ObjLang;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>