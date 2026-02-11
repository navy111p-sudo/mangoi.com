<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";


$ClassProductID = 2;//레벨테스트
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$ClassOrderLeveltestApplyDate = isset($_REQUEST["ClassOrderLeveltestApplyDate"]) ? $_REQUEST["ClassOrderLeveltestApplyDate"] : "";
$LeveltestTimeWeek = isset($_REQUEST["LeveltestTimeWeek"]) ? $_REQUEST["LeveltestTimeWeek"] : "";
$LeveltestTimeHour = isset($_REQUEST["LeveltestTimeHour"]) ? $_REQUEST["LeveltestTimeHour"] : "";
$LeveltestTimeMinute = isset($_REQUEST["LeveltestTimeMinute"]) ? $_REQUEST["LeveltestTimeMinute"] : "";
$ClassOrderLeveltestApplyTypeID = isset($_REQUEST["ClassOrderLeveltestApplyTypeID"]) ? $_REQUEST["ClassOrderLeveltestApplyTypeID"] : "";
$ClassOrderLeveltestApplyLevel = isset($_REQUEST["ClassOrderLeveltestApplyLevel"]) ? $_REQUEST["ClassOrderLeveltestApplyLevel"] : "";
$ClassOrderLeveltestApplyOverseaTypeID = isset($_REQUEST["ClassOrderLeveltestApplyOverseaTypeID"]) ? $_REQUEST["ClassOrderLeveltestApplyOverseaTypeID"] : "";
$ClassOrderLeveltestApplyText = isset($_REQUEST["ClassOrderLeveltestApplyText"]) ? $_REQUEST["ClassOrderLeveltestApplyText"] : "";

$ClassMemberType = 1;
$ClassOrderTimeTypeID = 2;
$ClassOrderWeekCountID = 1;
$ClassOrderRequestText = "";
$ClassOrderState = 1;
$ClassProgress = 11;


$Sql = " insert into ClassOrders ( ";
	$Sql .= " ClassProductID, ";
	$Sql .= " ClassOrderLeveltestApplyTypeID, ";
	$Sql .= " ClassOrderLeveltestApplyLevel, ";
	$Sql .= " ClassOrderLeveltestApplyOverseaTypeID, ";
	$Sql .= " ClassOrderLeveltestApplyText, ";
	$Sql .= " ClassOrderTimeTypeID, ";
	$Sql .= " ClassOrderWeekCountID, ";
	$Sql .= " MemberID, ";
	$Sql .= " ClassOrderRequestText, ";
	$Sql .= " ClassOrderState, ";
	$Sql .= " ClassMemberType, ";
	$Sql .= " ClassProgress, ";
	$Sql .= " ClassOrderRegDateTime, ";
	$Sql .= " ClassOrderModiDateTime ";
$Sql .= " ) values ( ";
	$Sql .= " :ClassProductID, ";
	$Sql .= " :ClassOrderLeveltestApplyTypeID, ";
	$Sql .= " :ClassOrderLeveltestApplyLevel, ";
	$Sql .= " :ClassOrderLeveltestApplyOverseaTypeID, ";
	$Sql .= " :ClassOrderLeveltestApplyText, ";
	$Sql .= " :ClassOrderTimeTypeID, ";
	$Sql .= " :ClassOrderWeekCountID, ";
	$Sql .= " :MemberID, ";
	$Sql .= " :ClassOrderRequestText, ";
	$Sql .= " :ClassOrderState, ";
	$Sql .= " :ClassMemberType, ";
	$Sql .= " :ClassProgress, ";
	$Sql .= " now(), ";
	$Sql .= " now() ";
$Sql .= " ) ";


$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassProductID', $ClassProductID);
$Stmt->bindParam(':ClassOrderLeveltestApplyTypeID', $ClassOrderLeveltestApplyTypeID);	
$Stmt->bindParam(':ClassOrderLeveltestApplyLevel', $ClassOrderLeveltestApplyLevel);
$Stmt->bindParam(':ClassOrderLeveltestApplyOverseaTypeID', $ClassOrderLeveltestApplyOverseaTypeID);
$Stmt->bindParam(':ClassOrderLeveltestApplyText', $ClassOrderLeveltestApplyText);
$Stmt->bindParam(':ClassOrderTimeTypeID', $ClassOrderTimeTypeID);
$Stmt->bindParam(':ClassOrderWeekCountID', $ClassOrderWeekCountID);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':ClassOrderRequestText', $ClassOrderRequestText);
$Stmt->bindParam(':ClassOrderState', $ClassOrderState);
$Stmt->bindParam(':ClassMemberType', $ClassMemberType);
$Stmt->bindParam(':ClassProgress', $ClassProgress);

$Stmt->execute();
$ClassOrderID = $DbConn->lastInsertId();
$Stmt = null;


$ClassOrderSlotType = 2;

//레벨테스트 20분 슬랏 등록 (2개)
for ($iiii=1;$iiii<=2;$iiii++){

	$TempLeveltestTimeWeek = $LeveltestTimeWeek;
	$TempLeveltestTimeHour = $LeveltestTimeHour;
	$TempLeveltestTimeMinute = $LeveltestTimeMinute + ( ($iiii-1) * 10);
	
	if ($TempLeveltestTimeMinute>=60){
		$TempLeveltestTimeHour = $TempLeveltestTimeHour + 1;
		$TempLeveltestTimeMinute = $TempLeveltestTimeMinute - 60;
	}

	if ($TempLeveltestTimeHour>=24){

		if ($TempLeveltestTimeHour==24){
			$TempLeveltestTimeHour = 0;
		}else if ($TempLeveltestTimeHour==25){
			$TempLeveltestTimeHour = 1;
		}

		if ($TempLeveltestTimeWeek==6){
			$TempLeveltestTimeWeek = 0;
		}else{
			$TempLeveltestTimeWeek = $TempLeveltestTimeWeek + 1;
		}
	}

	if ($iiii==1){
		$ClassOrderSlotMaster = 1;

		$Sql = "select ifnull(Max(ClassOrderSlotGroupID),0) as ClassOrderSlotGroupID from ClassOrderSlots";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$ClassOrderSlotGroupID = $Row["ClassOrderSlotGroupID"]+1;
	}else{
		$ClassOrderSlotMaster = 0;
	}


	$Sql = " insert into ClassOrderSlots ( ";
		$Sql .= " ClassOrderSlotGroupID, ";
		$Sql .= " ClassMemberType, ";
		$Sql .= " ClassOrderSlotType, ";
		$Sql .= " ClassOrderSlotDate, ";
		$Sql .= " TeacherID, ";
		$Sql .= " ClassOrderID, ";
		$Sql .= " ClassOrderSlotMaster, ";
		$Sql .= " StudyTimeWeek, ";
		$Sql .= " StudyTimeHour, ";
		$Sql .= " StudyTimeMinute, ";
		$Sql .= " ClassOrderSlotState, ";
		$Sql .= " ClassOrderSlotRegDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :ClassOrderSlotGroupID, ";
		$Sql .= " :ClassMemberType, ";
		$Sql .= " :ClassOrderSlotType, ";
		$Sql .= " :ClassOrderSlotDate, ";
		$Sql .= " :TeacherID, ";
		$Sql .= " :ClassOrderID, ";
		$Sql .= " :ClassOrderSlotMaster, ";
		$Sql .= " :StudyTimeWeek, ";
		$Sql .= " :StudyTimeHour, ";
		$Sql .= " :StudyTimeMinute, ";
		$Sql .= " 1, ";
		$Sql .= " now() ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderSlotGroupID', $ClassOrderSlotGroupID);
	$Stmt->bindParam(':ClassMemberType', $ClassMemberType);
	$Stmt->bindParam(':ClassOrderSlotType', $ClassOrderSlotType);
	$Stmt->bindParam(':ClassOrderSlotDate', $ClassOrderLeveltestApplyDate);
	$Stmt->bindParam(':TeacherID', $TeacherID);
	$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
	$Stmt->bindParam(':ClassOrderSlotMaster', $ClassOrderSlotMaster);
	$Stmt->bindParam(':StudyTimeWeek', $TempLeveltestTimeWeek);
	$Stmt->bindParam(':StudyTimeHour', $TempLeveltestTimeHour);
	$Stmt->bindParam(':StudyTimeMinute', $TempLeveltestTimeMinute);
	$Stmt->execute();
	$Stmt = null;

}

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



if ($err_num == 0){
	// 레벨 테스트 신청 완료 알림톡 보내기
	$RowSms = GetMemberSmsInfo($MemberID);

	$MemberName = $RowSms["MemberName"];
	$DecMemberPhone1 = $RowSms["DecMemberPhone1"];
	$DecMemberPhone2 = $RowSms["DecMemberPhone2"];
		
	$msg = "레벨테스트 신청이 완료되었습니다. $ClassOrderLeveltestApplyDate 수업에 입장해주세요. 문의사항이 있으신 경우 망고아이 카카오톡으로 연락 부탁드립니다.";
		
	$tmplId="mangoi_002";  //카카오 알림톡 템플릿 아이디(문자 메시지를 바꿀려면 비즈엠에서 템플릿 등록하고 그 메시지를 위에 복사후 템플릿id도 바꿔야 함)

	if (!empty($DecMemberPhone1))
		SendAlimtalk($DecMemberPhone1, $msg,$tmplId);
	if (!empty($DecMemberPhone2))
		SendAlimtalk($DecMemberPhone2, $msg,$tmplId);

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
alert("레벨테스트 신청이 완료되었습니다.");
parent.location.href = "mypage.php";
</script>
</body>
</html>
<?
}

include_once('./includes/dbclose.php');
?>