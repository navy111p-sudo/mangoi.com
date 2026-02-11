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
$MainMenuID = 29;
$SubMenuID = 2923;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
include_once('./inc_document_common.php');
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
	$SearchState = "100";
}	
		
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.DocumentReportState=$SearchState ";
}

$AddSqlWhere = $AddSqlWhere . " and A.DocumentReportState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.DocumentReportName like '%".$SearchText."%' ";
}
$AddSqlWhere = $AddSqlWhere . " and A.MemberID=".$_LINK_ADMIN_ID_." ";
$AddSqlWhere = $AddSqlWhere . " and A.DocumentID=99 ";


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "SELECT  
				count(*) TotalRowCount 
		from DocumentReports A 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );



$Sql = "SELECT 
			A.*,
			ifnull(B.DocumentName, '-') as DocumentName,
			C.MemberName,
			C.MemberDprtName
		from DocumentReports A 
			inner join Members C on A.MemberID=C.MemberID 
			left outer join Documents B on A.DocumentID=B.DocumentID 
		where ".$AddSqlWhere." 
		order by A.DocumentReportRegDateTime desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$기안_및_지출서_목록[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">

		

					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$문서명[$LangID]?></label>
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
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>>제출완료</option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>>작성중</option>
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
										<th nowrap><?=$사용양식[$LangID]?></th>
										<th nowrap><?=$문서명[$LangID]?></th>
										<th nowrap><?=$부서[$LangID]?></th>
										<th nowrap><?=$작성자[$LangID]?></th>
										<th nowrap><?=$회람인원[$LangID]?></th>
										<th nowrap><?=$작성일[$LangID]?></th>
										<th nowrap><?=$상태[$LangID]?></th>
										<th nowrap width='13%'>문서복사</th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$MemberName = $Row["MemberName"];
										$MemberDprtName = $Row["MemberDprtName"];
										$DocumentReportID = $Row["DocumentReportID"];
										$DocumentReportName = $Row["DocumentReportName"];
										$DocumentReportState = $Row["DocumentReportState"];
										$DocumentReportRegDateTime = $Row["DocumentReportRegDateTime"];


										$DocumentName = $기안_및_지출서[$LangID];
										
										if ($DocumentReportState==1){
											$StrDocumentReportState = "<span class=\"ListState_1\">".$제출완료[$LangID]."</span>";
										}else if ($DocumentReportState==2){
											$StrDocumentReportState = "<span class=\"ListState_2\">".$작성중[$LangID]."</span>";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap"><a href="my_document_draft_form.php?ListParam=<?=$ListParam?>&DocumentReportID=<?=$DocumentReportID?>"><?=$DocumentReportName?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$DocumentName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberDprtName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberName?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<?

											//결재해야 할 사람 수와 이미 결재완료한 사람 수가 일치하면 메시지를 '승인'으로 다르면 '진행중'으로 표시
											//if (compareApprovalMemberCount($DocumentReportID)) $ApporovalMessage = $승인[$LangID];
											//else $ApporovalMessage = "진행중";

											$Sql3 = "select A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID where A.DocumentReportID=".$DocumentReportID." order by B.MemberName asc";
											$Stmt3 = $DbConn->prepare($Sql3);
											$Stmt3->execute();
											$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
											
											$ii=1;
											while($Row3 = $Stmt3->fetch()) {
										
												$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
												if ($DocumentReportMemberState==0){
													$StrDocumentReportMemberState = "-";
												}else if ($DocumentReportMemberState==1){
													$StrDocumentReportMemberState = "<font color='blue'>O</font>";
												}else if ($DocumentReportMemberState==2){
													$StrDocumentReportMemberState = "<font color='red'>X</font>";
												}

												if ($ii>1){
													echo ", ";
												}
											?>
												<b><?=$Row3["MemberName"]?> (<?=$StrDocumentReportMemberState?>) </b>
											<?
												$ii++;
											}
											$Stmt3 = null;
											
											?>
										
										</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=substr($DocumentReportRegDateTime,0,10)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrDocumentReportState?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a type="button" href="my_document_draft_form.php?ListParam=<?=$ListParam?>&DocumentReportID=<?=$DocumentReportID?>&CopyMode=1" class="md-btn md-btn-primary"><?=$문서복사새문서[$LangID]?></a></td>
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
							<a type="button" href="my_document_draft_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
	document.SearchForm.action = "my_document_draft_list.php";
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