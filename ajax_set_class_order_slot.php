<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');


$MemberID = $_LINK_MEMBER_ID_;

$ClassOrderWeekCountID = isset($_REQUEST["ClassOrderWeekCountID"]) ? $_REQUEST["ClassOrderWeekCountID"] : "";
$ClassOrderLeveltestApplyTypeID = isset($_REQUEST["ClassOrderLeveltestApplyTypeID"]) ? $_REQUEST["ClassOrderLeveltestApplyTypeID"] : "";
$ClassOrderTimeTypeID = isset($_REQUEST["ClassOrderTimeTypeID"]) ? $_REQUEST["ClassOrderTimeTypeID"] : "";
$ClassProductID = isset($_REQUEST["ClassProductID"]) ? $_REQUEST["ClassProductID"] : "";
$SelectSlotCode = isset($_REQUEST["SelectSlotCode"]) ? $_REQUEST["SelectSlotCode"] : "";
$SelectStudyTimeCode = isset($_REQUEST["SelectStudyTimeCode"]) ? $_REQUEST["SelectStudyTimeCode"] : "";//사용안함
$ClassOrderStartDate = isset($_REQUEST["ClassOrderStartDate"]) ? $_REQUEST["ClassOrderStartDate"] : "";
$ClassOrderRequestText = isset($_REQUEST["ClassOrderRequestText"]) ? $_REQUEST["ClassOrderRequestText"] : "";

$ClassOrderState = -1;//신청중
$ClassProgress = 11;//스케줄 완료
$ClassMemberType = 1;//1:1 수업

if ($ClassOrderLeveltestApplyTypeID==""){
	$ClassOrderLeveltestApplyTypeID = 1;
}


if ($ClassProductID==1){//일반수업
	$ClassOrderLeveltestApplyTypeID = 1;
}else if ($ClassProductID==2){//레벨테스트
	$ClassOrderTimeTypeID = 2;
	$ClassOrderWeekCountID = 1;
}else if ($ClassProductID==3){//체험수업
	$ClassOrderLeveltestApplyTypeID = 1;
	$ClassOrderTimeTypeID = 2;
	$ClassOrderWeekCountID = 1;
}



$Sql = " insert into ClassOrders ( ";
	$Sql .= " ClassProductID, ";
	$Sql .= " ClassOrderLeveltestApplyTypeID, ";
	$Sql .= " ClassOrderTimeTypeID, ";
	$Sql .= " ClassOrderWeekCountID, ";
	$Sql .= " MemberID, ";
	$Sql .= " ClassOrderRequestText, ";
	$Sql .= " ClassOrderStartDate, ";
	$Sql .= " ClassOrderState, ";
	$Sql .= " ClassMemberType, ";
	$Sql .= " ClassProgress, ";
	$Sql .= " ClassOrderRegDateTime, ";
	$Sql .= " ClassOrderModiDateTime ";
$Sql .= " ) values ( ";
	$Sql .= " :ClassProductID, ";
	$Sql .= " :ClassOrderLeveltestApplyTypeID, ";
	$Sql .= " :ClassOrderTimeTypeID, ";
	$Sql .= " :ClassOrderWeekCountID, ";
	$Sql .= " :MemberID, ";
	$Sql .= " :ClassOrderRequestText, ";
	$Sql .= " :ClassOrderStartDate, ";
	$Sql .= " :ClassOrderState, ";
	$Sql .= " :ClassMemberType, ";
	$Sql .= " :ClassProgress, ";
	$Sql .= " now(), ";
	$Sql .= " now() ";
$Sql .= " ) ";


$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassProductID', $ClassProductID);
$Stmt->bindParam(':ClassOrderLeveltestApplyTypeID', $ClassOrderLeveltestApplyTypeID);
$Stmt->bindParam(':ClassOrderTimeTypeID', $ClassOrderTimeTypeID);
$Stmt->bindParam(':ClassOrderWeekCountID', $ClassOrderWeekCountID);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':ClassOrderRequestText', $ClassOrderRequestText);
$Stmt->bindParam(':ClassOrderStartDate', $ClassOrderStartDate);
$Stmt->bindParam(':ClassOrderState', $ClassOrderState);
$Stmt->bindParam(':ClassMemberType', $ClassMemberType);
$Stmt->bindParam(':ClassProgress', $ClassProgress);
$Stmt->execute();
$ClassOrderID = $DbConn->lastInsertId();
$Stmt = null;




$ArrSelectSlotCode = explode("|",$SelectSlotCode);

$OldStudyTimeWeek = -1;
$OldTeacherID = -1;
for ($iiii=1;$iiii<=count($ArrSelectSlotCode)-1;$iiii++){//교사 수업 시간 기록
	
	$ArrArrSelectSlotCode  = explode("_",$ArrSelectSlotCode[$iiii]);
	
	$TeacherID = $ArrArrSelectSlotCode[0];
	$StudyTimeWeek = $ArrArrSelectSlotCode[1];
	$StudyTimeHour = $ArrArrSelectSlotCode[2];
	$StudyTimeMinute = $ArrArrSelectSlotCode[3];


	if ($ClassProductID==2 || $ClassProductID==3){
		$ClassOrderSlotType = 2;//임시
		$ClassOrderSlotDate = date('Y-m-d', strtotime($ClassOrderStartDate. ' +'.$StudyTimeWeek.' day'));
	}else{
		$ClassOrderSlotType = 1;//정규
	}

	if ($OldStudyTimeWeek != $StudyTimeWeek || $OldTeacherID != $TeacherID){
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

	$OldStudyTimeWeek = $StudyTimeWeek;
	$OldTeacherID = $TeacherID;


	$Sql = " insert into ClassOrderSlots ( ";
		$Sql .= " ClassOrderSlotGroupID, ";
		$Sql .= " ClassMemberType, ";
		$Sql .= " ClassOrderSlotType, ";
		if ($ClassProductID==2 || $ClassProductID==3){
			$Sql .= " ClassOrderSlotDate, ";
		}
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
		if ($ClassProductID==2 || $ClassProductID==3){
			$Sql .= " :ClassOrderSlotDate, ";
		}
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
	if ($ClassProductID==2 || $ClassProductID==3){
		$Stmt->bindParam(':ClassOrderSlotDate', $ClassOrderSlotDate);
	}
	$Stmt->bindParam(':TeacherID', $TeacherID);
	$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
	$Stmt->bindParam(':ClassOrderSlotMaster', $ClassOrderSlotMaster);
	$Stmt->bindParam(':StudyTimeWeek', $StudyTimeWeek);
	$Stmt->bindParam(':StudyTimeHour', $StudyTimeHour);
	$Stmt->bindParam(':StudyTimeMinute', $StudyTimeMinute);
	$Stmt->execute();
	$Stmt = null;

}



$ArrValue["ClassOrderID"] = $ClassOrderID;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>