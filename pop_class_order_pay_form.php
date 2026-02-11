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

<body >
<?
include_once('./includes/common_body_top.php');
?>
<?
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$ClassOrderPayID = isset($_REQUEST["ClassOrderPayID"]) ? $_REQUEST["ClassOrderPayID"] : "";
$ClassOrderPayNumber = isset($_REQUEST["ClassOrderPayNumber"]) ? $_REQUEST["ClassOrderPayNumber"] : "";
$ClassOrderMode = isset($_REQUEST["ClassOrderMode"]) ? $_REQUEST["ClassOrderMode"] : "";
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";

$ReqUrl = isset($_REQUEST["ReqUrl"]) ? $_REQUEST["ReqUrl"] : "";
if ($ReqUrl!=""){
	header("Location: mypage_payment_list.php?FromDevice=$FromDevice"); 
	exit;
}


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


//=================== 아이맘 잉글리쉬는 1,3 개월만 결제
$DisplayMonthOnly13 = 0;
if ($CenterID==190){
	$DisplayMonthOnly13 = 1;
}
//=================== 아이맘 잉글리쉬는 1,3 개월만 결제


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

<?if ($FromDevice=="app"){?>
<header class="header_app_wrap">
    <h1 class="header_app_title TrnTag">수강신청</h1>
    <a href="javascript:window.Exit=true" class="header_app_close header_app_gray"><img src="images/btn_app_close_black.png" class="icon"></a>
</header>
<?}?>

<div class="payment_wrap" <?if ($FromDevice=="app"){?>style="margin-top:50px;"<?}?>>
	<h1 class="payment_caption TrnTag">수강신청</h1>

	<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">

	<h3 class="payment_left_caption TrnTag"><b>수강신청</b> 정보</h3>
	<table class="payment_table_5" style="margin-top:0px;">
		<tr>
			<th class="TrnTag">교육과정</th>
			<td><strong class="orange TrnTag">망고아이 화상강좌</strong></td>
		</tr>
		<tr>
			<th class="TrnTag">수업타입</th>
			<td>
				<strong>
				<?if ($ClassMemberType==1){?>
				<trn class="TrnTag">1:1수업</trn>
				<?}else if ($ClassMemberType==2){?>
				<trn class="TrnTag">1:2수업</trn>
				<?}else if ($ClassMemberType==3){?>
				<trn class="TrnTag">그룹수업</trn>
				<?}?>
				</strong>
			</td>
		</tr>
		<tr>
			<th id="classCountPerWeek" class="TrnTag">수강회수/주</th>
			<td><strong><?=$ClassOrderWeekCountName?></strong></td>
		<tr>
			<th class="TrnTag">수강시간/회</th>
			<td><strong><?=$ClassOrderTimeTypeName?></strong></td>
		</tr>
		<tr>
			<th class="TrnTag">수강기간</th>
			<td class="radio_wrap duration">
				<?if ($HideSelectMonth==1){?>
				<trn class="TrnTag">1개월</trn>
				<?}?>
				<span style="display:<?if ($HideSelectMonth==1){?>none<?}?>;">
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
				
					if ($DisplayMonthOnly13==0 || ($DisplayMonthOnly13==1 && $SelectClassOrderPayMonthNumberID<=3) ) {//아이맘 잉글리쉬는 1,3만
				?>
					<input type="radio" id="ClassOrderPayMonthNumberID<?=$SelectClassOrderPayMonthNumberID?>" class="input_radio" onclick="ChClassOrderPayMonthNumberID(<?=$SelectClassOrderPayMonthNumberID?>)" name="ClassOrderPayMonthNumberID" value="<?=$SelectClassOrderPayMonthNumberID?>" <?if ($SelectClassOrderPayMonthNumberID==1){?>checked<?}?>><label class="label" for="ClassOrderPayMonthNumberID<?=$SelectClassOrderPayMonthNumberID?>"><span class="bullet_radio"></span><?=$SelectClassOrderPayMonthNumberName?></label>
				<?
					}
				}
				?>
				</span>
			</td>
		</tr>
		<?
			//멤버의 포인트 가져오기
			$Sql3 = "SELECT sum(MemberPoint) as SumOfPoint from MemberPoints 
						where MemberID=:MemberID and MemberPointState=1";
			$Stmt3 = $DbConn->prepare($Sql3);
			$Stmt3->bindParam(':MemberID', $MemberID);
			$Stmt3->execute();
			$Stmt3->setFetchMode(PDO::FETCH_ASSOC);

			$Row3 = $Stmt3->fetch();
			$SumOfPoint = isset($Row3["SumOfPoint"])?$Row3["SumOfPoint"]:0;
			if ($SumOfPoint>=2000){
				$UseablePoint = $SumOfPoint;
			} else {
				$UseablePoint = 0;
			}
		?>
		<tr>
			<th class="TrnTag">포인트 사용하기 </th>
			<td> 보유중인 포인트 ( <?=number_format($SumOfPoint)?> P) : 2,000P 이상부터 사용가능<br>
				<input type="text"  onchange="ChangePoint(1);" id="ClassOrderPayUseSavedMoneyPrice" name="ClassOrderPayUseSavedMoneyPrice" value="<?=$UseablePoint?>" style="width:80px;text-align:right;height:30px;padding:0px 10px;" class="allownumericwithoutdecimal"> 원<br>
				<label><input type="radio" name="PointUse" value="1" checked onclick="ChangePoint(1);">포인트 사용하기</label>
      			<label><input type="radio" name="PointUse" value="0" onclick="ChangePoint(0);">포인트 미사용</label>
			</td>
		</tr>
	</table>


	<div id="DivClassTeacherInfo">
		<!-- 내용 -->
	</div>

	<script>
	var CheckedClassOrderPayMonthNumberID = 0;
	var AjaxCheckedClassOrderPayMonthNumberID = 0;
	var ClassOrderPaySellingPrice = 0;

	//포인트 사용에 따라서 포인트를 적용할지 안 할지를 결정
	function ChangePoint(isPointUse){

		var UsePoint = document.RegForm.ClassOrderPayUseSavedMoneyPrice.value;
		
		if (isPointUse==0) {  //포인트 미사용
			document.RegForm.ClassOrderPayUseSavedMoneyPrice.value = 0;
		} else {  //포인트 사용
			document.RegForm.PointUse[0].checked = true;
			if (UsePoint < 2000) {
				alert('사용 포인트가 2,000P 보다 작습니다.');
				document.RegForm.ClassOrderPayUseSavedMoneyPrice.value = 0;
			}  else if (UsePoint > (ClassOrderPaySellingPrice-2000)) {
				alert('사용하려는 포인트가 포인트 사용가능 수강료보다 많습니다.');
				document.RegForm.ClassOrderPayUseSavedMoneyPrice.value = ClassOrderPaySellingPrice-2000;
			} else if (UsePoint > <?=$SumOfPoint?>) {
				alert('사용하려는 포인트가 보유하신 포인트보다 많습니다.');
				document.RegForm.ClassOrderPayUseSavedMoneyPrice.value = <?=$UseablePoint?>;
			}

		}

		ChClassOrderPayMonthNumberID(CheckedClassOrderPayMonthNumberID);
	}

	function ChClassOrderPayMonthNumberID(ClassOrderPayMonthNumberID){
		
		CheckedClassOrderPayMonthNumberID = ClassOrderPayMonthNumberID;

		ClassOrderPayUseSavedMoneyPrice = document.RegForm.ClassOrderPayUseSavedMoneyPrice.value;

		url = "./ajax_set_class_order_pay_change.php";
		//location.href = url + "?ClassOrderID=<?=$ClassOrderID?>&ClassOrderPayID=<?=$ClassOrderPayID?>&ClassOrderPayMonthNumberID="+ClassOrderPayMonthNumberID+"&ClassOrderPayUseSavedMoneyPrice="+ClassOrderPayUseSavedMoneyPrice+"&ClassOrderMode=<?=$ClassOrderMode?>";
		$.ajax(url, {
			data: {
				ClassOrderID: "<?=$ClassOrderID?>",
				ClassOrderPayID: "<?=$ClassOrderPayID?>",
				ClassOrderPayMonthNumberID: ClassOrderPayMonthNumberID,
				ClassOrderPayUseSavedMoneyPrice: ClassOrderPayUseSavedMoneyPrice,
				ClassOrderMode: "<?=$ClassOrderMode?>"
			},
			success: function (data) {
				document.getElementById("BtnBox").style.display = "";
                
                console.log(data);
				ClassTeacherInfoHTML = data.ClassTeacherInfoHTML;
				ClassOrderPayUseCashPrice = data.ClassOrderPayUseCashPrice; //최종 결제금액
				ClassOrderPaySellingPrice = parseInt(data.ClassOrderPaySellingPrice); //기본수강료

				document.SendPayForm.good_mny.value = ClassOrderPayUseCashPrice;
                console.log(ClassTeacherInfoHTML)
				document.getElementById("DivClassTeacherInfo").innerHTML = ClassTeacherInfoHTML;

                const classCountPerWeek = document.getElementById('classCountPerWeek').nextElementSibling.childNodes[0].outerText;
                if(classCountPerWeek.includes('2')){
                    console.log('222222')
                    ClassOrderPayUseCashPrice = parseInt(data.ClassOrderPayUseCashPrice * 2);
                    ClassOrderPaySellingPrice = parseInt(data.ClassOrderPaySellingPrice * 2);
                    
                    console.log(ClassOrderPayUseCashPrice);
                    console.log(ClassOrderPaySellingPrice);
                }
			},
			error: function () {
				alert("오류가 발생했습니다. 창을 닫고 다시 시도해 주시기 바랍니다.");
				document.getElementById("BtnBox").style.display = "none";
			}
		});
	}

	</script>



	<div id="BtnBox">
		<!--<a href="paysample://card_pay" class="button_orange_white payment">결제하기</a>-->
		<!-- <a href="javascript:handleOpenURL('paysample://');" class="button_orange_white payment">결제하기</a> -->
		<a href="javascript:PayAction();" id="BtnPaymentAction" class="button_orange_white payment">결제하기</a>
		<a href="javascript:PayActionClose();" id="BtnPaymentActionClose" style="display:none;" class="button_orange_white payment TrnTag">닫기</a>
		<?if ($FromDevice=="app"){?>
		<a href="javascript:window.Exit=true;" id="" class="button_orange_white payment TrnTag"style="background-color:#6894D9;margin-top:5px;">취소하기</a>
		<?}?>

		<?if ($_LINK_MEMBER_LEVEL_ID_<=4){?>
		<a href="javascript:FormSubmit(1);" class="button_orange_white payment TrnTag" style="background-color:#6894D9;margin-top:5px;">판매가 그대로 결제상태 변경하기</a>
		<a href="javascript:FormSubmit(0);" class="button_orange_white payment TrnTag" style="background-color:#94B4E2;margin-top:5px;">판매가 0 원 결제상태 변경하기</a>
		<?}?>
	</div>

	</form>


	<ul class="payment_attention_list">
		<li class="TrnTag">1개월은 4주로 간주되며, 주 5회 1개월 수강은 총 20회, 주 3회 1개월 수강은 총 12회, 주 2회 1개월 수강은 총 8회로 진행됩니다.</li>
		<li class="TrnTag">모든 종류의 할인은 기본수강료를 기준으로 적용되며, 중복할인이 되지 않습니다.</li>
		<li class="TrnTag">추가 수강료란 핸드폰 수강자를 위한 추가 통신료, 과정별로 제공되는 예복습 프로그램이나 이북컨텐츠 등의 제공을 위한 과정별 추가비용을 말하며, 순수한 비용이므로 할인율이 적용되지 않습니다.</li>
		<li class="TrnTag">화상수업으로 수강하기 위해서는 PC 데스크탑 컴퓨터의 경우 화상캠과 해드셋이 필요합니다.</li>
		<li class="TrnTag">이벤트 상품의 경우, 특별할인(장기간 수강 할인 등)이 적용되지 않습니다.</li>
	</ul>
</div>


<!----------------------- KCP PC결제창을 띄우기위한 팝업(iframe 포함) ------------------------->
<div id='paylayer' class="wrap-loading" style="display:none; z-index:100000000;">
<iframe id='kcppay' name='kcppay' width='100%' height='100%'></iframe>
</div>
<!------------------------------------------------------------------------------------>

<style type="text/css">
/* 결제창을 위한 가상창 */
.wrap-loading { 
    z-index:+1;
    position: fixed; 
    width:100%;
    height:100%;
    left:0; 
    right:0; 
    top:0; 
    bottom:0; 
    background: rgba(255,255,255,0.4); /*not in ie */ 
    filter: progid:DXImageTransform.Microsoft.Gradient(startColorstr='#808080', endColorstr='#eeeeee');    /* ie */ 
} 
</style>

<!------------------------------------------------------------------------------------>
<?
//$FrchBsUqCode    = "48091699472481";            // 판매사고유코드(망고아이 - 테스트)
$FrchBsUqCode    = "36049230468271";            // 판매사고유코드(망고아이)
$FrchBrUqCode    = "";							// 지점고유코드
//$test_paysw      = "Y";                         // 테스트결제시-Y, 실결제시-N
$test_paysw      = "N";                         // 테스트결제시-Y, 실결제시-N
$pay_repaybutyn = "N";                         // 동일 주문번호 재결제 가능-Y, 재결제 불가-N
$conf_site_name  = "MANGOI";					// PC결제 - 상호[반드시 영문으로만지정] 
$domain_name     = "https://".$DefaultDomain2;   // 도메인
$url_close       = "https://".$DefaultDomain2 . "/order_pay_close.php";        // PC 결제일 경우에 KCP결제창 닫기 페이지(사용자지정)
$MOBILE_PAY_FLAG = "N";                         // 모바일 결제시 Y, PC결제시 N

if ($ClassOrderMode=="LMS"){
    $url_payreqhome  = "https://".$DefaultDomain2 . "/lms/class_order_list.php?type=21";          // 결제요청페이지(사용자지정)
    $url_returnhome  = "https://".$DefaultDomain2 . "/lms/class_order_list.php?type=21";      // 결제후 최종돌아갈 홈페이지(사용자지정)
}else{
    if ($FromDevice=="app" || $FromDevice=="app2"){
        $url_payreqhome  = "SelfPayExit";          // 결제요청페이지(사용자지정)
        $url_returnhome  = "SelfPayExit";      // 결제후 최종돌아갈 홈페이지(사용자지정)
        $pay_homekey   = "mangoi://kr.ahsol.mangoi";     // 앱-홈(사용자지정)
        $pay_replaceurl   = "https://mangoi.co.kr"; //앱에서 결제 종료후 시스템 브라우져 이동할 페이지
    }else{
        $url_payreqhome  = "https://".$DefaultDomain2 . "/pop_class_order_pay_form.php";          // 결제요청페이지(사용자지정)
        $url_returnhome  = "https://".$DefaultDomain2 . "/mypage_payment_list.php";      // 결제후 최종돌아갈 홈페이지(사용자지정)
        $pay_homekey   = "";     // 앱-홈(사용자지정)
        $pay_replaceurl   = ""; //앱에서 결제 종료후 시스템 브라우져 이동할 페이지

        // 접속 기기가 모바일인지 확인
        $mobile_agents = ['iPhone', 'iPad', 'iPod', 'Android', 'BlackBerry', 'Windows Phone', 'webOS', 'Opera Mini', 'IEMobile', 'Mobile'];
        foreach ($mobile_agents as $agent) {
            if (stripos($_SERVER['HTTP_USER_AGENT'], $agent) !== false) {
                $MOBILE_PAY_FLAG = "Y";
                break;
            }
        }
    }
}

//$url_result      = "https://".$DefaultDomain2 . "/order_pay_result.php";       // 결제결과처리 페이지(사용자지정)
//$url_result_json = "https://".$DefaultDomain2 . "/order_pay_result_json.php";  // 결제결과처리 JSON 페이지(사용자지정)
//$url_result_curl = "https://".$DefaultDomain2 . "/order_pay_result_curl.php";  // 결제결과처리 JSON 페이지(사용자지정)
//$url_vbnotice    = "https://".$DefaultDomain2 . "/order_pay_result_vbank.php";        // 가상계좌 결제결과 통보처리 페이지(사용자지정)
//$url_retmethod   = "curl";                                                       // 결과값 처리방법 (curl, iframe)

if ($MOBILE_PAY_FLAG == "Y") {
    $url_result = "https://mangoi.co.kr" . "/order_pay_result.php";       // 결제결과처리 페이지(사용자지정)
    $url_result_json = "https://mangoi.co.kr" . "/order_pay_result_json.php";  // 결제결과처리 JSON 페이지(사용자지정)
    $url_result_curl = "https://mangoi.co.kr" . "/order_pay_result_curl.php";  // 결제결과처리 JSON 페이지(사용자지정)
    $url_vbnotice = "https://mangoi.co.kr" . "/order_pay_result_vbank.php";        // 가상계좌 결제결과 통보처리 페이지(사용자지정)
    $url_retmethod = "curl";// 결과값 처리방법 (curl, iframe)
} else {
    $url_result      = "https://".$DefaultDomain2 . "/order_pay_result.php";       // 결제결과처리 페이지(사용자지정)
    $url_result_json = "https://".$DefaultDomain2 . "/order_pay_result_json.php";  // 결제결과처리 JSON 페이지(사용자지정)
    $url_result_curl = "https://".$DefaultDomain2 . "/order_pay_result_curl.php";  // 결제결과처리 JSON 페이지(사용자지정)
    $url_vbnotice    = "https://".$DefaultDomain2 . "/order_pay_result_vbank.php";        // 가상계좌 결제결과 통보처리 페이지(사용자지정)
    $url_retmethod   = "curl";                                                       // 결과값 처리방법 (curl, iframe)
}

$ReqUrl  = isset($_REQUEST["ReqUrl"])  ? $_REQUEST["ReqUrl"]  : "";               // 결제창에서 결제실행전 돌아올때
$TradeNo = isset($_REQUEST["TradeNo"]) ? $_REQUEST["TradeNo"] : "";               // 결제완료 후 홈으로 리턴시 거래번호를 가져옴


?>
<!------------------------------------------------------------------------------------>

<div style="display:none;">
<form id="SendPayForm" name="SendPayForm" method="POST">
<input type="hidden" name="Frch_BsUqCode"   value="<?=$FrchBsUqCode ?>">
<input type="hidden" name="Frch_BrUqCode"   value="<?=$FrchBrUqCode ?>">
<input type="hidden" name="TestPay"         value="<?=$test_paysw ?>">
<input type="hidden" name="pay_repaybutyn" value="<?=$pay_repaybutyn?>">
<input type="hidden" name="pay_closeurl"    value="<?=$url_close ?>">
<input type="hidden" name="pay_requrl"	    value="<?=$url_payreqhome ?>">
<input type="hidden" name="pay_homeurl"     value="<?=$url_returnhome ?>">
<input type="hidden" name="pay_returl"      value="<?=$url_result ?>">
<input type="hidden" name="pay_returl_json" value="<?=$url_result_json?>">
<input type="hidden" name="pay_returl_curl" value="<?=$url_result_curl?>">
<input type="hidden" name="pay_vbnturl"     value="<?=$url_vbnotice ?>">
<input type="hidden" name="pay_retmethod"   value="<?=$url_retmethod ?>">

<input type="hidden" name="ReqUrl"          value="<?=$ReqUrl ?>">
<input type="hidden" name="Ret_URL"          value="<?=$ReqUrl ?>">

<input type="hidden" name="conf_site_name"  value="<?=$conf_site_name ?>">
<input type="hidden" name="pay_homekey"  value="<?=$pay_homekey?>"  />
<input type="hidden" name="pay_replaceurl"  value="<?=$pay_replaceurl?>"  />
<!----------------------- 쇼핑몰운영시 분할승인을 사용여부 파라메터(필수) ------------------------------>
<input type="hidden" name="conf_divpay_use"   value="N">
<input type="hidden" name="DivPayReq_UqCode"  value="">
<!--------------- 쇼핑몰구매상품 분할승인 구매내역 파라메터(필수아님-테스트용임) ---------------------------->
<input type="hidden" name="shop_buy_goods" value="">
<!-- goods_key[] => [셀프페이고유코드/상품코드/상품명/상품가격/구매수량] --->

<input type="text" name="ordr_idxx" value="<?=$ClassOrderPayNumber?>"><!-- 주문번호 -->
<input type="text" name="buyr_name" value="<?=$MemberName?>"><!-- 고객성명 -->
<input type="text" name="buyr_tel1" value="<?=str_replace("-","",$MemberPhone1)?>"><!-- 전화번호 -->
<input type="text" name="buyr_tel2" value="<?=str_replace("-","",$MemberPhone1)?>"><!-- 휴대폰 -->
<input type="text" name="buyr_mail" value="<?=$MemberEmail?>"><!-- 이메일 -->
<input type="text" name="good_name" value="망고아이 수강신청"><!-- 상품명 -->
<input type="text" name="good_mny" value=""><!-- 결제금액 -->
</form>
</div>



<script type="text/javascript">
function FormSubmit(RegType){
	if (confirm('결제상태로 변경하시겠습니까?')){
		
		ClassOrderPayNumber = document.SendPayForm.ordr_idxx.value;

		url = "./ajax_set_class_order_no_pay_action.php";
		//location.href = url + "?ClassOrderID="+ClassOrderID;
		$.ajax(url, {
			data: {
				ClassOrderPayNumber: ClassOrderPayNumber,
				RegType: RegType
			},
			success: function (data) {
				<?if ($ClassOrderMode=="LMS"){?>
					parent.location.href = "/lms/class_order_list.php?type=21";
				<?}else{?>
					parent.location.href = "mypage_payment_list.php";
				<?}?>
			},
			error: function () {
				
			}
		});


	}
}

var mah_mobile_customurl = null;
function handleOpenURL(url) {
     console.log("receivedurl: " + url);
     mah_mobile_customurl = url;
     console.log("mah_mobile_customurl="+mah_mobile_customurl);
}  


//---------------------------------------------------------------------------------------------//
// 브라우저에서 뒤로가기 기능막기
//---------------------------------------------------------------------------------------------//
history.pushState(null, null, location.href);
window.onpopstate = function(event) {

     history.go(1);

     alert("뒤로가기 버튼은 사용할 수 없습니다!");
};
//---------------------------------------------------------------------------------------------//
// PC | MOBILE 구분
//---------------------------------------------------------------------------------------------//
function device_check() {
    // 디바이스 종류 설정
    var pc_device = "win16|win32|win64|mac|macintel";
 
    // 접속한 디바이스 환경
    var this_device = navigator.platform;
 
    if ( this_device ) {
 
        if ( pc_device.indexOf(navigator.platform.toLowerCase()) < 0 ) {
            return 'MOBILE';
            //return 'PC';
        } else {
            return 'PC';
        }
 
    }
}


//--------------------------------------------------------------------------------------------//
// 화폐단위
//--------------------------------------------------------------------------------------------//
function numberWithCommas(x) {

      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

}
//--------------------------------------------------------------------------------------------//
// 영수증보기
//--------------------------------------------------------------------------------------------//
function PayReceipt() {

    var device_name = device_check();

	if (device_name == 'MOBILE') {
		document.SendPayForm.action = "https://www.selfpay.kr/" + document.SendPayForm.ReqUrl.value;
		document.SendPayForm.submit();
    }

}


</script>

<script type="text/javascript">
//-------------------------------------------------------------------------------------------------------------------------//
// 단문비교함수
//-------------------------------------------------------------------------------------------------------------------------//
function jviif( sw, a, b ) {

      if (sw) {
            return a;
      } else {
            return b;
      }

}



function PayActionClose(){
	parent.$.fn.colorbox.close();
}
//-----------------------------------------------------------------------------------//
// 결제요청실행
//-----------------------------------------------------------------------------------//

var PayActionNum = 0;
function PayAction() {
//-----------------------------------------------------------------------------------//

	PayActionNum = PayActionNum + 1;
	ClassOrderPayNumber = document.SendPayForm.ordr_idxx.value;
	ClassOrderPayNumber_Origin = "<?=$ClassOrderPayNumber?>";

	url = "./ajax_get_class_order_pay_monthnumberid.php";
	//location.href = url + "?ClassOrderPayNumber="+ClassOrderPayNumber;
	$.ajax(url, {
		data: {
			ClassOrderPayNumber: ClassOrderPayNumber,
			ClassOrderPayNumber_Origin: ClassOrderPayNumber_Origin,
			PayActionNum: PayActionNum
		},
		success: function (data) {
			AjaxCheckedClassOrderPayMonthNumberID = data.AjaxCheckedClassOrderPayMonthNumberID;
			NewClassOrderPayNumber = data.NewClassOrderPayNumber;
			document.SendPayForm.ordr_idxx.value = NewClassOrderPayNumber;//결제하기 버튼을 클릭할때마다 ClassOrderPayNumber 를 변경해 준다.

			//alert(AjaxCheckedClassOrderPayMonthNumberID+"-"+CheckedClassOrderPayMonthNumberID);
		
			if (CheckedClassOrderPayMonthNumberID!=AjaxCheckedClassOrderPayMonthNumberID){
				
				alert("가격 설정에 오류가 발생했습니다. 창은 닫고 다시 시도해 주시기 바랍니다.[1]");
				document.getElementById("BtnBox").style.display = "none";
					
			}else{

					var device_name = device_check();

					var divpay_use = document.SendPayForm.conf_divpay_use.value;
					rabbit=confirm("결제 하시겠습니까?");
					if(!rabbit) {
						  return;
					}


					//------------------------------------------------------------------------------//
					// PC 결제시
					//------------------------------------------------------------------------------//
					if (device_name == 'PC') {
					//------------------------------------------------------------------------------//

						document.getElementById("BtnPaymentAction").style.display = "none";
						document.getElementById("BtnPaymentActionClose").style.display = "";


						var pay_layer = document.getElementById('paylayer');
						pay_layer.style.display = 'block';

						var pay_url = "https://www.selfpay.kr/KCPPAY/pcpay/from_order.php";

						document.SendPayForm.target = 'kcppay';
						document.SendPayForm.action = pay_url;
						document.SendPayForm.submit();

					//-------------------------------------------------------------------------------//
					// MOBIL 결제시
					//-------------------------------------------------------------------------------//
					} else {
					//-------------------------------------------------------------------------------//

						document.getElementById("BtnPaymentAction").style.display = "none";
						document.getElementById("BtnPaymentActionClose").style.display = "";

						<?if ($FromDevice=="app" || $FromDevice=="app2"){?>

							Frch_BsUqCode = document.SendPayForm.Frch_BsUqCode.value;
							Frch_BrUqCode = document.SendPayForm.Frch_BrUqCode.value;
							TestPay = document.SendPayForm.TestPay.value;
							pay_closeurl = document.SendPayForm.pay_closeurl.value;
							pay_requrl = document.SendPayForm.pay_requrl.value;
							pay_homeurl = document.SendPayForm.pay_homeurl.value;
							pay_returl = document.SendPayForm.pay_returl.value;
							pay_returl_json = document.SendPayForm.pay_returl_json.value;
							pay_returl_curl = document.SendPayForm.pay_returl_curl.value;
							pay_vbnturl = document.SendPayForm.pay_vbnturl.value;
							pay_retmethod = document.SendPayForm.pay_retmethod.value;
							ReqUrl = document.SendPayForm.ReqUrl.value;
							conf_site_name = document.SendPayForm.conf_site_name.value;
							conf_divpay_use = document.SendPayForm.conf_divpay_use.value;
							DivPayReq_UqCode = document.SendPayForm.DivPayReq_UqCode.value;
							shop_buy_goods = document.SendPayForm.shop_buy_goods.value;
							ordr_idxx = document.SendPayForm.ordr_idxx.value;
							buyr_name = document.SendPayForm.buyr_name.value;
							buyr_tel1 = document.SendPayForm.buyr_tel1.value;
							buyr_tel2 = document.SendPayForm.buyr_tel2.value;
							buyr_mail = document.SendPayForm.buyr_mail.value;
							good_name = document.SendPayForm.good_name.value;
							good_mny = document.SendPayForm.good_mny.value;
							pay_homekey = document.SendPayForm.pay_homekey.value;
							pay_replaceurl = document.SendPayForm.pay_replaceurl.value;

							var pay_url = "https://www.selfpay.kr/mselfpay_sms_order.php";

							pay_url = pay_url + "?1=1&Frch_BsUqCode="+Frch_BsUqCode+"&Frch_BrUqCode="+Frch_BrUqCode+"&TestPay="+TestPay+"&pay_closeurl="+pay_closeurl+"&pay_requrl="+pay_requrl+"&pay_homeurl="+pay_homeurl+"&pay_returl="+pay_returl+"&pay_returl_json="+pay_returl_json+"&pay_returl_curl="+pay_returl_curl+"&pay_vbnturl="+pay_vbnturl+"&pay_retmethod="+pay_retmethod+"&ReqUrl="+ReqUrl+"&conf_site_name="+conf_site_name+"&conf_divpay_use="+conf_divpay_use+"&DivPayReq_UqCode="+DivPayReq_UqCode+"&shop_buy_goods="+shop_buy_goods+"&ordr_idxx="+ordr_idxx+"&buyr_name="+buyr_name+"&buyr_tel1="+buyr_tel1+"&buyr_tel2="+buyr_tel2+"&buyr_mail="+buyr_mail+"&good_name="+good_name+"&good_mny="+good_mny+"&pay_homekey="+pay_homekey+"&pay_replaceurl="+pay_replaceurl;

							var varUA = navigator.userAgent.toLowerCase(); //userAgent 값 얻기

							if ( varUA.indexOf('android') > -1) {
								//안드로이드
								cordova_iab.InAppOpenBrowser(pay_url);
							} else if ( varUA.indexOf("iphone") > -1||varUA.indexOf("ipad") > -1||varUA.indexOf("ipod") > -1 ) {
								//IOS
								var message = {
									command: 'openwebview',
									value: pay_url
								};
								window.webkit.messageHandlers.cordova_iab.postMessage(message);
							} else {
								//아이폰, 안드로이드 외
								alert("관리자에게 문의해주세요.");
							}
							
							setTimeout(InAppBrowserClose, 3000);
							setTimeout(ColorBoxClose, 3000);


						<?}else{?>

                            // alert 창 띄우기
                            // >>> 모바일 결제 안내 말씀 <<<
                            // 불편을 드려 대단히 죄송합니다. 현재 모바일 환경에서 결제 오류가 있어, 조치 중에 있습니다.
                            // 번거로우시더라도 PC 에서 수강신청 및 결제를 부탁드립니다.
                            // 시간이 다소 걸리더라도 제대로 고치겠습니다. 감사합니다.

                            // // 사이트 url 이 slpmangoi.com 으로 시작하는 경우에만 alert 창 띄우기
                            // if (window.location.href.indexOf('slpmangoi.com') > -1)
                            //
                            //     alert(">>> 모바일 결제 안내 말씀 <<<\n\n" +
                            //         "불편을 드려 대단히 죄송합니다. 현재 모바일 환경에서 결제 시 수강신청이 되지 않는 오류가 있습니다.\n\n" +
                            //         "현재 조치 중에 있으며, 다소 번거로우시더라도 PC 환경에서 수강신청 및 결제를 부탁드립니다.\n" +
                            //         "\n");


							var pay_url = "https://www.selfpay.kr/mselfpay_sms_order.php";
							document.SendPayForm.action = pay_url;
							document.SendPayForm.submit();

						<?}?>

					//-------------------------------------------------------------------------------//
					}
					//-------------------------------------------------------------------------------//
			
			}
		

		
		},
		error: function () {
			alert("가격 설정에 오류가 발생했습니다. 창은 닫고 다시 시도해 주시기 바랍니다.[2]")
		}
	});



//-----------------------------------------------------------------------------------//
}


function InAppBrowserClose(){
	
	window.Exit=true;

}

function ColorBoxClose(){
	parent.window.Exit=true;
}

//-----------------------------------------------------------------------------------//
// PC 결제창 닫기
//-----------------------------------------------------------------------------------//
function PayWindow_Close() {

    var pay_layer = document.getElementById('paylayer');
    var kcppay    = document.getElementById('kcppay');

    pay_layer.style.display = 'none';
    kcppay.src = '';

}

//-----------------------------------------------------------------------------------//
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

<script>
window.onload = function(){


	setTimeout(function() {
		if (parseInt(document.RegForm.ClassOrderPayUseSavedMoneyPrice.value) > (ClassOrderPaySellingPrice-2000)){
			document.RegForm.ClassOrderPayUseSavedMoneyPrice.value = (ClassOrderPaySellingPrice-2000);
			ChClassOrderPayMonthNumberID(1);
		}
	}, 500);

	
	ChClassOrderPayMonthNumberID(1);
	
	
}
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