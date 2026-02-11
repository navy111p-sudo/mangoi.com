<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<?
include_once('./includes/common_body_top.php');
?>
<table border="0" cellspacing="0" cellpadding="0" class="bottom2">
  <tr>

<?php
include_once('./includes/dbopen.php');


$Sql = "select * from BoardContents where (BoardID=21 or BoardID=22) and BoardContentState=1 order by BoardContentNotice desc, BoardContentReplyID desc, BoardContentReplyOrder asc limit 0, 4 ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

while($Row = $Stmt->fetch()) {
	$BoardContentID = $Row["BoardContentID"];

	$Sql2 = "select * from BoardContentFiles  where BoardContentID=:BoardContentID order by BoardFileNumber asc";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':BoardContentID', $BoardContentID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();

?>
    <td><a href="board_list.php?BoardCode=product2" target="_parent"><img src="uploads/board_files/<?=$Row2["BoardFileName"]?>"></a></td>
 <?php
}
$Stmt = null;

include_once('./includes/dbclose.php');
?> 
   </tr>
</table>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>