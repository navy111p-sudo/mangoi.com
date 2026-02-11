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
$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";



$Sql = "
		select 
				A.*
		from Books A 
		where A.BookID=:BookID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookID', $BookID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BookGroupID = $Row["BookGroupID"];
$BookName = $Row["BookName"];
$BookMemo = $Row["BookMemo"];
$BookImageFileName = $Row["BookImageFileName"];
$BookState = $Row["BookState"];
$BookView = $Row["BookView"];

if ($BookImageFileName==""){
	$StrBookImageFileName = $AppDomain."/images/no_photo.png";
}else{
	$StrBookImageFileName = $AppDomain."/uploads/book_images/".$BookImageFileName;
}


$PageBookReadHTML = "";
$PageBookReadHTML .= "<img src=\"".$StrBookImageFileName."\" class=\"book_intro_img\">";
$PageBookReadHTML .= "<div class=\"book_intro_bottom\">";
$PageBookReadHTML .= "	<h3 class=\"book_intro_name\">".$BookName."</h3>";
$PageBookReadHTML .= "	<ul class=\"book_intro_text\">";
$PageBookReadHTML .= "		".str_replace("\n","<br>",$BookMemo)." ";
$PageBookReadHTML .= "	</ul>";
$PageBookReadHTML .= "</div>";


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageBookReadHTML"] = $PageBookReadHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>