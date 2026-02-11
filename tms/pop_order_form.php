<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./inc_header.php');
?>
</head>
<body style="padding:20px;">

<?php
$OrderID = isset($_REQUEST["OrderID"]) ? $_REQUEST["OrderID"] : "";

$ViewTable = "
		select 

			case when AA.ProductOptionRunCountType=1 then 
				BB.ProductRunCount
			else
				AA.ProductOptionItemRunCount
			end as ProductRunCount, 

			AA.ProductOptionItemPrice,

			BB.OrderID

		from OrderDetailOptionItems AA 
			inner join OrderDetails BB on AA.OrderDetailID=BB.OrderDetailID 
		where AA.OrderDetailOptionItemState=1 and BB.OrderDetailState=1 
";


$Sql = "
		select 
				A.*,
				B.MemberName,
				B.MemberLoginID,
				(
					select 
						sum(AA.ProductOptionItemPrice * BB.ProductRunCount) 
					from OrderDetailOptionItems AA 
						inner join OrderDetails BB on AA.OrderDetailID=BB.OrderDetailID 
					where BB.OrderID=A.OrderID and AA.OrderDetailOptionItemState=1 and BB.OrderDetailState=1 
				) as SumOrderPrice,

				(
					select
						sum(V.ProductOptionItemPrice * V.ProductRunCount) 
					from ($ViewTable) V 
					where V.OrderID=A.OrderID 
				) as SumOrderPrice,
				date_format(A.OrderDateTime, '%Y.%m.%d %h:%i:%s') as StrOrderDateTime,
				date_format(A.PaymentDateTime, '%Y.%m.%d %h:%i:%s') as StrPaymentDateTime,
				date_format(A.CancelRequestDateTime, '%Y.%m.%d %h:%i:%s') as StrCancelRequestDateTime,
				date_format(A.CancelDateTime, '%Y.%m.%d %h:%i:%s') as StrCancelDateTime,
				date_format(A.RefundRequestDateTime, '%Y.%m.%d %h:%i:%s') as StrRefundRequestDateTime,
				date_format(A.RefundDateTime, '%Y.%m.%d %h:%i:%s') as StrRefundDateTime
		from Orders A 
			inner join Members B on A.MemberID=B.MemberID 
		where A.OrderID=:OrderID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':OrderID', $OrderID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;


$OrderID = $Row["OrderID"];
$OrderNumber = $Row["OrderNumber"];
$OrderName = $Row["OrderName"];
$OrderState = $Row["OrderState"];
$OrderDateTime = $Row["OrderDateTime"];
$PaymentDateTime = $Row["PaymentDateTime"];

$payDevice = $Row["payDevice"];
$result = $Row["result"];
$errorMessage = $Row["errorMessage"];
$resultCode = $Row["resultCode"];
$resultMessage = $Row["resultMessage"];
$paymethod = $Row["paymethod"];
$refNo = $Row["refNo"];
$tranDate = $Row["tranDate"];
$payType = $Row["payType"];
$mbrRefNo = $Row["mbrRefNo"];
$tranTime = $Row["tranTime"];
$applNo = $Row["applNo"];
$cardNo = $Row["cardNo"];
$installment = $Row["installment"];
$issueCardName = $Row["issueCardName"];
$issueCompanyNo = $Row["issueCompanyNo"];
$acqCompanyNo = $Row["acqCompanyNo"];
$billkey = $Row["billkey"];
$bankCode = $Row["bankCode"];
$bankName = $Row["bankName"];
$accountNo = $Row["accountNo"];
$accountCloseDate = $Row["accountCloseDate"];

$StrAccountCloseDate = substr($accountCloseDate,0,2). "." . substr($accountCloseDate,2,2) . "." . substr($accountCloseDate,-2);
$StrBankName = $bankName;

$SumOrderPrice = $Row["SumOrderPrice"];

$StrOrderDateTime = $Row["StrOrderDateTime"];
$StrPaymentDateTime = $Row["StrPaymentDateTime"];
$StrCancelRequestDateTime = $Row["StrCancelRequestDateTime"];
$StrCancelDateTime = $Row["StrCancelDateTime"];
$StrRefundRequestDateTime = $Row["StrRefundRequestDateTime"];
$StrRefundDateTime = $Row["StrRefundDateTime"];

$MemberName = $Row["MemberName"];
$MemberLoginID  = $Row["MemberLoginID"];

if ($paymethod=="CARD"){//없음
	$StrPayMethod = "카드결제";
}else if ($paymethod=="HPP"){//없음
	$StrPayMethod = "휴대폰결제";
}else if ($paymethod=="VACCT"){
	$StrPayMethod = "가상계좌";
}else if ($paymethod=="ACCT"){
	$StrPayMethod = "실시간 계좌이체";
}

if ($OrderState=="11"){
	$StrOrderState = "입금대기";
	
	if ($paymethod=="VACCT"){
		if ($accountCloseDate>=date("ymd")){
			$StrVbankMsg = "<br>(".$StrAccountCloseDate.")";
		}else{
			$StrVbankMsg = "<br>(기한초과)";
		}

		$StrOrderState = $StrOrderState . $StrVbankMsg;
	}

}else if ($OrderState=="21"){
	$StrOrderState = "입금완료";
}else if ($OrderState=="31"){
	$StrOrderState = "취소신청";
}else if ($OrderState=="33"){
	$StrOrderState = "취소완료";
}else if ($OrderState=="41"){
	$StrOrderState = "환불신청";
}else if ($OrderState=="43"){
	$StrOrderState = "환불완료";
}

?>

<h1 class="Title" style="margin-bottom:20px;">결제 정보</h1>


<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8" autocomplete="off">
<input type="hidden" name="OrderID" value="<?=$OrderID?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form" style="margin-bottom:15px;">
  <tr>
	<th>결제수단<span></span></th>
	<td colspan="3">
		<?=$StrPayMethod?>
	</td>
  </tr>
  <tr>
	<th>현재상태<span></span></th>
	<td class="radio" colspan="3">
		<span style="display:inline-block;width:80px;padding:5px;border:1px solid #cccccc;text-align:center;margin-right:20px;"><?=$StrOrderState?></span>

		<?if ($OrderState==11){?>
			<input type="radio" name="OrderState" id="OrderState11" value="11" <?php if ($OrderState==11) {echo ("checked");}?>> <label for="OrderState11"><span></span>입금대기</label>
			<input type="radio" name="OrderState" id="OrderState21" value="21" <?php if ($OrderState==21) {echo ("checked");}?>> <label for="OrderState21"><span></span>결제완료</label>
			<input type="radio" name="OrderState" id="OrderState33" value="33" <?php if ($OrderState==33) {echo ("checked");}?>> <label for="OrderState33"><span></span>취소완료</label>
		<?}else if ($OrderState==21){?>
			<input type="radio" name="OrderState" id="OrderState11" value="11" <?php if ($OrderState==11) {echo ("checked");}?>> <label for="OrderState11"><span></span>입금대기</label>
			<input type="radio" name="OrderState" id="OrderState21" value="21" <?php if ($OrderState==21) {echo ("checked");}?>> <label for="OrderState21"><span></span>결제완료</label>
			<input type="radio" name="OrderState" id="OrderState43" value="43" <?php if ($OrderState==43) {echo ("checked");}?>> <label for="OrderState43"><span></span>환불완료</label>
		<?}else if ($OrderState==31){?>
			<input type="radio" name="OrderState" id="OrderState11" value="11" <?php if ($OrderState==11) {echo ("checked");}?>> <label for="OrderState11"><span></span>입금대기</label>
			<input type="radio" name="OrderState" id="OrderState31" value="31" <?php if ($OrderState==31) {echo ("checked");}?>> <label for="OrderState31"><span></span>취소요청</label>
			<input type="radio" name="OrderState" id="OrderState33" value="33" <?php if ($OrderState==33) {echo ("checked");}?>> <label for="OrderState33"><span></span>취소완료</label>
		<?}else if ($OrderState==33){?>
			<input type="radio" name="OrderState" id="OrderState11" value="11" <?php if ($OrderState==11) {echo ("checked");}?>> <label for="OrderState11"><span></span>입금대기</label>
			<input type="radio" name="OrderState" id="OrderState33" value="33" <?php if ($OrderState==33) {echo ("checked");}?>> <label for="OrderState33"><span></span>취소완료</label>
		<?}else if ($OrderState==41){?>
			<input type="radio" name="OrderState" id="OrderState21" value="21" <?php if ($OrderState==21) {echo ("checked");}?>> <label for="OrderState21"><span></span>결제완료</label>
			<input type="radio" name="OrderState" id="OrderState41" value="41" <?php if ($OrderState==41) {echo ("checked");}?>> <label for="OrderState41"><span></span>환불요청</label>
			<input type="radio" name="OrderState" id="OrderState43" value="43" <?php if ($OrderState==43) {echo ("checked");}?>> <label for="OrderState43"><span></span>환불완료</label>
		<?}else if ($OrderState==43){?>
			<input type="radio" name="OrderState" id="OrderState21" value="21" <?php if ($OrderState==21) {echo ("checked");}?>> <label for="OrderState21"><span></span>결제완료</label>
			<input type="radio" name="OrderState" id="OrderState43" value="43" <?php if ($OrderState==43) {echo ("checked");}?>> <label for="OrderState43"><span></span>환불완료</label>
		<?}?>

		<input type="radio" name="OrderState" id="OrderState0" value="0" <?php if ($OrderState==0) {echo ("checked");}?>> <label for="OrderState0" style='color:#ff0000;'><span></span>삭제</label>
	</td>
  </tr>
  <tr>
	<th>주문금액<span></span></th>
	<td colspan="3">
		<span style='color:#566BA8;'><?=number_format($SumOrderPrice,0)?></span> 원
	</td>
  </tr>
  <tr>
	<th>학부모명<span></span></th>
	<td colspan="3">
		<?=$MemberName?> ( <?=$MemberLoginID?> )
	</td>
  </tr>
  <tr>
	<th>주문일시<span></span></th>
	<td width="30%">
		<?=$StrOrderDateTime?>
	</td>
	<th>결제일시<span></span></th>
	<td width="30%">
		<?=$StrPaymentDateTime?>
	</td>
  </tr>
  <tr>
	<th>취소일시<span></span></th>
	<td>
		<?=$StrCancelDateTime?>
	</td>
	<th>환불일시<span></span></th>
	<td>
		<?=$StrRefundDateTime?>
	</td>
  </tr>

  </tr>
</table>
</form>

<div class="btn_center" style="padding-top:25px;">
	<a href="javascript:FormSubmit();" class="btn red">수정하기</a>
	<a href="javascript:parent.$.fn.colorbox.close();" class="btn gray">닫기</a>
</div>
<script>
function FormSubmit(){
	
	ConfrimMsg = "수정 하시겠습니까?";

	if (confirm(ConfrimMsg)){
		document.RegForm.action = "./pop_order_action.php"
		document.RegForm.submit(); 
	}

}

</script>

<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>