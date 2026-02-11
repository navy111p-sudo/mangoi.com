<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$PayMonth = isset($_REQUEST["PayMonth"]) ? $_REQUEST["PayMonth"] : "";

$YearMonth = explode("-",$PayMonth);

// 먼저 Pay 테이블에 해당 PayMonth 의 값을 전부 지운다.
$Sql = "DELETE from Pay WHERE PayMonth = '$PayMonth'";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();

// PayApprovalMembers에서 결재 정보를 삭제한다.
$Sql = "DELETE from PayApprovalMembers WHERE PayMonthStateID = (SELECT PayMonthStateID FROM PayMonthState WHERE PayMonth = '$PayMonth')";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
	
// PayMonthStat 테이블에서 해당 PayMonth 를 삭제한다.
$Sql = "DELETE from PayMonthState WHERE PayMonth = '$PayMonth'";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();


if ($err_num != 0){
	include_once('./inc_header.php');
?>
<body>
<script>
	//alert("<?=$err_msg?>");
	//history.go(-1);
</script>
<?php
	include_once('./inc_footer.php'); 
?>
</body>
</html>
<?
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: pay.php?$ListParam"); 
	exit;
}
?>