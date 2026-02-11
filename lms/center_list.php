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
$MainMenuID = 12;
$SubMenuID = 1201;
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
$SearchPhone = isset($_REQUEST["SearchPhone"]) ? $_REQUEST["SearchPhone"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
$SearchFranchiseID = isset($_REQUEST["SearchFranchiseID"]) ? $_REQUEST["SearchFranchiseID"] : "";
$SearchCompanyID = isset($_REQUEST["SearchCompanyID"]) ? $_REQUEST["SearchCompanyID"] : "";
$SearchBranchGroupID = isset($_REQUEST["SearchBranchGroupID"]) ? $_REQUEST["SearchBranchGroupID"] : "";
$SearchBranchID = isset($_REQUEST["SearchBranchID"]) ? $_REQUEST["SearchBranchID"] : "";
$SearchOnlineSiteID = isset($_REQUEST["SearchOnlineSiteID"]) ? $_REQUEST["SearchOnlineSiteID"] : "";
$SearchManagerID = isset($_REQUEST["SearchManagerID"]) ? $_REQUEST["SearchManagerID"] : "";
$SearchCenterStudyEndDateNum = isset($_REQUEST["SearchCenterStudyEndDateNum"]) ? $_REQUEST["SearchCenterStudyEndDateNum"] : "";

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

if ($SearchCenterStudyEndDateNum==""){
	$SearchCenterStudyEndDateNum = "100";
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
//	$ListParam = $ListParam . "&SearchText=" . $SearchText;
//	$AddSqlWhere = $AddSqlWhere . " and (A.CenterName like '%".$SearchText."%' or A.CenterManagerName like '%".$SearchText."%' or G.MemberLoginID like '%".$SearchText."%') ";

    $ListParam = $ListParam . "&SearchText=" . $SearchText;
    $AddSqlWhere = $AddSqlWhere . " and (A.CenterName like '%".$SearchText."%' or A.CenterManagerName like '%".$SearchText."%' or G.MemberLoginID like '%".$SearchText."%') ";
}

if ($SearchPhone!=""){
    $ListParam = $ListParam . "&SearchPhone=" . $SearchPhone;
    $SearchPhoneLike = "%".$SearchPhone."%";
    $AddSqlWhere .= " and A.CenterID in (select CenterID from Members where (AES_DECRYPT(UNHEX(MemberPhone1),:EncryptionKey) like :SearchPhoneLike or AES_DECRYPT(UNHEX(MemberPhone2),:EncryptionKey) like :SearchPhoneLike or AES_DECRYPT(UNHEX(MemberPhone3),:EncryptionKey) like :SearchPhoneLike)) ";
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

if ($SearchCenterStudyEndDateNum!="100"){
	$ListParam = $ListParam . "&SearchCenterStudyEndDateNum=" . $SearchCenterStudyEndDateNum;
	$AddSqlWhere = $AddSqlWhere . " and datediff(A.CenterStudyEndDate, now())=".$SearchCenterStudyEndDateNum."  ";
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
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
if ($SearchPhone!="") {
    $Stmt->bindParam(":SearchPhoneLike", $SearchPhoneLike);
}
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
			where AA.MemberLevelID=19 and AA.MemberState<>0 and BB.CenterID=A.CenterID 
			) as StudentCount 
			";

$StudyStudentCountSql = " ,
			(select count(*) from Members where CenterID=A.CenterID and MemberLevelID=19 and MemberState=1 and MemberID in (select MemberID from ClassOrders where ClassProgress=11 and (ClassOrderState=1 or ClassOrderState=2 or ClassOrderState=4))) as StudyStudentCount ";

$Sql = "
		select 
			A.* ,
			AES_DECRYPT(UNHEX(A.CenterPhone1),:EncryptionKey) as DecCenterPhone1,
			AES_DECRYPT(UNHEX(A.CenterPhone2),:EncryptionKey) as DecCenterPhone2,
			B.BranchName, 
			C.BranchGroupName,
			D.CompanyName,
			D.CompanyPricePerTime,
			ifnull(E.OnlineSiteName,'미지정') as OnlineSiteName,
			ifnull(F.ManagerName,'미지정') as ManagerName,
			G.MemberLoginID,
			G.MemberID,
			(select sum(AA.MemberPoint) from MemberPoints AA where AA.MemberID=G.MemberID and AA.MemberPointState=1) as MemberPoint,
			H.FranchiseName 
			".$StudentCountSql."
			".$StudyStudentCountSql."
		from Centers A 
			inner join Branches B on A.BranchID=B.BranchID 
			inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
			inner join Companies D on C.CompanyID=D.CompanyID 
			left outer join OnlineSites E on A.OnlineSiteID=E.OnlineSiteID 
			left outer join Managers F on A.ManagerID=F.ManagerID 
			inner join Franchises H on D.FranchiseID=H.FranchiseID 
			inner join Members G on A.CenterID=G.CenterID and G.MemberLevelID=12 
		where ".$AddSqlWhere." 
		order by A.CenterOrder desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
if ($SearchPhone!="") {
    $Stmt->bindParam(":SearchPhoneLike", $SearchPhoneLike);
}
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>


<div id="page_content">
	<div id="page_content_inner">


		<?
		if ($_LINK_ADMIN_LEVEL_ID_==9 or $_LINK_ADMIN_LEVEL_ID_==10){//지사 관리자
		
			$Sql2 = "select BranchName from Branches where BranchID=:BranchID ";
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':BranchID', $_LINK_ADMIN_BRANCH_ID_);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
			$Row2 = $Stmt2->fetch();
			$Stmt2 = null;

			$AccBranchName = $Row2["BranchName"];
			
			
			$Sql2 = "select sum(BranchAccountPrice) as BranchAccountPrice from BranchAccounts where BranchID=:BranchID ";
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':BranchID', $_LINK_ADMIN_BRANCH_ID_);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
			$Row2 = $Stmt2->fetch();
			$Stmt2 = null;

			$BranchAccountPrice = $Row2["BranchAccountPrice"];
			
			if ($BranchAccountPrice<0){
		?>
		<div style="border:5px solid #888888;height:50px;line-height:50px;text-align:center;margin-bottom:20px;background-color:#ffffff;font-size:15px;">
			<?=$AccBranchName?><?=$는_은_현재[$LangID]?><span style="color:#BD0000;"><?=number_format($BranchAccountPrice,0)?></span><?=$원의_미수금이_있습니다[$LangID]?>
		</div>
		<?
			}
		}
		?>


		<h3 class="heading_b uk-margin-bottom"><?=$대리점관리[$LangID]?></h3>
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
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$운영[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$휴원[$LangID]?></option>
								<option value="3" <?if ($SearchState=="3"){?>selected<?}?>><?=$미운영[$LangID]?></option>
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

					<div class="uk-width-medium-1-10">
						<div class="uk-margin-small-top">
							<select id="SearchCenterStudyEndDateNum" name="SearchCenterStudyEndDateNum" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100"><?=$전체[$LangID]?></option>
								<?for ($ii=14;$ii>=-30;$ii--){?>
								<option value="<?=$ii?>" <?if ($ii==$SearchCenterStudyEndDateNum){?>selected<?}?>><?=$종료[$LangID]?><?=$ii?><?=$일_전[$LangID]?></option>
								<?}?>
							</select>
						</div>
					</div>

                    <div class="uk-width-medium-1-10 uk-text-center">
                        <a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
                    </div>

                    <div class="uk-width-medium-2-10">
                        <label for="SearchPhone"><?=$학생_부모님_전화번호[$LangID]?></label>
                        <input type="text" class="md-input" id="SearchPhone" name="SearchPhone" value="<?=$SearchPhone?>">
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
										<th nowrap><input name="CheckBoxAll" type="checkbox" onclick="CheckListAll(this)"></th>
										<th nowrap>No</th>
										<th nowrap><?=$대리점명[$LangID]?></th>
										<th nowrap><?=$아이디[$LangID]?></th>
										<th nowrap><?=$결제타입[$LangID]?></th>
										<th nowrap><?=$수강종료일[$LangID]?></th>
										<th nowrap><?=$요약[$LangID]?></th>
										<th nowrap><?=$관리자[$LangID]?></th>
										<th nowrap><?=$포인트[$LangID]?></th>
										<th nowrap><?=$학생수[$LangID]?></th>
										<th nowrap><?=$수강생[$LangID]?></th>
										<th nowrap><?=$수강료_10분당[$LangID]?></th>
										<?if ($_LINK_ADMIN_LEVEL_ID_<=10){?>
										<th nowrap><?=$커미션_10분당[$LangID]?></th>
										<?}?>

										<th nowrap><?=$전화번호[$LangID]?></th>
										<th nowrap><?=$휴대폰[$LangID]?></th>
										
										<?if ($_LINK_ADMIN_LEVEL_ID_<6){?>
										<th nowrap><?=$무료체험횟수[$LangID]?></th>
										<?}?>
										
										
										<?if ($_LINK_ADMIN_LEVEL_ID_<6){?>
										<th nowrap><?=$본사명[$LangID]?></th>
										
										
										<th nowrap><?=$대표지사명[$LangID]?></th>
										<th nowrap><?=$지사명[$LangID]?></th>
										<th nowrap><?=$사이트[$LangID]?></th>
										<th nowrap><?=$영업본부[$LangID]?></th>
										<?}?>
										
										
										<!--<th nowrap>프랜차이즈</th>-->
										<th nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
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
										$CompanyPricePerTime = $Row["CompanyPricePerTime"];
										$OnlineSiteName = $Row["OnlineSiteName"];
										$ManagerName = $Row["ManagerName"];
										$MemberID = $Row["MemberID"];
										$MemberLoginID = $Row["MemberLoginID"];
										$FranchiseName = $Row["FranchiseName"];
										$StudentCount = $Row["StudentCount"];
										$StudyStudentCount = $Row["StudyStudentCount"];
										$CenterFreeTrialCount = $Row["CenterFreeTrialCount"];
										$CenterPricePerTime = $Row["CenterPricePerTime"];

										$MemberPoint = $Row["MemberPoint"];

										$CenterPayType = $Row["CenterPayType"];
										$CenterRenewType = $Row["CenterRenewType"];

										$CenterStudyEndDate = $Row["CenterStudyEndDate"];
										if ($CenterPayType==1){
											$StrCenterPayType = "<span style='color:#0080C0;'>B2B 결제</span>";
											$StrCenterStudyEndDate = $CenterStudyEndDate;

											$StudyAuthDateDiff = (strtotime($StrCenterStudyEndDate) - strtotime(date("Y-m-d"))) / 86400;
											if ($StudyAuthDateDiff<=7){
												$StrCenterStudyEndDate = "<span style='color:#ff0000;'>".$StrCenterStudyEndDate." (".$StudyAuthDateDiff."일)</span>";
											}


										}else{
											$StrCenterPayType = "<span style='color:#AE0000;'>B2C 결제</span>";
											$StrCenterStudyEndDate = "-";
										}

										if ($CenterRenewType==2){
											$StrCenterRenewType = "<br><span style='color:#ff0000;'>(무결제)</span>";
										}else{
											$StrCenterRenewType = "";
										}
										
										if ($CenterState==1){
											$StrCenterState = "<span class=\"ListState_1\">운영</span>";
										}else if ($CenterState==2){
											$StrCenterState = "<span class=\"ListState_2\">휴원</span>";
										}else if ($CenterState==3){
											$StrCenterState = "<span class=\"ListState_2\">미운영</span>";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><input name="CheckBox_<?=$ListCount?>" id="CheckBox_<?=$ListCount?>" type="checkbox" value="<?=$MemberID?>"></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="center_form.php?ListParam=<?=$ListParam?>&CenterID=<?=$CenterID?>">
											<?=$CenterName?>
												<?if ($_LINK_ADMIN_LEVEL_ID_<=4){?>
													<a href="javascript:OpenMemberPointForm(<?=$MemberID?>);"><i class="material-icons">monetization_on</i></a>
												<?}?>
										</a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="center_form.php?ListParam=<?=$ListParam?>&CenterID=<?=$CenterID?>"><?=$MemberLoginID?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrCenterPayType?><?=$StrCenterRenewType?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrCenterStudyEndDate?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenCenterSummary(<?=$CenterID?>);"><i class="material-icons">account_box</i></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterManagerName?></td>
										
										<td class="uk-text-nowrap uk-table-td-right">
											<span style="color:#0000ff;"><?=number_format($MemberPoint,0)?></span>&nbsp;P
											<?if ($_LINK_ADMIN_LEVEL_ID_<=4){?>
											&nbsp;
											<a href="javascript:OpenMemberPointForm(<?=$MemberID?>);"><i class="material-icons">monetization_on</i></a>\
											<?}?>
										</td>

										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($StudentCount,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($StudyStudentCount,0)?></td>

										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($CenterPricePerTime)?></td>
										<?if ($_LINK_ADMIN_LEVEL_ID_<=10){?>
										<td class="uk-text-nowrap uk-table-td-center" style="color:#AE0000;"><?=number_format($CenterPricePerTime-$CompanyPricePerTime)?></td>
										<?}?>
										
										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterPhone1?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterPhone2?></td>

										<?if ($_LINK_ADMIN_LEVEL_ID_<6){?>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterFreeTrialCount?></td>
										<?}?>
										
										<?if ($_LINK_ADMIN_LEVEL_ID_<6){?>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CompanyName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchGroupName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$OnlineSiteName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ManagerName?></td>
										<?}?>
										
										

										<!--<td class="uk-text-nowrap uk-table-td-center"><?=$FranchiseName?></td>-->
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrCenterState?></td>
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;
									?>


								</tbody>
							</table>
						</div>

						<div class="uk-form-row" style="text-align:left;margin-top:20px;">
                            <a type="button" href="javascript:SendMessageForm()" class="md-btn md-btn-primary"><?=$메시지전송[$LangID]?></a>
                        </div>
						

						<?php			
						include_once('./inc_pagination.php');
						?>

						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="center_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
						</div>

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


function OpenMemberPointForm(MemberID){
	openurl = "member_point_form.php?MemberID="+MemberID;
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
$(function(){
    $('#SearchText, #SearchPhone').on('keydown', function(e){
        if(e.keyCode==13){
            e.preventDefault();
            SearchSubmit();
        }
    });
});

function SearchSubmit(){
	// document.SearchForm.action = "center_list.php";
	// document.SearchForm.submit();

    document.SearchForm.action = "center_list.php";
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