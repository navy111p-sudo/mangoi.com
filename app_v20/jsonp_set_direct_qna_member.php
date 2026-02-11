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
$DirectQnaMemberTitle = isset($_REQUEST["DirectQnaMemberTitle"]) ? $_REQUEST["DirectQnaMemberTitle"] : "";
$DirectQnaMemberContent = isset($_REQUEST["DirectQnaMemberContent"]) ? $_REQUEST["DirectQnaMemberContent"] : "";
$MyFileRealName = isset($_REQUEST["MyFileRealName"]) ? $_REQUEST["MyFileRealName"] : "";
$MyFileName = isset($_REQUEST["MyFileName"]) ? $_REQUEST["MyFileName"] : "";
//$MyFileRealName2 = isset($_REQUEST["MyFileRealName2"]) ? $_REQUEST["MyFileRealName2"] : "";
//$MyFileName2 = isset($_REQUEST["MyFileName2"]) ? $_REQUEST["MyFileName2"] : "";
//$MyFileRealName3 = isset($_REQUEST["MyFileRealName3"]) ? $_REQUEST["MyFileRealName3"] : "";
//$MyFileName3 = isset($_REQUEST["MyFileName3"]) ? $_REQUEST["MyFileName3"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";

$Sql = "select A.MemberName from Members A where MemberID=:LocalLinkMemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':LocalLinkMemberID', $LocalLinkMemberID);
$Stmt->execute();
$Row = $Stmt->fetch();
$Stmt = null;

$MemberName = $Row["MemberName"];

$Sql = "
	insert into DirectQnaMembers ( ";
$Sql .= " MemberID, ";
$Sql .= " MemberName, ";
$Sql .= " DirectQnaMemberTitle, ";
$Sql .= " DirectQnaMemberContent, ";
$Sql .= " DirectQnaMemberFileName, ";
$Sql .= " DirectQnaMemberFileRealName, ";
//$Sql .= " DirectQnaMemberFileName2, ";
//$Sql .= " DirectQnaMemberFileRealName2, ";
//$Sql .= " DirectQnaMemberFileName3, ";
//$Sql .= " DirectQnaMemberFileRealName3, ";
$Sql .= " DirectQnaMemberRegDateTime, ";
$Sql .= " DirectQnaMemberModiDateTime, ";
$Sql .= " DirectQnaMemberState ";
$Sql .= " ) values ( ";
$Sql .= " :MemberID, ";
$Sql .= " :MemberName, ";
$Sql .= " :DirectQnaMemberTitle, ";
$Sql .= " :DirectQnaMemberContent, ";
$Sql .= " :DirectQnaMemberFileName, ";
$Sql .= " :DirectQnaMemberFileRealName, ";
//$Sql .= " :DirectQnaMemberFileName2, ";
//$Sql .= " :DirectQnaMemberFileRealName2, ";
//$Sql .= " :DirectQnaMemberFileName3, ";
//$Sql .= " :DirectQnaMemberFileRealName3, ";
$Sql .= " now(), ";
$Sql .= " now(), ";
$Sql .= " 1 ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->bindParam(':MemberName', $MemberName);
$Stmt->bindParam(':DirectQnaMemberTitle', $DirectQnaMemberTitle);
$Stmt->bindParam(':DirectQnaMemberContent', $DirectQnaMemberContent);
$Stmt->bindParam(':DirectQnaMemberFileName', $MyFileName);
$Stmt->bindParam(':DirectQnaMemberFileRealName', $MyFileRealName);
//$Stmt->bindParam(':DirectQnaMemberFileName2', $MyFileName2);
//$Stmt->bindParam(':DirectQnaMemberFileRealName2', $MyFileRealName2);
//$Stmt->bindParam(':DirectQnaMemberFileName3', $MyFileName3);
//$Stmt->bindParam(':DirectQnaMemberFileRealName3', $MyFileRealName3);
$Stmt->execute();



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