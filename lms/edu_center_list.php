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
$MainMenuID = 13;
$SubMenuID = 1303;
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
	$AddSqlWhere = $AddSqlWhere . " and A.EduCenterState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.EduCenterState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and B.FranchiseState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.EduCenterName like '%".$SearchText."%' or A.EduCenterManagerName like '%".$SearchText."%') ";
}

if ($SearchFranchiseID!=""){
	$ListParam = $ListParam . "&SearchFranchiseID=" . $SearchFranchiseID;
	$AddSqlWhere = $AddSqlWhere . " and A.FranchiseID=$SearchFranchiseID ";
}

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from EduCenters A 
			inner join Franchises B on A.FranchiseID=B.FranchiseID 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$TeacherCountSql = ",(
				select 
					count(*) 
				from Teachers AA 
					inner join TeacherGroups BB on AA.TeacherGroupID=BB.TeacherGroupID 
					inner join EduCenters CC on BB.EduCenterID=CC.EduCenterID 
					inner join Franchises DD on CC.FranchiseID=DD.FranchiseID 
				where AA.TeacherState<>0 and CC.EduCenterID=A.EduCenterID 
				) as TeacherCount 
				";


$Sql = "
		select 
			A.* ,
			AES_DECRYPT(UNHEX(A.EduCenterPhone1),:EncryptionKey) as DecEduCenterPhone1,
			AES_DECRYPT(UNHEX(A.EduCenterPhone2),:EncryptionKey) as DecEduCenterPhone2,
			B.FranchiseName,
			ifnull((select count(*) from TeacherGroups where EduCenterID=A.EduCenterID),0) as TeacherGroupCount,
			ifnull((select count(*) from ClassTypes where EduCenterID=A.EduCenterID),0) as ClassTypeCount
			".$TeacherCountSql."
		from EduCenters A 
			inner join Franchises B on A.FranchiseID=B.FranchiseID 
		where ".$AddSqlWhere." 
		order by A.EduCenterOrder desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>

<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$교육센터관리[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					
					<div class="uk-width-medium-4-10"></div>

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

					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$교육센터명_또는_관리자명[$LangID]?></label>
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
										<th nowrap><?=$교육센터명[$LangID]?></th>
										<th nowrap><?=$관리자[$LangID]?></th>
										<th nowrap><?=$전화번호[$LangID]?></th>
										<th nowrap><?=$휴대폰[$LangID]?></th>
										<th nowrap><?=$강사그룹수[$LangID]?></th>
										<th nowrap><?=$강사수[$LangID]?></th>
										<th nowrap><?=$프랜차이즈[$LangID]?></th>
										<th nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$EduCenterID = $Row["EduCenterID"];
										$EduCenterName = $Row["EduCenterName"];
										$EduCenterManagerName = $Row["EduCenterManagerName"];
										$EduCenterPhone1 = $Row["DecEduCenterPhone1"];
										$EduCenterPhone2 = $Row["DecEduCenterPhone2"];
										$EduCenterState = $Row["EduCenterState"];
										$TeacherGroupCount = $Row["TeacherGroupCount"];
										$ClassTypeCount = $Row["ClassTypeCount"];
										$TeacherCount = $Row["TeacherCount"];
										$FranchiseName = $Row["FranchiseName"];

										if ($EduCenterState==1){
											$StrEduCenterState = "<span class=\"ListState_1\">운영중</span>";
										}else if ($EduCenterState==2){
											$StrEduCenterState = "<span class=\"ListState_2\">미운영</span>";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="edu_center_form.php?ListParam=<?=$ListParam?>&EduCenterID=<?=$EduCenterID?>"><?=$EduCenterName?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$EduCenterManagerName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$EduCenterPhone1?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$EduCenterPhone2?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TeacherGroupCount,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TeacherCount,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$FranchiseName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrEduCenterState?></td>
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
							<a type="button" href="edu_center_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
	document.SearchForm.action = "edu_center_list.php";
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