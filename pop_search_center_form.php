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
	$AddSqlWhere = $AddSqlWhere . " and A.CenterName like '%".$SearchText."%' ";
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
}

$AddSqlWhere = $AddSqlWhere . " and OnlineSiteID=$OnlineSiteID ";
$AddSqlWhere = $AddSqlWhere . " and CenterState=1 ";
$AddSqlWhere = $AddSqlWhere . " and CenterView=1 ";

# 중국 20 // 중국 학원만 있는것 확인
# SLP 18 // SLP 외에 망고아이 지사(기본), 울산지사장_김재익 존재
# EIE 19 29 // EIE 학원 확인
# DREAM 19 31 // 드림 학원 확인
# TELL 10 38 // 잉글리시텔 확인
# THOMAS 19 154 // 토마스 지사 확인

// 사이트 별로 where 구문 추가 ( englishtell, thomas 는 회원가입이 없으므로 제외 )
if($DomainSiteID==0) { // 본사
	$AddSqlWhere = $AddSqlWhere . " and A.CenterAcceptJoin=1 and C.BranchGroupID!=20 and C.BranchGroupID!=18 and A.CenterID!=93 and  ( C.BranchGroupID!=19 and B.BranchID!=29 ) and ( C.BranchGroupID!=19 and B.BranchID!=31 ) and ( C.BranchGroupID!=10 and B.BranchID!=38 ) and ( C.BranchGroupID!=19 and B.BranchID!=154 ) ";
} else if($DomainSiteID==1) { // SLP
	$AddSqlWhere = $AddSqlWhere . " and A.CenterAcceptJoin=1 and C.BranchGroupID=18 ";
} else if($DomainSiteID==2) { // EIE
	$AddSqlWhere = $AddSqlWhere . " and A.CenterAcceptJoin=1 and C.BranchGroupID=19 and B.BranchID=29 ";
} else if($DomainSiteID==3) { // DREAM
	$AddSqlWhere = $AddSqlWhere . " and A.CenterAcceptJoin=1 and C.BranchGroupID=19 and B.BranchID=31 ";
} else if($DomainSiteID==6) { // HI
	$AddSqlWhereSearchCenter = $AddSqlWhereSearchCenter . " and B.BranchID=178 ";
}

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);



$Sql = "select count(*) as TotalRowCount from Centers A 
			inner join Branches B on A.BranchID=B.BranchID 
			inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
			where ".$AddSqlWhere;
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];


$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$Sql = "select * from Centers A 
			inner join Branches B on A.BranchID=B.BranchID 
			inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
			where $AddSqlWhere limit $StartRowNum, $PageListNum ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>
	<div id="bbs" style="padding:20px 2%;" >
		<div class="TopSearch">
			<h1 class="caption_sub TrnTag">학원명 검색</h1><br/>
			<form name="SearchForm" method="get">
				<input type="hidden" name="PageListNum" value="<?=$PageListNum?>">
				<input type="hidden" name="ListParam" value="<?=$ListParam?>">
				<input type="text" name="SearchText" class="Input2" placeholder="검색어를 입력해 주세요." value="<?=$SearchText?>"><a href="javascript:SearchSubmit()" class="BtnSearch">검색</a>
            </form>
		</div>

		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table1">
			  <tr>
				<th class="th_color TrnTag" width="16%">No</th>
				<th class="th_color TrnTag">센터명</th>
			  </tr>
			<?php
			$ListCount = 1;
			while($Row = $Stmt->fetch()) {
				$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;
				$CenterName = $Row["CenterName"];
				$CenterID = $Row["CenterID"];


			?>
			  <tr>
				<td class="border1"><?=$ListCount?></td>
				<td class="border1"><a href="javascript:selectCenter('<?=$CenterID?>', '<?=$CenterName?>');"><?=$CenterName?></a></td>
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

function selectCenter(CenterID, CenterName) {
	
	parent.RegForm.CenterID.value = CenterID;
	parent.RegForm.CenterName.value = CenterName;
	
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





