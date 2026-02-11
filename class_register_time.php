<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_03";
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
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_03_gumiivyleague)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_gumiivyleague_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.endedu.kr
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
$TodayWeekDay = date("w", strtotime(date("Y-m-d")));
$ClassOrderStartDate = date("Y-m-d", strtotime("+".(7-$TodayWeekDay)." days"));

$ArrClassOrderStartDate[0] = date("Y-m-d", strtotime("-".$TodayWeekDay." days"));
for ($ii=1;$ii<=3;$ii++){
	$ArrClassOrderStartDate[$ii] = date('Y-m-d', strtotime($ArrClassOrderStartDate[$ii-1]. ' +7 day'));
}
?>
<div class="sub_wrap bg_gray" style="border:0;">       
    <div class="sub_title_common_wrap"><h2 class="sub_title_common"><b>수강</b>신청</h2></div>

    <section class="mypage_wrap">
        <div class="mypage_area">

            <ul class="class_register_tab">
                <li><a href="class_register_teacher.php" class="TrnTag"><span class="register_img_1"></span>강사 먼저 선택</a></li>
                <li><a href="class_register_time.php" class="active TrnTag"><span class="register_img_2"></span>날짜 먼저 선택</a></li>
            </ul>

			<form name="RegForm" id="RegForm" style="display:none;">
			<input type="text" name="EduCenterID" id="EduCenterID" value="1">

			<input type="hidden" name="ClassWeekNum_0" id="ClassWeekNum_0" value="0">
			<input type="hidden" name="ClassWeekNum_1" id="ClassWeekNum_1" value="0">
			<input type="hidden" name="ClassWeekNum_2" id="ClassWeekNum_2" value="0">
			<input type="hidden" name="ClassWeekNum_3" id="ClassWeekNum_3" value="0">
			<input type="hidden" name="ClassWeekNum_4" id="ClassWeekNum_4" value="0">
			<input type="hidden" name="ClassWeekNum_5" id="ClassWeekNum_5" value="0">
			<input type="hidden" name="ClassWeekNum_6" id="ClassWeekNum_6" value="0">

			<input type="hidden" name="ClassStudyTime_0" id="ClassStudyTime_0" value="" style="height:40px;">
			<input type="hidden" name="ClassStudyTime_6" id="ClassStudyTime_6" value="" style="height:40px;">

			1:<input type="text" name="SelectSlotCode" id="SelectSlotCode" value="">
			2:<input type="text" name="SelectStudyTimeCode" id="SelectStudyTimeCode" value="">

			<input type="hidden" name="ClassOrderTimeSlotCount" id="ClassOrderTimeSlotCount" value="2"><!-- 슬랏수 , 한 수업에 10분 수업이 몇개? -->
			<input type="hidden" name="ClassOrderWeekCount" id="ClassOrderWeekCount" value="1"><!-- 주 수업 회수-->
			<input type="hidden" name="TeacherPayTypeItemCenterPriceX" id="TeacherPayTypeItemCenterPriceX" value="1"><!-- 강사에 따른 배수 -->
			</form>

            <!-- 강사 선택 -->
            <div class="mypage_inner">
                <h3 class="caption_left_common"><!--<b>키워드</b> 검색<span>* 아래 키워드를 선택하시면 해당 키워드의 강사들을 찾으 실 수 있습니다. (중복선택가능)</span>--></h3>
                
				<ul class="class_keyword_list noborder">
                    <li class="class_keyword_caption TrnTag">주간 시작일</li>                    
					<li class="class_start_day">
						<select id="ClassOrderStartDate" name="ClassOrderStartDate" class="class_days_time" style="width:100%;height:50px;border-radius:5px;font-size:16px;" onchange="GetTeacherList(0)"/>
							<?for ($ii=0;$ii<=3;$ii++) {?>
							<option value="<?=$ArrClassOrderStartDate[$ii]?>" <?if ($ClassOrderStartDate==$ArrClassOrderStartDate[$ii]){?>selected<?}?>><?=$ArrClassOrderStartDate[$ii]?></option>
							<?}?>
						</select>
					</li>
                </ul>

				<ul class="class_keyword_list">
					<li class="class_keyword_caption TrnTag">수강 시간</li>
                    <li id="SlotCount_2" onclick="ChClassOrderTimeSlotCount(2)" style="cursor:pointer;" class="active TrnTag">20분/회</li>
                    <li id="SlotCount_4" onclick="ChClassOrderTimeSlotCount(4)" style="cursor:pointer;" class="TrnTag">40분/회</li>
				</ul>
                
                <style>
                .class_days_time{width:100%; height:34px; vertical-align:top; box-shadow:none; display:block;}
                </style>
                
                <div class="class_days_wrap">
                    <h4 class="class_days_caption TrnTag">수업 요일</h4>
                    <div class="class_days_right">
                        <ul class="class_days_list">                        
                            <li id="SelectClassWeekNum_1" onclick="SelectClassWeekNum(1)" style="cursor:pointer;" class="TrnTag">월요일</li>
                            <li>
                                <input type="text" name="ClassStudyTime_1" id="ClassStudyTime_1" value="" class="class_days_time" style="width:100%;" onchange="GetTeacherList(0)">
                                <script>
                                    $(document).ready(function () {
                                        $("#ClassStudyTime_1").kendoTimePicker({
                                            format: "HH:mm",
                                            interval: 10,
                                            min: new Date(2000, 0, 1, 14, 0, 0),
                                            max: new Date(2000, 0, 1, 22, 50, 0)
                                        });
                                    });
                                </script>
                            </li>
                        </ul>
                        <ul class="class_days_list"> 
                            <li id="SelectClassWeekNum_2" onclick="SelectClassWeekNum(2)" style="cursor:pointer;" class="TrnTag">화요일</li>
                            <li>
                                <input type="text" name="ClassStudyTime_2" id="ClassStudyTime_2" value="" class="class_days_time" style="width:100%;" onchange="GetTeacherList(0)">
                                <script>
                                    $(document).ready(function () {
                                        $("#ClassStudyTime_2").kendoTimePicker({
                                            format: "HH:mm",
                                            interval: 10,
                                            min: new Date(2000, 0, 1, 14, 0, 0),
                                            max: new Date(2000, 0, 1, 22, 50, 0)
                                        });
                                    });
                                </script>
                            </li>
                        </ul>
                        <ul class="class_days_list">
                            <li id="SelectClassWeekNum_3" onclick="SelectClassWeekNum(3)" style="cursor:pointer;" class="TrnTag">수요일</li>
                            <li>
                                <input type="text" name="ClassStudyTime_3" id="ClassStudyTime_3" value="" class="class_days_time" style="width:100%;" onchange="GetTeacherList(0)">
                                <script>
                                    $(document).ready(function () {
                                        $("#ClassStudyTime_3").kendoTimePicker({
                                            format: "HH:mm",
                                            interval: 10,
                                            min: new Date(2000, 0, 1, 14, 0, 0),
                                            max: new Date(2000, 0, 1, 22, 50, 0)
                                        });
                                    });
                                </script>
                            </li>
                        </ul>
                        <ul class="class_days_list">
                            <li id="SelectClassWeekNum_4" onclick="SelectClassWeekNum(4)" style="cursor:pointer;" class="TrnTag">목요일</li>
                            <li>
                                <input type="text" name="ClassStudyTime_4" id="ClassStudyTime_4" value="" class="class_days_time" style="width:100%;" onchange="GetTeacherList(0)">
                                <script>
                                    $(document).ready(function () {
                                        $("#ClassStudyTime_4").kendoTimePicker({
                                            format: "HH:mm",
                                            interval: 10,
                                            min: new Date(2000, 0, 1, 14, 0, 0),
                                            max: new Date(2000, 0, 1, 22, 50, 0)
                                        });
                                    });
                                </script>
                            </li>
                        </ul>
                        <ul class="class_days_list">
                            <li id="SelectClassWeekNum_5" onclick="SelectClassWeekNum(5)" style="cursor:pointer;" class="TrnTag">금요일</li>
                            <li>
                                <input type="text" name="ClassStudyTime_5" id="ClassStudyTime_5" value="" class="class_days_time" style="width:100%;" onchange="GetTeacherList(0)">
                                <script>
                                    $(document).ready(function () {
                                        $("#ClassStudyTime_5").kendoTimePicker({
                                            format: "HH:mm",
                                            interval: 10,
                                            min: new Date(2000, 0, 1, 14, 0, 0),
                                            max: new Date(2000, 0, 1, 22, 50, 0)
                                        });
                                    });
                                </script>
                            </li>
                        </ul>
                    </div>
                </div>

				<script>

				function SelectClassWeekNum(WeekNum){
					if (document.getElementById("ClassWeekNum_"+WeekNum).value=="1"){
						document.getElementById("ClassWeekNum_"+WeekNum).value = "0";
						document.getElementById("SelectClassWeekNum_"+WeekNum).className = "";
						document.getElementById("ClassStudyTime_"+WeekNum).value = "";
						//document.getElementById("ClassStudyTime_"+WeekNum).disabled = true;
					}else{
						document.getElementById("ClassWeekNum_"+WeekNum).value = "1";
						document.getElementById("SelectClassWeekNum_"+WeekNum).className = "active";
						document.getElementById("ClassStudyTime_"+WeekNum).value = "14:00";
						//document.getElementById("ClassStudyTime_"+WeekNum).disabled = false;
					}

					ClassOrderWeekCount = 0;
					for (ii=0;ii<=6;ii++){
						if (document.getElementById("ClassWeekNum_"+ii).value=="1"){
							ClassOrderWeekCount = ClassOrderWeekCount + 1;
						}
					}

					document.RegForm.ClassOrderWeekCount.value = ClassOrderWeekCount;

					GetTeacherList(0);
				}


				function ChClassOrderTimeSlotCount(Cnt){
					document.getElementById("SlotCount_2").className="";
					document.getElementById("SlotCount_4").className="";

					document.getElementById("SlotCount_"+Cnt).className="active";
					document.RegForm.ClassOrderTimeSlotCount.value = Cnt;
				
					GetTeacherList(0);
				}


				function GetTeacherList(SetType){
					if (SetType==0){
						document.getElementById("BtnSearchTeacher").style.display = "";
						document.getElementById("DivSearchTeacherList").style.display = "none";
					}else{
						document.getElementById("BtnSearchTeacher").style.display = "none";
						document.getElementById("DivSearchTeacherList").style.display = "";

						document.getElementById("DivNoTeacherList").style.display = "none";
						

						EduCenterID = document.RegForm.EduCenterID.value;
						ClassOrderTimeSlotCount = document.RegForm.ClassOrderTimeSlotCount.value;
						ClassOrderStartDate = document.getElementById("ClassOrderStartDate").value;

						ClassWeekNum_0 = document.RegForm.ClassWeekNum_0.value;
						ClassWeekNum_1 = document.RegForm.ClassWeekNum_1.value;
						ClassWeekNum_2 = document.RegForm.ClassWeekNum_2.value;
						ClassWeekNum_3 = document.RegForm.ClassWeekNum_3.value;
						ClassWeekNum_4 = document.RegForm.ClassWeekNum_4.value;
						ClassWeekNum_5 = document.RegForm.ClassWeekNum_5.value;
						ClassWeekNum_6 = document.RegForm.ClassWeekNum_6.value;

						ClassStudyTime_0 = document.getElementById("ClassStudyTime_0").value;
						ClassStudyTime_1 = document.getElementById("ClassStudyTime_1").value;
						ClassStudyTime_2 = document.getElementById("ClassStudyTime_2").value;
						ClassStudyTime_3 = document.getElementById("ClassStudyTime_3").value;
						ClassStudyTime_4 = document.getElementById("ClassStudyTime_4").value;
						ClassStudyTime_5 = document.getElementById("ClassStudyTime_5").value;
						ClassStudyTime_6 = document.getElementById("ClassStudyTime_6").value;

						if (ClassWeekNum_1=="1" && ClassStudyTime_1==""){
							alert("월요일 수강시간을 선택하세요.");
						}else if (ClassWeekNum_2=="1" && ClassStudyTime_2==""){
							alert("화요일 수강시간을 선택하세요.");
						}else if (ClassWeekNum_3=="1" && ClassStudyTime_3==""){
							alert("수요일 수강시간을 선택하세요.");
						}else if (ClassWeekNum_4=="1" && ClassStudyTime_4==""){
							alert("목요일 수강시간을 선택하세요.");
						}else if (ClassWeekNum_5=="1" && ClassStudyTime_5==""){
							alert("금요일 수강시간을 선택하세요.");
						}else{

							document.getElementById("DivTeacherListHTML").innerHTML = "<div class=\"loading_wrap\"><div class=\"loading_inner\"><img src=\"images/loading.gif\" class=\"loading_img\">강사 정보를 불러오고 있습니다.</div></div>";
							
							url = "ajax_get_class_order_time_list.php"; 


							//window.open(url + "?EduCenterID="+EduCenterID+"&ClassOrderTimeSlotCount="+ClassOrderTimeSlotCount+"&ClassOrderStartDate="+ClassOrderStartDate+"&ClassWeekNum_0="+ClassWeekNum_0+"&ClassWeekNum_1="+ClassWeekNum_1+"&ClassWeekNum_2="+ClassWeekNum_2+"&ClassWeekNum_2="+ClassWeekNum_3+"&ClassWeekNum_4="+ClassWeekNum_4+"&ClassWeekNum_5="+ClassWeekNum_5+"&ClassWeekNum_6="+ClassWeekNum_6+"&ClassStudyTime_0="+ClassStudyTime_0+"&ClassStudyTime_1="+ClassStudyTime_1+"&ClassStudyTime_2="+ClassStudyTime_2+"&ClassStudyTime_3="+ClassStudyTime_3+"&ClassStudyTime_4="+ClassStudyTime_4+"&ClassStudyTime_5="+ClassStudyTime_5+"&ClassStudyTime_6="+ClassStudyTime_6, 'ajax_get_class_order_teacher_list');
							
							
							$.ajax(url, {
								data: {
									EduCenterID: EduCenterID,
									ClassOrderTimeSlotCount: ClassOrderTimeSlotCount,
									ClassOrderStartDate: ClassOrderStartDate,
									ClassWeekNum_0: ClassWeekNum_0,
									ClassWeekNum_1: ClassWeekNum_1,
									ClassWeekNum_2: ClassWeekNum_2,
									ClassWeekNum_3: ClassWeekNum_3,
									ClassWeekNum_4: ClassWeekNum_4,
									ClassWeekNum_5: ClassWeekNum_5,
									ClassWeekNum_6: ClassWeekNum_6,
									ClassStudyTime_0: ClassStudyTime_0,
									ClassStudyTime_1: ClassStudyTime_1,
									ClassStudyTime_2: ClassStudyTime_2,
									ClassStudyTime_3: ClassStudyTime_3,
									ClassStudyTime_4: ClassStudyTime_4,
									ClassStudyTime_5: ClassStudyTime_5,
									ClassStudyTime_6: ClassStudyTime_6
								},
								success: function (data) {

									TeacherListHTML = data.TeacherListHTML;
									document.getElementById("DivTeacherListHTML").innerHTML = TeacherListHTML;

									if (TeacherListHTML==""){
										document.getElementById("DivNoTeacherList").style.display = "";
									}

									/*
									$('.teacher_select_btn').click(function(e){		
										e.preventDefault();
										$('.teacher_select_btn').removeClass('active');		

										$('.teacher_time_wrap').stop().slideUp(200);		

										if(!$(this).parent().next().is(":visible"))
										{
											$(this).parent().next().stop().slideDown(200);
											$(this).addClass('active');	
										}	
									});
									*/

							
								},
								error: function () {
									alert('err1');
								} 
							});
						
						}

					}
				}
				
				window.onload = function(){
					GetTeacherList(0);
				}
				</script>

                <div class="class_teacher_search" style="display:none;">
                    <input type="text" name="" class="class_teacher_input" placeholder="검색어를 입력하세요.">
                    <a href="#"><img src="images/btn_zoom_black.png" alt="검색" class="class_teacher_search_btn"></a>
                </div>
				
                <a href="javascript:GetTeacherList(1);" class="teacher_search_btn" id="BtnSearchTeacher" style="display:none;">
                    <div class="teacher_search_btn_text TrnTag">강사찾기</div>
                </a>

				<span id="DivSearchTeacherList" style="display:none;">
                    <div class="level_application_text_box TrnTag">※ 강사선택 버튼을 클릭하신 후 수업을 예약하세요.</div>
					<h3 class="caption_left_common margin TrnTag"><b>강사</b> 선택</h3>
				
					<div class="teacher_select_none" id="DivNoTeacherList" style="display:none;">
						<img src="images/no_photo.png" class="teacher_select_none_img" alt="">
						<trn class="TrnTag">조건에 맞는 강사를 찾지 못했습니다.<br>조건을 다시 선택해 주세요.</trn>
					</div>
					
					<ul class="teacher_select_list" id="DivTeacherListHTML">
					<!-- 강사 목록 -->
					</ul>
				</span>
				


				<script>

				function ClassOrderSubmit(TeacherID, TeacherPayTypeItemID, SelectSlotCode, SelectStudyTimeCode){

					if (SelectSlotCode=="" || SelectStudyTimeCode==""){
						alert("요일과 시간을 선택해 주세요.");
					}else{

						document.RegForm.TeacherPayTypeItemCenterPriceX.value  = TeacherPayTypeItemID;
						document.RegForm.SelectSlotCode.value  = SelectSlotCode;
						document.RegForm.SelectStudyTimeCode.value  = SelectStudyTimeCode;

						<?if ($_LINK_MEMBER_ID_==""){?>
							alert("먼저 로그인 해주세요.");
						<?}else{?>	
							
							if (confirm("선택한 조건으로 수강신청을 진행하시겠습니까?")){

								ClassOrderWeekCountID = document.RegForm.ClassOrderWeekCount.value;
								ClassOrderTimeTypeID = document.RegForm.ClassOrderTimeSlotCount.value;
								SelectSlotCode = document.RegForm.SelectSlotCode.value;
								SelectStudyTimeCode = document.RegForm.SelectStudyTimeCode.value;
								ClassOrderStartDate = document.getElementById("ClassOrderStartDate").value;
								ClassProductID = 1;

								url = "ajax_set_class_order_slot.php";

								//location.href = url + "?ClassOrderWeekCountID="+ClassOrderWeekCountID+"&ClassOrderTimeTypeID="+ClassOrderTimeTypeID+"&ClassOrderStartDate="+ClassOrderStartDate+"&ClassProductID="+ClassProductID+"&SelectSlotCode="+SelectSlotCode+"&SelectStudyTimeCode="+SelectStudyTimeCode;
								$.ajax(url, {
									data: {
										ClassOrderWeekCountID: ClassOrderWeekCountID,
										ClassOrderTimeTypeID: ClassOrderTimeTypeID,
										ClassOrderStartDate: ClassOrderStartDate,
										ClassProductID: ClassProductID,
										SelectSlotCode: SelectSlotCode,
										SelectStudyTimeCode: SelectStudyTimeCode
									},
									success: function (data) {

										ClassOrderID = data.ClassOrderID;
										PayPreAction(ClassOrderID);

									},
									error: function () {

									}
								});

							}
						<?}?>
					}

				}


				function PayPreAction(ClassOrderID){
					url = "./ajax_set_class_order_pay.php";
					//location.href = url + "?ClassOrderID="+ClassOrderID;
					$.ajax(url, {
						data: {
							ClassOrderID: ClassOrderID,
							ClassOrderMode: "HOME"
						},
						success: function (data) {
							ClassOrderPayID = data.ClassOrderPayID;
							ClassOrderPayNumber = data.ClassOrderPayNumber;

							OpenPayForm(ClassOrderID, ClassOrderPayID, ClassOrderPayNumber);
						},
						error: function () {

						}
					});

				}


				function OpenPayForm(ClassOrderID, ClassOrderPayID, ClassOrderPayNumber){
					openurl = "./pop_class_order_pay_form.php?ClassOrderID="+ClassOrderID+"&ClassOrderPayID="+ClassOrderPayID+"&ClassOrderPayNumber="+ClassOrderPayNumber+"&ClassOrderMode=HOME";
					$.colorbox({	
						href:openurl
						,width:"95%" 
						,height:"80%"
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


            </div>

        </div>
    </section>

</div>


<script language="javascript">
$('.toggle_navi.three .three').addClass('active');
$('.sub_visual_navi .three').addClass('active');


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





