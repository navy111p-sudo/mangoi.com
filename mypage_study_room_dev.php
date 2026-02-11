<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
$DenyGuest = true;
include_once('./includes/member_check.php');



$EduCenterID = 1;

$MemberLevelID = $_LINK_MEMBER_LEVEL_ID_;

if($MemberLevelID==12 || $MemberLevelID==13) {
    header("Location: mypage_teacher_mode.php");
}        

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_07_2";
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
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_07_1_gumiivyleague)}}"));
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


<div class="sub_wrap" style="margin-top:100px;">       
    <div class="sub_title_common_wrap" ><h2 class="sub_title_common TrnTag"><b>나의</b> 공부방</h2></div>
    
    <section class="mypage_wrap">
        <div class="mypage_area" >

            <?
            $HideLinkBtn = 1;
            include_once('mypage_student_info_include.php');
            ?>
            

            <!-- 달력 시작 -->


            <?
			$student_id = $_LINK_MEMBER_LOGIN_ID_;
			if ($DomainSiteID==1) { // SLP
				$cid =  "_eduvision_mangoi";
				$secret_key = "88f9c4204bddf494f349859f59a29cb5";
			} else if ($DomainSiteID==5) { // ENGLISHTELL
				$cid = "_eduvision_englishtell";
				$secret_key = "0bddcf68e3b724e022067b0784e48778";
			} else {
				$cid = "_eduvision_mangopie";
				$secret_key = "f32ebf51cd3324689a35c8ea02a3aed4";
			}
			include_once('./webook/_config.php');

			
            $ArrWeekDay = explode(",","Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday");

            $GetAllList = isset($_REQUEST["GetAllList"]) ? $_REQUEST["GetAllList"] : "";
			$SelectYear = isset($_REQUEST["SelectYear"]) ? $_REQUEST["SelectYear"] : "";
            $SelectMonth = isset($_REQUEST["SelectMonth"]) ? $_REQUEST["SelectMonth"] : "";
            if ($SelectYear==""){
                $SelectYear = date("Y");
            }
            if ($SelectMonth==""){
                $SelectMonth = date("m");
            }
			if ($SelectDay==""){
                $SelectDay = date("d");
            }

            $SelectYearMonth = $SelectYear."-".$SelectMonth."-01";
			$SelectYearMonthStartDay = 1;
			$SelectYearMonthEndDay = date("t", strtotime($SelectYearMonth));
			
            $PrevYear = date("Y", strtotime("-1 month", strtotime($SelectYearMonth)));
            $PrevMonth = date("m", strtotime("-1 month", strtotime($SelectYearMonth)));

            $NextYear = date("Y", strtotime("1 month", strtotime($SelectYearMonth)));
            $NextMonth = date("m", strtotime("1 month", strtotime($SelectYearMonth)));
            ?>
            <div class="schedule_wrap" style="margin-top:30px;">
                <h3 class="schedule_caption">
                    
					<a href="mypage_study_room.php?SelectYear=<?=$PrevYear?>&SelectMonth=<?=$PrevMonth?>"><img src="images/arrow_btn_left.png" class="schedule_left"></a>
                    <?=$SelectYear?>.<?=$SelectMonth?>
                    <a href="mypage_study_room.php?SelectYear=<?=$NextYear?>&SelectMonth=<?=$NextMonth?>"><img src="images/arrow_btn_right.png" class="schedule_right"></a>
                    
					
					<!--a href="mypage_monthly_report.php" class="button_black_yellow month">월별평가보고서</a-->
                </h3>
                <!--div class="schedule_btns_right">
                    <a href="lesson_videos.php" class="button_orange_white lesson">전체레슨비디오</a>
                    <a href="javascript:OpenCalTable('<?=(int)$SelectYear?>', '<?=(int)$SelectMonth?>')" class="button_whtie_border_arrow schedule">시간표 출력</a> 
                </div-->

				<!--
				<li><a href="javascript:OpenStudyVideo(<?=$OnlineSiteGuideVideoType?>, '<?=$OnlineSiteGuideVideoCode?>', 0, <?=$BookRegForReason?>);" class="color_lesson_video" style="background-color:#8000FF;">학습가이드</a></li>
				<li><a href="javascript:OpenStudyRuleInfo()" class="color_lesson_video" style="background-color:#566BA8;">수강규정안내</a></li>
				-->

				<?
				if ($SelectMonth == date("m") && $SelectYear == date("Y")){//현재달
					if ($GetAllList==""){
						$SelectYearMonthStartDay = date("j");
				?>
				<div style="width:100%;height:30px;text-align:center;background-color:#cccccc;color:#ffffff;border-radius:10px;margin-top:20px;margin-bottom:20px;line-height:30px;cursor:pointer;" onclick="location.href='mypage_study_room.php?GetAllList=1'" class="TrnTag">지난수업보기</div>
				<?
					}
				}
				?>


				<ul class="schedule_list">
					
					<?
					//=========================== test01 샘플 수업 ===========================
					if ($_LINK_MEMBER_LOGIN_ID_=="test01"){ 
					
							$Sql_Del = "delete from BookQuizResultDetails where BookQuizResultID in (select BookQuizResultID from BookQuizResults where ClassID=-1)";
							$Stmt_Del = $DbConn->prepare($Sql_Del);
							$Stmt_Del->execute();
							$Stmt_Del = null;
							
							$Sql_Del = "delete from BookQuizResults where ClassID=-1";
							$Stmt_Del = $DbConn->prepare($Sql_Del);
							$Stmt_Del->execute();
							$Stmt_Del = null;
					
					?>

						<li style='border:2px solid #FDC644'>
							<div class="schedule_inner">
								<div class="schedule_date"><?=$ArrWeekDay[date('w', strtotime($SelectYear."-".$SelectMonth."-".$SelectDay))]?><b><?=substr("0".$SelectMonth,-2)?>.<?=substr("0".$SelectDay,-2)?></b></div>
								<div class="schedule_bar"><img src="images/img_calendar_bar.png" class="bar"></div>
								<div class="schedule_class">
									<div class="schedule_teacher">Rica</div>
									<div class="schedule_english">
										<div class="schedule_english_left">샘플수업</div>
									</div>
								</div>
								<div class="schedule_time"><img src="images/icon_clock_black.png" class="icon">12:00 ~ 12:20</div>
							</div>
							<ul class="schedule_btns">
								
								<li style="width:100%;"><a href="javascript:OpenClassSh_Sample('-1', '37_20101101_20_10', 0, 'test01', 'test01');" class="color_classin">수업입장</a></li>
								
								<li><a href="javascript:OpenStudyVideo_Sample(1, 'e5-NTp_0vpQ', -1, 1);" class="color_lesson_video">레슨비디오 A</a></li> 
								<li><a href="javascript:OpenStudyVideo_Sample(1, 'bFyIyDpC10M', -1, 1);" class="color_lesson_video">레슨비디오 B</a></li> 
								<li><a href="javascript:OpenBookScan_Sample('7fb2e2349ff0cfa7e5a70015c93ddfd1.pdf',-1);" class="color_review_book">학습교재</a></li>
								<li><a href="javascript:OpenStudyQuiz_Sample(193, -1);" class="color_review_quiz">리뷰퀴즈</a></li>


								<li style="width:100%;" id="BtnResetDate"><a href="javascript:OpenResetDateForm_Sample(-1, 1);" class="color_postpone">수업연기요청</a></li>
								
								<li style="width:100%;"><a href="javascript:OpenStudentScoreDailyReport_Sample(-1);" class="color_review_quiz" style="background-color:#3D3D3D;">평가보고서</a></li>

								<li style="width:100%;"><a href="javascript:OpenTeacherScoreForm_Sample(-1);" class="color_review_quiz" style="background-color:#0080C0;">강사수업평가</a></li>

							</ul>
						</li>


						<script>

						function OpenClassSh_Sample(ClassID, CommonShClassCode, MemberType, MemberName, MemberLoginID){

							var FormData = document.getElementById("ShClassForm");
							FormData.userid.value = MemberLoginID;
							FormData.username.value = MemberName;
							FormData.usertype.value = MemberType;  // 강사,학생
							FormData.confcode.value = CommonShClassCode;
							
							var newwin = window.open("", "newwin", "toolbar=no,scrollbars=no,resizable=no,top=100,left=500,width=600,height=600");
							FormData.target = "newwin";
							FormData.submit();

						}


						function OpenStudyVideo_Sample(VideoType, VideoCode, ClassID, ClassVideoType) {// ClassVideoType - 1: A타입, 2: B타입

							var OpenUrl = "pop_video_player_study.php?VideoType="+VideoType+"&VideoCode="+VideoCode+"&ClassID="+ClassID+"&ClassVideoType="+ClassVideoType;

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


						function OpenStudyVideoErr_Sample(){
							alert("준비된 레슨 비디오가 없습니다.");
						}


						function OpenBookScan_Sample(BookScanImageFileName,ClassID) {
							var iframe = "<html><head><style>body, html {width: 100%; height: 100%; margin: 0; padding: 0}</style></head><body><iframe src='../ViewerJS/?zoom=page-width#/uploads/book_pdf_uploads/"+BookScanImageFileName+"' frameborder='0' style='height:calc(100% - 4px);width:calc(100% - 4px)'></iframe><input type='hidden' id='filename' value='"+BookScanImageFileName+"'></div></body></html>";

							var win = window.open("","coursebookcontent","frameborder=0,width="+screen.width+",height="+screen.height+",top=0,left=0,directories=no,location=no,channelmode=yes,fullscreen=yes,menubar=no, resizable=no,status=no,toolbar=no,history=no,scrollbars=yes");

							win.document.write(iframe);
						}

						function OpenStudyQuiz_Sample(BookQuizID, ClassID){

							var OpenUrl = "pop_quiz_study_preset.php?BookQuizID="+BookQuizID+"&ClassID="+ClassID+"&QuizStudyNumber=1";

							$.colorbox({    
								href:OpenUrl
								,width:"95%" 
								,height:"95%"
								,maxWidth: "1000"
								,maxHeight: "850"
								,title:""
								,iframe:true 
								,scrolling:true
								,onClosed:function(){location.reload(true);}
								//,onComplete:function(){alert(1);}
							}); 

						}



						function OpenResetDateForm_Sample(ClassID, ClassProductID){
				
							var OpenUrl = "pop_class_reset_date_form.php?ClassID="+ClassID+"&ClassProductID="+ClassProductID;

							$.colorbox({    
								href:OpenUrl
								,width:"95%" 
								,height:"95%"
								,maxWidth: "500"
								,maxHeight: "400"
								,title:""
								,iframe:true 
								,scrolling:true
								,onClosed:function(){location.reload(true);}
								//,onComplete:function(){alert(1);}
							}); 


						}

						function OpenStudentScoreDailyReport_Sample(ClassID){
							
							var OpenUrl = "./report_sample_daily.php?ClassID="+ClassID;

							$.colorbox({	
								href:OpenUrl
								,width:"95%" 
								,height:"95%"
								,maxWidth: "1200"
								,maxHeight: "700"
								,title:""
								,iframe:true 
								,scrolling:true
								//,onClosed:function(){location.reload(true);}
								//,onComplete:function(){alert(1);}
							}); 
						}


						function OpenTeacherScoreForm_Sample(ClassID){

							var OpenUrl = "pop_teacher_score_form.php?ClassID="+ClassID;

							$.colorbox({    
								href:OpenUrl
								,width:"95%" 
								,height:"95%"
								,maxWidth: "600"
								,maxHeight: "500"
								,title:""
								,iframe:true 
								,scrolling:true
								,onClosed:function(){location.reload(true);}
								//,onComplete:function(){alert(1);}
							}); 


						}
						</script>

					<?
					}
					//=========================== test01 샘플 수업 ===========================
					?>



					<!-- ============= LIST ============= -->
					<?
					$AddSqlWhere = " 1=1 ";
					$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotMaster=1 ";
					$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotState=1 ";

					$AddSqlWhere = $AddSqlWhere . " and CO.MemberID=".$_LINK_MEMBER_ID_." ";
					$AddSqlWhere = $AddSqlWhere . " and CO.ClassProgress=11 ";
					$AddSqlWhere = $AddSqlWhere . " and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=3 or CO.ClassOrderState=5 or CO.ClassOrderState=6) ";

					
					//=========================== AA ===========================
					
					$SetTargetStudy = 0;

					if ($GetAllList==""){

					}

					for ($iiii=$SelectYearMonthStartDay;$iiii<=$SelectYearMonthEndDay;$iiii++){
						
						$SelectDay = $iiii;
						$SelectDate = $SelectYear."-".$SelectMonth."-".substr("0".$SelectDay,-2);
						$SelectDateWeek = date("w", strtotime($SelectDate));
						
						$AddSqlWhereList = $AddSqlWhere . " and 
															( 
																(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

																or 
																(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

																or 
																(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

																or 
																(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

																or 
																(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SelectDate."')=0 )   
															)  
														";						

						$ViewTable = "
								select 
									COS.ClassMemberType,
									COS.ClassOrderSlotType,
									COS.ClassOrderSlotType2,
									COS.ClassOrderSlotDate,
									COS.TeacherID,
									COS.ClassOrderSlotMaster,
									COS.StudyTimeWeek,
									COS.StudyTimeHour,
									COS.StudyTimeMinute,
									COS.ClassOrderSlotState,
									concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0)) as ClassStartTime, 

									CO.ClassOrderID,
									CO.ClassProductID,
									CO.ClassOrderTimeTypeID,
									CO.MemberID,
									CO.ClassOrderStartDate,
									CO.ClassOrderEndDate,
									CO.ClassOrderState,

									ifnull(CLS.ClassID,0) as ClassID,
									CLS.TeacherID as ClassTeacherID,
									CLS.ClassLinkType,
									CLS.StartDateTime,
									CLS.StartDateTimeStamp,
									CLS.StartYear,
									CLS.StartMonth,
									CLS.StartDay,
									CLS.StartHour,
									CLS.StartMinute,
									CLS.EndDateTime,
									CLS.EndDateTimeStamp,
									CLS.EndYear,
									CLS.EndMonth,
									CLS.EndDay,
									CLS.EndHour,
									CLS.EndMinute,
									CLS.CommonUseClassIn,
									CLS.CommonShClassCode,
									CLS.CommonCiCourseID,
									CLS.CommonCiClassID,
									CLS.CommonCiTelephoneTeacher,
									CLS.CommonCiTelephoneStudent,
									ifnull(CLS.ClassAttendState,0) as ClassAttendState,
									CLS.ClassAttendStateMemberID,
									ifnull(CLS.ClassState,0) as ClassState,
									CLS.BookSystemType,
									CLS.BookWebookUnitID,
									CLS.BookVideoID,
									CLS.BookQuizID,
									CLS.BookScanID,
									ifnull(CLS.BookRegForReason,0) as BookRegForReason,
									CLS.ClassRegDateTime,
									CLS.ClassModiDateTime,

									MB.MemberName,
									MB.MemberPayType,
									MB.MemberNickName,
									MB.MemberLoginID, 
									MB.MemberLevelID,
									MB.MemberCiTelephone,

									TEA.TeacherName,
									MB2.MemberLoginID as TeacherLoginID, 
									MB2.MemberCiTelephone as TeacherCiTelephone,

									CT.CenterPayType,
									CT.CenterRenewType,
									CT.CenterStudyEndDate,
									
									TEA2.TeacherName as ClassTeacherName,
									
									BV.BookVideoType,
									BV.BookVideoCode,

									BV.BookVideoType2,
									BV.BookVideoCode2,
									
									BS.BookScanName,
									BS.BookScanImageFileName,

									ifnull(AD.AssmtStudentDailyScoreID,0) as AssmtStudentDailyScoreID,
									ifnull(AL.AssmtStudentLeveltestScoreID,0) as AssmtStudentLeveltestScoreID

								from ClassOrderSlots COS 
									left outer join Classes CLS on COS.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$SelectYear." and CLS.StartMonth=".$SelectMonth." and CLS.StartDay=".$SelectDay." and CLS.StartHour=COS.StudyTimeHour and CLS.StartMinute=COS.StudyTimeMinute and CLS.TeacherID=COS.TeacherID and CLS.ClassAttendState<>99 

									inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 

									

									inner join Members MB on CO.MemberID=MB.MemberID 
									inner join Centers CT on MB.CenterID=CT.CenterID 
									inner join Teachers TEA on COS.TeacherID=TEA.TeacherID 
									left outer join Teachers TEA2 on CLS.TeacherID=TEA2.TeacherID 
									inner join Members MB2 on TEA.TeacherID=MB2.TeacherID 

									left outer join BookVideos BV on CLS.BookVideoID=BV.BookVideoID 
									left outer join BookScans BS on CLS.BookScanID=BS.BookScanID 
									left outer join AssmtStudentDailyScores AD on CLS.ClassID=AD.ClassID 
									left outer join AssmtStudentLeveltestScores AL on CLS.ClassID=AL.ClassID 


								where ".$AddSqlWhereList." ";

									//결제 안해도 관리자가 등록한 수업 진행가능하도록 주석 처리
									//inner join ClassOrderPayDetails COPD on COS.ClassOrderID=COPD.ClassOrderID 
									//inner join ClassOrderPays COP on COPD.ClassOrderPayID=COP.ClassOrderPayID and (COP.ClassOrderPayProgress=21 or COP.ClassOrderPayProgress=31 or COP.ClassOrderPayProgress=41) 




						// 오늘 수업이면 등록을 해준다. ===========================================
						if ( date("Y-m-d")==$SelectYear."-".substr("0".$SelectMonth,-2)."-".substr("0".$SelectDay,-2) ){


							$SqlWhereCenterRenew = "";
							if ($NoIgnoreCenterRenew==1){
								$SqlWhereCenterRenew = " and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ";
							}

							$Sql3 = "
									select 
										V.*
									from ($ViewTable) V 
									where 

										V.ClassAttendState<4 

										and 
										
										(
											V.CenterPayType=1 and V.CenterRenewType=1 
											".$SqlWhereCenterRenew." 
											and V.MemberPayType=0 
											and (
													(V.ClassOrderState=1 or V.ClassOrderState=2 or V.ClassOrderState=5 or V.ClassOrderState=6) 
													or 
													(V.ClassOrderState=3 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0) 
												)
										) 
										or 
										(
											V.CenterPayType=1 and V.CenterRenewType=2 
											and V.MemberPayType=0 
											and (
													(V.ClassOrderState=1 or V.ClassOrderState=2 or V.ClassOrderState=5 or V.ClassOrderState=6) 
													or 
													(V.ClassOrderState=3 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0) 
												)
										)
										or 
										( 
											( V.CenterPayType=2 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
											or 
											( V.CenterPayType=1 and V.MemberPayType=1 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
										)
										or
										V.ClassProductID=2 
										or 
										V.ClassProductID=3 
										or 
										(V.ClassOrderSlotType=2 and datediff(V.ClassOrderSlotDate, '".$SelectDate."')=0) 
									order by V.StudyTimeHour asc, V.StudyTimeMinute, V.TeacherID asc
							";

							
							$Stmt3 = $DbConn->prepare($Sql3);
							$Stmt3->execute();
							$Stmt3->setFetchMode(PDO::FETCH_ASSOC);

							
							while($Row3 = $Stmt3->fetch()) {
								
								$ClassState = $Row3["ClassState"];
								$ClassOrderID = $Row3["ClassOrderID"];
								$StartHour = $Row3["StudyTimeHour"];
								$StartMinute = $Row3["StudyTimeMinute"];
								$ClassOrderTimeTypeID = $Row3["ClassOrderTimeTypeID"];
								$TeacherID = $Row3["TeacherID"];
								$MemberID = $Row3["MemberID"];

								if ($ClassState==0){

									$StartYear = $SelectYear;
									$StartMonth = $SelectMonth;
									$StartDay = $SelectDay;

									$Sql2 = "select 
											A.TeacherPayPerTime,
											A.TeacherName,
											B.MemberLoginID
										from 
											Teachers A 
												inner join Members B on A.TeacherID=B.TeacherID and B.MemberLevelID=15 
										where A.TeacherID=:TeacherID";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->bindParam(':TeacherID', $TeacherID);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
									$Row2 = $Stmt2->fetch();
									$Stmt = null;
									$TeacherPayPerTime = $Row2["TeacherPayPerTime"];
									$TeacherName = $Row2["TeacherName"];
									$TeacherLoginID = $Row2["MemberLoginID"];


									if ($ClassOrderTimeTypeID==2){
										$PlusMinute = 20;
									}else if ($ClassOrderTimeTypeID==3){
										$PlusMinute = 30;
									}else if ($ClassOrderTimeTypeID==4){
										$PlusMinute = 40;
									}

									$EndMinute = $StartMinute + $PlusMinute;
									if ($EndMinute>=60){
										$EndMinute = $EndMinute - 60;
										$EndHour = $StartHour + 1;
									}else{
										$EndHour = $StartHour;
									}


									//종로시간이 24를 넘어가면 23시 59분으로 맞춘다.
									if ($EndHour>=24){
										$EndHour = 23;
										$EndMinute = 59;
									}
									//종로시간이 24를 넘어가면 23시 59분으로 맞춘다.


									$EndYear = $StartYear;
									$EndMonth = $StartMonth;
									$EndDay = $StartDay;



									$Sql2 = "select 
												ClassID, 
												CommonShClassCode 
											from Classes 
											where 
												ClassOrderID=".$ClassOrderID." 
												and MemberID=".$MemberID."
												and TeacherID=".$TeacherID."
												and StartYear=".$StartYear."
												and StartMonth=".$StartMonth."
												and StartDay=".$StartDay."
												and StartHour=".$StartHour."
												and StartMinute=".$StartMinute."

												and EndYear=".$EndYear."
												and EndMonth=".$EndMonth."
												and EndDay=".$EndDay."
												and EndHour=".$EndHour."
												and EndMinute=".$EndMinute." 

												and ClassAttendState<>99 

										";

									//echo $Sql2;
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->bindParam(':TeacherID', $TeacherID);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
									$Row2 = $Stmt2->fetch();
									$Stmt2 = null;
									$ClassID = $Row2["ClassID"];
									$CommonShClassCode = $Row2["CommonShClassCode"];


									if (!$ClassID){

										$StartDate = $StartYear."-".substr("0".$StartMonth,-2)."-".substr("0".$StartDay,-2);
										$CommonShClassCode = $TeacherID."_". str_replace("-","",$StartDate) ."_".$StartHour."_".$StartMinute;

										$StartDateTime = $StartYear."-".substr("0".$StartMonth,-2)."-".substr("0".$StartDay,-2)." ".substr("0".$StartHour,-2).":".substr("0".$StartMinute,-2).":00";
										$EndDateTime   = $EndYear.  "-".substr("0".$EndMonth,  -2)."-".substr("0".$EndDay,-2  )." ".substr("0".$EndHour,-2  ).":".substr("0".$EndMinute,-2  ).":00";

										$StartDateTimeStamp = DateToTimestamp($StartDateTime, "Asia/Seoul");
										$EndDateTimeStamp =   DateToTimestamp($EndDateTime  , "Asia/Seoul");


										$Sql2 = " insert into Classes ( ";
											$Sql2 .= " ClassOrderID, ";
											$Sql2 .= " MemberID, ";
											$Sql2 .= " TeacherID, ";
											$Sql2 .= " TeacherPayPerTime, ";
											$Sql2 .= " StartDateTime, ";
											$Sql2 .= " StartDateTimeStamp, ";
											$Sql2 .= " StartYear, ";
											$Sql2 .= " StartMonth, ";
											$Sql2 .= " StartDay, ";
											$Sql2 .= " StartHour, ";
											$Sql2 .= " StartMinute, ";
											$Sql2 .= " EndDateTime, ";
											$Sql2 .= " EndDateTimeStamp, ";
											$Sql2 .= " EndYear, ";
											$Sql2 .= " EndMonth, ";
											$Sql2 .= " EndDay, ";
											$Sql2 .= " EndHour, ";
											$Sql2 .= " EndMinute, ";
											$Sql2 .= " CommonUseClassIn, ";
											$Sql2 .= " CommonShClassCode, ";
											$Sql2 .= " ClassRegDateTime, ";
											$Sql2 .= " ClassModiDateTime ";
										$Sql2 .= " ) values ( ";
											$Sql2 .= " :ClassOrderID, ";
											$Sql2 .= " :MemberID, ";
											$Sql2 .= " :TeacherID, ";
											$Sql2 .= " :TeacherPayPerTime, ";
											$Sql2 .= " :StartDateTime, ";
											$Sql2 .= " :StartDateTimeStamp, ";
											$Sql2 .= " :StartYear, ";
											$Sql2 .= " :StartMonth, ";
											$Sql2 .= " :StartDay, ";
											$Sql2 .= " :StartHour, ";
											$Sql2 .= " :StartMinute, ";
											$Sql2 .= " :EndDateTime, ";
											$Sql2 .= " :EndDateTimeStamp, ";
											$Sql2 .= " :EndYear, ";
											$Sql2 .= " :EndMonth, ";
											$Sql2 .= " :EndDay, ";
											$Sql2 .= " :EndHour, ";
											$Sql2 .= " :EndMinute, ";
											$Sql2 .= " 0, ";
											$Sql2 .= " :CommonShClassCode, ";
											$Sql2 .= " now(), ";
											$Sql2 .= " now() ";
										$Sql2 .= " ) ";

										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
										$Stmt2->bindParam(':MemberID', $MemberID);
										$Stmt2->bindParam(':TeacherID', $TeacherID);
										$Stmt2->bindParam(':TeacherPayPerTime', $TeacherPayPerTime);
										$Stmt2->bindParam(':StartDateTime', $StartDateTime);
										$Stmt2->bindParam(':StartDateTimeStamp', $StartDateTimeStamp);
										$Stmt2->bindParam(':StartYear', $StartYear);
										$Stmt2->bindParam(':StartMonth', $StartMonth);
										$Stmt2->bindParam(':StartDay', $StartDay);
										$Stmt2->bindParam(':StartHour', $StartHour);
										$Stmt2->bindParam(':StartMinute', $StartMinute);
										$Stmt2->bindParam(':EndDateTime', $EndDateTime);
										$Stmt2->bindParam(':EndDateTimeStamp', $EndDateTimeStamp);
										$Stmt2->bindParam(':EndYear', $EndYear);
										$Stmt2->bindParam(':EndMonth', $EndMonth);
										$Stmt2->bindParam(':EndDay', $EndDay);
										$Stmt2->bindParam(':EndHour', $EndHour);
										$Stmt2->bindParam(':EndMinute', $EndMinute);
										$Stmt2->bindParam(':CommonShClassCode', $CommonShClassCode);
										$Stmt2->execute();
										$ClassID = $DbConn->lastInsertId();
										$Stmt2 = null;
 
									}



								}

							}
							$Stmt3 = null;
						
						}
						// 오늘 수업이면 등록을 해준다. ===========================================



						$SqlWhereCenterRenew = "";
						if ($NoIgnoreCenterRenew==1){
							$SqlWhereCenterRenew = " and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ";
						}
						
						$Sql = "select count(*) TotalRowCount from 
									(select 
											count(*) 
									from ($ViewTable) V 


									where 
										V.ClassAttendState<4 
										
										and 
										
										(
											V.CenterPayType=1 and V.CenterRenewType=1 
											".$SqlWhereCenterRenew." 
											and V.MemberPayType=0 
											and (
													(V.ClassOrderState=1 or V.ClassOrderState=2 or V.ClassOrderState=5 or V.ClassOrderState=6) 
													or 
													(V.ClassOrderState=3 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0) 
												)
										) 
										or 
										(
											V.CenterPayType=1 and V.CenterRenewType=2 
											and V.MemberPayType=0 
											and (
													(V.ClassOrderState=1 or V.ClassOrderState=2 or V.ClassOrderState=5 or V.ClassOrderState=6) 
													or 
													(V.ClassOrderState=3 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0) 
												)
										)
										or 
										( 
											( V.CenterPayType=2 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
											or 
											( V.CenterPayType=1 and V.MemberPayType=1 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
										)
										or
										V.ClassProductID=2 
										or 
										V.ClassProductID=3 
										or 
										(V.ClassOrderSlotType=2 and datediff(V.ClassOrderSlotDate, '".$SelectDate."')=0) 
									) VV
								
								
								";

						$Stmt = $DbConn->prepare($Sql);
						$Stmt->execute();
						$Stmt->setFetchMode(PDO::FETCH_ASSOC);
						$Row = $Stmt->fetch();
						$Stmt = null;
						$TotalRowCount = $Row["TotalRowCount"];

						//echo "$$$".$TotalRowCount;
						//=========================== BB ===========================
						if ($TotalRowCount>0){


							$Sql = "
									select 
										V.*
									from ($ViewTable) V 
									where 
										V.ClassAttendState<4 

										and 
										
										(
											V.CenterPayType=1 and V.CenterRenewType=1 
											".$SqlWhereCenterRenew." 
											and V.MemberPayType=0 
											and (
													(V.ClassOrderState=1 or V.ClassOrderState=2 or V.ClassOrderState=5 or V.ClassOrderState=6) 
													or 
													(V.ClassOrderState=3 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0) 
												)
										) 
										or 
										(
											V.CenterPayType=1 and V.CenterRenewType=2 
											and V.MemberPayType=0 
											and (
													(V.ClassOrderState=1 or V.ClassOrderState=2 or V.ClassOrderState=5 or V.ClassOrderState=6) 
													or 
													(V.ClassOrderState=3 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0) 
												)
										)
										or 
										( 
											( V.CenterPayType=2 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
											or 
											( V.CenterPayType=1 and V.MemberPayType=1 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
										)
										or
										V.ClassProductID=2 
										or 
										V.ClassProductID=3 
										or 
										(V.ClassOrderSlotType=2 and datediff(V.ClassOrderSlotDate, '".$SelectDate."')=0) 
									order by V.StudyTimeHour asc, V.StudyTimeMinute, V.TeacherID asc
							";

							//echo $Sql;

							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);

							
							//=========================== CC ===========================
							
							

							//====================== 에듀센터 휴무 검색 ======================
							$TodayIsHoliday = 0;
							$Sql3 = "
								select 
									A.EduCenterHolidayID
								from EduCenterHolidays A
								where A.EduCenterHolidayDate=:EduCenterHolidayDate
									and A.EduCenterID=:EduCenterID
									and A.EduCenterHolidayState=1 
							";
							$Stmt3 = $DbConn->prepare($Sql3);
							$Stmt3->bindParam(':EduCenterHolidayDate', $SelectDate);
							$Stmt3->bindParam(':EduCenterID', $EduCenterID);
							$Stmt3->execute();
							$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
							$Row3 = $Stmt3->fetch();
							$Stmt3 = null;
							$EduCenterHolidayID = $Row3["EduCenterHolidayID"];
							if ($EduCenterHolidayID){
								
								$TodayIsHoliday = 1;
							
							}
							//====================== 에듀센터 휴무 검색 ======================




							while($Row = $Stmt->fetch()) {


								$ClassMemberType = $Row["ClassMemberType"];
								$ClassOrderSlotType = $Row["ClassOrderSlotType"];
								$ClassOrderSlotType2 = $Row["ClassOrderSlotType2"];
								$ClassOrderSlotDate = $Row["ClassOrderSlotDate"];
								$TeacherID = $Row["TeacherID"];
								$ClassOrderSlotMaster = $Row["ClassOrderSlotMaster"];
								$StudyTimeWeek = $Row["StudyTimeWeek"];
								$StudyTimeHour = $Row["StudyTimeHour"];
								$StudyTimeMinute = $Row["StudyTimeMinute"];
								$ClassOrderSlotState = $Row["ClassOrderSlotState"];
								$ClassStartTime = $Row["ClassStartTime"];

								$ClassOrderID = $Row["ClassOrderID"];
								$ClassProductID = $Row["ClassProductID"];
								$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
								$MemberID = $Row["MemberID"];
								
								$ClassID = $Row["ClassID"];
								$ClassLinkType = $Row["ClassLinkType"];
								$ClassTeacherID = $Row["ClassTeacherID"];
								
								$StartDateTimeStamp = $Row["StartDateTimeStamp"];
								$StartYear = $Row["StartYear"];
								$StartMonth = $Row["StartMonth"];
								$StartDay = $Row["StartDay"];
								$StartHour = $Row["StartHour"];
								$StartMinute = $Row["StartMinute"];

								$EndDateTimeStamp = $Row["EndDateTimeStamp"];
								$EndYear = $Row["EndYear"];
								$EndMonth = $Row["EndMonth"];
								$EndDay = $Row["EndDay"];
								$EndHour = $Row["EndHour"];
								$EndMinute = $Row["EndMinute"];

								$CommonUseClassIn = $Row["CommonUseClassIn"];
								$CommonShClassCode = $Row["CommonShClassCode"];
								$CommonCiCourseID = $Row["CommonCiCourseID"];
								$CommonCiClassID = $Row["CommonCiClassID"];
								$CommonCiTelephoneTeacher = $Row["CommonCiTelephoneTeacher"];
								$CommonCiTelephoneStudent = $Row["CommonCiTelephoneStudent"];

								$ClassAttendState = $Row["ClassAttendState"];
								$ClassAttendStateMemberID = $Row["ClassAttendStateMemberID"];
								$ClassState = $Row["ClassState"];

								$BookVideoID = $Row["BookVideoID"];
								$BookQuizID = $Row["BookQuizID"];
								$BookScanID = $Row["BookScanID"];
								$BookRegForReason = $Row["BookRegForReason"];

								$BookSystemType = $Row["BookSystemType"];
								$BookWebookUnitID = $Row["BookWebookUnitID"];

								$ClassRegDateTime = $Row["ClassRegDateTime"];
								$ClassModiDateTime = $Row["ClassModiDateTime"];

								$MemberName = $Row["MemberName"];
								$MemberLoginID = $Row["MemberLoginID"];
								$MemberCiTelephone = $Row["MemberCiTelephone"];

								$TeacherName = $Row["TeacherName"];
								$TeacherLoginID = $Row["TeacherLoginID"];
								$TeacherCiTelephone = $Row["TeacherCiTelephone"];

								$BookVideoType = $Row["BookVideoType"];
								$BookVideoCode = $Row["BookVideoCode"];

								$BookVideoType2 = $Row["BookVideoType2"];
								$BookVideoCode2 = $Row["BookVideoCode2"];


								$BookScanName = $Row["BookScanName"];
								$BookScanImageFileName = $Row["BookScanImageFileName"];

								$AssmtStudentDailyScoreID = $Row["AssmtStudentDailyScoreID"];
								$AssmtStudentLeveltestScoreID = $Row["AssmtStudentLeveltestScoreID"];


								if ($ClassOrderTimeTypeID==2){
									$PlusMinute = 20;
								}else if ($ClassOrderTimeTypeID==3){
									$PlusMinute = 30;
								}else if ($ClassOrderTimeTypeID==4){
									$PlusMinute = 40;
								}

								$StudyTimeEndMinute = $StudyTimeMinute + $PlusMinute;
								if ($StudyTimeEndMinute>=60){
									$StudyTimeEndMinute = $StudyTimeEndMinute - 60;
									$StudyTimeEndHour = $StudyTimeHour + 1;
								}else{
									$StudyTimeEndHour = $StudyTimeHour;
								}



								if ($CommonCiTelephoneStudent==""){
									$CommonCiTelephoneStudent = $MemberCiTelephone;
								}

								if ($CommonCiTelephoneTeacher==""){
									$CommonCiTelephoneTeacher = $TeacherCiTelephone;
								}


								if ($BookSystemType==0){//망고교재
									if ($BookScanID==0){
										$LinkScan = "OpenBookScanErr()";
									}else{
										$LinkScan = "OpenBookScan('".$BookScanImageFileName."',".$ClassID.")";
									}
								}else{//JT교재
									if ($BookWebookUnitID==""){
										$LinkScan = "OpenBookScanErr()";
									}else{
										$LinkScan = "OpenWebook('".$BookWebookUnitID."',".$ClassID.")";
									}
								}


								if ($BookVideoID==0 || $BookVideoCode==""){
									$LinkVideo = "OpenStudyVideoErr()";
								}else{
									$LinkVideo = "OpenStudyVideo(".$BookVideoType.", '".$BookVideoCode."', ".$ClassID.", 1, ".$BookRegForReason.")";
								}

								if ($BookVideoID==0 || $BookVideoCode2==""){
									$LinkVideo2 = "OpenStudyVideoErr()";
								}else{
									$LinkVideo2 = "OpenStudyVideo(".$BookVideoType2.", '".$BookVideoCode2."', ".$ClassID.", 2, ".$BookRegForReason.")";
								}

								if ($BookQuizID==0){
									$LinkQuiz = "OpenStudyQuizErr()";
								}else{
									$LinkQuiz = "OpenStudyQuiz(".$BookQuizID.", ".$ClassID.", ".$BookRegForReason.")";
								}


								$LinkTeacherScore = "OpenTeacherScoreForm(".$ClassID.")";


								if ($ClassProductID==1){
									$StrClassProductID = "<trn class=\"TrnTag\">정규수업</trn>";

									if ($ClassOrderSlotType==2){
										$StrClassProductID = "<trn class=\"TrnTag\">임시수업</trn>";
										if ($ClassOrderSlotType2==4){
											$StrClassProductID = "<trn class=\"TrnTag\">연기수업</trn>";
										}else if ($ClassOrderSlotType2==5){
											$StrClassProductID = "<trn class=\"TrnTag\">연기수업</trn>";
										}else if ($ClassOrderSlotType2==8){
											$StrClassProductID = "<trn class=\"TrnTag\">변경수업</trn>";
										}else if ($ClassOrderSlotType2==10000){
											$StrClassProductID = "<trn class=\"TrnTag\">보강수업</trn>";
										}else if ($ClassOrderSlotType2==20000){
											$StrClassProductID = "<trn class=\"TrnTag\">스케줄변경</trn>";//안나옴 ClassOrderSlotType=1 일때만 생성됨
										}	
									}
								}else if ($ClassProductID==2){
									$StrClassProductID = "<trn class=\"TrnTag\">레벨테스트</trn>";
								}else if ($ClassProductID==3){
									$StrClassProductID = "<trn class=\"TrnTag\">체험수업</trn>";
								}




								
								if ($ClassAttendState==4){
									$StrClassAttendState = "<trn class=\"TrnTag\">연기된수업</trn>";//학생연기
								}else if ($ClassAttendState==5){
									$StrClassAttendState = "<trn class=\"TrnTag\">연기된수업</trn>";//강사연기
								}else if ($ClassAttendState==6){
									$StrClassAttendState = "<trn class=\"TrnTag\">취소된수업</trn>";//학생취소
								}else if ($ClassAttendState==7){
									$StrClassAttendState = "<trn class=\"TrnTag\">취소된수업</trn>";//강사취소
								}else if ($ClassAttendState==8){
									$StrClassAttendState = "<trn class=\"TrnTag\">교사변경수업</trn>";//교사변경수업
								}else{

								}
								
								$StudyDateNum = $SelectYear . substr("0".$SelectMonth,-2) . substr("0".$SelectDay,-2);
								$CurrnetDateNum = date("Ymd");


								if ($StudyDateNum>=$CurrnetDateNum && $SelectYear==date("Y") && $SelectMonth==date("m") && $SetTargetStudy==0){
									$LiStyle = " style='border:2px solid #FDC644' ";
									$SetTargetStudy=1;
								}else{
									$LiStyle = " ";
								}

								if ($StudyDateNum==$CurrnetDateNum){//오늘
									$CurrentDateType = 0;
								}else if ($StudyDateNum>$CurrnetDateNum){//오늘 이후
									$CurrentDateType = 1;
								}else if ($StudyDateNum<$CurrnetDateNum){//오늘 이전
									$CurrentDateType = -1;
								}


								if ($BookRegForReason==1){//퀴즈, 비디오 랜덤
									
									$Sql2 = "select BookQuizID from BookQuizs where BookQuizView=1 and BookQuizState=1 order by rand() limit 0,1";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
									$Row2 = $Stmt2->fetch();
									$BookQuizID = $Row2["BookQuizID"];
									
									$LinkQuiz = "OpenStudyQuiz(".$BookQuizID.", ".$ClassID.", ".$BookRegForReason.")";


									$Sql2 = "select * from BookVideos where BookVideoView=1 and BookVideoState=1 order by rand() limit 0,1";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
									$Row2 = $Stmt2->fetch();
									$BookVideoType = $Row2["BookVideoType"];
									$BookVideoType2 = $Row2["BookVideoType2"];
									$BookVideoCode = $Row2["BookVideoCode"];
									$BookVideoCode2 = $Row2["BookVideoCode2"];
									
									if ($BookVideoCode==""){
										$LinkVideo = "OpenStudyVideoErr()";
									}else{
										$LinkVideo = "OpenStudyVideo(".$BookVideoType.", '".$BookVideoCode."', ".$ClassID.", 1, ".$BookRegForReason.")";
									}

									if ($BookVideoCode2==""){
										$LinkVideo2 = "OpenStudyVideoErr()";
									}else{
										$LinkVideo2 = "OpenStudyVideo(".$BookVideoType2.", '".$BookVideoCode2."', ".$ClassID.", 2, ".$BookRegForReason.")";
									}

								}







					?>

								<li <?=$LiStyle?>>
									<div class="schedule_inner">
										<div class="schedule_date"><?=$ArrWeekDay[date('w', strtotime($SelectYear."-".$SelectMonth."-".$SelectDay))]?><b><?=substr("0".$SelectMonth,-2)?>.<?=substr("0".$SelectDay,-2)?></b></div>
										<div class="schedule_bar"><img src="images/img_calendar_bar.png" class="bar"></div>
										<div class="schedule_class">
											<div class="schedule_teacher"><?=$TeacherName?></div>
											<div class="schedule_english">
												<div class="schedule_english_left"><?=$StrClassProductID?></div>
												<!--<div class="schedule_english_right">고정</div>-->
											</div>
										</div>
										<!--<div class="schedule_time"><img src="images/icon_clock_black.png" class="icon">PM 06:00 ~ 06:50</div>-->
										<div class="schedule_time"><img src="images/icon_clock_black.png" class="icon"><?=substr("0".$StudyTimeHour,-2)?>:<?=substr("0".$StudyTimeMinute,-2)?> ~ <?=substr("0".$StudyTimeEndHour,-2)?>:<?=substr("0".$StudyTimeEndMinute,-2)?></div>
									</div>
									<ul class="schedule_btns">
										<?if ($TodayIsHoliday==1 && $ClassState!=2){?>
											<li style="width:100%;">
												<a class="color_classin TrnTag" style="background-color:#888888;">휴무일 - 연기처리(대상)</a>
											</li>
										<?}else{?>

											<?
											//=========================== DD ===========================
											if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==8){//연기, 취소, 교사변경
											?>
												<li style="width:100%;">
													<a class="color_classin" style="background-color:#888888;"><?=$StrClassAttendState?></a>
												</li>
											<?
											//=========================== DD ===========================
											}else{
											?>

												<?if ($ClassState==0){//등록완료 0:미등록 전 1:등록완료 2:수업완료?>
													<?if ($CurrentDateType==0 || $CurrentDateType>0){?>
														<li style="width:100%;"><a class="color_classin TrnTag" style="background-color:#F3A39E;">수업준비중</a></li>
													<?}else{?>
														<li style="width:100%;"><a class="color_classin TrnTag" style="background-color:#888888;">경과된수업</a></li>
													<?}?>
												<?}else{//등록완료 0:미등록 전 1:등록완료 2:수업완료?>
													
													<?if ($ClassState==2){//등록완료 0:미등록 전 1:등록완료 2:수업완료?>
															<li style="width:100%;"><a class="color_classin TrnTag" style="background-color:#888888;">종료된수업</a></li>
													<?}else{?>
														<?if ($CurrentDateType==0 || $CurrentDateType>0){?>
															<?if ($ClassLinkType==1){?>
																<li style="width:100%;"><a href="javascript:OpenClassShPreSet('<?=$ClassID?>', '<?=$CommonShClassCode?>', 0, '<?=$MemberName?>', '<?=$MemberLoginID?>');" class="color_classin TrnTag">수업입장</a></li>
															<?}else{?>
																<li style="width:100%;"><a href="javascript:OpenClassCiCheckPreSet('<?=$ClassID?>', '<?=$CommonCiTelephoneTeacher?>', '<?=$CommonCiTelephoneStudent?>', 1, '<?=$MemberName?>', 'MangoiClass_<?=$ClassID?>');" class="color_classin TrnTag">수업입장</a></li>
															<?}?>
														<?}else{?>
															<li style="width:100%;"><a class="color_classin TrnTag" style="background-color:#888888;">경과된수업</a></li>
														<?}?>
													<?}?>
													
													<?if ($ClassProductID!=2){?>
														<li><a href="javascript:<?=$LinkVideo?>;" class="color_lesson_video TrnTag">레슨비디오 A</a></li> 
														<li><a href="javascript:<?=$LinkVideo2?>;" class="color_lesson_video TrnTag">레슨비디오 B</a></li> 
														<li><a href="javascript:<?=$LinkScan?>;" class="color_review_book TrnTag">학습교재</a></li>
														<li><a href="javascript:<?=$LinkQuiz?>;" class="color_review_quiz TrnTag">리뷰퀴즈</a></li>
													<?}?>

												<?}?>
												

												<?if ($CurrentDateType==0 || $CurrentDateType>0){?>

													<?if ($ClassID==0){?>
														
														<?if ($ClassMemberType==1){// 1:1 수업?>	
															<?if($MemberID==13591){// anlab97 학생 말아달라고 요청 by 강주임 200107?>
																<li style="width:100%;" id="BtnResetDate"><a class="color_postpone TrnTag">수업연기요청</a></li>
															<?}else{?>
																<li style="width:100%;" id="BtnResetDate"><a href="javascript:OpenResetDateFormWithReg(<?=$ClassOrderID?>, <?=$SelectYear?>, <?=(int)$SelectMonth?>, <?=(int)$SelectDay?>, <?=(int)$StudyTimeHour?>, <?=(int)$StudyTimeMinute?>,	<?=$ClassOrderTimeTypeID?>, <?=$TeacherID?>, <?=$MemberID?>, <?=$ClassMemberType?>, <?=$ClassProductID?>);" class="color_postpone TrnTag">수업연기요청</a></li>
															<?}?>
														<?}else{?>
															<?if($MemberID==13591){// anlab97 학생 말아달라고 요청 by 강주임 200107?>
																<li style="width:100%;" id="BtnResetDate"><a  class="color_postpone TrnTag">수업연기요청</a></li>
															<?}else{?>
																<li style="width:100%;" id="BtnResetDate"><a href="javascript:OpenResetDateFormErr();" class="color_postpone TrnTag">수업연기요청</a></li>
															<?}?>
														<?}?>
														
													<?}else{?>

														<?if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==8 || $ClassState==2){?>

														<?}else{?>
															<?if ($ClassMemberType==1){// 1:1 수업?>	
																<?if($MemberID==13591){// anlab97 학생 말아달라고 요청 by 강주임 200107?>
																	<li style="width:100%;" id="BtnResetDate"><a class="color_postpone TrnTag">수업연기요청</a></li>
																<?}else{?>
																	<li style="width:100%;" id="BtnResetDate"><a href="javascript:OpenResetDateForm(<?=$ClassID?>, <?=$ClassProductID?>, <?=(int)$StudyTimeHour?>, <?=(int)$StudyTimeMinute?>);" class="color_postpone TrnTag">수업연기요청</a></li>
																<?}?>
															<?}else{?>
																<?if($MemberID==13591){// anlab97 학생 말아달라고 요청 by 강주임 200107?>
																	<li style="width:100%;" id="BtnResetDate"><a class="color_postpone TrnTag">수업연기요청</a></li>
																<?}else{?>
																	<li style="width:100%;" id="BtnResetDate"><a href="javascript:OpenResetDateFormErr();" class="color_postpone TrnTag">수업연기요청</a></li>
																<?}?>
															<?}?>
														<?}?>

													<?}?>
												
												<?}?>
												
												<?if ($ClassProductID!=2 && $AssmtStudentDailyScoreID!=0){//데일리 평가보고?>
													<li style="width:100%;"><a href="javascript:OpenStudentScoreDailyReport(<?=$ClassID?>);" class="color_review_quiz TrnTag" style="background-color:#3D3D3D;">평가보고서</a></li>
												<?}else if ($ClassProductID==2 && $AssmtStudentLeveltestScoreID!=0){?>
													<li style="width:100%;"><a href="javascript:OpenStudentScoreLeveltestReport(<?=$AssmtStudentLeveltestScoreID?>);" class="color_review_quiz TrnTag" style="background-color:#3D3D3D;">평가보고서</a></li>
												<?}?>

												<?if ($ClassState==2 && $ClassProductID==1){?>
													<li style="width:100%;"><a href="javascript:OpenTeacherScoreForm(<?=$ClassID?>);" class="color_review_quiz TrnTag" style="background-color:#0080C0;">강사수업평가</a></li>
												<?}?>

										<?}?>

									<?
											}
											//=========================== DD ===========================
									?>

									</ul>
								</li>


					<?
							}
							//=========================== CC ===========================
						
						}
						//=========================== BB ===========================
					
					}
					//=========================== AA ===========================
					?>
					<!-- ============= LIST ============= -->



                </ul>
            </div>
            <!-- 달력 끝 -->

            <div class="mypage_inner">                

                <table class="mypage_survey_wrap">
                    <tr>
                        <th><img src="images/img_survey.png" class="survey_img"></th>
                        <td class="survey_left">
                            <h3 class="TrnTag">"오늘수업 어떠셨나요?"</h3>
                            <trn class="TrnTag">궁금하거나 부족한점 있으면 의견을 남겨주세요.<br>항상 최선을 다하는 Mango-i가 되겠습니다.</trn>
                        </td>
                        <td class="survey_right">
                            <a href="javascript:window.open('https://pf.kakao.com/_xlqnSxd/chat', '_blank', 'location=no');">
                                <img src="images/bg_survey.png" class="survey_bg">
                                <div class="survey_link">
                                    <div class="survey_caption TrnTag">1:1 문의</div>
                                    <div class="survey_go">GO</div>
                                </div>
                            </a>
                        </td>
                    </tr>
                </table>

                <!--
				<h3 class="caption_left_common">학습캘린더 <b>버튼 설명</b></h3>
                <ul class="remark_list">
                    <li>
                        <div class="remark_left">
                            <span class="color_attend">Attended</span>
                            <span class="color_absent">Absent</span>
                        </div>
                        <div class="remark_right">표시 - 출석(Attended), 결석(Absent)</div>
                    </li>
                    <li>
                        <div class="remark_left">
                            <span class="color_cancel_1">Canceled</span>
                        </div>
                        <div class="remark_right">강사 결석 또는 인터넷 등의 문제로 인한 휴강 표시</div>
                    </li>
                    <li>
                        <div class="remark_left">
                            <span class="color_feedback">Feedback</span>
                        </div>
                        <div class="remark_right">수업 피드백 보기 버튼</div>
                    </li>
                    <li>
                        <div class="remark_left">
                            <span class="color_finish">Finished</span>
                            <span class="color_cancel_2">Canceled</span>
                        </div>
                        <div class="remark_right">레벨 테스트표시 - 채점 완료(Finished), 학생 결석으로 인한 미실시(Canceled)</div>
                    </li>
                    <li>
                        <div class="remark_left">
                            <span class="color_result">Test Result</span>
                        </div>
                        <div class="remark_right">성적표 보기 버튼</div>
                    </li>
                    <li>
                        <div class="remark_left">
                            <span class="color_classin">강의실 입장</span>
                            <span class="color_today">오늘 수업</span>
                        </div>
                        <div class="remark_right">오늘 예정된 수업 표시(화상영어의 경우 강의실 입장 버튼을 통해 입장함)</div>
                    </li>
                    <li>
                        <div class="remark_left">
                            <span class="color_requst">요청사항</span>
                        </div>
                        <div class="remark_right">예약된 수업에 대한 요청 사항이 있을 시 요청사항을 남기는 버튼 </div>
                    </li>
                    <li>
                        <div class="remark_left">
                            <span class="color_postpone">수업연기</span>
                        </div>
                        <div class="remark_right">예약된 수업을 연기하기 위한 버튼(고정예약의 경우)</div>
                    </li>
                    <li>
                        <div class="remark_left">
                            <span class="color_cancel_3">예약취소</span>
                        </div>
                        <div class="remark_right">예약된 수업을 취소하기 위한 버튼(자유예약의 경우)</div>
                    </li>
                    <li>
                        <div class="remark_left">
                            <span class="color_comment">코멘트 입력중</span>
                        </div>
                        <div class="remark_right">해당 수업 종료 이후 강사가 코멘트를 남기고 있음을 알리는 표시</div>
                    </li>
                    <li>
                        <div class="remark_left">
                            <span class="color_mark">테스트 채점중</span>
                            <span class="color_evaluation">테스트 평가중</span>
                        </div>
                        <div class="remark_right">테스트 실시 후, 채점 및 평가 단계를 알리는 표시</div>
                    </li>
                </ul>
				-->
            </div>

        </div>
    </section>

</div>


<script>


function OpenStudentScoreDailyReport(ClassID){
	
	var OpenUrl = "./report_daily.php?ClassID="+ClassID;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1200"
		,maxHeight: "700"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenStudentScoreLeveltestReport(AssmtStudentLeveltestScoreID){
	var OpenUrl = "./report_leveltest.php?AssmtStudentLeveltestScoreID="+AssmtStudentLeveltestScoreID;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1200"
		,maxHeight: "700"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}




function OpenStudyVideo(VideoType, VideoCode, ClassID, ClassVideoType, BookRegForReason) {// ClassVideoType - 1: A타입, 2: B타입
	
	if (BookRegForReason==1){
		//if (confirm("MES 교재 또는 BTS 교재를 사용하면 학생 레벨에 맞는 레슨 비디오와 리뷰퀴즈를 활용할 수 있습니다. 많은 이용 부탁드립니다.")){
		//	OpenStudyVideoAction(VideoType, VideoCode, ClassID, ClassVideoType);
		//}

		var OpenUrl = "pop_study_room_random_quiz_video_msg.php?PopType=Video&VideoType="+VideoType+"&VideoCode="+VideoCode+"&ClassID="+ClassID+"&ClassVideoType="+ClassVideoType;

		$.colorbox({    
			href:OpenUrl
			,width:"95%" 
			,height:"95%"
			,maxWidth: "800"
			,maxHeight: "400"
			,title:""
			,iframe:true 
			,scrolling:false
			//,onClosed:function(){location.reload(true);}
			//,onComplete:function(){alert(1);}
		}); 	
	
	
	}else{
		OpenStudyVideoAction(VideoType, VideoCode, ClassID, ClassVideoType);
	}

}
function OpenStudyVideoAction(VideoType, VideoCode, ClassID, ClassVideoType) {// ClassVideoType - 1: A타입, 2: B타입
	var OpenUrl = "pop_video_player_study.php?VideoType="+VideoType+"&VideoCode="+VideoCode+"&ClassID="+ClassID+"&ClassVideoType="+ClassVideoType;

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
function OpenStudyVideoErr(){
    alert("준비된 레슨 비디오가 없습니다.");
}


function OpenBookScan(BookScanImageFileName,ClassID) {
    url = "ajax_set_book_scan_view_logs.php";

    $.ajax(url,{    
		data: {
			ClassID: ClassID,
			ClassBookType: 0
		},
		success: function() {

			var iframe = "<html><head><style>body, html {width: 100%; height: 100%; margin: 0; padding: 0}</style></head><body><iframe src='../ViewerJS/?zoom=page-width#/uploads/book_pdf_uploads/"+BookScanImageFileName+"' frameborder='0' style='height:calc(100% - 4px);width:calc(100% - 4px)'></iframe><input type='hidden' id='filename' value='"+BookScanImageFileName+"'></div></body></html>";

			var win = window.open("","coursebookcontent","frameborder=0,width="+screen.width+",height="+screen.height+",top=0,left=0,directories=no,location=no,channelmode=yes,fullscreen=yes,menubar=no, resizable=no,status=no,toolbar=no,history=no,scrollbars=yes");

			win.document.write(iframe);
		},
		error: function() {
			alert("ERROR!");
		}
    });
}

function OpenWebook(Unit, ClassID) {

    url = "ajax_set_book_scan_view_logs.php";

    $.ajax(url,{    
		data: {
			ClassID: ClassID,
			ClassBookType: 1
		},
		success: function() {


			var StrContentType = "일반교재";

			$.post( "./webook/_get_unit_content.php", { content_type:"학생", MemberLoginID: "<?=$_LINK_MEMBER_LOGIN_ID_?>", secret_key: "<?=$secret_key?>", cid: "<?=$cid?>", unit_id:Unit, api_extension:'', width:"100%", height: "100%", unit_contents_type:StrContentType })
			.done(function( data ) {

				var iframe = data;

				var win = window.open("","jtwebook","frameborder=0,width="+screen.width+",height="+screen.height+",top=0,left=0,directories=no,location=no,channelmode=yes,fullscreen=yes,menubar=no, resizable=no,status=no,toolbar=no,history=no,scrollbars=yes");

				win.document.write(iframe);
			});


		},
		error: function() {
			alert("ERROR!");
		}
    });
}

function OpenBookScanErr(){
    alert("준비된 학습 교재가 없습니다.");
}

function OpenStudyQuiz(BookQuizID, ClassID, BookRegForReason){

	if (BookRegForReason==1){
		
		//if (confirm("MES 교재 또는 BTS 교재를 사용하면 학생 레벨에 맞는 레슨 비디오와 리뷰퀴즈를 활용할 수 있습니다. 많은 이용 부탁드립니다.")){
		//	OpenStudyQuizAction(BookQuizID, ClassID);
		//}

		var OpenUrl = "pop_study_room_random_quiz_video_msg.php?PopType=Quiz&BookQuizID="+BookQuizID+"&ClassID="+ClassID;

		$.colorbox({    
			href:OpenUrl
			,width:"95%" 
			,height:"95%"
			,maxWidth: "800"
			,maxHeight: "400"
			,title:""
			,iframe:true 
			,scrolling:false
			//,onClosed:function(){location.reload(true);}
			//,onComplete:function(){alert(1);}
		}); 

	}else{
		OpenStudyQuizAction(BookQuizID, ClassID);
	}


}
function OpenStudyQuizAction(BookQuizID, ClassID){

	url = "./lms/ajax_check_class_quiz_result.php";
	//location.href = url + "?ClassID="+ClassID;
	$.ajax(url,{    
		data: {
			ClassID: ClassID
		},
		success: function(data) {

			if (data.BookQuizResultID == 0){
				var OpenUrl = "pop_quiz_study_preset.php?BookQuizID="+BookQuizID+"&ClassID="+ClassID+"&QuizStudyNumber=1";

				$.colorbox({    
					href:OpenUrl
					,width:"95%" 
					,height:"95%"
					,maxWidth: "1000"
					,maxHeight: "850"
					,title:""
					,iframe:true 
					,scrolling:true
					,onClosed:function(){location.reload(true);}
					//,onComplete:function(){alert(1);}
				}); 


			}else{
				alert("이미 퀴즈 풀이를 완료했습니다. 학습이력에서 재응시가 가능합니다.");
			}

		},
		error: function() {

		}
	});

}
function OpenStudyQuizErr(){
    alert("준비된 리뷰 퀴즈가 없습니다.");
}

function OpenTeacherScoreForm(ClassID){

    url = "./lms/ajax_check_class_teacher_score.php";
	//location.href = url + "?ClassID="+ClassID;
    $.ajax(url,{    
		data: {
			ClassID: ClassID
		},
		success: function(data) {

			if (data.AssmtTeacherScoreID == 0){

				var OpenUrl = "pop_teacher_score_form.php?ClassID="+ClassID;

				$.colorbox({    
					href:OpenUrl
					,width:"95%" 
					,height:"95%"
					,maxWidth: "600"
					,maxHeight: "500"
					,title:""
					,iframe:true 
					,scrolling:true
					,onClosed:function(){location.reload(true);}
					//,onComplete:function(){alert(1);}
				}); 


			}else{
				alert("이미 평가가 완료되었습니다.");
			}

		},
		error: function() {

		}
    });


}


function OpenResetDateFormErr(){
	alert("단체수업 연기는 관리자(선생님)게 요청해 주시기 바랍니다.");
}


function OpenResetDateFormWithReg(ClassOrderID, SelectYear, SelectMonth, SelectDay, StudyTimeHour, StudyTimeMinute, ClassOrderTimeTypeID, TeacherID, MemberID, ClassMemberType, ClassProductID){


    url = "ajax_set_class_reg_for_reset_date.php";

    $.ajax(url,{    
		data: {
			ClassOrderID: ClassOrderID,
			SelectYear: SelectYear,
			SelectMonth: SelectMonth,
			SelectDay: SelectDay,
			StudyTimeHour: StudyTimeHour,
			StudyTimeMinute: StudyTimeMinute,
			ClassOrderTimeTypeID: ClassOrderTimeTypeID,
			TeacherID: TeacherID,
			MemberID: MemberID,
			ClassMemberType: ClassMemberType
		},
		success: function(data) {

			OpenResetDateForm(data.ClassID, ClassProductID, StudyTimeHour, StudyTimeMinute);
		
		},
		error: function() {
			//alert("ERROR!");
		}
    });


	
}

function OpenResetDateForm(ClassID, ClassProductID, SetHour, SetMinute){
	
    url = "./lms/ajax_check_class_time_reset.php";
	//location.href = url + "?ClassID="+ClassID;
    $.ajax(url,{    
		data: {
			ClassID: ClassID
		},
		success: function(data) {

			//1: 가능 2:시간문제 3:회수문제
			EnableClassTimeReset = data.EnableClassTimeReset;
			ClassTimeLimit = data.ClassTimeLimit;

			if (EnableClassTimeReset==1){
				var OpenUrl = "pop_class_reset_date_form.php?ClassID="+ClassID+"&ClassProductID="+ClassProductID+"&SetHour="+SetHour+"&ClassProductID="+SetMinute;

				$.colorbox({    
					href:OpenUrl
					,width:"95%" 
					,height:"95%"
					,maxWidth: "500"
					,maxHeight: "400"
					,title:""
					,iframe:true 
					,scrolling:true
					,onClosed:function(){location.reload(true);}
					//,onComplete:function(){alert(1);}
				}); 

			}else if(EnableClassTimeReset==2){
				 alert("수업시간 연기는 수업시작 30분 전까지 가능합니다.");
			}else if(EnableClassTimeReset==3){
				 alert("수업시간 연기는 월 2회까지 허용합니다.");
			}else{
				alert("수업시간 연기를 할 수 없습니다. 관리자에게 문의 바랍니다.");
			}

		
		},
		error: function() {
            alert("수업시간 연기를 할 수 없습니다. 관리자에게 문의 바랍니다.");
		}
    });


}





</script>



<!--- api post form -->
<div style="display:none;">
    <!-- <form name="ShClassForm" id="ShClassForm" action="http://180.150.230.195/sso/type1.do" method="POST"> -->
    <form name="ShClassForm" id="ShClassForm" action="https://www.mangoiclass.co.kr/sso/type1.do" method="POST">
        <input type="text" name="userid" value="" />
        <input type="text" name="username" value="" />
        <input type="text" name="usertype" value="" />
        <input type="text" name="remote" value="1" />
        <input type="text" name="confcode" value="" />
        <input type="text" name="conftype" value="2" />
    </form>
	<section>
		<form id="openJoinForm" data-mv-api="openJoin">
		<article>
			<div class="body">
				<div class="input-section">
					<input type="text" name="roomCode" value=""> <!-- 멀티룸 코드 -->
					<input type="text" name="template" value="1"> <!-- 템플릿 번호 -->
					<input type="text" name="title" value=""> <!-- 멀티룸 제목 -->
					<input type="text" name="openOption" value="0">
					<input type="text" name="joinUserType" value=""> <!-- 입장 사용자 타입 -->
					<input type="text" name="userId" value=""> <!-- 사용자 아이디 -->
					<input type="text" name="userName" value=""> <!-- 사용자 이름 -->
					<input type="text" name="roomOption" value=""> <!-- 멀티룸 옵션 -->
					<input type="text" name="extraMsg" value=""> <!-- 확장 메시지 -->
				</div>
			</div>
		</article>
		</form>
	</section>
    <form name="CiClassForm" id="CiClassForm" method="POST">
        <input type="text" name="ClassID" id="ClassID" value="">
        <input type="text" name="ClassName" id="ClassName" value="">
        <input type="text" name="MemberType" id="MemberType" value="">
    </form>
</div>




<script>
//새하 열기 : MemberType - 0:학생 1:강사
function OpenClassShPreSet(ClassID, CommonShClassCode, MemberType, MemberName, MemberLoginID){

	$.ajax({
		url: "./ajax_check_student_self_score.php",
		method: "POST",
		data: {
			ClassID: ClassID
		},
		success: function(data) {
			var OnlineSiteShVersion = data.OnlineSiteShVersion;
			console.log(OnlineSiteShVersion);
			if (data.AssmtStudentSelfScoreID==0){
				if(OnlineSiteShVersion==1) {
					var CommonShNewClassCode = data.CommonShNewClassCode;
					var OpenUrl = "pop_student_self_score_form.php?OpenType=1&ClassID="+ClassID+"&CommonShClassCode="+CommonShNewClassCode+"&MemberType="+MemberType+"&MemberName="+MemberName+"&MemberLoginID="+MemberLoginID+"&OnlineSiteShVersion="+OnlineSiteShVersion;
				} else if(OnlineSiteShVersion==2) {
					var OpenUrl = "pop_student_self_score_form.php?OpenType=1&ClassID="+ClassID+"&CommonShClassCode="+CommonShClassCode+"&MemberType="+MemberType+"&MemberName="+MemberName+"&MemberLoginID="+MemberLoginID+"&OnlineSiteShVersion="+OnlineSiteShVersion;
				}
				$.colorbox({    
					href:OpenUrl
					,width:"95%" 
					,height:"95%"
					,maxWidth: "700"
					,maxHeight: "500"
					,title:""
					,iframe:true 
					,scrolling:true
					//,onClosed:function(){location.reload(true);}
					//,onComplete:function(){alert(1);}
				}); 

			}else{
				if(OnlineSiteShVersion==1) {
					var CommonShNewClassCode = data.CommonShNewClassCode;
					OpenClassSh(ClassID, CommonShNewClassCode, MemberType, MemberName, MemberLoginID, OnlineSiteShVersion);
				} else if(OnlineSiteShVersion==2) {
					OpenClassSh(ClassID, CommonShClassCode, MemberType, MemberName, MemberLoginID, OnlineSiteShVersion);
				}
			}

		},
		error: function(req, stat, err) {

		}
	});

}

function OpenClassSh(ClassID, CommonShClassCode, MemberType, MemberName, MemberLoginID, OnlineSiteShVersion){
	//alert(OnlineSiteShVersion);
	if(OnlineSiteShVersion==1) {
		MvApi.defaultSettings({
			debug: false,
			// tcps: {key: 'MTgwLjE1MC4yMzAuMTk1OjcwMDE'},
			tcps: {key: 'MTIxLjE3MC4xNjQuMjMxOjcwMDE'},
			installPagePopup: "popup",
			company: {code: 2, authKey: '1577840400'},
			//web: {url: 'http://180.150.230.195:8080'},
			web: {url: 'https://www.mangoiclass.co.kr:8080'},
			
			// 클라이언트 설정 정보
			client: {
				// 암호화 사용 여부 - 유효성 검사를 수행하지 않는다.
				encrypt: false,
				// Windows Client 설정
				windows: {
					// 프로그램 이름
					product: 'BODA'
				}, 
				// Mobile Client 설정
				mobile: {
					// 스토어 배포 방식. true: Store 배포, false: 사설 배포
					store: false, 
					// 스킴 이름
					scheme: 'mangoi',
					// 패키지 이름
					packagename: 'zone.mangoi',
				},
				// Mac Client 설정 - V7.3.0
				mac: {
					// 스토어 배포 방식. true: Store 배포, false: 사설 배포 - V7.3.1
					store: false, 
					// 스킴 이름
					scheme: 'mangoi',
					// 패키지 이름
					packagename: 'zone.mangoi',
				},
				// 사용언어 - 없으면 한국어
				language: '<?=$ShLanguage?>',
				// 테마 - 클라이언트의 테마 코드 값 - v7.1.3
				theme: 3,
				// 버튼 타입 - 버튼을 표시하는 방식 - v7.1.3
				btnType: 1,
				// 어플리케이션 모드 - 회의,교육 등 동작 모드 설정 - v7.1.4
				appMode: 2
			},
			

		});


		if(MemberType==0) {
			MemberType = 22; // 학생
		} else {
			MemberType = 21; // 교사
		}

		$('input[name=userId]').val(MemberLoginID);
		$('input[name=title]').val("망고아이 수업");
		$('input[name=userName]').val(MemberName);
		$('input[name=joinUserType]').val(MemberType);
		$('input[name=roomCode]').val(CommonShClassCode);


		// 기능 실행 버큰 클릭 시 처리
		$('form[data-mv-api]').submit(function(){
			var $this = $(this);
			var api = $this.data('mvApi');
			
			// 요청 메시지 정보 설정
			var requestMsg = {};
			var parameters = $this.serializeArray();
			$.each(parameters, function(index, parameter){
				requestMsg[parameter.name] = parameter.value;
			})
					
			// API 호출
			MvApi[api](
					// 요청메시지
					requestMsg,
					// 성공 callback
					function(){
						console.log('success.');
					},
					// 오류 callback
					function(errorCode, reason){
						console.error('error.', errorCode, reason);
					}
			);
			return false;
		});

		$('form[data-mv-api]').submit();

	} else if(OnlineSiteShVersion==2) {
		var FormData = document.getElementById("ShClassForm");
		FormData.userid.value = MemberLoginID;
		FormData.username.value = MemberName;
		FormData.usertype.value = MemberType;  // 강사,학생
		FormData.confcode.value = CommonShClassCode;
		
		var newwin = window.open("", "newwin", "toolbar=no,scrollbars=no,resizable=no,top=100,left=500,width=600,height=600");
		FormData.target = "newwin";
		FormData.submit();
	}
}

function OpenClassCi(ClassID, ClassName, MemberType, ClassRoomUrl){
    var FormData = document.getElementById("CiClassForm");
    FormData.ClassID.value = ClassID;
    FormData.ClassName.value = ClassName;
    FormData.MemberType.value = MemberType;

    var newwin = window.open("", "newwin", "toolbar=no,scrollbars=no,resizable=no,top=100,left=500,width=600,height=600");
    FormData.target = "newwin";
    FormData.action = ClassRoomUrl;
    FormData.submit();
    setTimeout(function() {
      newwin.close();
    }, 5000);

}

//클래스인 열기 : MemberType - 1:학생 2:강사
function OpenClassCiCheckPreSet(ClassID, CommonCiTelephoneTeacher, CommonCiTelephoneStudent, MemberType, MemberName, ClassName){



				$.ajax({
					url: "./ajax_check_student_self_score.php",
					method: "POST",
					data: {
						ClassID: ClassID
					},
					success: function(data) {

						if (data.AssmtStudentSelfScoreID==0){

							var OpenUrl = "pop_student_self_score_form.php?OpenType=2&ClassID="+ClassID+"&CommonCiTelephoneTeacher="+CommonCiTelephoneTeacher+"&CommonCiTelephoneStudent="+CommonCiTelephoneStudent+"&MemberType="+MemberType+"&MemberName="+MemberName+"&ClassName="+ClassName;

							$.colorbox({    
								href:OpenUrl
								,width:"95%" 
								,height:"95%"
								,maxWidth: "700"
								,maxHeight: "500"
								,title:""
								,iframe:true 
								,scrolling:true
								//,onClosed:function(){location.reload(true);}
								//,onComplete:function(){alert(1);}
							}); 

						}else{
							OpenClassCiCheck(ClassID, CommonCiTelephoneTeacher, CommonCiTelephoneStudent, MemberType, MemberName, ClassName);
						}

					},
					error: function(req, stat, err) {

					}
				});




	/*
    url = "./lms/ajax_check_class_time.php";
	//location.href = url + "?ClassID="+ClassID;
    $.ajax(url,{    
		data: {
			ClassID: ClassID
		},
		success: function(data) {
			if (data.EnableClassTime == 1){


				$.ajax({
					url: "./ajax_check_student_self_score.php",
					emthod: "POST",
					data: {
						ClassID: ClassID
					},
					success: function(data) {

						if (data.AssmtStudentSelfScoreID==0){

							var OpenUrl = "pop_student_self_score_form.php?OpenType=2&ClassID="+ClassID+"&CommonCiTelephoneTeacher="+CommonCiTelephoneTeacher+"&CommonCiTelephoneStudent="+CommonCiTelephoneStudent+"&MemberType="+MemberType+"&MemberName="+MemberName+"&ClassName="+ClassName;

							$.colorbox({    
								href:OpenUrl
								,width:"95%" 
								,height:"95%"
								,maxWidth: "700"
								,maxHeight: "500"
								,title:""
								,iframe:true 
								,scrolling:true
								//,onClosed:function(){location.reload(true);}
								//,onComplete:function(){alert(1);}
							}); 

						}else{
							OpenClassCiCheck(ClassID, CommonCiTelephoneTeacher, CommonCiTelephoneStudent, MemberType, MemberName, ClassName);
						}

					},
					error: function(req, stat, err) {

					}
				});



			}else{
				alert("강의실 입장은 시작시간 10분 전부터 가능합니다.");
			}
		},
		error: function() {
            alert("수업을 열 수 없습니다. 관리자에게 문의해 주세요.");
		}
    });
	*/


}
function OpenClassCiCheck(ClassID, CommonCiTelephoneTeacher, CommonCiTelephoneStudent, MemberType, MemberName, ClassName){

    // moment 라이브러리 사용, UTC 기반 현재 시간 추출
    // subtract 현재 시간 이전, add 현재 시간 이후
    // UTC 기반 한국시간을 추출 후 1분 후로 설정 후 unix timestamp ( second ) 변환 
    //var CurrentDate = moment().tz("Asia/Seoul").utc();
    //var BeginTime = CurrentDate.add(2, "minutes").unix();
    //var EndTime = CurrentDate.add(60, "minutes").unix();

    //console.log("moment Begin : " +BeginTime+ "\n");
    //console.log("moment End : " +EndTime+ "\n");

    CurrentDate = new Date();
    BeginTime = parseInt(CurrentDate.setMinutes(CurrentDate.getMinutes()+2) / 1000 );
    EndTime = parseInt(CurrentDate.setMinutes(CurrentDate.getMinutes()+60) / 1000 );

    //console.log("moment Begin : " +BeginTime+ "\n");
    //console.log("moment End : " +EndTime+ "\n");



	if (CommonCiTelephoneTeacher=="" || CommonCiTelephoneStudent==""){
		if (CommonCiTelephoneTeacher==""){
			alert("강사 클래스인 로그인 아이디가 설정되지 않습니다.");
		}else{
			alert("학생 클래스인 로그인 아이디가 설정되지 않습니다.");
		}
	}else{

		//location.href = "ajax_check_ci_class_in_db.php?ClassID="+ClassID+"&MemberType="+MemberType+"&CommonCiTelephone="+CommonCiTelephone+"&BeginTime="+BeginTime+"&EndTime="+EndTime;

		if (MemberType==1){
			CommonCiTelephone = CommonCiTelephoneStudent;
		}else{
			CommonCiTelephone = CommonCiTelephoneTeacher;
		}    
		$.ajax({
			url: "./lms/ajax_check_ci_class_in_db.php",
			emthod: "POST",
			data: {
				ClassID: ClassID,
				MemberType: MemberType,
				CommonCiTelephone: CommonCiTelephone,
				BeginTime: BeginTime,
				EndTime: EndTime
			},
			success: function(data) {
				ClassRoomUrl = data.ClassRoomUrl;
				if (ClassRoomUrl==""){
					alert("아직 강의실이 개설되지 않았습니다. 잠시후 다시 접속해 주세요.");
				}else{
					OpenClassCi(ClassID, ClassName, MemberType, ClassRoomUrl)
				}
			},
			error: function(req, stat, err) {

			}
		});

		
	}        

}


function OpenStudyRuleInfo(){
    window.open("pop_study_rule_info.php", "pop_study_rule_info", "toolbar=no,scrollbars=yes,resizable=yes,top=100,left=100,width=1000,height=800");
}





//수업 자동등록(당일날 처음 열때 등록해 준다)
window.onload = function(){

}
//수업 자동등록(당일날 처음 열때 등록해 준다)
</script>


<script language="javascript">
$('.sub_visual_navi .two').addClass('active');
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





