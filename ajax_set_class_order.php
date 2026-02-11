 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";

$CompanyPricePerTime = isset($_REQUEST["CompanyPricePerTime"]) ? $_REQUEST["CompanyPricePerTime"] : "";
$CenterPricePerTime = isset($_REQUEST["CenterPricePerTime"]) ? $_REQUEST["CenterPricePerTime"] : "";

$CenterFreeTrialCount = isset($_REQUEST["CenterFreeTrialCount"]) ? $_REQUEST["CenterFreeTrialCount"] : "";
$ClassOrderTimeSlotCount = isset($_REQUEST["ClassOrderTimeSlotCount"]) ? $_REQUEST["ClassOrderTimeSlotCount"] : "";
$TeacherPayTypeItemCenterPriceX = isset($_REQUEST["TeacherPayTypeItemCenterPriceX"]) ? $_REQUEST["TeacherPayTypeItemCenterPriceX"] : "";
$ClassOrderWeekCount = isset($_REQUEST["ClassOrderWeekCount"]) ? $_REQUEST["ClassOrderWeekCount"] : "";
$ClassOrderTotalWeekCount = isset($_REQUEST["ClassOrderTotalWeekCount"]) ? $_REQUEST["ClassOrderTotalWeekCount"] : "";
$ClassOrderMonthDiscount = isset($_REQUEST["ClassOrderMonthDiscount"]) ? $_REQUEST["ClassOrderMonthDiscount"] : "";
$CenterFreeTrialDiscount = isset($_REQUEST["CenterFreeTrialDiscount"]) ? $_REQUEST["CenterFreeTrialDiscount"] : "";

$TeacherPayTypeItemID = isset($_REQUEST["TeacherPayTypeItemID"]) ? $_REQUEST["TeacherPayTypeItemID"] : "";
$ClassProductID = isset($_REQUEST["ClassProductID"]) ? $_REQUEST["ClassProductID"] : "";
$ClassOrderWishStartDate = isset($_REQUEST["ClassOrderWishStartDate"]) ? $_REQUEST["ClassOrderWishStartDate"] : "";
$ClassOrderType = isset($_REQUEST["ClassOrderType"]) ? $_REQUEST["ClassOrderType"] : "";
$ClassOrderWeekCountID = isset($_REQUEST["ClassOrderWeekCountID"]) ? $_REQUEST["ClassOrderWeekCountID"] : "";
$ClassOrderTimeTypeID = isset($_REQUEST["ClassOrderTimeTypeID"]) ? $_REQUEST["ClassOrderTimeTypeID"] : "";
$ClassOrderMonthNumberID = isset($_REQUEST["ClassOrderMonthNumberID"]) ? $_REQUEST["ClassOrderMonthNumberID"] : "";
$ClassOrderText1 = isset($_REQUEST["ClassOrderText1"]) ? $_REQUEST["ClassOrderText1"] : "";
$ClassOrderText2 = isset($_REQUEST["ClassOrderText2"]) ? $_REQUEST["ClassOrderText2"] : "";



$SellingPrice = isset($_REQUEST["SellingPrice"]) ? $_REQUEST["SellingPrice"] : "";
$DiscountPrice = isset($_REQUEST["DiscountPrice"]) ? $_REQUEST["DiscountPrice"] : "";
$PaymentPrice = isset($_REQUEST["PaymentPrice"]) ? $_REQUEST["PaymentPrice"] : "";
$UsePointPrice = isset($_REQUEST["UsePointPrice"]) ? $_REQUEST["UsePointPrice"] : "";
$UseCashPrice = isset($_REQUEST["UseCashPrice"]) ? $_REQUEST["UseCashPrice"] : "";
$UseCashPaymentType = isset($_REQUEST["UseCashPaymentType"]) ? $_REQUEST["UseCashPaymentType"] : "";
$ClassProgress = isset($_REQUEST["ClassProgress"]) ? $_REQUEST["ClassProgress"] : "";
$ClassOrderState = isset($_REQUEST["ClassOrderState"]) ? $_REQUEST["ClassOrderState"] : "";


$ClassOrderMakeType = isset($_REQUEST["ClassOrderMakeType"]) ? $_REQUEST["ClassOrderMakeType"] : "";
$SelectSlotCode = isset($_REQUEST["SelectSlotCode"]) ? $_REQUEST["SelectSlotCode"] : "";
$SelectStudyTimeCode = isset($_REQUEST["SelectStudyTimeCode"]) ? $_REQUEST["SelectStudyTimeCode"] : "";


$OrderProgress = 1;//DB 등록


$PaymentMemberID = $_LINK_MEMBER_ID_;
$ClassOrderNumber = "CH".date("YmdHis").substr("0000000000".$MemberID,-10);

$Sql = " insert into ClassOrders ( ";
	$Sql .= " ClassProductID, ";
	$Sql .= " MemberID, ";
	$Sql .= " PaymentMemberID, ";
	$Sql .= " ClassOrderType, ";
	$Sql .= " ClassOrderMakeType, ";
	$Sql .= " TeacherPayTypeItemID, ";
	$Sql .= " CompanyPricePerTime, ";
	$Sql .= " CenterPricePerTime, ";
	$Sql .= " CenterFreeTrialCount, ";
	$Sql .= " ClassOrderTimeSlotCount, ";
	$Sql .= " TeacherPayTypeItemCenterPriceX, ";
	$Sql .= " ClassOrderWeekCount, ";
	$Sql .= " ClassOrderTotalWeekCount, ";
	$Sql .= " ClassOrderMonthDiscount, ";
	$Sql .= " CenterFreeTrialDiscount, ";
	$Sql .= " ClassOrderNumber, ";
	$Sql .= " ClassOrderWeekCountID, ";
	$Sql .= " ClassOrderTimeTypeID, ";
	$Sql .= " ClassOrderMonthNumberID, ";
	$Sql .= " ClassOrderWishStartDate, ";
	$Sql .= " ClassOrderText1, ";
	$Sql .= " ClassOrderText2, ";
	$Sql .= " SellingPrice, ";
	$Sql .= " DiscountPrice, ";
	$Sql .= " PaymentPrice, ";
	$Sql .= " UsePointPrice, ";
	$Sql .= " UseCashPrice, ";
	$Sql .= " UseCashPaymentType, ";
	$Sql .= " OrderProgress, ";
	$Sql .= " ClassProgress, ";
	$Sql .= " ClassOrderState, ";
	$Sql .= " ClassOrderRegDateTime, ";
	$Sql .= " ClassOrderModiDateTime ";
$Sql .= " ) values ( ";
	$Sql .= " :ClassProductID, ";
	$Sql .= " :MemberID, ";
	$Sql .= " :PaymentMemberID, ";
	$Sql .= " :ClassOrderType, ";
	$Sql .= " :ClassOrderMakeType, ";
	$Sql .= " :TeacherPayTypeItemID, ";
	$Sql .= " :CompanyPricePerTime, ";
	$Sql .= " :CenterPricePerTime, ";
	$Sql .= " :CenterFreeTrialCount, ";
	$Sql .= " :ClassOrderTimeSlotCount, ";
	$Sql .= " :TeacherPayTypeItemCenterPriceX, ";
	$Sql .= " :ClassOrderWeekCount, ";
	$Sql .= " :ClassOrderTotalWeekCount, ";
	$Sql .= " :ClassOrderMonthDiscount, ";
	$Sql .= " :CenterFreeTrialDiscount, ";
	$Sql .= " :ClassOrderNumber, ";
	$Sql .= " :ClassOrderWeekCountID, ";
	$Sql .= " :ClassOrderTimeTypeID, ";
	$Sql .= " :ClassOrderMonthNumberID, ";
	$Sql .= " :ClassOrderWishStartDate, ";
	$Sql .= " :ClassOrderText1, ";
	$Sql .= " :ClassOrderText2, ";
	$Sql .= " :SellingPrice, ";
	$Sql .= " :DiscountPrice, ";
	$Sql .= " :PaymentPrice, ";
	$Sql .= " :UsePointPrice, ";
	$Sql .= " :UseCashPrice, ";
	$Sql .= " :UseCashPaymentType, ";
	$Sql .= " :OrderProgress, ";
	$Sql .= " :ClassProgress, ";
	$Sql .= " :ClassOrderState, ";
	$Sql .= " now(), ";
	$Sql .= " now() ";
$Sql .= " ) ";


$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassProductID', $ClassProductID);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':PaymentMemberID', $PaymentMemberID);
$Stmt->bindParam(':ClassOrderType', $ClassOrderType);
$Stmt->bindParam(':ClassOrderMakeType', $ClassOrderMakeType);
$Stmt->bindParam(':TeacherPayTypeItemID', $TeacherPayTypeItemID);
$Stmt->bindParam(':CompanyPricePerTime', $CompanyPricePerTime);
$Stmt->bindParam(':CenterPricePerTime', $CenterPricePerTime);
$Stmt->bindParam(':CenterFreeTrialCount', $CenterFreeTrialCount);
$Stmt->bindParam(':ClassOrderTimeSlotCount', $ClassOrderTimeSlotCount);
$Stmt->bindParam(':TeacherPayTypeItemCenterPriceX', $TeacherPayTypeItemCenterPriceX);
$Stmt->bindParam(':ClassOrderWeekCount', $ClassOrderWeekCount);
$Stmt->bindParam(':ClassOrderTotalWeekCount', $ClassOrderTotalWeekCount);
$Stmt->bindParam(':ClassOrderMonthDiscount', $ClassOrderMonthDiscount);
$Stmt->bindParam(':CenterFreeTrialDiscount', $CenterFreeTrialDiscount);
$Stmt->bindParam(':ClassOrderNumber', $ClassOrderNumber);
$Stmt->bindParam(':ClassOrderWeekCountID', $ClassOrderWeekCountID);
$Stmt->bindParam(':ClassOrderTimeTypeID', $ClassOrderTimeTypeID);
$Stmt->bindParam(':ClassOrderMonthNumberID', $ClassOrderMonthNumberID);
$Stmt->bindParam(':ClassOrderWishStartDate', $ClassOrderWishStartDate);
$Stmt->bindParam(':ClassOrderText1', $ClassOrderText1);
$Stmt->bindParam(':ClassOrderText2', $ClassOrderText2);
$Stmt->bindParam(':SellingPrice', $SellingPrice);
$Stmt->bindParam(':DiscountPrice', $DiscountPrice);
$Stmt->bindParam(':PaymentPrice', $PaymentPrice);
$Stmt->bindParam(':UsePointPrice', $UsePointPrice);
$Stmt->bindParam(':UseCashPrice', $UseCashPrice);
$Stmt->bindParam(':UseCashPaymentType', $UseCashPaymentType);
$Stmt->bindParam(':OrderProgress', $OrderProgress);
$Stmt->bindParam(':ClassProgress', $ClassProgress);
$Stmt->bindParam(':ClassOrderState', $ClassOrderState);

$Stmt->execute();
$ClassOrderID = $DbConn->lastInsertId();
$Stmt = null;

if ($ClassOrderMakeType=="1"){//학생이 강사, 시간 모두 선택한 경우

	$Sql = " insert into ClassOrderSlotInfos ( ";
		$Sql .= " ClassOrderID, ";
		$Sql .= " SelectSlotCode, ";
		$Sql .= " SelectStudyTimeCode ";

	$Sql .= " ) values ( ";
		$Sql .= " :ClassOrderID, ";
		$Sql .= " :SelectSlotCode, ";
		$Sql .= " :SelectStudyTimeCode ";
	$Sql .= " ) ";
	

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
	$Stmt->bindParam(':SelectSlotCode', $SelectSlotCode);
	$Stmt->bindParam(':SelectStudyTimeCode', $SelectStudyTimeCode);
	$Stmt->execute();
	$Stmt = null;

}



$ArrValue["ClassOrderNumber"] = $ClassOrderNumber;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>