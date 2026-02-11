<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";

$SelectYear = date("Y");
$SelectMonth = date("m");
$SelectDay = date("d");
$SelectDate = $SelectYear."-".$SelectMonth."-".$SelectDay;


$Sql = " select 
				A.TeacherStartHour  
			from Teachers A
			where A.TeacherID=:TeacherID
				";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherID', $TeacherID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$TeacherStartHour = $Row["TeacherStartHour"];



$Sql = " select count(*) as TotalRowCount from TeacherAttendances A where A.TeacherID=:TeacherID and A.CheckDate=:CheckDate ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherID', $TeacherID);
$Stmt->bindParam(':CheckDate', $SelectDate);
$Stmt->execute();
$Row = $Stmt->fetch();
$TotalRowCount = $Row["TotalRowCount"];
$Stmt = null;

if($TotalRowCount>0) { // 값이 있다면
	// 값을 업데이트한다
	$Sql2 = "update TeacherAttendances set TeacherAttendanceDateTime=now() where TeacherID=:TeacherID and CheckDate=:CheckDate ";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':TeacherID', $TeacherID);
	$Stmt2->bindParam(':CheckDate', $SelectDate);
	$Stmt2->execute();
	$Stmt2 = null;

} else { // 값이 없다면
	// insert 한다

	$TeacherAttendanceHour = $SelectDate ." ".  substr("0".$TeacherStartHour, -2).":00:00";

	$Sql2 = " insert into TeacherAttendances ( ";
		$Sql2 .= " TeacherID, ";
		$Sql2 .= " CheckDate, ";
		$Sql2 .= " TeacherAttendanceHour, ";
		$Sql2 .= " TeacherAttendanceDateTime ";
	$Sql2 .= " ) values ( ";
		$Sql2 .= " :TeacherID, ";
		$Sql2 .= " :CheckDate, ";
		$Sql2 .= " :TeacherAttendanceHour, ";
		$Sql2 .= " now() ";
	$Sql2 .= " ) ";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':TeacherID', $TeacherID);
	$Stmt2->bindParam(':CheckDate', $SelectDate);
	$Stmt2->bindParam(':TeacherAttendanceHour', $TeacherAttendanceHour);
	$Stmt2->execute();
	$Stmt2 = null;
}

?>

<body>
<script>
	parent.location.href = "index.php";
</script>
</body>
</html>

<?
include_once('../includes/dbclose.php');
?>
