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
$MainMenuID = 28;
$SubMenuID = 2804;

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
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherDataState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.TeacherDataState<>0 ";


if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.WriterName like '%".$SearchText."%' ";
}




$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from TeacherDatas A 
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
			A.*
		from TeacherDatas A 

		where ".$AddSqlWhere." 
		order by A.TeacherDataRegDateTime desc limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$일대일문의[$LangID]?></h3>

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
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$공개[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$미공개[$LangID]?></option>
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
										<th nowrap><?=$자료다운로드[$LangID]?></th>
										<th style="width:15%" nowrap><?=$보낸시간[$LangID]?></th>
										<th style="width:15%" nowrap><?=$받은시간[$LangID]?></th>
										<th style="width:10%" nowrap><?=$보낸사람[$LangID]?></th>
										<th style="width:10%" nowrap><?=$받는사람[$LangID]?></th>
										<th style="width:10%" nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$TeacherDataID = $Row["TeacherDataID"];
										$SendMemberID = $Row["SendMemberID"];
										$SendMemberName = $Row["SendMemberName"];
										$ReceiveMemberID = $Row["ReceiveMemberID"];
										$ReceiveMemberName = $Row["ReceiveMemberName"];
										$TeacherDataTitle = $Row["TeacherDataTitle"];
										$TeacherDataRegDateTime = $Row["TeacherDataRegDateTime"];
										$TeacherDataReceiveDateTime = $Row["TeacherDataReceiveDateTime"];
										$TeacherDataState = $Row["TeacherDataState"];
										
										
										if ($TeacherDataState==1){
											$StrTeacherDataState = "<?=$확인전[$LangID]?>";
										}else if ($TeacherDataState==2){
											$StrTeacherDataState = "<?=$확인완료[$LangID]?>";
										}

							
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap"><a href="javascript:DownTeacherData('<?=$TeacherDataID?>')"><i class="material-icons">save_alt</i><?=$TeacherDataTitle?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherDataRegDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherDataReceiveDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$SendMemberName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ReceiveMemberName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrTeacherDataState?></td>
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

						<div class="uk-form-row" style="text-align:center;display:none;">
							<a type="button" href="javascript:OpenTeacherDataForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
<script>
function DownTeacherData(TeacherDataID){
	location.href = "teacher_data_file_down.php?TeacherDataID="+TeacherDataID;
	setTimeout(function(){
		location.reload();
	}, 2000);
}
</script>



<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "teacher_data_list.php";
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