<?php
if (isset($_COOKIE["LoginAdminID"])){
	
	$Sql = "select MemberID, MemberName, MemberLevelID from Members where MemberLoginID=:LoginAdminID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':LoginAdminID', $_COOKIE["LoginAdminID"]);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$_ADMIN_ID_ = $Row["MemberID"];
	$_ADMIN_LOGIN_ID_ = $_COOKIE["LoginAdminID"];
	$_ADMIN_NAME_ = $Row["MemberName"];
	$_ADMIN_LEVEL_ID_ = $Row["MemberLevelID"];

}else{

	$_ADMIN_ID_ = '';
	$_ADMIN_LOGIN_ID_ = '';
	$_ADMIN_NAME_ = '';
	$_ADMIN_LEVEL_ID_ = 10;

	header("Location: login_form.php"); 
	exit;
}
?>
