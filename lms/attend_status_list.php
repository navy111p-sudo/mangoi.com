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
$SubMenuID = 1406;
include_once('./inc_top.php'); 
include_once('./inc_menu_left.php');
#===== 모바일 결제창에서 결제하지 않고 다시 돌아올경우 셀프페이에 남겨진 고유코드를 다시 재사용하기위한 변수 입니다. =====#
$ReqUrl = isset($_REQUEST["ReqUrl"]) ? $_REQUEST["ReqUrl"] : ""; //' 결제창에서 결제실행전 돌아올때
?>



<?php
$EduCenterID = 1;

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

$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

if ($SearchYear==""){
	$SearchYear = date("Y");
}
if ($SearchMonth==""){
	$SearchMonth = date("m");
}

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
	$AddSqlWhere = $AddSqlWhere . " and A.MemberState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and (A.MemberState<>0 or A.MemberState is null) ";
$AddSqlWhere = $AddSqlWhere . " and (B.CenterState<>0 or B.CenterState is null)";
$AddSqlWhere = $AddSqlWhere . " and (C.BranchState<>0 or C.BranchState is null)";
$AddSqlWhere = $AddSqlWhere . " and (D.BranchGroupState<>0 or D.BranchGroupState is null)";
$AddSqlWhere = $AddSqlWhere . " and (E.CompanyState<>0 or E.CompanyState is null)";
$AddSqlWhere = $AddSqlWhere . " and (F.FranchiseState<>0 or F.FranchiseState is null)";

$AddSqlWhere = $AddSqlWhere . " and A.MemberLevelID=19 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.MemberName like '%".$SearchText."%' or A.MemberLoginID like '%".$SearchText."%' or A.MemberNickName like '%".$SearchText."%') ";
}

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
		from Members A 
			left outer join Centers B on A.CenterID=B.CenterID 
			left outer join Branches C on B.BranchID=C.BranchID 
			left outer join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
			left outer join Companies E on D.CompanyID=E.CompanyID 
			left outer join Franchises F on E.FranchiseID=F.FranchiseID 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );

//$AddPointSqlWhere = " and (AA.MemberPointTypeID<>3 or (AA.MemberPointTypeID=3 and ( BB.OrderProgress=21 or BB.OrderProgress=31 or BB.OrderProgress=41 ))) "; 

$Sql = "
		select 
			A.* ,
			AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
			AES_DECRYPT(UNHEX(A.MemberPhone2),:EncryptionKey) as DecMemberPhone2,
			AES_DECRYPT(UNHEX(A.MemberPhone3),:EncryptionKey) as DecMemberPhone3,
			B.CenterID as JoinCenterID,
			B.CenterName as JoinCenterName,
			C.BranchID as JoinBranchID,
			C.BranchName as JoinBranchName, 
			D.BranchGroupID as JoinBranchGroupID,
			D.BranchGroupName as JoinBranchGroupName,
			E.CompanyID as JoinCompanyID,
			E.CompanyName as JoinCompanyName,
			F.FranchiseName
		

		from Members A 
			left outer join Centers B on A.CenterID=B.CenterID 
			left outer join Branches C on B.BranchID=C.BranchID 
			left outer join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
			left outer join Companies E on D.CompanyID=E.CompanyID 
			left outer join Franchises F on E.FranchiseID=F.FranchiseID 
		where ".$AddSqlWhere." 
		order by A.MemberID desc, A.MemberRegDateTime desc";// limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$출결현황[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchFranchiseID==1){?>none<?}?>;">
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

					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
						<select id="SearchYear" name="SearchYear" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%;"/>
							<option value=""><?=$년도선택[$LangID]?></option>
							<?
							for ($iiii=2018;$iiii<=2020;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchYear==$iiii){?>selected<?}?>><?=$iiii?><?=$년[$LangID]?></option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
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
						<label for="SearchText"><?=$학생명_또는_아이디[$LangID]?></label>
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
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$정상[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$휴면[$LangID]?></option>
								<option value="3" <?if ($SearchState=="3"){?>selected<?}?>><?=$탈퇴[$LangID]?></option>
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
										<!--<th nowrap><input name="CheckBoxAll" type="checkbox" onclick="CheckListAll(this)"></th>-->
										<th nowrap>No</th>
										<th nowrap>
										<?=$학생명[$LangID]?>(<?=$아이디[$LangID]?>) 
										</th>
										<?
										$MonthEndDay = date('t', strtotime($SearchYear."-".substr("0".$SearchMonth,-2)."-01"));
										for ($ii=1;$ii<=$MonthEndDay;$ii++){
										?>
										<th nowrap><?=$ii?>일</th>
										<?
										}
										?>
										<th nowrap>출석율</th>

									</tr>
								</thead>
								<tbody>
									
								<?php
								$ListCount = 1;
								if ($SearchBranchID!="" || $SearchCenterID!=""){

									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$MemberID = $Row["MemberID"];
										$MemberName = $Row["MemberName"];
										$MemberLoginID = $Row["MemberLoginID"];

										
									?>
									<tr>
										<!--<td class="uk-text-nowrap uk-table-td-center"><input name="CheckBox_<?=$ListCount?>" id="CheckBox_<?=$ListCount?>" type="checkbox" value="<?=$MemberID?>"></td>-->
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<a href="student_form.php?MemberID=<?=$MemberID?>" target="student_info"><?=$MemberName?>(<?=$MemberLoginID?>)</a> 
											<a href="javascript:OpenStudentCalendar(<?=$MemberID?>);"><i class="material-icons">date_range</i></a>	
										</td>
										<?
										$MonthEndDay = date('t', strtotime($SearchYear."-".substr("0".$SearchMonth,-2)."-01"));
										$ClassTotalCount = 0;
										$ClassAttendCount = 0;
										for ($ii=1;$ii<=$MonthEndDay;$ii++){
										?>
										<td nowrap class="uk-text-nowrap uk-table-td-center" style="font-size:11px;">
										<?
											$SelectDate = $SearchYear."-".substr("0".$SearchMonth,-2)."-".$ii;	
											$SelectWeek = date('w', strtotime($SelectDate));

											//====================== 에듀센터 휴무 검색 ======================
											$TodayIsHoliday = 0;
											$Sql3 = "
												select 
													A.EduCenterHolidayID
												from EduCenterHolidays A
												where A.EduCenterHolidayDate=:EduCenterHolidayDate
													and A.EduCenterID=:EduCenterID
													and A.EduCenterHolidayState=1 
											";
											$Stmt3 = $DbConn->prepare($Sql3);
											$Stmt3->bindParam(':EduCenterHolidayDate', $SelectDate);
											$Stmt3->bindParam(':EduCenterID', $EduCenterID);
											$Stmt3->execute();
											$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
											$Row3 = $Stmt3->fetch();
											$Stmt3 = null;
											$EduCenterHolidayID = $Row3["EduCenterHolidayID"];
											if ($EduCenterHolidayID){
												
												$TodayIsHoliday = 1;
											
											}
											//====================== 에듀센터 휴무 검색 ======================											

											$Sql2 = "

												select 
													COS.ClassMemberType,
													COS.ClassOrderSlotType,
													COS.ClassOrderSlotType2,
													COS.ClassOrderSlotDate,
													COS.TeacherID,
													COS.ClassOrderSlotMaster,
													COS.StudyTimeWeek,
													COS.StudyTimeHour,
													COS.StudyTimeMinute,
													COS.ClassOrderSlotState,
													concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0)) as ClassStartTime, 

													CO.ClassOrderID,
													CO.ClassProductID,
													CO.ClassOrderTimeTypeID,
													CO.MemberID,

													ifnull(CLS.ClassID,0) as ClassID,
													CLS.TeacherID as ClassTeacherID,
													CLS.ClassLinkType,
													CLS.StartDateTime,
													CLS.StartDateTimeStamp,
													CLS.StartYear,
													CLS.StartMonth,
													CLS.StartDay,
													CLS.StartHour,
													CLS.StartMinute,
													CLS.EndDateTime,
													CLS.EndDateTimeStamp,
													CLS.EndYear,
													CLS.EndMonth,
													CLS.EndDay,
													CLS.EndHour,
													CLS.EndMinute,
													CLS.CommonUseClassIn,
													CLS.CommonShClassCode,
													CLS.CommonCiCourseID,
													CLS.CommonCiClassID,
													CLS.CommonCiTelephoneTeacher,
													CLS.CommonCiTelephoneStudent,
													ifnull(CLS.ClassAttendState, -1) as ClassAttendState,
													CLS.ClassAttendStateMemberID,
													ifnull(CLS.ClassState, 0) as ClassState,
													CLS.BookVideoID,
													CLS.BookQuizID,
													CLS.BookScanID,
													CLS.ClassRegDateTime,
													CLS.ClassModiDateTime,

													MB.MemberName,
													MB.MemberNickName,
													MB.MemberLoginID, 
													MB.MemberLevelID,
													MB.MemberCiTelephone,
													TEA.TeacherName,
													MB2.MemberLoginID as TeacherLoginID, 
													MB2.MemberCiTelephone as TeacherCiTelephone,
													CT.CenterID as JoinCenterID,
													CT.CenterName as JoinCenterName,
													BR.BranchID as JoinBranchID,
													BR.BranchName as JoinBranchName, 
													BRG.BranchGroupID as JoinBranchGroupID,
													BRG.BranchGroupName as JoinBranchGroupName,
													COM.CompanyID as JoinCompanyID,
													COM.CompanyName as JoinCompanyName,
													FR.FranchiseName,
													MB3.MemberLoginID as CenterLoginID,
													TEA2.TeacherName as ClassTeacherName

												from ClassOrderSlots COS 

														left outer join Classes CLS on COS.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$SearchYear." and CLS.StartMonth=".$SearchMonth." and CLS.StartDay=".$ii." and CLS.StartHour=COS.StudyTimeHour and CLS.StartMinute=COS.StudyTimeMinute and CLS.ClassAttendState<>99 

														inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 




														inner join Members MB on CO.MemberID=MB.MemberID 
														inner join Centers CT on MB.CenterID=CT.CenterID 
														inner join Branches BR on CT.BranchID=BR.BranchID 
														inner join BranchGroups BRG on BR.BranchGroupID=BRG.BranchGroupID 
														inner join Companies COM on BRG.CompanyID=COM.CompanyID 
														inner join Franchises FR on COM.FranchiseID=FR.FranchiseID 
														inner join Teachers TEA on COS.TeacherID=TEA.TeacherID 
														left outer join Teachers TEA2 on CLS.TeacherID=TEA2.TeacherID 
														inner join Members MB2 on TEA.TeacherID=MB2.TeacherID 
														left outer join Members MB3 on CT.CenterID=MB3.CenterID and MB3.MemberLevelID=12 

												where TEA.TeacherState=1 
														and CO.MemberID=".$MemberID." 
														and COS.ClassOrderSlotMaster=1 
														and ( 
																(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

																or 
																(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

																or 
																(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

																or 
																(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

																or 
																(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SelectDate."')=0 )   
															)  
														and COS.ClassOrderSlotState=1 
														and CO.ClassProgress=11 
														and (
																CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or CO.ClassOrderState=5 or CO.ClassOrderState=6
																or 
																(CO.ClassOrderState=3 and CLS.ClassID is not null)
																
															)

														and (
																(CT.CenterPayType=1 and MB.MemberPayType=0 and ((CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=5 or CO.ClassOrderState=6) or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$SelectDate."')>=0) )) 
																or 
																( 
																	( CT.CenterPayType=2 and datediff(CO.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
																	or 
																	( CT.CenterPayType=1 and MB.MemberPayType=1 and datediff(CO.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
																)
																or
																CO.ClassProductID=2 
																or 
																CO.ClassProductID=3 
																or 
																(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SelectDate."')=0) 
															)
											";
														//결제 안해도 관리자가 등록한 수업 진행가능하도록 주석 처리
														//inner join ClassOrderPayDetails COPD on COS.ClassOrderID=COPD.ClassOrderID 
														//inner join ClassOrderPays COP on COPD.ClassOrderPayID=COP.ClassOrderPayID and (COP.ClassOrderPayProgress=21 or COP.ClassOrderPayProgress=31 or COP.ClassOrderPayProgress=41)

											$Stmt2 = $DbConn->prepare($Sql2);
											$Stmt2->execute();
											$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										
											
											while($Row2 = $Stmt2->fetch()) {


												$ClassProductID = $Row2["ClassProductID"];
												$ClassOrderSlotType = $Row2["ClassOrderSlotType"];
												$ClassOrderSlotType2 = $Row2["ClassOrderSlotType2"];

												$ClassMemberType = $Row2["ClassMemberType"];
												$StudyTimeHour = $Row2["StudyTimeHour"];
												$StudyTimeMinute = $Row2["StudyTimeMinute"];
												
												$ClassID = $Row2["ClassID"];
												$ClassOrderTimeTypeID = $Row2["ClassOrderTimeTypeID"];
												
												$TeacherName = $Row2["TeacherName"];
												$StartDateTime = $Row2["StartDateTime"];
												$ClassAttendState = $Row2["ClassAttendState"];
												$ClassState = $Row2["ClassState"];
												$StartHour = $Row2["StartHour"];
												$StartMinute = $Row2["StartMinute"];
												$EndHour = $Row2["EndHour"];
												$EndMinute = $Row2["EndMinute"];

												if ($ClassAttendState==-1){//1:출석 2:지각 3:결석 4:학생연기 5:강사연기 6:학생취소 7:강사취소
													$StrClassAttendState = "<div style='display:inline-block;padding:5px;border-radius:3px;background-color:#6894d9;color:#ffffff;'>".$예정[$LangID]."</div>";
												}else if ($ClassAttendState==0){
													$StrClassAttendState = "<div style='display:inline-block;padding:5px;border-radius:3px;background-color:#8080C0;color:#ffffff;'>".$미설정[$LangID]."</div>";
												}else if ($ClassAttendState==1){
													$StrClassAttendState = "<div style='display:inline-block;padding:5px;border-radius:3px;background-color:#0080C0;color:#ffffff;'>".$출석[$LangID]."</div>";
												}else if ($ClassAttendState==2){
													$StrClassAttendState = "<div style='display:inline-block;padding:5px;border-radius:3px;background-color:#018D60;color:#ffffff;'>".$지각[$LangID]."</div>";
												}else if ($ClassAttendState==3){
													$StrClassAttendState = "<div style='display:inline-block;padding:5px;border-radius:3px;background-color:#C02C16;color:#ffffff;'>".$결석[$LangID]."</div>";
												}else if ($ClassAttendState==4){
													$StrClassAttendState = "<div style='display:inline-block;padding:5px;border-radius:3px;background-color:#8080C0;color:#ffffff;'>연기</div>";
												}else if ($ClassAttendState==5){
													$StrClassAttendState = "<div style='display:inline-block;padding:5px;border-radius:3px;background-color:#8080C0;color:#ffffff;'>연기</div>";
												}else if ($ClassAttendState==6){
													$StrClassAttendState = "<div style='display:inline-block;padding:5px;border-radius:3px;background-color:#804040;color:#ffffff;'>취소</div>";
												}else if ($ClassAttendState==7){
													$StrClassAttendState = "<div style='display:inline-block;padding:5px;border-radius:3px;background-color:#804040;color:#ffffff;'>취소</div>";
												}else if ($ClassAttendState==8){
													$StrClassAttendState = "<div style='display:inline-block;padding:5px;border-radius:3px;background-color:#804040;color:#ffffff;'>변경</div>";
												}else{
													$StrClassAttendState = "-";
												}


												if ($TodayIsHoliday==1){
													$StrClassAttendState = "<div style='display:inline-block;padding:5px;border-radius:3px;background-color:#804040;color:#ffffff;'>휴무일</div>";
												}

												
												if ($ClassProductID==1){
													$StrClassProductID = "정규수업";
													if ($ClassOrderSlotType==2){
														$StrClassProductID = "임시수업";
														if ($ClassOrderSlotType2==4){
															$StrClassProductID = "연기수업";
														}else if ($ClassOrderSlotType2==5){
															$StrClassProductID = "연기수업";
														}else if ($ClassOrderSlotType2==8){
															$StrClassProductID = "변경수업";
														}else if ($ClassOrderSlotType2==10000){
															$StrClassProductID = "보강수업";
														}else if ($ClassOrderSlotType2==20000){
															$StrClassProductID = "스케줄변경";//안나옴 ClassOrderSlotType=1 일때만 생성됨
														}	
													}
												}else if ($ClassProductID==2){
													$StrClassProductID = "레벨테스트";
												}else if ($ClassProductID==3) {
													$StrClassProductID = "체험수업";
												}


											
												if ($ClassOrderTimeTypeID==2){
													$StrClassOrderTimeTypeName = "20 min";
												}else if ($ClassOrderTimeTypeID==3){
													$StrClassOrderTimeTypeName = "30 min";
												}else if ($ClassOrderTimeTypeID==4){
													$StrClassOrderTimeTypeName = "40 min";
												}
											
												if ($ClassAttendState==1 || $ClassAttendState==2){
													$ClassAttendCount++;
												}
												if ($ClassAttendState==1 || $ClassAttendState==2 || $ClassAttendState==3){
													$ClassTotalCount++;
												}
										?>
												
												<?if ($ClassMemberType) {?>
													<div>
														<b><?=$StrClassProductID?></b>
														<br>
														<b><?=$TeacherName?></b>
														<br>
														<?=substr("0".$StudyTimeHour,-2)?>:<?=substr("0".$StudyTimeMinute,-2)?><br><?=$StrClassOrderTimeTypeName?>
														<br>
														<span style="color:#FE9147;"><?=$StrClassAttendState?></span>
													</div>
												<?}?>
												
										<?
											}
											$Stmt2 = null;
										?>
										</td>
										<?
										}
										?>

										<td nowrap class="uk-text-nowrap uk-table-td-center" style="font-size:11px;">
											<?if ($ClassTotalCount>0) {?>
												<?=round(($ClassAttendCount/$ClassTotalCount)*100,0)?> %
											<?}else{?>
												0 %
											<?}?>


										</td>
									</tr>
								<?php
										$ListCount++;
									}
								
								}
								$Stmt = null;

								if ($ListCount==1 && $_LINK_ADMIN_LEVEL_ID_<9){
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

						<!--
						<div class="uk-form-row" style="text-align:left;margin-top:20px;">
                            <a type="button" href="javascript:SendMessageForm()" class="md-btn md-btn-primary"><?=$메시지전송[$LangID]?></a>
                        </div>
						-->

					

						<?php			
						//include_once('./inc_pagination.php');
						?>

						<!--
						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="student_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary">신규등록</a>
						</div>
						-->

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
function SearchSubmit(){
	document.SearchForm.action = "attend_status_list.php";
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