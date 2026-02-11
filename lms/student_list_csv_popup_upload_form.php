<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";

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
<style>
body{background:#fff;}
.ContentPopup{padding:30px 30px; text-align:center;}
.ContentPopup h2{border-bottom:1px solid #ccc; padding-bottom:10px; font-size:16px; color:#444; text-align:left; margin-bottom:50px;}
</style>

<?php

$Sql = "select A.MemberLevelID, A.CenterID, A.BranchID, A.BranchGroupID from Members A where MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();

$MemberLevelID = $Row["MemberLevelID"];
$CenterID = $Row["CenterID"];
$BranchID = $Row["BranchID"];
$BranchGroupID = $Row["BranchGroupID"];

$AddSqlWhere = "";


if($MemberLevelID == 12 || $MemberLevelID == 13) {
	$AddSqlWhere .= " and A.CenterID=$CenterID ";
} else if ($MemberLevelID == 9 || $MemberLevelID == 10) {
	$AddSqlWhere .= " and A.BranchID=$BranchID ";
} else if ($MemberLevelID == 6 || $MemberLevelID == 7) {
	$AddSqlWhere .= " and C.BranchGroupID=$BranchGroupID ";
} else if ($MemberLevelID <=4) {
	$AddSqlWhere .= " ";
}


// 조인을 붙이거나, 아님 빼야함
$Sql = "select A.CenterID, A.CenterName from Centers A 
			inner join Branches B on A.BranchID=B.BranchID 
			inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
		where 1=1".$AddSqlWhere;

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>



<div class="ContentPopup" style="text-align:center;">
	<h2 class="Font1"> 엑셀파일 업로드</h2>

	<form name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
		<div class="uk-width-medium-2-10" style="margin-bottom:20px;line-height:1.5; display:<?if($MemberLevelID>=12){?>none<?}?>" >학원지정<br/>
			<select name="CenterID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" style="width:50%; margin-top:10px; padding:10px;">
			<?php
				while($Row = $Stmt->fetch()) {
			?>
			<option value="<?=$Row['CenterID']?>" name="CenterID" ><?=$Row['CenterName']?></option>
			<?php
				}
			?>
			</select>
		</div>
		<div style="margin-bottom:20px;line-height:1.5;">업로드 시간이 오래걸릴수 있습니다<br>업로드 후 창이 닫힐때까지 기다려 주세요.<br/>

		</div>
		<input type="file" name="UpFile" id="UpFile" style="width:200px; height:32px; line-height:32px; margin-bottom:20px;">
	</form>
	

	<div class="BtnJoin" style="margin-bottom:100px;text-align:center;">
    	<a href="javascript:FormSubmit();" style="margin:0 auto;display:block; background-color:#556BAC; color:#ffffff; text-align:center; width:110px; width:100px; line-height:32px; font-size:14px;">업로드</a>
    </div>
</div>

<script>

function FormSubmit(){
	//openurl = "student_list_csv_popup_upload_action_check.php";

	//$.colorbox({	
	//	href:openurl
	//	,width:"95%" 
	//	,height:"95%"
	//	,maxWidth: "850"
	//	,maxHeight: "750"
	//	,title:""
	//	,iframe:true 
	//	,scrolling:true
	//	//,onClosed:function(){location.reload(true);}
	//	//,onComplete:function(){alert(1);}
	//});
	document.RegForm.action = "student_list_csv_popup_upload_action_check.php";
	document.RegForm.submit();
}


</script>
</body>
</html>
<?php
include_once('../includes/dbclose.php');
?>





