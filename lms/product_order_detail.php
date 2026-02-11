<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<?php
$ProductOrderID = isset($_REQUEST["ProductOrderID"]) ? $_REQUEST["ProductOrderID"] : "";


$Sql = "
		select 
				A.*,
				AES_DECRYPT(UNHEX(A.ReceivePhone1),:EncryptionKey) as DecReceivePhone1,
				B.ProductSellerCancelLiminTime 
		from ProductOrders A 
			inner join ProductSellers B on A.ProductSellerID=B.ProductSellerID 
		where A.ProductOrderID=:ProductOrderID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductOrderID', $ProductOrderID);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ProductSellerID = $Row["ProductSellerID"];
$ReceiveName = $Row["ReceiveName"];
$ReceivePhone1 = $Row["DecReceivePhone1"];
$ReceiveZipCode = $Row["ReceiveZipCode"];
$ReceiveAddr1 = $Row["ReceiveAddr1"];
$ReceiveAddr2 = $Row["ReceiveAddr2"];
$ReceiveMemo = $Row["ReceiveMemo"];
$ProductOrderEmail = $Row["ProductOrderEmail"];


$ProductOrderName = $Row["ProductOrderName"];
$ProductOrderState = $Row["ProductOrderState"];
$ProductOrderShipState = $Row["ProductOrderShipState"];
$ProductOrderShipNumber = $Row["ProductOrderShipNumber"];
$ProductOrderShipPrice = $Row["ProductOrderShipPrice"];

$CancelDateTime = $Row["CancelDateTime"];
$ShipDateTime = $Row["ShipDateTime"];

$ProductSellerCancelLiminTime = $Row["ProductSellerCancelLiminTime"];

$StrProductSellerCancelLiminTime = "";
if ($ProductSellerID==2){//올북스
	//$StrProductSellerCancelLiminTime = "<span style='color:#cc0000;'>※ ".substr($ProductSellerCancelLiminTime, 0,2)."시 ".substr($ProductSellerCancelLiminTime, -2)."분 이후에는 올북스 배송이 진행중일 수 있습니다. 배송 여부를 확인 후 취소 하시기 바랍니다.</span>";
	$StrProductSellerCancelLiminTime = "<span style='color:#cc0000;'>※ 올북스 주문제작 및 배송이 진행중일 수 있습니다. 올북스 측에 배송 여부를 확인 후 취소 하시기 바랍니다.</span>";
}


if ($CancelDateTime!="" && $ProductOrderState==33){
	$CancelDateTime = substr($CancelDateTime, 0,10);
}else{
	$CancelDateTime = "";
}
if ($ShipDateTime!="" && ($ProductOrderShipState==21 || $ProductOrderShipState==33)){
	$ShipDateTime = substr($ShipDateTime, 0,10);
}else{
	$ShipDateTime = "";
}
?>


<div id="page_content">
	<div id="page_content_inner">

		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-10-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$ProductOrderName?></span><span class="sub-heading" id="user_edit_position">교재 구매 바구니</span></h2>
						</div>
					</div>


					<div class="user_content">
						<table class="uk-table uk-table-align-vertical">
							<thead>
								<tr>
									<th width="10%"><?=$번호[$LangID]?></th>
									<th><?=$교재명[$LangID]?></th>
									<th width="15%"><?=$판매가[$LangID]?></th>
									<th width="15%"><?=$수량[$LangID]?></th>
									<th width="15%"><?=$합계[$LangID]?></th>
								</tr>
							</thead>
							<tbody>
							<?

							$Sql = "select 
										A.*
									from ProductOrderDetails A 
									where A.ProductOrderID=:ProductOrderID 
										and A.ProductOrderDetailState=1 
									order by A.ProductOrderDetailID asc";

							$Stmt = $DbConn->prepare($Sql);
							$Stmt->bindParam(':ProductOrderID', $ProductOrderID);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);

							$ListCount=1;
							$ProductPriceTotSum = 0;
							while($Row = $Stmt->fetch()) {
								$ProductOrderDetailID = $Row["ProductOrderDetailID"];
								$ProductID = $Row["ProductID"];
								$ProductCount = $Row["ProductCount"];
								$ProductName = $Row["ProductName"];
								$ProductPrice = $Row["ProductPrice"];

								$ProductPriceSum = $ProductPrice * $ProductCount;

								$ProductPriceTotSum = $ProductPriceTotSum + $ProductPriceSum;

							?>
							<tr>
								<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
								<td class="uk-text-nowrap"><?=$ProductName?></td>
								<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductPrice,0)?></td>
								<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductCount,0)?></td>
								<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductPriceSum,0)?></td>
							</tr>
							<?php
								$ListCount ++;
							}
							$Stmt = null;
							?>

							<tr>
								<td class="uk-text-nowrap uk-table-td-center" style="background-color:#f1f1f1;" colspan="2"><?=$합계[$LangID]?></td>
								<td class="uk-text-nowrap uk-table-td-center" style="background-color:#f1f1f1;" colspan="4"><?=number_format($ProductPriceTotSum,0)?> + <?=number_format($ProductOrderShipPrice,0)?>(배송비) = <?=number_format($ProductPriceTotSum+$ProductOrderShipPrice,0)?></td>
							</tr>
						
							</tbody>
						</table>
					</div>



					<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
					<input type="hidden" name="ProductOrderID" value="<?=$ProductOrderID?>">
					
					<div class="user_content" style="margin-top:-60px;">

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-3-10">
									<label for="ReceiveName"><?=$수취인[$LangID]?></label>
									<input type="text" id="ReceiveName" name="ReceiveName" value="<?=$ReceiveName?>" class="md-input label-fixed" />
								</div>
								<div class="uk-width-medium-4-10">
									<label for="ReceivePhone1"><?=$전화번호[$LangID]?></label>
									<input type="text" id="ReceivePhone1" name="ReceivePhone1" value="<?=$ReceivePhone1?>" class="md-input label-fixed"/>
								</div>
								<div class="uk-width-medium-3-10">
									<label for="ProductOrderEmail"><?=$이메일[$LangID]?></label>
									<input type="text" id="ProductOrderEmail" name="ProductOrderEmail" value="<?=$ProductOrderEmail?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>
						

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
									<label for="ReceiveZipCode"><?=$우편번호[$LangID]?></label>
									<input type="text" id="ReceiveZipCode" name="ReceiveZipCode" value="<?=$ReceiveZipCode?>" class="md-input label-fixed"/>
								</div>
								<div class="uk-width-medium-1-10">
									<a type="button" href="javascript:ExecDaumPostcode();" class="md-btn md-btn-gray"><?=$검색[$LangID]?></a>
								</div>
								<div class="uk-width-medium-4-10">
									<label for="ReceiveAddr1"><?=$주소[$LangID]?></label>
									<input type="text" id="ReceiveAddr1" name="ReceiveAddr1" value="<?=$ReceiveAddr1?>" class="md-input label-fixed"/>
								</div>
								<div class="uk-width-medium-3-10">
									<label for="ReceiveAddr2"><?=$상세주소[$LangID]?></label>
									<input type="text" id="ReceiveAddr2" name="ReceiveAddr2" value="<?=$ReceiveAddr2?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10">
									<textarea name="ReceiveMemo" id="ReceiveMemo" style="width:100%;height:60px;margin-top:5px;margin-bottom:5px;"><?=$ReceiveMemo?></textarea>
								</div>
							</div>
						</div>
					
					
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-5-10 uk-form-row">
									<span class="icheck-inline">
										<input type="radio" id="ProductOrderState_21" name="ProductOrderState" value="21" <?php if ($ProductOrderState==21) { echo "checked";}?> data-md-icheck/>
										<label for="ProductOrderState_21" class="inline-label"><?=$결제완료[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" id="ProductOrderState_33" name="ProductOrderState" value="33" <?php if ($ProductOrderState==33) { echo "checked";}?> data-md-icheck/>
										<label for="ProductOrderState_33" class="inline-label"><?=$취소완료[$LangID]?></label>
									</span>

								</div>
								<div class="uk-width-medium-5-10">
									<label for="CancelDateTime"><?=$취소일[$LangID]?></label>
									<input type="text" id="CancelDateTime" name="CancelDateTime" value="<?=$CancelDateTime?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}"/>
								</div>
							</div>
							<div style="margin-top:10px;text-align:right;"><?=$StrProductSellerCancelLiminTime?></div>
						</div>


						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-5-10 uk-form-row">
									<span class="icheck-inline">
										<input type="radio" id="ProductOrderShipState_1" name="ProductOrderShipState" value="1" <?php if ($ProductOrderShipState==1) { echo "checked";}?> data-md-icheck/>
										<label for="ProductOrderShipState_1" class="inline-label"><?=$주문접수[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" id="ProductOrderShipState_21" name="ProductOrderShipState" value="21" <?php if ($ProductOrderShipState==21) { echo "checked";}?> data-md-icheck/>
										<label for="ProductOrderShipState_21" class="inline-label"><?=$발송완료[$LangID]?></label>
									</span>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-5-10">
									<label for="ProductOrderShipNumber"><?=$송장번호[$LangID]?></label>
									<input type="text" id="ProductOrderShipNumber" name="ProductOrderShipNumber" value="<?=$ProductOrderShipNumber?>" class="md-input label-fixed"/>
								</div>
								<div class="uk-width-medium-5-10">
									<label for="ShipDateTime"><?=$발송일[$LangID]?></label>
									<input type="text" id="ShipDateTime" name="ShipDateTime" value="<?=$ShipDateTime?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}"/>
								</div>
							</div>
						</div>





						<div class="uk-margin-top" style="text-align:center;">
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?=$저장하기[$LangID]?></a>
						</div>

					</div>

					</form>


				</div>
			</div>

		</div>
		

	</div>
</div>


<div id="layer" style="padding-top:20px;display:none;position:fixed;overflow:hidden;z-index:100000;-webkit-overflow-scrolling:touch;background-color:#ffffff;">
<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" onclick="closeDaumPostcode()" alt="닫기 버튼">
</div>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script>
    // 우편번호 찾기 화면을 넣을 element
    var element_layer = document.getElementById('layer');

    function closeDaumPostcode() {
        // iframe을 넣은 element를 안보이게 한다.
        element_layer.style.display = 'none';
    }

    function ExecDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullAddr = data.address; // 최종 주소 변수
                var extraAddr = ''; // 조합형 주소 변수

                // 기본 주소가 도로명 타입일때 조합한다.
                if(data.addressType === 'R'){
                    //법정동명이 있을 경우 추가한다.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있을 경우 추가한다.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                document.getElementById('ReceiveZipCode').value = data.zonecode; //5자리 새우편번호 사용
                document.getElementById('ReceiveAddr1').value = fullAddr;
                //document.getElementById('sample2_addressEnglish').value = data.addressEnglish;

                // iframe을 넣은 element를 안보이게 한다.
                // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
                element_layer.style.display = 'none';
            },
            width : '100%',
            height : '100%'
        }).embed(element_layer);

        // iframe을 넣은 element를 보이게 한다.
        element_layer.style.display = 'block';

        // iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
        initLayerPosition();
    }

    // 브라우저의 크기 변경에 따라 레이어를 가운데로 이동시키고자 하실때에는
    // resize이벤트나, orientationchange이벤트를 이용하여 값이 변경될때마다 아래 함수를 실행 시켜 주시거나,
    // 직접 element_layer의 top,left값을 수정해 주시면 됩니다.
    function initLayerPosition(){
        var width = 500; //우편번호서비스가 들어갈 element의 width
        var height = 600; //우편번호서비스가 들어갈 element의 height
        var borderWidth = 5; //샘플에서 사용하는 border의 두께

        // 위에서 선언한 값들을 실제 element에 넣는다.
        element_layer.style.width = width + 'px';
        element_layer.style.height = height + 'px';
        element_layer.style.border = borderWidth + 'px solid';
        // 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
        element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
        element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
    }
</script>


<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<script>

function FormSubmit(){

	ProductOrderState = $(':radio[name="ProductOrderState"]:checked').val();
	ProductOrderShipState = $(':radio[name="ProductOrderShipState"]:checked').val();
	CancelDateTime = document.RegForm.CancelDateTime.value;
	ShipDateTime = document.RegForm.ShipDateTime.value;

	if (ProductOrderState=="33" && CancelDateTime==""){
		UIkit.modal.alert("취소일을 입력하세요.");
		obj.focus();
		return;
	}

	if (ProductOrderShipState=="21" && ShipDateTime==""){
		UIkit.modal.alert("발송일을 입력하세요.");
		obj.focus();
		return;	
	}


	UIkit.modal.confirm(
		'변경 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "product_order_detail_action.php";
			document.RegForm.submit();
		}
	);

}

</script>




<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>