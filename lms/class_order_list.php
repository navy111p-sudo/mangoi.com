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

$MainMenuID = 16;

if ($SearchState=="99"){//장기홀드
	$SubMenuID = 1699;
}else if ($SearchState=="11"){//스케줄대상
	$SubMenuID = 1611;
}else if ($SearchState=="21"){//스케줄완료
	$SubMenuID = 1621;
}else if ($SearchState=="31"){//종료대상
	$SubMenuID = 1631;
}else if ($SearchState=="41"){//종료완료
	$SubMenuID = 1641;

}else if ($SearchState=="111"){//결제대기
	$SubMenuID = 16111;
}else if ($SearchState=="121"){//결제완료
	$SubMenuID = 16121;
}else if ($SearchState=="131"){//취소완료
	$SubMenuID = 16131;

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

$SumOfSavedMoney = 0;

// 만약 대리점 관리자로 접속했다면 그 대리점의 수업료 충전금 잔액을 불러온다. 
// 충전금이 만약 10000원 이하이면 수업 스케줄을 불러올 수 없게 만든다.
if ($_LINK_ADMIN_CENTER_ID_ != 0) {
	// SavedMoney(충전금 테이블)에서 현재 CenterID의 사용가능한 충전금 잔액을 가지고 온다. SavedMoneyState = 1 정상 충전금
	$Sql = "SELECT SUM(SavedMoney) AS SumOfSavedMoney FROM SavedMoney WHERE SavedMoneyState = 1 AND CenterID = :CenterID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $_LINK_ADMIN_CENTER_ID_);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$SumOfSavedMoney = $Row["SumOfSavedMoney"];
}


if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 30;
}

if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}

		
if ($SearchState!="0"){
	if ($SearchState=="99"){
		$AddSqlWhere = $AddSqlWhere . " and A.ClassOrderState=4 and A.ClassProgress=11 ";//장기홀드
	}else if ($SearchState=="31"){
		$AddSqlWhere = $AddSqlWhere . " and A.ClassOrderState=2 and A.ClassProgress=11 ";//종료대상
	}else if ($SearchState=="41"){
		$AddSqlWhere = $AddSqlWhere . " and A.ClassOrderState=3 and A.ClassProgress=11 ";//종료완료
	}else if ($SearchState=="11"){
		$AddSqlWhere = $AddSqlWhere . " and A.ClassOrderState=1 and A.ClassProgress=1 ";//스케줄대상
	}else if ($SearchState=="21"){
		$AddSqlWhere = $AddSqlWhere . " and A.ClassOrderState=1 and A.ClassProgress=11 ";//스케줄완료

	}else if ($SearchState=="111"){
		$AddSqlWhere = $AddSqlWhere . " and A.ClassOrderState=1 and A.ClassProgress=11  ";//결제대기
		$AddSqlWhere2 = $AddSqlWhere2 . " and (V.ClassOrderPayProgress=1 or V.ClassOrderPayProgress=11 or V.ClassOrderPayProgress=0) ";
	}else if ($SearchState=="121"){
		$AddSqlWhere = $AddSqlWhere . " and A.ClassOrderState=1 and A.ClassProgress=11 ";//결제완료
		$AddSqlWhere2 = $AddSqlWhere2 . " and V.ClassOrderPayProgress=21 ";
	}else if ($SearchState=="131"){
		$AddSqlWhere = $AddSqlWhere . " and A.ClassOrderState=1 and A.ClassProgress=11 ";//취소완료
		$AddSqlWhere2 = $AddSqlWhere2 . " and V.ClassOrderPayProgress=33 ";
	}
}


$AddSqlWhere = $AddSqlWhere . " and A.ClassOrderState>0 ";
$AddSqlWhere = $AddSqlWhere . " and ( B.MemberState<>0  ) ";
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

$AddSqlWhere = $AddSqlWhere . " and (A.ClassProductID=1 or A.ClassProductID=3) ";


$ListParam = $ListParam . "&type=" . $type;


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);


$ViewTable = "

		select 
			A.*,

			AA.ClassProductName, 
			DD.ClassOrderTimeTypeName,
			EE.ClassOrderWeekCountName,

			B.MemberName,
			B.MemberLoginID,
			B.MemberPayType,
			
			C.CenterID as JoinCenterID,
			C.CenterName as JoinCenterName,
			C.CenterPayType,
			C.CenterRenewType,
			D.BranchID as JoinBranchID,
			D.BranchName as JoinBranchName, 
			E.BranchGroupID as JoinBranchGroupID,
			E.BranchGroupName as JoinBranchGroupName,
			F.CompanyID as JoinCompanyID,
			F.CompanyName as JoinCompanyName,
			G.FranchiseName,
			ifnull((select concat(ClassOrderSlotDate, ' (', StudyTimeHour, '시 ' , StudyTimeMinute, '분',')' ) from ClassOrderSlots where ClassOrderID=A.ClassOrderID order by StudyTimeHour asc, StudyTimeMinute asc limit 0,1),'-') as ClassDateTime,
			ifnull((select BBB.TeacherName from ClassOrderSlots AAA inner join Teachers BBB on AAA.TeacherID=BBB.TeacherID where AAA.ClassOrderID=A.ClassOrderID order by AAA.StudyTimeHour asc, AAA.StudyTimeMinute asc limit 0,1),'-') as ClassTeacherName,

			ifnull((select BBB.ClassOrderPayProgress from ClassOrderPayDetails AAA inner join ClassOrderPays BBB on AAA.ClassOrderPayID=BBB.ClassOrderPayID where AAA.ClassOrderID=A.ClassOrderID and AAA.ClassOrderPayDetailType=1 and AAA.ClassOrderPayDetailState=1 order by AAA.ClassOrderPayDetailID desc limit 0,1),0) as ClassOrderPayProgress
		from ClassOrders A
			inner join ClassProducts AA on A.ClassProductID=AA.ClassProductID 
			
			inner join ClassOrderTimeTypes DD on A.ClassOrderTimeTypeID=DD.ClassOrderTimeTypeID 
			inner join ClassOrderWeekCounts EE on A.ClassOrderWeekCountID=EE.ClassOrderWeekCountID 
			
			inner join Members B on A.MemberID=B.MemberID 
			inner join Centers C on B.CenterID=C.CenterID 
			inner join Branches D on C.BranchID=D.BranchID 
			inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
			inner join Companies F on E.CompanyID=F.CompanyID 
			inner join Franchises G on F.FranchiseID=G.FranchiseID 
		where ".$AddSqlWhere." 

";



$Sql = "select 
				count(*) TotalRowCount 
		from ($ViewTable) V 
		where ".$AddSqlWhere2." ";
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
			V.*
		from ($ViewTable) V
		where ".$AddSqlWhere2." 
		order by V.ClassOrderRegDateTime desc limit $StartRowNum, $PageListNum";


$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


$ArrWeekDayStr = explode(",","일,월,화,수,목,금,토");
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

					<div class="uk-width-medium-6-10"></div>

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
										echo "<optgroup label=\"대리점(운영중)\">";
									}else if ($SelectCenterState==2){
										echo "<optgroup label=\"대리점(미운영)\">";
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
					
				</div>
			</div>
		</div>
		</form>


		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">

<!--						--><?// if ($_LINK_ADMIN_CENTER_ID_ != 0 && $SumOfSavedMoney < 10000){?><!--						-->
						<? if (false){?>
							<p style="font-size:16px;color:red;">
							※ 현재 수업료 충전금이 1만원보다 적습니다. 충전금이 부족해서 스케줄 작성이 안됩니다. <br/>
							  &nbsp;&nbsp;&nbsp;&nbsp;[학생관리-수강 연장] 메뉴에서 <수업료 충전> 버튼을 눌러서 수업료를 충전해 주세요.<br/></p>
						<?} ?>	
							※ 스케줄이 변경된 경우 변경 후 스케줄만 표시 됩니다.<br/>
							※ 시작일 조건이 맞지 않는 경우, 잔여수업수 자동계산이 불가하여 '수동계산' 해주셔야합니다.
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap><?=$신청일[$LangID]?></th>
										<th nowrap><?=$학생명[$LangID]?></th>
										<th nowrap><?=$아이디[$LangID]?></th>
										<th nowrap><?=$요약[$LangID]?></th>
										<th nowrap><?=$스케줄[$LangID]?></th>
										<th nowrap><?=$시작일[$LangID]?></th>
										<th nowrap><?=$종료일[$LangID]?></th>
										<th nowrap><?=$이전결제[$LangID]?><br><?=$종료일[$LangID]?></th>
										<th nowrap><?=$잔여[$LangID]?><br><?=$수업수[$LangID]?></th>
										<th nowrap><?=$결제방법[$LangID]?></th>
										<th nowrap><?=$신청과정명[$LangID]?></th>
										<th nowrap><?=$수강관리[$LangID]?></th>
										<th nowrap><?=$수업타입[$LangID]?></th>
										<th nowrap><?=$회수[$LangID]?>/<?=$주[$LangID]?></th>
										<th nowrap><?=$학습시간[$LangID]?>/<?=$회[$LangID]?></th>
										<th nowrap><?=$수업스케줄[$LangID]?></th>
										<?if ($SearchState!="11"){?>
											<!--<th nowrap>결제정보</th>-->
										<?}?>
										<?if ($_LINK_ADMIN_LEVEL_ID_<=4 && $SearchState!="11"){?>
											<!--<th nowrap>결제관리</th>-->
										<?}?>
										<th nowrap><?=$스케줄상태[$LangID]?></th>
										<th nowrap><?=$수업상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$ListCount = 1;

                                    // 한국어 요일 배열
                                    $weekdaysKor = array('Mon' => '월', 'Tue' => '화', 'Wed' => '수', 'Thu' => '목', 'Fri' => '금', 'Sat' => '토', 'Sun' => '일');

                                    while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$ClassOrderID = $Row["ClassOrderID"];
										$ClassOrderPayID = $Row["ClassOrderPayID"];
										$ClassProductID = $Row["ClassProductID"];
										$ClassOrderLeveltestApplyTypeID = $Row["ClassOrderLeveltestApplyTypeID"];
										$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
										$ClassOrderWeekCountID = $Row["ClassOrderWeekCountID"];
										$MemberID = $Row["MemberID"];
										$ClassOrderRequestText = $Row["ClassOrderRequestText"];
										$ClassOrderState = $Row["ClassOrderState"];
										$ClassMemberType = $Row["ClassMemberType"];
										$ClassProgress = $Row["ClassProgress"];
										$ClassOrderRegDateTime = $Row["ClassOrderRegDateTime"];
										$ClassOrderModiDateTime = $Row["ClassOrderModiDateTime"];


										$ClassProductName = $Row["ClassProductName"];
										$ClassOrderTimeTypeName = $Row["ClassOrderTimeTypeName"];
										$ClassOrderWeekCountName = $Row["ClassOrderWeekCountName"];

										$ClassOrderStartDate = $Row["ClassOrderStartDate"];
										$ClassOrderEndDate = $Row["ClassOrderEndDate"];

										$LastClassOrderEndDateByPay = $Row["LastClassOrderEndDateByPay"];
				
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

										$ClassDateTime = $Row["ClassDateTime"];
										$ClassTeacherName = $Row["ClassTeacherName"];

										$CenterPayType = $Row["CenterPayType"];
										$CenterRenewType = $Row["CenterRenewType"];
										$MemberPayType = $Row["MemberPayType"];

                                        // "스케줄 상태"와 "수업 상태" 값을 문자열 변수에 할당
//                                        $StrClassOrderState = ($ClassOrderState == 0) ? "완전삭제" : (($ClassOrderState == 1) ? "정상운영" : (($ClassOrderState == 2) ? "종료대상" : (($ClassOrderState == 3) ? "종료완료" : "장기홀드")));
//                                        $StrClassProgress = ($ClassProgress == 1) ? "스케줄대상" : "스케줄완료";


                                        if ($ClassOrderState==0){//없음
											$StrClassOrderState = "<?=$완전삭제[$LangID]?>";
										}else if ($ClassOrderState==1){
											$StrClassOrderState = "<?=$정상운영[$LangID]?>";
										}else if ($ClassOrderState==2){
											$StrClassOrderState = "<?=$종료대상[$LangID]?>";
										}else if ($ClassOrderState==3){
											$StrClassOrderState = "<?=$종료완료[$LangID]?>";
										}else if ($ClassOrderState==4){
											$StrClassOrderState = "<?=$장기홀드[$LangID]?>";
										}
					

										if ($ClassProgress==1){
											$StrClassProgress = "<?=$스케줄대상[$LangID]?>";
										}else if ($ClassProgress==11){
											$StrClassProgress = "<?=$스케줄완료[$LangID]?>";
										}


                                        $ClassTeacherName = $Row["ClassTeacherName"];
                                        $ClassDateTime = $Row["ClassDateTime"]; // 예: "2024-02-14 (19시 40분)" 또는 "-"

                                        // 날짜와 시간 정보 변환
                                        if ($ClassDateTime != "-") {
                                            if (preg_match('/(\d{4})-(\d{2})-(\d{2})\s*\((\d{1,2})시\s*(\d{1,2})분\)/', $ClassDateTime, $matches)) {
                                                // 날짜 정보 추출 및 요일 계산
                                                $year = $matches[1];
                                                $month = $matches[2];
                                                $day = $matches[3];
                                                $timestamp = mktime(0, 0, 0, $month, $day, $year);
                                                $weekday = date("D", $timestamp); // 'Mon', 'Tue', ...
                                                $weekdayKor = ['Sun' => '일', 'Mon' => '월', 'Tue' => '화', 'Wed' => '수', 'Thu' => '목', 'Fri' => '금', 'Sat' => '토'][$weekday];
                                                $formattedDate = "{$month}-{$day} ({$weekdayKor})";

                                                // 시간 정보 추출
                                                $hour = $matches[4];
                                                $minute = $matches[5];
                                                $formattedTime = sprintf("%02d:%02d", $hour, $minute); // "HH:MM" 형식

                                                $displayDateTime = $formattedDate . ' ' . $formattedTime;
                                            } else {
                                                // 알 수 없는 형식
                                                $displayDateTime = $ClassDateTime;
                                            }
                                        } else {
                                            $displayDateTime = $ClassDateTime; // 변환하지 않음
                                        }



                                        if ($CenterPayType==1){//B2B결제
											if ($MemberPayType==0){
												$StrCenterPayType = "<span style='color:#0080C0;'>B2B 결제</span>";
											}else{
												$StrCenterPayType = "<span style='color:#FF8000;'>B2B 개인결제</span>";
											}
										}else{
											$StrCenterPayType = "<span style='color:#AE0000;'>B2C 결제</span>";
										}

										if ($LastClassOrderEndDateByPay==""){
											$LastClassOrderEndDateByPay = "-";
										}

										if ($CenterPayType==1 && $CenterRenewType==2 && $MemberPayType==0){
											$ClassOrderEndDate = "무결제B2B";
										}

										$Sql2 = "
											select 
												A.ClassOrderEndDate, 
												B.ClassOrderPayStartDate 
											from ClassOrders A 
												inner join ClassOrderPays B on A.ClassOrderPayID=B.ClassOrderPayID 
											where
												A.ClassOrderID=:ClassOrderID
										";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$TempClassOrderPayStartDate = $Row2["ClassOrderPayStartDate"];
										$TempClassOrderEndDate = $Row2["ClassOrderEndDate"];

										//$LastClassOrderEndDateByPay = "2020-03-23";
										//$ClassOrderEndDate = "2020-04-08";

										$TempClassOrderStartDate = $TempClassOrderPayStartDate . " 00:00:00";
										$TempClassOrderEndDate = $TempClassOrderEndDate . " 23:59:59";
										$Stmt2 = null;


										// 전체 수업 갯수 확인
										if($ClassOrderPayID!=0) {
											$Sql3 = "
												select 
													sum(AA.ClassOrderPayTotalClassCount) as ClassOrderPayTotalClassCount 
												from ClassOrderPayDetails AA 
												where 
													AA.ClassOrderPayID=:ClassOrderPayID 
													and 
													AA.ClassOrderID=:ClassOrderID
												group by
													AA.ClassOrderPayID, AA.ClassOrderID
											";
											/*
											$Sql3 = "
												select 
													sum(AA.ClassOrderPayTotalClassCount) as ClassOrderPayTotalClassCount 
												from ClassOrderPayDetails AA 
												where 
													AA.ClassOrderPayID=:ClassOrderPayID 
													and 
													AA.ClassOrderID=:ClassOrderID
												group by
													AA.ClassOrderPayID, AA.ClassOrderID
											";
											*/
											$Stmt3 = $DbConn->prepare($Sql3);
											$Stmt3->bindParam(':ClassOrderPayID', $ClassOrderPayID);
											$Stmt3->bindParam(':ClassOrderID', $ClassOrderID);
											$Stmt3->execute();
											$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
											$Row3 = $Stmt3->fetch();
											$ClassOrderPayTotalClassCount = $Row3["ClassOrderPayTotalClassCount"];
											$Stmt3 = null;

											$Sql4 = "
												select 
													count(*) as ClassesTotalRowCount 
												from Classes A 
												where
													A.ClassOrderID=:ClassOrderID
													and
													(
														A.ClassAttendState=1
														or
														A.ClassAttendState=2
														or
														A.ClassAttendState=3
													)
													and 
													datediff(A.StartDateTime, :ClassOrderStartDate) >=0
													and
													datediff(A.EndDateTime, :ClassOrderEndDate) <=0
											";
											$Stmt4 = $DbConn->prepare($Sql4);
											$Stmt4->bindParam(':ClassOrderID', $ClassOrderID);
											$Stmt4->bindParam(':ClassOrderStartDate', $TempClassOrderStartDate);
											$Stmt4->bindParam(':ClassOrderEndDate', $TempClassOrderEndDate);
											$Stmt4->execute();
											$Stmt4->setFetchMode(PDO::FETCH_ASSOC);
											$Row4 = $Stmt4->fetch();
											$ClassesTotalRowCount = $Row4["ClassesTotalRowCount"];
											$Stmt4 = null;

											/*
											// 이전에 결제한 수업 중, 이전된 수업의 수
											$Sql5 = "
												select 
													count(*) as TotalRowCount2
												from Classes A 
												where
													A.ClassOrderPayID=:ClassOrderPayID
													and
													A.ClassOrderID=:ClassOrderID
													and
													(
														A.ClassAttendState=4
														or
														A.ClassAttendState=5
														or
														A.ClassAttendState=8
													)
											";
											$Stmt5 = $DbConn->prepare($Sql5);
											$Stmt5->bindParam(':ClassOrderPayID', $ClassOrderPayID);
											$Stmt5->bindParam(':ClassOrderID', $ClassOrderID);
											$Stmt5->execute();
											$Stmt5->setFetchMode(PDO::FETCH_ASSOC);
											$Row5 = $Stmt5->fetch();
											$TotalRowCount2 = $Row5["TotalRowCount2"];
											$Stmt5 = null;
											*/
										}

										//$Today = date("Y-m-d H:i:s");
										$Today = date("2020-05-22 17:50:00");
										$TempToday = strtotime($Today);
										$CompareClassOrderEndDate = strtotime($ClassOrderEndDate);
										if($ClassOrderPayID && $ClassOrderState==3 && $TempClassOrderPayStartDate!=null ) {

											if($CompareClassOrderEndDate<= $TempToday) {
												//$StrRemainClass = $ClassOrderPayTotalClassCount - $TotalRowCount + $TotalRowCount2;
												$StrRemainClass = $ClassOrderPayTotalClassCount - $ClassesTotalRowCount;
											} else {
												$StrRemainClass = "수동계산";
											}
										} else {
											$StrRemainClass = "-";
										}


									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?><br>(<?=$ClassOrderID?>)</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderRegDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberLoginID?></td>

										<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenStudentForm(<?=$MemberID?>);"><i class="material-icons">account_box</i></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenStudentCalendar(<?=$MemberID?>);"><i class="material-icons">date_range</i></a></td>								
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderStartDate?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderEndDate?><br>
										<? if ($ClassOrderEndDate!="") {?>
											<a href="javascript:OpenClassEndDateLog(<?=$ClassOrderID?>)" class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" style="display:block;background-color:#719D40;margin:10px auto 10px auto;width:120px;">종료일변경로그</a>
										<?} ?>	
										</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$LastClassOrderEndDateByPay?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrRemainClass?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrCenterPayType?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassProductName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a class="md-btn md-btn-primary md-btn-small md-btn-wave-light" href="javascript:OpenClassOrderForm(<?=$ClassOrderID?>, <?=$ClassOrderPayID?>);">수강관리</a></td>

										<td class="uk-text-nowrap uk-table-td-center">
											<?if ($ClassMemberType==1){?>
											<span style="color:#FF8000;">1:1수업</span>
											<?}else if ($ClassMemberType==2){?>
											<span style="color:#8000FF;">1:2수업</span>
											<?}else if ($ClassMemberType==3){?>
											<span style="color:#8000FF;"><?=$그룹수업[$LangID]?></span>
											<?}?>
										</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderWeekCountName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderTimeTypeName?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<?
											// /충전금이 10000원 이하이면 <스케줄관리>를 숨긴다.
											/*if ($ClassProgress==1 && $_LINK_ADMIN_CENTER_ID_ != 0 && $SumOfSavedMoney >= 10000){?>*/
											if (true){?>
												<a class="md-btn md-btn-primary md-btn-small md-btn-wave-light" href="javascript:OpenClassScheduleForm(<?=$ClassOrderID?>);"><?=$스케줄관리[$LangID]?></a>
											<?} else if ($_LINK_ADMIN_CENTER_ID_ != 0 && $SumOfSavedMoney < 10000){?>
											 	<a class="md-btn md-btn-primary md-btn-small md-btn-wave-light" href="#">충전금 부족</a>
											<?} else if ($ClassProgress==1){?>
												<a class="md-btn md-btn-primary md-btn-small md-btn-wave-light" href="javascript:OpenClassScheduleForm(<?=$ClassOrderID?>);"><?=$스케줄관리[$LangID]?></a>
											<?} else {?>
												<?if ($ClassProductID==1){?>
													<?
													$Sql6 = "
															select 
																AAA.StudyTimeHour,
																AAA.StudyTimeMinute,
																AAA.StudyTimeWeek,
																AAA.ClassOrderSlotEndDate,
																BBB.TeacherName 
															from ClassOrderSlots AAA 
																inner join Teachers BBB on AAA.TeacherID=BBB.TeacherID 
															where AAA.ClassOrderID=$ClassOrderID and AAA.ClassOrderSlotState=1 and AAA.ClassOrderSlotType=1 and AAA.ClassOrderSlotMaster=1
															order by AAA.StudyTimeWeek, AAA.StudyTimeHour asc, AAA.StudyTimeMinute asc 
													";

													$Stmt6 = $DbConn->prepare($Sql6);
													$Stmt6->execute();
													$Stmt6->setFetchMode(PDO::FETCH_ASSOC);
													while($Row6 = $Stmt6->fetch()) {
														$StudyTimeHour = $Row6["StudyTimeHour"];
														$StudyTimeMinute = $Row6["StudyTimeMinute"];
														$StudyTimeWeek = $Row6["StudyTimeWeek"];
														$TeacherName = $Row6["TeacherName"];
														$ClassOrderSlotEndDate = $Row6["ClassOrderSlotEndDate"];
													
														if ($ClassOrderSlotEndDate==""){
													?>
														<?=$ArrWeekDayStr[$StudyTimeWeek]?>(<?=$StudyTimeHour?>시 <?=$StudyTimeMinute?>분) / <?=$TeacherName?><br>
													<?
														}
													}
													$Stmt6 = null;
													?>
												<?}else{?>
													<?=$ClassDateTime?> / <?=$ClassTeacherName?>
												<?}?>

												
												<a href="javascript:OpenClassOrderSlotLog(<?=$ClassOrderID?>)" class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" style="display:block;background-color:#719D40;margin:10px auto 10px auto;width:160px;"><?=$수업변경로그[$LangID]?></a>
											<?}?>


										</td>
										<?if ($SearchState!="11"){?>
											<!--
											<td class="uk-text-nowrap uk-table-td-center">
												<?
												if ($ClassProgress==11 && $ClassProductID==1){
													$Sql3 = "
															select 
																B.ClassOrderPayID,
																B.ClassOrderPayProgress,
																B.ClassOrderPayPaymentDateTime,
																B.ClassOrderPayCencelDateTime
															from ClassOrderPayDetails A 
																inner join ClassOrderPays B on A.ClassOrderPayID=B.ClassOrderPayID 
															where 
																A.ClassOrderID=$ClassOrderID 
																and A.ClassOrderPayDetailType=1 
																and A.ClassOrderPayDetailState=1 
															order by A.ClassOrderPayDetailID desc limit 0, 1
													";
													$Stmt3 = $DbConn->prepare($Sql3);
													$Stmt3->execute();
													$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
													$Row3 = $Stmt3->fetch();
													$ClassOrderPayID = $Row3["ClassOrderPayID"];
													$ClassOrderPayProgress = $Row3["ClassOrderPayProgress"];
													$ClassOrderPayPaymentDateTime = $Row3["ClassOrderPayPaymentDateTime"];
													$ClassOrderPayCencelDateTime = $Row3["ClassOrderPayCencelDateTime"];
												?>
													<?if ($ClassOrderPayProgress==21){?>
														결제완료(<?=substr($ClassOrderPayPaymentDateTime,0,10)?>)
													<?}else if ($ClassOrderPayProgress==31){?>
														취소요청
													<?}else if ($ClassOrderPayProgress==33){?>
														취소완료(<?=substr($ClassOrderPayCencelDateTime,0,10)?>)
													<?}else if ($ClassOrderPayProgress==41){?>
														환불요청
													<?}else if ($ClassOrderPayProgress==43){?>
														환불완료
													<?}else{?>
														<a class="md-btn md-btn-primary md-btn-small md-btn-wave-light" href="javascript:PayPreAction(<?=$ClassOrderID?>);">결제하기</a>
													<?}?>
												<?
												}else{
												?>
												-
												<?
												}
												?>

											</td>
											-->
										<?}?>
										<?if ($_LINK_ADMIN_LEVEL_ID_<=4 && $SearchState!="11"){?>
											<!--
											<td class="uk-text-nowrap uk-table-td-center">
												<?if ($ClassProgress==11 && $ClassProductID==1){?>
													<?if ($ClassOrderPayProgress==21 || $ClassOrderPayProgress==33){?>
													<a class="md-btn md-btn-warning md-btn-small md-btn-wave-light" href="javascript:OpenPaySetForm(<?=$ClassOrderPayID?>);">결제관리</a>
													<?}else{?>
													-
													<?}?>
												<?}?>
											</td>
											-->
										<?}?>
<!--										<td class="uk-text-nowrap uk-table-td-center">--><?php //=$StrClassProgress?><!--</td>-->
<!--										<td class="uk-text-nowrap uk-table-td-center">--><?php //=$StrClassOrderState?><!--</td>-->

                                        <td class="uk-text-nowrap uk-table-td-center">
                                            <?= htmlspecialchars($ClassTeacherName, ENT_QUOTES, 'UTF-8') ?><br>
                                            <?= htmlspecialchars($displayDateTime, ENT_QUOTES, 'UTF-8') ?>
                                        </td>


                                        <td class="uk-text-nowrap uk-table-td-center"><?=$StrClassOrderState?></td>




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
function OpenClassDetailList(ClassOrderID, ClassStudyType){
    openurl = "class_detail_list.php?ClassOrderID="+ClassOrderID+"&ClassStudyType="+ClassStudyType;
    $.colorbox({    
        href:openurl
        ,width:"95%"
        ,height:"95%"
        ,maxWidth: "1000"
        ,maxHeight: "800"
        ,title:""
        ,iframe:true 
        ,scrolling:true
        ,onClosed:function(){location.reload(true);}
        //,onComplete:function(){alert(1);}
    });
}


function OpenClassScheduleForm(ClassOrderID){
	openurl = "class_order_schedule_form.php?ClassOrderID="+ClassOrderID;
	window.open(openurl, "class_order_schedule_form"+ClassOrderID, "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1080,height=800");
}


function OpenClassOrderForm(ClassOrderID, ClassOrderPayID){
	openurl = "class_order_form.php?ClassOrderID="+ClassOrderID+"&ClassOrderPayID="+ClassOrderPayID;
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

function OpenClassOrderSlotLog(ClassOrderID){
	openurl = "../class_order_slot_log.php?ClassOrderID="+ClassOrderID;
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

function OpenClassEndDateLog(ClassOrderID){
	openurl = "./class_end_date_log.php?ClassOrderID="+ClassOrderID;
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




<script type="text/javascript">
function PayPreAction(ClassOrderID){
	url = "../ajax_set_class_order_pay.php";
	//location.href = url + "?ClassOrderID="+ClassOrderID;
	$.ajax(url, {
		data: {
			ClassOrderID: ClassOrderID,
			ClassOrderMode: "LMS"
		},
		success: function (data) {
			ClassOrderPayID = data.ClassOrderPayID;
			ClassOrderPayNumber = data.ClassOrderPayNumber;

			OpenPayForm(ClassOrderID, ClassOrderPayID, ClassOrderPayNumber);
		},
		error: function () {

		}
	});

}



function OpenPayForm(ClassOrderID, ClassOrderPayID, ClassOrderPayNumber){
	openurl = "../pop_class_order_pay_form.php?ClassOrderID="+ClassOrderID+"&ClassOrderPayID="+ClassOrderPayID+"&ClassOrderPayNumber="+ClassOrderPayNumber+"&ClassOrderMode=LMS";
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


function OpenPaySetForm(ClassOrderPayID){

	openurl = "../pop_class_order_pay_change_form.php?ClassOrderPayID="+ClassOrderPayID+"&ClassOrderMode=LMS";
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "600"
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
	document.SearchForm.action = "class_order_list.php";
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