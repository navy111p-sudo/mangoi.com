<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

if ($_LINK_ADMIN_LEVEL_ID_>7){
	header("Location: branch_form.php?BranchID=".$_LINK_ADMIN_BRANCH_ID_); 
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
$SubMenuID = 2108;
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
$SearchEduCenterID = isset($_REQUEST["SearchEduCenterID"]) ? $_REQUEST["SearchEduCenterID"] : "";
$SearchTeacherGroupID = isset($_REQUEST["SearchTeacherGroupID"]) ? $_REQUEST["SearchTeacherGroupID"] : "";


$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

$CellID = isset($_REQUEST["CellID"]) ? $_REQUEST["CellID"] : "";
$CellOrder = isset($_REQUEST["CellOrder"]) ? $_REQUEST["CellOrder"] : "";
$OldCellID = isset($_REQUEST["OldCellID"]) ? $_REQUEST["OldCellID"] : "";
$OldCellOrder = isset($_REQUEST["OldCellOrder"]) ? $_REQUEST["OldCellOrder"] : "";


if ($CellID==""){
	$CellID = "2";
	$OldCellID = "2";
}

if ($CellOrder==""){
	$CellOrder = "1";
}

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
	//폼으로 넘김
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
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.TeacherState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and B.TeacherGroupState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and C.EduCenterState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and D.FranchiseState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.TeacherName like '%".$SearchText."%' or A.TeacherNickName like '%".$SearchText."%') ";
}

if ($SearchFranchiseID!=""){
	$ListParam = $ListParam . "&SearchFranchiseID=" . $SearchFranchiseID;
	$AddSqlWhere = $AddSqlWhere . " and C.FranchiseID=$SearchFranchiseID ";
}

if ($SearchEduCenterID!=""){
	$ListParam = $ListParam . "&SearchEduCenterID=" . $SearchEduCenterID;
	$AddSqlWhere = $AddSqlWhere . " and B.EduCenterID=$SearchEduCenterID ";
}

if ($SearchTeacherGroupID!=""){
	$ListParam = $ListParam . "&SearchTeacherGroupID=" . $SearchTeacherGroupID;
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherGroupID=$SearchTeacherGroupID ";
}


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from Teachers A 
			inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
			inner join EduCenters C on B.EduCenterID=C.EduCenterID 
			inner join Franchises D on C.FranchiseID=D.FranchiseID 
			inner join Members G on A.TeacherID=G.TeacherID and G.MemberLevelID=15 
			left outer join MemberTimeZones Z on G.MemberTimeZoneID=Z.MemberTimeZoneID 
			left outer join TeacherPayTypeItems I on A.TeacherPayTypeItemID=I.TeacherPayTypeItemID
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
			AES_DECRYPT(UNHEX(A.TeacherPhone1),:EncryptionKey) as DecTeacherPhone1,
			AES_DECRYPT(UNHEX(A.TeacherPhone2),:EncryptionKey) as DecTeacherPhone2,
			B.TeacherGroupName,
			C.EduCenterName,
			G.MemberLoginID,
			G.MemberID,
			Z.MemberTimeZoneName,
			I.TeacherPayTypeItemTitle,
			D.FranchiseName,
			
			(select count(*) from ClassOrders AAA where AAA.ClassOrderID in (select ClassOrderID from ClassOrderSlots where TeacherID=A.TeacherID and ClassOrderSlotType=1) and AAA.ClassOrderState>=1 and AAA.ClassOrderState<=4 and AAA.ClassProgress=11 ) as TotalTeacherClassOrder,
			(select count(*) from ClassOrders AAA where AAA.ClassOrderID in (select ClassOrderID from ClassOrderSlots where TeacherID=A.TeacherID and ClassOrderSlotType=1) and AAA.ClassOrderState>=1 and AAA.ClassOrderState<=4 and AAA.ClassProgress=11 and AAA.ClassOrderState=3) as TotalTeacherEndClassOrder,

			(select sum(MemberPoint) from MemberPoints where MemberID=G.MemberID and MemberPointState=1) as SumMemberPoint

		from Teachers A 
			inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
			inner join EduCenters C on B.EduCenterID=C.EduCenterID 
			inner join Franchises D on C.FranchiseID=D.FranchiseID 
			inner join Members G on A.TeacherID=G.TeacherID and G.MemberLevelID=15 
			left outer join MemberTimeZones Z on G.MemberTimeZoneID=Z.MemberTimeZoneID 
			left outer join TeacherPayTypeItems I on A.TeacherPayTypeItemID=I.TeacherPayTypeItemID
		where ".$AddSqlWhere." 
		";//order by A.TeacherOrder desc limit $StartRowNum, $PageListNum";

$AddSqlWhere3 = ($CellOrder==1)? "desc":"asc";
if ($CellID=="1"){
	$Sql = "select * from ($ViewTable) V order by V.SumMemberPoint ".$AddSqlWhere3;
} else if($CellID=="2"){
	$Sql = "select * from ($ViewTable) V order by V.TotalTeacherClassOrder ".$AddSqlWhere3;
} else if($CellID=="3"){
	$Sql = "select * from ($ViewTable) V order by V.TotalTeacherEndClassOrder ".$AddSqlWhere3;
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$강사수업통계[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<input type="hidden" name="CellID" id="CellID" value="<?=$CellID?>"/>
		<input type="hidden" name="CellOrder" id="CellOrder" value="<?=$CellOrder?>"/>
		<input type="hidden" name="OldCellID" id="OldCellID" value="<?=$OldCellID?>"/>
		<input type="hidden" name="OldCellOrder" id="OldCellOrder" value="<?=$OldCellOrder?>"/>

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

					<div class="uk-width-medium-2-10" style="padding-top:7px;display:">
						<select id="SearchEduCenterID" name="SearchEduCenterID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$교육센터선택[$LangID]?>" style="width:100%;"/>
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
									from EduCenters A 
										inner join Franchises B on A.FranchiseID=B.FranchiseID 
									where A.EduCenterState<>0 and B.FranchiseState<>0 ".$AddWhere2." 
									order by A.EduCenterState asc, A.EduCenterName asc";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldSelectEduCenterState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectEduCenterID = $Row2["EduCenterID"];
								$SelectEduCenterName = $Row2["EduCenterName"];
								$SelectEduCenterState = $Row2["EduCenterState"];
							
								if ($OldSelectEduCenterState!=$SelectEduCenterState){
									if ($OldSelectEduCenterState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectEduCenterState==1){
										echo "<optgroup label=\"교육센터(운영중)\">";
									}else if ($SelectEduCenterState==2){
										echo "<optgroup label=\"교육센터(미운영)\">";
									}
								}
								$OldSelectEduCenterState = $SelectEduCenterState;
							?>

							<option value="<?=$SelectEduCenterID?>" <?if ($SearchEduCenterID==$SelectEduCenterID){?>selected<?}?>><?=$SelectEduCenterName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>
					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						<select id="SearchTeacherGroupID" name="SearchTeacherGroupID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$강사그룹선택[$LangID]?>" style="width:100%;"/>
							<option value=""></option>
							<?
							$AddWhere2 = "";
							if ($SearchEduCenterID!=""){
								$AddWhere2 = "and A.EduCenterID=".$SearchEduCenterID." ";
							}else{
								if ($SearchFranchiseID!=""){
									$AddWhere2 = "and B.FranchiseID=".$SearchFranchiseID." ";
								}else{
									$AddWhere2 = " ";
								}
							}
							$Sql2 = "select 
											A.* 
									from TeacherGroups A 
										inner join EduCenters B on A.EduCenterID=B.EduCenterID 
										inner join Franchises C on B.FranchiseID=C.FranchiseID 
									where A.TeacherGroupState<>0 and B.EduCenterState<>0 and C.FranchiseState<>0 ".$AddWhere2." 
									order by A.TeacherGroupState asc, A.TeacherGroupName asc";
							
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldSelectTeacherGroupState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectTeacherGroupID = $Row2["TeacherGroupID"];
								$SelectTeacherGroupName = $Row2["TeacherGroupName"];
								$SelectTeacherGroupState = $Row2["TeacherGroupState"];
							
								if ($OldSelectTeacherGroupState!=$SelectTeacherGroupState){
									if ($OldSelectTeacherGroupState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectTeacherGroupState==1){
										echo "<optgroup label=\"강사그룹(운영중)\">";
									}else if ($SelectTeacherGroupState==2){
										echo "<optgroup label=\"강사그룹(미운영)\">";
									}
								}
								$OldSelectTeacherGroupState = $SelectTeacherGroupState;
							?>

							<option value="<?=$SelectTeacherGroupID?>" <?if ($SearchTeacherGroupID==$SelectTeacherGroupID){?>selected<?}?>><?=$SelectTeacherGroupName?></option>
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
						<label for="SearchText"><?=$강사명[$LangID]?></label>
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
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$활동중[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$미활동[$LangID]?></option>
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
										<th nowrap><?=$강사명[$LangID]?></th>
										<th nowrap><a href="javascript:SetOrderList(1);" ><?=$포인트[$LangID]?> <?if ($CellOrder=="1" && $CellID=="1"){?>▼<?} else if($CellOrder=="2" && $CellID=="1") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(2);" ><?=$누적전체강의[$LangID]?> <?if ($CellOrder=="1" && $CellID=="2"){?>▼<?} else if($CellOrder=="2" && $CellID=="2") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(3);" ><?=$누적종료강의[$LangID]?> <?if ($CellOrder=="1" && $CellID=="3"){?>▼<?} else if($CellOrder=="2" && $CellID=="3") {?>▲<?}?></a></th>
										<th nowrap><?=$종료율[$LangID]?></th>
										<th nowrap><?=$평균수강개월[$LangID]?></th>
										<th nowrap><?=$연장율[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									$SumTotalTeacherClassOrder = 0;
									$SumTotalTeacherEndClassOrder = 0;
									$SumTotalTeacherEndClassOrderPercent = 0;
									$SumAvgStudyMonth = 0;
									$SumTeacherClassRelRatio = 0;
									$SumSumMemberPoint = 0;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$MemberID = $Row["MemberID"];
										$TeacherID = $Row["TeacherID"];
										$TeacherName = $Row["TeacherName"];
										$TeacherNickName = $Row["TeacherNickName"];
										$TeacherPhone1 = $Row["DecTeacherPhone1"];
										$TeacherPhone2 = $Row["DecTeacherPhone2"];
										$TeacherState = $Row["TeacherState"];
										$TeacherGroupName = $Row["TeacherGroupName"];
										$EduCenterName = $Row["EduCenterName"];
										$MemberLoginID = $Row["MemberLoginID"];
										$FranchiseName = $Row["FranchiseName"];
										$TeacherPayPerTime = $Row["TeacherPayPerTime"];
										$MemberTimeZoneName = $Row["MemberTimeZoneName"];
										$TeacherPayTypeItemTitle = $Row["TeacherPayTypeItemTitle"];

										$TotalTeacherClassOrder = $Row["TotalTeacherClassOrder"];
										$TotalTeacherEndClassOrder = $Row["TotalTeacherEndClassOrder"];

										$SumMemberPoint = $Row["SumMemberPoint"];



										$ViewTable2 = "
											select
												A.ClassOrderStartDate,
												case when ClassOrderEndDate is null or ClassOrderEndDate=''
													then date_format(now(), '%Y-%m-%d')
												else
													A.ClassOrderEndDate 
												end ClassOrderEndDate
													
											from ClassOrders A 
												inner join Members B on A.MemberID=B.MemberID 
												inner join Centers C on B.CenterID=C.CenterID 	
											where 
												A.ClassOrderID in (select ClassOrderID from ClassOrderSlots where TeacherID=$TeacherID and ClassOrderSlotType=1)
												and A.ClassOrderState>=1 and A.ClassOrderState<=4 
												and A.ClassProgress=11 
										";
										
										$Sql2 = "
											select 
												round(datediff(V.ClassOrderEndDate, V.ClassOrderStartDate)/30) as AvgStudyMonth
											from ($ViewTable2) V 
											where datediff(V.ClassOrderEndDate, V.ClassOrderStartDate)>=15
										";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$AvgStudyMonth = $Row2["AvgStudyMonth"];



										$Sql2 = "
											select 
												count(*) as TeacherClassCount
											from ClassOrderPayDetails AA 
												inner join ClassOrderPays AAA on AA.ClassOrderPayID=AAA.ClassOrderPayID 
												inner join ClassOrders A on AA.ClassOrderID=A.ClassOrderID  
												inner join Members B on A.MemberID=B.MemberID 
												inner join Centers C on B.CenterID=C.CenterID 
											where 
												(AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41)
												and AA.ClassOrderPayDetailType=1 
												and A.ClassOrderID in (select ClassOrderID from ClassOrderSlots where TeacherID=$TeacherID and ClassOrderSlotType=1) 
												and A.ClassProgress=11 
										";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$TeacherClassCount1 = $Row2["TeacherClassCount"];


										//LMS 에서 수강신청하고 아직 결제 안한것
										$Sql2 = "
											select 
												count(*) as TeacherClassCount
											from ClassOrders AA 
												inner join Members B on AA.MemberID=B.MemberID 
												inner join Centers C on B.CenterID=C.CenterID 
											where 
												AA.ClassOrderID in (select ClassOrderID from ClassOrderSlots where TeacherID=$TeacherID and ClassOrderSlotType=1) 
												and AA.ClassProgress=11 
												and ClassOrderID not in (select AAAAA.ClassOrderID from ClassOrderPayDetails AAAAA inner join ClassOrderPays BBBBB on AAAAA.ClassOrderPayID=BBBBB.ClassOrderPayID and BBBBB.ClassOrderPayProgress>=21) 
												and ClassOrderID not in (select AAAAA.ClassOrderID from ClassOrderPayB2bs    AAAAA inner join ClassOrderPays BBBBB on AAAAA.ClassOrderPayID=BBBBB.ClassOrderPayID and BBBBB.ClassOrderPayProgress>=21)
										";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$TeacherClassCount1 = $TeacherClassCount1 + $Row2["TeacherClassCount"];


										$Sql2 = "
											select 
												count(*) as TeacherClassCount
											from ClassOrderPayDetails AA 
												inner join ClassOrderPays AAA on AA.ClassOrderPayID=AAA.ClassOrderPayID 
												inner join ClassOrders A on AA.ClassOrderID=A.ClassOrderID  
												inner join Members B on A.MemberID=B.MemberID 
												inner join Centers C on B.CenterID=C.CenterID 
											where 
												(AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41) 
												and AA.ClassOrderPayDetailType=2 
												and A.ClassOrderID in (select ClassOrderID from ClassOrderSlots where TeacherID=$TeacherID and ClassOrderSlotType=1) 
												and A.ClassProgress=11 
										";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$TeacherClassCount2 = $Row2["TeacherClassCount"];


										//단체 연장
										$Sql2 = "
											select 
												count(*) as TeacherClassCount
											from ClassOrderPayB2bs AA 
												inner join ClassOrderPays AAA on AA.ClassOrderPayID=AAA.ClassOrderPayID 
												inner join ClassOrders A on AA.ClassOrderID=A.ClassOrderID  
												inner join Members B on A.MemberID=B.MemberID 
												inner join Centers C on B.CenterID=C.CenterID 
											where 
												(AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41) 
												and A.ClassOrderID in (select ClassOrderID from ClassOrderSlots where TeacherID=$TeacherID and ClassOrderSlotType=1) 
												and A.ClassProgress=11 
										";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$TeacherClassCount2 = $TeacherClassCount2 + $Row2["TeacherClassCount"];



										if ($TeacherClassCount1!=0){
											$TeacherClassRelRatio = 100 * $TeacherClassCount2 / $TeacherClassCount1;
										}else{
											$TeacherClassRelRatio = 0;
										}

										$SumTotalTeacherClassOrder = $SumTotalTeacherClassOrder + $TotalTeacherClassOrder;
										$SumTotalTeacherEndClassOrder = $SumTotalTeacherEndClassOrder + $TotalTeacherEndClassOrder;
										$SumAvgStudyMonth = $SumAvgStudyMonth + $AvgStudyMonth;
										$SumTeacherClassRelRatio = $SumTeacherClassRelRatio + $TeacherClassRelRatio;
										$SumSumMemberPoint = $SumSumMemberPoint + $SumMemberPoint;
										if($TotalTeacherClassOrder>0) {
											$TotalTeacherEndClassOrderPercent = number_format(($TotalTeacherEndClassOrder/$TotalTeacherClassOrder)*100,0);
											$SumTotalTeacherEndClassOrderPercent = $SumTotalTeacherEndClassOrderPercent + $TotalTeacherEndClassOrderPercent;
											$TotalTeacherEndClassOrderPercent .= "%";
										} else {
											$TotalTeacherEndClassOrderPercent = "-";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumMemberPoint,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalTeacherClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalTeacherEndClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<?=$TotalTeacherEndClassOrderPercent?>
										</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$AvgStudyMonth?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=round($TeacherClassRelRatio,0)?>%</td>
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;


									if ($ListCount==1){
										$AvgTotalTeacherClassOrder = 0;
										$AvgTotalTeacherEndClassOrder = 0;
										$AvgTotalTeacherEndClassOrderPercent = 0;
										$AvgAvgStudyMonth = 0;
										$AvgTeacherClassRelRatio = 0;
										$AvgSumMemberPoint = 0;
									}else{
										$AvgTotalTeacherClassOrder = $SumTotalTeacherClassOrder / ($ListCount-1);
										$AvgTotalTeacherEndClassOrder = $SumTotalTeacherEndClassOrder / ($ListCount-1);
										$AvgTotalTeacherEndClassOrderPercent = $SumTotalTeacherEndClassOrderPercent / ($ListCount-1);
										$AvgAvgStudyMonth = $SumAvgStudyMonth / ($ListCount-1);
										$AvgTeacherClassRelRatio = $SumTeacherClassRelRatio / ($ListCount-1);
										$AvgSumMemberPoint = $SumSumMemberPoint / ($ListCount-1);
									}
									?>

									<tr style="background-color:#f1f1f1;">
										<td class="uk-text-nowrap uk-table-td-center" colspan="2"><?=$합계[$LangID]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumSumMemberPoint,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTotalTeacherClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTotalTeacherEndClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"></td>
										<td class="uk-text-nowrap uk-table-td-center"></td>
										<td class="uk-text-nowrap uk-table-td-center"></td>
									</tr>
									<tr style="background-color:#f1f1f1;">
										<td class="uk-text-nowrap uk-table-td-center" colspan="2"><?=$평균[$LangID]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgSumMemberPoint,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgTotalTeacherClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgTotalTeacherEndClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgTotalTeacherEndClassOrderPercent,0)?>%</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgAvgStudyMonth,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format(round($AvgTeacherClassRelRatio,0),0)?>%</td>
									</tr>
								</tbody>
							</table>
						</div>
						

						<?php			
						//include_once('./inc_pagination.php');
						?>
						<!--
						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="branch_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary">신규등록</a>
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
	document.SearchForm.action = "account_teacher_study.php";
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