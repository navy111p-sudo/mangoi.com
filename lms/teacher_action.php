<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$TeacherGroupID = isset($_REQUEST["TeacherGroupID"]) ? $_REQUEST["TeacherGroupID"] : "";
$TeacherPayTypeItemID = isset($_REQUEST["TeacherPayTypeItemID"]) ? $_REQUEST["TeacherPayTypeItemID"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$TeacherName = isset($_REQUEST["TeacherName"]) ? $_REQUEST["TeacherName"] : "";
$TeacherNickName = isset($_REQUEST["TeacherNickName"]) ? $_REQUEST["TeacherNickName"] : "";

$TeacherPhone1_1 = isset($_REQUEST["TeacherPhone1_1"]) ? $_REQUEST["TeacherPhone1_1"] : "";
$TeacherPhone1_2 = isset($_REQUEST["TeacherPhone1_2"]) ? $_REQUEST["TeacherPhone1_2"] : "";
$TeacherPhone1_3 = isset($_REQUEST["TeacherPhone1_3"]) ? $_REQUEST["TeacherPhone1_3"] : "";
$TeacherPhone2_1 = isset($_REQUEST["TeacherPhone2_1"]) ? $_REQUEST["TeacherPhone2_1"] : "";
$TeacherPhone2_2 = isset($_REQUEST["TeacherPhone2_2"]) ? $_REQUEST["TeacherPhone2_2"] : "";
$TeacherPhone2_3 = isset($_REQUEST["TeacherPhone2_3"]) ? $_REQUEST["TeacherPhone2_3"] : "";
$TeacherPhone3_1 = isset($_REQUEST["TeacherPhone3_1"]) ? $_REQUEST["TeacherPhone3_1"] : "";
$TeacherPhone3_2 = isset($_REQUEST["TeacherPhone3_2"]) ? $_REQUEST["TeacherPhone3_2"] : "";
$TeacherPhone3_3 = isset($_REQUEST["TeacherPhone3_3"]) ? $_REQUEST["TeacherPhone3_3"] : "";
$TeacherEmail_1 = isset($_REQUEST["TeacherEmail_1"]) ? $_REQUEST["TeacherEmail_1"] : "";
$TeacherEmail_2 = isset($_REQUEST["TeacherEmail_2"]) ? $_REQUEST["TeacherEmail_2"] : "";

$TeacherZip = isset($_REQUEST["TeacherZip"]) ? $_REQUEST["TeacherZip"] : "";
$TeacherAddr1 = isset($_REQUEST["TeacherAddr1"]) ? $_REQUEST["TeacherAddr1"] : "";
$TeacherAddr2 = isset($_REQUEST["TeacherAddr2"]) ? $_REQUEST["TeacherAddr2"] : "";
$TeacherVideoType = isset($_REQUEST["TeacherVideoType"]) ? $_REQUEST["TeacherVideoType"] : "";
$TeacherVideoCode = isset($_REQUEST["TeacherVideoCode"]) ? $_REQUEST["TeacherVideoCode"] : "";
$TeacherLogoImage = isset($_REQUEST["TeacherLogoImage"]) ? $_REQUEST["TeacherLogoImage"] : "";
$TeacherIntroText = isset($_REQUEST["TeacherIntroText"]) ? $_REQUEST["TeacherIntroText"] : "";
$TeacherIntroSpec = isset($_REQUEST["TeacherIntroSpec"]) ? $_REQUEST["TeacherIntroSpec"] : "";
$TeacherStartHour = isset($_REQUEST["TeacherStartHour"]) ? $_REQUEST["TeacherStartHour"] : "";
$TeacherEndHour = isset($_REQUEST["TeacherEndHour"]) ? $_REQUEST["TeacherEndHour"] : "";
$TeacherPayPerTime = isset($_REQUEST["TeacherPayPerTime"]) ? $_REQUEST["TeacherPayPerTime"] : "";
$TeacherRegDateTime = isset($_REQUEST["TeacherRegDateTime"]) ? $_REQUEST["TeacherRegDateTime"] : "";
$TeacherState = isset($_REQUEST["TeacherState"]) ? $_REQUEST["TeacherState"] : "";
$TeacherView = isset($_REQUEST["TeacherView"]) ? $_REQUEST["TeacherView"] : "";
$TeacherIntroEdu = isset($_REQUEST["TeacherIntroEdu"]) ? $_REQUEST["TeacherIntroEdu"] : "";
$TeacherIsManager = isset($_REQUEST["TeacherIsManager"]) ? $_REQUEST["TeacherIsManager"] : "";
$TeacherBlock80Min = isset($_REQUEST["TeacherBlock80Min"]) ? $_REQUEST["TeacherBlock80Min"] : "";

$TeacherPhone1 = $TeacherPhone1_1 . "-". $TeacherPhone1_2 . "-" .$TeacherPhone1_3;
$TeacherPhone2 = $TeacherPhone2_1 . "-". $TeacherPhone2_2 . "-" .$TeacherPhone2_3;
$TeacherPhone3 = $TeacherPhone3_1 . "-". $TeacherPhone3_2 . "-" .$TeacherPhone3_3;
$TeacherEmail = $TeacherEmail_1 . "@". $TeacherEmail_2;

//Members 
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$MemberLoginPW = isset($_REQUEST["MemberLoginPW"]) ? $_REQUEST["MemberLoginPW"] : "";
$MemberLanguageID = isset($_REQUEST["MemberLanguageID"]) ? $_REQUEST["MemberLanguageID"] : "";
$MemberLoginNewPW = isset($_REQUEST["MemberLoginNewPW"]) ? $_REQUEST["MemberLoginNewPW"] : "";
$MemberCiTelephone = isset($_REQUEST["MemberCiTelephone"]) ? $_REQUEST["MemberCiTelephone"] : "";
$MemberTimeZoneID = isset($_REQUEST["MemberTimeZoneID"]) ? $_REQUEST["MemberTimeZoneID"] : "1";  // 값을 받지않는다면, 활동국가 한국으로 설정
$MemberBirthday = isset($_REQUEST["MemberBirthday"]) ? $_REQUEST["MemberBirthday"] : "";
$MemberSex = isset($_REQUEST["MemberSex"]) ? $_REQUEST["MemberSex"] : "";

$TeacherVideoCode = trim($TeacherVideoCode);


if ($TeacherView!="1"){
	$TeacherView = 0;
}

if ($TeacherState!="1"){
	$TeacherState = 2;
}


if ($TeacherBlock80Min!="1"){
	$TeacherBlock80Min = 0;
}


//================================== 파일 업로드 ============================
$Path = "../uploads/teacher_images/";
$UploadDir = str_replace(basename(__FILE__), '', realpath($Path)) . "/";


$TempFile = $_FILES['UpFile']['tmp_name'];
if ($TempFile){

	$MyFile         = $_FILES['UpFile']['name'];
	$MyFileSize     = $_FILES['UpFile']['size'];
	$MyFileMimeType = $_FILES['UpFile']['type'];
	$MyFileName     = (iconv('utf-8','euc-kr',$MyFile));
	$MyFileRealName = $MyFileName;

	$FileTypeCheck = explode('.',$MyFileName);
	$FileType       = $FileTypeCheck[count($FileTypeCheck)-1];
	$i = 0;
	
	$RealFileName = "";
	while($i < count($FileTypeCheck)-1){
		$RealFileName .= $FileTypeCheck[$i];
		$i++;
	}
	
	$RealFileName = md5($RealFileName);
	$RealFileNameResize = $RealFileName."_rs";
	$RealFileNameCrop = $RealFileName."_cp";

	$ExistFlag = 0;
	if(file_exists($Path.$RealFileName.'.'.$FileType)){
		$i = 1;
		while($ExistFlag != 1){
			if(!file_exists($Path.$RealFileName.'['.$i.'].'.$FileType)){
				$ExistFlag = 1;
				$MyFileName = $RealFileName.'['.$i.'].'.$FileType;
				$MyFileNameResize = $RealFileNameResize.'['.$i.'].'.$FileType;
				$MyFileNameCrop = $RealFileNameCrop.'['.$i.'].'.$FileType;
			}
			$i++;

		} 
	}else{
		$MyFileName = $RealFileName.'.'.$FileType;
		$MyFileNameResize = $RealFileNameResize.'.'.$FileType;
		$MyFileNameCrop = $RealFileNameCrop.'.'.$FileType;
	}

	if ($FileType=="php" || $FileType=="php3" || $FileType=="html"){
		$MyFileName = $MyFileName."_";
	}

	if(!@copy($TempFile, $Path.$MyFileName)) { echo("error"); }


	//사이즈 줄이기 and 자르기 ==============
	
	
	/*
	$ImgSize = getimagesize($UploadDir."".$MyFileName);
	$ImgWidth = $ImgSize[0];
	$ImgHeight = $ImgSize[1];

	$ffmpeg = "ffmpeg";

	
	//방법 1) 큰쪽을 상한 사이즈에 맞추기
	if ($ImgWidth>=$ImgHeight && $ImgWidth>1080){
		
		$ffmpegcmd = $ffmpeg." -i ".$UploadDir."".$MyFileName." -vf scale=1080:-1 ".$UploadDir."".$MyFileNameResize;
		exec($ffmpegcmd);
		unlink($UploadDir."".$MyFileName);

	}else if ($ImgWidth<$ImgHeight && $ImgHeight>1080){
		echo "bbbb";
		$ffmpegcmd = $ffmpeg." -i ".$UploadDir."".$MyFileName." -vf scale=-1:1080 ".$UploadDir."".$MyFileNameResize;
		unlink($UploadDir."".$MyFileName);

	}else{
		$MyFileNameResize = $MyFileName;
	}
	
	$MyFileName = $MyFileNameResize;



	//방법 2)정사각형 이미지 만들기

	if ($ImgWidth>=$ImgHeight && $ImgHeight>1080){

		$ffmpegcmd = $ffmpeg." -i ".$UploadDir."".$MyFileName." -vf scale=-1:1080 ".$UploadDir."".$MyFileNameResize;
		exec($ffmpegcmd);
		unlink($UploadDir."".$MyFileName);

	}else if ($ImgWidth<$ImgHeight && $ImgWidth>1080){
		
		$ffmpegcmd = $ffmpeg." -i ".$UploadDir."".$MyFileName." -vf scale=1080:-1 ".$UploadDir."".$MyFileNameResize;
		exec($ffmpegcmd);
		unlink($UploadDir."".$MyFileName);

	}else{
		$MyFileNameResize = $MyFileName;
	}
	
	$MyFileName = $MyFileNameResize;

	if ($ImgWidth>=1080 && $ImgHeight>=1080){

		$ffmpegcmd = $ffmpeg." -i ".$UploadDir."".$MyFileName." -vf crop=1080:1080 ".$UploadDir."".$MyFileNameCrop;
		exec($ffmpegcmd);
		unlink($UploadDir."".$MyFileName);

	}else{
		$MyFileNameCrop = $MyFileName;
	}

	$MyFileName = $MyFileNameCrop;
	*/


	//사이즈 줄이기 and 자르기 ==============




	$DbMyFileName      = (iconv('euc-kr','utf-8',$MyFileName));
	$DbMyFileRealName  = (iconv('euc-kr','utf-8',$MyFileRealName));
	$DbMyFileSize      = $MyFileSize;
	$DbMyFileExtension = $FileType;
	$DbMyFileMimeType  = $MyFileMimeType;

}
//================================== 파일 업로드 ============================

$MemberLoginNewPW_hash = password_hash(sha1($MemberLoginNewPW), PASSWORD_DEFAULT);


if ($TeacherID==""){

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
		$Sql = "select ifnull(Max(TeacherOrder),0) as TeacherOrder from Teachers";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$TeacherOrder = $Row["TeacherOrder"]+1;

		$Sql = " insert into Teachers ( ";
			$Sql .= " TeacherGroupID, ";
			$Sql .= " TeacherPayTypeItemID, ";
			$Sql .= " TeacherName, ";
			$Sql .= " TeacherNickName, ";
			$Sql .= " TeacherIsManager, ";
			if ($TempFile){
				$Sql .= " TeacherImageFileName, ";
				$Sql .= " TeacherImageFileRealName, ";
			}
			$Sql .= " TeacherPhone1, ";
			$Sql .= " TeacherPhone2, ";
			$Sql .= " TeacherPhone3, ";
			$Sql .= " TeacherEmail, ";
			$Sql .= " TeacherZip, ";
			$Sql .= " TeacherAddr1, ";
			$Sql .= " TeacherAddr2, ";
			$Sql .= " TeacherVideoType, ";
			$Sql .= " TeacherVideoCode, ";
			$Sql .= " TeacherLogoImage, ";
			$Sql .= " TeacherIntroEdu, ";
			$Sql .= " TeacherIntroText, ";
			$Sql .= " TeacherIntroSpec, ";
			//$Sql .= " TeacherStartHour, ";
			//$Sql .= " TeacherEndHour, ";
			$Sql .= " TeacherPayPerTime, ";
			$Sql .= " TeacherBlock80Min, ";
			$Sql .= " TeacherRegDateTime, ";
			$Sql .= " TeacherModiDateTime, ";
			$Sql .= " TeacherState, ";
			$Sql .= " TeacherView, ";
			$Sql .= " TeacherOrder ";
		$Sql .= " ) values ( ";
			$Sql .= " :TeacherGroupID, ";
			$Sql .= " :TeacherPayTypeItemID, ";
			$Sql .= " :TeacherName, ";
			$Sql .= " :TeacherNickName, ";
			$Sql .= " :TeacherIsManager, ";
			if ($TempFile){
				$Sql .= " :TeacherImageFileName, ";
				$Sql .= " :TeacherImageFileRealName, ";
			}
			$Sql .= " HEX(AES_ENCRYPT(:TeacherPhone1, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:TeacherPhone2, :EncryptionKey)), ";
			$Sql .= " HEX(AES_ENCRYPT(:TeacherPhone3, :EncryptionKey)), ";;
			$Sql .= " HEX(AES_ENCRYPT(:TeacherEmail, :EncryptionKey)), ";
			$Sql .= " :TeacherZip, ";
			$Sql .= " :TeacherAddr1, ";
			$Sql .= " :TeacherAddr2, ";
			$Sql .= " :TeacherVideoType, ";
			$Sql .= " :TeacherVideoCode, ";
			$Sql .= " :TeacherLogoImage, ";
			//$Sql .= " :TeacherStartHour, ";
			//$Sql .= " :TeacherEndHour, ";
			$Sql .= " :TeacherIntroEdu, ";
			$Sql .= " :TeacherIntroText, ";
			$Sql .= " :TeacherIntroSpec, ";
			$Sql .= " :TeacherPayPerTime, ";
			$Sql .= " :TeacherBlock80Min, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " :TeacherState, ";
			$Sql .= " :TeacherView, ";
			$Sql .= " :TeacherOrder ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':TeacherGroupID', $TeacherGroupID);
		$Stmt->bindParam(':TeacherPayTypeItemID', $TeacherPayTypeItemID);
		$Stmt->bindParam(':TeacherName', $TeacherName);
		$Stmt->bindParam(':TeacherNickName', $TeacherNickName);
		$Stmt->bindParam(':TeacherIsManager', $TeacherIsManager);
		if ($TempFile){
			$Stmt->bindParam(':TeacherImageFileName', $DbMyFileName);
			$Stmt->bindParam(':TeacherImageFileRealName', $DbMyFileRealName);
		}
		$Stmt->bindParam(':TeacherPhone1', $TeacherPhone1);
		$Stmt->bindParam(':TeacherPhone2', $TeacherPhone2);
		$Stmt->bindParam(':TeacherPhone3', $TeacherPhone3);
		$Stmt->bindParam(':TeacherEmail', $TeacherEmail);
		$Stmt->bindParam(':TeacherZip', $TeacherZip);
		$Stmt->bindParam(':TeacherAddr1', $TeacherAddr1);
		$Stmt->bindParam(':TeacherAddr2', $TeacherAddr2);
		$Stmt->bindParam(':TeacherVideoType', $TeacherVideoType);
		$Stmt->bindParam(':TeacherVideoCode', $TeacherVideoCode);
		$Stmt->bindParam(':TeacherLogoImage', $TeacherLogoImage);
		//$Stmt->bindParam(':TeacherStartHour', $TeacherStartHour);
		//$Stmt->bindParam(':TeacherEndHour', $TeacherEndHour);
		$Stmt->bindParam(':TeacherIntroEdu', $TeacherIntroEdu);
		$Stmt->bindParam(':TeacherIntroText', $TeacherIntroText);
		$Stmt->bindParam(':TeacherIntroSpec', $TeacherIntroSpec);
		$Stmt->bindParam(':TeacherPayPerTime', $TeacherPayPerTime);
		$Stmt->bindParam(':TeacherBlock80Min', $TeacherBlock80Min);
		$Stmt->bindParam(':TeacherState', $TeacherState);
		$Stmt->bindParam(':TeacherView', $TeacherView);
		$Stmt->bindParam(':TeacherOrder', $TeacherOrder);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$TeacherID = $DbConn->lastInsertId();
		$Stmt = null;


		//Members 
		$MemberLevelID = 15;//강사

		$Sql = " insert into Members ( ";
			$Sql .= " TeacherID, ";
			$Sql .= " MemberTimeZoneID, ";
			$Sql .= " MemberLevelID, ";
			$Sql .= " MemberCiTelephone, ";
			$Sql .= " MemberLoginID, ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " MemberLoginPW, ";
			}
			$Sql .= " MemberLanguageID, ";
			$Sql .= " MemberName, ";
			$Sql .= " MemberSex, ";
			$Sql .= " MemberBirthday, ";
			$Sql .= " MemberEmail, ";
			$Sql .= " MemberView, ";
			$Sql .= " MemberState, ";
			$Sql .= " MemberRegDateTime, ";
			$Sql .= " MemberModiDateTime ";

		$Sql .= " ) values ( ";

			$Sql .= " :TeacherID, ";
			$Sql .= " :MemberTimeZoneID, ";
			$Sql .= " :MemberLevelID, ";
			$Sql .= " :MemberCiTelephone, ";
			$Sql .= " :MemberLoginID, ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " :MemberLoginNewPW_hash, ";
			}
			$Sql .= " :MemberLanguageID, ";
			$Sql .= " :MemberName, ";
			$Sql .= " :MemberSex, ";
			$Sql .= " :MemberBirthday, ";
			$Sql .= " HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
			$Sql .= " :MemberView, ";
			$Sql .= " :MemberState, ";
			$Sql .= " now(), ";
			$Sql .= " now() ";

		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':TeacherID', $TeacherID);
		$Stmt->bindParam(':MemberTimeZoneID', $MemberTimeZoneID);
		$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
		$Stmt->bindParam(':MemberCiTelephone', $MemberCiTelephone);
		$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
		if ($MemberLoginNewPW!=""){
			$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
		}
		$Stmt->bindParam(':MemberLanguageID', $MemberLanguageID);
		$Stmt->bindParam(':MemberName', $TeacherName);
		$Stmt->bindParam(':MemberSex', $MemberSex);
		$Stmt->bindParam(':MemberBirthday', $MemberBirthday);
		$Stmt->bindParam(':MemberEmail', $TeacherEmail);
		$Stmt->bindParam(':MemberView', $TeacherView);
		$Stmt->bindParam(':MemberState', $TeacherState);

		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
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
		$Sql = " update Teachers set ";
			$Sql .= " TeacherGroupID = :TeacherGroupID, ";
			$Sql .= " TeacherPayTypeItemID = :TeacherPayTypeItemID, ";
			$Sql .= " TeacherName = :TeacherName, ";
			$Sql .= " TeacherNickName = :TeacherNickName, ";
			$Sql .= " TeacherIsManager = :TeacherIsManager, ";
			if ($TempFile){
				$Sql .= " TeacherImageFileName = :TeacherImageFileName, ";
				$Sql .= " TeacherImageFileRealName = :TeacherImageFileRealName, ";
			}
			$Sql .= " TeacherPhone1 = HEX(AES_ENCRYPT(:TeacherPhone1, :EncryptionKey)), ";
			$Sql .= " TeacherPhone2 = HEX(AES_ENCRYPT(:TeacherPhone2, :EncryptionKey)), ";
			$Sql .= " TeacherPhone3 = HEX(AES_ENCRYPT(:TeacherPhone3, :EncryptionKey)), ";
			$Sql .= " TeacherEmail = HEX(AES_ENCRYPT(:TeacherEmail, :EncryptionKey)), ";
			$Sql .= " TeacherZip = :TeacherZip, ";
			$Sql .= " TeacherAddr1 = :TeacherAddr1, ";
			$Sql .= " TeacherAddr2 = :TeacherAddr2, ";
			$Sql .= " TeacherVideoType = :TeacherVideoType, ";
			$Sql .= " TeacherVideoCode = :TeacherVideoCode, ";
			$Sql .= " TeacherLogoImage = :TeacherLogoImage, ";
			//$Sql .= " TeacherStartHour = :TeacherStartHour, ";
			//$Sql .= " TeacherEndHour = :TeacherEndHour, ";
			$Sql .= " TeacherIntroEdu = :TeacherIntroEdu, ";
			$Sql .= " TeacherIntroText = :TeacherIntroText, ";
			$Sql .= " TeacherIntroSpec = :TeacherIntroSpec, ";
			$Sql .= " TeacherPayPerTime = :TeacherPayPerTime, ";
			$Sql .= " TeacherBlock80Min = :TeacherBlock80Min, ";
			$Sql .= " TeacherState = :TeacherState, ";
			$Sql .= " TeacherView = :TeacherView, ";
			$Sql .= " TeacherModiDateTime = now() ";
		$Sql .= " where TeacherID = :TeacherID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':TeacherGroupID', $TeacherGroupID);
		$Stmt->bindParam(':TeacherPayTypeItemID', $TeacherPayTypeItemID);
		$Stmt->bindParam(':TeacherName', $TeacherName);
		$Stmt->bindParam(':TeacherNickName', $TeacherNickName);
		$Stmt->bindParam(':TeacherIsManager', $TeacherIsManager);
		if ($TempFile){
			$Stmt->bindParam(':TeacherImageFileName', $DbMyFileName);
			$Stmt->bindParam(':TeacherImageFileRealName', $DbMyFileRealName);
		}
		$Stmt->bindParam(':TeacherPhone1', $TeacherPhone1);
		$Stmt->bindParam(':TeacherPhone2', $TeacherPhone2);
		$Stmt->bindParam(':TeacherPhone3', $TeacherPhone3);
		$Stmt->bindParam(':TeacherEmail', $TeacherEmail);
		$Stmt->bindParam(':TeacherZip', $TeacherZip);
		$Stmt->bindParam(':TeacherAddr1', $TeacherAddr1);
		$Stmt->bindParam(':TeacherAddr2', $TeacherAddr2);
		$Stmt->bindParam(':TeacherVideoType', $TeacherVideoType);
		$Stmt->bindParam(':TeacherVideoCode', $TeacherVideoCode);
		$Stmt->bindParam(':TeacherLogoImage', $TeacherLogoImage);
		//$Stmt->bindParam(':TeacherStartHour', $TeacherStartHour);
		//$Stmt->bindParam(':TeacherEndHour', $TeacherEndHour);
		$Stmt->bindParam(':TeacherIntroEdu', $TeacherIntroEdu);
		$Stmt->bindParam(':TeacherIntroText', $TeacherIntroText);
		$Stmt->bindParam(':TeacherIntroSpec', $TeacherIntroSpec);
		$Stmt->bindParam(':TeacherPayPerTime', $TeacherPayPerTime);
		$Stmt->bindParam(':TeacherBlock80Min', $TeacherBlock80Min);
		$Stmt->bindParam(':TeacherState', $TeacherState);
		$Stmt->bindParam(':TeacherView', $TeacherView);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->bindParam(':TeacherID', $TeacherID);
		$Stmt->execute();
		$Stmt = null;

		//Members 
		$Sql = " update Members set ";
			if ($MemberLoginNewPW!=""){
				$Sql .= " MemberLoginPW = :MemberLoginNewPW_hash, ";
			}
			$Sql .= " MemberTimeZoneID = :MemberTimeZoneID, ";
			$Sql .= " MemberCiTelephone = :MemberCiTelephone, ";
			$Sql .= " MemberLanguageID = :MemberLanguageID, ";
			$Sql .= " MemberName = :MemberName, ";
			$Sql .= " MemberSex = :MemberSex, ";
			$Sql .= " MemberBirthday = :MemberBirthday, ";
			$Sql .= " MemberEmail = HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
			$Sql .= " MemberView = :MemberView, ";
			$Sql .= " MemberState = :MemberState, ";
			$Sql .= " MemberModiDateTime = now() ";
		$Sql .= " where MemberID = :MemberID ";

		$Stmt = $DbConn->prepare($Sql);
		if ($MemberLoginNewPW!=""){
			$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
		}
		$Stmt->bindParam(':MemberTimeZoneID', $MemberTimeZoneID);
		$Stmt->bindParam(':MemberCiTelephone', $MemberCiTelephone);
		$Stmt->bindParam(':MemberLanguageID', $MemberLanguageID);
		$Stmt->bindParam(':MemberName', $TeacherName);
		$Stmt->bindParam(':MemberSex', $MemberSex);
		$Stmt->bindParam(':MemberBirthday', $MemberBirthday);
		$Stmt->bindParam(':MemberEmail', $TeacherEmail);
		$Stmt->bindParam(':MemberView', $TeacherView);
		$Stmt->bindParam(':MemberState', $TeacherState);

		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->bindParam(':MemberID', $MemberID);
		$Stmt->execute();
		$Stmt = null;
	}
}


//===================== 성향관리 =======================
$Sql = "delete from TeacherCharacters where TeacherID=$TeacherID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt = null;

$Sql3 = "select A.* from TeacherCharacterItems A where A.TeacherCharacterItemState=1";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
while($Row3 = $Stmt3->fetch()) {
	$TeacherCharacterItemID = isset($_REQUEST["TeacherCharacterItemID_".$Row3['TeacherCharacterItemID']]) ? $_REQUEST["TeacherCharacterItemID_".$Row3['TeacherCharacterItemID']] : "";

	if ($TeacherCharacterItemID=="1"){

		$Sql = " insert into TeacherCharacters ( ";
			$Sql .= " TeacherID, ";
			$Sql .= " TeacherCharacterItemID ";
		$Sql .= " ) values ( ";
			$Sql .= " :TeacherID, ";
			$Sql .= " :TeacherCharacterItemID ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':TeacherID', $TeacherID);
		$Stmt->bindParam(':TeacherCharacterItemID', $Row3['TeacherCharacterItemID']);
		$Stmt->execute();
		$Stmt = null;


	}

}
//===================== 성향관리 =======================

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


