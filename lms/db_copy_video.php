<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<body>
<?
$Sql = "
		select 
			A.*
		from BookVideos A 
			where BookID=9
		order by A.BookVideoOrder asc 
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListCount = 1;
while($Row = $Stmt->fetch()) {

	$BookID = $Row["BookID"];
	$BookVideoName = $Row["BookVideoName"];
	$BookVideoMemo = $Row["BookVideoMemo"];
	$BookVideoType = $Row["BookVideoType"];
	$BookVideoType2 = $Row["BookVideoType2"];
	$BookVideoCode = $Row["BookVideoCode"];
	$BookVideoCode2 = $Row["BookVideoCode2"];
	$BookVideoView = $Row["BookVideoView"];
	$BookVideoState = $Row["BookVideoState"];


	$BookVideoName = str_replace("MES", "BTS", $BookVideoName);
	$BookVideoMemo = str_replace("MES", "BTS", $BookVideoMemo);

		$Sql2 = "select ifnull(Max(BookVideoOrder),0) as BookVideoOrder from BookVideos";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		$Row2 = $Stmt2->fetch();
		$Stmt2 = null;
		
		$BookVideoOrder = $Row2["BookVideoOrder"]+1;

		$InsertBookID = 13;

		$Sql2 = " insert into BookVideos ( ";
			$Sql2 .= " BookID, ";
			$Sql2 .= " BookVideoName, ";
			$Sql2 .= " BookVideoMemo, ";
			$Sql2 .= " BookVideoType, ";
			$Sql2 .= " BookVideoType2, ";
			$Sql2 .= " BookVideoCode, ";
			$Sql2 .= " BookVideoCode2, ";
			$Sql2 .= " BookVideoRegDateTime, ";
			$Sql2 .= " BookVideoModiDateTime, ";
			$Sql2 .= " BookVideoOrder, ";
			$Sql2 .= " BookVideoView, ";
			$Sql2 .= " BookVideoState ";
		$Sql2 .= " ) values ( ";
			$Sql2 .= " :BookID, ";
			$Sql2 .= " :BookVideoName, ";
			$Sql2 .= " :BookVideoMemo, ";
			$Sql2 .= " :BookVideoType, ";
			$Sql2 .= " :BookVideoType2, ";
			$Sql2 .= " :BookVideoCode, ";
			$Sql2 .= " :BookVideoCode2, ";
			$Sql2 .= " now(), ";
			$Sql2 .= " now(), ";
			$Sql2 .= " :BookVideoOrder, ";
			$Sql2 .= " :BookVideoView, ";
			$Sql2 .= " :BookVideoState ";
		$Sql2 .= " ) ";

		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':BookID', $InsertBookID);
		$Stmt2->bindParam(':BookVideoName', $BookVideoName);
		$Stmt2->bindParam(':BookVideoMemo', $BookVideoMemo);
		$Stmt2->bindParam(':BookVideoType', $BookVideoType);
		$Stmt2->bindParam(':BookVideoType2', $BookVideoType2);
		$Stmt2->bindParam(':BookVideoCode', $BookVideoCode);
		$Stmt2->bindParam(':BookVideoCode2', $BookVideoCode2);
		$Stmt2->bindParam(':BookVideoOrder', $BookVideoOrder);
		$Stmt2->bindParam(':BookVideoView', $BookVideoView);
		$Stmt2->bindParam(':BookVideoState', $BookVideoState);
		$Stmt2->execute();
		$Stmt2 = null;

}
$Stmt = null;
?>

</body>
</html>
<?
include_once('../includes/dbclose.php');
?>