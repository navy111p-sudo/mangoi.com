<?
//====================================================== DB ======================================================
$DbHost = "localhost";
$DbUser = "mangoi";
$DbPass = "mi!@#2019";

//$DbName = "mangoi";
//$LibUrl = "http://mangoi.co.kr";
$DbName = "mangoi_dev";
$LibUrl = "http://mangoidev.hihome.kr";

$SeverceMode = "T";//테스트
//$SeverceMode = "";

if ($SeverceMode=="T"){
	$BatchGroupID = "A52Q71000489";
}else{
	$BatchGroupID = "A8Z1G1003602";
}

$LibUrl = $LibUrl . "/kcp_batch_mobile/payx/pp_cli_hub.php";


try {
	$DbConn = new PDO("mysql:host=$DbHost;dbname=$DbName;charset=utf8", $DbUser, $DbPass);
	$DbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
	echo "Connection failed: " . $e->getMessage();
}

//====================================================== DB ======================================================
//=======================================================================================================
$EncryptionKey = md5("kr.ahsol");//절대 변경 불가(변경되면 회원정보 복구 불가)
//=======================================================================================================

$SqlAddWhere = " 1=1 ";
$SqlAddWhere = $SqlAddWhere . " and A.ClassOrderPayBatchState=1  ";
//$SqlAddWhere = $SqlAddWhere . " and datediff(B.ClassOrderEndDate, now())=1  ";

$Sql_Loop = "select 
			A.ClassOrderPayBatchID,
			A.ClassOrderPayBatchKey,
			A.ClassOrderPayBatchMonth,
			B.*
		from ClassOrderPayBatchs A 
			inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID
		where 
			A.ClassOrderPayBatchState=1 
		order by A.ClassOrderPayBatchID asc";	
$Stmt_Loop = $DbConn->prepare($Sql_Loop);
$Stmt_Loop->execute();
$Stmt_Loop->setFetchMode(PDO::FETCH_ASSOC);	

while($Row_Loop = $Stmt_Loop->fetch()) {
	$ClassOrderPayBatchID = $Row_Loop["ClassOrderPayBatchID"];
	$ClassOrderPayBatchKey = $Row_Loop["ClassOrderPayBatchKey"];
	$ClassOrderPayBatchMonth = $Row_Loop["ClassOrderPayBatchMonth"];

	$ClassOrderID = $Row_Loop["ClassOrderID"];
	$ClassProductID = $Row_Loop["ClassProductID"];
	$ClassMemberType = $Row_Loop["ClassMemberType"];
	$ClassMemberTypeGroupID = $Row_Loop["ClassMemberTypeGroupID"];
	$ClassOrderTimeTypeID = $Row_Loop["ClassOrderTimeTypeID"];
	$ClassOrderWeekCountID = $Row_Loop["ClassOrderWeekCountID"];
	$ClassOrderStartDate = $Row_Loop["ClassOrderStartDate"];
	$ClassOrderEndDate = $Row_Loop["ClassOrderEndDate"];
	$LastClassOrderEndDate = $Row_Loop["LastClassOrderEndDate"];
	$LastClassOrderEndDateByPay = $Row_Loop["LastClassOrderEndDateByPay"];
	$ClassOrderPayID = $Row_Loop["ClassOrderPayID"];
	$MemberID = $Row_Loop["MemberID"];
	$ClassOrderState = $Row_Loop["ClassOrderState"];
	$ClassProgress = $Row_Loop["ClassProgress"];


	$ClassOrderPayPaymentMemberID = $MemberID;
	$ClassOrderPayUseSavedMoneyPrice = 0;//적립금 사용
	$ClassOrderPayMonthNumberID = 1;//개월수(일단 1개월)
	

	

	//************************************************ Class Order Pay 구성 ************************************************
	$Sql = "
			select 
					A.MemberID,
					A.ClassMemberType,
					B.ClassOrderWeekCount,
					C.ClassOrderTimeSlotCount
			from ClassOrders A 
				inner join ClassOrderWeekCounts B on A.ClassOrderWeekCountID=B.ClassOrderWeekCountID 
				inner join ClassOrderTimeTypes C on A.ClassOrderTimeTypeID=C.ClassOrderTimeTypeID 
			where A.ClassOrderID=:ClassOrderID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$MemberID = $Row["MemberID"];
	$ClassMemberType = $Row["ClassMemberType"];
	$ClassOrderWeekCount = $Row["ClassOrderWeekCount"];
	$ClassOrderTimeSlotCount = $Row["ClassOrderTimeSlotCount"];



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


	$ClassOrderPayNumber = "BCP".date("YmdHis").substr("0000000000".$MemberID,-10);
	//=========================================================================================
	$Sql = " insert into ClassOrderPays ( ";
		$Sql .= " ClassOrderPayNumber, ";
		$Sql .= " ClassOrderPayBatchID, ";
		$Sql .= " ClassOrderPayPaymentMemberID, ";
		$Sql .= " ClassOrderPaySellingPrice, ";
		$Sql .= " ClassOrderPayDiscountPrice, ";
		$Sql .= " ClassOrderPayPaymentPrice, ";
		$Sql .= " ClassOrderPayUseSavedMoneyPrice, ";
		$Sql .= " ClassOrderPayUseCashPrice, ";
		$Sql .= " ClassOrderPayUseCashPaymentType, ";
		$Sql .= " ClassOrderPayPgFeeRatio, ";
		$Sql .= " ClassOrderPayPgFeePrice, ";
		$Sql .= " ClassOrderPayProgress, ";
		$Sql .= " ClassOrderPayRegDateTime, ";
		$Sql .= " ClassOrderPayModiDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :ClassOrderPayNumber, "; 
		$Sql .= " :ClassOrderPayBatchID, "; 
		$Sql .= " :ClassOrderPayPaymentMemberID, ";
		$Sql .= " 0, ";
		$Sql .= " 0, ";
		$Sql .= " 0, ";
		$Sql .= " 0, ";
		$Sql .= " 0, ";
		$Sql .= " 1, ";
		$Sql .= " 0, ";
		$Sql .= " 0, ";
		$Sql .= " 1, ";//DB등록, 주문상태는 건너뛴다
		$Sql .= " now(), ";
		$Sql .= " now() ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderPayNumber', $ClassOrderPayNumber);
	$Stmt->bindParam(':ClassOrderPayBatchID', $ClassOrderPayBatchID);
	$Stmt->bindParam(':ClassOrderPayPaymentMemberID', $ClassOrderPayPaymentMemberID);
	$Stmt->execute();
	$ClassOrderPayID = $DbConn->lastInsertId();
	$Stmt = null;
	//=========================================================================================


	//=========================================================================================
	$Sql = "
			select 
					count(*) as OldPayCount
			from ClassOrderPayDetails A 
				inner join ClassOrderPays B on A.ClassOrderPayID=B.ClassOrderPayID 
			where A.ClassOrderID=:ClassOrderID and ClassOrderPayDetailState=1 and (ClassOrderPayProgress=21 or ClassOrderPayProgress=31 or ClassOrderPayProgress=41 )";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$OldPayCount = $Row["OldPayCount"];
	if ($OldPayCount>0){
		$ClassOrderPayDetailType=2;
	}else{
		$ClassOrderPayDetailType=1;
	}


	$Sql = "
			select 
					A.CenterID,
					A.MemberName,
					A.MemberPricePerTime,
					AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as MemberPhone1,
					AES_DECRYPT(UNHEX(A.MemberEmail),:EncryptionKey) as MemberEmail,
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
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$CenterID = $Row["CenterID"]; 
	$MemberName = $Row["MemberName"]; 
	$MemberPhone1 = $Row["MemberPhone1"];
	$MemberEmail = $Row["MemberEmail"];
	$MemberPricePerTime = $Row["MemberPricePerTime"];
	$CenterPricePerTime = $Row["CenterPricePerTime"];
	$CompanyPricePerTime = $Row["CompanyPricePerTime"];

	if ($MemberPricePerTime>0){
		$CenterPricePerTime = $MemberPricePerTime;
	}



	$Sql = "
			select 
				count(*) as TeacherSlotCount,
				A.TeacherID,
				C.TeacherPayTypeItemCenterPriceX
			from ClassOrderSlots A 
				inner join Teachers B on A.TeacherID=B.TeacherID 
				inner join TeacherPayTypeItems C on B.TeacherPayTypeItemID=C.TeacherPayTypeItemID 
			where A.ClassOrderID=$ClassOrderID  and A.ClassOrderSlotType=1 and A.ClassOrderSlotEndDate is null and A.ClassOrderSlotState=1
			group by A.TeacherID  
			order by A.TeacherID 
	";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);



	$ClassOrderPaySellingPrice = 0;
	$ClassOrderPayDiscountPrice = 0;
	$ClassOrderPayPaymentPrice = 0;

	while($Row = $Stmt->fetch()) {

		$TeacherID = $Row["TeacherID"];
		$TeacherSlotCount = $Row["TeacherSlotCount"];
		$TeacherPayTypeItemCenterPriceX = $Row["TeacherPayTypeItemCenterPriceX"];

		$ClassOrderPayTotalSlotCount = $ClassOrderPayTotalWeekCount * $TeacherSlotCount;//총 슬랏수 = 신청주수 * 강사의 주당 슬랏수
		$ClassOrderPayTotalClassCount = ($TeacherSlotCount / $ClassOrderTimeSlotCount) * $ClassOrderPayTotalWeekCount;//총 수업수 = (강사의 주당 슬랏수 / 수업당 슬랏수) * 신청주수

		$ClassOrderPayDetailSellingPrice = $CenterPricePerTime * $TeacherPayTypeItemCenterPriceX * $ClassOrderPayTotalSlotCount;//판매가 = 센터 단가 * 교사배수 * 전체슬랏
		$ClassOrderPayDetailDiscountPrice = round( $ClassOrderPayDetailSellingPrice * ( $ClassOrderPayMonthDiscountRatio / 100 ), 0);//할인금액 = 판매가 * (할인율)
		$ClassOrderPayDetailPaymentPrice = $ClassOrderPayDetailSellingPrice - $ClassOrderPayDetailDiscountPrice;//실결제가 = 판매가 - 할인금액

		$ClassOrderPayClassSlotCount = $ClassOrderTimeSlotCount;//수업당 슬랏수
		$ClassOrderPayWeekClassCount = $TeacherSlotCount / $ClassOrderTimeSlotCount; //강사의 주당 수업수 = 강사의 주당 슬랏수 / 수업당 슬랏수


		$Sql2 = " insert into ClassOrderPayDetails ( ";
			$Sql2 .= " ClassOrderPayID, ";
			$Sql2 .= " ClassOrderID, ";
			$Sql2 .= " TeacherID, ";
			$Sql2 .= " TeacherPayTypeItemCenterPriceX, ";
			$Sql2 .= " CompanyPricePerTime, ";
			$Sql2 .= " CenterPricePerTime, ";
			$Sql2 .= " ClassOrderPayMonthNumberID, ";
			$Sql2 .= " ClassOrderPayMonthDiscountRatio, ";
			$Sql2 .= " ClassOrderPayTotalWeekCount, ";
			$Sql2 .= " ClassOrderPayWeekClassCount, ";
			$Sql2 .= " ClassOrderPayTotalClassCount, ";
			$Sql2 .= " ClassOrderPayClassSlotCount, ";
			$Sql2 .= " ClassOrderPayTotalSlotCount, ";
			$Sql2 .= " ClassOrderPayDetailType, ";
			$Sql2 .= " ClassOrderPayDetailSellingPrice, ";
			$Sql2 .= " ClassOrderPayDetailDiscountPrice, ";
			$Sql2 .= " ClassOrderPayDetailPaymentPrice, ";
			$Sql2 .= " ClassOrderPayDetailState ";
		$Sql2 .= " ) values ( ";
			$Sql2 .= " :ClassOrderPayID, "; 
			$Sql2 .= " :ClassOrderID, ";
			$Sql2 .= " :TeacherID, ";
			$Sql2 .= " :TeacherPayTypeItemCenterPriceX, ";
			$Sql2 .= " :CompanyPricePerTime, ";
			$Sql2 .= " :CenterPricePerTime, ";
			$Sql2 .= " :ClassOrderPayMonthNumberID, ";
			$Sql2 .= " :ClassOrderPayMonthDiscountRatio, ";
			$Sql2 .= " :ClassOrderPayTotalWeekCount, ";
			$Sql2 .= " :ClassOrderPayWeekClassCount, ";
			$Sql2 .= " :ClassOrderPayTotalClassCount, ";
			$Sql2 .= " :ClassOrderPayClassSlotCount, ";
			$Sql2 .= " :ClassOrderPayTotalSlotCount, ";
			$Sql2 .= " :ClassOrderPayDetailType, ";//1:신규 2:연장
			$Sql2 .= " :ClassOrderPayDetailSellingPrice, ";
			$Sql2 .= " :ClassOrderPayDetailDiscountPrice, ";
			$Sql2 .= " :ClassOrderPayDetailPaymentPrice, ";
			$Sql2 .= " 1 ";
		$Sql2 .= " ) ";

		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':ClassOrderPayID', $ClassOrderPayID);
		$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
		$Stmt2->bindParam(':TeacherID', $TeacherID);
		$Stmt2->bindParam(':TeacherPayTypeItemCenterPriceX', $TeacherPayTypeItemCenterPriceX);
		$Stmt2->bindParam(':CompanyPricePerTime', $CompanyPricePerTime);
		$Stmt2->bindParam(':CenterPricePerTime', $CenterPricePerTime);
		$Stmt2->bindParam(':ClassOrderPayMonthNumberID', $ClassOrderPayMonthNumberID);
		$Stmt2->bindParam(':ClassOrderPayMonthDiscountRatio', $ClassOrderPayMonthDiscountRatio);
		$Stmt2->bindParam(':ClassOrderPayTotalWeekCount', $ClassOrderPayTotalWeekCount);
		$Stmt2->bindParam(':ClassOrderPayWeekClassCount', $ClassOrderPayWeekClassCount);
		$Stmt2->bindParam(':ClassOrderPayTotalClassCount', $ClassOrderPayTotalClassCount);
		$Stmt2->bindParam(':ClassOrderPayClassSlotCount', $ClassOrderPayClassSlotCount);
		$Stmt2->bindParam(':ClassOrderPayTotalSlotCount', $ClassOrderPayTotalSlotCount);
		$Stmt2->bindParam(':ClassOrderPayDetailType', $ClassOrderPayDetailType);
		$Stmt2->bindParam(':ClassOrderPayDetailSellingPrice', $ClassOrderPayDetailSellingPrice);
		$Stmt2->bindParam(':ClassOrderPayDetailDiscountPrice', $ClassOrderPayDetailDiscountPrice);
		$Stmt2->bindParam(':ClassOrderPayDetailPaymentPrice', $ClassOrderPayDetailPaymentPrice);
		$Stmt2->execute();
		$ClassOrderPayDetailID = $DbConn->lastInsertId();
		$Stmt2 = null;

		$ClassOrderPaySellingPrice = $ClassOrderPaySellingPrice + $ClassOrderPayDetailSellingPrice;
		$ClassOrderPayDiscountPrice = $ClassOrderPayDiscountPrice + $ClassOrderPayDetailDiscountPrice;
		$ClassOrderPayPaymentPrice = $ClassOrderPayPaymentPrice + $ClassOrderPayDetailPaymentPrice;
		
	}
	$Stmt = null;

	$ClassOrderPayUseCashPrice = $ClassOrderPayPaymentPrice - $ClassOrderPayUseSavedMoneyPrice;
	//=========================================================================================


	//=========================================================================================
	$Sql = "update ClassOrderPays set 
				CenterID=:CenterID,
				CenterPricePerTime=:CenterPricePerTime,
				CompanyPricePerTime=:CompanyPricePerTime,
				ClassOrderPaySellingPrice=:ClassOrderPaySellingPrice,
				ClassOrderPayDiscountPrice=:ClassOrderPayDiscountPrice,
				ClassOrderPayPaymentPrice=:ClassOrderPayPaymentPrice,
				ClassOrderPayUseSavedMoneyPrice=:ClassOrderPayUseSavedMoneyPrice,
				ClassOrderPayUseCashPrice=:ClassOrderPayUseCashPrice,
				ClassOrderPayModiDateTime=now() 
			where ClassOrderPayID=:ClassOrderPayID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':CenterPricePerTime', $CenterPricePerTime);
	$Stmt->bindParam(':CompanyPricePerTime', $CompanyPricePerTime);
	$Stmt->bindParam(':ClassOrderPaySellingPrice', $ClassOrderPaySellingPrice);
	$Stmt->bindParam(':ClassOrderPayDiscountPrice', $ClassOrderPayDiscountPrice);
	$Stmt->bindParam(':ClassOrderPayPaymentPrice', $ClassOrderPayPaymentPrice);
	$Stmt->bindParam(':ClassOrderPayUseSavedMoneyPrice', $ClassOrderPayUseSavedMoneyPrice);
	$Stmt->bindParam(':ClassOrderPayUseCashPrice', $ClassOrderPayUseCashPrice);
	$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
	$Stmt->execute();
	$Stmt = null;
	//=========================================================================================

	//************************************************ Class Order Pay 구성 ************************************************



	//************************************************ Class Order Pay 결제금 변경 ************************************************
	$CenterFreeTrialCount = 0; //체험은 없음(연장)
	$ClassOrderPayMonthNumberID = $ClassOrderPayBatchMonth;//개월수

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

	//************************************************ Class Order Pay 결제금 변경 ************************************************



	//************************************************ Class Order Pay Batch Results ************************************************


	$Sql = " insert into ClassOrderPayBatchResults ( ";
		$Sql .= " ClassOrderPayBatchID, ";
		$Sql .= " ClassOrderPayID, ";
		$Sql .= " ClassOrderPayBatchResultState, ";
		$Sql .= " ClassOrderPayBatchResultRegDateTime, ";
		$Sql .= " ClassOrderPayBatchResultModiDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :ClassOrderPayBatchID, "; 
		$Sql .= " :ClassOrderPayID, ";
		$Sql .= " 0, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";
	$Sql .= " ) ";


	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderPayBatchID', $ClassOrderPayBatchID);
	$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
	$Stmt->execute();
	$ClassOrderPayBatchResultID = $DbConn->lastInsertId();
	$Stmt = null;


	$Sql = "update ClassOrderPays set 
				ClassOrderPayBatchResultID=:ClassOrderPayBatchResultID,
				ClassOrderPayModiDateTime=now() 
			where ClassOrderPayID=:ClassOrderPayID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderPayBatchResultID', $ClassOrderPayBatchResultID);
	$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
	$Stmt->execute();
	$Stmt = null;

	//************************************************ Class Order Pay Batch Results ************************************************


	
	//************************************************ 결제요청 ************************************************

	$jsonArray = '{"pay_method":"CARD","ordr_idxx":"'.$ClassOrderPayNumber.'","good_name":"망고아이정기결제","good_mny":"'.$ClassOrderPayUseCashPrice.'","buyr_name":"'.$MemberName.'","buyr_mail":"'.$MemberEmail.'","buyr_tel1":"'.$MemberPhone1.'","buyr_tel2":"'.$MemberPhone1.'","bt_batch_key":"'.$ClassOrderPayBatchKey.'","bt_group_id":"'.$BatchGroupID.'","quotaopt":"00","req_tx":"pay","card_pay_method":"Batch","currency":"410"}'; 

	$url = $LibUrl;
	$ch = curl_init();                                 //curl 초기화
	curl_setopt($ch, CURLOPT_URL, $url);               //URL 지정하기
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);      //connection timeout 10초 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);       //POST data
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonArray));
	curl_setopt($ch, CURLOPT_POST, true);              //true시 post 전송 
	 
	$response = curl_exec($ch);
 
	//var_dump($response);        //결과 값 출력
	//print_r(curl_getinfo($ch)); //모든 정보 출력
	//echo curl_errno($ch);       //에러 정보 출력
	//echo curl_error($ch);       //에러 정보 출력

	curl_close($ch);

	//************************************************ 결제요청 ************************************************

}
$Stmt_Loop = null;


?>
