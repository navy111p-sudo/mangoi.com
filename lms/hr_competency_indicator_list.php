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
$MainMenuID = 88;
$SubMenuID = 8805;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>


 
<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
//$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
//$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";


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
//	$SearchState = "1";
//}	
		
//if ($SearchState!="100"){
//	$ListParam = $ListParam . "&SearchState=" . $SearchState;
//	$AddSqlWhere = $AddSqlWhere . " and A.Hr_CompetencyIndicatorCate1State=$SearchState ";
//}
$AddSqlWhere = $AddSqlWhere . " and A.Hr_CompetencyIndicatorCate1State=1 ";

//if ($SearchText!=""){
//	$ListParam = $ListParam . "&SearchText=" . $SearchText;
//	$AddSqlWhere = $AddSqlWhere . " and A.CouponName like '%".$SearchText."%' ";
//}


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from Hr_CompetencyIndicatorCate1 A 
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
		from Hr_CompetencyIndicatorCate1 A 
		where ".$AddSqlWhere." 
		order by A.Hr_CompetencyIndicatorCate1Order asc";// limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$역량평가_문항관리[$LangID]?></h3>

		
		<!--
		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">

					
					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$쿠폰명[$LangID]?> </label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>
					

					<div class="uk-width-medium-1-10">
						<div class="uk-margin-small-top">
							<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$사용중[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$미사용[$LangID]?></option>
							</select>
						</div>
					</div>

					<div class="uk-width-medium-1-10 uk-text-center">
						<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?> </a>
					</div>

					
				</div>
			</div>
		</div>
		</form>
		-->


		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap style="width:30px;">No</th>
										<th nowrap style="width:100px;"><?=$역량군명[$LangID]?></th>
										<th nowrap style="width:50px;"><?=$관리[$LangID]?></th>
										<th nowrap><?=$역량목록[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$Hr_CompetencyIndicatorCate1ID = $Row["Hr_CompetencyIndicatorCate1ID"];
										$Hr_CompetencyIndicatorCate1State = $Row["Hr_CompetencyIndicatorCate1State"];
										
										$Hr_CompetencyIndicatorCate1Name = $Row["Hr_CompetencyIndicatorCate1Name"];
										

									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_CompetencyIndicatorCate1Name?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:OpenCompetencyIndicatorCate1Form(<?=$Hr_CompetencyIndicatorCate1ID?>)"><?=$관리[$LangID]?></a>
										</td>
										<td class="uk-text-nowrap uk-table-td-center">



											<table class="uk-table uk-table-align-vertical">
												<thead>
													<tr>
														<th nowrap style="width:30px;">No</th>
														<th nowrap style="width:130px;"><?=$역량명[$LangID]?></th>
														<th nowrap style="width:50px;"><?=$관리[$LangID]?></th>
														<th nowrap><?=$행동지표목록[$LangID]?></th>
													</tr>
												</thead>
												<tbody>
													
													<?php
													$Sql2 = "
															select 
																A.*
															from Hr_CompetencyIndicatorCate2 A 
															where A.Hr_CompetencyIndicatorCate1ID=:Hr_CompetencyIndicatorCate1ID 
															order by A.Hr_CompetencyIndicatorCate2Order asc";// limit $StartRowNum, $PageListNum";

													$Stmt2 = $DbConn->prepare($Sql2);
													$Stmt2->bindParam(':Hr_CompetencyIndicatorCate1ID', $Hr_CompetencyIndicatorCate1ID);
													$Stmt2->execute();
													$Stmt2->setFetchMode(PDO::FETCH_ASSOC);


													$ListCount2 = 1;
													while($Row2 = $Stmt2->fetch()) {
														
														$Hr_CompetencyIndicatorCate2ID = $Row2["Hr_CompetencyIndicatorCate2ID"];
														$Hr_CompetencyIndicatorCate2State = $Row2["Hr_CompetencyIndicatorCate2State"];
														$Hr_CompetencyIndicatorCate2Name = $Row2["Hr_CompetencyIndicatorCate2Name"];
													?>
													<tr style="border-bottom:1px solid #cccccc;">
														<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount2?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_CompetencyIndicatorCate2Name?></td>
														<td class="uk-text-nowrap uk-table-td-center">
															<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:OpenCompetencyIndicatorCate2Form(<?=$Hr_CompetencyIndicatorCate1ID?>, <?=$Hr_CompetencyIndicatorCate2ID?>)"><?=$관리[$LangID]?></a>
														</td>
														<td class="uk-text-nowrap uk-table-td-center">
														
														
															<table class="uk-table uk-table-align-vertical">
																<thead>
																	<tr>
																		<th nowrap style="width:30px;">No</th>
																		<th nowrap><?=$행동지표[$LangID]?></th>
																		<th nowrap>상사</th>
																		<th nowrap>동료</th>
																		<th nowrap>부하</th>
																		<th nowrap style="width:50px;"><?=$관리[$LangID]?></th>
																	</tr>
																</thead>
																<tbody>
																	
																	<?php
																	$Sql3 = "SELECT 
																				A.*
																			from Hr_CompetencyIndicators A 
																			where A.Hr_CompetencyIndicatorCate2ID=:Hr_CompetencyIndicatorCate2ID  
																			      AND A.Hr_CompetencyIndicatorState = 1 
																			order by A.Hr_CompetencyIndicatorOrder asc";// limit $StartRowNum, $PageListNum";

																	$Stmt3 = $DbConn->prepare($Sql3);
																	$Stmt3->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
																	$Stmt3->execute();
																	$Stmt3->setFetchMode(PDO::FETCH_ASSOC);


																	$ListCount3 = 1;
																	while($Row3 = $Stmt3->fetch()) {
																		
																		$Hr_CompetencyIndicatorID = $Row3["Hr_CompetencyIndicatorID"];
																		$Hr_CompetencyIndicatorState = $Row3["Hr_CompetencyIndicatorState"];
																		$Hr_CompetencyIndicatorName = $Row3["Hr_CompetencyIndicatorName"];
																		$Hr_MemberType1 = $Row3["Hr_MemberType1"];
																		$Hr_MemberType2 = $Row3["Hr_MemberType2"];
																		$Hr_MemberType3 = $Row3["Hr_MemberType3"];
																	?>
																	<tr style="border-bottom:1px solid #cccccc;">
																		<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount3?></td>
																		<td class="uk-text-nowrap uk-table-td-center" style="width:70%;text-align:left;"><?=$Hr_CompetencyIndicatorName?></td>
																		<td class="uk-text-nowrap uk-table-td-center">
																			<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" 
																				href="javascript:SetCompetencyIndicatorMember(<?=$Hr_CompetencyIndicatorID?>,3,<?=($Hr_MemberType3==0)?1:0?>)" 
																				style="background-color:<?if ($Hr_MemberType3>0){?>#556BAC<?}else{?>#cccccc;<?}?>" 
																				id="SetBtn_<?=$Hr_CompetencyIndicatorID?>3">
																			<?if ($Hr_MemberType3>0){?>사용<?}else{?>미사용<?}?></a>
																		</td>
																		<td class="uk-text-nowrap uk-table-td-center">
																			<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" 
																					href="javascript:SetCompetencyIndicatorMember(<?=$Hr_CompetencyIndicatorID?>,2,<?=($Hr_MemberType2==0)?1:0?>)" 
																					style="background-color:<?if ($Hr_MemberType2>0){?>#556BAC<?}else{?>#cccccc;<?}?>" 
																					id="SetBtn_<?=$Hr_CompetencyIndicatorID?>2">
																				<?if ($Hr_MemberType2>0){?>사용<?}else{?>미사용<?}?></a>
																		</td>
																		<td class="uk-text-nowrap uk-table-td-center">
																			<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" 
																					href="javascript:SetCompetencyIndicatorMember(<?=$Hr_CompetencyIndicatorID?>,1,<?=($Hr_MemberType1==0)?1:0?>)" 
																					style="background-color:<?if ($Hr_MemberType1>0){?>#556BAC<?}else{?>#cccccc;<?}?>" 
																					id="SetBtn_<?=$Hr_CompetencyIndicatorID?>1">
																				<?if ($Hr_MemberType1>0){?>사용<?}else{?>미사용<?}?></a>
																		</td>
																		<td class="uk-text-nowrap uk-table-td-center">
																			<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:OpenCompetencyIndicatorForm(<?=$Hr_CompetencyIndicatorCate2ID?>, <?=$Hr_CompetencyIndicatorID?>)"><?=$관리[$LangID]?></a>
																		</td>
																	</tr>
																	<?php
																		$ListCount3 ++;
																	}
																	$Stmt3 = null;
																	?>


																</tbody>
															</table>

															<div class="uk-form-row" style="text-align:right; margin-top:20px;">
																<a type="button" href="javascript:OpenCompetencyIndicatorForm(<?=$Hr_CompetencyIndicatorCate2ID?>, '')" class="md-btn md-btn-primary" style="background-color:#ffb400;"><?=$행동지표[$LangID]?> <?=$신규등록[$LangID]?></a>
															</div>



														</td>
													</tr>
													<?php
														$ListCount2 ++;
													}
													$Stmt2 = null;
													?>


												</tbody>
											</table>

											<div class="uk-form-row" style="text-align:right; margin-top:20px;">
												<a type="button" href="javascript:OpenCompetencyIndicatorCate2Form(<?=$Hr_CompetencyIndicatorCate1ID?>, '')" class="md-btn md-btn-primary" style="background-color:#556BAC;"><?=$역량행동지표[$LangID]?> <?=$신규등록[$LangID]?></a>
											</div>



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
						//include_once('./inc_pagination.php');
						?>

						<div class="uk-form-row" style="text-align:center; margin-top:20px;">
							<a type="button" href="javascript:OpenCompetencyIndicatorCate1Form('')" class="md-btn md-btn-primary"><?=$역량군[$LangID]?> <?=$신규등록[$LangID]?></a>
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
function SetCompetencyIndicatorMember(Hr_CompetencyIndicatorID, memberType, SetType){

	UIkit.modal.confirm(
		'<?=$상태를_변경_하시겠습니까[$LangID]?>?', 
		function(){ 

			url = "hr_ajax_set_competency_indicator_member.php";
			
			$.ajax(url, {
				data: {
					Hr_CompetencyIndicatorID: Hr_CompetencyIndicatorID,
					memberType: memberType,
					SetType: SetType
				},
				success: function (data) {
					if (SetType == 1)	{
						document.getElementById("SetBtn_"+Hr_CompetencyIndicatorID+memberType).style.backgroundColor = "#556BAC";
						document.getElementById("SetBtn_"+Hr_CompetencyIndicatorID+memberType).innerHTML = "<?=$사용[$LangID]?>";
						document.getElementById("SetBtn_"+Hr_CompetencyIndicatorID+memberType).href = "javascript:SetCompetencyIndicatorMember("+Hr_CompetencyIndicatorID+","+memberType+",0)";
					} else {
						document.getElementById("SetBtn_"+Hr_CompetencyIndicatorID+memberType).style.backgroundColor = "#cccccc";
						document.getElementById("SetBtn_"+Hr_CompetencyIndicatorID+memberType).innerHTML = "<?=$미사용[$LangID]?>";
						document.getElementById("SetBtn_"+Hr_CompetencyIndicatorID+memberType).href = "javascript:SetCompetencyIndicatorMember("+Hr_CompetencyIndicatorID+","+memberType+",1)";
					}
				},
				error: function () {
					alert('Error while contacting server, please try again');
				}

			});


		}
	);


}
</script>

<script>
function OpenCompetencyIndicatorCate1Form(Hr_CompetencyIndicatorCate1ID){
	openurl = "hr_competency_indicator_cate_1_form.php?Hr_CompetencyIndicatorCate1ID="+Hr_CompetencyIndicatorCate1ID;
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

function OpenCompetencyIndicatorCate2Form(Hr_CompetencyIndicatorCate1ID, Hr_CompetencyIndicatorCate2ID){
	openurl = "hr_competency_indicator_cate_2_form.php?Hr_CompetencyIndicatorCate1ID="+Hr_CompetencyIndicatorCate1ID+"&Hr_CompetencyIndicatorCate2ID="+Hr_CompetencyIndicatorCate2ID;
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

function OpenCompetencyIndicatorForm(Hr_CompetencyIndicatorCate2ID, Hr_CompetencyIndicatorID){
	openurl = "hr_competency_indicator_form.php?Hr_CompetencyIndicatorCate2ID="+Hr_CompetencyIndicatorCate2ID+"&Hr_CompetencyIndicatorID="+Hr_CompetencyIndicatorID;
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

function SearchSubmit(){
	document.SearchForm.action = "hr_competency_indicator_list.php";
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