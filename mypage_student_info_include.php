<?
$EduCenterID = 1;

$Sql = "
		select 
				sum(MemberPoint) as MemberPoint
		from MemberPoints A 
		where A.MemberID=:MemberID and A.MemberPointState=1 ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $_LINK_MEMBER_ID_);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberPoint = $Row["MemberPoint"];


$Sql = "
		select 
				A.MemberPhoto,
				DATE_FORMAT(A.MemberRegDateTime,'%Y년 %m월 %d일') as MemberRegDateTime
		from Members A 
		where A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $_LINK_MEMBER_ID_);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberRegDateTime = $Row["MemberRegDateTime"];
$MemberPhoto = $Row["MemberPhoto"];

if ($MemberPhoto==""){
	$StrMemberPhoto = "../images/no_photo.png";
}else{
	$StrMemberPhoto = "../uploads/member_photos/".$MemberPhoto;
}


$SelectWeek = date('w', strtotime(date("Y-m-d")));
$SelectDate = date("Y-m-d");
$SelectYear = date("Y");
$SelectMonth = date("n");
$SelectDay = date("j");


$AddSqlWhere = " 1=1 ";
$AddSqlWhere = $AddSqlWhere . " and CO.MemberID=".$_LINK_MEMBER_ID_."  ";

$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotMaster=1 ";
$AddSqlWhere = $AddSqlWhere . " and ( 

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
								";
$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotState=1 ";

$AddSqlWhere = $AddSqlWhere . " and CO.ClassProgress=11 ";
$AddSqlWhere = $AddSqlWhere . " and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=3 or CO.ClassOrderState=5 or CO.ClassOrderState=6) ";


$ViewTable = "

	select 
		COS.ClassMemberType,
		COS.ClassOrderSlotType,
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
		CLS.ClassAttendState,
		CLS.ClassAttendStateMemberID,
		CLS.ClassState,
		CLS.BookVideoID,
		CLS.BookQuizID,
		CLS.BookScanID,
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
		CT.CenterID as JoinCenterID,
		CT.CenterName as JoinCenterName,
		CT.CenterPayType,
		CT.CenterRenewType,
		CT.CenterStudyEndDate,
		BR.BranchID as JoinBranchID,
		BR.BranchName as JoinBranchName, 
		BRG.BranchGroupID as JoinBranchGroupID,
		BRG.BranchGroupName as JoinBranchGroupName,
		COM.CompanyID as JoinCompanyID,
		COM.CompanyName as JoinCompanyName,
		FR.FranchiseName,
		MB3.MemberLoginID as CenterLoginID,
		TEA2.TeacherName as ClassTeacherName 

	from ClassOrderSlots COS 

			left outer join Classes CLS on COS.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$SelectYear." and CLS.StartMonth=".$SelectMonth." and CLS.StartDay=".$SelectDay." and CLS.StartHour=COS.StudyTimeHour and CLS.StartMinute=COS.StudyTimeMinute and CLS.TeacherID=COS.TeacherID and CLS.ClassAttendState<>99 

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

	where ".$AddSqlWhere." ";


			//결제 안해도 관리자가 등록한 수업 진행가능하도록 주석 처리
			//inner join ClassOrderPayDetails COPD on COS.ClassOrderID=COPD.ClassOrderID 
			//inner join ClassOrderPays COP on COPD.ClassOrderPayID=COP.ClassOrderPayID and (COP.ClassOrderPayProgress=21 or COP.ClassOrderPayProgress=31 or COP.ClassOrderPayProgress=41) 


$SqlWhereCenterRenew = "";
if ($NoIgnoreCenterRenew==1){
	$SqlWhereCenterRenew = " and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ";
}

$Sql = "select 
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
		order by V.StudyTimeHour asc, V.StartMinute asc limit 0,1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$StudyTimeHour = $Row["StudyTimeHour"];
$StudyTimeMinute = $Row["StudyTimeMinute"];
$TeacherName = $Row["TeacherName"];



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
?>
<?if($DomainSiteID==7){?>
    <div class="mypage_top_wrap">
	<div class="mypage_top_left">
		<div class="mypage_photo" style="background-image:url(<?=$StrMemberPhoto?>)">
			<a href="#"><img src="images/btn_camera_black.png" class="mypage_photo_add light_box_btn_photo"></a>
		</div>
		<div class="mypage_info">
			<h3 class="mypage_name"><?=$_LINK_MEMBER_NAME_?>(<?=$_LINK_MEMBER_LOGIN_ID_?>)님</h3>
			<div class="mypage_join"><?=$MemberRegDateTime?>부터 특별한 아이비리그와 함께 하고 있습니다.</div>
			<div class="mypage_point">
				나의 포인트 <b><?=number_format($MemberPoint,0)?>점<small>(<?=number_format($MemberPoint,0)?>원)</small></b> <a href="mypage_point_list.php" class="button_br_black">내역보기</a>
			</div>
			<ul class="mypage_btns">
				<li><a href="member_form.php" class="button_black_yellow">회원정보</a></li>
				<li><a href="mypage_study_history.php" class="button_black_yellow">학습이력</a></li>
				<li><a href="mypage_payment_list.php" class="button_black_yellow">결제내역</a></li>
                <li><a href="mypage_monthly_report.php" class="button_black_yellow">정기평가보고서</a></li>
                <li><a href="lesson_videos.php" class="button_black_yellow">전체레슨비디오</a></li>
                <li><a href="javascript:OpenCalTable('<?=(int)$SelectYear?>', '<?=(int)$SelectMonth?>')" class="button_black_yellow">시간표출력</a></li>
			</ul>
		</div>
	</div>
	<div class="mypage_top_right">
		<div class="mypage_today_class">
			<h3 class="mypage_today">오늘의 수업</h3>
			<div class="mypage_today_teacher">
				<?if ($TodayIsHoliday==1){?>
					<span class="mypage_today_time">휴무일</span>
				<?}else{?>
					<?if ($TeacherName) {?>
					<span class="mypage_today_time"><?=substr("0".$StudyTimeHour,-2)?>:<?=substr("0".$StudyTimeMinute,-2)?></span> <?=$TeacherName?> 선생님
					<?}else{?>
					<span class="mypage_today_time">-</span>
					<?}?>
				<?}?>
			</div>
		</div>
		<ul class="mypage_today_btns">
			<?if ($HideLinkBtn==0) {?>
			<li><a href="mypage_study_room.php" class="button_yellow_black">공부방입장</a></li>
			<li><a href="mypage_payment_list.php" class="button_br_yellow">수강연장</a></li>
			<?}?>
		</ul>
	</div>
</div>
<?} else if($DomainSiteID==8){?>
    <div class="mypage_top_wrap">
	<div class="mypage_top_left">
		<div class="mypage_photo" style="background-image:url(<?=$StrMemberPhoto?>)">
			<a href="#"><img src="images/btn_camera_black.png" class="mypage_photo_add light_box_btn_photo"></a>
		</div>
		<div class="mypage_info">
			<h3 class="mypage_name"><?=$_LINK_MEMBER_NAME_?>(<?=$_LINK_MEMBER_LOGIN_ID_?>)님</h3>
			<div class="mypage_join"><?=$MemberRegDateTime?>부터 특별한 잉글리씨드와 함께 하고 있습니다.</div>
			<div class="mypage_point">
				나의 포인트 <b><?=number_format($MemberPoint,0)?>점<small>(<?=number_format($MemberPoint,0)?>원)</small></b> <a href="mypage_point_list.php" class="button_br_black">내역보기</a>
			</div>
			<ul class="mypage_btns">
				<li><a href="member_form.php" class="button_black_yellow">회원정보</a></li>
				<li><a href="mypage_study_history.php" class="button_black_yellow">학습이력</a></li>
				<li><a href="mypage_payment_list.php" class="button_black_yellow">결제내역</a></li>
                <li><a href="mypage_monthly_report.php" class="button_black_yellow">정기평가보고서</a></li>
                <li><a href="lesson_videos.php" class="button_black_yellow">전체레슨비디오</a></li>
                <li><a href="javascript:OpenCalTable('<?=(int)$SelectYear?>', '<?=(int)$SelectMonth?>')" class="button_black_yellow">시간표출력</a></li>
			</ul>
		</div>
	</div>
	<div class="mypage_top_right">
		<div class="mypage_today_class">
			<h3 class="mypage_today">오늘의 수업</h3>
			<div class="mypage_today_teacher">
				<?if ($TodayIsHoliday==1){?>
					<span class="mypage_today_time">휴무일</span>
				<?}else{?>
					<?if ($TeacherName) {?>
					<span class="mypage_today_time"><?=substr("0".$StudyTimeHour,-2)?>:<?=substr("0".$StudyTimeMinute,-2)?></span> <?=$TeacherName?> 선생님
					<?}else{?>
					<span class="mypage_today_time">-</span>
					<?}?>
				<?}?>
			</div>
		</div>
		<ul class="mypage_today_btns">
			<?if ($HideLinkBtn==0) {?>
			<li><a href="mypage_study_room.php" class="button_yellow_black">공부방입장</a></li>
			<li><a href="mypage_payment_list.php" class="button_br_yellow">수강연장</a></li>
			<?}?>
		</ul>
	</div>
</div>
<?} else if($DomainSiteID==9){?>
    <div class="mypage_top_wrap">
        <div class="mypage_top_left">
            <div class="mypage_photo" style="background-image:url(<?=$StrMemberPhoto?>)">
                <a href="#"><img src="images/btn_camera_black.png" class="mypage_photo_add light_box_btn_photo"></a>
            </div>
            <div class="mypage_info">
                <h3 class="mypage_name"><?=$_LINK_MEMBER_NAME_?>(<?=$_LINK_MEMBER_LOGIN_ID_?>)님</h3>
                <div class="mypage_join"><?=$MemberRegDateTime?>부터 특별한 이엔지 화상영어와 함께 하고 있습니다.</div>
                <div class="mypage_point">
                    나의 포인트 <b><?=number_format($MemberPoint,0)?>점<small>(<?=number_format($MemberPoint,0)?>원)</small></b> <a href="mypage_point_list.php" class="button_br_black">내역보기</a>
                </div>
                <ul class="mypage_btns">
                    <li><a href="member_form.php" class="button_black_yellow">회원정보</a></li>
                    <li><a href="mypage_study_history.php" class="button_black_yellow">학습이력</a></li>
                    <li><a href="mypage_payment_list.php" class="button_black_yellow">결제내역</a></li>
                    <li><a href="mypage_monthly_report.php" class="button_black_yellow">정기평가보고서</a></li>
                    <li><a href="lesson_videos.php" class="button_black_yellow">전체레슨비디오</a></li>
                    <li><a href="javascript:OpenCalTable('<?=(int)$SelectYear?>', '<?=(int)$SelectMonth?>')" class="button_black_yellow">시간표출력</a></li>
                </ul>
            </div>
        </div>
        <div class="mypage_top_right">
            <div class="mypage_today_class">
                <h3 class="mypage_today">오늘의 수업</h3>
                <div class="mypage_today_teacher">
                    <?if ($TodayIsHoliday==1){?>
                        <span class="mypage_today_time">휴무일</span>
                    <?}else{?>
                        <?if ($TeacherName) {?>
                            <span class="mypage_today_time"><?=substr("0".$StudyTimeHour,-2)?>:<?=substr("0".$StudyTimeMinute,-2)?></span> <?=$TeacherName?> 선생님
                        <?}else{?>
                            <span class="mypage_today_time">-</span>
                        <?}?>
                    <?}?>
                </div>
            </div>
            <ul class="mypage_today_btns">
                <?if ($HideLinkBtn==0) {?>
                    <li><a href="mypage_study_room.php" class="button_yellow_black">공부방입장</a></li>
                    <li><a href="mypage_payment_list.php" class="button_br_yellow">수강연장</a></li>
                <?}?>
            </ul>
        </div>
    </div>
<?}else{?>
<div class="mypage_top_wrap">
	<div class="mypage_top_left">
		<div class="mypage_photo" style="background-image:url(<?=$StrMemberPhoto?>)">
			<a href="#"><img src="images/btn_camera_black.png" class="mypage_photo_add light_box_btn_photo"></a>
		</div>
		<div class="mypage_info">
			<h3 class="mypage_name"><?=$_LINK_MEMBER_NAME_?>(<?=$_LINK_MEMBER_LOGIN_ID_?>)님</h3>
			<div class="mypage_join"><?=$MemberRegDateTime?>부터 특별한 망고아이와 함께 하고 있습니다.</div>
			<div class="mypage_point">
				나의 포인트 <b><?=number_format($MemberPoint,0)?>점<small>(<?=number_format($MemberPoint,0)?>원)</small></b> <a href="mypage_point_list.php" class="button_br_black">내역보기</a>
			</div>
			<ul class="mypage_btns">
				<li><a href="member_form.php" class="button_black_yellow">회원정보</a></li>
				<li><a href="mypage_study_history.php" class="button_black_yellow">학습이력</a></li>
				<li><a href="mypage_payment_list.php" class="button_black_yellow">결제내역</a></li>
                <li><a href="mypage_monthly_report.php" class="button_black_yellow">정기평가보고서</a></li>
                <li><a href="lesson_videos.php" class="button_black_yellow">전체레슨비디오</a></li>
                <li><a href="javascript:OpenCalTable('<?=(int)$SelectYear?>', '<?=(int)$SelectMonth?>')" class="button_black_yellow">시간표출력</a></li>
			</ul>
		</div>
	</div>
	<div class="mypage_top_right">
		<div class="mypage_today_class">
			<h3 class="mypage_today">오늘의 수업</h3>
			<div class="mypage_today_teacher">
				<?if ($TodayIsHoliday==1){?>
					<span class="mypage_today_time">휴무일</span>
				<?}else{?>
					<?if ($TeacherName) {?>
					<span class="mypage_today_time"><?=substr("0".$StudyTimeHour,-2)?>:<?=substr("0".$StudyTimeMinute,-2)?></span> <?=$TeacherName?> 선생님
					<?}else{?>
					<span class="mypage_today_time">-</span>
					<?}?>
				<?}?>
			</div>
		</div>
		<ul class="mypage_today_btns">
			<?if ($HideLinkBtn==0) {?>
			<li><a href="mypage_study_room.php" class="button_yellow_black">공부방입장</a></li>
			<li><a href="mypage_payment_list.php" class="button_br_yellow">수강연장</a></li>
			<?}?>
		</ul>
	</div>
</div>
<?}?>