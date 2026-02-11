 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$ClassOrderID = $_REQUEST["ClassOrderID"] ?? "";
$ClassOrderPayID = $_REQUEST["ClassOrderPayID"] ?? "";
$ClassOrderMode = $_REQUEST["ClassOrderMode"] ?? "";

$ClassOrderPayUseSavedMoneyPrice = isset($_REQUEST["ClassOrderPayUseSavedMoneyPrice"]) ? $_REQUEST["ClassOrderPayUseSavedMoneyPrice"] : "";//받지 않음
$ClassOrderPayMonthNumberID = isset($_REQUEST["ClassOrderPayMonthNumberID"]) ? $_REQUEST["ClassOrderPayMonthNumberID"] : "";//받지 않음
 $_LINK_MEMBER_ID_ = $_LINK_MEMBER_ID_ ?? "";
 $ClassOrderPayPaymentMemberID = $_LINK_MEMBER_ID_;

if ($ClassOrderPayUseSavedMoneyPrice==""){
	$ClassOrderPayUseSavedMoneyPrice = 0;//적립금 사용
}

if ($ClassOrderPayMonthNumberID==""){
	$ClassOrderPayMonthNumberID = 1;//1개월
}


$Sql = "
		select 
				A.MemberID,
				A.ClassMemberType,
				A.ClassOrderTimeTypeID,
				B.ClassOrderWeekCount,
				C.ClassOrderTimeSlotCount,
				ifnull(E.CenterFreeTrialCount, 0) as CenterFreeTrialCount
		from ClassOrders A 
			inner join ClassOrderWeekCounts B on A.ClassOrderWeekCountID=B.ClassOrderWeekCountID 
			inner join ClassOrderTimeTypes C on A.ClassOrderTimeTypeID=C.ClassOrderTimeTypeID 
			inner join Members D on A.MemberID=D.MemberID 
			left outer join Centers E on D.CenterID=E.CenterID 
		where A.ClassOrderID=:ClassOrderID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberID = $Row["MemberID"];
$ClassMemberType = $Row["ClassMemberType"];
$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
$ClassOrderWeekCount = $Row["ClassOrderWeekCount"];
$ClassOrderTimeSlotCount = $Row["ClassOrderTimeSlotCount"];
$CenterFreeTrialCount = $Row["CenterFreeTrialCount"];


$Sql = "
		select 
				count(*) as PreClassOrderCount 
		from ClassOrders A 
		where A.MemberID=:MemberID
			and (A.ClassOrderState=1 or A.ClassOrderState=2 or A.ClassOrderState=3 or A.ClassOrderState=4)
			and A.ClassProgress=11 
			and A.ClassProductID=1 
	";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$PreClassOrderCount = $Row["PreClassOrderCount"];

if ($PreClassOrderCount>0){//기존 신청한 수업이 있을경우 체험수업은 없다.
	$CenterFreeTrialCount = 0;
}



$Sql = "
		select 
				A.ClassOrderPayTotalWeekCount,
				A.ClassOrderPayMonthDiscountRatio
		from ClassOrderPayMonthNumbers A 
		where A.ClassOrderPayMonthNumberID=:ClassOrderPayMonthNumberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderPayMonthNumberID', $ClassOrderPayMonthNumberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ClassOrderPayTotalWeekCount = $Row["ClassOrderPayTotalWeekCount"];
$ClassOrderPayMonthDiscountRatio = $Row["ClassOrderPayMonthDiscountRatio"];


//=========================================================================================
$Sql = "
		select 
				A.MemberPricePerTime,
				B.CenterPricePerTime,
				E.CompanyPricePerTime
		from Members A 
			inner join Centers B on A.CenterID=B.CenterID 
			inner join Branches C on B.BranchID=C.BranchID 
			inner join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
			inner join Companies E on D.CompanyID=E.CompanyID 
		where A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberPricePerTime = $Row["MemberPricePerTime"];
$CenterPricePerTime = $Row["CenterPricePerTime"];
$CompanyPricePerTime = $Row["CompanyPricePerTime"];

if ($MemberPricePerTime>0){
	$CenterPricePerTime = $MemberPricePerTime;
}


/* b2c 에는 1:2 이상 그룹이 없다
if ($ClassMemberType==2){
	$CenterPricePerTime = round($CenterPricePerTime / 3 * 2, 0);
	$CompanyPricePerTime = round($CompanyPricePerTime / 3 * 2, 0);
}else if ($ClassMemberType==3){
	$CenterPricePerTime = round($CenterPricePerTime / 3 * 2, 0);
	$CompanyPricePerTime = round($CompanyPricePerTime / 3 * 2, 0);
}
*/

$Sql = "
		select 
			A.*
		from ClassOrderPayDetails A 
		where A.ClassOrderID=$ClassOrderID and A.ClassOrderPayID=$ClassOrderPayID and A.ClassOrderPayDetailState=1 
		order by A.ClassOrderPayDetailID asc
";



$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



$ClassOrderPayFreeTrialDiscountPrice = $CenterFreeTrialCount * ($CenterPricePerTime * $ClassOrderTimeTypeID);// 무료수업수 * 10분당기본가 * 수업시간(2 : 20분, 4 : 40분)
if ($ClassOrderPayMonthDiscountRatio>0){
	$ClassOrderPayFreeTrialDiscountPrice = $ClassOrderPayFreeTrialDiscountPrice - round( $ClassOrderPayFreeTrialDiscountPrice * ( $ClassOrderPayMonthDiscountRatio / 100 ), 0);
}

$ClassOrderPaySellingPrice = 0;
$ClassOrderPayDiscountPrice = 0;
$ClassOrderPayPaymentPrice = 0;


while($Row = $Stmt->fetch()) {

	$ClassOrderPayDetailID = $Row["ClassOrderPayDetailID"];

	$TeacherID = $Row["TeacherID"];
	$TeacherPayTypeItemCenterPriceX = $Row["TeacherPayTypeItemCenterPriceX"];
	$ClassOrderPayWeekClassCount = $Row["ClassOrderPayWeekClassCount"];
	$ClassOrderPayClassSlotCount = $Row["ClassOrderPayClassSlotCount"];

	$TeacherSlotCount = $ClassOrderPayWeekClassCount * $ClassOrderPayClassSlotCount;
	
	$ClassOrderPayTotalSlotCount = $ClassOrderPayTotalWeekCount * $TeacherSlotCount;//총 슬랏수 = 신청주수 * 강사의 주당 슬랏수
	$ClassOrderPayTotalClassCount = ($TeacherSlotCount / $ClassOrderTimeSlotCount) * $ClassOrderPayTotalWeekCount;//총 수업수 = (강사의 주당 슬랏수 / 수업당 슬랏수) * 신청주수

	$ClassOrderPayDetailSellingPrice = $CenterPricePerTime * $TeacherPayTypeItemCenterPriceX * $ClassOrderPayTotalSlotCount;//판매가 = 센터 단가 * 교사배수 * 전체슬랏
	$ClassOrderPayDetailDiscountPrice = round( $ClassOrderPayDetailSellingPrice * ( $ClassOrderPayMonthDiscountRatio / 100 ), 0);//할인금액 = 판매가 * (할인율)
	$ClassOrderPayDetailPaymentPrice = $ClassOrderPayDetailSellingPrice - $ClassOrderPayDetailDiscountPrice;//실결제가 = 판매가 - 할인금액

	$Sql2 = "update ClassOrderPayDetails set 
				ClassOrderPayMonthNumberID=:ClassOrderPayMonthNumberID,
				ClassOrderPayMonthDiscountRatio=:ClassOrderPayMonthDiscountRatio,
				ClassOrderPayTotalWeekCount=:ClassOrderPayTotalWeekCount,
				ClassOrderPayTotalClassCount=:ClassOrderPayTotalClassCount,
				ClassOrderPayTotalSlotCount=:ClassOrderPayTotalSlotCount,
				ClassOrderPayDetailSellingPrice=:ClassOrderPayDetailSellingPrice,
				ClassOrderPayDetailDiscountPrice=:ClassOrderPayDetailDiscountPrice,
				ClassOrderPayDetailPaymentPrice=:ClassOrderPayDetailPaymentPrice
			where ClassOrderPayDetailID=:ClassOrderPayDetailID ";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':ClassOrderPayMonthNumberID', $ClassOrderPayMonthNumberID);
	$Stmt2->bindParam(':ClassOrderPayMonthDiscountRatio', $ClassOrderPayMonthDiscountRatio);
	$Stmt2->bindParam(':ClassOrderPayTotalWeekCount', $ClassOrderPayTotalWeekCount);
	$Stmt2->bindParam(':ClassOrderPayTotalClassCount', $ClassOrderPayTotalClassCount);
	$Stmt2->bindParam(':ClassOrderPayTotalSlotCount', $ClassOrderPayTotalSlotCount);
	$Stmt2->bindParam(':ClassOrderPayDetailSellingPrice', $ClassOrderPayDetailSellingPrice);
	$Stmt2->bindParam(':ClassOrderPayDetailDiscountPrice', $ClassOrderPayDetailDiscountPrice);
	$Stmt2->bindParam(':ClassOrderPayDetailPaymentPrice', $ClassOrderPayDetailPaymentPrice);
	$Stmt2->bindParam(':ClassOrderPayDetailID', $ClassOrderPayDetailID);
	$Stmt2->execute();
	$Stmt2 = null;

	$ClassOrderPaySellingPrice = $ClassOrderPaySellingPrice + $ClassOrderPayDetailSellingPrice;
	$ClassOrderPayDiscountPrice = $ClassOrderPayDiscountPrice + $ClassOrderPayDetailDiscountPrice;
	$ClassOrderPayPaymentPrice = $ClassOrderPayPaymentPrice + $ClassOrderPayDetailPaymentPrice;
	
}
$Stmt = null;

$ClassOrderPayUseCashPrice = $ClassOrderPayPaymentPrice - $ClassOrderPayUseSavedMoneyPrice;

$ClassOrderPayUseCashPrice = $ClassOrderPayUseCashPrice - $ClassOrderPayFreeTrialDiscountPrice;
//=========================================================================================


//=========================================================================================
$Sql = "update ClassOrderPays set 
			ClassOrderPaySellingPrice=:ClassOrderPaySellingPrice,
			ClassOrderPayDiscountPrice=:ClassOrderPayDiscountPrice,
			ClassOrderPayFreeTrialDiscountPrice = :ClassOrderPayFreeTrialDiscountPrice,
			ClassOrderPayPaymentPrice=:ClassOrderPayPaymentPrice,
			ClassOrderPayUseSavedMoneyPrice=:ClassOrderPayUseSavedMoneyPrice,
			ClassOrderPayUseCashPrice=:ClassOrderPayUseCashPrice,
			ClassOrderPayModiDateTime=now() 
		where ClassOrderPayID=:ClassOrderPayID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderPaySellingPrice', $ClassOrderPaySellingPrice);
$Stmt->bindParam(':ClassOrderPayDiscountPrice', $ClassOrderPayDiscountPrice);
$Stmt->bindParam(':ClassOrderPayFreeTrialDiscountPrice', $ClassOrderPayFreeTrialDiscountPrice);
$Stmt->bindParam(':ClassOrderPayPaymentPrice', $ClassOrderPayPaymentPrice);
$Stmt->bindParam(':ClassOrderPayUseSavedMoneyPrice', $ClassOrderPayUseSavedMoneyPrice);
$Stmt->bindParam(':ClassOrderPayUseCashPrice', $ClassOrderPayUseCashPrice);
$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
$Stmt->execute();
$Stmt = null;
//=========================================================================================


$Sql = "
		select 
			A.*,
			B.TeacherName
		from ClassOrderPayDetails A 
			inner join Teachers B on A.TeacherID=B.TeacherID 
		where A.ClassOrderID=$ClassOrderID and A.ClassOrderPayID=$ClassOrderPayID and A.ClassOrderPayDetailState=1
		group by A.TeacherID 
		order by A.ClassOrderPayDetailID asc
";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


$ClassTeacherInfoHTML = "";

$ClassTeacherInfoHTML .= "<div class=\"payment_table_wrap\">";
$ClassTeacherInfoHTML .= "	<div class=\"payment_table_right\" style=\"width:100%;\">";
$ClassTeacherInfoHTML .= "		<h3 class=\"payment_left_caption\"><b>수업</b> 정보</h3>";
$ClassTeacherInfoHTML .= "		<table class=\"payment_table_5\">";
$ClassTeacherInfoHTML .= "			<tr>";
$ClassTeacherInfoHTML .= "				<th style=\"text-align:center;padding:0px;\">강사명</th>";
$ClassTeacherInfoHTML .= "				<th style=\"text-align:center;padding:0px;\">수업시간</th>";
$ClassTeacherInfoHTML .= "				<th style=\"text-align:center;padding:0px;\">수강료</th>";
$ClassTeacherInfoHTML .= "				<th style=\"text-align:center;padding:0px;\">기간할인</th>";
$ClassTeacherInfoHTML .= "				<th style=\"text-align:center;padding:0px;\">결제금액</th>";
$ClassTeacherInfoHTML .= "			</tr>";



$ArrWeekDayStr = explode(",","일,월,화,수,목,금,토");

while($Row = $Stmt->fetch()) {

	$TeacherID = $Row["TeacherID"];
	$TeacherName = $Row["TeacherName"];
	$ClassOrderPayDetailSellingPrice = $Row["ClassOrderPayDetailSellingPrice"];
	$ClassOrderPayDetailDiscountPrice = $Row["ClassOrderPayDetailDiscountPrice"];
	$ClassOrderPayDetailPaymentPrice = $Row["ClassOrderPayDetailPaymentPrice"];

	$Sql3 = "
			select 
				AAA.StudyTimeHour,
				AAA.StudyTimeMinute,
				AAA.StudyTimeWeek,
				AAA.ClassOrderSlotEndDate,
				BBB.TeacherName 
			from ClassOrderSlots AAA 
				inner join Teachers BBB on AAA.TeacherID=BBB.TeacherID 
			where AAA.ClassOrderID=$ClassOrderID and AAA.TeacherID=$TeacherID and AAA.ClassOrderSlotState=1 and AAA.ClassOrderSlotType=1 and AAA.ClassOrderSlotMaster=1 
			group by AAA.StudyTimeWeek, AAA.TeacherID, AAA.StudyTimeHour, AAA.StudyTimeMinute  
			order by AAA.StudyTimeWeek, AAA.StudyTimeHour asc, AAA.StudyTimeMinute asc 
	";

	$Stmt3 = $DbConn->prepare($Sql3);
	$Stmt3->execute();
	$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
	
	$TeacherClassTimeInfo = "";
	while($Row3 = $Stmt3->fetch()) {
		$StudyTimeHour = $Row3["StudyTimeHour"];
		$StudyTimeMinute = $Row3["StudyTimeMinute"];
		$StudyTimeWeek = $Row3["StudyTimeWeek"];
		$ClassOrderSlotEndDate = $Row3["ClassOrderSlotEndDate"];


		
		if ($ClassOrderSlotEndDate==""){
			if ($TeacherClassTimeInfo!=""){
				$TeacherClassTimeInfo .= "<br>";
			}
			$TeacherClassTimeInfo .= $ArrWeekDayStr[$StudyTimeWeek]."(".$StudyTimeHour."시 ".$StudyTimeMinute."분)";
		}else{
			//$TeacherClassTimeInfo .= $ArrWeekDayStr[$StudyTimeWeek]."(".$StudyTimeHour."시 ".$StudyTimeMinute."분)";
			//변경된 예전 수업 안보여줌
		}

	}
	$Stmt3 = null;



	$ClassTeacherInfoHTML .= "			<tr>";
	$ClassTeacherInfoHTML .= "				<td style=\"text-align:center;padding:5px 0px;\">".$TeacherName."</td>";
	$ClassTeacherInfoHTML .= "				<td style=\"text-align:center;padding:5px 0px;\">".$TeacherClassTimeInfo."</td>";
	$ClassTeacherInfoHTML .= "				<td style=\"text-align:center;padding:5px 0px;\">".number_format($ClassOrderPayDetailSellingPrice,0)." 원</td>";
	$ClassTeacherInfoHTML .= "				<td style=\"text-align:center;padding:5px 0px;\">".number_format($ClassOrderPayDetailDiscountPrice,0)." 원</td>";
	$ClassTeacherInfoHTML .= "				<td style=\"text-align:center;padding:5px 0px;\">".number_format($ClassOrderPayDetailPaymentPrice,0)." 원</td>";
	$ClassTeacherInfoHTML .= "			</tr>";
}


$ClassTeacherInfoHTML .= "		</table>";
$ClassTeacherInfoHTML .= "	</div>";
$ClassTeacherInfoHTML .= "</div>";


$ClassTeacherInfoHTML .= "<div class=\"payment_table_wrap\">";
$ClassTeacherInfoHTML .= "	<div class=\"payment_table_right\" style=\"width:100%;\">";
$ClassTeacherInfoHTML .= "		<h3 class=\"payment_left_caption\"><b>최종 결제</b> 정보</h3>";
$ClassTeacherInfoHTML .= "		<table class=\"payment_table_4\">";
$ClassTeacherInfoHTML .= "			<tr>";
$ClassTeacherInfoHTML .= "				<th>";
$ClassTeacherInfoHTML .= "					<span class=\"bullet\"></span>기본수강료<br>";
$ClassTeacherInfoHTML .= "					<span class=\"bullet\"></span>기간할인액<br>";
if ($CenterFreeTrialCount>0){
	$ClassTeacherInfoHTML .= "					<span class=\"bullet\"></span>무료수강할인(".$CenterFreeTrialCount."회)<br>";
}
$ClassTeacherInfoHTML .= "					<span class=\"bullet\"></span>포인트사용";
$ClassTeacherInfoHTML .= "					<!--<span class=\"bullet\"></span>구룹수업할인-->";
$ClassTeacherInfoHTML .= "				</th>";
$ClassTeacherInfoHTML .= "				<td>";
$ClassTeacherInfoHTML .= "					<span>".number_format($ClassOrderPaySellingPrice,0)."</span> 원";
$ClassTeacherInfoHTML .= "					<div class=\"orange\">-<span>".number_format($ClassOrderPayDiscountPrice,0)."</span> 원</div>";
$ClassTeacherInfoHTML .= "					<div class=\"orange\">-<span>".number_format($ClassOrderPayFreeTrialDiscountPrice,0)."</span> 원</div>";
$ClassTeacherInfoHTML .= "					<div class=\"orange\">-<span>".number_format($ClassOrderPayUseSavedMoneyPrice,0)."</span> 원</div>";
$ClassTeacherInfoHTML .= "					<!--<div class=\"orange\">-<span>0</span> 원</div>-->";
$ClassTeacherInfoHTML .= "				</td>";
$ClassTeacherInfoHTML .= "			</tr>";
$ClassTeacherInfoHTML .= "			<tr>";
$ClassTeacherInfoHTML .= "				<th><span class=\"bullet\"></span>최종 결제 금액</th>";
$ClassTeacherInfoHTML .= "				<td><span>".number_format($ClassOrderPayUseCashPrice,0)."</span> 원</td>";
$ClassTeacherInfoHTML .= "			</tr>";
$ClassTeacherInfoHTML .= "		</table>";
$ClassTeacherInfoHTML .= "	</div>";
$ClassTeacherInfoHTML .= "</div>";


$ArrValue["ClassTeacherInfoHTML"] = $ClassTeacherInfoHTML;
$ArrValue["ClassOrderPayUseCashPrice"] = $ClassOrderPayUseCashPrice;
$ArrValue["ClassOrderPaySellingPrice"] = $ClassOrderPaySellingPrice;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>