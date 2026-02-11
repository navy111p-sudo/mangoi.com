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
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";

$Sql = "select * from AssmtStudentDailyScores where ClassID=:ClassID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$AssmtStudentDailyScore1 = $Row["AssmtStudentDailyScore1"];
$AssmtStudentDailyScore2 = $Row["AssmtStudentDailyScore2"];
$AssmtStudentDailyScore3 = $Row["AssmtStudentDailyScore3"];
$AssmtStudentDailyScore4 = $Row["AssmtStudentDailyScore4"];
$AssmtStudentDailyScore5 = $Row["AssmtStudentDailyScore5"];
$AssmtStudentDailyComment = $Row["AssmtStudentDailyComment"];
?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area" style="width:90%;">
		<h3 class="caption_underline TrnTag">평가표</h3>
		<table class="level_reserve_table">
			<tr>
				<th style="width:120px;">Pronunciation</th>
				<td class="radio_wrap time">
					<?=$AssmtStudentDailyScore1?> / 10
				</td>
			</tr>
			<tr>
				<th>Grammar</th>
				<td class="radio_wrap time">
					<?=$AssmtStudentDailyScore2?> / 10
				</td>
			</tr>
			<tr>
				<th>Vocabulary</th>
				<td class="radio_wrap time">
					<?=$AssmtStudentDailyScore3?> / 10
				</td>
			</tr>
			<tr>
				<th>Attitude</th>
				<td class="radio_wrap time">
					<?=$AssmtStudentDailyScore4?> / 10
				</td>
			</tr>
			<tr>
				<th>Fluency</th>
				<td class="radio_wrap time">
					<?=$AssmtStudentDailyScore5?> / 10
				</td>
			</tr>
			<tr>
				<th>Comment</th>
				<td class="radio_wrap time">
					<?=str_replace("\n","<br>", $AssmtStudentDailyComment)?>
				</td>
			</tr>
		</table>
	</div>
</div>



</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>