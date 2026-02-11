<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$err_num = 0;
$err_msg = "";


$MemberID = $_LINK_MEMBER_ID_;
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";

$AssmtStudentDailyScoreID = isset($_REQUEST["AssmtStudentDailyScoreID"]) ? $_REQUEST["AssmtStudentDailyScoreID"] : "";
$AssmtStudentDailyScore1 = isset($_REQUEST["AssmtStudentDailyScore1"]) ? $_REQUEST["AssmtStudentDailyScore1"] : "";
$AssmtStudentDailyScore2 = isset($_REQUEST["AssmtStudentDailyScore2"]) ? $_REQUEST["AssmtStudentDailyScore2"] : "";
$AssmtStudentDailyScore3 = isset($_REQUEST["AssmtStudentDailyScore3"]) ? $_REQUEST["AssmtStudentDailyScore3"] : "";
$AssmtStudentDailyScore4 = isset($_REQUEST["AssmtStudentDailyScore4"]) ? $_REQUEST["AssmtStudentDailyScore4"] : "";
$AssmtStudentDailyScore5 = isset($_REQUEST["AssmtStudentDailyScore5"]) ? $_REQUEST["AssmtStudentDailyScore5"] : "";
$AssmtStudentDailyComment = isset($_REQUEST["AssmtStudentDailyComment"]) ? $_REQUEST["AssmtStudentDailyComment"] : "";

if ($AssmtStudentDailyScoreID==""){

	$Sql = " insert into AssmtStudentDailyScores ( ";
		$Sql .= " ClassID, ";
		$Sql .= " MemberID, ";
		$Sql .= " AssmtStudentDailyScore1, ";
		$Sql .= " AssmtStudentDailyScore2, ";
		$Sql .= " AssmtStudentDailyScore3, ";
		$Sql .= " AssmtStudentDailyScore4, ";
		$Sql .= " AssmtStudentDailyScore5, ";
		$Sql .= " AssmtStudentDailyComment, ";
		$Sql .= " AssmtStudentDailyScoreRegDateTime, ";
		$Sql .= " AssmtStudentDailyScoreModiDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :ClassID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " :AssmtStudentDailyScore1, ";
		$Sql .= " :AssmtStudentDailyScore2, ";
		$Sql .= " :AssmtStudentDailyScore3, ";
		$Sql .= " :AssmtStudentDailyScore4, ";
		$Sql .= " :AssmtStudentDailyScore5, ";
		$Sql .= " :AssmtStudentDailyComment, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassID', $ClassID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':AssmtStudentDailyScore1', $AssmtStudentDailyScore1);
	$Stmt->bindParam(':AssmtStudentDailyScore2', $AssmtStudentDailyScore2);
	$Stmt->bindParam(':AssmtStudentDailyScore3', $AssmtStudentDailyScore3);
	$Stmt->bindParam(':AssmtStudentDailyScore4', $AssmtStudentDailyScore4);
	$Stmt->bindParam(':AssmtStudentDailyScore5', $AssmtStudentDailyScore5);
	$Stmt->bindParam(':AssmtStudentDailyComment', $AssmtStudentDailyComment);
	$Stmt->execute();
	$Stmt = null;


	$Sql = " update Classes set ";
		$Sql .= " ClassState=2 ";
	$Sql .= " where ClassID=:ClassID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassID', $ClassID);
	$Stmt->execute();
	$Stmt = null;

	// 수업 종료일이 7일전이면 안내 알림톡을 보낸다.
	$RowSms = GetMemberSmsInfo($MemberID);

	$MemberName = $RowSms["MemberName"];
	$DecMemberPhone1 = $RowSms["DecMemberPhone1"];
	$DecMemberPhone2 = $RowSms["DecMemberPhone2"];

	$Sql = "SELECT * FROM ClassOrders A INNER JOIN Classes B ON A.ClassOrderID = B.ClassOrderID 
				 WHERE A.MemberID=:MemberID AND B.ClassID=:ClassID AND A.ClassOrderEndDate <= date_add(NOW(),interval 7 day);";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':ClassID', $ClassID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	
	while($Row = $Stmt->fetch()){
		$ClassOrderEndDate = $Row["ClassOrderEndDate"];
				
		$msg = "$MemberName 님의 마지막 수업은 $ClassOrderEndDate 일입니다. 수업 연장을 원하시는 경우 마이페이지에서 결제 부탁드립니다";
				
		$tmplId="mangoi_004";  //카카오 알림톡 템플릿 아이디(문자 메시지를 바꿀려면 비즈엠에서 템플릿 등록하고 그 메시지를 위에 복사후 템플릿id도 바꿔야 함)
			
		if (!empty($DecMemberPhone1))
			SendAlimtalk($DecMemberPhone1, $msg,$tmplId);
		if (!empty($DecMemberPhone2))
			SendAlimtalk($DecMemberPhone2, $msg,$tmplId);
	}

}else{


	$Sql = " update AssmtStudentDailyScores set ";
		$Sql .= " AssmtStudentDailyScore1 = :AssmtStudentDailyScore1, ";
		$Sql .= " AssmtStudentDailyScore2 = :AssmtStudentDailyScore2, ";
		$Sql .= " AssmtStudentDailyScore3 = :AssmtStudentDailyScore3, ";
		$Sql .= " AssmtStudentDailyScore4 = :AssmtStudentDailyScore4, ";
		$Sql .= " AssmtStudentDailyScore5 = :AssmtStudentDailyScore5, ";
		$Sql .= " AssmtStudentDailyComment = :AssmtStudentDailyComment, ";
		$Sql .= " AssmtStudentDailyScoreModiDateTime = now() ";
	$Sql .= " where AssmtStudentDailyScoreID = :AssmtStudentDailyScoreID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':AssmtStudentDailyScore1', $AssmtStudentDailyScore1);
	$Stmt->bindParam(':AssmtStudentDailyScore2', $AssmtStudentDailyScore2);
	$Stmt->bindParam(':AssmtStudentDailyScore3', $AssmtStudentDailyScore3);
	$Stmt->bindParam(':AssmtStudentDailyScore4', $AssmtStudentDailyScore4);
	$Stmt->bindParam(':AssmtStudentDailyScore5', $AssmtStudentDailyScore5);
	$Stmt->bindParam(':AssmtStudentDailyComment', $AssmtStudentDailyComment);

	$Stmt->bindParam(':AssmtStudentDailyScoreID', $AssmtStudentDailyScoreID);
	$Stmt->execute();
	$Stmt = null;

}

$TeacherMemberLoginID = $LoginMemberID;

// 평가를 완료하고 나면 해당 수업 다음 수업을 미리 생성해 주고 비디오, 퀴즈, 북 등 교재를 자동으로 등록해 준다. 
// 등록되는 자료는 이번 수업의 다음 회차에 해당하는 자료를 가지고 온다.


$Sql = "SELECT * from Classes where ClassID = $ClassID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$MemberID = $Row["MemberID"];
$ClassOrderID = $Row["ClassOrderID"];
$StartDateTime = $Row["StartDateTime"];
$BookVideoID = $Row["BookVideoID"];
$BookQuizID = $Row["BookQuizID"];
$BookScanID = $Row["BookScanID"];



$Sql2 = "SELECT 
			A.TeacherPayPerTime,
			A.TeacherName,
			B.MemberLoginID,
			A.TeacherID
		from 
			Teachers A 
				inner join Members B on A.TeacherID=B.TeacherID and B.MemberLevelID=15 
		where MemberLoginID = '".$TeacherMemberLoginID."' ";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':TeacherID', $TeacherID);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
$Row2 = $Stmt2->fetch();
$Stmt = null;

$TeacherPayPerTime = $Row2["TeacherPayPerTime"];
$TeacherName = $Row2["TeacherName"];
$TeacherLoginID = $Row2["MemberLoginID"];
$TeacherID = $Row2["TeacherID"];

$SelectDateWeek = date('w', strtotime($StartDateTime));

//슬랏에서 같은 선생님의 다음 수업이 어떤 슬랏인지 가져온다.
//지금 수업 요일 - studytimeweek 해서 큰수가 가장 가까운 요일이다. (0은 제외)

$Sql = "SELECT C.*, K.ClassOrderTimeTypeID, (DAYOFWEEK('".$StartDateTime."')-1 - C.StudyTimeWeek) as distance 
			from ClassOrderSlots C  
			left join classorders K on C.ClassOrderID = K.ClassOrderID
			where C.ClassOrderID = $ClassOrderID
				and C.TeacherID = $TeacherID
				and C.ClassOrderSlotMaster = 1 
				and C.ClassOrderSlotState = 1 
				and C.ClassOrderSlotType = 1   
				and ( 
					(C.ClassOrderSlotType=1 and C.ClassOrderSlotStartDate is NULL and C.ClassOrderSlotEndDate is NULL and datediff(K.ClassOrderStartDate, '".$StartDateTime."')<=0 ) 
					or 
					(C.ClassOrderSlotType=1 and datediff(C.ClassOrderSlotStartDate, '".$StartDateTime."')<=0 and C.ClassOrderSlotEndDate is NULL and datediff(K.ClassOrderStartDate, '".$StartDateTime."')<=0 ) 
					or 
					(C.ClassOrderSlotType=1 and C.ClassOrderSlotStartDate is NULL and datediff(C.ClassOrderSlotEndDate, '".$StartDateTime."')>=0 and datediff(K.ClassOrderStartDate, '".$StartDateTime."')<=0 ) 
					or 
					(C.ClassOrderSlotType=1 and datediff(C.ClassOrderSlotStartDate, '".$StartDateTime."')<=0 and datediff(C.ClassOrderSlotEndDate, '".$StartDateTime."')>=0 and datediff(K.ClassOrderStartDate, '".$StartDateTime."')<=0 ) 
					or 
					(C.ClassOrderSlotType=2 and datediff(C.ClassOrderSlotDate, '".$StartDateTime."')=0 )   
				)   
				order by C.StudyTimeWeek, C.StudyTimeHour 
			";
		
$Stmt = $DbConn->prepare($Sql);
//echo $Sql;
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$distance = 0;

$i=0;
while($Row = $Stmt->fetch()){
	//차이가 0보다 작으면 1순위 0보다 크면 2순위
	if ($Row["distance"] < 0){
		if ($distance > $Row["distance"]) {
			$distance = $Row["distance"];
			$StudyTimeWeek = $Row["StudyTimeWeek"];
			$StartHour = $Row["StudyTimeHour"];
			$StartMinute = $Row["StudyTimeMinute"];
			$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
		
		}
	} else if ($Row["distance"] > 0){
		if ($distance < $Row["distance"]) {
			$distance = $Row["distance"];
			$StudyTimeWeek = $Row["StudyTimeWeek"];
			$StartHour = $Row["StudyTimeHour"];
			$StartMinute = $Row["StudyTimeMinute"];
			$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
		} 
	} 
	
	if ($i==0 && $Row["distance"]==0) {
		$distance = $Row["distance"];
		$StudyTimeWeek = $Row["StudyTimeWeek"];
		$StartHour = $Row["StudyTimeHour"];
		$StartMinute = $Row["StudyTimeMinute"];
		$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
	}

	$i++;


}


if (date('w', strtotime($StartDateTime))==$StudyTimeWeek){
	$nextDateTime = date("Y-m-d",strtotime($StartDateTime.'+1 week'));
} else {
	$addDay = $StudyTimeWeek - date('w', strtotime($StartDateTime));
	// 위 값이 음수이면 한 주가 추가된다.
	if ($addDay < 0) {
		$nextDateTime = date("Y-m-d",strtotime($StartDateTime.'+1 week'));
	}
	$nextDateTime = date("Y-m-d",strtotime($nextDateTime.'+'.$addDay.' day'));
}
$StartYear = substr($nextDateTime,0,4);
$StartMonth = substr($nextDateTime,5,2);
$StartDay = substr($nextDateTime,8,2);


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


// 먼저 해당 수업이 이미 등록되어 있는지 확인한다.
$Sql2 = "SELECT
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
		and ClassAttendState<>99 ";


$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':TeacherID', $TeacherID);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
$Row2 = $Stmt2->fetch();
$Stmt2 = null;
$ClassID = $Row2["ClassID"];
$CommonShClassCode = $Row2["CommonShClassCode"];

// 있으면 그냥 넘어가고 없으면 새로 등록한다.
if (!$ClassID){
	$StartDate = $StartYear."-".substr("0".$StartMonth,-2)."-".substr("0".$StartDay,-2);
	$CommonShClassCode = $TeacherID."_". str_replace("-","",$StartDate) ."_".$StartHour."_".$StartMinute;
	$StartDateTime = $StartYear."-".substr("0".$StartMonth,-2)."-".substr("0".$StartDay,-2)." ".substr("0".$StartHour,-2).":".substr("0".$StartMinute,-2).":00";
	$EndDateTime   = $EndYear.  "-".substr("0".$EndMonth,  -2)."-".substr("0".$EndDay,-2  )." ".substr("0".$EndHour,-2  ).":".substr("0".$EndMinute,-2  ).":00";
	$StartDateTimeStamp = strtotime($StartDateTime);
	$EndDateTimeStamp =   strtotime($EndDateTime);
	
		$Sql2 = "SELECT 
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

	
	// 이전 수업의 비디오, 퀴즈, 책의 다음화를 가지고 온다.
	$Sql2 = "SELECT 
				A.BookScanID 
			from BookScans A WHERE A.BookScanState = 1 
			AND A.BookScanOrder = (SELECT B.BookScanOrder+1 From BookScans B WHERE B.BookScanID = :BookScanID)";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':BookScanID', $BookScanID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	
	
	if ($Row2["BookScanID"]=="") $NewBookScanID = 1;
		else $NewBookScanID = $Row2["BookScanID"];


	$Sql2 = "SELECT 
				A.BookVideoID 
			from BookVideos A WHERE A.BookVideoState = 1 
			AND A.BookVideoOrder = (SELECT B.BookVideoOrder+1 From BookVideos B WHERE B.BookVideoID = :BookVideoID)";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':BookVideoID', $BookVideoID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	
	if ($Row2["BookVideoID"]=="") $NewBookVideoID = 1;
		else $NewBookVideoID = $Row2["BookVideoID"];

	$Sql2 = "SELECT 
				A.BookQuizID 
			from BookQuizs A WHERE A.BookQuizState = 1 
			AND A.BookQuizOrder = (SELECT B.BookQuizOrder+1 From BookQuizs B WHERE B.BookQuizID = :BookQuizID)";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':BookQuizID', $BookQuizID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	
	if ($Row2["BookQuizID"]=="") $NewBookQuizID = 1;
		else $NewBookQuizID = $Row2["BookQuizID"];




	// 강의를 입력한다.
	$Sql2 = " INSERT into Classes ( ";
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
		$Sql2 .= " BookVideoID, ";
		$Sql2 .= " BookQuizID, ";
		$Sql2 .= " BookScanID, ";
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
		$Sql2 .= " :BookVideoID, ";
		$Sql2 .= " :BookQuizID, ";
		$Sql2 .= " :BookScanID, ";
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
	$Stmt2->bindParam(':BookVideoID', $NewBookVideoID);
	$Stmt2->bindParam(':BookQuizID', $NewBookQuizID);
	$Stmt2->bindParam(':BookScanID', $NewBookScanID);
	$Stmt2->execute();
	$ClassID = $DbConn->lastInsertId();
	$Stmt2 = null;

	//echo $ClassID."losthero" ;

}




/*

if ($err_num != 0){
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
</body>
</html>
<?php
}

include_once('./includes/dbclose.php');


if ($err_num == 0){
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>
<script>
<?if ($FromDevice==""){?>
parent.location.reload();
//parent.$.fn.colorbox.close();
<?}else{?>
window.Exit=true;
<?}?>
</script>
</body>
</html>
<?
}
*/
?>