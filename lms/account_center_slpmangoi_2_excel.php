<?php
$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = SLPMANGOI-TYPE2-".$SearchYear."-".$SearchMonth.".xls" );
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

$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

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
			A.ClassOrderPayID,
			A.ClassOrderPayUseCashPrice,
			A.ClassOrderPayPgFeeRatio,
			A.ClassOrderPayPgFeePrice,
			A.ClassOrderPayProgress,
			(select CenterID from Members where MemberID=(select MemberID from ClassOrders where ClassOrderID=(select ClassOrderID from ClassOrderPayDetails where ClassOrderPayID=A.ClassOrderPayID order by ClassOrderPayDetailID asc limit 0,1))) as CenterID 
			
		from ClassOrderPays A 
		where ".$AddSqlWhere." ";




$ViewTable2 = "select 
			AA.ClassOrderPayID,
			AA.ClassOrderPayUseCashPrice,
			AA.ClassOrderPayPgFeeRatio,
			AA.ClassOrderPayPgFeePrice,
			case
				when 
					AA.ClassOrderPayPgFeeRatio>0 then round(AA.ClassOrderPayUseCashPrice * (AA.ClassOrderPayPgFeeRatio/100),0)
				else 
					AA.ClassOrderPayPgFeePrice
			end as ClassOrderPayPgFee,
			AA.ClassOrderPayProgress,
			BB.CenterID,
			BB.CenterName,
			CC.MemberLoginID as CenterLoginID 
		from ($ViewTable) AA 
			inner join Centers BB on AA.CenterID=BB.CenterID 
			inner join Members CC on BB.CenterID=CC.CenterID and CC.MemberLevelID=12 
		";

$StudentCountSql = ",(
			select 
				count(*) 
			from Members AA_ 
				inner join Centers BB_ on AA_.CenterID=BB_.CenterID 
			where AA_.MemberLevelID=19 and AA_.MemberState<>0 and BB_.CenterID=AAA.CenterID 
			) as StudentCount 
			";

$StudyStudentCountSql = " ,
			(select count(*) from Members where CenterID=AAA.CenterID and MemberLevelID=19 and MemberState=1 and MemberID in (select MemberID from ClassOrders where ClassProgress=11 and (ClassOrderState=1 or ClassOrderState=2 or ClassOrderState=4))) as StudyStudentCount ";


$Sql = "select 
			AAA.CenterID,
			AAA.CenterName,
			AAA.CenterLoginID,
			sum(ClassOrderPayUseCashPrice) as ClassOrderPayUseCashPrice,
			sum(ClassOrderPayPgFee) as ClassOrderPayPgFee,
			count(CenterID) as ClassOrderPayCount
			".$StudentCountSql."
			".$StudyStudentCountSql."
		from ($ViewTable2) AAA 

		group by AAA.CenterID

		order by AAA.CenterName asc 
		";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>


							

<table border="1">
	<thead>
		<tr>
			<th nowrap>No</th>
			<th nowrap>학당아이디</th>
			<th nowrap>학당명</th>
			<th nowrap>학생수(현재)</th>
			<th nowrap>수강인원(현재)</th>
			<th nowrap>총 결제금액</th>
			<th nowrap>결제+환불 인원</th>
			<th nowrap>본사 로얄티<br>(A)</th>
			<th nowrap>결제 수수료<br>(B)</th>
			<th nowrap>실 정산금액<br>(Z=A-B)</th>
		</tr>
	</thead>
	<tbody>
		
		<?php
		$ListCount = 1;
		

		$SumSumClassOrderPayUseCashPrice = 0;
		$SumSumClassOrderPayCount = 0;
		$SumSumSupportCenterPrice = 0;
		$SumSumTrueSupportCenterPrice = 0;
		$SumSumCompayRoyalty = 0;
		$SumSumTrueCompayRoyalty = 0;

		while($Row = $Stmt->fetch()) {

			$CenterID = $Row["CenterID"];
			$CenterName = $Row["CenterName"];
			$CenterLoginID = $Row["CenterLoginID"];
			$SumClassOrderPayUseCashPrice = $Row["ClassOrderPayUseCashPrice"];
			$SumClassOrderPayPgFee = $Row["ClassOrderPayPgFee"];
			$SumClassOrderPayCount = $Row["ClassOrderPayCount"];

			$SumStudentCount = $Row["StudentCount"];
			$SumStudyStudentCount = $Row["StudyStudentCount"];

			$Sql2 = "select 
						A.*,
						D.ClassOrderPayUseCashPrice,
						D.ClassOrderPayPgFeeRatio,
						D.ClassOrderPayPgFeePrice
					from ClassOrderPayDetails A 
						inner join Teachers B on A.TeacherID=B.TeacherID 
						inner join TeacherPayTypeItems C on B.TeacherPayTypeItemID=C.TeacherPayTypeItemID  
						inner join ClassOrderPays D on A.ClassOrderPayID=D.ClassOrderPayID 
					where A.ClassOrderPayID in (select VV.ClassOrderPayID from ($ViewTable2) VV where VV.CenterID=$CenterID) 
					
					";

			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

			$TotalCompanyPrice = 0;
			$TrueTotalCompanyPrice = 0;

			$TotalCenterPrice = 0;
			$TrueTotalCenterPrice = 0;

			$SupportCenterPrice = 0;
			$TrueSupportCenterPrice = 0;

			$CompayRoyalty = 0;
			$TrueCompayRoyalty = 0;
			while($Row2 = $Stmt2->fetch()) {
				

				$TeacherPayTypeItemCenterPriceX = $Row2["TeacherPayTypeItemCenterPriceX"];
				$ClassOrderPayClassSlotCount = $Row2["ClassOrderPayClassSlotCount"];
				$ClassOrderPayMonthNumberID = $Row2["ClassOrderPayMonthNumberID"];

				$ClassOrderPayWeekClassCount = $Row2["ClassOrderPayWeekClassCount"];
				$ClassOrderPayMonthDiscountRatio = $Row2["ClassOrderPayMonthDiscountRatio"];

				$ClassOrderPayDetailPaymentPrice = $Row2["ClassOrderPayDetailPaymentPrice"];
				$ClassOrderPayDetailSellingPrice = $Row2["ClassOrderPayDetailSellingPrice"];
				$ClassOrderPayDetailDiscountPrice = $Row2["ClassOrderPayDetailDiscountPrice"];

				$ClassOrderPayUseCashPrice = $Row2["ClassOrderPayUseCashPrice"];
				$ClassOrderPayPgFeeRatio = $Row2["ClassOrderPayPgFeeRatio"];
				$ClassOrderPayPgFeePrice = $Row2["ClassOrderPayPgFeePrice"];

				if ($ClassOrderPayPgFeeRatio>0){
					$ClassOrderPayPgFee=$ClassOrderPayUseCashPrice*($ClassOrderPayPgFeeRatio/100);
				}else{
					$ClassOrderPayPgFee=$ClassOrderPayPgFeePrice;
				}
				$TrueClassOrderPayUseCashPrice = $ClassOrderPayUseCashPrice - $ClassOrderPayPgFee;


				$TempSupportCenterPricePerMonth = $SupportCenterPricePerMonth - ($SupportCenterPricePerMonth * ($ClassOrderPayMonthDiscountRatio/100));//할인금액 만큼 비율로 줄여줌
				$TempSupplyCompanyPricePerMonth = $SupplyCompanyPricePerMonth - ($SupplyCompanyPricePerMonth * ($ClassOrderPayMonthDiscountRatio/100));//할인금액 만큼 비율로 줄여줌

				//에듀비전 공급가 = 본사 10분당 공급가 * 수업슬랏 * 주당 회수 * 총주수
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



			$SumSumClassOrderPayUseCashPrice = $SumSumClassOrderPayUseCashPrice + $SumClassOrderPayUseCashPrice;
			$SumSumClassOrderPayCount = $SumSumClassOrderPayCount + $SumClassOrderPayCount;
			$SumSumSupportCenterPrice = $SumSumSupportCenterPrice + $SupportCenterPrice;
			$SumSumTrueSupportCenterPrice = $SumSumTrueSupportCenterPrice + $TrueSupportCenterPrice;
			$SumSumCompayRoyalty = $SumSumCompayRoyalty + $CompayRoyalty;
			$SumSumTrueCompayRoyalty = $SumSumTrueCompayRoyalty + $TrueCompayRoyalty;



		?>
		<tr>
			<td><?=$ListCount?><!-- No --></td>
			<td><?=$CenterLoginID?><!-- 학당아이디 --></td>
			<td><?=$CenterName?><!-- 학당명 --></td>
			<td><?=$SumStudentCount?><!-- 학생수 --></td>
			<td><?=$SumStudyStudentCount?><!-- 수강인원 --></td>
			<td><?=number_format($SumClassOrderPayUseCashPrice,0)?><!-- 총 결제금액 --></td>
			<td><?=number_format($SumClassOrderPayCount,0)?><!-- 결제+환불 인원 --></td>
			<td><?=number_format($CompayRoyalty, 0)?><!-- 본사 로얄티<br>(A) --></td>
			<td><?=number_format($CompayRoyalty-$TrueCompayRoyalty, 0)?><!-- 결제 수수료<br>(B) --></td>
			<td><?=number_format($TrueCompayRoyalty, 0)?><!-- 실 정산금액<br>(Z=A-B) --></td>
			

		</tr>
		<?
			$ListCount++;
		}
		$Stmt = null;
		?>
		<tr>
			<th colspan="5">합 계</th>

			<th><?=number_format($SumSumClassOrderPayUseCashPrice,0)?><!-- 총 결제금액 --></th>
			<th><?=number_format($SumSumClassOrderPayCount,0)?><!-- 결제+환불 인원 --></th>
			<th><?=number_format($SumSumCompayRoyalty, 0)?><!-- 본사 로얄티<br>(A) --></th>
			<th><?=number_format($SumSumCompayRoyalty-$SumSumTrueCompayRoyalty, 0)?><!-- 결제 수수료<br>(B) --></th>
			<th><?=number_format($SumSumTrueCompayRoyalty, 0)?><!-- 실 정산금액<br>(Z=A-B) --></th>
		</tr>
	</tbody>
</table>
						

