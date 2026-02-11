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
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";


$AddSqlWhere = "1=1";

if ($SearchText!=""){
	$AddSqlWhere = $AddSqlWhere . " and A.MemberName like '%".$SearchText."%'";
}

$MypageTeacherModeHTML = "";

$Sql = "SELECT A.MemberID, A.MemberName FROM Members A 
		WHERE ".$AddSqlWhere." and A.CenterID=:CenterID and A.MemberLevelId=19";


$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':CenterID', $CenterID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListCount = 1;
while($Row = $Stmt->fetch()) {

	$MemberID = $Row["MemberID"];
	$MemberName = $Row["MemberName"];
								

	$MypageTeacherModeHTML .= "<tr>";
	$MypageTeacherModeHTML .= "<td>".$ListCount."</td>";
	$MypageTeacherModeHTML .= "<td><a href=\"javascript:ChangeStudentAccount(".$MemberID.")\" class=\"mode_login_name_btn\">".$MemberName."</td>";
	$MypageTeacherModeHTML .= "</tr>";

	$ListCount ++;
}

$Stmt = null;

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MypageTeacherModeHTML"] = $MypageTeacherModeHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>