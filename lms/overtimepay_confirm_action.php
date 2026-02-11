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
$MemberLoginID     = isset($_COOKIE["LoginMemberID"   ]) ? $_COOKIE["LoginMemberID"   ] : "";
$PayMonth = isset($_REQUEST["PayMonth"]) ? $_REQUEST["PayMonth"] : "";
$ApprovalOK = isset($_REQUEST["ApprovalOK"]) ? $_REQUEST["ApprovalOK"] : 0;
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$OverTimePay = isset($_REQUEST["OverTimePay"]) ? $_REQUEST["OverTimePay"] : 0;

?>
<link rel="stylesheet" type="text/css" href="css/common.css" />
</head>
</body>
<?

$err_num = 0;
$err_msg = "";


// 현재 급여정보가 입력중인지 확인. 만약 입력된 정보가 없거나, 이미 결재요청중이거나 지급완료인 경우에는 진행하지 않는다.
$Sql = "SELECT *
			FROM PayMonthState 
			WHERE PayMonth = :PayMonth AND PayState = 0";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':PayMonth', $PayMonth);
$Stmt->execute();
$rowCount = $Stmt->rowCount();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();


if ($rowCount > 0 && $ApprovalOK == 1) {
	$PayMonthStateID = $Row["PayMonthStateID"];

	
	// 승인 정보 업데이트

	$Sql = "UPDATE PayOverTime SET
				Approval=1 
				WHERE MemberID=:MemberID AND PayMonthStateID = :PayMonthStateID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':PayMonthStateID', $PayMonthStateID);
			
	$Stmt->execute();
	$Stmt = null;

	// 승인 처리후 Pay 테이블에 초과근무수당 필드를 업데이트한다.
	$Sql = "UPDATE Pay SET
				OverTimePay = :OverTimePay 
				WHERE MemberID=:MemberID AND PayMonth = :PayMonth ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':OverTimePay', $OverTimePay);
	$Stmt->bindParam(':PayMonth', $PayMonth);
			
	$Stmt->execute();
	$Stmt = null;
}
header("Location: overtimepay_confirm.php"); 
?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>

<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');



?>
</body>
</html>

