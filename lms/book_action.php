<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";
$BookGroupID = isset($_REQUEST["BookGroupID"]) ? $_REQUEST["BookGroupID"] : "";
$BookName = isset($_REQUEST["BookName"]) ? $_REQUEST["BookName"] : "";
$BookMemo = isset($_REQUEST["BookMemo"]) ? $_REQUEST["BookMemo"] : "";
$BookState = isset($_REQUEST["BookState"]) ? $_REQUEST["BookState"] : "";
$BookView = isset($_REQUEST["BookView"]) ? $_REQUEST["BookView"] : "";
$BookTeacherView = isset($_REQUEST["BookTeacherView"]) ? $_REQUEST["BookTeacherView"] : "";
$BookViewList = isset($_REQUEST["BookViewList"]) ? $_REQUEST["BookViewList"] : "";

if ($BookView!="1"){
	$BookView = 0;
}

if ($BookState!="1"){
	$BookState = 2;
}

if ($BookTeacherView!="1"){
	$BookTeacherView = 0;
}

if ($BookViewList!="1"){
	$BookViewList = 0;
}


//================================== 파일 업로드 ============================
$Path = "../uploads/book_images/";
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




if ($BookID==""){

	$Sql = "select ifnull(Max(BookOrder),0) as BookOrder from Books";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BookOrder = $Row["BookOrder"]+1;

	$Sql = " insert into Books ( ";
		$Sql .= " BookGroupID, ";
		$Sql .= " BookName, ";
		$Sql .= " BookMemo, ";
		if ($TempFile){
			$Sql .= " BookImageFileName, ";
			$Sql .= " BookImageFileRealName, ";
		}
		$Sql .= " BookRegDateTime, ";
		$Sql .= " BookModiDateTime, ";
		$Sql .= " BookState, ";
		$Sql .= " BookView, ";
		$Sql .= " BookTeacherView, ";
		$Sql .= " BookViewList, ";
		$Sql .= " BookOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :BookGroupID, ";
		$Sql .= " :BookName, ";
		$Sql .= " :BookMemo, ";
		if ($TempFile){
			$Sql .= " :BookImageFileName, ";
			$Sql .= " :BookImageFileRealName, ";
		}
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :BookState, ";
		$Sql .= " :BookView, ";
		$Sql .= " :BookTeacherView, ";
		$Sql .= " :BookViewList, ";
		$Sql .= " :BookOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookGroupID', $BookGroupID);
	$Stmt->bindParam(':BookName', $BookName);
	$Stmt->bindParam(':BookMemo', $BookMemo);
	if ($TempFile){
		$Stmt->bindParam(':BookImageFileName', $DbMyFileName);
		$Stmt->bindParam(':BookImageFileRealName', $DbMyFileRealName);
	}
	$Stmt->bindParam(':BookState', $BookState);
	$Stmt->bindParam(':BookView', $BookView);
	$Stmt->bindParam(':BookTeacherView', $BookTeacherView);
	$Stmt->bindParam(':BookViewList', $BookViewList);
	$Stmt->bindParam(':BookOrder', $BookOrder);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update Books set ";
		$Sql .= " BookGroupID = :BookGroupID, ";
		$Sql .= " BookName = :BookName, ";
		$Sql .= " BookMemo = :BookMemo, ";
		if ($TempFile){
			$Sql .= " BookImageFileName = :BookImageFileName, ";
			$Sql .= " BookImageFileRealName = :BookImageFileRealName, ";
		}
		$Sql .= " BookState = :BookState, ";
		$Sql .= " BookView = :BookView, ";
		$Sql .= " BookTeacherView = :BookTeacherView, ";
		$Sql .= " BookViewList = :BookViewList, ";
		$Sql .= " BookModiDateTime = now() ";
	$Sql .= " where BookID = :BookID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookGroupID', $BookGroupID);
	$Stmt->bindParam(':BookName', $BookName);
	$Stmt->bindParam(':BookMemo', $BookMemo);
	if ($TempFile){
		$Stmt->bindParam(':BookImageFileName', $DbMyFileName);
		$Stmt->bindParam(':BookImageFileRealName', $DbMyFileRealName);
	}
	$Stmt->bindParam(':BookState', $BookState);
	$Stmt->bindParam(':BookView', $BookView);
	$Stmt->bindParam(':BookTeacherView', $BookTeacherView);
	$Stmt->bindParam(':BookViewList', $BookViewList);
	$Stmt->bindParam(':BookID', $BookID);
	$Stmt->execute();
	$Stmt = null;

}


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
	header("Location: book_list.php?$ListParam"); 
	exit;
}
?>


