<?php
$FileName = isset($_REQUEST["FileName"]) ? $_REQUEST["FileName"] : "";
$RealFileName = isset($_REQUEST["RealFileName"]) ? $_REQUEST["RealFileName"] : "";
$UploadPath = isset($_REQUEST["UploadPath"]) ? $_REQUEST["UploadPath"] : "";


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
 
$filepath = $UploadPath.$FileName;
if( is_ie() ) $filepath = utf2euc($filepath);
$filesize = filesize($filepath);
$filename = mb_basename($filepath);
if( is_ie() ) $filename = utf2euc($filename);
if( is_ie() ) $RealFileName = utf2euc($RealFileName);


header("Pragma: public");
header("Expires: 0");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$RealFileName\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: $filesize");
 
readfile($filepath);


?>