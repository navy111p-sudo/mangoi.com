<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/password_hash.php');
include_once("../PHPExcel-1.8/Classes/PHPExcel.php");

$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$UpPath = isset($_REQUEST["UpPath"]) ? $_REQUEST["UpPath"] : "";
$MyFileName = isset($_REQUEST["MyFileName"]) ? $_REQUEST["MyFileName"] : "";

$ErrNum = 0;
$ErrMsg = "";
$UpPath = "../uploads/excel_add_student/";
$row = 1; 
libxml_use_internal_errors(true); // 일반적인 경고문을 안보여주는...https://codeday.me/ko/qa/20190325/149807.html also stackoverflow too,
$filename = $UpPath.$MyFileName; // 읽어들일 엑셀 파일의 경로와 파일명을 지정한다.
$objPHPExcel = new PHPExcel();

try {
	// 업로드 된 엑셀 형식에 맞는 Reader객체를 만든다.
	$objReader = PHPExcel_IOFactory::createReaderForFile($filename);
	// 읽기전용으로 설정
	$objReader->setReadDataOnly(true);
	// 엑셀파일을 읽는다
	$objExcel = $objReader->load($filename);
	// 첫번째 시트를 선택
	$objExcel->setActiveSheetIndex(0);
	$objWorksheet = $objExcel->getActiveSheet();
	$rowIterator = $objWorksheet->getRowIterator();

	foreach ($rowIterator as $row) { // 모든 행에 대해서
		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false); 
	}

	$maxRow = $objWorksheet->getHighestRow();

	for ($i = 7 ; $i <= $maxRow ; $i++) {
		// 중복 아이디가 있는지 체크
		$MemberName = $objWorksheet->getCell('A' . $i)->getValue(); // A열
		$MemberNickName = $objWorksheet->getCell('B' . $i)->getValue(); // B열
		$MemberLoginID = $objWorksheet->getCell('C' . $i)->getValue(); // C열
		$MemberLoginPW = $objWorksheet->getCell('D' . $i)->getValue(); // D열
		$MemberPhone1 = $objWorksheet->getCell('E' . $i)->getValue(); // E열
		$MemberEmail = $objWorksheet->getCell('F' . $i)->getValue(); // F열
		$MemberSex = $objWorksheet->getCell('G' . $i)->getValue(); // F열
		$MemberBirthday = $objWorksheet->getCell('H' . $i)->getValue(); // F열
		
		$MemberBirthday = PHPExcel_Style_NumberFormat::toFormattedString($MemberBirthday, 'YYYY-MM-DD'); // 날짜 형태의 셀을 읽을때는 toFormattedString를 사용한다.
		$MemberZip = $objWorksheet->getCell('I' . $i)->getValue(); // F열
		$MemberAddr1 = $objWorksheet->getCell('J' . $i)->getValue(); // F열
		$MemberAddr2 = $objWorksheet->getCell('K' . $i)->getValue(); // F열
		$MemberStateText = $objWorksheet->getCell('L' . $i)->getValue(); // F열
		$MemberParentName = $objWorksheet->getCell('M' . $i)->getValue(); // F열
		$MemberPhone2 = $objWorksheet->getCell('N' . $i)->getValue(); // F열
		$MemberEmail2 = $objWorksheet->getCell('O' . $i)->getValue(); // F열

		if($MemberNickName=="") { // 닉네임이 없을 경우 이름 을 부여한다
			$MemberNickName = $MemberName;
		}

		if($MemberPhone1=="") {
			$MemberPhone1 = "--";
		}
		if($MemberEmail=="") {
			$MemberEmail = "@";
		}

		if($MemberBirthday=="") {
			$MemberBirthday = date('Y-m-d', strtotime("1970-01-01"));
		}

		if($MemberPhone2=="") {
			$MemberPhone2 = "--";
		}

		if($MemberEmail2=="") {
			$MemberEmail2 = "@";
		}

		if (mb_detect_encoding($MemberName, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberName = iconv("EUC-KR", "UTF-8", $MemberName);
		}
		if (mb_detect_encoding($MemberNickName, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberNickName = iconv("EUC-KR", "UTF-8", $MemberNickName);
		}
		if (mb_detect_encoding($MemberLoginID, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberLoginID = iconv("EUC-KR", "UTF-8", $MemberLoginID);
		}
		if (mb_detect_encoding($MemberLoginPW, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberLoginPW = iconv("EUC-KR", "UTF-8", $MemberLoginPW);
		}
		if (mb_detect_encoding($MemberPhone1, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberPhone1 = iconv("EUC-KR", "UTF-8", $MemberPhone1);
		}
		if (mb_detect_encoding($MemberEmail, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberEmail = iconv("EUC-KR", "UTF-8", $MemberEmail);
		}
		if (mb_detect_encoding($MemberSex, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberSex = iconv("EUC-KR", "UTF-8", $MemberSex);
		}
		if (mb_detect_encoding($MemberBirthday, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberBirthday = iconv("EUC-KR", "UTF-8", $MemberBirthday);
		}
		if (mb_detect_encoding($MemberZip, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberZip = iconv("EUC-KR", "UTF-8", $MemberZip);
		}
		if (mb_detect_encoding($MemberAddr1, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberAddr1 = iconv("EUC-KR", "UTF-8", $MemberAddr1);
		}
		if (mb_detect_encoding($MemberAddr2, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberAddr2 = iconv("EUC-KR", "UTF-8", $MemberAddr2);
		}
		if (mb_detect_encoding($MemberStateText, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberStateText = iconv("EUC-KR", "UTF-8", $MemberStateText);
		}
		if (mb_detect_encoding($MemberParentName, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberParentName = iconv("EUC-KR", "UTF-8", $MemberParentName);
		}
		if (mb_detect_encoding($MemberPhone2, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberPhone2 = iconv("EUC-KR", "UTF-8", $MemberPhone2);
		}
		if (mb_detect_encoding($MemberEmail2, "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
			$MemberEmail2 = iconv("EUC-KR", "UTF-8", $MemberEmail2);
		}

		if($MemberSex=="남") {
			$StrMemberSex = 1;
		} else if($MemberSex=="여") {
			$StrMemberSex = 2;
		} else if($MemberSex=="") {
			$StrMemberSex = 1;
		}

			$MemberLoginPW_hash = password_hash(sha1($MemberLoginPW), PASSWORD_DEFAULT);


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
			$MemberID = $DbConn->lastInsertId();
			$Stmt = null;
			
			InsertPoint(1, 0, $MemberID, "회원가입(LMS)", "회원가입(LMS)" ,$OnlineSiteMemberRegPoint);
			SendSmsWelcome($MemberID, $EncryptionKey);
		} 

} catch (exception $e) {
	echo '엑셀파일을 읽는도중 오류가 발생하였습니다.';
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





