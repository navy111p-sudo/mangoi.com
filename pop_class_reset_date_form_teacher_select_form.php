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

} else if($DomainSiteID==9){ //live.engedu.kr
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
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$GroupRowCount = isset($_REQUEST["GroupRowCount"]) ? $_REQUEST["GroupRowCount"] : "";
$ClassMemberType = isset($_REQUEST["ClassMemberType"]) ? $_REQUEST["ClassMemberType"] : "";
$SetYear = isset($_REQUEST["SetYear"]) ? $_REQUEST["SetYear"] : "";
$SetMonth = isset($_REQUEST["SetMonth"]) ? $_REQUEST["SetMonth"] : "";
$SetDay = isset($_REQUEST["SetDay"]) ? $_REQUEST["SetDay"] : "";
$SetHour = isset($_REQUEST["SetHour"]) ? $_REQUEST["SetHour"] : "";
$SetMinute = isset($_REQUEST["SetMinute"]) ? $_REQUEST["SetMinute"] : "";
$SetWeek = isset($_REQUEST["SetWeek"]) ? $_REQUEST["SetWeek"] : "";
$ResetType = isset($_REQUEST["ResetType"]) ? $_REQUEST["ResetType"] : "";
$FromPage = isset($_REQUEST["FromPage"]) ? $_REQUEST["FromPage"] : "";
$IframeMode = isset($_REQUEST["IframeMode"]) ? $_REQUEST["IframeMode"] : "0";

$Sql = "
	select
		A.*,
		B.ClassOrderPayID,
		B.ClassProductID, 
		B.ClassOrderTimeTypeID,
		B.ClassMemberType,
		D.CenterPayType
	from Classes A 
		inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID 
		inner join Members C on A.MemberID=C.MemberID 
		inner join Centers D on C.CenterID=D.CenterID 
	where ClassID=$ClassID 
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ClassOrderID = $Row["ClassOrderID"];
$MemberID = $Row["MemberID"];
$TeacherID = $Row["TeacherID"];
$ClassOrderPayID = $Row["ClassOrderPayID"];
$ClassProductID = $Row["ClassProductID"];
$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
$ClassMemberType = $Row["ClassMemberType"];
$CenterPayType = $Row["CenterPayType"];
$StartDateTime = $Row["StartDateTime"];
$StartHour = $Row["StartHour"];
$StartMinute = $Row["StartMinute"];




if ($ResetType=="ChTeacher"){
	$StrResetType = "강사변경";
	$StudyTimeDate = substr($StartDateTime,0,10);
}else if ($ResetType=="PlusClass"){
	$StrResetType = "보강등록";
	$StudyTimeDate = date("Y-m-d", strtotime(substr($StartDateTime,0,10). " + 1 days"));
}else if ($ResetType=="EverChange"){
	$StrResetType = "스케줄변경";
	$StudyTimeDate = substr($StartDateTime,0,10);
	if ($SetWeek == "") $SetWeek = date('w', strtotime($StudyTimeDate));
	if ($SetHour == "") $SetHour = $StartHour;
	if ($SetMinute == "") $SetMinute = $StartMinute;
}else{
	$StrResetType = "연기";
	$StudyTimeDate = date("Y-m-d", strtotime(substr($StartDateTime,0,10). " + 1 days"));
}



?>

<!-- 헤더(앱) 영역 -->
<?if ($FromDevice=="app"){?>
<header class="header_app_wrap">
    <h1 class="header_app_title"><?=$StrResetType?> 신청</h1>
    <a href="javascript:window.Exit=true" class="header_app_close header_app_gray"><img src="images/btn_app_close_black.png" class="icon"></a>
</header>
<?}?>

<div class="sub_wrap bg_gray padding_app" style="border:0;<?if ($FromDevice==""){?>margin-top:-60px;<?}?>">   
    <section class="level_application_wrap">
        <div class="level_application_area">


			<div class="level_application_text_box" style="display:inline-block;margin-bottom:30px;width:49%;margin-right:1%;">
				강사먼저 선택
            </div>
			<div class="level_application_text_box" style="display:inline-block;margin-bottom:30px;width:49%;cursor:pointer;background-color:#cccccc;" onclick="ChResetSelectType(1)">
				날짜먼저 선택
            </div>
			
            <div class="teacher-search">
                <input id="teacherSearchInput" type="text" placeholder="강사 이름 검색" onkeyup="javascript:SearchTeacherName()" style="margin-bottom: 5px" />
                <div id="teacherSearchResultList" style="width: 160px; background-color:#ffffff; margin-bottom: 5px;"/>
            </div>

			<script>
			function ChResetSelectType(v){
				if (v==1){
					url = "pop_class_reset_date_form_date_select_form.php";
				}else{
					url = "pop_class_reset_date_form_teacher_select_form.php";
				}

				location.href = url+"?FromDevice=<?=$FromDevice?>&ClassID=<?=$ClassID?>&ClassOrderID=<?=$ClassOrderID?>&MemberID=<?=$MemberID?>&TeacherID=<?=$TeacherID?>&GroupRowCount=<?=$GroupRowCount?>&ClassMemberType=<?=$ClassMemberType?>&SetYear=<?=$SetYear?>&SetMonth=<?=$SetMonth?>&SetDay=<?=$SetDay?>&SetHour=<?=$SetHour?>&SetMinute=<?=$SetMinute?>&SetWeek=<?=$SetWeek?>&ResetType=<?=$ResetType?>&FromPage=<?=$FromPage?>&IframeMode=<?=$IframeMode?>";
			}
			</script>


  
			<form name="RegForm" id="RegForm" style="display:none;"> 

			<input type="hidden" name="FromPage" id="FromPage" value="<?=$FromPage?>">
			<input type="hidden" name="ClassOrderPayID" id="ClassOrderPayID" value="<?=$ClassOrderPayID?>">
			<input type="hidden" name="IframeMode" id="IframeMode" value="<?=$IframeMode?>">
			<input type="text" name="EduCenterID" id="EduCenterID" value="1">

			<input type="text" name="TeacherID" id="TeacherID" value="">
			<input type="text" name="StudyTimeDate" id="StudyTimeDate" value="<?=$StudyTimeDate?>">
			<input type="text" name="StudyTimeHour" id="StudyTimeHour" value="">
			<input type="text" name="StudyTimeMinute" id="StudyTimeMinute" value="">

			<input type="text" name="FromDevice" id="FromDevice" value="<?=$FromDevice?>">
			<input type="text" name="ResetType" id="ResetType" value="<?=$ResetType?>">
			<input type="text" name="ClassID" id="ClassID" value="<?=$ClassID?>">
			<input type="text" name="ClassOrderID" id="ClassOrderID" value="<?=$ClassOrderID?>">
			<input type="text" name="MemberID" id="MemberID" value="<?=$MemberID?>">
			<input type="text" name="ClassProductID" id="ClassProductID" value="<?=$ClassProductID?>">
			<input type="text" name="ClassMemberType" id="ClassMemberType" value="<?=$ClassMemberType?>">
			<input type="text" name="CenterPayType" id="CenterPayType" value="<?=$CenterPayType?>">
			<input type="text" name="ClassOrderTimeTypeID" id="ClassOrderTimeTypeID" value="<?=$ClassOrderTimeTypeID?>">

			<!-- 스케줄 변경의 경우 기존 슬랏 정보 -->
			<input type="text" name="SetTeacherID" id="SetTeacherID" value="<?=$TeacherID?>">
			<input type="text" name="SetYear" id="SetYear" value="<?=$SetYear?>">
			<input type="text" name="SetMonth" id="SetMonth" value="<?=$SetMonth?>">
			<input type="text" name="SetDay" id="SetDay" value="<?=$SetDay?>">
			<input type="text" name="SetHour" id="SetHour" value="<?=$SetHour?>">
			<input type="text" name="SetMinute" id="SetMinute" value="<?=$SetMinute?>">
			<input type="text" name="SetWeek" id="SetWeek" value="<?=$SetWeek?>">
			<!-- 스케줄 변경의 경우 기존 슬랏 정보 -->

			<input type="text" name="GroupRowCount" id="GroupRowCount" value="<?=$GroupRowCount?>">
			</form>

			<script>
			var OpneTeacherID = 0;
			var OpneTeacherBlock80Min = 0;
			// function OpneTeacherTable(TeacherID, TeacherBlock80Min, StudyTimeDate){
            //
			// 	WeekDayNum = new Date(StudyTimeDate).getDay();
            //
			// 	OpneTeacherID = TeacherID;
			// 	OpneTeacherBlock80Min = TeacherBlock80Min;
			// 	ClassOrderTimeSlotCount = ClassOrderTimeTypeID;//20, 40분
            //
			// 	if (TeacherBlock80Min==1){//원하는 시간 모두 선택가능할때 - 이렇게 추가할경우 80분 연속강의에 위배되지 않는지 검사(풀스캔) - 숨기기
			//
			// 		//alert(TeacherID);
			// 		//alert(StudyTimeDate);
			// 		//alert(TeacherBlock80Min);
            //
            //
			// 		for (MinuteListNum=1;MinuteListNum<=144;MinuteListNum++){//6*24 = 144
			//
			//
			// 			//위쪽으로 현재 선택을 포함하여 최대 9단계 올라가본다.
			// 			//올라가면서 빈슬랏이 나오면 빈슬랏의 번호를 딴다.
			// 			EmptyNum = 100;//일단 100으로 한다. 100은 아직 빈슬랏을 못찾은 경우이다.
			// 			for (iiii=-1;iiii>=(ClassOrderTimeSlotCount-9) ;iiii--){
            //
			// 				if (EmptyNum==100){
			//
			// 					ii = MinuteListNum + iiii;
            //
			// 					if ($("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length==0 || $("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="1" || $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()!="11"){
			// 						EmptyNum = ii+1;//빈슬랏 바로 아래 슬랏
			// 					}
            //
			// 				}
            //
			// 			}
            //
			// 			ActiveSlotCount = 0;
            //
			// 			//alert("시작점 : "+EmptyNum);
            //
			// 			if (EmptyNum==100){//빈슬랏을 못찾았다면 80분 위배이다.
			// 				$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).css('background-color', '#bbbbbb');
			// 				$("#Break_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).val("101");
			// 				//DenySelect = 2;
			// 			}else{
			//
			// 				//위에서 찾은 빈슬랏 바로 아래 부터 9개를 살펴본다.
			//
			// 				for (iiii=EmptyNum;iiii<=(8+EmptyNum);iiii++)	{
			//
			// 					//현재 선택한 슬랏은 빈슬랏이 아님으로 취급한다.
			// 					if ( (iiii-MinuteListNum)>=0 && (iiii-MinuteListNum)<=(ClassOrderTimeSlotCount-1) ){
			// 						ActiveSlotCount++;
            //
			// 						//alert(iiii + " : 자신 ");
			// 					}else{
			//
			// 						ii = iiii;
            //
			// 						if ($("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length==0 || $("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="1" || $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()!="11"){
			// 							//alert(iiii + " : 빈 " + " : " + "#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii);
			// 							//alert("1:"+$("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length);
			// 							//alert("2:"+$("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val());
			// 							//alert("3:"+$("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val());
			// 						}else{
			// 							ActiveSlotCount++;
			// 							//alert(iiii + " : 참 ");
			// 						}
			// 					}
			// 				}
            //
			// 				//채워진 슬랏이 8개 초과이면 80분 위배이다//숨긴다.
			// 				if (ActiveSlotCount>8){
			// 					$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).css('background-color', '#bbbbbb');
			// 					$("#Break_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).val("101");
			// 					//$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).css('display', 'none');//숨긴다
			// 					//DenySelect = 2;
			// 				}
            //
			// 			}
            //
			// 			//alert(ActiveSlotCount);
			// 		}
            //
            //
			//
			// 	}
            //
			// 	CheckAbleClassOrderTime(TeacherID, WeekDayNum);
            //
			// }

            // function OpneTeacherTable(TeacherID, TeacherBlock80Min, StudyTimeDate) {
            //     WeekDayNum = new Date(StudyTimeDate).getDay();
            //     OpneTeacherID = TeacherID;
            //     OpneTeacherBlock80Min = TeacherBlock80Min;
            //     ClassOrderTimeSlotCount = ClassOrderTimeTypeID; //20, 40분
            //
            //     if (TeacherBlock80Min == 1) { // 연속 80분 강의 검사 추가
            //         for (MinuteListNum = 1; MinuteListNum <= 144; MinuteListNum++) {
            //             // 기존 EmptyNum 검사 로직
            //             EmptyNum = 100;
            //             for (iiii = -1; iiii >= (ClassOrderTimeSlotCount - 9); iiii--) {
            //                 if (EmptyNum == 100) {
            //                     ii = MinuteListNum + iiii;
            //                     if ($("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).length == 0 ||
            //                         $("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() == "1" ||
            //                         $("#Break_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() != "11") {
            //                         EmptyNum = ii + 1;
            //                     }
            //                 }
            //             }
            //
            //             ActiveSlotCount = 0;
            //             if (EmptyNum == 100) { // 80분 위배되면 음영 처리
            //                 $("#Div_Slot_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum).css('background-color', '#bbbbbb');
            //                 $("#Break_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum).val("101");
            //             } else {
            //                 for (iiii = EmptyNum; iiii <= (8 + EmptyNum); iiii++) {
            //                     if ((iiii - MinuteListNum) >= 0 && (iiii - MinuteListNum) <= (ClassOrderTimeSlotCount - 1)) {
            //                         ActiveSlotCount++;
            //                     } else {
            //                         ii = iiii;
            //                         if ($("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).length == 0 ||
            //                             $("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() == "1" ||
            //                             $("#Break_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() != "11") {
            //                         } else {
            //                             ActiveSlotCount++;
            //                         }
            //                     }
            //                 }
            //                 if (ActiveSlotCount > 8) {
            //                     $("#Div_Slot_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum).css('background-color', '#bbbbbb');
            //                     $("#Break_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum).val("101");
            //                 }
            //             }
            //         }
            //     }
            //
            //     CheckAbleClassOrderTime(TeacherID, WeekDayNum);
            // }

            function OpneTeacherTable(TeacherID, TeacherBlock80Min, StudyTimeDate) {
                WeekDayNum = new Date(StudyTimeDate).getDay();
                OpneTeacherID = TeacherID;
                OpneTeacherBlock80Min = TeacherBlock80Min;
                ClassOrderTimeSlotCount = ClassOrderTimeTypeID; // 20, 40분

                // 전체 슬롯을 반복하여 음영 처리할 수 없는 부분을 먼저 걸러냅니다.
                for (MinuteListNum = 1; MinuteListNum <= 144; MinuteListNum++) {
                    DenySelect = 0;
                    for (ii = 0; ii < ClassOrderTimeSlotCount; ii++) {
                        TempMinuteListNum = MinuteListNum + ii;

                        // 해당 시간 슬롯이 비어있거나, 사용할 수 없는 경우 음영 처리 대상
                        if ($("#Able_" + TeacherID + "_" + WeekDayNum + "_" + TempMinuteListNum).length == 0 ||
                            $("#Able_" + TeacherID + "_" + WeekDayNum + "_" + TempMinuteListNum).val() != "1" ||
                            $("#Break_" + TeacherID + "_" + WeekDayNum + "_" + TempMinuteListNum).val() == "101") {
                            DenySelect = 1;
                            break;
                        }
                    }

                    // DenySelect가 1로 설정되었다면 해당 시간대를 음영 처리
                    if (DenySelect == 1) {
                        $("#Div_Slot_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum).css('background-color', '#bbbbbb');
                        $("#Break_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum).val("101");
                    }
                }

                // 추가적으로 80분 연속 강의 검사 로직도 포함
                if (TeacherBlock80Min == 1) {
                    for (MinuteListNum = 1; MinuteListNum <= 144; MinuteListNum++) {
                        EmptyNum = 100;
                        for (iiii = -1; iiii >= (ClassOrderTimeSlotCount - 9); iiii--) {
                            if (EmptyNum == 100) {
                                ii = MinuteListNum + iiii;
                                if ($("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).length == 0 ||
                                    $("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() == "1" ||
                                    $("#Break_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() != "11") {
                                    EmptyNum = ii + 1;
                                }
                            }
                        }

                        ActiveSlotCount = 0;
                        if (EmptyNum == 100) {
                            $("#Div_Slot_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum).css('background-color', '#bbbbbb');
                            $("#Break_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum).val("101");
                        } else {
                            for (iiii = EmptyNum; iiii <= (8 + EmptyNum); iiii++) {
                                if ((iiii - MinuteListNum) >= 0 && (iiii - MinuteListNum) <= (ClassOrderTimeSlotCount - 1)) {
                                    ActiveSlotCount++;
                                } else {
                                    ii = iiii;
                                    if ($("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).length == 0 ||
                                        $("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() == "1" ||
                                        $("#Break_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() != "11") {
                                        // pass
                                    } else {
                                        ActiveSlotCount++;
                                    }
                                }
                            }
                            if (ActiveSlotCount > 8) {
                                $("#Div_Slot_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum).css('background-color', '#bbbbbb');
                                $("#Break_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum).val("101");
                            }
                        }
                    }
                }
            }





            // function CheckAbleClassOrderTime(TeacherID, WeekDayNum){//20분 수업이 가능한지 체크
            //
            //
			// 	for (MinuteListNum=1;MinuteListNum<=144;MinuteListNum++){//6*24 = 144
			//
            //
			// 		//바로 다음 슬랏 부터 20, 40 수업이 가능한지 체크한다.
			// 		Able2040 = 1;
			// 		for (jjjj=1;jjjj<=ClassOrderTimeTypeID-1;jjjj++){
            //
			// 			ii=MinuteListNum+jjjj;
            //
			// 			if ($("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length!=0 && $("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="1" && $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="0"){
			// 				//수업가능
			// 			}else{
			// 				Able2040 = 0;
			// 			}
			// 		}
			//
			// 		if (Able2040==0){
			// 			//$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).css('background-color', '#ff0011');
			// 			$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).css('display', 'none');//숨긴다
			// 		}
            //
            //
			// 	}
            //
            //
			// }

            function CheckAbleClassOrderTime(TeacherID, WeekDayNum) {
                for (MinuteListNum = 1; MinuteListNum <= 144; MinuteListNum++) {
                    Able2040 = 1;
                    for (jjjj = 1; jjjj <= ClassOrderTimeTypeID - 1; jjjj++) {
                        ii = MinuteListNum + jjjj;
                        if ($("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).length != 0 &&
                            $("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() == "1" &&
                            $("#Break_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() == "0") {
                            // 가능
                        } else {
                            Able2040 = 0;
                        }
                    }
                    if (Able2040 == 0) {
                        $("#Div_Slot_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum).css('display', 'none');
                    }
                }
            }


            function OpenTeacherCalendar(SearchTeacherID, OpneTeacherBlock80Min){
				StudyTimeDate = document.RegForm.StudyTimeDate.value;
				TeacherChangeDate(StudyTimeDate, SearchTeacherID, OpneTeacherBlock80Min);
			}


            function TeacherChangeDate(StudyTimeDate, SearchTeacherID, OpneTeacherBlock80Min) {
                document.RegForm.StudyTimeDate.value = StudyTimeDate;
                document.RegForm.StudyTimeHour.value = "";

                var url = "ajax_get_class_reset_date_form_teacher_list.php";
                var EduCenterID = document.RegForm.EduCenterID.value;

                $.ajax(url, {
                    data: {
                        EduCenterID: EduCenterID,
                        StudyTimeDate: StudyTimeDate,
                        SearchTeacherID: SearchTeacherID,
                        ClassOrderTimeTypeID: "<?=$ClassOrderTimeTypeID?>",
                        ResetType: "<?=$ResetType?>",
                        ClassOrderID: "<?=$ClassOrderID?>"
                    },
                    success: function (data) {
                        var ForceBlockSlotIDs = data.ForceBlockSlotIDs;
                        var TeacherListHTML = data.TeacherListHTML;

                        document.getElementById("DivTeacherTimeHTML_" + SearchTeacherID).innerHTML = TeacherListHTML;

                        var ArrForceBlockSlotID = ForceBlockSlotIDs.split("|");
                        for (var ii = 1; ii <= ArrForceBlockSlotID.length - 2; ii++) {
                            $("#" + ArrForceBlockSlotID[ii]).css('display', 'none');
                            $("#" + ArrForceBlockSlotID[ii].replace('Div_Slot_', 'Able_')).val('1');
                            $("#" + ArrForceBlockSlotID[ii].replace('Div_Slot_', 'Break_')).val('101');
                        }

                        OpneTeacherTable(SearchTeacherID, OpneTeacherBlock80Min, StudyTimeDate);
                    },
                    error: function () {
                        // 에러 처리
                    }
                });
            }

			function GetTeacherList(){

				document.getElementById("DivTeacherListHTML").innerHTML = "<div style=\"text-align:center;margin-top:50px;\"><img src=\"images/loading.gif\"><br><span style=\"font-weight: 500; font-size: 18px;\">강사 목록을 불러오는 중입니다.</span></div>";
				document.RegForm.TeacherID.value = "";
				document.RegForm.StudyTimeHour.value = "";
				document.RegForm.StudyTimeMinute.value = "";

				url = "ajax_get_class_reset_date_form_teacher_list.php";

				EduCenterID = document.RegForm.EduCenterID.value;
				StudyTimeDate = document.RegForm.StudyTimeDate.value;
				//window.open(url + "?EduCenterID="+EduCenterID+"&StudyTimeDate="+StudyTimeDate+"&ClassOrderTimeTypeID=<?=$ClassOrderTimeTypeID?>", 'ajax_get_class_order_teacher_list');


				$.ajax(url, {
					data: {
						EduCenterID: EduCenterID,
						StudyTimeDate: StudyTimeDate,
						ClassOrderTimeTypeID: "<?=$ClassOrderTimeTypeID?>",
						ResetType: "<?=$ResetType?>",
						ClassOrderID: "<?=$ClassOrderID?>"
					},
					success: function (data) {

						ForceBlockSlotIDs = data.ForceBlockSlotIDs;
						TeacherListHTML = data.TeacherListHTML;

						document.getElementById("DivTeacherListHTML").innerHTML = TeacherListHTML;
                        teacherList = TeacherListHTML;

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
						//alert('err');
					}
				});
			}

            function SearchTeacherName(){
                let teacherSearchInput = document.getElementById('teacherSearchInput');
                let teacherSearchResultList = document.getElementById('teacherSearchResultList');

				//클라이언트에서 가공할만한 선생님 리스트 배열 없어서 수동으로 데이터 추가(현재 html 스트링으로 데이터 내려오고 있음)
			    const teacherNameList = [
                    {
                        name: 'Mariane',
                        id: 24
                    },
                    {
                        name: 'Rica',
                        id: 26
                    },
                    {
                        name: 'Gretchelle',
                        id: 29
                    },
                    {
                        name: 'Maj',
                        id: 34
                    },
                    {
                        name: 'Farrah',
                        id: 35
                    },
                    {
                        name: 'Faye',
                        id: 38
                    },
                    {
                        name: 'Donna',
                        id: 45
                    },
                    {
                        name: 'JP',
                        id: 60
                    },
                    {
                        name: 'Chaine',
                        id: 64
                    },
                    {
                        name: 'Junessa',
                        id: 68
                    },
                    {
                        name: 'Ann',
                        id: 115
                    },
                    {
                        name: 'Prince',
                        id: 134
                    },
                    {
                        name: 'Anin',
                        id: 146
                    },
                    {
                        name: 'Ash',
                        id: 153
                    },
                    {
                        name: 'Daisy',
                        id: 154
                    },
                    {
                        name: 'R',
                        id: 155
                    },
                    {
                        name: 'Apple',
                        id: 156
                    },
                    {
                        name: 'Darrel',
                        id: 158
                    },
                    {
                        name: 'Lyn',
                        id: 159
                    },
                    {
                        name: 'Kliv',
                        id: 163
                    },
                    {
                        name: 'Sol',
                        id: 164
                    },
                    {
                        name: 'Ryan',
                        id: 165
                    },
                    {
                        name: 'Jenny',
                        id: 166
                    },
                    {
                        name: 'Beth',
                        id: 171
                    },
                    {
                        name: 'Shan',
                        id: 172
                    },
                    {
                        name: 'Novy',
                        id: 173
                    },
                    {
                        name: 'Kes',
                        id: 174
                    },
                    {
                        name : 'Ness',
                        id: 68
                    },
                    {
                        name : 'Cindy',
                        id: 177
                    },
                    {
                        name : 'Janice',
                        id: 37
                    },
                    {
                        name : 'Reiza',
                        id: 44
                    },
                    {
                        name : 'Maimai',
                        id: 52
                    },
                    {
                        name : 'Junry',
                        id: 75
                    },
                    {
                        name : 'Melca',
                        id: 162
                    },
                    {
                        name : 'Mo',
                        id: 167
                    },
                    {
                        name : 'Eden',
                        id: 176
                    },
                    {
                        name : 'Mary',
                        id: 178
                    },
                ];
                const searchFunc = (teacherId) => {
                    let searchId = teacherSearchInput.value;
                    return teacherId.indexOf(searchId.toLowerCase()) !== -1;
                }
                const showFilteredTeacher = (teacher) => {
                    teacherSearchResultList.style.display = "block";
                    const teacherSearchResult = document.createElement("li");
                    teacherSearchResult.innerHTML = `
                    <div onclick="javascript:ClickSearchTeacherNameResult(${teacher.id}, ${teacher.openEightyMin})">
                        <li class="teacher-search-item" style="padding: 5px; border-bottom: 1px solid #e0e0e0">
                            ${teacher.name}
                        </li>
                    </div>`;
                    teacherSearchResultList.append(teacherSearchResult);
                };

                teacherSearchResultList.innerHTML = "";
                teacherSearchResultList.style.display = "none";
                // input 값이 있다면,
                if (teacherSearchInput.value) {
                    const filteredTeacher = teacherNameList.filter((teacher) => searchFunc(teacher.name.toLowerCase()));
                    if (filteredTeacher) {
                        filteredTeacher.forEach((teacher) => showFilteredTeacher(teacher));
                    }
                }
            }

            function ClickSearchTeacherNameResult(teacherId){
                const teacherSelector = document.getElementById('DivTeacherTimeHTML_' + teacherId)
                const location = teacherSelector.parentNode.offsetTop;

                window.scrollTo({top: location, behavior: 'smooth'})
			};

			var OldDivSlotID = "";
			var ClassOrderTimeTypeID = <?=$ClassOrderTimeTypeID?>;
			//function SelectSlot(TeacherID, HourNum, MinuteNum, WeekDayNum, MinuteListNum, TeacherBlock80Min){
			//
			//
			//	if (OldDivSlotID != "Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum){
			//
            //
			//		DenySelect = 0;
			//		for (ii=1;ii<=ClassOrderTimeTypeID-1;ii++){
			//
			//			TempMinuteListNum = MinuteListNum+ii;
            //
			//			if ( $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+TempMinuteListNum).length==0 ){
			//				DenySelect = 1;
			//			}else if ( $("#Able_"+TeacherID+"_"+WeekDayNum+"_"+TempMinuteListNum).val()!="1" ){
			//				DenySelect = 1;
			//			}
			//
			//		}
            //
            //
            //
			//		if (DenySelect == 0 && TeacherBlock80Min==1){//원하는 시간 모두 선택가능할때 - 이렇게 추가할경우 80분 연속강의에 위배되지 않는지 검사(풀스캔)
			//
			//			//위쪽으로 현재 선택을 포함하여 최대 9단계 올라가본다.
			//			//올라가면서 빈슬랏이 나오면 빈슬랏의 번호를 딴다.
			//			ClassOrderTimeSlotCount = ClassOrderTimeTypeID;//20, 40분
			//			EmptyNum = 100;//일단 100으로 한다. 100은 아직 빈슬랏을 못찾은 경우이다.
			//			for (iiii=-1;iiii>=(ClassOrderTimeSlotCount-9) ;iiii--){
            //
			//				if (EmptyNum==100){
			//
			//					ii = MinuteListNum + iiii;
            //
			//					if ($("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length==0 || $("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="1" || $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()!="11"){
			//						EmptyNum = ii+1;//빈슬랏 바로 아래 슬랏
			//					}
            //
			//				}
            //
			//			}
            //
			//			ActiveSlotCount = 0;
            //
			//			//alert("시작점 : "+EmptyNum);
            //
			//			if (EmptyNum==100){//빈슬랏을 못찾았다면 80분 위배이다.
			//				DenySelect = 2;
			//			}else{
			//
			//				//위에서 찾은 빈슬랏 바로 아래 부터 9개를 살펴본다.
			//
			//
            //
			//				for (iiii=EmptyNum;iiii<=(8+EmptyNum);iiii++)	{
			//
			//					//현재 선택한 슬랏은 빈슬랏이 아님으로 취급한다.
			//					if ( (iiii-MinuteListNum)>=0 && (iiii-MinuteListNum)<=(ClassOrderTimeSlotCount-1) ){
			//						ActiveSlotCount++;
            //
			//						//alert(iiii + " : 자신 ");
			//					}else{
			//
			//						ii = iiii;
            //
			//						if ($("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length==0 || $("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()=="1" || $("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val()!="11"){
			//							//alert(iiii + " : 빈 " + " : " + "#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii);
			//							//alert("1:"+$("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).length);
			//							//alert("2:"+$("#Able_"+TeacherID+"_"+WeekDayNum+"_"+ii).val());
			//							//alert("3:"+$("#Break_"+TeacherID+"_"+WeekDayNum+"_"+ii).val());
			//						}else{
			//							ActiveSlotCount++;
			//							//alert(iiii + " : 참 ");
			//						}
			//					}
			//				}
            //
			//				//채워진 슬랏이 8개 초과이면 80분 위배이다
			//				if (ActiveSlotCount>8){
			//					DenySelect = 2;
			//				}
            //
			//			}
            //
			//			//alert(ActiveSlotCount);
			//		}
            //
            //
            //
			//		if (DenySelect==0){
			//			if (OldDivSlotID!=""){
			//				$("#"+OldDivSlotID).attr("class","teacher_time");
			//			}
			//
			//
			//			document.RegForm.TeacherID.value = TeacherID;
			//			document.RegForm.StudyTimeHour.value = HourNum;
			//			document.RegForm.StudyTimeMinute.value = MinuteNum;
            //
			//			$("#Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum).attr("class","teacher_time active");
			//			OldDivSlotID = "Div_Slot_"+TeacherID+"_"+WeekDayNum+"_"+MinuteListNum;
			//		}else{
			//			if (DenySelect==1){
			//				<?//if ($FromDevice=="app"){?>
			//					$.alert({title: "안내", content: (ClassOrderTimeTypeID*10)+"분 수업을 구성할 수 없습니다."});
			//				<?//}else{?>
			//					alert((ClassOrderTimeTypeID*10)+"분 수업을 구성할 수 없습니다.");
			//				<?//}?>
			//			}else{
			//				<?//if ($FromDevice=="app"){?>
			//					$.alert({title: "선택한 시간은 휴식시간 입니다."});
			//				<?//}else{?>
			//					alert("선택한 시간은 휴식시간 입니다.");
			//				<?//}?>
			//
			//			}
			//		}
            //
			//
			//	}
			//}

            function SelectSlot(TeacherID, HourNum, MinuteNum, WeekDayNum, MinuteListNum, TeacherBlock80Min) {
                if (OldDivSlotID != "Div_Slot_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum) {
                    DenySelect = 0;
                    for (ii = 1; ii <= ClassOrderTimeTypeID - 1; ii++) {
                        TempMinuteListNum = MinuteListNum + ii;
                        if ($("#Break_" + TeacherID + "_" + WeekDayNum + "_" + TempMinuteListNum).length == 0 ||
                            $("#Able_" + TeacherID + "_" + WeekDayNum + "_" + TempMinuteListNum).val() != "1") {
                            DenySelect = 1;
                        }
                    }

                    if (DenySelect == 0 && TeacherBlock80Min == 1) {
                        ClassOrderTimeSlotCount = ClassOrderTimeTypeID;
                        EmptyNum = 100;
                        for (iiii = -1; iiii >= (ClassOrderTimeSlotCount - 9); iiii--) {
                            if (EmptyNum == 100) {
                                ii = MinuteListNum + iiii;
                                if ($("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).length == 0 ||
                                    $("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() == "1" ||
                                    $("#Break_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() != "11") {
                                    EmptyNum = ii + 1;
                                }
                            }
                        }

                        ActiveSlotCount = 0;
                        if (EmptyNum == 100) {
                            DenySelect = 2;
                        } else {
                            for (iiii = EmptyNum; iiii <= (8 + EmptyNum); iiii++) {
                                if ((iiii - MinuteListNum) >= 0 && (iiii - MinuteListNum) <= (ClassOrderTimeSlotCount - 1)) {
                                    ActiveSlotCount++;
                                } else {
                                    ii = iiii;
                                    if ($("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).length == 0 ||
                                        $("#Able_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() == "1" ||
                                        $("#Break_" + TeacherID + "_" + WeekDayNum + "_" + ii).val() != "11") {
                                    } else {
                                        ActiveSlotCount++;
                                    }
                                }
                            }
                            if (ActiveSlotCount > 8) {
                                DenySelect = 2;
                            }
                        }
                    }

                    if (DenySelect == 0) {
                        if (OldDivSlotID != "") {
                            $("#" + OldDivSlotID).attr("class", "teacher_time");
                        }
                        document.RegForm.TeacherID.value = TeacherID;
                        document.RegForm.StudyTimeHour.value = HourNum;
                        document.RegForm.StudyTimeMinute.value = MinuteNum;
                        $("#Div_Slot_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum).attr("class", "teacher_time active");
                        OldDivSlotID = "Div_Slot_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum;
                    } else {
                        $("#Div_Slot_" + TeacherID + "_" + WeekDayNum + "_" + MinuteListNum).css('background-color', '#bbbbbb');
                    }
                }
            }

            function CheckAllSlots() {
                // 전체 강사와 요일, 시간 슬롯을 순회하면서 음영 처리해야 할지 검사
                $('.level_teacher_time_table span').each(function (index, element) {
                    var onclickAttr = $(element).attr('onclick');

                    if (onclickAttr) {
                        // SelectSlot 함수의 파라미터 추출 (SelectSlot(24,14,0, 3, 1, 1);)
                        var argsMatch = onclickAttr.match(/SelectSlot\(([^)]+)\)/);
                        if (argsMatch && argsMatch[1]) {
                            var args = argsMatch[1].split(',').map(function (arg) {
                                return Number(arg.trim());
                            });

                            if (args.length >= 4) {
                                var TeacherID = args[0];
                                var WeekDayNum = args[1];
                                var MinuteListNum = args[2];
                                var TeacherBlock80Min = args[3];

                                var result = SelectSlotTest(TeacherID, WeekDayNum, MinuteListNum, TeacherBlock80Min);
                                if (result && result.type) {
                                    if (result.type === 1 || result.type === 2) {
                                        $(element).css('background-color', 'rgb(187, 187, 187)');
                                    }
                                }
                            }
                        }
                    }
                });
            }


            window.onload = function(){
				GetTeacherList();
                CheckAllSlots(); // 일정표가 처음 로드된 후 선택 불가한 슬롯들을 음영 처리합니다.

            }
			</script>


            <ul class="level_teacher_select_list" id="DivTeacherListHTML">
			<!-- 강사 목록 -->
            </ul>

        </div>
    </section>

</div>



<script>
function OpenResetReserveSubmit(){

	TeacherID = document.RegForm.TeacherID.value;
	StudyTimeDate = document.RegForm.StudyTimeDate.value;
	StudyTimeHour = document.RegForm.StudyTimeHour.value;
	StudyTimeMinute = document.RegForm.StudyTimeMinute.value;

	<?if ($FromDevice=="app"){?>
		if (TeacherID=="" || StudyTimeHour=="" || StudyTimeMinute==""){
			$.alert({title: "안내", content: "날짜와 시간을 선택해 주세요."});
		}else{
			
			<?if ($_LINK_MEMBER_ID_==""){?>
				$.alert({title: "안내", content: "먼저 로그인 해주세요."});
			<?}else{?>
				$.confirm({
					title: '안내',
					content: '선택한 교사의 날짜와 시간으로 <?=$StrResetType?> 하시겠습니까?',
					buttons: {
						확인: function () {
							document.RegForm.action = "pop_class_reset_date_form_teacher_select_action.php";
							document.RegForm.submit();
						},
						취소: function () {

						}
					}
				});

			
			<?}?>	
		}
	<?}else{?>
		if (TeacherID=="" || StudyTimeDate == "" || StudyTimeHour=="" || StudyTimeMinute==""){
			alert("날짜와 시간을 선택해 주세요.");
		}else{
			
			<?if ($_LINK_MEMBER_ID_==""){?>
				alert("먼저 로그인 해주세요.");
			<?}else{?>
				if (confirm("선택한 교사의 날짜와 시간으로 <?=$StrResetType?> 하시겠습니까?")){
					document.RegForm.action = "pop_class_reset_date_form_teacher_select_action.php";
					document.RegForm.submit();
				}
			<?}?>	
		}

	<?}?>

}


var VideoWidth = "720";
var VideoHeight = "464";
var windowWidth = $( window ).width();
if(windowWidth < 780) {
	VideoWidth = "360";
	VideoHeight = "256";
}



function OpenTeacherVideo(TeacherID, TeacherVideoType, TeacherVideoCode) {

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


history.pushState(null, null, location.href);
window.onpopstate = function(event) {

     history.go(1);
	 window.Exit=true;
     //alert("뒤로가기 버튼은 사용할 수 없습니다!");
};
</script>


<script language="javascript">
$('.toggle_navi.four .one').addClass('active');
$('.sub_visual_navi .three').addClass('active');

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





