<?php
//include_once('../includes/dbopen.php');


	$DbHost = "localhost";
	$DbName = "mangoi";
	$DbUser = "mangoi";
	$DbPass = "mi!@#2019";

	try {
		$DbConn = new PDO("mysql:host=$DbHost;dbname=$DbName;charset=utf8", $DbUser, $DbPass);
		$DbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e){
		echo "Connection failed: " . $e->getMessage();
	}


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
$SubMenuID = 1108;
include_once('./inc_top.php'); 
include_once('./inc_menu_left.php');

function SecToMin($Sec){
	$TempMin = floor($Sec/60).":". substr("0".($Sec % 60),-2);
	return $TempMin;
}

$EduCenterID = 1;
$SearchType = isset($_REQUEST["SearchType"]) ? $_REQUEST["SearchType"] : "";

$SearchStartYear = isset($_REQUEST["SearchStartYear"]) ? $_REQUEST["SearchStartYear"] : "";
$SearchStartMonth = isset($_REQUEST["SearchStartMonth"]) ? $_REQUEST["SearchStartMonth"] : "";
$SearchStartDay = isset($_REQUEST["SearchStartDay"]) ? $_REQUEST["SearchStartDay"] : "";

$SearchEndYear = isset($_REQUEST["SearchEndYear"]) ? $_REQUEST["SearchEndYear"] : "";
$SearchEndMonth = isset($_REQUEST["SearchEndMonth"]) ? $_REQUEST["SearchEndMonth"] : "";
$SearchEndDay = isset($_REQUEST["SearchEndDay"]) ? $_REQUEST["SearchEndDay"] : "";

if ($SearchType==""){
	$SearchType = "1";
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

		<h3 class="heading_b uk-margin-bottom">출석현황</h3>

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
							<option value="<?=$iiii?>" <?if ($SearchStartMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
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
							<option value="<?=$iiii?>" <?if ($SearchEndMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
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


					<div class="uk-width-medium-3-10 uk-text-center" >
						<a type="button" href="javascript:SearchSubmit(1)" class="md-btn md-btn-primary uk-margin-small-top three">강사별</a>
						<a type="button" href="javascript:SearchSubmit(2)" class="md-btn md-btn-primary uk-margin-small-top three">날짜별</a>
						<a type="button" href="javascript:SearchSubmit(3)" class="md-btn md-btn-primary uk-margin-small-top three">강사-날짜별</a>
						<a type="button" href="javascript:DownloadPerDate()" class="md-btn md-btn-primary uk-margin-small-top two" style="display: inline-block">엑셀 다운로드</a>
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
			order by B.TeacherGroupOrder asc, A.TeacherOrder asc";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);


	$ArrTeacherID = [];
	$ArrTeacherName = [];
	$ArrDateTime = [];
	$ArrData = [];

	$ArrTeacherLateCount = [];
	$ArrTeacherLateSecSum = [];
	$ArrDateTimeLateCount = [];
	$ArrDateTimeLateSecSum = [];

	
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
		$DateTimeNum = 0;
		while ($EndLoop==0){
			$NowDate =  date('Y-m-d', strtotime($StartDate. ' +'.$DateNum.' day'));

			for ($HourNum=9;$HourNum<=23;$HourNum++){
				for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){
					
					$NowDateTime = $NowDate . "|" . substr("0".$HourNum, -2) . ":" .substr("0".$MinuteNum, -2);
					$ArrDateTime[$DateTimeNum] = $NowDateTime;

					$ArrDateTimeLateCount[$NowDateTime] = 0;
					$ArrDateTimeLateSecSum[$NowDateTime] = 0;

					$ArrData[$ArrTeacherID[$TeacherNum]][$NowDateTime] = "||";//규정출근시간|실제출근시간|경과시간(초)

					$DateTimeNum++;
				}
			}


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
				date_format(A.ClassStartDateTime, '%Y-%m-%d|%H:%i') as ClassDateTime, 
				timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime) as AttendDiff
			from ClassTeacherEnters A 
			where 
				datediff(A.ClassDate, '".$StartDate."')>=0 
				and datediff(A.ClassDate, '".$EndDate."')<=0 
				and timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime)>60
			order by A.ClassDate asc, A.ClassStartDateTime asc 
		";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);



	while($Row = $Stmt->fetch()) {
		
		$TeacherID = $Row["TeacherID"];
		$ClassStartDateTime = $Row["ClassStartDateTime"]; // 출근해야할 시간
		$ClassEnterDateTime = $Row["ClassEnterDateTime"]; // 출근 시간
		$ClassDate = $Row["ClassDate"];
		$ClassDateTime = $Row["ClassDateTime"];;
		$AttendDiff = $Row["AttendDiff"];

		$TempClassStartDateTime = date('H:i', strtotime($ClassStartDateTime));
		if($ClassEnterDateTime == null) {
			$TempClassEnterDateTime = "";
		} else {
			$TempClassEnterDateTime = date('H:i:s', strtotime($ClassEnterDateTime));

			$ArrTeacherLateCount[$TeacherID]++;
			$ArrTeacherLateSecSum[$TeacherID] = $ArrTeacherLateSecSum[$TeacherID] + $AttendDiff;
			$ArrDateTimeLateCount[$ClassDateTime]++;
			$ArrDateTimeLateSecSum[$ClassDateTime] = $ArrDateTimeLateSecSum[$ClassDateTime] + $AttendDiff;			
			
			$ArrData[$TeacherID][$ClassDateTime] = $TempClassStartDateTime."|".$TempClassEnterDateTime."|".$AttendDiff;//규정출근시간|실제출근시간|경과시간(초)
		
		}

		
	}
	?>


<table class="uk-table uk-table-align-vertical">
	<thead>
		<tr>
				<th style="white-space: nowrap;">-</th>
			<?
			for ($ii=0;$ii<=$TeacherNum-1;$ii++){
			?>
				<th colspan="3" nowrap style="border-bottom:0px;"><?=$ArrTeacherName[$ii]?></th>
			<? 
			}
			?>
				<th nowrap rowspan="2">합계</th>
		</tr>
		<tr>
				<th style="white-space: nowrap;">날짜</th>
			<?
			for ($ii=0;$ii<=$TeacherNum-1;$ii++){
			?>
				
				<th style="white-space: nowrap;">규정출근시간</th>
				<th style="white-space: nowrap;">실제출근시간</th>
				<th style="white-space: nowrap;">경과시간</th>
			<? 
			}
			?>
		</tr>
	</thead>
	<tbody>
		
		<?
		for ($jj=0;$jj<=$DateTimeNum-1;$jj++){

		?>
		<tr>
			<th style="white-space: nowrap;"><?=$ArrDateTime[$jj]?></th>
		<?
			for($ii=0; $ii<=$TeacherNum-1;$ii++) {
				$ArrStrData = explode("|", $ArrData[$ArrTeacherID[$ii]][$ArrDateTime[$jj]]);
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
			<th><?=SecToMin( $ArrDateTimeLateSecSum[$ArrDateTime[$jj]] )?></th>
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
				<th colspan="3" nowrap style="border-bottom:0px;"><?=$ArrTeacherName[$ii]?></th>
			<? 
			}
			?>
				<th nowrap>합계</th>
		</tr>

		<tr>
				<th style="white-space: nowrap;">지각횟수</th>
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
				<th style="white-space: nowrap;">지각누적(분)</th>
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
				<th style="white-space: nowrap;">지각평균(분)</th>
			<?
			for ($ii=0;$ii<=$TeacherNum-1;$ii++){
				$TotLateCount = $TotLateCount + $ArrTeacherLateCount[$ArrTeacherID[$ii]];
			?>
				<th colspan="3" nowrap style="border-bottom:0px;">
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
			order by B.TeacherGroupOrder asc, A.TeacherOrder asc";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);


	$ArrTeacherID = [];
	$ArrTeacherName = [];
	$ArrDate = [];
	$ArrDateTime = [];
	$ArrData = [];

	$ArrTeacherLateCount = [];
	$ArrTeacherLateSecSum = [];
	$ArrDateTimeLateCount = [];
	$ArrDateTimeLateSecSum = [];

	
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
		$DateTimeNum = 0;
		while ($EndLoop==0){
			$NowDate =  date('Y-m-d', strtotime($StartDate. ' +'.$DateNum.' day'));
			
			$ArrDate[$DateNum] = $NowDate;

			for ($HourNum=9;$HourNum<=23;$HourNum++){
				for ($MinuteNum=0;$MinuteNum<=50;$MinuteNum=$MinuteNum+10){

					$NowDateTime = $NowDate . "|" . substr("0".$HourNum, -2) . ":" .substr("0".$MinuteNum, -2);
					$ArrDateTime[$DateTimeNum] = $NowDateTime;
					
					$ArrDateTimeLateCount[$NowDateTime] = 0;
					$ArrDateTimeLateSecSum[$NowDateTime] = 0;

					$ArrData[$ArrTeacherID[$TeacherNum]][$NowDateTime] = "||";//규정출근시간|실제출근시간|경과시간(초)

					$DateTimeNum++;
				}
			}
				

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
				date_format(A.ClassStartDateTime, '%Y-%m-%d|%H:%i') as ClassDateTime, 
				timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime) as AttendDiff
			from ClassTeacherEnters A 
			where 
				datediff(A.ClassDate, '".$StartDate."')>=0 
				and datediff(A.ClassDate, '".$EndDate."')<=0 
				and timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime)>60
			order by A.ClassDate asc, A.ClassStartDateTime asc 
		";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);



	while($Row = $Stmt->fetch()) {
		
		$TeacherID = $Row["TeacherID"];
		$ClassStartDateTime = $Row["ClassStartDateTime"]; // 출근해야할 시간
		$ClassEnterDateTime = $Row["ClassEnterDateTime"]; // 출근 시간
		$ClassDate = $Row["ClassDate"];
		$ClassDateTime = $Row["ClassDateTime"];
		$AttendDiff = $Row["AttendDiff"];

		$TempClassStartDateTime = date('H:i', strtotime($ClassStartDateTime));
		if($ClassEnterDateTime == null) {
			$TempClassEnterDateTime = "";
		} else {
			$TempClassEnterDateTime = date('H:i:s', strtotime($ClassEnterDateTime));

			$ArrTeacherLateCount[$TeacherID]++;
			$ArrTeacherLateSecSum[$TeacherID] = $ArrTeacherLateSecSum[$TeacherID] + $AttendDiff;
			$ArrDateTimeLateCount[$ClassDateTime]++;
			$ArrDateTimeLateSecSum[$ClassDateTime] = $ArrDateTimeLateSecSum[$ClassDateTime] + $AttendDiff;

			$ArrData[$TeacherID][$ClassDateTime] = $TempClassStartDateTime."|".$TempClassEnterDateTime."|".$AttendDiff;//규정출근시간|실제출근시간|경과시간(초)
		}

		
	}
	?>


<table class="uk-table uk-table-align-vertical">
	<thead>
		<tr>
				<th style="white-space: nowrap;">-</th>
				<th style="white-space: nowrap;">-</th>
			<?
			for ($jj=0;$jj<=$DateNum-1;$jj++){
			?>
				<th colspan="3" nowrap style="border-bottom:0px;"><?=$ArrDate[$jj]?></th>
			<? 
			}
			?>
				<th nowrap rowspan="2">합계</th>
		</tr>
		<tr>
				<th style="white-space: nowrap;">시간</th>
				<th style="white-space: nowrap;">이름</th>
			<?
			for ($ii=0;$ii<=$DateNum-1;$ii++){
			?>
				
				<th style="white-space: nowrap;">규정출근시간</th>
				<th style="white-space: nowrap;">실제출근시간</th>
				<th style="white-space: nowrap;">경과시간</th>
			<? 
			}
			?>
		</tr>
	</thead>
	<tbody>
		
		<?
		for ($jj=0;$jj<=$DateTimeNum-1;$jj++){
			for($ii=0; $ii<=$TeacherNum-1;$ii++) {
			
		?>
		<tr>
				<th style="white-space: nowrap;"><?=substr($ArrDateTime[$jj],-5)?></th>
				<th style="white-space: nowrap;"><?=$ArrTeacherName[$ii]?></th>
				
		<?
			for($kk=0; $kk<=$DateNum-1;$kk++) {
				

				$ArrStrData = explode("|", $ArrData[$ArrTeacherID[$ii]][$ArrDateTime[$jj]]);
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
		}
		?>
		</tr>

		<tr>
				<th style="white-space: nowrap;">-</th>
				<th style="white-space: nowrap;">-</th>
			<?
			for ($jj=0;$jj<=$DateNum-1;$jj++){
			?>
				<th colspan="3" nowrap style="border-bottom:0px;"><?=$ArrDate[$jj]?></th>
			<? 
			}
			?>
				<th nowrap>합계</th>
		</tr>

		<tr>
				<th style="white-space: nowrap;">-</th>
				<th style="white-space: nowrap;">지각횟수</th>

			<?
			$TotLateCount = 0;
			for ($jj=0;$jj<=$DateNum-1;$jj++){
				$TotLateCount = $TotLateCount + $ArrDateTimeLateCount[$ArrDateTime[$jj]];
			?>
				<th colspan="3" nowrap style="border-bottom:0px;"><?=$ArrDateTimeLateCount[$ArrDateTime[$jj]]?></th>
			<? 
			}
			?>
				<th nowrap><?=$TotLateCount?></th>
		</tr>
		<tr>
				<th style="white-space: nowrap;">-</th>
				<th style="white-space: nowrap;">지각누적(분)</th>
			<?
			$TotLateSec = 0;
			for ($jj=0;$jj<=$DateNum-1;$jj++){
				$TotLateSec = $TotLateSec + $ArrDateTimeLateSecSum[$ArrDateTime[$jj]];
			?>
				<th colspan="3" nowrap style="border-bottom:0px;"><?=SecToMin( $ArrDateTimeLateSecSum[$ArrDateTime[$jj]] )?></th>
			<? 
			}
			?>
				<th nowrap><?=SecToMin( $TotLateSec )?></th>
		</tr>
		<tr>
				<th style="white-space: nowrap;">-</th>
				<th style="white-space: nowrap;">지각평균(분)</th>
			<?
			for ($jj=0;$jj<=$DateNum-1;$jj++){

			?>
				<th colspan="3" nowrap style="border-bottom:0px;">
					<?if ($ArrDateTimeLateCount[$ArrDateTime[$jj]]>0){ ?>
						<?=SecToMin( $ArrDateTimeLateSecSum[$ArrDateTime[$jj]] / $ArrDateTimeLateCount[$ArrDateTime[$jj]] )?>
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

<?}else if ($SearchType=="3") {?>
^^
<?}?>
<!-- ====================================================================== -->
						</div>
					</div>
				</div>
			</div>
		</div>



	</div>
</div>

<script>
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

	
	location.href = "teacher_enter_excel_form_download_per_date.php?SearchStartYear="+SearchStartYear+"&SearchStartMonth="+SearchStartMonth+"&SearchStartDay="+SearchStartDay+"&SearchEndYear="+SearchEndYear+"&SearchEndMonth="+SearchEndMonth+"&SearchEndDay="+SearchEndDay;
}


function DownloadPerTeacher() {
	//location.href = "student_list_excel_download.php";
	alert("not yet");
}


function SearchSubmit(SearchType) {
	document.SearchForm.SearchType.value = SearchType;
	document.SearchForm.action = "teacher_enter_excel_form_old.php";
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