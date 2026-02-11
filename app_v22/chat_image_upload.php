<?php
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ErrNum = 0;
$ErrMsg = "";

$UpPath = "../uploads/chat_images/";
$UploadDir = str_replace(basename(__FILE__), '', realpath($UpPath)) . "/";


$TempFile = $_FILES['file']['tmp_name'];
if ($TempFile){

	$MyFile         = $_FILES['file']['name'];
	$MyFileSize     = $_FILES['file']['size'];
	$MyFileMimeType = $_FILES['file']['type'];
	$MyFileName     = (iconv('utf-8','euc-kr',$MyFile));
	$MyFileRealName = $MyFileName;


	if (strpos($MyFileRealName,"?")>=0){
		$ArrMyFileRealName = explode("?", $MyFileRealName);
		$MyFileRealName = $ArrMyFileRealName[0];
	}


	$FileTypeCheck = explode('.',$MyFileName);
	$FileType       = $FileTypeCheck[count($FileTypeCheck)-1];
	$i = 0;

	if (strpos($FileType,"?")>=0){
		$ArrFileType = explode("?", $FileType);
		$FileType = $ArrFileType[0];
	}
	
	$RealFileName = "";
	while($i < count($FileTypeCheck)-1){
		$RealFileName .= $FileTypeCheck[$i];
		$i++;
	}
	
	$RealFileName = md5($RealFileName);
	$RealFileNameResize = $RealFileName."_rs";
	$RealFileNameCrop = $RealFileName."_cp";

	$ExistFlag = 0;
	if(file_exists($UpPath.$RealFileName.'.'.$FileType)){
		$i = 1;
		while($ExistFlag != 1){
			if(!file_exists($UpPath.$RealFileName.'['.$i.'].'.$FileType)){
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

	if(!@copy($TempFile, $UpPath.$MyFileName)) { echo("error"); }


	$DbMyFileName      = (iconv('euc-kr','utf-8',$MyFileName));//저장된 이름
	$DbMyFileRealName  = (iconv('euc-kr','utf-8',$MyFileRealName));//원래 이름
	$DbMyFileSize      = $MyFileSize;
	$DbMyFileExtension = $FileType;
	$DbMyFileMimeType  = $MyFileMimeType;


}

echo $DbMyFileName ."|". $DbMyFileRealName;

include_once('../includes/dbclose.php');
?>