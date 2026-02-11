<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

// 암호와 이동할 파일명칭, 그리고 계정명
$Password = isset($_REQUEST["Password"]) ? $_REQUEST["Password"] : "";
$Section = isset($_REQUEST["Section"]) ? $_REQUEST["Section"] : "";
$LinkLoginMemberID = isset($_REQUEST["LinkLoginMemberID"]) ? $_REQUEST["LinkLoginMemberID"] : "";

$LoginIP = $_SERVER['REMOTE_ADDR'];

$Sql = "select count(*) as TotalRowCount from Members where MemberState=1 and MemberLevelID<=19 and MemberLoginID=:LinkLoginMemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':LinkLoginMemberID', $LinkLoginMemberID);
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

	// $Sql = "select count(*) as TotalRowCount from Members where MemberState=1 and  MemberLoginID=:LinkLoginMemberID and MemberLoginPW=HEX(AES_ENCRYPT(:Password1, MD5(:Password2)))";
	$Sql = "select MemberID, MemberLoginPW as MemberLoginPW_hash from Members where MemberState=1 and MemberLoginID=:LinkLoginMemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':LinkLoginMemberID', $LinkLoginMemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$MemberLoginPW_hash = $Row["MemberLoginPW_hash"];
	$MemberID = $Row["MemberID"];

	$VerifyResult = password_verify(sha1($Password), $MemberLoginPW_hash);
	$Stmt = null;
	
	if ($VerifyResult==false){
		$err_num = 2;
		$err_msg = "비밀번호를 잘못 입력하셨습니다.";
	}else{		
		$Sql = "update Members set LastLoginDateTime=now() where MemberLoginID=:LinkLoginMemberID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':LinkLoginMemberID', $LinkLoginMemberID);
		$Stmt->execute();
		$Stmt = null;


		$Sql = "select MemberID from Members where MemberLoginID=:LinkLoginMemberID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':LinkLoginMemberID', $LinkLoginMemberID);
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

	}

	//echo $Sql;
}


if ($err_num != 0){
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
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>
<?php
}

include_once('./includes/dbclose.php');


if ($err_num == 0){
	if ($Section!=""){
		header("Location: mypage_teacher_$Section.php"); 
		exit;
	}else{
		header("Location: index.php");
		exit;
	}
}

?>