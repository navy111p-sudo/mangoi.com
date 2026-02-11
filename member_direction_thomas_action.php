<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');

$ThomasSSO = isset($_REQUEST["sso"]) ? $_REQUEST["sso"] : ""; // 1180100101 같은 로그인 아이디 계정
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<!--<script src="js/common.js"></script>-->

</head>
<body>


<?
/*
ThomasSSO 체크할 필요없이
MemberLoginID 으로 검색하여
분기 설정
*/


// ID 값이 있는 체크
$Sql = "
		select 
				count(*) as TotalRowCount 
		from Members A 
		where A.MemberLoginID=:ThomasSSO 
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ThomasSSO', $ThomasSSO);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalRowCount = $Row["TotalRowCount"];

// ID 값이 있다면 
if($TotalRowCount) {
	// 값이 있다면 로그인 후 작업 로직 태울 것
	$Sql = "update Members set  LastLoginDateTime=now() where MemberLoginID=:ThomasSSO";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ThomasSSO', $ThomasSSO);
	$Stmt->execute();
	$Stmt = null;

	$Sql = "select MemberID from Members where MemberLoginID=:ThomasSSO";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ThomasSSO', $ThomasSSO);
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

	// 학생 포인트 적립
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


	setcookie("RememberMemberID","", time()-1, "/", ".".$DefaultDomain2);
	setcookie("LinkLoginMemberID", $ThomasSSO, 0, "/", ".".$DefaultDomain2);
	setcookie("LinkLoginAdminID", $ThomasSSO, 0, "/", ".".$DefaultDomain2);
	setcookie("LoginAdminID", $ThomasSSO, 0, "/", ".".$DefaultDomain2);
	setcookie("LoginMemberID", $ThomasSSO, 0, "/", ".".$DefaultDomain2);

	// 로그인 완료 되면 마이페이지로
	header("Location: /mypage/");
	exit;
} else {
?>
	<!-- 데이터베이스에 계정이 없다면 에러문구 보여주기 -->
	<div style="text-align: center; margin-top: 3%;">
		<div>
			계정이 올바르지않거나 잘못된 접근입니다.<br>
			확인 후에 다시 시도해주세요.<br>
			<span style="color: red; font-size: 11px; margin-top: 7px;">( 확인을 누르시면 이전 페이지로 이동됩니다 )</span>
		</div>
		<button class="btn" style="margin-top: 20px;" onclick="history.go(-1);">확인
	</div>

<?
}

?>

</body>
</html>

<?php
include_once('./includes/dbclose.php');
//header("Location: http://$DefaultDomain2/mypage_study_room.php");


?>