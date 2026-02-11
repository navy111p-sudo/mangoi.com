<?
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ErrNum = 0;
$ErrMsg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$LocalLinkMemberID = isset($_REQUEST["LocalLinkMemberID"]) ? $_REQUEST["LocalLinkMemberID"] : "";
$SelectYear = isset($_REQUEST["SelectYear"]) ? $_REQUEST["SelectYear"] : "";
$SelectMonth = isset($_REQUEST["SelectMonth"]) ? $_REQUEST["SelectMonth"] : "";
$SelectDay = "";
$GetAllList = isset($_REQUEST["GetAllList"]) ? $_REQUEST["GetAllList"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";

$EduCenterID = 1;

$ArrWeekDay = explode(",","Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday");

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


$Sql5 = "select 
		count(*) as ProductOrderCartCount
	from ProductOrderCarts A 
	where A.MemberID=:MemberID and A.ProductOrderCartState=2 and A.ProductOrderCartID in (select ProductOrderCartID from ProductOrderCartDetails) ";

$Stmt5 = $DbConn->prepare($Sql5);
$Stmt5->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt5->execute();
$Stmt5->setFetchMode(PDO::FETCH_ASSOC);
$Row5 = $Stmt5->fetch();
$ProductOrderCartCount = $Row5["ProductOrderCartCount"];
$Stmt5 = null;



$PageMyPageRoomHTML = "";
$PageMyPageRoomHTML .= "<section class=\"mypage_start_area\">";


$PageMyPageRoomHTML .= "	<ul class=\"mypage_tabs\">";
//$PageMyPageRoomHTML .= "		<li><a href=\"mypage.html\" class=\"item-link item-content\"><span class=\"bar\"></span>마이 페이지</a></li>";
$PageMyPageRoomHTML .= "		<li style=\"width:48%;\"><a href=\"#\" class=\"active TrnTag\"><span class=\"bar\"></span>나의 공부방</a></li>";
$PageMyPageRoomHTML .= "		<li style=\"width:48%;\"><a href=\"mypage_study_history.html\" class=\"item-link item-content TrnTag\"><span class=\"bar\"></span>학습 이력</a></li>";
$PageMyPageRoomHTML .= "	</ul>";


$PageMyPageRoomHTML .= "<h3 class=\"schedule_caption\" style=\"margin-top:10px;background-color:#ffffff;padding-top:20px;border-radius:10px;margin-bottom:20px;\">";
$PageMyPageRoomHTML .= "	<a href=\"#\" onclick=\"GetPageMyPageRoom('".$PrevYear."','".$PrevMonth."','')\"><img src=\"".$ServerPath."images/btn_prev_black.png\" class=\"schedule_left\"></a>";
$PageMyPageRoomHTML .= "	".$SelectYear.".".$SelectMonth." ";
$PageMyPageRoomHTML .= "	<a href=\"#\" onclick=\"GetPageMyPageRoom('".$NextYear."','".$NextMonth."','')\"><img src=\"".$ServerPath."images/btn_next_black.png\" class=\"schedule_right\"></a>";
$PageMyPageRoomHTML .= "</h3>";

if ($ProductOrderCartCount>0){
	/* 교재구매 */
	$PageMyPageRoomHTML .= " 
			<div style=\"display:inline-block;width:49%;\">
				<li><a href=\"#\" onclick=\"OpenLessonVideos();\" class=\"color_review_book TrnTag\" style=\"display:inline-block;padding:2px 10px;width:100%;height:25px;line-height:20px;text-align:center;\">전체레슨비디오</a></li>
			</div>
			<div style=\"display:inline-block;width:49%;\">
				<li><a href=\"report_monthly_list.html\" class=\"color_review_book TrnTag\"          style=\"display:inline-block;padding:2px 10px;background-color:#005E8A;width:100%;height:25px;line-height:20px;text-align:center;\">정기평가보고서</a></li>
			</div>
			<!--
			<div style=\"display:inline-block;width:49%;margin-top:10px;\">
				<li><a onclick=\"OpenRemoteUrl('../product_order_cart.php?FromDevice=app')\" class=\"color_review_book TrnTag\"          style=\"display:inline-block;padding:2px 10px;background-color:#808040;width:100%;height:25px;line-height:20px;text-align:center;\">교재주문(".$ProductOrderCartCount.")</a></li>
			</div>
			<div style=\"display:inline-block;width:49%;margin-top:10px;\">
				<li><a onclick=\"OpenRemoteUrl('../product_order_list.php?FromDevice=app')\" class=\"color_review_book TrnTag\"          style=\"display:inline-block;padding:2px 10px;background-color:#888888;width:100%;height:25px;line-height:20px;text-align:center;\">주문내역</a></li>
			</div>
			-->
	";
	
}else{
	/* 교재구매 - 없을때 */
	$PageMyPageRoomHTML .= " 
			<div style=\"display:inline-block;width:49%;\">
				<li><a href=\"#\" onclick=\"OpenLessonVideos();\" class=\"color_review_book TrnTag\" style=\"display:inline-block;padding:2px 10px;width:100%;height:25px;line-height:20px;text-align:center;\">전체레슨비디오</a></li>
			</div>
			<div style=\"display:inline-block;width:49%;\">
				<li><a href=\"report_monthly_list.html\" class=\"color_review_book TrnTag\"          style=\"display:inline-block;padding:2px 10px;background-color:#005E8A;width:100%;height:25px;line-height:20px;text-align:center;\">정기평가보고서</a></li>
			</div>
			<!--
			<div style=\"display:inline-block;width:49%;margin-top:10px;\">
				<li><a onclick=\"AlertCommonErr('현재 주문할 교재가 없습니다. 주문할 교재는 선생님께서 선택해 주십니다.')\" class=\"color_review_book TrnTag\"          style=\"display:inline-block;padding:2px 10px;background-color:#c1c1c1;width:100%;height:25px;line-height:20px;text-align:center;\">교재주문(0)</a></li>
			</div>
			<div style=\"display:inline-block;width:49%;margin-top:10px;\">
				<li><a onclick=\"OpenRemoteUrl('../product_order_list.php?FromDevice=app')\" class=\"color_review_book TrnTag\"          style=\"display:inline-block;padding:2px 10px;background-color:#888888;width:100%;height:25px;line-height:20px;text-align:center;\">주문내역</a></li>
			</div>
			-->
	";
}









$ListCount = 1;
$SetTargetStudy = 0;


if ($SelectMonth == date("m") && $SelectYear == date("Y")){//현재달
	if ($GetAllList==""){
		$SelectYearMonthStartDay = date("j");
		$PageMyPageRoomHTML .= "<div class='TrnTag' style=\"width:100%;height:30px;text-align:center;background-color:#cccccc;color:#ffffff;border-radius:10px;margin-top:20px;line-height:30px;\" onclick=\"GetPageMyPageRoom('', '', '1')\">지난수업보기</div> ";
	}
}


$PageMyPageRoomHTML .= "<ul class=\"schedule_list\" style=\"margin-top:20px;\">";



//=========================== test01,test02, test03 샘플 수업 ===========================
if ($LocalLinkMemberID==10722 || $LocalLinkMemberID==10723 || $LocalLinkMemberID==13907 ){ 
	// 멤버로그인아이디와 교재들을 넣어준다.
	if ($LocalLinkMemberID==10722) {
		$memberLoginID = 'test01';
		$LessonAVideo = 'gDBPtoQALZM';
		$LessonBVideo = '9ld3pAr2xA4';
		$LessonBook = '56b963e1dc83dc46cbf4f383323a5326[1].pdf';
		$ReviewQuiz = 119;
	} else if ($LocalLinkMemberID==10723) {
		$memberLoginID = 'test02';
		$LessonAVideo = 'VwSjtvcVhZs';
		$LessonBVideo = '4smEUh4HgPs';
		$LessonBook = '51e480164220cafaada8559608d5f33a.pdf';
		$ReviewQuiz = 163;
	} else if ($LocalLinkMemberID==13907) {
		$memberLoginID = 'test03';
		$LessonAVideo = 'anNHwbMWXa0';
		$LessonBVideo = 'MOXD-UKDAP8&t=56s';
		$LessonBook = 'f26c2b0dfe8af8065c3c50c49a9bef88.pdf';
		$ReviewQuiz = 216;
	}


	$Sql_Del = "delete from BookQuizResultDetails where BookQuizResultID in (select BookQuizResultID from BookQuizResults where ClassID=-1)";
	$Stmt_Del = $DbConn->prepare($Sql_Del);
	$Stmt_Del->execute();
	$Stmt_Del = null;
	
	$Sql_Del = "delete from BookQuizResults where ClassID=-1";
	$Stmt_Del = $DbConn->prepare($Sql_Del);
	$Stmt_Del->execute();
	$Stmt_Del = null;


	$PageMyPageRoomHTML .= "<li style='border:2px solid #FDC644'>";
	$PageMyPageRoomHTML .= "	<div class=\"schedule_inner\">";
	$PageMyPageRoomHTML .= "		<div class=\"schedule_date\">".$ArrWeekDay[date('w', strtotime($SelectYear."-".$SelectMonth."-".$SelectDay))]."<b>".substr("0".$SelectMonth,-2).".".substr("0".$SelectDay,-2)."</b></div>";
	$PageMyPageRoomHTML .= "		<div class=\"schedule_bar\"><img src=\"".$ServerPath."images/img_calendar_bar.png\" class=\"bar\"></div>";
	$PageMyPageRoomHTML .= "		<div class=\"schedule_class\">";
	$PageMyPageRoomHTML .= "			<div class=\"schedule_teacher\">Rica</div>";
	$PageMyPageRoomHTML .= "			<div class=\"schedule_english\">";
	$PageMyPageRoomHTML .= "				<div class=\"schedule_english_left\">샘플</div>";
	$PageMyPageRoomHTML .= "				<div class=\"schedule_english_right\">수업</div>";
	$PageMyPageRoomHTML .= "			</div>";
	$PageMyPageRoomHTML .= "		</div>";
	$PageMyPageRoomHTML .= "		<div class=\"schedule_time\"><img src=\"".$ServerPath."images/icon_clock_black.png\" class=\"icon\">12:00 ~ 12:20</div>";
	$PageMyPageRoomHTML .= "	</div>";

	$PageMyPageRoomHTML .= "	<ul class=\"schedule_btns\">";
	$PageMyPageRoomHTML .= "		<li style=\"width:100%;\"><a href=\"#\" class=\"color_classin\" onclick=\"OpenClassShPreSet('322029', '151_20230704_14_0', 0, '".$memberLoginID."', '".$memberLoginID."');\">수업입장</a></li>";
//	$PageMyPageRoomHTML .= "		<li style=\"width:100%;\"><a href=\"#\" class=\"color_classin\" onclick=\"OpenClassSh_Sample('322029', '151_20230704_14_0', 0, '".$memberLoginID."', '".$memberLoginID."');\">수업입장</a></li>";
	$PageMyPageRoomHTML .= "		<li style=\"width:45%;\"><a href=\"#\" onclick=\"OpenStudyVideo_Sample(1, '".$LessonAVideo."', -1, 1);\" class=\"color_lesson_video\">레슨비디오 A</a></li>";
	$PageMyPageRoomHTML .= "		<li style=\"width:45%;\"><a href=\"#\" onclick=\"OpenStudyVideo_Sample(1, '".$LessonBVideo."', -1, 1);\" class=\"color_lesson_video\">레슨비디오 B</a></li>";
	$PageMyPageRoomHTML .= "		<li style=\"width:45%;\"><a href=\"#\" onclick=\"OpenBookScan_Sample('".$LessonBook."',-1);\" class=\"color_review_book\">학습교재</a></li>";
	$PageMyPageRoomHTML .= "		<li style=\"width:45%;\"><a href=\"#\" onclick=\"OpenStudyQuiz_Sample(".$ReviewQuiz.", -1)\" class=\"color_review_quiz\">리뷰퀴즈</a></li>";
	$PageMyPageRoomHTML .= "		<li id=\"BtnResetDate\" style=\"width:100%;\"><a href=\"#\" onclick=\"OpenResetDateForm_Sample(-1, 1);\" class=\"color_postpone\">수업 연기 요청</a></li>";
	$PageMyPageRoomHTML .= "		<li style=\"width:100%;\"><a href=\"#\" onclick=\"OpenStudentScoreDailyReport_Sample(-1);\" class=\"color_review_quiz\" style=\"background-color:#3D3D3D;\">평가보고서</a></li>";
	$PageMyPageRoomHTML .= "		<li style=\"width:100%;\"><a href=\"#\" onclick=\"OpenTeacherScoreForm_Sample(-1);\" class=\"color_review_quiz\" style=\"background-color:#0080C0;\">강사평가</a></li>";
	$PageMyPageRoomHTML .= "	</ul>";
	$PageMyPageRoomHTML .= "</li>";


}
//=========================== test01 샘플 수업 ===========================




$AddSqlWhere = " 1=1 ";
$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotMaster=1 ";
$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotState=1 ";

$AddSqlWhere = $AddSqlWhere . " and CO.MemberID=".$LocalLinkMemberID." ";
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

	//echo $TotalRowCount;
	//=========================== BB ===========================
	if ($TotalRowCount>0){

		$SqlWhereCenterRenew = "";
		if ($NoIgnoreCenterRenew==1){
			$SqlWhereCenterRenew = " and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ";
		}

		// 오늘 수업이면 등록을 해준다. ===========================================
		if ( date("Y-m-d")==$SelectYear."-".substr("0".$SelectMonth,-2)."-".substr("0".$SelectDay,-2) ){

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
					$LinkScan = "OpenBookScan('".$BookScanID."',".$ClassID.")";
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
			}else if ($ClassProductID==3){ 
				$StrClassProductID = "체험수업";
			}

			
			if ($ClassAttendState==4){
				$StrClassAttendState = "연기된수업";//학생연기
			}else if ($ClassAttendState==5){
				$StrClassAttendState = "연기된수업";//강사연기
			}else if ($ClassAttendState==6){
				$StrClassAttendState = "취소된수업";//학생취소
			}else if ($ClassAttendState==7){
				$StrClassAttendState = "취소된수업";//강사취소
			}else if ($ClassAttendState==8){
				$StrClassAttendState = "교사변경수업";//교사변경수업
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



			$PageMyPageRoomHTML .= "<li ".$LiStyle.">";
			$PageMyPageRoomHTML .= "	<div class=\"schedule_inner\">";
			$PageMyPageRoomHTML .= "		<div class=\"schedule_date\">".$ArrWeekDay[date('w', strtotime($SelectYear."-".$SelectMonth."-".$SelectDay))]."<b>".substr("0".$SelectMonth,-2).".".substr("0".$SelectDay,-2)."</b></div>";
			$PageMyPageRoomHTML .= "		<div class=\"schedule_bar\"><img src=\"".$ServerPath."images/img_calendar_bar.png\" class=\"bar\"></div>";
			$PageMyPageRoomHTML .= "		<div class=\"schedule_class\">";
			$PageMyPageRoomHTML .= "			<div class=\"schedule_teacher\">".$TeacherName."</div>";
			$PageMyPageRoomHTML .= "			<div class=\"schedule_english\">";
			$PageMyPageRoomHTML .= "				<div class=\"schedule_english_left TrnTag\">".$StrClassProductID."</div>";
			$PageMyPageRoomHTML .= "				<div class=\"schedule_english_right TrnTag\">수업</div>";
			$PageMyPageRoomHTML .= "			</div>";
			$PageMyPageRoomHTML .= "		</div>";
			$PageMyPageRoomHTML .= "		<div class=\"schedule_time\"><img src=\"".$ServerPath."images/icon_clock_black.png\" class=\"icon\">".substr("0".$StudyTimeHour,-2).":".substr("0".$StudyTimeMinute,-2)." ~ ".substr("0".$StudyTimeEndHour,-2).":".substr("0".$StudyTimeEndMinute,-2)."</div>";
			$PageMyPageRoomHTML .= "	</div>";
			$PageMyPageRoomHTML .= "	<ul class=\"schedule_btns\">";


			if ($TodayIsHoliday==1 && $ClassState!=2){
				
				$PageMyPageRoomHTML .= "<li style=\"width:100%;\"><a href=\"#\" class=\"color_classin TrnTag\"  style=\"background-color:#888888;\">휴무일 - 연기처리(대상)</a></li>";

			}else{
				//=========================== DD ===========================
				if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==8){//연기, 취소, 교사변경
					
					$PageMyPageRoomHTML .= "<li style=\"width:100%;\"><a href=\"#\" class=\"color_classin TrnTag\"  style=\"background-color:#888888;\">".$StrClassAttendState."</a></li>";

				//=========================== DD ===========================
				}else{

					//if ($ClassStudyType==1 && $FirstClassID==$ClassID){
					//	$PageMyPageRoomHTML .= "<li><a href=\"#\" onclick=\"".$LinkGruideVideo."\" class=\"color_lesson_video\" style=\"background-color:#8000FF;\">학습가이드</a></li>";
					//	$PageMyPageRoomHTML .= "<li><a href=\"#\" onclick=\"".$LinkGruideVideo."\" class=\"color_lesson_video\" style=\"background-color:#566BA8;\">수강규정안내</a></li>";
					//}


					if ($ClassState==0){//등록완료 0:미등록 전 1:등록완료 2:수업완료

						if ($CurrentDateType==0 || $CurrentDateType>0){
							$PageMyPageRoomHTML .= "<li style=\"width:100%;\"><a href=\"#\" class=\"color_classin TrnTag\" style=\"background-color:#F3A39E;\">수업준비중</a></li>";
						}else{
							$PageMyPageRoomHTML .= "<li style=\"width:100%;\"><a href=\"#\" class=\"color_classin TrnTag\" style=\"background-color:#888888;\">경과된수업</a></li>";
						}

					}else{//등록완료 0:미등록 전 1:등록완료 2:수업완료
						
						if ($ClassState==2){//등록완료 0:미등록 전 1:등록완료 2:수업완료
							$PageMyPageRoomHTML .= "<li style=\"width:100%;\"><a href=\"#\" class=\"color_classin TrnTag\" style=\"background-color:#888888;\">종료된수업</a></li>";
						}else{
							if ($CurrentDateType==0 || $CurrentDateType>0){

								if ($ClassLinkType==1){
									$PageMyPageRoomHTML .= "<li style=\"width:100%;\"><a href=\"#\" class=\"color_classin TrnTag\" onclick=\"OpenClassShPreSet('".$ClassID."', '".$CommonShClassCode."', 0, '".$MemberName."', '".$MemberLoginID."');\">수업입장</a></li>";
								}else{
									$PageMyPageRoomHTML .= "<li style=\"width:100%;\"><a href=\"#\" class=\"color_classin TrnTag\" onclick=\"OpenClassCiCheckPreSet(".$ClassID.", '".$CommonCiTelephoneTeacher."', '".$CommonCiTelephoneStudent."', 1, '".$MemberName."', 'MangoiClass_".$ClassID."');\">수업입장</a></li>";
								}
							
							}else{
								$PageMyPageRoomHTML .= "<li style=\"width:100%;\"><a href=\"#\" class=\"color_classin TrnTag\" style=\"background-color:#888888;\">경과된수업</a></li>";
							}
						}

						if ($ClassProductID!=2){
							$PageMyPageRoomHTML .= "<li style=\"width:45%;\"><a href=\"#\" onclick=\"".$LinkVideo."\" class=\"color_lesson_video TrnTag\">레슨비디오 A</a></li>";
							$PageMyPageRoomHTML .= "<li style=\"width:45%;\"><a href=\"#\" onclick=\"".$LinkVideo2."\" class=\"color_lesson_video TrnTag\">레슨비디오 B</a></li>";
							$PageMyPageRoomHTML .= "<li style=\"width:45%;\"><a href=\"#\" onclick=\"".$LinkScan."\" class=\"color_review_book TrnTag\">학습교재</a></li>";
							$PageMyPageRoomHTML .= "<li style=\"width:45%;\"><a href=\"#\" onclick=\"".$LinkQuiz."\" class=\"color_review_quiz TrnTag\">리뷰퀴즈</a></li>";
						}

					}



					if ($CurrentDateType==0 || $CurrentDateType>0){
						
						if ($ClassID==0){
							if ($ClassMemberType==1){// 1:1 수업
								if ($MemberID==13591) {
									$PageMyPageRoomHTML .= "<li id=\"BtnResetDate\" style=\"width:100%;\"><a class=\"color_postpone TrnTag\">수업 연기 요청</a></li>";
								} else {
									$PageMyPageRoomHTML .= "<li id=\"BtnResetDate\" style=\"width:100%;\"><a href=\"#\" onclick=\"javascript:OpenResetDateFormWithReg(".$ClassOrderID.", ".$SelectYear.", ".(int)$SelectMonth.", ".(int)$SelectDay.",  ".(int)$StudyTimeHour.", ".(int)$StudyTimeMinute.", ".$ClassOrderTimeTypeID.", ".$TeacherID.", ".$MemberID.", ".$ClassMemberType.", ".$ClassProductID.");\" class=\"color_postpone TrnTag\">수업 연기 요청</a></li>";
								}
							}else{
								if ($MemberID==13591) {
									$PageMyPageRoomHTML .= "<li id=\"BtnResetDate\" style=\"width:100%;\"><a class=\"color_postpone TrnTag\">수업 연기 요청</a></li>";
								} else {
									$PageMyPageRoomHTML .= "<li id=\"BtnResetDate\" style=\"width:100%;\"><a href=\"#\" onclick=\"OpenResetDateFormErr();\" class=\"color_postpone TrnTag\">수업 연기 요청</a></li>";
								}
							}
						}else{
							if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==8 || $ClassState==2){

							}else{
								if ($ClassMemberType==1){// 1:1 수업
									if ($MemberID==13591) {
										$PageMyPageRoomHTML .= "<li id=\"BtnResetDate\" style=\"width:100%;\"><a class=\"color_postpone TrnTag\">수업 연기 요청</a></li>";
									} else {
										$PageMyPageRoomHTML .= "<li id=\"BtnResetDate\" style=\"width:100%;\"><a href=\"#\" onclick=\"OpenResetDateForm(".$ClassID.", ".$ClassProductID.",  ".(int)$StudyTimeHour.", ".(int)$StudyTimeMinute.");\" class=\"color_postpone TrnTag\">수업 연기 요청</a></li>";
									}
								}else{
									if ($MemberID==13591) {
										$PageMyPageRoomHTML .= "<li id=\"BtnResetDate\" style=\"width:100%;\"><a class=\"color_postpone TrnTag\">수업 연기 요청</a></li>";
									} else {
										$PageMyPageRoomHTML .= "<li id=\"BtnResetDate\" style=\"width:100%;\"><a href=\"#\" onclick=\"OpenResetDateFormErr();\" class=\"color_postpone TrnTag\">수업 연기 요청</a></li>";
									}
								}
							}
						}
	 
					}

					if ($ClassProductID!=2 && $AssmtStudentDailyScoreID!=0){//데일리 평가보고
						$PageMyPageRoomHTML .= "<li style=\"width:100%;\"><a href=\"#\" onclick=\"OpenStudentScoreDailyReport(".$ClassID.");\" class=\"color_review_quiz TrnTag\" style=\"background-color:#3D3D3D;\">평가보고서</a></li>";
					}else if ($ClassProductID==2 && $AssmtStudentLeveltestScoreID!=0){
						$PageMyPageRoomHTML .= "<li style=\"width:100%;\"><a href=\"#\" onclick=\"OpenStudentScoreLeveltestReport(".$AssmtStudentLeveltestScoreID.");\" class=\"color_review_quiz TrnTag\" style=\"background-color:#3D3D3D;\">평가보고서</a></li>";
					}

					if ($ClassState==2 && $ClassProductID==1){
						$PageMyPageRoomHTML .= "<li style=\"width:100%;\"><a href=\"#\" onclick=\"OpenTeacherScoreForm(".$ClassID.");\" class=\"color_review_quiz TrnTag\" style=\"background-color:#0080C0;\">강사평가</a></li>";
					}


				}
				//=========================== DD ===========================

			}

			
			$PageMyPageRoomHTML .= "	</ul>";
			$PageMyPageRoomHTML .= "</li>";

			$ListCount++;

		}
		//=========================== CC ===========================
	
	}
	//=========================== BB ===========================

}
//=========================== AA ===========================




if ($ListCount==1){

	$PageMyPageRoomHTML .= "<li>";
	$PageMyPageRoomHTML .= "	<div class=\"schedule_inner\">";
	$PageMyPageRoomHTML .= "		<div class=\"schedule_time TrnTag\" style=\"height:200px;\"><img src=\"".$ServerPath."images/icon_clock_black.png\" class=\"icon\">등록된 수업이 없습니다.</div>";
	$PageMyPageRoomHTML .= "	</div>";
	$PageMyPageRoomHTML .= "	<ul class=\"schedule_btns\">";
	$PageMyPageRoomHTML .= "		<li><a href=\"main.html\" class=\"color_postpone TrnTag\">닫기</a></li>";
	$PageMyPageRoomHTML .= "	</ul>";
	$PageMyPageRoomHTML .= "</li>";

}

$PageMyPageRoomHTML .= "</ul>";



$PageMyPageRoomHTML .= "	
	
	<table class=\"mypage_survey_wrap\" style=\"display:none;\">
		<tr>
			<th><img src=\"".$ServerPath."".$ServerPath."images/img_survey.png\" class=\"survey_img\"></th>
			<td class=\"survey_left\">
				<h3 class='TrnTag'>\"오늘수업 어떠셨나요?\"</h3>
				<trn class='TrnTag'>칭찬이나 부족한 점에 대한 의견을 작성해주세요.<br>항상 최선을 다하는 Mango-i가 되겠습니다.</trn>
			</td>
			<td class=\"survey_right\">
				<a href=\"#\">
					<img src=\"".$ServerPath."".$ServerPath."images/bg_survey.png\" class=\"survey_bg\">
					<div class=\"survey_link\">
						<div class=\"survey_caption TrnTag\">망고아이<br>설문조사</div>
						<div class=\"survey_go\">GO</div>
					</div>
				</a>
			</td>
		</tr>
	</table>


	<h3 class=\"caption_left_common TrnTag\"><b>학습캘린더</b> 버튼 설명</h3>
	<ul class=\"remark_list\">
		<li>
			<div class=\"remark_left\">
				<span class=\"color_attend\">Attended</span>
				<span class=\"color_absent\">Absent</span>
			</div>
			<div class=\"remark_right TrnTag\">표시 - 출석(Attended), 결석(Absent)</div>
		</li>
		<li>
			<div class=\"remark_left\">
				<span class=\"color_cancel_1\">Canceled</span>
			</div>
			<div class=\"remark_right TrnTag\">강사 결석 또는 인터넷 등의 문제로 인한 휴강 표시</div>
		</li>
		<li>
			<div class=\"remark_left\">
				<span class=\"color_feedback\">Feedback</span>
			</div>
			<div class=\"remark_right TrnTag\">수업 피드백 보기 버튼</div>
		</li>
		<li>
			<div class=\"remark_left\">
				<span class=\"color_finish\">Finished</span>
				<span class=\"color_cancel_2\">Canceled</span>
			</div>
			<div class=\"remark_right TrnTag\">레벨 테스트표시 - 채점 완료(Finished), 학생 결석으로 인한 미실시(Canceled)</div>
		</li>
		<li>
			<div class=\"remark_left\">
				<span class=\"color_result\">Test Result</span>
			</div>
			<div class=\"remark_right TrnTag\">성적표 보기 버튼</div>
		</li>
		<li>
			<div class=\"remark_left\">
				<span class=\"color_classin TrnTag\">강의실 입장</span>
				<span class=\"color_today TrnTag\">오늘 수업</span>
			</div>
			<div class=\"remark_right TrnTag\">오늘 예정된 수업 표시(화상영어의 경우 강의실 입장 버튼을 통해 입장함)</div>
		</li>
		<li>
			<div class=\"remark_left\">
				<span class=\"color_requst TrnTag\">요청사항</span>
			</div>
			<div class=\"remark_right TrnTag\">예약된 수업에 대한 요청 사항이 있을 시 요청사항을 남기는 버튼 </div>
		</li>
		<li>
			<div class=\"remark_left\">
				<span class=\"color_postpone TrnTag\">수업연기</span>
			</div>
			<div class=\"remark_right TrnTag\">예약된 수업을 연기하기 위한 버튼(고정예약의 경우)</div>
		</li>
		<li>
			<div class=\"remark_left\">
				<span class=\"color_cancel_3 TrnTag\">예약취소</span>
			</div>
			<div class=\"remark_right TrnTag\">예약된 수업을 취소하기 위한 버튼(자유예약의 경우)</div>
		</li>
		<li>
			<div class=\"remark_left\">
				<span class=\"color_comment TrnTag\">코멘트 입력중</span>
			</div>
			<div class=\"remark_right TrnTag\">해당 수업 종료 이후 강사가 코멘트를 남기고 있음을 알리는 표시</div>
		</li>
		<li>
			<div class=\"remark_left\">
				<span class=\"color_mark TrnTag\">테스트 채점중</span>
				<span class=\"color_evaluation TrnTag\">테스트 평가중</span>
			</div>
			<div class=\"remark_right TrnTag\">테스트 실시 후, 채점 및 평가 단계를 알리는 표시</div>
		</li>
	</ul>

";

$PageMyPageRoomHTML .= "</section>";



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageMyPageRoomHTML"] = $PageMyPageRoomHTML;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>
