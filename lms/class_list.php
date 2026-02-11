<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
?>

<?
include_once('./inc_common_list_css.php');
?>
<!-- ============== only this page css ============== -->
<style>
.TrListBgColor{font-weight:bold;color:#AA0000;}
</style>
<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
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


include_once('./inc_top.php');
include_once('./inc_menu_left.php');
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

$SearchClassAttendState = isset($_REQUEST["SearchClassAttendState"]) ? $_REQUEST["SearchClassAttendState"] : "";


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

if ($SearchClassAttendState!="") {
	if($SearchClassAttendState!="-1") {
		$AddSqlWhere = $AddSqlWhere . " and CLS.ClassAttendState=".$SearchClassAttendState." ";
	} else {
		$AddSqlWhere = $AddSqlWhere . " and CLS.ClassAttendState is null ";
	}
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
		COS.ClassOrderSlotID,
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
		ifnull(COS.ClassOrderPayID,0) as ClassOrderPayID, 
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
		CLS.CommonShNewClassCode,

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
//echo $ViewTable;

			//결제 안해도 관리자가 등록한 수업 진행가능하도록 주석 처리
			//inner join ClassOrderPayDetails COPD on COS.ClassOrderID=COPD.ClassOrderID 
			//inner join ClassOrderPays COP on COPD.ClassOrderPayID=COP.ClassOrderPayID and (COP.ClassOrderPayProgress=21 or COP.ClassOrderPayProgress=31 or COP.ClassOrderPayProgress=41)


//오늘 수업 또는 선택하여 연기하기 일때 수업을 등록해 준다.
if (date("Y-m-d")==$SelectYear."-".substr("0".$SelectMonth,-2)."-".substr("0".$SelectDay,-2)  ||  $ListSelectResetDate=="1"){

	$SqlWhereCenterRenew = "";
	if ($NoIgnoreCenterRenew==1){
		$SqlWhereCenterRenew = " and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ";
	}

	$Sql3 = "
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
			order by V.ClassMemberType asc, V.StudyTimeHour asc, V.StudyTimeMinute asc, V.TeacherID asc, V.ClassMemberTypeGroupID desc
	";


	$Stmt3 = $DbConn->prepare($Sql3);
	$Stmt3->execute();
	$Stmt3->setFetchMode(PDO::FETCH_ASSOC);

	$OldGroupStudyTimeHour = 0;
	$OldGroupStudyTimeMinute = 0;
	$OldGroupTeacherID = 0;
	$OldClassMemberTypeGroupID = 0;
	while($Row3 = $Stmt3->fetch()) {
		$ClassMemberType = $Row3["ClassMemberType"];
		$ClassMemberTypeGroupID = $Row3["ClassMemberTypeGroupID"];
		$ClassState = $Row3["ClassState"];
		$ClassOrderID = $Row3["ClassOrderID"];
		$StartHour = $Row3["StudyTimeHour"];
		$StartMinute = $Row3["StudyTimeMinute"];
		$ClassOrderTimeTypeID = $Row3["ClassOrderTimeTypeID"];
		$TeacherID = $Row3["TeacherID"];
		$MemberID = $Row3["MemberID"];
		$ClassOrderPayID = $Row3["ClassOrderPayID"];
		

		//================ 그룹 아이디 생성 ===================
		if ($ClassMemberType!=1) {

			if ($OldGroupStudyTimeHour!=$StartHour || $OldGroupStudyTimeMinute!=$StartMinute || $OldGroupTeacherID!=$TeacherID){
				$OldClassMemberTypeGroupID = 0;
			}


			if ($ClassMemberTypeGroupID==0){//아직 그룹 아이디가 등록 안되어 있으면..
				
				if ($OldClassMemberTypeGroupID==0){//첫번째 리스트 - 그룹아이디 새로생성

					$Sql_OG = "select max(ClassMemberTypeGroupID) as NewClassMemberTypeGroupID from ClassOrders";
					$Stmt_OG = $DbConn->prepare($Sql_OG);
					$Stmt_OG->execute();
					$Stmt_OG->setFetchMode(PDO::FETCH_ASSOC);
					$Row_OG = $Stmt_OG->fetch();
					$Stmt_OG = null;
					
					$NewClassMemberTypeGroupID = $Row_OG["NewClassMemberTypeGroupID"] + 1;

				}else{//기존것 넣기

					$NewClassMemberTypeGroupID = $OldClassMemberTypeGroupID;
				
				}

				$Sql_OG = "update ClassOrders set ClassMemberTypeGroupID=:ClassMemberTypeGroupID where ClassOrderID=:ClassOrderID";
				$Stmt_OG = $DbConn->prepare($Sql_OG);
				$Stmt_OG->bindParam(':ClassMemberTypeGroupID', $NewClassMemberTypeGroupID);
				$Stmt_OG->bindParam(':ClassOrderID', $ClassOrderID);
				$Stmt_OG->execute();
				$Stmt_OG = null;

				$ClassMemberTypeGroupID = $NewClassMemberTypeGroupID;
			}

			
			$OldClassMemberTypeGroupID = $ClassMemberTypeGroupID;
			$OldGroupStudyTimeHour = $StartHour;
			$OldGroupStudyTimeMinute = $StartMinute;
			$OldGroupTeacherID = $TeacherID;
		}
		//================ 그룹 아이디 생성 ===================

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

				if($ClassOrderPayID==0) {
					$Sql2 = "
						select 
							A.ClassOrderPayID 
						from ClassOrderPays A 
							inner join ClassOrderPayDetails B on A.ClassOrderPayID=B.ClassOrderPayID 
						where 
							B.ClassOrderID=:ClassOrderID 
							and 
							B.TeacherID=:TeacherID 
							and
							datediff(A.ClassOrderPayStartDate, now() ) < 0
						order by 
							A.ClassOrderPayID desc;
						limit
							0,1
					";

					$Stmt2 = $DbConn->prepare($Sql2);
					$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
					$Stmt2->bindParam(':TeacherID', $TeacherID);
					$Stmt2->execute();
					$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
					$Row2 = $Stmt2->fetch();
					$ClassOrderPayID = $Row2["ClassOrderPayID"];
					if($ClassOrderPayID==null) {
						$ClassOrderPayID = 0;
					}
					$Stmt2 = null;
				}

				$Sql2 = " insert into Classes ( ";
					$Sql2 .= " ClassOrderPayID, ";
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
					$Sql2 .= " :ClassOrderPayID, ";
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
				$Stmt2->bindParam(':ClassOrderPayID', $ClassOrderPayID);
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
				
				//echo $ClassID."/";
			}



		}

	}
	$Stmt3 = null;
						

}
//오늘 수업 또는 선택하여 연기하기 일때 수업을 등록해 준다.



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

//echo $Sql;
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

$SqlLimit = "";
if ($PageListNum!="0"){
	$SqlLimit = " limit $StartRowNum, $PageListNum ";
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
		".$SqlLimit." 
";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

?>

<div id="page_content">
    <div id="page_content_inner">
        <h3 class="heading_b uk-margin-bottom"><?=$수업관리[$LangID]?></h3>
        <form name="SearchForm" method="get">
        <input type="hidden" name="type" value="<?=$SearchState?>">
		<input type="hidden" name="SelectYear" value="<?=$SelectYear?>">
		<input type="hidden" name="SelectMonth" value="<?=$SelectMonth?>">
		<input type="hidden" name="SelectDay" value="<?=$SelectDay?>">

		<input type="hidden" name="ListSelectResetDate" value="">
		<input type="hidden" name="ListSelectResetDatePage" value="">
		
        <div class="md-card" style="margin-bottom:10px;">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin="">

                    <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchFranchiseID==1){?>none<?}?>;">
                        <select id="SearchFranchiseID" name="SearchFranchiseID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="프랜차이즈선택" style="width:100%;"/>
                            <option value=""></option>
                            <?
                            $Sql2 = "select 
                                            A.* 
                                    from Franchises A 
                                    where A.FranchiseState<>0 
                                    order by A.FranchiseState asc, A.FranchiseName asc";
                            $Stmt2 = $DbConn->prepare($Sql2);
                            $Stmt2->execute();
                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                            
                            $OldSelectFranchiseState = -1;
                            while($Row2 = $Stmt2->fetch()) {
                                $SelectFranchiseID = $Row2["FranchiseID"];
                                $SelectFranchiseName = $Row2["FranchiseName"];
                                $SelectFranchiseState = $Row2["FranchiseState"];
                            
                                if ($OldSelectFranchiseState!=$SelectFranchiseState){
                                    if ($OldSelectFranchiseState!=-1){
                                        echo "</optgroup>";
                                    }
                                    
                                    if ($SelectFranchiseState==1){
                                        echo "<optgroup label=\"프랜차이즈(운영중)\">";
                                    }else if ($SelectFranchiseState==2){
                                        echo "<optgroup label=\"프랜차이즈(미운영)\">";
                                    }
                                } 
                                $OldSelectFranchiseState = $SelectFranchiseState;
                            ?>

                            <option value="<?=$SelectFranchiseID?>" <?if ($SearchFranchiseID==$SelectFranchiseID){?>selected<?}?>><?=$SelectFranchiseName?></option>
                            <?
                            }
                            $Stmt2 = null;
                            ?>
                        </select>
                    </div>

                    <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchCompanyID==1){?>none<?}?>;">
                        <select id="SearchCompanyID" name="SearchCompanyID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$본사관리[$LangID]?>" style="width:100%;"/>
                            <option value=""></option>
                            <?
                            $AddWhere2 = "";
                            if ($SearchFranchiseID!=""){
                                $AddWhere2 = "and A.FranchiseID=".$SearchFranchiseID." ";
                            }else{
                                $AddWhere2 = " ";
                            }
                            $Sql2 = "select 
                                            A.* 
                                    from Companies A 
                                        inner join Franchises B on A.FranchiseID=B.FranchiseID 
                                    where A.CompanyState<>0 and B.FranchiseState<>0 ".$AddWhere2." 
                                    order by A.CompanyState asc, A.CompanyName asc";
                            $Stmt2 = $DbConn->prepare($Sql2);
                            $Stmt2->execute();
                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                            
                            $OldSelectCompanyState = -1;
                            while($Row2 = $Stmt2->fetch()) {
                                $SelectCompanyID = $Row2["CompanyID"];
                                $SelectCompanyName = $Row2["CompanyName"];
                                $SelectCompanyState = $Row2["CompanyState"];
                            
                                if ($OldSelectCompanyState!=$SelectCompanyState){
                                    if ($OldSelectCompanyState!=-1){
                                        echo "</optgroup>";
                                    }
                                    
                                    if ($SelectCompanyState==1){
                                        echo "<optgroup label=\"본사(운영중)\">";
                                    }else if ($SelectCompanyState==2){
                                        echo "<optgroup label=\"본사(미운영)\">";
                                    }
                                }
                                $OldSelectCompanyState = $SelectCompanyState;
                            ?>

                            <option value="<?=$SelectCompanyID?>" <?if ($SearchCompanyID==$SelectCompanyID){?>selected<?}?>><?=$SelectCompanyName?></option>
                            <?
                            }
                            $Stmt2 = null;
                            ?>
                        </select>
                    </div>


                    <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
                        <select id="SearchBranchGroupID" name="SearchBranchGroupID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$대표지사선택[$LangID]?>" style="width:100%;"/>
                            <option value=""></option>
                            <?
                            $AddWhere2 = "";
                            if ($SearchCompanyID!=""){
                                $AddWhere2 = "and A.CompanyID=".$SearchCompanyID." ";
                            }else{
                                if ($SearchFranchiseID!=""){
                                    $AddWhere2 = "and B.FranchiseID=".$SearchFranchiseID." ";
                                }else{
                                    $AddWhere2 = " ";
                                }
                            }
                            $Sql2 = "select 
                                            A.* 
                                        from BranchGroups A 
                                            inner join Companies B on A.CompanyID=B.CompanyID 
                                            inner join Franchises C on B.FranchiseID=C.FranchiseID 
                                        where A.BranchGroupState<>0 and B.CompanyState<>0 and C.FranchiseState<>0 ".$AddWhere2." 
                                        order by A.BranchGroupState asc, A.BranchGroupName asc";
                            
                            $Stmt2 = $DbConn->prepare($Sql2);
                            $Stmt2->execute();
                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                            
                            $OldSelectBranchGroupState = -1;
                            while($Row2 = $Stmt2->fetch()) {
                                $SelectBranchGroupID = $Row2["BranchGroupID"];
                                $SelectBranchGroupName = $Row2["BranchGroupName"];
                                $SelectBranchGroupState = $Row2["BranchGroupState"];
                            
                                if ($OldSelectBranchGroupState!=$SelectBranchGroupState){
                                    if ($OldSelectBranchGroupState!=-1){
                                        echo "</optgroup>";
                                    }
                                    
                                    if ($SelectBranchGroupState==1){
                                        echo "<optgroup label=\"대표지사(운영중)\">";
                                    }else if ($SelectBranchGroupState==2){
                                        echo "<optgroup label=\"대표지사(미운영)\">";
                                    }
                                }
                                $OldSelectBranchGroupState = $SelectBranchGroupState;
                            ?>

                            <option value="<?=$SelectBranchGroupID?>" <?if ($SearchBranchGroupID==$SelectBranchGroupID){?>selected<?}?>><?=$SelectBranchGroupName?></option>
                            <?
                            }
                            $Stmt2 = null;
                            ?>
                        </select>
                    </div>
                    <div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchID==1){?>none<?}?>;">
                        <select id="SearchBranchID" name="SearchBranchID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$지사선택[$LangID]?>" style="width:100%;"/>
                            <option value=""></option>
                            <?
                            $AddWhere2 = "";
                            if ($SearchBranchGroupID!=""){
                                $AddWhere2 = "and A.BranchGroupID=".$SearchBranchGroupID." ";
                            }else{
                                if ($SearchCompanyID!=""){
                                    $AddWhere2 = "and B.CompanyID=".$SearchCompanyID." ";
                                }else{
                                    if ($SearchFranchiseID!=""){
                                        $AddWhere2 = "and C.FranchiseID=".$SearchFranchiseID." ";
                                    }else{
                                        $AddWhere2 = " ";
                                    }
                                }
                            }
    
                            $Sql2 = "select 
                                            A.* 
                                    from Branches A 
                                        inner join BranchGroups B on A.BranchGroupID=B.BranchGroupID 
                                        inner join Companies C on B.CompanyID=C.CompanyID 
                                        inner join Franchises D on C.FranchiseID=D.FranchiseID 
                                    where A.BranchState<>0 and B.BranchGroupState<>0 and C.CompanyState<>0 and D.FranchiseState<>0 ".$AddWhere2." 
                                    order by A.BranchState asc, A.BranchName asc";

                            $Stmt2 = $DbConn->prepare($Sql2);
                            $Stmt2->execute();
                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                            
                            $OldSelectBranchState = -1;
                            while($Row2 = $Stmt2->fetch()) {
                                $SelectBranchID = $Row2["BranchID"];
                                $SelectBranchName = $Row2["BranchName"];
                                $SelectBranchState = $Row2["BranchState"];
                            
                                if ($OldSelectBranchState!=$SelectBranchState){
                                    if ($OldSelectBranchState!=-1){
                                        echo "</optgroup>";
                                    }
                                    
                                    if ($SelectBranchState==1){
                                        echo "<optgroup label=\"지사(운영중)\">";
                                    }else if ($SelectBranchState==2){
                                        echo "<optgroup label=\"지사(미운영)\">";
                                    }
                                }
                                $OldSelectBranchState = $SelectBranchState;
                            ?>

                            <option value="<?=$SelectBranchID?>" <?if ($SearchBranchID==$SelectBranchID){?>selected<?}?>><?=$SelectBranchName?></option>
                            <?
                            }
                            $Stmt2 = null;
                            ?>
                        </select>
                    </div>
                    <div class="uk-width-medium-1-10" style="padding-top:7px;display:<?if ($HideSearchCenterID==1){?>none<?}?>;">
                        <select id="SearchCenterID" name="SearchCenterID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$대리점선택[$LangID]?>" style="width:100%;"/>
                            <option value=""></option>
                            <?

                            $AddWhere2 = "";
                            if ($SearchBranchID!=""){
                                $AddWhere2 = "and A.BranchID=".$SearchBranchID." ";
                            }else{
                                if ($SearchBranchGroupID!=""){
                                    $AddWhere2 = "and B.BranchGroupID=".$SearchBranchGroupID." ";
                                }else{
                                    if ($SearchCompanyID!=""){
                                        $AddWhere2 = "and C.CompanyID=".$SearchCompanyID." ";
                                    }else{
                                        if ($SearchFranchiseID!=""){
                                            $AddWhere2 = "and D.FranchiseID=".$SearchFranchiseID." ";
                                        }else{
                                            $AddWhere2 = " ";
                                        }
                                    }
                                }
                            }

                            $Sql2 = "select 
                                            A.* 
                                    from Centers A 
                                        inner join Branches B on A.BranchID=B.BranchID 
                                        inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
                                        inner join Companies D on C.CompanyID=D.CompanyID 
                                        inner join Franchises E on D.FranchiseID=E.FranchiseID 
                                    where A.CenterState<>0 and B.BranchState<>0 and C.BranchGroupState<>0 and D.CompanyState<>0 and E.FranchiseState<>0 ".$AddWhere2." 
                                    order by A.CenterState asc, A.CenterName asc";    
                            $Stmt2 = $DbConn->prepare($Sql2);
                            $Stmt2->execute();
                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                            
                            $OldSelectCenterState = -1;
                            while($Row2 = $Stmt2->fetch()) {
                                $SelectCenterID = $Row2["CenterID"];
                                $SelectCenterName = $Row2["CenterName"];
                                $SelectCenterState = $Row2["CenterState"];
                            
                                if ($OldSelectCenterState!=$SelectCenterState){
                                    if ($OldSelectCenterState!=-1){
                                        echo "</optgroup>";
                                    }
                                    
                                    if ($SelectCenterState==1){
                                        echo "<optgroup label=\"대리점(운영중)\">";
                                    }else if ($SelectCenterState==2){
                                        echo "<optgroup label=\"대리점(미운영)\">";
                                    }
                                }
                                $OldSelectCenterState = $SelectCenterState;
                            ?>

                            <option value="<?=$SelectCenterID?>" <?if ($SearchCenterID==$SelectCenterID){?>selected<?}?>><?=$SelectCenterName?></option>
                            <?
                            }
                            $Stmt2 = null;
                            ?>
                        </select>
                    </div>


                    <div class="uk-width-medium-1-10" style="padding-top:7px;display:<?if ($HideSearchTeacherID==1){?>none<?}?>;">
                        <select id="SearchTeacherID" name="SearchTeacherID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="강사선택" style="width:100%;"/>
                            <option value=""></option>
                            <?
                            $Sql2 = "select 
                                            A.* 
                                    from Teachers A 
                                    where A.TeacherState=1 
                                    order by A.TeacherName asc";    
                            $Stmt2 = $DbConn->prepare($Sql2);
                            $Stmt2->execute();
                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                            
                            $OldSelectCenterState = -1;
                            while($Row2 = $Stmt2->fetch()) {
                                $SelectTeacherID = $Row2["TeacherID"];
                                $SelectTeacherName = $Row2["TeacherName"];
                            ?>

                            <option value="<?=$SelectTeacherID?>" <?if ($SearchTeacherID==$SelectTeacherID){?>selected<?}?>><?=$SelectTeacherName?></option>
                            <?
                            }
                            $Stmt2 = null;
                            ?>
                        </select>
                    </div>

                    <!--
                    <div class="uk-width-medium-1-10">
                        <label for="product_search_price">Price</label>
                        <input type="text" class="md-input" id="product_search_price">
                    </div>
                    -->

                    <div class="uk-width-medium-1-10" style="display:none;">
                        <div class="uk-margin-small-top">
                            <select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
                                <option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
                                <option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$정상[$LangID]?></option>
                                <option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$비정성[$LangID]?></option>
                            </select>
                        </div>
                    </div>


					<div class="uk-width-medium-1-10" style="display:;">
                        <div class="uk-margin-small-top">
                            <select id="SearchStartHour" name="SearchStartHour" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
                                <option value=""><?=$시작시간[$LangID]?></option>
								<?for ($ii=0;$ii<=23;$ii++){?>
								<option value="<?=$ii?>" <?if ($SearchStartHour==$ii){?>selected<?}?>><?=substr("0".$ii,-2)?><?=$시[$LangID]?></option>
								<?}?>	
                            </select>
                        </div>
                    </div>
					<div class="uk-width-medium-1-10" style="display:;">
                        <div class="uk-margin-small-top">
                            <select id="SearchStartMinute" name="SearchStartMinute" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
                                <option value=""><?=$시작분[$LangID]?></option>
								<?for ($ii=0;$ii<=50;$ii=$ii+10){?>
								<option value="<?=$ii?>" <?if ($SearchStartMinute==$ii){?>selected<?}?>><?=substr("0".$ii,-2)?><?=$분[$LangID]?></option>
								<?}?>	
                            </select>
                        </div>
                    </div>
					<div class="uk-width-medium-1-10" style="display:;">
                        <div class="uk-margin-small-top">
                            <select id="SearchEndHour" name="SearchEndHour" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
                                <option value=""><?=$종료시간[$LangID]?></option>
								<?for ($ii=0;$ii<=23;$ii++){?>
								<option value="<?=$ii?>" <?if ($SearchEndHour==$ii){?>selected<?}?>><?=substr("0".$ii,-2)?><?=$시[$LangID]?></option>
								<?}?>	
                            </select>
                        </div>
                    </div>
					<div class="uk-width-medium-1-10" style="display:;">
                        <div class="uk-margin-small-top">
                            <select id="SearchEndMinute" name="SearchEndMinute" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
                                <option value=""><?=$종료분[$LangID]?></option>
								<?for ($ii=0;$ii<=50;$ii=$ii+10){?>
								<option value="<?=$ii?>" <?if ($SearchEndMinute==$ii){?>selected<?}?>><?=substr("0".$ii,-2)?><?=$분[$LangID]?></option>
								<?}?>	
                            </select>
                        </div>
                    </div>

					<div class="uk-width-medium-1-10" style="display:;">
                        <div class="uk-margin-small-top">
                            <select id="PageListNum" name="PageListNum" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
                                
                                <option value="10" <?if ($PageListNum=="10"){?>selected<?}?>>10<?=$몇개씩보기[$LangID]?></option>
                                <!--<option value="20" <?if ($PageListNum=="20"){?>selected<?}?>>20<?=$몇개씩보기[$LangID]?></option>-->
								<option value="30" <?if ($PageListNum=="30"){?>selected<?}?>>30<?=$몇개씩보기[$LangID]?></option>
								<!--<option value="40" <?if ($PageListNum=="40"){?>selected<?}?>>40<?=$몇개씩보기[$LangID]?></option>-->
								<option value="50" <?if ($PageListNum=="50"){?>selected<?}?>>50<?=$몇개씩보기[$LangID]?></option>
								<option value="100" <?if ($PageListNum=="100"){?>selected<?}?>>100<?=$몇개씩보기[$LangID]?></option>
								<option value="0" <?if ($PageListNum=="0"){?>selected<?}?>><?=$전체[$LangID]?></option>
                            </select>
                        </div>
                    </div>

                    <div class="uk-width-medium-2-10">
                        <label for="SearchText"><?=$학생명_또는_아이디[$LangID]?></label>
                        <input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
                    </div>


                    <div class="uk-width-medium-1-10">
                        <div class="uk-margin-top uk-text-nowrap">
                            <input type="checkbox" name="SearchNoSetQnV" id="SearchNoSetQnV" onclick="javascript:SearchSubmit();" value="1" <?if ($SearchNoSetQnV=="1") {?>checked<?}?>/>
                            <label for="SearchNoSetQnV" class="inline-label">교/퀴/비 미설정</label>
                        </div>
                    </div>

                    <div class="uk-width-medium-1-10" style="padding-top:7px;">
                        <select id="SearchClassAttendState" name="SearchClassAttendState" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="수업상태" style="width:100%;"/>
                            <option value=""></option>
                            <option value="0" <?if ($SearchClassAttendState=="0"){?>selected<?}?>><?=$미등록[$LangID]?></option>
							<option value="1" <?if ($SearchClassAttendState=="1"){?>selected<?}?>><?=$출석[$LangID]?></option>
							<option value="2" <?if ($SearchClassAttendState=="2"){?>selected<?}?>><?=$지각[$LangID]?></option>
							<option value="3" <?if ($SearchClassAttendState=="3"){?>selected<?}?>><?=$결석[$LangID]?></option>
							<option value="-1" <?if ($SearchClassAttendState=="-1"){?>selected<?}?>><?=$예정[$LangID]?></option>
                        </select>
                    </div>

					<div class="uk-width-medium-1-10">
					</div>

					<div class="uk-width-medium-8-10">
					</div>

					<div class="uk-width-medium-1-10 uk-text-center">
                        <a href="javascript:DownExcelSetup();" class="md-btn md-btn-primary uk-margin-small-top"><?=$설정현황[$LangID]?></a>
                    </div>

                    <div class="uk-width-medium-1-10 uk-text-center">
						
                        <a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
                    </div>
                    
                </div>
            </div>
        </div>
        </form>


        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div class="uk-overflow-container">
                            
							
							
							<?
							$HolidayMsg = "";
							$Sql3 = "
								select 
									A.EduCenterHolidayName  
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
							$EduCenterHolidayName = $Row3["EduCenterHolidayName"];
							
							$TodayIsEduCenterHoliday = 0;
							if ($EduCenterHolidayName){
								
								$HolidayMsg = "※ 오늘은 (".$EduCenterHolidayName.") 휴무일 입니다. 휴무일 수업은 당일 새벽 자동 연기됩니다.";
								$TodayIsEduCenterHoliday = 1;
							
							}else{
								
								$Sql4 = "
									select 
										 B.TeacherName 
									from TeacherHolidays A 
										inner join Teachers B on A.TeacherID=B.TeacherID 
										inner join TeacherGroups C on B.TeacherGroupID=C.TeacherGroupID 
									where A.TeacherHolidayDate=:TeacherHolidayDate
										and C.EduCenterID=:EduCenterID
										and A.TeacherHolidayState=1 
								";
								$Stmt4 = $DbConn->prepare($Sql4);
								$Stmt4->bindParam(':TeacherHolidayDate', $SelectDate);
								$Stmt4->bindParam(':EduCenterID', $EduCenterID);
								$Stmt4->execute();
								$Stmt4->setFetchMode(PDO::FETCH_ASSOC);
								$TeacherListNum = 0;
								while($Row4 = $Stmt4->fetch()) {
									$TeacherName = $Row4["TeacherName"];

									if ($TeacherListNum==0){
										$HolidayMsg = $HolidayMsg . "오늘 휴무 강사 (연기/변경대상) : ";
									}else{
										$HolidayMsg = $HolidayMsg . ", ";
									}
									$HolidayMsg = $HolidayMsg . $TeacherName;

									$TeacherListNum++;
								}
							}
							
							?>
							
							<?if ($HolidayMsg!=""){?>
							<div style="margin-bottom:20px;color:#cc0000;text-align:center;">
								<?=$HolidayMsg?>
							</div>
							<?}?>
							
							
							
							<script>
							function MoveMonth(SetYear, SetMonth, SetDay){
								document.SearchForm.SelectYear.value = SetYear;
								document.SearchForm.SelectMonth.value = SetMonth;
								document.SearchForm.SelectDay.value = SetDay;
								document.SearchForm.submit();
							}

							function MoveSelectDate(SelectType){
								if (SelectType==1){
									document.SearchForm.SelectYear.value = document.getElementById("SelectYear2").value;
									document.SearchForm.SelectMonth.value = 1;
									document.SearchForm.SelectDay.value = 1;
								}else if (SelectType==2){
									document.SearchForm.SelectYear.value = document.getElementById("SelectYear2").value;
									document.SearchForm.SelectMonth.value = document.getElementById("SelectMonth2").value;
									document.SearchForm.SelectDay.value = 1;
								}else if (SelectType==3){
									document.SearchForm.SelectYear.value = document.getElementById("SelectYear2").value;
									document.SearchForm.SelectMonth.value = document.getElementById("SelectMonth2").value;
									document.SearchForm.SelectDay.value = document.getElementById("SelectDay2").value;
								}

								document.SearchForm.submit();
							}
							</script>
							<div style="text-align:center;">
								<h3 class="schedule_caption">
									<a href="javascript:MoveMonth('<?=$PrevYear?>','<?=$PrevMonth?>','<?=$PrevDay?>')"><img src="../images/arrow_btn_left.png" class="schedule_left"></a>
									&nbsp; &nbsp; &nbsp; 
									<select name="SelectYear2"  id="SelectYear2" style="width:80px;height:25px;" onchange="MoveSelectDate(1)">
										<?for ($ii=$SelectYear-1;$ii<=$SelectYear+1;$ii++){?>
											<option value="<?=$ii?>" <?if ($ii==$SelectYear){?>selected<?}?>><?=$ii?></option>
										<?}?>
									</select>
									<select name="SelectMonth2"  id="SelectMonth2" style="width:50px;height:25px;" onchange="MoveSelectDate(2)">
										<?for ($ii=1;$ii<=12;$ii++){?>
											<option value="<?=$ii?>" <?if ($ii==$SelectMonth){?>selected<?}?>><?=substr("0".$ii,-2)?></option>
										<?}?>
									</select>
									<select name="SelectDay2"  id="SelectDay2" style="width:120px;height:25px;" onchange="MoveSelectDate(3)">
										<?
										$SelectEndDay = date("t", strtotime($SelectYear."-".$SelectMonth."-01"));
										for ($ii=1;$ii<=$SelectEndDay;$ii++){
												$SelectDateWeek2 = date('w', strtotime($SelectYear."-".substr("0".$SelectMonth,-2)."-".substr("0".$ii,-2)));
												$WeekDayStr2 = $ArrWeekDayStr[$SelectDateWeek2];
										?>
											<option value="<?=$ii?>" <?if ($ii==$SelectDay){?>selected<?}?>><?=substr("0".$ii,-2)?> (<?=$WeekDayStr2?>)</option>
										<?
										}
										?>
									</select>
									&nbsp; &nbsp; &nbsp; 
									
									<?=$WeekDayStr?>
									
									&nbsp; &nbsp; &nbsp; 
									<a href="javascript:MoveMonth('<?=$NextYear?>','<?=$NextMonth?>','<?=$NextDay?>')"><img src="../images/arrow_btn_right.png" class="schedule_right"></a> 


								</h3>
							</div>

							<?
							if ($_LINK_ADMIN_LEVEL_ID_<=4) {
								$Sql3 = "
									select 
										count(*) as ClassStudyWebCount
									from AssmtStudentSelfScores A 
										inner join Classes B on A.ClassID=B.ClassID 
									where StartYear=$SelectYear and StartMonth=$SelectMonth and StartDay=$SelectDay
										and A.DeviceType=1
								";
								$Stmt3 = $DbConn->prepare($Sql3);
								$Stmt3->execute();
								$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
								$Row3 = $Stmt3->fetch();
								$Stmt3 = null;
								$ClassStudyWebCount = $Row3["ClassStudyWebCount"];

								$Sql3 = "
									select 
										count(*) as ClassStudyAndroidCount
									from AssmtStudentSelfScores A 
										inner join Classes B on A.ClassID=B.ClassID 
									where StartYear=$SelectYear and StartMonth=$SelectMonth and StartDay=$SelectDay
										and A.DeviceType=11
								";
								$Stmt3 = $DbConn->prepare($Sql3);
								$Stmt3->execute();
								$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
								$Row3 = $Stmt3->fetch();
								$Stmt3 = null;
								$ClassStudyAndroidCount = $Row3["ClassStudyAndroidCount"];

								$Sql3 = "
									select 
										count(*) as ClassStudyIosCount
									from AssmtStudentSelfScores A 
										inner join Classes B on A.ClassID=B.ClassID 
									where StartYear=$SelectYear and StartMonth=$SelectMonth and StartDay=$SelectDay
										and A.DeviceType=12
								";
								$Stmt3 = $DbConn->prepare($Sql3);
								$Stmt3->execute();
								$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
								$Row3 = $Stmt3->fetch();
								$Stmt3 = null;
								$ClassStudyIosCount = $Row3["ClassStudyIosCount"];

							?>
							<div style="text-align:right;">
								<h3 class="schedule_caption">
									금일 누적 디바이스 접속 현황 : Web : <?=$ClassStudyWebCount?>, App : <?=$ClassStudyAndroidCount+$ClassStudyIosCount?>
								</h3>
							</div>
							<?
							}
							?>

							<table class="uk-table uk-table-align-vertical">
                                <thead>
                                    <tr>
                                        <th nowrap><input name="CheckBoxAll" type="checkbox" onclick="CheckListAll(this)"></th>
										<th nowrap>No</th>
										<th nowrap><?=$구분[$LangID]?></th>
                                        <th nowrap><?=$수업시작[$LangID]?><br>(<?=$수업진행시간[$LangID]?>)</th>
                                        <?if ($_LINK_ADMIN_LEVEL_ID_<=4) {?>
											<th nowrap><?=$강사입장시각[$LangID]?></th>
										<?}?>
										<!--<th nowrap><?=$수업진행시간[$LangID]?></th>-->
                                        <th nowrap>
											<?=$대리점명[$LangID]?>
											<br>
											
											<?=$학생명[$LangID]?>
										
											<?if ($_LINK_ADMIN_LEVEL_ID_==15) {?>
											<br>
											<?=$학생명[$LangID]?>(Eng)
											<?}?>							
										</th>
										<?if ($_LINK_ADMIN_LEVEL_ID_<15) {?>
										
										<th nowrap><?=$결제타입[$LangID]?></th>
										<!--<th nowrap>수강종료일</th>-->
										<?}?>

										<th nowrap><?=$요약[$LangID]?></th>
										<th nowrap><?=$스케줄[$LangID]?></th>
										<?if ($_LINK_ADMIN_LEVEL_ID_!=15) {?>
										<th nowrap><?=$강사명[$LangID]?></th>
										<?}?>
                                       
                                        <th nowrap><?=$요청[$LangID]?></th>
                                        <th nowrap><?=$상태[$LangID]?></th>
										<th nowrap><?=$이전수업평가[$LangID]?></th>
										<th nowrap><?=$수업평가[$LangID]?></th>
										<th nowrap><?=$출결현황[$LangID]?></th>
										<th nowrap><?=$교재[$LangID]?></th>
										<th nowrap><?=$비디오[$LangID]?></th>
										<th nowrap><?=$퀴즈[$LangID]?></th>
										<th nowrap><?=$수업입장[$LangID]?></th>
										<?if ($_LINK_ADMIN_LEVEL_ID_<=4) {?>
										<th nowrap><?=$대체강사[$LangID]?></th>
										<?}?>
										<th nowrap><?=$수업연기[$LangID]?></th>
										
										<!--
										<th nowrap><?=$전체수업설정[$LangID]?></th>
										-->
										<th nowrap><?=$수업설정[$LangID]?></th>
										<!--
                                        <th nowrap style="display:none;">개발용 모니터링</th>
                                        <th nowrap style="display:none;">학생모드</th>
										-->
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php
                                    $ListCount = 1;
                                    $CheckListCount = 1;
									
									//$StrClassRegScript = "";
									$OpenClassCount = 0;
									$LastClassNotAssmtCount = 0;
									$LastClassNotAssmtClasses = "|";
									while($Row = $Stmt->fetch()) {
                                        $ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;
										
										$ClassOrderSlotID = $Row["ClassOrderSlotID"];
										
										$ClassOrderPayID = $Row["ClassOrderPayID"];
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

										$CommonShNewClassCode = $Row["CommonShNewClassCode"];


										
										$Sql3 = " 
												select 
														B.AssmtStudentDailyScoreID 
												from Classes A inner join AssmtStudentDailyScores B on A.ClassID=B.ClassID 
												where 
													A.ClassOrderID=:ClassOrderID
													and (A.ClassAttendState=0 or A.ClassAttendState=1 or A.ClassAttendState=2) 
													and A.ClassState=2 and datediff(A.EndDateTime, '".$SelectDate."')<0 
												order by A.StartDateTime desc limit 0,1
										";
										$Stmt3 = $DbConn->prepare($Sql3);
										$Stmt3->bindParam(':ClassOrderID', $ClassOrderID);
										$Stmt3->execute();
										$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
										$Row3 = $Stmt3->fetch();
										$Stmt3 = null;
										if ($Row3["AssmtStudentDailyScoreID"]){
											$LastAssmtStudentDailyScoreID = $Row3["AssmtStudentDailyScoreID"];
										}else{
											$LastAssmtStudentDailyScoreID = 0;
										}


										$Sql3 = "
												select 
														A.ClassID 
												from Classes A 
												where 
													A.ClassOrderID=:ClassOrderID 
													and (A.ClassAttendState=0 or A.ClassAttendState=1 or A.ClassAttendState=2) 
													and A.ClassState=2 and datediff(A.EndDateTime, '".$SelectDate."')<0 
												order by A.StartDateTime desc limit 0,1
											";
										$Stmt3 = $DbConn->prepare($Sql3);
										$Stmt3->bindParam(':ClassOrderID', $ClassOrderID);
										$Stmt3->execute();
										$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
										$Row3 = $Stmt3->fetch();
										$Stmt3 = null;
										if ($Row3["ClassID"]){
											$LastClassID = $Row3["ClassID"];
										}else{
											$LastClassID = 0;
										}



										if($BookSystemType==0) {
											$Sql3 = "select BookScanImageFileName from BookScans where BookScanID=:BookScanID";
											$Stmt3 = $DbConn->prepare($Sql3);
											$Stmt3->bindParam(':BookScanID', $BookScanID);
											$Stmt3->execute();
											$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
											$Row3 = $Stmt3->fetch();
											$Stmt3 = null;
											$BookScanImageFileName = $Row3["BookScanImageFileName"];
										}


										$Sql3 = "select A.BookVideoType, A.BookVideoType2, A.BookVideoCode, A.BookVideoCode2 from BookVideos A where A.BookVideoID=:BookVideoID";
										$Stmt3 = $DbConn->prepare($Sql3);
										$Stmt3->bindParam(':BookVideoID', $BookVideoID);
										$Stmt3->execute();
										$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
										$Row3 = $Stmt3->fetch();
										$Stmt3 = null;
										$BookVideoType = $Row3["BookVideoType"];
										$BookVideoType2 = $Row3["BookVideoType2"];
										$BookVideoCode = $Row3["BookVideoCode"];
										$BookVideoCode2 = $Row3["BookVideoCode2"];

										if ($ClassProductID==1){
											$StrClassProductID = "<span style='color:#0080FF;'>".$수업[$LangID]."</span>";
										}else if ($ClassProductID==2){
											$StrClassProductID = "<span style='color:#FF8000;'>".$레벨테스트[$LangID]."</span>";
										}else if ($ClassProductID==3){
											$StrClassProductID = "<span style='color:#9D4983;'>".$체험수업[$LangID]."</span>";
										}

										if ($MemberChangeTeacher==1){
											$StrMemberChangeTeacher = "<?=$허용[$LangID]?>";
										}else{
											$StrMemberChangeTeacher = "<?=$거부[$LangID]?>";
										}

										$StrClassOrderTimeTypeName = "";
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


										if ($ClassState==0){
											$StrClassState = $미등록[$LangID];
										}else if ($ClassState==1){
											$StrClassState = $등록[$LangID];
										}else if ($ClassState==2){
											$StrClassState = $완료[$LangID];
										}

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

										
										if ($CommonCiTelephoneStudent==""){
											$CommonCiTelephoneStudent = $MemberCiTelephone;
										}

										if ($CommonCiTelephoneTeacher==""){
											$CommonCiTelephoneTeacher = $TeacherCiTelephone;
										}

										
										$Sql2 = "select count(*) as LastClassCount from Classes A where A.ClassID<:ClassID and A.ClassOrderID=:ClassOrderID";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->bindParam(':ClassID', $ClassID);
										$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$LastClassCount = $Row2["LastClassCount"];
										
										$AssmtMonth = 0;
										if ($LastClassCount>0 && (($LastClassCount+1) % 8)==0){
											$AssmtMonth = 1;
										}
                            

										$Sql2 = "select count(*) as AssmtStudentMonthlyScoreCount from AssmtStudentMonthlyScores A where A.ClassID=:ClassID";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->bindParam(':ClassID', $ClassID);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$AssmtStudentMonthlyScoreCount = $Row2["AssmtStudentMonthlyScoreCount"];


										if ($CenterPayType==1){//B2B결제
											if ($MemberPayType==0){
												$StrCenterPayType = "<span style='color:#0080C0;'>B2B 결제</span>";
												$StrStudyAuthDate = $CenterStudyEndDate;
											}else{
												$StrCenterPayType = "<span style='color:#FF8000;'>B2B 개인결제</span>";
												$StrStudyAuthDate = $ClassOrderEndDate;
											}
										}else{
											$StrCenterPayType = "<span style='color:#AE0000;'>B2C 결제</span>";
											$StrStudyAuthDate = $ClassOrderEndDate;
										}

										if ($StrStudyAuthDate=="0000-00-00" || $StrStudyAuthDate==""){
											$StrStudyAuthDate = "-";
										}else{
											$StudyAuthDateDiff = (strtotime($StrStudyAuthDate) - strtotime(date("Y-m-d"))) / 86400;
											if ($StudyAuthDateDiff<=7){
												$StrStudyAuthDate = "<span style='color:#ff0000;'>".$StrStudyAuthDate." (".$StudyAuthDateDiff."일)</span>";
											}
										}


										if ($ClassMemberType==1){//1:1 이면....

											$GroupRowCount = 1;
											$GroupListCount = 1;
											$GroupTrColor = "";
											$ClassMemberTypeName = "";

											$RegClassCount = 0;
											include('./class_list_include.php');
											$CheckListCount ++;
											if ($LastClassNotAssmtCount>0){
											?>
												<script>
												document.getElementById("TrClsEnter_<?=$TeacherID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>").innerHTML = "<?=$이전수업미평가[$LangID]?>";
												</script>
											<?
												$OpenClassCount = 0;
												$LastClassNotAssmtCount = 0;
											}else if ($OpenClassCount>0){
											?>
												<script>
												document.getElementById("TrClsEnter_<?=$TeacherID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>").innerHTML = "<?=$StrClassEnterBtn?>";
												</script>
											<?
												$OpenClassCount = 0;
												$LastClassNotAssmtCount = 0;
											}


											?>

											<script>
											<?if ($ListSelectResetDate!="" && $RegClassCount!=$RegClassCount){?>
												document.getElementById("TrClsReSet_<?=$TeacherID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>").innerHTML = "-";
											<?}?>
											</script>
											<?

										}else{
											
											if ($ClassMemberType==2){
												$ClassMemberTypeName = "<br><span style='color:#6F395A'>(1:2)</span>";
											}else if ($ClassMemberType==3){
												$ClassMemberTypeName = "<br><span style='color:#6F395A'>(G)</span>";
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

												$ClassOrderPayID = $Row_G["ClassOrderPayID"];
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


												$Sql3 = "
														select 
																A.ClassID 
														from Classes A 
														where 
															A.ClassOrderID=:ClassOrderID 
															and (A.ClassAttendState=0 or A.ClassAttendState=1 or A.ClassAttendState=2) 
															and A.ClassState=2 and datediff(A.EndDateTime, '".$SelectDate."')<0 
														order by A.StartDateTime desc limit 0,1
													";
												$Stmt3 = $DbConn->prepare($Sql3);
												$Stmt3->bindParam(':ClassOrderID', $ClassOrderID);
												$Stmt3->execute();
												$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
												$Row3 = $Stmt3->fetch();
												$Stmt3 = null;
												if ($Row3["ClassID"]){
													$LastClassID = $Row3["ClassID"];
												}else{
													$LastClassID = 0;
												}


												$Sql3 = " 
														select 
																B.AssmtStudentDailyScoreID 
														from Classes A inner join AssmtStudentDailyScores B on A.ClassID=B.ClassID 
														where 
															A.ClassOrderID=:ClassOrderID
															and (A.ClassAttendState=0 or A.ClassAttendState=1 or A.ClassAttendState=2) 
															and A.ClassState=2 and datediff(A.EndDateTime, '".$SelectDate."')<0 
														order by A.StartDateTime desc limit 0,1
												";
												$Stmt3 = $DbConn->prepare($Sql3);
												$Stmt3->bindParam(':ClassOrderID', $ClassOrderID);
												$Stmt3->execute();
												$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
												$Row3 = $Stmt3->fetch();
												$Stmt3 = null;
												if ($Row3["AssmtStudentDailyScoreID"]){
													$LastAssmtStudentDailyScoreID = $Row3["AssmtStudentDailyScoreID"];
												}else{
													$LastAssmtStudentDailyScoreID = 0;
												}


												if ($ClassProductID==1){
													$StrClassProductID = "<span style='color:#0080FF;'>".$수업[$LangID]."</span>";
												}else if ($ClassProductID==2){
													$StrClassProductID = "<span style='color:#FF8000;'>".$레벨테스트[$LangID]."</span>";
												}else if ($ClassProductID==3){
													$StrClassProductID = "<span style='color:#9D4983;'>".$체험수업[$LangID]."</span>";
												}

												if ($MemberChangeTeacher==1){
													$StrMemberChangeTeacher = "허용";
												}else{
													$StrMemberChangeTeacher = "거부";
												}

												$StrClassOrderTimeTypeName = "";
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


												if ($ClassState==0){
													$StrClassState = $미등록[$LangID];
												}else if ($ClassState==1){
													$StrClassState = $등록[$LangID];
												}else if ($ClassState==2){
													$StrClassState = $완료[$LangID];
												}


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

												
												if ($CommonCiTelephoneStudent==""){
													$CommonCiTelephoneStudent = $MemberCiTelephone;
												}

												if ($CommonCiTelephoneTeacher==""){
													$CommonCiTelephoneTeacher = $TeacherCiTelephone;
												}

												
												$Sql2 = "select count(*) as LastClassCount from Classes A where A.ClassID<:ClassID and A.ClassOrderID=:ClassOrderID";
												$Stmt2 = $DbConn->prepare($Sql2);
												$Stmt2->bindParam(':ClassID', $ClassID);
												$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
												$Stmt2->execute();
												$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
												$Row2 = $Stmt2->fetch();
												$Stmt2 = null;
												$LastClassCount = $Row2["LastClassCount"];
												
												$AssmtMonth = 0;
												if ($LastClassCount>0 && (($LastClassCount+1) % 8)==0){
													$AssmtMonth = 1;
												}
									

												$Sql2 = "select count(*) as AssmtStudentMonthlyScoreCount from AssmtStudentMonthlyScores A where A.ClassID=:ClassID";
												$Stmt2 = $DbConn->prepare($Sql2);
												$Stmt2->bindParam(':ClassID', $ClassID);
												$Stmt2->execute();
												$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
												$Row2 = $Stmt2->fetch();
												$Stmt2 = null;
												$AssmtStudentMonthlyScoreCount = $Row2["AssmtStudentMonthlyScoreCount"];


												if ($CenterPayType==1){//B2B결제
													if ($MemberPayType==0){
														$StrCenterPayType = "<span style='color:#0080C0;'>B2B 결제</span>";
														$StrStudyAuthDate = $CenterStudyEndDate;
													}else{
														$StrCenterPayType = "<span style='color:#FF8000;'>B2B 개인결제</span>";
														$StrStudyAuthDate = $ClassOrderEndDate;
													}

												}else{
													$StrCenterPayType = "<span style='color:#AE0000;'>B2C 결제</span>";
													$StrStudyAuthDate = $ClassOrderEndDate; 
												}

												if ($StrStudyAuthDate=="0000-00-00" || $StrStudyAuthDate==""){
													$StrStudyAuthDate = "-";
												}else{
													$StudyAuthDateDiff = (strtotime($StrStudyAuthDate) - strtotime(date("Y-m-d"))) / 86400;
													if ($StudyAuthDateDiff<=7){
														$StrStudyAuthDate = "<span style='color:#ff0000;'>".$StrStudyAuthDate." (".$StudyAuthDateDiff."일)</span>";
													}
												}

												include('./class_list_include.php');
												$CheckListCount ++;
												$GroupListCount ++;
												
											
											}
											$Stmt_G = null;


											if ($LastClassNotAssmtCount>0){
											?>
												<script>
												document.getElementById("TrClsEnter_<?=$TeacherID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>").innerHTML = "<?=$이전수업미평가[$LangID]?>";
												</script>
											<?
												$OpenClassCount = 0;
												$LastClassNotAssmtCount = 0;
											}else if ($OpenClassCount>0){
											?>
												<script>
												document.getElementById("TrClsEnter_<?=$TeacherID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>").innerHTML = "<?=$StrClassEnterBtn?>";
												</script>
											<?
												$OpenClassCount = 0;
												$LastClassNotAssmtCount = 0;
											}

											?>

											<script>
											<?if ($ListSelectResetDate!="" && $RegClassCount!=$RegClassCount){?>
												document.getElementById("TrClsReSet_<?=$TeacherID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>").innerHTML = "-";
											<?}?>
											</script>
											<?


										}
                                        $ListCount ++;
										
                                    }
                                    $Stmt = null;
                                    ?>


                                </tbody>
                            </table>
                        </div>

						<input type="hidden" name="LastClassNotAssmtClasses" id="LastClassNotAssmtClasses" value="<?=$LastClassNotAssmtClasses?>">
                        <div class="uk-form-row" style="text-align:left;margin-top:20px;">
							<?if ($ListSelectResetDate==""){?>
							<a type="button" href="javascript:SendMessageForm()" class="md-btn md-btn-primary"><?=$메시지전송[$LangID]?></a> 
							<a type="button" href="javascript:ClassListResetStep1()" class="md-btn md-btn-primary" style="background-color:#8080C0;"><?=$선택하여연기하기[$LangID]?> STEP 1</a> 
							<?}else{?>
							<a type="button" href="javascript:ClassListResetStep2()" class="md-btn md-btn-warning"><?=$선택하여연기하기[$LangID]?> STEP 2</a> 
							<?}?>
                        </div>

                        <?php
						if ($PageListNum!="0"){
							include_once('./inc_pagination.php');
						}
                        ?>
						
						<br><br><br>



                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<script>
function ClassListResetStep1(){
	document.SearchForm.ListSelectResetDate.value = "1";
	document.SearchForm.ListSelectResetDatePage.value = "<?=$CurrentPage?>";
	document.SearchForm.submit();
}
function ClassListResetStep2(){
	SendMessageForm();
}


var CheckListCount = <?=$CheckListCount-1?>;
function CheckListAll(obj){

	for (ii=1;ii<=CheckListCount;ii++){
		if (obj.checked){
			document.getElementById("CheckBox_"+ii).checked = true;
		}else{
			document.getElementById("CheckBox_"+ii).checked = false;
		}	
	}
}

function SendMessageForm(){

	if (CheckListCount==0){
		alert("<?=$선택한_목록이_없습니다[$LangID]?>");
	}else{
		
		MemberIDs = "|";
		for (ii=1;ii<=CheckListCount;ii++){
			if (document.getElementById("CheckBox_"+ii).checked){
				MemberIDs = MemberIDs + document.getElementById("CheckBox_"+ii).value + "|";
			}	
		}

	
		if (MemberIDs=="|"){
			alert("<?=$선택한_목록이_없습니다[$LangID]?>");
		}else{

			<?if ($ListSelectResetDate=="1"){?>
				var OpenUrl = "../pop_class_reset_date_multi_form.php?ClassIDs="+MemberIDs;

				$.colorbox({	
					href:OpenUrl
					,width:"95%" 
					,height:"95%"
					,maxWidth: "500"
					,maxHeight: "400"
					,title:""
					,iframe:true 
					,scrolling:true
					//,onClosed:function(){location.reload(true);}
					//,onComplete:function(){alert(1);}
				}); 
			<?}else{?>
				openurl = "send_message_log_multi_form.php?MemberIDs="+MemberIDs;
				$.colorbox({	
					href:openurl
					,width:"95%" 
					,height:"95%"
					,maxWidth: "850"
					,maxHeight: "750"
					,title:""
					,iframe:true 
					,scrolling:true
					//,onClosed:function(){location.reload(true);}
					//,onComplete:function(){alert(1);}
				});
			<?}?>	
			

		}
	}
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
<?
include_once('./inc_common_list_js.php');

?>

<!-- ============== only this page js ============== -->

<!-- moment & moment timezone library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.26/moment-timezone.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.26/moment-timezone-with-data.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/locale/ko.js"></script>
<script>


function OpenProductCartList(MemberID){

		var OpenUrl = "./product_order_cart_product_list_teacher.php?MemberID="+MemberID;

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


function SelectList(CheckListCount){
	if (document.getElementById("TrList_"+CheckListCount).className==""){
		document.getElementById("TrList_"+CheckListCount).className="TrListBgColor";
	}else{
		document.getElementById("TrList_"+CheckListCount).className="";
	}
}

function OpenScan(ScanID, ClassID, BookSystemType) {
	var dummy = document.createElement("input");
	dummy.id = "dummy_id";
	dummy.width = "2px";
	dummy.height  = "2px";
	document.body.appendChild(dummy);

	if(BookSystemType==0) { // mangoi
		if(ScanID=="") {
			alert("<?=$교재가_설정되지_않았습니다[$LangID]?>");
			return 0;
		} else {
			dummy.setAttribute("value", "http://<?=$DefaultDomain2?>/uploads/book_pdf_uploads/" + ScanID);
		}
	} else { // JT
		if(ScanID=="") {
			alert("<?=$교재가_설정되지_않았습니다[$LangID]?>");
			return 0;
		} else {
			var StrContentType = "<?=$일반교재[$LangID]?>";
			$.ajaxSetup({async: false}); 

			$.post( "../webook/_get_unit_content.php", { content_type:"학생<?=$선택한_목록이_없습니다[$LangID]?>", unit_id:ScanID, api_extension:"Content URL", width:"100%", height: "100%", unit_contents_type:StrContentType, async:false })
			  .done(function( data ) {
				dummy.setAttribute("value", data);
			});
		}
    }
	//alert(dummy.value);
	//dummy.focus();
	dummy.select();
	var CheckResult = document.execCommand("copy");

	if(CheckResult==true) {
		alert("<?=$교재_URL_복사_되었습니다[$LangID]?>");
	} else {
		alert("<?=$교재_URL_복사가_되지_않았습니다[$LangID]?>");
	}
	document.body.removeChild(dummy);
	//dummy.parentNode.removeChild(dummy);
	this.focus();
}

function OpenVideo(VideoType, VideoCode, ClassID, ClassVideoType) {
	if(ClassVideoType==1) {
		var Str = "A";
	} else {
		var Str = "B";
	}

	if(VideoCode=="" || VideoCode==0) {
		alert("<?=$비디오[$LangID]?>"+Str+ "<?=$존재하지_않습니다[$LangID]?>"); 
	} else {
		var OpenUrl = "../pop_video_player_study.php?VideoType="+VideoType+"&VideoCode="+VideoCode+"&ClassID="+ClassID+"&ClassVideoType="+ClassVideoType;

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
}

function OpenQuiz(QuizID) {
	alert("<?=$Quiz_설정된_수업입니다[$LangID]?>");
}

function OpenMessageSendForm(MemberID){
	openurl = "send_message_log_form.php?MemberID="+MemberID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenMemberPointForm(MemberID){
	openurl = "member_point_form.php?MemberID="+MemberID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function OpenStudentScoreMonthlyForm(ClassID){
	var OpenUrl = "../pop_student_monthly_score_form.php?ClassID="+ClassID;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "800"
		,maxHeight: "900"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function OpenStudentScoreDailyForm(ClassID){
	var OpenUrl = "../pop_student_daily_score_form.php?ClassID="+ClassID;
	WW = "800";
	WH = "600";

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: WW
		,maxHeight: WH
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenStudentScoreLeveltestForm(ClassID){
	var OpenUrl = "../pop_student_leveltest_score_form.php?ClassID="+ClassID;
	WW = "900";
	WH = "900";

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: WW
		,maxHeight: WH
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function OpenStudentScoreMonthlyReport(AssmtStudentMonthlyScoreID){

	if (AssmtStudentMonthlyScoreID!=""){

		var OpenUrl = "../report_monthly.php?AssmtStudentMonthlyScoreID="+AssmtStudentMonthlyScoreID;

		$.colorbox({	
			href:OpenUrl
			,width:"95%" 
			,height:"95%"
			,maxWidth: "1200"
			,maxHeight: "900"
			,title:""
			,iframe:true 
			,scrolling:true
			//,onClosed:function(){location.reload(true);}
			//,onComplete:function(){alert(1);}
		}); 
	}

}


function OpenStudentScoreDailyReport(ClassID){

	var OpenUrl = "../report_daily.php?ClassID="+ClassID;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1200"
		,maxHeight: "900"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function OpenStudentScoreLeveltestReport(AssmtStudentLeveltestScoreID){
	var OpenUrl = "../report_leveltest.php?AssmtStudentLeveltestScoreID="+AssmtStudentLeveltestScoreID;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1200"
		,maxHeight: "900"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function OpenClassReg(ClassOrderID, StartYear, StartMonth, StartDay, StartHour, StartMinute, ClassOrderTimeTypeID, TeacherID, MemberID, ClassMemberType, LastClassID, LastAssmtStudentDailyScoreID, RegType, GroupRowCount, ClassProductID, ClassOrderSlotType, StudyTimeWeek, ClassOrderPayID, ClassOrderSlotEndDate ){//RegType 1:등록 2:등록전 연기 
	
	url = "ajax_set_class_reg.php";
	//location.href = url + "?ClassOrderID="+ClassOrderID+"&StartYear="+StartYear+"&StartMonth="+StartMonth+"&StartHour="+StartHour+"&StartMinute="+StartMinute+"&ClassOrderTimeTypeID="+ClassOrderTimeTypeID+"&TeacherID="+TeacherID+"&MemberID="+MemberID;

    $.ajax(url, {
        data: {
			ClassOrderID: ClassOrderID,
			StartYear: StartYear,
			StartMonth: StartMonth,
			StartDay: StartDay,
			StartHour: StartHour,
			StartMinute: StartMinute,
			ClassOrderTimeTypeID: ClassOrderTimeTypeID,
			TeacherID: TeacherID,
			MemberID: MemberID,
			ClassOrderPayID: ClassOrderPayID
        },
        success: function (data) {

			ClassID = data.ClassID;
			CommonShClassCode = data.CommonShClassCode;
			TeacherName = data.TeacherName;
			TeacherLoginID = data.TeacherLoginID;
			var BookWebookUnitID = data.BookWebookUnitID; // JT 교재일때
			var BookScanImageFileName = data.BookScanImageFileName; // 자사 교재일때
			var BookSystemType = data.BookSystemType; // 0 은 망고, 1 는 JT
			var MemberLoginID = data.MemberLoginID;

			var BookScanValue = "";
			if(BookSystemType==0) {
				BookScanValue = BookScanImageFileName;
			} else {
				BookScanValue = BookWebookUnitID;
			}

			document.getElementById("TrClsSet_"+ClassOrderID+"_"+StartYear+"_"+StartMonth+"_"+StartDay+"_"+StartHour+"_"+StartMinute).innerHTML = "<a class=\"md-btn md-btn-warning md-btn-mini md-btn-wave-light\" href=\"javascript:OpenClassSetup("+ClassID+");\"><?=$수업설정[$LangID]?></a>";

			document.getElementById("TrClsQna_"+ClassOrderID+"_"+StartYear+"_"+StartMonth+"_"+StartDay+"_"+StartHour+"_"+StartMinute).innerHTML = "<a href=\"javascript:OpenClassQnaForm("+ClassID+");\"><i class=\"material-icons\">new_releases</i></a>";
	
			
			//=============================== 수업 상태에 따른 버튼 출력 ============================
			LastClassNotAssmtClasses = document.getElementById("LastClassNotAssmtClasses").value;
			ClassOpenBtn = "";
			//이전수업 미평가 처리 --> 아래 주석을 지우준다. PHP 리스트도 처리해 준다.
			//if (LastClassNotAssmtClasses.indexOf("|"+TeacherID+"_"+StartYear+"_"+StartMonth+"_"+StartDay+"_"+StartHour+"_"+StartMinute+"|")>=0){
			//	ClassOpenBtn = "이전수업 미평가";
			//}else{
				//if (LastClassID!=0 && LastAssmtStudentDailyScoreID==0){
				//	ClassOpenBtn = "이전수업 미평가";
				//	LastClassNotAssmtClasses = LastClassNotAssmtClasses + TeacherID+"_"+StartYear+"_"+StartMonth+"_"+StartDay+"_"+StartHour+"_"+StartMinute + "|"
				//}else{
					<?if($_LINK_ADMIN_LEVEL_ID_ <=13 && $_LINK_ADMIN_LEVEL_ID_>=9) {// 지사 나 센터일 경우 옵저버로 입장?>
						ClassOpenBtn = "<a class=\"md-btn md-btn-success md-btn-mini md-btn-wave-light\" style=\"background-color:#808000;\" href=\"javascript:OpenClassSh("+ClassID+", '"+CommonShClassCode+"', 2, '<?=$_LINK_ADMIN_NAME_?>', '<?=$_LINK_ADMIN_LOGIN_ID_?>');\">수업참관 (SH)</a>";
						//ClassOpenBtn = ClassOpenBtn + "<a class=\"md-btn md-btn-success md-btn-mini md-btn-wave-light\" style=\"background-color:#80FF00;\" href=\"javascript:CopyScanLink("+BookScanValue+", "+ClassID+", "+BookSystemType+", "+MemberLoginID+");\">교재링크</a>";
					<?} else if($_LINK_ADMIN_LEVEL_ID_==15) {// 강사일 경우 수업입장?>
						ClassOpenBtn = "<a class=\"md-btn md-btn-success md-btn-mini md-btn-wave-light\" href=\"javascript:OpenClassSh("+ClassID+", '"+CommonShClassCode+"', 1, '"+TeacherName+"', '"+TeacherLoginID+"');\"><?=$수업입장[$LangID]?> (SH)</a>";
						//ClassOpenBtn = ClassOpenBtn + "<a class=\"md-btn md-btn-success md-btn-mini md-btn-wave-light\" style=\"background-color:#80FF00;\" href=\"javascript:CopyScanLink("+BookScanValue+", "+ClassID+", "+BookSystemType+", "+MemberLoginID+");\">교재링크</a>";
					<?}else if($_LINK_ADMIN_LEVEL_ID_ < 9) {// 지사보다 위인 관리자들은 옵저버, 수업입장 둘다 출력?>
						ClassOpenBtn = "<a class=\"md-btn md-btn-success md-btn-mini md-btn-wave-light\" href=\"javascript:OpenClassSh("+ClassID+", '"+CommonShClassCode+"', 1, '"+TeacherName+"', '"+TeacherLoginID+"');\"><?=$수업입장[$LangID]?> (SH)</a>";

						ClassOpenBtn = ClassOpenBtn + "<a class=\"md-btn md-btn-success md-btn-mini md-btn-wave-light\" style=\"background-color:#808000;\" href=\"javascript:OpenClassSh("+ClassID+", '"+CommonShClassCode+"', 2, '<?=$_LINK_ADMIN_NAME_?>', '<?=$_LINK_ADMIN_LOGIN_ID_?>');\">수업참관 (SH)</a>";
						//ClassOpenBtn = ClassOpenBtn + "<a class=\"md-btn md-btn-success md-btn-mini md-btn-wave-light\" style=\"background-color:#80FF00;\" href=\"javascript:CopyScanLink("+BookScanValue+", "+ClassID+", "+BookSystemType+", "+MemberLoginID+");\">교재링크</a>";
					<?}?>
						
				//}
			//}
			document.getElementById("TrClsEnter_"+TeacherID+"_"+StartYear+"_"+StartMonth+"_"+StartDay+"_"+StartHour+"_"+StartMinute).innerHTML = ClassOpenBtn;
			//=============================== 수업 상태에 따른 버튼 출력 ============================

			<?if ($ListSelectResetDate!=""){?>
				
				ClassResetBtns = "<a class=\"md-btn md-btn-gray md-btn-mini md-btn-wave-light\" href=\"javascript:OpenResetDateForm("+ClassID+", "+GroupRowCount+", "+ClassMemberType+", "+StartHour+", "+StartMinute+");\"><?=$수업연기[$LangID]?></a><a class=\"md-btn md-btn-gray md-btn-mini md-btn-wave-light\" href=\"javascript:OpenResetDateForm2("+ClassID+","+ClassOrderID+","+MemberID+","+TeacherID+", "+GroupRowCount+", "+ClassMemberType+", "+StartHour+", "+StartMinute+");\" style=\"background-color:#f1f1f1;\"><?=$강사변경[$LangID]?></a>";
			
				if (ClassProductID==1 && ClassOrderSlotType==1){
					ClassResetBtns = ClassResetBtns + "<br><a class=\"md-btn md-btn-gray md-btn-mini md-btn-wave-light\" href=\"javascript:OpenResetDateForm3("+ClassID+","+ClassOrderID+","+MemberID+","+TeacherID+", "+GroupRowCount+", "+ClassMemberType+", "+StartHour+", "+StartMinute+");\" style=\"margin-top:10px;background-color:#C7C7C7;\"><?=$보강등록[$LangID]?></a><a class=\"md-btn md-btn-gray md-btn-mini md-btn-wave-light\" href=\"javascript:OpenResetDateForm4("+ClassID+","+ClassOrderID+","+MemberID+","+TeacherID+", "+GroupRowCount+", "+ClassMemberType+", "+StartYear+","+StartMonth+","+StartDay+", "+StartHour+", "+StartMinute+", "+StudyTimeWeek+", "+ClassOrderSlotEndDate+");\" style=\"margin-top:10px;background-color:#999999;color:#ffffff;\"><?=$스케줄변경[$LangID]?></a>";
				}

				document.getElementById("TrClsReSet_"+TeacherID+"_"+StartYear+"_"+StartMonth+"_"+StartDay+"_"+StartHour+"_"+StartMinute).innerHTML = ClassResetBtns;

			<?}?>

			if (RegType==2){//연기일 경우
				OpenResetDateForm(ClassID, GroupRowCount, ClassMemberType, StartHour, StartMinute);
			}else if (RegType==3){//강사변경
				OpenResetDateForm2(ClassID, ClassOrderID, MemberID, TeacherID, GroupRowCount, ClassMemberType, StartHour, StartMinute);
			}else if (RegType==4){//보충강의
				OpenResetDateForm3(ClassID, ClassOrderID, MemberID, TeacherID, GroupRowCount, ClassMemberType, StartHour, StartMinute);
			}else if (RegType==5){//스케줄변경
				OpenResetDateForm4(ClassID, ClassOrderID, MemberID, TeacherID, GroupRowCount, ClassMemberType, StartYear, StartMonth, StartDay, StartHour, StartMinute, StudyTimeWeek, ClassOrderSlotEndDate);
            }

        },
        error: function () {
            //alert('Error while contacting server, please try again');
        }

    });

}

function CopyScanLink(BookLink, ClassID, Type, MemberLoginID) {

	//alert(BookLink);
	var dummy = document.createElement("input");
	dummy.id = "dummy_id";
	document.body.appendChild(dummy);

	if(Type==0) { // mangoi
		if(BookLink=="") {
			alert("<?=$교재가_설정되지_않았습니다[$LangID]?>");
			return 0;
		} else {
			dummy.setAttribute("value", "http://<?=$DefaultDomain2?>/uploads/book_pdf_uploads/" + BookLink);
		}
	} else { // JT
		var StrContentType = "<?=$일반교재[$LangID]?>";
		//alert(BookLink);
		$.post( "../webook/_get_unit_content.php", { content_type:"<?=$학생[$LangID]?>", unit_id:BookLink, api_extension:"Content URL", width:"100%", height: "100%", unit_contents_type:StrContentType })
		  .done(function( data ) {

			dummy.setAttribute("value", data);
		});
    }
	//alert(dummy.value);
	dummy.focus();
	dummy.select();
	var CheckResult = document.execCommand("copy");

	if(CheckResult==true) {
		alert("<?=$교재_URL_복사_되었습니다[$LangID]?>");
	} else {
		alert("<?=$교재_URL_복사가_되지_않았습니다[$LangID]?>");
	}
	dummy.parentNode.removeChild(dummy);

}

function OpenResetDateForm(ClassID, GroupRowCount, ClassMemberType, SetHour, SetMinute){//연기
	var OpenUrl = "../pop_class_reset_date_form.php?ClassID="+ClassID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&SetHour="+SetHour+"&SetMinute="+SetMinute;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "500"
		,maxHeight: "400"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function OpenResetDateForm2(ClassID, ClassOrderID, MemberID, TeacherID, GroupRowCount, ClassMemberType, SetHour, SetMinute){//강사변경
	//var OpenUrl = "../pop_class_reset_date_form_teacher_select_form.php?ClassID="+ClassID+"&ClassOrderID="+ClassOrderID+"&MemberID="+MemberID+"&TeacherID="+TeacherID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&ResetType=ChTeacher";
	var OpenUrl = "../pop_class_reset_date_form_date_select_form.php?ClassID="+ClassID+"&ClassOrderID="+ClassOrderID+"&MemberID="+MemberID+"&TeacherID="+TeacherID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&SetHour="+SetHour+"&SetMinute="+SetMinute+"&ResetType=ChTeacher";

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "800"
		,maxHeight: "900"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenResetDateForm3(ClassID, ClassOrderID, MemberID, TeacherID, GroupRowCount, ClassMemberType, SetHour, SetMinute){//보강
	var OpenUrl = "../pop_class_reset_date_form_teacher_select_form.php?ClassID="+ClassID+"&ClassOrderID="+ClassOrderID+"&MemberID="+MemberID+"&TeacherID="+TeacherID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&ResetType=PlusClass";
	//var OpenUrl = "../pop_class_reset_date_form_date_select_form.php?ClassID="+ClassID+"&ClassOrderID="+ClassOrderID+"&MemberID="+MemberID+"&TeacherID="+TeacherID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&SetHour="+SetHour+"&SetMinute="+SetMinute+"&ResetType=PlusClass";

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "800"
		,maxHeight: "900"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenResetDateForm4(ClassID, ClassOrderID, MemberID, TeacherID, GroupRowCount, ClassMemberType, SetYear, SetMonth, SetDay, SetHour, SetMinute, SetWeek, ClassOrderSlotEndDate){//스케줄변경
	//if (confirm(SetYear+"년 "+SetMonth+"월 "+SetDay+"일 강의부터 일정을 변경합니다.")){
	if(ClassOrderSlotEndDate==null) {
		//var OpenUrl = "../pop_class_reset_date_form_teacher_select_form.php?ClassID="+ClassID+"&ClassOrderID="+ClassOrderID+"&MemberID="+MemberID+"&TeacherID="+TeacherID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&SetYear="+SetYear+"&SetMonth="+SetMonth+"&SetDay="+SetDay+"&SetHour="+SetHour+"&SetMinute="+SetMinute+"&SetWeek="+SetWeek+"&ResetType=EverChange";
		var OpenUrl = "../pop_class_reset_date_form_date_select_form.php?ClassID="+ClassID+"&ClassOrderID="+ClassOrderID+"&MemberID="+MemberID+"&TeacherID="+TeacherID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&SetYear="+SetYear+"&SetMonth="+SetMonth+"&SetDay="+SetDay+"&SetHour="+SetHour+"&SetMinute="+SetMinute+"&SetWeek="+SetWeek+"&ResetType=EverChange";

		$.colorbox({	
			href:OpenUrl
			,width:"95%" 
			,height:"95%"
			,maxWidth: "800"
			,maxHeight: "900"
			,title:""
			,iframe:true 
			,scrolling:true
			//,onClosed:function(){location.reload(true);}
			//,onComplete:function(){alert(1);}
		}); 
	} else {
		alert("<?=$스케줄변경을_하셨던_수업은[$LangID]?>\<?=$n다시_스케줄변경을_하실_수_없습니다[$LangID]?>");
	}
	//}
}

//시간체크
function ClassTimeCheck(ClassID){
	return true;
	/*
	$.ajax({
		url: "ajax_check_class_time.php",
		emthod: "POST",
		data: {
			ClassID: ClassID
		},
		success: function(data) {
			
			if (data.EnableClassTime == 0){
				EnableClassTime=false;
			}else{	
				EnableClassTime=true;
			}

			return EnableClassTime;

		},
		error: function(req, stat, err) {
			EnableClassTime=false;
			return EnableClassTime;
		}
	});
	*/

}

//새하 열기 : MemberType - 0:학생 1:강사
function OpenClassSh(ClassID, CommonShClassCode, MemberType, MemberName, MemberLoginID){

	if (ClassTimeCheck(ClassID)){

		//MemberType : 새하에서 1은 강사, 2는 옵저버

		url = "ajax_set_teacher_class_in_time.php";
		//location.href = url + "?ClassID="+ClassID+"&MemberType="+MemberType;
		$.ajax(url, {
			data: {
				ClassID:ClassID,
				MemberType: MemberType 
			},
			success: function (data) {
				AjaxOpenClassSh(ClassID, CommonShClassCode, MemberType, MemberName, MemberLoginID);



			},
			error: function (e) {
				alert(e);
			}
		});



	}else{
		alert("<?=$강의실_입장은_시작시간_전후_10분_이내만_허용합니다[$LangID]?>");
	}

}

function AjaxOpenClassSh(ClassID, CommonShClassCode, MemberType, MemberName, MemberLoginID) {
	var url = "ajax_set_open_class_sh.php";
	var result = '';
	var roomTitle = CommonShClassCode;
	//location.href = url + "?ClassID="+ClassID+"&MemberType="+MemberType;

	$.ajax(url, {
		data: {
			ClassID:ClassID
		},
		success: function (data) {
			CommonShNewClassCode = data.CommonShNewClassCode;
			OnlineSiteShVersion = data.OnlineSiteShVersion;
			if(OnlineSiteShVersion==1) {
				CommonShClassCode = CommonShNewClassCode;
			}
			
			OpenClassShNew(CommonShClassCode, MemberType, MemberName, MemberLoginID, OnlineSiteShVersion,roomTitle);
		},
		error: function (e) {
			alert(e);
		}
	});
}

function OpenClassShNew(CommonShClassCode, MemberType, MemberName, MemberLoginID, OnlineSiteShVersion,roomTitle) {
	//alert(OnlineSiteShVersion);
	
	
	if(OnlineSiteShVersion==1) {
		MvApi.defaultSettings({
			debug: false,
			// tcps: {key: 'MTgwLjE1MC4yMzAuMTk1OjcwMDE'},
            // 2024-11-29 새하컴즈 서버 이전 작업에 따른 변경
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
					store: true, 
					// 스킴 이름
					scheme: 'cloudboda',
					// 패키지 이름
					packagename: 'zone.cloudboda',
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


		if(MemberType==0) {//학생
			MemberType = 22;
		}else if(MemberType==2) {//참석표시 안되는 관리자
			MemberType = 3;
		} else {//교사
			MemberType = 21;
		}

		
		$('input[name=userId]').val(MemberLoginID);
		//$('input[name=title]').val("망고아이 수업");
		$('input[name=title]').val(roomTitle);
		$('input[name=userName]').val(MemberName);
		$('input[name=joinUserType]').val(MemberType);
		$('input[name=roomCode]').val(CommonShNewClassCode);

		console.log($('form[data-mv-api]'));
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
						//console.error('error.', errorCode, reason);
						//alert('error :' + errorCode +" / "+ reason);
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

		MemberType = 1;//클래스인에서는 1로 고정(새하에서 1은 강사, 2는 옵저버)

		url = "ajax_set_teacher_class_in_time.php";
		//location.href = url + "?ClassID="+ClassID;
		$.ajax(url, {
			data: {
				ClassID:ClassID,
				MemberType: MemberType 
			},
			success: function (data) {

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

			},
			error: function () {

			}
		});


}

//클래스인 열기 : MemberType - 1:학생 2:강사
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


	if (ClassTimeCheck(ClassID)){

		if (CommonCiTelephoneTeacher=="" || CommonCiTelephoneStudent==""){
			if (CommonCiTelephoneTeacher==""){
				alert("<?=$강사_클래스인_로그인_아이디가_설정되지_않습니다[$LangID]?>");
			}else{
				alert("<?=$학생_클래스인_로그인_아이디가_설정되지_않습니다[$LangID]?>");
			}
		}else{

			if (MemberType==1){
				CommonCiTelephone = CommonCiTelephoneStudent;
			}else{
				CommonCiTelephone = CommonCiTelephoneTeacher;
			}


			//location.href = "ajax_check_ci_class_in_db.php?ClassID="+ClassID+"&MemberType="+MemberType+"&CommonCiTelephone="+CommonCiTelephone+"&BeginTime="+BeginTime+"&EndTime="+EndTime;

			$.ajax({
				url: "ajax_check_ci_class_in_db.php",
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


	}else{
		alert("<?=$강의실_입장은_시작시간_전후_10분_이내만_허용합니다[$LangID]?>");
	}

}


function OpenClassSetup(ClassID){

    openurl = "class_setup_form.php?ClassID="+ClassID;
    $.colorbox({    
        href:openurl
        ,width:"95%"
        ,height:"95%"
        ,maxWidth: "850"
        ,maxHeight: "750"
        ,title:""
        ,iframe:true 
        ,scrolling:true
        //,onClosed:function(){location.reload(true);}
        //,onComplete:function(){alert(1);}
    });

}


function OpenClassDetailList(ClassOrderID){
    openurl = "class_detail_list.php?ClassOrderID="+ClassOrderID;
    $.colorbox({    
        href:openurl
        ,width:"95%"
        ,height:"95%"
        ,maxWidth: "1100"
        ,maxHeight: "800"
        ,title:""
        ,iframe:true 
        ,scrolling:true
        ,onClosed:function(){location.reload(true);}
        //,onComplete:function(){alert(1);}
    });
}




function OpenClassQnaForm(ClassID){
    openurl = "class_qna_form.php?ClassID="+ClassID;
    $.colorbox({    
        href:openurl
        ,width:"95%" 
        ,height:"95%"
        ,maxWidth: "850"
        ,maxHeight: "750"
        ,title:""
        ,iframe:true 
        ,scrolling:true
        //,onClosed:function(){location.reload(true);}
        //,onComplete:function(){alert(1);}
    }); 
}

function OpenTeacherMessageForm(TeacherID){
    openurl = "teacher_message_form.php?TeacherID="+TeacherID;
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



function CheckNewClassChMsg(){

		url = "ajax_check_new_class_ch_msg.php";

		//location.href = url + "?ClassOrderWeekCountID="+ClassOrderWeekCountID+"&ClassOrderTimeTypeID="+ClassOrderTimeTypeID+"&ClassOrderStartDate="+ClassOrderStartDate+"&ClassProductID="+ClassProductID+"&SelectSlotCode="+SelectSlotCode+"&SelectStudyTimeCode="+SelectStudyTimeCode;
		$.ajax(url, {
			data: {
			},
			success: function (data) {
				if (data.MsgCount>0){
					alert(data.Msg);
					location.reload();
				}
			},
			error: function () {

			}
		});
}


//클래스 복원
//ClassAttendState - 4:학생연기 5:강사연기 6:학생취소 7:강사취소 8:교사변경 
function ClassReturn(ClassID, ClassAttendState){
	
	ConfirmOk = 0;
	if (ClassAttendState==6 || ClassAttendState==7){
		ConfirmOk = 1;
		if (confirm("<?=$수업을_복원하시겠습니까[$LangID]?>?")){
			ConfirmOk = 1;
		}
	}else if (ClassAttendState==4 || ClassAttendState==5){
		if (confirm("복원하시겠습니까?")){
			ConfirmOk = 1;
		}
	}else{
		if (confirm("강사변경의 경우 복원하더라도 신규로 변경되어 등록된 수업은 그대로 남아 있습니다. 수동으로 삭제해 주시기 바랍니다.")){
			ConfirmOk = 1;
		}
	}

	if (ConfirmOk==1){
	
		url = "ajax_set_class_return.php";

		//location.href = url + "?ClassID="+ClassID;
		$.ajax(url, {
			data: {
				ClassID: ClassID
			},
			success: function (data) {
				location.reload();
			},
			error: function () {
		
			}
		});
	}
}

//연기, 강사변경으로 등록된 수업 삭제
function DeleteClassOrderSlot(SelectDate, ClassOrderID, ClassID, ClassOrderTimeTypeID, TeacherID, StudyTimeHour, StudyTimeMinute){
	
	if (confirm("<?=$삭제_하시겠습니까[$LangID]?>?")){
		url = "ajax_set_temp_class_delete.php";

		//location.href = url + "?SelectDate="+SelectDate+"&ClassOrderID="+ClassOrderID+"&ClassID="+ClassID+"&ClassOrderTimeTypeID="+ClassOrderTimeTypeID+"&TeacherID="+TeacherID+"&StudyTimeHour="+StudyTimeHour+"&StudyTimeMinute="+StudyTimeMinute;
		$.ajax(url, {
			data: {
				SelectDate: SelectDate,
				ClassOrderID: ClassOrderID,
				ClassID: ClassID,
				ClassOrderTimeTypeID: ClassOrderTimeTypeID,
				TeacherID: TeacherID,
				StudyTimeHour: StudyTimeHour,
				StudyTimeMinute: StudyTimeMinute
			},
			success: function (data) {
				location.reload();
			},
			error: function () {
		
			}
		});
	}

}



</script>



<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->
<!-- =================== moment library ================= -->
<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.26/moment-timezone.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.26/moment-timezone-with-data.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/locale/ko.js"></script>
-->
<!-- =================== moment library ================= -->

<script>
function DownExcelSetup(){
	document.SearchForm.action = "class_list_setup_excel.php";
    document.SearchForm.submit();
}

function SearchSubmit(){
	document.SearchForm.action = "class_list.php";
    document.SearchForm.submit();
}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>
