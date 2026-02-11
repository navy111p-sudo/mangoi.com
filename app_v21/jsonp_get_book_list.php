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


$ViewTable = "
		select 
			A.*,
			ifnull((select count(*) from Books where BookGroupID=A.BookGroupID and BookState=1 and BookViewList=1),0) as BookCount
		from BookGroups A 
		where A.BookGroupState=1 order by A.BookGroupOrder asc";//." limit $StartRowNum, $PageListNum";

$Sql = "select * from ($ViewTable) V where BookCount>0";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



$PageBookListHTML = "";

$ListNum = 1;
while($Row = $Stmt->fetch()) {

	$BookGroupID = $Row["BookGroupID"];
	$BookGroupName = $Row["BookGroupName"];
	$BookGroupMemo = $Row["BookGroupMemo"];
	$BookGroupState = $Row["BookGroupState"];
	$BookCount = $Row["BookCount"];


	$PageBookListHTML .= "<h3 class=\"book_course\">".$BookGroupName."</h3>";
	$PageBookListHTML .= "<ul class=\"book_list\">";
	
	$Sql2 = "
			select 
				A.* 
			from Books A 

			where 
				A.BookGroupID=:BookGroupID  
				and A.BookState=1 
				and A.BookViewList=1 
			order by A.BookOrder desc";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':BookGroupID', $BookGroupID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

	while($Row2 = $Stmt2->fetch()) {

		$BookID = $Row2["BookID"];
		$BookName = $Row2["BookName"];
		$BookImageFileName = $Row2["BookImageFileName"];

		if ($BookImageFileName==""){
			$StrBookImageFileName = $AppDomain."/images/no_photo.png";
		}else{
			$StrBookImageFileName = $AppDomain."/uploads/book_images/".$BookImageFileName;
		}

		
		$PageBookListHTML .= "<li>";
		$PageBookListHTML .= "	<a href=\"#\" onclick=\"GetPageBookRead(".$BookID.")\" class=\"open-popup\" data-popup=\".popup-book\">";
		$PageBookListHTML .= "		<img src=\"".$StrBookImageFileName."\" class=\"book_img\">";
		$PageBookListHTML .= "		<div class=\"book_name ellipsis\">".$BookName."</div>";
		$PageBookListHTML .= "	</a>";
		$PageBookListHTML .= "</li>";
	
	}
	$PageBookListHTML .= "</ul>";


	$ListNum++;
}




$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageBookListHTML"] = $PageBookListHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>