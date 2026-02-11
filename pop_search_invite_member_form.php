<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');

include_once('./includes/common_header.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css">
<link href="css/board.css" rel="stylesheet" type="text/css">    
</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<style>
body{background:#fff;}
.bbs_page img{height:8px; margin:11px 0 0 0;}
</style>
<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PageListNum = 20;

$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";


if($CurrentPage=="") {
	$CurrentPage = 1;
}

if($SearchText!="") {
	$ListParam = $ListParam . "&SearchText=".$SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.MemberLoginID like '%".$SearchText."%' ) ";
}

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}


$AddSqlWhere = $AddSqlWhere . " and A.MemberState=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.MemberLevelID=19 ";
$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select count(*) as TotalRowCount from Members A 
			where ".$AddSqlWhere;
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];


$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$Sql = "select A.MemberID, A.MemberName, A.MemberLoginID from Members A 
			where ".$AddSqlWhere." limit $StartRowNum, $PageListNum ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>
	<div id="bbs" style="padding:20px 2%;" >
		<div class="TopSearch">
			<h1 class="caption_sub TrnTag">추천인명 검색</h1><br/>
			<form name="SearchForm" method="get">
				<input type="hidden" name="PageListNum" value="<?=$PageListNum?>">
				<input type="hidden" name="ListParam" value="<?=$ListParam?>">
				<input type="text" name="SearchText" class="Input2" placeholder="검색어를 입력해 주세요." value="<?=$SearchText?>"><a href="javascript:SearchSubmit()" class="BtnSearch">검색</a>
            </form>
		</div>

		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table1">
			  <tr>
				<th class="th_color TrnTag" width="16%">No</th>
				<th class="th_color TrnTag" width="40%">추천인ID</th>
				<th class="th_color TrnTag" width="30%">추천인명</th>
			  </tr>
			<?php
			$ListCount = 1;
			while($Row = $Stmt->fetch()) {
				$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;
				$MemberName = $Row["MemberName"];
				$MemberLoginID = $Row["MemberLoginID"];
				$MemberID = $Row["MemberID"];


			?>
			  <tr>
				<td class="border1"><?=$ListCount?></td>
				<td class="border1"><a href="javascript:selectMember('<?=$MemberID?>', '<?=$MemberLoginID?>');"><?=$MemberLoginID?></a></td>
				<td class="border1"><a href="javascript:selectMember('<?=$MemberID?>', '<?=$MemberLoginID?>');"><?=$MemberName?></a></td>
			  </tr>
			<?php
				$ListCount ++;
			}
			$Stmt = null;
			?>
			  
		</table>

		<?php
			include_once('./inc_pagination.php');
		?>

	</div>

<script>

function SearchSubmit(){
	document.SearchForm.submit();
}

function selectMember(MemberID, MemberLoginID) {
	parent.RegForm.MemberInviteID.value = MemberID;
	parent.RegForm.MemberInviteLoginID.value = MemberLoginID;
	
	parent.$.fn.colorbox.close();
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





