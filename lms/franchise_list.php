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
$MainMenuID = 12;
$SubMenuID = 1207;
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
	$AddSqlWhere = $AddSqlWhere . " and A.FranchiseState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.FranchiseState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.FranchiseName like '%".$SearchText."%' or A.FranchiseManagerName like '%".$SearchText."%') ";
}

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select count(*) TotalRowCount from Franchises A where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
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
			where AA.MemberLevelID=19 and AA.MemberState<>0 and FF.FranchiseID=A.FranchiseID 
			) as StudentCount 
			";

$TeacherCountSql = ",(
				select 
					count(*) 
				from Teachers AA 
					inner join TeacherGroups BB on AA.TeacherGroupID=BB.TeacherGroupID 
					inner join EduCenters CC on BB.EduCenterID=CC.EduCenterID 
					inner join Franchises DD on CC.FranchiseID=DD.FranchiseID 
				where AA.TeacherState<>0 and DD.FranchiseID=A.FranchiseID 
				) as TeacherCount 
				";


$Sql = "
		select 
			A.* ,
			AES_DECRYPT(UNHEX(A.FranchisePhone1),:EncryptionKey) as DecFranchisePhone1,
			AES_DECRYPT(UNHEX(A.FranchisePhone2),:EncryptionKey) as DecFranchisePhone2,
			ifnull((select count(*) from Companies where FranchiseID=A.FranchiseID and CompanyState<>0),0) as CompanyCount,
			ifnull((select count(*) from BranchGroups AA inner join Companies BB on AA.CompanyID=BB.CompanyID where BB.FranchiseID=A.FranchiseID and AA.BranchGroupState<>0 and BB.CompanyState<>0),0) as BranchGroupCount,
			ifnull((select count(*) from Branches AA inner join BranchGroups BB on AA.BranchGroupID=BB.BranchGroupID inner join Companies CC on BB.CompanyID=CC.CompanyID where CC.FranchiseID=A.FranchiseID and AA.BranchState<>0 and BB.BranchGroupID<>0 and CC.CompanyState<>0),0) as BranchCount,
			ifnull((select count(*) from Centers AA inner join Branches BB on AA.BranchID=BB.BranchID inner join BranchGroups CC on BB.BranchGroupID=CC.BranchGroupID inner join Companies DD on CC.CompanyID=DD.CompanyID where DD.FranchiseID=A.FranchiseID and AA.CenterState<>0 and BB.BranchState<>0 and CC.BranchGroupState<>0 and DD.CompanyState<>0),0) as CenterCount,
			ifnull((select count(*) from EduCenters where FranchiseID=A.FranchiseID and EduCenterState<>0),0) as EduCenterCount
			".$StudentCountSql." 
			".$TeacherCountSql."
		from Franchises A 
		where ".$AddSqlWhere." 
		order by A.FranchiseOrder desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$프랜차이즈관리[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					
					<div class="uk-width-medium-6-10"></div>

					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$프랜차이즈명_또는_관리자명[$LangID]?></label>
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
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap><?=$프랜차이즈명[$LangID]?></th>
										
										<th nowrap><?=$관리자[$LangID]?></th>
										<th nowrap><?=$학생수[$LangID]?></th>
										<!--<th nowrap>대리점판매가(10분당)</th>-->
										<th nowrap><?=$전화번호[$LangID]?></th>
										<th nowrap><?=$휴대폰[$LangID]?></th>
										<th nowrap><?=$본사수[$LangID]?></th>
										<th nowrap><?=$대표지사수[$LangID]?></th>
										<th nowrap><?=$지사수[$LangID]?></th>
										<th nowrap><?=$대리점수[$LangID]?></th>
										
										<!--<th nowrap>수강생</th>-->
										<th nowrap><?=$교육센터수[$LangID]?></th>
										<th nowrap><?=$강사수[$LangID]?></th>
										<th nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$FranchiseID = $Row["FranchiseID"];
										$FranchiseName = $Row["FranchiseName"];
										$FranchiseManagerName = $Row["FranchiseManagerName"];
										$FranchisePhone1 = $Row["DecFranchisePhone1"];
										$FranchisePhone2 = $Row["DecFranchisePhone2"];
										$FranchiseSendNumber = $Row["FranchiseSendNumber"];
										$FranchiseReceiveNumber = $Row["FranchiseReceiveNumber"];
										$FranchiseState = $Row["FranchiseState"];
										$CompanyCount = $Row["CompanyCount"];
										$BranchGroupCount = $Row["BranchGroupCount"];
										$BranchCount = $Row["BranchCount"];
										$CenterCount = $Row["CenterCount"];
										$EduCenterCount = $Row["EduCenterCount"];
										$StudentCount = $Row["StudentCount"];
										$TeacherCount = $Row["TeacherCount"];

										if ($FranchiseState==1){
											$StrFranchiseState = "<span class=\"ListState_1\"><?=$운영중[$LangID]?></span>";
										}else if ($FranchiseState==2){
											$StrFranchiseState = "<span class=\"ListState_2\"><?=$미운영[$LangID]?></span>";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="franchise_form.php?ListParam=<?=$ListParam?>&FranchiseID=<?=$FranchiseID?>"><?=$FranchiseName?></a></td>
										
										<td class="uk-text-nowrap uk-table-td-center"><?=$FranchiseManagerName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($StudentCount,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$FranchisePhone1?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$FranchisePhone2?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($CompanyCount,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($BranchGroupCount,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($BranchCount,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($CenterCount,0)?></td>
										
										<!--<td class="uk-text-nowrap uk-table-td-center">-</td>-->
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($EduCenterCount,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TeacherCount,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrFranchiseState?></td>
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

						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="franchise_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
						</div>

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
function SearchSubmit(){
	document.SearchForm.action = "franchise_list.php";
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