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
$ClassOrderPayBatchID = isset($_REQUEST["ClassOrderPayBatchID"]) ? $_REQUEST["ClassOrderPayBatchID"] : "";
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";
$MemberID = $_LINK_MEMBER_ID_;


$Sql = "
		select 
				A.MemberName,
				AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
				AES_DECRYPT(UNHEX(A.MemberEmail),:EncryptionKey) as DecMemberEmail
		from Members A 

		where A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberName = $Row["MemberName"];
$MemberPhone1 = $Row["DecMemberPhone1"];
$MemberEmail = $Row["DecMemberEmail"];



$Sql = "
		select 

			AAAA.ClassOrderPayBatchMonth,
			AAAA.card_name,
			A.*,

			AA.ClassProductName, 
			DD.ClassOrderTimeTypeName,
			EE.ClassOrderWeekCountName,

			B.MemberName,
			B.MemberLoginID,
			
			C.CenterID as JoinCenterID,
			C.CenterName as JoinCenterName,
			D.BranchID as JoinBranchID,
			D.BranchName as JoinBranchName, 
			E.BranchGroupID as JoinBranchGroupID,
			E.BranchGroupName as JoinBranchGroupName,
			F.CompanyID as JoinCompanyID,
			F.CompanyName as JoinCompanyName,
			G.FranchiseName
		from ClassOrderPayBatchs AAAA 
			inner join ClassOrders A on AAAA.ClassOrderID=A.ClassOrderID 
			inner join ClassProducts AA on A.ClassProductID=AA.ClassProductID 
			
			inner join ClassOrderTimeTypes DD on A.ClassOrderTimeTypeID=DD.ClassOrderTimeTypeID 
			inner join ClassOrderWeekCounts EE on A.ClassOrderWeekCountID=EE.ClassOrderWeekCountID 
			
			inner join Members B on A.MemberID=B.MemberID 
			inner join Centers C on B.CenterID=C.CenterID 
			inner join Branches D on C.BranchID=D.BranchID 
			inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
			inner join Companies F on E.CompanyID=F.CompanyID 
			inner join Franchises G on F.FranchiseID=G.FranchiseID 
		where AAAA.ClassOrderPayBatchID=:ClassOrderPayBatchID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderPayBatchID', $ClassOrderPayBatchID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ClassOrderPayBatchMonth = $Row["ClassOrderPayBatchMonth"];
$card_name = $Row["card_name"];

$ClassProductID = $Row["ClassProductID"];
$ClassOrderLeveltestApplyTypeID = $Row["ClassOrderLeveltestApplyTypeID"];
$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
$ClassOrderWeekCountID = $Row["ClassOrderWeekCountID"];
$ClassOrderRequestText = $Row["ClassOrderRequestText"];
$ClassOrderState = $Row["ClassOrderState"];
$ClassMemberType = $Row["ClassMemberType"];
$ClassProgress = $Row["ClassProgress"];
$ClassOrderRegDateTime = $Row["ClassOrderRegDateTime"];
$ClassOrderModiDateTime = $Row["ClassOrderModiDateTime"];


$ClassProductName = $Row["ClassProductName"];
$ClassOrderTimeTypeName = $Row["ClassOrderTimeTypeName"];
$ClassOrderWeekCountName = $Row["ClassOrderWeekCountName"];


$MemberName = $Row["MemberName"];
$MemberLoginID = $Row["MemberLoginID"];

$CenterID = $Row["JoinCenterID"];
$CenterName = $Row["JoinCenterName"];
$BranchID = $Row["JoinBranchID"];
$BranchName = $Row["JoinBranchName"];
$BranchGroupID = $Row["JoinBranchGroupID"];
$BranchGroupName = $Row["JoinBranchGroupName"];
$CompanyID = $Row["JoinCompanyID"];
$CompanyName = $Row["JoinCompanyName"];
$FranchiseName = $Row["FranchiseName"];

?>
<div class="payment_wrap">
	<h1 class="payment_caption">정기결제 해지</h1>


	<form name="RegForm" method="post">

		<table class="payment_table_5" cellpadding="0" cellspacing="0">
		  <tr>
			<th>상품명</th>
			<td>망고아이 정기결제<input type="hidden" name="good_name" class="w100" value="망고아이 정기결제"></td>
		  </tr>
		  <tr>
			<th>결제 금액</th>
			<td>결제주기별 책정<input type="hidden" name="good_mny" class="w100" value="1000"></td>
		  </tr>
		  <tr>
			<th>이 름</th>
			<td><?=$MemberName?><input type="hidden" name="buyr_name" class="w100" value="<?=$MemberName?>"></td>
		  </tr>
		  <tr style="display:none;">
			<th>E-mail</th>
			<td><?=$MemberEmail?><input type="hidden" name="buyr_mail" class="w200" value="<?=$MemberEmail?>"></td>
		  </tr>
		  <tr style="display:none;">
			<th>전화번호</th>
			<td><?=$MemberPhone1?><input type="hidden" name="buyr_tel1" class="w100" value="<?=$MemberPhone1?>"></td>
		  </tr>
		  <tr style="display:none;">
			<th>휴대폰번호</th>
			<td><?=$MemberPhone1?><input type="hidden" name="buyr_tel2" class="w100" value="<?=$MemberPhone1?>"></td>
		  </tr>

		  <!-- 배치 인증키생성 그룹아이디(리얼테스트시 실제 업체의 그룹아이디 입력) -->
		  <tr style="display:none;">
			<th>그룹아이디</th>
			<td>
				<input type="hidden" name="kcp_group_id" class="w100" value="<?=$BatchGroupID?>">
			</td>
		  </tr>


		  <tr>
			<th>등록카드</th>
			<td class="radio_wrap duration">
				<?=$card_name?>
			</td>
		  </tr>
		  <tr>
			<th>결제주기</th>
			<td class="radio_wrap duration">
				<?=$ClassOrderPayBatchMonth?> 개월
			</td>
		  </tr>
		</table>



	</form>
	</div>


	<div id="BtnBox" style="padding: 0px 20px;">
		<a href="javascript:FormSubmit();" id="" class="button_orange_white payment">해지하기</a>
		<?if ($FromDevice=="app"){?>
		<a href="javascript:window.Exit=true;" id="" class="button_orange_white payment"style="background-color:#6894D9;margin-top:5px;">취소하기</a>
		<?}?>
	</div>

	</form>

	<!--
	<ul class="payment_attention_list">
		<li>1개월은 4주로 간주되며, 주 5회 1개월 수강은 총 20회, 주 3회 1개월 수강은 총 12회, 주 2회 1개월 수강은 총 8회로 진행됩니다.</li>
		<li>모든 종류의 할인은 기본수강료를 기준으로 적용되며, 중복할인이 되지 않습니다.</li>
		<li>추가 수강료란 핸드폰 수강자를 위한 추가 통신료, 과정별로 제공되는 예복습 프로그램이나 이북컨텐츠 등의 제공을 위한 과정별 추가비용을 말하며, 순수한 비용이므로 할인율이 적용되지 않습니다.</li>
		<li>화상수업으로 수강하기 위해서는 PC 데스크탑 컴퓨터의 경우 화상캠과 해드셋이 필요합니다.</li>
		<li>이벤트 상품의 경우, 특별할인(장기간 수강 할인 등)이 적용되지 않습니다.</li>
	</ul>
	-->
</div>





<script>
function FormSubmit(){

	if (confirm("해지 하시겠습니까?")){

		url = "./ajax_set_class_order_pay_batch_del.php";
		//location.href = url + "?ClassOrderPayBatchID=<?=$ClassOrderPayBatchID?>;
		$.ajax(url, {
			data: {
				ClassOrderPayBatchID: <?=$ClassOrderPayBatchID?>
			},
			success: function (data) {
				parent.location.reload();
			},
			error: function () {

			}
		});
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

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>