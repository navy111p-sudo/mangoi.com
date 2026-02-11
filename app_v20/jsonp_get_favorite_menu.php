<?
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');


$ErrNum = 0;
$ErrMsg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$LocalLinkMemberID = isset($_REQUEST["LocalLinkMemberID"]) ? $_REQUEST["LocalLinkMemberID"] : "";

$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$MemberID = $LocalLinkMemberID;
$ArrFavoriteMenuHTML = array();


$Sql = "
	select 
		*
	from FavoriteMenus A 
		inner join FavoriteMenuLists B on A.FavoriteMenuListID=B.FavoriteMenuListID
	where
		A.MemberID=:MemberID
		and
		A.FavoriteMenuState=1
	order by
		A.FavoriteMenuOrder asc
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


while($Row = $Stmt->fetch() ) {
	$FavoriteMenuOrder = $Row["FavoriteMenuOrder"];
	$FavoriteMenuListName = $Row["FavoriteMenuListName"];
	$FavoriteMenuListType = $Row["FavoriteMenuListType"];
	$FavoriteMenuListPath = $Row["FavoriteMenuListPath"];

	//$ArrFavoriteMenuHTML[$FavoriteMenuOrder] = "<li><a href=\"#\" onclick=\"LocatePath(".$FavoriteMenuListPath.", '".$FavoriteMenuListType."')\" class=\"popup_menu_btn_2\">".$FavoriteMenuListName."</a><img src=\"images/btn_favorite_add.png\" class=\"btn_favorite_add\" onclick=\"OpenFavoriteMenuList(".$FavoriteMenuOrder.", ".$MemberID.")\" ></li>";
	$ArrFavoriteMenuHTML[$FavoriteMenuOrder] = "<li><a href=\"#\" onclick=\"LocatePath(".$FavoriteMenuListPath.", '".$FavoriteMenuListType."')\" class=\"popup_menu_btn_2\">".$FavoriteMenuListName."<img src=\"images/btn_favorite_add.png\" class=\"btn_favorite_add\" onclick=\"OpenFavoriteMenuList(".$FavoriteMenuOrder.", ".$MemberID.")\" ></a></li>";
}

for($i=0;$i<8; $i++) {
	if(isset($ArrFavoriteMenuHTML[$i]) == false) {
		$ArrFavoriteMenuHTML[$i] = "<li><a href=\"#\" class=\"popup_menu_btn_2\"><img src=\"images/btn_favorite_add.png\" class=\"btn_favorite_add\" onclick=\"OpenFavoriteMenuList(".$i.", ".$MemberID.")\" ></a></li>";
	}
}

ksort($ArrFavoriteMenuHTML, 1);
$FavoriteMenuHTML = implode(" ", $ArrFavoriteMenuHTML);

//var_dump($ArrFavoriteMenuHTML);
//var_dump($ArrFavoriteMenuHTML);
//$FavoriteMenuHTML .= $CheckValue;


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["FavoriteMenuHTML"] = $FavoriteMenuHTML;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>