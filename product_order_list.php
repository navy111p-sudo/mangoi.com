<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
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
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";


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

if ($FromDevice==""){//앱이 아닐때만 표시
	echo "\n";
	echo $MainLayoutTop;
	echo "\n";
	echo $SubLayoutTop;
	echo "\n";
}//앱이 아닐때만 표시
?>


<?
if ($FromDevice==""){//앱이 아닐때만 표시
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
}else{// 앱이 아닐때만 표시
?>

<header class="header_app_wrap">
    <h1 class="header_app_title">교재구매</h1>
	<?if ($FromDevice=="mypage"){?>
	<a href="javascript:parent.$.fn.colorbox.close();" class="header_app_close header_app_gray"><img src="images/btn_app_close_black.png" class="icon"></a>
	<?}else{?>
    <a href="javascript:window.Exit=true" class="header_app_close header_app_gray"><img src="images/btn_app_close_black.png" class="icon"></a>
	<?}?>
</header>
<div style="padding:30px;">
<?
}
?>

			<?
			$MemberID = $_LINK_MEMBER_ID_;
						
			$Sql = "
					select 
							count(*) as TotalCount
					from ProductOrders A 
					where A.MemberID=:MemberID and (A.ProductOrderState=11 or A.ProductOrderState=21 or A.ProductOrderState=31 or A.ProductOrderState=33 or A.ProductOrderState=41 or A.ProductOrderState=44)";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':MemberID', $MemberID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;
			$TotalCount = $Row["TotalCount"];


			$Sql = "select 
						A.*,
						(select sum(AA.ProductCount*AA.ProductPrice) from ProductOrderDetails AA where AA.ProductOrderID=A.ProductOrderID and AA.ProductOrderDetailState=1) as ProductOrderProductPrice
					from ProductOrders A 
					where A.MemberID=:MemberID and (A.ProductOrderState=11 or A.ProductOrderState=21 or A.ProductOrderState=31 or A.ProductOrderState=33 or A.ProductOrderState=41 or A.ProductOrderState=44) 
					order by A.ProductOrderRegDateTime desc";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':MemberID', $MemberID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			?>
            <div class="mypage_inner">
                <h3 class="caption_left_br">
                    <trn class="TrnTag">교재 <b>주문내역</b></trn>
                    <a href="product_order_cart.php?FromDevice=<?=$FromDevice?>" class="book_order_btn TrnTag">주문하기</a>
                </h3>
                <div class="overflow_table">
                    <table class="book_cart_table">
                        <col width="7%">
                        <col width="12%">
                        <col width="">
                        <col width="12%">
                        <col width="13.5%">
                        <col width="13.5%">
                        <col width="13.5%" style="display:none;">
                        <tr>
                            <th class="TrnTag">번호</th>
                            <th class="TrnTag">구매일자</th>
                            <th class="TrnTag">상품명</th>
                            <th class="TrnTag">금액</th>
                            <th class="TrnTag">상태</th>
                            <th class="TrnTag">배송상태</th>
                            <th class="TrnTag" style="display:none;">영수증</th>
                        </tr>						
						<?
						$ListCount=1;
						while($Row = $Stmt->fetch()) {
							$ListNumber = $TotalCount - $ListCount + 1;
							
							$ProductSellerID = $Row["ProductSellerID"];
							$ProductOrderID = $Row["ProductOrderID"];
							$ProductOrderNumber = $Row["ProductOrderNumber"];
							$ProductOrderName = $Row["ProductOrderName"];
							$ProductOrderState = $Row["ProductOrderState"];
							$ProductOrderShipState = $Row["ProductOrderShipState"];
							$ProductOrderShipNumber = $Row["ProductOrderShipNumber"];
							$ProductOrderRegDateTime = $Row["ProductOrderRegDateTime"];
							$ProductOrderShipPrice = $Row["ProductOrderShipPrice"];
							$tno = $Row["tno"];
							$PayReTrno = $Row["PayReTrno"];
							$use_pay_method = $Row["use_pay_method"];
							$PayCardCD = $Row["PayCardCD"];

							if ($tno=="" && $PayReTrno!=""){
								$tno  = $PayReTrno;
							}

							
							$ProductOrderProductPrice = $Row["ProductOrderProductPrice"];
							$ProductOrderProductPriceWhthShip = $ProductOrderShipPrice + $ProductOrderProductPrice;
	
							$StrProductOrderRegDateTime = str_replace("-",".",substr($ProductOrderRegDateTime, 0,10));

							//0: 삭제  1:DB만생성   11: 미결제   21:결제완료  31:취소신청   33: 취소완료   41:환불신청    43:환불완료
							$StrProductOrderState = "-";
							if ($ProductOrderState==11){
								$StrProductOrderState = "<trn class\"TrnTag\">결제대기</trn>";
							}else if ($ProductOrderState==21){
								$StrProductOrderState = "<trn class\"TrnTag\">결제완료</trn>";
							}else if ($ProductOrderState==31){
								$StrProductOrderState = "<trn class\"TrnTag\">취소신청</trn>";
							}else if ($ProductOrderState==23){
								$StrProductOrderState = "<trn class\"TrnTag\">취소완료</trn>";
							}else if ($ProductOrderState==41){
								$StrProductOrderState = "<trn class\"TrnTag\">환불신청</trn>";
							}else if ($ProductOrderState==43){
								$StrProductOrderState = "<trn class\"TrnTag\">환불완료</trn>";
							}

							//1: 주문접수 11:배송준비중 21:발송완료 31:수취확인
							$StrProductOrderShipState = "-";
							if ($ProductOrderShipState==1){
								$StrProductOrderShipState = "<trn class\"TrnTag\">주문접수</trn>";
							}else if ($ProductOrderShipState==11){
								$StrProductOrderShipState = "<trn class\"TrnTag\">배송준비중</trn>";
							}else if ($ProductOrderShipState==21){
								$StrProductOrderShipState = "<trn class\"TrnTag\">발송완료</trn>";
							}else if ($ProductOrderShipState==31){
								$StrProductOrderShipState = "<trn class\"TrnTag\">수취확인</trn>";
							}

						?>

                        <tr>
                            <td><?=$ListNumber?></td>
                            <td><?=$StrProductOrderRegDateTime?></td>
                            <td><?=$ProductOrderName?></td>
                            <td><?=number_format($ProductOrderProductPrice,0)?></td>
                            <td><?=$StrProductOrderState?></td>
                            <td>
								<?=$StrProductOrderShipState?>
								<?if ($ProductOrderShipState>=21){?>
								<br>
								<a href="javascript:OpenShipInfo(<?=$ProductSellerID?>, '<?=$ProductOrderShipNumber?>')" class="book_transfer_complete TrnTag" style="margin-top:10px;">배송추적</a>
								<?}?>
							</td>
                            <td style="display:none;">

								<?if ($ProductOrderState==21){?>
									<?if ($use_pay_method=="100000000000" || $PayCardCD!=""){//신용카드?>
									<a href="javascript:OpenInvoiceCard('<?=$tno?>', '<?=$ProductOrderNumber?>', '<?=$ProductOrderProductPriceWhthShip?>')" class="book_transfer_ing TrnTag">영수증</a>
									<?}else if ($use_pay_method=="010000000000"){//계좌이체?>
									<a href="javascript:OpenInvoiceEtc('<?=$tno?>', '<?=$ProductOrderNumber?>', '<?=$ProductOrderProductPriceWhthShip?>')" class="book_transfer_ing TrnTag">영수증</a>
									<?}else if ($use_pay_method=="001000000000"){//가상계좌?>
									<a href="javascript:OpenInvoiceEtc('<?=$tno?>', '<?=$ProductOrderNumber?>', '<?=$ProductOrderProductPriceWhthShip?>')" class="book_transfer_ing TrnTag">영수증</a>
									<?}?>
								
								<?}else{?>
									-
								<?}?>

							</td>
                        </tr>
						<?php
							$ListCount ++;
						}
						$Stmt = null;

						if ($ListCount==1){
						?>
						<tr>
                            <td colspan="7" style="height:200px;text-align:center;line-height:200px;" class="TrnTag">구매 내역이 없습니다.</td>
                        </tr>

						<?
						}
						?>


                    </table>
                
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

<?
if ($FromDevice==""){//앱이 아닐때만 표시
?>
			</div>

        </div>
    </section>

</div>
<?
}else{// 앱이 아닐때만 표시
?>
</div>
<div id="MainFooterNotice" style="display:none;"></div>
<div id="MainFooterQna" style="display:none;"></div>
<?
}
?>

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


<script>
function OpenShipInfo(ProductSellerID, ProductOrderShipNumber){
	if (ProductSellerID==2){//올북스
		url = "https://www.doortodoor.co.kr/parcel/doortodoor.do?fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT&invc_no="+ProductOrderShipNumber;
		window.open(url, "doortodoor", "width=1000,height=900");
	}else{
		alert('준비중 입니다.');
	}
	
}

</script>



<script language="javascript">
$('.sub_visual_navi .one').addClass('active');
</script>


<?php
if ($FromDevice==""){//앱이 아닐때만 표시
	echo "\n";
	echo $SubLayoutBottom;
	echo "\n";
	echo $MainLayoutBottom;
	echo "\n";
}//앱이 아닐때만 표시
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





