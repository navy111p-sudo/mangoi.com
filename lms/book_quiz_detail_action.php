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
$BookQuizID = isset($_REQUEST["BookQuizID"]) ? $_REQUEST["BookQuizID"] : "";
$BookQuizDetailID = isset($_REQUEST["BookQuizDetailID"]) ? $_REQUEST["BookQuizDetailID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";

// 타입 정의
$BookQuizDetailQuizType = isset($_REQUEST["BookQuizDetailQuizType"]) ? $_REQUEST["BookQuizDetailQuizType"] : "";
$BookQuizDetailQuestionType = isset($_REQUEST["BookQuizDetailQuestionType"]) ? $_REQUEST["BookQuizDetailQuestionType"] : "";
$BookQuizDetailAnswerType = isset($_REQUEST["BookQuizDetailAnswerType"]) ? $_REQUEST["BookQuizDetailAnswerType"] : "";
// 타입 정의 //

// 문제 정의
$BookQuizDetailText = isset($_REQUEST["BookQuizDetailText"]) ? $_REQUEST["BookQuizDetailText"] : "";
$UpFile = isset($_REQUEST["UpFile"]) ? $_REQUEST["UpFile"] : "";
$BookQuizDetailTextQuestion = isset($_REQUEST["BookQuizDetailTextQuestion"]) ? $_REQUEST["BookQuizDetailTextQuestion"] : "";
$BookQuizDetailVideoCode = isset($_REQUEST["BookQuizDetailVideoCode"]) ? $_REQUEST["BookQuizDetailVideoCode"] : "";
$AudioFileName = isset($_REQUEST["AudioFileName"]) ? $_REQUEST["AudioFileName"] : "";
// 문제 정의 //

// 보기 정의
$BookQuizDetailChoice1 = isset($_REQUEST["BookQuizDetailChoice1"]) ? $_REQUEST["BookQuizDetailChoice1"] : "";
$BookQuizDetailChoice2 = isset($_REQUEST["BookQuizDetailChoice2"]) ? $_REQUEST["BookQuizDetailChoice2"] : "";
$BookQuizDetailChoice3 = isset($_REQUEST["BookQuizDetailChoice3"]) ? $_REQUEST["BookQuizDetailChoice3"] : "";
$BookQuizDetailChoice4 = isset($_REQUEST["BookQuizDetailChoice4"]) ? $_REQUEST["BookQuizDetailChoice4"] : "";

$BookQuizDetailChoiceImage1 = isset($_REQUEST["BookQuizDetailChoiceImage1"]) ? $_REQUEST["BookQuizDetailChoiceImage1"] : "";
$BookQuizDetailChoiceImage2 = isset($_REQUEST["BookQuizDetailChoiceImage2"]) ? $_REQUEST["BookQuizDetailChoiceImage2"] : "";
$BookQuizDetailChoiceImage3 = isset($_REQUEST["BookQuizDetailChoiceImage3"]) ? $_REQUEST["BookQuizDetailChoiceImage3"] : "";
$BookQuizDetailChoiceImage4 = isset($_REQUEST["BookQuizDetailChoiceImage4"]) ? $_REQUEST["BookQuizDetailChoiceImage4"] : "";
// 보기 정의 //

// 업데이트 하기 전 원본 값
$BookQuizDetailChoiceImageVal1 = isset($_REQUEST["BookQuizDetailChoiceImageVal1"]) ? $_REQUEST["BookQuizDetailChoiceImageVal1"] : "";
$BookQuizDetailChoiceImageVal2 = isset($_REQUEST["BookQuizDetailChoiceImageVal2"]) ? $_REQUEST["BookQuizDetailChoiceImageVal2"] : "";
$BookQuizDetailChoiceImageVal3 = isset($_REQUEST["BookQuizDetailChoiceImageVal3"]) ? $_REQUEST["BookQuizDetailChoiceImageVal3"] : "";
$BookQuizDetailChoiceImageVal4 = isset($_REQUEST["BookQuizDetailChoiceImageVal4"]) ? $_REQUEST["BookQuizDetailChoiceImageVal4"] : "";
// 업데이트 하기 전 원본 값 //

$BookQuizDetailCorrectAnswer = isset($_REQUEST["BookQuizDetailCorrectAnswer"]) ? $_REQUEST["BookQuizDetailCorrectAnswer"] : "";
$BookQuizDetailState = isset($_REQUEST["BookQuizDetailState"]) ? $_REQUEST["BookQuizDetailState"] : "";


if ($BookQuizDetailState!="1"){
	$BookQuizDetailState = 2;
}

$BookQuizDetailView = 1;


$DbMyFileName1 = $BookQuizDetailChoiceImageVal1;
$DbMyFileName2 = $BookQuizDetailChoiceImageVal2;
$DbMyFileName3 = $BookQuizDetailChoiceImageVal3;
$DbMyFileName4 = $BookQuizDetailChoiceImageVal4;



//================================== 파일 업로드 ============================
$Path = "../uploads/book_quiz_images/";
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


//================================== 보기 이미지 파일 업로드 ============================
$Path = "../uploads/book_quiz_images/";
$UploadDir = str_replace(basename(__FILE__), '', realpath($Path)) . "/";

if($BookQuizDetailAnswerType==2) {
	for($idx=1; $idx<=4; $idx++) {
		$Answer = "BookQuizDetailChoiceImage".$idx;
		$TempFile2 = $_FILES[$Answer]['tmp_name'];
		if ($TempFile2){

			$MyFile         = $_FILES[$Answer]['name'];
			$MyFileSize     = $_FILES[$Answer]['size'];
			$MyFileMimeType = $_FILES[$Answer]['type'];
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

			if(!@copy($TempFile2, $Path.$MyFileName)) { echo("error"); }


			${"DbMyFileName".$idx}	= (iconv('euc-kr','utf-8',$MyFileName));
			${"DbMyFileRealName".$idx}  = (iconv('euc-kr','utf-8',$MyFileRealName));
			${"DbMyFileSize".$idx}      = $MyFileSize;
			${"DbMyFileExtension".$idx} = $FileType;
			${"DbMyFileMimeType".$idx}  = $MyFileMimeType;
		}
	}
}
//================================== 파일 업로드 ============================


if ($BookQuizDetailID==""){

	$Sql = "select ifnull(Max(BookQuizDetailOrder),0) as BookQuizDetailOrder from BookQuizDetails";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	
	$BookQuizDetailOrder = $Row["BookQuizDetailOrder"]+1;


	$Sql = " insert into BookQuizDetails ( ";
		$Sql .= " BookQuizID, ";
		$Sql .= " BookQuizDetailQuizType, ";
		if ($BookQuizDetailQuizType==2) { // 듣기평가라면
			$Sql .= " BookQuizDetailSoundFileName, ";
		}
		$Sql .= " BookQuizDetailText, ";
		$Sql .= " BookQuizDetailQuestionType, ";
		if ($BookQuizDetailQuestionType==1) {
			if ($TempFile){
				$Sql .= " BookQuizDetailImageFileName, ";
				$Sql .= " BookQuizDetailImageFileRealName, ";
			}
		} else if($BookQuizDetailQuestionType==2) {
			$Sql .= " BookQuizDetailTextQuestion, ";
		} else if($BookQuizDetailQuestionType==4) {
			$Sql .= " BookQuizDetailVideoCode, ";
		}
		$Sql .= " BookQuizDetailAnswerType, ";
		if ($BookQuizDetailAnswerType==1) {
			$Sql .= " BookQuizDetailChoice1, ";
			$Sql .= " BookQuizDetailChoice2, ";
			$Sql .= " BookQuizDetailChoice3, ";
			$Sql .= " BookQuizDetailChoice4, ";
		} else {
			$Sql .= " BookQuizDetailChoiceImage1, ";
			$Sql .= " BookQuizDetailChoiceImage2, ";
			$Sql .= " BookQuizDetailChoiceImage3, ";
			$Sql .= " BookQuizDetailChoiceImage4, ";
		}
		$Sql .= " BookQuizDetailCorrectAnswer, ";
		$Sql .= " BookQuizDetailRegDateTime, ";
		$Sql .= " BookQuizDetailModiDateTime, ";
		$Sql .= " BookQuizDetailOrder, ";
		$Sql .= " BookQuizDetailView, ";
		$Sql .= " BookQuizDetailState ";

	$Sql .= " ) values ( ";
		$Sql .= " :BookQuizID, ";
		$Sql .= " :BookQuizDetailQuizType, ";
		if ($BookQuizDetailQuizType==2) {
			$Sql .= " :BookQuizDetailSoundFileName, ";
		}
		$Sql .= " :BookQuizDetailText, ";
		$Sql .= " :BookQuizDetailQuestionType, ";
		if ($BookQuizDetailQuestionType==1) {
			if ($TempFile){
				$Sql .= " :BookQuizDetailImageFileName, ";
				$Sql .= " :BookQuizDetailImageFileRealName, ";
			}
		} else if($BookQuizDetailQuestionType==2) {
			$Sql .= " :BookQuizDetailTextQuestion, ";
		} else if($BookQuizDetailQuestionType==4) {
			$Sql .= " :BookQuizDetailVideoCode, ";
		}
		$Sql .= " :BookQuizDetailAnswerType, ";
		if ($BookQuizDetailAnswerType==1) {
			$Sql .= " :BookQuizDetailChoice1, ";
			$Sql .= " :BookQuizDetailChoice2, ";
			$Sql .= " :BookQuizDetailChoice3, ";
			$Sql .= " :BookQuizDetailChoice4, ";
		} else {
			$Sql .= " :BookQuizDetailChoiceImage1, ";
			$Sql .= " :BookQuizDetailChoiceImage2, ";
			$Sql .= " :BookQuizDetailChoiceImage3, ";
			$Sql .= " :BookQuizDetailChoiceImage4, ";
		}
		$Sql .= " :BookQuizDetailCorrectAnswer, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :BookQuizDetailOrder, ";
		$Sql .= " :BookQuizDetailView, ";
		$Sql .= " :BookQuizDetailState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookQuizID', $BookQuizID);
	$Stmt->bindParam(':BookQuizDetailQuizType', $BookQuizDetailQuizType);
	if ($BookQuizDetailQuizType==2) {
		$Stmt->bindParam(':BookQuizDetailSoundFileName', $AudioFileName);
	}
	$Stmt->bindParam(':BookQuizDetailText', $BookQuizDetailText);
	$Stmt->bindParam(':BookQuizDetailQuestionType', $BookQuizDetailQuestionType);
	if ($BookQuizDetailQuestionType==1) {
		if ($TempFile){
			$Stmt->bindParam(':BookQuizDetailImageFileName', $DbMyFileName);
			$Stmt->bindParam(':BookQuizDetailImageFileRealName', $DbMyFileRealName);
		}
	} else if($BookQuizDetailQuestionType==2) {
		$Stmt->bindParam(':BookQuizDetailTextQuestion', $BookQuizDetailTextQuestion);
	} else if($BookQuizDetailQuestionType==4) {
		$Stmt->bindParam(':BookQuizDetailVideoCode', $BookQuizDetailVideoCode);
	}
	$Stmt->bindParam(':BookQuizDetailAnswerType', $BookQuizDetailAnswerType);
	if ($BookQuizDetailAnswerType==1) {
		$Stmt->bindParam(':BookQuizDetailChoice1', $BookQuizDetailChoice1);
		$Stmt->bindParam(':BookQuizDetailChoice2', $BookQuizDetailChoice2);
		$Stmt->bindParam(':BookQuizDetailChoice3', $BookQuizDetailChoice3);
		$Stmt->bindParam(':BookQuizDetailChoice4', $BookQuizDetailChoice4);
	} else {
		$Stmt->bindParam(':BookQuizDetailChoiceImage1', $DbMyFileName1);
		$Stmt->bindParam(':BookQuizDetailChoiceImage2', $DbMyFileName2);
		$Stmt->bindParam(':BookQuizDetailChoiceImage3', $DbMyFileName3);
		$Stmt->bindParam(':BookQuizDetailChoiceImage4', $DbMyFileName4);
	}
	$Stmt->bindParam(':BookQuizDetailCorrectAnswer', $BookQuizDetailCorrectAnswer);
	$Stmt->bindParam(':BookQuizDetailOrder', $BookQuizDetailOrder);
	$Stmt->bindParam(':BookQuizDetailView', $BookQuizDetailView);
	$Stmt->bindParam(':BookQuizDetailState', $BookQuizDetailState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update BookQuizDetails set ";
		$Sql .= " BookQuizDetailQuizType = :BookQuizDetailQuizType, ";
		if ($BookQuizDetailQuizType==2) {
			$Sql .= " BookQuizDetailSoundFileName = :BookQuizDetailSoundFileName, ";
		}
		$Sql .= " BookQuizDetailText = :BookQuizDetailText, ";
		$Sql .= " BookQuizDetailQuestionType = :BookQuizDetailQuestionType, ";
		if ($BookQuizDetailQuestionType==1) {
			if ($TempFile){
				$Sql .= " BookQuizDetailImageFileName = :BookQuizDetailImageFileName, ";
				$Sql .= " BookQuizDetailImageFileRealName = :BookQuizDetailImageFileRealName, ";
			}
		} else if($BookQuizDetailQuestionType==2) {
			$Sql .= " BookQuizDetailTextQuestion = :BookQuizDetailTextQuestion, ";
		} else if($BookQuizDetailQuestionType==4) {
			$Sql .= " BookQuizDetailVideoCode = :BookQuizDetailVideoCode, ";
		}
		$Sql .= " BookQuizDetailAnswerType = :BookQuizDetailAnswerType, ";
		if ($BookQuizDetailAnswerType==1) {
			$Sql .= " BookQuizDetailChoice1 = :BookQuizDetailChoice1, ";
			$Sql .= " BookQuizDetailChoice2 = :BookQuizDetailChoice2, ";
			$Sql .= " BookQuizDetailChoice3 = :BookQuizDetailChoice3, ";
			$Sql .= " BookQuizDetailChoice4 = :BookQuizDetailChoice4, ";
		} else {
			$Sql .= " BookQuizDetailChoiceImage1 = :BookQuizDetailChoiceImage1, ";
			$Sql .= " BookQuizDetailChoiceImage2 = :BookQuizDetailChoiceImage2, ";
			$Sql .= " BookQuizDetailChoiceImage3 = :BookQuizDetailChoiceImage3, ";
			$Sql .= " BookQuizDetailChoiceImage4 = :BookQuizDetailChoiceImage4, ";
		}

		$Sql .= " BookQuizDetailCorrectAnswer = :BookQuizDetailCorrectAnswer, ";
		$Sql .= " BookQuizDetailView = :BookQuizDetailView, ";
		$Sql .= " BookQuizDetailState = :BookQuizDetailState, ";
		$Sql .= " BookQuizDetailModiDateTime = now() ";
	$Sql .= " where BookQuizDetailID = :BookQuizDetailID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookQuizDetailQuizType', $BookQuizDetailQuizType);
	if ($BookQuizDetailQuizType==2) {
		$Stmt->bindParam(':BookQuizDetailSoundFileName', $AudioFileName);
	}
	$Stmt->bindParam(':BookQuizDetailText', $BookQuizDetailText);
	$Stmt->bindParam(':BookQuizDetailQuestionType', $BookQuizDetailQuestionType);
	if ($BookQuizDetailQuestionType==1) {
		if ($TempFile){
			$Stmt->bindParam(':BookQuizDetailImageFileName', $DbMyFileName);
			$Stmt->bindParam(':BookQuizDetailImageFileRealName', $DbMyFileRealName);
		}
	} else if($BookQuizDetailQuestionType==2) {
		$Stmt->bindParam(':BookQuizDetailTextQuestion', $BookQuizDetailTextQuestion);
	} else if($BookQuizDetailQuestionType==4) {
		$Stmt->bindParam(':BookQuizDetailVideoCode', $BookQuizDetailVideoCode);
	}
	$Stmt->bindParam(':BookQuizDetailAnswerType', $BookQuizDetailAnswerType);
	if ($BookQuizDetailAnswerType==1) {
		$Stmt->bindParam(':BookQuizDetailChoice1', $BookQuizDetailChoice1);
		$Stmt->bindParam(':BookQuizDetailChoice2', $BookQuizDetailChoice2);
		$Stmt->bindParam(':BookQuizDetailChoice3', $BookQuizDetailChoice3);
		$Stmt->bindParam(':BookQuizDetailChoice4', $BookQuizDetailChoice4);
	} else {
		$Stmt->bindParam(':BookQuizDetailChoiceImage1', $DbMyFileName1);
		$Stmt->bindParam(':BookQuizDetailChoiceImage2', $DbMyFileName2);
		$Stmt->bindParam(':BookQuizDetailChoiceImage3', $DbMyFileName3);
		$Stmt->bindParam(':BookQuizDetailChoiceImage4', $DbMyFileName4);
	}
	$Stmt->bindParam(':BookQuizDetailCorrectAnswer', $BookQuizDetailCorrectAnswer);
	$Stmt->bindParam(':BookQuizDetailView', $BookQuizDetailView);
	$Stmt->bindParam(':BookQuizDetailState', $BookQuizDetailState);
	$Stmt->bindParam(':BookQuizDetailID', $BookQuizDetailID);
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
parent.$.fn.colorbox.close();
//parent.location.href = "book_form.php?<?=$ListParam?>&BookID=<?=$BookID?>&PageTabID=3";
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

