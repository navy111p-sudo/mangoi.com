<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_04";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<?php
include_once('./includes/common_header.php');

$Sql = "select SubID from Subs where SubCode=:SubCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SubCode', $SubCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$SubID = $Row["SubID"];


if ($UseMain==1){
	$Sql = "select * from Main limit 0,1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MainLayout = $Row["MainLayout"];
	$MainLayoutCss = $Row["MainLayoutCss"];
	$MainLayoutJavascript = $Row["MainLayoutJavascript"];
	list($MainLayoutTop, $MainLayoutBottom) = explode("{{Page}}", $MainLayout);
}else{
	$MainLayoutTop = "";
	$MainLayoutBottom = "";
	$MainLayoutCss = "";
	$MainLayoutJavascript = "";
}


if ($UseSub==1){
	$Sql = "select * from Subs where SubID=:SubID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SubID', $SubID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$SubLayout = $Row["SubLayout"];
	$SubLayoutCss = $Row["SubLayoutCss"];
	$SubLayoutJavascript = $Row["SubLayoutJavascript"];
	list($SubLayoutTop, $SubLayoutBottom) = explode("{{Page}}", $SubLayout);
}else{
	$SubLayoutTop = "";
	$SubLayoutBottom = "";
	$SubLayoutCss = "";
	$SubLayoutJavascript = "";
}


if (trim($MainLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $MainLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

if (trim($SubLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $SubLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

?>
</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<?php
// $MainLayoutTop = convertHTML(trim($MainLayoutTop));
// $SubLayoutTop = convertHTML(trim($SubLayoutTop));
// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $MainLayoutTop = convertHTML(trim("{{Piece(header_gumiivyleague)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_04_gumiivyleague)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_gumiivyleague_bottom)}}"));    
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.engedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engedu)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engedu_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));


} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }

echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";
?>

<?
$LeveltestApplyDate = date("Y-m-d", strtotime ("+2 days"));
$ClassOrderTimeTypeID = 2;
?>


<div class="sub_wrap bg_gray" style="border:0;">       
    <div class="sub_title_common_wrap"><h2 class="sub_title_common TrnTag"><b>레벨테스트</b> 신청하기</h2></div>

    <section class="level_application_wrap">
        <div class="level_application_area">

            <div class="level_application_text_box TrnTag">※ 강사선택 버튼을 클릭하신 후 레벨테스트를 예약하세요.</div>
  
			<form name="RegForm" id="RegForm" style="display:none;">
			<input type="text" name="EduCenterID" id="EduCenterID" value="1">

			<input type="text" name="TeacherID" id="TeacherID" value="">
			<input type="text" name="LeveltestApplyDate" id="LeveltestApplyDate" value="<?=$LeveltestApplyDate?>">
			<input type="text" name="LeveltestTimeHour" id="LeveltestTimeHour" value="">
			<input type="text" name="LeveltestTimeMinute" id="LeveltestTimeMinute" value="">
			</form>

			<script>
			var OpneTeacherID = 0;
			var OpneTeacherBlock80Min = 0;
			function OpneTeacherTable(TeacherID, TeacherBlock80Min, LeveltestApplyDate){

				

				WeekDayNum = new Date(LeveltestApplyDate).getDay();

				OpneTeacherID = TeacherID;
				OpneTeacherBlock80Min = TeacherBlock80Min;
				ClassOrderTimeSlotCount = 2;//20분

				if (TeacherBlock80Min==1){//원하는 시간 모두 선택가능할때 - 이렇게 추가할경우 80분 연속강의에 위배되지 않는지 검사(풀스캔) - 숨기기
					
					//alert(TeacherID);
					//alert(LeveltestApplyDate);
					//alert(TeacherBlock80Min);


					for (MinuteListNum=1;MinuteListNum<=144;MinuteListNum++){//6*24 = 144
					
				
						//위쪽으로 현재 선택을 포함하여 최대 9단계 올라가본다.
						//올라가면서 빈슬랏이 나오면 빈슬랏의 번호를 딴다.
						EmptyNum = 100;//일단 100으로 한다. 100은 아직 빈슬랏을 못찾은 경우이다.
						for (iiii=-1;iiii>=(ClassOrderTimeSlotCount-9) ;iiii--){

							if (EmptyNum==100){
							
								ii = MinuteListNum + iiii;

								if ($("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length==0 || $("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="1" || $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()!="11"){
									EmptyNum = ii+1;//빈슬랏 바로 아래 슬랏
								}

							}

						}

						ActiveSlotCount = 0;

						//alert("시작점 : "+EmptyNum);

						if (EmptyNum==100){//빈슬랏을 못찾았다면 80분 위배이다.
							$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).css('background-color', '#bbbbbb');
							$("#Break_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).val("101");
							//DenySelect = 2;
						}else{
							
							//위에서 찾은 빈슬랏 바로 아래 부터 9개를 살펴본다.
							
							for (iiii=EmptyNum;iiii<=(8+EmptyNum);iiii++)	{
								
								//현재 선택한 슬랏은 빈슬랏이 아님으로 취급한다.
								if ( (iiii-MinuteListNum)>=0 && (iiii-MinuteListNum)<=(ClassOrderTimeSlotCount-1) ){
									ActiveSlotCount++;

									//alert(iiii + " : 자신 ");
								}else{
									
									ii = iiii;

									if ($("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length==0 || $("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="1" || $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()!="11"){
										//alert(iiii + " : 빈 " + " : " + "#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii);
										//alert("1:"+$("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length);
										//alert("2:"+$("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val());
										//alert("3:"+$("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val());
									}else{
										ActiveSlotCount++;
										//alert(iiii + " : 참 ");
									}
								}
							}



							//채워진 슬랏이 8개 초과이면 80분 위배이다//숨긴다.
							if (ActiveSlotCount>8){
								$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).css('background-color', '#bbbbbb');
								$("#Break_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).val("101");
								$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).css('display', 'none');//숨긴다
								//DenySelect = 2;
							}

						}

						//alert(ActiveSlotCount);
					}


					
				}

				

				CheckAbleClassOrderTime(TeacherID, WeekDayNum);

			}



			function CheckAbleClassOrderTime(TeacherID, WeekDayNum){//20분 수업이 가능한지 체크
			
				for (MinuteListNum=1;MinuteListNum<=144;MinuteListNum++){//6*24 = 144
				
					ii=MinuteListNum+1;
					//바로 다음 슬랏 수업이 가능한지 체크한다.
					if ($("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length!=0 && $("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="1" && $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="0"){
						//수업가능
					}else{
						$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).css('background-color', '#bbbbbb');
						$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).css('display', 'none');//숨긴다
					}

				}


			}


			function TeacherChangeDate(LeveltestApplyDate, SearchTeacherID, OpneTeacherBlock80Min){

				document.getElementById("DivTeacherTimeHTML_"+SearchTeacherID).innerHTML = "<div style=\"text-align:center;margin-top:50px;\"><img src=\"images/loading.gif\"></div>";

				if (LeveltestApplyDate==""){
					LeveltestApplyDate = document.RegForm.LeveltestApplyDate.value;
				}
				document.RegForm.LeveltestApplyDate.value = LeveltestApplyDate;
				
				url = "ajax_get_leveltest_reserve_teacher_list.php";

				EduCenterID = document.RegForm.EduCenterID.value;
				//window.open(url + "?EduCenterID="+EduCenterID+"&LeveltestApplyDate="+LeveltestApplyDate+"&SearchTeacherID="+SearchTeacherID, 'ajax_get_class_order_teacher_list');
				
				
				$.ajax(url, {
					data: {
						EduCenterID: EduCenterID,
						LeveltestApplyDate: LeveltestApplyDate,
						SearchTeacherID: SearchTeacherID
					},
					success: function (data) {

						ForceBlockSlotIDs = data.ForceBlockSlotIDs;
						TeacherListHTML = data.TeacherListHTML;


						document.getElementById("DivTeacherTimeHTML_"+SearchTeacherID).innerHTML = TeacherListHTML;

						ArrForceBlockSlotID = ForceBlockSlotIDs.split("|");
						//alert(ArrForceBlockSlotID.length);
						//alert(ForceBlockSlotIDs);
						for (ii=1;ii<=ArrForceBlockSlotID.length-2;ii++){
							//$("#"+ArrForceBlockSlotID[ii]).css('background-color', '#0000ff');
							$("#"+ArrForceBlockSlotID[ii]).css('display', 'none');

							$("#"+ArrForceBlockSlotID[ii].replace('Div_Slot_','Able_')).val('1');
							$("#"+ArrForceBlockSlotID[ii].replace('Div_Slot_','Break_')).val('101');
						}
						
						

			
						//80 막기 체크 =================================================
						OpneTeacherTable(SearchTeacherID, OpneTeacherBlock80Min, LeveltestApplyDate);
						//80 막기 체크 =================================================



					},
					error: function () {
						alert('err1');
					}
				});
				

			}
			
			
			function GetTeacherList(){
				
				document.getElementById("DivTeacherListHTML").innerHTML = "<div style=\"text-align:center;margin-top:50px;\"><img src=\"images/loading.gif\"></div>";
				document.RegForm.TeacherID.value = "";
				document.RegForm.LeveltestTimeHour.value = "";
				document.RegForm.LeveltestTimeMinute.value = "";
				
				url = "ajax_get_leveltest_reserve_teacher_list.php";

				EduCenterID = document.RegForm.EduCenterID.value;
				LeveltestApplyDate = document.RegForm.LeveltestApplyDate.value;
				//window.open(url + "?EduCenterID="+EduCenterID+"&LeveltestApplyDate="+LeveltestApplyDate, 'ajax_get_class_order_teacher_list');
				
				 
				$.ajax(url, {
					data: {
						EduCenterID: EduCenterID,
						LeveltestApplyDate: LeveltestApplyDate
					},
					success: function (data) {

						ForceBlockSlotIDs = data.ForceBlockSlotIDs;
						TeacherListHTML = data.TeacherListHTML;
						
						document.getElementById("DivTeacherListHTML").innerHTML = TeacherListHTML;

						ArrForceBlockSlotID = ForceBlockSlotIDs.split("|");
						//alert(ArrForceBlockSlotID.length);
						//alert(ForceBlockSlotIDs);
						for (ii=1;ii<=ArrForceBlockSlotID.length-2;ii++){
							$("#"+ArrForceBlockSlotID[ii]).css('background-color', '#0000ff');
							//$("#"+ArrForceBlockSlotID[ii]).css('display', 'none');

							$("#"+ArrForceBlockSlotID[ii].replace('Div_Slot_','Able_')).val('1');
							$("#"+ArrForceBlockSlotID[ii].replace('Div_Slot_','Break_')).val('101');
						}


						//80 막기 체크 =================================================
						// 여기서는 하지 않고 교사를 선택할때 한다.
						//80 막기 체크 =================================================


						$('.level_teacher_select_btn').click(function(e){		
							e.preventDefault();
							$('.level_teacher_select_btn').removeClass('active');		

							$('.level_teacher_time_wrap').stop().slideUp(200);		

							if(!$(this).parent().next().is(":visible"))
							{
								$(this).parent().next().stop().slideDown(200);
								$(this).addClass('active');	
							}	
						});

					
					},
					error: function () {
						alert('err2');
					}
				});
				

			}

			var OldDivSlotID = "";
			var ClassOrderTimeTypeID = <?=$ClassOrderTimeTypeID?>;
			function SelectSlot(TeacherID, HourNum, MinuteNum, WeekDayNum, MinuteListNum, TeacherBlock80Min){//TeacherBlock80Min - 1:80분 제한 / 0:제한안함
				
				if (OldDivSlotID != "Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum){
					
					DenySelect = 0;
					for (ii=1;ii<=ClassOrderTimeTypeID-1;ii++){
						
						TempMinuteListNum = MinuteListNum+ii;

						if ( $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+TempMinuteListNum).length==0 ){
							DenySelect = 1;
						}else if ( $("#Able_"+TeacherID+"_"+WeekDayNum+"_"+TempMinuteListNum).val()!="1" ){
							DenySelect = 1;
						}
					
					}




					if (DenySelect == 0 && TeacherBlock80Min==1){//원하는 시간 모두 선택가능할때 - 이렇게 추가할경우 80분 연속강의에 위배되지 않는지 검사(풀스캔)
						
						//위쪽으로 현재 선택을 포함하여 최대 9단계 올라가본다.
						//올라가면서 빈슬랏이 나오면 빈슬랏의 번호를 딴다.
						ClassOrderTimeSlotCount = 2;//20분
						EmptyNum = 100;//일단 100으로 한다. 100은 아직 빈슬랏을 못찾은 경우이다.
						for (iiii=-1;iiii>=(ClassOrderTimeSlotCount-9) ;iiii--){

							if (EmptyNum==100){
							
								ii = MinuteListNum + iiii;

								if ($("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length==0 || $("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="1" || $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()!="11"){
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

									if ($("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length==0 || $("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="1" || $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()!="11"){
										//alert(iiii + " : 빈 " + " : " + "#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii);
										//alert("1:"+$("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length);
										//alert("2:"+$("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val());
										//alert("3:"+$("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val());
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




					
					if (DenySelect==0){
						if (OldDivSlotID!=""){
							$("#"+OldDivSlotID).attr("class","teacher_time");
						}
							
						
						document.RegForm.TeacherID.value = TeacherID;
						document.RegForm.LeveltestTimeHour.value = HourNum;
						document.RegForm.LeveltestTimeMinute.value = MinuteNum;

						$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).attr("class","teacher_time active");
						OldDivSlotID = "Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum;
					}else{
						if (DenySelect==1){
							alert((ClassOrderTimeTypeID*10)+"분 레벨테스트 수업을 구성할 수 없습니다.");
						}else{
							alert("선택한 시간은 휴식시간 입니다.");
						}
					}
					
				}
			}
			
			window.onload = function(){
				GetTeacherList();
			}
			</script>			


            <ul class="level_teacher_select_list" id="DivTeacherListHTML">
			<!-- 강사 목록 -->
            </ul>

        </div>
    </section>

</div>



<script>
function OpenLeveltestReserveForm(){

	TeacherID = document.RegForm.TeacherID.value;
	LeveltestApplyDate = document.RegForm.LeveltestApplyDate.value;
	LeveltestTimeHour = document.RegForm.LeveltestTimeHour.value;
	LeveltestTimeMinute = document.RegForm.LeveltestTimeMinute.value;

	if (TeacherID=="" || LeveltestTimeHour=="" || LeveltestTimeMinute==""){
		alert("날짜와 시간을 선택해 주세요.");
	}else{
		<?if ($_LINK_MEMBER_ID_==""){?>
			alert("먼저 로그인 해주세요.");
		<?}else if ($_LINK_MEMBER_LEVEL_ID_!=19){?>
			alert("현재 학생 권한으로 로그인하지 않았습니다. 학생으로 로그인 후 신청해 주세요.");
		<?}else{?>
			var OpenUrl = "leveltest_reverve_form.php?TeacherID="+TeacherID+"&LeveltestApplyDate="+LeveltestApplyDate+"&LeveltestTimeHour="+LeveltestTimeHour+"&LeveltestTimeMinute="+LeveltestTimeMinute;

			$.colorbox({	
				href:OpenUrl
				,width:"95%" 
				,height:"95%"
				,maxWidth: "900"
				,maxHeight: "700"
				,title:""
				,iframe:true 
				,scrolling:true
				//,onClosed:function(){location.reload(true);}
				//,onComplete:function(){alert(1);}
			});
		<?}?>
 	}
}



function OpenTeacherVideo(TeacherID, TeacherVideoType, TeacherVideoCode) {

	var OpenUrl = "pop_video_player.php?TeacherID="+TeacherID+"&TeacherVideoType="+TeacherVideoType+"&TeacherVideoCode="+TeacherVideoCode;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "536"
		,title:""
		,iframe:true 
		,scrolling:false
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

</script>


<script language="javascript">
$('.toggle_navi.four .one').addClass('active');
$('.sub_visual_navi .three').addClass('active');

</script>


<?php
echo "\n";
echo $SubLayoutBottom;
echo "\n";
echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('./includes/common_analytics.php');
?>


<?
if ($_LINK_MEMBER_ID_!=""){

	$Sql = "select * from ClassOrders where MemberID=".$_LINK_MEMBER_ID_." and ClassProductID=2 and ClassOrderState>0";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$LeveltestClassOrderID = $Row["ClassOrderID"];

	if ($LeveltestClassOrderID){
?>
<script>
alert("이미 레벨테스트 신청 기록이 있습니다.\n\n관리자에게 문의해 주시기 바랍니다.");
history.go(-1);
</script>
<?
	}

}
?>


<?php
include_once('./includes/common_footer.php');

if (trim($SubLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $SubLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}

if (trim($MainLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $MainLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}
?>




</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>





