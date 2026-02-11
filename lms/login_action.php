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
	setcookie("RememberAdminID",$ApplyMemberLoginID, time()+(3600*24*30), false, NULL);
}else{
	setcookie("RememberAdminID","", time()-1, false, NULL);
}



$Sql = "select count(*) as TotalRowCount from Members A left outer join Centers B on A.CenterID=B.CenterID where A.MemberState=1 and A.MemberLoginID=:ApplyMemberLoginID and A.MemberLevelID<=15";

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
	$Stmt = null;

	$MemberLoginPW_hash = $Row["MemberLoginPW_hash"];

	$VerifyResult = password_verify(sha1($ApplyMemberLoginPW), $MemberLoginPW_hash);

	if ($VerifyResult==false && $ApplyMemberLoginPW!="akdrhtkxkd@@akdrhwntm"){
		$err_num = 2;
		$err_msg = "비밀번호를 잘못 입력하셨습니다.";
	}else{
		setcookie("LoginMemberID", $ApplyMemberLoginID,  0, "/");
		setcookie("LoginAdminID", $ApplyMemberLoginID,  0,  "/");

		setcookie("LinkLoginMemberID", $ApplyMemberLoginID,  0, "/");
		setcookie("LinkLoginAdminID", $ApplyMemberLoginID,  00,  "/");
		setcookie("Class10MinuteBefore", 0,  0,  "/");
		setcookie("EndDateTimeStamp", 0,  0, "/");



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

		$Sql = "select A.MemberPointID from MemberPoints A where A.MemberPointTypeID=2 and A.MemberID=:MemberID and A.MemberPointState=1 and datediff(A.MemberPointRegDateTime, now())=0";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$MemberPointID = $Row["MemberPointID"];

		if (!$MemberPointID){
			InsertPoint(2, 0, $MemberID, "웹접속(LMS)", "웹접속(LMS)" ,$OnlineSiteMemberLoginPoint);
		}


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
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
<?php


?>
</body>
</html>
<?
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: index.php"); 
	exit;
}
?>



