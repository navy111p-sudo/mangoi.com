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
$MainMenuID = 11;
$SubMenuID = 1134;

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

//if ($SearchState==""){
//	$SearchState = "100";
//}	
		
//if ($SearchState!="100"){
//	$ListParam = $ListParam . "&SearchState=" . $SearchState;
//	$AddSqlWhere = $AddSqlWhere . " and A.DirectQnaMemberState=$SearchState ";
//}
//$AddSqlWhere = $AddSqlWhere . " and A.DirectQnaMemberState<>0 ";

$AddSqlWhere = $AddSqlWhere . " and A.MemberPointTypeID>0 ";
$AddSqlWhere = $AddSqlWhere . " and A.MemberPointTypeState=1 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.MemberPointTypeName like '%".$SearchText."%' ";
}




$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from MemberPointNewTypes A 
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
		from MemberPointNewTypes A 
		where ".$AddSqlWhere." 
		order by A.MemberPointTypeType asc, A.MemberPointTypeMethod asc, A.MemberPointTypeID asc";// limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$포인트_항목관리[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">





					<div class="uk-width-medium-6-10">

					</div>
					<div class="uk-width-medium-3-10">
						<label for="SearchText"><?=$포인트명[$LangID]?></label>
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
							
							<div style="margin-bottom:10px;">※ {{이름}} 는 이름으로, {{포인트}} 는 지급 포인트로 치환되어 메시지가 발송됩니다.</div>
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th style="width:5%" nowrap>No</th>
										<th style="width:10%" nowrap><?=$구분[$LangID]?></th>
										<th style="width:10%" nowrap><?=$방식[$LangID]?></th>
										<th style="width:10%" nowrap><?=$포인트명[$LangID]?></th>
										<th style="width:10%" nowrap><?=$포인트[$LangID]?></th>
										<th nowrap><?=$메시지[$LangID]?></th>	
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$MemberPointTypeID = $Row["MemberPointTypeID"];
										$MemberPoint = $Row["MemberPoint"];
										$MemberPointTypeType = $Row["MemberPointTypeType"];
										$MemberPointTypeMethod = $Row["MemberPointTypeMethod"];
										$MemberPointTypeName = $Row["MemberPointTypeName"];
										$MemberPointTypeText = $Row["MemberPointTypeText"];

										if ($MemberPointTypeType==1){
											$StrMemberPointTypeType = "<span style='color:#1076B8;'>".$학생[$LangID]."</span>";
										}else if ($MemberPointTypeType==2){
											$StrMemberPointTypeType = "<span style='color:#CC99FF;'>".$학부모[$LangID]."</span>";
										}else if ($MemberPointTypeType==3){
											$StrMemberPointTypeType = "<span style='color:#CC6666;'>".$대리점[$LangID]."</span>";
										}

										if ($MemberPointTypeMethod==1){
											$StrMemberPointTypeMethod = "<span style='color:#CC47CC;'>".$자동[$LangID]."</span>";
										}else if ($MemberPointTypeMethod==2){
											$StrMemberPointTypeMethod = "<span style='color:#4747CC;'>".$수동[$LangID]."</span>";
										}


						
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrMemberPointTypeType?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrMemberPointTypeMethod?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenPointTypeForm(<?=$MemberPointTypeID?>)"><?=$MemberPointTypeName?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($MemberPoint,0)?></td>
										<td class="uk-text-nowrap"><?=$MemberPointTypeText?></td>
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
						//include_once('./inc_pagination.php');
						?>

						<!--
						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="javascript:OpenDirectQnaMemberForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
function OpenPointTypeForm(MemberPointTypeID){
	openurl = "point_type_form.php?MemberPointTypeID="+MemberPointTypeID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
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
	document.SearchForm.action = "point_type_list.php";
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