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
$SubMenuID = 2405;
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
	$SearchState = "1";
}	
		
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.EventState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.EventState<>0 ";


if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.EventTitle like '%".$SearchText."%' ";
}


$PaginationParam = $ListParam;

if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}



$ListParam = str_replace("&", "^^", $ListParam);


$Sql = "select 
				count(*) TotalRowCount 
		from Events A 

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
			* 
		from Events A 
		where ".$AddSqlWhere." order by A.EventOrder desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$이벤트[$LangID]?></h3>


		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					
					<div class="uk-width-medium-6-10">
					</div>

					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$항목명[$LangID]?></label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>

					<div class="uk-width-medium-1-10">
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
										<th style="width:10%;" nowrap>No</th>
										<th style="width:10%;" nowrap><?=$이미지[$LangID]?></th>
										<th nowrap><?=$이벤트명[$LangID]?></th>
										<th style="width:15%;" nowrap><?=$시작일[$LangID]?></th>
										<th style="width:15%;" nowrap><?=$종료일[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$사이트노출[$LangID]?></th>
										<th style="width:10%;" nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$EventID = $Row["EventID"];
										$EventTitle = $Row["EventTitle"];
										$EventContent = $Row["EventContent"];
										$EventImageFileName = $Row["EventImageFileName"];
										$EventStartDate = $Row["EventStartDate"];
										$EventEndDate = $Row["EventEndDate"];
										$EventView = $Row["EventView"];
										$EventState = $Row["EventState"];
										
										if ($EventView==1){
											$StrEventView = "<span class=\"ListState_1\"><?=$사이트노출[$LangID]?></span>";
										}else if ($EventView==0){
											$StrEventView = "<span class=\"ListState_2\"><?=$사이트숨김[$LangID]?></span>";
										}

										
										if ($EventState==1){
											$StrEventState = "<span class=\"ListState_1\"><?=$운영중[$LangID]?></span>";
										}else if ($EventState==2){
											$StrEventState = "<span class=\"ListState_2\"><?=$미운영[$LangID]?></span>";
										}

										if ($EventImageFileName==""){
											$StrEventImageFileName = "images/logo_mangoi.png";
										}else{
											$StrEventImageFileName = "../uploads/event_images/".$EventImageFileName;
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><img src="<?=$StrEventImageFileName?>" style="width:80px;height:80px;"></td>
										<td class="uk-text-nowrap uk-table-td"><a href="javascript:OpenEventForm(<?=$EventID?>)"><?=$EventTitle?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$EventStartDate?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$EventEndDate?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrEventView?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrEventState?></td>
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

						<div class="uk-form-row" style="text-align:center;margin-top:20px;">
							<a type="button" href="javascript:OpenEventForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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

function EventListOrder(EventID, OrderType) {

	url = "ajax_set_event_list_order.php";

	
	//location.href = url + "?EventID="+EventID+"&OrderType="+OrderType;

    $.ajax(url, {
        data: {
			EventID: EventID,
			OrderType: OrderType
        },
        success: function () {
			location.reload();
        },
        error: function () {
            alert('Error while contacting server, please try again');
        }

    });
}

function OpenEventForm(EventID){
	openurl = "event_form.php?EventID="+EventID;
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
	document.SearchForm.action = "event_list.php";
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