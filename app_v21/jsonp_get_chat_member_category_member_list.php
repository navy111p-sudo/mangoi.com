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

$ChatCategoryMemberListHTML = "";
$TotalPageCount = 0;

if($CurrentPage=="") {
	$CurrentPage = 1;
}


$TotalCount = (int)$TotalCount;
$RowCount = (int)$RowCount;

// 총 데이터의 수
/*
$Sql = $TempSql;
$Stmt = $DbConn->prepare($TempSql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$TotalCount = $Row["TotalCount"];
*/

$TotalPageCount = ceil($TotalCount / $RowCount);
$StartRowNum = $RowCount * ($CurrentPage - 1);

// 데이터 출력
$Sql = $TempSql." limit ".$StartRowNum.",".$RowCount;


//$Sql = "$TempSql limit $StartRowNum,$RowCount";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

//echo $CurrentPage . "/";
//echo $Sql;

while($Row = $Stmt->fetch()) {
	
	$MemberID = $Row["MemberID"];
	$MemberName = $Row["MemberName"];
	$MemberLevelID = $Row["MemberLevelID"];
	$MemberPhoto = $Row["MemberPhoto"];
	$MemberNickName = $Row["MemberNickName"];

	if ($MemberPhoto==""){
		$StrMemberPhoto = $AppDomain."/images/no_photo.png";
	}else{
		$StrMemberPhoto = $AppDomain."/uploads/member_photos/".$MemberPhoto;
	}

	if ($MemberLevelID==18) {
		$StrChildName = ""; // init
		$Sql2 = "select A.MemberChildID
					from MemberChilds A 
				where A.MemberID=:MemberID";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':MemberID', $MemberID);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

		while($Row2 = $Stmt2->fetch()) {
			if($StrChildName!="") {
				$StrChildName .= ", ";
			}

			$MemberChildID = $Row2["MemberChildID"];
			$Sql3 = "select A.MemberName as ChildName from Members A where A.MemberID=:MemberChildID";
			$Stmt3 = $DbConn->prepare($Sql3);
			$Stmt3->bindParam(':MemberChildID', $MemberChildID);
			$Stmt3->execute();
			$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
			$Row3 = $Stmt3->fetch();
			$ChildName = $Row3["ChildName"];

			$StrChildName .= $ChildName;
		}
		$Stmt3 = null;
		$StrMemberName = $MemberName."(학부모)";
		$MemberNickName = "(".$StrChildName."의 학부모)";
	} else if ($MemberLevelID==19) {
		$StrMemberName = $MemberName."(학생)";
	} else if ($MemberLevelID==9) {
		$StrMemberName = $MemberName."(지사장)";
	} else if ($MemberLevelID==12) {
		$StrMemberName = $MemberName."(학원장)";
	} else if ($MemberLevelID==13) {
		$StrMemberName = $MemberName."(학원직원)";
	} else if ($MemberLevelID==15) {
		$StrMemberName = $MemberName."(강사)";
	} else if ($MemberLevelID==3) {
		$StrMemberName = $MemberName."(본사관리자)";
	} else if ($MemberLevelID==4) {
		$StrMemberName = $MemberName."(본사직원)";
	}

	$ChatCategoryMemberListHTML .= " <li> ";
	$ChatCategoryMemberListHTML .= " <a href=\"#\" class=\"open-popup item-link item-content\" data-popup=\".popup-chat-room\" onclick=\"GetMainChatRoomMsgPreSet('".$MemberID."','')\"> ";
	$ChatCategoryMemberListHTML .= " <div class=\"chat_list_photo\" style=\"background-image:url(".$StrMemberPhoto.");\"></div> ";
	$ChatCategoryMemberListHTML .= " <div class=\"chat_list_wrap\"> ";
	$ChatCategoryMemberListHTML .= " <div class=\"chat_list_name\">".$StrMemberName."</div> ";
	$ChatCategoryMemberListHTML .= " <div class=\"chat_list_content ellipsis\">".$MemberNickName."</div> ";
	$ChatCategoryMemberListHTML .= " </div> ";
	$ChatCategoryMemberListHTML .= " <div class=\"chat_list_date\">></div> ";
	$ChatCategoryMemberListHTML .= " </a> ";
	$ChatCategoryMemberListHTML .= " </li> ";
}


if($TotalPageCount>$CurrentPage) {
	$CurrentPage = $CurrentPage + 1;

	//$ChatCategoryMemberListHTML .= " <a href=\"#\" onclick=\"GetChatCategoryMemberAppendList(".$DataPageCount.", '".$SendSql."', ".$TotalDataPageCount.")\"> ";
	$ChatCategoryMemberListHTML .= " <a class=\"btn_list_more\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$TempSql."', '".$RowCount."', ".$Count.", ".$TotalCount.")\"> ";
	$ChatCategoryMemberListHTML .= "MORE";
	$ChatCategoryMemberListHTML .= " <img src=\"images/btn_more_black.png\" class=\"btn_more_black\"> ";
	$ChatCategoryMemberListHTML .= " </a> ";
}


$Stmt = null;


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["Count"] = $Count;
$ArrValue["ChatCategoryMemberListHTML"] = $ChatCategoryMemberListHTML;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>