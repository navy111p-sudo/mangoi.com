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
			
			$Sql = "select 
						count(*) as TotalCount
					from ProductOrderCarts A 
					where A.MemberID=:MemberID and A.ProductOrderCartState=2 and A.ProductOrderCartID in (select ProductOrderCartID from ProductOrderCartDetails) ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':MemberID', $MemberID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$TotalCount = $Row["TotalCount"];


			$Sql = "select 
						A.*,
						(select count(*) from ProductOrderCartDetails where ProductOrderCartID=A.ProductOrderCartID) as ProductOrderCartDetailCount,
						(select sum(ProductCount) from ProductOrderCartDetails where ProductOrderCartID=A.ProductOrderCartID) as ProductOrderCartDetailProductCount,
						(select sum(AA.ProductCount*BB.ProductPrice) from ProductOrderCartDetails AA inner join Products BB on AA.ProductID=BB.ProductID where AA.ProductOrderCartID=A.ProductOrderCartID) as ProductOrderCartDetailProductPrice
					from ProductOrderCarts A 
					where A.MemberID=:MemberID and A.ProductOrderCartState=2 and A.ProductOrderCartID in (select ProductOrderCartID from ProductOrderCartDetails)
					order by A.ProductOrderCartOrder desc";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':MemberID', $MemberID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			?>
            <div class="mypage_inner">
                <h3 class="caption_left_br">
                    교재구매 <b>바구니</b>
                    <a href="product_order_list.php?FromDevice=<?=$FromDevice?>" class="book_order_btn TrnTag">주문내역</a>
                </h3>
                <div <?if ($FromDevice=="app"){?>style="width:100%;"<?}else{?>class="overflow_table"<?}?>>
                    <table class="book_cart_table" <?if ($FromDevice=="app"){?>style="min-width:300px;"<?}?>>
                        <?if ($FromDevice==""){?>
							<col width="7%">
							<col width="">
							
							<col width="11.5%">
							<col width="11.5%">
							<col width="13.5%">
							<col width="13.5%">
							
							<col width="13.5%">
						<?}else{?>
							<col width="15%">
							<col width="">
							<col width="25%">
						<?}?>
                        <tr>
                            <th class="TrnTag">번호</th>
                            <th class="TrnTag">교재구매</th>
							<?if ($FromDevice==""){?>
                            <th class="TrnTag">교재종류수</th>
                            <th class="TrnTag">총교재수</th>
                            <th class="TrnTag">금액</th>
                            <th class="TrnTag">등록일</th>
							<?}?>
                            <th class="TrnTag">주문하기</th>
                        </tr>

						<?
						$ListCount=1;
						while($Row = $Stmt->fetch()) {
							$ListNumber = $TotalCount - $ListCount + 1;
							
							$ProductOrderCartID = $Row["ProductOrderCartID"];
							$ProductOrderCartName = $Row["ProductOrderCartName"];
							$ProductOrderCartState = $Row["ProductOrderCartState"];
							$ProductOrderCartRegDateTime = $Row["ProductOrderCartRegDateTime"];
							$ProductOrderCartModiDateTime = $Row["ProductOrderCartModiDateTime"];

							$StrProductOrderCartRegDateTime = str_replace("-",".",substr($ProductOrderCartRegDateTime, 0,10));

							$ProductOrderCartDetailCount = $Row["ProductOrderCartDetailCount"];
							$ProductOrderCartDetailProductCount = $Row["ProductOrderCartDetailProductCount"];
							$ProductOrderCartDetailProductPrice = $Row["ProductOrderCartDetailProductPrice"];
						?>
						<tr>
                            <td><?=$ListNumber?></td>
                            <td><?=$ProductOrderCartName?></td>
							<?if ($FromDevice==""){?>
                            <td><?=number_format($ProductOrderCartDetailCount,0)?></td>
                            <td><?=number_format($ProductOrderCartDetailProductCount,0)?></td>
                            <td><?=number_format($ProductOrderCartDetailProductPrice,0)?></td>
                            <td><?=$StrProductOrderCartRegDateTime?></td>
							<?}?>
                            <td><a href="product_order_form.php?ProductOrderCartID=<?=$ProductOrderCartID?>&FromDevice=<?=$FromDevice?>" class="book_cart_btn">주문하기</a></td>
                        </tr>
						<?php
							$ListCount ++;
						}
						$Stmt = null;

						if ($ListCount==1){
						?>
						<tr>
                            <td colspan="7" style="height:200px;text-align:center;line-height:1.5;"  class="TrnTag">현재 주문할 교재가 없습니다.<br>주문할 교재는 선생님께서 선택해 주십니다..</td>
                        </tr>

						<?
						}
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





