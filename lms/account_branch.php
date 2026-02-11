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
$SubMenuID = 2101;
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

$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

$CellID = isset($_REQUEST["CellID"]) ? $_REQUEST["CellID"] : "";
$CellOrder = isset($_REQUEST["CellOrder"]) ? $_REQUEST["CellOrder"] : "";
$OldCellID = isset($_REQUEST["OldCellID"]) ? $_REQUEST["OldCellID"] : "";
$OldCellOrder = isset($_REQUEST["OldCellOrder"]) ? $_REQUEST["OldCellOrder"] : "";

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
	//폼으로 넘김
}else if ($_LINK_ADMIN_LEVEL_ID_==12 or $_LINK_ADMIN_LEVEL_ID_==13){//대리점 관리자
	//접속불가
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

if ($CellID==""){
	$CellID = "1";
	$OldCellID = "1";
}

if ($CellOrder==""){
	$CellOrder = "1";
}

if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.BranchState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.BranchState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and B.BranchGroupState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and C.CompanyState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and D.FranchiseState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.BranchName like '%".$SearchText."%' or A.BranchManagerName like '%".$SearchText."%') ";
}

if ($SearchFranchiseID!=""){
	$ListParam = $ListParam . "&SearchFranchiseID=" . $SearchFranchiseID;
	$AddSqlWhere = $AddSqlWhere . " and C.FranchiseID=$SearchFranchiseID ";
}

if ($SearchCompanyID!=""){
	$ListParam = $ListParam . "&SearchCompanyID=" . $SearchCompanyID;
	$AddSqlWhere = $AddSqlWhere . " and B.CompanyID=$SearchCompanyID ";
}

if ($SearchBranchGroupID!=""){
	$ListParam = $ListParam . "&SearchBranchGroupID=" . $SearchBranchGroupID;
	$AddSqlWhere = $AddSqlWhere . " and A.BranchGroupID=$SearchBranchGroupID ";
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

$AddSqlWhere2 = $AddSqlWhere2 . " and (AAA.ClassOrderPayProgress=21 or AAA.ClassOrderPayProgress=31 or AAA.ClassOrderPayProgress=41) ";


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from Branches A 
			inner join BranchGroups B on A.BranchGroupID=B.BranchGroupID 
			inner join Companies C on B.CompanyID=C.CompanyID 
			inner join Franchises D on C.FranchiseID=D.FranchiseID 
			inner join Members G on A.BranchID=G.BranchID and G.MemberLevelID=9 
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
			AES_DECRYPT(UNHEX(A.BranchPhone1),:EncryptionKey) as DecBranchPhone1,
			AES_DECRYPT(UNHEX(A.BranchPhone2),:EncryptionKey) as DecBranchPhone2,
			B.BranchGroupName,
			C.CompanyName,
			G.MemberLoginID,
			D.FranchiseName,

			(select sum(AAA.ClassOrderPayUseCashPrice-AAA.ClassOrderPayPgFeePrice-(AAA.ClassOrderPayUseCashPrice * AAA.ClassOrderPayPgFeeRatio / 100)) from ClassOrderPays AAA inner join Members BBB on AAA.ClassOrderPayPaymentMemberID=BBB.MemberID inner join Centers CCC on AAA.CenterID=CCC.CenterID where ".$AddSqlWhere2." and CCC.BranchID=A.BranchID) as TotalClassOrderPayUseCashPrice,

			(select sum((AAA.ClassOrderPayUseCashPrice-AAA.ClassOrderPayPgFeePrice-(AAA.ClassOrderPayUseCashPrice * AAA.ClassOrderPayPgFeeRatio / 100)) * ( (AAA.CenterPricePerTime - AAA.CompanyPricePerTime) / AAA.CenterPricePerTime )) * 0.967 from ClassOrderPays AAA inner join Members BBB on AAA.ClassOrderPayPaymentMemberID=BBB.MemberID inner join Centers CCC on AAA.CenterID=CCC.CenterID where ".$AddSqlWhere2." and CCC.BranchID=A.BranchID) as TotalBranchFee

		from Branches A 
			inner join BranchGroups B on A.BranchGroupID=B.BranchGroupID 
			inner join Companies C on B.CompanyID=C.CompanyID 
			inner join Franchises D on C.FranchiseID=D.FranchiseID 
			inner join Members G on A.BranchID=G.BranchID and G.MemberLevelID=9 
		where ".$AddSqlWhere." 
		";//order by A.BranchOrder desc limit $StartRowNum, $PageListNum";
$AddSqlWhere3 = ($CellOrder==1)? "desc":"asc";
if ($CellID=="1"){
	$Sql = "select * from ($ViewTable) V order by V.TotalClassOrderPayUseCashPrice ".$AddSqlWhere3;
}else{
	$Sql = "select * from ($ViewTable) V order by V.TotalBranchFee ".$AddSqlWhere3;
}

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$지사선택[$LangID]?></h3>

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
										echo "<optgroup label=\"$프랜차이즈[$LangID]($운영중[$LangID])\">";
									}else if ($SelectFranchiseState==2){
										echo "<optgroup label=\"$프랜차이즈[$LangID]($미운영[$LangID])\">";
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

					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
						<select id="SearchYear" name="SearchYear" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%;"/>
							<option value=""><?=$년도선택[$LangID]?></option>
							<?
							for ($iiii=2018;$iiii<=2020;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
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
						<label for="SearchText"><?=$지사명_또는_관리자명[$LangID]?></label>
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
							
							<div style="text-align:right;">※ 결제(PG)수수료를 제외한 금액입니다.</div>
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>순위</th>
										<th nowrap><?=$지사명[$LangID]?></th>
										<th nowrap><?=$아이디[$LangID]?></th>
										<th nowrap><?=$관리자[$LangID]?></th>
										<th nowrap><?=$대표지사명[$LangID]?></th>
										<th nowrap><a href="javascript:SetOrderList(1);"><?=$총매출[$LangID]?> <?if ($CellOrder=="1" && $CellID=="1"){?>▼<?} else if($CellOrder=="2" && $CellID=="1") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(2);"><?=$커미션_3점3_제외[$LangID]?><?if ($CellOrder=="1" && $CellID=="2"){?>▼<?} else if($CellOrder=="2" && $CellID=="2") {?>▲<?}?></a></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									$MaxTotalClassOrderPayUseCashPrice = 0;
									$MaxTotalBranchFee = 0;
									$MaxChartWidth = 200;
									$SumTotalClassOrderPayUseCashPrice = 0;
									$SumTotalBranchFee = 0;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;



										$BranchID = $Row["BranchID"];
										$BranchName = $Row["BranchName"];
										$BranchManagerName = $Row["BranchManagerName"];
										$BranchPhone1 = $Row["DecBranchPhone1"];
										$BranchPhone2 = $Row["DecBranchPhone2"];
										$BranchState = $Row["BranchState"];
										$BranchGroupName = $Row["BranchGroupName"];
										$CompanyName = $Row["CompanyName"];
										$MemberLoginID = $Row["MemberLoginID"];
										$FranchiseName = $Row["FranchiseName"];

										$TotalClassOrderPayUseCashPrice = $Row["TotalClassOrderPayUseCashPrice"];
										$TotalBranchFee = $Row["TotalBranchFee"];
										
										if ($BranchState==1){
											$StrBranchState = "<span class=\"ListState_1\">운영중</span>";
										}else if ($BranchState==2){
											$StrBranchState = "<span class=\"ListState_2\">미운영</span>";
										}


										// 첫번째 값에서만 체크,
										if($CellOrder==1) {
											if ($ListCount==1){
												$MaxTotalClassOrderPayUseCashPrice = $TotalClassOrderPayUseCashPrice;
												$MaxTotalBranchFee = $TotalBranchFee;
											}
										} else if($CellOrder==2) {
											//if ($ListCount==$TotalRowCount){
											//	$MaxTotalClassOrderPayUseCashPrice = $TotalClassOrderPayUseCashPrice;
											//	$MaxTotalBranchFee = $TotalBranchFee;
											//}
										}
										
										if ($CellID==1){
											if ($MaxTotalClassOrderPayUseCashPrice!=0){
												$ChartWidth = $MaxChartWidth *  $TotalClassOrderPayUseCashPrice / $MaxTotalClassOrderPayUseCashPrice;
												
												?><script>//alert(<?=$TotalRowCount?>);</script><?

											}else{
												$ChartWidth = 0;
											}
										}else if($CellID==2) {
											if ($MaxTotalBranchFee!=0){
												$ChartWidth = $MaxChartWidth *  $TotalBranchFee / $MaxTotalBranchFee;
											}else{
												$ChartWidth = 0;
											}
										}

										$SumTotalClassOrderPayUseCashPrice = $SumTotalClassOrderPayUseCashPrice + $TotalClassOrderPayUseCashPrice;
										$SumTotalBranchFee = $SumTotalBranchFee + $TotalBranchFee;

									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberLoginID?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchManagerName?></td>

										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchGroupName?></td>

										<td class="uk-text-nowrap uk-table-td-center" style="color:#AE0000;"><?=number_format($TotalClassOrderPayUseCashPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center" style="color:#AE0000;"><?=number_format($TotalBranchFee,0)?></td>

									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;

									if ($ListCount==1){
										$AvgTotalClassOrderPayUseCashPrice = 0;
										$AvgTotalBranchFee = 0;
									}else{
										$AvgTotalClassOrderPayUseCashPrice = $SumTotalClassOrderPayUseCashPrice / ($ListCount-1);
										$AvgTotalBranchFee = $SumTotalBranchFee / ($ListCount-1);
									}
									?>
									<tr style="background-color:#f1f1f1;">
										<td class="uk-text-nowrap uk-table-td-center" colspan="5"><?=$합계[$LangID]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTotalClassOrderPayUseCashPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTotalBranchFee,0)?></td>

										<!--<td class="uk-text-nowrap uk-table-td"></td>-->
									</tr>
									<tr style="background-color:#f1f1f1;">
										<td class="uk-text-nowrap uk-table-td-center" colspan="5"><?=$평균[$LangID]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgTotalClassOrderPayUseCashPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgTotalBranchFee,0)?></td>

										<!--<td class="uk-text-nowrap uk-table-td"></td>-->
									</tr>

								</tbody>
							</table>
						</div>
						

						<?php			
						//include_once('./inc_pagination.php');
						?>
						<!--
						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="branch_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
	document.SearchForm.action = "account_branch.php";
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