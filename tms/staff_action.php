<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";

$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$MemberLoginPW = isset($_REQUEST["MemberLoginPW"]) ? $_REQUEST["MemberLoginPW"] : "";
$MemberLoginNewPW = isset($_REQUEST["MemberLoginNewPW"]) ? $_REQUEST["MemberLoginNewPW"] : "";

$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
$MemberNumber = isset($_REQUEST["MemberNumber"]) ? $_REQUEST["MemberNumber"] : "";
$MemberPositionName = isset($_REQUEST["MemberPositionName"]) ? $_REQUEST["MemberPositionName"] : "";
$MemberSex = isset($_REQUEST["MemberSex"]) ? $_REQUEST["MemberSex"] : "";
$MemberBirthday_1 = isset($_REQUEST["MemberBirthday_1"]) ? $_REQUEST["MemberBirthday_1"] : "";
$MemberBirthday_2 = isset($_REQUEST["MemberBirthday_2"]) ? $_REQUEST["MemberBirthday_2"] : "";
$MemberBirthday_3 = isset($_REQUEST["MemberBirthday_3"]) ? $_REQUEST["MemberBirthday_3"] : "";

$MemberPhone1_1 = isset($_REQUEST["MemberPhone1_1"]) ? $_REQUEST["MemberPhone1_1"] : "";
$MemberPhone1_2 = isset($_REQUEST["MemberPhone1_2"]) ? $_REQUEST["MemberPhone1_2"] : "";
$MemberPhone1_3 = isset($_REQUEST["MemberPhone1_3"]) ? $_REQUEST["MemberPhone1_3"] : "";
$MemberPhone2_1 = isset($_REQUEST["MemberPhone2_1"]) ? $_REQUEST["MemberPhone2_1"] : "";
$MemberPhone2_2 = isset($_REQUEST["MemberPhone2_2"]) ? $_REQUEST["MemberPhone2_2"] : "";
$MemberPhone2_3 = isset($_REQUEST["MemberPhone2_3"]) ? $_REQUEST["MemberPhone2_3"] : "";
$MemberPhone3_1 = isset($_REQUEST["MemberPhone3_1"]) ? $_REQUEST["MemberPhone3_1"] : "";
$MemberPhone3_2 = isset($_REQUEST["MemberPhone3_2"]) ? $_REQUEST["MemberPhone3_2"] : "";
$MemberPhone3_3 = isset($_REQUEST["MemberPhone3_3"]) ? $_REQUEST["MemberPhone3_3"] : "";
$MemberEmail_1 = isset($_REQUEST["MemberEmail_1"]) ? $_REQUEST["MemberEmail_1"] : "";
$MemberEmail_2 = isset($_REQUEST["MemberEmail_2"]) ? $_REQUEST["MemberEmail_2"] : "";
$MemberZipCode = isset($_REQUEST["MemberZipCode"]) ? $_REQUEST["MemberZipCode"] : "";
$MemberAddr1 = isset($_REQUEST["MemberAddr1"]) ? $_REQUEST["MemberAddr1"] : "";
$MemberAddr2 = isset($_REQUEST["MemberAddr2"]) ? $_REQUEST["MemberAddr2"] : "";
$MemberState = isset($_REQUEST["MemberState"]) ? $_REQUEST["MemberState"] : "";
$MemberView = isset($_REQUEST["MemberView"]) ? $_REQUEST["MemberView"] : "";

$MemberPhone1 = $MemberPhone1_1 ."-". $MemberPhone1_2 ."-". $MemberPhone1_3;
$MemberPhone2 = $MemberPhone2_1 ."-". $MemberPhone2_2 ."-". $MemberPhone2_3;
$MemberPhone3 = $MemberPhone3_1 ."-". $MemberPhone3_2 ."-". $MemberPhone3_3;
$MemberEmail = $MemberEmail_1 ."@". $MemberEmail_2;

$MemberBirthday = $MemberBirthday_1 ."-". $MemberBirthday_2 ."-". $MemberBirthday_3;


if ($MemberNumber==""){
	$MemberNumber = $MemberPhone1_3;
}

if ($MemberView!="1"){
	$MemberView = 0;
}

if ($MemberState!="1"){
	$MemberState = 2;
}


if ($MemberID==""){
	
	$MemberLevelID = 2;//직원

	$Sql = "select count(*) as TotalRowCount from Members where MemberLoginID=:MemberLoginID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TotalRowCount = $Row["TotalRowCount"];

	if ($TotalRowCount > 0){
		$err_num = 1;
		echo "<script>alert('Error : ID is a duplicate!!');history.go(-1);</script>";
	}else{
		$Sql = " insert into Members ( ";
			$Sql .= " MemberName, ";
			$Sql .= " MemberNumber, ";
			$Sql .= " MemberPositionName, ";
			$Sql .= " MemberSex, ";
			$Sql .= " MemberBirthday, ";
			$Sql .= " MemberLevelID, ";
			$Sql .= " MemberLoginID, ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " MemberLoginPW, ";
			}
			$Sql .= " MemberPhone1, ";
			$Sql .= " MemberPhone2, ";
			$Sql .= " MemberPhone3, ";
			$Sql .= " MemberEmail, ";
			$Sql .= " MemberZipCode, ";
			$Sql .= " MemberAddr1, ";
			$Sql .= " MemberAddr2, ";
			$Sql .= " MemberRegDateTime, ";
			$Sql .= " MemberModiDateTime, ";
			$Sql .= " MemberState, ";
			$Sql .= " MemberView ";
		$Sql .= " ) values ( ";
			$Sql .= " :MemberName, ";
			$Sql .= " :MemberNumber, ";
			$Sql .= " :MemberPositionName, ";
			$Sql .= " :MemberSex, ";
			$Sql .= " :MemberBirthday, ";
			$Sql .= " :MemberLevelID, ";
			$Sql .= " :MemberLoginID, ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " HEX(AES_ENCRYPT(:MemberLoginNewPW, MD5(:MemberLoginNewPW))), ";
			}
			$Sql .= " HEX(AES_ENCRYPT(:MemberPhone1, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:MemberPhone2, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:MemberPhone3, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
			$Sql .= " :MemberZipCode, ";
			$Sql .= " :MemberAddr1, ";
			$Sql .= " :MemberAddr2, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " :MemberState, ";
			$Sql .= " :MemberView ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberName', $MemberName);
		$Stmt->bindParam(':MemberNumber', $MemberNumber);
		$Stmt->bindParam(':MemberPositionName', $MemberPositionName);
		$Stmt->bindParam(':MemberSex', $MemberSex);
		$Stmt->bindParam(':MemberBirthday', $MemberBirthday);
		$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
		$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
		if ($MemberLoginNewPW!=""){
			$Stmt->bindParam(':MemberLoginNewPW', $MemberLoginNewPW);
		}
		$Stmt->bindParam(':MemberPhone1', $MemberPhone1);
		$Stmt->bindParam(':MemberPhone2', $MemberPhone2);
		$Stmt->bindParam(':MemberPhone3', $MemberPhone3);
		$Stmt->bindParam(':MemberEmail', $MemberEmail);
		$Stmt->bindParam(':MemberZipCode', $MemberZipCode);
		$Stmt->bindParam(':MemberAddr1', $MemberAddr1);
		$Stmt->bindParam(':MemberAddr2', $MemberAddr2);
		$Stmt->bindParam(':MemberState', $MemberState);
		$Stmt->bindParam(':MemberView', $MemberView);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$MemberID = $DbConn->lastInsertId();
		$Stmt = null;
	}

}else{

	$Sql = "select count(*) as TotalRowCount from Members where MemberLoginID=:MemberLoginID and MemberID<>:MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TotalRowCount = $Row["TotalRowCount"];

	if ($TotalRowCount > 0){
		$err_num = 1;
		echo "<script>alert('Error : ID is a duplicate!!');history.go(-1);</script>";
	}else{
		$Sql = " update Members set ";
			$Sql .= " MemberName = :MemberName, ";
			$Sql .= " MemberNumber = :MemberNumber, ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " MemberLoginPW = HEX(AES_ENCRYPT(:MemberLoginNewPW, MD5(:MemberLoginNewPW))), ";
			}
			$Sql .= " MemberPositionName = :MemberPositionName, ";
			$Sql .= " MemberSex = :MemberSex, ";
			$Sql .= " MemberPhone1 = HEX(AES_ENCRYPT(:MemberPhone1, :EncryptionKey)), ";
			$Sql .= " MemberPhone2 = HEX(AES_ENCRYPT(:MemberPhone2, :EncryptionKey)), ";
			$Sql .= " MemberPhone3 = HEX(AES_ENCRYPT(:MemberPhone3, :EncryptionKey)), ";
			$Sql .= " MemberEmail = HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
			$Sql .= " MemberZipCode = :MemberZipCode, ";
			$Sql .= " MemberAddr1 = :MemberAddr1, ";
			$Sql .= " MemberAddr2 = :MemberAddr2, ";
			$Sql .= " MemberState = :MemberState, ";
			$Sql .= " MemberView = :MemberView, ";
			$Sql .= " MemberModiDateTime = now() ";
		$Sql .= " where MemberID = :MemberID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberName', $MemberName);
		$Stmt->bindParam(':MemberNumber', $MemberNumber);
		if ($MemberLoginNewPW!=""){
			$Stmt->bindParam(':MemberLoginNewPW', $MemberLoginNewPW);
		}
		$Stmt->bindParam(':MemberPositionName', $MemberPositionName);
		$Stmt->bindParam(':MemberSex', $MemberSex);
		$Stmt->bindParam(':MemberPhone1', $MemberPhone1);
		$Stmt->bindParam(':MemberPhone2', $MemberPhone2);
		$Stmt->bindParam(':MemberPhone3', $MemberPhone3);
		$Stmt->bindParam(':MemberEmail', $MemberEmail);
		$Stmt->bindParam(':MemberZipCode', $MemberZipCode);
		$Stmt->bindParam(':MemberAddr1', $MemberAddr1);
		$Stmt->bindParam(':MemberAddr2', $MemberAddr2);
		$Stmt->bindParam(':MemberState', $MemberState);
		$Stmt->bindParam(':MemberView', $MemberView);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->execute();
		$Stmt = null;
	}
}


if ($err_num != 0){
	include_once('./inc_header.php');
?>
</head>
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
	header("Location: staff_list.php?$ListParam"); 
	exit;
}
?>


