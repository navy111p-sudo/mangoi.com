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
$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "";
if ($type==""){
	$type = "11";
}
$SearchState = $type;

$MainMenuID = 33;

if ($SearchState=="11"){//결제완료
	$SubMenuID = 3301;
}else if ($SearchState=="21"){//배송완료
	$SubMenuID = 3302;
}else if ($SearchState=="31"){//취소완료
	$SubMenuID = 3303;
}

include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php

$AddSqlWhere = "1=1";
$AddSqlWhere2 = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchFranchiseID = isset($_REQUEST["SearchFranchiseID"]) ? $_REQUEST["SearchFranchiseID"] : "";
$SearchCompanyID = isset($_REQUEST["SearchCompanyID"]) ? $_REQUEST["SearchCompanyID"] : "";
$SearchBranchGroupID = isset($_REQUEST["SearchBranchGroupID"]) ? $_REQUEST["SearchBranchGroupID"] : "";
$SearchBranchID = isset($_REQUEST["SearchBranchID"]) ? $_REQUEST["SearchBranchID"] : "";
$SearchCenterID = isset($_REQUEST["SearchCenterID"]) ? $_REQUEST["SearchCenterID"] : "";
$SearchProductSellerID = isset($_REQUEST["SearchProductSellerID"]) ? $_REQUEST["SearchProductSellerID"] : "";

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

		
if ($SearchState=="11"){//결제완료
	$AddSqlWhere = $AddSqlWhere . " and A.ProductOrderState=21 ";//결제완료
}else if ($SearchState=="21"){
	$AddSqlWhere = $AddSqlWhere . " and (A.ProductOrderShipState=21 or A.ProductOrderShipState=31) ";//발송완료
}else if ($SearchState=="31"){
	$AddSqlWhere = $AddSqlWhere . " and A.ProductOrderState=33 ";//취소완료
}

$AddSqlWhere = $AddSqlWhere . " and A.ProductOrderState>=11 ";
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

if ($SearchProductSellerID!=""){
	$ListParam = $ListParam . "&SearchProductSellerID=" . $SearchProductSellerID;
	$AddSqlWhere = $AddSqlWhere . " and A.ProductSellerID=$SearchProductSellerID ";
}


$ListParam = $ListParam . "&type=" . $type;


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);



$Sql = "select 
				count(*) TotalRowCount 
		from ProductOrders A 
			inner join ProductSellers BB on A.ProductSellerID=BB.ProductSellerID 
			inner join Members B on A.MemberID=B.MemberID 
			inner join Centers C on B.CenterID=C.CenterID 
			inner join Branches D on C.BranchID=D.BranchID  
			inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
			inner join Companies F on E.CompanyID=F.CompanyID 
			inner join Franchises G on F.FranchiseID=G.FranchiseID 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$Sql = "
		select 
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
			(select sum(AA.ProductCount*AA.ProductPrice) from ProductOrderDetails AA where AA.ProductOrderID=A.ProductOrderID and AA.ProductOrderDetailState=1) as ProductOrderProductPrice,
			BB.ProductSellerName
		from ProductOrders A
			inner join ProductSellers BB on A.ProductSellerID=BB.ProductSellerID 
			inner join Members B on A.MemberID=B.MemberID 
			inner join Centers C on B.CenterID=C.CenterID 
			inner join Branches D on C.BranchID=D.BranchID 
			inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
			inner join Companies F on E.CompanyID=F.CompanyID 
			inner join Franchises G on F.FranchiseID=G.FranchiseID 
			inner join MemberLevels K on B.MemberLevelID=K.MemberLevelID 
		where ".$AddSqlWhere." 
		order by A.ProductOrderID desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$수강신청관리[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<input type="hidden" name="type" value="<?=$SearchState?>">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">

					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchFranchiseID==1){?>none<?}?>">
						<select id="SearchFranchiseID" name="SearchFranchiseID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="프랜차이즈선택" style="width:100%;"/>
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

					<div class="uk-width-medium-4-10"></div>


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


					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						<select id="SearchProductSellerID" name="SearchProductSellerID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="구분" style="width:100%;"/>
							<option value=""></option>
							<?
							$Sql2 = "select 
											A.* 
									from ProductSellers A 
									where A.ProductSellerState=1 
									order by A.ProductSellerOrder asc";	
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							while($Row2 = $Stmt2->fetch()) {
								$SelectProductSellerID = $Row2["ProductSellerID"];
								$SelectProductSellerName = $Row2["ProductSellerName"];
							?>

							<option value="<?=$SelectProductSellerID?>" <?if ($SearchProductSellerID==$SelectProductSellerID){?>selected<?}?>><?=$SelectProductSellerName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>


					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$학생명_또는_아이디[$LangID]?></label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>

					<!--
					<div class="uk-width-medium-1-10">
						<label for="product_search_price">Price</label>
						<input type="text" class="md-input" id="product_search_price">
					</div>
					-->

					<div class="uk-width-medium-1-10" style="display:none;">
						<div class="uk-margin-small-top">
							<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$정상[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$비정성[$LangID]?></option>
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
					

					<div class="uk-width-medium-1-10 uk-text-center">
						<a href="javascript:SearchExcel();" class="md-btn md-btn-primary uk-margin-small-top">Excel</a>
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
										<th nowrap><?=$구매일[$LangID]?></th>
										<th nowrap><?=$학생명[$LangID]?></th>
										<th nowrap><?=$아이디[$LangID]?></th>
										<th><?=$구분[$LangID]?></th>
										<th nowrap><?=$상품명[$LangID]?></th>
										<th nowrap><?=$상품가[$LangID]?></th>
										<th nowrap><?=$배송료[$LangID]?></th>
										<th nowrap><?=$결제금액[$LangID]?></th>
										<th nowrap><?=$결제상태[$LangID]?></th>
										<th nowrap><?=$올북스전송[$LangID]?></th>
										<th nowrap><?=$배송상태[$LangID]?></th>
										<th nowrap><?=$결제일[$LangID]?></th>
										<th nowrap><?=$발송일[$LangID]?></th>
										<th nowrap><?=$취소일[$LangID]?></th>
										<th nowrap><?=$배송추적[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$res_msg = $Row["res_msg"];
										$tno = $Row["tno"];

										$ProductSellerID = $Row["ProductSellerID"];
										$ProductOrderShipNumber = $Row["ProductOrderShipNumber"];
										$PayResultMsg = $Row["PayResultMsg"];
										$ProductOrderID = $Row["ProductOrderID"];

										$ProductOrderNumber = $Row["ProductOrderNumber"];
										$ProductOrderName = $Row["ProductOrderName"];
										$ProductOrderProductPrice = $Row["ProductOrderProductPrice"];
										$ProductOrderShipPrice = $Row["ProductOrderShipPrice"];
										
										$OrderPayPgFeeRatio = $Row["OrderPayPgFeeRatio"];
										$OrderPayPgFeePrice = $Row["OrderPayPgFeePrice"];
										$ProductOrderState = $Row["ProductOrderState"];
										$ProductOrderShipState = $Row["ProductOrderShipState"];
										$UseCashPaymentType = $Row["UseCashPaymentType"];

										$OrderDateTime = $Row["OrderDateTime"];
										$PaymentDateTime = $Row["PaymentDateTime"];
										$CancelRequestDateTime = $Row["CancelRequestDateTime"];
										$CancelDateTime = $Row["CancelDateTime"];
										$RefundRequestDateTime = $Row["RefundRequestDateTime"];
										$RefundDateTime = $Row["RefundDateTime"];
										$ProductOrderRegDateTime = $Row["ProductOrderRegDateTime"];
										$ProductOrderModiDateTime = $Row["ProductOrderModiDateTime"];
										$ShipDateTime = $Row["ShipDateTime"];

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

										$ProductSellerProductOrderSendCount = $Row["ProductSellerProductOrderSendCount"];
										$ProductSellerProductOrderSendDateTime = $Row["ProductSellerProductOrderSendDateTime"];

										$ProductSellerName = $Row["ProductSellerName"];

										if ($ProductSellerID==2){//올북스
											if ($ProductSellerProductOrderSendCount<1){
												$StrProductSellerProductOrderSendDateTime = "미전송";
											}else{
												$StrProductSellerProductOrderSendDateTime = $ProductSellerProductOrderSendDateTime;
											}
											
										}else{
											$StrProductSellerProductOrderSendDateTime = "-";
										}

										$ProductOrderProductPriceWhthShip = $ProductOrderShipPrice + $ProductOrderProductPrice;

										$StrUseCashPaymentType="-";
										if ($UseCashPaymentType==1){
											$StrUseCashPaymentType=$카드[$LangID];
										}else if ($UseCashPaymentType==2){
											$StrUseCashPaymentType=$실시간이체[$LangID];
										}else if ($UseCashPaymentType==3){
											$StrUseCashPaymentType=$가상계좌[$LangID];
										}else if ($UseCashPaymentType==4){
											$StrUseCashPaymentType=$계좌입금[$LangID];
										}else if ($UseCashPaymentType==5){
											$StrUseCashPaymentType=$오프라인[$LangID];
										}else if ($UseCashPaymentType==9){
											$StrUseCashPaymentType=$기타[$LangID];
										}

										//0: 삭제  1:DB만생성   11: 미결제   21:결제완료  31:취소신청   33: 취소완료   41:환불신청    43:환불완료
										if ($ProductOrderState==1){
											$StrProductOrderState = "-";
										}else if ($ProductOrderState==11){
											$StrProductOrderState = $주문완료[$LangID];
										}else if ($ProductOrderState==21){
											$StrProductOrderState = $결제완료[$LangID];
										}else if ($ProductOrderState==31){
											$StrProductOrderState = $취소요청[$LangID];
										}else if ($ProductOrderState==33){
											$StrProductOrderState = $취소완료[$LangID];
										}else if ($ProductOrderState==41){
											$StrProductOrderState = $환불요청[$LangID];
										}else if ($ProductOrderState==43){
											$StrProductOrderState = $환불완료[$LangID];
										}


										//1: 주문접수 11:배송준비중 21:발송완료 31:수취확인
										$StrProductOrderShipState = "-";
										if ($ProductOrderShipState==1){
											$StrProductOrderShipState = $주문접수[$LangID];
										}else if ($ProductOrderShipState==11){
											$StrProductOrderShipState = $배송준비중[$LangID];
										}else if ($ProductOrderShipState==21){
											$StrProductOrderShipState = $발송완료[$LangID];
										}else if ($ProductOrderShipState==31){
											$StrProductOrderShipState = $수취확인[$LangID];
										}

										$StrOrderDateTime = substr($OrderDateTime, 0,10);
										$StrPaymentDateTime = substr($PaymentDateTime, 0,10);
										$StrCancelDateTime = substr($CancelDateTime, 0,10);
										$StrShipDateTime = substr($ShipDateTime, 0,10);

									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?><br>(<?=$ProductOrderID?>)</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrOrderDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberLoginID?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ProductSellerName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenProductOrderDetail(<?=$ProductOrderID?>);"><?=$ProductOrderName?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductOrderProductPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductOrderShipPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductOrderProductPriceWhthShip,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrProductOrderState?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrProductSellerProductOrderSendDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrProductOrderShipState?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrPaymentDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<?if ($ProductOrderShipState==21 || $ProductOrderShipState==31){?>
												<?=$StrShipDateTime?>
											<?}else{?>
												-
											<?}?>
										</td>
										<td class="uk-text-nowrap uk-table-td-center">
											<?if ($ProductOrderState==33){?>
												<?=$StrCancelDateTime?>
											<?}else{?>
												-
											<?}?>
										</td>

										<td class="uk-text-nowrap uk-table-td-center">
											<?if ($ProductOrderShipState>=21){?>
											<a href="javascript:OpenShipInfo(<?=$ProductSellerID?>, '<?=$ProductOrderShipNumber?>')"><?=$배송추적[$LangID]?></a>
											<?}else{?>
											-
											<?}?>
										</td>

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

						<!--
						<div class="uk-form-row" style="text-align:center;display:none;">
							<a type="button" href="class_order_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary">신규등록</a>
						</div>
						-->

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
function OpenProductOrderDetail(ProductOrderID){
    openurl = "product_order_detail.php?ProductOrderID="+ProductOrderID;
    $.colorbox({    
        href:openurl
        ,width:"95%"
        ,height:"95%"
        ,maxWidth: "1000"
        ,maxHeight: "800"
        ,title:""
        ,iframe:true 
        ,scrolling:true
        //,onClosed:function(){location.reload(true);}
        //,onComplete:function(){alert(1);}
    });
}




</script>




<script>
function OpenInvoiceCard(tno, order_no, trade_mony){
	
	openurl = "http://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=card_bill&tno="+tno+"&order_no="+order_no+"&trade_mony="+trade_mony;
	window.open(openurl,'OpenInvoiceCard','width=470,height=815,toolbar=no,top=100,left=100');
	
}
function OpenInvoiceEtc(tno, order_no, trade_mony){
	
	openurl = "https://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=vcnt_bill&tno="+tno+"&&order_no="+order_no+"&trade_mony="+trade_mony;
	window.open(openurl,'OpenInvoiceCard','width=470,height=815,toolbar=no,top=100,left=100');
	
}
</script>


<script>
function OpenShipInfo(ProductSellerID, ProductOrderShipNumber){
	if (ProductSellerID==2){//올북스
		url = "https://www.doortodoor.co.kr/parcel/doortodoor.do?fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT&invc_no="+ProductOrderShipNumber;
		window.open(url, "doortodoor", "width=1000,height=900");
	}else{
		alert('이용중인 택배사를 개발자에게 알려주시기 바랍니다.');
	}
	
}
</script>



<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "product_order_list.php";
	document.SearchForm.submit();
}

function SearchExcel(){
	location.href = "product_order_list_excel.php?type=<?=$type?>&SearchFranchiseID=<?=$SearchFranchiseID?>&SearchCompanyID=<?=$SearchCompanyID?>&SearchBranchGroupID=<?=$SearchBranchGroupID?>&SearchBranchID=<?=$SearchBranchID?>&SearchCenterID=<?=$SearchCenterID?>&SearchText=<?=$SearchText?>&SearchState=<?=$SearchState?>&SearchProductSellerID=<?=$SearchProductSellerID?>";
}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>