<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$UpPath = isset($_REQUEST["UpPath"]) ? $_REQUEST["UpPath"] : "";
$MyFileName = isset($_REQUEST["MyFileName"]) ? $_REQUEST["MyFileName"] : "";

$ErrNum = 0;
$ErrMsg = "";
$UpPath = "../uploads/csv_add_student/";
$row = 1; 
$handle = fopen($UpPath.$MyFileName, "r"); 

$Count = 1;
while (($data = fgetcsv($handle, 1000, ",")) !== false) { 
	$num = count($data); 
	$row++; 

	if($row>7) {


		if (mb_detect_encoding($data[0], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[0] = iconv("EUC-KR", "UTF-8", $data[0]);
		}
		if (mb_detect_encoding($data[1], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[1] = iconv("EUC-KR", "UTF-8", $data[1]);
		}
		if (mb_detect_encoding($data[2], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[2] = iconv("EUC-KR", "UTF-8", $data[2]);
		}
		if (mb_detect_encoding($data[3], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[3] = iconv("EUC-KR", "UTF-8", $data[3]);
		}
		if (mb_detect_encoding($data[4], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[4] = iconv("EUC-KR", "UTF-8", $data[4]);
		}
		if (mb_detect_encoding($data[5], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[5] = iconv("EUC-KR", "UTF-8", $data[5]);
		}
		if (mb_detect_encoding($data[6], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[6] = iconv("EUC-KR", "UTF-8", $data[6]);
		}
		if (mb_detect_encoding($data[7], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[7] = iconv("EUC-KR", "UTF-8", $data[7]);
		}
		if (mb_detect_encoding($data[8], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[8] = iconv("EUC-KR", "UTF-8", $data[8]);
		}
		if (mb_detect_encoding($data[9], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[9] = iconv("EUC-KR", "UTF-8", $data[9]);
		}
		if (mb_detect_encoding($data[10], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[10] = iconv("EUC-KR", "UTF-8", $data[10]);
		}
		if (mb_detect_encoding($data[11], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[11] = iconv("EUC-KR", "UTF-8", $data[11]);
		}
		if (mb_detect_encoding($data[12], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[12] = iconv("EUC-KR", "UTF-8", $data[12]);
		}
		if (mb_detect_encoding($data[13], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[13] = iconv("EUC-KR", "UTF-8", $data[13]);
		}
		if (mb_detect_encoding($data[14], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$data[14] = iconv("EUC-KR", "UTF-8", $data[14]);
		}


		$MemberName = $data[0];
		$MemberNickName = $data[1];
		$MemberLoginID = $data[2];
		$MemberLoginPW = $data[3];
		$MemberPhone1 = $data[4];
		$MemberEmail = $data[5];
		$MemberSex = $data[6];
		$MemberBirthday = $data[7];
		$MemberZip = $data[8];
		$MemberAddr1 = $data[9];
		$MemberAddr2 = $data[10];
		$MemberStateText = $data[11];
		$MemberParentName = $data[12];
		$MemberPhone2 = $data[13];
		$MemberEmail2 = $data[14];

		if($MemberSex=="남자") {
			$StrMemberSex = 1;
		} else {
			$StrMemberSex = 2;
		}

		$MemberLoginPW_hash = password_hash(sha1($MemberLoginPW), PASSWORD_DEFAULT);

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
			$Sql = "insert into Members (
						CenterID,
						MemberLoginID,
						MemberLoginPW,
						MemberName,
						MemberNickName,
						MemberParentName,
						MemberSex,
						MemberBirthday,
						MemberPhone1,
						MemberPhone2,
						MemberEmail,
						MemberEmail2,
						MemberZip,
						MemberAddr1,
						MemberAddr2,
						MemberView,
						MemberState,
						MemberStateText,
						MemberRegDateTime,
						MemberModiDateTime
					) values (
						:CenterID,
						:MemberLoginID,
						:MemberLoginPW,
						:MemberName,
						:MemberNickName,
						:MemberParentName,
						:MemberSex,
						:MemberBirthday,
						HEX(AES_ENCRYPT(:MemberPhone1, :EncryptionKey)), 
						HEX(AES_ENCRYPT(:MemberPhone2, :EncryptionKey)), 
						HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)),
						HEX(AES_ENCRYPT(:MemberEmail2, :EncryptionKey)),
						:MemberZip,
						:MemberAddr1,
						:MemberAddr2,
						1,
						1,
						:MemberStateText,
						now(), 
						now()
						)";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':CenterID', $CenterID);
			$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
			$Stmt->bindParam(':MemberLoginPW', $MemberLoginPW_hash);
			$Stmt->bindParam(':MemberName', $MemberName);
			$Stmt->bindParam(':MemberNickName', $MemberNickName);
			$Stmt->bindParam(':MemberParentName', $MemberParentName);
			$Stmt->bindParam(':MemberSex', $StrMemberSex);
			$Stmt->bindParam(':MemberBirthday', $MemberBirthday);
			$Stmt->bindParam(':MemberPhone1', $MemberPhone1);
			$Stmt->bindParam(':MemberPhone2', $MemberPhone2);
			$Stmt->bindParam(':MemberEmail', $MemberEmail);
			$Stmt->bindParam(':MemberEmail2', $MemberEmail2);
			$Stmt->bindParam(':MemberZip', $MemberZip);
			$Stmt->bindParam(':MemberAddr1', $MemberAddr1);
			$Stmt->bindParam(':MemberAddr2', $MemberAddr2);
			$Stmt->bindParam(':MemberStateText', $MemberStateText);
			$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
			$Stmt->execute();
			$Stmt = null;
		} 
	}
} 


if ($ErrNum != 0){
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>
<script>
alert("<?=$ErrMsg?>");
//history.go(-1);
</script>
</body>
</html>
<?php
} else {
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>
<script>
parent.location.reload();
//parent.$.fn.colorbox.close();
</script>
</body>
</html>
<?
}
include_once('../includes/dbclose.php');
?>





