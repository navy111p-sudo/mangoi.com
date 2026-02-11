<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />

<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-2.2.4.min.js"></script>
<script src="js/common.js"></script>

<body>
<?
include_once('./includes/common_body_top.php');
?>
<?
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$today = strtotime(date('Y-m-d'));
$year = isset($_REQUEST["SelectYear"]) ? $_REQUEST["SelectYear"] : "";
$month = isset($_REQUEST["SelectMonth"]) ? $_REQUEST["SelectMonth"] : "";
$IframeMode = isset($_REQUEST["IframeMode"]) ? $_REQUEST["IframeMode"] : "";

$EduCenterID = 1;

if ($MemberID==""){
	$MemberID = $_LINK_MEMBER_ID_;
}

if ($year==""){
	$year = date('Y');
}
if ($month==""){
	$month = (int)date('m'); 
}

$p_month = $month - 1;
$n_month = $month + 1;

if ($p_month==0){
	$p_month = 12;
	$p_year = $year-1;
}else{
	$p_year = $year;
}

if ($n_month==13){
	$n_month = 1;
	$n_year = $year+1;
}else{
	$n_year = $year;
}



$Sql = "
		select 
				A.*,
				B.CenterPayType,
				B.CenterStudyEndDate
		from Members A 
			inner join Centers B on A.CenterID=B.CenterID 
		where A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$MemberPayType = $Row["MemberPayType"];
$CenterPayType = $Row["CenterPayType"];
$CenterStudyEndDate = $Row["CenterStudyEndDate"];




$time = strtotime($year.'-'.$month.'-01'); 
list($tday, $sweek) = explode('-', date('t-w', $time));  // 총 일수, 시작요일 
$tweek = ceil(($tday + $sweek) / 7);  // 총 주차 
$lweek = date('w', strtotime($year.'-'.$month.'-'.$tday));  // 마지막요일 
?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area" style="width:90%;">
		<h3 class="caption_underline">
			<trn class="TrnTag">이달의 학습일정</trn>
			<div  style="margin-top:20px;">
				<a href="?SelectYear=<?=$p_year?>&SelectMonth=<?=$p_month?>&MemberID=<?=$MemberID?>" class="calendar_arrow left" style="color:#999999;">◀</a>
				<?=$year?>.<?=substr("0".$month,-2)?>
				<a href="?SelectYear=<?=$n_year?>&SelectMonth=<?=$n_month?>&MemberID=<?=$MemberID?>" class="calendar_arrow right" style="color:#999999;">▶</a>
			</div>

		</h3> 
		<div style="text-align:right;margin-bottom:10px;padding-right:10px;" class="TrnTag">※ 종료일자는 정규 종료일자 입니다. 연기/보강/변경 등은 종료일자 이후라도 수업이 진행됩니다.</div>
		<table class="level_reserve_table"  align="center">
			<tr style="height:30px;">
				<th>SUN</th>
				<th>MON</th>
				<th>TUE</th>
				<th>WED</th>
				<th>THU</th>
				<th>FRI</th>
				<th>SAT</th>
			</tr>
			<?

			$ClassAttendCount = 0;
			$ClassTotalCount = 0;
			for ($nn=1,$ii=0; $ii<$tweek; $ii++){
			?> 
			<tr>
				<?
				for ($kk=0; $kk<7; $kk++){
				?> 
					<td style="padding:20px;" valign="top">
					<? 
					if (($ii == 0 && $kk < $sweek) || ($ii == $tweek-1 && $kk > $lweek)) {
						echo "</td>\n";
						continue;
					}
					
					$day = $nn++;
					$NowDate = strtotime($year . "-" . substr("0".$month,-2) . "-" . substr("0".$day,-2));
					$SelectDate = $year . "-" . substr("0".$month,-2) . "-" . substr("0".$day,-2);
					
					if ($today-$NowDate==0){
						echo "<span style='color:#ff0000'>".$day."</span>";
					}else{
						echo $day;
					}

					?>
						<?
						$SelectWeek = date('w', strtotime($SelectDate));
						

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


						$Sql2 = "

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
								ifnull(CLS.ClassAttendState, -1) as ClassAttendState,
								CLS.ClassAttendStateMemberID,
								ifnull(CLS.ClassState, 0) as ClassState,
								CLS.BookVideoID,
								CLS.BookQuizID,
								CLS.BookScanID,
								CLS.ClassRegDateTime,
								CLS.ClassModiDateTime,

								MB.MemberName,
								MB.MemberNickName,
								MB.MemberLoginID, 
								MB.MemberLevelID,
								MB.MemberCiTelephone,
								MB.MemberPayType,

								TEA.TeacherName,
								MB2.MemberLoginID as TeacherLoginID, 
								MB2.MemberCiTelephone as TeacherCiTelephone,
								CT.CenterID as JoinCenterID,
								CT.CenterName as JoinCenterName,
								CT.CenterPayType,
								CT.CenterStudyEndDate,
								BR.BranchID as JoinBranchID,
								BR.BranchName as JoinBranchName, 
								BRG.BranchGroupID as JoinBranchGroupID,
								BRG.BranchGroupName as JoinBranchGroupName,
								COM.CompanyID as JoinCompanyID,
								COM.CompanyName as JoinCompanyName,
								FR.FranchiseName,
								MB3.MemberLoginID as CenterLoginID,
								TEA2.TeacherName as ClassTeacherName,

								ifnull(AD.AssmtStudentDailyScoreID,0) as AssmtStudentDailyScoreID,
								ifnull(AL.AssmtStudentLeveltestScoreID,0) as AssmtStudentLeveltestScoreID

							from ClassOrderSlots COS 

									left outer join Classes CLS on COS.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$year." and CLS.StartMonth=".$month." and CLS.StartDay=".$day." and CLS.StartHour=COS.StudyTimeHour and CLS.StartMinute=COS.StudyTimeMinute and CLS.TeacherID=COS.TeacherID and CLS.ClassAttendState<>99 

									inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 




									inner join Members MB on CO.MemberID=MB.MemberID 
									inner join Centers CT on MB.CenterID=CT.CenterID 
									inner join Branches BR on CT.BranchID=BR.BranchID 
									inner join BranchGroups BRG on BR.BranchGroupID=BRG.BranchGroupID 
									inner join Companies COM on BRG.CompanyID=COM.CompanyID 
									inner join Franchises FR on COM.FranchiseID=FR.FranchiseID 
									inner join Teachers TEA on COS.TeacherID=TEA.TeacherID 
									left outer join Teachers TEA2 on CLS.TeacherID=TEA2.TeacherID 
									inner join Members MB2 on TEA.TeacherID=MB2.TeacherID 
									left outer join Members MB3 on CT.CenterID=MB3.CenterID and MB3.MemberLevelID=12 
									
									left outer join AssmtStudentDailyScores AD on CLS.ClassID=AD.ClassID 
									left outer join AssmtStudentLeveltestScores AL on CLS.ClassID=AL.ClassID 

							where TEA.TeacherState=1 
									and CO.MemberID=".$MemberID." 
									and COS.ClassOrderSlotMaster=1 
									and ( 
											(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

											or 
											(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

											or 
											(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

											or 
											(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

											or 
											(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SelectDate."')=0 )   
										)  
									and COS.ClassOrderSlotState=1 
									and CO.ClassProgress=11 
									and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=3 or CO.ClassOrderState=5 or CO.ClassOrderState=6)
									
									and (
											(CT.CenterPayType=1 and MB.MemberPayType=0 and ((CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=5 or CO.ClassOrderState=6) or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$SelectDate."')>=0) )) 
											or 
											( 
												( CT.CenterPayType=2 and datediff(CO.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
												or 
												( CT.CenterPayType=1 and MB.MemberPayType=1 and datediff(CO.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
											)
											or
											CO.ClassProductID=2 
											or 
											CO.ClassProductID=3 
											or 
											(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SelectDate."')=0) 
										)						

						";
						//결제 안해도 관리자가 등록한 수업 진행가능하도록 주석 처리
						//inner join ClassOrderPayDetails COPD on COS.ClassOrderID=COPD.ClassOrderID 
						//inner join ClassOrderPays COP on COPD.ClassOrderPayID=COP.ClassOrderPayID and (COP.ClassOrderPayProgress=21 or COP.ClassOrderPayProgress=31 or COP.ClassOrderPayProgress=41) 

						$Stmt2 = $DbConn->prepare($Sql2);
						$Stmt2->execute();
						$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
					
						
						while($Row2 = $Stmt2->fetch()) {

							$ClassOrderSlotType = $Row2["ClassOrderSlotType"];
							$ClassOrderSlotType2 = $Row2["ClassOrderSlotType2"];
							$ClassProductID = $Row2["ClassProductID"];
							
							$TeacherID = $Row2["TeacherID"];
							$ClassMemberType = $Row2["ClassMemberType"];
							$StudyTimeWeek = $Row2["StudyTimeWeek"];
							$StudyTimeHour = $Row2["StudyTimeHour"];
							$StudyTimeMinute = $Row2["StudyTimeMinute"];
							
							$ClassID = $Row2["ClassID"];
							$ClassOrderTimeTypeID = $Row2["ClassOrderTimeTypeID"];
							
							$TeacherName = $Row2["TeacherName"];
							$StartDateTime = $Row2["StartDateTime"];
							$ClassAttendState = $Row2["ClassAttendState"];
							$ClassState = $Row2["ClassState"];
							$StartHour = $Row2["StartHour"];
							$StartMinute = $Row2["StartMinute"];
							$EndHour = $Row2["EndHour"];
							$EndMinute = $Row2["EndMinute"];

							$ClassStartTime = $Row2["ClassStartTime"];

							$AssmtStudentDailyScoreID = $Row2["AssmtStudentDailyScoreID"];
							$AssmtStudentLeveltestScoreID = $Row2["AssmtStudentLeveltestScoreID"];

							$CenterPayType = $Row2["CenterPayType"];
							$MemberPayType = $Row2["MemberPayType"];
							$CenterStudyEndDate = $Row2["CenterStudyEndDate"];
							$ClassOrderEndDate = $Row2["ClassOrderEndDate"];

							

							if ($CenterPayType==1){//B2B결제
								if ($MemberPayType==0){
									$StrStudyAuthDate = $CenterStudyEndDate;
								}else{
									$StrStudyAuthDate = $ClassOrderEndDate;
								}
							}else{
								$StrStudyAuthDate = $ClassOrderEndDate;
							}

							$StrStudyAuthDate = str_replace("-","/",substr($StrStudyAuthDate, -8));


							$CalDateDiff = (int)str_replace("-","",$SelectDate) - (int)date("Ymd");

							if ($ClassAttendState==-1){//1:출석 2:지각 3:결석 4:학생연기 5:강사연기 6:학생취소 7:강사취소
								$StrClassAttendState = "<div style='display:inline-block;width:50px;text-align:center;padding:5px;border-radius:3px;background-color:#6894d9;color:#ffffff;'>예정</div>";
								
								if ($CalDateDiff < 0){
									$StrClassAttendState = "<div style='display:inline-block;width:50px;text-align:center;padding:5px;border-radius:3px;background-color:#8080C0;color:#ffffff;'>미설정</div>";
								}


							}else if ($ClassAttendState==0){
								$StrClassAttendState = "<div style='display:inline-block;width:50px;text-align:center;padding:5px;border-radius:3px;background-color:#8080C0;color:#ffffff;'>미설정</div>";
							}else if ($ClassAttendState==1){
								$StrClassAttendState = "<div style='display:inline-block;width:50px;text-align:center;padding:5px;border-radius:3px;background-color:#0080C0;color:#ffffff;'>출석</div>";
							}else if ($ClassAttendState==2){
								$StrClassAttendState = "<div style='display:inline-block;width:50px;text-align:center;padding:5px;border-radius:3px;background-color:#018D60;color:#ffffff;'>지각</div>";
							}else if ($ClassAttendState==3){
								$StrClassAttendState = "<div style='display:inline-block;width:50px;text-align:center;padding:5px;border-radius:3px;background-color:#C02C16;color:#ffffff;'>결석</div>";
							}else if ($ClassAttendState==4){
								$StrClassAttendState = "<div style='display:inline-block;width:50px;text-align:center;padding:5px;border-radius:3px;background-color:#8080C0;color:#ffffff;'>연기됨</div>";
							}else if ($ClassAttendState==5){
								$StrClassAttendState = "<div style='display:inline-block;width:50px;text-align:center;padding:5px;border-radius:3px;background-color:#8080C0;color:#ffffff;'>연기됨</div>";
							}else if ($ClassAttendState==6){
								$StrClassAttendState = "<div style='display:inline-block;width:50px;text-align:center;padding:5px;border-radius:3px;background-color:#804040;color:#ffffff;'>취소됨</div>";
							}else if ($ClassAttendState==7){
								$StrClassAttendState = "<div style='display:inline-block;width:50px;text-align:center;padding:5px;border-radius:3px;background-color:#804040;color:#ffffff;'>취소됨</div>";
							}else if ($ClassAttendState==8){
								$StrClassAttendState = "<div style='display:inline-block;width:50px;text-align:center;padding:5px;border-radius:3px;background-color:#804040;color:#ffffff;'>변경됨</div>";
							}else{
								$StrClassAttendState = "-";
							}

							if ($TodayIsHoliday==1){
								$StrClassAttendState = "<div style='display:inline-block;width:50px;text-align:center;padding:5px;border-radius:3px;background-color:#804040;color:#ffffff;'>휴무일</div>";
							}

							
							if ($ClassProductID==1){
								$StrClassProductID = "정규수업";
								if ($ClassOrderSlotType==2){
									$StrClassProductID = "임시수업";
									if ($ClassOrderSlotType2==4){
										$StrClassProductID = "연기수업";
									}else if ($ClassOrderSlotType2==5){
										$StrClassProductID = "연기수업";
									}else if ($ClassOrderSlotType2==8){
										$StrClassProductID = "변경수업";
									}else if ($ClassOrderSlotType2==10000){
										$StrClassProductID = "보강수업";
									}else if ($ClassOrderSlotType2==20000){
										$StrClassProductID = "스케줄변경";//안나옴 ClassOrderSlotType=1 일때만 생성됨
									}	
								}
							}else if ($ClassProductID==2){
								$StrClassProductID = "레벨테스트";
							}else if ($ClassProductID==3) {
								$StrClassProductID = "체험수업";
							}
						
							if ($ClassOrderTimeTypeID==2){
								$StrClassOrderTimeTypeName = "20 min";
							}else if ($ClassOrderTimeTypeID==3){
								$StrClassOrderTimeTypeName = "30 min";
							}else if ($ClassOrderTimeTypeID==4){
								$StrClassOrderTimeTypeName = "40 min";
							}
						
							if ($ClassAttendState>-1 && ($ClassAttendState==1 || $ClassAttendState==2)){
								$ClassAttendCount++;
							}
							if ($ClassAttendState>-1){
								$ClassTotalCount++;
							}
						?>
							<?
							if ($ClassMemberType) {
								if ($ClassMemberType==1){
									$StrClassMemberType = "1:1";
								}else if ($ClassMemberType==2){
									$StrClassMemberType = "1:2";
								}else if ($ClassMemberType==3){
									$StrClassMemberType = "G";
								}
							?>

							<div style="line-height:1.5;margin-bottom:20px;border:1px solid #CACACA;padding:10px;border-radius:5px;">
								
								<div style="margin-top:5px;margin-bottom:10px;border:1px solid #cccccc;background-color:#f1f1f1;border-radius:3px;padding:5px 0px;text-align:center;font-size:11px;"><?=$StrClassProductID?></div>

								<?if ($_LINK_MEMBER_LEVEL_ID_<=15){?>
									<b>
										<a href="javascript:OpenTeacherMessageForm(<?=$TeacherID?>);" style="color:#0080C0;"><?=$TeacherName?></a>
										<a href="javascript:OpenClassScheduleByTeacher(<?=$TeacherID?>);" style="color:#0080C0;"><img src="images/btn_calendar.png" style="width:13px;"></a>
										(<?=$StrClassMemberType?>)
									</b>
								<?}else{?>
									<b><?=$TeacherName?> (<?=$StrClassMemberType?>)</b> 
								<?}?>
								<br>
								<span style="font-size:11px;"><?=ConvAmPm($ClassStartTime)?></span><br><?=$StrClassOrderTimeTypeName?>
								<br>
								<span style="color:#FE9147;"><?=$StrClassAttendState?></span>

								<?if ($ClassProductID!=2 && $AssmtStudentDailyScoreID!=0){//데일리 평가보고?>
									<br>
									<span style="display:inline-block;width:50px;text-align:center;font-size:10px;margin-top:10px;padding:5px;border-radius:3px;background-color:#888888;color:#ffffff;cursor:pointer;" onclick="javascript:OpenStudentScoreDailyReport(<?=$ClassID?>);">REPORT</span>
								<?}else if ($ClassProductID==2 && $AssmtStudentLeveltestScoreID!=0){?>
									<br>
									<span style="display:inline-block;width:50px;text-align:center;font-size:10px;margin-top:10px;padding:5px;border-radius:3px;background-color:#888888;color:#ffffff;cursor:pointer;" onclick="avascript:OpenStudentScoreLeveltestReport(<?=$AssmtStudentLeveltestScoreID?>);">REPORT</span>
								<?}?>
								
								<?if ($_LINK_MEMBER_LEVEL_ID_<=15 && $ClassAttendState<=8){?>
								<div style='display:inline-block;width:50px;text-align:center;font-size:10px;margin-top:10px;padding:5px;border-radius:3px;background-color:#808000;color:#ffffff;cursor:pointer;' onclick="OpenClassListMini('<?=$SelectDate?>', <?=$TeacherID?>, <?=$StudyTimeWeek?>, <?=$StudyTimeHour?>, <?=$StudyTimeMinute?>);" class="TrnTag">일정변경</div>
								<?}?>

								

								<?if ($ClassProductID==1){?>
									<div style="margin-top:20px;border:1px solid #cccccc;border-radius:3px;padding:5px 0px;text-align:center;font-size:11px;"><trn class="TrnTag">종료일자</trn><br><?=$StrStudyAuthDate?></div>
								<?}?>
							</div>
							<?
							}
							?>
						<?
						}
						$Stmt2 = null;
						?>

					</td> 
					<?
					}
					?> 
			</tr> 
			<?
			}
			?>
		</table>


		<div class="button_wrap flex_justify" id="DivBtn">
			<a href="javascript:PagePrint();" class="button_orange_white mantoman TrnTag">인쇄하기</a>
			<a href="javascript:parent.$.fn.colorbox.close();" class="button_br_black mantoman TrnTag">닫기</a>
		</div>
	</div>
</div>




<script>
function OpenClassScheduleByTeacher(DirectTeacherID){
	openurl = "./lms/class_schedule_by_teacher.php?DirectTeacherID="+DirectTeacherID;
	window.open(openurl, "class_schedule_by_teacher", "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1080,height=800");
}



function OpenClassListMini(SelectDate, TeacherID, StudyTimeWeek, StudyTimeHour, StudyTimeMinute){
	var OpenUrl = "./lms/class_list_mini.php?SelectDate="+SelectDate+"&TeacherID="+TeacherID+"&StudyTimeWeek="+StudyTimeWeek+"&StudyTimeHour="+StudyTimeHour+"&StudyTimeMinute="+StudyTimeMinute;

	$.colorbox({	
		href:OpenUrl
        ,width:"95%" 
        ,height:"95%"
		//,width:"800" 
		//,height:"500"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenTeacherMessageForm(TeacherID){
    openurl = "./lms/teacher_message_form.php?TeacherID="+TeacherID;
    $.colorbox({    
        href:openurl
        ,width:"95%" 
        ,height:"95%"
        ,maxWidth: "850"
        ,maxHeight: "650"
        ,title:""
        ,iframe:true 
        ,scrolling:true
        //,onClosed:function(){location.reload(true);}
        //,onComplete:function(){alert(1);}
    }); 
}


function OpenStudentScoreDailyReport(ClassID){
	
	var OpenUrl = "./report_daily.php?ClassID="+ClassID;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "800"
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
		,maxWidth: "800"
		,maxHeight: "700"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function PagePrint(){
	document.getElementById("DivBtn").style.display = "none";
	setTimeout(PagePrintAction, 1000);
}

function PagePrintAction(){
	print();
	document.getElementById("DivBtn").style.display = "";
}



</script>


<link rel="stylesheet" href="./js/colorbox/example2/colorbox.css" />
<script src="./js/colorbox/jquery.colorbox.js"></script>
<script>
jQuery(document).ready(function(){
$(document).bind('cbox_open', function() {
	$('html').css({ overflow: 'hidden' });
}).bind('cbox_closed', function() {
	$('html').css({ overflow: '' });
});
});
</script>

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>