<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
//include_once('./includes/common_meta_tag.php');
//include_once('./inc_header.php');
?>

<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/sub_style.css" />
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->

</head>

<body>
<?
include_once('./includes/common_body_top.php');
?>
<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";

$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";

if ($SearchText!=""){
	$AddSqlWhere = $AddSqlWhere . " and A.MemberName like '%".$SearchText."%'";
}

//$ListParam = str_replace("&", "^^", $ListParam);


?>

<section class="mode_login_wrap">
    <h3 class="mode_login_caption"><b>학생계정</b> 전환</h3>
    <div class="mode_login_search">
		<form name="SearchForm" method="get">
		<input type="hidden" id="CenterID" name="CenterID" value="<?=$CenterID?>">
		<input type="hidden" name="MemberID">
        <input type="text" placeholder="학생명" class="mode_login_input" id="SearchText" name="SearchText" value="<?=$SearchText?>"><a href="javascript:SearchSubmit();" class="mode_login_search_btn">검색</a>
		</form>
	</div>
    <table class="mode_login_table">
        <col width="45%">
        <tr>
            <th>No</th>
            <th class="TrnTag">학생명</th>
        </tr>
		<?php

		$Sql = "SELECT A.MemberID, A.MemberName FROM Members A 
				WHERE ".$AddSqlWhere." and A.CenterID=:CenterID and A.MemberLevelId=19";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':CenterID', $CenterID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);

		$ListCount = 1;
		while($Row = $Stmt->fetch()) {

			$MemberID = $Row["MemberID"];
			$MemberName = $Row["MemberName"];
										
		?>
		<tr>
			<td><?=$ListCount?></td>
			<td><a href="javascript:ChangeStudentAccount(<?=$MemberID?>)" class="mode_login_name_btn"><?=$MemberName?></td>
		</tr>
		<?php
			$ListCount ++;
		}
		$Stmt = null;
		?>
    </table>							
</section>

<script>

function ChangeStudentAccount(MemberID) {

	document.SearchForm.MemberID.value = MemberID;
	document.SearchForm.action = "pop_mypage_teacher_mode_login_ok.php";
	document.SearchForm.submit();
	
}

</script>



<script>
function SearchSubmit(){
	document.SearchForm.submit();
}
</script>

<?php
include_once('./includes/dbclose.php');
?>
</body>
</html>