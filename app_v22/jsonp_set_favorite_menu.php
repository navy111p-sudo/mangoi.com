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
$FavoriteMenuOrder = isset($_REQUEST["FavoriteMenuOrder"]) ? $_REQUEST["FavoriteMenuOrder"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$FavoriteMenuListID = isset($_REQUEST["FavoriteMenuListID"]) ? $_REQUEST["FavoriteMenuListID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;

// 아이템이 있는지 여부
$Sql = "
	select 
		count(*) as TotalRowCount
	from FavoriteMenus A 
	where
		A.MemberID=:MemberID
		and
		A.FavoriteMenuListID=:FavoriteMenuListID
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':FavoriteMenuListID', $FavoriteMenuListID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$TotalRowCount = $Row["TotalRowCount"];
$Stmt = null;

// 해당 아이템의 활성화 여부
$Sql = "
	select 
		A.FavoriteMenuState
	from FavoriteMenus A 
	where
		A.MemberID=:MemberID
		and
		A.FavoriteMenuListID=:FavoriteMenuListID
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':FavoriteMenuListID', $FavoriteMenuListID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$FavoriteMenuState = $Row["FavoriteMenuState"];
$Stmt = null;

// 선택된 자리에 아이템이 있었는지 여부
$Sql = "
	select 
		count(*) as TotalRowCountOrder
	from FavoriteMenus A 
	where
		A.MemberID=:MemberID
		and
		A.FavoriteMenuOrder=:FavoriteMenuOrder
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':FavoriteMenuOrder', $FavoriteMenuOrder);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$TotalRowCountOrder = $Row["TotalRowCountOrder"];
$Stmt = null;

if($TotalRowCount>0) {
	if($FavoriteMenuState==1) {
		// 아이템이 있고 그 아이템이 활성화 되어있다면 안내알람
		$ErrNum = 1;
	} else {
		// 아이템이 있고 그 아이템이 비활성화 되어있다면  자리에 있던 원래 아이템을 체크 후 비활성화 후 해당 아이템을 활성화
		if($TotalRowCountOrder>0) {
			$Sql = "
				update FavoriteMenus
				set 
					FavoriteMenuState=0,
					FavoriteMenuModiDateTime=now()
				where
					MemberID=:MemberID
					and
					FavoriteMenuOrder=:FavoriteMenuOrder
			";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':MemberID', $MemberID);
			$Stmt->bindParam(':FavoriteMenuOrder', $FavoriteMenuOrder);
			$Stmt->execute();
			$Stmt = null;
		}

		$Sql2 = "
			update FavoriteMenus
			set 
				FavoriteMenuState=1,
				FavoriteMenuModiDateTime=now(),
				FavoriteMenuOrder=:FavoriteMenuOrder
			where
				MemberID=:MemberID
				and
				FavoriteMenuListID=:FavoriteMenuListID
		";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':MemberID', $MemberID);
		$Stmt2->bindParam(':FavoriteMenuListID', $FavoriteMenuListID);
		$Stmt2->bindParam(':FavoriteMenuOrder', $FavoriteMenuOrder);
		$Stmt2->execute();
		$Stmt2 = null;
	}
} else {
	if($TotalRowCountOrder>0) {
		$Sql = "
			update FavoriteMenus
			set 
				FavoriteMenuState=0,
				FavoriteMenuModiDateTime=now()
			where
				MemberID=:MemberID
				and
				FavoriteMenuOrder=:FavoriteMenuOrder
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->bindParam(':FavoriteMenuOrder', $FavoriteMenuOrder);
		$Stmt->execute();
		$Stmt = null;
	}

	$Sql2 = "
		insert into FavoriteMenus
		( MemberID, FavoriteMenuListID, FavoriteMenuRegDateTime, FavoriteMenuModiDateTime, FavoriteMenuState, FavoriteMenuOrder )
		values
		( :MemberID, :FavoriteMenuListID, now(), now(), 1, :FavoriteMenuOrder );
	";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':FavoriteMenuListID', $FavoriteMenuListID);
	$Stmt2->bindParam(':MemberID', $MemberID);
	$Stmt2->bindParam(':FavoriteMenuOrder', $FavoriteMenuOrder);
	$Stmt2->execute();
}

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;

$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>