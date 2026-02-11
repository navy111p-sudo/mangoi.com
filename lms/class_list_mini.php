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

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
<style>
.uk-overflow-container{
    overflow: unset;
}
.tooltip {
  position: relative;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;

  position: absolute;
  z-index: 1;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
}

/* 툴팁 화살표 기본 스타일 설정 */
.tooltip .tooltiptext::after {
  content: " ";
  position: absolute;
  border-style: solid;
  border-width: 5px;
}

/* 위쪽 툴팁 */
.tooltip .tooltip-top {
  width: 120px;
  bottom: 150%;
  left: 50%;
  margin-left: -60px;
}
.tooltip .tooltip-top::after {
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-color: black transparent transparent transparent;
}

/* 아래쪽 툴팁 */
.tooltip .tooltip-bottom {
  width: 120px;
  top: 150%;
  left: 50%;
  margin-left: -60px;
}
.tooltip .tooltip-bottom::after {
  bottom: 100%;
  left: 50%;
  margin-left: -5px;
  border-color: transparent transparent black transparent;
}
</style>
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


//include_once('./inc_top.php');
//include_once('./inc_menu_left.php');
?>



<?php
$AddSqlWhere = "1=1";
$ListSelectResetDate = "1";
$ListSelectResetDatePage = 1;


//==== mini ====
$SelectDate = isset($_REQUEST["SelectDate"]) ? $_REQUEST["SelectDate"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$StudyTimeWeek = isset($_REQUEST["StudyTimeWeek"]) ? $_REQUEST["StudyTimeWeek"] : "";
$StudyTimeHour = isset($_REQUEST["StudyTimeHour"]) ? $_REQUEST["StudyTimeHour"] : "";
$StudyTimeMinute = isset($_REQUEST["StudyTimeMinute"]) ? $_REQUEST["StudyTimeMinute"] : "";
$IframeMode = isset($_REQUEST["IframeMode"]) ? $_REQUEST["IframeMode"] : "0";
//==== mini ====

$SelectYear = date("Y", strtotime($SelectDate));
$SelectMonth = date("m", strtotime($SelectDate));;
$SelectDay = date("d", strtotime($SelectDate));;

$SearchStartHour = $StudyTimeHour;
$SearchStartMinute = $StudyTimeMinute;
$SearchEndHour=23;
$SearchEndMinute=50;

$SearchTeacherID = $TeacherID;
$SelectDateWeek = date('w', strtotime($SelectDate));



$CurrentPage = 1; 
$PageListNum = 30;



$AddSqlWhere = $AddSqlWhere . " and MB.MemberState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and CT.CenterState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and BR.BranchState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and BRG.BranchGroupState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and COM.CompanyState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and FR.FranchiseState<>0 ";

if ($SearchTeacherID!=""){
	$AddSqlWhere = $AddSqlWhere . " and COS.TeacherID=".$SearchTeacherID." ";
}

//============================
$SearchStartHourMinute = substr("0".$SearchStartHour,-2) . substr("0".$SearchStartMinute,-2);
$AddSqlWhere = $AddSqlWhere . " and concat(lpad(COS.StudyTimeHour,2,0) , lpad(COS.StudyTimeMinute,2,0))=$SearchStartHourMinute ";

$SearchEndHourMinute = substr("0".$SearchEndHour,-2) . substr("0".$SearchEndMinute,-2);
$AddSqlWhere = $AddSqlWhere . " and concat(lpad(COS.StudyTimeHour,2,0) , lpad(COS.StudyTimeMinute,2,0))<=$SearchEndHourMinute ";
//============================



if ($SearchState!="1"){//2:미등록 3:강사취소 4:학생취소 //0:준비 1:출석 2:지각 3:결석 4:학생연기 5:교사연기
	if ($SearchState=="2"){
		$AddSqlWhere = $AddSqlWhere . " and CLS.ClassAttendState is NULL ";
	}else if ($SearchState=="9"){
		$AddSqlWhere = $AddSqlWhere . " and CLS.ClassAttendState is not NULL and ( CLS.ClassAttendState=4 or CLS.ClassAttendState=5 or CLS.ClassAttendState=6 or CLS.ClassAttendState=7 or CLS.ClassAttendState=8 ) ";
	}
}


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
		CLS.ClassRegDateTime,
		CLS.ClassModiDateTime,

		MB.MemberName,
		MB.MemberPayType,
		MB.MemberChangeTeacher,
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
		CT.MemberAcceptCallByTeacher,

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


        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div class="uk-overflow-container">
                            

							<table class="uk-table uk-table-align-vertical">
                                <thead>
                                    <tr>

                                        <th nowrap>
											<?=$대리점명[$LangID]?>
											<br>
											
											<?=$학생명[$LangID]?>
										
											<?if ($_LINK_ADMIN_LEVEL_ID_==15) {?>
											<br>
											<?=$학생명[$LangID]?>(Eng)
											<?}?>							
										</th>
										

										<?if ($_LINK_ADMIN_LEVEL_ID_!=15) {?>
										<th nowrap><?=$강사명[$LangID]?></th>
										<?}?>
                                       
										<th nowrap><?=$출결현황[$LangID]?></th>
										<th nowrap><?=$수업연기[$LangID]?></th>

										<?if ($_LINK_ADMIN_LEVEL_ID_<=4) {?>
										<th nowrap><?=$설정변경[$LangID]?></th>
										<th nowrap><?=$대체강사[$LangID]?></th>
										<?}?>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php
                                    $ListCount = 1;
                                    $CheckListCount = 1;
									
									$StrClassRegScript = "";
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
										

										$ClassMemberTypeGroupID = $Row["ClassMemberTypeGroupID"];
										$ClassOrderID = $Row["ClassOrderID"];
										$ClassProductID = $Row["ClassProductID"];
										$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
                                        
										$ClassID = $Row["ClassID"];
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
										}else if ($ClassOrderTimeTypeID==3){
											$StrClassOrderTimeTypeName = "30 min";
										}else if ($ClassOrderTimeTypeID==4){
											$StrClassOrderTimeTypeName = "40 min";
										}


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
											include('./class_list_mini_include.php');
											$CheckListCount ++;
											if ($LastClassNotAssmtCount>0){
											?>
												<script>
												//document.getElementById("TrClsEnter_<?=$TeacherID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>").innerHTML = "<?=$이전수업미평가[$LangID]?>";
												</script>
											<?
												$OpenClassCount = 0;
												$LastClassNotAssmtCount = 0;
											}else if ($OpenClassCount>0){
											?>
												<script>
												//document.getElementById("TrClsEnter_<?=$TeacherID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>").innerHTML = "<?=$StrClassEnterBtn?>";
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
												
												$ClassMemberTypeGroupID = $Row_G["ClassMemberTypeGroupID"];
												$ClassOrderID = $Row_G["ClassOrderID"];
												$ClassProductID = $Row_G["ClassProductID"];
												$ClassOrderTimeTypeID = $Row_G["ClassOrderTimeTypeID"];
												
												$ClassID = $Row_G["ClassID"];
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
												}else if ($ClassOrderTimeTypeID==3){
													$StrClassOrderTimeTypeName = "30 min";
												}else if ($ClassOrderTimeTypeID==4){
													$StrClassOrderTimeTypeName = "40 min";
												}


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


												include('./class_list_mini_include.php');
												$CheckListCount ++;
												$GroupListCount ++;
												
											
											}
											$Stmt_G = null;


											if ($LastClassNotAssmtCount>0){
											?>
												<script>
												//document.getElementById("TrClsEnter_<?=$TeacherID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>").innerHTML = "<?=$이전수업미평가[$LangID]?>";
												</script>
											<?
												$OpenClassCount = 0;
												$LastClassNotAssmtCount = 0;
											}else if ($OpenClassCount>0){
											?>
												<script>
												//document.getElementById("TrClsEnter_<?=$TeacherID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>").innerHTML = "<?=$StrClassEnterBtn?>";
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
                        
					
						<br><br><br>



                    </div>
                </div>
            </div>
        </div>

    </div>
</div>





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
function ResetClassOrder(ClassMemberTypeGroupID, ClassOrderID){

	var openurl = "class_order_reset_form.php?ClassMemberTypeGroupID="+ClassMemberTypeGroupID+"&ClassOrderID="+ClassOrderID+"&IframeMode=<?=$IframeMode?>";
	if(<?=$IframeMode?>==1) {
		window.open(openurl, "reset_class_order", "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1080,height=800");
	} else {
		$.colorbox({	
			href:openurl
			,width:"90%" 
			,height:"90%"
			,maxWidth: "800"
			,maxHeight: "900"
			//,maxWidth: "500"
			//,maxHeight: "400"
			,title:""
			,iframe:true 
			,scrolling:true
			//,onClosed:function(){  parent.$.colorbox.resize({width:"90%", height:"90%"}); }
			//,onClosed:function(){  parent.$.colorbox.resize({width:800, height:500}); }
			//,onComplete:function(){alert(1);}
		}); 
	}
}

function OpenClassReg(ClassOrderID, StartYear, StartMonth, StartDay, StartHour, StartMinute, ClassOrderTimeTypeID, TeacherID, MemberID, ClassMemberType, LastClassID, LastAssmtStudentDailyScoreID, RegType, GroupRowCount, ClassProductID, ClassOrderSlotType, StudyTimeWeek, ClassOrderSlotEndDate){//RegType 1:등록 2:등록전 연기 
	
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
			MemberID: MemberID
        },
        success: function (data) {

			ClassID = data.ClassID;
			CommonShClassCode = data.CommonShClassCode;
			TeacherName = data.TeacherName;
			TeacherLoginID = data.TeacherLoginID;

			<?if ($ListSelectResetDate!=""){?>
				ClassResetBtns = "<a class=\"tooltip md-btn md-btn-gray md-btn-mini md-btn-wave-light\" href=\"javascript:OpenResetDateForm("+ClassID+", "+GroupRowCount+", "+ClassMemberType+", "+StartHour+", "+StartMinute+");\"><?=$수업연기[$LangID] ? '연기 및 스케줄 변경' : ''?><span class=\"tooltiptext tooltip-top\">당일 수업만 맨 뒤나<br/>특정한 날짜로 연기됨</span></a><a class=\"tooltip md-btn md-btn-gray md-btn-mini md-btn-wave-light\" href=\"javascript:OpenResetDateForm2("+ClassID+","+ClassOrderID+","+MemberID+","+TeacherID+", "+GroupRowCount+", "+ClassMemberType+", "+StartHour+", "+StartMinute+");\" style=\"background-color:#f1f1f1;\"><?=$강사변경[$LangID]?><span class=\"tooltiptext tooltip-top\">강사만 바뀌고<br/>수업은 그대로임</span></a>";
			
				if (ClassProductID==1 && ClassOrderSlotType==1){
					ClassResetBtns = ClassResetBtns + "<br><a class=\"tooltip md-btn md-btn-gray md-btn-mini md-btn-wave-light\" href=\"javascript:OpenResetDateForm3("+ClassID+","+ClassOrderID+","+MemberID+","+TeacherID+", "+GroupRowCount+", "+ClassMemberType+", "+StartHour+", "+StartMinute+");\" style=\"margin-top:10px;background-color:#C7C7C7;\"><?=$보강등록[$LangID]?><span class=\"tooltiptext tooltip-bottom\">휴일이나 수업 없을 시<br/>보강을 할 수 있음</span></a><a class=\"tooltip md-btn md-btn-gray md-btn-mini md-btn-wave-light\" href=\"javascript:OpenResetDateForm4("+ClassID+","+ClassOrderID+","+MemberID+","+TeacherID+", "+GroupRowCount+", "+ClassMemberType+", "+StartYear+","+StartMonth+","+StartDay+", "+StartHour+", "+StartMinute+", "+StudyTimeWeek+", "+ClassOrderSlotEndDate+");\" style=\"margin-top:10px;background-color:#999999;color:#ffffff;\"><?=$스케줄변경[$LangID]?><span class=\"tooltiptext tooltip-bottom\">완전히 교사와<br/>시간을 바꿈</span></a>";
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


function OpenResetDateForm(ClassID, GroupRowCount, ClassMemberType, SetHour, SetMinute){//연기
	var OpenUrl = "../pop_class_reset_date_form.php?ClassID="+ClassID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&SetHour="+SetHour+"&SetMinute="+SetMinute+"&IframeMode=<?=$IframeMode?>&FromPage=Calendar";
	if(<?=$IframeMode?>==1) {
		window.open(OpenUrl, "reset_date", "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1080,height=800");
	} else {
		$.colorbox({	
			href:OpenUrl
			,width:"90%" 
			,height:"90%"
			,maxWidth: "800"
			,maxHeight: "900"
			//,maxWidth: "500"
			//,maxHeight: "400"
			,title:""
			,iframe:true 
			,scrolling:true
			,onClosed:function(){  parent.$.colorbox.resize({width:"90%", height:"90%"}); }
			//,onClosed:function(){  parent.$.colorbox.resize({width:800, height:500}); }
			//,onComplete:function(){alert(1);}
		}); 
	}
}


function OpenResetDateForm2(ClassID, ClassOrderID, MemberID, TeacherID, GroupRowCount, ClassMemberType, SetHour, SetMinute){//강사변경
	//var OpenUrl = "../pop_class_reset_date_form_teacher_select_form.php?ClassID="+ClassID+"&ClassOrderID="+ClassOrderID+"&MemberID="+MemberID+"&TeacherID="+TeacherID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&ResetType=ChTeacher&FromPage=Calendar";
	var OpenUrl = "../pop_class_reset_date_form_date_select_form.php?ClassID="+ClassID+"&ClassOrderID="+ClassOrderID+"&MemberID="+MemberID+"&TeacherID="+TeacherID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&SetHour="+SetHour+"&SetMinute="+SetMinute+"&IframeMode=<?=$IframeMode?>&ResetType=ChTeacher&FromPage=Calendar";
	if(<?=$IframeMode?>==1) {
		window.open(OpenUrl, "reset_date_2", "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1080,height=800");
	} else {
		parent.$.colorbox.resize({width:"95%", height:"95%"});
		//parent.$.colorbox.resize({width:800, height:900});

		$.colorbox({	
			href:OpenUrl
			,width:"95%" 
			,height:"95%"
			,maxWidth: "800"
			,maxHeight: "900"
			,title:""
			,iframe:true 
			,scrolling:true
			,onClosed:function(){  parent.$.colorbox.resize({width:"90%", height:"90%"}); }
			//,onClosed:function(){ parent.$.colorbox.resize({width:800, height:500}); }
			//,onComplete:function(){alert(1);}
		}); 
	}
}

function OpenResetDateForm3(ClassID, ClassOrderID, MemberID, TeacherID, GroupRowCount, ClassMemberType, SetHour, SetMinute){//보강
	var OpenUrl = "../pop_class_reset_date_form_teacher_select_form.php?ClassID="+ClassID+"&ClassOrderID="+ClassOrderID+"&MemberID="+MemberID+"&TeacherID="+TeacherID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&IframeMode=<?=$IframeMode?>&ResetType=PlusClass&FromPage=Calendar";
	//var OpenUrl = "../pop_class_reset_date_form_date_select_form.php?ClassID="+ClassID+"&ClassOrderID="+ClassOrderID+"&MemberID="+MemberID+"&TeacherID="+TeacherID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&SetHour="+SetHour+"&SetMinute="+SetMinute+"&ResetType=PlusClass&FromPage=Calendar";
	if(<?=$IframeMode?>==1) {
		window.open(OpenUrl, "reset_date_3", "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1080,height=800");
	} else {
		parent.$.colorbox.resize({width:"95%", height:"95%"});

		$.colorbox({	
			href:OpenUrl
			,width:"95%" 
			,height:"95%"
			,maxWidth: "800"
			,maxHeight: "900"
			,title:""
			,iframe:true 
			,scrolling:true
			,onClosed:function(){  parent.$.colorbox.resize({width:"90%", height:"90%"}); }
			//,onClosed:function(){ parent.$.colorbox.resize({width:800, height:500}); }
			//,onComplete:function(){alert(1);}
		}); 
	}
}

function OpenResetDateForm4(ClassID, ClassOrderID, MemberID, TeacherID, GroupRowCount, ClassMemberType, SetYear, SetMonth, SetDay, SetHour, SetMinute, SetWeek, ClassOrderSlotEndDate){//스케줄변경

	//if (confirm(SetYear+"<?=$년[$LangID]?>"+SetMonth+"<?=$월월[$LangID]?>"+SetDay+"<?=$일_강의부터_일정을_변경합니다[$LangID]?>")){
	
	if(ClassOrderSlotEndDate==null) {
		//var OpenUrl = "../pop_class_reset_date_form_teacher_select_form.php?ClassID="+ClassID+"&ClassOrderID="+ClassOrderID+"&MemberID="+MemberID+"&TeacherID="+TeacherID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&SetYear="+SetYear+"&SetMonth="+SetMonth+"&SetDay="+SetDay+"&SetHour="+SetHour+"&SetMinute="+SetMinute+"&SetWeek="+SetWeek+"&ResetType=EverChange&FromPage=Calendar";
		var OpenUrl = "../pop_class_reset_date_form_date_select_form.php?ClassID="+ClassID+"&ClassOrderID="+ClassOrderID+"&MemberID="+MemberID+"&TeacherID="+TeacherID+"&GroupRowCount="+GroupRowCount+"&ClassMemberType="+ClassMemberType+"&SetYear="+SetYear+"&SetMonth="+SetMonth+"&SetDay="+SetDay+"&SetHour="+SetHour+"&SetMinute="+SetMinute+"&SetWeek="+SetWeek+"&IframeMode=<?=$IframeMode?>&ResetType=EverChange&FromPage=Calendar";

		if(<?=$IframeMode?>==1) {
			window.open(OpenUrl, "reset_date_4", "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1080,height=800");
		} else {
			parent.$.colorbox.resize({width:"95%", height:"95%"});
			//parent.$.colorbox.resize({width:800, height:900});


			$.colorbox({	
				href:OpenUrl
				,width:"95%" 
				,height:"95%"
				,maxWidth: "800"
				,maxHeight: "900"
				,title:""
				,iframe:true 
				,scrolling:true
				//,onClosed:function(){ parent.$.colorbox.resize({width:800, height:500}); }
				,onClosed:function(){  parent.$.colorbox.resize({width:"90%", height:"90%"}); }
				//,onComplete:function(){alert(1);}
			}); 
		}
	} else {
		alert("<?=$스케줄변경을_하셨던_수업은[$LangID]?>\<?=$n다시_스케줄변경을_하실_수_없습니다[$LangID]?>");
	}
	//}
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
		if (confirm("연기의 경우 복원하더라도 신규로 연기되어 등록된 수업은 그대로 남아 있습니다. 수동으로 삭제해 주시기 바랍니다.\n\n마지막 수업 뒤로 연기한 수업의 경우 [B2B 결제]인 학생은 종료일을 조정해 주어야 합니다.")){
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
				parent.location.reload();
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
				parent.location.reload();
			},
			error: function () {
		
			}
		});
	}

}



//수업 자동등록(당일날 처음 열때 등록해 준다)
var MyInterval;
window.onload = function(){
	<?if (date("Y-m-d")==$SelectYear."-".substr("0".$SelectMonth,-2)."-".substr("0".$SelectDay,-2)  ||  $ListSelectResetDate=="1"){?>
		<?=$StrClassRegScript."\n"?>
	<?}?>
}
//수업 자동등록(당일날 처음 열때 등록해 준다)
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
function SearchSubmit(){
	document.SearchForm.action = "class_list_mini.php";
    document.SearchForm.submit();
}
</script>

<?php
//include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>
