<?php
include_once('../includes/dbopen.php');

$NewCode = isset($_REQUEST["NewCode"]) ? $_REQUEST["NewCode"] : "";

$NewCode = trim($NewCode);
$Sql = "select count(*) as ExistCount from Recents where RecentCode=:NewCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':NewCode', $NewCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ExistCount = $Row["ExistCount"];



if ($ExistCount==0){
	$QueryResult_check_code = "1";
}else{
	$QueryResult_check_code = "0";
}

include_once('../includes/dbclose.php');
echo $QueryResult_check_code;
?>