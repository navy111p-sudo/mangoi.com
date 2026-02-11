<?
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/member_check.php');
include_once('../includes/password_hash.php');


$err_num = 0;
$err_msg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";
// 암호와 이동할 파일명칭, 그리고 계정명
$LocalMemberID = isset($_REQUEST["LocalMemberID"]) ? $_REQUEST["LocalMemberID"] : "";
$MypageTeacherPasswordValue_dash = isset($_REQUEST["MypageTeacherPasswordValue_dash"]) ? $_REQUEST["MypageTeacherPasswordValue_dash"] : "";
$MypageTeacherPasswordValue_device = isset($_REQUEST["MypageTeacherPasswordValue_device"]) ? $_REQUEST["MypageTeacherPasswordValue_device"] : "";
$LoginIP = $_SERVER['REMOTE_ADDR'];


if($MypageTeacherPasswordValue_dash) {
	$MypageTeacherPasswordValue = $MypageTeacherPasswordValue_dash;
} else {
	$MypageTeacherPasswordValue = $MypageTeacherPasswordValue_device;
}


$Sql = "select count(*) as TotalRowCount from Members where MemberState=1 and MemberLevelID<=19 and MemberID=:LocalMemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':LocalMemberID', $LocalMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];



if ($TotalRowCount==0){
	$MemberLoginPW = "";
	$err_num = 1;
	$err_msg = "아이디를 잘못 입력하셨습니다.";
}else{

	$Sql = "select MemberLoginPW as MemberLoginPW_hash from Members where MemberState=1 and MemberID=:LocalMemberID";
	// $Sql = "select count(*) as TotalRowCount from Members where MemberState=1 and  MemberID=:LocalMemberID and MemberLoginPW=HEX(AES_ENCRYPT(:MypageTeacherPasswordValue1, MD5(:MypageTeacherPasswordValue2)))";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':LocalMemberID', $LocalMemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$MemberLoginPW_hash = $Row["MemberLoginPW_hash"];

	$VerifyResult = password_verify(sha1($MypageTeacherPasswordValue), $MemberLoginPW_hash);
	//$TotalRowCount = $Row["TotalRowCount"];
	
	if ($VerifyResult==false){
		$err_num = 2;
		$err_msg = "비밀번호를 잘못 입력하셨습니다.";
	}else{
		$Sql = "update Members set LastLoginDateTime=now() where MemberID=:LocalMemberID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':LocalMemberID', $LocalMemberID);
		$Stmt->execute();
		$Stmt = null;

		$Sql = " insert into MemberLoginIPs ( ";
			$Sql .= " MemberID, ";
			$Sql .= " MemberLoginType, ";
			$Sql .= " MemberLoginIP, ";
			$Sql .= " RegDateTime ";
		$Sql .= " ) values ( ";
			$Sql .= " :LocalMemberID, ";
			$Sql .= " 3, ";
			$Sql .= " :LoginIP, ";
			$Sql .= " now() ";
		$Sql .= " ) ";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':LocalMemberID', $LocalMemberID);
		$Stmt->bindParam(':LoginIP', $LoginIP);
		$Stmt->execute();
		$Stmt = null;
	}
}

$ArrValue["err_msg"] = $err_msg;

$ResultValue = my_json_encode($ArrValue);

echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>
