<?php
include_once('../includes/dbopen.php');

$NewID = isset($_REQUEST["NewID"]) ? $_REQUEST["NewID"] : "";

$NewID = trim($NewID);
$Sql = "select count(*) as ExistCount from View_Members where MemberLoginID=:NewID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':NewID', $NewID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ExistCount = $Row["ExistCount"];

if ($ExistCount==0){
	$QueryResult_check_id = "1";
}else{
	$QueryResult_check_id = "0";
}

include_once('../includes/dbclose.php');
echo $QueryResult_check_id;
?>