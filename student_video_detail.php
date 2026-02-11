<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />

<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-2.2.4.min.js"></script>
<script src="js/common.js"></script>

<body>
<?
include_once('./includes/common_body_top.php');
?>
<?
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";

$Sql = "select 
			A.*,
			C.BookVideoName
		from ClassVideoPlayLogs A 
			inner join Classes B on A.ClassID=B.ClassID 
			inner join BookVideos C on B.BookVideoID=C.BookVideoID 
		where B.MemberID=:MemberID
		
		order by A.ClassVideoPlayLogDateTime desc;
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area" style="width:90%;">
		<h3 class="caption_underline TrnTag">레슨비디오 시청 이력</h3>

		
		<table class="level_reserve_table">
			<tr>
				<th style="padding:10px;text-align:center;">Date</th>
				<th style="padding:10px;text-align:center;">Video</th>
			</tr>
			<?
			$ListCount = 1;
			while($Row = $Stmt->fetch()) {
				$BookVideoName = $Row["BookVideoName"];
				$ClassVideoPlayLogDateTime = $Row["ClassVideoPlayLogDateTime"];
				$ClassVideoPlayLogDateTime = substr($ClassVideoPlayLogDateTime,0,10);
			?>
			<tr>
				<td class="radio_wrap time" style="text-align:center;"><?=$ClassVideoPlayLogDateTime?></td>
				<td class="radio_wrap time" style="text-align:center;"><?=$BookVideoName?></td>
			</tr>
			<?
				$ListCount++;
			}
			$Stmt = null;
			?>
		</table>
	</div>
</div>





</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>