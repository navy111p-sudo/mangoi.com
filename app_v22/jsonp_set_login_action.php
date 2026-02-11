<?
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/password_hash.php');


$ErrNum = 0;
$ErrMsg = "";

$ApplyMemberLoginID = isset($_REQUEST["ApplyMemberLoginID"]) ? $_REQUEST["ApplyMemberLoginID"] : "";
$ApplyMemberLoginPW = isset($_REQUEST["ApplyMemberLoginPW"]) ? $_REQUEST["ApplyMemberLoginPW"] : "";
$ApplyRememberID = isset($_REQUEST["ApplyRememberID"]) ? $_REQUEST["ApplyRememberID"] : "";
$RedirectUrl = isset($_REQUEST["RedirectUrl"]) ? $_REQUEST["RedirectUrl"] : "";
$ServerPath = $AppDomain.$AppPath."/";


$LoginIP = $_SERVER['REMOTE_ADDR'];

if ($ApplyRememberID=="on"){
	setcookie("RememberMemberID",$ApplyMemberLoginID, time()+(3600*24*30), "/", ".".$DefaultDomain2);
}else{
	setcookie("RememberMemberID","", time()-1, "/", ".".$DefaultDomain2);
}


//센터, 학생 은 독립 사이트 소속 체크
$Sql = "select 
			count(*) as TotalRowCount 
		from Members A 
			left outer join Centers B on A.CenterID=B.CenterID 
		where 
			A.MemberState=1 
			and A.MemberLoginID=:ApplyMemberLoginID 
			and (
					(B.OnlineSiteID=$OnlineSiteID and A.MemberLevelID>=12 and A.MemberLevelID<=19)
					or 
					(A.MemberLevelID<12)
				)
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];



if ($TotalRowCount==0){
	$MemberLoginPW = "";
	$ErrNum = 1;
	$ErrMsg = "아이디를 잘못 입력하셨습니다.";
}else{

	$Sql = "select MemberLoginPW as MemberLoginPW_hash from Members where MemberState=1 and  MemberLoginID=:ApplyMemberLoginID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberLoginPW_hash = $Row["MemberLoginPW_hash"];
	$VerifyResult = password_verify(sha1($ApplyMemberLoginPW), $MemberLoginPW_hash);
	if ($VerifyResult==false){
		$ErrNum = 2;
		$ErrMsg = "비밀번호를 잘못 입력하셨습니다.";
	}else{
		
		$Sql = "select A.MemberLevelID  
					from Members A 
					where MemberState=1 and  MemberLoginID=:ApplyMemberLoginID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$MemberLevelID = $Row["MemberLevelID"];

		if ($MemberLevelID<=15) {//강사 이상의 권한
			setcookie("LoginAdminID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);
			setcookie("LinkLoginAdminID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);
		}else{
			setcookie("LoginAdminID", "", 0, "/", ".".$DefaultDomain2);
			setcookie("LinkLoginAdminID", "", 0, "/", ".".$DefaultDomain2);
		}
		
		setcookie("LoginMemberID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);
		setcookie("LinkLoginMemberID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);

		$Sql = "update Members set LastLoginDateTime=now() where MemberLoginID=:ApplyMemberLoginID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
		$Stmt->execute();
		$Stmt = null;

		/*
			$Sql = "select MemberID from Members where MemberLoginID=:ApplyMemberLoginID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$PointMemberID = $Row["MemberID"];

			$Sql = " insert into MemberLoginIPs ( ";
				$Sql .= " MemberID, ";
				$Sql .= " MemberLoginType, ";
				$Sql .= " MemberLoginIP, ";
				$Sql .= " RegDateTime ";
			$Sql .= " ) values ( ";
				$Sql .= " :PointMemberID, ";
				$Sql .= " 1, ";
				$Sql .= " :LoginIP, ";
				$Sql .= " now() ";
			$Sql .= " ) ";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':PointMemberID', $PointMemberID);
			$Stmt->bindParam(':LoginIP', $LoginIP);
			$Stmt->execute();
			$Stmt = null;
		*/
	}


	//echo $Sql;
}




$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;


$ResultValue = my_json_encode($ArrValue);
echo $ResultValue; 

function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>