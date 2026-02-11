<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$DocumentType = isset($_REQUEST["DocumentType"]) ? $_REQUEST["DocumentType"] : "";
$DocumentReportMemberID0 = isset($_REQUEST["DocumentReportMemberID0"]) ? $_REQUEST["DocumentReportMemberID0"] : "";
$DocumentReportMemberID1 = isset($_REQUEST["DocumentReportMemberID1"]) ? $_REQUEST["DocumentReportMemberID1"] : "";
$DocumentReportMemberID2 = isset($_REQUEST["DocumentReportMemberID2"]) ? $_REQUEST["DocumentReportMemberID2"] : "";

echo "losthero".$DocumentReportMemberID0;
echo "losthero".$DocumentReportMemberID1;
echo "losthero".$DocumentReportMemberID2;


if ($DocumentType != "" ){
	//먼저 해당 문서의 기존 결재라인을 삭제한다.
	$Sql = " DELETE FROM FixedApprovalLine WHERE  DocumentType = :DocumentType ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentType', $DocumentType);
	$Stmt->execute();

	//새로운 결재라인을 등록한다. 
	for($i=0;$i<3;$i++){
		if (${"DocumentReportMemberID".$i} != "") {
			$Sql = " INSERT into FixedApprovalLine ( ";
				$Sql .= " DocumentType, ";
				$Sql .= " ApprovalSequence, ";
				$Sql .= " MemberID ";
			$Sql .= " ) values ( ";
				$Sql .= " :DocumentType, ";
				$Sql .= " :ApprovalSequence, ";
				$Sql .= " :MemberID ";
			$Sql .= " ) ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':DocumentType', $DocumentType);
			$Stmt->bindParam(':ApprovalSequence', $i);
			$Stmt->bindParam(':MemberID', ${"DocumentReportMemberID".$i});
			$Stmt->execute();
			$Stmt = null;
		}	
	}	

}

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
	header("Location: approval_line_list.php"); 
	exit;
}

?>


