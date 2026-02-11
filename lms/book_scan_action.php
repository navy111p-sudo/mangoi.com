<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
?>
<link rel="stylesheet" type="text/css" href="css/common.css" />
</head>
</body>
<?

$err_num = 0;
$err_msg = "";

$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";
$BookScanID = isset($_REQUEST["BookScanID"]) ? $_REQUEST["BookScanID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BookScanName = isset($_REQUEST["BookScanName"]) ? $_REQUEST["BookScanName"] : "";
$BookScanState = isset($_REQUEST["BookScanState"]) ? $_REQUEST["BookScanState"] : "";
$UpFile = isset($_REQUEST["UpFile"]) ? $_REQUEST["UpFile"] : "";

if ($BookScanState!="1"){
	$BookScanState = 2;
}

$BookScanView = 1;





//================================== 파일 업로드 ============================
/*$Path = "../uploads/book_scan_uploads/";
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
	


	//사이즈 줄이기 and 자르기 ==============



	$DbMyFileName      = (iconv('euc-kr','utf-8',$MyFileName));
	$DbMyFileRealName  = (iconv('euc-kr','utf-8',$MyFileRealName));
	$DbMyFileSize      = $MyFileSize;
	$DbMyFileExtension = $FileType;
	$DbMyFileMimeType  = $MyFileMimeType;

}
//================================== 파일 업로드 ============================*/






if ($BookScanID==""){

	$Sql = "select ifnull(Max(BookScanOrder),0) as BookScanOrder from BookScans";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	
	$BookScanOrder = $Row["BookScanOrder"]+1;


	$Sql = " insert into BookScans ( ";
		$Sql .= " BookID, ";
		$Sql .= " BookScanName, ";
		//if ($TempFile){
			$Sql .= " BookScanImageFileName, ";
		//	$Sql .= " BookScanImageFileRealName, ";
		//}
		$Sql .= " BookScanRegDateTime, ";
		$Sql .= " BookScanModiDateTime, ";
		$Sql .= " BookScanOrder, ";
		$Sql .= " BookScanView, ";
		$Sql .= " BookScanState ";
	$Sql .= " ) values ( ";
		$Sql .= " :BookID, ";
		$Sql .= " :BookScanName, ";
		//if ($TempFile){
			$Sql .= " :BookScanImageFileName, ";
		//	$Sql .= " :BookScanImageFileRealName, ";
		//}
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :BookScanOrder, ";
		$Sql .= " :BookScanView, ";
		$Sql .= " :BookScanState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookID', $BookID);
	$Stmt->bindParam(':BookScanName', $BookScanName);
	//if ($TempFile){
		$Stmt->bindParam(':BookScanImageFileName', $UpFile);
	//	$Stmt->bindParam(':BookScanImageFileRealName', $DbMyFileRealName);
	//}
	$Stmt->bindParam(':BookScanOrder', $BookScanOrder);
	$Stmt->bindParam(':BookScanView', $BookScanView);
	$Stmt->bindParam(':BookScanState', $BookScanState);
	$Stmt->execute();
	$Stmt = null;


}else{

	$Sql = " update BookScans set ";
		$Sql .= " BookScanName = :BookScanName, ";
		//if ($TempFile){
			$Sql .= " BookScanImageFileName = :BookScanImageFileName, ";
		//	$Sql .= " BookScanImageFileRealName = :BookScanImageFileRealName, ";
		//}
		$Sql .= " BookScanView = :BookScanView, ";
		$Sql .= " BookScanState = :BookScanState, ";
		$Sql .= " BookScanModiDateTime = now() ";
	$Sql .= " where BookScanID = :BookScanID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookScanName', $BookScanName);
	//if ($TempFile){
		$Stmt->bindParam(':BookScanImageFileName', $UpFile);
	//	$Stmt->bindParam(':BookScanImageFileRealName', $DbMyFileRealName);
	//}
	$Stmt->bindParam(':BookScanView', $BookScanView);
	$Stmt->bindParam(':BookScanState', $BookScanState);
	$Stmt->bindParam(':BookScanID', $BookScanID);
	$Stmt->execute();
	$Stmt = null;

}
?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>
//parent.$.fn.colorbox.close();
parent.location.href = "book_form.php?<?=$ListParam?>&BookID=<?=$BookID?>&PageTabID=4";
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

