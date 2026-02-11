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

if ($_LINK_ADMIN_LEVEL_ID_==15){
	$MainMenuID = 17;
	$SubMenuID = 1718;
}else{
	$MainMenuID = 11;
	$SubMenuID = 1108;
}



include_once('./inc_top.php'); 
include_once('./inc_menu_left.php');



function SecToMin($Sec){
	$TempMin = floor($Sec/60).":". substr("0".($Sec % 60),-2);
	return $TempMin;
}

$DisplayLateSec = 60;//화면에 표시할 늦은 시간(초)
$PenaltyLateSec = 120;//사유를 적어야할 늦은 시간(초)
$PenaltyPerMin = 10;//10페소

$EduCenterID = 1;
$SearchType = isset($_REQUEST["SearchType"]) ? $_REQUEST["SearchType"] : "";

$SearchStartYear = isset($_REQUEST["SearchStartYear"]) ? $_REQUEST["SearchStartYear"] : "";
$SearchStartMonth = isset($_REQUEST["SearchStartMonth"]) ? $_REQUEST["SearchStartMonth"] : "";
$SearchStartDay = isset($_REQUEST["SearchStartDay"]) ? $_REQUEST["SearchStartDay"] : "";

$SearchEndYear = isset($_REQUEST["SearchEndYear"]) ? $_REQUEST["SearchEndYear"] : "";
$SearchEndMonth = isset($_REQUEST["SearchEndMonth"]) ? $_REQUEST["SearchEndMonth"] : "";
$SearchEndDay = isset($_REQUEST["SearchEndDay"]) ? $_REQUEST["SearchEndDay"] : "";

$SearchTeacherID = isset($_REQUEST["SearchTeacherID"]) ? $_REQUEST["SearchTeacherID"] : "";


if ($SearchType==""){
	$SearchType = "1";
}


$AddTeacherWhere = "";
if ($SearchTeacherID!=""){
	$AddTeacherWhere = " and A.TeacherID=".$SearchTeacherID." ";
}
if ($_LINK_ADMIN_LEVEL_ID_==15){
	$AddTeacherWhere = " and A.TeacherID=".$_LINK_ADMIN_TEACHER_ID_." ";
}





if ($SearchStartYear==""){
	$SearchStartYear = date("Y");
}
if ($SearchStartMonth==""){
	$SearchStartMonth = date("m");
}
if ($SearchStartDay==""){
	$SearchStartDay = date("d");
}

if ($SearchEndYear==""){
	$SearchEndYear = date("Y");
}
if ($SearchEndMonth==""){
	$SearchEndMonth = date("m");
}
if ($SearchEndDay==""){
	$SearchEndDay = date("d");
}

?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom">출석현황(Status of Class Attendance)</h3>

		<form name="SearchForm" method="get">
		<input type="hidden" name="SearchType" value="1">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">

				<div class="uk-grid" data-uk-grid-margin="">
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchStartYear" name="SearchStartYear" class="uk-width-1-1" data-placeholder="년도선택" style="width:100%;height:40px;"/>
							<option value="">년도선택</option>
							<?
							for ($iiii=2019;$iiii<=$SearchStartYear+1;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchStartYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchStartMonth" name="SearchStartMonth" class="uk-width-1-1" onchange="ChSearchStartMonth(1, this.value);" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;height:40px;"/>
							<option value="">월선택</option>
							<?
							for ($iiii=1;$iiii<=12;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchStartMonth==$iiii){?>selected<?}?>><?=$iiii?> 월</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchStartDay" name="SearchStartDay" class="uk-width-1-1" data-placeholder="일선택" style="width:100%;height:40px;"/>
							<option value="">일선택</option>
						</select>
					</div>

					<!-- <div class="uk-width-medium-5-10"></div> --> <span style="padding-top: 15px; ">~</span>
					<div class="uk-width-medium-1-10" style="padding-top:7px; ">
						<select id="SearchEndYear" name="SearchEndYear" class="uk-width-1-1" data-placeholder="년도선택" style="width:100%;height:40px;"/>
							<option value="">년도선택</option>
							<?
							for ($iiii=2019;$iiii<=$SearchEndYear;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchEndYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchEndMonth" name="SearchEndMonth" class="uk-width-1-1" onchange="ChSearchStartMonth(2, this.value);" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;height:40px;"/>
							<option value="">월선택</option>
							<?
							for ($iiii=1;$iiii<=12;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchEndMonth==$iiii){?>selected<?}?>><?=$iiii?> 월</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchEndDay" name="SearchEndDay" class="uk-width-1-1" data-placeholder="일선택" style="width:100%;height:40px;"/>
							<option value="">일선택</option>
						</select>
					</div>
				</div>

				<div class="uk-grid" data-uk-grid-margin="">
					<div class="uk-width-medium-1-10" style="padding-top:7px;display:<?if ($_LINK_ADMIN_LEVEL_ID_==15){?>none<?}?>;">
						<select id="SearchTeacherID" name="SearchTeacherID" class="uk-width-1-1" style="width:100%;height:40px;"/>
							<option value="">전체강사</option>
							<?
							$Sql = "select 
											A.*
									from Teachers A 
										inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
									where 
										A.TeacherState=1
										and A.TeacherView=1
										and B.EduCenterID=$EduCenterID 
										and B.TeacherGroupState<>0 
										and A.TeacherState<>0 
									order by A.TeacherName asc";

						
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);

							while($Row = $Stmt->fetch()) {
							?>
							<option value="<?=$Row["TeacherID"]?>" <?if ($SearchTeacherID==$Row["TeacherID"]){?>selected<?}?>><?=$Row["TeacherName"]?></option>
							<?
							}
							$Stmt = null;
							?>
						</select>
					</div>

					<?if ($_LINK_ADMIN_LEVEL_ID_==15){?>
					<div class="uk-width-medium-4-10 uk-text-center" >
					</div>
					<?}else{?>
					<div class="uk-width-medium-2-10 uk-text-center" style="text-align: right;">
					</div>
					<?}?>

					<?if ($_LINK_ADMIN_LEVEL_ID_==15){?>
					<div class="uk-width-medium-1-10 uk-text-center" >
						<a type="button" href="javascript:SearchSubmit(1)" class="md-btn md-btn-primary uk-margin-small-top three">Search</a>
					</div>
					<?}else{?>
					<div class="uk-width-medium-4-10 uk-text-center" style="text-align: right;">
						<a type="button" href="javascript:SearchSubmit(1)" class="md-btn md-btn-primary uk-margin-small-top three">강사별</a>
						<a type="button" href="javascript:SearchSubmit(2)" class="md-btn md-btn-primary uk-margin-small-top three">날짜별</a>
						<a type="button" href="javascript:SearchSubmit(3)" class="md-btn md-btn-primary uk-margin-small-top three">강사-날짜별</a>
						<a type="button" href="javascript:SearchSubmit(4)" class="md-btn md-btn-primary uk-margin-small-top three">그래프</a>
						<a type="button" href="javascript:DownloadPerDate()" class="md-btn md-btn-primary uk-margin-small-top two" style="display: inline-block">엑셀 다운로드</a>
					</div>
					<?}?>
				</div>
					
			</div>
		</div>
		</form>

		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						
<!-- ====================================================================== -->
<?if ($SearchType=="1") {//============================================================================================ 강사별 ?>

	<?
	$StartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth,-2) . "-" . substr("0".$SearchStartDay,-2);
	$EndDate = $SearchEndYear . "-" . substr("0".$SearchEndMonth,-2) . "-" . substr("0".$SearchEndDay,-2);


	//강사목록 검색
	$Sql = "select 
					A.*
			from Teachers A 
				inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
			where 
				A.TeacherState=1
				and A.TeacherView=1
				and B.EduCenterID=$EduCenterID 
				and B.TeacherGroupState<>0 
				and A.TeacherState<>0 
				".$AddTeacherWhere."
			order by A.TeacherName asc";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);


	$ArrTeacherID = [];
	$ArrTeacherName = [];
	$ArrData = [];

	//============  강사 루프 ===============
	$TeacherNum=0;
	while($Row = $Stmt->fetch()) {
		$ArrTeacherID[$TeacherNum] = $Row["TeacherID"];
		$ArrTeacherName[$TeacherNum] = $Row["TeacherName"];

		$TeacherNum++;
	}
	//============  강사 루프 ===============
	$Stmt = null;
	//강사목록 검색
	?>

<div class="uk-overflow-container">
<table class="uk-table uk-table-align-vertical">
	<thead>
		<tr>
			<?
			for ($ii=0;$ii<=$TeacherNum-1;$ii++){
			?>
				<th nowrap style="border-bottom:0px;font-weight:bold;color:#0055AA;font-size:18px;" id="DivTeacherName_<?=$ii?>"><?=$ArrTeacherName[$ii]?></th>
			<? 
			}
			?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<?
			
			for ($ii=0;$ii<=$TeacherNum-1;$ii++){
				$PenaltyTeacher[$ii] = 0;
			?>		
				<td style="vertical-align: top; height:200px;">
					<div style="height:600px;overflow-y:scroll;">
					<table class="uk-table uk-table-align-vertical">
						<thead>
							<tr>
								<th style="white-space: nowrap;">날짜<br>(date)</th>
								<th style="white-space: nowrap;">규정출석시간<br>(start work time)</th>
								<th style="white-space: nowrap;">수업시간<br>(Class minute)</th>
								<th style="white-space: nowrap;">실제출석시간<br>(Actual enter time)</th>
								<th style="white-space: nowrap;">경과시간<br>(elapsed time)</th>
								<th style="white-space: nowrap;">벌점시간<br>(penalty time)</th>
								<th style="white-space: nowrap;">벌점<br>(Penalty)</th>
								<th style="white-space: nowrap;">지각사유<br>(Reason)</th>
								<th style="white-space: nowrap;">관리자답변<br>(Answer)</th>
								<th style="white-space: nowrap;">벌점삭제<br>(Delete)</th>
							</tr>
						</thead>
						<tbody>
							<?
							$Sql = " select 
											count(distinct A.ClassDate) as DateCount
										from ClassTeacherEnters A 
										where 
											A.TeacherID=".$ArrTeacherID[$ii]."
											and datediff(A.ClassDate,'".$StartDate."')>=0 
											and datediff(A.ClassDate,'".$EndDate."')<=0 
											and timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime)>".$DisplayLateSec." 
										";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Row = $Stmt->fetch();
							$Stmt = null;
							$DateCount = $Row["DateCount"];

							$Sql = " select 
											A.*,
											timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime) as AttendDiff
										from ClassTeacherEnters A 
										where 
											A.TeacherID=".$ArrTeacherID[$ii]."
											and datediff(A.ClassDate,'".$StartDate."')>=0 
											and datediff(A.ClassDate,'".$EndDate."')<=0 
											and timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime)>".$DisplayLateSec." 
										order by A.ClassDate asc, A.ClassStartDateTime asc";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();

							$TeacherLateCount = 0;
							$TeacherLateSecSum = 0;
							$TeacherPenaltySecSum = 0;
							$PenaltyPerMinSum = 0;
							
							while($Row = $Stmt->fetch() ) {
								$ClassTeacherEnterID = $Row["ClassTeacherEnterID"];
								$ClassStartDateTime = $Row["ClassStartDateTime"]; // 출근해야할 시간
								$ClassEnterDateTime = $Row["ClassEnterDateTime"]; // 출근 시간
								$ClassDate = $Row["ClassDate"];
								$AttendDiff = $Row["AttendDiff"];
								$ClassRunMinute = $Row["ClassRunMinute"];

								$ClassEnterLateReason = $Row["ClassEnterLateReason"];
								$ClassEnterLateReasonAnswer = $Row["ClassEnterLateReasonAnswer"];
								$ClassEnterLateReasonConfirm = $Row["ClassEnterLateReasonConfirm"];

								$TempClassStartDate = date('Y-m-d', strtotime($ClassStartDateTime));
								$TempClassStartDateTime = date('H:i', strtotime($ClassStartDateTime));
								if ($ClassEnterDateTime==null){
									$TempAttendDiff = 0;
									$TempClassEnterDateTime = "결석";
								}else{
									$TempAttendDiff = $AttendDiff;
									$TempClassEnterDateTime = date('H:i:s', strtotime($ClassEnterDateTime));
								}
								
								if ($ClassEnterDateTime!=null){//결석은 제외한다.
									$TeacherLateCount++;
									$TeacherLateSecSum = $TeacherLateSecSum + $TempAttendDiff;

									$Penalty = 0;
									if ($AttendDiff>$PenaltyLateSec && $ClassEnterLateReasonConfirm==0) {//벌점주는 시간보다 더 늦어지고 삭제가 아니면 
										
										$Penalty = $PenaltyPerMin * ceil(($AttendDiff-$PenaltyLateSec)/60);
										$TeacherPenaltySecSum = $TeacherPenaltySecSum + $AttendDiff;//누적벌점 시간
										$PenaltyPerMinSum = $PenaltyPerMinSum + $Penalty;//누적벌점
									}

									if ($AttendDiff>$PenaltyLateSec){//벌점 대상이 있으면(삭제 무시)
										$PenaltyTeacher[$ii] = 1;
									}

									// 지각사유는 내용||데이트타임 으로 설정되며
									// 사유를 적을 때마다 누적된다.
									if ( strpos($ClassEnterLateReason, "|||")!==false) { // 사유가 여러개가 있을 때
										// 포함
										$ArrClassEnterLateReason = explode("|||", $ClassEnterLateReason);
										$NewClassEnterLateReason = explode("||", $ArrClassEnterLateReason[0]); // 사유을 내용|데이트타임으로 나눔
										$ClassEnterLateReason = $NewClassEnterLateReason[0];
									} else { // 사유가 1개일 때
										$NewClassEnterLateReason = explode("||", $ClassEnterLateReason); // 사유을 내용|데이트타임으로 나눔
										$ClassEnterLateReason = $NewClassEnterLateReason[0];
									}
							?>
							<tr>
								<td style="white-space: nowrap;"><?=$TempClassStartDate?></td>
								<td style="white-space: nowrap;"><?=$TempClassStartDateTime?></td>
								<td style="white-space: nowrap;"><?=$ClassRunMinute?></td>
								<td style="white-space: nowrap;"><?=$TempClassEnterDateTime?></td>
								<td style="white-space: nowrap;"><?=SecToMin( $TempAttendDiff )?></td>

								<td style="white-space: nowrap; text-align:center;">
									<?if ($TempAttendDiff > $PenaltyLateSec && $ClassEnterLateReasonConfirm==0){?>
										<?=SecToMin( $TempAttendDiff )?>
									<?}else{?>
										-
									<?}?>
								</td>
								<td style="white-space: nowrap; text-align:center;">
									<?if ($TempAttendDiff > $PenaltyLateSec && $ClassEnterLateReasonConfirm==0){?>
										<?=$Penalty?>
									<?}else{?>
										-
									<?}?>
								</td>
								<td style=" text-align:center; vetial-align:top;">
									<?if ($TempAttendDiff > $PenaltyLateSec){?>
										<div style="text-align:left;<?if ($ClassEnterLateReason!=""){?>width:350px;<?}?>">
											<?if ($_LINK_ADMIN_LEVEL_ID_<=4){?>
												<a href="javascript:OpenLateReasonList(<?=$ClassTeacherEnterID?>);"><?=$ClassEnterLateReason?></a>
											<? } else { ?>
												<?=$ClassEnterLateReason?>
											<? } ?>
										</div>
										<?if ($_LINK_ADMIN_LEVEL_ID_==15 ){?>
											<div style="text-align:right;"><i title="teasdasdasdst" id="test1" class="material-icons" style="border:1px solid #cccccc;background-color:#f1f1f1;border-radius:3px;cursor:pointer;" onclick="OpenTeacherLateReasonForm(<?=$ClassTeacherEnterID?>,1);">create</i></div>
										<?}else{?>
											<div style="text-align:right;height:20px;"></div>
										<?}?>
									<?}else{?>
										-
									<?}?>
								</td>
								<td style=" text-align:center; vetial-align:top;">
									<?if ($TempAttendDiff > $PenaltyLateSec){?>
										<div style="text-align:left;<?if ($ClassEnterLateReasonAnswer!=""){?>width:350px;<?}?>"><?=$ClassEnterLateReasonAnswer?></div>
										<?if ($_LINK_ADMIN_LEVEL_ID_<=4){?>
											<div style="text-align:right;"><i class="material-icons" style="border:1px solid #cccccc;background-color:#f1f1f1;border-radius:3px;cursor:pointer;" onclick="OpenTeacherLateReasonForm(<?=$ClassTeacherEnterID?>,2);">create</i></div>
										<?}else{?>
											<div style="text-align:right;height:20px;"></div>
										<?}?>
									<?}else{?>
										-
									<?}?>
								</td>
								<td style="white-space: nowrap; text-align:center;">
									<?if ($TempAttendDiff > $PenaltyLateSec){?>
										<?
										if ($_LINK_ADMIN_LEVEL_ID_>4){
										
											if ($ClassEnterLateReasonConfirm==0){
												$StrClassEnterLateReasonConfirm = "-";
											}else if ($ClassEnterLateReasonConfirm==1){
												$StrClassEnterLateReasonConfirm = "삭제(deleted)";
											}

										?>
											<?=$StrClassEnterLateReasonConfirm?>
										<?
										}else{
										?>
											<input type="radio" id="ClassEnterLateReasonConfirm_<?=$ClassTeacherEnterID?>_0" onclick="Ch_Confirm(<?=$ClassTeacherEnterID?>, 0, <?=$ClassEnterLateReasonConfirm?>);" class="radio_input" name="ClassEnterLateReasonConfirm_<?=$ClassTeacherEnterID?>" <?php if ($ClassEnterLateReasonConfirm==0) { echo "checked";}?> value="0">
											<label for="ClassEnterLateReasonConfirm_<?=$ClassTeacherEnterID?>_0" class="radio_label"><span class="radio_bullet"></span>유지</label>
											&nbsp; &nbsp;
											<input type="radio" id="ClassEnterLateReasonConfirm_<?=$ClassTeacherEnterID?>_1" onclick="Ch_Confirm(<?=$ClassTeacherEnterID?>, 1, <?=$ClassEnterLateReasonConfirm?>);" class="radio_input" name="ClassEnterLateReasonConfirm_<?=$ClassTeacherEnterID?>" <?php if ($ClassEnterLateReasonConfirm==1) { echo "checked";}?> value="1">
											<label for="ClassEnterLateReasonConfirm_<?=$ClassTeacherEnterID?>_1" class="radio_label"><span class="radio_bullet"></span>삭제</label>
										<?
										}
										?>
									<?}else{?>
										-
									<?}?>

								
								</td>
							</tr>
							<?
								}
							}
							$Stmt = null;
							
							if ($TeacherLateCount==0){
							?>
							<tr>
								<td style="white-space: nowrap;" colspan="8"></td>
							</tr>
							<?
							}
							?>
							<tr>
								<th style="white-space: nowrap;" colspan="4">지각횟수<br>(Number of lateness)</th>
								<th style="white-space: nowrap;"><?=$TeacherLateCount?></th>
								<th style="white-space: nowrap;" colspan="5"></th>
							</tr>
							<tr>
								<th style="white-space: nowrap;" colspan="4">지각평균<br>(average of lateness, Number/Day)</th>
								<th style="white-space: nowrap;">
									<?if ($DateCount>0){?>		
										<?=round($TeacherLateCount/$DateCount,2)?>
									<?}else{?>
										0
									<?}?>
								</th>
								<th style="white-space: nowrap;" colspan="5"></th>
							</tr>
							<tr>
								<th style="white-space: nowrap;" colspan="4">지각누적(분)<br>(total late minutes)</th>
								<th style="white-space: nowrap;">
									<?=SecToMin( $TeacherLateSecSum )?>
								</th>
								<!--
								<th style="white-space: nowrap;">누적 벌점 시간(분)<br>(total penalty minutes)</th>
								<th style="white-space: nowrap;"><?=SecToMin( $TeacherPenaltySecSum )?></th>
								누적 벌점 시간은 초까지 합산되어 더 헥깔리므로 안보여주는게 좋을것 같음.
								-->
								<th style="white-space: nowrap;" colspan="5"></th>
							</tr>
							<tr>
								<th style="white-space: nowrap;" colspan="4">지각평균(분)<br>(average late minutes)</th>
								<th style="white-space: nowrap;">
									<?if ($TeacherLateCount>0){?>
										<?=SecToMin( $TeacherLateSecSum/$TeacherLateCount )?>
									<?}else{?>
										0
									<?}?>								
								</th>
								<th style="white-space: nowrap;">벌점 합계<br>(total penalty)</th>
								<th style="white-space: nowrap;"><?=number_format($PenaltyPerMinSum,0)?></th>
								<th style="white-space: nowrap;" colspan="3"></th>
							</tr>
						</tbody>
					</table>
					</div>

				</td>
			<?
			}
			?>
		</tr>
	</tbody>
	<thead>
		<tr>
			<?
			for ($ii=0;$ii<=$TeacherNum-1;$ii++){
			?>
				<th nowrap style="font-weight:bold;color:#0055AA;font-size:18px;color:<?if ($PenaltyTeacher[$ii]==1){?>#ff0000<?}?>;"><?=$ArrTeacherName[$ii]?></th>

				<?
				if ($PenaltyTeacher[$ii]==1){
				?>
				<script>
				document.getElementById("DivTeacherName_<?=$ii?>").style.color = "#ff0000";
				</script>
				<?
				}
				?>
			<? 
			}
			?>
		</tr>
	</thead>
</table>
</div>


<?}else if ($SearchType=="2") {//============================================================================================ 날짜별 ?>


	<?
	$StartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth,-2) . "-" . substr("0".$SearchStartDay,-2);
	$EndDate = $SearchEndYear . "-" . substr("0".$SearchEndMonth,-2) . "-" . substr("0".$SearchEndDay,-2);

	//============  날짜 루프 ===============
	$EndLoop = 0;
	$DateNum = 0;
	while ($EndLoop==0){
		$NowDate =  date('Y-m-d', strtotime($StartDate. ' +'.$DateNum.' day'));
		$ArrDate[$DateNum] = $NowDate;

		if ($EndDate==$NowDate){
			$EndLoop=1;
		}

		$DateNum++;
	}
	//============  날짜 루프 ===============
	?>

<div class="uk-overflow-container">
<table class="uk-table uk-table-align-vertical">
	<thead>
		<tr>
			<?
			for ($ii=0;$ii<=$DateNum-1;$ii++){
			?>
				<th nowrap style="border-bottom:0px;font-weight:bold;color:#0055AA;font-size:18px;"><?=$ArrDate[$ii]?></th>
			<? 
			}
			?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<?
			for ($ii=0;$ii<=$DateNum-1;$ii++){
			?>		
				<td style="vertical-align: top;">

					<table class="uk-table uk-table-align-vertical">
						<thead>
							<tr>
								<th style="white-space: nowrap;">강사명<br>(teacher)</th>
								<th style="white-space: nowrap;">규정출석시간<br>(start work time)</th>
								<th style="white-space: nowrap;">수업시간<br>(Class minute)</th>
								<th style="white-space: nowrap;">실제출석시간<br>(Actual enter time)</th>
								<th style="white-space: nowrap;">경과시간<br>(elapsed time)</th>
							</tr>
						</thead>
						<tbody>
							<?
							
							$Sql = " select 
											count(distinct A.TeacherID) as TeacherCount
										from ClassTeacherEnters A 
										where 
											datediff(A.ClassDate,'".$ArrDate[$ii]."')=0 
											and timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime)>".$DisplayLateSec." 
											".$AddTeacherWhere."
										";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Row = $Stmt->fetch();
							$Stmt = null;
							$TeacherCount = $Row["TeacherCount"];


							$Sql = " select 
											A.*,
											B.MemberName,
											timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime) as AttendDiff
										from ClassTeacherEnters A 
											inner join Members B on A.TeacherID=B.TeacherID 
										where 
											datediff(A.ClassDate,'".$ArrDate[$ii]."')=0 
											and timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime)>".$DisplayLateSec." 
											".$AddTeacherWhere."
										order by A.ClassDate asc, A.ClassStartDateTime asc";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();

							$DateLateCount = 0;
							$DateLateSecSum = 0;
							while($Row = $Stmt->fetch() ) {
								$ClassStartDateTime = $Row["ClassStartDateTime"]; // 출근해야할 시간
								$ClassEnterDateTime = $Row["ClassEnterDateTime"]; // 출근 시간
								$ClassDate = $Row["ClassDate"];
								$AttendDiff = $Row["AttendDiff"];
								$ClassRunMinute = $Row["ClassRunMinute"];
								$MemberName = $Row["MemberName"];

								$TempClassStartDateTime = date('H:i', strtotime($ClassStartDateTime));
								if ($ClassEnterDateTime==null){
									$TempAttendDiff = 0;
									$TempClassEnterDateTime = "결석";
								}else{
									$TempAttendDiff = $AttendDiff;
									$TempClassEnterDateTime = date('H:i:s', strtotime($ClassEnterDateTime));
								}
								
								if ($ClassEnterDateTime!=null){//결석은 제외한다.
									$DateLateCount++;
									$DateLateSecSum = $DateLateSecSum + $TempAttendDiff;
							?>
							<tr>
								<td style="white-space: nowrap;"><?=$MemberName?></td>
								<td style="white-space: nowrap;"><?=$TempClassStartDateTime?></td>
								<td style="white-space: nowrap;"><?=$ClassRunMinute?></td>
								<td style="white-space: nowrap;"><?=$TempClassEnterDateTime?></td>
								<td style="white-space: nowrap;"><?=SecToMin( $TempAttendDiff )?></td>
							</tr>
							<?
								}
							}
							$Stmt = null;
							
							if ($DateLateCount==0){
							?>
							<tr>
								<td style="white-space: nowrap;" colspan="5"></td>
							</tr>
							<?
							}
							?>
							<tr>
								<th style="white-space: nowrap;" colspan="4">지각횟수<br>(Number of lateness)</th>
								<th style="white-space: nowrap;"><?=$DateLateCount?></th>
							</tr>

							<tr>
								<th style="white-space: nowrap;" colspan="4">지각평균<br>(average of lateness, Number/Teacher)</th>
								<th style="white-space: nowrap;">
									<?if ($TeacherCount>0){?>		
										<?=round($DateLateCount/$TeacherCount,2)?> 
									<?}else{?>
										0
									<?}?>
								</th>
							</tr>


							<tr>
								<th style="white-space: nowrap;" colspan="4">지각누적(분)<br>(total late minutes)</th>
								<th style="white-space: nowrap;">
									<?=SecToMin( $DateLateSecSum )?>
								</th>
							</tr>
							<tr>
								<th style="white-space: nowrap;" colspan="4">지각평균(분)<br>(average late minutes)</th>
								<th style="white-space: nowrap;">
									<?if ($DateLateCount>0){?>
										<?=SecToMin( $DateLateSecSum/$DateLateCount )?>
									<?}else{?>
										0
									<?}?>								
								</th>
							</tr>
						</tbody>
					</table>

				</td>
			<?
			}
			?>
		</tr>
	</tbody>
	<thead>
		<tr>
			<?
			for ($ii=0;$ii<=$DateNum-1;$ii++){
			?>
				<th nowrap style="font-weight:bold;color:#0055AA;font-size:18px;"><?=$ArrDate[$ii]?></th>
			<? 
			}
			?>
		</tr>
	</thead>
</table>
</div>

<?}else if ($SearchType=="3") {?>


	<?
	$StartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth,-2) . "-" . substr("0".$SearchStartDay,-2);
	$EndDate = $SearchEndYear . "-" . substr("0".$SearchEndMonth,-2) . "-" . substr("0".$SearchEndDay,-2);


	//강사목록 검색
	$Sql = "select 
					A.*
			from Teachers A 
				inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
			where 
				A.TeacherState=1
				and A.TeacherView=1
				and B.EduCenterID=$EduCenterID 
				and B.TeacherGroupState<>0 
				and A.TeacherState<>0 
				".$AddTeacherWhere."
			order by A.TeacherName asc";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);


	$ArrTeacherID = [];
	$ArrTeacherName = [];
	$ArrData = [];

	//============  강사 루프 ===============
	$TeacherNum=0;
	while($Row = $Stmt->fetch()) {
		$ArrTeacherID[$TeacherNum] = $Row["TeacherID"];
		$ArrTeacherName[$TeacherNum] = $Row["TeacherName"];

		$TeacherNum++;
	}
	//============  강사 루프 ===============
	$Stmt = null;
	//강사목록 검색



	//============  날짜 루프 ===============
	$EndLoop = 0;
	$DateNum = 0;
	while ($EndLoop==0){
		$NowDate =  date('Y-m-d', strtotime($StartDate. ' +'.$DateNum.' day'));
		$ArrDate[$DateNum] = $NowDate;

		if ($EndDate==$NowDate){
			$EndLoop=1;
		}

		$DateNum++;
	}
	//============  날짜 루프 ===============
	?>

	<?
	for ($ii=0;$ii<=$TeacherNum-1;$ii++){
	?>
	<div class="uk-overflow-container">
	<table class="uk-table uk-table-align-vertical" style="margin-bottom:30px;">
		<thead>
			<tr>
			<?
			for ($jj=0;$jj<=$DateNum-1;$jj++){
			?>
				<th nowrap style="border-bottom:0px;font-weight:bold;color:#0055AA;font-size:18px;"><?=$ArrTeacherName[$ii]?> - <?=$ArrDate[$jj]?></th>
			<? 
			}
			?>
			</tr>
		</thead>
		<tbody>
			<tr>
			<?
			for ($jj=0;$jj<=$DateNum-1;$jj++){
			?>		
				<td style="vertical-align: top;">

					<table class="uk-table uk-table-align-vertical">
						<thead>
							<tr>
								<th style="white-space: nowrap;">규정출석시간<br>(start work time)</th>
								<th style="white-space: nowrap;">수업시간<br>(Class minute)</th>
								<th style="white-space: nowrap;">실제출석시간<br>(Actual enter time)</th>
								<th style="white-space: nowrap;">경과시간<br>(elapsed time)</th>
							</tr>
						</thead>
						<tbody>
							<?
							$Sql = " select 
											A.*,
											B.MemberName,
											timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime) as AttendDiff
										from ClassTeacherEnters A 
											inner join Members B on A.TeacherID=B.TeacherID 
										where 
											A.TeacherID=".$ArrTeacherID[$ii]." 
											and datediff(A.ClassDate,'".$ArrDate[$jj]."')=0 
											and timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime)>".$DisplayLateSec." 
										order by A.ClassDate asc, A.ClassStartDateTime asc";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();

							$DateLateCount = 0;
							$DateLateSecSum = 0;
							while($Row = $Stmt->fetch() ) {
								$ClassStartDateTime = $Row["ClassStartDateTime"]; // 출근해야할 시간
								$ClassEnterDateTime = $Row["ClassEnterDateTime"]; // 출근 시간
								$ClassDate = $Row["ClassDate"];
								$AttendDiff = $Row["AttendDiff"];
								$ClassRunMinute = $Row["ClassRunMinute"];
								$MemberName = $Row["MemberName"];

								$TempClassStartDateTime = date('H:i', strtotime($ClassStartDateTime));
								if ($ClassEnterDateTime==null){
									$TempAttendDiff = 0;
									$TempClassEnterDateTime = "결석";
								}else{
									$TempAttendDiff = $AttendDiff;
									$TempClassEnterDateTime = date('H:i:s', strtotime($ClassEnterDateTime));
								}
								
								if ($ClassEnterDateTime!=null){//결석은 제외한다.
									$DateLateCount++;
									$DateLateSecSum = $DateLateSecSum + $TempAttendDiff;
							?>
							<tr>
								<td style="white-space: nowrap;"><?=$TempClassStartDateTime?></td>
								<td style="white-space: nowrap;"><?=$ClassRunMinute?></td>
								<td style="white-space: nowrap;"><?=$TempClassEnterDateTime?></td>
								<td style="white-space: nowrap;"><?=SecToMin( $TempAttendDiff )?></td>
							</tr>
							<?
								}
							}
							$Stmt = null;
							
							if ($DateLateCount==0){
							?>
							<tr>
								<td style="white-space: nowrap;" colspan="4"></td>
							</tr>
							<?
							}
							?>
							<tr>
								<th style="white-space: nowrap;" colspan="3">지각횟수<br>(Number of lateness)</th>
								<th style="white-space: nowrap;"><?=$DateLateCount?></th>
							</tr>
							<tr>
								<th style="white-space: nowrap;" colspan="3">지각누적(분)<br>(total late minutes)</th>
								<th style="white-space: nowrap;">
									<?=SecToMin( $DateLateSecSum )?>
								</th>
							</tr>
							<tr>
								<th style="white-space: nowrap;" colspan="3">지각평균(분)<br>(average late minutes)</th>
								<th style="white-space: nowrap;">
									<?if ($DateLateCount>0){?>
										<?=SecToMin( $DateLateSecSum/$DateLateCount )?>
									<?}else{?>
										0
									<?}?>								
								</th>
							</tr>
						</tbody>
					</table>

				</td>
			<?
			}
			?>
			</tr>
		</tbody>
		<thead>
			<tr>
			<?
			for ($jj=0;$jj<=$DateNum-1;$jj++){
			?>
				<th nowrap style="font-weight:bold;color:#0055AA;font-size:18px;"><?=$ArrTeacherName[$ii]?> - <?=$ArrDate[$jj]?></th>
			<? 
			}
			?>
			</tr>

		</thead>
	</table>
	</div>

	<? 
	}
	?>

<?}else if ($SearchType=="4") {//============================================================================================ 날짜별 ?>

	<?
	$StartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth,-2) . "-" . substr("0".$SearchStartDay,-2);
	$EndDate = $SearchEndYear . "-" . substr("0".$SearchEndMonth,-2) . "-" . substr("0".$SearchEndDay,-2);


	//강사목록 검색
	$Sql = "select 
					A.*
			from Teachers A 
				inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
			where 
				A.TeacherState=1
				and A.TeacherView=1
				and B.EduCenterID=$EduCenterID 
				and B.TeacherGroupState<>0 
				and A.TeacherState<>0 
				".$AddTeacherWhere."
			order by A.TeacherName asc";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);


	$ArrTeacherID = [];
	$ArrTeacherName = [];
	$ArrData = [];
	$ArrLastCount = [];

	//============  강사 루프 ===============
	$TeacherNum=0;
	while($Row = $Stmt->fetch()) {
		$ArrTeacherID[$TeacherNum] = $Row["TeacherID"];
		$ArrTeacherName[$TeacherNum] = $Row["TeacherName"];
		$ArrLastCount[$ArrTeacherID[$TeacherNum]] = 0;
		$TeacherNum++;
	}
	//============  강사 루프 ===============
	$Stmt = null;
	//강사목록 검색



	//============  날짜 루프 ===============
	$EndLoop = 0;
	$DateNum = 0;
	while ($EndLoop==0){
		$NowDate =  date('Y-m-d', strtotime($StartDate. ' +'.$DateNum.' day'));
		$ArrDate[$DateNum] = $NowDate;

		if ($EndDate==$NowDate){
			$EndLoop=1;
		}

		$DateNum++;
	}
	//============  날짜 루프 ===============
	?>

	<?
	for ($ii=0;$ii<=$TeacherNum-1;$ii++){
		for ($jj=0;$jj<=$DateNum-1;$jj++){

			$Sql = " select 
							A.*,
							B.MemberName,
							timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime) as AttendDiff
						from ClassTeacherEnters A 
							inner join Members B on A.TeacherID=B.TeacherID 
						where 
							A.TeacherID=".$ArrTeacherID[$ii]." 
							and datediff(A.ClassDate,'".$ArrDate[$jj]."')=0 
							and timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime)>".$DisplayLateSec." 
						order by A.ClassDate asc, A.ClassStartDateTime asc";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();

			$DateLateCount = 0;
			$DateLateSecSum = 0;
			while($Row = $Stmt->fetch() ) {
				$TeacherID = $Row["TeacherID"];
				$ClassStartDateTime = $Row["ClassStartDateTime"]; // 출근해야할 시간
				$ClassEnterDateTime = $Row["ClassEnterDateTime"]; // 출근 시간
				$ClassDate = $Row["ClassDate"];
				$AttendDiff = $Row["AttendDiff"];
				$ClassRunMinute = $Row["ClassRunMinute"];
				$MemberName = $Row["MemberName"];

				$TempClassStartDateTime = date('H:i', strtotime($ClassStartDateTime));
				if ($ClassEnterDateTime==null){
					$TempAttendDiff = 0;
					$TempClassEnterDateTime = "결석";
				}else{
					$ArrLastCount[$TeacherID]++;
					$TempAttendDiff = $AttendDiff;
					$TempClassEnterDateTime = date('H:i:s', strtotime($ClassEnterDateTime));
				}
				
				if ($ClassEnterDateTime!=null){//결석은 제외한다.
					$DateLateCount++;
					$DateLateSecSum = $DateLateSecSum + $TempAttendDiff;

				}
			}
			$Stmt = null;
		}
	}
	?>

	<!-- Styles -->
	<style>
	#chartdiv {
	  width: 100%;
	  height: 500px;
	}
	</style>

	<!-- Resources -->
	<script src="https://www.amcharts.com/lib/4/core.js"></script>
	<script src="https://www.amcharts.com/lib/4/charts.js"></script>
	<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

	<!-- Chart code -->
	<script>
	am4core.ready(function() {

	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end

	var chart = am4core.create("chartdiv", am4charts.XYChart);

	var data = [];
	var open = 100;
	var close = 120;

	var names = <?=json_encode($ArrTeacherName)?>;
	var ids = <?=json_encode($ArrTeacherID);?>;
	var lastCount = <?=json_encode($ArrLastCount)?>;

	for (var i = 0; i < names.length; i++) {
	  open += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 5);
	  close = open + Math.round(Math.random() * 10) + 3;
	  //data.push({ category: names[i], open: open, close: close });
	  data.push({ category: names[i], close: lastCount[ids[i]] });
	}

	chart.data = data;
	var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
	categoryAxis.renderer.grid.template.location = 0;
	categoryAxis.dataFields.category = "category";
	categoryAxis.renderer.minGridDistance = 15;
	categoryAxis.renderer.grid.template.location = 0.5;
	categoryAxis.renderer.grid.template.strokeDasharray = "1,3";
	categoryAxis.renderer.labels.template.rotation = -90;
	categoryAxis.renderer.labels.template.horizontalCenter = "left";
	categoryAxis.renderer.labels.template.location = 0.5;
	categoryAxis.renderer.inside = true;

	categoryAxis.renderer.labels.template.adapter.add("dx", function(dx, target) {
		return -target.maxRight / 2;
	})

	var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
	valueAxis.tooltip.disabled = true;
	valueAxis.renderer.ticks.template.disabled = true;
	valueAxis.renderer.axisFills.template.disabled = true;

	var series = chart.series.push(new am4charts.ColumnSeries());
	series.dataFields.categoryX = "category";
	//series.dataFields.openValueY = "open";
	series.dataFields.valueY = "close";
	//series.tooltipText = "수업수: {openValueY.value} 지각수: {valueY.value}";
	series.tooltipText = "지각수: {valueY.value}";
	series.sequencedInterpolation = true;
	series.fillOpacity = 0;
	series.strokeOpacity = 1;
	series.columns.template.width = 0.01;
	series.tooltip.pointerOrientation = "horizontal";

	var openBullet = series.bullets.create(am4charts.CircleBullet);
	openBullet.locationY = 1;

	var closeBullet = series.bullets.create(am4charts.CircleBullet);

	closeBullet.fill = chart.colors.getIndex(4);
	closeBullet.stroke = closeBullet.fill;

	chart.cursor = new am4charts.XYCursor();

	chart.scrollbarX = new am4core.Scrollbar();
	chart.scrollbarY = new am4core.Scrollbar();


	}); // end am4core.ready()
	</script>

	<!-- HTML -->
	<div id="chartdiv"></div>
</div>
<?
}

?>
<!-- ====================================================================== -->
						
					</div>
				</div>
			</div>
		</div>



	</div>
</div>

<script>
function OpenTeacherLateReasonForm(ClassTeacherEnterID,ReasonType){
	openurl = "teacher_enter_late_reason_form.php?ClassTeacherEnterID="+ClassTeacherEnterID+"&ReasonType="+ReasonType;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "400"
		,title:""
		,iframe:true 
		,scrolling:false
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function Ch_Confirm(ClassTeacherEnterID, ClassEnterLateReasonConfirm, OldClassEnterLateReasonConfirm){
	if (confirm("변경 하시겠습니까?")){

		url = "ajax_set_class_teacher_late_delete.php";

		//location.href = url + "?ClassTeacherEnterID="+ClassTeacherEnterID+"&ClassEnterLateReasonConfirm"+ClassEnterLateReasonConfirm;
		$.ajax(url, {
			data: {
				ClassTeacherEnterID: ClassTeacherEnterID,
				ClassEnterLateReasonConfirm: ClassEnterLateReasonConfirm
			},
			success: function (data) {
				location.reload();
			},
			error: function () {

			}
		});	


	}else{
		document.getElementById("ClassEnterLateReasonConfirm_"+ClassTeacherEnterID+"_"+OldClassEnterLateReasonConfirm).checked = true;
	}
}

function ChSearchStartMonth(MonthType, MonthNumber){
	
	if (MonthType==1){
		YearNumber = document.SearchForm.SearchStartYear.value;
	}else{
		YearNumber = document.SearchForm.SearchEndYear.value;
	}
	url = "ajax_get_month_last_day.php";

	//location.href = url + "?YearNumber="+YearNumber+"&MonthNumber"+MonthNumber;
	$.ajax(url, {
		data: {
			YearNumber: YearNumber,
			MonthNumber: MonthNumber
		},
		success: function (data) {

			if (MonthType==1){

				SelBoxInitOption('SearchStartDay');

				SelBoxAddOption( 'SearchStartDay', '일선택', "", "");
				for (ii=1 ; ii<=data.LastDay ; ii++ ){
					ArrOptionText     = ii + "일";
					ArrOptionValue    = ii;

					ArrOptionSelected = "";
					if (ii==<?=(int)$SearchStartDay?>){
						ArrOptionSelected = "selected";
					}

					SelBoxAddOption( 'SearchStartDay', ArrOptionText, ArrOptionValue, ArrOptionSelected );
				}

			}else{

				SelBoxInitOption('SearchEndDay');

				SelBoxAddOption( 'SearchEndDay', '일선택', "", "");
				for (ii=1 ; ii<=data.LastDay ; ii++ ){
					ArrOptionText     = ii + "일";
					ArrOptionValue    = ii;
					
					ArrOptionSelected = "";
					if (ii==<?=(int)$SearchEndDay?>){
						ArrOptionSelected = "selected";
					}
						
					SelBoxAddOption( 'SearchEndDay', ArrOptionText, ArrOptionValue, ArrOptionSelected );
				}

			}

		},
		error: function () {

		}
	});
}


function DownloadPerDate() {
	
	SearchStartYear = document.SearchForm.SearchStartYear.value;
	SearchStartMonth = document.SearchForm.SearchStartMonth.value;
	SearchStartDay = document.SearchForm.SearchStartDay.value;
	SearchEndYear = document.SearchForm.SearchEndYear.value;
	SearchEndMonth = document.SearchForm.SearchEndMonth.value;
	SearchEndDay = document.SearchForm.SearchEndDay.value;
	SearchTeacherID = document.SearchForm.SearchTeacherID.value;

	
	location.href = "teacher_enter_excel_form_download_per_date.php?SearchStartYear="+SearchStartYear+"&SearchStartMonth="+SearchStartMonth+"&SearchStartDay="+SearchStartDay+"&SearchEndYear="+SearchEndYear+"&SearchEndMonth="+SearchEndMonth+"&SearchEndDay="+SearchEndDay+"&SearchTeacherID="+SearchTeacherID;
}


function DownloadPerTeacher() {
	//location.href = "student_list_excel_download.php";
	alert("not yet");
}


function SearchSubmit(SearchType) {
	document.SearchForm.SearchType.value = SearchType;
	document.SearchForm.action = "teacher_enter_excel_form.php";
	document.SearchForm.submit();
}

function OpenLateReasonList(ClassTeacherEnterID) {
	openurl = "teacher_enter_late_reason_list_form.php?ClassTeacherEnterID="+ClassTeacherEnterID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "600"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}
</script>



<?
include_once('./inc_common_list_js.php');
?>
<!-- ============== only this page js ============== -->


<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->




<script>
/** ===================================== 기본함수 ===================================== **/
// Option객체를 생성해서 Return
function SelBoxCreateOption( text, value, selected )
{
	var oOption = document.createElement("OPTION"); // Option 객체를 생성
	oOption.text = text; // Text(Keyword)를 입력
	oOption.value = value; // Value를 입력
	if (selected=="selected"){
		oOption.selected = true;
	}
	return oOption;
}

// SelectBox의 Option을 초기화
function SelBoxInitOption( ObjId ){
	var SelectObj = document.getElementById( ObjId );
	if ( SelectObj == null ) return; // 객체가 존재하지 않으면 취소

	SelectObj.options.length = 0; // 길이를 0으로 하면 초기화
}

// Option을 추가
function SelBoxAddOption( ObjId, text, value, selected ){
	var SelectObj = document.getElementById( ObjId );

	SelectObj.add( SelBoxCreateOption( text , value, selected ) );
	text     = "";
	value    = "";
	selected = "";
}
/** ===================================== 기본함수 ===================================== **/
</script>

<?


?>
<script>
ChSearchStartMonth(1, <?=(int)$SearchStartMonth?>);
ChSearchStartMonth(2, <?=(int)$SearchEndMonth?>);

</script>
<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');

include_once('../includes/dbclose.php');

?>
</body>
</html>