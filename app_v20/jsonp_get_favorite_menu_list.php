<?
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ErrNum = 0;
$ErrMsg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$LocalLinkMemberID = isset($_REQUEST["LocalLinkMemberID"]) ? $_REQUEST["LocalLinkMemberID"] : "";
$FavoriteMenuOrder = isset($_REQUEST["FavoriteMenuOrder"]) ? $_REQUEST["FavoriteMenuOrder"] : "";
$FavoriteMenuListID = isset($_REQUEST["FavoriteMenuListID"]) ? $_REQUEST["FavoriteMenuListID"] : "";

$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$MemberID = $LocalLinkMemberID;

$ArrBgColor = array(
	"background-color:#fdb15c;",
	"background-color:#975add;",
	"background-color:#f88e6e;",
	"background-color:#b952be;",
	"background-color:#f15d85;",
	"background-color:#d74ba5;",
	"background-color:#ee488f;",
	"background-color:#f3667f;",

	"background-color:#fdb15c;",
	"background-color:#975add;",
	"background-color:#f88e6e;",
	"background-color:#b952be;",
	"background-color:#f15d85;",
	"background-color:#d74ba5;",
	"background-color:#ee488f;",
	"background-color:#f3667f;",

	"background-color:#fdb15c;",
	"background-color:#975add;",
	"background-color:#f88e6e;",
	"background-color:#b952be;",
	"background-color:#f15d85;",
	"background-color:#d74ba5;",
	"background-color:#ee488f;",
	"background-color:#f3667f;"
);
$FavoriteMenuListHTML = "";



$Sql = "
		select 
				A.*
		from Members A 
		where A.MemberID=:MemberID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberLevelID = $Row["MemberLevelID"];



$AddSqlWhere = " 1=1 ";
$AddSqlWhere .= " and A.FavoriteMenuListState=1 ";

if ($MemberLevelID<=15){
	$AddSqlWhere .= " ";
}else{
	$AddSqlWhere .= " and A.FavoriteMenuListAdmin<>1 ";
}


$Sql = "
	select
		*
	from FavoriteMenuLists A
	where ".$AddSqlWhere." order by FavoriteMenuListOrder asc 
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListNumber = 0;
while($Row = $Stmt->fetch() ) {

	//$FavoriteMenuListHTML .= "<li><a href=\"#\" class=\"popup_menu_btn\" style=".$ArrBgColor[$ListNumber].">".$Row["FavoriteMenuListName"]."</a></li>";
	$FavoriteMenuListHTML .= "<li><a href=\"javascript:ChangeFavoriteMenu(".$Row["FavoriteMenuListID"].", ".$MemberID.");\" class=\"popup_menu_btn\" style=".$ArrBgColor[$ListNumber]." >".$Row["FavoriteMenuListName"]."</a></li>";
	$ListNumber++;
}


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["FavoriteMenuListHTML"] = $FavoriteMenuListHTML;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>