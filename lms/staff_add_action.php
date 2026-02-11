<?php
  error_reporting( E_ALL );
  ini_set( "display_errors", 1 );
?>
<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$FranchiseID = isset($_REQUEST["FranchiseID"]) ? $_REQUEST["FranchiseID"] : "";
$StaffID = isset($_REQUEST["StaffID"]) ? $_REQUEST["StaffID"] : "";
$StaffName = isset($_REQUEST["StaffName"]) ? $_REQUEST["StaffName"] : "";
$StaffNickName = isset($_REQUEST["StaffNickName"]) ? $_REQUEST["StaffNickName"] : "";
$StaffManageMent = isset($_REQUEST["StaffManageMent"]) ? $_REQUEST["StaffManageMent"] : 0;

$StaffPhone1 = isset($_REQUEST["StaffPhone1"]) ? $_REQUEST["StaffPhone1"] : "";
$StaffPhone2 = isset($_REQUEST["StaffPhone2"]) ? $_REQUEST["StaffPhone2"] : "";
$StaffPhone3 = "";

$StaffZip = isset($_REQUEST["StaffZip"]) ? $_REQUEST["StaffZip"] : "";
$StaffAddr1 = isset($_REQUEST["StaffAddr1"]) ? $_REQUEST["StaffAddr1"] : "";
$StaffAddr2 = isset($_REQUEST["StaffAddr2"]) ? $_REQUEST["StaffAddr2"] : "";
$StaffLogoImage = isset($_REQUEST["StaffLogoImage"]) ? $_REQUEST["StaffLogoImage"] : "";
$StaffIntroText = isset($_REQUEST["StaffIntroText"]) ? $_REQUEST["StaffIntroText"] : "";
$StaffRegDateTime = isset($_REQUEST["StaffRegDateTime"]) ? $_REQUEST["StaffRegDateTime"] : "";
$StaffState = isset($_REQUEST["StaffState"]) ? $_REQUEST["StaffState"] : "";
$StaffView = isset($_REQUEST["StaffView"]) ? $_REQUEST["StaffView"] : "";

$StaffEmail = "";

//Members 
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";


	
	
		$Sql = "SELECT ifnull(Max(StaffOrder),0) as StaffOrder from Staffs";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$StaffOrder = $Row["StaffOrder"]+1;

		$Sql = " INSERT into Staffs ( ";
			$Sql .= " FranchiseID, ";
			$Sql .= " StaffManageMent, ";
			$Sql .= " StaffName, ";
			$Sql .= " StaffNickName, ";
			$Sql .= " StaffPhone1, ";
			$Sql .= " StaffPhone2, ";
			$Sql .= " StaffPhone3, ";
			$Sql .= " StaffEmail, ";
			$Sql .= " StaffZip, ";
			$Sql .= " StaffAddr1, ";
			$Sql .= " StaffAddr2, ";
			$Sql .= " StaffLogoImage, ";
			$Sql .= " StaffIntroText, ";
			$Sql .= " StaffRegDateTime, ";
			$Sql .= " StaffModiDateTime, ";
			$Sql .= " StaffState, ";
			$Sql .= " StaffView, ";
			$Sql .= " StaffOrder ";
		$Sql .= " ) VALUES ( ";
			$Sql .= " :FranchiseID, ";
			$Sql .= " :StaffManageMent, ";
			$Sql .= " :StaffName, ";
			$Sql .= " :StaffNickName, ";
			$Sql .= " HEX(AES_ENCRYPT(:StaffPhone1, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:StaffPhone2, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:StaffPhone3, :EncryptionKey)), ";;
			$Sql .= " HEX(AES_ENCRYPT(:StaffEmail, :EncryptionKey)), ";
			$Sql .= " :StaffZip, ";
			$Sql .= " :StaffAddr1, ";
			$Sql .= " :StaffAddr2, ";
			$Sql .= " :StaffLogoImage, ";
			$Sql .= " :StaffIntroText, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " 1, ";
			$Sql .= " 1, ";
			$Sql .= " :StaffOrder ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':FranchiseID', $FranchiseID);
		$Stmt->bindParam(':StaffManageMent', $StaffManageMent);
		$Stmt->bindParam(':StaffName', $StaffName);
		$Stmt->bindParam(':StaffNickName', $StaffNickName);
		$Stmt->bindParam(':StaffPhone1', $StaffPhone1);
		$Stmt->bindParam(':StaffPhone2', $StaffPhone2);
		$Stmt->bindParam(':StaffPhone3', $StaffPhone3);
		$Stmt->bindParam(':StaffEmail', $StaffEmail);
		$Stmt->bindParam(':StaffZip', $StaffZip);
		$Stmt->bindParam(':StaffAddr1', $StaffAddr1);
		$Stmt->bindParam(':StaffAddr2', $StaffAddr2);
		$Stmt->bindParam(':StaffLogoImage', $StaffLogoImage);
		$Stmt->bindParam(':StaffIntroText', $StaffIntroText);
		$Stmt->bindParam(':StaffOrder', $StaffOrder);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$StaffID = $DbConn->lastInsertId();
		$Stmt = null;


	
	
		//Members에 sttaffID 업데이트
		$Sql = "UPDATE Members set ";
			$Sql .= " StaffID = :StaffID, ";
			$Sql .= " MemberModiDateTime = now() ";
		$Sql .= " where MemberID = :MemberID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':StaffID', $StaffID);
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