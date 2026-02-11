<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$DocumentReportID = isset($_REQUEST["DocumentReportID"]) ? $_REQUEST["DocumentReportID"] : "";


$Sql = "
		select 
				A.*
		from DocumentReports A 
		where A.DocumentReportID=:DocumentReportID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$FileName = $Row["FileName"];
$FileRealName = $Row["FileRealName"];



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
 
$filepath = '../uploads/document_files/'.$FileName;

if( is_ie() ) $filepath = utf2euc($filepath);
$filesize = filesize($filepath);
$filename = mb_basename($filepath);
if( is_ie() ) $filename = utf2euc($filename);
if( is_ie() ) $FileRealName = utf2euc($FileRealName);


header("Pragma: public");
header("Expires: 0");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$FileRealName\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: $filesize");
 
readfile($filepath);

include_once('../includes/dbclose.php');
?>
