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
$SubMenuID = 1107;
include_once('./inc_top.php'); 
include_once('./inc_menu_left.php');


function SecToMin($Sec){
	$TempMin = floor($Sec/60).":". substr("0".($Sec % 60),-2);
	return $TempMin;
}


$DisplayLateSec = 120;//화면에 표시할 늦은 시간(초)
$PenaltyLateSec = 120;//사유를 적어야할 늦은 시간(초)


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

		<h3 class="heading_b uk-margin-bottom"><?=$출근현황_Status_of_Work[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<input type="hidden" name="SearchType" value="1">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					<div class="uk-width-medium-1-10" style="padding-top:7px;display:<?if ($_LINK_ADMIN_LEVEL_ID_==15){?>none<?}?>;">
						<select id="SearchTeacherID" name="SearchTeacherID" class="uk-width-1-1" style="width:100%;height:40px;"/>
							<option value=""><?=$전체강사[$LangID]?></option>
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

				</div>

				<div class="uk-grid" data-uk-grid-margin="">
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchStartYear" name="SearchStartYear" class="uk-width-1-1" data-placeholder="년도선택" style="width:100%;height:40px;"/>
							<option value=""><?=$년도선택[$LangID]?></option>
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
							<option value=""><?=$월선택[$LangID]?></option>
							<?
							for ($iiii=1;$iiii<=12;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchStartMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchStartDay" name="SearchStartDay" class="uk-width-1-1" data-placeholder="<?=$일선택[$LangID]?>" style="width:100%;height:40px;"/>
							<option value=""><?=$일선택[$LangID]?></option>
						</select>
					</div>

					<!-- <div class="uk-width-medium-5-10"></div> --> <span style="padding-top: 15px; ">~</span>
					<div class="uk-width-medium-1-10" style="padding-top:7px; ">
						<select id="SearchEndYear" name="SearchEndYear" class="uk-width-1-1" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%;height:40px;"/>
							<option value=""><?=$년도선택[$LangID]?></option>
							<?
							for ($iiii=2019;$iiii<=$SearchEndYear+1;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchEndYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchEndMonth" name="SearchEndMonth" class="uk-width-1-1" onchange="ChSearchStartMonth(2, this.value);" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;height:40px;"/>
							<option value=""><?=$월선택[$LangID]?></option>
							<?
							for ($iiii=1;$iiii<=12;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchEndMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchEndDay" name="SearchEndDay" class="uk-width-1-1" data-placeholder="일선택" style="width:100%;height:40px;"/>
							<option value=""><?=$일선택[$LangID]?></option>
						</select>
					</div>

					<div class="uk-width-medium-3-10 uk-text-center" >
						<a type="button" href="javascript:SearchSubmit(1)" class="md-btn md-btn-primary uk-margin-small-top three"><?=$강사별[$LangID]?></a>
						<a type="button" href="javascript:SearchSubmit(2)" class="md-btn md-btn-primary uk-margin-small-top three"><?=$날짜별[$LangID]?></a>
						<!-- <a type="button" href="javascript:OpenAttendGraph()" class="md-btn md-btn-primary uk-margin-small-top three">그래프<?=$SearchTeacherID?></a> -->
						<a type="button" href="javascript:SearchSubmit(3)" class="md-btn md-btn-primary uk-margin-small-top three"><?=$그래프[$LangID]?></a>
						<a type="button" href="javascript:DownloadPerDate()" class="md-btn md-btn-primary uk-margin-small-top two" style="display: inline-block"><?=$엑셀_다운로드[$LangID]?></a>
					</div>


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
	$ArrDate = [];
	$ArrData = [];

	$ArrTeacherLateCount = [];
	$ArrTeacherLateSecSum = [];
	$ArrDateLateCount = [];
	$ArrDateLateSecSum = [];

	
	//============  강사 루프 ===============
	$TeacherNum=0;
	while($Row = $Stmt->fetch()) {
		$ArrTeacherID[$TeacherNum] = $Row["TeacherID"];
		$ArrTeacherName[$TeacherNum] = $Row["TeacherName"];

		$ArrTeacherLateCount[$ArrTeacherID[$TeacherNum]] = 0;
		$ArrTeacherLateSecSum[$ArrTeacherID[$TeacherNum]] = 0;

		//============ 날짜 루프 =============
		$EndLoop = 0;
		$DateNum = 0;
		while ($EndLoop==0){
			$NowDate =  date('Y-m-d', strtotime($StartDate. ' +'.$DateNum.' day'));
			$ArrDate[$DateNum] = $NowDate;

			$ArrDateLateCount[$NowDate] = 0;
			$ArrDateLateSecSum[$NowDate] = 0;

			$ArrData[$ArrTeacherID[$TeacherNum]][$NowDate] = "||";//규정출근시간|실제출근시간|경과시간(초)
			
			if ($EndDate==$NowDate){
				$EndLoop=1;
			}
			$DateNum++;
		}
		//============ 날짜 루프 =============

		$TeacherNum++;
	}
	//============  강사 루프 ===============

	$Stmt = null;
	//강사목록 검색

	$Sql = "select 
				A.*,
				timestampdiff(second, A.TeacherAttendanceHour, A.TeacherAttendanceDateTime) as AttendDiff
			from TeacherAttendances A 
				inner join Teachers B on A.TeacherID=B.TeacherID
				inner join TeacherGroups C on C.TeacherGroupID=B.TeacherGroupID 
			where 
				B.TeacherState=1
				and B.TeacherView=1
				and C.EduCenterID=$EduCenterID 
				and C.TeacherGroupState<>0 
				and B.TeacherState<>0 
				and datediff(A.CheckDate, '".$StartDate."')>=0 
				and datediff(A.CheckDate, '".$EndDate."')<=0 
				and timestampdiff(second, A.TeacherAttendanceHour, A.TeacherAttendanceDateTime)>".$DisplayLateSec." 
				".$AddTeacherWhere." 
			order by A.CheckDate asc 
		";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	while($Row = $Stmt->fetch()) {
		
		$TeacherID = $Row["TeacherID"];
		$TeacherAttendanceHour = $Row["TeacherAttendanceHour"]; // 출근해야할 시간
		$TeacherAttendanceDateTime = $Row["TeacherAttendanceDateTime"]; // 출근 시간
		$CheckDate = $Row["CheckDate"];
		$AttendDiff = $Row["AttendDiff"];

		$TempTeacherAttendanceHour = date('H:i', strtotime($TeacherAttendanceHour));
		if($TeacherAttendanceDateTime == null) {//결근은 표시해주지 않는다.
			$TempTeacherAttendanceDateTime = "";
		} else {

			$TempTeacherAttendanceDateTime = date('H:i:s', strtotime($TeacherAttendanceDateTime));
			$ArrTeacherLateCount[$TeacherID]++;
			$ArrTeacherLateSecSum[$TeacherID] = $ArrTeacherLateSecSum[$TeacherID] + $AttendDiff;
			$ArrDateLateCount[$CheckDate]++;
			$ArrDateLateSecSum[$CheckDate] = $ArrDateLateSecSum[$CheckDate] + $AttendDiff;
			
			$ArrData[$TeacherID][$CheckDate] = $TempTeacherAttendanceHour."|".$TempTeacherAttendanceDateTime."|".$AttendDiff;//규정출근시간|실제출근시간|경과시간(초)
		}

		
	
		//echo $TeacherID.">>".$DateNum."=".$ArrData[$TeacherID][$CheckDate]."{}<br>";
	}
	?>

<div class="uk-overflow-container">
<table class="uk-table uk-table-align-vertical">
	<thead>
		<tr>
				<th style="white-space: nowrap;">-</th>
			<?
			for ($ii=0;$ii<=$TeacherNum-1;$ii++){
			?>
				<th colspan="3" nowrap style="border-bottom:0px;font-weight:bold;color:#0055AA;font-size:18px;"><?=$ArrTeacherName[$ii]?></th>
			<? 
			}
			?>
				<th nowrap rowspan="2"><?=$합계[$LangID]?><br>(total)</th>
		</tr>
		<tr>
				<th style="white-space: nowrap;"><?=$날짜[$LangID]?><br>(date)</th>
			<?
			for ($ii=0;$ii<=$TeacherNum-1;$ii++){
			?>
				
				<th style="white-space: nowrap;"><?=$규정출근시간[$LangID]?><br>(start work time)</th>
				<th style="white-space: nowrap;"><?=$실제출근시간[$LangID]?><br>(Actual attend time)</th>
				<th style="white-space: nowrap;"><?=$경과시간[$LangID]?><br>(elapsed time)</th>
			<? 
			}
			?>
		</tr>
	</thead>
	<tbody>
		
		<?
		for($jj=0; $jj<=$DateNum-1;$jj++) {
		?>
		<tr>
				<th style="white-space: nowrap;"><?=$ArrDate[$jj]?></th>
		<?
			for($ii=0; $ii<=$TeacherNum-1;$ii++) {
				
				//echo $ArrTeacherID[$ii].">>".$jj."=".$ArrData[$ArrTeacherID[$ii]][$ArrDate[$jj]]."()<br>";

				$ArrStrData = explode("|", $ArrData[$ArrTeacherID[$ii]][$ArrDate[$jj]]);
				$StrAttendTime = $ArrStrData[0];
				$StrRealAttendTime = $ArrStrData[1];
				$StrRunTime = $ArrStrData[2];
		
		?>
			
			<td style="white-space: nowrap;"><?=$StrAttendTime?></td>
			<td style="white-space: nowrap;"><?=$StrRealAttendTime?></td>
			<td style="white-space: nowrap;">
				<?if ($StrRunTime!=""){?>
					<?=SecToMin( $StrRunTime )?>
				<?}?>
			</td>
		<?
			}
		?>
			<th><?=SecToMin( $ArrDateLateSecSum[$ArrDate[$jj]] )?></th>
		</tr>
		<?
		}
		?>
		</tr>

		<tr>
				<th style="white-space: nowrap;">-</th>
			<?
			for ($ii=0;$ii<=$TeacherNum-1;$ii++){
			?>
				<th colspan="3" nowrap style="border-bottom:0px;font-weight:bold;color:#0055AA;font-size:18px;"><?=$ArrTeacherName[$ii]?></th>
			<? 
			}
			?>
				<th nowrap><?=$합계[$LangID]?><br>(total)</th>
		</tr>

		<tr>
				<th style="white-space: nowrap;"><?=$지각횟수[$LangID]?><br>(Number of lateness)</th>
			<?
			$TotLateCount = 0;
			for ($ii=0;$ii<=$TeacherNum-1;$ii++){
				$TotLateCount = $TotLateCount + $ArrTeacherLateCount[$ArrTeacherID[$ii]];
			?>
				<th colspan="3" nowrap style="border-bottom:0px;"><?=$ArrTeacherLateCount[$ArrTeacherID[$ii]]?></th>
			<? 
			}
			?>
				<th nowrap><?=$TotLateCount?></th>
		</tr>
		<tr>
				<th style="white-space: nowrap;"><?=$지각누적_분[$LangID]?><br>(total late minutes)</th>
			<?
			$TotLateSec = 0;
			for ($ii=0;$ii<=$TeacherNum-1;$ii++){
				$TotLateSec = $TotLateSec + $ArrTeacherLateSecSum[$ArrTeacherID[$ii]];
			?>
				<th colspan="3" nowrap style="border-bottom:0px;"><?=SecToMin( $ArrTeacherLateSecSum[$ArrTeacherID[$ii]] )?></th>
			<? 
			}
			?>
				<th nowrap><?=SecToMin( $TotLateSec )?></th>
		</tr>
		<tr>
				<th style="white-space: nowrap;"><?=$지각평균_분[$LangID]?><br>(average late minutes)</th>
			<?
			for ($ii=0;$ii<=$TeacherNum-1;$ii++){
				$TotLateCount = $TotLateCount + $ArrTeacherLateCount[$ArrTeacherID[$ii]];
			?>
				<th colspan="3" nowrap style="">
					<?if ($ArrTeacherLateCount[$ArrTeacherID[$ii]]>0){ ?>
						<?=SecToMin( $ArrTeacherLateSecSum[$ArrTeacherID[$ii]] / $ArrTeacherLateCount[$ArrTeacherID[$ii]] )?>
					<?}else{?>
						0
					<?}?>
				</th>
			<? 
			}
			?>
				<th nowrap>
					<?if ($TotLateCount>0) {?>
						<?=SecToMin( $TotLateSec / $TotLateCount )?>
					<?}else{?>
						0
					<?}?>
				</th>
		</tr>
	</tbody>
</table>
</div>


<?}else if ($SearchType=="2") {//============================================================================================ 날짜별 ?>


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
	$ArrDate = [];
	$ArrData = [];

	$ArrTeacherLateCount = [];
	$ArrTeacherLateSecSum = [];
	$ArrDateLateCount = [];
	$ArrDateLateSecSum = [];

	
	//============  강사 루프 ===============
	$TeacherNum=0;
	while($Row = $Stmt->fetch()) {
		$ArrTeacherID[$TeacherNum] = $Row["TeacherID"];
		$ArrTeacherName[$TeacherNum] = $Row["TeacherName"];

		$ArrTeacherLateCount[$ArrTeacherID[$TeacherNum]] = 0;
		$ArrTeacherLateSecSum[$ArrTeacherID[$TeacherNum]] = 0;

		//============ 날짜 루프 =============
		$EndLoop = 0;
		$DateNum = 0;
		while ($EndLoop==0){
			$NowDate =  date('Y-m-d', strtotime($StartDate. ' +'.$DateNum.' day'));
			$ArrDate[$DateNum] = $NowDate;

			$ArrDateLateCount[$NowDate] = 0;
			$ArrDateLateSecSum[$NowDate] = 0;

			$ArrData[$ArrTeacherID[$TeacherNum]][$NowDate] = "||";//규정출근시간|실제출근시간|경과시간(초)
			
			if ($EndDate==$NowDate){
				$EndLoop=1;
			}

			$DateNum++;
		}
		//============ 날짜 루프 =============

		$TeacherNum++;
	}
	//============  강사 루프 ===============

	$Stmt = null;
	//강사목록 검색

	$Sql = "select 
				A.*,
				timestampdiff(second, A.TeacherAttendanceHour, A.TeacherAttendanceDateTime) as AttendDiff
			from TeacherAttendances A 
			where 
				datediff(A.CheckDate, '".$StartDate."')>=0 
				and datediff(A.CheckDate, '".$EndDate."')<=0 
				and timestampdiff(second, A.TeacherAttendanceHour, A.TeacherAttendanceDateTime)>".$DisplayLateSec." 
				".$AddTeacherWhere." 
			order by A.CheckDate asc 
		";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);



	while($Row = $Stmt->fetch()) {
		
		$TeacherID = $Row["TeacherID"];
		$TeacherAttendanceHour = $Row["TeacherAttendanceHour"]; // 출근해야할 시간
		$TeacherAttendanceDateTime = $Row["TeacherAttendanceDateTime"]; // 출근 시간
		$CheckDate = $Row["CheckDate"];
		$AttendDiff = $Row["AttendDiff"];

		$TempTeacherAttendanceHour = date('H:i', strtotime($TeacherAttendanceHour));
		if($TeacherAttendanceDateTime == null) {//결근은 표시해주지 않는다.
			$TempTeacherAttendanceDateTime = "";
		} else {
			$TempTeacherAttendanceDateTime = date('H:i:s', strtotime($TeacherAttendanceDateTime));

			$ArrTeacherLateCount[$TeacherID]++;
			$ArrTeacherLateSecSum[$TeacherID] = $ArrTeacherLateSecSum[$TeacherID] + $AttendDiff;
			$ArrDateLateCount[$CheckDate]++;
			$ArrDateLateSecSum[$CheckDate] = $ArrDateLateSecSum[$CheckDate] + $AttendDiff;

			$ArrData[$TeacherID][$CheckDate] = $TempTeacherAttendanceHour."|".$TempTeacherAttendanceDateTime."|".$AttendDiff;//규정출근시간|실제출근시간|경과시간(초)
		}

		
	
		//echo $TeacherID.">>".$DateNum."=".$ArrData[$TeacherID][$CheckDate]."{}<br>";
	}
	?>

<div class="uk-overflow-container">
<table class="uk-table uk-table-align-vertical">
	<thead>
		<tr>
				<th style="white-space: nowrap;">-</th>
			<?
			for ($jj=0;$jj<=$DateNum-1;$jj++){
			?>
				<th colspan="3" nowrap style="border-bottom:0px;font-weight:bold;color:#0055AA;font-size:18px;"><?=$ArrDate[$jj]?></th>
			<? 
			}
			?>
				<th nowrap rowspan="2"><?=$합계[$LangID]?><br>(total)</th>
		</tr>
		<tr>
				<th style="white-space: nowrap;"><?=$이름[$LangID]?></th>
			<?
			for ($ii=0;$ii<=$DateNum-1;$ii++){
			?>
				
				<th style="white-space: nowrap;"><?=$규정출근시간[$LangID]?><br>(start work time)</th>
				<th style="white-space: nowrap;"><?=$실제출근시간[$LangID]?><br>(Actual attend time)</th>
				<th style="white-space: nowrap;"><?=$경과시간[$LangID]?><br>(elapsed time)</th>
			<? 
			}
			?>
		</tr>
	</thead>
	<tbody>
		
		<?
		
		for($ii=0; $ii<=$TeacherNum-1;$ii++) {
		?>
		<tr>
				<th style="white-space: nowrap;"><?=$ArrTeacherName[$ii]?></th>
		<?
			for($jj=0; $jj<=$DateNum-1;$jj++) {
				
				//echo $ArrTeacherID[$ii].">>".$jj."=".$ArrData[$ArrTeacherID[$ii]][$ArrDate[$jj]]."()<br>";

				$ArrStrData = explode("|", $ArrData[$ArrTeacherID[$ii]][$ArrDate[$jj]]);
				$StrAttendTime = $ArrStrData[0];
				$StrRealAttendTime = $ArrStrData[1];
				$StrRunTime = $ArrStrData[2];



		?>
			
			<td style="white-space: nowrap;"><?=$StrAttendTime?></td>
			<td style="white-space: nowrap;"><?=$StrRealAttendTime?></td>
			<td style="white-space: nowrap;">
				<?if ($StrRunTime!=""){?>
					<?=SecToMin( $StrRunTime )?>
				<?}?>
			</td>
		<?
			}

		?>
			<th><?=SecToMin( $ArrTeacherLateSecSum[$ArrTeacherID[$ii]] )?></th>
		</tr>
		<?
		}
		?>
		</tr>

		<tr>
				<th style="white-space: nowrap;">-</th>
			<?
			for ($jj=0;$jj<=$DateNum-1;$jj++){
			?>
				<th colspan="3" nowrap style="border-bottom:0px;font-weight:bold;color:#0055AA;font-size:18px;"><?=$ArrDate[$jj]?></th>
			<? 
			}
			?>
				<th nowrap><?=$합계[$LangID]?><br>(total)</th>
		</tr>

		<tr>
				<th style="white-space: nowrap;"><?=$지각횟수[$LangID]?><br>(Number of lateness)</th>
			<?
			$TotLateCount = 0;
			for ($jj=0;$jj<=$DateNum-1;$jj++){
				$TotLateCount = $TotLateCount + $ArrDateLateCount[$ArrDate[$jj]];
			?>
				<th colspan="3" nowrap style="border-bottom:0px;"><?=$ArrDateLateCount[$ArrDate[$jj]]?></th>
			<? 
			}
			?>
				<th nowrap><?=$TotLateCount?></th>
		</tr>
		<tr>
				<th style="white-space: nowrap;"><?=$지각누적_분[$LangID]?><br>(total late minutes)</th>
			<?
			$TotLateSec = 0;
			for ($jj=0;$jj<=$DateNum-1;$jj++){
				$TotLateSec = $TotLateSec + $ArrDateLateSecSum[$ArrDate[$jj]];
			?>
				<th colspan="3" nowrap style="border-bottom:0px;"><?=SecToMin( $ArrDateLateSecSum[$ArrDate[$jj]] )?></th>
			<? 
			}
			?>
				<th nowrap><?=SecToMin( $TotLateSec )?></th>
		</tr>
		<tr>
				<th style="white-space: nowrap;"><?=$지각평균_분[$LangID]?><br>(average late minutes)</th>
			<?
			for ($jj=0;$jj<=$DateNum-1;$jj++){

			?>
				<th colspan="3" nowrap >
					<?if ($ArrDateLateCount[$ArrDate[$jj]]>0){ ?>
						<?=SecToMin( $ArrDateLateSecSum[$ArrDate[$jj]] / $ArrDateLateCount[$ArrDate[$jj]] )?>
					<?}else{?>
						0
					<?}?>
				</th>
			<? 
			}
			?>
				<th nowrap>
					<?if ($TotLateCount>0) {?>
						<?=SecToMin( $TotLateSec / $TotLateCount )?>
					<?}else{?>
						0
					<?}?>
				</th>
		</tr>
	</tbody>
</table>
</div>


<?}else if ($SearchType=="3") {//============================================================================================ 그래프 ?>


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
	$ArrDate = [];
	$ArrData = [];

	$ArrTeacherLateCount = [];
	$ArrTeacherLateSecSum = [];
	$ArrDateLateCount = [];
	$ArrDateLateSecSum = [];

	
	//============  강사 루프 ===============
	$TeacherNum=0;
	while($Row = $Stmt->fetch()) {
		$ArrTeacherID[$TeacherNum] = $Row["TeacherID"];
		$ArrTeacherName[$TeacherNum] = $Row["TeacherName"];

		$ArrTeacherLateCount[$ArrTeacherID[$TeacherNum]] = 0;
		$ArrTeacherLateSecSum[$ArrTeacherID[$TeacherNum]] = 0;

		//============ 날짜 루프 =============
		$EndLoop = 0;
		$DateNum = 0;
		while ($EndLoop==0){
			$NowDate =  date('Y-m-d', strtotime($StartDate. ' +'.$DateNum.' day'));
			$ArrDate[$DateNum] = $NowDate;

			$ArrDateLateCount[$NowDate] = 0;
			$ArrDateLateSecSum[$NowDate] = 0;

			$ArrData[$ArrTeacherID[$TeacherNum]][$NowDate] = "||";//규정출근시간|실제출근시간|경과시간(초)
			
			if ($EndDate==$NowDate){
				$EndLoop=1;
			}
			$DateNum++;
		}
		//============ 날짜 루프 =============

		$TeacherNum++;
	}
	//============  강사 루프 ===============

	$Stmt = null;
	//강사목록 검색

	$Sql = "select 
				A.*,
				timestampdiff(second, A.TeacherAttendanceHour, A.TeacherAttendanceDateTime) as AttendDiff
			from TeacherAttendances A 
				inner join Teachers B on A.TeacherID=B.TeacherID
				inner join TeacherGroups C on C.TeacherGroupID=B.TeacherGroupID 
			where 
				B.TeacherState=1
				and B.TeacherView=1
				and C.EduCenterID=$EduCenterID 
				and C.TeacherGroupState<>0 
				and B.TeacherState<>0 
				and datediff(A.CheckDate, '".$StartDate."')>=0 
				and datediff(A.CheckDate, '".$EndDate."')<=0 
				and timestampdiff(second, A.TeacherAttendanceHour, A.TeacherAttendanceDateTime)>".$DisplayLateSec." 
				".$AddTeacherWhere." 
			order by A.CheckDate asc 
		";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	
	while($Row = $Stmt->fetch()) {
		
		$TeacherID = $Row["TeacherID"];
		$TeacherAttendanceHour = $Row["TeacherAttendanceHour"]; // 출근해야할 시간
		$TeacherAttendanceDateTime = $Row["TeacherAttendanceDateTime"]; // 출근 시간
		$CheckDate = $Row["CheckDate"];
		$AttendDiff = $Row["AttendDiff"];

		$TempTeacherAttendanceHour = date('H:i', strtotime($TeacherAttendanceHour));
		if($TeacherAttendanceDateTime == null) {//결근은 표시해주지 않는다.
			$TempTeacherAttendanceDateTime = "";
		} else {
			$TempTeacherAttendanceDateTime = date('H:i:s', strtotime($TeacherAttendanceDateTime));
			$ArrTeacherLateCount[$TeacherID]++;
			$ArrTeacherLateSecSum[$TeacherID] = $ArrTeacherLateSecSum[$TeacherID] + $AttendDiff;
			$ArrDateLateCount[$CheckDate]++;
			$ArrDateLateSecSum[$CheckDate] = $ArrDateLateSecSum[$CheckDate] + $AttendDiff;
			
			$ArrData[$TeacherID][$CheckDate] = $TempTeacherAttendanceHour."|".$TempTeacherAttendanceDateTime."|".$AttendDiff;//규정출근시간|실제출근시간|경과시간(초)
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
	var lastCount = <?=json_encode($ArrTeacherLateCount);?>;

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


<?}//============================================================================================ 끝 ?>

<!-- ====================================================================== -->


					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<script>

function OpenAttendGraph() {
	var SearchStartYear = document.SearchForm.SearchStartYear.value;
	var SearchStartMonth = document.SearchForm.SearchStartMonth.value;
	var SearchStartDay = document.SearchForm.SearchStartDay.value;
	var SearchEndYear = document.SearchForm.SearchEndYear.value;
	var SearchEndMonth = document.SearchForm.SearchEndMonth.value;
	var SearchEndDay = document.SearchForm.SearchEndDay.value;
	var SearchTeacherID = document.SearchForm.SearchTeacherID.value;
	window.open("./teacher_attend_excel_form_graph.php?SearchTeacherID="+SearchTeacherID+"&SearchStartYear="+SearchStartYear+"&SearchStartMonth="+SearchStartMonth+"&SearchStartDay="+SearchStartDay+"&SearchEndYear="+SearchEndYear+"&SearchEndMonth="+SearchEndMonth+"&SearchEndDay="+SearchEndDay+"", "NewWin", "width:80%, height:80%");
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

				SelBoxAddOption( 'SearchStartDay', '<?=$일선택[$LangID]?>', "", "");
				for (ii=1 ; ii<=data.LastDay ; ii++ ){
					ArrOptionText     = ii + "<?=$일일[$LangID]?>";
					ArrOptionValue    = ii;

					ArrOptionSelected = "";
					if (ii==<?=(int)$SearchStartDay?>){
						ArrOptionSelected = "selected";
					}

					SelBoxAddOption( 'SearchStartDay', ArrOptionText, ArrOptionValue, ArrOptionSelected );
				}

			}else{

				SelBoxInitOption('SearchEndDay');

				SelBoxAddOption( 'SearchEndDay', '<?=$일선택[$LangID]?>', "", "");
				for (ii=1 ; ii<=data.LastDay ; ii++ ){
					ArrOptionText     = ii + "<?=$일일[$LangID]?>";
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

	
	location.href = "teacher_attend_excel_form_download_per_date.php?SearchStartYear="+SearchStartYear+"&SearchStartMonth="+SearchStartMonth+"&SearchStartDay="+SearchStartDay+"&SearchEndYear="+SearchEndYear+"&SearchEndMonth="+SearchEndMonth+"&SearchEndDay="+SearchEndDay+"&SearchTeacherID="+SearchTeacherID;
}


function SearchSubmit(SearchType) {
	document.SearchForm.SearchType.value = SearchType;
	document.SearchForm.action = "teacher_attend_excel_form.php";
	document.SearchForm.submit();
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


<script>
window.onload = function(){
	ChSearchStartMonth(1, <?=(int)$SearchStartMonth?>);
	ChSearchStartMonth(2, <?=(int)$SearchEndMonth?>);
}
</script>
<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>