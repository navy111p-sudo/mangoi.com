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
$SubMenuID = 8809;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>


 
<?php
// 카테고리별 역량의 총 개수
$Sql = "
		select 
			count(*) as TotalRowCount
		from Hr_CompetencyIndicatorCate1 A 
			left outer join Hr_CompetencyIndicatorCate2 B on A.Hr_CompetencyIndicatorCate1ID=B.Hr_CompetencyIndicatorCate1ID 
		where A.Hr_CompetencyIndicatorCate1State=1 and B.Hr_CompetencyIndicatorCate2State=1 ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalRowCount = $Row["TotalRowCount"];

// 카테고리별 역량과 역량이름을 가져오는 쿼리
$Sql = "
		select 
			A.Hr_CompetencyIndicatorCate1ID,
			A.Hr_CompetencyIndicatorCate1Name,
			ifnull(B.Hr_CompetencyIndicatorCate2ID, 0) as Hr_CompetencyIndicatorCate2ID,
			B.Hr_CompetencyIndicatorCate2Name,
			(select count(*) from Hr_CompetencyIndicatorCate2 where Hr_CompetencyIndicatorCate1ID=A.Hr_CompetencyIndicatorCate1ID and (Hr_CompetencyIndicatorCate2State=1 or Hr_CompetencyIndicatorCate2State is null)) as Hr_CompetencyIndicatorCate2Count
		from Hr_CompetencyIndicatorCate1 A 
			left outer join Hr_CompetencyIndicatorCate2 B on A.Hr_CompetencyIndicatorCate1ID=B.Hr_CompetencyIndicatorCate1ID 
		where A.Hr_CompetencyIndicatorCate1State=1 and (B.Hr_CompetencyIndicatorCate2State=1 or B.Hr_CompetencyIndicatorCate2State is null)
		order by A.Hr_CompetencyIndicatorCate1Order asc, B.Hr_CompetencyIndicatorCate2Order asc";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



// 직무에 따른 분류
$Sql2 = "
		select 
			A.Hr_OrganTask2ID,
			A.Hr_OrganTask2Name,
			B.Hr_OrganTask1ID,
			B.Hr_OrganTask1Name,
			(select count(*) from Hr_OrganTask2 where Hr_OrganTask1ID=A.Hr_OrganTask1ID and Hr_OrganTask2State=1) as Hr_OrganTask2Count
		from Hr_OrganTask2 A 
			inner join Hr_OrganTask1 B on A.Hr_OrganTask1ID=B.Hr_OrganTask1ID 
		where B.Hr_OrganTask1State=1 and A.Hr_OrganTask2State=1
		order by B.Hr_OrganTask1Name asc, A.Hr_OrganTask2Name asc";

$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
$OrganTaskCount = 0;
while($Row2 = $Stmt2->fetch()) {
	$Hr_OrganTask2ID = $Row2["Hr_OrganTask2ID"];
	$Hr_OrganTask2Name = $Row2["Hr_OrganTask2Name"];
	$Hr_OrganTask1ID = $Row2["Hr_OrganTask1ID"];
	$Hr_OrganTask1Name = $Row2["Hr_OrganTask1Name"];
	$Hr_OrganTask2Count = $Row2["Hr_OrganTask2Count"];

	$Arr_Hr_OrganTask2ID[$OrganTaskCount+1] = $Hr_OrganTask2ID;
	$Arr_Hr_OrganTask2Name[$OrganTaskCount+1] = $Hr_OrganTask2Name;
	$Arr_Hr_OrganTask1ID[$OrganTaskCount+1] = $Hr_OrganTask1ID;
	$Arr_Hr_OrganTask1Name[$OrganTaskCount+1] = $Hr_OrganTask1Name;
	$Arr_Hr_OrganTask2Count[$OrganTaskCount+1] = $Hr_OrganTask2Count;

	$OrganTaskCount++;
}
$Stmt2 = null;

?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$직무별_역량관리[$LangID]?></h3>


		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap rowspan="2" style="width:80px;">No</th>
										<th nowrap rowspan="2" style="width:150px;"><?=$역량군[$LangID]?></th>
										<th nowrap rowspan="2" style="width:150px;"><?=$역량[$LangID]?></th>
										<?
										$Old_Hr_OrganTask1ID = 0;
										for ($ii=1;$ii<=$OrganTaskCount;$ii++){
											if ($Old_Hr_OrganTask1ID!=$Arr_Hr_OrganTask1ID[$ii]){
												$Old_Hr_OrganTask1ID=$Arr_Hr_OrganTask1ID[$ii];
										?>
										<th nowrap colspan="<?=$Arr_Hr_OrganTask2Count[$ii]?>" style="border-bottom:0px;"><?=$Arr_Hr_OrganTask1Name[$ii]?></th>
										<?
											}
										}
										?>
									</tr>
									<tr>
										<?
										for ($ii=1;$ii<=$OrganTaskCount;$ii++){
										?>
										<th nowrap><?=$Arr_Hr_OrganTask2Name[$ii]?></th>
										<?
										}
										?>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									$Old_Hr_CompetencyIndicatorCate1ID = 0;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $ListCount + 1;

										$Hr_CompetencyIndicatorCate1ID = $Row["Hr_CompetencyIndicatorCate1ID"];
										$Hr_CompetencyIndicatorCate1Name = $Row["Hr_CompetencyIndicatorCate1Name"];
										$Hr_CompetencyIndicatorCate2ID = $Row["Hr_CompetencyIndicatorCate2ID"];
										$Hr_CompetencyIndicatorCate2Name = $Row["Hr_CompetencyIndicatorCate2Name"];

										$PrintSetupBtn = 1;
										if ($Hr_CompetencyIndicatorCate2ID==0){
											$Hr_CompetencyIndicatorCate2Name = "-";
											$PrintSetupBtn = 0;
										}

										$Hr_CompetencyIndicatorCate2Count = $Row["Hr_CompetencyIndicatorCate2Count"];
										if ($Hr_CompetencyIndicatorCate2Count==0){
											$Hr_CompetencyIndicatorCate2Count = 1;
										}

										$Print_Hr_CompetencyIndicatorCate1Name = 0;
										if ($Old_Hr_CompetencyIndicatorCate1ID!=$Hr_CompetencyIndicatorCate1ID){
											$Old_Hr_CompetencyIndicatorCate1ID = $Hr_CompetencyIndicatorCate1ID;
											$Print_Hr_CompetencyIndicatorCate1Name = 1;
										}



									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
										<?if ($Print_Hr_CompetencyIndicatorCate1Name==1){?>
										<td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$Hr_CompetencyIndicatorCate2Count?>"><?=$Hr_CompetencyIndicatorCate1Name?></td>
										<?}?>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_CompetencyIndicatorCate2Name?></td>
										
										<?for ($ii=1;$ii<=$OrganTaskCount;$ii++){?>
										<td class="uk-text-nowrap uk-table-td-center">
											<?
											if ($PrintSetupBtn==1){

												$Sql3 = "
														select 
															count(*) as Hr_CompetencyIndicatorTaskCount
														from Hr_CompetencyIndicatorTasks 
														where Hr_OrganTask2ID=$Arr_Hr_OrganTask2ID[$ii] and Hr_CompetencyIndicatorCate2ID=$Hr_CompetencyIndicatorCate2ID ";
												$Stmt3 = $DbConn->prepare($Sql3);
												$Stmt3->execute();
												$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
												$Row3 = $Stmt3->fetch();
												$Stmt3 = null;
												$Hr_CompetencyIndicatorTaskCount = $Row3["Hr_CompetencyIndicatorTaskCount"];
											?>
											<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:SetCompetencyIndicatorTask(<?=$Hr_CompetencyIndicatorCate2ID?>, <?=$Arr_Hr_OrganTask2ID[$ii]?>)" style="background-color:<?if ($Hr_CompetencyIndicatorTaskCount>0){?>#556BAC<?}else{?>#cccccc;<?}?>" id="SetBtn_<?=$Hr_CompetencyIndicatorCate2ID?>_<?=$Arr_Hr_OrganTask2ID[$ii]?>"><?if ($Hr_CompetencyIndicatorTaskCount>0){?>사용<?}else{?>미사용<?}?></a>
											<?
											}else{
											?>
											-
											<?
											}
											?>
										</td>
										<?}?>
									
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;
									?>


								</tbody>
							</table>
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
function SetCompetencyIndicatorTask(Hr_CompetencyIndicatorCate2ID, Hr_OrganTask2ID){

	UIkit.modal.confirm(
		'<?=$상태를_변경_하시겠습니까[$LangID]?>?', 
		function(){ 

			url = "hr_ajax_set_competency_indicator_task.php";
			//location.href = url + "?Hr_CompetencyIndicatorCate2ID="+Hr_CompetencyIndicatorCate2ID+"&Hr_OrganTask2ID="+Hr_OrganTask2ID;
			
			$.ajax(url, {
				data: {
					Hr_CompetencyIndicatorCate2ID, Hr_CompetencyIndicatorCate2ID,
					Hr_OrganTask2ID: Hr_OrganTask2ID
				},
				success: function (data) {
					SetType = data.SetType;
					if (SetType==1)	{
						document.getElementById("SetBtn_"+Hr_CompetencyIndicatorCate2ID+"_"+Hr_OrganTask2ID).style.backgroundColor = "#556BAC";
						document.getElementById("SetBtn_"+Hr_CompetencyIndicatorCate2ID+"_"+Hr_OrganTask2ID).innerHTML = "<?=$사용[$LangID]?>";
					}else{
						document.getElementById("SetBtn_"+Hr_CompetencyIndicatorCate2ID+"_"+Hr_OrganTask2ID).style.backgroundColor = "#cccccc";
						document.getElementById("SetBtn_"+Hr_CompetencyIndicatorCate2ID+"_"+Hr_OrganTask2ID).innerHTML = "<?=$미사용[$LangID]?>";
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

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>