<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$PayMonthStateID = isset($_REQUEST["PayMonthStateID"]) ? $_REQUEST["PayMonthStateID"] : "";
$PayMonth = isset($_REQUEST["PayMonth"]) ? $_REQUEST["PayMonth"] : "";

// 결재 귀속년월별 상태 table의 상태를 지급완료(3) 로 변경한다.
$Sql = "UPDATE PayMonthState 
			SET PayState = 3 
			where PayMonthStateID=:PayMonthStateID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(":PayMonthStateID", $PayMonthStateID);
$Stmt->execute();
$Stmt = null;

// Pay 테이블에 GivePayDate 에 현재 날짜를 입력한다.
$Sql = "UPDATE Pay 
			SET GivePayDate = now()  
			where PayMonth = :PayMonth";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(":PayMonth", $PayMonth);
$Stmt->execute();
$Stmt = null;

if ($err_num != 0){
	include_once('./inc_header.php');
?>
<body>
<script>
	alert("<?=$err_msg?>");
	history.go(-1);
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