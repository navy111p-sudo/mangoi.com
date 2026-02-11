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
$LocalLinkMemberID = isset($_REQUEST["LocalLinkMemberID"]) ? $_REQUEST["LocalLinkMemberID"] : "";
$ReviewClassMemberTitle = isset($_REQUEST["ReviewClassMemberTitle"]) ? $_REQUEST["ReviewClassMemberTitle"] : "";
$ReviewClassMemberContent = isset($_REQUEST["ReviewClassMemberContent"]) ? $_REQUEST["ReviewClassMemberContent"] : "";

$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


$ReviewClassMemberTitle = str_replace("{{QUT}}", "&", $ReviewClassMemberTitle);
$ReviewClassMemberTitle = str_replace("{{NL}}", "</br>", $ReviewClassMemberTitle);
$ReviewClassMemberTitle = str_replace("{{AND}}", "?", $ReviewClassMemberTitle);

$ReviewClassMemberContent = str_replace("{{QUT}}", "&", $ReviewClassMemberContent);
$ReviewClassMemberContent = str_replace("{{NL}}", "<br>", $ReviewClassMemberContent);
$ReviewClassMemberContent = str_replace("{{AND}}", "?", $ReviewClassMemberContent);

$Sql = "select A.MemberName from Members A where MemberID=:LocalLinkMemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':LocalLinkMemberID', $LocalLinkMemberID);
$Stmt->execute();
$Row = $Stmt->fetch();
$Stmt = null;

$MemberName = $Row["MemberName"];

$Sql = "
	insert into ReviewClassMembers ( ";
$Sql .= " MemberID, ";
$Sql .= " MemberName, ";
$Sql .= " ReviewClassMemberType, ";
$Sql .= " ReviewClassMemberTitle, ";
$Sql .= " ReviewClassMemberContent, ";
$Sql .= " ReviewClassMemberRegDateTime, ";
$Sql .= " ReviewClassMemberModiDateTime, ";
$Sql .= " ReviewClassMemberState ";
$Sql .= " ) values ( ";
$Sql .= " :MemberID, ";
$Sql .= " :MemberName, ";
$Sql .= " 1, ";
$Sql .= " :ReviewClassMemberTitle, ";
$Sql .= " :ReviewClassMemberContent, ";
$Sql .= " now(), ";
$Sql .= " now(), ";
$Sql .= " 1 ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->bindParam(':MemberName', $MemberName);
$Stmt->bindParam(':ReviewClassMemberTitle', $ReviewClassMemberTitle);
$Stmt->bindParam(':ReviewClassMemberContent', $ReviewClassMemberContent);
$Stmt->execute();

InsertNewTypePoint(7, 0, $LocalLinkMemberID, "");

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;



$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>