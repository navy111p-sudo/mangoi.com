<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$ClassProductID = isset($_REQUEST["ClassProductID"]) ? $_REQUEST["ClassProductID"] : "";
$SelectSlotCode = isset($_REQUEST["SelectSlotCode"]) ? $_REQUEST["SelectSlotCode"] : "";//같은요일 같은강사 선택 가능으로 ----- 사용안함
$SelectStudyTimeCode = isset($_REQUEST["SelectStudyTimeCode"]) ? $_REQUEST["SelectStudyTimeCode"] : "";//같은요일 같은강사 선택 가능으로 이 값을 사용함
$ClassOrderStartDate = isset($_REQUEST["ClassOrderStartDate"]) ? $_REQUEST["ClassOrderStartDate"] : "";
$ClassOrderRealStartDate = isset($_REQUEST["ClassOrderRealStartDate"]) ? $_REQUEST["ClassOrderRealStartDate"] : "";//정규수업에서만 사용됨.
$ClassMemberType = isset($_REQUEST["ClassMemberType"]) ? $_REQUEST["ClassMemberType"] : "";

$ClassOrderTimeTypeID = isset($_REQUEST["ClassOrderTimeTypeID"]) ? $_REQUEST["ClassOrderTimeTypeID"] : "";

$ArrSelectStudyTimeCode = explode("|",$SelectStudyTimeCode);


$FixClassOrderStartDate = $ClassOrderStartDate;
if ($ClassProductID==2 || $ClassProductID==3){
	$DbClassOrderStartDate = $ClassOrderStartDate;
}else{
	$DbClassOrderStartDate = $ClassOrderRealStartDate;
}
for ($iiii=1;$iiii<=count($ArrSelectStudyTimeCode)-1;$iiii++){//교사 수업 시간 기록
	
	$ArrArrSelectStudyTimeCode  = explode("_",$ArrSelectStudyTimeCode[$iiii]);
	
	$TeacherID = $ArrArrSelectStudyTimeCode[0];
	$StudyTimeWeek = $ArrArrSelectStudyTimeCode[1];
	$StudyTimeHour = $ArrArrSelectStudyTimeCode[2];
	$StudyTimeMinute = $ArrArrSelectStudyTimeCode[3];


	if ($ClassProductID==2 || $ClassProductID==3){
		$ClassOrderSlotType = 2;//임시
		$ClassOrderSlotDate = date('Y-m-d', strtotime($FixClassOrderStartDate. ' +'.$StudyTimeWeek.' day'));
		$ClassOrderStartDate = $ClassOrderSlotDate;
	}else{
		$ClassOrderSlotType = 1;//정규
		$ClassOrderStartDate = $ClassOrderRealStartDate;
	}


	for ($iiiii=0;$iiiii<=$ClassOrderTimeTypeID-1;$iiiii++){

		if ($iiiii==0){
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



		$InStudyTimeHour = $StudyTimeHour;
		$InStudyTimeMinute = $StudyTimeMinute + ($iiiii*10);
		if ($InStudyTimeMinute>=60){
			$InStudyTimeHour = $InStudyTimeHour + 1;
			$InStudyTimeMinute = $InStudyTimeMinute -  60;
		}

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
		$Stmt->bindParam(':StudyTimeHour', $InStudyTimeHour);
		$Stmt->bindParam(':StudyTimeMinute', $InStudyTimeMinute);
		$Stmt->execute();
		$Stmt = null;
	
	}
}



$Sql = "update ClassOrders set 
			ClassProgress=11, 
			ClassOrderStartDate=:ClassOrderStartDate,
			ClassOrderModiDateTime=now() 
		where ClassOrderID=:ClassOrderID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderStartDate', $DbClassOrderStartDate);
$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
$Stmt->execute();
$Stmt = null;



$ArrValue["CheckResult"] = 1;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>