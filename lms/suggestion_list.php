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
$MainMenuID = 24;
$SubMenuID = 2411;

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
	$SearchState = "100";
}	
		
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.SuggestionState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.SuggestionState<>0 ";


if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.MemberName like '%".$SearchText."%' ";
}




$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from Suggestions A 
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
		B.MemberLoginID,
		B.MemberLevelID	
		from Suggestions A 
		left outer join Members B on A.MemberID=B.MemberID

		where ".$AddSqlWhere." 
		order by A.SuggestionRegDateTime desc limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$건의사항[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">





					<div class="uk-width-medium-6-10">

					</div>
					<div class="uk-width-medium-3-10">
						<label for="SearchText"><?=$작성자[$LangID]?></label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>

					<!--
					<div class="uk-width-medium-1-10">
						<label for="product_search_price">Price</label>
						<input type="text" class="md-input" id="product_search_price">
					</div>
					-->

					<!--
					<div class="uk-width-medium-1-10">
						<div class="uk-margin-small-top">
							<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>>전체</option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>>공개</option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>>미공개</option>
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
										<th style="width:10%" nowrap>No</th>
										<th nowrap><?=$제목[$LangID]?></th>
										<th style="width:10%" nowrap><?=$작성자[$LangID]?></th>
										<th nowrap><?=$요약[$LangID]?></th>
										<th nowrap><?=$스케줄[$LangID]?></th>										
										<th style="width:10%" nowrap><?=$답변자[$LangID]?></th>
										<th style="width:15%" nowrap><?=$등록시간[$LangID]?></th>
										<th style="width:15%" nowrap><?=$답변시간[$LangID]?></th>
										<th style="width:10%" nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$SuggestionID = $Row["SuggestionID"];
										$MemberID = $Row["MemberID"];
										$MemberLoginID = $Row["MemberLoginID"];
										$MemberLevelID = $Row["MemberLevelID"];
										$MemberName = $Row["MemberName"];
										$AnswerMemberID = $Row["AnswerMemberID"];
										$AnswerMemberName = $Row["AnswerMemberName"];
										$SuggestionTitle = $Row["SuggestionTitle"];
										$SuggestionContent = $Row["SuggestionContent"];
										$SuggestionAnswer = $Row["SuggestionAnswer"];
										$SuggestionRegDateTime = $Row["SuggestionRegDateTime"];
										$SuggestionAnswerRegDateTime = $Row["SuggestionAnswerRegDateTime"];
										$SuggestionState = $Row["SuggestionState"];
										
										
										if ($SuggestionState==1){
											$StrSuggestionState = $답변전[$LangID];
										}else if ($SuggestionState==2){
											$StrSuggestionState = $답변완료[$LangID];
										}

										if ($SuggestionTitle==""){
											$SuggestionTitle = $제목없음[$LangID];
										}

							
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap"><a href="javascript:OpenSuggestionForm(<?=$SuggestionID?>)"><?=$SuggestionTitle?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<?if ($MemberLoginID!="") {?>
												<a href="student_form.php?MemberID=<?=$MemberID?>" target="student_info"><?=$MemberName?></a>
											<?}else{?>
												<?=$MemberName?>
											<?}?>
										</td>
										<td class="uk-text-nowrap uk-table-td-center">
											<?if ($MemberLoginID!="") {?>
												<a href="javascript:OpenStudentForm(<?=$MemberID?>);"><i class="material-icons">account_box</i></a>										
											<?}else{?>
												-
											<?}?>
										</td>
										
										<td class="uk-text-nowrap uk-table-td-center">
											<?if ($MemberLevelID==19) {?>
											<a href="javascript:OpenStudentCalendar(<?=$MemberID?>);"><i class="material-icons">date_range</i></a>
											<?}else{?>
											-
											<?}?>
										</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$AnswerMemberName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$SuggestionRegDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$SuggestionAnswerRegDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrSuggestionState?></td>
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

						<!--
						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="javascript:OpenSuggestionForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
<script>
function OpenSuggestionForm(SuggestionID){
	openurl = "suggestion_form.php?SuggestionID="+SuggestionID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}
</script>



<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "suggestion_list.php";
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