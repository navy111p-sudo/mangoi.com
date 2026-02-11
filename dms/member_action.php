<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);
$NewData = isset($_REQUEST["NewData"]) ? $_REQUEST["NewData"] : "";

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberLoginPW = isset($_REQUEST["MemberLoginPW"]) ? $_REQUEST["MemberLoginPW"] : "";
$MemberLevelID = isset($_REQUEST["MemberLevelID"]) ? $_REQUEST["MemberLevelID"] : "";
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$NewMemberLoginPW1 = isset($_REQUEST["NewMemberLoginPW1"]) ? $_REQUEST["NewMemberLoginPW1"] : "";
$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
$MemberNickName = isset($_REQUEST["MemberNickName"]) ? $_REQUEST["MemberNickName"] : "";
$MemberBirthday = isset($_REQUEST["MemberBirthday"]) ? $_REQUEST["MemberBirthday"] : "";
$MemberPhone1 = isset($_REQUEST["MemberPhone1"]) ? $_REQUEST["MemberPhone1"] : "";
$MemberPhone2 = isset($_REQUEST["MemberPhone2"]) ? $_REQUEST["MemberPhone2"] : "";
$MemberPhone3 = isset($_REQUEST["MemberPhone3"]) ? $_REQUEST["MemberPhone3"] : "";
$MemberZip = isset($_REQUEST["MemberZip"]) ? $_REQUEST["MemberZip"] : "";
$MemberAddr = isset($_REQUEST["MemberAddr"]) ? $_REQUEST["MemberAddr"] : "";
$MemberAddrDetail = isset($_REQUEST["MemberAddrDetail"]) ? $_REQUEST["MemberAddrDetail"] : "";
$MemberOldAddr1 = isset($_REQUEST["MemberOldAddr1"]) ? $_REQUEST["MemberOldAddr1"] : "";
$MemberOldAddr2 = isset($_REQUEST["MemberOldAddr2"]) ? $_REQUEST["MemberOldAddr2"] : "";
$MemberState = isset($_REQUEST["MemberState"]) ? $_REQUEST["MemberState"] : "";


$MemberLoginID = trim($MemberLoginID);

if (trim($NewMemberLoginPW1)!=""){
	$MemberLoginPW = md5($NewMemberLoginPW1);
}


if ($NewData=="1"){

	$Sql = " insert into Members ( ";
		$Sql .= " MemberLoginID, ";
		$Sql .= " MemberLoginPW, ";
		$Sql .= " MemberLevelID, ";
		$Sql .= " MemberName, ";
		$Sql .= " MemberNickName, ";
		$Sql .= " MemberBirthday, ";
		$Sql .= " MemberPhone1, ";
		$Sql .= " MemberPhone2, ";
		$Sql .= " MemberPhone3, ";
		$Sql .= " MemberZip, ";
		$Sql .= " MemberAddr, ";
		$Sql .= " MemberAddrDetail, ";
		$Sql .= " MemberOldAddr1, ";
		$Sql .= " MemberOldAddr2, ";
		$Sql .= " MemberState, ";
		$Sql .= " MemberRegDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :MemberLoginID, ";
		$Sql .= " :MemberLoginPW, ";
		$Sql .= " :MemberLevelID, ";
		$Sql .= " :MemberName, ";
		$Sql .= " :MemberNickName, ";
		$Sql .= " :MemberBirthday, ";
		$Sql .= " :MemberPhone1, ";
		$Sql .= " :MemberPhone2, ";
		$Sql .= " :MemberPhone3, ";
		$Sql .= " :MemberZip, ";
		$Sql .= " :MemberAddr, ";
		$Sql .= " :MemberAddrDetail, ";
		$Sql .= " :MemberOldAddr1, ";
		$Sql .= " :MemberOldAddr2, ";
		$Sql .= " :MemberState, ";
		$Sql .= " now() ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
	$Stmt->bindParam(':MemberLoginPW', $MemberLoginPW);
	$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
	$Stmt->bindParam(':MemberName', $MemberName);
	$Stmt->bindParam(':MemberNickName', $MemberNickName);
	$Stmt->bindParam(':MemberBirthday', $MemberBirthday);
	$Stmt->bindParam(':MemberPhone1', $MemberPhone1);
	$Stmt->bindParam(':MemberPhone2', $MemberPhone2);
	$Stmt->bindParam(':MemberPhone3', $MemberPhone3);
	$Stmt->bindParam(':MemberZip', $MemberZip);
	$Stmt->bindParam(':MemberAddr', $MemberAddr);
	$Stmt->bindParam(':MemberAddrDetail', $MemberAddrDetail);
	$Stmt->bindParam(':MemberOldAddr1', $MemberOldAddr1);
	$Stmt->bindParam(':MemberOldAddr2', $MemberOldAddr2);
	$Stmt->bindParam(':MemberState', $MemberState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update Members set ";
		$Sql .= " MemberLoginPW = :MemberLoginPW, ";
		$Sql .= " MemberLevelID = :MemberLevelID, ";
		$Sql .= " MemberName = :MemberName, ";
		$Sql .= " MemberNickName = :MemberNickName, ";
		$Sql .= " MemberBirthday = :MemberBirthday, ";
		$Sql .= " MemberPhone1 = :MemberPhone1, ";
		$Sql .= " MemberPhone2 = :MemberPhone2, ";
		$Sql .= " MemberPhone3 = :MemberPhone3, ";
		$Sql .= " MemberZip = :MemberZip, ";
		$Sql .= " MemberAddr = :MemberAddr, ";
		$Sql .= " MemberAddrDetail = :MemberAddrDetail, ";
		$Sql .= " MemberOldAddr1 = :MemberOldAddr1, ";
		$Sql .= " MemberOldAddr2 = :MemberOldAddr2, ";
		$Sql .= " MemberState = :MemberState ";
	$Sql .= " where MemberID=$MemberID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberLoginPW', $MemberLoginPW);
	$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
	$Stmt->bindParam(':MemberName', $MemberName);
	$Stmt->bindParam(':MemberNickName', $MemberNickName);
	$Stmt->bindParam(':MemberBirthday', $MemberBirthday);
	$Stmt->bindParam(':MemberPhone1', $MemberPhone1);
	$Stmt->bindParam(':MemberPhone2', $MemberPhone2);
	$Stmt->bindParam(':MemberPhone3', $MemberPhone3);
	$Stmt->bindParam(':MemberZip', $MemberZip);
	$Stmt->bindParam(':MemberAddr', $MemberAddr);
	$Stmt->bindParam(':MemberAddrDetail', $MemberAddrDetail);
	$Stmt->bindParam(':MemberOldAddr1', $MemberOldAddr1);
	$Stmt->bindParam(':MemberOldAddr2', $MemberOldAddr2);
	$Stmt->bindParam(':MemberState', $MemberState);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->execute();
	$Stmt = null;
}


if ($err_num != 0){
	include_once('./_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
</body>
<?php
	include_once('./_footer.php');
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: member_list.php?$ListParam"); 
	exit;
}
?>


