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
$SubMenuID = 1103;
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
	$AddSqlWhere = $AddSqlWhere . " and A.PopupState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.PopupState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.PopupName like '%".$SearchText."%' or A.PopupTitle like '%".$SearchText."%') ";
}

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select count(*) TotalRowCount from Popups A where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );



$Sql = "select A.* from Popups A where ".$AddSqlWhere." order by A.PopupID desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$팝업관리[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					
					<div class="uk-width-medium-6-10"></div>

					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$팝업명_또는_타이틀[$LangID]?></label>
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
							<select id="SearchState" name="SearchState" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>>전체</option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$활성[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$비활성[$LangID]?></option>
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
										<th nowrap><?=$제목[$LangID]?></th>
										<th nowrap style="width:80px;">WEB</th>
										<th nowrap style="width:80px;">MOBILE</th>
										<th nowrap style="width:80px;">APP</th>
										<?
										$ArrDomainSiteName = explode("|", "본사|SLP|EIE|DREAM|THOMAS|ENG_TELL");

										for ($ii=0;$ii<=5;$ii++){
										?>
										<th nowrap style='color:#0080C0;width:80px;'><?=$ArrDomainSiteName[$ii]?></th>
										<?
										}
										?>

										<th nowrap><?=$시작일[$LangID]?></th>
										<th nowrap><?=$종료일[$LangID]?></th>
										<th nowrap><?=$미리보기[$LangID]?></th>
										<th nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$WebPopup = $Row["WebPopup"];
										$MobilePopup = $Row["MobilePopup"];
										$AppPopup = $Row["AppPopup"];
										
										$ArrDomainSiteID[0] = $Row["DomainSiteID_0"];
										$ArrDomainSiteID[1] = $Row["DomainSiteID_1"];
										$ArrDomainSiteID[2] = $Row["DomainSiteID_2"];
										$ArrDomainSiteID[3] = $Row["DomainSiteID_3"];
										$ArrDomainSiteID[4] = $Row["DomainSiteID_4"];
										$ArrDomainSiteID[5] = $Row["DomainSiteID_5"];
										
										
										if ($Row["PopupState"]==1){
											$StrPopupState = "활성";
										}else{
											$StrPopupState = "비활성";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap"><a href="popup_form.php?ListParam=<?=$ListParam?>&PopupID=<?=$Row["PopupID"]?>"><?=$Row["PopupName"]?></a></td>

										<td class="uk-text-nowrap uk-table-td-center">
											<?if ($WebPopup==1){?>
											O
											<?}else{?>
											-
											<?}?>
										</td>
										<td class="uk-text-nowrap uk-table-td-center">
											<?if ($MobilePopup==1){?>
											O
											<?}else{?>
											-
											<?}?>
										</td>
										<td class="uk-text-nowrap uk-table-td-center">
											<?if ($AppPopup==1){?>
											O
											<?}else{?>
											-
											<?}?>
										</td>

										<?for ($ii=0;$ii<=5;$ii++){?>
											<td class="uk-text-nowrap uk-table-td-center" style="color:#0080C0;">
												<?if ($ArrDomainSiteID[$ii]==1){?>
												O
												<?}else{?>
												-
												<?}?>
											</td>
										<?}?>

										<td class="uk-text-nowrap uk-table-td-center"><?=$Row["PopupStartDateNum"]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Row["PopupEndDateNum"]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><span class="uk-badge uk-badge-muted"><a href="javascript:PreviewPopup(<?=$Row["PopupID"]?>,<?=$Row["PopupTop"]?>,<?=$Row["PopupLeft"]?>,<?=$Row["PopupWidth"]?>,<?=$Row["PopupHeight"]?>)">미리보기</a></span></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrPopupState?></td>
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
							<a type="button" href="popup_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
function PopupAddImage(PoupuID){
	openurl = "popup_form.php?PoupuID="+PoupuID;
	$.colorbox({	
		href:openurl
		,width:"500" 
		,height:"300"
		,title:""
		,iframe:true 
		,scrolling:false
		//,onClosed:function(){location.reload(true);}   
	}); 

	//openurl = "../pop_image_upload_form.php?ImgID="+ImgID+"&FormName="+FormName+"&Path="+Path+"&PopupType=1";
	//window.open(openurl,'pop_image_upload','width=500,height=280,toolbar=no,top=100,left=100');
}


function SearchSubmit(){
	document.SearchForm.action = "popup_list.php";
	document.SearchForm.submit();
}

function PreviewPopup(id, t, l, w, h){
	h=h+40;
	newwin = window.open('../popup_preview.php?PopupID='+id,'','width='+w+',height='+h+',toolbar=no,top='+t+',left='+l);
	newwin.focus();
}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>