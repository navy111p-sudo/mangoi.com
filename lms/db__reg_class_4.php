<?php
//상세주문 만들기(추가일경우 기존 OrderID 이후로 한다)

//include_once('../includes/dbopen.php');
include_once('../includes/common.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<body>
<?
$OldClassOrderID = 1424;//이전 입력 마지막 ClassOrderID 

$Sql_C = "
		select 
			A.*
		from ClassOrders A 
		where ClassOrderID > ".$OldClassOrderID." 
		order by A.ClassOrderID asc 
		";
$Stmt_C = $DbConn->prepare($Sql_C);
$Stmt_C->execute();
$Stmt_C->setFetchMode(PDO::FETCH_ASSOC);

$ClassOrderPayCount = 0;//생성되는 주문장 개수
while($Row_C = $Stmt_C->fetch()) {



		$ClassOrderID = $Row_C["ClassOrderID"];

		$ClassOrderPayPaymentMemberID = 1;
		$ClassOrderPayUseSavedMoneyPrice = 0;//적립금 사용
		$ClassOrderPayMonthNumberID = 1;//1개월


		$Sql = "
				select 
						A.MemberID,
						A.ClassMemberType,
						B.ClassOrderWeekCount,
						C.ClassOrderTimeSlotCount
				from ClassOrders A 
					inner join ClassOrderWeekCounts B on A.ClassOrderWeekCountID=B.ClassOrderWeekCountID 
					inner join ClassOrderTimeTypes C on A.ClassOrderTimeTypeID=C.ClassOrderTimeTypeID 
				where A.ClassOrderID=:ClassOrderID ";
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


		$ClassOrderPayNumber = "MA".date("YmdHis").substr("0000000000".$MemberID,-10); // MA -> Mangoi Admin

		//=========================================================================================
		$Sql = " insert into ClassOrderPays ( ";
			$Sql .= " ClassOrderPayNumber, ";
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
			$Sql .= " :ClassOrderPayPaymentMemberID, ";
			$Sql .= " 0, ";
			$Sql .= " 0, ";
			$Sql .= " 0, ";
			$Sql .= " 0, ";
			$Sql .= " 0, ";
			$Sql .= " 1, ";
			$Sql .= " 0, ";
			$Sql .= " 0, ";
			$Sql .= " 21, ";//결제완료
			$Sql .= " now(), ";
			$Sql .= " now() ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ClassOrderPayNumber', $ClassOrderPayNumber);
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
			$ClassOrderPayDetailType=2;//연장
		}else{
			$ClassOrderPayDetailType=1;//신규
		}


		/*
		$Sql = "
				select 
						ifnull(B.CenterPricePerTime,0) as CenterPricePerTime,
						ifnull(E.CompanyPricePerTime,0) as CompanyPricePerTime
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
		$CenterPricePerTime = $Row["CenterPricePerTime"];
		$CompanyPricePerTime = $Row["CompanyPricePerTime"];
		*/

		$CenterPricePerTime = 0;
		$CompanyPricePerTime = 2250;

		if ($ClassMemberType==2){
			$CenterPricePerTime = round($CenterPricePerTime / 3 * 2, 0);
			$CompanyPricePerTime = round($CompanyPricePerTime / 3 * 2, 0);
		}else if ($ClassMemberType==3){
			$CenterPricePerTime = round($CenterPricePerTime / 3 * 2, 0);
			$CompanyPricePerTime = round($CompanyPricePerTime / 3 * 2, 0);
		}

		$Sql = "
				select 
					count(*) as TeacherSlotCount,
					A.TeacherID,
					C.TeacherPayTypeItemCenterPriceX
				from ClassOrderSlots A 
					inner join Teachers B on A.TeacherID=B.TeacherID 
					inner join TeacherPayTypeItems C on B.TeacherPayTypeItemID=C.TeacherPayTypeItemID 
				where A.ClassOrderID=$ClassOrderID 
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
			if ($ClassOrderTimeSlotCount==0){
				$ClassOrderPayTotalClassCount = 0;
			}else{
				$ClassOrderPayTotalClassCount = ($TeacherSlotCount / $ClassOrderTimeSlotCount) * $ClassOrderPayTotalWeekCount;//총 수업수 = (강사의 주당 슬랏수 / 수업당 슬랏수) * 신청주수
			}
			$ClassOrderPayDetailSellingPrice = $CenterPricePerTime * $TeacherPayTypeItemCenterPriceX * $ClassOrderPayTotalSlotCount;//판매가 = 센터 단가 * 교사배수 * 전체슬랏
			$ClassOrderPayDetailDiscountPrice = round( $ClassOrderPayDetailSellingPrice * ( $ClassOrderPayMonthDiscountRatio / 100 ), 0);//할인금액 = 판매가 * (할인율)
			$ClassOrderPayDetailPaymentPrice = $ClassOrderPayDetailSellingPrice - $ClassOrderPayDetailDiscountPrice;//실결제가 = 판매가 - 할인금액

			$ClassOrderPayClassSlotCount = $ClassOrderTimeSlotCount;//수업당 슬랏수
			if ($ClassOrderPayClassSlotCount==""){
				$ClassOrderPayClassSlotCount = 2;
			}
			
			if ($ClassOrderTimeSlotCount==0){
				$ClassOrderPayWeekClassCount = 0;
			}else{
				$ClassOrderPayWeekClassCount = $TeacherSlotCount / $ClassOrderTimeSlotCount; //강사의 주당 수업수 = 강사의 주당 슬랏수 / 수업당 슬랏수
			}

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
					ClassOrderPaySellingPrice=:ClassOrderPaySellingPrice,
					ClassOrderPayDiscountPrice=:ClassOrderPayDiscountPrice,
					ClassOrderPayPaymentPrice=:ClassOrderPayPaymentPrice,
					ClassOrderPayUseSavedMoneyPrice=:ClassOrderPayUseSavedMoneyPrice,
					ClassOrderPayUseCashPrice=:ClassOrderPayUseCashPrice,
					ClassOrderPayModiDateTime=now() 
				where ClassOrderPayID=:ClassOrderPayID ";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ClassOrderPaySellingPrice', $ClassOrderPaySellingPrice);
		$Stmt->bindParam(':ClassOrderPayDiscountPrice', $ClassOrderPayDiscountPrice);
		$Stmt->bindParam(':ClassOrderPayPaymentPrice', $ClassOrderPayPaymentPrice);
		$Stmt->bindParam(':ClassOrderPayUseSavedMoneyPrice', $ClassOrderPayUseSavedMoneyPrice);
		$Stmt->bindParam(':ClassOrderPayUseCashPrice', $ClassOrderPayUseCashPrice);
		$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
		$Stmt->execute();
		$Stmt = null;
		//=========================================================================================

		$ClassOrderPayCount++;



}
$Stmt_C = null;
?>

주문장 개수 : <?=$ClassOrderPayCount?>

</body>
</html>
<?
include_once('../includes/dbclose.php');
?>