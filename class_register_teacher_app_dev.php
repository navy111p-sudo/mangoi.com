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
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
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

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="./js/jquery-confirm.min.js"></script>
<link href="./css/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>
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
    // $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    // $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
//    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
//    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.endedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
//    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
//    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));

} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }

echo "\n";
//echo $MainLayoutTop;
echo "\n";
//echo $SubLayoutTop;
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

<!-- 헤더(앱) 영역 -->
<header class="header_app_wrap">
    <h1 class="header_app_title">수강신청</h1>
    <a href="javascript:window.Exit=true" class="header_app_close header_app_gray"><img src="images/btn_app_close_black.png" class="icon"></a>
</header>
<div class="sub_wrap bg_gray padding_app" style="border:0;">
    <section class="mypage_wrap">
        <div class="mypage_area">

            <ul class="class_register_tab">
                <li><a href="class_register_teacher_app.php" class="active TrnTag"><span class="register_img_1"></span>강사 먼저 선택</a></li>
                <li><a href="class_register_time_app.php" class="TrnTag"><span class="register_img_2"></span>날짜 먼저 선택</a></li>
            </ul>

			<form name="RegForm" id="RegForm" style="display:none;">
			<input type="hidden" name="EduCenterID" id="EduCenterID" value="1">

			<input type="hidden" name="SearchSex" id="SearchSex" value="0">
			<input type="hidden" name="SearchChar" id="SearchChar" value="">
			<input type="hidden" name="SearchTeacherPayTypeItemID" id="SearchTeacherPayTypeItemID" value="1">

			<input type="hidden" name="SelectSlotCode" id="SelectSlotCode" value="">
			<input type="hidden" name="SelectStudyTimeCode" id="SelectStudyTimeCode" value="">

			<input type="hidden" name="ClassOrderTimeSlotCount" id="ClassOrderTimeSlotCount" value="2"><!-- 슬랏수 , 한 수업에 10분 수업이 몇개? -->
			<input type="hidden" name="ClassOrderWeekCount" id="ClassOrderWeekCount" value="1"><!-- 주 수업 회수-->
			<input type="hidden" name="TeacherPayTypeItemCenterPriceX" id="TeacherPayTypeItemCenterPriceX" value="1"><!-- 강사에 따른 배수 -->
			</form>

            <!-- 강사 선택 -->
            <div class="mypage_inner">
                <h3 class="caption_left_common"><b>키워드</b> 검색<span>* 아래 키워드를 선택하시면 해당 키워드의 강사들을 찾으 실 수 있습니다. (중복선택가능)</span></h3>
                


				<ul class="class_keyword_list">
                    <li class="class_keyword_caption gendar TrnTag">성별 선택</li>
                    <li id="Sex_1" onclick="ChSex(1)" class="TrnTag">남자</li>
                    <li id="Sex_2" onclick="ChSex(2)" class="TrnTag">여자</li>
                    <li id="Sex_0" onclick="ChSex(0)" class="active TrnTag">상관없음</li>
				</ul>
				<ul class="class_keyword_list noborder">
					<li class="class_keyword_caption nation TrnTag">강사 구분</li>
                    <li id="TeacherPayType_1" onclick="ChTeacherPayType(1)" class="active TrnTag">필리핀</li>
                    <li id="TeacherPayType_2" onclick="ChTeacherPayType(2)" class="TrnTag">미국/캐나다</li>
                </ul>
                
                <ul class="class_disposition_list noborder">
                    
					<?
					$Sql2 = "select 
									A.* 
							from TeacherCharacterItems A 
							where A.TeacherCharacterItemView=1 and A.TeacherCharacterItemState=1 
							order by A.TeacherCharacterItemOrder asc";
					$Stmt2 = $DbConn->prepare($Sql2);
					$Stmt2->execute();
					$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

					while($Row2 = $Stmt2->fetch()) {
						$TeacherCharacterItemID = $Row2["TeacherCharacterItemID"];
						$TeacherCharacterItemTitle = $Row2["TeacherCharacterItemTitle"];
					?>
					<li id="Char_<?=$TeacherCharacterItemID?>" onclick="ChChar(<?=$TeacherCharacterItemID?>)" class=""><?=$TeacherCharacterItemTitle?></li>
					<?
					}
					?>
                </ul>



				<ul class="class_keyword_list">
					<li class="class_keyword_caption time">수강 시간</li>
                    <li id="SlotCount_2" onclick="ChClassOrderTimeSlotCount(2)" class="active TrnTag">20분/회</li>
                    <li id="SlotCount_4" onclick="ChClassOrderTimeSlotCount(4)" class="TrnTag">40분/회</li>
                </ul>
                
                <ul class="class_keyword_list week noborder">
                    <li class="class_keyword_caption time">수업 회수</li>
                    <li id="WeekCount_1" onclick="ChClassOrderWeekCount(1)" class="active TrnTag">주1회</li>
                    <li id="WeekCount_2" onclick="ChClassOrderWeekCount(2)" class="TrnTag">주2회</li>
                    <li id="WeekCount_3" onclick="ChClassOrderWeekCount(3)" class="TrnTag">주3회</li>
					<li id="WeekCount_4" onclick="ChClassOrderWeekCount(4)" class="TrnTag">주4회</li>
					<li id="WeekCount_5" onclick="ChClassOrderWeekCount(5)" class="TrnTag">주5회</li>
                </ul>
				

				<ul class="class_keyword_list noborder">
                    <li class="class_keyword_caption time TrnTag">주간 시작일</li>
                    
					<li class="class_start_day">
						<select id="ClassOrderStartDate" name="ClassOrderStartDate" class="class_days_time" style="width:100%;height:50px;border-radius:5px;font-size:16px;" onchange="GetTeacherList(0,'')"/>
							<?for ($ii=0;$ii<=3;$ii++) {?>
							<option value="<?=$ArrClassOrderStartDate[$ii]?>" <?if ($ClassOrderStartDate==$ArrClassOrderStartDate[$ii]){?>selected<?}?>><?=$ArrClassOrderStartDate[$ii]?></option>
							<?}?>
						</select>
					</li>
                </ul>


				<script>
				function ChTeacherPayType(Cnt){
					document.getElementById("TeacherPayType_1").className="";
					document.getElementById("TeacherPayType_2").className="";

					document.getElementById("TeacherPayType_"+Cnt).className="active";
					document.RegForm.SearchTeacherPayTypeItemID.value = Cnt;
				
					GetTeacherList(0,'');
				}
				function ChClassOrderTimeSlotCount(Cnt){
					document.getElementById("SlotCount_2").className="";
					document.getElementById("SlotCount_4").className="";

					document.getElementById("SlotCount_"+Cnt).className="active";
					document.RegForm.ClassOrderTimeSlotCount.value = Cnt;
				
					GetTeacherList(0,'');
				}

				function ChClassOrderWeekCount(Cnt){
					document.getElementById("WeekCount_1").className="";
					document.getElementById("WeekCount_2").className="";
					document.getElementById("WeekCount_3").className="";
					document.getElementById("WeekCount_4").className="";
					document.getElementById("WeekCount_5").className="";

					document.getElementById("WeekCount_"+Cnt).className="active";
					document.RegForm.ClassOrderWeekCount.value = Cnt;
				
					GetTeacherList(0,'');
				}
				function ChSex(Sex){
					for (ii=0;ii<=2;ii++){
						document.getElementById("Sex_"+ii).className="";
					}
					document.getElementById("Sex_"+Sex).className="active";
					document.RegForm.SearchSex.value = Sex;
				
					GetTeacherList(0,'');
				}


				var ChCharActive=0;
				function ChChar(TeacherCharacterItemID){
					obj = document.getElementById("Char_"+TeacherCharacterItemID);
					if (obj.className=="active"){
						obj.className = "";
						ChCharActive--;
						SearchLoad=1;
					}else{
						if (ChCharActive>=3){
							SearchLoad=0;
						}else{
							obj.className = "active";
							ChCharActive++;
							SearchLoad=1;
						}
					}

					

					if (SearchLoad==0){
						$.alert({title: "안내", content:  ("3개까지 선택할 수 있습니다. 이미 선택한 항목을 해제해 주세요.")});
					}else{
						SearchChar = document.RegForm.SearchChar.value;
						if (SearchChar.indexOf("|"+TeacherCharacterItemID)<0){
							document.RegForm.SearchChar.value = SearchChar + "|" + TeacherCharacterItemID;
						}else{
							document.RegForm.SearchChar.value = SearchChar.replace("|"+TeacherCharacterItemID, "")
						}
						
						GetTeacherList(0,'');
					}
				}


				var GetTimeTeachers = "|";
				function GetTeacherList(SetType, SearchTeacherID){
					if (SetType==0){
						GetTimeTeachers = "|";
						document.getElementById("BtnSearchTeacher").style.display = "";
						document.getElementById("DivSearchTeacherList").style.display = "none";
					}else{

						if (GetTimeTeachers.indexOf("|"+SearchTeacherID+"|") == -1) {

							if (SearchTeacherID!=""){
								GetTimeTeachers = GetTimeTeachers + SearchTeacherID + "|";
							}
							
							document.getElementById("BtnSearchTeacher").style.display = "none";
							document.getElementById("DivSearchTeacherList").style.display = "";

							document.getElementById("DivNoTeacherList").style.display = "none";
							
							if (SearchTeacherID==""){
								document.getElementById("DivTeacherListHTML").innerHTML = "<div class=\"loading_wrap\"><div class=\"loading_inner\"><img src=\"images/loading.gif\" class=\"loading_img\">강사 정보를 불러오고 있습니다.</div></div>";
							}else{
								document.getElementById("DivTeacherTimeBox_"+SearchTeacherID).innerHTML = "<div style=\"width:100%;text-align:center;\"><img src=\"images/loading.gif\" class=\"loading_img\"></div>";
							}	
							document.RegForm.SelectSlotCode.value = "";
							document.RegForm.SelectStudyTimeCode.value = "";
							SelectedSlotCount = 0;
							
							url = "ajax_get_class_order_teacher_list.php"; 

							EduCenterID = document.RegForm.EduCenterID.value;
							SearchSex = document.RegForm.SearchSex.value;
							SearchChar = document.RegForm.SearchChar.value;
							SearchTeacherPayTypeItemID = document.RegForm.SearchTeacherPayTypeItemID.value;
							ClassOrderStartDate = document.getElementById("ClassOrderStartDate").value;
							//window.open(url + "?EduCenterID="+EduCenterID+"&SearchSex="+SearchSex+"&SearchChar="+SearchChar+"&SearchTeacherPayTypeItemID="+SearchTeacherPayTypeItemID+"&ClassOrderStartDate="+ClassOrderStartDate+"&SearchTeacherID="+SearchTeacherID, 'ajax_get_class_order_teacher_list');
							
							
							$.ajax(url, {
								data: {
									EduCenterID: EduCenterID,
									SearchSex: SearchSex,
									SearchChar: SearchChar,
									SearchTeacherPayTypeItemID: SearchTeacherPayTypeItemID,
									ClassOrderStartDate: ClassOrderStartDate,
									SearchTeacherID: SearchTeacherID
								},
								success: function (data) {

									ForceBlockSlotIDs = data.ForceBlockSlotIDs;
									TeacherListHTML = data.TeacherListHTML;
									TeacherBlock80Min = data.TeacherBlock80Min;//SearchTeacherID 가 있을때만 사용
									
									if (SearchTeacherID==""){
										document.RegForm.TeacherPayTypeItemCenterPriceX.value = data.TeacherPayTypeItemCenterPriceX;
										document.getElementById("DivTeacherListHTML").innerHTML = TeacherListHTML;

										if (TeacherListHTML==""){
											document.getElementById("DivNoTeacherList").style.display = "";
										}

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

									}else{
										document.getElementById("DivTeacherTimeBox_"+SearchTeacherID).innerHTML = TeacherListHTML;
									}



									ArrForceBlockSlotID = ForceBlockSlotIDs.split("|");
									for (ii=1;ii<=ArrForceBlockSlotID.length-2;ii++){
										//$("#"+ArrForceBlockSlotID[ii]).css('background-color', '#bbbbbb');
										$("#"+ArrForceBlockSlotID[ii]).css('display', 'none');

										$("#"+ArrForceBlockSlotID[ii].replace('Div_Slot_','Able_')).val('1');
										$("#"+ArrForceBlockSlotID[ii].replace('Div_Slot_','Break_')).val('101');
									}



								
									if (SearchTeacherID!=""){
										OpneTeacherTable(SearchTeacherID, TeacherBlock80Min);
									}
								},
								error: function () {
									//alert('err1');
								} 
							});
						}
					}
				}
				
				window.onload = function(){
					GetTeacherList(0,'');
				}
				</script>

                <div class="class_teacher_search" style="display:none;">
                    <input type="text" name="" class="class_teacher_input" placeholder="검색어를 입력하세요.">
                    <a href="#"><img src="images/btn_zoom_black.png" alt="검색" class="class_teacher_search_btn"></a>
                </div>
				
                <a href="javascript:GetTeacherList(1,'');" class="teacher_search_btn" id="BtnSearchTeacher" style="display:none;">
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

				var CheckTeacherBlock80Min_TeacherID = "|";

				function OpneTeacherTable(TeacherID, TeacherBlock80Min){

					ClassOrderTimeSlotCount = parseInt(document.RegForm.ClassOrderTimeSlotCount.value);

					if (TeacherBlock80Min==1){//원하는 시간 모두 선택가능할때 - 이렇게 추가할경우 80분 연속강의에 위배되지 않는지 검사(풀스캔) - 숨기기
						

						//if (CheckTeacherBlock80Min_TeacherID.indexOf("|"+TeacherID+"|") == -1) {

							//alert(TeacherID);
							CheckTeacherBlock80Min_TeacherID = CheckTeacherBlock80Min_TeacherID + TeacherID + "|";


							for (WeekDayNum=0;WeekDayNum<=6;WeekDayNum++){

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
											//DenySelect = 2;
										}

									}

									//alert(ActiveSlotCount);
								}
							}

						//}
					}

				}




				var SelectedSlotCount = 0;
				var ClassOrderTimeSlotCount = 2;
				var ClassOrderWeekCount = 1;

				function SelectSlot(TeacherID, WeekDayNum, MinuteListNum, TeacherBlock80Min){//TeacherBlock80Min - 1:80분 제한 / 0:제한안함

					ClassOrderWeekCount = parseInt(document.RegForm.ClassOrderWeekCount.value);
					ClassOrderTimeSlotCount = parseInt(document.RegForm.ClassOrderTimeSlotCount.value);

					if ($("#Able_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).val()=="1"){

						DenySelect = 0;
						
						SelectSlotCode = document.getElementById("SelectSlotCode").value;
						SelectStudyTimeCode = document.getElementById("SelectStudyTimeCode").value;
						
						SlotCode = "";
						StudyTimeCode = "";
						MinuteListCount=1;
						for (ii=MinuteListNum; ii<=MinuteListNum+ClassOrderTimeSlotCount-1; ii++){
							if (TeacherBlock80Min==1 && DenySelect == 0  && $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).length>0 && $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="101"){
								DenySelect = 2;
							}else if (DenySelect == 0 && ($("#Slot_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="0" || $("#Slot_"+TeacherID+"_"+WeekDayNum+"_"+ii).length==0)){
								DenySelect = 1;
							}else{
								SlotCode = SlotCode.concat($("#Slot_"+TeacherID+"_"+WeekDayNum+"_"+ii).val());
								
								if (MinuteListCount==1){
									StudyTimeCode = $("#Slot_"+TeacherID+"_"+WeekDayNum+"_"+ii).val();
								}else if (MinuteListCount==ClassOrderTimeSlotCount){
									ArrSlotCode = $("#Slot_"+TeacherID+"_"+WeekDayNum+"_"+ii).val().split('_');
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


					
						
						ArrStudyTimeCode = StudyTimeCode.split('_');
						CheckStudyTimeCode = ArrStudyTimeCode[0] + "_" +  ArrStudyTimeCode[1] + "_";

						if (SelectSlotCode.indexOf(SlotCode)<0 && SelectedSlotCount>=(ClassOrderWeekCount*ClassOrderTimeSlotCount)){
							$.alert({title: "안내", content: "주" + ClassOrderWeekCount + "회까지 선택할 수 있습니다."});
						}else if (SelectStudyTimeCode.indexOf(CheckStudyTimeCode)>=0 && SelectStudyTimeCode.indexOf(StudyTimeCode)<0){
							$.alert({title: "안내", content: "동일한 요일에 동일한 강사를 한번만 선택할 수 있습니다."});
						}else{
							if (DenySelect==1){
								$.alert({title: "안내", content: (ClassOrderTimeSlotCount*10) + "분 수업을 구성할 수 없습니다."});
							}else if (DenySelect==2){
								$.alert({title: "안내", content: "선택한 시간은 휴식시간 입니다."});//80분 이상 연속수업 금지 정책에 위배됩니다.
							}else{
								
								AbleNum = 0;
								for (ii=MinuteListNum; ii<=MinuteListNum+ClassOrderTimeSlotCount-1; ii++){
									
									if (SelectSlotCode.indexOf(SlotCode)>=0){
										//alert(2);
										document.getElementById("SelectSlotCode").value = SelectSlotCode.replace(SlotCode, ""); 
										document.getElementById("SelectStudyTimeCode").value = SelectStudyTimeCode.replace(StudyTimeCode, ""); 

										$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+ii).attr("class","teacher_time");

										SelectedSlotCount--;

										if (AbleNum>0){
											$("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val("1");
										}
									}else{
										//alert(1);
										document.getElementById("SelectSlotCode").value = SelectSlotCode + SlotCode;
										document.getElementById("SelectStudyTimeCode").value = SelectStudyTimeCode + StudyTimeCode;
										
										
										if (AbleNum==0){
											$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+ii).attr("class","teacher_time active");
										}else{
											$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+ii).attr("class","teacher_time active_sub");
										}

										SelectedSlotCount++;

										if (AbleNum>0){
											$("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val("0");
										}
									}
									
								
									AbleNum++;
								}
							}
						}
					}

				}

				</script>


            </div>

        </div>
    </section>

</div>


<script language="javascript">
$('.toggle_navi.three .three').addClass('active');
$('.sub_visual_navi .three').addClass('active');





function ClassOrderSubmit(){
	if (SelectedSlotCount < (ClassOrderWeekCount*ClassOrderTimeSlotCount)){
		$.alert({title: "안내", content: "주" + ClassOrderWeekCount + "회 선택하셔야 합니다."});
		
	}else{
		
		<?if ($_LINK_MEMBER_ID_==""){?>
			$.alert({title: "안내", content: "먼저 로그인 해주세요."});
		<?}else if ($_LINK_MEMBER_LEVEL_ID_!=19){?>
			$.alert({title: "안내", content: "현재 학생 권한으로 로그인하지 않았습니다. 학생으로 로그인 후 신청해 주세요."});
		<?}else{?>

			$.confirm({
				title: "안내",
				content: "선택한 조건으로 수강신청을 진행하시겠습니까?",
				buttons: {
					확인: function () {

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
				}
			});							

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
	openurl = "./pop_class_order_pay_form.php?ClassOrderID="+ClassOrderID+"&ClassOrderPayID="+ClassOrderPayID+"&ClassOrderPayNumber="+ClassOrderPayNumber+"&ClassOrderMode=HOME&FromDevice=app2";//FromDevice=app2 는 앱에서 신규신청 2020-08-01
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



var VideoWidth = "720";
var VideoHeight = "464";
var windowWidth = $( window ).width();
if(windowWidth < 780) {
	VideoWidth = "360";
	VideoHeight = "256";
}

function OpenTeacherVideo(TeacherID, TeacherVideoType, TeacherVideoCode) {
	if (TeacherVideoCode==""){
		$.alert({title: "안내", content: "소개영상 준비중 입니다."});
	}else{
		var OpenUrl = "pop_video_player.php?TeacherID="+TeacherID+"&TeacherVideoType="+TeacherVideoType+"&TeacherVideoCode="+TeacherVideoCode;

		$.colorbox({	
			href:OpenUrl
			,width:VideoWidth 
			,height:VideoHeight
			,title:""
			,iframe:true 
			,scrolling:false
			//,onClosed:function(){location.reload(true);}
			//,onComplete:function(){alert(1);}
		}); 
	}
}




history.pushState(null, null, location.href);
window.onpopstate = function(event) {

     history.go(1);
	 window.Exit=true;
     //alert("뒤로가기 버튼은 사용할 수 없습니다!");
};

</script>


<?php
echo "\n";
//echo $SubLayoutBottom;
echo "\n";
//echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('./includes/common_analytics.php');
?>


<?php
//include_once('./includes/common_footer.php');

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


<!-- ====    kendo -->
<link href="./kendo/styles/kendo.common.min.css" rel="stylesheet">
<link href="./kendo/styles/kendo.default.min.css" rel="stylesheet">
<script src="./kendo/js/kendo.web.min.js"></script>
<!-- ====    kendo   === -->


<!-- ====   Color Box -->
<?
$ColorBox = isset($ColorBox) ? $ColorBox : "";
if ($ColorBox==""){
	$ColorBox = "example2";
}
?>
<link rel="stylesheet" href="../js/colorbox/<?=$ColorBox?>/colorbox.css" />
<script src="../js/colorbox/jquery.colorbox.js"></script>
<script>
jQuery(document).ready(function(){
$(document).bind('cbox_open', function() {
    $('html').css({ overflow: 'hidden' });
}).bind('cbox_closed', function() {
    $('html').css({ overflow: '' });
});
});
</script>
<!-- ====   Color Box   === -->
</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>





