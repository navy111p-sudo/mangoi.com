<?php
include_once('../includes/dbopen.php');
include_once('./includes/common.php');
#----------------------------------------------------------------------------------------------#
$account_id = isset($_REQUEST["account_id"]) ? $_REQUEST["account_id"] : "";
$ArrValue   = "";
#----------------------------------------------------------------------------------------------#
$Sql = "select A.*, B.* 
			 from account_booksubconfig A 
		left join account_bookconfig B on A.AccBookConfigID=B.AccBookConfigID 
			where A.AccBookConfigID=:AccBookConfigID
		 order by B.AccBookConfigType asc, B.AccBookConfigID asc, A.AccBookSubConfigID asc"; 
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':AccBookConfigID', $account_id);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
#----------------------------------------------------------------------------------------------#
while($Row = $Stmt->fetch()) {
#----------------------------------------------------------------------------------------------#
       $ArrValue .= iif($ArrValue,"＾","") .  $Row["AccBookSubConfigID"] . "|" . $Row["AccBookSubConfigName"];
#----------------------------------------------------------------------------------------------#
}
#----------------------------------------------------------------------------------------------#
include_once('../includes/dbclose.php');

if ($ArrValue) {
      $ArrValue = "9＾" . $ArrValue; 
} else {
      $ArrValue = "1＾" . $account_id . " / " . $Sql; 
}
#----------------------------------------------------------------------------------------------#
echo $ArrValue;
?>