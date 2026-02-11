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

$ReqUrl = isset($_REQUEST["ReqUrl"]) ? $_REQUEST["ReqUrl"] : "";
if ($ReqUrl!=""){
	header("Location: product_order_list.php?FromDevice=$FromDevice"); 
	exit;
}

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
    <h1 class="header_app_title TrnTag">교재구매</h1>
	<?
	if ($FromDevice=="mypage"){
		setcookie("FromMyPage", "Y", 0, "/", ".".$DefaultDomain2);
		setcookie("FromMyPageMemberID", $_LINK_MEMBER_ID_, 0, "/", ".".$DefaultDomain2);
	?>
	<a href="javascript:parent.$.fn.colorbox.close();" class="header_app_close header_app_gray"><img src="images/btn_app_close_black.png" class="icon"></a>
	<?
	}else{
		setcookie("FromMyPage", "", 0, "/", ".".$DefaultDomain2);
		setcookie("FromMyPageMemberID", "", 0, "/", ".".$DefaultDomain2);
	?>
    <a href="javascript:window.Exit=true" class="header_app_close header_app_gray"><img src="images/btn_app_close_black.png" class="icon"></a>
	<?
	}
	?>
</header>
<div style="padding:30px;">

<?
}
?>

			<?
			$MemberID = $_LINK_MEMBER_ID_;
			$ProductOrderCartID = isset($_REQUEST["ProductOrderCartID"]) ? $_REQUEST["ProductOrderCartID"] : "";
			
			$Sql = "select 
						sum(A.ProductCount * B.ProductPrice) as TotCartPrice
					from ProductOrderCartDetails A 
						inner join Products B on A.ProductID=B.ProductID 
					where A.ProductOrderCartID=:ProductOrderCartID ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$TotCartPrice = $Row["TotCartPrice"];

			
			$Sql = "select 
						A.ProductSellerID,
						B.ProductSellerShipPriceType,
						B.ProductSellerOrderTotPrice,
						B.ProductSellerShipPrice
					from ProductOrderCarts A 
						inner join ProductSellers B on A.ProductSellerID=B.ProductSellerID 
					where A.ProductOrderCartID=:ProductOrderCartID ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$ProductSellerID = $Row["ProductSellerID"];
			$ProductSellerShipPriceType = $Row["ProductSellerShipPriceType"];
			$ProductSellerOrderTotPrice = $Row["ProductSellerOrderTotPrice"];
			$ProductSellerShipPrice = $Row["ProductSellerShipPrice"];

			$ShipPrice = 0;
			if ($ProductSellerShipPriceType==1){//무조건
				$ShipPrice = $ProductSellerShipPrice;
			}else if ($ProductSellerShipPriceType==2){//이하이면
				if ($TotCartPrice<=$ProductSellerOrderTotPrice){
					$ShipPrice = $ProductSellerShipPrice;
				}
			}else if ($ProductSellerShipPriceType==3){//이상이면
				if ($TotCartPrice>=$ProductSellerOrderTotPrice){
					$ShipPrice = $ProductSellerShipPrice;
				}
			}



			$Sql = "select 
				A.MemberID,
				A.MemberName,
				A.MemberLoginID,
				AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as MemberPhone1,
				AES_DECRYPT(UNHEX(A.MemberEmail),:EncryptionKey) as MemberEmail,
				A.MemberZip,
				A.MemberAddr1,
				A.MemberAddr2
				from Members A where A.MemberID=:MemberID";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
			$Stmt->bindParam(':MemberID', $MemberID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$MemberLoginID = $Row["MemberLoginID"];
			$MemberName = $Row["MemberName"];
			$MemberPhone1 = $Row["MemberPhone1"];
			$MemberEmail = $Row["MemberEmail"];
			$MemberZip = $Row["MemberZip"]; 
			$MemberAddr1 = $Row["MemberAddr1"]; 
			$MemberAddr2 = $Row["MemberAddr2"]; 

			$ReceiveName = $MemberName;
			$ReceivePhone1 = $MemberPhone1;
			$ProductOrderEmail = $MemberEmail;
			$ReceiveZipCode = $MemberZip;
			$ReceiveAddr1 = $MemberAddr1;
			$ReceiveAddr2 = $MemberAddr2;
			$ReceiveMemo = "";


			if ($FromDevice=="app"){
				$ProductOrderNumber = $BookOrderNumberHeader."A".date("YmdHis").substr("0000000000".$MemberID,-10);
			}else{
				$ProductOrderNumber = $BookOrderNumberHeader."H".date("YmdHis").substr("0000000000".$MemberID,-10);
			}
			$ProductOrderShipPrice = $ShipPrice;
			$ProductOrderName = "교재구매";

			$Sql = " insert into ProductOrders ( ";
				$Sql .= " ProductSellerID, ";
				$Sql .= " MemberID, ";
				$Sql .= " MemberName, ";
				$Sql .= " ProductOrderNumber, ";
				$Sql .= " ProductOrderName, ";
				$Sql .= " ReceiveName, ";
				$Sql .= " ReceivePhone1, ";
				$Sql .= " ReceivePhone2, ";
				$Sql .= " ReceiveZipCode, ";
				$Sql .= " ReceiveAddr1, ";
				$Sql .= " ReceiveAddr2, ";
				$Sql .= " ReceiveMemo, ";
				$Sql .= " ProductOrderEmail, ";
				$Sql .= " ProductOrderShipPrice, ";
				$Sql .= " ProductOrderCartID, ";
				$Sql .= " ProductOrderRegDateTime, ";
				$Sql .= " ProductOrderModiDateTime, ";
				$Sql .= " ProductOrderState ";
			$Sql .= " ) values ( ";
				$Sql .= " :ProductSellerID, ";
				$Sql .= " :MemberID, ";
				$Sql .= " :MemberName, ";
				$Sql .= " :ProductOrderNumber, ";
				$Sql .= " :ProductOrderName, ";
				$Sql .= " :ReceiveName, ";
				$Sql .= " HEX(AES_ENCRYPT(:ReceivePhone1, :EncryptionKey)), ";
				$Sql .= " HEX(AES_ENCRYPT(:ReceivePhone2, :EncryptionKey)), ";
				$Sql .= " :ReceiveZipCode, ";
				$Sql .= " :ReceiveAddr1, ";
				$Sql .= " :ReceiveAddr2, ";
				$Sql .= " :ReceiveMemo, ";
				$Sql .= " :ProductOrderEmail, ";
				$Sql .= " :ProductOrderShipPrice, ";
				$Sql .= " :ProductOrderCartID, ";
				$Sql .= " now(), ";
				$Sql .= " now(), ";
				$Sql .= " 1 ";
			$Sql .= " ) ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ProductSellerID', $ProductSellerID);
			$Stmt->bindParam(':MemberID', $MemberID);
			$Stmt->bindParam(':MemberName', $MemberName);
			$Stmt->bindParam(':ProductOrderNumber', $ProductOrderNumber);
			$Stmt->bindParam(':ProductOrderName', $ProductOrderName);
			$Stmt->bindParam(':ReceiveName', $ReceiveName);
			$Stmt->bindParam(':ReceivePhone1', $ReceivePhone1);
			$Stmt->bindParam(':ReceivePhone2', $ReceivePhone2);
			$Stmt->bindParam(':ReceiveZipCode', $ReceiveZipCode);
			$Stmt->bindParam(':ReceiveAddr1', $ReceiveAddr1);
			$Stmt->bindParam(':ReceiveAddr2', $ReceiveAddr2);
			$Stmt->bindParam(':ReceiveMemo', $ReceiveMemo);
			$Stmt->bindParam(':ProductOrderEmail', $ProductOrderEmail);
			$Stmt->bindParam(':ProductOrderShipPrice', $ProductOrderShipPrice);
			$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);

			$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
			$Stmt->execute();
			$ProductOrderID = $DbConn->lastInsertId();
			$Stmt = null;




			$Sql = "select 
						count(*) as TotalCount
					from ProductOrderCartDetails A 
						inner join Products B on A.ProductID=B.ProductID 
						inner join ProductOrderCarts C on A.ProductOrderCartID=C.ProductOrderCartID 
					where A.ProductOrderCartID=:ProductOrderCartID and C.MemberID=:MemberID ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
			$Stmt->bindParam(':MemberID', $MemberID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$TotalCount = $Row["TotalCount"];

			
			$Sql = "select 
						A.*,
						B.ProductSellerBookID,
						B.ProductName,
						B.ProductViewPrice,
						B.ProductPrice,
						B.ProductImageFileName,
						B.ProductImageFileRealName
					from ProductOrderCartDetails A 
						inner join Products B on A.ProductID=B.ProductID 
						inner join ProductOrderCarts C on A.ProductOrderCartID=C.ProductOrderCartID 
					where A.ProductOrderCartID=:ProductOrderCartID  and C.MemberID=:MemberID 
					order by A.ProductCartDetailOrder asc";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ProductOrderCartID', $ProductOrderCartID);
			$Stmt->bindParam(':MemberID', $MemberID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);


			?>
            <div class="mypage_inner">
                <h3 class="caption_left_br">
                    <trn  class="TrnTag">교재 <b>구매하기</b></trn>
                    <a href="product_order_cart.php?FromDevice=<?=$FromDevice?>" class="book_order_btn TrnTag">장바구니</a>
                </h3>
                <div class="overflow_table">
                    <table class="book_cart_table">
                        <col width="11.5%">
                        <col width="">
                        <col width="14%">
                        <col width="14%">
                        <col width="14%">
                        <tr>
                            <th class="TrnTag">구분</th>
                            <th class="TrnTag">교재명</th>
                            <th class="TrnTag">금액</th>
							<th class="TrnTag">수량</th>
                            <th class="TrnTag">합계</th>
                        </tr>
						<?
						$ListCount=1;
						$ProductListCount=0;
						$ProductPriceTotSum = 0;
						$ProductOrderName = "";

						while($Row = $Stmt->fetch()) {
							$ListNumber = $TotalCount - $ListCount + 1;
							
							$ProductOrderCartDetailID = $Row["ProductOrderCartDetailID"];
							$ProductID = $Row["ProductID"];
							$ProductCount = $Row["ProductCount"];
							$RegMemberID = $Row["RegMemberID"];
							$ModiMemberID = $Row["ModiMemberID"];

							$ProductSellerBookID = $Row["ProductSellerBookID"];
							$ProductName = $Row["ProductName"];
							$ProductViewPrice = $Row["ProductViewPrice"];
							$ProductPrice = $Row["ProductPrice"];

							$ProductImageFileName = $Row["ProductImageFileName"];
							$ProductImageFileRealName = $Row["ProductImageFileRealName"];

							$StrProductImage = "./uploads/product_images/".$ProductImageFileName;

							$ProductPriceSum = $ProductPrice * $ProductCount;

							$ProductPriceTotSum = $ProductPriceTotSum + $ProductPriceSum;

							if ($ListCount==1){
								$ProductOrderName = $ProductName;
							}

							//상품 상세 구성
							$Sql2 = " insert into ProductOrderDetails ( ";
								$Sql2 .= " ProductOrderID, ";
								$Sql2 .= " ProductID, ";
								$Sql2 .= " ProductSellerBookID, ";
								$Sql2 .= " ProductName, ";
								$Sql2 .= " ProductPrice, ";
								$Sql2 .= " ProductCount, ";
								$Sql2 .= " RegMemberID, ";
								$Sql2 .= " ProductOrderDetailRegDateTime, ";
								$Sql2 .= " ProductOrderDetailModiDateTime, ";
								$Sql2 .= " ProductOrderDetailState ";
							$Sql2 .= " ) values ( ";
								$Sql2 .= " :ProductOrderID, ";
								$Sql2 .= " :ProductID, ";
								$Sql2 .= " :ProductSellerBookID, ";
								$Sql2 .= " :ProductName, ";
								$Sql2 .= " :ProductPrice, ";
								$Sql2 .= " :ProductCount, ";
								$Sql2 .= " :RegMemberID, ";
								$Sql2 .= " now(), ";
								$Sql2 .= " now(), ";
								$Sql2 .= " 1 ";
							$Sql2 .= " ) ";

							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->bindParam(':ProductOrderID', $ProductOrderID);
							$Stmt2->bindParam(':ProductID', $ProductID);
							$Stmt2->bindParam(':ProductSellerBookID', $ProductSellerBookID);
							$Stmt2->bindParam(':ProductName', $ProductName);
							$Stmt2->bindParam(':ProductPrice', $ProductPrice);
							$Stmt2->bindParam(':ProductCount', $ProductCount);
							$Stmt2->bindParam(':RegMemberID', $RegMemberID);
							$Stmt2->execute();
							$Stmt2 = null;
							//상품 상세 구성

						?>

                        <tr>
                            <td><?=$ListNumber?></td>
                            <td>
                                <div class="book_buy_name">
                                    <div class="book_buy_img" style="background-image:url(<?=$StrProductImage?>);"></div>
                                    <div class="book_buy_caption"><?=$ProductName?></div>
                                </div>
                            </td>
                            <td><?=number_format($ProductPrice,0)?></td>
							<td><?=number_format($ProductCount,0)?></td>
                            <td><?=number_format($ProductPriceSum,0)?></td>
                        </tr>
						<?php
							$ListCount ++;
							$ProductListCount++;
						}
						$Stmt = null;


						if ($ProductListCount>1){
							$ProductOrderName = $ProductOrderName . "(외 ".($ProductListCount-1).")";
						}

						//구매명 업데이트
						$Sql = "update ProductOrders set ProductOrderName=:ProductOrderName where ProductOrderID=:ProductOrderID";
						$Stmt = $DbConn->prepare($Sql);
						$Stmt->bindParam(':ProductOrderName', $ProductOrderName);
						$Stmt->bindParam(':ProductOrderID', $ProductOrderID);
						$Stmt->execute();
						$Stmt = null;
						//구매명 업데이트


						if ($ListCount==1){
						?>
						<tr>
                            <td colspan="5" style="height:200px;text-align:center;line-height:200px;" class="TrnTag">잘못된 접근 입니다.</td>
                        </tr>

						<?
						}


						$ProductPriceTotSumPlusShip = $ProductPriceTotSum + $ShipPrice;
						?>


                        <tr>
                            <td colspan="5" class="book_buy_total"><?=number_format($ProductPriceTotSum,0)?> + <?=number_format($ShipPrice,0)?> (배송료) = <b><?=number_format($ProductPriceTotSumPlusShip,0)?></b></td>
                        </tr>
                    </table>
                </div>

				<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8" autocomplete="off">
                <input type="hidden" name="ProductOrderID" id="ProductOrderID" value="<?=$ProductOrderID?>">
                <table class="book_buy_info">
                    <tr>
                        <th class="TrnTag">받을 사람</th>
                        <td><input type="text" name="ReceiveName" id="ReceiveName" value="<?=$ReceiveName?>" class="book_buy_input"></td>
                    </tr>
                    <tr>
                        <th class="TrnTag">전화번호</th>
                        <td><input type="text" name="ReceivePhone1" id="ReceivePhone1" value="<?=$ReceivePhone1?>" class="book_buy_input"></td>
                    </tr>
                    <tr>
                        <th class="TrnTag">이메일</th>
                        <td><input type="text" name="OrderEmail" id="ProductOrderEmail" value="<?=$ProductOrderEmail?>" class="book_buy_input"></td>
                    </tr>
                    <tr>
                        <th class="TrnTag">주소</th>
                        <td>
                            <input type="text" name="ReceiveZipCode" id="ReceiveZipCode" value="<?=$ReceiveZipCode?>" class="book_buy_input zip">
                            <a href="javascript:ExecDaumPostcode();" class="book_zip_btn">우편번호</a>
                            <input type="text" name="ReceiveAddr1" id="ReceiveAddr1" value="<?=$ReceiveAddr1?>" class="book_buy_input margin">
                            <input type="text" name="ReceiveAddr2" id="ReceiveAddr2" value="<?=$ReceiveAddr2?>" class="book_buy_input">
                        </td>
                    </tr>
                    <tr>
                        <th class="TrnTag">전달메시지</th>
                        <td><textarea name="ReceiveMemo" id="ReceiveMemo" class="book_buy_text"><?=$ReceiveMemo?></textarea></td>
                    </tr>
                </table>
				</form>
                <div class="book_buy_btns">
                    <a href="product_order_cart.php" class="book_left_btn TrnTag">장바구니</a>
                    <a href="javascript:CheckOrder();" class="book_right_btn TrnTag">주문하기</a>
                </div>
				
				<br><br><br><br><br><br><br><br>
                
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


</script>


<div id="layer" style="padding-top:20px;display:none;position:fixed;overflow:hidden;z-index:100000;-webkit-overflow-scrolling:touch;background-color:#ffffff;">
<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" onclick="closeDaumPostcode()" alt="닫기 버튼">
</div>
<script src="https://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
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
$FrchBrUqCode    = "";							// 지점고유코드(**)
//$test_paysw      = "Y";                         // 테스트결제시-Y, 실결제시-N
$test_paysw      = "N";                         // 테스트결제시-Y, 실결제시-N
$pay_repaybutyn = "N";                         // 동일 주문번호 재결제 가능-Y, 재결제 불가-N
$conf_site_name  = "MANGOI";				// PC결제 - 상호[반드시 영문으로만지정] 
$domain_name     = "https://".$DefaultDomain2;   // 도메인
$url_close       = "https://".$DefaultDomain2 . "/product_order_pay_close.php";        // PC 결제일 경우에 KCP결제창 닫기 페이지(사용자지정)


if ($FromDevice=="app"){
	$url_payreqhome  = "SelfPayExit";          // 결제요청페이지(사용자지정)
	$url_returnhome  = "SelfPayExit";      // 결제후 최종돌아갈 홈페이지(사용자지정)
	$pay_homekey   = "mangoi://kr.ahsol.mangoi";     // 앱-홈(사용자지정)
	$pay_replaceurl   = "https://mangoi.co.kr"; //앱에서 결제 종료후 시스템 브라우져 이동할 페이지
}else{
	$url_payreqhome  = "https://".$DefaultDomain2 . "/product_order_list.php?FromDevice=<?=$FromDevice?>";          // 결제요청페이지(사용자지정)
	$url_returnhome  = "https://".$DefaultDomain2 . "/product_order_list.php?FromDevice=<?=$FromDevice?>";      // 결제후 최종돌아갈 홈페이지(사용자지정)
	$pay_homekey   = "";     // 앱-홈(사용자지정)
	$pay_replaceurl   = ""; //앱에서 결제 종료후 시스템 브라우져 이동할 페이지
}
$url_result      = "https://".$DefaultDomain2 . "/product_order_pay_result.php";       // 결제결과처리 페이지(사용자지정)
$url_result_json = "https://".$DefaultDomain2 . "/product_order_pay_result_json.php";  // 결제결과처리 JSON 페이지(사용자지정)
$url_result_curl = "https://".$DefaultDomain2 . "/product_order_pay_result_curl.php?";  // 결제결과처리 JSON 페이지(사용자지정)
$url_vbnotice    = "https://".$DefaultDomain2 . "/product_order_pay_result_vbank.php";        // 가상계좌 결제결과 통보처리 페이지(사용자지정)
$url_retmethod   = "curl";                                                       // 결과값 처리방법 (curl, iframe)

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
<input type="hidden" name="conf_site_name"  value="<?=$conf_site_name ?>">
<input type="hidden" name="pay_homekey"  value="<?=$pay_homekey?>"  />
<input type="hidden" name="pay_replaceurl"  value="<?=$pay_replaceurl?>"  />
<!----------------------- 쇼핑몰운영시 분할승인을 사용여부 파라메터(필수) ------------------------------>
<input type="hidden" name="conf_divpay_use"   value="N">
<input type="hidden" name="DivPayReq_UqCode"  value="">
<!--------------- 쇼핑몰구매상품 분할승인 구매내역 파라메터(필수아님-테스트용임) ---------------------------->
<input type="hidden" name="shop_buy_goods" value="">
<!-- goods_key[] => [셀프페이고유코드/상품코드/상품명/상품가격/구매수량] --->

<input type="text" name="ordr_idxx" value="<?=$ProductOrderNumber?>"><!-- 주문번호 -->
<input type="text" name="buyr_name" value="<?=$ReceiveName?>"><!-- 고객성명 -->
<input type="text" name="buyr_tel1" value="<?=str_replace("-","",$ReceivePhone1)?>"><!-- 전화번호 -->
<input type="text" name="buyr_tel2" value="<?=str_replace("-","",$ReceivePhone1)?>"><!-- 휴대폰 -->
<input type="text" name="buyr_mail" value="<?=$ProductOrderEmail?>"><!-- 이메일 -->
<input type="text" name="good_name" value="<?=$ProductOrderName?>"><!-- 상품명 -->
<input type="text" name="good_mny" id="good_mny" value="<?=$ProductPriceTotSumPlusShip?>"><!-- 결제금액 -->
</form>



<script type="text/javascript">
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

//-----------------------------------------------------------------------------------//
// 결제요청실행
//-----------------------------------------------------------------------------------//
function PayAction() {
//-----------------------------------------------------------------------------------//

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

			<?if ($FromDevice=="app"){?>

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

				cordova_iab.InAppOpenBrowser(pay_url);
				setTimeout(InAppBrowserClose, 3000);


			<?}else{?>

				var pay_url = "https://www.selfpay.kr/mselfpay_sms_order.php";
				document.SendPayForm.action = pay_url;
				document.SendPayForm.submit();

			<?}?>


    //-------------------------------------------------------------------------------//
	}
    //-------------------------------------------------------------------------------//

//-----------------------------------------------------------------------------------//
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
function CheckOrder(){

	CheckOk = 1;
	obj = document.RegForm.ReceiveName;
	if (obj.value==""){
		alert('받을사람 이름을 입력하세요.');
		CheckOk=0;
		return;
	}

	obj = document.RegForm.ReceiveZipCode;
	if (obj.value==""){
		alert('우편번호를 입력하세요.');
		CheckOk=0;
		return;
	}

	obj = document.RegForm.ReceiveAddr1;
	if (obj.value==""){
		alert('주소를 입력하세요.');
		CheckOk=0;
		return;
	}

	if (CheckOk==1){
	

		//document.RegForm.action = "ajax_set_product_order.php";
		//document.RegForm.submit();


		url = "ajax_set_product_order.php";

		var params =  $("#RegForm").serialize();
		jQuery.ajax({
			url: url,
			type: 'POST',
			data:params,
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8', 
			dataType: 'html',
			success: function (data) {
				PayAction();
			},
			error: function () {

			}
		});


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





