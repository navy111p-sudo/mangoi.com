<?php
  error_reporting( E_ALL );
  ini_set( "display_errors", 1 );
?>
<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');


if ($_LINK_ADMIN_LEVEL_ID_>10){
	header("Location: center_form.php?CenterID=".$_LINK_ADMIN_CENTER_ID_); 
	exit;
}

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
$MainMenuID = 21;
$SubMenuID = 2109;
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
$SearchOnlineSiteID = isset($_REQUEST["SearchOnlineSiteID"]) ? $_REQUEST["SearchOnlineSiteID"] : "";
$SearchManagerID = isset($_REQUEST["SearchManagerID"]) ? $_REQUEST["SearchManagerID"] : "";

$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

$CellID = isset($_REQUEST["CellID"]) ? $_REQUEST["CellID"] : "";
$CellOrder = isset($_REQUEST["CellOrder"]) ? $_REQUEST["CellOrder"] : "";
$OldCellID = isset($_REQUEST["OldCellID"]) ? $_REQUEST["OldCellID"] : "";
$OldCellOrder = isset($_REQUEST["OldCellOrder"]) ? $_REQUEST["OldCellOrder"] : "";


if ($CellID==""){
	$CellID = "1";
	$OldCellID = "1";
}

if ($CellOrder==""){
	$CellOrder = "1";
}

//================== 서치폼 감추기 =================
$HideSearchCenterID = 0;
$HideSearchBranchID = 0;
$HideSearchBranchGroupID = 0;
$HideSearchCompanyID = 0;
$HideSearchFranchiseID = 0;
$HideSearchOnlineSiteID = 0;
$HideSearchManagerID = 0;

if ($_LINK_ADMIN_LEVEL_ID_==0 or $_LINK_ADMIN_LEVEL_ID_==1){//마스터
	//모두허용
}else if ($_LINK_ADMIN_LEVEL_ID_==3 or $_LINK_ADMIN_LEVEL_ID_==4){//프랜차이즈 관리자
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	
	$HideSearchFranchiseID = 1;
	$HideSearchOnlineSiteID = 1;
	$HideSearchManagerID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==6 or $_LINK_ADMIN_LEVEL_ID_==7){//대표지사 관리자
	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	
	$HideSearchBranchGroupID = 1;
	$HideSearchCompanyID = 1;
	$HideSearchFranchiseID = 1;
	$HideSearchOnlineSiteID = 1;
	$HideSearchManagerID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==9 or $_LINK_ADMIN_LEVEL_ID_==10){//지사 관리자
	$SearchBranchID = $_LINK_ADMIN_BRANCH_ID_;
	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	
	$HideSearchBranchID = 1;
	$HideSearchBranchGroupID = 1;
	$HideSearchCompanyID = 1;
	$HideSearchFranchiseID = 1;
	$HideSearchOnlineSiteID = 1;
	$HideSearchManagerID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==12 or $_LINK_ADMIN_LEVEL_ID_==13){//대리점 관리자
	//폼으로 넘김
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
	$AddSqlWhere = $AddSqlWhere . " and A.CenterState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.CenterState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and B.BranchState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and C.BranchGroupState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and D.CompanyState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and H.FranchiseState<>0 ";

$AddSqlWhere = $AddSqlWhere . " and (E.OnlineSiteState<>0 or E.OnlineSiteState is null)";
$AddSqlWhere = $AddSqlWhere . " and (F.ManagerState<>0 or F.ManagerState is null)";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.CenterName like '%".$SearchText."%' or A.CenterManagerName like '%".$SearchText."%') ";
}

if ($SearchFranchiseID!=""){
	$ListParam = $ListParam . "&SearchFranchiseID=" . $SearchFranchiseID;
	$AddSqlWhere = $AddSqlWhere . " and D.FranchiseID=$SearchFranchiseID ";
}

if ($SearchCompanyID!=""){
	$ListParam = $ListParam . "&SearchCompanyID=" . $SearchCompanyID;
	$AddSqlWhere = $AddSqlWhere . " and C.CompanyID=$SearchCompanyID ";
}

if ($SearchBranchGroupID!=""){
	$ListParam = $ListParam . "&SearchBranchGroupID=" . $SearchBranchGroupID;
	$AddSqlWhere = $AddSqlWhere . " and B.BranchGroupID=$SearchBranchGroupID ";
}

if ($SearchBranchID!=""){
	$ListParam = $ListParam . "&SearchBranchID=" . $SearchBranchID;
	$AddSqlWhere = $AddSqlWhere . " and A.BranchID=$SearchBranchID ";
}

if ($SearchOnlineSiteID!=""){
	$ListParam = $ListParam . "&SearchOnlineSiteID=" . $SearchOnlineSiteID;
	$AddSqlWhere = $AddSqlWhere . " and A.OnlineSiteID=$SearchOnlineSiteID ";
}

if ($SearchManagerID!=""){
	$ListParam = $ListParam . "&SearchManagerID=" . $SearchManagerID;
	$AddSqlWhere = $AddSqlWhere . " and A.ManagerID=$SearchManagerID ";
}


$AddSqlWhere2 = " 1=1 ";
if ($SearchYear!=""){
	$ListParam = $ListParam . "&SearchYear=" . $SearchYear;
	$AddSqlWhere2 = $AddSqlWhere2 . " and date_format(AAA.ClassOrderPaymentDateTime, '%Y')='".$SearchYear."' ";
}

if ($SearchMonth!=""){
	$ListParam = $ListParam . "&SearchMonth=" . $SearchMonth;
	$AddSqlWhere2 = $AddSqlWhere2 . " and date_format(AAA.ClassOrderPaymentDateTime, '%m')='".substr("0".$SearchMonth,-2)."' ";
}



$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from Centers A 
			inner join Branches B on A.BranchID=B.BranchID 
			inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
			inner join Companies D on C.CompanyID=D.CompanyID 
			left outer join OnlineSites E on A.OnlineSiteID=E.OnlineSiteID 
			left outer join Managers F on A.ManagerID=F.ManagerID 
			inner join Franchises H on D.FranchiseID=H.FranchiseID 
			inner join Members G on A.CenterID=G.CenterID and G.MemberLevelID=12 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$ViewTable = "
		select 
			A.* ,
			AES_DECRYPT(UNHEX(A.CenterPhone1),:EncryptionKey) as DecCenterPhone1,
			AES_DECRYPT(UNHEX(A.CenterPhone2),:EncryptionKey) as DecCenterPhone2,
			B.BranchName, 
			C.BranchGroupName,
			D.CompanyName,
			ifnull(E.OnlineSiteName,'미지정') as OnlineSiteName,
			ifnull(F.ManagerName,'미지정') as ManagerName,
			G.MemberLoginID,
			G.MemberID,
			H.FranchiseName,			

			(select count(*) from ClassOrders AAA inner join Members BBB on AAA.MemberID=BBB.MemberID inner join Centers CCC on BBB.CenterID=CCC.CenterID where CCC.CenterID=A.CenterID) as TotalCenterClassOrder,

			(select count(*) from ClassOrders AAA inner join Members BBB on AAA.MemberID=BBB.MemberID inner join Centers CCC on BBB.CenterID=CCC.CenterID where CCC.CenterID=A.CenterID and AAA.ClassOrderState=3) as TotalCenterEndClassOrder

		from Centers A 
			inner join Branches B on A.BranchID=B.BranchID 
			inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
			inner join Companies D on C.CompanyID=D.CompanyID 
			left outer join OnlineSites E on A.OnlineSiteID=E.OnlineSiteID 
			left outer join Managers F on A.ManagerID=F.ManagerID 
			inner join Franchises H on D.FranchiseID=H.FranchiseID 
			inner join Members G on A.CenterID=G.CenterID and G.MemberLevelID=12 
		where ".$AddSqlWhere." 
		order by A.CenterOrder desc";// limit $StartRowNum, $PageListNum";

$AddSqlWhere3 = ($CellOrder==1)? "desc":"asc";
if ($CellID=="1"){
	$Sql = "select * from ($ViewTable) V order by V.TotalCenterClassOrder ".$AddSqlWhere3;
} else if($CellID=="2"){
	$Sql = "select * from ($ViewTable) V order by V.TotalCenterEndClassOrder ".$AddSqlWhere3;
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$대리점정산[$LangID]?></h3>
		<form name="SearchForm" method="get">
		<input type="hidden" name="CellID" id="CellID" value="<?=$CellID?>"/>
		<input type="hidden" name="CellOrder" id="CellOrder" value="<?=$CellOrder?>"/>
		<input type="hidden" name="OldCellID" id="OldCellID" value="<?=$OldCellID?>"/>
		<input type="hidden" name="OldCellOrder" id="OldCellOrder" value="<?=$OldCellOrder?>"/>

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

					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchOnlineSiteID==1){?>none<?}?>;">
						<select id="SearchOnlineSiteID" name="SearchOnlineSiteID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$사이트선택[$LangID]?>" style="width:100%;"/>
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
									from OnlineSites A 
										inner join Franchises B on A.FranchiseID=B.FranchiseID 
									where A.OnlineSiteState<>0 and B.FranchiseState<>0 ".$AddWhere2." 
									order by A.OnlineSiteState asc, A.OnlineSiteName asc";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldSelectOnlineSiteState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectOnlineSiteID = $Row2["OnlineSiteID"];
								$SelectOnlineSiteName = $Row2["OnlineSiteName"];
								$SelectOnlineSiteState = $Row2["OnlineSiteState"];
							
								if ($OldSelectOnlineSiteState!=$SelectOnlineSiteState){
									if ($OldSelectOnlineSiteState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectOnlineSiteState==1){
										echo "<optgroup label=\"".$사이트_운영중[$LangID]."\">";
									}else if ($SelectOnlineSiteState==2){
										echo "<optgroup label=\"".$사이트_미운영[$LangID]."\">";
									}
								}
								$OldSelectOnlineSiteState = $SelectOnlineSiteState;
							?>

							<option value="<?=$SelectOnlineSiteID?>" <?if ($SearchOnlineSiteID==$SelectOnlineSiteID){?>selected<?}?>><?=$SelectOnlineSiteName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>
					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchManagerID==1){?>none<?}?>;">
						<select id="SearchManagerID" name="SearchManagerID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$영업본부선택[$LangID]?>" style="width:100%;"/>
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
									from Managers A 
										inner join Franchises B on A.FranchiseID=B.FranchiseID 
									where A.ManagerState<>0 and B.FranchiseState<>0 ".$AddWhere2." 
									order by A.ManagerState asc, A.ManagerName asc";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldSelectManagerState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectManagerID = $Row2["ManagerID"];
								$SelectManagerName = $Row2["ManagerName"];
								$SelectManagerState = $Row2["ManagerState"];
							
								if ($OldSelectManagerState!=$SelectManagerState){
									if ($OldSelectManagerState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectManagerState==1){
										echo "<optgroup label=\"".$영업본부_운영중[$LangID]."\">";
									}else if ($SelectManagerState==2){
										echo "<optgroup label=\"".$영업본부_미운영[$LangID]."\">";
									}
								}
								$OldSelectManagerState = $SelectManagerState;
							?>

							<option value="<?=$SelectManagerID?>" <?if ($SearchManagerID==$SelectManagerID){?>selected<?}?>><?=$SelectManagerName?></option>
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


					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						<select id="SearchYear" name="SearchYear" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%;"/>
							<option value=""><?=$년도선택[$LangID]?></option>
							<?
							for ($iiii=2018;$iiii<=date("Y");$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-2-10" style="padding-top:7px;">
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


					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$대리점명_또는_관리자명[$LangID]?></label>
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
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>>전체</option>
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
							
							<div style="text-align:right;">※ 평균 수강개월 계산은 15일 이상 지속된 수업만 집계합니다.</div>
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap><?=$지사명[$LangID]?></th>
										<th nowrap><?=$대리점명[$LangID]?></th>
										<th nowrap><a href="javascript:SetOrderList(1);" ><?=$누적전체강의[$LangID]?> <?if ($CellOrder=="1" && $CellID=="1"){?>▼<?} else if($CellOrder=="2" && $CellID=="1") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(2);" ><?=$누적종료강의[$LangID]?> <?if ($CellOrder=="1" && $CellID=="2"){?>▼<?} else if($CellOrder=="2" && $CellID=="2") {?>▲<?}?></a></th>
										<th nowrap><?=$종료율[$LangID]?></th>
										<th nowrap><?=$평균수강개월[$LangID]?></th>
										<th nowrap><?=$연장율[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									$SumTotalCenterClassOrder = 0;
									$SumTotalCenterEndClassOrder = 0;
									$TotalCenterEndClassPercent = 0;
									$SumTotalCenterEndClassPercent = 0;
									$SumAvgStudyMonth = 0;
									$SumCenterClassRelRatio = 0;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$CenterID = $Row["CenterID"];
										$CenterName = $Row["CenterName"];
										$CenterManagerName = $Row["CenterManagerName"];
										$CenterPhone1 = $Row["DecCenterPhone1"];
										$CenterPhone2 = $Row["DecCenterPhone2"];
										$CenterState = $Row["CenterState"];
										$BranchName = $Row["BranchName"];
										$BranchGroupName = $Row["BranchGroupName"];
										$CompanyName = $Row["CompanyName"];
										$OnlineSiteName = $Row["OnlineSiteName"];
										$ManagerName = $Row["ManagerName"];
										$MemberID = $Row["MemberID"];
										$MemberLoginID = $Row["MemberLoginID"];
										$FranchiseName = $Row["FranchiseName"];
										$CenterFreeTrialCount = $Row["CenterFreeTrialCount"];
										$CenterPricePerTime = $Row["CenterPricePerTime"];
										
										if ($CenterState==1){
											$StrCenterState = "<span class=\"ListState_1\">운영중</span>";
										}else if ($CenterState==2){
											$StrCenterState = "<span class=\"ListState_2\">미운영</span>";
										}

										$TotalCenterClassOrder = $Row["TotalCenterClassOrder"];
										$TotalCenterEndClassOrder = $Row["TotalCenterEndClassOrder"];


										$ViewTable2 = "
											select
												A.ClassOrderStartDate,
												case when ClassOrderEndDate is null 
													then date_format(now(), '%Y-%m-%d')
												else
													A.ClassOrderEndDate 
												end ClassOrderEndDate
													
											from ClassOrders A 
												inner join Members B on A.MemberID=B.MemberID 
												inner join Centers C on B.CenterID=C.CenterID 	
											where 
												B.CenterID=$CenterID 
												and A.ClassOrderState>=1 and A.ClassOrderState<=4 
												and A.ClassProgress=11 
										";
										
										$Sql2 = "
											select 
												round(datediff(V.ClassOrderEndDate, V.ClassOrderStartDate)/30) as AvgStudyMonth
											from ($ViewTable2) V 
											where datediff(V.ClassOrderEndDate, V.ClassOrderStartDate)>=15
										";

										//echo $Sql2;
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$AvgStudyMonth = $Row2["AvgStudyMonth"];


										$Sql2 = "
											select 
												count(*) as CenterClassCount
											from ClassOrderPayDetails AA 
												inner join ClassOrderPays AAA on AA.ClassOrderPayID=AAA.ClassOrderPayID 
												inner join ClassOrders A on AA.ClassOrderID=A.ClassOrderID  
												inner join Members B on A.MemberID=B.MemberID 
												inner join Centers C on B.CenterID=C.CenterID 
											where C.CenterID=$CenterID 
												and (AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41)
												and AA.ClassOrderPayDetailType=1
										";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$CenterClassCount1 = $Row2["CenterClassCount"];


										//LMS 에서 수강신청하고 아직 결제 안한것
										$Sql2 = "
											select 
												count(*) as CenterClassCount
											from ClassOrders AA 
												inner join Members B on AA.MemberID=B.MemberID 
												inner join Centers C on B.CenterID=C.CenterID 
											where C.CenterID=$CenterID 
												and AA.ClassProgress=11 
												and ClassOrderID not in (select AAAAA.ClassOrderID from ClassOrderPayDetails AAAAA inner join ClassOrderPays BBBBB on AAAAA.ClassOrderPayID=BBBBB.ClassOrderPayID and BBBBB.ClassOrderPayProgress>=21) 
												and ClassOrderID not in (select AAAAA.ClassOrderID from ClassOrderPayB2bs    AAAAA inner join ClassOrderPays BBBBB on AAAAA.ClassOrderPayID=BBBBB.ClassOrderPayID and BBBBB.ClassOrderPayProgress>=21)
										";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$CenterClassCount1 = $CenterClassCount1 + $Row2["CenterClassCount"];



										$Sql2 = "
											select 
												count(*) as CenterClassCount
											from ClassOrderPayDetails AA 
												inner join ClassOrderPays AAA on AA.ClassOrderPayID=AAA.ClassOrderPayID 
												inner join ClassOrders A on AA.ClassOrderID=A.ClassOrderID  
												inner join Members B on A.MemberID=B.MemberID 
												inner join Centers C on B.CenterID=C.CenterID 
											where C.CenterID=$CenterID 
												and (AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41) 
												and AA.ClassOrderPayDetailType=2
										";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$CenterClassCount2 = $Row2["CenterClassCount"];


										//단체 연장
										$Sql2 = "
											select 
												count(*) as CenterClassCount
											from ClassOrderPayB2bs AA 
												inner join ClassOrderPays AAA on AA.ClassOrderPayID=AAA.ClassOrderPayID 
												inner join ClassOrders A on AA.ClassOrderID=A.ClassOrderID  
												inner join Members B on A.MemberID=B.MemberID 
												inner join Centers C on B.CenterID=C.CenterID 
											where C.CenterID=$CenterID 
												and (AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41) 
										";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$CenterClassCount2 = $CenterClassCount2 + $Row2["CenterClassCount"];


										if ($CenterClassCount1!=0){
											$CenterClassRelRatio = 100 * $CenterClassCount2 / $CenterClassCount1;
										}else{
											$CenterClassRelRatio = 0;
										}

										$SumTotalCenterClassOrder = $SumTotalCenterClassOrder + $TotalCenterClassOrder;
										$SumTotalCenterEndClassOrder = $SumTotalCenterEndClassOrder + $TotalCenterEndClassOrder;
										$SumAvgStudyMonth = $SumAvgStudyMonth + $AvgStudyMonth;
										$SumCenterClassRelRatio = $SumCenterClassRelRatio + $CenterClassRelRatio;
										if($TotalCenterClassOrder>0) {
											$TotalCenterEndClassPercent = number_format(($TotalCenterEndClassOrder/$TotalCenterClassOrder)*100,0);
											$SumTotalCenterEndClassPercent = $SumTotalCenterEndClassPercent + $TotalCenterEndClassPercent;
											$TotalCenterEndClassPercent .= "%";
										} else {
											$TotalCenterEndClassPercent = "-";
										}

									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalCenterClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalCenterEndClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<?=$TotalCenterEndClassPercent?>
										</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$AvgStudyMonth?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=round($CenterClassRelRatio,0)?>%</td>
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;


									if ($ListCount==1){
										$AvgTotalCenterClassOrder = 0;
										$AvgTotalCenterEndClassOrder = 0;
										$AvgTotalCenterEndClassPercent = 0;
										$AvgAvgStudyMonth = 0;
										$AvgCenterClassRelRatio = 0;
									}else{
										$AvgTotalCenterClassOrder = $SumTotalCenterClassOrder / ($ListCount-1);
										$AvgTotalCenterEndClassOrder = $SumTotalCenterEndClassOrder / ($ListCount-1);
										$AvgTotalCenterEndClassPercent = $SumTotalCenterEndClassPercent / ($ListCount-1);
										$AvgAvgStudyMonth = $SumAvgStudyMonth / ($ListCount-1);
										$AvgCenterClassRelRatio = $SumCenterClassRelRatio / ($ListCount-1);
									}
									?>

									<tr style="background-color:#f1f1f1;">
										<td class="uk-text-nowrap uk-table-td-center" colspan="3"><?=$합계[$LangID]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTotalCenterClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTotalCenterEndClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"></td>
										<td class="uk-text-nowrap uk-table-td-center"></td>
										<td class="uk-text-nowrap uk-table-td-center"></td>
									</tr>
									<tr style="background-color:#f1f1f1;">
										<td class="uk-text-nowrap uk-table-td-center" colspan="3"><?=$평균[$LangID]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgTotalCenterClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgTotalCenterEndClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgTotalCenterEndClassPercent,0)?>%</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgAvgStudyMonth,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format(round($AvgCenterClassRelRatio,0),0)?>%</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="uk-form-row" style="text-align:left;margin-top:20px;">
                            <a type="button" href="javascript:SendMessageForm()" class="md-btn md-btn-primary"><?=$메시지전송[$LangID]?></a>
                        </div>
						

						<?php			
						//include_once('./inc_pagination.php');
						?>



					</div>
				</div>
			</div>
		</div>

	</div>
</div>


<script>
var ListCount = <?=$ListCount-1?>;
function CheckListAll(obj){

	for (ii=1;ii<=ListCount;ii++){
		if (obj.checked){
			document.getElementById("CheckBox_"+ii).checked = true;
		}else{
			document.getElementById("CheckBox_"+ii).checked = false;
		}	
	}
}

function SendMessageForm(){

	if (ListCount==0){
		alert("<?=$선택한_목록이_없습니다[$LangID]?>");
	}else{
		
		MemberIDs = "|";
		for (ii=1;ii<=ListCount;ii++){
			if (document.getElementById("CheckBox_"+ii).checked){
				MemberIDs = MemberIDs + document.getElementById("CheckBox_"+ii).value + "|";
			}	
		}

	
		if (MemberIDs=="|"){
			alert("<?=$선택한_목록이_없습니다[$LangID]?>");
		}else{
			openurl = "send_message_log_multi_form.php?MemberIDs="+MemberIDs;
			$.colorbox({	
				href:openurl
				,width:"95%" 
				,height:"95%"
				,maxWidth: "850"
				,maxHeight: "750"
				,title:""
				,iframe:true 
				,scrolling:true
				//,onClosed:function(){location.reload(true);}
				//,onComplete:function(){alert(1);}
			});
		}
	}
}
</script>



<?
include_once('./inc_common_list_js.php');
?>
<!-- ============== only this page js ============== -->


<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SetOrderList(ID) {
	// .value 를 붙이면 단순 문자열 또는 숫자로 인식.
	var CellID = document.SearchForm.CellID;
	var CellOrder = document.SearchForm.CellOrder;
	var OldCellID = document.SearchForm.OldCellID;
	var OldCellOrder = document.SearchForm.OldCellOrder;

	// 클릭했었던 값은 Old 에 대입
	OldCellOrder.value = CellOrder.value;
	OldCellID.value = CellID.value;
	CellID.value = ID;

	//alert("CellID : "+CellID.value);
	//alert("CellOrder : "+CellOrder.value);
	//alert("OldCellID : "+OldCellID.value);
	//alert("OldCellOrder : "+OldCellOrder.value);
	//alert(document.SearchForm.OldCellOrder.value);

	// 동일한 CellID 를 눌렀다면 
	if (CellID.value==OldCellID.value) {
		// 기존값이 1,2 인지 확인 후 2 또는 1 대입
		CellOrder.value = (OldCellOrder.value==1)? 2:1;
		//alert("after if : "+CellOrder.value);
	} else { // 기존 Cell 과 누른 Cell 이 같지 않다면
		CellOrder.value = 1;
		//alert("after if : "+CellOrder.value);
	}




	SearchSubmit();
}

function SearchSubmit(){
	document.SearchForm.action = "account_center_study.php";
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