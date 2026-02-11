<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<style>
table.type05 {
    border-collapse: separate;
    border-spacing: 1px;
    text-align: left;
    line-height: 1.5;
    border-top: 1px solid #ccc;
    margin: 20px 10px;
}
table.type05 th {
	padding:2px;
	text-align: center;
    font-weight: bold;
    vertical-align: top;
	border: 0px solid #fff;
    border-bottom: 1px solid #ccc;
    background: #efefef;
}
table.type05 td {
	padding:2px;
    vertical-align: top;
	border: 0px solid #fff;
    border-bottom: 1px solid #ccc;
}
</style>
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:20px;">
<div id="DivLoading" style="position:absolute; top:50%; left:50%; width:200px; height:300px; overflow:hidden; margin-top:-150px; margin-left:-100px;z-index:1000000;">
	<img src="images/loading.gif">
	<span style="display:block;text-align:center;margin-top:20px;"><?=$페이지_로딩중입니다[$LangID]?><span>
</div>
<?php
$ArrWeekDayStr = explode(",","Sun.,Mon.,Tue.,Wed.,Thu.,Fri.,Sat.");

$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$SearchTeacherGroupID = isset($_REQUEST["SearchTeacherGroupID"]) ? $_REQUEST["SearchTeacherGroupID"] : "";
$EduCenterID = isset($_REQUEST["EduCenterID"]) ? $_REQUEST["EduCenterID"] : "";
if ($EduCenterID==""){
	$EduCenterID="1";
}

if ($SearchTeacherGroupID==""){
	$SearchTeacherGroupID="4";
}

$Sql = "
		select 
				A.*,
				B.MemberName,
				C.ClassOrderWeekCount,
				D.ClassOrderTimeSlotCount,
				D.ClassOrderTimeTypeName,
				E.ClassProductName 
		from ClassOrders A 
			inner join Members B on A.MemberID=B.MemberID 
			inner join ClassOrderWeekCounts C on A.ClassOrderWeekCountID=C.ClassOrderWeekCountID 
			inner join ClassOrderTimeTypes D on A.ClassOrderTimeTypeID=D.ClassOrderTimeTypeID 
			inner join ClassProducts E on A.ClassProductID=E.ClassProductID 
		where A.ClassOrderID=:ClassOrderID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ClassProductID = $Row["ClassProductID"];
$ClassOrderLeveltestApplyTypeID = $Row["ClassOrderLeveltestApplyTypeID"];
$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
$ClassOrderWeekCountID = $Row["ClassOrderWeekCountID"];
$ClassOrderStartDate = $Row["ClassOrderStartDate"];
$MemberID = $Row["MemberID"];
$ClassOrderRequestText = $Row["ClassOrderRequestText"];
$ClassOrderState = $Row["ClassOrderState"];
$ClassMemberType = $Row["ClassMemberType"];
$ClassProgress = $Row["ClassProgress"];
$ClassOrderRegDateTime = $Row["ClassOrderRegDateTime"];
$ClassOrderModiDateTime = $Row["ClassOrderModiDateTime"];


$MemberName = $Row["MemberName"];
$ClassOrderWeekCount = $Row["ClassOrderWeekCount"];
$ClassOrderTimeTypeName = $Row["ClassOrderTimeTypeName"];
$ClassOrderTimeSlotCount = $Row["ClassOrderTimeSlotCount"];
$ClassProductName = $Row["ClassProductName"];

$TodayWeekDay = date("w", strtotime(date("Y-m-d")));
$ClassOrderPreSundayDateByToday = date("Y-m-d", strtotime("-".$TodayWeekDay." days"));//오늘 기준으로 이전 일요일(레벨테스트 당일 등록 불가 기능에 필요)

if ($ClassOrderStartDate==""){
	//$ClassOrderStartDate = date("Y-m-d", strtotime("+".(7-$TodayWeekDay)." days"));//오늘 기준으로 다음 일요일
	$ClassOrderStartDate = date("Y-m-d", strtotime("-".$TodayWeekDay." days"));//오늘 기준으로 이전 일요일
}

if ($_LINK_ADMIN_LEVEL_ID_>4){
	if ($TodayWeekDay>=5){//금요일 이후
		$ArrClassOrderStartDate[0] = date("Y-m-d", strtotime("+".(7-$TodayWeekDay)." days"));
	}else{
		$ArrClassOrderStartDate[0] = date("Y-m-d", strtotime("-".$TodayWeekDay." days"));
	}
}else{
	$ArrClassOrderStartDate[0] = date("Y-m-d", strtotime("-".$TodayWeekDay." days"));
}

for ($ii=1;$ii<=3;$ii++){
	$ArrClassOrderStartDate[$ii] = date('Y-m-d', strtotime($ArrClassOrderStartDate[$ii-1]. ' +7 day'));
}


?>


<!-- --------------------------------------------------------------- AA -->
<div id="page_content_inner" style="display:none;padding-bottom:20px;">
<!-- --------------------------------------------------------------- AA -->
<h3 class="heading_b uk-margin-bottom" style="margin-top:-30px;"></span><?=$수강신청_스케줄관리[$LangID]?></h3>


<div class="uk-width-xLarge-10-10  uk-width-large-10-10" style="margin-bottom:20px;">
	<div class="md-card" style="padding:20px;">
		<form name="SearchForm" method="get">
		<input type="hidden" id="ClassOrderID" name="ClassOrderID" value="<?=$ClassOrderID?>">
		<input type="hidden" id="ClassProductID" name="ClassProductID" value="<?=$ClassProductID?>">
		<input type="hidden" id="EduCenterID" name="EduCenterID" value="<?=$EduCenterID?>">

		


		<table class="type05" style="width:100%;">
			<tr>
				<th style="width:25%;padding-top:10px;"></span><?=$강사그룹[$LangID]?></th>
				<td style="text-align:center;" colspan="3">
					<select id="SearchTeacherGroupID" name="SearchTeacherGroupID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="강사그룹선택" style="width:100%;"/>
						<option value=""></option>
						<?
						$AddWhere_TeacherGoup = "";
						if ($_LINK_ADMIN_LEVEL_ID_>4){// 관리자가 아니면 General / Homebase 만 나오게 한다.
							$AddWhere_TeacherGoup = " and (A.TeacherGroupID=4 or A.TeacherGroupID=9) ";
						}


						$Sql2 = "select 
										A.* 
								from TeacherGroups A 
								where A.TeacherGroupState<>0 and A.TeacherGroupView=1 and A.EduCenterID=$EduCenterID ".$AddWhere_TeacherGoup."
								order by A.TeacherGroupOrder asc";
						$Stmt2 = $DbConn->prepare($Sql2);
						$Stmt2->execute();
						$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
						
						$OldSelectTeacherGroupState = -1;
						while($Row2 = $Stmt2->fetch()) {
							$SelectTeacherGroupID = $Row2["TeacherGroupID"];
							$SelectTeacherGroupName = $Row2["TeacherGroupName"];
							$SelectTeacherGroupState = $Row2["TeacherGroupState"];
						
							if ($OldSelectTeacherGroupState!=$SelectTeacherGroupState){
								if ($OldSelectTeacherGroupState!=-1){
									echo "</optgroup>";
								}
								
								if ($SelectTeacherGroupState==1){
									echo "<optgroup label=\"강사그룹(운영중)\">";
								}else if ($SelectTeacherGroupState==2){
									echo "<optgroup label=\"강사그룹(미운영)\">";
								}
							} 
							$OldSelectTeacherGroupState = $SelectTeacherGroupState;
						?>

						<option value="<?=$SelectTeacherGroupID?>" <?if ($SearchTeacherGroupID==$SelectTeacherGroupID){?>selected<?}?>><?=$SelectTeacherGroupName?></option>
						<?
						}
						$Stmt2 = null;
						?>
						<option value="0" <?if ($SearchTeacherGroupID=="0"){?>selected<?}?>></span><?=$전체그룹[$LangID]?></option>
					</select>	
				</td>
			</tr>
			<tr>
				<th style="width:25%;"></span><?=$수업종류[$LangID]?></th>
				<th style="width:25%;"></span><?=$학생명[$LangID]?></th>
				<th style="width:25%;"></span><?=$학습시간[$LangID]?><?if ($ClassProductID==1){?>/회<?}?></th>
				<th style="width:25%;"></span><?=$학습회수[$LangID]?><?if ($ClassProductID==1){?>/주<?}?></th>
			</tr>
			<tr>
				<td style="text-align:center;padding:15px 0px;"><?=$ClassProductName?></td>
				<td style="text-align:center;padding:15px 0px;"><?=$MemberName?></td>
				<td style="text-align:center;padding:15px 0px;">
					<?if ($ClassProductID==2 || $ClassProductID==3){?>
						20분
					<?}else{?>
						<?=$ClassOrderTimeTypeName?>
					<?}?>
				</td>
				<td style="text-align:center;padding:15px 0px;">
					<?if ($ClassProductID==2){?>
						1회
					<?}else{?>
						<?=$ClassOrderWeekCount?>회/주
					<?}?>
				</td>
			</tr>
			<tr>
				<th style="width:25%;"><?=$주간시작일[$LangID]?></th>
				<th style="width:25%;"><?=$수업시작일[$LangID]?></th>
				<th style="width:50%;" colspan="2"><?=$수업타입[$LangID]?></th>
			</tr>
			<tr>
				<td style="text-align:center;">
					<select id="ClassOrderStartDate" name="ClassOrderStartDate" onchange="ChClassOrderStartDate()" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="<?=$주간시작일[$LangID]?>" style="width:100%;"/>
						<?
						$ExistWeekTerm = 0;
						for ($ii=0;$ii<=3;$ii++) {
							
							if ($ClassOrderStartDate==$ArrClassOrderStartDate[$ii]){
								$ExistWeekTerm = 1;
							}
						
						?>
						<option value="<?=$ArrClassOrderStartDate[$ii]?>" <?if ($ClassOrderStartDate==$ArrClassOrderStartDate[$ii]){?>selected<?}?>><?=$ArrClassOrderStartDate[$ii]?></option>
						<?
						}
						?>
					</select>
				</td>
				<td style="text-align:center;">

					<?if ($ClassProductID!=1){?>
					<div style="padding-top:10px;"><?=$자동계산[$LangID]?></div>
					<?}?>					
					<span style="display:<?if ($ClassProductID!=1){?>none<?}?>;">
						<select id="ClassOrderRealStartDate" name="ClassOrderRealStartDate" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="<?=$수업시작일[$LangID]?>" style="width:100%;"/>
							<?
							if ($ExistWeekTerm == 0){
								$ClassOrderStartDate = $ArrClassOrderStartDate[0];
							}

							for ($ii=1;$ii<=5;$ii++) {
								$ClassOrderRealStartDate = date('Y-m-d', strtotime($ClassOrderStartDate . ' +'.$ii.' day'));
								if ( ($_LINK_ADMIN_LEVEL_ID_<=4 && date('Ymd', strtotime($ClassOrderStartDate . ' +'.$ii.' day')) == date("Ymd")) || date('Ymd', strtotime($ClassOrderStartDate . ' +'.$ii.' day')) > date("Ymd") ){//관리자 이거나 날짜가 오늘보다 클때
							?>
							<option value="<?=$ClassOrderRealStartDate?>"><?=$ClassOrderRealStartDate?> (<?=$ArrWeekDayStr[$ii]?>)</option>
							<?
								}
							}
							?>
						</select>
					</span>
				</td>
				<td style="text-align:center;padding-top:15px;" colspan="2">
					<?if ($ClassProductID==2){?>
						-
						<input type="hidden" name="ClassMemberType" value="<?=$ClassMemberType?>">
					<?}else{?>
						<span class="icheck-inline">
							<input type="radio" id="ClassMemberType1" onclick="ChClassMemberType(1)" class="radio_input" name="ClassMemberType" <?php if ($ClassMemberType==1) { echo "checked";}?> value="1">
							<label for="ClassMemberType1" class="radio_label"><span class="radio_bullet"></span>1:1 수업</label>
						</span>
						<span class="icheck-inline">
							<input type="radio" id="ClassMemberType2" onclick="ChClassMemberType(2)" class="radio_input" name="ClassMemberType" <?php if ($ClassMemberType==2) { echo "checked";}?> value="2">
							<label for="ClassMemberType2" class="radio_label"><span class="radio_bullet"></span>1:2 수업</label>
						</span>
						<span class="icheck-inline">
							<input type="radio" id="ClassMemberType3" onclick="ChClassMemberType(3)" class="radio_input" name="ClassMemberType" <?php if ($ClassMemberType==3) { echo "checked";}?> value="3">
							<label for="ClassMemberType3" class="radio_label"><span class="radio_bullet"></span>그룹수업</label>
						</span>
					<?}?>
				</td>

			</tr>
			<tr>
				<th colspan="4"><?=$요청사항[$LangID]?></th>
			</tr>
			<tr>
				<td colspan="4" style="text-align:left;padding:15px;"><?=str_replace("\n"," ",$ClassOrderRequestText)?></td>
			</tr>
		</table>
		<div style="color:#2196F3;text-align:left;padding-left:10px;line-height:1.5;">
			※ 주2회 이상 수업일 경우 <span style="color:#CC6666;">동일요일-동일강사</span><?=$선택이_가능합니다[$LangID]?>
			<br>
			※ 단, 수업시간을 연속하여 선택하거나 다른 수업에 붙여 선택할 경우 강사의 <span style="color:#CC6666;">80분 연속수업 자동 체크가 불가</span>할 수 있습니다.
			<br>
			※ 모든 강사는 원칙적으로 <span style="color:#CC6666;">연속 80분 수업이 금지</span>되어 있습니다.(일부 강사 예외)
			<br>
			※ 연속하여 80분 이상 수업하도록 수강신청되면 추후 관리자에 의해 <span style="color:#CC6666;">수업시간 변경이 될수도 있으니</span> 주의해 주시기 바랍니다.
		</div>
		</form>
	</div>
</div>



<!-- --------------------------------------------------------------- BB -->
<div class="md-card">
	<div class="md-card-content">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-1-1">
				<div id="DivWeekDateInfo" style="margin-bottom:5px;padding-left:15px;font-weight:bold;"></div>
				<div id="DivWeekDateInfoLoading" style="margin-bottom:5px;padding-left:15px;font-weight:bold;text-align:center;"><img src="images/loading.gif" style="width:20px;"> 스케줄 구성중.....</div>
				<div class="uk-overflow-container" id="TimeSelectBox" style="height:700px;overflow-y:scroll;">
<!-- --------------------------------------------------------------- BB -->
				<table class="type05" id="DivScheduleTable" style="margin-bottom:30px;">
					<tbody>
					
					</tbody>
				</table>
<!-- --------------------------------------------------------------- BB -->
				</div>
			</div>
		</div>
	</div>
</div>


<div class="uk-form-row" id="submit" style="text-align:center;display:<?if ($ClassProgress!=1){?>none<?}?>;margin-top:20px;">
	<a type="button" href="javascript:ClassOrderSubmit();" class="md-btn md-btn-primary"><?=$적용하기[$LangID]?></a>
</div>

<input type="hidden" name="ClassOrderTimeTypeID" id="ClassOrderTimeTypeID" value="<?=$ClassOrderTimeTypeID?>">
<input type="hidden" name="SelectSlotCode" id="SelectSlotCode" value="">
<input type="hidden" name="SelectStudyTimeCode" id="SelectStudyTimeCode" value="">

<!-- --------------------------------------------------------------- BB -->

<!-- --------------------------------------------------------------- AA -->
</div>
<!-- --------------------------------------------------------------- AA -->




<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->

<script>
var ClassOrderTimeSlotCount = <?=$ClassOrderTimeSlotCount?>;
var ClassOrderID = <?=$ClassOrderID?>;

var ClassOrderWeekCount = <?=$ClassOrderWeekCount?>;
var SelectedSlotCount = 0;

var ArrBgColor = [];
var ArrColor = [];

function SelectSlot(TeacherNum, WeekDayNum, MinuteListNum, TeacherBlock80Min){//TeacherBlock80Min - 1:80분 제한 / 0:제한안함
<?
//if ($ClassProgress==1){//======================================================= 
?>	


	if ($("#Able_"+TeacherNum+"_"+WeekDayNum+"_"+MinuteListNum).val()=="1"){

		DenySelect = 0;
		
		SelectSlotCode = document.getElementById("SelectSlotCode").value;
		SelectStudyTimeCode = document.getElementById("SelectStudyTimeCode").value;
		
		SlotCode = "";
		StudyTimeCode = "";
		MinuteListCount=1;
		for (ii=MinuteListNum; ii<=MinuteListNum+ClassOrderTimeSlotCount-1; ii++){
			if (DenySelect == 0 && ($("#Slot_"+TeacherNum+"_"+WeekDayNum+"_"+ii).val()=="0" || $("#Slot_"+TeacherNum+"_"+WeekDayNum+"_"+ii).length==0)){
				DenySelect = 1;
			}else if (TeacherBlock80Min==1 && DenySelect == 0  && $("#Break_"+TeacherNum+"_"+WeekDayNum+"_"+ii).length>0 && $("#Break_"+TeacherNum+"_"+WeekDayNum+"_"+ii).val()=="101"){
				DenySelect = 2;
			}else{
				SlotCode = SlotCode.concat($("#Slot_"+TeacherNum+"_"+WeekDayNum+"_"+ii).val());
				
				if (MinuteListCount==1){
					StudyTimeCode = $("#Slot_"+TeacherNum+"_"+WeekDayNum+"_"+ii).val();
				}else if (MinuteListCount==ClassOrderTimeSlotCount){
					ArrSlotCode = $("#Slot_"+TeacherNum+"_"+WeekDayNum+"_"+ii).val().split('_');
					StudyTimeCode = StudyTimeCode + "_"+ArrSlotCode[2]+"_"+ArrSlotCode[3];
				}

				MinuteListCount++;

			}
 

		}


		if (DenySelect == 0 && TeacherBlock80Min==1){//원하는 시간 모두 선택가능할때 - 이렇게 추가할경우 80분 연속강의에 위배되지 않는지 검사(풀스캔)
			
			//위쪽으로 현재 선택을 포함하여 최대 9단계 올라가본다.
			//올라가면서 빈슬랏이 나오면 빈슬랏의 번호를 딴다.
			EmptyNum = 100;//일단 100으로 한다. 100은 아직 빈슬랏을 못찾은 경우이다.
			for (iiii=-1;iiii>=(ClassOrderTimeSlotCount-9) ;iiii--){

				if (EmptyNum==100){
				
					ii = MinuteListNum + iiii;

					if ($("#Able_"+TeacherNum+"_"+WeekDayNum+"_"+ii).length==0 || $("#Able_"+TeacherNum+"_"+WeekDayNum+"_"+ii).val()=="1" || $("#Break_"+TeacherNum+"_"+WeekDayNum+"_"+ii).val()!="11"){
						EmptyNum = ii+1;//빈슬랏 바로 아래 슬랏
					}

				}

			}

			ActiveSlotCount = 0;

			//alert("시작점 : "+EmptyNum);

			if (EmptyNum==100){//빈슬랏을 못찾았다면 80분 위배이다.
				DenySelect = 2;
			}else{
				
				//위에서 찾은 빈슬랏 바로 아래 부터 9개를 살펴본다.
				
				for (iiii=EmptyNum;iiii<=(8+EmptyNum);iiii++)	{
					
					//현재 선택한 슬랏은 빈슬랏이 아님으로 취급한다.
					if ( (iiii-MinuteListNum)>=0 && (iiii-MinuteListNum)<=(ClassOrderTimeSlotCount-1) ){
						ActiveSlotCount++;

						//alert(iiii + " : 자신 ");
					}else{
						
						ii = iiii;

						if ($("#Able_"+TeacherNum+"_"+WeekDayNum+"_"+ii).length==0 || $("#Able_"+TeacherNum+"_"+WeekDayNum+"_"+ii).val()=="1" || $("#Break_"+TeacherNum+"_"+WeekDayNum+"_"+ii).val()!="11"){
							//alert(iiii + " : 빈 " + " : " + "#Break_"+TeacherNum+"_"+WeekDayNum+"_"+ii);
							//alert("1:"+$("#Able_"+TeacherNum+"_"+WeekDayNum+"_"+ii).length);
							//alert("2:"+$("#Able_"+TeacherNum+"_"+WeekDayNum+"_"+ii).val());
							//alert("3:"+$("#Break_"+TeacherNum+"_"+WeekDayNum+"_"+ii).val());
						}else{
							ActiveSlotCount++;
							//alert(iiii + " : 참 ");
						}
					}
				}

				//채워진 슬랏이 8개 초과이면 80분 위배이다
				if (ActiveSlotCount>8){
					DenySelect = 2;
				}

			}

			//alert(ActiveSlotCount);
		}

		
		
		ArrStudyTimeCode = StudyTimeCode.split('_');
		CheckStudyTimeCode = ArrStudyTimeCode[0] + "_" +  ArrStudyTimeCode[1] + "_";

		if (SelectSlotCode.indexOf(SlotCode)<0 && SelectedSlotCount>=(ClassOrderWeekCount*ClassOrderTimeSlotCount)){
			<?if ($ClassProductID==2 || $ClassProductID==3){?>
				UIkit.modal.alert( "<?=$먼저_선택한_시간을_취소한_후_새로운_시간을_선택하세요[$LangID]?>");
			<?}else{?>
				UIkit.modal.alert( "주" + ClassOrderWeekCount + "<?=$회까지_선택할_수_있습니다[$LangID]?>");
			<?}?>
		//}else if (SelectStudyTimeCode.indexOf(CheckStudyTimeCode)>=0 && SelectStudyTimeCode.indexOf(StudyTimeCode)<0){
		//	UIkit.modal.alert( "동일한 요일에 동일한 강사를 한번만 선택할 수 있습니다.");
		}else{
			if (DenySelect==1){
				UIkit.modal.alert( (ClassOrderTimeSlotCount*10) + '<?=$분_수업을_구성할_수_없습니다[$LangID]?>');
			}else if (DenySelect==2){
				UIkit.modal.alert( '80분 이상 연속수업 금지 정책에 위배됩니다.');
			}else{
				
				AbleNum = 0;
				for (ii=MinuteListNum; ii<=MinuteListNum+ClassOrderTimeSlotCount-1; ii++){
					console.log("ii : " +ii);
					console.log(<?=$ClassOrderID?>);
					if (SelectSlotCode.indexOf(SlotCode)>=0){
						//alert(2);
						document.getElementById("SelectSlotCode").value = SelectSlotCode.replace(SlotCode, ""); 
						document.getElementById("SelectStudyTimeCode").value = SelectStudyTimeCode.replace(StudyTimeCode, ""); 

						$("#Div_Slot_"+TeacherNum+"_"+WeekDayNum+"_"+ii).css("background-color",ArrBgColor[TeacherNum+"_"+WeekDayNum+"_"+ii]);
						$("#Div_Slot_"+TeacherNum+"_"+WeekDayNum+"_"+ii).css("color",ArrColor[TeacherNum+"_"+WeekDayNum+"_"+ii]);
						ArrBgColor[TeacherNum+"_"+WeekDayNum+"_"+ii] = "";
						ArrColor[TeacherNum+"_"+WeekDayNum+"_"+ii] = "";

						SelectedSlotCount--;

						if (AbleNum>0){
							$("#Able_"+TeacherNum+"_"+WeekDayNum+"_"+ii).val("1");
						}
					}else{
						//alert(1);
						document.getElementById("SelectSlotCode").value = SelectSlotCode + SlotCode;
						document.getElementById("SelectStudyTimeCode").value = SelectStudyTimeCode + StudyTimeCode;
						
						ArrBgColor[TeacherNum+"_"+WeekDayNum+"_"+ii] = $("#Div_Slot_"+TeacherNum+"_"+WeekDayNum+"_"+ii).css("background-color");
						ArrColor[TeacherNum+"_"+WeekDayNum+"_"+ii] = $("#Div_Slot_"+TeacherNum+"_"+WeekDayNum+"_"+ii).css("color");

						if (AbleNum==0){
							console.log(AbleNum);
							$("#Div_Slot_"+TeacherNum+"_"+WeekDayNum+"_"+ii).css("background-color","#2D96FF");
						}else{
							$("#Div_Slot_"+TeacherNum+"_"+WeekDayNum+"_"+ii).css("background-color","#76B6FC");
						}
						$("#Div_Slot_"+TeacherNum+"_"+WeekDayNum+"_"+ii).css("color","#ffffff");

						SelectedSlotCount++;

						if (AbleNum>0){
							$("#Able_"+TeacherNum+"_"+WeekDayNum+"_"+ii).val("0");
						}
					}
					
				
					AbleNum++;
				}
			}
		}
	}

<?
//}//======================================================= 
?>
}

function HideClassOrderSubmit() {
	document.getElementById("submit").style.display = "none";
}

function ShowClassOrderSubmit() {
	document.getElementById("submit").style.display = "";
}


var CheckClassOrderPreSundayDateByToday = <?=str_replace("-","",$ClassOrderPreSundayDateByToday)?>;
var CheckClassOrderStartDate = <?=str_replace("-","",$ClassOrderStartDate)?>;
var CheckClassProductID = <?=$ClassProductID?>;
var CheckTodayWeekDay = <?=$TodayWeekDay?>;
var CheckAdminLevelID = <?=$_LINK_ADMIN_LEVEL_ID_?>;

function ClassOrderSubmit(){

	CheckSelectStudyTimeCode = document.getElementById("SelectStudyTimeCode").value;
	if (CheckSelectStudyTimeCode!=""){
		ArrCheckSelectStudyTimeCode = CheckSelectStudyTimeCode.split('_');
		CheckSelectStudyTimeCodeWeekDay = ArrCheckSelectStudyTimeCode[1];
	}else{
		CheckSelectStudyTimeCodeWeekDay = 0;
	}

	if (SelectedSlotCount < (ClassOrderWeekCount*ClassOrderTimeSlotCount)){
		UIkit.modal.alert( "주" + ClassOrderWeekCount + '<?=$회_선택하셔야_합니다[$LangID]?>');
	}else if ( CheckAdminLevelID>4 && CheckClassProductID!=1 && ( CheckClassOrderStartDate < CheckClassOrderPreSundayDateByToday || (CheckClassOrderStartDate==CheckClassOrderPreSundayDateByToday && CheckSelectStudyTimeCodeWeekDay<=CheckTodayWeekDay) ) ){
		if (CheckClassProductID==2){
			UIkit.modal.alert( "<?=$레벨테스트는_오늘_이후_날짜부터_선택_가능합니다[$LangID]?>");
		}else if (CheckClassProductID==3){
			UIkit.modal.alert( "<?=$체험수업은_오늘_이후_날짜부터_선택_가능합니다[$LangID]?>");
		}
	}else if (CheckClassProductID==1 && document.getElementById("ClassOrderRealStartDate").value==""){
		UIkit.modal.alert( "수업 시작일이 설정되지 않았습니다. 수업 시작일은 오늘 이후 날짜만 선택 가능합니다. 수업 시작일 선택이 안될 경우 주간 시작일을 조정해 주세요.");
	}else{
		HideClassOrderSubmit();

		UIkit.modal.confirm(
			'<?=$적용_하시겠습니까[$LangID]?>?', 
			function(){ 

				ClassProductID = document.getElementById("ClassProductID").value;
				ClassOrderStartDate = document.getElementById("ClassOrderStartDate").value;
				ClassOrderRealStartDate = document.getElementById("ClassOrderRealStartDate").value;
				SelectSlotCode = document.getElementById("SelectSlotCode").value;
				SelectStudyTimeCode = document.getElementById("SelectStudyTimeCode").value;
				ClassOrderTimeTypeID = document.getElementById("ClassOrderTimeTypeID").value;
				url = "ajax_set_class_order_slot.php";

				//location.href = url + "?ClassOrderStartDate="+ClassOrderStartDate+"&ClassOrderRealStartDate="+ClassOrderRealStartDate+"&ClassOrderID="+ClassOrderID+"&ClassProductID="+ClassProductID+"&SelectSlotCode="+SelectSlotCode+"&SelectStudyTimeCode="+SelectStudyTimeCode+"&ClassMemberType=<?=$ClassMemberType?>&ClassOrderTimeTypeID="+ClassOrderTimeTypeID;

				
				$.ajax(url, {
					data: {
						ClassOrderStartDate: ClassOrderStartDate,
						ClassOrderID: ClassOrderID,
						ClassProductID: ClassProductID,
						SelectSlotCode: SelectSlotCode,
						SelectStudyTimeCode: SelectStudyTimeCode,
						ClassOrderRealStartDate: ClassOrderRealStartDate,
						ClassMemberType: "<?=$ClassMemberType?>",
						ClassOrderTimeTypeID: ClassOrderTimeTypeID
					},
					success: function (data) {
						opener.location.reload();
						window.close();
					},
					error: function () {

					}
				});
				


			}
		);



	}
	ShowClassOrderSubmit();
}


var OldClassOrderStartDate = "<?=$ClassOrderStartDate?>";
function ChClassOrderStartDate(){
	

	UIkit.modal.confirm(
		'<?=$주간시작일을_변경하시겠습니까[$LangID]?>?', 
		function(){ 
				
				ClassOrderStartDate = document.SearchForm.ClassOrderStartDate.value;
				url = "ajax_set_class_order_start_date.php";

				//location.href = url + "?ClassOrderStartDate="+ClassOrderStartDate+"&ClassOrderID="+ClassOrderID;
				$.ajax(url, {
					data: {
						ClassOrderStartDate: ClassOrderStartDate,
						ClassOrderID: ClassOrderID
					},
					success: function (data) {
						location.reload();
					},
					error: function () {

					}
				});
		},
		function(){
			document.SearchForm.ClassOrderStartDate.value = OldClassOrderStartDate;
		}
	);

}


var OldClassMemberType = "<?=$ClassMemberType?>";
function ChClassMemberType(ClassMemberType){
	if (OldClassMemberType!=ClassMemberType){
	
		UIkit.modal.confirm(
			'<?=$수업타입을_변경하시겠습니까[$LangID]?>?', 
			function(){ 
					
					url = "ajax_set_class_order_class_member_type.php";

					//location.href = url + "?ClassMemberType="+ClassMemberType+"&ClassOrderID="+ClassOrderID;
					$.ajax(url, {
						data: {
							ClassMemberType: ClassMemberType,
							ClassOrderID: ClassOrderID
						},
						success: function (data) {
							location.reload();
						},
						error: function () {

						}
					});
			},
			function(){
				if (OldClassMemberType==1){
					document.SearchForm.ClassMemberType[0].checked = true;
				}else if (OldClassMemberType==2){
					document.SearchForm.ClassMemberType[1].checked = true;
				}else if (OldClassMemberType==3){
					document.SearchForm.ClassMemberType[2].checked = true;
				}
			}
		);

	
	}
}


function SearchSubmit(){
	document.SearchForm.action = "class_order_schedule_form.php";
    document.SearchForm.submit();
}
</script>

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->





<script language="javascript">
$("#TimeSelectBox").scroll(function(){
	var el = $(this);
	if ( ( el[0].scrollHeight-el.scrollTop()-130 ) <= el.outerHeight() && TableLoading == 0 && AjaxLoading == 1 ){
		//alert("tl : "+TableLoading);
		GetScheduleTable();
	}
});



var EduCenterID = "<?=$EduCenterID?>";
var ClassOrderID = "<?=$ClassOrderID?>";
var SearchTeacherGroupID = "<?=$SearchTeacherGroupID?>";
var ClassOrderStartDate = "<?=$ClassOrderStartDate?>";
var StartHour = "";
var EndHour = "";

var TableLoading = 0;
var AjaxLoading = 1;
var MinuteListNum = 1;

function GetScheduleTable(){
	
	TableLoading = 1;
	document.getElementById("DivWeekDateInfoLoading").style.display = "";

	if (StartHour==""){
		document.getElementById("DivLoading").style.display = "";
		document.getElementById("page_content_inner").style.display = "none";
	}
		
	url = "ajax_get_class_order_schedule_table.php";

	//alert(MinuteListNum);
	//alert(StartHour);
	//alert(EndHour);

	//if (StartHour==15){
	//	window.open(url + "?EduCenterID="+EduCenterID+"&ClassOrderID="+ClassOrderID+"&SearchTeacherGroupID="+SearchTeacherGroupID+"&StartHour="+StartHour+"&EndHour="+EndHour+"&MinuteListNum="+MinuteListNum+"&ClassOrderStartDate="+ClassOrderStartDate);
	//}

	

	$.ajax(url, {
		data: {
			EduCenterID: EduCenterID,
			ClassOrderID: ClassOrderID,
			SearchTeacherGroupID: SearchTeacherGroupID,
			StartHour: StartHour,
			EndHour: EndHour,
			MinuteListNum: MinuteListNum,
			ClassOrderStartDate: ClassOrderStartDate
		},
		success: function (data) {
			
			$('#DivScheduleTable > tbody:last').append(data.ScheduleTable);
			
			AjaxLoading = data.AjaxLoading;
			StartHour = data.NextStartHour;
			EndHour = data.NextEndHour;
			MinuteListNum = data.NextMinuteListNum;
			WeekStartDate = data.WeekStartDate;
			WeekEndDate = data.WeekEndDate;

			document.getElementById("DivLoading").style.display = "none";
			document.getElementById("page_content_inner").style.display = "";
			document.getElementById("DivWeekDateInfo").innerHTML = "<span style='color:#ff0000;'>" + WeekStartDate + "(일)</span> 부터 " + "<span style='color:#ff0000;'>" + WeekEndDate + "(토)</span> 까지 스케줄";

			TableLoading = 0;
			document.getElementById("DivWeekDateInfoLoading").style.display = "none";

			if (EndHour<23){
				GetScheduleTable();
			}
				
		},
		error: function () {
			//alert('오류가 발생했습니다. 다시 시도해 주세요.');
		}
	});	


}



var OldTeacherNum = "";
var OldWeekDayNum = "";
var OldHourNum = "";
var OldMinuteNum = "";
function EventMouseOver(TeacherNum, WeekDayNum, HourNum, MinuteNum){

	
	if ($(".TdTeacherName_"+OldTeacherNum).length>0){
		$(".TdTeacherName_"+OldTeacherNum).css('border', '0px solid #fff');
		$(".TdTeacherName_"+OldTeacherNum).css('border-bottom', '1px solid #ccc');
	}
	if ($(".TdWeekName_"+OldTeacherNum+"_"+OldWeekDayNum).length>0){
		$(".TdWeekName_"+OldTeacherNum+"_"+OldWeekDayNum).css('border', '0px solid #fff');
		$(".TdWeekName_"+OldTeacherNum+"_"+OldWeekDayNum).css('border-bottom', '1px solid #ccc');
	}
	if ($(".TdMinuteNum_"+OldHourNum+"_"+OldMinuteNum).length>0){
		$(".TdMinuteNum_"+OldHourNum+"_"+OldMinuteNum).css('border', '0px solid #fff');
		$(".TdMinuteNum_"+OldHourNum+"_"+OldMinuteNum).css('border-bottom', '1px solid #ccc');
	}
	if ($(".TdSlot_"+OldTeacherNum+"_"+OldWeekDayNum).length>0){
		$(".TdSlot_"+OldTeacherNum+"_"+OldWeekDayNum).css('border', '0px solid #fff');
		$(".TdSlot_"+OldTeacherNum+"_"+OldWeekDayNum).css('border-bottom', '1px solid #ccc');
	}
	if ($(".TdSlot_"+OldHourNum+"_"+OldMinuteNum).length>0){
		$(".TdSlot_"+OldHourNum+"_"+OldMinuteNum).css('border', '0px solid #fff');
		$(".TdSlot_"+OldHourNum+"_"+OldMinuteNum).css('border-bottom', '1px solid #ccc');
	}


	$(".TdTeacherName_"+TeacherNum).css('border', '1px solid #ff0000');
	$(".TdWeekName_"+TeacherNum+"_"+WeekDayNum).css('border', '1px solid #ff0000');
	$(".TdMinuteNum_"+HourNum+"_"+MinuteNum).css('border', '1px solid #ff0000');
	$(".TdSlot_"+TeacherNum+"_"+WeekDayNum).css('border', '1px solid #ff0000');
	$(".TdSlot_"+HourNum+"_"+MinuteNum).css('border', '1px solid #ff0000');

	
	OldTeacherNum = TeacherNum;
	OldWeekDayNum = WeekDayNum;
	OldHourNum = HourNum;
	OldMinuteNum = MinuteNum;
}



$(document).ready(function(){
//$(window).load(function(){
	GetScheduleTable();
});



</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>