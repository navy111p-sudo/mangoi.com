<?
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/member_check_app.php');


$ErrNum = 0;
$ErrMsg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$LocalLinkCenterID = isset($_REQUEST["LocalLinkCenterID"]) ? $_REQUEST["LocalLinkCenterID"] : "";
$ServerPath = $AppDomain.$AppPath."/";
$CommonShNewClassCode = "";

$MyMemberID = $_LINK_MEMBER_ID_;

if($LocalLinkCenterID) {
	$LocalLinkCenterID = $LocalLinkCenterID;
} else {
	$LocalLinkCenterID = $_LINK_MEMBER_CENTER_ID_;
}
// 신 구 버전 데이터 가져오기
$Sql = "
	select 
		A.OnlineSiteShVersion,
		B.CenterPerShAllow,
		B.CenterPerShVersion
	from OnlineSites A
		inner join Centers B on A.OnlineSiteID=B.OnlineSiteID
	where
		A.FranchiseID=1 and B.CenterID=:LocalLinkCenterID
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':LocalLinkCenterID', $LocalLinkCenterID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$OnlineSiteShVersion = $Row["OnlineSiteShVersion"];
$CenterPerShAllow = $Row["CenterPerShAllow"];
$CenterPerShVersion = $Row["CenterPerShVersion"];


// 대리점 설정에 따른 새하버전 선택 로직
//if($CenterPerShAllow==2) {
//	$OnlineSiteShVersion = $CenterPerShVersion;
//}
$OnlineSiteShVersion = 1;
if($OnlineSiteShVersion==1) {
	// 신규버전
	$CompanyCode = "000002";
	//$RoomCode = $CompanyCode .$MyMemberID. Date("HisuB");
	$RoomCode = $CompanyCode . Date("ymdHis") .substr("000000000".$MyMemberID, -9);

	$Sql = "
			select 
				A.CommonShNewClassCode,
				A.StartYear,
				A.StartMonth,
				A.StartDay,
				A.StartHour,
				A.StartMinute,
				A.TeacherID
			from Classes A 
			where ClassID=$ClassID 
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$CommonShNewClassCode = $Row["CommonShNewClassCode"];
	$StartYear = $Row["StartYear"];
	$StartMonth = $Row["StartMonth"];
	$StartDay = $Row["StartDay"];
	$StartHour = $Row["StartHour"];
	$StartMinute = $Row["StartMinute"];
	$TeacherID = $Row["TeacherID"];


	if($CommonShNewClassCode==null) {

		
		$Sql = "
				select 
					A.CommonShNewClassCode as OldCommonShNewClassCode
				from Classes A 
				where 
					A.StartYear=$StartYear
					and A.StartMonth=$StartMonth
					and A.StartDay=$StartDay
					and A.StartHour=$StartHour
					and A.StartMinute=$StartMinute
					and A.TeacherID=$TeacherID
					and A.CommonShNewClassCode<>'' 
					and A.CommonShNewClassCode is not null
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$OldCommonShNewClassCode = $Row["OldCommonShNewClassCode"];
		
		if ($OldCommonShNewClassCode!=""){ //내 클래스는 비어 있는데.. 같은 시간 같은 강사의 다른 클래스는 설정되어 있을때(기존 그룹에 갑자기 끼어 들었을때) 내 수업만 업데이트 해준다.
			
			$RoomCode = $OldCommonShNewClassCode;
		
			$Sql = "
					update Classes 
						set CommonShNewClassCode='$RoomCode'
					where
						ClassID=$ClassID
			";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt = null;
			$CommonShNewClassCode = $RoomCode;
		
		
		}else{

			$Sql = "
					update Classes 
						set CommonShNewClassCode='$RoomCode'
					where
						StartYear=$StartYear
						and StartMonth=$StartMonth
						and StartDay=$StartDay
						and StartHour=$StartHour
						and StartMinute=$StartMinute
						and TeacherID=$TeacherID
			";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt = null;
			$CommonShNewClassCode = $RoomCode;

		}
	}

	
}


$Sql = "select 
			AssmtStudentSelfScoreID
		from AssmtStudentSelfScores A 
		where 
			A.ClassID=:ClassID
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$AssmtStudentSelfScoreID = $Row["AssmtStudentSelfScoreID"];

if (!$AssmtStudentSelfScoreID){
	$AssmtStudentSelfScoreID=0;
}


// 위 코드는 점수를 내기전에는 쌓이지않음
// 처음 눌렀을 때, 멤버계정을 가져오려면 별개의 SQL 필요
$Sql = "select 
			A.MemberID,
			B.MemberInviteID
		from Classes A 
			inner join Members B on A.MemberID=B.MemberID 
		where 
			A.ClassID=:ClassID
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberID = $Row["MemberID"];
$MemberInviteID = $Row["MemberInviteID"];

InsertNewTypePoint(1, 0, $MemberID, $ClassID);


// 친구 초대로 유입된 학생이라면
if($MemberInviteID) {
	$InviteInfo = $MemberID."|".$MemberInviteID;
	InsertNewTypePoint(6, 0, $MemberInviteID, $ClassID);
}

$ArrValue["CommonShNewClassCode"] = $CommonShNewClassCode;
$ArrValue["OnlineSiteShVersion"] = $OnlineSiteShVersion;
$ArrValue["AssmtStudentSelfScoreID"] = $AssmtStudentSelfScoreID;
$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;

$ResultValue = my_json_encode($ArrValue);


echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');


?>
