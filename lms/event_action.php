<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$MemberID = $_LINK_ADMIN_ID_;

$EventID = isset($_REQUEST["EventID"]) ? $_REQUEST["EventID"] : "";
$EventTitle = isset($_REQUEST["EventTitle"]) ? $_REQUEST["EventTitle"] : "";
$EventContent = isset($_REQUEST["EventContent"]) ? $_REQUEST["EventContent"] : "";
$EventStartDate = isset($_REQUEST["EventStartDate"]) ? $_REQUEST["EventStartDate"] : "";
$EventEndDate = isset($_REQUEST["EventEndDate"]) ? $_REQUEST["EventEndDate"] : "";
$EventState = isset($_REQUEST["EventState"]) ? $_REQUEST["EventState"] : "";
$EventView = isset($_REQUEST["EventView"]) ? $_REQUEST["EventView"] : "";
$EventContentSummary = isset($_REQUEST["EventContentSummary"]) ? $_REQUEST["EventContentSummary"] : "";

if ($EventView!="1"){
	$EventView = 0;
}

if ($EventState!="1"){
	$EventState = 2;
}




//================================== 파일 업로드 ============================
$Path = "../uploads/event_images/";
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


if ($EventID==""){

	$Sql = "select ifnull(Max(EventOrder),0) as EventOrder from Events";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$EventOrder = $Row["EventOrder"]+1;

	$Sql = " insert into Events ( ";
		$Sql .= " MemberID, ";
		$Sql .= " EventTitle, ";
		$Sql .= " EventContentSummary, ";
		$Sql .= " EventContent, ";
		if ($TempFile){
			$Sql .= " EventImageFileName, ";
			$Sql .= " EventImageFileRealName, ";
		}
		$Sql .= " EventStartDate, ";
		$Sql .= " EventEndDate, ";
		$Sql .= " EventRegDateTime, ";
		$Sql .= " EventModiDateTime, ";
		$Sql .= " EventState, ";
		$Sql .= " EventView, ";
		$Sql .= " EventOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :MemberID, ";
		$Sql .= " :EventTitle, ";
		$Sql .= " :EventContentSummary, ";
		$Sql .= " :EventContent, ";
		if ($TempFile){
			$Sql .= " :EventImageFileName, ";
			$Sql .= " :EventImageFileRealName, ";
		}
		$Sql .= " :EventStartDate, ";
		$Sql .= " :EventEndDate, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :EventState, ";
		$Sql .= " :EventView, ";
		$Sql .= " :EventOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':EventTitle', $EventTitle);
	$Stmt->bindParam(':EventContentSummary', $EventContentSummary);
	$Stmt->bindParam(':EventContent', $EventContent);
	if ($TempFile){
		$Stmt->bindParam(':EventImageFileName', $DbMyFileName);
		$Stmt->bindParam(':EventImageFileRealName', $DbMyFileRealName);
	}
	$Stmt->bindParam(':EventStartDate', $EventStartDate);
	$Stmt->bindParam(':EventEndDate', $EventEndDate);
	$Stmt->bindParam(':EventState', $EventState);
	$Stmt->bindParam(':EventView', $EventView);
	$Stmt->bindParam(':EventOrder', $EventOrder);
	$Stmt->execute();
	$EventID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = " update Events set ";
		$Sql .= " EventTitle = :EventTitle, ";
		$Sql .= " EventContentSummary = :EventContentSummary, ";
		$Sql .= " EventContent = :EventContent, ";
		if ($TempFile){
			$Sql .= " EventImageFileName = :EventImageFileName, ";
			$Sql .= " EventImageFileRealName = :EventImageFileRealName, ";
		}
		$Sql .= " EventStartDate = :EventStartDate, ";
		$Sql .= " EventEndDate = :EventEndDate, ";
		$Sql .= " EventModiDateTime = now(), ";
		$Sql .= " EventState = :EventState, ";
		$Sql .= " EventView = :EventView ";
	$Sql .= " where EventID = :EventID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EventTitle', $EventTitle);
	$Stmt->bindParam(':EventContentSummary', $EventContentSummary);
	$Stmt->bindParam(':EventContent', $EventContent);
	if ($TempFile){
		$Stmt->bindParam(':EventImageFileName', $DbMyFileName);
		$Stmt->bindParam(':EventImageFileRealName', $DbMyFileRealName);
	}
	$Stmt->bindParam(':EventStartDate', $EventStartDate);
	$Stmt->bindParam(':EventEndDate', $EventEndDate);
	$Stmt->bindParam(':EventState', $EventState);
	$Stmt->bindParam(':EventView', $EventView);
	$Stmt->bindParam(':EventID', $EventID);
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
?>
<script>
parent.$.fn.colorbox.close();
</script>
<?
}
?>


