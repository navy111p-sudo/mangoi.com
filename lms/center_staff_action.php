<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('../includes/password_hash.php');


$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
$MemberNickName = isset($_REQUEST["MemberNickName"]) ? $_REQUEST["MemberNickName"] : "";
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$MemberPhone1 = isset($_REQUEST["MemberPhone1"]) ? $_REQUEST["MemberPhone1"] : "";
$MemberEmail = isset($_REQUEST["MemberEmail"]) ? $_REQUEST["MemberEmail"] : "";
$MemberState = isset($_REQUEST["MemberState"]) ? $_REQUEST["MemberState"] : "";
$MemberView = isset($_REQUEST["MemberView"]) ? $_REQUEST["MemberView"] : "";
$MemberLoginNewPW = isset($_REQUEST["MemberLoginNewPW"]) ? $_REQUEST["MemberLoginNewPW"] : "";

//================ 전화번호 / 이메일 =============
$MemberPhone1_1 = isset($_REQUEST["MemberPhone1_1"]) ? $_REQUEST["MemberPhone1_1"] : "";
$MemberPhone1_2 = isset($_REQUEST["MemberPhone1_2"]) ? $_REQUEST["MemberPhone1_2"] : "";
$MemberPhone1_3 = isset($_REQUEST["MemberPhone1_3"]) ? $_REQUEST["MemberPhone1_3"] : "";

$MemberEmail_1 = isset($_REQUEST["MemberEmail_1"]) ? $_REQUEST["MemberEmail_1"] : "";
$MemberEmail_2 = isset($_REQUEST["MemberEmail_2"]) ? $_REQUEST["MemberEmail_2"] : "";

$MemberPhone1 = $MemberPhone1_1 . "-". $MemberPhone1_2 . "-" .$MemberPhone1_3;
$MemberEmail = $MemberEmail_1 . "@". $MemberEmail_2;


$MemberLoginNewPW_hash = password_hash(sha1($MemberLoginNewPW), PASSWORD_DEFAULT);


if ($MemberView!="1"){
	$MemberView = 0; 
}

if ($MemberState!="1"){
	$MemberState = 2;
}

if ($MemberID==""){

	//Members 
	$MemberLevelID = 13;//센터직원(대리점직원)

	$Sql = " insert into Members ( ";
		$Sql .= " CenterID, ";
		$Sql .= " MemberLevelID, ";
		$Sql .= " MemberLoginID, ";
		if ($MemberLoginNewPW!=""){
			$Sql .= " MemberLoginPW, ";
		}
		$Sql .= " MemberName, ";
		$Sql .= " MemberNickName, ";
		$Sql .= " MemberPhone1, ";
		$Sql .= " MemberEmail, ";
		$Sql .= " MemberView, ";
		$Sql .= " MemberState, ";
		$Sql .= " MemberRegDateTime, ";
		$Sql .= " MemberModiDateTime ";

	$Sql .= " ) values ( ";

		$Sql .= " :CenterID, ";
		$Sql .= " :MemberLevelID, ";
		$Sql .= " :MemberLoginID, ";
		if ($MemberLoginNewPW!=""){
			$Sql .= " :MemberLoginNewPW_hash, ";
		}
		$Sql .= " :MemberName, ";
		$Sql .= " :MemberNickName, ";
		$Sql .= " HEX(AES_ENCRYPT(:MemberPhone1, :EncryptionKey)), ";
		$Sql .= " HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
		$Sql .= " :MemberView, ";
		$Sql .= " :MemberState, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";

	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
	$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
	if ($MemberLoginNewPW!=""){
		$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
	}
	$Stmt->bindParam(':MemberName', $MemberName);
	$Stmt->bindParam(':MemberNickName', $MemberNickName);
	$Stmt->bindParam(':MemberPhone1', $MemberPhone1);
	$Stmt->bindParam(':MemberEmail', $MemberEmail);
	$Stmt->bindParam(':MemberView', $MemberView);
	$Stmt->bindParam(':MemberState', $MemberState);

	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt = null;

}else{

	//Members 
	$Sql = " update Members set ";
		if ($MemberLoginNewPW!=""){
			$Sql .= " MemberLoginPW = :MemberLoginNewPW_hash, ";
		}
		$Sql .= " MemberName = :MemberName, ";
		$Sql .= " MemberNickName = :MemberNickName, ";
		$Sql .= " MemberPhone1 = HEX(AES_ENCRYPT(:MemberPhone1, :EncryptionKey)), ";
		$Sql .= " MemberEmail = HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
		$Sql .= " MemberView = :MemberView, ";
		$Sql .= " MemberState = :MemberState, ";
		$Sql .= " MemberModiDateTime = now() ";
	$Sql .= " where MemberID = :MemberID ";

	$Stmt = $DbConn->prepare($Sql);
	if ($MemberLoginNewPW!=""){
		$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
	}
	$Stmt->bindParam(':MemberName', $MemberName);
	$Stmt->bindParam(':MemberNickName', $MemberNickName);
	$Stmt->bindParam(':MemberPhone1', $MemberPhone1);
	$Stmt->bindParam(':MemberEmail', $MemberEmail);
	$Stmt->bindParam(':MemberView', $MemberView);
	$Stmt->bindParam(':MemberState', $MemberState);

	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->execute();
	$Stmt = null;
}



if ($err_num != 0){
	include_once('./inc_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
//history.go(-1);
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
//parent.$.fn.colorbox.close();
parent.location.href = "center_form.php?<?=$ListParam?>&CenterID=<?=$CenterID?>&PageTabID=5";

</script>
<?
}

?>

