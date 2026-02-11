<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/password_hash.php');


$err_num = 0;
$err_msg = "";

$ApplyMemberLoginID = isset($_REQUEST["ApplyMemberLoginID"]) ? $_REQUEST["ApplyMemberLoginID"] : "";
$ApplyMemberLoginPW = isset($_REQUEST["ApplyMemberLoginPW"]) ? $_REQUEST["ApplyMemberLoginPW"] : "";
$ApplyRememberID = isset($_REQUEST["ApplyRememberID"]) ? $_REQUEST["ApplyRememberID"] : "";
$RedirectUrl = isset($_REQUEST["RedirectUrl"]) ? $_REQUEST["RedirectUrl"] : "";


$LoginIP = $_SERVER['REMOTE_ADDR'];

if ($ApplyRememberID=="on"){
	setcookie("RememberMemberID",$ApplyMemberLoginID, time()+(3600*24*30), "/", ".".$DefaultDomain2);
}else{
	setcookie("RememberMemberID","", time()-1, "/", ".".$DefaultDomain2);
}

// 카카오 등 SNS 계정으로 로그인하면 Center 값이 비어 아래에 소속 체크가 문제가 생김
// 이를 방지하고자 로그인 타입을 체크
$Sql = "select MemberNickName, MemberLoginType, CenterID from Members where MemberLoginID=:ApplyMemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$MemberLoginType = $Row["MemberLoginType"];
$CenterID = $Row["CenterID"];
$MemberNickName = $Row["MemberNickName"];


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
	$err_num = 1;
	$err_msg = "아이디를 잘못 입력하셨습니다.";
}else{

	$Sql = "select MemberLoginPW as MemberLoginPW_hash from Members where MemberState=1 and MemberLoginID=:ApplyMemberLoginID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$MemberLoginPW_hash = $Row["MemberLoginPW_hash"];

	$VerifyResult = password_verify(sha1($ApplyMemberLoginPW), $MemberLoginPW_hash);
	$Stmt = null;
	
	if ($VerifyResult==false){
		$err_num = 2;
		$err_msg = "비밀번호를 잘못 입력하셨습니다.";
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


		$Sql = "select MemberID from Members where MemberLoginID=:ApplyMemberLoginID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
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


	//echo $Sql;
}


if ($err_num != 0){
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<?if ($DomainSiteID==5){?>
<title>(주)englishtell</title>
<?}else{?>
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="../uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="../uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<?}?>
</head>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
<?
include_once('../includes/common_analytics.php');
?>
</body>
</html>
<?php
}

include_once('../includes/dbclose.php');

if ($err_num == 0){
	if ($RedirectUrl!=""){
		header("Location: $RedirectUrl"); 
		exit;
	} else {
		header("Location: mypage_study_room.php");
		exit;
	}
}

?>