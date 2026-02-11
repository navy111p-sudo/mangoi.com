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
$FriendID = isset($_REQUEST["FriendID"]) ? $_REQUEST["FriendID"] : "";
$MangoTalkID = isset($_REQUEST["MangoTalkID"]) ? $_REQUEST["MangoTalkID"] : "";
$LastMangoTalkMsgID = isset($_REQUEST["LastMangoTalkMsgID"]) ? $_REQUEST["LastMangoTalkMsgID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


if ($FriendID!="" && $MangoTalkID==""){//친구목록에서 왔다

	$Sql = "select 
				A.MangoTalkID 
			from MangoTalks A 

			where
				A.MangoTalkID in (select MangoTalkID from MangoTalkMembers where MemberID=:MemberID) 
				and A.MangoTalkID in (select MangoTalkID from MangoTalkMembers where MemberID=:FriendID)
			";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
	$Stmt->bindParam(':FriendID', $FriendID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$MangoTalkID = $Row["MangoTalkID"]; 

	if (!$MangoTalkID){

		$Sql = "
			insert into MangoTalks ( ";
		$Sql .= " MangoTalkType, ";
		$Sql .= " MangoTalkRegDateTime, ";
		$Sql .= " MangoTalkOpenDateTime ";
		$Sql .= " ) values ( ";
		$Sql .= " 1, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$MangoTalkID = $DbConn->lastInsertId();
		$Stmt = null;


		$Sql = "
			insert into MangoTalkMembers ( ";
		$Sql .= " MangoTalkID, ";
		$Sql .= " MemberID, ";
		$Sql .= " MangoTalkMemberRegDateTime ";
		$Sql .= " ) values ( ";
		$Sql .= " :MangoTalkID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " now() ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MangoTalkID', $MangoTalkID);
		$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
		$Stmt->execute();
		$Stmt = null;

		$Sql = "
			insert into MangoTalkMembers ( ";
		$Sql .= " MangoTalkID, ";
		$Sql .= " MemberID, ";
		$Sql .= " MangoTalkMemberRegDateTime ";
		$Sql .= " ) values ( ";
		$Sql .= " :MangoTalkID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " now() ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MangoTalkID', $MangoTalkID);
		$Stmt->bindParam(':MemberID', $FriendID);
		$Stmt->execute();
		$Stmt = null;
	}

}

$Sql = " update MangoTalks set MangoTalkOpenDateTime=now() where MangoTalkID=:MangoTalkID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MangoTalkID', $MangoTalkID);
$Stmt->execute();
$Stmt = null;




$MainChatRoomMsgHTML = "";

$Sql = "
	select 
			A.MangoTalkID,
			A.MangoTalkMsgType,
			A.MangoTalkMsgID,
			A.MemberID,
			A.MangoTalkMsg,
			A.MangoTalkImageName,
			A.MangoTalkImageSaveName,
			A.MangoTalkMsgRegDateTime,
			date_format(A.MangoTalkMsgRegDateTime, '%m.%d %W %p %h:%i') as StrMangoTalkMsgRegDateTime,

			B.MemberLevelID,
			B.MemberName,
			B.MemberNickName,
			B.MemberPhoto
	from MangoTalkMsgs A 
		inner join Members B on A.MemberID=B.MemberID 
	where A.MangoTalkID=:MangoTalkID and A.MangoTalkMsgID>:LastMangoTalkMsgID
	order by A.MangoTalkMsgRegDateTime asc 
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MangoTalkID', $MangoTalkID);
$Stmt->bindParam(':LastMangoTalkMsgID', $LastMangoTalkMsgID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListNum = 1;
$OldMangoTalkMsgRegDateTime = "1900-01-01 00:00:00";
$MangoTalkMsgID = 0;


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



if ($LastMangoTalkMsgID==0){
	if ($MangoTalkID==1){
		$MainChatRoomMsgHTML .= "<div class=\"messages-date TrnTag\">-- 본사-지사 단톡방 대화 시작 --</div>";
	}else{
		$MainChatRoomMsgHTML .= "<div class=\"messages-date\">-- ".$FriendNames."<trn class='TrnTag'>님과 대화 시작 --</trn></div>";
	}
}


while($Row = $Stmt->fetch()) {
	
	$MangoTalkID = $Row["MangoTalkID"];
	$MangoTalkMsgType = $Row["MangoTalkMsgType"];
	$MangoTalkMsgID = $Row["MangoTalkMsgID"];
	$MemberID = $Row["MemberID"];
	$MangoTalkMsg = $Row["MangoTalkMsg"];
	$MangoTalkImageName = $Row["MangoTalkImageName"];
	$MangoTalkImageSaveName = $Row["MangoTalkImageSaveName"];
	$MangoTalkMsgRegDateTime = $Row["MangoTalkMsgRegDateTime"];
	$StrMangoTalkMsgRegDateTime = $Row["StrMangoTalkMsgRegDateTime"];
	
	$MemberID = $Row["MemberID"];
	$MemberName = $Row["MemberName"];
	$MemberNickName = $Row["MemberNickName"];
	$MemberPhoto = $Row["MemberPhoto"];

	if ($MemberPhoto==""){
		$StrMemberPhoto = $AppDomain."/images/no_photo.png";
	}else{
		$StrMemberPhoto = $AppDomain."/uploads/member_photos/".$MemberPhoto;
	}

	
	$start_date = new DateTime($OldMangoTalkMsgRegDateTime);
	$since_start = $start_date->diff(new DateTime($MangoTalkMsgRegDateTime));
	
	/*
	if ($since_start->s > 60){
		$MainChatRoomMsgHTML .= "<div class=\"messages-date\">".$StrMangoTalkMsgRegDateTime."</div>";
		$NewCheckDate = 1;
	}else{
		$NewCheckDate = 0;
	}
	*/

	//$MainChatRoomMsgHTML .= "<div class=\"messages-date\">".$StrMangoTalkMsgRegDateTime."</div>";
			
	if ($MemberID!=$LocalLinkMemberID){//받은 메시지
		$MainChatRoomMsgHTML .= "<div class=\"message message-last message-with-avatar message-received\" id=\"MangoTalkMsg_".$MangoTalkMsgID."\">";
		$MainChatRoomMsgHTML .= "	<div class=\"message-name\">".$MemberName."(".$StrMangoTalkMsgRegDateTime.")</div>";

		if ($MangoTalkMsgType==1){
			$MainChatRoomMsgHTML .= "	<div class=\"message-text\">".$MangoTalkMsg."</div>";
		}else{
			$MainChatRoomMsgHTML .= "	<div class=\"message-text\"><img src=\"".$AppDomain."/uploads/chat_images/".$MangoTalkImageSaveName."\"  onclick=\"ChatImageDownload(".$MangoTalkMsgID.", '".$MangoTalkImageSaveName."', '".$MangoTalkImageName."')\"></div>";
		}
		$MainChatRoomMsgHTML .= "	<div style=\"background-image:url(".$StrMemberPhoto.")\" class=\"message-avatar\"></div>";
		$MainChatRoomMsgHTML .= "</div>";
	
	}else{//보낸 메시지
		if ($MangoTalkMsgType==1){
			$MainChatRoomMsgHTML .= "<div class=\"message message-last message-sent\" id=\"MangoTalkMsg_".$MangoTalkMsgID."\">";
			//$MainChatRoomMsgHTML .= "	<div class=\"message-name\">".$MemberName."(".$StrMangoTalkMsgRegDateTime.")</div>";
			$MainChatRoomMsgHTML .= "	<div class=\"message-name\">".$StrMangoTalkMsgRegDateTime."</div>";
			$MainChatRoomMsgHTML .= "	<div class=\"message-text\">".$MangoTalkMsg."</div>";
			$MainChatRoomMsgHTML .= "</div>";
		}else{
			$MainChatRoomMsgHTML .= "<div class=\"message message-last message-sent\" id=\"MangoTalkMsg_".$MangoTalkMsgID."\">";
			//$MainChatRoomMsgHTML .= "	<div class=\"message-name\">".$MemberName."(".$StrMangoTalkMsgRegDateTime.")</div>";
			$MainChatRoomMsgHTML .= "	<div class=\"message-name\">".$StrMangoTalkMsgRegDateTime."</div>";
			$MainChatRoomMsgHTML .= "	<div class=\"message-text\" style=\"background-color:#f1f1f1;\"><img src=\"".$AppDomain."/uploads/chat_images/".$MangoTalkImageSaveName."\" onclick=\"ChatImageDownload(".$MangoTalkMsgID.", '".$MangoTalkImageSaveName."', '".$MangoTalkImageName."')\"></div>";
			$MainChatRoomMsgHTML .= "</div>";
		}
	}

	//if ($NewCheckDate == 1){
	//	$OldMangoTalkMsgRegDateTime = $MangoTalkMsgRegDateTime;
	//}
}
$Stmt = null;

/*
$MainChatRoomMsgHTML = "";

$MainChatRoomMsgHTML .= "<div class=\"messages-date\">Sunday, Feb 9 <span>12:58</span></div>";
$MainChatRoomMsgHTML .= "<div class=\"message message-sent\">";
$MainChatRoomMsgHTML .= "	<div class=\"message-text\">Hello</div>";
$MainChatRoomMsgHTML .= "</div>";
$MainChatRoomMsgHTML .= "<div class=\"message message-sent\">";
$MainChatRoomMsgHTML .= "	<div class=\"message-text\">How are you?</div>";
$MainChatRoomMsgHTML .= "</div>";
$MainChatRoomMsgHTML .= "<div class=\"message message-with-avatar message-received\">";
$MainChatRoomMsgHTML .= "	<div class=\"message-name\">Kate</div>";
$MainChatRoomMsgHTML .= "	<div class=\"message-text\"><img src=\"images/sample_event_list_1.jpg\"></div>";
$MainChatRoomMsgHTML .= "	<div style=\"background-image:url(images/sample_teacher_photo_1.jpg)\" class=\"message-avatar\"></div>";
$MainChatRoomMsgHTML .= "</div>";


$MainChatRoomMsgHTML .= "<div class=\"messages-date\">Sunday, Feb 3 <span>11:58</span></div>";
$MainChatRoomMsgHTML .= "<div class=\"message message-sent\">";
$MainChatRoomMsgHTML .= "	<div class=\"message-text\">Nice photo?</div>";
$MainChatRoomMsgHTML .= "</div>";
$MainChatRoomMsgHTML .= "<div class=\"message message-sent message-pic\">";
$MainChatRoomMsgHTML .= "	<div class=\"message-text\"><img src=\"images/sample_event_list_3.jpg\"></div>";
$MainChatRoomMsgHTML .= "	<div class=\"message-label\">Delivered</div>";
$MainChatRoomMsgHTML .= "</div>";
$MainChatRoomMsgHTML .= "<div class=\"message message-with-avatar message-received\">";
$MainChatRoomMsgHTML .= "	<div class=\"message-name\">Kate</div>";
$MainChatRoomMsgHTML .= "	<div class=\"message-text\">Wow, awesome!</div>";
$MainChatRoomMsgHTML .= "	<div style=\"background-image:url(images/sample_teacher_photo_1.jpg)\" class=\"message-avatar\"></div>";
$MainChatRoomMsgHTML .= "</div>";

*/

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MangoTalkID"] = $MangoTalkID;
$ArrValue["LastMangoTalkMsgID"] = $MangoTalkMsgID;
$ArrValue["MainChatRoomMsgHTML"] = $MainChatRoomMsgHTML;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?> 