<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
$DenyGuest = true;
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_07";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<?php
include_once('./includes/common_header.php');

$Sql = "select SubID from Subs where SubCode=:SubCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SubCode', $SubCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$SubID = $Row["SubID"];


if ($UseMain==1){
	$Sql = "select * from Main limit 0,1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MainLayout = $Row["MainLayout"];
	$MainLayoutCss = $Row["MainLayoutCss"];
	$MainLayoutJavascript = $Row["MainLayoutJavascript"];
	list($MainLayoutTop, $MainLayoutBottom) = explode("{{Page}}", $MainLayout);
}else{
	$MainLayoutTop = "";
	$MainLayoutBottom = "";
	$MainLayoutCss = "";
	$MainLayoutJavascript = "";
}


if ($UseSub==1){
	$Sql = "select * from Subs where SubID=:SubID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SubID', $SubID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$SubLayout = $Row["SubLayout"];
	$SubLayoutCss = $Row["SubLayoutCss"];
	$SubLayoutJavascript = $Row["SubLayoutJavascript"];
	list($SubLayoutTop, $SubLayoutBottom) = explode("{{Page}}", $SubLayout);
}else{
	$SubLayoutTop = "";
	$SubLayoutBottom = "";
	$SubLayoutCss = "";
	$SubLayoutJavascript = "";
}


if (trim($MainLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $MainLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

if (trim($SubLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $SubLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

?>
</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<?php
// $MainLayoutTop = convertHTML(trim($MainLayoutTop));
// $SubLayoutTop = convertHTML(trim($SubLayoutTop));
// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $MainLayoutTop = convertHTML(trim("{{Piece(header_gumiivyleague)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_07_gumiivyleague)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_gumiivyleague_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.engedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engedu)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engedu_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));


} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }

echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";
?>

<div class="sub_wrap">       
    <div class="sub_title_common_wrap"><h2 class="sub_title_common"><b>마이</b>페이지</h2></div>

    <section class="mypage_wrap">
        <div class="mypage_area">

			<?
			$HideLinkBtn = 0;
			include_once('mypage_student_info_include.php');
			?>


			<?
			$MemberID = $_LINK_MEMBER_ID_;
			
			$Sql = "
				select 
					A.MemberPayType,
					A.MemberStudyEndDate,
					B.CenterPayType 
				from Members A 
					inner join Centers B on A.CenterID=B.CenterID 
				where A.MemberID=$MemberID and MemberLevelID=19 
			";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;
			$MemberPayType = $Row["MemberPayType"];
			$MemberStudyEndDate = $Row["MemberStudyEndDate"];
			$CenterPayType = $Row["CenterPayType"];


			if (!$CenterPayType){
				$CenterPayType = 0;
			}
			?>

			<div class="mypage_inner">
				<?
				$Sql = "
						select 
								count(*) as TotalCount
						from ClassOrders A 
							inner join ClassProducts B on A.ClassProductID=B.ClassProductID 
						where A.MemberID=$MemberID and A.ClassProductID=1 and A.ClassProgress=11 and A.ClassOrderState >= 1  ";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$TotalCount = $Row["TotalCount"];


				$Sql = "select 
							A.*,
							B.ClassProductName
						from ClassOrders A 
							inner join ClassProducts B on A.ClassProductID=B.ClassProductID 
						where A.MemberID=$MemberID and A.ClassProductID=1 and A.ClassProgress=11 and A.ClassOrderState >= 1 
						order by A.ClassOrderRegDateTime desc";

				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);

				?>

				<h3 class="caption_left_br"><trn class="TrnTag">나의 <b>수강신청정보</b></trn><span>Total : <b id="PaymentTotalCount"><?=number_format($TotalCount,0)?></b></span></h3>
				<div class="overflow_table">
					<table class="mypage_payment_table">
						<col width="7%">
						<col width="14%">
						<col width="">
						<col width="14%">
						<col width="14%">
						<col width="14%">
						<col width="14%">
						<col width="14%">
						<col width="16%">
						<tr>
							<th class="TrnTag">번호</th>
							<th class="TrnTag">신청일</th>
							<th class="TrnTag">수강명</th>
							<th class="TrnTag">수업회수</th>
							<th class="TrnTag">시작일</th>
							<th class="TrnTag">종료일</th>
							<th class="TrnTag">상태</th>
							<th class="TrnTag">수강연장</th>
						</tr>
						<?	
						$ii=1;
						while($Row = $Stmt->fetch()) {

							$ClassProductID = $Row["ClassProductID"];
							$ClassMemberType = $Row["ClassMemberType"];
							$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
							$ClassOrderWeekCountID = $Row["ClassOrderWeekCountID"];
							$ClassOrderStartDate = $Row["ClassOrderStartDate"];
							$ClassOrderEndDate = $Row["ClassOrderEndDate"];
							$ClassOrderID = $Row["ClassOrderID"];

							$ClassOrderState = $Row["ClassOrderState"];
							$ClassProgress = $Row["ClassProgress"];
							$ClassOrderRegDateTime = $Row["ClassOrderRegDateTime"];

							$ClassProductName = $Row["ClassProductName"];

							//-1:신청중 0:완전삭제 1:정상 2:종료대상 3:종료완료 4:장기홀드 5:레벨테스트완료 6:미응시
							if ($ClassOrderState==1){
								$StrClassOrderState = "<trn class=\"TrnTag\">수업진행</trn>";
							}else if ($ClassOrderState==2){
								$StrClassOrderState = "<trn class=\"TrnTag\">종료대상</trn>";
							}else if ($ClassOrderState==3){
								$StrClassOrderState = "<trn class=\"TrnTag\">종료</trn>";
							}else if ($ClassOrderState==4){
								$StrClassOrderState = "<trn class=\"TrnTag\">장기홀드</trn>";
							}

							if ($ClassOrderEndDate!=""){
								$StrClassOrderState = substr(str_replace("-",".",$ClassOrderEndDate),-8)."(<trn class=\"TrnTag\">종료</trn>)";
								$MemberStudyEndDate = $ClassOrderEndDate;
							}

							
						?>
						<tr>
							<td><?=$TotalCount-$ii+1?></td>
							<td><?=str_replace("-",".",substr($ClassOrderRegDateTime,0,10))?></td>
							<td><a><?=$ClassProductName?></a></td>
							<td><?=$ClassOrderWeekCountID?> <trn class="TrnTag">회 / 주</trn></td>
							<td><?=$ClassOrderStartDate?></td>
							<td><?=$MemberStudyEndDate?></td>
							<td><?=$StrClassOrderState?></td>
							<td>
								<?if ($ClassOrderEndDate!=""){?>
									-
								<?}else{?>
									<?if ($ClassOrderState==1 || $ClassOrderState==2) {?>
										<?if ($CenterPayType==2){?>
											<a href="javascript:PayPreAction(<?=$ClassOrderID?>)" class="study_repeat_btn_2 TrnTag">연장하기</a>
										<?}else{?>
											<?if ($MemberPayType==1){?>
												<a href="javascript:PayPreAction(<?=$ClassOrderID?>)" class="study_repeat_btn_2 TrnTag">연장하기</a>
											<?}else{?>
												<a href="javascript:PayPreActionErr(<?=$ClassOrderID?>)" class="study_repeat_btn_2 TrnTag">연장하기</a>
											<?}?>
										<?}?>
									<?}else{?>
									-
									<?}?>
								<?}?>
							</td>
							
						</tr>

						<?
						
							$ii++;
						}
						$Stmt = null;
						?>
					</table>
				</div>


				<script>

				function PayPreAction(ClassOrderID){
					url = "./ajax_set_class_order_pay_dev.php";
					location.href = url + "?ClassOrderID="+ClassOrderID+"&ClassOrderMode=HOME";
					/*
					$.ajax(url, {
						data: {
							ClassOrderID: ClassOrderID,
							ClassOrderMode: "HOME"
						},
						success: function (data) {
							ClassOrderPayID = data.ClassOrderPayID;
							ClassOrderPayNumber = data.ClassOrderPayNumber;

							OpenPayForm(ClassOrderID, ClassOrderPayID, ClassOrderPayNumber);
						},
						error: function () {

						}
					});
					*/

				}

				function PayPreActionErr(ClassOrderID){
					alert("회원님은 개인결제를 이용하지 않습니다. 관리자에게 문의해 주세요.");
				}


				function OpenPayForm(ClassOrderID, ClassOrderPayID, ClassOrderPayNumber){
					openurl = "./pop_class_order_pay_form.php?ClassOrderID="+ClassOrderID+"&ClassOrderPayID="+ClassOrderPayID+"&ClassOrderPayNumber="+ClassOrderPayNumber+"&ClassOrderMode=HOME";
					$.colorbox({	
						href:openurl
						,width:"95%" 
						,height:"95%"
						,maxWidth: "850"
						,maxHeight: "750"
						,title:""
						,iframe:true 
						,scrolling:true
						//,onClosed:function(){location.reload(true);}
						//,onComplete:function(){alert(1);}
					}); 
				}
				</script>


			<?
			if ($CenterPayType==2){
			?>
				<?
				$Sql = "
						select 
								count(*) as TotalCount
						from ClassOrderPays A 
						where A.ClassOrderPayPaymentMemberID=$MemberID and A.ClassOrderPayProgress > 1 ";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$TotalCount = $Row["TotalCount"];
				
				
				
				$Sql = "select A.* from ClassOrderPays A where A.ClassOrderPayPaymentMemberID=$MemberID and A.ClassOrderPayProgress > 1 order by A.ClassOrderPayDateTime desc";

				?>
				
				
				<h3 class="caption_left_br" style="margin-top:100px;"><trn class="TrnTag">나의 <b>결제내역</b></trn><span>Total : <b id="PaymentTotalCount"><?=number_format($TotalCount,0)?></b></span></h3>
				<div class="overflow_table">
					<table class="mypage_payment_table">
						<col width="7%">
						<col width="14%">
						<col width="">
						<col width="14%">
						<col width="14%">
						<col width="14%">
						<col width="16%">
						<tr>
							<th class="TrnTag">번호</th>
							<th class="TrnTag">구매일자</th>
							<th class="TrnTag">이용권명</th>
							<th class="TrnTag">금액</th>
							<th class="TrnTag">상태</th>
							<th class="TrnTag">영수증</th>
						</tr>
						<?	
						$Stmt = $DbConn->prepare($Sql);
						$Stmt->execute();
						$Stmt->setFetchMode(PDO::FETCH_ASSOC);

						$ii=1;
						while($Row = $Stmt->fetch()) {

							$ClassOrderPayDateTime = $Row["ClassOrderPayDateTime"];
							$ClassOrderPayPaymentPrice = $Row["ClassOrderPayPaymentPrice"];
							$ClassOrderPayProgress = $Row["ClassOrderPayProgress"];
							$tno = $Row["tno"];
							$ClassOrderPayNumber = $Row["ClassOrderPayNumber"];
							$use_pay_method = $Row["use_pay_method"];

							if ($ClassOrderPayProgress==11){
								$StrClassOrderPayProgress = "<trn class=\"TrnTag\">주문완료</trn>";
							}else if ($ClassOrderPayProgress==21){
								$StrClassOrderPayProgress = "<trn class=\"TrnTag\">결제완료</trn>";
							}else if ($ClassOrderPayProgress==31){
								$StrClassOrderPayProgress = "<trn class=\"TrnTag\">취소요청</trn>";
							}else if ($ClassOrderPayProgress==33){
								$StrClassOrderPayProgress = "<trn class=\"TrnTag\">취소완료</trn>";
							}else if ($ClassOrderPayProgress==41){
								$StrClassOrderPayProgress = "<trn class=\"TrnTag\">환불요청</trn>";
							}else if ($ClassOrderPayProgress==43){
								$StrClassOrderPayProgress = "<trn class=\"TrnTag\">환불완료</trn>";
							}
						?>
						<tr>
							<td><?=$TotalCount-$ii+1?></td>
							<td><?=str_replace("-",".",substr($ClassOrderPayDateTime,0,10))?></td>
							<td><a>망고아의 수강등록</a></td>
							<td><?=number_format($ClassOrderPayPaymentPrice,0)?>원</td>
							<td><?=$StrClassOrderPayProgress?></td>
							<td>
								<?if ($ClassOrderPayProgress==21){?>
									<?if ($use_pay_method=="100000000000"){//신용카드?>
									<a href="javascript:OpenInvoiceCard('<?=$tno?>', '<?=$ClassOrderPayNumber?>', '<?=$ClassOrderPayPaymentPrice?>')" class="button_br_gray TrnTag">신용카드 영수증</a>
									<?}else if ($use_pay_method=="010000000000"){//계좌이체?>
									<a href="javascript:OpenInvoiceEtc('<?=$tno?>', '<?=$ClassOrderPayNumber?>', '<?=$ClassOrderPayPaymentPrice?>')" class="button_br_gray TrnTag">신용카드 영수증</a>
									<?}else if ($use_pay_method=="001000000000"){//가상계좌?>
									<a href="javascript:OpenInvoiceEtc('<?=$tno?>', '<?=$ClassOrderPayNumber?>', '<?=$ClassOrderPayPaymentPrice?>')" class="button_br_gray TrnTag">신용카드 영수증</a>
									<?}?>
								
								<?}else{?>
									-
								<?}?>
							</td>
						</tr>

						<?
						
							$ii++;
						}
						$Stmt = null;
						?>
					</table>
				</div>
				
				<!--
				<div class="bbs_page">
					<span class="arrow_left_2"><img src="images/arrow_bbs_left_2.png"></span>
					<span class="arrow_left_1"><img src="images/arrow_bbs_left_1.png"></span>
					<span class="active">1</span>
					<a href="#">2</a>
					<a href="#">3</a>
					<a href="#" class="arrow_right_1"><img src="images/arrow_bbs_right_1.png"></a>
					<a href="#" class="arrow_right_2"><img src="images/arrow_bbs_right_2.png"></a>
				</div>
				-->
				
			</div>


			<?}else{?>
			
				<h3 class="caption_left_br" style="margin-top:100px;"><trn class="TrnTag">나의 <b>결제내역</trn></b><span>Total : <b id="PaymentTotalCount">0</b></span></h3>
				<div class="overflow_table">
					<table class="mypage_payment_table">
						<col width="7%">
						<col width="14%">
						<col width="">
						<col width="14%">
						<col width="14%">
						<col width="14%">
						<col width="16%">
						<tr>
							<th class="TrnTag">번호</th>
							<th class="TrnTag">구매일자</th>
							<th class="TrnTag">이용권명</th>
							<th class="TrnTag">금액</th>
							<th class="TrnTag">상태</th>
							<th class="TrnTag">영수증</th>
						</tr>
						<tr>
							<td colspan="6" class="TrnTag"> 결제 내역이 없습니다. </td>
						</tr>
					</table>
				</div>
				
			</div>


			
			<?}?>

        </div>
    </section>

</div>

<!-- 사진 변경 라이트 박스 -->
<div class="light_box_wrap photo_change">
    <div class="light_box_area">
        <a href="#"><img src="images/btn_close_white.png" alt="닫기" class="light_box_close"></a>
        <div class="light_box_box">
            <div class="phto_change_wrap">
                <div class="phto_change_area">
                    <h3 class="caption_underline TrnTag">사진 업로드</h3>
                    <div class="photo_change_box" style="background-image:url(images/no_photo.png)"></div>
                    <input type="file" class="photo_change_file">
                    <div class="button_wrap"><a href="#" class="button_orange_white photo_change TrnTag">업로드</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 사진 변경 라이트 박스 -->

<!-- 변경 사유 라이트 박스 -->
<div class="light_box_wrap">
    <div class="light_box_area">
        <a href="#"><img src="images/btn_close_white.png" alt="닫기" class="light_box_close"></a>
        <div class="light_box_box">
            <div class="mantoman_write_wrap">
                <div class="mantoman_write_area">
                    <h3 class="caption_underline TrnTag">수업 변경 요청</h3>
                    <ul class="mantoman_write_list">
                        <li class="TrnTag">변경 사유와 연락처를 적어주시면 전화 연락 후 수업일정을 조정합니다.</li>
                    </ul>
                    <table class="mantoman_write_table">
                        <tr>
                            <th class="TrnTag">연락처</th>
                            <td>
                                <select name="MemberPhone1_1" class="member_select_1 change">
                                    <option value="010">010</option>
                                    <option value="011">011</option>
                                </select>
                                <span class="member_space">-</span>
                                <input type="text" name="MemberPhone1_2" class="member_input_small change">
                                <span class="member_space">-</span>
                                <input type="text" name="MemberPhone1_3" class="member_input_small change">
                            </td>
                        </tr>
                        <tr>
                            <th class="TrnTag">변경사유</th>
                            <td><textarea class="mantoman_textarea"></textarea></td>
                        </tr>
                    </table>
                    <div class="button_wrap"><a href="#" class="button_orange_white mantoman TrnTag">변경 요청하기</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 라이트 박스 -->


<script>
function OpenInvoiceCard(tno, order_no, trade_mony){
	
	openurl = "http://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=card_bill&tno="+tno+"&order_no="+order_no+"&trade_mony="+trade_mony;
	window.open(openurl,'OpenInvoiceCard','width=470,height=815,toolbar=no,top=100,left=100');
	
}
function OpenInvoiceEtc(tno, order_no, trade_mony){
	
	openurl = "https://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=vcnt_bill&tno="+tno+"&&order_no="+order_no+"&trade_mony="+trade_mony;
	window.open(openurl,'OpenInvoiceCard','width=470,height=815,toolbar=no,top=100,left=100');
	
}
</script>


<script language="javascript">
$('.sub_visual_navi .one').addClass('active');
</script>


<?php
echo "\n";
echo $SubLayoutBottom;
echo "\n";
echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('./includes/common_analytics.php');
?>


<?php
include_once('./includes/common_footer.php');

if (trim($SubLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $SubLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}

if (trim($MainLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $MainLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}
?>

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>





