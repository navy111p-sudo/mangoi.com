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
$MainMenuID = 17;
$SubMenuID = 1721;
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

/*
if ($SearchState==""){
	$SearchState = "1";
}	
		
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.PushMessageState=$SearchState ";
}
*/


if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherMessageText like '%".$SearchText."%' ";
}
$AddSqlWhere = $AddSqlWhere . " and A.MemberID=".$_LINK_ADMIN_ID_."";
$AddSqlWhere = $AddSqlWhere . " and A.TeacherMessageType=1 ";
$AddSqlWhere = $AddSqlWhere . " and (A.TeacherMessageID in (select TeacherMessageID from TeacherMessageReads where MemberID=".$_LINK_ADMIN_ID_.") or datediff(A.TeacherMessageRegDateTime, now())=0) ";// 읽었거나 오늘 메시지

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from TeacherMessages A 
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
			ifnull((select TeacherMessageReadID from TeacherMessageReads where TeacherMessageID=A.TeacherMessageID and MemberID=".$_LINK_ADMIN_ID_."),0) as TeacherMessageReadID,
			ifnull((select TeacherMessageReadDateTime from TeacherMessageReads where TeacherMessageID=A.TeacherMessageID and MemberID=".$_LINK_ADMIN_ID_."),'<?=$확인전[$LangID]?>') as TeacherMessageReadDateTime
		from TeacherMessages A
		where ".$AddSqlWhere." 
		order by A.TeacherMessageRegDateTime desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);

$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


//echo $Sql;
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$알림메시지[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">

					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$내용검색[$LangID]?></label>
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
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap style="width:8%;">No</th>
										<th nowrap style="width:15%;"><?=$등록일[$LangID]?></th>
										<th nowrap><?=$메시지[$LangID]?></th>
										<th nowrap style="width:15%;"><?=$확인시간[$LangID]?></th>
										<th nowrap style="width:15%;"><?=$메시지확인[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$TeacherMessageID = $Row["TeacherMessageID"];

										$TeacherMessageType = $Row["TeacherMessageType"];
										$TeacherMessageText = $Row["TeacherMessageText"];
										$TeacherMessageRegDateTime = $Row["TeacherMessageRegDateTime"];

										$TeacherMessageReadID = $Row["TeacherMessageReadID"];
										$TeacherMessageReadDateTime = $Row["TeacherMessageReadDateTime"];

							
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherMessageRegDateTime?></td>
										<td class="uk-text-nowrap"><b><?=$TeacherMessageText?></b></td>
										<td class="uk-text-nowrap uk-table-td-center" id="DivTeacherMessageReadDateTime_<?=$TeacherMessageID?>"><?=$TeacherMessageReadDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center" id="DivTeacherMessageReadBtn_<?=$TeacherMessageID?>">
											<?
											if ($TeacherMessageReadID==0){
											?>
											<a class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" href="javascript:SetTeacherMessageRead(<?=$TeacherMessageID?>, <?=$_LINK_ADMIN_ID_?>);">메시지확인</a>
											<?
											}else{
											?>
											-
											<?
											}
											?>
										</td>
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

<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->

<script>
function SearchSubmit(){
	document.SearchForm.action = "teacher_message_list.php";
	document.SearchForm.submit();
}
</script>

<?php
include_once('./inc_menu_right.php');
$TeacherMessageAlert = 0;
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>