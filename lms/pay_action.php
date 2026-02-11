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

$Sql = "DELETE from PayApprovalMembers where PayMonthStateID=:PayMonthStateID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':PayMonthStateID', $PayMonthStateID);
$Stmt->execute();
$Stmt = null;


// 결재라인을 PayApprovalMembers 테이블에 추가한다.
for ($ii=1; $ii<=4; $ii++){
	$DocumentReportMemberID = isset($_REQUEST["DocumentReportMemberID".$ii]) ? $_REQUEST["DocumentReportMemberID".$ii] : "";
	if ($DocumentReportMemberID!="0" && $DocumentReportMemberID!=NULL && $DocumentReportMemberID!="직원 선택"){
		$ApprovalMemberOrder = $ii;
		$Sql = "INSERT into PayApprovalMembers ( ";
			$Sql .= " PayMonthStateID, ";
			$Sql .= " MemberID, ";
			$Sql .= " ApprovalMemberOrder, ";
			$Sql .= " ApprovalRegDateTime, ";
			$Sql .= " ApprovalModiDateTime, ";
			$Sql .= " ApprovalState ";
		$Sql .= " ) values ( ";
			$Sql .= " :PayMonthStateID, ";
			$Sql .= " :MemberID, ";
			$Sql .= " :ApprovalMemberOrder, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " 0 ";
		$Sql .= " ) ";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':PayMonthStateID', $PayMonthStateID);
		$Stmt->bindParam(':ApprovalMemberOrder', $ApprovalMemberOrder);
		$Stmt->bindParam(':MemberID', $DocumentReportMemberID);
		$Stmt->execute();
		$Stmt = null;
	}
}

// 결재 귀속년월별 상태 table의 상태를 결재중(1) 로 변경한다.
$Sql = "UPDATE PayMonthState 
			SET PayState = 1 
			where PayMonthStateID=:PayMonthStateID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(":PayMonthStateID", $PayMonthStateID);
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