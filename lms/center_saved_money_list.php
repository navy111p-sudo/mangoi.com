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
$MainMenuID = 11;
$SubMenuID = 1255;
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
$SearchCompanyID = isset($_REQUEST["SearchCompanyID"]) ? $_REQUEST["SearchCompanyID"] : "";
$SearchBranchGroupID = isset($_REQUEST["SearchBranchGroupID"]) ? $_REQUEST["SearchBranchGroupID"] : "";
$SearchBranchID = isset($_REQUEST["SearchBranchID"]) ? $_REQUEST["SearchBranchID"] : "";
$SearchCenterID = isset($_REQUEST["SearchCenterID"]) ? $_REQUEST["SearchCenterID"] : "";


//================== 서치폼 감추기 =================
$HideSearchCenterID = 0;
$HideSearchBranchID = 0;
$HideSearchBranchGroupID = 0;
$HideSearchCompanyID = 0;
$HideSearchFranchiseID = 0;

if ($_LINK_ADMIN_LEVEL_ID_==0 or $_LINK_ADMIN_LEVEL_ID_==1){//마스터
	//모두허용
}else if ($_LINK_ADMIN_LEVEL_ID_==3 or $_LINK_ADMIN_LEVEL_ID_==4){//프랜차이즈 관리자
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	
	$HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==6 or $_LINK_ADMIN_LEVEL_ID_==7){//대표지사 관리자
	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	
	$HideSearchBranchGroupID = 1;
	$HideSearchCompanyID = 1;
	$HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==9 or $_LINK_ADMIN_LEVEL_ID_==10){//지사 관리자
	$SearchBranchID = $_LINK_ADMIN_BRANCH_ID_;
	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	
	$HideSearchBranchID = 1;
	$HideSearchBranchGroupID = 1;
	$HideSearchCompanyID = 1;
	$HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==12 or $_LINK_ADMIN_LEVEL_ID_==13){//대리점 관리자
	$SearchCenterID = $_LINK_ADMIN_CENTER_ID_;
	$SearchBranchID = $_LINK_ADMIN_BRANCH_ID_;
	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;

	$HideSearchCenterID = 1;
	$HideSearchBranchID = 1;
	$HideSearchBranchGroupID = 1;
	$HideSearchCompanyID = 1;
	$HideSearchFranchiseID = 1;
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
	$AddSqlWhere = $AddSqlWhere . " and A.SavedMoneyState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.SavedMoneyState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and B.MemberState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and C.CenterState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and D.BranchState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and E.BranchGroupState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and F.CompanyState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and G.FranchiseState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (B.MemberName like '%".$SearchText."%' or B.MemberLoginID like '%".$SearchText."%' or B.MemberNickName like '%".$SearchText."%') ";
}

if ($SearchFranchiseID!=""){
	$ListParam = $ListParam . "&SearchFranchiseID=" . $SearchFranchiseID;
	$AddSqlWhere = $AddSqlWhere . " and F.FranchiseID=$SearchFranchiseID ";
}

if ($SearchCompanyID!=""){
	$ListParam = $ListParam . "&SearchCompanyID=" . $SearchCompanyID;
	$AddSqlWhere = $AddSqlWhere . " and E.CompanyID=$SearchCompanyID ";
}

if ($SearchBranchGroupID!=""){
	$ListParam = $ListParam . "&SearchBranchGroupID=" . $SearchBranchGroupID;
	$AddSqlWhere = $AddSqlWhere . " and D.BranchGroupID=$SearchBranchGroupID ";
}

if ($SearchBranchID!=""){
	$ListParam = $ListParam . "&SearchBranchID=" . $SearchBranchID;
	$AddSqlWhere = $AddSqlWhere . " and C.BranchID=$SearchBranchID ";
}

if ($SearchCenterID!=""){
	$ListParam = $ListParam . "&SearchCenterID=" . $SearchCenterID;
	$AddSqlWhere = $AddSqlWhere . " and B.CenterID=$SearchCenterID ";
}


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "SELECT  count(DISTINCT SavedMoneyID ) TotalRowCount 
		from SavedMoney A 
			inner join Members B on A.CenterID=B.CenterID 
			inner join Centers C on B.CenterID=C.CenterID 
			inner join Branches D on C.BranchID=D.BranchID  
			inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
			inner join Companies F on E.CompanyID=F.CompanyID 
			inner join Franchises G on F.FranchiseID=G.FranchiseID 
		where ".$AddSqlWhere." and A.SavedMoneyState=1 
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$Sql = "SELECT  
			A.*,
			B.MemberName,
			B.MemberLoginID, 
			B.MemberLevelID,
			C.CenterID as JoinCenterID,
			C.CenterName as JoinCenterName,
			D.BranchID as JoinBranchID,
			D.BranchName as JoinBranchName, 
			E.BranchGroupID as JoinBranchGroupID,
			E.BranchGroupName as JoinBranchGroupName,
			F.CompanyID as JoinCompanyID,
			F.CompanyName as JoinCompanyName,
			G.FranchiseName,
			K.MemberLevelName,
			(select sum(AA.SavedMoney) from SavedMoney AA where AA.CenterID=A.CenterID and AA.SavedMoneyState=1 and AA.SavedMoneyID<=A.SavedMoneyID) as TotalSavedMoney 
		from SavedMoney A
			inner join Members B on A.CenterID=B.CenterID 
			inner join Centers C on B.CenterID=C.CenterID 
			inner join Branches D on C.BranchID=D.BranchID 
			inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
			inner join Companies F on E.CompanyID=F.CompanyID 
			inner join Franchises G on F.FranchiseID=G.FranchiseID 
			inner join MemberLevels K on B.MemberLevelID=K.MemberLevelID 
		where ".$AddSqlWhere." and A.SavedMoneyState=1 
		GROUP BY A.SavedMoneyID 
		order by A.SavedMoneyRegDateTime desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$충전금내역[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">

					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchFranchiseID==1){?>none<?}?>;">
						<select id="SearchFranchiseID" name="SearchFranchiseID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="프랜차이즈선택" style="width:100%;"/>
							<option value=""></option>
							<?
							$Sql2 = "SELECT  
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
										echo "<optgroup label=\"".$프랜차이즈_운영중[$LangID]."\">";
									}else if ($SelectFranchiseState==2){
										echo "<optgroup label=\"".$프랜차이즈_미운영[$LangID]."\">";
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

					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchCompanyID==1){?>none<?}?>;">
						<select id="SearchCompanyID" name="SearchCompanyID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$본사선택[$LangID]?>" style="width:100%;"/>
							<option value=""></option>
							<?
							$AddWhere2 = "";
							if ($SearchFranchiseID!=""){
								$AddWhere2 = "and A.FranchiseID=".$SearchFranchiseID." ";
							}else{
								$AddWhere2 = " ";
							}
							$Sql2 = "SELECT  
											A.* 
									from Companies A 
										inner join Franchises B on A.FranchiseID=B.FranchiseID 
									where A.CompanyState<>0 and B.FranchiseState<>0 ".$AddWhere2." 
									order by A.CompanyState asc, A.CompanyName asc";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldSelectCompanyState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectCompanyID = $Row2["CompanyID"];
								$SelectCompanyName = $Row2["CompanyName"];
								$SelectCompanyState = $Row2["CompanyState"];
							
								if ($OldSelectCompanyState!=$SelectCompanyState){
									if ($OldSelectCompanyState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectCompanyState==1){
										echo "<optgroup label=\"".$본사_운영중[$LangID]."\">";
									}else if ($SelectCompanyState==2){
										echo "<optgroup label=\"".$본사_미운영[$LangID]."\">";
									}
								}
								$OldSelectCompanyState = $SelectCompanyState;
							?>

							<option value="<?=$SelectCompanyID?>" <?if ($SearchCompanyID==$SelectCompanyID){?>selected<?}?>><?=$SelectCompanyName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>


					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
						<select id="SearchBranchGroupID" name="SearchBranchGroupID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$대표지사선택[$LangID]?>" style="width:100%;"/>
							<option value=""></option>
							<?
							$AddWhere2 = "";
							if ($SearchCompanyID!=""){
								$AddWhere2 = "and A.CompanyID=".$SearchCompanyID." ";
							}else{
								if ($SearchFranchiseID!=""){
									$AddWhere2 = "and B.FranchiseID=".$SearchFranchiseID." ";
								}else{
									$AddWhere2 = " ";
								}
							}
							$Sql2 = "SELECT  
											A.* 
										from BranchGroups A 
											inner join Companies B on A.CompanyID=B.CompanyID 
											inner join Franchises C on B.FranchiseID=C.FranchiseID 
										where A.BranchGroupState<>0 and B.CompanyState<>0 and C.FranchiseState<>0 ".$AddWhere2." 
										order by A.BranchGroupState asc, A.BranchGroupName asc";
							
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldSelectBranchGroupState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectBranchGroupID = $Row2["BranchGroupID"];
								$SelectBranchGroupName = $Row2["BranchGroupName"];
								$SelectBranchGroupState = $Row2["BranchGroupState"];
							
								if ($OldSelectBranchGroupState!=$SelectBranchGroupState){
									if ($OldSelectBranchGroupState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectBranchGroupState==1){
										echo "<optgroup label=\"".$대표지사_운영중[$LangID]."\">";
									}else if ($SelectBranchGroupState==2){
										echo "<optgroup label=\"".$대표지사_미운영[$LangID]."\">";
									}
								}
								$OldSelectBranchGroupState = $SelectBranchGroupState;
							?>

							<option value="<?=$SelectBranchGroupID?>" <?if ($SearchBranchGroupID==$SelectBranchGroupID){?>selected<?}?>><?=$SelectBranchGroupName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>
					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchID==1){?>none<?}?>;">
						<select id="SearchBranchID" name="SearchBranchID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$지사선택[$LangID]?>" style="width:100%;"/>
							<option value=""></option>
							<?
							$AddWhere2 = "";
							if ($SearchBranchGroupID!=""){
								$AddWhere2 = "and A.BranchGroupID=".$SearchBranchGroupID." ";
							}else{
								if ($SearchCompanyID!=""){
									$AddWhere2 = "and B.CompanyID=".$SearchCompanyID." ";
								}else{
									if ($SearchFranchiseID!=""){
										$AddWhere2 = "and C.FranchiseID=".$SearchFranchiseID." ";
									}else{
										$AddWhere2 = " ";
									}
								}
							}
	
							$Sql2 = "select 
											A.* 
									from Branches A 
										inner join BranchGroups B on A.BranchGroupID=B.BranchGroupID 
										inner join Companies C on B.CompanyID=C.CompanyID 
										inner join Franchises D on C.FranchiseID=D.FranchiseID 
									where A.BranchState<>0 and B.BranchGroupState<>0 and C.CompanyState<>0 and D.FranchiseState<>0 ".$AddWhere2." 
									order by A.BranchState asc, A.BranchName asc";

							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldSelectBranchState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectBranchID = $Row2["BranchID"];
								$SelectBranchName = $Row2["BranchName"];
								$SelectBranchState = $Row2["BranchState"];
							
								if ($OldSelectBranchState!=$SelectBranchState){
									if ($OldSelectBranchState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectBranchState==1){
										echo "<optgroup label=\"".$지사_운영중[$LangID]."\">";
									}else if ($SelectBranchState==2){
										echo "<optgroup label=\"".$지사_미운영[$LangID]."\">";
									}
								}
								$OldSelectBranchState = $SelectBranchState;
							?>

							<option value="<?=$SelectBranchID?>" <?if ($SearchBranchID==$SelectBranchID){?>selected<?}?>><?=$SelectBranchName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>
					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchCenterID==1){?>none<?}?>;">
						<select id="SearchCenterID" name="SearchCenterID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$대리점선택[$LangID]?>" style="width:100%;"/>
							<option value=""></option>
							<?

							$AddWhere2 = "";
							if ($SearchBranchID!=""){
								$AddWhere2 = "and A.BranchID=".$SearchBranchID." ";
							}else{
								if ($SearchBranchGroupID!=""){
									$AddWhere2 = "and B.BranchGroupID=".$SearchBranchGroupID." ";
								}else{
									if ($SearchCompanyID!=""){
										$AddWhere2 = "and C.CompanyID=".$SearchCompanyID." ";
									}else{
										if ($SearchFranchiseID!=""){
											$AddWhere2 = "and D.FranchiseID=".$SearchFranchiseID." ";
										}else{
											$AddWhere2 = " ";
										}
									}
								}
							}

							$Sql2 = "select 
											A.* 
									from Centers A 
										inner join Branches B on A.BranchID=B.BranchID 
										inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
										inner join Companies D on C.CompanyID=D.CompanyID 
										inner join Franchises E on D.FranchiseID=E.FranchiseID 
									where A.CenterState<>0 and B.BranchState<>0 and C.BranchGroupState<>0 and D.CompanyState<>0 and E.FranchiseState<>0 ".$AddWhere2." 
									order by A.CenterState asc, A.CenterName asc";	
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldSelectCenterState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectCenterID = $Row2["CenterID"];
								$SelectCenterName = $Row2["CenterName"];
								$SelectCenterState = $Row2["CenterState"];
							
								if ($OldSelectCenterState!=$SelectCenterState){
									if ($OldSelectCenterState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectCenterState==1){
										echo "<optgroup label=\"".$대리점_운영중[$LangID]."\">";
									}else if ($SelectCenterState==2){
										echo "<optgroup label=\"".$대리점_미운영[$LangID]."\">";
									}
								}
								$OldSelectCenterState = $SelectCenterState;
							?>

							<option value="<?=$SelectCenterID?>" <?if ($SearchCenterID==$SelectCenterID){?>selected<?}?>><?=$SelectCenterName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>
					<div class="uk-width-medium-3-10">
						<label for="SearchText">대리점명 또는 아이디</label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>


					<div class="uk-width-medium-1-10" style="display:none;">
						<div class="uk-margin-small-top">
							<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$정상[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$비정상[$LangID]?></option>
							</select>
						</div>
					</div>


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
										<th nowrap><?=$대리점명[$LangID]?></th>										
										<th nowrap><?=$충전금[$LangID]?></th>
										<th nowrap><?=$누적충전금[$LangID]?></th>
										<th nowrap><?=$날짜[$LangID]?></th>
										<th nowrap><?=$본사명[$LangID]?></th>
										<th nowrap><?=$대표지사명[$LangID]?></th>
										<th nowrap><?=$지사명[$LangID]?></th>
										<th nowrap><?=$프랜차이즈[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$SavedMoneyID = $Row["SavedMoneyID"];
										$CenterID = $Row["CenterID"];
										$MemberLevelID = $Row["MemberLevelID"];
										$MemberLevelName = $Row["MemberLevelName"];
										
										$SavedMoney = $Row["SavedMoney"];
										$SavedMoneyRegDateTime = $Row["SavedMoneyRegDateTime"];
										
										$MemberName = $Row["MemberName"];
										$MemberLoginID = $Row["MemberLoginID"];

										$CenterID = $Row["JoinCenterID"];
										$CenterName = $Row["JoinCenterName"];
										$BranchID = $Row["JoinBranchID"];
										$BranchName = $Row["JoinBranchName"];
										$BranchGroupID = $Row["JoinBranchGroupID"];
										$BranchGroupName = $Row["JoinBranchGroupName"];
										$CompanyID = $Row["JoinCompanyID"];
										$CompanyName = $Row["JoinCompanyName"];
										$FranchiseName = $Row["FranchiseName"];

										$TotalSavedMoney = $Row["TotalSavedMoney"];
										
							
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=($_LINK_ADMIN_LEVEL_ID_==0 or $_LINK_ADMIN_LEVEL_ID_==1)?"<a href='javascript:OpenSavedMoneyForm(".$SavedMoneyID.");'>".$ListNumber."</a>":$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=($_LINK_ADMIN_LEVEL_ID_==0 or $_LINK_ADMIN_LEVEL_ID_==1)?"<a href='javascript:OpenSavedMoneyForm(".$SavedMoneyID.");'>".$CenterName."</a>":$CenterName?></td>
										<td class="uk-text-nowrap uk-table-td-right"><?=number_format($SavedMoney,0)?> P</td>
										<td class="uk-text-nowrap uk-table-td-right"><?=number_format($TotalSavedMoney,0)?> P</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$SavedMoneyRegDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CompanyName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchGroupName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$FranchiseName?></td>
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
						
						if ($_LINK_ADMIN_LEVEL_ID_==0 or $_LINK_ADMIN_LEVEL_ID_==1){//마스터?>
						
							<div class="uk-form-row" style="text-align:center;">
							<a href="javascript:OpenSavedMoneyForm('');" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
							</div>
						<?php } ?>
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
<script>


function OpenSavedMoneyForm(SavedMoneyID){
	openurl = "saved_money_form.php?SavedMoneyID="+SavedMoneyID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}
</script>



<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "center_saved_money_list.php";
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