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
$MainMenuID = 21;
$SubMenuID = 2106;
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

$SearchAbsenceContinueCount = isset($_REQUEST["SearchAbsenceContinueCount"]) ? $_REQUEST["SearchAbsenceContinueCount"] : "";
$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

$CellID = isset($_REQUEST["CellID"]) ? $_REQUEST["CellID"] : "";
$CellOrder = isset($_REQUEST["CellOrder"]) ? $_REQUEST["CellOrder"] : "";
$OldCellID = isset($_REQUEST["OldCellID"]) ? $_REQUEST["OldCellID"] : "";
$OldCellOrder = isset($_REQUEST["OldCellOrder"]) ? $_REQUEST["OldCellOrder"] : "";


if ($CellID==""){
	$CellID = "5";
	$OldCellID = "5";
}

if ($CellOrder==""){
	$CellOrder = "1";
}

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


if ($SearchBranchID=="" && $SearchCenterID==""){
	$SearchBranchID = -1;
	$SearchCenterID = -1;
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
$AddSqlWhere = $AddSqlWhere . " and AA.ClassOrderState=1  "; // 0:완전삭제 1:정상 2:종료대상 3:종료완료 4:장기홀드
//$AddSqlWhere = $AddSqlWhere . " and AA.ClassProgress=11 ";//1:스케줄대상 11:스케줄완료 
//$AddSqlWhere = $AddSqlWhere . " and (AA.OrderProgress=21 or AA.OrderProgress=31 or AA.OrderProgress=41) ";//1:DB등록 11:주문완료 21:결제완료 31:취소요청 33:취소완료 41:환불요청 43:환불완료


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
		from ClassOrders AA 
			inner join Members A on AA.MemberID=A.MemberID 
			inner join Centers B on A.CenterID=B.CenterID 
			inner join Branches C on B.BranchID=C.BranchID 
			inner join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
			inner join Companies E on D.CompanyID=E.CompanyID 
			inner join Franchises F on E.FranchiseID=F.FranchiseID 
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
			F.FranchiseName,

			B.CenterPricePerTime,
			AA.ClassOrderID,
			AA.ClassOrderStartDate,
			AA.ClassOrderEndDate,

			(select count(*) from Classes where ClassAttendState<>99 and ClassOrderID=AA.ClassOrderID and StartYear=".$SearchYear." and StartMonth=".$SearchMonth.") as RegClassCount,

			(select count(*) from Classes where ClassState=2 and ClassAttendState<>99 and ClassOrderID=AA.ClassOrderID and StartYear=".$SearchYear." and StartMonth=".$SearchMonth.") as StartClassCount,

			(select count(*) from Classes where ClassOrderID=AA.ClassOrderID and ClassAttendState<>99 and ClassAttendState>=4 and ClassAttendState<=5 and StartYear=".$SearchYear." and StartMonth=".$SearchMonth.") as DelayClassCount,

			(select count(*) from Classes where ClassState=2 and ClassOrderID=AA.ClassOrderID and ClassAttendState<>99 and (ClassAttendState=1 or ClassAttendState=2) and StartYear=".$SearchYear." and StartMonth=".$SearchMonth.") as AttendClassCount,
			
			(select count(*) from Classes where ClassState=2 and ClassOrderID=AA.ClassOrderID and ClassAttendState<>99 and ClassAttendState=3 and StartYear=".$SearchYear." and StartMonth=".$SearchMonth.") as AbsenceClassCount,

			(select avg(AAA.AssmtStudentDailyScore1+AAA.AssmtStudentDailyScore2+AAA.AssmtStudentDailyScore3+AAA.AssmtStudentDailyScore4+AAA.AssmtStudentDailyScore5) from AssmtStudentDailyScores AAA inner join Classes BBB on AAA.ClassID=BBB.ClassID where BBB.ClassOrderID=AA.ClassOrderID and BBB.StartYear=".$SearchYear." and BBB.StartMonth=".$SearchMonth.") as AssmtScore,
			(select count(*) from ClassVideoPlayLogs AAA inner join Classes BBB on AAA.ClassID=BBB.ClassID where BBB.ClassOrderID=AA.ClassOrderID and BBB.StartYear=".$SearchYear." and BBB.StartMonth=".$SearchMonth.") as VideoCount,
			(select count(*) from BookQuizResults AAA inner join Classes BBB on AAA.ClassID=BBB.ClassID where BBB.ClassOrderID=AA.ClassOrderID and BBB.StartYear=".$SearchYear." and BBB.StartMonth=".$SearchMonth.") as QuizCount
		

		from ClassOrders AA 
			inner join Members A on AA.MemberID=A.MemberID 
			inner join Centers B on A.CenterID=B.CenterID 
			inner join Branches C on B.BranchID=C.BranchID 
			inner join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
			inner join Companies E on D.CompanyID=E.CompanyID 
			inner join Franchises F on E.FranchiseID=F.FranchiseID 
		where ".$AddSqlWhere." 
		order by A.MemberName asc, A.MemberRegDateTime desc";// limit $StartRowNum, $PageListNum";

$AddSqlWhere3 = ($CellOrder==1)? "desc":"asc";
if ($CellID=="1"){
	$Sql = "select * from ($ViewTable) V order by V.AssmtScore ".$AddSqlWhere3;
} else if($CellID=="2"){
	$Sql = "select * from ($ViewTable) V order by V.VideoCount ".$AddSqlWhere3;
} else if($CellID=="3"){
	$Sql = "select * from ($ViewTable) V order by V.QuizCount ".$AddSqlWhere3;
} else if($CellID=="4"){
	$Sql = "select * from ($ViewTable) V order by V.RegClassCount ".$AddSqlWhere3;
} else if($CellID=="5"){
	$Sql = "select * from ($ViewTable) V order by V.StartClassCount ".$AddSqlWhere3;
} else if($CellID=="6"){
	$Sql = "select * from ($ViewTable) V order by V.DelayClassCount ".$AddSqlWhere3;
} else if($CellID=="7"){
	$Sql = "select * from ($ViewTable) V order by V.AbsenceClassCount ".$AddSqlWhere3;
} else if($CellID=="8"){
	$Sql = "select * from ($ViewTable) V order by V.AttendClassCount ".$AddSqlWhere3;
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$학생수업통계[$LangID]?></h3>

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
							<option value="<?=$iiii?>" <?if ($SearchMonth==$iiii){?>selected<?}?>><?=$iiii?><?=$월월[$LangID]?></option>
							<?
							}
							?>
						</select>
					</div>

					<!--
					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
						<select id="SearchAbsenceContinueCount" name="SearchAbsenceContinueCount" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="연속결석" style="width:100%;"/>
							<option value="0">연속결석</option>
							<?
							for ($iiii=1;$iiii<=4;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchAbsenceContinueCount==$iiii){?>selected<?}?>><?=$iiii?> 회</option>
							<?
							}
							?>
							<option value="5" <?if ($SearchAbsenceContinueCount==5){?>selected<?}?>>5 회이상</option>
						</select>
					</div>
					-->

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
										<th nowrap><input name="CheckBoxAll" type="checkbox" onclick="CheckListAll(this)"></th>
										<th nowrap>No</th>
										<th nowrap><?=$학생명_아이디[$LangID]?></th>
										<th nowrap><?=$수강시작일[$LangID]?></th>
										<th nowrap><?=$수강종료일[$LangID]?></th>


										
										<th nowrap><a href="javascript:SetOrderList(1);" ><?=$평가평균[$LangID]?><?if ($CellOrder=="1" && $CellID=="1"){?>▼<?} else if($CellOrder=="2" && $CellID=="1") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(2);" ><?=$레슨비디오[$LangID]?><?if ($CellOrder=="1" && $CellID=="2"){?>▼<?} else if($CellOrder=="2" && $CellID=="2") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(3);" ><?=$리뷰퀴즈[$LangID]?><?if ($CellOrder=="1" && $CellID=="3"){?>▼<?} else if($CellOrder=="2" && $CellID=="3") {?>▲<?}?></a></th>
										<!--<th nowrap>컴플레인</th>-->
										<th nowrap><a href="javascript:SetOrderList(4);" ><?=$수업등록[$LangID]?><?if ($CellOrder=="1" && $CellID=="4"){?>▼<?} else if($CellOrder=="2" && $CellID=="4") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(5);" ><?=$수업진행[$LangID]?><?if ($CellOrder=="1" && $CellID=="5"){?>▼<?} else if($CellOrder=="2" && $CellID=="5") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(6);" ><?=$연기회수[$LangID]?><?if ($CellOrder=="1" && $CellID=="6"){?>▼<?} else if($CellOrder=="2" && $CellID=="6") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(8);" ><?=$출석회수[$LangID]?><?if ($CellOrder=="1" && $CellID=="8"){?>▼<?} else if($CellOrder=="2" && $CellID=="8") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(7);" ><?=$결석회수[$LangID]?><?if ($CellOrder=="1" && $CellID=="7"){?>▼<?} else if($CellOrder=="2" && $CellID=="7") {?>▲<?}?></a></th>
										<th nowrap><?=$출석율[$LangID]?></th>
										<!--<th nowrap>연속결석(최근)</th>-->
										<!--<th nowrap>출석율 차트</th>-->
									</tr>
								</thead>
								<tbody>
									
								<?php
								$ListCount = 1;
								$MaxChartWidth = 200;
								$SumAbsenceContinueCount = 0;
								$SumVideoCount = 0;
								$SumQuizCount = 0;
								$SumRegClassCount = 0;
								$SumStartClassCount = 0;
								$SumDelayClassCount = 0;
								$SumAbsenceClassCount = 0;
								$SumAttendClassCount = 0;
								$SumAssmtScore = 0;
								$SumAttendClassRatio = 0;



								while($Row = $Stmt->fetch()) {
									$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

									$ClassOrderID = $Row["ClassOrderID"];

									$MemberID = $Row["MemberID"];
									$MemberName = $Row["MemberName"];
									$MemberLoginID = $Row["MemberLoginID"];

									$CenterPricePerTime = $Row["CenterPricePerTime"];
									

									$RegClassCount = $Row["RegClassCount"];
									$StartClassCount = $Row["StartClassCount"];
									$DelayClassCount = $Row["DelayClassCount"];
									$AbsenceClassCount = $Row["AbsenceClassCount"];
									$AttendClassCount = $Row["AttendClassCount"];
									$AssmtScore = $Row["AssmtScore"];

									$VideoCount = $Row["VideoCount"];
									$QuizCount = $Row["QuizCount"];

									$ClassOrderStartDate = $Row["ClassOrderStartDate"];
									$ClassOrderEndDate = $Row["ClassOrderEndDate"];

									
									$AbsenceContinueCount = 0;
									//if ($SearchAbsenceContinueCount!="0"){
										
										$Sql2 = "select * from Classes where ClassOrderID=$ClassOrderID and datediff(StartDateTime, now())<=0 order by StartDateTimeStamp desc";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

										
										$ContinueCount = 1;
										while($Row2 = $Stmt2->fetch()) {
											$ClassAttendState = $Row2["ClassAttendState"];
											if ($ClassAttendState==3 && $ContinueCount==1){
												$AbsenceContinueCount++;
											}else{
												$ContinueCount = 0;
											}
										}
										$Stmt2 = null;

									//}
									
									if ($SearchAbsenceContinueCount==0 || ($SearchAbsenceContinueCount>0 && $SearchAbsenceContinueCount<5 && $AbsenceContinueCount==$SearchAbsenceContinueCount) || ($SearchAbsenceContinueCount>=5 && $AbsenceContinueCount>=5) ) {


										if ($StartClassCount+$AbsenceClassCount!=0){
											$AttendClassRatio = round(100 * $StartClassCount / ($StartClassCount+$AbsenceClassCount),0);
											$ChartWidth = $MaxChartWidth *  $StartClassCount / ($StartClassCount+$AbsenceClassCount);
										}else{
											$AttendClassRatio = 0;
											$ChartWidth = 0;
										}

										$SumAbsenceContinueCount = $SumAbsenceContinueCount + $AbsenceContinueCount;
										$SumVideoCount = $SumVideoCount + $VideoCount;
										$SumQuizCount = $SumQuizCount + $QuizCount;
										$SumRegClassCount = $SumRegClassCount + $RegClassCount;
										$SumStartClassCount = $SumStartClassCount + $StartClassCount;
										$SumDelayClassCount = $SumDelayClassCount + $DelayClassCount;
										$SumAbsenceClassCount = $SumAbsenceClassCount + $AbsenceClassCount;
										$SumAttendClassCount = $SumAttendClassCount + $AttendClassCount;
										$SumAssmtScore = $SumAssmtScore + $AssmtScore;
										$SumAttendClassRatio = $SumAttendClassRatio + $AttendClassRatio;
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><input name="CheckBox_<?=$ListCount?>" id="CheckBox_<?=$ListCount?>" type="checkbox" value="<?=$MemberID?>"></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberName?>(<?=$MemberLoginID?>)</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderStartDate?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderEndDate?></td>


										
										<td class="uk-text-nowrap uk-table-td-center"><?=round($AssmtScore,0)?> <a href="javascript:OpenAssmtScoreDetail(<?=$MemberID?>)"><i class="material-icons">flip_to_front</i></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$VideoCount?> <a href="javascript:OpenVideoDetail(<?=$MemberID?>)"><i class="material-icons">flip_to_front</i></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$QuizCount?> <a href="javascript:OpenQuizDetail(<?=$MemberID?>)"><i class="material-icons">flip_to_front</i></a></td>
										<!--<td class="uk-text-nowrap uk-table-td-center">-</td>-->
										<td class="uk-text-nowrap uk-table-td-center"><?=$RegClassCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StartClassCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$DelayClassCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$AttendClassCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$AbsenceClassCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$AttendClassRatio?>%</td>
										<!--<td class="uk-text-nowrap uk-table-td-center"><?=$AbsenceContinueCount?></td>-->
										<!--
										<td class="uk-text-nowrap uk-table-td">
											<span style="display:inline-block;height:15px;background-color:#1D76CE;width:<?=$ChartWidth?>px;"></span>
										</td>
										-->
									</tr>
								<?php
										$ListCount ++;
									}
								}
								
							
								$Stmt = null;


								if ($ListCount==1){
									$AvgAbsenceContinueCount = 0;
									$AvgVideoCount = 0;
									$AvgQuizCount = 0;
									$AvgRegClassCount = 0;
									$AvgStartClassCount = 0;
									$AvgDelayClassCount = 0;
									$AvgAbsenceClassCount = 0;
									$AvgAttendClassCount = 0;
									$AvgAssmtScore = 0;
									$AvgAttendClassRatio = 0;
								}else{
									$AvgAbsenceContinueCount = $SumAbsenceContinueCount / ($ListCount-1);
									$AvgVideoCount = $SumVideoCount / ($ListCount-1);
									$AvgQuizCount = $SumQuizCount / ($ListCount-1);
									$AvgRegClassCount = $SumRegClassCount / ($ListCount-1);
									$AvgStartClassCount = $SumStartClassCount / ($ListCount-1);
									$AvgDelayClassCount = $SumDelayClassCount / ($ListCount-1);
									$AvgAbsenceClassCount = $SumAbsenceClassCount / ($ListCount-1);
									$AvgAttendClassCount = $SumAttendClassCount / ($ListCount-1);
									$AvgAssmtScore = $SumAssmtScore / ($ListCount-1);
									$AvgAttendClassRatio = $SumAttendClassRatio / ($ListCount-1);
								}

								?>

								<?
								if ($ListCount==1 && $_LINK_ADMIN_LEVEL_ID_<9){
								?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center" colspan="19" style="height:100px;">지사 또는 가맹점을 검색하시기 바랍니다.</td>
									</tr>
								<?
								}
								?>

								<tr style="background-color:#f1f1f1;">
									<td class="uk-text-nowrap uk-table-td-center" colspan="5"><?=$합계[$LangID]?></td>
									
									<td class="uk-text-nowrap uk-table-td-center">-<!--<?=number_format($SumAssmtScore,0)?>--></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumVideoCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumQuizCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumRegClassCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumStartClassCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumDelayClassCount,0)?></td>
									
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumAttendClassCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumAbsenceClassCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center">-<!--<?=number_format($SumAttendClassRatio,0)?>--></td>
									<!--<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumAbsenceContinueCount,0)?></td>-->
									<!--<td class="uk-text-nowrap uk-table-td-center"></td>-->
								</tr>
								<tr style="background-color:#f1f1f1;">
									<td class="uk-text-nowrap uk-table-td-center" colspan="5"><?=$평균[$LangID]?></td>
									
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format(round($AvgAssmtScore,0),0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgVideoCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgQuizCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgRegClassCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgStartClassCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgDelayClassCount,0)?></td>
									
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgAttendClassCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgAbsenceClassCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgAttendClassRatio,0)?>%</td>
									<!--<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgAbsenceContinueCount,0)?></td>-->
									<!--<td class="uk-text-nowrap uk-table-td-center"></td>-->
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




<?
include_once('./inc_common_list_js.php');
?>
<!-- ============== only this page js ============== -->


<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


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


function OpenAssmtScoreDetail(MemberID){
	openurl = "../student_assmt_detail.php?MemberID="+MemberID;
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
function OpenVideoDetail(MemberID){
	openurl = "../student_video_detail.php?MemberID="+MemberID;
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
function OpenQuizDetail(MemberID){
	openurl = "../student_quiz_detail.php?MemberID="+MemberID;
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
	document.SearchForm.action = "account_study_total.php";
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