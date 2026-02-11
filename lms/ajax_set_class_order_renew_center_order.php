<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ClassOrderPayYear = isset($_REQUEST["ClassOrderPayYear"]) ? $_REQUEST["ClassOrderPayYear"] : "";
$ClassOrderPayMonth = isset($_REQUEST["ClassOrderPayMonth"]) ? $_REQUEST["ClassOrderPayMonth"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";

$DefaultCenterPricePerTime = isset($_REQUEST["DefaultCenterPricePerTime"]) ? $_REQUEST["DefaultCenterPricePerTime"] : "";
$DefaultCompanyPricePerTime = isset($_REQUEST["DefaultCompanyPricePerTime"]) ? $_REQUEST["DefaultCompanyPricePerTime"] : "";

$ClassOrderPayUseSavedMoneyPrice = isset($_REQUEST["ClassOrderPayUseSavedMoneyPrice"]) ? $_REQUEST["ClassOrderPayUseSavedMoneyPrice"] : "";
$ClassOrderPayMonthNumberID = isset($_REQUEST["ClassOrderPayMonthNumberID"]) ? $_REQUEST["ClassOrderPayMonthNumberID"] : "";//받지 않음
$ClassOrderPayB2bDifferencePrice = isset($_REQUEST["ClassOrderPayB2bDifferencePrice"]) ? $_REQUEST["ClassOrderPayB2bDifferencePrice"] : "";

$ClassOrderPayPaymentMemberID = $_LINK_ADMIN_ID_;

// 선행 미결제 월이 있으면 해당 월부터 처리하도록 강제(서버에서 차단)
$RequestedYear = (int)$ClassOrderPayYear;
$RequestedMonth = (int)$ClassOrderPayMonth;
$RequestedYearMonthNum = sprintf("%04d%02d", $RequestedYear, $RequestedMonth);

if ($CenterID != "" && $RequestedYear > 0 && $RequestedMonth >= 1 && $RequestedMonth <= 12) {
	$Sql = "select CenterRenewStartYearMonthNum from Centers where CenterID=:CenterID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$CenterRenewStartYearMonthNum = isset($Row["CenterRenewStartYearMonthNum"]) ? $Row["CenterRenewStartYearMonthNum"] : "";
	if (!preg_match('/^[0-9]{6}$/', $CenterRenewStartYearMonthNum)) {
		$CenterRenewStartYearMonthNum = $RequestedYearMonthNum;
	}

	$StartYear = (int)substr($CenterRenewStartYearMonthNum, 0, 4);
	$StartMonth = (int)substr($CenterRenewStartYearMonthNum, 4, 2);
	$StartYearMonthNum = sprintf("%04d%02d", $StartYear, $StartMonth);

	// 미결제 체크 시작월 하한: 2025-01부터만 체크(레거시 과거월은 미결제라도 차단하지 않음)
	$MinEnforceYearMonthNum = "202501";
	if ((int)$StartYearMonthNum < (int)$MinEnforceYearMonthNum) {
		$StartYearMonthNum = $MinEnforceYearMonthNum;
		$StartYear = (int)substr($StartYearMonthNum, 0, 4);
		$StartMonth = (int)substr($StartYearMonthNum, 4, 2);
	}

	// 시작월이 요청월보다 뒤로 잡혀있으면(비정상 값) 요청월 기준으로만 진행
	if ((int)$StartYearMonthNum > (int)$RequestedYearMonthNum) {
		$StartYearMonthNum = $RequestedYearMonthNum;
		$StartYear = (int)substr($StartYearMonthNum, 0, 4);
		$StartMonth = (int)substr($StartYearMonthNum, 4, 2);
	}

	$Sql = "
		select 
			concat(A.ClassOrderPayYear, lpad(A.ClassOrderPayMonth,2,'0')) as PaidYearMonthNum
		from ClassOrderPayB2bs A
			inner join ClassOrderPays B on A.ClassOrderPayID=B.ClassOrderPayID
		where A.CenterID=:CenterID
			and A.ClassOrderPayB2bState=1
			and (B.ClassOrderPayProgress=21 or B.ClassOrderPayProgress=31 or B.ClassOrderPayProgress=41)
			and concat(A.ClassOrderPayYear, lpad(A.ClassOrderPayMonth,2,'0')) >= :StartYearMonthNum
			and concat(A.ClassOrderPayYear, lpad(A.ClassOrderPayMonth,2,'0')) <= :EndYearMonthNum
		group by PaidYearMonthNum
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':StartYearMonthNum', $StartYearMonthNum);
	$Stmt->bindParam(':EndYearMonthNum', $RequestedYearMonthNum);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	$PaidYearMonthSet = array();
	while ($Row = $Stmt->fetch()) {
		$PaidYearMonthNum = $Row["PaidYearMonthNum"];
		if ($PaidYearMonthNum != "") {
			$PaidYearMonthSet[$PaidYearMonthNum] = 1;
		}
	}
	$Stmt = null;

	$StartDate = DateTimeImmutable::createFromFormat('Y-m-d', sprintf('%04d-%02d-01', $StartYear, $StartMonth));
	$EndDate = DateTimeImmutable::createFromFormat('Y-m-d', sprintf('%04d-%02d-01', $RequestedYear, $RequestedMonth));

	if ($StartDate && $EndDate) {
		$MonthDiff = (($RequestedYear - $StartYear) * 12) + ($RequestedMonth - $StartMonth);
		if ($MonthDiff < 0) $MonthDiff = 0;

		// 비정상 값으로 과도한 루프 방지(최대 20년)
		if ($MonthDiff > 240) $MonthDiff = 240;

		$FirstUnpaidYearMonthNum = "";
		$CursorDate = $StartDate;
		for ($i = 0; $i <= $MonthDiff; $i++) {
			$Ym = $CursorDate->format('Ym');
			if (!isset($PaidYearMonthSet[$Ym])) {
				$FirstUnpaidYearMonthNum = $Ym;
				break;
			}
			$CursorDate = $CursorDate->modify('+1 month');
		}

		if ($FirstUnpaidYearMonthNum != "" && $FirstUnpaidYearMonthNum != $RequestedYearMonthNum) {
			$RequiredYear = (int)substr($FirstUnpaidYearMonthNum, 0, 4);
			$RequiredMonth = (int)substr($FirstUnpaidYearMonthNum, 4, 2);

			$ArrValue = array();
			$ArrValue["ErrNum"] = 1;
			$ArrValue["ErrMsg"] = $RequiredYear . "년 " . $RequiredMonth . "월 결제가 진행되지 않았습니다. 해당 결제를 먼저 진행해주세요.";
			$ArrValue["RequiredPayYear"] = $RequiredYear;
			$ArrValue["RequiredPayMonth"] = $RequiredMonth;
			$ArrValue["RequiredPayYearMonthNum"] = $FirstUnpaidYearMonthNum;
			echo my_json_encode($ArrValue);
			include_once('../includes/dbclose.php');
			exit;
		}
	}
}

if ($ClassOrderPayUseSavedMoneyPrice==""){
	$ClassOrderPayUseSavedMoneyPrice = 0;//적립금 사용
}
 

$ClassOrderPayNumber = "ML".date("YmdHis").substr("0000000000".$_LINK_ADMIN_ID_,-10); // ML -> Mangoi Lms

//=========================================================================================
$Sql = " insert into ClassOrderPays ( ";
	$Sql .= " ClassOrderPayNumber, ";
	$Sql .= " ClassOrderPayType, ";
	$Sql .= " ClassOrderPayPaymentMemberID, ";
	$Sql .= " ClassOrderPaySellingPrice, ";
	$Sql .= " ClassOrderPayDiscountPrice, ";
	$Sql .= " ClassOrderPayPaymentPrice, ";
	$Sql .= " ClassOrderPayUseSavedMoneyPrice, ";
	$Sql .= " ClassOrderPayUseCashPrice, ";
	$Sql .= " ClassOrderPayUseCashPaymentType, ";
	$Sql .= " ClassOrderPayB2bDifferencePrice, ";
	$Sql .= " ClassOrderPayPgFeeRatio, ";
	$Sql .= " ClassOrderPayPgFeePrice, ";
	$Sql .= " ClassOrderPayProgress, ";
	$Sql .= " ClassOrderPayRegDateTime, ";
	$Sql .= " ClassOrderPayModiDateTime ";
$Sql .= " ) values ( ";
	$Sql .= " :ClassOrderPayNumber, "; 
	$Sql .= " 1, ";//B2B
	$Sql .= " :ClassOrderPayPaymentMemberID, ";
	$Sql .= " 0, ";
	$Sql .= " 0, ";
	$Sql .= " 0, ";
	$Sql .= " :ClassOrderPayUseSavedMoneyPrice, ";
	$Sql .= " 0, ";
	$Sql .= " 1, ";
	$Sql .= " :ClassOrderPayB2bDifferencePrice, ";
	$Sql .= " 0, ";
	$Sql .= " 0, ";
	$Sql .= " 1, ";//DB등록, 주문상태는 건너뛴다
	$Sql .= " now(), ";
	$Sql .= " now() ";
$Sql .= " ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderPayNumber', $ClassOrderPayNumber);
$Stmt->bindParam(':ClassOrderPayPaymentMemberID', $ClassOrderPayPaymentMemberID);
$Stmt->bindParam(':ClassOrderPayB2bDifferencePrice', $ClassOrderPayB2bDifferencePrice);
$Stmt->bindParam(':ClassOrderPayUseSavedMoneyPrice', $ClassOrderPayUseSavedMoneyPrice);
$Stmt->execute();
$ClassOrderPayID = $DbConn->lastInsertId();
$Stmt = null;
//=========================================================================================


//=========================================================================================



$CheckBoxNums = isset($_REQUEST["CheckBoxNums"]) ? $_REQUEST["CheckBoxNums"] : "";
$ArrCheckBoxNums = explode("||", $CheckBoxNums);

$SumClassOrderPayDetailPaymentPrice = 0;
$SumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice = 0;

for ($ii=1;$ii<=count($ArrCheckBoxNums)-2;$ii++){

	$ListPayCount = $ArrCheckBoxNums[$ii];

	$ClassOrderIDs = isset($_REQUEST["CheckBox_".$ListPayCount]) ? $_REQUEST["CheckBox_".$ListPayCount] : "";


	$ArrClassOrderID = explode("|", $ClassOrderIDs);
	$ClassMemberType = $ArrClassOrderID[0];
	if ($ClassMemberType=="3"){
		$ClassOrderID = 0;
		$ClassMemberTypeGroupID = $ArrClassOrderID[1];
	}else{
		$ClassMemberType = 1;//ClassMemberType : 2는 개별결제임으로 1로 취급 =========================
		$ClassOrderID = $ArrClassOrderID[1];
		$ClassMemberTypeGroupID = 0;
	}
	
	$ClassOrderPayLogStartDate = isset($_REQUEST["ClassOrderPayLogStartDate_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogStartDate_".$ListPayCount] : "";
	$ClassOrderPayLogEndDate = isset($_REQUEST["ClassOrderPayLogEndDate_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogEndDate_".$ListPayCount] : "";
	$ClassOrderPayLogState = isset($_REQUEST["ClassOrderPayLogState_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogState_".$ListPayCount] : "";
	$ClassOrderPayLogWeekCount = isset($_REQUEST["ClassOrderPayLogWeekCount_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogWeekCount_".$ListPayCount] : "";
	$ClassOrderPayLogTeacherListInfo = isset($_REQUEST["ClassOrderPayLogTeacherListInfo_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogTeacherListInfo_".$ListPayCount] : "";

	$ClassOrderPayLogClassMemberType = isset($_REQUEST["ClassOrderPayLogClassMemberType_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogClassMemberType_".$ListPayCount] : "";
	$ClassOrderPayLogPrevPrevMonthPaidClassCount = isset($_REQUEST["ClassOrderPayLogPrevPrevMonthPaidClassCount_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogPrevPrevMonthPaidClassCount_".$ListPayCount] : "";
	$ClassOrderPayLogPrevPrevMonthPaidMoney = isset($_REQUEST["ClassOrderPayLogPrevPrevMonthPaidMoney_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogPrevPrevMonthPaidMoney_".$ListPayCount] : "";
	$ClassOrderPayLogPrevMonthEndClassCount = isset($_REQUEST["ClassOrderPayLogPrevMonthEndClassCount_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogPrevMonthEndClassCount_".$ListPayCount] : "";
	$ClassOrderPayLogPrevMonthUsedMoney = isset($_REQUEST["ClassOrderPayLogPrevMonthUsedMoney_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogPrevMonthUsedMoney_".$ListPayCount] : "";
	$ClassOrderPayLogDifferencePrevPrevMonthPaidMoney = isset($_REQUEST["ClassOrderPayLogDifferencePrevPrevMonthPaidMoney_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogDifferencePrevPrevMonthPaidMoney_".$ListPayCount] : "";
	$ClassOrderPayLogNextMonthClassCountInfo = isset($_REQUEST["ClassOrderPayLogNextMonthClassCountInfo_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogNextMonthClassCountInfo_".$ListPayCount] : "";
	$ClassOrderPayLogNextMonthPayMoney = isset($_REQUEST["ClassOrderPayLogNextMonthPayMoney_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogNextMonthPayMoney_".$ListPayCount] : "";
	$ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice = isset($_REQUEST["ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice_".$ListPayCount] : "";
	$ClassOrderPayLogDifferenceNextMonthPayMoney = isset($_REQUEST["ClassOrderPayLogDifferenceNextMonthPayMoney_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogDifferenceNextMonthPayMoney_".$ListPayCount] : "";
	
	$SumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice = $SumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice + $ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice;
	$SumClassOrderPayDetailPaymentPrice = $SumClassOrderPayDetailPaymentPrice + $ClassOrderPayLogDifferenceNextMonthPayMoney;


	$Sql2 = " insert into ClassOrderPayB2bs ( ";
		$Sql2 .= " ClassOrderPayYear, ";
		$Sql2 .= " ClassOrderPayMonth, ";
		$Sql2 .= " CenterID, ";
		$Sql2 .= " ClassOrderPayID, ";
		$Sql2 .= " ClassMemberType, ";
		$Sql2 .= " ClassOrderID, ";
		$Sql2 .= " ClassMemberTypeGroupID, ";
		$Sql2 .= " ClassOrderPayLogStartDate, ";
		$Sql2 .= " ClassOrderPayLogEndDate, ";
		$Sql2 .= " ClassOrderPayLogState, ";
		$Sql2 .= " ClassOrderPayLogWeekCount, ";
		$Sql2 .= " ClassOrderPayLogTeacherListInfo, ";
		$Sql2 .= " ClassOrderPayLogClassMemberType, ";
		$Sql2 .= " ClassOrderPayLogPrevPrevMonthPaidClassCount, ";
		$Sql2 .= " ClassOrderPayLogPrevPrevMonthPaidMoney, ";
		$Sql2 .= " ClassOrderPayLogPrevMonthEndClassCount, ";
		$Sql2 .= " ClassOrderPayLogPrevMonthUsedMoney, ";
		$Sql2 .= " ClassOrderPayLogDifferencePrevPrevMonthPaidMoney, ";
		$Sql2 .= " ClassOrderPayLogNextMonthClassCountInfo, ";
		$Sql2 .= " ClassOrderPayLogNextMonthPayMoney, ";
		$Sql2 .= " ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice, ";
		$Sql2 .= " ClassOrderPayLogDifferenceNextMonthPayMoney, ";
		$Sql2 .= " ClassOrderPayB2bRegDateTime, ";
		$Sql2 .= " ClassOrderPayB2bModiDateTime, ";
		$Sql2 .= " ClassOrderPayB2bState ";
	$Sql2 .= " ) values ( ";
		$Sql2 .= " :ClassOrderPayYear, "; 
		$Sql2 .= " :ClassOrderPayMonth, ";
		$Sql2 .= " :CenterID, ";
		$Sql2 .= " :ClassOrderPayID, ";
		$Sql2 .= " :ClassMemberType, ";
		$Sql2 .= " :ClassOrderID, ";
		$Sql2 .= " :ClassMemberTypeGroupID, ";
		$Sql2 .= " :ClassOrderPayLogStartDate, ";
		$Sql2 .= " :ClassOrderPayLogEndDate, ";
		$Sql2 .= " :ClassOrderPayLogState, ";
		$Sql2 .= " :ClassOrderPayLogWeekCount, ";
		$Sql2 .= " :ClassOrderPayLogTeacherListInfo, ";
		$Sql2 .= " :ClassOrderPayLogClassMemberType, ";
		$Sql2 .= " :ClassOrderPayLogPrevPrevMonthPaidClassCount, ";
		$Sql2 .= " :ClassOrderPayLogPrevPrevMonthPaidMoney, ";
		$Sql2 .= " :ClassOrderPayLogPrevMonthEndClassCount, ";//1:신규 2:연장
		$Sql2 .= " :ClassOrderPayLogPrevMonthUsedMoney, ";
		$Sql2 .= " :ClassOrderPayLogDifferencePrevPrevMonthPaidMoney, ";
		$Sql2 .= " :ClassOrderPayLogNextMonthClassCountInfo, ";
		$Sql2 .= " :ClassOrderPayLogNextMonthPayMoney, ";
		$Sql2 .= " :ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice, ";
		$Sql2 .= " :ClassOrderPayLogDifferenceNextMonthPayMoney, ";
		$Sql2 .= " now(), ";
		$Sql2 .= " now(), ";
		$Sql2 .= " 0 ";
	$Sql2 .= " ) ";

	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':ClassOrderPayYear', $ClassOrderPayYear);
	$Stmt2->bindParam(':ClassOrderPayMonth', $ClassOrderPayMonth);
	$Stmt2->bindParam(':CenterID', $CenterID);
	$Stmt2->bindParam(':ClassOrderPayID', $ClassOrderPayID);
	$Stmt2->bindParam(':ClassMemberType', $ClassMemberType);
	$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
	$Stmt2->bindParam(':ClassMemberTypeGroupID', $ClassMemberTypeGroupID);
	$Stmt2->bindParam(':ClassOrderPayLogStartDate', $ClassOrderPayLogStartDate);
	$Stmt2->bindParam(':ClassOrderPayLogEndDate', $ClassOrderPayLogEndDate);
	$Stmt2->bindParam(':ClassOrderPayLogState', $ClassOrderPayLogState);
	$Stmt2->bindParam(':ClassOrderPayLogWeekCount', $ClassOrderPayLogWeekCount);
	$Stmt2->bindParam(':ClassOrderPayLogTeacherListInfo', $ClassOrderPayLogTeacherListInfo);
	$Stmt2->bindParam(':ClassOrderPayLogClassMemberType', $ClassOrderPayLogClassMemberType);
	$Stmt2->bindParam(':ClassOrderPayLogPrevPrevMonthPaidClassCount', $ClassOrderPayLogPrevPrevMonthPaidClassCount);
	$Stmt2->bindParam(':ClassOrderPayLogPrevPrevMonthPaidMoney', $ClassOrderPayLogPrevPrevMonthPaidMoney);
	$Stmt2->bindParam(':ClassOrderPayLogPrevMonthEndClassCount', $ClassOrderPayLogPrevMonthEndClassCount);
	$Stmt2->bindParam(':ClassOrderPayLogPrevMonthUsedMoney', $ClassOrderPayLogPrevMonthUsedMoney);
	$Stmt2->bindParam(':ClassOrderPayLogDifferencePrevPrevMonthPaidMoney', $ClassOrderPayLogDifferencePrevPrevMonthPaidMoney);
	$Stmt2->bindParam(':ClassOrderPayLogNextMonthClassCountInfo', $ClassOrderPayLogNextMonthClassCountInfo);
	$Stmt2->bindParam(':ClassOrderPayLogNextMonthPayMoney', $ClassOrderPayLogNextMonthPayMoney);
	$Stmt2->bindParam(':ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice', $ClassOrderPayLogClassOrderPayFreeTrialDiscountPrice);
	$Stmt2->bindParam(':ClassOrderPayLogDifferenceNextMonthPayMoney', $ClassOrderPayLogDifferenceNextMonthPayMoney);
	$Stmt2->execute();
	$ClassOrderPayB2bID = $DbConn->lastInsertId();
	$Stmt2 = null;

	

	$ClassOrderPayLogTeacherIDs = isset($_REQUEST["ClassOrderPayLogTeacherIDs_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogTeacherIDs_".$ListPayCount] : "";
	$ClassOrderPayLogTeacherPayTypeItemCenterPriceXs = isset($_REQUEST["ClassOrderPayLogTeacherPayTypeItemCenterPriceXs_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogTeacherPayTypeItemCenterPriceXs_".$ListPayCount] : "";
	$ClassOrderPayLogCenterPricePerTimes = isset($_REQUEST["ClassOrderPayLogCenterPricePerTimes_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogCenterPricePerTimes_".$ListPayCount] : "";
	$ClassOrderPayLogCompanyPricePerTimes = isset($_REQUEST["ClassOrderPayLogCompanyPricePerTimes_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogCompanyPricePerTimes_".$ListPayCount] : "";
	$ClassOrderPayLogTotalClassCounts = isset($_REQUEST["ClassOrderPayLogTotalClassCounts_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogTotalClassCounts_".$ListPayCount] : "";
	$ClassOrderPayLogClassSlotCounts = isset($_REQUEST["ClassOrderPayLogClassSlotCounts_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogClassSlotCounts_".$ListPayCount] : "";
	$ClassOrderPayLogDetailPaymentPrices = isset($_REQUEST["ClassOrderPayLogDetailPaymentPrices_".$ListPayCount]) ? $_REQUEST["ClassOrderPayLogDetailPaymentPrices_".$ListPayCount] : "";

	$ArrClassOrderPayLogTeacherIDs = explode("||", $ClassOrderPayLogTeacherIDs);
	$ArrClassOrderPayLogTeacherPayTypeItemCenterPriceXs = explode("||", $ClassOrderPayLogTeacherPayTypeItemCenterPriceXs);
	$ArrClassOrderPayLogCenterPricePerTimes = explode("||", $ClassOrderPayLogCenterPricePerTimes);
	$ArrClassOrderPayLogCompanyPricePerTimes = explode("||", $ClassOrderPayLogCompanyPricePerTimes);
	$ArrClassOrderPayLogTotalClassCounts = explode("||", $ClassOrderPayLogTotalClassCounts);
	$ArrClassOrderPayLogClassSlotCounts = explode("||", $ClassOrderPayLogClassSlotCounts);
	$ArrClassOrderPayLogDetailPaymentPrices = explode("||", $ClassOrderPayLogDetailPaymentPrices);


	
	for ($jj=1;$jj<=count($ArrClassOrderPayLogTeacherIDs)-2;$jj++){

		$ClassOrderPayLogTeacherID = $ArrClassOrderPayLogTeacherIDs[$jj];
		$ClassOrderPayLogTeacherPayTypeItemCenterPriceX = $ArrClassOrderPayLogTeacherPayTypeItemCenterPriceXs[$jj];
		$ClassOrderPayLogCenterPricePerTime = $ArrClassOrderPayLogCenterPricePerTimes[$jj];
		$ClassOrderPayLogCompanyPricePerTime = $ArrClassOrderPayLogCompanyPricePerTimes[$jj];
		$ClassOrderPayLogTotalClassCount = $ArrClassOrderPayLogTotalClassCounts[$jj];
		$ClassOrderPayLogClassSlotCount = $ArrClassOrderPayLogClassSlotCounts[$jj];
		$ClassOrderPayLogDetailPaymentPrice = $ArrClassOrderPayLogDetailPaymentPrices[$jj];


		$Sql2 = " insert into ClassOrderPayB2bDetails ( ";
			$Sql2 .= " ClassOrderPayB2bID, ";
			$Sql2 .= " TeacherID, ";
			$Sql2 .= " TeacherPayTypeItemCenterPriceX, ";
			$Sql2 .= " CompanyPricePerTime, ";
			$Sql2 .= " CenterPricePerTime, ";
			$Sql2 .= " ClassOrderPayTotalClassCount, ";
			$Sql2 .= " ClassOrderPayClassSlotCount, ";
			$Sql2 .= " ClassOrderPayDetailPaymentPrice ";

		$Sql2 .= " ) values ( ";
			$Sql2 .= " :ClassOrderPayB2bID, "; 
			$Sql2 .= " :TeacherID, ";
			$Sql2 .= " :TeacherPayTypeItemCenterPriceX, ";
			$Sql2 .= " :CompanyPricePerTime, ";
			$Sql2 .= " :CenterPricePerTime, ";
			$Sql2 .= " :ClassOrderPayTotalClassCount, ";
			$Sql2 .= " :ClassOrderPayClassSlotCount, ";
			$Sql2 .= " :ClassOrderPayDetailPaymentPrice ";
		$Sql2 .= " ) ";

		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':ClassOrderPayB2bID', $ClassOrderPayB2bID);
		$Stmt2->bindParam(':TeacherID', $ClassOrderPayLogTeacherID);
		$Stmt2->bindParam(':TeacherPayTypeItemCenterPriceX', $ClassOrderPayLogTeacherPayTypeItemCenterPriceX);
		$Stmt2->bindParam(':CompanyPricePerTime', $ClassOrderPayLogCompanyPricePerTime);
		$Stmt2->bindParam(':CenterPricePerTime', $ClassOrderPayLogCenterPricePerTime);
		$Stmt2->bindParam(':ClassOrderPayTotalClassCount', $ClassOrderPayLogTotalClassCount);
		$Stmt2->bindParam(':ClassOrderPayClassSlotCount', $ClassOrderPayLogClassSlotCount);
		$Stmt2->bindParam(':ClassOrderPayDetailPaymentPrice', $ClassOrderPayLogDetailPaymentPrice);
		$Stmt2->execute();
		$ClassOrderPayB2bDetailID = $DbConn->lastInsertId();
		$Stmt2 = null;

	}
}
$Stmt = null;
//=========================================================================================


//=========================================================================================

$SumClassOrderPayDetailPaymentPrice = $SumClassOrderPayDetailPaymentPrice - $ClassOrderPayB2bDifferencePrice;

$ClassOrderPaySellingPrice = $SumClassOrderPayDetailPaymentPrice+$SumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice;
$ClassOrderPayDiscountPrice = 0;
$ClassOrderPayPaymentPrice = $ClassOrderPaySellingPrice-$ClassOrderPayDiscountPrice;

$ClassOrderPayUseCashPrice = $ClassOrderPayPaymentPrice - $ClassOrderPayUseSavedMoneyPrice - $SumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice;

$Sql = "update ClassOrderPays set 
			CenterID=:CenterID,
			CenterPricePerTime=:CenterPricePerTime,
			CompanyPricePerTime=:CompanyPricePerTime,
			ClassOrderPaySellingPrice=:ClassOrderPaySellingPrice,
			ClassOrderPayDiscountPrice=:ClassOrderPayDiscountPrice,
			ClassOrderPayFreeTrialDiscountPrice=:ClassOrderPayFreeTrialDiscountPrice,
			ClassOrderPayPaymentPrice=:ClassOrderPayPaymentPrice,
			ClassOrderPayUseSavedMoneyPrice=:ClassOrderPayUseSavedMoneyPrice,
			ClassOrderPayUseCashPrice=:ClassOrderPayUseCashPrice,
			ClassOrderPayModiDateTime=now() 
		where ClassOrderPayID=:ClassOrderPayID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':CenterID', $CenterID);
$Stmt->bindParam(':CenterPricePerTime', $DefaultCenterPricePerTime);
$Stmt->bindParam(':CompanyPricePerTime', $DefaultCompanyPricePerTime);
$Stmt->bindParam(':ClassOrderPaySellingPrice', $ClassOrderPaySellingPrice);
$Stmt->bindParam(':ClassOrderPayDiscountPrice', $ClassOrderPayDiscountPrice);
$Stmt->bindParam(':ClassOrderPayFreeTrialDiscountPrice', $SumClassOrderPayLogClassOrderPayFreeTrialDiscountPrice);
$Stmt->bindParam(':ClassOrderPayPaymentPrice', $ClassOrderPayPaymentPrice);
$Stmt->bindParam(':ClassOrderPayUseSavedMoneyPrice', $ClassOrderPayUseSavedMoneyPrice);
$Stmt->bindParam(':ClassOrderPayUseCashPrice', $ClassOrderPayUseCashPrice);
$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID); 
$Stmt->execute();
$Stmt = null;
//=========================================================================================

$ArrValue["ClassOrderPayID"] = $ClassOrderPayID;//주문아이디
$ArrValue["ClassOrderPayNumber"] = $ClassOrderPayNumber;//주문번호
$ArrValue["ClassOrderPayUseCashPrice"] = $ClassOrderPayUseCashPrice;//결제금액


$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>
