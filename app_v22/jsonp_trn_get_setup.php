<?
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
$TrnCollectByFullUrl = $Row["TrnCollectByFullUrl"];
$TrnSiteLocCode = $Row["TrnSiteLocCode"];
$TrnIndexCode = $Row["TrnIndexCode"];
$TrnIndexCodeCommon = $Row["TrnIndexCodeCommon"];
$TrnIndexCodeCommonUrl = $Row["TrnIndexCodeCommonUrl"];
$TrnTranslationMode = $Row["TrnTranslationModeApp"];//앱 설정을 가져온다.

$TrnRunType = $Row["TrnRunType"];
$TrnDefaultLanguageID = $Row["TrnDefaultLanguageID"];

$TrnCollectTextExplodeIndex = trim($TrnCollectTextExplodeIndex);
$TrnSiteLocCode = trim($TrnSiteLocCode);
$TrnIndexCode = trim($TrnIndexCode);
$TrnIndexCodeCommon = trim($TrnIndexCodeCommon);
$TrnIndexCodeCommonUrl = trim($TrnIndexCodeCommonUrl);




$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["TrnCollectDomain"] = $TrnCollectDomain;
$ArrValue["TrnCollectTextMode"] = $TrnCollectTextMode;
$ArrValue["TrnCollectTextExplodeIndex"] = $TrnCollectTextExplodeIndex;
$ArrValue["TrnCollectByFullUrl"] = $TrnCollectByFullUrl;
$ArrValue["TrnSiteLocCode"] = $TrnSiteLocCode;
$ArrValue["TrnIndexCode"] = $TrnIndexCode;
$ArrValue["TrnIndexCodeCommon"] = $TrnIndexCodeCommon;
$ArrValue["TrnIndexCodeCommonUrl"] = $TrnIndexCodeCommonUrl;
$ArrValue["TrnTranslationMode"] = $TrnTranslationMode;
$ArrValue["TrnRunType"] = $TrnRunType;
$ArrValue["TrnDefaultLanguageID"] = $TrnDefaultLanguageID;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>