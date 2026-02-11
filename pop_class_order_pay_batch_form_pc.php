<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');


include "./kcp_batch_pc/cfg/site_conf_inc.php";       // 환경설정 파일 include
//$url = "http://" . $DefaultDomain2 ."/kcp_batch_pc/pc_auth/result_preset.php";
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

<script type="text/javascript">
/****************************************************************/
/* m_Completepayment  설명                                      */
/****************************************************************/
/* 인증완료시 재귀 함수                                         */
/* 해당 함수명은 절대 변경하면 안됩니다.                        */
/* 해당 함수의 위치는 payplus.js 보다먼저 선언되어여 합니다.    */
/* Web 방식의 경우 리턴 값이 form 으로 넘어옴                   */
/* EXE 방식의 경우 리턴 값이 json 으로 넘어옴                   */
/****************************************************************/
function m_Completepayment( FormOrJson, closeEvent ) 
{
	var frm = document.formOrder; 
 
	/********************************************************************/
	/* FormOrJson은 가맹점 임의 활용 금지                               */
	/* frm 값에 FormOrJson 값이 설정 됨 frm 값으로 활용 하셔야 됩니다.  */
	/* FormOrJson 값을 활용 하시려면 기술지원팀으로 문의바랍니다.       */
	/********************************************************************/
	GetField( frm, FormOrJson ); 


	if( frm.res_cd.value == "0000" )
	{
		/*
			가맹점 리턴값 처리 영역
		*/
	 
		frm.submit(); 
	}
	else
	{
		alert( "[" + frm.res_cd.value + "] " + frm.res_msg.value );
		
		closeEvent();
	}
}
</script>

<script type="text/javascript" src="<?=$g_conf_js_url ?>"></script>


<script type="text/javascript">  

	/* Payplus Plug-in 실행 */
	function jsf__pay( form )
	{
		try
		{
			KCP_Pay_Execute( form ); 
		}
		catch (e)
		{
			/* IE 에서 결제 정상종료시 throw로 스크립트 종료 */ 
		}
	}             

	/* 주문번호 생성 예제 */
	function init_orderid()
	{
		var today = new Date();
		var year  = today.getFullYear();
		var month = today.getMonth() + 1;
		var date  = today.getDate();
		var time  = today.getTime();

		if(parseInt(month) < 10) {
			month = "0" + month;
		}

		if(parseInt(date) < 10) {
			date = "0" + date;
		}

		var order_idxx = "TEST" + year + "" + month + "" + date + "" + time;

		document.formOrder.ordr_idxx.value = order_idxx;            
	}
   
</script>

<script src="js/jquery-2.2.4.min.js"></script>
<script src="js/common.js"></script>
</head>
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






<div class="payment_wrap">
	<h1 class="payment_caption">정기결제 신청</h1>


	<form name="formOrder" method="post" accept-charset="EUC-KR" action="./kcp_batch_pc/pc_auth/pp_cli_hub.php">

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
				<input type="hidden" name="kcpgroup_id" value="<?=$BatchGroupID?>">
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


		<!-- 필수 항목 : 요청구분 -->
		<input type="hidden" name="req_tx"         value="pay"/>
		<input type="hidden" name="site_cd"        value="<?=$g_conf_site_cd   ?>" />
		<input type="hidden" name="site_name"      value="<?=$g_conf_site_name ?>" />
		
		<!-- 결제 방법 : 인증키 요청(AUTH:CARD) -->
		<input type='hidden' name='pay_method'     value='AUTH:CARD'>

		<!-- 인증 방식 : 공인인증(BCERT) -->
		<input type='hidden' name='card_cert_type' value='BATCH'>

		<!-- 필수 항목 : PULGIN 설정 정보 변경하지 마세요 -->
		<input type='hidden' name='module_type'    value='01'>

		<!-- 필수 항목 : PLUGIN에서 값을 설정하는 부분으로 반드시 포함되어야 합니다. ※수정하지 마십시오.-->
		<input type='hidden' name='res_cd'         value=''>
		<input type='hidden' name='res_msg'        value=''>
		<input type='hidden' name='trace_no'       value=''>
		<input type='hidden' name='enc_info'       value=''>
		<input type='hidden' name='enc_data'       value=''>
		<input type='hidden' name='tran_cd'        value=''>

		<!-- 배치키 발급시 주민번호 입력을 결제창 안에서 진행 -->
		<input type='hidden' name='batch_soc'      value='Y'>

		<!-- 상품제공기간 설정 -->
		<!--input type='hidden' name='good_expr' value='2:1m'-->
		
		<!-- 카드번호 해쉬 데이터 리턴 여부 -->
		<!-- 배치키 리턴 시 카드번호 해쉬데이터 추가 전달 -->
		<!-- <input type='hidden' name='rtn_key_info_yn' value='Y' /> -->

		<!-- 주민번호 S / 사업자번호 C 픽스 여부 -->
		<!-- <input type='hidden' name='batch_soc_choice' value='' /> -->

		<!-- 카드번호 해쉬 데이터 리턴 여부 -->
		<!-- 배치키 리턴 시 카드번호 해쉬데이터 추가 전달 -->
		<!-- <input type='hidden' name='rtn_key_info_yn' value='Y' /> -->

		<!-- 배치키 발급시 카드번호 리턴 여부 설정 -->
		<!-- Y : 1234-4567-****-8910 형식, L : 8910 형식(카드번호 끝 4자리) -->
		<!-- <input type='hidden' name='batch_cardno_return_yn'  value=''> -->

		<!-- batch_cardno_return_yn 설정시 결제창에서 리턴 -->
		<!-- <input type='hidden' name='card_mask_no'			  value=''>    -->

	</div>


	<div id="BtnBox" style="padding: 0px 20px;">
		<a id="" href="javascript:jsf__pay(document.formOrder)" class="button_orange_white payment">신청하기</a>
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