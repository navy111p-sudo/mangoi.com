<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;


$DeductionInfoID = isset($_REQUEST["DeductionInfoID"]) ? $_REQUEST["DeductionInfoID"] : "";
$DeductionInfoItemTitle = isset($_REQUEST["DeductionInfoItemTitle"]) ? $_REQUEST["DeductionInfoItemTitle"] : "";

if ($DeductionInfoItemTitle!=""){

	// 현재 비어 있는 항목을 확인하고 비어 있는 항목에다가 신규 항목을 추가해 준다.

	$Sql = "SELECT * from PayDeductionInfo WHERE DeductionInfoID = :DeductionInfoID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DeductionInfoID', $DeductionInfoID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$Add1Name = $Row["Add1Name"];
	$Add2Name = $Row["Add2Name"];
	$Add3Name = $Row["Add3Name"];
	$Add4Name = $Row["Add4Name"];

	for ($i=1;$i<=4;$i++){
		
		//항목이 비어있는지 확인해 보고 해당 항목에 업데이트시켜주고 루프를 끝낸다.
		if (${"Add".$i."Name"} == "") {
			$Sql = "UPDATE PayDeductionInfo SET Add".$i."Name = :DeductionInfoItemTitle, Add".$i." = 1 WHERE DeductionInfoID = :DeductionInfoID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':DeductionInfoID', $DeductionInfoID);
			$Stmt->bindParam(':DeductionInfoItemTitle', $DeductionInfoItemTitle);
			
			$Stmt->execute();
			$Stmt = null;
			break;
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
?>
<script>
parent.$.fn.colorbox.close();
parent.location.reload();
</script>
<?
}
?>


