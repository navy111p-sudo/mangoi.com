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
if ( $_LINK_ADMIN_LEVEL_ID_>=9  && $_LINK_ADMIN_LEVEL_ID_<=13 ){
	$MainMenuID = 12;
	$SubMenuID = 1299;
}else{
	$MainMenuID = 11;
	$SubMenuID = 1132;
}
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



if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 30;
}

if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}


$AddSqlWhere = $AddSqlWhere . " and A.PayResultMsg<>'' ";

$AddSqlWhere = $AddSqlWhere . " and C.CenterState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and D.BranchState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and E.BranchGroupState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and F.CompanyState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and G.FranchiseState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and ( C.CenterName like '%".$SearchText."%' or C.CenterManagerName like '%".$SearchText."%' or A.ClassOrderPayNumber like '%".$SearchText."%' ) ";
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
	$AddSqlWhere = $AddSqlWhere . " and C.CenterID=$SearchCenterID ";
}


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);


$ViewTable = "
		select 
			A.*
		from ClassOrderPays A
		where 
			A.ClassOrderPayType=1
";


$Sql = "select 
				count(*) TotalRowCount 
		from ($ViewTable) A 
			inner join Centers C on A.CenterID=C.CenterID 
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
			C.CenterID as JoinCenterID,
			C.CenterName as JoinCenterName,
			C.CenterManagerName as JoinCenterManagerName,
			D.BranchID as JoinBranchID,
			D.BranchName as JoinBranchName, 
			E.BranchGroupID as JoinBranchGroupID,
			E.BranchGroupName as JoinBranchGroupName,
			F.CompanyID as JoinCompanyID,
			F.CompanyName as JoinCompanyName,
			G.FranchiseName,
			
			ifnull(T.TaxInvoiceRegType, '') as TaxInvoiceRegType,
			ifnull(T.TaxInvoiceID,'') as TaxInvoiceID,
			ifnull(T.code,'') as TaxInvoiceResultCode,
			ifnull(T.message,'') as TaxInvoiceResultMsg,
			ifnull(T.ntsConfirmNum,'') as TaxInvoiceResultNumber
		from ($ViewTable) A
			inner join Centers C on A.CenterID=C.CenterID 
			inner join Branches D on C.BranchID=D.BranchID 
			inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
			inner join Companies F on E.CompanyID=F.CompanyID 
			inner join Franchises G on F.FranchiseID=G.FranchiseID 

			left outer join TaxInvoices T on A.ClassOrderPayID=T.TaxInvoicePayID and T.TaxInvoiceType=1 and T.TaxInvoiceState=1 
		where ".$AddSqlWhere." 
		order by A.ClassOrderPayID desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?if ( $_LINK_ADMIN_LEVEL_ID_>=9  && $_LINK_ADMIN_LEVEL_ID_<=13 ){?>대리점 B2B 결제<?}else{?>B2B 결제관리<?}?></h3>

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
						<label for="SearchText"><?if ( $_LINK_ADMIN_LEVEL_ID_<=4 ){?>대리점명 또는 원장명 또는 주문번호<?}else{?>주문번호<?}?></label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>

					<!--
					<div class="uk-width-medium-1-10">
						<label for="product_search_price">Price</label>
						<input type="text" class="md-input" id="product_search_price">
					</div>
					-->

					<!--
					<div class="uk-width-medium-1-10" style="display:none;">
						<div class="uk-margin-small-top">
							<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>>전체</option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>>정상</option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>>비정성</option>
							</select>
						</div>
					</div>
					-->

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
						
						<?if ( $_LINK_ADMIN_LEVEL_ID_<=4 ){?>
							※ 아래 목록에서 취소시 실제 카드 전표는 취소되지 않습니다.
							<br/>
							※ 실제 금액이 인출되지 않게 하려면 <a target="_blank" href="https://selfpay.kr/webpay/index.html"><?=$셀프페이[$LangID]?></a><?=$정식으로_취소를_해야_합니다[$LangID]?>
							<br>
							※ 반대로 셀프페이에서 취소한건은 이곳에서도 취소해 주셔야 수강료 정산에 반영됩니다.
							<br>
							※ [세금계산서 ] <span style="color:#52A3EB">발행하기</span> : 팝빌 시스템을 통해 수동 발행하기
							<br>
							※ [세금계산서 ] <span style="color:#135FA4">재발행하기</span> : 팝빌 시스템을 통해 자동 또는 수동 발행에 실패한 건을 재발행 하기
							<br>
							※ [세금계산서 ] <span style="color:#008080">발행완료</span> : 팝빌 시스템을 통해 자동 또는 수동 발행 완료
							<br>
							※ [세금계산서 ] <span style="color:#A60000">외부발행처리</span> : 팝빌이 아닌 다른 시스템으로 발행 완료한 경우 외부발행건으로 처리하기
							<br>
							※ [세금계산서 ] <span style="color:#883417">외부발행처리됨</span> : 외부발행으로 처리된 건
						<?}?>

							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap><?=$상태[$LangID]?></th>
										<th nowrap><?=$결제번호[$LangID]?></th>
										<th nowrap><?=$결제명[$LangID]?></th>
										<th nowrap><?=$결제일[$LangID]?></th>
										<th nowrap><?=$대리점[$LangID]?></th>
										<th nowrap><?=$결제수단[$LangID]?></th>
										<?if ( $_LINK_ADMIN_LEVEL_ID_<=4 ){?>
											<th nowrap><?=$세금계산서[$LangID]?></th>
										<?}?>
										<th nowrap><?=$판매금액[$LangID]?></th>
										<th nowrap><?=$할인금액[$LangID]?></th>
										<th nowrap><?=$결제금액[$LangID]?></th>
										<?if ( $_LINK_ADMIN_LEVEL_ID_<=4 ){?>
											<th nowrap><?=$수수료율[$LangID]?></th>
											<th nowrap><?=$수수료[$LangID]?></th>
										<?}?>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$res_msg = $Row["res_msg"];
										$tno = $Row["tno"];
										$PayResultMsg = $Row["PayResultMsg"];
										$PayGoods = $Row["PayGoods"];
										$ClassOrderPayID = $Row["ClassOrderPayID"];

										$ClassOrderPayNumber = $Row["ClassOrderPayNumber"];
										$ClassOrderPaySellingPrice = $Row["ClassOrderPaySellingPrice"];
										$ClassOrderPayDiscountPrice = $Row["ClassOrderPayDiscountPrice"];
										$ClassOrderPayFreeTrialDiscountPrice = $Row["ClassOrderPayFreeTrialDiscountPrice"];
										$ClassOrderPayPaymentPrice = $Row["ClassOrderPayPaymentPrice"];
										$ClassOrderPayUseSavedMoneyPrice = $Row["ClassOrderPayUseSavedMoneyPrice"];
										$ClassOrderPayUseCashPrice = $Row["ClassOrderPayUseCashPrice"];
										$ClassOrderPayUseCashPaymentType = $Row["ClassOrderPayUseCashPaymentType"];
										
										$ClassOrderPayPgFeeRatio = $Row["ClassOrderPayPgFeeRatio"];
										$ClassOrderPayPgFeePrice = $Row["ClassOrderPayPgFeePrice"];
										$ClassOrderPayProgress = $Row["ClassOrderPayProgress"];

										$ClassOrderPayDateTime = $Row["ClassOrderPayDateTime"];
										$ClassOrderPayPaymentDateTime = $Row["ClassOrderPayPaymentDateTime"];
										$ClassOrderPayCencelRequestDateTime = $Row["ClassOrderPayCencelRequestDateTime"];
										$ClassOrderPayCencelDateTime = $Row["ClassOrderPayCencelDateTime"];
										$ClassOrderPayRefundRequestDateTime = $Row["ClassOrderPayRefundRequestDateTime"];
										$ClassOrderPayRefundDateTime = $Row["ClassOrderPayRefundDateTime"];
										$ClassOrderPayRegDateTime = $Row["ClassOrderPayRegDateTime"];
										$ClassOrderPayModiDateTime = $Row["ClassOrderPayModiDateTime"];

										
										$CenterID = $Row["JoinCenterID"];
										$CenterName = $Row["JoinCenterName"];
										$CenterManagerName = $Row["JoinCenterManagerName"];
										$BranchID = $Row["JoinBranchID"];
										$BranchName = $Row["JoinBranchName"];
										$BranchGroupID = $Row["JoinBranchGroupID"];
										$BranchGroupName = $Row["JoinBranchGroupName"];
										$CompanyID = $Row["JoinCompanyID"];
										$CompanyName = $Row["JoinCompanyName"];
										$FranchiseName = $Row["FranchiseName"];

										$TaxInvoiceRegType = $Row["TaxInvoiceRegType"];
										$TaxInvoiceID = $Row["TaxInvoiceID"];
										$TaxInvoiceResultCode = $Row["TaxInvoiceResultCode"];
										$TaxInvoiceResultMsg = $Row["TaxInvoiceResultMsg"];
										$TaxInvoiceResultNumber = $Row["TaxInvoiceResultNumber"];

										if ($TaxInvoiceResultCode==""){
											$StrTaxInvoiceResultCode = "-";
										}else{
											$StrTaxInvoiceResultCode = "<span title=\"".$TaxInvoiceResultMsg."\">".$TaxInvoiceResultCode."</span>";
										}
										
										if ($ClassOrderPayUseCashPaymentType!=2){//카드결제
											$StrTaxInvoiceBtn = "-";
										}else{


											if ($TaxInvoiceID==""){
												$StrTaxInvoiceBtn = "<a href=\"javascript:OpenTaxInvoiceForm(".$ClassOrderPayID.", '')\" style=\"color:#52A3EB\">발행하기</a>";
												$StrTaxInvoiceBtn .= "<br>";
												$StrTaxInvoiceBtn .= "<a href=\"javascript:TaxInvoiceForceEnd(".$ClassOrderPayID.", '')\" style=\"color:#A60000\">외부발행처리</a>";
											}else{

												if ($TaxInvoiceRegType==2){
													$StrTaxInvoiceBtn = "<span style=\"color:#883417\">외부발행처리됨</span>";
												}else {
													if ($TaxInvoiceResultNumber!=""){
														$StrTaxInvoiceBtn = "<span style=\"color:#008080\">발행완료</span>";
													}else{
														$StrTaxInvoiceBtn = "<a href=\"javascript:OpenTaxInvoiceForm(".$ClassOrderPayID.", '".$TaxInvoiceID."')\" style=\"color:#135FA4\">재발행하기</a>";
														$StrTaxInvoiceBtn .= "<br>";
														$StrTaxInvoiceBtn .= "<a href=\"javascript:TaxInvoiceForceEnd(".$ClassOrderPayID.", '".$TaxInvoiceID."')\" style=\"color:#A60000\">외부발행처리</a>";
														$StrTaxInvoiceBtn .= "<br>";
														$StrTaxInvoiceBtn .= "(".$StrTaxInvoiceResultCode.")";
													}
												}
											}
										}


										$StrClassOrderPayUseCashPaymentType="-";
										if ($ClassOrderPayUseCashPaymentType==1){
											$StrClassOrderPayUseCashPaymentType="카드";
										}else if ($ClassOrderPayUseCashPaymentType==2){
											$StrClassOrderPayUseCashPaymentType="실시간이체";
										}else if ($ClassOrderPayUseCashPaymentType==3){
											$StrClassOrderPayUseCashPaymentType="가상계좌";
										}else if ($ClassOrderPayUseCashPaymentType==4){
											$StrClassOrderPayUseCashPaymentType="계좌입금";
										}else if ($ClassOrderPayUseCashPaymentType==5){
											$StrClassOrderPayUseCashPaymentType="오프라인";
										}else if ($ClassOrderPayUseCashPaymentType==9){
											$StrClassOrderPayUseCashPaymentType="기타";
										}
							
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$PayResultMsg?></td>
										<td class="uk-text-nowrap">
											<span style="font-size:11px;"><?=$ClassOrderPayNumber?></span>
											<?if ( $_LINK_ADMIN_LEVEL_ID_<=4 ){?>
												<?if ($ClassOrderPayProgress==21){?>
													<div style='display:inline-block;width:80px;text-align:center;padding:5px;border-radius:3px;background-color:#8A0000;color:#ffffff;font-size:10px;cursor:pointer;' onclick="javascript:ChangeState(<?=$ClassOrderPayID?>,33);">최소완료로 변경</div>
												<?} else if ($ClassOrderPayProgress==33){?>
													<div style='display:inline-block;width:80px;text-align:center;padding:5px;border-radius:3px;background-color:#1A5495;color:#ffffff;font-size:10px;cursor:pointer;' onclick="javascript:ChangeState(<?=$ClassOrderPayID?>, 21);"><?=$결제완료로_변경[$LangID]?></div>
												<?}?>
											<?}?>
										</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$PayGoods?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderPayPaymentDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterName?></td>
										
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrClassOrderPayUseCashPaymentType?></td>

										<?if ( $_LINK_ADMIN_LEVEL_ID_<=4 ){?>
											<td class="uk-text-nowrap uk-table-td-center" style="line-height:1.5;"><?=$StrTaxInvoiceBtn?></td>
										<?}?>

										<td class="uk-text-nowrap uk-table-td-right"><?=number_format($ClassOrderPaySellingPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-right"><?=number_format($ClassOrderPayDiscountPrice+$ClassOrderPayFreeTrialDiscountPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-right"><?=number_format($ClassOrderPayUseCashPrice,0)?></td>
										<?if ( $_LINK_ADMIN_LEVEL_ID_<=4 ){?>
											<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderPayPgFeeRatio?></td>
											<td class="uk-text-nowrap uk-table-td-center">
												<?if ($ClassOrderPayPgFeeRatio>0){?>
													<?=number_format(($ClassOrderPayUseCashPrice*($ClassOrderPayPgFeeRatio/100)),0)?>
												<?}else{?>
													<?=number_format($ClassOrderPayPgFeePrice,0)?>
												<?}?>
											</td>
										<?}?>
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

function OpenTaxInvoiceForm(TaxInvoicePayID, TaxInvoiceID){

	TaxInvoiceType = 1;

	openurl = "tax_reg_form.php?TaxInvoiceType="+TaxInvoiceType+"&TaxInvoicePayID="+TaxInvoicePayID+"&TaxInvoiceID="+TaxInvoiceID;

	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1100"
		,maxHeight: "800"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	});
}
function TaxInvoiceForceEnd(TaxInvoicePayID, TaxInvoiceID){
	UIkit.modal.confirm(
		'외부발행처리로 변경 하시겠습니까? 변경 후에는 본 시스템을 통한 발행이 불가능 합니다.', 
		function(){ 

				url = "ajax_set_invoice_force_end.php";
				//location.href = url + "?TaxInvoiceType="+TaxInvoiceType+"&TaxInvoicePayID="+TaxInvoicePayID+"&TaxInvoiceID="+TaxInvoiceID;

				TaxInvoiceType = 1;
				
				$.ajax(url, {
					data: {
						TaxInvoiceType, TaxInvoiceType,
						TaxInvoicePayID: TaxInvoicePayID,
						TaxInvoiceID: TaxInvoiceID
					},
					success: function () {
						location.reload();
					},
					error: function () {
						alert('Error while contacting server, please try again');
					}

				});

		}
	);
}

</script>



<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "b2b_payment_list.php";
	document.SearchForm.submit();
}

function ChangeState(ClassOrderPayID, ClassOrderPayProgress) {

	if (confirm("<?=$상태를_변경하겠습니까[$LangID]?>?")){
	
			url = "ajax_change_class_order_pay_progress.php";
			//location.href = url + "?ClassOrderPayID="+ClassOrderPayID+"&ClassOrderPayProgress="+ClassOrderPayProgress;
			$.ajax(url, {
				data: {
					ClassOrderPayID: ClassOrderPayID,
					ClassOrderPayProgress: ClassOrderPayProgress 
				},
				success: function (data) {

					location.reload();
				},
				error: function () {

				}
			});

	}

}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>