<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />

<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-2.2.4.min.js"></script>
<script src="js/common.js"></script>

<body>
<?
include_once('./includes/common_body_top.php');
?>
<?
$ClassOrderPayID = isset($_REQUEST["ClassOrderPayID"]) ? $_REQUEST["ClassOrderPayID"] : "";
$ClassOrderMode = isset($_REQUEST["ClassOrderMode"]) ? $_REQUEST["ClassOrderMode"] : "";


$Sql = "
		select 
			A.*
		from ClassOrderPays A
		where A.ClassOrderPayID=:ClassOrderPayID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ClassOrderPayProgress = $Row["ClassOrderPayProgress"];
$ClassOrderPayPaymentDateTime = $Row["ClassOrderPayPaymentDateTime"];
$ClassOrderPayCencelDateTime = $Row["ClassOrderPayCencelDateTime"];
?>

<div class="payment_wrap">
	<h1 class="payment_caption">결제관리</h1>

	<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
	<input type="hidden" name="ClassOrderPayID" value="<?=$ClassOrderPayID?>">
	<input type="hidden" name="ClassOrderMode" value="<?=$ClassOrderMode?>">
	<h3 class="payment_left_caption"><b>수강신청</b> 정보</h3>
	<table class="payment_table_5" style="margin-top:0px;">
		<tr>
			<th>결제상태</th>
			<td class="radio_wrap duration">
				<input type="radio" id="ClassOrderPayProgress1" class="input_radio" name="ClassOrderPayProgress" value="1" <?if ($ClassOrderPayProgress==1){?>checked<?}?>><label class="label" for="ClassOrderPayProgress1"><span class="bullet_radio"></span>결제대기</label>

				<input type="radio" id="ClassOrderPayProgress21" class="input_radio" name="ClassOrderPayProgress" value="21" <?if ($ClassOrderPayProgress==21){?>checked<?}?>><label class="label" for="ClassOrderPayProgress21"><span class="bullet_radio"></span>결제완료</label>

				<input type="radio" id="ClassOrderPayProgress33" class="input_radio" name="ClassOrderPayProgress" value="33" <?if ($ClassOrderPayProgress==33){?>checked<?}?>><label class="label" for="ClassOrderPayProgress33"><span class="bullet_radio"></span>취소완료</label>
			</td>
		</tr>
		<tr>
			<th>결제일</th>
			<td>
				<input type="text" id="ClassOrderPayPaymentDateTime" name="ClassOrderPayPaymentDateTime" value="<?=$ClassOrderPayPaymentDateTime?>">
			</td>
		</tr>
		<tr>
			<th>취소일</th>
			<td>
				<input type="text" id="ClassOrderPayCencelDateTime" name="ClassOrderPayCencelDateTime" value="<?=$ClassOrderPayCencelDateTime?>">
			</td>
		</tr>
	</table>



	<a href="javascript:FormSubmit();" class="button_orange_white payment">결제상태변경</a>
	</form>


</div>


<script type="text/javascript">
function FormSubmit(){
	if (confirm('결제상태로 변경하시겠습니까?')){
		document.RegForm.action = "pop_class_order_pay_change_action.php";
		document.RegForm.submit();
	}
}


</script>


<script>
//float
$(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
	//this.value = this.value.replace(/[^0-9\.]/g,'');
	$(this).val($(this).val().replace(/[^0-9\.]/g,''));
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});

//int
$(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {    
	$(this).val($(this).val().replace(/[^\d].+/, ""));
	if ((event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});


// numberOnly="true" 와 같이 사용
$(function(){
	$(document).on("keyup", "input:text[numberOnly]", function() {$(this).val( $(this).val().replace(/[^0-9]/gi,"") );});
	$(document).on("keyup", "input:text[datetimeOnly]", function() {$(this).val( $(this).val().replace(/[^0-9:\-]/gi,"") );});
});


// class="numeric-only" 와 같이 사용
$(document).on('keyup', '.numeric-only', function(event) {
   var v = this.value;
   if($.isNumeric(v) === false) {
        //chop off the last char entered
        this.value = this.value.slice(0,-1);
   }
});
</script>



<!-- ====    kendo -->
<link href="./kendo/styles/kendo.common.min.css" rel="stylesheet">
<link href="./kendo/styles/kendo.default.min.css" rel="stylesheet">
<script src="./kendo/js/kendo.web.min.js"></script>
<!-- ====    kendo   === -->

<script>
$("#ClassOrderPayPaymentDateTime").kendoDateTimePicker({
	format: "yyyy-MM-dd HH:mm:ss"
});
$("#ClassOrderPayCencelDateTime").kendoDateTimePicker({
	format: "yyyy-MM-dd HH:mm:ss"
});
</script>

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>