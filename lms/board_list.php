<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./includes/board_config.php');
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
if ($BoardCode=="notice"){
	$MainMenuID = 24;
	$SubMenuID = 2403;
}else if ($BoardCode=="exchange"){
	$MainMenuID = 24;
	$SubMenuID = 2404;
}else if ($BoardCode=="event"){
	$MainMenuID = 24;
	$SubMenuID = 2405;
}else if ($BoardCode=="qna"){
	$MainMenuID = 24;
	$SubMenuID = 2406;
}else if ($BoardCode=="faq"){
	$MainMenuID = 24;
	$SubMenuID = 2407;
}else if ($BoardCode=="reference"){
	$MainMenuID = 28;
	$SubMenuID = 2808;
}else if ($BoardCode=="branch"){
	$MainMenuID = 28;
	$SubMenuID = 28081;
}else if ($BoardCode=="center"){
	$MainMenuID = 28;
	$SubMenuID = 28082;
}else if ($BoardCode=="etc"){
	$MainMenuID = 28;
	$SubMenuID = 28083;
}else if ($BoardCode=="hrfile"){
	$MainMenuID = 88;
	$SubMenuID = 8841;
}


include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$BoardCategoryID = isset($_REQUEST["BoardCategoryID"]) ? $_REQUEST["BoardCategoryID"] : "";
$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchItem = isset($_REQUEST["SearchItem"]) ? $_REQUEST["SearchItem"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";


$Sql = "select BoardID from Boards where BoardCode=:BoardCode ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardCode', $BoardCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BoardID = $Row["BoardID"];

$ListParam = $ListParam . "&BoardCode=" . $BoardCode;
$AddSqlWhere = $AddSqlWhere . " and (BoardID=$BoardID) ";


if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 10;
}


if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}


if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchItem=" . $SearchItem;
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	switch ($SearchItem) {
	case "1":
		$AddSqlWhere = $AddSqlWhere . " and (BoardContentSubject like '%".$SearchText."%' or BoardContent like '%".$SearchText."%') ";
		break;
	case "2":
		$AddSqlWhere = $AddSqlWhere . " and (BoardContentSubject like '%".$SearchText."%') ";
		break;	
	case "3":
		$AddSqlWhere = $AddSqlWhere . " and (BoardContent like '%".$SearchText."%') ";
		break;
	case "4":
		$AddSqlWhere = $AddSqlWhere . " and (BoardContentWriterName like '%".$SearchText."%') ";
		break;
	}

	
}




$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);


//매뉴얼일때 기본값
if ($BoardCode=="manual" && $BoardCategoryID==""){
	$BoardCategoryID = "1";
}
//매뉴얼일때 기본값



if ($BoardCategoryID!=""){
	$AddSqlWhere = $AddSqlWhere . " and BoardCategoryID=$BoardCategoryID ";
}

$AddSqlWhere = $AddSqlWhere . " and BoardContentState=1 ";


$Sql = "select count(*) as TotalRowCount from BoardContents where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];


$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );



$Sql = "select A.*, DATE_FORMAT(EventStartDate,'%Y-%m-%d') as EventStartDate2 , 
	DATE_FORMAT(EventEndDate,'%Y-%m-%d') as EventEndDate2, 
	(select count(*) from BoardComments where BoardContentID=A.BoardContentID) as BoardCommentCount, 
	timestampdiff(day, BoardContentRegDateTime, now()) as RecentDay,
	A.BoardContentMemberID,
	B.MemberLoginID,
	B.MemberLevelID

	from BoardContents A 
	left outer join Members B on A.BoardContentMemberID=B.MemberID
	where ".$AddSqlWhere." 
	order by BoardContentNotice desc, BoardContentReplyID desc, BoardContentReplyOrder asc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$BoardTitle?></h3>

		<form name="SearchForm" method="get">
		<input type="hidden" name="BoardCode" value="<?=$BoardCode?>">
		<input type="hidden" name="PageListNum" value="<?=$PageListNum?>">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					
					<div class="uk-width-medium-4-10"></div>

					<?if ($BoardEnableCategory==1){?>
					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						
						<select id="BoardCategoryID" name="BoardCategoryID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="카테고리" style="width:100%;"/>
							<option value=""></option>
							<?
							$Sql2 = "select * from BoardCategories  where BoardID=:BoardID order by BoardCategoryOrder asc";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->bindParam(':BoardID', $BoardID);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							while($Row2 = $Stmt2->fetch()){
							?>
							<option value="<?=$Row2["BoardCategoryID"]?>" <?php if ($BoardCategoryID==$Row2["BoardCategoryID"]) {echo "selected";}?>><?=$Row2["BoardCategoryName"]?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
						
					</div>
					<?}else{?>
					<div class="uk-width-medium-2-10" style="padding-top:7px;"></div>
					<?}?>

					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$검색[$LangID]?></label>
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
								<option value="1" <?php if ($SearchItem=="1") {echo "selected";}?>><?=$제목_내용[$LangID]?></option>
								<option value="2" <?php if ($SearchItem=="2") {echo "selected";}?>><?=$제목[$LangID]?></option>
								<option value="3" <?php if ($SearchItem=="3") {echo "selected";}?>><?=$내용[$LangID]?></option>
								<option value="4" <?php if ($SearchItem=="4") {echo "selected";}?>><?=$작성자[$LangID]?></option>
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
										<th><?=$제목[$LangID]?></th>
										<?if ($BoardCode=="event"){?>
										<th style="width:10%;" nowrap><?=$시작일[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$종료일[$LangID]?></th>
										<?}else{?>

										<th style="width:10%;" nowrap><?=$작성자[$LangID]?></th>
										<?if ($BoardCode=="qna"){?>
										<th nowrap><?=$요약[$LangID]?></th>
										<th nowrap><?=$스케줄[$LangID]?></th>
										<?}?>
										<th style="width:10%;" nowrap><?=$작성일[$LangID]?></th>
										<?}?>
										<th style="width:10%;" nowrap><?=$조회수[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
								<?php
								$ListCount = 1;
								while($Row = $Stmt->fetch()) {
									$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$BoardContentMemberID = $Row["BoardContentMemberID"];
										$MemberLevelID = $Row["MemberLevelID"];
										$MemberLoginID = $Row["MemberLoginID"];

									if ($Row["BoardContentNotice"]==1){
										$IcoNotice = "<img src='images/icon_notice.png' class='notice'>";
										$IcoReply = "";
										$IcoDepth = "";
									}else{
										$IcoNotice = "";
									
										$IcoReply = "";
										if ($Row["BoardContentReplyOrder"]>0) {
											$IcoReply = "<img src='images/icon_re.png'>";
										}else{
											$IcoReply = "";
										}

										
										$IcoDepth = "";
										if ($Row["BoardContentReplyDepth"]>0){
											for ($ii=1; $ii<=$Row["BoardContentReplyDepth"]; $ii++){
												$IcoDepth = $IcoDepth . "&nbsp;&nbsp;";
											}
										}else{
											$IcoDepth = "";
										}
									
									}



									if ($Row["BoardCommentCount"]==0){
										$StrBoardCommentCount = "";
									}else{
										$StrBoardCommentCount = "&nbsp;<span style='color:#FF663C'>[".$Row["BoardCommentCount"]."]</span>";
									}

									if ($Row["RecentDay"]>=6){
										$IcoNew = "";
									}else{
										$IcoNew = " <img src='images/icon_new.png' class='notice'>";
									}

									if ($Row["BoardContentSecret"]==0){
										$IcoSecret = "";
									}else{
										$IcoSecret = " <img src='images/icon_key.png'>";
									}


									$StrListNumber = $ListNumber;
									if ($IcoNotice!=""){
										$StrListNumber = $IcoNotice;
									}

									$EventStartDate = $Row["EventStartDate2"];
									$EventEndDate = $Row["EventEndDate2"];
									?>

									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrListNumber?></td>
										<td class="uk-text-nowrap uk-table-td">
											<?=$IcoDepth?><?=$IcoReply?><a href="board_read.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$Row["BoardContentID"]?>&BoardCode=<?=$BoardCode?>" class="color"><?=$Row["BoardContentSubject"]?></a><?=$StrBoardCommentCount?><?=$IcoSecret?><?=$IcoNew?>
										</td>

										<?if ($BoardCode=="event" || $BoardCode=="main_notice"){?>
										<td class="uk-text-nowrap uk-table-td-center"><?=$EventStartDate?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$EventEndDate?></td>
										<?}else{?>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Row["BoardContentWriterName"]?></td>
										<?if ($BoardCode=="qna"){?>
											<td class="uk-text-nowrap uk-table-td-center">
												<?if ($MemberLoginID!="") {?>
													<a href="javascript:OpenStudentForm(<?=$BoardContentMemberID?>);"><i class="material-icons">account_box</i></a>										
												<?}else{?>
													-
												<?}?>
											</td>
											
											<td class="uk-text-nowrap uk-table-td-center">
												<?if ($MemberLevelID==19) {?>
												<a href="javascript:OpenStudentCalendar(<?=$BoardContentMemberID?>);"><i class="material-icons">date_range</i></a>
												<?}else{?>
												-
												<?}?>
											</td>
										<?}?>
										<td class="uk-text-nowrap uk-table-td-center"><?=substr($Row["BoardContentRegDateTime"],0,10)?></td>
										<?}?>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Row["BoardContentViewCount"]?></td>
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


						<?if ($_LINK_ADMIN_LEVEL_ID_>1 && $BoardCode=="hrfile"){?>

						<?}else{?>
						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="board_form.php?BoardCode=<?=$BoardCode?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
						</div>
						<?}?>

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
	document.SearchForm.action = "board_list.php";
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