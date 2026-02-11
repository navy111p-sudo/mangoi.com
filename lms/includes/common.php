<?php
// http 리다이렉트
/*
$allowed_hosts = array("slpmangoi.com");
if(!isset($_SERVER["HTTPS"]) && !in_array($_SERVER["HTTP_HOST"], $allowed_hosts)) {
	header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
	exit;
} else if(isset($_SERVER["HTTPS"]) && in_array($_SERVER["HTTP_HOST"], $allowed_hosts)) {
	header("Location: http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
	exit;
}
*/

if(!isset($_SERVER["HTTPS"])) {
	header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
	exit;
}

$conn_date       = date("Y-m-d",time());                      // 접속 DATE
$conn_time       = time();                                    // 접속 TIME
$conn_ip         = $_SERVER["REMOTE_ADDR"];                   // 접속 IP
$conn_agent      = $_SERVER["HTTP_USER_AGENT"];               // 접속 O/S 및 브라우져 정보
#====================================================================================================================================#
# 단순 비교문
#====================================================================================================================================#
function iif( $sw, $a, $b ) {
    if ($sw) {
        return $a;
    } else {
        return $b;
    }
}
#====================================================================================================================================#
# 파일 관련함수
#------------------------------------------------------------------------------------------------------------------------------------#
function mb_basename($path) { 
	$result = end(explode('/',$path)); 
	return $result;
} 

function utf2euc($str) { 
	$result = iconv("UTF-8","cp949//IGNORE", $str); 
	return $result;
}
function euc2utf($str) { 
	$result = iconv("cp949","UTF-8//IGNORE", $str); 
	return $result;
}

function is_ie() {
	if(!isset($_SERVER['HTTP_USER_AGENT']))return false;
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; // IE8
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; // IE11
	return false;
}

?>