<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$err_num = 0;
$err_msg = "";

$ApplyMemberLoginID = isset($_REQUEST["ApplyMemberLoginID"]) ? $_REQUEST["ApplyMemberLoginID"] : "";
$ApplyMemberLoginPW = isset($_REQUEST["ApplyMemberLoginPW"]) ? $_REQUEST["ApplyMemberLoginPW"] : "";
$ApplyRememberID = isset($_REQUEST["ApplyRememberID"]) ? $_REQUEST["ApplyRememberID"] : "";


if ($ApplyRememberID=="on"){
	setcookie("RememberAdminID",$ApplyMemberLoginID,time()+(3600*24*30));
}else{
	setcookie("RememberAdminID","",time()-1);
}




$Sql = "select count(*) as TotalRowCount from Members where MemberState=1 and MemberLoginID=:ApplyMemberLoginID and MemberLevelID<=1";
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
		$err_num = 2;
		$err_msg = "비밀번호를 잘못 입력하셨습니다.";
	}else{
		
		$Sql = "select MemberLevelID from Members where MemberLoginID=:ApplyMemberLoginID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$MemberLevelID = $Row["MemberLevelID"];

		if ($MemberLevelID<=1) {
			//setcookie("LoginAdminID",$ApplyMemberLoginID);
			setcookie("LoginMemberID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);
			setcookie("LoginAdminID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);
		}
		
		$Sql = "update Members set LastLoginDateTime=now() where MemberLoginID='$ApplyMemberLoginID'";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt = null;
	}

}


if ($err_num != 0){
	include_once('./_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
</body>
<?php
	include_once('./_footer.php');
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: index.php"); 
	exit;
}
?>


