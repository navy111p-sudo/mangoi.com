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
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


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
$LocalLinkMemberLevelID = $Row["MemberLevelID"]; 



$ChatRoomListHTML = "";


$ViewTable = "
	select 
		A.MangoTalkID,
		A.MangoTalkType,
		(select MangoTalkMsgRegDateTime from MangoTalkMsgs where MangoTalkID=A.MangoTalkID order by MangoTalkMsgRegDateTime desc limit 0,1) as MangoTalkMsgRegDateTime,
		(select MangoTalkMsg from MangoTalkMsgs where MangoTalkID=A.MangoTalkID order by MangoTalkMsgRegDateTime desc limit 0,1) as MangoTalkMsg
	from MangoTalks A 
	where 
		A.MangoTalkID in (select MangoTalkID from MangoTalkMembers where MemberID=$LocalLinkMemberID ) 
		and A.MangoTalkID > 1
	";

$Sql = "select * from ($ViewTable) V order by V.MangoTalkMsgRegDateTime desc";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


if ($LocalLinkMemberLevelID < 12 ){//대리점 이상 관리자
	$Sql2 = "select 
		A.MangoTalkID,
		A.MangoTalkType,
		(select MangoTalkMsgRegDateTime from MangoTalkMsgs where MangoTalkID=A.MangoTalkID order by MangoTalkMsgRegDateTime desc limit 0,1) as MangoTalkMsgRegDateTime,
		(select MangoTalkMsg from MangoTalkMsgs where MangoTalkID=A.MangoTalkID order by MangoTalkMsgRegDateTime desc limit 0,1) as MangoTalkMsg
	from MangoTalks A 
	where 
		A.MangoTalkID = 1
	";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	$Stmt2 = null;
	$MangoTalkMsgRegDateTime = $Row2["MangoTalkMsgRegDateTime"]; 
	$MangoTalkMsg = $Row2["MangoTalkMsg"]; 
	$StrMangoTalkMsgRegDateTime = str_replace("-",".",substr($MangoTalkMsgRegDateTime, 0,10));

	/*
	$ChatRoomListHTML .= "<li>";
	$ChatRoomListHTML .= "<a href=\"#\" class=\"open-popup item-link item-content\" data-popup=\".popup-chat-room\" onclick=\"GetMainChatRoomMsgPreSet('','1')\">";
	$ChatRoomListHTML .= "	<div class=\"chat_list_photo\" style=\"background-image:url(".$AppDomain."/images/no_photo.png".");\"></div>";
	$ChatRoomListHTML .= "	<div class=\"chat_list_wrap\">";
	$ChatRoomListHTML .= "		<div class=\"chat_list_name\">본사-지사 단톡방</div>";
	$ChatRoomListHTML .= "		<div class=\"chat_list_content ellipsis\">".$MangoTalkMsg."</div>";
	$ChatRoomListHTML .= "	</div>";
	$ChatRoomListHTML .= "	<div class=\"chat_list_date\">".$StrMangoTalkMsgRegDateTime."</div>";
	$ChatRoomListHTML .= "</a>";
	$ChatRoomListHTML .= "</li>";
	*/
}


$ListNum = 1;
while($Row = $Stmt->fetch()) {

	$MangoTalkID = $Row["MangoTalkID"];
	$MangoTalkType = $Row["MangoTalkType"];
	$MangoTalkMsgRegDateTime = $Row["MangoTalkMsgRegDateTime"];
	$MangoTalkMsg = $Row["MangoTalkMsg"];

	$StrMangoTalkMsgRegDateTime = str_replace("-",".",substr($MangoTalkMsgRegDateTime, 0,10));

	$StrMemberPhoto = $AppDomain."/images/no_photo.png";


	$Sql2 = "
			select 
				B.MemberName,
				B.MemberNickName
			from MangoTalkMembers A
				inner join Members B on A.MemberID=B.MemberID 
			where 
				A.MemberID<>:MemberID 
				and A.MangoTalkID=:MangoTalkID 
			order by A.MangoTalkMemberRegDateTime asc";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':MemberID', $LocalLinkMemberID);
	$Stmt2->bindParam(':MangoTalkID', $MangoTalkID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

	$FriendNames = "";
	while($Row2 = $Stmt2->fetch()) {
		$FriendNames = $FriendNames . $Row2["MemberName"];
	}
	$Stmt2 = null;

	$ChatRoomListHTML .= "<li>";
	$ChatRoomListHTML .= "<a href=\"#\" class=\"open-popup item-link item-content\" data-popup=\".popup-chat-room\" onclick=\"GetMainChatRoomMsgPreSet('','".$MangoTalkID."')\">";
	$ChatRoomListHTML .= "	<div class=\"chat_list_photo\" style=\"background-image:url(".$StrMemberPhoto.");\"></div>";
	$ChatRoomListHTML .= "	<div class=\"chat_list_wrap\">";
	$ChatRoomListHTML .= "		<div class=\"chat_list_name\">".$FriendNames."</div>";
	$ChatRoomListHTML .= "		<div class=\"chat_list_content ellipsis\">".$MangoTalkMsg."</div>";
	$ChatRoomListHTML .= "	</div>";
	$ChatRoomListHTML .= "	<div class=\"chat_list_date\">".$StrMangoTalkMsgRegDateTime."</div>";
	$ChatRoomListHTML .= "</a>";
	$ChatRoomListHTML .= "</li>";
}
$Stmt = null;


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["ChatRoomListHTML"] = $ChatRoomListHTML;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>