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
$MainMenuID = 14;
$SubMenuID = 1408;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>

 

<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";



$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";

$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";


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



if ($SearchBranchID=="" && $SearchCenterID==""){
	$SearchBranchID = -1;
	$SearchCenterID = -1;
}


if (!$CurrentPage){
    $CurrentPage = 1;    
}
if ($PageListNum==""){
    $PageListNum = 30;
}




if ($SearchYear==""){
	$SearchYear = date("Y");
}

if ($SearchMonth==""){
	$SearchMonth = date("m");
}

$ListParam = $ListParam . "&SearchYear=" . $SearchYear;
$ListParam = $ListParam . "&SearchMonth=" . $SearchMonth;


$AddSqlWhere = $AddSqlWhere . " and A.MemberState=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.MemberLevelID=19 ";

$AddSqlWhere = $AddSqlWhere . " and (A.MemberState<>0 or A.MemberState is null) ";
$AddSqlWhere = $AddSqlWhere . " and (B.CenterState<>0 or B.CenterState is null)";
$AddSqlWhere = $AddSqlWhere . " and (C.BranchState<>0 or C.BranchState is null)";
$AddSqlWhere = $AddSqlWhere . " and (D.BranchGroupState<>0 or D.BranchGroupState is null)";
$AddSqlWhere = $AddSqlWhere . " and (E.CompanyState<>0 or E.CompanyState is null)";
$AddSqlWhere = $AddSqlWhere . " and (F.FranchiseState<>0 or F.FranchiseState is null)";

if ($SearchFranchiseID!=""){
	$ListParam = $ListParam . "&SearchFranchiseID=" . $SearchFranchiseID;
	$AddSqlWhere = $AddSqlWhere . " and E.FranchiseID=$SearchFranchiseID ";
}

if ($SearchCompanyID!=""){
	$ListParam = $ListParam . "&SearchCompanyID=" . $SearchCompanyID;
	$AddSqlWhere = $AddSqlWhere . " and D.CompanyID=$SearchCompanyID ";
}

if ($SearchBranchGroupID!=""){
	$ListParam = $ListParam . "&SearchBranchGroupID=" . $SearchBranchGroupID;
	$AddSqlWhere = $AddSqlWhere . " and C.BranchGroupID=$SearchBranchGroupID ";
}

if ($SearchBranchID!=""){
	$ListParam = $ListParam . "&SearchBranchID=" . $SearchBranchID;
	$AddSqlWhere = $AddSqlWhere . " and B.BranchID=$SearchBranchID ";
}



if ($SearchCenterID!=""){
	$ListParam = $ListParam . "&SearchCenterID=" . $SearchCenterID;
	$AddSqlWhere = $AddSqlWhere . " and A.CenterID=$SearchCenterID ";
}



$AddSqlWhere = $AddSqlWhere . " and A.MemberID 
										in (
												select 
													AA.MemberID 
												from Classes AA 
													inner join ClassOrders BB on AA.ClassOrderID=BB.ClassOrderID
												where 
													AA.ClassState=2 
													and AA.StartYear=".$SearchYear." 
													and AA.StartMonth=".$SearchMonth." 
													and (AA.ClassAttendState=1 or AA.ClassAttendState=2 or AA.ClassAttendState=3) 
													and BB.ClassProductID=1 
											) 
								";


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
    $ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}
$ListParam = str_replace("&", "^^", $ListParam);



$Sql = "select 
			count(*) as TotalRowCount
		from Members A 
			inner join Centers B on A.CenterID=B.CenterID 
			inner join Members BB on B.CenterID=BB.CenterID and BB.MemberLevelID=12 
			inner join Branches C on B.BranchID=C.BranchID 
			inner join Members CC on C.BranchID=CC.BranchID and CC.MemberLevelID=9 

			inner join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
			inner join Companies E on D.CompanyID=E.CompanyID 
			inner join Franchises F on E.FranchiseID=F.FranchiseID 

		where 
			".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );



$Sql = "select 
			A.*,
			B.CenterName,
			BB.MemberLoginID as CenterLoginID, 
			C.BranchName,
			CC.MemberLoginID as BranchLoginID,
			(select count(*) FROM Classes AA inner join ClassOrders BB on AA.ClassOrderID=BB.ClassOrderID where AA.ClassState=2 and AA.MemberID=A.MemberID and ( AA.ClassAttendState<>99 and AA.ClassAttendState<>4 and AA.ClassAttendState<>5 ) and BB.ClassProductID=1 and AA.StartYear=".$SearchYear." and AA.StartMonth=".$SearchMonth.") as TotalClassCount,
			(select count(*) FROM Classes AA inner join ClassOrders BB on AA.ClassOrderID=BB.ClassOrderID where AA.ClassState=2 and AA.MemberID=A.MemberID and AA.ClassAttendState<>99 and BB.ClassProductID=1 and AA.StartYear=".$SearchYear." and AA.StartMonth=".$SearchMonth." and AA.ClassAttendState=3) as AbsentClassCount,
			(select count(*) FROM Classes AA inner join ClassOrders BB on AA.ClassOrderID=BB.ClassOrderID where AA.ClassState=2 and AA.MemberID=A.MemberID and AA.ClassAttendState<>99 and BB.ClassProductID=1 and AA.StartYear=".$SearchYear." and AA.StartMonth=".$SearchMonth." and (AA.ClassAttendState=1 or AA.ClassAttendState=2)) as AttendClassCount

		from Members A 
			inner join Centers B on A.CenterID=B.CenterID 
			inner join Members BB on B.CenterID=BB.CenterID and BB.MemberLevelID=12 
			inner join Branches C on B.BranchID=C.BranchID 
			inner join Members CC on C.BranchID=CC.BranchID and CC.MemberLevelID=9

			inner join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
			inner join Companies E on D.CompanyID=E.CompanyID 
			inner join Franchises F on E.FranchiseID=F.FranchiseID 

		where 
			".$AddSqlWhere."
		order by C.BranchName asc, B.CenterName asc limit $StartRowNum, $PageListNum
		";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$수업현황[$LangID]?></h3>
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
							$Sql2 = "select 
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
										echo "<optgroup label=\"본사(운영중)\">";
									}else if ($SelectCompanyState==2){
										echo "<optgroup label=\"본사(미운영)\">";
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
							$Sql2 = "select 
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
										echo "<optgroup label=\"대표지사(운영중)\">";
									}else if ($SelectBranchGroupState==2){
										echo "<optgroup label=\"대표지사(미운영)\">";
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
										echo "<optgroup label=\"지사(운영중)\">";
									}else if ($SelectBranchState==2){
										echo "<optgroup label=\"지사(미운영)\">";
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
							if ($SearchBranchID!="" && $SearchBranchID!="-1"){
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
							
							$OldSelectBranchState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectCenterID = $Row2["CenterID"];
								$SelectCenterName = $Row2["CenterName"];
								$SelectCenterState = $Row2["CenterState"];
							
								if ($OldSelectBranchState!=$SelectCenterState){
									if ($OldSelectBranchState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectCenterState==1){
										echo "<optgroup label=\"대리점(운영중)\">";
									}else if ($SelectCenterState==2){
										echo "<optgroup label=\"대리점(미운영)\">";
									}
								}
								$OldSelectBranchState = $SelectCenterState;
							?>

							<option value="<?=$SelectCenterID?>" <?if ($SearchCenterID==$SelectCenterID){?>selected<?}?>><?=$SelectCenterName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>


					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchYear" name="SearchYear" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%;"/>
							<option value=""><?=$년도선택[$LangID]?></option>
							<?
							for ($iiii=$SearchYear-1;$iiii<=$SearchYear+1;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchMonth" name="SearchMonth" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;"/>
							<option value=""><?=$월선택[$LangID]?></option>
							<?
							for ($iiii=1;$iiii<=12;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
							<?
							}
							?>
						</select>
					</div>


					
					<div class="uk-width-medium-3-10 uk-text-center">
						<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
						<a href="javascript:ExcelDown();" class="md-btn md-btn-primary uk-margin-small-top">EXCEL DOWN</a>
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
										<th nowrap><?=$지사명[$LangID]?><br>(<?=$지사아이디[$LangID]?>)</th>
										<th nowrap><?=$대리점선명[$LangID]?><br>(<?=$대리점아이디[$LangID]?>)</th>
										<th nowrap><?=$학생이름[$LangID]?></th>
										<th nowrap><?=$영어이름[$LangID]?></th>
										<th nowrap><?=$학생아이디[$LangID]?></th>
										<th nowrap><?=$결석수[$LangID]?></th>
										<th nowrap><?=$출석수[$LangID]?>/<?=$수업수[$LangID]?><!--<br>(종료수업기준)--></th>
										<!--<th nowrap>출석수/수업수<br>(전체수업기준)</th>-->
										<th nowrap><?=$출석률[$LangID]?><!--<br>(종료수업기준)--></th>
										<!--<th nowrap>출석률<br>(전체수업기준)</th>-->
										<th nowrap><?=$출석률_결과[$LangID]?></th>
										<th nowrap><?=$출석율_그래프[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {

										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$MemberID = $Row["MemberID"];
										$MemberLoginID = $Row["MemberLoginID"];
										$MemberName = $Row["MemberName"];
										$MemberNickName = $Row["MemberNickName"];

										$CenterID = $Row["CenterID"];
										$CenterName = $Row["CenterName"];
										$CenterLoginID = $Row["CenterLoginID"];

										$BranchID = $Row["BranchID"];
										$BranchName = $Row["BranchName"];
										$BranchLoginID = $Row["BranchLoginID"];

										$TotalClassCount = $Row["TotalClassCount"];
										$AbsentClassCount = $Row["AbsentClassCount"];
										$AttendClassCount = $Row["AttendClassCount"];

										$AttendRatio = round($AttendClassCount/$TotalClassCount*100);

										if ($AttendRatio>50){
											$StrResultAttend = "<span style='color:#0000ff;'>Excellent</span>";
										}else{
											$StrResultAttend = "<span style='color:#ff0000;'>Fail</span>";
										}

									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?><!-- No --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchName?><br>(<?=$BranchLoginID?>)<!-- 지사이름 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterName?><br>(<?=$CenterLoginID?>)<!-- 대리점이름 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberName?><!-- 학생이름 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberNickName?><!-- 영어이름 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberLoginID?><!-- 학생아이디 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$AbsentClassCount?><!-- 결석수 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$AttendClassCount?>/<?=$TotalClassCount?><!-- 출석수/수업수<br>(종료수업기준) --></td>
										<!--<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?>!-- 출석수/수업수<br>(전체수업기준) --</td>-->
										<td class="uk-text-nowrap uk-table-td-center"><?=$AttendRatio?> %<!-- 출석률<br>(종료수업기준) --></td>
										<!--<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?>!-- 출석률<br>(전체수업기준) --</td>-->
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrResultAttend?><!-- 출석률 결과 --></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<div style="display:inline-block;height:20px;background-color:#0CAD4B;width:<?=150*($AttendRatio/100)?>px;margin:0px;"></div><div style="display:inline-block;height:20px;background-color:#FF2D00;width:<?=150*((100-$AttendRatio)/100)?>px;margin:0px;"></div>
											<!-- 출석율 그래프 -->
										</td>
									</tr>
									<?
										$ListCount++;
									}
									$Stmt = null;

									if ($SearchCenterID==-1){
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center" colspan="40" style="height:100px;"><?=$지사_또는_가맹점을_검색하시기_바랍니다[$LangID]?></td>
									</tr>
									<?
									}
									?>
								</tbody>
							</table>
						
						

						
						</div>


						<?php			
						include_once('./inc_pagination.php');
						?>

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
	document.SearchForm.action = "account_center_class_status.php";
	document.SearchForm.submit();
}

function ExcelDown(){
	location.href = "account_center_class_status_excel.php?SearchYear=<?=$SearchYear?>&SearchMonth=<?=$SearchMonth?>&SearchFranchiseID=<?=$SearchFranchiseID?>&SearchCompanyID=<?=$SearchCompanyID?>&SearchBranchGroupID=<?=$SearchBranchGroupID?>&SearchBranchID=<?=$SearchBranchID?>&SearchCenterID=<?=$SearchCenterID?>";
}
</script>



<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>