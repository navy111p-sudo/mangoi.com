<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$TaxInfoID = isset($_REQUEST["TaxInfoID"]) ? $_REQUEST["TaxInfoID"] : "";
$TaxInfoItemTitle = isset($_REQUEST["TaxInfoItemTitle"]) ? $_REQUEST["TaxInfoItemTitle"] : "";


if ($TaxInfoItemTitle!=""){

	// 현재 비어 있는 항목을 확인하고 비어 있는 항목에다가 신규 항목을 추가해 준다.

	$Sql = "SELECT * from PayTaxInfo WHERE TaxInfoID = :TaxInfoID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TaxInfoID', $TaxInfoID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$Add1Name = $Row["Add1Name"];
	$Add2Name = $Row["Add2Name"];
	$Add3Name = $Row["Add3Name"];
	$Add4Name = $Row["Add4Name"];
	$Add5Name = $Row["Add5Name"];
	$Add6Name = $Row["Add6Name"];
	$Add7Name = $Row["Add7Name"];

	for ($i=1;$i<=7;$i++){
		
		//항목이 비어있는지 확인해 보고 해당 항목에 업데이트시켜주고 루프를 끝낸다.
		if (${"Add".$i."Name"} == "") {
			$Sql = "UPDATE PayTaxInfo SET Add".$i."Name = :TaxInfoItemTitle, Add".$i." = 1 WHERE TaxInfoID = :TaxInfoID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':TaxInfoID', $TaxInfoID);
			$Stmt->bindParam(':TaxInfoItemTitle', $TaxInfoItemTitle);
			
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


