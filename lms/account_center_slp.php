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
$SubMenuID = 21051;
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


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$SLP_정산_상세[$LangID]?></h3>
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
										<th nowrap><?=$결제승인일[$LangID]?></th>
										<th nowrap><?=$학생아이디[$LangID]?></th>
										<th nowrap><?=$학생이름[$LangID]?></th>
										<th nowrap><?=$생년월일[$LangID]?></th>
										<th nowrap><?=$수업형태[$LangID]?></th>
										<th nowrap><?=$강사[$LangID]?></th>
										<th nowrap><?=$수업횟수[$LangID]?></th>
										<th nowrap><?=$수업시간[$LangID]?></th>
										<th nowrap><?=$기간[$LangID]?></th>
										<th nowrap><?=$승인일시[$LangID]?></th>
										<th nowrap><?=$결제방식[$LangID]?></th>
										<th nowrap><?=$승인금액[$LangID]?><br>(A)</th>
										<th nowrap><?=$결제수수료[$LangID]?><br>(B)</th>
										<th nowrap><?=$실매출[$LangID]?><br>(A-B)</th>
										<th nowrap><?=$에듀비전[$LangID]?><br><?=$공급가[$LangID]?></th>
										<th nowrap><?=$에듀비전[$LangID]?><br><?=$실공급가[$LangID]?></th>
										<th nowrap><?=$학당[$LangID]?><br><?=$지원금[$LangID]?></th>
										<th nowrap><?=$학당[$LangID]?><br><?=$실지원금[$LangID]?></th>
										<th nowrap><?=$본사[$LangID]?><br><?=$로얄티[$LangID]?></th>
										<th nowrap><?=$본사[$LangID]?><br><?=$실로얄티[$LangID]?></th>
										<th nowrap><?=$대리점명[$LangID]?></th>
										<th nowrap><?=$월선택[$LangID]?></th>
										<th nowrap><?=$지사명[$LangID]?></th>
										<th nowrap><?=$지사아이디[$LangID]?></th>
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
												where A.ClassOrderPayID=$ClassOrderPayID and A.ClassOrderPayDetailState=1 
												
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
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=substr($ClassOrderPayPaymentDateTime,0,10)?><!-- 결제승인일 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StudentLoginID?><!-- 학생아이디 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StudentName?><!-- 학생이름 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StudentBirthday?><!-- 생년월일 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StudyType?><!-- 수업형태 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrTeacherPayType?><!-- 강사 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$SumClassOrderPayWeekClassCount?><!-- 수업횟수 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderPayClassSlotCount*10?>분<!-- 수업시간 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderPayMonthNumberID?>개월<!-- 기간 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderPayPaymentDateTime?><!-- 승인일시 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrClassOrderPayUseCashPaymentType?><!-- 결제방식 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ClassOrderPayUseCashPrice,0)?><!-- 승인금액<br>(A) --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ClassOrderPayPgFee,0)?><!-- 결제수수료<br>(B) --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TrueClassOrderPayUseCashPrice,0)?><!-- 실매출<br>(A-B) --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalCompanyPrice, 0)?><!-- 에듀비전<br>공급가 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TrueTotalCompanyPrice, 0)?><!-- 에듀비전<br>실공급가 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SupportCenterPrice, 0)?><!--학당<br>지원금--></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TrueSupportCenterPrice, 0)?><!--학당<br>실지원금--></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($CompayRoyalty, 0)?><!--본사<br>로얄티--></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TrueCompayRoyalty, 0)?><!--본사<br>실로얄티--></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterLoginID?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchLoginID?></td>
										<!--
										<td class="uk-text-nowrap uk-table-td-center">상태</td>
										<td class="uk-text-nowrap uk-table-td-center">유효성검사</td>
										-->
									</tr>
									<?
										$ListCount++;
									}
									$Stmt = null;
									?>
									<tr>
										<th class="uk-text-nowrap uk-table-td-center" colspan="12"><?=$합계[$LangID]?></th>
						
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumClassOrderPayUseCashPrice,0)?><!-- 승인금액<br>(A) --></th>
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumClassOrderPayPgFee,0)?><!-- 결제수수료<br>(B) --></th>
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTrueClassOrderPayUseCashPrice,0)?><!-- 실매출<br>(A-B) --></th>
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTotalCompanyPrice, 0)?><!-- 에듀비전<br>공급가 --></th>
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTrueTotalCompanyPrice, 0)?><!-- 에듀비전<br>실공급가 --></th>
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumSupportCenterPrice, 0)?><!--학당<br>지원금--></th>
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTrueSupportCenterPrice, 0)?><!--학당<br>실지원금--></th>
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumCompayRoyalty, 0)?><!--본사<br>로얄티--></th>
										<th class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTrueCompayRoyalty, 0)?><!--본사<br>실로얄티--></th>
										<th class="uk-text-nowrap uk-table-td-center" colspan="4"></th>
										<!--
										<th class="uk-text-nowrap uk-table-td-center">상태</th>
										<th class="uk-text-nowrap uk-table-td-center">유효성검사</th>
										-->
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
	document.SearchForm.action = "account_center_slp.php";
	document.SearchForm.submit();
}

function ExcelDown(){
	location.href = "account_center_slp_excel.php?SearchYear=<?=$SearchYear?>&SearchMonth=<?=$SearchMonth?>";
}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>