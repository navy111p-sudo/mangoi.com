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


$ChatCategoryCount = 1;
$ChatCategoryListHTML = "";
$StrMemberName = "";
$StrMemberPhoto = "";
$StrMemberNickName = "";
$CurrentPage = 1; // 현재 페이지
$RowCount = 10; // 1 페이지당 데이터 출력 갯수
$CategoryRowCount = 10; // 카테고리쪽 데이터 출력 갯수


//$ChatCategoryCount = (($CurrentPage-1) * $CategoryRowCount) + 1;


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
$CenterID = $Row["CenterID"]; //학생, 대리점 일경우
$BranchID = $Row["BranchID"]; //지사 일경우
$LocalLinkMemberLevelID = $Row["MemberLevelID"]; 

if($LocalLinkMemberLevelID==19 || $LocalLinkMemberLevelID==18) { // 학생, 학부모 ==========================================================================================

	
	//=================================== 학원 ===================================
	$Sql = "select A.* from Centers A where A.CenterID=:CenterID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$CenterName = $Row["CenterName"];

	
	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.CenterID=$CenterID and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=12 or A.MemberLevelID=13) order by A.MemberLevelID asc, A.MemberName asc";// 멤버 리스트를 불러오기 위한 sql문

	$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.CenterID=$CenterID and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=12 or A.MemberLevelID=13)";
	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_academy.png);\"></span>".$CenterName;///// 학원 건물 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;
	//=================================== 학원 ===================================


	//=================================== 필리핀 강사 ===================================
	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=15 order by A.MemberName asc";// 멤버 리스트를 불러오기 위한 sql문

	$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=15";
	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_teacher.png);\"></span>교육센터 강사진";///// 강사 얼굴 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;
	//=================================== 필리핀 강사 ===================================

	
	//=================================== 본사 실무진 ===================================
	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=3 or A.MemberLevelID=4 or A.MemberLevelID=5 ) order by A.MemberLevelID asc";// 멤버 리스트를 불러오기 위한 sql문

	$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=3 or A.MemberLevelID=4 or A.MemberLevelID=5 )";
	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_header.png);\"></span>본사 실무진";//// 본사 건물 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;
	//=================================== 본사 실무진 ===================================


} else if($LocalLinkMemberLevelID==15) { // 필리핀 강사  ==========================================================================================


	//=================================== 본사 실무진 ===================================
	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=3 or A.MemberLevelID=4 or A.MemberLevelID=5 ) order by A.MemberLevelID asc";// 멤버 리스트를 불러오기 위한 sql문

	$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=3 or A.MemberLevelID=4 or A.MemberLevelID=5 )";
	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_header.png);\"></span>Head Office (본사)";//// 본사 건물 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;
	//=================================== 본사 실무진 ===================================

	//=================================== 대리점 루프 ===================================
	$Sql_all2 = "select 
					count(*) as TotalCount 
			from Centers A where A.CenterState=1";
	$Stmt_all2 = $DbConn->prepare($Sql_all2);
	$Stmt_all2->execute();
	$Stmt_all2->setFetchMode(PDO::FETCH_ASSOC);
	$Row_all2 = $Stmt_all2->fetch();
	$TotalCount_all = $Row_all2["TotalCount"];


	$Sql_Loop = "select A.* from Centers A where A.CenterState=1 order by A.CenterName asc limit 0, ".$CategoryRowCount."";
	$Stmt_Loop = $DbConn->prepare($Sql_Loop);
	$Stmt_Loop->execute();
	while($Row_Loop = $Stmt_Loop->fetch()) {

		$CenterID_Loop = $Row_Loop["CenterID"];
		$CenterName_Loop = $Row_Loop["CenterName"];

		
		$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ((A.CenterID=$CenterID_Loop and (A.MemberLevelID=19 or A.MemberLevelID=12 or A.MemberLevelID=13)) or A.MemberID in (select MemberID from MemberChilds where MemberChildID in (select MemberID from Members where MemberState=1 and CenterID=$CenterID_Loop and MemberID<>$LocalLinkMemberID and MemberLevelID=19 ))) order by A.MemberLevelID asc, A.MemberName asc";
		$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ((A.CenterID=$CenterID_Loop and (A.MemberLevelID=19 or A.MemberLevelID=12 or A.MemberLevelID=13)) or A.MemberID in (select MemberID from MemberChilds where MemberChildID in (select MemberID from Members where MemberState=1 and CenterID=$CenterID_Loop and MemberID<>$LocalLinkMemberID and MemberLevelID=19 ))) ";
		$Stmt = $DbConn->prepare($SendSqlCount);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$TotalCount = $Row["TotalCount"];

		
		

		$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
		$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
		$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_academy.png);\"></span>".$CenterName_Loop;//학원 건물 아이콘(이성현)
		$ChatCategoryListHTML .= " </div> ";
		$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
		$ChatCategoryCount++;

	}
	$Stmt_Loop = null;


	$TotalPageCount = ceil($TotalCount_all / $CategoryRowCount);
	if($TotalPageCount > $CurrentPage) {
		$CurrentPage = $CurrentPage + 1;

		$ChatCategoryListHTML .= " <li class=\"accordion-item\" id=\"MoreCategoryBtn\" style=\"text-align:left; font-size:20px;\"> ";
		$ChatCategoryListHTML .= " <div style=\"color:#333;\"> ";
		$ChatCategoryListHTML .= " <a class=\"btn_list_more\" href=\"javascript:GetChatCategoryListAppend(".$CurrentPage.", '".$Sql_Loop."', '".$CategoryRowCount."', ".$ChatCategoryCount.", ".$TotalCount_all.")\"> ";
		$ChatCategoryListHTML .= "MORE<img src=\"images/btn_more_black.png\" class=\"btn_more_black\">";
		$ChatCategoryListHTML .= " </a> ";
		$ChatCategoryListHTML .= " </div> ";
		$ChatCategoryListHTML .= " </li> ";
	}
	//=================================== 대리점 루프 ===================================

} else if($LocalLinkMemberLevelID==12 || $LocalLinkMemberLevelID==13) { // 대리점 직원 및 대리점장  ==========================================================================================

	//=================================== 학생 ===================================
	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.CenterID=$CenterID and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=19 order by A.MemberName asc";
	
	$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.CenterID=$CenterID and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=19 ";
	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_student.png);\"></span>수강생";//학생 얼굴 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;
	//=================================== 학생 ===================================


	//=================================== 학부모 ===================================
	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.MemberID in (select MemberID from MemberChilds where MemberChildID in (select MemberID from Members where MemberState=1 and CenterID=$CenterID and MemberID<>$LocalLinkMemberID and MemberLevelID=19 )) and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=18 order by A.MemberName asc";
	$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.MemberID in (select MemberID from MemberChilds where MemberChildID in (select MemberID from Members where MemberState=1 and CenterID=$CenterID and MemberID<>$LocalLinkMemberID and MemberLevelID=19 )) and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=18 ";
	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_mom.png);\"></span>학부모";//학부모 얼굴 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;
	//=================================== 학부모 ===================================


	//=================================== 지사 ===================================
	$Sql = "select A.BranchID, B.BranchName from Centers A inner join Branches B on A.BranchID=B.BranchID where A.CenterID=:CenterID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$BranchID = $Row["BranchID"];
	$BranchName = $Row["BranchName"];


	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.BranchID=$BranchID and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=9 or A.MemberLevelID=10) order by A.MemberLevelID asc, A.MemberName asc";// 멤버 리스트를 불러오기 위한 sql문
	$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.BranchID=$BranchID and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=9 or A.MemberLevelID=10)";

	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_branch.png);\"></span>".$BranchName;//지사 건물 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;
	//=================================== 지사 ===================================


	//=================================== 필리핀 강사 ===================================
	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=15 order by A.MemberName asc";// 멤버 리스트를 불러오기 위한 sql문

	$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=15";
	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_teacher.png);\"></span>교육센터 강사진";///// 강사 얼굴 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;
	//=================================== 필리핀 강사 ===================================

	
	//=================================== 본사 실무진 ===================================
	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=3 or A.MemberLevelID=4 or A.MemberLevelID=5 ) order by A.MemberLevelID asc";// 멤버 리스트를 불러오기 위한 sql문

	$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=3 or A.MemberLevelID=4 or A.MemberLevelID=5 )";
	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_header.png);\"></span>본사 실무진";//// 본사 건물 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;
	//=================================== 본사 실무진 ===================================
	

} else if($LocalLinkMemberLevelID==9 || $LocalLinkMemberLevelID==10) { // 지사 및 지사직원  ==========================================================================================

	
	
	//=================================== 소속 학원장 ===================================

	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A inner join Centers B on A.CenterID=B.CenterID where B.BranchID=$BranchID and A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=12 or A.MemberLevelID=13 ) order by B.CenterName asc, A.MemberLevelID asc";// 멤버 리스트를 불러오기 위한 sql문

	$SendSqlCount = "select count(*) as TotalCount from Members A inner join Centers B on A.CenterID=B.CenterID where B.BranchID=$BranchID and A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=12 or A.MemberLevelID=13 )";
	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_jang.png);\"></span>가맹 대리점";//// 학원장 얼굴 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;


	//=================================== 소속 학원장 ===================================


	//=================================== 타 지사장 ===================================

	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=9 order by A.MemberName asc";// 멤버 리스트를 불러오기 위한 sql문

	$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=9";
	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_branch_jang.png);\"></span>지사장";//// 지사장 얼굴 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;


	//=================================== 타 지사장 ===================================
	
	
	//=================================== 본사 실무진 ===================================
	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=3 or A.MemberLevelID=4 or A.MemberLevelID=5 ) order by A.MemberLevelID asc";// 멤버 리스트를 불러오기 위한 sql문

	$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ( A.MemberLevelID=3 or A.MemberLevelID=4 or A.MemberLevelID=5 )";
	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_header.png);\"></span>본사 실무진";//// 본사 건물 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;
	//=================================== 본사 실무진 ===================================


}else if($LocalLinkMemberLevelID<=5) { // 관리자, 프랜차이즈, 영업본부  ==========================================================================================

	//=================================== 지사장 ===================================

	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=9 order by A.MemberName asc";// 멤버 리스트를 불러오기 위한 sql문

	$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=9";
	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_branch_jang.png);\"></span>지사장";//// 지사장 얼굴 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;


	//=================================== 지사장 ===================================


	//=================================== 필리핀 강사 ===================================
	$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=15 order by A.MemberName asc";// 멤버 리스트를 불러오기 위한 sql문

	$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and A.MemberLevelID=15";
	$Stmt = $DbConn->prepare($SendSqlCount);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalCount = $Row["TotalCount"];

	
	
	$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
	$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
	$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_teacher.png);\"></span>교육센터 강사진";///// 강사 얼굴 아이콘(이성현)
	$ChatCategoryListHTML .= " </div> ";
	$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
	$ChatCategoryCount++;
	//=================================== 필리핀 강사 ===================================


	//=================================== 대리점 루프 ===================================

	$Sql_all2 = "select 
					count(*) as TotalCount 
			from Centers A where A.CenterState=1";
	$Stmt_all2 = $DbConn->prepare($Sql_all2);
	$Stmt_all2->execute();
	$Stmt_all2->setFetchMode(PDO::FETCH_ASSOC);
	$Row_all2 = $Stmt_all2->fetch();
	$TotalCount_all = $Row_all2["TotalCount"];


	$Sql_Loop = "select A.* from Centers A where A.CenterState=1 order by A.CenterName asc limit 0, ".$CategoryRowCount."";
	$Stmt_Loop = $DbConn->prepare($Sql_Loop);
	$Stmt_Loop->execute();
	while($Row_Loop = $Stmt_Loop->fetch()) {

		$CenterID_Loop = $Row_Loop["CenterID"];
		$CenterName_Loop = $Row_Loop["CenterName"];

		
		$SendSql = "select A.MemberID, A.MemberName, A.MemberLevelID, A.MemberPhoto, A.MemberNickName from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ((A.CenterID=$CenterID_Loop and (A.MemberLevelID=19 or A.MemberLevelID=12 or A.MemberLevelID=13)) or A.MemberID in (select MemberID from MemberChilds where MemberChildID in (select MemberID from Members where MemberState=1 and CenterID=$CenterID_Loop and MemberID<>$LocalLinkMemberID and MemberLevelID=19 ))) order by A.MemberLevelID asc, A.MemberName asc";
		$SendSqlCount = "select count(*) as TotalCount from Members A where A.MemberState=1 and A.MemberID<>$LocalLinkMemberID and ((A.CenterID=$CenterID_Loop and (A.MemberLevelID=19 or A.MemberLevelID=12 or A.MemberLevelID=13)) or A.MemberID in (select MemberID from MemberChilds where MemberChildID in (select MemberID from Members where MemberState=1 and CenterID=$CenterID_Loop and MemberID<>$LocalLinkMemberID and MemberLevelID=19 ))) ";
		$Stmt = $DbConn->prepare($SendSqlCount);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$TotalCount = $Row["TotalCount"];

		
		

		$ChatCategoryListHTML .= " <li class=\"accordion-item\"> ";
		$ChatCategoryListHTML .= " <div class=\"chat_group accordion-item-toggle\" style=\"color:#333;\" onclick=\"GetChatCategoryMemberList(".$CurrentPage.", '".$SendSql."', '".$RowCount."', ".$ChatCategoryCount.", ".$TotalCount.");\"> ";
		$ChatCategoryListHTML .= " <img src=\"images/btn_next_black.png\" class=\"chat_group_icon\"><span class=\"chat_group_img\" style=\"background-image:url(images/chat_academy.png);\"></span>".$CenterName_Loop;//학원 건물 아이콘(이성현)
		$ChatCategoryListHTML .= " </div> ";
		$ChatCategoryListHTML .= " <ul id=\"DivChatCategoryMemberList_".$ChatCategoryCount."\" class=\"chat_list accordion-item-content\" style=\"margin:0; padding:0;\"> ";

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
		$ChatCategoryCount++;

	}
	$Stmt_Loop = null;

	$TotalPageCount = ceil($TotalCount_all / $CategoryRowCount);
	if($TotalPageCount > $CurrentPage) {
		$CurrentPage = $CurrentPage + 1;

		$ChatCategoryListHTML .= " <li class=\"accordion-item\" id=\"MoreCategoryBtn\" style=\"text-align:left; font-size:20px;\"> ";
		$ChatCategoryListHTML .= " <div style=\"color:#333;\"> ";
		$ChatCategoryListHTML .= " <a class=\"btn_list_more\" href=\"javascript:GetChatCategoryListAppend(".$CurrentPage.", '".$Sql_Loop."', '".$CategoryRowCount."', ".$ChatCategoryCount.", ".$TotalCount_all.")\"> ";
		$ChatCategoryListHTML .= "MORE<img src=\"images/btn_more_black.png\" class=\"btn_more_black\">";
		$ChatCategoryListHTML .= " </a> ";
		$ChatCategoryListHTML .= " </div> ";
		$ChatCategoryListHTML .= " </li> ";
	}
	//=================================== 대리점 루프 ===================================

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