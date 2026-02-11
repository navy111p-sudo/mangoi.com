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
<style>
.TrListBgColor{background-color:#f1f1f1;font-weight:bold;color:#AA0000;}
</style>
<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 14;
$SubMenuID = 1402;
include_once('./inc_top.php'); 
include_once('./inc_menu_left.php');
#===== 모바일 결제창에서 결제하지 않고 다시 돌아올경우 셀프페이에 남겨진 고유코드를 다시 재사용하기위한 변수 입니다. =====#
$ReqUrl = isset($_REQUEST["ReqUrl"]) ? $_REQUEST["ReqUrl"] : ""; //' 결제창에서 결제실행전 돌아올때
?>



<?php

$AddSqlWhere = "1=1";
$AddSqlWhere2 = "1=1";
$AddSqlOrder = "";
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
$SearchClassOrderEndDateNum = isset($_REQUEST["SearchClassOrderEndDateNum"]) ? $_REQUEST["SearchClassOrderEndDateNum"] : "";
$SearchPayType = isset($_REQUEST["SearchPayType"]) ? $_REQUEST["SearchPayType"] : "";
$SearchClassEndType = isset($_REQUEST["SearchClassEndType"]) ? $_REQUEST["SearchClassEndType"] : "";

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

if ($SearchClassOrderEndDateNum==""){
	$SearchClassOrderEndDateNum = "100";
}
		
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.MemberState=$SearchState ";
}
if($SearchPayType!="100") {
	if($SearchPayType==1) {
		$AddSqlOrder = $AddSqlOrder . " and B.CenterPayType=2 ";
	} else if($SearchPayType==2) {
		$AddSqlOrder = $AddSqlOrder . " and B.CenterPayType=1 and A.MemberPayType=0 ";
	} else if($SearchPayType==3) {
		$AddSqlOrder = $AddSqlOrder . " and B.CenterPayType=1 and A.MemberPayType=1 ";
	}
	$ListParam = $ListParam . "&SearchPayType=".$SearchPayType;
}

if($SearchClassEndType!="100") {
	if($SearchClassEndType==1) {
		$AddSqlWhere = $AddSqlWhere . " and B.CenterPayType=2 ";
	} else if($SearchClassEndType==2) {
		$AddSqlWhere = $AddSqlWhere . " and B.CenterPayType=1 and A.MemberPayType=0 ";
	} else if($SearchClassEndType==3) {
		$AddSqlWhere = $AddSqlWhere . " and B.CenterPayType=1 and A.MemberPayType=1 ";
	}
	$ListParam = $ListParam . "&SearchClassEndType=".$SearchClassEndType;
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

if ($SearchClassOrderEndDateNum!="100"){
	$ListParam = $ListParam . "&SearchClassOrderEndDateNum=" . $SearchClassOrderEndDateNum;
	$AddSqlWhere2 = $AddSqlWhere2 . " and 
	(
		( V.CenterPayType=1 and V.MemberPayType=0 and datediff(V.CenterStudyEndDate, now())=".$SearchClassOrderEndDateNum.") 
		or 
		(V.CenterPayType=1 and V.MemberPayType=1 and V.MemberID in (select MemberID from ClassOrders where datediff(ClassOrderEndDate, now())=".$SearchClassOrderEndDateNum." and (ClassOrderState=1 or ClassOrderState=2 or ClassOrderState=4)) ) 
		or 
		(V.CenterPayType=2 and V.MemberID in (select MemberID from ClassOrders where datediff(ClassOrderEndDate, now())=".$SearchClassOrderEndDateNum." and (ClassOrderState=1 or ClassOrderState=2 or ClassOrderState=4)) ) 
 
	) ";
}


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);


$ViewTable = "

		select 
			A.* ,
			AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
			AES_DECRYPT(UNHEX(A.MemberPhone2),:EncryptionKey) as DecMemberPhone2,
			AES_DECRYPT(UNHEX(A.MemberPhone3),:EncryptionKey) as DecMemberPhone3,
			B.CenterID as JoinCenterID,
			B.CenterName as JoinCenterName,
			B.CenterPayType,
			B.CenterRenewType,
			B.CenterStudyEndDate,
			C.BranchID as JoinBranchID,
			C.BranchName as JoinBranchName, 
			D.BranchGroupID as JoinBranchGroupID,
			D.BranchGroupName as JoinBranchGroupName,
			E.CompanyID as JoinCompanyID,
			E.CompanyName as JoinCompanyName,
			F.FranchiseName,
			(select sum(AA.MemberPoint) from MemberPoints AA where AA.MemberID=A.MemberID and AA.MemberPointState=1) as MemberPoint,

			ifnull((select BB.ClassOrderWeekCount from ClassOrders AA inner join ClassOrderWeekCounts BB on AA.ClassOrderWeekCountID=BB.ClassOrderWeekCountID where AA.MemberID=A.MemberID and AA.ClassOrderState=1 and AA.ClassProductID=1 order by AA.ClassOrderID desc limit 0,1),'-') as ClassOrderWeekCount
		from Members A 
			left outer join Centers B on A.CenterID=B.CenterID 
			left outer join Branches C on B.BranchID=C.BranchID 
			left outer join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
			left outer join Companies E on D.CompanyID=E.CompanyID 
			left outer join Franchises F on E.FranchiseID=F.FranchiseID 
		where ".$AddSqlWhere." 
";



$Sql = "select 
				count(*) TotalRowCount 
		from ($ViewTable) V 
		where ".$AddSqlWhere2."
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );

/*

if ($StrClassOrderEndDateGroup==1){
	$Sql2 = "select 
					A.* 
			from ClassOrders A 
			where 
				A.MemberID=$MemberID 
				and (A.ClassOrderState=1 or A.ClassOrderState=2 or A.ClassOrderState=4) 
				and A.ClassProgress=11 
				and A.ClassProductID=1 
			order by A.ClassOrderEndDate asc";	
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

	$kkk=0;
	$StrClassOrderEndDate = "";
	while($Row2 = $Stmt2->fetch()) {
		if ($kkk>0){
			$StrClassOrderEndDate = $StrClassOrderEndDate . "<br>";
		}

		$ClassOrderEndDate = $Row2["ClassOrderEndDate"];
		
		if ($ClassOrderEndDate=="0000-00-00" || $ClassOrderEndDate==""){
			$ClassOrderEndDate = "[미설정]";
		}else{
			$ClassOrderEndDateDiff = (strtotime($ClassOrderEndDate) - strtotime(date("Y-m-d"))) / 86400;
			if ($ClassOrderEndDateDiff<=7){
				$ClassOrderEndDate = "<span style='color:#ff0000;'>".$ClassOrderEndDate." (".$ClassOrderEndDateDiff."일)</span>";
			}
		}
		$StrClassOrderEndDate .= $ClassOrderEndDate;
		
		
		$kkk++;
	}
	$Stmt2 = null;
}else{
	$StrClassOrderEndDate = $CenterStudyEndDate;
}

*/

$Sql = "
		select 
			V.*
		from ($ViewTable) V 
		
		where ".$AddSqlWhere2." 
		order by V.MemberRegDateTime desc limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$학생관리[$LangID]?></h3>

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
					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$학생명_또는_아이디[$LangID]?></label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>

					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						<select id="SearchPayType" name="SearchPayType" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="결제타입" style="width:100%;"/>
							<option></option>
							<option value="1" <?if($SearchPayType=="1"){?>selected<?}?>>B2C 결제</option>
							<option value="2" <?if($SearchPayType=="2"){?>selected<?}?>>B2B 결제</option>
							<option value="3" <?if($SearchPayType=="3"){?>selected<?}?>>B2B 개인결제</option>
						</select>
					</div>

					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						<select id="SearchClassEndType" name="SearchClassEndType" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="수강종료타입" style="width:100%;"/>
							<option></option>
							<option value="1" <?if($SearchClassEndType=="1"){?>selected<?}?>>미설정</option>
							<option value="2" <?if($SearchClassEndType=="2"){?>selected<?}?>>수강종료일-</option>
							<option value="3" <?if($SearchClassEndType=="3"){?>selected<?}?>>공백</option>
						</select>
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
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>>정상</option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>>휴면</option>
								<option value="3" <?if ($SearchState=="3"){?>selected<?}?>>탈퇴</option>
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
							<select id="SearchClassOrderEndDateNum" name="SearchClassOrderEndDateNum" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100">전체</option>
								<?for ($ii=14;$ii>=-30;$ii--){?>
								<option value="<?=$ii?>" <?if ($ii==$SearchClassOrderEndDateNum){?>selected<?}?>>종료 <?=$ii?>일 전</option>
								<?}?>
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
										<th nowrap><input name="CheckBoxAll" type="checkbox" onclick="CheckListAll(this)"></th>
										<th nowrap>No</th>
										<th nowrap><?=$학생명[$LangID]?></th>
										<th nowrap><?=$아이디[$LangID]?></th>
										<th nowrap>결제타입</th>
										<th nowrap>수강종료일</th>
										<th nowrap><?=$요약[$LangID]?></th>
										<th nowrap>스케줄</th>

										<th nowrap>가입일</th>
										<th nowrap>수업회수(주)</th>

										<th nowrap><?=$상담[$LangID]?></th>
										<th nowrap><?=$포인트[$LangID]?></th>
										<th nowrap><?=$메시지[$LangID]?></th>
										<!--th nowrap>레벨테스트</th-->
										<?if ($_LINK_ADMIN_LEVEL_ID_!=9 && $_LINK_ADMIN_LEVEL_ID_!=10){?>
										<th nowrap><?=$수강신청_스케쥴요청[$LangID]?><br></th>
										<?}?>
										<?if ($_LINK_ADMIN_LEVEL_ID_!=9 && $_LINK_ADMIN_LEVEL_ID_!=10){?>
										<th nowrap><?=$학생번호[$LangID]?></th>
										<th nowrap><?=$부모님번호[$LangID]?></th>
										<th nowrap>관리교사번호</th>
										<?}?>

										<th nowrap><?=$대리점명[$LangID]?></th>
										<?if ($_LINK_ADMIN_LEVEL_ID_<6){?>
										<th nowrap><?=$본사명[$LangID]?></th>
										<th nowrap><?=$대표지사명[$LangID]?></th>
										<th nowrap><?=$지사명[$LangID]?></th>
										
										<th nowrap><?=$프랜차이즈[$LangID]?></th>
										<?}?>
										<th nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$MemberID = $Row["MemberID"];
										$MemberPayType = $Row["MemberPayType"];
										$MemberLevelID = $Row["MemberLevelID"];
										$MemberNumber = $Row["MemberNumber"];
										$MemberLoginID = $Row["MemberLoginID"];
										$MemberLoginPW = $Row["MemberLoginPW"];
										$MemberName = $Row["MemberName"];
										$MemberNickName = $Row["MemberNickName"];
										$MemberSex = $Row["MemberSex"];
										$MemberCompanyName = $Row["MemberCompanyName"];
										$MemberPhoto = $Row["MemberPhoto"];
										$MemberBirthday = $Row["MemberBirthday"];
										$MemberPhone1 = $Row["DecMemberPhone1"];
										$MemberPhone2 = $Row["DecMemberPhone2"];
										$MemberPhone3 = $Row["DecMemberPhone3"];
										$MemberEmail = $Row["MemberEmail"];
										$MemberZip = $Row["MemberZip"];
										$MemberAddr1 = $Row["MemberAddr1"];
										$MemberAddr2 = $Row["MemberAddr2"];
										$SchoolName = $Row["SchoolName"];
										$SchoolGrade = $Row["SchoolGrade"];
										$MemberView = $Row["MemberView"];
										$MemberState = $Row["MemberState"];
										$MemberStateText = $Row["MemberStateText"];
										$WithdrawalText = $Row["WithdrawalText"];
										$LastLoginDateTime = $Row["LastLoginDateTime"];
										$LastAppLoginDateTime = $Row["LastAppLoginDateTime"];
										$MemberRegDateTime = $Row["MemberRegDateTime"];
										$MemberModiDateTime = $Row["MemberModiDateTime"];
										$WithdrawalDateTime = $Row["WithdrawalDateTime"];
										

										$CenterID = $Row["JoinCenterID"];
										$CenterName = $Row["JoinCenterName"];
										$CenterPayType = $Row["CenterPayType"];
										$CenterRenewType = $Row["CenterRenewType"];
										$CenterStudyEndDate = $Row["CenterStudyEndDate"];

										$BranchID = $Row["JoinBranchID"];
										$BranchName = $Row["JoinBranchName"];
										$BranchGroupID = $Row["JoinBranchGroupID"];
										$BranchGroupName = $Row["JoinBranchGroupName"];
										$CompanyID = $Row["JoinCompanyID"];
										$CompanyName = $Row["JoinCompanyName"];
										$FranchiseName = $Row["FranchiseName"];

										$MemberPoint = $Row["MemberPoint"];

										$ClassOrderWeekCount = $Row["ClassOrderWeekCount"];
										
										if ($MemberState==1){
											$StrCenterState = "<span class=\"ListState_1\">정상</span>";
										}else if ($MemberState==2){
											$StrCenterState = "<span class=\"ListState_2\">휴면</span>";
										}else if ($MemberState==3){
											$StrCenterState = "<span class=\"ListState_3\">탈퇴</span>";
										}

										$StrClassOrderEndDateGroup = 0;
										if ($CenterPayType==1){//B2B결제
											if ($MemberPayType==0){
												$StrCenterPayType = "<span style='color:#0080C0;'>B2B 결제</span>";
											}else{
												$StrCenterPayType = "<span style='color:#FF8000;'>B2B 개인결제</span>";
												$StrClassOrderEndDateGroup = 1;
											}
										}else{
											$StrCenterPayType = "<span style='color:#AE0000;'>B2C 결제</span>";
											$StrClassOrderEndDateGroup = 1;
										}


										$StrClassOrderEndDateGroup=1;//20200320 수강신청 종료날짜로 보여준다.

										if ($StrClassOrderEndDateGroup==1){
											$Sql2 = "select 
															A.* 
													from ClassOrders A 
													where 
														A.MemberID=$MemberID 
														and (A.ClassOrderState=1 or A.ClassOrderState=2 or A.ClassOrderState=4) 
														and A.ClassProgress=11 
														and A.ClassProductID=1 
													order by A.ClassOrderEndDate asc";	
											$Stmt2 = $DbConn->prepare($Sql2);
											$Stmt2->execute();
											$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

											$kkk=0;
											$StrClassOrderEndDate = "";
											while($Row2 = $Stmt2->fetch()) {
												if ($kkk>0){
													$StrClassOrderEndDate = $StrClassOrderEndDate . "<br>";
												}

												$ClassOrderEndDate = $Row2["ClassOrderEndDate"];
												
												if ($ClassOrderEndDate=="0000-00-00" || $ClassOrderEndDate==""){
													$ClassOrderEndDate = "[미설정]";
												}else{
													$ClassOrderEndDateDiff = (strtotime($ClassOrderEndDate) - strtotime(date("Y-m-d"))) / 86400;
													if ($ClassOrderEndDateDiff<=7){
														$ClassOrderEndDate = "<span style='color:#ff0000;'>".$ClassOrderEndDate." (".$ClassOrderEndDateDiff."일)</span>";
													}
												}
												$StrClassOrderEndDate .= $ClassOrderEndDate;
												
												
												$kkk++;
											}
											$Stmt2 = null;

											if ($StrClassOrderEndDate==""){
												$StrClassOrderEndDate = "미수강";
											}
										}else{
											$StrClassOrderEndDate = $CenterStudyEndDate;
										}

										if ($CenterPayType==1 && $CenterRenewType==2 && $MemberPayType==0){
											$StrClassOrderEndDate = "무결제B2B";
										}

									?>
									<tr id="TrList_<?=$MemberID?>" onclick="SelectList(<?=$MemberID?>);" class="">
										<td class="uk-text-nowrap uk-table-td-center"><input name="CheckBox_<?=$ListCount?>" id="CheckBox_<?=$ListCount?>" type="checkbox" value="<?=$MemberID?>"></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="student_form.php?ListParam=<?=$ListParam?>&MemberID=<?=$MemberID?>"><?=$MemberName?></a></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<a href="student_form.php?ListParam=<?=$ListParam?>&MemberID=<?=$MemberID?>">
												<?=$MemberLoginID?>
											</a>
											<a href="javascript:OpenFavoriteMenu(<?=$MemberID?>)">
												<span class="material-icons">
													drag_indicator
												</span>
											</a>
										</td>

										<td class="uk-text-nowrap uk-table-td-center"><?=$StrCenterPayType?></td>

										<td class="uk-text-nowrap uk-table-td-center"><?=$StrClassOrderEndDate?></td>

										<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenStudentForm(<?=$MemberID?>);"><i class="material-icons">account_box</i></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenStudentCalendar(<?=$MemberID?>);"><i class="material-icons">date_range</i></a></td>

										<td class="uk-text-nowrap uk-table-td-center"><?=substr($MemberRegDateTime,0,10)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderWeekCount?></td>

										<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenCounselForm(<?=$MemberID?>);"><i class="material-icons">contact_phone</i></a></td>
										<td class="uk-text-nowrap uk-table-td-right">
											<span style="color:#0000ff;"><?=number_format($MemberPoint,0)?></span>&nbsp;P
											<?if ($_LINK_ADMIN_LEVEL_ID_<=4){?>
											&nbsp;
											<a href="javascript:OpenMemberPointForm(<?=$MemberID?>);"><i class="material-icons">monetization_on</i></a>
											<?}?>
										</td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenMessageSendForm(<?=$MemberID?>);"><i class="material-icons">sms</i></a></td>
										<!--td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenLeveltestApplyForm(<?=$MemberID?>);"><i class="material-icons">multiline_chart</i></a></td-->

										<?if ($_LINK_ADMIN_LEVEL_ID_!=9 && $_LINK_ADMIN_LEVEL_ID_!=10){?>
										<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenClassOrderForm(<?=$MemberID?>,'<?=$ReqUrl?>');"><i class="material-icons">contacts</i></a></td>
										<?}?>

										<?if ($_LINK_ADMIN_LEVEL_ID_!=9 && $_LINK_ADMIN_LEVEL_ID_!=10){?>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberPhone1?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberPhone2?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberPhone3?></td>
										<?}?>

										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterName?></td>
										<?if ($_LINK_ADMIN_LEVEL_ID_<6){?>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CompanyName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchGroupName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchName?></td>
										
										<td class="uk-text-nowrap uk-table-td-center"><?=$FranchiseName?></td>
										<?}?>
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

						<div class="uk-form-row btns">
                            <a type="button" href="javascript:SendMessageForm()" class="md-btn md-btn-primary one"><?=$메시지전송[$LangID]?></a>
                            <div class="uk-form-row-right">
							<a type="button" href="javascript:PopUpUploadForm(<?=$_LINK_ADMIN_ID_?>)" class="md-btn md-btn-primary two">학생목록 일괄 업로드</a>
							<a type="button" href="javascript:DownloadForm()" class="md-btn md-btn-primary three">학생목록 샘플폼 다운로드</a>
                            </div>
                        </div>
						<div class="uk-form-row" style="margin-top:20px;text-align:right;color:#bb0000;">
						※ 다운받은 CSV 파일은 반드시 엑셀로 수정해 주세요. 한셀 등 다른 프로그램 사용시 한글이 깨질 수도 있습니다.
						<br>
						※ 반드시 샘플폼을 사용하셔야 하며 전화번호, 이메일 등은 지정한 형식으로 입력하셔야 합니다. 
						</div>
						

						<?php			
						include_once('./inc_pagination.php');
						?>

						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="student_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
						</div>

					</div>
				</div>
			</div>
		</div>

	</div>
</div>


<script>

function OpenFavoriteMenu(MemberID) {
	var newWin = window.open("popup_favorite_student_menu.php?MemberID="+MemberID+"&IframeMode=1", "", "width=${window.outerWidth}, height=${window.outerHeight}");
}

function SelectList(MemberID){
	if (document.getElementById("TrList_"+MemberID).className==""){
		document.getElementById("TrList_"+MemberID).className="TrListBgColor";
	}else{
		document.getElementById("TrList_"+MemberID).className="";
	}
}


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

	// 체크박스 영역이 비어있다면
	if (ListCount==0){
		alert("선택한 목록이 없습니다.");
	}else{
		
		// 계정 구분을 위한 | 를 입력 후 체크 확인 후에 값 넣기
		MemberIDs = "|";
		for (ii=1;ii<=ListCount;ii++){
			if (document.getElementById("CheckBox_"+ii).checked){
				MemberIDs = MemberIDs + document.getElementById("CheckBox_"+ii).value + "|";
			}	
		}

		// 초기 값인 | 가 그대로라면...
		if (MemberIDs=="|"){
			alert("선택한 목록이 없습니다.");
		}else{
			// ajax 통신
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

function DownloadForm() {
	location.href = "student_list_excel_download.php";
//	openurl = "student_list_csv_download.php";
//
//	$.colorbox({	
//		href:openurl
//		,width:"95%" 
//		,height:"95%"
//		,maxWidth: "850"
//		,maxHeight: "750"
//		,title:""
//		,iframe:true 
//		,scrolling:true
//		//,onClosed:function(){location.reload(true);}
//		//,onComplete:function(){alert(1);}
//	});
}

function PopUpUploadForm(MemberID) {
	openurl = "student_list_excel_popup_upload_form.php?MemberID="+MemberID;

	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "750"
		,maxHeight: "650"
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
<script>
function OpenClassOrderForm(MemberID,ReqUrl){
	openurl = "class_order_form.php?MemberID="+MemberID+"&ReqUrl="+ReqUrl;
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

function OpenLeveltestApplyForm(MemberID){
	openurl = "leveltest_apply_form.php?MemberID="+MemberID;
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

function OpenMessageSendForm(MemberID){
	openurl = "send_message_log_form.php?MemberID="+MemberID;
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


function OpenCounselForm(MemberID){
	openurl = "counsel_form.php?MemberID="+MemberID;
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


<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "student_list_origin.php";
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