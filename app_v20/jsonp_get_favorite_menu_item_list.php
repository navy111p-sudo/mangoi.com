<?
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ErrNum = 0;
$ErrMsg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$AppVersionID = isset($_REQUEST["AppVersionID"]) ? $_REQUEST["AppVersionID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;

$FavoriteMenuItemListHTML = "";

$ArrBackgroundImages = array(
	0=> "images/bubble_red.png",
	1=> "images/bubble_orange.png",
	2=> "images/bubble_green.png",
	3=> "images/bubble_blue.png",
	4=> "images/bubble_indigo.png",
	5=> "images/bubble_yellow.png",
	6=> "images/bubble_purple.png",
	7=> "images/bubble_red.png"
);

$Sql = "
	select
		*
	from FavoriteMenuItemLists A
	where
		A.FavoriteMenuItemListState=1
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListNumber = 0;
while($Row = $Stmt->fetch() ) {
	$FavoriteMenuItemListName = $Row["FavoriteMenuItemListName"];
	$FavoriteMenuItemListPath = $Row["FavoriteMenuItemListPath"];
	$FavoriteMenuItemListType = $Row["FavoriteMenuItemListType"];


	$FavoriteMenuItemListHTML .= "<div class=\"socialCircle-item\" style=\"background-image:url(".$ArrBackgroundImages[$ListNumber].")\" onclick=\"LocatePathItem(".$FavoriteMenuItemListPath.", ".$FavoriteMenuItemListType.")\">".$FavoriteMenuItemListName."</div>";

	$ListNumber++;
}

$FavoriteMenuItemListHTML .= "<div class=\"socialCircle-center closed close-popup\" id=\"DivFavoriteMenuItem\" style=\"position: static; margin:0 auto;\"></div>";

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["FavoriteMenuItemListHTML"] = $FavoriteMenuItemListHTML;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>