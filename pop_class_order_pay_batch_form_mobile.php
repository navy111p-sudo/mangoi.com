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
$ClassOrderPayBatchMonth = 1;
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$ClassOrderPayBatchID = isset($_REQUEST["ClassOrderPayBatchID"]) ? $_REQUEST["ClassOrderPayBatchID"] : "";
$ClassOrderPayBatchNumber = isset($_REQUEST["ClassOrderPayBatchNumber"]) ? $_REQUEST["ClassOrderPayBatchNumber"] : "";
$ClassOrderMode = isset($_REQUEST["ClassOrderMode"]) ? $_REQUEST["ClassOrderMode"] : "";
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
		from ClassOrders A
			inner join ClassProducts AA on A.ClassProductID=AA.ClassProductID 
			
			inner join ClassOrderTimeTypes DD on A.ClassOrderTimeTypeID=DD.ClassOrderTimeTypeID 
			inner join ClassOrderWeekCounts EE on A.ClassOrderWeekCountID=EE.ClassOrderWeekCountID 
			
			inner join Members B on A.MemberID=B.MemberID 
			inner join Centers C on B.CenterID=C.CenterID 
			inner join Branches D on C.BranchID=D.BranchID 
			inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
			inner join Companies F on E.CompanyID=F.CompanyID 
			inner join Franchises G on F.FranchiseID=G.FranchiseID 
		where A.ClassOrderID=:ClassOrderID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

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




include "./kcp_batch_mobile/cfg/site_conf_inc.php";       // 환경설정 파일 include
$tablet_size     = "1.0"; // 화면 사이즈 고정
$url = "http://" . $DefaultDomain2 ."/kcp_batch_mobile/mobile_auth/result_preset.php";




//=================== SLP 1개월만 결제
$SLP_BranchID_0=42;//gangseo
$SLP_BranchID_1=107;//seodaemoon
$SLP_BranchID_2=113;//slp
$SLP_BranchID_3=114;//soowon

$HideSelectMonth = 0;
if ($BranchID==$SLP_BranchID_0 || $BranchID==$SLP_BranchID_1 || $BranchID==$SLP_BranchID_2 || $BranchID==$SLP_BranchID_3){
	$HideSelectMonth = 1;
}
//=================== SLP 1개월만 결제
?>
<script type="text/javascript" src="./kcp_batch_mobile/mobile_auth/js/approval_key.js"></script>

<div class="payment_wrap">
	<h1 class="payment_caption">정기결제 신청</h1>


	<form name="order_info" method="post" accept-charset="EUC-KR">

		<table class="payment_table_5" cellpadding="0" cellspacing="0">
		  <tr style="display:none;">
			<th>주문 번호</th>
			<td><?=$ClassOrderPayBatchNumber?><input type="hidden" name="ordr_idxx" value="<?=$ClassOrderPayBatchNumber?>"></td>
		  </tr>
		  <tr>
			<th>상품명</th>
			<td>망고아이 정기결제<input type="hidden" name="good_name" value="망고아이 정기결제"></td>
		  </tr>
		  <tr>
			<th>결제 금액</th>
			<td>결제주기별 책정<input type="hidden" name="good_mny" value="1000"></td>
		  </tr>
		  <tr>
			<th>주문자명</th>
			<td><?=$MemberName?><input type="hidden" name="buyr_name" value="<?=$MemberName?>"></td>
		  </tr>
		  <tr>
			<th>E-mail</th>
			<td><?=$MemberEmail?><input type="hidden" name="buyr_mail" value="<?=$MemberEmail?>"></td>
		  </tr>
		  <tr>
			<th>전화번호</th>
			<td><?=$MemberPhone1?><input type="hidden" name="buyr_tel1" value="<?=$MemberPhone1?>"></td>
		  </tr>
		  <tr style="display:none;">
			<th>휴대폰번호</th>
			<td><?=$MemberPhone1?><input type="hidden" name="buyr_tel2" value="<?=$MemberPhone1?>"></td>
		  </tr>

		  <!-- 배치 인증키생성 그룹아이디(리얼테스트시 실제 업체의 그룹아이디 입력) -->
		  <tr style="display:none;">
			<th>그룹아이디</th>
			<td>
				<input type="hidden" name="kcp_group_id" value="<?=$BatchGroupID?>">
			</td>
		  </tr>
		  <tr>
			<th>결제주기</th>
			<td class="radio_wrap duration">
				<?if ($HideSelectMonth==1){?>
				1개월
				<?}else{?>

					<?
					$SqlWhere2 = "";
					if ($ClassOrderMode=="LMS"){
						$SqlWhere2 = " and A.ClassOrderPayMonthNumberUseLms=1 ";
					}
					$Sql2 = "select 
									A.* 
							from ClassOrderPayMonthNumbers A 
							where ClassOrderPayMonthNumberState=1 ".$SqlWhere2."
							order by A.ClassOrderPayMonthNumberOrder asc";
					$Stmt2 = $DbConn->prepare($Sql2);
					$Stmt2->execute();
					$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
					
					while($Row2 = $Stmt2->fetch()) {
						$SelectClassOrderPayMonthNumberID = $Row2["ClassOrderPayMonthNumberID"];
						$SelectClassOrderPayMonthNumberName = $Row2["ClassOrderPayMonthNumberName"];

						//if ($SelectClassOrderPayMonthNumberID<=3){
					
					?>
						<input type="radio" class="input_radio" id="ClassOrderPayBatchMonth<?=$SelectClassOrderPayMonthNumberID?>" name="ClassOrderPayBatchMonth" value="<?=$SelectClassOrderPayMonthNumberID?>" <?if ($ClassOrderPayBatchMonth==$SelectClassOrderPayMonthNumberID){?>checked<?}?>><label class="label" for="ClassOrderPayBatchMonth<?=$SelectClassOrderPayMonthNumberID?>" onclick="ChClassOrderPayBatchMonth(<?=$SelectClassOrderPayMonthNumberID?>)" class="input_radio"><span class="bullet_radio"></span><?=$SelectClassOrderPayMonthNumberName?></label>
					<?
						//}
					}
					?>

				<?}?>
			</td>
		  </tr>
		</table>


		<!-- 공통정보 -->
		<input type="hidden" name="req_tx"          value="pay">                           <!-- 요청 구분 -->
		<input type="hidden" name="shop_name"       value="<?=$g_conf_site_name ?>">       <!-- 사이트 이름 --> 
		<input type="hidden" name="site_cd"         value="<?=$g_conf_site_cd   ?>">       <!-- 사이트 키 -->
		<input type="hidden" name="currency"        value="410"/>                          <!-- 통화 코드 -->
		<input type="hidden" name="eng_flag"        value="N"/>                            <!-- 한 / 영 -->        

		<!-- 결제등록 키 -->
		<input type="hidden" name="approval_key"    id="approval">
		<!-- 인증시 필요한 파라미터(변경불가)-->
		<input type="hidden" name="escw_used"       value="N">
		<input type="hidden" name="pay_method"      value="AUTH">
		<input type="hidden" name="ActionResult"    value="batch">
		<!-- 리턴 URL (kcp와 통신후 결제를 요청할 수 있는 암호화 데이터를 전송 받을 가맹점의 주문페이지 URL) -->
		<input type="hidden" name="Ret_URL"         value="<?=$url?>">
		<!-- 화면 크기조정 -->
		<input type="hidden" name="tablet_size"     value="<?=$tablet_size?>">

		<!-- 추가 파라미터 ( 가맹점에서 별도의 값전달시 param_opt 를 사용하여 값 전달 ) -->
		<input type="hidden" name="param_opt_1"     value="">
		<input type="hidden" name="param_opt_2"     value="">
		<input type="hidden" name="param_opt_3"     value="">

		<!-- 결제 정보 등록시 응답 타입 ( 필드가 없거나 값이 '' 일경우 TEXT, 값이 XML 또는 JSON 지원 -->
		<input type="hidden" name="response_type"  value="TEXT"/>
		<input type="hidden" name="PayUrl"   id="PayUrl"   value=""/>
		<input type="hidden" name="traceNo"  id="traceNo"  value=""/>
	</form>
	</div>


	<div id="BtnBox" style="padding: 0px 20px;">
		<a href="javascript:kcp_AJAX();" id="" class="button_orange_white payment">신청하기</a>
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
function call_pay_form()
{
	var v_frm = document.order_info;
   
	v_frm.action = PayUrl;

	if (v_frm.Ret_URL.value == "")
	{
		/* Ret_URL값은 현 페이지의 URL 입니다. */
		alert("연동시 Ret_URL을 반드시 설정하셔야 됩니다.");
		return false;
	}
	else
	{
		v_frm.submit();
	}
}


function ChClassOrderPayBatchMonth(ClassOrderPayBatchMonth){
	url = "./ajax_set_class_order_pay_batch_ch_month.php";
	//location.href = url + "?ClassOrderPayBatchID=<?=$ClassOrderPayBatchID?>&ClassOrderPayBatchMonth"+ClassOrderPayBatchMonth;
	$.ajax(url, {
		data: {
			ClassOrderPayBatchID: <?=$ClassOrderPayBatchID?>,
			ClassOrderPayBatchMonth: ClassOrderPayBatchMonth
		},
		success: function (data) {

		},
		error: function () {

		}
	});
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