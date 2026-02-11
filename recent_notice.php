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
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="recent_notice">
    <col width="65%">
    <col>
<?php
include_once('./includes/dbopen.php');


$Sql = "select * from BoardContents where BoardID=20 and BoardContentState=1 order by BoardContentNotice desc, BoardContentReplyID desc, BoardContentReplyOrder asc limit 0, 3 ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

while($Row = $Stmt->fetch()) {
?>
	<tr>
    <td><a href="board_read.php?ListParam=1=1^^BoardCode=notice^^PageListNum=10^^CurrentPage=1&BoardContentID=<?=$Row["BoardContentID"]?>&BoardCode=notice" target="_parent"><?=substr_utf8($Row["BoardContentSubject"],0,15)?>...</a></td>
    <th><?=substr($Row["BoardContentRegDateTime"],0,10)?></th>
    </tr>
<?php
}
$Stmt = null;

include_once('./includes/dbclose.php');


function substr_utf8($str,$from,$len)
{
    return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $from .'}'.'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $len .'}).*#s','$1', $str);
}

?>

</table>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>

    

