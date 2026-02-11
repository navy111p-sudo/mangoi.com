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
$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$TempSql = isset($_REQUEST["TempSql"]) ? $_REQUEST["TempSql"] : "";
$RowCount = isset($_REQUEST["RowCount"]) ? $_REQUEST["RowCount"] : "";
$Count = isset($_REQUEST["Count"]) ? $_REQUEST["Count"] : "";
$TotalCount = isset($_REQUEST["TotalCount"]) ? $_REQUEST["TotalCount"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


$TotalPageCount = 0;
$ChatCategoryListHTML = "";
$StrMemberName = "";
$StrMemberPhoto = "";
$StrMemberNickName = "";

$TotalCount = (int)$TotalCount;
$RowCount = (int)$RowCount;

$TotalPageCount = ceil($TotalCount / $RowCount);
$StartRowNum = $RowCount * ($CurrentPage - 1);

$Sql = "select 
			A.*
		from Members A 
		where A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$CenterID = $Row["CenterID"]; 
$LocalLinkMemberLevelID = $Row["MemberLevelID"]; 



$Sql_all = "select 
			A.CenterName, 
			A.CenterID 
		from Centers A where A.CenterState=1 limit ".$StartRowNum.", ".$RowCount." ";
$Stmt_all = $DbConn->prepare($Sql_all);
$Stmt_all->execute();
$Stmt_all->setFetchMode(PDO::FETCH_ASSOC);

//$Count = (($CurrentPage-1) * $RowCount) + 10001;


while($Row_all = $Stmt_all->fetch()) {

	

	$CenterName = $Row_all["CenterName"];
	$CenterID = $Row_all["CenterID"];

	$SendSql1 = "select A.MemberID,A.MemberName,A.MemberLevelID,A.MemberPhoto,A.MemberNickName from Members A where A.MemberState=1 and A.CenterID=$CenterID and ( A.MemberLevelID=12 or A.MemberLevelID=13 or A.MemberLevelID=18 or A.MemberLevelID=19 ) order by MemberLevelID asc";
	$TotalDataPageCount1 = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.CenterID=$CenterID and ( A.MemberLevelID=12 or A.MemberLevelID=13 or A.MemberLevelID=18 or A.MemberLevelID=19 ) order by MemberLevelID asc";
	$Stmt = $DbConn->prepare($TotalDataPageCount1);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount1 = $Row["TotalCount"];

	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(1, '".$SendSql1."', '".$RowCount."', ".$Count.", ".$TotalCount1.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_academy.png);\"></span> ".$CenterName ;
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$Count."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

	$ChatCategoryListHTML .= " <li> ";
	$ChatCategoryListHTML .= " <a href=\"#\" class=\"open-popup item-link item-content\" data-popup=\".popup-chat-room\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_list_photo\" style=\"background-image:url();\"></div> ";
	$ChatCategoryListHTML .= " <div class=\"chat_list_wrap\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_list_name\"></div> ";
	$ChatCategoryListHTML .= " <div class=\"chat_list_content ellipsis\"></div> ";
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <div class=\"chat_list_date\">></div> ";
	$ChatCategoryListHTML .= " </a> ";
	$ChatCategoryListHTML .= " </li> ";
	
	$ChatCategoryListHTML .= " </ul> ";

	$Count++;
}


if($TotalPageCount>$CurrentPage) {
	$CurrentPage = $CurrentPage + 1;

	$ChatCategoryListHTML .= " <li class=\"accordion-item\" id=\"MoreCategoryBtn\" style=\"text-align:left; font-size:20px;\"> ";
	$ChatCategoryListHTML .= " <div style=\"color:#333;\"> ";
	$ChatCategoryListHTML .= " <a class=\"btn_list_more\" href=\"javascript:GetChatCategoryListAppend(".$CurrentPage.", '".$Sql_all."', '".$RowCount."', ".$Count.", ".$TotalCount.")\"> ";
	$ChatCategoryListHTML .= "MORE<img src=\"images/btn_more_black.png\" class=\"btn_more_black\">";
	$ChatCategoryListHTML .= " </a> ";
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " </li> ";
}


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["ChatCategoryListHTML"] = $ChatCategoryListHTML;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>