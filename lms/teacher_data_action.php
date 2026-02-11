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

$TeacherDataID = isset($_REQUEST["TeacherDataID"]) ? $_REQUEST["TeacherDataID"] : "";
$SendMemberID = isset($_REQUEST["SendMemberID"]) ? $_REQUEST["SendMemberID"] : "";
$SendMemberName = isset($_REQUEST["SendMemberName"]) ? $_REQUEST["SendMemberName"] : "";
$ReceiveMemberID = isset($_REQUEST["ReceiveMemberID"]) ? $_REQUEST["ReceiveMemberID"] : "";
$ReceiveMemberName = isset($_REQUEST["ReceiveMemberName"]) ? $_REQUEST["ReceiveMemberName"] : "";
$TeacherDataTitle = isset($_REQUEST["TeacherDataTitle"]) ? $_REQUEST["TeacherDataTitle"] : "";
$TeacherDataState = isset($_REQUEST["TeacherDataState"]) ? $_REQUEST["TeacherDataState"] : "";



//================================== 파일 업로드 ============================
$Path = "../uploads/teacher_datas/";
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



	$DbMyFileName      = (iconv('euc-kr','utf-8',$MyFileName));
	$DbMyFileRealName  = (iconv('euc-kr','utf-8',$MyFileRealName));
	$DbMyFileSize      = $MyFileSize;
	$DbMyFileExtension = $FileType;
	$DbMyFileMimeType  = $MyFileMimeType;

}
//================================== 파일 업로드 ============================



$Sql = " insert into TeacherDatas ( ";
	$Sql .= " SendMemberID, ";
	$Sql .= " SendMemberName, ";
	$Sql .= " ReceiveMemberID, ";
	$Sql .= " ReceiveMemberName, ";
	$Sql .= " TeacherDataTitle, ";
	if ($TempFile){
		$Sql .= " TeacherDataFileName, ";
		$Sql .= " TeacherDataFileRealName, ";
	}
	$Sql .= " TeacherDataRegDateTime, ";
	$Sql .= " TeacherDataState ";
$Sql .= " ) values ( ";
	$Sql .= " :SendMemberID, ";
	$Sql .= " :SendMemberName, ";
	$Sql .= " :ReceiveMemberID, ";
	$Sql .= " :ReceiveMemberName, ";
	$Sql .= " :TeacherDataTitle, ";
	if ($TempFile){
		$Sql .= " :TeacherDataFileName, ";
		$Sql .= " :TeacherDataFileRealName, ";
	}
	$Sql .= " now(), ";
	$Sql .= " :TeacherDataState ";
$Sql .= " ) ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SendMemberID', $SendMemberID);
$Stmt->bindParam(':SendMemberName', $SendMemberName);
$Stmt->bindParam(':ReceiveMemberID', $ReceiveMemberID);
$Stmt->bindParam(':ReceiveMemberName', $ReceiveMemberName);
$Stmt->bindParam(':TeacherDataTitle', $TeacherDataTitle);
if ($TempFile){
	$Stmt->bindParam(':TeacherDataFileName', $DbMyFileName);
	$Stmt->bindParam(':TeacherDataFileRealName', $DbMyFileRealName);
}
$Stmt->bindParam(':TeacherDataState', $TeacherDataState);
$Stmt->execute();
$Stmt = null;


?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>
UIkit.modal.alert("자료를 전송했습니다.");
setTimeout(function(){
	parent.$.fn.colorbox.close();
}, 2000);
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

