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
	padding-top:6px;
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
	<span style="display:block;text-align:center;margin-top:20px;"><?=$페이지_로딩중_입니다[$LangID]?><span>
</div>

<?php
$SearchTeacherGroupID = isset($_REQUEST["SearchTeacherGroupID"]) ? $_REQUEST["SearchTeacherGroupID"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
$EduCenterID = isset($_REQUEST["EduCenterID"]) ? $_REQUEST["EduCenterID"] : "";
$SearchDate = isset($_REQUEST["SearchDate"]) ? $_REQUEST["SearchDate"] : "";
$SearchType = isset($_REQUEST["SearchType"]) ? $_REQUEST["SearchType"] : "";

if ($SearchState==""){
	$SearchState="1";
}

if ($EduCenterID==""){
	$EduCenterID="1";
}

if ($SearchDate==""){
	$SearchDate = date("Y-m-d");
}

if ($SearchTeacherGroupID==""){
	$SearchTeacherGroupID="4";
}

if ($SearchType==""){
	$SearchType="2";
}
?>


<!-- --------------------------------------------------------------- AA -->
<div id="page_content_inner" style="display:none;padding-bottom:20px;">
<!-- --------------------------------------------------------------- AA -->
<h3 class="heading_b uk-margin-bottom" style="margin-top:-30px;"><?=$전체_스케줄관리[$LangID]?></h3>


<form name="SearchForm" method="get">
<input type="hidden" name="EduCenterID" id="EduCenterID" value="<?=$EduCenterID?>">
<div class="md-card" style="margin-bottom:10px;">
	<div class="md-card-content">


		<div class="uk-grid" data-uk-grid-margin="">

			<div class="uk-width-medium-2-10">
				<div class="uk-margin-small-top">
					<select id="SearchType" name="SearchType" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
						<option value="2" <?if ($SearchType=="2"){?>selected<?}?>><?=$임시수업_배정기준[$LangID]?></option>
						<option value="1" <?if ($SearchType=="1"){?>selected<?}?>><?=$정규수업_배정기준[$LangID]?></option>
					</select>
				</div>
			</div>			

			<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchFranchiseID==1){?>none<?}?>;">
				<select id="SearchTeacherGroupID" name="SearchTeacherGroupID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$강사그룹선택[$LangID]?>" style="width:100%;"/>
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
				</select>		
			</div>

			<div class="uk-width-medium-2-10" style="display:none;">
				<div class="uk-margin-small-top">
					<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
						<option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
						<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$활동중[$LangID]?></option>
						<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$미활동[$LangID]?></option>
					</select>
				</div>
			</div>

			<div class="uk-width-medium-2-10">
				<label for="SearchText"><?=$강사명[$LangID]?></label>
				<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
			</div>

			<div class="uk-width-medium-2-10">
				<input type="text" id="SearchDate" name="SearchDate" value="<?=$SearchDate?>" class="md-input label-fixed" style="text-align:center;" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}" readonly>
			</div>

			<div class="uk-width-medium-2-10 uk-text-center">
				<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
			</div>
			
		</div>


	</div>
</div>
</form>



<!-- --------------------------------------------------------------- BB -->
<div class="md-card">
	<div class="md-card-content">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-1-1">
				<div class="uk-overflow-container" id="TimeSelectBox" style="height:700px;overflow-y:scroll;">
<!-- --------------------------------------------------------------- BB -->
				<div style="padding-left:10px;">※ 학생 이름 앞의 (★) 표시는 종료수업 (종료수업 기준 : B2B, B2C 상관없이 수강신청 - 수강관리 종료일 기준)</div>
				<div style="padding-left:10px;">※ 강사 이름 뒤의 숫자 (수업수 / 슬랏수)</div>
				<table class="type05" id="DivScheduleTable" style="margin-bottom:30px;min-width:950px;">
					<tbody>
					
					</tbody>
				</table>
<!-- --------------------------------------------------------------- BB -->
				</div>
			</div>
		</div>
	</div>
</div>


<!-- --------------------------------------------------------------- BB -->

<!-- --------------------------------------------------------------- AA -->
</div>
<!-- --------------------------------------------------------------- AA -->




<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->

<script>
function SearchSubmit(){
	document.SearchForm.action = "class_schedule.php";
    document.SearchForm.submit();
}


function OpenStudentCalendar(MemberID){
    var OpenUrl = "../pop_study_calendar.php?MemberID="+MemberID;

    $.colorbox({    
        href:OpenUrl
        ,width:"95%" 
        ,height:"95%"
        ,maxWidth: "1000"
        ,maxHeight: "850"
        ,title:""
        ,iframe:true 
        ,scrolling:true
        //,onClosed:function(){location.reload(true);}
        //,onComplete:function(){alert(1);}
    }); 
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



var SearchTeacherGroupID = "<?=$SearchTeacherGroupID?>";
var SearchText = "<?=$SearchText?>";
var SearchState = "<?=$SearchState?>";
var SearchDate = "<?=$SearchDate?>";
var EduCenterID = "<?=$EduCenterID?>";
var SearchType = "<?=$SearchType?>";
var StartHour = "";
var EndHour = "";

var TableLoading = 0;
var AjaxLoading = 1;
var MinuteListNum = 1;

function GetScheduleTable(){
	
	TableLoading = 1;

	if (StartHour==""){
		document.getElementById("DivLoading").style.display = "";
		document.getElementById("page_content_inner").style.display = "none";
	}
		
	url = "ajax_get_class_schedule_table_by_day.php";

	//alert(MinuteListNum);
	//alert(StartHour);
	//alert(EndHour);

	//if (StartHour==21){
	//	window.open( url + "?SearchState="+SearchState+"&EduCenterID="+EduCenterID+"&SearchText="+SearchText+"&SearchDate="+SearchDate+"&SearchTeacherGroupID="+SearchTeacherGroupID+"&StartHour="+StartHour+"&EndHour="+EndHour+"&MinuteListNum="+MinuteListNum);
	//}
	$.ajax(url, {
		data: {
			SearchTeacherGroupID: SearchTeacherGroupID,
			SearchText: SearchText,
			SearchState: SearchState,
			SearchDate: SearchDate,
			EduCenterID: EduCenterID,
			SearchType: SearchType,
			StartHour: StartHour,
			EndHour: EndHour,
			MinuteListNum: MinuteListNum
		},
		success: function (data) {
			
			$('#DivScheduleTable > tbody:last').append(data.ScheduleTable);
			
			AjaxLoading = data.AjaxLoading;
			StartHour = data.NextStartHour;
			EndHour = data.NextEndHour;
			MinuteListNum = data.NextMinuteListNum;
			ForceBlockSlotIDs = data.ForceBlockSlotIDs;
			document.getElementById("DivLoading").style.display = "none";
			document.getElementById("page_content_inner").style.display = "";

			ArrForceBlockSlotID = ForceBlockSlotIDs.split("|");
			//alert(ForceBlockSlotIDs);
			//alert(ArrForceBlockSlotID.length);
			for (ii=1;ii<=ArrForceBlockSlotID.length-2;ii++){
				$("#"+ArrForceBlockSlotID[ii]).css('background-color', '#ff0000');
				$("#"+ArrForceBlockSlotID[ii]).attr("title","강휴 - 80분연속수업"); 

				$("#"+ArrForceBlockSlotID[ii].replace('Div_Slot_','Able_')).val('1');
				$("#"+ArrForceBlockSlotID[ii].replace('Div_Slot_','Break_')).val('101');
			}

			TableLoading = 0;
		},
		error: function () {
			//alert('오류가 발생했습니다. 다시 시도해 주세요.');
		}
	});	


}



var SelectedCell = [];
function EventMouseOver(TeacherNum, WeekDayNum, HourNum, MinuteNum){


	if (SelectedCell[TeacherNum+"_"+WeekDayNum+"_"+HourNum+"_"+MinuteNum]!=1){

		//$(".TdTeacherName_"+TeacherNum).css('border', '1px solid #ff0000');
		//$(".TdWeekName_"+TeacherNum+"_"+WeekDayNum).css('border', '1px solid #ff0000');
		$(".TdMinuteNum_"+HourNum+"_"+MinuteNum).css('border', '1px solid #ff0000');
		//$(".TdSlot_"+TeacherNum+"_"+WeekDayNum).css('border', '1px solid #ff0000');
		$(".TdSlot_"+HourNum+"_"+MinuteNum).css('border', '1px solid #ff0000');

		SelectedCell[TeacherNum+"_"+WeekDayNum+"_"+HourNum+"_"+MinuteNum] = 1;

	}else{

		/*
		if ($(".TdTeacherName_"+TeacherNum).length>0){
			$(".TdTeacherName_"+TeacherNum).css('border', '0px solid #fff');
			$(".TdTeacherName_"+TeacherNum).css('border-bottom', '1px solid #ccc');
		}
		if ($(".TdWeekName_"+TeacherNum+"_"+WeekDayNum).length>0){
			$(".TdWeekName_"+TeacherNum+"_"+WeekDayNum).css('border', '0px solid #fff');
			$(".TdWeekName_"+TeacherNum+"_"+WeekDayNum).css('border-bottom', '1px solid #ccc');
		}
		*/
		if ($(".TdMinuteNum_"+HourNum+"_"+MinuteNum).length>0){
			$(".TdMinuteNum_"+HourNum+"_"+MinuteNum).css('border', '0px solid #fff');
			$(".TdMinuteNum_"+HourNum+"_"+MinuteNum).css('border-bottom', '1px solid #ccc');
		}
		/*
		if ($(".TdSlot_"+TeacherNum+"_"+WeekDayNum).length>0){
			$(".TdSlot_"+TeacherNum+"_"+WeekDayNum).css('border', '0px solid #fff');
			$(".TdSlot_"+TeacherNum+"_"+WeekDayNum).css('border-bottom', '1px solid #ccc');
		}
		*/
		if ($(".TdSlot_"+HourNum+"_"+MinuteNum).length>0){
			$(".TdSlot_"+HourNum+"_"+MinuteNum).css('border', '0px solid #fff');
			$(".TdSlot_"+HourNum+"_"+MinuteNum).css('border-bottom', '1px solid #ccc');
		}		

		SelectedCell[TeacherNum+"_"+WeekDayNum+"_"+HourNum+"_"+MinuteNum] = 0;
	}

}



$(document).ready(function(){
	GetScheduleTable();

	//document.getElementById("DivLoading").style.display = "none";
	//document.getElementById("page_content_inner").style.display = "";
});



</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>