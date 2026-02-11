<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');


if ($_LINK_ADMIN_LEVEL_ID_>10){
	header("Location: center_form.php?CenterID=".$_LINK_ADMIN_CENTER_ID_); 
	exit;
}

?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
?>

<?
include_once('./inc_common_list_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 21;
$SubMenuID = 21053;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>

 

<?php
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


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$SLP_정산_본사로얄티[$LangID]?></h3>
		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">



					<div class="uk-width-medium-1-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
						<select id="SearchYear" name="SearchYear" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%;"/>
							<option value=""><?=$년도선택[$LangID]?></option>
							<?
							for ($iiii=$SearchYear-1;$iiii<=$SearchYear+1;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
						<select id="SearchMonth" name="SearchMonth" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;"/>
							<option value=""><?=$월선택[$LangID]?></option>
							<?
							for ($iiii=1;$iiii<=12;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
							<?
							}
							?>
						</select>
					</div>


					
					<div class="uk-width-medium-3-10 uk-text-center">
						<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
						<a href="javascript:ExcelDown();" class="md-btn md-btn-primary uk-margin-small-top">EXCEL DOWN</a>
					</div>
					
					
				</div>
			</div>
		</div>
		</form>


		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							
							
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap><?=$학당아이디[$LangID]?></th>
										<th nowrap><?=$학당명[$LangID]?></th>
										<th nowrap><?=$학생수_현재[$LangID]?></th>
										<th nowrap><?=$수강인원_현재[$LangID]?></th>
										<th nowrap><?=$총_결제금액[$LangID]?></th>
										<th nowrap><?=$결제_환불_인원[$LangID]?></th>
										<th nowrap><?=$본사_로얄티[$LangID]?><br>(A)</th>
										<th nowrap><?=$결제_수수료[$LangID]?><br>(B)</th>
										<th nowrap><?=$실_정산금액[$LangID]?><br>(Z=A-B)</th>
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
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?><!-- No --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterLoginID?><!-- 학당아이디 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterName?><!-- 학당명 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumStudentCount,0)?><!-- 학생수 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumStudyStudentCount,0)?><!-- 수강인원 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumClassOrderPayUseCashPrice,0)?><!-- 총 결제금액 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumClassOrderPayCount,0)?><!-- 결제+환불 인원 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($CompayRoyalty, 0)?><!-- 본사 로얄티<br>(A) --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($CompayRoyalty-$TrueCompayRoyalty, 0)?><!-- 결제 수수료<br>(B) --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TrueCompayRoyalty, 0)?><!-- 실 정산금액<br>(Z=A-B) --></td>
										

									</tr>
									<?
										$ListCount++;
									}
									$Stmt = null;
									?>
									<tr>
										<th class="uk-text-nowrap uk-table-td-center" colspan="5"><?=$합계[$LangID]?></th>
						
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumSumClassOrderPayUseCashPrice,0)?><!-- 총 결제금액 --></th>
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumSumClassOrderPayCount,0)?><!-- 결제+환불 인원 --></th>
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumSumCompayRoyalty, 0)?><!-- 본사 로얄티<br>(A) --></th>
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumSumCompayRoyalty-$SumSumTrueCompayRoyalty, 0)?><!-- 결제 수수료<br>(B) --></th>
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumSumTrueCompayRoyalty, 0)?><!-- 실 정산금액<br>(Z=A-B) --></th>
									</tr>
								</tbody>
							</table>
						
						
						
						
						</div>




					</div>
				</div>
			</div>
		</div>

	</div>
</div>



<?
include_once('./inc_common_list_js.php');
?>
<!-- ============== only this page js ============== -->


<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "account_center_slpmangoi_2.php";
	document.SearchForm.submit();
}

function ExcelDown(){
	location.href = "account_center_slpmangoi_2_excel.php?SearchYear=<?=$SearchYear?>&SearchMonth=<?=$SearchMonth?>";
}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>