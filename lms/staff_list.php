<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

if ($_LINK_ADMIN_LEVEL_ID_==4){
	header("Location: ./staff_form.php?StaffID=".$_LINK_ADMIN_STAFF_ID_); 
	exit;
}


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
$MainMenuID = 11;
$SubMenuID = 1101;
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
	$AddSqlWhere = $AddSqlWhere . " and A.StaffState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.StaffState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and B.FranchiseState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.StaffName like '%".$SearchText."%' or A.StaffNickName like '%".$SearchText."%') ";
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
		from Staffs A 
			inner join Franchises B on A.FranchiseID=B.FranchiseID 
			inner join Members C on A.StaffID=C.StaffID and (C.MemberLevelID=4 OR C.MemberLevelID=15)
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
			A.* ,
			AES_DECRYPT(UNHEX(A.StaffPhone1),:EncryptionKey) as DecStaffPhone1,
			AES_DECRYPT(UNHEX(A.StaffPhone2),:EncryptionKey) as DecStaffPhone2,
			B.FranchiseName,
			C.MemberLoginID,
			ifnull(D.MemberID,0) as Hr_MemberID,
			ifnull(D.Hr_OrganLevel,0) as Hr_OrganLevel

		from Staffs A 
			inner join Franchises B on A.FranchiseID=B.FranchiseID 
			inner join Members C on A.StaffID=C.StaffID and (C.MemberLevelID=4 OR C.MemberLevelID=15)
			left outer join Hr_OrganLevelTaskMembers D on C.MemberID=D.MemberID 

		where ".$AddSqlWhere." 
		order by A.StaffOrder desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$직원관리[$LangID]?></h3>

		<form name="SearchForm" method="post" ENCTYPE="multipart/form-data">
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


					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$교사_및_직원명[$LangID]?></label>
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
					<div class="uk-width-medium-4-10">
						<div class="uk-margin-small-top">
                           <input type="file" id="StaffData" name="StaffData" style="width:100%;">   
						</div>
						<a href="javascript:StaffTable_Upload();" class="md-btn md-btn-primary uk-margin-small-top"><?=$일괄_엑셀자료_올리기[$LangID]?></a>
						<a href="StaffTable.xlsx" class="md-btn md-btn-primary uk-margin-small-top" style="background:#408080;"><?=$직원추가_엑셀자료구조_다운로드[$LangID]?></a>
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
										<th nowrap><?=$부서[$LangID]?></th>
										<th nowrap><?=$교사_및_직원명[$LangID]?></th>
										<th nowrap><?=$아이디[$LangID]?></th>
										<th nowrap><?=$닉네임[$LangID]?></th>
										<th nowrap><?=$전화번호[$LangID]?></th>
										<th nowrap><?=$휴대폰[$LangID]?></th>
										<th nowrap><?=$프랜차이즈[$LangID]?></th>
										<th nowrap><?=$상태[$LangID]?></th>
										<?if ($_LINK_ADMIN_LEVEL_ID_==0){?>
										<th nowrap><?=$인사평가셋팅[$LangID]?></th>
										<th nowrap><?=$권한[$LangID]?></th>
										<?}?>
									</tr>
								</thead>
								<tbody>
									
									<?php

									include('./inc_departments.php');
									$departments = getDepartments($LangID);

									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$StaffID = $Row["StaffID"];
										$StaffManageMent = $Row["StaffManageMent"];
										$StaffName = $Row["StaffName"];
										$StaffNickName = $Row["StaffNickName"];
										$StaffPhone1 = $Row["DecStaffPhone1"];
										$StaffPhone2 = $Row["DecStaffPhone2"];
										$StaffState = $Row["StaffState"];
										$FranchiseName = $Row["FranchiseName"];
										$MemberLoginID = $Row["MemberLoginID"];

										$Hr_MemberID = $Row["Hr_MemberID"];
										$Hr_OrganLevel = $Row["Hr_OrganLevel"];
										
										if ($StaffState==1){
											$StrStaffState = "<span class=\"ListState_1\">".$활동중[$LangID]."</span>";
										}else if ($StaffState==2){
											$StrStaffState = "<span class=\"ListState_2\">".$미활동[$LangID]."</span>";
										}

										if ($Hr_MemberID!=0){
											$Str_Hr_MemberID = "<span class=\"ListState_1\">".$완료[$LangID]."</span>";
										}else{
											$Str_Hr_MemberID = "<span class=\"ListState_2\">".$미완료[$LangID]."</span>";
										}

										
										$StrStaffManageMent = $departments[$StaffManageMent];
										

										if ($Hr_OrganLevel==1) {
											$Str_Hr_OrganLevel = "LEVEL 1(".$경영진[$LangID].")";
										} else if ($Hr_OrganLevel==2) {
											$Str_Hr_OrganLevel = "LEVEL 2(".$부문[$LangID].")";
										} else if ($Hr_OrganLevel==3) {
											$Str_Hr_OrganLevel = "LEVEL 3(".$부서[$LangID].")";
										} else if ($Hr_OrganLevel==4) {
											$Str_Hr_OrganLevel = "LEVEL 4(".$파트[$LangID].")";
										}else{
											$Str_Hr_OrganLevel = "-";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrStaffManageMent?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="staff_form.php?ListParam=<?=$ListParam?>&StaffID=<?=$StaffID?>"><?=$StaffName?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="staff_form.php?ListParam=<?=$ListParam?>&StaffID=<?=$StaffID?>"><?=$MemberLoginID?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StaffNickName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StaffPhone1?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StaffPhone2?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$FranchiseName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrStaffState?></td>
										<?if ($_LINK_ADMIN_LEVEL_ID_==0){?>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_MemberID?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_OrganLevel?></td>
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

						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="staff_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
// 엑셀자료 등록
function StaffTable_Upload() {

	obj = document.SearchForm.StaffData;
	if (obj.value==""){
		UIkit.modal.alert("직원 추가 자료엑셀파일을 선택해 주세요");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'직원 추가 업로드 하시겠습니까?', 
		function(){ 
			document.SearchForm.action = "staff_excell_upload.php";
			document.SearchForm.submit();
		}
	);

}

function SearchSubmit(){
	document.SearchForm.action = "staff_list.php";
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