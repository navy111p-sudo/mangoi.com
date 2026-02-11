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
<link rel="stylesheet" type="text/css" href="css/common.css" />
</head>
</body>
<?

$err_num = 0;
$err_msg = "";

$CouponID = isset($_REQUEST["CouponID"]) ? $_REQUEST["CouponID"] : "";
$CouponDetailID = isset($_REQUEST["CouponDetailID"]) ? $_REQUEST["CouponDetailID"] : "";
$CouponDetailNumber = isset($_REQUEST["CouponDetailNumber"]) ? $_REQUEST["CouponDetailNumber"] : "";
$CouponDetailState = isset($_REQUEST["CouponDetailState"]) ? $_REQUEST["CouponDetailState"] : "";

$CouponCount = isset($_REQUEST["CouponCount"]) ? $_REQUEST["CouponCount"] : "";



if ($CouponDetailID!=""){

	$Sql = " update CouponDetails set ";
		$Sql .= " CouponDetailModiDateTime = now(), ";
		$Sql .= " CouponDetailState = :CouponDetailState ";
	$Sql .= " where CouponDetailID = :CouponDetailID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CouponDetailState', $CouponDetailState);
	$Stmt->bindParam(':CouponDetailID', $CouponDetailID);
	$Stmt->execute();
	$Stmt = null;



}else{

	$ii=0;
	while ($ii<$CouponCount){


		$CouponDetailNumber = mongoi_coupon_generator(12);

		$Sql = "
				select 
					A.CouponDetailID
				from CouponDetails A 
				where A.CouponDetailNumber=:CouponDetailNumber ";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':CouponDetailNumber', $CouponDetailNumber);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$CouponDetailID = $Row["CouponDetailID"];


		if (!$CouponDetailID){

			$Sql = " insert into CouponDetails ( ";
				$Sql .= " CouponID, ";
				$Sql .= " CouponDetailNumber, ";
				$Sql .= " CouponDetailRegDateTime, ";
				$Sql .= " CouponDetailModiDateTime, ";
				$Sql .= " CouponDetailState ";
			$Sql .= " ) values ( ";
				$Sql .= " :CouponID, ";
				$Sql .= " :CouponDetailNumber, ";
				$Sql .= " now(), ";
				$Sql .= " now(), ";
				$Sql .= " 1 ";
			$Sql .= " ) ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':CouponID', $CouponID);
			$Stmt->bindParam(':CouponDetailNumber', $CouponDetailNumber);
			$Stmt->execute();
			$Stmt = null;

			$ii++;
		}

	}
}


function mongoi_coupon_generator($len){
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ123456789";

    srand((double)microtime()*1000000);

    $i = 0;
    $str = "";

    while ($i < $len) {
        $num = rand() % strlen($chars);
        $tmp = substr($chars, $num, 1);
        $str .= $tmp;
        $i++;
    }

    $str = preg_replace("/([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})/", "\1-\2-\3-\4", $str);

    return $str;
}
?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>
parent.$.fn.colorbox.close();
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

