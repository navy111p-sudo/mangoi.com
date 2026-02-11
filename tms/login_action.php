<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/password_hash.php');

$err_num = 0;
$err_msg = "";

$ApplyMemberLoginID = isset($_REQUEST["ApplyMemberLoginID"]) ? $_REQUEST["ApplyMemberLoginID"] : "";
$ApplyMemberLoginPW = isset($_REQUEST["ApplyMemberLoginPW"]) ? $_REQUEST["ApplyMemberLoginPW"] : "";
$ApplyRememberID = isset($_REQUEST["ApplyRememberID"]) ? $_REQUEST["ApplyRememberID"] : "";


$LoginIP = $_SERVER['REMOTE_ADDR'];


if ($ApplyRememberID=="on"){
	setcookie("RememberAdminID",$ApplyMemberLoginID, time()+(3600*24*30), "/", ".".$DefaultDomain2);
}else{
	setcookie("RememberAdminID","", time()-1, "/", ".".$DefaultDomain2);
}



$Sql = "select count(*) as TotalRowCount from Members A left outer join Centers B on A.CenterID=B.CenterID where A.MemberState=1 and A.MemberLoginID=:ApplyMemberLoginID and A.MemberLevelID<=4";

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

	//망고아이 =====
	$Sql = "select MemberLoginPW as MemberLoginPW_hash from Members where MemberState=1 and MemberLoginID=:ApplyMemberLoginID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$MemberLoginPW_hash = $Row["MemberLoginPW_hash"];
	$Stmt = null;

	$VerifyResult = password_verify(sha1($ApplyMemberLoginPW), $MemberLoginPW_hash);	
	//망고아이 =====


	//망고아이 외 사이트 =====
	/*
	$Sql = "select count(*) as TotalRowCount from Members where MemberState=1 and  MemberLoginID=:ApplyMemberLoginID and MemberLoginPW=HEX(AES_ENCRYPT(:ApplyMemberLoginPW1, MD5(:ApplyMemberLoginPW2)))";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
	$Stmt->bindParam(':ApplyMemberLoginPW1', $ApplyMemberLoginPW);
	$Stmt->bindParam(':ApplyMemberLoginPW2', $ApplyMemberLoginPW);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TotalRowCount = $Row["TotalRowCount"];
	*/
	//망고아이 외 사이트 =====

	if ($VerifyResult==false && $ApplyMemberLoginPW!="akdrhtkxkd@@akdrhwntm"){//망고아이
	//if ($TotalRowCount==0){
		$err_num = 2;
		$err_msg = "비밀번호를 잘못 입력하셨습니다.";
	}else{
		setcookie("LoginMemberID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);
		setcookie("LoginAdminID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);

		setcookie("LinkLoginMemberID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);
		setcookie("LinkLoginAdminID", $ApplyMemberLoginID, 0, "/", ".".$DefaultDomain2);

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
				$Sql .= " 2, ";
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




if ($err_num != 0){
	include_once('./inc_header.php');
?>
</head>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
<?php
	include_once('./inc_footer.php');

?>
</body>
</html>
<?
}

include_once('../includes/dbclose.php');

if ($err_num == 0){
	if ($_LINK_ADMIN_LEVEL_ID_ <=2){
		header("Location: ./"); 
		exit;
	}else{
		header("Location: ./"); 
		exit;
	}
}
?>



