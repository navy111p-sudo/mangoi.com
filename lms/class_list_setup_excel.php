<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');


header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = CLASS_STATUS.xls" );
header( "Content-Description: PHP4 Generated Data" );
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<meta charset="utf-8">
</head>

<body>
<?php
$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "";
if ($type==""){
    $type = "0";
} 

if ($type==""){
	$type = "1";
}

$SearchState = $type;

$MainMenuID = 17;

if ($SearchState=="1"){//일반
    $SubMenuID = 1701;
}else if ($SearchState=="2"){//미등록수업
    $SubMenuID = 1702;
}else if ($SearchState=="9"){//연기/취소/변경
    $SubMenuID = 1709;
}


?>



<?php
$ArrWeekDayStr = explode(",","Sun.,Mon.,Tue.,Wed.,Thu.,Fri.,Sat.");


$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$EduCenterID = isset($_REQUEST["EduCenterID"]) ? $_REQUEST["EduCenterID"] : "";
if ($EduCenterID==""){
	$EduCenterID="1";
}


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchFranchiseID = isset($_REQUEST["SearchFranchiseID"]) ? $_REQUEST["SearchFranchiseID"] : "";
$SearchCompanyID = isset($_REQUEST["SearchCompanyID"]) ? $_REQUEST["SearchCompanyID"] : "";
$SearchBranchGroupID = isset($_REQUEST["SearchBranchGroupID"]) ? $_REQUEST["SearchBranchGroupID"] : "";
$SearchBranchID = isset($_REQUEST["SearchBranchID"]) ? $_REQUEST["SearchBranchID"] : "";
$SearchCenterID = isset($_REQUEST["SearchCenterID"]) ? $_REQUEST["SearchCenterID"] : "";
$SearchTeacherID = isset($_REQUEST["SearchTeacherID"]) ? $_REQUEST["SearchTeacherID"] : "";

$SearchStartHour = isset($_REQUEST["SearchStartHour"]) ? $_REQUEST["SearchStartHour"] : "";
$SearchStartMinute = isset($_REQUEST["SearchStartMinute"]) ? $_REQUEST["SearchStartMinute"] : "";
$SearchEndHour = isset($_REQUEST["SearchEndHour"]) ? $_REQUEST["SearchEndHour"] : "";
$SearchEndMinute = isset($_REQUEST["SearchEndMinute"]) ? $_REQUEST["SearchEndMinute"] : "";

$SelectYear = isset($_REQUEST["SelectYear"]) ? $_REQUEST["SelectYear"] : "";
$SelectMonth = isset($_REQUEST["SelectMonth"]) ? $_REQUEST["SelectMonth"] : "";
$SelectDay = isset($_REQUEST["SelectDay"]) ? $_REQUEST["SelectDay"] : "";

$SearchNoSetQnV = isset($_REQUEST["SearchNoSetQnV"]) ? $_REQUEST["SearchNoSetQnV"] : "";

$ListSelectResetDate = isset($_REQUEST["ListSelectResetDate"]) ? $_REQUEST["ListSelectResetDate"] : "";
$ListSelectResetDatePage = isset($_REQUEST["ListSelectResetDatePage"]) ? $_REQUEST["ListSelectResetDatePage"] : "";
if ($ListSelectResetDatePage!=""){
	$CurrentPage = $ListSelectResetDatePage;
}


if ($SearchNoSetQnV!="1"){
	$SearchNoSetQnV = "0";
}


if ($_LINK_ADMIN_LEVEL_ID_==15){
	$SearchTeacherID = $_LINK_ADMIN_TEACHER_ID_;
}


if ($SelectYear==""){
	$SelectYear = date("Y");
}
if ($SelectMonth==""){
	$SelectMonth = date("m");
}
if ($SelectDay==""){
	$SelectDay = date("d");
}

$SelectDate = $SelectYear."-".$SelectMonth."-".$SelectDay;

$SelectDateWeek = date('w', strtotime($SelectDate));
$WeekDayStr = $ArrWeekDayStr[$SelectDateWeek];

$PrevYear = date("Y", strtotime("-1 day", strtotime($SelectDate)));
$PrevMonth = date("m", strtotime("-1 day", strtotime($SelectDate)));
$PrevDay = date("d", strtotime("-1 day", strtotime($SelectDate)));

$NextYear = date("Y", strtotime("1 day", strtotime($SelectDate)));
$NextMonth = date("m", strtotime("1 day", strtotime($SelectDate)));
$NextDay = date("d", strtotime("1 day", strtotime($SelectDate)));





//================== 서치폼 감추기 =================
$HideSearchCenterID = 0;
$HideSearchBranchID = 0;
$HideSearchBranchGroupID = 0;
$HideSearchCompanyID = 0;
$HideSearchFranchiseID = 0;
$HideSearchTeacherID = 0;

if ($_LINK_ADMIN_LEVEL_ID_==0 or $_LINK_ADMIN_LEVEL_ID_==1){//마스터
	//모두허용
}else if ($_LINK_ADMIN_LEVEL_ID_==3 or $_LINK_ADMIN_LEVEL_ID_==4){//프랜차이즈 관리자
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	
	$HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==6 or $_LINK_ADMIN_LEVEL_ID_==7){//대표지사 관리자
	$HideSearchFranchiseID = 1;
	$HideSearchCompanyID = 1;
	$HideSearchBranchGroupID = 1;

	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;

}else if ($_LINK_ADMIN_LEVEL_ID_==9 or $_LINK_ADMIN_LEVEL_ID_==10){//지사 관리자
	$SearchBranchID = $_LINK_ADMIN_BRANCH_ID_;
	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;

	$HideSearchBranchID = 1;
	$HideSearchBranchGroupID = 1;
	$HideSearchCompanyID = 1;
	$HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==12 or $_LINK_ADMIN_LEVEL_ID_==13){//대리점 관리자
	$SearchCenterID = $_LINK_ADMIN_CENTER_ID_;
	$SearchBranchID = $_LINK_ADMIN_BRANCH_ID_;
	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;

	$HideSearchCenterID = 1;
	$HideSearchBranchID = 1;
	$HideSearchBranchGroupID = 1;
	$HideSearchCompanyID = 1;
	$HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==15){//강사 
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	
	$HideSearchFranchiseID = 1;
	$HideSearchTeacherID = 1;
}
//================== 서치폼 감추기 =================


if (!$CurrentPage){
    $CurrentPage = 1;    
}
if ($PageListNum==""){
    $PageListNum = 30;
}



if ($SearchStartHour==""){
    $SearchStartHour=0;
}
if ($SearchStartMinute==""){
    $SearchStartMinute=0;
}
if ($SearchEndHour==""){
    $SearchEndHour=23;
}
if ($SearchEndMinute==""){
    $SearchEndMinute=50;
}


if ($PageListNum!=""){
    $ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}


$AddSqlWhere = $AddSqlWhere . " and MB.MemberState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and CT.CenterState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and BR.BranchState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and BRG.BranchGroupState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and COM.CompanyState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and FR.FranchiseState<>0 ";

if ($SearchText!=""){
    $ListParam = $ListParam . "&SearchText=" . $SearchText;
    $AddSqlWhere = $AddSqlWhere . " and (MB.MemberName like '%".$SearchText."%' or MB.MemberLoginID like '%".$SearchText."%' or MB.MemberNickName like '%".$SearchText."%') ";
}

if ($SearchFranchiseID!=""){
    $ListParam = $ListParam . "&SearchFranchiseID=" . $SearchFranchiseID;
    $AddSqlWhere = $AddSqlWhere . " and COM.FranchiseID=$SearchFranchiseID ";
}

if ($SearchCompanyID!=""){
    $ListParam = $ListParam . "&SearchCompanyID=" . $SearchCompanyID;
    $AddSqlWhere = $AddSqlWhere . " and BRG.CompanyID=$SearchCompanyID ";
}

if ($SearchBranchGroupID!=""){
    $ListParam = $ListParam . "&SearchBranchGroupID=" . $SearchBranchGroupID;
    $AddSqlWhere = $AddSqlWhere . " and BR.BranchGroupID=$SearchBranchGroupID ";
}

if ($SearchBranchID!=""){
    $ListParam = $ListParam . "&SearchBranchID=" . $SearchBranchID;
    $AddSqlWhere = $AddSqlWhere . " and CT.BranchID=$SearchBranchID ";
}

if ($SearchCenterID!=""){
    $ListParam = $ListParam . "&SearchCenterID=" . $SearchCenterID;
    $AddSqlWhere = $AddSqlWhere . " and MB.CenterID=$SearchCenterID ";
}

if ($SearchNoSetQnV!=0) {
	$ListParam = $ListParam . "&SearchNoSetQnV=" . $SearchNoSetQnV;
}

if ($SearchTeacherID!=""){
	$AddSqlWhere = $AddSqlWhere . " and COS.TeacherID=".$SearchTeacherID." ";
}

//============================
$SearchStartHourMinute = substr("0".$SearchStartHour,-2) . substr("0".$SearchStartMinute,-2);
$ListParam = $ListParam . "&SearchStartHour=" . $SearchStartHour;
$ListParam = $ListParam . "&SearchStartMinute=" . $SearchStartMinute;
$AddSqlWhere = $AddSqlWhere . " and concat(lpad(COS.StudyTimeHour,2,0) , lpad(COS.StudyTimeMinute,2,0))>=$SearchStartHourMinute ";

$SearchEndHourMinute = substr("0".$SearchEndHour,-2) . substr("0".$SearchEndMinute,-2);
$ListParam = $ListParam . "&SearchEndHour=" . $SearchEndHour;
$ListParam = $ListParam . "&SearchEndMinute=" . $SearchEndMinute;
$AddSqlWhere = $AddSqlWhere . " and concat(lpad(COS.StudyTimeHour,2,0) , lpad(COS.StudyTimeMinute,2,0))<=$SearchEndHourMinute ";
//============================




if ($SelectYear!=""){
    $ListParam = $ListParam . "&SelectYear=" . $SelectYear;
}
if ($SelectMonth!=""){
    $ListParam = $ListParam . "&SelectMonth=" . $SelectMonth;
}
if ($SelectDay!=""){
    $ListParam = $ListParam . "&SelectDay=" . $SelectDay;
}


if ($SearchState!="1"){//2:미등록 3:강사취소 4:학생취소 //0:준비 1:출석 2:지각 3:결석 4:학생연기 5:교사연기
	if ($SearchState=="2"){
		$AddSqlWhere = $AddSqlWhere . " and CLS.ClassAttendState is NULL ";
	}else if ($SearchState=="9"){
		$AddSqlWhere = $AddSqlWhere . " and CLS.ClassAttendState is not NULL and ( CLS.ClassAttendState=4 or CLS.ClassAttendState=5 or CLS.ClassAttendState=6 or CLS.ClassAttendState=7 or CLS.ClassAttendState=8 ) ";
	}
}
$ListParam = $ListParam . "&type=" . $SearchState;


$AddSqlWhere = $AddSqlWhere . " and TEA.TeacherState=1 ";


$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotMaster=1 ";


$AddSqlWhere = $AddSqlWhere . " and ( 
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

$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotState=1 ";

$AddSqlWhere = $AddSqlWhere . " and CO.ClassProgress=11 ";
$AddSqlWhere = $AddSqlWhere . " and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=3 or CO.ClassOrderState=5 or CO.ClassOrderState=6) ";


if ($SearchState=="1" && $_LINK_ADMIN_LEVEL_ID_==15){//일반
	$AddSqlWhere = $AddSqlWhere . " and ( CLS.ClassAttendState is NULL or  CLS.ClassAttendState<4 ) ";
}


if ($SearchNoSetQnV=="1"){
	
	
	$AddSqlWhere = $AddSqlWhere . " 
			and ( 
					( CLS.BookSystemType=0 and CLS.BookRegForReason=0 and ( CLS.BookQuizID=0 or CLS.BookQuizID is null) and (CLS.BookVideoID=0 or CLS.BookVideoID is null) and (CLS.BookScanID=0 or CLS.BookScanID is null) )
					or
					( CLS.BookSystemType=1 and CLS.BookRegForReason=0 and ( CLS.BookQuizID=0 or CLS.BookQuizID is null) and (CLS.BookVideoID=0 or CLS.BookVideoID is null) and (CLS.BookWebookUnitID=0 or CLS.BookWebookUnitID is null) )
					
				)
				
			and CLS.ClassAttendState<=3 
			";
	
}


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
    $ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);



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
		COS.ClassOrderSlotEndDate, 
		concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0)) as ClassStartTime, 

		CO.ClassMemberTypeGroupID,
		CO.ClassOrderID,
		CO.ClassProductID,
		CO.ClassOrderTimeTypeID,
		CO.MemberID,
		CO.ClassOrderStartDate,
		CO.ClassOrderEndDate,
		CO.ClassOrderState,

		ifnull(CLS.ClassID,0) as ClassID,
		CLS.TeacherInDateTime, 
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
		ifnull(CLS.ClassAttendState,-1) as ClassAttendState,
		CLS.ClassAttendStateMemberID,
		CLS.ClassAttendStateMsg,
		ifnull(CLS.ClassState, 0) as ClassState,
		CLS.BookVideoID,
		CLS.BookQuizID,
		CLS.BookScanID,
		CLS.BookWebookUnitID, 
		CLS.BookSystemType, 
		CLS.ClassRegDateTime,
		CLS.ClassModiDateTime,

		MB.MemberName,
		MB.MemberPayType,
		MB.MemberChangeTeacher,
		MB.MemberNickName,
		MB.MemberLoginID, 
        MB.MemberLevelID,
		MB.MemberCiTelephone,

		AES_DECRYPT(UNHEX(MB.MemberPhone1),'$EncryptionKey') as DecMemberPhone1,

		TEA.TeacherName,
		MB2.MemberLoginID as TeacherLoginID, 
		MB2.MemberCiTelephone as TeacherCiTelephone,
		CT.CenterID as JoinCenterID,
		CT.CenterName as JoinCenterName,
		CT.MemberAcceptCallByTeacher,
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
		TEA2.TeacherName as ClassTeacherName,
		
		(select count(*) from Classes where ClassOrderID=COS.ClassOrderID and ClassState=2 and TIMESTAMPDIFF(minute, StartDateTime, CLS.StartDateTime)>0 and CLS.ClassState=2) as LastStudyClassCount

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


$Sql = "select count(*) TotalRowCount from 
			(select 
					count(*) 
			from ($ViewTable) V 
			
			where 
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
			group by V.ClassMemberType, V.ClassOrderSlotType, V.ClassOrderSlotDate, V.TeacherID, V.StudyTimeWeek, V.StudyTimeHour, V.StudyTimeMinute
			) VV
		";


$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

if ($PageListNum!="0"){
	$TotalPageCount = ceil($TotalRowCount / $PageListNum);
	$StartRowNum = $PageListNum * ($CurrentPage - 1 );
}else{
	$TotalPageCount = $TotalRowCount;
	$StartRowNum = 0;
}



$SqlWhereCenterRenew = "";
if ($NoIgnoreCenterRenew==1){
	$SqlWhereCenterRenew = " and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ";
}


$Sql = "
        select 
            V.*
        from ($ViewTable) V 

		where 
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
		group by V.ClassMemberType, V.ClassOrderSlotType, V.ClassOrderSlotDate, V.TeacherID, V.StudyTimeWeek, V.StudyTimeHour, V.StudyTimeMinute
        order by V.StudyTimeHour asc, V.StudyTimeMinute, V.TeacherID asc
";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

?>



<table border="1">
<thead>
	<tr>
		<td>NO</td>
		<td>인원</td>
		<td>구분</td>
		<td>날짜</td>
		<td>시작시간</td>
		<td>종료시간</td>
		<td>-</td>
		<td>대리점</td>
		<td>학생</td>
		<td>아이디</td>
		<td>강사명</td>
		<td>강사입장</td>
		<td>출결현황</td>
		<td>교재</td>
		<td>비디오</td>
		<td>퀴즈</td>
	</tr>
</thead>
<tbody>
	
	<?php
	$OldListCount = 0;
	$ListCount = 1;
	$CheckListCount = 1;
	
	//$StrClassRegScript = "";
	$OpenClassCount = 0;
	$LastClassNotAssmtCount = 0;
	$LastClassNotAssmtClasses = "|";
	while($Row = $Stmt->fetch()) {
		$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;
		
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
		$ClassOrderSlotEndDate = $Row["ClassOrderSlotEndDate"];
		$ClassStartTime = $Row["ClassStartTime"];
		

		$ClassOrderID = $Row["ClassOrderID"];
		$ClassProductID = $Row["ClassProductID"];
		$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
		
		$ClassID = $Row["ClassID"];
		$TeacherInDateTime = $Row["TeacherInDateTime"];
		$ClassLinkType = $Row["ClassLinkType"];
		$MemberID = $Row["MemberID"];
		
		$ClassOrderStartDate = $Row["ClassOrderStartDate"];
		$ClassOrderEndDate = $Row["ClassOrderEndDate"];

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
		$ClassAttendStateMsg = $Row["ClassAttendStateMsg"];
		$ClassState = $Row["ClassState"];

		$BookVideoID = $Row["BookVideoID"];
		$BookQuizID = $Row["BookQuizID"];
		$BookScanID = $Row["BookScanID"];
		$BookWebookUnitID = $Row["BookWebookUnitID"];
		$BookSystemType = $Row["BookSystemType"];


		$ClassRegDateTime = $Row["ClassRegDateTime"];
		$ClassModiDateTime = $Row["ClassModiDateTime"];
		
		$MemberName = $Row["MemberName"];
		$MemberPayType = $Row["MemberPayType"];
		$MemberChangeTeacher = $Row["MemberChangeTeacher"];
		$MemberAcceptCallByTeacher = $Row["MemberAcceptCallByTeacher"];

		$MemberNickName = $Row["MemberNickName"];
		$MemberLoginID = $Row["MemberLoginID"];
		$MemberLevelID = $Row["MemberLevelID"];
		$MemberCiTelephone = $Row["MemberCiTelephone"];
		$DecMemberPhone1 = $Row["DecMemberPhone1"];
		

		$CenterPayType = $Row["CenterPayType"];
		$CenterStudyEndDate = $Row["CenterStudyEndDate"];

		$TeacherName = $Row["TeacherName"];
		$TeacherLoginID = $Row["TeacherLoginID"];
		$TeacherCiTelephone = $Row["TeacherCiTelephone"];
		
		$CenterID = $Row["JoinCenterID"];
		$CenterName = $Row["JoinCenterName"];
		$BranchID = $Row["JoinBranchID"];
		$BranchName = $Row["JoinBranchName"];
		$BranchGroupID = $Row["JoinBranchGroupID"];
		$BranchGroupName = $Row["JoinBranchGroupName"];
		$CompanyID = $Row["JoinCompanyID"];
		$CompanyName = $Row["JoinCompanyName"];
		$FranchiseName = $Row["FranchiseName"];

		$CenterLoginID = $Row["CenterLoginID"];
		$LastStudyClassCount = $Row["LastStudyClassCount"];


		if ($ClassOrderTimeTypeID==2){
			$StrClassOrderTimeTypeName = "20 min";
			$StudyEndTimeHourPlus = 20;
		}else if ($ClassOrderTimeTypeID==3){
			$StrClassOrderTimeTypeName = "30 min";
			$StudyEndTimeHourPlus = 30;
		}else if ($ClassOrderTimeTypeID==4){
			$StrClassOrderTimeTypeName = "40 min";
			$StudyEndTimeHourPlus = 40;
		}

		$StudyEndTimeHour = $StudyTimeHour;
		$StudyEndTimeMinute = $StudyTimeMinute + $StudyEndTimeHourPlus;
		if ($StudyEndTimeMinute>=60){
			$StudyEndTimeHour = $StudyEndTimeHour + 1;
			$StudyEndTimeMinute = $StudyEndTimeMinute - 60;
		}
		$StrClassEndTime = substr("0".$StudyEndTimeHour,-2).":".substr("0".$StudyEndTimeMinute,-2);



		$CalDateDiff = (int)str_replace("-","",$SelectDate) - (int)date("Ymd");
		if ($ClassAttendState==-1){
			$StrClassAttendState = $예정[$LangID];

			if ($CalDateDiff < 0){
				$StrClassAttendState = $미설정[$LangID];
			}

		}else if ($ClassAttendState==0){
			$StrClassAttendState = $미설정[$LangID];
		}else if ($ClassAttendState==1){
			$StrClassAttendState = $출석[$LangID];
		}else if ($ClassAttendState==2){
			$StrClassAttendState = $지각[$LangID];
		}else if ($ClassAttendState==3){
			$StrClassAttendState = $결석[$LangID];
		}else if ($ClassAttendState==4){
			$StrClassAttendState = $연기된수업[$LangID]."(S)";//학생연기
		}else if ($ClassAttendState==5){
			$StrClassAttendState = $연기된수업[$LangID]."(T)";//강사연기
		}else if ($ClassAttendState==6){
			$StrClassAttendState = $취소된수업[$LangID]."(S)";//학생취소
		}else if ($ClassAttendState==7){
			$StrClassAttendState = $취소된수업[$LangID]."(T)";//강사취소
		}else if ($ClassAttendState==8){
			$StrClassAttendState = $교사변경수업[$LangID];//교사변경수업
		}else{

		}


		if ($ClassProductID==1){
			$StrClassProductID = "수업";
		}else if ($ClassProductID==2){
			$StrClassProductID = "레벨";
		}else if ($ClassProductID==3){
			$StrClassProductID = "체험";
		}


		if ($ClassMemberType==1){//1:1 이면....
		?>
		<tr>
			<td><?=$ListNumber?></td>
			<td>1:1</td>
			<td><?=$StrClassProductID?></td>
			<td><?=$SelectYear?>-<?=substr("0".$SelectMonth,-2)?>-<?=substr("0".$SelectDay,-2)?></td>
			<td><?=ConvAmPm($ClassStartTime)?></td>
			<td><?=ConvAmPm($StrClassEndTime)?></td>
			<td><?=$StrClassOrderTimeTypeName?></td>
			<td><?=$CenterName?></td>
			<td><?=$MemberName?></td>
			<td><?=$MemberLoginID?></td>
			<td><?=$TeacherName?></td>
			<td><?=$TeacherInDateTime?></td>
			<td><?=$StrClassAttendState?></td>
			<td>
				<?if($BookSystemType==0 && $BookScanID!="" && $BookScanID!=0) {?>
				O
				<?}else if($BookSystemType==1 && $BookWebookUnitID!="" && $BookWebookUnitID!=0) {?>
				O
				<?}else{?>

				<?}?>
			</td>
			<td>
				<!-- Video A -->
				<?if($BookVideoID==0) { ?>
					
				<? } else { ?>
					O
				<? } ?>
			</td>
			<td>
				<?if($BookQuizID==0) { ?>
					
				<? } else { ?>
					O
				<? } ?>
			</td>
		</tr>
		<?
		}else{
			
			if ($ClassMemberType==2){
				$ClassMemberTypeName = "1:2";
			}else if ($ClassMemberType==3){
				$ClassMemberTypeName = "G";
			}


			$SqlWhereCenterRenew = "";
			if ($NoIgnoreCenterRenew==1){
				$SqlWhereCenterRenew = " and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ";
			}

			$Sql_G = "
					select 
						count(*) as GroupRowCount
					from ($ViewTable) V
					where 
						V.ClassMemberType=".$ClassMemberType." 
						and V.ClassOrderSlotType=".$ClassOrderSlotType." 
						
						and V.TeacherID=".$TeacherID." 
						and V.StudyTimeWeek=".$StudyTimeWeek." 
						and V.StudyTimeHour=".$StudyTimeHour." 
						and V.StudyTimeMinute=".$StudyTimeMinute."  

						and
							(
						
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
							)


			";
			$Stmt_G = $DbConn->prepare($Sql_G);
			$Stmt_G->execute();
			$Stmt_G->setFetchMode(PDO::FETCH_ASSOC);
			$Row_G = $Stmt_G->fetch();
			$Stmt_G = null;
			$GroupRowCount = $Row_G["GroupRowCount"];


			$SqlWhereCenterRenew = "";
			if ($NoIgnoreCenterRenew==1){
				$SqlWhereCenterRenew = " and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ";
			}

			$Sql_G = "
					select 
						V.*
					from ($ViewTable) V
					where 
						V.ClassMemberType=".$ClassMemberType." 
						and V.ClassOrderSlotType=".$ClassOrderSlotType." 
						
						and V.TeacherID=".$TeacherID." 
						and V.StudyTimeWeek=".$StudyTimeWeek." 
						and V.StudyTimeHour=".$StudyTimeHour." 
						and V.StudyTimeMinute=".$StudyTimeMinute."  



						and
							(
						
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
							)

					order by V.MemberID asc
			";

			$Stmt_G = $DbConn->prepare($Sql_G);
			$Stmt_G->execute();
			$Stmt_G->setFetchMode(PDO::FETCH_ASSOC);
			
			$GroupTrColor = "#FFFBFF";
			$GroupListCount = 1;
			$RegClassCount = 0;
			while($Row_G = $Stmt_G->fetch()) {


				$ClassMemberTypeGroupID = $Row_G["ClassMemberTypeGroupID"];
				$ClassMemberType = $Row_G["ClassMemberType"];
				$ClassOrderSlotType = $Row_G["ClassOrderSlotType"];
				$ClassOrderSlotType2 = $Row_G["ClassOrderSlotType2"];
				$ClassOrderSlotDate = $Row_G["ClassOrderSlotDate"];
				$TeacherID = $Row_G["TeacherID"];
				$ClassOrderSlotMaster = $Row_G["ClassOrderSlotMaster"];
				$StudyTimeWeek = $Row_G["StudyTimeWeek"];
				$StudyTimeHour = $Row_G["StudyTimeHour"];
				$StudyTimeMinute = $Row_G["StudyTimeMinute"];
				$ClassOrderSlotState = $Row_G["ClassOrderSlotState"];
				$ClassOrderSlotEndDate = $Row_G["ClassOrderSlotEndDate"];
				$ClassStartTime = $Row_G["ClassStartTime"];
				

				$ClassOrderID = $Row_G["ClassOrderID"];
				$ClassProductID = $Row_G["ClassProductID"];
				$ClassOrderTimeTypeID = $Row_G["ClassOrderTimeTypeID"];
				
				$ClassID = $Row_G["ClassID"];
				$TeacherInDateTime = $Row_G["TeacherInDateTime"];
				$ClassLinkType = $Row_G["ClassLinkType"];
				$MemberID = $Row_G["MemberID"];

				$ClassOrderStartDate = $Row_G["ClassOrderStartDate"];
				$ClassOrderEndDate = $Row_G["ClassOrderEndDate"];
				
				$StartDateTimeStamp = $Row_G["StartDateTimeStamp"];
				$StartYear = $Row_G["StartYear"];
				$StartMonth = $Row_G["StartMonth"];
				$StartDay = $Row_G["StartDay"];
				$StartHour = $Row_G["StartHour"];
				$StartMinute = $Row_G["StartMinute"];

				$EndDateTimeStamp = $Row_G["EndDateTimeStamp"];
				$EndYear = $Row_G["EndYear"];
				$EndMonth = $Row_G["EndMonth"];
				$EndDay = $Row_G["EndDay"];
				$EndHour = $Row_G["EndHour"];
				$EndMinute = $Row_G["EndMinute"];

				$CommonUseClassIn = $Row_G["CommonUseClassIn"];
				$CommonShClassCode = $Row_G["CommonShClassCode"];
				$CommonCiCourseID = $Row_G["CommonCiCourseID"];
				$CommonCiClassID = $Row_G["CommonCiClassID"];
				$CommonCiTelephoneTeacher = $Row_G["CommonCiTelephoneTeacher"];
				$CommonCiTelephoneStudent = $Row_G["CommonCiTelephoneStudent"];

				$ClassAttendState = $Row_G["ClassAttendState"];
				$ClassAttendStateMemberID = $Row_G["ClassAttendStateMemberID"];
				$ClassAttendStateMsg = $Row_G["ClassAttendStateMsg"];
				$ClassState = $Row_G["ClassState"];

				$BookVideoID = $Row_G["BookVideoID"];
				$BookQuizID = $Row_G["BookQuizID"];
				$BookScanID = $Row_G["BookScanID"];

				$ClassRegDateTime = $Row_G["ClassRegDateTime"];
				$ClassModiDateTime = $Row_G["ClassModiDateTime"];
				
				$MemberName = $Row_G["MemberName"];
				$MemberPayType = $Row_G["MemberPayType"];
				$MemberChangeTeacher = $Row_G["MemberChangeTeacher"];
				$MemberNickName = $Row_G["MemberNickName"];
				$MemberLoginID = $Row_G["MemberLoginID"];
				$MemberLevelID = $Row_G["MemberLevelID"];
				$MemberCiTelephone = $Row_G["MemberCiTelephone"];
				$DecMemberPhone1 = $Row_G["DecMemberPhone1"];


				$CenterPayType = $Row_G["CenterPayType"];
				$CenterStudyEndDate = $Row_G["CenterStudyEndDate"];

				$TeacherName = $Row_G["TeacherName"];
				$TeacherLoginID = $Row_G["TeacherLoginID"];
				$TeacherCiTelephone = $Row_G["TeacherCiTelephone"];
				
				$CenterID = $Row_G["JoinCenterID"];
				$CenterName = $Row_G["JoinCenterName"];
				$BranchID = $Row_G["JoinBranchID"];
				$BranchName = $Row_G["JoinBranchName"];
				$BranchGroupID = $Row_G["JoinBranchGroupID"];
				$BranchGroupName = $Row_G["JoinBranchGroupName"];
				$CompanyID = $Row_G["JoinCompanyID"];
				$CompanyName = $Row_G["JoinCompanyName"];
				$FranchiseName = $Row_G["FranchiseName"];

				$CenterLoginID = $Row_G["CenterLoginID"];

				$LastStudyClassCount = $Row_G["LastStudyClassCount"];


				if ($ClassOrderTimeTypeID==2){
					$StrClassOrderTimeTypeName = "20 min";
					$StudyEndTimeHourPlus = 20;
				}else if ($ClassOrderTimeTypeID==3){
					$StrClassOrderTimeTypeName = "30 min";
					$StudyEndTimeHourPlus = 30;
				}else if ($ClassOrderTimeTypeID==4){
					$StrClassOrderTimeTypeName = "40 min";
					$StudyEndTimeHourPlus = 40;
				}

				$StudyEndTimeHour = $StudyTimeHour;
				$StudyEndTimeMinute = $StudyTimeMinute + $StudyEndTimeHourPlus;
				if ($StudyEndTimeMinute>=60){
					$StudyEndTimeHour = $StudyEndTimeHour + 1;
					$StudyEndTimeMinute = $StudyEndTimeMinute - 60;
				}
				$StrClassEndTime = substr("0".$StudyEndTimeHour,-2).":".substr("0".$StudyEndTimeMinute,-2);

				$CalDateDiff = (int)str_replace("-","",$SelectDate) - (int)date("Ymd");
				if ($ClassAttendState==-1){
					$StrClassAttendState = $예정[$LangID];

					if ($CalDateDiff < 0){
						$StrClassAttendState = $미설정[$LangID];
					}

				}else if ($ClassAttendState==0){
					$StrClassAttendState = $미설정[$LangID];
				}else if ($ClassAttendState==1){
					$StrClassAttendState = $출석[$LangID];
				}else if ($ClassAttendState==2){
					$StrClassAttendState = $지각[$LangID];
				}else if ($ClassAttendState==3){
					$StrClassAttendState = $결석[$LangID];
				}else if ($ClassAttendState==4){
					$StrClassAttendState = $연기된수업[$LangID]."(S)";//학생연기
				}else if ($ClassAttendState==5){
					$StrClassAttendState = $연기된수업[$LangID]."(T)";//강사연기
				}else if ($ClassAttendState==6){
					$StrClassAttendState = $취소된수업[$LangID]."(S)";//학생취소
				}else if ($ClassAttendState==7){
					$StrClassAttendState = $취소된수업[$LangID]."(T)";//강사취소
				}else if ($ClassAttendState==8){
					$StrClassAttendState = $교사변경수업[$LangID];//교사변경수업
				}else{

				}


				if ($ClassProductID==1){
					$StrClassProductID = "수업";
				}else if ($ClassProductID==2){
					$StrClassProductID = "레벨";
				}else if ($ClassProductID==3){
					$StrClassProductID = "체험";
				}
			?>
			<tr>
				<td>
					<?if ($OldListCount==$ListCount){?>
						-
					<?}else{?>
						<?=$ListNumber?>
					<?}?>
				</td>
				<td><?=$ClassMemberTypeName?></td>
				<td><?=$StrClassProductID?></td>
				<td><?=$SelectYear?>-<?=substr("0".$SelectMonth,-2)?>-<?=substr("0".$SelectDay,-2)?></td>
				<td><?=ConvAmPm($ClassStartTime)?></td>
				<td><?=ConvAmPm($StrClassEndTime)?></td>
				<td><?=$StrClassOrderTimeTypeName?></td>
				<td><?=$CenterName?></td>
				<td><?=$MemberName?></td>
				<td><?=$MemberLoginID?></td>
				<td><?=$TeacherName?></td>
				<td><?=$TeacherInDateTime?></td>
				<td><?=$StrClassAttendState?></td>
				<td>
					<?if($BookSystemType==0 && $BookScanID!="" && $BookScanID!=0) {?>
					O
					<?}else if($BookSystemType==1 && $BookWebookUnitID!="" && $BookWebookUnitID!=0) {?>
					O
					<?}else{?>

					<?}?>
				</td>
				<td>
					<!-- Video A -->
					<?if($BookVideoID==0) { ?>
						
					<? } else { ?>
						O
					<? } ?>
				</td>
				<td>
					<?if($BookQuizID==0) { ?>
						
					<? } else { ?>
						O
					<? } ?>
				</td>
			</tr>
			<?
				$OldListCount = $ListCount;
			
			}
			$Stmt_G = null;


		}
		$ListCount ++;
		
	}
	$Stmt = null;
	?>

	</tbody>
</table>

<?php
include_once('../includes/dbclose.php');
?>
</body>
</html>
