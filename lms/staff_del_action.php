<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$StaffID = isset($_REQUEST["StaffID"]) ? $_REQUEST["StaffID"] : "";

//Members 
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";


	
	
		$Sql = "DELETE FROM Staffs WHERE StaffID = :StaffID";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':StaffID', $StaffID);
		$Stmt->execute();
		$Stmt = null;

	
		//Members에 sttaffID 업데이트
		$Sql = "UPDATE Members set ";
			$Sql .= " StaffID = 0, ";
			$Sql .= " MemberModiDateTime = now() ";
		$Sql .= " where MemberID = :MemberID ";

		$Stmt = $DbConn->prepare($Sql);
		
		$Stmt->bindParam(':MemberID', $MemberID);
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
	header("Location: teacher_list.php?$ListParam"); 
	exit;
}
?>