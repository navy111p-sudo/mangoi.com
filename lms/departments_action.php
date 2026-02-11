<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";

$DepartmentID = isset($_REQUEST["DepartmentID"]) ? $_REQUEST["DepartmentID"] : "";
$DepartmentName = isset($_REQUEST["DepartmentName"]) ? $_REQUEST["DepartmentName"] : "";
$DepartmentNameEng = isset($_REQUEST["DepartmentNameEng"]) ? $_REQUEST["DepartmentNameEng"] : "";
$InUse = isset($_REQUEST["InUse"]) ? $_REQUEST["InUse"] : "";


if ($DepartmentID==""){

	$Sql = " INSERT into Departments ( ";
		$Sql .= " DepartmentName, ";
		$Sql .= " DepartmentNameEng, ";
		$Sql .= " InUse, ";
		$Sql .= " regDate ";

	$Sql .= " ) values ( ";
		$Sql .= " :DepartmentName, ";
		$Sql .= " :DepartmentNameEng, ";
		$Sql .= " :InUse, ";
		$Sql .= " now() ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DepartmentName', $DepartmentName);
	$Stmt->bindParam(':DepartmentNameEng', $DepartmentNameEng);
	$Stmt->bindParam(':InUse', $InUse);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " UPDATE Departments set ";
		$Sql .= " DepartmentName = :DepartmentName, ";
		$Sql .= " DepartmentNameEng = :DepartmentNameEng, ";
		$Sql .= " InUse = :InUse ";
	$Sql .= " where DepartmentID = :DepartmentID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DepartmentID', $DepartmentID);
	$Stmt->bindParam(':DepartmentName', $DepartmentName);
	$Stmt->bindParam(':DepartmentNameEng', $DepartmentNameEng);
	$Stmt->bindParam(':InUse', $InUse);
	$Stmt->execute();
	$Stmt = null;

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
	header("Location: departments_list.php?$ListParam"); 
	exit;
}
?>


