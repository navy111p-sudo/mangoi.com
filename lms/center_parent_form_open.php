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
//include_once('./includes/common_meta_tag.php');
//include_once('./inc_header.php');
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
$MainMenuID = 26;
$SubMenuID = 2601;
//include_once('./inc_top.php');
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
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$Type = isset($_REQUEST["Type"]) ? $_REQUEST["Type"] : "";


if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 10;
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

$ListParam = $ListParam . "&CenterID=" . $CenterID;


if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.MemberName like '%".$SearchText."%'";
}


$PaginationParam = $ListParam;

if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}



$ListParam = str_replace("&", "^^", $ListParam);


$Sql = "select 
				count(*) TotalRowCount 
		from Members A 

		where ".$AddSqlWhere." and A.CenterID=:CenterID and A.MemberLevelID=19 order by A.MemberID asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':CenterID', $CenterID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$Sql = "
		select 
			A.MemberName,
			A.MemberID, 
			A.MemberLoginID 
		from Members A 

		where ".$AddSqlWhere." and A.CenterID=:CenterID and A.MemberLevelID=19 order by A.MemberID asc limit ".$StartRowNum.", ".$PageListNum."";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':CenterID', $CenterID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$자녀관리[$LangID]?></h3>
		<form name="SearchForm" method="get">
		<input type="hidden" name="CenterID" value="<?=$CenterID?>" />
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					<div class="uk-width-medium-6-10">
						<label for="SearchText"><?=$학생명[$LangID]?></label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>

					<div class="uk-width-medium-2-10">
						<div class="uk-margin-small-top">
							<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
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
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th style="width:5%;" nowrap>No</th>
										<th style="width:10%;" nowrap><?=$학생번호[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$아이디[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$학생명[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$MemberID = $Row["MemberID"];
										$MemberName = $Row["MemberName"];
										$MemberLoginID = $Row["MemberLoginID"];
										
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberID?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberLoginID?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenMemberForm(<?=$MemberID?>, '<?=$MemberName?>', '<?=$Type?>')"><?=$MemberName?></td>
										<!--
										<td class="uk-text-nowrap uk-table-td-center">
										<?php
											if($SearchText=="" && $SearchState!="100") {
										?>
											<div class="uk-text-nowrap uk-table-td-center">
												<a href="javascript:MemberListOrder(<?=$MemberID?>, 1)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block" >arrow_drop_up</i></a>
												<a href="javascript:MemberListOrder(<?=$MemberID?>, 0)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block">arrow_drop_down</i></a>
											</div>
										<?php
											} else {
										?>
											-
										<?php
											}
										?>
											
										</td>
										-->
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

function OpenMemberForm(MemberID, MemberName, Type) {

	var ID = "MemberChildID"+Type;
	var Name = "MemberChildName"+Type;

	parent.document.getElementById(ID).value = MemberID;
	parent.document.getElementById(Name).value = MemberName;
	parent.$.fn.colorbox.close();
	//parent.location.href = "center_form.php?<?=$ListParam?>&CenterID=<?=$CenterID?>&PageTabID=2";
}


</script>




<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "center_parent_form_open.php";
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