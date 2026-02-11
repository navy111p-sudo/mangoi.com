<?php
$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = SLPMANGOI-DETAIL-".$SearchYear."-".$SearchMonth.".xls" );
header( "Content-Description: PHP4 Generated Data" );

include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');


$SLP_BranchID_0=42;//gangseo
$SLP_BranchID_1=107;//seodaemoon
$SLP_BranchID_2=113;//slp
$SLP_BranchID_3=114;//soowon

$SupportCenterPricePerMonth = 2500;//학당지원금 10분당 (월)
$SupplyCompanyPricePerMonth = 7500;//공급가 10분당 (월)

$AddSqlWhere = "1=1";



if ($SearchYear==""){
	$SearchYear = date("Y");
}

if ($SearchMonth==""){
	$SearchMonth = date("m");
}

$AddSqlWhere = $AddSqlWhere . " and date_format(A.ClassOrderPayPaymentDateTime, '%Y')='".$SearchYear."' ";
$AddSqlWhere = $AddSqlWhere . " and date_format(A.ClassOrderPayPaymentDateTime, '%m')='".substr("0".$SearchMonth,-2)."' ";

$AddSqlWhere = $AddSqlWhere . " and A.ClassOrderPayType=0 ";//B2C
$AddSqlWhere = $AddSqlWhere . " and (A.ClassOrderPayProgress=21 or A.ClassOrderPayProgress=31 or A.ClassOrderPayProgress=41) ";
$AddSqlWhere = $AddSqlWhere . " and A.ClassOrderPayID in (
															select 
																ClassOrderPayID 
															from ClassOrderPayDetails 
															where ClassOrderID in (
																					select 
																						AAA.ClassOrderID 
																					from ClassOrders AAA 
																						inner join Members BBB on AAA.MemberID=BBB.MemberID
																						inner join Centers CCC on BBB.CenterID=CCC.CenterID 
																					where 
																						(
																							CCC.BranchID=".$SLP_BranchID_0."
																							or 
																							CCC.BranchID=".$SLP_BranchID_1."
																							or 
																							CCC.BranchID=".$SLP_BranchID_2."
																							or 
																							CCC.BranchID=".$SLP_BranchID_3."
																						)
																				  )
														 ) ";


$ViewTable = "
		select 
			A.*,
			(select MemberID from Members where MemberID=(select MemberID from ClassOrders where ClassOrderID=(select ClassOrderID from ClassOrderPayDetails where ClassOrderPayID=A.ClassOrderPayID order by ClassOrderPayDetailID asc limit 0,1))) as StudentID 
			
		from ClassOrderPays A 
		where ".$AddSqlWhere." ";

$Sql = "select 
			V.*

		from ($ViewTable) V
		order by V.ClassOrderPayPaymentDateTime desc
		";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>

							
							
<table border="1">
	<thead>
		<tr>
			<th nowrap>No</th>
			<th nowrap>결제승인일</th>
			<th nowrap>학생아이디</th>
			<th nowrap>학생이름</th>
			<th nowrap>생년월일</th>
			<th nowrap>수업형태</th>
			<th nowrap>강사</th>
			<th nowrap>수업횟수</th>
			<th nowrap>수업시간</th>
			<th nowrap>기간</th>
			<th nowrap>승인일시</th>
			<th nowrap>결제방식</th>
			<th nowrap>승인금액<br>(A)</th>
			<th nowrap>결제수수료<br>(B)</th>
			<th nowrap>실매출<br>(A-B)</th>
			<th nowrap>에듀비전<br>공급가</th>
			<th nowrap>에듀비전<br>실공급가</th>
			<th nowrap>학당<br>지원금</th>
			<th nowrap>학당<br>실지원금</th>
			<th nowrap>본사<br>로얄티</th>
			<th nowrap>본사<br>실로얄티</th>
			<th nowrap>대리점명</th>
			<th nowrap>대리점아이디</th>
			<th nowrap>지사명</th>
			<th nowrap>지사아이디</th>
			<!--
			<th nowrap>상태</th>
			<th nowrap>유효성검사</th>
			-->
		</tr>
	</thead>
	<tbody>
		
		<?php
		$ListCount = 1;

		$SumClassOrderPayUseCashPrice = 0;
		$SumClassOrderPayPgFee = 0;
		$SumTrueClassOrderPayUseCashPrice = 0;
		$SumTotalCompanyPrice = 0;
		$SumTrueTotalCompanyPrice = 0;
		$SumSupportCenterPrice = 0;
		$SumTrueSupportCenterPrice = 0;
		$SumCompayRoyalty = 0;
		$SumTrueCompayRoyalty = 0;

		while($Row = $Stmt->fetch()) {

			$ClassOrderPayID = $Row["ClassOrderPayID"];
			$ClassOrderPayPaymentDateTime = $Row["ClassOrderPayPaymentDateTime"];

			$StudentID = $Row["StudentID"];


			$ClassOrderPayUseCashPaymentType = $Row["ClassOrderPayUseCashPaymentType"];
			$ClassOrderPayUseCashPrice = $Row["ClassOrderPayUseCashPrice"];
			$ClassOrderPayPgFeeRatio = $Row["ClassOrderPayPgFeeRatio"];
			$ClassOrderPayPgFeePrice = $Row["ClassOrderPayPgFeePrice"];
			

			if ($ClassOrderPayPgFeeRatio>0){
				$ClassOrderPayPgFee=$ClassOrderPayUseCashPrice*($ClassOrderPayPgFeeRatio/100);
			}else{
				$ClassOrderPayPgFee=$ClassOrderPayPgFeePrice;
			}
			$TrueClassOrderPayUseCashPrice = $ClassOrderPayUseCashPrice - $ClassOrderPayPgFee;


			$StrClassOrderPayUseCashPaymentType="-";
			if ($ClassOrderPayUseCashPaymentType==1){
				$StrClassOrderPayUseCashPaymentType="카드";
			}else if ($ClassOrderPayUseCashPaymentType==2){
				$StrClassOrderPayUseCashPaymentType="실시간이체";
			}else if ($ClassOrderPayUseCashPaymentType==3){
				$StrClassOrderPayUseCashPaymentType="가상계좌";
			}else if ($ClassOrderPayUseCashPaymentType==4){
				$StrClassOrderPayUseCashPaymentType="계좌입금";
			}else if ($ClassOrderPayUseCashPaymentType==5){
				$StrClassOrderPayUseCashPaymentType="오프라인";
			}else if ($ClassOrderPayUseCashPaymentType==9){
				$StrClassOrderPayUseCashPaymentType="기타";
			}

			$StudyType = "화상영어";

			$Sql2 = "select 
						A.MemberLoginID as StudentLoginID,
						A.MemberName as StudentName,
						A.MemberBirthday as StudentBirthday,
						B.CenterName, 
						C.BranchName,
						BB.MemberLoginID as CenterLoginID,
						CC.MemberLoginID as BranchLoginID
					from Members A 
						inner join Centers B on A.CenterID=B.CenterID
						inner join Branches C on B.BranchID=C.BranchID
						
						inner join Members BB on B.CenterID=BB.CenterID and BB.MemberLevelID=12 
						inner join Members CC on C.BranchID=CC.BranchID and CC.MemberLevelID=9 
					where A.MemberID=$StudentID 
					";

			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
			$Row2 = $Stmt2->fetch();
			$Stmt2 = null;

			$StudentLoginID = $Row2["StudentLoginID"];
			$StudentName = $Row2["StudentName"];
			$StudentBirthday = $Row2["StudentBirthday"];
			$CenterName = $Row2["CenterName"];
			$BranchName = $Row2["BranchName"];
			$CenterLoginID = $Row2["CenterLoginID"];
			$BranchLoginID = $Row2["BranchLoginID"];

			

			$Sql2 = "select 
						A.*, 
						C.TeacherPayTypeItemTitle2 
					from ClassOrderPayDetails A 
						inner join Teachers B on A.TeacherID=B.TeacherID 
						inner join TeacherPayTypeItems C on B.TeacherPayTypeItemID=C.TeacherPayTypeItemID 
					where A.ClassOrderPayID=$ClassOrderPayID 
					
					";

			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
			$StrTeacherPayType = "";

			$TotalCompanyPrice = 0;
			$TrueTotalCompanyPrice = 0;
			$TotalCenterPrice = 0;
			$TrueTotalCenterPrice = 0;
			$SupportCenterPrice = 0;
			$TrueSupportCenterPrice = 0;
			$CompayRoyalty = 0;
			$TrueCompayRoyalty = 0;
			$SumClassOrderPayWeekClassCount = 0;

			while($Row2 = $Stmt2->fetch()) {
				
				$TeacherPayTypeItemTitle2 = $Row2["TeacherPayTypeItemTitle2"];

				$TeacherPayTypeItemCenterPriceX = $Row2["TeacherPayTypeItemCenterPriceX"];
				$ClassOrderPayClassSlotCount = $Row2["ClassOrderPayClassSlotCount"];
				$ClassOrderPayMonthNumberID = $Row2["ClassOrderPayMonthNumberID"];
				$ClassOrderPayTotalWeekCount = $Row2["ClassOrderPayTotalWeekCount"];
				$ClassOrderPayWeekClassCount = $Row2["ClassOrderPayWeekClassCount"];
				$CompanyPricePerTime = $Row2["CompanyPricePerTime"];
				$CenterPricePerTime = $Row2["CenterPricePerTime"];
				$ClassOrderPayMonthDiscountRatio = $Row2["ClassOrderPayMonthDiscountRatio"];

				$ClassOrderPayDetailPaymentPrice = $Row2["ClassOrderPayDetailPaymentPrice"];
				$ClassOrderPayDetailSellingPrice = $Row2["ClassOrderPayDetailSellingPrice"];
				$ClassOrderPayDetailDiscountPrice = $Row2["ClassOrderPayDetailDiscountPrice"];


				$TempSupportCenterPricePerMonth = $SupportCenterPricePerMonth - ($SupportCenterPricePerMonth * ($ClassOrderPayMonthDiscountRatio/100));//할인금액 만큼 비율로 줄여줌
				$TempSupplyCompanyPricePerMonth = $SupplyCompanyPricePerMonth - ($SupplyCompanyPricePerMonth * ($ClassOrderPayMonthDiscountRatio/100));//할인금액 만큼 비율로 줄여줌

				if (strpos($StrTeacherPayType,$TeacherPayTypeItemTitle2)===false){
					if ($StrTeacherPayType!=""){
						$StrTeacherPayType = $StrTeacherPayType . ", ";
					}
					$StrTeacherPayType = $StrTeacherPayType . $TeacherPayTypeItemTitle2;
				}
				


				$SumClassOrderPayWeekClassCount = $SumClassOrderPayWeekClassCount + $ClassOrderPayWeekClassCount;

				//에듀비전 공급가 = 본사 10분당 공급가 * 수업슬랏 * 주당 회수 * 총주수
				//$TotalCompanyPrice = $CompanyPricePerTime * $ClassOrderPayClassSlotCount * $ClassOrderPayWeekClassCount * $ClassOrderPayTotalWeekCount;//DB 계산가
				$TempTotalCompanyPrice = $TempSupplyCompanyPricePerMonth * $ClassOrderPayWeekClassCount * $ClassOrderPayClassSlotCount * $ClassOrderPayMonthNumberID * $TeacherPayTypeItemCenterPriceX;//고정가
				$TotalCompanyPrice = $TotalCompanyPrice + $TempTotalCompanyPrice;
				
				
				//에듀비전 실공급가  
				$TempTrueTotalCompanyPrice = $TempTotalCompanyPrice * ($TrueClassOrderPayUseCashPrice/$ClassOrderPayUseCashPrice);
				$TrueTotalCompanyPrice = $TrueTotalCompanyPrice + $TempTrueTotalCompanyPrice;
				

				//학당 판매가 = 학당 10분당 판매가 * 수업슬랏 * 주당 회수 * 총주수
				$TempTotalCenterPrice = $ClassOrderPayDetailPaymentPrice;
				$TotalCenterPrice = $TotalCenterPrice + $TempTotalCenterPrice;
				

				//학당 실판매가
				$TempTrueTotalCenterPrice = $TempTotalCenterPrice * ($TrueClassOrderPayUseCashPrice/$ClassOrderPayUseCashPrice);
				$TrueTotalCenterPrice = $TrueTotalCenterPrice + $TempTrueTotalCenterPrice;
				 
				
				//학당 지원금
				$TempSupportCenterPrice = $TempSupportCenterPricePerMonth * $ClassOrderPayWeekClassCount * $ClassOrderPayClassSlotCount * $ClassOrderPayMonthNumberID * $TeacherPayTypeItemCenterPriceX;
				$SupportCenterPrice = $SupportCenterPrice + $TempSupportCenterPrice;
				
				
				//실 학당 지원금
				$TempTrueSupportCenterPrice = $TempSupportCenterPrice * ($TrueClassOrderPayUseCashPrice/$ClassOrderPayUseCashPrice);
				$TrueSupportCenterPrice = $TrueSupportCenterPrice + $TempTrueSupportCenterPrice;
				

				//본사 로얄티
				$TempCompayRoyalty = $TempTotalCenterPrice - $TempTotalCompanyPrice - $TempSupportCenterPrice;
				$CompayRoyalty = $CompayRoyalty + $TempCompayRoyalty;
				

				//본사 실로얄티
				$TempTrueCompayRoyalty = $TempCompayRoyalty * ($TrueClassOrderPayUseCashPrice/$ClassOrderPayUseCashPrice);
				$TrueCompayRoyalty = $TrueCompayRoyalty + $TempTrueCompayRoyalty;


			}
			$Stmt2 = null;



			$SumClassOrderPayUseCashPrice = $SumClassOrderPayUseCashPrice + $ClassOrderPayUseCashPrice;
			$SumClassOrderPayPgFee = $SumClassOrderPayPgFee + $ClassOrderPayPgFee;
			$SumTrueClassOrderPayUseCashPrice = $SumTrueClassOrderPayUseCashPrice + $TrueClassOrderPayUseCashPrice;
			$SumTotalCompanyPrice = $SumTotalCompanyPrice + $TotalCompanyPrice;
			$SumTrueTotalCompanyPrice = $SumTrueTotalCompanyPrice + $TrueTotalCompanyPrice;
			$SumSupportCenterPrice = $SumSupportCenterPrice + $SupportCenterPrice;
			$SumTrueSupportCenterPrice = $SumTrueSupportCenterPrice + $TrueSupportCenterPrice;
			$SumCompayRoyalty = $SumCompayRoyalty + $CompayRoyalty;
			$SumTrueCompayRoyalty = $SumTrueCompayRoyalty + $TrueCompayRoyalty;

		?>
		<tr>
			<td><?=$ListCount?> / <?=$ClassOrderPayID?></td>
			<td><?=substr($ClassOrderPayPaymentDateTime,0,10)?><!-- 결제승인일 --></td>
			<td><?=$StudentLoginID?><!-- 학생아이디 --></td>
			<td><?=$StudentName?><!-- 학생이름 --></td>
			<td><?=$StudentBirthday?><!-- 생년월일 --></td>
			<td><?=$StudyType?><!-- 수업형태 --></td>
			<td><?=$StrTeacherPayType?><!-- 강사 --></td>
			<td><?=$SumClassOrderPayWeekClassCount?><!-- 수업횟수 --></td>
			<td><?=$ClassOrderPayClassSlotCount*10?>분<!-- 수업시간 --></td>
			<td><?=$ClassOrderPayMonthNumberID?>개월<!-- 기간 --></td>
			<td><?=$ClassOrderPayPaymentDateTime?><!-- 승인일시 --></td>
			<td><?=$StrClassOrderPayUseCashPaymentType?><!-- 결제방식 --></td>
			<td><?=number_format($ClassOrderPayUseCashPrice,0)?><!-- 승인금액<br>(A) --></td>
			<td><?=number_format($ClassOrderPayPgFee,0)?><!-- 결제수수료<br>(B) --></td>
			<td><?=number_format($TrueClassOrderPayUseCashPrice,0)?><!-- 실매출<br>(A-B) --></td>
			<td><?=number_format($TotalCompanyPrice, 0)?><!-- 에듀비전<br>공급가 --></td>
			<td><?=number_format($TrueTotalCompanyPrice, 0)?><!-- 에듀비전<br>실공급가 --></td>
			<td><?=number_format($SupportCenterPrice, 0)?><!--학당<br>지원금--></td>
			<td><?=number_format($TrueSupportCenterPrice, 0)?><!--학당<br>실지원금--></td>
			<td><?=number_format($CompayRoyalty, 0)?><!--본사<br>로얄티--></td>
			<td><?=number_format($TrueCompayRoyalty, 0)?><!--본사<br>실로얄티--></td>
			<td><?=$CenterName?></td>
			<td><?=$CenterLoginID?></td>
			<td><?=$BranchName?></td>
			<td><?=$BranchLoginID?></td>
			<!--
			<td>상태</td>
			<td>유효성검사</td>
			-->
		</tr>
		<?
			$ListCount++;
		}
		$Stmt = null;
		?>
		<tr>
			<th colspan="12">합 계</th>

			<th><?=number_format($SumClassOrderPayUseCashPrice,0)?><!-- 승인금액<br>(A) --></th>
			<th><?=number_format($SumClassOrderPayPgFee,0)?><!-- 결제수수료<br>(B) --></th>
			<th><?=number_format($SumTrueClassOrderPayUseCashPrice,0)?><!-- 실매출<br>(A-B) --></th>
			<th><?=number_format($SumTotalCompanyPrice, 0)?><!-- 에듀비전<br>공급가 --></th>
			<th><?=number_format($SumTrueTotalCompanyPrice, 0)?><!-- 에듀비전<br>실공급가 --></th>
			<th><?=number_format($SumSupportCenterPrice, 0)?><!--학당<br>지원금--></th>
			<th><?=number_format($SumTrueSupportCenterPrice, 0)?><!--학당<br>실지원금--></th>
			<th><?=number_format($SumCompayRoyalty, 0)?><!--본사<br>로얄티--></th>
			<th><?=number_format($SumTrueCompayRoyalty, 0)?><!--본사<br>실로얄티--></th>
			<th colspan="4"></th>
			<!--
			<th>상태</th>
			<th>유효성검사</th>
			-->
		</tr>
	</tbody>
</table>
						


