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
$MainMenuID = 25;
$SubMenuID = 2501;
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
$SearchBookGroupID = isset($_REQUEST["SearchBookGroupID"]) ? $_REQUEST["SearchBookGroupID"] : "";

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
	$SearchState = "100";
}	
		
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.BookState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.BookState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and B.BookGroupState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.BookName like '%".$SearchText."%' ";
}

if ($SearchBookGroupID!=""){
	$ListParam = $ListParam . "&SearchBookGroupID=" . $SearchBookGroupID;
	$AddSqlWhere = $AddSqlWhere . " and A.BookGroupID=$SearchBookGroupID ";
}

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
			count(*) TotalRowCount 
		from Books A 
			inner join BookGroups B on A.BookGroupID=B.BookGroupID 
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
			B.BookGroupName,
			ifnull((select count(*) from BookVideos where BookID=A.BookID and BookVideoState=1),0) as BookVideoCount,
			ifnull((select count(*) from BookQuizs where BookID=A.BookID and BookQuizState=1),0) as BookQuizCount,
			ifnull((select count(*) from BookQuizDetails AA inner join BookQuizs BB on AA.BookQuizID=BB.BookQuizID where BB.BookID=A.BookID and AA.BookQuizDetailState=1 and BB.BookQuizState=1),0) as BookQuizDetailCount,
			ifnull((select count(*) from BookScans where BookID=A.BookID and BookScanState=1),0) as BookScanCount
		from Books A 
			inner join BookGroups B on A.BookGroupID=B.BookGroupID 
		where ".$AddSqlWhere." 
		order by A.BookOrder desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$교재관리[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					
					<div class="uk-width-medium-4-10"></div>

					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						<select id="SearchBookGroupID" name="SearchBookGroupID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$교재그룹선택[$LangID]?>" style="width:100%;"/>
							<option value=""></option>
							<?
							$Sql2 = "select 
											A.* 
									from BookGroups A 
									where A.BookGroupState<>0 
									order by A.BookGroupState asc, A.BookGroupName asc";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldSelectBookGroupState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectBookGroupID = $Row2["BookGroupID"];
								$SelectBookGroupName = $Row2["BookGroupName"];
								$SelectBookGroupState = $Row2["BookGroupState"];

								if ($OldSelectBookGroupState!=$SelectBookGroupState){
									if ($OldSelectBookGroupState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectBookGroupState==1){
										echo "<optgroup label=\"교재그룹(사용중)\">";
									}else if ($SelectBookGroupState==2){
										echo "<optgroup label=\"교재그룹(미사용)\">";
									}
								} 
								$OldSelectBookGroupState = $SelectBookGroupState;
							?>

							<option value="<?=$SelectBookGroupID?>" <?if ($SearchBookGroupID==$SelectBookGroupID){?>selected<?}?>><?=$SelectBookGroupName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>

					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$교재명[$LangID]?></label>
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
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$사용중[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$미사용[$LangID]?></option>
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
										<th style="width:10%;" nowrap>No</th>
										<th nowrap><?=$교재명[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$동영상수[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$퀴즈수_문제수[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$스캔자료수[$LangID]?></th>
										<th style="width:20%;" nowrap><?=$교재그룹명[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$상태[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$강사에게노출[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$목록에노출[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$BookID = $Row["BookID"];
										$BookName = $Row["BookName"];
										$BookState = $Row["BookState"];
										$BookTeacherView = $Row["BookTeacherView"];
										$BookViewList = $Row["BookViewList"];
										$BookGroupName = $Row["BookGroupName"];

										$BookVideoCount = $Row["BookVideoCount"];
										$BookQuizCount = $Row["BookQuizCount"];
										$BookQuizDetailCount = $Row["BookQuizDetailCount"];
										$BookScanCount = $Row["BookScanCount"];

										if ($BookState==1){
											$StrBookState = "<span class=\"ListState_1\">사용중</span>";
										}else if ($BookState==2){
											$StrBookState = "<span class=\"ListState_2\">미사용</span>";
										}

										if ($BookTeacherView==1){
											$StrBookTeacherView = "<span class=\"ListState_1\">노출</span>";
										}else if ($BookTeacherView==0){
											$StrBookTeacherView = "<span class=\"ListState_2\">-</span>";
										}

										if ($BookViewList==1){
											$StrBookViewList = "<span class=\"ListState_1\">노출</span>";
										}else if ($BookViewList==0){
											$StrBookViewList = "<span class=\"ListState_2\">-</span>";
										}

									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="book_form.php?ListParam=<?=$ListParam?>&BookID=<?=$BookID?>"><?=$BookName?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BookVideoCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BookQuizCount?> (<?=$BookQuizDetailCount?>)</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BookScanCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BookGroupName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrBookState?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrBookTeacherView?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrBookViewList?></td>
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
							<a type="button" href="book_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
	document.SearchForm.action = "book_list.php";
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