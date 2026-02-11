<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
?>

<?
include_once('./inc_common_list_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 12;
$SubMenuID = 1206;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>


 
<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
$SearchFranchiseID = isset($_REQUEST["SearchFranchiseID"]) ? $_REQUEST["SearchFranchiseID"] : "";


//================== 서치폼 감추기 =================
$HideSearchFranchiseID = 0;

if ($_LINK_ADMIN_LEVEL_ID_==0 or $_LINK_ADMIN_LEVEL_ID_==1){//마스터
	//모두허용
}else if ($_LINK_ADMIN_LEVEL_ID_==3 or $_LINK_ADMIN_LEVEL_ID_==4){//프랜차이즈 관리자
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	
	$HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==6 or $_LINK_ADMIN_LEVEL_ID_==7){//대표지사 관리자
	//접속불가
}else if ($_LINK_ADMIN_LEVEL_ID_==9 or $_LINK_ADMIN_LEVEL_ID_==10){//지사 관리자
	//접속불가
}else if ($_LINK_ADMIN_LEVEL_ID_==12 or $_LINK_ADMIN_LEVEL_ID_==13){//대리점 관리자
	//접속불가
}else if ($_LINK_ADMIN_LEVEL_ID_==15){//강사 
	//접속불가
}
//================== 서치폼 감추기 =================

if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 30;
}


if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}

if ($SearchState==""){
	$SearchState = "1";
}	
		
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.CompanyState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.CompanyState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and B.FranchiseState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.CompanyName like '%".$SearchText."%' or A.CompanyManagerName like '%".$SearchText."%') ";
}

if ($SearchFranchiseID!=""){
	$ListParam = $ListParam . "&SearchFranchiseID=" . $SearchFranchiseID;
	$AddSqlWhere = $AddSqlWhere . " and A.FranchiseID=$SearchFranchiseID ";
}

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
			count(*) TotalRowCount 
		from Companies A 
			inner join Franchises B on A.FranchiseID=B.FranchiseID 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$StudentCountSql = ",(
			select 
				count(*) 
			from Members AA 
				inner join Centers BB on AA.CenterID=BB.CenterID 
				inner join Branches CC on BB.BranchID=CC.BranchID 
				inner join BranchGroups DD on CC.BranchGroupID=DD.BranchGroupID 
				inner join Companies EE on DD.CompanyID=EE.CompanyID 
				inner join Franchises FF on EE.FranchiseID=FF.FranchiseID 
			where AA.MemberLevelID=19 and AA.MemberState<>0 and EE.CompanyID=A.CompanyID 
			) as StudentCount 
			";


$Sql = "
		select 
			A.* ,
			AES_DECRYPT(UNHEX(A.CompanyPhone1),:EncryptionKey) as DecCompanyPhone1,
			AES_DECRYPT(UNHEX(A.CompanyPhone2),:EncryptionKey) as DecCompanyPhone2,
			B.FranchiseName,
			ifnull((select count(*) from BranchGroups where CompanyID=A.CompanyID and BranchGroupState<>0),0) as BranchGroupCount,
			ifnull((select count(*) from Branches AA inner join BranchGroups BB on AA.BranchGroupID=BB.BranchGroupID where BB.CompanyID=A.CompanyID and AA.BranchState<>0 and BB.BranchGroupState<>0),0) as BranchCount,
			ifnull((select count(*) from Centers AA inner join Branches BB on AA.BranchID=BB.BranchID inner join BranchGroups CC on BB.BranchGroupID=CC.BranchGroupID where CC.CompanyID=A.CompanyID and AA.CenterState<>0 and BB.BranchState<>0 and CC.BranchGroupState<>0),0) as CenterCount
			".$StudentCountSql."
		from Companies A 
			inner join Franchises B on A.FranchiseID=B.FranchiseID 
		where ".$AddSqlWhere." 
		order by A.CompanyOrder desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$본사관리[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					
					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchFranchiseID==1){?>none<?}?>;">
						<select id="SearchFranchiseID" name="SearchFranchiseID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$프랜차이즈선택[$LangID]?>" style="width:100%;"/>
							<option value=""></option>
							<?
							$Sql2 = "select 
											A.* 
									from Franchises A 
									where A.FranchiseState<>0 
									order by A.FranchiseState asc, A.FranchiseName asc";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldSelectFranchiseState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectFranchiseID = $Row2["FranchiseID"];
								$SelectFranchiseName = $Row2["FranchiseName"];
								$SelectFranchiseState = $Row2["FranchiseState"];
							
								if ($OldSelectFranchiseState!=$SelectFranchiseState){
									if ($OldSelectFranchiseState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectFranchiseState==1){
										echo "<optgroup label=\"프랜차이즈(운영중)\">";
									}else if ($SelectFranchiseState==2){
										echo "<optgroup label=\"프랜차이즈(미운영)\">";
									}
								} 
								$OldSelectFranchiseState = $SelectFranchiseState;
							?>

							<option value="<?=$SelectFranchiseID?>" <?if ($SearchFranchiseID==$SelectFranchiseID){?>selected<?}?>><?=$SelectFranchiseName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>

					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$본사명_또는_관리자명[$LangID]?></label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>

					<!--
					<div class="uk-width-medium-1-10">
						<label for="product_search_price">Price</label>
						<input type="text" class="md-input" id="product_search_price">
					</div>
					-->

					<div class="uk-width-medium-1-10">
						<div class="uk-margin-small-top">
							<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$운영중[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$미운영[$LangID]?></option>
							</select>
						</div>
					</div>

					<!--
					<div class="uk-width-medium-1-10">
						<div class="uk-margin-top uk-text-nowrap">
							<input type="checkbox" name="product_search_active" id="product_search_active" data-md-icheck/>
							<label for="product_search_active" class="inline-label">Active</label>
						</div>
					</div>
					-->

					<div class="uk-width-medium-1-10 uk-text-center">
						<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
					</div>
					
				</div>
			</div>
		</div>
		</form>


		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap><?=$본사명[$LangID]?></th>
										
										<th nowrap><?=$관리자[$LangID]?></th>
										<th nowrap><?=$학생수[$LangID]?></th>
										<th nowrap><?=$전화번호[$LangID]?></th>
										<th nowrap><?=$휴대폰[$LangID]?></th>
										<th nowrap><?=$수강료_10분당 [$LangID]?></th>
										<th nowrap><?=$대표지사수[$LangID]?></th>
										<th nowrap><?=$지사수[$LangID]?></th>
										<th nowrap><?=$대리점수[$LangID]?></th>
										
										<!--<th nowrap>수강생</th>-->
										<th nowrap><?=$프랜차이즈[$LangID]?></th>
										<th nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$CompanyID = $Row["CompanyID"];
										$CompanyName = $Row["CompanyName"];
										$CompanyManagerName = $Row["CompanyManagerName"];
										$CompanyPhone1 = $Row["DecCompanyPhone1"];
										$CompanyPhone2 = $Row["DecCompanyPhone2"];
										$CompanyPricePerTime = $Row["CompanyPricePerTime"];
										$CompanyState = $Row["CompanyState"];
										$FranchiseName = $Row["FranchiseName"];
										$BranchGroupCount = $Row["BranchGroupCount"];
										$BranchCount = $Row["BranchCount"];
										$CenterCount = $Row["CenterCount"];
										$StudentCount = $Row["StudentCount"];

										if ($CompanyState==1){
											$StrCompanyState = "<span class=\"ListState_1\">운영중</span>";
										}else if ($CompanyState==2){
											$StrCompanyState = "<span class=\"ListState_2\">미운영</span>";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="company_form.php?ListParam=<?=$ListParam?>&CompanyID=<?=$CompanyID?>"><?=$CompanyName?></a></td>
										
										<td class="uk-text-nowrap uk-table-td-center"><?=$CompanyManagerName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($StudentCount,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CompanyPhone1?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CompanyPhone2?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($CompanyPricePerTime,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($BranchGroupCount,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($BranchCount,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($CenterCount,0)?></td>
										
										<!--<td class="uk-text-nowrap uk-table-td-center">-</td>-->
										<td class="uk-text-nowrap uk-table-td-center"><?=$FranchiseName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrCompanyState?></td>
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;
									?>


								</tbody>
							</table>
						</div>
						

						<?php			
						include_once('./inc_pagination.php');
						?>

						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="company_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
						</div>

					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<?
include_once('./inc_common_list_js.php');
?>
<!-- ============== only this page js ============== -->

<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "company_list.php";
	document.SearchForm.submit();
}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>