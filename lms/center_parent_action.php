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


// =================== 자녀 / 번호 =====================

$MemberChildID1 = isset($_REQUEST["MemberChildID1"]) ? $_REQUEST["MemberChildID1"] : "";
$MemberChildID2 = isset($_REQUEST["MemberChildID2"]) ? $_REQUEST["MemberChildID2"] : "";
$MemberChildID3 = isset($_REQUEST["MemberChildID3"]) ? $_REQUEST["MemberChildID3"] : "";

// =================== 자녀 이름 / 번호 ===================== //

if ($MemberView!="1"){
	$MemberView = 0; 
}

if ($MemberState!="1"){
	$MemberState = 2;
}

if($MemberChildID2=="") {
	$MemberChildID2 = null;
}

if($MemberChildID3=="") {
	$MemberChildID3 = null;
}


if ($MemberID=="") {

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
		//Members 
		$MemberLevelID = 18;// 학부모

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
		$MemberID = $DbConn->lastInsertId();
		$Stmt = null;

		// 3번 반복하면서 자녀 테이블에 값을 넣는다.
		for($i=0; $i<3;$i++) {
			if($i==0) {
				$TempMemberChildID = $MemberChildID1;
			} else if($i==1) {
				$TempMemberChildID = $MemberChildID2;
			} else if($i==2) {
				$TempMemberChildID = $MemberChildID3;
			}

			$Sql2 = " insert into MemberChilds ( ";
				$Sql2 .= " MemberID, ";
				$Sql2 .= " MemberChildID, ";
				$Sql2 .= " MemberChildRegDateTime, ";
				$Sql2 .= " MemberChildView, ";
				$Sql2 .= " MemberChildState ";
			$Sql2 .= " ) values ( ";
				$Sql2 .= " :MemberID, ";
				$Sql2 .= " :MemberChildID, ";
				$Sql2 .= " now(), ";
				$Sql2 .= " 1, ";
				$Sql2 .= " 1 ";
			$Sql2 .= " ) ";

			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':MemberID', $MemberID);
			$Stmt2->bindParam(':MemberChildID', $TempMemberChildID);
			$Stmt2->execute();
			$Stmt2 = null;
		}
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


		$Sql2 = " DELETE FROM MemberChilds WHERE MemberID=:MemberID";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':MemberID', $MemberID);
		$Stmt2->execute();
		$Stmt2 = null;

		// 3번 반복하면서 자녀 테이블에 값을 넣는다.
		for($i=0; $i<3;$i++) {
			if($i==0) {
				$TempMemberChildID = $MemberChildID1;
			} else if($i==1) {
				$TempMemberChildID = $MemberChildID2;
			} else if($i==2) {
				$TempMemberChildID = $MemberChildID3;
			}

			$Sql2 = " insert into MemberChilds ( ";
				$Sql2 .= " MemberID, ";
				$Sql2 .= " MemberChildID, ";
				$Sql2 .= " MemberChildRegDateTime, ";
				$Sql2 .= " MemberChildView, ";
				$Sql2 .= " MemberChildState ";
			$Sql2 .= " ) values ( ";
				$Sql2 .= " :MemberID, ";
				$Sql2 .= " :MemberChildID, ";
				$Sql2 .= " now(), ";
				$Sql2 .= " 1, ";
				$Sql2 .= " 1 ";
			$Sql2 .= " ) ";

			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':MemberID', $MemberID);
			$Stmt2->bindParam(':MemberChildID', $TempMemberChildID);
			$Stmt2->execute();
			$Stmt2 = null;
		}
	}
}


// 자녀 등록



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
parent.location.href = "center_form.php?<?=$ListParam?>&CenterID=<?=$CenterID?>&PageTabID=6";

</script>
<?
}

?>

