<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$SubMenuID = isset($_REQUEST["SubMenuID"]) ? $_REQUEST["SubMenuID"] : "";
$MenuUrl = isset($_REQUEST["MenuUrl"]) ? $_REQUEST["MenuUrl"] : "";
$MenuType = isset($_REQUEST["MenuType"]) ? $_REQUEST["MenuType"] : "";
$MenuName = isset($_REQUEST["MenuName"]) ? $_REQUEST["MenuName"] : "";

$Result = 0;
$LastID = 0;

if($MenuType=="") {
	$MenuType = 1;
}

// 계정과 메뉴가 있는지 체크
$Sql = "
	select 
		count(*) as TotalRowCount
	from FavoriteLmsMenus A 
	where
		A.MemberID=:MemberID
		and 
		A.FavoriteLmsMenuSubMenuID=:FavoriteLmsMenuSubMenuID
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':FavoriteLmsMenuSubMenuID', $SubMenuID);
$Stmt->execute();
$Row = $Stmt->fetch();
$TotalRowCount = $Row["TotalRowCount"];
$Stmt = null;

if($TotalRowCount>0) {
	$Sql = "
		select 
			A.FavoriteLmsMenuState
		from FavoriteLmsMenus A 
		where
			A.MemberID=:MemberID
			and 
			A.FavoriteLmsMenuSubMenuID=:FavoriteLmsMenuSubMenuID
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':FavoriteLmsMenuSubMenuID', $SubMenuID);
	$Stmt->execute();
	$Row = $Stmt->fetch();
	$FavoriteLmsMenuState = $Row["FavoriteLmsMenuState"];
	$Stmt = null;

	if($FavoriteLmsMenuState==1) {
		$Sql = "update FavoriteLmsMenus set FavoriteLmsMenuState=0 where FavoriteLmsMenuSubMenuID=:FavoriteLmsMenuSubMenuID and MemberID=:MemberID";
	} else if($FavoriteLmsMenuState==0) {
		$Sql = "update FavoriteLmsMenus set FavoriteLmsMenuState=1 where FavoriteLmsMenuSubMenuID=:FavoriteLmsMenuSubMenuID and MemberID=:MemberID";
		$Result = 1;
		$LastID = $SubMenuID;
	}
	// 이미 있다면 삭제
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':FavoriteLmsMenuSubMenuID', $SubMenuID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->execute();
	$Stmt = null;

} else {
	// 없다면 추가
	$Sql = "
		insert into FavoriteLmsMenus 
			(MemberID, FavoriteLmsMenuSubMenuID, FavoriteLmsMenuType, FavoriteLmsMenuName, FavoriteLmsMenuUrl, FavoriteLmsMenuState) 
		values 
			(:MemberID, :FavoriteLmsMenuSubMenuID, :FavoriteLmsMenuType, :FavoriteLmsMenuName, :FavoriteLmsMenuUrl, 1);";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':FavoriteLmsMenuSubMenuID', $SubMenuID);
	$Stmt->bindParam(':FavoriteLmsMenuType', $MenuType);
	$Stmt->bindParam(':FavoriteLmsMenuName', $MenuName);
	$Stmt->bindParam(':FavoriteLmsMenuUrl', $MenuUrl);
	$Stmt->execute();
	$LastID = $DbConn->lastInsertId("FavoriteLmsMenus");
	$Result = 1;
	$Stmt = null;

	$Sql = "
		select 
			A.FavoriteLmsMenuSubMenuID
		from FavoriteLmsMenus A 
		where
			A.MemberID=:MemberID
			and 
			A.FavoriteLmsMenuID=:FavoriteLmsMenuID
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':FavoriteLmsMenuID', $LastID);
	$Stmt->execute();
	$Row = $Stmt->fetch();
	$LastID = $Row["FavoriteLmsMenuSubMenuID"];
	$Stmt = null;
}


$Sql_Last = "
	select
		FavoriteLmsMenuSubMenuID,
		Rank
	from (
		select
			t.FavoriteLmsMenuSubMenuID,
			@rownum := @rownum + 1 AS Rank
		from FavoriteLmsMenus t
		inner join (select @rownum := 0) r
		where
			t.MemberID=:MemberID
			and
			t.FavoriteLmsMenuState=1
		order by t.FavoriteLmsMenuSubMenuID asc
	) res
	where
		res.FavoriteLmsMenuSubMenuID=:FavoriteLmsMenuSubMenuID
";
$Stmt_Last = $DbConn->prepare($Sql_Last);
$Stmt_Last->bindParam(':MemberID', $MemberID);
$Stmt_Last->bindParam(':FavoriteLmsMenuSubMenuID', $LastID);
$Stmt_Last->execute();
$Row_Last = $Stmt_Last->fetch();
$Rank = $Row_Last["Rank"];
$Stmt_Last = null;



$ArrValue["Result"] = $Result;
$ArrValue["Rank"] = $Rank;
$QueryResult = my_json_encode($ArrValue);
echo $QueryResult;



function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');

?>