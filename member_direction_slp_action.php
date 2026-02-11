<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/password_hash.php');

/*
2 가지 분류

학생 / 관리자
본사 / 대리점

*/
$sso = isset($_GET['sso']) ? $_GET['sso'] : "";
$str = isset($_GET['str']) ? $_GET['str'] : "";
$Param = isset($_GET['Param']) ? $_GET['Param'] : "";


$LoginIP = $_SERVER['REMOTE_ADDR'];
//$ssoAdmin = "*실행*|woX%2F65u3izCq3iZAps%2BQkcyP9m5LbVoKNTn2BpcpmRw%3D|SLPSSO_dopo1203|mangoicom_a|도도독포|dopo|02-333-4444|--||||choi@sogang.ac.kr|000||서울 마포구|402|||admin";
//$ssoStudent = "*실행*|woX%2F65u3izCq3iZAps%2BQkcyP9m5LbVoKNTn2BpcpmRw%3D|SLPSSO_dopo1203|mangoicom_a|도도독포|dopo|02-333-4444|--||||choi@sogang.ac.kr|000||서울 마포구|402||";



function encrypt($arr) {
	$key = 'f9a90bfa9e8d1d9965fecc00q2e6786cf59f31x1';
	$key_256 = substr($key, 0, 256/8);
	$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
	$encrypt_arr = openssl_encrypt($arr, 'AES-256-CBC', $key_256, 0, $iv);
	return $encrypt_arr;
}

function decrypt($encrypt_arr) {
	$encrypt_arr = preg_replace('/\ /', '+', $encrypt_arr);
	$key = 'f9a90bfa9e8d1d9965fecc00q2e6786cf59f31x1';
	$key_256 = substr($key, 0, 256/8);
	$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
	$decrypt_arr = openssl_decrypt($encrypt_arr, 'AES-256-CBC', $key_256, 0, $iv);
	$decrypt_arr = explode('|', $decrypt_arr);
	return $decrypt_arr;
}

/* 암복호화를 배열 방식 했을 때, 사용했던 함수이나 현잰 사용 안함 ( 추후 사용 가능성 있을 수도 있어 남겨놓음 ) */
function decrypt2($encrypt_arr) {
	//$encrypt_arr = str_replace(' ', '+', $encrypt_arr);
	//$encrypt_arr = urlencode($encrypt_arr);
	$encrypt_arr = preg_replace('/\ /', '+', $encrypt_arr);
	$decrypt_arr_arr = [];
	$key = 'f9a90bfa9e8d1d9965fecc00q2e6786cf59f31x1';
	$key_256 = substr($key, 0, 256/8);
	$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
	$decrypt_arr = openssl_decrypt($encrypt_arr, 'AES-256-CBC', $key_256, 0, $iv);
	$decrypt_arr = explode('|', $decrypt_arr);

	foreach($decrypt_arr as $decrypt_arr_index => $decrypt_arr_value) {
		$decrypt_arr[$decrypt_arr_index] = $decrypt_arr_value;
	}
	return $decrypt_arr;
}

// 학생은 sso 관리자는 str 파라미터로 넘어옴에 따른 분기
if($str!="") {
	$arr = decrypt2($str);
} else if($sso!="") {
	$arr = decrypt2($sso);
} else if($Param!="") {
	$arr = decrypt2($Param);
}

// ========================================================= //

/* 
SLP:본사 = 에듀비젼:대표지사 와 같은 개념
SLP:학당 = 에듀비젼:센터 와 같은 개념

1. 본사, 그외 계정인지 확인
2. 본사라면 본사장(대표지사장) 존재여부 확인
3. 존재한다면 해당 본사 아이디 추출
4. 없다면 본사 및 본사장 생성 및 본사 아이디 추출

5. 그외 계정이라면 ( 학당, 학생들 )
6. 학당장을 검색
7. 없다면 학당 및 학당장 생성 후 학당 아이디 추출
8. 있다면 학당 아이디 추출

9. 계정 존재 여부 확인
10. 없다면 계정 생성 
11. 있다면 로그인 로그 업데이트
12. 로그인
*/

// 계정을 검색하여 디비안에 계정 여부 확인
$SLPMemberLoginID = $arr[2]; //slp_28dkdk
$SLPCenterCode = $arr[3]; //slp_28
$SLPMemberName = $arr[4];
$SLPMemberNickName = $arr[5];
$SLPMemberCellPhone = $arr[6]; // 일반전화
$SLPMemberPhone1 = $arr[7]; // 이동전화
$SLPMemberLoginPW = $arr[8];
$SLPMemberSex = $arr[9];
$SLPMemberBirthday = $arr[10];
$SLPMemberEmail = $arr[11];
$SLPMemberZip = $arr[12];
$SLPMemberZip .= $arr[13];
$SLPMemberAddr1 = $arr[14];
$SLPMemberAddr2 = $arr[15];
$IsMemberAdmin = isset($arr[18]);
if($IsMemberAdmin) { // admin 필드가 있다면 값 대입
	$SLPMemberAdmin = $arr[18];
}

if ($SLPCenterCode=="mangoicom"){
	$IsMemberLevelID = 1;
} else {
	$IsMemberLevelID = 0;
}
//$IsMemberLevelID = preg_match('/^mangoicom\d*/', $SLPCenterCode);  // 0 이라면 본사 나머지는 센터(학당)

// ======================================= 초기값 대입
if($SLPMemberPhone1=="") {
	$SLPMemberPhone1 = "010--";
}
$SLPMemberPhone2 = "010--";
$SLPMemberPhone3 = "010--";

if($SLPMemberEmail=="") {
	$SLPMemberEmail = "abc@abc.com";
}
if($SLPMemberSex=="M") {
	$SLPMemberSex = 1;
} else if($SLPMemberSex=="F") {
	$SLPMemberSex = 2;
} else {
	$SLPMemberSex = 1;
}
if($SLPMemberLoginPW=="") {
	$SLPMemberLoginPW_hash = "n/a";
} else {
	$SLPMemberLoginPW_hash = password_hash(sha1($SLPMemberLoginPW), PASSWORD_DEFAULT);
}

$LoginIP = $_SERVER['REMOTE_ADDR'];
$TempCenterID = "";
$TempBranchID = "";
// ======================================= 초기값 대입 // 

// ===================================== 학당 과 본사 중 본사계정이라면
if($IsMemberLevelID==1) {

	$TempBranchGroupID = 18;

} else { // 본사계정이 아니라면 ... ( 학당 과 학생이 아래 로직을 타게 됨 )

	$Sql = "select ifnull(Max(CenterOrder),0) as CenterOrder from Centers";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$CenterOrder = $Row["CenterOrder"]+1;

	$Sql = " select  count(*) as TotalRowCount 
	from Members A 
	inner join Centers B on A.CenterID=B.CenterID 
	where A.MemberLoginID=:CenterCode 
	and A.MemberState=1 
	and A.MemberLevelID=12
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterCode', $SLPCenterCode);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalRowCount = $Row["TotalRowCount"];
	$Stmt = null;

	// CenterID 가 없다면 생성, 있다면 그냥 이용
	if($TotalRowCount==0) {

		$Sql = "select count(*) as TotalRowCount from Members where MemberLoginID=:MemberLoginID ";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberLoginID', $SLPCenterCode);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$TotalRowCount = $Row["TotalRowCount"];

		if ($TotalRowCount > 0){
			$err_num = 1;
			echo "<script>alert('Error : ID is a duplicate!!');history.go(-1);</script>";
		}else{
			// Branch slp 113, onlinesite 1 본사, manager 1 본사
			$CenterStudyEndDate = date("Y", strtotime("+5 Years"))."-".date("m")."-".substr("0".date("t", strtotime(date("Y-m-d"))),-2); // 대리점 수강 종료일 초기화
			$Sql = " insert into Centers 
						( BranchID, OnlineSiteID, ManagerID, CenterPayType, CenterStudyEndDate, CenterName, CenterManagerName, CenterPhone1, CenterPhone2, CenterPhone3, CenterEmail, CenterRegDateTime, CenterModiDateTime, CenterOrder ) 
						values 
						( 113, 1, 1, 2, :CenterStudyEndDate, 'SLP 신규 학당(수정필수)', '수정필수', 
						HEX(AES_ENCRYPT('010--', :EncryptionKey)), 
						HEX(AES_ENCRYPT('010--', :EncryptionKey)), 
						HEX(AES_ENCRYPT('010--', :EncryptionKey)), 
						HEX(AES_ENCRYPT('abc@abc.com', :EncryptionKey)), 
						now(),
						now(),
						$CenterOrder ) ";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
			$Stmt->bindParam(':CenterStudyEndDate', $CenterStudyEndDate);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Stmt = null;
			$TempCenterID = $DbConn->lastInsertId();

			//Members 
			$MemberLevelID = 12;//센터장(대리점장)
			$MemberLoginNewPW_hash = password_hash(sha1('12345'), PASSWORD_DEFAULT);

			$Sql = " insert into Members ( ";
			$Sql .= " CenterID, ";
			$Sql .= " MemberLevelID, ";
			$Sql .= " MemberLoginID, ";
			$Sql .= " MemberLoginPW, ";
			$Sql .= " MemberName, ";
			$Sql .= " MemberNickName, ";
			$Sql .= " MemberPhone1, ";
			$Sql .= " MemberEmail, ";
			$Sql .= " MemberView, ";
			$Sql .= " MemberState, ";
			$Sql .= " MemberRegDateTime, ";
			$Sql .= " MemberModiDateTime ";

			$Sql .= " ) values ( ";

			$Sql .= " :CenterID, ";
			$Sql .= " :MemberLevelID, ";
			$Sql .= " :MemberLoginID, ";
			$Sql .= " :MemberLoginNewPW_hash, ";
			$Sql .= " '대리점명 ( 수정필수 )', ";
			$Sql .= " '수정필수', ";
			$Sql .= " HEX(AES_ENCRYPT('010--', :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT('abc@abc.com', :EncryptionKey)), ";
			$Sql .= " 1, ";
			$Sql .= " 1, ";
			$Sql .= " now(), ";
			$Sql .= " now() ";

			$Sql .= " ) ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':CenterID', $TempCenterID);
			$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
			$Stmt->bindParam(':MemberLoginID', $SLPCenterCode);
			$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
			$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
			$Stmt->execute();
			$Stmt = null;


			$MasterMessageType = 1;//대리점 신규 등록
			$MasterMessageText = 'SLP 신규 학당(수정필수)' . "(수정필수) 신규등록 ";
			InsertMasterMessage($MasterMessageType, $MasterMessageText);
		}
	} else {                                     
		$Sql = " select  A.CenterID 
				from Members A 
				inner join Centers B on A.CenterID=B.CenterID 
				where A.MemberLoginID=:SLPCenterCode 
				and A.MemberState=1 
				and A.MemberLevelID=12 ";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':SLPCenterCode', $SLPCenterCode);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$TempCenterID = $Row["CenterID"];
	}
}

// 레벨 선택을 위한 분기
if($IsMemberAdmin==false) {
	// admin 영역이 비어있다면 학생
	$TempMemberLevelID = "19";
	//$TempMemberLevelID = "19";
} else { // admin 영역이 있다면 관리자
	// CenterCode 에 본사를 지칭하는 구문이 있는지 체크
	if($IsMemberLevelID==1) { // true 라면 본사
	  //$MinMemberLevelID = "6";
	  $TempMemberLevelID = "7";
	} else {
	  // 본사가 아니라면 센터소속
	  //$MinMemberLevelID = "12";
	  $TempMemberLevelID = "13";
	}
}






	/*
	상단의 코드는
	admin 이냐 가 아닌 본사냐 학당이냐를 구분
	1. 본사 또는 학당 확인
	2. 본사 또는 학당 존재여부  확인
	3. 확인 후 없다면 본사 또는 학당 생성 & 관리자 생성
	4. 아래코드의 로직대로, 이용자 계정 생성

	*/
	// ================= 계정 확인 후 생성
	$Sql = "select 
		  count(*) as TotalRowCount 
		from Members A 
		where 
		  A.MemberState=1 
		  and A.MemberLoginID=:SLPMemberLoginID 
		";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SLPMemberLoginID', $SLPMemberLoginID);
	//$Stmt->bindParam(':TempMemberLevelID', $TempMemberLevelID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$TotalRowCount = $Row["TotalRowCount"];
	$Stmt = null;

	// 데이터 기입
	if($TotalRowCount>0) {
		// 값이 있다면 로그인 후 작업 로직 태울 것
		$Sql = "update Members set  LastLoginDateTime=now() where MemberLoginID=:SLPMemberLoginID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':SLPMemberLoginID', $SLPMemberLoginID);
		$Stmt->execute();
		$Stmt = null;

		$Sql = "select MemberID from Members where MemberLoginID=:SLPMemberLoginID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':SLPMemberLoginID', $SLPMemberLoginID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$MemberID = $Row["MemberID"];

		$Sql = " insert into MemberLoginIPs ( ";
		$Sql .= " MemberID, ";
		$Sql .= " MemberLoginType, ";
		$Sql .= " MemberLoginIP, ";
		$Sql .= " RegDateTime ";
		$Sql .= " ) values ( ";
		$Sql .= " :MemberID, ";
		$Sql .= " 1, ";
		$Sql .= " :LoginIP, ";
		$Sql .= " now() ";
		$Sql .= " ) ";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->bindParam(':LoginIP', $LoginIP);
		$Stmt->execute();
		$Stmt = null;

		// 학생일 경우에만 포인트 적립
		if($IsMemberAdmin==false) {
			$Sql = "select A.MemberPointID from MemberPoints A where A.MemberPointTypeID=2 and A.MemberID=:MemberID and A.MemberPointState=1 and datediff(A.MemberPointRegDateTime, now())=0";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':MemberID', $MemberID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;
			$MemberPointID = $Row["MemberPointID"];

			if (!$MemberPointID){
				InsertPoint(2, 0, $MemberID, "로그인(웹)", "로그인(웹)" ,$OnlineSiteMemberLoginPoint);
			}
		}

	} else {

		// 값이 없다면 회원가입 로직 태울 것
		$Sql = " insert into Members ( ";
		//$Sql .= " CampusID, ";
		if($TempMemberLevelID==7) {
			$Sql .= " BranchGroupID, ";
		} else {
			$Sql .= " CenterID, ";
		}
		$Sql .= " MemberLevelID, ";
		$Sql .= " MemberLoginID, ";
		$Sql .= " MemberLoginPW, ";
		$Sql .= " MemberName, ";
		$Sql .= " MemberNickName, ";
		$Sql .= " MemberParentName, ";
		$Sql .= " MemberSex, ";
		if ( $SLPMemberBirthday!="") {
		  $Sql .= " MemberBirthday, ";
		}
		$Sql .= " MemberPhone1, ";
		$Sql .= " MemberPhone1Agree, ";
		$Sql .= " MemberEmail, ";
		$Sql .= " MemberEmailAgree, ";
		$Sql .= " MemberZip, ";
		$Sql .= " MemberAddr1, ";
		$Sql .= " MemberAddr2, ";
		$Sql .= " MemberRegDateTime, ";
		$Sql .= " MemberModiDateTime, ";
		$Sql .= " MemberState ";
		$Sql .= " ) values ( ";
		//$Sql .= " :SelectedCampusID, ";
		if($TempMemberLevelID==7) {
			$Sql .= " :TempBranchGroupID, ";
		} else {
			$Sql .= " :TempCenterID, ";
		}
		$Sql .= " :TempMemberLevelID, ";
		$Sql .= " :MemberLoginID, ";
		$Sql .= " :MemberLoginPW_hash, ";
		$Sql .= " :MemberName, ";
		$Sql .= " :MemberNickName, ";
		$Sql .= " :MemberParentName, ";
		$Sql .= " :MemberSex, ";
		if ( $SLPMemberBirthday!="") {
		  $Sql .= " :MemberBirthday, ";
		}
		$Sql .= " HEX(AES_ENCRYPT(:MemberPhone1, :EncryptionKey)), ";
		$Sql .= " 1, ";
		$Sql .= " HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
		$Sql .= " 1, ";
		$Sql .= " :MemberZip, ";
		$Sql .= " :MemberAddr1, ";
		$Sql .= " :MemberAddr2, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " 1 ";
	  $Sql .= " )";

		$Stmt = $DbConn->prepare($Sql);
		//$Stmt->bindParam(':SelectedCampusID', $SelectedCampusID);
		if($TempMemberLevelID==7) {
			$Stmt->bindParam(':TempBranchGroupID', $TempBranchGroupID);
		} else {
			$Stmt->bindParam(':TempCenterID', $TempCenterID);
		}
		$Stmt->bindParam(':TempMemberLevelID', $TempMemberLevelID);
		$Stmt->bindParam(':MemberLoginID', $SLPMemberLoginID);
		$Stmt->bindParam(':MemberLoginPW_hash', $SLPMemberLoginPW_hash);
		$Stmt->bindParam(':MemberName', $SLPMemberName);
		$Stmt->bindParam(':MemberNickName', $SLPMemberNickName);
		$Stmt->bindParam(':MemberParentName', $MemberParentName);
		$Stmt->bindParam(':MemberSex', $SLPMemberSex);
		if ( $SLPMemberBirthday!="") {
		  $Stmt->bindParam(':MemberBirthday', $SLPMemberBirthday);
		}
		$Stmt->bindParam(':MemberPhone1', $SLPMemberPhone1);
		$Stmt->bindParam(':MemberPhone3', $SLPMemberPhone3);
		$Stmt->bindParam(':MemberEmail', $SLPMemberEmail);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->bindParam(':MemberZip', $SLPMemberZip);
		$Stmt->bindParam(':MemberAddr1', $SLPMemberAddr1);
		$Stmt->bindParam(':MemberAddr2', $SLPMemberAddr2);
		$Stmt->execute();
		$MemberID = $DbConn->lastInsertId();
		$Stmt = null;

		$NewData = 1;
		$AlertMsg = "등록 하였습니다.";

		if($TempMemberLevelID==19) {
			InsertPoint(1, 0, $MemberID, "회원가입(웹)", "회원가입(웹)" ,$OnlineSiteMemberRegPoint);
		}
	}




if($IsMemberAdmin==false) {
  setcookie("RememberMemberID","", time()-1, "/", ".".$DefaultDomain2);
  setcookie("LoginMemberID", $SLPMemberLoginID, 0, "/", ".".$DefaultDomain2);
  setcookie("LinkLoginMemberID", $SLPMemberLoginID, 0, "/", ".".$DefaultDomain2);
} else {
  setcookie("RememberMemberID","", time()-1, "/", ".".$DefaultDomain2);
  setcookie("LinkLoginMemberID", $SLPMemberLoginID, 0, "/", ".".$DefaultDomain2);
  setcookie("LinkLoginAdminID", $SLPMemberLoginID, 0, "/", ".".$DefaultDomain2);
  setcookie("LoginAdminID", $SLPMemberLoginID, 0, "/", ".".$DefaultDomain2);
  setcookie("LoginMemberID", $SLPMemberLoginID, 0, "/", ".".$DefaultDomain2);
}

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>


<?php
include_once('./includes/common_analytics.php');
?>
</body>
</html>
<?php
include_once('./includes/dbclose.php');
//header("Location: http://$DefaultDomain2/mypage_study_room.php");


if($IsMemberAdmin==false) {
	header("Location: mypage.php");
} else {
	header("Location: lms/");
}

?>
