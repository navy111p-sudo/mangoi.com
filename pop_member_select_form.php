<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />

<link rel="stylesheet" type="text/css" href="./css/common.css">
</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<style>
body{background:#fff;}
.ContentPopup{padding:30px 30px; text-align:center;}
.ContentPopup h2{border-bottom:1px solid #ccc; padding-bottom:10px; font-size:16px; color:#444; text-align:left; margin-bottom:50px;}
</style>
<div class="ContentPopup" style="text-align:center;">
	<h2 class="Font1"> 학생선택</h2>
  	
	<div style="width:100%;height:300px;overflow-y:scroll;text-align:left;">
	<?
	$ProductInterviewID = isset($_REQUEST["ProductInterviewID"]) ? $_REQUEST["ProductInterviewID"] : "";
	$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
	?>

	<?
	$Sql = "select A.* from Members A where CenterID=$CenterID and MemberLevelID=9 and MemberState=1 and MemberID not in (select MemberID from MemberProductInterviews where ProductInterviewID=$ProductInterviewID and MemberProductInterviewState=1) order by MemberName desc";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	while($Row = $Stmt->fetch()) {
	?>
	<div style="display:inline-block;width:170px;padding:5px;border:1px solid #cccccc;margin-bottom:2px;cursor:pointer;border-radius:5px;" onclick="AddMember(<?=$Row["MemberID"]?>,<?=$ProductInterviewID?>, <?=$CenterID?>)"><img src="images/IconPlus.png" style="width:15px;margin-top:5px;">&nbsp;<?=$Row["MemberName"]?>(<?=$Row["MemberLoginID"]?>)</div>
	<?
	}
	$Stmt = null;
	?>
	</div>
	

</div>

<script>

function AddMember(MemberID,ProductInterviewID,CenterID){
	if (confirm('추가 하시겠습니까?')){
		location.href="pop_member_select_action.php?MemberID="+MemberID+"&ProductInterviewID="+ProductInterviewID+"&CenterID="+CenterID;
	}
}


</script>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>





