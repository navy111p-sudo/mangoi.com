<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$TeacherDataID = isset($_REQUEST["TeacherDataID"]) ? $_REQUEST["TeacherDataID"] : "";

$Sql = "
		select 
				A.*
		from TeacherDatas A 
		where A.TeacherDataID=:TeacherDataID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherDataID', $TeacherDataID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ReceiveMemberID = $Row["ReceiveMemberID"];
$TeacherDataFileName = $Row["TeacherDataFileName"];
$TeacherDataFileRealName = $Row["TeacherDataFileRealName"];
$TeacherDataState = $Row["TeacherDataState"];

if ($TeacherDataState==1 && $ReceiveMemberID==$_LINK_ADMIN_ID_){
	$Sql = "
			update TeacherDatas set TeacherDataReceiveDateTime=now(), TeacherDataState=2 where TeacherDataID=:TeacherDataID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherDataID', $TeacherDataID);
	$Stmt->execute();
	$Stmt = null;
}


function mb_basename($path) { 
	$arr_path = explode('/',$path);
	return $arr_path[count($arr_path)-1]; 
} 
function utf2euc($str) { return iconv("UTF-8","cp949//IGNORE", $str); }
function is_ie() {
	if(!isset($_SERVER['HTTP_USER_AGENT']))return false;
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; // IE8
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; // IE11
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) return true; // IE11
	return false;
}
 
$filepath = '../uploads/teacher_datas/'.$TeacherDataFileName;

//echo $filepath ;

if( is_ie() ) $filepath = utf2euc($filepath);
$filesize = filesize($filepath);
$filename = mb_basename($filepath);
if( is_ie() ) $filename = utf2euc($filename);
if( is_ie() ) $TeacherDataFileRealName = utf2euc($TeacherDataFileRealName);


header("Pragma: public");
header("Expires: 0");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$TeacherDataFileRealName\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: $filesize");
 
readfile($filepath);

include_once('../includes/dbclose.php');
?>